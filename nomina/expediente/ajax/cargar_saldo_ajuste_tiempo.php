<?
//session_start();

	
//DECLARACION DE LIBRERIAS
require_once '../../lib/common.php';

$conexion = conexion();
$tipo_justificacion=$_GET['tipo_justificacion'];
$cedula=$_GET['cedula'];

$sql = "SELECT a.fecha, SUM(a.tiempo) as tiempo, SUM(a.horas) as horas, SUM(a.minutos) as minutos , SUM(a.dias) as dias, b.tiempo as tiempo_total "
        . " FROM dias_incapacidad as a, tipo_justificacion as b "
        . " WHERE a.tipo_justificacion=b.idtipo AND a.tipo_justificacion='$tipo_justificacion' AND a.cedula='$cedula' AND YEAR(fecha)=YEAR(NOW())";
//     echo     $sql;
//     exit;
$resultado=query($sql,$conexion);
//$fetch=fetch_array($resultado,$conexion);

$arr = array();

while ($fetch=fetch_array($resultado,$conexion)) {
    $tiempo=$fetch['tiempo'];
    $tiempo_total=$fetch['tiempo_total'];
   
}
$saldo= $tiempo_total - $tiempo;
 echo number_format($saldo, 2);
?>
