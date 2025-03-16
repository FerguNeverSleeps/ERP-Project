<?php
session_start();
error_reporting(0);
ini_set('display_errors', FALSE);
ini_set('display_startup_errors', FALSE);
date_default_timezone_set('America/Panama');

if (PHP_SAPI == 'cli')
	die('Este ejemplo solo se debe ejecutar desde un navegador Web');

include('../lib/common.php');
include('lib/php_excel.php');


if(isset($_GET['codnom']) && isset($_GET['codtip']))
{
	$codnom=$_GET['codnom'];
	$codtip=$_GET['codtip'];

	$conexion= new bd($_SESSION['bd']);

	$sql = "SELECT UPPER(t.descrip) as descrip 
			FROM   nomtipos_nomina t
			WHERE  t.codtip=".$codtip;
	$res=$conexion->query($sql);

	if($fila=$res->fetch_array())
	{
		$NOMINA = $fila['descrip']; 
	}

require_once 'phpexcel/Classes/PHPExcel.php';

$objPHPExcel = new PHPExcel();

$objPHPExcel->getProperties()->setCreator("Selectra")
							 ->setLastModifiedBy("Selectra")
							 ->setTitle("Horizontal de Planilla");

$objPHPExcel->getDefaultStyle()->getFont()->setName('Calibri');
// $objPHPExcel->getActiveSheet()->getStyle('A2:A4')->getFont()->setName('Calibri');
$objPHPExcel->getDefaultStyle()->getFont()->setSize(11);


$sql = "SELECT e.nom_emp as empresa, e.rif, e.tel_emp as telefono, e.dir_emp as direccion, e.ciu_emp as ciudad, 
               e.edo_emp, e.imagen_izq as logo
		FROM   nomempresa e";
$res=$conexion->query($sql);
$fila=$res->fetch_array();
$logo=$fila['logo'];

$sql2 = "SELECT np.periodo_ini as desde, np.periodo_fin as hasta, np.mes, np.anio,
				DATE_FORMAT(np.periodo_ini,'%d') as dia_ini, DATE_FORMAT(np.periodo_fin, '%d') as dia_fin, np.status    
		 FROM nom_nominas_pago np 
         WHERE  np.codnom=".$codnom." AND np.tipnom=".$codtip;
$res2=$conexion->query($sql2);
$fila2=$res2->fetch_array();

$meses = array('Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre');

$desde=$fila2['desde'];
$hasta=$fila2['hasta'];
$dia_ini = $fila2['dia_ini'];
$dia_fin = $fila2['dia_fin']; 
$mes_numero = $fila2['mes'];
$mes_letras = $meses[$mes_numero - 1];
$anio = $fila2['anio'];

if($fila2['status']=="A"){
	$estatus_mov_nomina ="Aprobado";
}

else if($fila2['status']=="P"){
	$estatus_mov_nomina ="Pendiente";
}

else{
	$estatus_mov_nomina = "";
}





$empresa = $fila['empresa'];


$sql = "SELECT nn.codorg, nn.descrip,
			   CASE WHEN nn.codorg=1 THEN 'Administrativos'
			        WHEN nn.codorg=2 THEN 'Técnicos'
			        WHEN nn.codorg=3 THEN 'Desarrollo Sostenible'
			        WHEN nn.codorg=4 THEN 'Comercialización' 
			   END as grupo
		FROM   nomnivel1 nn
		WHERE  nn.descrip NOT IN('Eventual', 'O&M')
		ORDER BY nn.codorg";
$res=$conexion->query($sql);

$i=9; $nivel=1;
// $monto_salarios=0;
$total_salarios=$total_comision=$total_combustible=$total_reembolso=$total_uso_auto=$total_bono="=";
$total_extra_salario=$total_extra_gastosrep=$total_vacaciones=$total_cxc="=";
$total_segurosocial_salario=$total_tardanza=$total_seguro_educativo="=";
$total_isr_gastos=$total_isr_salario=$total_ausencia=$total_descuentos=$total_neto="=";
$total_salario=$MesXIII=$Total_Desc_Ley=$SeguroSocialMesXIII="=";
$check_deducciones = false;//
$check_asignaciones = false;
$total_seguro_educativo2 ='';
$total_segurosocial_salario2='';
$ASIGNACIONES_ARRAY = array();
$DEDUCCIONES_ARRAY = array();
$ASIGNACIONES_ARRAY_GENERAL = array();
$DEDUCCIONES_ARRAY_GENERAL = array();

$CANTIDAD_PAGO_CHEQUE=0;
$CANTIDAD_PAGO_TRANSFERENCA=0;
$CANTIDAD_PAGO_EFECTIVO=0;
$CANTIDAD_VACACIONES=0;
$CANTIDAD_ACTIVO=0;


$MONTO_TOTAL_TRANSFERENCIA = 0;
$MONTO_TOTAL_CHEQUE = 0;
$MONTO_TOTAL_EFECTIVO = 0;



while($row=$res->fetch_array())
{
	$codorg = $row['codorg'];
	$grupo 	= $row['grupo'];

	$sql2= "SELECT np.ficha,  np.suesal , np.hora_base, CONCAT_WS(' ', np.nombres, np.apellidos) as nombre, np.estado,
				   SUBSTRING_INDEX(SUBSTRING_INDEX(np.nombres, ' ', 1), ' ', -1) as primer_nombre,
				   CASE WHEN np.apellidos LIKE 'De %' THEN 
						     SUBSTRING_INDEX(SUBSTRING_INDEX(np.apellidos, ' ', 2), ' ',  2) 
				   ELSE      SUBSTRING_INDEX(SUBSTRING_INDEX(np.apellidos, ' ', 1), ' ', -1) 
				   END as primer_apellido
			FROM   nompersonal np
			WHERE  np.codnivel1=".$codorg." AND np.tipnom=".$codtip."
			AND    np.ficha IN (SELECT DISTINCT n.ficha FROM nom_movimientos_nomina n WHERE n.codnom=".$codnom." AND n.tipnom=np.tipnom)
			ORDER BY np.ficha";

	$res2=$conexion->query($sql2);

	$ini=$i; $enc=false;
	
	while($row2=$res2->fetch_array())
	{	$enc=true;
		$ficha = $row2['ficha'];
		$primer_nombre = utf8_encode($row2['primer_nombre']);
		$apellido   = utf8_encode($row2['primer_apellido']);
		$trabajador = utf8_encode($row2['primer_nombre']).' '.utf8_encode($row2['primer_apellido']); // $row2['nombre']
		$check_vacacion = '';
		$suesal 		= $row2['suesal'];
		$hora_base 		= $row2['hora_base'];
		$rata_por_hora 	= (($suesal/4.3333)/$hora_base);
		$rata_por_hora	= number_format($rata_por_hora,2);
		$asignaciones_monto 	= 0;
		$deducciones_monto  	= 0;
		if($row2['estado']=='Vacaciones'){
			$check_vacacion = '    (V)';//
			$CANTIDAD_VACACIONES++;
		}
		else{
			$CANTIDAD_ACTIVO++;
		}

		/*else{
			$check_vacacion = '';//
		}*/

		//$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':Q'.$i)->getFont()->setName('Calibri');
		

		$sql3 = "SELECT
				 COALESCE((SELECT n.monto FROM nom_movimientos_nomina n 
  				  WHERE  n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon=100),0) as salario,
				 COALESCE((SELECT SUM(n.monto) AS monto FROM nom_movimientos_nomina n 
  				  WHERE  n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon in (141,157)), 0) as comision,  				
				 COALESCE((SELECT n.monto FROM nom_movimientos_nomina n 
  				  WHERE  n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon=199), 0) as tardanza,			 
				 COALESCE((SELECT n.monto FROM nom_movimientos_nomina n 
  				  WHERE  n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon=158), 0) as bono,		 
				 COALESCE((SELECT n.monto FROM nom_movimientos_nomina n 
  				  WHERE  n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon=200), 0) as seguro_social,
				COALESCE((SELECT  n.monto FROM nom_movimientos_nomina n 
  				  WHERE  n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon=201), 0) as seguro_educativo,
				COALESCE((SELECT  n.monto FROM nom_movimientos_nomina n 
  				  WHERE  n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon=208), 0) as seguro_social_xiii,
				COALESCE((SELECT  n.monto FROM nom_movimientos_nomina n 
  				  WHERE  n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon=145), 0) as reembolso,
				COALESCE((SELECT  SUM(n.monto) AS monto FROM nom_movimientos_nomina n 
  				  WHERE  n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon in (147,156)), 0) as uso_auto,
				COALESCE((SELECT  n.monto FROM nom_movimientos_nomina n 
  				  WHERE  n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon=203), 0) as ausencia,
 				COALESCE((SELECT  SUM(n.monto) FROM nom_movimientos_nomina n 
  				  WHERE  n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha."
  				  AND    n.codcon IN (202, 601, 605)), 0) as isr,
				COALESCE((SELECT  SUM(n.monto) FROM nom_movimientos_nomina n 
  				  WHERE  n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha."
  				  AND    n.codcon IN (207, 208, 606, 607)), 0) as isr_gastos,
				(SELECT  COALESCE(SUM(n.monto), 0) FROM nom_movimientos_nomina n 
  				  WHERE  n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND (n.codcon BETWEEN 500 AND 507 
  				OR n.codcon BETWEEN 508 AND 599))  as descuentos,
				(SELECT  COALESCE(SUM(n.monto), 0) FROM nom_movimientos_nomina n 
  				  WHERE  n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon=508)  as cxc,
				(SELECT  COALESCE(SUM(n.monto), 0) FROM nom_movimientos_nomina n 
  				  WHERE  n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon=114)  as vacaciones";
				 //echo $sql3;exit;
		$res3 = $conexion->query($sql3);
		$neto=0;

		if($row3=$res3->fetch_array())
		{
			$MONTO_EXTRA_SALARIO = $MONTO_DOMINGO_SALARIO = 0;
			$MONTO_GR_EXTRA_SALARIO = $MONTO_GR_DOMINGO_SALARIO = 0;
			$AJUSTE = 0;



			$sql5 = "SELECT COALESCE(SUM(n.monto), 0) AS total_extra_salario  
					FROM   nom_movimientos_nomina n 
					WHERE  n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha."
					AND (  n.codcon IN (106, 108, 142, 143, 153, 152)
					 OR    n.codcon IN (111,116,123, 124, 125, 126, 144, 149, 150, 151)
					 OR    n.codcon IN (107, 109, 152, 153, 120, 121, 127, 122,112, 113, 127, 128, 129, 130, 131, 132,133, 134, 135, 136, 138, 139) )";
			$res5 =$conexion->query($sql5);
			if($row5=$res5->fetch_array()){ $MONTO_EXTRA_SALARIO = $row5['total_extra_salario']; }

			$sql5 = "SELECT COALESCE(SUM(n.monto), 0) AS total_domingo_salario  
					 FROM   nom_movimientos_nomina n 
					 WHERE  n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha."
					 AND (  n.codcon IN (112, 113, 127, 128, 129, 130, 131, 132)
					  OR    n.codcon IN (133, 134, 135, 136, 138, 139) )";
			$res5 =$conexion->query($sql5);


			// Calcular AJUSTE
			$sql5= "SELECT COALESCE(SUM(monto), 0) AS ajuste
					FROM   nom_movimientos_nomina n 
					WHERE  n.codcon=604
					AND    n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha;

			$res5 = $conexion->query($sql5);
			if($row5 = $res5->fetch_array() ){ $AJUSTE = $row5['ajuste']; }

			//$impuesto = ($row3['isr_gastos'] >0) ? $row3['isr'] - $row3['isr_gastos'] : $row3['isr'];

			
			

			//======================================================================================
			// HORAS EXTRA SALARIO
			if( ($MONTO_EXTRA_SALARIO + $MONTO_DOMINGO_SALARIO) <= 0 && $row3['horas_extras'] > 0 )
			{
				
				$HORAS_EXTRA_SALARIO = $row3['horas_extras'];
			}
			else
			{
				
				$HORAS_EXTRA_SALARIO = $MONTO_EXTRA_SALARIO + $MONTO_DOMINGO_SALARIO;			
			}


			// $objPHPExcel->getActiveSheet()->setCellValue('F'.$i, "=(C".$i."+F".$i.") * 9.75% ");
			// $objPHPExcel->getActiveSheet()->setCellValue('L'.$i, "=(E".$i."+G".$i.") * 9.75% ");
			if ($row3['mesxiii']!=0) {
				$mes13=$row3['mesxiii'];
				# code...
			}
			elseif ($row3['mesxiii2']!=0) {
				$mes13=$row3['mesxiii2'];
				# code...
			}
			//$salario_trabajador=$row3['salario']+$row3['uso_auto']+$row3['comision']+$row3['bono']+$HORAS_EXTRA_SALARIO+$row3['reembolso']+$row3['vacaciones']-$row3['tardanza']-$row3['ausencia'];
			$salario_trabajador=$row3['salario']+$row3['uso_auto']+$row3['comision']+$row3['bono']+$row3['reembolso']+$row3['vacaciones']-$row3['tardanza']-$row3['ausencia'];
					
			
			$neto2 = $row3['salario'] + $row3['vacaciones']+ $row3['reembolso']+ $row3['comision']+ $row3['bono'] + $HORAS_EXTRA_SALARIO 
				   - $row3['seguro_social'] - $row3['seguro_educativo'] - $row3['tardanza'] - $row3['ausencia']
				   - $row3['isr'] - $row3['isr_gastos'] - $row3['descuentos'] - $row3['cxc']- $AJUSTE;

			//$objPHPExcel->getActiveSheet()->setCellValue('Q'.$i, $neto);
			
			// =+C38+F38+G38+H38+I38+J38-K38-L38-M38-N38-O38-P38
			$neto = "='".$dia_fin." ".$mes_letras."'!R".$i; //$objPHPExcel->getActiveSheet()->getCell('Q'.$i)->getValue();
			

			
			

			$clave_isr 		= '-';			
			$ingreso_regular= $salario_trabajador;
			$otros_ingresos = $row3['vacaciones']+ $row3['reembolso']+ $row3['comision']+ $row3['bono'] + $HORAS_EXTRA_SALARIO;
			$total_Ingresos = $ingreso_regular+$otros_ingresos;

			$renta 			= '';
			$impuesto 		= $row3['isr'];
			$seguro_educativo = $row3['seguro_educativo'];
			$seguro_social = $row3['seguro_social'];
			$total_retener =  $row3['seguro_social'] + $row3['seguro_educativo'] + $row3['tardanza'] + $row3['ausencia'] + $row3['isr'] +$row3['isr_gastos'] + $row3['descuentos'] + $row3['cxc']- $AJUSTE;

			
			
		}

		//$horas_extras[] = $HORAS_EXTRA_SALARIO;

		//SELECT codcon, descrip, tipcon FROM nomconceptos ORDER BY tipcon, codcon, descrip
		
			
		
		
			$sql6 = "SELECT COALESCE( SUM( n.monto ) , 0 ) AS total_deduccion, c.codcon, c.descrip, c.tipcon
						FROM nom_movimientos_nomina AS n, nomconceptos AS c
						WHERE n.codcon = c.codcon
						AND n.codnom =".$codnom."
						AND n.tipnom =".$codtip."
						AND n.ficha =".$ficha."
						AND c.tipcon = 'D'
						GROUP BY c.codcon
						ORDER BY n.codcon ASC ";
			$res6 =$conexion->query($sql6);
				
			
			/*while($row6=$res6->fetch_array())
			{					
				$deduciones[] 	= array('cod' => $row6['codcon'],'descripcion'=>$row6['descrip'],'monto'=>$row6['total_deduccion']);
			}*/

			while($deduc=$res6->fetch_array())
			{
				//$dd++;
				$deducciones_monto  	+= $deduc['total_deduccion'];
				$key_to_index = $deduc['codcon'];				
				if(is_array($DEDUCCIONES_ARRAY[$key_to_index][$row['grupo']])) {//Modificamos

					# code...
					$DEDUCCIONES_ARRAY[$key_to_index][$row['grupo']]['periodo']+=$deduc['total_deduccion'];
					$DEDUCCIONES_ARRAY[$key_to_index][$row['grupo']]['mes']+=($deduc['total_deduccion']*2);
					$DEDUCCIONES_ARRAY[$key_to_index][$row['grupo']]['anho']+=(($deduc['total_deduccion']*2)*12);
					
				}
				else{ //no existe, lo agregamos
					//$ASIGNACIONES_ARRAY[$key_to_index]['hora'] 	+=0;
					//$dd++;
					$DEDUCCIONES_ARRAY[$key_to_index][$row['grupo']]= array('hora' 		=> 0,
																			'ingreso' 	=>$deduc['descrip'],
																			'periodo' 	=>$deduc['total_deduccion'],
																			'mes' 		=>($deduc['total_deduccion']*2),
																			'anho' 		=>(($deduc['total_deduccion']*2)*12)
																	
												);

				}


				if(is_array($DEDUCCIONES_ARRAY_GENERAL[$key_to_index])) {//Modificamos

					# code...
					$DEDUCCIONES_ARRAY_GENERAL[$key_to_index]['periodo']+=$deduc['total_deduccion'];
					$DEDUCCIONES_ARRAY_GENERAL[$key_to_index]['mes']+=($deduc['total_deduccion']*2);
					$DEDUCCIONES_ARRAY_GENERAL[$key_to_index]['anho']+=(($deduc['total_deduccion']*2)*12);
					
				}
				else{ //no existe, lo agregamos
					//$ASIGNACIONES_ARRAY[$key_to_index]['hora'] 	+=0;
					//$dd++;
					$DEDUCCIONES_ARRAY_GENERAL[$key_to_index]= array('hora' 		=> 0,
																			'ingreso' 	=>$deduc['descrip'],
																			'periodo' 	=>$deduc['total_deduccion'],
																			'mes' 		=>($deduc['total_deduccion']*2),
																			'anho' 		=>(($deduc['total_deduccion']*2)*12)
																	
												);

				}

				//$refcheque = $row8['cheque'];
			}
		

		
			
			$sql7 = "SELECT COALESCE( SUM( n.monto ) , 0 ) AS total_asignacion, c.codcon, c.descrip, c.tipcon
						FROM nom_movimientos_nomina AS n, nomconceptos AS c
						WHERE n.codcon = c.codcon
						AND n.codnom =".$codnom."
						AND n.tipnom =".$codtip."
						AND n.ficha =".$ficha."
						AND c.tipcon = 'A'
						GROUP BY c.codcon
						ORDER BY n.codcon ASC ";
			$res7 =$conexion->query($sql7);
				
			while($asig=$res7->fetch_array())
			{
				//$dd++;
				$asignaciones_monto += $asig['total_asignacion'];
				$key_to_index = $asig['codcon'];
				if(is_array($ASIGNACIONES_ARRAY[$key_to_index][$row['grupo']])) {//Modificamos
					# code...
					$ASIGNACIONES_ARRAY[$key_to_index][$row['grupo']]['hora']+=$rata_por_hora;
					$ASIGNACIONES_ARRAY[$key_to_index][$row['grupo']]['periodo']+=$asig['total_asignacion'];
					$ASIGNACIONES_ARRAY[$key_to_index][$row['grupo']]['mes']+=($asig['total_asignacion']*2);
					$ASIGNACIONES_ARRAY[$key_to_index][$row['grupo']]['anho']+=(($asig['total_asignacion']*2)*12);
					
				}
				else{ //no existe, lo agregamos
					//$ASIGNACIONES_ARRAY[$key_to_index]['hora'] 	+=0;

					//$dd++;
					$ASIGNACIONES_ARRAY[$key_to_index][$row['grupo']]= array('hora' 	=> $rata_por_hora,
																			'ingreso' 	=>$asig['descrip'],
																			'periodo' 	=>$asig['total_asignacion'],
																			'mes' 		=>($asig['total_asignacion']*2),
																			'anho' 		=>(($asig['total_asignacion']*2)*12)
																	
												);
				}

				if(is_array($ASIGNACIONES_ARRAY_GENERAL[$key_to_index])) {//Modificamos
					# code...
					$ASIGNACIONES_ARRAY_GENERAL[$key_to_index]['hora']+=$rata_por_hora;
					$ASIGNACIONES_ARRAY_GENERAL[$key_to_index]['periodo']+=$asig['total_asignacion'];
					$ASIGNACIONES_ARRAY_GENERAL[$key_to_index]['mes']+=($asig['total_asignacion']*2);
					$ASIGNACIONES_ARRAY_GENERAL[$key_to_index]['anho']+=(($asig['total_asignacion']*2)*12);
					
				}
				else{ //no existe, lo agregamos
					//$ASIGNACIONES_ARRAY[$key_to_index]['hora'] 	+=0;
					//$dd++;
					$ASIGNACIONES_ARRAY_GENERAL[$key_to_index]= array('hora' 	=> $rata_por_hora,
																			'ingreso' 	=>$asig['descrip'],
																			'periodo' 	=>$asig['total_asignacion'],
																			'mes' 		=>($asig['total_asignacion']*2),
																			'anho' 		=>(($asig['total_asignacion']*2)*12)
																	
												);
				}

				

				

				//$refcheque = $row8['cheque'];
			

			/*while($row7=$res7->fetch_array())
			{					
				if($row7['codcon']==100 || $row7['codcon']==145){
					$row7['codcon'] =$row7['valor'];
				}

				$asignaciones[] 	= array('cod' => $row7['codcon'],'descripcion'=>$row7['descrip'],'monto'=>$row7['total_deduccion']);
			}*/
		}
		
		$sql8 ="SELECT  n.refcheque  as cheque  FROM nom_movimientos_nomina n  WHERE n.codcon=100 AND n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha;
		  		$res8 =$conexion->query($sql8);
		  		while($row8=$res8->fetch_array())
				{
					$refcheque = $row8['cheque'];
					if ($row8['cheque']>0) {			
						$CANTIDAD_PAGO_CHEQUE++;
						$MONTO_TOTAL_CHEQUE += ($total_Ingresos-$deducciones_monto);
					}
					else{
						$CANTIDAD_PAGO_TRANSFERENCA++;
						$MONTO_TOTAL_TRANSFERENCIA += ($total_Ingresos-$deducciones_monto);
					}					
						///CANTIDAD_PAGO_EFECTIV
						//$MONTO_TOTAL_EFECTIVO
				}

		$trabajadores[] = array('nombre'=>$trabajador, 'primer_nombre'=> $primer_nombre, 'apellido'=>$apellido, 'neto'=> $neto,'ingreso_regular'=>$ingreso_regular,'otros_ingresos'=>$otros_ingresos,'total_Ingresos'=>$total_Ingresos, 'neto2'=>$neto2,'seguro_social'=>$seguro_social,'seguro_educativo'=>$seguro_educativo,'total_retener'=>$total_retener,'impuesto'=>$impuesto,'vacaciones'=>$check_vacacion,'desc_grupo'=>$grupo,'cod_grupo'=>$row['grupo']);

		$i++;
	}


	$trabajadores2[$codorg]=$trabajadores;
	$trabajadores =array();
	
}

//$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':Q'.$i);
						  


foreach ($trabajadores as $key => $registro) 
{
    $aux[$key] = $registro['apellido'];
}

array_multisort($aux, SORT_ASC, $trabajadores);

if($NOMINA != 'TEMPORAL'){
$j = $i;
$i = $i + 2; 
$i++;
$ss=$i;
$i++;
$se=$i;

$i=$i+2;

}


//===========================================================================

/*$fecha_desde = explode('/', $desde);
$fecha_hasta = explode('/', $hasta);*/

$objPHPExcel->createSheet();
$objPHPExcel->setActiveSheetIndex(0);

$objPHPExcel->getActiveSheet()->setTitle('Resumido pago');

$objPHPExcel->getActiveSheet()
			->setCellValue('B1', 'Control #.')
			->setCellValue('C1', strtoupper($empresa))
			->setCellValue('C2', 'Planilla Regular No.18')
			//->setCellValue('C1', '')
			->setCellValue('C3', strtoupper($NOMINA." Del ".fecha($desde).' al '.fecha($hasta)))
			->setCellValue('J1', 'Ref:MM-703' )
			->setCellValue('J2', 'Formato Ejecutivo')
			->setCellValue('J3', $estatus_mov_nomina);								
			//->setCellValue('J2', PHPExcel_Shared_Date::FormattedPHPToExcel($anio, $mes_numero, $dia_fin) )
			
$objPHPExcel->getActiveSheet()->getStyle('C1:I1')->getFont()->setSize(16);
$objPHPExcel->getActiveSheet()->getStyle('C1:I1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->mergeCells('C1:I1');

$objPHPExcel->getActiveSheet()->getStyle('C3:I3')->getFont()->setSize(12);
$objPHPExcel->getActiveSheet()->getStyle('C3:I3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->mergeCells('C3:I3');

$objPHPExcel->getActiveSheet()->getStyle('C2:I2')->getFont()->setSize(12);
$objPHPExcel->getActiveSheet()->getStyle('C2:I2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->mergeCells('C2:I2');

$objPHPExcel->getActiveSheet()->getStyle('J1:J3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
$objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(20);

$objPHPExcel->getActiveSheet()->mergeCells('J1:K1');
$objPHPExcel->getActiveSheet()->mergeCells('J2:K2');
$objPHPExcel->getActiveSheet()->mergeCells('J3:K3');

cellColor('A1:k6', 'FFFFFF');



/*$objPHPExcel->getActiveSheet()->getStyle('C2:D2')->getFont()->setSize(9);
$objPHPExcel->getActiveSheet()->getStyle('C2:D2')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->mergeCells('C2:D2');*/

/*$objPHPExcel->getActiveSheet()->getStyle('F2')->getFont()->setSize(12);
$objPHPExcel->getActiveSheet()->getStyle('F2')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('F2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('F2')->applyFromArray(allBordersThin());*/




$objPHPExcel->getActiveSheet()->getStyle('C3:F3')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

$objPHPExcel->getActiveSheet()->getStyle('B3')->applyFromArray(
array(
	'font'      => array(
		//'name'         => 'Times New Roman',
		'bold'         => true,
		'color'        => array('rgb' => '800000'),
		'italic'       => true,
		'size'         => 16
	),
	'alignment' => array(
		'horizontal'  => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
		'vertical'    => PHPExcel_Style_Alignment::VERTICAL_CENTER,
		//'wrap'        => true
	)
));

$objPHPExcel->getActiveSheet()->getStyle('E3')->applyFromArray(
array(
	'font'      => array(
		'bold'         => true,
		'color'        => array('rgb' => '800000'),
		'size'         => 16
	),
	'alignment' => array(
		'horizontal'  => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
		'vertical'    => PHPExcel_Style_Alignment::VERTICAL_CENTER,
		'wrap'        => true
	)
));

//$objPHPExcel->getActiveSheet()->mergeCells('B6:C6');



//$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(false);
//$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth("5");  
/*$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);*/

//$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth("10");

//$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
//$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth("10");


//$objPHPExcel->getActiveSheet()->getStyle('B6:K6')->getAlignment()->setWrapText(true);

$i=5;
$ini = $i;
$fin = 0;
$cont_global=0;
$TotalRegular = 0;
$TotalIngresoregular=0;
$TotalOtrosIngresos = 0;
$TotalImpuesto = 0;
$TotalSeguro = 0;
$TotalSeguroEducativo=0;
$TotalDeduccion=0;
foreach ($trabajadores2 as $key => $grupo) 
{	
	if(count($grupo)>=1){
		 
		$i++;
		$cont=0;
		$cod_grupo='';

		$objPHPExcel->getActiveSheet()
					->setCellValue('B'.$i, 'Núm.')
					->setCellValue('C'.$i, 'Nombre')
					->setCellValue('D'.$i, "Clave\r ISR")
					->setCellValue('E'.$i, "Ingreso\r Regular")
					->setCellValue('F'.$i, "Otros\r Ingresos")
					->setCellValue('G'.$i, "Total\r Ingresos")
					->setCellValue('H'.$i, "Impuesto\r /Renta")
					->setCellValue('I'.$i, "Seguros\r Soc.+Edu.")
					->setCellValue('J'.$i, "Total a \r Retener")			
					->setCellValue('k'.$i, "Neto a\r Pagar"); 

		$objPHPExcel->getActiveSheet()->getDefaultStyle()->getFont()->setName('Times New Roman');
		//$objPHPExcel->getActiveSheet()->getStyle('B1:F1')->getFont()->setName('Times New Roman');
		
		$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':K'.$i)->getFont()->setSize(10);
		$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':K'.$i)->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':K'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':K'.$i)->getBorders()->getTop()
		        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
		$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':K'.$i)->getBorders()->getBottom()
		        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

		$objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(40);
		$intro_grupo =false;
		$ini=$i+1;
		foreach ($grupo as $key => $row) {
			$cont_global++;
			$cod_grupo = $row['cod_grupo'];
			$cont++;
			$i++;

			if(!$intro_grupo){
				$objPHPExcel->getActiveSheet()
						->setCellValue('B'.$i, '>     '.$row['desc_grupo']);
				$objPHPExcel->getActiveSheet()->mergeCells('B'.$i.':K'.$i);
				$objPHPExcel->getActiveSheet()->getStyle('B'.$i)->getFont()->setItalic(true);
				$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':K'.$i)->getFont()->setBold(true);
				$objPHPExcel->getActiveSheet()->getStyle('B'.$i)->getFont()->setSize(12);
				$i++;
			}
			$intro_grupo =true;

			//echo $row['nombre'].' '.$row['neto'].'<br/>';
			$objPHPExcel->getActiveSheet()
						->setCellValue('B'.$i, $cont )
						->setCellValue('C'.$i, strtoupper($row['apellido'].', '.$row['primer_nombre']).$row['vacaciones'])
						->setCellValue('D'.$i, '')								
						->setCellValue('E'.$i, $row['ingreso_regular'])
						->setCellValue('F'.$i, $row['otros_ingresos'])
						->setCellValue('G'.$i, $row['total_Ingresos'])
						->setCellValue('H'.$i, $row['impuesto'])
						->setCellValue('I'.$i, $row['seguro_social'])
						->setCellValue('J'.$i, $row['total_retener'])				
						->setCellValue('K'.$i, '=G'.$i.'-J'.$i);
			/*$TotalAPagar 			+=$row['neto'];
			$Totalingresoregular 	+= $row['ingreso_regular'];
			$TotalOtrosIngresos 	+= $row['otros_ingresos'];
			$TotalTotalIngreso 		+= $row['total_Ingresos'];
			$TotalImpuesto			+= $row['impuesto'];
			$TotalSeguro 			+= $row['seguro_social'];
			$TotalTotalRetener 		+= $row['total_retener'];*/

			cellColor('A'.$i.':K'.$i, 'FFFFFF');

			$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':F'.$i)->getFont()->setSize(10);

			//$objPHPExcel->getActiveSheet()->getStyle('C'.$i)->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('C'.$i)->getFont()->setItalic(true);
			//$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':K'.$i)->getBorders()->getBottom()
		        					      //->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);



			$objPHPExcel->getActiveSheet()->getStyle('E'.$i.':K'.$i)->getNumberFormat()
									      ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
							      

			//$i++
			$TotalAPagar 		+=$row['total_Ingresos']-$row['total_retener'];		
			$TotalOtrosIngresos +=	$row['otros_ingresos'];			
			$TotalImpuesto 		+= 	$row['impuesto'];	
			$TotalSeguro 		+= $row['seguro_social'];
			$TotalSeguroEducativo +=$row['seguro_educativo'];
			
		}

		$fin =$i;
		$i++;$i++;

		$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':K'.$i)->getFont()->setSize(9);
		$objPHPExcel->getActiveSheet()
					->setCellValue('C'.$i, $cont.' Empleados')
					->setCellValue('D'.$i, "Horas" )
					->setCellValue('E'.$i, "Ingreso" )
					->setCellValue('G'.$i, "Total" )
					->setCellValue('H'.$i, "Retenciones" )
					->setCellValue('K'.$i, "Monto");
		$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':G'.$i)->getBorders()->getBottom()
		        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

		$objPHPExcel->getActiveSheet()->getStyle('K'.$i)->getBorders()->getBottom()
		        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

		$i++;		        					  
		$xx=$i;
		$yy=$i;
		$ii=$i;
		foreach ($ASIGNACIONES_ARRAY as $key => $value) {			
			//foreach ($value as $key2 =>$asignacion) {
				$objPHPExcel->getActiveSheet()
							->setCellValue('D'.$xx, $value[$cod_grupo]['hora'])
							->setCellValue('E'.$xx, strtoupper($value[$cod_grupo]['ingreso']))
							->setCellValue('G'.$xx, $value[$cod_grupo]['periodo']);
							//->setCellValue('E'.$xx, $value[$grupo]['mes'])
							//->setCellValue('F'.$xx, $value[$grupo]['anho']);
				$objPHPExcel->getActiveSheet()->getStyle('D'.$xx.':G'.$xx)->getNumberFormat()
										  ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);	
				$objPHPExcel->getActiveSheet()->getStyle('B'.$xx.':F'.$xx)->getFont()->setSize(9);			
				$objPHPExcel->getActiveSheet()->getStyle('E'.$xx)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);		
				$objPHPExcel->getActiveSheet()->mergeCells('E'.$xx.':F'.$xx);
				$TotalIngresoregular+=$value[$cod_grupo]['periodo'];
			//}
			$xx++;
		}


		foreach ($DEDUCCIONES_ARRAY as $key => $value) {		
			//foreach ($value as $key2 =>$asignacion) {				
				$objPHPExcel->getActiveSheet()
							//->setCellValue('D'.$i, $row2['cod'])
							->setCellValue('I'.$yy, strtoupper($value[$cod_grupo]['ingreso']))
							->setCellValue('K'.$yy, $value[$cod_grupo]['periodo']);
							//->setCellValue('J'.$yy, $value[$grupo]['mes'])
							//->setCellValue('K'.$yy, $value[$grupo]['anho']);
				$TotalTotalRetener+=$value[$cod_grupo]['periodo'];
				$objPHPExcel->getActiveSheet()->getStyle('K'.$yy)->getNumberFormat()
										  	  ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);	
				$objPHPExcel->getActiveSheet()->getStyle('G'.$yy.':K'.$yy)->getFont()->setSize(9);			
				$objPHPExcel->getActiveSheet()->getStyle('I'.$yy.':K'.$yy)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

				$objPHPExcel->getActiveSheet()->mergeCells('I'.$yy.':J'.$yy);

				$TotalDeduccion+=$TotalTotalRetener;
				$yy++;
			//}			
		}

		if($xx>$yy)
			$i=$xx;
		else
			$i=$yy;

		//$i++;
		//cellColor('A'.$i.':K'.($i+2), 'FFFFFF');
		$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':K'.$i)->getBorders()->getBottom()
		        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		//$i++;
		$objPHPExcel->getActiveSheet()->getStyle('C'.$i)->getFont()->setSize(10);
		$objPHPExcel->getActiveSheet()->getStyle('C'.$i)->getFont()->setBold(true);
		//$objPHPExcel->getActiveSheet()->mergeCells('C'.$i.':D'.$i);
		$objPHPExcel->getActiveSheet()
					->setCellValue('B'.$i, 'Sub. total:')
					->setCellValue('C'.$i, $cod_grupo)//CD
					->setCellValue('E'.$i, "=SUM(E".$ini.":E".($fin).")" )
					->setCellValue('F'.$i, "=SUM(F".$ini.":F".($fin).")" )
					->setCellValue('G'.$i, "=SUM(G".$ini.":G".($fin).")" )
					->setCellValue('H'.$i, "=SUM(H".$ini.":H".($fin).")" )
					->setCellValue('I'.$i, "=SUM(I".$ini.":I".($fin).")" )
					->setCellValue('J'.$i, "=SUM(J".$ini.":J".($fin).")" )
					->setCellValue('K'.$i, "=SUM(K".$ini.":K".($fin).")" );
		$objPHPExcel->getActiveSheet()->mergeCells('C'.$i.':D'.$i);
		//$TotalAPagar=$objPHPExcel->getActiveSheet()->getCell('K'.$i)->getCalculatedValue();
			$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':K'.$i)->getBorders()->getBottom()
		        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle('E'.$i.':K'.$i)->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('E'.$i.':K'.$i)->getNumberFormat()
									      ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);		
		$i++;									      
	}	
	   
}

//cellColor('A'.$i.':K'.($i+2), 'FFFFFF');


			/*->setCellValue('H'.$i, "=SUM(H7:H".($i-2).")" )
			->setCellValue('I'.$i, "=SUM(I7:I".($i-2).")" )
			->setCellValue('J'.$i, "=SUM(J7:J".($i-2).")" )
			->setCellValue('K'.$i, "=SUM(K7:K".($i-2).")" );*/

//$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':K'.$i)->getBorders()->getBottom()
        					 // ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
//$objPHPExcel->getActiveSheet()->getStyle('K'.$i)->getFont()->setBold(true);

/*$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);*/

/*$TotalIngresoregular 	= $objPHPExcel->getActiveSheet()->getCell('G'.$i)->getCalculatedValue();*/
//$TotalRegular 			= $objPHPExcel->getActiveSheet()->getCell('E'.$i)->getCalculatedValue();
/*$TotalOtrosIngresos  	= $objPHPExcel->getActiveSheet()->getCell('F'.$i)->getCalculatedValue();*/
//$TotalIngresos  		= $objPHPExcel->getActiveSheet()->getCell('G'.$i)->getCalculatedValue();
//$ImpuestoRenta			= $objPHPExcel->getActiveSheet()->getCell('H'.$i)->getCalculatedValue();
//$SeguraSocial 			= $objPHPExcel->getActiveSheet()->getCell('I'.$i)->getCalculatedValue();
/*$TotalTotalRetener   	= $objPHPExcel->getActiveSheet()->getCell('J'.$i)->getCalculatedValue();*/



$importe=$TotalAPagar;
			/*
// indicamos todas las monedas posibles
 $monedas=array(100, 50, 20, 10, 5, 1, 0.5, 0.25, 0.10, 0.05, 0.01);
//Dolares
 
// creamos un array con la misma cantidad de monedas
// Este array contendra las monedas a devolver
$cambio=array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
 
// Recorremos todas las monedas
for($ixx=0; $ixx<count($monedas); $ixx++)
{
    // Si el importe actual, es superior a la moneda
    if($importe>=$monedas[$ixx])
    {
 
        // obtenemos cantidad de monedas
        $cambio[$ixx]=floor($importe/$monedas[$ixx]);
 
        // actualizamos el valor del importe que nos queda por didivir
        $importe=$importe-($cambio[$ixx]*$monedas[$ixx]);
    }
}
 $i+=2;
// Bucle para mostrar el resultado
for($ix=0; $ix<count($monedas); $ix++)
{

	$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':F'.$i)->getFont()->setSize(12);
	$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':F'.$i)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_JUSTIFY);

	$objPHPExcel->getActiveSheet()->mergeCells('B'.$i.':F'.$i);

    if($cambio[$ix]>0)
    {
        if($monedas[$ix]>=1)
           $objPHPExcel->getActiveSheet()
			->setCellValue('B'.$i, 'Hay: '.$cambio[$ix].' Billetes de: '.$monedas[$ix]);
            //echo "Hay: ".$cambio[$ix]." billetes de: ".$monedas[$ix]." &euro;<br>";
        else
        	$objPHPExcel->getActiveSheet()
			->setCellValue('B'.$i,  'Hay: '.$cambio[$ix].' Monedas de: '.$monedas[$ix]);
           // echo "Hay: ".$cambio[$ix]." monedas de: ".$monedas[$ix]." &euro;<br>";
		$i++;
    }
    
}

$i+=2;
$objPHPExcel->getActiveSheet()
			->setCellValue('B'.$i, 'TOTAL PLANILLA NETA QUINCENAL');

$objPHPExcel->getActiveSheet()->mergeCells('B'.$i.':F'.$i);

$objPHPExcel->getActiveSheet()->getStyle('B'.$i)->applyFromArray(
array(
	'font'      => array(
		'bold'         => true,
		'size'         => 12
	),
	'alignment' => array(
		'horizontal'  => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
		'vertical'    => PHPExcel_Style_Alignment::VERTICAL_CENTER,
		'wrap'        => true
	)
));

$objPHPExcel->getActiveSheet()->getStyle('F'.$i)->applyFromArray(
array(
	'font'      => array(
		'bold'         => true,
		'size'         => 16
	)
));*/

//$objPHPExcel->getActiveSheet()->getStyle('E'.$i.':K'.$i)->applyFromArray(allBordersThin());

$objPHPExcel->getActiveSheet()->getStyle('E'.$i)->getNumberFormat()
							  ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
$objPHPExcel->getActiveSheet()->getStyle('F'.$i)->getNumberFormat()
							  ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
$objPHPExcel->getActiveSheet()->getStyle('G'.$i)->getNumberFormat()
							  ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
$objPHPExcel->getActiveSheet()->getStyle('H'.$i)->getNumberFormat()
							  ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
$objPHPExcel->getActiveSheet()->getStyle('I'.$i)->getNumberFormat()
							  ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
$objPHPExcel->getActiveSheet()->getStyle('J'.$i)->getNumberFormat()
							  ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);							  




$i++;
/*
$k=$i;
foreach ($asignaciones as $key => $row) 
{
	$objPHPExcel->getActiveSheet()
				->setCellValue('D'.$k, $row['cod'])
				->setCellValue('E'.$k, strtoupper($row['descripcion']))
				->setCellValue('G'.$k, $row['monto']);
	$objPHPExcel->getActiveSheet()->getStyle('G'.$k)->getNumberFormat()
							  ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);	
	$objPHPExcel->getActiveSheet()->getStyle('D'.$k.':G'.$k)->getFont()->setSize(8);	
	
	$objPHPExcel->getActiveSheet()->mergeCells('E'.$k.':F'.$k);						  
	$k++;						  
}*/

/*$objPHPExcel->getActiveSheet()
				->setCellValue('H32', 'eeeeeeeee');
$objPHPExcel->getActiveSheet()->getStyle('H32')->getFont()->setSize(9);*/
$j=$i;
/*
foreach ($deduciones as $key => $row) 
{
	$objPHPExcel->getActiveSheet()
				->setCellValue('H'.$j, $row['cod'])
				->setCellValue('I'.$j, strtoupper($row['descripcion']))
				->setCellValue('K'.$j, $row['monto']);
	$objPHPExcel->getActiveSheet()->getStyle('K'.$j)->getNumberFormat()
							  ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);	
	$objPHPExcel->getActiveSheet()->getStyle('H'.$j.':K'.$j)->getFont()->setSize(8);	
	
	$objPHPExcel->getActiveSheet()->mergeCells('I'.$j.':J'.$j);						  
	$j++;						  
}*/

/*if($j>=$k){
	$i=$j;	
}
else{
	$i=$k;	
}
*/
$objPHPExcel->getActiveSheet()
			->setCellValue('B'.$i, 'Neto a Pagar:')
			->setCellValue('C'.$i, $TotalAPagar )//->setCellValue('K'.$i, "=SUM(K7:K".($i-2).")" );
			->setCellValue('E'.$i, "SubTotal Ingresos:" )
			->setCellValue('G'.$i, $TotalIngresoregular )				
			->setCellValue('H'.$i, "SubTotal Retenciones:" )						
			->setCellValue('J'.$i, $TotalTotalRetener );
//$TotalAPagar=$objPHPExcel->getActiveSheet()->getCell('K'.$i)->getCalculatedValue();
$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':K'.$i)->getBorders()->getBottom()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle('C'.$i)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('G'.$i)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('J'.$i)->getFont()->setBold(true);

$objPHPExcel->getActiveSheet()->getStyle('K'.$i)->getNumberFormat()
							  ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);	
$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':K'.$i)->getFont()->setSize(12);		
//$objPHPExcel->getActiveSheet()->mergeCells('I'.$i.':J'.$i);	

$objPHPExcel->getActiveSheet()->mergeCells('H'.$i.':I'.$i);	
$objPHPExcel->getActiveSheet()->mergeCells('J'.$i.':K'.$i);	
$objPHPExcel->getActiveSheet()->mergeCells('E'.$i.':F'.$i);	

$objPHPExcel->getActiveSheet()->getStyle('C'.$i)->getNumberFormat()
							  ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);	
$objPHPExcel->getActiveSheet()->getStyle('G'.$i)->getNumberFormat()
							  ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);	
$objPHPExcel->getActiveSheet()->getStyle('J'.$i)->getNumberFormat()
							  ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);									  							  

			
cellColor('A7:K'.($i+2), 'FFFFFF');


$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(7);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(19);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(17);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(17);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(18);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(17);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(30);
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(18);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(17);
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(18);
$objPHPExcel->getActiveSheet()->setSelectedCells('F'.($i+20));


//$objPHPExcel->createSheet();
$objPHPExcel->setActiveSheetIndex(1); 
$objPHPExcel->getActiveSheet()->setTitle('Resumido pago general');


$objPHPExcel->getActiveSheet()			
			->setCellValue('B1', strtoupper($empresa))
			->setCellValue('B2', 'Planilla Regular No.18')
			//->setCellValue('B1', '')
			->setCellValue('B3', strtoupper($NOMINA." Del ".fecha($desde).' al '.fecha($hasta)))
			->setCellValue('J1', 'Ref:MM-703' )
			->setCellValue('J2', 'Formato Ejecutivo')
			->setCellValue('J3', $estatus_mov_nomina)								
			//->setCellValue('J2', PHPExcel_Shared_Date::FormattedPHPToExcel($anio, $mes_numero, $dia_fin) )
			->setCellValue('B6', 'Núm.')
			->setCellValue('C6', 'Nombre')
			->setCellValue('D6', "Clave\r ISR")
			->setCellValue('E6', "Ingreso\r Regular")
			->setCellValue('F6', "Otros\r Ingresos")
			->setCellValue('G6', "Total\r Ingresos")
			->setCellValue('H6', "Impuesto\r /Renta")
			->setCellValue('I6', "Seguros\r Soc.+Edu.")
			->setCellValue('J6', "Total a \r Retener")			
			->setCellValue('k6', "Neto a\r Pagar"); 

$objPHPExcel->getActiveSheet()->getDefaultStyle()->getFont()->setName('Times New Roman');
//$objPHPExcel->getActiveSheet()->getStyle('B1:F1')->getFont()->setName('Times New Roman');
$objPHPExcel->getActiveSheet()->getStyle('B1:I1')->getFont()->setSize(16);
$objPHPExcel->getActiveSheet()->getStyle('B1:I1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$objPHPExcel->getActiveSheet()->mergeCells('B1:I1');

$objPHPExcel->getActiveSheet()->getStyle('B3:I3')->getFont()->setSize(12);
$objPHPExcel->getActiveSheet()->getStyle('B3:I3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$objPHPExcel->getActiveSheet()->mergeCells('B3:I3');

$objPHPExcel->getActiveSheet()->getStyle('B2:I2')->getFont()->setSize(12);
$objPHPExcel->getActiveSheet()->getStyle('B2:I2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$objPHPExcel->getActiveSheet()->mergeCells('B2:I2');


/*$objPHPExcel->getActiveSheet()->getStyle('J2')->getNumberFormat()							  
							  ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX15);	*/

$objPHPExcel->getActiveSheet()->getStyle('J1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
$objPHPExcel->getActiveSheet()->mergeCells('J1:K1');
$objPHPExcel->getActiveSheet()->getStyle('J2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
$objPHPExcel->getActiveSheet()->mergeCells('J2:K2');
$objPHPExcel->getActiveSheet()->getStyle('J3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
$objPHPExcel->getActiveSheet()->mergeCells('J3:K3');
							  

$objPHPExcel->getActiveSheet()->getStyle('C3:F3')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('B6:K6')->getFont()->setSize(10);
$objPHPExcel->getActiveSheet()->getStyle('B6:K6')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('B6:K6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('B6:K6')->getBorders()->getTop()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
$objPHPExcel->getActiveSheet()->getStyle('B6:K6')->getBorders()->getBottom()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

$objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(20);
$objPHPExcel->getActiveSheet()->getRowDimension('6')->setRowHeight(40);



//$i++;



$r = 7;

$objPHPExcel->getActiveSheet()->getStyle('C'.$r)->getFont()->setSize(10);
$objPHPExcel->getActiveSheet()->getStyle('C'.$r)->getFont()->setBold(true);
//$objPHPExcel->getActiveSheet()->mergeCells('C'.$i.':D'.$i);
$objPHPExcel->getActiveSheet()
			->setCellValue('B'.$r, 'Total:')
			->setCellValue('C'.$r, strtoupper($empresa))
			->setCellValue('E'.$r, $TotalIngresoregular  )
			->setCellValue('F'.$r, $TotalOtrosIngresos )
			->setCellValue('G'.$r, $TotalOtrosIngresos+$TotalIngresoregular)
			->setCellValue('H'.$r, $TotalImpuesto )
			->setCellValue('I'.$r, $TotalSeguro + $TotalSeguroEducativo)
			->setCellValue('J'.$r, $TotalTotalRetener )
			->setCellValue('K'.$r, $TotalAPagar );

$objPHPExcel->getActiveSheet()->getStyle('B'.$r.':K'.$r)->getBorders()->getBottom()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);        					  
$objPHPExcel->getActiveSheet()->getStyle('K'.$r)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('E'.$r)->getNumberFormat()
							      ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);	
$objPHPExcel->getActiveSheet()->getStyle('F'.$r)->getNumberFormat()
							      ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);	
$objPHPExcel->getActiveSheet()->getStyle('G'.$r)->getNumberFormat()
							      ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);	
$objPHPExcel->getActiveSheet()->getStyle('H'.$r)->getNumberFormat()
							      ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);	
$objPHPExcel->getActiveSheet()->getStyle('I'.$r)->getNumberFormat()
							      ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);	
$objPHPExcel->getActiveSheet()->getStyle('J'.$r)->getNumberFormat()
							      ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);	
$objPHPExcel->getActiveSheet()->getStyle('K'.$r)->getNumberFormat()
							      ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);	

$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true); 
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true); 
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true); 
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true); 
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true); 
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true); 
$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true); 

$r++;





$objPHPExcel->getActiveSheet()->getStyle('C'.$r.':K'.$r)->getFont()->setSize(9);
$objPHPExcel->getActiveSheet()
			->setCellValue('C'.$r,$cont_global.' Empleados')
			->setCellValue('D'.$r, "Horas" )
			->setCellValue('E'.$r, "Ingreso" )
			->setCellValue('G'.$r, "Total" )
			->setCellValue('H'.$r, "Retenciones" )
			->setCellValue('K'.$r, "Monto" );
$objPHPExcel->getActiveSheet()->getStyle('C'.$r.':G'.$r)->getBorders()->getBottom()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

$objPHPExcel->getActiveSheet()->getStyle('K'.$r)->getBorders()->getBottom()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$r++;
$k=$r;
foreach ($ASIGNACIONES_ARRAY_GENERAL as $key => $row) 
{



	$objPHPExcel->getActiveSheet()
				->setCellValue('D'.$k, $row['hora'])
				->setCellValue('E'.$k, strtoupper($row['ingreso']))
				->setCellValue('G'.$k, $row['periodo']);
	$objPHPExcel->getActiveSheet()->getStyle('G'.$k)->getNumberFormat()
							  ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);	
	$objPHPExcel->getActiveSheet()->getStyle('D'.$k.':G'.$k)->getFont()->setSize(8);	
	
	$objPHPExcel->getActiveSheet()->mergeCells('E'.$k.':F'.$k);						  
	$k++;						  
}

/*$objPHPExcel->getActiveSheet()
				->setCellValue('H32', 'eeeeeeeee');
$objPHPExcel->getActiveSheet()->getStyle('H32')->getFont()->setSize(9);*/
$j=$r;

foreach ($DEDUCCIONES_ARRAY_GENERAL as $key => $row) 
{
	$objPHPExcel->getActiveSheet()
				->setCellValue('H'.$j, $row['cod'])
				->setCellValue('I'.$j, strtoupper($row['ingreso']))
				->setCellValue('K'.$j, $row['periodo']);
	$objPHPExcel->getActiveSheet()->getStyle('K'.$j)->getNumberFormat()
							  ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);	
	$objPHPExcel->getActiveSheet()->getStyle('H'.$j.':K'.$j)->getFont()->setSize(8);	
	
	$objPHPExcel->getActiveSheet()->mergeCells('I'.$j.':J'.$j);						  
	$j++;						  
}

if($j>=$k){
	$r=$j;	
}
else{
	$r=$k;	
}

$objPHPExcel->getActiveSheet()
			->setCellValue('B'.$r, 'Neto a Pagar:')
			->setCellValue('C'.$r, $TotalAPagar )//->setCellValue('K'.$i, "=SUM(K7:K".($i-2).")" );
			->setCellValue('E'.$r, "SubTotal Ingresos:" )
			->setCellValue('G'.$r, $TotalIngresoregular )				
			->setCellValue('H'.$r, "SubTotal Retenciones:" )						
			->setCellValue('K'.$r, $TotalTotalRetener );
//$TotalAPagar=$objPHPExcel->getActiveSheet()->getCell('K'.$i)->getCalculatedValue();
$objPHPExcel->getActiveSheet()->getStyle('B'.$r.':K'.$r)->getBorders()->getBottom()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle('C'.$r)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('G'.$r)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('K'.$r)->getFont()->setBold(true);

$objPHPExcel->getActiveSheet()->getStyle('K'.$r)->getNumberFormat()
							  ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);	
$objPHPExcel->getActiveSheet()->getStyle('B'.$r.':K'.$r)->getFont()->setSize(8);		
$objPHPExcel->getActiveSheet()->mergeCells('I'.$r.':J'.$r);	

$objPHPExcel->getActiveSheet()->mergeCells('H'.$r.':J'.$r);	
$objPHPExcel->getActiveSheet()->mergeCells('E'.$r.':F'.$r);	

$objPHPExcel->getActiveSheet()->getStyle('C'.$r)->getNumberFormat()
							  ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);	
$objPHPExcel->getActiveSheet()->getStyle('G'.$r)->getNumberFormat()
							  ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);	
$objPHPExcel->getActiveSheet()->getStyle('K'.$r)->getNumberFormat()
							  ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);	


$r++;
$r++;

$objPHPExcel->getActiveSheet()			
			->setCellValue('E'.$r,"Total de\r Ingresos" )			
			//->setCellValue('B1', '')																	
			->setCellValue('F'.$r, "Impuesto\r S/ Renta")
			->setCellValue('G'.$r, "Seguro\r Social")
			->setCellValue('H'.$r, "Seguro\r Educativo")
			->setCellValue('I'.$r, "Cuota\r Sindical")
			->setCellValue('J'.$r, "Total\r Acreedores")
			->setCellValue('K'.$r, "Neto a \r Pagar");
$objPHPExcel->getActiveSheet()->getRowDimension($r)->setRowHeight(40);			
$r++;
$objPHPExcel->getActiveSheet()			
			->setCellValue('E'.$r,$TotalIngresoregular )			
			//->setCellValue('B1', '')																	
			->setCellValue('F'.$r, $TotalImpuesto )
			->setCellValue('G'.$r, $TotalSeguro)
			->setCellValue('H'.$r, $TotalSeguroEducativo)
			->setCellValue('I'.$r, '0')
			->setCellValue('J'.$r, $TotalTotalRetener)
			->setCellValue('K'.$r, $TotalAPagar);			
			

$objPHPExcel->getActiveSheet()->getStyle('E'.$r)->getNumberFormat()
							  ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);	
$objPHPExcel->getActiveSheet()->getStyle('F'.$r)->getNumberFormat()
							  ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);	
$objPHPExcel->getActiveSheet()->getStyle('G'.$r)->getNumberFormat()
							  ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

$objPHPExcel->getActiveSheet()->getStyle('H'.$r)->getNumberFormat()
							  ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);	
$objPHPExcel->getActiveSheet()->getStyle('I'.$r)->getNumberFormat()
							  ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);	
$objPHPExcel->getActiveSheet()->getStyle('J'.$r)->getNumberFormat()
							  ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);	

$objPHPExcel->getActiveSheet()->getStyle('K'.$r)->getNumberFormat()
							  ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);	

$objPHPExcel->getActiveSheet()->getStyle('E'.($r-1).':K'.$r)->applyFromArray(allBordersThin()); 
$r++;
$r++;
$objPHPExcel->getActiveSheet()			
			->setCellValue('D'.$r,"CONTROL DE EMPLEADOS" )			
			//->setCellValue('B1', '')																	
			->setCellValue('H'.$r, "CONTROL DE PLANILLA");
$objPHPExcel->getActiveSheet()->getStyle('B'.$r.':K'.$r)->getFont()->setSize(12);	
$objPHPExcel->getActiveSheet()->getStyle('B'.$r.':K'.$r)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

$objPHPExcel->getActiveSheet()->mergeCells('D'.$r.':G'.$r);	
$objPHPExcel->getActiveSheet()->mergeCells('H'.$r.':K'.$r);					
			
$objPHPExcel->getActiveSheet()->getRowDimension($r)->setRowHeight(20);	

$r++;
$objPHPExcel->getActiveSheet()			
			->setCellValue('D'.$r,'Activos' )
			->setCellValue('E'.$r,'Vacaciones' )			
			//->setCellValue('B1', '')																	
			->setCellValue('F'.$r, 'Licencias')
			->setCellValue('G'.$r, 'Total')
			->setCellValue('H'.$r, 'Efectivo')
			->setCellValue('I'.$r, "Cheques")
			->setCellValue('J'.$r, "Transferencias")
			->setCellValue('K'.$r, "Total");	

$r++;







$objPHPExcel->getActiveSheet()			
			->setCellValue('D'.$r,$CANTIDAD_ACTIVO )
			->setCellValue('E'.$r,$CANTIDAD_VACACIONES )			
			//->setCellValue('B1', '')																	
			->setCellValue('F'.$r, 0)
			->setCellValue('G'.$r, $cont_global)
			->setCellValue('H'.$r, $CANTIDAD_PAGO_EFECTIVO)
			->setCellValue('I'.$r, $CANTIDAD_PAGO_CHEQUE)
			->setCellValue('J'.$r, $CANTIDAD_PAGO_TRANSFERENCA)
			->setCellValue('K'.$r, $cont_global);	



$r++;
$objPHPExcel->getActiveSheet()			
			->setCellValue('D'.$r,'' )
			->setCellValue('E'.$r,'' )			
			//->setCellValue('B1', '')																	
			->setCellValue('F'.$r, '')
			->setCellValue('G'.$r, '')
			->setCellValue('H'.$r, $MONTO_TOTAL_EFECTIVO)
			->setCellValue('I'.$r, $MONTO_TOTAL_CHEQUE)
			->setCellValue('J'.$r, $MONTO_TOTAL_TRANSFERENCIA)
			->setCellValue('K'.$r,  $TotalAPagar);	

$objPHPExcel->getActiveSheet()->getStyle('H'.$r.':K'.$r)->getNumberFormat()
							  ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);								  
/*$objPHPExcel->getActiveSheet()->getStyle('B'.$r.':K'.$r)->getBorders()->getBottom()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);*/


$objPHPExcel->getActiveSheet()->getStyle('D'.($r-3).':K'.$r)->applyFromArray(allBordersThin());    


 $r++;
 $r++;
 $r++;
 $r++;
					  
$objPHPExcel->getActiveSheet()->getStyle('B'.$r.':D'.$r)->getBorders()->getBottom()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

$objPHPExcel->getActiveSheet()->getStyle('G'.$r.':J'.$r)->getBorders()->getBottom()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
 $r++;   
 $objPHPExcel->getActiveSheet()			
			->setCellValue('B'.$r,'Elaborado por' )
			->setCellValue('G'.$r,'Aprobado por' );
$objPHPExcel->getActiveSheet()->mergeCells('B'.$r.':D'.$r);	
$objPHPExcel->getActiveSheet()->mergeCells('G'.$r.':J'.$r);	


$objPHPExcel->getActiveSheet()->getStyle('B'.$r.':D'.$r)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('G'.$r.':J'.$r)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

cellColor('A1:k'.$r, 'FFFFFF');
//===========================================================================
//$objPHPExcel->setActiveSheetIndex(0); 
$objPHPExcel->getActiveSheet()->setSelectedCells('I30');



$NOMINA = str_replace(' ', '', $NOMINA);
//LISTADO-PLANILLA_FORMATO_RESUMIDO
$filename = "Listado_Planilla_Formato_Resumido_Del_".fecha($desde).'_Hasta_'.fecha($hasta);

// Redirect output to a client’s web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="'.$filename.'.xls"');
header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header ('Pragma: public'); // HTTP/1.0

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
}
exit;
