<?php
include("../../nomina/lib/common.php");
require_once('../../nomina/lib/database.php');
$db = new Database($_SESSION['bd']);
require_once '../../includes/phpexcel/Classes/PHPExcel.php';

$objPHPExcel = new PHPExcel();
$objPHPExcel = PHPExcel_IOFactory::load("plantillas/plantilla4.xlsx");
//------------------------------------------------------------

$sqlvac = "SELECT A.estado,A.ficha,A.cedula,A.apenom,A.fecing,B.fechavac,B.fechareivac,A.tipo_empleado "
        . "from nompersonal as A "
        . "LEFT JOIN nom_progvacaciones as B ON (B.ficha=A.ficha)"
        . "WHERE A.estado = 'Vacaciones'
        GROUP BY A.ficha";        
$result2 = $db->query($sqlvac, $conexion);
$i=0;

//--------------------------------------------------------------
//titulo de la tabla
$objPHPExcel->getActiveSheet()->SetCellValue('B1', "LISTADO DE COLABORADORES EN VACACIONES");
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
$objPHPExcel->getActiveSheet()->getStyle('B2:F2')->applyFromArray($borders);
$objPHPExcel->getActiveSheet()->SetCellValue('B2', "No.");
$objPHPExcel->getActiveSheet()->SetCellValue('C2', "Cédula");
$objPHPExcel->getActiveSheet()->SetCellValue('D2', "Nombre Colaborador");
$objPHPExcel->getActiveSheet()->SetCellValue('E2', "I. Labores");
$objPHPExcel->getActiveSheet()->SetCellValue('F2', "F. Vacaciones");
//Fin encabezado tabla
$rowCount = 3;
$count=0;

while($row = mysqli_fetch_array($result2))
{
    //----------------------------------------    
    $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, $row['ficha']);
    $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, $row['cedula']);
    $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, $row['apenom']);
    $objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, $row['fechavac']);
    $objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, $row['fechareivac']);
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

$final = $rowCount-1;/*
$objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount,'Total:');
$objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowCount, '=CONTAR(G2:G'.$final.')');*/
$objPHPExcel->getActiveSheet()->setTitle('Lista de Posiciones');
// clean the output buffer
ob_clean();
header('Content-Type: application/vnd.openxmlformats-   officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="lista_colab_vacaciones.xlsx"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
?>