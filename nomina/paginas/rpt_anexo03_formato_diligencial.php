<?php
error_reporting(E_ALL);
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('America/Panama');
if (PHP_SAPI == 'cli')
    die('Este ejemplo solo se debe ejecutar desde un navegador Web');

include('../lib/common_excel.php');
include('lib/php_excel.php');
include("funciones_nomina.php");
$conexion=conexion();

$codtip=$_GET['codtip'];
$mesano1=$_GET['mesano'];
$mesano2=$_GET['mesano1'];
$ma1=explode("/",$mesano1);
$ma2=explode("/",$mesano2);
$fecha_fin = $ma2[0]."-".$ma2[1]."-".$ma2[2];

require_once 'phpexcel/Classes/PHPExcel.php';

$objPHPExcel = new PHPExcel();

$objPHPExcel->getProperties()->setCreator("Selectra")
                             ->setLastModifiedBy("Selectra")
                             ->setTitle("Formato Diligencial")
                             ->setSubject("Office 2007 XLSX Test Document");

$sql = "SELECT e.nom_emp as empresa, e.rif, e.tel_emp as telefono, e.dir_emp as direccion, e.ciu_emp as ciudad, 
               e.edo_emp, e.imagen_izq as logo
        FROM   nomempresa e";

$res=query($sql, $conexion);

$fila=fetch_array($res);
$logo=$fila['logo'];

function obtenerMesesTrabajador($ficha, $tipnom, $fecha_inicio, $fecha_fin)
{
    $fecha_inicio = date('Y-m-d',strtotime($fecha_inicio));
    //$fecha_fin = date('Y-m-d',strtotime($fecha_fin));
    
    $sql = "SELECT fecha_pago from salarios_acumulados WHERE fecha_pago BETWEEN '$fecha_inicio' AND '$fecha_fin' AND tipo_planilla = '$tipnom' AND ficha='$ficha'
     order by fecha_pago";
    $conexion = conexion();
    $res = query($sql,$conexion);
    $cant = 0;
    $meses = array();
    while($filas = fetch_array($res))
    {
        $fecha_mes = date('m',strtotime($filas['fecha_pago']));
        if(!in_array($fecha_mes,$meses))
        {
            $meses[]=$fecha_mes;
            $cant++;
        }
    }
    return count($meses);
}

//$sql2 = "SELECT np.periodo_ini as desde, np.periodo_fin as hasta  FROM nom_nominas_pago np 
//         WHERE  np.codnom=".$codnom." AND np.tipnom=".$codtip;
//$res2=query($sql2, $conexion);
//$fila2=fetch_array($res2);

/*$objDrawing->setName('Logo');
$objDrawing->setDescription('Logo');
$objDrawing->setCoordinates('B2');
$objDrawing->setPath('../imagenes/'.$logo);
//$objDrawing->setResizeProportional(true);
$objDrawing->setHeight(80);
//$objDrawing->setAutoSize(220);
$objDrawing->setOffsetX(0);
$objDrawing->setOffsetY(0);
$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
*/
//$objDrawing->setHeight(36);
//$objPHPExcel->getActiveSheet()->getHeaderFooter()->addImage($objDrawing, PHPExcel_Worksheet_HeaderFooter::IMAGE_HEADER_LEFT);

$objPHPExcel->setActiveSheetIndex(0); 

$objPHPExcel->getActiveSheet()->setTitle('Informe anexo 03');
$objPHPExcel->getActiveSheet()
            ->setCellValue('A1',' INFORME 03v4 - FORMATO A DILIGENCIAR Versión vigente del 2017 en adelante.')
            ->setCellValue('A2', 'Esta sección de encabezado no se debe modificar.')
            ->setCellValue('A3', 'Los datos de este informe deben ser registrados a partir de la línea 5 en adelante.');
            // ->setCellValue('E6', 'PLANILLA ANEXO 03')
            // ->setCellValue('E7', 'FECHA INICIO : '.$mesano1.' -  FECHA FIN:'.$mesano2)
            //  ->setCellValue('E8', 'Fecha: '.date('d/m/Y'));    

// $objPHPExcel->getActiveSheet()->getStyle('E6')->getFont()->setName('Arial');
$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(12);
$objPHPExcel->getActiveSheet()->getStyle('A3')->getFont()->setSize(9);
$objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setSize(9);
$objPHPExcel->getActiveSheet()->mergeCells('A1:F1');
// $objPHPExcel->getActiveSheet()->mergeCells('E2:P2');
// $objPHPExcel->getActiveSheet()->mergeCells('E3:P3');
// $objPHPExcel->getActiveSheet()->mergeCells('E4:P4');
// $objPHPExcel->getActiveSheet()->mergeCells('E6:K6');
// $objPHPExcel->getActiveSheet()->mergeCells('E7:K7');
// $objPHPExcel->getActiveSheet()->mergeCells('E8:K8');
// $objPHPExcel->getActiveSheet()->mergeCells('F10:G10');
// $objPHPExcel->getActiveSheet()->mergeCells('F11:G11');

$objPHPExcel->getActiveSheet()->getStyle('A4:Y4')->applyFromArray(allBordersThin());
$objPHPExcel->getActiveSheet()->getStyle('A4:Y4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('A4:Y4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
// $objPHPExcel->getActiveSheet()->getStyle('E6:K6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
// $objPHPExcel->getActiveSheet()->getStyle('E7:K7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
// $objPHPExcel->getActiveSheet()->getStyle('E8:K8')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
// $objPHPExcel->getActiveSheet()->getStyle('E6:K6')->getFont()->setBold(true);
 $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
 $objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);
 $objPHPExcel->getActiveSheet()->getStyle('A3')->getFont()->setBold(true);
// $objPHPExcel->getActiveSheet()->getStyle('E3')->getFont()->setBold(true);
// $objPHPExcel->getActiveSheet()->getStyle('E4')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('A4:Y4')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('A4:Y4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('A4:Y4')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('A4:Y4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(9);
// $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn('A')->setWidth(12);

// $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn('A')->setAutoSize(false);
// $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn('A')->setWidth(4);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(5);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(5);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(25);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(25);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(25);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(30);
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(30);
$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(30);
$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(30);
$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setAutoSize(30);
$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setAutoSize(25);
$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setAutoSize(25);
$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setAutoSize(25);
$objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setAutoSize(25);
$objPHPExcel->getActiveSheet()->getColumnDimension('R')->setAutoSize(30);
$objPHPExcel->getActiveSheet()->getColumnDimension('S')->setAutoSize(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('T')->setAutoSize(25);
$objPHPExcel->getActiveSheet()->getColumnDimension('U')->setAutoSize(25);
$objPHPExcel->getActiveSheet()->getColumnDimension('V')->setAutoSize(30);
$objPHPExcel->getActiveSheet()->getColumnDimension('W')->setAutoSize(30);
$objPHPExcel->getActiveSheet()->getColumnDimension('X')->setAutoSize(30);
$objPHPExcel->getActiveSheet()->getColumnDimension('Y')->setAutoSize(30);
//$objPHPExcel->getActiveSheet()->getColumnDimension('Z')->setAutoSize(30);
//$objPHPExcel->getActiveSheet()->getColumnDimension('AA')->setAutoSize(30);
//$objPHPExcel->getActiveSheet()->getColumnDimension('AB')->setAutoSize(30);
//$objPHPExcel->getActiveSheet()->getColumnDimension('AC')->setAutoSize(30);
$cadena='GRUPO SEGUN TABLA RETENCION Nº DEPENDIENTES';
$tam_cadena=strlen($cadena);
// $objPHPExcel->getActiveSheet()
        
//             ->setCellValue('A10', '')
//             ->setCellValue('B10', '')
//             ->setCellValue('C10', '7')
//             ->setCellValue('D10', '')
//             ->setCellValue('E10', '8')
//             ->setCellValue('F10', '9')
//             ->setCellValue('H10', '10')
//             ->setCellValue('I10', '11')
//             ->setCellValue('J10', '12')                   
//             ->setCellValue('K10', '13')
//             ->setCellValue('L10', '14')
//             ->setCellValue('M10', '15')
//             ->setCellValue('N10', '16')
//             ->setCellValue('O10', '17')
//             ->setCellValue('P10', '18')
//             ->setCellValue('Q10', '19')
//             ->setCellValue('R10', '20')
//             ->setCellValue('S10', '21')
//             ->setCellValue('T10', '22')
//             ->setCellValue('U10', '23')
//             ->setCellValue('V10', '24')
//             ->setCellValue('W10', '25')
//             ->setCellValue('X10', '26')
//             ->setCellValue('Y10', '27')
//             ->setCellValue('Z10', '28')
//             ->setCellValue('AA10', '29')
//             ->setCellValue('AB10', '30')
//             ->setCellValue('AC10', '31');

$objPHPExcel->getActiveSheet()       
            ->setCellValue('A4', 'EMPLEADO DECLARA')
            ->setCellValue('B4', 'TIPO ID.')
            ->setCellValue('C4', 'CÉDULA')
            ->setCellValue('D4', 'DV')
            ->setCellValue('E4', 'NOMBRE')
            ->setCellValue('F4', 'GRUPO SEGÚN TABLA RETENCIÓN')
            ->setCellValue('G4', 'NÚMERO DE MESES TRABAJADOS')
            ->setCellValue('H4', 'REMUNERACIONES RECIBIDAS DURANTE EL ANO EN SALARIOS')
            ->setCellValue('I4', 'REMUNERACIONES RECIBIDAS DURANTE EL AÑO EN SALARIOS EN ESPECIES')                   
            ->setCellValue('J4', 'REMUNERACIONES RECIBIDAS DURANTE EL AÑO EN GASTOS DE REPRESENTACIÓN')
            ->setCellValue('K4', 'REMUNERACIONES RECIBIDAS DURANTE EL AÑO EN SALARIOS SIN RETENCIONES')
            ->setCellValue('L4', 'DEDUCCIÓN CONJUNTA')
            ->setCellValue('M4', 'INTERESES HIPOTECARIOS')
            ->setCellValue('N4', 'INTERESES EDUCATIVOS')
            ->setCellValue('O4', 'PRIMAS SEGUROS')
            ->setCellValue('P4', 'APORTE FONDOS DE JUBILACIÓN')
            ->setCellValue('Q4', 'TOTAL DE DEDUCCIONES')
            ->setCellValue('R4', 'RENTA NETA GRAVABLE')
            ->setCellValue('S4', 'IMPUESTO CAUSADO')
            ->setCellValue('T4', 'IMPUESTO CAUSADO GASTO DE REPRESENTACIÓN')
            ->setCellValue('U4', 'RETENCIONES DURANTE EL AÑO EN SALARIOS')
            ->setCellValue('V4', 'RETENCIONES DURANTE EL AÑO EN GASTOS DE REPRESENTACIÓN')
            ->setCellValue('W4', 'RETENCIONES DURANTE EL AÑO EN SALARIOS')
            ->setCellValue('X4', 'A FAVOR DEL FISCO')
            ->setCellValue('Y4', 'A FAVOR DEL EMPLEADO');
cellColor('A4:Y4', 'dddddd');
cellColor('A1:Y1', 'dddddd');
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth($tam_cadena);
//$objPHPExcel->getActiveSheet()->getStyle('A10:AC10')->applyFromArray(allBordersThin());
$objPHPExcel->getActiveSheet()->getStyle('A11:AC11')->applyFromArray(allBordersThin());
$sql = "SELECT DISTINCT np.ficha, np.cedula as cedula, np.apenom as nombre, np.seguro_social as seguro, np.fecing as fecha_ingreso, np.clave_ir,np.tipnom,np.inicio_periodo,np.suesal,np.dv
        FROM   salarios_acumulados nm
        INNER JOIN nompersonal np ON nm.cedula=np.cedula
        INNER JOIN nomcampos_adic_personal cam ON np.ficha=cam.ficha
        WHERE  YEAR(nm.fecha_pago) = '".$ma2[2]."'
        ORDER BY np.apenom";
$i=5;
$ini=$i;
$res=query($sql, $conexion);

$total_salarios_integral=$total_deducciones=$total_seguro=$total_total=$total_renta_neta=0;
$total_salarios=$total_xiii_mes=$total_vacaciones=0;
while($fila=fetch_array($res))
{
    $ficha=$fila['ficha'];
    //$grupo_ir=$fila['valor'];
    $ingreso = '0000-00-00';
    $SUELDO = $fila['suesal'];
    if($fila['tipnom'] == '1'){
        $ingreso = $fila['fecha_ingreso'];

    }else{
        $ingreso = $fila['inicio_periodo'];
    }
    if(strtotime($ingreso)>strtotime(fecha_sql($mesano1))){
        $inicio=date('Y-m-d',strtotime($ingreso));
    }else{
        $inicio=date('Y-m-d',strtotime($mesano1));
    }    
    $fecha2 = str_replace("/","-",$mesano2);
   
    if($fila['tipnom'] == '1'){
        //$meses=floor(antiguedad($inicio,date('Y-m-d',strtotime($fecha2)),'D')/30);
    $meses = obtenerMesesTrabajador($ficha,$codtip,$mesano1,$fecha_fin);

    }else{
        $meses = obtenerMesesTrabajador($ficha,$codtip,$mesano1,$fecha_fin);
    }
    
    //SALARIOS INTEGRAL (SALARIOS + XIII MES + VACACIONES)
   /* $sql_salarios_integral = "SELECT nm.monto as monto
            FROM   nom_movimientos_nomina nm, nom_nominas_pago np 
            WHERE  np.codnom=nm.codnom AND np.status='C' AND np.anio=nm.anio AND np.frecuencia in (2,3,7,8,12,13,14) AND np.tipnom=".$codtip.""
            . " AND nm.ficha=".$ficha." AND ( nm.codcon=100 OR nm.codcon=102 OR nm.codcon=114 ) AND nm.anio=".$ma1[2]."";*/
    $sql_salarios_integral = "SELECT SUM(salario_bruto) monto,SUM(vacac) vacac,SUM(xiii) xiii,SUM(gtorep) gtorep,SUM(xiii_gtorep) xiii_gtorep,
    SUM(liquida) liquida,SUM(bono) bono,SUM(otros_ing) otros_ing,SUM(prima) as prima
    FROM salarios_acumulados WHERE ficha = '{$ficha}' and YEAR(fecha_pago) = '".$ma2[2]."'";
    $res_salarios_integral=query($sql_salarios_integral, $conexion);
    $salarios_integral=0;
    $fila_salarios_integral=fetch_array($res_salarios_integral);
    $salarios_integral=$fila_salarios_integral['monto'];

//grupo retencion
    $cam_grupo="SELECT valor FROM nomcampos_adic_personal 
    WHERE ficha = '{$ficha}' and id='103'";
    $tabla_integral=query($cam_grupo, $conexion);
    $fx=fetch_array($tabla_integral);
 //empleado declara
    $cam_declara="SELECT valor FROM nomcampos_adic_personal 
    WHERE ficha = '{$ficha}' and id='101'";
     $tabla_integral2=query($cam_declara, $conexion);
    $fx_declara=fetch_array($tabla_integral2);
    // //tipo ID
    $camp="SELECT valor FROM nomcampos_adic_personal 
    WHERE ficha = '{$ficha}' and id='102'";
    $tabla_integral3=query($camp, $conexion);
    $fx_tipo_id=fetch_array($tabla_integral3);
    //GASTOS REPRESENTACION
    /*$sql_gastosr = "SELECT nm.monto as monto
            FROM   nom_movimientos_nomina nm, nom_nominas_pago np 
            WHERE  np.codnom=nm.codnom AND np.status='C' AND np.anio=nm.anio AND np.frecuencia in (2,3,7,8,12,13,14) AND np.tipnom=".$codtip.""
            . " AND nm.ficha=".$ficha." AND ( nm.codcon=145) AND nm.anio=".$ma1[2]."";*/    
            
    $sql_gastosr = "SELECT SUM(gtorep) monto FROM salarios_acumulados WHERE ficha = '{$ficha}' and YEAR(fecha_pago) = '".$ma2[2]."'";    

    $res_gastosr=query($sql_gastosr, $conexion);
    $gastosr=0;
    $fila_gastosr=fetch_array($res_gastosr);
    $gastosr=$fila_gastosr['monto'];  


    $sql_prima = "SELECT SUM(prima) monto FROM salarios_acumulados WHERE ficha = '{$ficha}' and YEAR(fecha_pago) = '".$ma2[2]."'";    

    $res_prima=query($sql_prima, $conexion);
    $prima=0;
    $fila_prima=fetch_array($res_prima);
    $prima=$fila_prima['monto'];
      
    
    //DEDUCCIONES
    /*$sql_deducciones = "SELECT nm.monto as monto
            FROM   nom_movimientos_nomina nm, nom_nominas_pago np 
            WHERE  np.codnom=nm.codnom AND np.status='C' AND np.anio=nm.anio AND np.frecuencia in (2,3,7,8,12,13,14) AND np.tipnom=".$codtip.""
            . " AND nm.ficha=".$ficha." AND ( nm.codcon=200) AND nm.anio=".$ma1[2]."";*/
    $sql_deducciones = "SELECT SUM(s_s) monto FROM salarios_acumulados WHERE ficha = '{$ficha}' and YEAR(fecha_pago) = '".$ma2[2]."'";
    
    $res_deducciones=query($sql_deducciones, $conexion);
    $deducciones=0;
    $fila_deducciones=fetch_array($res_deducciones);
    $deducciones=$fila_deducciones['monto'];    
    
    //SEGURO EDUCATIVO
    /*$sql_seguro = "SELECT nm.monto as monto
            FROM   nom_movimientos_nomina nm, nom_nominas_pago np 
            WHERE  np.codnom=nm.codnom AND np.status='C' AND np.anio=nm.anio AND np.frecuencia in (2,3,7,8,12,13,14) AND np.tipnom=".$codtip.""
            . " AND nm.ficha=".$ficha." AND ( nm.codcon=201) AND nm.anio=".$ma1[2]."";*/
    $sql_seguro = "SELECT SUM(s_e) monto FROM salarios_acumulados WHERE ficha = '{$ficha}' and YEAR(fecha_pago) = '".$ma2[2]."'";
            
    $res_seguro=query($sql_seguro, $conexion);
    $seguro=0;
    $fila_seguro=fetch_array($res_seguro);
    $seguro=$fila_seguro['monto'];
    
    //SEGURO EDUCATIVO GR
    /*$sql_segurogr = "SELECT nm.monto as monto
            FROM   nom_movimientos_nomina nm, nom_nominas_pago np 
            WHERE  np.codnom=nm.codnom AND np.status='C' AND np.anio=nm.anio AND np.frecuencia in (2,3,7,8,12,13,14) AND np.tipnom=".$codtip.""
            . " AND nm.ficha=".$ficha." AND ( nm.codcon=207) AND nm.anio=".$ma1[2]."";*/
    $sql_isr = "SELECT SUM(islr) monto FROM salarios_acumulados WHERE ficha = '{$ficha}' and YEAR(fecha_pago) = '".$ma2[2]."'";
            
    $res_isr=query($sql_isr, $conexion);
    $islr=0;
    $fila_isr=fetch_array($res_isr);
    $islr=$fila_isr['monto'];


    $sql_isr_gr = "SELECT SUM(islr_gr) monto FROM salarios_acumulados WHERE ficha = '{$ficha}' and YEAR(fecha_pago) = '".$ma2[2]."'";
            
    $res_isr_gr=query($sql_isr_gr, $conexion);
    $islr_gr=0;
    $fila_isr_gr=fetch_array($res_isr_gr);
    $islr_gr=$fila_isr_gr['monto'];
    
    $sql_isr_gr13 = "SELECT SUM(monto) monto FROM nom_movimientos_nomina WHERE ficha = '{$ficha}' and anio = '".$ma2[2]."' and codcon in (212)";
    $res_isr_gr13=query($sql_isr_gr13, $conexion);
    $islr_gr13=0;
    $fila_isr_gr13=fetch_array($res_isr_gr13);
    $islr_gr13=$fila_isr_gr13['monto'];
      
    //INTERESES HIPOTECARIOS
    $interes_hipotecarios=0;
    
    //INTERESES EDUCATIVOS
    $interes_educativos=0;
    
    //PRIMAS SEGUROS
    $primas_seguros=0;
    
    //FONDO JUBILACION
    $fondo_jubilacion=0;
    $renta_integral=0;

    
    //SALARIOS
    /*$sql_salarios = "SELECT nm.monto as monto
            FROM   nom_movimientos_nomina nm, nom_nominas_pago np 
            WHERE  np.codnom=nm.codnom AND np.status='C' AND np.anio=nm.anio AND np.frecuencia in (2,3,7,8,12,13,14) AND np.tipnom=".$codtip.""
            . " AND nm.ficha=".$ficha." AND ( nm.codcon=100) AND nm.anio=".$ma1[2]."";*/

   /* $sql_salarios = "SELECT SUM(salario_bruto+vacac+xiii+gtorep+xiii_gtorep+liquida+bono+otros_ing) monto
    FROM salarios_acumulados WHERE ficha = '{$ficha}' and YEAR(fecha_pago) = '".$ma2[2]."'";

    $res_salarios=query($sql_salarios, $conexion);
    $salarios=0;
    $fila_salarios=fetch_array($res_salarios);    
    $salarios=$salarios+$fila_salarios['monto'];   */
    //TOTAL
    $salarios_integral=$fila_salarios_integral['monto']+$fila_salarios_integral['vacac']+$fila_salarios_integral['xiii']+$fila_salarios_integral['bono']+$fila_salarios_integral['otros_ing']; 
       
    //$total=$salarios+$gastosr;
    $total=$salarios_integral+$gastosr+$fila_salarios_integral['xiii_gtorep']+$fila_salarios_integral['liquida']; 
    $total_grep = $gastosr+$fila_salarios_integral['xiii_gtorep'];
    $total_islr = $islr_gr + $islr;
    $total_prima = $prima;
    $tot_ret_grep=$total_grep*0.10;
    //RENTA NETA GRAVABLE
    $renta_neta=$salarios_integral-$deducciones-$seguro-$interes_hipotecarios-$interes_educativos-$primas_seguros-$fondo_jubilacion;  
    $isrl_anual  =  $SUELDO * 12;
    if($fila['clave_ir'][0]=="E")
    {
        $salarios_integral = $salarios_integral-800;
    }
    $isrl_anual = number_format($salarios_integral,0,'.','');

    $rango = ($isrl_anual > 11001 AND $isrl_anual < 50000)?true:false;
    $rango2 = ($isrl_anual > 49999 AND $isrl_anual < 500000)?true:false;

    if($fila['tipnom'] == '1'){
        if ($rango )
        {
            $T01 = $salarios_integral + campoadicionalper(1,$ficha);
            $T02 = $T01 -11000;
            $T03 = $T02*0.15;
            $T04 = ((campoadicionalper(2,$ficha)*0.10)/2);
            $renta_integral = $T03+$T04;
            /*$T06 = campoadicionalper(2,$ficha);
            if ($T06 == "NO") {
                $renta_integral = $T04+$T05;
            } else {
                $renta_integral = 0;
            }       */  
        } 
        elseif ($rango2) 
        {
            $T01 = $salarios_integral + campoadicionalper(1,$ficha);
            $T02 = ($T01 - 50000);
            $T03 = ($T02*0.25);
            $renta_integral  =   ($T03+5850);
            $islr_formula = "=";
        } 
        elseif ($islr_anual <= 11000)
        {
            $renta_integral  =  0;
        }
        $renta_integral = round($renta_integral,2);
    }
    else{
        $cant_meses_trabajador = obtenerMesesTrabajador($ficha,$codtip,$mesano1,$fecha_fin);
        if($cant_meses_trabajador == NULL OR $cant_meses_trabajador==0)
        {
            $islr_integral = 0;
        }
        else
        {
            $islr_integral  =  $renta_neta/$cant_meses_trabajador;
        }
        if ($rango) 
        {
            if($fila['clave_ir'][0]=="E")
            {
                $salarios_integral = $salarios_integral-800;
            }
            $T01 = $salarios_integral + campoadicionalper(1,$ficha);
            $T02 = $T01 -11000;
            $T03 = $T02*0.15;
            $T04 = ((campoadicionalper(2,$ficha)*0.10)/2);
            $renta_integral = $T03+$T04;
            $renta_integral = round($renta_integral,2);
            /**if ($fila['cedula'] == "8-846-1341") {
                echo $T01;
                exit;
            }******/
        } 
        elseif ($rango2) 
        {
            $T01 = $salarios_integral + campoadicionalper(1,$ficha);
            $T02 = ($T01 - 50000);
            $T03 = ($T02*0.25);
            $renta_integral  =   ($T03+5850);
            $islr_formula = "=";
        } 
        elseif ($islr_integral <= 11000)
        {
            $renta_integral  =  0;
        }

    }
    $impuesto_causado = $renta_integral;
    $renta_integral = round($impuesto_causado,2);

    $clave_ir = $fila['clave_ir'];
    $monto_deducciones=$renta_neta_gravable=0;
    if ($clave_ir[0]=="A") {
        $grupo1=1;
        $monto_deducciones = 0;
        $total_deducciones = $monto_deducciones;
        $renta_neta_gravable = $renta_neta - $monto_deducciones;
    } else {
        $grupo1=4;
        $monto_deducciones = 800;
        $total_deducciones = 0;
        $renta_neta_gravable = $renta_neta - $monto_deducciones;
    }

    $grupo2=$clave_ir[1];
    $a_favor = $impuesto_causado - $renta_gravable;
    $dv = ($fila['dv'] == "") ? $fila['dv'] : 99;

    //$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(6);
    // $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn('A')->setAutoSize(false);
    //$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn('A')->setWidth(6);
    $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn('A')->setWidth(22);
    //$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn('A')->setAutoSize(true);
    $objPHPExcel->getActiveSheet()->setCellValue('A'.$i, $fx_declara['valor']);
    $objPHPExcel->getActiveSheet()->setCellValue('B'.$i, $fx_tipo_id['valor']);
    $objPHPExcel->getActiveSheet()->setCellValue('C'.$i, $fila['cedula']);
    $objPHPExcel->getActiveSheet()->setCellValue('D'.$i, $fila['dv']);
    $objPHPExcel->getActiveSheet()->setCellValue('E'.$i, utf8_encode($fila['nombre']));
    $objPHPExcel->getActiveSheet()->setCellValue('F'.$i, $fx['valor']);
    $objPHPExcel->getActiveSheet()->setCellValue('G'.$i, $meses);
    $objPHPExcel->getActiveSheet()->setCellValue('H'.$i, $salarios_integral);
    $objPHPExcel->getActiveSheet()->setCellValue('I'.$i, '0.00');
    $objPHPExcel->getActiveSheet()->setCellValue('J'.$i, $total_grep);
    $objPHPExcel->getActiveSheet()->setCellValue('K'.$i, $total_prima);
    $objPHPExcel->getActiveSheet()->setCellValue('L'.$i, '0.00');
    $objPHPExcel->getActiveSheet()->setCellValue('M'.$i, '0.00');
    $objPHPExcel->getActiveSheet()->setCellValue('N'.$i, '0.00');
    $objPHPExcel->getActiveSheet()->setCellValue('O'.$i, '0.00');
    $objPHPExcel->getActiveSheet()->setCellValue('P'.$i, '0.00');
    $objPHPExcel->getActiveSheet()->setCellValue('Q'.$i, "=L".$i."+M".$i."+N".$i."+O".$i."+P".$i);
    $objPHPExcel->getActiveSheet()->setCellValue('R'.$i, "=H".$i."+I".$i."+K".$i);
    $objPHPExcel->getActiveSheet()->setCellValue('S'.$i, $impuesto_causado);
    $objPHPExcel->getActiveSheet()->setCellValue('T'.$i, $tot_ret_grep);
    $objPHPExcel->getActiveSheet()->setCellValue('U'.$i, $islr);
    $objPHPExcel->getActiveSheet()->setCellValue('V'.$i, ($islr_gr+$islr_gr13));
    $objPHPExcel->getActiveSheet()->setCellValue('W'.$i, $islr);
    //$objPHPExcel->getActiveSheet()->setCellValue('Y'.$i, '0');
    // $objPHPExcel->getActiveSheet()->setCellValue('AA'.$i, '0');
    if(($impuesto_causado-$islr)>0)
    {
        $objPHPExcel->getActiveSheet()->setCellValue('X'.$i, abs($impuesto_causado-$total_islr));
        $objPHPExcel->getActiveSheet()->setCellValue('Y'.$i, '0.00');
        //$objPHPExcel->getActiveSheet()->setCellValue('AB'.$i, );
    }
    else
    {
        $objPHPExcel->getActiveSheet()->setCellValue('X'.$i, '0.00');
        $objPHPExcel->getActiveSheet()->setCellValue('Y'.$i,  abs($impuesto_causado-$total_islr));
    }
    
    $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':Y'.$i)->applyFromArray(allBordersThin());
        //$objPHPExcel->getActiveSheet()->getStyle('H'.$i)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
    $i++;
}
/*
$objPHPExcel->getActiveSheet()->setCellValue('H'.$i, "TOTALES");
$objPHPExcel->getActiveSheet()->setCellValue('I'.$i, "=SUM(I".$ini.":I".($i-1).")");
$objPHPExcel->getActiveSheet()->setCellValue('J'.$i, "=SUM(J".$ini.":J".($i-1).")");
$objPHPExcel->getActiveSheet()->setCellValue('K'.$i, "=SUM(K".$ini.":K".($i-1).")");
$objPHPExcel->getActiveSheet()->setCellValue('L'.$i, "=SUM(L".$ini.":L".($i-1).")");
$objPHPExcel->getActiveSheet()->setCellValue('M'.$i, "=SUM(M".$ini.":M".($i-1).")");
$objPHPExcel->getActiveSheet()->setCellValue('N'.$i, "=SUM(N".$ini.":N".($i-1).")");
$objPHPExcel->getActiveSheet()->setCellValue('O'.$i, "=SUM(O".$ini.":O".($i-1).")");
$objPHPExcel->getActiveSheet()->setCellValue('P'.$i, "=SUM(P".$ini.":P".($i-1).")");
$objPHPExcel->getActiveSheet()->setCellValue('Q'.$i, "=SUM(Q".$ini.":Q".($i-1).")");
$objPHPExcel->getActiveSheet()->setCellValue('R'.$i, "=SUM(R".$ini.":R".($i-1).")");
$objPHPExcel->getActiveSheet()->setCellValue('S'.$i, "=SUM(S".$ini.":S".($i-1).")");
$objPHPExcel->getActiveSheet()->setCellValue('T'.$i, "=SUM(T".$ini.":T".($i-1).")");
$objPHPExcel->getActiveSheet()->setCellValue('U'.$i, "=SUM(U".$ini.":U".($i-1).")");
$objPHPExcel->getActiveSheet()->setCellValue('V'.$i, "=SUM(V".$ini.":V".($i-1).")");
$objPHPExcel->getActiveSheet()->setCellValue('W'.$i, "=SUM(W".$ini.":W".($i-1).")");
$objPHPExcel->getActiveSheet()->setCellValue('X'.$i, "=SUM(X".$ini.":X".($i-1).")");
$objPHPExcel->getActiveSheet()->setCellValue('Y'.$i, "=SUM(Y".$ini.":Y".($i-1).")");
// $objPHPExcel->getActiveSheet()->setCellValue('AB'.$i, "=SUM(AB".$ini.":AB".($i-1).")");
// $objPHPExcel->getActiveSheet()->setCellValue('AC'.$i, "=SUM(AC".$ini.":AC".($i-1).")");
$objPHPExcel->getActiveSheet()->getStyle('I'.$i.':Y'.$i)->applyFromArray(allBordersThin());
$objPHPExcel->getActiveSheet()->getStyle('H'.$i.':Y'.$i)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('H'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$i=$i+7;

$objPHPExcel->getActiveSheet()->mergeCells('E'.$i.':G'.($i+4));
$objPHPExcel->getActiveSheet()->getStyle('E'.$i.':G'.($i+4))->applyFromArray(allBordersThin());
$objPHPExcel->getActiveSheet()->mergeCells('H'.$i.':I'.($i+4));
$objPHPExcel->getActiveSheet()->getStyle('H'.$i.':I'.($i+4))->applyFromArray(allBordersThin());
$objPHPExcel->getActiveSheet()->mergeCells('K'.$i.':M'.($i+4));
$objPHPExcel->getActiveSheet()->getStyle('K'.$i.':M'.($i+4))->applyFromArray(allBordersThin());
$i=$i+5;
$objPHPExcel->getActiveSheet()->mergeCells('E'.$i.':G'.$i);
$objPHPExcel->getActiveSheet()->getStyle('E'.$i.':G'.$i)->applyFromArray(allBordersThin());
$objPHPExcel->getActiveSheet()->setCellValue('E'.$i, 'RECIBIDO POR');
$objPHPExcel->getActiveSheet()->getStyle('E'.$i)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->mergeCells('H'.$i.':I'.$i);
$objPHPExcel->getActiveSheet()->getStyle('H'.$i.':I'.$i)->applyFromArray(allBordersThin());
$objPHPExcel->getActiveSheet()->setCellValue('H'.$i, 'AUTORIZADO POR');
$objPHPExcel->getActiveSheet()->getStyle('H'.$i)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('H'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->mergeCells('K'.$i.':M'.$i);
$objPHPExcel->getActiveSheet()->getStyle('K'.$i.':M'.$i)->applyFromArray(allBordersThin());
$objPHPExcel->getActiveSheet()->setCellValue('K'.$i, 'PREPARADO POR');
$objPHPExcel->getActiveSheet()->getStyle('K'.$i)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('K'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$objPHPExcel->getActiveSheet()->setSelectedCells('B100');

*/
//=================================================================================
//HOJA RESUMEN PREELABORADA
// $objPHPExcel->createSheet();
// $objPHPExcel->setActiveSheetIndex(1); 
// $sql = "SELECT e.nom_emp as empresa, e.rif, e.tel_emp as telefono, e.dir_emp as direccion, e.ciu_emp as ciudad, 
//                e.edo_emp, e.imagen_izq as logo
//         FROM   nomempresa e";

// $res=query($sql, $conexion);

// $fila=fetch_array($res);
// $objPHPExcel->getActiveSheet()->setTitle('Resumen Preelaborada');

// $objPHPExcel->getActiveSheet()
//             ->setCellValue('E2',  $fila['empresa'])
//             ->setCellValue('E3', 'RIF '.$fila['rif'].' Telefonos '.$fila['telefono'])
//             ->setCellValue('E4', 'Direccion: '.$fila['direccion'])
//             ->setCellValue('E6', 'RESUMEN PREELABORADA')
//             ->setCellValue('E7', 'FECHA INICIO : '.$mesano1.' -  FECHA FIN:'.$mesano2)
//              ->setCellValue('E8', 'Fecha: '.date('d/m/Y'));    

// $objPHPExcel->getActiveSheet()->getStyle('E6')->getFont()->setName('Arial');
// $objPHPExcel->getActiveSheet()->getStyle('E6')->getFont()->setSize(14);
// //$objPHPExcel->getActiveSheet()->mergeCells('C2:D5');
// $objPHPExcel->getActiveSheet()->mergeCells('E2:P2');
// $objPHPExcel->getActiveSheet()->mergeCells('E3:P3');
// $objPHPExcel->getActiveSheet()->mergeCells('E4:P4');
// $objPHPExcel->getActiveSheet()->mergeCells('E6:K6');
// $objPHPExcel->getActiveSheet()->mergeCells('E7:K7');
// $objPHPExcel->getActiveSheet()->mergeCells('E8:K8');
// //$objPHPExcel->getActiveSheet()->getStyle('C2:D5')->applyFromArray(allBordersThin());
// //$objPHPExcel->getActiveSheet()->getStyle('C2:D5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
// //$objPHPExcel->getActiveSheet()->getStyle('C2:D5')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
// $objPHPExcel->getActiveSheet()->getStyle('E6:K6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
// $objPHPExcel->getActiveSheet()->getStyle('E7:K7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
// $objPHPExcel->getActiveSheet()->getStyle('E8:K8')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
// $objPHPExcel->getActiveSheet()->getStyle('E6:K6')->getFont()->setBold(true);
// $objPHPExcel->getActiveSheet()->getStyle('E2')->getFont()->setBold(true);
// $objPHPExcel->getActiveSheet()->getStyle('E3')->getFont()->setBold(true);
// $objPHPExcel->getActiveSheet()->getStyle('E4')->getFont()->setBold(true);
// $objPHPExcel->getActiveSheet()->getStyle('A10:AM10')->getFont()->setBold(true);
// $objPHPExcel->getActiveSheet()->getStyle('A10:AM10')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
// $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
// $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(7);
// $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
// $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
// $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
// $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
// $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(10);
// $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
// $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(10);
// $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
// $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(10);
// $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(15);
// $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(10);
// $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(10);
// $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(15);
// $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(10);
// $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(15);
// $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(10);
// $objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(15);
// $objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(10);
// $objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth(15);
// $objPHPExcel->getActiveSheet()->getColumnDimension('V')->setWidth(10);
// $objPHPExcel->getActiveSheet()->getColumnDimension('W')->setWidth(10);
// $objPHPExcel->getActiveSheet()->getColumnDimension('X')->setWidth(15);
// $objPHPExcel->getActiveSheet()->getColumnDimension('Y')->setWidth(10);
// $objPHPExcel->getActiveSheet()->getColumnDimension('Z')->setWidth(15);
// $objPHPExcel->getActiveSheet()->getColumnDimension('AA')->setWidth(10);
// $objPHPExcel->getActiveSheet()->getColumnDimension('AB')->setWidth(15);
// $objPHPExcel->getActiveSheet()->getColumnDimension('AC')->setWidth(10);
// $objPHPExcel->getActiveSheet()->getColumnDimension('AD')->setWidth(15);
// $objPHPExcel->getActiveSheet()->getColumnDimension('AE')->setWidth(10);
// $objPHPExcel->getActiveSheet()->getColumnDimension('AF')->setWidth(10);
// $objPHPExcel->getActiveSheet()->getColumnDimension('AG')->setWidth(20);
// $objPHPExcel->getActiveSheet()->getColumnDimension('AH')->setWidth(15);
// $objPHPExcel->getActiveSheet()->getColumnDimension('AI')->setWidth(25);
// $objPHPExcel->getActiveSheet()->getColumnDimension('AJ')->setWidth(20);
// $objPHPExcel->getActiveSheet()->getColumnDimension('AK')->setWidth(20);
// $objPHPExcel->getActiveSheet()->getColumnDimension('AL')->setWidth(25);
// $objPHPExcel->getActiveSheet()->getColumnDimension('AM')->setWidth(20);

// $objPHPExcel->getActiveSheet()       
//             ->setCellValue('A10', 'CEDULA')
//             ->setCellValue('B10', 'DV')
//             ->setCellValue('C10', 'NOMBRE')
//             ->setCellValue('D10', 'CLAVE')
//             ->setCellValue('E10', 'DEDUCCION')
//             ->setCellValue('F10', 'ENERO')
//             ->setCellValue('G10', 'G.R.')
//             ->setCellValue('H10', 'FEBRERO')
//             ->setCellValue('I10', 'G.R.')
//             ->setCellValue('J10', 'MARZO')                   
//             ->setCellValue('K10', 'G.R.')
//             ->setCellValue('L10', 'ABRIL')
//             ->setCellValue('M10', 'G.R.')
//             ->setCellValue('N10', 'XIII MES')
//             ->setCellValue('O10', 'MAYO')
//             ->setCellValue('P10', 'G.R.')
//             ->setCellValue('Q10', 'JUNIO')
//             ->setCellValue('R10', 'G.R.')
//             ->setCellValue('S10', 'JULIO')
//             ->setCellValue('T10', 'G.R.')
//             ->setCellValue('U10', 'AGOSTO')
//             ->setCellValue('V10', 'G.R.')
//             ->setCellValue('W10', 'XIII MES')
//             ->setCellValue('X10', 'SEPTIEMBRE')
//             ->setCellValue('Y10', 'G.R.')
//             ->setCellValue('Z10', 'OCTUBRE')
//             ->setCellValue('AA10', 'G.R.')
//             ->setCellValue('AB10', 'NOVIEMBRE')
//             ->setCellValue('AC10', 'G.R.')
//             ->setCellValue('AD10', 'DICIEMBRE')
//             ->setCellValue('AE10', 'G.R.')
//             ->setCellValue('AF10', 'XIII MES')
//             ->setCellValue('AG10', 'AGUINALDO')
//             ->setCellValue('AH10', 'BONOS')
//             ->setCellValue('AI10', 'TOTAL SALARIO')
//             ->setCellValue('AJ10', 'TOTAL G.R.')
//             ->setCellValue('AK10', 'TOTAL XIII')
//             ->setCellValue('AL10', 'TOTAL SEGURO EDUCATIVO')
//             ->setCellValue('AM10', 'DESC. RENTA');
            
// cellColor('A10:AM10', 'FFFF00');
// $objPHPExcel->getActiveSheet()->getStyle('A10:AM10')->applyFromArray(allBordersThin());
// $sql = "SELECT DISTINCT np.ficha, np.cedula as cedula, np.apenom as nombre, np.seguro_social as seguro, np.fecing as fecha_ingreso
// FROM   salarios_acumulados nm 
// INNER JOIN nompersonal np on (nm.cedula=np.cedula)  AND nm.tipo_planilla='".$codtip."'
//                     ORDER BY np.apenom";
// $i=11;
// $ini=$i;
// $res=query($sql, $conexion);
// while($fila=fetch_array($res))
// {
//     $ficha=$fila['ficha'];
//     $cedula=$fila['cedula'];
//     $nombre= utf8_encode( $fila['nombre'] );
//     $enero=$febrero=$marzo=$abril=$mayo=$junio=$julio=$agosto=$septiembre=$octubre=$noviembre=$diciembre=0;
//     $gr_enero=$gr_febrero=$gr_marzo=$gr_abril=$gr_mayo=$gr_junio=$gr_julio=$gr_agosto=$gr_septiembre=$gr_octubre=$gr_noviembre=$gr_diciembre=0;
//     $xiii_mes_abril=$xiii_mes_agosto=$xiii_mes_diciembre=0;
//     $aguinaldos=$total_salario=$total_gr=$total_xiii_mes=$seguro_educativo=$desc_renta=0;
//     $sql_montos = "SELECT nm.salario_bruto as salario_bruto,nm.gtorep as gtorep,nm.xiii as xiii, MONTH(nm.fecha_pago) as mes
//             FROM   salarios_acumulados nm
//             WHERE   nm.ficha=".$ficha." AND YEAR(nm.fecha_pago)='".$ma1[2]."'";
//     $res_montos=query($sql_montos, $conexion);
    
//     while($fila_montos=fetch_array($res_montos))
//     {
//         if($fila_montos['mes']==1)
//         {
//             if($fila_montos['salario_bruto'])           
//                 $enero=$enero+$fila_montos['salario_bruto'];
         
//             if($fila_montos['gtorep'])            
//                 $gr_enero=$gr_enero+$fila_montos['gtorep'];
           
//         }
        
//         if($fila_montos['mes']==2)
//         {
//             if($fila_montos['salario_bruto'])           
//                 $febrero=$febrero+$fila_montos['salario_bruto'];
         
//             if($fila_montos['gtorep'])            
//                 $gr_febrero=$gr_febrero+$fila_montos['gtorep'];
           
//         }
        
//         if($fila_montos['mes']==3)
//         {
//             if($fila_montos['salario_bruto'])           
//                 $marzo=$marzo+$fila_montos['salario_bruto'];
         
//             if($fila_montos['gtorep'])            
//                 $gr_marzo=$gr_marzo+$fila_montos['gtorep'];
           
//         }
        
//         if($fila_montos['mes']==4)
//         {
//             if($fila_montos['salario_bruto'])           
//                 $abril=$abril+$fila_montos['salario_bruto'];
         
//             if($fila_montos['gtorep'])            
//                 $gr_abril=$gr_abril+$fila_montos['gtorep'];
            
//              if($fila_montos['xiii'])            
//                 $xiii_mes_abril=$xiii_mes_abril+$fila_montos['xiii'];
           
//         }
        
//         if($fila_montos['mes']==5)
//         {
//             if($fila_montos['salario_bruto'])           
//                 $mayo=$mayo+$fila_montos['salario_bruto'];
         
//             if($fila_montos['gtorep'])            
//                 $gr_mayo=$gr_mayo+$fila_montos['gtorep'];
           
//         }
        
//         if($fila_montos['mes']==6)
//         {
//             if($fila_montos['salario_bruto'])           
//                 $junio=$junio+$fila_montos['salario_bruto'];
         
//             if($fila_montos['gtorep'])            
//                 $gr_junio=$gr_junio+$fila_montos['gtorep'];
           
//         }
        
//         if($fila_montos['mes']==7)
//         {
//             if($fila_montos['salario_bruto'])           
//                 $julio=$julio+$fila_montos['salario_bruto'];
         
//             if($fila_montos['gtorep'])            
//                 $gr_julio=$gr_julio+$fila_montos['gtorep'];
           
//         }
        
//         if($fila_montos['mes']==8)
//         {
//             if($fila_montos['salario_bruto'])           
//                 $agosto=$agosto+$fila_montos['salario_bruto'];
         
//             if($fila_montos['gtorep'])            
//                 $gr_agosto=$gr_agosto+$fila_montos['gtorep'];
            
//               if($fila_montos['xiii'])            
//                 $xiii_mes_agosto=$xiii_mes_agosto+$fila_montos['xiii'];
           
//         }
        
//         if($fila_montos['mes']==9)
//         {
//             if($fila_montos['salario_bruto'])           
//                 $septiembre=$septiembre+$fila_montos['salario_bruto'];
         
//             if($fila_montos['gtorep'])            
//                 $gr_septiembre=$gr_septiembre+$fila_montos['gtorep'];
           
//         }
        
//         if($fila_montos['mes']==10)
//         {
//             if($fila_montos['salario_bruto'])           
//                 $octubre=$octubre+$fila_montos['salario_bruto'];
         
//             if($fila_montos['gtorep'])            
//                 $gr_octubre=$gr_octubre+$fila_montos['gtorep'];
           
//         }
        
//         if($fila_montos['mes']==11)
//         {
//             if($fila_montos['salario_bruto'])           
//                 $noviembre=$noviembre+$fila_montos['salario_bruto'];
         
//             if($fila_montos['gtorep'])            
//                 $gr_noviembre=$gr_noviembre+$fila_montos['gtorep'];
           
//         }
        
//         if($fila_montos['mes']==12)
//         {
//             if($fila_montos['salario_bruto'])           
//                 $diciembre=$diciembre+$fila_montos['salario_bruto'];
         
//             if($fila_montos['gtorep'])            
//                 $gr_diciembre=$gr_diciembre+$fila_montos['gtorep'];
            
//               if($fila_montos['xiii'])            
//                 $xiii_mes_diciembre=$xiii_mes_diciembre+$fila_montos['xiii'];
           
//         }
        
//         $total_salario=$enero+$febrero+$marzo+$abril+$mayo+$junio+$julio+$agosto+$septiembre+$octubre+$noviembre+$diciembre;
//         $total_gr=$gr_enero+$gr_febrero+$gr_marzo+$gr_abril+$gr_mayo+$gr_junio+$gr_julio+$gr_agosto+$gr_septiembre+$gr_octubre+$gr_noviembre+$gr_diciembre;
//         $total_xiii_mes=$xiii_mes_abril+$xiii_mes_agosto+$xiii_mes_diciembre;
//         $seguro_educativo=($total_salario+($total_gr*0.55))*0.0125;
        
//     }
    
//     $objPHPExcel->getActiveSheet()->setCellValue('A'.$i, $cedula);
//     $objPHPExcel->getActiveSheet()->setCellValue('B'.$i, '');
//     $objPHPExcel->getActiveSheet()->setCellValue('C'.$i, $nombre);
//     $objPHPExcel->getActiveSheet()->setCellValue('D'.$i, '');
//     $objPHPExcel->getActiveSheet()->setCellValue('E'.$i, '');
//     $objPHPExcel->getActiveSheet()->setCellValue('F'.$i, $enero);
//     $objPHPExcel->getActiveSheet()->setCellValue('G'.$i, $gr_enero);
//     $objPHPExcel->getActiveSheet()->setCellValue('H'.$i, $febrero);
//     $objPHPExcel->getActiveSheet()->setCellValue('I'.$i, $gr_febrero);
//     $objPHPExcel->getActiveSheet()->setCellValue('J'.$i, $marzo);
//     $objPHPExcel->getActiveSheet()->setCellValue('K'.$i, $gr_marzo);
//     $objPHPExcel->getActiveSheet()->setCellValue('L'.$i, $abril);
//     $objPHPExcel->getActiveSheet()->setCellValue('M'.$i, $gr_abril);
//     $objPHPExcel->getActiveSheet()->setCellValue('N'.$i, $xiii_mes_abril);
//     $objPHPExcel->getActiveSheet()->setCellValue('O'.$i, $mayo);
//     $objPHPExcel->getActiveSheet()->setCellValue('P'.$i, $gr_mayo);
//     $objPHPExcel->getActiveSheet()->setCellValue('Q'.$i, $junio);
//     $objPHPExcel->getActiveSheet()->setCellValue('R'.$i, $gr_junio);
//     $objPHPExcel->getActiveSheet()->setCellValue('S'.$i, $julio);
//     $objPHPExcel->getActiveSheet()->setCellValue('T'.$i, $gr_julio);
//     $objPHPExcel->getActiveSheet()->setCellValue('U'.$i, $agosto);
//     $objPHPExcel->getActiveSheet()->setCellValue('V'.$i, $gr_agosto);
//     $objPHPExcel->getActiveSheet()->setCellValue('W'.$i, $xiii_mes_agosto);
//     $objPHPExcel->getActiveSheet()->setCellValue('X'.$i, $septiembre);
//     $objPHPExcel->getActiveSheet()->setCellValue('Y'.$i, $gr_septiembre);
//     $objPHPExcel->getActiveSheet()->setCellValue('Z'.$i, $octubre);
//     $objPHPExcel->getActiveSheet()->setCellValue('AA'.$i, $gr_octubre);
//     $objPHPExcel->getActiveSheet()->setCellValue('AB'.$i, $noviembre);
//     $objPHPExcel->getActiveSheet()->setCellValue('AC'.$i, $gr_noviembre);
//     $objPHPExcel->getActiveSheet()->setCellValue('AD'.$i, $diciembre);
//     $objPHPExcel->getActiveSheet()->setCellValue('AE'.$i, $gr_diciembre);
//     $objPHPExcel->getActiveSheet()->setCellValue('AF'.$i, $xiii_mes_diciembre);
//     $objPHPExcel->getActiveSheet()->setCellValue('AG'.$i, '');
//     $objPHPExcel->getActiveSheet()->setCellValue('AH'.$i, '');
//     $objPHPExcel->getActiveSheet()->setCellValue('AI'.$i, $total_salario);
//     $objPHPExcel->getActiveSheet()->setCellValue('AJ'.$i, $total_gr);
//     $objPHPExcel->getActiveSheet()->setCellValue('AK'.$i, $total_xiii_mes);
//     $objPHPExcel->getActiveSheet()->setCellValue('AL'.$i,$seguro_educativo);
//     $objPHPExcel->getActiveSheet()->setCellValue('AM'.$i, '0');
    
//     $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':AM'.$i)->applyFromArray(allBordersThin());
//         //$objPHPExcel->getActiveSheet()->getStyle('E'.$i)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
//     $i++;
// }
// $objPHPExcel->getActiveSheet()->getStyle('F'.$i.':AM'.$i)->applyFromArray(allBordersThin());
// $objPHPExcel->getActiveSheet()->setCellValue('E'.$i, "TOTALES");
// $objPHPExcel->getActiveSheet()->getStyle('F'.$i.':AC'.$i)->getFont()->setBold(true);
// $objPHPExcel->getActiveSheet()->getStyle('E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
// $objPHPExcel->getActiveSheet()->setCellValue('F'.$i, "=SUM(F".$ini.":F".($i-1).")");
// $objPHPExcel->getActiveSheet()->setCellValue('G'.$i, "=SUM(G".$ini.":G".($i-1).")");
// $objPHPExcel->getActiveSheet()->setCellValue('H'.$i, "=SUM(H".$ini.":H".($i-1).")");
// $objPHPExcel->getActiveSheet()->setCellValue('I'.$i, "=SUM(I".$ini.":I".($i-1).")");
// $objPHPExcel->getActiveSheet()->setCellValue('J'.$i, "=SUM(J".$ini.":J".($i-1).")");
// $objPHPExcel->getActiveSheet()->setCellValue('K'.$i, "=SUM(K".$ini.":K".($i-1).")");
// $objPHPExcel->getActiveSheet()->setCellValue('L'.$i, "=SUM(L".$ini.":L".($i-1).")");
// $objPHPExcel->getActiveSheet()->setCellValue('M'.$i, "=SUM(M".$ini.":M".($i-1).")");
// $objPHPExcel->getActiveSheet()->setCellValue('N'.$i, "=SUM(N".$ini.":N".($i-1).")");
// $objPHPExcel->getActiveSheet()->setCellValue('O'.$i, "=SUM(O".$ini.":O".($i-1).")");
// $objPHPExcel->getActiveSheet()->setCellValue('P'.$i, "=SUM(P".$ini.":P".($i-1).")");
// $objPHPExcel->getActiveSheet()->setCellValue('Q'.$i, "=SUM(Q".$ini.":Q".($i-1).")");
// $objPHPExcel->getActiveSheet()->setCellValue('R'.$i, "=SUM(R".$ini.":R".($i-1).")");
// $objPHPExcel->getActiveSheet()->setCellValue('S'.$i, "=SUM(S".$ini.":S".($i-1).")");
// $objPHPExcel->getActiveSheet()->setCellValue('T'.$i, "=SUM(T".$ini.":T".($i-1).")");
// $objPHPExcel->getActiveSheet()->setCellValue('U'.$i, "=SUM(U".$ini.":U".($i-1).")");
// $objPHPExcel->getActiveSheet()->setCellValue('V'.$i, "=SUM(V".$ini.":V".($i-1).")");
// $objPHPExcel->getActiveSheet()->setCellValue('W'.$i, "=SUM(W".$ini.":W".($i-1).")");
// $objPHPExcel->getActiveSheet()->setCellValue('X'.$i, "=SUM(X".$ini.":X".($i-1).")");
// $objPHPExcel->getActiveSheet()->setCellValue('Y'.$i, "=SUM(Y".$ini.":Y".($i-1).")");
// $objPHPExcel->getActiveSheet()->setCellValue('Z'.$i, "=SUM(Z".$ini.":Z".($i-1).")");
// $objPHPExcel->getActiveSheet()->setCellValue('AA'.$i, "=SUM(AA".$ini.":AA".($i-1).")");
// $objPHPExcel->getActiveSheet()->setCellValue('AB'.$i, "=SUM(AB".$ini.":AB".($i-1).")");
// $objPHPExcel->getActiveSheet()->setCellValue('AC'.$i, "=SUM(AC".$ini.":AC".($i-1).")");
// $objPHPExcel->getActiveSheet()->setCellValue('AD'.$i, "=SUM(AD".$ini.":AD".($i-1).")");
// $objPHPExcel->getActiveSheet()->setCellValue('AE'.$i, "=SUM(AE".$ini.":AE".($i-1).")");
// $objPHPExcel->getActiveSheet()->setCellValue('AF'.$i, "=SUM(AF".$ini.":AF".($i-1).")");
// $objPHPExcel->getActiveSheet()->setCellValue('AG'.$i, "=SUM(AG".$ini.":AG".($i-1).")");
// $objPHPExcel->getActiveSheet()->setCellValue('AH'.$i, "=SUM(AH".$ini.":AH".($i-1).")");
// $objPHPExcel->getActiveSheet()->setCellValue('AI'.$i, "=SUM(AI".$ini.":AI".($i-1).")");
// $objPHPExcel->getActiveSheet()->setCellValue('AJ'.$i, "=SUM(AJ".$ini.":AJ".($i-1).")");
// $objPHPExcel->getActiveSheet()->setCellValue('AK'.$i, "=SUM(AK".$ini.":AK".($i-1).")");
// $objPHPExcel->getActiveSheet()->setCellValue('AL'.$i, "=SUM(AL".$ini.":AL".($i-1).")");
// $objPHPExcel->getActiveSheet()->setCellValue('AM'.$i, "=SUM(AM".$ini.":AM".($i-1).")");

// //$objPHPExcel->getActiveSheet()->setCellValue('J'.$i, $total_prima);
// //$objPHPExcel->getActiveSheet()->setCellValue('K'.$i, $total_indemnizacion);
// //$objPHPExcel->getActiveSheet()->setCellValue('L'.$i, $total_total);
// $i=$i+7;

// $objPHPExcel->getActiveSheet()->mergeCells('E'.$i.':G'.($i+4));
// $objPHPExcel->getActiveSheet()->getStyle('E'.$i.':G'.($i+4))->applyFromArray(allBordersThin());
// $objPHPExcel->getActiveSheet()->mergeCells('H'.$i.':J'.($i+4));
// $objPHPExcel->getActiveSheet()->getStyle('H'.$i.':J'.($i+4))->applyFromArray(allBordersThin());
// $objPHPExcel->getActiveSheet()->mergeCells('K'.$i.':M'.($i+4));
// $objPHPExcel->getActiveSheet()->getStyle('K'.$i.':M'.($i+4))->applyFromArray(allBordersThin());
// $i=$i+5;
// $objPHPExcel->getActiveSheet()->mergeCells('E'.$i.':G'.$i);
// $objPHPExcel->getActiveSheet()->getStyle('E'.$i.':G'.$i)->applyFromArray(allBordersThin());
// $objPHPExcel->getActiveSheet()->setCellValue('E'.$i, 'RECIBIDO POR');
// $objPHPExcel->getActiveSheet()->getStyle('E'.$i)->getFont()->setBold(true);
// $objPHPExcel->getActiveSheet()->getStyle('E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
// $objPHPExcel->getActiveSheet()->mergeCells('H'.$i.':J'.$i);
// $objPHPExcel->getActiveSheet()->getStyle('H'.$i.':J'.$i)->applyFromArray(allBordersThin());
// $objPHPExcel->getActiveSheet()->setCellValue('H'.$i, 'AUTORIZADO POR');
// $objPHPExcel->getActiveSheet()->getStyle('H'.$i)->getFont()->setBold(true);
// $objPHPExcel->getActiveSheet()->getStyle('H'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
// $objPHPExcel->getActiveSheet()->mergeCells('K'.$i.':M'.$i);
// $objPHPExcel->getActiveSheet()->getStyle('K'.$i.':M'.$i)->applyFromArray(allBordersThin());
// $objPHPExcel->getActiveSheet()->setCellValue('K'.$i, 'PREPARADO POR');
// $objPHPExcel->getActiveSheet()->getStyle('K'.$i)->getFont()->setBold(true);
// $objPHPExcel->getActiveSheet()->getStyle('K'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);


// $objPHPExcel->getActiveSheet()->setSelectedCells('B100');


// $objPHPExcel->setActiveSheetIndex(0);

// Redirect output to a client’s web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="Reporte_Formato_diligencial.xls"');
header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header ('Pragma: public'); // HTTP/1.0

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

$objWriter->save('php://output');

$fecha_inicio = date('Y-m-d',strtotime($mesano1));
$fecha_fin = date('Y-m-d',strtotime($mesano2));
$filename = 'excel/anexo03 '.$fecha_inicio. ' '.$fecha_fin.'.xlsx';

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');   

$objWriter->save($filename);

echo $filename;


exit;

?>




