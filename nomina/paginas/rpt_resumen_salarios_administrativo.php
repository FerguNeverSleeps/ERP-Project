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


$anio           = intval($_GET['anio']);
$mes            = intval($_GET['mes']);
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
$sql1 = "select * from config_reportes_planilla where id = 7";
$res=$conexion->query($sql1);
$configReporte=$res->fetch_array();
$sql_reporte = $configReporte['sql_reporte'];


$sql_reporte=str_replace("{anio}", $anio, $sql_reporte);
$sql_reporte=str_replace("{mes}", $mes, $sql_reporte);
$sql_reporte=str_replace("{fecha_inicio}", $fecha_inicio, $sql_reporte);
$sql_reporte=str_replace("{fecha_fin}", $fecha_fin, $sql_reporte);

if(!isset($configReporte['id'])){
	print "config_reportes_planilla id=7 no encontrado";
	exit;
}

//cargar configuracion de reporte (columnas)
$sql1 = "select * from config_reportes_planilla_columnas where id_reporte = '".$configReporte['id']."'";
$res=$conexion->query($sql1);
$configReporteColumnas=[];
while($tmp=$res->fetch_array()){
	$configReporteColumnas[]=$tmp;
}

//print_r($configReporteColumnas);exit;


$objPHPExcel = new PHPExcel();
//$objPHPExcel = PHPExcel_IOFactory::load("plantillas/resumen_salario_centro_costos.xlsx");
if($configReporte['plantilla_excel'] and file_exists($configReporte['plantilla_excel'])){
	$objPHPExcel = PHPExcel_IOFactory::load($configReporte['plantilla_excel']);
}
$objPHPExcel->getProperties()->setCreator("AMAXONIA")
							 ->setLastModifiedBy("AMAXONIA")
							 ->setTitle("Resumen de Salarios Personal Administrativo");

//$objPHPExcel->getDefaultStyle()->getFont()->setName('Calibri');
//$objPHPExcel->getDefaultStyle()->getFont()->setSize(11);

$sheet=$objPHPExcel->getActiveSheet();


if(!mb_detect_encoding($empresa['nombre'],["UTF-8"],true))
	$empresa['nombre']=utf8_encode($empresa['nombre']);

$sheet->setCellValue("A1", "RESUMEN DE SALARIOS PERSONAL ADMINISTRATIVO    ( ".letra_mes($mes)." ".$anio." )");
$sheet->setCellValue("A4", $empresa["nombre"]." / ".letra_mes($mes)." ".$anio);



$ln=6;

	
$sql_ejecutar=str_replace("{centro_costo}", "1", $sql_reporte);

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



$sql = "
	SELECT
		nn.codorg,
		nn.porcentaje,
		upper(nn.markar) markar,
		upper(nn.descripcion_completa) descripcion_completa,
		upper(nn.descrip) descrip
	FROM   nomnivel1 nn
	WHERE  codorg!=1 AND nn.estatus!=0
	ORDER BY nn.markar";
$res=$conexion->query($sql);

$ln=11;
$ln_inicio=$ln;
while($centro_costo=$res->fetch_array()){	

	if(!mb_detect_encoding($centro_costo['descrip'],["UTF-8"],true))
		$centro_costo['descrip']=utf8_encode($centro_costo['descrip']);

	$sheet->setCellValue('A'.$ln, $centro_costo['markar']." ".$centro_costo['descrip']."");
	$sheet->setCellValue('C'.$ln, $centro_costo["porcentaje"]*1);
	$sheet->setCellValue('D'.$ln, "=R6*C{$ln}%");

	$ln++;
}
$ln++;

$sheet->setCellValue("C".$ln,"=SUM(C{$ln_inicio}:C".($ln-1).")");
$sheet->setCellValue("D".$ln,"=SUM(D{$ln_inicio}:D".($ln-1).")");

$sheet->getStyle("A".$ln_inicio.":C".($ln-1))->getFont()->setSize(12);
$sheet->getStyle("D".$ln_inicio.":D".($ln-1))->getFont()->setSize(14);
$sheet->getStyle("C".$ln_inicio.":D$ln")->getNumberFormat()->setFormatCode('_-* #,##0.00_-;[RED]-* #,##0.00_-;_-* -_-;_-@_-');
$sheet->getStyle("C".$ln_inicio.":D$ln")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

$sheet->getStyle("C".$ln.":D".$ln)->getFont()->setBold(true);
$sheet->getStyle("C".$ln.":D".$ln)->getFont()->setSize(14);

$border=array(
  'borders' => array(
    'allborders' => array(
      'style' => PHPExcel_Style_Border::BORDER_THIN,
      'color' => array('rgb' => '000000')
    )
  )
);

$sheet->getStyle("A".$ln_inicio.":D".($ln-2))->applyFromArray($border);
$sheet->getStyle("C".($ln-1).":D".$ln)->applyFromArray($border);


/*


$sheet->setCellValue("A$ln","Total");
$sheet->getStyle("A$ln")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
//totalizacion
for($i=0; $i <count($configReporteColumnas[$i]); $i++){ 
	if(!$configReporteColumnas[$i]["col_letra"])
		continue;

	$sheet->setCellValue($configReporteColumnas[$i]["col_letra"].$ln,"=SUM(".$configReporteColumnas[$i]["col_letra"]."$ln_inicio:".$configReporteColumnas[$i]["col_letra"].($ln-1).")");
}

$sheet->getStyle("A".$ln.":P".$ln)->getFont()->setBold(true);


$sheet->getStyle("A".$ln_inicio.":A".($ln-1))->getFont()->setSize(12);
$sheet->getStyle("A".$ln_inicio.":A".($ln-1))->getFont()->setBold(true);

$sheet->getStyle("A".$ln_inicio.":P$ln")->getFont()->setSize(11);
//$sheet->getStyle("B".$ln_inicio.":P$ln")->getNumberFormat()->setFormatCode('#,##0.00');
$sheet->getStyle("B".$ln_inicio.":P$ln")->getNumberFormat()->setFormatCode('_-* #,##0.00_-;[RED]-* #,##0.00_-;_-* -_-;_-@_-');
$sheet->getStyle("B".$ln_inicio.":P$ln")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);



*/
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
ob_end_clean();
$writer->save('php://output');
?>