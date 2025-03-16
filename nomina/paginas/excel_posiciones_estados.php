<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering

require_once('../lib/database.php');
require_once '../../lib/monto_escrito.php';
require_once 'phpexcel/Classes/PHPExcel.php';

$db = new Database($_SESSION['bd']);

$estado = (empty($_GET['estado'])) ? 0 : $_GET['estado'];
$estados = (empty($_GET['estados'])) ? 0 : $_GET['estados'];
/*$fecha2 = (empty($_GET['fecha2'])) ? 0 : $_GET['fecha2'];

$inicio = date("Y-m-d",strtotime($fecha));
$final = date("Y-m-d",strtotime($fecha2));*/


$objPHPExcel = new PHPExcel();
$objPHPExcel = PHPExcel_IOFactory::load("../../reportes/excel/plantillas/excel_posiciones_estados.xlsx");
//------------------------------------------------------------

$consulta = "SELECT * FROM `nomposicion` AS A LEFT JOIN `nomcargos` AS B ON A.`cargo_id` = B.`cod_car` WHERE A.`estado` = '$estado' ORDER BY A.`nomposicion_id` ASC";
$resultado = $db->query($consulta);

// Actualizamos campo dia_semana en vig_asistencia_control
//--------------------------------------------------------------


//titulo de la tabla
$objPHPExcel->getActiveSheet()->SetCellValue('C2', "Posiciones: ".$estados);
$objPHPExcel->getActiveSheet()->SetCellValue('F2', "Fecha: ".date('d-m-Y'));

//Encabezado de la tabla
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(35);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(30);


$borders = array(
    'borders' => array(
        'allborders' => array(
          'style' => PHPExcel_Style_Border::BORDER_THIN,
          'color' => array('argb' => 'FF000000'),
    'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
    )
    ),
    'font'  => array(
        'bold'  => true,
        'color' => array('rgb' => '000000'),
        'size'  => 12,
        'name'  => 'Verdana',
        'center' => true
    )
);
$objPHPExcel->getActiveSheet()->getStyle('A4:F4')->applyFromArray($borders);
$objPHPExcel->getActiveSheet()->SetCellValue('A4', "Posición");
$objPHPExcel->getActiveSheet()->SetCellValue('B4', "Sueldo propuesto");
$objPHPExcel->getActiveSheet()->SetCellValue('C4', "Sueldo anual");
$objPHPExcel->getActiveSheet()->SetCellValue('D4', "Partida");
$objPHPExcel->getActiveSheet()->SetCellValue('E4', "Gastos de representación");
$objPHPExcel->getActiveSheet()->SetCellValue('F4', "Cargo");


//Fin encabezado tabla
$rowCount = 5;
//$suma = 0;
while($row = $resultado->fetch_assoc())
{
	$nomposicion_id = $row["nomposicion_id"];
	$sueldo_propuesto = $row["sueldo_propuesto"];
	$sueldo_anual = $row["sueldo_anual"];
	$partida = $row["partida"];
	$gastos_representacion = $row["gastos_representacion"];
	$cargo_id = $row["cargo_id"];

	$cod_car = $row["cod_car"];
	$des_car = $row["des_car"];


	$objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, $nomposicion_id);
	$objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, $sueldo_propuesto);
	$objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, $sueldo_anual);
	$objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, $partida);
	$objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, $gastos_representacion);
	$objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, $des_car);
	$rowCount++;
}
//$final = $rowCount;
//$objPHPExcel->getActiveSheet()->SetCellValue('L'.$final, 'Total: '.$moneda.' '.$suma);
$objPHPExcel->getActiveSheet()->setTitle('Listado de posiciones');

header('Content-Type: application/vnd.openxmlformats-   officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="excel_posiciones_estados.xlsx"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
?>