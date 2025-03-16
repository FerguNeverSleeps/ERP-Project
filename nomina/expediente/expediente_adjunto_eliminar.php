<?php 
session_start();
	
//DECLARACION DE LIBRERIAS
require_once '../lib/common.php';
require_once '../paginas/func_bd.php';
include ("../paginas/funciones_nomina.php");
//include ("../header.php");

include ("../header4.php");
$conexion=conexion();

//echo "CODIGO: "; echo $_REQUEST['cod_eliminar'];
if(isset($_REQUEST['cod_eliminar']))
    $codigo=$_REQUEST['cod_eliminar'];



$consulta_adjunto="SELECT * FROM expediente_adjunto WHERE id_adjunto='$codigo'";

$resultado_adjunto=query($consulta_adjunto,$conexion);
$fetch_adjunto=fetch_array($resultado_adjunto,$conexion);

$filas_adjunto  = count($fetch_adjunto);

$codigo_expediente = $fetch_adjunto['cod_expediente_det'];

$consulta_expediente="SELECT * FROM expediente WHERE cod_expediente_det='$codigo_expediente'";

$resultado_expediente=query($consulta_expediente,$conexion);
$fetch_expediente=fetch_array($resultado_expediente,$conexion);

$cedula = $fetch_expediente['cedula'];

//echo " cedula: "; echo $cedula;
//echo " expediente: "; echo $codigo_expediente;
//
//exit;


if ($filas_adjunto)
{
    $consulta="DELETE FROM expediente_adjunto WHERE id_adjunto='$codigo'";
    $resultado=query($consulta,$conexion);   
}


activar_pagina("expediente_adjunto.php?cedula=$cedula&codigo=$codigo_expediente");


?>


