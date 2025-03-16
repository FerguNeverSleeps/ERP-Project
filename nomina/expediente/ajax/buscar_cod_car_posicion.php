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
    $cod_car=$fetch_posicion['cargo_id'];
}

$sql_cargo = "SELECT  *
            FROM   nomcargos 
            WHERE  cod_car='{$cod_car}'";

$resultado_cargo=query($sql_cargo,$conexion);
while ($fetch_cargo=fetch_array($resultado_cargo,$conexion)) 
{
    $cargo=$fetch_cargo['cod_car'];
}
echo $cargo;
//    $arr[] = array('id_accion_funcionario_tipo' => $fetch['id_accion_funcionario_tipo'],
//                   'nombre_accion' => $fetch['nombre_accion'],
//                   'correlativo' => $fetch['correlativo'],
//        );

//echo '' . json_encode($arr) . '';

?>
