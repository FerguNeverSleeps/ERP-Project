<?php
session_start();
require_once('../../lib/database.php');

$db = new Database($_SESSION['bd']);

$proceso = $_REQUEST["proceso"];

$sql = "INSERT INTO `procesos` (`id`, `descrip_proceso`) VALUES (NULL, '".$proceso."')";
        
$res = $db->query($sql);

if($res)
{
	echo "Proceso agregado exitosamente";
}
else
{
	echo "Hubo un error al agregar el proceso";
}


?>