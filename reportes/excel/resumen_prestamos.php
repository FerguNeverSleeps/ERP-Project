<?php
include("../../nomina/lib/common.php");
require_once('../../nomina/lib/database.php');
$db = new Database($_SESSION['bd']);
require_once '../../includes/phpexcel/Classes/PHPExcel.php';

$objPHPExcel = new PHPExcel();
$objPHPExcel = PHPExcel_IOFactory::load("plantillas/resumen_prestamos.xlsx");
//------------------------------------------------------------
$sql2 = "SELECT npc.ficha, npc.numpre, npc.detalle, npc.mtocuota, np.apenom, np.cedula, np.ficha,npc.codigopr,p.descrip,npc.monto,npc.estadopre
FROM nomprestamos_cabecera npc  
left join nompersonal np on (npc.ficha= np.ficha)
left join nomprestamos p on  (npc.codigopr = p.codigopr)";
$result2 = $db->query($sql2, $conexion);
$i=0;

//--------------------------------------------------------------
//titulo de la tabla
$objPHPExcel->getActiveSheet()->SetCellValue('A1', "Listado de Préstamos y Acreedores");

//Encabezado de la tabla
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(40);

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
$objPHPExcel->getActiveSheet()->getStyle('A2:I2')->applyFromArray($borders);
$objPHPExcel->getActiveSheet()->SetCellValue('A2', "Cédula");
$objPHPExcel->getActiveSheet()->SetCellValue('B2', "Nombres");
$objPHPExcel->getActiveSheet()->SetCellValue('C2', "Ficha");
$objPHPExcel->getActiveSheet()->SetCellValue('D2', "Préstamo");
$objPHPExcel->getActiveSheet()->SetCellValue('E2', "Monto Préstamo");
$objPHPExcel->getActiveSheet()->SetCellValue('F2', "Cuota Préstamo");
$objPHPExcel->getActiveSheet()->SetCellValue('G2', "Acreedor");
$objPHPExcel->getActiveSheet()->SetCellValue('H2', "Nombre");
$objPHPExcel->getActiveSheet()->SetCellValue('I2', "Estado");

//Fin encabezado tabla
$rowCount = 3;
$count=0;

while($row = mysqli_fetch_array($result2))
{
    //----------------------------------------    
    $objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, $row['cedula']);
    $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, $row['apenom']);
    $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, $row['ficha']);
    $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, $row['numpre']);
    $objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, $row['monto']);
    $objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, $row['mtocuota']);
    $objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowCount, $row['codigopr']);
    $objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowCount, $row['descrip']);
    $objPHPExcel->getActiveSheet()->SetCellValue('I'.$rowCount, $row['estadopre']);
    $borders = array(
        'borders' => array(
            'allborders' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('argb' => 'FF0000'),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
            )
        ),
    );
    $desde = "A".$rowCount;
    $hasta = "I".$rowCount;
    $objPHPExcel->getActiveSheet()->getStyle($desde.':'.$hasta)->applyFromArray($borders);
    $rowCount++;
    $count++;
}

$final = $rowCount-1;
$objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount,'Total:');
$objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount,'=CONTAR(C2:C'.$final.')');
$objPHPExcel->getActiveSheet()->setTitle('Lista de Prestamos');

header('Content-Type: application/vnd.openxmlformats-   officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="resumen_prestamos.xlsx"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
?>
