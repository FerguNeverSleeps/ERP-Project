<?php
session_start();
ob_start();
?>
<?php
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // Always modified
header("Cache-Control: private, no-store, no-cache, must-revalidate"); // HTTP/1.1 
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache"); // HTTP/1.0
?>

<?php
include("../lib/common.php");
include ("func_bd.php") ;
//codigo para pagina;
$TAMANO_PAGINA = 20;
$pagina = $_GET["pagina"];
$bandera=$_GET['bandera'];
if(isset($_POST['bandera']))
  $bandera=$_POST['bandera'];
if (!$pagina) {
    $inicio = 1;
    $pagina=1;
}
else {
    $inicio = ($pagina - 1) * $TAMANO_PAGINA+1;
} 
$limit=$inicio-1;
?>

<script>

function CerrarVentana(){
  javascript:window.close();
}

function enviar(op,id){
  
  if (op==1){
    // Opcion de Agregar
    document.frmPrincipal.registro_id.value=id;   
    document.frmPrincipal.op.value=op;
    document.frmPrincipal.action="salarios_acumulados_agregar.php";
    document.frmPrincipal.submit(); 
  }
  if (op==2){   // Opcion de Modificar
    document.frmPrincipal.registro_id.value=id;
    document.frmPrincipal.op.value=op;
    document.frmPrincipal.action="salarios_acumulados_agregar.php";
    document.frmPrincipal.submit();   
  }
  if (op==3){   // Opcion de Eliminar
    if (confirm("Esta seguro que desea eliminar el registro ?"))
    {         
      document.frmPrincipal.registro_id.value=id;
      document.frmPrincipal.op.value=op;
      document.frmPrincipal.submit();
    }   
  }
}
</script>
  <?php 


$criterio=$_POST['optOpcion'];
$cadena=$_POST['textfield'];
$registro_id=$_POST[registro_id];

if (isset($_GET[txtficha]))
{
  $ficha=$_GET[txtficha];
   $cedula=$_GET[cedula];
}
else
{
  $ficha=$_POST[txtficha];
  $cedula=$_POST[cedula];
}

$op=$_POST['op'];

if ($op==3) {//Se presiono el boton de Eliminar     
  $query="delete FROM salarios_acumulados WHERE id='$registro_id'";     
  $result=sql_ejecutar($query);
  
  activar_pagina("salarios_acumulados_ajax.php?txtficha=$ficha&cedula=$cedula&bandera=$bandera");     
  }     
elseif ($cadena <> ""){ // Condicion para filtrado
    
    // para obtener la cantidad de registros
    $strsql="SELECT COUNT(*) FROM salarios_acumulados";
    $strsql=filtrado($criterio,$cadena,$strsql,"cedula","nombre");    
    $result =sql_ejecutar($strsql);     
    $fila = fetch_array($result); 
    $num_total_registros = $fila[0];  
  
    $strsql="SELECT * FROM salarios_acumulados";
    $strsql=filtrado($criterio,$cadena,$strsql,"cedula","nombre");  
    
      
    
    $strsql= "$strsql LIMIT $TAMANO_PAGINA OFFSET $limit";
    $result =sql_ejecutar($strsql);     
      $total_paginas = ceil($num_total_registros / $TAMANO_PAGINA); 
     }
else{// No se filtra y se muestran todos los datos

  $strsql= "SELECT COUNT(*) from salarios_acumulados where cedula='$cedula' AND ficha='$ficha'";
  $result =sql_ejecutar($strsql); 
  $fila = fetch_array($result); 
  
  $num_total_registros = $fila[0];
  
  $strsql= "SELECT a.*, b.personal_id, b.cedula, b.ficha, b.apenom, c.descrip as planilla, d.descrip as tipo_planilla, e.descrip as frecuencia "
          . " FROM salarios_acumulados as a"
          . " LEFT JOIN nompersonal as b ON (a.cedula=b.cedula AND a.ficha=b.ficha)"
          . " LEFT JOIN nom_nominas_pago as c ON (a.cod_planilla=c.codnom AND a.tipo_planilla=c.tipnom)"
          . " LEFT JOIN nomtipos_nomina as d ON (a.tipo_planilla=d.codtip)"
          . " LEFT JOIN nomfrecuencias as e ON (a.frecuencia_planilla=e.codfre)"
          . " WHERE a.cedula='$cedula' and a.ficha='$ficha'"
          . " ORDER BY a.fecha_pago DESC ";
  $result =sql_ejecutar($strsql);   
    $total_paginas = ceil($num_total_registros / $TAMANO_PAGINA);
}
include ("../header4.php");
?>
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
                Salarios Acumulados - Empleado
              </div>
              <div class="actions">
                <a class="btn btn-md red"  id="boton_reporte">
                  <i class="fa fa-print"></i>
                      Reporte
                  </a>
                <a class="btn btn-sm blue"  href="javascript:enviar(1,0)">
                  <i class="fa fa-plus"></i>
                      Agregar
                  </a>
              </div>
            </div>
             <div class="portlet-body">
                <table class="table table-striped table-bordered table-hover" id="table_datatable">
                  <thead>
                    <tr>
                      <th>Fecha</th>
                      <th>Planilla</th>
                      <th>Tipo Planilla</th>
                      <th>Frecuencia</th>
                      <th>Salario</th>
                      <th>Ac. Vac.</th>
                      <th>XIII</th>
                      <th>G. Rep.</th>
                      <th>XIII G. Rep.</th>
                      <th>Com.</th>
                      <th>Viáticos.</th>
                      <th>Grat.</th>
                      <th>Bono</th>
                      <th>Prima</th>
                      <th>Otros</th>
                      <th>Donac.</th>
                      <th>Seg. Soc.</th>
                      <th>Seg. Edu.</th>
                      <th>ISLR</th>
                      <th>ISLR G. Rep.</th>
                      <th>Acre. Sum.</th>
                      <th>Desc. Emp.</th>
                      <th>Liq.</th>
                      <th>Neto</th>                      
                      <th></th>
                      <th></th>
                    </tr>
                  </thead>
                  <tbody style="font-size: 12px; ">
                      <?php
                          while ($fila = fetch_array($result))
                          { 
                          ?>
                            <tr>
                              <td><?php echo $fila['fecha_pago']; ?></td>
                              <td><?php echo $fila['planilla']; ?></td>
                              <td><?php echo $fila['tipo_planilla']; ?></td>
                              <td><?php echo $fila['frecuencia']; ?></td>
                              <td><?php echo $fila['salario_bruto']; ?></td>
                              <td><?php echo $fila['vacac']; ?></td>
                              <td><?php echo $fila['xiii']; ?></td>
                              <td><?php echo $fila['gtorep']; ?></td>
                              <td><?php echo $fila['xiii_gtorep']; ?></td>
                              <td><?php echo $fila['comisiones']; ?></td>
                              <td><?php echo $fila['viaticos']; ?></td>
                              <td><?php echo $fila['gratificaciones']; ?></td>
                              <td><?php echo $fila['bono']; ?></td>
                              <td><?php echo $fila['prima']; ?></td>
                              <td><?php echo $fila['otros_ing']; ?></td>
                              <td><?php echo $fila['donaciones']; ?></td>
                              <td><?php echo $fila['s_s']; ?></td>
                              <td><?php echo $fila['s_e']; ?></td>
                              <td><?php echo $fila['islr']; ?></td>
                              <td><?php echo $fila['islr_gr']; ?></td>
                              <td><?php echo $fila['acreedor_suma']; ?></td>
                              <td><?php echo $fila['desc_empresa']; ?></td>
                              <td><?php echo $fila['liquida']; ?></td>
                              <td><?php echo $fila['Neto']; ?></td>
                              <td>
                                <div align="center">
                                    <font size="2" face="Arial, Helvetica, sans-serif">
                                        <a href="javascript:enviar(<?php echo(2); ?>,<?php echo $fila['id']; ?>);"><img src="img_sis/ico_edit.gif" alt="Modificar el Registro Actual" width="16" height="16" border="0" align="absmiddle">
                                        </a>
                                    </font>
                                </div>
                              </td>
                              <td>
                                <div align="center"><font size="2" face="Arial, Helvetica, sans-serif"><a href="javascript:enviar(<?php echo(3); ?>,<?php echo($fila['id']); ?>);"><img src="../imagenes/delete.gif" alt="Eliminar el Registro Actual" width="16" height="16" border="0" align="absmiddle" ></a></font></div>
                              </td>
                            </tr>
                            <?php
                            }
                            ?>
                  </tbody>
                </table>
              </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="filtro" tabindex="-1" role="basic" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Reporte Salarios Acumulados</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4 text-right">Fecha Inicio</div>
                    <div class="col-md-6" id="messages" role="alert">
                    <div class="input-group date date-picker" data-provide="datepicker"  data-date-format="dd-mm-yyyy"> 
                         <input name="fecha_inicio" type="text" id="fecha_inicio" class="form-control" value="" maxlength="60">
                          <span class="input-group-btn">
                            <button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
                          </span>
                        </div>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-4 text-right">Fecha Fin</div>
                    <div class="col-md-6" id="messages" role="alert">
                        <div class="input-group date date-picker" data-provide="datepicker"  data-date-format="dd-mm-yyyy"> 
                         <input name="fecha_fin" type="text" id="fecha_fin" class="form-control" value="" maxlength="60">
                          <span class="input-group-btn">
                            <button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
                          </span>
                        </div>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-4 text-right">Formato</div>
                    <div class="col-md-6" id="messages" role="alert">
                        <select name="tipo_reporte" id="tipo_reporte"  class="form-control" maxlength="60">
                            <option value="_pdf" selected>PDF</option>
                            <option value="_excel">EXCEL</option>
                        </select>
                    </div>
                </div><br>
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn dark btn-outline" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn blue" id="btnFiltro"><i class="fa fa-print"></i> &nbsp;Imprimir</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
    <input name="txtficha" id="txtficha" type="hidden" value="<?php echo $ficha; ?>">    
    <input name="cedula" id="cedula" type="hidden" value="<?php echo $cedula; ?>">
    <input name="registro_id" id="registro_id" type="hidden" value="">  
    <input name="op" type="hidden" value="">  
  </form>
  <?php include("../footer4.php"); ?>
<script type="text/javascript">
   $(document).ready(function() { 
    $("#boton_reporte").on("click", function(){
        $('#filtro').modal('show');
        /*$.get("obtenerPlanillaRango.php",function(res){
            $("#rangoList").empty();
            $("#rangoList").append(res);
        });*/
        $("#btnFiltro").on("click",function(){
            var cedula       = $("#cedula").val();
            var ficha        = $("#txtficha").val();
            var fecha_inicio = $("#fecha_inicio").val();
            var fecha_fin    = $("#fecha_fin").val();
            var tipo_reporte = $("#tipo_reporte").val();
            if(cedula == '0' && fecha == '0')
            {
                $("#messages").addClass("alert alert-danger alert-dismissible");
                $("#messages").text("Seleccione una ubicación o una planilla");
            }else
            {
                if(fecha_inicio == '' && fecha_inicio == '')
                {
                    $("#messages").addClass("alert alert-danger alert-dismissible");
                    $("#messages").text("Seleccione el tipo de chequera");

                }
                else
                {
                    $("#messages").removeClass("alert alert-danger");
                    $("#messages").empty();
                    
                    $('#filtro').modal('hide');
                    if(tipo_reporte == '_pdf'){
                      var path = 'pdf/pdf_salarios_acumulados.php';
                    }else{
                      var path = 'excel/excel_salarios_acumulados.php';
                    }
                    var ruta = "../../reportes/"+ path +"?cedula="+cedula+"&fecha_inicio="+fecha_inicio+"&fecha_fin="+fecha_fin+"&ficha="+ficha;
                    window.open(ruta,'_blank')

                }
            }
        });

    });
    //$('#table_datatable').DataTable(); 

            // begin first table
            $('#table_datatable').DataTable({
              //"oSearch": {"sSearch": "Escriba frase para buscar"},
              "iDisplayLength": 10,
                //"sPaginationType": "bootstrap",
              "sPaginationType": "bootstrap_extended", 
              "aaSorting": [[ 0, "desc" ]], 
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
                    { 'bSortable': false, 'aTargets': [19, 20] },
                    { "bSearchable": false, "aTargets": [19, 20 ] },
                    { "sWidth": "5%", "aTargets": [19, 20] },
                    { "sWidth": "10%", "aTargets": [1, 2, 3] }
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
</script>
</body>
</html>
