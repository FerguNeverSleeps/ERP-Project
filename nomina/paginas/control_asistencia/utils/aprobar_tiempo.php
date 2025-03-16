<?php
require_once "../config/db.php";
require_once "funciones_procesar.php";
//----------------------------------------------------------------
$conexion =  mysqli_connect( $params['host'], $params['user'], $params['password'], $params['dbname'] ) or
        die( 'Could not open connection to server' );
mysqli_query($conexion, 'SET CHARACTER SET utf8');
//----------------------------------------------------------------
$mes = $_GET['mes'];
$ficha = $_GET['aprob_ficha'];
$fecha = $_GET['aprob_fecha'];
$campo = $_GET['aprob_campo'];
//----------------------------------------------------------------
$conexion->query("SET AUTOCOMMIT=0");
$conexion->query("START TRANSACTION");
$sql = "UPDATE caa_resumen SET estado = 1,$campo = 1 WHERE ficha = '$ficha' AND fecha = '$fecha' AND $campo = 0";
$a1 = $conexion->query($sql);

if ($a1) {
    $conexion->query("COMMIT");
    echo $sql;
    header("location:../gestion_incidencias.php?ficha=".$ficha."&&fecha=".$fecha."&&msj=1&&mes=".$mes);
} else {        
    $conexion->query("ROLLBACK");
    echo $sql;
    header("location:../gestion_incidencias.php?ficha=".$ficha."&&fecha=".$fecha."&&msj=2&&mes=".$mes);
}
//----------------------------------------------------------------
?>