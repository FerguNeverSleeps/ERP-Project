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
{literal}
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
</style>
{/literal}

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

                            <div class="caption" style="font-size: 16px; padding-top: 15px; padding-left: 0px; padding-bottom:5px; float: none; display: block; margin-bottom: 0px;">
                                <!-- <i class="fa fa-reorder"></i> Login -->
                                <img src="../../../includes/assets/img/logo_selectra.png" alt="logo" class="img-responsive" style="height: auto; width: 280px;  margin: 0px auto;" />
                            <!--
                            <div class="caption" style="font-size: 16px; padding-top: 15px; padding-left: 10px; padding-bottom:5px; float: none; display: block; margin-bottom: 0px;">
                                <img src="../../img_sis/logo.png" alt="logo" class="img-responsive" style="width: 170px; height: auto; margin: 0px auto;" />
                            -->
                            </div>
                        </div>
                        <div class="portlet-body form">

                            <form role="form" class="login-form" action="#" method="post">

                                <div class="form-body">

        <div class="alert alert-danger display-hide" style="padding: 10px;">
            <button class="close" data-close="alert" ></button>
            <span>
                 No Autorizado
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
                                </div>
                            </form>
                        </div>
                    </div>

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
{literal}
<script>
        jQuery(document).ready(function() {     
          App.init();
          Login.init();
        });
</script>
{/literal}
<!-- END JAVASCRIPTS -->
</body>
</html>