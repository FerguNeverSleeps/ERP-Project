<?php
include("../../nomina/lib/common.php");
require_once('../../nomina/lib/database.php');
$db = new Database($_SESSION['bd']);
require_once '../../includes/phpexcel/Classes/PHPExcel.php';

$objPHPExcel = new PHPExcel();
$objPHPExcel = PHPExcel_IOFactory::load("plantillas/plantilla_blanco.xlsx");
//------------------------------------------------------------
$sql2 = "SELECT * FROM nomnivel1";
$result2 = $db->query($sql2, $conexion);
$i=0;

//--------------------------------------------------------------
//titulo de la tabla
$objPHPExcel->getActiveSheet()->SetCellValue('B1', "Reporte Salario Neto");

//Encabezado de la tabla
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
$rowCount = 2;
//Fin encabezado tabla
$count=0;
$mes = array('1' => "Enero",
 '2' => "Febrero",
 '3' => "Marzo",
 '4' => "Abril",
 '5' => "Mayo",
 '6' => "Junio",
 '7' => "Julio",
 '8' => "Agosto",
 '9' => "Septiembre",
 '10' => "Octubre",
 '11' => "Noviembre",
 '12' => "Diciembre");
 
 $letra = "C";   
 $anio = "2019";
 $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(40);
 //----------------------------------------    
 for( $i = 1;  $i<=12 ; $i++)
 {
    $objPHPExcel->getActiveSheet()->getColumnDimension($letra)->setWidth(15);
    $objPHPExcel->getActiveSheet()->SetCellValue($letra.$rowCount, $mes[$i]." ".$anio);
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
    $letra++;

 }
 $rowCount++;

 $inicio = $rowCount;
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
while($row = mysqli_fetch_array($result2))
{
    $letra = "B"; 
    $objPHPExcel->getActiveSheet()->SetCellValue($letra.$rowCount,utf8_encode( $row['descrip'] ));
    $nivel1 = $row['codorg'];
    $letra++;  
    //----------------------------------------    
    for( $i = 1;  $i<=12 ; $i++)
    {
        $sql_row = "SELECT SUM(nmn.monto) as monto,
        SUM( IF(nmn.tipcon = 'A' AND nmn.codcon='100', ROUND(nmn.monto,2),0) ) AS salario_bruto
        FROM nom_movimientos_nomina as nmn
        INNER JOIN nomnivel1 nv1 ON (nv1.codorg = nmn.codnivel1)
        WHERE nmn.mes = '{$i}' and nmn.anio = '{$anio}' and nmn.codnivel1 = '{$nivel1}' and nmn.tipcon in ('A','D') and nmn.codcon in ('100')
        GROUP BY nmn.codnivel1";
        $result_row = $db->query($sql_row);
        $celda = $result_row->fetch_array();
        $monto = ( $celda['monto']!= "") ?  $celda['monto'] : 0 ;
        $salario_bruto = ( $celda['salario_bruto']!= "") ?  $celda['salario_bruto'] : 0 ;
        $monto = $salario_bruto;
        $objPHPExcel->getActiveSheet()->SetCellValue($letra.$rowCount, $monto);
        $letra++;


    }

    $desde = "B".$rowCount;
    $hasta = "C".$rowCount;
   // $objPHPExcel->getActiveSheet()->getStyle($desde.':'.$hasta)->applyFromArray($borders);
    $rowCount++;
    $count++;
}
$letra="C";
$final = $rowCount-1;
$objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount,'Total:');
//----------------------------------------    
for( $i = 1;  $i<=12 ; $i++)
{
   $objPHPExcel->getActiveSheet()->getColumnDimension($letra)->setWidth(15);
   $objPHPExcel->getActiveSheet()->SetCellValue($letra.$rowCount, "=SUMA(".$letra.$inicio.":".$letra.$final.")");
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
   $letra++;

}
$objPHPExcel->getActiveSheet()->setTitle('SALARIOS NETROS');

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="salarios_brutos.xlsx"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
?>