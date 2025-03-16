<?php
	session_start();
	ob_start();
	$bd      = $_SESSION['bd'];
	require_once '../lib/common.php';
	$bd      = new bd($bd);
	$query="SELECT numProyecto
	FROM proyectos
	WHERE numProyecto = '".$_GET['numProyecto']."'";
	$result=$bd->query($query);
	$fila = $result->fetch_array();
	echo count($fila[numProyecto]);
?>