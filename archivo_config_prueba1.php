<?php
error_reporting(E_ALL^E_NOTICE);
define('DB_USUARIO','root', true);
define('DB_CLAVE', '', true);
define('DB_HOST', 'localhost', true);
define('DB_PUERTO', '3306', true);
define('DB_SELECTRA_CONF', 'PLANILLAEXPRESS_CONF', true);#sisalud_selectraconf
define('DB_SELECTRA_CONT', '', true);
define('DB_SELECTRA_NOM', 'express_planilla', true);
define('DB_SELECTRA_FAC', '', true);
define('SUGARCRM', 'sugarcrm',true);
define('TOMCAT', 'http://localhost:8080/JavaBridge/java/Java.inc', true);
//CONSTANTES UTILIZADAS POR LA INTERFAZ DE REGISTRO DE EVENTOS (LOG)
define('REG_INFO',0, true);
define('REG_LOGIN_OK',1, true);
define('REG_LOGIN_FAIL',2, true);
define('REG_LOGOUT',3, true);
define('REG_SESSION_INVALIDATE',4, true);
define('REG_SESSION_READ_ERROR',5, true);
define('REG_SQL_OK',6, true);
define('REG_SQL_FAIL',7, true);
define('REG_ILLEGAL_ACCESS',8, true);
define('REG_ALL',9, true);
$config['bd']='mysql';
require_once('funciones.inc.php');
?>
