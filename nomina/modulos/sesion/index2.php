<?php

ini_set("display_errors", 1);
if (!isset($_SESSION)) {
    session_start();
    ob_start();
}



require_once("../../config.ini.php");
require_once("../../../generalp.config.inc.php");
require_once(RAIZ_PROYECTO . "/libs/php/clases/producto.php");
require_once("../../../menu_sistemas/lib/common.php");
include("../../../general.config.inc.php");

$conexion = new bd(SELECTRA_CONF_PYME);
$empresas = new bd(SELECTRA_CONF_PYME);

$smarty->assign("acceso", 1);
$_SESSION['EmpresaFacturacion'] ;//= $_POST['empresaSeleccionada']; #Base de datos
header("Location: ../principal/?opt_menu=54");
    
?>
