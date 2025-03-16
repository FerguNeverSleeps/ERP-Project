<?php 
require_once '../../generalp.config.inc.php';
session_start();
ob_start();

error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require_once '../lib/common.php';
include ("func_bd.php");
$conexion=conexion();

	$relacion112=$_REQUEST['relacion112'];
	$relacion122=$_REQUEST['relacion122'];
	$relacion212=$_REQUEST['relacion212'];
	$relacion222=$_REQUEST['relacion222'];
	$relacion232=$_REQUEST['relacion232'];
	$relacion242=$_REQUEST['relacion242'];
	$relacion252=$_REQUEST['relacion252'];
	$relacion312=$_REQUEST['relacion312'];
	$relacion322=$_REQUEST['relacion322'];
	$relacion412=$_REQUEST['relacion412'];
	$relacion422=$_REQUEST['relacion422'];
	$relacion432=$_REQUEST['relacion432'];
	$relacion512=$_REQUEST['relacion512'];
	$relacion522=$_REQUEST['relacion522'];
	$relacion532=$_REQUEST['relacion532'];
	$relacion542=$_REQUEST['relacion542'];
	$relacion612=$_REQUEST['relacion612'];
	$relacion622=$_REQUEST['relacion622'];
	$relacion632=$_REQUEST['relacion632'];
	$relacion642=$_REQUEST['relacion642'];
	$relacion712=$_REQUEST['relacion712'];
	$relacion722=$_REQUEST['relacion722'];
	$relacion732=$_REQUEST['relacion732'];
	$relacion742=$_REQUEST['relacion742'];

	$_SESSION['_relacion112']=$_REQUEST['relacion112'];
	$_SESSION['_relacion122']=$_REQUEST['relacion122'];
	$_SESSION['_relacion212']=$_REQUEST['relacion212'];
	$_SESSION['_relacion222']=$_REQUEST['relacion222'];
	$_SESSION['_relacion232']=$_REQUEST['relacion232'];
	$_SESSION['_relacion242']=$_REQUEST['relacion242'];
	$_SESSION['_relacion252']=$_REQUEST['relacion252'];
	$_SESSION['_relacion312']=$_REQUEST['relacion312'];
	$_SESSION['_relacion322']=$_REQUEST['relacion322'];
	$_SESSION['_relacion412']=$_REQUEST['relacion412'];
	$_SESSION['_relacion422']=$_REQUEST['relacion422'];
	$_SESSION['_relacion432']=$_REQUEST['relacion432'];
	$_SESSION['_relacion512']=$_REQUEST['relacion512'];
	$_SESSION['_relacion522']=$_REQUEST['relacion522'];
	$_SESSION['_relacion532']=$_REQUEST['relacion532'];
	$_SESSION['_relacion542']=$_REQUEST['relacion542'];
	$_SESSION['_relacion612']=$_REQUEST['relacion612'];
	$_SESSION['_relacion622']=$_REQUEST['relacion622'];
	$_SESSION['_relacion632']=$_REQUEST['relacion632'];
	$_SESSION['_relacion642']=$_REQUEST['relacion642'];
	$_SESSION['_relacion712']=$_REQUEST['relacion712'];
	$_SESSION['_relacion722']=$_REQUEST['relacion722'];
	$_SESSION['_relacion732']=$_REQUEST['relacion732'];
	$_SESSION['_relacion742']=$_REQUEST['relacion742'];


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
							<h4>Evaluación desempeño del empleado</h4>
						</div>
						<div class="portlet-body">

								<div>
									<h3>Evaluación general y comentarios</h3>
								</div>
								<div>
									<h5>Por favor, resuma su evaluación del empleado; en su opinión, el empleado merece una calificación:</h5>
								</div>
								<div class="form-group">
									<div class="row">
										<div class="radio-list">
											<div class="col-sm-2 text-center"><label>Pobre</label></div>
											<div class="col-sm-2 text-center"><label>Media</label></div>
											<div class="col-sm-2 text-center"><label>Buena</label></div>
											<div class="col-sm-2 text-center"><label>Muy Buena</label></div>
											<div class="col-sm-2 text-center"><label>Excelente</label></div>
										</div>
									</div>
									<div class="row">
										<div class="radio-list">
											<div class="col-sm-2 text-center"><label>
											<input type="radio" name="relacion123" id="relacion113" value="110" autofocus></label>

											</div>
											<div class="col-sm-2 text-center"><label>
											<input type="radio" name="relacion123" id="relacion113" value="111"></label>

											</div>
											<div class="col-sm-2 text-center"><label>
											<input type="radio" name="relacion123" id="relacion113" value="112"></label>

											</div>
											<div class="col-sm-2 text-center"><label>
											<input type="radio" name="relacion123" id="relacion113" value="113"></label>

											</div>
											<div class="col-sm-2 text-center"><label>
											<input type="radio" name="relacion123" id="relacion113" value="114" checked></label>

											</div>
										</div>
									</div>									
								</div>

			

								<div class="form-group">
									<div class="row">
										<div class="col-sm-12">
											<div><h3>En su opinión, ¿en qué áreas debe concentrar el empleado sus esfuerzos de mejora?</h3>
											</div>
										</div>
										<div class="col-sm-12">
											<div><h4>1.-</h4></div>
												<input type="text" id="opinion1" class="form-control" placeholder="Escriba su opinión...">
										</div>
										<div class="col-sm-12">
											<div><h4>2.-</h4></div>
												<input type="text" id="opinion2" class="form-control" placeholder="Escriba su opinión...">
										</div>
										<div class="col-sm-12">
											<div><h4>3.-</h4></div>
												<input type="text" id="opinion3" class="form-control" placeholder="Escriba su opinión...">
										</div>																				
									</div>
								</div>

								<div class="form-group">
									<div class="row">
										<div class="col-sm-12">
											<div><h3>Por favor, introduzca cualquier comentario que desee sobre el empleado:</h3></div>
											<div><textarea class="form-control"  id="comentario1" rows="4" placeholder="Comentario..."></textarea></div>
										</div>
										
									</div>
								</div>

							<div class="form-actions text-center">
								<a href="evaluacion_empleado_2.php" class="btn blue button-next">ATRÁS</a>
								<a id="sig3" href="#" class="btn blue button-next">SIGUIENTE</a>
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

$("#sig3").on("click",function(){
	relacion113=$("#relacion113:checked").val();
	opinion1=$("#opinion1").val();
	opinion2=$("#opinion2").val();
	opinion3=$("#opinion3").val();
	comentario1=$("#comentario1").val();
	location.href="evaluar_empleado.php?relacion113="+relacion113+"&opinion1="+opinion1+"&opinion2="+opinion2+"&opinion3="+opinion3+"&comentario1="+comentario1;
  
});
	// TableManaged.init();
 // $('#sample_1').DataTable(); 
});
</script>

</body>
</html>