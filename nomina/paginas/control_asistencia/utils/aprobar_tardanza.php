<?php
require_once "../config/db.php";
require_once "funciones_procesar.php";
//----------------------------------------------------------------
$conexion =  mysqli_connect( $params['host'], $params['user'], $params['password'], $params['dbname'] ) or
        die( 'Could not open connection to server' );
mysqli_query($conexion, 'SET CHARACTER SET utf8');
//----------------------------------------------------------------
$tipo = $_GET['tipo'];
$fecha = $_GET['fecha'];
$ficha = $_GET['ficha'];
//----------------------------------------------------------------
$conexion->query("SET AUTOCOMMIT=0");
$conexion->query("START TRANSACTION");

$a1 = $conexion->query("UPDATE caa_resumen SET estado = 1,aprob_tar = 1 WHERE ficha = '$ficha' AND fecha = '$fecha' AND aprob_tar = 0");

if ($a1) {
    $conexion->query("COMMIT");
    header("location:../gestion_incidencias.php?ficha=".$ficha."&&fecha=".$fecha."&&msj=1");
} else {        
    $conexion->query("ROLLBACK");
    header("location:../gestion_incidencias.php?ficha=".$ficha."&&fecha=".$fecha."&&msj=2");
}
//----------------------------------------------------------------
?>