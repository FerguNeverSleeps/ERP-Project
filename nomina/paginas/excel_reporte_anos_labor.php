<?php
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('America/Caracas');

if (PHP_SAPI == 'cli')
	die('Este ejemplo solo se debe ejecutar desde un navegador Web');

include('../lib/common.php');
require_once('funciones_nomina.php');
$conexion=conexion();

$mesano=$_POST['mesano1'];
$anio=explode("-",$mesano);
$newDate = date("Y-m-d", strtotime($mesano));



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
							 ->setTitle("Reporte de anios de labor")
							 ->setSubject("Office 2007 XLSX Test Document");

$sql = "SELECT e.nom_emp as empresa, e.rif, e.tel_emp as telefono, e.dir_emp as direccion, e.ciu_emp as ciudad, 
               e.edo_emp, e.imagen_izq as logo
		FROM   nomempresa e";
$res=query($sql, $conexion);
$fila=fetch_array($res);
$logo=$fila['logo'];

$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', 'PREMIACION AÑOS DE LABOR '.$anio[2].' / CIERRE AL '.$mesano);
            //->setCellValue('E8', 'Primer Trimestre (Enero-Febrero-Marzo)');

// $objPHPExcel->getActiveSheet()->getStyle('E7')->getFont()->setName('Arial');
// $objPHPExcel->getActiveSheet()->getStyle('E7')->getFont()->setSize(14);
//$objPHPExcel->getActiveSheet()->mergeCells('C2:D5');
// $objPHPExcel->getActiveSheet()->mergeCells('E2:P2');
// $objPHPExcel->getActiveSheet()->mergeCells('E3:P3');
// $objPHPExcel->getActiveSheet()->mergeCells('E4:P4');
// $objPHPExcel->getActiveSheet()->mergeCells('E5:P5');
// $objPHPExcel->getActiveSheet()->mergeCells('E7:K7');
// $objPHPExcel->getActiveSheet()->mergeCells('E8:K8');
//$objPHPExcel->getActiveSheet()->getStyle('C2:D5')->applyFromArray(allBordersThin());
//$objPHPExcel->getActiveSheet()->getStyle('C2:D5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//$objPHPExcel->getActiveSheet()->getStyle('C2:D5')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
// $objPHPExcel->getActiveSheet()->getStyle('E7:K7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
// $objPHPExcel->getActiveSheet()->getStyle('E8:K8')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('A1:E1')->getFont()->setBold(true);
// $objPHPExcel->getActiveSheet()->getStyle('E2')->getFont()->setBold(true);
// $objPHPExcel->getActiveSheet()->getStyle('E3')->getFont()->setBold(true);
// $objPHPExcel->getActiveSheet()->getStyle('E4')->getFont()->setBold(true);
// $objPHPExcel->getActiveSheet()->getStyle('A2:E2')->getFont()->setBold(true);
// $objPHPExcel->getActiveSheet()->getStyle('A2:E2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
// $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(25);
// $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(25);
// $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
// $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
// $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
// $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(20);
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A2', '#')
            ->setCellValue('B2', 'Nombre en la Placa ')
            ->setCellValue('C2', 'Años de Labor')
            ->setCellValue('D2', 'Sucursal ')
            ->setCellValue('E2', 'Monto ');
cellColor('A2:E2', 'FF8000');
$objPHPExcel->getActiveSheet()->getStyle('A2:E2')->applyFromArray(allBordersThin());

$sql = "SELECT per.apenom as nombre,per.fecing as fecha_ingreso,cedula,n.descrip as 
sucursal FROM nompersonal as per
LEFT JOIN nomnivel1 as n ON n.codorg=per.codnivel1
WHERE per.estado <> 'egresado'
ORDER by fecha_ingreso ASC";
$i=3;
$indice=1;
$monto=0;$monto2=0;
$res=query($sql, $conexion);
//$total_prima=$total_indemnizacion=$total_total=0;
while($fila=fetch_array($res))
{
    $anios=antiguedad($fila['fecha_ingreso'],$newDate,"A");
    if ($anios==25) {
        $monto=2500;      
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$i, $indice);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$i, utf8_encode($fila['nombre']));
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.$i, $anios."  años");
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'.$i, $fila['sucursal']);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'.$i, "$ ".$monto);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('L'.$i, $total);
        $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':E'.$i)->applyFromArray(allBordersThin());         
        $i++;$indice++;
    } elseif($anios==10) {       
        $monto=1000;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$i, $indice);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$i, utf8_encode($fila['nombre']));
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.$i, $anios."  años");
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'.$i, $fila['sucursal']);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'.$i, "$ ".$monto);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('L'.$i, $total);
        $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':E'.$i)->applyFromArray(allBordersThin());         
        $i++;$indice++;
    }elseif ($anios==5) {
        $monto=500;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$i, $indice);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$i, utf8_encode($fila['nombre']));
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.$i, $anios."  años");
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'.$i, $fila['sucursal']);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'.$i, "$ ".$monto);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('L'.$i, $total);
        $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':E'.$i)->applyFromArray(allBordersThin());         
        $i++;$indice++;
    }elseif ($anios==15) {
        $monto=1500;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$i, $indice);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$i, utf8_encode($fila['nombre']));
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.$i, $anios."  años");
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'.$i, $fila['sucursal']);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'.$i, "$ ".$monto);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('L'.$i, $total);
        $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':E'.$i)->applyFromArray(allBordersThin());         
        $i++;$indice++;
    }elseif ($anios==20) {
        $monto=2000;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$i, $indice);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$i, utf8_encode($fila['nombre']));
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.$i, $anios."  años");
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'.$i, $fila['sucursal']);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'.$i, "$ ".$monto);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('L'.$i, $total);
        $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':E'.$i)->applyFromArray(allBordersThin());         
        $i++;$indice++;
    }
    
    //$objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'.$i+3, "Preparado por:________________ ");
    
    // $ficha=$fila['ficha'];
    // $salario_trimestral=$prima_antiguedad=$indemnizacion=$total=0;
    // $sql_mes1 = "SELECT nm.monto as monto
    //         FROM   nom_movimientos_nomina nm
    //         WHERE  nm.tipnom=".$codtip." AND nm.ficha=".$ficha." AND ( nm.codcon=100 OR nm.codcon=145) AND nm.mes=1";
    // $res_mes1=query( $sql_mes1, $conexion);
    // $mes1=0;
    // while($fila_mes1=fetch_array($res_mes1))
    // {
        //     $mes1=$mes1+$fila_mes1['monto'];
        // }
        // $sql_mes2 = "SELECT nm.monto as monto
        //         FROM   nom_movimientos_nomina nm
        //         WHERE  nm.tipnom=".$codtip." AND nm.ficha=".$ficha." AND ( nm.codcon=100 OR nm.codcon=145) AND nm.mes=2";
        // $res_mes2=query( $sql_mes2, $conexion);
        // $mes2=0;
        // while($fila_mes2=fetch_array($res_mes2))
        // {
            //     $mes2=$mes2+$fila_mes2['monto'];
            // }
            // $sql_mes3 = "SELECT nm.monto as monto
            //         FROM   nom_movimientos_nomina nm
            //         WHERE  nm.tipnom=".$codtip." AND nm.ficha=".$ficha." AND ( nm.codcon=100 OR nm.codcon=145) AND nm.mes=3";
            // $res_mes3=query( $sql_mes3, $conexion);
            // $mes3=0;
            // while($fila_mes3=fetch_array($res_mes3))
            // {
                //     $mes3=$mes3+$fila_mes3['monto'];
                // }
                // $salario_trimestral=$mes1+$mes2+$mes3;
                // $prima_antiguedad=$salario_trimestral*0.01923;
                // $indemnizacion=$salario_trimestral*0.00327;
                // $total=$prima_antiguedad+$indemnizacion;
                // $total_prima=$total_prima+$prima_antiguedad;
                // $total_indemnizacion=$total_indemnizacion+$indemnizacion;
                // $total_total=$total_total+$total;
                // $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$i, $indice);
                // $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$i, utf8_encode($fila['nombre']));
                // $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.$i, $anios);
                // $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'.$i, $fila['sucursal']);
                // $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'.$i, $fila['seguro_social']);
                // // $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F'.$i, $mes1);
                // // $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G'.$i, $mes2);
                // // $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H'.$i, $mes3);
                // // $objPHPExcel->setActiveSheetIndex(0)->setCellValue('I'.$i, $salario_trimestral);
                // // $objPHPExcel->setActiveSheetIndex(0)->setCellValue('J'.$i, $prima_antiguedad);
                // // $objPHPExcel->setActiveSheetIndex(0)->setCellValue('K'.$i, $indemnizacion);
                // $objPHPExcel->setActiveSheetIndex(0)->setCellValue('L'.$i, $total);
                // $objPHPExcel->getActiveSheet()->getStyle('B'.$i.':L'.$i)->applyFromArray(allBordersThin());
                //     //$objPHPExcel->getActiveSheet()->getStyle('E'.$i)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                // $i++;$indice++;
                $monto2=$monto2+$monto;
            }
            $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':E'.$i)->applyFromArray(allBordersThin());
            $objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':B'.$i);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$i, 'TOTAL:');
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'.$i, "$ ".$monto2);
            $i=$i+3;
$objPHPExcel->getActiveSheet()->mergeCells('B'.$i.':D'.$i);
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$i, "Prerapado por:_________________ ");
$i=$i+3;
$objPHPExcel->getActiveSheet()->mergeCells('B'.$i.':D'.$i);
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$i, "Fecha:_________________ ");
$i=$i+3;
$objPHPExcel->getActiveSheet()->mergeCells('B'.$i.':D'.$i);
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$i, "Autorizado por:_________________ ");
// $objPHPExcel->setActiveSheetIndex(0)->setCellValue('I'.$i, "TOTALES");
// $objPHPExcel->setActiveSheetIndex(0)->setCellValue('J'.$i, $total_prima);
// $objPHPExcel->setActiveSheetIndex(0)->setCellValue('K'.$i, $total_indemnizacion);
// $objPHPExcel->setActiveSheetIndex(0)->setCellValue('L'.$i, $total_total);
// $i=$i+7;

// $objPHPExcel->getActiveSheet()->mergeCells('E'.$i.':G'.($i+4));
// $objPHPExcel->getActiveSheet()->getStyle('E'.$i.':G'.($i+4))->applyFromArray(allBordersThin());
// $objPHPExcel->getActiveSheet()->mergeCells('H'.$i.':J'.($i+4));
// $objPHPExcel->getActiveSheet()->getStyle('H'.$i.':J'.($i+4))->applyFromArray(allBordersThin());
// $objPHPExcel->getActiveSheet()->mergeCells('K'.$i.':M'.($i+4));
// $objPHPExcel->getActiveSheet()->getStyle('K'.$i.':M'.($i+4))->applyFromArray(allBordersThin());
// $i=$i+5;
// $objPHPExcel->getActiveSheet()->mergeCells('E'.$i.':G'.$i);
// $objPHPExcel->getActiveSheet()->getStyle('E'.$i.':G'.$i)->applyFromArray(allBordersThin());
// $objPHPExcel->getActiveSheet()->setCellValue('E'.$i, 'RECIBIDO POR');
// $objPHPExcel->getActiveSheet()->getStyle('E'.$i)->getFont()->setBold(true);
// $objPHPExcel->getActiveSheet()->getStyle('E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
     $objPHPExcel->getActiveSheet()->mergeCells('A1:E1');
// $objPHPExcel->getActiveSheet()->getStyle('H'.$i.':J'.$i)->applyFromArray(allBordersThin());
// $objPHPExcel->getActiveSheet()->setCellValue('H'.$i, 'AUTORIZADO POR');
// $objPHPExcel->getActiveSheet()->getStyle('H'.$i)->getFont()->setBold(true);
// $objPHPExcel->getActiveSheet()->getStyle('H'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
// $objPHPExcel->getActiveSheet()->mergeCells('K'.$i.':M'.$i);
    $objPHPExcel->getActiveSheet()->getStyle('A3:A180')->getAlignment()->applyFromArray(array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)
//$objPHPExcel->getActiveSheet()->getStyle('B3:B180')->getAlignment()->applyFromArray(array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)

);
// $objPHPExcel->getActiveSheet()->setCellValue('K'.$i, 'PREPARADO POR');
    $objPHPExcel->getActiveSheet()->getStyle('B3:B180')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->getStyle('C3:C180')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->getStyle('D3:D180')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->getStyle('E3:E180')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->getStyle('A1:E1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->getStyle('A2:E2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
// $objPHPExcel->getActiveSheet()->getStyle('K'.$i)->getFont()->setBold(true);



$objPHPExcel->getActiveSheet()->setTitle('Reporte');
$objPHPExcel->getActiveSheet()->setSelectedCells('B100');

// Redirect output to a client’s web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="Reporte_años_de_labor.xls"');
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






