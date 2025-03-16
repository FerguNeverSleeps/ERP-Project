<?php
require_once("../../nomina/lib/common.php");
require_once('../../nomina/lib/database.php');
require_once('../utilidades/funciones_reportes.php');
require_once '../../includes/phpexcel/Classes/PHPExcel.php';
$db = new Database($_SESSION['bd']);

//------------------------------------------------------------
function fechas($fecha,$formato)
{
    $meses = array("ENERO","FEBRERO","MARZO","ABRIL","MAYO","JUNIO","JULIO","AGOSTO","SEPTIEMBRE","OCTUBRE","NOVIEMBRE","DICIEMBRE");
    if (strlen($fecha)<2) {
        return "";
    }
    $separa = explode("-",$fecha);
    $anio = $separa[0];
    $mes = $separa[1];
    $dia = $separa[2];
    switch ($formato)
    {
        case 1:
            // ejemplo = DEL 01 DE ENERO DE 2016
            $f = 'DEL '.$dia." DE ".$meses[$mes-1]. " DE ".$anio ;
            break;
        case 2:
            // ejemplo = DEL 01 DE ENERO DE 2016
                $f = $dia." DIAS DEL MES DE ".$meses[$mes-1]. " DE ".$anio ;            
            break;
         case 3:
            // ejemplo = DEL 01 DE ENERO DE 2016
                $f = $dia."-".$meses[$mes-1]. "-".$anio ;            
            break;
        default:
            $f = date('Y');
            break;
    }
    return $f;
}
//------------------------------------------------------------
$objPHPExcel = new PHPExcel();
$objPHPExcel = PHPExcel_IOFactory::load("plantillas/funcionarios_situacion.xlsx");
//------------------------------------------------------------
$sql     = "SELECT * FROM nomempresa";
$res     = $db->query($sql, $conexion);
$empresa = mysqli_fetch_array($res);
//------------------------------------------------------------
$sql     = "SELECT * FROM nomnivel1 WHERE codorg = '".$_SESSION['region']."'";
$res     = $db->query($sql, $conexion);
$ger     = mysqli_fetch_array($res);
//------------------------------------------------------------
$sql     = "SELECT * FROM nomnivel2 WHERE codorg = '".$_SESSION['departamento']."'";
$res     = $db->query($sql, $conexion);
$dpto    = mysqli_fetch_array($res);
//------------------------------------------------------------
$sql     = "SELECT a.*,DATE_FORMAT(a.fecnac, '%d-%m-%Y') as fecnac,DATE_FORMAT(a.fecha_permanencia, '%d-%m-%Y') as fecha_permanencia,b.descrip AS gerencia,c.descrip AS departamento, d.des_car AS cargo, e.descripcion_funcion as funcion
            FROM nompersonal AS a
            LEFT JOIN nomnivel1 AS b ON a.codnivel1 = b.codorg
            LEFT JOIN nomnivel2 AS c ON a.codnivel2 = c.codorg
            LEFT JOIN nomcargos AS d ON a.codcargo  = d.cod_car
            LEFT JOIN nomfuncion AS e ON a.nomfuncion_id = e.nomfuncion_id
            WHERE estado = '".$_POST['situacion']."'";
$res     = $db->query($sql, $conexion);
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
$objPHPExcel->getActiveSheet()->SetCellValue('C1', $empresa['nom_emp']);
$objPHPExcel->getActiveSheet()->SetCellValue('C2', "DIRECCION DE RECURSOS HUMANOS");
$objPHPExcel->getActiveSheet()->SetCellValue('C3', "FUNCIONARIOS CON ESTADO: ".$_POST['situacion']." - FECHA: ".date('d-m-Y'));
//Fin encabezado tabla
$rowCount = 5;
$i=1;
while($datos = mysqli_fetch_array($res))
{
    $objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, $i);
    $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, $datos['gerencia'].((empty($datos['dpto']))?"":" / ".$datos['dpto']) );
    $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, $datos['apenom']);
    $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, $datos['cedula']);
    $objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, $datos['seguro_social']);
    $objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, $datos['tipnom']);
    $objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowCount, $datos['nomposicion_id']);
    $objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowCount, $datos['funcion']);
    $objPHPExcel->getActiveSheet()->SetCellValue('I'.$rowCount, $datos['suesal']);
    $objPHPExcel->getActiveSheet()->SetCellValue('J'.$rowCount, fechas($datos['fecha_permanencia'],3));
    $objPHPExcel->getActiveSheet()->SetCellValue('K'.$rowCount, fechas($datos['fecnac'],3));
    
    $objPHPExcel->getActiveSheet()->getStyle("A".$rowCount.':'."K".$rowCount)->applyFromArray($borders1);
    $rowCount++;
    $i++;
}
$objPHPExcel->getActiveSheet()->setTitle($_POST['situacion']);

header('Content-Type: application/vnd.openxmlformats-   officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="funcionarios_situacion.xlsx"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
?>