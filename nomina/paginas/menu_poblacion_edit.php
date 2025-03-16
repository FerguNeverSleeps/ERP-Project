<?php 
require_once '../../generalp.config.inc.php';
session_start();
ob_start();

error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require_once '../lib/common.php';
include ("func_bd.php");
$conexion=conexion();

$codigo=$_REQUEST['codigo'];
$sql = "SELECT * FROM menu_poblacion WHERE id=".$codigo;
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
<input type="hidden" id="id" value="<?php echo $codigo;?>">
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
								<a class="btn btn-sm blue"  onclick="javascript: window.location='poblacion_list.php'">
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
										<input type="text" id="codigo_pob" class="form-control"  value="<?php echo $fila[codigo_pob]; ?>">
									</div>
								</div>
							</div>

							<div class="row">
								<div class="form-group">
									<label class="col-md-3"><h4>Dirección</h4></label>
									<div class="col-md-6">
										<input type="text" id="direccion_pob" class="form-control" p value="<?php echo $fila[direccion_pob]; ?>">
									</div>
								</div>
							</div>
							<div class="row">
								<div class="form-group">
									<label class="col-md-3"><h4>Población de Entrenamientos</h4></label>
									<div class="col-md-6">
										<input type="text" id="entrenamiento_pob" class="form-control" value="<?php echo $fila[entrenamiento_pob]; ?>">
									</div>
								</div>
							</div>
							<div class="row">
								<div class="form-group">
									<label class="col-md-3"><h4>Roles</h4></label>
									<div class="col-md-6">
										<textarea class="form-control" id="roles_pob" cols="5"><?php echo $fila[roles_pob]; ?></textarea>
									</div>
								</div>
							</div>
							&nbsp;

							<div class="row">
								<div class="form-group">
									<label class="col-md-3"><h4>Agregar plan</h4></label>
									<div class="col-md-6">
									<?php
									$tabla="menu_plan_dist";
									$consulta="select * from ".$tabla."";
									$resultado=query($consulta,$conexion);
									?>
										<select id="planes_pob" name="planes_pob" class="form-control select2_category" autofocus>
										<option value="">Seleccione...</option>
										<?php 
										while($filas=mysqli_fetch_array($resultado)){
										echo "<option value='".$filas["id"]."'> Plan ".$filas["id"]." - ".$filas["modulo_plan"]."</option>";}
										?>
										</select>
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
   		var id=$("#id").val();
   		var codigo_pob=$("#codigo_pob").val();
   		var planes_pob=$("#planes_pob").val();
   		var roles_pob=$("#roles_pob").val();
   		var entrenamiento_pob=$("#entrenamiento_pob").val();
   		var direccion_pob=$("#direccion_pob").val();
	
   		//alert(curso+" "+fecha+" "+instructor+" "+descripcion+" "+duracion+" ");
		if (codigo_pob=='') 
		{
			alert("Seleccione el modulo");
		}
		if (rol_plan_dist=='') 
		{
			alert("Seleccione el rol");
		}

		if(duracion_curso =='')
		{
			alert("Ingrese la duracion del plan dist.");
		}
		
		if(modulo_plan_dist !='' && rol_plan_dist !='' && practica_curso !='' )
		{
			location.href="poblacion_edit.php?id="+id+"&codigo_pob="+codigo_pob+"&planes_pob="+planes_pob+"&roles_pob="+roles_pob+"&entrenamiento_pob="+entrenamiento_pob+"&direccion_pob="+direccion_pob;

		}

   });
});
</script>

</body>
</html>