<?php 
require_once '../lib/common.php';
include ("func_bd.php");

$conexion= new bd($_SESSION['bd']);

$registro_id = (isset($_POST['registro_id'])) ? $_POST['registro_id'] : ''; 
$op = (isset($_POST['op'])) ? $_POST['op'] : '';

if ($op==3) //Se presiono el boton de Eliminar
{ 
  $query="DELETE FROM nomtipos_empresa WHERE codigo=$registro_id"; 
    
  $result=sql_ejecutar($query);
  activar_pagina("tipos_empresa.php");   
}   

$sql = "SELECT codigo, descripcion FROM nomtipos_empresa";
$res = $conexion->query($sql, "utf8");
?>
<?php include("../header4.php"); ?>
<link href="../../includes/assets/css/custom-datatables.css" rel="stylesheet" type="text/css"/>
<script type="text/javascript">
function enviar(op,id)
{
  if (op==2)
  {   
    document.frmPrincipal.action="ag_tiposempresa.php?id="+id;
    document.frmPrincipal.submit();   
  }
  if (op==3)
  {   
    if (confirm("\u00BFEst\u00E1 seguro que desea eliminar el registro ?"))
    {         
      document.frmPrincipal.registro_id.value=id;
      document.frmPrincipal.op.value=op;
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
                Tipos de Empresa
              </div>
              <div class="actions">
                <a class="btn btn-sm blue"  onclick="javascript: window.location='ag_tiposempresa.php'">
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
                  <th>C&oacute;digo</th>
                  <th>Descripci&oacute;n</th>
                  <th>&nbsp;</th>
                  <th>&nbsp;</th>
                </tr>
                </thead>
                <tbody>
                <?php
                  while( $fila = $res->fetch_assoc() )
                  {                 
                  ?>
                    <tr class="odd gradeX">
                      <td><?php echo $fila['codigo']; ?></td>
                      <td><?php echo $fila['descripcion']; ?></td>
                      <td style="text-align: center">
                          <a href="javascript:enviar(<?php echo(2); ?>,<?php echo($fila['codigo']); ?>);" title="Editar">
                          <img src="../../includes/imagenes/icons/pencil.png" alt="Editar" width="16" height="16"></a>
                      </td>
                      <td style="text-align: center">
                          <a href="javascript:enviar(<?php echo(3); ?>,<?php echo($fila['codigo']); ?>);" title="Eliminar">
                          <img src="../../includes/imagenes/icons/delete.png" alt="Eliminar" width="16" height="16"></a>
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
          "sPaginationType": "bootstrap_extended", 
            "oLanguage": {
              "sSearch": "<img src='../../includes/imagenes/icons/magnifier.png' width='16' height='16' > Buscar:",
                "sLengthMenu": "Mostrar _MENU_",
                "sInfoEmpty": "",
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
                { 'bSortable': false, 'aTargets': [2, 3] },
                { "bSearchable": false, "aTargets": [2, 3] },
                { "sWidth": "8%", "aTargets": [2, 3] },
                { "sClass": "text-center", "aTargets": [0, 1] }
            ],
            "fnDrawCallback": function() {
                  $('#table_datatable_filter input').attr("placeholder", "Escriba frase para buscar");
            }
        });

        $('#table_datatable_wrapper .dataTables_filter input').addClass("form-control input-medium input-inline"); 
        $('#table_datatable_wrapper .dataTables_length select').addClass("form-control input-xsmall"); 
});
</script>
</body>
</html>