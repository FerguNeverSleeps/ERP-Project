<?php
require_once "../config/db.php";
require_once "../utils/funciones_procesar.php";
//------------------------------------------------------------
//se carga el calendario de personal completo
$cal = $conexion->createQueryBuilder()->select('ficha', 'fecha', 'turno_id')->from('nomcalendarios_personal')->execute();
$i=0;
while ($fila = $cal->fetch())
{
	$calendario[$i] = array('ficha' => $fila['ficha'],'fecha' => $fila['fecha'],'turno_id' => $fila['turno_id']);
	$i++;
}
//------------------------------------------------------------
//se cargan todos los turnos registrados
$tur = $conexion->createQueryBuilder()
			  	->select('turno_id', 'descripcion','entrada', 'tolerancia_entrada', 'salida', 'tolerancia_salida', 'libre', 'tipo')
			    ->from('nomturnos')->execute();
$i=0;
while ($fila = $tur->fetch())
{
	$turnos[$i] = array('turno_id' => $fila['turno_id'],'descripcion' => $fila['descripcion'],'entrada' => $fila['entrada'],'tolerancia_entrada' => $fila['tolerancia_entrada'],'salida' => $fila['salida'],'tolerancia_salida' => $fila['tolerancia_salida'],'libre' => $fila['libre'],'tipo' => $fila['tipo']);
	$i++;
}
//------------------------------------------------------------
$ficha    = $_POST['ficha'];
$fecha    = date("Y-m-d", strtotime($_POST['fecha']));
//------------------------------------------------------------
$turno    = get_turno(get_turno_id($ficha,$fecha,$calendario),$turnos);
if ($turno['tipo'] == 1) {
	$tipo = "Diurna";
}
if ($turno['tipo'] == 2) {
	$tipo = "Nocturna";
}
if ($turno['tipo'] == 3) {
	$tipo = "Mixta";
}
//------------------------------------------------------------
$datos = array(
    'fecha' => $fecha,
    'turno_id' => $turno['turno_id'],
    'turno' => $turno['descripcion']." - ".$turno['entrada']." - ".$turno['salida'],
    'tipo' => $tipo
);
//------------------------------------------------------------
echo json_encode($datos);
?>