<?php
session_start();
ob_start();

require('../../nomina/tcpdf/tcpdf.php');
include("../../nomina/lib/common.php");
date_default_timezone_set('America/Panama');
require_once('../../nomina/lib/database.php');
$db = new Database($_SESSION['bd']);
//--------------------------------------------------------------------------------------------------------------------------------------
$codtip = isset($_GET['codtip'])?$_GET['codtip']:NULL;
$codnom = isset($_GET['codnom'])?$_GET['codnom']:NULL;
//--------------------------------------------------------------------------------------------------------------------------------------
//consulta de datos
$sql = "SELECT nmn.ficha
FROM nom_nominas_pago nnp 
INNER JOIN nom_movimientos_nomina nmn on (nnp.codnom = nmn.codnom and nnp.codtip = nmn.tipnom) 
WHERE nnp.frecuencia = '10' AND nnp.codnom = '{$codnom}' AND nnp.codtip = '{$codtip}' 
group by nmn.ficha";
$res = $db->query($sql);
while($fila = $res->fetch_assoc()){
    $data[] = $fila;
}
function justificacion($jus)
{
	if($jus==5)
	{
		return "Incapacidad";
	}
	if($jus==6)
	{
		return "Incapacidad por discapacidad";
	}
	if($jus==8)
	{
		return "Incapacidad por familiar discapacitado";
	}
}

//------------------------------------------------------------------------------------------------------------------------------------
$sql2="SELECT * FROM nomempresa";
$res2 = $db->query($sql2);
$empresa = mysqli_fetch_array($res2);
//--------------------------------------------------------------------------------------------------------------------------------------
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('America/Panama');

function fechas($fecha,$formato)
{
    $meses = array("ENERO","FEBRERO","MARZO","ABRIL","MAYO","JUNIO","JULIO","AGOSTO","SEPTIEMBRE","OCTUBRE","NOVIEMBRE","DICIEMBRE");
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
        default:
            $f = date('Y');
            break;
    }
    return $f;
}
class MYPDF extends TCPDF
{   
    public function Header() 
    {
        
    }
    public function Footer()
    {

    }
    
    function setEmpresa()
    {
        $amaxonia = new bd($_SESSION['bd']);
         $consulta = "SELECT * from nomempresa"; 
        $resultado = $amaxonia->query($consulta);
        $fila = $resultado->fetch_assoc();
        if ($resultado->num_rows <= 0) {
            return 0;
        } else {
            $this->empresa =  $fila['nom_emp'];
        }
    }
    public function ConvertirFecha($fecha)
    {
        return date("d/m/Y",strtotime($fecha));
    }
    public function ConvertirFechaYMD($fecha)
    {
        return date("Y-m-d",strtotime($fecha));
    }
    public function antiguedad($fecha1, $fecha2, $tipo) {
        if ($fecha1 > $fecha2) {
            return 0;
        }
        if ($tipo == "") {
            return 0;
        }
        if ($fecha1 == "0000-00-00") {
            return 0;
        }
        $ano1 = substr($fecha1, 0, 4);
        $mes1 = substr($fecha1, 5, 2);
        $dia1 = substr($fecha1, 8, 2);

        $ano2 = substr($fecha2, 0, 4);
        $mes2 = substr($fecha2, 5, 2);
        $dia2 = substr($fecha2, 8, 2);

        if ($dia1 > $dia2) {
            $dia2+=30;
            $mes2-=1;
            if ($mes2 < 0) {
                $mes2 = 12;
                $ano2-=1;
            }
        }

        //calculo timestam de las dos fechas
        $timestamp1 = mktime(0, 0, 0, $mes1, $dia1, $ano1);
        $timestamp2 = mktime(0, 0, 0, $mes2, $dia2, $ano2);

        //resto a una fecha la otra
        $segundos_diferencia = $timestamp1 - $timestamp2;
        //convierto segundos en dÃ­as
        $dias = $segundos_diferencia / (60 * 60 * 24);

        //obtengo el valor absoulto de los dÃ­as (quito el posible signo negativo)
        $dias = abs($dias);

        //quito los decimales a los dÃ­as de diferencia
        $dias = floor($dias);

        if ($mes1 > $mes2) {
            $mes2+=12;
            $ano2-=1;
        }
        $meses = $mes2 - $mes1;
        $anios = $ano2 - $ano1;

        switch ($tipo) {
            case "A":
                return $anios;
                break;
            case "M":
                return $meses;
                break;
            case "D":
                return $dias;
                break;
        }
    }


    public function acumsalseismesessalacum($FICHA, $fecha_inicio, $fecha_fin) {

        $amaxonia = new bd($_SESSION['bd']);

        $fechames11 = date('Y-m-d', strtotime('-6 month')) ; // resta 6 mes

        $consulta = "SELECT (SUM(salario_bruto)+SUM(vacac)+SUM(gtorep)) as monto 
        from salarios_acumulados where ficha='".$FICHA."' and fecha_pago >='$fechames11' and fecha_pago <='$fecha_fin'"; 

        $resultado = $amaxonia->query($consulta);
        $fila = $resultado->fetch_assoc();
        if ($resultado->num_rows <= 0) {
            return 0;
        } else {
            return $fila['monto'];
        }

    }
        public function acumsal60mesessalacum($FICHA, $fecha_inicio, $fecha_fin) {

        $amaxonia = new bd($_SESSION['bd']);

        $fechames11 = date('Y-m-d', strtotime('-60 month')) ; // resta 60 mes

        $consulta = "SELECT (SUM(salario_bruto)+SUM(vacac)+SUM(gtorep)) as monto 
        from salarios_acumulados where ficha='".$FICHA."' and fecha_pago >='$fechames11' and fecha_pago <='$fecha_fin'"; 

        $resultado = $amaxonia->query($consulta);
        $fila = $resultado->fetch_assoc();
        if ($resultado->num_rows <= 0) {
            return 0;
        } else {
            return $fila['monto'];
        }

    }

    public function acumsalseismeses($FICHA,$tipo, $fecha_inicio, $fecha_fin) {
        $amaxonia = new bd($_SESSION['bd']);
         
        $fechames11 = date('Y-m-d', strtotime('-6 month')) ; // resta 6 mes
        $consulta = "SELECT ifnull(sum(monto) ,0) as monto 
        FROM nom_nominas_pago 
        INNER JOIN nom_movimientos_nomina ON nom_nominas_pago.codnom = nom_movimientos_nomina.codnom AND nom_movimientos_nomina.tipnom = nom_nominas_pago.codtip 
        INNER JOIN nomconceptos_acumulados ON nom_movimientos_nomina.codcon = nomconceptos_acumulados.codcon 
        WHERE ficha='$FICHA' AND cod_tac='$tipo' and periodo_ini>='$fechames11' and periodo_fin<='$fecha_fin'" ;
         
        $resultado = $amaxonia->query($consulta);
        $fila = $resultado->fetch_array();
        $suma = $fila['monto'];
        return $suma;
    }

    public function salariointegralultimomes($FICHA,$tipo, $fecha_inicio, $fecha_fin) {
        
        $amaxonia     = new Database($_SESSION['bd']);
        //$conexion = conexion();
         
        $fechames11 = date('Y-m-d', strtotime('-1 month')) ; // resta 6 mes
        $fechahoy=date('Y-m-d');
        $consulta = "SELECT ifnull(sum(monto) ,0) as monto 
        FROM nom_nominas_pago 
        INNER JOIN nom_movimientos_nomina ON nom_nominas_pago.codnom = nom_movimientos_nomina.codnom AND nom_movimientos_nomina.tipnom = nom_nominas_pago.codtip 
        INNER JOIN nomconceptos_acumulados ON nom_movimientos_nomina.codcon = nomconceptos_acumulados.codcon 
        WHERE ficha='$FICHA' AND cod_tac='$tipo' and periodo_ini>='$fechames11' and periodo_fin<='$fechahoy'  and status='C'" ;
         
        $resultado = $amaxonia->query($consulta);
        $fila = $resultado->fetch_array();
        $suma = $fila['monto'];
        return $suma;
    }
    public function si($condicion, $v, $f) {

        if ($condicion == "") {
            return 0;
        }

        if($f == "") $f=0;

        $if = "if(" . $condicion . "){\$retorno=" . $v . ";}else{\$retorno=" . $f . ";}";

        eval($if);
        return $retorno;
    }

    public function acumuladoliquidacion($codcon, $fecha_inicio, $fecha_fin,$FICHA) {
        $amaxonia = new bd($_SESSION['bd']);

        $consulta = "SELECT ifnull(sum(monto),0) as monto FROM nom_movimientos_nomina  where ficha='$FICHA' AND codcon IN (90,91,100,105,106,107,108,109,110,111,112,113,114,115,116,119,120,121,122,123,124,125,125,126,127,128,129,130,131,132,133,134,135,136,137,138,139,141142,143,144,147,149,150,151,152,153,154,155,157,158,159,160,161,162,163,164,165,166,167,168,169,170,171,172,173,174,175,176,177,178,179,180,603,628)";
         
        $resultado = $amaxonia->query($consulta);
        $fila = $resultado->fetch_array();
        $suma = $fila['monto'];
        return $suma;
    }
    public function acumcomvacpanama($ficha,$tipo, $fecha_fin) {
        $amaxonia = new bd($_SESSION['bd']);
        //$conexion = conexion();
         
        $fechames11 = date('Y-m-d', strtotime('-11 month', strtotime ( $fecha_fin ))) ; // resta 11 mes
    
         $consulta = "SELECT ifnull(sum(DISTINCT nmn.monto) ,0) as monto 
         FROM nom_nominas_pago as nnp 
         INNER JOIN nom_movimientos_nomina as nmn ON nnp.codnom = nmn.codnom AND nmn.tipnom = nnp.codtip 
         INNER JOIN nomconceptos_acumulados as nca ON nmn.codcon = nca.codcon 
         left join nom_progvacaciones  as npv on (npv.ficha =  nmn.ficha)
         WHERE nmn.ficha='".$ficha."' AND nca.cod_tac='$tipo' and nnp.periodo_ini>='$fechames11' and npv.fechareivac<='$fecha_fin'" ;
      
        $resultado = $amaxonia->query($consulta);
        $fila = $resultado->fetch_assoc();
        $suma = $fila['monto'];
        return $suma;
    }
    public function vac_panama_dpendiente($ficha,$periodo)
    {
        $amaxonia = new bd($_SESSION['bd']);
    
       $consulta = "SELECT IF(saldo_vacaciones >0, saldo_vacaciones, 0) as suma 
       FROM nom_progvacaciones 
       WHERE tipooper='DV'  AND ficha='".$ficha."' AND estado='Pendiente' AND periodo = '$periodo' and marca = '0' ";
        $resultado = $amaxonia->query($consulta); 
        $fila = $resultado->fetch_assoc();
        $suma = $fila['suma'];
    
        return $suma;
    }
    function acumcom($ficha,$codcon, $fecha_inicio, $fecha_fin) {
        $amaxonia = new bd($_SESSION['bd']);
        //$conexion = conexion();
        //$consulta = "SELECT SUM(monto) AS monto FROM nom_movimientos_nomina WHERE mes=$mes AND anio=$anio AND tipcon='A' AND ficha=$ficha AND tipnom=$_SESSION[codigo_nomina] AND (codcon<>520 OR codcon<>1402 OR codcon<>1400 OR codcon<>1463)";
        $consulta = "SELECT ifnull(sum(monto),0) as monto FROM nom_nominas_pago INNER JOIN nom_movimientos_nomina ON nom_nominas_pago.codnom = nom_movimientos_nomina.codnom
         AND nom_movimientos_nomina.tipnom = nom_nominas_pago.codtip where ficha='$ficha' AND codcon='$codcon' and (periodo_ini>='$fecha_inicio' and periodo_fin<='$fecha_fin' and  (select  fecing from nompersonal where   ficha = '$ficha' ) <= periodo_fin )";

        $resultado = $amaxonia->query($consulta);
        $fila = $resultado->fetch_assoc();
        $suma = $fila['monto'];
        return $suma;
    
    }function conceptosanualfull($codcon, $fecha)
    {
        $selectra = new bd($_SESSION['bd']);
        $anio = substr($fecha, 0, 4);
        $mes = substr($fecha, 6, 7);
    
        $consulta = "SELECT sum(monto) as monto from nom_movimientos_nomina where ficha='".$FICHA."' and codcon in ('$codcon') and anio='$anio' and codnom<>'$CODNOM' and mes between '01' AND '{$mes}'"; 
        $resultado = $selectra->query($consulta);
        $fila = $resultado->fetch_assoc();
        if ($resultado->num_rows <= 0) {
            return 0;
        } else {
            return $fila['monto'];
        }
    }
    function campoadicionalper($idcampo,$FICHA,$tipnom) 
    {
        $selectra = new bd($_SESSION['bd']);
        $consulta = "SELECT valor from nomcampos_adic_personal where id='" . $idcampo . "' and ficha='" . $FICHA . "' and tiponom='{$tipnom}'";
        $resultado = $selectra->query($consulta);
        if (num_rows($resultado) == 0) {
            return;
        } else {
            $fila = fetch_array($resultado);
            return $fila['valor'];
        }
    }
    function acumcomtip($tipo, $fecha_inicio, $fecha_fin,$FICHA) {
        $selectra = new bd($_SESSION['bd']);
        
        $consulta = "SELECT ifnull(sum(monto) ,0) as monto 
        FROM nom_nominas_pago 
        INNER JOIN nom_movimientos_nomina ON nom_nominas_pago.codnom = nom_movimientos_nomina.codnom AND nom_movimientos_nomina.tipnom = nom_nominas_pago.codtip 
        INNER JOIN nomconceptos_acumulados ON nom_movimientos_nomina.codcon = nomconceptos_acumulados.codcon 
        WHERE ficha='$FICHA' AND cod_tac='$tipo' and (periodo_fin>='$fecha_inicio' and periodo_fin<='$fecha_fin' and  
        (select  fecing from nompersonal where   ficha = '$FICHA' ) <= periodo_fin ) " ;
        $resultado = $selectra->query($consulta);
        $fila = $resultado->fetch_assoc();
        $suma = $fila['monto'];
        return $suma;
    }
    function acumuladosalxiii($FICHA, $fecha_inicio, $fecha_fin) 
    {
        $selectra = new bd($_SESSION['bd']);
    
        $consulta = "SELECT sum(salario_bruto) as monto from salarios_acumulados where ficha='".$FICHA."' and (fecha_pago>='$fecha_inicio' and fecha_pago<='$fecha_fin') and estatus = 0"; 
     
        $resultado = $selectra->query($consulta);
        $fila = $resultado->fetch_assoc();
        if ($resultado->num_rows <= 0) {
            return 0;
        } else {
            return $fila['monto'];
        }
    }
    function acumuladovacxiii($FICHA, $fecha_inicio, $fecha_fin) 
    {
        $selectra = new bd($_SESSION['bd']);
    
        $consulta = "SELECT sum(vacac) as monto from salarios_acumulados where ficha='".$FICHA."' and (fecha_pago>='$fecha_inicio' and fecha_pago<='$fecha_fin') and estatus = 0"; 
    
        $resultado = $selectra->query($consulta);
        $fila = $resultado->fetch_assoc();
        if ($resultado->num_rows <= 0) {
            return 0;
        } else {
            return $fila['monto'];
        }
    }
    function acumuladootrosxiii($FICHA, $fecha_inicio, $fecha_fin) 
    {
        $selectra = new bd($_SESSION['bd']);
    
        $consulta = "SELECT sum(otros_ing) as monto from salarios_acumulados where ficha='".$FICHA."' and (fecha_pago>='$fecha_inicio' and fecha_pago<='$fecha_fin') and estatus=0"; 
    
        $resultado = $selectra->query($consulta);
        $fila = $resultado->fetch_assoc();
        if ($resultado->num_rows <= 0) {
            return 0;
        } else {
            return $fila['monto'];
        }
    }
    function salario_brutoacum($FICHA, $fechaini, $fechafin)
    {
        $selectra = new bd($_SESSION['bd']);
    
        $consulta = "SELECT ifnull(sum(salario_bruto) ,0) as monto  from salarios_acumulados where ficha='".$FICHA."' and fecha_pago >='$fechaini' and fecha_pago <='$fechafin' and estatus = 0"; 
        $resultado = $selectra->query($consulta);
        $fila = $resultado->fetch_assoc();
        if ($resultado->num_rows <= 0) {
            return 0;
        } else {
            return $fila['monto'];
        }
    }
        function salario_brutoacumliq($FICHA, $fechaini, $fechafin)
    {
        $selectra = new bd($_SESSION['bd']);
    
        $consulta = "SELECT ifnull(sum(salario_bruto)+SUM(vacac)+SUM(gtorep) ,0) as monto  from salarios_acumulados where ficha='".$FICHA."' and fecha_pago >='$fechaini' and fecha_pago <='$fechafin'"; 


        $resultado = $selectra->query($consulta);
        $fila = $resultado->fetch_assoc();
        if ($resultado->num_rows <= 0) {
            return 0;
        } else {
            return $fila['monto'];
        }
    }
    function concepto($codigo_concepto, $FICHA,$tipo) {
        $selectra = new bd($_SESSION['bd']);
        $consulta = "SELECT monto FROM nom_movimientos_nomina WHERE ficha='{$FICHA}' AND codcon='{$codigo_concepto}' AND tipnom='{$tipo}'";
     
        $resultado = $selectra->query($consulta);
        $fila = $resultado->fetch_assoc();
        if ($resultado->num_rows <= 0) {
            return 0;
        } else {
            return $fila['monto'];
        }
    }
    function conceptosanual($codcon, $fecha, $FICHA,$anio)
    {
        $selectra = new bd($_SESSION['bd']);
    
        $anio = substr($fecha, 0, 4);
        $consulta = "SELECT sum(monto) as monto from nom_movimientos_nomina where ficha='".$FICHA."' and codcon in ('$codcon') and anio='$anio' "; 
        $resultado = $selectra->query($consulta);
        $fila = $resultado->fetch_assoc();
        if ($resultado->num_rows <= 0) {
            return 0;
        } else {
            return $fila['monto'];
        }
    }
    function periodoxiii($FECHA)
    {
        $anio_xiiiprop = date('Y',strtotime($FECHA));
        $aniopasado_xiii = $anio_xiiiprop-1;
        
        if($FECHA >= $anio_xiiiprop.'-12-16' AND $FECHA <= $anio_xiiiprop.'-12-31')
        {
            return $anio_xiiiprop.'-12-16';
        }
        $periodo_ini1  = $aniopasado_xiii.'-12-16';
        $periodo_fin1  = $anio_xiiiprop.'-04-15';
        $periodo_ini2  = $anio_xiiiprop.'-04-16';
        $periodo_fin2  = $anio_xiiiprop.'-08-15';
        $periodo_ini3  = $anio_xiiiprop.'-08-16';
        $periodo_fin3  = $anio_xiiiprop.'-12-15';
        if($FECHA >= $periodo_ini1 && $FECHA <= $periodo_fin1)
        {
            return  $periodo_ini1;
        }
        if($FECHA >= $periodo_ini2 && $FECHA <= $periodo_fin2)
        {
            return  $periodo_ini2;
        }
        if($FECHA >= $periodo_ini3 && $FECHA <= $periodo_fin3)
        {
            return  $periodo_ini3;
        }
    }
    function calcularxiii($FICHA,$FECHAINICON, $FECHAFINNOM,$TIPNOM)
    {
        $FECHANOMINA = $this->periodoxiii($FECHAFINNOM);
        $T011=$this->acumcomtip("SI",$FECHANOMINA, $FECHAFINNOM,$FICHA);
        $T01=$this->salario_brutoacum($FICHA,$FECHANOMINA,$FECHAFINNOM);
        $T02=$this->acumuladootrosxiii($FICHA,$FECHANOMINA,$FECHAFINNOM);
        $T012=$this->acumcomtip("SI",$FECHAINICON, $FECHAFINNOM,$FICHA);
        $T03=$this->salario_brutoacum($FICHA,$FECHAINICON,$FECHAFINNOM);
        $T04=$this->acumuladootrosxiii($FICHA,$FECHAINICON,$FECHAFINNOM);

        $T05=$T011+$T01+$T02;
        $T06=$T012+$T03+$T04;

        $T200=(new DateTime("$FECHAINICON"))->getTimestamp();
        $T201=(new DateTime("$FECHANOMINA"))->getTimestamp();
        
        $T0100=$this->SI("$T200 > $T201",$T06,$T05);
        $anio_xiiiprop = date('Y',strtotime($FECHAFINNOM));
        $T07=$this->concepto(90, $FICHA,$TIPNOM);
        $T08=$this->conceptosanual("203",$FECHANOMINA,$FICHA, $anio_xiiiprop);
        $T10=($T0100+$T07)-$T08;
        $MONTO=$T10/12;
        $MONTO = round($MONTO,2);
        
        return $MONTO;
        }    
    function calcularprima($FICHA,$FECHAINICON, $FECHAFINNOM,$TIPNOM)
    {
            # Antiguedad en Dias
            #$FECHAFINNOM = $fecha;
            $T01= $this->antiguedad($FECHAINICON,$FECHAFINNOM,"D");
            $T02=$T01/365;
            $anio_traba = round($T02,2);

            #Salario en los ultimos 60 meses
            $FECHA_CALCULO = date("Y-m-d",strtotime($FECHAFINNOM."- 60 month"));
            $T12=$this->acumsal60mesessalacum($FICHA,$FECHA_CALCULO,$FECHAFINNOM);
            $T0100=$this->salario_brutoacum($FICHA,$FECHAINICON,$FECHAFINNOM);

            $T09999 = ($anio_traba > 5)? ($T12/60)*12*$anio_traba: $T0100;

            $MONTO=($T09999)*0.01923;
            $MONTO = round($MONTO,2);
            return $MONTO;
    }
    function acumuladogrxiii($FICHA, $fecha_inicio, $fecha_fin) 
    {
        $selectra = new bd($_SESSION['bd']);
    
        $consulta = "SELECT sum(gtorep) as monto from salarios_acumulados 
        where ficha='".$FICHA."' and (fecha_pago>='$fecha_inicio' and fecha_pago<='$fecha_fin') and estatus = 0"; 
    
        $resultado = $selectra->query($consulta);
        $fila = $resultado->fetch_assoc();
        if ($resultado->num_rows <= 0) {
            return 0;
        } else {
            return $fila['monto'];
        }
    }
    function vacacionesproporcionales($tipo, $fecha_inicio, $fecha_fin,$FICHA) {
        $selectra = new bd($_SESSION['bd']);
        
    // $fechaingresocolaborador = $fecha_inicio; // se le suma el año actual
    $dia=date("d", strtotime( $fecha_inicio));
    $mes=date("m", strtotime( $fecha_inicio));
    $diaf=date("d", strtotime( $fecha_fin));
    $mesf=date("m", strtotime( $fecha_fin));
    $mes=$mes;
    if ($mesf < $mes) {
    
            $anio=date("Y")-1;
    
        } else {
    
            $anio=date("Y");
    
        }   
    
        $fechaingresocolaborador= "$anio-$mes-$dia";
        $consulta = "SELECT ifnull(sum(monto) ,0) as monto 
        FROM nom_nominas_pago 
        INNER JOIN nom_movimientos_nomina ON nom_nominas_pago.codnom = nom_movimientos_nomina.codnom AND nom_movimientos_nomina.tipnom = nom_nominas_pago.codtip  
        WHERE ficha='$FICHA' 
        AND nom_movimientos_nomina.codcon IN (90,100,105,106,107,108,109,110,111,112,113,114,115,116,119,120,121,122,123,124,125,125,126,127,128,129,130,131,132,133,134,135,136,137,138,139,141,142,143,144,147,149,150,151,152,153,154,155,157,158,159,160,161,162,163,164,165,166,167,168,169,170,171,172,173,174,175,176,177,178,179,180) 
        and periodo_ini>='$fechaingresocolaborador' and periodo_fin<='$fecha_fin' " ;
    
        $resultado = $selectra->query($consulta);
        $fila = $resultado->fetch_assoc();
        $suma = $fila['monto'];
        return $suma;
    }
        
    function horas_ausencia_justificada($ficha, $fechaini, $fechafin, $opc)
    {
        $selectra = new bd($_SESSION['bd']);
        $consulta_personal = "SELECT cedula FROM nompersonal WHERE ficha = {$ficha}";
        $resultado = $selectra->query($consulta_personal);
        $nompersonal = $resultado->fetch_assoc();
        $cedula = $nompersonal['cedula'];
        
        //HORAS ENFERMEDAD
        if($opc==1)
        {
            $consulta="SELECT SUM(tiempo) AS tiempo "
            . "FROM dias_incapacidad "
            . "WHERE cedula='$cedula' "
            . "AND tipo_justificacion=5 "
            . "AND fecha BETWEEN '$fechaini' and '$fechafin'"        
            . "AND tiempo <0";
        }
        //HORAS AUSENCIA PERSONAL concepto 169
        if($opc==2)
        {
            $consulta="SELECT SUM(tiempo) AS tiempo "
            . "FROM dias_incapacidad "
            . "WHERE cedula='$cedula' "
            . "AND tipo_justificacion=3 "
            . "AND fecha BETWEEN '$fechaini' and '$fechafin'";
        }
        //HORAS MATRIMONIO, DUELO O NACIMIENTO concepto 170
        if($opc==3)
        {
            $consulta="SELECT SUM(tiempo) AS tiempo "
            . "FROM dias_incapacidad "
            . "WHERE cedula='$cedula' "
            . "AND tipo_justificacion=2 "
            . "AND fecha BETWEEN '$fechaini' and '$fechafin'";
        }
        //HORAS SINDICALES Concepto 171
        if($opc==4)
        {
            $consulta="SELECT SUM(tiempo) AS tiempo "
            . "FROM dias_incapacidad "
            . "WHERE cedula='$cedula' "
            . "AND tipo_justificacion=9 "
            . "AND fecha BETWEEN '$fechaini' and '$fechafin'";
        }
        $resultado = $selectra->query($consulta);
        $tiempo = $resultado->fetch_assoc();
        $horas = ($tiempo['tiempo'] <0 ? (-1)*$tiempo['tiempo'] : $tiempo['tiempo']);


        return number_format($horas,2,'.','');
        
    }
    function fondo_asist($FICHA, $fechaini, $fechafin)
    {
        $selectra = new bd($_SESSION['bd']);
    
        $consulta = "SELECT (SUM(minutos)) as monto 
        from reloj_procesar where ficha='".$FICHA."' and fecha >='$fechaini' and fecha <='$fechafin' and minutos >= 180 and concepto = 'tardanza'"; 
        $resultado = $selectra->query($consulta);
        $fila = $resultado->fetch_assoc();
        if ($resultado->num_rows <= 0) {
            return 0;
        } else {
            return $fila['monto'];
        }
    }
    function horastrabajadas($ficha, $fechaini, $fechafin, $opc)
    {
        $selectra = new bd($_SESSION['bd']);
        //$conexion = conexion();
        $select = "select capital from nomempresa";
        $resultado = $selectra->query($select);
        $filaemp = $resultado->fetch_assoc();
        $empresa = $filaemp['capital'];
        switch ($empresa)
        {
            
    
            case "zkteco":
                $limiteExtrasOrd=18;
                if($opc==1)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='ordinarias' AND ficha='$ficha'";
                elseif($opc==2)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='extras' AND ficha='$ficha'";
                elseif($opc==3)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='domingos' AND ficha='$ficha'";
                elseif($opc==4)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='extrasext' AND ficha='$ficha'";
                elseif($opc==5)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='extrasnoc' AND ficha='$ficha'";
                elseif($opc==6)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='extrasextnoc' AND ficha='$ficha'";
                elseif($opc==7)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='extrasdom' AND ficha='$ficha'";
                elseif($opc==8)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='extrasnocdom' AND ficha='$ficha'";
                elseif($opc==9)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='tardanza' AND ficha='$ficha'";
                elseif($opc==10)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='nacionalnac' AND ficha='$ficha'";
                elseif($opc==11)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='extranac' AND ficha='$ficha'";
                elseif($opc==12)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='extranocnac' AND ficha='$ficha'";
                elseif($opc==13)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='descextra1' AND ficha='$ficha'";
                elseif($opc==14)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='extrasextdom' AND ficha='$ficha'";
                elseif($opc==15)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='extrasextnocdom' AND ficha='$ficha'";
                elseif($opc==16)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='mixtodiurnadom' AND ficha='$ficha'";
                elseif($opc==17)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='mixtoextdiurnadom' AND ficha='$ficha'";
                elseif($opc==18)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='mixtonocdom' AND ficha='$ficha'";
                elseif($opc==19)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='mixtoextnocdom' AND ficha='$ficha'";
                elseif($opc==20)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='dialibredom' AND ficha='$ficha'";
                elseif($opc==21)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='emergenciadom' AND ficha='$ficha'";
                elseif($opc==22)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='descansoincompletodom' AND ficha='$ficha'";
                elseif($opc==23)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='mixtodiurnanac' AND ficha='$ficha'";
                elseif($opc==24)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='mixtoextdiurnanac' AND ficha='$ficha'";
                elseif($opc==25)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='mixtonocnac' AND ficha='$ficha'";
                elseif($opc==26)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='mixtoextnocnac' AND ficha='$ficha'";
                elseif($opc==27)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='dialibrenac' AND ficha='$ficha'";
                elseif($opc==28)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='emergencianac' AND ficha='$ficha'";
                elseif($opc==29)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='descansoincompletonac' AND ficha='$ficha'";
                elseif($opc==30)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='mixtodiurna' AND ficha='$ficha'";
                elseif($opc==31)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='mixtoextdiurna' AND ficha='$ficha'";
                elseif($opc==32)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='mixtonoc' AND ficha='$ficha'";
                elseif($opc==33)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='mixtoextnoc' AND ficha='$ficha'";
                elseif($opc==34)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='dialibre' AND ficha='$ficha'";
                elseif($opc==35)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='emergencia' AND ficha='$ficha'";
                elseif($opc==36)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='descansoincompleto' AND ficha='$ficha'";
    
                elseif($opc==37)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='extrasextnac' AND ficha='$ficha'";
                elseif($opc==38)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='extrasdl' AND ficha='$ficha'";
                elseif($opc==39)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='extranocdl' AND ficha='$ficha'";
                elseif($opc==40)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='mixtodiurnadl' AND ficha='$ficha'";
                elseif($opc==41)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='mixtoextdiurnadl' AND ficha='$ficha'";
                elseif($opc==42)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='mixtonocdl' AND ficha='$ficha'";
                elseif($opc==43)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='mixtoextnocdl' AND ficha='$ficha'";
                elseif($opc==44)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='dialibredl' AND ficha='$ficha'";
                elseif($opc==45)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='emergenciadl' AND ficha='$ficha'";
                elseif($opc==46)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='descansoincompletodl' AND ficha='$ficha'";
                elseif($opc==47)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='extrasextnocnac' AND ficha='$ficha'";
                elseif($opc==48)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='extrasextd' AND ficha='$ficha'";
                elseif($opc==49)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='extrasextnocdl' AND ficha='$ficha'";
                
                elseif($opc==50)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='extrassab' AND ficha='$ficha'";
                elseif($opc==51)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='extrasextsab' AND ficha='$ficha'";
                elseif($opc==52)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='extrasnocsab' AND ficha='$ficha'";
                elseif($opc==53)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='extrasextnocsab' AND ficha='$ficha'";
                elseif($opc==54)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='extranacsab' AND ficha='$ficha'";
                elseif($opc==55)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='extranocnacsab' AND ficha='$ficha'";
                elseif($opc==56)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='descextra1sab' AND ficha='$ficha'";
                elseif($opc==57)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='mixtoextdiurnasab' AND ficha='$ficha'";
                elseif($opc==58)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='mixtoextnocsab' AND ficha='$ficha'";
                
                elseif($opc==59)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='tarea' AND ficha='$ficha'";
                elseif($opc==60)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='lluvia' AND ficha='$ficha'";
                elseif($opc==61)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='paralizacionlluvia' AND ficha='$ficha'";
                elseif($opc==62)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='alturamenor' AND ficha='$ficha'";
                elseif($opc==63)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='alturamayor' AND ficha='$ficha'";
                elseif($opc==64)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='profundidad' AND ficha='$ficha'";
                elseif($opc==65)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='tunel' AND ficha='$ficha'";
                elseif($opc==66)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='martillo' AND ficha='$ficha'";
                elseif($opc==67)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='rastrilleo' AND ficha='$ficha'";
                elseif($opc==68)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='otras' AND ficha='$ficha'";            
                elseif($opc==69)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='descansocontrato' AND ficha='$ficha'";
                $resultado = $selectra->query($consulta);
                $fetch = $resultado->fetch_assoc();
                
                $horas = $fetch['minutos']/60;
    
                return $horas;
            break;
    
            case "excel":
                $limiteExtrasOrd=18;
                if($opc==1)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='ordinarias' AND ficha='$ficha'";
                elseif($opc==2)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='extras' AND ficha='$ficha'";
                elseif($opc==3)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='domingos' AND ficha='$ficha'";
                elseif($opc==4)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='extrasext' AND ficha='$ficha'";
                elseif($opc==5)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='extrasnoc' AND ficha='$ficha'";
                elseif($opc==6)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='extrasextnoc' AND ficha='$ficha'";
                elseif($opc==7)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='extrasdom' AND ficha='$ficha'";
                elseif($opc==8)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='extrasnocdom' AND ficha='$ficha'";
                elseif($opc==9)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='tardanza' AND ficha='$ficha'";
                elseif($opc==10)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='nacionalnac' AND ficha='$ficha'";
                elseif($opc==11)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='extranac' AND ficha='$ficha'";
                elseif($opc==12)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='extranocnac' AND ficha='$ficha'";
                elseif($opc==13)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='descextra1' AND ficha='$ficha'";
                elseif($opc==14)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='extrasextdom' AND ficha='$ficha'";
                elseif($opc==15)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='extrasextnocdom' AND ficha='$ficha'";
                elseif($opc==16)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='mixtodiurnadom' AND ficha='$ficha'";
                elseif($opc==17)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='mixtoextdiurnadom' AND ficha='$ficha'";
                elseif($opc==18)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='mixtonocdom' AND ficha='$ficha'";
                elseif($opc==19)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='mixtoextnocdom' AND ficha='$ficha'";
                elseif($opc==20)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='dialibredom' AND ficha='$ficha'";
                elseif($opc==21)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='emergenciadom' AND ficha='$ficha'";
                elseif($opc==22)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='descansoincompletodom' AND ficha='$ficha'";
                elseif($opc==23)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='mixtodiurnanac' AND ficha='$ficha'";
                elseif($opc==24)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='mixtoextdiurnanac' AND ficha='$ficha'";
                elseif($opc==25)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='mixtonocnac' AND ficha='$ficha'";
                elseif($opc==26)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='mixtoextnocnac' AND ficha='$ficha'";
                elseif($opc==27)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='dialibrenac' AND ficha='$ficha'";
                elseif($opc==28)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='emergencianac' AND ficha='$ficha'";
                elseif($opc==29)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='descansoincompletonac' AND ficha='$ficha'";
                elseif($opc==30)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='mixtodiurna' AND ficha='$ficha'";
                elseif($opc==31)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='mixtoextdiurna' AND ficha='$ficha'";
                elseif($opc==32)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='mixtonoc' AND ficha='$ficha'";
                elseif($opc==33)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='mixtoextnoc' AND ficha='$ficha'";
                elseif($opc==34)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='dialibre' AND ficha='$ficha'";
                elseif($opc==35)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='emergencia' AND ficha='$ficha'";
                elseif($opc==36)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='descansoincompleto' AND ficha='$ficha'";
    
                elseif($opc==37)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='extrasextnac' AND ficha='$ficha'";
                elseif($opc==38)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='extrasdl' AND ficha='$ficha'";
                elseif($opc==39)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='extranocdl' AND ficha='$ficha'";
                elseif($opc==40)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='mixtodiurnadl' AND ficha='$ficha'";
                elseif($opc==41)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='mixtoextdiurnadl' AND ficha='$ficha'";
                elseif($opc==42)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='mixtonocdl' AND ficha='$ficha'";
                elseif($opc==43)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='mixtoextnocdl' AND ficha='$ficha'";
                elseif($opc==44)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='dialibredl' AND ficha='$ficha'";
                elseif($opc==45)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='emergenciadl' AND ficha='$ficha'";
                elseif($opc==46)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='descansoincompletodl' AND ficha='$ficha'";
                elseif($opc==47)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='extrasextnocnac' AND ficha='$ficha'";
                elseif($opc==48)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='extrasextd' AND ficha='$ficha'";
                elseif($opc==49)
                    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='extrasextnocdl' AND ficha='$ficha'";
                
                $resultado = $selectra->query($consulta);
                $fetch = $resultado->fetch_assoc();
                
                $horas = $fetch['minutos']/60;
    
                return $horas;
            break;
        }
    }
    function calculargrxiii($FICHA,$FECHAINICON, $FECHAFINNOM,$TIPNOM)
    {
        $FECHANOMINA = $this->periodoxiii($FECHAFINNOM);
        $T01=$this->acumcomtip("SIGR",$FECHANOMINA, $FECHAFINNOM,$FICHA);
        $T02=$this->acumuladogrxiii($FICHA, $FECHANOMINA, $FECHAFINNOM);
        
        $MONTO=($T01+$T02)/12;
        $MONTO = round($MONTO,2);
        return $MONTO;
    }
    
    function calcularvacxiii($FICHA,$FECHAINICON, $FECHAFINNOM,$TIPNOM,$ANIO)
    {
        $REF =$this-> vac_panama_dpendiente($FICHA,$ANIO);
        
        $T04=$this->conceptosanual("615",$FECHAINICON,$FICHA, $ANIO);
        $T05=$this->conceptosanual("617",$FECHAINICON,$FICHA, $ANIO);
        $T06=$this->conceptosanual("619",$FECHAINICON,$FICHA, $ANIO);
        
        $T08=($T04+$T05+$T06)/11;
        
        
        #$T10=SI("$T09>$T011",$T09,$T011);
        
        $T11=$T08/30;
        
        $MONTO=$REF*$T11;
        $MONTO = round($MONTO,2);
        return $MONTO;
    }
    function calcularcesantia($FICHA,$FECHAINICON, $FECHAFINNOM,$TIPNOM,$ANIO)
    {
        $T01=$this->salario_brutoacum($FICHA,$FECHAINICON,$FECHAFINNOM);
        $T02=$this->acumuladootrosxiii($FICHA,$FECHAINICON,$FECHAFINNOM);
        $T03=$this->vacacionesproporcionales("SI",$FECHAINICON,$FECHAFINNOM,$FICHA);
        $T04=$this->concepto(91,$FICHA,$TIPNOM);
        $T05=$this->concepto(90,$FICHA,$TIPNOM);
        $T06=1;
        $MONTO=(($T01+$T02+$T03+$T04+$T05)*0.06)*$T06;
        $MONTO = round($MONTO,2);
        return $MONTO;
    }
    function calcularbonoasistencia($FICHA,$FECHAINICON, $FECHAFINNOM,$TIPNOM,$ANIO,$SUELDO){
        # CANTIDAD DE HORAS
        #horas de certificado medico;
        $T01=$this->horas_ausencia_justificada($FICHA,$FECHAINICON,$FECHAFINNOM,1);
        #minutos de tardanza mayores a 3 horas;
        $T02=$this->fondo_asist($FICHA,$FECHAINICON,$FECHAFINNOM);
        #Meses Laborados
        $T03=$this->antiguedad($FECHAINICON,$FECHAFINNOM, "M");
        $T04=$this->HORASTRABAJADAS($FICHA,$FECHAINICON,$FECHAFINNOM,9);

        $T05=($T02/60)+$T01;
        $T06=(($T03-1)*12);
        $T07=$T06-$T05;
        if($T07 < 0)
        {
            $T07 = (-1)*($T07);
        }
        $T10=($SUELDO);
        $T11=1;

        /*$T01=$this->salario_brutoacum($FICHA,$FECHAINICON,$FECHAFINNOM);
        $T02=$this->acumuladootrosxiii($FICHA,$FECHAINICON,$FECHAFINNOM);*/

        #RESULTADO
        $MONTO=($T07*$T10)*$T11;
        $MONTO = round($MONTO,2);
        return $MONTO;
    }
    function vacacionesproporcionalessalarioacum($FICHA, $fecha_inicio, $fecha_fin) 
    {
        $selectra = new bd($_SESSION['bd']);
      // $fechaingresocolaborador = $fecha_inicio; // se le suma el año actual
       $dia=date("d", strtotime( $fecha_inicio));
       $mes=date("m", strtotime( $fecha_inicio));
       $diaf=date("d", strtotime( $fecha_fin));
       $mesf=date("m", strtotime( $fecha_fin));
       $mes=$mes;
       if ($mesf < $mes) {
    
            $anio=date("Y")-1;
    
        } else {
    
            $anio=date("Y");
    
        }   
    
       $fechaingresocolaborador= "$anio-$mes-$dia";
       $consulta = "select (SUM(salario_bruto)+SUM(bono)+SUM(otros_ing)) as monto from salarios_acumulados where ficha='".$FICHA."' and fecha_pago>='$fechaingresocolaborador' and fecha_pago<='$fecha_fin' and estatus = 0 " ;
       $resultado = $selectra->query($consulta);
        $fila = $resultado->fetch_assoc();
        if ($resultado->num_rows <= 0) {
            return 0;
        } else {
            return $fila['monto'];
        }
    }
    function vacacacionespendientes($ficha)
    {
        $selectra = new bd($_SESSION['bd']);
    
       $consulta = "SELECT SUM(saldo_vacaciones) as suma FROM nom_progvacaciones WHERE  ficha='".$ficha."' AND estado='Pendiente' ";
    
        $resultado = $selectra->query($consulta);
        $fila = $resultado->fetch_assoc();
        if ($resultado->num_rows <= 0) {
            return 0;
        } else {
            $suma = $fila['suma'];
        return $suma;
        }
    }
    function calcularvacacionproporcional($FICHA,$FECHAINICON, $FECHAFINNOM,$TIPNOM,$ANIO,$SUELDO)
    {
        $T01=$this->vacacionesproporcionalessalarioacum($FICHA,$FECHAINICON,$FECHAFINNOM);
        $T02=$this->vacacionesproporcionales("SI",$FECHAINICON,$FECHAFINNOM, $FICHA);
        $T03=$this->vacacionesproporcionalessalarioacum($FICHA,$FECHAINICON,$FECHAFINNOM);
        $T04=$this->calcularvacacionesproporcionales("SI",$FECHAINICON,$FECHAFINNOM, $FICHA);
        $T05=$this->conceptosanual("203",$FECHAINICON,$FICHA, $ANIO);
        $T06=$this->concepto(90,$FICHA, $TIPNOM);
        $T10=$this->vacacacionespendientes($FICHA);

        $REF=$T10;
        $T11=(($SUELDO)/30);
        $T12=$T11*$T10;

        
        $T07=$T01+$T02;

        //$T08=$T03+$T04;

        $T1011=$this->campoadicionalper(9,$FICHA, $TIPNOM);

        //$T09=$this->SI("$T1011==Si",$T07,$T08); 
        $MONTO=((($T07+$T06)-$T05)/11)+$T12;
        return $MONTO;

    }
    function calcularvacacionesproporcionales($FICHA,$FECHAINICON, $FECHAFINNOM,$TIPNOM)
    {
        $selectra = new bd($_SESSION['bd']);
         // $fechaingresocolaborador = $fecha_inicio; // se le suma el año actual
        $dia=date("d", strtotime( $FECHAINICON));
        $mes=date("m", strtotime( $FECHAINICON));
        $diaf=date("d", strtotime( $FECHAFINNOM));
        $mesf=date("m", strtotime( $FECHAFINNOM));
        if ($mesf < $mes) {

            $anio=date("Y")-1;

        } else {

            $anio=date("Y");

        }   

        $fechaingresocolaborador= "$anio-$mes-$dia";
        $consulta = "SELECT ifnull(sum(nmn.monto) ,0) as monto
         FROM nom_nominas_pago nnp
         INNER JOIN nom_movimientos_nomina nmn ON nnp.codnom = nmn.codnom AND nmn.tipnom = nnp.codtip  
        WHERE nmn.ficha='$FICHA' AND nnp.tipnom='$TIPNOM'
        AND nmn.codcon IN (90,100,105,106,107,108,109,110,111,112,113,114,115,116,119,120,121,122,123,124,125,125,126,127,128,129,130,131,132,133,134,135,136,137,138,139,141,142,143,144,147,149,150,151,152,153,154,155,157,158,159,160,161,162,163,164,165,166,167,168,169,170,171,172,173,174,175,176,177,178,179,180) 
        AND nnp.periodo_ini>='$fechaingresocolaborador' AND nnp.periodo_fin<='$FECHAFINNOM' " ;
        $resultado = $selectra->query($consulta);
        $fila = $resultado->fetch_array();
        $suma = $fila['monto'];
        return $suma;

    }
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
}

# creamos  un nuevo objeto (MiPDF) utilizando la clase TCPDF
$MiPDF=new MYPDF('L','mm','LEGAL');
#desactivamos encabezado y pie de página
# el método SetAuthor nos permite incluir el nombre del autor
$MiPDF->SetAuthor('Pedro Martinez');
// set margins
$MiPDF->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
# el método SetTitle nos permite incluir un título
$MiPDF->SetTitle('Liquidaciones');
# el método SetDisplayMode nos permite incluir palabras claves
# separadas por espacios y dentro de una misma cadena
$MiPDF->SetDisplayMode('fullpage','two');
# creamos una página en blanco. Incluimos, para esta primera página
# un cambio de orientación respecto a la inicial
$MiPDF->AddPage('P','mm','LETTER');

    $fill = FALSE;
    $border=0;
    $ln=0;
    $fill = 0;
    $align='L';
    $link=0;
    $stretch=1;
    $ignore_min_height=0;
    $calign='';
    $valign='';
    $height=6;
    $ruta_img='../../nomina/imagenes/';
    $MiPDF->SetFont('helvetica', 'B', 10);
    /*for ($i=0; $i < count($data) ; $i++) { 
        print_r($data[$i]);echo "<br>";
    }exit;*/
    $sql_cab_planilla = "SELECT np.periodo_ini as desde, np.periodo_fin as hasta, np.mes, np.anio,np.fechapago,
    DATE_FORMAT(np.periodo_ini,'%d') as dia_ini,
    DATE_FORMAT(np.periodo_ini,'%m') as mes_ini, 
    DATE_FORMAT(np.periodo_fin,'%d') as dia_fin,
    DATE_FORMAT(np.periodo_ini,'%m') as mes_fin, np.descrip
    FROM nom_nominas_pago np 
    WHERE  np.codnom ='{$codnom}' AND np.tipnom='{$codtip}'";
    
    $res_cab_planilla = $db->query($sql_cab_planilla);
    $fila_nominas_pago = $res_cab_planilla->fetch_array();
        $datos_p = $data[0];
        $sql_persona = "SELECT np.*, nc.des_car from nompersonal np INNER JOIN nomcargos nc on np.codcargo = nc.cod_car  where np.ficha = '{$datos_p['ficha']}'";
        $res_persona = $db->query($sql_persona);
        $persona = $res_persona->fetch_array();        

        $MiPDF->Multicell(184,5,$empresa['nom_emp'],0,$alineacion='C',$fondo=false,$salto_de_linea=0,$x='15',$y='15',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

        $MiPDF->SetFont('times','', 9);    $MiPDF->ln(5);

        # Antiguedad en Dias
        $dias= $MiPDF->antiguedad($persona['fecing'],$fila_nominas_pago['hasta'],"D");
        $anios_dias=$dias/365;
        $anio_traba = round($anios_dias,2);

        $MiPDF->Cell(184,5,$fila_nominas_pago['descrip'], $border=0, 1, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->SetFont('helvetica', 'B', 10);
        $MiPDF->SetFont('helvetica', 'B',8);
        $fecharetiro = $persona['fecharetiro'] != "0000-00-00" ? $persona['fecharetiro'] : date("Y-m-d");
        $gastos_representacion = $persona['gastos_representacion'] != "" ? $persona['gastos_representacion'] : "0.00";
        $ln=1;
        $MiPDF->Cell(46,5,$persona['ficha'], $border='0', 0, $align='L', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(46,5,$persona['apenom'], $border='0', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(46,5,$persona['des_car'], $border='0', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(46,5,"Céd.:".$persona['cedula'], $border='0', 1, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(46,5,"Fecha de entrada", $border='LT', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(46,5,$MiPDF->ConvertirFecha($persona['fecing']), $border='T', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(46,5,"Suedo Mensual", $border='T', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(46,5,$persona['suesal'], $border='RT', 1, $align='L', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->SetFont('helvetica', '',8);

        $MiPDF->Cell(46,5,"Terminación", $border='L', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(46,5,$fecharetiro, $border='', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(46,5,"Gasto Representación", $border='', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(46,5,$gastos_representacion, $border='R', 1, $align='L', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);

        $MiPDF->Cell(46,5,utf8_encode("Antiguedad"), $border='L', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(46,5,$anio_traba, $border='', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(46,5,"-> Ingreso Base", $border='', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(46,5,$persona['hora_base'], $border='R', 1, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);

        $MiPDF->Cell(46,5,"", $border='LB', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(46,5,"", $border='B', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(46,5,"Tarifa x Hora", $border='B', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(46,5,"", $border='RB', 1, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $i=0;
        $ln=1;
        $MiPDF->SetFont('helvetica', 'B',8);

        $MiPDF->Cell(46,5,"Causal", $border='LT', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(46,5,"", $border='T', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(46,5,"Liquidar", $border='T', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(46,5,"", $border='RT', 1, $align='L', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->SetFont('helvetica', '',8);

        $sql_conceptos_mov = "SELECT nmn.codcon
        FROM nom_movimientos_nomina nmn
        INNER JOIN nomconceptos nc ON (nc.codcon = nmn.codcon)
        WHERE nmn.codnom = '{$codnom}' AND nmn.tipnom = '{$codtip}' AND nmn.ficha = '{$persona['ficha']}' AND nmn.tipcon = 'A'";
        $res_mov = $db->query($sql_conceptos_mov);
        $conceptos_liquidacion= array(
            "vacaciones" => '',
            "decimo" => '',
            "prima" => '',
            "indemnizacion" => '',
            "preaviso" => '',
            "recargo25" => '',
            "recargo50" => ''
        );
        while($mov_conceptos = $res_mov->fetch_array())
        {
            $data_liq[]=$mov_conceptos;
            if($mov_conceptos['codcon'] == 91)
            {
                $conceptos_liquidacion['vacaciones'] = "X";
            }
            if($mov_conceptos['codcon'] == 92)
            {
                $conceptos_liquidacion['decimo'] = "X";
            }
            if($mov_conceptos['codcon'] == 93)
            {
                $conceptos_liquidacion['prima'] = "X";
            }
            if($mov_conceptos['codcon'] == 94)
            {
                $conceptos_liquidacion['indemnizacion'] = "X";
            }
            if($mov_conceptos['codcon'] == 95)
            {
                $conceptos_liquidacion['preaviso'] = "X";
            }
            if($mov_conceptos['codcon'] == 98)
            {
                $conceptos_liquidacion['recargo25'] = "X";
            }
            if($mov_conceptos['codcon'] == 99)
            {
                $conceptos_liquidacion['recargo50'] = "X";
            }
        }
        
        $MiPDF->Cell(52,5,"", $border='L', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(40,5,"", $border='', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(20,5,"", $border='', 0, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(5,5, $conceptos_liquidacion['vacaciones'], $border='B', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(67,5,"Vacaciones", $border='R', 1, $align='L', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(52,5,"", $border='L', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(40,5,"", $border='', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(20,5,"", $border='', 0, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(5,5, $conceptos_liquidacion['decimo'], $border='B', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(67,5,"XIII", $border='R', 1, $align='L', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(52,5,"", $border='L', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(40,5,"", $border='', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(20,5,"", $border='', 0, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(5,5, $conceptos_liquidacion['prima'], $border='B', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(67,5,"Preaviso", $border='R', 1, $align='L', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(52,5,"", $border='L', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(40,5,"", $border='', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(20,5,"", $border='', 0, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(5,5, $conceptos_liquidacion['indemnizacion'], $border='B', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(67,5,"Prima de Antiguedad", $border='R', 1, $align='L', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(52,5,"", $border='L', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(40,5,"", $border='', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(20,5,"", $border='', 0, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(5,5, $conceptos_liquidacion['preaviso'], $border='B', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(67,5,"Indemnización", $border='R', 1, $align='L', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(52,5,"", $border='L', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(40,5,"", $border='', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(20,5,"", $border='', 0, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(5,5, $conceptos_liquidacion['recargo25'], $border='B', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(67,5,"Recargo 25% sobre indemnización", $border='R', 1, $align='L', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(52,5,"", $border='L', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(40,5,"", $border='', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(20,5,"", $border='', 0, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(5,5, $conceptos_liquidacion['recargo50'], $border='B', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(67,5,"Recargo 50% sobre indemnización", $border='R', 1, $align='L', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(46,5,"", $border='LB', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(46,5,"", $border='B', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(46,5,"", $border='B', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(46,5,"", $border='RB', 1, $align='L', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->ln();

        $MiPDF->SetFont('helvetica', 'B',8);

        $MiPDF->Cell(184,5,"VACACIONES", $border='1', 1, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->SetFont('helvetica', '',8);

        $FICHA = $persona['ficha'];
        $FECHANOMINA =  $fila_nominas_pago['fechapago'];
        $FECHA_XII =  date("d-m-Y",strtotime($FECHANOMINA."- 4 month"));
        $FECHA_PRIMA =  date("d-m-Y",strtotime($FECHANOMINA."- 11 month"));
        $FICHA = $persona['ficha'];
        $FECHAINGRESO = $MiPDF->ConvertirFechaYMD($persona['fecing']);
        $FECHAFIN = $fila_nominas_pago['hasta'];
        $TIPNOM = $codtip;
        $ANIOFECHA = date('Y',strtotime($fila_nominas_pago['hasta']));
        $MONTO_VAC = $MiPDF->calcularvacacionproporcional($persona['ficha'],$persona['fecing'], $fila_nominas_pago['hasta'],$codtip,$ANIOFECHA,$persona['suesal']);
        $VACXIII=$MiPDF->calcularvacxiii($FICHA,$FECHA_XII,$FECHANOMINA,$TIPNOM,$ANIOFECHA);


        $f= $fila_nominas_pago['hasta'];
        $fecha_inicio_vac= strtotime('-11 month',strtotime($f));
        $fecha_inicio_vac= date('d-m-Y',$fecha_inicio_vac);
        $fecha_fin_vac= date('d-m-Y',strtotime($fila_nominas_pago['hasta'])) ;
        $MiPDF->SetFont('helvetica', '',8);

        $MiPDF->Cell(184,5,"Periodo desde ".$fecha_inicio_vac." hasta " .$fecha_fin_vac, $border='LR', 1, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(92,5,"Salario", $border='L', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(92,5,"Gastos Representación", $border='R', 1, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);

        $MiPDF->Cell(20,5,"", $border="L", 0, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(32,5,"Subtotal", $border=1, 0, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(20,5,$MONTO_VAC, $border=1, 0, $align='L', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(20,5,"", $border="L", 0, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(25,5,"Subtotal", $border='', 0, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(20,5,$VACXIII, $border='', 0, $align='L', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(47,5,"", $border='R', 1, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);

        $MiPDF->Cell(20,5,"", $border="L", 0, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(32,5,"Ajustar Acumulado", $border=1, 0, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(40,5,"", $border=1, 0, $align='L', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(32,5,"Ajustar Acumulado", $border='', 0, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(40,5,"", $border='', 0, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(20,5,"", $border='R', 1, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);

        $MiPDF->Cell(20,5,"", $border="L", 0, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(32,5,"Acumulado", $border=0, 0, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(40,5,"", $border=1, 0, $align='L', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(32,5,"Acumulado", $border='', 0, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(40,5,"", $border='', 0, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(20,5,"", $border='R', 1, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        
        $MiPDF->Cell(20,5,"", $border="L", 0, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(32,5,"Promedio = Acumulado /11", $border=0, 0, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(40,5,"", $border=1, 0, $align='L', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(32,5,"Promedio = Acumulado /11", $border='', 0, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(40,5,"", $border='', 0, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(20,5,"", $border='R', 1, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);

        $MiPDF->Cell(20,5,"", $border="L", 0, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(32,5,"Salario Base", $border=0, 0, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(40,5,"", $border=1, 0, $align='L', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(37,5,"Gasto de Representación", $border='', 0, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(55,5,"", $border='R', 1, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);

        $MiPDF->Cell(184,5,"", $border="LR", 1, $align='L', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);

        $MiPDF->Cell(184,5,"Vacaciones proporcionales x Pagar:", $border="LR", 1, $align='L', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(20,5,"", $border="L", 0, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(32,5,"x Salario", $border=0, 0, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(40,5,"", $border='B', 0, $align='L', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(37,5,"", $border='', 0, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(55,5,"", $border='R', 1, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);


        $MiPDF->Cell(46,5,"", $border='LB', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(46,5,"", $border='B', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(46,5,"", $border='B', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(46,5,"", $border='RB', 1, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->ln();
        $MiPDF->SetFont('helvetica', 'B',8);

        $MiPDF->Cell(184,5,"DÉCIMO TERCER MES", $border=1, 1, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->SetFont('helvetica', '',8);

        $MiPDF->Cell(184,5,"Periodo desde ".$FECHA_XII." hasta " .$FECHANOMINA, $border='LR', 1, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(92,5,"Salario", $border='L', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(92,5,"Gastos Representación", $border='R', 1, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);

        $MiPDF->Cell(9,5,"", $border='L', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(25,5,"Fecha", $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(25,5,"Tipo Planilla", $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(25,5,"Salario", $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(100,5,"", $border='R', 1, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $sql_xiii = "SELECT (sum(case when nmn.tipcon='A' then nmn.monto else 0 end)-sum(case when nmn.tipcon='D' then nmn.monto else 0 end)) salario, nnp.periodo_fin, nmn.mes, nmn.anio, nnp.frecuencia, nf.descrip
        FROM nom_movimientos_nomina nmn 
        INNER JOIN nom_nominas_pago nnp on (nnp.codnom = nmn.codnom AND nnp.codtip = nmn.tipnom) 
        INNER JOIN nomfrecuencias nf on (nf.codfre= nnp.frecuencia) 
        WHERE nmn.tipcon in ('A','D') AND nnp.frecuencia in (2,3,8) AND nmn.tipnom = '{$codtip}' AND nnp.fechapago  BETWEEN '{$MiPDF->ConvertirFechaYMD($FECHA_XII)}' AND '{$FECHAFIN}' AND nmn.ficha = '{$FICHA}' 
        GROUP BY nmn.mes";
        $res_xiii = $db->query($sql_xiii);
        $subtotal = 0;
        while($mov_xiii = $res_xiii->fetch_array())
        {
            $MiPDF->Cell(9,5,"", $border='L', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
            $MiPDF->Cell(25,5,$mov_xiii['periodo_fin'], $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
            $MiPDF->Cell(25,5,$mov_xiii['descrip'], $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
            $MiPDF->Cell(25,5,$mov_xiii['salario'], $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
            $subtotal += $mov_xiii['salario'];
            $MiPDF->Cell(100,5,"", $border='R', 1, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);

        }    
        
        $MiPDF->Cell(9,5,"", $border="L", 0, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(50,5,"Subtotal", $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(25,5,$subtotal, $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(100,5,"", $border='R', 1, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        
        $MiPDF->Cell(9,5,"", $border="L", 0, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(50,5,"Ajustar Acumulado", $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(25,5,"", $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(100,5,"", $border='R', 1, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        
        $MiPDF->Cell(9,5,"", $border="L", 0, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(50,5,"+Vacaciones x Pagar", $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(25,5,"", $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(100,5,"", $border='R', 1, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);

        $MiPDF->Cell(9,5,"", $border="L", 0, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(50,5,"Acumulado", $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(25,5,"", $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(100,5,"", $border='R', 1, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);

        $MiPDF->Cell(9,5,"", $border="L", 0, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(50,5,"XIII Mes = Acumulado /12", $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(25,5,"", $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(100,5,"", $border='R', 1, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);

        $MiPDF->Cell(184,5,"", $border="LR", 1, $align='L', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);

        $MiPDF->Cell(46,5,"", $border='LB', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(46,5,"", $border='B', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(46,5,"", $border='B', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(46,5,"", $border='RB', 1, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->ln();
        $MiPDF->AddPage('P','mm','LETTER');
        $MiPDF->SetFont('helvetica', 'B',8);

        $MiPDF->Cell(184,5,"PRIMA ANTIGUEDAD", $border=1, 1, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->SetFont('helvetica', '',8);

        $MiPDF->Cell(184,5,"Periodo desde ".$FECHAINGRESO." hasta " .$FECHAFIN, $border='LR', 1, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(92,5,"Salario", $border='L', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(92,5,"Comentarios", $border='R', 1, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        
        $sql_prima = "SELECT (sum(case when nmn.tipcon='A' then nmn.monto else 0 end)-sum(case when nmn.tipcon='D' then nmn.monto else 0 end)) salario, 
        nnp.periodo_fin, nmn.mes, nmn.anio, nnp.frecuencia, nf.descrip,ntn.descrip tipo_planilla
        FROM nom_movimientos_nomina nmn
        INNER JOIN nom_nominas_pago nnp on (nnp.codnom = nmn.codnom AND nnp.codtip = nmn.tipnom)
        INNER JOIN nomfrecuencias nf on (nf.codfre= nnp.frecuencia)
        INNER JOIN nomtipos_nomina ntn on (ntn.codtip = nnp.codtip)
        WHERE nmn.tipcon in ('A','D') AND nnp.frecuencia in (2,3,8) AND nmn.tipnom = '{$codtip}' AND nnp.fechapago  BETWEEN '{$MiPDF->ConvertirFechaYMD($FECHAINGRESO)}' AND '{$FECHAFIN}' AND nmn.ficha = '{$FICHA}' 
        GROUP BY nmn.mes";
        $res_prima = $db->query($sql_prima);
        $subtotal_prima = 0;
        while($mov_prima = $res_prima->fetch_array())
        {
            $MiPDF->Cell(9,5,"", $border='L', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
            $MiPDF->Cell(25,5,$mov_prima['periodo_fin'], $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
            $MiPDF->Cell(25,5,$mov_prima['tipo_planilla'], $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
            $MiPDF->Cell(25,5,$mov_prima['salario'], $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
            $subtotal_prima += $mov_prima['salario'];
            $MiPDF->Cell(100,5,"", $border='R', 1, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);

        }
        $prima=$MiPDF->calcularprima($FICHA,$FECHAINGRESO,$FECHANOMINA,$TIPNOM);

        $MiPDF->Cell(9,5,"", $border="L", 0, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(50,5,"Subtotal", $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(25,5,$subtotal, $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(10,5,"", $border='', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(45,5,"", $border='', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(45,5,"", $border='R', 1, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        
        $MiPDF->Cell(9,5,"", $border="L", 0, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(50,5,"Ajustar Acumulado", $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(25,5,"", $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(100,5,"", $border='R', 1, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        
        $MiPDF->Cell(9,5,"", $border="L", 0, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(50,5,"+Vacaciones Proporcionales", $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(25,5,"", $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(100,5,"", $border='R', 1, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);

        $MiPDF->Cell(9,5,"", $border="L", 0, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(50,5,"Acumulado", $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(25,5,"", $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(100,5,"", $border='R', 1, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);

        $MiPDF->Cell(59,5,"Prima Antiguedad = Acumulado x1.92 %", $border='L', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(25,5,$prima, $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(100,5,"", $border='R', 1, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);

        $MiPDF->Cell(184,5,"", $border="LR", 1, $align='L', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);

        $MiPDF->Cell(46,5,"", $border='LB', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(46,5,"", $border='B', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(46,5,"", $border='B', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(46,5,"", $border='RB', 1, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->ln();
        $FECHA_INDEMNIZACION = $MiPDF->ConvertirFecha($FECHAFIN);
        $MiPDF->SetFont('helvetica', 'B',8);

        $MiPDF->Cell(184,5,"INDEMNIZACIÓN", $border=1, 1, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->SetFont('helvetica', '',8);

        $MiPDF->Cell(184,5,"Periodo desde ".$FECHA_INDEMNIZACION." hasta " .$FECHAFIN, $border='LR', 1, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(92,5,"Salario", $border='L', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(92,5,"Comentarios", $border='R', 1, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        
        $sql_prima = "SELECT (sum(case when nmn.tipcon='A' then nmn.monto else 0 end)-sum(case when nmn.tipcon='D' then nmn.monto else 0 end)) salario, 
        nnp.periodo_fin, nmn.mes, nmn.anio, nnp.frecuencia, nf.descrip,ntn.descrip tipo_planilla
        FROM nom_movimientos_nomina nmn
        INNER JOIN nom_nominas_pago nnp on (nnp.codnom = nmn.codnom AND nnp.codtip = nmn.tipnom)
        INNER JOIN nomfrecuencias nf on (nf.codfre= nnp.frecuencia)
        INNER JOIN nomtipos_nomina ntn on (ntn.codtip = nnp.codtip)
        WHERE nmn.tipcon in ('A','D') AND nnp.frecuencia in (2,3,8) AND nmn.tipnom = '{$codtip}' AND nnp.fechapago  BETWEEN '{$MiPDF->ConvertirFechaYMD($FECHAINGRESO)}' AND '{$FECHAFIN}' AND nmn.ficha = '{$FICHA}' 
        GROUP BY nmn.mes";
        $res_prima = $db->query($sql_prima);
        $subtotal_prima = 0;
        while($mov_prima = $res_prima->fetch_array())
        {
            $MiPDF->Cell(9,5,"", $border='L', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
            $MiPDF->Cell(25,5,$mov_prima['periodo_fin'], $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
            $MiPDF->Cell(25,5,$mov_prima['tipo_planilla'], $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
            $MiPDF->Cell(25,5,$mov_prima['salario'], $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
            $subtotal_prima += $mov_prima['salario'];
            $MiPDF->Cell(100,5,"", $border='R', 1, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);

        }
        

        $MiPDF->Cell(9,5,"", $border="L", 0, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(50,5,"Subtotal", $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(25,5,$subtotal, $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(10,5,"", $border='', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(45,5,"", $border='', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(45,5,"", $border='R', 1, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        
        $MiPDF->Cell(9,5,"", $border="L", 0, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(50,5,"Ajustar Acumulado", $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(25,5,"", $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(100,5,"", $border='R', 1, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        
        $MiPDF->Cell(9,5,"", $border="L", 0, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(50,5,"Acumulado", $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(25,5,"", $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(100,5,"", $border='R', 1, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        
        $MiPDF->Cell(9,5,"", $border="L", 0, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(50,5,"Salario Semanal = Acumulado/26", $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(25,5,"", $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(100,5,"", $border='R', 1, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        
        $MiPDF->Cell(9,5,"", $border="L", 0, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(50,5,"Salario Semanal Ultimo mes", $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(25,5,"", $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(100,5,"", $border='R', 1, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);

        $MiPDF->Cell(59,5,"Ingreso base semanal", $border='L', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(25,5,"", $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(100,5,"", $border='R', 1, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);

        $MiPDF->Cell(184,5,"", $border='LR', 1, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $TIPOLIQUIDACION = 4;

        $T01= $MiPDF->antiguedad($FECHAINGRESO,$FECHANOMINA,"D");
        $T02=$T01/365;
        #Salario en los ultimos 6 meses
        $FECHA_CALCULO =  date("d-m-Y",strtotime($FECHANOMINA."- 6 month"));
        $FECHA_XII =  date("d-m-Y",strtotime($FECHANOMINA."- 4 month"));
        $T12=$MiPDF->acumsalseismesessalacum($FICHA,$FECHA_CALCULO,$FECHANOMINA);
        $T03=$MiPDF->acumsalseismeses($FICHA,"SI",$FECHA_CALCULO,$FECHANOMINA);
        $T04=($T12/6)/4.333;

        #Salario ultimo fecha
        $T05= $MiPDF->salariointegralultimomes($FICHA,"SI",$FECHA_CALCULO,$FECHANOMINA);
        $T06=$T05/4.333;

        $T07=$MiPDF->SI("$T02>10",($T02-10),0);
        $T08=$MiPDF->SI("$T06>$T04",$T06,$T04);

        $T09=$MiPDF->SI("$TIPOLIQUIDACION<>4",0,1);

        $indem=$MiPDF->SI("$T02>10",((($T08*10*3.4)+($T08*$T07))*$T09),($T08*$T02*3.4)*$T09);
        $indem = round($indem,2);

        $MiPDF->Cell(184,5,"Indemnización x Pagar: Según Código de Trabajo, Artículos 225 y 149", $border="LR", 1, $align='L', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);

        $MiPDF->Cell(59,5,"", $border='L', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(25,5,$indem, $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(100,5,"", $border='R', 1, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);


        $MiPDF->Cell(46,5,"", $border='LB', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(46,5,"", $border='B', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(46,5,"", $border='B', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(46,5,"", $border='RB', 1, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->ln();
        $MiPDF->SetFont('helvetica', 'B',8);

        $MiPDF->Cell(184,5,"PREAVISO", $border=1, 1, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->SetFont('helvetica', '',8);
        $FECHA_PREAVISO =  date("d-m-Y",strtotime($FECHANOMINA."- 6 month"));

        $MiPDF->Cell(184,5,"Periodo desde ".$FECHA_PREAVISO." hasta " .$fecha_fin_vac, $border='LR', 1, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(92,5,"Salario", $border='L', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(92,5,"Gastos Representación", $border='R', 1, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->SetFont('helvetica', '',8);
        
        $sql_preaviso = "SELECT (sum(case when nmn.tipcon='A' then nmn.monto else 0 end)-sum(case when nmn.tipcon='D' then nmn.monto else 0 end)) salario, 
        nnp.periodo_fin, nmn.mes, nmn.anio, nnp.frecuencia, nf.descrip,ntn.descrip tipo_planilla
        FROM nom_movimientos_nomina nmn
        INNER JOIN nom_nominas_pago nnp on (nnp.codnom = nmn.codnom AND nnp.codtip = nmn.tipnom)
        INNER JOIN nomfrecuencias nf on (nf.codfre= nnp.frecuencia)
        INNER JOIN nomtipos_nomina ntn on (ntn.codtip = nnp.codtip)
        WHERE nmn.tipcon in ('A','D') AND nnp.frecuencia in (2,3,8) AND nmn.tipnom = '{$codtip}' AND nnp.fechapago  BETWEEN '{$MiPDF->ConvertirFechaYMD($FECHA_PREAVISO)}' AND '{$FECHAFIN}' AND nmn.ficha = '{$FICHA}' 
        GROUP BY nmn.mes";
        $res_preaviso = $db->query($sql_preaviso);
        $subtotal_preaviso = 0;
        while($mov_preaviso = $res_preaviso->fetch_array())
        {
            $MiPDF->Cell(9,5,"", $border='L', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
            $MiPDF->Cell(25,5,$mov_preaviso['periodo_fin'], $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
            $MiPDF->Cell(25,5,$mov_preaviso['tipo_planilla'], $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
            $MiPDF->Cell(25,5,$mov_preaviso['salario'], $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
            $subtotal_preaviso += $mov_preaviso['salario'];
            $MiPDF->Cell(100,5,"", $border='R', 1, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);

        }

        $MiPDF->Cell(9,5,"", $border="L", 0, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(50,5,"Subtotal", $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(25,5,$subtotal, $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(10,5,"", $border='', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(45,5,"", $border='', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(45,5,"", $border='R', 1, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        
        $MiPDF->Cell(9,5,"", $border="L", 0, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(50,5,"Ajustar Acumulado", $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(25,5,"", $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(100,5,"", $border='R', 1, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        
        $MiPDF->Cell(9,5,"", $border="L", 0, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(50,5,"+Vacaciones Proporcionales", $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(25,5,"", $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(100,5,"", $border='R', 1, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);

        $MiPDF->Cell(9,5,"", $border="L", 0, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(50,5,"Acumulado", $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(25,5,"", $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(100,5,"", $border='R', 1, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);

        $MiPDF->Cell(59,5,"Prima Antiguedad = Acumulado x1.92 %", $border='L', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(25,5,$prima, $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(100,5,"", $border='R', 1, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);

        $MiPDF->Cell(184,5,"", $border="LR", 1, $align='L', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);

        $MiPDF->Cell(46,5,"", $border='LB', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(46,5,"", $border='B', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(46,5,"", $border='B', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(46,5,"", $border='RB', 1, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->ln();
        

        $MiPDF->ln();
        $MiPDF->SetFont('helvetica', 'B',8);
        $MiPDF->Cell(185,5,"RESUMEN", $border=1, 1, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->SetFont('helvetica', '',8);

        $MiPDF->Cell(65,5,"Detalle", $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(24,5,"Ingresos", $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(24,5," Imp. S/Renta", $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(24,5,"Seg. Social.", $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(24,5,"Seg. Educ.", $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(24,5,"Neto a Pagar", $border='1', 1, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);

        $MiPDF->Cell(65,5,"Ingreso Pendiente", $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(24,5,"", $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(24,5,"", $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(24,5,"", $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(24,5,"", $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(24,5,"", $border='1', 1, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);

        $MiPDF->Cell(65,5,"Ingreso Pendiente x Gtos Rep.", $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(24,5,"", $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(24,5,"", $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(24,5,"", $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(24,5,"", $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(24,5,"", $border='1', 1, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);

        $MiPDF->Cell(65,5,"Vacaciones", $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(24,5,"", $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(24,5,"", $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(24,5,"", $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(24,5,"", $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(24,5,"", $border='1', 1, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);

        $MiPDF->Cell(65,5,"Vacaciones x Gtos Rep.", $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(24,5,"", $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(24,5,"", $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(24,5,"", $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(24,5,"", $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(24,5,"", $border='1', 1, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);

        $MiPDF->Cell(65,5,"Decimo Tercer Mes", $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(24,5,"", $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(24,5,"", $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(24,5,"", $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(24,5,"", $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(24,5,"", $border='1', 1, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);

        $MiPDF->Cell(65,5,"Decimo Tercer Mes x Gtos Rep.", $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(24,5,"", $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(24,5,"", $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(24,5,"", $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(24,5,"", $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(24,5,"", $border='1', 1, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);

        $MiPDF->Cell(65,5,"*Preaviso", $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(24,5,"", $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(24,5,"", $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(24,5,"", $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(24,5,"", $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(24,5,"", $border='1', 1, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);

        $MiPDF->Cell(65,5,"*Preaviso x Gtos Rep.", $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(24,5,"", $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(24,5,"", $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(24,5,"", $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(24,5,"", $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(24,5,"", $border='1', 1, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);

        $MiPDF->Cell(65,5,"*Prima de Antiguedad", $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(24,5,"", $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(24,5,"", $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(24,5,"", $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(24,5,"", $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(24,5,"", $border='1', 1, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);

        $MiPDF->Cell(65,5,"*Indemnización", $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(24,5,"", $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(24,5,"", $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(24,5,"", $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(24,5,"", $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(24,5,"", $border='1', 1, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);

        $MiPDF->Cell(65,5,"*Indemnización (Recargo 25%)", $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(24,5,"", $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(24,5,"", $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(24,5,"", $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(24,5,"", $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(24,5,"", $border='1', 1, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);

        $MiPDF->Cell(65,5,"*Indemnización (Recargo 50%)", $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(24,5,"", $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(24,5,"", $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(24,5,"", $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(24,5,"", $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(24,5,"", $border='1', 1, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);

        $MiPDF->Cell(65,5,"*Indemnización (Recargo 50%)", $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(24,5,"", $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(24,5,"", $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(24,5,"", $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(24,5,"", $border='1', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(24,5,"", $border='1', 1, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);

        $MiPDF->Cell(37,5,"", $border='LB', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(37,5,"", $border='B', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(37,5,"", $border='B', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(37,5,"", $border='B', 0, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(37,5,"", $border='RB', 1, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->ln();
        $MiPDF->AddPAge();

    $res = $db->query($sql);/*
    while ($data = fetch_array($res))
    {
        $MiPDF->Cell(37,5,$data['nombre'], $border='LRB', $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(37,5,justificacion($data['justificativo']), $border='LRB', $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(37,5,$data['tiempo'], $border='LRB', $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(37,5,$data['tiempo']/8, $border='LRB', $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(37,5,$data['minutos'], $border='LRB', 1, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $i=$i+1;
    }
    $MiPDF->SetFont('helvetica', 'B',9);
    $MiPDF->Cell(40,5,"TOTAL POSICIONES DISPONIBLES: ", $border='TB', $ln, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(20,5,$i, $border='TB', $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(130,5,"", $border='TB', $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
*/
    $MiPDF->Output('liquidaciones.pdf','I');
?>