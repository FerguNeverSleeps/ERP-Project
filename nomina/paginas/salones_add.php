<?php
require_once '../../generalp.config.inc.php';
include ("../header.php");
include("../lib/common.php") ;
include("func_bd.php");	

session_start();
ob_start();

$nom_salon=$_REQUEST['nombre_salon'];
$capacidad_salon=$_REQUEST['direccion_pob'];

$SQL="INSERT INTO salones(nom_salon, capacidad_salon) 
VALUES ('".$nom_salon."','".$capacidad_salon."')";
	@$result=sql_ejecutar($SQL);	
	if($result)
	{
		echo "<script>alert('Salón Agregado exitosamente');
		location.href='salones_list.php'</script>";
	}
	else
	{
		echo "<script>alert('Hubo un error, verifique los datos nuevamente');
		location.href='menu_salones.php'</script>";			
	}
?>