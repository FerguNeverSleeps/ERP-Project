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

$consulta_incapacidad="SELECT * FROM dias_incapacidad";

$resultado_incapacidad=mysqli_query($conexion,$consulta_incapacidad);

$cont=1;
while($fila=mysqli_fetch_array($resultado_incapacidad))
{

    $id=$fila['id'];
    $dias=$fila['dias'];
    $horas=$fila['horas'];
    $minutos=$fila['minutos'];
    //ACTUALIZACION INCAPACIDAD
    $actualizacion_incapacidad = "UPDATE dias_incapacidad SET dias_restante = '{$dias}', "
                        . "horas_restante = '{$horas}', minutos_restante = '{$minutos}'"
                        . " WHERE id='{$id}' AND tipo_justificacion='3'";
    
    
    $resultado_actualizacion_incapacidad=mysqli_query($conexion,$actualizacion_incapacidad);

}

?>