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
							 ->setTitle("Reporte Seguros Optima")
							 ->setSubject("Office 2007 XLSX Test Document");

$sql = "SELECT e.nom_emp as empresa, e.rif, e.tel_emp as telefono, e.dir_emp as direccion, e.ciu_emp as ciudad, 
               e.edo_emp, e.imagen_izq as logo
		FROM   nomempresa e";
$res=query($sql, $conexion);
$fila=fetch_array($res);
$logo=$fila['logo'];

$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', 'SEGUROS OPTIMA');
$objPHPExcel->getActiveSheet()->getStyle('A1:G1')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(35);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(10);

$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A2', '#')
            ->setCellValue('B2', 'Sucursal')
            ->setCellValue('C2', 'Nombre del Colaborador')
            ->setCellValue('D2', 'Fecha de ingreso ')
            ->setCellValue('E2', 'Cedula')
            ->setCellValue('F2', 'Monto')
            ->setCellValue('G2', 'Poliza');
cellColor('A2:G2', 'FF8000');
$objPHPExcel->getActiveSheet()->getStyle('A2:G2')->applyFromArray(allBordersThin());

$sql= "SELECT per.apenom as nombres,per.fecing as fecha_ingreso,per.cedula,n.descrip as 
sucursal FROM nompersonal as per
LEFT JOIN nomnivel1 as n ON n.codorg=per.codnivel1
WHERE per.estado <> 'egresado'
ORDER by fecha_ingreso DESC";
$res=query($sql, $conexion);

$i=3;
$indice=1;
$monto1=0;
$monto2=0;
$monto3=0;

while($fila=fetch_array($res))
{
    $anios=antiguedad($fila['fecha_ingreso'],date("Y-m-d"),"A");
        if ($i==3) {
            # code...
            $aux=$anios;
            $monto1=0;
            $monto=1.75;
            $poliza=5000; 
        }
        if($anios!=$aux && $anios<1) {
            $aux=$anios;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'.$i, 'Sub total:');
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F'.$i, $monto2); 
            $monto2=0; 
        }elseif($anios!=$aux && $anios<2) {
            $aux=$anios;
            $monto=3.50;$poliza=10000;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'.$i, 'Sub total:');
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F'.$i, $monto2); 
            $i++;    
            $monto2=0;    
        }elseif($anios!=$aux && $anios==2) {
            $aux=$anios;
            $monto=8.75;$poliza=25000; 
             $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'.$i, 'Sub total:');
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F'.$i, $monto2);         
            $i++;
            $monto2=0; 
        }elseif($anios>2){
            $aux=$anios;
            $monto=8.75;$poliza=25000; 
            //  $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'.$i, 'Sub total:');
            // $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F'.$i, $monto2);         
            //$i++;
            //$monto2=0; 
        }
        $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':G'.$i)->applyFromArray(allBordersThin());         
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$i, $indice);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$i, $fila['sucursal']);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.$i, utf8_encode($fila['nombres']));        
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'.$i, $fila['fecha_ingreso']);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'.$i, $fila['cedula']);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F'.$i, "$ ".$monto);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G'.$i, "$ ".$poliza);
        $monto1=$monto1+$monto; 
        $monto2=$monto2+$monto;
       
        $i++;$indice++;
        
        $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':G'.$i)->applyFromArray(allBordersThin());
    
    
}
//$i++;   
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'.$i, 'Sub total:');
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('F'.$i, $monto2);         
      
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'.$i, 'Sub total:');
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('F'.$i, "$ ".$monto2);         
$i++;

$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':B'.$i);
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$i, 'TOTAL:');
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('F'.$i, "$ ".$monto1);       
$i=$i+3;
$objPHPExcel->getActiveSheet()->mergeCells('B'.$i.':D'.$i);
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$i, "Prerapado por:_________________ ");
$i=$i+3;
$objPHPExcel->getActiveSheet()->mergeCells('B'.$i.':D'.$i);
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$i, "Fecha:_________________ ");
$i=$i+3;
$objPHPExcel->getActiveSheet()->mergeCells('B'.$i.':D'.$i);
$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$i, "Autorizado por:_________________ ");    $objPHPExcel->getActiveSheet()->mergeCells('A1:E1');
$objPHPExcel->getActiveSheet()->getStyle('A3:A180')->getAlignment()->applyFromArray(array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,)

);

$objPHPExcel->getActiveSheet()->getStyle('B3:B300')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('C3:C300')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('D3:D300')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('E3:E300')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('F3:F300')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('G3:G300')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('A1:G1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('A2:G2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$objPHPExcel->getActiveSheet()->setTitle('Reporte');
$objPHPExcel->getActiveSheet()->setSelectedCells('B100');

// Redirect output to a client’s web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="Reporte_seguros_optima.xls"');
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






