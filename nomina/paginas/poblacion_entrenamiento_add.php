<?php
require_once '../../generalp.config.inc.php';
include ("../header.php");
include("../lib/common.php") ;
include("func_bd.php");	

session_start();
ob_start();

$codigo_pob=$_REQUEST['codigo_pob'];
$direccion_pob=$_REQUEST['direccion_pob'];
$entrenamiento_pob=$_REQUEST['entrenamiento_pob'];
$roles_pob=$_REQUEST['roles_pob'];
$planes_pob=$_REQUEST['planes_pob'];




$SQL="INSERT INTO poblacion_entrenamiento(codigo_pob, direccion_pob, entrenamiento_pob, roles_pob,planes_pob) 
VALUES ('".$codigo_pob."','".$direccion_pob."','".$entrenamiento_pob."','".$roles_pob."','".$planes_pob."')";
	@$result=sql_ejecutar($SQL);	
	if($result)
	{
		echo "<script>alert('Curso Agregado exitosamente');
		location.href='poblacion_list.php'</script>";
	}
	else
	{
		echo "<script>alert('Hubo un error, verifique los datos nuevamente');
		location.href='menu_poblacion.php'</script>";			
	}
?>