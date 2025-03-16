<?php
require_once("../../nomina/lib/common.php");
require_once('../../nomina/lib/database.php');
require_once('../utilidades/funciones_reportes.php');
require_once '../../includes/phpexcel/Classes/PHPExcel.php';
$db = new Database($_SESSION['bd']);
//------------------------------------------------------------
$objPHPExcel = new PHPExcel();
$objPHPExcel = PHPExcel_IOFactory::load("plantillas/resumen_asistencias_individuales.xlsx");
//------------------------------------------------------------
$ficha  = $_GET['ficha'];
$fecini = date("Y-m-d", strtotime($_GET['fecha1']));
$fecfin = date("Y-m-d", strtotime($_GET['fecha2']));
//------------------------------------------------------------
$sql     = "SELECT * FROM nomempresa";
$res     = $db->query($sql);
$empresa = mysqli_fetch_array($res);
//------------------------------------------------------------
$sql     = "SELECT a.*,b.descrip AS gerencia,c.descrip AS dpto FROM nompersonal AS a
            LEFT JOIN nomnivel1 AS b ON a.codnivel1 = b.codorg
            LEFT JOIN nomnivel2 AS c ON a.codnivel2 = c.codorg
            WHERE ficha = '$ficha'";
$res_emp = $db->query($sql);
$emp     = mysqli_fetch_array($res_emp);
$per_dat = utf8_encode($emp['apenom'].", Cedula: ".$emp['cedula'].",  Numero: ".$emp['ficha'].", Gerencia: ".$emp['gerencia'].", Dpto: ".$emp['dpto']);
//------------------------------------------------------------
$sql = "SELECT * FROM nomturnos";
$res = $db->query($sql);
while ($tur = mysqli_fetch_array($res)) {
	$turno[$tur['turno_id']] = $tur['descripcion']." - ".$tur['entrada']." - ".$tur['salida'];
}
//------------------------------------------------------------
$sql     = "SELECT * FROM `caa_resumen` WHERE `ficha` = '$ficha' AND `fecha` BETWEEN '$fecini' AND '$fecfin' ORDER BY `fecha` ASC";
$res_caa = $db->query($sql);
//------------------------------------------------------------
$dias = array('Lun','Mar','Mie','Jue','Vie','Sab','Dom');
//------------------------------------------------------------
//Estilos para filas
$borders1 = array(
    'borders' => array(
        'allborders' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN,
            'color' => array(
                'argb' => 'FF000000'
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
            ),
        )
    ),
    'font'  => array(
    'color' => array('rgb' => 'FF000000'),
    'size'  => 11,
    'name'  => 'Arial'
    )
);
//------------------------------------------------------------
//titulo de la tabla
$objPHPExcel->getActiveSheet()->SetCellValue('B1', $empresa['nom_emp']);
$objPHPExcel->getActiveSheet()->SetCellValue('B2', "DIRECCION DE RECURSOS HUMANOS");
$objPHPExcel->getActiveSheet()->SetCellValue('B3', "DEPARTAMENTO DE REGISTRO Y CONTROL DE RECURSOS HUMANOS");
$objPHPExcel->getActiveSheet()->SetCellValue('B4', $per_dat);
$objPHPExcel->getActiveSheet()->SetCellValue('J1', $_SESSION['nombre']);
$objPHPExcel->getActiveSheet()->SetCellValue('J2', date('d-m-Y'));
$objPHPExcel->getActiveSheet()->SetCellValue('J3', $_GET['fecha1']." al ".$_GET['fecha2']);
//Fin encabezado tabla
$rowCount = 6;
while($datos = mysqli_fetch_array($res_caa))
{
    $objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, $dias[date('N', strtotime($datos['fecha']))-1]." ".$datos['fecha']);
    $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, $datos['entrada']);
    $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, $datos['salida']);
    $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, $datos['tiempo']);
    $objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, $datos['tardanza']);
    $objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, (($datos['ausencia'])?"SI":"") );
    $objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowCount, $datos['h_extra']);

    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('H'.$rowCount.':I'.$rowCount);
    $objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowCount, $turno[$datos['turno_id']]);
    $objPHPExcel->getActiveSheet()->SetCellValue('J'.$rowCount, cargaIncidencias( $datos['ficha'], $datos['fecha'], $db ) );

    $objPHPExcel->getActiveSheet()->getStyle("A".$rowCount.':'."J".$rowCount)->applyFromArray($borders1);
    $rowCount++;
}
$objPHPExcel->getActiveSheet()->setTitle('Asistencias Individuales');

header('Content-Type: application/vnd.openxmlformats-   officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="asistencias_diarias.xlsx"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
?>