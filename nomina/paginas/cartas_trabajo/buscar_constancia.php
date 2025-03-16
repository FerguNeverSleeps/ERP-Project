<?php
require_once('../../lib/database.php');

$db = new Database($_SESSION['bd']);

if(isset($_POST['btn-aceptar']))
{
    $cedula = $_POST['cedula'];
    $codigo = $_POST['codigo_verificacion'];

    // Buscar constancia
    $sql = "SELECT np.codigo as codigo 
            FROM   nompersonal_constancias np
            INNER JOIN nompersonal n ON n.ficha=np.ficha
            WHERE  n.cedula='{$cedula}' AND np.codigo_validacion='{$codigo}'";
    $res = $db->query($sql);

    if($fila = $res->fetch_array())
        echo "<script>location.href='validar_constancia.php?codigo='+".$fila['codigo'].";</script>";
    else
        echo "<script>alert('Constancia no encontrada'); location.href='buscar_constancia.php';</script>";
}
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="es" class="no-js">
<!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
<meta charset="utf-8"/>
<title>Configuraci&oacute;n General de Constancias</title>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<meta content="" name="description"/>
<meta content="" name="author"/>
<!-- BEGIN GLOBAL MANDATORY STYLES -->
<link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css"/>
<link href="../../../includes/assets/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
<link href="../../../includes/assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
<link href="../../../includes/assets/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css"/>
<!-- END GLOBAL MANDATORY STYLES -->
<!-- BEGIN PAGE LEVEL STYLES -->
<link rel="stylesheet" type="text/css" href="../../../includes/assets/plugins/select2/select2.css"/>
<link rel="stylesheet" type="text/css" href="../../../includes/assets/plugins/select2/select2-metronic.css"/>
<!-- BEGIN THEME STYLES -->
<link href="../../../includes/assets/css/style-metronic.css" rel="stylesheet" type="text/css"/>
<link href="../../../includes/assets/css/style.css" rel="stylesheet" type="text/css"/>
<link href="../../../includes/assets/css/style-responsive.css" rel="stylesheet" type="text/css"/>
<link href="../../../includes/assets/css/plugins.css" rel="stylesheet" type="text/css"/>
<link href="../../../includes/assets/css/themes/default.css" rel="stylesheet" type="text/css" id="style_color"/>
<link href="../../../includes/assets/css/custom.css" rel="stylesheet" type="text/css"/>
<!-- END THEME STYLES -->
<script>
function enviar(){
    document.form1.submit();
}
</script>
<style>
body {  /* En uso */
  background-color: white !important; 
}

.page-content-wrapper { /* En uso */
  background-color: white !important; 
}

.page-sidebar-closed .page-content { /* En uso */
  margin-left: 0px !important;
}

.portlet > .portlet-title > .caption { /* En uso */
  font-family: helvetica, arial, verdana, sans-serif;
  font-size: 13px;
  font-weight: bold;
  line-height: 21px;
  margin-bottom: 5px;
}

label.error { /* En uso */
    color: #b94a48;
}
</style>
</head>
<!-- END HEAD -->
<!-- BEGIN BODY -->
<body class="page-full-width">
<div class="clearfix"></div>
<!-- BEGIN CONTAINER -->
<div class="page-container">
  <!-- BEGIN CONTENT -->
  <div class="page-content-wrapper">
    <div class="page-content">
      <!-- BEGIN PAGE HEADER-->
      <div class="row">
        <div class="col-md-12">
          <!-- BEGIN PAGE TITLE & BREADCRUMB-->
          <h3 class="page-title">Constancias</h3>
          <ul class="page-breadcrumb breadcrumb">
            <li><i class="glyphicon glyphicon-wrench"></i>
              <a style="text-decoration: none;">Consultas</a><i class="fa fa-angle-right"></i>
            </li>
            <li><a style="text-decoration: none;">Validar Constancias</a></li>
          </ul>
          <!-- END PAGE TITLE & BREADCRUMB-->
        </div>
      </div>
      <!-- END PAGE HEADER-->
      <!-- BEGIN PAGE CONTENT-->
      <!--<div class="row">
        <div class="col-md-12">-->
          <div class="row">
            <div class="col-md-9">
              <div class="tab-content">
                  <div class="portlet box blue">
                    <div class="portlet-title">
                      <div class="caption">
                        <i class="fa fa-search"></i> Buscar Constancia
                      </div>
                    </div>
                    <div class="portlet-body form">
                      <form action="#" id="form1" name="form1" class="form-horizontal" method="post">
                          <div class="form-body">
                              <div class="form-group">
                                <label class="control-label col-md-4">C&eacute;dula de identidad</label>
                                <div class="col-md-4">
                                    <input type="text" name="cedula" id="cedula" class="form-control" value="">
                                </div>
                              </div>
                              <div class="form-group">
                                <label class="control-label col-md-4">C&oacute;digo de verificaci&oacute;n</label>
                                <div class="col-md-4">
                                    <input type="text" name="codigo_verificacion" id="codigo_verificacion" class="form-control" value="">
                                </div>
                              </div>
                          </div>
                          <div class="form-actions fluid">
                            <div class="col-md-12 text-center">
                              <button type="submit" name="btn-aceptar" class="btn btn-sm blue active">Aceptar</button>
                            </div>
                          </div>
                      </form>
                    </div>
                  </div>
                
              </div>
            </div>
          </div>
          <!--
        </div>
      </div>-->
      <!-- END PAGE CONTENT-->
    </div>
  </div>
  <!-- END CONTENT -->
</div>
<!-- END CONTAINER -->
<!-- BEGIN FOOTER -->
<!-- END FOOTER -->
<!-- BEGIN JAVASCRIPTS(Load javascripts at bottom, this will reduce page load time) -->
<!-- BEGIN CORE PLUGINS -->
<!--[if lt IE 9]>
<script src="../../../includes/assets/plugins/respond.min.js"></script>
<script src="../../../includes/assets/plugins/excanvas.min.js"></script>
<![endif]-->
<script src="../../../includes/assets/plugins/jquery-1.10.2.min.js" type="text/javascript"></script>
<script src="../../../includes/assets/plugins/jquery-migrate-1.2.1.min.js" type="text/javascript"></script>
<!-- IMPORTANT! Load jquery-ui-1.10.3.custom.min.js before bootstrap.min.js to fix bootstrap tooltip conflict with jquery ui tooltip -->
<script src="../../../includes/assets/plugins/jquery-ui/jquery-ui-1.10.3.custom.min.js" type="text/javascript"></script>
<script src="../../../includes/assets/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script src="../../../includes/assets/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js" type="text/javascript"></script>
<script src="../../../includes/assets/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
<script src="../../../includes/assets/plugins/jquery.blockui.min.js" type="text/javascript"></script>
<script src="../../../includes/assets/plugins/jquery.cokie.min.js" type="text/javascript"></script>
<script src="../../../includes/assets/plugins/uniform/jquery.uniform.min.js" type="text/javascript"></script>
<!-- END CORE PLUGINS -->
<!-- BEGIN PAGE LEVEL PLUGINS -->
<script type="text/javascript" src="../../../includes/assets/plugins/jquery-validation/dist/jquery.validate.min.js"></script>
<script type="text/javascript" src="../../../includes/assets/plugins/jquery-validation/dist/additional-methods.min.js"></script>
<script type="text/javascript" src="../../../includes/assets/plugins/jquery-validation/localization/messages_es.js"></script>
<script type="text/javascript" src="../../../includes/assets/plugins/bootstrap-select/bootstrap-select.min.js"></script>
<script type="text/javascript" src="../../../includes/assets/plugins/select2/select2.min.js"></script>
<script type="text/javascript" src="../../../includes/assets/plugins/select2/select2_locale_es.js"></script>
<script type="text/javascript" src="../../../includes/assets/plugins/bootstrap-datepicker-1.5.1/js/bootstrap-datepicker.min.js"></script>
<script type="text/javascript" src="../../../includes/assets/plugins/bootstrap-datepicker-1.5.1/locales/bootstrap-datepicker.es.min.js"></script>
<script type="text/javascript" src="../../../includes/assets/plugins/fancybox/source/jquery.fancybox.pack.js"></script>
<script type="text/javascript" src="../../../includes/assets/plugins/bootstrap-fileinput/bootstrap-fileinput.js"></script>
<!-- END PAGE LEVEL PLUGINS -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="../../../includes/assets/scripts/core/app.js"></script>
<script>
jQuery(document).ready(function() {
  App.init();
});
</script>
<script>
  $(document).ready(function(){

    $("#form1").validate({
        rules: {
          cedula: {
              required: true
          },
          codigo_verificacion: {
              required: true
          }
        }
    });
  });
</script>
</body>
</html>