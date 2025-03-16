<?
//session_start();

	
//DECLARACION DE LIBRERIAS
require_once '../../lib/common.php';

$conexion = conexion();

$sql_posicion = "SELECT  *
                FROM   nomposicion 
                WHERE  nomposicion_id='{$_GET['posicion']}'";

$resultado_posicion=query($sql_posicion,$conexion);
//$fetch=fetch_array($resultado,$conexion);

$arr = array();

while ($fetch_posicion=fetch_array($resultado_posicion,$conexion)) 
{
    $salario=$fetch_posicion['sueldo_propuesto'];
}

echo $salario;
//    $arr[] = array('id_accion_funcionario_tipo' => $fetch['id_accion_funcionario_tipo'],
//                   'nombre_accion' => $fetch['nombre_accion'],
//                   'correlativo' => $fetch['correlativo'],
//        );

//echo '' . json_encode($arr) . '';

?>
