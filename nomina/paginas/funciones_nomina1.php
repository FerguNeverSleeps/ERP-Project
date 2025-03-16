<?php
session_start();
ob_start();

$conexion = conexion();

function concepto($codigo_concepto) {
    include 'globales.php';
    $selectra = new bd($_SESSION['bd']);
    $consulta = "SELECT monto FROM nom_movimientos_nomina WHERE ficha='{$FICHA}' AND codcon='{$codigo_concepto}' AND codnom='{$CODNOM}' AND tipnom='{$_SESSION['codigo_nomina']}'";
 
    $resultado = $selectra->query($consulta);
    $fila = $resultado->fetch_assoc();
    if ($resultado->num_rows <= 0) {
        return 0;
    } else {
        return $fila['monto'];
    }
}

function valorconcepto($codigo_concepto) {
    include 'globales.php';
    $selectra = new bd($_SESSION['bd']);
    $consulta = "select valor from nom_movimientos_nomina where ficha='" . $FICHA . "' and codcon='" . $codigo_concepto . "' and codnom='" . $CODNOM . "' and tipnom='" . $_SESSION['codigo_nomina'] . "'";

    $resultado = $selectra->query($consulta);
    $fila = $resultado->fetch_assoc();
    if ($resultado->num_rows <= 0) {
        return 0;
    } else {
        return $fila['valor'];
    }
}

function acumcom($codcon, $fecha_inicio, $fecha_fin) {
    include 'globales.php';
    global $conexion;
    //$conexion = conexion();
    //$consulta = "SELECT SUM(monto) AS monto FROM nom_movimientos_nomina WHERE mes=$mes AND anio=$anio AND tipcon='A' AND ficha=$ficha AND tipnom=$_SESSION[codigo_nomina] AND (codcon<>520 OR codcon<>1402 OR codcon<>1400 OR codcon<>1463)";
    $consulta = "SELECT ifnull(sum(monto),0) as monto FROM nom_nominas_pago INNER JOIN nom_movimientos_nomina ON nom_nominas_pago.codnom = nom_movimientos_nomina.codnom
     AND nom_movimientos_nomina.tipnom = nom_nominas_pago.codtip where ficha='$FICHA' AND codcon='$codcon' and (periodo_ini>='$fecha_inicio' and periodo_fin<='$fecha_fin' and  (select  fecing from nompersonal where   ficha = '$FICHA' ) <= periodo_fin )";
    $resultado = query($consulta, $conexion);
    $fila = fetch_array($resultado);
    $suma = $fila['monto'];
    return $suma;
}



function acumcomliqui($codcon, $fecha_inicio, $fecha_fin) {
    include 'globales.php';
    global $conexion;
    //$conexion = conexion();
    //$consulta = "SELECT SUM(monto) AS monto FROM nom_movimientos_nomina WHERE mes=$mes AND anio=$anio AND tipcon='A' AND ficha=$ficha AND tipnom=$_SESSION[codigo_nomina] AND (codcon<>520 OR codcon<>1402 OR codcon<>1400 OR codcon<>1463)";
    $consulta = "SELECT ifnull(sum(monto),0) as monto FROM nom_nominas_pago INNER JOIN nom_movimientos_nomina ON nom_nominas_pago.codnom = nom_movimientos_nomina.codnom
     AND nom_movimientos_nomina.tipnom = nom_nominas_pago.codtip where ficha='$FICHA' AND codcon='$codcon' and ((select  fecing from nompersonal where   ficha = '$FICHA' ) <= periodo_fin )";
    $resultado = query($consulta, $conexion);
    $fila = fetch_array($resultado);
    $suma = $fila['monto'];
    return $suma;
}


function acumcomtip($tipo, $fecha_inicio, $fecha_fin) {
    include 'globales.php';
    global $conexion;
    
  $consulta = "SELECT ifnull(sum(monto) ,0) as monto FROM nom_nominas_pago INNER JOIN nom_movimientos_nomina ON nom_nominas_pago.codnom = nom_movimientos_nomina.codnom AND nom_movimientos_nomina.tipnom = nom_nominas_pago.codtip INNER JOIN nomconceptos_acumulados ON nom_movimientos_nomina.codcon = nomconceptos_acumulados.codcon WHERE ficha='$FICHA' AND cod_tac='$tipo' and (periodo_fin>='$fecha_inicio' and periodo_fin<='$fecha_fin' and  (select  fecing from nompersonal where   ficha = '$FICHA' ) <= periodo_fin ) " ;
    
    $resultado = query($consulta, $conexion);
    $fila = fetch_array($resultado);
    $suma = $fila['monto'];
    return $suma;
}

function acumuladoliquidacion($codcon, $fecha_inicio, $fecha_fin) {
    include 'globales.php';
    global $conexion;
    $consulta = "SELECT ifnull(sum(monto),0) as monto FROM nom_movimientos_nomina  where ficha='$FICHA' AND codcon IN (90,91,100,105,106,107,108,109,110,111,112,113,114,115,116,119,120,121,122,123,124,125,125,126,127,128,129,130,131,132,133,134,135,136,137,138,139,141142,143,144,147,149,150,151,152,153,154,155,157,158,159,160,161,162,163,164,165,166,167,168,169,170,171,172,173,174,175,176,177,178,179,180,603,628)";
    $resultado = query($consulta, $conexion);
    $fila = fetch_array($resultado);
    $suma = $fila['monto'];
    return $suma;
}


function vacacionesproporcionales($tipo, $fecha_inicio, $fecha_fin) {
    include 'globales.php';
    global $conexion;
    
  // $fechaingresocolaborador = $fecha_inicio; // se le suma el año actual
 /*  $dia=date("d", strtotime( $fecha_inicio));
   $mes=date("m", strtotime( $fecha_inicio));
   $diaf=date("d", strtotime( $fecha_fin));
   $mesf=date("m", strtotime( $fecha_fin));
   $mes=$mes;
   if ($mesf < $mes) {

		$anio=date("Y")-1;

    } else {

    	$anio=date("Y");

    }	**/

    $fechaingresocolaborador = date("Y-m-d",strtotime($fecha_inicio));
    $fechafin = date("Y-m-d",strtotime($fecha_fin));

    $fechaingresocolaborador= "$anio-$mes-$dia";
    $consulta = "SELECT ifnull(sum(monto) ,0) as monto FROM nom_nominas_pago 
    INNER JOIN nom_movimientos_nomina ON nom_nominas_pago.codnom = nom_movimientos_nomina.codnom AND nom_movimientos_nomina.tipnom = nom_nominas_pago.codtip  
    WHERE ficha='$FICHA' 
    AND nom_movimientos_nomina.codcon IN (90,100,105,106,107,108,109,110,111,112,113,114,115,116,119,120,121,122,123,124,125,125,126,127,128,129,130,131,132,133,134,135,136,137,138,139,141,142,143,144,147,149,150,151,152,153,154,155,157,158,159,160,161,162,163,164,165,166,167,168,169,170,171,172,173,174,175,176,177,178,179,180) 
    and periodo_ini>='$fechaingresocolaborador' and periodo_fin<='$fechafin' " ;
 
    $resultado = query($consulta, $conexion);
    $fila = fetch_array($resultado);
    $suma = $fila['monto'];
    return $suma;
}

function vacacionesproporcionalessalarioacum($FICHA, $fecha_inicio, $fecha_fin) 
{
    include 'globales.php';
    global $conexion;
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
   $consulta = "SELECT (SUM(salario_bruto)+SUM(bono)+SUM(otros_ing)+SUM(prima)) as monto "
           . "FROM salarios_acumulados "
           . "WHERE ficha='".$FICHA."' and fecha_pago>='$fechaingresocolaborador' and fecha_pago<='$fecha_fin'" ;
   $resultado = $selectra->query($consulta);
    $fila = $resultado->fetch_assoc();
    if ($resultado->num_rows <= 0) {
        return 0;
    } else {
        return $fila['monto'];
    }
}

function vacacionesproporcionalessalarioacumgr($FICHA, $fecha_inicio, $fecha_fin) 
{
    include 'globales.php';
    global $conexion;
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
   $consulta = "SELECT (SUM(gtorep)) as monto "
           . "FROM salarios_acumulados "
           . "WHERE ficha='".$FICHA."' and fecha_pago>='$fechaingresocolaborador' and fecha_pago<='$fecha_fin'" ;
   $resultado = $selectra->query($consulta);
    $fila = $resultado->fetch_assoc();
    if ($resultado->num_rows <= 0) {
        return 0;
    } else {
        return $fila['monto'];
    }
}

function descuentodesalacumisrl($FICHA, $fecha_inicio) 
{
    include 'globales.php';
    global $conexion;
    $selectra = new bd($_SESSION['bd']);
  // $fechaingresocolaborador = $fecha_inicio; // se le suma el año actual
   $dia="01";
   $mes="01";
   $anio=date("Y");
   //$dia=date("d", strtotime( $fecha_inicio));
   //$mes=date("m", strtotime( $fecha_inicio));
   //$diaf=date("d", strtotime( $fecha_fin));
   //$mesf=date("m", strtotime( $fecha_fin));
   $fechainicioanio= "$anio-$mes-$dia";

   if ("$fechainicioanio" > "$fecha_inicio") {
        return 0;
    } else {

    $consulta = "SELECT (SUM(salario_bruto)+SUM(bono)+SUM(otros_ing)+SUM(vacac)) as monto "
            . "FROM salarios_acumulados "
            . "WHERE ficha='".$FICHA."' and fecha_pago>='$fechainicioanio' and fecha_pago<='$fecha_inicio'" ;
   $resultado = $selectra->query($consulta);
   $fila = $resultado->fetch_assoc();
    if ($resultado->num_rows <= 0) {
        return 0;
    } else {
        return $fila['monto'];
    }

}
}


function descuentodeislracumisrl($FICHA, $fecha_inicio) 
{
    include 'globales.php';
    global $conexion;
    $selectra = new bd($_SESSION['bd']);
  // $fechaingresocolaborador = $fecha_inicio; // se le suma el año actual
   $dia="01";
   $mes="01";
   $anio=date("Y");
   //$dia=date("d", strtotime( $fecha_inicio));
   //$mes=date("m", strtotime( $fecha_inicio));
   //$diaf=date("d", strtotime( $fecha_fin));
   //$mesf=date("m", strtotime( $fecha_fin));
   $fechainicioanio= "$anio-$mes-$dia";

   if ("$fechainicioanio" > "$fecha_inicio") {
        return 0;
    } else {

    $consulta = "select SUM(islr) as monto "
            . "from salarios_acumulados "
            . "where ficha='".$FICHA."' and fecha_pago>='$fechainicioanio' and fecha_pago<='$fecha_inicio' " ;
   $resultado = $selectra->query($consulta);
   $fila = $resultado->fetch_assoc();
    if ($resultado->num_rows <= 0) {
        return 0;
    } else {
        return $fila['monto'];
    }

}
}



function vacacionespendientes($ficha)
{
    include 'globales.php';
    global $conexion;

   $consulta = "SELECT SUM(saldo_vacaciones) as suma "
           . "FROM nom_progvacaciones WHERE  ficha='".$ficha."' AND estado='Pendiente' ";

       
    $resultado = query($consulta, $conexion);
    
    
    $fila = fetch_array($resultado);
    $suma = $fila['suma'];
    return $suma;
    
//    if ($resultado->num_rows <= 0) {
//        return 0;
//    } else {
//        $suma = $fila['suma'];
//    return $suma;

}



function acum_vac_22($ficha, $fecha_inicio) {
    include 'globales.php';
    global $conexion;
    //$conexion = conexion();
        
    
    $consulta = "SELECT (SUM(salario_bruto)+SUM(bono)+SUM(prima)+SUM(otros_ing)) as monto FROM salarios_acumulados 
                WHERE 
                fecha_pago IN 
                (
                    SELECT fecha_pago FROM 
                    (
                        SELECT fecha_pago FROM salarios_acumulados 
                        WHERE fecha_pago < '$fecha_inicio' AND ficha='$ficha' AND  salario_bruto > 0 
                        ORDER BY fecha_pago DESC 
                        LIMIT 22
                    ) AS S
                ) 
                AND ficha='$ficha' AND  salario_bruto > 0 " ;

  
    $resultado = query($consulta, $conexion);
    $fila = fetch_array($resultado);
    $suma = $fila['monto'];
    return $suma;
}

function acumcomvac($tipo, $fecha_inicio, $fecha_fin) {
    include 'globales.php';
    global $conexion;
    //$conexion = conexion();
     
    $fechames11 = date('Y-m-d', strtotime('-11 month')) ; // resta 11 mes

    //$consulta = "SELECT SUM(monto) AS monto FROM nom_movimientos_nomina WHERE mes=$mes AND anio=$anio AND tipcon='A' AND ficha=$ficha AND tipnom=$_SESSION[codigo_nomina] AND (codcon<>520 OR codcon<>1402 OR codcon<>1400 OR codcon<>1463)";
    //$consulta = "SELECT sum(monto) as monto FROM nom_nominas_pago INNER JOIN nom_movimientos_nomina ON nom_nominas_pago.codnom = nom_movimientos_nomina.codnom AND nom_movimientos_nomina.tipnom = nom_nominas_pago.codtip INNER JOIN nomconceptos_acumulados ON nom_movimientos_nomina.codcon = nomconceptos_acumulados.codcon WHERE ficha='$FICHA' AND cod_tac='$tipo' and periodo_ini>='$fecha_inicio' and periodo_fin<='$fecha_fin' and status='C'";
    $consulta = "SELECT ifnull(sum(monto) ,0) as monto FROM nom_nominas_pago INNER JOIN nom_movimientos_nomina ON nom_nominas_pago.codnom = nom_movimientos_nomina.codnom AND nom_movimientos_nomina.tipnom = nom_nominas_pago.codtip INNER JOIN nomconceptos_acumulados ON nom_movimientos_nomina.codcon = nomconceptos_acumulados.codcon WHERE ficha='$FICHA' AND cod_tac='$tipo' and periodo_ini>='$fechames11' and periodo_fin<='$fecha_fin'  and status='C'" ;
   

  
    $resultado = query($consulta, $conexion);
    $fila = fetch_array($resultado);
    $suma = $fila['monto'];
    return $suma;
}


function acumcomvacfrac($tipo, $fecha_inicio, $fecha_fin) {
    include 'globales.php';
    global $conexion;
    //$conexion = conexion();
   
  // $fechaingresocolaborador = $fecha_inicio; // se le suma el año actual
   $dia=date("d", strtotime( $fecha_inicio));
   $mes=date("m", strtotime( $fecha_inicio));
   $anio=date("Y");
 
   $fechaingresocolaborador= "$anio-$mes-$dia";

    //$consulta = "SELECT SUM(monto) AS monto FROM nom_movimientos_nomina WHERE mes=$mes AND anio=$anio AND tipcon='A' AND ficha=$ficha AND tipnom=$_SESSION[codigo_nomina] AND (codcon<>520 OR codcon<>1402 OR codcon<>1400 OR codcon<>1463)";
    //$consulta = "SELECT sum(monto) as monto FROM nom_nominas_pago INNER JOIN nom_movimientos_nomina ON nom_nominas_pago.codnom = nom_movimientos_nomina.codnom AND nom_movimientos_nomina.tipnom = nom_nominas_pago.codtip INNER JOIN nomconceptos_acumulados ON nom_movimientos_nomina.codcon = nomconceptos_acumulados.codcon WHERE ficha='$FICHA' AND cod_tac='$tipo' and periodo_ini>='$fecha_inicio' and periodo_fin<='$fecha_fin' and status='C'";
   $consulta = "SELECT ifnull(sum(monto) ,0) as monto FROM nom_nominas_pago INNER JOIN nom_movimientos_nomina ON nom_nominas_pago.codnom = nom_movimientos_nomina.codnom AND nom_movimientos_nomina.tipnom = nom_nominas_pago.codtip INNER JOIN nomconceptos_acumulados ON nom_movimientos_nomina.codcon = nomconceptos_acumulados.codcon WHERE ficha='$FICHA' AND cod_tac='$tipo' and periodo_ini>='$fechaingresocolaborador' and periodo_fin<='$fecha_fin' " ;
   

  
    $resultado = query($consulta, $conexion);
    $fila = fetch_array($resultado);
    $suma = $fila['monto'];
    return $suma;
}

function mensajecon($var) {

    echo $var;
}

function dia($fecha) {
    return date("d", strtotime($fecha));
}

function mes($fecha) {
    return date("m", strtotime($fecha));
}

function si($condicion, $v, $f) {

    if ($condicion == "") {
        return 0;
    }

    if($f == "") $f=0;

    $if = "if(" . $condicion . "){\$retorno=" . $v . ";}else{\$retorno=" . $f . ";}";

    eval($if);
    return $retorno;
}

function salida($condicion, $v, $f) {
    if ($condicion == "") {
        return 0;
    }
    $if = "if(" . $condicion . "){" . $v . ";}else{\$retorno=" . $f . ";}";
    eval($if);
}

function lunes($periodo) {
//funcion lunes
    $cadena = explode("-", $periodo);
    //$mes=date($cadena[1]);
    //$ano=date("Y");
    $fecha_lunes = $cadena[0] . "-" . $cadena[1] . "-01";
    $num_dias_mes = date("t", strtotime($fecha_lunes));
    $dia_inicio = date("N", strtotime($fecha_lunes));
    $LUNES = 0;
    for ($i = 1; $i <= $num_dias_mes; $i++) {
        if ($dia_inicio == 1) {
            $dia_inicio++;
            $LUNES++;
        } elseif ($dia_inicio == 7) {
            $dia_inicio = 1;
        } else {
            $dia_inicio++;
        }
    }
    return $LUNES;
}

function lunes_per($inicio, $final) {
    if ($final < $inicio)
        return 0;
    if (($final == '') || ($inicio == ''))
        return 0;
    $fecha_inicio = explode("-", $inicio);
    $fecha_fin = explode("-", $final);
//			echo gregoriantojd($fecha_inicio[1],substr($fecha_inicio[2],0,2),$fecha_inicio[0]);

    $LUNES = 0;

    for ($i = gregoriantojd($fecha_inicio[1], substr($fecha_inicio[2], 0, 2), $fecha_inicio[0]); $i <= gregoriantojd($fecha_fin[1], substr($fecha_fin[2], 0, 2), $fecha_fin[0]); $i++) {
        if ($i % 7 == 0) {
            $LUNES++;
        }
    }
    return $LUNES;
}

function lunes_per_ingre($inicio, $final) {
    $cadena = explode("-", $final);
    //echo "-".$inicio."-";
    if ($inicio == "" || $inicio == "0000-00-00") {
        return 0;
    } else {
        //$inicio=sumadia($inicio,1);
        if ($final < $inicio)
            return 0;
        if (($final == '') || ($inicio == ''))
            return 0;
        $fecha_inicio = explode("-", $inicio);
        $fecha_fin = explode("-", $final);
        //			echo gregoriantojd($fecha_inicio[1],substr($fecha_inicio[2],0,2),$fecha_inicio[0]);

        $LUNES = 0;

        for ($i = gregoriantojd($fecha_inicio[1], substr($fecha_inicio[2], 0, 2), $fecha_inicio[0]); $i <= gregoriantojd($fecha_fin[1], substr($fecha_fin[2], 0, 2), $fecha_fin[0]); $i++) {
            if ($i % 7 == 0) {
                $LUNES++;
            }
        }
        return $LUNES;
    }
}

function lunes_per_vaca($inicio, $final) {
    $cadena = explode("-", $final);
    //echo "-".$inicio."-";
    if ($inicio == "" || $inicio == "0000-00-00") {
        return 0;
    } else {
        $inicio = sumadia($inicio, 1);
        if ($final < $inicio)
            return 0;
        if (($final == '') || ($inicio == ''))
            return 0;
        $fecha_inicio = explode("-", $inicio);
        $fecha_fin = explode("-", $final);
        //			echo gregoriantojd($fecha_inicio[1],substr($fecha_inicio[2],0,2),$fecha_inicio[0]);

        $LUNES = 0;

        for ($i = gregoriantojd($fecha_inicio[1], $fecha_inicio[2], $fecha_inicio[0]); $i <= gregoriantojd($fecha_fin[1], $fecha_fin[2], $fecha_fin[0]); $i++) {
            if ($i % 7 == 0) {
                $LUNES++;
            }
        }
        return $LUNES;
        //2455776
        //2455799 
    }
}

function lunespervac($inicio, $final) {
    if ($final < $inicio)
        return 0;
    if (($final == '') || ($inicio == ''))
        return 0;
    $fecha_inicio = explode("-", $inicio);
    $fecha_fin = explode("-", $final);
//	echo gregoriantojd($fecha_inicio[1],substr($fecha_inicio[2],0,2),$fecha_inicio[0]);

    $LUNES = 0;
    for ($i = gregoriantojd($fecha_inicio[1], substr($fecha_inicio[2], 0, 2), $fecha_inicio[0]); $i <= gregoriantojd($fecha_fin[1], substr($fecha_fin[2], 0, 2), $fecha_fin[0]); $i++) {
        if ($i % 7 == 0) {
            $LUNES++;
        }
        if ($i == gregoriantojd($fecha_fin[1], substr($fecha_fin[2], 0, 2), $fecha_fin[0]))
            $LUNES = $LUNES - 1;
    }
    return $LUNES;
}

function lunes_nuevo($inicio, $fin) {
    $start = strtotime($inicio);
    $end = strtotime($fin);
    $day = 1;

    $w = array(date('w', $start), date('w', $end));
    return floor(( date('z', $end) - date('z', $start) ) / 7) + ($day == $w[0] || $day == $w[1] || $day < ((7 + $w[1] - $w[0]) % 7));
}

function lunes_total_vaca($cedula) {
    global $conexion;
    //$conexion = conexion();
    $consulta = "SELECT fechavac,fechareivac  FROM nom_progvacaciones WHERE tipooper='DA'  AND ceduda='$cedula'  AND estado='Pendiente' AND fechavac<>'0000-00-00' and fechareivac<>'0000-00-00'";
    $resultado = query($consulta, $conexion);
    $lunes = 0;
    while ($fila = fetch_array($resultado)) {
        $lunes+=lunes_nuevo($fila['fechavac'], $fila['fechareivac']);
    }
    return $lunes;
}

function antiguedad($fecha1, $fecha2, $tipo) {
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
    //echo $segundos_diferencia;
    //convierto segundos en dÃ­as
    $dias = $segundos_diferencia / (60 * 60 * 24);

    //obtengo el valor absoulto de los dÃ­as (quito el posible signo negativo)
    $dias = abs($dias);

    //quito los decimales a los dÃ­as de diferencia
    $dias = floor($dias);


// 	$fecha_inicio=explode("-",$fecha1);
// 	$fecha_fin=explode("-",$fecha2);
// 	$dias=0;
// 	for($i=gregoriantojd( $fecha_inicio[1],substr($fecha_inicio[2],0,2),$fecha_inicio[0]); $i<=gregoriantojd($fecha_fin[1],substr($fecha_fin[2],0,2),$fecha_fin[0]);$i++)
// 	{
// 		$dias++;
// 	}
    //$dias=$dia2-$dia1;
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

function cuotapre_ext($ficha, $inicio, $final, $tipo) {
//    echo "Entro CUOTAPRE_EXT";
    global $conexion;
    $selectra = new bd($_SESSION['bd']);
    $i=0;$monto=array();

    //$conexion = conexion();
    $dia = substr($final, 8, 2);
    if ($dia > 25)
        $dia = 31;
    $final = substr($final, 0, 8) . $dia;
//    $consulta = "SELECT montocuo,  FROM nomprestamos_detalles"
//                . "WHERE ficha=$ficha "
//                . "AND fechaven between '$inicio' AND '$final' "
//                . "AND estadopre='Pendiente'";

//    echo "<br>";
    $consulta = "SELECT pd.montocuo, pd.numpre, pd.tipocuo, pd.numcuo, pd.fechaven, pd.montocuo, pd.salinicial, pd.salfinal,
                        pc.codigopr, pc.id_tipoprestamo
                 FROM   nomprestamos_detalles as pd
                 INNER JOIN nomprestamos_cabecera as pc ON pd.numpre=pc.numpre
                 WHERE pd.ficha='$ficha'
                 AND   pd.fechaven BETWEEN '$inicio' AND '$final'
                 AND   pd.estadopre='Pendiente' AND pc.codigopr='$tipo'";
    //$resultado = query($consulta, $conexion);
    //$fila = fetch_array($resultado);
    $resultado = $selectra->query($consulta);
    //$fetch = $resultado->fetch_assoc();

    while ($fila = $resultado->fetch_assoc())
    {
        $monto[$i] = $fila;
//        print_r($fila);echo "<br>";
        $i++;
        //print_r($monto[$i]);echo "<br>";
    }
    //print_r($monto);
//    echo "<br>";
    return $monto;
}

function cuotapre($ficha, $inicio, $final)
{
    global $conexion;
    //$conexion = conexion();
    $dia = substr($final, 8, 2);
    if ($dia > 25)
        $dia = 31;
    $final = substr($final, 0, 8) . $dia;
    $consulta = "SELECT SUM(montocuo) as total "
            . "FROM nomprestamos_detalles "
            . "WHERE ficha=$ficha and fechaven between '$inicio' and '$final' and estadopre='Pendiente'";
    $resultado = query($consulta, $conexion);
    $fetch = fetch_array($resultado);
    return $fetch[total];
}

function cuotaprecant($ficha, $inicio, $final) {
    global $conexion;
    //$conexion = conexion();
    $dia = substr($final, 8, 2);
    if ($dia > 25)
        $dia = 31;
    $final = substr($final, 0, 8) . $dia;
    $consulta = "SELECT COUNT(montocuo) as total FROM nomprestamos_detalles WHERE ficha=$ficha and fechaven between '$inicio' and '$final' and estadopre='Pendiente'";
    $resultado = query($consulta, $conexion);
    $fetch = fetch_array($resultado);
    return $fetch[total];
}

function cuotafact($ficha, $inicio, $final) {
    global $conexion;
    //$conexion = conexion();
    $dia = substr($final, 8, 2);
    if ($dia > 25)
        $dia = 31;
    $final = substr($final, 0, 8) . $dia;
    $consulta = "SELECT SUM(montocuo) as total FROM nomfacturas_detalles WHERE ficha=$ficha and fechaven between '$inicio' and '$final' and estadopre='Pendiente'";
    $resultado = query($consulta, $conexion);
    $fetch = fetch_array($resultado);
    return $fetch[total];
}

function cuotafactcant($ficha, $inicio, $final) {
    global $conexion;
    //$conexion = conexion();
    $dia = substr($final, 8, 2);
    if ($dia > 25)
        $dia = 31;
    $final = substr($final, 0, 8) . $dia;
    $consulta = "SELECT COUNT(montocuo) as total FROM nomfacturas_detalles WHERE ficha=$ficha and fechaven between '$inicio' and '$final' and estadopre='Pendiente'";
    $resultado = query($consulta, $conexion);
    $fetch = fetch_array($resultado);
    return $fetch[total];
}
function mesesfinalanio($fecha)
{  
    $fecha1 = date('Y-m-d',strotime($fecha));
    $date1 = new DateTime($fecha1);
    $fecha_fin = date('Y-m-d',strtotime('last day of this year'));
    $date2 = new DateTime($fecha_fin);
    $diff = $date1->diff($date2);
    //return (($diff->m) <= 12) ? $diff->m : 12;

}
function cadenatiempo($tipo,$fecha)
{
	return date($tipo,strtotime($fecha));
}
function anofecing($fecha)
{
    return cadenatiempo('Y',$fecha);
}
function mesfecing($fecha)
{
    return cadenatiempo('m',$fecha);
}
function aniomesfecin($fecha)
{
    return cadenatiempo('Y-m',$fecha);
}
function anioactual()
{
    return date('Y');
}
function cuotapretip($ficha, $inicio, $final, $tipo) {
    global $conexion;
    //$conexion = conexion();
    $consulta = "SELECT SUM(montocuo) as total
                 FROM   nomprestamos_detalles as pd
                 INNER JOIN nomprestamos_cabecera as pc ON pd.numpre=pc.numpre
                 WHERE pd.ficha='$ficha'
                 AND   pd.fechaven BETWEEN '$inicio' AND '$final'
                 AND   pd.estadopre='Pendiente' AND pc.codigopr='$tipo'";
    $resultado = query($consulta, $conexion);
    $fetch = fetch_array($resultado);
    return $fetch['total'];
}

function gastoadmon($ficha,$codigopr) {
    global $conexion;
    //$conexion = conexion();
    $consulta = "SELECT gastos_admon FROM nomprestamos_cabecera AS pc WHERE pc.ficha=$ficha and pc.codigopr=$codigopr and estadopre='Pendiente'";
    $resultado = query($consulta, $conexion);
    $fetch = fetch_array($resultado);
    return $fetch['gastos_admon'];
}

function cuotapretipFecPer($ficha, $inicio, $final, $tipo) {
    global $conexion;
    //$conexion = conexion();
    $dia = substr($final, 8, 2);
    if ($dia > 25)
        $dia = 31;
   // $final = substr($final, 0, 8) . $dia;
    $consulta = "SELECT SUM(montocuo) as total
                 FROM   nomprestamos_detalles as pd
                 INNER JOIN nomprestamos_cabecera as pc ON pd.numpre=pc.numpre
                 WHERE pd.ficha='$ficha'
                 AND   pd.fechaven BETWEEN '$inicio' AND '$final'
                 AND   pd.estadopre='Pendiente' AND pc.codigopr='$tipo'";
    $resultado = query($consulta, $conexion);
    $fetch = fetch_array($resultado);
    return $fetch['total'];
}
function cuotapretipNumPer($ficha, $inicio, $final, $tipo) {
    global $conexion;
    //$conexion = conexion();
    $dia = substr($final, 8, 2);
    if ($dia > 25)
        $dia = 31;
   // $final = substr($final, 0, 8) . $dia;
    $consulta = "SELECT SUM(montocuo) as total 
                 FROM   nomprestamos_detalles as pd 
                 INNER JOIN nomprestamos_cabecera as pc ON pd.numpre=pc.numpre 
                 WHERE pd.ficha=$ficha 
                 AND   pd.fechaven BETWEEN '$inicio' AND '$final'
                 AND   pd.estadopre='Pendiente' AND pc.codigopr='$tipo'";
    $resultado = query($consulta, $conexion);
    $fetch = fetch_array($resultado);
    return $fetch['total'];
}

function saldopre($ficha, $inicio, $final, $tipo) {
    //saldo actual del prestamo, 0 para todos los prestamos, 1 en adelante para diferentes tipos de prestamos
    global $conexion;
    //$conexion = conexion();
    $dia = substr($final, 8, 2);
    if ($dia > 25)
        $dia = 31;
    $final = substr($final, 0, 8) . $dia;
    if ($tipo == 0)
        $consulta = "SELECT SUM(salfinal) as total FROM nomprestamos_detalles WHERE ficha=$ficha and fechaven between '$inicio' and '$final' and estadopre='Pendiente'";
    else
        $consulta = "SELECT SUM(salfinal) as total FROM nomprestamos_detalles as pd inner join nomprestamos_cabecera as pc on (pd.numpre=pc.numpre) WHERE pd.ficha=$ficha and pd.fechaven between '$inicio' and '$final' and pd.estadopre='Pendiente' and pc.codigopr='$tipo'";
    $resultado = query($consulta, $conexion);
    $fetch = fetch_array($resultado);
    return $fetch[total];
}

function lunes_per2($inicio, $final, $fecing, $prt) {
//funcion lunes
    $cadena = explode("-", $final);
    if ($cadena[2] > 28)
        $cadena[2] = 30;
    $final2 = $cadena[0] . "-" . $cadena[1] . "-" . $cadena[2];
    //$mes=date($cadena[1]);
    //$ano=date("Y");
    if ($prt == 1)
        if (($fecing > $inicio) && ($fecing <= $final)) {
            $cadena = explode("-", $fecing);
            if ($cadena[2] == 31)
                $cadena[2] = 30;
            $fecha_lunes = $cadena[0] . "-" . $cadena[1] . "-" . $cadena[2];
        }
        else
            $fecha_lunes = $inicio;
    else
        $fecha_lunes = $inicio;
    //$fecha_lunes= $cadena[0]."-".$cadena[1]."-01";
    $num_dias = antiguedad($fecha_lunes, $final2, "D") + 1;
    $dia_inicio = date("N", strtotime($fecha_lunes));
    $LUNES = 0;
    for ($i = 1; $i <= $num_dias; $i++) {
        if ($dia_inicio == 1) {
            $dia_inicio++;
            $LUNES++;
        } elseif ($dia_inicio == 7) {
            $dia_inicio = 1;
        } else {
            $dia_inicio++;
        }
    }
    return $LUNES;
}

function sumadia($fecha, $dia) {
    //list($year,$mon,$day) = explode('-',$fecha);
    return date("Y-m-d", strtotime("$fecha + $dia days")); //date('Y-m-d',mktime(0,0,0,$mon,$day+$dia,$year));		
}

function antiguedadliq($fecha1, $fecha2, $opcion, $preaviso, $codnom, $ficha) {
    //opcion 1 para bono vacacional
    //opcion 2 para dias de disfrute
    global $conexion;
    //$conexion = conexion();
    $consulta = "SELECT diasbonvac, diasmaxinc, diasdisfrute, diasincrem, diasincremdis, diasmaxincdis, antigincremvac  FROM nomtipos_nomina WHERE codtip=$_SESSION[codigo_nomina]";
    $resultado = query($consulta, $conexion);
    $fetch = fetch_array($resultado);
    if ($opcion == 1) {
        $dias_defecto = $fetch['diasbonvac'];
        $dias_incremento = $fetch['diasincrem'];
        $dias_max_incremento = $fetch['diasmaxinc'];
    } elseif ($opcion == 2) {
        $dias_defecto = $fetch['diasdisfrute'];
        $dias_incremento = $fetch['diasincremdis'];
        $dias_max_incremento = $fetch['diasmaxincdis'];
    }

    $ant = antiguedad($fecha1, $fecha2, "A");
    $antmes = antiguedad($fecha1, $fecha2, "M");
    $mes1 = substr($fecha1, 5, 2);
    $dia1 = substr($fecha1, 8, 2);
    $mes2 = substr($fecha2, 5, 2);
    $dia2 = substr($fecha2, 8, 2);
    $diasant = 0;
    $diasadic = 0;
    if ($ant >= 1) {
        $diasant = $dias_defecto;
        if ($ant >= 2) {
            if ((($ant >= $fetch['antigincremvac']) && ($opcion == 2)) || ($opcion == 1)) {
                $diasadic = ($ant - 1) * $dias_incremento;
                if ($diasadic > $dias_max_incremento) {
                    $diasadic = $dias_max_incremento;
                }
                $diasant+=$diasadic;
                if ($mes1 < $mes2) {
                    $diasant+=1;
                } elseif ($mes1 == $mes2) {
                    if ($dia1 < $dia2) {
                        $diasant+=1;
                    }
                }
                if ($antmes >= 1) {
                    $diasant+=1;
                }
            }
        }
    }
    //cerrar_conexion($conexion);
    return $diasant;
}

function tasainteres($fecha) {
    $ano = substr($fecha, 0, 4);
    $mes = substr($fecha, 5, 2);
    //$dia1=substr($fecha1,8,2);
    global $conexion;
    //$conexion = conexion();
    $consulta = "SELECT tasa FROM nomtasas_interes WHERE anio=$ano AND mes=$mes";
    $resultado = query($consulta, $conexion);
    $fetch = fetch_array($resultado);
    //cerrar_conexion($conexion);
    return $fetch['tasa'];
}

function asigmesactual($fechanom, $ficha) {
// 	$anios=antiguedad($fechaing,$fechafinnom,"A");
// 	$meses=antiguedad($fechaing,$fechafinnom,"M");
// 	if((($anios==0)&&($meses>=4))||(($anios>=1)))
// 	{
    $mes = substr($fechanom, 5, 2);
// 		$mes2=substr($fechaing,5,2);
    $anio = substr($fechanom, 0, 4);
    global $conexion;
    //$conexion = conexion();
    $consulta = "SELECT SUM(monto) AS monto FROM nom_movimientos_nomina WHERE mes=$mes AND anio=$anio AND tipcon='A' AND (codcon<>520 AND codcon<>1402 AND codcon<>519 AND codcon<>1519) AND ficha=$ficha AND tipnom=$_SESSION[codigo_nomina]";
    $resultado = query($consulta, $conexion);
    $fetch = fetch_array($resultado);

    // DOZAVO DE UTILIDADES
// 		$doz_util=$fetch['monto']*(25/100);
// 		$diasvac=antiguedadliq($fechaing,$fechafinnom,2,0,0,0);
// 		$doz_vac=(($fetch['monto']/30)*$diasvac)/12;
// 		$total=$fetch['monto']+$doz_util+$doz_vac;
// 		
// 		if($mes==$mes2)
// 		{
// 			$diasantig=(($anios)*2)+5;
// 		}
// 		$prestacion=($total/30)*$diasantig;
    return $fetch['monto'];
// 		return $prestacion;
    //cerrar_conexion($conexion);
// 	}
// 	else
// 		return;
}

function prestcontratados($cedula, $ficha, $codcon) {
    global $conexion;
    //$conexion = conexion();
    $consulta = "SELECT SUM(montototal) AS monto FROM nomacumulados_det WHERE ceduda=$cedula AND codcon=$codcon ";
    $resultado = query($consulta, $conexion);
    $fetch = fetch_array($resultado);

    //cerrar_conexion($conexion);
    return $fetch['monto'];
}

function prestcontratadosref($cedula, $ficha, $codcon) {
    global $conexion;
    //$conexion = conexion();
    $consulta = "SELECT SUM(refer) AS monto FROM nomacumulados_det WHERE ceduda=$cedula AND ficha=$ficha AND codcon=$codcon";
    $resultado = query($consulta, $conexion);
    $fetch = fetch_array($resultado);

    //cerrar_conexion($conexion);
    return $fetch['monto'];
}

function asigmensuales($ficha, $mes, $anio) {
    global $conexion;
    //$conexion = conexion();
    $consulta = "SELECT SUM(monto) AS monto FROM nom_movimientos_nomina WHERE mes=$mes AND anio=$anio AND tipcon='A' AND (codcon<>520 OR codcon<>1402) AND ficha=$ficha AND tipnom=$_SESSION[codigo_nomina]";
    $resultado = query($consulta, $conexion);
    $fetch = fetch_array($resultado);

    //cerrar_conexion($conexion);
    return $fetch['monto'];
}

function anticipos($cedula) {
    global $conexion;
    //$conexion = conexion();
    $consulta = "SELECT SUM(monto) AS monto FROM nomexpediente WHERE tipo_registro='Antic. prestaciones' AND cedula=$cedula";
    $resultado = query($consulta, $conexion);
    $fetch = fetch_array($resultado);

    //cerrar_conexion($conexion);
    return $fetch['monto'];
}

function diasreposo($cedula, $anio) {
    global $conexion;
    //$conexion = conexion();
    $fecha1 = ($anio - 1) . "-11-" . "01";
    $fecha2 = $anio . "-10-" . "31";
    $consulta = "SELECT SUM(dias) AS dias FROM nomexpediente WHERE tipo_registro='Permisos' AND tipo_tiporegistro=4 AND cedula=$cedula AND fecha_salida BETWEEN '$fecha1' AND '$fecha2'";
    $resultado = query($consulta, $conexion);
    $fetch = fetch_array($resultado);

    //cerrar_conexion($conexion);
    return $fetch['dias'];
}

function vacbonopendiente($cedula, $fecha, $opcion) {
    global $conexion;
    //$conexion = conexion();
    if ($opcion == 1) {
        $consulta = "SELECT fecha_venc FROM nom_progvacaciones WHERE estado='Pendiente' AND tipnom=$_SESSION[codigo_nomina] AND ceduda=$cedula AND tipooper='DV'";
        $resultado1 = query($consulta, $conexion);
        $fetch1 = fetch_array($resultado1);

        if ($fecha < $fetch1['fecha_venc'])
            return 0;

        $consulta = "SELECT SUM(ddisfrute) AS dias FROM nom_progvacaciones WHERE estado='Pendiente' AND tipnom=$_SESSION[codigo_nomina] AND ceduda=$cedula AND (tipooper='DV' OR tipooper='DA')";
        $resultado = query($consulta, $conexion);
        $fetch = fetch_array($resultado);

        $consulta = "SELECT SUM(dpagob) AS dpagob FROM nom_progvacaciones WHERE estado='Pendiente' AND tipnom=$_SESSION[codigo_nomina] AND ceduda=$cedula AND tipooper='DV'";
        $resultado2 = query($consulta, $conexion);
        $fetch2 = fetch_array($resultado2);
        return $fetch['dias'] - $fetch2['dpagob'];
    }
    elseif ($opcion == 2) {
        $consulta = "SELECT fecha_venc FROM nom_progvacaciones WHERE estado='Pendiente' AND tipnom=$_SESSION[codigo_nomina] AND ceduda=$cedula AND tipooper='DV'";
        $resultado1 = query($consulta, $conexion);
        $fetch1 = fetch_array($resultado1);
        if ($fecha < $fetch1[fecha_venc])
            return 0;

        $consulta = "SELECT SUM(dpago) AS bono FROM nom_progvacaciones WHERE estado='Pendiente' AND tipnom=$_SESSION[codigo_nomina] AND ceduda=$cedula AND tipooper='DB'";
        $resultado = query($consulta, $conexion);
        $fetch = fetch_array($resultado);
        return $fetch['bono'];
    }
    //cerrar_conexion($conexion);
}

function meseslaborados($codnom, $codcon, $ficha, $fecha, $fechaing) {
    $anos = substr($fecha, 4, 0);
    $fec = $anos . "-01-01";
    if ($fechaing > $fec) {
        $meses = antiguedad($fechaing, $fecha, "M");
        return $meses;
    } else {
        $meses = antiguedad($fec, $fecha, "M");
        return $meses;
    }
}

/* function meseslaborados($codnom,$codcon,$ficha,$fecha)
  {
  $meses=substr($fecha,5,2);
  $dias=substr($fecha,8,2);
  if($dias<25)
  return $meses-1;
  else
  return $meses;
  cerrar_conexion($conexion);
  } */

function diashabilesvac($cedula, $fecha) {
    global $conexion;
    //$conexion = conexion();
    $anio = substr($fecha, 0, 4);
    $consulta = "SELECT SUM(ddisfrute) AS dias FROM nom_progvacaciones WHERE (tipooper='DA' OR tipooper='DV') AND ceduda=$cedula AND periodo=$anio AND estado='Pendiente'";
    $resultado = query($consulta, $conexion);
    $fetch = fetch_array($resultado);
    return $fetch['dias'];
    //cerrar_conexion($conexion);
}

function diashabilesvacsinperiodo($cedula) {
    global $conexion;
    //$conexion = conexion();
    $anio = substr($fecha, 0, 4);
    $consulta = "SELECT SUM(ddisfrute) AS dias FROM nom_progvacaciones WHERE (tipooper='DA' OR tipooper='DV') AND ceduda=$cedula  AND estado='Pendiente' AND fechavac<>'0000-00-00' and fechareivac<>'0000-00-00'";
    $resultado = query($consulta, $conexion);
    $fetch = fetch_array($resultado);
    return $fetch['dias'];
    //cerrar_conexion($conexion);
}

function bonovac($cedula, $fecha) {
    global $conexion;
    //$conexion = conexion();
    $anio = substr($fecha, 0, 4);
    $consulta = "SELECT SUM(dpago) AS bono FROM nom_progvacaciones WHERE tipooper='DB' AND ceduda=$cedula AND periodo=$anio AND estado='Pendiente'";
    $resultado = query($consulta, $conexion);
    $fetch = fetch_array($resultado);
    if ($fetch['bono'] == '')
        return 0;
    else
        return $fetch['bono'];
    //cerrar_conexion($conexion);
}

function diabonovac($cedula, $fecha) {
    global $conexion;
    //$conexion = conexion();
    $anio = substr($fecha, 0, 4);
    $consulta = "SELECT SUM(dpagob) AS bono FROM nom_progvacaciones WHERE (tipooper='DB'  OR tipooper='DI') AND ceduda=$cedula AND periodo=$anio AND estado='Pendiente'";
    $resultado = query($consulta, $conexion);
    $fetch = fetch_array($resultado);
    if ($fetch['bono'] == '')
        return 0;
    else
        return $fetch['bono'];
    //cerrar_conexion($conexion);
}

function diabonovacsinperiodo($cedula) {
    global $conexion;
    //$conexion = conexion();
    $anio = substr($fecha, 0, 4);
    //con el bono vacacional en nomina general
    $consulta_u = "SELECT periodo  FROM nom_progvacaciones WHERE tipooper='DV' AND ceduda=$cedula  AND estado='Pendiente' and fechavac<>'0000-00-00' and fechareivac<>'0000-00-00' ";
    $resultado_u = query($consulta_u, $conexion);
    $cantidad = 0;
    while ($fila = fetch_array($resultado_u)) {
        $consulta = "SELECT SUM(dpagob) AS bono FROM nom_progvacaciones WHERE (tipooper='DB'  OR tipooper='DI') AND ceduda=$cedula  AND estado='Pendiente' and periodo='" . $fila['periodo'] . "' ";
        $resultado = query($consulta, $conexion);
        $fetch = fetch_array($resultado);
        if ($fetch['bono'] != '')
            $cantidad+= $fetch['bono'];
    }
    return $cantidad;
    //cerrar_conexion($conexion);
}

function reintvac($fechareivac, $fechanomina, $fechafinnom) {
    if (($fechareivac >= $fechanomina) && ($fechareivac <= $fechafinnom))
        return $resp = "SI";
    else
        return $resp = "NO";
}

function diasnohabiles($cedula, $fecha1) {
    global $conexion;
    //$conexion = conexion();
    $diasvac = diashabilesvac($cedula, $fecha1);
    $anio = substr($fecha1, 0, 4);
    $consulta = "SELECT fechavac FROM nom_progvacaciones WHERE tipooper='DV' AND ceduda=$cedula AND periodo=$anio AND estado='Pendiente'";
    $resultado = query($consulta, $conexion);
    $fetch = fetch_array($resultado);

    $fecha = explode("-", $fetch['fechavac']);
    $ano = $ano0 = $fecha[0];
    $mes = $mes0 = $fecha[1];
    $dia = $dia0 = $diainivac = $fecha[2];
    $fechainivac = $ano . "-" . $mes . "-" . $dia;
    $i = 0;

    $iniciomes = $ano . "-" . $mes . "-01";
    $numdiasmes = date("t", strtotime($iniciomes));
    $contador = 0;
    //echo "<br>";
    while ($i < $diasvac) {
        if ($diainivac == 1) {
            //$diainivac=1;
            $diainivac = "0" . $diainivac;
            $i = $i + 1;
            if ($mes == 12) {
                $mes = 1;
                $ano = $ano + 1;
                $iniciomes = $ano . "-" . $mes . "-01";
                $numdiasmes = date("t", strtotime($iniciomes));
            } elseif ($i == 1) {

                if ($mes <= 9)
                    $mes = "0" . $mes;
                $iniciomes = $ano . "-" . $mes . "-01";
                $numdiasmes = date("t", strtotime($iniciomes));
            }
            else {
                $mes = $mes + 1;
                if ($mes <= 9)
                    $mes = "0" . $mes;
                $iniciomes = $ano . "-" . $mes . "-01";
                $numdiasmes = date("t", strtotime($iniciomes));
            }
        }
        else {
            //$diainivac=$diainivac+1;
            if ($diainivac <= 9)
                $diainivac = "0" . $diainivac;
            $i = $i + 1;
        }
        $fechaconsulta = $ano . "-" . $mes . "-" . $diainivac;
        $consulta = "SELECT dia_fiesta FROM nomcalendarios_tiposnomina WHERE cod_tiponomina='" . $_SESSION['codigo_nomina'] . "' AND fecha='" . $fechaconsulta . "'";
        $resultado2 = query($consulta, $conexion);
        $fetch2 = fetch_array($resultado2);
        $fetch2['dia_fiesta'];
        if (($fetch2['dia_fiesta'] == 3) || ($fetch2['dia_fiesta'] == 1)) {
            $i-=1;
            $contador+=1;
        }
        if ($numdiasmes == $diainivac)
            $diainivac = 1;
        else
            $diainivac = $diainivac + 1;
    }
    return $contador;
    //cerrar_conexion($conexion);
}

function diasnohabilessinperiodo($cedula) {
    global $conexion;
    //$conexion = conexion();
    //echo $diasvac=diashabilesvacsinperiodo($cedula);
    //$anio=substr($fecha1,0,4);
    $consulta = "SELECT fechavac FROM nom_progvacaciones WHERE tipooper='DV' AND ceduda=$cedula AND fechavac<>'0000-00-00' and fechareivac<>'0000-00-00' AND estado='Pendiente'";
    $resultado = query($consulta, $conexion);
    $contador = 0;
    while ($fetch = fetch_array($resultado)) {

        $consulta_dia = "SELECT sum(ddisfrute) as dias FROM nom_progvacaciones WHERE (tipooper='DV' or tipooper='DA') AND ceduda=$cedula AND fechavac='" . $fetch['fechavac'] . "' AND estado='Pendiente'";
        $resultado_dia = query($consulta_dia, $conexion);
        $dia_query = fetch_array($resultado_dia);
        $diasvac = $dia_query['dias'];
        $fecha = explode("-", $fetch['fechavac']);
        $ano = $ano0 = $fecha[0];
        $mes = $mes0 = trim($fecha[1]);
        $dia = $dia0 = $diainivac = $fecha[2];
        $fechainivac = $ano . "-" . $mes . "-" . $dia;
        $i = 0;

        $iniciomes = $ano . "-" . $mes . "-01";
        $numdiasmes = date("t", strtotime($iniciomes));

        //echo "<br>";
        while ($i < $diasvac) {
            if ($diainivac == 1) {
                //$diainivac=1;
                $diainivac = "0" . $diainivac;
                $i = $i + 1;
                if ($mes == 12) {
                    $mes = 1;
                    $ano = $ano + 1;
                    $iniciomes = $ano . "-" . $mes . "-01";
                    $numdiasmes = date("t", strtotime($iniciomes));
                } elseif ($i == 1) {

                    if ($mes <= 9)
                        $mes = "0" . $mes;
                    $iniciomes = $ano . "-" . $mes . "-01";
                    $numdiasmes = date("t", strtotime($iniciomes));
                }
                else {
                    $mes = $mes + 1;
                    if ($mes <= 9)
                        $mes = "0" . $mes;
                    $iniciomes = $ano . "-" . $mes . "-01";
                    $numdiasmes = date("t", strtotime($iniciomes));
                }
            }
            else {
                //$diainivac=$diainivac+1;
                if ($diainivac <= 9)
                    $diainivac = "0" . $diainivac;
                $i = $i + 1;
            }
            $fechaconsulta = $ano . "-" . $mes . "-" . $diainivac;
            $consulta = "SELECT dia_fiesta FROM nomcalendarios_tiposnomina WHERE cod_tiponomina='" . $_SESSION['codigo_nomina'] . "' AND fecha='" . $fechaconsulta . "'";
            $resultado2 = query($consulta, $conexion);
            $fetch2 = fetch_array($resultado2);
            $fetch2['dia_fiesta'];
            if (($fetch2['dia_fiesta'] == 3) || ($fetch2['dia_fiesta'] == 1)) {
                $i-=1;
                $contador+=1;
            }
            if ($numdiasmes == $diainivac)
                $diainivac = 1;
            else
                $diainivac = $diainivac + 1;
        }
    }
    return $contador;
    //cerrar_conexion($conexion);
}

function baremo($codigo_baremo, $valor) {
    $val = 0;
    $resultado = 0;
    global $conexion;
    //$conexion = conexion();
    $consulta = "select codigo from nombaremos where codigo='" . $codigo_baremo . "'";
    $result = query($consulta, $conexion);
     if (num_rows($result) == 0) {
        return;
    }

    $consulta = "select limite_menor, limite_mayor, monto from nomtarifas where codigo='" . $codigo_baremo . "'";
    $resultado = query($consulta, $conexion);


    if (num_rows($resultado) == 0) {
        return;
    }
    while ($fila = fetch_array($resultado)) {
        if ($fila['limite_menor'] <= $valor and $fila['limite_mayor'] >= $valor) {
            return $fila['monto'];
        }
    }
    return;
}

function conceptonomant($codigo_concepto,$opcion){
//========================================================================================
// FUNCION CONCEPTONOMANT: Devuelve el valor del monto del concepto en la nomina anterior
// codigo_concepto = Codigo del concepto
// opcion   = '1' referencia, '2' monto resultado
//========================================================================================
	include 'globales.php';
	//echo $CODNOM;
    global $conexion;
	//$conexion=conexion();
	$consulta="select MAX(codnom) as codnom from nom_movimientos_nomina where tipnom='".$_SESSION['codigo_nomina']."' AND codnom<'$CODNOM' and codcon='$codigo_concepto' and mes=month('$FECHAFINNOM') and anio=year('$FECHAFINNOM')";
	$resultado=query($consulta,$conexion);
	$fila=fetch_array($resultado);
	if($fila['codnom']>1){
		$ultima_nomina=$fila['codnom'];
	}else{
		//$ultima_nomina=1;
        return 0;
	}
	$consulta="select valor, monto from nom_movimientos_nomina where codnom='$ultima_nomina' and ficha='$FICHA' and codcon='".$codigo_concepto."' and tipnom='".$_SESSION['codigo_nomina']."'";
	$resultado=query($consulta,$conexion);

	if(num_rows($resultado)==0){
	return 0;
	}
	$fila=fetch_array($resultado);
	switch($opcion){
		case '1':
	return $fila['valor'];
		case '2':
	return $fila['monto'];
	}
}

function buscarcuantosconcepto($codigo_concepto, $opcion) {
    //me dice cuantas nomina no le he cobrado este concepto
    include 'globales.php';
    global $conexion;
    //$conexion = conexion();
    $consulta = "select codnom from nomtipos_nomina where codtip='" . $_SESSION['codigo_nomina'] . "'";
    $resultado = query($consulta, $conexion);
    $fila = fetch_array($resultado);
    $consulta = "select codnom from nom_movimientos_nomina where tipnom='" . $_SESSION['codigo_nomina'] . "' and codnom<'" . $fila['codnom'] . "'  group by codnom order by codnom DESC";
    $resultado2 = query($consulta, $conexion);
    $i = 0;
    while ($fila = fetch_array($resultado2)) {
        $consulta_con = "select monto from nom_movimientos_nomina where codnom='" . $fila['codnom'] . "' and ficha='$FICHA' and codcon='" . $codigo_concepto . "' and tipnom='" . $_SESSION['codigo_nomina'] . "'";
        $resultado_con = query($consulta_con, $conexion);
        $concepto = fetch_array($resultado_con);
        if ($concepto['monto'] == 0) {
            $i++;
        } else {
            break;
        }
    }
    return $i;
}

function conceptonomant2ultimavac($codigo_concepto, $opcion, $tipo) {
//========================================================================================
// FUNCION CONCEPTONOMANT: Devuelve el valor del monto del concepto en la nomina anterior
// codigo_concepto = Codigo del concepto
// opcion   = '1' referencia, '2' monto resultado
//========================================================================================
    include 'globales.php';
    global $conexion;
    //$conexion = conexion();
    $consulta = "select codnom from nomtipos_nomina where codtip='" . $tipo . "'";
    $resultado = query($consulta, $conexion);
    $fila = fetch_array($resultado);
    if (num_rows($resultado) > 0) {
        $ultima_nomina = $fila['codnom'] - 1;
    } else {
        $ultima_nomina = 0;
    }
    $consulta = "select max(nom.codnom) as codnom from nom_movimientos_nomina as nom inner join nom_nominas_pago as pag on pag.codnom=nom.codnom where pag.tipnom='" . $tipo . "' and pag.frecuencia<>8 and nom.tipnom='" . $tipo . "' and nom.codnom<" . $ultima_nomina;
    $resultado = query($consulta, $conexion);
    $fila = fetch_array($resultado);
    if (num_rows($resultado) > 0) {
        $ultima_nomina = $fila['codnom'];
    } else {
        $ultima_nomina = 0;
    }


    $consulta = "select max(nom.codnom) as codnom from nom_movimientos_nomina as nom inner join nom_nominas_pago as pag on pag.codnom=nom.codnom where pag.tipnom='" . $tipo . "' and pag.frecuencia<>8 and nom.tipnom='" . $tipo . "' and nom.codnom<" . $ultima_nomina;
    $resultado = query($consulta, $conexion);
    $fila = fetch_array($resultado);
    if (num_rows($resultado) > 0) {
        $ultima_nomina2 = $fila['codnom'];
    } else {
        $ultima_nomina2 = 0;
    }


    $consulta = "select valor, monto from nom_movimientos_nomina where codnom='$ultima_nomina' and ficha='$FICHA' and codcon='" . $codigo_concepto . "' and tipnom='" . $tipo . "'";
    $resultado = query($consulta, $conexion);

    $consulta2 = "select valor, monto from nom_movimientos_nomina where codnom='$ultima_nomina2' and ficha='$FICHA' and codcon='" . $codigo_concepto . "' and tipnom='" . $tipo . "'";
    $resultado2 = query($consulta2, $conexion);
    $valor = 0;
    $ref = 0;

    $fila = fetch_array($resultado);
    switch ($opcion) {
        case '1':
            $ref+= $fila['valor'];
        case '2':
            $valor+= $fila['monto'];
    }
    $fila = fetch_array($resultado2);
    switch ($opcion) {
        case '1':
            $ref+= $fila['valor'];
        case '2':
            $valor+= $fila['monto'];
    }
    switch ($opcion) {
        case '1':
            return $ref;
        case '2':
            return $valor;
    }
}

function horasbrentwood($ficha, $fechaini, $fechafin, $opc)
{

    $limiteExtrasOrd    =18;
    $limiteExtrasOrdMin =$limiteExtrasOrd*60;
    if($opc==1)
        $consulta = "SELECT SUM(horas_trabajadas_num) AS horas_trabajadas_num FROM vig_asistencia WHERE id_empleado='$ficha'";
    elseif($opc==2)
        $consulta = "SELECT SUM(horas_extra_num) AS horas_extra_num FROM vig_asistencia WHERE id_empleado='$ficha'";
    elseif($opc==3)
        $consulta = "SELECT SUM(horas_extras_signo) AS horas_extras_signo FROM vig_asistencia WHERE id_empleado='$ficha'";
        elseif($opc==4)
        $consulta = "SELECT SUM(tardanza) AS tardanza FROM vig_asistencia WHERE id_empleado='$ficha'";
    elseif($opc==5)
        $consulta = "SELECT SUM(inasistencia) AS inasistencia FROM vig_asistencia WHERE id_empleado='$ficha'";

    $fetch    = $conn->GetArray($SQL);
    $horas    = (($fetch[horas_trabajadas_num]/60) == '' ? 0 : ($fetch[horas_trabajadas_num]/60));
    $horasmin = ($fetch[horas_trabajadas_num] == '' ? 0 : $fetch[horas_trabajadas_num]);

    if($opc==1)
    {
        $query       = "select ifnull(count(dia_fiesta),0) as dias from nomcalendarios_tiposnomina where fecha BETWEEN '$fechaini' and '$fechafin'  and dia_fiesta='3'";
        $result      = query($query,$conexion);
        $fetch       = fetch_array($result);
        $dias        = $fetch[dias] * 480;
        
        $consulta    = "SELECT SUM(horas_trabajadas_num) AS horas_trabajadas_num FROM vig_asistencia WHERE concepto='nacional' AND id_empleado='$ficha'";
        $resultado   = query($consulta, $conexion);
        $fetch       = fetch_array($resultado);
        $horasminnac = ($fetch[horas_trabajadas_num] == '' ? 0 : $fetch[horas_trabajadas_num]);
        
        
        $horas       = ($horasmin + $dias)/60;
    }

    if($opc       ==2)
    {
        $consulta     = "SELECT fecha, horas_extra_num  FROM `vig_asistencia` WHERE id_empleado='$ficha'   group by fecha order by fecha  ";
        $resultado    = query($consulta, $conexion);
        $extranoc     = $extra = $total = 0;
        while($fetch2 = fetch_array($resultado))
        {
            $extra        += $fetch2[horas_extra_num];
            $total        += $fetch2[horas_extra_num];
                
            if(($total/60)>$limiteExtrasOrd)
            {
            $horas        =$extra/60;
            break;
            }
        }
    }

    if($opc==3)
    {
        $consulta     = "SELECT fecha, horas_extras_signo  FROM `vig_asistencia` WHERE id_empleado='$ficha'   group by fecha order by fecha  ";
        $resultado    = query($consulta, $conexion);
        $i            = $extranoc = $extra = $total = 0;
        while($fetch2 = fetch_array($resultado))
            {
            $extra    += $fetch2[horas_extras_signo];
            $total    += $fetch2[horas_extras_signo];
           if(($total/60)>$limiteExtrasOrd) 
            {
                if($i == 1)
                    $horasmin += $fetch2[horas_extras_signo];
                $i = 1;
            }
        }
        $horas = $horasmin/60;
    }

    if($opc==5)
    {
        $consulta     = "SELECT fecha, inasistencia  FROM `vig_asistencia` WHERE id_empleado='$ficha'   group by fecha order by fecha  ";
        $resultado    = query($consulta, $conexion);
        $extranoc     = $extra = $total = 0;
        while($fetch2 = fetch_array($resultado))
        {
            $inasistencia += $fetch2[inasistencia];
            $total        += $fetch2[inasistencia];

        }
    }

    if($opc==4)
    {
        $consulta  = "SELECT fecha, tardanza  FROM `vig_asistencia` WHERE id_empleado='$ficha'   group by fecha order by fecha  ";
        $resultado = query($consulta, $conexion);
        $j         = $i = $extranoc = $extra = $total = 0;
        while($fetch2 = fetch_array($resultado))
        {
            $tardanza += $fetch2[tardanza];
            $total    += $fetch2[tardanza]+$fetch2[extranoc];

            
        }
        $horas = $horasmin/60;
    }



    return number_format($horas,2,'.','');
    //break;
 }
function conceptonomant2ultima($codigo_concepto, $opcion) {
//========================================================================================
// FUNCION CONCEPTONOMANT: Devuelve el valor del monto del concepto en la nomina anterior
// codigo_concepto = Codigo del concepto
// opcion   = '1' referencia, '2' monto resultado
//========================================================================================
    include 'globales.php';
    global $conexion;
    //$conexion = conexion();
    $consulta = "select codnom from nomtipos_nomina where codtip='" . $_SESSION['codigo_nomina'] . "'";
    $resultado = query($consulta, $conexion);
    $fila = fetch_array($resultado);

    $consulta = "select max(nom.codnom) as codnom from nom_movimientos_nomina as nom inner join nom_nominas_pago as pag on pag.codnom=nom.codnom where pag.tipnom='" . $_SESSION['codigo_nomina'] . "' and pag.frecuencia<>8 and nom.tipnom='" . $_SESSION['codigo_nomina'] . "' and nom.codnom<" . ($fila['codnom'] - 1);
    $resultado = query($consulta, $conexion);
    $fila = fetch_array($resultado);
    if (num_rows($resultado) > 0) {
        $ultima_nomina = $fila['codnom'];
    } else {
        $ultima_nomina = 0;
    }
    $consulta = "select max(nom.codnom) as codnom from nom_movimientos_nomina as nom inner join nom_nominas_pago as pag on pag.codnom=nom.codnom where pag.tipnom='" . $_SESSION['codigo_nomina'] . "' and pag.frecuencia<>8 and nom.tipnom='" . $_SESSION['codigo_nomina'] . "' and nom.codnom<" . $ultima_nomina;
    $resultado = query($consulta, $conexion);
    $fila = fetch_array($resultado);
    if (num_rows($resultado) > 0) {
        $ultima_nomina2 = $fila['codnom'];
    } else {
        $ultima_nomina2 = 0;
    }

    $consulta = "select valor, monto from nom_movimientos_nomina where codnom='$ultima_nomina' and ficha='$FICHA' and codcon='" . $codigo_concepto . "' and tipnom='" . $_SESSION['codigo_nomina'] . "'";
    $resultado = query($consulta, $conexion);

    $consulta2 = "select valor, monto from nom_movimientos_nomina where codnom='$ultima_nomina2' and ficha='$FICHA' and codcon='" . $codigo_concepto . "' and tipnom='" . $_SESSION['codigo_nomina'] . "'";
    $resultado2 = query($consulta2, $conexion);
    $valor = 0;
    $ref = 0;

    $fila = fetch_array($resultado);
    switch ($opcion) {
        case '1':
            $ref+= $fila['valor'];
        case '2':
            $valor+= $fila['monto'];
    }
    $fila = fetch_array($resultado2);
    switch ($opcion) {
        case '1':
            $ref+= $fila['valor'];
        case '2':
            $valor+= $fila['monto'];
    }
    switch ($opcion) {
        case '1':
            return $ref;
        case '2':
            return $valor;
    }
}

function conceptonomant2ultimaSF($codigo_concepto, $ficha, $opcion) {
//========================================================================================
// FUNCION CONCEPTONOMANT: Devuelve el valor del monto del concepto en la nomina anterior
// codigo_concepto = Codigo del concepto
// opcion   = '1' referencia, '2' monto resultado
//========================================================================================
    include 'globales.php';
    global $conexion;
    //$conexion = conexion();
    $consulta = "select codnom from nomtipos_nomina where codtip='" . $_SESSION['codigo_nomina'] . "'";
    $resultado = query($consulta, $conexion);
    $fila = fetch_array($resultado);

    $consulta = "select max(nom.codnom) as codnom from nom_movimientos_nomina as nom inner join nom_nominas_pago as pag on pag.codnom=nom.codnom where pag.tipnom='" . $_SESSION['codigo_nomina'] . "' and pag.frecuencia<>8 and nom.tipnom='" . $_SESSION['codigo_nomina'] . "' and nom.codnom<" . ($fila['codnom'] - 1);
    $resultado = query($consulta, $conexion);
    $fila = fetch_array($resultado);
    if (num_rows($resultado) > 0) {
        $ultima_nomina = $fila['codnom'];
    } else {
        $ultima_nomina = 0;
    }
    $consulta = "select max(nom.codnom) as codnom from nom_movimientos_nomina as nom inner join nom_nominas_pago as pag on pag.codnom=nom.codnom where pag.tipnom='" . $_SESSION['codigo_nomina'] . "' and pag.frecuencia<>8 and nom.tipnom='" . $_SESSION['codigo_nomina'] . "' and nom.codnom<" . $ultima_nomina;
    $resultado = query($consulta, $conexion);
    $fila = fetch_array($resultado);
    if (num_rows($resultado) > 0) {
        $ultima_nomina2 = $fila['codnom'];
    } else {
        $ultima_nomina2 = 0;
    }

    $consulta = "select valor, monto from nom_movimientos_nomina where codnom='$ultima_nomina' and ficha='" . $ficha . "' and codcon='" . $codigo_concepto . "' and tipnom='" . $_SESSION['codigo_nomina'] . "'";
    $resultado = query($consulta, $conexion);

    $consulta2 = "select valor, monto from nom_movimientos_nomina where codnom='$ultima_nomina2' and ficha='" . $ficha . "' and codcon='" . $codigo_concepto . "' and tipnom='" . $_SESSION['codigo_nomina'] . "'";
    $resultado2 = query($consulta2, $conexion);
    $valor = 0;
    $ref = 0;

    $fila = fetch_array($resultado);
    switch ($opcion) {
        case '1':
            $ref+= $fila['valor'];
        case '2':
            $valor+= $fila['monto'];
    }
    $fila = fetch_array($resultado2);
    switch ($opcion) {
        case '1':
            $ref+= $fila['valor'];
        case '2':
            $valor+= $fila['monto'];
    }
    switch ($opcion) {
        case '1':
            return $ref;
        case '2':
            return $valor;
    }
}

function campoadicionalper($idcampo) {
    include 'globales.php';
    global $conexion;
    //$conexion = conexion();
    $consulta = "select valor from nomcampos_adic_personal where id='" . $idcampo . "' and ficha='" . $FICHA . "' and tiponom=$_SESSION[codigo_nomina]";
    $resultado = query($consulta, $conexion);
    if (num_rows($resultado) == 0) {
        return;
    } else {
        $fila = fetch_array($resultado);
        return $fila['valor'];
    }
}

function puntos($nivel, $codnivel) {
    global $conexion;
    //$conexion = conexion();
    $consulta = "SELECT sum(grado) as suma FROM nomcargos  as nc inner join nompersonal as np on (nc.cod_car=np.codcargo) where np.codnivel$nivel=$codnivel";
    $resultado = query($consulta, $conexion);
    $fila = fetch_array($resultado);
    return $fila['suma'];
}

function entrefechas($inicio, $fin, $fecha) {
    if ($fecha == null || $fecha == "") {
        return "NO";
    } else {
        $fecha = sumadia($fecha, 1);
        if (($fecha >= $inicio) && ($fecha <= $fin))
            return "SI";
        else
            return "NO";
    }
}

function control($cedula, $concepto) {
    global $conexion;
    //$conexion = conexion();
    #$consulta = "SELECT sum(valor) as suma FROM procesar where concepto='" . $concepto . "' and trabajador='" . $cedula . "'";
    $consulta = "SELECT valor FROM procesar p INNER JOIN nompersonal np ON p.trabajador = np.ficha WHERE p.concepto = '" . $concepto . "' AND np.cedula = '" . $cedula . "'";
    $resultado = query($consulta, $conexion);
    $fila = fetch_array($resultado);
    return $fila['valor'];
}

function hcm($cedula) {
    global $conexion;
    //$conexion = conexion();
    $consulta = "select fecha_nac from nomfamiliares where cedula='" . $cedula . "' and afiliado=1";
    $familiares = query($consulta, $conexion);
    $acumulado = 0;
    while ($fila = fetch_array($familiares)) {
        $anos = antiguedad($fila['fecha_nac'], date('Y-m-d'), 'A');
        $consulta_seg = "select monto_seg from nomseguro where desde_seg<=" . $anos . " and hasta_seg>=" . $anos;
        $monto = query($consulta_seg, $conexion);
        $fila2 = fetch_array($monto);
        $acumulado+=$fila2["monto_seg"];
    }
    return $acumulado;
}

function hcm_total($cedula) {
    global $conexion;
    //$conexion = conexion();
    $consulta = "SELECT fechavac,fechareivac  FROM nom_progvacaciones WHERE (tipooper='DA' OR tipooper='DV') AND ceduda=$cedula  AND estado='Pendiente' AND fechavac<>'0000-00-00' and fechareivac<>'0000-00-00'";
    $resultado = query($consulta, $conexion);
    $suma = 0;
    $monto = hcm($cedula);
    while ($fila = fetch_array($resultado)) {
        $suma++;
    }
    $total = $monto * $suma;
    return $total;
}

function hcm_persona($cedula) {
    global $conexion;
    //$conexion = conexion();
    $consulta = "select fecnac from nompersonal where cedula='" . $cedula . "'";
    $personal = query($consulta, $conexion);
    $fila = fetch_array($personal);
    $anos = antiguedad($fila['fecnac'], date('Y-m-d'), 'A');
    $consulta_seg = "select monto_seg from nomseguro where desde_seg<=" . $anos . " and hasta_seg>=" . $anos;
    $monto = query($consulta_seg, $conexion);
    $fila = fetch_array($monto);
    $acumulado = $fila["monto_seg"];

    return $acumulado;
}

function suma_fechas($fecha, $ndias) {
//$fecha="2005-10-03"; // tu sabrÃ¡s como la obtienes, solo asegurate que tenga este formato

    $nuevafecha = date("Y-m-d", strtotime("$fecha +$ndias day"));
    return ($nuevafecha);
}

function cumpleperiodo($fecha, $fechades, $fechahas) {
    if (($fecha >= $fechades)) {
        return $resp = "NO";
    } else {
        $diaantes = suma_fechas($fechades, -1);
        $antes = antiguedad($fecha, $diaantes, "A");
        $ahora = antiguedad($fecha, $fechahas, "A");

        if (($antes == $ahora))
            return $resp = "NO";
        else
            return $resp = "SI";
    }
}

function diastrabajados($cedula, $cod_concepto, $fecha_ini, $fecha_fin) {
    global $conexion;
    //$conexion = conexion();
    $consulta = "SELECT cod_enca FROM control_encabezado WHERE fecha_ini = '{$fecha_ini}' AND fecha_fin = '{$fecha_fin}'";
    $result = query($consulta, $conexion);
    $control_encabezado = fetch_array($result);

    $consulta = "SELECT ficha FROM nompersonal WHERE cedula = {$cedula}";
    $result = query($consulta, $conexion);
    $nompersonal = fetch_array($result);

    #$consulta = "SELECT valor FROM control_acceso ca INNER JOIN nompersonal np ON ca.cod_trabajador = np.ficha AND np.cedula = {$cedula} WHERE ca.cod_enca = {$control_encabezado['cod_enca']} AND ca.concepto = {$cod_concepto} AND ca.cod_compania = {$_SESSION['cod_empresa']} AND ca.cod_nomina = {$_SESSION['codigo_nomina']}";
    $consulta = "SELECT valor FROM control_acceso WHERE cod_trabajador = {$nompersonal['ficha']} AND cod_enca = {$control_encabezado['cod_enca']} AND concepto = {$cod_concepto} AND cod_compania = {$_SESSION['cod_empresa']} AND cod_nomina = {$_SESSION['codigo_nomina']}";
    $personal = query($consulta, $conexion);
    $fila = fetch_array($personal);
    #$anos = antiguedad($fila['fecnac'], date('Y-m-d'), 'A');
    #$consulta_seg = "select * from nomseguro where desde_seg<=" . $anos . " and hasta_seg>=" . $anos;
    #$monto = query($consulta_seg, $conexion);
    #$fila = fetch_array($monto);
    return $fila["valor"];
}

//ARMADILLO
//LVIERA@ARMADILLOTEC.COM
//FUNCION QUE DEVUELVE LA SUMATORIA POR CONCEPTO Y RANGO DE FECHA, DICHA 
//SUMATORIA ESTA CONDICIONADA CON PARAMETRO DE FRECUENCIA.
function SUMA_POR_CONCEPTO($codcon,$fecha_inicio,$fecha_fin,$frecuencias=""){
	include 'globales.php';
    global $conexion;
	//$conexion=conexion();

$consulta = "
SELECT sum(monto) as suma FROM `nom_movimientos_nomina`  nmon  inner join nom_nominas_pago p on p.codnom = nmon.codnom
 where   nmon.ficha=$FICHA AND nmon.tipnom=".$_SESSION[codigo_nomina]."
and p.fechapago >= '$fecha_inicio' and p.fechapago <= '$fecha_fin'  and nmon.codcon = ".$codcon;
if($frecuencias!=""){
	$consulta .= " and p.frecuencia  in(   ".$frecuencias." )";
}

	$resultado=query($consulta,$conexion);
	$fila=fetch_array($resultado);
	$suma=$fila['suma'];
	return $suma;
}

// cuenta los dias laborables de un mes segun fecha inicio y fin nomina segun calendario 
function dias_laborables_mes($FECHAFINNOM) {
    include 'globales.php';
    global $conexion;
    //$conexion = conexion();
    $anio = substr($FECHAFINNOM, 0, 4);
    $mes = substr($FECHAFINNOM, 5, 2);
    $consulta = "SELECT count(*) as dias FROM `nomcalendarios_tiposnomina` WHERE `dia_fiesta` =0 AND YEAR(`fecha`) = $anio AND MONTH(`fecha`)  =$mes";
    $resultado = query($consulta, $conexion);
    $fetch = fetch_array($resultado);
    return $fetch[dias];
}
   function frecuencia($CODNOM) {
    include 'globales.php';
    global $conexion;
    //$conexion = conexion();
           
    $consulta = "SELECT frecuencia FROM `nom_nominas_pago` WHERE codnom=$CODNOM";
    $resultado = query($consulta, $conexion);
    $fetch = fetch_array($resultado);
    return $fetch[frecuencia];
}

//Brentwood INICIO
/*
function conceptosanual($codcon, $fecha)
{
    include 'globales.php';
    $selectra = new bd($_SESSION['bd']);

    $anio = substr($fecha, 0, 4);;
    $consulta = "select sum(monto) as monto from nom_movimientos_nomina where ficha='".$FICHA."' and codcon in ('$codcon') and anio='$anio' and codnom<>'$CODNOM'"; 
    $resultado = $selectra->query($consulta);
    $fila = $resultado->fetch_assoc();
    if ($resultado->num_rows <= 0) {
        return 0;
    } else {
        return $fila['monto'];
    }
}
*/

//OPC = AL CONCEPTO A BUSCAR
function horasregulares($ficha, $fechaini, $fechafin, $opc)
{
    include 'globales.php';
    $selectra = new bd($_SESSION['bd']);
    $consulta = "SELECT SUM(referencia) AS horas FROM caa_procesar_asistencia WHERE fecha BETWEEN '$fechaini' and '$fechafin'  and concepto=$opc AND ficha='$ficha'";
    $resultado = $selectra->query($consulta);
    $fetch = $resultado->fetch_assoc();
    $horas = ($fetch[horas] == '' ? 0 : $fetch[horas]);
    return($horas);

}


//opc= concepto tipo N = novena, E = Extra - novena
function extrasmenosnovena($ficha, $fechaini, $fechafin, $opc, $tipo)
{
    include 'globales.php';
    $selectra = new bd($_SESSION['bd']);
    if ($tipo=='N'|| $tipo=='n') {
    	$consulta = "SELECT COUNT(referencia) AS horas FROM caa_procesar_asistencia WHERE fecha BETWEEN '$fechaini' and '$fechafin'  and concepto=$opc AND ficha='$ficha'";

    }else{
		$consulta = "SELECT SUM(referencia-1) AS horas FROM caa_procesar_asistencia WHERE fecha BETWEEN '$fechaini' and '$fechafin'  and concepto=$opc AND ficha='$ficha'";
    }

    $resultado = $selectra->query($consulta);
    $fetch = $resultado->fetch_assoc();
    $horas = ($fetch[horas] == '' ? 0 : $fetch[horas]);
    //$horas = $fetch[horas];
    return($horas);

}

//opc= concepto (regulares) opc1 = concepto
function extrasadmon($ficha, $fechaini, $fechafin, $opc)
{
    include 'globales.php';
    $selectra = new bd($_SESSION['bd']);

    $consulta = "SELECT SUM(referencia-4) AS horas FROM caa_procesar_asistencia WHERE fecha BETWEEN '$fechaini' AND '$fechafin'  AND ficha='$ficha' AND concepto=$opc AND referencia>4";


    $resultado = $selectra->query($consulta);
    $fetch = $resultado->fetch_assoc();
    $horas = ($fetch[horas] == '' ? 0 : $fetch[horas]);
    //$horas = $fetch[horas];
    return($horas);

}



function horasnacionales($ficha, $fechaini, $fechafin, $opc)
{
    global $conexion;
    //$conexion = conexion();
    $consulta = "SELECT SUM(referencia) AS horas FROM caa_procesar_asistencia WHERE fecha BETWEEN '$fechaini' and '$fechafin'  and concepto=$opc AND ficha='$ficha'";
    $resultado = query($consulta, $conexion);
    $fetch = fetch_array($resultado);
    $horas = $fetch[horas];
    return($horas);

}





//Brentwood FIN






// 1 == HORAS ORDINARIAS, 2 == HORAS EXTRAORDINADIAS, 3 == DOMINGOS, 4 == EXTRAORDINARIAS EXTENDIDAS, 5 == EXTRAS NOCTURNAS, 6 == EXTRAS NOC EXTENDIDAS, 
// 7 == EXTRAS DOMNGOS, 8 == EXTRAS DOM NOC, 9 == TARDANZA , 10 == NACIONAL , 11 == EXTRANACIONAL , 12 == EXTRANOCTURNANACIONAL
function horastrabajadas($ficha, $fechaini, $fechafin, $opc)
{
    global $conexion;
    //$conexion = conexion();
    $select = "select capital from nomempresa";
    $result = query($select,$conexion);
    $filaemp = fetch_array($result);
    $empresa = $filaemp['capital'];
    switch ($empresa)
    {
        case "picadilly":
            $limiteExtrasOrd=18;
            $limiteExtrasOrdMin=$limiteExtrasOrd*60;
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
                $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='extrasnocnac' AND ficha='$ficha'";
            $resultado = query($consulta, $conexion);
            $fetch = fetch_array($resultado);
            $horas = (($fetch[minutos]/60) == '' ? 0 : ($fetch[minutos]/60));
            $horasmin = ($fetch[minutos] == '' ? 0 : $fetch[minutos]);

            if($opc==1)
            {
                $query = "select ifnull(count(dia_fiesta),0) as dias from nomcalendarios_tiposnomina where fecha BETWEEN '$fechaini' and '$fechafin'  and dia_fiesta='3'";
                $result = query($query,$conexion);
                $fetch = fetch_array($result);
                $dias = $fetch[dias] * 480;

                $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='nacionalnac' AND ficha='$ficha'";
                $resultado = query($consulta, $conexion);
                $fetch = fetch_array($resultado);
                $horasminnac = ($fetch[minutos] == '' ? 0 : $fetch[minutos]);

                
                //$horas = ($horasmin + $dias - $horasminnac)/60;
                $horas = ($horasmin + $dias)/60;
            }

            if($opc==2)
            {
                $consulta = "SELECT fecha, sum((case when concepto='extras' then minutos else 0 end)) as extra, sum((case when concepto='extrasnoc' then minutos else 0 end)) as extranoc  FROM `reloj_procesar` WHERE ficha='$ficha'   group by fecha order by fecha  ";
                $resultado = query($consulta, $conexion);
                $extranoc = $extra = $total = 0;
                while($fetch2 = fetch_array($resultado))
                {
                    $extra += $fetch2[extra];
                    $extranoc += $fetch2[extranoc]; 
                    $total += $fetch2[extra]+$fetch2[extranoc];

                    if(($total/60)>$limiteExtrasOrd)
            		{
            			$horas=$extra/60;
                        break;
            		}
                }
            }

        	if($opc==4)
            {
            	$consulta = "SELECT fecha, sum((case when concepto='extras' then minutos else 0 end)) as extra, sum((case when concepto='extrasnoc' then minutos else 0 end)) as extranoc  FROM `reloj_procesar` WHERE ficha='$ficha'   group by fecha order by fecha  ";
                $resultado = query($consulta, $conexion);
                $i = $extranoc = $extra = $total = 0;
                while($fetch2 = fetch_array($resultado))
                {
                    $extra += $fetch2[extra];
                    $extranoc += $fetch2[extranoc]; 
                    $total += $fetch2[extra]+$fetch2[extranoc];

                    if(($total/60)>$limiteExtrasOrd) 
                    {
                        if($i == 1)
                            $horasmin += $fetch2[extra];
                        $i = 1;
                    }
                }
                $horas = $horasmin/60;
            }

            if($opc==5)
            {
                $consulta = "SELECT fecha, sum((case when concepto='extras' then minutos else 0 end)) as extra, sum((case when concepto='extrasnoc' then minutos else 0 end)) as extranoc  FROM `reloj_procesar` WHERE ficha='$ficha'   group by fecha order by fecha  ";
                $resultado = query($consulta, $conexion);
                $extranoc = $extra = $total = 0;
                while($fetch2 = fetch_array($resultado))
                {
                    $extra += $fetch2[extra];
                    $extranoc += $fetch2[extranoc]; 
                    $total += $fetch2[extra]+$fetch2[extranoc];

                    if(($total/60)>$limiteExtrasOrd)
                    {
                        $extranoc -= $total - $limiteExtrasOrdMin;
                        $horas=$extranoc/60;
                        break;
                    }
                }
            }

            if($opc==6)
            {
                $consulta = "SELECT fecha, sum((case when concepto='extras' then minutos else 0 end)) as extra, sum((case when concepto='extrasnoc' then minutos else 0 end)) as extranoc  FROM `reloj_procesar` WHERE ficha='$ficha'   group by fecha order by fecha  ";
                $resultado = query($consulta, $conexion);
                $j = $i = $extranoc = $extra = $total = 0;
                while($fetch2 = fetch_array($resultado))
                {
                    $extra += $fetch2[extra];
                    $extranoc += $fetch2[extranoc]; 
                    $total += $fetch2[extra]+$fetch2[extranoc];

                    if(($total/60)>$limiteExtrasOrd)
                    {
                        if($i == 0)
                        {
                           $horasmin += $total - $limiteExtrasOrdMin;
                           $i = 1;
                        }
                        if($j == 1)
                            $horasmin += $fetch2[extranoc];
                        $j = 1;
                    }
                }
                $horas = $horasmin/60;
            }

            

            return number_format($horas,2,'.','');
        break;

        case "jimmy":
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
                $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='extrasnocnac' AND ficha='$ficha'";
            elseif($opc==13)
                $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='descextra1' AND ficha='$ficha'";
            $resultado = query($consulta, $conexion);
            $fetch = fetch_array($resultado);
            
            $horas = $fetch[minutos]/60;
            /*if($opc==2)
            {
                if($horas>$limiteExtrasOrd)
                {
                    $horas=$limiteExtrasOrd;
                }
            }*/

            if($opc==4)
            {
                $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='extras' AND ficha='$ficha'";
                $resultado = query($consulta, $conexion);
                $fetch = fetch_array($resultado);
            
                $horasExt = $fetch[minutos]/60;
                if($horasExt>$limiteExtrasOrd)
                {
                    $horasExt2=$horasExt-$limiteExtrasOrd;
                    $horas+=$horasExt2;
                }
            }

            if($opc==5)
            {
                $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='extras' AND ficha='$ficha'";
                $resultado = query($consulta, $conexion);
                $fetch = fetch_array($resultado);
            
                $horasExt = $fetch[minutos]/60;
                $horasTot = $horasExt+$horas;
                if($horasTot>$limiteExtrasOrd)
                {
                    $horas = $limiteExtrasOrd-$horasExt;
                    if($horas < 0)
                        $horas = 0;
                }
                else
                {
                    $horasExt2 = $limiteExtrasOrd - $horasExt;
                    if($horas >= $horasExt2)
                        $horas = $horasExt2;
                }
            }

            if($opc==6)
            {
                $consulta = "SELECT ifnull(SUM(minutos),0) AS minutos FROM reloj_procesar WHERE concepto='extras' AND ficha='$ficha'";
                $resultado = query($consulta, $conexion);
                $fetch = fetch_array($resultado);

                $consulta = "SELECT ifnull(SUM(minutos),0) AS minutos FROM reloj_procesar WHERE concepto='extrasnoc' AND ficha='$ficha'";
                $resultado = query($consulta, $conexion);
                $fetch2 = fetch_array($resultado);
            
                $horasExt = $fetch[minutos]/60;
                $horasExt2 = $fetch2[minutos]/60;
                $totalHoras = $horasExt + $horasExt2;
                /*if($totalHoras > $limiteExtrasOrd)
                {
                    $totalHoras=$totalHoras-$limiteExtrasOrd;
                    $horas+=$totalHoras;
                }*/
                if($horasExt > $limiteExtrasOrd)
                {
                   $horas += $horasExt2;
                }
                elseif(($horasExt < $limiteExtrasOrd) && ($totalHoras > $limiteExtrasOrd))
                {
                    $horas += $horasExt2 - ($limiteExtrasOrd - $horasExt);
                }
            }

            return $horas;
        break;

        case "electron":
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
                $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='extrasnocnac' AND ficha='$ficha'";
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
            
            $resultado = query($consulta, $conexion);
            $fetch = fetch_array($resultado);
            
            $horas = $fetch[minutos]/60;

            return $horas;
        break;

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
                $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='extrasnocnac' AND ficha='$ficha'";
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
            $resultado = query($consulta, $conexion);
            $fetch = fetch_array($resultado);
            
            $horas = $fetch[minutos]/60;

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
                $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='extrasnocnac' AND ficha='$ficha'";
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
            
            $resultado = query($consulta, $conexion);
            $fetch = fetch_array($resultado);
            
            $horas = $fetch[minutos]/60;

            return $horas;
        break;
    }
}

function horastrabajadas_panama($ficha, $fechaini, $fechafin, $opc)
{
    global $conexion;
    //$conexion = conexion();
    $select = "select capital from nomempresa";
    $result = query($select,$conexion);
    $filaemp = fetch_array($result);
    $empresa = $filaemp['capital'];
    switch ($empresa)
    {
        case "zkteco":
            $limiteExtrasOrd=18;
            if($opc=="1")
                $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='1' AND ficha='$ficha'";
            elseif($opc=="1.25")
                $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='1.25' AND ficha='$ficha'";
            elseif($opc=="1.5")
                $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='1.5' AND ficha='$ficha'";
            elseif($opc=="1.75")
                $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='1.75' AND ficha='$ficha'";
            elseif($opc=="2.5")
                $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='2.5' AND ficha='$ficha'";
            elseif($opc=="1.5")
                $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='1.5' AND ficha='$ficha'";
            elseif($opc=="1.25x1.75")
                $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='1.25x1.75' AND ficha='$ficha'";
            elseif($opc=="1.25x1.50")
                $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='1.25x1.50' AND ficha='$ficha'";
            elseif($opc=="1.25x1.50x1.75")
                $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='1.25x1.50x1.75' AND ficha='$ficha'";
            elseif($opc=="1.25x2.50")
                $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='1.25x2.50' AND ficha='$ficha'";
            elseif($opc=="1.25x1.75x2.50")
                $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='1.25x1.75x2.50' AND ficha='$ficha'";
            elseif($opc=="1.50x1.75")
                $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='1.50x1.75' AND ficha='$ficha'";
            elseif($opc=="1.50x1.50")
                $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='1.50x1.50' AND ficha='$ficha'";
            elseif($opc=="1.50x1.50x1.75")
                $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='1.50x1.50x1.75' AND ficha='$ficha'";
            elseif($opc=="1.50x2.50")
                $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='1.50x2.50' AND ficha='$ficha'";
            elseif($opc=="1.50x1.75x2.50")
                $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='1.50x1.75x2.50' AND ficha='$ficha'";
            elseif($opc=="1.75x1.75")
                $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='1.75x1.75' AND ficha='$ficha'";
            elseif($opc=="1.50x1.75")
                $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='1.50x1.75' AND ficha='$ficha'";
            elseif($opc=="1.50x1.75x1.75")
                $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='1.50x1.75x1.75' AND ficha='$ficha'";
            elseif($opc=="1.75x2.50")
                $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='1.75x2.50' AND ficha='$ficha'";
            elseif($opc=="1.75x1.75x2.50")
                $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='1.75x1.75x2.50' AND ficha='$ficha'";
            elseif($opc=="domingo")
                $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='domingo' AND ficha='$ficha'";
            elseif($opc=="nacional")
                $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='nacional' AND ficha='$ficha'";
            
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
            $resultado = query($consulta, $conexion);
            $fetch = fetch_array($resultado);
            
            $horas = $fetch[minutos]/60;

            return $horas;
        break;
    }
}

function ausenciahoras($ficha, $fechaini, $fechafin, $opc, $tiponomina)
{
    global $conexion;
    //$conexion = conexion();
    $select = "select capital from nomempresa";
    $result = query($select,$conexion);
    $filaemp = fetch_array($result);
    $empresa = $filaemp['capital'];
    switch ($empresa)
    {
        case "picadilly":
            
            $query = "SELECT ifnull(count(dia_fiesta),0) as dias from nomcalendarios_tiposnomina where dia_fiesta='0' AND fecha BETWEEN '$fechaini' and '$fechafin' and cod_tiponomina = '$tiponomina'";
            $result = query($query,$conexion);
            $fetch = fetch_array($result);
            $laborables = $fetch[dias];

            
            $query = "SELECT ifnull(count(fecha),0) as dias from reloj_procesar where fecha BETWEEN '$fechaini' and '$fechafin' GROUP BY FECHA";
            $result = query($query,$conexion);
            $laborados = num_rows($result);

            $total = ($laborables - $laborados) * 8;
            
            return number_format($horas,2,'.','');
        break;
    }
}

//FUNCION QUE DEVUELVE EL COSTO POR HORA SEGUN LA LEGISLACION PANAMEÑA DEL MONTO MENSUAL QUE SE LE ENVIE
// EJEMPLO $SUELDO SERIA ASI valorhora($sueldo)=RESULTADO  LUIS MONTERO
function valorhora($valor) {

    $resultado = ((($valor*12)/52)/48);
    return ($resultado);
}
//FUNCION QUE DEVUELVE EL ACUMULADO DE LAS ASIGNACIONES DE SUELDO INTEGRAL (SI)
// EJEMPLO SERIA ASI acumuladomes_si($FICHA,$FECHANOMINA,11)  LUIS MONTERO
function acumuladomes_si($ficha, $fecha_inicio, $meses) {
    include 'globales.php';
    global $conexion;
    //$conexion = conexion();
    $anioctual = substr($fecha_inicio, 0, 4);
    $mesactual = substr($fecha_inicio, 5, 2);
   
    for ($i = 1; $i <= $meses; $i++) {
    
    if ($mesactual == 0) {
            $mesactual = 12;
        $anioctual -=1;
        } 
        $query = "SELECT SUM(monto) AS suma FROM nom_movimientos_nomina
 INNER JOIN nomconceptos_acumulados ON (nom_movimientos_nomina.codcon = nomconceptos_acumulados.codcon) WHERE (nomconceptos_acumulados.cod_tac = 'SI') AND (nom_movimientos_nomina.ficha = $ficha) AND nom_movimientos_nomina.mes=$mesactual AND nom_movimientos_nomina.anio=$anioctual AND nom_movimientos_nomina.tipnom=$_SESSION[codigo_nomina]";
//echo $query;
       $result = query($query, $conexion);
       $fila = fetch_array($result);
       $suma += $fila['suma'];

       $mesactual -=1;
    }
    //echo $suma;
    return $suma;
}

//FUNCION QUE DEVUELVE EL ACUMULADO DE LAS ASIGNACIONES DE SUELDO INTEGRAL (SI)
// EJEMPLO SERIA ASI acumuladofecha_si($FICHA,fecha_inicio,fecha_fin)  LUIS MONTERO
function acumuladofecha_si($ficha, $fecha_inicio, $fecha_fin) {
    include 'globales.php';
    $fecha_inicio;
    $fecha_fin;
    
    global $conexion;
    //$conexion = conexion();

        $query = "SELECT SUM(monto) AS suma FROM `nom_movimientos_nomina`
  INNER JOIN `nom_nominas_pago` ON (`nom_movimientos_nomina`.`codnom` = `nom_nominas_pago`.`codnom`)
  INNER JOIN `nomconceptos_acumulados` ON (`nom_movimientos_nomina`.`codcon` = `nomconceptos_acumulados`.`codcon`)
 WHERE (nomconceptos_acumulados.cod_tac = 'SI') AND (nom_movimientos_nomina.ficha = $ficha) and (nom_nominas_pago.periodo_ini>=$fecha_inicio and nom_nominas_pago.periodo_fin>=$fecha_fin) AND nom_movimientos_nomina.tipnom=$_SESSION[codigo_nomina]";
//echo $query;
       $result = query($query, $conexion);
       $fila = fetch_array($result);
       $suma += $fila['suma'];
       $mesactual -=1;
    return $suma;
}


function anio($fecha) {
    return substr($fecha, 0, 4);;
}


function ausenciamarcacion($ficha)
{
    global $conexion;
    //$conexion = conexion();
    $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='inasistencia' AND ficha='$ficha'";
    $resultado = query($consulta, $conexion);
    $fetch = fetch_array($resultado);
    $horas = $fetch[minutos];
    return $horas;
}

function periodo($fecha, $FRECUENCIA)
{
//echo   $FRECUENCIA; 
    $mes = substr($fecha, 5, 2);
    $dia = substr($fecha, 8, 2);
    
    if(($frecuencia == 2 or $frecuencia == 7 or $frecuencia == 10 or $frecuencia == 16 or $frecuencia == 12 or $frecuencia == 8) and $dia < 16)
    {

        return $periodo = $mes*2-1;
    }
    elseif(($frecuencia == 3 or $frecuencia == 10 or $FRECUENCIA == 17 or $FRECUENCIA == 15  or $frecuencia == 13 or $frecuencia == 14 or $frecuencia == 8) and $dia >= 15)
    {
    	
        return $periodo = $mes*2;
    }

}
function periodoactualquincenal($ficha, $fecha)
{
    include 'globales.php';
    global $conexion;
    $selectra = new bd($_SESSION['bd']);

    $anioplanilla = substr($fecha, 0, 4);
    $consulta = "SELECT fecing FROM nompersonal WHERE ficha='".$FICHA."'"; 
    $resultado = query($consulta, $conexion);
    $fetch = fetch_array($resultado);
    $fechaingreso = $fetch['fecing'];
    $anioingreso = substr($fechaingreso, 0, 4);
    $mesingreso = substr($fechaingreso, 5, 2);
    $diaingreso = substr($fechaingreso, 8, 2);

    if ($anioingreso == $anioplanilla) {

            $mes = $mesingreso*2;
            
            if ($diaingreso < 16) {
        
                return $periodo = $mes-2;
            
            } else {

                return $periodo = $mes-1;
            }    
        
    } else {

        return 0;
    }  

}

function periodoactualbisemanal($ficha, $fecha)
{
    include 'globales.php';
    global $conexion;
    $selectra = new bd($_SESSION['bd']);

    $consulta = "SELECT inicio_periodo FROM nompersonal WHERE ficha='".$FICHA."'"; 
    $resultado = query($consulta, $conexion);
    $fetch = fetch_array($resultado);
    $fechaingreso = $fetch['inicio_periodo'];

    $anioingreso = substr($fechaingreso, 0, 4);
    $anioplanilla = substr($fecha, 0, 4);

    if ($anioingreso == $anioplanilla) {

        $consulta1 = "SELECT numBisemana FROM bisemanas WHERE '$fechaingreso' >= fechaInicio and '$fechaingreso' <= fechaFin";
        $resultado1 = query($consulta1, $conexion);
        $fetch1 = fetch_array($resultado1);
        $numerobi1 = $fetch1['numBisemana']; 

        return $numerobi = $numerobi1 - 1;
    
    } else {

        return 0;
    }
       
}

function conceptosanual($codcon, $fecha)
{
    include 'globales.php';
    $selectra = new bd($_SESSION['bd']);

    $anio = substr($fecha, 0, 4);
    $consulta = "select sum(monto) as monto from nom_movimientos_nomina where ficha='".$FICHA."' and codcon in ('$codcon') and anio='$anio' and codnom<>'$CODNOM'"; 
    $resultado = $selectra->query($consulta);
    $fila = $resultado->fetch_assoc();
    if ($resultado->num_rows <= 0) {
        return 0;
    } else {
        return $fila['monto'];
    }
}

function acumuladossalarios($ficha, $fecha)
{
    include 'globales.php';
    $selectra = new bd($_SESSION['bd']);

    $anio = substr($fecha, 0, 4);

    $consulta = "select sum(salario_bruto) as monto "
            . "from salarios_acumulados "
            . "where ficha='".$FICHA."' and year(fecha_pago)='$anio'"; 
    $resultado = $selectra->query($consulta);
    $fila = $resultado->fetch_assoc();
    if ($resultado->num_rows <= 0) {
        return 0;
    } else {
        return $fila['monto'];
    }
}

function acumuladosalxiii($ficha, $fecha_inicio, $fecha_fin) 
{
    include 'globales.php';
    $selectra = new bd($_SESSION['bd']);

    $consulta = "select sum(salario_bruto) as monto "
            . "from salarios_acumulados "
            . "where ficha='".$FICHA."' and (fecha_pago>='$fecha_inicio' and fecha_pago<='$fecha_fin')"; 
 
    $resultado = $selectra->query($consulta);
    $fila = $resultado->fetch_assoc();
    if ($resultado->num_rows <= 0) {
        return 0;
    } else {
        return $fila['monto'];
    }
}
function acumuladogrxiii($ficha, $fecha_inicio, $fecha_fin) 
{
    include 'globales.php';
    $selectra = new bd($_SESSION['bd']);

    $consulta = "SELECT sum(gtorep) as monto "
            . "FROM salarios_acumulados "
            . "WHERE ficha='".$FICHA."' and (fecha_pago>='$fecha_inicio' and fecha_pago<='$fecha_fin')"; 
 
    $resultado = $selectra->query($consulta);
    $fila = $resultado->fetch_assoc();
    if ($resultado->num_rows <= 0) {
        return 0;
    } else {
        return $fila['monto'];
    }
}

function acumuladovacxiii($ficha, $fecha_inicio, $fecha_fin) 
{
    include 'globales.php';
    $selectra = new bd($_SESSION['bd']);

    $consulta = "select sum(vacac) as monto from salarios_acumulados "
            . "where ficha='".$FICHA."' and (fecha_pago>='$fecha_inicio' and fecha_pago<='$fecha_fin')"; 

    $resultado = $selectra->query($consulta);
    $fila = $resultado->fetch_assoc();
    if ($resultado->num_rows <= 0) {
        return 0;
    } else {
        return $fila['monto'];
    }
}

function acumuladootrosxiii($ficha, $fecha_inicio, $fecha_fin) 
{
    include 'globales.php';
    $selectra = new bd($_SESSION['bd']);

    $consulta = "SELECT sum(otros_ing) as monto "
            . "FROM salarios_acumulados "
            . "WHERE ficha='".$FICHA."' and (fecha_pago>='$fecha_inicio' and fecha_pago<='$fecha_fin')"; 

    $resultado = $selectra->query($consulta);
    $fila = $resultado->fetch_assoc();
    if ($resultado->num_rows <= 0) {
        return 0;
    } else {
        return $fila['monto'];
    }
}
function acumuladoviaticosxiii($ficha, $fecha_inicio, $fecha_fin) 
{
    include 'globales.php';
    $selectra = new bd($_SESSION['bd']);

    $consulta = "SELECT sum(bono) as monto "
            . "FROM salarios_acumulados "
            . "WHERE ficha='".$FICHA."' and (fecha_pago>='$fecha_inicio' and fecha_pago<='$fecha_fin')"; 

    $resultado = $selectra->query($consulta);
    $fila = $resultado->fetch_assoc();
    if ($resultado->num_rows <= 0) {
        return 0;
    } else {
        return $fila['monto'];
    }
}

function acumuladosxiiipagado($ficha, $fecha_inicio, $fecha_fin) 
{
    include 'globales.php';
    $selectra = new bd($_SESSION['bd']);

    $consulta = "SELECT sum(xiii) as monto "
            . "FROM salarios_acumulados "
            . "WHERE ficha='".$FICHA."' and (fecha_pago>='$fecha_inicio' and fecha_pago<='$fecha_fin')"; 
 
    $resultado = $selectra->query($consulta);
    $fila = $resultado->fetch_assoc();
    if ($resultado->num_rows <= 0) {
        return 0;
    } else {
        return $fila['monto'];
    }
}



function acumuladosalvac11($ficha, $fecha_inicio) 
{
    include 'globales.php';
    $selectra = new bd($_SESSION['bd']);

    $fechames11 = date('Y-m-d', strtotime('-11 month', strtotime ( $fecha_inicio ))) ; // resta 11 mes   

    $consulta = "SELECT sum(salario_bruto) as monto "
            . "FROM salarios_acumulados "
            . "WHERE ficha='".$FICHA."' and (fecha_pago>='$fechames11' and fecha_pago<='$fecha_inicio')"; 

    $resultado = $selectra->query($consulta);
    $fila = $resultado->fetch_assoc();
    if ($resultado->num_rows <= 0) {
        return 0;
    } else {
        return $fila['monto'];
    }
}

function acumuladootrosvac11($ficha, $fecha_inicio) 
{
    include 'globales.php';
    $selectra = new bd($_SESSION['bd']);

    $fechames11 = date('Y-m-d', strtotime('-11 month', strtotime ( $fecha_inicio ))) ; // resta 11 mes   
    
    
    
//    $consulta = "SELECT (sum(otros_ing) as monto "
//            . "FROM salarios_acumulados "
//            . "WHERE ficha='".$FICHA."' and (fecha_pago>='$fechames11' and fecha_pago<='$fecha_inicio')"; 
    
    $consulta = "SELECT (SUM(bono)+SUM(otros_ing)+SUM(prima)) as monto "
            . "FROM salarios_acumulados "
            . "WHERE ficha='".$FICHA."' and (fecha_pago>='$fechames11' and fecha_pago<='$fecha_inicio')"; 
    
    $resultado = $selectra->query($consulta);
    $fila = $resultado->fetch_assoc();
    if ($resultado->num_rows <= 0) {
        return 0;
    } else {
        return $fila['monto'];
    }
} 

function acumuladogtorepvac11($ficha, $fecha_inicio) 
{
    include 'globales.php';
    $selectra = new bd($_SESSION['bd']);

    $fechames11 = date('Y-m-d', strtotime('-11 month', strtotime ( $fecha_inicio ))) ; // resta 11 mes   
    
    
    
//    $consulta = "SELECT (sum(otros_ing) as monto "
//            . "FROM salarios_acumulados "
//            . "WHERE ficha='".$FICHA."' and (fecha_pago>='$fechames11' and fecha_pago<='$fecha_inicio')"; 
    
    $consulta = "SELECT SUM(gtorep) as monto "
            . "FROM salarios_acumulados "
            . "WHERE ficha='".$FICHA."' and (fecha_pago>='$fechames11' and fecha_pago<='$fecha_inicio')"; 
    
    $resultado = $selectra->query($consulta);
    $fila = $resultado->fetch_assoc();
    if ($resultado->num_rows <= 0) {
        return 0;
    } else {
        return $fila['monto'];
    }
} 

function acumuladovacvac11($ficha, $fecha_inicio) 
{
    include 'globales.php';
    $selectra = new bd($_SESSION['bd']);

    $fechames11 = date('Y-m-d', strtotime('-11 month', strtotime ( $fecha_inicio ))) ; // resta 11 mes   

    $consulta = "SELECT sum(vacac) as monto "
            . "FROM salarios_acumulados "
            . "WHERE ficha='".$FICHA."' and (fecha_pago>='$fechames11' and fecha_pago<='$fecha_inicio')"; 

    $resultado = $selectra->query($consulta);
    $fila = $resultado->fetch_assoc();
    if ($resultado->num_rows <= 0) {
        return 0;
    } else {
        return $fila['monto'];
    }
} 

function acumuladobonovac11($ficha, $fecha_inicio) 
{
    include 'globales.php';
    $selectra = new bd($_SESSION['bd']);

    $fechames11 = date('Y-m-d', strtotime('-11 month', strtotime ( $fecha_inicio ))) ; // resta 11 mes   

    $consulta = "SELECT sum(bono) as monto "
            . "FROM salarios_acumulados "
            . "WHERE ficha='".$FICHA."' and (fecha_pago>='$fechames11' and fecha_pago<='$fecha_inicio')"; 

    $resultado = $selectra->query($consulta);
    $fila = $resultado->fetch_assoc();
    if ($resultado->num_rows <= 0) {
        return 0;
    } else {
        return $fila['monto'];
    }
}  
 

function acumuladosislr($ficha, $fecha)
{
    include 'globales.php';
    $selectra = new bd($_SESSION['bd']);

    $anio = substr($fecha, 0, 4);

    $consulta = "SELECT sum(islr) as monto "
            . "FROM salarios_acumulados "
            . "WHERE ficha='".$FICHA."' and year(fecha_pago)='$anio'"; 
    $resultado = $selectra->query($consulta);
    $fila = $resultado->fetch_assoc();
    if ($resultado->num_rows <= 0) {
        return 0;
    } else {
        return $fila['monto'];
    }
}

function acumuladosvacac($ficha, $fecha)
{
    include 'globales.php';
    $selectra = new bd($_SESSION['bd']);

    $anio = substr($fecha, 0, 4);

    $consulta = "SELECT sum(vacac) as monto "
            . "FROM salarios_acumulados "
            . "WHERE ficha='".$FICHA."' and year(fecha_pago)='$anio'"; 
    $resultado = $selectra->query($consulta);
    $fila = $resultado->fetch_assoc();
    if ($resultado->num_rows <= 0) {
        return 0;
    } else {
        return $fila['monto'];
    }
}

function acumuladosxiii($ficha, $fecha)
{
    include 'globales.php';
    $selectra = new bd($_SESSION['bd']);

    $anio = substr($fecha, 0, 4);

    $consulta = "SELECT sum(xiii) as monto "
            . "FROM salarios_acumulados "
            . "WHERE ficha='".$FICHA."' and year(fecha_pago)='$anio'"; 
    $resultado = $selectra->query($consulta);
    $fila = $resultado->fetch_assoc();
    if ($resultado->num_rows <= 0) {
        return 0;
    } else {
        return $fila['monto'];
    }
}

function acumuladosgtorep($ficha, $fecha)
{
    include 'globales.php';
    $selectra = new bd($_SESSION['bd']);

    $anio = substr($fecha, 0, 4);

    $consulta = "SELECT sum(gtorep) as monto "
            . "FROM salarios_acumulados "
            . "WHERE ficha='".$FICHA."' and year(fecha_pago)='$anio'"; 
    $resultado = $selectra->query($consulta);
    $fila = $resultado->fetch_assoc();
    if ($resultado->num_rows <= 0) {
        return 0;
    } else {
        return $fila['monto'];
    }
}

function acumuladosxiii_gtorep($ficha, $fecha)
{
    include 'globales.php';
    $selectra = new bd($_SESSION['bd']);

    $anio = substr($fecha, 0, 4);

    $consulta = "SELECT sum(xiii_gtorep) as monto "
            . "FROM salarios_acumulados "
            . "WHERE ficha='".$FICHA."' and year(fecha_pago)='$anio'"; 
    $resultado = $selectra->query($consulta);
    $fila = $resultado->fetch_assoc();
    if ($resultado->num_rows <= 0) {
        return 0;
    } else {
        return $fila['monto'];
    }
}

function acumuladosliquida($ficha, $fecha)
{
    include 'globales.php';
    $selectra = new bd($_SESSION['bd']);

    $anio = substr($fecha, 0, 4);

    $consulta = "SELECT sum(liquida) as monto "
            . "FROM salarios_acumulados "
            . "WHERE ficha='".$FICHA."' and year(fecha_pago)='$anio'"; 
    $resultado = $selectra->query($consulta);
    $fila = $resultado->fetch_assoc();
    if ($resultado->num_rows <= 0) {
        return 0;
    } else {
        return $fila['monto'];
    }
}

function acumuladosbono($ficha, $fecha)
{
    include 'globales.php';
    $selectra = new bd($_SESSION['bd']);

    $anio = substr($fecha, 0, 4);

    $consulta = "SELECT sum(bono) as monto "
            . "FROM salarios_acumulados "
            . "WHERE ficha='".$FICHA."' and year(fecha_pago)='$anio'"; 
    $resultado = $selectra->query($consulta);
    $fila = $resultado->fetch_assoc();
    if ($resultado->num_rows <= 0) {
        return 0;
    } else {
        return $fila['monto'];
    }
}

function acumuladosotros_ing($ficha, $fecha)
{
    include 'globales.php';
    $selectra = new bd($_SESSION['bd']);

    $anio = substr($fecha, 0, 4);

    $consulta = "SELECT sum(otros_ing) as monto "
            . "FROM salarios_acumulados "
            . "WHERE ficha='".$FICHA."' and year(fecha_pago)='$anio'"; 
    $resultado = $selectra->query($consulta);
    $fila = $resultado->fetch_assoc();
    if ($resultado->num_rows <= 0) {
        return 0;
    } else {
        return $fila['monto'];
    }
}

function acumuladoss_s($ficha, $fecha)
{
    include 'globales.php';
    $selectra = new bd($_SESSION['bd']);

    $anio = substr($fecha, 0, 4);

    $consulta = "SELECT sum(s_s) as monto "
            . "FROM salarios_acumulados "
            . "WHERE ficha='".$FICHA."' and year(fecha_pago)='$anio'"; 
    $resultado = $selectra->query($consulta);
    $fila = $resultado->fetch_assoc();
    if ($resultado->num_rows <= 0) {
        return 0;
    } else {
        return $fila['monto'];
    }
}

function acumuladoss_e($ficha, $fecha)
{
    include 'globales.php';
    $selectra = new bd($_SESSION['bd']);

    $anio = substr($fecha, 0, 4);

    $consulta = "SELECT sum(s_e) as monto "
            . "FROM salarios_acumulados "
            . "WHERE ficha='".$FICHA."' and year(fecha_pago)='$anio'"; 
    $resultado = $selectra->query($consulta);
    $fila = $resultado->fetch_assoc();
    if ($resultado->num_rows <= 0) {
        return 0;
    } else {
        return $fila['monto'];
    }
}

function acumuladosislr_gr($ficha, $fecha)
{
    include 'globales.php';
    $selectra = new bd($_SESSION['bd']);

    $anio = substr($fecha, 0, 4);

    $consulta = "SELECT sum(islr_gr) as monto "
            . "FROM salarios_acumulados "
            . "WHERE ficha='".$FICHA."' and year(fecha_pago)='$anio'"; 
    $resultado = $selectra->query($consulta);
    $fila = $resultado->fetch_assoc();
    if ($resultado->num_rows <= 0) {
        return 0;
    } else {
        return $fila['monto'];
    }
}

function acumuladosNeto($ficha, $fecha)
{
    include 'globales.php';
    $selectra = new bd($_SESSION['bd']);

    $anio = substr($fecha, 0, 4);

    $consulta = "SELECT sum(Neto) as monto "
            . "FROM salarios_acumulados "
            . "WHERE ficha='".$FICHA."' and year(fecha_pago)='$anio'"; 
    $resultado = $selectra->query($consulta);
    $fila = $resultado->fetch_assoc();
    if ($resultado->num_rows <= 0) {
        return 0;
    } else {
        return $fila['monto'];
    }
}

function vac_proporcionales_acum($ficha, $fechaini, $fechafin)
{
    include 'globales.php';
    $selectra = new bd($_SESSION['bd']);

    $consulta = "SELECT (SUM(salario_bruto)+SUM(bono)+SUM(otros_ing)) as monto "
            . "FROM salarios_acumulados "
            . "WHERE ficha='".$FICHA."' and fecha_pago >='$fechaini' and fecha_pago <='$fechafin'"; 
    $resultado = $selectra->query($consulta);
    $fila = $resultado->fetch_assoc();
    if ($resultado->num_rows <= 0) {
        return 0;
    } else {
        return $fila['monto'];
    }
}

function salario_brutoacum($ficha, $fechaini, $fechafin)
{
    include 'globales.php';
    $selectra = new bd($_SESSION['bd']);

    $consulta = "SELECT (SUM(salario_bruto)) as monto "
            . "FROM salarios_acumulados "
            . "WHERE ficha='".$FICHA."' and fecha_pago >='$fechaini' and fecha_pago <='$fechafin'"; 
    $resultado = $selectra->query($consulta);
    $fila = $resultado->fetch_assoc();
    if ($resultado->num_rows <= 0) {
        return 0;
    } else {
        return $fila['monto'];
    }
}

function fondo_asist($ficha, $fechaini, $fechafin)
{
    include 'globales.php';
    $selectra = new bd($_SESSION['bd']);

    $consulta = "select (SUM(minutos)) as monto from reloj_procesar where ficha='".$FICHA."' and fecha >='$fechaini' and fecha <='$fechafin' and minutos >= 180 and concepto = 'tardanza'"; 
    $resultado = $selectra->query($consulta);
    $fila = $resultado->fetch_assoc();
    if ($resultado->num_rows <= 0) {
        return 0;
    } else {
        return $fila['monto'];
    }
}

function acumcomtipanual($tipo, $fecha_inicio) {
    include 'globales.php';
    global $conexion;
    //$conexion = conexion();
   
   $dia=date("d", strtotime( $fecha_inicio));
   $mes=date("m", strtotime( $fecha_inicio));
   $anio=date("Y", strtotime( $fecha_inicio));

  $consulta = "SELECT sum(`montototal`) as monto 
                    FROM  `nomacumulados_det` 
                    WHERE  `ficha` ='$FICHA' 
                    AND  `cod_tac` LIKE  '$tipo' 
                    AND `anioa`  ='$anio'";
    

  
    $resultado = query($consulta, $conexion);
    $fila = fetch_array($resultado);
    $suma = $fila['monto'];
    return $suma;
}

function acumsalseismesessalacum($tipo, $fecha_inicio, $fecha_fin) {
    include 'globales.php';

    $selectra = new bd($_SESSION['bd']);

    $fechames11 = date('Y-m-d', strtotime('-6 month')) ; // resta 6 mes

    $consulta = "SELECT (SUM(salario_bruto)+SUM(vacac)) as monto "
            . "FROM salarios_acumulados "
            . "WHERE ficha='".$FICHA."' and fecha_pago >='$fechames11' and fecha_pago <='$fecha_fin'"; 

    $resultado = $selectra->query($consulta);
    $fila = $resultado->fetch_assoc();
    if ($resultado->num_rows <= 0) {
        return 0;
    } else {
        return $fila['monto'];
    }

}

function acumsalseismeses($tipo, $fecha_inicio, $fecha_fin) {
    include 'globales.php';
    global $conexion;
    //$conexion = conexion();
     
    $fechames11 = date('Y-m-d', strtotime('-6 month')) ; // resta 6 mes
    
    //$fechahoy=date('Y-m-d');
    //$consulta = "SELECT SUM(monto) AS monto FROM nom_movimientos_nomina WHERE mes=$mes AND anio=$anio AND tipcon='A' AND ficha=$ficha AND tipnom=$_SESSION[codigo_nomina] AND (codcon<>520 OR codcon<>1402 OR codcon<>1400 OR codcon<>1463)";
    //$consulta = "SELECT sum(monto) as monto FROM nom_nominas_pago INNER JOIN nom_movimientos_nomina ON nom_nominas_pago.codnom = nom_movimientos_nomina.codnom AND nom_movimientos_nomina.tipnom = nom_nominas_pago.codtip INNER JOIN nomconceptos_acumulados ON nom_movimientos_nomina.codcon = nomconceptos_acumulados.codcon WHERE ficha='$FICHA' AND cod_tac='$tipo' and periodo_ini>='$fecha_inicio' and periodo_fin<='$fecha_fin' and status='C'";
    $consulta = "SELECT ifnull(sum(monto) ,0) as monto FROM nom_nominas_pago INNER JOIN nom_movimientos_nomina ON nom_nominas_pago.codnom = nom_movimientos_nomina.codnom AND nom_movimientos_nomina.tipnom = nom_nominas_pago.codtip INNER JOIN nomconceptos_acumulados ON nom_movimientos_nomina.codcon = nomconceptos_acumulados.codcon WHERE ficha='$FICHA' AND cod_tac='$tipo' and periodo_ini>='$fechames11' and periodo_fin<='$fecha_fin'" ;
     
    $resultado = query($consulta, $conexion);
    $fila = fetch_array($resultado);
    $suma = $fila['monto'];
    return $suma;
}
function salariointegralultimomes($tipo, $fecha_inicio, $fecha_fin) {
    include 'globales.php';
    global $conexion;
    //$conexion = conexion();
     
    $fechames11 = date('Y-m-d', strtotime('-1 month')) ; // resta 6 mes
    $fechahoy=date('Y-m-d');
    //$consulta = "SELECT SUM(monto) AS monto FROM nom_movimientos_nomina WHERE mes=$mes AND anio=$anio AND tipcon='A' AND ficha=$ficha AND tipnom=$_SESSION[codigo_nomina] AND (codcon<>520 OR codcon<>1402 OR codcon<>1400 OR codcon<>1463)";
    //$consulta = "SELECT sum(monto) as monto FROM nom_nominas_pago INNER JOIN nom_movimientos_nomina ON nom_nominas_pago.codnom = nom_movimientos_nomina.codnom AND nom_movimientos_nomina.tipnom = nom_nominas_pago.codtip INNER JOIN nomconceptos_acumulados ON nom_movimientos_nomina.codcon = nomconceptos_acumulados.codcon WHERE ficha='$FICHA' AND cod_tac='$tipo' and periodo_ini>='$fecha_inicio' and periodo_fin<='$fecha_fin' and status='C'";
    $consulta = "SELECT ifnull(sum(monto) ,0) as monto FROM nom_nominas_pago INNER JOIN nom_movimientos_nomina ON nom_nominas_pago.codnom = nom_movimientos_nomina.codnom AND nom_movimientos_nomina.tipnom = nom_nominas_pago.codtip INNER JOIN nomconceptos_acumulados ON nom_movimientos_nomina.codcon = nomconceptos_acumulados.codcon WHERE ficha='$FICHA' AND cod_tac='$tipo' and periodo_ini>='$fechames11' and periodo_fin<='$fecha_fin'" ;
     
    $resultado = query($consulta, $conexion);
    $fila = fetch_array($resultado);
    $suma = $fila['monto'];
    return $suma;
}
function vac_panama_dpendiente($ficha,$fecha_inicio,$fecha_fin)
{
    include 'globales.php';
    $selectra = new bd($_SESSION['bd']);

   $consulta = "SELECT SUM(dias_solic_ppagar) as suma FROM nom_progvacaciones WHERE tipooper='DV'  AND ficha='".$ficha."' AND estado='Pendiente' AND fechavac >= '$fecha_inicio' AND fechareivac <= '$fecha_fin' and marca = '1' ";

    $resultado = $selectra->query($consulta);
    $fila = $resultado->fetch_assoc();
    $suma = $fila['suma'];

    return $suma;
}
function acumcomvacpanama($ficha,$tipo, $fecha_fin) {
    include 'globales.php';
    $selectra = new bd($_SESSION['bd']);
    //$conexion = conexion();
     
    $fechames11 = date('Y-m-d', strtotime('-11 month', strtotime ( $fecha_fin ))) ; // resta 11 mes

     $consulta = "SELECT ifnull(sum(DISTINCT nmn.monto) ,0) as monto 
     FROM nom_nominas_pago as nnp 
     INNER JOIN nom_movimientos_nomina as nmn ON nnp.codnom = nmn.codnom AND nmn.tipnom = nnp.codtip 
     INNER JOIN nomconceptos_acumulados as nca ON nmn.codcon = nca.codcon 
     left join nom_progvacaciones  as npv on (npv.ficha =  nmn.ficha)
     WHERE nmn.ficha='".$ficha."' AND nca.cod_tac='$tipo' and nnp.periodo_ini>='$fechames11' and npv.fechareivac<='$fecha_fin'" ;
   

  
    $resultado = $selectra->query($consulta);
    $fila = $resultado->fetch_assoc();
    $suma = $fila['monto'];
    return $suma;
}

function acumcomvacconcepto($ficha, $concepto, $fecha_fin) {
    include 'globales.php';
    $selectra = new bd($_SESSION['bd']);
    //$conexion = conexion();
     
    $fechames11 = date('Y-m-d', strtotime('-11 month', strtotime ( $fecha_fin ))) ; // resta 11 mes

    $consulta = "SELECT ifnull(sum(nmn.monto) ,0) as monto 
    FROM nom_nominas_pago 
    INNER JOIN nom_movimientos_nomina as nmn ON nmn.codnom = nmn.codnom 
    AND nmn.tipnom = nom_nominas_pago.codtip  
    LEFT JOIN nompersonal as np and (np.ficha = nmn.ficha)
    WHERE ficha='".$FICHA."'AND codcon = '{$concepto}'and periodo_ini>='{$fechames11}' and periodo_fin<='{$fecha_fin}'" ;
   

  
    $resultado = $selectra->query($consulta);
    $fila = $resultado->fetch_assoc();
    $suma = $fila['monto'];
    return $suma;
}

function conceptosanualfull($codcon, $fecha)
{
    include 'globales.php';
    $selectra = new bd($_SESSION['bd']);
    $anio = substr($fecha, 0, 4);
    $mes = substr($fecha, 6, 7);

    $consulta = "select sum(monto) as monto from nom_movimientos_nomina where ficha='".$FICHA."' and codcon in ('$codcon') and anio='$anio' and codnom<>'$CODNOM' and mes between '01' AND '{$mes}'"; 
    $resultado = $selectra->query($consulta);
    $fila = $resultado->fetch_assoc();
    if ($resultado->num_rows <= 0) {
        return 0;
    } else {
        return $fila['monto'];
    }
}
function deduccion_vac_panama($ficha,$fecfinivac,$fecffinvac)
{
    include 'globales.php';

    $selectra = new bd($_SESSION['bd']);

    $consulta = "SELECT fechavac,fechareivac from nompersonal WHERE ficha = '{$ficha}' AND fechavac >= '{$fecfinivac}' AND fechareivac <= '{$fecffinvac}'"; 

    $resultado = $selectra->query($consulta);

    $fila = $resultado->fetch_assoc();

    if ($resultado->num_rows <= 0)
     {
        return 0;
    } 
    else 
    {

        $datetime1 = new DateTime($fila['fechavac']);
        $datetime2 = new DateTime($fila['fechareivac']);
        $interval = $datetime1->diff($datetime2);
        return $interval->format('%d');
    }
}
function acumuladoliq($tipo, $fecha_fin) {
    include 'globales.php';
    global $conexion;

    $SQL        = "SELECT fecing FROM nompersonal WHERE   ficha = '$FICHA'";
    $resultados = query($SQL, $conexion);
    $filas      = fetch_array($resultados);
    $fecing     = $filas['fecing'];
    
    $fecha5     = date('Y-m-d', strtotime('-60 month', strtotime ( $fecha_fin ))) ; // resta 11 mes
    
    $fecha1     = new DateTime($fechames11);
    $fecha2     = new DateTime($fecing);

    if ($fecha1<$fecha2) 
    {
        $consulta   = " SELECT ifnull(sum(monto) ,0) as monto 
        FROM nom_nominas_pago as nnp
        LEFT JOIN nom_movimientos_nomina as nmn ON (nnp.codnom = nmn.codnom AND nmn.tipnom = nnp.codtip )
        LEFT JOIN nomconceptos_acumulados as nca ON (nmn.codcon = nca.codcon )
        WHERE nmn.ficha='$FICHA' AND nca.cod_tac='$tipo' AND (nnp.periodo_ini>='$fecha5' AND nnp.periodo_fin<='$fecha_fin'  AND  
        (SELECT  fecing FROM nompersonal WHERE ficha = '$FICHA' ) <= nnp.periodo_fin ) " ;    
    }
    else
    {
        $consulta   = " SELECT ifnull(sum(monto) ,0) as monto 
        FROM nom_nominas_pago as nnp
        LEFT JOIN nom_movimientos_nomina as nmn ON (nnp.codnom = nmn.codnom AND nmn.tipnom = nnp.codtip )
        LEFT JOIN nomconceptos_acumulados as nca ON (nmn.codcon = nca.codcon )
        WHERE nmn.ficha='$FICHA' AND nca.cod_tac='$tipo' AND (nnp.periodo_fin<='$fecha_fin'  AND  
        (SELECT  fecing FROM nompersonal WHERE ficha = '$FICHA' ) <= nnp.periodo_fin ) " ;    
    }




    $resultado = query($consulta, $conexion);
    $fila      = fetch_array($resultado);
    $suma      = $fila['monto'];
    return $suma;
}
function acumuladoliq_cant($tipo, $fecha_fin) {
    include 'globales.php';
    global $conexion;

    $SQL        = "SELECT fecing FROM nompersonal WHERE   ficha = '$FICHA'";
    $resultados = query($SQL, $conexion);
    $filas      = fetch_array($resultados);
    $fecing     = $filas['fecing'];
    
    $fecha5     = date('Y-m-d', strtotime('-60 month', strtotime ( $fecha_fin ))) ; // resta 11 mes
    
    $fecha1     = new DateTime($fechames11);
    $fecha2     = new DateTime($fecing);

    if ($fecha1<$fecha2) 
    {
        return 60;
    }
    else
    {
        $cantidad_meses_anio = ($intervalo->format('%Y')*12);
        $cantidad_meses      = ($intervalo->format('%m'));
        $cantidad_meses_dia  = ($intervalo->format('%d')/30);
        return $cantidad_meses_anio+$cantidad_meses+$cantidad_meses_dia;
 
    }
}

function horas_ausencia_justificada($ficha, $fechaini, $fechafin, $opc)
{
    global $conexion;
    $consulta_personal = "SELECT cedula FROM nompersonal WHERE ficha = {$ficha}";
    $result_personal = query($consulta_personal, $conexion);
    $nompersonal = fetch_array($result_personal);
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
    $resultado   = query($consulta, $conexion);
    $tiempo       = fetch_array($resultado);
    $horas = ($tiempo[tiempo] <0 ? (-1)*$tiempo[tiempo] : $tiempo[tiempo]);


    return number_format($horas,2,'.','');
    
 }
 
 function bisemana($fechaini, $fechafin)
 {
    include 'globales.php';
    global $conexion;
    //$conexion = conexion();
           
    $consulta = "SELECT periodo FROM `nom_nominas_pago` "
                . " WHERE periodo_ini='$fechaini' AND periodo_fin='$fechafin'"
                . " AND tipnom=2";
    $resultado = query($consulta, $conexion);
    $fetch = fetch_array($resultado);
    return $fetch[periodo];
}

function bisemana1($fechaini)
{
    include 'globales.php';
    global $conexion;
    //$conexion = conexion();
           
     $consulta = "SELECT numBisemana FROM `bisemanas` "
                . " WHERE '$fechaini' >= fechaInicio and '$fechaini' <= fechaFin";
  
    $resultado = query($consulta, $conexion);
    $fetch = fetch_array($resultado);
    if ($resultado->num_rows <= 0) {
        return 0;
    } else {
      return $fetch[numBisemana];
    }  
}

function equivalencias_planilla($ficha, $fechaini, $fechafin, $opc)
{
    include 'globales.php';
    global $conexion;
    //$conexion = conexion();
    
    $consulta_periodo = "SELECT periodo, anio FROM `nom_nominas_pago` "
                . " WHERE periodo_ini='$fechaini' AND periodo_fin='$fechafin'"
                . " AND tipnom='" . $_SESSION['codigo_nomina'] . "'";
    $resultado_periodo = query($consulta_periodo, $conexion);
    $fetch_periodo = fetch_array($resultado_periodo);
    $periodo=$fetch_periodo[periodo];
    $anio=$fetch_periodo[anio];
    
    $consulta_personal = "SELECT suesal FROM `nompersonal` "
                . " WHERE ficha='$ficha' AND tipnom='" . $_SESSION['codigo_nomina'] . "'";
    $resultado_personal = query($consulta_personal, $conexion);
    $fetch_personal = fetch_array($resultado_personal);
    $valor=$fetch_personal[suesal];
    
    $consulta_resumen = "SELECT * FROM resumen_marcaciones_det "
                        . " LEFT JOIN resumen_marcaciones_cab ON (resumen_marcaciones_cab.id_rm = resumen_marcaciones_det.id_rm)"
                        . " WHERE resumen_marcaciones_det.ficha='$ficha' "
                        . " AND (resumen_marcaciones_cab.periodo='" .$periodo. "' AND resumen_marcaciones_cab.anio='" .$anio. "')";
    $resultado_resumen = query($consulta_resumen, $conexion);
    $fetch_resumen = fetch_array($resultado_resumen);
    $asistencias=$fetch_resumen[asistencias];   
    $rd=$fetch_resumen[rd];   
    $lt=$fetch_resumen[lt];   
    $domingos=$fetch_resumen[domingos];   
    $feriados=$fetch_resumen[feriados];   
    $libres=$fetch_resumen[libres];   
    $faltas=$fetch_resumen[faltas];   
    $amonestaciones=$fetch_resumen[amonestaciones];   
    $h_totales=$fetch_resumen[h_totales];   
    $h_extras=$fetch_resumen[h_extras];   
    $h_domingos=$fetch_resumen[h_domingos];   
    $h_feriados=$fetch_resumen[h_feriados];   
    $f_domingos=$fetch_resumen[f_domingos];   
    $f_feriados=$fetch_resumen[f_feriados];   
    $monto_pagar=$fetch_resumen[monto_pagar];   
    $t=$fetch_resumen[t];   
    $s=$fetch_resumen[s];   
    
    $consulta_equivalencia = "SELECT * FROM equivalencia_planilla "
                        . " WHERE (horas_trabajadas_valor='$valor' AND horas_trabajadas_horas='" .$h_totales. "' AND feriados_i_cant='" .$feriados. "')";
    $resultado_equivalencia = query($consulta_equivalencia, $conexion);
    $fetch_equivalencia = fetch_array($resultado_equivalencia);
    if($opc==1)
    {
        $valor=$fetch_equivalencia[horas_trabajadas_valor];
    }
    if($opc==2)
    {
        $valor=$fetch_equivalencia[horas_trabajadas_horas];
    }
    if($opc==3)
    {
        $valor=$fetch_equivalencia[horas_trabajadas_total];
    }
    if($opc==4)
    {
        $valor=$fetch_equivalencia[salario_basico_i_cant];
    }
    if($opc==5)
    {
        $valor=$fetch_equivalencia[salario_basico_i_total];
    }
    if($opc==6)
    {
        $valor=$fetch_equivalencia[recargo_domingo_i_cant];
    }
    if($opc==7)
    {
        $valor=$fetch_equivalencia[recargo_domingo_i_valor];
    }
    if($opc==8)
    {
        $valor=$fetch_equivalencia[recargo_domingo_i_total];
    }
    if($opc==9)
    {
        $valor=$fetch_equivalencia[hora_extra_diurna_i_cant];
    }
    if($opc==10)
    {
        $valor=$fetch_equivalencia[hora_extra_diurna_i_valor];
    }
    if($opc==11)
    {
        $valor=$fetch_equivalencia[hora_extra_diurna_i_total];
    }
    if($opc==12)
    {
        $valor=$fetch_equivalencia[hora_extra_mixta_i_cant];
    }
    if($opc==13)
    {
        $valor=$fetch_equivalencia[hora_extra_mixta_i_valor];
    }
    if($opc==14)
    {
        $valor=$fetch_equivalencia[hora_extra_mixta_i_total];
    }
    if($opc==15)
    {
        $valor=$fetch_equivalencia[hora_extra_nocturna_i_cant];
    }
    if($opc==16)
    {
        $valor=$fetch_equivalencia[hora_extra_nocturna_i_valor];
    }
    if($opc==16)
    {
        $valor=$fetch_equivalencia[hora_extra_nocturna_i_total];
    }
    if($opc==17)
    {
        $valor=$fetch_equivalencia[feriados_i_cant];
    }
    if($opc==18)
    {
        $valor=$fetch_equivalencia[feriados_i_valor];
    }
    if($opc==19)
    {
        $valor=$fetch_equivalencia[feriados_i_total];
    }
    if($opc==20)
    {
        $valor=$fetch_equivalencia[subtotal_asignaciones];
    }
    if($opc==21)
    {
        $valor=$fetch_equivalencia[diferencia_120_iii_cant];
    }
    if($opc==22)
    {
        $valor=$fetch_equivalencia[diferencia_120_iii_total];
    }
    if($opc==23)
    {
        $valor=$fetch_equivalencia[recargo_hora_extra_iii_cant];
    }
    if($opc==24)
    {
        $valor=$fetch_equivalencia[recargo_hora_extra_iii_valor];
    }
    if($opc==25)
    {
        $valor=$fetch_equivalencia[recargo_hora_extra_iii_total];
    }
    if($opc==26)
    {
        $valor=$fetch_equivalencia[recargo_feriado_iii_cant];
    }
    if($opc==27)
    {
        $valor=$fetch_equivalencia[recargo_feriado_iii_valor];
    }
    if($opc==28)
    {
        $valor=$fetch_equivalencia[recargo_feriado_iii_total];
    }
    if($opc==29)
    {
        $valor=$fetch_equivalencia[recargo_domingo_iii_cant];
    }
    if($opc==30)
    {
        $valor=$fetch_equivalencia[recargo_domingo_iii_valor];
    }
    if($opc==31)
    {
        $valor=$fetch_equivalencia[recargo_domingo_iii_total];
    }
    if($opc==32)
    {
        $valor=$fetch_equivalencia[neto_pago];
    }
   
    
    return number_format($valor,2,'.','');
    //break;
 }
 
 function resumen_marcaciones($ficha, $fechaini, $fechafin, $opc)
{
    include 'globales.php';
    global $conexion;
    //$conexion = conexion();
    
    $consulta_periodo = "SELECT periodo, anio FROM `nom_nominas_pago` "
                . " WHERE periodo_ini='$fechaini' AND periodo_fin='$fechafin'"
                . " AND tipnom='" . $_SESSION['codigo_nomina'] . "'";
    $resultado_periodo = query($consulta_periodo, $conexion);
    $fetch_periodo = fetch_array($resultado_periodo);
    $periodo=$fetch_periodo[periodo];
    $anio=$fetch_periodo[anio];
    
    $consulta_personal = "SELECT suesal FROM `nompersonal` "
                . " WHERE ficha='$ficha' AND tipnom='" . $_SESSION['codigo_nomina'] . "'";
    $resultado_personal = query($consulta_personal, $conexion);
    $fetch_personal = fetch_array($resultado_personal);
    $valor=$fetch_personal[suesal];
    
    $consulta_resumen = "SELECT * FROM resumen_marcaciones_det "
                        . " LEFT JOIN resumen_marcaciones_cab ON (resumen_marcaciones_cab.id_rm = resumen_marcaciones_det.id_rm)"
                        . " WHERE resumen_marcaciones_det.ficha='$ficha' "
                        . " AND (resumen_marcaciones_cab.periodo='" .$periodo. "' AND resumen_marcaciones_cab.anio='" .$anio. "')";
    $resultado_resumen = query($consulta_resumen, $conexion);
    $fetch_resumen = fetch_array($resultado_resumen);
    $asistencias=$fetch_resumen[asistencias];   
    $rd=$fetch_resumen[rd];   
    $lt=$fetch_resumen[lt];   
    $domingos=$fetch_resumen[domingos];   
    $feriados=$fetch_resumen[feriados];   
    $libres=$fetch_resumen[libres];   
    $faltas=$fetch_resumen[faltas];   
    $amonestaciones=$fetch_resumen[amonestaciones];   
    $h_totales=$fetch_resumen[h_totales];   
    $h_extras=$fetch_resumen[h_extras];   
    $h_domingos=$fetch_resumen[h_domingos];   
    $h_feriados=$fetch_resumen[h_feriados];   
    $f_domingos=$fetch_resumen[f_domingos];   
    $f_feriados=$fetch_resumen[f_feriados];   
    $monto_pagar=$fetch_resumen[monto_pagar];
    $numero_tc=$fetch_resumen[numero_tc];
    $pxc=$fetch_resumen[pxc];
    $valor_hora=$fetch_resumen[valor_hora];
    $valor_hora_extra=$fetch_resumen[valor_hora_extra];
    $valor_hora_dom=$fetch_resumen[valor_hora_dom];
    $valor_hora_fer=$fetch_resumen[valor_hora_fer];
    $t=$fetch_resumen[t];   
    $s=$fetch_resumen[s]; 
  
    if($opc==1)
    {
        $valor=$fetch_resumen[asistencias];
    }
    if($opc==2)
    {
        $valor=$fetch_resumen[rd];
    }
    if($opc==3)
    {
        $valor=$fetch_resumen[lt];
    }
    if($opc==4)
    {
        $valor=$fetch_resumen[domingos];
    }
    if($opc==5)
    {
        $valor=$fetch_resumen[feriados];
    }
    if($opc==6)
    {
        $valor=$fetch_resumen[libres];
    }
    if($opc==7)
    {
        $valor=$fetch_resumen[faltas];
    }
    if($opc==8)
    {
        $valor=$fetch_resumen[amonestaciones];
    }
    if($opc==9)
    {
        $valor=$fetch_resumen[h_totales];
    }
    if($opc==10)
    {
        $valor=$fetch_resumen[h_extras];
    }
    if($opc==11)
    {
        $valor=$fetch_resumen[h_domingos];   
    }
    if($opc==12)
    {
        $valor=$fetch_resumen[h_feriados];   
    }
    if($opc==13)
    {
        $valor=$fetch_resumen[f_domingos]; 
    }
    if($opc==14)
    {
        $valor=$fetch_resumen[f_feriados];   
    }
    if($opc==15)
    {
        $valor=$fetch_resumen[monto_pagar];
    }
    if($opc==16)
    {
        $valor=$fetch_resumen[numero_tc];
    }
    if($opc==16)
    {
        $valor=$fetch_resumen[pxc];
    }
    if($opc==17)
    {
        $valor=$fetch_resumen[valor_hora];
    }
    if($opc==18)
    {
        $valor=$fetch_resumen[valor_hora_extra];
    }
    if($opc==19)
    {
        $valor=$fetch_resumen[valor_hora_dom];
    }
    if($opc==20)
    {
        $valor=$fetch_resumen[valor_hora_fer];
    }
    if($opc==21)
    {
        $valor=$fetch_resumen[t];   
    }
    if($opc==22)
    {
        $valor=$s=$fetch_resumen[s]; 
    }
   
    
    return number_format($valor,2,'.','');
    //break;
 }
 
 function horas_ausencia_exp($ficha, $fechaini, $fechafin)
{
    global $conexion;
    $consulta_personal = "SELECT cedula FROM nompersonal WHERE ficha = {$ficha}";
    $result_personal = query($consulta_personal, $conexion);
    $nompersonal = fetch_array($result_personal);
    $cedula = $nompersonal['cedula'];
    
   $consulta="SELECT SUM(duracion) AS duracion "
        . "FROM expediente "
        . "WHERE cedula='$cedula' "
        . "AND expediente_tipo=69 "
        . "AND fecha BETWEEN '$fechaini' and '$fechafin'"        
        . "AND duracion >0";    
    
    
    $resultado   = query($consulta, $conexion);
    $tiempo       = fetch_array($resultado);
    $horas = ($tiempo[tiempo] <0 ? (-1)*$tiempo[tiempo] : $tiempo[tiempo]);


    return number_format($horas,2,'.','');
    
 }
 
  function horas_tardanza_exp($ficha, $fechaini, $fechafin)
{
    global $conexion;
    $consulta_personal = "SELECT cedula FROM nompersonal WHERE ficha = {$ficha}";
    $result_personal = query($consulta_personal, $conexion);
    $nompersonal = fetch_array($result_personal);
    $cedula = $nompersonal['cedula'];
    
    $consulta="SELECT SUM(duracion) AS duracion "
        . "FROM expediente "
        . "WHERE cedula='$cedula' "
        . "AND expediente_tipo=70 "
        . "AND fecha BETWEEN '$fechaini' and '$fechafin'"        
        . "AND duracion >0";    
    
    $resultado   = query($consulta, $conexion);
    $tiempo       = fetch_array($resultado);
    $horas = ($tiempo[tiempo] <0 ? (-1)*$tiempo[tiempo] : $tiempo[tiempo]);


    return number_format($horas,2,'.','');
    
 }

function diaperiodo($inicio, $final, $dia) {
    // 0  para domingo y 6 para sabado
    if ($final < $inicio)
        return 0;
    if (($final == '') || ($inicio == ''))
        return 0;
    $fecha_inicio = explode("-", $inicio);
    $fecha_fin = explode("-", $final);

    $cantidad = 0;
    while($fecha_inicio[2]<=$fecha_fin[2])
    {
        $fecha = $fecha_inicio[0]."-".$fecha_inicio[1]."-".$fecha_inicio[2];
        if(date('w', strtotime($fecha)) == $dia)
        {
            $cantidad += 1;
        }

        $fecha_inicio[2] += 1;
    }

    
    return $cantidad;
}

function marvin_horastrabajadas($ficha, $fechaini, $fechafin, $opc)
{
    global $conexion;
    if($opc==1)
        $consulta = "SELECT md.regular as horas "
                    . "FROM marvin_detalle as md "
                    . "LEFT JOIN marvin_encabezado as me ON (md.id_encabezado=me.id) "
                    . "WHERE me.fecha_inicio='$fechaini' AND me.fecha_fin='$fechafin' AND md.ficha='$ficha'";
    elseif($opc==2)
        $consulta = "SELECT md.dia_libre_nacional_150 as horas "
                    . "FROM marvin_detalle as md "
                    . "LEFT JOIN marvin_encabezado as me ON (md.id_encabezado=me.id) "
                    . "WHERE me.fecha_inicio='$fechaini' AND me.fecha_fin='$fechafin' AND md.ficha='$ficha'";
    elseif($opc==3)
        $consulta = "SELECT md.dia_feriado_250 as horas "
                    . "FROM marvin_detalle as md "
                    . "LEFT JOIN marvin_encabezado as me ON (md.id_encabezado=me.id) "
                    . "WHERE me.fecha_inicio='$fechaini' AND me.fecha_fin='$fechafin' AND md.ficha='$ficha'";
    elseif($opc==4)
        $consulta = "SELECT md.compensatorio_050 as horas "
                    . "FROM marvin_detalle as md "
                    . "LEFT JOIN marvin_encabezado as me ON (md.id_encabezado=me.id) "
                    . "WHERE me.fecha_inicio='$fechaini' AND me.fecha_fin='$fechafin' AND md.ficha='$ficha'";
    elseif($opc==5)
        $consulta = "SELECT md.extra_125 as horas "
                    . "FROM marvin_detalle as md "
                    . "LEFT JOIN marvin_encabezado as me ON (md.id_encabezado=me.id) "
                    . "WHERE me.fecha_inicio='$fechaini' AND me.fecha_fin='$fechafin' AND md.ficha='$ficha'";
    elseif($opc==6)
        $consulta = "SELECT md.extra_150 as horas "
                    . "FROM marvin_detalle as md "
                    . "LEFT JOIN marvin_encabezado as me ON (md.id_encabezado=me.id) "
                    . "WHERE me.fecha_inicio='$fechaini' AND me.fecha_fin='$fechafin' AND md.ficha='$ficha'";
    elseif($opc==7)
        $consulta = "SELECT md.extra_175 as horas "
                    . "FROM marvin_detalle as md "
                    . "LEFT JOIN marvin_encabezado as me ON (md.id_encabezado=me.id) "
                    . "WHERE me.fecha_inicio='$fechaini' AND me.fecha_fin='$fechafin' AND md.ficha='$ficha'";
    elseif($opc==8)
        $consulta = "SELECT md.extra_188 as horas "
                    . "FROM marvin_detalle as md "
                    . "LEFT JOIN marvin_encabezado as me ON (md.id_encabezado=me.id) "
                    . "WHERE me.fecha_inicio='$fechaini' AND me.fecha_fin='$fechafin' AND md.ficha='$ficha'";
    elseif($opc==9)
        $consulta = "SELECT md.extra_219 as horas "
                    . "FROM marvin_detalle as md "
                    . "LEFT JOIN marvin_encabezado as me ON (md.id_encabezado=me.id) "
                    . "WHERE me.fecha_inicio='$fechaini' AND me.fecha_fin='$fechafin' AND md.ficha='$ficha'";
    elseif($opc==10)
        $consulta = "SELECT md.extra_225 as horas "
                    . "FROM marvin_detalle as md "
                    . "LEFT JOIN marvin_encabezado as me ON (md.id_encabezado=me.id) "
                    . "WHERE me.fecha_inicio='$fechaini' AND me.fecha_fin='$fechafin' AND md.ficha='$ficha'";
    elseif($opc==11)
        $consulta = "SELECT md.extra_263_regular as horas "
                    . "FROM marvin_detalle as md "
                    . "LEFT JOIN marvin_encabezado as me ON (md.id_encabezado=me.id) "
                    . "WHERE me.fecha_inicio='$fechaini' AND me.fecha_fin='$fechafin' AND md.ficha='$ficha'";
    elseif($opc==12)
        $consulta = "SELECT md.extra_263_libre as horas "
                    . "FROM marvin_detalle as md "
                    . "LEFT JOIN marvin_encabezado as me ON (md.id_encabezado=me.id) "
                    . "WHERE me.fecha_inicio='$fechaini' AND me.fecha_fin='$fechafin' AND md.ficha='$ficha'";
    elseif($opc==13)
        $consulta = "SELECT md.extra_306 as horas "
                    . "FROM marvin_detalle as md "
                    . "LEFT JOIN marvin_encabezado as me ON (md.id_encabezado=me.id) "
                    . "WHERE me.fecha_inicio='$fechaini' AND me.fecha_fin='$fechafin' AND md.ficha='$ficha'";
    elseif($opc==14)
        $consulta = "SELECT md.extra_313 as horas "
                    . "FROM marvin_detalle as md "
                    . "LEFT JOIN marvin_encabezado as me ON (md.id_encabezado=me.id) "
                    . "WHERE me.fecha_inicio='$fechaini' AND me.fecha_fin='$fechafin' AND md.ficha='$ficha'";
    elseif($opc==15)
        $consulta = "SELECT md.extra_329 as horas "
                    . "FROM marvin_detalle as md "
                    . "LEFT JOIN marvin_encabezado as me ON (md.id_encabezado=me.id) "
                    . "WHERE me.fecha_inicio='$fechaini' AND me.fecha_fin='$fechafin' AND md.ficha='$ficha'";
    elseif($opc==16)
        $consulta = "SELECT md.extra_375 as horas "
                    . "FROM marvin_detalle as md "
                    . "LEFT JOIN marvin_encabezado as me ON (md.id_encabezado=me.id) "
                    . "WHERE me.fecha_inicio='$fechaini' AND me.fecha_fin='$fechafin' AND md.ficha='$ficha'";
    elseif($opc==17)
        $consulta = "SELECT md.extra_394 as horas "
                    . "FROM marvin_detalle as md "
                    . "LEFT JOIN marvin_encabezado as me ON (md.id_encabezado=me.id) "
                    . "WHERE me.fecha_inicio='$fechaini' AND me.fecha_fin='$fechafin' AND md.ficha='$ficha'";
    elseif($opc==18)
        $consulta = "SELECT md.extra_438 as horas "
                    . "FROM marvin_detalle as md "
                    . "LEFT JOIN marvin_encabezado as me ON (md.id_encabezado=me.id) "
                    . "WHERE me.fecha_inicio='$fechaini' AND me.fecha_fin='$fechafin' AND md.ficha='$ficha'";
    elseif($opc==19)
        $consulta = "SELECT md.extra_459 as horas "
                    . "FROM marvin_detalle as md "
                    . "LEFT JOIN marvin_encabezado as me ON (md.id_encabezado=me.id) "
                    . "WHERE me.fecha_inicio='$fechaini' AND me.fecha_fin='$fechafin' AND md.ficha='$ficha'";
    elseif($opc==20)
        $consulta = "SELECT md.extra_547 as horas "
                    . "FROM marvin_detalle as md "
                    . "LEFT JOIN marvin_encabezado as me ON (md.id_encabezado=me.id) "
                    . "WHERE me.fecha_inicio='$fechaini' AND me.fecha_fin='$fechafin' AND md.ficha='$ficha'";
    elseif($opc==21)
        $consulta = "SELECT md.extra_547 as horas "
                    . "FROM marvin_detalle as md "
                    . "LEFT JOIN marvin_encabezado as me ON (md.id_encabezado=me.id) "
                    . "WHERE me.fecha_inicio='$fechaini' AND me.fecha_fin='$fechafin' AND md.ficha='$ficha'";
    elseif($opc==22)
        $consulta = "SELECT md.extra_656 as horas "
                    . "FROM marvin_detalle as md "
                    . "LEFT JOIN marvin_encabezado as me ON (md.id_encabezado=me.id) "
                    . "WHERE me.fecha_inicio='$fechaini' AND me.fecha_fin='$fechafin' AND md.ficha='$ficha'";
    elseif($opc==23)
        $consulta = "SELECT md.certificado_medico as horas "
                    . "FROM marvin_detalle as md "
                    . "LEFT JOIN marvin_encabezado as me ON (md.id_encabezado=me.id) "
                    . "WHERE me.fecha_inicio='$fechaini' AND me.fecha_fin='$fechafin' AND md.ficha='$ficha'";
    elseif($opc==24)
        $consulta = "SELECT md.tardanza as horas "
                    . "FROM marvin_detalle as md "
                    . "LEFT JOIN marvin_encabezado as me ON (md.id_encabezado=me.id) "
                    . "WHERE me.fecha_inicio='$fechaini' AND me.fecha_fin='$fechafin' AND md.ficha='$ficha'";
    elseif($opc==25)
        $consulta = "SELECT md.ausencia as horas "
                    . "FROM marvin_detalle as md "
                    . "LEFT JOIN marvin_encabezado as me ON (md.id_encabezado=me.id) "
                    . "WHERE me.fecha_inicio='$fechaini' AND me.fecha_fin='$fechafin' AND md.ficha='$ficha'";
    elseif($opc==26)
        $consulta = "SELECT md.permiso as horas "
                    . "FROM marvin_detalle as md "
                    . "LEFT JOIN marvin_encabezado as me ON (md.id_encabezado=me.id) "
                    . "WHERE me.fecha_inicio='$fechaini' AND me.fecha_fin='$fechafin' AND md.ficha='$ficha'";
    
    
    $resultado = query($consulta, $conexion);
    $fetch = fetch_array($resultado);
    
    $horas = $fetch[horas];

    return $horas;
}

function otrosing_acum($ficha, $fechaini, $fechafin)
{
    include 'globales.php';
    $selectra = new bd($_SESSION['bd']);

    $consulta = "SELECT (SUM(otros_ing)) as monto "
            . "FROM salarios_acumulados "
            . "WHERE ficha='".$FICHA."' and fecha_pago >='$fechaini' and fecha_pago <='$fechafin'"; 
    $resultado = $selectra->query($consulta);
    $fila = $resultado->fetch_assoc();
    if ($resultado->num_rows <= 0) {
        return 0;
    } else {
        return $fila['monto'];
    }
}

function librenacionalcompensatorio($ficha, $fechaini, $fechafin)
{
    include 'globales.php';
    $selectra = new bd($_SESSION['bd']);

    $consulta = "SELECT COUNT(turno_id) as monto "
            . "FROM nomcalendarios_personal "
            . "WHERE ficha='".$FICHA."' and fecha >='$fechaini' and fecha <='$fechafin' and turno_id=204 "; 
    $resultado = $selectra->query($consulta);
    $fila = $resultado->fetch_assoc();
    if ($resultado->num_rows <= 0) {
        return 0;
    } else {
        return $fila['monto'];
    }
}

function acumulado_liquidacion_panama($ficha, $fechafin)
{
    include 'globales.php';
    global $conexion;

//    $consulta = "SELECT (SUM(salario_bruto)+SUM(vacac)+SUM(bono)) as monto "
//            . "FROM salarios_acumulados "
//            . "WHERE ficha='".$ficha."' and fecha_pago >=DATE_SUB('$fechafin',INTERVAL 5 YEAR) and fecha_pago <='$fechafin'";
//    
    $fechaf=explode("-",$fechafin);
    $fechafin1=$fechaf[0]."-".$fechaf[1]."-"."01";
     $consulta = "SELECT (SUM(salario_bruto)+SUM(vacac)+SUM(bono)+SUM(otros_ing)+SUM(prima)+SUM(gtorep)) as monto "
            . "FROM salarios_acumulados "
            . "WHERE ficha='".$ficha."' and fecha_pago >DATE_SUB('$fechafin1',INTERVAL '5' YEAR) and fecha_pago <='$fechafin'";
    $resultado = query($consulta, $conexion);
    $fetch_resultado = fetch_array($resultado);
    $monto = $fetch_resultado['monto'];
    
     
    if ($monto==0 || $monto==NULL || $monto=='') 
    {
        return 0;
    } else 
    {
        return $monto;
    }
}

function indemnizacion_proporcional_ultimas_doce_quincenas($ficha, $fechafin)
{
    include 'globales.php';
    global $conexion;


  $anio = substr($fechafin, 0, 4);
$mes  = substr($fechafin, 5, 2)-1;
    $consultavac = "SELECT fecha_pago,vacac "
            . "FROM salarios_acumulados "
            . "WHERE ficha='".$ficha."' and  month(fecha_pago) ='$mes' and  year(fecha_pago) ='$anio' and `vacac`>0";
    $resultadovac = query($consultavac, $conexion);
    $fetch_resultadovac = fetch_array($resultadovac);
    $montox = $fetch_resultadovac['vacac'];
    if ($montox==0 || $montox==NULL || $montox=='') 
    {
        $vac= 12-0;
    } else 
    {
       $vac=12-1;
    }
//echo $vac;
//    
    
    $consulta = "SELECT (SUM(salario_bruto)+SUM(gtorep)+SUM(vacac)) as monto "
            . "FROM "
            . "(SELECT salario_bruto,gtorep,vacac FROM salarios_acumulados "
            . "WHERE ficha='".$ficha."' AND fecha_pago <='$fechafin' "
            . "AND frecuencia_planilla IN ( 2, 3, 8 ) "
            . "ORDER BY fecha_pago DESC "
            . "LIMIT 0 , $vac) "
            . "AS subquery";
    $resultado = query($consulta, $conexion);
    $fetch_resultado = fetch_array($resultado);
    $monto = $fetch_resultado['monto'];
    
     
    if ($monto==0 || $monto==NULL || $monto=='') 
    {
        return 0;
    } else 
    {
        return ($monto/26);
    }
}

function indemnizacion_proporcional_ultimas_dos_quincenas($ficha, $fechafin)
{
    include 'globales.php';
    global $conexion;

//    $consulta = "SELECT (SUM(salario_bruto)+SUM(vacac)+SUM(bono)) as monto "
//            . "FROM salarios_acumulados "
//            . "WHERE ficha='".$ficha."' and fecha_pago >=DATE_SUB('$fechafin',INTERVAL 5 YEAR) and fecha_pago <='$fechafin'";
//    
  
$anio = substr($fechafin, 0, 4);
$mes  = substr($fechafin, 5, 2)-1;
    $consultavac = "SELECT fecha_pago,vacac "
            . "FROM salarios_acumulados "
            . "WHERE ficha='".$ficha."' and  month(fecha_pago) ='$mes' and  year(fecha_pago) ='$anio' and `vacac`>0";
    $resultadovac = query($consultavac, $conexion);
    $fetch_resultadovac = fetch_array($resultadovac);
    $montox = $fetch_resultadovac['vacac'];
    if ($montox==0 || $montox==NULL || $montox=='') 
    {
        $vac= 2-0;
    } else 
    {
       $vac=2-1;
    }


  
    $consulta = "SELECT (SUM(salario_bruto)+SUM(gtorep)+SUM(vacac)) as monto "
            . "FROM "
            . "(SELECT salario_bruto,gtorep,vacac FROM salarios_acumulados "
            . "WHERE ficha='".$ficha."' AND fecha_pago <='$fechafin' "
            . "AND frecuencia_planilla IN ( 2, 3 ,8) "
            . "ORDER BY fecha_pago DESC "
            . "LIMIT 0 , $vac) "
            . "AS subquery";
    $resultado = query($consulta, $conexion);
    $fetch_resultado = fetch_array($resultado);
    $monto = $fetch_resultado['monto'];
    
     
    if ($monto==0 || $monto==NULL || $monto=='') 
    {
        return 0;
    } else 
    {
        return ($monto/4.333);
    }
}

function diferencia_fechas_anios($fechaingreso, $fechafin) 
{
    $fecha1 = new DateTime($fechaingreso);
    $fecha2 = new DateTime($fechafin);
    
    if ($fecha1 > $fecha2) 
    {
        return 0;
    }
    
    $diff = $fecha1->diff($fecha2);
    
    $anios = $diff->y;
    $meses = $diff->m;
    $dias = $diff->d;
    
    return $anios;
   
}

function diferencia_fechas_meses($fechaingreso, $fechafin) 
{
    $fecha1 = new DateTime($fechaingreso);
    $fecha2 = new DateTime($fechafin);
    
    if ($fecha1 > $fecha2) 
    {
        return 0;
    }
    
    $diff = $fecha1->diff($fecha2);
    
    $anios = $diff->y;
    $meses = $diff->m;
    $dias = $diff->d;
    
    $meses = ( $diff->y * 12 ) + $diff->m;
    
    return $meses;
   
}

function diferencia_fechas_dias($fechaingreso, $fechafin) 
{
    $fecha1 = new DateTime($fechaingreso);
    $fecha2 = new DateTime($fechafin);
    
    if ($fecha1 > $fecha2) 
    {
        return 0;
    }
    
    $diff = $fecha1->diff($fecha2);
    
    $anios = $diff->y;
    $meses = $diff->m;
    $dias = $diff->d;
    
    return $dias;
   
}

function diferencia_fechas_anios_total($fechaingreso, $fechafin) 
{
    $fecha1 = new DateTime($fechaingreso);
    $fecha2 = new DateTime($fechafin);
    
    if ($fecha1 > $fecha2) 
    {
        return 0;
    }
    
    $diff = $fecha1->diff($fecha2);
    
    $anios = $diff->y;
    $meses = $diff->m;
    $dias = $diff->d;
    
    $diff_anios = $anios + ($meses/12) + ($dias/365);
    
    return $diff_anios;
   
}

function campoadicionalper_concepto($idcampo,$monto,$ficha) 
{
    include 'globales.php';
    global $conexion;
    
    //$conexion = conexion();
    $consulta = "UPDATE nomcampos_adic_personal "
              . "SET valor=" . $monto . " "
              . "WHERE id='" . $idcampo . "' and ficha='" . $ficha . "' and tiponom='".$_SESSION[codigo_nomina]. "'";
    $resultado = query($consulta, $conexion);
}

function vacaciones_acum($ficha, $fechaini, $fechafin)
{
    include 'globales.php';
    $selectra = new bd($_SESSION['bd']);

    $consulta = "SELECT (SUM(vacac)) as monto "
            . "FROM salarios_acumulados "
            . "WHERE ficha='".$FICHA."' and fecha_pago >='$fechaini' and fecha_pago <='$fechafin'"; 
    $resultado = $selectra->query($consulta);
    $fila = $resultado->fetch_assoc();
    if ($resultado->num_rows <= 0) {
        return 0;
    } else {
        return $fila['monto'];
    }
}
function acumulados_salarios_mes($ficha, $fecha)
{
    include 'globales.php';
    $selectra = new bd($_SESSION['bd']);

    $anio = substr($fecha, 0, 4);
    $mes  = substr($fecha, 5, 2);

    $consulta = "select ifnull(sum(salario_bruto),0) as monto "
            . "from salarios_acumulados "
            . "where ficha='".$FICHA."' and year(fecha_pago)='$anio' and month(fecha_pago)=$mes and frecuencia_planilla NOT IN (7,8)"; 
    $resultado = $selectra->query($consulta);
    $fila = $resultado->fetch_assoc();
    if ($resultado->num_rows <= 0) {
        return 0;
    } else {
        return $fila['monto'];
    }
}

function acumulados_impuesto_mes($ficha, $fecha)
{
    include 'globales.php';
    $selectra = new bd($_SESSION['bd']);

    $anio = substr($fecha, 0, 4);
    $mes  = substr($fecha, 5, 2);

    $consulta = "select ifnull(sum(islr),0) as monto "
            . "from salarios_acumulados "
            . "where ficha='".$FICHA."' and year(fecha_pago)='$anio' and month(fecha_pago)=$mes and frecuencia_planilla NOT IN (7,8)"; 
    $resultado = $selectra->query($consulta);
    $fila = $resultado->fetch_assoc();
    if ($resultado->num_rows <= 0) {
        return 0;
    } else {
        return $fila['monto'];
    }
}


function acumsal60mesessalacum($tipo, $fecha_inicio, $fecha_fin) 
{
    include 'globales.php';

    $selectra = new bd($_SESSION['bd']);

    $fechames11 = date('Y-m-d', strtotime('-60 month')) ; // resta 6 mes

   $consulta = "select (SUM(salario_bruto)+SUM(vacac)+SUM(gtorep)) as monto from salarios_acumulados where ficha='".$FICHA."' and fecha_pago >='$fechames11' and fecha_pago <='$fecha_fin'"; 


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

function acumuladosprima($ficha, $fecha)
{
    include 'globales.php';
    $selectra = new bd($_SESSION['bd']);

    $anio = substr($fecha, 0, 4);

    $consulta = "select ifnull(sum(prima),0) as monto "
            . "from salarios_acumulados "
            . "where ficha='".$FICHA."' and year(fecha_pago)='$anio'"; 
    $resultado = $selectra->query($consulta);
    $fila = $resultado->fetch_assoc();
    if ($resultado->num_rows <= 0) {
        return 0;
    } else {
        return $fila['monto'];
    }
}

?>
