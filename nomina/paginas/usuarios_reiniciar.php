<?php
error_reporting(E_ALL);
date_default_timezone_set('America/Panama');
session_start();
ob_start();
require_once '../../generalp.config.inc.php';
require_once '../lib/common.php';
require_once('../lib/database.php');
require_once('../../configuracion/funciones_generales.php');
$db1 = new Database($_SESSION['bd']);
$db = new Database(SELECTRA_CONF_PYME);
$usuario_creacion = $_SESSION['usuario'];

$a1 = $db->query("DELETE FROM nom_modulos_usuario where coduser='".$_GET["codigo"]."'");
$a2 = $db->query("DELETE FROM nom_paginas_usuario where coduser='".$_GET["codigo"]."'");
$a3 = $db->query("DELETE FROM usuario_permisos where id_usuario='".$_GET["codigo"]."'");

//LOG TRANSACCIONES - REINICIAR PERMISOS DE USUARIO            
$descripcion_transaccion = 'REINICIAR PERMISOS DE USUARIO: ' . $_GET['codigo'];
$client_ip = "";
$sql_transaccion = "INSERT INTO log_transacciones (`descripcion`, `fecha_hora`, `modulo`, `url`, `accion`, `valor`, `usuario`,`host`) 
VALUES ('".$descripcion_transaccion."', now(), 'Reiniciar Permisos de Usuario', 'usuarios_reiniciar.php', 'Reiniciar','".$_GET["codigo"]."','".$usuario_creacion."','".$client_ip."')";

$res_transaccion = $db1->query($sql_transaccion);

$data = "&data=Usuario Reiniciado con exito..!!";
header("location:usuarios_list.php?accion=Reiniciar&".$data);