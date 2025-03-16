<?php 
session_start();
ob_start();
?>
<?php
require_once '../lib/common.php';
include ("../header.php");

$fecha = $_GET['fecha'];
$estado = $_GET['estado'];
$dia = $_GET['dia'];
$ficha = $_GET['ficha'];
$per = $_GET['per'];
$turno = $_GET['turno'];


$laborable="lightgray";
$nolaborable="red";
$mediajornada="magenta";
$feriado="green";

$conexion=conexion();	
if($per==1)
{
	$consulta="UPDATE nomcalendarios_personal SET dia_fiesta = '".$estado."', turno_id='".$turno."' where fecha='".$fecha."' AND ficha = '".$ficha."'";
	$resultado=query($consulta,$conexion);
}
else
{
	/*$consulta="UPDATE nomcalendarios_tiposnomina SET dia_fiesta = '".$estado."' where fecha='".$fecha."'";
	$resultado=query($consulta,$conexion);

    $consultap="UPDATE nomcalendarios_personal SET dia_fiesta = '".$estado."' where fecha='".$fecha."'";
	$resultadop=query($consultap,$conexion);*/

	try{		
        $empresa=$_SESSION["nombre_empresa_nomina"];
	$sqldb = "select e.* 
				from ".SELECTRA_CONF_PYME.".nomempresa e 
				where e.nomina_activo='1' and e.nombre='$empresa'";
		$res=query($sqldb, $conexion);
		
		while($fila=fetch_array($res))
		{			
			$consulta1 = "UPDATE ".$fila['bd_nomina'].".nomcalendarios_tiposnomina SET dia_fiesta = '".$estado."' where fecha='".$fecha."'";
			$res1 = query($consulta1,$conexion);

			$consulta2 = "UPDATE ".$fila['bd_nomina'].".nomcalendarios_personal SET dia_fiesta = '".$estado."' where fecha='".$fecha."'";
			$res2 = query($consulta2,$conexion);
		}

	}catch(Exception $e) {
		echo 'Error No: ' . $e->getCode() . 'Error Message:' . $e->getMessage();
	}
}
//echo $consulta;
//echo $consultap;
//exit;

$color=$laborable;
if($estado=="1")
{
	$color=$nolaborable;
}
elseif($estado=="2")
{
	$color=$mediajornada;
}
elseif($estado=="3")
{
	$color=$feriado;
}
