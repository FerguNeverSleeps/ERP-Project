<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
ini_set('memory_limit', '64M');

//=================================================================
require_once "/var/www/html/asamblea_rrhh/nomina/paginas/control_asistencia/config/db.php";
//require_once $_SESSION['PATH']."nomina/paginas/control_asistencia/config/db.php";
//require_once $_SESSION['PATH']."nomina/paginas/control_asistencia/libs/importar_archivos.php";
require_once "funciones_cron.php";
//=================================================================
//Descomentar en Produccion
$fecha_registro = date('Y-m-d');
$archivo = date('Ymd').".log";
//Comentar para Produccion
//$fecha_registro = "2017-01-07";
//$archivo    = "20170107.log";
//Directorios donde buscar archivos (.log) con asistencias
$directorio1 = "/media/reloj/";//Ruta donde se debe buscar el archivo generado por el reloj
$directorio2 = "/var/www/html/asamblea_rrhh/nomina/paginas/control_asistencia/archivos/";
//$directorio2 = $_SESSION['PATH']."nomina/paginas/control_asistencia/archivos/";
//=================================================================
//establecer fechas en las cuales cargo los registros del calendario de personal
$fecha1 = date ( 'Y-m-j' , strtotime ( '-3 day' , strtotime ( $fecha_registro ) ) );
$fecha2 = date ( 'Y-m-j' , strtotime ( '+2 day' , strtotime ( $fecha_registro ) ) );
//Verificando si el archivo se puede leer
if ( is_readable($directorio1.$archivo) ) {
	//PASO 1
	//Si se puede leer se procede a insertar los datos que este contiene en la tabla
	//CAA_REGISTROS.
	//=================================================================
	$conexion->query("DELETE FROM `caa_registros` WHERE fecha_registro = '$fecha_registro'");
	$conexion->query("ALTER TABLE `caa_registros` AUTO_INCREMENT =1");
	//=================================================================
	//Cargamos las Variables
	$fecha_inicio  = date('Y-m-d');
	$fecha_fin     = date('Y-m-d');
	// Inicio de la transacción
	//cargando archivos necesarios para buscar turnos
	//=================================================================
	//se carga el calendario de personal completo
	$cal = $conexion->query("SELECT a.* FROM `nomcalendarios_personal` a,nompersonal b WHERE a.ficha = b.ficha AND b.estado != 'Egresado' AND b.estado != 'De Baja' AND a.fecha >= '{$fecha1}' AND a.fecha <= '{$fecha2}'");
	$i=0;
	while ( $fila = mysqli_fetch_array($cal) )
	{
		$calendario[$i] = array('ficha' => $fila['ficha'],'fecha' => $fila['fecha'],'turno_id' => $fila['turno_id']);
		$i++;
	}
	//=================================================================
	//se cargan todos los turnos registrados
	$tur = $conexion->query("SELECT * FROM `nomturnos`");
	$i=0;
	while ( $fila = mysqli_fetch_array($tur) )
	{
		$t_extra = date("H:i:s",strtotime("00:00:00")+strtotime($fila['salida'])+strtotime("01:00:00"));
		$turnos[$i] = array('turno_id' => $fila['turno_id'],'descripcion' => $fila['descripcion'],'entrada' => $fila['entrada'],'tolerancia_entrada' => $fila['tolerancia_entrada'],'salida' => $fila['salida'],'tolerancia_salida' => $fila['tolerancia_salida'],'tolerancia_extra' => $t_extra,'libre' => $fila['libre'],'tipo' => $fila['tipo']);
		$i++;
	}
	//=================================================================
	//Se abre el archivo e inicia la lectura y carga de los datos en caa_registros
	//=================================================================
	//verificando si se puede leer el archivo
	$fichero = fopen($directorio1.$archivo, "r" );
	while (($data = fgetcsv($fichero, 1000, "\n")) !== false) {
	    foreach ($data as $value) {
	        $linea = explode( "," , $value );
	        $dispositivo = (int) $linea[0].(int) $linea[1];
	        $date = new DateTime( $linea[5].":".$linea[6].":00" );
	        //echo $date->format('H:i:s')." 2: ".$linea[5].":".$linea[6].":00 <hr>";
	        $hora = $date->format('H:i:s');
	        $date = new DateTime($linea[9]."-".$linea[7]."-".$linea[8]);
	        $fecha = $date->format('Y-m-d');
	        $ficha = (int) $linea[3];
	        $sql = "INSERT INTO `caa_registros`(`fecha_registro`,`ficha`,`fecha`,`hora`,`turno_id`,`dispositivo`,`archivo_reloj`)VALUES('".$fecha_registro."','".$ficha."','".$fecha."','".$hora."','".get_turno_id( $ficha,$fecha,$calendario)."','".$dispositivo."',0)";
	        $conexion->query($sql);
	    }
	}
	fclose($fichero);
	//=================================================================
	//PASO 2
	//Aca se identifican que tipo de registros trajo el archivo .log (entrada/salida)
	//=================================================================
	$resultado = $conexion->query("SELECT * FROM `caa_registros` WHERE fecha_registro = '$fecha_registro' ORDER BY ficha,fecha,hora ASC");
	//=================================================================
	$i=0;
	while ($fila = $resultado->fetch_assoc()){
		$nuevo[$i] = $fila;
		$i++;
	}
	for ($i=0; $i < count($nuevo); $i++) { 
		$turno = get_turno(get_turno_id($nuevo[$i]['ficha'],$nuevo[$i]['fecha'],$calendario),$turnos);
		$nuevo[$i]['tipo_registro']=get_mov($nuevo[$i]['ficha'],$nuevo,$i,$turno);
		$sql = "UPDATE `caa_registros` SET tipo_registro = '".$nuevo[$i]['tipo_registro']."', estado = '".$nuevo[$i]['estado']."' WHERE id = '".$nuevo[$i]['id']."'";
		$conexion->query( $sql );
	}
	//Se limpia la tabla caa_periodos con la finalidad de recalcular los datos 
	//en caso de que el archivo tenga modificaciones o nuevos registros
	//=================================================================
	$conexion->query("DELETE FROM `caa_periodos` WHERE fecha_registro = '$fecha_registro'");
	$conexion->query("ALTER TABLE `caa_periodos` AUTO_INCREMENT =1");
	//=================================================================
	//PASO 3
	//Procesar los registros y calcular los bloques de trabajo por dia luego se 
	//registran en caa_periodos
	$registros = validar_e_s($nuevo);
	for ($i=0; $i < count($registros); $i++){
		$conexion->query("INSERT INTO `caa_periodos`( `fecha_registro`, `ficha`, `entrada`, `fec_ent`, `salida`, `fec_sal`, `tiempo`, `turno_id`, `estado` ) VALUES ( '".$fecha_registro."','".$registros[$i]['ficha']."','".$registros[$i]['entrada']."','".$registros[$i]['fec_ent']."','".$registros[$i]['salida']."','".$registros[$i]['fec_sal']."','".$registros[$i]['tiempo']."','".$registros[$i]['turno_id']."', 0 )");
	}
	//=================================================================
	//Se limpia la tabla caa_resumen con la finalidad de recalcular los datos 
	//en caso de que el archivo tenga modificaciones o nuevos registros
	//=================================================================
	$conexion->query("DELETE FROM `caa_resumen` WHERE fecha_registro = '$fecha_registro'");
	$conexion->query("ALTER TABLE `caa_resumen` AUTO_INCREMENT =1");
	//=================================================================
	//PASO 4
	//Procesar los registros y calcular un resumen de horas trabajadas por dia luego se 
	//registran en caa_resumen
	$resultado = $conexion->query("SELECT ficha, fec_ent , MIN(entrada) As entrada , MAX(salida) AS salida , fec_sal, SEC_TO_TIME(SUM(TIME_TO_SEC(tiempo))) AS tiempo, turno_id, id_archivo FROM `caa_periodos` WHERE fecha_registro = '$fecha_registro' GROUP BY ficha, fec_ent ORDER BY ficha,fec_ent ASC");
	//=================================================================
	while ($fila = $resultado->fetch_assoc())
	{
		$turno    = get_turno(get_turno_id($fila['ficha'],$fila['fec_ent'],$calendario),$turnos);
		$tardanza = tardanza($fila['entrada'],$turno);
		$extra    = horas_extras($fila['entrada'],$fila['salida'],$turno);
		$tiempo   = diff_fechora($fila['fec_ent']." ".$fila['entrada'],$fila['fec_sal']." ".$fila['salida']);		
		$sql = "INSERT INTO `caa_resumen` (`fecha_registro`,`ficha`,`fecha`,`fec_sal`,`entrada`, `salida`,`tiempo`,`tardanza`,`h_extra`,`recargo_25`,`recargo_50`,`jornada`,`turno_id`,`id_archivo`) VALUES ( '".$fecha_registro."', '".$fila['ficha']."', '".$fila['fec_ent']."', '".$fila['fec_sal']."', '".$fila['entrada']."', '".$fila['salida']."', '".$tiempo."', '".$tardanza."', '".$extra[0]."', '".$extra[1]."', '".$extra[2]."', '".$extra[3]."', '".$fila['turno_id']."', '".$fila['id_archivo']."')";
		$conexion->query($sql);
	}
}
?>
