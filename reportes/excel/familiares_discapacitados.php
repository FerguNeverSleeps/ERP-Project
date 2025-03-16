<?php
require_once("../../nomina/lib/common.php");
require_once('../../nomina/lib/database.php');
require_once('../utilidades/funciones_reportes.php');
require_once '../../includes/phpexcel/Classes/PHPExcel.php';
$db = new Database($_SESSION['bd']);
//------------------------------------------------------------
$objPHPExcel = new PHPExcel();
$objPHPExcel = PHPExcel_IOFactory::load("plantillas/funcionarios_discapacitados.xlsx");
//------------------------------------------------------------
$sql     = "SELECT * FROM nomempresa";
$res     = $db->query($sql, $conexion);
$empresa = mysqli_fetch_array($res);
//------------------------------------------------------------
$sql     = "SELECT * FROM nompersonal
            WHERE tiene_familiar_disca = 1 AND estado != 'Egresado' AND estado != 'De Baja'";
$res     = $db->query($sql, $conexion);
//------------------------------------------------------------
$dias = array('Lun','Mar','Mie','Jue','Vie','Sab','Dom');
//------------------------------------------------------------
//Estilos para filas
$borders1 = array(
    'borders' => array(
        'allborders' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN,
            'color' => array(
                'argb' => 'FF000000'
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
            ),
        )
    ),
    'font'  => array(
    'color' => array('rgb' => 'FF000000'),
    'size'  => 11,
    'name'  => 'Arial'
    )
);
//------------------------------------------------------------
//titulo de la tabla
$objPHPExcel->getActiveSheet()->SetCellValue('B1', $empresa['nom_emp']);
$objPHPExcel->getActiveSheet()->SetCellValue('B2', "DIRECCION DE RECURSOS HUMANOS");
$objPHPExcel->getActiveSheet()->SetCellValue('B3', "DEPARTAMENTO DE EQUIPARACION DE OPORTUNIDADES");
$objPHPExcel->getActiveSheet()->SetCellValue('B4', "SERVIDORES CON HIJOS O TUTELADOS CON DISCAPACIDAD - FECHA: ".date('d-m-Y'));
//Fin encabezado tabla
$rowCount = 6;
$i=1;
while($datos = mysqli_fetch_array($res))
{
    $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, $i);
    $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, $datos['nombres']);
    $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, $datos['apellidos']);
    $objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, $datos['cedula']);
    $objPHPExcel->getActiveSheet()->getStyle("B".$rowCount.':'."E".$rowCount)->applyFromArray($borders1);
    $rowCount++;
    $i++;
}
$objPHPExcel->getActiveSheet()->setTitle('Familiares con Discapacidad');

header('Content-Type: application/vnd.openxmlformats-   officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="familiares_discapacitados.xlsx"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
?>