<?
//session_start();

	
//DECLARACION DE LIBRERIAS
require_once '../../lib/common.php';

$conexion = conexion();

$sql_falta = "SELECT  *
                FROM   tipos_faltas 
                WHERE  id='{$_GET['tipo_falta']}'";

$resultado_falta=query($sql_falta,$conexion);
//$fetch=fetch_array($resultado,$conexion);

$arr = array();

while ($fetch_falta=fetch_array($resultado_falta,$conexion)) 
{
    $descripcion=$fetch_falta['descripcion'];
}

echo utf8_encode($descripcion);
?>
