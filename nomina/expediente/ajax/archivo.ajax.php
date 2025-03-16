<?php

require_once('../../lib/database.php');
$id = $_POST['id'];
$tipo = $_POST['tipo'];
$cedula = $_POST['cedula'];
$archivo = $_POST['file'];

$db    = new Database($_SESSION['bd']);
if ($tipo == "documento" ){
    $query = "  DELETE 
                FROM expediente_documento
                WHERE id_documento = '{$id}';";
}

if( $tipo == "adjunto" ){
    $query = "  DELETE
                FROM expediente_adjunto
                WHERE id_adjunto = '{$id}';";
                
}
$dir = "../navegador_archivos/archivos/".$cedula."/".$archivo;
if($db->query($query)){
    
    unlink($dir);
    echo json_encode(array("error" => 0, "mensaje" => "Adjunto eliminado exitosamente"));
}
else{
    echo json_encode(array("error" => 1, "mensaje" => "No se pudo eliminar el adjunto"));

}
?>