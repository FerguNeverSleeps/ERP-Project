<?php
// require_once("../lib/common.php");
require_once('../lib/database.php');

$db           = new Database($_SESSION['bd']);

$ficha_actual = (isset($_GET['ficha'])) ? $_GET['ficha'] : '';
$operacion    = (isset($_GET['edit']))  ? 'editar' : 'agregar';

//PARAMETROS WORKFLOW - CONSULTA
$sql_workflow        = "SELECT workflow,db_host,db_name,db_user,db_pass FROM param_ws";
$res_workflow        = $db->query($sql_workflow);
$res_workflow        = $res_workflow->fetch_array();

 //echo $res_workflow["workflow"].'zxzczc';
                    
if($res_workflow["workflow"]==1)
{
    $workflow       = $res_workflow["workflow"];
    $db_host        = $res_workflow["db_host"];
    $db_name        = $res_workflow["db_name"];
    $db_user        = $res_workflow["db_user"];
    $db_pass        = $res_workflow["db_pass"];    
    $conexion       = mysqli_connect($db_host, $db_user, $db_pass, $db_name) or die('No se puede conectar con el servidor Workflow');
    mysqli_query($conexion, 'SET CHARACTER SET utf8');
}

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
$usuario='';
if($operacion=='editar')
{
	$sql = "SELECT * FROM nompersonal WHERE nompersonal.ficha='".$ficha_actual."'";
	$res = $db->query($sql);
       

	if($integrante = $res->fetch_object())
	{
                if(!empty($integrante->fecnac) && $integrante->fecnac!='0000-00-00')
                {
                    $fecha_nacimiento = DateTime::createFromFormat('Y-m-d', $integrante->fecnac);
                    $fecha_nacimiento = ($fecha_nacimiento !== false) ? $fecha_nacimiento->format('d-m-Y') : '';
                }
		
                if(!empty($integrante->fecing) && $integrante->fecing!='0000-00-00')
                {
                    $fecha_ingreso = DateTime::createFromFormat('Y-m-d', $integrante->fecing);
                    $fecha_ingreso = ($fecha_ingreso !== false) ? $fecha_ingreso->format('d-m-Y') : '';
                }
		

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
                
                if(empty($integrante->inicio_periodo) || $integrante->inicio_periodo=='0000-00-00')
		{
			$inicio_periodo = $fecha_ingreso;	
		}
                
		if(!empty($integrante->inicio_periodo) && $integrante->inicio_periodo!='0000-00-00')
		{
			$inicio_periodo =  DateTime::createFromFormat('Y-m-d', $integrante->inicio_periodo);
			$inicio_periodo = ($inicio_periodo !== false) ? $inicio_periodo->format('d-m-Y') : '';
		}
                
                //echo 'asdasd'.$inicio_periodo;
                
                                                
		if(!empty($integrante->fin_periodo) && $integrante->fin_periodo!='0000-00-00')
		{
			$fin_periodo =  DateTime::createFromFormat('Y-m-d', $integrante->fin_periodo);
			$fin_periodo = ($fin_periodo !== false) ? $fin_periodo->format('d-m-Y') : '';
		}
                
                if(!empty($integrante->fecha_permanencia) && $integrante->fecha_permanencia!='0000-00-00')
		{
                   // echo $integrante->fecha_permanencia.'asdasd';
			$fecha_permanencia =  DateTime::createFromFormat('Y-m-d', $integrante->fecha_permanencia);
			$fecha_permanencia = ($fecha_permanencia !== false) ? $fecha_permanencia->format('d-m-Y') : '';
                        //echo '  ddddd'.$fecha_permanencia;
		}
                
                 if(!empty($integrante->sfecing) && $integrante->sfecing!='0000-00-00')
		{
                   // echo $integrante->fecha_permanencia.'asdasd';
			$fecha_creacion =  DateTime::createFromFormat('Y-m-d', $integrante->sfecing);
			$fecha_creacion = ($fecha_creacion !== false) ? $fecha_creacion->format('d-m-Y') : '';
//                        echo '  ddddd'.$fecha_creacion;
		}  
                 if(!empty($integrante->fechajubipensi) && $integrante->fechajubipensi!='0000-00-00')
                {
                    $fechajubipensi = DateTime::createFromFormat('Y-m-d', $integrante->fechajubipensi);
                    $fechajubipensi = ($fechajubipensi !== false) ? $fechajubipensi->format('d-m-Y') : '';
                }
                
                //if($integrante->useruid){
                    $usuario = $integrante->usuario_workflow ;
                    //$usuario = str_replace(' ','',$usuario);
                //}
             //echo $usuario;
                
                             
	}
}
else
{
    //$sql = "SELECT max(ficha) as valor FROM nompersonal";
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
<body>

<div class="page-container">
	<div class="page-content-wrapper">
		<div class="page-content">
			<!-- BEGIN PAGE CONTENT-->
			<div class="row">
				<div class="col-md-12">
                                    <form class="form-horizontal" id="form-integrantes" name="form-integrantes"  onsubmit="return validarDisponibilidad()" method="post" enctype="multipart/form-data" role="form" style="margin-bottom: 5px;">
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
                                                        <li><a href="#tab_8" id="tab-estudios" data-toggle="tab"><i class="fa fa-archive"></i>Estudios</a></li>
							<li><a href="#tab_7" id="tab-tiempos" data-toggle="tab"><i class="fa fa-clock-o"></i>Tiempos</a></li>

							<li><a href="#tab_9" id="tab-vacaciones" data-toggle="tab"><i class="fa fa-sun-o"></i>Vacaciones</a></li>
                                                        
							<?php
								}
							?>
						</ul>
						<div class="tab-content">
                                                    
							<div class="tab-pane active" id="tab_0">
								<!-- BEGIN FORM-->
								
									<div class="form-body">
										<div class="row">
											<div class="col-md-9">
												<div class="form-group"> 
													<label class="control-label col-md-3">Planilla <span class="required">*</span></label>
													<div class="col-md-9">
														<?php 
															$sql = "SELECT codtip, descrip FROM nomtipos_nomina WHERE 1";

															/*if($operacion=='agregar')
																$sql .= " AND (codtip = '".$_SESSION['codigo_nomina']."') ";*/

															$res = $db->query($sql);
														?>
														<select name="tipnom" id="tipnom" class="form-control">
															<?php
																
                                                                                                                                //echo "<option value=''>Seleccione una planilla</option>";

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
                                                                                                                            <input type="hidden" id="nomposicion_id_actual" name="nomposicion_id_actual"  value="<? echo (isset($integrante) && $integrante->nomposicion_id) ? $integrante->nomposicion_id : ''; ?>">
                                                                                                                            <input type="number" id="nomposicion_id" name="nomposicion_id" min="1"  class="form-control" value="<? echo (isset($integrante) && $integrante->nomposicion_id) ? $integrante->nomposicion_id : ''; ?>" placeholder="Introduce la posición">
																<?php 
                                                                                                                                       
                                                                                                                                        
                                                                                                                                        
//																	$sql = "SELECT nomposicion_id, sueldo_propuesto FROM nomposicion";
//																	$res = $db->query($sql);                                                                                                                                                                                                                                                                               
//
//																	$max_sueldo = (isset($usuario->acceso_sueldo) && $usuario->acceso_sueldo==1) ? '' : 0;
																?>
<!--																<select name="nomposicion_id" id="nomposicion_id" class="form-control select2">
																	<?php
//                                                                                                                                                echo "<option value=''>Seleccione una posici&oacute;n</option>";
//																		//if($operacion=='agregar')																		
//																		while($fila = $res->fetch_assoc())
//																		{
//																			if(isset($integrante->nomposicion_id) && $integrante->nomposicion_id==$fila['nomposicion_id'])
//																			{
//																				$max_sueldo = $fila['sueldo_propuesto'];
//																				echo "<option value='".$fila['nomposicion_id']."' selected>".$fila['nomposicion_id']."</option>";
//																			}
//																			else
//																				echo "<option value='".$fila['nomposicion_id']."'>".$fila['nomposicion_id']."</option>";
//																		}
																	?>
																</select>-->
															</div>
															<input type="hidden" name="posicion_original" id="posicion_original" value="<?php echo isset($integrante->nomposicion_id) ? $integrante->nomposicion_id : ''; ?>">
														</div>
													<?php 
													}
													?>
												                                                                                               
												<div id="posicion_disponible">
												</div>
                                                                                                <div id="accion_funcionario">
												</div>
                                                                                                <div class="form-group">
													<label class="control-label col-md-3">Cargo <span class="required">*</span></label>
													<div class="col-md-9">
														<?php 
															//$sql = "SELECT cod_car, des_car FROM nomcargos";
															//$res = $db->query($sql);
														?>
														<select name="codcargo" id="codcargo" class="form-control">
                                                                                                                    <option value=''>No existe cargo para la posición introducida.</option>
															<?php
																//if($operacion=='agregar')
																//echo "<option value=''>Seleccione un cargo</option>";

																//while($fila = $res->fetch_assoc())
																//{
																//	if(isset($integrante) && $integrante->codcargo==$fila['cod_car'])
																//		echo "<option value='".$fila['cod_car']."' selected>".$fila['des_car']."</option>";
																//	else
																//		echo "<option value='".$fila['cod_car']."'>".$fila['des_car']."</option>";
																//}
															?>
														</select>
													</div>
													<input type="hidden" name="cargo_original" id="cargo_original" value="<?php echo isset($integrante->codcargo) ? $integrante->codcargo : ''; ?>">
												</div> 
												<div class="form-group" style="display: <?php echo $display_acceso; ?>">
													<label class="control-label col-md-3">Salario<span class="required">*</span></label>
													<div class="col-md-9">
                                                                                                            <input type="<?php echo $type_acceso; ?>" name="suesal" id="suesal" class="form-control" value="<?php echo (isset($integrante) && $integrante->suesal) ? $integrante->suesal : 0.00; ?>">
                                                                                                            <input type="hidden" name="sueldo_original" id="sueldo_original" value="<?php echo (isset($integrante) && $integrante->suesal) ? $integrante->suesal : 0; ?>">
                                                                                                            <input type="hidden" name="max_sueldo" id="max_sueldo" value="<?php echo $max_sueldo; ?>" />
                                                                                                        </div>
												</div>
												
												<div class="form-group" style="display: <?php echo $display_acceso; ?>">
													<label class="control-label col-md-3">Sobresueldo (080) </label>
													<div class="col-md-9">
														<input type="<?php echo $type_acceso; ?>" name="otros" id="otros" class="form-control" value="<?php echo (isset($integrante) && $integrante->otros) ? $integrante->otros : 0.00; ?>">
													</div>
												</div>
												<div class="form-group" style="display: <?php echo $display_acceso; ?>">
													<label class="control-label col-md-3">Gastos de Representación (030) </label>
													<div class="col-md-9">
														<input type="<?php echo $type_acceso; ?>" name="gastos_repre" id="gastos_repre" class="form-control" value="<?php echo (isset($integrante) && $integrante->gastos_representacion) ? $integrante->gastos_representacion : 0.00; ?>">

													</div>
												</div>
                                                                                                <div class="form-group" style="display: <?php echo $display_acceso; ?>">
													<label class="control-label col-md-3">Dieta </label>
													<div class="col-md-9">
														<input type="<?php echo $type_acceso; ?>" name="dieta" id="dieta" class="form-control" value="<?php echo (isset($integrante) && $integrante->dieta) ? $integrante->dieta : 0.00; ?>">

													</div>
												</div>
                                                                                                <div class="form-group" style="display: <?php echo $display_acceso; ?>">
													<label class="control-label col-md-3">Combustible </label>
													<div class="col-md-9">
														<input type="<?php echo $type_acceso; ?>" name="combustible" id="combustible" class="form-control" value="<?php echo (isset($integrante) && $integrante->combustible) ? $integrante->combustible : 0.00; ?>">

													</div>
												</div>
                                                                                                <div class="form-group">
													<label class="control-label col-md-3">C&eacute;dula <span class="required">*</span></label>
													<div class="col-md-9">
                                                                                                            <input type="text" name="cedula" id="cedula" class="form-control" value="<?php  echo (isset($integrante)) ? $integrante->cedula : ''; ?>" <?= ($operacion == 'agregar') ? 'onblur="ValidarCedula();"' : ''; ?> >
													</div>
												</div>
                                                                                                <div class="form-group">
                                                                                                        <label class="control-label col-md-3"></label>
													<label class="control-label col-md-1">Sigla </label>
													<div class="col-md-2">
                                                                                                            <input type="text" name="sigla" id="sigla" class="form-control" value="<?php  echo (isset($integrante)) ? $integrante->sigla : ''; ?>">
													</div>
                                                                                                        <label class="control-label col-md-1">Provincia</label>
													<div class="col-md-2">
														<input type="text" name="provincia" id="provincia"  class="form-control" value="<?php  echo (isset($integrante)) ? $integrante->provincia : ''; ?>">
													</div>
                                                                                                        
												</div>
                                                                                                <div class="form-group">
													<label class="control-label col-md-3"></label>
                                                                                                        <label class="control-label col-md-1">Tomo </label>
													<div class="col-md-2">
														<input type="text" name="tomo" id="tomo" class="form-control" value="<?php  echo (isset($integrante)) ? $integrante->tomo : ''; ?>">
													</div>
                                                                                                        <label class="control-label col-md-1">Folio </label>
													<div class="col-md-2">
														<input type="text" name="folio" id="folio" class="form-control" value="<?php  echo (isset($integrante)) ? $integrante->folio : ''; ?>">
													</div>
												</div>
                                                                                                <div class="form-group">
													<label class="control-label col-md-3">Primer Nombre <span class="required">*</span></label>
													<div class="col-md-3">
														<input type="text" name="nombres" id="nombres" class="form-control" value="<?php  echo (isset($integrante)) ? $integrante->nombres : ''; ?>">
													</div>	
                                                                                                        <label class="control-label col-md-3">Segundo Nombre </label>
													<div class="col-md-3">
														<input type="text" name="segundo_nombre" id="segundo_nombre" class="form-control" value="<?php  echo (isset($integrante)) ? $integrante->nombres2 : ''; ?>">
													</div>	
												</div>

												<div class="form-group">
                                                                                                        <label class="control-label col-md-3">Primer Apellido <span class="required">*</span></label>
													<div class="col-md-3">
														<input type="text" name="apellidos" id="apellidos" class="form-control" value="<?php  echo (isset($integrante)) ? $integrante->apellidos : ''; ?>">
													</div>
													<label class="control-label col-md-3">Segundo Apellido </label>
													<div class="col-md-3">
														<input type="text" name="apellido_materno" id="apellido_materno" class="form-control" value="<?php  echo (isset($integrante)) ? $integrante->apellido_materno : ''; ?>">
													</div>													
												</div>
                                                                                                <div class="form-group">
													
													<label class="control-label col-md-3">Apellido de Casada </label>
													<div class="col-md-3">
														<input type="text" name="apellido_casada" id="apellido_casada" class="form-control" value="<?php  echo (isset($integrante)) ? $integrante->apellido_casada : ''; ?>">
													</div>
												</div>
                                                                                                <?php
                                                                                                if ($workflow ==0)
                                                                                                {?>
                                                                                                    <input type="hidden" name="usuario" id="usuario" value="">	                                                                                                                                                                                                 
                                                                                                <?php                                                                                                
                                                                                                }
                                                                                                else
                                                                                                {?>
                                                                                                    <div class="form-group">
                                                                                                            <label class="control-label col-md-3">Usuario Workflow</label>
                                                                                                            <div class="col-md-3">
                                                                                                                <input type="text" name="usuario" id="usuario" class="form-control" value="<?php  echo (isset($integrante)) ? $usuario : ''; ?>">
                                                                                                            </div>
                                                                                                            <label class="control-label col-md-3">USER UID</label>
                                                                                                            <div class="col-md-3">
                                                                                                                <input type="text" name="usuario_uid" id="usuario_uid" class="form-control" value="<?php  echo (isset($integrante)) ? $integrante->useruid : ''; ?>">
                                                                                                            </div> 
                                                                                                    </div>        
                                                                                                <?php                                                                                                
                                                                                                }
                                                                                                ?>
												<div class="form-group">
													<label class="control-label col-md-3">N&uacute;mero Decreto</label>
													<div class="col-md-3">
														<input type="text" name="num_decreto" id="num_decreto" class="form-control" value="<?php  echo (isset($integrante)) ? $integrante->num_decreto : ''; ?>"  <?= ($operacion == 'editar') ? 'readonly' : ''; ?> >
													</div>
                                                                                                        
                                                                                                        <label class="control-label col-md-3">N&uacute;mero Resuelto</label>
													<div class="col-md-3">
														<input type="text" name="num_resolucion" id="num_resolucion" class="form-control" value="<?php  echo (isset($integrante)) ? $integrante->num_resolucion : ''; ?>"  <?= ($operacion == 'editar') ? 'readonly' : ''; ?> >
													</div>
												</div>
												<div class="form-group">
													<label class="control-label col-md-3">Fecha Decreto</label>
													<div class="col-md-3">
														<div class="input-group date date-picker" data-provide="datepicker">
															<input type="text" class="form-control" name="fecha_decreto" id="fecha_decreto" value="<?php echo (isset($fecha_decreto)) ? $fecha_decreto : ''; ?>"  <?= ($operacion == 'editar') ? 'readonly' : ''; ?> >
															<span class="input-group-btn">
																<button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
															</span>
														</div>
													</div>
                                                                                                        <label class="control-label col-md-3">Fecha Resuelto</label>
													<div class="col-md-3">
														<div class="input-group date date-picker" data-provide="datepicker">
															<input type="text" class="form-control" name="fecha_resolucion" id="fecha_resolucion" value="<?php echo (isset($fecha_resolucion)) ? $fecha_resolucion : ''; ?>"  <?= ($operacion == 'editar') ? 'readonly' : ''; ?> >
															<span class="input-group-btn">
																<button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
															</span>
														</div>
													</div>
												</div>
												<div class="form-group">
													<label class="control-label col-md-3">N° Colaborador</label>
													<div class="col-md-9">
														<input type="text" name="ficha" id="ficha" class="form-control" value="<?php echo $ficha_actual; ?>" readonly> 
													</div>
													<input type="hidden" name="personal_id" id="personal_id" value="<?php echo (isset($integrante) ? $integrante->personal_id : '0'); ?>">
												</div>
												
												<div class="form-group">
													<label class="control-label col-md-3">Foto</label>
													<div class="col-md-9">
														<input type="file" id="foto" name="foto">
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
														$nacionalidad1 = '';//'checked';
														$nacionalidad2 = $nacionalidad3 = '';

														if(isset($integrante))
														{
															$nacionalidad1 = ($integrante->nacionalidad=='1')  ? 'checked' : '';
															$nacionalidad2 = ($integrante->nacionalidad=='2')  ? 'checked' : '';
															$nacionalidad3 = ($integrante->nacionalidad=='3')  ? 'checked' : '';
														} 
													?>
													<label class="control-label col-md-3">Nacionalidad <span class="required">*</span></label>
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
														$sexo1 = '';//'checked';
														$sexo2 = '';

														if(isset($integrante))
														{
															$sexo1 = ($integrante->sexo=='Masculino') ? 'checked' : '';
															$sexo2 = ($integrante->sexo=='Femenino')  ? 'checked' : '';
														} 
													?>
													<label class="control-label col-md-3">Sexo <span class="required">*</span> </label>
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
                                                                                                    <label class="col-md-3 control-label" >Dirección Provincia </label>
                                                                                                    <div class="col-md-9">
                                                                                                            <?php 
                                                                                                                    $sql = "SELECT * FROM provincia";
                                                                                                                    $res = $db->query($sql);
                                                                                                            ?>
                                                                                                            <select name="direccion_provincia" id="direccion_provincia" class="form-control select2me">

                                                                                                                    <option value=''>Seleccione Provincia</option>
                                                                                                                    <?php
                                                                                                                            while($fila = $res->fetch_assoc())
                                                                                                                            {
                                                                                                                                    if(isset($integrante) && $integrante->dir_provincia==$fila['id_provincia'])
                                                                                                                                            echo "<option value='".$fila['id_provincia']."' selected>".$fila['nombre']."</option>";
                                                                                                                                    else
                                                                                                                                            echo "<option value='".$fila['id_provincia']."'>".$fila['nombre']."</option>";
                                                                                                                            }
                                                                                                                    ?>
                                                                                                            </select>
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="form-group">
                                                                                                    <label class="col-md-3 control-label" >Dirección Distrito </label>
                                                                                                    <div class="col-md-9">
                                                                                                            <?php 
                                                                                                                    $sql = "SELECT * FROM distrito";
                                                                                                                    $res = $db->query($sql);
                                                                                                            ?>
                                                                                                            <select name="direccion_distrito" id="direccion_distrito" class="form-control select2me">

                                                                                                                    <option value=''>Seleccione Distrito</option>
                                                                                                                    <?php
                                                                                                                            while($fila = $res->fetch_assoc())
                                                                                                                            {
                                                                                                                                    if(isset($integrante) && $integrante->dir_distrito==$fila['id_distrito'])
                                                                                                                                            echo "<option value='".$fila['id_distrito']."' selected>".$fila['nombre']."</option>";
                                                                                                                                    else
                                                                                                                                            echo "<option value='".$fila['id_distrito']."'>".$fila['nombre']."</option>";
                                                                                                                            }
                                                                                                                    ?>
                                                                                                            </select>
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="form-group">
                                                                                                    <label class="col-md-3 control-label" >Dirección Corregimiento </label>
                                                                                                    <div class="col-md-9">
                                                                                                            <?php 
                                                                                                                    $sql = "SELECT * FROM corregimiento";
                                                                                                                    $res = $db->query($sql);
                                                                                                            ?>
                                                                                                            <select name="direccion_corregimiento" id="direccion_corregimiento" class="form-control select2me">

                                                                                                                    <option value=''>Seleccione Corregimiento</option>
                                                                                                                    <?php
                                                                                                                            while($fila = $res->fetch_assoc())
                                                                                                                            {
                                                                                                                                    if(isset($integrante) && $integrante->dir_corregimiento==$fila['id_corregimiento'])
                                                                                                                                            echo "<option value='".$fila['id_corregimiento']."' selected>".$fila['nombre']."</option>";
                                                                                                                                    else
                                                                                                                                            echo "<option value='".$fila['id_corregimiento']."'>".$fila['nombre']."</option>";
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
													<label class="control-label col-md-3">E-mail Sugerido </label>
													<div class="col-md-9">
														<input type="text" name="email" id="email" class="form-control" value="<?php  echo (isset($integrante)) ? $integrante->email : ''; ?>">
													</div>
												</div>
                                                                                                <div class="form-group">
													<label class="control-label col-md-3">E-mail Alternativo </label>
													<div class="col-md-9">
														<input type="text" name="correo_alternativo" id="correo_alternativo" class="form-control" value="<?php  echo (isset($integrante)) ? $integrante->correo_alternativo : ''; ?>">
													</div>
												</div>
												<div class="form-group">
													<label class="control-label col-md-3">Estatus <span class="required">*</span> <?php if($empresa->tipo_empresa != 1){ ?><?php } ?></label>
													<div class="col-md-9">
														<?php 
															if($operacion=='agregar')
                                                                                                                            $sql = "SELECT situacion FROM nomsituaciones WHERE `codigo` IN ( 1, 40, 42 )";
                                                                                                                        else
                                                                                                                            $sql = "SELECT situacion FROM nomsituaciones";
															$res = $db->query($sql);
														?>
														<select name="estado" id="estado" class="form-control select2" <?= ($operacion == 'editar') ? 'disabled' : ''; ?> >                                                                                                                        
															<?php
																if($operacion=='agregar')
																	echo "<option value=''>Seleccione Estatus</option>";

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
													<label class="control-label col-md-3">Fecha Inicio Institución <span class="required">*</span> <?php if($empresa->tipo_empresa != 1){ ?> <?php } ?></label>
													<div class="col-md-9">
                                                                                                            <div class="input-group date date-picker" data-provide="datepicker" data-date-end-date="0d" onchange="cambiarFecha()">
															<input type="text" class="form-control" name="fecing" id="fecing" value="<?php echo (isset($fecha_ingreso)) ? $fecha_ingreso : ''; ?>" <?= ($operacion == 'editar') ? 'readonly' : ''; ?> >
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
                                                                                                <?php
                                                                                                    if($operacion=='editar')
                                                                                                    {
                                                                                                ?>
                                                                                                        <div class="form-group">
                                                                                                                <label class="control-label col-md-3">Decreto Baja</label>
                                                                                                                <div class="col-md-3">
                                                                                                                        <input type="text" name="num_decreto_baja" id="num_decreto_baja" class="form-control" value="<?php  echo (isset($integrante)) ? $integrante->num_decreto_baja : ''; ?>" <?= ($operacion == 'editar') ? 'readonly' : ''; ?> >
                                                                                                                </div>
                                                                                                                <label class="control-label col-md-3">Resuelto Baja</label>
                                                                                                                <div class="col-md-3">
                                                                                                                        <input type="text" name="num_resolucion_baja" id="num_resolucion_baja" class="form-control" value="<?php  echo (isset($integrante)) ? $integrante->num_resolucion_baja : ''; ?>"  <?= ($operacion == 'editar') ? 'readonly' : ''; ?> >
                                                                                                                </div>
                                                                                                        </div>
                                                                                                        <div class="form-group">
                                                                                                                <label class="control-label col-md-3">Fecha Decreto Baja</label>
                                                                                                                <div class="col-md-3">
                                                                                                                        <div class="input-group date date-picker" data-provide="datepicker" data-date-end-date="0d">	
                                                                                                                                <input type="text" class="form-control" name="fecha_decreto_baja" id="fecha_decreto_baja" value="<?php echo (isset($fecha_decreto_baja)) ? $fecha_decreto_baja : ''; ?>" <?= ($operacion == 'editar') ? 'readonly' : ''; ?>>
                                                                                                                                <span class="input-group-btn">
                                                                                                                                        <button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
                                                                                                                                </span>
                                                                                                                        </div>
                                                                                                                </div>
                                                                                                                <label class="control-label col-md-3">Fecha Resuelto Baja</label>
                                                                                                                <div class="col-md-3">
                                                                                                                        <div class="input-group date date-picker" data-provide="datepicker" data-date-end-date="0d">	
                                                                                                                                <input type="text" class="form-control" name="fecha_resolucion_baja" id="fecha_resolucion_baja" value="<?php echo (isset($fecha_resolucion_baja)) ? $fecha_resolucion_baja : ''; ?>"  <?= ($operacion == 'editar') ? 'readonly' : ''; ?> >
                                                                                                                                <span class="input-group-btn">
                                                                                                                                        <button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
                                                                                                                                </span>
                                                                                                                        </div>
                                                                                                                </div>
                                                                                                        </div>
                                                                                                <?php
                                                                                                    }
                                                                                                ?>
                                                                                                     <div class="form-group">
                                                                                                    <label class="col-md-3 control-label" >Causal Baja</label>
                                                                                                    <div class="col-md-9">
                                                                                                            <?php 
                                                                                                                    $sql = "SELECT * FROM causal_baja";
                                                                                                                    $res = $db->query($sql);
                                                                                                            ?>
                                                                                                            <select name="causal_baja" id="causal_baja" class="form-control select2me" disabled>

                                                                                                                    <option value=''>Seleccione Causal Baja</option>
                                                                                                                    <?php
                                                                                                                            while($fila = $res->fetch_assoc())
                                                                                                                            {
                                                                                                                                    if(isset($integrante) && $integrante->causal_despido==$fila['id_causal_baja'])
                                                                                                                                            echo "<option value='".$fila['id_causal_baja']."' selected>".$fila['nombre']."</option>";
                                                                                                                                    else
                                                                                                                                            echo "<option value='".$fila['id_causal_baja']."'>".$fila['nombre']."</option>";
                                                                                                                            }
                                                                                                                    ?>
                                                                                                            </select>
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
														$data = array('Efectivo', 'ACH', 'Cheque', 'Cuenta Ahorro', 'Cuenta Corriente');
													?>
													<label class="control-label col-md-3">Forma de Pago <span class="required">*</span><?php if($empresa->tipo_empresa != 1){ ?><?php } ?></label>
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
												<?php $display_forma_pago = (isset($integrante) && ($integrante->forcob!='Efectivo' && $integrante->forcob!='ACH')) ? 'display: block' : 'display: none'; ?>
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
													<label class="control-label col-md-3">Cuenta </label>
													<div class="col-md-9">
														<input type="text" name="cuentacob" id="cuentacob" class="form-control" value="<?php echo (isset($integrante)) ? $integrante->cuentacob : ''; ?>">
													</div>
												</div>
												
												<div class="form-group">
													<?php
														//$tipemp1 = 'checked';
                                                                                                                $tipemp1 = '';
														$tipemp2 = $tipemp3 = $tipemp4 = $tipemp5 = '';
														$disabled_periodo = 'disabled';

														if(isset($integrante))
														{
															$tipemp1 = ($integrante->tipemp=='Fijo')  ? 'checked' : '';
															$tipemp2 = ($integrante->tipemp=='Contratado Transitorio')  ? 'checked' : '';
															$tipemp3 = ($integrante->tipemp=='Contratado Contingente')  ? 'checked' : '';
															$tipemp4 = ($integrante->tipemp=='Contratado Servicios')    ? 'checked' : '';
                                                                                                                        $tipemp5 = ($integrante->tipemp=='Contratado Diputados')    ? 'checked' : '';

															$disabled_periodo = ($tipemp1=='checked') ? 'disabled' : '' ;
														} 
													?>
													<label class="control-label col-md-3">Tipo de Contrato <span class="required">*</span> </label>
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
															<input type="radio" name="tipemp" id="tipemp4" value="Contratado Servicios" <?php echo $tipemp4; ?>> Contrato Servicios Profesionales</label>														
                                                                                                                </div>
                                                                                                                <div class="radio-list">
															<label class="radio-inline">
															<input type="radio" name="tipemp" id="tipemp5" value="Contratado Diputados" <?php echo $tipemp5; ?>> <span class="tipo_contrato">Contrato Diputado</span></label>
														</div>
                                                                                                        </div>
												</div>
												<div class="form-group">
													<label class="control-label col-md-3"></label>
													<div class="col-md-9">
														<div class="row">
															<label class="control-label col-md-3" style="padding-left: 20px">Fecha Inicio </label>
															<div class="col-md-5" style="padding-right: 0px">
																<div class="input-group date date-picker" data-provide="datepicker" data-date-end-date="0d">	
                                                                                                                                    <input type="text" class="form-control" name="inicio_periodo" id="inicio_periodo" onchange="parrafoObservacion()" value="<?php echo (isset($inicio_periodo)) ? $inicio_periodo : ''; ?>" <?php echo $disabled_periodo; ?> <?= ($operacion == 'editar') ? 'readonly' : ''; ?> >
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
																	<input type="text" class="form-control" name="fin_periodo" id="fin_periodo"  onchange="parrafoObservacion()" value="<?php echo (isset($fin_periodo)) ? $fin_periodo : ''; ?>" <?php echo $disabled_periodo; ?> <?= ($operacion == 'editar') ? 'readonly' : ''; ?> >
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
                                                                                                
                                                                                                <div class="form-group">
													<label class="control-label col-md-3">Creado/Editado Por: </label>
													<div class="col-md-3">
                                                                                                            <input type="text" name="usuario_creacion" readonly="true" id="usuario_creacion" class="form-control" value="<?php echo (isset($integrante)) ? $integrante->comentario : ''; ?>">
													</div>
                                                                                                        <?php if (!isset($integrante))
                                                                                                        {?>
                                                                                                            <label class="control-label col-md-3">Fecha Solicitud: </label>
                                                                                                            <div class="col-md-3">
                                                                                                                    <input type="text" name="fecha_creacion" readonly="true" id="fecha_creacion" class="form-control" value="<?php echo (isset($integrante)) ? $fecha_creacion : ''; ?>">
                                                                                                            </div>
                                                                                                        <?}
                                                                                                        else
                                                                                                        {?>
                                                                                                            <label class="control-label col-md-3">Fecha Solicitud: </label>
                                                                                                            <div class="col-md-3" style="padding-right: 0px">
                                                                                                                <div class="input-group date date-picker" data-provide="datepicker" data-date-end-date="0d">	
                                                                                                                    <input type="text" class="form-control" name="fecha_creacion" id="fecha_creacion" value="<?php echo (isset($integrante)) ? $fecha_creacion : ''; ?>">
                                                                                                                        <span class="input-group-btn">
                                                                                                                                <button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
                                                                                                                        </span>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                            
                                                                                                        <?}?>    
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
									
								
							</div>
							<div class="tab-pane" id="tab_5">                                                            									                                                                                                                       
									<div class="form-body">										
                                                                            <div class="row">
										<div class="col-md-9">                                                                                    
                                                                                    <div class="form-group">
                                                                                            <label class="col-md-3 control-label">Aprueba Solicitudes?</label>
                                                                                            <div class="col-md-4">
                                                                                                <?php
														$solicitud2 = 'checked';													
                                                                                                                $solicitud1 = '';

														if(isset($integrante))
														{
                                                                                                                    
															$solicitud1 = ($integrante->uid_user_aprueba!='' && $integrante->uid_user_aprueba!='')  ? 'checked' : '';
															$solicitud2 = ($integrante->uid_user_aprueba=='' || $integrante->uid_user_aprueba=='')  ? 'checked' : '';
															
														} 
                                                                                                    ?>
                                                                                                <div class="radio-list">
                                                                                                    <label class="radio-inline">
                                                                                                        <input type="hidden" name="uid_user_aprueba" id="uid_user_aprueba" value="<?= $integrante->uid_user_aprueba;?>" <?=$solicitud1?>>
                                                                                                    <input type="radio" name="solicitud" id="solicitud1" value="1" <?=$solicitud1?>> Si</label>
                                                                                                    <label class="radio-inline">
                                                                                                    <input type="radio" name="solicitud" id="solicitud2" value="2" <?=$solicitud2?>> No</label>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="col-md-5">                                                                                                
                                                                                                <div id="apruebasolicitud">
                                                                                                </div>                                                                                                
                                                                                            </div>
                                                                                    </div> 
                                                                                    
                                                                                    <div class="form-group">
                                                                                        <label class="col-md-3 control-label">Prestamo Institucional</label>
                                                                                        <div class="col-md-4">
                                                                                            <?php
                                                                                            
														$externo2 = 'checked';												
                                                                                                                $externo1 = '';
														if(isset($integrante))
														{
															$externo1 = ($integrante->personal_externo=='1')  ? 'checked' : '';
															$externo2 = ($integrante->personal_externo=='2')  ? 'checked' : '';															
														} 
                                                                                                    ?>
                                                                                            <div class="radio-list">
                                                                                                <label class="radio-inline">
                                                                                                <input type="radio" name="personalexterno" id="personalexterno1" value="1" <?=$externo1?>> Si</label>
                                                                                                <label class="radio-inline">
                                                                                                <input type="radio" name="personalexterno" id="personalexterno2" value="2" <?=$externo2?>> No</label>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="col-md-5">                                                                                            
                                                                                            <div id="personaexterna">
                                                                                            </div>                                                                                            	
                                                                                        </div>											
                                                                                    </div>
                                                                                    <div class="form-group">	
                                                                                        <label class="control-label col-md-3">Número Carnet</label>
                                                                                        <div class="col-md-8">
                                                                                            <input type="text" name="numero_carnet" id="numero_carnet" class="form-control form-control-inline" placeholder="xxxx-xxxx" value="<?php echo (isset($integrante->numero_carnet)) ? $integrante->numero_carnet : ''; ?>">
                                                                                        </div>
                                                                                    </div> 
                                                                                    <div class="form-group">	
                                                                                        <label class="control-label col-md-3">Código Carnet</label>
                                                                                        <div class="col-md-8">
                                                                                            <input type="text" name="codigo_carnet" id="codigo_carnet" class="form-control form-control-inline" placeholder="xxxx-xxxx" value="<?php echo (isset($integrante->codigo_carnet)) ? $integrante->codigo_carnet : ''; ?>">
                                                                                        </div>
                                                                                    </div> 
                                                                                    <!--
                                                                                    <div class="form-group">	
                                                                                        <label class="control-label col-md-3">Número Marcar</label>
                                                                                        <div class="col-md-8">
                                                                                            <input type="text" name="numero_marcar" id="numero_marcar" class="form-control form-control-inline" placeholder="xxxx-xxxx" value="<?php echo (isset($integrante->numero_marcar)) ? $integrante->numero_marcar : ''; ?>">
                                                                                        </div>
                                                                                    </div>
                                                                                    -->
                                                                                    <div class="form-group">
                                                                                        <label class="control-label col-md-3">Fecha de Permanencia</label>
                                                                                        <div class="col-md-8">
                                                                                            <div class="input-group date date-picker" data-provide="datepicker" data-date-end-date="0d">
                                                                                                <input type="text" class="form-control form-control-inline" name="fecha_permanencia" id="fecha_permanencia" value="<?php echo (isset($fecha_permanencia)) ? $fecha_permanencia : ''; ?>" >
                                                                                                <span class="input-group-btn">
                                                                                                    <button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
                                                                                                </span>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="form-group">	
                                                                                        <label class="control-label col-md-3">Teléfono Celular</label>
                                                                                        <div class="col-md-8">
                                                                                            <input type="text" name="telefono_celular" id="telefono_celular" class="form-control form-control-inline" placeholder="xxxx-xxxx" value="<?php echo (isset($integrante->TelefonoCelular)) ? $integrante->TelefonoCelular : ''; ?>">
                                                                                        </div>
                                                                                    </div> 
                                                                                    <div class="form-group">
                                                                                            <label class="col-md-3 control-label">Contacto Emergencia</label>
                                                                                            <div class="col-md-8">
                                                                                                <input class="form-control form-control-inline" type="text" name="contacto_emergencia" value="<?php echo (isset($integrante->ContactoEmergencia)) ? $integrante->ContactoEmergencia : ''; ?>" />
                                                                                            </div>
                                                                                    </div>

                                                                                    <div class=" form-group">
                                                                                            <label class="control-label col-md-3">Teléfono Contacto</label>
                                                                                            <div class="col-md-8">
                                                                                                <input class="form-control form-control-inline" type="text" name="telefono_contacto"  value="<?php echo (isset($integrante->TelefonoContactoEmergencia)) ? $integrante->TelefonoContactoEmergencia : ''; ?>" />
                                                                                            </div>
                                                                                    </div>
                                                                                    <div class=" form-group">
                                                                                            <label class="control-label col-md-3">Extensión</label>
                                                                                            <div class="col-md-8">
                                                                                                <input class="form-control form-control-inline" type="text" name="extension"  value="<?php echo (isset($integrante->extension)) ? $integrante->extension : ''; ?>" />
                                                                                            </div>
                                                                                    </div>
                                                                                    <div class="form-group">
                                                                                            <?php
                                                                                                    $data_contribuyente = array('C','A');
                                                                                            ?>
                                                                                            <label class="control-label col-md-3">Tipo Contribuyente </label>
                                                                                            <div class="col-md-8">
                                                                                                    <select name="tipo_contribuyente" id="tipo_contribuyente" class="form-control select2me">
                                                                                                            <?php
                                                                                                                    if(!isset($integrante->tipo_contribuyente)) 
                                                                                                                            echo "<option value='0'>Seleccione</option>";

                                                                                                                    foreach ($data_contribuyente as $tipo_contribuyente) 
                                                                                                                    {
                                                                                                                            if(isset($integrante) && $integrante->tipo_contribuyente==$tipo_contribuyente)
                                                                                                                                    echo "<option value='".$tipo_contribuyente."' selected>".$tipo_contribuyente."</option>";
                                                                                                                            else
                                                                                                                                    echo "<option value='".$tipo_contribuyente."'>".$tipo_contribuyente."</option>";
                                                                                                                    }
                                                                                                            ?>
                                                                                                    </select>
                                                                                            </div>

                                                                                    </div>
                                                                                    <div class="form-group">	
                                                                                        <label class="control-label col-md-3">Carrera Legislativa</label>                                                                                        
                                                                                        <div class="col-md-8">                                                                                            
                                                                                            <input type="checkbox" name="carrera_legislativa" id="carrera_legislativa" value="1" <? if ($integrante->carrera_legislativa=='1') echo "checked='true'"?>/>        
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="form-group">
                                                                                        <label class="col-md-3">Sabe Leer</label>
                                                                                        <div class="col-md-8">
                                                                                            <input type="checkbox" name="sabe_leer" id="sabe_leer" value="1" <? if ($integrante->sabe_leer=='1' or $operacion == 'agregar') echo "checked='true'"?>/>        

                                                                                        </div>
                                                                                    </div>
                                                                                     <div class="form-group">
                                                                                        <label class="col-md-3">Sabe Escribir</label>
                                                                                        <div class="col-md-8">
                                                                                            <input type="checkbox" name="sabe_escribir" id="sabe_escribir" value="1" <? if ($integrante->sabe_escribir=='1'  or $operacion == 'agregar') echo "checked='true'"?>/>        

                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="form-group">
                                                                                            <label class="control-label col-md-3">Nivel Educativo</label>
                                                                                            <div class="col-md-8">	
                                                                                                <select id="nivel_educativo" name="nivel_educativo" class="form-control select2me">
                                                                                                    <option value="">Seleccione ...</option>
                                                                                                    
                                                                                                    <?php
                                                                                                            $sql = "SELECT IdNivelEducativo,Descripcion FROM niveleducativo ORDER BY Descripcion ASC";
                                                                                                            $res = $db->query($sql);
                                                                                                            
                                                                                                            while($fila = $res->fetch_assoc())
                                                                                                            { 
                                                                                                                $select="";
                                                                                                                if(isset($integrante) && $integrante->IdNivelEducativo==$fila['IdNivelEducativo'])
                                                                                                                    $select="selected";
                                                                                                                else
                                                                                                                    $select="";                                                                                                                        
                                                                                                                ?>               
                                                                                                    
                                                                                                                    <option value="<?=$fila['IdNivelEducativo'];?>" <?=$select?>><?=utf8_decode($fila['Descripcion']);?></option>
                                                                                                    <?php }?>                                                                                                                                                                                                                       
                                                                                                </select>
                                                                                            </div>
                                                                                    </div>
                                                                                    
                                                                                    <div class="form-group">
                                                                                            <label class="control-label col-md-3">Profesi&oacute;n <?php if($empresa->tipo_empresa != 1){ ?><span class="required">*</span><?php } ?></label>
                                                                                            <div class="col-md-8">
                                                                                                    <?php 
                                                                                                            $sql_profesion = "SELECT codorg, descrip FROM nomprofesiones ORDER BY descrip ASC";

                                                                                                            $res_profesion = $db->query($sql_profesion);
                                                                                                    ?>
                                                                                                    <select name="codpro" id="codpro" class="form-control select2me">
                                                                                                        <option value="">Seleccione ...</option>
                                                                                                            <?php
                                                                                                                    if($operacion=='agregar')
                                                                                                                            echo "<option value=''>Seleccione</option>";

                                                                                                                    while($fila_profesion = $res_profesion->fetch_assoc())
                                                                                                                    {
                                                                                                                            if(isset($integrante) && $integrante->codpro==$fila_profesion['codorg'])
                                                                                                                                    echo "<option value='".$fila_profesion['codorg']."' selected>".$fila_profesion['descrip']."</option>";
                                                                                                                            else
                                                                                                                                    echo "<option value='".$fila_profesion['codorg']."'>".$fila_profesion['descrip']."</option>";
                                                                                                                    }
                                                                                                            ?>
                                                                                                    </select>
                                                                                            </div>
                                                                                    </div>
                                                                                    
                                                                                    <div class="form-group">
                                                                                            <label class="control-label col-md-3">Título <?php if($empresa->tipo_empresa != 1){ ?><?php } ?></label>
                                                                                            <div class="col-md-8">
                                                                                                    <?php 
                                                                                                            $sql_titulo = "SELECT codorg, descrip FROM nomprofesiones ORDER BY descrip ASC";

                                                                                                            $res_titulo = $db->query($sql_titulo);
                                                                                                    ?>
                                                                                                    <select name="titulo_profesional" id="titulo_profesional" class="form-control select2me">
                                                                                                        <option value="">Seleccione ...</option>
                                                                                                            <?php
                                                                                                                    if($operacion=='agregar')
                                                                                                                            echo "<option value=''>Seleccione</option>";

                                                                                                                    while($fila_titulo = $res_titulo->fetch_assoc())
                                                                                                                    {
                                                                                                                            if(isset($integrante) && $integrante->titulo_profesional==$fila_titulo['codorg'])
                                                                                                                                    echo "<option value='".$fila_titulo['codorg']."' selected>".$fila_titulo['descrip']."</option>";
                                                                                                                            else
                                                                                                                                    echo "<option value='".$fila_titulo['codorg']."'>".$fila_titulo['descrip']."</option>";
                                                                                                                    }
                                                                                                            ?>
                                                                                                    </select>
                                                                                            </div>
                                                                                    </div>
                                                                                    
                                                                                     <div class="form-group">
                                                                                            <label class="control-label col-md-3">Institución <?php if($empresa->tipo_empresa != 1){ ?><?php } ?></label>
                                                                                            <div class="col-md-8">
                                                                                                    <?php 
                                                                                                            $sql_institucion = "SELECT id_institucion, nombre FROM institucion_educativa ORDER BY nombre ASC";

                                                                                                            $res_institucion = $db->query($sql_institucion);
                                                                                                    ?>
                                                                                                    <select name="institucion_educativa" id="institucion_educativa" class="form-control select2me">
                                                                                                         <option value="">Seleccione ...</option>   
                                                                                                        <?php
                                                                                                                    if($operacion=='agregar')
                                                                                                                            echo "<option value=''>Seleccione una profesión</option>";

                                                                                                                    while($fila = $res_institucion->fetch_assoc())
                                                                                                                    {
                                                                                                                            if(isset($integrante) && $integrante->institucion==$fila['id_institucion'])
                                                                                                                                    echo "<option value='".$fila['id_institucion']."' selected>".$fila['nombre']."</option>";
                                                                                                                            else
                                                                                                                                    echo "<option value='".$fila['id_institucion']."'>".$fila['nombre']."</option>";
                                                                                                                    }
                                                                                                            ?>
                                                                                                    </select>
                                                                                            </div>
                                                                                    </div>                                                                                    
                                                                                    <div class=" form-group">
                                                                                        <label class="col-md-3 control-label">Hijos</label>
                                                                                        <div class="col-md-8">
                                                                                            <input class="form-control form-control-inline" type="text" name="hijos" value="<?php echo (isset($integrante->Hijos)) ? $integrante->Hijos : ''; ?>" />
                                                                                        </div>
                                                                                        
                                                                                    </div>
                                                                                    
                                                                                    <div class="form-group">	
                                                                                            <label class="control-label col-md-3">Tipo de Sangre</label>
                                                                                            <div class="col-md-8">
                                                                                                <select id="tipo_sangre" name="tipo_sangre" class="form-control select2me">
                                                                                                    <option value="">Seleccione ...</option>
                                                                                                    <?php
                                                                                                            $sql = "SELECT 	IdTipoSangre,Descripcion FROM tiposangre ORDER BY Descripcion ASC";
                                                                                                            $res = $db->query($sql);
                                                                                                            while($fila = $res->fetch_assoc())
                                                                                                            {
                                                                                                                $select="";
                                                                                                                if(isset($integrante) && $integrante->IdTipoSangre==$fila['IdTipoSangre'])
                                                                                                                    $select="selected";
                                                                                                                else
                                                                                                                    $select="";                                                                                                                        
                                                                                                                ?>                                                                                                                                                                                                                                           
                                                                                                                    <option value="<?= $fila['IdTipoSangre'];?>" <?=$select?>><?= $fila['Descripcion'];?></option>
                                                                                                    <?php }?>	
                                                                                                </select>
                                                                                                
                                                                                            </div>
                                                                                    </div>
                                                                                    <div class="form-group">
                                                                                            <label class="col-md-3">Estatura</label>
                                                                                            <div class="col-md-8"><input name="estatura" class="form-control form-control-inline" value="<?php echo (isset($integrante->estatura)) ? $integrante->estatura : ''; ?>"/></div>
                                                                                    </div>
                                                                                    <div class="form-group">
                                                                                            <label class="col-md-3">Peso</label>
                                                                                            <div class="col-md-8"><input name="peso" class="form-control form-control-inline" value="<?php echo (isset($integrante->peso)) ? $integrante->peso : ''; ?>"/></div>
                                                                                    </div>

                                                                                    <div class="form-group">
                                                                                            <label class="col-md-3">Enfermedades y Alergias</label>
                                                                                            <div class="col-md-8"><input name="enfermedades_alergias" class="form-control form-control-inline" value="<?php echo (isset($integrante->EnfermedadesYAlergias)) ? $integrante->EnfermedadesYAlergias : ''; ?>"/></div>
                                                                                    </div>
                                                                                    
                                                                                    <div class="form-group">
                                                                                            <label class="col-md-3">¿Tiene Discapacidad?</label>
                                                                                            <div class="col-md-8">
                                                                                                
                                                                                                
                                                                                                <?php
														$discapacidad2 = 'checked';													
                                                                                                                $discapacidad1 = '';
														if(isset($integrante))
														{
															$discapacidad1 = ($integrante->tiene_discapacidad=='1')  ? 'checked' : '';
															$discapacidad2 = ($integrante->tiene_discapacidad=='2')  ? 'checked' : '';															
														} 
                                                                                                ?>
                                                                                                
                                                                                                    <div class="radio-list">
                                                                                                            <label class="radio-inline">
                                                                                                            <input type="radio" name="discapacidad" id="discapacidad1" value="1" <?=$discapacidad1?>> Si</label>
                                                                                                            <label class="radio-inline">
                                                                                                            <input type="radio" name="discapacidad" id="discapacidad2" value="2" <?=$discapacidad2?>> No</label>

                                                                                                    </div>
                                                                                            </div>
                                                                                    </div>

                                                                                    <div class="form-group">
                                                                                            <label class="col-md-3">¿Tiene Familiares con Discapacidad?</label>
                                                                                            <div class="col-md-8">
                                                                                                    <?php
														$familiar_discapacidad2 = 'checked';													
                                                                                                                $familiar_discapacidad1 = '';
														if(isset($integrante))
														{
															$familiar_discapacidad1 = ($integrante->tiene_familiar_disca=='1')  ? 'checked' : '';
															$familiar_discapacidad2 = ($integrante->tiene_familiar_disca=='2')  ? 'checked' : '';															
														} 
                                                                                                    ?>
                                                                                                    <div class="radio-list">
                                                                                                            <label class="radio-inline">
                                                                                                            <input type="radio" name="fam_discapacidad" id="fam_discapacidad1" value="1" <?=$familiar_discapacidad1?>> Si</label>
                                                                                                            <label class="radio-inline">
                                                                                                            <input type="radio" name="fam_discapacidad" id="fam_discapacidad2" value="2" <?=$familiar_discapacidad2?>> No</label>

                                                                                                    </div>
                                                                                            </div>
                                                                                    </div>
                                                                                    <div class="form-group">
                                                                                            <label class="col-md-3">Marca Reloj</label>
                                                                                            <div class="col-md-8">
                                                                                                <input type="checkbox" name="marca_reloj" id="marca_reloj" value="1" <? if ($integrante->marca_reloj=='1' or $operacion == 'agregar') echo "checked='true'"?>/>        
                                                                                            
                                                                                            </div>
                                                                                    </div>
                                                                                    <div class="form-group">
                                                                                            <label class="col-md-3">Código Diputado</label>
                                                                                            <div class="col-md-8"><input name="codigo_diputado" id="codigo_diputado" class="form-control form-control-inline" value="<?php echo (isset($integrante->codigo_diputado)) ? $integrante->codigo_diputado : ''; ?>"/></div>
                                                                                    </div>
                                                                                    <div class="form-group">
                                                                                            <label class="col-md-3">Piso</label>
                                                                                            <div class="col-md-8"><input name="piso" id="piso" class="form-control form-control-inline" value="<?php echo (isset($integrante->piso)) ? $integrante->piso : ''; ?>"/></div>
                                                                                    </div>

                                                                                    <div class="form-group">
                                                                                            <label class="col-md-3">Oficina</label>
                                                                                            <div class="col-md-8"><input name="oficina" id="oficina" class="form-control form-control-inline" value="<?php echo (isset($integrante->oficina)) ? $integrante->oficina : ''; ?>"/></div>
                                                                                    </div>
                                                                                    <div class="form-group">
                                                                                            <label class="control-label col-md-3">Promocion</label>
                                                                                            <div class="col-md-8">	
                                                                                                <select id="promocion" name="promocion" class="form-control select2me">
                                                                                                    <option value="">Seleccione ...</option>
                                                                                                    
                                                                                                    <?php
                                                                                                            $sql = "SELECT id,descripcion FROM promocion ORDER BY descripcion ASC";
                                                                                                            $res = $db->query($sql);
                                                                                                            
                                                                                                            while($fila = $res->fetch_assoc())
                                                                                                            { 
                                                                                                                $select="";
                                                                                                                if(isset($integrante) && $integrante->id_promo==$fila['id'])
                                                                                                                    $select="selected";
                                                                                                                else
                                                                                                                    $select="";                                                                                                                        
                                                                                                                ?>               
                                                                                                    
                                                                                                                    <option value="<?=$fila['id'];?>" <?=$select?>><?=utf8_decode($fila['descripcion']);?></option>
                                                                                                    <?php }?>                                                                                                                                                                                                                       
                                                                                                </select>
                                                                                            </div>
                                                                                    </div>
                                                                                    <div class="form-group">
                                                                                            <label class="control-label col-md-3">Fecha Jubilación</label>
                                                                                            <div class="col-md-8">
                                                                                                    <div class="input-group date date-picker" data-provide="datepicker" data-date-end-date="0d">
                                                                                                            <input type="text" class="form-control" name="fecha_jubilacion" id="fecha_jubilacion" value="<?php echo (isset($fechajubipensi)) ? $fechajubipensi : ''; ?>">
                                                                                                            <span class="input-group-btn">
                                                                                                                    <button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
                                                                                                            </span>
                                                                                                    </div>
                                                                                            </div>
                                                                                    </div>
                                                                                </div>
										
                                                                            </div>
                                                                        </div>
                                                                        
                                                        </div>

							
							<div class="tab-pane" id="tab_6">
                                                            <div class="form-body">										
                                                                <div class="row">
                                                                    <div class="col-md-9"> 
                                                                        <div class="form-group">
                                                                                <label class="col-md-3 control-label">Posici&oacute;n <span class="required">*</span></label>
                                                                                <div class="col-md-8">
                                                                                    <input type="text" name="posicion_estructura" id="posicion_estructura" class="form-control" value=""> 
                                                                                </div>
                                                                        </div>
                                                                        

                                                                        <div class="form-group">
                                                                                <label class="col-md-3 control-label">Cuenta Contable </label>
                                                                                <div class="col-md-8">
                                                                                        <input type="text" name="cuentacontable_estructura" id="cuentacontable_estructura" class="form-control" value="<?php echo isset($integrante->ctacontab) ? $integrante->ctacontab : ''; ?>" >
                                                                                        </div>
                                                                        </div>
                                                                        <div class="form-group">
                                                                                <label class="col-md-3 control-label">Planilla <span class="required">*</span> </label>
                                                                                <div class="col-md-8">
                                                                                        <input type="text" name="planilla_estructura" id="planilla_estructura" class="form-control">
                                                                                        </div>
                                                                        </div>
                                                                        <div class="form-group">
                                                                                <label class="col-md-3 control-label">Departamento </label>
                                                                                <div class="col-md-8">
                                                                                        <select id="departamento_estructura" name="departamento_estructura" class="form-control select2me">
                                                                                            <option value="">Seleccione ...</option>
                                                                                        <?php
                                                                                        $sql = "SELECT 	IdDepartamento,Descripcion FROM departamento ORDER BY Descripcion ASC";
                                                                                        $res = $db->query($sql);

                                                                                        $sql2   = "SELECT b.IdDepartamento,b.Descripcion
                                                                                                                                FROM posicionempleado a, departamento b
                                                                                                                                WHERE b.IdDepartamento=a.IdDepartamento AND a.Posicion='".$nomposicion_id."'";
                                                                                        $res2   = $db->query($sql2);
                                                                                    $fila2 = $res2->fetch_assoc();?>
                                                                                        <option value="<?php echo $fila2['IdDepartamento'];?>"><?php echo $fila2['Descripcion'];?></option>
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

                                                                    <?php
                                                                        $sql = "SELECT 	IdTipoEmpleado,Descripcion FROM tipoempleado ORDER BY Descripcion ASC";
                                                                        $res = $db->query($sql);

                                                        ?>
                                                                        <div class="form-group">
                                                                                <label class="col-md-3 control-label">Tipo Empleado <span class="required">*</span> </label>
                                                                                <div class="col-md-8">
                                                                                    
                                                                                    <select id="tipoempleado_estructura" name="tipoempleado_estructura" class="form-control select2me" onchange="obtenerNombreTipoEmpleado()">
                                                                                                <option value="">Seleccione ...</option>
                                                        <?php
                                                                                            $posicionSelecionada='';  
                                                                                        while($fila = $res->fetch_assoc())
                                                                                        {
                                                                                             //$select="";
                                                                                                                if(isset($integrante) && $integrante->tipo_funcionario==$fila['IdTipoEmpleado']){
                                                                                                                    $select="selected";
                                                                                                                    $posicionSelecionada=$fila['Descripcion'];                                                                                                                
                                                                                                                }
                                                                                                                else{
                                                                                                                    $select="";   
                                                                                                                                                                                                                                     
                                                                                                                }
                                                                                                                ?>
                                                                                                <option  value="<?php echo $fila['IdTipoEmpleado'];?>" <?=$select;?>><?php echo $fila['Descripcion'];?>
                                                                                                </option>							<?php			}	?>
                                                                                                
                                                                                    </select>
                                                                                    <input type="hidden" name="f_tipoempleado_estructura" id="f_tipoempleado_estructura" value="<?=$posicionSelecionada;?>">
                                                                        </div>
                                                                    </div>

                                                                        <div class="form-group">
                                                                        <?php
                                                                                $titular = 'checked';
                                                                                $interino = '';
                                                                                $transitorio = '';
                                                                                if(isset($integrante))
                                                                                {
                                                                                        $titular = ($integrante->tipo_empleado=='Titular') ? 'checked' : '';
                                                                                        $interino = ($integrante->tipo_empleado=='Interino')  ? 'checked' : '';
                                                                                        $transitorio = ($integrante->tipo_empleado=='Transitorio')  ? 'checked' : '';	
                                                                                } 
                                                                        ?>		
                                                                                <label class="col-md-3">Titular/Interino</label>
                                                                                <div class="col-md-8">
                                                                                        <div class="radio-list">
                                                                                                <label class="radio-inline">
                                                                                                <input type="radio" name="tipo_empleado" id="titular" value="Titular" <?php echo $titular;?>>Titular</label> 
                                                                                                <label class="radio-inline"> 
                                                                                                <input type="radio" name="tipo_empleado" id="interino" value="Interino" <?php echo $interino;?>>Interino</label>
                                                                                                <label class="radio-inline"> 
                                                                                                <input type="radio" name="tipo_empleado" id="transitorio" value="Transitorio" <?php echo $transitorio;?>>Transitorio</label>
                                                                                        
                                                                                        </div>
                                                                                </div>
                                                                        </div>

                                                                        <div class="form-group">
                                                                                <label class="col-md-3 control-label" >Cargo <span class="required">*</span> </label>
                                                                                <div class="col-md-8">
                                                                                        <?php 
                                                                                                $sql = "SELECT cod_car, des_car FROM nomcargos";
                                                                                                $res = $db->query($sql);
                                                                                        ?>
                                                                                        <select name="cargo_estructura" id="cargo_estructura" class="form-control select2me">

                                                                                                <option value=''>Seleccione un cargo</option>
                                                                                                <?php
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
                                                                        </div>

                                                                        <?php 
                                                                                $sql = "SELECT 	nomfuncion_id,descripcion_funcion FROM `nomfuncion` ORDER BY descripcion_funcion ASC";
                                                                                $res = $db->query($sql);
                                                                                ?>


                                                                                <div class="form-group">
                                                                                        <label class="col-md-3 control-label">Función <span class="required">*</span></label>
                                                                                        <div class="col-md-8">
                                                                                                <select id="funcion_estructura" name="funcion_estructura" class="form-control select2me">
                                                                                                <option value="">Seleccione ...</option>
                                                                                                <?php

                                                                                                while($fila = $res->fetch_assoc())
                                                                                                {
                                                                                                     $select="";
                                                                                                      if(isset($integrante) && $integrante->nomfuncion_id==$fila['nomfuncion_id'])
                                                                                                            $select="selected";
                                                                                                      else
                                                                                                            $select="";
                                                                                                      echo '<option value="'.$fila['nomfuncion_id'].'" '.$select.'>'.$fila['descripcion_funcion'].'</option>'; 
                                                                                                }	
                                                                                            ?>
                                                                                            </select>
                                                                                    </div>
                                                                                </div>
                                                                                
                                                                                <div class="form-group">
                                                                                        <label class="col-md-3 control-label" >Grado / Etapa </label>
                                                                                        <div class="col-md-8">
                                                                                                <?php 
                                                                                                        $sql = "SELECT ge.id_grado_etapa as id_grado_etapa, ge.monto as monto, g.descripcion as desc_grado, g.ajuste as ajuste, e.descripcion as desc_etapa, e.minimo as minimo, e.maximo as maximo"
                                                                                                                . " FROM grado_etapa AS ge, grado as g, etapa as e"
                                                                                                                . " WHERE ge.grado=g.id_grado AND ge.etapa=e.id_etapa";
                                                                                                        $res = $db->query($sql);
                                                                                                ?>
                                                                                                <select name="grado_etapa" id="grado_etapa" class="form-control select2me">

                                                                                                        <option value=''>Seleccione Grado / Etapa</option>
                                                                                                        <?php
                                                                                                                while($fila = $res->fetch_assoc())
                                                                                                                {
                                                                                                                        if(isset($integrante) && $integrante->paso==$fila['id_grado_etapa'])
                                                                                                                                echo "<option value='".$fila['id_grado_etapa']."' selected>".$fila['desc_grado']." / ".$fila['desc_etapa']." / AÑOS SERVICIO: ( ".$fila['minimo']." - ".$fila['maximo']." ) / MONTO: ".$fila['monto']."</option>";
                                                                                                                        else
                                                                                                                                echo "<option value='".$fila['id_grado_etapa']."'>".$fila['desc_grado']." / ".$fila['desc_etapa']." / AÑOS SERVICIO: ( ".$fila['minimo']." - ".$fila['maximo']." ) / MONTO: ".$fila['monto']."</option>";
                                                                                                                }
                                                                                                        ?>
                                                                                                </select>
                                                                                        </div>
                                                                                </div>

                                                                                <div class="form-group">
                                                                                        <label class="col-md-3 control-label">Salario </label>
                                                                                                <div class="col-md-8">
                                                                                                    <input type="text" name="salario_estructura" id="salario_estructura" class="form-control" value="<?php echo (isset($integrante) && $integrante->suesal) ? $integrante->suesal : 0.00; ?>" /> 
                                                                                                </div>
                                                                                </div>

                                                                                <div class="form-group">
                                                                                        <label class="col-md-3 control-label">Gastos Representacion</label>
                                                                                        <div class="col-md-8"><input type="text" name="gr_estructura" id="gr_estructura" class="form-control" value="<?php echo (isset($integrante) && $integrante->gastos_representacion) ? $integrante->gastos_representacion : 0.00; ?>"></div>
                                                                                </div>
                                                                                <div class="form-group">
                                                                                        <label class="col-md-3 control-label">Sobresueldo </label>
                                                                                        <div class="col-md-8"><input type="text" name="sobresueldo_estructura" id="sobresueldo_estructura" class="form-control" value="<?php echo (isset($integrante) && $integrante->otros) ? $integrante->otros : 0.00; ?>"></div>
                                                                                </div>
                                                                                <div class="form-group">
                                                                                        <label class="col-md-3 control-label">Dieta </label>
                                                                                        <div class="col-md-8"><input type="text" name="dieta_estructura" id="dieta_estructura" class="form-control" value="<?php echo (isset($integrante) && $integrante->dieta) ? $integrante->dieta : 0.00; ?>"></div>
                                                                                </div>
                                                                                <div class="form-group">
                                                                                        <label class="col-md-3 control-label">Combustible </label>
                                                                                        <div class="col-md-8"><input type="text" name="combustible_estructura" id="combustible_estructura" class="form-control" value="<?php echo (isset($integrante) && $integrante->combustible) ? $integrante->combustible : 0.00; ?>"></div>
                                                                                </div>
                                                                                <div class="form-group">
                                                                                        <label class="col-md-3 control-label">Fecha Inicio <span class="required">*</span></label>
                                                                                        <div class="col-md-8">
                                                                                                <div class="input-group date date-picker" data-provide="datepicker" data-date-end-date="0d">	
                                                                                                        <input type="text" class="form-control" name="fechainicio_estructura" id="fechainicio_estructura" value="<?php echo (isset($inicio_periodo)) ? $inicio_periodo : ''; ?>">
                                                                                                        <span class="input-group-btn">
                                                                                                                <button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
                                                                                                        </span>
                                                                                                </div>
                                                                                        </div>
                                                                                </div>


                                                                        <?php
                                                                        for($i=1; $i<=7; $i++)
                                                                        {
                                                                                // $display_nivel1 = ($empresa->nivel1 != "1") ? 'display: none' : 'display: block';
                                                                                if($empresa->{"nivel".$i} == 1)
                                                                                {
                                                                                echo '<div id="nivel'.$i.'">'; 
                                                                                        echo '<div class="form-group ">
                                                                                                        <label class="col-md-3 control-label">';
                                                                                         echo $empresa->{"nomniv".$i};
                                                                                         if($i==1){
                                                                                             $required='<span class="required">*</span>';
                                                                                         }
                                                                                         else{$required='';}
                                                                                         echo ' '.$required.'</label>';
                                                                                                echo '<div class="col-md-8">';

                                                                                                        $sql = "SELECT codorg, CONCAT_WS(' ', codorg, descrip, markar) as descrip 
                                                                                                                                FROM   nomnivel".$i."";
                                                                                                                if($i>1)
                                                                                                                {
                                                                                                                        $nivel_anterior = "codnivel".($i-1);
                                                                                                                        $gerencia = isset($integrante->{$nivel_anterior}) ? $integrante->{$nivel_anterior} : '' ;
                                                                                                                        //$sql .= " WHERE gerencia='".$gerencia."' ";
                                                                                                                }
                                                                                                        //echo $sql,"<br>";
                                                                                                        $res = $db->query($sql);
                                                                                                echo '	<select name="codnivel'.$i.'" id="codnivel'.$i.'" class="form-control form-control-inline input-medium select2me">';
                                                                                                                if($operacion=='agregar' || $res->num_rows==0 || (isset($integrante->{"codnivel".$i}) && $integrante->{"codnivel".$i}==0))
                                                                                                                        echo "<option value=''>Seleccione ".$empresa->{"nomniv".$i}."</option>";

                                                                                                                while($fila = $res->fetch_assoc())
                                                                                                                {
                                                                                                                        if(isset($integrante) && $integrante->{"codnivel".$i}==$fila['codorg'])
                                                                                                                                echo "<option value='".$fila['codorg']."' selected>".$fila['descrip']."</option>";
                                                                                                                        else
                                                                                                                                echo "<option value='".$fila['codorg']."'>".$fila['descrip']."</option>";
                                                                                                                }
                                                                                                echo '	</select>
                                                                                                        </div><br>
                                                                                                </div>
                                                                                        </div>
                                                                                        ';


                                                                                }
                                                                        }

                                        echo '<div class="form-group">
                                        <label class="col-md-3 control-label">Categoría <span class="required">*</span> </label>
                                                                <div class="col-md-8">';
                                                                                $sql = "SELECT codorg, descrip FROM nomcategorias";
                                                                                $res = $db->query($sql);
                                        echo '<select name="codcat" id="codcat" class="form-control form-control-inline input-medium select2me">';
                                                                                        //if($operacion=='agregar')
                                                                                        echo "<option value=''>Seleccione una categor&iacute;a</option>";

                                                                                        while($fila = $res->fetch_assoc())
                                                                                        {
                                                                                                if(isset($integrante) && $integrante->codcat==$fila['codorg'])
                                                                                                        echo "<option value='".$fila['codorg']."' selected>".$fila['descrip']."</option>";
                                                                                                else
                                                                                                        echo "<option value='".$fila['codorg']."'>".$fila['descrip']."</option>";
                                                                                        }
                                        echo '				</select>
                                                                </div>
                                                        </div>';?>
                                                                        
                                                                    </div>
                                                                </div>
                                                            </div>	
                                                            
                                                            
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
                                                                        <div class="tab-pane"  id="tab_7"><iframe id="iframe_tab_7" src="" frameborder="0" scrolling="0" onload="resizeIframe(this);"></iframe></div>
                                                                        <div class="tab-pane"  id="tab_8"><iframe id="iframe_tab_8" src="" frameborder="0" scrolling="0" onload="resizeIframe(this);"></iframe></div>

                                                                        <div class="tab-pane"  id="tab_9"><iframe id="iframe_tab_9" src="" frameborder="0" scrolling="0" onload="resizeIframe(this);"></iframe></div>
                                                                <?php
							}
							?>
                                                    
						</div>
					</div>
                                        <div class="form-actions fluid">
                                            <div class="col-md-offset-4 col-md-8">
                                                <button type="submit" class="btn blue" id="btn-guardar" name="btn-guardar">Guardar</button>
						<button type="button" class="btn default" onclick="javascript: document.location.href='<?php echo isset($_GET['back_listado_integrantes']) ? 'datos_integrantes/listado_integrantes_contraloria.php' : 'datos_integrantes/listado_integrantes_contraloria.php'; ?>'">Cancelar</button>
                                            </div>
					</div>
                                    </form>
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
    //var GLOBAL_CTACONTABLE=0;
   
$(document).ready(function(){
	//Otros Datos
        

	$("#apruebasolicitud").hide();
	$("#institucion").hide();
	//$("#nomposicion_id").change(function(){buscarPosicionDisponible();mostrarCargo(); });
	$("#solicitud2").on('click',function(){
		$("#apruebasolicitud").hide();
                $("#apruebasolicitud").empty();
	});
	$("#personalesterno2").on('click',function(){
		$("#institucion").hide();
	});
        
	$("#solicitud1").on('click',function(){
            //alert($("#solicitud1").prop('checked')+' '+$("#solicitud2").prop('checked'));  
                $("#apruebasolicitud").empty();
                $("#apruebasolicitud").append('Cargando...');
                $("#apruebasolicitud").show();
		$.get("datos_integrantes/ajax/listarUsuarioSolicitud.php",function(result){
			$("#apruebasolicitud").empty();
			$("#apruebasolicitud").show();
			$("#apruebasolicitud").append(result);
		});
	});
        
        $("#personalexterno1").on('click',function(){
            //alert($("#solicitud1").prop('checked')+' '+$("#solicitud2").prop('checked'));   
            $("#personaexterna").empty();
            $("#personaexterna").append('Cargando...');
            $("#apruebasolicitud").show();
		$.get("datos_integrantes/ajax/listarPersonalExterno.php",function(result){
			$("#personaexterna").empty();
			$("#personaexterna").show();
			$("#personaexterna").append(result);
		});
	});
        
        $("#personalexterno2").on('click',function(){                   		
            $("#personaexterna").empty();
            $("#personaexterna").hide();			
		
	});
        
        
        
        
	$("#personalesterno1").change(function(){
		$.get("datos_integrantes/ajax/listarPersonalExterno.php",function(result){
			$("#institucion").empty();
			$("#institucion").show();
			$("#institucion").append(result);
		});
	});
        
        $("#codcargo").select2();
        $("#tipnom").select2();        
	//cargar_estructura();



	//niveleducativo();
	/*profesiones();
	nacionalidad();
	condicion();*/
	$("#nombres").keyup(function(){
                //alert('nombres');
		nombre   = $("#nombres").val();
		apellido = $("#apellidos").val();                                                                               
                
		if($('#nombres').val().trim() === '')
		{
			usuario  = nombre+"."+apellido;			
                        usuario=usuario.replace(/\s/g, "");
                        
                        $("#usuario").val(usuario);
                        
		}
	});

	$("#apellidos").keyup(function(){
		nombre   = $("#nombres").val();
		apellido = $("#apellidos").val();
		if(apellido != " ")
		{
			usuario  = nombre+"."+apellido;
                        usuario=usuario.replace(/\s/g, "");
			$("#usuario").val(usuario);
                        checkUsuarioWorkflow();
		}
	});
        
        
        $("#usuario").keyup(function(){		
            checkUsuarioWorkflow();		
	});
        
        
        
        $("#gastos_repre").keyup(function(){
            //alert('asdasd');
             var gastos = $("#gastos_repre").val();
                 $("#gr_estructura").val(gastos);
        });
        
                   
        
        
        $("#suesal").keypress(function(e){
            var key = window.Event ? e.which : e.keyCode
            //alert(key);
            return (key >= 48 && key <= 57 || (key==8 || key==46 || key==44));                
        });
        
               
        $("#otros").keypress(function(e){
            var key = window.Event ? e.which : e.keyCode
            //alert(key);
            return (key >= 48 && key <= 57 || (key==8 || key==46 || key==44));                
        });
        
        $("#gastos_repre").keypress(function(e){
            var key = window.Event ? e.which : e.keyCode
            //alert(key);
            return (key >= 48 && key <= 57 || (key==8 || key==46 || key==44));                
        });
        
        
        
	$("#suesal").keyup(function(){
		nomposicion_id=$("#nomposicion_id").val();
		$.get("datos_integrantes/ajax/validarSalario.php",{nomposicion_id:nomposicion_id},function(resultado){
			if (resultado) {
				suesal  = $("#suesal").val();                               
                                suesal1 = parseFloat(suesal);                                                                 
                                suesal2 = parseFloat(resultado);                                
                                //alert(suesal1+'>'+suesal2);                                                                
				if (suesal1>suesal2) {$("#suesal").val(resultado);
				alert("Salario no puede ser mayor a "+resultado);
				}
                                else{
                                    var sueldo = $("#suesal").val();
                                    $("#salario_estructura").val(sueldo);
                                }
			}                        
		});
	});
        
        $("#nomposicion_id").bind('keyup mouseup', function () {
            if($("#nomposicion_id").val()!='')
            {
                buscarPosicionDisponible();
                mostrarSalario();
                buscarCargo();
                mostrarPosicion();
                //cargarEstructura();    
            }
               
        });

	/*$("#nomposicion_id").keyup(function(){
		buscarPosicionDisponible();
		mostrarSalario();
		buscarCargo();
		mostrarPosicion();
                //cargarEstructura();

	});*/
        
	/*$("#select_cargo").change(function(){
		buscarCargoDisponible();
		mostrarCargo();

	});*/
        
        
        
        $("#form-integrantes").submit(function(){
                        data= $(this).serialize();
                        //alert(data);
        });
        
        $("#codcargo").change(function(){
            cargarEstructura();
        });
        
        cargarUsuarioAprueba();
});     
        function validarDisponibilidad(){
            var disponible= $('#hid_posicion_disponible').val();
            posicionActual = $('#nomposicion_id_actual').val();
            posicion = $('#nomposicion_id').val();
            if(posicionActual != posicion)                
            {
                // alert(disponible);
                if (disponible == null || disponible == "" || disponible == '0') {
                    alert('Error. La posición no está disponible.\nPor favor verifique');
                    return false;
                }
            }
            else{
                //alert('ahora si');
                return true;
            }
                
        }
        
        function cargarEstructura()
        {
            var ficha = $("#tipnom").val();
            var nomposicion_id = $("#nomposicion_id").val();
            var cargo = $("#codcargo").val();
            
           // alert(cargo);
            
            $('#posicion_estructura').val(nomposicion_id); 
            $('#posicion_estructura').prop("readonly", true);
             
            partida = $('#hid_partida').val();
            //alert(partida+' '+GLOBAL_CTACONTABLE);
            //if(GLOBAL_CTACONTABLE==0){//
                if(partida!='0'){
                    //$('#cuentacontable_estructura').empty();                             
                    $('#cuentacontable_estructura').val(partida);
                    //$('#cuentacontable_estructura').prop("readonly", true);
                }
                else{
                    //$('#cuentacontable_estructura').empty();
                    $('#cuentacontable_estructura').val('');
                    //$('#cuentacontable_estructura').prop("readonly", false);
                }
            //}
                                    
            //$('#cuentacontable_estructura').prop("readonly", true);
            
            
            
            
            
             
            $('#planilla_estructura').val(ficha);
            $('#planilla_estructura').prop("readonly", true);
            
            $('#cargo_estructura').val(cargo);
            $('#cargo_estructura').prop("readonly", true);
            
            //$('#cargo_estructura').select2('VAL',cargo);    
            $("#cargo_estructura").val(cargo).trigger('change');             
            $("#cargo_estructura").prop("readonly", true);  
            //GLOBAL_CTACONTABLE=0;
        }
        
        
      
	function mostrarPosicion()
	{
		ficha = $("#ficha").val();
		nomposicion_id = $("#nomposicion_id").val();
		$.get("datos_integrantes/ajax/mostraPosicion.php",{ficha:ficha,nomposicion_id:nomposicion_id},function(result){
			$("#posicion_estructura").empty();
			$("#posicion_estructura").append(result);
                        buscarCargo();
		});
	}
	function mostrarCuentaContable()
	{
		ficha = $("#ficha").val();
		nomposicion_id = $("#nomposicion_id").val();
		$.get("datos_integrantes/ajax/mostraCuentaContable.php",{ficha:ficha,nomposicion_id:nomposicion_id},function(result){
			$("#cuentacontable_estructura").empty();
			$("#cuentacontable_estructura").append(result);
		});
	}
	function mostrarPlanilla()
	{
		ficha = $("#ficha").val();
		nomposicion_id = $("#nomposicion_id").val();
		$.get("datos_integrantes/ajax/mostrarPlanilla.php",{ficha:ficha,nomposicion_id:nomposicion_id},function(result){
			$("#planilla_estructura").empty();
			$("#planilla_estructura").append(result);
		});
	}
	function mostrarDepartamento(){
		$.get("datos_integrantes/ajax/mostrarDepartamento.php",function(res){
			$("#departamento_estructura").empty();
			$("#departamento_estructura").append(res);
		});
	}
	function mostrarTipoEmpleado(){
		$.get("datos_integrantes/ajax/mostrarTipoEmpleado.php",function(res){                    
			$("#tipoempleado_estructura").empty();
			$("#tipoempleado_estructura").append(res);
		});
	}
	function buscarCargo(){
		ficha = $("#ficha").val();
		nomposicion_id = $("#nomposicion_id").val();
		$.get("datos_integrantes/ajax/buscarCargo.php",{nomposicion_id:nomposicion_id},function(res){
                    //alert(res);
			$("#codcargo").empty();
			$("#codcargo").append(res);                        
                        $("#codcargo").select2(); //set the value
                        
                        cargarEstructura();
		});
	}
	function mostrarCargo(){
		ficha = $("#ficha").val();
		nomposicion_id = $("#nomposicion_id").val();
		$.get("datos_integrantes/ajax/mostrarCargo.php",{nomposicion_id:nomposicion_id},function(res){
			$("#codcargo").empty();
			$("#codcargo").append(res);
		});
	}
	function mostrarFuncion(){
		ficha = $("#ficha").val();
		nomposicion_id = $("#nomposicion_id").val();
		$.get("datos_integrantes/ajax/mostrarFuncion.php",function(res){
			$("#funcion_estructura").empty();
			$("#funcion_estructura").append(res);
		});

	}
	function mostrarSalario(){
		ficha = $("#ficha").val();
		nomposicion_id = $("#nomposicion_id").val();

		$.get("datos_integrantes/ajax/buscarSalario.php",{ficha:ficha,nomposicion_id:nomposicion_id},function(res){
			$("#suesal").empty();
			$("#suesal").val(res);
                        
                        $('#salario_estructura').val(res);
//                        $("#salario_estructura").prop("readonly", true);
                        
		});
		
		$.get("datos_integrantes/ajax/mostrarOtros.php",{nomposicion_id:nomposicion_id},function(res){
                       // alert(res);
			$("#otros").empty();
			$("#otros").val(res);
		});
		$.get("datos_integrantes/ajax/mostrarGastosRepresentacion.php",{nomposicion_id:nomposicion_id},function(res){
			$("#gastos_repre").empty();
			$("#gastos_repre").val(res);
                        $("#gr_estructura").val(res);
                        $("#gr_estructura").prop("readonly", true);                        
		});		
	}



	function mostrarGR(){
		ficha = $("#ficha").val();
		nomposicion_id = $("#nomposicion_id").val();

		$.get("datos_integrantes/ajax/mostrarGR.php",{ficha:ficha,nomposicion_id:nomposicion_id},function(res){
			$("#gr_estructura").empty();
			$("#gr_estructura").append(res);
		});

	}
	function mostrarFechaInicio(){
		ficha = $("#ficha").val();
		nomposicion_id = $("#nomposicion_id").val();

		$.get("datos_integrantes/ajax/mostrarFechaInicio.php",{ficha:ficha,nomposicion_id:nomposicion_id},function(res){
			$("#fechainicio_estructura").empty();
			$("#fechainicio_estructura").append(res);
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
                                $("#btn-toggle").click(function(){
                                    $("#collapse1").collapse('toggle');
                                });
                                
                                
                                
                                //alert();
			}
		});
	}
        
        function buscarAccionFuncionario(){
		id_funcionario = $("#personal_id").val();
		//alert(id_funcionario);
		$.get("datos_integrantes/ajax/buscarAccionFuncionario.php",{id_funcionario:id_funcionario},function(resultado){
			if(resultado)
			{	
				$("#accion_funcionario").empty();
				$("#accion_funcionario").append(resultado);   
                                
                                
                                $("#btn-toggle2").click(function(){
                                    $("#collapse2").collapse('toggle');
                                });
                                //alert();
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
          
        function cambiarFecha()
        {
            var fechaIngreso=$("#fecing").val();
            $("#fecha_permanencia").val(fechaIngreso);
            $("#fechainicio_estructura").val(fechaIngreso);                                      
            //alert('asdasdasd');
        }
        
        function obtenerNombreTipoEmpleado(){
            var nomTipoEmpleado = $('#tipoempleado_estructura option:selected').text();
             $('#f_tipoempleado_estructura').val(nomTipoEmpleado);     
        }
        
        function checkUsuarioWorkflow(){
            var usuario = $('#usuario').val();
            $.get("datos_integrantes/ajax/checkUsuarioWorkflow.php",{usuario:usuario},function(resultado){
			if(resultado)
			{	
				$("#d_usuario").empty();
				$("#d_usuario").append(resultado);
			}
            });
        }
        
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
          
        function ValidarCedula(){
		cedula = $("#cedula").val();
		//alert(cedula);
		$.get("datos_integrantes/ajax/ValidarCedula.php",{cedula:cedula},function(resultado){
			if (resultado) {
				                                                       
                                validar = parseInt(resultado);                                
                                //alert(suesal1+'>'+suesal2);                                                                
				if (validar==1)
                                {
                                    
                                    alert("Funcionario ya existe en estado Egresado o De Baja. \nSi desea realizar una Reincorporación diríjase a la subsección de Expediente - Movimiento Reincorporación");
                                    document.getElementById('btn-guardar').disabled = 'disabled';
                                }
                                if (validar==2)
                                {
                                    
                                    alert("Funcionario ya existe. Por favor, verifique la cédula.");
                                    document.getElementById('btn-guardar').disabled = 'disabled';
				}
                                if (validar==0)
                                {
                                    
                                    document.getElementById('btn-guardar').disabled = false;
				}
			}                        
		});
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
var USR_UID               = '<?php echo $integrante->useruid; ?>';
var PARENT                = "ag_integrantes2";
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
    
    
    
                $("#tipemp1").click(function () {
                    $("#observaciones").text('');
                });
                
    		$("#tipemp2").click(function () {
                    $("#tipemp2").attr('checked', true);
                    var fechaIni = $("#inicio_periodo").val();
                    if(fechaIni=='') fechaIni='dd/mm/aaaa';
                    
                    var fechaFin = $("#fin_periodo").val();
                    if(fechaFin=='') fechaFin='dd/mm/aaaa';
                    var observacionContrato ="PARAGRAFO:PARA LOS EFECTOS FISCALES ESTE RESUELTO TENDRA VIGENCIA A PARTIR DEL "+fechaIni+" HASTA EL "+fechaFin;
                    $("#observaciones").text(observacionContrato);
		});
 
		$("#tipemp3").click(function () {	 
			$("#tipemp3").attr('checked', true);
                        var fechaIni = $("#inicio_periodo").val();
                        if(fechaIni=='') fechaIni='dd-mm-aaaa';

                        var fechaFin = $("#fin_periodo").val();
                        if(fechaFin=='') fechaFin='dd-mm-aaaa';
                        var observacionContrato ="PARAGRAFO:PARA LOS EFECTOS FISCALES ESTE RESUELTO TENDRA VIGENCIA A PARTIR DEL "+fechaIni+" HASTA EL "+fechaFin;
                        $("#observaciones").text(observacionContrato);
		});
 
		$("#tipemp4").click(function () {	 
			$("#tipemp4").attr('checked', true);
                        var fechaIni = $("#inicio_periodo").val();
                        if(fechaIni=='') fechaIni='dd/mm/aaaa';

                        var fechaFin = $("#fin_periodo").val();
                        if(fechaFin=='') fechaFin='dd/mm/aaaa';
                        var observacionContrato ="PARAGRAFO:PARA LOS EFECTOS FISCALES ESTE RESUELTO TENDRA VIGENCIA A PARTIR DEL "+fechaIni+" HASTA EL "+fechaFin;
                        $("#observaciones").text(observacionContrato);
		});
                
                
                
                
function parrafoObservacion()
{
    var fechaIni = $("#inicio_periodo").val();
    if(fechaIni=='') fechaIni='dd/mm/aaaa';

    var fechaFin = $("#fin_periodo").val();
    if(fechaFin=='') fechaFin='dd/mm/aaaa';
    var observacionContrato ="PARAGRAFO:PARA LOS EFECTOS FISCALES ESTE RESUELTO TENDRA VIGENCIA A PARTIR DEL "+fechaIni+" HASTA EL "+fechaFin;
    $("#observaciones").text(observacionContrato);
}
    
    
    
</script>

<?  if($operacion=='editar')
                { ?>
                    <script> 
                            buscarPosicionDisponible();
                            buscarAccionFuncionario();
                            mostrarPosicion(); 
                            //mostrarSalario();

                            
                    </script>
              <?  } ?> 
              
              
              
</body>
</html>