<?php
include('../../../generalp.config.inc.php');
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

//CAMBIAR PARA EJECUCION EN CADA TABLA MIGRADA DE TIEMPO COMPENSATORIO
$tabla = 'rrhh_tiempo_compensatorio1';

$consulta_vacaciones1="SELECT * FROM $tabla";  
//echo $consulta_vacaciones1;
$resultado_vacaciones1=mysqli_query($conexion,$consulta_vacaciones1);
echo '<table border="1">';
echo '<tbody>';
echo '<tr>';
echo '<td>NUM</td><td>CEDULA</td><td>NOMBRE Y APELLIDO</td><td>FECHA INICIO</td><td>CANTIDAD</td>';
echo '</tr>';
$cont=1;
while($fila1=mysqli_fetch_array($resultado_vacaciones1))
{          
    
    $cedula=$fila1['cedula'];
    $nombre_apellido=$fila1['nombre_apellido'];
    $fecha_inicio=$fila1['fecha_inicio'];
    $tiempo=$fila1['tiempo'];
    
    $consultap="SELECT useruid, usuario_workflow FROM nompersonal WHERE cedula='$cedula'";               
    $resultadop=mysqli_query($conexion,$consultap);
    $fetchCon=mysqli_fetch_array($resultadop);
    $user_uid=$fetchCon['useruid'];
    $cod_user=$fetchCon['nomposicion_id'];
 
    $tipo=12;
    $estatus=1;
    $fecha_creacion = date("Y-m-d");     
    
     $descripcion="REGISTRO DE TIEMPO COMPENSATORIO MIGRADO EXCEL RRHH - FECHA: ". $fecha_creacion;     

    //INSERCIÓN EXPEDIENTE
    $consulta_expediente="INSERT INTO expediente
                        (cod_expediente_det, cedula, descripcion, fecha, fecha_efectividad, fecha_aprobado, fecha_enterado, 
                        horas, duracion, tipo, estatus, fecha_creacion, usuario_creacion, fecha_aprobacion, usuario_aprobacion)
                        VALUES  
                        ('','{$cedula}','{$descripcion}', '{$fecha_creacion}','{$fecha_creacion}','{$fecha_creacion}','{$fecha_creacion}', "
                        . "'{$tiempo}','{$tiempo}','{$tipo}','{$estatus}','{$fecha_creacion}','{$usuario}','{$fecha_creacion}','{$usuario}')";
    
    $insert_expediente=mysqli_query($conexion,$consulta_expediente);
//        echo "CONSULTA EXPEDIENTE: "; echo $consulta_expediente; 
//        echo "<br>";
//        echo "<br>";

    $ultimo_id_expediente = mysqli_insert_id($conexion);
    
    $tipo_justificacion=3;
    //INSERCION DIAS INCAPACIDAD    
    $consulta_incapacidad="INSERT INTO dias_incapacidad
            (id, cod_user, tipo_justificacion, fecha, tiempo, observacion, documento, st, usr_uid,
            dias, horas, minutos, idparent, cedula)
            VALUES  
            ('','{$cod_user}','{$tipo_justificacion}', '{$fecha_creacion}', '{$tiempo}', '{$descripcion}', '', '', '{$user_uid}',"
            . "'', '{$tiempo}','', '{$ultimo_id_expediente}', '{$cedula}')";
    $insert_incapacidad=mysqli_query($conexion,$consulta_incapacidad);
//        echo "CONSULTA DIAS INCAPACIDAD: "; echo $consulta_incapacidad; 
//        echo "<br>";
//        echo "<br>";
        
    echo '<tr>';
    echo "<td>".$cont."</td>";
    echo "<td>".$cedula."</td>";
    echo "<td>".$nombre_apellido."</td>";
    echo "<td>".$fecha_inicio."</td>";
    echo "<td>".$tiempo."</td>";
    echo '</tr>';   
       
    $cont++;
   
}

echo '</tbody>';
echo '</table>';

?>

