<?php
//Valores "SERVER" = "0" o "LOCAL" = "1"
$config = 0;
if ($config == 0) {
	//----------------------------------
	//Conectar desde el servidor
	define('DB', 'asamblea',true);
	define('DB_HOST', 'localhost', true);
	define('DB_CLAVE', 'g1nt3v3n', true);
	define('DB_USUARIO', 'ginteven', true);
}else {
	//----------------------------------
	//Conectar desde local
	define('DB', 'asamblea',true);
	define('DB_HOST', '172.17.145.86', true);
	define('DB_CLAVE', 'Pw$45?^.#', true);
	define('DB_USUARIO', 'selectra', true);
}
$conexion = mysqli_connect(DB_HOST,DB_USUARIO,DB_CLAVE,DB) or die("Error de conexion: ".$config . mysqli_error($conexion));
?>
