<?php
require_once '../../generalp.config.inc.php';
include ("../header.php");
include("../lib/common.php") ;
include("func_bd.php");	

session_start();
ob_start();

$rol_plan_dist=$_REQUEST['rol_plan_dist'];
$modulo_plan_dist=$_REQUEST['modulo_plan_dist'];
$duracion_plan_dist=$_REQUEST['duracion_plan_dist'];

$SQL="INSERT INTO menu_plan_dist(rol_plan_dist, modulo_plan_dist, duracion_plan_dist)
VALUES ('".$rol_plan_dist."','".$modulo_plan_dist."','".$duracion_plan_dist."')";
	@$result=sql_ejecutar($SQL);	
	if($result)
	{
		echo "<script>alert('Plan de distribución Agregado exitosamente');
		location.href='plan_dist_list.php'</script>";
	}
	else
	{
		echo "<script>alert('Hubo un error, verifique los datos nuevamente');
		location.href='menu_plan_dist.php'</script>";			
	}
?>