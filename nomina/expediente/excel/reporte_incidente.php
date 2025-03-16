<?php
if (!isset($_SESSION)) {
  session_start();
  ob_start();
}
include("../../../nomina/lib/common.php");
require_once('../../../nomina/lib/database.php');
$db          = new Database($_SESSION['bd']);
require_once '../../../includes/phpexcel/Classes/PHPExcel.php';

error_reporting(E_ALL);
    function formato_fecha($fecha,$formato = 3)
    {
	    $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
        $separa = explode("-",$fecha);
        $dia = $separa[2];
	    $mes = $separa[1];
	    $anio = $separa[0];
	    switch ($formato)
	    {
	        case 1:
	            $f = $dia." de ".$meses[$mes-1]." de ".$anio;
	            break;
	        case 2:
	            $f = $dia." del mes ".$meses[$mes-1]." de ".$anio;
	            break;
            case 3:
                $f=$dia."-".$mes."-".$anio;
	            break;
	    }
	    return $f;
    }
    


$ced      = (isset($_GET['cedula'])) ? $_GET['cedula'] : '' ;
$codigo      = (isset($_GET['codigo'])) ? $_GET['codigo'] : '' ;
//------------------------------------------------------------

$objPHPExcel = new PHPExcel();
$objPHPExcel = PHPExcel_IOFactory::load("plantillas/reporte_incidente.xlsx");


$sql = "SELECT a.*,b.codorg,b.descrip 
        FROM   nompersonal a 
        LEFT JOIN nomnivel1 b ON a.codnivel1=b.codorg
        WHERE  a.cedula='{$ced}'";
$res1 = $db->query($sql);
$persona = $res1->fetch_object();

$nombre_apellido  = strtoupper($persona ->apenom);
$cedula        = $persona ->cedula;
$sucursal  = strtoupper($persona ->descrip);
$ficha  = $persona ->ficha;

$sql2   = "SELECT a.* FROM expediente a WHERE a.cod_expediente_det='".$codigo."'";
$res2 = $db->query($sql2);
$expediente = $res2->fetch_object();
$fecha_resolucion = formato_fecha($expediente ->fecha_resolucion,3);
$fecha_inicio = formato_fecha($expediente ->fecha_inicio,3);
//$fecha_hora_inicio = formato_fecha($expediente ->fecha_hora_inicio,3);
$fecha_hora_inicio =$expediente ->fecha_hora_inicio;
$fecha = formato_fecha($expediente ->fecha,3);
$conce = $expediente ->numero_resolucion;

$descripcion  = $expediente ->descripcion ;
$cargo_estructura  = strtoupper($expediente ->cargo_estructura) ;
$monto  = $expediente ->monto ;
$monto_nuevo  = $expediente ->monto_nuevo ;
$comentarios_1  = $expediente ->comentarios_1 ;
$comentarios_2  = $expediente ->comentarios_2 ;
$analista  = $expediente ->analista ;
$concepto  = $expediente ->concepto ;
$n="Nº";
//--------------------------------------------------------------
//titulo de la tabla

$objPHPExcel->getActiveSheet()->SetCellValue('H1', $n);
$objPHPExcel->getActiveSheet()->SetCellValue('H2',$conce);
//$objPHPExcel->getActiveSheet()->SetCellValue('H3',"sdad");
$objPHPExcel->getActiveSheet()->SetCellValue('B4', $nombre_apellido);
$objPHPExcel->getActiveSheet()->SetCellValue('G4', $ficha);
$objPHPExcel->getActiveSheet()->SetCellValue('B5', $cargo_estructura);
$objPHPExcel->getActiveSheet()->SetCellValue('G5', formato_fecha($fecha,1));
$objPHPExcel->getActiveSheet()->SetCellValue('C8', formato_fecha($fecha_inicio,1));
$objPHPExcel->getActiveSheet()->SetCellValue('H8', $fecha_hora_inicio);
$objPHPExcel->getActiveSheet()->SetCellValue('C9', $descripcion );
$objPHPExcel->getActiveSheet()->SetCellValue('G9', $monto);
$objPHPExcel->getActiveSheet()->SetCellValue('B10', $monto_nuevo);
$objPHPExcel->getActiveSheet()->SetCellValue('A12', ($comentarios_1));
$objPHPExcel->getActiveSheet()->SetCellValue('A16', ($comentarios_2));
$objPHPExcel->getActiveSheet()->SetCellValue('A19', "Recursos Humanos aprueba: ".$analista);
$objPHPExcel->getActiveSheet()->SetCellValue('A20', "Tipo de Amonestación: ".$concepto);
$objPHPExcel->getActiveSheet()->getStyle('A12')->getAlignment()->setWrapText(true);
$objPHPExcel->getActiveSheet()->setTitle('REPORTE DE INCIDENTE');
// clean the output buffer
ob_clean();
header('Content-Type: application/vnd.openxmlformats-   officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="reporte_incidente.xlsx"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
?>