<?php 
require_once '../../generalp.config.inc.php';
if (!isset($_SESSION)) {
    session_start();
    ob_start();
}

//error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require_once '../lib/common.php';
include ("func_bd.php");
//$conexion=conexion();
error_reporting(E_ALL ^ E_DEPRECATED);
$registro_id=(isset($_POST['registro_id'])) ? $_POST['registro_id'] : ''; 
$op=(isset($_POST['op'])) ? $_POST['op'] : '';

if ($op==3) //Se presiono el boton de Eliminar
{
  // Consultar numero de trabajadores con este turno
  $sql = "SELECT COUNT(*) as total FROM nompersonal WHERE turno_id='{$registro_id}'";
  $res = sql_ejecutar($sql);

  $cantidad = 0;
  if($fila = mysqli_fetch_array($res))
  {
      $cantidad = $fila['total'];
  }

  if($cantidad<=0)
  {
      // Eliminar primero horarios del turno

      $query  = "DELETE FROM nomturnos_horarios WHERE turno_id='$registro_id'"; 
      $result = sql_ejecutar($query);

      $query  = "DELETE FROM nomturnos WHERE turno_id='$registro_id'"; 
      $result = sql_ejecutar($query);

      activar_pagina("turnos.php");  
  }
  else
      echo '<script type="text/javascript">alert("¡No se puede eliminar el turno! Existen trabajadores asignados.");</script>';    
}     
$strsql = "SELECT 
            turno_id,
            nt.descripcion,
            date_format(entrada,'%h:%i:%s %p') as entrada,
            date_format(tolerancia_entrada,'%h:%i:%s %p') as tolerancia_entrada,
            date_format(inicio_descanso,'%h:%i:%s %p') as inicio_descanso,
            date_format(salida_descanso,'%h:%i:%s %p') as salida_descanso,
            date_format(tolerancia_descanso,'%h:%i:%s %p') as tolerancia_descanso,
            date_format(salida,'%h:%i:%s %p') as salida,
            date_format(tolerancia_salida,'%h:%i:%s %p') as tolerancia_salida,
            COALESCE((SELECT rotativo    FROM nomturnos_tipo ntt WHERE ntt.turnotipo_id=nt.tipo),0) as rotativo,
            COALESCE((SELECT descripcion FROM nomturnos_tipo ntt WHERE ntt.turnotipo_id=nt.tipo), nt.tipo) as tipo_turno 
           FROM  nomturnos nt";
$result = sql_ejecutar_utf8($strsql);       
?>
<?php include("../header4.php"); // <html><head></head><body> ?>
<link href="../../includes/assets/css/custom-datatables.css" rel="stylesheet" type="text/css"/>
<style type="text/css">
  #table_datatable thead tr th{
    text-align: center;
    vertical-align: middle;
  }
  .dropdown-menu {
      min-width: 170px;
  }

  .portlet > .portlet-title > .actions > .btn-group {
      margin-top: -11px;
  }

  #show_cols{
      color: white; 
      padding-left: 5px; 
      padding-right: 5px;
  }

  @media (max-width:336px) {
    .portlet > .portlet-title > .actions {
        float: left;
    }
  }
</style>
<script type="text/javascript">
function enviar(op,id)
{
  if (op==1)
  {   // Opcion de Agregar
    document.frmCargos.op.value=op;
    document.frmCargos.action="ag_turnos.php";
    document.frmCargos.submit(); 
  }
  if (op==2)
  {   // Opcion de Modificar
    document.frmCargos.registro_id.value=id;    
    document.frmCargos.op.value=op;
    document.frmCargos.action="ag_turnos.php";
    document.frmCargos.submit();        
  }
  if (op==3)
  {   // Opcion de Eliminar
    if (confirm("\u00BFEst\u00E1 seguro que desea eliminar el registro?"))
    {         
      document.frmCargos.registro_id.value=id;
      document.frmCargos.op.value=op;
      document.frmCargos.action="turnos.php";
      document.frmCargos.submit();
    }   
  }
  if(op==4)
  {
    document.frmCargos.registro_id.value=id;    
    document.frmCargos.op.value=op;
    document.frmCargos.action="horarios_turno2.php";
    document.frmCargos.submit();
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
               Turnos
              </div>
              <div class="actions">
                <div class="btn-group">
                  <a id="show_cols" class="btn btn-sm" href="#" data-toggle="dropdown">
                     Columnas <i class="fa fa-angle-down"></i>
                  </a>
                  <div id="table_datatable_column_toggler" class="dropdown-menu hold-on-click dropdown-checkboxes pull-right">
                    <label><input type="checkbox" data-column="0" checked>C&oacute;digo</label>
                    <label><input type="checkbox" data-column="1" checked>Turno</label>
                    <label><input type="checkbox" data-column="2" checked>Entrada</label>
                    <label><input type="checkbox" data-column="3">Tolerancia de entrada</label>
                    <label><input type="checkbox" data-column="4">Inicio descanso</label>
                    <label><input type="checkbox" data-column="5">Salida descanso</label>
                    <label><input type="checkbox" data-column="6">Tolerancia descanso</label>
                    <label><input type="checkbox" data-column="7" checked>Salida</label>
                    <label><input type="checkbox" data-column="8">Tolerancia salida</label>
                    <label><input type="checkbox" data-column="9">Tipo de turno</label>
                  </div>
                </div>
                <a class="btn btn-sm blue"  onclick="javascript: window.location='ag_turnos.php'">
                  <i class="fa fa-plus"></i>
                  Agregar
                </a>
                <a class="btn btn-sm blue"  onclick="javascript: window.location='menu_configuracion.php'">
                  <i class="fa fa-arrow-left"></i> Regresar
                </a>
              </div>
            </div>
            <div class="portlet-body">
              <div class="table-responsive">
              <form action="" method="post" name="frmCargos" id="frmCargos">
              <table class="table table-striped table-bordered table-hover" id="table_datatable">
              <thead>
              <tr>
                <th>C&oacute;digo</th>
                <th>Turno</th>
                <th>Entrada</th>
                <th>Tolerancia de entrada</th>
                <th>Inicio descanso</th>
                <th>Salida descanso</th>
                <th>Tolerancia descanso</th>
                <th>Salida</th>
                <th>Tolerancia salida</th>
                <th>Tipo de turno</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
              </tr>
              </thead>
              <tbody>
              <?php
                while( $fila = mysqli_fetch_array($result) ) // $fila=fetch_array($res)
                {    
                  $rotativo = $fila['rotativo'];             
                ?>
                  <tr class="odd gradeX">
                    <td style="text-align: center"><?php echo $fila['turno_id']; ?></td>
                    <td style="text-align: center"><?php echo $fila['descripcion']; ?></td>
                    <td style="text-align: center"><?php echo $fila['entrada']; ?></td>
                    <td style="text-align: center"><?php echo $fila['tolerancia_entrada']; ?></td>
                    <td style="text-align: center"><?php echo $fila['inicio_descanso']; ?></td>
                    <td style="text-align: center"><?php echo $fila['salida_descanso']; ?></td>
                    <td style="text-align: center"><?php echo $fila['tolerancia_descanso']; ?></td>
                    <td style="text-align: center"><?php echo $fila['salida']; ?></td>
                    <td style="text-align: center"><?php echo $fila['tolerancia_salida']; ?></td>
                    <td style="text-align: center"><?php echo $fila['tipo_turno']; ?></td>
                    <td style="text-align: center">
                        <a href="javascript:enviar(<?php echo(2); ?>,<?php echo "'".$fila['turno_id']."'"; ?>);" title="Editar"><img src="../../includes/imagenes/icons/pencil.png" alt="Editar" width="16" height="16"></a>
                        <a href="javascript:enviar(<?php echo(4); ?>,<?php echo "'".$fila['turno_id']."'"; ?>);" title="Horarios del turno"><img src="../../includes/imagenes/icons/calendar.png" alt="Horarios del turno" width="16" height="16"></a>
                    </td>
                    <td style="text-align: center">
                        <a href="javascript:enviar(<?php echo(3); ?>,<?php echo "'".$fila['turno_id']."'"; ?>);" title="Eliminar"><img src="../../includes/imagenes/icons/delete.png" alt="Eliminar" width="16" height="16"></a>
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

            var oTable = $('#table_datatable').DataTable({
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
                    { 'bSortable': false, 'aTargets': [10] },
                    { "bSearchable": false, "aTargets": [10] },
                    { "sWidth": "8%", "aTargets": [10] },
                    { 'bSortable': false, 'aTargets': [11] },
                    { "bSearchable": false, "aTargets": [11] },
                    { "bVisible": false, "aTargets": [ 3 ] },
                    { "bVisible": false, "aTargets": [ 4 ]  },
                    { "bVisible": false, "aTargets": [ 5 ]  },
                    { "bVisible": false, "aTargets": [ 6 ]  },
                    { "bVisible": false, "aTargets": [ 8 ]  },
                    { "bVisible": false, "aTargets": [ 9 ]  },
                    { "sWidth": "15%", "aTargets": [9] },
                ],
                 "fnDrawCallback": function() {
                        $('#table_datatable_filter input').attr("placeholder", "Escriba frase para buscar");
                 }
            });

            $('#table_datatable_wrapper .dataTables_filter input').addClass("form-control input-medium input-inline"); 
            $('#table_datatable_wrapper .dataTables_length select').addClass("form-control input-xsmall");

            $('#table_datatable_column_toggler input[type="checkbox"]').change(function(){
                console.log("Listo");
                /* Get the DataTables object again - this is not a recreation, just a get of the object */
                var iCol = parseInt($(this).attr("data-column"));
                var bVis = oTable.fnSettings().aoColumns[iCol].bVisible;
                oTable.fnSetColumnVis(iCol, (bVis ? false : true));
            });
   });
</script>
</body>
</html>