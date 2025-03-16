<?php
require_once '../../generalp.config.inc.php';
include ("../header.php");
include("../lib/common.php") ;
include("func_bd.php");	

session_start();
ob_start();

$id=$_REQUEST['id'];
$modulo_plan_dist=$_REQUEST['modulo_plan_dist'];
$rol_plan_dist=$_REQUEST['rol_plan_dist'];
$practica_curso=$_REQUEST['practica_curso'];


$SQL="UPDATE menu_plan_dist SET modulo_plan_dist='".$modulo_plan_dist."',rol_plan_dist='".$rol_plan_dist."',practica_curso='".$practica_curso."'
WHERE id=".$id ;
//echo $SQL,"<BR>";

	@$result=sql_ejecutar($SQL);	
	if($result)
	{
		echo "<script>alert('Curso modificado exitosamente');
		location.href='cursos_list.php'</script>";
	}
	else
	{
		echo "<script>alert('Hubo un error, verifique los datos nuevamente');
		location.href='menu_cursos_edit.php'</script>";			
	}
?>