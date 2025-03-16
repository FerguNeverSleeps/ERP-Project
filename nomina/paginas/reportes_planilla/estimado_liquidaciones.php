<?php
session_start();
error_reporting(1);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('America/Panama');

if (PHP_SAPI == 'cli')
    die('Este ejemplo solo se debe ejecutar desde un navegador Web');

include('../../lib/common.php');
include('../lib/php_excel.php');

require_once 'planillaClass.php';
require_once '../phpexcel/Classes/PHPExcel.php';

$conexion= new bd($_SESSION['bd']);

$objPlanilla = new PlanillaClass($conexion);
$objPHPExcel = new PHPExcel();
$objPHPExcel = PHPExcel_IOFactory::load("../plantillas/liquidaciones.xlsx");

    $fecha = date('Y-m-d');
    $len = 22;
    $tam1 = $tam2 = $tam3 =[
        "codigo"      => ($len-15),
        "nombre"      => ($len+45),
        "ingreso"     => ($len-3),
        "liq1"        => ($len-3),
        "anio_trab"   => ($len-5),
        "vac1"        => ($len-3),
        "xiii"        => ($len-3),
        "vac_gr"      => ($len-3),
        "xiii_gr"     => ($len-3),
        "prima"       => ($len-3),
        "indem"       => ($len-3),
        "ingreso6per" => ($len-3),
        "bono"        => $len,
        "totales"     => $len,
        "total"       => 25
    ];

    //$NOMINA = $fila['descrip']; 
    //$NOMINA = $objPlanilla->setNomina($codtip);

    $objPHPExcel->getProperties()->setCreator("Selectra")
                                ->setLastModifiedBy("Selectra")
                                ->setTitle("Horizontal de Planilla");

    $objPHPExcel->getDefaultStyle()->getFont()->setName('Calibri');
    $objPHPExcel->getDefaultStyle()->getFont()->setSize(11);
    //cargar configuracion de reporte
    $sql1 = "select * from config_reportes_planilla where id = 1";
    $res=$objPlanilla->db->query($sql1);
    $configReporte=$res->fetch_array();
    $sql_reporte = $configReporte['sql_reporte'];


    $sql = "SELECT e.nom_emp as empresa, e.rif, e.tel_emp as telefono, e.dir_emp as direccion, e.ciu_emp as ciudad, 
                e.edo_emp, e.imagen_izq as logo
            FROM   nomempresa e";
    $res=$objPlanilla->db->query($sql);
    $fila=$res->fetch_array();
    $logo=$fila['logo'];

    /*$sql2 = "SELECT np.periodo_ini as desde, np.periodo_fin as hasta, np.mes, np.anio,np.status,
                    DATE_FORMAT(np.periodo_ini,'%d') as dia_ini, DATE_FORMAT(np.periodo_fin, '%d') as dia_fin   
            FROM nom_nominas_pago np 
            WHERE  np.codnom=".$codnom." AND np.tipnom=".$codtip;
    //$res2=$objPlanilla->db->query($sql2);
    //$fila2=$res2->fetch_array();*/

    $meses = array('Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre');

    /*$desde=$fila2['desde'];
    $hasta=$fila2['hasta'];
    $dia_ini = $fila2['dia_ini'];
    $dia_fin = $fila2['dia_fin']; 
    $mes_numero = $fila2['mes'];
    $mes_letras = $meses[$mes_numero - 1];
    $anio = $fila2['anio'];
    $status = $fila2['status'];
    if($status=='A')
        $titulo1 = strtoupper("ESTIMADO DE LIQUIDACIONES");
    else*/
        $titulo1 = strtoupper("ESTIMADO DE LIQUIDACIONES");

    $empresa = utf8_encode($fila['empresa']);
    $objPHPExcel->getActiveSheet()
            ->setCellValue('C2', strtoupper($empresa) )
            ->setCellValue('C3', strtoupper("ESTIMADO DE LIQUIDACIONES"))
            ->setCellValue('C4', strtoupper("CALCULADO HASTA EL ÚLTIMO DÍA PAGADO"))
            ->setCellValue('C5', "" );

    //$objPHPExcel->getActiveSheet()->getStyle('A2:A4')->getFont()->setName('Calibri');
    $objPHPExcel->getActiveSheet()->getStyle('C2:O5')->getFont()->setSize(14);
    $objPHPExcel->getActiveSheet()->getStyle('C2:O5')->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('C2:O5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->mergeCells('C2:O2');
    $objPHPExcel->getActiveSheet()->mergeCells('C3:O3');
    $objPHPExcel->getActiveSheet()->mergeCells('C4:O4');
    $objPHPExcel->getActiveSheet()->mergeCells('C5:O5');

    $excelLetra = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V');
    //columnas reporte
    $sqlIng = "select * from config_reportes_planilla_columnas where visible = 1 and tipo = 0 and id_reporte =".$configReporte['id']." order by col_orden";
    $resCol=$objPlanilla->db->query($sqlIng);

    $colExcelIni = 2;
    $colExcelActual = 2;
    $filaExcelIni = 8;
    $filaExcelActual = 8;

    $objPHPExcel->getActiveSheet()
            ->setCellValue('A'.$filaExcelActual, 'CÓDIGO')
            ->setCellValue('B'.$filaExcelActual, 'NOMBRE')
            ->setCellValue('C'.$filaExcelActual, 'INGRESO')
            ->setCellValue('D'.$filaExcelActual, 'LIQUIDACION')
            ->setCellValue('E'.$filaExcelActual, 'AÑOS TRAB.')
            ->setCellValue('F'.$filaExcelActual, 'SALARIOS')
            ->setCellValue('G'.$filaExcelActual, 'VACACIONES PROP.')
            ->setCellValue('H'.$filaExcelActual, 'XIII MES PROP')
            ->setCellValue('I'.$filaExcelActual, 'VACACION GP')
            ->setCellValue('J'.$filaExcelActual, 'XIII MES GP')
            ->setCellValue('K'.$filaExcelActual, 'PRIMA ANTIG.')
            ->setCellValue('L'.$filaExcelActual, 'INDEMIZACION')
            ->setCellValue('M'.$filaExcelActual, 'INGRESO DEL 6%')
            ->setCellValue('N'.$filaExcelActual, 'BONO DE ASISTENCIA')
            ->setCellValue('O'.$filaExcelActual, 'TOTAL');
    
    //ACA VAN LOS VALORES DEL OTRO REPORTE PARA CADA EMPLEADO

    $colFinDesc=$excelLetra[15].'8';
    $objPHPExcel->getActiveSheet()->setCellValue($colFinDesc, '          ');
    $objPHPExcel->getActiveSheet()->getStyle('A'.$filaExcelActual.':'.$colFinDesc)->getBorders()->getBottom()
                        ->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
                        
    $objPHPExcel->getActiveSheet()->getStyle('A6:'.$colFinDesc )->getFont()->setSize(12);
    $objPHPExcel->getActiveSheet()->getStyle('A6:'.$colFinDesc )->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('A6:'.$colFinDesc )->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $objPHPExcel->getActiveSheet()->freezePane('C9'); // Inmovilizar Paneles

    $sql = "SELECT codorg, descrip
        FROM   nomnivel1
        WHERE  1
        ORDER BY codorg";
    $res1=$objPlanilla->db->query($sql);

    $i=$filaExcelActual=9; $nivel=1;
    
    while($row1=$res1->fetch_array())
    {
        $codorg = $row1['codorg'];
        //echo $row1['descrip'];exit;
        $objPHPExcel->getActiveSheet()->setCellValue('A'.$filaExcelActual, $row1['descrip']);
        $objPHPExcel->getActiveSheet()->mergeCells('A'.$filaExcelActual.':B'.$filaExcelActual);
        $objPHPExcel->getActiveSheet()->getStyle('A'.$filaExcelActual.':B'.$filaExcelActual)->getFont()->setSize(12);
        $objPHPExcel->getActiveSheet()->getStyle('A'.$filaExcelActual.':C'.$filaExcelActual)->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('A'.$filaExcelActual)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $filaExcelActual++;
        $i++;
        $enc=true;

        //ajustes desde el pdf
        
        $fecha = $objPlanilla->ConvertirFechaYMD($fecha);

        $fecha_anio = date("Y-m-d",strtotime($fecha."- 1 year"));
        $sql_max = "
        SELECT 
            max(sa.cod_planilla) as codnom, 
            sa.tipo_planilla as codtip,
            tip.descrip
        FROM 
            salarios_acumulados sa
        LEFT join
            nomtipos_nomina tip on (sa.tipo_planilla=tip.codtip)
        WHERE 
            sa.fecha_pago <= '{$fecha}'
        GROUP BY sa.tipo_planilla";

        $res_max = $objPlanilla->db->query($sql_max);

        $total_salario       = 0;
        $total_comision      = 0;
        $total_sobretiempo   = 0;
        $total_bono          = 0;
        $total_altura        = 0;
        $total_otros         = 0;
        $total_vacac         = 0;
        $total_xiii          = 0;
        $total_util          = 0;
        $total_liquida       = 0;
        $total_licencia      = 0;
        $total_gtorep        = 0;
        $total_otros_ing     = 0;
        $total_adelanto      = 0;
        $total_s_s           = 0;
        $total_s_e           = 0;
        $total_islr          = 0;
        $total_acreedor_suma = 0;
        $total_Neto          = 0;
        $total_indem         = 0;
        $total_prima         = 0;
        $total_xiii_gtorep   = 0;
        $total_vacac_gtorep  = 0;
        
        while($fila = $res_max->fetch_assoc()){
            $tasa_prima = 0.01923;
            $tasa_indem = 3.4;
            $strsql= "SELECT 
                        sa.ficha,
                        sa.tipo_planilla,
                        np.suesal, 
                        np.apenom, 
                        np.tipnom , 
                        np.fecing , 
                        np.inicio_periodo ,
                        np.hora_base
                    from 
                        salarios_acumulados sa
                    left join 
                        nompersonal np on (sa.ficha = np.ficha) AND (np.tipnom = sa.tipo_planilla)
                    where 
                        sa.tipo_planilla = '".$fila['codtip']."' AND sa.fecha_pago between '{$fecha_anio}' AND '{$fecha}' and np.estado not like '%Egresado%'
                        AND np.fecharetiro = '0000-00-00' and np.codnivel1='{$codorg}'
                    group by 
                        sa.ficha,np.tipnom";
            $res =$objPlanilla->db->query($strsql);   


            while($row = $res->fetch_assoc()) {
                $FICHA = $row['ficha'];
                $SUELDO = $row['suesal'];
                $TIPNOM =  $row['tipnom'];

                if($row['tipo_planilla'] == '1'){
                    $FECHAINGRESO = $row['fecing'];
                    $HORABASE     = $row['hora_base'];

                }else{
                    $FECHAINGRESO = $row['inicio_periodo'];
                    $HORABASE     = $row['hora_base'];

                }
                $consulta_acumulado = "SELECT 
                    sa.ficha,
                    sum(sa.salario_bruto) sum_sal,
                    sum(sa.vacac) sum_vacac,
                    sum(sa.xiii) sum_xiii,
                    sum(sa.gtorep) sum_gtorep,
                    sum(sa.xiii_gtorep) sum_xiii_gtorep,
                    sum(sa.liquida) sum_liquida,
                    sum(sa.bono) sum_bono,
                    sum(sa.otros_ing) sum_otros_ing,
                    sum(sa.s_s) sum_s_s,
                    sum(sa.s_e) sum_s_e,
                    sum(sa.islr) sum_islr,
                    sum(sa.islr_gr) sum_islr_gr,
                    sum(sa.acreedor_suma) sum_acreedor_suma,
                    sum(sa.Neto) sum_Neto,
                    sa.tipo_planilla 
                FROM
                    salarios_acumulados sa
                WHERE 
                    sa.ficha = '{$FICHA}' AND sa.tipo_planilla = '".$TIPNOM."'  AND sa.fecha_pago between '{$FECHAINGRESO}' AND '{$fecha}'";
                $res_acum =$objPlanilla->db->query($consulta_acumulado); 

                $row_acum = $res_acum->fetch_assoc();
                
                $FECHANOMINA = $fecha;

                $ANIOFECHA = date('Y',strtotime($FECHANOMINA));
                $TIPOLIQUIDACION = 4;

                # Antiguedad en Dias
                $T01= $objPlanilla->antiguedad($FECHAINGRESO,$FECHANOMINA,"D");
                $T02=$T01/365;
                $anio_traba = round($T02,2);

                #Salario en los ultimos 6 meses
                $FECHA_CALCULO =  date("d-m-Y",strtotime($FECHANOMINA."- 6 month"));
                $FECHA_XII =  date("d-m-Y",strtotime($FECHANOMINA."- 4 month"));
                $T12=$objPlanilla->acumsalseismesessalacum($FICHA,$FECHA_CALCULO,$FECHANOMINA);
                $T03=$objPlanilla->acumsalseismeses($FICHA,"SI",$FECHA_CALCULO,$FECHANOMINA);
                $T04=($T12/6)/4.333;

                #Salario ultimo fecha
                $T05= $objPlanilla->salariointegralultimomes($FICHA,"SI",$FECHA_CALCULO,$FECHANOMINA);
                $T06=$T05/4.333;

                $T07=$objPlanilla->SI("$T02>10",($T02-10),0);
                $T08=$objPlanilla->SI("$T06>$T04",$T06,$T04);

                $T09=$objPlanilla->SI("$TIPOLIQUIDACION<>4",0,1);

                //Vacaciones Proporcionales
                $REF_VAC = $objPlanilla->vac_panama_dpendiente($FICHA,$ANIOFECHA);

                $VAC01= $objPlanilla->acumcomvacpanama($FICHA,"SI",$FECHANOMINA);
                $VAC04= $objPlanilla->acumcom($FICHA,"614",$FECHAINGRESO,$FECHANOMINA);
                $VAC05= $objPlanilla->acumcom($FICHA,"616",$FECHAINGRESO,$FECHANOMINA);
                $VAC06= $objPlanilla->acumcom($FICHA,"618",$FECHAINGRESO,$FECHANOMINA);

                $VAC011=$VAC01/11;
                $VAC07=$SUELDO;
                $VAC08=($VAC01+$VAC06)/11;

                $VAC09=$objPlanilla->SI("$VAC07>$VAC08",$VAC07,$VAC08);
                $VAC10=$objPlanilla->SI("$VAC09>$VAC011",$VAC09,$VAC011);

                $VAC11=$VAC10/30;

                $MONTO_VAC = $objPlanilla->calcularvacacionproporcional($FICHA,$FECHAINGRESO, $FECHANOMINA,$TIPNOM,$ANIOFECHA,$SUELDO,$HORABASE);

                if($FECHA_XII > $FECHAINGRESO)
                {
                    $FECHA_XII = $FECHAINGRESO;
                }    
                //$CESANTIA = $objPlanilla->calcularcesantia($FICHA,$FECHA_XII,$FECHANOMINA,$TIPNOM,$ANIOFECHA);
                $CESANTIA =0;
                if($TIPNOM == 1)
                {
                    $BONO_ASISTENCIA = 0;
                    $CESANTIA = 0;
                    $MONTO_XIII = $objPlanilla->calcularxiii($FICHA,$FECHA_XII,$FECHANOMINA,$TIPNOM);
                    $GRXIII=$objPlanilla->calculargrxiii($FICHA,$FECHA_XII,$FECHANOMINA,$TIPNOM);
                    $VACXIII=$objPlanilla->calcularvacxiii($FICHA,$FECHA_XII,$FECHANOMINA,$TIPNOM,$ANIOFECHA);
                    $prima=$objPlanilla->calcularprima($FICHA,$FECHAINGRESO,$FECHANOMINA,$TIPNOM);
                    $indem=$objPlanilla->SI("$T02>10",((($T08*10*3.4)+($T08*$T07))*$T09),($T08*$T02*3.4)*$T09);
                }
                else 
                {
                    $FECHA_13 = $objPlanilla->periodoxiii($FECHANOMINA);
                    #Si la fecha inicio de contrato es antes del decimo
                    if($FECHAINGRESO > $FECHA_13)
                    {
                        $FECHA_XII = $FECHAINGRESO;
                        $bruto = $row_acum['sum_sal'];
                        $MONTO_VAC = ($bruto / 11);
                        $MONTO_XIII = (($bruto + $MONTO_VAC)/12);
                    }
                    else
                    {
                        $MONTO_XIII = $objPlanilla->calcularxiii($FICHA,$FECHAINGRESO,$FECHANOMINA,$TIPNOM);

                    }
                    $GRXIII=$objPlanilla->calculargrxiii($FICHA,$FECHA_XII,$FECHANOMINA,$TIPNOM);
                    $VACXIII=$objPlanilla->calcularvacxiii($FICHA,$FECHA_XII,$FECHANOMINA,$TIPNOM,$ANIOFECHA);
                    if($T01>30)
                    {
                        $BONO_ASISTENCIA = $objPlanilla->calcularbonoasistencia($FICHA,$FECHAINGRESO,$FECHANOMINA,$TIPNOM,$ANIOFECHA,$SUELDO);
                    }
                    else
                    {
                        $BONO_ASISTENCIA =0;
                    }
                    $CESANTIA = $MONTO_VAC*0.06;
                    $prima = 0;
                    $indem = 0;
                }
                $total_salario       += $row_acum['sum_sal'];
                $total_prima         += $prima;
                $total_indem         += $indem;
                $total_bono          += $BONO_ASISTENCIA;
                $total_vacac_gtorep  += $VACXIII;
                $total_vacac         += $MONTO_VAC;
                $total_xiii          += $MONTO_XIII;
                $total_xiii_gtorep   += $GRXIII;
                $total_gtorep        += $row_acum['sum_gtorep'];
                $total_otros_ing     += $CESANTIA;
                $total_s_s           += $row_acum['sum_s_s'];
                $total_s_e           += $row_acum['sum_s_e'];
                $total_islr          += $row_acum['sum_islr'];
                $total_acreedor_suma += $row_acum['sum_acreedor_suma'];
                $subtotal=0;

                $subtotal += $indem+$BONO_ASISTENCIA+$CESANTIA+$VACXIII+$MONTO_VAC+$MONTO_XIII+$GRXIII+$prima+$acum_liq;
                $total_subtotal+=$subtotal;
                
                $total_Neto          += $subtotal;
                

                $objPHPExcel->getActiveSheet()
                        ->setCellValue('A'.$filaExcelActual, $row['ficha'] )
                        ->setCellValue('B'.$filaExcelActual, utf8_encode($row['apenom']) )
                        ->setCellValue('C'.$filaExcelActual, date('d-m-Y',strtotime($FECHAINGRESO)) )
                        ->setCellValue('D'.$filaExcelActual, date('d-m-Y',strtotime($fecha)) )
                        ->setCellValue('E'.$filaExcelActual, $anio_traba )
                        ->setCellValue('F'.$filaExcelActual, number_format($row_acum['sum_sal'],2,".",",") )
                        ->setCellValue('G'.$filaExcelActual, number_format($MONTO_VAC,2,".",",") )
                        ->setCellValue('H'.$filaExcelActual, number_format($MONTO_XIII,2,".",",") )
                        ->setCellValue('I'.$filaExcelActual, number_format($VACXIII,2,".",",") )
                        ->setCellValue('J'.$filaExcelActual, number_format($GRXIII,2,".",",") )
                        ->setCellValue('K'.$filaExcelActual, number_format($prima,2,".",",") )
                        ->setCellValue('L'.$filaExcelActual, number_format($indem,2,".",",") )
                        ->setCellValue('M'.$filaExcelActual, number_format($CESANTIA,2,".",",") )
                        ->setCellValue('N'.$filaExcelActual, number_format($BONO_ASISTENCIA,2,".",",") )
                        ->setCellValue('O'.$filaExcelActual, number_format($subtotal,2,".",",") );
                
                $filaExcelActual++;
                $i++;
                
            }//iteracion empleados codnivel1 X

        }//iteracion de planillas
        
        $objPHPExcel->getActiveSheet()
            ->setCellValue('A'.$filaExcelActual, "" )
            ->setCellValue('B'.$filaExcelActual, "" )
            ->setCellValue('C'.$filaExcelActual, "" )
            ->setCellValue('D'.$filaExcelActual, "" )
            ->setCellValue('E'.$filaExcelActual, "" )
            ->setCellValue('F'.$filaExcelActual, number_format( $total_salario ,2,".",",") )
            ->setCellValue('G'.$filaExcelActual, number_format( $total_vacac ,2,".",",") )
            ->setCellValue('H'.$filaExcelActual, number_format( $total_xiii ,2,".",",") )
            ->setCellValue('I'.$filaExcelActual, number_format( $total_vacac_gtorep ,2,".",",") )
            ->setCellValue('J'.$filaExcelActual, number_format( $total_xiii_gtorep ,2,".",",") )
            ->setCellValue('K'.$filaExcelActual, number_format( $total_prima ,2,".",",") )
            ->setCellValue('L'.$filaExcelActual, number_format( $total_indem ,2,".",",") )
            ->setCellValue('M'.$filaExcelActual, number_format( $total_otros_ing ,2,".",",") )
            ->setCellValue('N'.$filaExcelActual, number_format( $total_bono ,2,".",",") )
            ->setCellValue('O'.$filaExcelActual, number_format( $total_Neto ,2,".",",") );
        $objPHPExcel->getActiveSheet()->getStyle('A'.$filaExcelActual.':O'.$filaExcelActual)->getFont()->setBold(true);
        $filaExcelActual++;
        $i++;
    }//iteracion niveles
    
    $i++;


//==================================================================================================
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(32);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setAutoSize(true);


$objPHPExcel->getActiveSheet()->setTitle($dia_fin.' '.$mes_letras);

//==========================================================================
//$NOMINA = str_replace(' ', '', $NOMINA);
$filename = "estimado_liquidacion_".date('d-m-Y',strtotime($fecha));
header( "Content-type: application/vnd.ms-excel; charset=UTF-8" );
// Redirect output to a client’s web browser (Excel5)
//header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="'.$filename.'.xls"');
header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header ('Pragma: public'); // HTTP/1.0

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
/* Limpiamos el búfer */
ob_end_clean();
$objWriter->save('php://output');

exit;
