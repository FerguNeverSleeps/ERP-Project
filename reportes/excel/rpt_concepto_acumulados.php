<?php
include("../../nomina/lib/common.php");
require_once('../../nomina/lib/database.php');
$db = new Database($_SESSION['bd']);
require_once '../../includes/phpexcel/Classes/PHPExcel.php';

$objPHPExcel = new PHPExcel();
$objPHPExcel = PHPExcel_IOFactory::load("plantillas/acumulados_concepto.xlsx");
//------------------------------------------------------------
$ficha=$_GET['ficha'];
$concepto=$_GET['concepto'];
$anio=$_GET['anio'];

$meses = array('','Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre');

if ($concepto != 'all') {
	$sql = "SELECT sum(monto) as acumulado,codcon,descrip,mes FROM nomvis_acumulados
	where ficha='{$ficha}' and codcon='{$concepto}' and anio='{$anio}' and cod_tac='CON' group by mes,codcon order by mes asc";
}else{
	$sql = "SELECT sum(monto) as acumulado,codcon,descrip,mes FROM nomvis_acumulados
	where ficha='{$ficha}' and anio='{$anio}' and cod_tac='CON' group by mes,codcon order by mes asc";
}
$result = $db->query($sql, $conexion);

$sql2 = "SELECT * FROM nompersonal WHERE ficha='{$ficha}'";
$result2 = $db->query($sql2, $conexion);
$emp = mysqli_fetch_array($result2);
$i=0;

//--------------------------------------------------------------
//titulo de la tabla
$objPHPExcel->getActiveSheet()->SetCellValue('B1', " Acumulados por concepto");
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
$objPHPExcel->getActiveSheet()->SetCellValue('C2', $emp['apenom'].", ".$emp['ficha']);
$objPHPExcel->getActiveSheet()->getStyle('B3:C3')->applyFromArray($borders);
$objPHPExcel->getActiveSheet()->SetCellValue('B3', "Concepto");
$objPHPExcel->getActiveSheet()->SetCellValue('C3', "Acumulado");

//Fin encabezado tabla
$rowCount = 4;
$count=0;

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

$mesAnt='';
while($row = mysqli_fetch_array($result))
{
    if ($row['mes']!=$mesAnt){
        //----------------------------------------
        $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, $meses[$row['mes']]);
        $objPHPExcel->getActiveSheet()->getStyle('B'.$rowCount)->applyFromArray($borders);
        $mesAnt=$row['mes'];
        $rowCount++;
    }

    $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, $row['descrip']);
    $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, $row['acumulado']);

    $objPHPExcel->getActiveSheet()->getStyle('B'.$rowCount.':C'.$rowCount)->applyFromArray($borders);
    $rowCount++;
    $count++;
}
$objPHPExcel->getActiveSheet()->setTitle('Lista de Acumulados');
// clean the output buffer
ob_clean();
header('Content-Type: application/vnd.openxmlformats-   officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="lista_acumulados.xlsx"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
?>
