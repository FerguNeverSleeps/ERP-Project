<?
//session_start();

	
//DECLARACION DE LIBRERIAS
require_once '../../lib/common.php';

$conexion = conexion();

  $sql = "SELECT  ffin_periodo
                FROM   periodos_vacaciones 
                WHERE  id='{$_GET['id_periodo_vacacion']}'";

//echo $sql;
                
$resultado=query($sql,$conexion);
//$fetch=fetch_array($resultado,$conexion);

$arr = array();

while ($fetch=fetch_array($resultado,$conexion)) {
    $fecha=$fetch['ffin_periodo'];
    $fecha_fin_periodo = date("d/m/Y", strtotime($fecha));
    echo $fecha_fin_periodo;
//    $arr[] = array('id_accion_funcionario_tipo' => $fetch['id_accion_funcionario_tipo'],
//                   'nombre_accion' => $fetch['nombre_accion'],
//                   'correlativo' => $fetch['correlativo'],
//        );
}
//echo '' . json_encode($arr) . '';




?>
