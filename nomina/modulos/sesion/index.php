<?php
date_default_timezone_set("America/Panama");
ini_set("display_errors", 1);
if (!isset($_SESSION)) {
    session_start();
    ob_start();
}

if (isset($_POST['empresaSeleccionada'])) {
    $_SESSION['EmpresaFacturacion'] = $_POST['empresaSeleccionada']; #Base de datos
}

include_once("../../../config.ini.php");
include_once("../../../generalp.config.inc.php");


class bd
{

    private $servidor = DB_HOST;
    private $usuario = DB_USUARIO;
    private $clave = DB_CLAVE;
    private $puerto = DB_PUERTO;
    private $base;
    private $conn;

    public function bd($var)
    {
        $this->base = $var;
    }

    public function create_database($bdnueva)
    {
        $conexion = new mysqli($this->servidor, $this->usuario, $this->clave, $this->puerto);
        $this->conn = $conexion;
        if (mysqli_connect_errno()) {
            printf("Connect failed: %s\n", mysqli_connect_error());
            return;
        }
        $sentencia = "create database " . $bdnueva;
        $resultado = $conexion->query($sentencia);
    }

    private function connect()
    {
        $conexion = new mysqli($this->servidor, $this->usuario, $this->clave, $this->base, $this->puerto);
        $this->conn = $conexion;
        if (mysqli_connect_errno()) {
            printf("Fallo de conexion: %s\n", mysqli_connect_error());
        }
        return $conexion;
    }

    public function query($sentencia)
    {
        $conexion = $this->connect();
        $resultado = $conexion->query($sentencia);

        if (!$resultado) {
            printf("Error: %s\n", $conexion->error);
        }
        $conexion->close();
        return $resultado;
    }

    public function multi_query($sentencia)
    {
        $conexion = $this->connect();
        $resultado = $conexion->multi_query($sentencia);

        if (!$resultado) {
            printf("Error: %s\n", $conexion->error);
        }
        $conexion->close();
        return $resultado;
    }

    public function fetch($resultado)
    {
        return $resultado->fetch_array();
    }
}
$error = isset($_GET['error']) ? $_GET['error'] : '';
$tipo = isset($_GET['tipo']) ? $_GET['tipo'] : '';

include_once("../../../general.config.inc.php");

$empresas = new bd(SELECTRA_CONF_PYME);

$query = "SELECT e.*,de.nombre_sistema FROM nomempresa e left join datos_empresa de on de.cod_empresa = e.codigo WHERE e.bd <> ''";
$resultado_empresas = $empresas->query($query);
$empresasArray = array();
//$empresasArray [] = $resultado_empresas->fetch_array();
//$smarty->assign("empresas", $resultado_empresas);
while ($row = $resultado_empresas->fetch_array()) {
    $empresasArray[] = $row;
    if ($row['nombre_sistema'] != "") {
        $nombre_sistema = $row['nombre_sistema'];
    }
}
$empresas = $empresasArray;

$login = new Login();
$_POST['empresaSeleccionada'];
$id_empresa = $login->getIdEmpresa($_POST['empresaSeleccionada']);
$acceso = -1;

$permisos = new Permisos();

if (isset($_POST["txtUsuario"])) { //if (isset($_POST["submit"])) {
    $resLongin = $login->validarAcceso($_POST['txtUsuario'], $_POST['txtContrasena'], $id_empresa);
    if ($resLongin["error"] == 1) {
        $query = "SELECT cr.*,n.*,de.nombre_sistema,de.nombre_empresa,de.img_izq FROM conf_ejecucion_reporte cr "
            . "inner join nomempresa n on n.bd='" . $_POST['empresaSeleccionada'] . "' ";
        $query .= " left join datos_empresa de on de.cod_empresa = n.codigo   WHERE 1;";

        $resultado_conf_reporte = $empresas->query($query);
        $conf_reporte_lista =  $resultado_conf_reporte->fetch_assoc();
        $acceso = 1;

        $_SESSION['nombre_sistema'] = $conf_reporte_lista['nombre_sistema'];
        $_SESSION['nombre_empresa'] = $conf_reporte_lista['nombre_empresa'];
        $_SESSION["codigo_nomempresa"] = $conf_reporte_lista['codigo'];

        //LEctura de accesos por nivel
        $resultArray = array();
        $query = "SELECT  * from operacion_nivel  WHERE 1;";
        $resultado_nivel_operacion = $empresas->query($query);
        while ($rownivel_operacion =  $resultado_nivel_operacion->fetch_assoc()) {
            $resultArray[$rownivel_operacion['operacion_id']] = $rownivel_operacion['nivel_id'];
        }
        $_SESSION['operacion_nivel'] = json_encode($resultArray);
        //fin lectura de accesos por nivel

        //$smarty->assign("titulo_pagina", $_SESSION['nombre_sistema']." :: Login");
        if (isset($_POST['empresaSeleccionada'])) {
            $_SESSION['EmpresaFacturacion'] = $_POST['empresaSeleccionada']; #Base de datos
            $_SESSION['logo_empresa'] = $conf_reporte_lista['img_izq'];
            $_SESSION['conf_reportes_host'] = $conf_reporte_lista['host'];
            $_SESSION['conf_reportes_puerto'] = $conf_reporte_lista['puerto'];
            $_SESSION['conf_reportes_app'] = $conf_reporte_lista['app'];
            $_SESSION['bd_administrativo'] = $conf_reporte_lista['bd']; #Base de datos
            $_SESSION['bd_contabilidad'] = $conf_reporte_lista['bd_contabilidad']; #Base de datos
            $_SESSION['bd_nomina'] = $conf_reporte_lista['bd_nomina']; #Base de datos
            $_SESSION['nombre_empresa'] = $conf_reporte_lista['nombre']; #Base de datos
            $_SESSION['admis_activo'] = $conf_reporte_lista['admis_activo']; #Base de datos
            $_SESSION['contab_activo'] = $conf_reporte_lista['contab_activo']; #Base de datos
            $_SESSION['nomina_activo'] = $conf_reporte_lista['nomina_activo']; #Base de datos    

            $query = "SELECT * FROM " . $_POST['empresaSeleccionada'] . ".parametros_generales WHERE 1;";
            $resparam = $empresas->query($query);
            $params =  $resparam->fetch_assoc();
            $_SESSION['sucursal'] = $params['sucursal_id'];
        }
        $permiso = $permisos->getPermisosUsuarios($_SESSION['cod_usuario']);
        $modulos = $permisos->getPermisosModulo();

        header("Location: ../../../nomina/paginas/seleccionar_nomina1.php");
    } else {
        $acceso = 0;
    }
}

$titulo_pagina = $nombre_sistema . " :: Login";
$year = date('Y');

if ($login->getIdSessionActual() != "") {
    //header("Location: ../principal/?opt_menu=54");
    header("Location: ../../../nomina/paginas/seleccionar_nomina1.php");
    exit;
} else {
    /*
    if (isset($_GET['mobile'])) {
        $smarty->display("index_mobile.tpl");
    }
    else{
        // $smarty->display("index.tpl");
        // $smarty->display("login_1.tpl");
        // $smarty->display('login_soft.tpl');
    }
    */
?>
    <!DOCTYPE html>
    <!-- 
Template Name: Metronic - Responsive Admin Dashboard Template build with Twitter Bootstrap 3.3.7
Version: 4.7.5
Author: KeenThemes
Website: http://www.keenthemes.com/
Contact: support@keenthemes.com
Follow: www.twitter.com/keenthemes
Dribbble: www.dribbble.com/keenthemes
Like: www.facebook.com/keenthemes
Purchase: http://themeforest.net/item/metronic-responsive-admin-dashboard-template/4021469?ref=keenthemes
Renew Support: http://themeforest.net/item/metronic-responsive-admin-dashboard-template/4021469?ref=keenthemes
License: You must have a valid license purchased only from themeforest(the above link) in order to legally use the theme for your project.
-->
    <!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
    <!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
    <!--[if !IE]><!-->
    <html lang="en">
    <!--<![endif]-->
    <!-- BEGIN HEAD -->

    <head>
        <meta charset="utf-8" />
        <title>.:: AMAXONIA RRHH::.</title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta content="width=device-width, initial-scale=1" name="viewport" />
        <meta content="Preview page of Metronic Admin Theme #1 for " name="description" />
        <meta content="" name="author" />
        <!-- BEGIN GLOBAL MANDATORY STYLES -->
        <!-- <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css" />-->
        <link href="../../../includes/assets/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <!-- <link href="../../../includes/assets/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css" /> -->
        <!-- <link href="../../../includes/assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" /> -->
        <link href="../../../includes/assets/plugins/bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet" type="text/css" />
        <!-- END GLOBAL MANDATORY STYLES -->
        <!-- BEGIN PAGE LEVEL PLUGINS -->
        <link href="../../../includes/assets/plugins/select2/4.0.13/css/select2.css" rel="stylesheet" type="text/css" />
        <link href="../../../includes/assets/plugins/select2/select2-bootstrap.css" rel="stylesheet" type="text/css" />
        <!-- END PAGE LEVEL PLUGINS -->
        <!-- BEGIN THEME GLOBAL STYLES -->
        <!-- <link href="../../../includes/assets/css/components.css" rel="stylesheet" id="style_components" type="text/css" /> -->
        <!-- <link href="../../../includes/assets/css/plugins.css" rel="stylesheet" type="text/css" /> -->
        <!-- END THEME GLOBAL STYLES -->
        <!-- BEGIN PAGE LEVEL STYLES -->
        <link href="../../../includes/assets/css/login-5.css" rel="stylesheet" type="text/css" />
        <!-- END PAGE LEVEL STYLES -->
        <!-- BEGIN THEME LAYOUT STYLES -->
        <!-- END THEME LAYOUT STYLES -->
        <link rel="shortcut icon" href="../../imagenes/logo.ico" />
        <!-- INICIO NUEVOS ESTILOS -->
        <script src="../../js/tailwindcss.js"></script>
        <style>
            /* Fondo de la página */
            .background-content {
                background-image: url('../../imagenes/trade/fondo1.png');
            }

            /* Loader con fondo translúcido */
            #loader {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                display: flex;
                align-items: center;
                justify-content: center;
                background-color: rgba(255, 255, 255, .9);
                z-index: 9999;
            }

            /* Animacion tipo Spinner */
            .spinner {
                width: 80px;
                height: 80px;
                border: 8px solid #ccc;
                border-top: 8px solid #1e3a8a;
                border-radius: 50%;
                animation: spin 1.5s linear infinite;
            }

            @keyframes spin {
                0% {
                    transform: rotate(0deg);
                }

                100% {
                    transform: rotate(360deg);
                }
            }
        </style>
        <!-- FIN NUEVOS ESTILOS -->
    </head>

    <body>
        <!-- Loader -->
        <div id="loader">
            <div class="spinner"></div>
        </div>

        <!-- Contenido principal -->
        <div class="h-screen grid grid-cols-1 lg:grid-cols-2">
            <!-- Sección derecha -->
            <div class="bg-white relative flex flex-col items-center">
                <!-- parte del logo -->
                <div class="absolute top-8 left-8">
                    <img src="<?php echo LOGO ?>" alt="Logo" class="w-96">
                </div>

                <!-- Formulario flotante -->
                <div class="mt-24 lg:mt-0 lg:absolute lg:inset-0 lg:flex lg:items-center lg:justify-center">
                    <div class="bg-white p-8 shadow-xl shadow-blue-200/40 w-11/12 max-w-lg border rounded-3xl">
                        <h2 class="text-xl lg:text-4xl font-bold text-gray-800 text-center mb-6">
                            Bienvenido a nuestra comunidad
                        </h2>
                        <p class="text-base text-gray-600 text-center mb-4">
                            Ingrese su usuario y contraseña para iniciar sesión
                        </p>
                        <form action="javascript:;" class="login-form" method="post">
                            <input type="hidden" value="<?php echo REGISTRO; ?>" id="registro">
                            <div
                                id="error-alert"
                                class="hidden bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative"
                                role="alert">
                                <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                                    <button
                                        class="text-red-700 hover:text-red-900"
                                        onclick="document.getElementById('error-alert').classList.add('hidden')">
                                        &times;
                                    </button>
                                </span>
                                <span id="txt_error">Usuario o contraseña inválida. Por Favor, verifique.</span>
                            </div>

                            <?php if ($error != "" && $tipo != "") { ?>
                                <div
                                    id="alerta_olvido"
                                    class="bg-<?= $tipo === 'danger' ? 'red' : 'green'; ?>-100 border border-<?= $tipo === 'danger' ? 'red' : 'green'; ?>-400 text-<?= $tipo === 'danger' ? 'red' : 'green'; ?>-700 px-4 py-3 rounded relative"
                                    role="alert">
                                    <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                                        <button
                                            class="text-<?= $tipo === 'danger' ? 'red' : 'green'; ?>-700 hover:text-<?= $tipo === 'danger' ? 'red' : 'green'; ?>-900"
                                            onclick="document.getElementById('alerta_olvido').classList.add('hidden')">
                                            &times;
                                        </button>
                                    </span>
                                    <span><?= $error; ?></span>
                                </div>
                            <?php } ?>
                            <div class="mb-4">
                                <div class="relative z-0">
                                    <input
                                        type="text"
                                        id="txtUsuario"
                                        name="txtUsuario"
                                        autocomplete="off"
                                        class="block pl-2 py-1.5 px-0 w-full text-lg text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                                        placeholder=" " />
                                    <label for="txtUsuario" class="absolute text-xl text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-1 -z-10 origin-[0] peer-focus:start-0 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto">Usuario</label>
                                    <span
                                        class="absolute inset-y-0 right-3 flex items-center pl-3 text-gray-400 pointer-events-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 12c2.485 0 4.5-2.015 4.5-4.5S14.485 3 12 3 7.5 5.015 7.5 7.5 9.515 12 12 12z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 19.5a6.992 6.992 0 0112 0" />
                                        </svg>
                                    </span>
                                </div>
                            </div>
                            <div class="mb-4">
                                <div class="relative z-0">
                                    <input
                                        type="password"
                                        id="txtContrasena"
                                        name="txtContrasena"
                                        autocomplete="off"
                                        class="block pl-2 py-1.5 px-0 w-full text-lg text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                                        placeholder=" " />
                                    <label for="txtContrasena" class="absolute text-xl text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-1 -z-10 origin-[0] peer-focus:start-0 peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto">Contraseña</label>
                                    <button type="button"
                                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400"
                                        onclick="togglePassword()">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.478 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <div class="mb-4 mt-[1.6rem]">
                                <select
                                    name="empresaSeleccionada"
                                    id="empresaSeleccionada"
                                    class="block py-2.5 px-0 w-full text-sm text-gray-500 bg-transparent border-0 border-b-2 border-gray-200 appearance-none dark:text-gray-400 dark:border-gray-700 focus:outline-none focus:ring-0 focus:border-gray-200 peer">
                                </select>
                            </div>
                            <div class="flex items-center justify-center mb-4">
                                <a href="javascript:;" id="forget-password" class="forget-password text-base text-blue-600 hover:underline">¿Has olvidado tu contraseña?</a>
                            </div>
                            <button type="submit" id="btnEnviar"
                                class="w-full bg-blue-600 text-white font-bold py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                Entrar
                            </button>
                        </form>
                        <!-- BEGIN FORGOT PASSWORD FORM -->
                        <form class="forget-form bg-white p-8 rounded-lg shadow-lg w-11/12 max-w-lg mx-auto" method="post" id="form-forget" action="login_reset.php">
                            <h3 class="text-2xl font-bold text-gray-800 mb-4 text-center">
                                ¿Olvidaste tu contraseña?
                            </h3>
                            <p class="text-base text-gray-600 text-center mb-6">
                                Ingresa tu usuario y correo electrónico para restaurar tu clave de acceso.
                            </p>

                            <div class="mb-4">
                                <input
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 bg-white text-gray-700 placeholder-gray-400"
                                    type="text"
                                    autocomplete="off"
                                    placeholder="Usuario"
                                    name="usuario_forget"
                                    id="usuario_forget"
                                    required />
                            </div>

                            <div class="mb-6">
                                <input
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 bg-white text-gray-700 placeholder-gray-400"
                                    type="email"
                                    autocomplete="off"
                                    placeholder="Correo Electrónico"
                                    name="correo_forget"
                                    id="correo_forget"
                                    required />
                            </div>

                            <div class="flex justify-between items-center">
                                <button
                                    type="button"
                                    id="back-btn"
                                    class="bg-transparent border border-blue-600 text-blue-600 font-semibold py-2 px-4 rounded-md hover:bg-blue-600 hover:text-white focus:outline-none focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    Atrás
                                </button>
                                <button
                                    type="submit"
                                    id="enviar-form"
                                    class="bg-blue-600 text-white font-bold py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    Enviar
                                </button>
                            </div>
                        </form>
                        <!-- END FORGOT PASSWORD FORM -->
                    </div>
                </div>
            </div>

            <!-- Sección izquierda -->
            <div class="lg:block bg-cover bg-center background-content"></div>
        </div>

        <!-- INICIO DE CARGA DE ARCHIVOS Y FUNCIONES JS -->
        <script src="../../../includes/assets/plugins/jquery.js" type="text/javascript"></script>
        <!-- BEGIN PAGE LEVEL SCRIPTS -->
        <script src="../../../includes/assets/scripts/custom/login_new.js" type="text/javascript"></script>
        <!-- END CORE PLUGINS -->
        <!-- BEGIN PAGE LEVEL PLUGINS -->
        <script src="../../../includes/assets/plugins/jquery-validation/dist/jquery.validate.min.js" type="text/javascript"></script>
        <script src="../../../includes/assets/plugins/select2/4.0.13/js/select2.js" type="text/javascript"></script>
        <script src="../../../includes/assets/plugins/backstretch/jquery.backstretch.min.js" type="text/javascript"></script>
        <!-- END PAGE LEVEL PLUGINS -->
        <script>
            jQuery(document).ready(function() {
                let reg = $("#registro").val();
                if (reg === "0") {
                    $("#reg_link").hide();
                } else {
                    $("#reg_link").on("click", function() {
                        if (reg === "1") {
                            location.href = "index_registro.php";
                        }
                    });
                }
            });

            // Script para controlar el loader
            window.addEventListener('load', () => {
                const loader = document.getElementById('loader');
                loader.style.opacity = '0';
                setTimeout(() => {
                    loader.style.display = 'none';
                    document.body.style.visibility = 'visible';
                }, 500);
            });

            // Función para alternar la visibilidad de la contraseña
            function togglePassword() {
                const passwordInput = document.getElementById('txtContrasena');
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
            }
        </script>
        <!-- FIN DE CARGA DE ARCHIVOS Y FUNCIONES JS -->
    </body>


    <!-- BEGIN : LOGIN PAGE 5-2 -->
    <!-- <div class="user-login-5"> -->
    <!-- <div class="row bs-reset"> -->
    <!-- <div class="col-md-6 login-container bs-reset"> -->
    <!-- <div class="login-content"> -->
    <!-- <img class="login-logo login-6" src="<?php echo LOGO ?>" width="50%" /><BR> -->
    <!-- <h2 class="text-center">SISTEMA DE RECURSOS HUMANOS </h2> -->
    <!-- <form action="javascript:;" class="login-form" method="post"> -->
    <!-- <input type="hidden" value="<?php echo REGISTRO ?>" id="registro"> -->
    <!-- <div class="alert alert-danger display-hide" style="padding: 10px;"> -->
    <!-- <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a> -->
    <!-- <span id="txt_error"> -->
    <!-- Usuario o contraseña inválida. Por Favor, verifique. -->
    <!-- </span> -->
    <!-- </div> -->
    <!-- <?php if ($error != "" and $tipo != "") { ?> -->
    <!-- <div class="alert alert-<?= $tipo ?>" id="alerta_olvido"> -->
    <!-- <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a> -->
    <!-- <span><?= $error ?></span> -->
    <!-- </div> -->
    <!-- <?php } ?> -->
    <!-- <div class="row"> -->
    <!-- <div class="col-xs-6"> -->
    <!-- <input class="form-control form-control-solid placeholder-no-fix form-group" type="text" autocomplete="off" placeholder="Usuario" name="txtUsuario" id="txtUsuario" required /> -->
    <!-- </div> -->
    <!-- <div class="col-xs-6"> -->
    <!-- <input class="form-control form-control-solid placeholder-no-fix form-group" type="password" autocomplete="off" placeholder="Contraseña" name="txtContrasena" id="txtContrasena" required /> -->
    <!-- </div> -->
    <!-- </div> -->
    <!-- <div class="row"> -->
    <!-- <div class="col-xs-8"> -->
    <!-- <label class="control-label visible-ie8 visible-ie9">Organizaci&oacute;n</label> -->
    <!-- <select name="empresaSeleccionada" id="empresaSeleccionada" class="form-control" style="border:1px !important;border-bottom:1px solid #a0a9b4 !important;"> -->
    <!-- </select> -->
    <!-- </div> -->
    <!-- </div> -->
    <!-- <div class="row bs-reset"> -->
    <!-- <div class="col-sm-8 text-right"> -->
    <!-- &nbsp; -->
    <!-- </div> -->
    <!-- </div> -->
    <!-- <div class="row bs-reset"> -->
    <!-- <div class="col-sm-4 text-left"> -->
    <!-- <div class="forgot-password"> -->
    <!-- <a href="javascript:;" id="forget-password" class="forget-password">Olvido su clave?</a> -->
    <!-- </div> -->
    <!-- </div> -->
    <!-- <div class="col-sm-4 text-right"> -->
    <!-- <label class="control-label text-center"><a id="reg_link" data-toggle="tooltip" title="En construcción">Registrarse</a></label> -->
    <!-- </div> -->
    <!-- </div> -->
    <!-- <div class="row bs-reset"> -->
    <!-- <div class="form-actions right"> -->
    <!-- <button type="button" id="btnEnviar" class="btn blue pull-right"> -->
    <!-- ENTRAR -->
    <!-- </button> -->
    <!-- </div> -->
    <!-- </div> -->
    <!-- </form> -->
    <!-- BEGIN FORGOT PASSWORD FORM -->
    <!-- <form class="forget-form" method="post" id="form-forget" action="login_reset.php"> -->
    <!-- <h3>Olvido su clave ?</h3> -->
    <!-- <p> Ingrese su usuario para restaurar su clave de acceso </p> -->
    <!-- <div class="form-group"> -->
    <!-- <input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="Usuario" name="usuario_forget" id="usuario_forget" /> -->
    <!-- </div> -->
    <!-- <div class="form-group"> -->
    <!-- <input class="form-control placeholder-no-fix" type="email" autocomplete="off" placeholder="Correo Electrónico" name="correo_forget" id="correo_forget" /> -->
    <!-- </div> -->
    <!-- <div class="form-actions"> -->
    <!-- <button type="button" id="back-btn" class="btn blue btn-outline">Atras</button> -->
    <!-- <button type="submit" id="enviar-form" class="btn blue uppercase pull-right">Enviar</button> -->
    <!-- </div> -->
    <!-- </form> -->
    <!-- END FORGOT PASSWORD FORM -->

    <!-- </div> -->
    <!-- <div class="login-footer"> -->
    <!-- <div class="row bs-reset"> -->
    <!-- <div class="col-xs-5 bs-reset"> -->
    <!--<ul class="login-social">
                                    <li>
                                        <a href="javascript:;">
                                            <i class="icon-social-facebook"></i>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="javascript:;">
                                            <i class="icon-social-twitter"></i>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="javascript:;">
                                            <i class="icon-social-dribbble"></i>
                                        </a>
                                    </li>
                                </ul>-->
    <!-- </div> -->
    <!-- <div class="col-xs-7 bs-reset"> -->
    <!-- <div class="login-copyright text-right"> -->
    <!-- <p>Copyright &copy; AMAXONIA ERP <?php echo $year ?></p> -->
    <!-- </div> -->
    <!-- </div> -->
    <!-- </div> -->
    <!-- </div> -->
    <!-- </div> -->
    <!-- <div class="col-md-6 bs-reset"> -->
    <!-- <div class="login-bg"> </div> -->
    <!-- </div> -->
    <!-- </div> -->
    <!-- </div> -->
    <!-- BEGIN CORE PLUGINS -->
    <!-- <script src="../../../includes/assets/plugins/jquery.js" type="text/javascript"></script> -->
    <!-- <script src="../../../includes/assets/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script> -->
    <!-- <script src="../../../includes/assets/plugins/js.cookie.min.js" type="text/javascript"></script> -->
    <!-- <script src="../../../includes/assets/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script> -->
    <!-- <script src="../../../includes/assets/plugins/jquery.blockui.min.js" type="text/javascript"></script> -->
    <!-- <script src="../../../includes/assets/plugins/bootstrap-switch/js/bootstrap-switch.min.js" type="text/javascript"></script> -->
    <!-- END CORE PLUGINS -->
    <!-- BEGIN PAGE LEVEL PLUGINS -->
    <!-- <script src="../../../includes/assets/plugins/jquery-validation/dist/jquery.validate.min.js" type="text/javascript"></script> -->
    <!-- <script src="../../../includes/assets/plugins/jquery-validation/dist/additional-methods.min.js" type="text/javascript"></script> -->
    <!-- <script src="../../../includes/assets/plugins/select2/4.0.13/js/select2.js" type="text/javascript"></script> -->
    <!-- <script src="../../../includes/assets/plugins/backstretch/jquery.backstretch.min.js" type="text/javascript"></script> -->
    <!-- END PAGE LEVEL PLUGINS -->
    <!-- BEGIN THEME GLOBAL SCRIPTS
        <script src="../../../includes/assets/scripts/app.min.js" type="text/javascript"></script>-->
    <!-- END THEME GLOBAL SCRIPTS -->
    <!-- BEGIN PAGE LEVEL SCRIPTS -->
    <!-- <script src="../../../includes/assets/scripts/custom/login_new.js" type="text/javascript"></script> -->
    <!-- END PAGE LEVEL SCRIPTS -->
    <!-- BEGIN THEME LAYOUT SCRIPTS -->
    <!-- END THEME LAYOUT SCRIPTS -->

    <!-- <script>
            jQuery(document).ready(function() {
                let reg = $("#registro").val();
                if (reg === "0") {
                    $("#reg_link").hide();
                } else {
                    $("#reg_link").on("click", function() {
                        if (reg === "1") {
                            location.href = "index_registro.php";

                        } else {
                        }
                    });

                }

            });
        </script> -->

    <!-- </body> -->

    </html>
<?php
}
?>