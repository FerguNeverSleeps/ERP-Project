<?php
require_once '../../generalp.config.inc.php';
include ("../header.php");
include("../lib/common.php") ;
include("func_bd.php");

session_start();
ob_start();

$codigo=$_REQUEST['codigo'];
$id=$_REQUEST['id'];
$personal=$_REQUEST['personal'];
$pagina=$_REQUEST['pagina'];
$consulta = "SELECT * from nomcursos_personal WHERE id_curso=".$codigo." AND id_personal=".$personal;

	@$result=sql_ejecutar($consulta);
	$num_rows=num_rows($result);
	/*echo $num_rows,"<BR>";
	echo $consulta,"<BR>";*/
	if($num_rows<1 AND $pagina!=2)
	{
			$SQL="INSERT INTO nomcursos_personal( id_personal, id_curso) 
			VALUES ('".$personal."','".$codigo."')";
			sql_ejecutar($SQL);
			//echo $SQL,"<BR>";
			echo "<script>alert('Colaborador Agregado al curso exitosamente');
			location.href='capacitacion_add.php?codigo=".$codigo."&id=".$id."'</script>";

	}
	elseif ($num_rows>=1 AND $pagina!=2) {
		echo "<script>alert('No se puede agregar al mismo colaborador');
		location.href='capacitacion_add.php?codigo=".$codigo."&id=".$id."'</script>";
	}


	if($pagina==2 AND $num_rows<1)
	{
		$SQL="INSERT INTO nomcursos_personal( id_personal, id_curso) 
		VALUES ('".$personal."','".$codigo."')";
		sql_ejecutar($SQL);
		echo "<script>alert('Colaborador Agregado al curso');
		location.href='capacitacion_add_curso.php?codigo=".$codigo."&id=".$personal."'</script>";
	}
	elseif($pagina==2 AND $num_rows>=1)
	{

		echo "<script>alert('No se agregó al colaborador');
		location.href='capacitacion_add_curso.php?codigo=".$codigo."&id=".$personal."'</script>";
	}
		

		
	
?>