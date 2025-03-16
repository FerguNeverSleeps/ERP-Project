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
        <link href="../../../includes/assets/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css" />
        <link href="../../../includes/assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="../../../includes/assets/plugins/bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet" type="text/css" />
        <!-- END GLOBAL MANDATORY STYLES -->
        <!-- BEGIN PAGE LEVEL PLUGINS -->
        <link href="../../../includes/assets/plugins/select2/4.0.13/css/select2.css" rel="stylesheet" type="text/css" />
        <!--<link href="../../../includes/assets/plugins/select2/select2-bootstrap.css" rel="stylesheet" type="text/css" />-->
        <!-- END PAGE LEVEL PLUGINS -->
        <!-- BEGIN THEME GLOBAL STYLES -->
        <link href="../../../includes/assets/css/components.css" rel="stylesheet" id="style_components" type="text/css" />
        <link href="../../../includes/assets/css/plugins.css" rel="stylesheet" type="text/css" />
        <!-- END THEME GLOBAL STYLES -->
        <!-- BEGIN PAGE LEVEL STYLES -->
        <link href="../../../includes/assets/css/login-5.css" rel="stylesheet" type="text/css" />
        <!-- END PAGE LEVEL STYLES -->
        <!-- BEGIN THEME LAYOUT STYLES -->
        <!-- END THEME LAYOUT STYLES -->
<link rel="shortcut icon" href="../../imagenes/logo.ico" />
    <!-- END HEAD -->

    <body class=" login">
        <!-- BEGIN : LOGIN PAGE 5-2 -->
        <div class="user-login-5">
            <div class="row bs-reset">
                <div class="col-md-6 login-container bs-reset">
                    <div class="login-content">
                    <img class="login-logo login-6" src="{$LOGO}" width="50%" height="15%"/><BR>
                        <h2 class="text-center"><!-- SISTEMA DE RECURSOS HUMANOS--> </h2>
                        <form action="javascript:;" class="login-form" method="post" id="form-login">
                            <div class="row bs-reset">
                            
                                <div class="alert alert-danger display-hide" style="padding: 10px;" id="alerts">
                                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>

                                    <span>
                                            Usuario o contraseña inválida. Verifique. Por Favor.
                                    </span>
                                </div>
                                {if $TIPO!= "" AND $ERROR!= ""}
                                <div class="alert alert-{$TIPO}" id="alerta_olvido">
                                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                                    <span>{$ERROR}</span>
                                </div>
                                {/if}
                            </div>
                            <input type="hidden" value="{$REGISTRO} ?>" id="registro" >
                            <div class="row">
                                <div class="col-xs-6">
                                    <input class="form-control form-control-solid placeholder-no-fix form-group" type="text" autocomplete="off" placeholder="Usuario" name="txtUsuario" id="txtUsuario" required/> </div>
                                <div class="col-xs-6">
                                    <input class="form-control form-control-solid placeholder-no-fix form-group" type="password" autocomplete="off" placeholder="Contraseña" name="txtContrasena" id="txtContrasena" required/> </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-8">
                                        <!-- <i class="fa fa-check"></i> -->
                                        <label class="control-label visible-ie8 visible-ie9">Organizaci&oacute;n</label>
                                        <select name="empresaSeleccionada" id="empresaSeleccionada" class="form-control" style="border:1px !important;border-bottom:1px solid #a0a9b4 !important;">
                                        </select>
                                </div>
                            </div>
                            <div class="row bs-reset">
                                <div class="col-sm-8 text-right">
                                    
                                    &nbsp;
                                </div>
                            </div>
                            <div class="row bs-reset">
                                <div class="col-sm-4 text-left">
                                    <div class="forgot-password">
                                        <a href="javascript:;" id="forget-password" class="forget-password">Olvido su clave?</a>
                                    </div>                                    
                                </div>
                                <div class="col-sm-4 text-right">
                                    <button class="btn blue" id="btnEnviar" type="submit">ENTRAR</button>
                                </div>
                            </div>
                        </form>
                        <!-- BEGIN FORGOT PASSWORD FORM -->
                        <form class="forget-form"  method="post" id="form-forget" action="login_reset.php">
                            <h3>Olvido su clave ?</h3>
                            <p> Ingrese su usuario para restaurar su clave de acceso </p>
                            <div class="form-group">
                                <input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="Usuario" name="usuario_forget" id="usuario_forget" />
                            </div>
                            <div class="form-group">
                                <input class="form-control placeholder-no-fix" type="email" autocomplete="off" placeholder="Correo Electrónico" name="correo_forget" id="correo_forget" />
                            </div>
                            <div class="form-actions">
                                <button type="button" id="back-btn" class="btn blue btn-outline">Atras</button>
                                <button type="submit" id="enviar-form" class="btn blue uppercase pull-right">Enviar</button>
                            </div>
                        </form>
                        <!-- END FORGOT PASSWORD FORM -->

                    </div>
                    <div class="login-footer">
                        <div class="row bs-reset">
                            <div class="col-xs-5 bs-reset">
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
                            </div>
                            <div class="col-xs-7 bs-reset">
                                <div class="login-copyright text-right">
                                    <p>Copyright &copy; AMAXONIA ERP {$year}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 bs-reset">
                    <div class="login-bg"> </div>
                </div>
            </div>
        </div>
        <!-- END : LOGIN PAGE 5-2 -->
        <!--[if lt IE 9]>
<script src="../../../includes/assets/plugins/respond.min.js"></script>
<script src="../../../includes/assets/plugins/excanvas.min.js"></script> 
<script src="../../../includes/assets/plugins/ie8.fix.min.js"></script> 
<![endif]-->
        <!-- BEGIN CORE PLUGINS -->
        <script src="../../../includes/assets/plugins/jquery.js" type="text/javascript"></script>
        <script src="../../../includes/assets/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
        <script src="../../../includes/assets/plugins/js.cookie.min.js" type="text/javascript"></script>
        <script src="../../../includes/assets/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
        <script src="../../../includes/assets/plugins/jquery.blockui.min.js" type="text/javascript"></script>
        <script src="../../../includes/assets/plugins/bootstrap-switch/js/bootstrap-switch.min.js" type="text/javascript"></script>
        <!-- END CORE PLUGINS -->
        <!-- BEGIN PAGE LEVEL PLUGINS -->
        <script src="../../../includes/assets/plugins/jquery-validation/dist/jquery.validate.min.js" type="text/javascript"></script>
        <script src="../../../includes/assets/plugins/jquery-validation/dist/additional-methods.min.js" type="text/javascript"></script>
        <script src="../../../includes/assets/plugins/select2/4.0.13/js/select2.js" type="text/javascript"></script>
        <script src="../../../includes/assets/plugins/backstretch/jquery.backstretch.min.js" type="text/javascript"></script>
        <!-- END PAGE LEVEL PLUGINS -->
        <!-- BEGIN THEME GLOBAL SCRIPTS
        <script src="../../../includes/assets/scripts/app.min.js" type="text/javascript"></script>-->
        <!-- END THEME GLOBAL SCRIPTS -->
        <!-- BEGIN PAGE LEVEL SCRIPTS -->
        <script src="../../../includes/assets/scripts/custom/login_new.js" type="text/javascript"></script>
        <!-- END PAGE LEVEL SCRIPTS -->
        <!-- BEGIN THEME LAYOUT SCRIPTS -->
        <!-- END THEME LAYOUT SCRIPTS -->
        {literal}
        <script>
                jQuery(document).ready(function() {   
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
        {/literal}
    </body>

</html>