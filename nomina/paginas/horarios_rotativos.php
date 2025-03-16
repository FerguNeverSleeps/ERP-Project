<?php 
require_once '../../generalp.config.inc.php';
session_start();
ob_start();

require_once '../lib/common.php';
include ("func_bd.php");
//error_reporting(E_ALL ^ E_DEPRECATED);
$conexion=conexion();

$id=(isset($_POST['registro_id'])) ? $_POST['registro_id'] : '' ;
$op=(isset($_POST['op'])) ? $_POST['op'] : '';

if ($op==3) //Se presiono el boton de Eliminar
{ 
  // Primero se deben eliminar los registros asociados en la tabla nomturnos_rotacion_detalle
  $query  = "DELETE FROM nomturnos_rotacion_detalle WHERE codigo_rotacion='{$id}'";
  $result = sql_ejecutar($query);

  $query  = "DELETE FROM nomturnos_rotacion WHERE codigo='{$id}'";      
  $result = sql_ejecutar($query);
  activar_pagina("horarios_rotativos.php");   
} 

$strsql = "SELECT ntr.codigo, ntr.descripcion, ntr.frecuencia, ntr.inicio,
                  ntt.descripcion as turno, ntt.rotativo
           FROM   nomturnos_rotacion ntr, nomturnos_tipo ntt
           WHERE  ntr.turnotipo_id=ntt.turnotipo_id";
$result = sql_ejecutar_utf8($strsql);      
?>
<?php include("../header4.php"); // <html><head></head><body> ?>
<link href="../../includes/assets/css/custom-datatables.css" rel="stylesheet" type="text/css"/>
<style type="text/css">
/*
  .portlet > .portlet-title > .actions > .btn.btn-sm {
    margin-top: -9px !important;
  }
  */
</style>
<script type="text/javascript">
function enviar(op,id)
{
  if (op==2)
  {   
    document.frmPrincipal.registro_id.value=id;    
    document.frmPrincipal.op.value=op;
    document.frmPrincipal.action="ag_horario_rotativo.php";
    document.frmPrincipal.submit();        
  }
  if (op==3)
  {   // Opcion de Eliminar
    if (confirm("\u00BFEst\u00E1 seguro que desea eliminar el registro?"))
    {         
      document.frmPrincipal.registro_id.value=id;
      document.frmPrincipal.op.value=op;
      document.frmPrincipal.action="horarios_rotativos.php";
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
               Horarios Rotativos
              </div>
              <div class="actions">
                <a class="btn btn-sm blue"  onclick="javascript: window.location='ejecutar_rotacion_manual.php'">
                  <i class="fa fa-gears"></i> Rotación Manual
                </a>
                <a class="btn btn-sm blue"  onclick="javascript: window.location='ag_horario_rotativo.php'">
                  <i class="fa fa-plus"></i> Agregar
                </a>
              </div>
            </div>
            <div class="portlet-body">
              <form action="" method="post" name="frmPrincipal" id="frmPrincipal">
              <table class="table table-striped table-bordered table-hover" id="table_datatable">
              <thead>
              <tr>
                <th class="text-center">C&oacute;digo</th>
                <th class="text-center">Descripci&oacute;n</th>
                <th class="text-center">Frecuencia</th>
                <th class="text-center">Inicio</th>
                <th class="text-center">Tipo de Turno</th>
                <th class="text-center">&nbsp;</th>
                <th class="text-center">&nbsp;</th>
              </tr>
              </thead>
              <tbody>
              <?php
                while( $fila = mysqli_fetch_array($result) ) // $fila=fetch_array($res)
                {  
                    $frecuencia = $fila['frecuencia']; 
                    $inicio     = $fila['inicio'];

                    if( $frecuencia == 'Semanal' )
                    {
                        $semana = array('Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo');
                        $inicio = ($inicio<1 || $inicio>7) ? 1 : $inicio;

                        $inicio = $semana[$inicio-1];
                    }
                    else if ($frecuencia == 'Mensual')
                    {
                          $meses = array('1' =>'Enero',    '2' =>'Febrero',   '3' =>'Marzo',
                                         '4' =>'Abril',    '5' =>'Mayo',      '6' =>'Junio',
                                         '7' =>'Julio',    '8' =>'Agosto',    '9' =>'Septiembre',
                                         '10'=>'Octubre',  '11'=>'Noviembre', '12'=>'Diciembre');

                          $inicio = ($inicio<1 || $inicio>12) ? 1 : $inicio;

                          $inicio = $meses["$inicio"];
                    }
                ?>
                  <tr class="odd gradeX">
                    <td class="text-center" style="vertical-align:middle;"><?php echo $fila['codigo']; ?></td>
                    <td class="text-center" style="vertical-align:middle;"><?php echo $fila['descripcion']; ?></td>
                    <td class="text-center" style="vertical-align:middle;"><?php echo $frecuencia; ?></td>
                    <td class="text-center" style="vertical-align:middle;"><?php echo $inicio; ?></td>
                    <td class="text-center" style="vertical-align:middle;"><?php echo $fila['turno']; ?></td>
                    <td class="text-center" style="vertical-align:middle;">
                        <a href="javascript:enviar(<?php echo(2); ?>,'<?php echo($fila['codigo']); ?>');" title="Editar"><img src="../../includes/imagenes/icons/pencil.png" width="16" height="16"></a>
                    </td>
                    <td class="text-center" style="vertical-align:middle;">
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
        //"bAutoWidth": false,
        //"aaSorting": [], 
        "aaSortingFixed": [[0,'asc']],
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
            { 'bSortable': false, 'aTargets': [ 5 ] },
            { "bSearchable": false, "aTargets": [ 5 ] },
            { "sWidth": "8%", "aTargets": [ 5 ] },
            { 'bSortable': false, 'aTargets': [ 6 ] },
            { "bSearchable": false, "aTargets": [ 6 ] },
            { "sWidth": "8%", "aTargets": [ 6 ] },
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