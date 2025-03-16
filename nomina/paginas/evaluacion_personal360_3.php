<?php 
require_once '../../generalp.config.inc.php';
session_start();
ob_start();

error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require_once '../lib/common.php';
include ("func_bd.php");
$conexion=conexion();

$_SESSION['_relacion112']=$_REQUEST['relacion112'];
$_SESSION['_relacion122']=$_REQUEST['relacion122'];
$_SESSION['_relacion132']=$_REQUEST['relacion132'];
$_SESSION['_relacion142']=$_REQUEST['relacion142'];
$_SESSION['_relacion212']=$_REQUEST['relacion212'];
$_SESSION['_relacion222']=$_REQUEST['relacion222'];
$_SESSION['_relacion232']=$_REQUEST['relacion232'];
$_SESSION['_relacion242']=$_REQUEST['relacion242'];
$_SESSION['_relacion252']=$_REQUEST['relacion252'];
$_SESSION['_relacion262']=$_REQUEST['relacion262'];
$_SESSION['_relacion312']=$_REQUEST['relacion312'];
$_SESSION['_relacion322']=$_REQUEST['relacion322'];
$_SESSION['_comentario1']=$_REQUEST['comentario1'];

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
							<h4>Relación con los clientes</h4>
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
									<h3>Relación con los clientes externos:</h3>
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
											<div class="col-sm-6"><h4>Se comunica con los clientes de forma directa y sincera</h4>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion113" id="relacion113" value="111" autofocus> 	</label>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion113" id="relacion113" value="112" checked>	</label>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion113" id="relacion113" value="113">	</label>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion113" id="relacion113" value="114" checked> 	</label>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion113" id="relacion113" value="115"> 	</label>

											</div>
										</div>
									</div>
									<div class="row">
										<div class="radio-list">
											<div class="col-sm-6"><h4>Da a los clientes máxima prioridad</h4>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion123" id="relacion123" value="121"> 	</label>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion123" id="relacion123" value="122">	</label>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion123" id="relacion123" value="123">	</label>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion123" id="relacion123" value="124" checked> 	</label>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion123" id="relacion123" value="125"> 	</label>

											</div>
										</div>
									</div>
									<div class="row">
										<div class="radio-list">
											<div class="col-sm-6"><h4>Establece una buena relación con los clientes actuales y potenciales</h4>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion133" id="relacion133" value="131"> 	</label>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion133" id="relacion133" value="132">	</label>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion133" id="relacion133" value="133">	</label>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion133" id="relacion133" value="134" checked> 	</label>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion133" id="relacion133" value="135"> 	</label>

											</div>
										</div>
									</div>
									<div class="row">
										<div class="radio-list">
											<div class="col-sm-6"><h4>Se centra en las prioridades de los clientes</h4>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion143" id="relacion143" value="141"> 	</label>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion143" id="relacion143" value="142">	</label>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion143" id="relacion143" value="143">	</label>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion143" id="relacion143" value="144" checked> 	</label>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion143" id="relacion143" value="145"> 	</label>

											</div>
										</div>
									</div>	
									<div class="row">
										<div class="radio-list">
											<div class="col-sm-6"><h4>Busca la forma de mejorar el servicio a los clientes</h4>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion153" id="relacion153" value="151"> 	</label>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion153" id="relacion153" value="152">	</label>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion153" id="relacion153" value="153">	</label>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion153" id="relacion153" value="154" checked> 	</label>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion153" id="relacion153" value="155"> 	</label>

											</div>
										</div>
									</div>
								</div>	
								<div class="form-group">
									<div class="row">
										<div class="col-sm-12">
											<div><h3>¿Desea añadir algún comentario sobre la pregunta anterior?</h3></div>
											<div><textarea class="form-control" id="comentario2" rows="4" placeholder="Comentario..."></textarea></div>
										</div>
										
									</div>
								</div>


								<div>
									<h3>Relación con los clientes internos:</h3>
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
											<div class="col-sm-6"><h4>Se comunica con los clientes internos de una forma clara y sincera</h4>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion213" id="relacion213" value="211"> 	</label>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion213" id="relacion213" value="212">	</label>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion213" id="relacion213" value="213">	</label>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion213" id="relacion213" value="214" checked> 	</label>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion213" id="relacion213" value="215"> 	</label>

											</div>
										</div>
									</div>
									<div class="row">
										<div class="radio-list">
											<div class="col-sm-6"><h4>Tiene una buena relación con los clientes internos</h4>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion223" id="relacion223" value="221"> 	</label>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion223" id="relacion223" value="222">	</label>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion223" id="relacion223" value="223">	</label>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion223" id="relacion223" value="224" checked> 	</label>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion223" id="relacion123" value="225"> 	</label>

											</div>
										</div>
									</div>
									<div class="row">
										<div class="radio-list">
											<div class="col-sm-6"><h4>Busca fórmulas de mejorar el servicio a los clientes interno</h4>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion233" id="relacion233" value="231"> 	</label>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion233" id="relacion233" value="232">	</label>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion233" id="relacion233" value="233">	</label>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion233" id="relacion233" value="234" checked> 	</label>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion233" id="relacion233" value="235"> 	</label>

											</div>
										</div>
									</div>	
								</div>
			


								<div class="form-group">
									<div class="row">
										<div class="col-sm-12">
											<div><h3>¿Desea añadir algún comentario sobre la pregunta anterior?</h3></div>
											<div><textarea id="comentario3" class="form-control" rows="4" placeholder="Comentario..."></textarea></div>
										</div>
										
									</div>
								</div>

							<div class="form-actions text-center">
								<a id="sig2" class="btn blue button-next">ATRÁS</a>
								<a id="sig3" class="btn blue button-next">SIGUIENTE</a>
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
	$("#sig2").on("click", function()
	{	
		location.href="evaluacion_personal360_2.php";
	});
	$("#sig3").on("click", function()
	{
		var relacion113=$("#relacion113:checked").val();
		var relacion123=$("#relacion123:checked").val();
		var relacion133=$("#relacion133:checked").val();
		var relacion143=$("#relacion143:checked").val();
		var relacion153=$("#relacion153:checked").val();
		var relacion213=$("#relacion213:checked").val();
		var relacion223=$("#relacion223:checked").val();
		var relacion233=$("#relacion233:checked").val();

		var comentario2=$("#comentario2").val();
		var comentario3=$("#comentario3").val();
		//alert(relacion112+" "+relacion122);

		location.href="evaluacion_personal360_4.php?relacion113="+relacion113+"&relacion123="+relacion123+
		"&relacion133="+relacion133+"&relacion143="+relacion143+"&relacion153="+relacion153+"&relacion213="+relacion213+
		"&relacion223="+relacion223+"&relacion233="+relacion233+"&comentario2="+comentario2+"&comentario3="+comentario3;
	});
});
</script>

</body>
</html>