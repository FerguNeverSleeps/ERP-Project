<?php
//Produccion
//$archivo = date('Ymd').".log";
//$fecha_registro = date('Y-m-d');
$directorio1 = "/media/reloj/";//Ruta donde se debe buscar el archivo generado por el reloj
$directorio  = "/var/www/html/asamblea_rrhh/nomina/paginas/control_asistencia/archivos/";
// para hacer pruebas descomentar
//Pruebas

$fecha_registro = "2016-12-03";
$archivo    = "20161203.log";

//=================================================================

/*if ( !copy( $directorio1.$archivo , $directorio.$archivo ) ) {
    echo "Error al copiar ".$directorio1.$archivo." a ".$directorio.$archivo;
}*/

if (!is_readable($directorio1.$archivo)) {
	echo "Error al leer el archivo: ".$directorio1.$archivo;
}else{
	echo "Leyendo archivo: " .$directorio1.$archivo. "(Paso 1) <br>";
	
	//=================================================================
	require_once "config/db.php";
	require_once "libs/importar_archivos.php";
	require_once "utils/funciones_procesar.php";
	require_once "utils/cargar_archivo_handpunch.php";
	//=================================================================
	if ($proceso) 
	{
		//global $conexion;

		//=================================================================
		$res = $conexion2->query("SELECT * FROM `caa_registros` WHERE fecha_registro = '$fecha_registro' ORDER BY ficha,fecha,hora ASC");
		//=================================================================
		$i=0;
		while( $fila = mysqli_fetch_array($res) )
		{
			$turno    = get_turno(get_turno_id($fila['ficha'],$fila['fecha'],$calendario),$turnos);
			$nuevo[$i]=array(
				'id'            => $fila['id'],
				'ficha'         => $fila['ficha'],
				'fecha'         => $fila['fecha'],
				'hora'          => $fila['hora'],
				'tipo_registro' => get_mov($fila['ficha'],$nuevo,$i,$turno),
				'turno_id'      => $fila['turno_id'],
				'dispositivo'   => $fila['dispositivo'],
				'id_archivo'    => $fila['archivo_reloj'],
				'estado'        => 0
				);
			$sql = "UPDATE `caa_registros` SET tipo_registro = '".$nuevo[$i]['tipo_registro']."', estado = '".$nuevo[$i]['estado']."' WHERE id = '".$fila['id']."'";
			$conexion2->query( $sql ) or die( 'No se ejecuto el update en caa_registros - '.$sql );
			$i++;
		}
		if ($i > 0) {
			echo "Paso 3: Identificar entradas/salidas cargados en caa_registros : ".$i." <br>";
		}
		//=================================================================
		$del = $conexion2->query("DELETE FROM `caa_periodos` WHERE fecha_registro = '$fecha_registro'");
		//=================================================================
		$del = $conexion2->query("ALTER TABLE `caa_periodos` AUTO_INCREMENT =1");
		//=================================================================
		$registros = validar_e_s($nuevo);
		for ($i=0; $i < count($registros); $i++)
		{
			$del = $conexion2->query("INSERT INTO `caa_periodos`(
			`fecha_registro`, `ficha`, `entrada`, `fec_ent`, `salida`, `fec_sal`, `tiempo`, `turno_id`, `id_archivo`, `estado` ) 
			VALUES ( 
			'".$fecha_registro."','".$registros[$i]['ficha']."','".$registros[$i]['entrada']."','".$registros[$i]['fec_ent']."','".$registros[$i]['salida']."','".$registros[$i]['fec_sal']."','".$registros[$i]['tiempo']."','".$registros[$i]['turno_id']."','".$registros[$i]['id_archivo']."', 0 )");
		}
		//=================================================================
		
		echo "Paso 4: Periodos de trabajo registrados en caa_periodos : ".count($registros)." <br>";

		$res = $conexion2->query("SELECT ficha, fec_ent , MIN(entrada) As entrada , MAX(salida) AS salida , fec_sal, SEC_TO_TIME(SUM(TIME_TO_SEC(tiempo))) AS tiempo, turno_id, id_archivo FROM `caa_periodos` WHERE fecha_registro = '$fecha_registro' GROUP BY ficha, fec_ent ORDER BY ficha,fec_ent ASC");
		//=================================================================
		$del = $conexion2->query("DELETE FROM `caa_resumen` WHERE fecha_registro = '$fecha_registro'");
		//=================================================================
		$del = $conexion2->query("ALTER TABLE `caa_resumen` AUTO_INCREMENT =1");
		//=================================================================
		$resumen = 0;
		while( $fila = mysqli_fetch_array($res) )
		{
			$turno    = get_turno(get_turno_id($fila['ficha'],$fila['fec_ent'],$calendario),$turnos);
			$tardanza = tardanza($fila['entrada'],$turno);
			$extra    = horas_extras($fila['entrada'],$fila['salida'],$turno);
			$tiempo   = diff_fechora($fila['fec_ent']." ".$fila['entrada'],$fila['fec_sal']." ".$fila['salida']);
			
			$conexion2->query("INSERT INTO `caa_resumen`(
			`fecha_registro`, `ficha`, `fecha`, `fec_sal`, `entrada`, `salida`, `tiempo`, `tardanza`, `h_extra`, `recargo_25`, `recargo_50`, `jornada`, `turno_id`, `id_archivo` ) 
			VALUES ( 
			'".$fecha_registro."','".$fila['ficha']."','".$fila['fec_ent']."','".$fila['fec_sal']."','".$fila['entrada']."','".$fila['salida']."','".$tiempo."','".$tardanza."','".$extra[0]."', '".$extra[1]."','".$extra[2]."', '".$extra[3]."','".$fila['turno_id']."','".$fila['id_archivo']."' )");
		   	$resumen++;
		}

		echo "Paso 5: Marcaciones registradas en caa_resumen : ".$resumen." <br>";
	}
}
//borrar_archivo($directorio);
echo "Termino el proceso se cargo archivo : ".$last_id. ". Fecha : ". $fecha_registro;
?>