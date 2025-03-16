<?php
if (isset($_SESSION['usuario_rhexpress']))
{
    header("rhexpress_menu.php");
}
?>
<!DOCTYPE html>
<html lang="en">
    <!-- BEGIN HEAD -->
    <head>
        <?php 
            include("config/dep_css.php");
            include("../generalp.config.inc.php");
            require_once('../nomina/lib/database.php');
            
            $db = new Database(SELECTRA_CONF_PYME);
            //------------------------------------------------------------
            $sql     = "SELECT * FROM datos_empresa";
            $res     = $db->query($sql, $conexion);
            $empresa = mysqli_fetch_array($res);
            //------------------------------------------------------------
            $logo_empresa = "../../includes/imagenes/".$empresa['img_izq'];
        ?>
    </head>
    <!-- END HEAD -->
    <body class=" login">
        <!-- BEGIN : LOGIN PAGE 5-2 -->
        <div class="user-login-5">
            <div class="row bs-reset">
                <div class="col-md-6 login-container bs-reset">
                    <div class="row">
                        <img class="login-logo login-6 img-resposive" src="amaxonia.png" width="50%" style="align-content: center;" />
                    </div>
               
                    <div class="login-content">
                        <h1><strong>:::: PORTAL DEL EMPLEADO ::::</strong></h1>
                        <form action="proceso/login.php" id='login-form' class="login-form" method="post">
                            
                            <div class="alert alert-danger display-hide">
                                <button class="close" data-close="alert"></button>
                                <span>Ingrese Usuario y Clave de acceso.</span>
                            </div>
                            <?php if (isset($_GET['error']) AND !isset($_GET['tipo'])): ?>
                            <div class="alert alert-danger">
                                <button class="close" data-close="alert"></button>
                                <span><?php echo $_GET['error'] ?></span>
                            </div>
                            <?php endif ?>
                            <?php if (isset($_GET['error']) AND isset($_GET['tipo'])): ?>
                            <div class="alert alert-<?= $_GET['tipo'] ?>">
                                <button class="close" data-close="alert"></button>
                                <span><?php echo $_GET['error'] ?></span>
                            </div>
                            <?php endif ?>
                            <div class="row">
                                <!--<div class="col-xs-6">
                                    <input class="form-control form-control-solid placeholder-no-fix form-group" type="text" 
                                    autocomplete="off" placeholder="Nombre de Usuario" id="usuario" name="usuario" required>
                                </div>
                                <div class="col-xs-6">
                                    <input class="form-control form-control-solid placeholder-no-fix form-group" type="password" 
                                    autocomplete="off" placeholder="Clave de Acesso" id="password" name="password" required>
                                </div>-->
                                <div class="col-xs-6">
                                    <input class="form-control form-control-solid placeholder-no-fix form-group" type="text" 
                                    autocomplete="off" placeholder="Usuario" name="txtUsuario" id="txtUsuario" required/> 
                                </div>
                                <div class="col-xs-6">
                                    <input class="form-control form-control-solid placeholder-no-fix form-group" type="password" 
                                    autocomplete="off" placeholder="Contraseña" name="txtContrasena" id="txtContrasena" required/> 
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <label class="control-label visible-ie8 visible-ie9">Organizaci&oacute;n</label>
                                    <select name="empresaSeleccionada" id="empresaSeleccionada" class="form-control" 
                                    style="border:1px !important;border-bottom:1px solid #a0a9b4 !important;">
                                    </select>
                                    <!--<label class="rememberme mt-checkbox mt-checkbox-outline">
                                        <input type="checkbox" name="remember" value="1" /> Recuerdame
                                        <span></span>
                                    </label>-->
                                    
                                </div>
                                <div class="col-sm-6 text-right">
                                    <div class="forgot-password">
                                        <a href="javascript:;" id="forget-password" class="forget-password">Olvido su clave?</a>
                                    </div>
                                    <button class="btn blue" id="conectar" name="conectar" type="submit">Conectar</button>
                                </div>
                            </div>
                        </form>
                        <!-- BEGIN FORGOT PASSWORD FORM -->
                        <form class="forget-form hidden"  method="post" id="forget-form" action="ajax/reset_clave.php">
                            <h3>Olvido su clave ?</h3>
                            <p> Ingrese su usuario para restaurar su clave de acceso </p>
                            <div class="form-group">
                                <input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="Usuario" name="usuario_forget" id="usuario_forget" /> </div>
                                <input type="hidden" name="empresa_seleccionada" id = "empresa_seleccionada">
                            <div class="form-actions">
                                <button type="button" id="back-btn" class="btn blue btn-outline">Atras</button>
                                <button type="submit" id="enviar-form" class="btn blue uppercase pull-right">Enviar</button>
                            </div>
                        </form>
                        <!-- END FORGOT PASSWORD FORM -->
                        <!-- END FORGOT PASSWORD FORM -->
                    </div>
                    <div class="login-footer">
                        <div class="row bs-reset">
                            <div class="col-xs-7 bs-reset">
                                <div class="login-copyright text-right">
                                    <p>AMAXONIA LATIN <?php echo date('Y'); ?></p>
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
        <?php include("config/dep_js.php"); ?>
        <script src="ajax/js/login_new.js" type="text/javascript"></script>
        <script>
        $('#forget-password').click( function() {
            $('#forget-form').removeClass("hidden");
            $('#login-form').addClass("hidden");
        });
        $('#back-btn').click( function() {
            $('#forget-form').addClass("hidden");
            $('#login-form').removeClass("hidden");
        });
        </script>
    </body>
</html>