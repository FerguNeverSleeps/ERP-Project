<?php
	session_start();
	ob_start();
	$bd      = $_SESSION['bd'];
	require_once '../lib/common.php';
	$bd      = new bd($bd);
	$query="SELECT codigo
	FROM proyecto_cc
	WHERE codigo = '".$_GET['codigo']."'";
	$result=$bd->query($query);
	$fila = $result->fetch_array();
	echo count($fila[codigo]);
?>