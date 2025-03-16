<?php
require_once '../../generalp.config.inc.php';
include ("../header.php");
include("../lib/common.php") ;
include("func_bd.php");	

session_start();
ob_start();


$codigo=$_REQUEST['codigo'];



$SQL="DELETE FROM  salones WHERE id=".$codigo;
	@$result=sql_ejecutar($SQL);	
	if($result)
	{
		echo "<script>alert('Salón Eliminado exitosamente');
		location.href='cursos_list.php'</script>";
	}
	else
	{
		echo "<script>alert('Hubo un error, verifique los datos nuevamente');
		location.href='menu_cursos.php'</script>";			
	}
?>