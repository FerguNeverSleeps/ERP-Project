<?php
require_once "../config/db.php";
require_once "funciones_procesar.php";
//----------------------------------------------------------------
$conexion =  mysqli_connect( $params['host'], $params['user'], $params['password'], $params['dbname'] ) or
        die( 'Could not open connection to server' );
mysqli_query($conexion, 'SET CHARACTER SET utf8');
//----------------------------------------------------------------
$id = $_GET['id'];
$ficha   = $_GET['ficha'];
$fecha   = isset($_GET['fecha']) ? $_GET['fecha'] : date('d-m-Y');
$mes     = (isset($_GET['mes'])) ? $_GET['mes']:$_POST['mes'];
//----------------------------------------------------------------
$conexion->query("SET AUTOCOMMIT=0");
$conexion->query("START TRANSACTION");

if ($_GET['opc'] == 1) {
	$a1 = $conexion->query("UPDATE caa_justificacion SET estado = 1 WHERE id = '$id'");
}else{
	$a1 = $conexion->query("DELETE FROM caa_justificacion WHERE id = '$id'");
}
if ( $a1 ) {
    $conexion->query("COMMIT");
    header("location:../list_justificaciones.php?ficha=".$ficha."&fecha=".$fecha."&mes=".$mes."&msj=1");
} else {        
    $conexion->query("ROLLBACK");
    header("location:../list_justificaciones.php?ficha=".$ficha."&fecha=".$fecha."&mes=".$mes."&msj=2");
}
//----------------------------------------------------------------
?>