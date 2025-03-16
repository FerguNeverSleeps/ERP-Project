<?php

/** Error reporting */
// error_reporting(E_ALL);
// ini_set('display_errors', TRUE);
// ini_set('display_startup_errors', TRUE);

include("../../nomina/lib/common.php");
require_once('../../nomina/lib/database.php');
$db          = new Database($_SESSION['bd']);
require_once '../../includes/phpexcel/Classes/PHPExcel.php';
// Esto para las imagenes
require_once '../../includes/phpexcel/Classes/PHPExcel/Writer/Excel2007.php';
/*Se reciben los parámetros*/
function ConvertirFechaYMD($fecha)
{
    return date("Y-m-d",strtotime($fecha));
}
$reg         = $_GET["reg"];

$codnom     = $_GET['nomina_id'];
$tipnom     = $_GET['codt'];
$mes        = $_GET['mes'];
$anio       = $_GET['anio'];
$chequera   = $_GET['codigo'];
$ficha      = $_GET['ficha'];
$cedula     = $_GET['cedula'];
$quincena   = $_GET['quincena'];
$inicio     = $_GET["fecha_inicio"];
$fin        = $_GET["fecha_fin"];

/*Se inicializa la plantilla de excel*/
// $objPHPExcel = new PHPExcel();
$objPHPExcel = PHPExcel_IOFactory::load("plantillas/detalle_salarios_acumulados.xlsx");

$sql_empresa = "SELECT * FROM nomempresa";
$empresa = $db->query($sql_empresa)->fetch_object();

$sql_empleado = "SELECT * FROM nompersonal WHERE cedula = '{$cedula}'";
$empleado = $db->query($sql_empleado)->fetch_object();

$objPHPExcel->getActiveSheet()->SetCellValue('D6', date("d-m-Y") );
$objPHPExcel->getActiveSheet()->SetCellValue('D1', $empresa->nom_emp );
$objPHPExcel->getActiveSheet()->SetCellValue('D2', "INFORME DE TRANSACCIONES POR EMPLEADO" );
$objPHPExcel->getActiveSheet()->SetCellValue('D3', "DESDE EL EMPLEADO: ".$empleado->ficha." HASTA EL EMPLEADO: ".$empleado->ficha );
$objPHPExcel->getActiveSheet()->SetCellValue('D4', "DESDE LA FECHA: ".date('d-m-Y',strtotime($inicio))." HASTA: ".date('d-m-Y',strtotime($fin)) );

$objPHPExcel->getActiveSheet()->SetCellValue('A6', "Empleado: ".$empleado->ficha." ".$empleado->apenom );
$objPHPExcel->getActiveSheet()->SetCellValue('V6', "Clave ISR: ".$empleado->clave_ir );

$baseDirectory = $_SERVER['DOCUMENT_ROOT'] . "/includes/imagenes/";

if (!empty($empresa->imagen_der)) {
    $safeImageName = basename($empresa->imagen_der);

    $path = $baseDirectory . $safeImageName;

    if (!file_exists($path)) {
        $path = $baseDirectory . "logo_amx.png";
    }
} else {
    $path = $baseDirectory . "logo_amx.png";
}

// ruta normal de una imagen ../../includes/imagenes/verde.png
// $path = $_SERVER['DOCUMENT_ROOT'] . "\\includes\\imagenes\\" . $empresa->imagen_der;

// Create new picture object and insert picture
$objDrawing = new PHPExcel_Worksheet_Drawing();
$objDrawing->setName('Imagen de empresa');
$objDrawing->setDescription('Imagen de empresa');
$objDrawing->setPath($path);
$objDrawing->setCoordinates('A1');                      
//setOffsetX works properly
$objDrawing->setOffsetX(5); 
$objDrawing->setOffsetY(5);                
//set width, height
$objDrawing->setWidth(400); 
$objDrawing->setHeight(90); 
$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());

// header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
// // /*Nombre del archivo*/
// $nombre_archivo = "DETALLE_SALARIOS_ACUMULADOS_".$mes."-".$anio .".xlsx";
// header('Content-Disposition: attachment;filename='.$nombre_archivo);
// header('Cache-Control: max-age=0');

// // Guardar el archivo de Excel
// $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

// ob_clean();
// $objWriter->save('php://output');

$sql= "SELECT a.*, b.personal_id, b.cedula, b.ficha, b.apenom,c.codnom, c.descrip as planilla ,e.descrip desc_frec_planilla "
        . " FROM salarios_acumulados as a"
        . " LEFT JOIN nompersonal as b ON (a.cedula=b.cedula AND a.ficha=b.ficha)"
        . " LEFT JOIN nom_nominas_pago as c ON (a.cod_planilla=c.codnom AND a.tipo_planilla=c.tipnom)"
        . " LEFT JOIN nomtipos_nomina as d ON (a.tipo_planilla=d.codtip)"
        . " LEFT JOIN nomfrecuencias as e ON (a.frecuencia_planilla=e.codfre)"

        . " WHERE a.cedula='{$cedula}' AND a.ficha='{$ficha}' "
        . " AND  a.fecha_pago between '".ConvertirFechaYMD($inicio)."' AND '".ConvertirFechaYMD($fin)."'"
        . " ORDER BY a.fecha_pago ASC ";
$res_sal   = $db->query($sql);
$i=10;

while($row  = $res_sal->fetch_assoc())
{
    $subtotal               = 0;
    $total_salario         += $row['salario_bruto'];
    $total_vacaciones      += $row['vacac'];
    $total_xiii            += $row['xiii'];
    $total_gtorep          += $row['gtorep'];
    $total_xiigtorep       += $row['xiii_gtorep'];
    $total_liquida         += $row['liquida'];
    $total_bono            += $row['bono'];
    $total_otros_ing       += $row['otros_ing'];
    $total_prima           += $row['prima'];
    $total_viaticos        += $row['viaticos'];
    $total_gratificaciones += $row['gratificaciones'];
    $total_donaciones      += $row['donaciones'];
    $total_comision        += $row['comision'];
    $total_adelanto        += $row['x'];
    $total_s_s             += $row['s_s'];
    $total_s_e             += $row['s_e'];
    $total_islr            += $row['islr'];
    $total_islr_gr         += $row['islr_gr'];
    $total_acreedor_suma   += $row['acreedor_suma'];
    $total_desc_empresa    += $row['desc_empresa'];
    $total_Neto            += $row['Neto'];

    $subtotal += $row['salario_bruto']+$row['comision']+$row['sobretiempo']+$row['bono']+$row['altura']+$row['otros']+$row['vacac']+$row['xiii']+$row['gtorep'];
    
    $codnom = (!is_null($row['codnom']) ) ? $row['codnom'] : '' ;

    $objPHPExcel->getActiveSheet()->SetCellValue('A'.$i, date('d-m-Y',strtotime($row['fecha_pago'])) );
    $objPHPExcel->getActiveSheet()->SetCellValue('B'.$i, $codnom );
    $objPHPExcel->getActiveSheet()->SetCellValue('C'.$i, $row['tipo_planilla'] );
    $objPHPExcel->getActiveSheet()->SetCellValue('D'.$i, $row['desc_frec_planilla'] );
    $objPHPExcel->getActiveSheet()->SetCellValue('E'.$i, $row['salario_bruto'] );
    $objPHPExcel->getActiveSheet()->SetCellValue('F'.$i, $row['vacac'] );
    $objPHPExcel->getActiveSheet()->SetCellValue('G'.$i, $row['xiii'] );
    $objPHPExcel->getActiveSheet()->SetCellValue('H'.$i, $row['gtorep'] );
    $objPHPExcel->getActiveSheet()->SetCellValue('I'.$i, $row['xiii_gtorep'] );
    $objPHPExcel->getActiveSheet()->SetCellValue('J'.$i, $row['liquida'] );
    $objPHPExcel->getActiveSheet()->SetCellValue('K'.$i, $row['viaticos'] );
    // $objPHPExcel->getActiveSheet()->SetCellValue('L'.$i, $row['comision'] );
    $objPHPExcel->getActiveSheet()->SetCellValue('M'.$i, $row['gratificaciones'] );
    $objPHPExcel->getActiveSheet()->SetCellValue('N'.$i, $row['donaciones'] );
    $objPHPExcel->getActiveSheet()->SetCellValue('O'.$i, $row['bono'] );
    $objPHPExcel->getActiveSheet()->SetCellValue('P'.$i, $row['otros_ing'] );
    $objPHPExcel->getActiveSheet()->SetCellValue('Q'.$i, $row['prima'] );
    //$objPHPExcel->getActiveSheet()->SetCellValue('Q'.$i, $row['x'] );
    $objPHPExcel->getActiveSheet()->SetCellValue('S'.$i, $subtotal );
    //$objPHPExcel->getActiveSheet()->SetCellValue('T'.$i, $row['s_s'] );
    $objPHPExcel->getActiveSheet()->SetCellValue('U'.$i, $row['s_s'] );
    $objPHPExcel->getActiveSheet()->SetCellValue('V'.$i, $row['s_e'] );
    $objPHPExcel->getActiveSheet()->SetCellValue('W'.$i, $row['islr'] );
    $objPHPExcel->getActiveSheet()->SetCellValue('X'.$i, $row['islr_gr'] );
    $objPHPExcel->getActiveSheet()->SetCellValue('Y'.$i, $row['desc_empresa'] );
    $objPHPExcel->getActiveSheet()->SetCellValue('Z'.$i, $row['acreedor_suma'] );
    $objPHPExcel->getActiveSheet()->SetCellValue('AA'.$i, $row['Neto'] );
    $i++;
}
$i++;
//TOTALES
$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':W'.$i)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->SetCellValue('B'.$i, "TOTALES" );
$objPHPExcel->getActiveSheet()->SetCellValue('E'.$i, $total_salario ); //$row['salario_bruto']
$objPHPExcel->getActiveSheet()->SetCellValue('F'.$i, $total_vacaciones ); //$row['comision']
$objPHPExcel->getActiveSheet()->SetCellValue('G'.$i, $total_xiii ); //$row['sobretiempo']
$objPHPExcel->getActiveSheet()->SetCellValue('H'.$i, $total_gtorep ); //$row['bono']
$objPHPExcel->getActiveSheet()->SetCellValue('I'.$i, $total_xiigtorep ); //$row['altura']
$objPHPExcel->getActiveSheet()->SetCellValue('J'.$i, $total_liquida ); //$row['otros']
$objPHPExcel->getActiveSheet()->SetCellValue('K'.$i, $total_bono ); //$row['otros']
// $objPHPExcel->getActiveSheet()->SetCellValue('L'.$i, $total_viaticos ); //$row['otros']
$objPHPExcel->getActiveSheet()->SetCellValue('M'.$i, $total_gratificaciones ); //$row['otros']
$objPHPExcel->getActiveSheet()->SetCellValue('N'.$i, $total_donaciones ); //$row['vacac']
$objPHPExcel->getActiveSheet()->SetCellValue('O'.$i, $total_comision ); //$row['xiii']
$objPHPExcel->getActiveSheet()->SetCellValue('P'.$i, $total_otros_ing ); //$row['util']
$objPHPExcel->getActiveSheet()->SetCellValue('Q'.$i, $total_prima ); //$row['liquida']
$objPHPExcel->getActiveSheet()->SetCellValue('R'.$i, $total_licencia ); //$row['licencia']
//$objPHPExcel->getActiveSheet()->SetCellValue('S'.$i, $subtotal );
//$objPHPExcel->getActiveSheet()->SetCellValu'T'.e($i, $total_gtorep ); //$row['gtorep']
$objPHPExcel->getActiveSheet()->SetCellValue('U'.$i, $total_s_s ); //$row['otros_ing']
$objPHPExcel->getActiveSheet()->SetCellValue('V'.$i, $total_s_e ); //$row['adelanto']
$objPHPExcel->getActiveSheet()->SetCellValue('W'.$i, $total_islr ); //$row['s_s']
$objPHPExcel->getActiveSheet()->SetCellValue('X'.$i, $total_islr_gr ); //$row['s_s']
$objPHPExcel->getActiveSheet()->SetCellValue('Y'.$i, $total_acreedor_suma ); //$row['s_e']
$objPHPExcel->getActiveSheet()->SetCellValue('Z'.$i, $total_desc_empresa ); //$row['islr']
$objPHPExcel->getActiveSheet()->SetCellValue('AA'.$i, $total_Neto ); //$row['Neto']

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
/*Nombre del archivo*/
$nombre_archivo = "DETALLE_SALARIOS_ACUMULADOS_".$mes."-".$anio .".xlsx";
header('Content-Disposition: attachment;filename='.$nombre_archivo);
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
ob_clean();
$objWriter->save('php://output');

 ?>
