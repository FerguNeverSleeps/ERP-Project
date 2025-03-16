<?php
	$ruta = dirname(dirname(dirname(dirname(__FILE__))));
	require_once($ruta.'/lib/database.php');

	$db             = new Database($_SESSION['bd']);
	$nomposicion_id = isset($_REQUEST['nomposicion_id']) ? $_REQUEST['nomposicion_id']:NULL;
	$sql            = "SELECT sueldo_propuesto
						FROM nomposicion b 
						WHERE nomposicion_id='".$nomposicion_id."'";
	/*$sql            = "SELECT b.nomposicion_id, b.sueldo_propuesto, b.sueldo_2,b.sueldo_3,b.sueldo_4,b.mes_1,b.mes_2,b.mes_3,b.mes_4
						FROM nomposicion b
						WHERE  b.nomposicion_id='".$nomposicion_id."'";*/

	$res            = $db->query($sql);

    $fila = $res->fetch_assoc();
	//echo $fila['nomposicion_id'];


	echo $fila["sueldo_propuesto"]


?>