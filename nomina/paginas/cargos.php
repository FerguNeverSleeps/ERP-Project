<?php 
require_once '../../generalp.config.inc.php';
session_start();
ob_start();

//error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require_once '../lib/common.php';
include ("func_bd.php");
$conexion=conexion();

$registro_id = (isset($_POST['registro_id'])) ? $_POST['registro_id'] : ''; 
$op = (isset($_POST['op'])) ? $_POST['op'] : '' ;

if ($op==3) // Se presiono el boton de Eliminar
{ 
    $query="delete from nomcargos where cod_car='$registro_id'";  
  
    $result=sql_ejecutar($query);
    activar_pagina("cargos.php");    
}     

$strsql= "SELECT * FROM nomcargos ORDER BY cod_car,des_car";
$result =sql_ejecutar($strsql); 
?>
<?php include("../header4.php"); // <html><head></head><body> ?>
<link href="../../includes/assets/css/custom-datatables.css" rel="stylesheet" type="text/css"/>
<script type="text/javascript">
function enviar(op,id)
{
  if (op==1)
  { // Opcion de Agregar
    document.frmAgregar.op.value=op;
    document.frmAgregar.action="ag_cargos.php";
    document.frmAgregar.submit(); 
  }
  if (op==2)
  { // Opcion de Modificar
    document.frmCargos.registro_id.value=id;    
    document.frmCargos.op.value=op;
    document.frmCargos.action="ag_cargos.php";
    document.frmCargos.submit();
        
  }
  if (op==3)
  { // Opcion de Eliminar
    if (confirm("\u00BFEst\u00E1 seguro que desea eliminar el registro ?"))
    {         
      document.frmCargos.registro_id.value=id;
      document.frmCargos.op.value=op;
      document.frmCargos.action="cargos.php";
        document.frmCargos.submit();
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
                Cargos
              </div>
              <div class="actions">
                <a class="btn btn-sm blue"  onclick="javascript: window.location='../../reportes/excel/excel_cargos.php'">
                  <img height="19px" width="19px" src="../../includes/imagenes/icons/logo_excel.png" alt="">
                  Exportar
                </a>

                <a class="btn btn-sm blue"  onclick="javascript: window.location='ag_cargos.php'">
                  <i class="fa fa-plus"></i>
                  Agregar
                </a>
                <!--
                <a class="btn btn-sm blue"  onclick="javascript: window.location='menu_configuracion.php'">
                  <i class="fa fa-arrow-left"></i> Regresar
                </a>-->
              </div>
            </div>
            <div class="portlet-body">
              <form action="" method="post" name="frmCargos" id="frmCargos">
              <table class="table table-striped table-bordered table-hover" id="table_datatable">
              <thead>
              <tr>
                <th>C&oacute;digo</th>
                <th>Descripci&oacute;n Corta</th>
                <th>Descripci&oacute;n</th>
                <th>Tipo</th>
                <th>Salario Base</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <!-- <th>&nbsp;</th> -->
              </tr>
              </thead>
              <tbody>
              <?php
                while($fila = mysqli_fetch_array($result))
                {                 
                ?>
                  <tr class="odd gradeX">
                    <td><?php echo $fila['cod_car']; ?></td>
                    <td><?php echo $fila['descrip_corto']; ?></td>
                    <td><?php echo utf8_encode($fila['des_car']); ?></td>
                    <td>
                        <?php 
                         if($fila['gremio']==0)
                              echo "Obrero";
                         else
                              echo "Empleado";
                        ?>
                    </td>
                    <td><?php echo $fila['sueldo']; ?></td>
                    <td style="text-align: center">
                      <a href="ag_cargos.php?edit&id=<?php echo $fila['cod_cargo'] ?>" title="Editar">
                        <img src="../../includes/imagenes/icons/pencil.png" width="16" height="16">
                      </a>
                    </td>
                    <td style="text-align: center">
                                  <a href="javascript:enviar(<?php echo(3); ?>,<?php echo "'".$fila['cod_car']."'"; ?>);" title="Eliminar">
                                  <img src="../../includes/imagenes/icons/delete.png" alt="Eliminar" width="16" height="16">
                                  </a>   
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
                    { 'bSortable': false, 'aTargets': [5,6] },
                    { "bSearchable": false, "aTargets": [5,6] },
                    { "sWidth": "8%", "aTargets": [5,6] },                   
                    { "sWidth": "10%", "aTargets": [1,3,4] }
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