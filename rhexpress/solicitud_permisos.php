<?php
//-------------------------------------------------
session_start();
//-------------------------------------------------
require_once "config/rhexpress_config.php";
include("config/rhexpress_header2.php");
require("../nomina/procesos/PHPMailer_5.2.4/class.phpmailer.php");
include("../nomina/procesos/PHPMailer_5.2.4/class.smtp.php");
$conexion =  mysqli_connect( DB_HOST, DB_USUARIO, DB_CLAVE, $_SESSION['bd'] ) or die( 'No Hay Conexión con el Servidor de Mysql' );
        mysqli_query($conexion, 'SET CHARACTER SET utf8');
//-------------------------------------------------

$SQL    = "SELECT  nompersonal.apenom,nompersonal.email FROM departamento
JOIN nompersonal on (nompersonal.cedula = departamento.IdJefe)
WHERE departamento.IdJefe  = '".$_SESSION['_Jefe']."'";

$var_sql="SELECT * FROM nomempresa";
$resul_nomempresa=$conexion->query($var_sql);

$res=mysqli_fetch_assoc($resul_nomempresa);
$correo_sistemas2=$res['correo_sistemas2'];
$correo_sistemas2_password=$res['correo_sistemas2_password'];
$correo_sistemas2_remitente=$res['correo_sistemas2_remitente'];
$correo_sistemas2_host=$res['correo_sistemas2_host'];
$correo_sistemas2_puerto=$res['correo_sistemas2_puerto'];
$correo_sistemas2_modo=$res['correo_sistemas2_modo'];

$cedula      = isset($_SESSION['cedula_rhexpress'])? $_SESSION['cedula_rhexpress']: '';
$res1        = $conexion->query($SQL);
$jefe        = mysqli_fetch_assoc($res1);
//print_r($jefe);
$correo_jefe=$jefe['email'];

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
//echo $jefes['cedula'] ." ". $jefe['IdJefe']. " ".$_SESSION['_Jefe'];
if ( $cedula==$jefe['IdJefe']) 
{
	$nombre_jefe = $director['apenom'];
}
else
{
	$nombre_jefe = $jefe['apenom'];
}
// $sql_tiempo="SELECT tiempo FROM dias_incapacidad WHERE cedula = '6-714-1325' AND tipo_justificacion = '5'";
// $saldo ini
$sql="SELECT SUM(tiempo) as tiempo FROM `dias_incapacidad` WHERE cedula='{$cedula}' AND tipo_justificacion='5'";
$suma_hora        = $conexion->query($sql)->fetch_assoc();
$suma=$suma_hora['tiempo'];
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

		if(isset($_FILES['documento_tardanza']) AND $_FILES['documento_tardanza']["name"] != "")
		{
			if (!file_exists("archivos/".$cedula)) 
			{
				mkdir("archivos/".$cedula, 0777, true);
			}
			$archivo_tardanza = basename($_FILES['documento_tardanza']['name']);
			$archivo_tardanza = str_replace(' ', '', strtolower($archivo_tardanza));
			$archivo_tardanza = "archivos/" . $cedula . "/" . time() . '_' . $archivo_tardanza ;
			chmod($_FILES['documento_tardanza']['tmp_name'],777);
			chmod( $archivo_tardanza, 777);
			if (! rename($_FILES['documento_tardanza']['tmp_name'], $archivo_tardanza) ) {
				exit("¡Error! Al mover el archivo");
			}
		}
		$tiempo_tardanza_inc  = (isset($_REQUEST['tiempo_tardanza_inc'])) ? $_REQUEST['tiempo_tardanza_inc'] : '' ;
		$tiempo_tardanza_acum = (isset($_REQUEST['tiempo_tardanza_acum'])) ? $_REQUEST['tipo_justificacion'] : '' ;
		$hora_inicia       = (isset($_REQUEST['hora_inicia'])) ? $_REQUEST['hora_inicia'] : '' ;
		$hora_termina       = (isset($_REQUEST['hora_termina'])) ? $_REQUEST['hora_termina'] : '' ;
		$fecha_tardanza       = (isset($_REQUEST['fecha_tardanza'])) ? $_REQUEST['fecha_tardanza'] : '' ;
		$fecha_tardanza_fin   = (isset($_REQUEST['fecha_tardanza_fin'])) ? $_REQUEST['fecha_tardanza_fin'] : '' ;
		$horas_tardanza       = (isset($_REQUEST['horas_tardanza'])) ? $_REQUEST['horas_tardanza'] : '' ;
		$minutos_tardanza     = (isset($_REQUEST['minutos_tardanza'])) ? $_REQUEST['minutos_tardanza'] : '' ;
		$observacion_tardanza = (isset($_REQUEST['observacion_tardanza'])) ? $_REQUEST['observacion_tardanza'] : '' ;

		$fecha_tardanza       = date('Y-m-d', strtotime($fecha_tardanza)) ; // resta 6 mes
		$hora_inicia           = date('H:i:s', strtotime($hora_inicia)) ; // resta 6 mes
		$hora_termina        = date('H:i:s', strtotime($hora_termina)) ; // resta 6 mes
		$fecha_ini            = date('Y-m-d H:i:s', strtotime($fecha_tardanza)) ; // resta 6 mes
		$fecha_fini           = date('Y-m-d H:i:s', strtotime($fecha_tardanza_fin)) ; // resta 6 mes
		$tiempo_solicitado    = $horas_tardanza + round(($minutos_tardanza/60),2);
		$tiempo_soli = (isset($_REQUEST['total_tard_solic'])) ? $_REQUEST['total_tard_solic'] : '' ;
		$tiempo_tard = (isset($_POST['total_tard'])) ? $_POST['total_tard'] : '' ;

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
			fecha_tardanza,
			fecha_tardanza_fin,
			horas_tardanza,
			minutos_tardanza,
			observaciones_tardanza,
			archivo_tardanza,
			hora_inicio,
			hora_fin,
			id_tipo_solicitud)
		VALUES (
			'',
			'".$_SESSION['cedula_rhexpress']."',
			'1',
			'".$persona['IdDepartamento']."',
			'{$fecha_tardanza}',
			'{$fecha_ini}',
			'{$fecha_fini}',
			'{$fecha_ini}',
			'{$fecha_fini}',
			'{$observacion_tardanza}',
			'1',
			'{$tiempo_tard}',
			'{$dias}',
			'{$tiempo_soli}',
			'{$minutos_tardanza}',
			'{$fecha_tardanza}',
			'{$fecha_tardanza_fin}',
			'{$horas_tardanza}',
			'{$minutos_tardanza}',
			'{$observacion_tardanza}',
			'{$archivo_tardanza}',
			'{$hora_inicia}',
			'{$hora_termina}',
			'{$tipo_solicitud}');";

		// $q    = "INSERT INTO dias_incapacidad (cod_user, tipo_justificacion, fecha, tiempo, observacion,horas,  cedula)
		// VALUES ('', '5', '{$fecha_ini}', '{$tiempo_soli}', '{$observacion_tardanza}','$tiempo_tard',  '$cedula')";
		// $conexion->query($q);

	}
	//Ausencia
	elseif ($tipo_solicitud == 2) 
	{
		if(isset($_FILES['archivo_ausencia']) AND $_FILES['archivo_ausencia']["name"] != "")
		{
			if (!file_exists("archivos/".$cedula)) 
			{
				mkdir("archivos/".$cedula, 0777, true);
			}
			$archivo_ausencia = basename($_FILES['archivo_ausencia']['name']);
			$archivo_ausencia = str_replace(' ', '', strtolower($archivo_ausencia));
			$archivo_ausencia = "archivos/" . $cedula . "/" . time() . '_' . $archivo_ausencia ;
			chmod($_FILES['archivo_ausencia']['tmp_name'],777);
			chmod( $archivo_ausencia, 777);
			if (! rename($_FILES['archivo_ausencia']['tmp_name'], $archivo_ausencia) ) 
				exit("¡Error! Al mover el archivo");    
		}
		$tiempo_solicitado      = (isset($_REQUEST['tiempo_ausencia_inc'])) ? $_REQUEST['tiempo_ausencia_inc'] : '' ;
		$tiempo_ausencia_acum   = (isset($_REQUEST['tiempo_ausencia_acum'])) ? $_REQUEST['tipo_justificacion'] : '' ;
		$fecha_ausencia         = (isset($_REQUEST['fecha_ausencia'])) ? date('Y-m-d') : '' ;
		$horas_ausencia         = (isset($_REQUEST['horas_ausencia'])) ? $_REQUEST['horas_ausencia'] : '' ;
		$motivo_ausencia        = (isset($_REQUEST['motivo_ausencia'])) ? $_REQUEST['motivo_ausencia'] : '' ;
		$minutos_ausencia       = (isset($_REQUEST['minutos_ausencia'])) ? $_REQUEST['minutos_ausencia'] : '' ;
		$observaciones_ausencia = (isset($_REQUEST['observaciones_ausencia'])) ? $_REQUEST['observaciones_ausencia'] : '' ;		

		$inicio_ausencia        = (isset($_REQUEST['inicio_ausencia'])) ? $_REQUEST['inicio_ausencia'] : '' ;
		$fin_ausencia           = (isset($_REQUEST['fin_ausencia'])) ? $_REQUEST['fin_ausencia'] : '' ;
		$dias_ausencia          = (isset($_REQUEST['dias_ausencia'])) ? $_REQUEST['dias_ausencia'] : '' ;
		$fecha_ausencia         = date('Y-m-d', strtotime($fecha_ausencia)) ; // resta 6 mes
		$inicio_ausencia        = date('Y-m-d H:i:s', strtotime($inicio_ausencia)) ; // resta 6 mes
		$fin_ausencia           = date('Y-m-d H:i:s', strtotime($fin_ausencia)) ; // resta 6 mes

		$insertar = "INSERT INTO solicitudes_casos (
			id_solicitudes_casos,
			cedula, 
			id_tipo_caso,
			id_departamento,
			fecha_registro,
			fecha_inicio,
			inicio_permiso,
			fin_permiso,
			fecha_fin,
			fin_ausencia,
			observacion,
			observaciones_ausencia,
			archivo_ausencia,
			id_solicitudes_casos_status,
			tiempo,
			dias,
			horas,
			minutos,			
			id_tipo_solicitud)
			VALUES (
				'',
				'".$_SESSION['cedula_rhexpress']."',
				'1',
				'".$persona['IdDepartamento']."',
				'NOW()',
				'{$inicio_ausencia}',
				'{$inicio_ausencia}',
				'{$fin_ausencia}',
				'{$fin_ausencia}',
				'{$fin_ausencia}',
				'{$observaciones_ausencia}',
				'{$observaciones_ausencia}',
				'{$archivo_ausencia}',
				'1',
				'{$tiempo_solicitado}',
				'{$dias_ausencia}',
				'{$horas_ausencia}',
				'{$minutos_ausencia}',
				'{$tipo_solicitud}')";
		//echo $insertar;exit;
	}

	elseif ($tipo_solicitud == 3) 
	{


		if(isset($_FILES['documento_permisos']) AND $_FILES['documento_permisos']["name"] != "")
		{
			if (!file_exists("archivos/".$cedula))
			{
				mkdir("archivos/".$cedula, 0777, true);
			}
			$archivo_permiso = basename($_FILES['documento_permisos']['name']);
			$archivo_permiso = str_replace(' ', '', strtolower($archivo_permiso));
			$archivo_permiso = "archivos/" . $cedula . "/" . time() . '_' . $archivo_permiso ;
			chmod($_FILES['documento_permisos']['tmp_name'],777);
			chmod( $archivo_permiso, 777);
			if (! rename($_FILES['documento_permisos']['tmp_name'], $archivo_permiso) ) {
				exit("¡Error! Al mover el archivo");
			}
		}


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
		$hoy = date("Y-m-d H:i:s");
		// echo $_SESSION['fecha_one']=$fecha_permisos;exit;
		// echo $_SESSION['fecha_two']=$fecha_permisos_fin;exit;
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
		 observaciones_permiso,
		 archivo_permiso,
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
		'{$hoy}',		
		'{$fecha_permisos}',
		'{$fecha_permisos_fin}',
		'{$hora_permisos_inicio}',
		'{$hora_permisos_fin}',
		'{$observacion_permisos}',
		'{$observacion_permisos}',
		'{$archivo_permiso}',
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
		$email=$correo_jefe;
		$mail = new PHPMailer();
		$mail->SMTPAuth = true;
		$mail->isSMTP();
		$mail->SMTPDebug = 0;
		$mail->Host = $correo_sistemas2_host;                        
		$mail->Port = $correo_sistemas2_puerto;
		$mail->SMTPSecure = $correo_sistemas2_modo;			
		$mail->Username = $correo_sistemas2;
		$mail->Password = $correo_sistemas2_password;
		$mail->From = $correo_sistemas2_remitente;
		$mail->FromName = "RRHH Solicitudes";
		$tit='SOLICITUD ';
		
		$mail->Subject = $tit;
		
		$asunto="SOLICITUD DE PERMISOS DEL COLABORADOR ".$_SESSION['nombre_rhexpress'].", POR FAVOR DIRIGIRSE BANDEJA DE ENTRADA PARA APROBAR O RECHAZAR.";
		$mail->Body = $asunto;
		$mail->IsHTML(true);
		// Se asigna la dirección de correo a donde se enviará el mensaje.
		$mail->AddAddress ($email);
		$mail->AddAttachment($ruta);
		
		if(!$mail->Send()) {
			echo $ruta." ".$email." Mailer Error: " . $mail->ErrorInfo;
		}
		$mail->ClearAddresses();
		$mail->ClearAttachments();
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
<!-- BEGIN PAGE LEVEL PLUGINS -->
<link href="../includes/assets/plugins/bootstrap-fileinput/bootstrap-fileinput.css" rel="stylesheet" type="text/css" />
<!-- END PAGE LEVEL PLUGINS -->
<div class="row">
    <div class="col-md-12">
        <!-- BEGIN SAMPLE TABLE PORTLET-->
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-social-dribbble font-blue"></i>
                    <span class="caption-subject font-blue bold uppercase">SOLICITUD DE PERMISOS</span>
                </div>
                <div class="actions">
                    <a class="btn btn-circle btn-icon-only btn-default" href="rhexpress_bandeja_entrada.php">
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
					<div class="row">
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
								<input name="txtdescripcion"  class="form-control input-circle" type="text" id="txtdescripcion" value="<?= $_SESSION['nombre_rhexpress'] ?>" disabled><p></p>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="form-group">
							<label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2 col-md-3 col-lg-offset-2 col-lg-3 control-label">Colaborador</label>
							<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
								<input name="ficha"  class="form-control input-circle" type="text" id="ficha" value="<?= $_SESSION['ficha_rhexpress'] ?>" disabled><p></p>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="form-group">
							<label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2 col-md-3 col-lg-offset-2 col-lg-3 control-label">Cédula:</label>
							<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
								<input name="cedula"  class="form-control input-circle" type="text" id="cedula" value="<?= $_SESSION['cedula_rhexpress'] ?>" disabled><p></p>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="form-group">
							<label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2
							col-md-3 col-lg-offset-2 col-lg-3 control-label">Unidad Administrativa:</label>
							<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
								<input name="unidad_administrativa"  class="form-control input-circle" type="text" id="unidad_administrativa" value="<?= $_SESSION['gerencia_rhexpress'] ?>" disabled><p></p>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="form-group">
							<label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2
							col-md-3 col-lg-offset-2 col-lg-3 control-label">Departamento Adscrito:</label>
							<div class="col-xs-7 col-sm-7 col-md-7 col-lg-7">
								<input name="unidad_administrativa"  class="form-control input-circle" type="text" id="unidad_administrativa"
								value="<?= $_SESSION['_Departamento'] ?>" disabled><p></p>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="form-group">
							<label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2
							col-md-3 col-lg-offset-2 col-lg-3 control-label">Jefe Inmediato:</label>
							<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
								<input name="jefe"  class="form-control input-circle" type="text" id="jefe"
								value="<?php echo $jefe['apenom']; ?>" disabled>
							</div>
						</div>
					</div><p></p>
		<form action="" enctype="multipart/form-data" method="post" name="solicitud_permisos" id="solicitud_permisos" role="form" >
					<div class="row">
						<div class="form-group">
							<label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2
							col-md-3 col-lg-offset-2 col-lg-3 control-label">Tipo de Solicitud (Horas):</label>
							<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
								<select name="tipo_solicitud" class="form-control input-circle" type="text" id="tipo_solicitud">
									<option value="">Seleccione...</option>
									<option value="1">Tardanza</option>
									<option value="2">Ausencia injustificada</option>
									<option value="3">Permiso</option>
								</select>

									<p></p>
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
									col-md-3 col-lg-offset-2 col-lg-3 control-label">Suma de tardanzas (Horas):</label>
									<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
										<input name="tiempo_tardanza_inc" class="form-control input-circle" type="text" id="tiempo_tardanza_inc" value="<?= abs($suma); ?>"><p></p>
									</div>
								</div>
							</div>

							<!-- <div class="row">
								<div class="form-group">
									<label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2
									col-md-3 col-lg-offset-2 col-lg-3 control-label">Tiempo Acumulado Extra (Horas):</label>
									<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
										<input name="tiempo_tardanza_acum" class="form-control input-circle" type="text" id="tiempo_tardanza_acum" value="<?= $tiempo_comp; ?>"><p></p>
									</div>
								</div>
							</div> -->
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
							<!-- <div class="row">
								<div class="form-group">
									<label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2
									col-md-3 col-lg-offset-2 col-lg-3 control-label">Total Disponible:</label>
									<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
										<input name="total_tard" class="form-control input-circle" type="text" id="total_tard" ><p></p>
									</div>
								</div>
							</div> -->


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
										<textarea cols="5" rows="5" name="observacion_tardanza"  class="form-control" type="text" id="observacion_tardanza"></textarea>
									</div>
								</div>
							</div><p></p>

							<div class="row">
								<div class="form-group">

									<label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2
									col-md-3 col-lg-offset-2 col-lg-3 control-label">Adjuntar</label>
									<div class="col-md-3">
										<div class="fileinput fileinput-new" data-provides="fileinput">
											<div class="input-group input-large">
												<div class="form-control uneditable-input input-fixed input-medium" data-trigger="fileinput">
													<i class="fa fa-file fileinput-exists"></i>&nbsp;
													<span class="fileinput-filename"> </span>
												</div>
												<span class="input-group-addon btn default btn-file">
													<span class="fileinput-new"> Seleccione </span>
													<span class="fileinput-exists"> Cambiar </span>
													<input type="file" name="documento_tardanza" > </span>
												<a href="javascript:;" class="input-group-addon btn red fileinput-exists" data-dismiss="fileinput"> Quitar </a>
											</div>
										</div>
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
					<div class="portlet box blue" id="tiempo_acumulet">
						<div class="portlet-title">
							<H4>Permiso - Tiempo acumulado</H4>
						</div>
						<div class="portlet-body">
							<div class="row">
								<div class="form-group">
									<label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2
									col-md-3 col-lg-offset-2 col-lg-3 control-label">Tiempo Acumulado Incapac. (Horas):</label>
									<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
										<input name="tiempo_permisos_inc" class="form-control input-circle" type="text" id="tiempo_permisos_inc" value="<?= abs($suma); ?>"><p></p>
									</div>
								</div>
							</div>

							<!-- <div class="row">
								<div class="form-group">
									<label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2
									col-md-3 col-lg-offset-2 col-lg-3 control-label">Tiempo Acumulado Extra (Horas):</label>
									<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
										<input name="tiempo_permisos_acum" class="form-control input-circle" type="text" id="tiempo_permisos_acum" value="<?= $tiempo_comp; ?>"><p></p>
									</div>
								</div>
							</div> -->
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
											<input type="text" class="form-control input-circle-left"  id="fecha_permisos_fin" name="fecha_permisos_fin" value="<?= date('d-m-Y'); ?>" >
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
                                                                        <!-- <label class="text-left col-sm-1 col-md-1 col-lg-1 control-label">(Horas)</label> -->
                                                                        
									<!-- <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                                                                            <input name="dias_permisos" readonly class="form-control input-circle" type="text" id="dias_permisos" value="0"><p></p>
									</div> -->
                                                                        <!-- <label class="text-left col-sm-1 col-md-1 col-lg-1 control-label">(Dias)</label> -->
								</div>
                                                                
							</div>
                                                        
                                                        <!-- <div class="row">
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
                                                                
							</div> -->
							<div class="row">
								<div class="form-group">
									<label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2
									col-md-3 col-lg-offset-2 col-lg-3 control-label"></label>
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
									col-md-3 col-lg-offset-2 col-lg-3 control-label">Adjuntar</label>
                                    <div class="col-md-3">
                                        <div class="fileinput fileinput-new" data-provides="fileinput">
                                            <div class="input-group input-large">
                                                <div class="form-control uneditable-input input-fixed input-medium" data-trigger="fileinput">
                                                    <i class="fa fa-file fileinput-exists"></i>&nbsp;
                                                    <span class="fileinput-filename"> </span>
                                                </div>
                                                <span class="input-group-addon btn default btn-file">
                                                    <span class="fileinput-new"> Seleccione </span>
                                                    <span class="fileinput-exists"> Cambiar </span>
                                                    <input type="file" name="documento_permisos" > </span>
                                                <a href="javascript:;" class="input-group-addon btn red fileinput-exists" data-dismiss="fileinput"> Quitar </a>
                                            </div>
                                        </div>
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
							<H4>Registro de Ausencia Injustificada</H4>
						</div>
						<div class="portlet-body">
							<div class="row">
								<div class="form-group">
									<label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2
									col-md-3 col-lg-offset-2 col-lg-3 control-label">Suma de ausencias injustificadas (Horas):</label>
									<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
										<input name="tiempo_ausencia_inc" class="form-control input-circle" type="text" id="tiempo_ausencia_inc" value="<?= abs($suma); ?>"><p></p>
									</div>
								</div>
							</div>
							<!-- <div class="row">
								<div class="form-group">
									<label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2
									col-md-3 col-lg-offset-2 col-lg-3 control-label">Tiempo Acumulado Extra (Horas):</label>
									<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
										<input name="tiempo_ausencia_acum" class="form-control input-circle" type="text" id="tiempo_ausencia_acum" value="<?= $tiempo_comp; ?>"><p></p>
									</div>
								</div>
							</div> -->
						</div>
					</div>
					<div class="portlet box blue">
						<div class="portlet-title">
							<H4>Registro de Ausencia Injustificada</H4>
						</div>
						<div class="portlet-body">
							<div class="row">
								<!-- <div class="form-group">
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
								</div> -->
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
							<!-- <div class="row">
								<div class="form-group">
									<label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2
									col-md-3 col-lg-offset-2 col-lg-3 control-label">Horas Restantes:</label>
									<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
										<input name="dias_ausencia_total" class="form-control input-circle" type="text" id="dias_ausencia_total" value="0"><p></p>
									</div>
								</div>
							</div> -->

							<div class="row">
								<div class="form-group">
									<label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2
									col-md-3 col-lg-offset-2 col-lg-3 control-label">Observaciones</label>
									<div class="col-xs-7 col-sm-7 col-md-7 col-lg-7">
										<textarea cols="5" rows="5" name="observaciones_ausencia"  class="form-control" type="text" id="observaciones_ausencia" ></textarea>
									</div>
								</div>
							</div>

							<div class="row">
								<div class="form-group">

									<label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2
									col-md-3 col-lg-offset-2 col-lg-3 control-label">Adjuntar</label>
									<div class="col-md-3">
										<div class="fileinput fileinput-new" data-provides="fileinput">
											<div class="input-group input-large">
												<div class="form-control uneditable-input input-fixed input-medium" data-trigger="fileinput">
													<i class="fa fa-file fileinput-exists"></i>&nbsp;
													<span class="fileinput-filename"> </span>
												</div>
												<span class="input-group-addon btn default btn-file">
													<span class="fileinput-new"> Seleccione </span>
													<span class="fileinput-exists"> Cambiar </span>
													<input type="file" name="archivo_ausencia"> </span>
												<a href="javascript:;" class="input-group-addon btn red fileinput-exists" data-dismiss="fileinput"> Quitar </a>
											</div>
										</div>
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
							<button class="btn btn-primary" onclick="document.solicitud_permisos.submit();"> Guardar</button>
						</div>

						<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
							<a class="btn btn-danger" href="rhexpress_bandeja_entrada.php"> Salir</a>
						</div>
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
	let cantDiasLibres="";
	$('#hora_permisos_inicio').on("click", function(){ 
		let ficha=document.getElementById('ficha').value;
		let fecha_permisos=document.getElementById('fecha_permisos').value;
		let fecha_permisos_fin=document.getElementById('fecha_permisos_fin').value;	
		let url="ajax/ajax_obtener_cantdias_libres.php";
		$.ajax({
			type:"POST",
			url: url,
			data: {fecha_permisos:fecha_permisos,fecha_permisos_fin:fecha_permisos_fin,ficha:ficha},
			success: function (data) {
				var diasLibre=JSON.parse(data);
					//console.log(diasLibre.dias_libres);
					console.log(diasLibre.dias_libres);
					cantDiasLibres=diasLibre.dias_libres;
					//console.log(cantDiasLibres);

            }
		});
	});
	$(document).ready(function(){
	
	$.get("ajax/ajax_tiempo_compensatorio_disponible.php",function(res){
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



		dias                 = dias*8;
		total                = disponible - dias;
		console.log(total);
		$("#total_tiempo_disponible").val(total);
	}
	function restaFechas(f1,f2)
	{
	var aFecha1 = f1.split('-');
	var aFecha2 = f2.split('-');
	var fFecha1 = Date.UTC(aFecha1[2],aFecha1[1]-1,aFecha1[0]);
	var fFecha2 = Date.UTC(aFecha2[2],aFecha2[1]-1,aFecha2[0]);
	var dif = fFecha2 - fFecha1;
	var dias = dif / (1000 * 60 * 60 * 24);
	return dias;
	}
	function convertir_horas(num)
	{ 
		var horas = Math.floor(num / 60);  
		var minutos = num % 60;
		return horas + ":" + minutos;         
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
			var fecha_permisos=document.getElementById('fecha_permisos').value;
			var fecha_permisos_fin=document.getElementById('fecha_permisos_fin').value;			
			var dias_de_permiso= restaFechas(fecha_permisos,fecha_permisos_fin);
			console.log(dias_de_permiso);
			
			var a = moment(fecha_hora_inicio, "DD-MM-YYYY HH:mm");
			var b = moment(fecha_hora_fin, "DD-MM-YYYY HH:mm");
			
			var difDias = b.diff(a, 'days');
			var difMinutes = b.diff(a, 'minutes');                
			difMinutes = difMinutes-(difDias*1440);
			var dias = difDias;
			var horas = Math.floor( (difMinutes)/60 );
			var minutos = difMinutes-(horas*60);
			let horas_libres="";
			horas_libres=cantDiasLibres*8;
			if( horas >= 8 ){ dias=dias+1; horas=horas-8; }
			/*if(dias<10){ dias=dias; }
			if(horas<10){ horas=horas; }
			if(minutos<10){ minutos=minutos; }		*/
			
			var total= parseFloat((dias*8))+parseFloat(horas)+(parseFloat(minutos)/60);
			total=total-horas_libres;
			//Por Ley
			if (difDias <= 0 && total >= 8 ){
				document.getElementById('total_tiempo_permisos').value=8;
				document.getElementById('dias_permisos').value= 1;
				document.getElementById('horas_permisos').value=0;
				document.getElementById('minutos_permisos').value=0;

			}
			else{
				document.getElementById('total_tiempo_permisos').value=total;
				document.getElementById('dias_permisos').value= (dias > 1) ? dias+1-cantDiasLibres : dias-cantDiasLibres;
				document.getElementById('horas_permisos').value=horas;
				document.getElementById('minutos_permisos').value=minutos;
			}
		}
	}
	
	function calcular_disponible_permiso()
	{
		let horas_libres="";
		horas_libres=cantDiasLibres*8;
		var tiempo_disponible=document.getElementById('tiempo_permisos_inc').value;
		var tiempo_solicitado=document.getElementById('total_tiempo_permisos').value;            
		total = parseFloat(tiempo_disponible)-parseFloat(tiempo_solicitado);
		//document.getElementById('horas_permisos').value=tiempo_solicitado;
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
		//total = 0;
		$("#total_tard").val(total);
		document.getElementById("total_tard").value = valor;
	  

	}
	
	$("#Tardanza").hide();
	$("#Permiso").hide();
	$("#Ausencia").hide();
			$("#row_permiso").hide();

	$("#tipo_solicitud").change(function(){
		tipo = $("#tipo_solicitud").val();
		// tipo_permiso = $("#tipo_permiso").val();
		// alert(tipo_permiso);
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
			$("#tiempo_acumulet").hide();
			$("#row_permiso").show();
			$.get("ajax/select_expediente_tipo_permiso.php",function(rest){
				$("#tipo_permiso").empty();
				$("#tipo_permiso").append(rest);


			});
			// var e = document.getElementById("row_permiso tipo_permiso");
			// var strUser = e.options[e.selectedIndex].value;
			// console.log(strUser);
			//  if(strUser=='21'){
			//  }
		}
		//console.log(tipo);
		
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
		calcular_duracion_permiso();
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
<script>
	 $( document ).ready(function() {
	});
	window.addEventListener("load", function(){
		// var e = document.getElementById("tipo_permiso");
		// var strUser = e.options[e.selectedIndex].value;
		// if(strUser=='21'){
		//  	console.log(strUser);
		// }
		//console.log(strUser);
		$("#tipo_permiso").change(function(){
			tipop = $("#tipo_permiso").val();
			if (tipop=='21') {
				$("#tiempo_acumulet").show();
			}else{
				$("#tiempo_acumulet").hide();
			}
		
		});
    
	});
</script>
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
