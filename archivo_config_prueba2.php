<?php
if (!isset($_SESSION)) {
session_start();
ob_start();
}
error_reporting(E_ALL^E_NOTICE);
define('DB_USUARIO','root', true);
define('DB_CLAVE', '', true);
define('DB_HOST', 'localhost', true);
define('DB_PUERTO', '3306', true);
define('DB_SELECTRA_BIE', '', true);
define('DB_SELECTRA_DEFAULT', '', true);
define('SELECTRA_CONF_PYME', 'planillaexpress_conf',true);
define('SUGARCRM', 'sugarcrm',true);
if (isset($_SESSION['EmpresaContabilidad']))
define('DB_SELECTRA_CONT',$_SESSION['EmpresaContabilidad'], true);
if (isset($_SESSION['Empresa_Nomina']))
define('DB_SELECTRA_NOM',$_SESSION['EmpresaNomina'], true);
else
define('DB_SELECTRA_NOM', 'express_planilla', true);
if (isset($_SESSION['EmpresaFacturacion']))
define('DB_SELECTRA_FAC',$_SESSION['EmpresaFacturacion'], true);
$_SESSION['ROOT_PROYECTO']= str_replace('\', '/' , dirname(__FILE__) );  
$_SESSION['LIVEURL']= 'http://localhost/selectra_planilla'; // CAMBIAR EN PRODUCCION
define('PATH_SF','{$_SESSION['LIVEURL']}/solucion/web/pyme.php/', true);
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
$ConnSys = array('server' => DB_HOST, 'user' => DB_USUARIO, 'pass' => DB_CLAVE, 'db' => DB_SELECTRA_DEFAULT);
$config['bd']='mysql';
require_once('funciones.inc.php');
?>
