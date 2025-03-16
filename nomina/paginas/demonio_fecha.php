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
$consulta = "SELECT * FROM Sheet1";
//$consulta = "SELECT ficha, tipnom, estado, suesal, codcargo FROM nompersonal";
$resultado = query($consulta,$conexion);

while($fetch = fetch_array($resultado))
{
	echo "<br/>";
	$g=explode("/",$fetch[G]);
	$fecha=$g[2]."-".$g[0]."-".$g[1];
	echo $consulta = "UPDATE Sheet1 SET G='$fecha' where B='$fetch[B]' ";
	$resultado1 = query($consulta,$conexion);
}

?>
