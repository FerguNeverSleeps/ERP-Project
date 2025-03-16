<?php
	session_start();
	include("../lib/common.php") ;
	include("func_bd.php");
	$query           = "UPDATE implemento 
	SET nombre='$_GET[nombre]',
	descripcion      = '$_GET[descripcion]',
	vencimiento = '$_GET[vencimiento]'
	WHERE id_implemento = '$_GET[registro_id]'";	
	$result=sql_ejecutar($query);				
?>