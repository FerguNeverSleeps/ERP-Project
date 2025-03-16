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
    $fecha_registro = "2017-01-01";
    $anio_registro = date("Y", strtotime($fecha_registro));
    $mes_registro = date("m", strtotime($fecha_registro));
    $dia_registro = date("d", strtotime($fecha_registro));

$consulta_personal="SELECT personal_id, cedula, apenom, tipnom, nomposicion_id, fecing, useruid, usuario_workflow "
        . "FROM nompersonal WHERE `estado` LIKE  '%Licencia%'"
        . "AND fin_periodo = '{$fecha_registro}'";

$resultado_personal=mysqli_query($conexion,$consulta_personal);

$cont=1;
while($fila=mysqli_fetch_array($resultado_personal))
{          
    $personal_id=$fila['personal_id'];
    $cedula=$fila['cedula'];
    $apenom=$fila['apenom'];
    $tipnom=$fila['tipnom'];
    $nomposicion_id=$fila['nomposicion_id'];
    $fecing=$fila['fecing'];
    $usr_uid=$fila['useruid'];
    $cod_user=$fila['usuario_workflow'];
    $codnivel1=$fila['codnivel1'];
    $codnivel2=$fila['codnivel2'];
    $codnivel3=$fila['codnivel3'];
    $codcargo=$fila['codcargo'];
    $estado=$fila['estado'];
    $sueldopro=$fila['sueldopro'];
    $suesal=$fila['suesal'];
    $gastos_representacion=$fila['gastos_representacion'];
    $otros=$fila['otros'];
    $dieta=$fila['dieta'];
    $combustible=$fila['combustible'];
    $inicio_periodo=$fila['inicio_periodo'];
    $fin_periodo=$fila['fin_periodo'];
    $num_decreto=$fila['num_decreto'];
    $fecha_decreto=$fila['fecha_decreto'];
    $num_resolucion=$fila['num_resolucion'];
    $fecha_resolucion=$fila['fecha_resolucion'];
    $nomfuncion_id=$fila['nomfuncion_id'];
        
    
    echo "ID: "; echo $personal_id;
    echo " - CEDULA: "; echo $cedula;
    echo " - APELLIDOS Y NOMBRE: "; echo $apenom;
    echo " - POSICION: "; echo $nomposicion_id;
    echo " - FECHA INGRESO: "; echo $fecing;       
    echo "<br>";
    echo "<br>";
    
    
    //APLICAR REINTEGRO A FUNCIONARIO EN LICENCIA
    
    $descripcion="REINTEGRO POR CULMINACIÓN DE PERIODO DE LICENCIA (VENCIMIENTO) GENERADO AUTOMATICAMENTE POR EL SISTEMA - FECHA: ". $fecha_registro;     
    
    $tipo=58;
    $estatus=1;
    $fecha_creacion = date("Y-m-d");       
    
    //INSERCIÓN EXPEDIENTE
    $consulta_expediente="INSERT INTO expediente
                (cod_expediente_det, cedula, descripcion, fecha, fecha_inicio, fecha_fin,fecha_notificacion, fecha_decreto_ingreso_anterior,
                fecha_decreto_ingreso_nuevo, gerencia_anterior, gerencia_nueva, departamento_anterior, departamento_nuevo, seccion_anterior, seccion_nueva,
                posicion_anterior, posicion_nueva,cod_cargo_anterior, cod_cargo_nuevo, funcion_anterior, funcion_nueva, planilla_anterior, planilla_nueva,
                situacion_anterior, situacion_nueva,
                tipo, fecha_creacion, usuario_creacion, fecha_aprobacion, usuario_aprobacion)
                VALUES  
                ('','{$cedula}','{$descripcion}', '{$fecha_reintegro}','{$fecha_inicio}','{$fecha_fin}','{$fecha_notificacion}',"
                . "'{$fecha_decreto_ingreso_anterior}','{$fecha_decreto_ingreso_nuevo}','{$gerencia_anterior}','{$gerencia_nueva}',"
                . "'{$departamento_anterior}','{$departamento_nuevo}','{$seccion_anterior}','{$seccion_nueva}',"
                . "'{$posicion_anterior}','{$posicion_nueva}','{$cod_cargo_anterior}','{$cargo_nuevo}',"
                . "'{$funcion_anterior}','{$funcion_nueva}','{$planilla_anterior}','{$planilla_nueva}',"
                . "'{$numero_decreto_anterior}','{$numero_decreto_nuevo}','{$situacion_anterior}','{$situacion_nueva}',"
                . "'{$tipo}','{$fecha_creacion}','{$usuario}','{$fecha_creacion}','{$usuario}')";    
    $resultado_expediente=mysqli_query($conexion,$consulta_expediente);
    
//        echo "CONSULTA EXPEDIENTE: "; echo $consulta_expediente; 
//        echo "<br>";
//        echo "<br>"; 
    
    //ACTUALIZACION PERSONAL
    $consulta_personal = "UPDATE nompersonal SET estado = '{$situacion_nueva}',"                       
                        . "inicio_periodo = '{$fecha_inicio}',fin_periodo = '{$fecha_fin}'"
                        . "WHERE cedula='{$cedula}'";    
    $resultado_personal=mysqli_query($conexion,$consulta_personal); 
    
    //ACTUALIZACION POSICION
    $estado = 1;
    $consulta_posicion = "UPDATE nomposicion SET estado = '{$estado}'"
               . "WHERE nomposicion_id='{$nomposicion_id}'";
    //echo $consulta2;
    $resultado_posicion=mysqli_query($conexion,$consulta_posicion);  
    
    //ACCION FUNCIONARIO    
    $consulta_correlativo = "SELECT correlativo FROM accion_funcionario_tipo "
                    . "WHERE id_accion_funcionario_tipo = '$tipo_accion'";
    $resultado_correlativo=mysqli_query($conexion,$consulta_correlativo);
    $fetch_correlativo=mysqli_fetch_array($resultado_correlativo);
    $correlativo = $fetch_correlativo["correlativo"];
    $correlativo = $correlativo+1;

   $consulta_accion="INSERT INTO accion_funcionario
            (id_accion_funcionario, tipo_accion, numero_accion,id_funcionario)
            VALUES  
            ('','{$tipo_accion}','{$correlativo}', '{$id_empleado}')";
    $resultado_accion=mysqli_query($conexion,$consulta_accion);           

    $consulta_accion_tipo ="UPDATE accion_funcionario_tipo SET correlativo='".$correlativo."' "
                        . "WHERE id_accion_funcionario_tipo='".$tipo_accion."'";
    $resultado_accion_tipo=mysqli_query($conexion,$consulta_accion_tipo);  
    
    //APLICAR BAJA POR RETORNO DE FUNCIONARIO TITULAR A FUNCIONARIO QUE OCUPA POSICION ACTUALMENTE
    
    $consulta_personal2="SELECT personal_id, cedula, apenom, tipnom, nomposicion_id, fecing, useruid, usuario_workflow "
        . "FROM nompersonal WHERE `estado` LIKE  '%REGULAR%' OR `estado` LIKE  '%INTERINO%' "
        . "AND nomposicion_id <= '{$nomposicion_id}'";

    $resultado_personal2=mysqli_query($conexion,$consulta_personal2);

    $cont=1;
    while($fila2=mysqli_fetch_array($resultado_personal2))
    {
        $personal_id2=$fila2['personal_id'];
        $cedula2=$fila2['cedula'];
        $apenom2=$fila2['apenom'];
        $tipnom2=$fila2['tipnom'];
        $nomposicion_id2=$fila2['nomposicion_id'];
        $fecing2=$fila2['fecing'];
        $usr_uid2=$fila2['useruid'];
        $cod_user2=$fila2['usuario_workflow'];
        $codnivel12=$fila2['codnivel1'];
        $codnivel22=$fila2['codnivel2'];
        $codnivel32=$fila2['codnivel3'];
        $codcargo2=$fila2['codcargo'];
        $estado2=$fila2['estado'];
        $sueldopro2=$fila2['sueldopro'];
        $suesal2=$fila2['suesal'];
        $gastos_representacion2=$fila2['gastos_representacion'];
        $otros2=$fila2['otros'];
        $dieta2=$fila2['dieta'];
        $combustible2=$fila2['combustible'];
        $inicio_periodo2=$fila2['inicio_periodo'];
        $fin_periodo2=$fila2['fin_periodo'];
        $num_decreto2=$fila2['num_decreto'];
        $fecha_decreto2=$fila2['fecha_decreto'];
        $num_resolucion2=$fila2['num_resolucion'];
        $fecha_resolucion2=$fila2['fecha_resolucion'];
        $nomfuncion_id2=$fila2['nomfuncion_id'];
        
        $descripcion="BAJA POR RETORNO DE PERIODO DE LICENCIA (VENCIMIENTO) DE FUNCIONARIO TITULAR GENERADA AUTOMATICAMENTE POR EL SISTEMA - FECHA: ". $fecha_registro;     
    
        $tipo=57;
        $subtipo=103;
        $estatus=1;
        $fecha_creacion = date("Y-m-d");     
        
        //INSERCIÓN EXPEDIENTE
        $consulta_expediente2="INSERT INTO expediente
                    (cod_expediente_det, cedula, descripcion, fecha,fecha_notificacion, numero_resolucion, fecha_resolucion, numero_edicto, 
                    fecha_edicto, tipo, subtipo,fecha_creacion, usuario_creacion)
                    VALUES  
                    ('','{$cedula}','{$_POST['descripcion']}', '{$fecha_baja}','{$fecha_notificacion}','{$_POST['numero_resuelto']}',"
                    . "'{$fecha_resuelto}', '{$_POST['numero_edicto']}','{$fecha_edicto}',"
                    . "'{$_POST['tipo_registro']}','{$_POST['tipo_tiporegistro']}','{$fecha_creacion}','{$usuario}')";    
//            echo $consulta;
        $resultado_expediente2=mysqli_query($conexion,$consulta_expediente2);
        
        $tipo_accion=41;
        $fecha_baja=$fetch_expediente['fecha_resolucion'];
        $numero_baja=$fetch_expediente['numero_resolucion'];

        $fecha = date('Y-m-d');
        $estado = $expediente_tipo. " ". $expediente_subtipo;

         //ACTUALIZACION PERSONAL
        $consulta_personal = "UPDATE nompersonal SET num_resolucion_baja = '{$numero_baja}', fecha_resolucion_baja = '{$fecha_baja}',estado = '{$estado}'"
                   . "WHERE cedula='{$cedula}'";
        //echo $consulta2;
        $resultado_personal=mysqli_query($conexion,$consulta_personal); 

        //ACTUALIZACION POSICION EMPLEADO    
        $consulta_posicionempleado = "DELETE FROM  posicionempleado "
                   . "WHERE IdEmpleado='$personal_id'";
        //echo $consulta3;   
        $resultado_posicionempleado=mysqli_query($conexion,$consulta_posicionempleado);  

        //ACCION FUNCIONARIO    
        $consulta_correlativo = "SELECT correlativo FROM accion_funcionario_tipo "
                        . "WHERE id_accion_funcionario_tipo = '$tipo_accion'";
        $resultado_correlativo=mysqli_query($conexion,$consulta_correlativo);
        $fetch_correlativo=fetch_array($resultado_correlativo);
        $correlativo = $fetch_correlativo["correlativo"];
        $correlativo = $correlativo+1;

       $consulta_accion="INSERT INTO accion_funcionario
                (id_accion_funcionario, tipo_accion, numero_accion,id_funcionario)
                VALUES  
                ('','{$tipo_accion}','{$correlativo}', '{$id_empleado}')";
        $resultado_accion=mysqli_query($conexion,$consulta_accion);           

        $consulta_accion_tipo ="UPDATE accion_funcionario_tipo SET correlativo='".$correlativo."' "
                            . "WHERE id_accion_funcionario_tipo='".$tipo_accion."'";
        $resultado_accion_tipo=mysqli_query($conexion,$consulta_accion_tipo);  
        
    }          
    
    $cont++;
}
if($cont==1)
{
    echo "NO SE ENCONTRARON FUNCIONARIOS CON VENCIMIENTO DE PERIODO DE LICENCIA PARA ESTA FECHA";
    exit;
}
?>

