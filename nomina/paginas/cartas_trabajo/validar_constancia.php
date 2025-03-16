<?php
require_once('../../lib/database.php');

date_default_timezone_set('America/Panama');

$db = new Database($_SESSION['bd']);

if(isset($_POST['btn-aceptar']))
{
	$codigo = $_POST['codigo'];
	$fecha_validacion =  date("Y-m-d H:i:s");

	$sql = "UPDATE nompersonal_constancias SET 
	        	validada='Si', 
	        	fecha_validacion='{$fecha_validacion}'
            WHERE codigo='{$codigo}'";
    $db->query($sql);

    echo "<script>location.href='validar_constancia.php?codigo='+".$codigo.";</script>";
}

if(isset($_GET['codigo']))
{
	$codigo = $_GET['codigo'];

	$sql = "SELECT np.codigo, np.codigo_validacion as codigo_verificacion, nt.codigo as constancia_id, nt.nombre as tipo_constancia, 
	               n.cedula, np.ficha, n.tipnom, n.apenom as colaborador,
			       DATE_FORMAT(np.fecha_emision,'%d-%m-%Y %h:%i %p') as fecha_emision, np.validada,
			       DATE_FORMAT(np.fecha_validacion,'%d-%m-%Y %h:%i %p') as fecha_validacion
			FROM   nompersonal_constancias np
			INNER JOIN nomtipos_constancia nt ON nt.codigo=np.tipo_constancia
			INNER JOIN nompersonal n ON np.ficha=n.ficha
			WHERE np.codigo='{$codigo}'";
	$res = $db->query($sql);
	$constancia =  $res->fetch_object();

	// Consultar si la validación de constancias está activa
	$sql = "SELECT validar_constancias FROM nomconf_constancia";
	$res = $db->query($sql);
	$conf_general = $res->fetch_object();
}
else
{
   echo "<script>alert('Acceso Denegado');</script>";
   echo "<script>document.location.href = 'buscar_constancia.php';</script>";
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
<link href="../../../includes/assets/plugins/select2/select2.css" rel="stylesheet" type="text/css" />
<link href="../../../includes/assets/plugins/select2/select2-metronic.css" rel="stylesheet" type="text/css" />
<link href="../../../includes/assets/plugins/fancybox/source/jquery.fancybox.css" rel="stylesheet" type="text/css"/>
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

.form-horizontal .control-label { /* En uso */
    text-align: left;
    padding-left: 40px;
}

.form-actions.fluid { /* En uso */
	margin-top: 0px;
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
                      <div class="caption"> Datos de la constancia
                      </div>
                    </div>
                    <div class="portlet-body form">
                      <form action="#" id="form1" name="form1" class="form-horizontal" method="post">
                  			<input type="hidden" name="codigo" id="codigo" value="<?php echo $codigo; ?>">
                            <div class="form-body">
                              	<div class="form-group">
                                	<label class="control-label col-md-4">C&oacute;digo de verificaci&oacute;n:</label>
                                	<div class="col-md-8"><p class="form-control-static"><?php echo $constancia->codigo_verificacion; ?></p></div>
                              	</div>
                              	<div class="form-group">
                                	<label class="control-label col-md-4">Tipo de Constancia:</label>
                                	<div class="col-md-8"><p class="form-control-static"><?php echo $constancia->tipo_constancia; ?></p></div>
                              	</div>
                              	<div class="form-group">
                                	<label class="control-label col-md-4">N° Colaborador:</label>
                                	<div class="col-md-8"><p class="form-control-static"><?php echo $constancia->ficha; ?></p></div>
                              	</div>
                              	<div class="form-group">
                                	<label class="control-label col-md-4">Colaborador:</label>
                                	<div class="col-md-8"><p class="form-control-static"><?php echo $constancia->colaborador; ?></p></div>
                              	</div>
                              	<div class="form-group">
                                	<label class="control-label col-md-4">Fecha de emisi&oacute;n:</label>
                                	<div class="col-md-8"><p class="form-control-static"><?php echo $constancia->fecha_emision; ?></p></div>
                              	</div>
                            	<div class="form-group">
                                	<label class="control-label col-md-4">Constancia validada:</label>
                                	<div class="col-md-8"><p class="form-control-static"><?php echo $constancia->validada; ?></p></div>
                            	</div>
								<?php
									$class_aceptar = ($conf_general->validar_constancias=='No' || $constancia->validada=='Si') ? 'hide' : ''; 

									if($constancia->validada=='Si')
									{
									?>
								  	<div class="form-group">
								    	<label class="control-label col-md-4">Fecha validaci&oacute;n:</label>
								    	<div class="col-md-8"><p class="form-control-static"><?php echo $constancia->fecha_validacion; ?></p></div>
								  	</div>
									<?php
									}
									else if($conf_general->validar_constancias=='Si')
									{
									?>
									<!--
								    <div class="form-group">
								    	<label class="control-label col-md-4">¿Desea validar la constancia?</label>
								    	<div class="col-md-8">
			                                <div class="radio-list">
			                                  <label class="radio-inline">
			                                  <input type="radio" name="validar_constancia" id="validar_constancia1" value="Si"> Sí</label>
			                                  <label class="radio-inline">
			                                  <input type="radio" name="validar_constancia" id="validar_constancia2" value="No" checked> No</label>
			                                </div>									    		
								    	</div>
								    </div>   
								    -->                         
									<?php
									}
								?>
                          	</div>
	                        <div class="form-actions fluid">
	                            <div class="col-md-11 text-center">
	                            	<button type="submit" name="btn-aceptar" id="btn-aceptar" class="btn btn-sm blue active <?php echo $class_aceptar; ?>"><i class="fa fa-check"></i> Validar</button>
	                            	<!--
	                                <a href="constancia_pdf.php?ficha=<?php echo $constancia->ficha; ?>&tipnom=<?php echo $constancia->tipnom; ?>&constancia_id=<?php echo $constancia->constancia_id; ?>&cove=<?php echo $constancia->codigo_verificacion; ?>" target="_blank" class="fancybox fancybox.iframe btn btn-sm blue active">Ver Constancia</a>
	                                -->
	                                <button type="button" class="btn btn-sm default"
                        					onclick="javascript: document.location.href='buscar_constancia.php'"><?php echo ($class_aceptar=='') ? 'Cancelar' : 'Regresar'; ?></button>
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

		$("#btn-aceptar").click(function(){
			var resp = confirm("¿Esta seguro que desea validar la constancia?");

			if(!resp)
			{
				return false;
			}
		});

		$('.fancybox').fancybox( {topRatio:0, width:1000} );
	});
</script>
</body>
</html>