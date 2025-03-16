<?php
	session_start();
	include("../lib/common.php") ;
	include("func_bd.php");
	$query="INSERT INTO proyecto_cc 
	(codigo,descripcion,cuenta_contable)
	values ('$_GET[codigo]','$_GET[descripcion]','$_GET[cuenta_contable]')";
	$result=sql_ejecutar($query);
?>
