<?php 
require_once '../../generalp.config.inc.php';
session_start();
ob_start();

//error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require_once '../lib/common.php';
include ("func_bd.php");
$conexion=conexion();


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
							<h4>Evaluación desempeño del empleado</h4>
						</div>
						<div class="portlet-body">
						<h4>Datos del empleado y del evaluador</h4>
								<div class="form-group">
									<label><h3>Datos del empleado a evaluar:</h3></label>
									<div class="radio-list">
									<?php
									$tabla="nompersonal";
									$consulta="select * from ".$tabla." WHERE estado='Activo' OR estado='REGULAR'";
									$resultado=query($consulta,$conexion);
									?>
										<select id="personal" name="personal" class="form-control select2_category" autofocus>
										<option value=''>Seleccione...</option>
										<?php 
										while($fila=mysqli_fetch_array($resultado)){
										echo "<option value='".$fila["ficha"]."'>".$fila["nombres"]." ".$fila["apellidos"]."</option>";}
										?>
										</select>

									</div>
								</div>
								<span  id="cambiar1">									
								</span>
								<div class="form-group">
									<label><h3>Datos del evaluador:</h3></label>
									<div class="radio-list">
									<?php
									$tabla="nompersonal";
									$consulta="select * from ".$tabla." WHERE estado='ACTIVO'  OR estado='REGULAR'";
									$resultado=query($consulta,$conexion);
									?>
										<select id="personal2" name="personal2" class="form-control select2_category" autofocus>
										<option value=''>Seleccione...</option>
										<?php 
										while($fila=mysqli_fetch_array($resultado)){
										echo "<option value='".$fila["ficha"]."'>".$fila["nombres"]." ".$fila["apellidos"]."</option>";}
										?>
										</select>
									</div>
																
								</div>
								<div id="cambiar2">
									</div>	
								


							<div class="form-actions text-center">
								<a id="sig1" class="btn blue button-next">SIGUIENTE</a>
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
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="../../includes/assets/scripts/core/app1.js"></script>
<!-- <script src="../../includes/assets/scripts/custom/table-managed.js"></script> -->
<script>
jQuery(document).ready(function() { 
   App.init();

   $("#personal").change(function(){
   		var personal=$("#personal").val();

	   	$.get("obtener_empleado.php",{personal:personal},function(resultado){
	   		$("#cambiar1").empty();
	   		$("#cambiar1").append(resultado);
	   		$("#cambiar1").select2({
			 	placeholder: 'Seleccione un Colaborador',
	            //allowClear: true,
	        });
	   	});
   });
   $("#personal2").change(function(){
   		var personal=$("#personal2").val();

	   	$.get("obtener_empleado.php",{personal:personal},function(resultado2){
	   		$("#cambiar2").empty();
	   		$("#cambiar2").append(resultado2);
	   		$("#cambiar2").select2({
			 	placeholder: 'Seleccione un Colaborador',
	            //allowClear: true,
	        });
	   	});
   });   
   $("#sig1").on("click", function()
   {
   		var personal=$("#personal").val();
   		var personal2=$("#personal2").val();
   		//alert(personal+" "+personal2);
   		if (personal=='') {
   			alert("Seleccione el empleado a evaluar");
   		}
   		else
		{	if (personal2=='')			
			{
				alert("Ingrese los datos del evaluador");
			}
			else
			{
				if (personal=='' && personal2=='') {
					alert("Ingrese los datos del empleado a evaluar y del evaluador");
				}
				else
				{	
					if(personal==personal2)
					{
						alert("El evaluador y el evaluado no pueden ser lo mismos");
					}
					else
					{
						location.href="evaluacion_empleado_2.php?personal="+personal+"&personal2="+personal2;

					}
				}
			}
		}

   })
});
</script>

</body>
</html>