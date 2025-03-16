<?php 
require_once '../../generalp.config.inc.php';
session_start();
ob_start();

error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require_once '../lib/common.php';
include ("func_bd.php");
$conexion=conexion();

if(isset($_GET['cod_eliminar']))
{
  $consulta  = "DELETE FROM nomgradospasos WHERE grado=" . $_GET['cod_eliminar'];
  $resultado = query($consulta, $conexion);
}

$consulta  = "SELECT * FROM nomgradospasos ORDER BY grado";
$resultado = query($consulta, $conexion);    
?>
<?php include("../header4.php"); // <html><head></head><body> ?>
<link href="../../includes/assets/css/custom-datatables.css" rel="stylesheet" type="text/css"/>
<script type="text/javascript">
function confirmar2(valor)
{
  if (confirm("\u00BFEst\u00E1 seguro que desea eliminar este registro?") == true) 
    window.location.href = "gradospasos_list.php?cod_eliminar=" + valor;
}
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
               <img src="../imagenes/21.png" width="22" height="22" class="icon"> Grados y Pasos
              </div>
              <div class="actions">
                <a class="btn btn-sm blue"  onclick="javascript: window.location='gradospasos_agregar.php?cedula=<?php echo $cedula; ?>'">
                  <i class="fa fa-plus"></i>
                  Agregar
                </a>
                <a class="btn btn-sm blue"  onclick="javascript: window.location='menu_configuracion.php'">
                  <i class="fa fa-arrow-left"></i> Regresar
                </a>
              </div>
            </div>
            <div class="portlet-body">
              <form action="" method="post" name="gradospasos_list" id="gradospasos_list">
              <table class="table table-striped table-bordered table-hover" id="table_datatable">
              <thead>
              <tr>
                <th>Grado</th>
                <th>Periodo</th>
                <th>P1</th>
                <th>P2</th>
                <th>P3</th>
                <th>P4</th>
                <th>P5</th>
                <th>P6</th>
                <th>P7</th>
                <th>P8</th>
                <th>P9</th>
                <th>P10</th>
                <th>P11</th>
                <th>P12</th>
                <th>P13</th>
                <th>P14</th>
                <th>P15</th>
                <th>ajuste</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
              </tr>
              </thead>
              <tbody>
              <?php
                while( $fila=fetch_array($resultado) ) // $fila=fetch_array($res)
                {  
                  $codigo=$fila['grado'];               
                ?>
                  <tr class="odd gradeX">
                    <td><?php echo $fila['grado']; ?></td>
                    <td><?php echo $fila['periodo']; ?></td>
                    <td><?php echo $fila['p1']; ?></td>
                    <td><?php echo $fila['p2']; ?></td>
                    <td><?php echo $fila['p3']; ?></td>
                    <td><?php echo $fila['p4']; ?></td>
                    <td><?php echo $fila['p5']; ?></td>
                    <td><?php echo $fila['p6']; ?></td>
                    <td><?php echo $fila['p7']; ?></td>
                    <td><?php echo $fila['p8']; ?></td>
                    <td><?php echo $fila['p9']; ?></td>
                    <td><?php echo $fila['p10']; ?></td>
                    <td><?php echo $fila['p11']; ?></td>
                    <td><?php echo $fila['p12']; ?></td>
                    <td><?php echo $fila['p13']; ?></td>
                    <td><?php echo $fila['p14']; ?></td>
                    <td><?php echo $fila['p15']; ?></td>
                    <td><?php echo $fila['ajuste']; ?></td>

                    <td style="text-align: center">
                        <a href="gradospasos_agregar.php?grado=<?php echo $codigo; ?>" title="Editar"><img src="../../includes/imagenes/icons/pencil.png" alt="Editar" width="16" height="16"></a>
                    </td>

                    <td style="text-align: center">
                        <a href="javascript:confirmar2('<?php echo $codigo; ?>')" title="Eliminar"><img src="../../includes/imagenes/icons/delete.png" alt="Eliminar" width="16" height="16"></a>
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
                    { 'bSortable': false, 'aTargets': [16] },
                    { "bSearchable": false, "aTargets": [16] },
                    { "sWidth": "8%", "aTargets": [16] },
                    { 'bSortable': false, 'aTargets': [17] },
                    { "bSearchable": false, "aTargets": [17] },
                    { "sWidth": "8%", "aTargets": [17] },
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