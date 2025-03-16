<?php 
require_once '../../generalp.config.inc.php';
session_start();
ob_start();

//error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require_once '../lib/common.php';
include ("func_bd.php");
$conexion    = conexion();

$registro_id = $_POST['registro_id']; 
$op          = $_POST['op'];

if ($op==3) //Se presiono el boton de Eliminar
{ 
  $query="DELETE FROM implemento WHERE id_implemento='$registro_id'";      
  $result=sql_ejecutar($query);
  activar_pagina("implementos_list.php");   
}     
$strsql = "SELECT * "
        . " FROM implemento "
        . " ORDER BY nombre ASC";
$result = sql_ejecutar($strsql);     
?>
<?php include("../header4.php"); // <html><head></head><body> ?>
<link href="../../includes/assets/css/custom-datatables.css" rel="stylesheet" type="text/css"/>
<!--
<script type="text/javascript" src="ewp.js"></script>
-->
<script type="text/javascript">
function enviar(op,id){
  
  if (op==1){   // Opcion de Agregar
    //document.frmAgregar.registro_id.value=id;
    document.frmPrincipal.op.value=op;
    document.frmPrincipal.action="implementos_add.php";
    document.frmPrincipal.submit(); 
  }
  if (op==2){   // Opcion de Modificar
    //alert($op);   
    document.frmPrincipal.registro_id.value=id;   
    document.frmPrincipal.op.value=op;
    document.frmPrincipal.action="implementos_edit.php";
    document.frmPrincipal.submit();   
  }
  if (op==3){   // Opcion de Eliminar
    if (confirm("\u00BFEst\u00E1 seguro que desea eliminar el registro ?"))
    {         
      document.frmPrincipal.registro_id.value=id;
      document.frmPrincipal.op.value=op;
      document.frmPrincipal.action="implementos_delete.php";
      document.frmPrincipal.submit();
    }   
  }
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
               Tipos de Impleentos
              </div>
              <div class="actions">
                <a class="btn btn-sm blue"  onclick="javascript: window.location='implementos_add.php'">
                  <i class="fa fa-plus"></i>
                  Agregar
                </a>
                <a class="btn btn-sm blue"  onclick="javascript: window.location='submenu_tipos.php?modulo=71'">
                  <i class="fa fa-arrow-left"></i> Regresar
                </a>
              </div>
            </div>
            <div class="portlet-body">
              <form action="" method="post" name="frmPrincipal" id="frmPrincipal">
              <table class="table table-striped table-bordered table-hover" id="table_datatable">
              <thead>
              <tr>
                <th>ID.</th>
                <th>Nombre</th>
                <th>Descripci&oacute;n</th>
                <th>Vencimiento (Dias)</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
              </tr>
              </thead>
              <tbody>
              <?php
                while( $fila = mysqli_fetch_array($result) ) // $fila=fetch_array($res)
                {                 
                ?>
                  <tr class="odd gradeX">
                    <td><?php echo $fila['id_implemento']; ?></td>
                    <td><?php echo $fila['nombre']; ?></td>
                    <td><?php echo $fila['descripcion']; ?></td>
                    <td><?php echo $fila['vencimiento']; ?></td>
                    <td style="text-align: center">
                        <a href="javascript:enviar(<?php echo(2); ?>,'<?php echo($fila['id_implemento']); ?>');" title="Editar"><img src="../../includes/imagenes/icons/pencil.png" width="16" height="16"></a>
                    </td>
                    <td style="text-align: center">
                        <a href="javascript:enviar(<?php echo(3); ?>,'<?php echo($fila['id_implemento']); ?>');" title="Eliminar"><img src="../../includes/imagenes/icons/delete.png" alt="Eliminar" width="16" height="16"></a>
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
      "iDisplayLength": 25,
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
            { 'bSortable': false, 'aTargets': [4,5] },
            { "bSearchable": false, "aTargets": [4,5] },
            { "sWidth": "15%", "aTargets": [1] },
            { "sWidth": "8%", "aTargets": [4,5] }
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