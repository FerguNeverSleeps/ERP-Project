<?php
include("../../nomina/lib/common.php");
require_once('../../nomina/lib/database.php');
$db = new Database($_SESSION['bd']);
require_once '../../includes/phpexcel/Classes/PHPExcel.php';

$objPHPExcel = new PHPExcel();
$objPHPExcel = PHPExcel_IOFactory::load("plantillas/plantilla3.xlsx");

//------------------------------------------------
$sql = "SELECT DISTINCT b.Descripcion as descripcion, SUM(a.suesal) as saltotal,COUNT(a.personal_id) as todos FROM nompersonal AS a, departamento AS b WHERE a.IdDepartamento = b.IdDepartamento GROUP BY descripcion";
$result = $db->query($sql);
//---------------------------------------------------------------
//titulo de la tabla
$objPHPExcel->getActiveSheet()->SetCellValue('B1', "Resumen de Planilla Siclar");
$objPHPExcel->getActiveSheet()->SetCellValue('C1', "Usuario: ".$_SESSION['nombre']);
$objPHPExcel->getActiveSheet()->SetCellValue('D1', "Fecha: ".date('d-m-Y'));

//Encabezado de la tabla
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(50);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(50);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(50);

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
$objPHPExcel->getActiveSheet()->getStyle('B2:N2')->applyFromArray($borders);
$objPHPExcel->getActiveSheet()->SetCellValue('B2', "Descripcion");
$objPHPExcel->getActiveSheet()->SetCellValue('C2', "Total de Salarios");
$objPHPExcel->getActiveSheet()->SetCellValue('D2', "Colaboradores");

//Fin encabezado tabla
$rowCount = 3;
$count=0;
$departamento=NULL;

while($row = fetch_array($result)){
       
      $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, $row['descripcion']);
      $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, $row['saltotal']);
      $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, $row['todos']);
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
      $hasta = "D".$rowCount;
      $objPHPExcel->getActiveSheet()->getStyle($desde.':'.$hasta)->applyFromArray($borders);
      $rowCount++;
      $count++;
}

$final = $rowCount-1;
$objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount,'Total:');
$objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, '=CONTAR(D3:D'.$final.')');
$objPHPExcel->getActiveSheet()->setTitle('Planilla Siclar');

header('Content-Type: application/vnd.openxmlformats-   officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="resumen_planilla_siclar.xlsx"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');

 ?>