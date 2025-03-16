<?php
	$ruta = dirname(dirname(dirname(dirname(__FILE__))));
	require_once($ruta.'/lib/database.php');

	$db             = new Database($_SESSION['bd']);
	$ficha = isset($_REQUEST['ficha']) ? $_REQUEST['ficha']:NULL;
	$nomposicion_id = isset($_REQUEST['nomposicion_id']) ? $_REQUEST['nomposicion_id']:NULL;
	$sql            = "SELECT nomposicion_id
						FROM nomposicion
						WHERE nomposicion_id='".$nomposicion_id."'";
	$res            = $db->query($sql);
    $fila = $res->fetch_assoc();


		echo  $fila['nomposicion_id'];
		



?>