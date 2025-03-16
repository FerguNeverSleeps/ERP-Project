<?php
$res1 = $conexion->query("SELECT * FROM caa_periodos WHERE ficha ='$ficha' ORDER BY ficha,fecha DESC") or die(mysqli_error($conexion));
$fila = mysqli_fetch_array($res1);

$registros = $conexion->query("SELECT * FROM caa_periodos WHERE ficha ='$ficha' ORDER BY ficha,fecha DESC") or die(mysqli_error($conexion));
$i=0;
while ($fila = mysqli_fetch_array($registros))
{
	$periodos[$i] = array('turno_id' => $fila['turno_id'],'descripcion' => $fila['descripcion'],'entrada' => $fila['entrada'],'tolerancia_entrada' => $fila['tolerancia_entrada'],'salida' => $fila['salida'],'tolerancia_salida' => $fila['tolerancia_salida'],'libre' => $fila['libre'],'tipo' => $fila['tipo']);
	$i++;
}
//---------------------------------------------
?>