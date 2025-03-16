<?php 
//Cargamos las Variables
$fecha_inicio  = date('Y-m-d');
$fecha_fin     = date('Y-m-d');
$configuracion = 3;
// Inicio de la transacción
//cargando archivos necesarios para buscar turnos
//------------------------------------------------------------
//se carga el calendario de personal completo
$cal = $conexion->query("SELECT * FROM `nomcalendarios_personal`");
$i=0;
while ( $fila = mysqli_fetch_array($cal) )
{
	$calendario[$i] = array('ficha' => $fila['ficha'],'fecha' => $fila['fecha'],'turno_id' => $fila['turno_id']);
	$i++;
}
?>