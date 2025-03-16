<?php
if (!isset($_SESSION)) {
    session_start();
    ob_start();
}
//echo $_SESSION['EmpresaContabilidad'];
//echo $_SESSION['EmpresaNomina'];
//echo $_SESSION['EmpresaFacturacion'];
error_reporting(E_ALL^E_NOTICE);
define('DB_USUARIO','root', true);
define('DB_CLAVE', '', true);
define('DB_HOST', '127.0.0.1', true);
define('DB_PUERTO', '3306', true);
define('DB_SELECTRA_BIE', '', true);
define('DB_SELECTRA_DEFAULT', 'selectra_conf_pyme', true);
define('SELECTRA_CONF_PYME', 'planilla_configuracion',true);
define('SUGARCRM', 'sugarcrm',true);
define('REGISTRO', 0,true);
define('LOGO', "../../../logo_amx.png",true);
define('LOGO_SELECCION', "../../log_amx.png",true);
define('USUARIO_EMPRESA','admin', true);
define('USUARIO_EMPRESA2','zrivera', true);
define('VALIDAR_EMPLEADOS','SI', true);

define('VALIDAR_LICENCIA','1', true);

if (isset($_SESSION['EmpresaContabilidad']))
 define('DB_SELECTRA_CONT',$_SESSION['EmpresaContabilidad'], true);
if (isset($_SESSION['bd']))
 	define('DB_SELECTRA_NOM',$_SESSION['bd'], true);
else
	define('DB_SELECTRA_NOM', 'clubyatepesca_rrhh', true);
if (isset($_SESSION['EmpresaFacturacion']))
define('DB_SELECTRA_FAC',$_SESSION['EmpresaFacturacion'], true);
$_SESSION['ROOT_PROYECTO']= str_replace('\\', '/' , dirname(__FILE__) );  
//$_SESSION['ROOT_PROYECTO']= $_SERVER['DOCUMENT_ROOT']."/selectra_planilla"; // debe especificarse el nivel donde está instalada la aplicacion con respecto al root del sitio
$_SESSION['LIVEURL']= "http://planilla.amaxonialatin.cloud"; // CAMBIAR EN PRODUCCION
$_SESSION['PATH']= "http://planilla.amaxonialatin.cloud"; // CAMBIAR EN PRODUCCION
/*
 * CONSTANTES UTILIZADAS POR LA INTERFAZ DE REGISTRO DE EVENTOS (LOG)
 */
define('PATH_SF',"{$_SESSION['LIVEURL']}/solucion/web/pyme.php/", true);

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
/**
 * $config es un "por ahora", mientras se define donde va a residir la
 * configuracion general de selectraasdf aadfa
 **/
 $ConnSys = array('server' => DB_HOST, 'user' => DB_USUARIO, 'pass' => DB_CLAVE, 'db' => DB_SELECTRA_DEFAULT);
 $config['bd']='mysql';
 /** Archivo _temporal_ para ir colocando las funciones generales... :/ */
 //require_once($_SERVER['DOCUMENT_ROOT'].'/funciones.inc.php');
 require_once('funciones.inc.php');




?>
