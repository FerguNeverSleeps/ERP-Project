<?php
$rutaFile = dirname(dirname(__FILE__));
$rutaFile = str_replace('\\', '/' , $rutaFile);
require_once($rutaFile . '/generalp.config.inc.php');
//require_once($_SERVER['DOCUMENT_ROOT'].'/selectra_planilla/generalp.config.inc.php');
$ConnSys = array('server' => DB_HOST, 'user' =>  DB_USUARIO, 'pass' => DB_CLAVE, 'db' => $_SESSION['bd']);
?>
