<?php 
require_once '../../generalp.config.inc.php';
session_start();
ob_start();

error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require_once '../lib/common.php';
include ("func_bd.php");
$conexion=conexion();

$id=  isset($_GET["codigo"]) ? $_GET["codigo"] : null ;
$nivel= 1;


$sql = "SELECT id, CodCue, Denominacion FROM cwprecue WHERE id=".$id;
//echo $sql,"<br>";
$res = query($sql, $conexion);
$fila=fetch_array($res);

?>


<html lang="es">
<!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
<meta charset="UTF-8">
<!-- BEGIN GLOBAL MANDATORY STYLES -->
<link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css"/>
<link href="../../includes/assets/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
<link href="../../includes/assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
<link href="../../includes/assets/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css"/>
<!-- END GLOBAL MANDATORY STYLES -->
<!-- BEGIN PAGE LEVEL STYLES -->
<link rel="stylesheet" type="text/css" href="../../includes/assets/plugins/select2/select2.css"/>
<link rel="stylesheet" type="text/css" href="../../includes/assets/plugins/select2/select2-metronic.css"/>
<link rel="stylesheet" href="../../includes/assets/plugins/data-tables/DT_bootstrap.css"/>
<link rel="stylesheet" type="text/css" href="../../includes/assets/plugins/clockface/css/clockface.css"/>
<link rel="stylesheet" type="text/css" href="../../includes/assets/plugins/bootstrap-datepicker/css/datepicker3.css"/>
<link rel="stylesheet" type="text/css" href="../../includes/assets/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css"/>
<link rel="stylesheet" type="text/css" href="../../includes/assets/plugins/bootstrap-colorpicker/css/colorpicker.css"/>
<link rel="stylesheet" type="text/css" href="../../includes/assets/plugins/bootstrap-daterangepicker/daterangepicker-bs3.css"/>
<link rel="stylesheet" type="text/css" href="../../includes/assets/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css"/>
<!-- END PAGE LEVEL STYLES -->
<!-- BEGIN THEME STYLES -->
<link href="../../includes/assets/css/style-metronic.css" rel="stylesheet" type="text/css"/>
<link href="../../includes/assets/css/style.css" rel="stylesheet" type="text/css"/>
<link href="../../includes/assets/css/style-responsive.css" rel="stylesheet" type="text/css"/>
<link href="../../includes/assets/css/plugins.css" rel="stylesheet" type="text/css"/>
<!-- <link href="../../includes/assets/css/themes/default.css" rel="stylesheet" type="text/css" id="style_color"/> -->
<link href="../../includes/assets/css/custom.css" rel="stylesheet" type="text/css"/>
<!-- END THEME STYLES -->
<link href="../../includes/assets/css/custom-header4.css" rel="stylesheet" type="text/css"/>
</head>
<!-- END HEAD -->
<!-- BEGIN BODY -->
<body class="page-full-width"  marginheight="0">
<input id="id" type="hidden" value="<?php echo $id; ?>">
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
									 Planificación Presupuestaria
							</div>							
							<div class="actions">
								<?php 
								echo '<a class="btn btn-sm blue"  href="presupuesto_cuentas_add.php?codigo='.$id.'">'; ?>
									<i class="fa fa-arrow-left"></i>
									 Regresar
								</a>
							</div>
						</div>
						<div class="portlet-body">
							<div class="row">
								<div class="form-group">
									<label class="col-md-3"><h4>Agregar Cuenta presupuestaria</h4></label>
									<div class="col-md-8">
									<?php
									$tabla="cwprecue";
									$consulta="select * from ".$tabla;
									$resultado=query($consulta,$conexion);
									?>
										<select id="cuenta" name="cuenta" class="form-control select2_category" autofocus>
										<option value=''>Seleccione...</option>
										<?php 
										while($fila=mysqli_fetch_array($resultado)){
										echo "<option value='".$fila["id"]."'>".$fila["CodCue"]." ".$fila["Denominacion"]."</option>";}
										?>
										</select>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="form-group">
									<label class="col-md-3"><h4>Año presupuestario</h4></label>
									<div class="col-md-3">
										<input name="anio" id="anio" type="text" class="text-right" value="<?= date(Y);?>">
									</div>
								</div>
							</div>

							<div class="row">
								<div class="form-group">
									<label class="col-md-3"><h4>Monto Inicial</h4></label>
									<div class="col-md-9">
									<input class="text-right" id="monto_inicial">

									</div>
								</div>
							</div>							
							<div class="row">
								<div class="form-group">
									<label class="col-md-3"><h4>Monto Enero</h4></label>
									<div class="col-md-9">
									<input class="text-right" id="Enero">

									</div>
								</div>
							</div>	
							<div class="row">
								<div class="form-group">
									<label class="col-md-3"><h4>Monto Febrero</h4></label>
									<div class="col-md-9">
									<input class="text-right" id="Febrero">

									</div>
								</div>
							</div>
							<div class="row">
								<div class="form-group">
									<label class="col-md-3"><h4>Monto Marzo</h4></label>
									<div class="col-md-9">
									<input class="text-right" id="Marzo">

									</div>
								</div>
							</div>
							<div class="row">
								<div class="form-group">
									<label class="col-md-3"><h4>Monto Abril</h4></label>
									<div class="col-md-9">
									<input class="text-right" id="Abril">

									</div>
								</div>
							</div>
							<div class="row">
								<div class="form-group">
									<label class="col-md-3"><h4>Monto Mayo</h4></label>
									<div class="col-md-9">
									<input class="text-right" id="Mayo">

									</div>
								</div>
							</div>
							<div class="row">
								<div class="form-group">
									<label class="col-md-3"><h4>Monto Junio</h4></label>
									<div class="col-md-9">
									<input class="text-right" id="Junio">

									</div>
								</div>
							</div>
							<div class="row">
								<div class="form-group">
									<label class="col-md-3"><h4>Monto Julio</h4></label>
									<div class="col-md-9">
									<input class="text-right" id="Julio">

									</div>
								</div>
							</div>	
							<div class="row">
								<div class="form-group">
									<label class="col-md-3"><h4>Monto Agosto</h4></label>
									<div class="col-md-9">
									<input class="text-right" id="Agosto">

									</div>
								</div>
							</div>
							<div class="row">
								<div class="form-group">
									<label class="col-md-3"><h4>Monto Septiembre</h4></label>
									<div class="col-md-9">
									<input class="text-right" id="Septiembre">

									</div>
								</div>
							</div>
							<div class="row">
								<div class="form-group">
									<label class="col-md-3"><h4>Monto Octubre</h4></label>
									<div class="col-md-9">
									<input class="text-right" id="Octubre">

									</div>
								</div>
							</div>
							<div class="row">
								<div class="form-group">
									<label class="col-md-3"><h4>Monto Noviembre</h4></label>
									<div class="col-md-9">
									<input class="text-right" id="Noviembre">

									</div>
								</div>
							</div>
							<div class="row">
								<div class="form-group">
									<label class="col-md-3"><h4>Monto Diciembre</h4></label>
									<div class="col-md-9">
									<input class="text-right" id="Diciembre">

									</div>
								</div>
							</div>


							<div>
								<div class="form-actions text-center">
									<a id="sig1" class="btn blue button-next">AGREGAR</a>
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



<!-- BEGIN JAVASCRIPTS(Load javascripts at bottom, this will reduce page load time) -->
<!-- BEGIN CORE PLUGINS -->
<!--[if lt IE 9]>
<script src="../../includes/assets/plugins/respond.min.js"></script>
<script src="../../includes/assets/plugins/excanvas.min.js"></script> 
<![endif]-->
<script src="../../includes/assets/plugins/jquery-1.10.2.min.js" type="text/javascript"></script>
<script src="../../includes/assets/plugins/jquery-migrate-1.2.1.min.js" type="text/javascript"></script>
<script src="../../includes/assets/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script src="../../includes/assets/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js" type="text/javascript"></script>
<script src="../../includes/assets/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
<script src="../../includes/assets/plugins/jquery.blockui.min.js" type="text/javascript"></script>
<script src="../../includes/assets/plugins/jquery.cokie.min.js" type="text/javascript"></script>
<script src="../../includes/assets/plugins/uniform/jquery.uniform.min.js" type="text/javascript"></script>
<script src="../../includes/assets/scripts/core/app1.js"></script>

<!-- END CORE PLUGINS -->
<!-- BEGIN PAGE LEVEL PLUGINS -->
<script type="text/javascript" src="../../includes/assets/plugins/select2/select2.min.js"></script>
<script type="text/javascript" src="../../includes/assets/plugins/data-tables/jquery.dataTables.js"></script>
<script type="text/javascript" src="../../includes/assets/plugins/data-tables/DT_bootstrap.js"></script>
<!-- END PAGE LEVEL PLUGINS -->
<script type="text/javascript" src="../../includes/assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
<script type="text/javascript" src="../../includes/assets/plugins/bootstrap-datepicker/js/locales/bootstrap-datepicker.es.js"></script>
<script type="text/javascript" src="../../includes/assets/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js"></script>
<script type="text/javascript" src="../../includes/assets/plugins/clockface/js/clockface.js"></script>
<script type="text/javascript" src="../../includes/assets/plugins/bootstrap-daterangepicker/moment.min.js"></script>
<script type="text/javascript" src="../../includes/assets/plugins/bootstrap-daterangepicker/daterangepicker.js"></script>
<script type="text/javascript" src="../../includes/assets/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.js"></script>
<script type="text/javascript" src="../../includes/assets/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"></
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="../../includes/assets/scripts/core/app1.js"></script>
<!-- <script src="../../includes/assets/scripts/custom/table-managed.js"></script> -->
<script>
window.scrollTo(0,0);
jQuery(document).ready(function() {

	$("#monto_inicial").keyup(function(){
		var monto_inicial=$("#monto_inicial").val();
		var division=monto_inicial/12;
		var division=division.toFixed(3);
		$("#Enero").val(division);
		$("#Febrero").val(division);
		$("#Marzo").val(division);
		$("#Abril").val(division);
		$("#Mayo").val(division);
		$("#Junio").val(division);
		$("#Julio").val(division);
		$("#Agosto").val(division);
		$("#Septiembre").val(division);
		$("#Octubre").val(division);
		$("#Noviembre").val(division);
		$("#Diciembre").val(division);



	});


   $("#sig1").on("click", function()
   {
   		var monto_inicial=$("#monto_inicial").val();
		var id=$("#id").val();
		var cuenta=$("#cuenta").val();
		var anio=$("#anio").val();
		var Enero=$("#Enero").val();
		var Febrero=$("#Febrero").val();
		var Marzo=$("#Marzo").val();
		var Abril=$("#Abril").val();
		var Mayo=$("#Mayo").val();
		var Junio=$("#Junio").val();
		var Julio=$("#Julio").val();
		var Agosto=$("#Agosto").val();
		var Septiembre=$("#Septiembre").val();
		var Octubre=$("#Octubre").val();
		var Noviembre=$("#Noviembre").val();
		var Diciembre=$("#Diciembre").val();	
		var nivel=$("#nivel").val();
		//alert(cuenta);	
		if(monto_inicial =='')
		{
			alert("Agregue el monto Inicial a la Cuenta Presupuestaria");
		}
		if(anio =='')
		{
			alert("Agregue el año de la Cuenta Presupuestaria");
		}
		else
		{
			location.href="presupuestar.php?id="+id+"&cuenta="+cuenta+"&monto_inicial="+monto_inicial+"&Enero="+Enero+"&Febrero="+Febrero+"&Marzo="+Marzo+
			"&Abril="+Abril+"&Mayo="+Mayo+"&Junio="+Junio+"&Julio="+Julio+"&Agosto="+Agosto+"&Septiembre="+Septiembre+"&Octubre="+Octubre+
			"&Noviembre="+Noviembre+"&Diciembre="+Diciembre+"&anio="+anio;
		}
   });

    $('#table_datatable').DataTable({
    	"iDisplayLength": 10,
    	
    	"oSearching": false,
    	"sPaginationType": "bootstrap_extended", 
        "oLanguage": {
        	"sSearch": "<img src='../../includes/imagenes/icons/magnifier.png' width='16' height='16' > Buscar:",
            "sLengthMenu": "Mostrar _MENU_",
            "sInfoEmpty": "",
            "sInfo":"Total _TOTAL_ registros",
            "sInfoFiltered": "",
  		    "sEmptyTable":  "No hay datos disponibles",//"No data available in table",
            "sZeroRecords": "No se encontraron registros",//"No matching records found",

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
            { 'bSortable': false, 'aTargets': [2] },
            { "bSearchable": false, "aTargets": [ 2 ] },
            { 'bSortable': false, 'aTargets': [2] },
            { "bSearchable": false, "aTargets": [ 2 ] },
            { "sWidth": "8%", "aTargets": [2] },
            { "sWidth": "8%", "aTargets": [2] }
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