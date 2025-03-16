<?php
require_once('../../lib/database.php');

$db = new Database($_SESSION['bd']);

$personal_id = isset($_GET['personal_id']) ? $_GET['personal_id'] : 0;
$operacion   = isset($_GET['editar'])      ? 'editar' : 'agregar';

require "save_editar_integrante.php";

$sql = "SELECT e.nivel1, e.nomniv1, e.nivel2, e.nomniv2, e.nivel3, e.nomniv3, e.nivel4, e.nomniv4, e.nivel5, e.nomniv5,
		       e.nivel6, e.nomniv6, e.nivel7, e.nomniv7, e.tipo_empresa
		FROM nomempresa e";
$res = $db->query($sql);
$empresa = $res->fetch_object();

$sql = "SELECT acceso_sueldo FROM ".SELECTRA_CONF_PYME.".nomusuarios WHERE login_usuario='".$_SESSION['usuario']."'";
$res = $db->query($sql);
$usuario = $res->fetch_object();

if($operacion=='editar')
{
	$sql = "SELECT * FROM nompersonal WHERE personal_id='".$personal_id."'";
	$res = $db->query($sql);

	if($integrante = $res->fetch_object())
	{
		$ficha_actual = isset($integrante->ficha) ? $integrante->ficha : '';

		$fecha_nacimiento = DateTime::createFromFormat('Y-m-d', $integrante->fecnac);
		$fecha_nacimiento = ($fecha_nacimiento !== false) ? $fecha_nacimiento->format('d-m-Y') : '';

		$fecha_ingreso = DateTime::createFromFormat('Y-m-d', $integrante->fecing);
		$fecha_ingreso = ($fecha_ingreso !== false) ? $fecha_ingreso->format('d-m-Y') : '';

		if(!empty($integrante->fecha_decreto) && $integrante->fecha_decreto!='0000-00-00')
		{
			$fecha_decreto =  DateTime::createFromFormat('Y-m-d', $integrante->fecha_decreto);
			$fecha_decreto = ($fecha_decreto !== false) ? $fecha_decreto->format('d-m-Y') : '';
		}

		if(!empty($integrante->fecha_decreto_baja) && $integrante->fecha_decreto_baja!='0000-00-00')
		{
			$fecha_decreto_baja =  DateTime::createFromFormat('Y-m-d', $integrante->fecha_decreto_baja);
			$fecha_decreto_baja = ($fecha_decreto_baja !== false) ? $fecha_decreto_baja->format('d-m-Y') : '';
		}

		if(!empty($integrante->inicio_periodo) && $integrante->inicio_periodo!='0000-00-00')
		{
			$inicio_periodo =  DateTime::createFromFormat('Y-m-d', $integrante->inicio_periodo);
			$inicio_periodo = ($inicio_periodo !== false) ? $inicio_periodo->format('d-m-Y') : '';
		}

		if(!empty($integrante->fin_periodo) && $integrante->fin_periodo!='0000-00-00')
		{
			$fin_periodo =  DateTime::createFromFormat('Y-m-d', $integrante->fin_periodo);
			$fin_periodo = ($fin_periodo !== false) ? $fin_periodo->format('d-m-Y') : '';
		}
	}
	else
	{
		echo "<script>alert('Acceso Denegado');</script>";
		echo "<script>document.location.href = '../maestro_personal.php';</script>";
	}
}
else
{
    $sql = "SELECT max(ficha) as valor FROM nompersonal";
    $res = $db->query($sql);

    $fila = $res->fetch_assoc();
    $ficha_actual = $fila['valor'] + 1;
}
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
<title>Datos Integrantes</title>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<meta content="" name="description"/>
<meta content="" name="author"/>
<!-- BEGIN GLOBAL MANDATORY STYLES -->
<link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css"/>
<link href="../../../includes/assets/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
<link href="../../../includes/assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
<link href="../../../includes/assets/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css"/>
<!-- END GLOBAL MANDATORY STYLES -->
<!-- BEGIN PAGE LEVEL STYLES -->
<link rel="stylesheet" type="text/css" href="../../../includes/assets/plugins/bootstrap-select/bootstrap-select.min.css"/>
<link rel="stylesheet" type="text/css" href="../../../includes/assets/plugins/select2/select2.css"/>
<link rel="stylesheet" type="text/css" href="../../../includes/assets/plugins/select2/select2-metronic.css"/>
<!-- <link rel="stylesheet" type="text/css" href="../../../includes/assets/plugins/jquery-multi-select/css/multi-select.css"/> -->
<link rel="stylesheet" type="text/css" href="../../../includes/assets/plugins/bootstrap-datepicker-1.5.1/css/bootstrap-datepicker3.min.css"/>
<link href="../../../includes/assets/plugins/fancybox/source/jquery.fancybox.css" rel="stylesheet" type="text/css"/>
<!-- BEGIN THEME STYLES -->
<link href="../../../includes/assets/css/style-metronic.css" rel="stylesheet" type="text/css"/>
<link href="../../../includes/assets/css/style.css" rel="stylesheet" type="text/css"/>
<link href="../../../includes/assets/css/style-responsive.css" rel="stylesheet" type="text/css"/>
<link href="../../../includes/assets/css/plugins.css" rel="stylesheet" type="text/css"/>
<link href="../../../includes/assets/css/themes/default.css" rel="stylesheet" type="text/css" id="style_color"/>
<!--<link href="../../../includes/assets/css/pages/profile.css" rel="stylesheet" type="text/css"/>-->
<link href="../../../includes/assets/css/custom.css" rel="stylesheet" type="text/css"/>
<!-- END THEME STYLES -->
<link rel="shortcut icon" href="favicon.ico"/>
<script>
var FICHA_ACTUAL  = '<?php echo $ficha_actual; ?>';
var CEDULA_ACTUAL = '<?php echo (isset($integrante->cedula)) ? $integrante->cedula : ''; ?>';
var OPERACION     = '<?php echo $operacion; ?>';
var TIPO_EMPRESA  = '<?php echo $empresa->tipo_empresa; ?>';
var NIVEL1        = '<?php echo $empresa->nivel1; ?>';
var NIVEL2        = '<?php echo $empresa->nivel2; ?>';
var NIVEL3        = '<?php echo $empresa->nivel3; ?>';
var NIVEL4        = '<?php echo $empresa->nivel4; ?>';
var NIVEL5        = '<?php echo $empresa->nivel5; ?>';
var NIVEL6        = '<?php echo $empresa->nivel6; ?>';
var NIVEL7        = '<?php echo $empresa->nivel7; ?>';
var USUARIO_ACCESO_SUELDO = '<?php echo $usuario->acceso_sueldo; ?>';	
</script>
<style>
body {
	background-color: white !important;
}

.fancybox-wrap {
	top: 60px !important;
}

/*
.fancybox-wrap.fancybox-foto {
	top: 50px !important;
}
*/

.fancybox-wrap.fancybox-cedula {
	top: 300px !important;
}

.tile {
    height: 115px;
    width: 151px !important;
    /*width: 145px !important;*/
    /*margin-right: 0px;
    margin-left: 13px; */
    margin-left: 0px;
    margin-right: 10px;
}

.tile.first{
	/*margin-left: 0px; */
}

.tile .tile-body {
	margin-bottom: 0px;
	padding: 0px;
}
.tile .tile-object > .name{
	text-align: center;
	width: 100%;
	margin-left: 0px;
	margin-right: 0px;
}
a.tabs_tiles:hover, a.tabs_tiles:focus {
	color: #ffffff;
	text-decoration: none;
}
.row.tiles {
	margin-left: 0px;
	margin-right: 0px
}
.form-horizontal .control-label {
	text-align: left;
}
.form-actions.fluid {
	padding: 0px;
	background-color: white;
	border-top: 0px;
	margin-top: 0px;
}

#form_integrantes1.form-horizontal .radio-list > label {
    padding-left: 0px;
    margin-left: 0px;
    padding-right: 20px;
}

.max-width-250{
	max-width: 250px;
}

.dysplay-none{
	display: none;
}

.btn.dark.claro {
	opacity: .85;
}

.portlet > .portlet-title > .caption {
    font-size: 15px;
    line-height: 19px;
    font-weight: 500;
    margin-bottom: 6px;
}

.page-title {
    font-size: 24px;
}

.padding-left-40{
	padding-left: 40px;
}

.padding-right-5{
	padding-right: 5px;
}
</style>
</head>
<!-- END HEAD -->
<!-- BEGIN BODY -->
<body class="page-full-width">
<!-- BEGIN HEADER -->
<!-- END HEADER -->
<div class="clearfix">
</div>
<!-- BEGIN CONTAINER -->
<div class="page-container">
	<!-- END EMPTY PAGE SIDEBAR -->
	<!-- END SIDEBAR -->
	<!-- BEGIN CONTENT -->
	<div class="page-content-wrapper">
		<div class="page-content">
			<!-- BEGIN PAGE HEADER-->
			<div class="row">
				<div class="col-md-12">
					<!-- BEGIN PAGE TITLE & BREADCRUMB-->
					<h3 class="page-title">Datos de Integrantes</h3>
					<ul class="page-breadcrumb breadcrumb">
						<li class="btn-group">
							<button type="button" class="btn dark claro btn-print">
								<i class="fa fa-print"></i> <span>Imprimir</span>							
							</button>
						</li>
						<li>
							<i class="glyphicon glyphicon-user"></i>
							<a href="index.html">
								Colaboradores
							</a>
							<i class="fa fa-angle-right"></i>
						</li>
						<li>
							<a href="#">
								Datos de Integrantes
							</a>
						</li>
					</ul>
					<!-- END PAGE TITLE & BREADCRUMB-->
				</div>
			</div>
			<!-- END PAGE HEADER-->
			<!-- BEGIN PAGE CONTENT-->
			<div class="row">
				<div class="col-md-3">
					<div class="row">
						<div class="col-md-12 col-sm-6 col-xs-12">
							<?php
								$foto_integrante = '../../../includes/assets/img/profile/profile.png';

								$directorio_fotos = '../';

								if(isset($integrante->foto) && file_exists($directorio_fotos . $integrante->foto))
								{
									$foto_integrante = $directorio_fotos . $integrante->foto;
								}
							?>
							<a class="fancybox" href="<?php echo $foto_integrante; ?>">
							<img src="<?php echo $foto_integrante; ?>" class="img-responsive img-thumbnail max-width-250" alt="Foto del integrante" />
							</a>
							<div class="text-center margin-top-10 margin-bottom-20 max-width-250">
								<a class="btn btn-sm blue active" data-toggle="tab" href="#tab_6" title="Cambiar foto del integrante"><i class="fa fa-picture-o"></i></a>
								<!-- <button type="button" class="btn btn-sm blue active" data-toggle="tooltip" data-placement="top" title="Cambiar foto del integrante"><i class="fa fa-picture-o"></i></button>-->
								<a class="btn btn-sm red active fancybox" data-toggle="tooltip" data-placement="top" title="Ver foto del integrante" href="<?php echo $foto_integrante; ?>"><i class="fa fa-search"></i></a>
							</div>							
						</div>		
						<div class="col-md-12 col-sm-6 col-xs-12">
							<?php
								$imagen_cedula = '../../../includes/assets/img/profile/no_disponible.png';

								$directorio_fotos = '../';

								if(isset($integrante->imagen_cedula) && file_exists($directorio_fotos . $integrante->imagen_cedula))
								{
									$imagen_cedula = $directorio_fotos . $integrante->imagen_cedula;
								}
							?>						
							<a class="fancybox fancybox-cedula" href="<?php echo $imagen_cedula; ?>">
							<img src="<?php echo $imagen_cedula; ?>" class="img-responsive img-thumbnail max-width-250" alt="Imagen de la c&eacute;dula"/>
							</a>
							<div class="text-center margin-top-10 margin-bottom-10 max-width-250">
								<a class="btn btn-sm blue active" data-toggle="tab" href="#tab_7" title="Cambiar imagen de la c&eacute;dula"><i class="fa fa-picture-o"></i></a>
								<!--<button type="button" class="btn btn-sm blue active" data-toggle="tooltip" data-placement="top" title="Cambiar imagen de la c&eacute;dula"><i class="fa fa-picture-o"></i></button>-->
								<a class="btn btn-sm red active fancybox fancybox-cedula" data-toggle="tooltip" data-placement="top" title="Ver imagen de la c&eacute;dula" href="<?php echo $imagen_cedula; ?>"><i class="fa fa-search"></i></a>
							</div>							
						</div>			
					</div>
				</div>
				<div class="col-md-9">
					<div class="row tiles">
						<a class="tabs_tiles" data-toggle="tab" href="#tab_1">
							<div class="tile bg-blue">
								<div class="tile-body">
									<i class="fa fa-user"></i>
								</div>
								<div class="tile-object">
									<div class="name">Personal</div>
								</div>
							</div>
						</a>
			
						<a class="tabs_tiles" data-toggle="tab" href="#tab_2">
							<div class="tile bg-red">
								<div class="tile-body">
									<i class="fa fa-calendar"></i>
								</div>
								<div class="tile-object">
									<div class="name">Calendario</div>
								</div>
							</div>
						</a>

						<a class="tabs_tiles" data-toggle="tab" href="#tab_3">
							<div class="tile bg-blue">
								<div class="tile-body">
									<i class="fa fa-group"></i>
								</div>
								<div class="tile-object">
									<div class="name">
										 Carga Familiar
									</div>
								</div>
							</div>
						</a>

						<a class="tabs_tiles" data-toggle="tab" href="#tab_4">
							<div class="tile bg-red">
								<div class="tile-body">
									<i class="fa fa-wrench"></i>
								</div>
								<div class="tile-object">
									<div class="name">
										 Campos Adicionales
									</div>
								</div>
							</div>
						</a>

						<a class="tabs_tiles" data-toggle="tab" href="#tab_5">
							<div class="tile bg-blue">
								<div class="tile-body">
									<i class="fa fa-briefcase"></i>
								</div>
								<div class="tile-object">
									<div class="name">
										 Expediente
									</div>
								</div>
							</div>
						</a>
					</div>
					<hr>
					<div class="row">
						<div class="col-md-12">
							<div class="tab-content">
								<div id="tab_1" class="tab-pane active">
									<div class="row" style="margin-bottom: 20px; margin-top: 5px;">
										<div class="col-md-12 text-center">
											<form action="#" id="form_integrantes" name="form_integrantes" method="post">
												<button type="submit" id="btn_guardar" name="btn_guardar" class="btn btn-sm red active hide"><i class="fa fa-check"></i> Guardar Todo</button>
												<button type="button" class="btn btn-sm default btn-cancelar"><i class="fa fa-mail-reply"></i> Regresar</button>		
											</form>						
										</div>
									</div>
									<div class="portlet box blue">
										<div class="portlet-title">
											<div class="caption">
												<i class="fa fa-edit"></i> Datos Personales
											</div>
											<div class="tools">
												<a href="javascript:;" class="collapse"></a>
											</div>
											<!--
											<div class="actions">
												<a href="#" class="btn btn-sm blue active">
													<i class="fa fa-check"></i> Guardar
												</a>
											</div>-->
										</div>
										<div class="portlet-body">
											<!-- BEGIN FORM-->
											<form action="#" class="form-horizontal" id="form_integrantes1" name="form_integrantes1" method="post">
												<span class="error"></span>
												<div class="form-body">
													<!--
													<div class="alert alert-danger display-hide">
														<button class="close" data-close="alert"></button>
														 You have some form errors. Please check below.
													</div>
													<!--
													<div class="alert alert-success display-hide">
														<button class="close" data-close="alert"></button>
														Your form validation is successful!
													</div>
													-->
													<div class="row">
														<div class="col-md-6">
															<div class="form-group">
																<label class="control-label col-md-4">Nombres <span class="required">*</span></label>
																<div class="col-md-8">
																	<input type="text" name="nombres" id="nombres" class="form-control" value="<?php  echo (isset($integrante->nombres)) ? $integrante->nombres : ''; ?>">
																</div>
															</div>
														</div>
														<!--/span-->
														<div class="col-md-6">
															<div class="form-group">
																<label class="control-label col-md-4 padding-left-40">Apellidos <span class="required">*</span></label>
																<div class="col-md-8">
																	<input type="text" name="apellidos" id="apellidos" class="form-control" value="<?php echo (isset($integrante->apellidos)) ? $integrante->apellidos : ''; ?>">
																</div>
															</div>
														</div>
														<!--/span-->
													</div>
													<!--/row-->
													<div class="row">
														<div class="col-md-6">
															<div class="form-group">
																<label class="control-label col-md-4">C&eacute;dula <span class="required">*</span></label>
																<div class="col-md-8">
																	<input type="text" name="cedula" id="cedula" class="form-control" value="<?php echo(isset($integrante->cedula)) ? $integrante->cedula : ''; ?>">
																</div>
															</div>
														</div>
														<!--/span-->
														<div class="col-md-6">
															<div class="form-group">
																<?php
																	$nacionalidad1 = 'checked';
																	$nacionalidad2 = $nacionalidad3 = '';

																	if(isset($integrante->nacionalidad))
																	{
																		$nacionalidad1 = ($integrante->nacionalidad=='1')  ? 'checked' : '';
																		$nacionalidad2 = ($integrante->nacionalidad=='2')  ? 'checked' : '';
																		$nacionalidad3 = ($integrante->nacionalidad=='3')  ? 'checked' : '';
																	} 
																?>														
																<label class="control-label col-md-4 padding-left-40">Nacionalidad</label>
																<div class="col-md-8">
																	<div class="radio-list">
																		<label class="radio-inline">
																		<input type="radio" name="nacionalidad" id="nacionalidad1" value="1" <?php echo $nacionalidad1; ?>> Panameño</label>
																		<label class="radio-inline">
																		<input type="radio" name="nacionalidad" id="nacionalidad2" value="2" <?php echo $nacionalidad2; ?>> Extranjero</label>
																		<label class="radio-inline">
																		<input type="radio" name="nacionalidad" id="nacionalidad3" value="3" <?php echo $nacionalidad3; ?>> Nacionalizado</label>
																	</div>																		
																</div>
															</div>
														</div>
														<!--/span-->
													</div>
													<!--/row-->
													<div class="row">
														<!--/span-->
														<div class="col-md-6">
															<div class="form-group">
																<?php
																	$sexo1 = 'checked';	$sexo2 = '';

																	if(isset($integrante->sexo))
																	{
																		$sexo1 = ($integrante->sexo=='Masculino') ? 'checked' : '';
																		$sexo2 = ($integrante->sexo=='Femenino')  ? 'checked' : '';
																	} 
																?>															
																<label class="control-label col-md-4">Sexo</label>
																<div class="col-md-8">
																	<div class="radio-list">
																		<label class="radio-inline">
																		<input type="radio" name="sexo" id="sexo1" value="Masculino" <?php echo $sexo1; ?>> Masculino</label>
																		<label class="radio-inline">
																		<input type="radio" name="sexo" id="sexo2" value="Femenino"  <?php echo $sexo2; ?>> Femenino</label>
																	</div>																	
																</div>
															</div>
														</div>
														<!--/span-->
														<div class="col-md-6">
															<div class="form-group">
																<?php
																	$data = array('Soltero/a', 'Casado/a', 'Viudo/a', 'Divorciado/a', 'Unido');
																?>															
																<label class="control-label col-md-4 padding-left-40">Estado Civil <span class="required">*</span></label>
																<div class="col-md-8">
																	<select name="estado_civil" id="estado_civil" class="form-control select2" data-placeholder="Seleccione el estado civil">
																		<?php
																			if(!isset($integrante->estado_civil)) 
																				echo "<option value=''>Seleccione el estado civil</option>";

																			foreach ($data as $estado_civil) 
																			{
																				if(isset($integrante->estado_civil) && $integrante->estado_civil==$estado_civil)
																					echo "<option value='".$estado_civil."' selected>".$estado_civil."</option>";
																				else
																					echo "<option value='".$estado_civil."'>".$estado_civil."</option>";
																			}
																		?>
																	</select>																	
																</div>
															</div>
														</div>
													</div>
													<!--/row-->
													<div class="row">
														<div class="col-md-6">
															<div class="form-group">
																<label class="control-label col-md-4">Fecha de Nacimiento <span class="required">*</span></label>
																<div class="col-md-8">
																	<div class="input-group date date-picker" data-provide="datepicker" data-date-end-date="0d">
																		<input type="text" class="form-control" name="fecnac" id="fecnac" value="<?php echo (isset($fecha_nacimiento)) ? $fecha_nacimiento : ''; ?>">
																		<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
																		<!--<span class="input-group-btn">
																			<button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
																		</span>-->
																	</div>																	
																</div>
															</div>
														</div>
														<!--/span-->
														<div class="col-md-6">
															<div class="form-group">
																<label class="control-label col-md-4 padding-left-40">Lugar de Nacimiento <span class="required">*</span></label>
																<div class="col-md-8">
																	<input type="text" name="lugarnac" id="lugarnac" class="form-control" value="<?php  echo (isset($integrante->lugarnac)) ? $integrante->lugarnac : ''; ?>">
																</div>
															</div>
														</div>
														<!--/span-->
													</div>
													<div class="row">
														<div class="col-md-6">
															<div class="form-group">
																<label class="control-label col-md-4">Profesi&oacute;n <span class="required">*</span></label>
																<div class="col-md-8">
																	<?php 
																		$sql = "SELECT codorg, descrip FROM nomprofesiones ORDER BY descrip ASC";
																		$res = $db->query($sql);
																	?>
																	<select name="codpro" id="codpro" class="form-control select2" data-placeholder="Seleccione una profesión">
																		<?php
																			if($operacion=='agregar')
																				echo "<option value=''>Seleccione una profesión</option>";

																			while($fila = $res->fetch_assoc())
																			{
																				if(isset($integrante->codpro) && $integrante->codpro==$fila['codorg'])
																					echo "<option value='".$fila['codorg']."' selected>".$fila['descrip']."</option>";
																				else
																					echo "<option value='".$fila['codorg']."'>".$fila['descrip']."</option>";
																			}
																		?>
																	</select>																	
																</div>
															</div>
														</div>
														<!--/span-->
														<div class="col-md-6">
															<div class="form-group">
																<label class="control-label col-md-4 padding-left-40">Direcci&oacute;n <span class="required">*</span></label>
																<div class="col-md-8">
																	<textarea name="direccion" id="direccion" class="form-control" rows="2" style="resize:vertical;"><?php echo (isset($integrante->direccion)) ? $integrante->direccion : ''; ?></textarea>
																</div>
															</div>
														</div>
														<!--/span-->
													</div>
													<div class="row">
														<div class="col-md-6">
															<div class="form-group">
																<label class="control-label col-md-4">Tel&eacute;fonos <span class="required">*</span></label>
																<div class="col-md-8">
																	<input type="text" name="telefonos" id="telefonos" class="form-control" placeholder="xxxx-xxxx" value="<?php  echo (isset($integrante->telefonos)) ? $integrante->telefonos : ''; ?>">	
																</div>
															</div>
														</div>
														<!--/span-->
														<div class="col-md-6">
															<div class="form-group">
																<label class="control-label col-md-4 padding-left-40">Email</label>
																<div class="col-md-8">
																	<div class="input-group">
																		<span class="input-group-addon">
																			<i class="fa fa-envelope"></i>
																		</span>
																		<input type="text" name="email" id="email" class="form-control" value="<?php  echo (isset($integrante)) ? $integrante->email : ''; ?>">
																	</div>
																</div>
															</div>
														</div>
														<!--/span-->
													</div>
												</div>
												<div class="form-actions fluid">
													<div class="row">
														<div class="col-md-12 text-center">
																<button type="submit" id="btn_guardar1" name="btn_guardar1" class="btn btn-sm blue active">Guardar</button>
																<!--<button type="button" class="btn btn-sm default btn-cancelar">Cancelar</button>-->
														</div>
													</div>
												</div>
											</form>
											<!-- END FORM-->
										</div>
									</div>

									<div class="portlet box blue">
										<div class="portlet-title">
											<div class="caption">
												<i class="fa fa-cogs"></i> Datos Planilla
											</div>
											<div class="tools">
												<a href="javascript:;" class="collapse"></a>
											</div>
										</div>
										<div class="portlet-body">
											<!-- BEGIN FORM-->
											<form action="#" class="form-horizontal" id="form_integrantes2" name="form_integrantes2" method="post">
												<span class="error"></span>
												<div class="form-body">
													<div class="row">
														<div class="col-md-6">
															<div class="form-group">
																<label class="control-label col-md-4">Planilla</label>
																<div class="col-md-8">
																	<?php 
																		$sql = "SELECT codtip, descrip FROM nomtipos_nomina WHERE 1";

																		if($operacion=='agregar')
																			$sql .= " AND (codtip = '".$_SESSION['codigo_nomina']."') ";

																		$res = $db->query($sql);
																	?>
																	<select name="tipnom" id="tipnom" class="form-control select2" data-placeholder="Seleccione una planilla">
																		<?php
																			if($res->num_rows==0)
																				echo "<option value=''>Seleccione una planilla</option>";

																			while($fila = $res->fetch_assoc())
																			{
																				if(isset($integrante->tipnom) && $integrante->tipnom==$fila['codtip'])
																					echo "<option value='".$fila['codtip']."' selected>".$fila['descrip']."</option>";
																				else
																					echo "<option value='".$fila['codtip']."'>".$fila['descrip']."</option>";
																			}
																		?>
																	</select>																	
																</div>
															</div>
														</div>
														<!--/span-->
														<div class="col-md-6">
															<div class="form-group">
																<label class="control-label col-md-4 padding-left-40 padding-right-5">No Ficha</label>
																<div class="col-md-8">
																	<input type="text" name="ficha" id="ficha" class="form-control" value="<?php echo $ficha_actual; ?>" readonly>
																</div>
															</div>															
														</div>
														<!--/span-->
													</div>
													<div class="row">
														<div class="col-md-6">
															<div class="form-group">
																<label class="control-label col-md-4">Situaci&oacute;n <span class="required">*</span></label>
																<div class="col-md-8">
																	<?php 
																		$sql = "SELECT situacion FROM nomsituaciones";
																		$res = $db->query($sql);
																	?>
																	<select name="estado" id="estado" class="form-control select2" data-placeholder="Seleccione situación"><!-- width: 240px !important; -->
																		<?php
																			if($operacion=='agregar')
																				echo "<option value=''>Seleccione situación</option>";

																			while($fila = $res->fetch_assoc())
																			{
																				if(isset($integrante->estado) && $integrante->estado==$fila['situacion'])
																					echo "<option value='".$fila['situacion']."' selected>".$fila['situacion']."</option>";
																				else
																					echo "<option value='".$fila['situacion']."'>".$fila['situacion']."</option>";
																			}
																		?>
																	</select>																	
																</div>
															</div>
														</div>
														<div class="col-md-6">
															<div class="form-group">
																<label class="control-label col-md-4 padding-left-40 padding-right-5">Fecha de Ingreso <span class="required">*</span></label>
																<div class="col-md-8">
																	<div class="input-group date" data-provide="datepicker" data-date-end-date="0d">
																		<input type="text" class="form-control" name="fecing" id="fecing" value="<?php echo (isset($fecha_ingreso)) ? $fecha_ingreso : ''; ?>">
																		<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
																		<!--<span class="input-group-btn">
																			<button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
																		</span>-->
																	</div>	
																	<span class="help-block">
																		 Antiguedad: <span id="antiguedad"></span>
																	</span>
																</div>
															</div>
														</div>
													</div>
													<div class="row">
														<div class="col-md-6">
															<div class="form-group">
																<?php
																	$data = array('Efectivo', 'Cheque', 'Cuenta Ahorro', 'Cuenta Corriente');
																?>															
																<label class="control-label col-md-4">Forma de Pago <span class="required">*</span></label>
																<div class="col-md-8">
																	<select name="forcob" id="forcob" class="form-control select2" data-placeholder="Seleccione la forma de pago">
																		<?php
																			if(!isset($integrante->forcob)) 
																				echo "<option value=''>Seleccione la forma de pago</option>";

																			foreach ($data as $forma_pago) 
																			{
																				if(isset($integrante->forcob) && $integrante->forcob==$forma_pago)
																					echo "<option value='".$forma_pago."' selected>".$forma_pago."</option>";
																				else
																					echo "<option value='".$forma_pago."'>".$forma_pago."</option>";
																			}
																		?>
																	</select>																			
																</div>
															</div>
														</div>
														<div class="col-md-6">
															<div class="form-group">
																<?php $disabled_forma_pago = (isset($integrante->forcob) && $integrante->forcob!='Efectivo') ? '' : 'disabled'; ?>
																<label class="control-label col-md-4 padding-left-40 padding-right-5">Bancos</label>
																<div class="col-md-8">
																	<?php 
																		$sql = "SELECT cod_ban, des_ban FROM nombancos";
																		$res = $db->query($sql);
																	?>
																	<select name="codbancob" id="codbancob" class="form-control select2"  data-placeholder="Seleccione un banco" <?php echo $disabled_forma_pago; ?> >
																		<?php
																			//if($operacion=='agregar')
																				echo "<option value=''>Seleccione un banco</option>";

																			while($fila = $res->fetch_assoc())
																			{
																				if(isset($integrante) && $integrante->codbancob==$fila['cod_ban'])
																					echo "<option value='".$fila['cod_ban']."' selected>".$fila['des_ban']."</option>";
																				else
																					echo "<option value='".$fila['cod_ban']."'>".$fila['des_ban']."</option>";
																			}
																		?>
																	</select>		
																</div>
															</div>
														</div>
													</div>
													<div class="row">
														<div class="col-md-6">
															<div class="form-group">
																<label class="control-label col-md-4">Cuenta</label>
																<div class="col-md-8">
																	<input type="text" name="cuentacob" id="cuentacob" class="form-control" value="<?php echo (isset($integrante->cuentacob)) ? $integrante->cuentacob : ''; ?>" <?php echo $disabled_forma_pago; ?> >
																</div>
															</div>
														</div>
														<div class="col-md-6">
															<div class="form-group">
																<?php
																	$data = array('Fijo'                   => 'Permanente', 
																				  'Contratado Transitorio' => 'Contrato Transitorio', 
																		          'Contratado Contingente' => 'Contrato Contingente', 
																		          'Contratado Servicios'   => 'Contrato Servicios Profesionales');
																?>																														
																<label class="control-label col-md-4 padding-left-40 padding-right-5">Tipo de contrato</label>
																<div class="col-md-8">
																	<?php $disabled_periodo = 'disabled'; ?>
																	<select name="tipemp" id="tipemp" class="form-control select2" data-placeholder="Seleccione un tipo de contrato">
																		<?php
																			if(!isset($integrante->tipemp)) 
																				echo "<option value=''>Seleccione un tipo de contrato</option>";

																			foreach ($data as $clave => $valor) 
																			{
																				if(isset($integrante->tipemp) && $integrante->tipemp==$clave)
																				{
																					echo "<option value='".$clave."' selected>".$valor."</option>";
																					$disabled_periodo = ($clave=='Fijo') ? 'disabled' : '' ;
																				}
																				else
																					echo "<option value='".$clave."'>".$valor."</option>";
																			}
																		?>
																	</select>			
																</div>
															</div>
														</div>
													</div>
													<div class="row">
														<div class="col-md-6">
															<div class="form-group">
																<label class="control-label col-md-4">Fecha Inicio</label>
																<div class="col-md-8">
																	<div class="input-group date" data-provide="datepicker" data-date-end-date="0d">	
																		<input type="text" class="form-control" name="inicio_periodo" id="inicio_periodo" value="<?php echo (isset($inicio_periodo)) ? $inicio_periodo : ''; ?>" <?php echo $disabled_periodo; ?> >
																		<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
																		<!--<span class="input-group-btn">
																			<button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
																		</span>
																		-->
																	</div>
																</div>
															</div>
														</div>
														<div class="col-md-6">
															<div class="form-group">
																<label class="control-label col-md-4 padding-left-40">Fecha Fin</label>
																<div class="col-md-8">
																	<div class="input-group date">	
																		<input type="text" class="form-control" name="fin_periodo" id="fin_periodo" value="<?php echo (isset($fin_periodo)) ? $fin_periodo : ''; ?>" <?php echo $disabled_periodo; ?> >
																		<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
																		<!--<span class="input-group-btn">
																			<button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
																		</span>-->
																	</div>																	
																</div>
															</div>
														</div>
													</div>
													<?php 
													if($empresa->tipo_empresa == 1)
													{ 
														require_once("template/form_tipo_empresa_1.php");
													} 
													else if($empresa->tipo_empresa == 2)
													{
														require_once("template/form_tipo_empresa_2.php");
													}
													?>													
													<div class="row">
														<div class="col-md-6">
															<div class="form-group">
																<label class="control-label col-md-4">Hora Base Trabajada</label>
																<div class="col-md-8">
																	<input type="text" name="hora_base" id="hora_base" class="form-control" value="<?php echo (isset($integrante->hora_base)) ? number_format($integrante->hora_base, '0') : ''; ?>">													
																</div>
															</div>													
														</div>
														<!--/span-->
														<div class="col-md-6">
															<div class="form-group">
																<label class="control-label col-md-4 padding-left-40">Categor&iacute;a <span class="required">*</span></label>
																<div class="col-md-8">
																	<?php 
																		$sql = "SELECT codorg, descrip FROM nomcategorias";
																		$res = $db->query($sql);
																	?>
																	<select name="codcat" id="codcat" class="form-control select2" data-placeholder="Seleccione una categoría">
																		<?php
																			//if($operacion=='agregar')
																			echo "<option value=''>Seleccione una categor&iacute;a</option>";

																			while($fila = $res->fetch_assoc())
																			{
																				if(isset($integrante->codcat) && $integrante->codcat==$fila['codorg'])
																					echo "<option value='".$fila['codorg']."' selected>".$fila['descrip']."</option>";
																				else
																					echo "<option value='".$fila['codorg']."'>".$fila['descrip']."</option>";
																			}
																		?>
																	</select>																
																</div>
															</div>
														</div>
														<!--/span-->
													</div>
													<div class="row">
														<!--/span-->
														<div class="col-md-6">
															<div class="form-group">
																<label class="control-label col-md-4">Cargo <span class="required">*</span></label>
																<div class="col-md-8">
																	<?php 
																		$sql = "SELECT cod_car, des_car FROM nomcargos";
																		$res = $db->query($sql);
																	?>
																	<select name="codcargo" id="codcargo" class="form-control select2" data-placeholder="Seleccione un cargo">
																		<?php
																			//if($operacion=='agregar')
																			echo "<option value=''>Seleccione un cargo</option>";

																			while($fila = $res->fetch_assoc())
																			{
																				if(isset($integrante->codcargo) && $integrante->codcargo==$fila['cod_car'])
																					echo "<option value='".$fila['cod_car']."' selected>".$fila['des_car']."</option>";
																				else
																					echo "<option value='".$fila['cod_car']."'>".$fila['des_car']."</option>";
																			}
																		?>
																	</select>	
																	<input type="hidden" name="cargo_original" id="cargo_original" value="<?php echo isset($integrante->codcargo) ? $integrante->codcargo : ''; ?>">															
																</div>
															</div>
														</div>
														<!--/span-->
														<div class="col-md-6">
															<?php
																$display_acceso   = (isset($usuario->acceso_sueldo) && $usuario->acceso_sueldo==1) ? 'block' : 'none';
																$suesal_propuesto = (isset($usuario->acceso_sueldo) && $usuario->acceso_sueldo==1) ? '' : number_format(0, '2', '.', '');
															?>													
															<div class="form-group" style="display: <?php echo $display_acceso; ?>">
																<label class="control-label col-md-4 padding-left-40">Salario <span class="required">*</span></label>
																<div class="col-md-8">
																	<input type="text" name="suesal" id="suesal" class="form-control" value="<?php echo isset($integrante->suesal) ? number_format($integrante->suesal,'2', '.', '')  : $suesal_propuesto; ?>">
																</div>
															</div>
															<input type="hidden" name="max_sueldo" id="max_sueldo" value="<?php echo $max_sueldo; ?>" />
															<input type="hidden" name="sueldo_original" id="sueldo_original" value="<?php echo (isset($integrante->suesal) && $integrante->suesal) ? $integrante->suesal : 0; ?>">													</div>
													</div>
												</div>												
												<div class="form-actions fluid">
													<div class="row">
														<div class="col-md-12 text-center">
															<button type="submit" id="btn_guardar2" name="btn_guardar2"  class="btn btn-sm blue active">Guardar</button>
															<!--<button type="button" class="btn btn-sm default btn-cancelar">Cancelar</button>-->
														</div>
													</div>
												</div>
											</form>
											<!-- END FORM-->
										</div>
									</div>

									<?php
										$display_niveles = ($empresa->nivel1 == 1 || $empresa->nivel2 == 1 || $empresa->nivel3 == 1 ||
															$empresa->nivel4 == 1 || $empresa->nivel5 == 1 || $empresa->nivel6 == 1 ||
															$empresa->nivel7 == 1) ? 'block' : 'none';
									?>

									<div class="portlet box blue" style="display: <?php echo $display_niveles; ?>">
										<div class="portlet-title">
											<div class="caption">
												<i class="fa fa-check-square-o"></i> Niveles Funcionales
											</div>
											<div class="tools">
												<a href="javascript:;" class="collapse"></a>
											</div>
										</div>
										<div class="portlet-body">
											<!-- BEGIN FORM-->
											<form action="#" class="form-horizontal" id="form_integrantes3" name="form_integrantes3" method="post">
												<?php
													for($i=1; $i<=7; $i++)
													{
														if($empresa->{"nivel".$i} == 1)
														{ 
														?>												
												<div class="form-body">
													<div class="row">
														<div class="col-md-12">
															<div class="form-group">
																<label class="control-label col-md-2"><?php echo $empresa->{"nomniv".$i}; ?></label>
																<div class="col-md-10">
																	<?php 
																		$sql = "SELECT codorg, CONCAT_WS(' ', codorg, descrip, markar) as descrip 
						 														FROM   nomnivel{$i}";
				 														if($i>1)
				 														{
				 															$nivel_anterior = "codnivel".($i-1);
				 															$gerencia = isset($integrante->{$nivel_anterior}) ? $integrante->{$nivel_anterior} : '' ;
				 															$sql .= " WHERE gerencia='".$gerencia."' ";
				 														}				 														
																		$res = $db->query($sql);
																	?>
																	<select name="codnivel<?php echo $i; ?>" id="codnivel<?php echo $i; ?>" class="form-control select2me" data-placeholder="Seleccione <?php echo $empresa->{"nomniv".$i}; ?>">
																		<?php
																			if($operacion=='agregar' || $res->num_rows==0 || (isset($integrante->{"codnivel".$i}) && $integrante->{"codnivel".$i}==0))
																				echo "<option value=''>Seleccione ".$empresa->{"nomniv".$i}."</option>";

																			while($fila = $res->fetch_assoc())
																			{
																				if(isset($integrante->{"codnivel".$i}) && $integrante->{"codnivel".$i}==$fila['codorg'])
																					echo "<option value='".$fila['codorg']."' selected>".$fila['descrip']."</option>";
																				else
																					echo "<option value='".$fila['codorg']."'>".$fila['descrip']."</option>";
																			}
																		?>
																	</select>																	
																</div>
															</div>
														</div>
													</div>
												</div>
														<?php
														}
													}
												?>													
												<div class="form-actions fluid">
													<div class="row">
														<div class="col-md-12 text-center">
															<button type="submit" id="btn_guardar3" name="btn_guardar3"  class="btn btn-sm blue active">Guardar</button>
															<!--<button type="button" class="btn btn-sm default btn-cancelar">Cancelar</button>	-->														
														</div>
													</div>
												</div>
											</form>
											<!-- END FORM-->
										</div>
									</div>

									<div class="portlet box blue">
										<div class="portlet-title">
											<div class="caption">
												<i class="fa fa-bars"></i> Otros Datos
											</div>
											<div class="tools">
												<a href="javascript:;" class="collapse"></a>
											</div>
										</div>
										<div class="portlet-body">
											<!-- BEGIN FORM-->
											<form action="#" class="form-horizontal" id="form_integrantes4" name="form_integrantes4" method="post">
												<div class="form-body">
													<div class="row">
														<div class="col-md-6">
															<div class="form-group">
																<label class="control-label col-md-4">D&iacute;gito Verificador</label>
																<div class="col-md-8">
																	<input type="text" name="dv" id="dv" class="form-control" value="<?php  echo (isset($integrante->dv)) ? $integrante->dv : ''; ?>">													
																</div>
															</div>
														</div>
														<div class="col-md-6">
															<div class="form-group">
																<label class="control-label col-md-4">N&uacute;mero Decreto</label>
																<div class="col-md-8">
																	<input type="text" name="num_decreto" id="num_decreto" class="form-control" value="<?php  echo (isset($integrante->num_decreto)) ? $integrante->num_decreto : ''; ?>">																										
																</div>
															</div>
														</div>
														<!--/span-->
													</div>
													<div class="row">
														<div class="col-md-6">
															<div class="form-group">
																<label class="control-label col-md-4">Fecha Decreto</label>
																<div class="col-md-8">
																	<div class="input-group date date-picker" data-provide="datepicker" data-date-end-date="0d">
																		<input type="text" class="form-control" name="fecha_decreto" id="fecha_decreto" value="<?php echo (isset($fecha_decreto)) ? $fecha_decreto : ''; ?>">
																		<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
																		<!--<span class="input-group-btn">
																			<button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
																		</span>-->
																	</div>																	
																</div>
															</div>
														</div>
														<!--/span-->
														<div class="col-md-6">
															<div class="form-group">
																<label class="control-label col-md-4">Decreto Baja</label>
																<div class="col-md-8">
																	<input type="text" name="num_decreto_baja" id="num_decreto_baja" class="form-control" value="<?php  echo (isset($integrante->num_decreto_baja)) ? $integrante->num_decreto_baja : ''; ?>">
																</div>
															</div>
														</div>
														<!--/span-->
													</div>
													<div class="row">
														<div class="col-md-6">
															<div class="form-group">
																<label class="control-label col-md-4">Fecha Decreto Baja</label>
																<div class="col-md-8">
																	<div class="input-group date date-picker" data-provide="datepicker" data-date-end-date="0d">	
																		<input type="text" class="form-control" name="fecha_decreto_baja" id="fecha_decreto_baja" value="<?php echo (isset($fecha_decreto_baja)) ? $fecha_decreto_baja : ''; ?>">
																		<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
																		<!--<span class="input-group-btn">
																			<button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
																		</span>-->
																	</div>																	
																</div>
															</div>
														</div>
														<div class="col-md-6">
															<div class="form-group">
																<label class="control-label col-md-4">Siacap</label>
																<div class="col-md-8">
																	<input type="text" name="siacap" id="siacap" class="form-control" value="<?php  echo (isset($integrante)) ? $integrante->siacap : ''; ?>">
																</div>
															</div>
														</div>
													</div>
													<div class="row">
														<div class="col-md-6">
															<div class="form-group">
																<label class="control-label col-md-4">Seguro Social</label>
																<div class="col-md-8">
																	<input type="text" name="seguro_social" id="seguro_social" class="form-control" value="<?php echo (isset($integrante->seguro_social)) ? $integrante->seguro_social : ''; ?>">
																</div>
															</div>
														</div>
														<!--/span-->
														<div class="col-md-6">
															<div class="form-group">
																<label class="control-label col-md-4">C&oacute;digo Seguro Social Sipe:</label>
																<div class="col-md-8">
																	<input type="text" name="segurosocial_sipe" id="segurosocial_sipe" class="form-control" value="<?php echo (isset($integrante->segurosocial_sipe)) ? $integrante->segurosocial_sipe : ''; ?>">													
																</div>
															</div>
														</div>
														<!--/span-->
													</div>
												</div>
												<div class="form-actions fluid">
													<div class="row">
														<div class="col-md-12 text-center">
															<button type="submit" id="btn_guardar4" name="btn_guardar4"  class="btn btn-sm blue active">Guardar</button>
															<!--<button type="button" class="btn btn-sm default btn-cancelar">Cancelar</button>-->
														</div>
													</div>
												</div>
											</form>
											<!-- END FORM-->
										</div>
									</div>
									<!--
									<div class="row margin-top-20">
										<div class="col-md-12 text-center">
											<form action="#" id="form_integrantes" name="form_integrantes" method="post">
												<button type="submit" id="btn_guardar_all" name="btn_guardar_all" class="btn btn-sm red active"><i class="fa fa-check"></i> Guardar Todo</button>
												<button type="button" class="btn btn-sm default btn-cancelar">Cancelar</button>		
											</form>						
										</div>
									</div>
									-->
								</div>
								<div id="tab_2" class="tab-pane">Calendario</div>
								<div id="tab_3" class="tab-pane">Carga Familiar</div>
								<div id="tab_4" class="tab-pane">Campos Adicionales</div>
								<div id="tab_5" class="tab-pane">Expediente</div>
								<div id="tab_6" class="tab-pane">Foto Integrante</div>
								<div id="tab_7" class="tab-pane">Imagen Cedula</div>
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
<!-- BEGIN FOOTER -->
<!-- END FOOTER -->
<!-- BEGIN JAVASCRIPTS(Load javascripts at bottom, this will reduce page load time) -->
<!-- BEGIN CORE PLUGINS -->
<!--[if lt IE 9]>
<script src="../../../includes/assets/plugins/respond.min.js"></script>
<script src="../../../includes/assets/plugins/excanvas.min.js"></script>
<![endif]-->
<script src="../../../includes/assets/plugins/jquery-1.10.2.min.js" type="text/javascript"></script>
<script src="../../../includes/assets/plugins/jquery-migrate-1.2.1.min.js" type="text/javascript"></script>
<!-- IMPORTANT! Load jquery-ui-1.10.3.custom.min.js before bootstrap.min.js to fix bootstrap tooltip conflict with jquery ui tooltip -->
<script src="../../../includes/assets/plugins/jquery-ui/jquery-ui-1.10.3.custom.min.js" type="text/javascript"></script>
<script src="../../../includes/assets/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script src="../../../includes/assets/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js" type="text/javascript"></script>
<script src="../../../includes/assets/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
<script src="../../../includes/assets/plugins/jquery.blockui.min.js" type="text/javascript"></script>
<script src="../../../includes/assets/plugins/jquery.cokie.min.js" type="text/javascript"></script>
<script src="../../../includes/assets/plugins/uniform/jquery.uniform.min.js" type="text/javascript"></script>
<!-- END CORE PLUGINS -->
<!-- BEGIN PAGE LEVEL PLUGINS -->
<script type="text/javascript" src="../../../includes/assets/plugins/jquery-validation/dist/jquery.validate.min.js"></script>
<script type="text/javascript" src="../../../includes/assets/plugins/jquery-validation/dist/additional-methods.min.js"></script>
<script type="text/javascript" src="../../../includes/assets/plugins/jquery-validation/localization/messages_es.js"></script>
<script type="text/javascript" src="../../../includes/assets/plugins/bootstrap-select/bootstrap-select.min.js"></script>
<script type="text/javascript" src="../../../includes/assets/plugins/select2/select2.min.js"></script>
<script type="text/javascript" src="../../../includes/assets/plugins/select2/select2_locale_es.js"></script>
<script type="text/javascript" src="../../../includes/assets/plugins/bootstrap-datepicker-1.5.1/js/bootstrap-datepicker.min.js"></script>
<script type="text/javascript" src="../../../includes/assets/plugins/bootstrap-datepicker-1.5.1/locales/bootstrap-datepicker.es.min.js"></script>
<!-- <script type="text/javascript" src="../../../includes/assets/plugins/jquery-multi-select/js/jquery.multi-select.js"></script> -->
<script type="text/javascript" src="../../../includes/assets/plugins/fancybox/source/jquery.fancybox.pack.js"></script>
<!-- END PAGE LEVEL PLUGINS -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="../../../includes/assets/scripts/core/app.js"></script>
<script src="../lib/funciones_ag_integrantes.js?<?php echo time(); ?>"></script>
<script src="js/validate_form.js?<?php echo time(); ?>"></script>
<script>
jQuery(document).ready(function() {
	App.init();
});

$('[data-toggle="tooltip"]').tooltip();

$('[data-toggle="tab"]').tooltip({
    trigger: 'hover',
    placement: 'top',
    animate: true,
    container: 'body'
});

$('a.fancybox').each(function() {
   var link = this;
   $(this).fancybox({
      beforeShow: function() {
		 	if ($(link).hasClass('fancybox-cedula'))
		        $('.fancybox-wrap').addClass("fancybox-cedula");    
       }
    });
});

$(".btn-cancelar").click(function(){
	document.location.href = '../maestro_personal.php';
});

$(".btn-print").click(function(){
	document.location.href = '../../fpdf/datos_personal.php?cedula=' + CEDULA_ACTUAL + '&ficha=' + FICHA_ACTUAL;
});

$('#btn_guardar').click(function(){

	var form1 = $('#form_integrantes1').valid();
	var form2 = $('#form_integrantes2').valid();

	if(!form1 || !form2)
	{
		//alert("Hay datos incompletos. Por favor, verifique");
		return false;
	} 

	var formAll = $('#form_integrantes');

	
	$(":input:not(:button)").each(function(){ // :input:not(:button)
		//$(this).clone().css("display","none").appendTo(formAll);
		var elemento = $(this);
		console.log("Elemento: "+elemento.attr('id')+' Valor: '+elemento.val());

		$(this).clone().css("display","none").appendTo(formAll);
	});

	var inputs = $('input, textarea, select')
	             .not(':input[type=button], :input[type=submit], :input[type=reset]');

	$(inputs).each(function() {
	    //$(this).clone().css( "display", "none" ).appendTo(formAll);
	});

	// form1.find(':input').not('.search_button').each(function(){});

    // form1.children().each(function(i){});
});	
</script>
<!-- END JAVASCRIPTS -->
</body>
<!-- END BODY -->
</html>