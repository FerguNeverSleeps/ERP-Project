<?php
require_once("../lib/common.php");

if(isset($_POST['nomposicion_id']))
{
	$nomposicion_id = $_POST['nomposicion_id'];
	$jsondata = array();

	$conexion = new bd($_SESSION['bd']);

	$sql = "SELECT sueldo_propuesto FROM nomposicion WHERE nomposicion_id='{$nomposicion_id}'";
	$res = $conexion->query($sql);

	if($fila = $res->fetch_assoc())
	{
		$jsondata["sueldo_propuesto"] = $fila['sueldo_propuesto'];
	}	

	header('Content-type: application/json; charset=utf-8');
	echo json_encode($jsondata);
}
exit();
?>