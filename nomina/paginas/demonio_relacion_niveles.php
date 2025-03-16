<?php 
session_start();
ob_start();
$termino=$_SESSION['termino'];

include ("../header.php");
include("../lib/common.php");
include("func_bd.php");	

$conexion=conexion();

$consulta="select * from nomnivel2";
$resultado=query($consulta,$conexion);

while($fetch=fetch_array($resultado))
{
	$consulta="select codorg from nomnivel1 where concat(tipo_presup,'-',programa,'-',fuente_finan)=concat('$fetch[tipo_presup]','-','$fetch[programa]','-','$fetch[fuente_finan]')";
	$resultado2=query($consulta,$conexion);
	$fetch2=fetch_array($resultado2);



	$consulta="update nomnivel2 set gerencia='$fetch2[codorg]' where codorg='$fetch[codorg]'";
	$resultado3=query($consulta,$conexion);
	echo $fetch[cororg].'  '.$fetch[descrip].'<br>';
}


//////////////////////////////////////


$consulta="select * from nomnivel3";
$resultado=query($consulta,$conexion);

while($fetch=fetch_array($resultado))
{
	$consulta="select codorg from nomnivel2 where concat(tipo_presup,'-',programa,'-',fuente_finan,'-',sub_programa)=concat('$fetch[tipo_presup]','-','$fetch[programa]','-','$fetch[fuente_finan]','-','$fetch[sub_programa]')";
	$resultado2=query($consulta,$conexion);
	$fetch2=fetch_array($resultado2);



	$consulta="update nomnivel3 set gerencia='$fetch2[codorg]' where codorg='$fetch[codorg]'";
	$resultado3=query($consulta,$conexion);
	echo $fetch[cororg].'  '.$fetch[descrip].'<br>';
}


////////////////////////////////////////////

$consulta="select * from nomnivel4";
$resultado=query($consulta,$conexion);

while($fetch=fetch_array($resultado))
{
	$consulta="select codorg from nomnivel3 where concat(tipo_presup,'-',programa,'-',fuente_finan,'-',sub_programa,'-',actividad)=concat('$fetch[tipo_presup]','-','$fetch[programa]','-','$fetch[fuente_finan]','-','$fetch[sub_programa]','-','$fetch[actividad]')";
	$resultado2=query($consulta,$conexion);
	$fetch2=fetch_array($resultado2);



	$consulta="update nomnivel4 set gerencia='$fetch2[codorg]' where codorg='$fetch[codorg]'";
	$resultado3=query($consulta,$conexion);
	echo $fetch[cororg].'  '.$fetch[descrip].'<br>';
}




?>

