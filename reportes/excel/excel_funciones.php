<?php
include("../../nomina/lib/common.php");
require_once('../../nomina/lib/database.php');
$db = new Database($_SESSION['bd']);
require_once '../../includes/phpexcel/Classes/PHPExcel.php';

$objPHPExcel = new PHPExcel();
$objPHPExcel = PHPExcel_IOFactory::load("plantillas/lista_funciones.xlsx");
//------------------------------------------------------------
$sql2 = "SELECT * FROM nomfuncion";
$result2 = $db->query($sql2, $conexion);
$i=0;

//--------------------------------------------------------------
//titulo de la tabla
$objPHPExcel->getActiveSheet()->SetCellValue('B1', "Listado de Funciones");
$objPHPExcel->getActiveSheet()->SetCellValue('D1', "Usuario: ".$_SESSION['nombre']);
$objPHPExcel->getActiveSheet()->SetCellValue('F1', "Fecha: ".date('d-m-Y'));

//Encabezado de la tabla
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(80);

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
$objPHPExcel->getActiveSheet()->SetCellValue('B2', "Código");
$objPHPExcel->getActiveSheet()->SetCellValue('C2', "Función");

//Fin encabezado tabla
$rowCount = 3;
$count=0;

while($row = mysqli_fetch_array($result2))
{
    //----------------------------------------    
    $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, $row['nomfuncion_id']);
    $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, $row['descripcion_funcion']);
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
    $count++;
}

$final = $rowCount-1;
$objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount,'Total:');
$objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, '='.$final.')');
$objPHPExcel->getActiveSheet()->setTitle('Lista de Funciones');

header('Content-Type: application/vnd.openxmlformats-   officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="lista_funciones.xlsx"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
?>