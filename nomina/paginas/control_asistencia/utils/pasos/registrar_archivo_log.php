<?php 
echo "Registramos los datos generales del archivo de reloj <br>";

$conexion->query("INSERT INTO `caa_archivos_reloj` (`fecha_registro`, `fecha_inicio`, `fecha_fin`, `configuracion` ) 
		VALUES ('".date('Y-m-d H:i:s')."','".$fecha_inicio."','".$fecha_fin."','".$configuracion."' )");

$max_id = $conexion->query("SELECT MAX(codigo) as id FROM `caa_archivos_reloj`");
$max_id = mysqli_fetch_array($max_id);
$last_id = $max_id['id'];

echo "Paso 1: Se cargo el archivo".$directorio1.$archivo." en la tabla caa_archivos_reloj con el id :". $last_id." <br>";
?>