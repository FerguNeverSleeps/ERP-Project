<?php
session_start();
include("../../nomina/lib/common.php");
require_once('../../nomina/lib/database.php');
ini_set("memory_limit", "-1");
$db            = new Database($_SESSION['bd']);
require_once '../../includes/phpexcel/Classes/PHPExcel.php';
$codtip        = $_SESSION['codigo_nomina'];
$objPHPExcel   = new PHPExcel();
$objPHPExcel   = PHPExcel_IOFactory::load("plantillas/siacap_sanmiguelito.xlsx");
//------------------------------------------------------------
$mes           = (empty($_GET['mes'])) ? 0:   $_GET['mes'];
$meses         = (empty($_GET['meses'])) ? 0: $_GET['meses'];
$anio          = (empty($_GET['anio'])) ? 0:  $_GET['anio'];

$i             = 0;
$patronal      = "878100038";
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
        'size'  => 10,
        'name'  => 'Arial'
    )
);
$objPHPExcel->getActiveSheet()->setCellValue('B1', 'SIACAP MES DE '. $mes_letras .' '. $anio );
$objPHPExcel->getActiveSheet()->mergeCells('B1:I1');
$objPHPExcel->getActiveSheet()->SetCellValue('J1', "Usuario: ".$_SESSION['nombre']);
$objPHPExcel->getActiveSheet()->SetCellValue('K1', "Fecha: ".date('d-m-Y'));
$objPHPExcel->getActiveSheet()->getStyle('B1')->applyFromArray($borders);
$objPHPExcel->getActiveSheet()->getStyle('B1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$objPHPExcel->getActiveSheet()->getStyle('B1')->applyFromArray($borders);
$objPHPExcel->getActiveSheet()->getStyle('J1')->applyFromArray($borders);
$objPHPExcel->getActiveSheet()->getStyle('K1')->applyFromArray($borders);
//Encabezado de la tabla
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(14);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(16);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(30);

$objPHPExcel->getActiveSheet()->getStyle('A2:T2')->applyFromArray($borders);
$objPHPExcel->getActiveSheet()->SetCellValue('A2', "Nº EMPLEADO");
$objPHPExcel->getActiveSheet()->SetCellValue('B2', "Nº CÉDULA");
$objPHPExcel->getActiveSheet()->SetCellValue('C2', "SEGURO SOCIAL");
$objPHPExcel->getActiveSheet()->SetCellValue('D2', "PRIMER NOMBRE");
$objPHPExcel->getActiveSheet()->SetCellValue('E2', "PRIMER APELLIDO");
$objPHPExcel->getActiveSheet()->SetCellValue('F2', "SEGUNDO APELLIDO");
$objPHPExcel->getActiveSheet()->SetCellValue('G2', "APELLIDO CASADA");
$objPHPExcel->getActiveSheet()->SetCellValue('H2', "Nº PATRONAL");
$objPHPExcel->getActiveSheet()->SetCellValue('I2', "I QUINCENA");
$objPHPExcel->getActiveSheet()->SetCellValue('J2', "II QUINCENA");
$objPHPExcel->getActiveSheet()->SetCellValue('K2', "SALARIO MENSUAL");
$objPHPExcel->getActiveSheet()->SetCellValue('L2', "APORTE 2%");
$objPHPExcel->getActiveSheet()->SetCellValue('M2', "APORTE 0.3%");
$objPHPExcel->getActiveSheet()->SetCellValue('N2', "APORTE EXTRA");
$objPHPExcel->getActiveSheet()->SetCellValue('O2', "RECARGO DEL 1%");
$objPHPExcel->getActiveSheet()->SetCellValue('P2', "RECARGO DEL 10%");
$objPHPExcel->getActiveSheet()->SetCellValue('Q2', "MES Y AÑO QUE REPORTA");
$objPHPExcel->getActiveSheet()->SetCellValue('R2', "OBSERVACIONES");
$objPHPExcel->getActiveSheet()->SetCellValue('S2', "PROVINCIA");
$objPHPExcel->getActiveSheet()->SetCellValue('T2', "UNIDAD ADMINISTRATIVA");
/*$objPHPExcel->getActiveSheet()->SetCellValue('L2', "Deducc.");*/
//Fin encabezado tabla
$rowCount = 3;
$count=0;
/* Se buscan los campos que tuvieron movimientos en planilla */
$db            = new Database($_SESSION['bd']);

$sql_     = "SELECT a.ficha,c.codorg,c.descrip,a.tipnom,np.cedula,np.cedula_corregida, np.apenom, np.nomposicion_id,np.nombres,np.apellidos,ntn.descrip as planillanom,
SUM( IF(a.codcon in (204,210), ROUND(a.monto,2),0) ) AS siacap
FROM nom_movimientos_nomina AS a 
LEFT JOIN nomnivel1 AS c ON a.codnivel1 = c.codorg 
LEFT JOIN nom_nominas_pago AS nnp ON (a.codnom=nnp.codnom AND a.tipnom=nnp.tipnom)
INNER JOIN nompersonal AS np ON (a.cedula= np.cedula)
INNER JOIN nomtipos_nomina AS ntn ON (a.tipnom=ntn.codtip)
WHERE a.mes = '{$mes}' AND a.anio = '{$anio}' AND a.codcon IN (204,210) 
AND a.tipnom IN (1,2,3,5) 
AND nnp.frecuencia in (2,3)
GROUP BY a.ficha
ORDER BY a.tipnom,a.codnivel1,np.nomposicion_id ASC;
";
$result2 = $db->query($sql_);

while($row = $result2->fetch_assoc())
{
    $ficha          = $row['ficha'];
    $cedula         = $row['cedula'];
    $nomposicion_id = $row['nomposicion_id'];
    $sueldo         = $row['suesal'];    
    $conex          = new Database($_SESSION['bd']);

    $select_per     = "SELECT 
    SUM( IF(a.codcon = 100 AND nnp.frecuencia =2, ROUND(a.monto,2),0) ) AS salario1,
    SUM( IF(a.codcon in (150,115,117,118,119) AND nnp.frecuencia =2, ROUND(a.monto,2),0) ) AS lic1,
    SUM( IF(a.codcon = 100 AND nnp.frecuencia =3, ROUND(a.monto,2),0) ) AS salario2,
    SUM( IF(a.codcon in (150,115,117,118,119) AND nnp.frecuencia =3, ROUND(a.monto,2),0) ) AS lic2
    FROM nom_movimientos_nomina AS a 
    LEFT JOIN nomnivel1 AS c ON a.codnivel1 = c.codorg 
    LEFT JOIN nom_nominas_pago AS nnp ON (a.codnom=nnp.codnom AND a.tipnom=nnp.tipnom)
    INNER JOIN nompersonal AS np ON (a.cedula= np.cedula)
    WHERE a.mes = '{$mes}' AND a.anio = '{$anio}' AND a.codcon IN (100,150,115,117,118,119) 
    AND a.tipnom IN (1,2,3,5) 
    AND nnp.frecuencia in (2,3) AND np.cedula LIKE '%".$cedula."%'";
    $res            = $conex->query($select_per);
    $row2           = $res->fetch_array();
    $quincen1       = $row2['salario1'] - $row2['lic1'];
    $quincen2       = $row2['salario2'] - $row2['lic2'];
    $total_quincena = $quincen1 + $quincen2;
    $siacap_excel   = round(($total_quincena * 0.02),2);
    $fonts          = array(
    'borders'       => array(
            'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN,'color' => array('argb' => 'FF000000'))
        ),
        'font'  => array('bold'  => false,'color' => array('rgb' => '000000'),'size'  => 10,'name'  => 'Arial','center' => true)
    );
	$objPHPExcel->getActiveSheet()->getStyle('A'.$rowCount.':K'.$rowCount)->applyFromArray($fonts);
    $objPHPExcel->getActiveSheet()->getStyle('F'.$rowCount)->getNumberFormat()->setFormatCode("#,##0.00");
	$objPHPExcel->getActiveSheet()->getStyle('G'.$rowCount)->getNumberFormat()->setFormatCode("#,##0.00");/*
	$objPHPExcel->getActiveSheet()->getStyle('H'.$rowCount)->getNumberFormat()->setFormatCode("#,##0.00");
    $objPHPExcel->getActiveSheet()->getStyle('I'.$rowCount)->getNumberFormat()->setFormatCode("#,##0.00");*/
    /* Se muestran los datos del funcionario/colaborador además de los conceptos devengados que están almacenados en planilla */
    $objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, $row['nomposicion_id']);
    $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, $row['cedula_corregida']);
    $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, $row['cedula_corregida']);
    $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, $row['nombres']);
    $objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, $row['apellidos']);
    $objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, $row['nombres2']);
    $objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowCount, $row['nombres2']);
    $objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowCount, $patronal);
    $objPHPExcel->getActiveSheet()->SetCellValue('I'.$rowCount, $quincen1);
    $objPHPExcel->getActiveSheet()->SetCellValue('J'.$rowCount, $quincen2);
    $objPHPExcel->getActiveSheet()->SetCellValue('K'.$rowCount, $total_quincena);
    $objPHPExcel->getActiveSheet()->SetCellValue('L'.$rowCount, $row['siacap']);
    $objPHPExcel->getActiveSheet()->SetCellValue('M'.$rowCount, '=K'.$rowCount.'*0.003');
    $objPHPExcel->getActiveSheet()->SetCellValue('N'.$rowCount, '0');
    $objPHPExcel->getActiveSheet()->SetCellValue('O'.$rowCount, '0');
    $objPHPExcel->getActiveSheet()->SetCellValue('P'.$rowCount, '0');
    $objPHPExcel->getActiveSheet()->SetCellValue('Q'.$rowCount, "31/".$mes."/".$anio);
    $objPHPExcel->getActiveSheet()->SetCellValue('R'.$rowCount, "Afiliado Recurrente");
    $objPHPExcel->getActiveSheet()->SetCellValue('S'.$rowCount, "8");
    $objPHPExcel->getActiveSheet()->SetCellValue('T'.$rowCount, $row['descrip']);


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
    $hasta = "T".$rowCount;
    $objPHPExcel->getActiveSheet()->getStyle($desde.':'.$hasta)->applyFromArray($borders);
    $rowCount++;
    $count++;

}

$final = $rowCount-1;
/* Se muestra la sumatoria de los conceptos que está almacenados en movimientos de planilla */
$objPHPExcel->getActiveSheet()->getStyle('B'.$rowCount.':K'.$rowCount)->getNumberFormat()->setFormatCode("#,##0.00");

$objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowCount,'Total:');
$objPHPExcel->getActiveSheet()->SetCellValue('I'.$rowCount, '=SUM(I2:I'.$final.')');
$objPHPExcel->getActiveSheet()->SetCellValue('J'.$rowCount, '=SUM(J2:J'.$final.')');
$objPHPExcel->getActiveSheet()->SetCellValue('K'.$rowCount, '=SUM(K2:K'.$final.')');
$objPHPExcel->getActiveSheet()->setTitle('Listado Siacap');

header('Content-Type: application/vnd.openxmlformats-   officedocument.spreadsheetml.sheet');
$nombre_archivo = "listado_siacap_".$mes_letras."_".$anio.".xlsx";
header('Content-Disposition: attachment;filename="'.$nombre_archivo.'"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
?>