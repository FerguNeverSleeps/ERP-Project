<?php
require_once '../../generalp.config.inc.php';
include ("../header.php");
include("../lib/common.php") ;
include("func_bd.php");	

session_start();
ob_start();


$cod_curso=$_REQUEST['codigo_curso'];
$capitulo_curso=$_REQUEST['capitulos_curso'];
$modulo_curso=$_REQUEST['modulo_curso'];
$area_curso=$_REQUEST['area_curso'];
$id_cliente_curso=$_REQUEST['cliente_curso'];
$estrategia_curso=$_REQUEST['estrategia_curso'];
$id_profesor_curso=$_REQUEST['instructor_curso'];
$duracion_curso=$_REQUEST['duracion_curso'];
$teorica_curso=$_REQUEST['teorica_curso'];
$practica_curso=$_REQUEST['practica_curso'];



$SQL="INSERT INTO menu_cursos(cod_curso, capitulo_curso, modulo_curso, area_curso, id_cliente_curso, estrategia_curso,
 id_profesor_curso, duracion_curso, duracion_teorica_curso, duracion_practica_curso)
VALUES ('".$cod_curso."','".$capitulo_curso."','".$modulo_curso."','".$area_curso."','".$id_cliente_curso."',
	'".$estrategia_curso."'	,'".$id_profesor_curso."',	".$duracion_curso.",'".$teorica_curso."','".$practica_curso."')";
echo $fecha_curso;
	@$result=sql_ejecutar($SQL);	
	if($result)
	{
		echo "<script>alert('Curso Agregado exitosamente');
		location.href='cursos_list.php'</script>";
	}
	else
	{
		echo "<script>alert('Hubo un error, verifique los datos nuevamente');
		location.href='menu_cursos.php'</script>";			
	}
?>