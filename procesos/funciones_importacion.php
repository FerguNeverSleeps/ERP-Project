<?php
//Funciones para facilitar el proceso de importacion de archivos
function validar($campo)
{
	$res = (empty($campo)) ? '' : $campo;
	return $res;
}
function validar_posicion($posicion,$posiciones)
{
	$res = false;
	for ($i=0; $i < count($posiciones); $i++) { 
		if ($posicion == $posiciones[$i]['nomposicion_id']) {
			$res = true;
		}
	}
	return $res;
}
function sueldo_propuesto($sueldo)
{
	$res = (empty($sueldo)) ? 0.00 : $sueldo;
	return $res;
}
function validar_sueldo_anual($sueldo,$sualdo_anual)
{
	$res = (($sueldo*12) == $sueldo_anual) ? $sueldo_anual : ($sueldo*12);
	return $res;
}

function agregar_posicion($posicion)
{
	$sql="INSERT INTO nomposicion (nomposicion_id,sueldo_propuesto,sueldo_anual,partida,cargo_id) 
		VALUES ('".$posicion['nomposicion_id']."','".$posicion['sueldo_propuesto']."','".$posicion['sueldo_anual']."','".$posicion['partida']."','".$posicion['cargo_id']."')";		
	$res = $conexion->query($sql);
	if($res)
	{
		return TRUE;
	}else{
		return FALSE;
	}
	
}

?>