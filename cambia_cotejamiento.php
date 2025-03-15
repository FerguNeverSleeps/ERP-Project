<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/planillaexpress/nomina/lib/common.php');
error_reporting(0);
$mysqli = new bd($_SESSION["bd_nomina"]);

$consulta= "show tables";

$consulta_base_datos = "ALTER DATABASE ".$_SESSION["bd_nomina"]." DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci";

$mysqli->query($consulta_base_datos);

if ($result = $mysqli->query($consulta) )
{
	while ($data = $result->fetch_array())
	{
		$sql = "ALTER TABLE ".$data[0]." DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci";
		$mysqli->query($sql);
	}

}


?>