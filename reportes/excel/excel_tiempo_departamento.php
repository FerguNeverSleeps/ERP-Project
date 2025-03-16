<?php
include("../../nomina/lib/common.php");
require_once('../../nomina/lib/database.php');
$db = new Database($_SESSION['bd']);
require_once '../../includes/phpexcel/Classes/PHPExcel.php';

$objPHPExcel = new PHPExcel();
$objPHPExcel = PHPExcel_IOFactory::load("plantillas/plantilla2.xlsx");

$sql = "SELECT Descripcion,IdDepartamento FROM departamento GROUP BY Descripcion";
$res = $db->query($sql);

$sql2 = "SELECT a.descripcion as Descripcion,count(B.IdDepartamento) as total FROM departamento as a,nompersonal as B WHERE a.IdDepartamento=b.IdDepartamento GROUP BY Descripcion";
$res2 = $db->query($sql2);

while($col=fetch_array($res2))
{
	$dep=$col['Descripcion'];
	$departamento[$dep]=$col['total'];
}

//titulo de la tabla
$objPHPExcel->getActiveSheet()->SetCellValue('B1', "Tiempos por Departamento");

//Encabezado de la tabla
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(60);
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
$objPHPExcel->getActiveSheet()->SetCellValue('B2', "Departamento");
$objPHPExcel->getActiveSheet()->SetCellValue('C2', "Total de Empleados");
//Fin encabezado tabla
$rowCount = 3;
while($row = fetch_array($res)){
	$dep=$row['Descripcion'];	
	$idd = $row['IdDepartamento'];
	if(isset($departamento[$dep])){$depp=$departamento[$dep];}else{$depp="0";}
	$objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, $row['Descripcion']);
	$objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, $depp);
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
header('Content-Disposition: attachment;filename="tiempo_departamento.xlsx"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');

 ?>