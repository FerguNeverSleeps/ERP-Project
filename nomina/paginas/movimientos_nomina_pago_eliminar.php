<?php 
session_start();
ob_start();
?>
<?
require_once "../lib/common.php";
$conexion=conexion();

$host_ip = $_SERVER['REMOTE_ADDR'];
$modulo = "Movimientos Planilla - Eliminar";
$url = "movimientos_nomina_pago_eliminar.php";

$ficha=$_GET['ficha'];
$concepto=$_GET['concepto'];
$nombre_nomina=$_GET['nomina'];
//echo $nombre_nomina;
//exit;
$pagina=$_GET['pagina'];
$todo=$_GET['todo'];

$consulta_personal="SELECT p.apenom, p.cedula "
        . "FROM nompersonal as p "
        . "WHERE p.ficha='".$_GET['ficha']."'";
//echo $consulta;
$resultado_personal=query($consulta_personal,$conexion);
$fila_personal = mysqli_fetch_array($resultado_personal);
$nombre=       $fila_personal[apenom];
$cedula=       $fila_personal[cedula];

$consulta_concepto="SELECT  monto FROM "
            . "nom_movimientos_nomina "
            . "WHERE ficha='$ficha' and id='$concepto' and codnom='$nombre_nomina' and tipnom='".$_SESSION['codigo_nomina']."'";
$resultado_concepto=query($consulta_concepto,$conexion);
$fila_concepto = mysqli_fetch_array($resultado_concepto);
$monto=       $fila_concepto[monto];

$nomina=$nombre_nomina;
$tipo_nomina=$_SESSION['codigo_nomina'];
$monto_anterior=$monto;
$monto=$monto;
$accion="eliminar";

if($todo==1)
{
    
    $consulta="DELETE FROM "
            . "nom_movimientos_nomina "
            . "WHERE ficha='$ficha' and codnom='$nombre_nomina' and tipnom='".$_SESSION['codigo_nomina']."'";
    
    $descripcion = "Eliminar Movimientos - Colaborador ".$ficha." - Nombre ".$nombre." - Cedula: ".$cedula;
       
}
else
{
    $consulta="DELETE  FROM "
            . "nom_movimientos_nomina "
            . "WHERE ficha='$ficha' and id='$concepto' and codnom='$nombre_nomina' and tipnom='".$_SESSION['codigo_nomina']."'";
    $descripcion = "Eliminar Movimiento - Colaborador ".$ficha." - Nombre ".$nombre." - Cedula: ".$cedula.""
             . " - Concepto: ".$concepto." - Monto Anterior: ".$monto_anterior." - Monto Nuevo: ".$monto;    
        
}
$resultado=query($consulta,$conexion);

$sql_log = "INSERT INTO log_transacciones 
            (cod_log, 
            descripcion, 
            fecha_hora, 
            modulo, 
            url, 
            accion, 
            valor, 
            usuario,
            host) 
            VALUES 
            (NULL, 
            '".$descripcion."', "
            . "now(), "
        . "'".$modulo."', "
        . "'".$url."', "
        . "'".$accion."',"
        . "'".$cod."',"
        . "'".$_SESSION['usuario'] ."',"
        . "'".$host_ip."')";

$res_log = query($sql_log,$conexion) or die("no se actualizo el Tipo de personal");

if($pagina && $_REQUEST["vacaciones"]){
    header("Location: movimientos_nomina_vacaciones.php?codigo_nomina=".$nombre_nomina."&codt=".$_SESSION['codigo_nomina']."&pagina=".$pagina."&vac=1");
    exit;
}
if($pagina && $_REQUEST["liquidaciones"]){
    header("Location: movimientos_nomina_liquidaciones.php?codigo_nomina=".$nombre_nomina."&codt=".$_SESSION['codigo_nomina']."&pagina=".$pagina."&bandera=1");
    exit;
}


if($_GET['auxlia'] != 1)
{   
if($pagina)
    header("Location: movimientos_nomina_pago.php?codigo_nomina=".$nombre_nomina."&flag=1&ficha=".$ficha."&codt=".$_SESSION['codigo_nomina']."&pagina=".$pagina);
else
    header("Location: movimientos_nomina_pago.php?codigo_nomina=".$nombre_nomina."&flag=1&ficha=".$ficha."&codt=".$_SESSION['codigo_nomina']."&des=".$ficha."&buscar=".$ficha);
}
else
    header("Location: movimientos_nomina_liquidaciones.php?codigo_nomina=".$nombre_nomina."&pagina=".$pagina."&bandera=1");
	





?>
