<?php 
require_once '../../generalp.config.inc.php';
session_start();
ob_start();

error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require_once '../lib/common.php';
include ("func_bd.php");
$conexion=conexion();


$_SESSION['_relacion113']=$_REQUEST['relacion113'];
$_SESSION['_relacion123']=$_REQUEST['relacion123'];
$_SESSION['_relacion133']=$_REQUEST['relacion133'];
$_SESSION['_relacion143']=$_REQUEST['relacion143'];
$_SESSION['_relacion153']=$_REQUEST['relacion153'];
$_SESSION['_relacion213']=$_REQUEST['relacion213'];
$_SESSION['_relacion223']=$_REQUEST['relacion223'];
$_SESSION['_relacion233']=$_REQUEST['relacion233'];
$_SESSION['_comentario2']=$_REQUEST['comentario2'];
$_SESSION['_comentario3']=$_REQUEST['comentario3'];


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
							<h4>
								Objetivos y capacidad de resolución
							</h4>
						</div>
						<div class="portlet-body">
							<br>
							<div class="alert alert-info" role="alert">

								<h5>Utilice la siguiente escala para responder:</h5>
									<ul>
										<li>
											 <h5>1- No estoy de acuerdo</h5>
										 </li>
										 <li>
											 <h5>2- Ni de acuerdo ni en desacuerdo</h5>
										 </li>
										 <li>
											 <h5>3 -De acuerdo</h5>
										 </li>
										 <li>
											 <h5>4- Completamente de acuerdo</h5>
										 </li>
										 <li>
											<h5>N/A: No aplica</h5>
										 </li>
									</ul>
							</div>
								<div>
									<h3>Resultados y capacidad de resolución:</h3>
								</div>				
								<div class="form-group">
									<div class="row">
										<div class="radio-list">
											<div class="col-sm-6">&nbsp;</div>
											<div class="col-sm-1 text-center"><label>1</label>

											</div>
											<div class="col-sm-1 text-center"><label>2</label>

											</div>
											<div class="col-sm-1 text-center"><label>3</label>

											</div>
											<div class="col-sm-1 text-center"><label>4</label>

											</div>
											<div class="col-sm-1 text-center"><label>N/A</label>

											</div>
										</div>
									</div>

									<div class="row">
										<div class="radio-list">
											<div class="col-sm-6"><h4>Confronta situaciones de conflicto de forma directa y honesta</h4>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion114" id="relacion114" value="111" autofocus> 	</label>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion114" id="relacion114" value="112">	</label>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion114" id="relacion114" value="113">	</label>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion114" id="relacion114" value="114" checked> 	</label>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion114" id="relacion114" value="115"> 	</label>

											</div>
										</div>
									</div>
									<div class="row">
										<div class="radio-list">
											<div class="col-sm-6"><h4>Cumple plazos y objetivos</h4>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion124" id="relacion124" value="121"> 	</label>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion124" id="relacion124" value="122">	</label>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion124" id="relacion124" value="123">	</label>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion124" id="relacion124" value="124" checked> 	</label>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion124" id="relacion124" value="125"> 	</label>

											</div>
										</div>
									</div>
									<div class="row">
										<div class="radio-list">
											<div class="col-sm-6"><h4>Utiliza su tiempo de forma eficiente</h4>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion134" id="relacion134" value="131"> 	</label>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion134" id="relacion134" value="132">	</label>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion134" id="relacion134" value="133">	</label>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion134" id="relacion134" value="134" checked> 	</label>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion134" id="relacion134" value="135"> 	</label>

											</div>
										</div>
									</div>
									<div class="row">
										<div class="radio-list">
											<div class="col-sm-6"><h4>Tiene sentido de la prioridad</h4>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion144" id="relacion144" value="141"> 	</label>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion144" id="relacion144" value="142">	</label>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion144" id="relacion144" value="143">	</label>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion144" id="relacion144" value="144" checked> 	</label>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion144" id="relacion144" value="145"> 	</label>

											</div>
										</div>
									</div>	
									<div class="row">
										<div class="radio-list">
											<div class="col-sm-6"><h4>Busca la forma de mejorar el servicio a los clientes</h4>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion154" id="relacion154" value="151"> 	</label>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion154" id="relacion154" value="152">	</label>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion154" id="relacion154" value="153">	</label>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion154" id="relacion154" value="154" checked> 	</label>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion154" id="relacion154" value="155"> 	</label>

											</div>
										</div>
									</div>
									<div class="row">
										<div class="radio-list">
											<div class="col-sm-6"><h4>Puede manejar varias tareas al mismo tiempo</h4>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion164" id="relacion164" value="161"> 	</label>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion164" id="relacion164" value="162">	</label>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion164" id="relacion164" value="163">	</label>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion164" id="relacion164" value="164" checked> 	</label>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion164" id="relacion164" value="165"> 	</label>

											</div>
										</div>
									</div>
									<div class="row">
										<div class="radio-list">
											<div class="col-sm-6"><h4>Se responsabiliza de los resultados de su trabajo</h4>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion174" id="relacion174" value="171"> 	</label>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion174" id="relacion174" value="172">	</label>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion174" id="relacion174" value="173">	</label>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion174" id="relacion174" value="174" checked> 	</label>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion174" id="relacion174" value="175"> 	</label>

											</div>
										</div>
									</div>									

								</div>	
								<div class="form-group">
									<div class="row">
										<div class="col-sm-12">
											<div><h3>¿Desea añadir algún comentario sobre la pregunta anterior?</h3></div>
											<div><textarea id="comentario4" class="form-control" rows="4" placeholder="Comentario..."></textarea></div>
										</div>
										
									</div>
								</div>

							<div class="form-actions text-center">
								<a id="sig3" class="btn blue button-next">ATRÁS</a>
								<a id="sig2" class="btn blue button-next">SIGUIENTE</a>
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
window.scrollTo(0,0);
jQuery(document).ready(function() {       
	$("#sig3").on("click", function()
	{	
		location.href="evaluacion_personal360_3.php";
	});
	$("#sig2").on("click", function()
	{
		var relacion114=$("#relacion114:checked").val();
		var relacion124=$("#relacion124:checked").val();
		var relacion134=$("#relacion134:checked").val();
		var relacion144=$("#relacion144:checked").val();
		var relacion154=$("#relacion154:checked").val();
		var relacion164=$("#relacion164:checked").val();
		var relacion174=$("#relacion174:checked").val();

		var comentario4=$("#comentario4").val();
		//alert(relacion112+" "+relacion122);

		location.href="evaluacion_personal360_5.php?relacion114="+relacion114+"&relacion124="+relacion124+
		"&relacion134="+relacion134+"&relacion144="+relacion144+"&relacion154="+relacion154+"&relacion164="+relacion164+
		"&relacion174="+relacion174+"&comentario4="+comentario4;
	});
});
</script>

</body>
</html>