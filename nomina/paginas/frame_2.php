<?php
session_start();
ob_start();
$termino= $_SESSION['termino'];
require_once("func_bd.php");
require_once("../lib/common.php");

$sSql = new bd($_SESSION['bd']);
$sql = "SELECT * FROM nomempresa";
$result = $sSql->query($sql);;
$fila_empresa= $result->fetch_assoc();

$sSql = new bd($_SESSION['bd']);
$sql = "select evento,hora_inicio "
                . " from selectra_conf_pyme.evento_usuario "
                . " where curdate() between  fecha_inicio and fecha_fin "
                . " order by hora_inicio asc ";        
$resultNotificaciones = $sSql->query($sql);

if(!isset($_GET['archivo'])){
    $_GET['archivo'] = base64_encode("pagina_inicio.php");
    $_GET['menu']="inicio";
}
$path_foto_usuario = ($_SESSION['foto']=="")?"../../includes/assets/img/User-red-icon.png":"http://localhost/pyme_usuario/web/uploads/pictures/users/".$_SESSION['foto'];
$existe_foto = @file_get_contents($path_foto_usuario);
if(!$existe_foto){
    $path_foto_usuario = "../../includes/assets/img/User-red-icon.png";
}

if(file_exists("../../includes/imagenes/".$_SESSION['logo_empresa'])){
    $logo_empresa = "../../includes/imagenes/".$_SESSION['logo_empresa'];
}
else{
    $logo_empresa = "../../includes/imagenes/no_logo.png";
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
<title>.: <?php echo $_SESSION['nombre_sistema']." :: N&oacute;mina"; ?> :.</title>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta content="width=device-width, initial-scale=1.0" name="viewport">
<meta content="" name="description">
<meta content="" name="author">

<!-- BEGIN GLOBAL MANDATORY STYLES -->
<link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&amp;subset=all" rel="stylesheet" type="text/css">
<link href="../../includes/assets/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
<link href="../../includes/assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
<link href="../../includes/assets/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css">
<!-- END GLOBAL MANDATORY STYLES -->
<!-- BEGIN PAGE LEVEL STYLES -->
<link rel="stylesheet" type="text/css" href="../../includes/assets/plugins/select2/select2.css">
<link rel="stylesheet" type="text/css" href="../../includes/assets/plugins/select2/select2-metronic.css">

<link rel="stylesheet" href="../../includes/assets/plugins/data-tables/DT_bootstrap.css">
<link rel="stylesheet" type="text/css" href="../../includes/assets/plugins/bootstrap-datepicker/css/datepicker.css">
<link rel="stylesheet" type="text/css"href="../../includes/assets/plugins/fullcalendar/fullcalendar/fullcalendar.css"/>
<link rel="stylesheet" type="text/css" href="../../includes/assets/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css"/>
<link rel="stylesheet" type="text/css" href="../../includes/assets/plugins/bootstrap-datetimepicker/css/datetimepicker.css"/>
<!-- END PAGE LEVEL STYLES -->
<!-- BEGIN THEME STYLES -->
<link href="../../includes/assets/css/style-metronic.css" rel="stylesheet" type="text/css">
<link href="../../includes/assets/css/style.css" rel="stylesheet" type="text/css">
<link href="../../includes/assets/css/style-responsive.css" rel="stylesheet" type="text/css">
<link href="../../includes/assets/css/plugins.css" rel="stylesheet" type="text/css">
<link href="../../includes/assets/css/themes/green.css" rel="stylesheet" type="text/css">
<link href="../../includes/assets/css/custom.css" rel="stylesheet" type="text/css">
<!-- END THEME STYLES -->
<link rel="shortcut icon" href="favicon.ico">

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
    jQuery(document).ready(function() {    
       App.init();
    });
    function Abrir_Ventana2(){
        AbrirVentana('licencia.php',500,600,0);
    }
</script>
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
        <a class="navbar-brand logo" href="frame.php">
            <img src="../img_sis/logo.png" width="183" height="25" alt="logo" class="img-responsive"/>
        </a>
        <!-- END LOGO -->
        <!-- BEGIN RESPONSIVE MENU TOGGLER -->
        <a href="javascript:;" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <img src="../../includes/assets/img/menu-toggler.png" alt=""/>
        </a>
        <!-- END RESPONSIVE MENU TOGGLER -->
        <!-- BEGIN TOP NAVIGATION MENU -->
        <ul class="nav navbar-nav pull-right">
                    <!-- BEGIN NOTIFICATION DROPDOWN -->
                    
                    <li class="dropdown" id="header_notification_bar">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                            <i class="fa fa-warning"></i>
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
                                <div class="slimScrollDiv" style="position: relative; overflow: hidden; width: auto; height: 250px;"><ul class="dropdown-menu-list scroller" style="height: 250px; overflow: hidden; width: auto;">
                                    <?php
                                    if ($resultNotificaciones->num_rows > 0){
                                        while($fila = $result->fetch_assoc()){?>
                                            <li>
                                                <a href="#">
                                                    <span class="label label-icon label-danger">
                                                        <i class="fa fa-bell-o"></i>
                                                    </span>
                                                     <?php echo $fila['evento'];?>.
                                                    <span class="time">
                                                         <?php echo $fila['hora_inicio'];?>.
                                                    </span>
                                                </a>
                                            </li>
                                        <?php
                                        }
                                    }    
                                    ?>
                                </ul><div class="slimScrollBar" style="background-color: rgb(187, 187, 187); width: 7px; position: absolute; top: 0px; opacity: 0.4; display: none; border-top-left-radius: 7px; border-top-right-radius: 7px; border-bottom-right-radius: 7px; border-bottom-left-radius: 7px; z-index: 99; right: 1px; height: 152.4390243902439px; background-position: initial initial; background-repeat: initial initial;"></div><div class="slimScrollRail" style="width: 7px; height: 100%; position: absolute; top: 0px; display: none; border-top-left-radius: 7px; border-top-right-radius: 7px; border-bottom-right-radius: 7px; border-bottom-left-radius: 7px; background-color: rgb(234, 234, 234); opacity: 0.2; z-index: 90; right: 1px; background-position: initial initial; background-repeat: initial initial;"></div></div>
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
                            <img alt="" width="25" src="<?php echo $path_foto_usuario;?>">
                            <span class="username">
                                 <?php echo $_SESSION["nombre"];?>.
                            </span>
                            <i class="fa fa-angle-down"></i>
                        </a>
                        <ul class="dropdown-menu">
                            <!-- <li>
                                <a href="extra_profile.html">
                                    <i class="fa fa-user"></i> My Profile
                                </a>
                            </li>
                            <li>
                                <a href="page_calendar.html">
                                    <i class="fa fa-calendar"></i> My Calendar
                                </a>
                            </li>
                            <li>
                                <a href="inbox.html">
                                    <i class="fa fa-envelope"></i> My Inbox
                                    <span class="badge badge-danger">
                                         3
                                    </span>
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    <i class="fa fa-tasks"></i> My Tasks
                                    <span class="badge badge-success">
                                         7
                                    </span>
                                </a>
                            </li>
                            <li class="divider">
                            </li>
                            <li>
                                <a href="javascript:;" id="trigger_fullscreen">
                                    <i class="fa fa-arrows"></i> Full Screen
                                </a>
                            </li>-->
                            <li>
                                <a href="#" onClick="javascript:Abrir_Ventana2()">
                                    <i class="fa fa-lock"></i> Licencia
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
        <div class="hor-menu hidden-sm hidden-xs" >
            
            <!-- class="page-sidebar navbar-collapse collapse" -->
            <!-- add "navbar-no-scroll" class to disable the scrolling of the sidebar menu -->
            <!-- AQUI COMIENZA EL MENU -->
            
            <ul class="page-sidebar-menu" data-auto-scroll="true" data-slide-speed="200">
                <!--li class="sidebar-toggler-wrapper">
                    <br></br>    
                    <div class="sidebar-toggler hidden-phone">
                    </div>
                </li-->
                <!-- <li class="sidebar-toggler-wrapper">
                    <br>
                    
                    <img src="../../imagenes/pluginnx.jpg" width="86" height="14" alt="logo" class="img-responsive"/>
                </li> -->
                <a class="navbar-brand" href="#">
                    <img src="<?php echo $logo_empresa; ?>" width="183" height="35" alt="logo"/>
                </a>
                <br></br>
                <br></br>
                <li <?php if($_GET['menu'] == "inicio"){?>class="start active" <?php }else{?> class="start" <?php }?>>
                    <a href="frame.php?menu=inicio">
                        <i class="fa fa-home"></i>
                        <span class="title">
                            Inicio
                        </span>
                        <span class="selected">
                        </span>
                    </a>
                </li>

                <?php  
                    $sql = "SELECT * FROM nom_modulos WHERE cod_modulo_padre IS NULL ORDER BY orden";
                    $result = $sSql->query($sql);

                    //$resultNotificaciones->num_rows > 0){
                    //while($fila = $result->fetch_assoc()){

                    
                    $select = $sSql->query("select * from nomusuario_nomina where id_nomina='".$_SESSION['codigo_nomina']."'");
                    //if($select->num_rows!=0){
                        if ($result->num_rows > 0  ) 
                        {   
                            while ($row_rs = $result->fetch_assoc()){
                                //if ( (($_SESSION['acce_procesos'] == 1) && ($row_rs['cod_modulo'] == 1)) || (($_SESSION['acce_reportes'] == 1) && ($row_rs['cod_modulo'] == 4)) || (($_SESSION['acce_transacciones']== 1) && ($row_rs['cod_modulo'] == 7)) || (($_SESSION['acce_personal'] == 1) && ($row_rs['cod_modulo'] == 9)) || (($_SESSION['acce_configuracion'] == 1) && (($row_rs['cod_modulo'] == 10) || $row_rs['cod_modulo'] == 282)) || (($_SESSION['acce_elegibles'] == 1) && ($row_rs['cod_modulo'] == 60)) || (($_SESSION['acce_consultas'] == 1) && ($row_rs['cod_modulo'] == 61))|| (($_SESSION['acce_prestamos'] == 1) && ($row_rs['cod_modulo'] == 65)) || (($_SESSION['acce_generarordennomina'] == 1) && ($row_rs['cod_modulo'] == 66)) || ($row_rs['cod_modulo'] == 3) ){   
                                ?>
                                    <li <?php if($_GET['menu'] == $row_rs['cod_modulo']){?>class="start active" <?php }?> >
                                        <a href="javascript:;">
                                            <i class="glyphicon glyphicon-cog"></i>
                                            <span class="title">
                                                <?php echo utf8_encode($row_rs['nom_menu']); ?>
                                            </span>
                                            <span class="arrow ">
                                            </span>
                                        </a>
                                        <?php
                                            $sql = "SELECT * FROM nom_modulos WHERE cod_modulo_padre = '".$row_rs['cod_modulo']."' ORDER BY orden";
                                            $resultMenHijo = $sSql->query($sql);
                                        ?>
                                        <ul class="sub-menu">
                                            <?php
                                            while ($row_rs_hijo = $resultMenHijo->fetch_assoc()){
                                            ?>
                                            <li <?php if($_GET['opcion'] == $row_rs_hijo['cod_modulo']){?>class="active" <?php }?>> 
                                                <a href="frame.php?menu<?php echo $row_rs['cod_modulo'] ?>=&opcion=<?php echo $row_rs_hijo['cod_modulo'] ?>&archivo=<?php echo base64_encode($row_rs_hijo['archivo']) ?>" >
                                                <?php echo utf8_encode($row_rs_hijo['nom_menu']); ?>
                                                </a>
                                            </li>
                                        <?php
                                            }
                                        ?>
                                        </ul>
                                    </li>
                                <?php 
                                //}                                
                            }
                            ?>
                            <li <?php if($_GET['menu'] == "modulos"){?>class="active" <?php }?> >
                                <a href="../../entrada/" target="_blank">
                                    <i class="glyphicon glyphicon-cog"></i>
                                    <span class="title">
                                        M&oacute;dulos
                                    </span>
                                    <span class="arrow ">
                                    </span>
                                </a>
                            </li>
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
            
            <iframe id="iframe1"   marginheight="0" scrolling="auto" frameborder="0"  src="<?php echo base64_decode($_GET['archivo'])?>" height="1085px" width="100%" >
            </iframe>

        </div>
    </div>
    <!-- END CONTENT -->
</div>
<!-- END CONTAINER -->
<!-- BEGIN FOOTER -->
<div class="footer">
    <div class="footer-inner">
        Selectra 2015
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