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

$SQL = "SELECT id_expediente_tipo,nombre_tipo
FROM expediente_tipo
WHERE id_expediente_tipo in (4,15,16,17)";
$expediente = $conexion->query($SQL);
while ($tipo = $expediente->fetch_assoc() ){
	echo "<option value='".$tipo["id_expediente_tipo"]."'>".$tipo["nombre_tipo"]."</option>";

}



?>