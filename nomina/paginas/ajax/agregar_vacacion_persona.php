<?php

$ruta          = dirname(dirname(dirname(dirname(__FILE__))));
$ruta          = str_replace('\\', '/', $ruta);
require_once $ruta.'/generalp.config.inc.php';

$db_user       = DB_USUARIO;
$db_pass       = DB_CLAVE;
$db_name       = $_SESSION['bd'];
$db_host       = DB_HOST;

$cedula        = isset($_GET["cedula"]) ? $_GET["cedula"] : '' ;
$ficha         = isset($_GET["ficha"]) ? $_GET["ficha"] : '' ;
$codigo_nomina = isset($_GET["codigo_nomina"]) ? $_GET["codigo_nomina"] : '' ;
$tipnom        = isset($_GET["tipnom"]) ? $_GET["tipnom"] : '' ;
$vac           = isset($_GET["vac"]) ? $_GET["vac"] : '' ;
$conexion      =  mysqli_connect( $db_host, $db_user, $db_pass, $db_name  ) or die( 'Could not open connection to server' );
$sql="";


foreach ($_GET['periodo'] as $key => $value)
{
	//Se marca el periodo y el codigo de la nomina/planilla que se va a pagar a un colaborador
	$sql .= "UPDATE nom_progvacaciones 
	SET marca = '1'
	WHERE periodo = '{$value}'
	AND ficha ='{$ficha}'
	AND ceduda ='{$cedula}'
	AND tipnom = '{$tipnom}'";


	//Se busca el monto, el saldo de dias, el mes y el anio que se le va a pagar a un colaborador

	$sql_vac = "SELECT monto, saldo_dias, dias_disfrutado,mes, anio from nom_progvacaciones where periodo = '{$value}'
	AND ficha ='{$ficha}'
	AND ceduda ='{$cedula}'
	AND tipnom = '{$tipnom}'";
	
	//Se inserta en la tabla de movimientos de nomina/planilla para luego hacer el cierre de la nomina/planillaç

	$sql2 = "INSERT into nom_movimientos_nomina (codnom, tipnom, codcon, ficha, cedula, codnivel1, monto, unidad, anio, mes, descrip,contractual)
	values ('{$codigo_nomina}','{$tipnom}',114,'{$ficha}','{$cedula}','{$codnivel1}','{$monto}','{$unidad}','{$anio}','{$mes}','{$descrip}',1)";
	$res  = $conexion->query($sql) or die(mysqli_error($conexion));
	//$res2 = $conexion->query($sql2) or die(mysqli_error($conexion));

}

echo $vac," ",$codt," ",$codigo_nomina;
if ($res) 
{
	echo $sql;

	echo "<SCRIPT>alert('Se han agregado las vacaciones al colaborador')</SCRIPT>";
	//header("Location:../movimientos_nomina_vacaciones.php?codigo_nomina={$codigo_nomina}&codt={$codt}&vac=1");

}
else
{
	echo $sql;
	echo "<SCRIPT>alert('No se agregaron las vacaciones al colaborador')</SCRIPT>";
	echo "<SCRIPT>window.location='../movimientos_nomina_vacaciones.php?codigo_nomina=".$codigo_nomina."&codt=".$codt."&vac=".$vac."'</SCRIPT>";
}

?>