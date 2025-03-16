<?php

//-------------------------------------------------
session_start();
//-------------------------------------------------
require_once "../config/rhexpress_config.php";
//-------------------------------------------------
//$useruid = $_SESSION['useruid_rhexpress'];
$tipo     = intval($_REQUEST['tipo']);
$cedula = isset($_SESSION['cedula_rhexpress'])? $_SESSION['cedula_rhexpress']: '';
//-------------------------------------------------

$SQL = "SELECT IFNULL(SUM(tiempo), 0) AS tiempo
FROM dias_incapacidad
WHERE cedula= '{$cedula}' AND tipo_justificacion = 3";
$tiempo = $conexion->query($SQL)->fetch_assoc();
echo $tiempo['tiempo'];

?>
