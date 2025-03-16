<?php

require_once '../../generalp.config.inc.php';
session_start();
ob_start();

include("../lib/common.php") ;

$nombre_banco = $_POST['nombre_banco'];
$num_cuenta = $_POST['num_cuenta'];
$tipo_cuenta = $_POST['tipo_cuenta'];
$cta_cont_presup = $_POST['cta_cont_presup'];
$cta_cont_fiscal = $_POST['cta_cont_fiscal'];
$monto_apertura = $_POST['monto_apertura'];
$monto_disponible = $_POST['monto_disponible'];
$fecha = $_POST['fecha'];
$conexion = conexion();

$sql = "INSERT INTO `nombancos`(`cod_ban`,`des_ban`,`cuentacob`,`tipocuenta`,`ctacon`,`ctaconfis`,`monto_apertura`,`monto_disponible`,`fecha`) VALUES ('','$nombre_banco','$num_cuenta','$tipo_cuenta','$cta_cont_presup','$cta_cont_fiscal','$monto_apertura','$monto_disponible','$fecha')";
$res = query($sql, $conexion);
if($res)
{
	echo "PERFECTO";
}
else
{
	echo "ERROR";
}