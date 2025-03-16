<?php 
require_once '../../../generalp.config.inc.php';
session_start();
ob_start();

//error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require_once '../../lib/common.php';
include ("../func_bd.php");
$conexion    = conexion();

$registro_id = $_POST['registro_id']; 
$op          = $_POST['op'];

if ($op==3) //Se presiono el boton de Eliminar
{ 
	$query="DELETE FROM nompersonal_pry_cc WHERE id='$registro_id'";      
	$result=sql_ejecutar($query);
	activar_pagina("proyecto_cc.php");   
}  

?>
