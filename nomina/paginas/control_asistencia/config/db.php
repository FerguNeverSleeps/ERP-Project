<?php
$ruta = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$ruta = str_replace('\\', '/', $ruta);
include $ruta.'/generalp.config.inc.php';
$params = array(
    'dbname'   => "demo_planilla",
    'user'     => DB_USUARIO,
    'password' => DB_CLAVE,
    'host'     => DB_HOST,
    'driver'   => 'pdo_mysql', 
	'charset'  => 'utf8',    
);

$conexion = new mysqli( $params['host'], $params['user'], $params['password'], $params['dbname'] );

/* verificar la conexión */
if ($conexion->connect_errno) {
    printf("Conexión fallida: %s\n", $mysqli->connect_error);
    exit();
}

?>
