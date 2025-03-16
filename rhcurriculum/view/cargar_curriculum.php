<!DOCTYPE html>
<!-- 
Template Name: Metronic - Responsive Admin Dashboard Template build with Twitter Bootstrap 3.3.7
Version: 4.7.1
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
        <title>Ingrese su curriculum | RRHH</title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta content="width=device-width, initial-scale=1" name="viewport" />
        <meta content="Preview page of Metronic Admin Theme #1 for user account page" name="description" />
        <meta content="" name="author" />
        <!-- BEGIN GLOBAL MANDATORY STYLES -->
        <!--<link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css" />-->
        <link href="../lib/metronic/assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <link href="../lib/metronic/assets/global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css" />
        <link href="../lib/metronic/assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="../lib/metronic/assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet" type="text/css" />
        <!-- END GLOBAL MANDATORY STYLES -->
        <!-- BEGIN PAGE LEVEL PLUGINS -->
        <link href="../lib/metronic/assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css" rel="stylesheet" type="text/css" />
        <link href="../lib/metronic/assets/global/plugins/bootstrap-sweetalert/sweetalert.css" rel="stylesheet" type="text/css" />
        <!-- END PAGE LEVEL PLUGINS -->
        <!-- BEGIN THEME GLOBAL STYLES -->
        <link href="../lib/metronic/assets/global/css/components.min.css" rel="stylesheet" id="style_components" type="text/css" />
        <link href="../lib/metronic/assets/global/css/plugins.min.css" rel="stylesheet" type="text/css" />
        <!-- END THEME GLOBAL STYLES -->
        <!-- BEGIN PAGE LEVEL STYLES -->
        <link href="../lib/metronic/assets/pages/css/profile.min.css" rel="stylesheet" type="text/css" />
        <link href="../lib/metronic/assets/global/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
        <link href="../lib/metronic/assets/global/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
        
        <!-- END PAGE LEVEL STYLES -->
        <!-- BEGIN THEME LAYOUT STYLES -->
        <link href="../lib/metronic/assets/layouts/layout/css/layout.min.css" rel="stylesheet" type="text/css" />
        <link href="../lib/metronic/assets/layouts/layout/css/themes/darkblue.min.css" rel="stylesheet" type="text/css" id="style_color" />
        <link href="../lib/metronic/assets/layouts/layout/css/custom.min.css" rel="stylesheet" type="text/css" />
        <!-- END THEME LAYOUT STYLES -->
        <link rel="shortcut icon" href="favicon.ico" /> 

        <style type="text/css">

            .select2-container--bootstrap .select2-selection{
                border-radius: 25px 0 0 25px !important;
                
            }
            .select2 .select2-container .select2-container--bootstrap{
                min-width: 80%!important;
            }
        </style>
        </head>
    <!-- END HEAD -->

    <body class="page-header-fixed page-sidebar-closed-hide-logo page-container-bg-solid page-content-white page-sidebar-closed">
        <div class="page-wrapper">
            <!-- BEGIN HEADER -->
            
            <!-- END HEADER -->
            <!-- BEGIN HEADER & CONTENT DIVIDER -->
            
            <!-- END HEADER & CONTENT DIVIDER -->
            <!-- BEGIN CONTAINER -->
            <div class="page-container" style="margin-top: 0px !important;">               
                <!-- BEGIN CONTENT -->
                <div class="page-content-wrapper">
                    <!-- BEGIN CONTENT BODY -->
                    <div class="page-content" style=" margin-left: 0px !important;">
                        <!-- BEGIN PAGE HEADER-->
                        <!-- BEGIN THEME PANEL -->
                        <!--<div class="theme-panel hidden-xs hidden-sm">
                            <div class="toggler"> </div>
                            <div class="toggler-close"> </div>
                            <div class="theme-options">
                                <div class="theme-option theme-colors clearfix">
                                    <span> THEME COLOR </span>
                                    <ul>
                                        <li class="color-default current tooltips" data-style="default" data-container="body" data-original-title="Default"> </li>
                                        <li class="color-darkblue tooltips" data-style="darkblue" data-container="body" data-original-title="Dark Blue"> </li>
                                        <li class="color-blue tooltips" data-style="blue" data-container="body" data-original-title="Blue"> </li>
                                        <li class="color-grey tooltips" data-style="grey" data-container="body" data-original-title="Grey"> </li>
                                        <li class="color-light tooltips" data-style="light" data-container="body" data-original-title="Light"> </li>
                                        <li class="color-light2 tooltips" data-style="light2" data-container="body" data-html="true" data-original-title="Light 2"> </li>
                                    </ul>
                                </div>
                                <div class="theme-option">
                                    <span> Theme Style </span>
                                    <select class="layout-style-option form-control input-sm">
                                        <option value="square" selected="selected">Square corners</option>
                                        <option value="rounded">Rounded corners</option>
                                    </select>
                                </div>
                                <div class="theme-option">
                                    <span> Layout </span>
                                    <select class="layout-option form-control input-sm">
                                        <option value="fluid" selected="selected">Fluid</option>
                                        <option value="boxed">Boxed</option>
                                    </select>
                                </div>
                                <div class="theme-option">
                                    <span> Header </span>
                                    <select class="page-header-option form-control input-sm">
                                        <option value="fixed" selected="selected">Fixed</option>
                                        <option value="default">Default</option>
                                    </select>
                                </div>
                                <div class="theme-option">
                                    <span> Top Menu Dropdown</span>
                                    <select class="page-header-top-dropdown-style-option form-control input-sm">
                                        <option value="light" selected="selected">Light</option>
                                        <option value="dark">Dark</option>
                                    </select>
                                </div>
                                <div class="theme-option">
                                    <span> Sidebar Mode</span>
                                    <select class="sidebar-option form-control input-sm">
                                        <option value="fixed">Fixed</option>
                                        <option value="default" selected="selected">Default</option>
                                    </select>
                                </div>
                                <div class="theme-option">
                                    <span> Sidebar Menu </span>
                                    <select class="sidebar-menu-option form-control input-sm">
                                        <option value="accordion" selected="selected">Accordion</option>
                                        <option value="hover">Hover</option>
                                    </select>
                                </div>
                                <div class="theme-option">
                                    <span> Sidebar Style </span>
                                    <select class="sidebar-style-option form-control input-sm">
                                        <option value="default" selected="selected">Default</option>
                                        <option value="light">Light</option>
                                    </select>
                                </div>
                                <div class="theme-option">
                                    <span> Sidebar Position </span>
                                    <select class="sidebar-pos-option form-control input-sm">
                                        <option value="left" selected="selected">Left</option>
                                        <option value="right">Right</option>
                                    </select>
                                </div>
                                <div class="theme-option">
                                    <span> Footer </span>
                                    <select class="page-footer-option form-control input-sm">
                                        <option value="fixed">Fixed</option>
                                        <option value="default" selected="selected">Default</option>
                                    </select>
                                </div>
                            </div>
                        </div>-->
                        <!-- END THEME PANEL -->
                        <!-- BEGIN PAGE BAR -->
                        <div class="page-bar">
                            <ul class="page-breadcrumb">
                                <li>
                                    <a href="#">RRHH</a>
                                    <i class="fa fa-circle"></i>
                                </li>
                                <li>
                                    <span>Curriculum</span>
                                </li>
                            </ul>
                            <!--<div class="page-toolbar">
                                <div class="btn-group pull-right">
                                    <button type="button" class="btn green btn-sm btn-outline dropdown-toggle" data-toggle="dropdown"> Actions
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right" role="menu">
                                        <li>
                                            <a href="#">
                                                <i class="icon-bell"></i> Action</a>
                                        </li>
                                        <li>
                                            <a href="#">
                                                <i class="icon-shield"></i> Another action</a>
                                        </li>
                                        <li>
                                            <a href="#">
                                                <i class="icon-user"></i> Something else here</a>
                                        </li>
                                        <li class="divider"> </li>
                                        <li>
                                            <a href="#">
                                                <i class="icon-bag"></i> Separated link</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>-->
                        </div>
                        <!-- END PAGE BAR -->
                        <!-- BEGIN PAGE TITLE-->
                        <h1 class="page-title"> Ingresa tu curriculum |
                            <small>RRHH</small>
                        </h1>
                        <!-- END PAGE TITLE-->
                        <!-- END PAGE HEADER-->
                        <div class="row">
                            <form id='fm_curriculum' name="fm_curriculum" method="POST" enctype='multipart/form-data' >
                            <div class="col-md-12">
                                <!-- BEGIN PROFILE SIDEBAR -->
                                <div class="profile-sidebar">
                                    <!-- PORTLET MAIN -->
                                    <div class="portlet light ">
                                        <!-- SIDEBAR USERPIC -->
                                        <div class="portlet-title">
                                            <div class="caption caption-md">
                                                <i class="icon-globe theme-font hide"></i>
                                                <span class="caption-subject font-blue-madison bold uppercase">Adjuntos</span>
                                            </div>
                                                    
                                        </div>
                                        <div class="portlet-body" style="height: 350px">
                                            <div class="form-horizontal form-bordered" rol="form">
                                                <div class="form-body">
                                                    <div class="form-group">
                                                        <label class="control-label col-md-2">Foto</label>
                                                        <div class="col-md-12">
                                                            <div class="fileinput fileinput-new" data-provides="fileinput">
                                                                <div class="fileinput-new thumbnail" style="width: 200px; height: 150px;">
                                                                    <img src="http://www.placehold.it/200x150/EFEFEF/AAAAAA&amp;text=no+image" alt="" /> </div>
                                                                <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 200px; max-height: 150px;"> </div>
                                                                <div>
                                                                    <span class="btn default btn-file">
                                                                        <span class="fileinput-new"> Seleccione</span>
                                                                        <span class="fileinput-exists"> Cambiar </span>
                                                                        <input type="file" name="file_foto" id="file_foto"> </span>
                                                                    <a href="javascript:;" class="btn default fileinput-exists" data-dismiss="fileinput"> Remover </a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="form-group">
                                                        <label class="control-label col-md-2">Documento</label>
                                                        <div class="col-md-12">
                                                            <div class="fileinput fileinput-new" data-provides="fileinput">
                                                                <span class="btn green btn-file">
                                                                    <span class="fileinput-new"> Seleccione </span>
                                                                    <span class="fileinput-exists"> Cambiar </span>
                                                                    <input type="file" name="file_documento" id="file_documento"> </span>
                                                                <span class="fileinput-filename"> </span> &nbsp;
                                                                <a href="javascript:;" class="close fileinput-exists" data-dismiss="fileinput"> </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            
                                        </div>
                                            

                                        <div class="clearfix margin-top-10">
                                            <!--<span class="label label-danger">NOTE! </span>
                                            <span>Attached image thumbnail is supported in Latest Firefox, Chrome, Opera, Safari and Internet Explorer 10 only </span>-->
                                        </div>
                                                                                
                                    </div>
                                    <!-- END PORTLET MAIN -->
                                    
                                </div>
                                <!-- END BEGIN PROFILE SIDEBAR -->
                                <!-- BEGIN PROFILE CONTENT -->
                                <div class="profile-content">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="portlet light ">
                                               
                                                <div class="portlet-title tabbable-line">
                                                    <div class="caption caption-md">
                                                        <i class="icon-globe theme-font hide"></i>
                                                        <span class="caption-subject font-blue-madison bold uppercase">Cargar curriculum</span>
                                                    </div>
                                                    <ul class="nav nav-tabs">
                                                        <li class="active">
                                                            <a href="#tab_1_1" data-toggle="tab">Datos generales</a>
                                                        </li>
                                                        <li>
                                                            <a href="#tab_1_2" data-toggle="tab">Informaci&oacute;n acad&eacute;mica</a>
                                                        </li>
                                                        <li>
                                                            <a href="#tab_1_3" data-toggle="tab">Informaci&oacute;n ade contacto</a>
                                                        </li>
                                                        <!--<li>
                                                            <a href="#tab_1_4" data-toggle="tab">Privacy Settings</a>
                                                        </li>-->
                                                    </ul>
                                                </div>
                                                <div class="portlet-body" style="height: 350px">
                                                     
                                                    <div class="tab-content">
                                                        <!-- PERSONAL INFO TAB -->
                                                        <div class="tab-pane active" id="tab_1_1">
                                                             <div class="form-horizontal" role="form">
                                                                <div class="form-body">
                                                                    <div class="form-group">
                                                                        <label class="col-md-3 control-label">Nombre<span class="required"> * </span></label>
                                                                        <div class="col-md-6">
                                                                            <div class="input-group">
                                                                                <input class="form-control input-circle-left" placeholder="" type="text" id="tx_nombre" name="tx_nombre">
                                                                                <span class="input-group-addon input-circle-right">
                                                                                    <i class="fa fa-user"></i>
                                                                                </span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label class="col-md-3 control-label">Apellido<span class="required"> * </span></label>
                                                                        <div class="col-md-6">
                                                                            <div class="input-group">
                                                                                <input class="form-control input-circle-left" placeholder="" type="text" id="tx_apellido" name="tx_apellido">
                                                                                <span class="input-group-addon input-circle-right">
                                                                                    <i class="fa fa-user"></i>
                                                                                </span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label class="col-md-3 control-label">C&eacute;dula<span class="required"> * </span></label>
                                                                        <div class="col-md-6">
                                                                            <div class="input-group">
                                                                                <input class="form-control input-circle-left" placeholder="" type="text" id="tx_cedula" name="tx_cedula">
                                                                                <span class="input-group-addon input-circle-right">
                                                                                    <i class="fa fa-user"></i>
                                                                                </span>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="form-group">
                                                                        <label class="col-md-3 control-label">Sexo<span class="required"> * </span></label>
                                                                        <div class="col-md-6">
                                                                            <div class="input-group">
                                                                                <select id="sel_sexo" name="sel_sexo" class="form-control input-circle select2" style="">
                                                                                    <option value="F">Femenino</option>
                                                                                    <option value="M">Masculino</option>
                                                                                </select>
                                                                                <span class="input-group-addon input-circle-right">
                                                                                        <i class="fa fa-intersex"></i>
                                                                                    </span>
                                                                                
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="form-group">
                                                                        <label class="col-md-3 control-label">Fecha nacimiento<span class="required"> * </span></label>
                                                                        <div class="col-md-6">
                                                                            <div class="input-group">
                                                                                <input class="form-control input-circle-left" placeholder="" type="text" id="tx_fecha" name="tx_fecha">
                                                                                <span class="input-group-addon input-circle-right">
                                                                                    <i class="fa fa-calendar"></i>
                                                                                </span>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="form-group">
                                                                        <label class="col-md-3 control-label">Lugar de nacimiento<span class="required"> * </span></label>
                                                                        <div class="col-md-6">
                                                                           <div class="input-group">
                                                                                <input class="form-control input-circle-left" placeholder="" type="text" id="tx_lugar" name="tx_lugar">
                                                                                <span class="input-group-addon input-circle-right">
                                                                                    <i class="fa fa-hospital-o"></i>
                                                                                </span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- END PERSONAL INFO TAB -->
                                                        <!-- CHANGE AVATAR TAB -->
                                                        <div class="tab-pane" id="tab_1_2">
                                                            <div class="form-horizontal" role="form">
                                                                <div class="form-body">
                                                                
                                                                <div class="form-group">
                                                                        <label class="col-md-3 control-label">Profesi&oacute;n<span class="required"> * </span></label>
                                                                        <div class="col-md-6">
                                                                           <div class="input-group">
                                                                                <select id="sel_profesion" class="form-control input-circle select2" name="sel_profesion">
                                                                                </select>
                                                                                <span class="input-group-addon input-circle-right">
                                                                                    <i class="fa fa-university"></i>
                                                                                </span>

                                                                                
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                <div class="form-group">
                                                                        <label class="col-md-3 control-label">Grado de Instrucci&oacute;n<span class="required"> * </span></label>
                                                                        <div class="col-md-6">
                                                                           <div class="input-group">
                                                                            <select id="sel_grado" class="form-control input-circle select2" id="sel_grado" name="sel_grado">
                                                                            </select>
                                                                            <span class="input-group-addon input-circle-right">
                                                                                <i class="fa fa-graduation-cap"></i>
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group">
                                                                        <label class="col-md-3 control-label">&Aacute;rea de desempeño<span class="required"> * </span></label>
                                                                        <div class="col-md-6">
                                                                           <div class="input-group">
                                                                                <select class="form-control select2" id="sel_area_desempeno" name="sel_area_desempeno">
                                                                                    
                                                                                </select>
                                                                                <span class="input-group-addon input-circle-right">
                                                                                    <i class="fa fa-cubes"></i>
                                                                                </span>
                                                                                
                                                                            </div>
                                                                        </div>
                                                                </div>

                                                                <div class="form-group">
                                                                        <label class="col-md-3 control-label">Año de experiencia<span class="required"> * </span></label>
                                                                        <div class="col-md-6">
                                                                           <div class="input-group">
                                                                                <input class="form-control input-circle-left" placeholder="" type="text" id="tx_anho" name="tx_anho">
                                                                                <span class="input-group-addon input-circle-right">
                                                                                    <i class="fa fa-calendar"></i>
                                                                                </span>
                                                                            </div>
                                                                        </div>
                                                                    </div>                                                    
                                                                </div>            
                                                            </div>
                                                        </div>
                                                        <!-- END CHANGE AVATAR TAB -->
                                                        <!-- CHANGE PASSWORD TAB -->
                                                        <div class="tab-pane" id="tab_1_3">
                                                            <div class="form-horizontal" role="form">
                                                                <dir class="form-body">
                                                                    <div class="form-group">
                                                                        <label class="col-md-3 control-label">Tel&eacute;fono<span class="required"> * </span></label>
                                                                        <div class="col-md-6">
                                                                            <div class="input-group">
                                                                                <input class="form-control input-circle-left" placeholder="" type="text" id="tx_telefono" name="tx_telefono">
                                                                                <span class="input-group-addon input-circle-right">
                                                                                    <i class="fa fa-phone"></i>
                                                                                </span>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="form-group">
                                                                        <label class="col-md-3 control-label">Correo Electr&oacute;nico<span class="required"> * </span></label>
                                                                        <div class="col-md-6">
                                                                            <div class="input-group">
                                                                                <input class="form-control input-circle-left" placeholder="" type="email" id="tx_email" name="tx_email">
                                                                                <span class="input-group-addon input-circle-right">
                                                                                    <i class="fa fa-envelope"></i>
                                                                                </span>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="form-group">
                                                                        <label class="col-md-3 control-label">Direcci&oacute;n<span class="required"> * </span></label>
                                                                        <div class="col-md-6">
                                                                            <div class="input-group">
                                                                                <input class="form-control input-circle-left" placeholder="" type="text" id="tx_direccion" name="tx_direccion">
                                                                                <span class="input-group-addon input-circle-right">
                                                                                    <i class="fa fa-map-signs"></i>
                                                                                </span>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="form-group">
                                                                        <label class="col-md-3 control-label">Observaci&oacute;n</label>
                                                                        <div class="col-md-6">
                                                                            <div class="input-group">
                                                                                <input class="form-control input-circle-left" placeholder="" type="text" id="tx_observacion" name="tx_observacion">
                                                                                <span class="input-group-addon input-circle-right">
                                                                                    <i class="fa fa-pencil"></i>
                                                                                </span>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                </dir>
                                                            </div>
                                                        </div>                                                       
                                                    </div>
                                                    <div class="col-md-offset-5 margin-top-10">
                                                        <div class="margiv-top-10">
                                                            <button type="submit" class="btn green" id="btn_registrar">Registrar</button>
                                                            <a href="javascript:;" class="btn default"> Cancelar </a>
                                                        </div>
                                                    </div>
                                                    
                                                </div> 
                                                                                               
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- END PROFILE CONTENT -->
                            </div>
                            </form>
                        </div>


                    </div>
                    <!-- END CONTENT BODY -->
                </div>
                <!-- END CONTENT -->
                
            </div>
            <!-- END CONTAINER -->
            <!-- BEGIN FOOTER -->
            <!--<div class="page-footer">
                <div class="page-footer-inner"> 2016 &copy; Metronic Theme By
                    <a target="_blank" href="http://keenthemes.com">Keenthemes</a> &nbsp;|&nbsp;
                    <a href="http://themeforest.net/item/metronic-responsive-admin-dashboard-template/4021469?ref=keenthemes" title="Purchase Metronic just for 27$ and get lifetime updates for free" target="_blank">Purchase Metronic!</a>
                </div>
                <div class="scroll-to-top">
                    <i class="icon-arrow-up"></i>
                </div>
            </div>-->
            <!-- END FOOTER -->
        </div>
        <!-- BEGIN QUICK NAV -->
        
        
        <!-- END QUICK NAV -->
        <!--[if lt IE 9]>
<script src="../lib/metronic/assets/global/plugins/respond.min.js"></script>
<script src="../lib/metronic/assets/global/plugins/excanvas.min.js"></script> 
<script src="../lib/metronic/assets/global/plugins/ie8.fix.min.js"></script> 
<![endif]-->
        <!-- BEGIN CORE PLUGINS -->
        <script src="../lib/metronic/assets/global/plugins/jquery.min.js" type="text/javascript"></script>
        <script src="../lib/metronic/assets/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
        <script src="../lib/metronic/assets/global/plugins/js.cookie.min.js" type="text/javascript"></script>
        <script src="../lib/metronic/assets/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
        <script src="../lib/metronic/assets/global/plugins/jquery.blockui.min.js" type="text/javascript"></script>
        <script src="../lib/metronic/assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js" type="text/javascript"></script>

        <script src="../lib/metronic/assets/global/plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
        <script src="../lib/metronic/assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js" type="text/javascript"></script>
        
        <!-- END CORE PLUGINS -->
        <!-- BEGIN PAGE LEVEL PLUGINS -->
        <script src="../lib/metronic/assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js" type="text/javascript"></script>
        <script src="../lib/metronic/assets/global/plugins/jquery.sparkline.min.js" type="text/javascript"></script>
        <script src="../lib/metronic/assets/global/plugins/bootstrap-sweetalert/sweetalert.min.js" type="text/javascript"></script>

        <script src="../lib/metronic/assets/global/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
        <script src="../lib/metronic/assets/global/plugins/jquery-validation/js/additional-methods.min.js" type="text/javascript"></script>
        <script src="../lib/metronic/assets/global/plugins/jquery-validation/js/localization/messages_es.js" type="text/javascript"></script>
        <!-- END PAGE LEVEL PLUGINS -->
        <!-- BEGIN THEME GLOBAL SCRIPTS -->
        <script src="../lib/metronic/assets/global/scripts/app.min.js" type="text/javascript"></script>
        <!-- END THEME GLOBAL SCRIPTS -->
        <!-- BEGIN PAGE LEVEL SCRIPTS -->
        <script src="../js/curriculum.js" type="text/javascript"></script>
        <!-- END PAGE LEVEL SCRIPTS -->
        <!-- BEGIN THEME LAYOUT SCRIPTS -->
        <script src="../lib/metronic/assets/layouts/layout/scripts/layout.min.js" type="text/javascript"></script>
        <script src="../lib/metronic/assets/layouts/layout/scripts/demo.min.js" type="text/javascript"></script>
        <script src="../lib/metronic/assets/layouts/global/scripts/quick-sidebar.min.js" type="text/javascript"></script>
        <script src="../lib/metronic/assets/layouts/global/scripts/quick-nav.min.js" type="text/javascript"></script>
        <!-- END THEME LAYOUT SCRIPTS -->
    </body>

</html>