<?php
session_start();
error_reporting(0);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('America/Panama');

if (PHP_SAPI == 'cli')
	die('Este ejemplo solo se debe ejecutar desde un navegador Web');

include('../../lib/common.php');
//include('lib/php_excel.php');
require_once 'phpexcel/Classes/PHPExcel.php';

if(!isset($_GET['mes']) or !isset($_GET['anio'])){
	exit;
}

//function fecha_sql($fecha)
//{
//	$fecha_tmp = explode("/", $fecha);
//	return $fecha_tmp[2]."-".$fecha_tmp[1]."-".$fecha_tmp[0];
//}

function letra_mes($n){
	switch($n){
		case '1': case '01': return "ENERO";
		case '2': case '02': return "FEBRERO";
		case '3': case '03': return "MARZO";
		case '4': case '04': return "ABRIL";
		case '5': case '05': return "MAYO";
		case '6': case '06': return "JUNIO";
		case '7': case '07': return "JULIO";
		case '8': case '08': return "AGOSTO";
		case '9': case '09': return "SEPTIEMBRE";
		case '10': return "OCTUBRE";
		case '11': return "NOVIEMBRE";
		case '12': return "DICIEMBRE";
	}
	return ""; 
}


function conceptos_suman_restan($conceptos){	
	$tmp=explode(",", $conceptos);
	$suman=[];
	$restan=[];
	for($i=0; $i < count($tmp); $i++){ 
		$tmp[$i]=trim($tmp[$i]);
		if(substr($tmp[$i], 0,1)=="-")
			$restan[]=trim($tmp[$i],"-");
		else
			$suman[]=$tmp[$i];
	}
	return [
		"conceptos_suman"  => implode(",",$suman),
		"conceptos_restan" => implode(",",$restan)
	];
}


$anio = intval($_GET['anio']);
$mes  = intval($_GET['mes']);
$fecha_inicio   = fecha_sql($_GET['fecha_inicio']);
$fecha_fin      = fecha_sql($_GET['fecha_fin']);

//$anio=2020;
//$mes=9;

$conexion= new bd($_SESSION['bd']);

$sql = "SELECT e.nom_emp as nombre, e.rif, e.tel_emp as telefono, e.dir_emp as direccion, e.ciu_emp as ciudad, 
               e.edo_emp, e.imagen_izq as logo
		FROM   nomempresa e";
$res=$conexion->query($sql);
$empresa=$res->fetch_array();
$logo=$empresa['logo'];

//cargar configuracion de reporte
$sql1 = "select * from config_reportes_planilla where id = 8";
$res=$conexion->query($sql1);
$configReporte=$res->fetch_array();
$sql_reporte = $configReporte['sql_reporte'];


$sql_reporte=str_replace("{anio}", $anio, $sql_reporte);
$sql_reporte=str_replace("{mes}", $mes, $sql_reporte);
$sql_reporte=str_replace("{fecha_inicio}", $fecha_inicio, $sql_reporte);
$sql_reporte=str_replace("{fecha_fin}", $fecha_fin, $sql_reporte);

if(!isset($configReporte['id'])){
	print "config_reportes_planilla id=8 no encontrado";
	exit;
}

//cargar configuracion de reporte (columnas)
$sql1 = "select * from config_reportes_planilla_columnas where id_reporte = '".$configReporte['id']."' order by col_orden";
$res=$conexion->query($sql1);
$configReporteColumnas=[];
while($tmp=$res->fetch_array()){
	$configReporteColumnas[]=$tmp;
}

$ultima_columna=$configReporteColumnas[count($configReporteColumnas)-1]["col_letra"];



//print_r($configReporteColumnas);exit;


$objPHPExcel = new PHPExcel();
if($configReporte['plantilla_excel'] and file_exists($configReporte['plantilla_excel'])){
	$objPHPExcel = PHPExcel_IOFactory::load($configReporte['plantilla_excel']);
}
$objPHPExcel->getProperties()->setCreator("AMAXONIA")
							 ->setLastModifiedBy("AMAXONIA")
							 ->setTitle("Distribucion Otros Beneficios");

//$objPHPExcel->getDefaultStyle()->getFont()->setName('Calibri');
//$objPHPExcel->getDefaultStyle()->getFont()->setSize(11);

$sheet=$objPHPExcel->getActiveSheet();


if(!mb_detect_encoding($empresa['nombre'],["UTF-8"],true))
	$empresa['nombre']=utf8_encode($empresa['nombre']);

if(!mb_detect_encoding($configReporte['titulo1'],["UTF-8"],true))
	$configReporte['titulo1']=utf8_encode($configReporte['titulo1']);

$ln=2;
$sheet->setCellValue("A{$ln}", $configReporte['titulo1']."    ( ".letra_mes($mes)." ".$anio." )");
$sheet->mergeCells("A{$ln}:{$ultima_columna}{$ln}");
$sheet->getStyle("A{$ln}")->getFont()->setBold(true);
$sheet->getStyle("A{$ln}")->getFont()->setSize(18);
$sheet->getStyle("A{$ln}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$ln=3;
$sheet->setCellValue("A{$ln}", "#");
$sheet->setCellValue("B{$ln}", "SUCURSAL");

$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(50);
//agregar titulos de las columnas
for($i=0; $i<count($configReporteColumnas); $i++){ 
	if(!$configReporteColumnas[$i]["col_letra"])
		continue;
	$columna=$configReporteColumnas[$i]["col_letra"];

	if(!mb_detect_encoding($configReporteColumnas[$i]['nombre'],["UTF-8"],true))
		$configReporteColumnas[$i]['nombre']=utf8_encode($configReporteColumnas[$i]['nombre']);

	$objPHPExcel->getActiveSheet()->getColumnDimension($columna)->setWidth(12);
	$sheet->setCellValue("{$columna}{$ln}", $configReporteColumnas[$i]['nombre']);
	$sheet->getStyle("{$columna}{$ln}")->getAlignment()->setWrapText(true);
}
$sheet->getStyle("A".$ln.":{$ultima_columna}".$ln)->getFont()->setBold(true);
$sheet->getStyle("A".$ln.":{$ultima_columna}".$ln)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);


$sql = "
	SELECT
		nn.codorg,
		upper(nn.markar) markar,
		upper(nn.descripcion_completa) descripcion_completa,
		upper(nn.descrip) descrip
	FROM   nomnivel1 nn 
	WHERE  codorg!=1 AND nn.estatus!=0
	ORDER BY nn.markar";
$res=$conexion->query($sql);
$ln=4;
$ln_inicio=$ln;
$n=1;
while($centro_costo=$res->fetch_array()){	
	
	$sql_ejecutar=str_replace("{centro_costo}", $centro_costo["codorg"], $sql_reporte);
	//hacer los reemplazos de conceptos en sql_reporte del encabezado de reportes
	for($i=0; $i <count($configReporteColumnas[$i]); $i++){ 
		if($configReporteColumnas[$i]["conceptos"]){
			$tmp=conceptos_suman_restan($configReporteColumnas[$i]["conceptos"]);
			$conceptos_suman  = $tmp["conceptos_suman"];
			$conceptos_restan = $tmp["conceptos_restan"];
			if(!$conceptos_suman)  $conceptos_suman  ="-1";
			if(!$conceptos_restan) $conceptos_restan ="-1";

			$sql_ejecutar = str_replace("{".$configReporteColumnas[$i]["nombre_corto"]."}",        "$conceptos_suman",   $sql_ejecutar);
			$sql_ejecutar = str_replace("{".$configReporteColumnas[$i]["nombre_corto"]."_restan}", "$conceptos_restan", $sql_ejecutar);
		}
		else{
			$sql_ejecutar = str_replace("{".$configReporteColumnas[$i]["nombre_corto"]."}",        "-1",   $sql_ejecutar);
			$sql_ejecutar = str_replace("{".$configReporteColumnas[$i]["nombre_corto"]."_restan}", "-1", $sql_ejecutar);
		}
	}

	//print $sql_ejecutar;exit;
	$res2=$conexion->query($sql_ejecutar);
	$data=$res2->fetch_array();

	if(!mb_detect_encoding($centro_costo['descripcion_completa'],["UTF-8"],true))
		$centro_costo['descripcion_completa']=utf8_encode($centro_costo['descripcion_completa']);

	if(!mb_detect_encoding($centro_costo['descrip'],["UTF-8"],true))
		$centro_costo['descrip']=utf8_encode($centro_costo['descrip']);

	$sheet->setCellValue('A'.$ln, $n);$n++;
	$sheet->setCellValue('B'.$ln, $centro_costo['markar']." ".$centro_costo['descripcion_completa']." (".$centro_costo['descrip'].")");


	for($i=0; $i <count($configReporteColumnas[$i]); $i++){ 
		if(!$configReporteColumnas[$i]["col_letra"])
			continue;
		$columna=$configReporteColumnas[$i]["col_letra"];
		
		$valor=NULL;
		if($configReporteColumnas[$i]["formula_valor"]){
			$valor=$configReporteColumnas[$i]["formula_valor"];
			$valor=str_replace("{ln}", "$ln", $valor);
			$sheet->setCellValue("{$columna}{$ln}", "$valor");
		}
		else{
			$nombre_corto=$configReporteColumnas[$i]["nombre_corto"];
			if(isset($data[$nombre_corto])){
				$valor=$data[$nombre_corto];
				$sheet->setCellValue("{$columna}{$ln}", "$valor");
			}
		}

	}

	$ln++;
}


$sheet->setCellValue("B$ln","Total");
$sheet->getStyle("B$ln")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
//totalizacion
for($i=0; $i <count($configReporteColumnas[$i]); $i++){ 
	if(!$configReporteColumnas[$i]["col_letra"])
		continue;

	$sheet->setCellValue($configReporteColumnas[$i]["col_letra"].$ln,"=SUM(".$configReporteColumnas[$i]["col_letra"]."$ln_inicio:".$configReporteColumnas[$i]["col_letra"].($ln-1).")");
}

$sheet->getStyle("A".$ln.":{$ultima_columna}".$ln)->getFont()->setBold(true);


$sheet->getStyle("A".$ln_inicio.":B".($ln-1))->getFont()->setSize(12);
$sheet->getStyle("A".$ln_inicio.":B".($ln-1))->getFont()->setBold(true);

$sheet->getStyle("C".$ln_inicio.":{$ultima_columna}$ln")->getFont()->setSize(11);
$sheet->getStyle("C".$ln_inicio.":{$ultima_columna}$ln")->getNumberFormat()->setFormatCode('_-* #,##0.00_-;[RED]-* #,##0.00_-;_-* -_-;_-@_-');
$sheet->getStyle("C".$ln_inicio.":{$ultima_columna}$ln")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

$sheet->getStyle("{$ultima_columna}".$ln_inicio.":{$ultima_columna}".$ln)->getFont()->setBold(true);
$sheet->getStyle("A".$ln_inicio.":A$ln")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$border=array(
  'borders' => array(
    'allborders' => array(
      'style' => PHPExcel_Style_Border::BORDER_THIN,
      'color' => array('rgb' => '000000')
    )
  )
);

$sheet->getStyle("A".($ln_inicio-1).":{$ultima_columna}".$ln)->applyFromArray($border);


$filename = $configReporte['nombre_archivo']."_".$anio."_".$mes;

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

$writer = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$writer->setPreCalculateFormulas(true);

/* Limpiamos el búfer */
ob_clean();
$writer->save('php://output');
?>