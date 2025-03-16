<?
//session_start();	
//DECLARACION DE LIBRERIAS
require_once '../../lib/common.php';
$conexion = conexion();
//$sql_posicion = "SELECT  *
//                FROM   nomposicion 
//                WHERE  nomposicion_id='{$_GET['posicion']}'";              
$sql_posicion = "SELECT COUNT(b.nomposicion_id) as total
                        FROM nompersonal b
                        WHERE  b.nomposicion_id='{$_GET['posicion']}' AND b.cedula != '{$_GET['cedula']}' AND (b.estado LIKE '%Activo%' OR b.estado LIKE '%REGULAR%' "
                        . "OR b.estado LIKE '%INTERINO%' OR b.estado LIKE '%RESERVADO%' OR b.estado LIKE '%Vacaciones%' "
                        . "OR b.estado LIKE '%Licencias con Sueldo%' OR b.estado LIKE '%SIN ASIGNACION%')";
            
$resultado_posicion=query($sql_posicion,$conexion);
$fetch_posicion=fetch_array($resultado_posicion,$conexion);

if($fetch_posicion["total"]==0)
{   
    $estado=0;
}
else
{
    $estado=1;
}
$arr = array();
echo $estado;
?>
