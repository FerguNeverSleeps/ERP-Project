<?php
include("../../nomina/lib/common.php");
require_once('../../nomina/lib/database.php');
$db = new Database($_SESSION['bd']);
require_once '../../includes/phpexcel/Classes/PHPExcel.php';

$objPHPExcel = new PHPExcel();
$objPHPExcel = PHPExcel_IOFactory::load("plantillas/plantilla_blanco.xlsx");
$tipos       = (empty($_GET['tipos'])) ? 0:   $_GET['tipos'];
 
$tipos   = explode(",", $tipos);
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
$sql2    = "
SELECT B.descrip,A.mes_sipe,A.anio_sipe
FROM 
    nom_nominas_pago as A 
    JOIN nomtipos_nomina B on (B.codtip= A.codtip)   WHERE   
 " ;
$sql2 .= $consulta_WHERE;
$result2 = $db->query($sql2);
$planillas = $result2->fetch_assoc();
$i       = 0;
$meses=["ENERO","FEBRERO","MARZO","ABRIL","MAYO","JUNIO","JULIO","AGOSTO","SEPTIEMBRE","OCTUBRE","NOVIEMBRE","DICIEMBRE"];
//------------------------------------------------------------
$sql3= "SELECT   
    C.descrip,  
    A.ficha ,  
    A.cedula ,  
    B.nombres , 
    B.apellidos , 
    D.fechapago ,  
    SUM(A.monto) as montos,
    A.codcon,
    D.periodo_fin , 
    E.descripcionLarga as proyecto,
    A.codnom,
    A.tipnom
FROM 
    nom_movimientos_nomina A  
    JOIN nompersonal B on (A.ficha= B.ficha)
    JOIN nomconceptos C on (A.codcon = C.codcon)
    JOIN nom_nominas_pago D on (D.codnom = A.codnom AND D.tipnom = A.tipnom)
    JOIN proyectos E on (B.proyecto= E.idProyecto)
WHERE A.codcon = 539  
    and D.mes_sipe='{$planillas[mes_sipe]}'
    and D.anio_sipe='{$planillas[anio_sipe]}'
    AND ";

$sql3.=$consulta_WHERE;

$sql3.="  
GROUP BY 
    A.ficha
ORDER by 
    C.descrip , A.ficha" ;
$result3 = $db->query($sql3);
//--------------------------------------------------------------
//titulo de la tabla
$objPHPExcel->getActiveSheet()->mergeCells('B1:F1');
$objPHPExcel->getActiveSheet()->SetCellValue('B1', "INGENIERÍA TÉCNICA ESPECIALIZADA");
//$objPHPExcel->getActiveSheet()->SetCellValue('D1', "Usuario: ".$_SESSION['nombre']);
//$objPHPExcel->getActiveSheet()->SetCellValue('F1', "Fecha: ".date('d-m-Y'));
$objPHPExcel->getActiveSheet()->mergeCells('B2:F2');
$objPHPExcel->getActiveSheet()->SetCellValue('B2', $meses[($planillas[mes_sipe]-1)]);
$objPHPExcel->getActiveSheet()->mergeCells('B3:F3');
$objPHPExcel->getActiveSheet()->SetCellValue('B3', $planillas[anio_sipe]);
$objPHPExcel->getActiveSheet()->mergeCells('B4:F4');
$objPHPExcel->getActiveSheet()->SetCellValue('B4', "SUNTRAC CUOTA SINDICAL");
//Encabezado de la tabla
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
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
$objPHPExcel->getActiveSheet()->getStyle('B1:F4')->applyFromArray($borders_negrita);

$objPHPExcel->getActiveSheet()->getStyle('B1:F4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('B5:F5')->applyFromArray($borders_negrita);
$objPHPExcel->getActiveSheet()->SetCellValue('B5', "# Colab.");
$objPHPExcel->getActiveSheet()->SetCellValue('C5', "Proyecto");
$objPHPExcel->getActiveSheet()->SetCellValue('D5', "Cédula");
$objPHPExcel->getActiveSheet()->SetCellValue('E5', "Nombre del Empleado");
$objPHPExcel->getActiveSheet()->SetCellValue('F5', "Descuento");

//Fin encabezado tabla
$rowCount = 6;
$count=0;
//------------------------------------------------------------
while($row = mysqli_fetch_array($result3))
{
    //----------------------------------------    
    $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, $row['ficha']);
    $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, $row['proyecto']);
    $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, $row['cedula']);
    $objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, $row['apellidos']." ".$row['nombres']);
    $objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, $row['montos']);
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
    $hasta    = "F".$rowCount;
    $objPHPExcel->getActiveSheet()->getStyle($desde.':'.$hasta)->applyFromArray($borders);
    $rowCount++;
    $count++;
}
//------------------------------------------------------------
$final = $rowCount-1;
$titulo= "Suntrac ".$planillas[mes_sipe]."_".$planillas[anio_sipe];
$objPHPExcel->getActiveSheet()->getStyle('B'.$rowCount.':'.'D'.$rowCount)->applyFromArray($borders_negrita);

$objPHPExcel->getActiveSheet()->mergeCells('B'.$rowCount.':'.'D'.$rowCount);
$objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount,$count);
$objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, '=SUM(F2:F'.$final.')');
$objPHPExcel->getActiveSheet()->setTitle();
$nombre_archivo = "Reporte_Suntrac_".$planillas[mes_sipe]."_".$planillas[anio_sipe].".xlsx";
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename='.$nombre_archivo);
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
?>