<?php
$ruta = dirname(dirname(dirname(dirname(__FILE__))));
require_once($ruta.'/lib/database.php');

if(isset($_POST['id_puesto']))
{
	$id_puesto = $_POST['id_puesto'];
	$jsondata = array();

	$db = new Database($_SESSION['bd']);

	$sql = "SELECT * FROM vig_puestos WHERE id_puesto='{$id_puesto}'";
	$res = $db->query($sql);

	$jsondata["registro"] = $res->fetch_assoc();

	header('Content-type: application/json; charset=utf-8');
	echo json_encode($jsondata);
}
exit();
?>