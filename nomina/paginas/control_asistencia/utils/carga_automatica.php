<?php
$res = $conexion->query("SELECT ficha FROM nompersonal WHERE marca_reloj = 0 AND estado = 'REGULAR' ORDER BY ficha ASC");
//------------------------------------------------------------
//registrar Asistencias Automaticas
while($per = mysqli_fetch_array($res))
{
	$turno = get_turno(get_turno_id($per['ficha'],$fec_reg,$calendario),$turnos);
	$dia_semana = get_dia($per['ficha'],$fec_reg,$calendario);
	
	if ( (!validar_asistencia($per['ficha'],$fec_reg,$conexion))&&($turno['tipo']<6)&&($dia_semana<6) ) {
		$sql = "INSERT INTO caa_resumen (fecha_registro,ficha, fecha, fec_sal, entrada, salida, tiempo, jornada, turno_id,estado,carga) VALUES ('".$fec_reg."'".$per['ficha'].",'".$fec_reg."','".$fec_reg."','".$turno['entrada']."','".$turno['salida']."','".cant_horas($turno['entrada'],$turno['salida'])."','".get_jornada($turno['tipo'])."','".$turno['turno_id']."',1,1)";
		$conexion->query($sql);
		echo $fec_reg."<br>";
	}
}
?>