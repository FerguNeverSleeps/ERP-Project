<?php 
//se cargan todos los turnos registrados
$tur = $conexion->query("SELECT * FROM `nomturnos`");
$i=0;
while ( $fila = mysqli_fetch_array($tur) )
{
	$t_extra = date("H:i:s",strtotime("00:00:00")+strtotime($fila['salida'])+strtotime("01:00:00"));
	$turnos[$i] = array('turno_id' => $fila['turno_id'],'descripcion' => $fila['descripcion'],'entrada' => $fila['entrada'],'tolerancia_entrada' => $fila['tolerancia_entrada'],'salida' => $fila['salida'],'tolerancia_salida' => $fila['tolerancia_salida'],'tolerancia_extra' => $t_extra,'libre' => $fila['libre'],'tipo' => $fila['tipo']);
	$i++;
}
?>