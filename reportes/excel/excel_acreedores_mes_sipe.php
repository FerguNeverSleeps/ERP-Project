<?php
session_start();
include("../../nomina/lib/common.php");
require_once('../../nomina/lib/database.php');
ini_set("memory_limit", "-1");
$db            = new Database($_SESSION['bd']);
require_once '../../includes/phpexcel/Classes/PHPExcel.php';
$objPHPExcel   = new PHPExcel();
$objPHPExcel   = PHPExcel_IOFactory::load("plantillas/plantilla_blanco.xlsx");
//------------------------------------------------------------
$mes           = (empty($_GET['mes'])) ? 0:   $_GET['mes'];
$tipos        = (empty($_GET['tipos'])) ? 0: $_GET['tipos'];
$anio          = (empty($_GET['anio'])) ? 0:  $_GET['anio'];

$tipos = explode('_', $tipos);
$codnom = $tipos[0];
$tipnom = $tipos[1];
$i             = 0;
$meses1     = array('ENERO','FEBRERO','MARZO','ABRIL','MAYO','JUNIO','JULIO','AGOSTO','SEPTIEMBRE','OCTUBRE','NOVIEMBRE','DICIEMBRE');
$mes_letras = $meses1[$mes - 1];
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
        'name'  => 'Arial'
    )
);
$objPHPExcel->getActiveSheet()->setCellValue('B1', 'INGENIERÍA TÉCNICA ESPECIALIZADA S.A.'. $mes_letras .' '. $anio );
$objPHPExcel->getActiveSheet()->mergeCells('B1:E1');
$objPHPExcel->getActiveSheet()->SetCellValue('F1', "Usuario: ".$_SESSION['nombre']);
$objPHPExcel->getActiveSheet()->SetCellValue('G1', "Fecha: ".date('d-m-Y'));
$objPHPExcel->getActiveSheet()->getStyle('B1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('B1')->applyFromArray($borders);
$objPHPExcel->getActiveSheet()->getStyle('G1')->applyFromArray($borders);

//Encabezado de la tabla
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(16);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(21);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(21);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(22);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);

$objPHPExcel->getActiveSheet()->getStyle('A2:G2')->applyFromArray($borders);
$objPHPExcel->getActiveSheet()->SetCellValue('A2', "CÉDULA");
$objPHPExcel->getActiveSheet()->SetCellValue('B2', "PRIMER NOMBRE");
$objPHPExcel->getActiveSheet()->SetCellValue('C2', "PRIMER APELLIDO");
$objPHPExcel->getActiveSheet()->SetCellValue('D2', "FECHA NACI.");
$objPHPExcel->getActiveSheet()->SetCellValue('E2', "EDAD");
$objPHPExcel->getActiveSheet()->SetCellValue('F2', "SEXO");
$objPHPExcel->getActiveSheet()->SetCellValue('G2', "ESTADO CIVIL");
/*$objPHPExcel->getActiveSheet()->SetCellValue('L2', "Deducc.");*/
//Fin encabezado tabla
$rowCount = 3;
$count=0;
/* Se buscan los campos que tuvieron movimientos en planilla */
$db            = new Database($_SESSION['bd']);

$sql_ = "SELECT nmn.codcon, nmn.descrip, nmn.ficha
from nom_movimientos_nomina as nmn
left join nom_nominas_pago as nnp on (nmn.codnom = nnp.codnom and nmn.tipnom=nnp.tipnom)
where nnp.mes_sipe = '{$mes}' and nnp.anio_sipe = '{$anio}' and nmn.codnom = '{$codnom}' and nmn.tipnom = '{$tipnom}'
AND nmn.tipcon = 'D'
AND nmn.codcon > 500 
order by codcon";
$result2 = $db->query($sql_);
$temp = "";
while($row = $result2->fetch_assoc())
{
    if($temp != $row[codcon])
    {
        $count++;
        $objPHPExcel->getActiveSheet()->getStyle('A'.$rowCount.':G'.$rowCount)->applyFromArray($borders);
        $objPHPExcel->getActiveSheet()->mergeCells('A'.$rowCount.':G'.$rowCount);
        $objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, $row[descrip]);

        $temp = $row[codcon];
        $rowCount++;
        $count++;

    }
    $sql_empleado = "SELECT nombres, apellidos, cedula, fecnac, sexo, estado_civil from nompersonal where ficha = '{$row[ficha]}'";
    $resultado = $db->query($sql_empleado);
    $assoc = $resultado->fetch_assoc();
    //----------------------------------------    
	$fonts = array(
	    'borders' => array(
	        'allborders' => array(
	          'style' => PHPExcel_Style_Border::BORDER_THIN,
	          'color' => array('argb' => 'FF000000')
	    	)
	    ),
	    'font'  => array(
	        'bold'  => false,
	        'color' => array('rgb' => '000000'),
	        'size'  => 10,
	        'name'  => 'Arial',
	        'center' => true
	    )
	);

    $fecnac = new DateTime($assoc['fecnac']);
    $hoy = new DateTime();
    $annos = $hoy->diff($fecnac);
    if($assoc[sexo] == "Femenino")
    {
        $sexo = "F";
    }
    else
    {
        $sexo = "M";
    }
	$objPHPExcel->getActiveSheet()->getStyle('A'.$rowCount.':G'.$rowCount)->applyFromArray($fonts);
    $objPHPExcel->getActiveSheet()->getStyle('F'.$rowCount)->getNumberFormat()->setFormatCode("#,##0.00");
	$objPHPExcel->getActiveSheet()->getStyle('G'.$rowCount)->getNumberFormat()->setFormatCode("#,##0.00");/*
	$objPHPExcel->getActiveSheet()->getStyle('H'.$rowCount)->getNumberFormat()->setFormatCode("#,##0.00");
    $objPHPExcel->getActiveSheet()->getStyle('I'.$rowCount)->getNumberFormat()->setFormatCode("#,##0.00");*/
    /* Se muestran los datos del funcionario/colaborador además de los conceptos devengados que están almacenados en planilla */
    $objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, $assoc['cedula']);
    $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, $assoc['nombres']);
    $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, $assoc['apellidos']);
    $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, $assoc['fecnac']);
    $objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, $annos->y);
    $objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, $sexo);
    $objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowCount, $assoc['estado_civil']);


    $borders2 = array(
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
    $objPHPExcel->getActiveSheet()->getStyle($desde.':'.$hasta)->applyFromArray($borders2);
    $rowCount++;
    $count++;

}

$final = $rowCount-1;
/* Se muestra la sumatoria de los conceptos que está almacenados en movimientos de planilla 
$objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount,'Total:');
$objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, '=SUM(F2:F'.$final.')');
$objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowCount, '=SUM(G2:G'.$final.')');*/
$objPHPExcel->getActiveSheet()->setTitle('Listado Acreedores'.$mes_letras.' '.$anio);
header('Content-Type: application/vnd.openxmlformats-   officedocument.spreadsheetml.sheet');
$nombre_archivo = "listado_acreedores_".$mes_letras."_".$anio.".xlsx";
header('Content-Disposition: attachment;filename="'.$nombre_archivo.'"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
?>