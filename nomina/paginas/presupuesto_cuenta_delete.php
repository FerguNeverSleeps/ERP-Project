<?php
require_once '../../generalp.config.inc.php';
include ("../header.php");
include("../lib/common.php") ;
include("func_bd.php");	

session_start();
ob_start();

$codigo=$_REQUEST['codigo'];
$id=$_REQUEST['id'];
$consulta = "DELETE from presupuesto WHERE id=".$id;

	@$result=sql_ejecutar($consulta);

	if($result)
	{

		echo "<script>alert('Cuenta eliminada correctamente');
		location.href='presupuesto_cuentas_add.php?codigo=".$codigo."&id=".$id."'</script>";
	}
	else
	{
		echo "<script>alert('No se puede eliminar la centa del presupuesto');
		location.href='presupuesto_cuentas_add.php?codigo=".$codigo."&id=".$id."'</script>";
	}
?>