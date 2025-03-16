<?php
date_default_timezone_set("America/Panama");
ini_set("display_errors", 1);
if (!isset($_SESSION)) {
    session_start();
    ob_start();
}

if(isset($_POST['empresaSeleccionada'])){
    $_SESSION['EmpresaFacturacion'] = $_POST['empresaSeleccionada']; #Base de datos
}

include("../../../config.ini.php"); 
include("../../../generalp.config.inc.php"); 


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


include("../../../general.config.inc.php"); 

$empresas = new bd(SELECTRA_CONF_PYME);

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
$query = "SELECT e.*,de.nombre_sistema FROM nomempresa e left join datos_empresa de on de.cod_empresa = e.codigo WHERE e.bd <> ''";
$resultado_empresas = $empresas->query($query);
$empresasArray = Array();
//$empresasArray [] = $resultado_empresas->fetch_array();
//$smarty->assign("empresas", $resultado_empresas);
while ($row = $resultado_empresas->fetch_array()) {
    $empresasArray [] = $row;
    if($row['nombre_sistema'] != ""){
        $nombre_sistema = $row['nombre_sistema'];
    }
}
$smarty->assign("empresas", $empresasArray);

$login = new Login();
$_POST['empresaSeleccionada'];
$id_empresa = $login->getIdEmpresa($_POST['empresaSeleccionada']);
$smarty->assign("acceso", -1);

$permisos = new Permisos();
ob_clean();
flush();
if (isset($_POST["txtUsuario"])) {//if (isset($_POST["submit"])) {
    $resLongin = $login->validarAcceso($_POST['txtUsuario'], $_POST['txtContrasena'],$_POST['empresaSeleccionada']/*$id_empresa*/);
    if ($resLongin["error"] == 1) {
        $query = "SELECT cr.*,n.*,de.nombre_sistema,de.nombre_empresa,de.img_izq FROM conf_ejecucion_reporte cr "
                . "inner join nomempresa n on n.bd_nomina='".$_POST['empresaSeleccionada']."' ";
        $query .= " left join datos_empresa de on de.cod_empresa = n.codigo   WHERE 1;";

        $resultado_conf_reporte = $empresas->query($query);
        $conf_reporte_lista =  $resultado_conf_reporte->fetch_assoc();   

        if(VALIDAR_LICENCIA == "1"){
            $query = "SELECT de.fecha_vencimiento FROM   nomempresa n  
             LEFT JOIN datos_empresa de on de.cod_empresa = n.codigo   
            WHERE n.bd_nomina='".$_POST['empresaSeleccionada']."';";

            $resultado_datos_empresa = $empresas->query($query);
            $vencimiento =  $resultado_datos_empresa->fetch_assoc(); 

            
            $datetime1 = new DateTime($vencimiento['fecha_vencimiento']);
            $datetime2 = new DateTime(date('Y-m-d'));
            $intervalo = $datetime1->diff($datetime2);
            if($intervalo->invert == 0 ){
                echo json_encode(Array("success" => true,"login" => "0","resp" => "Su licencia se ha vencido", "mensaje" => "Su licencia se ha vencido"));
                return;
            }
        } 
        $smarty->assign("acceso", 1);

        $_SESSION['nombre_sistema'] = $conf_reporte_lista['nombre_sistema']; 
        $_SESSION['nombre_empresa'] = $conf_reporte_lista['nombre_empresa']; 
        $_SESSION["codigo_nomempresa"] = $conf_reporte_lista['codigo'];

        //LEctura de accesos por nivel
        
        /*$resultArray = array();
        $query = "SELECT  * from operacion_nivel  WHERE 1;";
        $resultado_nivel_operacion = $empresas->query($query);
        while($rownivel_operacion =  $resultado_nivel_operacion->fetch_assoc()){
            $resultArray[$rownivel_operacion['operacion_id']] = $rownivel_operacion['nivel_id'];
        }
        $_SESSION['operacion_nivel'] = json_encode($resultArray);
        */
        
        //fin lectura de accesos por nivel

        $smarty->assign("titulo_pagina", $_SESSION['nombre_sistema']." :: Login");
        if (isset($_POST['empresaSeleccionada'])){
            $_SESSION['EmpresaFacturacion'] = $_POST['empresaSeleccionada']; #Base de datos
            $_SESSION['logo_empresa'] = $conf_reporte_lista['img_izq'];
            $_SESSION['conf_reportes_host'] = $conf_reporte_lista['host']; 
            $_SESSION['conf_reportes_puerto'] = $conf_reporte_lista['puerto']; 
            $_SESSION['conf_reportes_app'] = $conf_reporte_lista['app']; 
            $_SESSION['bd_administrativo'] = $conf_reporte_lista['bd'];#Base de datos
            $_SESSION['bd_contabilidad'] = $conf_reporte_lista['bd_contabilidad'];#Base de datos
            $_SESSION['bd_nomina'] = $conf_reporte_lista['bd_nomina'];#Base de datos
            $_SESSION['nombre_empresa'] = $conf_reporte_lista['nombre'];#Base de datos
            $_SESSION['admis_activo'] = $conf_reporte_lista['admis_activo'];#Base de datos
            $_SESSION['contab_activo'] = $conf_reporte_lista['contab_activo'];#Base de datos
            $_SESSION['nomina_activo'] = $conf_reporte_lista['nomina_activo'];#Base de datos    

            $query = "SELECT nom_emp, tipo_empresa FROM ".$_POST['empresaSeleccionada'].".nomempresa;";
            $resparam = $empresas->query($query);
            $params =  $resparam->fetch_assoc(); 
            $_SESSION['sucursal'] = $params['sucursal_id']; 
            $_SESSION["tipo_empresa"] = $params['tipo_empresa'];    
        }
        ///$permiso = $permisos->getPermisosUsuarios($_SESSION['cod_usuario']);    
        //$modulos = $permisos->getPermisosModulo();
        //LOG TRANSACCIONES - REGISTRAR LOGIN
        $db1 = new bd($_POST['empresaSeleccionada']);
        $descripcion_transaccion = 'LOGIN USUARIO: ' . $_POST['txtUsuario'] . ', Empresa: '. $_POST['empresaSeleccionada'].", Acceso Correcto";
        $sql_transaccion = "INSERT INTO log_transacciones ( descripcion, fecha_hora, modulo, url, accion, valor, usuario,host) 
        VALUES ('".$descripcion_transaccion."', now(), 'Login Usuario', 'login.php', 'Login','".$_SESSION['usuario']."','".$_SESSION['usuario']."','".get_client_ip()."')";
        $res_transaccion = $db1->query($sql_transaccion);
        echo json_encode(Array("success" => true,"login" => "1","resp" => $descripcion_transaccion, "mensaje" => $resLongin["mensaje"] ));
    } else {
        
        $db1 = new bd($_POST['empresaSeleccionada']);
        //LOG TRANSACCIONES - REGISTRAR LOGIN
        $descripcion_transaccion = 'LOGIN USUARIO: ' . $_POST['txtUsuario'] . ', Empresa: '. $_POST['empresaSeleccionada'].", Acceso Fallido";
        $sql_transaccion = "INSERT INTO log_transacciones ( descripcion, fecha_hora, modulo, url, accion, valor, usuario,host) 
        VALUES ('".$descripcion_transaccion."', now(), 'Login Usuario', 'login.php', 'Login','".$_SESSION['usuario']."','".$_SESSION['usuario']."','".get_client_ip()."')";
        $res_transaccion = $db1->query($sql_transaccion);
        echo json_encode(Array("success" => true,"login" => "0","resp" => $descripcion_transaccion, "mensaje" => $resLongin["mensaje"]));
    }
}

//$smarty->assign("titulo_pagina", $nombre_sistema." :: Login");
//$smarty->assign("nombre_sistema", $nombre_sistema);


/*if ($login->getIdSessionActual() != "") {
    //header("Location: ../principal/?opt_menu=54");
    header("Location: ../../../nomina/paginas/seleccionar_nomina.php");
    exit;
} else {
    
}*/
?>
