<?php
// require_once("../lib/common.php");
require_once('../lib/database.php');
$db = new Database($_SESSION['bd']);

$ficha_actual  = (isset($_GET['ficha'])) ? $_GET['ficha'] : '';
$operacion     = (isset($_GET['edit']))  ? 'editar' : 'agregar';
$sql_cant_empresa = "SELECT cant_empleados FROM nomempresa";
$res_cant_empresa = $db->query($sql_cant_empresa);
$cant_empresa = $res_cant_empresa->fetch_object();
$sql_cant_empleados = "SELECT count(*) cant_empleados  FROM nompersonal";
$res_cant_emmpleados = $db->query($sql_cant_empleados);
$cant_empleados = $res_cant_emmpleados->fetch_object();

if(isset($_POST['btn-guardar']) || isset($_POST['btn-guardar2']))
{
	require "ag_integrantes_guardar.php";
}

//PARAMETROS WORKFLOW - CONSULTA
$sql_workflow        = "SELECT workflow,db_host,db_name,db_user,db_pass FROM param_ws";
$res_workflow        = $db->query($sql_workflow);
$row_workflow        = $res_workflow->fetch_array();

$sw=1;

$sql = "SELECT e.nivel1, e.nomniv1, e.nivel2, e.nomniv2, e.nivel3, e.nomniv3, e.nivel4, e.nomniv4, e.nivel5, e.nomniv5,
		       e.nivel6, e.nomniv6, e.nivel7, e.nomniv7, e.tipo_empresa, e.pais
		FROM nomempresa e";
$res = $db->query($sql);
$empresa = $res->fetch_object();
$pais=$empresa->pais; 

$sql = "SELECT *
        FROM pais
        WHERE id='".$pais."'";
$res = $db->query($sql);
$pais = $res->fetch_object();
$iso_pais=$pais->iso;
$nombre_pais=$pais->nombre;
$moneda_simbolo=$pais->moneda_simbolo; 
$moneda_nombre=$pais->moneda_nombre; 
$gentilicio=$pais->gentilicio;
$identificacion_tributaria=$pais->identificacion_tributaria; 
$identificacion_personal=$pais->identificacion_personal; 

$sql = "SELECT acceso_sueldo FROM ".SELECTRA_CONF_PYME.".nomusuarios WHERE login_usuario='".$_SESSION['usuario']."'";
$res = $db->query($sql);
$usuario = $res->fetch_object();

$sql = "SELECT COUNT(*) as cantidad, MIN(LENGTH(nomposicion_id)) as longitud FROM nomposicion";
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

		$fecha_egreso = $integrante->fecharetiro;

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
                
                if(!empty($integrante->fin_probatorio) && $integrante->fin_probatorio!='0000-00-00')
		{
			$fin_probatorio =  DateTime::createFromFormat('Y-m-d', $integrante->fin_probatorio);
			$fin_probatorio = ($fin_probatorio !== false) ? $fin_probatorio->format('d-m-Y') : '';
		}
	}
}
else
{
    $sql = "SELECT conficha FROM nomempresa";
    $res = $db->query($sql);

    $fila = $res->fetch_assoc();
    $ficha_actual = $fila['conficha'] + 1;
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
.page-content-wrapper .page-content {
    padding-top: 5px;
    

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
span.marca_reloj{
	display: inline-block;
	width: 50px; 
}
iframe {
	margin: 0px; 
	width: 100% !important;
}
</style>
<div class="page-container">
         
                     <?php
                    if($operacion=='editar')
                    {
                    ?>   
                            <br>
                        <div class="row" style="background-color: 	#FFFF66;"> 
                            <div class="col-md-8" style="text-align: left;" >
                                
                                <div class="caption" style="font-size: 16px; font-weight: bold; color: black;">
                                    &nbsp;&nbsp;&nbsp;&nbsp;<strong><?php echo $integrante->cedula." / ".$integrante->nombres." ".$integrante->nombres2." ".$integrante->apellidos." ".$integrante->apellido_materno; ?></strong>
                                </div>
                            </div>
                            <div class="col-md-4" style="text-align: right;" >
                                
                                <button type="button" class="btn blue" onclick="javascript: document.location.href='<?php echo 'maestro_personal.php'; ?>'">Regresar</button>
<!--                            </div>
                            <div class="col-md-2" style="text-align: right;" >-->
                                
                                <button type="button" class="btn green" onclick="javascript:recargar();">Recargar</button>
                            </div>
                        </div>
                    <?php
                    }
                    ?>   
	<input type="hidden" id="workflow" name="workflow" values ="<?= $row_workflow["workflow"]; ?>" />

	<div class="page-content-wrapper">
		<div class="page-content">
			<!-- BEGIN PAGE CONTENT-->
			<div class="row">
				<div class="col-md-12">
					<div class="tabbable tabbable-custom tabbable-reversed"> <!-- boxless tabbable-reversed  -->
						<ul class="nav nav-tabs">
							<li class="active"><a href="#tab_0" id="tab-personal" data-toggle="tab">Personal</a></li>
                                                        
							<?php
								if($operacion=='editar')
								{
							?>
								<?php if($_SESSION['ver_nomina_electronica'] == 1): ?>
									<li><a href="#tab_7" id="tab-nomina-electronica" data-toggle="tab">Nomina Electronica</a></li>
								<?php endif; ?>								
								<?php if($_SESSION['ver_calendario'] == 1): ?>
									<li><a href="#tab_1" id="tab-calendario" data-toggle="tab">Calendario</a></li>
								<?php endif; ?>								
								<?php if($_SESSION['ver_carga_familiar'] == 1): ?>
									<li><a href="#tab_2" id="tab-cargas-familiares"   data-toggle="tab">Carga Familiar</a></li>
								<?php endif; ?>
								<?php if($_SESSION['ver_campos_adicionales'] == 1): ?>
									<li><a href="#tab_3" id="tab-campos-adicionales"  data-toggle="tab">Campos Adicionales</a></li>
								<?php endif; ?>
								<?php if($_SESSION['ver_exp'] == 1): ?>
									<li><a href="#tab_4" id="tab-expediente" data-toggle="tab">Expediente</a></li>
								<?php endif; ?>
								<?php if($_SESSION['ver_tiempos'] == 1): ?>
									<li><a href="#tab_5" id="tab-tiempos" data-toggle="tab"><i class="fa fa-clock-o"></i>Tiempos</a></li>
								<?php endif; ?>
								<?php if($_SESSION['ver_salarios_acumulados'] == 1): ?>
									<li><a href="#tab_6" id="tab-salarios-acumulados" data-toggle="tab"><i class="fa fa-clock-o"></i>Salarios Acumulados</a></li>
								<?php endif; ?>
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

															if($operacion=='agregar')
																$sql .= " AND (codtip = '".$_SESSION['codigo_nomina']."') ";

															$res = $db->query($sql);
														?>
														<select name="tipnom" id="tipnom" class="form-control select2">
															<?php
																if($res->num_rows==0)
																	echo "<option value=''>Seleccione una planilla</option>";

																while($fila = $res->fetch_assoc())
																{
																	if(isset($integrante) && $integrante->tipnom==$fila['codtip'])
																		echo "<option value='".$fila['codtip']."' selected>".$fila['descrip']."</option>";
																	else
																		echo "<option value='".$fila['codtip']."'>".$fila['descrip']."</option>";
																}
															?>
														</select>
													</div>
												</div>
												<div class="form-group">
													<label class="control-label col-md-3">N° Colaborador</label>
													<div class="col-md-9">
														<input type="text" name="ficha" id="ficha" class="form-control" value="<?php echo $ficha_actual; ?>" > 
													</div>
													<input type="hidden" name="personal_id" id="personal_id" value="<?php echo (isset($integrante) ? $integrante->personal_id : '0'); ?>">
													<input type="hidden" id="usr_pass_ant" name="usr_pass_ant" value ="<?= $integrante->usr_password; ?>" />
												</div>
												<div class="form-group">
													<label class="control-label col-md-3">Foto</label>
													<div class="col-md-9">
														<input type="file" id="foto" name="foto">
													</div>
												</div>
												<div class="form-group">
													<label class="control-label col-md-3">Primer Apellido <span class="required">*</span></label>
													<div class="col-md-3">
														<input type="text" name="apellidos" id="apellidos" class="form-control" value="<?php  echo (isset($integrante)) ? $integrante->apellidos : ''; ?>">
													</div>
                                                                                                        <label class="control-label col-md-3">Segundo Apellido </label>
													<div class="col-md-3">
														<input type="text" name="apellidos2" id="apellidos2" class="form-control" value="<?php  echo (isset($integrante)) ? $integrante->apellido_materno : ''; ?>">
													</div>
												</div>
												<div class="form-group">
													<label class="control-label col-md-3">Primer Nombre <span class="required">*</span></label>
													<div class="col-md-3">
														<input type="text" name="nombres" id="nombres" class="form-control" value="<?php  echo (isset($integrante)) ? $integrante->nombres : ''; ?>">
													</div>
                                                                                                        <label class="control-label col-md-3">Segundo  Nombre </label>
													<div class="col-md-3">
														<input type="text" name="nombres2" id="nombres2" class="form-control" value="<?php  echo (isset($integrante)) ? $integrante->nombres2 : ''; ?>">
													</div>
												</div>
                                                                                                <div class="form-group">
													<label class="control-label col-md-3">Apellido Casada </label>
													<div class="col-md-6">
														<input type="text" name="apellido_casada" id="apellido_casada" class="form-control" value="<?php  echo (isset($integrante)) ? $integrante->apellido_casada : ''; ?>">
													</div>
                                                                                                        
												</div>
												<div class="form-group">
													<label class="control-label col-md-3"><?php echo $identificacion_personal; ?><span class="required">*</span></label>
													<div class="col-md-9">
														<input type="text" name="cedula" id="cedula" class="form-control" value="<?php  echo (isset($integrante)) ? $integrante->cedula : ''; ?>">
                                                                                                                <input type="hidden" name="cedula_actual" id="cedula_actual" value="<?php echo (isset($integrante) ? $integrante->cedula : ''); ?>">
													</div>
												</div>
												<div class="form-group">
													<label class="control-label col-md-3">Foto <?php echo $identificacion_personal; ?></label>
													<div class="col-md-9">
														<input type="file" id="imagen_cedula" name="imagen_cedula">
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
															<input type="radio" name="nacionalidad" id="nacionalidad1" value="1" <?php echo $nacionalidad1; ?>> <?php echo $gentilicio;?></label>
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
													<label class="control-label col-md-3">Tel&eacute;fonos <?php if($empresa->tipo_empresa != 1){ ?><span class="required">*</span><?php } ?></label>
													<div class="col-md-9">
														<input type="text" name="telefonos" id="telefonos" class="form-control" placeholder="xxxx-xxxx" value="<?php  echo (isset($integrante)) ? $integrante->telefonos : ''; ?>">	
													</div>
												</div>
												<div class="form-group">
													<label class="control-label col-md-3">E-mail Sugerido</label>
													<div class="col-md-9">
														<input type="text" name="email" id="email" class="form-control" value="<?php  echo (isset($integrante)) ? $integrante->email : ''; ?>">
													</div>
												</div>
												
                                                                                                <div class="form-group">
													<label class="control-label col-md-3">Situación <span class="required">*</span> <?php if($empresa->tipo_empresa != 1){ ?><?php } ?></label>
													<div class="col-md-9">
														 <?php 
                                                                                                                    $sql = "SELECT situacion FROM nomsituaciones";
                                                                                                                    $res = $db->query($sql);
                                                                                                                ?>
														<select name="estado" id="estado" class="form-control select2" >                                                                                                                        
															<?php
																if($operacion=='agregar')
																	echo "<option value=''>Seleccione Situación</option>";

																while($fila = $res->fetch_assoc())
																{
																	if(isset($integrante) && $integrante->estado===$fila['situacion'])
																		echo "<option value='".$fila['situacion']."' selected>".$fila['situacion']."</option>";
																	else
																		echo "<option value='".$fila['situacion']."'>".$fila['situacion']."</option>";
																}
															?>
														</select>
													</div>
												</div>
                                                                                                <?php if($iso_pais=='PA'){ ?>
												<div class="form-group">
													<label class="control-label col-md-3">D&iacute;gito Verificador</label>
													<div class="col-md-9">
														<input type="text" name="dv" id="dv" class="form-control" value="<?php  echo (isset($integrante)) ? $integrante->dv : ''; ?>">
													</div>
												</div>
												
                                                                                            
												<div class="form-group">
													<label class="control-label col-md-3">Siacap</label>
													<div class="col-md-9">
														<input type="text" name="siacap" id="siacap" class="form-control" value="<?php  echo (isset($integrante)) ? $integrante->siacap : ''; ?>">
													</div>
												</div>
                                                                                                <?php } ?>
                                                                                            
												<div class="form-group">
													<?php
														$data = array('Efectivo', 'Cheque', 'Cuenta Ahorro', 'Cuenta Corriente','Deposito','Tarjeta');
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
												<?php $display_forma_pago = (isset($integrante) && ($integrante->forcob!='Efectivo' && $integrante->forcob!='Cheque')) ? 'display: block' : 'display: none'; ?>
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
													<label class="control-label col-md-3">Seguro Social</label>
													<div class="col-md-9">
														<input type="text" name="seguro_social" id="seguro_social" class="form-control" value="<?php echo (isset($integrante)) ? $integrante->seguro_social : ''; ?>">
													</div>
												</div>
                                                                                            
                                                                                                <?php if($iso_pais=='PA'){ ?>
												<div class="form-group">
													<label class="control-label col-md-3">C&oacute;digo Seguro Social Sipe:</label>
													<div class="col-md-9">
														<input type="text" name="segurosocial_sipe" id="segurosocial_sipe" class="form-control" value="<?php echo (isset($integrante)) ? $integrante->segurosocial_sipe : ''; ?>">
													</div>
												</div>
                                                                                                <div class="form-group">
													<label class="control-label col-md-3">Clave I/R</label>
													<div class="col-md-9">
														<input type="text" name="clave_ir" id="clave_ir" class="form-control" value="<?php echo (isset($integrante)) ? $integrante->clave_ir : ''; ?>">
													</div>
												</div>
                                                                                                <?php } ?>
                                                                                            
                                                                                                <div class="form-group">
													<label class="control-label col-md-3">Fecha de Ingreso <?php if($empresa->tipo_empresa != 1){ ?><span class="required">*</span><?php } ?></label>
													<div class="col-md-9">
														<div class="input-group date date-picker" data-provide="datepicker">
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
												<?php if ($operacion == "editar"){

												?>
                                                                                                <div class="form-group">
													<label class="control-label col-md-3">Fecha de Egreso <?php if($empresa->tipo_empresa != 1){ ?><span class="required">*</span><?php } ?></label>
													<div class="col-md-9">
														<div class="input-group date date-picker" data-provide="datepicker">
															<input type="text" class="form-control" name="fecfin" id="fecfin" value="<?php echo (isset($fecha_egreso)) ? $fecha_egreso : ''; ?>">
															<span class="input-group-btn">
																<button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
															</span>
														</div>
													</div>
												</div>
												<?php } ?>
												<div class="form-group">
													<?php
														$tipemp1 = 'checked';
														$tipemp2 = $tipemp3 = $tipemp4 = '';
														//$disabled_periodo = 'disabled';
														$disabled_periodo = 'enabled';
														if(isset($integrante))
														{
															$tipemp1 = ($integrante->tipemp=='Fijo')  ? 'checked' : '';
															$tipemp2 = ($integrante->tipemp=='Contratado Transitorio')  ? 'checked' : '';
															$tipemp3 = ($integrante->tipemp=='Contratado Contingente')  ? 'checked' : '';
															$tipemp4 = ($integrante->tipemp=='Contratado Servicios')    ? 'checked' : '';

															$disabled_periodo = ($tipemp1=='checked') ? 'enabled' : '' ;
															//$disabled_periodo = ($tipemp1=='checked') ? 'disabled' : '' ;

														} 
													?>
													<label class="control-label col-md-3">Tipo de Contrato</label>
													<div class="col-md-9">
														<div class="radio-list">
															<label class="radio-inline">
															<input type="radio" name="tipemp" id="tipemp1" value="Fijo" <?php echo $tipemp1; ?>> <span class="tipo_contrato">Contrato Indefinido</span></label>
															<label class="radio-inline">
															<input type="radio" name="tipemp" id="tipemp2" value="Contratado Transitorio" <?php echo $tipemp2; ?>> Contrato Obra Determinada</label>
														</div>	
														<div class="radio-list">
															<label class="radio-inline">
															<input type="radio" name="tipemp" id="tipemp3" value="Contratado Contingente" <?php echo $tipemp3; ?>> <span class="tipo_contrato">Servicios Profesionales</span></label>
															<label class="radio-inline">
															<input type="radio" name="tipemp" id="tipemp4" value="Contratado Servicios" <?php echo $tipemp4; ?>> Contrato Definido </label>
														</div>
													</div>
												</div>
												<div class="form-group">
													<label class="control-label col-md-3"></label>
													<div class="col-md-9">
														<div class="row">
															<label class="control-label col-md-3" style="padding-left: 20px">Fecha Inicio (Contrato)</label>
															<div class="col-md-5" style="padding-right: 0px">
																<div class="input-group date date-picker" data-provide="datepicker">	
																	<input type="text" class="form-control" name="inicio_periodo" id="inicio_periodo" value="<?php echo (isset($inicio_periodo)) ? $inicio_periodo : ''; ?>" <?php echo $disabled_periodo; ?> >
																	<span class="input-group-btn">
																		<button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
																	</span>
																</div>
															</div>
														</div>
														<div class="row" style="margin-top: 10px">
															<label class="control-label col-md-3" style="padding-left: 20px">Fecha Fin (Contrato)</label>
															<div class="col-md-5" style="padding-right: 0px">
																<div class="input-group date date-picker" data-provide="datepicker">	
																	<input type="text" class="form-control" name="fin_periodo" id="fin_periodo" value="<?php echo (isset($fin_periodo)) ? $fin_periodo : ''; ?>" <?php echo $disabled_periodo; ?> >
																	<span class="input-group-btn">
																		<button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
																	</span>
																</div>
															</div>
														</div>	
                                                                                                                <div class="row" style="margin-top: 10px">
															<label class="control-label col-md-3" style="padding-left: 20px">Fecha Fin (Probatorio)</label>
															<div class="col-md-5" style="padding-right: 0px">
																<div class="input-group date date-picker" data-provide="datepicker">	
																	<input type="text" class="form-control" name="fin_probatorio" id="fin_probatorio" value="<?php echo (isset($fin_probatorio)) ? $fin_probatorio : ''; ?>" >
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
													<label class="control-label col-md-3">Turno <?php if($empresa->tipo_empresa != 1){ ?><span class="required">*</span><?php } ?></label>
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
													if($empresa->tipo_empresa==3)
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
													$display_acceso   = (isset($usuario->acceso_sueldo) && $usuario->acceso_sueldo==1) ? 'block' : 'none';
													$type_acceso      = (isset($usuario->acceso_sueldo) && $usuario->acceso_sueldo==1) ? 'text'  : 'hidden';
													$suesal_propuesto = (isset($usuario->acceso_sueldo) && $usuario->acceso_sueldo==1) ? '' : number_format(0, '2', '.', '');
												?>
                                                                                                <div class="form-group">
													<label class="control-label col-md-3">Cargo <span class="required">*</span></label>
													<div class="col-md-9">
														<?php 
															$sql = "SELECT cod_car, des_car FROM nomcargos";
															$res = $db->query($sql);
														?>
														<select name="codcargo" id="codcargo" class="form-control select2">
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
                                                                                                <div class="form-group">
													<label class="control-label col-md-3">Categor&iacute;a <?php if($empresa->tipo_empresa != 1){ ?><span class="required">*</span><?php } ?></label>
													<div class="col-md-9">
														<?php 
															$sql = "SELECT codorg, descrip FROM nomcategorias";
															$res = $db->query($sql);
														?>
														<select name="codcat" id="codcat" class="form-control select2">
															<?php
																//if($operacion=='agregar')
																echo "<option value=''>Seleccione una categor&iacute;a</option>";

																while($fila = $res->fetch_assoc())
																{
																	if(isset($integrante) && $integrante->codcat==$fila['codorg'])
																		echo "<option value='".$fila['codorg']."' selected>".$fila['descrip']."</option>";
																	else
																		echo "<option value='".$fila['codorg']."'>".$fila['descrip']."</option>";
																}
															?>
														</select>
													</div>
												</div>
                                                                                                <div class="form-group">
													<label class="control-label col-md-3">Locación <?php if($empresa->tipo_empresa != 1){ ?><?php } ?></label>
													<div class="col-md-9">
														<?php 
															$sql = "SELECT idProyecto, numProyecto, descripcionLarga FROM proyectos";
															$res = $db->query($sql);
														?>
														<select name="proyecto" id="proyecto" class="form-control select2">
															<?php
																//if($operacion=='agregar')
																echo "<option value=''>Seleccione Proyecto</option>";

																while($fila = $res->fetch_assoc())
																{
																	if(isset($integrante) && $integrante->proyecto==$fila['idProyecto'])
																		echo "<option value='".$fila['idProyecto']."' selected>".$fila['numProyecto']." - ".$fila['descripcionLarga']."</option>";
																	else
																		echo "<option value='".$fila['idProyecto']."'>".$fila['numProyecto']." - ".$fila['descripcionLarga']."</option>";
																}
															?>
														</select>
													</div>
												</div>
												<div class="form-group" style="display: <?php echo $display_acceso; ?>">
													<label class="control-label col-md-3">Salario <span class="required">*</span></label>
													<div class="col-md-9">
														<input type="<?php echo $type_acceso; ?>" name="suesal" id="suesal" class="form-control" value="<?php echo isset($integrante->suesal) ? number_format($integrante->suesal,'2', '.', '')  : $suesal_propuesto; ?>">
														<input type="hidden" name="max_sueldo" id="max_sueldo" value="<?php echo $max_sueldo; ?>" />
														<input type="hidden" name="sueldo_original" id="sueldo_original" value="<?php echo (isset($integrante) && $integrante->suesal) ? $integrante->suesal : 0; ?>">
													</div>
												</div>
												<div class="form-group">
													<label class="control-label col-md-3">Hora Base Trabajada</label>
													<div class="col-md-9">
														<input type="text" name="hora_base" id="hora_base" class="form-control" value="<?php echo (isset($integrante)) ? number_format($integrante->hora_base, '0') : ''; ?>">
													</div>
												</div>
												
												<div class="form-group">
													<label class="control-label col-md-3">Marca Reloj</label>
													<?php
															$marca_reloj = ($integrante->marca_reloj=='1')  ? 'checked' : '';
													?>
													<div class="col-md-9">
														<label  class="mt-checkbox mt-checkbox-outline"> 
															<input type="checkbox" name="marcar_reloj" id="marcar_reloj1"  <?php echo $marca_reloj; ?>>
														</label>

													</div>
												</div>
												<div class="form-group">
													<label class="control-label col-md-3">Tiene Discapacidad</label>
													<?php
															$incapacidad = ($integrante->incapacidad=='1')  ? 'checked' : '';
													?>
													<div class="col-md-9">
														<label  class="mt-checkbox mt-checkbox-outline"> 
															<input type="checkbox" name="incapacidad" id="incapacidad1"  <?php echo $incapacidad; ?>>
														</label>

													</div>
												</div>
												<div class="form-group">
													<label class="control-label col-md-3">Familiar con Discapacidad</label>
													<?php
															$familiar_incapacidad = ($integrante->familiar_incapacidad=='1')  ? 'checked' : '';
													?>
													<div class="col-md-9">
														<label  class="mt-checkbox mt-checkbox-outline"> 
															<input type="checkbox" name="familiar_incapacidad" id="familiar_incapacidad1"  <?php echo $familiar_incapacidad; ?>>
														</label>

													</div>
												</div>
												
												<?php
													for($i=1; $i<=7; $i++)
													{
														// $display_nivel1 = ($empresa->nivel1 != "1") ? 'display: none' : 'display: block';
														if($empresa->{"nivel".$i} == 1)
														{ 
														?>
														<div class="form-group">
															<label class="control-label col-md-3"><?php echo $empresa->{"nomniv".$i}; ?></label>
															<div class="col-md-9">
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
																<select name="codnivel<?php echo $i; ?>" id="codnivel<?php echo $i; ?>" class="form-control select2me">
																	<?php
																		if($operacion=='agregar' || $res->num_rows==0 || (isset($integrante->{"codnivel".$i}) && $integrante->{"codnivel".$i}==0))
																			echo "<option value=''>Seleccione ".$empresa->{"nomniv".$i}."</option>";

																		while($fila = $res->fetch_assoc())
																		{
																			if(isset($integrante) && $integrante->{"codnivel".$i}==$fila['codorg'])
																				echo "<option value='".$fila['codorg']."' selected>".$fila['descrip']."</option>";
																			else
																				echo "<option value='".$fila['codorg']."'>".$fila['descrip']."</option>";
																		}
																	?>
																</select>
															</div>
														</div>
														<?php
														}
													}
												?>
												<?php
												                    
													if($row_workflow["workflow"]==1)
													{
													?>
                                                                                                
												<div class="form-group">
													<label class="col-md-3 control-label">Usuario Workflow</label>
													<div class="col-md-9">
														<input type="text" name="usuario_workflow" id="usuario_workflow" class="form-control" value="<?php echo (isset($integrante)) ? $integrante->usuario_workflow : ''; ?>">
													</div>
												</div>                                 
												<div class="form-group">
													<label class="col-md-3 control-label">Contraseña Workflow</label>
													<div class="col-md-9">
														<input type="password" name="usr_password" id="usr_password" class="form-control" value="<?php echo (isset($integrante)) ? $integrante->usr_password : ''; ?>">
													</div>
												</div>
                                                                                                <div class="form-group">
                                                                                                    <label class="col-md-3 control-label">Departamento (Workflow)</label>
                                                                                                    <div class="col-md-9">
                                                                                                            <select id="departamento_estructura" name="departamento_estructura" class="form-control select2me">
                                                                                                                
                                                                                                            <?php
                                                                                                            $sql = "SELECT 	IdDepartamento,Descripcion FROM departamento ORDER BY Descripcion ASC";
                                                                                                            $res = $db->query($sql);
                                                                                                            ?>
                                                                                                            <option value="">Seleccione Departamento</option>
                                                                                                            <?php
                                                                                                            while($fila = $res->fetch_assoc())
                                                                                                            {
                                                                                                                $select="";
                                                                                                                                    if(isset($integrante) && $integrante->IdDepartamento==$fila['IdDepartamento'])
                                                                                                                                        $select="selected";
                                                                                                                                    else
                                                                                                                                        $select="";   
                                                                                                                ?>
                                                                                                                    <option value="<?php echo $fila['IdDepartamento'];?>" <?=$select;?>><?php echo $fila['Descripcion'];?>
                                                                                                                    </option>
                                                                                                            <?php }	?>
                                                                                                        </select>
                                                                                                    </div>
                                                                   
                                                                                                </div>
                                                                                                <div class="form-group">
                                                                                                        <?php
                                                                                                                $apruebasolicitud_no = 'checked';
                                                                                                                $apruebasolicitud_si = '';
                                                                                                                if(isset($integrante))
                                                                                                                {
                                                                                                                        $apruebasolicitud_no = ($integrante->jefe=='0') ? 'checked' : '';
                                                                                                                        $pruebasolicitud_si = ($integrante->jefe=='1')  ? 'checked' : '';	
                                                                                                                } 
                                                                                                        ?>	
                                                                                                        <input type="hidden" id="uid_user_aprueba" name="uid_user_aprueba"  value="<? echo (isset($integrante) && $integrante->uid_user_aprueba) ? $integrante->uid_user_aprueba : ''; ?>">
                                                                                                        <label class="col-md-3">Aprueba Solicitudes</label>
                                                                                                        <div class="col-md-3">
                                                                                                                <div class="radio-list">
                                                                                                                        <label class="radio-inline">
                                                                                                                        <input type="radio" name="aprueba_solicitud" id="apruebasolicitud_no" value="0" <?php echo $apruebasolicitud_no;?>>No</label> 
                                                                                                                        <label class="radio-inline"> 
                                                                                                                        <input type="radio" name="aprueba_solicitud" id="apruebasolicitud_si" value="1" <?php echo $pruebasolicitud_si;?>>Si</label>

                                                                                                                </div>
                                                                                                        </div>
                                                                                                        <div class="col-md-6">                                                                                                
                                                                                                            <div id="apruebasolicitud" >
                                                                                                            </div>

                                                                                                        </div>
                                                                                                </div> 
																											<?php } ?>
											<!--/span-->
                                                                                        </div>
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
											<?php 
											if(VALIDAR_EMPLEADOS == "SI" ){?>

												<?php if($cant_empleados->cant_empleados < $cant_empresa->cant_empleados ){?>
													<button type="submit" class="btn blue" id="btn-guardar" name="btn-guardar">Guardar</button>

												<?php } else {?>
													<span class="alert alert-info"> Usted ha alcanzado el límite para agregar empleados</span>
													<?php }?>

											<?php } else { ?>
												<button type="submit" class="btn blue" id="btn-guardar" name="btn-guardar">Guardar</button>
											<?php }?>
											<button type="button" class="btn default" 
											        onclick="javascript: document.location.href='<?php echo isset($_GET['back_listado_integrantes']) ? 'datos_integrantes/listado_integrantes_contraloria.php' : 'maestro_personal.php'; ?>'">Cancelar</button>
										</div>
									</div>
								</form>
								<!-- END FORM-->
							</div>
                                                        
                                                        <div class="tab-pane" id="tab_7">
								<!-- BEGIN FORM-->
								<form class="form-horizontal" id="form-integrantes-nomina-electronica" name="form-integrantes-nomina-electronica" method="post" enctype="multipart/form-data" role="form" style="margin-bottom: 5px;">
                                                                    <input type="hidden" name="personal_id" id="personal_id" value="<?php echo (isset($integrante) ? $integrante->personal_id : '0'); ?>">
                                                                    <input type="hidden" name="ficha" id="ficha" value="<?php echo (isset($integrante) ? $integrante->ficha : '0'); ?>">
                                                                    <input type="hidden" name="nomina_electronica" id="nomina_electronica" value="1">
									<div class="form-body">
										<div class="row">                                                                                        
                                                                                        
											<div class="col-md-9">	
                                                                                                
                                                                                                <div class="form-group">
													<label class="control-label col-md-3">Tipo Identificacion </label>
													<div class="col-md-9">
														 <?php 
                                                                                                                    $sql = "SELECT * FROM tipo_documento_trabajador";
                                                                                                                    $res = $db->query($sql);
                                                                                                                ?>
														<select name="tipo_identificacion" id="tipo_identificacion" class="form-control select2" >                                                                                                                        
															<?php
																if($operacion=='agregar')
																	echo "<option value=''>Seleccione</option>";

																while($fila = $res->fetch_assoc())
																{
																	if(isset($integrante) && $integrante->tipo_identificacion===$fila['id'])
																		echo "<option value='".$fila['id']."' selected>".$fila['codigo']." - ".$fila['descripcion']."</option>";
																	else
																		echo "<option value='".$fila['id']."'>".$fila['codigo']." - ".$fila['descripcion']."</option>";
																}
															?>
														</select>
													</div>
												</div>
                                                                                            
												<div class="form-group">
													<label class="control-label col-md-3">Tipo Trabajador </label>
													<div class="col-md-9">
														 <?php 
                                                                                                                    $sql = "SELECT * FROM tipo_trabajador";
                                                                                                                    $res = $db->query($sql);
                                                                                                                ?>
														<select name="tipo_trabajador" id="tipo_trabajador" class="form-control select2" >                                                                                                                        
															<?php
																if($operacion=='agregar')
																	echo "<option value=''>Seleccione</option>";

																while($fila = $res->fetch_assoc())
																{
																	if(isset($integrante) && $integrante->tipo_trabajador===$fila['id'])
																		echo "<option value='".$fila['id']."' selected>".$fila['codigo']." - ".$fila['descripcion']."</option>";
																	else
																		echo "<option value='".$fila['id']."'>".$fila['codigo']." - ".$fila['descripcion']."</option>";
																}
															?>
														</select>
													</div>
												</div>
                                                                                                
                                                                                                <div class="form-group">
													<label class="control-label col-md-3">Subtipo Trabajador </label>
													<div class="col-md-9">
														 <?php 
                                                                                                                    $sql = "SELECT * FROM tipo_subtipo_trabajador";
                                                                                                                    $res = $db->query($sql);
                                                                                                                ?>
														<select name="subtipo_trabajador" id="subtipo_trabajador" class="form-control select2" >                                                                                                                        
															<?php
																if($operacion=='agregar')
																	echo "<option value=''>Seleccione</option>";

																while($fila = $res->fetch_assoc())
																{
																	if(isset($integrante) && $integrante->subtipo_trabajador===$fila['id'])
																		echo "<option value='".$fila['id']."' selected>".$fila['codigo']." - ".$fila['descripcion']."</option>";
																	else
																		echo "<option value='".$fila['id']."'>".$fila['codigo']." - ".$fila['descripcion']."</option>";
																}
															?>
														</select>
													</div>
												</div>
                                                                                            
                                                                                                <div class="form-group">
													<label class="control-label col-md-3">Tipo Contrato </label>
													<div class="col-md-9">
														 <?php 
                                                                                                                    $sql = "SELECT * FROM tipo_contrato";
                                                                                                                    $res = $db->query($sql);
                                                                                                                ?>
														<select name="tipo_contrato" id="tipo_contrato" class="form-control select2" >                                                                                                                        
															<?php
																if($operacion=='agregar')
																	echo "<option value=''>Seleccione</option>";

																while($fila = $res->fetch_assoc())
																{
																	if(isset($integrante) && $integrante->tipo_contrato===$fila['id'])
																		echo "<option value='".$fila['id']."' selected>".$fila['codigo']." - ".$fila['descripcion']."</option>";
																	else
																		echo "<option value='".$fila['id']."'>".$fila['codigo']." - ".$fila['descripcion']."</option>";
																}
															?>
														</select>
													</div>
												</div>
                                                                                            
                                                                                                <div class="form-group">
													<label class="control-label col-md-3">Direccion Trabajo - Departamento / Estado </label>
													<div class="col-md-9">
														 <?php 
                                                                                                                    $sql = "SELECT * FROM provincia";
                                                                                                                    $res = $db->query($sql);
                                                                                                                ?>
														<select name="dir_trabajo_provincia" id="dir_trabajo_provincia" class="form-control select2" >                                                                                                                        
															<?php
																if($operacion=='agregar')
																	echo "<option value=''>Seleccione</option>";

																while($fila = $res->fetch_assoc())
																{
																	if(isset($integrante) && $integrante->dir_trabajo_provincia===$fila['id_provincia'])
																		echo "<option value='".$fila['id_provincia']."' selected>".$fila['codigo_iso']." - ".$fila['nombre']."</option>";
																	else
																		echo "<option value='".$fila['id_provincia']."'>".$fila['codigo_iso']." - ".$fila['nombre']."</option>";
																}
															?>
														</select>
													</div>
												</div>
                                                                                                
                                                                                                <div class="form-group">
													<label class="control-label col-md-3">Direccion Trabajo - Municipio / Ciudad </label>
													<div class="col-md-9">
														 <?php 
                                                                                                                    $sql = "SELECT * FROM distrito";
                                                                                                                    $res = $db->query($sql);
                                                                                                                ?>
														<select name="dir_trabajo_distrito" id="dir_trabajo_distrito" class="form-control select2" >                                                                                                                        
															<?php
																if($operacion=='agregar')
																	echo "<option value=''>Seleccione</option>";

																while($fila = $res->fetch_assoc())
																{
																	if(isset($integrante) && $integrante->dir_trabajo_distrito===$fila['id_distrito'])
																		echo "<option value='".$fila['id_distrito']."' selected>".$fila['codigo_iso']." - ".$fila['nombre']."</option>";
																	else
																		echo "<option value='".$fila['id_distrito']."'>".$fila['codigo_iso']." - ".$fila['nombre']."</option>";
																}
															?>
														</select>
													</div>
												</div>
                                                                                            
												<div class="form-group">
													<label class="control-label col-md-3">Dirección Trabajo </label>
													<div class="col-md-9">
														<textarea name="dir_trabajo_direccion" id="dir_trabajo_direccion" class="form-control" rows="3" style="resize:vertical;"><?php echo (isset($integrante)) ? $integrante->dir_trabajo_direccion : ''; ?></textarea>
													</div>
												</div>												
                                                                                                											
												
												<div class="form-group">
													<label class="control-label col-md-3">Alto Riesgo Pension</label>
													<?php
															$alto_riesgo_pension = ($integrante->alto_riesgo_pension=='1')  ? 'checked' : '';
													?>
													<div class="col-md-9">
														<label  class="mt-checkbox mt-checkbox-outline"> 
															<input type="checkbox" name="alto_riesgo_pension" id="alto_riesgo_pension"  <?php echo $alto_riesgo_pension; ?>>
														</label>

													</div>
												</div>
												
												<div class="form-group">
													<label class="control-label col-md-3">Salario Integral</label>
													<?php
															$salario_integral = ($integrante->salario_integral=='1')  ? 'checked' : '';
													?>
													<div class="col-md-9">
														<label  class="mt-checkbox mt-checkbox-outline"> 
															<input type="checkbox" name="salario_integral" id="salario_integral"  <?php echo $salario_integral; ?>>
														</label>

													</div>
												</div>
                                                                                                
                                                                                        </div>
											
											<!--/span-->
										</div>
									</div>
									<div class="form-actions fluid">
										<div class="col-md-offset-4 col-md-8">
											<?php 
											if(VALIDAR_EMPLEADOS == "SI" ){?>

												<?php if($cant_empleados->cant_empleados < $cant_empresa->cant_empleados ){?>
													<button type="submit" class="btn blue" id="btn-guardar2" name="btn-guardar2">Guardar</button>

												<?php } else {?>
													<span class="alert alert-info"> Usted ha alcanzado el límite para agregar empleados</span>
													<?php }?>

											<?php } else { ?>
												<button type="submit" class="btn blue" id="btn-guardar2" name="btn-guardar2">Guardar</button>
											<?php }?>
											<button type="button" class="btn default" 
											        onclick="javascript: document.location.href='<?php echo isset($_GET['back_listado_integrantes']) ? 'datos_integrantes/listado_integrantes_contraloria.php' : 'maestro_personal.php'; ?>'">Cancelar</button>
										</div>
									</div>
								</form>
								<!-- END FORM-->
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

									<div class="tab-pane"  id="tab_5"><iframe id="iframe_tab_5" src="" frameborder="0" scrolling="0" onload="resizeIframe(this);"></iframe></div>
                                                                        <div class="tab-pane"  id="tab_6"><iframe id="iframe_tab_6" src="" frameborder="0" scrolling="0" onload="resizeIframe(this);"></iframe></div>
                                                                        <div class="tab-pane"  id="tab_7"><iframe id="iframe_tab_7" src="" frameborder="0" scrolling="0" onload="resizeIframe(this);"></iframe></div>
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
<script src="lib/funciones_ag_integrantes.js?<?php echo time(); ?>"></script>
<script>
    //var GLOBAL_CTACONTABLE=0;
   
$(document).ready(function(){

        uid_user_aprueba   = $("#uid_user_aprueba").val();
	
                
            $("#apruebasolicitud").empty();
			$("#apruebasolicitud").hide();
			let es_workflow = $("#workflow").val();
        
            
	if(es_workflow)
	{

        if(document.getElementById("apruebasolicitud_si").checked===true)
		{
				$("#apruebasolicitud").empty();
					$("#apruebasolicitud").append('Cargando...');
					$("#apruebasolicitud").show();
			$.get("datos_integrantes/ajax/listarUsuarioSolicitud.php",{uid_user_aprueba:uid_user_aprueba},function(result){
				$("#apruebasolicitud").empty();
				$("#apruebasolicitud").show();
				$("#apruebasolicitud").append(result);
							$("#usuario_aprueba").select2();
			});
			}
			
			$("#apruebasolicitud_no").on('click',function(){
			$("#apruebasolicitud").hide();
					$("#apruebasolicitud").empty();
		});
		
			
		$("#apruebasolicitud_si").on('click',function(){
				//alert($("#solicitud1").prop('checked')+' '+$("#solicitud2").prop('checked'));  
					$("#apruebasolicitud").empty();
					$("#apruebasolicitud").append('Cargando...');
					$("#apruebasolicitud").show();
			$.get("datos_integrantes/ajax/listarUsuarioSolicitud.php",function(result){
				$("#apruebasolicitud").empty();
				$("#apruebasolicitud").show();
				$("#apruebasolicitud").append(result);
							$("#usuario_aprueba").select2();
			});
		});
	}
        
        $("#usuario_aprueba").select2();
        
        function cargarUsuarioAprueba() {
            //alert($('#solicitud1').is(':checked'));
            if($('#solicitud1').is(':checked')){
                var uid_user_aprueba=$('#uid_user_aprueba').val();
                $.get("datos_integrantes/ajax/listarUsuarioSolicitud.php",{uid_user_aprueba:uid_user_aprueba},function(result){
                            $("#apruebasolicitud").empty();
                            $("#apruebasolicitud").show();
                            $("#apruebasolicitud").append(result);
                });
            }
        }
        
//        antiguedad($("#fecing").val());
 });        
        function recargar()
        {
            var r = confirm("¿Realmente desea Recargar esta página?\n Perderá todo el trabajo no guardado");
            if (r == true) {
                document.location.href = 'ag_integrantes.php?ficha=' + FICHA_ACTUAL + '&edit&back_listado_integrantes_contraloria';
            }//document.location.href = '../../reportes/pdf/datos_personal3.php?cedula=' + CEDULA_ACTUAL + '&ficha=' + FICHA_ACTUAL;
	}
        
        function actualizar_departamento()
        {
            Descripcion = $("#departamento_estructura").val();
            IdDepartamento = $("#departamento_estructura").val();
            IdJefe = $("#usuario_uid").val();
            uid_jefe = $("#uid_user_aprueba").val();
            var r = confirm("¿Realmente desea Actualizar el Departamento "+Descripcion+"?");
            if (r == true) 
            {                
		//alert(cedula);
		$.get("datos_integrantes/ajax/ActualizarDepartamento.php",{IdDepartamento:IdDepartamento,IdJefe:IdJefe,uid_jefe:uid_jefe},function(resultado){
			if (resultado) {
				                                                       
                                actualizar = parseInt(resultado);                                
                                //alert(suesal1+'>'+suesal2);                                                                
				if (actualizar==1)
                                {
                                    
                                    alert("Departamento Actualizado con Exito");
                                    
                                }
                                else
                                {
                                    alert("Ocurrió un Error al Actualizar Departamento");
                                    
				}
			}                        
		});
            }
	}

</script>
<script>
var FICHA_ACTUAL  = '<?php echo $ficha_actual; ?>';
var CEDULA_ACTUAL = '<?php echo (isset($integrante->cedula)) ? $integrante->cedula : ''; ?>';
var OPERACION     = '<?php echo $operacion; ?>';
var TIPO_EMPRESA  = '<?php echo $empresa->tipo_empresa; ?>';
//4: Colombia, 12: Panamá, 15: Venezuela
var PAIS          = '<?php echo $empresa->pais; ?>';
var NIVEL1        = '<?php echo $empresa->nivel1; ?>';
var NIVEL2        = '<?php echo $empresa->nivel2; ?>';
var NIVEL3        = '<?php echo $empresa->nivel3; ?>';
var NIVEL4        = '<?php echo $empresa->nivel4; ?>';
var NIVEL5        = '<?php echo $empresa->nivel5; ?>';
var NIVEL6        = '<?php echo $empresa->nivel6; ?>';
var NIVEL7        = '<?php echo $empresa->nivel7; ?>';
var USUARIO_ACCESO_SUELDO = '<?php echo $usuario->acceso_sueldo; ?>';
var NUMERO_POSICIONES = parseInt('<?php echo $posiciones->cantidad; ?>');
var LONGITUD_POSICION = '<?php echo ($posiciones->longitud >= 3) ? 3 : $posiciones->longitud; ?>';
var PARENT        = "ag_integrantes";
</script>
<script src="lib/validaciones_ag_integrantes.js?<?php echo time(); ?>"></script>
<script>
$('a.fancybox').fancybox();

$(".btn-print").click(function(){
	document.location.href = '../fpdf/datos_personal.php?cedula=' + CEDULA_ACTUAL + '&ficha=' + FICHA_ACTUAL;
});	
</script>
</body>
</html>
