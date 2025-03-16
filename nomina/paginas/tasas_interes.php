<?php 
require_once '../../generalp.config.inc.php';
session_start();
ob_start();

//error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require_once '../lib/common.php';
include ("func_bd.php");
//$conexion=conexion();
$ano = $_POST['ano']; 
$mes = $_POST['mes']; 
$op  = $_POST['op'];

if ($op==3) //Se presiono el boton de Eliminar
{ 
  $query  = "DELETE FROM nomtasas_interes WHERE anio=$ano AND mes=$mes";     
  $result = sql_ejecutar($query);
  activar_pagina("tasas_interes.php");       
}     
  $strsql = "SELECT * FROM nomtasas_interes ORDER BY anio DESC,mes";
  $result = sql_ejecutar($strsql);       
?>
<?php include("../header4.php"); // <html><head></head><body> ?>
<link href="../../includes/assets/css/custom-datatables.css" rel="stylesheet" type="text/css"/>
<script type="text/javascript">
function enviar(op,ano,mes){
  
  if (op==1){   // Opcion de Agregar
    //document.frmAgregar.registro_id.value=id;
    document.frmPrincipal.op.value=op;
    document.frmPrincipal.action="ag_tasas_interes.php";
    document.frmPrincipal.submit(); 
  }
  if (op==2){   // Opcion de Modificar
    //alert($op);   
    
    document.frmPrincipal.ano.value=ano;    
    document.frmPrincipal.mes.value=mes;    
    document.frmPrincipal.op.value=op;
    document.frmPrincipal.action="ag_tasas_interes.php";
    document.frmPrincipal.submit();   
  }
  if (op==3){   // Opcion de Eliminar
    if (confirm("\u00BFEst\u00E1 seguro que desea eliminar el registro?"))
    {         
      document.frmPrincipal.ano.value=ano;    
      document.frmPrincipal.mes.value=mes;    
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
               Tasas de Inter&eacute;s
              </div>
              <div class="actions">
                <a class="btn btn-sm blue"  onclick="javascript: window.location='ag_tasas_interes.php'">
                  <i class="fa fa-plus"></i>
                  Agregar
                </a>
                <a class="btn btn-sm blue"  onclick="javascript: window.location='submenu_bancos.php'">
                  <i class="fa fa-arrow-left"></i> Regresar
                </a>
              </div>
            </div>
            <div class="portlet-body">
              <form action="" method="post" name="frmPrincipal" id="frmPrincipal">
              <table class="table table-striped table-bordered table-hover" id="table_datatable">
              <thead>
              <tr>
                <th>A&ntilde;o</th>
                <th>Mes</th>
                <th>Tasa</th>
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
                    <td><?php echo $fila['anio']; ?></td>
                    <td>
                              <?php 
                                  if ($fila[mes]==1)
                                  {echo "Enero";}
                                  else if ($fila[mes]==2)
                                  {echo "Febrero";}
                                  else if ($fila[mes]==3)
                                  {echo "Marzo";}
                                  else if ($fila[mes]==4)
                                  {echo "Abril";}
                                  else if ($fila[mes]==5)
                                  {echo "Mayo";}
                                  else if ($fila[mes]==6)
                                  {echo "Junio";}
                                  else if ($fila[mes]==7)
                                  {echo "Julio";}     
                                  else if ($fila[mes]==8)     
                                  {echo "Agosto";}
                                  else if ($fila[mes]==9)
                                  {echo "Septiembre";}
                                  else if ($fila[mes]==10)
                                  {echo "Octubre";}
                                  else if ($fila[mes]==11)
                                  {echo "Noviembre";}
                                  else if ($fila[mes]==12)
                                  {echo "Diciembre";}
                              ?>
                    </td>
                    <td><?php echo $fila['tasa']; ?></td>
                    <td style="text-align: center">
                        <a href="javascript:enviar(<?php echo(2); ?>,<?php echo($fila['anio']); ?>,<?php echo($fila['mes']); ?>);" title="Editar"><img src="../../includes/imagenes/icons/pencil.png" alt="Editar" width="16" height="16"></a>
                    </td>
                    <td style="text-align: center">
                        <a href="javascript:enviar(<?php echo(3); ?>,<?php echo($fila['anio']); ?>,<?php echo($fila['mes']); ?>);" title="Eliminar"><img src="../../includes/imagenes/icons/delete.png" alt="Eliminar" width="16" height="16"></a>
                    </td>
                  </tr>
                  <?php                 
                }
              ?>
              </tbody>
              </table>
                  <input name="ano" type="hidden" value="">
                  <input name="mes" type="hidden" value="">
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
                    { 'bSortable': false, 'aTargets': [3] },
                    { "bSearchable": false, "aTargets": [3] },
                    { "sWidth": "8%", "aTargets": [3] },
                    { 'bSortable': false, 'aTargets': [4] },
                    { "bSearchable": false, "aTargets": [4] },
                    { "sWidth": "8%", "aTargets": [4] },
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