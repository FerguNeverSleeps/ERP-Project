<?php 
if (!isset($_SESSION)) {
    session_start();
    ob_start();
}

require_once '../lib/common.php';
include ("func_bd.php");
error_reporting(E_ALL ^ E_DEPRECATED);
$conexion=conexion();
?>
<?php include("../header4.php"); ?>
<link href="../../includes/assets/css/custom-datatables.css" rel="stylesheet" type="text/css"/>
<style type="text/css">
.portlet > .portlet-title > .caption {
    margin-bottom: 3px;
}

#table_result2_length .form-control {
    padding: 6px 5px;
}

#table_result2_length .input-xsmall {
    width: 77px !important;
}

.text-center{
	text-align: center;
}
</style>
<div class="page-container">
	<!-- BEGIN CONTENT -->
	<div class="page-content-wrapper">
		<div class="page-content">
			<!-- BEGIN PAGE CONTENT-->
			<div class="row">
				<div class="col-md-12">
					<!-- BEGIN EXAMPLE TABLE PORTLET-->
					<div class="portlet box blue">
						<div class="portlet-title" style="padding-top: 5px;">
							<div class="caption" style="width: 100%;">
								<i class="fa fa-cogs" style="margin-top: 0px;"></i> Ejecutar Horarios Rotativos
							</div>
						</div>
						<div class="portlet-body" id="blockui_portlet_body">
							<h4 class="block" style="padding-top: 0px;">Rotaci&oacute;n Manual:</h4>

							<div class="note note-info">
								<p>
								 Esta opci&oacute;n permite ejecutar manualmente la rotaci&oacute;n de turnos de acuerdo a la
								 configuraci&oacute;n de horarios registrada en el sistema.</p>
							</div>

							<form id="formPrincipal" name="formPrincipal" method="post" role="form" style="margin-bottom: 5px;">
									<button type="button" class="btn btn-sm blue active" id="btn-ejecutar" name="btn-ejecutar">Ejecutar</button>&nbsp;
									<button type="button" class="btn btn-sm default active" 
									        onclick="javascript: document.location.href='horarios_rotativos.php'">Cancelar</button>
							</form>

							

							<div id="div-result" class="clearfix" style="display: none">
								<div class="alert" style="padding: 8px 15px; margin-top:30px"></div>
								<!-- <h4 class="block" style="padding-top: 0px;">Resultado:</h4> -->
								<div class="panel" style="display: none">
									<!--<div class="panel-heading">
										 Resultado:
									</div>
									-->
									<!-- Table -->
									<!--
									<div class="table-responsive" style="max-height: 560px; overflow-y:auto">
									<table class="table table-bordered table-striped table-hover" id="table_result" style="margin-bottom: 0px !important">
										<thead>
											<tr>
												<th style="text-align: center">Rotaci&oacute;n</th>
												<th style="text-align: center">Ficha</th>
												<th style="text-align: center">Trabajador</th>
												<th style="text-align: center">Turno Anterior</th>
												<th style="text-align: center">Turno Actual</th>
											</tr>
										</thead>
										<tbody id="tbody_result">									
										</tbody>
									</table>
									</div>
									-->
									<table class="table table-bordered table-striped table-hover" id="table_result2">
										<thead>
											<tr>
												<th style="text-align: center">Rotaci&oacute;n</th>
												<th style="text-align: center">Ficha</th>
												<th style="text-align: center">Trabajador</th>
												<th style="text-align: center">Turno Anterior</th>
												<th style="text-align: center">Turno Actual</th>
											</tr>
										</thead>
										<tbody id="tbody_result2">									
										</tbody>
									</table>
								</div>
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
<script src="../../includes/assets/plugins/jquery-validation/dist/jquery.validate.min.js" type="text/javascript"></script>
<script src="../../includes/assets/scripts/custom/ui-blockui.js"></script>
<script type="text/javascript">
jQuery(document).ready(function() {

    $('#table_result2').dataTable({
    	"aaSorting": [],
        "aLengthMenu": [ // set available records per page
            [5, 10, 25, 50,  -1],
            [5, 10, 25, 50, "Todos"]
        ],   
        // set the initial value
        "iDisplayLength": 10,
        "sPaginationType": "bootstrap_extended", 
        "oLanguage": {
            // "sSearch": "<img src='../../includes/imagenes/icons/magnifier.png' width='16' height='16' > Buscar:",
            "sSearch": "Buscar:",
            "sLengthMenu": "Mostrar _MENU_",
            "sInfoEmpty": "",
            //"sInfo":"Total _TOTAL_ registros",
            "sInfo":"",
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
        "aoColumnDefs": [
        	{ "sClass": "text-center", "aTargets": [ 0 ] },
        	{ "sClass": "text-center", "aTargets": [ 1 ] },
        	{ "sClass": "text-center", "aTargets": [ 2 ] },
        	{ "sClass": "text-center", "aTargets": [ 3 ] },
        	{ "sClass": "text-center", "aTargets": [ 4 ] }
          //  { 'bSortable': false, 'aTargets': [ 2 ] },
          //  { "bSearchable": false, "aTargets": [ 2 ] },
          //  { "sWidth": "8%", "aTargets": [ 5 ] },
        ],

        "fnDrawCallback": function() {
              $('#table_result2_filter input').attr("placeholder", "Escriba frase para buscar");
        }
    });

    $('#table_result2_wrapper .dataTables_filter input').addClass("form-control input-medium input-inline"); 
    $('#table_result2_wrapper .dataTables_length select').addClass("form-control input-xsmall"); 

	$('#btn-ejecutar').click(function(){

		if( confirm("\u00BFEst\u00E1 seguro de querer ejecutar la rotaci\u00F3n de turnos?") )
		{

	    	$("#div-result").hide();

		    App.blockUI({
		        target: '#blockui_portlet_body',
		        boxed: true,
		        message: 'Procesando...',
		        // textOnly: true,
		        // overlayColor: 'none'
		    });

		    var class_success = "alert-info";
		    var class_error   = "alert-danger";

	        $.ajax({
	            url:  'cron_horarios_rotativos_ajax.php',
	            dataType: "json",
	            success: function(data)
	            {	
	                // console.log('Resultado: ');
	                // console.log(data);

	                if(data.success)
	                {
	                	$("#div-result .alert").removeClass( class_error ).addClass( class_success );
	                	$("#div-result .alert").text("Rotación de turnos realizada correctamente");
	                	$("#div-result .panel").show();

	                	//var tbody = document.getElementById("tbody_result"); 
	                	//$("#tbody_result").empty();

	                	var oTable = $('#table_result2').dataTable();
	                	oTable.fnClearTable();

						$.each(data.empleado, function( indice, empleado ) 
						{
							//console.log("Indice: "+indice+" / Empleado: " + empleado['ficha']);
	 			
	 						/*
	 							var rowCount = tbody.rows.length;
					            var row      = tbody.insertRow(rowCount);

					            var celda1   = row.insertCell(0);
					            var celda2   = row.insertCell(1);
					            var celda3   = row.insertCell(2);
					            var celda4   = row.insertCell(3); 
					            var celda5   = row.insertCell(4);

					            celda1.innerHTML = empleado['descripcion'];
					            celda2.innerHTML = empleado['ficha'];
					            celda3.innerHTML = empleado['nombre'];
					            celda4.innerHTML = empleado['desc_turno_anterior'];
					            celda5.innerHTML = empleado['desc_turno_actual']; 

					            celda1.style.cssText = 'text-align: center';
					            celda2.style.cssText = 'text-align: center';
					            celda3.style.cssText = 'text-align: center';
					            celda4.style.cssText = 'text-align: center';
					            celda5.style.cssText = 'text-align: center';
					        */

								$('#table_result2').dataTable().fnAddData( [
								        empleado['descripcion'],
								        empleado['ficha'],
								        empleado['nombre'],
								        empleado['desc_turno_anterior'],
								        empleado['desc_turno_actual']
								] );

	                    });

	                }
	                else
	                {
	                	$("#div-result .alert").removeClass( class_success ).addClass( class_error );
	                	$("#div-result .alert").text("¡Error al realizar la rotación de turnos!");
	                }

		            window.setTimeout(function () {
		                App.unblockUI('#blockui_portlet_body');
		                $("#div-result").show();
		            }, 2000);  

	            },
	            error: function(data)
	            {
	                console.log('Resultado Error: ');
	                console.log(data);

	         	    $("#div-result .alert").removeClass( class_success ).addClass( class_error );
	                $("#div-result .alert").text("¡Ocurrió un error al realizar la petición!");

	                App.unblockUI('#blockui_portlet_body');
	                $("#div-result").show();
	            }
	        });

		}

	});

});
</script>
</body>
</html>