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
    //$fecha_registro = date('Y-m-d');   
    //CASO MANUAL DIARIO
    $fecha_registro = "2017-02-28";
    $anio_registro = date("Y", strtotime($fecha_registro));
    $mes_registro = date("m", strtotime($fecha_registro));
    $dia_registro = date("d", strtotime($fecha_registro));

 $consulta_personal = "UPDATE nompersonal SET estado = '{$situacion_nueva}',"
                        . "fechavac = '',fechareivac = ''"
                        . "WHERE cedula='{$cedula}'";

$resultado_personal=mysqli_query($conexion,$consulta_personal);

$cont=1;
while($fila=mysqli_fetch_array($resultado_personal))
{          
    $personal_id=$fila['personal_id'];
    $cedula=$fila['cedula'];
    $apenom=$fila['apenom'];
    $nomposicion_id=$fila['nomposicion_id'];
    $fecing=$fila['fecing'];
    
    echo "ID: "; echo $personal_id;
    echo " - CEDULA: "; echo $cedula;
    echo " - APELLIDOS Y NOMBRE: "; echo $apenom;
    echo " - POSICION: "; echo $nomposicion_id;
    echo " - FECHA INGRESO: "; echo $fecing;       
    echo "<br>";
    echo "<br>";
    
    
    //APLICAR REINTEGRO A FUNCIONARIO EN LICENCIA
    
//    $descripcion="REINTEGRO POR CULMINACIÓN DE PERIODO DE LICENCIA (VENCIMIENTO) GENERADO AUTOMATICAMENTE POR EL SISTEMA - FECHA: ". $fecha_registro;     
//    
//    $tipo=58;
//    $estatus=1;
//    $fecha_creacion = date("Y-m-d");       
    
    //INSERCIÓN EXPEDIENTE
//    $consulta_expediente="INSERT INTO expediente
//                (cod_expediente_det, cedula, descripcion, fecha, fecha_inicio, fecha_fin,fecha_notificacion, fecha_decreto_ingreso_anterior,
//                fecha_decreto_ingreso_nuevo, gerencia_anterior, gerencia_nueva, departamento_anterior, departamento_nuevo, seccion_anterior, seccion_nueva,
//                posicion_anterior, posicion_nueva,cod_cargo_anterior, cod_cargo_nuevo, funcion_anterior, funcion_nueva, planilla_anterior, planilla_nueva,
//                situacion_anterior, situacion_nueva,
//                tipo, fecha_creacion, usuario_creacion, fecha_aprobacion, usuario_aprobacion)
//                VALUES  
//                ('','{$cedula}','{$descripcion}', '{$fecha_reintegro}','{$fecha_inicio}','{$fecha_fin}','{$fecha_notificacion}',"
//                . "'{$fecha_decreto_ingreso_anterior}','{$fecha_decreto_ingreso_nuevo}','{$gerencia_anterior}','{$gerencia_nueva}',"
//                . "'{$departamento_anterior}','{$departamento_nuevo}','{$seccion_anterior}','{$seccion_nueva}',"
//                . "'{$posicion_anterior}','{$posicion_nueva}','{$cod_cargo_anterior}','{$cargo_nuevo}',"
//                . "'{$funcion_anterior}','{$funcion_nueva}','{$planilla_anterior}','{$planilla_nueva}',"
//                . "'{$numero_decreto_anterior}','{$numero_decreto_nuevo}','{$situacion_anterior}','{$situacion_nueva}',"
//                . "'{$tipo}','{$fecha_creacion}','{$usuario}','{$fecha_creacion}','{$usuario}')";    
//    $resultado_expediente=query($consulta,$conexion);
    
//        echo "CONSULTA EXPEDIENTE: "; echo $consulta_expediente; 
//        echo "<br>";
//        echo "<br>"; 
    
    //ACTUALIZACION PERSONAL
    $situacion_nueva="REGULAR";
    $consulta_personal = "UPDATE nompersonal SET estado = '{$situacion_nueva}',"                       
                        . "fechavac = '',fechareivac = ''"
                        . "WHERE cedula='{$cedula}'";    
    $resultado_personal=mysqli_query($conexion,$consulta_personal); 
    
    //ACTUALIZACION POSICION
    $estado = 1;
    $consulta_posicion = "UPDATE nomposicion SET estado = '{$estado}'"
               . "WHERE nomposicion_id='{$nomposicion_id}'";
    //echo $consulta2;
    $resultado_posicion=mysqli_query($conexion,$consulta_posicion);  
    
//    //ACCION FUNCIONARIO    
//    $consulta_correlativo = "SELECT correlativo FROM accion_funcionario_tipo "
//                    . "WHERE id_accion_funcionario_tipo = '$tipo_accion'";
//    $resultado_correlativo=query($consulta_correlativo,$conexion);
//    $fetch_correlativo=fetch_array($resultado_correlativo,$conexion);
//    $correlativo = $fetch_correlativo["correlativo"];
//    $correlativo = $correlativo+1;
//
//   $consulta_accion="INSERT INTO accion_funcionario
//            (id_accion_funcionario, tipo_accion, numero_accion,id_funcionario)
//            VALUES  
//            ('','{$tipo_accion}','{$correlativo}', '{$id_empleado}')";
//    $resultado_accion=query($consulta_accion,$conexion);           
//
//    $consulta_accion_tipo ="UPDATE accion_funcionario_tipo SET correlativo='".$correlativo."' "
//                        . "WHERE id_accion_funcionario_tipo='".$tipo_accion."'";
//    $resultado_accion_tipo=query($consulta_accion_tipo,$conexion);  
    
    
    
    $cont++;
}
if($cont==1)
{
    echo "NO SE ENCONTRARON FUNCIONARIOS CON VENCIMIENTO DE PERIODO DE VACACIONES PARA ESTA FECHA";
    exit;
}
?>

