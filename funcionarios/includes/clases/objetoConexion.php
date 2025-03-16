<?php

/*esto ira en un archivo aparte*/		
	$host = "localhost";
	$usuario = "root";
	$clave = "";
	$baseDatos = "planilla_express";
	$puerto = "3306";

	$objConexion = new MySQL($host,$usuario,$clave,$baseDatos,$puerto);	
	if(!$objConexion)
	{	
		//echo "fallo";
		exit;
	}
	
?>
