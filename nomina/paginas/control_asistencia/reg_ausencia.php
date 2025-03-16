<?php
require_once "config/db.php";
require_once "utils/funciones_procesar.php";
//------------------------------------------------------------
$conexion =  mysqli_connect( $params['host'], $params['user'], $params['password'], $params['dbname'] ) or
        die( 'Could not open connection to server' );
mysqli_query($conexion, 'SET CHARACTER SET utf8');
//------------------------------------------------------------
$ficha = $_POST['ficha'];
$fecha = date("Y-m-d", strtotime($_POST['fecha']));
//------------------------------------------------------------
//se carga el calendario de personal completo
$cal = $conexion->query("SELECT * FROM nomcalendarios_personal") or die(mysqli_error($conexion));
$i=0;
while ($fila = mysqli_fetch_array($cal))
{
	$calendario[$i] = array('ficha' => $fila['ficha'],'fecha' => $fila['fecha'],'turno_id' => $fila['turno_id']);
	$i++;
}
//------------------------------------------------------------
//se cargan todos los turnos registrados
$tur = $conexion->query("SELECT * FROM nomturnos") or die(mysqli_error($conexion));
$i=0;
while ($fila = mysqli_fetch_array($tur))
{
	$turnos[$i] = array('turno_id' => $fila['turno_id'],'descripcion' => $fila['descripcion'],'entrada' => $fila['entrada'],'tolerancia_entrada' => $fila['tolerancia_entrada'],'salida' => $fila['salida'],'tolerancia_salida' => $fila['tolerancia_salida'],'libre' => $fila['libre'],'tipo' => $fila['tipo']);
	$i++;
}
//------------------------------------------------------------
$turno = get_turno(get_turno_id($ficha,$fecha,$calendario),$turnos);
if ($turno['tipo'] == 1) {
	$jornada = "Diurna";
}elseif ($turno['tipo'] == 2) {
	$jornada = "Nocturna";
}elseif ($turno['tipo'] == 3) {
	$jornada = "Mixta";
}
if (!validar_registro($ficha,$fecha,$conexion))
{
	$sql = "INSERT INTO `caa_resumen`(`ficha`, `fecha`, `jornada`, `ausencia`, `turno_id`) VALUES ( '$ficha', '$fecha', '".$jornada."', '1','".$turno['turno_id']."')";
	echo $sql;
	$res = $conexion->query($sql) or die(mysqli_error($conexion));
	if ($res) {
		header("location:resumen_asistencias.php?ficha=".$ficha."&&msj=1");
	}else{
		header("location:resumen_asistencias.php?ficha=".$ficha."&&msj=2");
	}
}else{
	header("location:resumen_asistencias.php?ficha=".$ficha."&&msj=3");
}
?>