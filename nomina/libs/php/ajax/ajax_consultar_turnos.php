<?php
	require_once '../../../../generalp.config.inc.php';
	require_once '../../../lib/common.php';
	require_once ("../../../paginas/func_bd.php");

	$conexion=conexion();

	$tipo_turno = $_POST['tipo_turno'];
	$contador   = 0;

	// Consultar los turnos de este tipo

	$sql = "SELECT turno_id, descripcion 
			FROM   nomturnos n WHERE n.tipo='{$tipo_turno}'";

	$res = sql_ejecutar_utf8($sql);

	 echo "<option value=''>Seleccione un turno</option>";   

	 while( $fila = mysqli_fetch_array($res) )
	 {
	 	echo "<option value='".$fila['turno_id']."'>".$fila['descripcion']."</option>";
	 	$contador++;
	 }

	 echo "&&&&&$contador";
?>