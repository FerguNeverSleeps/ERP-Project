<?php 
echo "Consultamos la configuracion del formato de reloj seleccionado por el usuario <br>";

$resultado = $conexion->query( "SELECT * FROM `caa_configuracion` WHERE codigo = '".$configuracion."'" );

if( $fila = $resultado->fetch_assoc() )
{
	$delimitador      = str_replace("\\t", "\t", $fila['delimitador']);
	$primera_linea    = $fila['primera_linea'];
	$ignorar_columnas = $fila['ignorar_columnas'];
	$filas_vacias     = $fila['filas_vacias'];
	$valor_entrada    = $fila['valor_entrada'];
	$valor_salida     = $fila['valor_salida'];
}

echo "Consultamos los parametros de esta configuracion seleccionada <br>";

$parametros = array();
$resultado = $conexion->query( "SELECT * FROM `caa_parametros` WHERE configuracion = '".$configuracion."'" );
while( $fila = $resultado->fetch_assoc() )
{
	if( $fila['nombre'] == 'dispositivo' ) // Si el nombre del parametro es dispositivo
	{	
		$parametros['dispositivo'][] = $fila['posicion'];
	}
	else{
		$parametros[$fila['nombre']] = $fila['posicion'];
	}
	if(!empty($fila['formato'])) $parametros['formato_'.$fila['nombre']] = $fila['formato'];
}
echo "Se termino de cargar la configuracion seleccionada <br>";
?>