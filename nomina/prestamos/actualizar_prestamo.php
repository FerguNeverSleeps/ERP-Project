<?php
	$ficha  = $_GET["ficha"];
	$numcuo = $_GET["numcuo"];
	$numpre = $_GET["numpre"];
	$pre = $_GET["pre"];

	require_once '../lib/common.php';
	include ("../paginas/func_bd.php");
	$conexion=conexion();
	if($pre=='pendiente'){
		$consulta   = "UPDATE nomprestamos_detalles SET estadopre='Cancelada' 
		               WHERE ficha=$ficha AND numcuo=$numcuo";
		$resultado2 = query($consulta, $conexion);
		if($resultado2==TRUE){
			header("Location:prestamos_edit.php?numpre=$numpre");
		}
		else{
			echo "Ocurrio un error";
		}
	}
	elseif($pre=='cancelado'){
		$consulta   = "UPDATE nomprestamos_detalles SET estadopre='Pendiente' 
		               WHERE ficha=$ficha AND numcuo=$numcuo";
		$resultado2 = query($consulta, $conexion);
		if($resultado2==TRUE){
			header("Location:prestamos_edit.php?numpre=$numpre");
		}
		else{
			echo "Ocurrio un error";
		}
	}
?>
