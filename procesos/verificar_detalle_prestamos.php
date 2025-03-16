<?php
include('../generalp.config.inc.php');
session_start();

error_reporting(E_ALL);
header("Content-Type: text/html;charset=utf-8");
ini_set('memory_limit', '-1');
ini_set('time_limit', '-1');
set_time_limit(-1);

function bisiesto($a){
  return (($a%4==0 && $a%100!=0)||($a%400==0)?true:false);
}

function dias_meses($anio){
  return array( 31,
                bisiesto($anio)?29:28,
                31,
                30,
                31,
                30,
                31,
                31,
                30,
                31,
                30,
                31);
}

function generar_cuotas($fecha_inicio,$monto_inicial,$monto_cuota,$numero_cuotas,$diciembre_generar_cuota=false){
    
    $saldo=$monto_inicial;

    $return=[];

    $return[]=[
        "fecha"          => $fecha_inicio,
        "saldo_inicial"  => $saldo,
        "saldo_final"    => round($saldo-$monto_cuota,2),
        "numero"         => 1,
        "monto"          => $monto_cuota
    ];

    for($i=2; $i <= $numero_cuotas; $i++) { 
        $tmp=explode("-",$return[$i-2]["fecha"]);

        $anio=$tmp[0];
        $mes =$tmp[1];
        $dia =$tmp[2];

        if($dia=="15"){//si es quince, el valor q viene es el ultimo dia de ese mes            
            $fecha=$anio."-".$mes."-".dias_meses($anio)[$mes*1-1];
        }
        else{//si es el ultimo dia del mes. viene el mes siguiente
            $mes=$mes*1+1;
            if($diciembre_generar_cuota==false and $mes>=12){
                $mes=1;
                $anio++;
            }
            if($diciembre_generar_cuota==true and $mes>=13){
                $mes=1;
                $anio++;
            }

            $fecha=$anio."-".str_pad($mes,2,'0',STR_PAD_LEFT)."-15";
        }

        $saldo-=$monto_cuota;
        $salir=false;
        if($saldo<=0){
            $monto_cuota=round($monto_cuota-$saldo,2);
            $saldo=0;
            $saldo_final=0;
            $salir=true;
        }
        $saldo_final=round($saldo-$monto_cuota,2);
        if($saldo_final<=0){
            $salir=true;
            $monto_cuota=round($saldo,2);
            $saldo_final=0;
        }

        $return[]=[
            "fecha"          => $fecha,
            "saldo_inicial"  => round($saldo,2),
            "saldo_final"    => $saldo_final,
            "numero"         => $i,
            "monto"          => $monto_cuota
        ];
        if($salir){
            break;
        }
    }

    return $return;
}

$db_user   = DB_USUARIO;
$db_pass   = DB_CLAVE;
$db_name   = $_SESSION['bd'];
$db_host   = DB_HOST;
$usuario = $_SESSION['usuario'];

$conexion =  mysqli_connect( $db_host, $db_user, $db_pass, $db_name  ) or
      die( 'Could not open connection to server' );

mysqli_query($conexion, 'SET CHARACTER SET utf8');


//$sql="select * from nomprestamos_cabecera where numpre=4 limit 10";
$sql="SELECT * FROM nomprestamos_cabecera WHERE estadopre='Pendiente' AND numpre <= 195"; 

$prestamo=mysqli_query($conexion,$sql);



while($row=mysqli_fetch_array($prestamo)){  
    //print_r($row);
    //$sql="select * from nomprestamos_detalles where numpre='".$row["numpre"]."'";

    $numpre=$row["numpre"];
    $n_cuotas=$row["cuotas"];
    $ficha=$row["ficha"];
    $monto_cuota=round($row["mtocuota"]/2,2);
    $total=$row["monto"];
    $fecha_inicio=$row["fecpricup"];
    $codnom=$row["codnom"];

    $tipocuo='';


    $result=generar_cuotas($fecha_inicio, $total, $monto_cuota, $n_cuotas);
    //print_r($result);
    print "<br>";

    if($row["cuotas"]!=count($result)){
        print "update nomprestamos_cabecera set cuotas='".count($result)."' where numpre='".$numpre."' and ficha='$ficha';<br>";
    }

    $sql="select * from nomprestamos_detalles where numpre='$numpre' and ficha='$ficha' order by anioven, mesven, numcuo";
    $detalle_res=mysqli_query($conexion,$sql);

    $prestamo_detalle=[];
    while($row2=mysqli_fetch_array($detalle_res)) {
        $prestamo_detalle[]=$row2;
    }
    $i2=0;
    //print_r($detalle);


    for($i=0; $i<count($result); $i++, $i2++){ 
        $fecha         = $result[$i]["fecha"];
        $numcuo        = $i+1;
        $salinicial    = $result[$i]["saldo_inicial"];
        $salfinal      = $result[$i]["saldo_final"];
        $fechaven      = $fecha;
        $montocuo      = $result[$i]["monto"];

        $tmp=explode("-", $fecha);
        $anio = $tmp[0];
        $mes  = $tmp[1]*1;

        $anioven     = $anio;
        $mesven      = $mes;
        $dias        = 0;
        $montoint    = 0;
        $montocap    = 0;
        $fechacan    = '0000-00-00';
        $estadopre   = 'Pendiente';
        $detalle     = '';
        $dedespecial = 0;
        $sfechaven   = '0000-00-00';
        $sfechacan   = '0000-00-00';
        $ee          = 0;

        //buscarla cuota por el mes y año (no por el numero de la cuota)
        //si existe, actualizar los datos
        //si no, insertar
        
        //si hay registros viejos (hacer update), en caso contrario hacer inserts
        if($i2<count($prestamo_detalle)){

            $anioven_anterior    = $prestamo_detalle[$i2]["anioven"];
            $mesven_anterior     = $prestamo_detalle[$i2]["mesven"];
            $numcuo_anterior     = $prestamo_detalle[$i2]["numcuo"];
            $fechaven_anterior   = $prestamo_detalle[$i2]["fechaven"];
            $salinicial_anterior = $prestamo_detalle[$i2]["salinicial"];
            $salfinal_anterior   = $prestamo_detalle[$i2]["salfinal"];

            print "
                update nomprestamos_detalles 
                set 
                    fechaven='$fecha',
                    numcuo='$numcuo',
                    salinicial='$salinicial',
                    montocuo='$montocuo',
                    salfinal='$salfinal',
                    anioven='$anioven',
                    mesven='$mesven'
                where 
                    numpre='$numpre' and
                    ficha='$ficha' and
                    anioven='$anioven_anterior' and 
                    mesven='$mesven_anterior' and 
                    numcuo='$numcuo_anterior' and
                    fechaven='$fechaven_anterior' and
                    salinicial=$salinicial_anterior and
                    salfinal=$salfinal_anterior
                ;<br>";
        }
        else{
            print "insert into nomprestamos_detalles(numpre, ficha, tipocuo, numcuo, fechaven, anioven, mesven, dias, salinicial, montocuo, montoint, montocap, salfinal, fechacan, estadopre, detalle, dedespecial, codnom, sfechaven, sfechacan, ee)
            values('$numpre', '$ficha', '$tipocuo', '$numcuo', '$fechaven', '$anioven', '$mesven', '$dias', '$salinicial', '$montocuo', '$montoint', '$montocap', '$salfinal', '$fechacan', '$estadopre', '$detalle', '$dedespecial', '$codnom', '$sfechaven', '$sfechacan', '$ee');<br>";
        } 

    }   



    
}

print "<br>/*Finalizo*/";
?>

