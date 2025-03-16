<?php
date_default_timezone_set('America/Panama');
// Unix
setlocale(LC_TIME, 'es_PA.UTF-8');
include("../../nomina/lib/common.php");
require_once('../../nomina/lib/database.php');
$db = new Database($_SESSION['bd']);
require_once '../../includes/phpexcel/Classes/PHPExcel.php';

$objPHPExcel = new PHPExcel();
$objPHPExcel = PHPExcel_IOFactory::load("plantillas/plantilla4.xlsx");
//------------------------------------------------------------
$sqlC = "SELECT a.*,b.Descripcion,  a.fecnac from nompersonal a "
        . "left join departamento b ON a.IdDepartamento=b.IdDepartamento "
        . "where estado != 'Egresado' and MONTH(fecnac) = MONTH(NOW()) ORDER BY DAY(fecnac) ASC"; 
$result2 = $db->query($sqlC, $conexion);
$i=0;

//--------------------------------------------------------------
//titulo de la tabla
$objPHPExcel->getActiveSheet()->SetCellValue('B1', "PROXIMOS CUMPLEAÑOS");
$objPHPExcel->getActiveSheet()->SetCellValue('G1', "Fecha: ".date('d-m-Y'));

//Encabezado de la tabla
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(40);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(40);
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
$objPHPExcel->getActiveSheet()->SetCellValue('B2', "Nombres");
$objPHPExcel->getActiveSheet()->SetCellValue('C2', "Apellidos");
$objPHPExcel->getActiveSheet()->SetCellValue('D2', "Cédula");
$objPHPExcel->getActiveSheet()->SetCellValue('E2', "Posición");
$objPHPExcel->getActiveSheet()->SetCellValue('F2', "Departamento");
$objPHPExcel->getActiveSheet()->SetCellValue('G2', "Fecha Nacimiento");
//Fin encabezado tabla
$rowCount = 3;
$count=0;

while($row = mysqli_fetch_array($result2))
{
    //----------------------------------------    
    if($row['fecnac']!="0000-00-00" && $row['fecnac']!="" && $row['fecnac']!=NULL)
    {

        $fecha_nac=strftime('%d/%b/%Y', strtotime($row['fecnac']));
    }  
    else
    {
        $fecha_nac="";
    }
    
    $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, utf8_encode($row['nombres']). " ".utf8_encode($row['nombres2']));
    $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, utf8_encode($row['apellidos']). " ".utf8_encode($row['apellido_materno']));
    $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, $row['cedula']);
    $objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, $row['nomposicion_id']);
    $objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, $row['Descripcion']);
    $objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowCount, $fecha_nac);
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
$objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount,'Total:');
$objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, '=CONTAR(G2:G'.$final.')');
$objPHPExcel->getActiveSheet()->setTitle('Cumpleanios');
// clean the output buffer
ob_clean();
header('Content-Type: application/vnd.openxmlformats-   officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="listado_cumpleanios.xlsx"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
?>
