<?php
//-------------------------------------------------
session_start();
//ob_start();
//-------------------------------------------------
require_once "config/rhexpress_config.php";
//require ('reportes/pdf/mypdf.php');
//require_once '../lib/common.php';
error_reporting(E_ALL ^ E_DEPRECATED);
//include ("func_bd.php");
require_once '../lib/common.php';
include("config/rhexpress_header2.php");
//-------------------------------------------------
$opc = ( isset($_GET['action']) ) ? $_GET['action'] : 1;
switch ($opc) {
  case '1':
    $archivo = "reportes/reportes_planilla/planilla_general.php";//config_rpt_nomina_horizontal_modelo6
    $titulo = 'Quincenal';
    $boton = "Procesar";
    break;
  case '2':
    $archivo = "reportes_planilla/reporte_descuentos.php";//config_rpt_nomina_horizontal_modelo7
    $titulo = 'Descuentos de Planilla Quincenal';
    $boton = "Procesar";
    break;  
  case '3':
    $archivo = "reportes_planilla/comprobantes.php";//config_rpt_nomina_horizontal_modelo9
    $titulo = 'Comprobantes de Pago Quincenal';
    $boton = "Procesar";
  break;
  case '4':
    $archivo = "reportes_planilla/reporte_ahorros.php";//config_rpt_nomina_horizontal_modelo9
    $titulo = 'Ahorros de empleados Quincenal';
    $boton = "Procesar";
  break;
  case '5':
    $archivo = "reportes_planilla/desglose_nomina_formapago.php";
    $titulo = 'Desglose de Nómina por Forma de Pago';
    $boton = "Procesar";
  break;
  case '6':
    $archivo = "reportes_planilla/enviar_recibo_pdf.php";//config_rpt_nomina_horizontal_modelo9
    $titulo = 'Envio de comprobantes de pago Quincenal';
    $boton = "Procesar";
  break;
  case '7':
    $archivo = "reportes/reportes_planilla/comprobante_individual.php";//individual para rhexpress
    $titulo = 'Envio de comprobantes de pago Quincenal';
    $boton = "Procesar";
  break;
}

//-------------------------------------------------------------


$SQL    = "SELECT  departamento.*,nompersonal.apenom,nompersonal.cedula FROM departamento
JOIN nompersonal on (nompersonal.cedula = departamento.IdJefe)
WHERE departamento.IdJefe = '".$_SESSION['_Jefe']."'";

$cedula      = isset($_SESSION['cedula_rhexpress'])? $_SESSION['cedula_rhexpress']: '';
$res1        = $conexion->query($SQL);
$jefe        = mysqli_fetch_assoc($res1);
//print_r($jefe);

if ($jefe['uid_jefe'] != "") 
{
	//Se verifica que el jefe tenga un superior
	$sql_subjefe = "SELECT apenom, jefe, uid_user_aprueba,cedula from nompersonal where  cedula = '{$jefe['uid_jefe']}'";
	//echo $sql_subjefe,"<br>";print_r($jefe);exit;
	$res1        = $conexion->query($sql_subjefe);
	$jefes       = mysqli_fetch_assoc($res1);
	if ($jefe['cedula']==$jefe['IdJefe']) 
	{
		$sql_director = "SELECT apenom, jefe, uid_user_aprueba,cedula from nompersonal where  cedula = '{$jefe['uid_subjefe']}'";
		//echo $sql_director,"<br>";print_r($jefe);
		$res_directos = $conexion->query($sql_director);
		$director     = mysqli_fetch_assoc($res_directos);	
		//print_r($director);exit;
	}
}
/*print_r($jefe);
print_r($jefes);*/
//echo $jefes['cedula'] ." ". $jefe['IdJefe']. " ".$_SESSION['_Jefe'] ;
if ( $cedula==$jefe['IdJefe']) 
{
	$nombre_jefe = $director['apenom'];
}
else
{
	$nombre_jefe = $jefe['apenom'];
}

$sql2        = "SELECT ifnull(ROUND( SUM( tiempo ) , 2 ),0) AS  tiempo, dias, horas, minutos
FROM dias_incapacidad
WHERE cedula = '{$cedula}' AND tipo_justificacion = 5";
$time        = $conexion->query($sql2)->fetch_assoc();
$tiempo_inc  = $time['tiempo'];
$sql_comp    = "SELECT ifnull(ROUND( SUM( tiempo ) , 2 ),0) AS  tiempo_comp, dias, horas, minutos
FROM dias_incapacidad
WHERE cedula = '{$cedula}' AND tipo_justificacion = 3";
$time_comp   = $conexion->query($sql_comp)->fetch_assoc();
$tiempo_comp = $time_comp['tiempo_comp'];
/*$horas = 8;
$dias    = intval($tiempo/$horas);
$hora    = ($tiempo - ($dias*$horas));
$minutos = ($tiempo - ($dias*$horas) - $hora);*/
//echo $tiempo," ",$dias," ",$horas," ",$minutos;
$dias    = number_format($time['dias'],2);
$horas   = number_format($time['horas'],2);
$minutos = number_format($time['minutos'],2);
$con     = (isset($_REQUEST['con'])) ? $_REQUEST['con'] : 0 ;
$sql_nomina="SELECT * FROM nom_nominas_pago;";
$planilla_nomina=$conexion->query($sql_nomina);
$valores     = mysqli_fetch_assoc($planilla_nomina);

if ($con == 1)
{
	$SQL_1             = "SELECT  * FROM  nompersonal
	WHERE cedula       = '".$_SESSION['cedula_rhexpress']."'";
	
	$persona           = $conexion->query($SQL_1)->fetch_assoc();	
	$tiempo            = (isset($_REQUEST['tiempo'])) ? $_REQUEST['tiempo'] : '' ;
	$fecha_registro    = (isset($_REQUEST['fecha'])) ? $_REQUEST['fecha'] : '' ;
	$fecha_inicio      = (isset($_REQUEST['fecha_inicio'])) ? $_REQUEST['fecha_inicio'] : '' ;
	$fecha_fin         = (isset($_REQUEST['fecha_fin'])) ? $_REQUEST['fecha_fin'] : '' ;
	$observacion       = (isset($_REQUEST['observacion'])) ? $_REQUEST['observacion'] : '' ;
	$dias              = (isset($_REQUEST['dias_solicitados'])) ? $_REQUEST['dias_solicitados'] : '' ;
	$horas             = (isset($_REQUEST['horas_solicitadas'])) ? $_REQUEST['horas_solicitadas'] : '' ;
	$minutos           = (isset($_REQUEST['minutos_solicitados'])) ? $_REQUEST['minutos_solicitados'] : '' ;
	$tiempo_solicitado = (isset($_REQUEST['total_tard_solic'])) ? $_REQUEST['total_tard_solic'] : '' ;
	$tipo_solicitud    = (isset($_REQUEST['tipo_solicitud'])) ? $_REQUEST['tipo_solicitud'] : '' ;
	$hora_inicio       = (isset($_REQUEST['hora_inicio'])) ? $_REQUEST['hora_inicio'] : '00:00:00' ;
	$hora_fin          = (isset($_REQUEST['hora_fin'])) ? $_REQUEST['hora_fin'] : '00:00:00' ;
	$fecha_ini         = date('Y-m-d H:i:s', strtotime($fecha_inicio)) ; // resta 6 mes
	$fecha_fini        = date('Y-m-d H:i:s', strtotime($fecha_fin)) ; // resta 6 mes
	//Tardanza
	//echo $tipo_solicitud;exit;
	if ($tipo_solicitud == 1) 
	{
		$tiempo_tardanza_inc  = (isset($_REQUEST['tiempo_tardanza_inc'])) ? $_REQUEST['tiempo_tardanza_inc'] : '' ;
		$tiempo_tardanza_acum = (isset($_REQUEST['tiempo_tardanza_acum'])) ? $_REQUEST['tipo_justificacion'] : '' ;
		$hora_inicia       = (isset($_REQUEST['hora_inicia'])) ? $_REQUEST['hora_inicia'] : '' ;
		$hora_termina       = (isset($_REQUEST['hora_termina'])) ? $_REQUEST['hora_termina'] : '' ;
		$fecha_tardanza       = (isset($_REQUEST['fecha_tardanza'])) ? $_REQUEST['fecha_tardanza'] : '' ;
		$fecha_tardanza_fin   = (isset($_REQUEST['fecha_tardanza_fin'])) ? $_REQUEST['fecha_tardanza_fin'] : '' ;
		$horas_tardanza       = (isset($_REQUEST['horas_tardanza'])) ? $_REQUEST['horas_tardanza'] : '' ;
		$minutos_tardanza     = (isset($_REQUEST['minutos_tardanza'])) ? $_REQUEST['minutos_tardanza'] : '' ;
		$observacion_tardanza = (isset($_REQUEST['observacion_tardanza'])) ? $_REQUEST['observacion_tardanza'] : '' ;
		$archivo_tardanza     = (isset($_REQUEST['archivo_tardanza'])) ? $_REQUEST['archivo_tardanza'] : '' ;
		$fecha_tardanza       = date('Y-m-d', strtotime($fecha_tardanza)) ; // resta 6 mes
		$hora_inicia           = date('H:i:s', strtotime($hora_inicia)) ; // resta 6 mes
		$hora_termina        = date('H:i:s', strtotime($hora_termina)) ; // resta 6 mes
		$fecha_ini            = date('Y-m-d H:i:s', strtotime($fecha_tardanza)) ; // resta 6 mes
		$fecha_fini           = date('Y-m-d H:i:s', strtotime($fecha_tardanza_fin)) ; // resta 6 mes
		//$tiempo_solicitado    = $horas_tardanza + round(($minutos_tardanza/60),2);

		$insertar = "INSERT INTO solicitudes_casos (id_solicitudes_casos, cedula, id_tipo_caso, id_departamento,  fecha_registro, fecha_inicio, fecha_fin, observacion,id_solicitudes_casos_status, tiempo,dias, horas, minutos,		fecha_tardanza,fecha_tardanza_fin,horas_tardanza,minutos_tardanza,observaciones_tardanza,archivo_tardanza,hora_inicio,hora_fin,id_tipo_solicitud)
		VALUES ('','".$_SESSION['cedula_rhexpress']."','1','".$persona['IdDepartamento']."','{$fecha_tardanza}','{$fecha_ini}','{$fecha_fini}','{$observacion}','1','{$tiempo_solicitado}','{$dias}','{$horas_tardanza}',
		'{$minutos_tardanza}','{$fecha_tardanza}','{$fecha_tardanza_fin}','{$horas_tardanza}','{$minutos_tardanza}','{$observacion_tardanza}','{$archivo_tardanza}','{$hora_inicia}','{$hora_termina}','{$tipo_solicitud}');";
	}
	//Ausencia
	elseif ($tipo_solicitud == 2) 
	{
		$tiempo_solicitado      = (isset($_REQUEST['tiempo_ausencia_inc'])) ? $_REQUEST['tiempo_ausencia_inc'] : '' ;
		$tiempo_ausencia_acum   = (isset($_REQUEST['tiempo_ausencia_acum'])) ? $_REQUEST['tipo_justificacion'] : '' ;
		$fecha_ausencia         = (isset($_REQUEST['fecha_ausencia'])) ? date('Y-m-d') : '' ;
		$horas_ausencia         = (isset($_REQUEST['horas_ausencia'])) ? $_REQUEST['horas_ausencia'] : '' ;
		$motivo_ausencia        = (isset($_REQUEST['motivo_ausencia'])) ? $_REQUEST['motivo_ausencia'] : '' ;
		$minutos_ausencia       = (isset($_REQUEST['minutos_ausencia'])) ? $_REQUEST['minutos_ausencia'] : '' ;
		$observaciones_ausencia = (isset($_REQUEST['observaciones_ausencia'])) ? $_REQUEST['observaciones_ausencia'] : '' ;		
		$archivo_ausencia       = (isset($_REQUEST['archivo_ausencia'])) ? $_REQUEST['archivo_ausencia'] : '' ;		
		$inicio_ausencia        = (isset($_REQUEST['inicio_ausencia'])) ? $_REQUEST['inicio_ausencia'] : '' ;
		$fin_ausencia           = (isset($_REQUEST['fin_ausencia'])) ? $_REQUEST['fin_ausencia'] : '' ;
		$dias_ausencia          = (isset($_REQUEST['dias_ausencia'])) ? $_REQUEST['dias_ausencia'] : '' ;
		$fecha_ausencia         = date('Y-m-d', strtotime($fecha_ausencia)) ; // resta 6 mes
		$inicio_ausencia        = date('Y-m-d H:i:s', strtotime($inicio_ausencia)) ; // resta 6 mes
		$fin_ausencia           = date('Y-m-d H:i:s', strtotime($fin_ausencia)) ; // resta 6 mes

		$insertar = "INSERT INTO solicitudes_casos (id_solicitudes_casos, cedula, id_tipo_caso, id_departamento, fecha_registro, fecha_inicio, fecha_fin, observacion,id_tipo_ausencia, id_solicitudes_casos_status, tiempo,dias, horas, minutos,		motivo_ausencia,inicio_ausencia,fin_ausencia,dias_ausencia,observaciones_ausencia,archivo_ausencia,id_tipo_solicitud,id_tipo_solicitud)
		VALUES ('','".$_SESSION['cedula_rhexpress']."','1','".$persona['IdDepartamento']."',NOW(),'{$inicio_ausencia}','{$fin_ausencia}','{$observaciones_ausencia}','{$motivo}','1','{$tiempo_solicitado}','{$dias_ausencia}','{$horas_ausencia}','{$minutos_ausencia}',
		'{$motivo_ausencia}','{$inicio_ausencia}','{$fin_ausencia}','{$dias_ausencia}','{$observaciones_ausencia}','{$archivo_ausencia}','{$tipo_solicitud}');";
		//echo $insertar;exit;
	}

	elseif ($tipo_solicitud == 3) 
	{

		$tiempo_solicitado    = (isset($_REQUEST['tiempo_permisos_inc'])) ? $_REQUEST['tiempo_permisos_inc'] : '' ;
		$tiempo_permisos_acum = (isset($_REQUEST['tiempo_permisos_acum'])) ? $_REQUEST['tiempo_permisos_acum'] : '' ;
		$fecha_permisos       = (isset($_REQUEST['fecha_permisos'])) ? $_REQUEST['fecha_permisos'] : '' ;
		$fecha_permisos_fin   = (isset($_REQUEST['fecha_permisos_fin'])) ? $_REQUEST['fecha_permisos_fin'] : '' ;
		$hora_permisos_inicio = (isset($_REQUEST['hora_permisos_inicio'])) ? $_REQUEST['hora_permisos_inicio'] : '' ;
		$hora_permisos_fin    = (isset($_REQUEST['hora_permisos_fin'])) ? $_REQUEST['hora_permisos_fin'] : '' ;
                $total_tiempo_permisos = (isset($_REQUEST['total_tiempo_permisos'])) ? $_REQUEST['total_tiempo_permisos'] : '' ;
		$dias   = (isset($_REQUEST['dias_permisos'])) ? $_REQUEST['dias_permisos'] : '' ;
                $horas  = (isset($_REQUEST['horas_permisos'])) ? $_REQUEST['horas_permisos'] : '' ;
                $minutos  = (isset($_REQUEST['minutos_permisos'])) ? $_REQUEST['minutos_permisos'] : '' ;
		$observacion_permisos = (isset($_REQUEST['observacion_permisos'])) ? $_REQUEST['observacion_permisos'] : '' ;
		$tipo_permiso         = (isset($_REQUEST['tipo_permiso'])) ? $_REQUEST['tipo_permiso'] : '' ;
		$cedula               = (isset($_SESSION['cedula_rhexpress'])) ? $_SESSION['cedula_rhexpress'] : '' ;
		$fecha_permisos       = date('Y-m-d', strtotime($fecha_permisos)) ; // resta 6 mes
		$fecha_permisos_fin   = date('Y-m-d', strtotime($fecha_permisos_fin)) ; // resta 6 mes
		$hora_permisos_inicio = date('Y-m-d H:i:s', strtotime($hora_permisos_inicio)) ; // resta 6 mes
		$hora_permisos_fin    = date('Y-m-d H:i:s', strtotime($hora_permisos_fin)) ; // resta 6 mes

		$insertar = "INSERT INTO solicitudes_casos (
		id_solicitudes_casos,
		 cedula,
		 id_tipo_caso,
		 id_departamento,
		 fecha_registro,
		 fecha_inicio,
		 fecha_fin,
		 inicio_permiso,
		 fin_permiso,
		 observacion,
		 id_solicitudes_casos_status,
		 tiempo,
		 dias,
		 horas,
		 minutos,
		 id_tipo_solicitud,
		id_tipo_permiso)
		VALUES (
		'',
		'{$cedula}',
		'1',
		'".$persona['IdDepartamento']."',
		'{$fecha_permisos}',		
		'{$fecha_permisos}',
		'{$fecha_permisos_fin}',
		'{$hora_permisos_inicio}',
		'{$hora_permisos_fin}',
		'{$observacion_permisos}',
		'1',
		'{$total_tiempo_permisos}',
		'{$dias}',
		'{$horas}',
		'{$minutos}',
		'{$tipo_solicitud}',
		'{$tipo_permiso}');";
		//echo $insertar;exit;
	}


	//echo $insertar,"<br>";exit;

	$insertado    = $conexion->query($insertar);
	$horas_dia    = 8;
	$dias         = intval($tiempo/$horas_dia);
	$hora         = ($tiempo - ($dias*$horas));
	$minutos      = ($tiempo - ($dias*$horas) - $hora) * 60;
	$tiempo_nuevo = (($dias * $horas_dia) + $hora + ($minutos / 60))*(-1);
	if ($insertado) 
  	{
    	//echo "<script>alert('SOLICITUD APROBADA');</script>";
     	echo "<script>alert('SOLICITUD DE PERMISOS GUARDADA EXITOSAMENTE');location.href='rhexpress_bandeja_entrada.php';</script>";
 	}
  	else
  	{
    	echo "<script>alert('HUBO UN ERROR AL PROCESAR SU SOLICITUD');location.href='rhexpress_bandeja_entrada.php';</script>";
    	//echo "<script>alert('SOLICITUD RECHAZADA');;</script>";
  	}

/*
	$q    = "INSERT INTO dias_incapacidad (cod_user, tipo_justificacion, fecha, tiempo, observacion,  usr_uid)
	VALUES ('".$persona['nomposicion_id']."', '3', '{$fecha_registro}', '{$tiempo_nuevo}', '{$observacion}',  '$cedula')";
	$conexion->query($q);

	$qexp ="INSERT INTO expediente (cedula, descripcion, fecha, usuario, horas, minutos, tipo,subtipo)
	VALUES ('{$cedula}', '{$observacion}', '$fecha', '$codigo_usuario', '$horas', '$minutos','27', '60')";
*/

}
?>
<div class="row">
    <div class="col-md-12">
        <!-- BEGIN SAMPLE TABLE PORTLET-->
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-social-dribbble font-blue"></i>
                    <span class="caption-subject font-blue bold uppercase">RECIBOS DE PAGO</span>
                </div>
                <div class="actions">
                    <a class="btn btn-circle btn-icon-only btn-default" href="rhexpress_menu.php">
                        <i class="fa fa-arrow-left" aria-hidden="true"></i>
                    </a>
                </div>
            </div>
            <div class="portlet-body">
                <!-- BEGIN PORTLET BODY-->
								<!-- INICIO DATOS-->
				<div class="portlet box blue">
					<div class="portlet-title">
				<h4> Datos del Funcionario</h4>
			</div>
	            <div class="portlet-body">
					<!-- <div class="row">
						<div class="form-group">
							<label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2 col-md-3 col-lg-offset-2 col-lg-3 control-label">Fecha de Solicitud:</label>
							<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
								<input name="fecha"  class="form-control input-circle" id="fecha" value="<?php echo date('d-m-Y'); ?>" disabled><p></p>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="form-group">
							<label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2 col-md-3 col-lg-offset-2 col-lg-3 control-label">Nombre:</label>
							<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
								<input name="txtdescripcion"  class="form-control input-circle" type="text" id="txtdescripcion" value="<?= $_SESSION['nombre_rhexpress'] ?>"><p></p>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="form-group">
							<label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2 col-md-3 col-lg-offset-2 col-lg-3 control-label">Colaborador</label>
							<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
								<input name="ficha"  class="form-control input-circle" type="text" id="ficha" value="<?= $_SESSION['ficha_rhexpress'] ?>"><p></p>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="form-group">
							<label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2 col-md-3 col-lg-offset-2 col-lg-3 control-label">Cédula:</label>
							<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
								<input name="cedula"  class="form-control input-circle" type="text" id="cedula" value="<?= $_SESSION['cedula_rhexpress'] ?>"><p></p>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="form-group">
							<label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2
							col-md-3 col-lg-offset-2 col-lg-3 control-label">Unidad Administrativa:</label>
							<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
								<input name="unidad_administrativa"  class="form-control input-circle" type="text" id="unidad_administrativa" value="<?= $_SESSION['dpto_rhexpress'] ?>"><p></p>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="form-group">
							<label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2
							col-md-3 col-lg-offset-2 col-lg-3 control-label">Departamento Adscrito:</label>
							<div class="col-xs-7 col-sm-7 col-md-7 col-lg-7">
								<input name="unidad_administrativa"  class="form-control input-circle" type="text" id="unidad_administrativa"
								value="<?= $_SESSION['_Departamento'] ?>"><p></p>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="form-group">
							<label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2
							col-md-3 col-lg-offset-2 col-lg-3 control-label">Jefe Inmediato:</label>
							<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
								<input name="jefe"  class="form-control input-circle" type="text" id="jefe"
								value="<?php echo $nombre_jefe; ?>">
							</div>
						</div>
					</div><p></p> -->
			<form action="<?= $archivo ?>"  method="get" name="frmPrincipal" id="frmPrincipal" role="form" >
					<div class="row">
						<div class="form-group">
							<label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3
							col-md-2 col-lg-2 control-label" style="margin-top:20px !important;">Planilla</label>
							<div class="col-xs-3 col-sm-6 col-md-8 col-lg-8">							
								<!-- <select name="tipo_solicitud" class="form-control input-circle" type="text" id="tipo_solicitud">
									<option value="">Seleccione...</option>
									
										// while($valores = mysqli_fetch_assoc($planilla_nomina)) {
										// 	echo '<option value="'.$valores[codnom].'">'.$valores[descrip].'</option>';
										// }																
								</select> -->
								 <div class="input-group">
									<span id="id_nomina"></span>
								</div>
							</div>
						</div>
					</div>
					<div class="row" id="row_permiso">
						<div class="form-group">
							<label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2
							col-md-3 col-lg-offset-2 col-lg-3 control-label">Tipo de Permiso:</label>
							<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
								<select name="tipo_permiso" class="form-control input-circle" type="text" id="tipo_permiso">
									<option value="">Seleccione...</option>
								</select>

							</div>
						</div>
					</div>
				</div>

			</div>


			<input name="btn_guardar" type="Hidden" id="op_tp" value="<?= $btn_guardar; ?>">
			<div class="form-body">

					
			<!-- FN DATOS -->
			<span id = "Tardanza">
				<!-- INICIO USO TIEMPO -->
					<div class="portlet box blue">
						<div class="portlet-title">
							<H4>Registro de Tardanza</H4>
						</div>
						<div class="portlet-body">
							<div class="row">
								<div class="form-group">
									<label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2
									col-md-3 col-lg-offset-2 col-lg-3 control-label">Tiempo Acumulado Incapac. (Horas):</label>
									<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
										<input name="tiempo_tardanza_inc" class="form-control input-circle" type="text" id="tiempo_tardanza_inc" value="<?= $tiempo_inc; ?>"><p></p>
									</div>
								</div>
							</div>

							<div class="row">
								<div class="form-group">
									<label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2
									col-md-3 col-lg-offset-2 col-lg-3 control-label">Tiempo Acumulado Extra (Horas):</label>
									<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
										<input name="tiempo_tardanza_acum" class="form-control input-circle" type="text" id="tiempo_tardanza_acum" value="<?= $tiempo_comp; ?>"><p></p>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="portlet box blue">
						<div class="portlet-title">
							<H4>Registro de Tardanza</H4>
						</div>
						<div class="portlet-body">
							<div class="row">
								<div class="form-group">
							        <label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2
									col-md-3 col-lg-offset-2 col-lg-3 control-label">Fecha Inicia:</label>
									<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
										<div class="input-group date date-picker">
			                                <input type="text" class="form-control input-circle-left" readonly="" id="fecha_tardanza" name="fecha_tardanza"  value="<?= date('d-m-Y'); ?>">
			                                <span class="input-group-btn">
			                                    <button class="btn default" type="button">
			                                        <i class="fa fa-calendar"></i>
			                                    </button>
			                                </span>
			                            </div>
									</div>
							    </div>
							</div><p></p>
							<div class="row">
								<div class="form-group">
							        <label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2
									col-md-3 col-lg-offset-2 col-lg-3 control-label">Fecha Termina:</label>
									<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
										<div class="input-group date date-picker">
			                                <input type="text" class="form-control input-circle-left" readonly="" id="fecha_tardanza_fin" name="fecha_tardanza_fin"  value="<?= date('d-m-Y'); ?>">
			                                <span class="input-group-btn">
			                                    <button class="btn default" type="button">
			                                        <i class="fa fa-calendar"></i>
			                                    </button>
			                                </span>
			                            </div>
									</div>
							    </div>
							</div><p></p>

							<div class="row">
								<div class="form-group">
									<label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2
									col-md-3 col-lg-offset-2 col-lg-3 control-label">Horas Inicia:</label>
									<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
										<input name="hora_inicia" class="form-control input-circle" type="time" id="hora_inicia" value="00:00"><p></p>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="form-group">
									<label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2
									col-md-3 col-lg-offset-2 col-lg-3 control-label">Horas Termina:</label>
									<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
										<input name="hora_termina" class="form-control input-circle" type="time" id="hora_termina" value="00:00"><p></p>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="form-group">
									<label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2
									col-md-3 col-lg-offset-2 col-lg-3 control-label">Total Solicitado:</label>
									<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
										<input name="total_tard_solic" class="form-control input-circle" type="text" id="total_tard_solic" ><p></p>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="form-group">
									<label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2
									col-md-3 col-lg-offset-2 col-lg-3 control-label">Total Disponible:</label>
									<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
										<input name="total_tard" class="form-control input-circle" type="text" id="total_tard" ><p></p>
									</div>
								</div>
							</div>


							<!--<div class="row">
								<div class="form-group">
									<label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2
									col-md-3 col-lg-offset-2 col-lg-3 control-label">Minutos:</label>
									<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
										<input name="minutos_tardanza" class="form-control input-circle" type="text" id="minutos_tardanza" value="0"><p></p>
									</div>
								</div>
							</div>-->

							<div class="row">
								<div class="form-group">
									<label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2
									col-md-3 col-lg-offset-2 col-lg-3 control-label">Observaciones</label>
									<div class="col-xs-7 col-sm-7 col-md-7 col-lg-7">
										<textarea cols="5" rows="5" name="observacion_tardanza"  class="form-control" type="text" id="observacion_tardanza" value="<?php ?>"></textarea>
									</div>
								</div>
							</div><p></p>							

							<div class="row">
								<div class="form-group">
									<label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2
									col-md-3 col-lg-offset-2 col-lg-3 control-label">Documento Legal (PDF):</label>
									<div class="col-xs-7 col-sm-7 col-md-7 col-lg-7">
										<input name="archivo_tardanza" class="form-control input-circle" type="file" id="archivo_tardanza" value="0"><p></p>
									</div>
								</div>
							</div><p></p>
						</div>
					</div>
					<!-- FIN USO TIEMPO -->
			</span>
			<!-- Fin Tardanza-->


			<!-- Inicio Permiso -->
			<span id = "Permiso">
				<!-- INICIO USO TIEMPO -->
					<div class="portlet box blue">
						<div class="portlet-title">
							<H4>Permiso - Tiempo Disponible</H4>
						</div>
						<div class="portlet-body">
							<div class="row">
								<div class="form-group">
									<label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2
									col-md-3 col-lg-offset-2 col-lg-3 control-label">Tiempo Acumulado Incapac. (Horas):</label>
									<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
										<input name="tiempo_permisos_inc" class="form-control input-circle" type="text" id="tiempo_permisos_inc" value="<?= $tiempo_inc; ?>"><p></p>
									</div>
								</div>
							</div>

							<div class="row">
								<div class="form-group">
									<label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2
									col-md-3 col-lg-offset-2 col-lg-3 control-label">Tiempo Acumulado Extra (Horas):</label>
									<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
										<input name="tiempo_permisos_acum" class="form-control input-circle" type="text" id="tiempo_permisos_acum" value="<?= $tiempo_comp; ?>"><p></p>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="portlet box blue">
						<div class="portlet-title">
							<H4>Permiso - Registro</H4>
						</div>
						<div class="portlet-body">
							<div class="row">
								<div class="form-group">
							        <label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2
									col-md-3 col-lg-offset-2 col-lg-3 control-label">Fecha Inicia:</label>
									<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
										<div class="input-group date date-picker">
                                                                                    <input type="text" class="form-control input-circle-left" id="fecha_permisos" name="fecha_permisos" value="<?= date('d-m-Y'); ?>">
                                                                                    <span class="input-group-btn">
                                                                                        <button class="btn default date-set" type="button">
                                                                                            <i class="fa fa-calendar"></i>
                                                                                        </button>
                                                                                    </span>
                                                                                </div>
									</div>
                                                                </div>
							</div><p></p>
							<div class="row">
								<div class="form-group">
							        <label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2
									col-md-3 col-lg-offset-2 col-lg-3 control-label">Fecha Termina:</label>
									<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
										<div class="input-group date date-picker">
                                                                                    <input type="text" class="form-control input-circle-left"  id="fecha_permisos_fin" name="fecha_permisos_fin" value="<?= date('d-m-Y'); ?>">
                                                                                    <span class="input-group-btn">
                                                                                        <button class="btn default date-set" type="button">
                                                                                            <i class="fa fa-calendar"></i>
                                                                                        </button>
                                                                                    </span>
                                                                                </div>
									</div>
                                                                </div>
							</div><p></p>

							<div class="row">
								<div class="form-group">
                                                                <label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2
                                                                                                    col-md-3 col-lg-offset-2 col-lg-3 control-label">Horas Desde:</label>
                                                                                                    <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                                                                    <div class="input-group date" id="div_hora_permisos_inicio">
                                                                        <input id="hora_permisos_inicio" name="hora_permisos_inicio" type="text" size="16" class="form-control input-circle-left">
                                                                        <span class="input-group-btn">
                                                                            <button class="btn default date-reset" type="button">
                                                                                <i class="fa fa-times"></i>
                                                                            </button>
                                                                            <button class="btn default date-set" disabled type="button">
                                                                                <i class="fa fa-calendar"></i>
                                                                            </button>
                                                                        </span>
                                                                    </div><p></p>
                                                                </div>
                                                            </div>
                                                        </div>

							<div class="row">
								<div class="form-group">
									<label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2
									col-md-3 col-lg-offset-2 col-lg-3 control-label">Horas Hasta:</label>
									<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                                                                        <div class="input-group date" id="div_hora_permisos_fin">
                                                                            <input id="hora_permisos_fin" name="hora_permisos_fin" type="text" size="16" class="form-control input-circle-left">
                                                                            <span class="input-group-btn">
                                                                                <button class="btn default date-reset" type="button">
                                                                                    <i class="fa fa-times"></i>
                                                                                </button>
                                                                                <button class="btn default date-set" disabled type="button">
                                                                                    <i class="fa fa-calendar"></i>
                                                                                </button>
                                                                            </span>
                                                                        </div><p></p>
                                                                    </div>
								</div>
							</div>

							<div class="row">
								<div class="form-group">
									<label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2
									col-md-3 col-lg-offset-2 col-lg-3 control-label">Total Solicitado:</label>
									<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
										<input name="total_tiempo_permisos" class="form-control input-circle" type="text" id="total_tiempo_permisos" value="0"> <p></p>
									</div>
                                                                        <label class="text-left col-sm-1 col-md-1 col-lg-1 control-label">(Horas)</label>
                                                                        
									<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                                                                            <input name="dias_permisos" readonly class="form-control input-circle" type="text" id="dias_permisos" value="0"><p></p>
									</div>
                                                                        <label class="text-left col-sm-1 col-md-1 col-lg-1 control-label">(Dias)</label>
								</div>
                                                                
							</div>
                                                        
                                                        <div class="row">
								<div class="form-group">
									<label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2
									col-md-3 col-lg-offset-2 col-lg-3 control-label"></label>
									<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                                                                        </div>
                                                                        
									<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
										<input name="horas_permisos" readonly class="form-control input-circle" type="text" id="horas_permisos" value="0"><p></p>
									</div>
                                                                        <label class="text-left col-sm-1 col-md-1 col-lg-1 control-label">(Horas)</label>
								</div>
                                                                
							</div>
                                                        
                                                        <div class="row">
								<div class="form-group">
									<label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2
									col-md-3 col-lg-offset-2 col-lg-3 control-label"></label>
									<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
									</div>
                                                                       <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
										<input name="minutos_permisos" readonly class="form-control input-circle" type="text" id="minutos_permisos" value="0"><p></p>
									</div>
                                                                        <label class="text-left col-sm-1 col-md-1 col-lg-1 control-label">(Minutos)</label>
								</div>
                                                                
							</div>

							<div class="row">
								<div class="form-group">
									<label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2
									col-md-3 col-lg-offset-2 col-lg-3 control-label">Total Disponible:</label>
									<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
										<input name="total_tiempo_disponible" readonly class="form-control input-circle" type="text" id="total_tiempo_disponible" value="0"><p></p>
									</div>
                                                                        <label class="text-left col-sm-1 col-md-1 col-lg-1 control-label">(Horas)</label>
								</div>
							</div>

							<div class="row">
								<div class="form-group">
									<label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2
									col-md-3 col-lg-offset-2 col-lg-3 control-label">Observaciones</label>
									<div class="col-xs-7 col-sm-7 col-md-7 col-lg-7">
										<textarea cols="5" rows="5" name="observacion_permisos"  class="form-control" type="text" id="observacion_permisos"></textarea>
									</div>
								</div>
							</div><p></p>

							<div class="row">
								<div class="form-group">
									<label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2
									col-md-3 col-lg-offset-2 col-lg-3 control-label">Documento Legal (PDF):</label>
									<div class="col-xs-7 col-sm-7 col-md-7 col-lg-7">
										<input name="documentos_permisos" class="form-control input-circle" type="file" id="documentos_permisos" value="0"><p></p>
									</div>
								</div>
							</div>
						</div>
					</div>
					<!-- FIN USO TIEMPO -->
			</span>
			<!-- Fin Permiso-->



			<!-- Inicio Ausencia -->
			<span id = "Ausencia">
				<!-- INICIO USO TIEMPO -->
					<div class="portlet box blue">
						<div class="portlet-title">
							<H4>Registro de Ausencia Justificada</H4>
						</div>
						<div class="portlet-body">
							<div class="row">
								<div class="form-group">
									<label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2
									col-md-3 col-lg-offset-2 col-lg-3 control-label">Tiempo Acumulado Incapac. (Horas):</label>
									<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
										<input name="tiempo_ausencia_inc" class="form-control input-circle" type="text" id="tiempo_ausencia_inc" value="<?= $tiempo_inc; ?>"><p></p>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="form-group">
									<label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2
									col-md-3 col-lg-offset-2 col-lg-3 control-label">Tiempo Acumulado Extra (Horas):</label>
									<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
										<input name="tiempo_ausencia_acum" class="form-control input-circle" type="text" id="tiempo_ausencia_acum" value="<?= $tiempo_comp; ?>"><p></p>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="portlet box blue">
						<div class="portlet-title">
							<H4>Registro de Ausencia Justificada</H4>
						</div>
						<div class="portlet-body">
							<div class="row">
								<div class="form-group">
								    <label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2
										col-md-3 col-lg-offset-2 col-lg-3 control-label">Motivo:</label>
									<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
								        <select class="form-control input-circle" name="motivo_ausencia" id="motivo_ausencia">
								        	<option value="116">Cita Médica</option>
								        	<option value="117">Misión Oficial</option>
								        	<option value="118">Representación Gremial</option>
								        	<option value="119">Otros</option>
								          
								        </select>
								    </div>
								</div>
							</div><p></p>

							<div class="row">
								<div class="form-group">
							        <label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2
									col-md-3 col-lg-offset-2 col-lg-3 control-label">Fecha Desde:</label>
									<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
										<div class="input-group date date-picker">
			                                <input type="text" class="form-control input-circle-left" readonly="" id="inicio_ausencia" name="inicio_ausencia"  value="<?= date('d-m-Y'); ?>">
			                                <span class="input-group-btn">
			                                    <button class="btn default" type="button">
			                                        <i class="fa fa-calendar"></i>
			                                    </button>
			                                </span>
			                            </div>
									</div>
							    </div>
							</div><p></p>

							<div class="row">
								<div class="form-group">
							        <label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2
									col-md-3 col-lg-offset-2 col-lg-3 control-label">Fecha Hasta:</label>
									<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
										<div class="input-group date date-picker">
			                                <input type="text" class="form-control input-circle-left" readonly="" id="fin_ausencia" name="fin_ausencia"  value="<?= date('d-m-Y'); ?>">
			                                <span class="input-group-btn">
			                                    <button class="btn default" type="button">
			                                        <i class="fa fa-calendar"></i>
			                                    </button>
			                                </span>
			                            </div>
									</div>
							    </div>
							</div><p></p>
							<!--<div class="row">
								<div class="form-group">
									<label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2
									col-md-3 col-lg-offset-2 col-lg-3 control-label">Horas Inicia:</label>
									<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
										<input name="horas_ausencia_inicio" class="form-control input-circle" type="time" id="horas_ausencia_inicio" value="00:00"><p></p>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="form-group">
									<label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2
									col-md-3 col-lg-offset-2 col-lg-3 control-label">Horas Termina:</label>
									<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
										<input name="horas_ausencia_fin" class="form-control input-circle" type="time" id="horas_ausencia_fin" value="00:00"><p></p>
									</div>
								</div>
							</div>-->

							<div class="row">
								<div class="form-group">
									<label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2
									col-md-3 col-lg-offset-2 col-lg-3 control-label">Dias:</label>
									<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
										<input name="dias_ausencia" class="form-control input-circle" type="text" id="dias_ausencia" value="0"><p></p>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="form-group">
									<label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2
									col-md-3 col-lg-offset-2 col-lg-3 control-label">Horas Restantes:</label>
									<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
										<input name="dias_ausencia_total" class="form-control input-circle" type="text" id="dias_ausencia_total" value="0"><p></p>
									</div>
								</div>
							</div>

							<div class="row">
								<div class="form-group">
									<label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2
									col-md-3 col-lg-offset-2 col-lg-3 control-label">Observaciones</label>
									<div class="col-xs-7 col-sm-7 col-md-7 col-lg-7">
										<textarea cols="5" rows="5" name="observaciones_ausencia"  class="form-control" type="text" id="observaciones_ausencia" value="<?php ?>"></textarea>
									</div>
								</div>
							</div>

							<div class="row">
								<div class="form-group">
									<label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2
									col-md-3 col-lg-offset-2 col-lg-3 control-label">Documento Legal (PDF):</label>
									<div class="col-xs-7 col-sm-7 col-md-7 col-lg-7">
										<input name="archivo_ausencia" class="form-control input-circle" type="file" id="archivo_ausencia" value="0"><p></p>
									</div>
								</div>
							</div>
						</div>
					</div>
					<!-- FIN USO TIEMPO -->
			</span>
			<!-- Fin Ausencia-->
					
					<br>
					<div class="row">
						<input type="Hidden" name="con" value="1">
						<input type="Hidden" name="tiempo" value="<?php ?>">
						<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
						</div>
						<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
							<button class="btn btn-primary float-left" ><?php echo $boton; ?></button>
						</div>

						<!-- <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
							<a class="btn btn-danger" href="rhexpress_menu.php"> Salir</a>
						</div> -->
						<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
						</div>
					</div>
				</div>
			</form>
			<!-- END PORTLET BODY-->
        </div>
    </div>
    <!-- END SAMPLE TABLE PORTLET-->
</div>
</div><?php include("config/rhexpress_footer2.php");?>

<script>
$(document).ready(function(){
	/*$.get("ajax/ajax_tiempo_compensatorio_disponible.php",function(res){
		tiempo  = parseFloat(res);
		horas   = 8;
		dias    = tiempo/horas;
		dias    = parseFloat(dias);
		hora    = parseFloat(tiempo - (dias*horas));
		minutos = parseFloat(tiempo - (dias*horas) - hora);
		//$("#").value(tiempo);
		$("#dias_disponibles").empty(dias);

		$("#dias_disponibles").val(dias);
		$("#horas_disponibles").val(hora);
		$("#minutos_disponibles").val(minutos);
		$("#tiempo_disponible").val(tiempo);
		//console.log(tiempo+" "+horas+" "+dias+" "+hora+" "+minutos);
	});*/
	$.get("../nomina/paginas/ajax/obtenerNomina_leon.php",function(res){
    $("#id_nomina").empty();
    $("#id_nomina").append(res);
 	 });   
	function calculo(dia, horas, minutos)
	{
		dia     = parseInt(dia) * 8;
		hora    = parseInt(horas);
		minutos = parseInt(minutos) / 60;
		console.log(minutos);
		tiempo  = dia + hora + minutos;
		
		return tiempo.toFixed(2);
	}
	function variables()
	{
		dias       = $("#dias_solicitados").val();
		horas      = $("#horas_solicitadas").val();
		minutos    = $("#minutos_solicitados").val();
		disponible = $("#tiempo_disponible").val(); 
		valor      = calculo(dias,horas, minutos);
		$("#tiempo_solicitado").val(valor);
	}

	function restar_dias()
	{
		dias       = $("#dias_ausencia").val();
		disponible = $("#tiempo_tardanza_inc").val(); 
		dias = dias*8;
		total = disponible - dias;
		$("#dias_ausencia_total").val(total);
	}


	function restar_permisos()
	{

		hora_permisos_inicio = $("#hora_permisos_inicio").val();
		hora_permisos_fin    = $("#hora_permisos_fin").val();
		disponible           = $("#tiempo_tardanza_inc").val(); 

		inicioMinutos       = parseInt(hora_permisos_inicio.substr(14,2));
		inicioHoras         = parseInt(hora_permisos_inicio.substr(11,2));
		//console.log(hora_permisos_inicio.substr(14,2)+" "+hora_permisos_inicio.substr(11,2));

		finMinutos          = parseInt(hora_permisos_fin.substr(14,2));
		finHoras            = parseInt(hora_permisos_fin.substr(11,2));

		transcurridoMinutos = finMinutos - inicioMinutos;
		transcurridoHoras   = finHoras - inicioHoras;
	  
		if (transcurridoMinutos < 0) 
		{
			transcurridoHoras--;
			transcurridoMinutos = 60 + transcurridoMinutos;
		}

		horas   = transcurridoHoras.toString();
		minutos = transcurridoMinutos.toString();

		if (horas.length < 2) 
		{
			horas = "0"+horas;
		}

		if (horas.length < 2) 
		{
			horas = "0"+horas;
		}
		minutos = minutos / 60;
		minutos = minutos.toFixed(2);
		tiempo_disponible = $("#tiempo_tardanza_inc").val();
		console.log(tiempo_disponible-horas-minutos);
		total_2 = horas-minutos;
		if (total_2<0) {
			total_2 = (-1)*total_2;
		}
		$("#total_tiempo_permisos").val(total_2);

		total = tiempo_disponible-horas-minutos;
		$("#total_tiempo_disponible").val(total);



		/*dias                 = dias*8;
		total                = disponible - dias;
		$("#total_tiempo_disponible").val(total);*/
	}
        
        function calcular_duracion_permiso()
        {
            var fecha_hora_inicio=document.getElementById('hora_permisos_inicio').value;
            var fecha_hora_fin=document.getElementById('hora_permisos_fin').value;            
            if (fecha_hora_inicio=='' || fecha_hora_fin=='')
            {
                return false;
            }
            else
            {
//                alert(fecha_inicio);
//                alert(fecha_fin);
                var a = moment(fecha_hora_inicio, "DD-MM-YYYY HH:mm");
                var b = moment(fecha_hora_fin, "DD-MM-YYYY HH:mm");
            	var difDias = b.diff(a, 'days');
            	var difMinutes = b.diff(a, 'minutes');                
            	difMinutes = difMinutes-(difDias*1440);
                var dias = difDias;
                var horas = Math.floor( (difMinutes)/60 );
                var minutos = difMinutes-(horas*60);
            	
            	if( horas >= 8 ){ dias=dias+1; horas=horas-8; }
            	if(dias<10){ dias="0"+dias; }
            	if(horas<10){ horas="0"+horas; }
            	if(minutos<10){ minutos="0"+minutos; }
                
                var total= parseFloat((dias*8))+parseFloat(horas)+(parseFloat(minutos)/60);
                document.getElementById('total_tiempo_permisos').value=total;
                document.getElementById('dias_permisos').value=dias;
                document.getElementById('horas_permisos').value=horas;
                document.getElementById('minutos_permisos').value=minutos;
            }
        }
        
        function calcular_disponible_permiso()
        {
            var tiempo_disponible=document.getElementById('tiempo_permisos_inc').value;
            var tiempo_solicitado=document.getElementById('total_tiempo_permisos').value;            
            total = parseFloat(tiempo_disponible)-parseFloat(tiempo_solicitado);
            $("#total_tiempo_disponible").val(total);
        }
        
	function restarHoras() 
	{

		inicio              = document.getElementById("hora_inicia").value;
		fin                 = document.getElementById("hora_termina").value;

		inicioMinutos       = parseInt(inicio.substr(3,2));
		inicioHoras         = parseInt(inicio.substr(0,2));

		finMinutos          = parseInt(fin.substr(3,2));
		finHoras            = parseInt(fin.substr(0,2));

		transcurridoMinutos = finMinutos - inicioMinutos;
		transcurridoHoras   = finHoras - inicioHoras;
	  
		if (transcurridoMinutos < 0) 
		{
			transcurridoHoras--;
			transcurridoMinutos = 60 + transcurridoMinutos;
		}

		horas   = transcurridoHoras.toString();
		minutos = transcurridoMinutos.toString();

		if (horas.length < 2) 
		{
			horas = "0"+horas;
		}

		if (horas.length < 2) 
		{
			horas = "0"+horas;
		}
		minutos = minutos / 60;
		minutos = minutos.toFixed(2);
		tiempo_disponible = $("#tiempo_tardanza_inc").val();
		console.log(tiempo_disponible-horas-minutos);
		total_2 = horas-minutos;
		if (total_2<0) {
			total_2 = (-1)*total_2;
		}
		$("#total_tard_solic").val(total_2);

		total = tiempo_disponible-horas-minutos;
		$("#total_tard").val(total);
	  

	}
	
	$("#Tardanza").hide();
	$("#Permiso").hide();
	$("#Ausencia").hide();
			$("#row_permiso").hide();

	$("#tipo_solicitud").change(function(){
		tipo = $("#tipo_solicitud").val();
		if (tipo == 1) 
		{
			$("#Tardanza").show();
			$("#Permiso").hide();
			$("#Ausencia").hide();
			$("#row_permiso").hide();

		}
		if (tipo == 2) 
		{
			$("#Tardanza").hide();
			$("#Permiso").hide();
			$("#Ausencia").show();
			$("#row_permiso").hide();

			$.get("ajax/select_expediente_tipo.php",function(res){
				//$("#motivo_ausencia").empty();
				//$("#motivo_ausencia").append(res);
			});
		}
		if (tipo == 3) 
		{
			$("#Tardanza").hide();
			$("#Permiso").show();
			$("#Ausencia").hide();
			$("#row_permiso").show();
			$.get("ajax/select_expediente_tipo_permiso.php",function(rest){
				$("#tipo_permiso").empty();
				$("#tipo_permiso").append(rest);


			});
		}
		
	});

	$("#hora_termina").on('keyup',function(){
		restarHoras()
	});
	$("#dias_ausencia").on('keyup',function(){
		restar_dias()
	});
	$("#hora_permisos_fin").change(function(){
		calcular_duracion_permiso();
                calcular_disponible_permiso();
	});
        $("#hora_permisos_inicio").change(function(){
		rcalcular_duracion_permiso();
                calcular_disponible_permiso();
	});
	$("#dias_solicitados").on('keyup',function(){
		variables()
	});
	$("#horas_solicitadas").on('keyup',function(){
		variables()
	});
	$("#minutos_solicitados").on('keyup',function(){
		variables()
	});
	
});
</script>
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<!--<script src="assets/pages/scripts/components-date-time-pickers.js" type="text/javascript"></script>-->
<script type="text/javascript">
    $(document).ready(function() {     
        $("#div_hora_permisos_inicio").datetimepicker({
        format: 'dd-mm-yyyy hh:ii',
        autoclose: true,
        useCurrent: false
        });
        $("#div_hora_permisos_fin").datetimepicker({
        format: 'dd-mm-yyyy hh:ii',
        autoclose: true,
        useCurrent: false
        });
        $("#fecha_permisos").datepicker({
        format: 'dd-mm-yyyy',
        autoclose: true,
        useCurrent: false
        });
        $("#fecha_permisos_fin").datepicker({
        format: 'dd-mm-yyyy',
        autoclose: true,
        useCurrent: false
        });
    });
    

</script>     
<!-- END PAGE LEVEL SCRIPTS -->
<?php
 include("config/end.php"); ?>
