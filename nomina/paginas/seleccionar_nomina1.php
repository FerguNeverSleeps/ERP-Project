<?php
session_start();
//session_destroy();
require_once("func_bd.php") ;
$config = parse_ini_file("../lib/selectra.ini");


    if (empty($_SESSION['bd']))
    {
        ?><script> alert("Disculpe, debe iniciar sesion para acceder"); </script> <?php
        echo '<script language="Javascript">location.href="logout.php"</script>';
    }
    if ($_SESSION['bd'] == "" || !isset($_GET["seleccion_nomina"])) {
        #echo 'salio';exit;
        $_SESSION['bd'] = $config['bdnombre'];
        $_SESSION['termino'] = $config['termino'];
    }
    require_once "../lib/common.php";
 
    
    $seleccion=$_GET["seleccion_nomina"];
    //echo $_GET["seleccion_nomina"];exit;
    if ($seleccion==1){
            /*foreach($_GET['opt'] as $key => $value){
                    $valuetxt=$_GET['opt'];
            }*/
            ///echo $_SESSION['bd'];exit;
            $strsql= "select codtip,descrip from nomtipos_nomina where codtip = '".$_GET['opt']."'";
            $result =sql_ejecutar($strsql);         
            $fila = mysqli_fetch_array($result);
            $_SESSION['codigo_nomina'] = $fila[0];
            $_SESSION['nomina'] = $fila[1];//$valuetxt;
            //echo "nomina: ".$_SESSION['codigo_nomina']." - ".$_SESSION['nomina'];exit;
            activar_pagina("frame.php");
    }
    //echo "nomina: ".$_SESSION['codigo_nomina']." - ".$_SESSION['nomina'];exit;
        $bValidPwd = false;
        $sUsername = $_SESSION['usuario'];
        $sPassword = $_SESSION['clave'];
        //echo $_SESSION['bd'];
        $sSql = new bd($_SESSION['bd']);
        $result = $sSql->query("select bd_nomina,bd_contabilidad,nombre nom_emp from nomempresa where bd_nomina = '".$_SESSION['bd_nomina']."'");
        $filaemp = $result->fetch_assoc();
        $_SESSION["bd"] = $filaemp['bd_nomina'];
        $_SESSION["bdc"] = $filaemp['bd_contabilidad'];
        $_SESSION["nombre_empresa_nomina"] = $filaemp['nom_emp'];

        $sSql = new bd($_SESSION['bd']);
        //echo $_SESSION['bd'];
        //echo "select * from nomusuarios where login_usuario='$sUsername' and clave='".hash("sha256",$sPassword)."'";
        
        $result = $sSql->query("select * from ".SELECTRA_CONF_PYME.".nomusuarios where login_usuario='$sUsername'");// and clave='" . ($sPassword) . "'
        //echo "select * from nomusuarios where login_usuario='$sUsername'";
        //@TODO colocar clave en el query.
        //echo $result->num_rows;
        //exit;
        if ($result->num_rows > 0) {
            //cerrar_conexion($Conn);
            $fila = $result->fetch_assoc();
            $_SESSION['ewSessionStatus'] = "login";            
            $_SESSION['ewSessionUserName'] = $sUsername;
            $_SESSION['nombre'] = $fila["descrip"];
            $_SESSION['coduser'] = $fila["coduser"];
            $_SESSION['id_usuario'] = $fila["coduser"];
            $_SESSION['ewSessionSysAdmin'] = 0; // Non system admin

            $_SESSION['acce_configuracion'] = $fila['acce_configuracion'];
            $_SESSION['acce_usuarios'] = $fila['acce_usuarios'];
            $_SESSION['acce_elegibles'] = $fila['acce_elegibles'];
            $_SESSION['acce_personal'] = $fila['acce_personal'];
            $_SESSION['acce_prestamos'] = $fila['acce_prestamos'];
            $_SESSION['acce_consultas'] = $fila['acce_consultas'];
            $_SESSION['acce_transacciones'] = $fila['acce_transacciones'];
            $_SESSION['acce_procesos'] = $fila['acce_procesos'];
            $_SESSION['acce_reportes'] = $fila['acce_reportes'];
            $_SESSION['acce_enviar_nom'] = $fila['acce_enviar_nom'];
            $_SESSION['acce_autorizar_nom'] = $fila['acce_autorizar_nom'];
            $_SESSION['acce_estuaca'] = $fila['acce_estuaca'];
            $_SESSION['acce_xestuaca'] = $fila['acce_xestuaca'];
            $_SESSION['acce_permisos'] = $fila['acce_permisos'];
            $_SESSION['acce_logros'] = $fila['acce_logros'];
            $_SESSION['acce_penalizacion'] = $fila['acce_penalizacion'];
            $_SESSION['acce_movpe'] = $fila['acce_movpe'];
            $_SESSION['acce_evalde'] = $fila['acce_evalde'];
            $_SESSION['acce_experiencia'] = $fila['acce_experiencia'];
            $_SESSION['acce_antic'] = $fila['acce_antic'];
            $_SESSION['acce_uniforme'] = $fila['acce_uniforme'];
            $_SESSION['acce_generarordennomina'] = $fila['acce_generarordennomina'];
            
            $_SESSION["ewSessionStatus"] = "login";
            $_SESSION['termino'] = $config['termino'];
        } else {
            $_SESSION["ewSessionMessage"] = "no";
        }

?>

<!DOCTYPE html>
<!-- 
Template Name: Metronic - Responsive Admin Dashboard Template build with Twitter Bootstrap 3.1.1
Version: 2.0.2
Author: KeenThemes
Website: http://www.keenthemes.com/
Contact: support@keenthemes.com
Purchase: http://themeforest.net/item/metronic-responsive-admin-dashboard-template/4021469?ref=keenthemes
License: You must have a valid license purchased only from themeforest(the above link) in order to legally use the theme for your project.
-->
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en" class="no-js">
<!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
<meta charset="utf-8"/>
<title>.:: <?php echo $_SESSION['nombre_sistema']." - ".$config['termino']; ?> ::.</title>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<meta content="" name="description"/>
<meta content="" name="author"/>
<!-- BEGIN GLOBAL MANDATORY STYLES -->
<!-- <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css"/> -->
<link href="../libs/css/font-metronic.css" rel="stylesheet" type="text/css"/>
<link href="../../includes/assets/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
<link href="../../includes/assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
<link href="../../includes/assets/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css"/>
<!-- END GLOBAL MANDATORY STYLES -->
<!-- BEGIN PAGE LEVEL STYLES -->
<link rel="stylesheet" type="text/css" href="../../includes/assets/plugins/select2/select2.css"/>
<link rel="stylesheet" type="text/css" href="../../includes/assets/plugins/select2/select2-metronic.css"/>
<!-- END PAGE LEVEL SCRIPTS -->
<!-- BEGIN THEME STYLES -->
<link href="../../includes/assets/css/style-metronic.css" rel="stylesheet" type="text/css"/>
<link href="../../includes/assets/css/style.css" rel="stylesheet" type="text/css"/>
<link href="../../includes/assets/css/style-responsive.css" rel="stylesheet" type="text/css"/>
<link href="../../includes/assets/css/plugins.css" rel="stylesheet" type="text/css"/>
<link href="../../includes/assets/css/themes/default.css" rel="stylesheet" type="text/css" id="style_color"/>
<link href="../../includes/assets/css/pages/login.css" rel="stylesheet" type="text/css"/>
<link href="../../includes/assets/css/custom.css" rel="stylesheet" type="text/css"/>
<!-- END THEME STYLES -->
<link rel="shortcut icon" href="../imagenes/logo.ico" />
<style type="text/css">
body {
  background: url("../../includes/assets/img/bg/1.jpg") no-repeat center center fixed; 
  -webkit-background-size: cover;
  -moz-background-size: cover;
  -o-background-size: cover;
  background-size: cover;
}

.login {
    background-color: #62707f !important;
}

.btn.blue {
    background-color: #0362fd;
}

.content {
    border-radius: 12px !important; 
    border: 1px solid rgba(0,0,0,0.1);
    padding-bottom: 0px !important; 
}

.content .logo{
    margin-top: 0px; 
    padding: 0px;
}

h3.form-title{
    font-size: 18px; 
    font-weight: 400 !important; 
    margin-bottom: 15px;
    margin-top: 5px; 
}

.form-actions{
    border-radius: 12px !important;
}

.login .content .select2-container {
    border-left: 2px solid #0362fd !important; 
}
</style>
</head>
<!-- END HEAD -->
<!-- BEGIN BODY -->
<body class="login">
<!-- BEGIN LOGO --> 
<!--
<div class="logo">
    <a href="#">
        <img src="../../includes/assets/img/logo-PE-login.png" alt=""/>
    </a>
</div>
-->
<div class="logo">&nbsp;</div>
<!-- END LOGO -->
<!-- BEGIN LOGIN -->
<div class="content">
    <div class="logo">
        <a href="#">
            <img src="../../includes/assets/img/logo_selectra.png" alt="" class="img-responsive" style="height: auto; width: 180px;  margin: 0px auto; margin-top: 20px;"/>
        </a>
    </div>
    <!-- BEGIN LOGIN FORM -->
    <form action="" method="post"  name="frmseleccionar" id="frmseleccionar" class="login-form">
        <h3 class="form-title">&nbsp;<?php //echo $config['termino']; ?></h3>
        <div class="form-group">
            <input id="seleccion_nomina" name="seleccion_nomina" type="hidden" value="0">
            <?php
                $strsql= "SELECT n.* 
                          FROM   nomtipos_nomina n 
                          INNER JOIN ".SELECTRA_CONF_PYME.".nomusuario_nomina nu ON nu.id_nomina = n.codtip 
                          AND    nu.id_usuario = '".$_SESSION['cod_usuario'] ."' AND nu.acceso=1";
                $result =sql_ejecutar($strsql);     
            ?>
                <select name="opt" id="opt" class="select2 form-control">
                    <option value="">Seleccione una <?php echo $config['termino']; ?></option>
                    <?php
                            while ($fila = mysqli_fetch_array($result))
                            { ?>
                                 <option value="<?php echo $fila['codtip']; ?>"><?php echo trim($fila['descrip']); ?></option>
                              <?php
                            }
                    ?>
                </select>
        </div>
        <div class="form-actions">
            <button type="button" class="btn blue pull-right" onclick="javascript:VerificarSeleccion();">
            Continuar
            </button>
        </div>
    </form>
    <!-- END LOGIN FORM -->
</div>
<!-- END LOGIN -->
<!-- BEGIN JAVASCRIPTS(Load javascripts at bottom, this will reduce page load time) -->
<!-- BEGIN CORE PLUGINS -->
<!--[if lt IE 9]>
    <script src="../../includes/assets/plugins/respond.min.js"></script>
    <script src="../../includes/assets/plugins/excanvas.min.js"></script> 
    <![endif]-->
<script src="../../includes/assets/plugins/jquery-1.10.2.min.js" type="text/javascript"></script>
<script src="../../includes/assets/plugins/jquery-migrate-1.2.1.min.js" type="text/javascript"></script>
<script src="../../includes/assets/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script src="../../includes/assets/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js" type="text/javascript"></script>
<script src="../../includes/assets/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
<script src="../../includes/assets/plugins/jquery.blockui.min.js" type="text/javascript"></script>
<script src="../../includes/assets/plugins/jquery.cokie.min.js" type="text/javascript"></script>
<script src="../../includes/assets/plugins/uniform/jquery.uniform.min.js" type="text/javascript"></script>
<!-- END CORE PLUGINS -->
<!-- BEGIN PAGE LEVEL PLUGINS -->
<script src="../../includes/assets/plugins/jquery-validation/dist/jquery.validate.min.js" type="text/javascript"></script>
<script type="text/javascript" src="../../includes/assets/plugins/select2/select2.min.js"></script>
<script type="text/javascript" src="../../includes/assets/plugins/select2/select2_locale_es.js"></script>
<!-- END PAGE LEVEL PLUGINS -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="../../includes/assets/scripts/core/app.js" type="text/javascript"></script>
<!-- <script src="../../../includes/assets/scripts/custom/login.js" type="text/javascript"></script> -->
<!-- END PAGE LEVEL SCRIPTS -->
<script>
        jQuery(document).ready(function() {     
          App.init();
            $("#opt").select2({
                language: "es",
              //  placeholder: 'Organizaci&oacute;n', // <i class="fa fa-map-marker"></i>&nbsp;
                allowClear: true,
            });
        });
</script>
<!-- END JAVASCRIPTS -->        
<script> 
    function VerificarSeleccion()
    {
        seleccion=0;

        var opt = $("#opt").val();

        if(opt!='') seleccion=1;   
        
        if (seleccion==0)
            alert("Debe seleccionar un tipo de <?php echo $config['termino']; ?>. Verifique...");
        else
        {
            document.frmseleccionar.seleccion_nomina.value=1;

            var seleccion_nomina = $('#seleccion_nomina').val(); 

            document.location.href = "seleccionar_nomina.php?seleccion_nomina="+seleccion_nomina+"&opt="+opt;
        }
    }
</script>
</body>
</html>