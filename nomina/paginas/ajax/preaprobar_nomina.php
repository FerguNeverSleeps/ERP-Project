<?php 
session_start();
ob_start();
$bd      = $_SESSION['bd'];
//Preaprobar
require_once '../lib/common.php';
$id      = ($_GET['codigo_nomina']) ? $_GET['codigo_nomina'] : '' ;

$bd      = new bd($bd);
$select  = "SELECT status FROM  nom_nominas_pago  WHERE codnom='{$id}' and tipnom='".$_SESSION['codigo_nomina']."'";
$estatus = $bd->query($select)->fetch_array();

if($estatus['status'] == "A")
{
	$consulta = "UPDATE nom_nominas_pago SET status = 'P',fecha_preaprobada = NOW(), usuario_preaprobada = '".$_SESSION['usuario']."' WHERE codnom='{$id}' and tipnom='".$_SESSION['codigo_nomina']."'";
	$res_cons = $bd->query($consulta);
	if ($res_cons) 
	{
		echo "La ".$_SESSION['termino']." ha sido preaprobada";
	}
	else
	{
		echo "Hubo un error al preaprobar la ".$_SESSION['termino'];
	}

}
