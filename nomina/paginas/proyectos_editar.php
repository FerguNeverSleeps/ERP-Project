<?php
	session_start();
	include("../lib/common.php") ;
	include("func_bd.php");
	$query           = "UPDATE proyectos 
	SET descripcionLarga='$_GET[descripcionLarga]',
	numProyecto      = '$_GET[numProyecto]',
	descripcionCorta = '$_GET[descripcionCorta]',
	idDispositivo = '$_GET[idDispositivo]',
	lat = '$_GET[lat]',
	lng = '$_GET[lng]'
	WHERE idProyecto = '$_GET[registro_id]'";	
	$result=sql_ejecutar($query);				
?>
