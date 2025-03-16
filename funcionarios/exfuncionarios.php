<?php 
$id = $_GET['id'];
session_start();
ob_start();
date_default_timezone_set('America/Panama');

require_once('../nomina/lib/database.php');
$db = new Database($_SESSION['bd']);

$sql1 = "SELECT * FROM nompersonal WHERE personal_id = '$id'";
$result1 = $db->query($sql1, $conexion);
$personal=mysqli_fetch_array($result1);

$sql2 = "UPDATE nompersonal SET estado='Egresado' WHERE personal_id = '$id'";
$result2 = $db->query($sql2, $conexion);

$sql3 = "UPDATE nomposicion SET status='V' WHERE nomposicion_id = ".$personal['nomposicion_id'];
$result3 = $db->query($sql3, $conexion);

//------------------------------------------------------------
header('location:../nomina/paginas/datos_integrantes/listado_integrantes_contraloria.php')
?>