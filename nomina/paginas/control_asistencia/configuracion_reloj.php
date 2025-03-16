<?php
require_once "config/db.php";
include ("../func_bd.php");

$registro_id=(isset($_POST['registro_id'])) ? $_POST['registro_id'] : ''; 
$op=(isset($_POST['op'])) ? $_POST['op'] : '';

$flash_message = ( isset($_GET['msj']) || isset($_GET['del']) ) ? ( isset($_GET['msj']) ? $_GET['msj'] : $_GET['del'] )  : '';
$flash_class   = ( isset($_GET['del']) ) ? 'alert-danger' : 'alert-info';

if ($op==3) 
{ 
  try {
      //$conexion->query('caa_parametros',    array('configuracion' => $registro_id));
      $conexion->query("DELETE a.* FROM caa_parametros a WHERE a.configuracion =".$registro_id);
      $conexion->query("DELETE a.* FROM caa_configuracion a WHERE a.codigo =".$registro_id);
      //$conexion->query('caa_configuracion', array('codigo' => $registro_id));

      activar_pagina("configuracion_reloj.php?del=".'Formato de reloj eliminado');
  } 
  catch (Exception $e) {
      //$code = $e->getPrevious()->getCode();
      $flash_message = "<strong>¡Ha ocurrido un error!</strong><p>" . $e->getMessage() ."</p>";
      $flash_class   = 'alert-danger';
  }
}  
$res = $conexion->query( "SELECT c.*, ct.nombre as tipo_reloj FROM caa_configuracion as c INNER JOIN caa_tiporeloj ct ON c.tipo_reloj = ct.codigo" );
?>
<?php include("vistas/layouts/header.php"); ?>
<body class="page-header-fixed page-full-width" marginheight="0">
<style type="text/css">
  .alert {
    padding: 10px;
  }
  .portlet > .portlet-title > .actions > .btn.btn-sm {
    margin-top: -9px !important;
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
               Formatos de Reloj
              </div>
              <div class="actions">
                <a class="btn btn-sm blue"  onclick="javascript: window.location='agregar_formato_reloj.php'">
                  <i class="fa fa-plus"></i>
                  Agregar
                </a>
              </div>
            </div>
            <div class="portlet-body">
              <form action="" method="post" name="frmPrincipal" id="frmPrincipal">
                  <table class="table table-striped table-bordered table-hover" id="table_datatable">
                  <thead>
                  <tr>
                    <th>C&oacute;digo</th>
                    <th>Descripci&oacute;n</th>
                    <th>Formato</th>
                    <th>Delimitador</th>
                    <th>Tipo de reloj</th>
                    <th></th>
                    <th></th>
                  </tr>
                  </thead>
                  <tbody>
                  <?php
                    while( $fila = mysqli_fetch_array($res) )
                    {                 
                    ?>
                      <tr class="odd gradeX">
                        <td><?php echo $fila['codigo']; ?></td>
                        <td><?php echo $fila['descripcion']; ?></td>
                        <td><?php echo $fila['formato']; ?></td>
                        <td><?php echo $fila['delimitador']; ?></td>
                        <td><?php echo $fila['tipo_reloj']; ?></td>
                        <td style="text-align: center">
                            <a href="javascript:enviar(<?php echo(2); ?>,'<?php echo($fila['codigo']); ?>');" title="Editar"><img src="web/images/icons/pencil.png" width="16" height="16"></a>
                        </td>
                        <td style="text-align: center">
                            <a href="javascript:enviar(<?php echo(3); ?>,'<?php echo($fila['codigo']); ?>');" title="Eliminar"><img src="web/images/icons/delete.png" alt="Eliminar" width="16" height="16"></a>
                        </td>
                      </tr>
                      <?php                 
                    }
                  ?>
                  </tbody>
                  </table>
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
<script src="web/js/configuracion_reloj.js?<?php echo time(); ?>" type="text/javascript"></script>
</body>
</html>