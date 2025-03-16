<?php
/** Error reporting */
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('America/Caracas');

if (PHP_SAPI == 'cli')
	die('Este ejemplo solo se debe ejecutar desde un navegador Web');

include("../lib/common_excel.php");
//include ("func_bd.php") ;
//include ("funciones_nomina.php");

// $codnom=$_GET['codnom'];
// $codtip=$_GET['codtip'];
$proyecto = $_POST['nivel1'];
$fecha_ini = fecha_sql($_POST['fecha_inicio']);
$fecha_fin = fecha_sql($_POST['fecha_fin']);

function cellColor($cells,$color){
    global $objPHPExcel;
    $objPHPExcel->getActiveSheet()->getStyle($cells)->getFill()
        ->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID,
        'startcolor' => array('rgb' => $color)));
}

/** Include PHPExcel */
require_once 'phpexcel/Classes/PHPExcel.php';

// Create new PHPExcel object
$objPHPExcel = new PHPExcel();

// Set document properties
$objPHPExcel->getProperties()->setCreator("Selectra")
							 ->setLastModifiedBy("Selectra")
							 ->setTitle("Verificar Turnos")
							 ->setSubject("Office 2007 XLSX Test Document")
							 ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
							 ->setKeywords("office 2007 openxml php")
							 ->setCategory("Test result file");

// Add some data
$objPHPExcel->setActiveSheetIndex(0)
            //->setCellValue('B8', 'COMPROBANTE DE DIARIO')
            //->setCellValue('B10', 'Fecha: '.date('d/m/Y'))
            ->setCellValue('B13', 'DEPARTAMENTO')
            //->setCellValue('I11', 'No.')
            //->setCellValue('G13', 'CODIGO')
            //->setCellValue('I13', 'DEBITO')
            //->setCellValue('L13', 'CREDITO')
            ->setCellValue('B15', 'NOMBRE')
            ->setCellValue('C15', 'APELLIDO')
            ->setCellValue('D15', 'FECHA')
            ->setCellValue('E15', 'DIA')
            ->setCellValue('F15', 'FICHA')
            ->setCellValue('G15', 'TURNO');

// $objPHPExcel->getActiveSheet()->mergeCells('B8:L8');
// $objPHPExcel->getActiveSheet()->mergeCells('B10:L10');
// $objPHPExcel->getActiveSheet()->mergeCells('B13:D13');
// $objPHPExcel->getActiveSheet()->mergeCells('I13:J13');

$objPHPExcel->getActiveSheet()->getStyle('B10')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('I11')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('B13:L13')->getFont()->setBold(true);

$styleArray = array(
      'borders' => array(
          'allborders' => array(
              'style' => PHPExcel_Style_Border::BORDER_THICK
          )
      )
  );

$objPHPExcel->getActiveSheet()->getStyle('B8')->getFont()->setName('Arial');
$objPHPExcel->getActiveSheet()->getStyle('B8')->getFont()->setSize(14);
$objPHPExcel->getActiveSheet()->getStyle('B8')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('B8')->getFont()->setItalic(true);
$objPHPExcel->getActiveSheet()->getStyle('B8')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('G13:L13')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

// Consultar datos de la base de datos
if ($proyecto!='0') {
    # code...
$sql = "SELECT DISTINCT np.codnivel1, nn.descrip as departamento 
FROM   nompersonal np
INNER JOIN nomnivel1 nn ON nn.codorg=np.codnivel1
where np.codnivel1=$proyecto";
} else {
    $sql = "SELECT DISTINCT np.codnivel1, nn.descrip as departamento 
FROM   nompersonal np
INNER JOIN nomnivel1 nn ON nn.codorg=np.codnivel1";
}


$conexion=conexion();
$res=query($sql, $conexion);
// $fila=fetch_array($res);
$i=14;
$ini=$i;

// $debitoss=$creditoss=0;
// $neto=0;
while($fila=fetch_array($res)){
    $codnivel1=$fila['codnivel1'];
    $departamento=$fila['departamento'];
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$i, $fila['departamento']);
    $objPHPExcel->getActiveSheet()->mergeCells('B'.$i.':D'.$i);
    $objPHPExcel->getActiveSheet()->getStyle('B'.$i)->getFont()->setBold(true);
    cellColor('B'.$i.':L'.$i, 'FFFF00');
    $i++;
// Ahora realizamos la subconsulta
if($proyecto !='0'){
    $sql2="SELECT c.ficha ,c.fecha,c.dia_fiesta,c.turno_id,n.descripcion,p.nombres,p.apellidos,nn.descrip, 
        CASE WHEN c.dia_fiesta='1' THEN 'normal'
        WHEN c.dia_fiesta='2' THEN 'no laboral'
        WHEN c.dia_fiesta='3' THEN 'nacional'
        ELSE 'laboral'
        END as tipo_dia from
        nomcalendarios_personal AS c
        INNER JOIN nompersonal as p ON p.ficha=c.ficha
        INNER JOIN nomturnos AS n ON n.turno_id=p.turno_id  
        INNER JOIN nomnivel1 as nn ON nn.codorg=p.codnivel1  
        WHERE c.fecha BETWEEN '$fecha_ini' and '$fecha_fin' and p.codnivel1='$proyecto'";
}else{

 $sql2="SELECT c.ficha,c.fecha,n.descripcion,p.nombres,p.apellidos,nn.descrip, 
 CASE WHEN c.dia_fiesta='1' THEN 'normal'
     WHEN c.dia_fiesta='2' THEN 'no laboral'
         WHEN c.dia_fiesta='3' THEN 'nacional'
        ELSE 'laboral'
 END as tipo_dia
 FROM nomcalendarios_personal AS c 
 INNER JOIN nompersonal as p ON p.ficha=c.ficha 
 INNER JOIN nomturnos AS n ON n.turno_id=p.turno_id 
 INNER JOIN nomnivel1 as nn ON nn.codorg=p.codnivel1  
 WHERE c.fecha BETWEEN '$fecha_ini' and '$fecha_fin' and nn.codorg=$codnivel1";
}

    $res2=query($sql2, $conexion);
    
    $i++;
    while($fila2=fetch_array($res2)){
        
      //$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$i, $fila2['ficha']);
    //$objPHPExcel->getActiveSheet()->mergeCells('B'.$i.':D'.$i); 
        // $objPHPExcel->getActiveSheet()->getStyle('B'.$i.':D'.$i)->getAlignment()->setWrapText(true);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$i, $fila2['nombres']);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.$i, $fila2['apellidos']);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'.$i, $fila2['fecha']);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'.$i, $fila2['tipo_dia']);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F'.$i, $fila2['ficha']);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G'.$i, $fila2['descripcion']);
    //     $objPHPExcel->getActiveSheet()->getStyle('G'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    //     if($fila2['tipo']=='A'){ // Es un debito
    //         $objPHPExcel->setActiveSheetIndex(0)->setCellValue('J'.$i, $fila2['monto_total']); 
    //         $objPHPExcel->getActiveSheet()->getStyle('J'.$i)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
    //         $debitoss+=$fila2[monto_total];
    //     }
    //     else{ // Es un credito
    //         $objPHPExcel->getActiveSheet()->getStyle('L'.$i)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);  
    //         $objPHPExcel->setActiveSheetIndex(0)->setCellValue('L'.$i, $fila2['monto_total']);
    //         $creditoss+=$fila2[monto_total];
    //     }
    //     $neto=$debitoss- $creditoss;  
    //  $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$i, 'NOMBRE');
       
       $i++;   
    }
}




// Ahora vamos a agregar a aquellos que no estan en ningun departamento

// $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$i, 'SIN UBICAR');
// $objPHPExcel->getActiveSheet()->mergeCells('B'.$i.':D'.$i);
// $objPHPExcel->getActiveSheet()->getStyle('B'.$i)->getFont()->setBold(true);
// cellColor('B'.$i.':L'.$i, 'FFFF00');
// $i++;

// $sql2="SELECT nc.codcon, nc.descrip as concepto, SUM(nm.monto) as monto_total, nc.tipcon as tipo, nc.ctacon as cuenta_contable 
//        FROM nom_movimientos_nomina as nm 
//        INNER JOIN nomconceptos as nc ON nc.codcon=nm.codcon 
//        INNER JOIN nompersonal as np ON np.ficha=nm.ficha  
//        WHERE nm.codnom=".$codnom." AND nm.tipnom=".$codtip." AND np.codnivel1 IS NULL
//        GROUP BY nc.codcon ORDER BY nc.codcon";

//     $res2=query($sql2, $conexion);
//     while($fila2=fetch_array($res2)){
//         $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$i, $fila2['concepto']);
//         $objPHPExcel->getActiveSheet()->mergeCells('B'.$i.':D'.$i); 
//         //$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':D'.$i)->getAlignment()->setWrapText(true);
//         $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G'.$i, $fila2['cuenta_contable']);
//         $objPHPExcel->getActiveSheet()->getStyle('G'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//         if($fila2['tipo']=='A'){ // Es un debito
//             $objPHPExcel->setActiveSheetIndex(0)->setCellValue('J'.$i, $fila2['monto_total']); 
//             $objPHPExcel->getActiveSheet()->getStyle('J'.$i)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
//             $debitoss+=$fila2[monto_total];
//         }
//         else{ // es un credito
//             $objPHPExcel->getActiveSheet()->getStyle('L'.$i)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);  
//             $objPHPExcel->setActiveSheetIndex(0)->setCellValue('L'.$i, $fila2['monto_total']);
//             $creditoss+=$fila2[monto_total];
//         }
           
//         $i++;   
//     }
    $fin=$i;
    //      $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$i, 'NETO A PAGAR BANCO');
    // $objPHPExcel->getActiveSheet()->mergeCells('B'.$i.':D'.$i); 
    // //$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':D'.$i)->getAlignment()->setWrapText(true);
    // $objPHPExcel->getActiveSheet()->getStyle('B'.$i)->getFont()->setBold(true);
    // cellColor('B'.$i.':L'.$i, 'FFFF00');
    // $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G'.$i, '');
    // $objPHPExcel->getActiveSheet()->getStyle('G'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    
  //  $objPHPExcel->setActiveSheetIndex(0)->setCellValue('J'.$i, $neto); 
 //   $objPHPExcel->getActiveSheet()->getStyle('J'.$i)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
//    $debitoss=$fila2[monto_total];

//     $objPHPExcel->getActiveSheet()->getStyle('L'.$i)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);  
//     $objPHPExcel->setActiveSheetIndex(0)->setCellValue('L'.$i, $neto);
//     $creditoss=$fila2[monto_total];

    
//     $i++;
 

//     $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$i, 'TOTALES');
//     $objPHPExcel->getActiveSheet()->mergeCells('B'.$i.':D'.$i); 
//     //$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':D'.$i)->getAlignment()->setWrapText(true);
//     $objPHPExcel->getActiveSheet()->getStyle('B'.$i)->getFont()->setBold(true);
//     cellColor('B'.$i.':L'.$i, 'FFFF00');
//     $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G'.$i, '');
//     $objPHPExcel->getActiveSheet()->getStyle('G'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//       $debitoss=$fila2[monto_total];
  
//     $objPHPExcel->setActiveSheetIndex(0)->setCellValue('J'.$i, "=SUM(J".$ini.":J".($fin).")"); 
//     $objPHPExcel->getActiveSheet()->getStyle('J'.$i)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

//     $objPHPExcel->getActiveSheet()->getStyle('L'.$i)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);  
//     $objPHPExcel->setActiveSheetIndex(0)->setCellValue('L'.$i, "=SUM(L".$ini.":L".($fin).")");
//     $creditoss=$fila2[monto_total];

    
//     $i++;



$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getStyle('B13:L'.($i-1))->applyFromArray($styleArray);

//
//$i++;
// $objPHPExcel->setActiveSheetIndex(0)
//             ->setCellValue('B'.$i, 'PREPARADO:')
//             ->setCellValue('E'.$i, 'REVISADO:')
//             ->setCellValue('J'.$i, 'REVISADO:');
$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':L'.$i)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->setSelectedCells('B100');

// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Comprobante');


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);


// Redirect output to a client’s web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="verificar_turno.xls"');
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
