<?php
include("../../nomina/lib/common.php");
require_once('../../nomina/lib/database.php');
 $db             = new Database($_SESSION['bd']);
 require_once '../../includes/phpexcel/Classes/PHPExcel.php';
 
 $objPHPExcel    = new PHPExcel();
 $objPHPExcel    = PHPExcel_IOFactory::load("plantillas/plantilla_blanco.xlsx");
 $mes            = (empty($_GET['mes'])) ? 0:   $_GET['mes'];
 $anio           = (empty($_GET['anio'])) ? 0:   $_GET['anio'];
 $tipos          = (empty($_GET['tipos'])) ? 0:   $_GET['tipos'];
 
 $tipos          = explode(",", $tipos);
 $consulta_WHERE = "";
if (is_array($tipos) || is_object($tipos))
{
    $consulta_WHERE = "(";
    foreach ($tipos as $planilla) {
        $temporal = explode('_', $planilla);
        //$planillas[] = array('codigo' => $temporal[0],'tipo' => $temporal[1] );
        $consulta_WHERE .= "(A.codnom='".$temporal[0]."' AND A.tipnom = '".$temporal[1]."') OR ";
    }
    $consulta_WHERE .= '****';
    $consulta_WHERE = str_replace(' OR ****', '', $consulta_WHERE);
    $consulta_WHERE .= ")";
}
else
{
    echo "<script>alert('Error al generar el reporte');";
    echo "location.href='consulta_acreedor_mensual.php';</script>";

}

//------------------------------------------------------------

$i       = 0;
$meses=["ENERO","FEBRERO","MARZO","ABRIL","MAYO","JUNIO","JULIO","AGOSTO","SEPTIEMBRE","OCTUBRE","NOVIEMBRE","DICIEMBRE"];
//------------------------------------------------------------
$sql3= "SELECT   
  A.ficha ,  
  SUM(A.monto) as monto ,  
  B.cedula ,  
  B.nombres , 
  B.apellidos , 
  B.fecnac , 
  B.estado_civil , 
  B.sexo 
FROM 
    nom_movimientos_nomina A  
    JOIN nompersonal B on (A.ficha= B.ficha)  
    JOIN nomconceptos C on (A.codcon = C.codcon)  
    LEFT JOIN nom_nominas_pago D on (D.codnom = A.codnom AND D.tipnom = A.tipnom)
WHERE A.codcon = 502 
    and D.mes_sipe='{$mes}'
    and D.anio_sipe='{$anio}'
    AND ";

$sql3.=$consulta_WHERE;

$sql3.="  
GROUP BY 
    A.ficha
ORDER by 
    C.descrip , A.ficha" ;

$result3 = $db->query($sql3, $conexion);
//Encabezado de la tabla
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
//--------------------------------------------------------------
//titulo de la tabla
$objPHPExcel->getActiveSheet()->mergeCells('B1:H1');
$objPHPExcel->getActiveSheet()->SetCellValue('B1', "INGENIERÍA TÉCNICA ESPECIALIZADA");
//$objPHPExcel->getActiveSheet()->SetCellValue('D1', "Usuario: ".$_SESSION['nombre']);
//$objPHPExcel->getActiveSheet()->SetCellValue('F1', "Fecha: ".date('d-m-Y'));
$objPHPExcel->getActiveSheet()->mergeCells('B2:H2');
$objPHPExcel->getActiveSheet()->SetCellValue('B2', $meses[($mes-1)]);
$objPHPExcel->getActiveSheet()->mergeCells('B3:H3');
$objPHPExcel->getActiveSheet()->SetCellValue('B3', $anio);
$objPHPExcel->getActiveSheet()->mergeCells('B4:H4');
$objPHPExcel->getActiveSheet()->SetCellValue('B4', "NACIONAL DE SEGUROS");
$objPHPExcel->getActiveSheet()->mergeCells('B5:H5');
$objPHPExcel->getActiveSheet()->SetCellValue('B5', "Poliza: 11-04-0949522-0");

$borders_negrita = array(
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
        'size'  => 12,
        'name'  => 'Verdana'
    )
);
$objPHPExcel->getActiveSheet()->getStyle('B1:H5')->applyFromArray($borders_negrita);

$objPHPExcel->getActiveSheet()->getStyle('B1:H5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$objPHPExcel->getActiveSheet()->getStyle('B6:H6')->applyFromArray($borders_negrita);
    $objPHPExcel->getActiveSheet()->getStyle('B6:H6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->SetCellValue('B6', "Cédula");
$objPHPExcel->getActiveSheet()->SetCellValue('C6', "Nombre");
$objPHPExcel->getActiveSheet()->SetCellValue('D6', "Apellido");
$objPHPExcel->getActiveSheet()->SetCellValue('E6', "Fec. Nac.");
$objPHPExcel->getActiveSheet()->SetCellValue('F6', "Edad");
$objPHPExcel->getActiveSheet()->SetCellValue('G6', "Sexo");
$objPHPExcel->getActiveSheet()->SetCellValue('H6', "Edo. Civil");

//Fin encabezado tabla
$rowCount = 7;
$count    = 0;
$total    = 0;
//------------------------------------------------------------
while($row = mysqli_fetch_array($result3))
{
    //----------------------------------------    
    $total += $row['monto'];
    list($Y,$m,$d) = explode("-",$row['fecnac']);
    if(date("md") < $m.$d){
        $edad=date("Y")-$Y-1;
    }
    else
    {
        $edad=date("Y")-$Y;
    }

    $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, $row['cedula']);
    $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, $row['nombres']);
    $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, $row['apellidos']);
    $objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, date('Ymd',strtotime($row['fecnac'])));
    $objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, $edad);
    $objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowCount, $row['sexo']);
    $objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowCount, $row['estado_civil'][0]);

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
    $desde    = "B".$rowCount;
    $centrado = "F".$rowCount;
    $hasta    = "H".$rowCount;    
    $objPHPExcel->getActiveSheet()->getStyle($centrado.':'.$hasta)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $objPHPExcel->getActiveSheet()->getStyle($desde.':'.$hasta)->applyFromArray($borders);
    $rowCount++;
    $count++;
}
//------------------------------------------------------------
$final = $rowCount-1;
$titulo= $mes."_".$anio;
$objPHPExcel->getActiveSheet()->mergeCells('B'.$rowCount.':'.'D'.$rowCount);

$objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount,'Total Empleados:');
$objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, $count);
    $objPHPExcel->getActiveSheet()->getStyle('B'.$rowCount.':'.'E'.$rowCount)->applyFromArray($borders_negrita);
$rowCount+=2;
$objPHPExcel->getActiveSheet()->mergeCells('B'.$rowCount.':'.'G'.$rowCount);
$objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount,'Total A Pagar:');
$objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowCount, $total);
    $objPHPExcel->getActiveSheet()->getStyle('B'.$rowCount.':'.'H'.$rowCount)->applyFromArray($borders_negrita);
$objPHPExcel->getActiveSheet()->setTitle();
$nombre_archivo = "Reporte_Seguro_Vida_".$meses[($mes-1)]."_".$anio.".xlsx";
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename='.$nombre_archivo);
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
?>