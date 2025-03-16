<?
//session_start();

	
//DECLARACION DE LIBRERIAS
require_once '../../lib/common.php';

$conexion = conexion();

  $sql = "SELECT  saldo
                FROM   periodos_vacaciones 
                WHERE  id='{$_GET['id_periodo_vacacion']}'";

//echo $sql;
                
$resultado=query($sql,$conexion);
//$fetch=fetch_array($resultado,$conexion);

$arr = array();

while ($fetch=fetch_array($resultado,$conexion)) {
    $saldo=$fetch['saldo'];
    echo $saldo;
}

?>
