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
      $conexion->delete('caa_archivos_datos', array('archivo_reloj' => $registro_id));
      $conexion->delete('caa_archivos_reloj', array('codigo' => $registro_id));

      activar_pagina("listado_archivos.php?del=".'Archivo de reloj eliminado');
  } 
  catch (Exception $e) {
      $flash_message = "<strong>¡Ha ocurrido un error!</strong><p>" . $e->getMessage() ."</p>";
      $flash_class   = 'alert-danger';
  }
}  

/*$qbr = $conexion->createQueryBuilder()
			    ->select('ca.codigo', 'ca.fecha_registro', 'ca.fecha_inicio', 'ca.fecha_fin', 'cc.descripcion as configuracion')
			    ->from('caa_archivos_reloj', 'ca')
          ->innerJoin('ca', 'caa_configuracion', 'cc', 'ca.configuracion = cc.codigo');

$res = $qbr->execute();   */
$res = $conexion->query( "SELECT ca.codigo, ca.fecha_registro, ca.fecha_inicio, ca.fecha_fin, cc.descripcion as configuracion FROM caa_archivos_reloj as ca INNER JOIN caa_configuracion cc ON ca.configuracion = cc.codigo'" );
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
               Archivos de reloj importados
              </div>
              <div class="actions">
                <a class="btn btn-sm blue"  onclick="javascript: window.location='agregar_archivo_reloj.php'">
                  <i class="fa fa-plus"></i>
                  Cargar Archivo
                </a>
              </div>
            </div>
            <div class="portlet-body" id="blockui_portlet_body">
              <form action="" method="post" name="frmPrincipal" id="frmPrincipal">
                  <table class="table table-striped table-bordered table-hover" id="table_datatable">
                  <thead>
                  <tr>
                    <th>C&oacute;digo</th>
                    <th>Configuración</th>                    
                    <th>Fecha de Inicio</th>
                    <th>Fecha Fin</th>
                    <th>Fecha de Registro</th>
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
                        <td><?php echo $fila['configuracion']; ?></td>                        
                        <td><?php echo date('d-m-Y h:i a', strtotime($fila['fecha_inicio'])); ?></td>
                        <td><?php echo date('d-m-Y h:i a', strtotime($fila['fecha_fin'])); ?></td>
                        <td><?php echo date('d-m-Y h:i a', strtotime($fila['fecha_registro'])) ; ?></td>
                        <td style="text-align: center">
                            <a href="mantenimiento_datos.php?archivo=<?php echo($fila['codigo']); ?>" title="Mantenimiento de datos"><img src="web/images/icons/tools.png" alt="Mantenimiento" width="16" height="16"></a>
                        </td>
                        <td style="text-align: center">
                            <a href="javascript:enviar(<?php echo(3); ?>,'<?php echo($fila['codigo']); ?>');" title="Eliminar archivo"><img src="web/images/icons/delete.png" alt="Eliminar" width="16" height="16"></a>
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
<script src="web/js/listado_archivos.js?<?php echo time(); ?>" type="text/javascript"></script>
</body>
</html>