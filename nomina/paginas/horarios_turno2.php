<?php 
require_once '../../generalp.config.inc.php';
if (!isset($_SESSION)) {
    session_start();
    ob_start();
}

require_once '../lib/common.php';
include ("func_bd.php");
//error_reporting(E_ALL ^ E_DEPRECATED);
$conexion=conexion();

$id=(isset($_POST['registro_id'])) ? $_POST['registro_id'] 
                                   : ( isset($_GET['id']) ? $_GET['id'] : '') ;  // id del turno
$op=(isset($_POST['op'])) ? $_POST['op'] : '';

if ($op==3) //Se presiono el boton de Eliminar
{ 
  $horario_id = $_POST['horario_id'];

  $query  = "DELETE FROM nomturnos_horarios WHERE turnohorario_id='{$horario_id}'";      
  $result = sql_ejecutar($query);
  activar_pagina("horarios_turno2.php?id=".$id);   
} 

$strsql = "SELECT nt.descripcion FROM nomturnos nt WHERE nt.turno_id='{$id}'";
$result = sql_ejecutar_utf8($strsql);

if( $fila = mysqli_fetch_array($result) )
{
  $turno = $fila['descripcion'];
}

$strsql = "SELECT nth.*, 
                  date_format(hora_desde,'%h:%i:%s %p') as hora_inicio,
                  date_format(hora_hasta,'%h:%i:%s %p') as hora_fin
           FROM   nomturnos_horarios nth 
           WHERE  turno_id='{$id}'";
$result = sql_ejecutar_utf8($strsql);      
?>
<?php include("../header4.php"); // <html><head></head><body> ?>
<link href="../../includes/assets/css/custom-datatables.css" rel="stylesheet" type="text/css"/>
<style type="text/css">
  .text-bold{
    font-weight: bold;
  }

  .text-danger{
    color: #e02222;
  }
</style>
<script type="text/javascript">
function enviar(op, horario_id, turno_id){
  if (op==1){   // Opcion de Agregar
    document.frmPrincipal.op.value=op;
    document.frmPrincipal.action="ag_horario_turno.php";
    document.frmPrincipal.submit(); 
  }
  if (op==2){   // Opcion de Modificar
    //alert($op);   
    document.frmPrincipal.horario_id.value = horario_id;   
    document.frmPrincipal.op.value=op;
    document.frmPrincipal.action="ag_horario_turno.php";
    document.frmPrincipal.submit();   
  }
  if (op==3){   // Opcion de Eliminar
    if (confirm("\u00BFEst\u00E1 seguro que desea eliminar el registro ?"))
    {         
      document.frmPrincipal.horario_id.value  = horario_id;
      document.frmPrincipal.registro_id.value = turno_id;
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
               Horarios del Turno <?php echo str_replace('Turno', '', $turno); ?>
              </div>
              <div class="actions">
                <a class="btn btn-sm blue"  onclick="javascript: window.location='ag_horario_turno.php?turno_id=<?php echo $id; ?>'">
                  <i class="fa fa-plus"></i>
                  Agregar
                </a>
                <a class="btn btn-sm blue"  onclick="javascript: window.location='turnos.php'">
                  <i class="fa fa-arrow-left"></i> Regresar
                </a>
              </div>
            </div>
            <div class="portlet-body">
              <form action="" method="post" name="frmPrincipal" id="frmPrincipal">
              <table class="table table-striped table-bordered table-hover" id="table_datatable">
              <thead>
              <tr>
                <th class="text-center">D&iacute;a</th>
                <th class="text-center">Hora Inicio</th>
                <th class="text-center">Hora Fin</th>
                <th class="text-center">Libre</th>
                <th class="text-center">&nbsp;</th>
                <th class="text-center">&nbsp;</th>
              </tr>
              </thead>
              <tbody>
              <?php
                while( $fila = mysqli_fetch_array($result) ) // $fila=fetch_array($res)
                {   $_class = ($fila['dialibre']==0) ? 'text-primary' : 'text-danger';
                ?>
                  <tr class="odd gradeX">
                    <td class="text-center <?php echo $_class; ?>" style="vertical-align:middle;">
                        <?php echo ($fila['dia1']==1) ? "Lunes"     . "<br>"  : ''; ?>
                        <?php echo ($fila['dia2']==1) ? "Martes"    . "<br>"  : ''; ?>
                        <?php echo ($fila['dia3']==1) ? "Miércoles" . "<br>"  : ''; ?>
                        <?php echo ($fila['dia4']==1) ? "Jueves"    . "<br>"  : ''; ?>
                        <?php echo ($fila['dia5']==1) ? "Viernes"   . "<br>"  : ''; ?>
                        <?php echo ($fila['dia6']==1) ? "Sábado"    . "<br>"  : ''; ?>
                        <?php echo ($fila['dia7']==1) ? "Domingo"   . "<br>"  : ''; ?>                       
                    </td>
                    <td class="text-center" style="vertical-align:middle;"><?php echo $fila['hora_inicio']; ?></td>
                    <td class="text-center" style="vertical-align:middle;"><?php echo $fila['hora_fin']; ?></td>
                    <td class="text-center <?php echo $_class; ?>" style="vertical-align:middle;"><?php echo ($fila['dialibre']==0) ? 'No' : 'Si'; ?></td>
                    <td class="text-center" style="vertical-align:middle;">
                        <a href="javascript:enviar(<?php echo(2); ?>,'<?php echo($fila['turnohorario_id']); ?>','<?php echo $id; ?>');" title="Editar"><img src="../../includes/imagenes/icons/pencil.png" width="16" height="16"></a>
                    </td>
                    <td class="text-center" style="vertical-align:middle;">
                        <a href="javascript:enviar(<?php echo(3); ?>,'<?php echo($fila['turnohorario_id']); ?>','<?php echo $id; ?>');" title="Eliminar"><img src="../../includes/imagenes/icons/delete.png" alt="Eliminar" width="16" height="16"></a>
                    </td>
                  </tr>
                  <?php                 
                }
              ?>
              </tbody>
              </table>
                  <input name="registro_id" type="hidden" value="">
                  <input name="horario_id"  type="hidden" value="">
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
        //"bAutoWidth": false,
        "aaSorting": [], //"aaSortingFixed": [[0,'desc']],
        "iDisplayLength": 10,
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
            { 'bSortable': false, 'aTargets': [ 4 ] },
            { "bSearchable": false, "aTargets": [ 4 ] },
            { "sWidth": "8%", "aTargets": [ 4 ] },
            { 'bSortable': false, 'aTargets': [ 5 ] },
            { "bSearchable": false, "aTargets": [ 5 ] },
            { "sWidth": "8%", "aTargets": [ 5 ] },
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