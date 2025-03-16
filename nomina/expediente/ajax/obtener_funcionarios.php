<?
//session_start();

	
//DECLARACION DE LIBRERIAS
require_once '../../lib/common.php';

$conexion = conexion();

$sql_personal = "SELECT  cedula, apenom
                FROM   nompersonal";

$resultado_personal=query($sql_personal,$conexion);
//$fetch=fetch_array($resultado,$conexion);

$personal = array();

while ($fetch_personal=fetch_array($resultado_personal,$conexion)) 
{
    $personal[]=$fetch_personal;
}

for ($i=0; $i < count($personal); $i++) { 
	echo "<option value='".$personal[$i]["cedula"]."'>".$personal[$i]["cedula"]." - ".utf8_encode($personal[$i]["apenom"])."</option>";
}

?>

