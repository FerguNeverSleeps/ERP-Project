<?php
require_once('../../lib/database.php');

$db = new Database($_SESSION['bd']);

if(isset($_POST['btn-guardar']))
{
  $codigo         = $_POST['codigo'];
  $nombre         = $_POST['nombre'];
  $tipo_documento = $_POST['tipo_documento'];
  $titulo         = isset($_POST['titulo']) ? $_POST['titulo'] : '';
  $contenido2     = $db->escape_string($_POST['contenido2']);
  $configuracion  = ($tipo_documento=='pdf') ? $_POST['configuracion'] : 'Ninguno';
  $formula        = isset($_POST['formula']) ? $_POST['formula'] : '';     

  $margen_sup = isset($_POST['margen_sup']) ? $_POST['margen_sup'] : '';
  $margen_izq = isset($_POST['margen_izq']) ? $_POST['margen_izq'] : '';
  $margen_der = isset($_POST['margen_der']) ? $_POST['margen_der'] : '';

  $archivo    = isset($_POST['archivo']) ? $_POST['archivo'] : '';
  $fuente     = isset($_POST['fuente'])  ? $_POST['fuente']  : '';

  $template   = $_POST['txttemplate'];

  $target_dir  = "templates/";

  $target_file = $target_dir . basename($_FILES["template"]["name"]);
  $uploadOk = 1;
  $fileType = pathinfo($target_file, PATHINFO_EXTENSION);
  $nombre_fichero = $_FILES["template"]["name"];

  $conAcentos = array('á','é','í','ó','ú',' ','&aacute;','&eacute;','&iacute;','&oacute;','&uacute;');
  $sinAcentos = array('a','e','i','o','u','_','a','e','i','o','u');
  $nombre_fichero = str_replace($conAcentos, $sinAcentos, strtolower($nombre_fichero));

  $target_file = $target_dir . basename($nombre_fichero);

  if($fileType != "pdf" && $fileType != "PDF") 
  {
      $uploadOk = 0;
  }
  if($uploadOk == 1) 
  {
      if(move_uploaded_file($_FILES["template"]["tmp_name"], $target_file)) 
      {
          $template = $nombre_fichero;
      } 
  }

  $sql = "UPDATE nomtipos_constancia SET 
          nombre         = '{$nombre}', 
          tipo_documento = '{$tipo_documento}',
          contenido2     =  NULLIF('{$contenido2}',''),
          titulo         =  NULLIF('{$titulo}',''),
          configuracion  = '{$configuracion}',
          template       =  NULLIF('{$template}',''),
          formula        =  NULLIF('{$formula}',''),
          archivo        =  NULLIF('{$archivo}',''),
          fuente         =  NULLIF('{$fuente}',''),
          margen_sup     =  NULLIF('{$margen_sup}',''),
          margen_izq     =  NULLIF('{$margen_izq}',''),
          margen_der     =  NULLIF('{$margen_der}','')
          WHERE codigo = '{$codigo}'";  
  
  $res = $db->query($sql);       

  echo "<script>location.href='listado_modelos_constancias.php';</script>";
}

// Consultar datos del modelo de constancia
$codigo_modelo = $_POST['codigo'];

$sql = "SELECT codigo, nombre, tipo_documento, contenido2, titulo, configuracion, template, formula, archivo,
               fuente, margen_sup, margen_izq, margen_der
        FROM   nomtipos_constancia
        WHERE  codigo = '{$codigo_modelo}'";

$res = $db->query($sql);

$modelo = $res->fetch_object();
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
<title>Editar Modelo Constancia</title>
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
<style>
body { /* En uso */
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

.padding-right-30{ /* En uso */
  padding-right: 30px;
}

.form-group { /* En uso */
    margin-bottom: 35px;
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
            <li><a style="text-decoration: none;">Constancias</a><i class="fa fa-angle-right"></i></li>
            <li><a href="listado_modelos_constancias.php">Modelos de Constancias</a></li>
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
                        <i class="fa fa-edit"></i> Editar Modelo de Constancia
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

                      <form action="#" id="form1" name="form1" class="form-horizontal" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="codigo" id="codigo" value="<?php echo isset($codigo_modelo) ? $codigo_modelo : ''; ?>">
                        
                        <div class="form-body">
                            <div class="form-group">
                              <label class="control-label col-md-2">Nombre del modelo <span class="required">*</span></label>
                              <div class="col-md-10 padding-right-30">
                                <input type="text" name="nombre" id="nombre" class="form-control" value="<?php echo $modelo->nombre; ?>">
                              </div>
                            </div>

                            <div class="form-group">
                              <label class="control-label col-md-2">Formato del modelo</label>
                              <div class="col-md-10 padding-right-30">
                                <div class="radio-list">
                                  <?php
                                      $tipo_documento1 = ($modelo->tipo_documento == 'pdf')  ? 'checked' : '';
                                      $tipo_documento2 = ($modelo->tipo_documento == 'docx') ? 'checked' : '';
                                      $display_pdf     = ($modelo->tipo_documento == 'pdf')  ? 'block' : 'none';
                                      $display_docx    = ($modelo->tipo_documento == 'docx') ? 'block' : 'none'; 
                                  ?>
                                  <label class="radio-inline">
                                  <input type="radio" name="tipo_documento" id="tipo_documento1" value="pdf" <?php echo $tipo_documento1; ?>> pdf</label>
                                  <label class="radio-inline">
                                  <input type="radio" name="tipo_documento" id="tipo_documento2" value="docx" <?php echo $tipo_documento2; ?>> docx</label>
                                </div>
                              </div>
                            </div>

                            <div class="div-tipo-pdf" style="display: <?php echo $display_pdf; ?>;">
                              <div class="form-group">
                                <label class="control-label col-md-2">T&iacute;tulo</label>
                                <div class="col-md-10 padding-right-30">
                                  <input type="text" name="titulo" id="titulo" class="form-control" value="<?php echo $modelo->titulo; ?>">
                                </div>
                              </div>

                              <div class="form-group">
                                <label class="control-label col-md-2">Contenido</label>
                                <div class="col-md-10 padding-right-30">
                                  <textarea cols="30" rows="5" name="contenido2" id="contenido2"><?php echo $modelo->contenido2; ?></textarea>
                                  <script>
                                    CKEDITOR.stylesSet.add('default', [
                                        // Block Styles
                                        { name: 'Espaciado: 200%',       element: 'p',      styles: { 'line-height': '200%' } },
                                        { name: 'Espaciado: 195%',       element: 'p',      styles: { 'line-height': '195%' } },
                                        { name: 'Espaciado: 190%',       element: 'p',      styles: { 'line-height': '190%' } },
                                        { name: 'Espaciado: 180%',       element: 'p',      styles: { 'line-height': '180%' } },
                                        { name: 'Espaciado: 170%',       element: 'p',      styles: { 'line-height': '170%' } },
                                        { name: 'Espaciado: 160%',       element: 'p',      styles: { 'line-height': '160%' } },
                                        { name: 'Espaciado: 150%',       element: 'p',      styles: { 'line-height': '150%' } },
                                        { name: 'Espaciado: 140%',       element: 'p',      styles: { 'line-height': '140%' } },
                                        { name: 'Espaciado: 130%',       element: 'p',      styles: { 'line-height': '130%' } },
                                        { name: 'Espaciado: 120%',       element: 'p',      styles: { 'line-height': '120%' } },
                                        { name: 'Espaciado: 110%',       element: 'p',      styles: { 'line-height': '110%' } },
                                        { name: 'Espaciado: 100%',       element: 'p',      styles: { 'line-height': '100%' } },
                                        { name: 'Espaciado: 90%',        element: 'p',      styles: { 'line-height': '90%' } },
                                        { name: 'Espaciado: 80%',        element: 'p',      styles: { 'line-height': '80%' } },
                                        { name: 'Espaciado: 70%',        element: 'p',      styles: { 'line-height': '70%' } },
                                        { name: 'Espaciado: 60%',        element: 'p',      styles: { 'line-height': '60%' } },
                                        { name: 'Espaciado: 50%',        element: 'p',      styles: { 'line-height': '50%' } },
                                        { name: 'Espaciado: 45%',        element: 'p',      styles: { 'line-height': '45%' } },
                                        { name: 'Espaciado: 40%',        element: 'p',      styles: { 'line-height': '40%' } },
                                        { name: 'Espaciado: 30%',        element: 'p',      styles: { 'line-height': '30%' } },
                                        { name: 'Espaciado: 20%',        element: 'p',      styles: { 'line-height': '20%' } },
                                        { name: 'Espaciado: 10%',        element: 'p',      styles: { 'line-height': '10%' } },
                                        { name: 'Espaciado: 5%',         element: 'p',      styles: { 'line-height': '5%' } },
                                        // Inline Styles
                                        { name: 'Resaltar: Amarillo',   element: 'span',    styles: { 'background-color': 'Yellow' } },
                                    ]);

                                    CKEDITOR.replace('contenido2', {
                                      extraPlugins: 'tableresize,tabletools',
                                      height: '700px',
                                      resize_dir: 'vertical',
                                      toolbar: [['Source'],
                                                ['Bold', 'Italic', 'Underline'],
                                                ['JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'],
                                                ['Image', 'Table', 'HorizontalRule'], 
                                                ['TextColor', 'BGColor'],
                                                [ 'Styles', 'Format', 'Font', 'FontSize']]
                                    }); 
                                  </script>
                                </div>
                              </div> 

                              <div class="form-group">
                                <label class="control-label col-md-2">M&aacute;rgenes Contenido</label>
                                <div class="col-md-10 padding-right-30">
                                  <div class="col-md-3">
                                    <input type="text" class="form-control" name="margen_sup" id="margen_sup" placeholder="Margen Superior" value="<?php echo $modelo->margen_sup; ?>">
                                  </div>
                                  <div class="col-md-3">
                                    <input type="text" class="form-control" name="margen_izq" id="margen_izq" placeholder="Margen Izquierdo" value="<?php echo $modelo->margen_izq; ?>">
                                  </div>
                                  <div class="col-md-3">
                                    <input type="text" class="form-control" name="margen_der" id="margen_der" placeholder="Margen Derecho" value="<?php echo $modelo->margen_der; ?>">
                                  </div>
                                </div>
                              </div>

                              <div class="form-group">
                                <?php
                                    $configuracion1 = ($modelo->configuracion=='Header')   ? 'checked' : '';
                                    $configuracion2 = ($modelo->configuracion=='Template') ? 'checked' : '';
                                    $configuracion3 = ($modelo->configuracion=='Ninguno')  ? 'checked' : '';
                                    $display_template =  ($modelo->configuracion=='Template') ? 'block' : 'none';
                                ?>
                                <label class="control-label col-md-2">Fondo del PDF</label>
                                <div class="col-md-10 padding-right-30">
                                  <div class="radio-list">
                                    <label><input type="radio" name="configuracion" id="configuracion1" value="Header"   <?php echo $configuracion1; ?>> Utilizar encabezado y pie de p&aacute;gina de la configuraci&oacute;n general</label>
                                    <label><input type="radio" name="configuracion" id="configuracion2" value="Template" <?php echo $configuracion2; ?>> Utilizar Template</label>
                                    <label><input type="radio" name="configuracion" id="configuracion3" value="Ninguno"  <?php echo $configuracion3; ?>> Ninguno</label>
                                  </div>
                                </div>
                              </div>

                              <div class="div-template" style="display: <?php echo $display_template; ?>;">
                                <div class="form-group">
                                  <label class="control-label col-md-2">Nombre del Template</label>
                                  <div class="col-md-10 padding-right-30">
                                    <input type="text" name="txttemplate" id="txttemplate" class="form-control" value="<?php echo $modelo->template; ?>" readonly>
                                  </div>
                                </div>                       

                                <div class="form-group">
                                  <label class="control-label col-md-2">Cargar Template</label>
                                  <div class="col-md-10 padding-right-30">
                                    <input type="file" name="template" id="template" style="border: none">
                                  </div>
                                </div>                              
                              </div>

                              <div class="form-group">
                                <label class="control-label col-md-2">F&oacute;rmulas</label>
                                <div class="col-md-10 padding-right-30">
                                  <textarea class="form-control" name="formula" id="formula" rows="8" style="resize: vertical;"><?php echo $modelo->formula; ?></textarea>
                                </div>
                              </div>

                              <div class="form-group">
                                <?php
                                    $fuentes = array(''=>'Courier (Por defecto)', 'Calibri'=>'Calibri');
                                ?>
                                <label class="control-label col-md-2">Fuente</label>
                                <div class="col-md-10 padding-right-30">
                                    <select class="form-control select2me" name="fuente" id="fuente">
                                      <?php
                                          foreach ($fuentes as $key => $value) 
                                          {
                                              if($key == $modelo->fuente)
                                                echo "<option value='{$key}' selected>{$value}</option>";
                                              else
                                                echo "<option value='{$key}'>{$value}</option>";

                                          }
                                      ?>
                                    </select>
                                </div>
                              </div>
                            </div>  

                            <div class="div-tipo-docx" style="display: <?php echo $display_docx; ?>;">
                                <div class="form-group">
                                  <label class="control-label col-md-2">Archivo PHP <span class="required">*</span></label>
                                  <div class="col-md-10 padding-right-30">
                                    <input type="text" name="archivo" id="archivo" class="form-control" value="<?php echo $modelo->archivo; ?>">
                                  </div>
                                </div>     
                            </div>
                        </div>
                        <div class="form-actions fluid">
                          <div class="col-md-12 text-center">
                            <button type="submit" class="btn btn-sm blue active" id="btn-guardar" name="btn-guardar">Guardar</button>
                            <button type="button" class="btn btn-sm default"
                            onclick="javascript: document.location.href='listado_modelos_constancias.php'">Cancelar</button>
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
            nombre:  {required: true},
            archivo: {
                required: function(){
                    var tipo_documento = $("[name='tipo_documento']:checked").val();
                    // console.log("Tipo documento: "+tipo_documento);
                    return (tipo_documento=='docx');
                }
            },
            template: {
                required: function(){
                    var configuracion = $("[name='configuracion']:checked").val();
                    var txttemplate   = $("#txttemplate").val();
                    return (configuracion=='Template' && txttemplate=='');
                },
                extension: "pdf"
            },
            margen_sup: {
                number: true
            },
            margen_izq: {
                number: true
            },
            margen_der: {
                number: true
            }
        },
        messages: {
            template: {
                extension: "Extensión de archivo no válida"
            },
            margen_sup: {
                number: "Número no válido"
            },
            margen_izq: {
                number: "Número no válido"
            },
            margen_der: {
                number: "Número no válido"
            }
        }
    });

    $("[name='tipo_documento']").change(function(){
        var tipo_doc = $(this).val();

        if(tipo_doc=='pdf')
        {
            $(".div-tipo-docx").hide();
            $(".div-tipo-pdf").show();

            $("label[for='archivo'].error").hide();
        }
        else
        {
            $(".div-tipo-pdf").hide();
            $(".div-tipo-docx").show();
        }
    });

    $("[name='configuracion']").change(function(){
        var configuracion = $(this).val();

        if(configuracion=='Template')
        {
            $(".div-template").show();
        }
        else
        {
            $(".div-template").hide();
        }
    });

    $("#btn-guardar").click(function(){
        var form1 = $('#form1').valid();

        if(!form1) return false;
    });
});
</script>
</body>
</html>
