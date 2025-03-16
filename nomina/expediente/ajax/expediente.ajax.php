<?
//session_start();

	
//DECLARACION DE LIBRERIAS
require_once '../../lib/common.php';

$conexion = conexion();
if($_POST['accion'] == "guardarDescripcionExpediente")
{
    $descripcion = ($_POST['descripcion']) ;
    $id_expediente = $_POST['id_expediente'] ;
    query("set names utf8;",$conexion);
    $sql_exp = "UPDATE expediente SET descripcion = '{$descripcion}'
                    WHERE  cod_expediente_det='{$id_expediente}'";

    $resultado=query($sql_exp,$conexion);
    //$fetch=fetch_array($resultado,$conexion);
    if($resultado)  echo '1'; 
    else echo '0' ;
}  
//    $arr[] = array('id_accion_funcionario_tipo' => $fetch['id_accion_funcionario_tipo'],
//                   'nombre_accion' => $fetch['nombre_accion'],
//                   'correlativo' => $fetch['correlativo'],
//        );

//echo '' . json_encode($arr) . '';

?>
