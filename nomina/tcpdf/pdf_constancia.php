<?php
//if(!isset($_SESSION)){
//	session_start();
//}
  //ob_start();
  //error_reporting(E_ALL & ~E_NOTICE);
  //ini_set('display_errors', 0);
  //ini_set('log_errors', 1);
require('mypdf.php');

if( isset($_GET['registro_id']) && isset($_GET['tipn']) && isset($_GET['const_id']) )
{
	$registro_id   = (int) $_GET['registro_id']; // ficha
	$tipnom        = (int) $_GET['tipn'];
	$constancia_id = (int) $_GET['const_id'];
	// $est=$_GET['est'];
	
	//===================================================================================================
	// Configurar reporte PDF	
	$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, 'LETTER', true, 'UTF-8', false);
	$pdf->SetCreator(PDF_CREATOR);
	$pdf->SetAuthor('Selectra');
	$pdf->SetTitle('Constancia');

	// set header and footer fonts
	$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
	$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

	// set default monospaced font
	$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
	// set margins
	$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
	//$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
	//$pdf->SetFooterMargin(43); // 22
	// set auto page breaks
	$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
	// set image scale factor
	$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
	
	$pdf->AddPage();
	//$pdf->AddPage('P', 'LETTER');

	$calibri      = $pdf->addTTFfont('fonts/CALIBRI.TTF',  'TrueTypeUnicode', '', 32);
	$calibri_bold = $pdf->addTTFfont('fonts/CALIBRIB.TTF', 'TrueTypeUnicode', '', 32);
	$calibri_bi   = $pdf->addTTFfont('fonts/CALIBRIB.TTF', 'TrueTypeUnicode', '', 32);

	//$pdf->SetFont('times', '', 10);
	
	//===================================================================================================
	
	# Conexion a la base de datos
	$conexion = conexion();
	
	# Consultar datos generales de las constancias 
	$res = query('SELECT titulo, slogan, cargo_gerente, abreviatura, observaciones FROM nomconf_constancia', $conexion);
	$fila = fetch_array($res);
	$titulo = (!empty($fila['titulo'])) ? utf8_encode($fila['titulo']) : '';
	
	# Consultar datos especificos del modelo de constancia
	$res = query('SELECT nombre, contenido1, contenido2, contenido3, titulo, observaciones, formula FROM nomtipos_constancia WHERE codigo='.$constancia_id, $conexion);
	$fila = fetch_array($res);
	$nombre_constancia = utf8_encode($fila['nombre']);
	$contenido2= $fila['contenido2'];
	if(!empty($fila['titulo'])){ $titulo = utf8_encode($fila['titulo']); }
	$formula=$fila['formula'];
	
	# Consultar datos del empleado
	$sql1='SELECT cedula, ficha, apenom, codcargo, fecing, fecharetiro, suesal, nombres, apellidos, sexo FROM nompersonal WHERE ficha='.$registro_id.' AND tipnom='.$tipnom;
	$res=query($sql1, $conexion);
	$empleado=fetch_array($res);
	$CEDULA = $empleado['cedula'];
	$NOMBRE = strtoupper(utf8_encode($empleado['nombres'] .' ' . $empleado['apellidos'] ));
	$NOMBRE_NORMAL = ucwords( strtolower($NOMBRE) );
	$codcargo = $empleado['codcargo'];
	$SUELDO   = $empleado['suesal']; 
	$PRIMER_NOMBRE = utf8_encode($empleado['nombres']);
	$APELLIDOS =  utf8_encode($empleado['apellidos']);
	$nombres = explode(' ', $PRIMER_NOMBRE);
	if(count($nombres)>0)
	{
		$PRIMER_NOMBRE = $nombres[0];
	}
	$PRIMER_APELLIDO = utf8_encode($empleado['apellidos']);
	$apellidos = explode(' ', $PRIMER_APELLIDO);
	if(count($apellidos)>0)
	{
		$PRIMER_APELLIDO = $apellidos[0];
	}
	$sexo = $empleado['sexo'];
	// Fecha de ingreso
	$fecha_ingreso = $empleado['fecing'];
	list($anioing, $mesing, $diaing)=explode('-', $fecha_ingreso);
	$FECHA_INGRESO = $diaing.' de '. ucfirst(strtolower(mesaletras($mesing))) .' de '.$anioing;
	// Fecha de egreso
	$fecha_egreso = $empleado['fecharetiro'];
	list($anioegr, $mesegr, $diaegr)=explode('-', $fecha_egreso);
	$FECHA_EGRESO= $diaegr.'/'.$mesegr.'/'.$anioegr;	
	
	# Consultar datos del cargo
	$sql2='SELECT des_car FROM nomcargos WHERE cod_car='.$codcargo;
	$res2=query($sql2, $conexion);
	$fila2 = fetch_array($res2);
	$CARGO = $fila2['des_car'];
    
    $FICHA=$empleado['ficha'];

    $mes_actual   = date('n');
    $anio_actual  = date('Y');
    $dia_inicio_quincena = 0;

    $sql = "SELECT MAX(mes) as mes, Max(anio) as anio 
            FROM   nom_movimientos_nomina n 
            WHERE  ficha='".$FICHA."' AND tipnom='".$tipnom."' 
            AND    n.anio=(SELECT MAX(anio) FROM nom_movimientos_nomina WHERE ficha=n.ficha)";

    if($fila  = fetch_array(query($sql, $conexion)))
    {
    	$mes_actual  = ($fila['mes']  != null) ? $fila['mes']  : $mes_actual;
    	$anio_actual = ($fila['anio'] != null) ? $fila['anio'] : $anio_actual;
    }

    // Primero voy a consultar si ya se cancelo alguna quincena del mes actual
    $sql = "SELECT DATE_FORMAT(n.periodo_ini,'%e') AS dia_inicio, DATE_FORMAT(n.periodo_fin,'%e') AS dia_fin
			FROM   nom_nominas_pago n 
			WHERE  n.periodo_fin=(SELECT MAX(periodo_fin) 
				                  FROM   nom_nominas_pago 
				                  WHERE  anio=".$anio_actual." AND mes=".$mes_actual." AND tipnom='".$tipnom."')";
	$res = query($sql, $conexion);

	if( $fila = fetch_array($res) )
	{
		$dia_inicio_quincena = $fila['dia_inicio']; // Comienzo de la quincena
		$dia_fin_quincena    = $fila['dia_fin'];

		if($dia_inicio_quincena==1)
		{
			$ini_ultima_quincena = date("Y-m-d", mktime(0,0,0,$mes_actual,$dia_fin_quincena+1,$anio_actual));
			$fin_ultima_quincena = date("Y-m-d",(mktime(0,0,0,$mes_actual+1,1,$anio_actual)-1));			
		}
	}

    //=============================================================================================
    // Consultar GASTOS DE REPRESENTACION 
	$sql = "SELECT nap.valor AS monto 
	        FROM   nomcampos_adic_personal nap 
			INNER  JOIN nomcampos_adicionales na ON nap.id =  na.id 
			WHERE  nap.ficha = '".$FICHA."' AND UPPER(na.descrip)='GASTOS DE REPRESENTACION'";

	$res = query($sql, $conexion);

	if( $fila = fetch_array($res) )
	{
		$GASTOS_REPRESENTACION = $fila['monto'];
	}
    //=============================================================================================
    // Consultar SEGURO SOCIAL (S.S.) - Tabla: nomconceptos - codcon: 200
    $sql = "SELECT SUM(nm.monto) AS monto 
			FROM   nomconceptos nc, nom_movimientos_nomina nm
			WHERE  nc.codcon=nm.codcon
			AND    nc.descrip LIKE '%SEGURO SOCIAL (S.S.)%' 
			AND    nm.ficha='".$FICHA."' AND nm.tipnom='".$tipnom."'
			AND    nm.codnom IN (SELECT codnom FROM nom_nominas_pago WHERE mes='".$mes_actual."' AND anio='".$anio_actual."')";

	$res = query($sql, $conexion);

	if( $fila = fetch_array($res) )
	{
		$SEGURO_SOCIAL = $fila['monto'];

		if($dia_inicio_quincena==1) // Es la primera quincena
		{
			$SEGURO_SOCIAL *= 2;
		}
	}
    //=============================================================================================
    // Consultar IMPUESTO SOBRE LA RENTA
    // IMPUESTO SOBRE LA RENTA (I.S.R.) - Tabla: nomconceptos - codcon: 202
    // IMPUESTO SOBRE LA RENTA GASTOS DE REPRESENTACION - Tabla: nomconceptos - codcon: 207 
    $sql = "SELECT SUM(nm.monto) AS monto 
			FROM   nomconceptos nc, nom_movimientos_nomina nm
			WHERE  nc.codcon=nm.codcon
			AND    nc.codcon IN (202, 207) -- nc.descrip LIKE '%IMPUESTO SOBRE LA RENTA%' 
			AND    nm.ficha='".$FICHA."' AND nm.tipnom='".$tipnom."' 
			AND    nm.codnom IN (SELECT codnom FROM nom_nominas_pago WHERE mes='".$mes_actual."' AND anio='".$anio_actual."')";
	$res = query($sql, $conexion);

	if( $fila = fetch_array($res) )
	{
		$IMPUESTO_SOBRE_RENTA = $fila['monto'];

		if($dia_inicio_quincena==1) // Es la primera quincena
		{
			$IMPUESTO_SOBRE_RENTA *= 2;
		}
	}
    //=============================================================================================
    // Consultar SEGURO EDUCATIVO (S.E.) - Tabla: nomconceptos - codcon: 201
    $sql = "SELECT SUM(nm.monto) AS monto
			FROM   nomconceptos nc, nom_movimientos_nomina nm
			WHERE  nc.codcon=nm.codcon
			AND    nc.descrip LIKE 'SEGURO EDUCATIVO (S.E.)' 
		    AND    nm.ficha='".$FICHA."' AND nm.tipnom='".$tipnom."'
			AND    nm.codnom IN (SELECT codnom FROM nom_nominas_pago WHERE mes='".$mes_actual."' AND anio='".$anio_actual."')";
	$res = query($sql, $conexion);

	if( $fila = fetch_array($res) )
	{
		$SEGURO_EDUCATIVO = $fila['monto'];

		if($dia_inicio_quincena==1) // Es la primera quincena
		{
			$SEGURO_EDUCATIVO *= 2;
		}
	}
    //=============================================================================================
    // Consultar Casa Comercial - Tabla nomconceptos - codcon del 501 al 599
    /*$sql = "SELECT SUM(nm.monto) AS monto
			FROM   nomconceptos nc, nom_movimientos_nomina nm
			WHERE  nc.codcon=nm.codcon
			AND    nc.codcon BETWEEN 501 AND 599 
		    AND    nm.ficha='".$FICHA."' AND nm.tipnom='".$tipnom."'
			AND    nm.codnom IN (SELECT codnom FROM nom_nominas_pago WHERE mes='".$mes_actual."' AND anio='".$anio_actual."')";
	$res = query($sql, $conexion);

	if( $fila = fetch_array($res) )
	{
		$CASA_COMERCIAL = $fila['monto'];

		if($dia_inicio_quincena==1) // Es la primera quincena
		{
			// Tengo que consultar si hay prestamos pendientes para la segunda quincena del mes actual
			$sql = "SELECT SUM(n.montocuo) as monto 
					FROM   nomprestamos_detalles n
					WHERE  n.ficha='".$FICHA."' AND n.estadopre='Pendiente'
					AND    n.fechaven BETWEEN '".$ini_ultima_quincena."' AND '".$fin_ultima_quincena."'";

			$res = query($sql, $conexion);

			if( $fila = fetch_array($res) )
			{
				$CASA_COMERCIAL += $fila['monto'];
			}			
		}
	}*/
	 $sql = "SELECT SUM(n.mtocuota) as monto
             FROM   nomprestamos_cabecera n
             WHERE  n.ficha='".$FICHA."' AND n.estadopre='Pendiente'";
             $res = query($sql, $conexion);
             if( $fila = fetch_array($res) )
             {
                $CASA_COMERCIAL = $fila['monto'];
             }


    //=============================================================================================

	# Día, mes y año actual
	$DIA  = date('d'); //trim($numeroTexto->convertirdia(date('d'))) . ' (' . date('d') .')';
	$MES  = strtolower(mesaletras(date('m')));
	$ANIO = date('Y'); // trim($numeroTexto->convertirdia(date('Y'))) . ' (' . date('Y').')';
	$ANIO_FISCAL = $ANIO - 1;
	
	//===================================================================================================
    // VARIABLES DE LAS CONSTANCIAS
 	// APLICAR FORMULAS   
	$numeroTexto = new numerosALetras();




	$SUELDO_LETRAS = strtoupper(trim($numeroTexto->convertir($SUELDO, '1')));


	$SALARIO_BRUTO    =  $SUELDO + $GASTOS_REPRESENTACION; 

    //esto es nuevo si lo quitan funciona como antes
	$SEGURO_SOCIAL         =  $SALARIO_BRUTO * 0.0975;
	$SEGURO_EDUCATIVO      =  $SUELDO * 0.0125;


	$IMPUESTO_SOBRE_RENTA_SALARIO  =  $SUELDO*12;

	if ($IMPUESTO_SOBRE_RENTA_SALARIO >= 11001 && $IMPUESTO_SOBRE_RENTA_SALARIO <= 50000) {
        $IMPUESTO_SOBRE_RENTA_SALARIO  =  $SUELDO * 0.15;
	} elseif ($IMPUESTO_SOBRE_RENTA_SALARIO >= 50001 && $IMPUESTO_SOBRE_RENTA_SALARIO <= 500000) {
	    $IMPUESTO_SOBRE_RENTA_SALARIO  =   $SUELDO * 0.25;
	} else {
	   $IMPUESTO_SOBRE_RENTA_SALARIO  =  0;
	}
	 

  
    $IMPUESTO_SOBRE_RENTA_GASTOR   =  $GASTOS_REPRESENTACION*12;
    if($IMPUESTO_SOBRE_RENTA_GASTOR <= 25000)
    {
       $IMPUESTO_SOBRE_RENTA_GASTOR  =  $GASTOS_REPRESENTACION * 0.10;
    }
	else
	{
	  $IMPUESTO_SOBRE_RENTA_GASTOR  =  (($IMPUESTO_SOBRE_RENTA_GASTOR - 25000)* 0.10)/12;
	  $IMPUESTO_SOBRE_RENTA_GASTOR  =  $GASTOS_REPRESENTACION + (2500/12); 
	}


	$IMPUESTO_SOBRE_RENTA  =  $IMPUESTO_SOBRE_RENTA_SALARIO + $IMPUESTO_SOBRE_RENTA_GASTOR;

	

	$TOTAL_DESCUENTOS =  $SEGURO_SOCIAL + $SEGURO_EDUCATIVO + $IMPUESTO_SOBRE_RENTA + $CASA_COMERCIAL;
	$SALARIO_NETO     =  $SALARIO_BRUTO - $TOTAL_DESCUENTOS;
    
	$SUELDO_LETRAS_SALARIOBRUTO = strtoupper(trim($numeroTexto->convertir($SALARIO_BRUTO, '1')));




  	$SUELDO                = number_format($SUELDO, 2, ',', '.');
	$GASTOS_REPRESENTACION = number_format($GASTOS_REPRESENTACION, 2, ',', '.');
    $SALARIO_BRUTO         = number_format($SALARIO_BRUTO, 2, ',', '.');
    $SEGURO_SOCIAL         = number_format($SEGURO_SOCIAL, 2, ',', '.');
    $SEGURO_EDUCATIVO      = number_format($SEGURO_EDUCATIVO, 2, ',', '.'); 
    $IMPUESTO_SOBRE_RENTA  = number_format($IMPUESTO_SOBRE_RENTA, 2, ',', '.');
    $CASA_COMERCIAL        = number_format($CASA_COMERCIAL, 2, ',', '.');
    $TOTAL_DESCUENTOS      = number_format($TOTAL_DESCUENTOS, 2, ',', '.');
    $SALARIO_NETO          = number_format($SALARIO_NETO, 2, ',', '.');
	//===================================================================================================
	eval($formula);

	$sql = "SELECT SUM(nm.monto) as salario_anual 
			FROM   nom_movimientos_nomina nm
			WHERE  nm.ficha='".$FICHA."' AND nm.anio=".$ANIO_FISCAL." 
			AND    nm.codcon=100"; // AND    UPPER(nm.descrip)='SALARIO';

	$res = query($sql, $conexion);

	if( $fila = fetch_array($res) )
	{
		$SALARIOS_ANIO_FISCAL = $fila['salario_anual'];
		$DECIMO_TERCER_MES_SALARIO = ($SALARIOS_ANIO_FISCAL / 3) / 4;
		$SALARIOS_ANIO_FISCAL = number_format($SALARIOS_ANIO_FISCAL, 2, '.', ',');
		$DECIMO_TERCER_MES_SALARIO = number_format($DECIMO_TERCER_MES_SALARIO, 2, '.', ',');
	}

	$sql = "SELECT SUM(nm.monto) as isr_salario_anual
			FROM   nom_movimientos_nomina nm
			WHERE  nm.ficha='".$FICHA."' AND nm.anio=".$ANIO_FISCAL."  
			AND    nm.codcon=202"; // AND UPPER(nm.descrip) LIKE 'IMPUESTO SOBRE LA RENTA (I.S.R.)%' 

	$res = query($sql, $conexion);

	if( $fila = fetch_array($res) )
	{
		$ISR_SALARIOS = $fila['isr_salario_anual'];
		$ISR_SALARIOS = number_format($ISR_SALARIOS, 2, '.', ',');
	}
	// 145 - GASTOS DE REPRESENTACION
	$sql = "SELECT SUM(nm.monto) as gastos_anual 
			FROM   nom_movimientos_nomina nm
			WHERE  nm.ficha='".$FICHA."' AND nm.anio=".$ANIO_FISCAL." 
			AND    nm.codcon=145"; // UPPER(nm.descrip) LIKE 'GASTOS DE REPRESENTACION'

	$res = query($sql, $conexion);

	if( $fila = fetch_array($res) )
	{
		$GASTOS_REPR_ANIO_FISCAL  = $fila['gastos_anual'];
		$DECIMO_TERCER_MES_GASTOS = ($GASTOS_REPR_ANIO_FISCAL / 3) / 4;
		$GASTOS_REPR_ANIO_FISCAL  = number_format($GASTOS_REPR_ANIO_FISCAL, 2, '.', ',');
		$DECIMO_TERCER_MES_GASTOS = number_format($DECIMO_TERCER_MES_GASTOS, 2, '.', ',');
	}		

	$sql = "SELECT SUM(nm.monto) as isr_gastos_anual
			FROM   nom_movimientos_nomina nm
			WHERE  nm.ficha='".$FICHA."' AND nm.anio=".$ANIO_FISCAL." 
			AND    nm.codcon=207"; // 207 IMPUESTO SOBRE LA RENTA GR

	$res = query($sql, $conexion);

	if( $fila = fetch_array($res) )
	{
		$ISR_GASTOS_REP = $fila['isr_gastos_anual'];
		$ISR_GASTOS_REP = number_format($ISR_GASTOS_REP, 2, '.', ',');
	}	

	//===================================================================================================
	# Imprimir titulo
	if(!empty($titulo))
	{
		$pdf->SetXY(29, 55);
		$pdf->SetFont($calibri, 'B', 16);
		$pdf->MultiCell(156, 12, $titulo, 0, 'C', false);

		$nombre_constancia=$titulo;

		$pdf->SetXY(29, 68);
	}
	else
	{
		$pdf->SetXY(29, 55);
	}

	# Imprimir fecha actual
	/*
	$pdf->SetXY(45, 68);
	$pdf->SetFont($calibri, '', 12);
	$pdf->MultiCell(156, 5, $DIA . ' de ' . $MES . ' del ' . $ANIO .'.', 0, 'L');
	*/
	
	# Imprimir contenido de la constancia
	
	//$pdf->SetXY(45, 68); //$pdf->SetXY(29, 108);
	$pdf->setCellHeightRatio(1.5); // Espaciado entre lineas
	$pdf->SetFont($calibri, '', 12);

	if(!empty($contenido2))
	{
		$pdf->SetMargins(30, PDF_MARGIN_TOP, 29);
    	$html = str_replace('"', "'", $contenido2);

    	eval("\$html = \"$html\";");

    	$html = str_replace("'", '"', $html);

    	if($sexo=='Femenino')
    	{
    		$html = str_replace("El <strong>Sr.", 'La <strong>Sra.', $html);
    		$html = str_replace("var&oacute;n", 'mujer', $html); // Paname&ntilde;o
    		$html = str_replace("Paname&ntilde;o", 'Paname&ntilde;a', $html); // trabajador activo
    		$html = str_replace("trabajador activo", 'trabajadora activa', $html); // El Sr.
    		$html = str_replace("El Sr.", 'La Sra.', $html);
    	}

		$pdf->writeHTML($html, true, false, false, false, 'J');	
	}

	$acentos = array('á','é','í','ó','ú',' ','&aacute;','&eacute;','&iacute;','&oacute;','&uacute;');
	$vocales = array('a','e','i','o','u','_','a','e','i','o','u');
	$nombre_constancia = strtolower($nombre_constancia);
	$nombre_constancia = str_replace($acentos, $vocales, $nombre_constancia);
	
	$fichero = $nombre_constancia.'_'.$empleado['cedula'].'.pdf';
ob_clean();
	$pdf->Output($fichero, 'I');
}
?>
