<?php
session_start();
require_once '../../lib/common.php';

$conexion = new bd($_SESSION['bd']);

$sql_posicion = "SELECT  *
                FROM   tiposangre ";

$resultado_tipo= $conexion->query($sql_posicion);
//$fetch=fetch_array($resultado,$conexion);
$tipo = array();

while($emp = $resultado_tipo->fetch_array()){
	$tipo[]=$emp;

}
for ($i=0; $i < count($tipo); $i++) { 
	echo "<option value='".$tipo[$i]["IdTipoSangre"]."'>".$tipo[$i]["Descripcion"]."</option>";
}
?>
