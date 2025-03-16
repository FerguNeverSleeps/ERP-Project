<?php
require_once "config/db.php";
require_once "utils/funciones_procesar.php";
//---------------------------------------------------------------
$conexion =  mysqli_connect( $params['host'], $params['user'], $params['password'], $params['dbname'] ) or
        die( 'Could not open connection to server' );
mysqli_query($conexion, 'SET CHARACTER SET utf8');

$mes = (isset($_GET['mes'])) ? $_GET['mes']:$_POST['mes'];

$ficha   = $_POST['ficha'];
$fecha = $_POST['fec_inc'];
$incidencia = $_POST['incidencia'];
$aplica     = $_POST['aplica'];

if ( ( $_POST['incidencia'] != "#" ) && ( $_POST['aplica'] != "#" ) && ( !validar_incidencia($ficha,$fecha,$incidencia,$conexion) ) )
{
	$conexion->query("SET AUTOCOMMIT=0");
	$conexion->query("START TRANSACTION");
	$res1 = $conexion->query( "DELETE a FROM caa_incidencias_empleados a INNER JOIN caa_incidencias b WHERE a.id_incidencia = b.id AND a.ficha = '$ficha' AND a.fecha = '$fecha' AND b.tipo = 2" );
	$res2 = $conexion->query( "INSERT INTO caa_incidencias_empleados ( ficha, fecha, id_incidencia, aplica ) VALUES ( '".$ficha."', '".$fecha."', '".$incidencia."', '".$aplica."' )" );

	if ( $res1 and $res2 ) {
	    $conexion->query("COMMIT");
	    $msj = 1;
	} else {        
	    $conexion->query("ROLLBACK");
	    $msj = 2;
	}
	header( "location:gestion_incidencias.php?ficha=".$ficha."&fecha=".$fecha."&msj=".$msj."&mes=".$mes );
}else{
	header( "location:gestion_incidencias.php?ficha=".$ficha."&fecha=".$fecha."&msj=3&mes=".$mes );
}
?>