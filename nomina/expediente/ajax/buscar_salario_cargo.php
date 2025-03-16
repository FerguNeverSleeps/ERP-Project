<?
//session_start();

	
//DECLARACION DE LIBRERIAS
require_once '../../lib/common.php';

$conexion = conexion();

$sql_cargo = "SELECT  sueldo
                FROM   nomcargos 
                WHERE  cod_car='{$_GET['cargo']}'";

$resultado_cargo=query($sql_cargo,$conexion);
//$fetch=fetch_array($resultado,$conexion);

$arr = array();

while ($fetch_cargo=fetch_array($resultado_cargo,$conexion)) 
{
    $salario=$fetch_cargo['sueldo'];
}

echo $salario;
//    $arr[] = array('id_accion_funcionario_tipo' => $fetch['id_accion_funcionario_tipo'],
//                   'nombre_accion' => $fetch['nombre_accion'],
//                   'correlativo' => $fetch['correlativo'],
//        );

//echo '' . json_encode($arr) . '';

?>