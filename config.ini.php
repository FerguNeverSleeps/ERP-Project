<?php
date_default_timezone_set("America/Panama");
if (!isset($_SESSION)) {
    session_start();
    ob_start();
}

$app_dir = "nomina/";

require_once($app_dir."libs/php/smarty/Smarty.class.php");
require_once($app_dir."libs/php/adodb5/adodb.inc.php");
require_once($app_dir."libs/php/configuracion/config.php");
require_once($app_dir."libs/php/clases/ConexionComun.php");
require_once($app_dir."libs/php/clases/ConexionAdmin.php");
require_once($app_dir."libs/php/clases/login.php");
require_once($app_dir."libs/php/clases/Menu.php");
require_once($app_dir."libs/php/clases/permisos.php");
require_once($app_dir."libs/php/clases/parametrosgenerales.php");
require_once($app_dir."libs/php/clases/comunes.php");
require_once($app_dir."libs/php/clases/Mensajes.php");
$smarty = new Smarty();
$smarty->template_dir = "templates/";
$smarty->compile_dir = "templates_c";
$smarty->config_dir = "configs/";
$smarty->cache_dir = "cache/";
$smarty->caching = false;
$smarty->force_compile = true;
$smarty->compile_check = false;
?>