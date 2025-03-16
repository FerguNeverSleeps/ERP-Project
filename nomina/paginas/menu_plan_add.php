<?php 
require_once '../../generalp.config.inc.php';
session_start();
ob_start();

error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require_once '../lib/common.php';
include ("func_bd.php");
$conexion=conexion();
$codigo=$_REQUEST['codigo'];
$sql = "SELECT * FROM menu_cursos WHERE id=".$codigo;
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
								Planes
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
										<input type="text" id="codigo_plan" class="form-control" value="<?php echo utf8_encode($fila[cod_curso]); ?>">
									</div>
								</div>
							</div>

							<div class="row">
								<div class="form-group">
									<label class="col-md-3"><h4>Capitulo</h4></label>
									<div class="col-md-6">
										<input type="text" id="capitulo_plan" class="form-control" value="<?php echo utf8_encode($fila[capitulo_curso]); ?>">
									</div>
								</div>
							</div>

							<div class="row">
								<div class="form-group">
									<label class="col-md-3"><h4>Módulo</h4></label>
									<div class="col-md-6">
										<input type="text" id="modulo_plan" class="form-control" value="<?php echo utf8_encode($fila[modulo_curso]); ?>">
									</div>
								</div>
							</div>

							<div class="row">
								<div class="form-group">
									<label class="col-md-3"><h4>Duración</h4></label>
									<div class="col-md-6">
										<input type="text" id="duracion_plan" class="form-control"value="<?php echo utf8_encode($fila[duracion_curso]); ?>">
									</div>
								</div>
							</div>
							<div class="row">
								<div class="form-group">
									<label class="col-md-3"><h4>Agregar plan de distribución</h4></label>
									<div class="col-md-6">
									<?php
									$tabla="menu_plan_dist";
									$consulta="select * from ".$tabla."";
									$resultado=query($consulta,$conexion);
									?>
										<select id="id_plan_dist" name="id_plan_dist" class="form-control select2_category" autofocus>
										<option value="">Seleccione...</option>
										<?php 
										while($filas=mysqli_fetch_array($resultado)){
											$fila_curso= utf8_encode($filas["id"])."-".utf8_encode($filas["modulo_plan_dist"]);
										echo "<option value='".$filas["id"]."'>".$fila_curso."</option>";}
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
   		var codigo_plan=$("#codigo_plan").val();
   		var capitulo_plan=$("#capitulo_plan").val();
   		var modulo_plan=$("#modulo_plan").val();
   		var id_plan_dist=$("#id_plan_dist").val();
   		var duracion=$("#duracion").val();
   		//alert(curso+" "+fecha+" "+instructor+" "+descripcion+" "+duracion+" ");
   		if (codigo_plan=='') 
		{
			alert("Seleccione el código");
		}
		if (capitulo_plan=='') 
		{
			alert("Seleccione la dirección");
		}
		if (modulo_plan=='')
		{
			alert("Ingrese el campo de Población de Entrenamiento");
		}

		if(id_plan_dist=='') 
		{
			alert("Seleccione los roles");
		}

		if(codigo_plan !='' && capitulo_plan !='' && modulo_plan !='' && id_plan_dist)
		{
			location.href="planes_add.php?id="+id+"&codigo_plan="+codigo_plan+"&capitulo_plan="+capitulo_plan+"&modulo_plan="+modulo_plan+"&id_plan_dist="+id_plan_dist;

		}

   });
});
</script>

</body>
</html>