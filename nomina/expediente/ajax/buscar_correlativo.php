<?
//session_start();

	
//DECLARACION DE LIBRERIAS
require_once '../../lib/common.php';

$conexion = conexion();

  $sql = "SELECT  *
                FROM   accion_funcionario_tipo 
                WHERE  id_accion_funcionario_tipo='{$_GET['tipo_accion']}'";

//echo $sql;
                
$resultado=query($sql,$conexion);
//$fetch=fetch_array($resultado,$conexion);

$arr = array();

while ($fetch=fetch_array($resultado,$conexion)) {
    echo $fetch['correlativo'];
//    $arr[] = array('id_accion_funcionario_tipo' => $fetch['id_accion_funcionario_tipo'],
//                   'nombre_accion' => $fetch['nombre_accion'],
//                   'correlativo' => $fetch['correlativo'],
//        );
}
//echo '' . json_encode($arr) . '';




?>
