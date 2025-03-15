<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/planillaexpress/nomina/lib/common.php');
error_reporting(0);
$base_datos="planillaexpress_conf";

$mysqli             = new bd($base_datos);
$consulta           = "SELECT TABLE_NAME,ENGINE,TABLE_TYPE,TABLE_COLLATION
FROM   information_schema.TABLES
WHERE  TABLE_SCHEMA ='".$base_datos."'";

if ($result = $mysqli->query($consulta) )
{
	$consulta_base_datos = "ALTER DATABASE ".$base_datos." DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci";
	$mysqli->query($consulta_base_datos);

	while ($data = $result->fetch_assoc())
	{
		if ($data["TABLE_TYPE"]!="VIEW") 
		{
			/*if ($data["ENGINE"] != "InnoDB") 
			{
				$sqlAlter = "ALTER TABLE ".$data["TABLE_NAME"]." ENGINE=InnoDB;";
		   		$mysqli->query($sqlAlter);
		   		
			}*/
			if ($data["TABLE_COLLATION"] != "utf8_general_ci") 
			{
				$sql = "ALTER TABLE ".$data["TABLE_NAME"]." DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;";
				$mysqli->query($sql);

				$sql2 = "ALTER TABLE ".$data["TABLE_NAME"]." CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;";
				$mysqli->query($sql2);
			}
		}
	}
}


?>