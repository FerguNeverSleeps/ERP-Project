<?php
require_once "../../config/db.php";
require_once "../../utils/funciones_procesar.php";
//------------------------------------------------------------
$turno_id = $_POST['turno_id'];
$ficha    = $_POST['ficha'];
$n_turno  = $_POST['n_turno'];
//---------------------------------------------------------------
$conexion =  mysqli_connect( $params['host'], $params['user'], $params['password'], $params['dbname'] ) or
        die( 'Could not open connection to server' );
mysqli_query($conexion, 'SET CHARACTER SET utf8');

//---------------------------------------------------------------
$res = $conexion->query("SELECT * FROM nomturnos WHERE turno_id = '$n_turno'") or die(mysqli_error($conexion));
$turno = mysqli_fetch_array($res);
//---------------------------------------------------------------
$datos = array(
	'turno_id' =>$turno['turno_id'],
	'entrada' =>$turno['entrada'],
	'salida' =>$turno['salida'],
	'turno'  =>$turno['descripcion']." - ".$turno['entrada']." - ".$turno['salida'],
);
echo json_encode($datos);
?>