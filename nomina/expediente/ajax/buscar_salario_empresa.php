<?
//session_start();
//DECLARACION DE LIBRERIAS
require_once '../../lib/common.php';

$conexion = conexion();


$sql_minsal = "SELECT monsalmin FROM nomempresa WHERE 1";

$resultado_minsal=query($sql_minsal,$conexion);

$arr = array();

while ($fetch_salmin=fetch_array($resultado_minsal,$conexion)) 
{
    $salario=$fetch_salmin['monsalmin'];
}

echo $salario;

?>