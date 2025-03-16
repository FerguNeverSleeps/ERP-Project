<?php
//-------------------------------------------------
session_start();
//-------------------------------------------------
require_once "config/rhexpress_config.php";
$conexion =  mysqli_connect( DB_HOST, DB_USUARIO, DB_CLAVE, $_SESSION['bd'] ) or die( 'No Hay Conexión con el Servidor de Mysql' );
        mysqli_query($conexion, 'SET CHARACTER SET utf8');
//-------------------------------------------------
$id_solicitud=$_GET['id'];
if (isset($id_solicitud)) {
        $sql="DELETE FROM solicitudes_casos WHERE id_solicitudes_casos='$id_solicitud'";
        if($conexion->query($sql)){
        echo "<script>alert('SOLICITUD DE ANULADA EXITOSAMENTE');location.href='rhexpress_menu.php';</script>";
        }
} else {
        # code...
}

?>