<?php

//-------------------------------------------------
session_start();
//-------------------------------------------------
require_once "../config/rhexpress_config.php";
$conexion =  mysqli_connect( DB_HOST, DB_USUARIO, DB_CLAVE, $_SESSION['bd'] ) or die( 'No Hay Conexión con el Servidor de Mysql' );
        mysqli_query($conexion, 'SET CHARACTER SET utf8');
//-------------------------------------------------
$fecha1=$_POST["fecha_permisos"];
$fecha2=$_POST["fecha_permisos_fin"];
$ficha=$_POST["ficha"];
$fecha_inicial = date('Y-m-d', strtotime($fecha1));
$fecha_final= date('Y-m-d', strtotime($fecha2));
//-------------------------------------------------
$sql="SELECT COUNT(id) as dias_libres  FROM nomcalendarios_personal
 WHERE ficha='$ficha' AND fecha >= '$fecha_inicial' AND  fecha <= '$fecha_final' AND turno_id = '11'";

$dias = $conexion->query($sql)->fetch_assoc();
echo json_encode( array('dias_libres'=>$dias['dias_libres'])) ;


?>
  