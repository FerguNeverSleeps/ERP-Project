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

include_once("../../../config.ini.php"); 
include_once("../../../generalp.config.inc.php"); 


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


include_once("../../../general.config.inc.php"); 

$empresas = new bd(SELECTRA_CONF_PYME);

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
//$smarty->assign("empresas", $empresasArray);

$login = new Login();
$_POST['empresaSeleccionada'];
$id_empresa = $login->getIdEmpresa($_POST['empresaSeleccionada']);
//$smarty->assign("acceso", -1);

$permisos = new Permisos();

if (isset($_POST["txtUsuario"])) {//if (isset($_POST["submit"])) {
    $resLongin = $login->validarAcceso($_POST['txtUsuario'], $_POST['txtContrasena'],$id_empresa);
    if ($resLongin == 1) {
        $query = "SELECT cr.*,n.*,de.nombre_sistema,de.nombre_empresa,de.img_izq FROM conf_ejecucion_reporte cr "
                . "inner join nomempresa n on n.bd='".$_POST['empresaSeleccionada']."' ";
        $query .= " left join datos_empresa de on de.cod_empresa = n.codigo   WHERE 1;";

        $resultado_conf_reporte = $empresas->query($query);
        $conf_reporte_lista =  $resultado_conf_reporte->fetch_assoc();    
        //$smarty->assign("acceso", 1);

        $_SESSION['nombre_sistema'] = $conf_reporte_lista['nombre_sistema']; 
        $_SESSION['nombre_empresa'] = $conf_reporte_lista['nombre_empresa']; 
        $_SESSION["codigo_nomempresa"] = $conf_reporte_lista['codigo'];
        
        //MODULO EXPEDIENTE: SE CREAN ESTAS VARIABLES DE SESION TEMPORALMENTE 
            $_SESSION['add_exp'] = 1;
            $_SESSION['edit_exp'] = 1;
            $_SESSION['adjuntar_exp'] = 1;
            $_SESSION['ver_exp'] = 1;
            $_SESSION['aprobar_exp'] = 1;
            $_SESSION['delete_exp'] = 1;
            
            $_SESSION['exp_per'] = 1;
            $_SESSION['exp_sus'] = 1;
            $_SESSION['exp_ren'] = 1;
            $_SESSION['exp_des'] = 1;
            $_SESSION['exp_mov'] = 1;
            $_SESSION['exp_vac'] = 1;
            $_SESSION['exp_tie'] = 1;
            $_SESSION['exp_doc'] = 1;
            $_SESSION['exp_liccs'] = 1;
            $_SESSION['exp_licss'] = 1;
            $_SESSION['exp_lices'] = 1;
            $_SESSION['exp_aju'] = 1;
            $_SESSION['exp_aum'] = 1;
            $_SESSION['exp_def'] = 1;
            $_SESSION['exp_rei'] = 1;
            $_SESSION['exp_rc'] = 1;
            $_SESSION['exp_aat'] = 1;
            $_SESSION['exp_baja'] = 1;
            $_SESSION['exp_reint'] = 1;

        //LEctura de accesos por nivel
        $resultArray = array();
        $query = "SELECT  * from operacion_nivel  WHERE 1;";
        $resultado_nivel_operacion = $empresas->query($query);
        while($rownivel_operacion =  $resultado_nivel_operacion->fetch_assoc()){
            $resultArray[$rownivel_operacion['operacion_id']] = $rownivel_operacion['nivel_id'];
        }
        $_SESSION['operacion_nivel'] = json_encode($resultArray);
        //fin lectura de accesos por nivel

        //$smarty->assign("titulo_pagina", $_SESSION['nombre_sistema']." :: Login");
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
            $query = "SELECT * FROM ".$_POST['empresaSeleccionada'].".parametros_generales WHERE 1;";
            $resparam = $empresas->query($query);
            $params =  $resparam->fetch_assoc(); 
            $_SESSION['sucursal'] = $params['sucursal_id'];         
        }
        $permiso = $permisos->getPermisosUsuarios($_SESSION['cod_usuario']);    
        $modulos = $permisos->getPermisosModulo();
   
        header("Location: ../../../nomina/paginas/seleccionar_nomina1.php");
    } else {
        //$smarty->assign("acceso", 0);
    }
}

//$smarty->assign("titulo_pagina", $nombre_sistema." :: Login");
//$smarty->assign("nombre_sistema", $nombre_sistema);
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
   // $smarty->caching = false;  
    //$smarty->display("login_1.tpl");  
}
?>

<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en" class="no-js">
<!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
<meta charset="utf-8">
<title>.:: Login ::.</title>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta content="width=device-width, initial-scale=1.0" name="viewport">
<meta content="" name="description">
<meta content="" name="author">

<!-- BEGIN GLOBAL MANDATORY STYLES -->
<!-- <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&amp;subset=all" rel="stylesheet" type="text/css"> -->
<link href="../../libs/css/font-metronic.css" rel="stylesheet" type="text/css"/>
<link href="../../../includes/assets/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
<link href="../../../includes/assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
<link href="../../../includes/assets/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css">
<!-- END GLOBAL MANDATORY STYLES -->
<!-- BEGIN PAGE LEVEL STYLES -->
<link rel="stylesheet" type="text/css" href="../../../includes/assets/plugins/select2/select2.css">
<link rel="stylesheet" type="text/css" href="../../../includes/assets/plugins/select2/select2-metronic.css">


<!-- END PAGE LEVEL STYLES -->
<!-- BEGIN THEME STYLES -->
<link href="../../../includes/assets/css/style-metronic.css" rel="stylesheet" type="text/css">
<link href="../../../includes/assets/css/style.css" rel="stylesheet" type="text/css">
<link href="../../../includes/assets/css/style-responsive.css" rel="stylesheet" type="text/css">
<link href="../../../includes/assets/css/plugins.css" rel="stylesheet" type="text/css">
<link href="../../../includes/assets/css/themes/blue.css" rel="stylesheet" type="text/css">
<link href="../../../includes/assets/css/custom.css" rel="stylesheet" type="text/css">
<!-- END THEME STYLES -->
<link rel="shortcut icon" href="../../imagenes/logo.ico" />

<!-- END JAVASCRIPTS -->

<style type="text/css">
body {
  /*  background: #f7f7f7 url("login.jpg") 50% 50px no-repeat; */

  background: url("../../../includes/assets/img/bg/login.jpg") no-repeat center center fixed; 
  -webkit-background-size: cover;
  -moz-background-size: cover;
  -o-background-size: cover;
  background-size: cover;

  background-color: #E7ECF2 !important;
}

.page-content {
    background: none;
}
.form-actions {
    padding: 0px 10px;
    margin-top: 0px;
    background-color: #ffffff;
    border-top: 0px;
}

.portlet-login{
    background-color: #ffffff;
    max-width: 300px; 
    margin: 0px auto;
}

.header {
    background-color: transparent !important;
}
/*
.input-icon i,  .login .content .select2-container i{
    color: #999;
}*/
/*
input[type="text"]:focus {
    border: 1px solid #56b4ef;
    box-shadow: inset 0 1px 3px rgba(0,0,0,0.05),0 0 8px rgba(82,168,236,0.6);
}
*/

.form-group .input-icon {
    border-left: 2px solid #35aa47 !important;
    border-left: 2px solid #0362fd !important;
}

.form-group .select2-container {
    border-left: 2px solid #35aa47 !important;
    border-left: 2px solid #0362fd !important;
}

.header.navbar .navbar-brand.logo img {
    height: auto;
    width: 170px;
}

.header.navbar .navbar-brand.logo img {
    height: 80px;
    width: auto;
}

@media (max-width: 400px) {
    .header.navbar .navbar-brand.logo img {
        height: 70px;
        width: auto;
    }
}
.btn.blue {
    background-color: #0362fd;
}
.cuadro_imagen{
    font-size: 16px; padding-top: 0px; padding-left: 0px; padding-bottom:0px; float: none; display: block; margin-bottom: 0px;
}
</style>


</head>
<!-- END HEAD -->
<!-- BEGIN BODY -->
<body class="page-header-fixed page-full-width">

    <div class="header navbar navbar-fixed-top mega-menu">
        <div class="header-inner">
            <a class="navbar-brand logo" href="#">
                <!--img src="../../img_sis/logo.png" alt="logo" class="img-responsive" --/-->
            </a>
        </div>
    </div>

<div class="clearfix">
</div>

<div class="page-container">

    <div class="page-content-wrapper">
        <div class="page-content">
            <!-- BEGIN DASHBOARD STATS -->
            <div class="row" style="margin-top: 45px">
                <div class="col-lg-7">
                </div>
                <div class="col-lg-4 col-xs-12">

                    <div class="portlet portlet-login">
                        <div class="portlet-title">

                            <div class="caption" class="cuadro_imagen">
                                <!-- <i class="fa fa-reorder"></i> Login -->
                                <img src="<?php echo LOGO ?>" alt="logo" class="img-responsive" style="height: auto; width: 100%;  margin: 0px auto;" />
                            <!--
                            <div class="caption" style="font-size: 16px; padding-top: 15px; padding-left: 10px; padding-bottom:5px; float: none; display: block; margin-bottom: 0px;">
                                <img src="../../img_sis/logo.png" alt="logo" class="img-responsive" style="width: 170px; height: auto; margin: 0px auto;" />
                            -->
                            </div>
                        </div>
                        <div class="portlet-body form">
                            <span id="form_login">
                            <form role="form" class="login-form" action="#" method="post" >
                                <input type="hidden" value="<?php echo REGISTRO ?>" id="registro" >
                                <div class="form-body">

                                    <div class="alert alert-danger display-hide" style="padding: 10px;">
                                        <button class="close" data-close="alert" ></button>
                                        <span>
                                            No Autorizado
                                        </span>
                                    </div>
                                    <div class="alert alert-success display-hide" style="padding: 10px;">
                                        <button class="close" data-close="alert" ></button>
                                        <span id="mensaje_registro">
                                            
                                        </span>
                                    </div>

                                    <div class="form-group">
                                        <!--ie8, ie9 does not support html5 placeholder, so we just show field title for that-->
                                        <label class="control-label visible-ie8 visible-ie9">Usuario</label>
                                        <div class="input-icon">
                                            <i class="fa fa-user"></i>
                                            <input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="Usuario" 
                                                   name="txtUsuario" id="txtUsuario" />
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label visible-ie8 visible-ie9">Password</label>
                                        <div class="input-icon">
                                            <i class="fa fa-lock"></i>
                                            <input class="form-control placeholder-no-fix" type="password" autocomplete="off" placeholder="Password"
                                                   name="txtContrasena" id="txtContrasena" />
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <!-- <i class="fa fa-check"></i> -->
                                        <label class="control-label visible-ie8 visible-ie9">Organizaci&oacute;n</label>
                                        <select name="empresaSeleccionada" id="empresaSeleccionada" class="select2 form-control">
                                            <option value="">Seleccione una organizaci&oacute;n</option>
                                        </select>
                                    </div>
                                    <div class="form-actions right">
                                        <button  type="button" id="btnEnviar" class="btn blue pull-right">
                                        Login
                                        </button>
                                    </div>    
                                    <div class="form-group">
                                        <label class="control-label visible-ie8 visible-ie9">&nbsp;</label>
                                    </div>      
                                    <div class="form-group">
                                        <label class="control-label text-center"><a id="reg_link" data-toggle="tooltip" title="En construcción">Registrarse</a></label>

                                    </div>                          
                                </div>
                            </form>
                            </span>
                        </div>
                    </div>
                    <div></div>

                </div>
            </div>
            <!-- END DASHBOARD STATS -->
            <div class="clearfix">
            </div>
        </div>
    </div>
    <!-- END CONTENT -->
</div>

<!-- BEGIN JAVASCRIPTS(Load javascripts at bottom, this will reduce page load time) -->
<!-- BEGIN CORE PLUGINS -->
<!--[if lt IE 9]>
    <script src="assets/plugins/respond.min.js"></script>
    <script src="assets/plugins/excanvas.min.js"></script> 
    <![endif]-->
<script src="../../../includes/assets/plugins/jquery-1.10.2.min.js" type="text/javascript"></script>
<script src="../../../includes/assets/plugins/jquery-migrate-1.2.1.min.js" type="text/javascript"></script>
<script src="../../../includes/assets/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<!--

<script src="../../../includes/assets/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js" type="text/javascript"></script>
<script src="../../../includes/assets/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
<script src="../../../includes/assets/plugins/jquery.blockui.min.js" type="text/javascript"></script>
<script src="../../../includes/assets/plugins/jquery.cokie.min.js" type="text/javascript"></script>
<script src="../../../includes/assets/plugins/uniform/jquery.uniform.min.js" type="text/javascript"></script>
-->
<!-- END CORE PLUGINS -->
<!-- BEGIN PAGE LEVEL PLUGINS -->
<script src="../../../includes/assets/plugins/jquery-validation/dist/jquery.validate.min.js" type="text/javascript"></script>
<script type="text/javascript" src="../../../includes/assets/plugins/select2/select2.min.js"></script>
<script type="text/javascript" src="../../../includes/assets/plugins/select2/select2_locale_es.js"></script>
<!-- END PAGE LEVEL PLUGINS -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="../../../includes/assets/scripts/core/app.js" type="text/javascript"></script>
<!-- <script src="../../../includes/assets/scripts/custom/login.js" type="text/javascript"></script> -->
<script src="../../../includes/assets/scripts/custom/login-soft-tpl.js" type="text/javascript"></script>
<!-- END PAGE LEVEL SCRIPTS -->

<script>
    jQuery(document).ready(function() {     
        App.init();
        Login.init();
        var cont_usu = 0;
        let reg = $("#registro").val();
        if(reg === "0"){
            $("#reg_link").hide();
        }
        else
        {
            $("#reg_link").on("click",function(){
                if(reg === "1"){
                    location.href = "index_registro.php";
                    
                }
                else
                {
                    //location.href = "index_registro.php";

                }
            });

        }
    });
</script>

<!-- END JAVASCRIPTS -->
</body>
</html>
