<?php
require_once "config/db.php";
include ("utils/detectar_errores_datos.php");
include ("../func_bd.php");

$archivo_reloj=(isset($_GET['archivo'])) ? $_GET['archivo'] : '' ;
$registro_id=(isset($_POST['registro_id'])) ? $_POST['registro_id'] : ''; 
$op=(isset($_POST['op'])) ? $_POST['op'] : '';

$flash_message = ( isset($_GET['msj']) || isset($_GET['del']) ) ? ( isset($_GET['msj']) ? $_GET['msj'] : $_GET['del'] )  : '';
$flash_class   = ( isset($_GET['del']) ) ? 'alert-danger' : 'alert-info';

if ($op==3)
{
  try {
      $conexion->delete('caa_archivos_datos', array('codigo' => $registro_id));

      activar_pagina("mantenimiento_datos.php?archivo=".$archivo_reloj."&del=".'Registro eliminado');
  }
  catch (Exception $e) {
      $flash_message = "<strong>¡Ha ocurrido un error!</strong><p>" . $e->getMessage() ."</p>";
      $flash_class   = 'alert-danger';
  }
}
?>
<?php include("vistas/layouts/header.php"); ?>
<body class="page-header-fixed page-full-width" marginheight="0">
<style type="text/css">
  .alert {
    padding: 10px;
  }

.text-red {
    color: red;
}

tr.warning td{
  background-color: #F6F99D !important;
}

tr.danger td{
  background-color: #F9C1BF !important;
}

tr.info td{
  background-color: #A3DCED !important;
}



</style>
<div class="page-container">
  <!-- BEGIN CONTENT -->
  <div class="page-content-wrapper">
    <div class="page-content">
      <!-- BEGIN PAGE CONTENT-->
      <div class="row">
        <div class="col-md-12">
          <?php
              if(!empty($flash_message))
              {
                ?> <div class="alert <?php echo $flash_class ; ?>"><button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button><?php echo $flash_message; ?></div> <?php
              }
          ?>
          <!-- BEGIN EXAMPLE TABLE PORTLET-->
          <div class="portlet box blue">
            <div class="portlet-title">
              <div class="caption">
               Mantenimiento de datos del archivo No <?php echo $archivo_reloj; ?>
              </div>
              <div class="actions">
                <a class="btn btn-sm blue"  onclick="javascript: window.location='agregar_movimiento_reloj.php?archivo=<?php echo $archivo_reloj; ?>'">
                  <i class="fa fa-plus"></i>
                  Agregar
                </a>
                <a class="btn btn-sm blue"  onclick="javascript: window.location='listado_archivos.php'">
                  <i class="fa fa-arrow-left"></i> Regresar
                </a>
              </div>
            </div>
            <div class="portlet-body" id="blockui_portlet_body">
              <div class="table-toolbar">
                <div class="btn-group">&nbsp;
                  <!--<button id="sample_editable_1_new" class="btn btn-sm blue"><i class="fa fa-plus"></i> Tipo de Movimiento</button>-->
                </div>
                <div class="btn-group pull-right">
                <div class="actions">
                  <a href="procesar_asistencias.php?archivo=<?php echo $archivo_reloj;?>" class="btn btn-sm blue"><i class="fa fa-cogs" aria-hidden="true"></i> Procesar</a>

                  <button class="btn btn-sm red dropdown-toggle" data-toggle="dropdown">Corregir <i class="fa fa-angle-down"></i></button>
                  <ul class="dropdown-menu pull-right">
                    <li><a href="javascript:corregir_error('All','<?php echo $archivo_reloj; ?>');">Autom&aacute;ticamente</a></li>
                    <li><a href="javascript:corregir_error('0',  '<?php echo $archivo_reloj; ?>');">Tipo de Movimiento</a></li>
                    <li class="divider"></li>
                    <li><a href="javascript:corregir_error('5',  '<?php echo $archivo_reloj; ?>');" onclick="return confirm('\u00BFEst\u00E1 seguro de efectuar este cambio?')">Convertir en entrada</a></li>
                    <li><a href="javascript:corregir_error('6',  '<?php echo $archivo_reloj; ?>');" onclick="return confirm('\u00BFEst\u00E1 seguro de efectuar este cambio?')">Convertir en salida</a></li>
                    <li><a href="javascript:corregir_error('7',  '<?php echo $archivo_reloj; ?>');" onclick="return confirm('\u00BFEst\u00E1 seguro de realizar esta accion?')">Eliminar movimiento</a></li>
                  </ul>
                </div>
                </div>
              </div>
              <form action="" method="post" name="frmPrincipal" id="frmPrincipal">
                  <!--<div class="table-responsive">-->
                    <table class="table" id="table_datatable"> <!-- table-striped table-hover -->
                        <thead>
                            <tr>
                                <th><input type="checkbox" class="group-checkable"></th>
                                <th>Ficha</th>
                                <th>Fecha y Hora</th>
                                <th>Tipo de Movimiento</th>
                                <th>Dispositivo</th>
                                <th></th>
                                <th></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                  <!--</div>-->

                  <input name="registro_id" type="hidden" value="">
                  <input name="op" type="hidden" value="">  
              </form>
            </div>
          </div>
          <!-- END EXAMPLE TABLE PORTLET-->
        </div>
      </div>
      <!-- END PAGE CONTENT-->
    </div>
  </div>
  <!-- END CONTENT -->
</div>
<?php include("vistas/layouts/footer.php"); ?>
<script>var archivo_reloj = '<?php echo $archivo_reloj; ?>'</script>
<script src="web/js/mantenimiento_datos.js?<?php echo time(); ?>" type="text/javascript"></script>
</body>
</html>