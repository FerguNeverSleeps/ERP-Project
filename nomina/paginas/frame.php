<?php
session_start();
ob_start();
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
$termino = $_SESSION['termino'];
require_once("func_bd.php");
require_once("../lib/common.php");
if (empty($_SESSION['bd'])) {
?><script>
        alert("Disculpe, debe iniciar sesion para acceder");
    </script> <?php
                echo '<script language="Javascript">location.href="logout.php"</script>';
            }
            $sSql = new bd($_SESSION['bd']);
            $sql = "SELECT * FROM nomempresa ne";
            $result = $sSql->query($sql);;
            $fila_empresa = $result->fetch_assoc();

            $sSql = new bd($_SESSION['bd']);
            $sql = "select evento,hora_inicio "
                . " from " . SELECTRA_CONF_PYME . ".evento_usuario "
                . " where curdate() between  fecha_inicio and fecha_fin "
                . " order by hora_inicio asc ";
            $resultNotificaciones = $sSql->query($sql);

            if (!isset($_GET['archivo'])) {
                $_GET['archivo'] = base64_encode("pagina_inicio.php");
                $_GET['menu'] = "inicio";
            }
            $path_foto_usuario = ($_SESSION['foto'] == "") ? "../../includes/assets/img/User-red-icon.png" : "../img_sis/avatars/thumbs/" . $_SESSION['foto'];
            //$existe_foto = @file_get_contents($path_foto_usuario);
            //if(!$existe_foto){
            //    $path_foto_usuario = "../../includes/assets/img/User-red-icon.png";
            //}
            $conexion = new bd(SELECTRA_CONF_PYME);
            $sql_datos = "SELECT * FROM datos_empresa WHERE cod_empresa = '" . $_SESSION['codigo_nomempresa'] . "'";
            $result_de = $conexion->query($sql_datos);;
            $fila_datosempresa = $result_de->fetch_assoc();
            $logo_empresa = "../../includes/imagenes/logo_amx.png";

            // if(file_exists("../../includes/imagenes/".$fila_datosempresa['img_izq'])){
            //     $logo_empresa = "../../includes/imagenes/".$fila_datosempresa['img_izq'];
            //     //$logo_empresa = "../../includes/imagenes/logo_selectra.png";
            // }
            // else{
            //     $logo_empresa = "../../includes/imagenes/no_logo.png";
            // }
                ?>
<!DOCTYPE html>
<html lang="en" class="no-js">
<!--<![endif]-->
<!-- BEGIN HEAD -->

<head>
    <meta charset="utf-8">
    <title>.: <?php echo $_SESSION['nombre_sistema'] . " Planilla"; ?> :.</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="description">
    <meta content="" name="author">

    <!-- BEGIN GLOBAL MANDATORY STYLES
<link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&amp;subset=all" rel="stylesheet" type="text/css"> -->
    <link href="../../includes/assets/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <link href="../../includes/assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="../../includes/assets/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css">
    <!-- END GLOBAL MANDATORY STYLES -->
    <!-- BEGIN PAGE LEVEL STYLES -->
    <link rel="stylesheet" type="text/css" href="../../includes/assets/plugins/select2/select2.css">
    <link rel="stylesheet" type="text/css" href="../../includes/assets/plugins/select2/select2-metronic.css">

    <link rel="stylesheet" href="../../includes/assets/plugins/data-tables/DT_bootstrap.css">
    <link rel="stylesheet" type="text/css" href="../../includes/assets/plugins/bootstrap-datepicker/css/datepicker.css">
    <link rel="stylesheet" type="text/css" href="../../includes/assets/plugins/fullcalendar/fullcalendar/fullcalendar.css" />
    <link rel="stylesheet" type="text/css" href="../../includes/assets/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css" />
    <link rel="stylesheet" type="text/css" href="../../includes/assets/plugins/bootstrap-datetimepicker/css/datetimepicker.css" />
    <!-- END PAGE LEVEL STYLES -->
    <!-- BEGIN THEME STYLES -->
    <link href="../../includes/assets/css/style-metronic.css" rel="stylesheet" type="text/css">
    <link href="../../includes/assets/css/style.css" rel="stylesheet" type="text/css">
    <link href="../../includes/assets/css/style-responsive.css" rel="stylesheet" type="text/css">
    <link href="../../includes/assets/css/plugins.css" rel="stylesheet" type="text/css">
    <link href="../../includes/assets/css/themes/blue.css" rel="stylesheet" type="text/css">
    <link href="../../includes/assets/css/custom.css" rel="stylesheet" type="text/css">
    <!-- END THEME STYLES -->
    <link rel="shortcut icon" href="amaxonia.ico">

    <!-- BEGIN JAVASCRIPTS(Load javascripts at bottom, this will reduce page load time) -->
    <!-- BEGIN CORE PLUGINS -->
    <!--[if lt IE 9]>
    <script src="../../includes/assets/plugins/respond.min.js"></script>
    <script src="../../includes/assets/plugins/excanvas.min.js"></script> 
    <![endif]-->
    <script src="../../includes/assets/plugins/jquery-1.10.2.min.js" type="text/javascript"></script>
    <script src="../../includes/assets/plugins/jquery-migrate-1.2.1.min.js" type="text/javascript"></script>
    <!-- IMPORTANT! Load jquery-ui-1.10.3.custom.min.js before bootstrap.min.js to fix bootstrap tooltip conflict with jquery ui tooltip -->
    <script src="../../includes/assets/plugins/jquery-ui/jquery-ui-1.10.3.custom.min.js" type="text/javascript"></script>
    <script src="../../includes/assets/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
    <script src="../../includes/assets/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js" type="text/javascript"></script>
    <script src="../../includes/assets/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
    <script src="../../includes/assets/plugins/jquery.blockui.min.js" type="text/javascript"></script>
    <script src="../../includes/assets/plugins/jquery.cokie.min.js" type="text/javascript"></script>
    <script src="../../includes/assets/plugins/uniform/jquery.uniform.min.js" type="text/javascript"></script>
    <!-- END CORE PLUGINS -->
    <!-- BEGIN PAGE LEVEL PLUGINS -->
    <script type="text/javascript" src="../../includes/assets/plugins/select2/select2.min.js"></script>

    <!--<script type="text/javascript" src="../../includes/assets/plugins/select2/select2_locale_es.js"></script>-->
    <script type="text/javascript" src="../../includes/assets/plugins/data-tables/jquery.dataTables.js"></script>
    <script type="text/javascript" src="../../includes/assets/plugins/data-tables/DT_bootstrap.js"></script>
    <script type="text/javascript" src="../../includes/assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
    <script type="text/javascript" src="../../includes/assets/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"></script>
    <script src="../../includes/assets/plugins/fullcalendar/fullcalendar/fullcalendar.min.js" type="text/javascript"></script>
    <!-- BEGIN PAGE LEVEL PLUGINS -->
    <script src="../../includes/assets/plugins/flot/jquery.flot.min.js"></script>
    <script src="../../includes/assets/plugins/flot/jquery.flot.resize.min.js"></script>
    <script src="../../includes/assets/plugins/flot/jquery.flot.pie.min.js"></script>
    <script src="../../includes/assets/plugins/flot/jquery.flot.stack.min.js"></script>
    <script src="../../includes/assets/plugins/flot/jquery.flot.crosshair.min.js"></script>
    <script src="../../includes/assets/plugins/flot/jquery.flot.categories.min.js" type="text/javascript"></script>
    <!-- END PAGE LEVEL PLUGINS -->
    <!-- BEGIN PAGE LEVEL SCRIPTS -->
    <!-- END PAGE LEVEL PLUGINS -->
    <!-- BEGIN PAGE LEVEL SCRIPTS -->
    <script src="../../includes/assets/scripts/core/app.js"></script>
    <script src="../../includes/assets/scripts/core/helpers.js"></script>
    <script src="../../includes/assets/scripts/core/datatable.js"></script>
    <script src="../../includes/assets/scripts/custom/lista-escala.js"></script>
    <script src="../../includes/assets/scripts/custom/lista-auditoria.js"></script>
    <script src="../../includes/assets/scripts/custom/lista-escala-nueva.js"></script>
    <script src="../../includes/js/gui/numeros.js"></script>
    <script src="../../includes/assets/scripts/custom/charts-inicio.js"></script>
    <script src="../../includes/assets/scripts/custom/chat-inicio.js"></script>
    <script src="../../includes/assets/scripts/custom/calendar-inicio.js" type="text/javascript"></script>
    <script src="../../includes/assets/scripts/custom/components-pickers.js"></script>
    <!-- END PAGE LEVEL SCRIPTS -->
    <script>
        function AbrirVentana(Ventana, Largo, Alto, Modal) {
            if (Modal == 1) {
                mainWindow = showModalDialog(Ventana, 'mainWindow', 'dialogWidth:' + Alto + 'px;dialogHeight:' + Largo + 'px;resizable:yes;toolbar:no;menubar:no;scrollbars:yes;help: no');
            } else {

                mainWindow = window.open(Ventana, 'mainWindow', 'menub ar=no,resizable=no,width=' + Alto + ',height=' + Largo + ',left=0,top=0,titlebar=yes,alwaysraised=yes,status=no,scrollbars=yes');
            }


        }
        jQuery(document).ready(function() {
            App.init();
        });

        function Abrir_Ventana2() {
            AbrirVentana('licencia.php', 500, 600, 0);
        }

        function Abrir_Ventana3() {
            AbrirVentana('cambiar_clave.php', 400, 420, 0);
        }

        function recarga() {
            document.location.href = "frame.php?menu=inicio";
        }
    </script>
    <style type="text/css">
        .page-content {
            padding: 0px !important;
        }
    </style>
    <!-- BEGIN PAGE LEVEL FONT STYLES -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro:ital,wght@0,200;0,300;0,400;0,600;0,700;0,900;1,200;1,300;1,400;1,600;1,700;1,900&display=swap" rel="stylesheet">
    <!-- END PAGE LEVEL FONT STYLES -->
    <!-- END JAVASCRIPTS -->
</head>
<!-- END HEAD -->
<!-- BEGIN BODY -->

<body class="page-header-fixed">
    <!-- BEGIN HEADER -->
    <div class="header navbar navbar-fixed-top">
        <!-- BEGIN TOP NAVIGATION BAR -->
        <div class="header-inner">
            <!-- BEGIN LOGO -->
            <!--a class="navbar-brand logo" href="frame.php">
            <img src="../../includes/imagenes/logo_selectra1.png" width="260" height="50" alt="logo" class="img-responsive"/>
        </a-->
            <a class="navbar-brand logo" href="#">
                <!-- <img src="../../includes/imagenes/trade_unify.png" width="60%" height="20%" alt="logo" class="img-responsive"/> -->
                <img src="<?php echo $logo_empresa; ?>" width="60%" height="20%" alt="logo" class="img-responsive" />
            </a>
            <!-- END LOGO -->
            <!-- BEGIN RESPONSIVE MENU TOGGLER -->
            <a href="javascript:;" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <!-- <img src="../../includes/assets/img/menu-toggler.png" alt=""/> -->
                <svg width="25" height="18" viewBox="0 0 25 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M0.568359 1.5C0.568359 0.670312 1.2271 0 2.04247 0H19.7318C20.5471 0 21.2059 0.670312 21.2059 1.5C21.2059 2.32969 20.5471 3 19.7318 3H2.04247C1.2271 3 0.568359 2.32969 0.568359 1.5ZM3.51657 9C3.51657 8.17031 4.17532 7.5 4.99068 7.5H22.68C23.4953 7.5 24.1541 8.17031 24.1541 9C24.1541 9.82969 23.4953 10.5 22.68 10.5H4.99068C4.17532 10.5 3.51657 9.82969 3.51657 9ZM21.2059 16.5C21.2059 17.3297 20.5471 18 19.7318 18H2.04247C1.2271 18 0.568359 17.3297 0.568359 16.5C0.568359 15.6703 1.2271 15 2.04247 15H19.7318C20.5471 15 21.2059 15.6703 21.2059 16.5Z" fill="white" />
                </svg>
            </a>
            <!-- END RESPONSIVE MENU TOGGLER -->
            <!-- BEGIN TOP NAVIGATION MENU -->
            <ul class="nav navbar-nav pull-right">
                <!-- BEGIN NOTIFICATION DROPDOWN -->
                <li class="dropdown user">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                        <span class="username">
                            <?php echo $termino . ": " ?><?php echo ($_SESSION["nomina"]); ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo "Empresa: " . ($_SESSION["nombre_empresa_nomina"]); ?>
                        </span>
                    </a>
                </li>

                <li class="dropdown" id="header_notification_bar">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                        <!-- <i class="fa fa-warning"></i> -->
                        <i class="fa fa-bell"></i>
                        <span class="badge">
                            <?php echo $resultNotificaciones->num_rows; ?>
                        </span>
                    </a>
                    <ul class="dropdown-menu extended notification">
                        <li>
                            <p>
                                Tiene <?php echo $resultNotificaciones->num_rows; ?> Notificaciones
                            </p>
                        </li>
                        <li>
                            <div class="slimScrollDiv" style="position: relative; overflow: hidden; width: auto; height: 250px;">
                                <ul class="dropdown-menu-list scroller" style="height: 250px; overflow: hidden; width: auto;">
                                    <?php
                                    if ($resultNotificaciones->num_rows > 0) {
                                        while ($fila = $result->fetch_assoc()) { ?>
                                            <li>
                                                <a href="#">
                                                    <span class="label label-icon label-danger">
                                                        <i class="fa fa-bell-o"></i>
                                                    </span>
                                                    <?php echo $fila['evento']; ?>.
                                                    <span class="time">
                                                        <?php echo $fila['hora_inicio']; ?>.
                                                    </span>
                                                </a>
                                            </li>
                                    <?php
                                        }
                                    }
                                    ?>
                                </ul>
                                <div class="slimScrollBar" style="background-color: rgb(187, 187, 187); width: 7px; position: absolute; top: 0px; opacity: 0.4; display: none; border-top-left-radius: 7px; border-top-right-radius: 7px; border-bottom-right-radius: 7px; border-bottom-left-radius: 7px; z-index: 99; right: 1px; height: 152.4390243902439px; background-position: initial initial; background-repeat: initial initial;"></div>
                                <div class="slimScrollRail" style="width: 7px; height: 100%; position: absolute; top: 0px; display: none; border-top-left-radius: 7px; border-top-right-radius: 7px; border-bottom-right-radius: 7px; border-bottom-left-radius: 7px; background-color: rgb(234, 234, 234); opacity: 0.2; z-index: 90; right: 1px; background-position: initial initial; background-repeat: initial initial;"></div>
                            </div>
                        </li>
                        <!-- 
                            <li class="external">
                                <a href="#">
                                     See all notifications <i class="m-icon-swapright"></i>
                                </a>
                            </li>
                             -->
                    </ul>
                </li>
                <!-- END NOTIFICATION DROPDOWN -->

                <!-- BEGIN USER LOGIN DROPDOWN -->
                <li class="dropdown user">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                        <img alt="" width="25" src="../../includes/assets/img/navbar/profile.png">
                        <!-- <img alt="" width="25" src="<?php echo $path_foto_usuario; ?>"> -->
                        <span class="username">
                            <?php echo $_SESSION["nombre"]; ?>.
                        </span>
                        <i class="fa fa-angle-down"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="#" onClick="javascript:Abrir_Ventana2()">
                                <i class="fa fa-lock"></i> Licencia
                            </a>
                        </li>
                        <li>
                            <a href="#" onClick="javascript:Abrir_Ventana3()">
                                <i class="fa fa-user"></i> Cambiar Clave
                            </a>
                        </li>
                        <li>
                            <a href="../modulos/principal/index.php?logout=off'">
                                <i class="fa fa-key"></i> Salir
                            </a>
                        </li>
                    </ul>
                </li>
                <!-- END USER LOGIN DROPDOWN -->
            </ul>

            <!-- END TOP NAVIGATION MENU -->
        </div>
        <!-- END TOP NAVIGATION BAR -->
    </div>
    <!-- END HEADER -->
    <div class="clearfix">
    </div>
    <!-- BEGIN CONTAINER -->
    <div class="page-container">
        <!-- BEGIN SIDEBAR -->
        <div class="page-sidebar-wrapper">
            <div class="page-sidebar navbar-collapse collapse">
                <!-- add "navbar-no-scroll" class to disable the scrolling of the sidebar menu -->
                <!-- AQUI COMIENZA EL MENU -->

                <ul class="page-sidebar-menu" data-auto-scroll="true" data-slide-speed="200">
                    <li class="sidebar-toggler-wrapper">
                        <!-- BEGIN SIDEBAR TOGGLER BUTTON -->

                        <div class="sidebar-toggler hidden-phone">
                        </div>
                        <!-- BEGIN SIDEBAR TOGGLER BUTTON -->
                    </li>
                    <!-- <li class="sidebar-toggler-wrapper">
                    <br>
                    
                    <img src="../../imagenes/pluginnx.jpg" width="86" height="14" alt="logo" class="img-responsive"/>
                </li> -->
                    <!--a class="navbar-brand" href="#">
                    <img src="<?php echo $logo_empresa; ?>" width="183" height="35" alt="logo"/>
                </a-->
                    <br>
                    <li <?php if ($_GET['menu'] == "inicio") { ?>class="start active" <?php } else { ?> class="start" <?php } ?>>
                        <a href="frame.php?menu=inicio">
                            <i class="fa fa-home"></i>
                            <span class="title">
                                Inicio
                            </span>
                        </a>
                    </li>

                    <?php
                    //$sql = "SELECT * FROM nom_modulos WHERE cod_modulo_padre IS NULL and activo=1 ORDER BY orden";
                    $sql = "SELECT mp.* 
                    FROM " . SELECTRA_CONF_PYME . ".nom_modulos_usuario mu  
                    inner join nom_modulos mh on mh.cod_modulo = mu.cod_modulo 
                    inner join nom_modulos mp on mp.cod_modulo = mh.cod_modulo_padre 
                    WHERE  mu.coduser='" . $_SESSION['cod_usuario'] . "' and mp.activo=1 and mp.cod_modulo_padre IS NULL
                    group by mp.cod_modulo 
                    ORDER BY mp.orden";
                    //echo $sql;
                    $result = $sSql->query($sql);
                    //$resultNotificaciones->num_rows > 0){
                    //while($fila = $result->fetch_assoc()){


                    $select = $sSql->query("select * from " . SELECTRA_CONF_PYME . ".nomusuario_nomina where id_nomina='" . $_SESSION['codigo_nomina'] . "'");
                    // echo "select * from ".SELECTRA_CONF_PYME.".nomusuario_nomina where id_nomina='".$_SESSION['codigo_nomina']."'";
                    //if($select->num_rows!=0){
                    if ($result->num_rows > 0) {
                        while ($row_rs = $result->fetch_assoc()) {
                            //if ( (($_SESSION['acce_procesos'] == 1) && ($row_rs['cod_modulo'] == 1)) || (($_SESSION['acce_reportes'] == 1) && ($row_rs['cod_modulo'] == 4)) || (($_SESSION['acce_transacciones']== 1) && ($row_rs['cod_modulo'] == 7)) || (($_SESSION['acce_personal'] == 1) && ($row_rs['cod_modulo'] == 9)) || (($_SESSION['acce_configuracion'] == 1) && (($row_rs['cod_modulo'] == 10) || $row_rs['cod_modulo'] == 282)) || (($_SESSION['acce_elegibles'] == 1) && ($row_rs['cod_modulo'] == 60)) || (($_SESSION['acce_consultas'] == 1) && ($row_rs['cod_modulo'] == 61))|| (($_SESSION['acce_prestamos'] == 1) && ($row_rs['cod_modulo'] == 65)) || (($_SESSION['acce_generarordennomina'] == 1) && ($row_rs['cod_modulo'] == 66)) || ($row_rs['cod_modulo'] == 3) ){   
                            //$sql = "SELECT * FROM nom_modulos WHERE cod_modulo_padre = '".$row_rs['cod_modulo']."' and activo=1 ORDER BY orden";
                            $sql = "SELECT mh.* 
                                    FROM " . SELECTRA_CONF_PYME . ".nom_modulos_usuario mu  
                                    inner join nom_modulos mh on mh.cod_modulo = mu.cod_modulo 
                                    WHERE  mh.cod_modulo_padre = '" . $row_rs['cod_modulo'] . "' and mu.coduser='" . $_SESSION['cod_usuario'] . "' and mh.activo=1 
                                    ORDER BY mh.orden";
                            // echo $sql;
                            $resultMenHijoCount = $sSql->query($sql);
                            if ($resultMenHijoCount->num_rows == 1) {
                                $row_rs_hijoCount1 = $resultMenHijoCount->fetch_assoc();
                                $hrefHijo = "frame.php?menu=" . $row_rs['cod_modulo'] . "&opcion=" . $row_rs_hijoCount1['cod_modulo'] . "&archivo=" . base64_encode($row_rs_hijoCount1['archivo']);
                                $opcionSola = 1;
                            } else {
                                $hrefHijo = "javascript:;";
                                $opcionSola = 0;
                            }
                    ?>
                            <li <?php if ($_GET['menu'] == $row_rs['cod_modulo']) { ?>class="start active" <?php } ?>>
                                <a href="<?php echo $hrefHijo; ?>">
                                    <i class="glyphicon <?php echo ($row_rs['glyphicon']); ?>"></i>
                                    <span class="title">
                                        <?php echo utf8_encode($row_rs['nom_menu']); ?>
                                    </span>
                                    <?php
                                    if ($opcionSola == 0) {
                                    ?>
                                        <span class="arrow ">
                                        </span>
                                    <?php
                                    }
                                    ?>
                                </a>
                                <?php
                                if ($opcionSola == 0) {
                                    //$sql = "SELECT * FROM nom_modulos WHERE cod_modulo_padre = '".$row_rs['cod_modulo']."' and activo=1 ORDER BY orden";
                                    $sql = "SELECT mh.* 
                                                    FROM " . SELECTRA_CONF_PYME . ".nom_modulos_usuario mu  
                                                    inner join nom_modulos mh on mh.cod_modulo = mu.cod_modulo 
                                                    WHERE  mh.cod_modulo_padre = '" . $row_rs['cod_modulo'] . "' and mu.coduser='" . $_SESSION['cod_usuario'] . "' and mh.activo=1 ";

                                    $resultMenHijo = $sSql->query($sql);
                                }
                                ?>
                                <?php
                                if ($opcionSola == 0) {
                                ?>
                                    <ul class="sub-menu">
                                        <?php
                                        while ($row_rs_hijo = $resultMenHijo->fetch_assoc()) {
                                        ?>

                                            <li <?php if ($_GET['opcion'] == $row_rs_hijo['cod_modulo']) { ?>class="active" <?php } ?>>
                                                <a title="<?php echo $row_rs_hijo['alt']; ?>" href="frame.php?menu=<?php echo $row_rs['cod_modulo'] ?>&opcion=<?php echo $row_rs_hijo['cod_modulo'] ?>&archivo=<?php echo base64_encode($row_rs_hijo['archivo']) ?>">
                                                    <?php if ($row_rs_hijo['img'] != "") {  ?>
                                                        <img alt="<?php echo $row_rs_hijo['alt']; ?>" src="<?php echo $row_rs_hijo['img']; ?>" width="16" height="16" />
                                                    <?php } ?>
                                                    <?php echo utf8_encode($row_rs_hijo['nom_menu']); ?>
                                                </a>
                                            </li>
                                        <?php
                                        }
                                        ?>
                                    </ul>
                                <?php
                                }
                                ?>
                            </li>
                        <?php
                            //}                                
                        }
                        ?>
                    <?php
                    }
                    /*}else{
                            echo"<SCRIPT language=\"JavaScript\" type=\"text/javascript\">
                            alert(\"NO ESTA AUTORIZADO PARA ACCEDER CON ESTE TIPO DE PLANILLA/NOMINA\")
                            parent.location.href='logout.php'
                            </SCRIPT>";
                            
                    }*/
                    ?>
                    <li class="last"></li>
                </ul>


                <!-- AQUI TERMINA EL MENU -->
            </div>
        </div>
        <!-- END SIDEBAR -->
        <!-- BEGIN CONTENT -->
        <div class="page-content-wrapper">
            <div class="page-content" id="page-content-ajax">
                <?php
                if ($_GET['menu'] == 3 && $_GET['opcion'] == 229) {
                    $sizeIframe = 2200;
                } elseif ($_GET['menu'] == 9 && $_GET['opcion'] == 177) {
                    $sizeIframe = 3100;
                }
                /*
                elseif($_GET['menu'] == 10 && $_GET['opcion'] == 307){
                    $sizeIframe = 500;
                }
                */
                /*
                elseif($_GET['menu'] == 10 && $_GET['opcion'] == 308){
                    $sizeIframe = 500;
                }
                */
                /*
                elseif($_GET['menu'] == 10 && $_GET['opcion'] == 309){
                    $sizeIframe = 550;
                }
                */ elseif ($_GET['menu'] == 10 && $_GET['opcion'] == 315) {
                    $sizeIframe = 600;
                } elseif ($_GET['menu'] == 9 && $_GET['opcion'] == 325) {
                    $sizeIframe = 3200;
                } elseif ($_GET['menu'] == 337 && $_GET['opcion'] == 293) {
                    $sizeIframe = 3500;
                } else {
                    $sizeIframe = 2000; //1580;
                }
                ?>

                <?php if ($_GET['menu'] == 9 && $_GET['opcion'] == 177) { ?>
                    <style>
                        #page-content-ajax {
                            padding: 0px;
                        }
                    </style>
                <?php } ?>
                <iframe id="iframe1" marginheight="0" scrolling="auto" frameborder="0" src="<?php echo base64_decode($_GET['archivo']) . "?modulo=" . $_GET['opcion'] ?>" height="<?php echo $sizeIframe; ?>px" width="100%" onload="scroll(0,0);" style="margin-left: 15px;">
                </iframe>

            </div>
        </div>
        <!-- END CONTENT -->
    </div>
    <!-- END CONTAINER -->
    <!-- BEGIN FOOTER -->
    <div class="footer">
        <div class="footer-inner">
            www.amaxonialatin.cloud
        </div>
        <div class="footer-tools">
            <span class="go-top">
                <i class="fa fa-angle-up"></i>
            </span>
        </div>
    </div>
    <!-- END FOOTER -->

    <!-- END BODY -->
</body>
<!-- END BODY -->

</html>