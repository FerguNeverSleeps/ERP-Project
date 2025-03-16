<?php
session_start();
ob_start();

require_once '../lib/config.php';
require_once '../lib/common.php';
include ("func_bd.php");
$conexion=conexion();
$host_ip = $_SERVER['REMOTE_ADDR'];

//echo $conexion;
$url      = "control_acceso2";
$modulo   = "Control de Acceso";
$tabla    = "reloj_encabezado";
$titulos  = array("Cod.","Fecha inicio", "Fecha fin", "Fecha Registro");
$indices  = array("cod_enca","fecha_ini", "fecha_fin", "fecha_reg");

$cedula   = @$_GET['cedula'];
$eliminar = @$_GET['eliminar'];
$tipob    = @$_GET['tipo'];
$des      = @$_GET['des'];
$pagina   = @$_GET['pagina'];
$busqueda = @$_GET['busqueda'];

$reg=$_POST['registro_id'];
$nom=$_POST['codigo_nomina'];

$datos1="SELECT
    A.cod_enca,
    A.fecha_reg,
    A.fecha_ini,
    A.fecha_fin
FROM
    reloj_encabezado as A
WHERE A.cod_enca='$reg'";

$rs = query($datos1,$conexion);
$encabezado=fetch_array($rs);

$id_encabezado = $encabezado['cod_enca'];
$fecha_ini = $encabezado['fecha_ini'];
$fecha_fin = $encabezado['fecha_fin'];


$datos2="select a.ficha,a.fecha,a.dia_fiesta,a.turno_id,c.entrada,c.salida from nomcalendarios_personal a 
left join nomturnos c on a.turno_id = c.turno_id 
where a.fecha between '$fecha_ini' and '$fecha_fin' and a.ficha in 
(select b.ficha as ficha from nompersonal b where b.tipnom = '$nom' and b.estado not like '%Baja%') 
order by a.ficha, a.fecha";

echo $datos2;
echo "<br>";
$rs = query($datos2,$conexion);
$sql = "INSERT INTO `reloj_detalle` (`id_encabezado`, `ficha`, `fecha`, `entrada`, `salida`, `ordinaria`, `turno`) VALUES ";
while($fila=fetch_array($rs)){
    
    $ficha = $fila['ficha'];
    $fecha = $fila['fecha'];
    //dia de la semana
    $dia = date( "w", strtotime($fecha));
    $entrada = $fila['entrada'];
    $salida = $fila['salida'];
    
    $horas = ($dia==0 || $dia==6)?'00:00':'08:00';

    $turno_id = $fila['turno_id'];
    //INSERT EN RELOJ DETALLES
    $delete = "DELETE FROM `reloj_detalle` WHERE  `id_encabezado`='$id_encabezado';";
    $del = query($delete,$conexion);

    $sql .= "('$id_encabezado', '$ficha', '$fecha', '$entrada', '$salida', '$horas', '$turno_id'),";
    
}

$sql .= ";;";
$sql = str_replace(",;;", ";", $sql );
$inser = query($sql,$conexion);

echo $sql;
echo "<br>";

?>