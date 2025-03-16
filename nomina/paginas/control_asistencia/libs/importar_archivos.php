<?php
function cargar_archivo($directorio, $max_size, $_files)
{	
	if ($_files["size"] <= $max_size)
	{
		if ($_files["error"] > 0)
			return array(false, "¡Error al subir el archivo! Código: " . $_files["error"], "");
		else
		{
			$archivo = $directorio . time() . '_' . basename($_files['name']);

			if (move_uploaded_file($_files['tmp_name'], $archivo)) 
			    return array(true, "", $archivo);
			else
				return array(false, "¡Error! Al mover el archivo", "");
		}
	}
	else
		return array(false, "¡Error! Límite de tamaño de archivo superado", "");
}

function obtener_extension($archivo)
{
	$partes_nombre = explode(".", $archivo);
	$extension     = end($partes_nombre);	

	return $extension;
}

function verifica_archivo($directorio,$archivo)
{
	//Si es un directorio
	if (is_dir($directorio)) {
	    //Escaneamos el directorio
	    $carpeta = @scandir($directorio);
	    //Miramos si existen archivos
	    if (count($carpeta) > 2){
	        //Miramos si existe el archivo pasado como parámetro
	        if (file_exists($directorio.$archivo)) 
	            return TRUE;
	        else
	            return FALSE;
	    }else{
	        return FALSE;
	    }
	}
	else {
	    return FALSE;
	}
}
function borrar_archivo($dir){
	$files = glob($dir); // obtiene todos los archivos
	foreach($files as $file){
	  if(is_file($file)) // si se trata de un archivo
	    unlink($file); // lo elimina
	}
}