<?php 
require_once '../../generalp.config.inc.php';
session_start();
ob_start();

require_once '../lib/common.php';
error_reporting(E_ALL ^ E_DEPRECATED);
include ("func_bd.php");
$conexion    = new bd( $_SESSION['bd']);


$id_empleado = (isset($_POST['id_empleado'])) ? $_POST['id_empleado']: ''; 
$fecha_inicio = (isset($_POST['fecha_inicio'])) ? fecha_sql($_POST['fecha_inicio']): ''; 
$fecha_fin = (isset($_POST['fecha_fin'])) ? fecha_sql($_POST['fecha_fin']): ''; 
$op          = (isset($_POST['op'])) ? $_POST['op']:                    '';


if (isset($_POST['id_empleado']) && isset($_POST['fecha_inicio']) && isset($_POST['fecha_fin'])) //Se presiono el boton de Eliminar
{ 
  $strsql= "SELECT *, ri.nombre as nombre
          FROM  reloj_marcaciones rm          
          LEFT JOIN nompersonal pe ON (rm.id_empleado = pe.personal_id)
          LEFT JOIN reloj_info ri ON (rm.dispositivo = ri.id_dispositivo)
          WHERE id_empleado =  '$id_empleado' AND
          fecha BETWEEN '$fecha_inicio' AND '$fecha_fin'";
  //echo $strsql;
  //$result = $conexion->query($query);
  //activar_pagina("marcaciones_list.php");   
  $result =$conexion->query($strsql);
  
  
} 
//else
//{
//$strsql= "SELECT *, ri.nombre as nombre
//          FROM  reloj_marcaciones rm
//          LEFT JOIN nompersonal pe ON (rm.id_empleado = pe.personal_id)
//          LEFT JOIN reloj_info ri ON (rm.dispositivo = ri.id_dispositivo)";
//}
 //sql_ejecutar($strsql);     
?>
<?php include("../header4.php"); // <html><head></head><body> ?>
<link href="../../includes/assets/css/custom-datatables.css" rel="stylesheet" type="text/css"/>
<!--
<script type="text/javascript" src="ewp.js"></script>
-->

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
               Relación de Marcaciones
              </div>
            </div>
           
            <div class="portlet-body">
                <form action="" method="post" name="frmPrincipal" id="frmPrincipal">
                    <div class="row">
                        <div class="col-md-10">                        
                            <div class="form-group">
                               <div class="row">
                                    <label class="col-md-2 control-label" for="txtcodigo">Colaborador</label>
                                    <div class="col-md-8">
                                        <div class="input-group">
                                           <span id="id_emp"></span>
                                        </div> 
                                    </div>
                               </div>
                                <br>
                                <div class="row" style="vertical-align: text-top;">
                                    <label class="col-md-2 control-label" for="txtcodigo">Fecha Inicio:</label>
                                    <div class="col-md-3">
                                        <div class="input-group date date-picker" data-provide="datepicker" data-date-format="dd/mm/yyyy">
                                            <input size="10" type="text" name="fecha_inicio" class="form-control" placeholder="(dd/mm/aaaa)" id="fecha_inicio" value="">
                                            <span class="input-group-btn">
                                                <button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
                                            </span>
                                        </div>
                                    </div>


                                    <label class="col-md-2 control-label" for="txtcodigo">Fecha Fin: </label>
                                    <div class="col-md-3">
                                        <div class="input-group date date-picker" data-provide="datepicker" data-date-format="dd/mm/yyyy">
                                            <input size="10" type="text" class="form-control" placeholder="(dd/mm/aaaa)" name="fecha_fin" id="fecha_fin" value="">
                                            <span class="input-group-btn">
                                                <button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
                                            </span>    
                                        </div>
                                    </div>
                                    <button type="submit" id="bt_filtra" class="btn btn-sm btn-default" onclick="imprimir_listado();">Buscar</button>
                                </div>
                            </div>  
                        </div>
                    </div>             
                    <table class="table table-striped table-bordered table-hover" id="table_datatable">
                    <thead>
                    <tr>
                      <th>ID Marcación</th>
                      <th>Cédula</th>
                      <th>Ficha</th>
                      <th>Nombre y Apellidos</th>
                      <th>Fecha</th>
                      <th>Hora</th>
                      <th>Dispositivo</th>
                      <th>Estatus</th>

                    </tr>
                    </thead>
                    <tbody>
                    <?php
                      while( $fila = mysqli_fetch_array($result) ) // $fila=fetch_array($res)
                      {                 
                      ?>
                        <tr class="odd gradeX">
                          <td><?php echo $fila['id']; ?></td>
                          <td><?php echo $fila['cedula']; ?></td>
                          <td><?php echo $fila['ficha']; ?></td>
                          <td><?php echo $fila['apenom']; ?></td>
                          <td><?php echo $fila['fecha']; ?></td>
                          <td><?php echo $fila['hora']; ?></td>
                          <td><?php echo $fila['nombre']; ?></td>
                          <td><?php echo $fila['estatus']; ?></td>
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
$(document).ready(function() 
{ 
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
            { 'bSortable': false, 'aTargets': [2] },
            { "bSearchable": false, "aTargets": [2] },
            { "sWidth": "8%", "aTargets": [2] },
            { 'bSortable': false, 'aTargets': [3] },
            { "bSearchable": false, "aTargets": [3] },
            { "sWidth": "8%", "aTargets": [3] },
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
   
    $.get("ajax/getEmpleadosList.php",function(res)
    {
        
        $("#id_emp").empty();
        $("#id_emp").append(res);
        $("#id_empleado").select2({
                                language: "es",              
                                 allowClear: true,
                            });
    });
    
    
                                    
});  

function imprimir_listado(){
    var fecha_inicio = $("#fecha_inicio").val();
    var fecha_fin = $("#fecha_fin").val();	
    var id_empleado = $("#id_empleado").val();
//    alert(fecha_fin);
//    alert(fecha_inicio);
   window.open("../../reportes/excel/excel_relacion_marcaciones.php?id_empleado="+id_empleado+"&fecha_fin="+fecha_fin+"&fecha_inicio="+fecha_inicio,'_blank');
    
  
    
}
</script>
</body>
</html>