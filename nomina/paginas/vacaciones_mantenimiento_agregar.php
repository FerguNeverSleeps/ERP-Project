<?php
require_once('../lib/database.php');

error_reporting(E_ALL);
date_default_timezone_set('America/Panama');

$db                    = new Database($_SESSION['bd']);

$buscar                = isset($_REQUEST['buscar'])  ? $_REQUEST['buscar']  : '';
$pagina                = isset($_GET['pagina'])  ? $_GET['pagina']  : '';
$anio                  = isset($_POST['anio'])    ? $_POST['anio']    : '';
$ficha                 = isset($_POST['ficha'])   ? $_POST['ficha']   : '';
$cedula                = isset($_POST['cedula'])  ? $_POST['cedula']  : '';
$periodo               = isset($_POST['periodo']) ? $_POST['periodo'] : '';
$fechavac              = isset($_POST['fechavac']) ? $_POST['fechavac'] : '';
$fechareivac           = isset($_POST['fechareivac']) ? $_POST['fechareivac'] : '';
$saldo_dias            = isset($_POST['saldo_dias']) ? $_POST['saldo_dias'] : '';
$monto_vac             = isset($_POST['monto_vac']) ? $_POST['monto_vac'] : '';
$diasvac               = isset($_POST['diasvac']) ? $_POST['diasvac'] : '';
$estado                = isset($_POST['estado']) ? $_POST['estado'] : '';
$opcion                = isset($_POST['opcion']) ? $_POST['opcion'] : '';
$dias_ppagar           = isset($_POST['dias_ppagar']) ? $_POST['dias_ppagar'] : '';
$saldo_vacaciones      = isset($_POST['saldo_vacaciones']) ? $_POST['saldo_vacaciones'] : '';
$dias_solic_ppagar     = isset($_POST['dias_solic_ppagar']) ? $_POST['dias_solic_ppagar'] : '';
$dias_vac_disfrute     = isset($_POST['dias_vac_disfrute']) ? $_POST['dias_vac_disfrute'] : '';
$saldo_dias_pdisfrutar = isset($_POST['saldo_dias_pdisfrutar']) ? $_POST['saldo_dias_pdisfrutar'] : '';
$dias_solic_pdisfrutar = isset($_POST['dias_solic_pdisfrutar']) ? $_POST['dias_solic_pdisfrutar'] : '';
$dias_pagados          = isset($_POST['dias_pagados']) ? $_POST['dias_pagados'] : '';
$dias_disfrute         = isset($_POST['dias_disfrute']) ? $_POST['dias_disfrute'] : '';
$tipo                  = isset($_POST['tipo']) ? $_POST['tipo'] : '';

$sql_persona = "SELECT personal_id, cedula, apenom, tipnom, nomposicion_id, fecing, useruid, usuario_workflow  from nompersonal WHERE ficha = '{$ficha}'";

$fila_persona=$db->query($sql_persona)->fetch_assoc();

function formateoFecha($fecha) {
	$fechaPartes = explode('/', $fecha);
	$fechaFormateada = $fechaPartes[2] . '-' . $fechaPartes[1] . '-' . $fechaPartes[0];
	return $fechaFormateada;
}


if ($opcion == 1 ) {

	$tipo_justificacion = 7;
	$descripcion = 'VACACIONES ';

	switch ($tipo) {
		case 1:
			$descripcion .= 'DISFRUTE';
			break;
		case 2:
			$descripcion .= 'DISFRUTE Y PAGADAS';
			break;
		case 3:
			$descripcion .= 'PAGADAS';
			break;
		case 4:
			$descripcion .= 'ACUMULADAS';
			break;
	}

    if ($tipo == 4) {

			$dias_pagados  = isset($_POST['dias_pagados']) ? $_POST['dias_pagados'] : '';
			$dias_disfrute = isset($_POST['dias_disfrute']) ? $_POST['dias_disfrute'] : '';
			$fechareivac = $fechavac = $fechaopr = date("Y-m-d H:m:s");

			$consulta_incapacidad="INSERT INTO dias_incapacidad
																(
																	cod_user,
																	tipo_justificacion,
																	fecha,
																	tiempo,
																	observacion,
																	documento,
																	st,
																	usr_uid,
																	fecha_vence,
																	dias_restante,
																	horas_restante,
																	minutos_restante,
																	dias,
																	horas,
																	minutos,
																	cedula
																)
																VALUES
																(
																	'{$fila_persona["nomposicion_id"]}',
																	'{$tipo_justificacion}',
																	'{$fechavac}',
																	'{$dias_disfrute}',
																	'{$descripcion}',
																	'',
																	'',
																	'{$fila_persona["useruid"]}',
																	'{$fechareivac}',
																	'',
																	'0',
																	'0',
																	'{$dias_disfrute}',
																	'0',
																	'0',
																	'{$cedula}')";

			$db->query($consulta_incapacidad);

			$insert_id="SELECT LAST_INSERT_ID() as id;";

			$ultimo_id = $db->query($insert_id)->fetch_assoc();

			$sql1 = "INSERT INTO nom_progvacaciones SET
					periodo               = '{$periodo}',
					ficha                 = '{$ficha}',
					ceduda                = '{$cedula}',
					ddisfrute             = '{$dias_pagados}',
					saldo_vacaciones      = '{$dias_pagados}',
					saldo_dias_pdisfrutar = '{$dias_disfrute}',
					dias_vac_disfrute     = '{$dias_disfrute}',
					fechaopr 					 		= '{$fechaopr}',
					fechavac 					 		= '{$fechavac}',
					fechareivac 				  = '{$fechareivac}',
					marca                 = '1',
					estado                = '{$estado}',
					tipooper              = 'DV',
					desoper               = 'Dias Vacaciones',
					tipnom                = '{$_SESSION['codigo_nomina']}',
					id_dias_incapacidad   = '{$ultimo_id["id"]}'
					";
			$sql1.= "ON DUPLICATE KEY UPDATE
					ceduda                = '{$cedula}'";


			$res=$db->query($sql1);

			if ($res) {
				echo "<script>alert('Vacaciones agregadas exitosamente');</script>";
				header("location:vacaciones_mantenimiento.php?buscar=$buscar&pagina=$pagina");
			} else {

				echo "<script>alert('Error');</script>";
				header("location:vacaciones_mantenimiento.php?buscar=$buscar&pagina=$pagina");
			}
    }
    else {
		
			$fechavac = formateoFecha($fechavac);
			$fechareivac = formateoFecha($fechareivac);

			$consulta_incapacidad="INSERT INTO dias_incapacidad
																		(
																			cod_user,
																			tipo_justificacion,
																			fecha,
																			tiempo,
																			observacion,
																			documento,
																			st,
																			usr_uid,
																			fecha_vence,
																			dias_restante,
																			horas_restante,
																			minutos_restante,
																			dias,
																			horas,
																			minutos,
																			cedula
																		)
																		VALUES
																		(
																			'{$fila_persona["nomposicion_id"]}',
																			'{$tipo_justificacion}',
																			'{$fechavac}',
																			'{$dias_vac_disfrute}',
																			'{$descripcion}',
																			'',
																			'',
																			'{$fila_persona["useruid"]}',
																			'{$fechareivac}',
																			'{$saldo_vacaciones}',
																			'0',
																			'0',
																			'{$dias_solic_pdisfrutar}',
																			'0',
																			'0',
																			'{$cedula}'
																		)";

			$db->query($consulta_incapacidad);

			$insert_id="SELECT LAST_INSERT_ID() as id;";
			$ultimo_id = $db->query($insert_id)->fetch_assoc();

			$sql1 = "INSERT INTO nom_progvacaciones SET
					periodo               = '{$periodo}',
					ficha                 = '{$ficha}',
					ceduda                = '{$cedula}',
					saldo_vacaciones      = '{$saldo_vacaciones}',
					saldo_dias_pdisfrutar = '{$saldo_dias_pdisfrutar}',
					dias_solic_ppagar     = '{$dias_solic_ppagar}',
					dias_vac_disfrute     = '{$dias_vac_disfrute}',
					dias_solic_pdisfrutar = '{$dias_solic_pdisfrutar}',
					marca                 = '1', ";

			if ($fechavac != '') {
				$sql1 .= "fechavac = '{$fechavac}',";
			}

			if ($fechareivac != '') {
				$sql1 .= "fechareivac = '{$fechareivac}',";
			}

			$sql1 .= "ddisfrute  = '{$diasvac}',
					estado      = 'Pendiente',
					tipooper    = 'DV',
					desoper     = 'Dias Vacaciones',
					tipnom      = '{$_SESSION['codigo_nomina']}'";

			//Para evitar el mensaje llave duplicada
			$sql1.= "ON DUPLICATE KEY UPDATE
			ceduda                = '{$cedula}'";

			$res=$db->query($sql1);

			//INSERCION DIAS INCAPACIDAD
			if( isset($_POST['fechavac']) )
			{
				$sql = "UPDATE nompersonal SET
						fechavac    = STR_TO_DATE('{$_POST['fechavac']}', '%d/%m/%Y'),
						fechareivac = STR_TO_DATE('{$_POST['fechareivac']}', '%d/%m/%Y')
						WHERE ficha={$_POST['ficha']} AND cedula='{$_POST['cedula']}'";
				$db->query($sql);
			}
			if ($res) {
				echo "<script>alert('Vacaciones agregadas exitosamente');</script>";
				header("location:vacaciones_mantenimiento.php?buscar=$buscar&pagina=$pagina");
			}
		}


    //echo "<script> window.location.href='vacaciones_mantenimiento.php?pagina={$_POST['pagina']}'; </script>";
}
$sql32                   = "SELECT ddisfrute,saldo_dias_pdisfrutar from nom_progvacaciones where ceduda = '{$_GET['cedula']}' and ficha = '{$_GET['ficha']}'";
$row_vac               = $db->query($sql32)->fetch_assoc();
$ddisfrute             = ($row_vac['ddisfrute'] == 30) ? 30 : $row_vac['ddisfrute'] ;
$saldo_dias_pdisfrutar = ($row_vac['saldo_dias_pdisfrutar'] == 30) ? 30 : $row_vac['saldo_dias_pdisfrutar'] ;


?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="es" class="no-js">
<!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
<meta charset="utf-8"/>
<title>Vacaciones</title>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<meta content="" name="description"/>
<meta content="" name="author"/>
<!-- BEGIN GLOBAL MANDATORY STYLES
<link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css"/> -->
<link href="../../includes/assets/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
<link href="../../includes/assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
<link href="../../includes/assets/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css"/>
<!-- END GLOBAL MANDATORY STYLES -->
<!-- BEGIN PAGE LEVEL STYLES -->
<link rel="stylesheet" type="text/css" href="../../includes/assets/plugins/select2/select2.css"/>
<link rel="stylesheet" type="text/css" href="../../includes/assets/plugins/select2/select2-metronic.css"/>
<link rel="stylesheet" type="text/css" href="../../includes/assets/plugins/bootstrap-datepicker-1.5.1/css/bootstrap-datepicker3.min.css"/>
<!-- BEGIN THEME STYLES -->
<link href="../../includes/assets/css/style-metronic.css" rel="stylesheet" type="text/css"/>
<link href="../../includes/assets/css/style.css" rel="stylesheet" type="text/css"/>
<link href="../../includes/assets/css/style-responsive.css" rel="stylesheet" type="text/css"/>
<link href="../../includes/assets/css/plugins.css" rel="stylesheet" type="text/css"/>
<link href="../../includes/assets/css/themes/default.css" rel="stylesheet" type="text/css" id="style_color"/>
<link href="../../includes/assets/css/custom.css" rel="stylesheet" type="text/css"/>
<!-- END THEME STYLES -->
<style>
body {  /* En uso */
	background-color: white !important; 
}

.page-content-wrapper { /* En uso */
	background-color: white !important; 
}

.page-sidebar-closed .page-content { /* En uso */
	margin-left: 0px !important;
}

.portlet > .portlet-title > .caption { /* En uso */
	font-family: helvetica, arial, verdana, sans-serif;
  	font-size: 13px;
  	font-weight: bold;
  	line-height: 21px;
  	margin-bottom: 5px;
}

label.error { /* En uso */
	color: #b94a48;
}

@media (min-width: 768px)
{
	.form-horizontal .control-label {
		text-align: left;
	  	padding-left: 40px;
	}
}

.hide-underline{
	text-decoration: none !important;
}

.btn-md {
	padding: 5px 12px; 
}

.margin-top-15{ /* En uso */
	margin-top: 15px !important; 
}
</style>
</head>
<!-- END HEAD -->
<!-- BEGIN BODY -->
<body class="page-full-width">
<div class="clearfix"></div>
<!-- BEGIN CONTAINER -->
<div class="page-container">
  <!-- BEGIN CONTENT -->
  <div class="page-content-wrapper">
	<div class="page-content">
	  <!-- BEGIN PAGE HEADER-->
	  
	  <div class="row">
		<div class="col-md-12">
		  <h3 class="page-title">Vacaciones</h3>
		  <ul class="page-breadcrumb breadcrumb">
			<li><i class="glyphicon glyphicon glyphicon-sort"></i>
			  <a class="hide-underline">Transacciones</a><i class="fa fa-angle-right"></i>
			</li>
			<li><a class="hide-underline">Vacaciones</a><i class="fa fa-angle-right"></i></li>
			<li><a href="vacaciones_mantenimiento.php?pagina=<?php echo $pagina; ?>">Mantenimiento de vacaciones</a></li>
		  </ul>
		</div>
	  </div>

	  <!-- END PAGE HEADER-->
	  <!-- BEGIN PAGE CONTENT-->
	  <div class="row">
		<div class="col-md-12">
		  <div class="row">
			<div class="col-md-12">
			  <div class="tab-content">
				  <div class="portlet box blue">
					<div class="portlet-title">
						<div class="caption">
							<i class="fa fa-edit"></i> Agregar de vacaciones
						</div>
					  <!--
					  <div class="actions">
						<a class="btn btn-sm blue active"  onclick="javascript: window.location='../submenu_constancias.php'">
						  <i class="fa fa-arrow-left"></i> Regresar
						</a>
					  </div>
					  -->
					</div>
					<div class="portlet-body form">

					  <form action="#" id="frmPrincipal" name="frmPrincipal" class="form-horizontal" method="post">
					  	<input type='hidden' name="buscar" value="<?php print $_REQUEST['buscar']?>">
						<div class="form-body">
						<div class="form-group">
								<div class="col-md-4"></div>
							  	<div class="col-md-6">
									<label> Disfrute
							                <input type="radio" value="1" name="tipo" id="tipo1" />
							                <span></span>
							            </label>
							            <label> Disfrute y Pagados
							                <input type="radio" value="2" name="tipo" id="tipo2" checked />
							                <span></span>
							            </label>
							            <label> Pagados
							                <input type="radio" value="3" name="tipo" id="tipo3" />
							                <span></span>
							            </label>
							            <label> Vac. Acumuladas
							                <input type="radio" value="4" name="tipo" id="tipo4" />
							                <span></span>
							            </label>
							  	</div>
							</div>
						<div class="form-group">
							<label class="control-label col-md-3">Cédula</label>
							<div class="col-md-3">
								
									<span> <?PHP echo $_GET['cedula']; ?></span>

							</div>
						</div>
						<div class="form-group">

						  	<label class="control-label col-md-3">Periodo</label>
						  	<div class="col-md-3">
						  		<div id="list_periodo">
						  		</div>
						  	</div>
						</div>
													<div id ="fechas">
						<div class="row">
							&nbsp;	
							</div>	

							
							<div class="form-group margin-top-15">
								
								<label class="control-label col-md-3">Fecha de salida de vacaciones</label>
								<div class="col-md-3">
									<div class="input-group date date-picker" data-provide="datepicker">
										<input type="text" class="form-control" name="fechavac" id="fechavac" placeholder="Fecha salida">
										<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
									</div>	
								</div>
							</div>

							<div class="form-group">
								
								<label class="control-label col-md-3">Fecha de reintegro</label>
								<div class="col-md-3">
									<div class="input-group date date-picker" data-provide="datepicker">
										<input type="text" class="form-control" name="fechareivac" id="fechareivac" placeholder="Fecha reintegro">
										<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
									</div>	
								</div>
							</div>
							</div>
							<div id="pagados">
							<div class="row">
							&nbsp;	
							</div>



								<div class="form-group">
									
								  	<label class="control-label col-md-3">Días de vacaciones</label>
								  	<div class="col-md-3">
										<input type="text" name="diasvac" id="diasvac" class="form-control" placeholder="Días de vacaciones" value="<?= $ddisfrute; ?>" readonly="readonly">
								  	</div>	

								  	<label class="control-label col-md-3">Saldo vacaciones</label>
								  	<div class="col-md-3">
										<input type="text" name="saldo_vacaciones" id="saldo_vacaciones" class="form-control" placeholder="Saldo vacaciones" readonly="readonly">
								  	</div>						
									
								  	
								</div>
								<div class="form-group">
									
								  	<label class="control-label col-md-3">Días solicitados por pagar</label>
								  	<div class="col-md-3">
										<input type="text" name="dias_solic_ppagar" id="dias_solic_ppagar" class="form-control" placeholder="Días solicitados por pagar">
								  	</div>
								</div>
							</div>

							<!--<div class="form-group">
								<label class="control-label col-md-3">Días pendientes por pagar</label>
							  	<div class="col-md-3">
									<input type="text" name="dias_ppagar" id="dias_ppagar" class="form-control" placeholder="Días pendientes por pagar">
							  	</div>
							  	
							</div>	-->

							<div id = "disfrute">
							<div class="row">
							&nbsp;	
							</div>
								<div class="form-group">
									
								  	<label class="control-label col-md-3">Días vacaciones de disfrute</label>
								  	<div class="col-md-3">
										<input type="text" name="dias_vac_disfrute" id="dias_vac_disfrute" class="form-control" placeholder="Días vacaciones disfrutados" value="<?= $saldo_dias_pdisfrutar; ?>" readonly="readonly">
								  	</div>
								  	<label class="control-label col-md-3">Saldo Días de disfrute</label>
								  	<div class="col-md-3">
										<input type="text" name="saldo_dias_pdisfrutar" id="saldo_dias_pdisfrutar" class="form-control" placeholder="Saldo de Días por disfrutar" readonly="readonly">
								  	</div>
								</div>	
								<div class="form-group">
									
								  	<label class="control-label col-md-3">Días solicitados de disfrute</label>
								  	<div class="col-md-3">
										<input type="text" name="dias_solic_pdisfrutar" id="dias_solic_pdisfrutar" class="form-control" placeholder="Días solicitados por disfrutar">
								  	</div>
								</div>	
									
								<div class="form-group">
									
								  	
								</div>
							</div>
							<div class="row">
								&nbsp;	
							</div>
							<div id = "acum">

								<div class="form-group">
									
								  	<label class="control-label col-md-3">Días Pagados</label>
								  	<div class="col-md-3">
										<input type="text" name="dias_pagados" id="dias_pagados" class="form-control" placeholder="Días Pagados">
								  	</div>
								</div>
								<div class="form-group">
									
								  	
								  	<label class="control-label col-md-3">Días de disfrute</label>
								  	<div class="col-md-3">
										<input type="text" name="dias_disfrute" id="dias_disfrute" class="form-control"  placeholder="Días de disfrutar">
								  	</div>
								</div>
									

							</div>
							<div class="row">
								&nbsp;	
							</div>

							<!--<div class="form-group">
								
							  	<label class="control-label col-md-3">MONTO</label>
							  	<div class="col-md-3">
									<input type="text" name="monto_vac" id="monto_vac" class="form-control" placeholder="MONTO PENDIENTE">
							  	</div>
							</div>	-->

							<div class="form-group">
								
							  	<label class="control-label col-md-3">Estado</label>
							  	<div class="col-md-3">
									<select name="estado" id="estado" class="form-control" > 
										<option value="Pendiente">Pendiente</option>
										<option value="Pagado">Pagado</option>
									</select>
							  	</div>
							</div>	
				
						</div>

						<div class="form-actions fluid">
							<div class="col-md-12 text-center">
								<button type="button" class="btn blue btn-md" 
								onclick="javascript: enviar();"><i class="fa fa-check"></i> Aceptar</button>
								<button type="button" class="btn default btn-md"
								onclick="javascript: window.location='vacaciones_mantenimiento.php?buscar=<?php print $buscar;?>&pagina=<?php echo $pagina; ?>';">Cancelar</button>
							</div>
						</div>

			            <input type="hidden" name="pagina" id="pagina" value="<?php echo $pagina; ?>">
			            <input type="hidden" name="anio"   id="anio"   value="<?php echo $anio; ?>">  
						<input type="hidden" name="cedula" id="cedula" value="<?= $_GET['cedula'] ?>">
						<input type="hidden" name="ficha"  id="ficha"  value="<?= $_GET['ficha'] ?>">
			            <input type="hidden" name="opcion" id="opcion" value="1">

					  </form>

					</div>
				  </div>
				
			  </div>
			</div>
		  </div>
		</div>
	  </div>
	  <!-- END PAGE CONTENT-->
	</div>
  </div>
  <!-- END CONTENT -->
</div>
<!-- END CONTAINER -->
<!-- BEGIN JAVASCRIPTS(Load javascripts at bottom, this will reduce page load time) -->
<!-- BEGIN CORE PLUGINS -->
<!--[if lt IE 9]>
<script src="../../includes/assets/plugins/respond.min.js"></script>
<script src="../../includes/assets/plugins/excanvas.min.js"></script>
<![endif]-->
<script src="../../includes/assets/plugins/jquery-1.10.2.min.js" type="text/javascript"></script>
<script src="../../includes/assets/plugins/jquery-migrate-1.2.1.min.js" type="text/javascript"></script>
<!-- IMPORTANT! Load jquery-ui-1.10.3.custom.min.js before bootstrap.min.js to fix bootstrap tooltip conflict with jquery ui tooltip -->
<script src="../../includes/assets/plugins/jquery-ui/jquery-ui-1.10.3.custom.min.js" type="text/javascript"></script>
<script src="../../includes/assets/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script src="../../includes/assets/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js" type="text/javascript"></script>
<script src="../../includes/assets/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
<script src="../../includes/assets/plugins/jquery.blockui.min.js" type="text/javascript"></script>
<script src="../../includes/assets/plugins/jquery.cokie.min.js" type="text/javascript"></script>
<script src="../../includes/assets/plugins/uniform/jquery.uniform.min.js" type="text/javascript"></script>
<!-- END CORE PLUGINS -->
<!-- BEGIN PAGE LEVEL PLUGINS -->
<script type="text/javascript" src="../../includes/assets/plugins/jquery-validation/dist/jquery.validate.min.js"></script>
<script type="text/javascript" src="../../includes/assets/plugins/jquery-validation/dist/additional-methods.min.js"></script>
<script type="text/javascript" src="../../includes/assets/plugins/jquery-validation/localization/messages_es.js"></script>
<script type="text/javascript" src="../../includes/assets/plugins/bootstrap-select/bootstrap-select.min.js"></script>
<script type="text/javascript" src="../../includes/assets/plugins/select2/select2.min.js"></script>
<script type="text/javascript" src="../../includes/assets/plugins/select2/select2_locale_es.js"></script>
<script type="text/javascript" src="../../includes/assets/plugins/bootstrap-datepicker-1.5.1/js/bootstrap-datepicker.min.js"></script>
<script type="text/javascript" src="../../includes/assets/plugins/bootstrap-datepicker-1.5.1/locales/bootstrap-datepicker.es.min.js"></script>
<script type="text/javascript" src="../../includes/assets/plugins/fancybox/source/jquery.fancybox.pack.js"></script>
<script type="text/javascript" src="../../includes/assets/plugins/bootstrap-fileinput/bootstrap-fileinput.js"></script>
<!-- END PAGE LEVEL PLUGINS -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="../../includes/assets/scripts/core/app1.js"></script>
<script>
jQuery(document).ready(function() {
  App.init();
});
</script>
<script>
$(document).ready(function(){
	$("#pagados").show();
	$("#disfrute").show();
	$("#fechas").show();
	$("#acum").hide();
	cedula = $('#cedula').val();
	tipos(cedula, 2);
	$("#tipo1").on('click',function(){
		tipo=$("#tipo1").val();
		console.log(tipo);
		$("#pagados").hide();
		$("#disfrute").show();
		$("#acum").hide();
		$("#fechas").show();
		tipos(cedula,tipo);

	});
	$("#tipo2").on('click',function(){
		tipo=$("#tipo2").val();
		//console.log(tipo);
		$("#pagados").show();
		$("#disfrute").show();
		$("#acum").hide();
		$("#fechas").show();
		tipos(cedula,tipo);
	});
	$("#tipo3").on('click',function(){
		tipo=$("#tipo3").val();
		console.log(tipo);
		$("#fechas").show();
		$("#pagados").show();
		$("#disfrute").hide();
		$("#acum").hide();
		tipos(cedula,tipo);
	});
	$("#tipo4").on('click',function(){
		tipo=$("#tipo4").val();
		//console.log(tipo);
		$("#pagados").hide();
		$("#disfrute").hide();
		$("#fechas").hide();
		$("#acum").show();
		tipos(cedula,tipo);
	});
	

	// Datepicker + jQuery Validate  
	$('.input-group.date').datepicker({
		format: "dd/mm/yyyy",
	    language: 'es',
		autoclose: true
	});

	$("#dias_solic_ppagar").on('keyup',function(){
		dias_solic_ppagar = $('#dias_solic_ppagar').val();
		total_vac = $('#diasvac').val();
		total = total_vac - dias_solic_ppagar;
		//console.log(total+" "+total_vac+" "+dias_solic_ppagar);

		if (total < 0) 
		{
			alert("El saldo de días no puede ser negativo");
			$('#dias_solic_ppagar').val(0);

		}
		if (total > total_vac) 
		{
			alert("Los días a disfrutar no deben ser mayores que los días pendientes");
			$('#dias_solic_ppagar').empty();
			$('#dias_solic_ppagar').val(0);

		}
		if(total >= 0 && total <= total_vac)
		{
			//console.log("aqui");
			$('#saldo_vacaciones').empty();
			$('#saldo_vacaciones').val(total);
		}		
	});
	$("#dias_solic_pdisfrutar").on('keyup',function(){
		dias_solic_pdisfrutar = $('#dias_solic_pdisfrutar').val();
		total_vac = $('#dias_vac_disfrute').val();
		total = total_vac - dias_solic_pdisfrutar;
		//console.log(total+" "+total_vac+" "+dias_solic_pdisfrutar);

		if (total < 0) 
		{
			alert("El saldo de días no puede ser negativo");
			$('#dias_solic_pdisfrutar').val(0);

		}
		if (total > total_vac) 
		{
			alert("Los días a disfrutar no deben ser mayores que los días pendientes");
			$('#dias_solic_pdisfrutar').empty();
			$('#dias_solic_pdisfrutar').val(0);

		}
		if(total >= 0 && total <= total_vac)
		{
			//console.log("aqui");
			$('#saldo_dias_pdisfrutar').empty();
			$('#saldo_dias_pdisfrutar').val(total);
		}		
	});
});
function tipos (cedula, tipo)
{
	$.get("ajax/periodosPendientes.php",{cedula:cedula,tipo:tipo},function(res){
		$("#list_periodo").empty();
		$("#list_periodo").append(res);
		$("#periodo").change(function()
		{
		periodo = $("#periodo").val();

			$.get("ajax/getDiasVacaciones.php",{cedula:cedula,periodo:periodo},function(res){
				$("#diasvac").empty();
				$("#diasvac").val(res);
			});
			$.get("ajax/geSaldoVacaciones.php",{cedula:cedula,periodo:periodo},function(res){
				$("#saldo_vacaciones").empty();
				$("#saldo_vacaciones").val(res);
			});
			$.get("ajax/getDiasDisfrute.php",{cedula:cedula,periodo:periodo},function(res){
				$("#dias_vac_disfrute").empty();
				$("#dias_vac_disfrute").val(res);
			});
			$.get("ajax/getSaldoDisfrute.php",{cedula:cedula,periodo:periodo},function(res){
				$("#saldo_dias_pdisfrutar").empty();
				$("#saldo_dias_pdisfrutar").val(res);
			});		
		});

	});
}
function enviar()
{
	if( $('#fechavac').length ) // Si existe el elemento
	{ 
		// Validar fechas

		var fecha_sal = $("#fechavac").val();
		var fecha_rei = $("#fechareivac").val();

		if(fecha_sal!=''  &&  fecha_rei!='')
		{
			var d1 = fecha_sal.split("/");
			var d2 = fecha_rei.split("/");

			var date1 = new Date(d1[2], d1[1] - 1, d1[0], 0, 0, 0, 0);
			var date2 = new Date(d2[2], d2[1] - 1, d2[0], 0, 0, 0, 0);

			if(date2 < date1)
			{
				alert("\u00A1Error! La fecha de reintegro no puede ser menor a la fecha de salida");
				return false;
			}
		}
	}

	document.frmPrincipal.submit();
}
</script>
</body>
</html>