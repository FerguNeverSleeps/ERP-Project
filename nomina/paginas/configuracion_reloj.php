<?php
require_once 'control_asistencia/db.php';
include ("func_bd.php");

$registro_id=(isset($_POST['registro_id'])) ? $_POST['registro_id'] : ''; 
$op=(isset($_POST['op'])) ? $_POST['op'] : '';

if ($op==3) 
{ 
  try {
      $conexion->delete('caa_parametros',    array('configuracion' => $registro_id));
      $conexion->delete('caa_configuracion', array('codigo' => $registro_id));

      activar_pagina("configuracion_reloj.php");
  } 
  catch (Exception $e) {
      echo 'Excepción capturada: ' . $e->getMessage();
  }
}  

$qbr = $conexion->createQueryBuilder()
			    ->select('c.codigo', 'c.descripcion', 'c.formato', 'c.delimitador', 'ct.nombre as tipo_reloj')
			    ->from('caa_configuracion', 'c')
          ->innerJoin('c', 'caa_tiporeloj', 'ct', 'c.tipo_reloj = ct.codigo');

$res = $qbr->execute();   
?>
<?php include("../header4.php"); ?>
<link href="../../includes/assets/css/custom-datatables.css" rel="stylesheet" type="text/css"/>
<style type="text/css">
  .portlet > .portlet-title > .actions > .btn.btn-sm {
    margin-top: -9px !important;
  }
</style>
<script type="text/javascript">
function enviar(op,id){
  if (op==2){   // Opcion de Modificar
    //alert($op);   
    document.frmPrincipal.registro_id.value=id;   
    document.frmPrincipal.op.value=op;
    document.frmPrincipal.action="ag_configuracion_reloj.php";
    document.frmPrincipal.submit();   
  }
  if (op==3){   // Opcion de Eliminar
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
               Formatos de Reloj
              </div>
              <div class="actions">
                <a class="btn btn-sm blue"  onclick="javascript: window.location='ag_configuracion_reloj.php'">
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
                while( $fila = $res->fetch() )
                {                 
                ?>
                  <tr class="odd gradeX">
                    <td><?php echo $fila['codigo']; ?></td>
                    <td><?php echo $fila['descripcion']; ?></td>
                    <td><?php echo $fila['formato']; ?></td>
                    <td><?php echo $fila['delimitador']; ?></td>
                    <td><?php echo $fila['tipo_reloj']; ?></td>
                    <td style="text-align: center">
                        <a href="javascript:enviar(<?php echo(2); ?>,'<?php echo($fila['codigo']); ?>');" title="Editar"><img src="../../includes/imagenes/icons/pencil.png" width="16" height="16"></a>
                    </td>
                    <td style="text-align: center">
                        <a href="javascript:enviar(<?php echo(3); ?>,'<?php echo($fila['codigo']); ?>');" title="Eliminar"><img src="../../includes/imagenes/icons/delete.png" alt="Eliminar" width="16" height="16"></a>
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
                "sPrevious": "P&aacute;gina Anterior",
                "sNext": "P&aacute;gina Siguiente",
                "sPage": "P&aacute;gina",
                "sPageOf": "de",
            }
        },
        "aLengthMenu": [ 
            [5, 10, 25, 50,  -1],
            [5, 10, 25, 50, "Todos"]
        ],                
        "aoColumnDefs": [
            { 'bSortable': false, 'aTargets': [5, 6] },
            { "bSearchable": false, "aTargets": [5, 6] },
            { "sWidth": "8%", "aTargets": [5, 6] },
            { "sClass": "text-center", "aTargets": [ 0, 2, 3, 4 ] }

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