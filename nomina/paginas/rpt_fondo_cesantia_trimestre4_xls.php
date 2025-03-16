<?php
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('America/Caracas');

if (PHP_SAPI == 'cli')
	die('Este ejemplo solo se debe ejecutar desde un navegador Web');

include('../lib/common.php');
$conexion=conexion();

//$num_semestre=$_GET['num_semestre'];
$codtip=$_GET['codtip'];

function allBordersThin(){
	$style = array('borders'=>array(
			'allborders'=>array('style'=> PHPExcel_Style_Border::BORDER_THIN)
	));
	return $style;
}

function cellColor($cells,$color){
    global $objPHPExcel;
    $objPHPExcel->getActiveSheet()->getStyle($cells)->getFill()
        ->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID,
        'startcolor' => array('rgb' => $color)));
}

function fecha($value) { // fecha de YYYY/MM/DD a DD/MM/YYYY
  if ( ! empty($value) ) return substr($value,8,2) ."/". substr($value,5,2) ."/". substr($value,0,4);
  }
require_once 'phpexcel/Classes/PHPExcel.php';

$objPHPExcel = new PHPExcel();

$objPHPExcel->getProperties()->setCreator("Selectra")
							 ->setLastModifiedBy("Selectra")
							 ->setTitle("Fondo de Cesantia")
							 ->setSubject("Office 2007 XLSX Test Document");

$sql = "SELECT e.nom_emp as empresa, e.rif, e.tel_emp as telefono, e.dir_emp as direccion, e.ciu_emp as ciudad, 
               e.edo_emp, e.imagen_izq as logo
		FROM   nomempresa e";
$res=query($sql, $conexion);
$fila=fetch_array($res);
$logo=$fila['logo'];

//$sql2 = "SELECT np.periodo_ini as desde, np.periodo_fin as hasta  FROM nom_nominas_pago np 
//         WHERE  np.codnom=".$codnom." AND np.tipnom=".$codtip;
//$res2=query($sql2, $conexion);
//$fila2=fetch_array($res2);

/*$objDrawing->setName('Logo');
$objDrawing->setDescription('Logo');
$objDrawing->setCoordinates('B2');
$objDrawing->setPath('../imagenes/'.$logo);
//$objDrawing->setResizeProportional(true);
$objDrawing->setHeight(80);
//$objDrawing->setWidth(220);
$objDrawing->setOffsetX(0);
$objDrawing->setOffsetY(0);
$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
*/
//$objDrawing->setHeight(36);
//$objPHPExcel->getActiveSheet()->getHeaderFooter()->addImage($objDrawing, PHPExcel_Worksheet_HeaderFooter::IMAGE_HEADER_LEFT);

$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('E2',  $fila['empresa'])
            ->setCellValue('E3', 'RIF '.$fila['rif'].' Telefonos '.$fila['telefono'])
            ->setCellValue('E4', 'Direccion: '.$fila['direccion'])
            ->setCellValue('E7', 'FONDO DE CESANTIA')
            ->setCellValue('E8', 'Cuarto Trimestre (Octubre-Noviembre-Diciembre)');

$objPHPExcel->getActiveSheet()->getStyle('E7')->getFont()->setName('Arial');
$objPHPExcel->getActiveSheet()->getStyle('E7')->getFont()->setSize(14);
//$objPHPExcel->getActiveSheet()->mergeCells('C2:D5');
$objPHPExcel->getActiveSheet()->mergeCells('E2:P2');
$objPHPExcel->getActiveSheet()->mergeCells('E3:P3');
$objPHPExcel->getActiveSheet()->mergeCells('E4:P4');
$objPHPExcel->getActiveSheet()->mergeCells('E5:P5');
$objPHPExcel->getActiveSheet()->mergeCells('E7:K7');
$objPHPExcel->getActiveSheet()->mergeCells('E8:K8');
//$objPHPExcel->getActiveSheet()->getStyle('C2:D5')->applyFromArray(allBordersThin());
//$objPHPExcel->getActiveSheet()->getStyle('C2:D5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//$objPHPExcel->getActiveSheet()->getStyle('C2:D5')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('E7:K7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('E8:K8')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('E7:K7')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('E2')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('E3')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('E4')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('B10:L10')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('B10:L10')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(17);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(25);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(25);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(25);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(25);
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(20);
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('B10', 'NOMBRE EMPLEADO')
            ->setCellValue('C10', 'CEDULA IDENTIDAD')
            ->setCellValue('D10', 'FECHA INGRESO')
            ->setCellValue('E10', 'NUMERO SEGURO SOCIAL')
            ->setCellValue('F10', 'PRIMER MES (OCTUBRE)')
            ->setCellValue('G10', 'SEGUNDO MES (NOVIEMBRE)')
            ->setCellValue('H10', 'TERCER MES (DICIEMBRE)')
            ->setCellValue('I10', 'SALARIO TRIMESTRAL')
            ->setCellValue('J10', 'PRIMA ANTIGÜEDAD')                   
            ->setCellValue('K10', 'INDEMNIZACION')
            ->setCellValue('L10', 'TOTALES');
cellColor('B10:L10', 'FFFF00');
$objPHPExcel->getActiveSheet()->getStyle('B10:L10')->applyFromArray(allBordersThin());
$sql = "SELECT DISTINCT np.ficha, np.cedula as cedula, np.apenom as nombre, np.seguro_social as seguro, np.fecing as fecha_ingreso
					FROM   nom_movimientos_nomina nm, nompersonal np
					WHERE  nm.cedula=np.cedula  AND nm.tipnom='".$codtip."' AND (nm.mes=10 OR nm.mes=11 OR nm.mes=12)
					ORDER BY np.apenom";
$i=12;
$total_prima=$total_indemnizacion=$total_total=0;
$res=query($sql, $conexion);
while($fila=fetch_array($res))
{
    $ficha=$fila['ficha'];
    $salario_trimestral=$prima_antiguedad=$indemnizacion=$total=0;
    $sql_mes10 = "SELECT nm.monto as monto
            FROM   nom_movimientos_nomina nm
            WHERE  nm.tipnom=".$codtip." AND nm.ficha=".$ficha." AND ( nm.codcon=100 OR nm.codcon=145) AND nm.mes=10";
    $res_mes10=query( $sql_mes10, $conexion);
    $mes10=0;
    while($fila_mes10=fetch_array($res_mes10))
    {
        $mes10=$mes10+$fila_mes10['monto'];
    }
    $sql_mes11 = "SELECT nm.monto as monto
            FROM   nom_movimientos_nomina nm
            WHERE  nm.tipnom=".$codtip." AND nm.ficha=".$ficha." AND ( nm.codcon=100 OR nm.codcon=145) AND nm.mes=11";
    $res_mes11=query( $sql_mes11, $conexion);
    $mes11=0;
    while($fila_mes11=fetch_array($res_mes11))
    {
        $mes11=$mes11+$fila_mes11['monto'];
    }
    $sql_mes12 = "SELECT nm.monto as monto
            FROM   nom_movimientos_nomina nm
            WHERE  nm.tipnom=".$codtip." AND nm.ficha=".$ficha." AND ( nm.codcon=100 OR nm.codcon=145) AND nm.mes=12";
    $res_mes12=query( $sql_mes12, $conexion);
    $mes12=0;
    while($fila_mes12=fetch_array($res_mes12))
    {
        $mes12=$mes12+$fila_mes12['monto'];
    }
    $salario_trimestral=$mes10+$mes11+$mes12;
    $prima_antiguedad=$salario_trimestral*0.01923;
    $indemnizacion=$salario_trimestral*0.00327;
    $total=$prima_antiguedad+$indemnizacion;
    $total_prima=$total_prima+$prima_antiguedad;
    $total_indemnizacion=$total_indemnizacion+$indemnizacion;
    $total_total=$total_total+$total;
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$i, utf8_encode($fila['nombre']));
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.$i, $fila['cedula']);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'.$i, $fila['fecha_ingreso']);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'.$i, $fila['seguro_social']);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F'.$i, $mes10);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G'.$i, $mes11);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H'.$i, $mes12);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('I'.$i, $salario_trimestral);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('J'.$i, $prima_antiguedad);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('K'.$i, $indemnizacion);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('L'.$i, $total);
    $objPHPExcel->getActiveSheet()->getStyle('B'.$i.':L'.$i)->applyFromArray(allBordersThin());
        //$objPHPExcel->getActiveSheet()->getStyle('E'.$i)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
    $i++;
}
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('I'.$i, "TOTALES");
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('J'.$i, $total_prima);
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('K'.$i, $total_indemnizacion);
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('L'.$i, $total_total);
$i=$i+7;

$objPHPExcel->getActiveSheet()->mergeCells('E'.$i.':G'.($i+4));
$objPHPExcel->getActiveSheet()->getStyle('E'.$i.':G'.($i+4))->applyFromArray(allBordersThin());
$objPHPExcel->getActiveSheet()->mergeCells('H'.$i.':J'.($i+4));
$objPHPExcel->getActiveSheet()->getStyle('H'.$i.':J'.($i+4))->applyFromArray(allBordersThin());
$objPHPExcel->getActiveSheet()->mergeCells('K'.$i.':M'.($i+4));
$objPHPExcel->getActiveSheet()->getStyle('K'.$i.':M'.($i+4))->applyFromArray(allBordersThin());
$i=$i+5;
$objPHPExcel->getActiveSheet()->mergeCells('E'.$i.':G'.$i);
$objPHPExcel->getActiveSheet()->getStyle('E'.$i.':G'.$i)->applyFromArray(allBordersThin());
$objPHPExcel->getActiveSheet()->setCellValue('E'.$i, 'RECIBIDO POR');
$objPHPExcel->getActiveSheet()->getStyle('E'.$i)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->mergeCells('H'.$i.':J'.$i);
$objPHPExcel->getActiveSheet()->getStyle('H'.$i.':J'.$i)->applyFromArray(allBordersThin());
$objPHPExcel->getActiveSheet()->setCellValue('H'.$i, 'AUTORIZADO POR');
$objPHPExcel->getActiveSheet()->getStyle('H'.$i)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('H'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->mergeCells('K'.$i.':M'.$i);
$objPHPExcel->getActiveSheet()->getStyle('K'.$i.':M'.$i)->applyFromArray(allBordersThin());
$objPHPExcel->getActiveSheet()->setCellValue('K'.$i, 'PREPARADO POR');
$objPHPExcel->getActiveSheet()->getStyle('K'.$i)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('K'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);



$objPHPExcel->getActiveSheet()->setTitle('Reporte');
$objPHPExcel->getActiveSheet()->setSelectedCells('B100');

// Redirect output to a client’s web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="Reporte_Fondo_Cesantia_Trimestre4.xls"');
header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header ('Pragma: public'); // HTTP/1.0

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

$objWriter->save('php://output');

exit;






