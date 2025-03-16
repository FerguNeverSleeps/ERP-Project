<?php
require_once("../lib/common.php");

if(isset($_POST['id_puesto']))
{
	$id_puesto = $_POST['id_puesto'];
	$jsondata = array();

	$conexion = new bd($_SESSION['bd']);

	$sql = "SELECT * FROM vig_puestos WHERE id_puesto='{$id_puesto}'";
	$res = $conexion->query($sql, "utf8");

	$jsondata["registro"] = $res->fetch_assoc();

	header('Content-type: application/json; charset=utf-8');
	echo json_encode($jsondata);
}
exit();
?>