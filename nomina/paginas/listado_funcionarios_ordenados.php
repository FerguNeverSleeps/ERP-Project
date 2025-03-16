<?php 
session_start();
ob_start();

require_once('../lib/database.php');

$db = new Database($_SESSION['bd']);

?>
<!DOCTYPE html>
<html >
<head>
	<meta charset="utf-8">
</head>
<body>



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

<div class="row">
	<div class="col-md-12">
		<!-- BEGIN EXAMPLE TABLE PORTLET-->
		<div class="portlet box blue">
			<div class="portlet-title">
				<div class="caption">
				   INFORME DE FUNCIONARIOS POR ORDEN ALFABÉTICO
				</div>
				<div class="actions">
					
					<a class="btn btn-sm blue-hoki"  onclick="javascript: window.location='config_rpt_funcionarios_orden_alfabetico.php'">
						<i class="fa fa-arrow-left"></i> Regresar
					</a>
				</div>
			</div>
			<input type="hidden" name="posicion" id="posicion" value="<?= $_GET['posicion'] ?>">
			<input type="hidden" name="nombre" id="nombre" value="<?= $_GET['nombre'] ?>">
			<input type="hidden" name="apellido" id="apellido" value="<?= $_GET['apellido'] ?>">
			<input type="hidden" name="funcion" id="funcion" value="<?= $_GET['funcion'] ?>">
			<input type="hidden" name="cargo" id="cargo" value="<?= $_GET['cargo'] ?>">
			<input type="hidden" name="genero" id="genero" value="<?= $_GET['genero'] ?>">
			<input type="hidden" name="externo" id="externo" value="<?= $_GET['externo'] ?>">
			<input type="hidden" name="promocion" id="promocion" value="<?= $_GET['promocion'] ?>">
			<div class="portlet-body">
				<form action="" method="post" name="frmPrincipal" id="frmPrincipal">
				<table class="table table-striped table-bordered table-hover" id="tablX">
				<thead>
				<tr>
					<th>Cédula</th>
					<th>Nombre</th>
					<th>Fec. Nac.</th>
					<th>Pos.</th>
					<th>Fec. Ini.</th>
					<th>Función</th>
					<th>Salario</th>
					<th>Género</th>
					<th>Departamento</th>
					<th>Niv- Educ.</th>
					<th>Prom.</th>
					<th>Doc. Carrera</th>
					<th>F. Doc. Carrera</th>
					<th>Estado</th>
					<th>Tipo</th>
					<th>Dir.</th>
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
<script type="text/javascript" src="../../estructura/public/tema/jquery-validation/localization/messages_es.js"></script><script type="text/javascript">
 $(document).ready(function() { 
 	var promocion = $("#promocion").val();
	var posicion  = $("#posicion").val();
	var nombre    = $("#nombre").val();
	var apellido  = $("#apellido").val();
	var cargo     = $("#cargo").val();
	var funcion   = $("#funcion").val();
	var genero    = $("#genero").val();
	var externo   = $("#externo").val();
	console.log(posicion+" "+nombre+" "+apellido+" "+cargo+" "+funcion+" "+genero+" "+promocion );

	$('#tablX').DataTable({

		"processing": true,

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
                    "url": "ajax/listadoFuncionariosOrdenados.php", // ajax source
                    "data":  {posicion:posicion,nombre:nombre,apellido:apellido,cargo:cargo,funcion:funcion,genero:genero,externo:externo}
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
 });
</script>
</body>
</html>