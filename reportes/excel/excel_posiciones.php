<?php
include("../../nomina/lib/common.php");
require_once('../../nomina/lib/database.php');
$db = new Database($_SESSION['bd']);
require_once '../../includes/phpexcel/Classes/PHPExcel.php';

$objPHPExcel = new PHPExcel();
$objPHPExcel = PHPExcel_IOFactory::load("plantillas/lista_posiciones.xlsx");
//------------------------------------------------------------
$sql2 = "SELECT * FROM nomposicion";
$result2 = $db->query($sql2, $conexion);
$i=0;

//--------------------------------------------------------------
//titulo de la tabla
$objPHPExcel->getActiveSheet()->SetCellValue('B1', "Listado de Posiciones");
$objPHPExcel->getActiveSheet()->SetCellValue('D1', "Usuario: ".$_SESSION['nombre']);
$objPHPExcel->getActiveSheet()->SetCellValue('F1', "Fecha: ".date('d-m-Y'));

//Encabezado de la tabla
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(25);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
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
$objPHPExcel->getActiveSheet()->getStyle('B2:G2')->applyFromArray($borders);
$objPHPExcel->getActiveSheet()->SetCellValue('B2', "Posicion");
$objPHPExcel->getActiveSheet()->SetCellValue('C2', "Sueldo Mensual");
$objPHPExcel->getActiveSheet()->SetCellValue('D2', "Sueldo Anual");
$objPHPExcel->getActiveSheet()->SetCellValue('E2', "Partida");
$objPHPExcel->getActiveSheet()->SetCellValue('F2', "Gastos Rep");
$objPHPExcel->getActiveSheet()->SetCellValue('G2', "Cargo");
//Fin encabezado tabla
$rowCount = 3;
$count=0;

while($row = mysqli_fetch_array($result2))
{
    //----------------------------------------    
    $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, $row['nomposicion_id']);
    $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, $row['sueldo_propuesto']);
    $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, $row['sueldo_anual']);
    $objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, $row['partida']);
    $objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, $row['gastos_representacion']);
    $objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowCount, $row['cargo_id']);
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
    $hasta = "G".$rowCount;
    $objPHPExcel->getActiveSheet()->getStyle($desde.':'.$hasta)->applyFromArray($borders);
    $rowCount++;
    $count++;
}

$final = $rowCount-1;
$objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount,'Total:');
$objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowCount, '=CONTAR(G2:G'.$final.')');
$objPHPExcel->getActiveSheet()->setTitle('Lista de Posiciones');

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="lista_posiciones.xls"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
?>