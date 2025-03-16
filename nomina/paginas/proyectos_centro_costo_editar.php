<?php
	session_start();
	include("../lib/common.php") ;
	include("func_bd.php");
	$query           = "UPDATE proyecto_cc 
	SET descripcion='$_GET[descripcion]',
	codigo      = '$_GET[codigo]',
	cuenta_contable = '$_GET[cuenta_contable]'
	WHERE id = '$_GET[id]'";	
	$result=sql_ejecutar($query);				
?>
