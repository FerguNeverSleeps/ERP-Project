<?php
session_start();
ob_start();
$config=parse_ini_file("../lib/selectra.ini");
$logout = $config['logout'];
//Obtiene la IP del cliente
function get_client_ip() {
    $ipaddress = '';
    if (getenv('HTTP_CLIENT_IP'))
        $ipaddress = getenv('HTTP_CLIENT_IP');
    else if(getenv('HTTP_X_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    else if(getenv('HTTP_X_FORWARDED'))
        $ipaddress = getenv('HTTP_X_FORWARDED');
    else if(getenv('HTTP_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_FORWARDED_FOR');
    else if(getenv('HTTP_FORWARDED'))
        $ipaddress = getenv('HTTP_FORWARDED');
    else if(getenv('REMOTE_ADDR'))
        $ipaddress = getenv('REMOTE_ADDR');
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
  }
class bd {

    private $servidor = DB_HOST;
    private $usuario = DB_USUARIO;
    private $clave = DB_CLAVE;
    private $puerto = DB_PUERTO;
    private $base;
    private $conn;

    public function bd($var) {
        $this->base = $var;
    }

    public function create_database($bdnueva) {
        $conexion = new mysqli($this->servidor, $this->usuario, $this->clave,$this->puerto);
        $this->conn = $conexion;
        if (mysqli_connect_errno()) {
            printf("Connect failed: %s\n", mysqli_connect_error());
            return;
        }
        $sentencia = "create database " . $bdnueva;
        $resultado = $conexion->query($sentencia);
    }

    private function connect() {
        $conexion = new mysqli($this->servidor, $this->usuario, $this->clave, $this->base,$this->puerto);
        $this->conn = $conexion;
        if (mysqli_connect_errno()) {
            printf("Fallo de conexion: %s\n", mysqli_connect_error());
        }
        return $conexion;
    }

    public function query($sentencia) {
        $conexion = $this->connect();
        $resultado = $conexion->query($sentencia);

        if (!$resultado) {
            printf("Error: %s\n", $conexion->error);
        }
        $conexion->close();
        return $resultado;
    }

    public function multi_query($sentencia) {
        $conexion = $this->connect();
        $resultado = $conexion->multi_query($sentencia);

        if (!$resultado) {
            printf("Error: %s\n", $conexion->error);
        }
        $conexion->close();
        return $resultado;
    }
    
    public function fetch($resultado){
        return $resultado->fetch_array();
    }

}
//LOG TRANSACCIONES - REGISTRAR LOGOUT
$db1 = new bd($_SESSION['bd_planilla']);
$descripcion_transaccion = 'LOGOUT USUARIO: ' . $_SESSION['usuario'] . ', Empresa: '. $_POST['bd_planilla'].", Acceso Correcto";
$sql_transaccion = "INSERT INTO log_transacciones (cod_log, descripcion, fecha_hora, modulo, url, accion, valor, usuario,host) 
VALUES ('', '".$descripcion_transaccion."', now(), 'Login Usuario', 'logout.php', 'Logout','". $_SESSION['usuario']."','". $_SESSION['usuario']."','".get_client_ip()."')";
$res_transaccion = $db1->query($sql_transaccion);

?>
<?php
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // Always modified
header("Cache-Control: private, no-store, no-cache, must-revalidate"); // HTTP/1.1
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache"); // HTTP/1.0
?>
<?php //include ("ewconfig.php") ?>
<?php include ("func_bd.php") ?>
<?php //include ("advsecu.php") ?>
<?php

if (@$_COOKIE[ewCookieAutoLogin] == "") {
	$_SESSION[] = "";
}

@session_unset();
@session_destroy();

#header("Location:../seleccionar_empresa.php?tabla=nomempresa");
header("Location:../../");
exit();

//../../selectra_prosol/login.php
?>
