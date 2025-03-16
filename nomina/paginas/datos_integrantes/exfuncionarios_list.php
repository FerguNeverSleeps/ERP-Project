<?php 
require_once('../../lib/database.php');
require_once("../func_bd.php") ;
$config = parse_ini_file("../../lib/selectra.ini");

$db = new Database($_SESSION['bd']);
$id_usuario=$_SESSION['id_usuario'];
$sql = "SELECT i.descrip as planilla, i.personal_id, i.ficha, i.cedula, i.apenom, i.estado, i.nomposicion_id as posicion,i.tipnom as tipnom
		FROM   nomvis_integrantes i
        WHERE  i.tipnom IN 
        (SELECT  id_nomina from ".SELECTRA_CONF_PYME.".nomusuario_nomina where id_usuario=$id_usuario AND  acceso=1)";
//echo $sql;
//$res = $db->query($sql);

$sql1         = "SELECT tipo_empresa FROM nomempresa";
$res1         = $db->query($sql1); 
$rs1          = $res1->fetch_assoc();
$tipo_empresa = $rs1['tipo_empresa']; 
?>
<script type="text/javascript">
function enviar(id){
	// Opcion de Eliminar
	if (confirm("Seguro desea Dejar sin Efecto a esta Funcionario?"))
	{
		window.location="../../../funcionarios/exfuncionarios.php?id="+id;
	}
}
</script>
<!DOCTYPE html>
<html lang="es" class="no-js">
<!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
<meta charset="utf-8"/>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<meta content="" name="description"/>
<meta content="" name="author"/>
<!-- BEGIN GLOBAL MANDATORY STYLES -->
<link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css"/>
<link href="../../../includes/assets/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
<link href="../../../includes/assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
<link href="../../../includes/assets/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css"/>
<!-- END GLOBAL MANDATORY STYLES -->
<!-- BEGIN PAGE LEVEL STYLES -->
<link rel="stylesheet" type="text/css" href="../../../includes/assets/plugins/select2/select2.css"/>
<link rel="stylesheet" type="text/css" href="../../../includes/assets/plugins/select2/select2-metronic.css"/>
<link rel="stylesheet" href="../../../includes/assets/plugins/data-tables/DT_bootstrap.css"/>
<link rel="stylesheet" type="text/css" href="../../../reporte_pub/css/jquery.fancybox.css" media="screen" />
<!-- END PAGE LEVEL STYLES -->
<!-- BEGIN THEME STYLES -->
<link href="../../../includes/assets/css/style-metronic.css" rel="stylesheet" type="text/css"/>
<link href="../../../includes/assets/css/style.css" rel="stylesheet" type="text/css"/>
<link href="../../../includes/assets/css/style-responsive.css" rel="stylesheet" type="text/css"/>
<link href="../../../includes/assets/css/plugins.css" rel="stylesheet" type="text/css"/>
<!-- <link href="../../includes/assets/css/themes/default.css" rel="stylesheet" type="text/css" id="style_color"/> -->
<link href="../../../includes/assets/css/custom.css" rel="stylesheet" type="text/css"/>
<!-- END THEME STYLES -->
<link href="../../../includes/assets/css/custom-header4.css" rel="stylesheet" type="text/css"/>
<link href="../../../includes/assets/css/custom-datatables.css" rel="stylesheet" type="text/css"/>
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

</style>
</head>
<body class="page-header-fixed page-full-width" marginheight="0">
<div class="page-container">
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
								Exfuncionarios
							</div>
							<div class="actions">
								<?php if (isset($_SESSION['ver_rpt_funcionario'])): ?>
								<div class="btn-group">
								  <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								      <img src="../../../includes/imagenes/icons/printer.png" alt="" width="16" height="16"> Reportes Generales <span class="caret"></span>
								  </button>
								  <ul class="dropdown-menu">
									<li><a target="_blank" href="../../../reporte_pub/pantalla_cambio_cedula.php?id=<?= $fila['ficha'] ?>" class="fancybox fancybox.iframe"
									href="../../tcpdf/reportes/reporte_cambios_cedula.php?id=<?= $fila['ficha'] ?>" title="" style="cursor: pointer">
									<img src="../../../includes/imagenes/icons/printer.png" alt="" width="16" height="16"> Modif. O Cambios de Cedula</a></li>
									<li><a target="_blank" href="../../../reporte_pub/pantalla_baja.php?id=<?= $fila['ficha'] ?>" class="fancybox fancybox.iframe" style="cursor: pointer">
									<img src="../../../includes/imagenes/icons/printer.png" alt="" width="16" height="16"> Bajas</a></li>
									<li><a target="_blank" href="../../../reporte_pub/pantalla_activacion_baja.php?id=<?= $fila['ficha'] ?>" class="fancybox fancybox.iframe" style="cursor: pointer">
									<img src="../../../includes/imagenes/icons/printer.png" alt="" width="16" height="16"> Activacion de Bajas</a></li>
									<li><a target="_blank" href="../../../reporte_pub/pantalla_traslados.php?id=<?= $fila['ficha'] ?>" class="fancybox fancybox.iframe" style="cursor: pointer">
									<img src="../../../includes/imagenes/icons/printer.png" alt="" width="16" height="16"> Traslados</a></li>
									<li><a target="_blank" href="../../../reporte_pub/pantalla_solicitud_descuentos.php?id=<?= $fila['ficha'] ?>" class="fancybox fancybox.iframe" style="cursor: pointer">
									<img src="../../../includes/imagenes/icons/printer.png" alt="" width="16" height="16"> Solicitud Descuentos</a></li>
									<li><a target="_blank" href="../../../reporte_pub/pantalla_cambio_cedula2.php?id=<?= $fila['ficha'] ?>" class="fancybox fancybox.iframe" style="cursor: pointer">
									<img src="../../../includes/imagenes/icons/printer.png" alt="" width="16" height="16"> Modificaciones</a></li>
									<!--Nuevos Reportes -->
									<li><a target="_blank" href="../../../reporte_pub/pantalla_cambio_cedula2.php?id=<?= $fila['ficha'] ?>" class="fancybox fancybox.iframe" style="cursor: pointer">
									<img src="../../../includes/imagenes/icons/printer.png" alt="" width="16" height="16"> Suspensiones</a></li>
									<li><a target="_blank" href="../../../reporte_pub/pantalla_cambio_cedula2.php?id=<?= $fila['ficha'] ?>" class="fancybox fancybox.iframe" style="cursor: pointer">
									<img src="../../../includes/imagenes/icons/printer.png" alt="" width="16" height="16"> Ajustes Planilla</a></li>
									<li><a target="_blank" href="../../../reporte_pub/pantalla_cambio_cedula2.php?id=<?= $fila['ficha'] ?>" class="fancybox fancybox.iframe" style="cursor: pointer">
									<img src="../../../includes/imagenes/icons/printer.png" alt="" width="16" height="16"> Imputaciones</a></li>
									<li><a target="_blank" href="../../../reporte_pub/pantalla_cambio_cedula2.php?id=<?= $fila['ficha'] ?>" class="fancybox fancybox.iframe" style="cursor: pointer">
									<img src="../../../includes/imagenes/icons/printer.png" alt="" width="16" height="16"> Reclamos</a></li>
								  </ul>
								</div>
								<?php endif ?>
								<?php if (isset($_SESSION['add_funcionario'])): ?>
									<a class="btn btn-sm blue"  onclick="javascript: window.location='../ag_integrantes2.php?back_listado_integrantes'">
										<i class="fa fa-plus"></i> Agregar
									</a>
								<?php endif ?>
							</div>
						</div>
						<div class="portlet-body">
							<div id="div_search_situ" style="display: inline;">
								<select id="search_situ" class="form-control input-inline input-small">
									<option value="Todos">Buscar por situación</option>
									<?php 
										$sql1 = "SELECT situacion FROM nomsituaciones";
										$res1 = $db->query($sql1);

										while($fila = $res1->fetch_array())
										{
											echo '<option value="'.$fila['situacion'].'">'.$fila['situacion'].'</option>';
										}
									?>										
								</select>
							</div>
							<form action="" method="post" name="frmPrincipal" id="frmPrincipal">
								<table class="table table-striped table-bordered table-hover" id="datatable">
									<thead>
										<tr>
											<th>Foto</th>
											<th>Planilla</th>
											<th># Colab.</th>
											<th>C&eacute;dula</th>
											<th>Apellidos y nombres</th>
											<th>Situaci&oacute;n</th>
											<th>Posici&oacute;n</th>						
											<th>&nbsp;</th>
											<th>&nbsp;</th>
											<th>&nbsp;</th>
											<th>&nbsp;</th>
											<th>&nbsp;</th>
											<th>&nbsp;</th>
										</tr>
									</thead>
									<tbody>
									
									</tbody>
								</table>
							    <input name="registro_id" type="hidden" value="">
							    <input name="op" type="hidden" value="">	
							</form>
							<!-- </div> -->
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
<!-- BEGIN JAVASCRIPTS(Load javascripts at bottom, this will reduce page load time) -->
<!-- BEGIN CORE PLUGINS -->
<!--[if lt IE 9]>
<script src="../../../includes/assets/plugins/respond.min.js"></script>
<script src="../../../includes/assets/plugins/excanvas.min.js"></script> 
<![endif]-->
<script src="../../../includes/assets/plugins/jquery-1.10.2.min.js"></script>
<script src="../../../includes/assets/plugins/jquery-migrate-1.2.1.min.js"></script>
<script src="../../../includes/assets/plugins/bootstrap/js/bootstrap.min.js"></script>
<script src="../../../includes/assets/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js"></script>
<script src="../../../includes/assets/plugins/jquery-slimscroll/jquery.slimscroll.min.js"></script>
<script src="../../../includes/assets/plugins/jquery.blockui.min.js"></script>
<script src="../../../includes/assets/plugins/jquery.cokie.min.js"></script>
<script src="../../../includes/assets/plugins/uniform/jquery.uniform.min.js"></script>
<!-- END CORE PLUGINS -->
<!-- BEGIN PAGE LEVEL PLUGINS -->
<script src="../../../includes/assets/plugins/select2/select2.min.js"></script>
<script src="../../../includes/assets/plugins/select2/select2_locale_es.js"></script>
<script src="../../../includes/assets/plugins/data-tables/jquery.dataTables.js"></script>
<script src="../../../includes/assets/plugins/data-tables/DT_bootstrap.js"></script>
<!-- END PAGE LEVEL PLUGINS -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="../../../includes/assets/scripts/core/app1.js"></script>
<script>
jQuery(document).ready(function() {       
   App.init();
});
</script>
<script type="text/javascript">
$(document).ready(function() { 
	createEvents();
	var oTable = $('#datatable').DataTable({
			        "bProcessing": true,
			        "bServerSide": true,
					"sAjaxSource": "ajax/server_processing_exfuncionarios.php", 
	 				"sDom": "<'row'<'col-md-3 col-sm-12'l><'col-md-9 col-sm-12'f>r><'table-scrollable't><'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>",
	            	"iDisplayLength": 25,
	            	"sPaginationType": "bootstrap_extended",
	            	"aaSorting": [[1, 'asc'], [ 2, "asc" ]], 
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
	                        "sNext":     "P&aacute;gina Siguiente",
	                        "sPage":     "P&aacute;gina",
	                        "sPageOf":   "de",
	                    }
	                },
	                "aLengthMenu": [ 
	                    [5, 10, 25, 50,  -1],
	                    [5, 10, 25, 50, "Todos"]
	                ],                
	                "aoColumnDefs": [ 
	                	{ 'bSortable':   false, 'aTargets': [0, 7, 8, 9, 10, 11,12] },
	                	{ 'bSearchable': false, 'aTargets': [0, 7, 8, 9, 10, 11,12] },
	                	{ "sClass": "text-center", "aTargets": [7, 8, 9, 10, 11,12] },
	                ],
					"fnDrawCallback": function() {
					    $('#datatable_filter input').attr("placeholder", "Escriba frase para buscar");
					    createEvents();
					},
					initComplete: function(){
					    createEvents();
					}
	            });

	            $('#datatable_wrapper .dataTables_filter input').addClass("form-control input-medium input-inline"); 
	            $('#datatable_wrapper .dataTables_length select').addClass("form-control input-small"); 
	            $('#datatable_wrapper .dataTables_length select').select2({
	                showSearchInput : false
	            }); 

	            $('#div_search_situ').insertBefore("#table_datatable_wrapper .dataTables_filter input");

	            $('#datatable_wrapper .dataTables_filter input').after(' <a class="btn blue" id="btn-search"><i class="fa fa-search"></i> Buscar</a> ');

			    $("#btn-search").click( function()
			    {
			    	 var valor_buscar =$('#search_situ').val();
			    	 if(valor_buscar == 'Todos')
			    	 	valor_buscar = '';

			    	 oTable.fnFilter( valor_buscar, 5 ); // Se filtra por la columna 5 - Situación
			    });

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
</script>
<script src="../../../reporte_pub/js/jquery.fancybox.pack.js"></script>
<script>
    $(document).ready(function() {
        $('.fancybox').fancybox( {topRatio:0,width:1000} );
    });
</script>
</body>
</html>