<?php
	session_start();
	ob_start();
	$termino=$_SESSION['termino'];
	?>
	<?php
	include ("../header.php");
	include("../lib/common.php");
	include ("../paginas/func_bd.php");
	$conexion=conexion();
	$id = $_GET["id"];
	if (isset($id))
	{
		$sql1 = "DELETE 
	             FROM   marvin_detalle 
	             WHERE  id_encabezado='$id'";
		$res1 =  query($sql1,$conexion);
		$sql2 = "DELETE 
	             FROM   marvin_encabezado
	             WHERE  id='$id'";
		$res2 = query($sql2,$conexion);
		echo "<SCRIPT>alert('Se ha eliminado el archivo Marvin')</SCRIPT>";
		echo "<SCRIPT>window.location='proceso_marvin.php'</SCRIPT>";
	}
?>