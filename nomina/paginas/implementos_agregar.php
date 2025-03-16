<?php
	session_start();
	include("../lib/common.php") ;
	include("func_bd.php");
	$query="INSERT INTO implemento 
	(id_implemento,nombre,descripcion, vencimiento)
	values ('','$_GET[nombre]','$_GET[descripcion]','$_GET[vencimiento]')";
	$result=sql_ejecutar($query);
?>