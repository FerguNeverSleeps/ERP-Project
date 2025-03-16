<?php

class PlanillaClass
{
    public $db;
    public $empresa;
    function __construct($conex) {
        $this->db = $conex;
    }
    function setEmpresa()
    {
        
        $consulta = "SELECT * from nomempresa"; 
        $resultado = $this->db->query($consulta);
        $fila = $resultado->fetch_assoc();
        if ($resultado->num_rows <= 0) {
            return 0;
        } else {
            $this->empresa =  $fila['nom_emp'];
        }
    }
    function setNomina($codtip)
    {        
        $consulta = "SELECT UPPER(t.descrip) as descrip FROM nomtipos_nomina t WHERE t.codtip=".$codtip;
        $resultado = $this->db->query($consulta);
        $fila = $resultado->fetch_assoc();
        if ($resultado->num_rows <= 0) {
            return "indefinida";
        } else {
            return $fila['descrip'];
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
    public function antiguedad($fecha1, $fecha2, $tipo) 
    {
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

        

        $fechames11 = date('Y-m-d', strtotime('-6 month')) ; // resta 6 mes

        $consulta = "SELECT (SUM(salario_bruto)+SUM(vacac)+SUM(gtorep)) as monto 
        from salarios_acumulados where ficha='".$FICHA."' and fecha_pago >='$fechames11' and fecha_pago <='$fecha_fin'"; 

        $resultado = $this->db->query($consulta);
        $fila = $resultado->fetch_assoc();
        if ($resultado->num_rows <= 0) {
            return 0;
        } else {
            return $fila['monto'];
        }

    }
        public function acumsal60mesessalacum($FICHA, $fecha_inicio, $fecha_fin) {

        

        $fechames11 = date('Y-m-d', strtotime('-60 month')) ; // resta 60 mes

        $consulta = "SELECT (SUM(salario_bruto)+SUM(vacac)+SUM(gtorep)) as monto 
        from salarios_acumulados where ficha='".$FICHA."' and fecha_pago >='$fechames11' and fecha_pago <='$fecha_fin'"; 

        $resultado = $this->db->query($consulta);
        $fila = $resultado->fetch_assoc();
        if ($resultado->num_rows <= 0) {
            return 0;
        } else {
            return $fila['monto'];
        }

    }
    public function acumsalseismeses($FICHA,$tipo, $fecha_inicio, $fecha_fin) 
    {
        
         
        $fechames11 = date('Y-m-d', strtotime('-6 month')) ; // resta 6 mes
        $consulta = "SELECT ifnull(sum(monto) ,0) as monto 
        FROM nom_nominas_pago 
        INNER JOIN nom_movimientos_nomina ON nom_nominas_pago.codnom = nom_movimientos_nomina.codnom AND nom_movimientos_nomina.tipnom = nom_nominas_pago.codtip 
        INNER JOIN nomconceptos_acumulados ON nom_movimientos_nomina.codcon = nomconceptos_acumulados.codcon 
        WHERE ficha='$FICHA' AND cod_tac='$tipo' and periodo_ini>='$fechames11' and periodo_fin<='$fecha_fin'" ;
         
        $resultado = $this->db->query($consulta);
        $fila = $resultado->fetch_array();
        $suma = $fila['monto'];
        return $suma;
    }
    public function salariointegralultimomes($FICHA,$tipo, $fecha_inicio, $fecha_fin) 
    {
        //$conexion = conexion();
         
        $fechames11 = date('Y-m-d', strtotime('-1 month')) ; // resta 6 mes
        $fechahoy=date('Y-m-d');
        $consulta = "SELECT ifnull(sum(monto) ,0) as monto 
        FROM nom_nominas_pago 
        INNER JOIN nom_movimientos_nomina ON nom_nominas_pago.codnom = nom_movimientos_nomina.codnom AND nom_movimientos_nomina.tipnom = nom_nominas_pago.codtip 
        INNER JOIN nomconceptos_acumulados ON nom_movimientos_nomina.codcon = nomconceptos_acumulados.codcon 
        WHERE ficha='$FICHA' AND cod_tac='$tipo' and periodo_ini>='$fechames11' and periodo_fin<='$fechahoy'  and status='C'" ;
         
        $resultado = $this->db->query($consulta);
        $fila = $resultado->fetch_array();
        $suma = $fila['monto'];
        return $suma;
    }
    public function si($condicion, $v, $f) 
    {
        try{
            if ($condicion == "") {
                return 0;
            }

            if($f == "") $f=0;

            $if = "if(" . $condicion . "){\$retorno=" . $v . ";}else{\$retorno=" . $f . ";}";
            //echo $if."<br>";
            eval($if);
            return $retorno;
        }catch (Exception $e) {
            //echo 'Excepción capturada: ',  $e->getMessage(), "\n";
            return 0;
        }
    }
    
    public function acumuladoliquidacion($codcon, $fecha_inicio, $fecha_fin,$FICHA) 
    {
        

        $consulta = "SELECT ifnull(sum(monto),0) as monto FROM nom_movimientos_nomina  where ficha='$FICHA' AND codcon IN (90,91,100,105,106,107,108,109,110,111,112,113,114,115,116,119,120,121,122,123,124,125,125,126,127,128,129,130,131,132,133,134,135,136,137,138,139,141142,143,144,147,149,150,151,152,153,154,155,157,158,159,160,161,162,163,164,165,166,167,168,169,170,171,172,173,174,175,176,177,178,179,180,603,628)";
         
        $resultado = $this->db->query($consulta);
        $fila = $resultado->fetch_array();
        $suma = $fila['monto'];
        return $suma;
    }
    public function acumcomvacpanama($ficha,$tipo, $fecha_fin) 
    {
        
        //$conexion = conexion();
         
        $fechames11 = date('Y-m-d', strtotime('-11 month', strtotime ( $fecha_fin ))) ; // resta 11 mes
    
         $consulta = "SELECT ifnull(sum(DISTINCT nmn.monto) ,0) as monto 
         FROM nom_nominas_pago as nnp 
         INNER JOIN nom_movimientos_nomina as nmn ON nnp.codnom = nmn.codnom AND nmn.tipnom = nnp.codtip 
         INNER JOIN nomconceptos_acumulados as nca ON nmn.codcon = nca.codcon 
         left join nom_progvacaciones  as npv on (npv.ficha =  nmn.ficha)
         WHERE nmn.ficha='".$ficha."' AND nca.cod_tac='$tipo' and nnp.periodo_ini>='$fechames11' and npv.fechareivac<='$fecha_fin'" ;
      
        $resultado = $this->db->query($consulta);
        $fila = $resultado->fetch_assoc();
        $suma = $fila['monto'];
        return $suma;
    }
    public function vac_panama_dpendiente($ficha,$periodo)
    {
        
    
       $consulta = "SELECT IF(saldo_vacaciones >0, saldo_vacaciones, 0) as suma 
       FROM nom_progvacaciones 
       WHERE tipooper='DV'  AND ficha='".$ficha."' AND estado='Pendiente' AND periodo = '$periodo' and marca = '0' ";
        $resultado = $this->db->query($consulta); 
        $fila = $resultado->fetch_assoc();
        $suma = $fila['suma'];
    
        return $suma;
    }
    function acumcom($ficha,$codcon, $fecha_inicio, $fecha_fin) 
    {
        
        //$conexion = conexion();
        //$consulta = "SELECT SUM(monto) AS monto FROM nom_movimientos_nomina WHERE mes=$mes AND anio=$anio AND tipcon='A' AND ficha=$ficha AND tipnom=$_SESSION[codigo_nomina] AND (codcon<>520 OR codcon<>1402 OR codcon<>1400 OR codcon<>1463)";
        $consulta = "SELECT ifnull(sum(monto),0) as monto FROM nom_nominas_pago INNER JOIN nom_movimientos_nomina ON nom_nominas_pago.codnom = nom_movimientos_nomina.codnom
         AND nom_movimientos_nomina.tipnom = nom_nominas_pago.codtip where ficha='$ficha' AND codcon='$codcon' and (periodo_ini>='$fecha_inicio' and periodo_fin<='$fecha_fin' and  (select  fecing from nompersonal where   ficha = '$ficha' ) <= periodo_fin )";

        $resultado = $this->db->query($consulta);
        $fila = $resultado->fetch_assoc();
        $suma = $fila['monto'];
        return $suma;
    
    }
    function conceptosanualfull($codcon, $fecha)
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
            return 0;
        } else {
            $fila = fetch_array($resultado);
            return $fila['valor'];
        }
    }
    function acumcomtip($tipo, $fecha_inicio, $fecha_fin,$FICHA) 
    {
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
    
        $consulta = "SELECT sum(salario_bruto) as monto from salarios_acumulados where ficha='".$FICHA."' and (fecha_pago>='$fecha_inicio' and fecha_pago<='$fecha_fin')"; 
     
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
    
        $consulta = "SELECT sum(vacac) as monto from salarios_acumulados where ficha='".$FICHA."' and (fecha_pago>='$fecha_inicio' and fecha_pago<='$fecha_fin')"; 
    
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
    
        $consulta = "SELECT sum(otros_ing) as monto from salarios_acumulados where ficha='".$FICHA."' and (fecha_pago>='$fecha_inicio' and fecha_pago<='$fecha_fin')"; 
    
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
    
        $consulta = "SELECT ifnull(sum(salario_bruto) ,0) as monto  from salarios_acumulados where ficha='".$FICHA."' and fecha_pago >='$fechaini' and fecha_pago <='$fechafin'"; 
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
        where ficha='".$FICHA."' and (fecha_pago>='$fecha_inicio' and fecha_pago<='$fecha_fin')"; 
     
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
        
        $T07=$this->campoadicionalper(1,$FICHA, $TIPNOM);
        $T08=($T04+$T05+$T06)/11;
        
        
        $T09=$this->SI("$T07>$T08",$T07,$T08);
        
        $T11=$T09/30;
        
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
        $consulta = "select (SUM(salario_bruto)+SUM(bono)+SUM(otros_ing)) as monto from salarios_acumulados where ficha='".$FICHA."' and fecha_pago>='$fechaingresocolaborador' and fecha_pago<='$fecha_fin' " ;
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
    function calcularvacacionproporcional($FICHA,$FECHAINICON, $FECHAFINNOM,$TIPNOM,$ANIO,$SUELDO,$HORABASE)
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

        $T1011=$this->campoadicionalper(9,$FICHA, $TIPNOM);

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
}