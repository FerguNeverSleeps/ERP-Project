<?php 
session_start();
ob_start();
$termino=$_SESSION['termino'];
?>
<!-- BEGIN GLOBAL MANDATORY STYLES
<link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css"/> -->
<link href="../../includes/assets/plugins/font-awesome/css/font-awesome.css" rel="stylesheet" type="text/css"/>
<link href="../../includes/css/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css"/>
<link href="../../includes/assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
<link href="../../includes/assets/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css"/>
<link href="../../includes/assets/plugins/bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet" type="text/css"/>
<!-- END GLOBAL MANDATORY STYLES -->
<!-- BEGIN PAGE LEVEL STYLES -->
<link rel="stylesheet" type="text/css" href="../../includes/assets/plugins/select2/select2.css"/>
<link rel="stylesheet" type="text/css" href="../../includes/assets/plugins/select2/select2-metronic.css"/>
<link rel="stylesheet" href="../../includes/assets/plugins/data-tables/DT_bootstrap.css"/>
<!-- END PAGE LEVEL STYLES -->
<!-- BEGIN THEME STYLES -->
<link href="../../includes/css/components.css" rel="stylesheet" type="text/css"/>
<link href="../../includes/assets/css/style-metronic.css" rel="stylesheet" type="text/css"/>
<link href="../../includes/assets/css/style.css" rel="stylesheet" type="text/css"/>
<link href="../../includes/assets/css/style-responsive.css" rel="stylesheet" type="text/css"/>
<link href="../../includes/assets/css/plugins.css" rel="stylesheet" type="text/css"/>
<!-- <link href="../../includes/assets/css/themes/default.css" rel="stylesheet" type="text/css" id="style_color"/> -->
<link href="../../includes/assets/css/custom.css" rel="stylesheet" type="text/css"/>
<!-- END THEME STYLES -->
<link href="../../includes/assets/css/custom-header4.css" rel="stylesheet" type="text/css"/>
</head>
<!-- END HEAD -->
<!-- BEGIN BODY -->
<body class="page-header-fixed page-full-width"  marginheight="0">
<div class="page-container">
  <!-- BEGIN CONTENT -->
  <div class="page-content-wrapper">
    <div class="page-content">
      <!-- BEGIN PAGE CONTENT-->
      <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-center">
          <!-- BEGIN EXAMPLE TABLE PORTLET-->
          <div class="portlet box blue">
            <div class="portlet-title">
              <h4>Tipos</h4>
            </div>
            <div class="portlet-body">
              <div class="row">
                <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 text-center">
                  <a href="tipos_nominas.php">
                    <div class="tile bg-blue">
                      <div class="tile-body">
                      <i class="fa fa-cubes"></i>
                          <h5>Tipos de <?echo $termino?> </h5>
                      </div>
                    </div>
                  </a>
                </div>
                <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 text-center">
                  <a href="frecuencias.php">
                    <div class="tile bg-blue">
                      <div class="tile-body">
                      <i class="glyphicon glyphicon-th-large"></i>
                          <h5>Tipos de Frecuencias de Pago</h5>
                      </div>
                    </div>
                  </a>
                </div>
                <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 text-center">
                  <a href="acumulados.php">
                    <div class="tile bg-blue">
                      <div class="tile-body">
                      <i class="glyphicon glyphicon-cog"></i>
                          <h5>Tipos de Acumulados</h5>
                      </div>
                    </div>
                  </a>
                </div>
                <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 text-center">
                  <a href="liquidaciones.php">
                    <div class="tile bg-blue">
                      <div class="tile-body">
                      <i class="glyphicon glyphicon-list"></i>
                          <h5>Tipos de Liquidaciones</h5>                            
                      </div>
                    </div>
                  </a>
                </div>
              </div>
              <div class="row">
                <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 text-center">
                  <a href="aumentos.php">
                    <div class="tile bg-blue">
                      <div class="tile-body">
                      <i class="fa fa-cogs"></i>
                          <h5> Modificaciones y Aumentos</h5>
                      </div>
                    </div>
                  </a>
                </div>
                <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 text-center">
                  <a href="suspenciones.php">
                    <div class="tile bg-blue">
                      <div class="tile-body">
                      <i class="fa fa-file-text"></i>
                          <h5>Licencias, Suspensiones y Permisos</h5>                            
                      </div>
                    </div>
                  </a>
                </div>
                <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 text-center">
                  <a href="prestamos.php">
                    <div class="tile bg-blue">
                      <div class="tile-body">
                      <i class="glyphicon glyphicon-user"></i>
                          <h5>Acreedores</h5>
                      </div>
                    </div>
                  </a>
                </div> 
                <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 text-center">
                  <a href="parentescos.php">
                    <div class="tile bg-blue">
                      <div class="tile-body">
                      <i class="glyphicon glyphicon-user"></i>
                          <h5>Parentescos</h5>
                      </div>
                    </div>
                  </a>
                </div>
              </div>
              <div class="row">
                <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 text-center">
                  <a href="motivos_retiros.php">
                    <div class="tile bg-blue">
                      <div class="tile-body">
                      <i class="glyphicon glyphicon-list-alt"></i>
                          <h5>Motivo de Retiros</h5>
                      </div>
                    </div>
                  </a>
                </div>
                <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 text-center">
                  <a href="situaciones.php">
                    <div class="tile bg-blue">
                      <div class="tile-body">
                      <i class="fa fa-archive"></i>
                          <h5>Situaciones</h5>
                      </div>
                    </div>
                  </a>
                </div>
                <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 text-center">
                  <a href="guarderias.php">
                    <div class="tile bg-blue">
                      <div class="tile-body text-center">
                      <i class="fa fa-tasks"></i>
                          <h5>Tipos de Guarderías</h5>
                      </div>
                    </div>
                  </a>
                </div>
                <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 text-center">
                  <a href="tipos_empresa.php">
                    <div class="tile bg-blue">
                      <div class="tile-body text-center">
                      <i class="fa fa-sitemap"></i>
                          <h5>Tipos de Empresa</h5>
                      </div>
                    </div>
                  </a>
                </div> 
              </div>
              <div class="row">
                <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 text-center">
                  <a href="tipos_turno.php">
                    <div class="tile bg-blue">
                      <div class="tile-body text-center">
                      <i class="fa fa-edit"></i>
                          <h5>Tipos de Turno</h5>
                      </div>
                    </div>
                  </a>
                </div> 
                <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 text-center">
                  <a href="tipos_reloj.php">
                    <div class="tile bg-blue">
                      <div class="tile-body text-center">
                      <i class="fa fa-clock-o"></i>
                          <h5>Tipos de Reloj</h5>
                      </div>
                    </div>
                  </a>
                </div>
                <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 text-center">
                  <a href="control_asistencia/list_incidencias.php">
                    <div class="tile bg-blue">
                      <div class="tile-body text-center">
                      <i class="fa fa-tag" aria-hidden="true"></i>
                          <h5>Tipos de Incidencias</h5>
                      </div>
                    </div>
                  </a>
                </div>
                <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 text-center">
                  <a href="../../configuracion/tipos/permisos/tipos_permisos_list.php">
                    <div class="tile bg-blue">
                      <div class="tile-body text-center">
                        <i class="fa fa-bookmark" aria-hidden="true"></i>
                          <h5>Tipos de Permisos</h5>
                      </div>
                    </div>
                  </a>
                </div>
              </div>
              <div class="row">
                <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 text-center">
                  <a href="../../configuracion/tipos/reportes/tipos_reportes_list.php">
                    <div class="tile bg-blue">
                      <div class="tile-body text-center">
                        <i class="fa fa-bookmark" aria-hidden="true"></i>
                          <h5>Tipos de Reportes</h5>
                      </div>
                    </div>
                  </a>
                </div>
                <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 text-center">
                  <a href="../../configuracion/tipos/tipos_acreedores_list.php">
                    <div class="tile bg-blue">
                      <div class="tile-body text-center">
                        <i class="fa fa-bank" aria-hidden="true"></i>
                          <h5>Tipos de Acreedores</h5>
                      </div>
                    </div>
                  </a>
                </div>  
                <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 text-center">
                  <a href="../../configuracion/tipos/tipos_prestamos_list.php">
                    <div class="tile bg-blue">
                      <div class="tile-body text-center">
                        <i class="icon-wallet" aria-hidden="true"></i>
                          <h5>Tipos de Préstamos</h5>
                      </div>
                    </div>
                  </a>
                </div>
                <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 text-center">
                  <a href="./proyectos_list.php">
                    <div class="tile bg-blue">
                      <div class="tile-body text-center">
                        <i class="icon-briefcase" aria-hidden="true"></i>
                          <h5>Proyectos</h5>
                      </div>
                    </div>
                  </a>
                </div>
              </div>
              <div class="row">
                <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 text-center">
                  <a href="./bisemanas_list.php">
                    <div class="tile bg-blue">
                      <div class="tile-body text-center">
                        <i class="icon-calendar" aria-hidden="true"></i>
                          <h5>Bisemanas</h5>
                      </div>
                    </div>
                  </a>
                </div>
              </div>

            <!-- END PORTLET BODY-->
            
          </div>
        <!-- END EXAMPLE TABLE PORTLET-->
        </div>
      </div>
    <!-- END PAGE CONTENT-->
    </div>
  </div>
    <!-- END CONTENT -->
</div>



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
<script type="text/javascript" src="../../includes/assets/plugins/select2/select2.min.js"></script>
<script type="text/javascript" src="../../includes/assets/plugins/data-tables/jquery.dataTables.js"></script>
<script type="text/javascript" src="../../includes/assets/plugins/data-tables/DT_bootstrap.js"></script>
<!-- END PAGE LEVEL PLUGINS -->


