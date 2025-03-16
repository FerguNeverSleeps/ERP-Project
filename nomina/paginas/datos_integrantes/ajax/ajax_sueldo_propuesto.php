<?php
$ruta = dirname(dirname(dirname(dirname(__FILE__))));
require_once($ruta.'/lib/database.php');

if(isset($_POST['nomposicion_id']))
{
	$nomposicion_id = $_POST['nomposicion_id'];
	$jsondata = array();

	$db = new Database($_SESSION['bd']);

	$sql = "SELECT sueldo_propuesto FROM nomposicion WHERE nomposicion_id='{$nomposicion_id}'";
	$res = $db->query($sql);

	if($fila = $res->fetch_assoc())
	{
		$jsondata["sueldo_propuesto"] = $fila['sueldo_propuesto'];
	}	

	header('Content-type: application/json; charset=utf-8');
	echo json_encode($jsondata);
}
exit();
?>