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
	$numpre = $_GET["numpre"];
	if (isset($numpre))
	{
		$sql1 = "DELETE FROM nomprestamos_cabecera WHERE numpre='$numpre'";
		$res1 =  query($sql1,$conexion);
		$sql2 = "DELETE FROM nomprestamos_detalles WHERE numpre='$numpre'";
		$res2 = query($sql2,$conexion);
		echo "<SCRIPT>alert('Se ha eliminado el prestamo')</SCRIPT>";
		echo "<SCRIPT>window.location='prestamos_list.php'</SCRIPT>";
	}
?>