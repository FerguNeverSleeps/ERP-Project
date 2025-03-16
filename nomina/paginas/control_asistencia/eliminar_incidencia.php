<?php
error_reporting(E_ALL ^ E_DEPRECATED);
require_once "config/db.php";
require_once "libs/importar_archivos.php";

$mes = (isset($_GET['mes'])) ? $_GET['mes']:$_POST['mes'];
//---------------------------------------------------------------
$conexion =  mysqli_connect( $params['host'], $params['user'], $params['password'], $params['dbname'] ) or
        die( 'Could not open connection to server' );
mysqli_query($conexion, 'SET CHARACTER SET utf8');
//---------------------------------------------------------------
$id    = $_GET['id'];
$ficha = $_GET['ficha'];
$fecha = $_GET['fecha'];
//---------------------------------------------------------------
$sql = "DELETE FROM caa_incidencias_empleados WHERE id ='$id'";
if ($res = $conexion->query($sql)) {
	header("location:gestion_incidencias.php?ficha=".$ficha."&&fecha=".$fecha."&&msj=1&&mes=".$mes);
}else{
	header("location:gestion_incidencias.php?ficha=".$ficha."&&fecha=".$fecha."&&msj=2&&mes=".$mes);
}
?>