<?php
require_once('../../lib/database.php');

$db = new Database($_SESSION['bd']);

// Verificar si se envío el formulario
if(isset($_POST['codigo']))
{
  $codigo     = $_POST['codigo'];
  $encabezado = $db->escape_string($_POST['encabezado']);
  $pie_pagina = $db->escape_string($_POST['pie_pagina']);
  $titulo     = isset($_POST['titulo']) ? $_POST['titulo'] : '';
  $validar_constancias = $_POST['validar_constancias'];
  $codigo_verificacion = $_POST['codigo_verificacion'];
  $posicionx_codigo = isset($_POST['posicionx_codigo']) ? $_POST['posicionx_codigo'] : '';
  $posiciony_codigo = isset($_POST['posiciony_codigo']) ? $_POST['posiciony_codigo'] : '';
  $texto_codigo = isset($_POST['texto_codigo']) ? $_POST['texto_codigo'] : '';

  if($codigo=='')
  {
    // Insertar configuracion general
    $sql = "INSERT INTO nomconf_constancia 
            (encabezado, pie_pagina, titulo, validar_constancias, codigo_verificacion, 
            posicionx_codigo, posiciony_codigo, texto_codigo) VALUES
            (NULLIF('{$encabezado}',''), NULLIF('{$pie_pagina}',''), NULLIF('{$titulo}',''),
             NULLIF('{$validar_constancias}',''), NULLIF('{$codigo_verificacion}',''),
             NULLIF('{$posicionx_codigo}',''), NULLIF('{$posiciony_codigo}',''),
             NULLIF('{$texto_codigo}',''))";
  }
  else
  {
    // Actualizar configuracion general de constancias
    $sql = "UPDATE nomconf_constancia SET 
            encabezado = NULLIF('{$encabezado}',''),
            pie_pagina = NULLIF('{$pie_pagina}',''),
            titulo     = NULLIF('{$titulo}',''),
            validar_constancias = NULLIF('{$validar_constancias}',''),
            codigo_verificacion = NULLIF('{$codigo_verificacion}',''),
            posicionx_codigo = NULLIF('{$posicionx_codigo}',''),
            posiciony_codigo = NULLIF('{$posiciony_codigo}',''),
            texto_codigo     = NULLIF('{$texto_codigo}','')
            WHERE codigo = '{$codigo}'";               
  }
  $res = $db->query($sql); 

  echo "<script> window.location.href='configuracion_general.php';</script>";
}

// Consultamos los datos de la tabla de configuracion general de constancias (nomconf_constancia)
$sql = "SELECT codigo, encabezado, pie_pagina, titulo, slogan, cargo_gerente, abreviatura, observaciones,
               cantidad_mensual, tipo_validacion, 
               codigo_verificacion, posicionx_codigo, posiciony_codigo, texto_codigo, validar_constancias
        FROM   nomconf_constancia";
$res = $db->query($sql);

$conf = $res->fetch_object();
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
<script src="../ckeditor/ckeditor.js"></script>
<script>
function enviar(){
  /*
    var limite = document.form1.txtcantidad.value;
    if(!/^([0-9])*$/.test(limite)){
        alert("Limite mensual invalido");
        return false;
    } */

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

.padding-left-0{ /* En uso */
  padding-left: 0px;
}

.padding-right-30{ /* En uso */
  padding-right: 30px;
}

.form-group { /* En uso */
  margin-bottom: 35px;
}

.margin-bottom-0{ /* En uso */
  margin-bottom: 0px !important; 
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
              <a style="text-decoration: none;">Configuraci&oacute;n</a><i class="fa fa-angle-right"></i>
            </li>
            <li><a href="../submenu_constancias.php">Constancias</a></li>
          </ul>
          <!-- END PAGE TITLE & BREADCRUMB-->
        </div>
      </div>
      <!-- END PAGE HEADER-->
      <!-- BEGIN PAGE CONTENT-->
      <div class="row">
        <div class="col-md-12">
          <div class="row">
            <div class="col-md-12">
              <div class="tab-content">
                  <div class="portlet box blue">
                    <div class="portlet-title">
                      <div class="caption">
                        <i class="fa fa-edit"></i> Configuraci&oacute;n General Constancias PDF
                      </div>
                      <!--
                      <div class="actions">
                        <a class="btn btn-sm blue active"  onclick="javascript: window.location='../submenu_constancias.php'">
                          <i class="fa fa-arrow-left"></i> Regresar
                        </a>
                      </div>
                      -->
                    </div>
                    <div class="portlet-body form">
                      <form action="#" id="form1" name="form1" class="form-horizontal" method="post">
                        <input type="hidden" name="codigo" id="codigo" value="<?php echo (isset($conf->codigo)) ? $conf->codigo : ''; ?>">
                        <div class="form-body">
                            <div class="form-group">
                              <label class="control-label col-md-2">T&iacute;tulo</label>
                              <div class="col-md-10 padding-right-30">
                                <input type="text" name="titulo" id="titulo" class="form-control" value="<?php  echo (isset($conf->titulo)) ? $conf->titulo : '';  ?>">
                              </div>
                            </div>
                            <div class="form-group">
                              <label class="control-label col-md-2">Encabezado</label>
                              <div class="col-md-10 padding-right-30">
                                <textarea cols="30" rows="5" name="encabezado" id="encabezado"><?php echo (isset($conf->encabezado)) ? $conf->encabezado : ''; ?></textarea>
                                <script>
                                 CKEDITOR.replace( 'encabezado', {
                                  extraPlugins: 'tableresize',
                                  height: '245px',
                                  resize_dir: 'vertical',
                                  toolbar: [['Source'],['Bold', 'Italic', 'Underline' ],['JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'],['Image', 'Table', 'HorizontalRule' ]]
                                }); 
                                </script>
                              </div>
                            </div>
                            <div class="form-group">
                              <label class="control-label col-md-2">Pie de p&aacute;gina</label>
                              <div class="col-md-10 padding-right-30">
                                <textarea cols="30" rows="5" name="pie_pagina" id="pie_pagina"><?php echo (isset($conf->pie_pagina)) ? $conf->pie_pagina : ''; ?></textarea>
                                <script>
                                 CKEDITOR.replace( 'pie_pagina', {
                                  extraPlugins: 'tableresize',
                                  height: '210px',
                                  resize_dir: 'vertical',
                                  toolbar: [['Source'],['Bold', 'Italic', 'Underline' ],['JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'],['Image', 'Table', 'HorizontalRule' ]]
                                }); 
                                </script>
                              </div>
                            </div>

                            <?php
                                $validar_constancias2 = 'checked';

                                if($conf->validar_constancias != '')
                                {
                                    $validar_constancias1 = ($conf->validar_constancias == 'Si') ? 'checked' : '';
                                    $validar_constancias2 = ($conf->validar_constancias == 'No') ? 'checked' : '';
                                }
                            ?>
                            <div class="form-group">
                              <label class="control-label col-md-2">Validar Constancias</label>
                              <div class="col-md-10 padding-right-30">
                                <div class="radio-list">
                                  <label class="radio-inline">
                                  <input type="radio" name="validar_constancias" id="validar_constancias1" value="Si" <?php echo $validar_constancias1; ?>> Sí</label>
                                  <label class="radio-inline">
                                  <input type="radio" name="validar_constancias" id="validar_constancias2" value="No" <?php echo $validar_constancias2; ?>> No</label>
                                </div>
                              </div>
                            </div>

                            <?php
                                $codigo_verificacion2 = 'checked';
                                $display_codigo = 'none';
                                $display_class  = 'margin-bottom-0';

                                if($conf->codigo_verificacion != '')
                                {
                                  $codigo_verificacion1 = ($conf->codigo_verificacion == 'Si') ? 'checked' : '';
                                  $codigo_verificacion2 = ($conf->codigo_verificacion == 'No') ? 'checked' : '';
                                  $display_codigo =  ($conf->codigo_verificacion == 'Si') ? 'block' : 'none';
                                  $display_class  =  ($conf->codigo_verificacion == 'No') ? 'margin-bottom-0' : '';
                                }
                            ?>
                            <div class="form-group form-group-codigo <?php echo $display_class; ?>">
                              <label class="control-label col-md-2">C&oacute;digo de Verificación</label>
                              <div class="col-md-10 padding-right-30">
                                <div class="radio-list">
                                  <label class="radio-inline">
                                  <input type="radio" name="codigo_verificacion" id="codigo_verificacion1" value="Si" <?php echo $codigo_verificacion1; ?>> Sí</label>
                                  <label class="radio-inline">
                                  <input type="radio" name="codigo_verificacion" id="codigo_verificacion2" value="No" <?php echo $codigo_verificacion2; ?>> No</label>
                                </div>
                              </div>
                            </div>

                            <div class="div-codigo-verificacion" style="display: <?php echo $display_codigo; ?>">
                                <div class="form-group">
                                  <label class="control-label col-md-2">Posici&oacute;n</label>
                                  <div class="col-md-10 padding-right-30">
                                    <div class="col-md-3 padding-left-0">
                                      <input type="text" class="form-control" name="posicionx_codigo" id="posicionx_codigo" placeholder="Coordenada X" value="<?php echo (isset($conf->posicionx_codigo)) ? $conf->posicionx_codigo : ''; ?>">
                                    </div>
                                    <div class="col-md-3 padding-left-0">
                                      <input type="text" class="form-control" name="posiciony_codigo" id="posiciony_codigo" placeholder="Coordenada Y" value="<?php echo (isset($conf->posiciony_codigo)) ? $conf->posiciony_codigo : ''; ?>">
                                    </div>
                                  </div>
                                </div>
                                <div class="form-group margin-bottom-0">
                                  <label class="control-label col-md-2">Texto C&oacute;digo de Verificaci&oacute;n</label>
                                  <div class="col-md-10 padding-right-30">
                                    <textarea cols="30" rows="3" name="texto_codigo" id="texto_codigo"><?php echo (isset($conf->texto_codigo)) ? $conf->texto_codigo : ''; ?></textarea>
                                    <script>
                                     CKEDITOR.replace( 'texto_codigo', {
                                      extraPlugins: 'tableresize',
                                      height: '60px',
                                      resize_dir: 'vertical',
                                      toolbar: [['Source'], ['Bold', 'Italic', 'Underline' ], 
                                                ['JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'],
                                                ['TextColor', 'BGColor'],
                                                ['Font', 'FontSize']]
                                    }); 
                                    </script>
                                  </div>
                                </div>
                            </div>

                        </div>
                        <div class="form-actions fluid">
                          <div class="col-md-12 text-center">
                            <button type="button" class="btn btn-sm blue active" 
                            onclick="javascript: enviar();">Guardar</button>
                            <button type="button" class="btn btn-sm default"
                            onclick="javascript: document.location.href='../submenu_constancias.php'">Cancelar</button>
                          </div>
                        </div>
                      </form>
                    </div>
                  </div>
                
              </div>
            </div>
          </div>
        </div>
      </div>
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
            posicionx_codigo: {
                number: true
            },
            posiciony_codigo: {
                number: true
            }
        },
        messages: {
            posicionx_codigo: {
                number: "Coordenada X no válida"
            },
            posiciony_codigo: {
                number: "Coordenada Y no válida"
            }
        }
    });

    $("[name='validar_constancias']").change(function(){
        var validar_constancias = $(this).filter(':checked').val();

        if(validar_constancias == 'Si')
        {
          $('#codigo_verificacion1').prop('checked', true);
          $('#codigo_verificacion1').change();
          $.uniform.update("[name='codigo_verificacion']");
        }
    });

    $("[name='codigo_verificacion']").change(function(){
        var codigo_verificacion = $(this).filter(':checked').val();

        if(codigo_verificacion=='Si')
        {
            $(".div-codigo-verificacion").show();
            $(".form-group-codigo").removeClass('margin-bottom-0');
        }
        else
        {
            $(".div-codigo-verificacion").hide();
            $(".form-group-codigo").addClass('margin-bottom-0');
        }
    });
  });
</script>
</body>
</html>