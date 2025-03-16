<?php 
require_once '../../generalp.config.inc.php';
session_start();
ob_start();

error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require_once '../lib/common.php';
include ("func_bd.php");
$conexion=conexion();

//echo $_REQUEST['personal']." ".$_REQUEST['personal2'];

$_SESSION['_personal']=$_REQUEST['personal'];
$_SESSION['_personal2']=$_REQUEST['personal2'];

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
								<h3>Evaluación del empleado</h3>
							</div>
							<div class="alert alert-info" role="alert">
								<h5>Puntúe del 1 al 10 los siguientes aspectos, siendo 1 "Muy pobre" y 10 "Excelente"</h5>
							</div>

							<div>
								<h3>Conocimiento del puesto:</h3>
							</div>
							<table class="table table-striped">
								<thead>
									<tr>
										<th width="50%"></th>
										<th width="5%">1</th>
										<th width="5%">2</th>
										<th width="5%">3</th>
										<th width="5%">4</th>
										<th width="5%">5</th>
										<th width="5%">6</th>
										<th width="5%">7</th>
										<th width="5%">8</th>
										<th width="5%">9</th>
										<th width="5%">10</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td width="50%"><h4>El empleado entiende las funciones y responsabilidades del puesto</h4></td>
										<td width="5%"><input type="radio" name="relacion112" id="relacion112"  value="110" autofocus></td>
										<td width="5%"><input type="radio" name="relacion112" id="relacion112"  value="111"></td>
										<td width="5%"><input type="radio" name="relacion112" id="relacion112"  value="112"></td>
										<td width="5%"><input type="radio" name="relacion112" id="relacion112"  value="113"></td>
										<td width="5%"><input type="radio" name="relacion112" id="relacion112"  value="114"></td>
										<td width="5%"><input type="radio" name="relacion112" id="relacion112"  value="115"></td>
										<td width="5%"><input type="radio" name="relacion112" id="relacion112"  value="116"></td>
										<td width="5%"><input type="radio" name="relacion112" id="relacion112"  value="117"></td>
										<td width="5%"><input type="radio" name="relacion112" id="relacion112"  value="118"></td>
										<td width="5%"><input type="radio" name="relacion112" id="relacion112"  value="119" checked></td>
									</tr>									

									<tr>
										<td width="50%"><h4>El empleado posee los conocimientos y habilidades necesarios para el puesto</h4></td>
										<td width="5%"><input type="radio" name="relacion122" id="relacion122"  value="120"></td>
										<td width="5%"><input type="radio" name="relacion122" id="relacion122"  value="121"></td>
										<td width="5%"><input type="radio" name="relacion122" id="relacion122"  value="122"></td>
										<td width="5%"><input type="radio" name="relacion122" id="relacion122"  value="123"></td>
										<td width="5%"><input type="radio" name="relacion122" id="relacion122"  value="124"></td>
										<td width="5%"><input type="radio" name="relacion122" id="relacion122"  value="125"></td>
										<td width="5%"><input type="radio" name="relacion122" id="relacion122"  value="126"></td>
										<td width="5%"><input type="radio" name="relacion122" id="relacion122"  value="127"></td>
										<td width="5%"><input type="radio" name="relacion122" id="relacion122"  value="128"></td>
										<td width="5%"><input type="radio" name="relacion122" id="relacion122"  value="129" checked></td>
									</tr>	

								</tbody>
							</table>		

							<div>
								<h3>Planificación y resolución:</h3>
							</div>
							<table class="table table-striped">
								<thead>
									<tr>
										<th width="50%"></th>
										<th width="5%">1</th>
										<th width="5%">2</th>
										<th width="5%">3</th>
										<th width="5%">4</th>
										<th width="5%">5</th>
										<th width="5%">6</th>
										<th width="5%">7</th>
										<th width="5%">8</th>
										<th width="5%">9</th>
										<th width="5%">10</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td width="50%"><h4>El empleado requiere una supervisión mínima</h4></td>
										<td width="5%"><input type="radio" name="relacion212" id="relacion212"  value="210"></td>
										<td width="5%"><input type="radio" name="relacion212" id="relacion212"  value="211"></td>
										<td width="5%"><input type="radio" name="relacion212" id="relacion212"  value="212"></td>
										<td width="5%"><input type="radio" name="relacion212" id="relacion212"  value="213"></td>
										<td width="5%"><input type="radio" name="relacion212" id="relacion212"  value="214"></td>
										<td width="5%"><input type="radio" name="relacion212" id="relacion212"  value="215"></td>
										<td width="5%"><input type="radio" name="relacion212" id="relacion212"  value="216"></td>
										<td width="5%"><input type="radio" name="relacion212" id="relacion212"  value="217"></td>
										<td width="5%"><input type="radio" name="relacion212" id="relacion212"  value="218"></td>
										<td width="5%"><input type="radio" name="relacion212" id="relacion212"  value="219" checked></td>
									</tr>									

									<tr>
										<td width="50%"><h4>El empleado trabaja de forma organizada</h4></td>
										<td width="5%"><input type="radio" name="relacion222" id="relacion222"  value="220"></td>
										<td width="5%"><input type="radio" name="relacion222" id="relacion222"  value="221"></td>
										<td width="5%"><input type="radio" name="relacion222" id="relacion222"  value="222"></td>
										<td width="5%"><input type="radio" name="relacion222" id="relacion222"  value="223"></td>
										<td width="5%"><input type="radio" name="relacion222" id="relacion222"  value="224"></td>
										<td width="5%"><input type="radio" name="relacion222" id="relacion222"  value="225"></td>
										<td width="5%"><input type="radio" name="relacion222" id="relacion222"  value="226"></td>
										<td width="5%"><input type="radio" name="relacion222" id="relacion222"  value="227"></td>
										<td width="5%"><input type="radio" name="relacion222" id="relacion222"  value="228"></td>
										<td width="5%"><input type="radio" name="relacion222" id="relacion222"  value="229" checked></td>
									</tr>	

									<tr>
										<td width="50%"><h4>El empleado es capaz de identificar problemas</h4></td>
										<td width="5%"><input type="radio" name="relacion232" id="relacion232"  value="230"></td>
										<td width="5%"><input type="radio" name="relacion232" id="relacion232"  value="231"></td>
										<td width="5%"><input type="radio" name="relacion232" id="relacion232"  value="232"></td>
										<td width="5%"><input type="radio" name="relacion232" id="relacion232"  value="233"></td>
										<td width="5%"><input type="radio" name="relacion232" id="relacion232"  value="234"></td>
										<td width="5%"><input type="radio" name="relacion232" id="relacion232"  value="235"></td>
										<td width="5%"><input type="radio" name="relacion232" id="relacion232"  value="236"></td>
										<td width="5%"><input type="radio" name="relacion232" id="relacion232"  value="237"></td>
										<td width="5%"><input type="radio" name="relacion232" id="relacion232"  value="238"></td>
										<td width="5%"><input type="radio" name="relacion232" id="relacion232"  value="239" checked></td>
									</tr>									

									<tr>
										<td width="50%"><h4>El empleado es capaz de solucionar problemas</h4></td>
										<td width="5%"><input type="radio" name="relacion242" id="relacion242"  value="240"></td>
										<td width="5%"><input type="radio" name="relacion242" id="relacion242"  value="241"></td>
										<td width="5%"><input type="radio" name="relacion242" id="relacion242"  value="242"></td>
										<td width="5%"><input type="radio" name="relacion242" id="relacion242"  value="243"></td>
										<td width="5%"><input type="radio" name="relacion242" id="relacion242"  value="244"></td>
										<td width="5%"><input type="radio" name="relacion242" id="relacion242"  value="245"></td>
										<td width="5%"><input type="radio" name="relacion242" id="relacion242"  value="246"></td>
										<td width="5%"><input type="radio" name="relacion242" id="relacion242"  value="247"></td>
										<td width="5%"><input type="radio" name="relacion242" id="relacion242"  value="248"></td>
										<td width="5%"><input type="radio" name="relacion242" id="relacion242"  value="249" checked></td>
									</tr>	

									<tr>
										<td width="50%"><h4>El empleado reacciona rápidamente ante las dificultades</h4></td>
										<td width="5%"><input type="radio" name="relacion252" id="relacion252"  value="250"></td>
										<td width="5%"><input type="radio" name="relacion252" id="relacion252"  value="251"></td>
										<td width="5%"><input type="radio" name="relacion252" id="relacion252"  value="252"></td>
										<td width="5%"><input type="radio" name="relacion252" id="relacion252"  value="253"></td>
										<td width="5%"><input type="radio" name="relacion252" id="relacion252"  value="254"></td>
										<td width="5%"><input type="radio" name="relacion252" id="relacion252"  value="255"></td>
										<td width="5%"><input type="radio" name="relacion252" id="relacion252"  value="256"></td>
										<td width="5%"><input type="radio" name="relacion252" id="relacion252"  value="257"></td>
										<td width="5%"><input type="radio" name="relacion252" id="relacion252"  value="258"></td>
										<td width="5%"><input type="radio" name="relacion252" id="relacion252"  value="259" checked></td>
									</tr>	

								</tbody>
								
							</table>	

							<div>
								<h3>Evaluación del empleado</h3>
							</div>
							<table class="table table-striped">
								<thead>
									<tr>
										<th width="50%"></th>
										<th width="5%">1</th>
										<th width="5%">2</th>
										<th width="5%">3</th>
										<th width="5%">4</th>
										<th width="5%">5</th>
										<th width="5%">6</th>
										<th width="5%">7</th>
										<th width="5%">8</th>
										<th width="5%">9</th>
										<th width="5%">10</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td width="50%"><h4>El empleado entiende las funciones y responsabilidades del puesto</h4></td>
										<td width="5%"><input type="radio" name="relacion312" id="relacion312"  value="310"></td>
										<td width="5%"><input type="radio" name="relacion312" id="relacion312"  value="311"></td>
										<td width="5%"><input type="radio" name="relacion312" id="relacion312"  value="312"></td>
										<td width="5%"><input type="radio" name="relacion312" id="relacion312"  value="313"></td>
										<td width="5%"><input type="radio" name="relacion312" id="relacion312"  value="314"></td>
										<td width="5%"><input type="radio" name="relacion312" id="relacion312"  value="315"></td>
										<td width="5%"><input type="radio" name="relacion312" id="relacion312"  value="316"></td>
										<td width="5%"><input type="radio" name="relacion312" id="relacion312"  value="317"></td>
										<td width="5%"><input type="radio" name="relacion312" id="relacion312"  value="318"></td>
										<td width="5%"><input type="radio" name="relacion312" id="relacion312"  value="319" checked></td>
									</tr>									

									<tr>
										<td width="50%"><h4>El empleado posee los conocimientos y habilidades necesarios para el puesto</h4></td>
										<td width="5%"><input type="radio" name="relacion322" id="relacion322"  value="320"></td>
										<td width="5%"><input type="radio" name="relacion322" id="relacion322"  value="321"></td>
										<td width="5%"><input type="radio" name="relacion322" id="relacion322"  value="322"></td>
										<td width="5%"><input type="radio" name="relacion322" id="relacion322"  value="323"></td>
										<td width="5%"><input type="radio" name="relacion322" id="relacion322"  value="324"></td>
										<td width="5%"><input type="radio" name="relacion322" id="relacion322"  value="325"></td>
										<td width="5%"><input type="radio" name="relacion322" id="relacion322"  value="326"></td>
										<td width="5%"><input type="radio" name="relacion322" id="relacion322"  value="327"></td>
										<td width="5%"><input type="radio" name="relacion322" id="relacion322"  value="328"></td>
										<td width="5%"><input type="radio" name="relacion322" id="relacion322"  value="329" checked></td>
									</tr>	

								</tbody>
							</table>		

							<div>
								<h3>Productividad:</h3>
							</div>
							<table class="table table-striped">
								<thead>
									<tr>
										<th width="50%"></th>
										<th width="5%">1</th>
										<th width="5%">2</th>
										<th width="5%">3</th>
										<th width="5%">4</th>
										<th width="5%">5</th>
										<th width="5%">6</th>
										<th width="5%">7</th>
										<th width="5%">8</th>
										<th width="5%">9</th>
										<th width="5%">10</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td width="50%"><h4>El empleado consigue los objetivos</h4></td>
										<td width="5%"><input type="radio" name="relacion412" id="relacion412"  value="410"></td>
										<td width="5%"><input type="radio" name="relacion412" id="relacion412"  value="411"></td>
										<td width="5%"><input type="radio" name="relacion412" id="relacion412"  value="412"></td>
										<td width="5%"><input type="radio" name="relacion412" id="relacion412"  value="413"></td>
										<td width="5%"><input type="radio" name="relacion412" id="relacion412"  value="414"></td>
										<td width="5%"><input type="radio" name="relacion412" id="relacion412"  value="415"></td>
										<td width="5%"><input type="radio" name="relacion412" id="relacion412"  value="416"></td>
										<td width="5%"><input type="radio" name="relacion412" id="relacion412"  value="417"></td>
										<td width="5%"><input type="radio" name="relacion412" id="relacion412"  value="418"></td>
										<td width="5%"><input type="radio" name="relacion412" id="relacion412"  value="419" checked></td>
									</tr>									

									<tr>
										<td width="50%"><h4>El empleado puede manejar varios proyectos a la vez</h4></td>
										<td width="5%"><input type="radio" name="relacion422" id="relacion422"  value="420"></td>
										<td width="5%"><input type="radio" name="relacion422" id="relacion422"  value="421"></td>
										<td width="5%"><input type="radio" name="relacion422" id="relacion422"  value="422"></td>
										<td width="5%"><input type="radio" name="relacion422" id="relacion422"  value="423"></td>
										<td width="5%"><input type="radio" name="relacion422" id="relacion422"  value="424"></td>
										<td width="5%"><input type="radio" name="relacion422" id="relacion422"  value="425"></td>
										<td width="5%"><input type="radio" name="relacion422" id="relacion422"  value="426"></td>
										<td width="5%"><input type="radio" name="relacion422" id="relacion422"  value="427"></td>
										<td width="5%"><input type="radio" name="relacion422" id="relacion422"  value="428"></td>
										<td width="5%"><input type="radio" name="relacion422" id="relacion422"  value="429" checked></td>
									</tr>	

									<tr>
										<td width="50%"><h4>El empleado consigue los estándares de productividad</h4></td>
										<td width="5%"><input type="radio" name="relacion432" id="relacion432"  value="430"></td>
										<td width="5%"><input type="radio" name="relacion432" id="relacion432"  value="431"></td>
										<td width="5%"><input type="radio" name="relacion432" id="relacion432"  value="432"></td>
										<td width="5%"><input type="radio" name="relacion432" id="relacion432"  value="433"></td>
										<td width="5%"><input type="radio" name="relacion432" id="relacion432"  value="434"></td>
										<td width="5%"><input type="radio" name="relacion432" id="relacion432"  value="435"></td>
										<td width="5%"><input type="radio" name="relacion432" id="relacion432"  value="436"></td>
										<td width="5%"><input type="radio" name="relacion432" id="relacion432"  value="437"></td>
										<td width="5%"><input type="radio" name="relacion432" id="relacion432"  value="438"></td>
										<td width="5%"><input type="radio" name="relacion432" id="relacion432"  value="439" checked></td>
									</tr>

								</tbody>
								
							</table>
							<div>
								<h3>Trabajo en equipo:</h3>
							</div>
							<table class="table table-striped">
								<thead>
									<tr>
										<th width="50%"></th>
										<th width="5%">1</th>
										<th width="5%">2</th>
										<th width="5%">3</th>
										<th width="5%">4</th>
										<th width="5%">5</th>
										<th width="5%">6</th>
										<th width="5%">7</th>
										<th width="5%">8</th>
										<th width="5%">9</th>
										<th width="5%">10</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td width="50%"><h4>El empleado sabe trabajar en equipo</h4></td>
										<td width="5%"><input type="radio" name="relacion512" id="relacion512"  value="510"></td>
										<td width="5%"><input type="radio" name="relacion512" id="relacion512"  value="511"></td>
										<td width="5%"><input type="radio" name="relacion512" id="relacion512"  value="512"></td>
										<td width="5%"><input type="radio" name="relacion512" id="relacion512"  value="513"></td>
										<td width="5%"><input type="radio" name="relacion512" id="relacion512"  value="514"></td>
										<td width="5%"><input type="radio" name="relacion512" id="relacion512"  value="515"></td>
										<td width="5%"><input type="radio" name="relacion512" id="relacion512"  value="516"></td>
										<td width="5%"><input type="radio" name="relacion512" id="relacion512"  value="517"></td>
										<td width="5%"><input type="radio" name="relacion512" id="relacion512"  value="518"></td>
										<td width="5%"><input type="radio" name="relacion512" id="relacion512"  value="519" checked></td>
									</tr>									

									<tr>
										<td width="50%"><h4>El empleado ayuda a su equipo</h4></td>
										<td width="5%"><input type="radio" name="relacion522" id="relacion522"  value="520"></td>
										<td width="5%"><input type="radio" name="relacion522" id="relacion522"  value="521"></td>
										<td width="5%"><input type="radio" name="relacion522" id="relacion522"  value="522"></td>
										<td width="5%"><input type="radio" name="relacion522" id="relacion522"  value="523"></td>
										<td width="5%"><input type="radio" name="relacion522" id="relacion522"  value="524"></td>
										<td width="5%"><input type="radio" name="relacion522" id="relacion522"  value="525"></td>
										<td width="5%"><input type="radio" name="relacion522" id="relacion522"  value="526"></td>
										<td width="5%"><input type="radio" name="relacion522" id="relacion522"  value="527"></td>
										<td width="5%"><input type="radio" name="relacion522" id="relacion522"  value="528"></td>
										<td width="5%"><input type="radio" name="relacion522" id="relacion522"  value="529" checked></td>
									</tr>	

									<tr>
										<td width="50%"><h4>El empleado trabaja bien con diferentes tipos de persona</h4></td>
										<td width="5%"><input type="radio" name="relacion532" id="relacion532"  value="530"></td>
										<td width="5%"><input type="radio" name="relacion532" id="relacion532"  value="531"></td>
										<td width="5%"><input type="radio" name="relacion532" id="relacion532"  value="532"></td>
										<td width="5%"><input type="radio" name="relacion532" id="relacion532"  value="533"></td>
										<td width="5%"><input type="radio" name="relacion532" id="relacion532"  value="534"></td>
										<td width="5%"><input type="radio" name="relacion532" id="relacion532"  value="535"></td>
										<td width="5%"><input type="radio" name="relacion532" id="relacion532"  value="536"></td>
										<td width="5%"><input type="radio" name="relacion532" id="relacion532"  value="537"></td>
										<td width="5%"><input type="radio" name="relacion532" id="relacion532"  value="538"></td>
										<td width="5%"><input type="radio" name="relacion532" id="relacion532"  value="539" checked></td>
									</tr>									

									<tr>
										<td width="50%"><h4>El empleado participa en conversaciones de grupo</h4></td>
										<td width="5%"><input type="radio" name="relacion542" id="relacion542"  value="540"></td>
										<td width="5%"><input type="radio" name="relacion542" id="relacion542"  value="541"></td>
										<td width="5%"><input type="radio" name="relacion542" id="relacion542"  value="542"></td>
										<td width="5%"><input type="radio" name="relacion542" id="relacion542"  value="543"></td>
										<td width="5%"><input type="radio" name="relacion542" id="relacion542"  value="544"></td>
										<td width="5%"><input type="radio" name="relacion542" id="relacion542"  value="545"></td>
										<td width="5%"><input type="radio" name="relacion542" id="relacion542"  value="546"></td>
										<td width="5%"><input type="radio" name="relacion542" id="relacion542"  value="547"></td>
										<td width="5%"><input type="radio" name="relacion542" id="relacion542"  value="548"></td>
										<td width="5%"><input type="radio" name="relacion542" id="relacion542"  value="549" checked></td>
									</tr>	

								</tbody>
								
							</table>

							<div>
								<h3>Habilidades de comunicación:</h3>
							</div>
							<table class="table table-striped">
								<thead>
									<tr>
										<th width="50%"></th>
										<th width="5%">1</th>
										<th width="5%">2</th>
										<th width="5%">3</th>
										<th width="5%">4</th>
										<th width="5%">5</th>
										<th width="5%">6</th>
										<th width="5%">7</th>
										<th width="5%">8</th>
										<th width="5%">9</th>
										<th width="5%">10</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td width="50%"><h4>El empleado participa en las reuniones</h4></td>
										<td width="5%"><input type="radio" name="relacion612" id="relacion612"  value="610"></td>
										<td width="5%"><input type="radio" name="relacion612" id="relacion612"  value="611"></td>
										<td width="5%"><input type="radio" name="relacion612" id="relacion612"  value="612"></td>
										<td width="5%"><input type="radio" name="relacion612" id="relacion612"  value="613"></td>
										<td width="5%"><input type="radio" name="relacion612" id="relacion612"  value="614"></td>
										<td width="5%"><input type="radio" name="relacion612" id="relacion612"  value="615"></td>
										<td width="5%"><input type="radio" name="relacion612" id="relacion612"  value="616"></td>
										<td width="5%"><input type="radio" name="relacion612" id="relacion612"  value="617"></td>
										<td width="5%"><input type="radio" name="relacion612" id="relacion612"  value="618"></td>
										<td width="5%"><input type="radio" name="relacion612" id="relacion612"  value="619" checked></td>
									</tr>									

									<tr>
										<td width="50%"><h4>El empleado se explica de forma clara y fácil de entender</h4></td>
										<td width="5%"><input type="radio" name="relacion622" id="relacion622"  value="620"></td>
										<td width="5%"><input type="radio" name="relacion622" id="relacion622"  value="621"></td>
										<td width="5%"><input type="radio" name="relacion622" id="relacion622"  value="622"></td>
										<td width="5%"><input type="radio" name="relacion622" id="relacion622"  value="623"></td>
										<td width="5%"><input type="radio" name="relacion622" id="relacion622"  value="624"></td>
										<td width="5%"><input type="radio" name="relacion622" id="relacion622"  value="625"></td>
										<td width="5%"><input type="radio" name="relacion622" id="relacion622"  value="626"></td>
										<td width="5%"><input type="radio" name="relacion622" id="relacion622"  value="627"></td>
										<td width="5%"><input type="radio" name="relacion622" id="relacion622"  value="628"></td>
										<td width="5%"><input type="radio" name="relacion622" id="relacion622"  value="629" checked></td>
									</tr>	

									<tr>
										<td width="50%"><h4>El empleado sabe escuchar</h4></td>
										<td width="5%"><input type="radio" name="relacion632" id="relacion632"  value="630"></td>
										<td width="5%"><input type="radio" name="relacion632" id="relacion632"  value="631"></td>
										<td width="5%"><input type="radio" name="relacion632" id="relacion632"  value="632"></td>
										<td width="5%"><input type="radio" name="relacion632" id="relacion632"  value="633"></td>
										<td width="5%"><input type="radio" name="relacion632" id="relacion632"  value="634"></td>
										<td width="5%"><input type="radio" name="relacion632" id="relacion632"  value="635"></td>
										<td width="5%"><input type="radio" name="relacion632" id="relacion632"  value="636"></td>
										<td width="5%"><input type="radio" name="relacion632" id="relacion632"  value="637"></td>
										<td width="5%"><input type="radio" name="relacion632" id="relacion632"  value="638"></td>
										<td width="5%"><input type="radio" name="relacion632" id="relacion632"  value="639" checked></td>
									</tr>									

									<tr>
										<td width="50%"><h4>El empleado expone sus ideas de forma eficaz</h4></td>
										<td width="5%"><input type="radio" name="relacion642" id="relacion642" value="640"></td>
										<td width="5%"><input type="radio" name="relacion642" id="relacion642" value="641"></td>
										<td width="5%"><input type="radio" name="relacion642" id="relacion642" value="642"></td>
										<td width="5%"><input type="radio" name="relacion642" id="relacion642" value="643"></td>
										<td width="5%"><input type="radio" name="relacion642" id="relacion642" value="644"></td>
										<td width="5%"><input type="radio" name="relacion642" id="relacion642" value="645"></td>
										<td width="5%"><input type="radio" name="relacion642" id="relacion642" value="646"></td>
										<td width="5%"><input type="radio" name="relacion642" id="relacion642" value="647"></td>
										<td width="5%"><input type="radio" name="relacion642" id="relacion642" value="648"></td>
										<td width="5%"><input type="radio" name="relacion642" id="relacion642" value="649" checked></td>
									</tr>	

								</tbody>
								
							</table>

							<div>
								<h3>Habilidades de dirección:</h3>
							</div>
							<table class="table table-striped">
								<thead>
									<tr>
										<th width="50%"></th>
										<th width="5%">1</th>
										<th width="5%">2</th>
										<th width="5%">3</th>
										<th width="5%">4</th>
										<th width="5%">5</th>
										<th width="5%">6</th>
										<th width="5%">7</th>
										<th width="5%">8</th>
										<th width="5%">9</th>
										<th width="5%">10</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td width="50%"><h4>EEl empleado transmite bien los objetivos a los integrantes de su equipo</h4></td>
										<td width="5%"><input type="radio" name="relacion712" id="relacion712" value="710"></td>
										<td width="5%"><input type="radio" name="relacion712" id="relacion712" value="711"></td>
										<td width="5%"><input type="radio" name="relacion712" id="relacion712" value="712"></td>
										<td width="5%"><input type="radio" name="relacion712" id="relacion712" value="713"></td>
										<td width="5%"><input type="radio" name="relacion712" id="relacion712" value="714"></td>
										<td width="5%"><input type="radio" name="relacion712" id="relacion712" value="715"></td>
										<td width="5%"><input type="radio" name="relacion712" id="relacion712" value="716"></td>
										<td width="5%"><input type="radio" name="relacion712" id="relacion712" value="717"></td>
										<td width="5%"><input type="radio" name="relacion712" id="relacion712" value="718"></td>
										<td width="5%"><input type="radio" name="relacion712" id="relacion712" value="719" checked></td>
									</tr>									

									<tr>
										<td width="50%"><h4>El empleado comunica a todos en su área el éxito en el cumplimiento de objetivo</h4></td>
										<td width="5%"><input type="radio" name="relacion722" id="relacion722" value="720"></td>
										<td width="5%"><input type="radio" name="relacion722" id="relacion722" value="721"></td>
										<td width="5%"><input type="radio" name="relacion722" id="relacion722" value="722"></td>
										<td width="5%"><input type="radio" name="relacion722" id="relacion722" value="723"></td>
										<td width="5%"><input type="radio" name="relacion722" id="relacion722" value="724"></td>
										<td width="5%"><input type="radio" name="relacion722" id="relacion722" value="725"></td>
										<td width="5%"><input type="radio" name="relacion722" id="relacion722" value="726"></td>
										<td width="5%"><input type="radio" name="relacion722" id="relacion722" value="727"></td>
										<td width="5%"><input type="radio" name="relacion722" id="relacion722" value="728"></td>
										<td width="5%"><input type="radio" name="relacion722" id="relacion722" value="729" checked></td>
									</tr>	

									<tr>
										<td width="50%"><h4>El empleado demuestra dotes de liderazgo</h4></td>
										<td width="5%"><input type="radio" name="relacion732" id="relacion732" value="730"></td>
										<td width="5%"><input type="radio" name="relacion732" id="relacion732" value="731"></td>
										<td width="5%"><input type="radio" name="relacion732" id="relacion732" value="732"></td>
										<td width="5%"><input type="radio" name="relacion732" id="relacion732" value="733"></td>
										<td width="5%"><input type="radio" name="relacion732" id="relacion732" value="734"></td>
										<td width="5%"><input type="radio" name="relacion732" id="relacion732" value="735"></td>
										<td width="5%"><input type="radio" name="relacion732" id="relacion732" value="736"></td>
										<td width="5%"><input type="radio" name="relacion732" id="relacion732" value="737"></td>
										<td width="5%"><input type="radio" name="relacion732" id="relacion732" value="738"></td>
										<td width="5%"><input type="radio" name="relacion732" id="relacion732" value="739" checked></td>
									</tr>									

									<tr>
										<td width="50%"><h4>El empleado motiva a su equipo para conseguir los objetivos</h4></td>
										<td width="5%"><input type="radio" name="relacion742" id="relacion742" value="740"></td>
										<td width="5%"><input type="radio" name="relacion742" id="relacion742" value="741"></td>
										<td width="5%"><input type="radio" name="relacion742" id="relacion742" value="742"></td>
										<td width="5%"><input type="radio" name="relacion742" id="relacion742" value="743"></td>
										<td width="5%"><input type="radio" name="relacion742" id="relacion742" value="744"></td>
										<td width="5%"><input type="radio" name="relacion742" id="relacion742" value="745"></td>
										<td width="5%"><input type="radio" name="relacion742" id="relacion742" value="746"></td>
										<td width="5%"><input type="radio" name="relacion742" id="relacion742" value="747"></td>
										<td width="5%"><input type="radio" name="relacion742" id="relacion742" value="748"></td>
										<td width="5%"><input type="radio" name="relacion742" id="relacion742" value="749" checked></td>
									</tr>	

								</tbody>
								
							</table>
							<div class="form-actions text-center">
								<a href="evaluacion_empleado.php" class="btn blue button-next">ATRÁS</a>
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
   //App.init();
   $("#sig2").on("click",function()
   	{
		var relacion112=$("#relacion112:checked").val();
		var relacion122=$("#relacion122:checked").val();
		var relacion212=$("#relacion212:checked").val();
		var relacion222=$("#relacion222:checked").val();
		var relacion232=$("#relacion232:checked").val();
		var relacion242=$("#relacion242:checked").val();
		var relacion252=$("#relacion252:checked").val();
		var relacion312=$("#relacion312:checked").val();
		var relacion322=$("#relacion322:checked").val();
		var relacion412=$("#relacion412:checked").val();
		var relacion422=$("#relacion422:checked").val();
		var relacion432=$("#relacion432:checked").val();
		var relacion512=$("#relacion512:checked").val();
		var relacion522=$("#relacion522:checked").val();
		var relacion532=$("#relacion532:checked").val();
		var relacion542=$("#relacion542:checked").val();
		var relacion612=$("#relacion612:checked").val();
		var relacion622=$("#relacion622:checked").val();
		var relacion632=$("#relacion632:checked").val();
		var relacion642=$("#relacion642:checked").val();
		var relacion712=$("#relacion712:checked").val();
		var relacion722=$("#relacion722:checked").val();
		var relacion732=$("#relacion732:checked").val();
		var relacion742=$("#relacion742:checked").val();
			//alert(relacion112+" "+relacion122);

 		location.href="evaluacion_empleado_3.php?relacion112="+relacion112+"&relacion122="+relacion122+"&relacion212="+relacion212+"&relacion222="+relacion222+"&relacion232="+relacion232+"&relacion242="+relacion242+"&relacion252="+relacion252+"&relacion312="+ relacion312+"&relacion322="+relacion322+"&relacion412="+relacion412 +"&relacion422="+relacion422+"&relacion432="+relacion432+"&relacion512="+relacion512+"&relacion522="+relacion522+"&relacion532="+relacion532+   "&relacion542="+relacion542+"&relacion612="+relacion612+"&relacion622="+relacion622+"&relacion632="+relacion632+"&relacion642="+relacion642+"&relacion712="+relacion712+"&relacion722="+relacion722+"&relacion732="+relacion732+"&relacion742="+relacion742;
     });
  // TableManaged.init();
 // $('#sample_1').DataTable(); 
});
</script>

</body>
</html>