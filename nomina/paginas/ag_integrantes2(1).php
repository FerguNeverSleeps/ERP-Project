<?php
// require_once("../lib/common.php");
require_once('../lib/database.php');

$db           = new Database($_SESSION['bd']);

$ficha_actual = (isset($_GET['ficha'])) ? $_GET['ficha'] : '';
$operacion    = (isset($_GET['edit']))  ? 'editar' : 'agregar';

if(isset($_POST['btn-guardar']) )
{
	require "ag_integrantes2_guardar.php";
}

$sql = "SELECT e.nivel1, e.nomniv1, e.nivel2, e.nomniv2, e.nivel3, e.nomniv3, e.nivel4, e.nomniv4, e.nivel5, e.nomniv5,
		       e.nivel6, e.nomniv6, e.nivel7, e.nomniv7, e.tipo_empresa
		FROM nomempresa e";
$res        = $db->query($sql);
$empresa    = $res->fetch_object();

$sql        = "SELECT acceso_sueldo FROM ".SELECTRA_CONF_PYME.".nomusuarios WHERE login_usuario='".$_SESSION['usuario']."'";
$res        = $db->query($sql);
$usuario    = $res->fetch_object();

$sql        = "SELECT COUNT(*) as cantidad, MIN(LENGTH(nomposicion_id)) as longitud FROM nomposicion";
$posiciones = $db->query($sql)->fetch_object();

if($operacion=='editar')
{
	$sql = "SELECT * FROM nompersonal WHERE ficha='".$ficha_actual."'";
	$res = $db->query($sql);

	if($integrante = $res->fetch_object())
	{
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
}
else
{
    $sql = "SELECT max(ficha) as valor FROM nompersonal";
    $res = $db->query($sql);

    $fila = $res->fetch_assoc();
    $ficha_actual = $fila['valor'] + 1;
}
?>
<?php include("../header4.php"); ?>
<link href="../../includes/assets/plugins/bootstrap-datepicker/css/datepicker.css" rel="stylesheet" type="text/css" />
<link href="../../includes/assets/plugins/fancybox/source/jquery.fancybox.css" rel="stylesheet" type="text/css"/>
<style>
.fancybox-wrap {
	top: 50px !important;
}

.page-full-width .page-content, body {
	background-color: #FFFFFF !important;
}
.tabbable-custom .nav-tabs > li.active {
    border-top: 3px solid #157fcc;
}

.portlet > .portlet-body.blue, .portlet.blue {
    background-color: #ffffff !important;
}

.portlet > .portlet-title > .caption {
    font-size: 13px;
    font-weight: bold;
    font-family: helvetica, arial, verdana, sans-serif;
    margin-bottom: 3px;
}

.form-horizontal .control-label {
    text-align: left;
    padding-top: 3px;
}

.form-body{
	padding-bottom: 5px;
}

span.tipo_contrato{
	display: inline-block;
	width: 150px; 
}

iframe {
	margin: 0px; 
	width: 100% !important;
}
</style>
<div class="page-container">
	<div class="page-content-wrapper">
		<div class="page-content">
			<!-- BEGIN PAGE CONTENT-->
			<div class="row">
				<div class="col-md-12">
					<div class="tabbable tabbable-custom tabbable-reversed"> <!-- boxless tabbable-reversed  -->
						<ul class="nav nav-tabs">
							<li class="active"><a href="#tab_0" id="tab-personal" data-toggle="tab"><i class="fa fa-suitcase"></i>Generales</a></li>
							<li><a href="#tab_5" id="tab-personal2" data-toggle="tab"><i class="fa fa-plus"></i>Otros Datos</a></li>
							<li><a href="#tab_6" id="tab-personal3" data-toggle="tab"><i class="fa fa-cubes"></i>Estructura</a></li>
							<?php
								if($operacion=='editar')
								{
							?>
							<li><a href="#tab_1" id="tab-calendario" data-toggle="tab"><i class="fa fa-calendar"></i>Calendario</a></li>
							<li><a href="#tab_2" id="tab-cargas-familiares" data-toggle="tab"><i class="fa fa-child"></i>Carga Familiar</a></li>
							<li><a href="#tab_3" id="tab-campos-adicionales" data-toggle="tab"><i class="fa fa-plus-square-o"></i>Campos Adic.</a></li>
							<li><a href="#tab_4" id="tab-expediente" data-toggle="tab"><i class="fa fa-archive"></i>Expediente</a></li>
							<li><a href="#tab_7" id="tab-tiempos" data-toggle="tab"><i class="fa fa-clock-o"></i>Tiempos</a></li>
							<?php
								}
							?>
						</ul>
						<div class="tab-content">
							<div class="tab-pane active" id="tab_0">
								<!-- BEGIN FORM-->
								<form class="form-horizontal" id="form-integrantes" name="form-integrantes" method="post" enctype="multipart/form-data" role="form" style="margin-bottom: 5px;">
									<div class="form-body">
										<div class="row">
											<div class="col-md-9">
												<div class="form-group">
													<label class="control-label col-md-3">Planilla</label>
													<div class="col-md-9">
														<?php 
															$sql = "SELECT codtip, descrip FROM nomtipos_nomina WHERE 1";

															//if($operacion=='agregar')
															//	$sql .= " AND (codtip = '".$_SESSION['codigo_nomina']."') ";

															$res = $db->query($sql);
														?>
														<select name="tipnom" id="tipnom" class="form-control select2">
															<?php
																if($res->num_rows==0)
																	echo "<option value=''>Seleccione una planilla</option>";

																while($fila = $res->fetch_assoc())
																{
																	if(isset($integrante) && $integrante->tipnom==$fila['codtip'])
																		echo "<option value='".$fila['codtip']."' selected>".$fila['codtip']."</option>";
																	else
																		echo "<option value='".$fila['codtip']."'>".$fila['codtip']."</option>";
																}
															?>
														</select>
													</div>
												</div>
												<?php
												if($empresa->tipo_empresa==1)
													{
													?>
														<div class="form-group">
															<label class="control-label col-md-3">Posici&oacute;n <span class="required">*</span></label>
															<div class="col-md-9">
																<?php 
																	$sql = "SELECT nomposicion_id, sueldo_propuesto FROM nomposicion";
																	$res = $db->query($sql);

																	$max_sueldo = (isset($usuario->acceso_sueldo) && $usuario->acceso_sueldo==1) ? '' : 0;
																?>
																<select name="nomposicion_id" id="nomposicion_id" class="form-control select2">
																	<?php
																		//if($operacion=='agregar')
																		echo "<option value=''>Seleccione la posición</option>";

																		while($fila = $res->fetch_assoc())
																		{
																			if(isset($integrante->nomposicion_id) && $integrante->nomposicion_id==$fila['nomposicion_id'])
																			{
																				$max_sueldo = $fila['sueldo_propuesto'];
																				echo "<option value='".$fila['nomposicion_id']."' selected>".$fila['nomposicion_id']."</option>";
																			}
																			else
																				echo "<option value='".$fila['nomposicion_id']."'>".$fila['nomposicion_id']."</option>";
																		}
																	?>
																</select>
															</div>
															<input type="hidden" name="posicion_original" id="posicion_original" value="<?php echo isset($integrante->nomposicion_id) ? $integrante->nomposicion_id : ''; ?>">
														</div>
													<?php
													}
													?>

												<div class="form-group">
													<label class="control-label col-md-3">Cargo <span class="required">*</span></label>
													<div class="col-md-9">
														<?php 
															$sql = "SELECT cod_car, des_car FROM nomcargos";
															$res = $db->query($sql);
														?>
														<select name="codcargo" id="codcargo" class="form-control">
															<?php
																//if($operacion=='agregar')
																echo "<option value=''>Seleccione un cargo</option>";

																while($fila = $res->fetch_assoc())
																{
																	if(isset($integrante) && $integrante->codcargo==$fila['cod_car'])
																		echo "<option value='".$fila['cod_car']."' selected>".$fila['des_car']."</option>";
																	else
																		echo "<option value='".$fila['cod_car']."'>".$fila['des_car']."</option>";
																}
															?>
														</select>
													</div>
													<input type="hidden" name="cargo_original" id="cargo_original" value="<?php echo isset($integrante->codcargo) ? $integrante->codcargo : ''; ?>">
												</div>
												<div id="posicion_disponible">
												</div>
												<div class="form-group" style="display: <?php echo $display_acceso; ?>">
													<label class="control-label col-md-3">Salario <span class="required">*</span></label>
													<div class="col-md-9">
														<input type="<?php echo $type_acceso; ?>" name="suesal" id="suesal" class="form-control">
														<input type="hidden" name="sueldo_original" id="sueldo_original" value="<?php echo (isset($integrante) && $integrante->suesal) ? $integrante->suesal : 0; ?>">
													</div>
												</div>
												<div class="form-group" style="display: <?php echo $display_acceso; ?>">
													<label class="control-label col-md-3">Antigüedad (011) <span class="required">*</span></label>
													<div class="col-md-9">
														<input type="<?php echo $type_acceso; ?>" name="antiguedad" id="antiguedad" class="form-control">
													</div>
												</div>
												<div class="form-group" style="display: <?php echo $display_acceso; ?>">
													<label class="control-label col-md-3">Zonas Apartadas (012) <span class="required">*</span></label>
													<div class="col-md-9">
														<input type="<?php echo $type_acceso; ?>" name="zonas_apartadas" id="zonas_apartadas" class="form-control">
													</div>
												</div>
												<div class="form-group" style="display: <?php echo $display_acceso; ?>">
													<label class="control-label col-md-3">Jefaturas (013) <span class="required">*</span></label>
													<div class="col-md-9">
														<input type="<?php echo $type_acceso; ?>" name="jefaturas" id="jefaturas" class="form-control">
														<input type="hidden" name="max_sueldo" id="max_sueldo" value="<?php echo $max_sueldo; ?>" />
													</div>
												</div>
												<div class="form-group" style="display: <?php echo $display_acceso; ?>">
													<label class="control-label col-md-3">Especialidad o Exc. (019) <span class="required">*</span></label>
													<div class="col-md-9">
														<input type="text" name="especialidad" id="especialidad" class="form-control">
													</div>
												</div>
												<div class="form-group" style="display: <?php echo $display_acceso; ?>">
													<label class="control-label col-md-3">Otros (080) <span class="required">*</span></label>
													<div class="col-md-9">
														<input type="<?php echo $type_acceso; ?>" name="otros" id="otros" class="form-control">
													</div>
												</div>
												<div class="form-group" style="display: <?php echo $display_acceso; ?>">
													<label class="control-label col-md-3">Gastos de Representación (030) <span class="required">*</span></label>
													<div class="col-md-9">
														<input type="<?php echo $type_acceso; ?>" name="gastos_repre" id="gastos_repre" class="form-control">

													</div>
												</div>
												<div class="form-group">
													<label class="control-label col-md-3">N&uacute;mero Decreto/Resoluci&oacute;n</label>
													<div class="col-md-9">
														<input type="text" name="num_decreto" id="num_decreto" class="form-control" value="<?php  echo (isset($integrante)) ? $integrante->num_decreto : ''; ?>">
													</div>
												</div>
												<div class="form-group">
													<label class="control-label col-md-3">Fecha Decreto/Resoluci&oacute;n</label>
													<div class="col-md-9">
														<div class="input-group date date-picker" data-provide="datepicker" data-date-end-date="0d">
															<input type="text" class="form-control" name="fecha_decreto" id="fecha_decreto" value="<?php echo (isset($fecha_decreto)) ? $fecha_decreto : ''; ?>">
															<span class="input-group-btn">
																<button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
															</span>
														</div>
													</div>
												</div>
												<div class="form-group">
													<label class="control-label col-md-3">N° Colaborador</label>
													<div class="col-md-9">
														<input type="text" name="ficha" id="ficha" class="form-control" value="<?php echo $ficha_actual; ?>" > 
													</div>
													<input type="hidden" name="personal_id" id="personal_id" value="<?php echo (isset($integrante) ? $integrante->personal_id : '0'); ?>">
												</div>
												<div class="form-group">
													<label class="control-label col-md-3">Nombres <span class="required">*</span></label>
													<div class="col-md-3">
														<input type="text" name="nombres" id="nombres" class="form-control" value="<?php  echo (isset($integrante)) ? $integrante->nombres : ''; ?>">
													</div>
													<label class="control-label col-md-3">Apellido Paterno <span class="required">*</span></label>
													<div class="col-md-3">
														<input type="text" name="apellidos" id="apellidos" class="form-control" value="<?php  echo (isset($integrante)) ? $integrante->apellidos : ''; ?>">
													</div>
												</div>

												<div class="form-group">
													<label class="control-label col-md-3">Apellido Materno </label>
													<div class="col-md-3">
														<input type="text" name="apellido_materno" id="apellido_materno" class="form-control" value="<?php  echo (isset($integrante)) ? $integrante->apellido_materno : ''; ?>">
													</div>
													<label class="control-label col-md-3">Apellido de Casada </label>
													<div class="col-md-3">
														<input type="text" name="apellido_casada" id="apellido_casada" class="form-control" value="<?php  echo (isset($integrante)) ? $integrante->apellido_casada : ''; ?>">
													</div>
												</div>
												<div class="form-group">
													<label class="control-label col-md-3">Usuario Workflow</label>
													<div class="col-md-3">
														<input type="text" name="usuario" id="usuario" class="form-control">
													</div>
												</div>
												<div class="form-group">
													<label class="control-label col-md-3">Foto</label>
													<div class="col-md-9">
														<input type="file" id="foto" name="foto">
													</div>
												</div>
												<div class="form-group">
													<label class="control-label col-md-3">C&eacute;dula <span class="required">*</span></label>
													<div class="col-md-9">
														<input type="text" name="cedula" id="cedula" class="form-control" value="<?php  echo (isset($integrante)) ? $integrante->cedula : ''; ?>">
													</div>
												</div>
												<div class="form-group">
													<label class="control-label col-md-3">Foto C&eacute;dula</label>
													<div class="col-md-9">
														<input type="file" id="imagen_cedula" name="imagen_cedula">
													</div>
												</div>
												<div class="form-group">
													<label class="control-label col-md-3">Seguro Social</label>
													<div class="col-md-3">
														<input type="text" name="seguro_social" id="seguro_social" class="form-control" value="<?php echo (isset($integrante)) ? $integrante->seguro_social : ''; ?>">
													</div>
													<label class="control-label col-md-3">C&oacute;digo Seguro Social Sipe:</label>
													<div class="col-md-3">
														<input type="text" name="segurosocial_sipe" id="segurosocial_sipe" class="form-control" value="<?php echo (isset($integrante)) ? $integrante->segurosocial_sipe : ''; ?>">
													</div>
												</div>
												<div class="form-group">
													<?php
														$data = array('Soltero/a', 'Casado/a', 'Viudo/a', 'Divorciado/a', 'Unido');
													?>
													<label class="control-label col-md-3">Estado Civil <?php if($empresa->tipo_empresa != 1){ ?><span class="required">*</span><?php } ?></label>
													<div class="col-md-9">
														<select name="estado_civil" id="estado_civil" class="form-control select2">
															<?php
																if(!isset($integrante->estado_civil)) 
																	echo "<option value=''>Seleccione el estado civil</option>";

																foreach ($data as $estado_civil) 
																{
																	if(isset($integrante) && $integrante->estado_civil==$estado_civil)
																		echo "<option value='".$estado_civil."' selected>".$estado_civil."</option>";
																	else
																		echo "<option value='".$estado_civil."'>".$estado_civil."</option>";
																}
															?>
														</select>
													</div>
												</div>
												<div class="form-group">
													<?php
														$nacionalidad1 = 'checked';
														$nacionalidad2 = $nacionalidad3 = '';

														if(isset($integrante))
														{
															$nacionalidad1 = ($integrante->nacionalidad=='1')  ? 'checked' : '';
															$nacionalidad2 = ($integrante->nacionalidad=='2')  ? 'checked' : '';
															$nacionalidad3 = ($integrante->nacionalidad=='3')  ? 'checked' : '';
														} 
													?>
													<label class="control-label col-md-3">Nacionalidad</label>
													<div class="col-md-9">
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
												<div class="form-group">
													<?php
														$sexo1 = 'checked';
														$sexo2 = '';

														if(isset($integrante))
														{
															$sexo1 = ($integrante->sexo=='Masculino') ? 'checked' : '';
															$sexo2 = ($integrante->sexo=='Femenino')  ? 'checked' : '';
														} 
													?>
													<label class="control-label col-md-3">Sexo</label>
													<div class="col-md-9">
														<div class="radio-list">
															<label class="radio-inline">
															<input type="radio" name="sexo" id="sexo1" value="Masculino" <?php echo $sexo1; ?>> Masculino</label>
															<label class="radio-inline">
															<input type="radio" name="sexo" id="sexo2" value="Femenino"  <?php echo $sexo2; ?>> Femenino</label>
														</div>
													</div>
												</div>
												<div class="form-group">
													<label class="control-label col-md-3">Tel&eacute;fonos <?php if($empresa->tipo_empresa != 1){ ?><span class="required">*</span><?php } ?></label>
													<div class="col-md-9">
														<input type="text" name="telefonos" id="telefonos" class="form-control" placeholder="xxxx-xxxx" value="<?php  echo (isset($integrante)) ? $integrante->telefonos : ''; ?>">	
													</div>
												</div>
												<div class="form-group">
													<label class="control-label col-md-3">Fecha de Nacimiento <?php if($empresa->tipo_empresa != 1){ ?><span class="required">*</span><?php } ?></label>
													<div class="col-md-9">
														<div class="input-group date date-picker" data-provide="datepicker" data-date-end-date="0d">
															<input type="text" class="form-control" name="fecnac" id="fecnac" value="<?php echo (isset($fecha_nacimiento)) ? $fecha_nacimiento : ''; ?>">
															<span class="input-group-btn">
																<button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
															</span>
														</div>
													</div>
												</div>
												<div class="form-group">
													<label class="control-label col-md-3">Edad</label>
													<div class="col-md-9">
														<strong id="edad">0 Años</strong>
													</div>
												</div>
												<div class="form-group">
													<label class="control-label col-md-3">Lugar de Nacimiento <?php if($empresa->tipo_empresa != 1){ ?><span class="required">*</span><?php } ?></label>
													<div class="col-md-9">
														<input type="text" name="lugarnac" id="lugarnac" class="form-control" value="<?php  echo (isset($integrante)) ? $integrante->lugarnac : ''; ?>">
													</div>
												</div>
												<div class="form-group">
													<label class="control-label col-md-3">Profesi&oacute;n <?php if($empresa->tipo_empresa != 1){ ?><span class="required">*</span><?php } ?></label>
													<div class="col-md-9">
														<?php 
															$sql = "SELECT codorg, descrip FROM nomprofesiones ORDER BY descrip ASC";

															$res = $db->query($sql);
														?>
														<select name="codpro" id="codpro" class="form-control select2">
															<?php
																if($operacion=='agregar')
																	echo "<option value=''>Seleccione una profesión</option>";

																while($fila = $res->fetch_assoc())
																{
																	if(isset($integrante) && $integrante->codpro==$fila['codorg'])
																		echo "<option value='".$fila['codorg']."' selected>".$fila['descrip']."</option>";
																	else
																		echo "<option value='".$fila['codorg']."'>".$fila['descrip']."</option>";
																}
															?>
														</select>
													</div>
												</div>
												<div class="form-group">
													<label class="control-label col-md-3">Direcci&oacute;n <?php if($empresa->tipo_empresa != 1){ ?><span class="required">*</span><?php } ?></label>
													<div class="col-md-9">
														<textarea name="direccion" id="direccion" class="form-control" rows="3" style="resize:vertical;"><?php echo (isset($integrante)) ? $integrante->direccion : ''; ?></textarea>
													</div>
												</div>
												<div class="form-group">
													<label class="control-label col-md-3">Otra Direcci&oacute;n <?php if($empresa->tipo_empresa != 1){ ?><span class="required">*</span><?php } ?></label>
													<div class="col-md-9">
														<textarea name="direccion2" id="direccion2" class="form-control" rows="3" style="resize:vertical;"><?php echo (isset($integrante)) ? $integrante->direccion2 : ''; ?></textarea>
													</div>
												</div>
												
												<div class="form-group">
													<label class="control-label col-md-3">E-mail Sugerido</label>
													<div class="col-md-9">
														<input type="text" name="email" id="email" class="form-control" value="<?php  echo (isset($integrante)) ? $integrante->email : ''; ?>">
													</div>
												</div>
												<div class="form-group">
													<label class="control-label col-md-3">Situaci&oacute;n <?php if($empresa->tipo_empresa != 1){ ?><span class="required">*</span><?php } ?></label>
													<div class="col-md-9">
														<?php 
															$sql = "SELECT situacion FROM nomsituaciones";
															$res = $db->query($sql);
														?>
														<select name="estado" id="estado" class="form-control select2">
															<?php
																if($operacion=='agregar')
																	echo "<option value=''>Seleccione situación</option>";

																while($fila = $res->fetch_assoc())
																{
																	if(isset($integrante) && $integrante->estado==$fila['situacion'])
																		echo "<option value='".$fila['situacion']."' selected>".$fila['situacion']."</option>";
																	else
																		echo "<option value='".$fila['situacion']."'>".$fila['situacion']."</option>";
																}
															?>
														</select>
													</div>
												</div>
												<div class="form-group">
													<label class="control-label col-md-3">D&iacute;gito Verificador</label>
													<div class="col-md-9">
														<input type="text" name="dv" id="dv" class="form-control" value="<?php  echo (isset($integrante)) ? $integrante->dv : ''; ?>">
													</div>
												</div>
												<div class="form-group">
													<label class="control-label col-md-3">Fecha de Ingreso <?php if($empresa->tipo_empresa != 1){ ?><span class="required">*</span><?php } ?></label>
													<div class="col-md-9">
														<div class="input-group date date-picker" data-provide="datepicker" data-date-end-date="0d">
															<input type="text" class="form-control" name="fecing" id="fecing" value="<?php echo (isset($fecha_ingreso)) ? $fecha_ingreso : ''; ?>">
															<span class="input-group-btn">
																<button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
															</span>
														</div>
													</div>
												</div>
												<div class="form-group">
													<label class="control-label col-md-3">Antiguedad</label>
													<div class="col-md-9">
														<strong id="antiguedad"></strong>
													</div>
												</div>

												<div class="form-group">
													<label class="control-label col-md-3">Decreto/Resoluci&oacute;n Baja</label>
													<div class="col-md-9">
														<input type="text" name="num_decreto_baja" id="num_decreto_baja" class="form-control" value="<?php  echo (isset($integrante)) ? $integrante->num_decreto_baja : ''; ?>">
													</div>
												</div>
												<div class="form-group">
													<label class="control-label col-md-3">Fecha Decreto/Resoluci&oacute;n Baja</label>
													<div class="col-md-9">
														<div class="input-group date date-picker" data-provide="datepicker" data-date-end-date="0d">	
															<input type="text" class="form-control" name="fecha_decreto_baja" id="fecha_decreto_baja" value="<?php echo (isset($fecha_decreto_baja)) ? $fecha_decreto_baja : ''; ?>">
															<span class="input-group-btn">
																<button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
															</span>
														</div>
													</div>
												</div>
												<div class="form-group">
													<label class="control-label col-md-3">Siacap</label>
													<div class="col-md-9">
														<input type="text" name="siacap" id="siacap" class="form-control" value="<?php  echo (isset($integrante)) ? $integrante->siacap : ''; ?>">
													</div>
												</div>
												<div class="form-group">
													<?php
														$data = array('Efectivo', 'Cheque', 'Cuenta Ahorro', 'Cuenta Corriente');
													?>
													<label class="control-label col-md-3">Forma de Pago <?php if($empresa->tipo_empresa != 1){ ?><span class="required">*</span><?php } ?></label>
													<div class="col-md-9">
														<select name="forcob" id="forcob" class="form-control select2">
															<?php
																if(!isset($integrante->forcob)) 
																	echo "<option value=''>Seleccione la forma de pago</option>";

																foreach ($data as $forma_pago) 
																{
																	if(isset($integrante) && $integrante->forcob==$forma_pago)
																		echo "<option value='".$forma_pago."' selected>".$forma_pago."</option>";
																	else
																		echo "<option value='".$forma_pago."'>".$forma_pago."</option>";
																}
															?>
														</select>
													</div>

												</div>
												<?php $display_forma_pago = (isset($integrante) && $integrante->forcob!='Efectivo') ? 'display: block' : 'display: none'; ?>
												<div class="form-group forma-pago" style="<?php echo $display_forma_pago; ?>">
													<label class="control-label col-md-3">Bancos</label>
													<div class="col-md-9">
														<?php 
															$sql = "SELECT cod_ban, des_ban FROM nombancos";
															$res = $db->query($sql);
														?>
														<select name="codbancob" id="codbancob" class="form-control select2me">
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
												<div class="form-group forma-pago" style="<?php echo $display_forma_pago; ?>">
													<label class="control-label col-md-3">Cuenta <span class="required">*</span></label>
													<div class="col-md-9">
														<input type="text" name="cuentacob" id="cuentacob" class="form-control" value="<?php echo (isset($integrante)) ? $integrante->cuentacob : ''; ?>">
													</div>
												</div>
												
												<div class="form-group">
													<?php
														$tipemp1 = 'checked';
														$tipemp2 = $tipemp3 = $tipemp4 = '';
														$disabled_periodo = 'disabled';

														if(isset($integrante))
														{
															$tipemp1 = ($integrante->tipemp=='Fijo')  ? 'checked' : '';
															$tipemp2 = ($integrante->tipemp=='Contratado Transitorio')  ? 'checked' : '';
															$tipemp3 = ($integrante->tipemp=='Contratado Contingente')  ? 'checked' : '';
															$tipemp4 = ($integrante->tipemp=='Contratado Servicios')    ? 'checked' : '';

															$disabled_periodo = ($tipemp1=='checked') ? 'disabled' : '' ;
														} 
													?>
													<label class="control-label col-md-3">Tipo de Contrato</label>
													<div class="col-md-9">
														<div class="radio-list">
															<label class="radio-inline">
															<input type="radio" name="tipemp" id="tipemp1" value="Fijo" <?php echo $tipemp1; ?>> <span class="tipo_contrato">Permanente</span></label>
															<label class="radio-inline">
															<input type="radio" name="tipemp" id="tipemp2" value="Contratado Transitorio" <?php echo $tipemp2; ?>> Contrato Transitorio</label>
														</div>	
														<div class="radio-list">
															<label class="radio-inline">
															<input type="radio" name="tipemp" id="tipemp3" value="Contratado Contingente" <?php echo $tipemp3; ?>> <span class="tipo_contrato">Contrato Contingente</span></label>
															<label class="radio-inline">
															<input type="radio" name="tipemp" id="tipemp4" value="Contratado Servicios" <?php echo $tipemp4; ?>> Contrato Servicios Profesionales</label>														</div>
													</div>
												</div>
												<div class="form-group">
													<label class="control-label col-md-3"></label>
													<div class="col-md-9">
														<div class="row">
															<label class="control-label col-md-3" style="padding-left: 20px">Fecha Inicio</label>
															<div class="col-md-5" style="padding-right: 0px">
																<div class="input-group date date-picker" data-provide="datepicker" data-date-end-date="0d">	
																	<input type="text" class="form-control" name="inicio_periodo" id="inicio_periodo" value="<?php echo (isset($inicio_periodo)) ? $inicio_periodo : ''; ?>" <?php echo $disabled_periodo; ?> >
																	<span class="input-group-btn">
																		<button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
																	</span>
																</div>
															</div>
														</div>
														<div class="row" style="margin-top: 10px">
															<label class="control-label col-md-3" style="padding-left: 20px">Fecha Fin</label>
															<div class="col-md-5" style="padding-right: 0px">
																<div class="input-group date date-picker">	
																	<input type="text" class="form-control" name="fin_periodo" id="fin_periodo" value="<?php echo (isset($fin_periodo)) ? $fin_periodo : ''; ?>" <?php echo $disabled_periodo; ?> >
																	<span class="input-group-btn">
																		<button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
																	</span>
																</div>
															</div>
														</div>	
													</div>
												</div>
												<?php 
													if($empresa->tipo_empresa == 2)
													{
														?>
														<div class="form-group">
															<label class="control-label col-md-3">Puesto de Trabajo <span class="required">*</span></label>
															<div class="col-md-9">
															<?php 
																$sql  = "SELECT v.*, CONCAT_WS(', ', codigo, cliente, ubicacion, descripcion) as desc_puesto	
																	     FROM   vig_puestos v";

																$res  = $db->query($sql);
															?>
															<select name="puesto_id" id="puesto_id" class="form-control select2">
																<?php
																	//if($operacion=='agregar')
																	echo "<option value=''>Seleccione un puesto</option>";

																	while($fila = $res->fetch_assoc())
																	{
																		if(isset($integrante) && $integrante->puesto_id==$fila['id_puesto'])
																		{
																			$registro_puesto = $fila;
																			echo "<option value='".$fila['id_puesto']."' selected>".$fila['desc_puesto']."</option>";
																		}
																		else
																			echo "<option value='".$fila['id_puesto']."'>".$fila['desc_puesto']."</option>";
																	}
																?>
															</select>
															</div>
														</div>
														<div class="form-group" id="horario_puesto" style="display: <?php echo (isset($integrante) && $integrante->puesto_id!='') ? 'block': 'none'; ?>">
															<label class="control-label col-md-3">Horario del Puesto</label>
															<div class="col-md-9">
																<table class="table table-bordered" style="font-size: 14px;">
																	<thead>
																		<tr>
																			<th class="text-center">Lunes</th>
																			<th class="text-center">Martes</th>
																			<th class="text-center">Mi&eacute;rcoles</th>
																			<th class="text-center">Jueves</th>
																			<th class="text-center">Viernes</th>
																			<th class="text-center">S&aacute;bado</th>
																			<th class="text-center">Domingo</th>
																		</tr>
																	</thead>
																	<tbody class="mostrarHorario">
																		<tr>
																			<?php
																				if($registro_puesto)
																				{
																				?>
																					<td class="text-center"><?php echo $registro_puesto['dia1_desde']; ?><br>-<br><?php echo $registro_puesto['dia1_hasta']; ?></td>
																					<td class="text-center"><?php echo $registro_puesto['dia2_desde']; ?><br>-<br><?php echo $registro_puesto['dia2_hasta']; ?></td>
																					<td class="text-center"><?php echo $registro_puesto['dia3_desde']; ?><br>-<br><?php echo $registro_puesto['dia3_hasta']; ?></td>
																					<td class="text-center"><?php echo $registro_puesto['dia4_desde']; ?><br>-<br><?php echo $registro_puesto['dia4_hasta']; ?></td>
																					<td class="text-center"><?php echo $registro_puesto['dia5_desde']; ?><br>-<br><?php echo $registro_puesto['dia5_hasta']; ?></td>
																					<td class="text-center"><?php echo $registro_puesto['dia6_desde']; ?><br>-<br><?php echo $registro_puesto['dia6_hasta']; ?></td>
																					<td class="text-center"><?php echo $registro_puesto['dia7_desde']; ?><br>-<br><?php echo $registro_puesto['dia7_hasta']; ?></td>
																				<?php
																				}
																			?>
																		</tr>
																	</tbody>
																</table>

															</div>
														</div>
														<?php
													}
												?>
												<div class="form-group">
													<label class="control-label col-md-3">Clave I/R</label>
													<div class="col-md-9">
														<input type="text" name="clave_ir" id="clave_ir" class="form-control" value="<?php echo (isset($integrante)) ? $integrante->clave_ir : ''; ?>">
													</div>
												</div>
												<div class="form-group">
													<label class="control-label col-md-3">Turnos <?php if($empresa->tipo_empresa != 1){ ?><span class="required">*</span><?php } ?></label>
													<div class="col-md-9">
														<?php 
															$sql = "SELECT turno_id, concat('Turno ', descripcion) as descripcion FROM nomturnos";

															$res = $db->query($sql);
														?>
														<select name="turno_id" id="turno_id" class="form-control select2">
															<?php
																//if($operacion=='agregar')
																echo "<option value=''>Seleccione un turno</option>";

																while($fila = $res->fetch_assoc())
																{
																	if(isset($integrante) && $integrante->turno_id==$fila['turno_id'])
																		echo "<option value='".$fila['turno_id']."' selected>".$fila['descripcion']."</option>";
																	else
																		echo "<option value='".$fila['turno_id']."'>".$fila['descripcion']."</option>";
																}
															?>
														</select>
													</div>
												</div>
												<?php

													$display_acceso   = (isset($usuario->acceso_sueldo) && $usuario->acceso_sueldo==1) ? 'block' : 'none';
													$type_acceso      = (isset($usuario->acceso_sueldo) && $usuario->acceso_sueldo==1) ? 'text'  : 'hidden';
													$suesal_propuesto = (isset($usuario->acceso_sueldo) && $usuario->acceso_sueldo==1) ? '' : number_format(0, '2', '.', '');
												?>
																				
												<div class="form-group">
													<label class="control-label col-md-3">Hora Base Trabajada</label>
													<div class="col-md-9">
														<input type="text" name="hora_base" id="hora_base" class="form-control" value="<?php echo (isset($integrante)) ? number_format($integrante->hora_base, '0') : ''; ?>">
													</div>
												</div>


												<div class="form-group">
													<label class="control-label col-md-3">Observaciones </label>
													<div class="col-md-9">
														<textarea name="observaciones" id="observaciones" class="form-control" rows="3" style="resize:vertical;"><?php echo (isset($integrante)) ? $integrante->observaciones : ''; ?></textarea>
													</div>
												</div>
				
											</div>
											<!--/span-->
											<div class="col-md-3">
												
												<?php
													if(isset($integrante) && file_exists($integrante->foto))
													{
														?>
															<img src="<?php echo (isset($integrante)) ? $integrante->foto : ''; ?>" alt="Foto" class="img-responsive">
														<?php
													}
													else
															echo '<img src="../paginas/fotos/silueta.gif" alt="Foto" class="img-responsive">';


													if($operacion=='editar')
													{
														?>
														<div class="form-group" style="margin-top: 10px">
															<div class="col-md-12" style="text-align: center;">
																<?php
																	$imagen_cedula = '../../includes/assets/img/profile/no_disponible.png';

																	if(isset($integrante->imagen_cedula) && file_exists($integrante->imagen_cedula))
																	{
																		$imagen_cedula = $integrante->imagen_cedula; // data-toggle="modal" 
																	}
																?>	
																<a href="<?php echo $imagen_cedula; ?>" class="btn blue btn-sm fancybox"><i class="fa fa-picture-o"></i> Foto C&eacute;dula</a>
																&nbsp;
																<a href="#" class="btn dark btn-sm btn-print"><i class="fa fa-print"></i> Imprimir</a>
															</div>
														</div>

														<div id="foto_cedula" class="modal fade" tabindex="-1" aria-hidden="true">
															<div class="modal-dialog" style="max-width: 450px"><!-- modal-sm -->
																<div class="modal-content">
																	<div class="modal-header">
																		<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
																		<h4 class="modal-title" style="font-size: 16px;">Foto C&eacute;dula</h4>
																	</div>
																	<div class="modal-body">
																		<?php
																			if(isset($integrante) && file_exists($integrante->imagen_cedula))
																			{
																				?>
																					<img src="<?php echo (isset($integrante)) ? $integrante->imagen_cedula : ''; ?>" alt="Foto Cédula" class="img-responsive">
																				<?php
																			}
																			else
																					echo "No disponible";
																		?>
																	</div>
																</div>
															</div>
														</div>
														<?php
													}
												?>

											</div>
											<!--/span-->
										</div>
									</div>
									<div class="form-actions fluid">
										<div class="col-md-offset-4 col-md-8">
											<button type="submit" class="btn blue" id="btn-guardar" name="btn-guardar">Guardar</button>
											<button type="button" class="btn default" 
											        onclick="javascript: document.location.href='<?php echo isset($_GET['back_listado_integrantes']) ? 'datos_integrantes/listado_integrantes_contraloria.php' : 'datos_integrantes/listado_integrantes_contraloria.php'; ?>'">Cancelar</button>
										</div>
									</div>
								<!-- END FORM-->
							</div>
							<div class="tab-pane" id="tab_5">
								
									<div class="form-body">

										<div class="form-group">
											<label class="col-md-3">Aprueba Solicitudes?</label>
											<div class="com-md-8">
												<div class="radio-list">
													<label class="radio-inline">
													<input type="radio" name="solicitud" id="solicitud1" value="1"> Si</label>
													<label class="radio-inline">
													<input type="radio" name="solicitud" id="solicitud2" value="2"> No</label>
													<label class="radio-inline">
													<span id="apruebasolicitud">
													</span></label>
												</div>
											</div>
											
										</div>
										<div class="form-group">
											<label class="col-md-3">Es personal externo?</label>
											<div class="com-md-8">
												<div class="radio-list">
													<label class="radio-inline">
													<input type="radio" name="personalexterno" id="personalexterno1" value="1"> Si</label>
													<label class="radio-inline">
													<input type="radio" name="personalexterno" id="personalexterno2" value="2"> No</label>
													<label class="radio-inline">
													<span id="institucion">
													</span></label>
												</div>

											</div>
											<div class="col-md-3">
											</div>
										</div>

										<div class="form-group">
											<label class="col-md-3">Fecha de Permanencia</label>
											<div class="col-md-8">
												<div class="input-group input-medium date date-picker" data-date-format="dd-mm-yyyy" data-date-viewmode="years">
	            									<input name="fecha_permanencia" id="fecha_permanencia" type="text" value="" class="form-control form-control-inline" size="16" readonly>
								                <span class="input-group-btn">
								                  <button class="btn default" type="button"><i class="fa fa-calendar"></i></button>
								                </span>
								              </div>
											</div>
										</div>

										<div class="form-group">	
											<label class="col-md-3">Teléfono de Contacto</label>
											<div class="com-md-8"><input type="text" size="16" name="telefono_celular" id="telefono_celular" class="form-control form-control-inline input-medium" placeholder="xxxx-xxxx"></div>
										</div>

										<div class="form-group">	
											<label class="col-md-3">Tipo de Sangre</label>
											<div class="com-md-8"><span id="tiposangre"></span></div>
										</div>

										<div class="form-group">
											<label class="col-md-3">Nivel Educativo</label>
											<div class="com-md-8"><span id="nivel_educativo"></span></div>
										</div>

										<div class="control-label form-group">
											<label class="col-md-3">Hijos</label>
											<div class="com-md-8"><input class="form-control form-control-inline input-medium" type="text" name="hijos" size="16" /></div>
										</div>

										<div class="control-label form-group">
											<label class="col-md-3">Contacto Emergencia</label>
											<div class="com-md-8"><input class="form-control form-control-inline input-medium" type="text" name="contacto_emergencia" size="16" /></div>
										</div>

										<div class="control-label form-group">
											<label class="col-md-3">Teléfono Contacto</label>
											<div class="com-md-8"><input class="form-control form-control-inline input-medium" type="text" name="telefono_contacto" size="16" /></div>
										</div>
										<div class="form-group">
											<label class="col-md-3">Enfermedades y Alergias</label>
											<div class="com-md-8"><input name="enfermedades_alergias" class="form-control form-control-inline input-medium" size="16"  /></div>
										</div>

										<div class="control-label form-group">
											<label class="col-md-3">Tiene Discapacidad?</label>
											<div class="com-md-8">
												<div class="radio-list">
													<label class="radio-inline">
													<input type="radio" name="discapacidad" id="discapacidad1" value="1"> Si</label>
													<label class="radio-inline">
													<input type="radio" name="discapacidad" id="discapacidad2" value="2"> No</label>

												</div>
											</div>
										</div>

										<div class="control-label form-group">
											<label class="col-md-3">Tiene Familiares con Discapacidad?</label>
											<div class="com-md-8">
												<div class="radio-list">
													<label class="radio-inline">
													<input type="radio" name="fam_discapacidad" id="fam_discapacidad1" value="1"> Si</label>
													<label class="radio-inline">
													<input type="radio" name="fam_discapacidad" id="fam_discapacidad2" value="2"> No</label>
													
												</div>
											</div>
										</div>



									</div>

							</div>
							<div class="tab-pane" id="tab_6">
								<div id="tab_estructura">
								<div id="laposicion">
								</div>
								<div id="lacuentacontable">
								</div>
								
								<div id="laplanilla">
								</div>
								<div id="titularinterino">
								</div>
								<div id="eldepartamento">
								</div>

								<div id="eltipoempleado">
								</div>
								
								<div id="elcargo">
								</div>

								<div id="lafuncion">
								</div>

								<div id="elsalario">
								</div>

								<div id="elgasto">
								</div>

								<div id="lafecha">
								</div>
								</div>
																

							</div>
							<div class="tab-pane" id="tab_7">
								<div class="form-body">
									<div class="page-container">
									  <div class="page-content-wrapper">
									    <div class="page-content">
									      <div class="row">
									        <div class="col-md-12">
									           <!-- BEGIN EXAMPLE TABLE PORTLET-->
									         <!--Descomentar si es necesario <div class="portlet box blue">
									            <div class="portlet-title">
									              <div class="caption">
									                Tiempos del Empleado
									              </div> 
									              <div class="actions">
									                <a class="btn btn-sm blue"  href="javascript:enviar(1,0)">
									                  <i class="fa fa-plus"></i>
									                      Agregar
									                  </a>
									              </div> -->
									            </div>
									             <div class="portlet-body">
									                <table class="table table-striped table-bordered table-hover" id="table_datatable">
									                  <thead>
									                    <tr>
									                      <th>Tiempo Disponible</th>
									                      <th>Tiempo</th>
									                      <th>Dias</th>
									                      <th>Horas</th>
									                      <th>Minutos</th>
									                    </tr>
									                  </thead>
									                  <tbody>
									                      <?php
									                          while ($fila = fetch_array($result))
									                          { 
									                          ?>
									                            <tr>
									                              <td><?php echo $fila['cedula_beneficiario']; ?></td>
									                              <td><?php echo $fila['apellido'].", ".$fila['nombre']; ?></td>
									                              <td>
									                                  <?php 
									                                    $consulta="select * from nomparentescos where codorg='".$fila['codpar']."'";
									                                    $resultado_parentesco=sql_ejecutar($consulta);
									                                    $fila_parentesco=fetch_array($resultado_parentesco);
									                                    echo $fila_parentesco['descrip']; 
									                                  ?>
									                              </td>
									                              <td>
									                                <div align="center">
									                                    <font size="2" face="Arial, Helvetica, sans-serif">
									                                        <a href="javascript:enviar(<?php echo(2); ?>,<?php echo $fila['correl']; ?>);"><img src="img_sis/ico_edit.gif" alt="Modificar el Registro Actual" width="16" height="16" border="0" align="absmiddle">
									                                        </a>
									                                    </font>
									                                </div>
									                              </td>
									                              <td>
									                                <div align="center"><font size="2" face="Arial, Helvetica, sans-serif"><a href="javascript:enviar(<?php echo(3); ?>,<?php echo($fila['correl']); ?>);"><img src="../imagenes/delete.gif" alt="Eliminar el Registro Actual" width="16" height="16" border="0" align="absmiddle" ></a></font></div>
									                              </td>
									                            </tr>
									                            <?php
									                            }
									                            ?>
									                  </tbody>
									                </table>
									              </table>
									          </div>
									        <!--</div> -->
									      </div>
									    </div>
									  </div>
									</div>

								</div>
							</div>
							
																</form>
						</div>
							<?php
							if($operacion=='editar')
							{
								?>
									<script type="text/javascript">
									  function resizeIframe(obj) {
										  	var height = obj.contentWindow.document.body.scrollHeight;

										  	if(height < 1100){ height = 1100; }

										    obj.style.height = height + 'px';
									  }
									</script>
		 							<div class="tab-pane"  id="tab_1"><iframe id="iframe_tab_1" src="" frameborder="0" scrolling="0" onload="resizeIframe(this);"></iframe></div>
									<div class="tab-pane " id="tab_2"><iframe id="iframe_tab_2" src="" frameborder="0" scrolling="0" onload="resizeIframe(this);"></iframe></div>
									<div class="tab-pane " id="tab_3"><iframe id="iframe_tab_3" src="" frameborder="0" scrolling="0" onload="resizeIframe(this);"></iframe></div>
									<div class="tab-pane"  id="tab_4"><iframe id="iframe_tab_4" src="" frameborder="0" scrolling="0" onload="resizeIframe(this);"></iframe></div>
								<?php
							}
							?>
						</div>
					</div>
				</div>
			</div>
			<!-- END PAGE CONTENT-->
		</div>
	</div>
</div>
<?php include("../footer4.php"); ?>
<script src="../../includes/assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
<script src="../../includes/assets/plugins/jquery-validation/dist/jquery.validate.min.js"></script>
<script src="../../includes/assets/plugins/jquery-validation/localization/messages_es.js"></script>
<script src="../../includes/assets/plugins/fancybox/source/jquery.fancybox.pack.js"></script>
<script src="../../includes/js/scripts/momentjs/moment-with-locales.js"></script>
<script src="../../includes/js/scripts/momentjs/readable-range-es.js"></script>
<script>
$(document).ready(function(){
	//Otros Datos


	$("#apruebasolicitud").hide();
	$("#institucion").hide();
	//$("#nomposicion_id").change(function(){buscarPosicionDisponible();mostrarCargo(); });
	$("#solicitud2").on('click',function(){
		$("#apruebasolicitud").hide();
	});
	$("#personalesterno2").on('click',function(){
		$("#institucion").hide();
	});
	$("#solicitud1").change(function(){
		$.get("datos_integrantes/ajax/listarUsuarioSolicitud.php",function(result){
			$("#apruebasolicitud").empty();
			$("#apruebasolicitud").show();
			$("#apruebasolicitud").append(result);
		});
	});
	$("#personalesterno1").change(function(){
		$.get("datos_integrantes/ajax/listarPersonalExterno.php",function(result){
			$("#institucion").empty();
			$("#institucion").show();
			$("#institucion").append(result);
		});
	});
	cargar_estructura();
	mostrarPosicion();
	mostrarCuentaContable();
	mostrarPlanilla();
	mostrarDepartamento();
	mostrarTipoEmpleado();
	//mostrarCargo();
	mostrarFuncion();
	mostrarSalario();
	mostrarGR();
	mostrarFechaInicio();
	tipodesangre();


	niveleducativo();
	/*profesiones();
	nacionalidad();
	condicion();*/
	$("#nombres").keyup(function(){
		nombre   = $("#nombres").val();
		apellido = $("#apellidos").val();
		usuario  = nombre+"."+apellido;
		$("#usuario").val(usuario);
	});

	$("#apellidos").keyup(function(){
		nombre   = $("#nombres").val();
		apellido = $("#apellidos").val();
		usuario  = nombre+"."+apellido;
		$("#usuario").val(usuario);
	});
	$("#suesal").keyup(function(){
		nomposicion_id=$("#nomposicion_id").val();
		$.get("datos_integrantes/ajax/validarSalario.php",{nomposicion_id:nomposicion_id},function(resultado){
			if (resultado) {
				suesal=$("#suesal").val();

				if (suesal>resultado) {$("#suesal").val(0);
				alert("Salario no puede ser mayor a "+resultado);
				}
				

			}
		});
	});
	$("#nomposicion_id").change(function(){
		buscarPosicionDisponible();
		mostrarSalario();
		buscarCargo();
		mostrarPosicion();
		/*mostrarAntiguedad();
		mostrarZonasApartadas();
		mostrarJefaturas();
		mostrarEspecialidad();
		mostrarOtros();
		mostrarGastosRepresentacion();*/
	});
	/*$("#select_cargo").change(function(){
		buscarCargoDisponible();
		mostrarCargo();

	});*/
	/*$("#btn-guardar2").on('click',function(){
		solicitud           =$("#solicitud1").val();
		personalexterno     =$("#personalexterno1").val();
		fecha_permanencia   =$("#fecha_permanencia").val();
		telefono_celular    =$("#telefono_celular").val();
		tiposangre          =$("select#tipo_sangre").val();
		nivel_educativo     =$("select#nivel_educativo").val();
		discapacidad        =$("#discapacidad").val();
		hijos               =$("#hijos").val();
		contacto_emergencia =$("#contacto_emergencia").val();
		telefono_contacto   =$("#telefono_contacto").val();
		fam_discapacidad    =$("#fam_discapacidad").val();
		/*alert(solicitud+" "+personalexterno+" "+fecha_permanencia+" "+telefono_celular+" "+tiposangre+" "+nivel_educativo+"< "+discapacidad+" "+hijos+" "+contacto_emergencia+" "+telefono_contacto+" "+fam_discapacidad);
		tipoempleado_estructura+" "+cargo_estructura+" "+titular+" "+interino+" "+gr_estructura);
		$.get("datos_integrantes/ajax/insertar_otrosdatos.php",function(result){

		});
	});
	$("#form-integrantes").submit(function(){
		data= $(this).serialize();
		alert(data);
	});	*/

});
	function cargar_estructura()
	{
		$.get("datos_integrantes/ajax/estructura.php",function(res){
			$("#tab_estructura").empty();
			$("#tab_estructura").append(res);

			/*$("#btn-guardar3").on('click',function(){
			posicion_estructura     =$("#posicion_estructura").val();
			cargo_estructura        =$("#cargo_estructura").val();
			departamento_estructura =$("#departamento_estructura").val();
			funcion_estructura      =$("#funcion_estructura").val();
			gr_estructura           =$("#gr_estructura").val();
			usuario_estructura      =$("#usuario_estructura").val();
			planilla_estructura     =$("#planilla_estructura").val();
			salario_estructura      =$("#salario_estructura").val();
			tipoempleado_estructura =$("#tipoempleado_estructura").val();
			cargo_estructura        =$("#cargo_estructura").val();
			titular                 =$("#titular").val();
			interino                =$("#interino").val();
			gr_estructura           =$("#gr_estructura").val();
			//alert(posicion_estructura+" "+cargo_estructura+" "+departamento_estructura+" "+funcion_estructura+" "+gr_estructura+" "+usuario_estructura+"< "+planilla_estructura+" "+salario_estructura+" "+tipoempleado_estructura+" "+cargo_estructura+" "+titular+" "+interino+" "+gr_estructura);
			$.get("datos_integrantes/ajax/insertar_estructura.php",{posicion_estructura:posicion_estructura,cargo_estructura:cargo_estructura,departamento_estructura:departamento_estructura,funcion_estructura:funcion_estructura,gr_estructura:gr_estructura,usuario_estructura:usuario_estructura,planilla_estructura:planilla_estructura,salario_estructura:salario_estructura,tipoempleado_estructura:tipoempleado_estructura,cargo_estructura:cargo_estructura,titular:titular,interino:interino,gr_estructura:gr_estructura},function(result){
				alert(result);

			});
		});	*/

		});
	}
	function mostrarPosicion()
	{
		ficha = $("#ficha").val();
		nomposicion_id = $("#nomposicion_id").val();
		$.get("datos_integrantes/ajax/mostraPosicion.php",{ficha:ficha,nomposicion_id:nomposicion_id},function(result){
			$("#laposicion").empty();
			$("#laposicion").append(result);
		});
	}
	function mostrarCuentaContable()
	{
		ficha = $("#ficha").val();
		nomposicion_id = $("#nomposicion_id").val();
		$.get("datos_integrantes/ajax/mostraCuentaContable.php",{ficha:ficha,nomposicion_id:nomposicion_id},function(result){
			$("#lacuentacontable").empty();
			$("#lacuentacontable").append(result);
		});
	}
	function mostrarPlanilla()
	{
		ficha = $("#ficha").val();
		nomposicion_id = $("#nomposicion_id").val();
		$.get("datos_integrantes/ajax/mostrarPlanilla.php",{ficha:ficha,nomposicion_id:nomposicion_id},function(result){
			$("#laplanilla").empty();
			$("#laplanilla").append(result);
		});
	}
	function mostrarDepartamento(){
		$.get("datos_integrantes/ajax/mostrarDepartamento.php",function(res){
			$("#eldepartamento").empty();
			$("#eldepartamento").append(res);
		});
	}
	function mostrarTipoEmpleado(){
		$.get("datos_integrantes/ajax/mostrarTipoEmpleado.php",function(res){
			$("#eltipoempleado").empty();
			$("#eltipoempleado").append(res);
		});
	}
	function buscarCargo(){
		ficha = $("#ficha").val();
		nomposicion_id = $("#nomposicion_id").val();
		$.get("datos_integrantes/ajax/buscarCargo.php",{nomposicion_id:nomposicion_id},function(res){
			$("#codcargo").empty();
			$("#codcargo").append(res);
		});
	}
	function mostrarCargo(){
		ficha = $("#ficha").val();
		nomposicion_id = $("#nomposicion_id").val();
		$.get("datos_integrantes/ajax/mostrarCargo.php",{nomposicion_id:nomposicion_id},function(res){
			$("#codcargo").empty();
			$("#codcargo").append(res);
		});
		$.get("datos_integrantes/ajax/buscarSalario.php",{nomposicion_id:nomposicion_id},function(resultado){
			if(resultado)
			{	
				$("#suesal").empty();
				$("#suesal").val(resultado);
			}
		});
	}
	function mostrarFuncion(){
		ficha = $("#ficha").val();
		nomposicion_id = $("#nomposicion_id").val();
		$.get("datos_integrantes/ajax/mostrarFuncion.php",function(res){
			$("#lafuncion").empty();
			$("#lafuncion").append(res);
		});

	}
	function mostrarSalario(){
		ficha = $("#ficha").val();
		nomposicion_id = $("#nomposicion_id").val();

		$.get("datos_integrantes/ajax/mostrarSalario.php",{ficha:ficha,nomposicion_id:nomposicion_id},function(res){
			$("#elsalario").empty();
			$("#elsalario").append(res);
		});
		$.get("datos_integrantes/ajax/mostrarAntiguedad.php",{nomposicion_id:nomposicion_id},function(res){
			$("#antiguedad").empty();
			$("#antiguedad").val(res);
		});
		$.get("datos_integrantes/ajax/mostrarZonasApartadas.php",{nomposicion_id:nomposicion_id},function(res){
			$("#zonas_apartadas").empty();
			$("#zonas_apartadas").val(res);
		});
		$.get("datos_integrantes/ajax/mostrarJefaturas.php",{nomposicion_id:nomposicion_id},function(res){
			$("#jefaturas").empty();
			$("#jefaturas").val(res);
		});
		$.get("datos_integrantes/ajax/mostrarEspecialidad.php",{nomposicion_id:nomposicion_id},function(res){
			$("#especialidad").empty();
			$("#especialidad").val(res);
		});
		$.get("datos_integrantes/ajax/mostrarOtros.php",{nomposicion_id:nomposicion_id},function(res){
			$("#otros").empty();
			$("#otros").val(res);
		});
		$.get("datos_integrantes/ajax/mostrarGastosRepresentacion.php",{nomposicion_id:nomposicion_id},function(res){
			$("#gastos_repre").empty();
			$("#gastos_repre").val(res);
		});		
	}



	function mostrarGR(){
		ficha = $("#ficha").val();
		nomposicion_id = $("#nomposicion_id").val();

		$.get("datos_integrantes/ajax/mostrarGR.php",{ficha:ficha,nomposicion_id:nomposicion_id},function(res){
			$("#elgasto").empty();
			$("#elgasto").append(res);
		});

	}
	function mostrarFechaInicio(){
		ficha = $("#ficha").val();
		nomposicion_id = $("#nomposicion_id").val();

		$.get("datos_integrantes/ajax/mostrarFechaInicio.php",{ficha:ficha,nomposicion_id:nomposicion_id},function(res){
			$("#lafecha").empty();
			$("#lafecha").append(res);
		});

	}
	function mostrarTitularInterino()
	{
		ficha = $("#ficha").val();

		$.get("datos_integrantes/ajax/mostrarTitularInterino.php",{ficha:ficha},function(res){
			$("#titularinterino").empty();
			$("#titularinterino").append(res);
		});
	}

	function buscarPosicionDisponible(){
		nomposicion_id = $("#nomposicion_id").val();
		//alert(cod_cargo);
		$.get("datos_integrantes/ajax/buscarPosicionDisponible.php",{nomposicion_id:nomposicion_id},function(resultado){
			if(resultado)
			{	
				$("#posicion_disponible").empty();
				$("#posicion_disponible").append(resultado);
			}
		});
	}
	function buscarCargoDisponible(){
		cod_cargo=$("#codcargo").val();
		//alert(cod_cargo);
		$.get("datos_integrantes/ajax/buscarCargoDisponible.php",{cod_cargo:cod_cargo},function(resultado){
			if(resultado)
			{	
				$("#posicion_disponible").empty();
				$("#posicion_disponible").append(resultado);
			}
		});
	}
		

	function tipodesangre(){
		$.get("datos_integrantes/ajax/listarTipoSangre.php",function(resultado){
			if(resultado)
			{	
				$("#tiposangre").empty();
				$("#tiposangre").append(resultado);
			}
		});

	}
	function niveleducativo()
	{
		$.get("datos_integrantes/ajax/listarNivelEducativo.php",function(resultado){
			if(resultado)
			{	
				$("#nivel_educativo").empty();
				$("#nivel_educativo").append(resultado);
			}
		});

	}
	function usuarioworkflow(){
		nombre = $("#nombres").val();
		apellido = $("#apellidos").val();
		return nombre+"."+apellido;
	}


</script>
<script src="lib/funciones_ag_integrantes.js?<?php echo time(); ?>"></script>
<script>
var FICHA_ACTUAL          = '<?php echo $ficha_actual; ?>';
var CEDULA_ACTUAL         = '<?php echo (isset($integrante->cedula)) ? $integrante->cedula : ''; ?>';
var OPERACION             = '<?php echo $operacion; ?>';
var TIPO_EMPRESA          = '<?php echo $empresa->tipo_empresa; ?>';
var NIVEL1                = '<?php echo $empresa->nivel1; ?>';
var NIVEL2                = '<?php echo $empresa->nivel2; ?>';
var NIVEL3                = '<?php echo $empresa->nivel3; ?>';
var NIVEL4                = '<?php echo $empresa->nivel4; ?>';
var NIVEL5                = '<?php echo $empresa->nivel5; ?>';
var NIVEL6                = '<?php echo $empresa->nivel6; ?>';
var NIVEL7                = '<?php echo $empresa->nivel7; ?>';
var USUARIO_ACCESO_SUELDO = '<?php echo $usuario->acceso_sueldo; ?>';
var NUMERO_POSICIONES     = parseInt('<?php echo $posiciones->cantidad; ?>');
var LONGITUD_POSICION     = '<?php echo ($posiciones->longitud >= 3) ? 3 : $posiciones->longitud; ?>';
</script>
<script src="lib/validaciones_ag_integrantes.js?<?php echo time(); ?>"></script>
<script>
$('a.fancybox').fancybox();

$(".btn-print").click(function(){
	document.location.href = '../fpdf/datos_personal.php?cedula=' + CEDULA_ACTUAL + '&ficha=' + FICHA_ACTUAL;
});	
$('.date-picker').datepicker({
        orientation: "left",
        language: 'es',
    autoclose: true
    }); 
</script>
</body>
</html>