<?php 
require_once('../lib/database.php');

$db = new Database($_SESSION['bd']);


if($_REQUEST["accion"]=="ajax"):
    $start=$_REQUEST["iDisplayStart"];
    $limit=$_REQUEST["iDisplayLength"];
    $id_rm=$_REQUEST["id_rm"];

    $buscar=$_REQUEST["sSearch_0"];
    $buscar=json_decode($buscar,true);

    $add="";
    if(isset($buscar["ficha"]) and $buscar["ficha"]!="")
      $add.=" ficha ='".$buscar["ficha"]."' and";

    if(isset($buscar["cedula"]) and $buscar["cedula"]!="")
      $add.=" cedula like '".$buscar["cedula"]."%' and";

    if(isset($buscar["as"]) and $buscar["as"]!="")
      $add.=" asistencias ='".$buscar["as"]."' and";

    if(isset($buscar["rd"]) and $buscar["rd"]!="")
      $add.=" rd ='".$buscar["rd"]."' and";

    if(isset($buscar["lt"]) and $buscar["lt"]!="")
      $add.=" lt ='".$buscar["lt"]."' and";

    if(isset($buscar["rd"]) and $buscar["rd"]!="")
      $add.=" rd ='".$buscar["rd"]."' and";

    if(isset($buscar["domingos"]) and $buscar["domingos"]!="")
      $add.=" domingos ='".$buscar["domingos"]."' and";

    if(isset($buscar["feriados"]) and $buscar["feriados"]!="")
      $add.=" feriados ='".$buscar["feriados"]."' and";

    if(isset($buscar["libres"]) and $buscar["libres"]!="")
      $add.=" libres ='".$buscar["libres"]."' and";

    if(isset($buscar["faltas"]) and $buscar["faltas"]!="")
      $add.=" faltas ='".$buscar["faltas"]."' and";

    if(isset($buscar["amonestaciones"]) and $buscar["amonestaciones"]!="")
      $add.=" amonestaciones ='".$buscar["amonestaciones"]."' and";

    if(isset($buscar["h_totales"]) and $buscar["h_totales"]!="")
      $add.=" h_totales ='".$buscar["h_totales"]."' and";

    $orderby="";
    if($sort){
      foreach ($sort as $key => $value) 
        $orderby.="$key $value,";
      $orderby=" ORDER BY ".trim($orderby,","); 
    } 

    $limit_start="";
    if(!($start===NULL and $limit===NULL))
      $limit_start="LIMIT $limit OFFSET $start";


    $sql="select * from `resumen_marcaciones_det` WHERE $add id_rm='$id_rm'";
    $return=[];
    $return["aaData"]=[];
    $res = $db->query("$sql $orderby $limit_start");
    while($row = $res->fetch_assoc())
      $return["aaData"][]=$row;
    
    $res = $db->query("select count(*) total from ($sql) t");
    $row = $res->fetch_assoc();
    $return["iTotalRecords"]= $return["iTotalDisplayRecords"]=$row["total"];
    $return["sEcho"]=intval($_REQUEST['sEcho']);

    print json_encode($return);
  exit;
endif;


?>
<?php include("../header4.php"); ?>
<link href="../../includes/assets/css/custom-datatables.css" rel="stylesheet" type="text/css"/>
<style type="text/css">

.portlet > .portlet-title > .actions > .btn {
    padding: 4px 10px !important;
    margin-top: -14px !important;
}
#table_datatable tbody tr td{
  vertical-align: middle;
}

#table_datatable_length .form-control {
    padding: 0px;
}

div.dataTables_filter label {
    float: left;
}

#table_datatable_wrapper .dataTables_filter input {
   margin-top: 5px;
   margin-bottom: 5px;
}

#table_datatable_wrapper div.dataTables_length label {
  margin-top: 5px;
}

@media (min-width: 513px) {
  #search_situ.input-small {
      width: 122px !important;
  }
}

#table_datatable thead {
  color: white; 
  background: #00BCD4;
}

#table_datatable thead th {
  text-align: center;
}

.fieldset {
  display: flex;
  margin-top: 10px;
  border-bottom: 1px solid #BDBDBD;
}

.legend {
  font-size: 20px;
  flex: 0.4;
  padding-top: 25px;
  padding-left: 10px;
  color: #1f1f1f;
}

.fieldset .row {
  flex: 0.6;
}

tr.filter td{
  padding: 0 !important;
}

.form-control {
  border-color: #999999 !important;
}

#btnBuscar,
#btnLimpiar {
  color: #03A9F4;
  font-size: 16px;
  margin: 7px 3px;
  cursor: pointer;
}

#btnBuscar {
  margin-left: 7px;
}

.dataTables_filter{
  display: none;
}

.input-sm {
  padding: 5px 5px !important;
}

</style>
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
                Resumen Marcaciones
              </div>
              <div class="actions"> 
                <a class="btn btn-sm blue"  onclick="javascript: window.location='ag_integrantes.php'" style="display: none;">
                  <i class="fa fa-plus"></i>
                  Agregar
                </a>        
              </div>
            </div>
            <div class="portlet-body">
              <select id="encabezado" onchange="seleccionar_encabezado()">
                <?php
                  $res = $db->query("SELECT C.*, DATE_FORMAT(C.fecha_inicio,'%d/%m/%Y') fi, DATE_FORMAT(C.fecha_fin,'%d/%m/%Y') ff FROM `resumen_marcaciones_cab` C");
                  while($row = $res->fetch_assoc())
                    print "<option value='".$row["id_rm"]."'>".$row["anio"]."-".$row["periodo"]." &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ".$row["fi"]." - ".$row["ff"]." [revision: ".$row["revision"]."]</option>";
                ?>
              </select>
              <br><br>
              <table class="table table-striped table-bordered table-hover" id="table_datatable">
                <thead>
                  <tr>                        
                    <th>Ficha</th>
                    <th>Cédula</th>
                    <th>AS</th>
                    <th>RD</th>
                    <th>LT</th>
                    <th>Domingos</th>
                    <th>Feriados</th>
                    <th>Libres</th>
                    <th>Faltas</th>
                    <th>Amonest.</th>
                    <th>Horas<br>Totales</th>
                    <th>Horas<br>Extras</th>
                    <th>Horas<br>Domingos</th>
                    <th>Horas<br>Feriados</th>
                    <th>F. Domingos</th>
                    <th>F. Feriados</th>
                    <th>Monto<br>Pagar</th>
                    <th>T</th>                      
                    <th>S</th>       
                  </tr>

                  <tr role="row" class="filter" style="background: white;">
                    <td><input type="text" class="form-control form-filter input-sm" id="buscar_horas_ficha"></td>
                    <td><input type="text" class="form-control form-filter input-sm" id="buscar_horas_cedula"></td>
                    <td><input type="text" class="form-control form-filter input-sm" id="buscar_horas_as"></td>
                    <td><input type="text" class="form-control form-filter input-sm" id="buscar_horas_rd"></td>
                    <td><input type="text" class="form-control form-filter input-sm" id="buscar_horas_lt"></td>
                    <td><input type="text" class="form-control form-filter input-sm" id="buscar_horas_domingos"></td>
                    <td><input type="text" class="form-control form-filter input-sm" id="buscar_horas_feriados"></td>
                    <td><input type="text" class="form-control form-filter input-sm" id="buscar_horas_libres"></td>
                    <td><input type="text" class="form-control form-filter input-sm" id="buscar_horas_faltas"></td>
                    <td><input type="text" class="form-control form-filter input-sm" id="buscar_horas_amonestaciones"></td>
                    <td><input type="text" class="form-control form-filter input-sm" id="buscar_horas_totales"></td>
                    <td>
                      <i id="btnBuscar" class="fa fa-search filter-submit"></i>
                      <i id="btnLimpiar" class="fa fa-close filter-submit"></i>
                    </td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></th>                
                    <td colspan="16">
                    </td>
                  </tr>
                </thead>
                <tbody></tbody>
              </table>
          </div>
          <!-- END EXAMPLE TABLE PORTLET-->
        </div>
      </div>
      <!-- END PAGE CONTENT-->
    </div>
  </div>
  <!-- END CONTENT -->
  <div class="modal fade" id="modal-reporte">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title">Selecione Formato De Reporte</h4>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
              <a id="reportepdf" href="" class="btn btn-default btn-block">Pdf</a>
            </div>
            <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
              <a  id="reporteexcel" href="" class="btn btn-default btn-block">Excel</a>
            </div>
            <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
              <a  id="reportepdf2" href="" class="btn btn-default btn-block">Pdf 2</a>
            </div>
          </div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include("../footer4.php"); ?>
<script type="text/javascript">

function seleccionar_encabezado(){
  $('#table_datatable').DataTable()._fnDraw();
}


$(document).ready(function() { 
  $('.fancybox').fancybox( {topRatio:0,width:1000} );

  $("#encabezado").select2();
  var oTable = $('#table_datatable').DataTable({
            "bProcessing": true,
            "bServerSide": true,
                "bStateSave" : true,
                "sAjaxSource": "?accion=ajax", 
                "fnServerParams": function ( aoData ) {
                  aoData.push({ "name": "id_rm", "value": $("#encabezado").val()});
                },
                "sDom": "<'row'<'col-md-3 col-sm-12'l><'col-md-9 col-sm-12'f>r><'table-scrollable't><'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>",
                "iDisplayLength": 25,
              "sPaginationType": "bootstrap_extended",
              "aaSorting": [[ 1, "asc" ]], // Ordenar por columna 1 (Ficha) 
                "oLanguage": {
                  "sProcessing": "Cargando...",
                  "sSearch": "",
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
                "aoColumns": [ 
                  {"mData": "ficha"}, 
                  {"mData": "cedula"}, 
                  {"mData": "asistencias"}, 
                  {"mData": "rd"}, 
                  {"mData": "lt"}, 
                  {"mData": "domingos"}, 
                  {"mData": "feriados"}, 
                  {"mData": "libres"}, 
                  {"mData": "faltas"}, 
                  {"mData": "amonestaciones"}, 
                  {"mData": "h_totales"}, 
                  {"mData": "h_extras"}, 
                  {"mData": "h_domingos"}, 
                  {"mData": "h_feriados"}, 
                  {"mData": "f_domingos"}, 
                  {"mData": "f_feriados"}, 
                  {"mData": "monto_pagar"}, 
                  {"mData": "t"}, 
                  {"mData": "s"}
                ], 

                "aoColumnDefs": [
                  //{ 'bSortable':   false, 'aTargets': [0,34] }
                  { 'bSortable':   false, 'aTargets': [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18] }
                ],           

                "fnDrawCallback": function() {
                  $('#table_datatable_filter input').attr("placeholder", "Escriba frase para buscar");
                  createEvents();
                },
                initComplete: function(){
                  createEvents();
                }
            });           
           

        $('#table_datatable_wrapper .dataTables_filter input').addClass("form-control input-medium input-inline"); 
        $('#table_datatable_wrapper .dataTables_length select').addClass("form-control input-small"); 
        $('#table_datatable_wrapper .dataTables_length select').select2({
            showSearchInput : false //hide search box with special css class
        }); // initialize select2 dropdown

        $('#div_search_situ').insertBefore("#table_datatable_wrapper .dataTables_filter input");

        $('#table_datatable_wrapper .dataTables_filter input').after(' <a class="btn blue" id="btn-search"><i class="fa fa-search"></i> Buscar</a> ');

            
        $("#btn-search").click( function()
        {
           var valor_buscar =$('#search_situ').val();

           if( valor_buscar == 'Todos' )
           {
            valor_buscar = '';
           }

           // Se filtra por la columna 4 - Situación
           oTable.fnFilter( valor_buscar, 4 );
        });

        $("#btnBuscar").click(function(){
          var ficha          = $("#buscar_horas_ficha").val();
          var cedula         = $("#buscar_horas_cedula").val();
          var as             = $("#buscar_horas_as").val();
          var rd             = $("#buscar_horas_rd").val();
          var lt             = $("#buscar_horas_lt").val();
          var domingos       = $("#buscar_horas_domingos").val();
          var feriados       = $("#buscar_horas_feriados").val();
          var libres         = $("#buscar_horas_libres").val();
          var faltas         = $("#buscar_horas_faltas").val();
          var amonestaciones = $("#buscar_horas_amonestaciones").val();
          var h_totales      = $("#buscar_horas_totales").val();

          var buscar={
            ficha:          ficha,
            cedula:         cedula,
            as:             as,
            rd:             rd,
            lt:             lt,
            domingos:       domingos,
            feriados:       feriados,
            libres:         libres,
            faltas:         faltas,
            amonestaciones: amonestaciones,
            h_totales:       h_totales
          };

          oTable.fnPageChange("first",false);
          oTable.fnFilter( JSON.stringify(buscar), 0 , true);
        }); 

        $("#btnLimpiar").click(function(){
          $("#buscar_horas_ficha").val("");
          $("#buscar_horas_cedula").val("");
          $("#buscar_horas_as").val("");
          $("#buscar_horas_rd").val("");
          $("#buscar_horas_lt").val("");
          $("#buscar_horas_domingos").val("");
          $("#buscar_horas_feriados").val("");
          $("#buscar_horas_libres").val("");
          $("#buscar_horas_faltas").val("");
          $("#buscar_horas_amonestaciones").val("");
          $("#buscar_horas_totales").val("");

          oTable.fnPageChange("first",false);
          oTable.fnFilter( JSON.stringify([]), 0 , true);
        });

        function createEvents() {
          $('.modalreporte').off().on('click', function(){

            var url = $(this).attr('rel');
            $('#reportepdf').attr('href', url);
            var url = $(this).attr('rel2');
            $('#reportepdf2').attr('href', url);
            var url = $(this).attr('name');
            $('#reporteexcel').attr('href', url);
              $("#modal-reporte").modal('show');

          });
        }

});
</script>
<!-- manuel -->
<script type="text/javascript" src="../../reporte_pub/js/jquery.fancybox.pack.js"></script>
<link rel="stylesheet" type="text/css" href="../../reporte_pub/css/jquery.fancybox.css" media="screen" />
</body>
</html>