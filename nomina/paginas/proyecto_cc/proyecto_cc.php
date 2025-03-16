<?php 
require_once '../../../generalp.config.inc.php';
session_start();
ob_start();

//error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require_once '../../lib/common.php';
include ("../func_bd.php");
$conexion    = conexion();

$cedula = $_GET['cedula'];
$registro_id = $_POST['registro_id']; 
$cwconcue = $_POST['cwconcue']; 
$op          = $_POST['op'];

if ($op==3) //Se presiono el boton de Eliminar
{ 
  $query="DELETE FROM proyecto_cc WHERE id='$registro_id'";      
  $result=sql_ejecutar($query);
  activar_pagina("proyectos_centro_costo.php");   
}     
// $strsql = "SELECT a.*, b.cod_dispositivo as codigo, b.nombre as nombre"
//         . " FROM proyectos as a "
//         . " LEFT JOIN reloj_info as b ON b.id_dispositivo=a.idDispositivo"
//         . " ORDER BY descripcionLarga ASC";
// $result = sql_ejecutar($strsql);     
$strsql = "SELECT *"
        . " FROM nompersonal_pry_cc where cedula = '$cedula'";
$result = sql_ejecutar($strsql);     
?>
<?php include("../../header5.php"); // <html><head></head><body> ?>
<link href="../../../includes/assets/css/custom-datatables.css" rel="stylesheet" type="text/css"/>
<!--
<script type="text/javascript" src="ewp.js"></script>
-->
<script type="text/javascript">
function enviar(op,id, cod_pry, cedula){
  
  if (op==1){   // Opcion de Agregar
    //document.frmAgregar.registro_id.value=id;
    document.frmPrincipal.op.value=op;
    document.frmPrincipal.action="proyecto_cc_add.php";
    document.frmPrincipal.cedula.value=cedula;
    document.frmPrincipal.submit(); 
  }
  if (op==2){   // Opcion de Modificar
    //alert($op);   
    document.frmPrincipal.registro_id.value=id;   
    document.frmPrincipal.cod_pry.value=cod_pry;   
    document.frmPrincipal.op.value=op;
    document.frmPrincipal.cedula.value=cedula;
    document.frmPrincipal.action="proyecto_cc_edit.php";
    document.frmPrincipal.submit();   
  }
  if (op==3){   // Opcion de Eliminar
    if (confirm("\u00BFEst\u00E1 seguro que desea eliminar el registro ?"))
    {         
      document.frmPrincipal.registro_id.value=id;
      document.frmPrincipal.op.value=op;
      document.frmPrincipal.action="proyecto_cc_delete.php";
      document.frmPrincipal.cedula.value=cedula;
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
               Proyectos / Centro De Costo
              </div>
              <div class="actions">
                  <a class="btn btn-sm blue"  onclick="javascript: window.location='proyecto_cc_add.php?cedula=<?php echo $cedula; ?>'">
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
                <th>C&oacute;digo de proyecto</th>
                <th>Fecha Inicio</th>
                <th>Fecha Fin</th>
                <th>Porcentaje</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
              </tr>
              </thead>
              <tbody>
              <?php
                while( $fila = mysqli_fetch_array($result) ) // $fila=fetch_array($res)
                { 
                  $query="select descripcion from proyecto_cc where codigo = '$fila[codigo_proyecto_cc]'";  
                  $result2=sql_ejecutar($query);    
                  $descripcion = mysqli_fetch_array($result2)['descripcion']; // Aquí es donde extraemos la descripción de la segunda consulta
          
                ?>
                  <tr class="odd gradeX">
                    <td><?php echo $fila['codigo_proyecto_cc'] . ' - ' . $descripcion; ?></td>
                    <td><?php echo $fila['fecha_inicio']; ?></td>
                    <td><?php echo $fila['fecha_fin']; ?></td>
                    <td><?php echo $fila['porcentaje']; ?></td>
                    <td style="text-align: center">
                        <a href="javascript:enviar(<?php echo(2); ?>,'<?php echo($fila['id']); ?>', '<?php echo($fila['codigo_proyecto_cc']); ?>', <?php echo($cedula); ?>);" title="Editar"><img src="../../../includes/imagenes/icons/pencil.png" width="16" height="16"></a>
                    </td>
                    <td style="text-align: center">
                        <a href="javascript:enviar(<?php echo(3); ?>,'<?php echo($fila['id']); ?>');" title="Eliminar"><img src="../../../includes/imagenes/icons/delete.png" alt="Eliminar" width="16" height="16"></a>
                    </td>
                  </tr>
                  <?php                 
                }
              ?>
              </tbody>
              </table>
                  <input name="registro_id" type="hidden" value="">
                  <input name="cod_pry" type="hidden" value="">
                  <input name="op" type="hidden" value="">
                  <input name="cedula" type="hidden" value="">
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
<?php include("../../footer5.php"); ?>
<script type="text/javascript">
  $(document).ready(function() { 
    $('#table_datatable').DataTable({
      "iDisplayLength": 25,
        //"sPaginationType": "bootstrap",
      "sPaginationType": "bootstrap_extended", 
        "oLanguage": {
          "sSearch": "<img src='../../../includes/imagenes/icons/magnifier.png' width='16' height='16' > Buscar:",
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
            { 'bSortable': false, 'aTargets': [3,4] },
            { "bSearchable": false, "aTargets": [3,4] },
            { "sWidth": "15%", "aTargets": [0] },
            { "sWidth": "8%", "aTargets": [3,4] }
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