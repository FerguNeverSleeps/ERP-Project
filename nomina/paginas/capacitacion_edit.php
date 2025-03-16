<?php 
require_once '../../generalp.config.inc.php';
session_start();
ob_start();

error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require_once '../lib/common.php';
include ("func_bd.php");
$conexion=conexion();

$codigo=$_REQUEST['codigo'];
$sql = "SELECT * FROM nomcursos as a, nomcursos_personal as b where a.id=b.id_curso and b.id_curso=".$codigo;
$res = query($sql, $conexion);

$fila = fetch_array($res);

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
								Capacitación de Personal
							</div>	
							<div class="actions">
								<a class="btn btn-sm blue"  onclick="javascript: window.location='capacitacion_list.php'">
									<i class="fa fa-arrow-left"></i>
									 Regresar
								</a>
							</div>
						</div>

						<div class="portlet-body">
							<div class="row">
								<div class="form-group">
									<label class="col-md-3"><h4>Nombre del curso</h4></label>
									<div class="col-md-9">
										<input type="text" id="curso" class="form-control" value="<?php echo $fila[nombre_curso]; ?>">
										<input type="hidden" id="codigo"  value="<?php echo $codigo ?>">

									</div>
								</div>
							</div>
							<div class="row">
								<div class="form-group">
									<label class="col-md-3"><h4>Fecha</h4></label>
									<div class="col-md-9">
										<div class="input-group input-medium date  date-picker" data-date-format="dd-mm-yyyy" data-date-viewmode="years">
											<input id="fecha" class="form-control form-control-inline input-medium" size="16" type="text" value="<?php echo $fila[fecha_curso]; ?>" readonly>
											<span class="input-group-btn">
												<button class="btn default" type="button"><i class="fa fa-calendar"></i></button>
											</span>
										</div>
										<span class="help-block">
											Seleccione la fecha </span>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="form-group">
									<label class="col-md-3"><h4>Instructor</h4></label>
									<div class="col-md-9">
										<input type="text" id="instructor" class="form-control" value="<?php echo  $fila[instructor_curso]; ?>">
									</div>
								</div>
							</div>
							<div class="row">
								<div class="form-group">
									<label class="col-md-3"><h4>Descripción del curso</h4></label>
									<div class="col-md-9">
										<textarea class="form-control" id="descripcion" cols="3" value=""><?php echo  $fila[descripcion_curso]; ?></textarea>
									</div>
								</div>
							</div>	
							<div class="row">
								<div class="form-group">
									<label class="col-md-3"><h4>Duración del curso</h4></label>
									<div class="col-md-9">
										<input id="duracion" type="text" class="form-control form-control-inline input-medium" value="<?php echo  $fila[duracion]; ?>"> horas
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
   		var curso=$("#curso").val();
   		var codigo=$("#codigo").val();
   		var fecha=$("#fecha").val();
   		var instructor=$("#instructor").val();
   		var descripcion=$("#descripcion").val();
   		var duracion=$("#duracion").val();
   		//alert(curso+" "+fecha+" "+instructor+" "+descripcion+" "+duracion+" ");
   		if (curso=='') 
   		{
   			alert("Seleccione el curso");
   		}
   		else
		{	
			if (fecha=='')
			{
				alert("Ingrese la fecha del curso");
			}
			else
			{
				if (instructor=='') 
				{
					alert("Ingrese los datos del instructor");
				}
				else
				{	
					if(descripcion=='') 
					{
						alert("Ingrese la descripcion del curso");
					}
					else
					{	
						if(duracion =='')
						{
							alert("Ingrese la duracion del curso");
						}
						else
						{
							location.href="cursos_edit.php?id="+codigo+"&fecha="+fecha+"&curso="+curso+"&instructor="+instructor+"&descripcion="+descripcion+"&duracion="+duracion;
						}
					}
				}
			}
		}

   });
});
</script>

</body>
</html>