<?php
//Valores "SERVER" = "0" o "LOCAL" = "1"
$config = 1;
if ($config == 0) {
	//----------------------------------
	//Conectar desde el servidor
	define('DB', 'demo_planilla',true);
	define('DB_HOST', '127.0.0.1', true);
	define('DB_CLAVE', 'ND2icErvLfDGX5n%Fwcf9gbngMTy6Ai%_I`_%4q#f1b&', true);
	define('DB_USUARIO', 'root', true);
	define('URL', 'location:vistas/',true);
}else {
	//---------------------------------
	//Conectar desde local
	define('DB', 'ingenieria_soluciones_especializadas_planilla',true);
	define('DB_HOST', '127.0.0.1', true);
	define('DB_CLAVE', '', true);
	define('DB_USUARIO', 'root', true);
	define('URL', 'location:vistas/',true);
}
if(!isset($manual)){
	$conexion =  mysqli_connect( DB_HOST, DB_USUARIO, DB_CLAVE, DB ) or die( 'No Hay Conexión con el Servidor de Mysql1' );
	mysqli_query($conexion, 'SET CHARACTER SET utf8');
}
?>
