<?php
require_once '../../generalp.config.inc.php';
session_start();
ob_start();
$usuarios = array();
include("../lib/common.php");
date_default_timezone_set('America/Panama');
$db  = new bd(SELECTRA_CONF_PYME);
$sql = "SELECT * from ".SELECTRA_CONF_PYME.".nomusuarios";
$a1  = $db->query($sql);
while ($fila=fetch_array($a1)) 
{
	$usuarios[] = $fila;
}
print_r($usuarios);
//echo $sql,"<br>";