<?php 
// require_once '../../generalp.config.inc.php';
session_start();
ob_start();

//error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require_once '../lib/common.php';
include ("func_bd.php");
$conexion=conexion();

$sql = "SELECT * FROM nombaremos ORDER BY codigo";  
$res = query($sql, $conexion);   
?>
<?php include("../header4.php"); // <html><head></head><body> ?>
<link href="../../includes/assets/css/custom-datatables.css" rel="stylesheet" type="text/css"/>
<script type="text/javascript">
</script>
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
               Baremos
              </div>
              <div class="actions">
                <a class="btn btn-sm blue"  onclick="javascript: window.location='baremos_agregar.php'">
                  <i class="fa fa-plus"></i>
                  Agregar
                </a>
                <a class="btn btn-sm blue"  onclick="javascript: window.location='submenu_formulacion_conceptos.php'">
                  <i class="fa fa-arrow-left"></i> Regresar
                </a>
              </div>
            </div>
            <div class="portlet-body">
              <form action="" method="post" name="baremos" id="baremos">
              <table class="table table-striped table-bordered table-hover" id="table_datatable">
              <thead>
              <tr>
                <th>C&oacute;digo</th>
                <th>Descripci&oacute;n</th>
                <th>Tipo de Dato</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
              </tr>
              </thead>
              <tbody>
              <?php
                while( $fila = mysqli_fetch_array($res) ) // $fila=fetch_array($res)
                {                 
                ?>
                  <tr class="odd gradeX">
                    <td><?php echo $fila['codigo']; ?></td>
                    <td><?php echo $fila['descripcion']; ?></td>
                    <td><?php echo $fila['tipodato']; ?></td>
                    <td style="text-align: center">
                        <a href="javascript: window.location='baremos_editar.php?codigo=<?php echo $fila['codigo']; ?>'" title="Editar"><img src="../../includes/imagenes/icons/pencil.png" alt="Editar" width="16" height="16"></a>
                    </td>

                    <td style="text-align: center">
                        <a href="javascript: window.location='baremos_eliminar.php?codigo=<?php echo $fila['codigo']; ?>'" title="Eliminar"><img src="../../includes/imagenes/icons/delete.png" alt="Eliminar" width="16" height="16"></a>
                    </td>

                    <td style="text-align: center">
                    <a href="javascript: window.location='baremos_detalles.php?codigo=<?php echo $fila['codigo']; ?>'" title="Detalles del Baremo"><img src="../imagenes/list.gif" width="16"></a>
                    </td>


                  </tr>
                  <?php                 
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