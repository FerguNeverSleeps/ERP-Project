<?php
include('../../../generalp.config.inc.php');
require_once "funciones_vacaciones.php";
session_start();
 
error_reporting(E_ALL);

$db_user   = DB_USUARIO;
$db_pass   = DB_CLAVE;
$db_name   = $_SESSION['bd'];
$db_host   = DB_HOST;
$usuario = $_SESSION['usuario'];

$conexion =  mysqli_connect( $db_host, $db_user, $db_pass, $db_name  ) or
      die( 'Could not open connection to server' );

mysqli_query($conexion, 'SET CHARACTER SET utf8');

//CASO AUTOMATICO DIARIO
$fecha_acreditacion = date('Y-m-d'); 
    
//CASO MANUAL DIARIO
//    $fecha_acreditacion = "2017-01-01";
    
$dias = $restante = 30;

$consulta_personal="SELECT personal_id, cedula, apenom, tipnom, nomposicion_id, fecha_permanencia, useruid, usuario_workflow "
        . "FROM nompersonal WHERE `estado` NOT LIKE '%Baja%' AND `estado` "
        . "NOT LIKE '%INTERINO%' AND `estado` NOT LIKE '%PENSIONADO POR INVALIDEZ%' "
        . "AND `tipemp` LIKE 'Fijo' AND `tipo_funcionario` IN ( 1, 7, 20, 39 ) "
        . "AND fecha_permanencia < '{$fecha_acreditacion}'";

$resultado_personal=mysqli_query($conexion,$consulta_personal);

$cont=1;
while($fila=mysqli_fetch_array($resultado_personal))
{          
    $personal_id=$fila['personal_id'];
    $cedula=$fila['cedula'];
    $apenom=$fila['apenom'];
    $tipnom=$fila['tipnom'];
    $nomposicion_id=$fila['nomposicion_id'];
    $fecha_permanencia=$fila['fecha_permanencia'];
    $usr_uid=$fila['useruid'];
    $cod_user=$fila['usuario_workflow'];
    
    $anio_permanencia = date("Y", strtotime($fecha_permanencia));
    $mes_permanencia = date("m", strtotime($fecha_permanencia));
    $dia_permanencia = date("d", strtotime($fecha_permanencia));
    
    $anio_acreditacion = date("Y", strtotime($fecha_acreditacion));
    $mes_acreditacion = date("m", strtotime($fecha_acreditacion));
    $dia_acreditacion = date("d", strtotime($fecha_acreditacion));
    
    $cantidad_meses=cantidad_meses($fecha_permanencia,$fecha_acreditacion);
    $cantidad_dias_mes=cantidad_dias_mes($fecha_permanencia);
    
    
    if($cantidad_meses>=11 && $mes_acreditacion==($mes_permanencia-1) && $dia_acreditacion==$dia_permanencia)
    {
        //SE GENERA FECHA INICIO PERIODO LEGAL A PARTIR DEL MES Y DIA DE LA FECHA DE PERMANENCIA
        $fecha_ini_per = $anio_acreditacion."-".$mes_permanencia."-".$dia_permanencia;
        $fecha_inicio_periodo = date('Y-m-d', strtotime($fecha_ini_per));
        
        //SE GENERA FECHA FIN PERIODO LEGAL 11 MESES A PARTIR DE LA FECHA DE INICIO DEL PERIODO
        $fecha_fin_periodo = date('Y-m-d', strtotime($fecha_inicio_periodo. ' + 11 months'));
        
        $fechai = $anio_acreditacion."-".$mes_permanencia."-".$dia_permanencia;
        $fecha_inicio = date('Y-m-d', strtotime($fechai. ' + 1 days'));
        $fecha_fin = date('Y-m-d', strtotime($fecha_inicio. ' + 30 days'));
        
        $tipo=11;        
        $estatus=1;
        
        $fecha_creacion = date('Y-m-d');
        
        echo "ID: "; echo $personal_id;
        echo " - CEDULA: "; echo $cedula;
        echo " - APELLIDOS Y NOMBRE: "; echo $apenom;
        echo " - POSICION: "; echo $nomposicion_id;
        echo " - FECHA INGRESO: "; echo $fecha_permanencia;
        echo " - DIA INGRESO: "; echo $dia_permanencia;
        echo " - MES INGRESO: "; echo $mes_permanencia;
        echo " - AÑO INGRESO: "; echo $anio_permanencia;
        echo " - FECHA REGISTRO: "; echo $fecha_acreditacion;
        echo " - DIA REGISTRO: "; echo $dia_acreditacion;
        echo " - MES REGISTRO: "; echo $mes_acreditacion;
        echo " - AÑO REGISTRO: "; echo $anio_acreditacion;
        echo " - FECHA INICIO: "; echo $fecha_inicio;
        echo " - FECHA FIN: "; echo $fecha_fin;
        echo " - CANTIDAD MESES: "; echo $cantidad_meses;
        echo " - CANTIDAD DIAS MES INGRESO: "; echo $cantidad_dias_mes;
        echo "<br>";
        echo "<br>";
        
        $descripcion="VACACIONES GENERADAS AUTOMATICAMENTE POR EL SISTEMA - FECHA: ". $fecha_acreditacion . " PERIODO: 2016-2017";   
        
        //INSERCIÓN EXPEDIENTE
        $consulta_expediente="INSERT INTO expediente
                    (cod_expediente_det, cedula, descripcion, fecha_inicio, fecha_fin, fecha_inicio_periodo, fecha_fin_periodo, fecha,
                    dias, restante, tipo, estatus, fecha_creacion, usuario_creacion)
                    VALUES  
                    ('','{$cedula}','{$descripcion}', '{$fecha_inicio}', '{$fecha_fin}','{$fecha_inicio_periodo}','{$fecha_fin_periodo}', "
                    . "'{$fecha_acreditacion}','{$dias}','{$restante}','{$tipo}','{$estatus}','{$fecha_creacion}','{$usuario}')";
        $resultado_expediente=mysqli_query($conexion,$consulta_expediente);
//        echo "CONSULTA EXPEDIENTE: "; echo $consulta_expediente; 
//        echo "<br>";
//        echo "<br>";
        $ultimo_id_expediente = mysqli_insert_id($conexion);
        
        $tipo_justificacion=7;
        //INSERCION DIAS INCAPACIDAD
        $tiempo = 30;//$dias*8;
        $consulta_incapacidad="INSERT INTO dias_incapacidad
                (id, cod_user, tipo_justificacion, fecha, tiempo, observacion, documento, st, usr_uid, fecha_vence, 
                dias, horas, minutos, idparent, cedula)
                VALUES  
                ('','{$nomposicion_id}','{$tipo_justificacion}', '{$fecha_acreditacion}', '{$tiempo}', '{$descripcion}', '', '', '{$usr_uid}',"
                . "'{$fecha_fin}', '{$dias}', '{$horas}','{$minutos}','{$ultimo_id_expediente}', '{$cedula}')";
        $resultado_incapacidad=mysqli_query($conexion,$consulta_incapacidad);
//        echo "CONSULTA DIAS INCAPACIDAD: "; echo $consulta_incapacidad; 
//        echo "<br>";
//        echo "<br>";
        $ultimo_id_incapacidad = mysqli_insert_id($conexion);
        
        //INSERCION EN PERIODOS VACACIONES
        $consulta_vacaciones="INSERT INTO periodos_vacaciones
                (id_periodo_vacacion, usr_uid, fini_periodo, ffin_periodo, dias,
                saldo, fecha_efectivas, fecha_creacion, usuario_creacion, id_dias_incapacidad, vac_desde,
                vac_hasta, cod_expediente_det, cedula)
                VALUES  
                ('','{$usr_uid}','{$fecha_inicio_periodo}', '{$fecha_fin_periodo}',"
                . "'{$dias}', '{$restante}', '{$fecha_inicio}','{$fecha_acreditacion}','{$usuario}','{$ultimo_id_incapacidad}','{$fecha_inicio}', '{$fecha_fin}',"
                . "'{$ultimo_id_expediente}', '{$cedula}')";
        $resultado_vacaciones=mysqli_query($conexion,$consulta_vacaciones);
//        echo "CONSULTA VACACIONES: "; echo $consulta_vacaciones; 
//        echo "<br>";
//        echo "<br>";
//        
//        exit;
    }    
    $cont++;
}
if($cont==1)
{
    echo "NO SE ENCONTRARON REGISTROS PARA ESTA FECHA";
    exit;
}
?>

