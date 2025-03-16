<?php 
require_once('../lib/database.php');

$db = new Database($_SESSION['bd']);	
?>

<link href="../../estructura/public/tema/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
<link href="../../estructura/public/tema/bootstrap/css/bootstrap.min.css" rel="stylesheet"  type="text/css">
<link href="../../estructura/public/tema/bootstrap/css/dataTables.bootstrap.min.css" rel="stylesheet" type="text/css">
<link href="../../estructura/public/tema/uniform/css/uniform.default.min.css" rel="stylesheet" type="text/css">
<!-- END GLOBAL MANDATORY STYLES -->
<!-- BEGIN PAGE LEVEL STYLES -->
<link href="../../estructura/public/tema/select2/select2.css" rel="stylesheet" type="text/css">
<link href="../../estructura/public/tema/select2/select2-metronic.css" rel="stylesheet" type="text/css">
<link href="../../estructura/public/tema/data-tables/DT_bootstrap.css" rel="stylesheet" type="text/css">
<!-- END PAGE LEVEL STYLES -->
<!-- BEGIN THEME STYLES -->
<link href="../../estructura/public/tema/css/components.css" rel="stylesheet" type="text/css">
<link href="../../estructura/public/tema/css/layout.css" rel="stylesheet" type="text/css">
<link href="../../estructura/public/tema/css/style-metronic.css" rel="stylesheet" type="text/css">
<link href="../../estructura/public/tema/css/style.css" rel="stylesheet" type="text/css">
<link href="../../estructura/public/tema/css/style-responsive.css" rel="stylesheet" type="text/css">
<link href="../../estructura/public/tema/css/plugins.css" rel="stylesheet" type="text/css">
<!-- <link href="../../../includes/assets/css/themes/default.css" rel="stylesheet" type="text/css" id="style_color"/> -->
<!-- END THEME STYLES -->
<link href="../../estructura/public/tema/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet" type="text/css">
<link href="../../estructura/public/tema/bootstrap-colorpicker/css/colorpicker.css" rel="stylesheet" type="text/css">
<link href="../../estructura/public/tema/bootstrap-daterangepicker/daterangepicker-bs3.css" rel="stylesheet" type="text/css">
<link href="../../estructura/public/tema/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" type="text/css" href="../../includes/assets/plugins/jquery-multi-select/css/multi-select.css"/>
<style>
.form-horizontal .control-label {
    text-align: center;
    padding-top: 7px;
}
.portlet-title{
	padding-right: 5px !important;
}

label.error {
    color: #b94a48;
}

.margin-top-20{
	margin-top: 20px
}

.text-left
{
	text-align: left !important;
}

.ms-container {
    width: 100%;
}

.ms-container .ms-list {
    height: 200px;
}

.ms-container .ms-selectable li.ms-elem-selectable, .ms-container .ms-selection li.ms-elem-selection {
    cursor: pointer;
}
</style>
<div class="page-container">
	<!-- BEGIN PAGE CONTENT-->
	<div class="row" id="div_buscador">
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<!-- BEGIN EXAMPLE TABLE PORTLET-->
			<div class="portlet box blue">
				<div class="portlet-title">
					<div class="caption">
						BÚSQUEDA AVANZADA
					</div>
					<div class="actions">
						<a class="btn btn-sm blue"  onclick="javascript: window.location='elegibles_list.php?modulo=175';">
							<i class="fa fa-arrow-left"></i> Regresar
						</a>				
					</div>
				</div>
				<div class="portlet-body form" id="blockui_portlet_body">
                <br>
				<form class="form-horizontal" id="form1" name="form1" method="post">
					<div class="form-body">
						<div class="row">
							<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">Rango de edad</div>
							<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 text-center">
							<input type="number" class="form-control" name="edad1" id="edad1" min="10" max="100" value="0" placeholder="Entre">
							</div>
							<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 text-center">
							<input type="number" class="form-control" name="edad2" id="edad2" min="10" max="100" value="0" placeholder="Y">
							</div>
						</div>
                        <br>
						<div class="row">
							<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">Estado Civil</div>
							<div class="col-xs-9 col-sm-9 col-md-9 col-lg-9">
							<?php
								$data = array('Soltero/a', 'Casado/a', 'Viudo/a', 'Divorciado/a', 'Unido');
							?>
							<select name="estado_civil" id="estado_civil" class="form-control select2">
									<option value=''>Seleccione el estado civil...</option>
								<?php

									foreach ($data as $estado_civil) 
									{
										echo "<option value='".$estado_civil."'>".$estado_civil."</option>";
									}
								?>
							</select>
							</div>
						</div>
                        <br>
						<div class="row">
							<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">Género</div>
							<div class="col-xs-9 col-sm-9 col-md-9 col-lg-9">
							<select name="genero" id="genero" class="form-control">
								<option value="">Seleccione el género...</option>
								<option value="Masculino">Masculino</option>
								<option value="Femenino">Femenino</option>
							</select>
							</div>
						</div>
                        <br>
                        <div class="row">
							<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">Profesión</div>
							<div class="col-xs-9 col-sm-9 col-md-9 col-lg-9">
							<select name="cod_profesion" id="cod_profesion" class="form-control">
				        	</select>
							</div>
						</div>
                        <br>
                        <div class="row">
							<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">Grado de Instrucción</div>
							<div class="col-xs-9 col-sm-9 col-md-9 col-lg-9">
							<select name="gradInst" id="gradInst" class="form-control">										
							</select>
							</div>
						</div>
                        <br>
						<div class="row">
							<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">Área de Desempeño</div>
							<div class="col-xs-9 col-sm-9 col-md-9 col-lg-9">
							<select name="areaDesem" id="areaDesem" class="form-control">
								
							</select>
							</div>
						</div>
                        <br>
						<div class="row">
							<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">Años de experiencia</div>
							<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 text-center">
							<input type="number" class="form-control" name="a_exp" min="0" max="100" value="0" placeholder="Años de experiencia">
							</div>
						</div>
					</div>

					<div class="form-actions fluid">
						<div class="row">
							<div class="col-md-11 text-center">
								<button type="button" class="btn btn-sm blue active" id="btn-buscar"><i class="fa fa-search"></i> Buscar</button>
							</div>
						</div>
					</div>

				</form>

				</div>
			</div>
			<!-- END EXAMPLE TABLE PORTLET-->
		</div>
	</div>
	<!-- END PAGE CONTENT-->

</div>

	<div class="row" id="div_tabla">
		<div class="col-md-12">
			<!-- BEGIN EXAMPLE TABLE PORTLET-->
			<div class="portlet box blue">
				<div class="portlet-title">
					<div class="caption">
					   ELEGIBLES
					</div>
					<div class="actions">
						<a class="btn btn-sm grey"  id="rpt-pdf">
							<i class="fa fa-file-excel-o"></i> Excel
						</a>
						<a class="btn btn-sm red"  id="rpt-pdf">
							<i class="fa fa-file-pdf-o"></i> PDF
						</a>
						<a class="btn btn-sm blue-hoki"  onclick="javascript: window.location='config_rpt_funcionarios_orden_alfabetico.php'">
							<i class="fa fa-arrow-left"></i> Regresar
						</a>
					</div>
				</div>
				<div class="portlet-body" id="tab-id">
					<form action="" method="post" name="frmPrincipal" id="frmPrincipal">
					<table class="table table-striped table-bordered table-hover" id="tablX">
					<thead>
					<tr>
						<th>Cédula</th>
						<th>Nombre</th>
						<th>Apellidos</th>
						<th>Fec. Nac.</th>
						<th>Profesión</th>
						<th>Área de Desempeño</th>
						<th>Grado Instrucción</th>
						<th>Años Experiencia</th>
						<th>Género</th>
						<th>Teléfono</th>
						<th>Correo</th>
					</tr>
					</thead>
					<tbody></tbody>
					
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
<script type="text/javascript" src="../../estructura/public/tema/bootstrap/js/jquery.min.js"></script>
<script type="text/javascript" src="../../estructura/public/tema/bootstrap/js/bootstrap.min.js"></script>
<script type="text/javascript" src="../../estructura/public/tema/data-tables/all.min.js"></script>
<script type="text/javascript" src="../../estructura/public/tema/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
<script type="text/javascript" src="../../estructura/public/tema/bootstrap-datepicker/js/locales/bootstrap-datepicker.es.js"></script>
<script type="text/javascript" src="../../estructura/public/tema/bootstrap-daterangepicker/moment.min.js"></script>
<script type="text/javascript" src="../../estructura/public/tema/bootstrap-colorpicker/js/bootstrap-colorpicker.js"></script>
<script type="text/javascript" src="../../estructura/public/tema/bootstrap-daterangepicker/daterangepicker.js"></script>
<script type="text/javascript" src="../../estructura/public/tema//bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"></script>
<script type="text/javascript" src="../../estructura/public/tema/jquery-validation/dist/jquery.validate.min.js"></script>
<script type="text/javascript" src="../../estructura/public/tema/jquery-validation/localization/messages_es.js"></script>
<script src="../../includes/assets/plugins/jquery-multi-select/js/jquery.multi-select.js"></script>
<script src="../../includes/assets/plugins/jquery-validation/dist/additional-methods.min.js"></script>
<script>
$(document).ready(function()
{
	$("#div_tabla").hide();
	$("#div_buscador").show();

	$.get("ajax/getProfesiones.php",function(res){
		$("#cod_profesion").empty().append(res);

	});
	$.get("ajax/getGradoInst.php",function(res){
		$("#gradInst").empty().append(res);

	});

	$.get("ajax/getAreaDesemp.php",function(res){
		$("#areaDesem").empty().append(res);

	});

        localStorage.setItem("edad1","" );
        localStorage.setItem("edad2","" );
        localStorage.setItem("estado_civil","" );
        localStorage.setItem("genero","" );
        localStorage.setItem("cod_profesion","" );
        localStorage.setItem("gradInst","" );
        localStorage.setItem("areaDesem","" );
        localStorage.setItem("a_exp","" );
	$("#btn-buscar").on("click",function()
	{
		var edad1         = $("#edad1").val();
		var edad2         = $("#edad2").val();
		var estado_civil  = $("#estado_civil").val();
		var genero        = $("#genero").val();
		var cod_profesion = $("#cod_profesion").val();
		var gradInst      = $("#gradInst").val();
		var areaDesem     = $("#areaDesem").val();
		var a_exp         = $("#a_exp").val();
        localStorage.setItem("edad1", edad1);
        localStorage.setItem("edad2", edad2);
        localStorage.setItem("estado_civil", estado_civil);
        localStorage.setItem("genero", genero);
        localStorage.setItem("cod_profesion", cod_profesion);
        localStorage.setItem("gradInst", gradInst);
        localStorage.setItem("areaDesem", areaDesem);
        localStorage.setItem("a_exp", a_exp);
		$("#div_tabla").show();
		$("#div_buscador").hide();
        $('#tablX').DataTable({

		"processing": true,
		"scrollX": true,
		// Internationalisation. For more info refer to http://datatables.net/manual/i18n
		"paging":   true,
		"ordering": true,
		"info":     false,
		"searching":     false,
		"language": {
			"aria": {
				"sortAscending": ": activate to sort column ascending",
				"sortDescending": ": activate to sort column descending"
			},
		"emptyTable": "No hay datos disponibles en la tabla",
		"info": "Mostrando _START_ de _END_ de  _TOTAL_ registros",
		"infoEmpty": "No se encontraron registros",
		"infoFiltered": "(filtrados de _MAX_ total registros)",
		"lengthMenu": "Mostrando _MENU_ entradas",
		"search": "Buscar:",
		"zeroRecords": "No se encontraron registros"
		},

		"order": [
			[0, 'asc']
		],
		"lengthMenu": [
			[5,10, 15, 20, -1],
			[5,10, 15, 20, "Todo"] // change per page values here
		],

		// set the initial value
		"pageLength": 10,
		"serverSide": true,
		"ajax": {
                    "url": "ajax/listadoBusquedaElegibles.php", // ajax source
                    "data":  {edad1:edad1,edad2:edad2,estado_civil:estado_civil,genero:genero,cod_profesion:cod_profesion,gradInst:gradInst,areaDesem:areaDesem,a_exp:a_exp}
                },

		 "dom": 'Tlfrtip',
        "tableTools": {
        	"aButtons": [
				"csv",
				"xls",
				{
					"sExtends": "pdf",
					"sPdfOrientation": "landscape",
				}
			],
                "sSwfPath": "../../estructura/public/tema/data-tables/extensions/TableTools/swf/copy_csv_xls_pdf.swf"
        }
		
 	});
		//console.log(posicion+" "+nombre+" "+apellido+" "+cargo+" "+funcion+" "+genero+" "+promocion );
		//location.href ="listado_busqueda_elegibles.php?posicion="+posicion+"&nombre="+nombre+"&apellido="+apellido+"&cargo="+cargo+"&funcion="+funcion+"&genero="+genero+"&promocion="+promocion+"&externo="+externo+"&tipo="+tipo+"&categoria="+categoria;
	});
});	


</script>
</body>
</html>