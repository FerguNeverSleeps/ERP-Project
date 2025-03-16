<?php
include("../../nomina/lib/common.php");
require_once('../../nomina/lib/database.php');
$db = new Database($_SESSION['bd']);
require_once '../../includes/phpexcel/Classes/PHPExcel.php';
$codigo = $_GET["codigo"];
$objPHPExcel = new PHPExcel();
$objPHPExcel = PHPExcel_IOFactory::load("plantillas/plantilla2.xlsx");

$sql = "SELECT personal_id,apenom FROM nompersonal WHERE IdDepartamento='$codigo'";
$res = $db->query($sql);

//titulo de la tabla
$objPHPExcel->getActiveSheet()->SetCellValue('B1', "Tiempos por Departamento en detalle");

//Encabezado de la tabla
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(40);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(40);

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
        'color' => array('rgb' => 'FFFFFF'),
        'size'  => 12,
        'name'  => 'Verdana'
    )
    );
$objPHPExcel->getActiveSheet()->getStyle('B2:C2')->applyFromArray($borders);
$objPHPExcel->getActiveSheet()->SetCellValue('B2', "Posicion");
$objPHPExcel->getActiveSheet()->SetCellValue('C2', "Nombre del Empleado");
//Fin encabezado tabla
$rowCount = 3;
while($row = fetch_array($res)){
	$objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, $row['personal_id']);
	$objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, $row['apenom']);
	$borders = array(
      'borders' => array(
        'allborders' => array(
          'style' => PHPExcel_Style_Border::BORDER_THIN,
          'color' => array('argb' => 'FF000000'),
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
        )
      ),
    );
    $desde = "B".$rowCount;
    $hasta = "C".$rowCount;
	$objPHPExcel->getActiveSheet()->getStyle($desde.':'.$hasta)->applyFromArray($borders);
	$rowCount++;
}
$final = $rowCount-1;
$objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount,'Total:');
$objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, '=CONTAR(C3:C'.$final.')');
$objPHPExcel->getActiveSheet()->setTitle('Tiempo por Departamento');

header('Content-Type: application/vnd.openxmlformats-   officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="tiempo_departamento_detalles.xlsx"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');

 ?>