<?php
include("../../nomina/lib/common.php");
require_once('../../nomina/lib/database.php');
$db          = new Database($_SESSION['bd']);
require_once '../../includes/phpexcel/Classes/PHPExcel.php';
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
$objPHPExcel = new PHPExcel();
$objPHPExcel = PHPExcel_IOFactory::load("plantillas/reorganizacion_salarios_acumulados.xlsx");

$sql_empresa = "SELECT * FROM nomempresa";
$empresa = $db->query($sql_empresa)->fetch_object();

$sql_empleado = "SELECT * FROM nompersonal WHERE cedula = '{$cedula}'";
$empleado = $db->query($sql_empleado)->fetch_object();

$objPHPExcel->getActiveSheet()->SetCellValue('D3', date("d-m-Y") );
$objPHPExcel->getActiveSheet()->SetCellValue('F2', $empresa->nom_emp );
$objPHPExcel->getActiveSheet()->SetCellValue('F3', "INFORME DE TRANSACCIONES POR EMPLEADO" );
$objPHPExcel->getActiveSheet()->SetCellValue('F4', "DESDE EL EMPLEADO: ".$empleado->ficha." HASTA EL EMPLEADO: ".$empleado->ficha );
$objPHPExcel->getActiveSheet()->SetCellValue('F5', "DESDE LA FECHA: ".date('d-m-Y',strtotime($inicio))." HASTA: ".date('d-m-Y',strtotime($fin)) );

$objPHPExcel->getActiveSheet()->SetCellValue('A6', "Empleado: ".$empleado->ficha." ".$empleado->apenom );
$objPHPExcel->getActiveSheet()->SetCellValue('V6', "Clave ISR: ".$empleado->clave_ir );

$sql= "SELECT a.*, b.personal_id, b.cedula, b.ficha, b.apenom,c.codnom, c.descrip as planilla "
        . " FROM salarios_acumulados as a"
        . " LEFT JOIN nompersonal as b ON (a.cedula=b.cedula)"
        . " LEFT JOIN nom_nominas_pago as c ON (a.cod_planilla=c.codnom AND a.tipo_planilla=c.tipnom)"
        . " LEFT JOIN nomtipos_nomina as d ON (a.tipo_planilla=d.codtip)"
        . " LEFT JOIN nomfrecuencias as e ON (a.frecuencia_planilla=e.codfre)"

        . " WHERE a.cedula='{$cedula}' "
        . " AND  a.fecha_pago between '".ConvertirFechaYMD($inicio)."' AND '".ConvertirFechaYMD($fin)."'"
        . " ORDER BY a.fecha_pago ASC ";
$res_sal   = $db->query($sql);
$i=10;

while($row  = $res_sal->fetch_assoc())
{
    $subtotal            = 0;
    $total_salario       += $row['salario_bruto'];
    $total_comision      += $row['vacac'];
    $total_sobretiempo   += $row['xiii'];
    $total_bono          += $row['gtorep'];
    $total_altura        += $row['xiii_gtorep'];
    $total_otros         += $row['liquida'];
    $total_vacac         += $row['bono'];
    $total_xiii          += $row['otros_ing'];
    $total_util          += $row['prima'];
    $total_liquida       += $row['x'];
    $total_licencia      += $row['x'];
    $total_gtorep        += $row['x'];
    $total_otros_ing     += $row['x'];
    $total_adelanto      += $row['x'];
    $total_s_s           += $row['s_s'];
    $total_s_e           += $row['s_e'];
    $total_islr          += $row['islr'];
    $total_acreedor_suma += $row['acreedor_suma'];
    $total_Neto          += $row['Neto'];

    $subtotal += $row['salario_bruto']+$row['comisiones']+$row['sobretiempo']+$row['bono']+$row['altura']+$row['otros']+$row['vacac']+$row['xiii']+$row['gtorep'];
    
    $codnom = (!is_null($row['codnom']) ) ? $row['codnom'] : '' ;
    
    $objPHPExcel->getActiveSheet()->SetCellValue('A'.$i, date('d-m-Y',strtotime($row['fecha_pago'])) );
    $objPHPExcel->getActiveSheet()->SetCellValue('B'.$i, $codnom );
    $objPHPExcel->getActiveSheet()->SetCellValue('C'.$i, $row['tipo_planilla'] );
    $objPHPExcel->getActiveSheet()->SetCellValue('E'.$i, $row['frecuencia'] );
    $objPHPExcel->getActiveSheet()->SetCellValue('D'.$i, $row['salario_bruto'] );
    $objPHPExcel->getActiveSheet()->SetCellValue('E'.$i, $row['vacac'] );
    $objPHPExcel->getActiveSheet()->SetCellValue('F'.$i, $row['xiii'] );
    $objPHPExcel->getActiveSheet()->SetCellValue('G'.$i, $row['gtorep'] );
    $objPHPExcel->getActiveSheet()->SetCellValue('H'.$i, $row['xiii_gtorep'] );
    $objPHPExcel->getActiveSheet()->SetCellValue('I'.$i, $row['comisiones'] );
    $objPHPExcel->getActiveSheet()->SetCellValue('I'.$i, $row['viaticos'] );
    $objPHPExcel->getActiveSheet()->SetCellValue('J'.$i, $row['gratificaciones'] );
    $objPHPExcel->getActiveSheet()->SetCellValue('K'.$i, $row['otros_ing'] );
    $objPHPExcel->getActiveSheet()->SetCellValue('L'.$i, $row['bono'] );
    $objPHPExcel->getActiveSheet()->SetCellValue('M'.$i, $row['prima'] );
    $objPHPExcel->getActiveSheet()->SetCellValue('N'.$i, $row['otros'] );
    $objPHPExcel->getActiveSheet()->SetCellValue('N'.$i, $row['donaciones'] );
    $objPHPExcel->getActiveSheet()->SetCellValue('N'.$i, $row['s_s'] );
    $objPHPExcel->getActiveSheet()->SetCellValue('Q'.$i, $row['s_e'] );
    $objPHPExcel->getActiveSheet()->SetCellValue('R'.$i, $row['islr'] );
    $objPHPExcel->getActiveSheet()->SetCellValue('S'.$i, $row['islr_gr'] );
    $objPHPExcel->getActiveSheet()->SetCellValue('T'.$i, $row['acreedor_suma'] );
    $objPHPExcel->getActiveSheet()->SetCellValue('U'.$i, $row['des_empresa'] );
    $objPHPExcel->getActiveSheet()->SetCellValue('V'.$i, $row['liquida'] );
    $objPHPExcel->getActiveSheet()->SetCellValue('W'.$i, $row['Neto'] );
    $i++;
}
$i++;
//TOTALES
$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':W'.$i)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->SetCellValue('B'.$i, "TOTALES" );
$objPHPExcel->getActiveSheet()->SetCellValue('D'.$i, $total_salario ); //$row['salario_bruto']
$objPHPExcel->getActiveSheet()->SetCellValue('E'.$i, $total_comision ); //$row['comision']
$objPHPExcel->getActiveSheet()->SetCellValue('F'.$i, $total_sobretiempo ); //$row['sobretiempo']
$objPHPExcel->getActiveSheet()->SetCellValue('G'.$i, $total_bono ); //$row['bono']
$objPHPExcel->getActiveSheet()->SetCellValue('H'.$i, $total_altura ); //$row['altura']
$objPHPExcel->getActiveSheet()->SetCellValue('I'.$i, $total_otros ); //$row['otros']
$objPHPExcel->getActiveSheet()->SetCellValue('J'.$i, $total_vacac ); //$row['vacac']
$objPHPExcel->getActiveSheet()->SetCellValue('K'.$i, $total_xiii ); //$row['xiii']
$objPHPExcel->getActiveSheet()->SetCellValue('L'.$i, $total_util ); //$row['util']
$objPHPExcel->getActiveSheet()->SetCellValue('M'.$i, $total_liquida ); //$row['liquida']
$objPHPExcel->getActiveSheet()->SetCellValue('N'.$i, $total_licencia ); //$row['licencia']
$objPHPExcel->getActiveSheet()->SetCellValue('O'.$i, $subtotal );
$objPHPExcel->getActiveSheet()->SetCellValue('P'.$i, $total_gtorep ); //$row['gtorep']
$objPHPExcel->getActiveSheet()->SetCellValue('Q'.$i, $total_otros_ing ); //$row['otros_ing']
$objPHPExcel->getActiveSheet()->SetCellValue('R'.$i, $total_adelanto ); //$row['adelanto']
$objPHPExcel->getActiveSheet()->SetCellValue('S'.$i, $total_s_s ); //$row['s_s']
$objPHPExcel->getActiveSheet()->SetCellValue('T'.$i, $total_s_e ); //$row['s_e']
$objPHPExcel->getActiveSheet()->SetCellValue('U'.$i, $total_islr ); //$row['islr']
$objPHPExcel->getActiveSheet()->SetCellValue('V'.$i, $total_acreedor_suma ); //$row['acreedor_suma']
$objPHPExcel->getActiveSheet()->SetCellValue('W'.$i, $total_Neto ); //$row['Neto']

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
/*Nombre del archivo*/
$nombre_archivo = "DETALLE_SALARIOS_ACUMULADOS_".$mes."-".$anio .".xlsx";
header('Content-Disposition: attachment;filename='.$nombre_archivo);
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
ob_clean();
$objWriter->save('php://output');

 ?>
