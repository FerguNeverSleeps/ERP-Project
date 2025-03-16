<?php
require_once '../../generalp.config.inc.php';
include ("../header.php");
include("../lib/common.php") ;
include("func_bd.php");	

session_start();
ob_start();


$cod_curso=$_REQUEST['codigo_curso'];



$SQL="DELETE FROM  menu_cursos WHERE id=".$cod_curso;
	@$result=sql_ejecutar($SQL);	
	if($result)
	{
		echo "<script>alert('Curso eliminado exitosamente');
		location.href='cursos_list.php'</script>";
	}
	else
	{
		echo "<script>alert('Hubo un error, verifique los datos nuevamente');
		location.href='menu_cursos.php'</script>";			
	}
?>