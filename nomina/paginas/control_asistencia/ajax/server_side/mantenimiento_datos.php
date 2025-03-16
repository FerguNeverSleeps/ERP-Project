<?php
	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 * Easy set variables
	 */
	$archivo_reloj = isset($_GET['archivo']) ? $_GET['archivo'] : '';
	
	/* Array of database columns which should be read and sent back to DataTables. Use a space where
	 * you want to insert a non-database field (for example a counter or static image)
	 */
	$aColumns = array('codigo', 'ficha', 'fecha_hora', 'tipo_movimiento', 'dispositivo', 'corregido' );
	
	/* Indexed column (used for fast and accurate table cardinality) */
	$sIndexColumn = "codigo";
	
	/* DB table to use */
	$sTable = "caa_archivos_datos";

/*
$res  = $conexion->createQueryBuilder()
                 ->select('codigo', 'ficha', 'fecha_hora', 'tipo_movimiento', 'dispositivo')
                 ->from('caa_archivos_datos')
                 ->where('archivo_reloj = ?')
                 ->setParameter(0, $archivo_reloj)
                 ->orderBy('ficha', 'ASC')
                 ->addOrderBy('fecha_hora', 'ASC')
                 ->execute(); 
*/	
	
	/* Database connection information */
	require_once "../../config/db.php";
	require_once "../../utils/detectar_errores_datos.php";
	
	
	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 * If you just want to use the basic configuration for DataTables with PHP server-side, there is
	 * no need to edit below this line
	 */
	
	/* 
	 * Paging
	 */
	$sLimit = "";
	if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' )
	{
		$sLimit = "LIMIT ".intval( $_GET['iDisplayStart'] ).", ".
			intval( $_GET['iDisplayLength'] );
	}
	
	
	/*
	 * Ordering
	 */
	$sOrder = "ORDER BY ficha ASC, fecha_hora ASC";
	if ( isset( $_GET['iSortCol_0'] ) )
	{
		$sOrder = "ORDER BY  ";
		for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ )
		{
			if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" )
			{
				$sOrder .= "`".$aColumns[ intval( $_GET['iSortCol_'.$i] ) ]."` ".
					($_GET['sSortDir_'.$i]==='asc' ? 'asc' : 'desc') .", ";
			}
		}
		
		$sOrder = substr_replace( $sOrder, "", -2 );
		if ( $sOrder == "ORDER BY" )
		{
			$sOrder = "";
		}
	}
	
	
	/* 
	 * Filtering
	 * NOTE this does not match the built-in DataTables filtering which does it
	 * word by word on any field. It's possible to do here, but concerned about efficiency
	 * on very large tables, and MySQL's regex functionality is very limited
	 */
	$sWhere = "WHERE archivo_reloj='{$archivo_reloj}' ";
	if ( isset($_GET['sSearch']) && $_GET['sSearch'] != "" )
	{
		$sWhere .= " AND (";
		for ( $i=0 ; $i<count($aColumns) ; $i++ )
		{
			$sWhere .= "`".$aColumns[$i]."` LIKE '%". $_GET['sSearch'] ."%' OR ";
			//$sWhere .= "`ficha` = '". $_GET['sSearch'] ."' OR ";
			// mysqli_real_escape_string( $_GET['sSearch'] )
		}
		$sWhere = substr_replace( $sWhere, "", -3 );
		$sWhere .= ')';
	}
	
	/* Individual column filtering */
	for ( $i=0 ; $i<count($aColumns) ; $i++ )
	{
		if ( isset($_GET['bSearchable_'.$i]) && $_GET['bSearchable_'.$i] == "true" && $_GET['sSearch_'.$i] != '' )
		{
			if ( $sWhere == "" )
			{
				$sWhere = "WHERE ";
			}
			else
			{
				$sWhere .= " AND ";
			}
			$sWhere .= "`".$aColumns[$i]."` LIKE '%". $_GET['sSearch_'.$i] ."%' ";
			// mysqli_real_escape_string($_GET['sSearch_'.$i])
		}
	}
	
	/*
	 * SQL queries
	 * Get data to display
	 */
	
	$sQuery = "
		SELECT SQL_CALC_FOUND_ROWS `".str_replace(" , ", " ", implode("`, `", $aColumns))."`
		FROM   $sTable
		$sWhere
		$sOrder
		$sLimit
		";
	/*
	$sQuery = "
		SELECT `".str_replace(" , ", " ", implode("`, `", $aColumns))."`
		FROM   $sTable
		$sWhere
		$sOrder
		$sLimit
		";	
	*/	

	$rResult = $conexion->query($sQuery); 
	
	/* Data set length after filtering */
	
	$sQuery = "
		SELECT FOUND_ROWS()
	";
	
/*	$sQuery = "
		SELECT COUNT(*)
		FROM   $sTable
		$sWhere
		";	*/
	$rResultFilterTotal = $conexion->query($sQuery);
	$aResultFilterTotal = $rResultFilterTotal->fetchColumn();
	$iFilteredTotal = $aResultFilterTotal; //$aResultFilterTotal[0];
	
	/* Total data set length */
	$sQuery = "
		SELECT COUNT(`".$sIndexColumn."`)
		FROM   $sTable
		WHERE  archivo_reloj='{$archivo_reloj}'
	"; 
	$rResultTotal = $conexion->query($sQuery);
	$aResultTotal = $rResultTotal->fetchColumn();
	$iTotal = $aResultTotal; //$aResultTotal[0];

	/*
	 * Output
	 */
	$output = array(
		"sEcho" => intval($_GET['sEcho']),
		"iTotalRecords" => $iTotal,
		"iTotalDisplayRecords" => $iFilteredTotal,
		"aaData" => array()
	);
	
	$aux_ficha=$aux_fecha='';  $cont_entradas=$cont_salidas=0;
	$tipo_anterior = '';
	$entradas_anterior = array(); // Entradas de la fecha anterior
	$salidas_anterior  = array(); // Salidas de la fecha anterior de una ficha
	$hora_desde1_anterior = $hora_hasta1_anterior = ''; // Rango dia anterior
	$total_entradas_fuera= $total_salidas_fuera= 0;
	$total_entradas = $total_salidas = 0;
	while ( $fila = $rResult->fetch() )
	{
		list($tipo_error, $msj_error, $class_error, $class_text, $entradas_anterior, $salidas_anterior) = detectar_errores($archivo_reloj, $fila, $aux_ficha, $aux_fecha, $cont_entradas, $cont_salidas, $entradas_anterior, $salidas_anterior, $hora_desde1_anterior, $hora_hasta1_anterior, $total_entradas_fuera, $total_salidas_fuera, $tipo_anterior, $total_entradas, $total_salidas);

		$row = array();

		if($class_error!=='')
			$row["DT_RowClass"]=$class_error;

		$row[] = '<input type="checkbox" name="chk_id[]" value="'.$fila[ 'codigo' ].'">';

		$row[] = $fila['ficha'];

		if($class_text!=='')
		{
			if(!empty($fila['tipo_movimiento']))
			{
				$title1 = "La fecha y hora se encuentran fuera del rango permitido para una ". strtolower($fila['tipo_movimiento']);
				$title2 = "La ".strtolower($fila['tipo_movimiento'])." se encuentra fuera del rango permitido";
			}

			$row[] = '<span class="'.$class_text.'" title="'.$title1.'">'.date('d-m-Y h:i:s a', strtotime($fila['fecha_hora'])) .'</span>' ;
			$row[] = '<span class="'.$class_text.'" title="'.$title2.'">'.(!empty($fila['tipo_movimiento']) ? $fila['tipo_movimiento'] : 'No disponible' ). '</span>';
		}
		else
		{
			$row[] = date('d-m-Y h:i:s a', strtotime($fila['fecha_hora']));
			$row[] = (!empty($fila['tipo_movimiento']) ? $fila['tipo_movimiento'] : 'No disponible' );
		}

		$row[] = (!empty($fila['dispositivo']) ? $fila['dispositivo'] : 'No disponible' );

		if($tipo_error!=='')
		{
			if($tipo_error!='0')
			{
				$row[]  = '<a href="javascript:corregir_error(\''.$tipo_error.'\',\''.$archivo_reloj.'\', \''.$fila[ 'codigo' ].'\', \''.$total_entradas.'\', \''.$total_salidas.'\');" data-toggle="tooltip" data-placement="top" title="'.$msj_error.'"><img src="web/images/icons/tools.png" alt="Corregir" width="16" height="16"></a>'
						. '<input type="hidden" name="tipo_error_'.$fila['codigo'].'" value="'.$tipo_error.'" >'
						. '<input type="hidden" name="total_entr_'.$fila['codigo'].'" value="'.$total_entradas.'">'
						. '<input type="hidden" name="total_sali_' .$fila['codigo'].'" value="'.$total_salidas.'" >';
			}
			else
			{
				$row[]  = '<input type="hidden" name="tipo_error_'.$fila['codigo'].'" value="'.$tipo_error.'" >'
						. '<input type="hidden" name="total_entr_'.$fila['codigo'].'" value="'.$total_entradas.'">'
						. '<input type="hidden" name="total_sali_' .$fila['codigo'].'" value="'.$total_salidas.'" >';
			}
		}
		else
		{
			if($msj_error!=='')
				$row[] = '<a title="'.$msj_error.'" style="cursor: pointer"><img src="web/images/icons/information.png" width="16" height="16"></a>'; //'<span class="text-red">'. $msj_error.'</span>';
			else
				$row[] = '';
		}

		$row[] = "<a href=\"javascript:enviar('2','".$fila['codigo']."', '".$archivo_reloj."');\" title=\"Editar\">" . 
		         "<img src=\"web/images/icons/pencil.png\" alt=\"Editar\" width=\"16\" height=\"16\"></a>";

		$row[] = "<a href=\"javascript:enviar('3','".$fila['codigo']."');\" title=\"Eliminar\">" . 
		         "<img src=\"web/images/icons/delete.png\" alt=\"Eliminar\" width=\"16\" height=\"16\"></a>";
		
		$output['aaData'][] = $row;
	}
	
	echo json_encode( $output );
?>