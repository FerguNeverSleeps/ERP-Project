<?php
include("../../nomina/lib/common.php");
require_once('../../nomina/lib/database.php');
$db = new Database($_SESSION['bd']);
require_once '../../includes/phpexcel/Classes/PHPExcel.php';

$objPHPExcel = new PHPExcel();
$objPHPExcel = PHPExcel_IOFactory::load("./plantillas/plantilla6.xlsx");

$justificacion = $_GET["justificacion"];
$departamento = $_GET["departamento"];
$sql = "SELECT b.apenom as nombre, d.descripcion as descripcion, SUM(a.tiempo) as tiempo,SUM(a.dias) as dias,SUM(a.horas) as horas,SUM(a.minutos) as minutos FROM dias_incapacidad as a,nompersonal as b, departamento as c, dias_incapacidad as d WHERE a.cod_user=b.personal_id AND a.tipo_justificacion='$justificacion' AND c.IdDepartamento='$departamento' AND b.IdDepartamento=c.IdDepartamento AND a.tipo_justificacion=d.idtipo GROUP BY nombre";
$res = $db->query($sql);
function justificacion($jus)
{
	if($jus==5)
	{
		return "Incapacidad";
	}
	if($jus==6)
	{
		return "Incapacidad por discapacidad";
	}
	if($jus==8)
	{
		return "Incapacidad por familiar discapacitado";
	}
}

//titulo de la tabla
$objPHPExcel->getActiveSheet()->SetCellValue('B1', "Tiempos por Tipo de Funcionario");

//Encabezado de la tabla
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(40);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(40);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(10);

$borders = array(
      'borders' => array(
        'allborders' => array(
          'style' => PHPExcel_Style_Border::BORDER_THIN,
          'color' => array('argb' => 'FF0000'),
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
$objPHPExcel->getActiveSheet()->SetCellValue('B2', "Nombre");
$objPHPExcel->getActiveSheet()->SetCellValue('C2', "Justificacion");
$objPHPExcel->getActiveSheet()->SetCellValue('D2', "Tiempo");
$objPHPExcel->getActiveSheet()->SetCellValue('E2', "Dias");
$objPHPExcel->getActiveSheet()->SetCellValue('F2', "Horas");
$objPHPExcel->getActiveSheet()->SetCellValue('G2', "Minutos");
//Fin encabezado tabla
$rowCount = 3;
while($row = fetch_array($res)){
	$objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, $row['nombre']);
	$objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, justificacion($row['justificativo']));
	$objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, $row['tiempo']);
	$objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, $row['tiempo']/8);
	$objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, $row['horas']);
	$objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowCount, $row['minutos']);
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
}
$final = $rowCount-1;
$objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount,'Total:');
$objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowCount, '=CONTAR(G3:G'.$final.')');
$objPHPExcel->getActiveSheet()->setTitle('Tiempo por Funcionarios');

header('Content-Type: application/vnd.openxmlformats-   officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="tiempo_funcionario_tipo.xlsx"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');

 ?>