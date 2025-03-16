<?php
require_once '../../generalp.config.inc.php';
include ("../header.php");
include("../lib/common.php") ;
include("func_bd.php");	

session_start();
ob_start();

$cod_plan=$_REQUEST['cod_plan'];
$modulo_plan=$_REQUEST['modulo_plan'];
$duracion_plan=$_REQUEST['duracion_plan'];
$id_plan_dist=$_REQUEST['id_plan_dist'];
$id_curso=$_REQUEST['id_curso'];

$SQL="INSERT INTO nomplanes(cod_plan, modulo_plan, duracion_plan, id_plan_dist,id_curso)
VALUES ('".$cod_plan."','".$modulo_plan."','".$duracion_plan."','".$id_plan_dist."','".$id_curso."')";
	@$result=sql_ejecutar($SQL);	
	if($result)
	{
		echo "<script>alert('Plan Agregado exitosamente');
		location.href='cursos_list.php'</script>";
	}
	else
	{
		echo "<script>alert('Hubo un error, verifique los datos nuevamente');
		location.href='menu_plan_add.php'</script>";			
	}
?>