<?php
session_start();
error_reporting(0);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('America/Panama');

if (PHP_SAPI == 'cli')
	die('Este ejemplo solo se debe ejecutar desde un navegador Web');

include('../../lib/common.php');
include('../lib/php_excel.php');


if(isset($_GET['anio']) && isset($_GET['mes']))
{
	$anio=$_GET['anio'];
	$mes=$_GET['mes'];

	$conexion= new bd($_SESSION['bd']);

	require_once '../phpexcel/Classes/PHPExcel.php';

	$objPHPExcel = new PHPExcel();
	$objPHPExcel = PHPExcel_IOFactory::load("../plantillas/detalle_preelabora_tmp.xlsx");

	$objPHPExcel->getProperties()->setCreator("Amaxonia ERP")
								->setLastModifiedBy("Amaxonia ERP")
								->setTitle("Planilla Preelaborada");

	$objPHPExcel->getDefaultStyle()->getFont()->setName('Calibri');
	$objPHPExcel->getDefaultStyle()->getFont()->setSize(11);

	$sql = "SELECT e.nom_emp as empresa, e.rif, e.tel_emp as telefono, e.dir_emp as direccion, e.ciu_emp as ciudad, 
				e.edo_emp, e.imagen_izq as logo,e.cedula_juridica,e.numero_patronal,e.pre_sid
			FROM   nomempresa e";
	$res=$conexion->query($sql);
	$fila=$res->fetch_array();
	$logo=$fila['logo'];

	$meses = array('Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre');

	$mes_letras = $meses[$mes - 1];

	$objPHPExcel->getActiveSheet()
				->setCellValue('J3', "CAJA DE SEGURO SOCIAL DE PANAMA")
				->setCellValue('J5', "PLANILLA MENSUAL DE CUOTAS, APORTES E IMPUESTO SOBRE LA RENTA")
				->setCellValue('J7', strtoupper("CORRESPONDIENTE AL MES DE ".$mes_letras." DE ".$anio ) )
				->setCellValue('D10', strtoupper($fila['empresa'] ) )
				->setCellValue('D11', strtoupper($fila['direccion'] ) )
				->setCellValue('D12', strtoupper($fila['cedula_juridica'] ) )
				->setCellValue('D14', strtoupper($fila['pre_sid'] ) )
				->setCellValue('D16', strtoupper("") )
				->setCellValue('I11', strtoupper($fila['rif'] ) )
				->setCellValue('N10', strtoupper($fila['empresa'] ) )
				->setCellValue('N11', strtoupper("" ) )
				->setCellValue('N14', strtoupper($fila['telefono'] ) )
				->setCellValue('N16', strtoupper($fila['numero_patronal'] ) );

	//$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':'.$col.$i)->applyFromArray(allBordersMedium());
	//$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':'.$col.$i)->getFont()->setSize(12);
	//$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':'.$col.$i)->getFont()->setBold(true);

	$sql = "SELECT a.*,b.sexo,b.ficha FROM ach_sipe_detalle a LEFT JOIN nompersonal b ON a.numero_documento=b.cedula 
		WHERE id in ( select id from ach_sipe where anio='".$anio."' and mes='".$mes."')";
	$resp=$conexion->query($sql);

	//SUMA De TOTALES DE SEGUROS SOCIAL EMPLEADO
	$sql1 = "SELECT SUM(monto) s_social_emp,codcon,descrip 
	FROM nom_movimientos_nomina 
	WHERE anio='".$anio."' and mes='".$mes."' AND codcon IN (200) 
	GROUP BY codcon";
	//echo $sql1."<br>";
	$res=$conexion->query($sql1);
	$re_ss1=$res->fetch_array();
	$total_s_social_emp = $re_ss1['s_social_emp'];

	//SUMA De TOTALES DE SEGUROS SOCIAL PATRONAL
	$sql2 = "SELECT SUM(monto) s_social_pat,codcon,descrip 
	FROM nom_movimientos_nomina 
	WHERE anio='".$anio."' and mes='".$mes."' AND codcon IN (3000) 
	GROUP BY codcon";
	//echo $sql2."<br>";
	$res=$conexion->query($sql2);
	$re_ss2=$res->fetch_array();
	$total_s_social_pat = $re_ss2['s_social_pat'];

	//SUMA De TOTALES DE SEGUROS EDUCATIVO EMPLEADO
	$sql3 = "SELECT SUM(monto) s_educativo_emp,codcon,descrip 
	FROM nom_movimientos_nomina 
	WHERE anio='".$anio."' and mes='".$mes."' AND codcon IN (201) 
	GROUP BY codcon";
	//echo $sql3."<br>";
	$res=$conexion->query($sql3);
	$re_se1=$res->fetch_array();
	$total_s_educativo_emp = $re_se1['s_educativo_emp'];

	//SUMA De TOTALES DE SEGUROS EDUCATIVO PATRONAL
	$sql4 = "SELECT SUM(monto) s_educativo_pat,codcon,descrip 
	FROM nom_movimientos_nomina 
	WHERE anio='".$anio."' and mes='".$mes."' AND codcon IN (3001) 
	GROUP BY codcon";
	//echo $sql4."<br>";
	$res=$conexion->query($sql4);
	$re_ss2=$res->fetch_array();
	$total_s_educativo_pat = $re_se2['s_educativo_pat'];

	//SUMA De TOTALES DE SEGUROS DECIMO EMPLEADO
	$sql5 = "SELECT SUM(monto) s_decimo_emp,codcon,descrip 
	FROM nom_movimientos_nomina 
	WHERE anio='".$anio."' and mes='".$mes."' AND codcon IN (205) 
	GROUP BY codcon";
	//echo $sql5."<br>";
	$res=$conexion->query($sql5);
	$re_sd1=$res->fetch_array();
	$total_s_decimo_emp = $re_sd1['s_decimo_emp'];

	//SUMA De TOTALES DE SEGUROS DECIMO PATRONAL
	$sql6 = "SELECT SUM(monto) s_decimo_pat,codcon,descrip 
	FROM nom_movimientos_nomina 
	WHERE anio='".$anio."' and mes='".$mes."' AND codcon IN (3002) 
	GROUP BY codcon";
	//echo $sql6."<br>";
	$res=$conexion->query($sql6);
	$re_sd2=$res->fetch_array();
	$total_s_decimo_pat = $re_sd2['s_decimo_pat'];

	//SUMA De TOTALES DE IMPUESTO
	$sql6 = "SELECT SUM(monto) total_impuesto,codcon,descrip 
	FROM nom_movimientos_nomina 
	WHERE anio='".$anio."' and mes='".$mes."' AND codcon IN (202) 
	GROUP BY codcon";
	//echo $sql6."<br>";
	$res=$conexion->query($sql6);
	$impuesto=$res->fetch_array();
	$total_impuesto = $impuesto['total_impuesto'];

	//SUMA De TOTALES DE RIESGO PROFESIONAL
	$sql6 = "SELECT SUM(monto) total_riesgo,codcon,descrip 
	FROM nom_movimientos_nomina 
	WHERE anio='".$anio."' and mes='".$mes."' AND codcon IN (9009) 
	GROUP BY codcon";
	//echo $sql6."<br>";
	$res=$conexion->query($sql6);
	$riesgo=$res->fetch_array();
	$total_riesgo = $riesgo['total_riesgo'];

	$i=21;
	$CONEMP=$CONVAC=$CONLIC=$CONLIQ=$SALARIOS=$DECIMO=$IMPUESTO=$INDMENIZACION=0;
	while($sipe=$resp->fetch_array()){ 

		switch ($sipe['sexo']) {
			case 'Masculino':
				$sexo = 'M';
				break;
			case 'Femenino':
				$sexo = 'F';
				break;	
			default:
				$sexo = '';
				break;
		} 

		$obs = "";
		if($sipe['sueldo']==0.00){
			$obs = "NO LABORÓ";
		}elseif($sipe['vacaciones']==0.00){
			$CONEMP++;
		}
		if($sipe['vacaciones']!=0.00){
			$obs = "VACACIÓN";
			$CONVAC++;

		}
		if($sipe['indmenizacion']!=0.00){
			$obs = "VACACIÓN";
			$CONLIQ++;

		}
		
		$SALARIOS=$SALARIOS+$sipe['sueldo'];
		$DECIMO=$DECIMO+$sipe['decimo_tercer_mes'];
		$IMPUESTO=$IMPUESTO+$sipe['impuesto_renta'];
		$INDMENIZACION=$INDMENIZACION+$sipe['indmenizacion'];

		$objPHPExcel->getActiveSheet()->setCellValue('B'.$i , $sipe['seguro_social'] );
		$objPHPExcel->getActiveSheet()->setCellValue('C'.$i , $sipe['numero_documento'] );
		$objPHPExcel->getActiveSheet()->setCellValue('E'.$i , $sipe['apellido'] );
		$objPHPExcel->getActiveSheet()->setCellValue('F'.$i , $sipe['nombre'] );
		$objPHPExcel->getActiveSheet()->setCellValue('H'.$i , $sexo );
		$objPHPExcel->getActiveSheet()->setCellValue('I'.$i , "" );
		$objPHPExcel->getActiveSheet()->setCellValue('J'.$i , "" );
		$objPHPExcel->getActiveSheet()->setCellValue('K'.$i , $sipe['ficha'] );
		$objPHPExcel->getActiveSheet()->setCellValue('L'.$i , ( ( $sipe['sueldo']==0.00 ) ?'X':0 ) );
		$objPHPExcel->getActiveSheet()->setCellValue('M'.$i , $sipe['sueldo'] );
		$objPHPExcel->getActiveSheet()->setCellValue('N'.$i , $sipe['decimo_tercer_mes'] );
		$objPHPExcel->getActiveSheet()->setCellValue('O'.$i , $sipe['impuesto_renta'] );
		$objPHPExcel->getActiveSheet()->setCellValue('P'.$i , "0.00" );
		$objPHPExcel->getActiveSheet()->setCellValue('Q'.$i , "0.00" );
		$objPHPExcel->getActiveSheet()->setCellValue('R'.$i , "" );
		$objPHPExcel->getActiveSheet()->setCellValue('S'.$i , "" );
		$objPHPExcel->getActiveSheet()->setCellValue('T'.$i , $obs );
		
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':T'.$i)->getFont()->setName('Courier New');
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':T'.$i)->getFont()->setSize(10);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':T'.$i)->getFont()->setBold(false);
		$i++;
	}
	$i++;
	$objPHPExcel->getActiveSheet()->setCellValue('F'.$i , " ---SALARIOS---  ELIMINA   --I/RENTA--   EXCEPCI   ---D.T. MES---   TOT. X" );
	$objPHPExcel->getActiveSheet()->mergeCells('F'.$i.':M'.$i);
	$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':T'.$i)->getFont()->setName('Courier New');
	$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':T'.$i)->getFont()->setSize(10);
	$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':T'.$i)->getFont()->setBold(false);
	
	$objPHPExcel->getActiveSheet()->mergeCells('B'.$i.':D'.$i);
	$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':D'.$i)->getBorders()->getBottom()
	        		->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
	$i++;
	$objPHPExcel->getActiveSheet()->setCellValue('B'.$i , "SELLO Y FIRMA AUTORIZADA" );
	$objPHPExcel->getActiveSheet()->mergeCells('B'.$i.':D'.$i);
	$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':D'.$i)->getFont()->setName('Courier New');
	$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':D'.$i)->getFont()->setSize(10);
	$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':D'.$i)->getFont()->setBold(false);
	
	$objPHPExcel->getActiveSheet()->setCellValue('F'.$i , " ---".$SALARIOS."---              --".$DECIMO."--   ".$IMPUESTO." ---        ---         " );
	$objPHPExcel->getActiveSheet()->mergeCells('F'.$i.':M'.$i);
	$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':T'.$i)->getFont()->setName('Courier New');
	$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':T'.$i)->getFont()->setSize(10);
	$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':T'.$i)->getFont()->setBold(false);

	$i++;$i++;$i++;

	$objPHPExcel->getActiveSheet()->setCellValue('A'.$i , "RESUMEN" );
	$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getFont()->setSize(12);
	$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getFont()->setBold(true);


	$sqllic = "SELECT COUNT(tipo) CONLIC FROM expediente WHERE tipo IN (15,16,17)
	AND (MONTH(fecha_inicio) <= ".$mes." AND MONTH(fecha_fin) >= ".$mes.") 
	AND (YEAR(fecha_inicio) <= ".$anio." AND YEAR(fecha_fin) >= ".$anio.") AND estatus = 1 GROUP BY cedula";
	$reslic=$conexion->query($sqllic);
	$lic=$reslic->fetch_array();

	$i++;
	$objPHPExcel->getActiveSheet()->setCellValue('A'.$i , "Total de empleados activos:" );
	$objPHPExcel->getActiveSheet()->setCellValue('D'.$i , $CONEMP );
	$objPHPExcel->getActiveSheet()->setCellValue('E'.$i , "De vacaciones:" );
	$objPHPExcel->getActiveSheet()->setCellValue('F'.$i , $CONVAC );
	$objPHPExcel->getActiveSheet()->setCellValue('G'.$i , "De Licencia:" );
	$objPHPExcel->getActiveSheet()->setCellValue('H'.$i , $lic['CONLIC'] );
	$objPHPExcel->getActiveSheet()->setCellValue('I'.$i , "Liquidados:" );
	$objPHPExcel->getActiveSheet()->setCellValue('J'.$i , $CONLIQ );
	$objPHPExcel->getActiveSheet()->setCellValue('K'.$i , "Total:" );
	$objPHPExcel->getActiveSheet()->setCellValue('L'.$i , $TOTAL );
	$objPHPExcel->getActiveSheet()->setCellValue('M'.$i , "Ajuste a 100:" );
	$objPHPExcel->getActiveSheet()->setCellValue('N'.$i , "" );
	$objPHPExcel->getActiveSheet()->setCellValue('O'.$i , "Ajuste a 175:" );
	$objPHPExcel->getActiveSheet()->setCellValue('P'.$i , "" );
		
	$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':T'.$i)->getFont()->setName('Courier New');
	$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':T'.$i)->getFont()->setSize(10);
	$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':T'.$i)->getFont()->setBold(true);

	$i++;$i++;
	$objPHPExcel->getActiveSheet()->setCellValue('A'.$i , "Total de" );
	$objPHPExcel->getActiveSheet()->setCellValue('C'.$i , "Total de" );
	$objPHPExcel->getActiveSheet()->setCellValue('D'.$i , "Total de" );
	$objPHPExcel->getActiveSheet()->setCellValue('E'.$i , "Total de" );
	$objPHPExcel->getActiveSheet()->setCellValue('G'.$i , "Seg.Soc.Reg." );
	$objPHPExcel->getActiveSheet()->setCellValue('I'.$i , "Seg.Soc.XIII" );
	$objPHPExcel->getActiveSheet()->setCellValue('J'.$i , "Seg.Soc." );
	$objPHPExcel->getActiveSheet()->setCellValue('L'.$i , "Seguro Educativo" );
	$objPHPExcel->getActiveSheet()->setCellValue('O'.$i , "Riesgo" );
	$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':O'.$i)->getFont()->setName('Courier New');
	$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':O'.$i)->getFont()->setSize(9);
	$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':O'.$i)->getFont()->setBold(true);

	$i++;
	$objPHPExcel->getActiveSheet()->setCellValue('A'.$i , "Registro" );
	$objPHPExcel->getActiveSheet()->setCellValue('C'.$i , "Ingresos" );
	$objPHPExcel->getActiveSheet()->setCellValue('D'.$i , "XIII Mes" );
	$objPHPExcel->getActiveSheet()->setCellValue('E'.$i , "Otros" );
	$objPHPExcel->getActiveSheet()->setCellValue('F'.$i , "Total" );
	$objPHPExcel->getActiveSheet()->setCellValue('G'.$i , "Laboral" );
	$objPHPExcel->getActiveSheet()->setCellValue('H'.$i , "Emp." );
	$objPHPExcel->getActiveSheet()->setCellValue('I'.$i , "Laboral" );
	$objPHPExcel->getActiveSheet()->setCellValue('J'.$i , "Emp." );
	$objPHPExcel->getActiveSheet()->setCellValue('K'.$i , "Total" );
	$objPHPExcel->getActiveSheet()->setCellValue('L'.$i , "Laboral" );
	$objPHPExcel->getActiveSheet()->setCellValue('M'.$i , "Empleado" );
	$objPHPExcel->getActiveSheet()->setCellValue('N'.$i , "Total" );
	$objPHPExcel->getActiveSheet()->setCellValue('O'.$i , "I.S.R." );
	$objPHPExcel->getActiveSheet()->setCellValue('P'.$i , "Prof." );
	$objPHPExcel->getActiveSheet()->setCellValue('Q'.$i , "Total" );
	$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':Q'.$i)->getFont()->setName('Courier New');
	$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':Q'.$i)->getFont()->setSize(9);
	$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':Q'.$i)->getFont()->setBold(true);
	$i++;
	$i++;

	$objPHPExcel->getActiveSheet()->setCellValue('A'.$i , "" );
	$objPHPExcel->getActiveSheet()->setCellValue('C'.$i , $SALARIOS);
	$objPHPExcel->getActiveSheet()->setCellValue('D'.$i , $DECIMO);
	$objPHPExcel->getActiveSheet()->setCellValue('E'.$i , $IMPUESTO);
	$objPHPExcel->getActiveSheet()->setCellValue('F'.$i , ($SALARIOS+$DECIMO+$IMPUESTO) );
	$objPHPExcel->getActiveSheet()->setCellValue('G'.$i , $total_s_social_pat);
	$objPHPExcel->getActiveSheet()->setCellValue('H'.$i , $total_s_social_emp);
	$objPHPExcel->getActiveSheet()->setCellValue('I'.$i , $total_s_decimo_pat);
	$objPHPExcel->getActiveSheet()->setCellValue('J'.$i , $total_s_decimo_emp);
	$objPHPExcel->getActiveSheet()->setCellValue('K'.$i , ($total_s_social_pat+$total_s_social_emp+$total_s_decimo_pat+total_s_decimo_emp));
	$objPHPExcel->getActiveSheet()->setCellValue('L'.$i , $total_s_educativo_pat);
	$objPHPExcel->getActiveSheet()->setCellValue('M'.$i , $total_s_educativo_emp);
	$objPHPExcel->getActiveSheet()->setCellValue('N'.$i , ($total_s_educativo_pat+$total_s_educativo_emp));
	$objPHPExcel->getActiveSheet()->setCellValue('O'.$i , $total_impuesto);
	$objPHPExcel->getActiveSheet()->setCellValue('P'.$i , $total_riesgo);
	$objPHPExcel->getActiveSheet()->setCellValue('Q'.$i , ($total_impuesto+$total_riesgo));
	$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':Q'.$i)->getFont()->setName('Courier New');
	$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':Q'.$i)->getFont()->setSize(9);
	$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':Q'.$i)->getFont()->setBold(true);
	$i++;

	$objPHPExcel->getActiveSheet()->setCellValue('A'.$i , "Totales:" );
	$objPHPExcel->getActiveSheet()->setCellValue('C'.$i , $SALARIOS);
	$objPHPExcel->getActiveSheet()->setCellValue('D'.$i , $DECIMO);
	$objPHPExcel->getActiveSheet()->setCellValue('E'.$i , $IMPUESTO);
	$objPHPExcel->getActiveSheet()->setCellValue('F'.$i , ($SALARIOS+$DECIMO+$IMPUESTO) );
	$objPHPExcel->getActiveSheet()->setCellValue('G'.$i , $total_s_social_pat);
	$objPHPExcel->getActiveSheet()->setCellValue('H'.$i , $total_s_social_emp);
	$objPHPExcel->getActiveSheet()->setCellValue('I'.$i , $total_s_decimo_pat);
	$objPHPExcel->getActiveSheet()->setCellValue('J'.$i , $total_s_decimo_emp);
	$objPHPExcel->getActiveSheet()->setCellValue('K'.$i , ($total_s_social_pat+$total_s_social_emp+$total_s_decimo_pat+total_s_decimo_emp));
	$objPHPExcel->getActiveSheet()->setCellValue('L'.$i , $total_s_educativo_pat);
	$objPHPExcel->getActiveSheet()->setCellValue('M'.$i , $total_s_educativo_emp);
	$objPHPExcel->getActiveSheet()->setCellValue('N'.$i , ($total_s_educativo_pat+$total_s_educativo_emp));
	$objPHPExcel->getActiveSheet()->setCellValue('O'.$i , $total_impuesto);
	$objPHPExcel->getActiveSheet()->setCellValue('P'.$i , $total_riesgo);
	$objPHPExcel->getActiveSheet()->setCellValue('Q'.$i , ($total_impuesto+$total_riesgo));
	$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':Q'.$i)->getFont()->setName('Courier New');
	$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':Q'.$i)->getFont()->setSize(9);
	$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':Q'.$i)->getFont()->setBold(true);
	//===========================================================================
	$objPHPExcel->setActiveSheetIndex(0); 
	$objPHPExcel->getActiveSheet()->setSelectedCells('I30');

	$filename = "preelaborada".$anio;

	// Redirect output to a client’s web browser (Excel5)
	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"');
	header('Cache-Control: max-age=0');
	// If you're serving to IE 9, then the following may be needed
	header('Cache-Control: max-age=1');

	// If you're serving to IE over SSL, then the following may be needed
	header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
	header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
	header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
	header ('Pragma: public'); // HTTP/1.0

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	/* Limpiamos el búfer */
	ob_end_clean();
	$objWriter->save('php://output');
}
exit;
