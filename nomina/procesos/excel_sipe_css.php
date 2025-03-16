<?
session_start();
ob_start();
$termino= $_SESSION['termino'];
?>
<?
//DECLARACION DE LIBRERIAS
require_once '../lib/common.php';
require_once '../paginas/func_bd.php';
require_once '../../includes/phpexcel/Classes/PHPExcel.php';
//include ("../paginas/funciones_nomina.php");

//include('../lib/common_excel.php');
//include('../paginas/lib/php_excel.php');
//require_once '../paginas/func_bd.php';
//include ("../paginas/funciones_nomina.php");

$conexion=conexion();

$objPHPExcel = new PHPExcel();
$objPHPExcel = PHPExcel_IOFactory::load("excel_sipe_css.xlsx");
//------------------------------------------------------------
$id = $_REQUEST['id'];
//echo $id;
//exit; 

$consulta_id= "SELECT mes, anio "
          . " FROM ach_sipe "
          . " WHERE id='$id' ";
$resultado_id=query($consulta_id,$conexion);
$fila_id=fetch_array($resultado_id,$conexion);
$mes=$fila_id['mes'];
$anio=$fila_id['anio'];

$consulta= "SELECT a.*, b.* "
          . " FROM ach_sipe as a"
          . " LEFT JOIN ach_sipe_detalle as b ON (a.id=b.id)"
          . " WHERE a.id='$id' "
          . " ORDER BY b.posicion ASC ";
$resultado=query($consulta,$conexion);



$meses1      = array('ENERO','FEBRERO','MARZO','ABRIL','MAYO','JUNIO','JULIO','AGOSTO','SEPTIEMBRE','OCTUBRE','NOVIEMBRE','DICIEMBRE');
$mes_letras  = $meses1[$mes - 1];
//--------------------------------------------------------------
//titulo de la tabla
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
        'color' => array('rgb' => '000000'),
        'size'  => 11,
        'name'  => 'Calibri'
    )
);
//$objPHPExcel->getActiveSheet()->setCellValue('B1', 'SIPE MES DE '. $mes_letras .' '. $anio );
//$objPHPExcel->getActiveSheet()->mergeCells('B1:I1');
//$objPHPExcel->getActiveSheet()->SetCellValue('G1', "Usuario: ".$_SESSION['nombre']);
//$objPHPExcel->getActiveSheet()->SetCellValue('I1', "Fecha: ".date('d-m-Y'));
//$objPHPExcel->getActiveSheet()->getStyle('B1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//$objPHPExcel->getActiveSheet()->getStyle('B1')->applyFromArray($borders);
//$objPHPExcel->getActiveSheet()->getStyle('G1')->applyFromArray($borders);
//$objPHPExcel->getActiveSheet()->getStyle('H1')->applyFromArray($borders);
//$objPHPExcel->getActiveSheet()->getStyle('I1')->applyFromArray($borders);

//Encabezado de la tabla
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('V')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('W')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('X')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('Y')->setWidth(20);
$objPHPExcel->getActiveSheet()->getStyle('A1:Y1')->applyFromArray($borders);
$objPHPExcel->getActiveSheet()->SetCellValue('A1', "TIPO DOCUMENTO");
$objPHPExcel->getActiveSheet()->SetCellValue('B1', "NUMERO DOCUMENTO");
$objPHPExcel->getActiveSheet()->SetCellValue('C1', "NUMERO SEGURO SOCIAL");
$objPHPExcel->getActiveSheet()->SetCellValue('D1', "NOMBRE");
$objPHPExcel->getActiveSheet()->SetCellValue('E1', "APELLIDO");
$objPHPExcel->getActiveSheet()->SetCellValue('F1', "SUELDO");
$objPHPExcel->getActiveSheet()->SetCellValue('G1', "HORAS EXTRAS");
$objPHPExcel->getActiveSheet()->SetCellValue('H1', "ISR.");
$objPHPExcel->getActiveSheet()->SetCellValue('I1', "XIII");
$objPHPExcel->getActiveSheet()->SetCellValue('J1', "VACACIONES");
$objPHPExcel->getActiveSheet()->SetCellValue('K1', "COMISIONES");
$objPHPExcel->getActiveSheet()->SetCellValue('L1', "BONIFICACIONES");
$objPHPExcel->getActiveSheet()->SetCellValue('M1', "COMBUSTIBLE");
$objPHPExcel->getActiveSheet()->SetCellValue('N1', "DIETA");
$objPHPExcel->getActiveSheet()->SetCellValue('O1', "SALARIO EN ESPECIA");
$objPHPExcel->getActiveSheet()->SetCellValue('P1', "VIÁTICOS");
$objPHPExcel->getActiveSheet()->SetCellValue('Q1', "GASTOS DE REPRESENTACIÓN");
$objPHPExcel->getActiveSheet()->SetCellValue('R1', "ISR GASTOS DE REPRESENTACIÓN");
$objPHPExcel->getActiveSheet()->SetCellValue('S1', "XIII GASTOS DE REPRESENTACIÓN");
$objPHPExcel->getActiveSheet()->SetCellValue('T1', "PRIMAS DEDUCCIÓN");
$objPHPExcel->getActiveSheet()->SetCellValue('U1', "DIVIDENDOS");
$objPHPExcel->getActiveSheet()->SetCellValue('V1', "PART. BENEF. INGR.");
$objPHPExcel->getActiveSheet()->SetCellValue('W1', "GRATIFICACION AGUINALDO");
$objPHPExcel->getActiveSheet()->SetCellValue('X1', "PREAVISO");
$objPHPExcel->getActiveSheet()->SetCellValue('Y1', "INDEMNIZACIÓN");
/*$objPHPExcel->getActiveSheet()->SetCellValue('L2', "Deducc.");*/
//Fin encabezado tabla
$rowCount = 2;
$count    = 1;
/* Se buscan los campos que tuvieron movimientos en planilla */


while ($fila=fetch_array($resultado,$conexion))
{ 
    
  
    //----------------------------------------
    $fonts = array(
        'borders' => array(
            'allborders' => array(
              'style' => PHPExcel_Style_Border::BORDER_THIN,
              'color' => array('argb' => 'FF000000')
            )
        ),
        'font'  => array(
            'bold'   => false,
            'color'  => array('rgb' => '000000'),
            'size'   => 10,
            'name'   => 'Arial',
            'center' => true
        )
    );
  
    $objPHPExcel->getActiveSheet()->getStyle('A'.$rowCount.':I'.$rowCount)->applyFromArray($fonts);/*
    $objPHPExcel->getActiveSheet()->getStyle('E'.$rowCount)->getNumberFormat()->setFormatCode("#.##0.00");
    $objPHPExcel->getActiveSheet()->getStyle('F'.$rowCount)->getNumberFormat()->setFormatCode("#.##0.00");
    $objPHPExcel->getActiveSheet()->getStyle('G'.$rowCount)->getNumberFormat()->setFormatCode("#.##0.00");
    $objPHPExcel->getActiveSheet()->getStyle('H'.$rowCount)->getNumberFormat()->setFormatCode("#.##0.00");
    $objPHPExcel->getActiveSheet()->getStyle('I'.$rowCount)->getNumberFormat()->setFormatCode("#.##0.00");*/
    /* Se muestran los datos del funcionario/colaborador además de los conceptos devengados que están almacenados en planilla */
  
//    $objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, $fila['tipo_documento']);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit("A$rowCount", $fila['tipo_documento'], PHPExcel_Cell_DataType::TYPE_STRING);
//    $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, $fila['numero_documento']);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit("B$rowCount", $fila['numero_documento'], PHPExcel_Cell_DataType::TYPE_STRING);
   $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, $fila['seguro_social']);
//$objPHPExcel->getActiveSheet()->setCellValueExplicit("C$rowCount", $fila['seguro_social'], PHPExcel_Cell_DataType::TYPE_STRING);
//    $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, utf8_encode($fila['nombre']));
    $objPHPExcel->getActiveSheet()->setCellValueExplicit("D$rowCount", utf8_encode($fila['nombre']), PHPExcel_Cell_DataType::TYPE_STRING);
//    $objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, utf8_encode($fila['apellido']));
    $objPHPExcel->getActiveSheet()->setCellValueExplicit("E$rowCount", utf8_encode($fila['apellido']), PHPExcel_Cell_DataType::TYPE_STRING);
    //7$objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, number_format($salario1, 2, '.',','));
    //$objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, number_format($salario2, 2, '.',','));
    $objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, $fila['sueldo']);
    $objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowCount, $fila['horas_extras']);
    $objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowCount, $fila['impuesto_renta']);
    $objPHPExcel->getActiveSheet()->SetCellValue('I'.$rowCount, $fila['decimo_tercer_mes']);
    $objPHPExcel->getActiveSheet()->SetCellValue('J'.$rowCount, $fila['vacaciones']);
    $objPHPExcel->getActiveSheet()->SetCellValue('K'.$rowCount, $fila['comisiones']);
    $objPHPExcel->getActiveSheet()->SetCellValue('L'.$rowCount, $fila['bonificaciones']);
    $objPHPExcel->getActiveSheet()->SetCellValue('M'.$rowCount, $fila['combustible']);
    $objPHPExcel->getActiveSheet()->SetCellValue('N'.$rowCount, $fila['dieta']);
    $objPHPExcel->getActiveSheet()->SetCellValue('O'.$rowCount, $fila['salario_especies']);
    $objPHPExcel->getActiveSheet()->SetCellValue('P'.$rowCount, $fila['viaticos']);
    $objPHPExcel->getActiveSheet()->SetCellValue('Q'.$rowCount, $fila['gasto_representacion']);
    $objPHPExcel->getActiveSheet()->SetCellValue('R'.$rowCount, $fila['impuesto_renta_gr']);
    $objPHPExcel->getActiveSheet()->SetCellValue('S'.$rowCount, $fila['decimo_tercer_mes_gr']);
    $objPHPExcel->getActiveSheet()->SetCellValue('T'.$rowCount, $fila['prima_produccion']);
    $objPHPExcel->getActiveSheet()->SetCellValue('U'.$rowCount, $fila['dividendo']);
    $objPHPExcel->getActiveSheet()->SetCellValue('V'.$rowCount, $fila['beneficio_ingreso']);
    $objPHPExcel->getActiveSheet()->SetCellValue('W'.$rowCount, $fila['gratificacion_aguinaldo']);
    $objPHPExcel->getActiveSheet()->SetCellValue('X'.$rowCount, $fila['preaviso']);
    $objPHPExcel->getActiveSheet()->SetCellValue('Y'.$rowCount, $fila['indmenizacion']);
//    $objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowCount, number_format($total, 2, '.',','));
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
    $hasta = "Y".$rowCount;
    $objPHPExcel->getActiveSheet()->getStyle($desde.':'.$hasta)->applyFromArray($borders);
    $rowCount++;
    $count++;

}

$final = $rowCount-1;
/* Se muestra la sumatoria de los conceptos que está almacenados en movimientos de planilla */
/*$objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount,'Totales:');
$objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, '=SUM(E2:E'.$final.')');
$objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, '=SUM(F2:F'.$final.')');
$objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowCount, '=SUM(G2:G'.$final.')');
$objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowCount, '=SUM(H2:H'.$final.')');
$objPHPExcel->getActiveSheet()->SetCellValue('I'.$rowCount, '=SUM(I2:I'.$final.')');*/
$objPHPExcel->getActiveSheet()->setTitle('Listado SIPE CSS');
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
$nombre_archivo = "listado_sipe_css_".$mes_letras."_".$anio.".xlsx";
header('Content-Disposition: attachment;filename="'.$nombre_archivo.'"');
header('Cache-Control: max-age=0');
ob_clean();
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
?>