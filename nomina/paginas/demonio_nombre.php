<?php 
session_start();
ob_start();
$termino=$_SESSION['termino'];
?>

<?
include("../header.php");
include("../lib/common.php");
include("func_bd.php");


$conexion=conexion();
$consulta = "SELECT * FROM nompersonal";
//$consulta = "SELECT ficha, tipnom, estado, suesal, codcargo FROM nompersonal";
$resultado = query($consulta,$conexion);

while($fetch = fetch_array($resultado))
{
	echo "<br/>";
	$g=explode(" ",$fetch[apenom]);
	if(count($g)==1)
	{
		$nom=$g[0];	
		$ape=$g[1];
	}
	elseif(count($g)==2)
	{
		$nom=$g[0];	
		$ape=$g[1]." ".$g[2];
	}
	elseif(count($g)==3)
	{
		$nom=$g[0]." ".$g[1];	
		$ape=$g[3]." ".$g[4];
	}
	
	echo $consulta = "UPDATE nompersonal SET nombres='$nom', apellidos='$ape' where cedula='$fetch[cedula]' ";
	$resultado1 = query($consulta,$conexion);
}

?>