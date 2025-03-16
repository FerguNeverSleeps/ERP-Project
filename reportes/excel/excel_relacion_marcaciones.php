<?php
if (!isset($_SESSION)) {
  session_start();
  ob_start();
}
include("../../nomina/lib/common.php");
require_once('../../nomina/lib/database.php');
$db            = new Database($_SESSION['bd']);
require_once '../../includes/phpexcel/Classes/PHPExcel.php';
//echo $_GET['fecha_inicio'];
//echo $_GET['fecha_fin'];
//exit;
$objPHPExcel   = new PHPExcel();
$objPHPExcel   = PHPExcel_IOFactory::load("plantillas/plantilla5.xlsx");
$id_empleado        = (isset($_GET['id_empleado'])) ? $_GET['id_empleado'] : '' ;
$fecha_inicio        = (isset($_GET['fecha_inicio'])) ? fecha_sql($_GET['fecha_inicio']) : '' ;
$fecha_fin        = (isset($_GET['fecha_fin'])) ? fecha_sql($_GET['fecha_fin']) : '' ;
//------------------------------------------------------------
$sql           = "SELECT apenom, ficha, cedula, nombres, apellidos,apellido_materno,nombres2 from nompersonal where personal_id = '{$id_empleado}'";
$result        = $db->query($sql, $conexion);
$nombre_titulo = $result->fetch_array();
$nom_titulo    = $nombre_titulo['nombres']." ".$nombre_titulo['nombres2']." ".$nombre_titulo['apellidos']." ".$nombre_titulo['apellido_materno'];

$sql2= "SELECT *, ri.nombre as nombre
          FROM  reloj_marcaciones rm          
          LEFT JOIN nompersonal pe ON (rm.id_empleado = pe.personal_id)
          LEFT JOIN reloj_info ri ON (rm.dispositivo = ri.id_dispositivo)
          WHERE id_empleado =  '$id_empleado' AND
          fecha BETWEEN '$fecha_inicio' AND '$fecha_fin'";
//echo $sql2;
//exit;
$result2       = $db->query($sql2, $conexion);
$i=0;
//--------------------------------------------------------------
//titulo de la tabla
$objPHPExcel->getActiveSheet()->SetCellValue('A1', "Relacion de Marcaciones");
$objPHPExcel->getActiveSheet()->SetCellValue('C1', "Empleado ".$nom_titulo);
$objPHPExcel->getActiveSheet()->SetCellValue('F1', "Fecha: ".date('d-m-Y'));/*
$objPHPExcel->getActiveSheet()->SetCellValue('D1', "Usuario: ".$_SESSION['nombre']);
*/

//Encabezado de la tabla
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
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
        'color' => array('rgb' => 'FFFFFF'),
        'size'  => 12,
        'name'  => 'Verdana'
    )
);
$objPHPExcel->getActiveSheet()->getStyle('A2:F2')->applyFromArray($borders);
$objPHPExcel->getActiveSheet()->SetCellValue('A2', "Cédula");
$objPHPExcel->getActiveSheet()->SetCellValue('B2', "Ficha");
$objPHPExcel->getActiveSheet()->SetCellValue('C2', "Nombre y Apellidos");
$objPHPExcel->getActiveSheet()->SetCellValue('D2', "Fecha");
$objPHPExcel->getActiveSheet()->SetCellValue('E2', "Hora");
$objPHPExcel->getActiveSheet()->SetCellValue('F2', "Dispositivo");
//Fin encabezado tabla
$rowCount = 3;
$count=0;

while($row = mysqli_fetch_array($result2))
{
    
    $objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, $row['cedula']);
    $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, $row['ficha']);
    $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, $row['apenom']);
    $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, $row['fecha']);
    $objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, $row['hora']);
    $objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, $row['nombre']);  
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
    $desde = "A".$rowCount;
    $hasta = "F".$rowCount;
    $objPHPExcel->getActiveSheet()->getStyle($desde.':'.$hasta)->applyFromArray($borders);
    $rowCount++;
    $count++;
}

$final = $rowCount-1;/*
$objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount,'Total:');
$objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowCount, '=CONTAR(G2:G'.$final.')');*/
$objPHPExcel->getActiveSheet()->setTitle($nombre_titulo['cedula']);
//$objPHPExcel->getActiveSheet()->setTitle('Titulo');
//$nombre_archivo = "Vacaciones_".$nombre_titulo['apenom']."-".$nombre_titulo['cedula']."xlsx";
// clean the output buffer
ob_clean();
header('Content-Type: application/vnd.openxmlformats-   officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename=relacion_marcaciones.xlsx');
header('Cache-Control: max-age=0');
ob_clean();
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
?>