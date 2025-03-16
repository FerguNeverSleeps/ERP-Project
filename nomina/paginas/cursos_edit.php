<?php
require_once '../../generalp.config.inc.php';
include ("../header.php");
include("../lib/common.php") ;
include("func_bd.php");	

session_start();
ob_start();

$id=$_REQUEST['id'];
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

$SQL="UPDATE menu_cursos SET cod_curso='".$cod_curso."',capitulo_curso='".$capitulo_curso."',modulo_curso='".$modulo_curso."',area_curso='".$area_curso."',
id_cliente_curso='".$id_cliente_curso."',estrategia_curso='".$estrategia_curso."',id_profesor_curso='".$id_profesor_curso."',duracion_curso='".$duracion_curso."',
duracion_teorica_curso='".$teorica_curso."',duracion_practica_curso='".$practica_curso."'
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