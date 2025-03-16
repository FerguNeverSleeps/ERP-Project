<?php
	session_start();
	include("../lib/common.php") ;
	include("func_bd.php");
	$query="INSERT INTO proyectos 
	(numProyecto,descripcionCorta,descripcionLarga, idDispositivo, lat, lng)
	values ('$_GET[numProyecto]','$_GET[descripcionCorta]','$_GET[descripcionLarga]','$_GET[idDispositivo]','$_GET[lat]','$_GET[lng]')";
	$result=sql_ejecutar($query);
?>
