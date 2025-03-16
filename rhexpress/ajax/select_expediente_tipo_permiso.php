<?php
//-------------------------------------------------
session_start();
//-------------------------------------------------
require_once "../config/rhexpress_config.php";
$conexion =  mysqli_connect( DB_HOST, DB_USUARIO, DB_CLAVE, $_SESSION['bd'] ) or die( 'No Hay Conexión con el Servidor de Mysql' );
        mysqli_query($conexion, 'SET CHARACTER SET utf8');
//-------------------------------------------------
//$useruid = $_SESSION['useruid_rhexpress'];
$tipo     = intval($_REQUEST['tipo']);
$cedula = isset($_SESSION['cedula_rhexpress'])? $_SESSION['cedula_rhexpress']: '';
//-------------------------------------------------

$SQL = "SELECT * FROM `expediente_subtipo` WHERE `id_expediente_tipo` = '4'	";
$expediente = $conexion->query($SQL);
while ($tipo = $expediente->fetch_assoc() ){
	echo "<option value='".$tipo["id_expediente_subtipo"]."'>".$tipo["nombre_subtipo"]."</option>";

}



?>