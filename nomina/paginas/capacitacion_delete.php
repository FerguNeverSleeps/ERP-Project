<?php
require_once '../../generalp.config.inc.php';
include ("../header.php");
include("../lib/common.php") ;
include("func_bd.php");	

session_start();
ob_start();

$codigo=$_REQUEST['codigo'];
$id=$_REQUEST['id'];
$consulta = "DELETE from nomcursos_personal WHERE id=".$id;

	@$result=sql_ejecutar($consulta);

	if($result)
	{

		echo "<script>alert('Colaborador eliminado al curso exitosamente');
		location.href='capacitacion_add.php?codigo=".$codigo."'</script>";
	}
	else
	{
		echo "<script>alert('No se puede eliminar al colaborador del curso');
		location.href='capacitacion_add.php?codigo=".$codigo."'</script>";			
	}
?>