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
<!--link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css"/-->
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
<body class="page-header-fixed page-full-width"  marginheight="0">

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
							<h4>Datos del evaluador y del evaluado</h4>
						</div>
						<div class="portlet-body">
							<span style="color: rgb(66, 66, 66);"><font ;="" size="+3"><b>Evaluación 360º</b></font></span>
							<br>
							<br>Esta evaluación sirve para identificar las áreas de mejora necesarias para el desarollo profesional del evaluado.<br>Gracias por dedicar su tiempo a completarla.

								<div>Sus respuestas serán tratadas de forma 
									<span style="font-weight: bold;">ANÓNIMA</span>
									<span style="background-color: rgb(247, 247, 247);">.</span>
									<br/><br>Es importante que responda con total sinceridad.
								</div>

								<div class="form-group">
									<label><h2>Nombre de la persona a evaluar:</h2></label>
									<div class="radio-list">
									<?php
									$tabla="nompersonal";
									$consulta="select * from ".$tabla." WHERE estado='ACTIVO' OR estado='REGULAR'";
									$resultado=query($consulta,$conexion);
									?>
										<select name="personal" id="personal" class="form-control select2_category" autofocus>
										<option value=''>Seleccione...</option>
										<?php 
										while($fila=mysqli_fetch_array($resultado)){
										echo "<option value='".$fila["personal_id"]."'>".$fila["nombres"]." ".$fila["apellidos"]."</option>";}
										?>
										</select>
									</div>
								</div>	
								<div id='cambiar1'></div>
								<div class="form-group">
									<label><h2>Relación con el evaluado:</h2></label>
									<div class="radio-list">
										<label>
											<input type="radio" name="relacion111" id="relacion111" value="110" checked> Colega / Compañero 	</label>
										<label>
											<input type="radio" name="relacion111" id="relacion111" value="111"> Jefe o superior inmediato </label>
										<label>
											<input type="radio" name="relacion111" id="relacion111" value="112"> Cliente</label>
										<label>
											<input type="radio" name="relacion111" id="relacion111" value="113"> Director del departamento </label>
										<label>
											<input type="radio" name="relacion111" id="relacion111" value="114">Subordinado </label>
										<label>
											<input type="radio" name="relacion111" id="relacion111" value="115"> Cliente interno </label>
										<label>
											<input type="radio" name="relacion111" id="relacion111" value="116"> Autoevaluación: (Me evalúo a mi mismo) </label>
									</div>
								</div>				

								<div class="form-group">
									<label><h2>¿Con qué frecuencia interactúa con esta persona?</h2></label>
									<div class="radio-list">

										<select class="form-control" name="frecuencia" id="frecuencia">
											<option value="0">Seleccione</option>
											<option value="1">A diario</option>
											<option value="2">Dos o tres veces por semana</option>
											<option value="3">Una vez a la semana</option>
											<option value="4">Dos o tres veces al mes</option>
											<option value="5">Una vez al mes</option>
											<option value="6">Menos de una vez al mes</option>
										</select>
									</div>
								</div>

								<div class="form-group">
									<label><h2>¿Cómo evaluaría la labor de esta persona en conjunto?</h2></label>
									<div class="radio-list">
										<label>
											<input type="radio" name="relacion121" id="relacion121" value="120" checked> Excelente</label>
										<label>
											<input type="radio" name="relacion121" id="relacion121" value="121"> Muy buena </label>
										<label>
											<input type="radio" name="relacion121" id="relacion121" value="122"> Buena</label>
										<label>
											<input type="radio" name="relacion121" id="relacion121" value="123"> Mediocre </label>
										<label>
											<input type="radio" name="relacion121" id="relacion121" value="124"> Insuficiente </label>
									</div>
								</div>

							<div class="form-actions text-center">
								<a id="sig1"class="btn blue button-next">SIGUIENTE</a>
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
<!-- <script src="../../includes/assets/scripts/custom/table-managed.js"></script> -->
<script>
jQuery(document).ready(function() { 
   $("#personal").change(function(){
   		var personal=$("#personal").val();

	   	$.get("obtener_personal.php",{personal:personal},function(resultado){
	   		$("#cambiar1").empty();
	   		$("#cambiar1").append(resultado);
	   	});
   });

   $("#sig1").on("click", function()
   {
   		var personal=$("#personal").val();
		var relacion111=$("#relacion111:checked").val();
		var relacion121=$("#relacion121:checked").val();
		var frecuencia=$("#frecuencia :selected").val();
		
   		//alert(personal+" "+personal2);
   		if (personal=='') {
   			alert("Seleccione el empleado a evaluar");
   		}
   		else
		{	
			if (frecuencia=='0')			
			{
				alert("Ingrese la frecuencia con que interactúa con esta persona");
			}
			else
			{

				//alert(personal+" "+ relacion111+ ' ' + relacion121+ ' ' + frecuencia);
				location.href="evaluacion_personal360_2.php?personal="+personal+"&relacion111="+relacion111+"&relacion121="+relacion121+"&fecuencia="+frecuencia;

			}
		}

   })
});
</script>

</body>
</html>
