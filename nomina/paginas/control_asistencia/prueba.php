<?php
function borrar_archivo($dir){
	$files = glob($dir); // obtiene todos los archivos
	foreach($files as $file){
	  if(is_file($file)) // si se trata de un archivo
	    unlink($file); // lo elimina
	}
}
$archivo = date('Ymd').".log";

$directorio1 = "archivos/";
$directorio2 = "a2/";

$ruta1 = $directorio1.$archivo;
$ruta2 = $directorio2.$archivo;

if (!copy($ruta1, $ruta2)) {
    echo "Error al copiar $fichero...\n";
}
?>