<?php

require_once '../../generalp.config.inc.php';
session_start();
ob_start();

include("../lib/common.php") ;
$codigo = $_POST['codigo'];
$nombre_banco = $_POST['nombre_banco'];
$num_cuenta = $_POST['num_cuenta'];
$tipo_cuenta = $_POST['tipo_cuenta'];
$cta_cont_presup = $_POST['cta_cont_presup'];
$cta_cont_fiscal = $_POST['cta_cont_fiscal'];
$monto_apertura = $_POST['monto_apertura'];
$monto_disponible = $_POST['monto_disponible'];
$fecha = $_POST['fecha'];
$conexion = conexion();

$sql = "UPDATE `nombancos` SET `des_ban` = '$nombre_banco',`cuentacob` = '$num_cuenta',`tipocuenta` = '$tipo_cuenta',`ctacon` = '$cta_cont_presup',`ctaconfis` = '$cta_cont_fiscal',`monto_apertura` = '$monto_apertura',`monto_disponible` = '$monto_disponible',`fecha` = '$fecha' WHERE `cod_ban` = '$codigo'";
$res = query($sql, $conexion);
if($res)
{
	echo "PERFECTO";
}
else
{
	echo "ERROR";
}