<?php
require_once '../../generalp.config.inc.php';
include ("../header.php");
include("../lib/common.php") ;
include("func_bd.php");	

session_start();
ob_start();

$id=$_REQUEST['id'];
$codigo=$_REQUEST['codigo'];
/*$nuevafecha=explode('-', $fecha);
$fecha = $nuevafecha[2].'-'.$nuevafecha[1].'-'.$nuevafecha[0];*/
$cuenta=$_REQUEST['cuenta'];
$monto_inicial=$_REQUEST['monto_inicial'];
$anio=$_REQUEST['anio'];

$Enero=$_REQUEST['Enero'];
$Febrero=$_REQUEST['Febrero'];
$Marzo=$_REQUEST['Marzo'];
$Mayo=$_REQUEST['Mayo'];
$Abril=$_REQUEST['Abril'];
$Junio=$_REQUEST['Junio'];
$Julio=$_REQUEST['Julio'];
$Agosto=$_REQUEST['Agosto'];
$Septiembre=$_REQUEST['Septiembre'];
$Octubre=$_REQUEST['Octubre'];
$Noviembre=$_REQUEST['Noviembre'];
$Diciembre=$_REQUEST['Diciembre'];


$SQL="UPDATE presupuesto 
SET id_cuenta=".$cuenta.",id_nomnivel=".$cuenta.",anio=".$anio.",Cuenta=".$cuenta.",MontoInicial=".$monto_inicial.",
Enero=".$Enero.",Febrero=".$Febrero.",Marzo=".$Marzo.",Abril=".$Abril.",Mayo=".$Mayo.",Junio=".$Junio.",Julio=".$Julio.",
Agosto=".$Agosto.",Septiembre=".$Septiembre.",Octubre=".$Octubre.",Noviembre=".$Noviembre.",Diciembre=".$Diciembre."
WHERE id=".$id;


/*$SQL="INSERT INTO presupuesto_nivel(codorg_nomnivel".$nivel.", id_cuenta,anio) 
VALUES ('".$codigo."','".$cuenta."','".$anio."')";*/
//echo $SQL,"<br>";
	@$result=sql_ejecutar($SQL);	
	if($result)
	{
		echo "<script>alert('Cuenta Modificada');
		location.href='presupuesto_cuentas_add.php?codigo=".$codigo."&id=".$id."'</script>";
	}
	else
	{
		echo "<script>alert('Hubo un error al editar, verifique los datos nuevamente');
		location.href='presupuesto_cuentas_add.php'</script>";			
	}
?>