<?
session_start();
ob_start();
$termino= $_SESSION['termino'];
?>
<?


//DECLARACION DE LIBRERIAS
require_once '../lib/common.php';
require_once '../paginas/func_bd.php';
include ("../paginas/funciones_nomina.php");
//include ("../header.php");

$conexion=conexion();

$id = $_REQUEST['id'];
$opcion    = $_REQUEST['opcion'];

if($opcion==3){
    $id_detalle=$_REQUEST["id_detalle"];
    $dias_enfermedad=$_REQUEST["dias_enfermedad"];
    $observaciones=$_REQUEST["observaciones"];
    $consulta_sipe = "UPDATE ach_sipe_detalle_sysmeca SET "
                   . "dias_enfermedad = '{$dias_enfermedad}',"
                   . "observaciones = '{$observaciones}'"
                   . " WHERE id_detalle='{$id_detalle}'";

    $resultado_sipe=query($consulta_sipe,$conexion);
}



$consulta_tabla= "SELECT a.*, b.* "
          . " FROM marvin_encabezado as a "
          . " LEFT JOIN  marvin_detalle as b ON (a.id=b.id_encabezado) "
          . " WHERE a.id='$id' "
          . " ORDER BY b.ficha ASC ";
$resultado_tabla=query($consulta_tabla,$conexion);

$consulta_id= "SELECT id "
          . " FROM marvin_encabezado as a "
          . " WHERE a.id='$id' ";
$resultado_id=query($consulta_id,$conexion);
$fila_id=fetch_array($resultado_id,$conexion);
$id=$fila_id['id'];

include ("../header4.php");
//?>

<form action="" method="post" name="frmPrincipal" id="frmPrincipal">
<div class="page-container">
  <div class="page-content-wrapper">
    <div class="page-content">
      <div class="row">
        <div class="col-md-12">
           <!-- BEGIN EXAMPLE TABLE PORTLET-->
          <div class="portlet box blue">
            <div class="portlet-title">
              <div class="caption">
                Marvin - Procesamiento
              </div>
<!--              <div class="actions">
                <a class="btn btn-sm blue" onclick="enviar(<?php echo(4); ?>,<?php echo $id;?>,'','','','');">
                  <i class="fa fa-pencil"></i>
                      Generar TXT
                  </a>
              </div>-->
            </div>
             <div class="portlet-body">
                <table class="table table-striped table-bordered table-hover" id="table_datatable">
                  <thead>
                    <tr>
                      <th>Ficha</th>  
                      <th>Regular</th>
                      <th>Dia Libre Nacional (150)</th>
                      <th>Dia Feriado (250)</th>
                      <th>Compensatorio (050)</th>
                      <th>Extra (125)</th>
                      <th>Extra (150)</th>
                      <th>Extra (175)</th>
                      <th>Extra (188)</th>
                      <th>Extra (219)</th>
                      <th>Extra (225)</th>
                      <th>Extra (263-Regular)</th>  
                      <th>Extra (263-Libre)</th>  
                      <th>Extra (306)</th> 
                      <th>Extra (313)</th> 
                      <th>Extra (329)</th> 
                      <th>Extra (375)</th> 
                      <th>Extra (394)</th> 
                      <th>Extra (438)</th> 
                      <th>Extra (459)</th> 
                      <th>Extra (547)</th> 
                      <th>Extra (656)</th> 
                      <th>Extra (766)</th> 
                      <th>Certificado</th> 
                      <th>Tardanza</th> 
                      <th>Ausencia</th> 
                      <th>Permiso</th> 
                      <th></th>
                      <th></th>
                    </tr>
                  </thead>
                  <tbody style="font-size: 12px; ">
                      <?php
                          while ($fila_tabla=fetch_array($resultado_tabla,$conexion))
                          { 
                              $total_excep=$fila_tabla['total_excep'];
                              $total_salario=$fila_tabla['total_salario'];
                              $total_isr=$fila_tabla['total_isr'];
                              $total_xiii=$fila_tabla['$total_xiii'];
                              $total_x=$fila_tabla['total_x'];
                              $total_enfermedad=$total_enfermedad+$fila_tabla['dias_enfermedad'];
                           
                          ?>
                            <tr id="<?php echo $fila_tabla['id_detalle'];?>">
                              <td><?php echo $fila_tabla['ficha']; ?></td>
                              <td><?php echo $fila_tabla['regular']; ?></td>
                              <td><?php echo $fila_tabla['dia_libre_nacional_150']; ?></td>
                              <td><?php echo $fila_tabla['dia_feriado_250']; ?></td>
                              <td><?php echo $fila_tabla['compensatorio_050']; ?></td>
                              <td><?php echo $fila_tabla['extra_125']; ?></td>
                              <td><?php echo $fila_tabla['extra_150']; ?></td>
                              <td><?php echo $fila_tabla['extra_175']; ?></td>
                              <td><?php echo $fila_tabla['extra_188']; ?></td>
                              <td><?php echo $fila_tabla['extra_219']; ?></td>
                              <td><?php echo $fila_tabla['extra_225']; ?></td>
                              <td><?php echo $fila_tabla['extra_263_regular']; ?></td>
                              <td><?php echo $fila_tabla['extra_263_libre']; ?></td>
                              <td><?php echo $fila_tabla['extra_306']; ?></td>
                              <td><?php echo $fila_tabla['extra_313']; ?></td>
                              <td><?php echo $fila_tabla['extra_329']; ?></td>
                              <td><?php echo $fila_tabla['extra_375']; ?></td>
                              <td><?php echo $fila_tabla['extra_394']; ?></td>
                              <td><?php echo $fila_tabla['extra_438']; ?></td>
                              <td><?php echo $fila_tabla['extra_459']; ?></td>
                              <td><?php echo $fila_tabla['extra_547']; ?></td>
                              <td><?php echo $fila_tabla['extra_656']; ?></td>
                              <td><?php echo $fila_tabla['extra_766']; ?></td>
                              <td><?php echo $fila_tabla['certificado_medico']; ?></td>
                              <td><?php echo $fila_tabla['tardanza']; ?></td>
                              <td><?php echo $fila_tabla['ausencia']; ?></td>
                              <td><?php echo $fila_tabla['permiso']; ?></td>
                              <td>
                                <div align="center">
                                    <font size="2" face="Arial, Helvetica, sans-serif">
                                        <a onclick="enviar(<?php echo(3); ?>,<?php echo $fila_tabla['id_detalle'];?>,'<?php echo $fila_tabla['sec_empleado'];?>','<?php echo $fila_tabla['apellido'];?>','<?php echo $fila_tabla['dias_enfermedad'];?>','<?php echo $fila_tabla['observaciones'];?>');" data-toggle="modal" href="#editar"><img src="../img_sis/ico_edit.gif" alt="Modificar el Registro Actual" width="16" height="16" border="0" align="absmiddle">
                                        </a>
                                    </font>
                                </div>
                              </td>
                              <td>
                                <div align="center"><font size="2" face="Arial, Helvetica, sans-serif"><a href="javascript:enviar(<?php echo(3); ?>,<?php echo($fila_tabla['id']); ?>);"><img src="../imagenes/delete.gif" alt="Eliminar el Registro Actual" width="16" height="16" border="0" align="absmiddle" ></a></font></div>
                              </td>
                            </tr>
                            <?php
                            }
                            ?>
                  </tbody>
                  <tfoot>
                        <tr>                            
                            <th></th>
                            <th colspan="4">TOTALES</th>
                            <th id="total_excepciones"><?php print $total_excep;?></th>
                            <th id="total_salario"><?php print $total_salario;?></th>
                            <th id="total_isr"><?php print $total_isr;?></th>
                            <th id="total_xiii"><?php print $total_xiii;?></th>
                            <th id="total_otros"><?php print $total_otros;?></th>
                            <th id="total_x"><?php print $total_x;?></th>
                            <th id="total_enfermedad"><?php print $total_enfermedad;?></th>
                            <th></th>
                            <th></th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
              </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
    <input name="txtficha" id="txtficha" type="hidden" value="<?php echo $ficha; ?>">    
    <input name="cedula" id="cedula" type="hidden" value="<?php echo $cedula; ?>">
    <input name="registro_id" id="registro_id" type="hidden" value="">  
    <input name="op" type="hidden" value="">  
  </form>
  <?php include("../footer4.php"); ?>
<script type="text/javascript">
   $(document).ready(function() { 
    //$('#table_datatable').DataTable(); 

            // begin first table
            $('#table_datatable').DataTable({
              //"oSearch": {"sSearch": "Escriba frase para buscar"},
              "iDisplayLength": 25,
              "bStateSave" : true,
                //"sPaginationType": "bootstrap",
              "sPaginationType": "bootstrap_extended",
              "aaSorting": [[ 0, "asc" ]],
              //"sPaginationType": "full_numbers",
                "oLanguage": {
                  "sSearch": "<img src='../../includes/imagenes/icons/magnifier.png' width='16' height='16' > Buscar:",
                    "sLengthMenu": "Mostrar _MENU_",
                    //"sInfo": "Showing page _PAGE_ of _PAGES_", // Mostrando 1 to 5 de 18 entradas
                    //"sInfoEmpty": "No hay registros para mostrar",
                    "sInfoEmpty": "",
                    //"sInfo": "",
                    "sInfo":"Total _TOTAL_ registros",
                    "sInfoFiltered": "",
                  "sEmptyTable":  "No hay datos disponibles",//"No data available in table",
                    "sZeroRecords": "No se encontraron registros",//"No matching records found",
                    /*"oPaginate": {
                        "sPrevious": "Página Anterior",
                        "sNext": "Página Siguiente"
                    }*/
                    "oPaginate": {
                        "sPrevious": "P&aacute;gina Anterior",//"Prev",
                        "sNext": "P&aacute;gina Siguiente",//"Next",
                        "sPage": "P&aacute;gina",//"Page",
                        "sPageOf": "de",//"of"
                    }
                },
                /*
                "aLengthMenu": [
                    [5, 15, 20, -1],
                    [5, 15, 20, "Todos"] // change per page values here
                ],
                */
                "aLengthMenu": [ // set available records per page
                    [5, 10, 25, 50,  -1],
                    [5, 10, 25, 50, "Todos"]
                ],                
                "aoColumnDefs": [
                    { 'bSortable': false, 'aTargets': [27, 28] },
                    { "bSearchable": false, "aTargets": [27, 28 ] },
                    { "sWidth": "5%", "aTargets": [27, 28] }
                ],
         "fnDrawCallback": function() {
                $('#table_datatable_filter input').attr("placeholder", "Escriba frase para buscar");
         }
                /*
              "aoColumns": [
            null,
            null,
            { "sWidth": "10px" },
            { "sWidth": "10px" },
        ]*/
              /*
                "aoColumns": [
                  { "bSortable": false },
                  null,
                  { "bSortable": false, "sType": "text" },
                  null,
                  { "bSortable": false },
                  { "bSortable": false }
                ],*/
            });

            $('#table_datatable').on('change', 'tbody tr .checkboxes', function(){
                 $(this).parents('tr').toggleClass("active");
            });

            $('#table_datatable_wrapper .dataTables_filter input').addClass("form-control input-medium input-inline"); 
            $('#table_datatable_wrapper .dataTables_length select').addClass("form-control input-xsmall"); 
   });



function enviar(op,id,sec_empleado,apellido,dias_enfermedad,observaciones){
  if(op==3){

    $("#modal-id-detalle").val(id);
    $("#modal-ficha").val(sec_empleado+" "+apellido);
    $("#modal-dias-enfermedad").val(dias_enfermedad);
    $("#modal-observaciones").val(observaciones);

    return;
  }
  
  if (op==4){   // Opcion de Eliminar
    if (confirm("¿Desea Generar TXT SIPE?"))
    {         
      
      location.href ="txt_sipe_sysmeca.php?id="+id;
    }   
  }
}

function modal_aceptar(){
    window.location.href="?anio=<?php print $_REQUEST['anio']?>&mes=<?php print $_REQUEST['mes']?>&opcion=3&id_detalle="+$("#modal-id-detalle").val()+"&dias_enfermedad="+$("#modal-dias-enfermedad").val()+"&observaciones="+$("#modal-observaciones").val();
}
</script>



    <!--MODAL-->
    <div class="modal fade" id="editar" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">Editar</h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="modal-id-detalle" value="">
                    <div class="row">
                        <div class="col-md-12"><b>Ficha:</b></div>
                        <div class="col-md-12"><input type="text" id="modal-ficha" class="form-control" disabled=""></div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-12"><b>Dias Enfermedad:</b></div>
                        <div class="col-md-12">
                            <input type="number" class="form-control" id="modal-dias-enfermedad" min="0" value="0">
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-12"><b>Observación:</b></div>
                        <div class="col-md-12">
                            <textarea class="form-control" id="modal-observaciones"></textarea>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn dark btn-outline" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn green" onclick="modal_aceptar()">Aceptar</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>


</body>
</html>