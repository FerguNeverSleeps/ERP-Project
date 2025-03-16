<?php 
require_once '../../generalp.config.inc.php';
session_start();
ob_start();

error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require_once '../lib/common.php';
include ("func_bd.php");
$conexion=conexion();

?>


<html lang="es">
<!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
<meta charset="UTF-8">
<!-- BEGIN GLOBAL M&&ATORY STYLES -->
<link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css"/>
<link href="../../includes/assets/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
<link href="../../includes/assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
<link href="../../includes/assets/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css"/>
<!-- END GLOBAL M&&ATORY STYLES -->
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
								Cursos
							</div>	
							<div class="actions">
								<a class="btn btn-sm blue"  onclick="javascript: window.location='cursos_list.php'">
									<i class="fa fa-arrow-left"></i>
									 Regresar
								</a>
							</div>
						</div>

						<div class="portlet-body">

							<div class="row">
								<div class="form-group">
									<label class="col-md-3"><h4>Código</h4></label>
									<div class="col-md-6">
										<input type="text" id="codigo_curso" class="form-control" placeholder="Código...">
									</div>
								</div>
							</div>

							<div class="row">
								<div class="form-group">
									<label class="col-md-3"><h4>Capitulo</h4></label>
									<div class="col-md-6">
										<input type="text" id="capitulos_curso" class="form-control" placeholder="Capitulo...">
									</div>
								</div>
							</div>
							
							<div class="row">
								<div class="form-group">
									<label class="col-md-3"><h4>Área</h4></label>
									<div class="col-md-6">
										<input type="text" id="area_curso" class="form-control" placeholder="Área...">
									</div>
								</div>
							</div>

							<div class="row">
								<div class="form-group">
									<label class="col-md-3"><h4>Cliente</h4></label>
									<div class="col-md-6">
										<input type="text" id="cliente_curso" class="form-control" placeholder="Cliente...">
									</div>
								</div>
							</div>

							<div class="row">
								<div class="form-group">
									<label class="col-md-3"><h4>Módulo</h4></label>
									<div class="col-md-6">
										<input type="text" id="modulo_curso" class="form-control" placeholder="Módulo...">
									</div>
								</div>
							</div>
							<div class="row">
								<div class="form-group">
									<label class="col-md-3"><h4>Duración del curso</h4></label>
									<div class="col-md-6">
										<input id="duracion_curso" type="text" class="form-control form-control-inline input-medium" placeholder="Duración del curso..."> horas
									</div>
								</div>
							</div>

							<div class="row">
								<div class="form-group">
									<label class="col-md-3"><h4>Horas Teóricas</h4></label>
									<div class="col-md-6">
										<input id="teorica_curso" type="text" class="form-control form-control-inline input-medium" placeholder="Horas Teórica..."> horas
									</div>
								</div>
							</div>

							<div class="row">
								<div class="form-group">
									<label class="col-md-3"><h4>Horas Prácticas</h4></label>
									<div class="col-md-6">
										<input id="practica_curso" type="text" class="form-control form-control-inline input-medium" placeholder="Horas Prácticas..."> horas
									</div>
								</div>
							</div>
							<div class="row">
								<div class="form-group">
									<label class="col-md-3"><h4>Estrategia del curso</h4></label>
									<div class="col-md-6">
										<textarea class="form-control" id="estrategia_curso" cols="3" placeholder="Estrategia del curso..."></textarea>
									</div>
								</div>
							</div>	
							<div class="row">
								<div class="form-group">
									<label class="col-md-3"><h4>Proveedores/Facilitadores</h4></label>
									<div class="col-md-6">
										<input type="text" id="instructor_curso" class="form-control" placeholder="Proveedores/Facilitadores...">
									</div>
								</div>
							</div>




							<div class="form-actions text-center">
								<a id="sig1" class="btn blue button-next">GUARDAR</a>
							</div>
							
							
							
						</div>
						<!-- END PORTLET BODY-->
						
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
jQuery(document).ready(function() { 
window.scrollTo(0,0);

    $('.date-picker').datepicker({
        orientation: "left",
        language: 'es',
		autoclose: true
    });
     
   $("#sig1").on("click", function()
   {
   		var codigo_curso=$("#codigo_curso").val();
   		var area_curso=$("#area_curso").val();
   		var estrategia_curso=$("#estrategia_curso").val();
   		var duracion_curso=$("#duracion_curso").val();
   		var capitulos_curso=$("#capitulos_curso").val();
   		var modulo_curso=$("#modulo_curso").val();
   		var cliente_curso=$("#cliente_curso").val();
   		var teorica_curso=$("#teorica_curso").val();
   		var practica_curso=$("#practica_curso").val();   		
   		//alert(curso+" "+fecha+" "+instructor+" "+descripcion+" "+duracion+" ");
		if (codigo_curso=='') 
		{
			alert("Seleccione el curso");
		}
		if (area_curso=='') 
		{
			alert("Seleccione el curso");
		}
		if (instructor_curso=='') 
		{
			instructor_curso='';
		}

		if(estrategia_curso=='') 
		{
			alert("Ingrese la descripcion del curso");
		}

		if(duracion_curso =='')
		{
			alert("Ingrese la duracion del curso");
		}
		if (capitulos_curso=='') 
		{
			alert("Seleccione el curso");
		}
		if (modulo_curso=='')
		{
			alert("Ingrese la fecha del curso");
		}

		if (teorica_curso=='') 
		{
			alert("Ingrese los datos del instructor");
		}

		if(practica_curso=='') 
		{
			alert("Ingrese la descripcion del curso");
		}

		if(cliente_curso=='') 
		{
			alert("Ingrese la descripcion del curso");
		}

		if(codigo_curso !='' && cliente_curso !='' && practica_curso !='' && teorica_curso !='' && modulo_curso !='' && capitulos_curso !='' 
			&& duracion_curso !='' && estrategia_curso !='' && instructor_curso  !='' && area_curso !='')
		{
			location.href="menu_cursos_add.php?codigo_curso="+codigo_curso+"&cliente_curso="+cliente_curso+"&practica_curso="+practica_curso+"&teorica_curso="+teorica_curso+"&modulo_curso="+modulo_curso+"&capitulos_curso="+capitulos_curso+"&duracion_curso="+duracion_curso+"&instructor_curso="+instructor_curso+"&area_curso="+area_curso+"&estrategia_curso="+estrategia_curso;

		}


   });
});
</script>

</body>
</html>