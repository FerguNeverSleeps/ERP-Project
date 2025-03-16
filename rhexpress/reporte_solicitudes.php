<?php
session_start();
// include("../../nomina/lib/common.php");
// require_once('../../nomina/lib/database.php');
//-------------------------------------------------
require_once "config/rhexpress_config.php";
$conexion =  mysqli_connect( DB_HOST, DB_USUARIO, DB_CLAVE, $_SESSION['bd'] ) or die( 'No Hay Conexión con el Servidor de Mysql' );
        mysqli_query($conexion, 'SET CHARACTER SET utf8');
require_once '../includes/phpexcel/Classes/PHPExcel.php';

$objPHPExcel = new PHPExcel();
$objPHPExcel = PHPExcel_IOFactory::load("reportes/excel/reporte_solicitudes.xlsx");
//------------------------------------------------------------
$sql = "select a.codcon,a.descrip,
SUM(IF(month(b.periodo_fin)=01,a.monto,0)) As 'ENERO',
SUM(IF(month(b.periodo_fin)=02,a.monto,0)) As 'FEBRERO',
SUM(IF(month(b.periodo_fin)=03,a.monto,0)) As 'MARZO',
SUM(IF(month(b.periodo_fin)=04,a.monto,0)) As 'ABRIL',
SUM(IF(month(b.periodo_fin)=05,a.monto,0)) As 'MAYO',
SUM(IF(month(b.periodo_fin)=06,a.monto,0)) As 'JUNIO',
SUM(IF(month(b.periodo_fin)=07,a.monto,0)) As 'JULIO',
SUM(IF(month(b.periodo_fin)=08,a.monto,0)) As 'AGOSTO',
SUM(IF(month(b.periodo_fin)=09,a.monto,0)) As 'SEPTIEMBRE',
SUM(IF(month(b.periodo_fin)=10,a.monto,0)) As 'OCTUBRE',
SUM(IF(month(b.periodo_fin)=11,a.monto,0)) As 'NOVIEMBRE',
SUM(IF(month(b.periodo_fin)=12,a.monto,0)) As 'DICIEMBRE'
from nom_movimientos_nomina a 
left join `nom_nominas_pago` `b` on `b`.`codnom` = `a`.`codnom`
where year(b.periodo_fin)=2020 group by `a`.`codcon`";
$result2 = $conexion->query($sql, $conexion);
$i=0;

//--------------------------------------------------------------
//titulo de la tabla
$objPHPExcel->getActiveSheet()->SetCellValue('B1', "Resumen de Planilla por Mes");
$objPHPExcel->getActiveSheet()->SetCellValue('J1', "Usuario: ".$_SESSION['nombre']);
$objPHPExcel->getActiveSheet()->SetCellValue('N1', date('d-m-Y'));

//Encabezado de la tabla
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
$objPHPExcel->getActiveSheet()->getStyle('B2:O2')->applyFromArray($borders);

//Fin encabezado tabla
$rowCount = 3;
$count=0;

while($row = mysqli_fetch_array($result2))
{
    //----------------------------------------    
    $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, $row['codcon']);
    $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, $row['descrip']);
    $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, $row['ENERO']);
    $objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, $row['FEBRERO']);
    $objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, $row['MARZO']);
    $objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowCount, $row['ABRIL']);
    $objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowCount, $row['MAYO']);
    $objPHPExcel->getActiveSheet()->SetCellValue('I'.$rowCount, $row['JUNIO']);
    $objPHPExcel->getActiveSheet()->SetCellValue('J'.$rowCount, $row['JULIO']);
    $objPHPExcel->getActiveSheet()->SetCellValue('K'.$rowCount, $row['AGOSTO']);
    $objPHPExcel->getActiveSheet()->SetCellValue('L'.$rowCount, $row['SEPTIEMBRE']);
    $objPHPExcel->getActiveSheet()->SetCellValue('M'.$rowCount, $row['OCTUBRE']);
    $objPHPExcel->getActiveSheet()->SetCellValue('N'.$rowCount, $row['NOVIEMBRE']);
    $objPHPExcel->getActiveSheet()->SetCellValue('O'.$rowCount, $row['DICIEMBRE']);
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
    $hasta = "O".$rowCount;
    $objPHPExcel->getActiveSheet()->getStyle($desde.':'.$hasta)->applyFromArray($borders);
    $rowCount++;
    $count++;
}

$objPHPExcel->getActiveSheet()->setTitle('Resumen de Planilla por Mes');

header('Content-Type: application/vnd.openxmlformats-   officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="reporte_solicitudes.xlsx"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');

?>