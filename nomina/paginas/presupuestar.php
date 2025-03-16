<?php
require_once '../../generalp.config.inc.php';
include ("../header.php");
include("../lib/common.php") ;
include("func_bd.php");	

session_start();
ob_start();

$id=$_REQUEST['id'];
$nivel=1;
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


$SQL = "INSERT INTO presupuesto(id_cuenta, id_nomnivel, anio, Cuenta, MontoInicial, Enero, Febrero, Marzo, Abril, 
	Mayo, Junio, Julio, Agosto, Septiembre, Octubre, Noviembre, Diciembre) 
VALUES (".$cuenta.",".$id.",".$anio.",".$cuenta.",".$monto_inicial.",".$Enero.",".$Febrero.",".$Marzo.",".$Abril.",
	".$Mayo.",".$Junio.",".$Julio.",".$Agosto.",".$Septiembre.",".$Octubre.",".$Noviembre.",".$Diciembre.")";

/*$SQL="INSERT INTO presupuesto_nivel(codorg_nomnivel".$nivel.", id_cuenta,anio) 
VALUES ('".$codigo."','".$cuenta."','".$anio."')";*/
//echo $SQL,"<br>";
	@$result=sql_ejecutar($SQL);	
	if($result)
	{
		echo "<script>alert('Cuenta agregada al presupuesto exitosamente');
		location.href='presupuesto_cuentas_add.php?codigo=".$id."&nivel=".$nivel."'</script>";
	}
	else
	{
		echo "<script>alert('Hubo un error, verifique los datos nuevamente');
		location.href='presupuesto_cuentas_add.php'</script>";			
	}
?>