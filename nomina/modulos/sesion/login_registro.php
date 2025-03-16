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
require("../../../nomina/procesos/PHPMailer_5.2.4/class.phpmailer.php");
include("../../../nomina/procesos/PHPMailer_5.2.4/class.smtp.php"); 


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
function limpiarString($string)
{
      $textoLimpio = preg_replace('([^A-Za-z0-9])', '', $string);	     					
      return $textoLimpio;
}
function nombrar_base_datos($string)
{
    $empresa = filter_var(limpiarString($string), FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH | FILTER_FLAG_STRIP_LOW);
    $empresa .= "_planilla";
    $empresa = strtolower($empresa);
    return $empresa;
}
function nombrar_base_datos_ct($string)
{
    $empresa = filter_var(limpiarString($string), FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH | FILTER_FLAG_STRIP_LOW);
    $empresa .= "_contabilidad";
    $empresa = strtolower($empresa);
    return $empresa;
}
function nombrar_base_datos_adm($string)
{
    $empresa = filter_var(limpiarString($string), FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH | FILTER_FLAG_STRIP_LOW);
    $empresa .= "_administrativo";
    $empresa = strtolower($empresa);
    return $empresa;
}


//$output = shell_exec("mysql -u DB_USUARIO -p DB_CLAVE");
$link = mysqli_connect(DB_HOST,DB_USUARIO, DB_CLAVE);
 
// Check connection
if($link === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}
$emp = isset($_POST['txtEmpresa']) ? $_POST['txtEmpresa'] : '' ;
$user = isset($_POST['txtUser']) ? $_POST['txtUser'] : '' ;
$ruc = isset($_POST['txtRuc']) ? $_POST['txtRuc'] : '' ;
$direccion = isset($_POST['txtDireccion']) ? $_POST['txtDireccion'] : '' ;
$telefono = isset($_POST['txtTlf']) ? $_POST['txtTlf'] : '' ;
$email = isset($_POST['txtEmail']) ? $_POST['txtEmail'] : '' ;
$clave = isset($_POST['txtPass']) ? $_POST['txtPass'] : '' ;
$logo = isset($_FILES['txtLogo']) ? $_FILES['txtLogo'] : '' ;
$clave =  hash("sha256",$clave);
$archivo_logo = isset($_FILES['txtLogo']['name']) ? $_FILES['txtLogo']['name'] : '';
$setLogo      = "";
if($archivo_logo!='')
{
    $dir_logo    = "../../../includes/imagenes/";
    if (copy($_FILES['txtLogo']['tmp_name'], $dir_logo . $archivo_logo)) 
    {
        chmod( $dir_logo . $archivo_logo, 0777);
        $insertLogo = $archivo_logo;
        $setLogo    =  $dir_logo . $archivo_logo;
        $mensaje_foto = "Logo agregado con éxito";
    } 
    else
        $mensaje_foto = "Error al subir la foto";
}
$sql_usuario = "SELECT * from nomusuarios where login_usuario = '{$user}'";
$result = $empresas -> query($sql_usuario);
if( $result->num_rows >0)
{
    
    $array = array(
        "mensaje" => "Por favor, seleccione otro nombre de usuario",
        "error" => 2
    );
    echo json_encode($array);
    exit;

}

if(mysqli_query($link, $sql_usuario)){
    $mensaje_bd =  "Base de datos creada exitosamente";
    $error = 1;
} else{
    $error = 0;
    $mensaje_bd = "ERROR: No se pudo crear la base de datos. " . mysqli_error($link);

}

$NOMBRE_BD = nombrar_base_datos($emp);
$NOMBRE_CT = nombrar_base_datos_ct($emp);
$NOMBRE_ADM = nombrar_base_datos_adm($emp);
// Attempt create database query execution
$comando_bd = "CREATE DATABASE ".$NOMBRE_BD." DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci; ";
//$comando_bd .= " USE ".$NOMBRE_BD."; ";
if(mysqli_query($link, $comando_bd)){
    $mensaje_bd =  "Base de datos creada exitosamente";
    $error = 1;
} else{
    $error = 0;
    $mensaje_bd = "ERROR: No se pudo crear la base de datos. " . mysqli_error($link);

}

$script_path = "../../../db/base.sql";

$conex = new bd($NOMBRE_BD);

$buscar_ultima_empresa = "SELECT (MAX(codigo)+1) maximo from ".SELECTRA_CONF_PYME.".nomempresa  ";
$res_max_empresa = $empresas -> query($buscar_ultima_empresa);
$max_empresa = $res_max_empresa->fetch_array();


$sql = file_get_contents($script_path);
$sql .= "UPDATE nomempresa SET nom_emp = '{$emp}',dir_emp='{$direccion}', rif = '{$ruc}',imagen_izq = '{$setLogo}',imagen_izq = '{$setLogo}';";
$conex->multi_query($sql);
//$actualizar_empresa_planilla = "UPDATE ".$NOMBRE_BD.".nomempresa SET nom_emp = '{$emp}',dir_emp='{$direccion}', rif = '{$ruc}',imagen_izq = '{$setLogo}',imagen_izq = '{$setLogo}'";
//$conex -> query($actualizar_empresa_planilla);
//crear empresa en configurador
$crear_empresa = "INSERT INTO ".SELECTRA_CONF_PYME.".nomempresa (codigo,nombre,bd, bd_contabilidad, bd_nomina, nomina_activo) VALUES ('{$max_empresa[maximo]}','{$emp}','{$NOMBRE_ADM}','{$NOMBRE_CT}','{$NOMBRE_BD}', '1')";
$empresas -> query($crear_empresa);

$buscar_empresa_nueva = "SELECT codigo from ".SELECTRA_CONF_PYME.".nomempresa  WHERE bd_nomina = '{$NOMBRE_BD}'";
$res_empresa_nueva = $empresas -> query($buscar_empresa_nueva);
$empresa_nueva = $res_empresa_nueva->fetch_array();

$buscar_datos_empresa = "SELECT (MAX(cod_datos_empresa)+1) maximo from ".SELECTRA_CONF_PYME.".datos_empresa  ";
$res_datos_empresa = $empresas -> query($buscar_datos_empresa);
$max_datos_empresa = $res_datos_empresa->fetch_array();
//crear datos_empresa
$crear_datos_empresa = "INSERT INTO ".SELECTRA_CONF_PYME.".datos_empresa (cod_datos_empresa,cod_empresa,nombre_empresa,direccion,img_izq,cod_moneda,nombre_sistema,pais_id) 
VALUES ('{$max_datos_empresa[maximo]}','{$empresa_nueva[codigo]}','{$emp}','{$direccion}','{$insertLogo}','170','AMX','170')";
$empresas -> query($crear_datos_empresa);

//crear usuario
$buscar_ultimo_usuario = "SELECT (MAX(coduser)+1) maximo from nomusuarios ";
$res_max_user = $empresas -> query($buscar_ultimo_usuario);
$max_user = $res_max_user->fetch_array();
$crear_usuario = "INSERT INTO ".SELECTRA_CONF_PYME.".nomusuarios (coduser,descrip, clave, correo, login_usuario, id_rol)
VALUES ('$max_user[maximo]','{$user}','{$clave}','{$email}','{$user}','1')";
$empresas -> query($crear_usuario);

//



$crear_usuario_empresa =" INSERT INTO nomusuario_empresa (id_usuario, id_empresa) VALUES ('$max_user[maximo]', '$max_empresa[maximo]')";
$empresas -> query($crear_usuario_empresa);
$crear_usuario_planilla =" INSERT INTO nomusuario_nomina (id_usuario, id_nomina, acceso) VALUES ('$max_user[maximo]', '1','1')";
$empresas -> query($crear_usuario_planilla);

$crear_modulos_usuario = "
INSERT INTO `nom_modulos_usuario` ( `coduser`, `cod_modulo`) VALUES
('$max_user[maximo]',	3),
('$max_user[maximo]',	9),
('$max_user[maximo]',	11),
('$max_user[maximo]',	45),
('$max_user[maximo]',	66),
('$max_user[maximo]',	71),
('$max_user[maximo]',	104),
('$max_user[maximo]',	110),
('$max_user[maximo]',	124),
('$max_user[maximo]',	128),
('$max_user[maximo]',	136),
('$max_user[maximo]',	140),
('$max_user[maximo]',	177),
('$max_user[maximo]',	196),
('$max_user[maximo]',	203),
('$max_user[maximo]',	217),
('$max_user[maximo]',	222),
('$max_user[maximo]',	229),
('$max_user[maximo]',	307),
('$max_user[maximo]',	335),
('$max_user[maximo]',	336),
('$max_user[maximo]',	342),
('$max_user[maximo]',	343),
('$max_user[maximo]',	344),
('$max_user[maximo]',	382);";
$empresas -> query($crear_modulos_usuario);

$crear_paginas = "INSERT INTO `nom_paginas_usuario` (`coduser`, `id_pagina`) VALUES
('$max_user[maximo]',	1),
('$max_user[maximo]',	2),
('$max_user[maximo]',	3),
('$max_user[maximo]',	4),
('$max_user[maximo]',	5),
('$max_user[maximo]',	7),
('$max_user[maximo]',	9),
('$max_user[maximo]',	21),
('$max_user[maximo]',	24),
('$max_user[maximo]',	25),
('$max_user[maximo]',	27),
('$max_user[maximo]',	30),
('$max_user[maximo]',	48),
('$max_user[maximo]',	51),
('$max_user[maximo]',	53),
('$max_user[maximo]',	54),
('$max_user[maximo]',	55),
('$max_user[maximo]',	65),
('$max_user[maximo]',	67),
('$max_user[maximo]',	71),
('$max_user[maximo]',	78),
('$max_user[maximo]',	79);";
$empresas -> query($crear_paginas);

$crear_permisos = "INSERT INTO usuario_permisos (id_usuario, id_permiso) VALUES
('$max_user[maximo]',	129),
('$max_user[maximo]',	130),
('$max_user[maximo]',	131),
('$max_user[maximo]',	132),
('$max_user[maximo]',	133),
('$max_user[maximo]',	134),
('$max_user[maximo]',	136),
('$max_user[maximo]',	147),
('$max_user[maximo]',	160),
('$max_user[maximo]',	340),
('$max_user[maximo]',	348),
('$max_user[maximo]',	355),
('$max_user[maximo]',	357),
('$max_user[maximo]',	359),
('$max_user[maximo]',	360),
('$max_user[maximo]',	361),
('$max_user[maximo]',	371),
('$max_user[maximo]',	262),
('$max_user[maximo]',	263),
('$max_user[maximo]',	264),
('$max_user[maximo]',	268),
('$max_user[maximo]',	168),
('$max_user[maximo]',	174),
('$max_user[maximo]',	175),
('$max_user[maximo]',	194),
('$max_user[maximo]',	227),
('$max_user[maximo]',	14),
('$max_user[maximo]',	15),
('$max_user[maximo]',	16),
('$max_user[maximo]',	17),
('$max_user[maximo]',	18),
('$max_user[maximo]',	21),
('$max_user[maximo]',	325),
('$max_user[maximo]',	230),
('$max_user[maximo]',	231),
('$max_user[maximo]',	232),
('$max_user[maximo]',	233),
('$max_user[maximo]',	234),
('$max_user[maximo]',	235),
('$max_user[maximo]',	236),
('$max_user[maximo]',	478),
('$max_user[maximo]',	481),
('$max_user[maximo]',	484);";
$empresas -> query($crear_permisos);
$correo = 'contacto@amaxoniaerp.com';

    //----------------------------------------------------------------------------------------
    if(!empty($correo))
    {

        $mail = new PHPMailer();
        $mail->IsSMTP();
        $mail->SMTPAuth = true;
        $mail->SMTPDebug = 0;
        $mail->Host = "smtp.gmail.com";
        $mail->Port = 587;
        $mail->SMTPSecure = 'tls';

        // El correo y contraseña de donde saldran los mensajes.
        $mail->Username = "planillaexpresspanama@gmail.com";
        $mail->Password = "S3l3ctr4";

        //Indicamos cual es nuestra dirección de correo y el nombre que 
        //queremos que vea el usuario que lee nuestro correo

        $mail->SetFrom("planillaexpresspanama@gmail.com", "Planillaexpress");

        // Asunto de mensaje
        $tit = "Registro de Empresas";

        $mail->Subject = $tit; 
        $cuerpo  = "Empresa: ".$emp."\t";
        $cuerpo .= "RUC: ".$ruc."\t";
        $cuerpo .= "\tUsuario: ".$user."\t";
        $mail->Body = $cuerpo;
        $mail->IsHTML(true);

        // Se asigna la dirección de correo a donde se enviará el mensaje.
            $mail->AddAddress ($correo, 'AMX');   
            $mail->AddAddress ($email, $nombre_completo);   
            //$mail->addCC('contacto@amaxoniaerp.com');

        // $mail->AddAddress('marianna.pessolano@gmail.com', 'Marianna Pessolano');

        // Si hay archivos adjuntos se mandan así.
        $mail->AddAttachment($filename);
        
        // Comprobamos que el correo se ha enviado.
        if( !$mail->Send() ) 
        {
           $error = 0;
           $mensaje_correo = "Error al enviar el correo";
        } 
        else 
        {
            $error = 1;
            $mensaje_correo = "Mensaje enviado exitosamente";
        }
        
        $mail->ClearAddresses();
        $mail->ClearAttachments();
    }

$array = array(
    "mensaje" => $mensaje,
    "mensaje_bd" => $mensaje_bd,
    "mensaje_correo" => $mensaje_correo,
    "mensaje_empresa" => $mensaje_empresa,
    "mensaje_foto" => $mensaje_foto,
    "error" => $error,
);
echo json_encode($array);


?>
