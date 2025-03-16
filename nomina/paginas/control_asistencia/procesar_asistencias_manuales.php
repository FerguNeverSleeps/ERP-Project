<?php
	error_reporting(E_ALL ^ E_DEPRECATED);
	require_once "config/db.php";
	require_once "libs/importar_archivos.php";
	require_once "utils/funciones_procesar.php";
	//=================================================================
	$conexion =  mysqli_connect( $params['host'], $params['user'], $params['password'], $params['dbname'] ) or die( 'Could not open connection to server' );
	mysqli_query($conexion, 'SET CHARACTER SET utf8');
	//=================================================================
	$val = $conexion->query("SELECT * FROM `caa_resumen` WHERE ficha = '".$_POST['ficha']."' AND fecha_registro = '".$_POST['fecha_registro']."'");
	if ( ($_POST['turno_id'] != '#') && ( mysqli_num_rows($val) == 0 ) ) {
		//------------------------------------------------------------
		//se carga el calendario de personal completo
		$sql = "SELECT * FROM `nomcalendarios_personal` WHERE ficha = '".$_POST['ficha']."' AND fecha = '".$_POST['fecha_registro']."'";
		$res = $conexion->query($sql);
		$calendario = mysqli_fetch_array($res);
		//------------------------------------------------------------
		$res = $conexion->query("SELECT * FROM `nomturnos` WHERE turno_id = '".$_POST['turno_id']."'");
		$turno = mysqli_fetch_array($res);
		$res = $conexion->query("SELECT * FROM `nomcalendarios_personal` WHERE ficha = '".$_POST['ficha']."' AND fecha = '".$_POST['fecha_registro']."'");
		//=================================================================
		$tardanza = tardanza($_POST['entrada'],$turno);
		$extra    = horas_extras($_POST['entrada'],$_POST['salida'],$turno);
		$tiempo   = diff_fechora($_POST['fecha_registro']." ".$_POST['entrada'],$_POST['fecha_registro']." ".$_POST['salida']);
		
		/*$s = "<br>`fecha_registro` = '".$_POST['fecha_registro']."'
		<br>`ficha` = '".$_POST['ficha']."'
		<br>`fecha` = '".$_POST['fecha_registro']."'
		<br>`fec_sal` = '".$_POST['fecha_registro']."'
		<br>`entrada` = '".$_POST['entrada']."'
		<br>`salida` = '".$_POST['salida']."'
		<br>`tiempo` = '".$tiempo."'
		<br>`tardanza` = '".$tardanza."'
		<br>`h_extra` = '".$extra[0]."'
		<br>`recargo_25` = '".$extra[1]."'
		<br>`recargo_50` = '".$extra[2]."'
		<br>`jornada` = '".$extra[3]."'
		<br>`turno_id` = '".$_POST['turno_id']."'";*/

		$sql = "INSERT INTO `caa_resumen`(`fecha_registro`,`ficha`,`fecha`,`fec_sal`,`entrada`,`salida`,`tiempo`,`tardanza`,`h_extra`,`recargo_25`,`recargo_50`,`jornada`,`turno_id` ) VALUES ('".$_POST['fecha_registro']."','".$_POST['ficha']."','".$_POST['fecha_registro']."','".$_POST['fecha_registro']."','".$_POST['entrada']."','".$_POST['salida']."','".$tiempo."','".$tardanza."','".$extra[0]."','".$extra[1]."','".$extra[2]."','".$extra[3]."','".$_POST['turno_id']."' )";
		$conexion->query($sql);
		header('location: add_asistencias_manual.php?msj=1&ficha='.$_POST['ficha']);
	}else{
		header('location: add_asistencias_manual.php?msj=0&ficha='.$_POST['ficha']);
	}
?>