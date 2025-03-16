<?php 
require_once('../lib/database.php');
include("../lib/common.php");
include("../lib/pdfcommon.php");
include ("../header4.php");
include ("func_bd.php") ;
$db = new Database($_SESSION['bd']);

?>
<link href="../../includes/assets/css/custom-datatables.css" rel="stylesheet" type="text/css"/>
<?php include("../footer4.php"); ?>
<script type="text/javascript">

  function AbrirConstancia(ficha, nomina, constancia_id, _this)
  {
    if(constancia_id != '')
    {
      var select_id = "tipo_constancia"+ficha
      var sel      = document.getElementById(select_id);
      var selected = sel.options[sel.selectedIndex];
      var tipo     = selected.getAttribute('data-id');
      var archivo  = selected.getAttribute('data-arc');
        
      if(tipo=='pdf' && archivo=='2'){

        location.href='cartas_trabajo/constancia_pdf.php?ficha='+ficha+'&tipnom='+nomina+'&constancia_id='+constancia_id;
      }
      else if(tipo=='pdf' && archivo=='1')
      {
        location.href='cartas_trabajo/constancia_pdf2.php?ficha='+ficha+'&tipnom='+nomina+'&constancia_id='+constancia_id;
          
      }
      else if(tipo=='docx')
      {
        if (constancia_id=='4') {          
         location.href='../../reportes/word/constancia_de_trabajo_egresados.php?ficha='+ficha+'&tipnom='+nomina;
        }
        else {
          var html3 ='';
          html3 += '<div class="modal fade" id="filtro" tabindex="-1" role="basic" aria-hidden="true">';
          html3 += '    <div class="modal-dialog">';
          html3 += '        <div class="modal-content">';
          html3 += '            <div class="modal-header">';
          html3 += '                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>';
          html3 += '                <h4 class="modal-title">Seleccione una opción</h4>';
          html3 += '            </div>';
          html3 += '           <div class="modal-body">';
          html3 += '               <div class="row">';
          html3 += '                   <div class="col-md-2"></div>';
          html3 += '                    <div class="col-md-8" id="messages" role="alert">&nbsp;';
          html3 += '                    </div>';
          html3 += '                </div>';
          /*html3 += '                <div class="row">';
          html3 += '                    <div class="col-md-4 text-right">';
          html3 += '                        <label>Dirigido A:</label>';
          html3 += '                    </div>';
          html3 += '                    <div class="col-md-6">';
          html3 += '                        <input type="text" name="dirigido_a" id="dirigido_a"  class="form-control">';
          html3 += '                    </div>';
          html3 += '                </div>';
          html3 += '                <div class="row">';
          html3 += '                    <div class="col-md-4 text-right">&nbsp;';
          html3 += '                    </div>';
          html3 += '                </div>';
          html3 += '                <div class="row">';
          html3 += '                    <div class="col-md-4 text-right">';
          html3 += '                        <label>Prestamos detallados</label>';
          html3 += '                    </div>';
          html3 += '                    <div class="col-md-6">';
          html3 += '                      <input type="checkbox" name="prestamo_detallado" id="prestamo_detallado" value="1" /> ';
          html3 += '                    </div>';
          html3 += '                </div>';
          html3 += '                <div class="row">';
          html3 += '                    <div class="col-md-4 text-right">&nbsp;';
          html3 += '                    </div>';
          html3 += '                </div>';
          html3 += '                <div class="row">';
          html3 += '                    <div class="col-md-4 text-right">';
          html3 += '                        <label>Prestamos Empresa</label>';
          html3 += '                    </div>';
          html3 += '                    <div class="col-md-6">';  
          html3 += '                      <label class="mt-checkbox mt-checkbox-outline"> ';
          html3 += '                      <input type="checkbox" name="prestamo_empresa" id="prestamo_empresa" value="1" /> ';
          html3 += '                      </label>';
          html3 += '                    </div>';
          html3 += '                </div>';*/
          html3 += '           </div>';
          html3 += '           <div class="modal-footer">';
          html3 += '               <button type="button" class="btn dark btn-outline" data-dismiss="modal">Cerrar</button>';
          html3 += '               <button type="button" class="btn blue" id="btnGenerar">Generar</button>';
          html3 += '           </div>';
          html3 += '       </div>';
          html3 += '       <!-- /.modal-content -->';
          html3 += '   </div>';
          html3 += '   <!-- /.modal-dialog -->';
          html3 += '</div>';
          html3 += '<!-- /.modal -->';
          $("#modal").empty();
          $("#modal").append(html3);
          $('#filtro').modal('show');
          $("#btnGenerar").off("click").on("click", function(){
            //Nullish coalescing operator (??)
            var dirigido_a = $("#dirigido_a").val() ?? '';
            var prestamo_detallado = $("#prestamo_detallado").is(':checked') ?? '';
            var prestamo_empresa = $("#prestamo_empresa").is(':checked') ?? '';
            location.href='cartas_trabajo/'+archivo+'?ficha='+ficha+'&tipnom='+nomina;
            if(nomina==1)
              location.href='cartas_trabajo/carta_trabajo_administrativo.php?ficha='+ficha+'&tipnom='+nomina+'&dirigido_a='+dirigido_a+'&prestamo_detallado='+prestamo_detallado+'&prestamo_empresa='+prestamo_empresa;
            else
              location.href='cartas_trabajo/carta_trabajo_bisemanal.php?ficha='+ficha+'&tipnom='+nomina+'&dirigido_a='+dirigido_a+'&prestamo_detallado='+prestamo_detallado+'&prestamo_empresa='+prestamo_empresa;
            

          });     
        }          
      }
      else
      {
        location.href='cartas_trabajo/constancia_pdf.php?ficha='+ficha+'&tipnom='+nomina+'&constancia_id='+constancia_id;
      }
    }
  }
  function AbrirContrato(tipo_contrato, ficha, planilla){
    var html ='';
    html += '<div class="modal fade" id="filtro" tabindex="-1" role="basic" aria-hidden="true">';
    html += '    <div class="modal-dialog">';
    html += '        <div class="modal-content">';
    html += '            <div class="modal-header">';
    html += '                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>';
    html += '                <h4 class="modal-title">Seleccione un año</h4>';
    html += '            </div>';
    html += '           <div class="modal-body">';
    html += '               <div class="row">';
    html += '                   <div class="col-md-2"></div>';
    html += '                    <div class="col-md-8" id="messages" role="alert">&nbsp;';
    html += '                    </div>';
    html += '                </div>';
    html += '                <div class="row">';
    html += '                    <div class="col-md-12">';
    html += '                        <div id="anioList"></div>';
    html += '                    </div>';
    html += '                </div>';
    html += '           </div>';
    html += '           <div class="modal-footer">';
    html += '               <button type="button" class="btn dark btn-outline" data-dismiss="modal">Cerrar</button>';
    html += '               <button type="button" class="btn blue" id="btnImprimir">Imprimir</button>';
    html += '           </div>';
    html += '       </div>';
    html += '       <!-- /.modal-content -->';
    html += '   </div>';
    html += '   <!-- /.modal-dialog -->';
    html += '</div>';
    html += '<!-- /.modal -->';
    let anio_actual = $('#anio_actual').val();
    var html2 ='';
    html2 += '<div class="modal fade" id="filtro" tabindex="-1" role="basic" aria-hidden="true">';
    html2 += '    <div class="modal-dialog">';
    html2 += '        <div class="modal-content">';
    html2 += '            <div class="modal-header">';
    html2 += '                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>';
    html2 += '                <h4 class="modal-title">Seleccione un año</h4>';
    html2 += '            </div>';
    html2 += '           <div class="modal-body">';
    html2 += '               <div class="row">';
    html2 += '                   <div class="col-md-2"></div>';
    html2 += '                    <div class="col-md-8" id="messages" role="alert">&nbsp;';
    html2 += '                    </div>';
    html2 += '                </div>';
    html2 += '                <div class="row">';
    html2 += '                    <div class="col-md-4 text-right">';
    html2 += '                        <label>Año</label>';
    html2 += '                    </div>';
    html2 += '                    <div class="col-md-3">';
    html2 += '                        <input type="number" name="anio_acum" id="anio_acum" type="number"  class="form-control">';
    html2 += '                    </div>';
    html2 += '                </div>';
    html2 += '           </div>';
    html2 += '           <div class="modal-footer">';
    html2 += '               <button type="button" class="btn dark btn-outline" data-dismiss="modal">Cerrar</button>';
    html2 += '               <button type="button" class="btn blue" id="btnImprimir">Imprimir</button>';
    html2 += '           </div>';
    html2 += '       </div>';
    html2 += '       <!-- /.modal-content -->';
    html2 += '   </div>';
    html2 += '   <!-- /.modal-dialog -->';
    html2 += '</div>';
    html2 += '<!-- /.modal -->';
    //console.log("Tipo de contrato: " + tipo_contrato + " Ficha: " + ficha + " Planilla: " + planilla);

    if(tipo_contrato=='Indefinido'){
      location.href='cartas_trabajo/contrato_indefinido.php?ficha='+ficha+'&tipnom='+planilla;
    }if(tipo_contrato=='Definido'){
      location.href='cartas_trabajo/contrato_definido.php?ficha='+ficha+'&tipnom='+planilla;
    }if(tipo_contrato=='Nombramiento'){
      location.href='cartas_trabajo/nombramiento_minsa_pdf.php?ficha='+ficha+'&tipnom='+planilla;
    }if(tipo_contrato=='Resuelto'){
      location.href='cartas_trabajo/resuelto_minsa_pdf.php?ficha='+ficha+'&tipnom='+planilla;
    }if(tipo_contrato=='Resuelto_g'){
      location.href='../../reportes/config_resueltogeneral.php';
    }if(tipo_contrato=='ajust_h'){
      location.href='../../reportes/word/ajuste_hombre.php?ficha='+ficha+'&tipnom='+planilla;
    }if(tipo_contrato=='ajust_m'){
      location.href='../../reportes/word/ajuste_mujer.php?ficha='+ficha+'&tipnom='+planilla;
    }if(tipo_contrato=='modelo_h'){
      location.href='../../reportes/word/modelo_h.php?ficha='+ficha+'&tipnom='+planilla;
    }if(tipo_contrato=='decreto_g'){
      location.href='../../reportes/config_decretogeneral.php';
    }if(tipo_contrato=='expediente'){
      location.href='../../reportes/pdf/expediente.php?ficha='+ficha+'&tipnom='+planilla;
    }if(tipo_contrato=='decreto'){
      location.href='../../reportes/word/decreto_asamblea.php?ficha='+ficha+'&tipnom='+planilla+'&tipo_contrato='+tipo_contrato;
    }if(tipo_contrato=='toma_posicion'){
      location.href='../../reportes/word/toma_posesion_asamblea.php?ficha='+ficha+'&tipnom='+planilla;
    }if(tipo_contrato=='nota_contaloria'){
      location.href='../../reportes/form_rpt_nota_contraloria.php?ficha='+ficha+'&tipnom='+planilla;
    }if(tipo_contrato=='carta_estado_gravidez'){
      location.href='../../reportes/word/carta_estado_gravidez.php?ficha='+ficha+'&tipnom='+planilla;
    }if(tipo_contrato=='contrato_de_trabajo_administrativo'){
      location.href='../../reportes/word/contrato_de_trabajo_administrativo.php?ficha='+ficha+'&tipnom='+planilla;
    }if(tipo_contrato=='contrato_de_trabajo_anfitrion'){
      location.href='../../reportes/word/contrato_de_trabajo_anfitrion.php?ficha='+ficha+'&tipnom='+planilla;
    }if(tipo_contrato=='contrato_de_trabajo_asesor_de_ventas'){
      location.href='../../reportes/word/contrato_de_trabajo_asesor_de_ventas.php?ficha='+ficha+'&tipnom='+planilla;
    }if(tipo_contrato=='contrato_de_trabajo_asistente_de_jefe_de_unidad'){
      location.href='../../reportes/word/contrato_de_trabajo_asistente_de_jefe_de_unidad.php?ficha='+ficha+'&tipnom='+planilla;
    }if(tipo_contrato=='contrato_de_trabajo_jefe_de_unidad'){
      location.href='../../reportes/word/contrato_de_trabajo_jefe_de_unidad.php?ficha='+ficha+'&tipnom='+planilla;
    }if(tipo_contrato=='contrato_jefe_de_unidad_pjv'){
      location.href='../../reportes/word/contrato_jefe_de_unidad_pjv.php?ficha='+ficha+'&tipnom='+planilla;
    }if(tipo_contrato=='contrato_asesor_de_venta_pjv'){
      location.href='../../reportes/word/contrato_asesor_de_venta_pjv.php?ficha='+ficha+'&tipnom='+planilla;
    }if(tipo_contrato=='contrato_asistente_jefe_de_unidad_pjv'){
      location.href='../../reportes/word/contrato_asistente_jefe_de_unidad_pjv.php?ficha='+ficha+'&tipnom='+planilla;
    }if(tipo_contrato=='contrato_de_agente_de_Atencion_pjv'){
      location.href='../../reportes/word/contrato_de_agente_de_Atencion_pjv.php?ficha='+ficha+'&tipnom='+planilla;
    }if(tipo_contrato=='contrato_de_trabajo_administrativo_pjv'){
      location.href='../../reportes/word/contrato_de_trabajo_administrativo_pjv.php?ficha='+ficha+'&tipnom='+planilla;
    }if(tipo_contrato=='constancia_de_trabajo_egresados'){
      location.href='../../reportes/word/constancia_de_trabajo_egresados.php?ficha='+ficha+'&tipnom='+planilla;
    }if(tipo_contrato=='expediente_asamblea'){
      location.href='../../reportes/pdf/expediente.php?ficha='+ficha+'&tipnom='+planilla;
    }if(tipo_contrato=='contrato_administrativo')
    {
      
      location.href='cartas_trabajo/contrato_administrativo_quincenal.php?ficha='+ficha+'&tipnom='+planilla;
    }
    if(tipo_contrato=='contrato_ayudante')
    {
      location.href='cartas_trabajo/contrato_ayudante.php?ficha='+ficha+'&tipnom='+planilla;
    }
    if(tipo_contrato=='contrato_electrico')
    {
      location.href='cartas_trabajo/contrato_electrico.php?ficha='+ficha+'&tipnom='+planilla;
    }
    if (tipo_contrato=='carta_de_maternidad') {
      location.href='../../reportes/word/carta_de_maternidad.php?ficha='+ficha+'&tipnom='+planilla;
    }
    if(tipo_contrato=='contrato_principiante')
    {
      location.href='cartas_trabajo/contrato_principiante.php?ficha='+ficha+'&tipnom='+planilla;
    }
    if(tipo_contrato=='carta_terminacion_obra')
    {
      location.href='cartas_trabajo/carta_terminacion_obra.php?ficha='+ficha+'&tipnom='+planilla;
    }
    if(tipo_contrato=='carta_ficha_itesa')
    {
      /*$("#modal").empty();
      $("#modal").append(html);
      $('#filtro').modal('show');
      $.get("ajax/obtenerAnioPlanilla.php",function(res){
        $("#anioList").empty();
        $("#anioList").append(res);
        $("#btnImprimir").on("click",function(res){
          anio = $("select#anio").val();
          console.log(anio);
          if(anio !== "")
          {
            location.href='cartas_trabajo/carta_ficha_itesa.php?ficha='+ficha+'&tipnom='+planilla+'&anio='+anio;
          }
          else{
            alert("Por favor, seleccione un Año");
          }
        });
      });*/
      //
      
      location.href='cartas_trabajo/carta_ficha_itesa.php?ficha='+ficha+'&tipnom='+planilla;
    }
    if(tipo_contrato=='carta_ficha_panaconstruct')
    {

      
      location.href='cartas_trabajo/carta_ficha_panaconstruct.php?ficha='+ficha+'&tipnom='+planilla;
    }
    if(tipo_contrato=='carta_certificacion_isr')
    {
      $("#modal").empty();
      $("#modal").append(html3);
      $('#filtro').modal('show');
      $.get("ajax/obtenerAnioPlanilla.php",function(res){
        $("#anioList").empty();
        $("#anioList").append(res);
        $("#btnImprimir").on("click",function(res){
          anio_acum = $("#anio_acum").val();
          var inpObj = document.getElementById("anio_acum");
          console.log(anio_acum);
          
          if(anio_acum !== "" && inpObj.checkValidity())
          {
            location.href='cartas_trabajo/carta_certificacion_isr.php?ficha='+ficha+'&tipnom='+planilla+'&anio='+anio_acum;
          }
          else{
            alert("Por favor, seleccione un Año");
          }
        });
      });
      
    }
    if(tipo_contrato=='carta_certificacion_isr')
    {
      $("#modal").empty();
      $("#modal").append(html2);
      $('#filtro').modal('show');
      $.get("ajax/obtenerAnioPlanilla.php",function(res){
        $("#anioList").empty();
        $("#anioList").append(res);
        $("#btnImprimir").on("click",function(res){
          anio_acum = $("#anio_acum").val();
          var inpObj = document.getElementById("anio_acum");
          console.log(anio_acum);
          
          if(anio_acum !== "" && inpObj.checkValidity())
          {
            location.href='cartas_trabajo/carta_certificacion_isr.php?ficha='+ficha+'&tipnom='+planilla+'&anio='+anio_acum;
          }
          else{
            alert("Por favor, seleccione un Año");
          }
        });
      });
      
    }
  }
</script>
<input type="hidden" name="anio_actual" id="anio_actual" value="<?= date('Y'); ?>">
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
                Personal
              </div>
            </div>
            <div class="portlet-body">
              <!-- <div class="table-container"> -->
              <form action="" method="post" name="frmPrincipal" id="frmPrincipal">
              <table class="table table-striped table-bordered table-response" id="table_datatable">
              <thead>
              <tr>
               <th>Foto Cedula</th>
                <th>C.I.</th>
                <th># Colab.</th>                
                <th>Nombres</th>
                <th>Apellidos</th>
                <th>Situaci&oacute;n</th>    
                <th>Planilla</th>   
                <th>Constancia</th>
                <th>Documentos</th>
              </tr>
              </thead>
              <tbody>
              </tbody>
              </table>
              </form>
              <!-- </div> -->
            </div>
          </div>
          <!-- END EXAMPLE TABLE PORTLET-->
        </div>
      </div>
      <!-- END PAGE CONTENT-->
<div id="modal"></div>
    </div>
  </div>
  <!-- END CONTENT -->
  
</div>
<script type="text/javascript">

   $(document).ready(function() { 
    //$('#table_datatable').DataTable(); 

            // begin first table
            $('#table_datatable').DataTable({
              "bProcessing": true,
              "bServerSide": true,
              "sAjaxSource": "ajax/server_processing_listado_documentos.php", 
              //"oSearch": {"sSearch": "Escriba frase para buscar"},
              "iDisplayLength": 10,
                //"sPaginationType": "bootstrap",
              "sPaginationType": "bootstrap_extended",
              "aaSorting": [[1, 'asc'], [ 3, "asc" ]],  
              //"sPaginationType": "full_numbers",
                "oLanguage": {
                    "sSearch": "<img src='../../includes/imagenes/icons/magnifier.png' width='16' height='16' > Buscar:",
                    "sLengthMenu": "Mostrar _MENU_",
                    "sInfoEmpty": "",
                    "sInfo":"Total _TOTAL_ registros",
                    "sInfoFiltered": "",
                    "sEmptyTable":  "No hay datos disponibles",//"No data available in table",
                    "sZeroRecords": "No se encontraron registros",//"No matching records found",
                    "oPaginate": 
                    {
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
                    { 'bSortable': false, 'aTargets': [7] },
                    { "bSearchable": false, "aTargets": [ 7 ] },
                    { 'bSortable': false, 'aTargets': [8] },
                    { "bSearchable": false, "aTargets": [ 8 ] },
                    { "sWidth": "8%", "aTargets": [0] },
                    { "sWidth": "8%", "aTargets": [1] },                   
                    { "sWidth": "8%", "aTargets": [2] },
                    { "sWidth": "15%", "aTargets": [3] },
                    { "sWidth": "8%", "aTargets": [4] },
                    { "sWidth": "8%", "aTargets": [5] },
                    { "sWidth": "8%", "aTargets": [6] }
                ],
         "fnDrawCallback": function() {
                $('#table_datatable_filter input').attr("placeholder", "Escriba frase para buscar");
                $("td#tipo_contrato").each(function () {
                    // Definimos una variable valor usando como dato el atributo title
                    var ficha = $(this).data("ficha");
                    // ejecutamos la función click sobr el elemento que estamos clickando
                    $(this).on("click", function () {
                        console.log(id+" "+ficha);
                    });
                });
         }
            });

            $('#table_datatable').on('change', 'tbody tr .checkboxes', function(){
                 $(this).parents('tr').toggleClass("active");
            });

            $('#table_datatable_wrapper .dataTables_filter input').addClass("form-control input-medium input-inline"); 
            $('#table_datatable_wrapper .dataTables_length select').addClass("form-control input-xsmall"); 
 
   });
</script>
<!-- manuel -->
<script type="text/javascript" src="../../reporte_pub/js/jquery.fancybox.pack.js"></script>
<link rel="stylesheet" type="text/css" href="../../reporte_pub/css/jquery.fancybox.css" media="screen" />
<script>
    $(document).ready(function() {
        $('.fancybox').fancybox( {topRatio:0,width:1000} );
    });
</script>
</body>
</html>