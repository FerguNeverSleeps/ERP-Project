<?php
require_once "../../config/db.php";
require_once "../../utils/funciones_procesar.php";
//------------------------------------------------------------
$ficha = $_POST['ficha'];
$fecha = date_format( date_create($_POST['fecha']), 'Y-m-d');
//---------------------------------------------------------------
$conexion =  mysqli_connect( $params['host'], $params['user'], $params['password'], $params['dbname'] ) or
        die( 'Could not open connection to server' );
mysqli_query($conexion, 'SET CHARACTER SET utf8');
//---------------------------------------------------------------
$sql = "SELECT a.*,b.descripcion,b.acronimo FROM caa_incidencias_empleados a LEFT JOIN caa_incidencias b ON a.id_incidencia=b.id WHERE ficha='$ficha' AND fecha='$fecha'";
$res = $conexion->query( $sql ) or die(mysqli_error($conexion));
//---------------------------------------------------------------
$d[0] = array(
	'id' => 0,
	'ficha' => 0,
	'descripcion' => "Seleccione",
);
$i=1;
if (mysqli_num_rows($res) != 0) {
	while ( $row = mysqli_fetch_array($res) ) {
		$d[$i] = array(
			'id' => $row['id'],
			'ficha' => $row['ficha'],
			'descripcion' => $row['descripcion']." - ".$row['acronimo'],
		);
		$i++;
	}
}else{
	$d[$i] = array(
		'id' => 0,
		'ficha' => 0,
		'descripcion' => "No hay incidencias para la fecha seleccionada",
	);
}
$json['respuesta'] = $d;
echo json_encode($json);
?>