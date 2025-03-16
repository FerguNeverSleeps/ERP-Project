<?php 
//require_once '../../generalp.config.inc.php';
session_start();
ob_start();

//error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require_once '../lib/common.php';
include ("func_bd.php");

$conexion=conexion();

function agregar_fila()
{
?>
  <tr id="actual0">        
    <td><input type="text" name="desde" id="desde"></td>
    <td><input type="text" name="hasta" id="hasta"></td>
    <td><input type="text" name="monto" id="resultado" onblur="javascript: guardar('0','agregar')"></td>
    <td></td>
    <td></td>
    <td></td>
  </tr>
<?php
}

$codigo = @$_GET['codigo'];
   
$strsql = "SELECT * FROM nomtarifas WHERE codigo='".$codigo."' ORDER BY codigo";
$result = sql_ejecutar($strsql); // $resultado=query($consulta,$conexion);    
?>
<?php include("../header4.php"); // <html><head></head><body> ?>
<link href="../../includes/assets/css/custom-datatables.css" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="mantenimiento_dinamico2.js"></script>
<style type="text/css">
  .portlet > .portlet-title > .actions > .btn.btn-sm {
    margin-top: -9px !important;
  }
</style>
<div class="page-container">
  <!-- BEGIN SIDEBAR -->
  <!-- END SIDEBAR -->
  <!-- BEGIN CONTENT -->
  <div class="page-content-wrapper">
    <div class="page-content">
      <!-- BEGIN PAGE CONTENT-->
      <div class="row">
        <div class="col-md-12">
          <!-- BEGIN EXAMPLE TABLE PORTLET-->
          <div class="portlet box blue">
            <div class="portlet-title">
              <div class="caption">
               Detalles de baremos
              </div>
              <div class="actions">
                <a class="btn btn-sm blue"  onclick="javascript: window.location='baremos.php'">
                  <i class="fa fa-arrow-left"></i> Regresar
                </a>
              </div>
            </div>
            <div class="portlet-body">
              <form action="" method="post" name="frmPrincipal" id="frmPrincipal">
              <input id="codigo" type="hidden" name="codigo" value="<?php echo $codigo; ?>">
              <table class="table table-striped table-bordered table-hover" id="table_datatable">
              <thead>
              <tr>
                <th>Desde</th>
                <th>Hasta</th>
                <th>Resultado</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
              </tr>
              </thead>
              <tbody id="tbody">
              <?php
                $i=0;
                while( $fila = mysqli_fetch_array($result) ) // $fila=fetch_array($res)
                { 
                  $i++;                
                ?>
                  <tr class="odd gradeX" id="actual<?php echo $i; ?>">
                    <td><?php echo $fila['limite_menor']; ?></td>
                    <td><?php echo $fila['limite_mayor']; ?></td>
                    <td><?php echo $fila['monto']; ?></td>
                    <?php
                        $codigo = $fila["codigo"];
                        $hasta  = $fila["limite_mayor"];
                       // icono("javascript:agregar_despues('$i','agregar')", "Agregar despues de este registro", "add.gif");
                       // icono("javascript:editar('$i','$codigo','$hasta')", "Editar", "edit.gif");
                       // icono("javascript:eliminar('$codigo','$hasta')", "Eliminar", "delete.gif");
                    ?>
                    <td style="text-align: center">
                      <a href="javascript: agregar_despues('<?php echo $i; ?>')" title="Agregar despues de este registro"><img src="../../includes/imagenes/icons/add.png" width="16" height="16"></a>
                    </td>
                    <td style="text-align: center">
                      <a href="javascript:editar('<?php echo $i; ?>','<?php echo $codigo; ?>','<?php echo $hasta; ?>')" title="Editar"><img src="../../includes/imagenes/icons/pencil.png" alt="Editar" width="16" height="16"></a>                    
                    </td>
                    <td style="text-align: center">
                      <a href="javascript:eliminar('<?php echo $codigo; ?>','<?php echo $hasta; ?>')" title="Eliminar"><img src="../../includes/imagenes/icons/delete.png" width="16" height="16"></a>                      
                    </td>
                  </tr>
                  <?php                 
                }

                if($i == 0)
                {
                    agregar_fila();
                }
              ?>
              </tbody>
              </table>
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
<?php include("../footer4.php"); ?>
<script type="text/javascript">
   $(document).ready(function() { 

            $('#table_datatable').DataTable({
              "iDisplayLength": 10,
                //"sPaginationType": "bootstrap",
              "sPaginationType": "bootstrap_extended", 
                "oLanguage": {
                  "sSearch": "<img src='../../includes/imagenes/icons/magnifier.png' width='16' height='16' > Buscar:",
                    "sLengthMenu": "Mostrar _MENU_",
                    //"sInfo": "Showing page _PAGE_ of _PAGES_", // Mostrando 1 to 5 de 18 entradas
                    //"sInfoEmpty": "No hay registros para mostrar",
                    "sInfoEmpty": "",
                    //"sInfo": "",
                    "sInfo":"Total _TOTAL_ registros",
                    "sInfoFiltered": "",
                  "sEmptyTable":  "No hay datos disponibles",
                    "sZeroRecords": "No se encontraron registros",
                    "oPaginate": {
                        "sPrevious": "P&aacute;gina Anterior",//"Prev",
                        "sNext": "P&aacute;gina Siguiente",//"Next",
                        "sPage": "P&aacute;gina",//"Page",
                        "sPageOf": "de",//"of"
                    }
                },
                "aLengthMenu": [ // set available records per page
                    [5, 10, 25, 50,  -1],
                    [5, 10, 25, 50, "Todos"]
                ],                
                "aoColumnDefs": [
                    { 'bSortable': false, 'aTargets': [3] },
                    { "bSearchable": false, "aTargets": [3] },
                    { "sWidth": "8%", "aTargets": [3] },
                    { 'bSortable': false, 'aTargets': [4] },
                    { "bSearchable": false, "aTargets": [4] },
                    { "sWidth": "8%", "aTargets": [4] },
                    { 'bSortable': false, 'aTargets': [5] },
                    { "bSearchable": false, "aTargets": [5] },
                    { "sWidth": "8%", "aTargets": [5] },
                ],
         "fnDrawCallback": function() {
                $('#table_datatable_filter input').attr("placeholder", "Escriba frase para buscar");
         }
            });

            $('#table_datatable').on('change', 'tbody tr .checkboxes', function(){
                 $(this).parents('tr').toggleClass("active");
            });

            $('#table_datatable_wrapper .dataTables_filter input').addClass("form-control input-medium input-inline"); 
            $('#table_datatable_wrapper .dataTables_length select').addClass("form-control input-xsmall"); 
   });
</script>
</body>
</html>