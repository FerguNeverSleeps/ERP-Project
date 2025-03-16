<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 'On');
ini_set('memory_limit', '64M');
echo "string <br>";
//require_once $_SESSION['PATH']."nomina/paginas/control_asistencia/config/db.php";
//require_once $_SESSION['PATH']."nomina/paginas/control_asistencia/utils/funciones_cron.php";
require_once "/var/www/html/asamblea_rrhh/nomina/paginas/control_asistencia/config/db.php";
require_once "/var/www/html/asamblea_rrhh/nomina/paginas/control_asistencia/utils/funciones_cron.php";
//------------------------------------------------
//$fecha = date('Y-m-d');
$fecha = "2017-06-05";
$fec_reg = date ( 'Y-m-d' , strtotime ( '-1 day' , strtotime ( $fecha ) ) );
//------------------------------------------------
// Inicio de la transacción
$conexion =  mysqli_connect( $params['host'], $params['user'], $params['password'], $params['dbname'] ) or die( 'Could not open connection to server' );
mysqli_query($conexion, 'SET CHARACTER SET utf8');
//cargando archivos necesarios para buscar turnos
//------------------------------------------------------------
//se carga el calendario de personal completo
$cal = $conexion->query("SELECT * FROM nomcalendarios_personal WHERE fecha = '{$fec_reg}'");
$i=0;
while ($fila = mysqli_fetch_array($cal))
{
	$calendario[$i] = array('ficha' => $fila['ficha'],'fecha' => $fila['fecha'],'dia_fiesta' => $fila['dia_fiesta'],'turno_id' => $fila['turno_id']);
	$i++;
}
//------------------------------------------------------------
//se cargan todos los turnos registrados
$tur = $conexion->query("SELECT * FROM nomturnos");
$i=0;
while ($fila = mysqli_fetch_array($tur))
{
	$turnos[$i] = array('turno_id' => $fila['turno_id'],'descripcion' => $fila['descripcion'],'entrada' => $fila['entrada'],'tolerancia_entrada' => $fila['tolerancia_entrada'],'salida' => $fila['salida'],'tolerancia_salida' => $fila['tolerancia_salida'],'libre' => $fila['libre'],'tipo' => $fila['tipo']);
	$i++;
}
echo "string <br>";
//------------------------------------------------------------
//registrar incidencias Automaticas (Tardanzas)
$tar = $conexion->query("SELECT * FROM caa_resumen WHERE tardanza != '00:00:00' AND fecha = '{$fec_reg}'");
while($fila = mysqli_fetch_array($tar))
{
	if (!validar_incidencia($fila['ficha'],$fila['fecha'],1,$conexion)) {
		$conexion->query("INSERT INTO caa_incidencias_empleados (ficha,fecha,id_incidencia) VALUES (".$fila['ficha'].",'".$fila['fecha']."',1)");
	}
}
//registrar incidencias Automaticas (Ausencias)
$res = $conexion->query("SELECT ficha FROM nompersonal WHERE marca_reloj = 1 AND estado = 'REGULAR' ORDER BY ficha ASC");
while($per = mysqli_fetch_array($res))
{
	$turno = get_turno(get_turno_id($per['ficha'],$fec_reg,$calendario),$turnos);
	$dia_semana = get_dia($per['ficha'],$fec_reg,$calendario);
	if ( (!validar_incidencia($per['ficha'],$fec_reg,3,$conexion))&&(!validar_asistencia($per['ficha'],$fec_reg,$conexion))&&($turno['tipo']<6)&&($dia_semana==0) ) {
		$sql = "INSERT INTO caa_resumen (fecha_registro,ficha, fecha,jornada,ausencia,h_ausencia,turno_id,estado) VALUES ('".$fec_reg."',".$per['ficha'].",'".$fec_reg."','".get_jornada($turno['tipo'])."',1,'".cant_horas($turno['entrada'],$turno['salida'])."','".$turno['turno_id']."',1)";
		$conexion->query($sql);
		$conexion->query("INSERT INTO caa_incidencias_empleados (ficha,fecha,id_incidencia) VALUES (".$per['ficha'].",'".$fec_reg."',3)");
	}
}
//------------------------------------------------------------
//registrar Asistencias Automaticas
//------------------------------------------------------------
$res = $conexion->query("SELECT ficha FROM nompersonal WHERE marca_reloj = 0 AND estado = 'REGULAR' ORDER BY ficha ASC");
while($per = mysqli_fetch_array($res))
{
	$turno = get_turno(get_turno_id($per['ficha'],$fec_reg,$calendario),$turnos);
	$dia_semana = get_dia($per['ficha'],$fec_reg,$calendario);
	
	if ( (!validar_asistencia($per['ficha'],$fec_reg,$conexion))&&($turno['tipo']<6)&&($dia_semana<6) ) {
		$sql = "INSERT INTO caa_resumen (fecha_registro,ficha, fecha, fec_sal, entrada, salida, tiempo, jornada, turno_id,estado,carga) VALUES ('".$fec_reg."','".$per['ficha']."','".$fec_reg."','".$fec_reg."','".$turno['entrada']."','".$turno['salida']."','".cant_horas($turno['entrada'],$turno['salida'])."','".get_jornada($turno['tipo'])."','".$turno['turno_id']."',1,1)";
		$conexion->query($sql);
	}
}
//------------------------------------------------------------
//registrar Incidencias a funcionarios de Vacaciones
//------------------------------------------------------------
//$res = $conexion->query("SELECT ficha FROM nompersonal WHERE  estado = 'VACACIONES' ORDER BY ficha ASC");
$sql_vac = "SELECT a.ficha,b.tipo FROM nompersonal a LEFT JOIN expediente b ON a.cedula = b.cedula WHERE b.fecha_inicio <= '$fecha' AND b.fecha_fin >= '$fecha' AND b.tipo IN (4,11,12) GROUP BY a.ficha,b.tipo ORDER BY b.fecha DESC";
$res = $conexion->query($sql_vac);
//echo $sql_vac."<br>";
while($per = mysqli_fetch_array($res))
{
	$turno = get_turno(get_turno_id($per['ficha'],$fec_reg,$calendario),$turnos);
	$dia_semana = get_dia($per['ficha'],$fec_reg,$calendario);
	
	if ( (!validar_asistencia($per['ficha'],$fec_reg,$conexion))&&($turno['tipo']<6)&&($dia_semana<6) ) {
		$sql = "INSERT INTO caa_resumen (fecha_registro,ficha, fecha, fec_sal, entrada, salida, tiempo, jornada, turno_id,estado,carga) VALUES ('".$fec_reg."','".$per['ficha']."','".$fec_reg."','".$fec_reg."','".$turno['entrada']."','".$turno['salida']."','".cant_horas($turno['entrada'],$turno['salida'])."','".get_jornada($turno['tipo'])."','".$turno['turno_id']."',1,1)";
		$conexion->query($sql);
	}
	switch ($per['tipo']) {
		case 4:
			$incidencia = 9;
			break;
		
		case 11:
			$incidencia = 52;
			break;
		
		case 12:
			$incidencia = 6;
			break;
		
		default:
			$incidencia = 9;
			break;
	}
	if ( ( !validar_incidencia( $per['ficha'],$fec_reg,$incidencia,$conexion ) ) ) {
		$sql_inc = "INSERT INTO caa_incidencias_empleados (ficha,fecha,id_incidencia) VALUES ('".$per['ficha']."','".$fec_reg."','".$incidencia."')";
		$conexion->query($sql_inc);
		//echo $sql_inc."<br>";
	}
}
//------------------------------------------------------------
//registrar Ausencias en salidas Automaticas
//------------------------------------------------------------
$res = $conexion->query("SELECT a.ficha,a.fecha,a.entrada,a.salida,a.tiempo,a.tardanza,a.h_ausencia,b.turno_id,b.entrada t_entrada,b.salida t_salida FROM caa_resumen a,nomturnos b WHERE a.turno_id = b.turno_id AND a.salida < b.salida AND a.fecha = '".$fec_reg."' ORDER BY a.fecha ASC");
while($per = mysqli_fetch_array($res))
{
	if ($per['salida'] == '00:00:00') {
		$sql = "UPDATE caa_resumen SET h_ausencia='".cant_horas($per['t_entrada'],$per['t_salida'])."' WHERE ficha ='".$per['ficha']."' AND fecha_registro='".$fec_reg."'";
	}else{
		$sql = "UPDATE caa_resumen SET h_ausencia='".cant_horas($per['salida'],$per['t_salida'])."' WHERE ficha ='".$per['ficha']."' AND fecha_registro='".$fec_reg."'";
	}
	$conexion->query($sql);
	if ( ( !validar_incidencia( $per['ficha'],$fec_reg,3,$conexion ) ) ) {
		$conexion->query("INSERT INTO caa_incidencias_empleados (ficha,fecha,id_incidencia) VALUES ('".$per['ficha']."','".$fec_reg."',8)");
	}
}
?>
