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
<title>Casos</title>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<meta content="" name="description"/>
<meta content="" name="author"/>
<!-- BEGIN GLOBAL MANDATORY STYLES -->
<link href="../../../includes/assets/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
<link href="../../../includes/assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
<link href="../../../includes/assets/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css"/>
<!-- END GLOBAL MANDATORY STYLES -->
<!-- BEGIN PAGE LEVEL STYLES -->
<link href="../../../includes/assets/plugins/jquery-nestable/jquery.nestable.css" rel="stylesheet" type="text/css"/>
<!-- END PAGE LEVEL STYLES -->
<!-- BEGIN PAGE LEVEL STYLES -->
<link rel="stylesheet" type="text/css" href="../../../includes/assets/plugins/select2/select2.css"/>
<link rel="stylesheet" type="text/css" href="../../../includes/assets/plugins/select2/select2-metronic.css"/>
<link rel="stylesheet" type="text/css" href="../../../includes/assets/plugins/jstree/dist/themes/default/style.min.css"/>
<link rel="stylesheet" type="text/css" href="../../../includes/assets/plugins/bootstrap-datepicker/css/datepicker3.css"/>
<link rel="stylesheet" type="text/css" href="../../../includes/assets/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css"/>


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
          <h3 class="page-title">Casos</h3>
          <ul class="page-breadcrumb breadcrumb">
            <li><i class="fa fa-archive"></i>
              <a style="text-decoration: none;">Casos</a><i class="fa fa-angle-right"></i>
            </li>
            <li><a style="text-decoration: none;">Nuevo Caso</a></li>
          </ul>
          <!-- END PAGE TITLE & BREADCRUMB-->
        </div>
      </div>
      <!-- END PAGE HEADER-->
      <!-- BEGIN PAGE CONTENT-->
      <!--<div class="row">
        <div class="col-md-12">-->
          <div class="row">
            <div class="col-md-3">
              <div class="tab-content">
                <div class="portlet box blue">
                  <div class="portlet-title">
                    <div class="caption">
                      <i class="fa fa-search"></i> Nuevo Caso
                    </div>
                  </div>
                  <div class="portlet-body form">
                    <div id="tree_1" class="tree-demo">
                      <ul>
                      <li data-jstree='{ "opened" : true }'>
                           Casos
                          <ul>
                            <li id="caso_nuevo" data-jstree='{ "icon" : "fa fa-briefcase" }'>
                               <a href="#">
                              Nuevo Caso </a>
                            </li>
                            <li id="listado_casos" data-jstree='{ "icon" : "fa fa-envelope" }'>
                               <a href="#">
                              Casos</a>
                            </li>
                            <li id="casos_aprobados" data-jstree='{ "icon" : "fa fa-check" }'>
                               <a href="#">
                              Casos Aprobados</a>
                            </li>
                            <li id="formulario" data-jstree='{ "icon" : "fa fa-file" }'>
                               <a href="#">
                              Formulario </a>
                            </li>                              
                          </ul>
                        </li>
                        
                      </ul>
                    </div>
                  </div>
                </div>
                
              </div>
            </div>
            <div id="cuerpo_3">
              <div class="col-md-9">
                <div class="portlet box blue">
                  <div class="portlet-title">
                    <div class="caption">
                      <i class="fa fa-gears"></i> Procesos
                    </div>
                  </div>
                  <div class="portlet-body form">
                    <div class="row">
                      <div class="col-md-10">
                      <div id="casos_procesos">
                        <?php
                        $sql = "SELECT *
                                  FROM   procesos";
                                  
                          $res = $db->query($sql);
                          $data="";
                          
                          while ($fila = $res->fetch_array()) 
                          {
                            $data.= "<div class='dd' id='nestable_list_".$fila[0]."'>";
                            $data.= '<ol class="dd-list">';
                            $data.= '<li>';
                            
                            $data.='<div class="dd-handle"><i class="glyphicon glyphicon-folder-open"></i>&nbsp; '.$fila[1].'</div>';
                            $data.='</li>';
                            $sql2="SELECT *
                                FROM subprocesos
                                WHERE id_procesos=".$fila[0];
                            $res2= $db ->query($sql2);
                            $data.= '<ol class="dd-list">';
                                              
                            while ($fila2 = $res2->fetch_array()) 
                            {
                                $data.='<li class="dd-item" data-id="'.$fila2[0].'">';
                                $data.='<div class="dd-handle">';
                                $data.= '<a rel="'.$fila2[0].'"><i class="fa fa-gear"></i>'.$fila2[2].'</a>';
                                $data.='</div>';
                                $data.='</li>';
                            }
                            $data.= "</ol>";
                            $data.= "</div>";

                          }
                          echo $data;
                        ?>

                      </div>                        
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div id="cuerpo_1">
              <div class="col-md-9">
                <div class="portlet box blue">
                  <div class="portlet-title">
                    <div class="caption">
                      <i class="fa fa-gears"></i> Procesos
                    </div>
                  </div>
                  <div class="portlet-body form">
                    <div class="row">
                    </div>
                    <div class="row">
                      <div class="col-md-3"><button class="btn btn-blue" id="agregar_proceso">Agregar Procesos</button></div>
                      <div class="col-md-9"></div>
                    </div>
                    <div class="row">
                    </div>
                    <div class="row">
                      <div class="col-md-12">
                        <table class="table table-responsive">
                          <thead>
                            <th>#</th>
                            <th>Resúmen</th>
                            <th>Notas</th>
                            <th>Caso</th>
                            <th>Proceso</th>
                            <th>Tarea</th>
                            <th>Enviado por</th>
                            <th>Fecha expiración</th>
                            <th>Última modificición</th>
                            <th>Prioridad</th>
                          </thead>
                          <tbody>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                          </tbody>
                          <tfoot></tfoot>

                        </table>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
<div class="modal fade" id="modal-casos">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">AGREGAR PROCESO</h4>
            </div>
            <div class="modal-body">
               <div class="row">
                 <label for="" class="col-md-3">Proceso</label>
                 <div class="col-md-3"><input type="text" name="procesos" id="procesos"></div>
               </div>              
            </div>
            <div class="modal-footer">
                <a href="#" class="btn btn-primary" id="registra_proceso"><i class="fa fa-plus" aria-hidden="true"></i> REGISTRAR PROCESO</a>

                <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times" aria-hidden="true"> </i>CANCELAR</button>
            </div>
        </div>
    </div>
</div>
            <div id="cuerpo_2">
              <div class="col-md-9">
                <div class="portlet box grey">
                  <div class="portlet-title">
                    <div class="caption">
                      <i class="fa fa-search"></i>SOLICITUD DE TIEMPO EXTRAORDINARIO
                    </div>
                  </div>
                  <div class="portlet-body form">                    
                    <div class="row">
                      <div class="col-md-12">
                        <div class="portlet box blue">
                          <div class="portlet-title"> 
                            DATOS DEL FUNCIONARIO
                          </div>
                          <div class="portlet-body form">
                            <form  id="Form" class="form-horizontal form-bordered">
                            <div class="row">
                              <div class="form-goup">
                                <label class="col-md-4 control-label">FECHA DE SOLICITUD</label>
                                <div class="col-md-4">
                                  <div class="input-group date date-picker" data-date-format="dd-mm-yyyy" data-date-viewmode="years">
                                    <input id="Fecha" name="Fecha" class="form-control form-control-inline" type="text" value="" readonly>
                                    <span class="input-group-btn">
                                      <button class="btn btn-primary" type="button"><i class="fa fa-calendar"></i></button>
                                    </span>
                                  </div>
                                  <span class="help-block">
                                    Seleccione la fecha </span>
                                  </div>
                              </div>
                            </div>
                            <div class="row">
                              <div class="form-goup">
                                <label class="col-md-4 control-label">NOMBRE</label>
                                <div class="col-md-4">
                                  <input type="text" name="nombre_solicitud" class="form-control">
                                </div>
                              </div>
                            </div>
                            <div class="row">
                              <div class="form-goup">
                                <label class="col-md-4 control-label">POSICIÓN</label>
                                <div class="col-md-4">
                                  <input type="text" name="posicion_solicitud" class="form-control">
                                </div>
                              </div>
                            </div>
                            <div class="row">
                              <div class="form-goup">
                                <label class="col-md-4 control-label">CÉDULA DE IDENTIDAD</label>
                                <div class="col-md-4">
                                  <input type="text" name="cedula_solicitud" class="form-control">
                                </div>
                              </div>
                            </div>
                            <div class="row">
                              <div class="form-goup">
                                <label class="col-md-4 control-label">UNIDAD ADMINISTRATIVA</label>
                                <div class="col-md-4">
                                  <input type="text" name="unidad_administrativa_solicitud" class="form-control">
                                </div>
                              </div>
                            </div>
                            <div class="row">
                              <div class="col-md-12">
                                <div class="form-group  bg-blue">
                                  &nbsp; &nbsp; &nbsp;TIEMPO SOLICITADO
                                </div> 
                              </div>
                            </div>
                            <div class="row">
                              <div class="form-goup">
                                <label for="fecha_solicitado" class="col-md-4 control-label">FECHA EN LA QUE SE TRABAJÓ</label>
                                <div class="col-md-4">
                                  <div class="input-group date date-picker" data-date-format="dd-mm-yyyy" data-date-viewmode="years">
                                    <input id="Fecha" name="Fecha" class="form-control form-control-inline" type="text" value="" readonly>
                                    <span class="input-group-btn">
                                      <button class="btn btn-primary" type="button"><i class="fa fa-calendar"></i></button>
                                    </span>
                                  </div>
                                  <span class="help-block">
                                    Seleccione la fecha </span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                              <div class="form-goup">
                                <label for="dia_solicitado" class="col-md-4 control-label">DÍA</label>
                                <div class="col-md-4">
                                  <input type="text" name="dia_solicitado" class="form-control">
                                </div>
                              </div>
                            </div>
                            <div class="row">
                              <div class="form-goup">
                                <label for="horas_solicitado" class="col-md-4 control-label">HORAS</label>
                                <div class="col-md-4">
                                  <input type="text" name="horas_solicitado" class="form-control">
                                </div>
                              </div>
                            </div>
                            <div class="row">
                              <div class="form-goup">
                                <label for="minutos_solicitado" class="col-md-4 control-label">MINUTOS</label>
                                <div class="col-md-4">
                                  <input type="text" name="minutos_solicitado" class="form-control">
                                </div>
                              </div>
                            </div>
                            <div class="row">
                              <div class="form-goup">
                                <label for="observaciones_solicitado" class="col-md-4 control-label">OBSERVACIONES</label>
                                <div class="col-md-4">
                                  <textarea name="observaciones_solicitado" class="form-control"></textarea>
                                </div>
                              </div>
                            </div>
                            <div class="row">
                              <div class="form-group">
                                <label for="" class="col-md-5"></label>
                                <div class="col-md-4"><button class="btn btn-primary">GUARDAR</button></div>
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

<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="../../../includes/assets/plugins/jquery-nestable/jquery.nestable.js"></script>
<script src="../../../includes/assets/plugins/jstree/dist/jstree.min.js"></script>

<!-- END PAGE LEVEL SCRIPTS -->
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
<script src="../../../includes/assets/scripts/custom/ui-tree.js"></script>

<!-- END PAGE LEVEL PLUGINS -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="../../../includes/assets/scripts/core/app.js"></script>
<script>
jQuery(document).ready(function() {

  App.init();
  UITree.init();

});
</script>
<script>
  $(document).ready(function(){
    $( "#cuerpo_1" ).hide();
    $( "#cuerpo_2" ).hide();
    $( "#cuerpo_3" ).hide();
/*$.get("procesos.php",function(res){
          $("div#casos_proceso").empty();
          $("div#casos_proceso").append(res);

        });*/
    $('.tree-demo').on("changed.jstree", function (e, data) {
     // console.log(data.selected);


      if ( data.selected =='caso_nuevo')
      {
        $( "#cuerpo_1" ).show();
        $( "#cuerpo_2" ).hide();
        $( "#cuerpo_3" ).hide();
        $( "#agregar_proceso" ).on('click', function(){
        // console.log(data.selected);

          $('#modal-casos').modal('show');
          $("#registra_proceso").off().on('click',function(){
            proceso = $("#procesos").val();
            $.get('registrar_procesos.php',{proceso:proceso},function(res){
              if (res==false) {
                alert("hubo un error al registrar el proceso");
              }
              $('#modal-casos').modal('hide');
              window.reload();
            });
          });
        });
      }

      if (data.selected=='formulario') 
      {
        $( "#cuerpo_1" ).hide();
        $( "#cuerpo_2" ).hide();
        $( "#cuerpo_3" ).hide();

        $( "#agregar_proceso" ).on('click', function(){
          //console.log(data.selected);

          $('#modal-casos').modal('show');
        });
      }

      if (data.selected=='listado_casos') 
      {
        $( "#cuerpo_1" ).hide();
        $( "#cuerpo_2" ).hide();
        $( "#cuerpo_3" ).show();
        $( "div.dd-handle" ).on('click', function(event){
          //console.log(data.selected);
        var id = $('.dd-item').attr('data-id');

          if (id == 1) {
            valor = $(this).val();
            console.log(valor+" "+id);
            $( "#cuerpo_2" ).show();
            $( "#cuerpo_3" ).hide();

          }

         // $('#modal-casos').modal('show');
        });
        
      }

      if (data.selected=='casos_aprobados') 
      {
        $( "#cuerpo_1" ).hide();
        $( "#cuerpo_2" ).hide();
        $( "#cuerpo_3" ).hide();

        //console.log("Aquí van los casos aprobados");
      }
    });

    $('.date-picker').datepicker({
        orientation: "left",
        language: 'es',
    autoclose: true
    }); 

   
    $("#Form").validate({
        rules: {
          fecha_solicitado: {
              required: true
          },
          Fecha: {
              required: true
          },
          observaciones_solicitado: {
              required: true
          }
          
        }
    });
  });
</script>
</body>
</html>