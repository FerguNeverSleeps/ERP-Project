<?php 
require_once '../../generalp.config.inc.php';
session_start();
ob_start();

// error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require_once '../lib/common.php';
// include ("func_bd.php") ;
$conexion=conexion();

$sql = "SELECT * FROM nomempresa";
$res = query($sql, $conexion);
?>
<?php include("../header4.php"); // <html><head></head><body> ?>
<link href="../../includes/assets/css/custom-datatables.css" rel="stylesheet" type="text/css"/>
<style type="text/css">
  .portlet > .portlet-title > .actions > .btn.btn-sm {
    margin-top: -9px !important;
  }
</style>
<script type="text/javascript">
function enviar(id){
  document.frmPrincipal.nivel.value=id;
  document.frmPrincipal.action="subniveles.php";
  document.frmPrincipal.submit();   
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
                <!-- <i class="fa fa-globe"></i> -->
                Niveles Funcionales
              </div>
              
            </div>
            <div class="portlet-body">
              <div class="table-toolbar" style="display: none">
                <div class="btn-group">&nbsp;
                  <!--<button id="sample_editable_1_new" class="btn green">
                  Add New <i class="fa fa-plus"></i>
                  </button>-->
                </div>
                <div class="btn-group pull-right">
                  <button class="btn btn-sm blue" onclick="javascript: window.location='usuarios_add.php'">
                  <i class="fa fa-plus"></i>
                  <!-- <img src="../../includes/imagenes/icons/add.png"  width="16" height="16">--> Agregar
                  </button>
                  <!--
                  <button class="btn dropdown-toggle" data-toggle="dropdown">Tools <i class="fa fa-angle-down"></i>
                  </button>
                  <ul class="dropdown-menu pull-right">
                    <li>
                      <a href="#">
                         Print
                      </a>
                    </li>
                    <li>
                      <a href="#">
                         Save as PDF
                      </a>
                    </li>
                    <li>
                      <a href="#">
                         Export to Excel
                      </a>
                    </li>
                  </ul>
                  -->
                </div>
              </div>
              <form action="" method="post" name="frmPrincipal" id="frmPrincipal">
              <table class="table table-striped table-bordered table-hover" id="table_datatable">
              <thead>
              <tr>
                <th>Descripci&oacute;n del Nivel</th>
                <th>&nbsp;</th>
              </tr>
              </thead>
              <tbody>
              <?php

                $fila = fetch_array($res);

                for( $i = 1; $i <= 7; $i++)
                {
                      if($fila["nivel$i"])
                      {
                      ?>
                          <tr class="odd gradeX">
                            <td><?php echo $fila["nomniv$i"]; ?></td>
                            <td style="text-align: center">
                              <a href="javascript:enviar(<?php echo $i; ?>);" title="Agregar Datos">
                              <img src= "../../includes/imagenes/icons/add.png" title="Agregar Datos" width="16" height="16" 
                                   border="0" align="absmiddle" >
                              </a>    
                            </td>
                          </tr>
                      <?php
                      }
                }
              ?>
              </tbody>
              </table>
                 <input name="nivel" type="hidden" value="">
                 <!-- <input name="op" type="hidden" value=""> -->
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
              //"oSearch": {"sSearch": "Escriba frase para buscar"},
              "iDisplayLength": 10,
                //"sPaginationType": "bootstrap",
              "bPaginate": false,
              //"sPaginationType": "full_numbers",
                "oLanguage": {
                  "sSearch": "<img src='../../includes/imagenes/icons/magnifier.png' width='16' height='16' > Buscar:",
                    "sLengthMenu": "Mostrar _MENU_",
                    //"sInfo": "Showing page _PAGE_ of _PAGES_", // Mostrando 1 to 5 de 18 entradas
                    //"sInfoEmpty": "No hay registros para mostrar",
                    "sInfoEmpty": "",
                    "sInfo": "",
                    //"sInfo":"Total _TOTAL_ registros",
                    "sInfoFiltered": "",
                  "sEmptyTable":  "No hay datos disponibles",//"No data available in table",
                    "sZeroRecords": "No se encontraron registros",//"No matching records found",
                    "oPaginate": {
                        "sPrevious": "P&aacute;gina Anterior",//"Prev",
                        "sNext": "P&aacute;gina Siguiente",//"Next",
                        "sPage": "P&aacute;gina",//"Page",
                        "sPageOf": "de",//"of"
                    }
                },   
                "aoColumnDefs": [
                    { 'bSortable': false, 'aTargets': [1] },
                    { "bSearchable": false, "aTargets": [1] },
                    { "sWidth": "8%", "aTargets": [1] }
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