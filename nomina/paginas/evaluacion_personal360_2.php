<?php 
require_once '../../generalp.config.inc.php';
session_start();
ob_start();

error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require_once '../lib/common.php';
include ("func_bd.php");
$conexion=conexion();

$_SESSION['_personal']=$_REQUEST['personal'];
$_SESSION['_relacion111']=$_REQUEST['relacion111'];
$_SESSION['_relacion121']=$_REQUEST['relacion121'];
$_SESSION['_frecuencia']=$_REQUEST['frecuencia'];

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
							<h4>Actitud y habilidades sociales</h4>
						</div>
						<div class="portlet-body">
							<br>
							<div class="alert alert-info" role="alert">
								<h4>Utilice la siguiente escala para responder:</h4>
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
									<h3>Aptitudes generales:</h3>
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
											<div class="col-sm-6"><h4>Tiene una actitud positiva</h4>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion112" id="relacion112" value="111" autofocus> 	</label>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion112" id="relacion112" value="112">	</label>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion112" id="relacion112" value="113">	</label>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion112" id="relacion112" value="114" checked> 	</label>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion112" id="relacion112" value="115"> 	</label>

											</div>
										</div>
									</div>
									<div class="row">
										<div class="radio-list">
											<div class="col-sm-6"><h4>Resulta agradable trabajar con el/ella</h4>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion122" id="relacion122" value="121"> 	</label>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion122" id="relacion122" value="122">	</label>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion122" id="relacion122" value="123">	</label>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion122" id="relacion122" value="124" checked> 	</label>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion122" id="relacion122" value="125"> 	</label>

											</div>
										</div>
									</div>
									<div class="row">
										<div class="radio-list">
											<div class="col-sm-6"><h4>Acepta la crítica constructiva</h4>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion132" id="relacion132" value="131"> 	</label>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion132" id="relacion132" value="132">	</label>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion132" id="relacion132" value="133">	</label>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion132" id="relacion132" value="134" checked> 	</label>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion132" id="relacion132" value="135"> 	</label>

											</div>
										</div>
									</div>
									<div class="row">
										<div class="radio-list">
											<div class="col-sm-6"><h4>Tiene una imagen profesional</h4>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion142" id="relacion142" value="141"> 	</label>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion142" id="relacion142" value="142">	</label>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion142" id="relacion142" value="143">	</label>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion142" id="relacion142" value="144" checked> 	</label>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion142" id="relacion142" value="145"> 	</label>

											</div>
										</div>
									</div>	
								</div>	
								<div class="form-group">
									<div class="row">
										<div class="col-sm-12">
											<div><h3>¿Desea añadir algún comentario sobre la pregunta anterior?</h3></div>
											<div><textarea class="form-control" rows="4" placeholder="Comentario..."></textarea></div>
										</div>
										
									</div>
								</div>


								<div>
									<h3>Habilidades sociales:</h3>
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
											<div class="col-sm-6"><h4>Se comunica de una forma clara y sincera</h4>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion212" id="relacion212" value="211"> 	</label>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion212" id="relacion212" value="212">	</label>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion212" id="relacion212" value="213">	</label>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion212" id="relacion212" value="214" checked> 	</label>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion212" id="relacion212" value="215"> 	</label>

											</div>
										</div>
									</div>
									<div class="row">
										<div class="radio-list">
											<div class="col-sm-6"><h4>Escucha las ideas y sugerencias de otros</h4>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion222" id="relacion222" value="221"> 	</label>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion222" id="relacion222" value="222">	</label>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion222" id="relacion222" value="223">	</label>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion222" id="relacion222" value="224" checked> 	</label>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion222" id="relacion222" value="225"> 	</label>

											</div>
										</div>
									</div>
									<div class="row">
										<div class="radio-list">
											<div class="col-sm-6"><h4>Comparte la información con otros</h4>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion232" id="relacion232" value="231"> 	</label>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion232" id="relacion232" value="232">	</label>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion232" id="relacion232" value="233">	</label>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion232" id="relacion232" value="234" checked> 	</label>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion232" id="relacion232" value="235"> 	</label>

											</div>
										</div>
									</div>
									<div class="row">
										<div class="radio-list">
											<div class="col-sm-6"><h4>Tiene sentido del humor</h4>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion242" id="relacion242" value="241"> 	</label>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion242" id="relacion242" value="242">	</label>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion242" id="relacion242" value="243">	</label>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion242" id="relacion242" value="244" checked> 	</label>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion242" id="relacion242" value="245"> 	</label>

											</div>
										</div>
									</div>
									<div class="row">
										<div class="radio-list">
											<div class="col-sm-6"><h4>Consulta otros puntos de vista</h4>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion252" id="relacion252" value="251"> 	</label>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion252" id="relacion252" value="252">	</label>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion252" id="relacion252" value="253">	</label>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion252" id="relacion252" value="254" checked> 	</label>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion252" id="relacion252" value="255"> 	</label>

											</div>
										</div>
									</div>	
									<div class="row">
										<div class="radio-list">
											<div class="col-sm-6"><h4>Escucha sin interrumpir</h4>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion262" id="relacion262" value="261"> 	</label>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion262" id="relacion262" value="262">	</label>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion262" id="relacion262" value="263">	</label>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion262" id="relacion262" value="264" checked> 	</label>

											</div>
											<div class="col-sm-1 text-center"><label>
											<input type="radio" name="relacion262" id="relacion262" value="265"> 	</label>

											</div>
										</div>
									</div>										</div>
			


								<div class="form-group">
									<div class="row">
										<div class="col-sm-12">
											<div><h3>¿Desea añadir algún comentario sobre la pregunta anterior?</h3></div>
											<div><textarea id="comentario1" class="form-control" rows="4" placeholder="Comentario..."></textarea></div>
										</div>
										
									</div>
								</div>

							<div class="form-actions text-center">
								<a id="sig3" class="btn blue button-next">ATRÁS</a>
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
<!-- <script src="../../includes/assets/scripts/custom/table-managed.js"></script> -->
<script>
window.scrollTo(0,0);
jQuery(document).ready(function() {       
	$("#sig3").on("click", function()
	{	
		location.href="evaluacion_personal.php";
	});   

   $("#sig1").on("click", function()
   {
   		var relacion112=$("#relacion112:checked").val();
		var relacion122=$("#relacion122:checked").val();
		var relacion132=$("#relacion132:checked").val();
		var relacion142=$("#relacion142:checked").val();
		var relacion212=$("#relacion212:checked").val();
		var relacion222=$("#relacion222:checked").val();
		var relacion232=$("#relacion232:checked").val();
		var relacion242=$("#relacion242:checked").val();
		var relacion252=$("#relacion252:checked").val();
		var relacion262=$("#relacion262:checked").val();
		var comentario1=$("#comentario1").val();

			//alert(relacion112+" "+relacion122);

 		location.href="evaluacion_personal360_3.php?relacion112="+relacion112+"&relacion122="+relacion122+"&relacion132="+relacion132+"&relacion142="+relacion142+"&relacion212="+relacion212+"&relacion222="+relacion222+
 		"&relacion232="+relacion232+"&relacion242="+relacion242+"&relacion252="+relacion252+"&relacion262="+relacion262+
 		"&comentario1="+comentario1;
 	});

});
</script>

</body>
</html>