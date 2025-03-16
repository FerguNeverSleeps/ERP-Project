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

$SQL = "SELECT  nompersonal.apenom FROM departamento
JOIN nompersonal on (nompersonal.cedula = departamento.IdJefe)
WHERE departamento.IdJefe = '".$_SESSION['_Jefe']."'";
$res1        = $conexion->query($SQL);
$jefe        = mysqli_fetch_assoc($res1);
//echo $SQL;

//PARAMETROS CORREO EMPRESA
$var_sql="SELECT * FROM nomempresa";
$resul_nomempresa=$conexion->query($var_sql);
$res=mysqli_fetch_assoc($resul_nomempresa);
$correo_sistemas2=$res['correo_sistemas2'];
$correo_sistemas2_password=$res['correo_sistemas2_password'];
$correo_sistemas2_remitente=$res['correo_sistemas2_remitente'];
$correo_sistemas2_host=$res['correo_sistemas2_host'];
$correo_sistemas2_puerto=$res['correo_sistemas2_puerto'];
$correo_sistemas2_modo=$res['correo_sistemas2_modo'];
$correo_usuario2=$res['correo_rrhh'];
$correo_usuario3=$res['correo_planilla'];
$correo_usuario4=$res['correo_sistemas1'];



$cedula      = isset($_SESSION['cedula_rhexpress'])? $_SESSION['cedula_rhexpress']: '';


$sql2        = "SELECT IFNULL(SUM(tiempo), 0) AS tiempo
FROM dias_incapacidad
WHERE cedula = '{$cedula}' AND tipo_justificacion = 3";
$time        = $conexion->query($sql2)->fetch_assoc();
$tiempo      = intval($time['tiempo']);
$horas       = 8;
$dias        = intval($tiempo/$horas);
$hora        = ($tiempo - ($dias*$horas));
$minutos     = ($tiempo - ($dias*$horas) - $hora);
//echo $tiempo," ",$dias," ",$horas," ",$minutos;
$con         = (isset($_REQUEST['con'])) ? $_REQUEST['con'] : 0 ;
$id          = (isset($_REQUEST['id'])) ? $_REQUEST['id'] : 0 ;
/////////////////////////////////////////////////////////////
$sql_datos="SELECT val.solicitudes_campos_id,cas.fecha_registro,cam.nombre,val.valores FROM solicitudes_valores as val
	LEFT JOIN solicitudes_casos as cas on cas.id_solicitudes_casos=val.solicitudes_casos_id
	LEFT JOIN solicitudes_campos as cam on cam.id_solicitudes_campos=val.solicitudes_campos_id
    WHERE solicitudes_casos_id='$id' ORDER by nombre DESC ";
///$result = $conexion->query($sql_datos)->fetch_assoc();
$sol_reclamo = $conexion->query($sql_datos);
$datos=array();
	
while ($filas = mysqli_fetch_array($sol_reclamo)) {
    $datos[]=$filas['valores'];
}
/////////////////////////////////////////////////////////////
$consulta_sql = "SELECT solicitudes_casos.*,nompersonal.*
FROM solicitudes_casos 
LEFT JOIN nompersonal on (solicitudes_casos.cedula = nompersonal.cedula)
WHERE solicitudes_casos.id_solicitudes_casos = '{$id}'";

$caso         = $conexion->query($consulta_sql)->fetch_assoc();
 $correo_colab=  $caso['email'];
if ($con == 1)
{
	$SQL_1          = "SELECT  * FROM  nompersonal
	WHERE cedula    = '".$_SESSION['cedula_rhexpress']."'";
	
	$persona           = $conexion->query($SQL_1)->fetch_assoc();
	
	$fecha_aprobacion  = (isset($_REQUEST['fecha_aprobacion'])) ? $_REQUEST['fecha_aprobacion'] : '' ;
	$aprobado          = (isset($_REQUEST['aprobado'])) ? $_REQUEST['aprobado'] : '' ;
	$observacion_jefe  = (isset($_REQUEST['observacion_jefe'])) ? $_REQUEST['observacion_jefe'] : '' ;
	$fecha_aprobacion = date('Y-m-d', strtotime($fecha_aprobacion)) ; // resta 6 mes

	if ($aprobado == 1) 
	{

		$update = "UPDATE solicitudes_casos
			SET fecha_aprobado_jefe     = '{$fecha_aprobacion}', 
			observacion_jefe            = '{$observacion_jefe}',  
			aprobado                    = '{$aprobado}',
			id_solicitudes_casos_status = '2' 
			WHERE id_solicitudes_casos  = '{$id}'";
			$aprobacion = $conexion->query($update);
			$tiempo       = $caso['tiempo'];
			$cedula       = $caso['cedula'];
			$horas_dia    = 8;
			$dias         = intval($tiempo/$horas_dia);
			$hora         = ($tiempo - ($dias*$horas));
			$minutos      = ($tiempo - ($dias*$horas) - $hora) * 60;
			$tiempo_nuevo = (($dias * $horas_dia) + $hora + ($minutos / 60))*(-1);

			// $q    = "INSERT INTO dias_incapacidad (cod_user, tipo_justificacion, fecha, tiempo,dias, horas, minutos, dias_restante, horas_restante, minutos_restante, observacion,cedula)
			// VALUES ('".$SESSION['ficha']."', '7', '{$fecha_aprobacion}', '{$tiempo_nuevo}', '{$dias}',  '{$hora}','{$minutos}', '{$dias}',  '{$hora}','{$minutos}', '{$observacion_rrhh}','{$cedula}')";			
			// $conexion->query($q);
			// $aprobacion = $conexion->query($update);

			$qexp ="INSERT INTO expediente (cedula, descripcion, fecha, usuario, horas,dias, minutos, tipo,subtipo,registro)
			VALUES ('{$cedula}', '{$observacion_jefe}', '{$fecha_aprobacion}', '$cedula', '$hora','{$dias}', '$minutos','83', '','{$id}')";
			$conexion->query($qexp);
			//PARAMETROS ENVIO CORREO
			$email=$correo_colab;
			$email2=$correo_usuario2;
			$email3=$correo_usuario3;
			$email4=$correo_usuario4;
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
			$asunto="SOLICITUD Nº: ".$id."  APROBADA";				
			$mail->Body = $asunto;
			$mail->IsHTML(true);
			// Se asigna la dirección de correo a donde se enviará el mensaje.
			$mail->AddAddress ($email);
			$mail->AddAddress ($email2);
			$mail->AddAddress ($email3);
			$mail->AddAddress ($email4);
			$mail->AddAttachment($ruta);
			if(!$mail->Send()) {
				echo $ruta." ".$email." Mailer Error: " . $mail->ErrorInfo;
			}
			echo "<script>alert('SOLICITUD APROBADA');location.href='rhexpress_bandeja_aprobados.php';</script>";
	}
	else
	{
		$update = "UPDATE solicitudes_casos
			SET observacion_jefe        = '{$observacion_jefe}', 
			id_solicitudes_casos_status = '3',
			aprobado                    = '{$aprobado}' 
			WHERE id_solicitudes_casos  = '{$id}'";	

			$aprobacion = $conexion->query($update);
			//PARAMETROS ENVIO CORREO
			$email=$correo_colab;
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
			$asunto="SOLICITUD Nº: ".$id." RECHAZADA";				
			$mail->Body = $asunto;
			$mail->IsHTML(true);
			// Se asigna la dirección de correo a donde se enviará el mensaje.
			$mail->AddAddress ($email);
			$mail->AddAttachment($ruta);
			if(!$mail->Send()) {
				echo $ruta." ".$email." Mailer Error: " . $mail->ErrorInfo;
			}
	}


	// if ($aprobado) 
	// {

	// //PARAMETROS ENVIO CORREO
	// $email=$correo_colab;
	// $mail = new PHPMailer();
	// $mail->SMTPAuth = true;
	// $mail->isSMTP();
	// $mail->SMTPDebug = 0;
	// $mail->Host = $correo_sistemas2_host;                        
	// $mail->Port = $correo_sistemas2_puerto;
	// $mail->SMTPSecure = $correo_sistemas2_modo;			
	// $mail->Username = $correo_sistemas2;
	// $mail->Password = $correo_sistemas2_password;
	// $mail->From = $correo_sistemas2_remitente;
	// $mail->FromName = "RRHH Solicitudes";
	// $tit='SOLICITUD ';
	
	// $mail->Subject = $tit;
	// if ($aprobado) 
	// {
	// 	$asunto="SOLICITUD Nº: ".$id."  APROBADA";
	// }
	// else
	// {
	// 	$asunto="SOLICITUD Nº: ".$id." RECHAZADA";
	// }	
	// $mail->Body = $asunto;
	// $mail->IsHTML(true);
	// // Se asigna la dirección de correo a donde se enviará el mensaje.
	// $mail->AddAddress ($email);
	// $mail->AddAttachment($ruta);
		

	// 	if(!$mail->Send()) {
	// 		echo $ruta." ".$email." Mailer Error: " . $mail->ErrorInfo;
	// 	} else {
	// 			if ($aprobado) 
	// 		  	{
	// 		    	//echo "<script>alert('SOLICITUD APROBADA');</script>";
	// 		     	echo "<script>alert('SOLICITUD APROBADA');location.href='rhexpress_bandeja_aprobados.php';</script>";
	// 		 	}
	// 		  	else
	// 		  	{
	// 		    	echo "<script>alert('SOLICITUD RECHAZADA');location.href='rhexpress_bandeja_rechazados.php';</script>";
	// 		    	//echo "<script>alert('SOLICITUD RECHAZADA');;</script>";
	// 		  	}

	// 	//echo "Message enviado!";
	// 	}
		$mail->ClearAddresses();
		$mail->ClearAttachments();


	//}


}
?>
<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <!-- BEGIN SAMPLE TABLE PORTLET-->
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-social-dribbble font-blue"></i>
                    <span class="caption-subject font-blue bold uppercase">SOLICITUD ACTUALIZACON ANUAL DE BIENES</span>
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
								<input name="fecha"  class="form-control input-circle" id="fecha" value="<?php echo $caso['fecha_registro']; ?>" disabled><p></p>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="form-group">
							<label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2 col-md-3 col-lg-offset-2 col-lg-3 control-label">Nombre:</label>
							<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
								<input name="txtdescripcion"  class="form-control input-circle" type="text" id="txtdescripcion" value="<?php echo $caso['apenom']; ?>" disabled><p></p>
							</div>
						</div>
					</div>

					<!-- <div class="row">
						<div class="form-group">
							<label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2 col-md-3 col-lg-offset-2 col-lg-3 control-label">Posición:</label>
							<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
								<input name="posicion"  class="form-control input-circle" type="text" id="posicion" value="<?php echo $caso['nomposicion_id']; ?>" disabled><p></p>
							</div>
						</div>
					</div> -->

					<div class="row">
						<div class="form-group">
							<label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2 col-md-3 col-lg-offset-2 col-lg-3 control-label">Cédula:</label>
							<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
								<input name="cedula"  class="form-control input-circle" type="text" id="cedula"  value="<?php echo $caso['cedula']; ?>" disabled><p></p>
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
					</div>
				</div>

			</div>
			<!-- FN DATOS -->

			<!-- INICIO TIEMPO SOLICITADO-->
			
            <div class="portlet box blue">
				<div class="portlet-title">
					<H4>Datos de articulo</H4>
				</div>
				<div class="portlet-body">
				<div class="row">
						<div class="form-group">
							<label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2
							col-md-3 col-lg-offset-2 col-lg-3 control-label">Salario empresa:</label>
							<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
								<input name="dias_solicitados" class="form-control input-circle" type="text" id="dias_solicitados" value="<?php echo $datos['6']; ?>" disabled><p></p>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="form-group">
							<label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2
							col-md-3 col-lg-offset-2 col-lg-3 control-label">Vivienda principal:</label>
							<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
								<input name="horas_solicitadas" class="form-control input-circle" type="text" id="horas_solicitadas" value="<?php echo $datos['0']; ?>" disabled><p></p>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="form-group">
							<label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2
							col-md-3 col-lg-offset-2 col-lg-3 control-label">Valor vivienda principal:</label>
							<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
								<input name="minutos_solicitados" class="form-control input-circle" type="text" id="minutos_solicitados" value="<?php echo $datos['1']; ?>" disabled><p></p>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="form-group">
							<label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2
							col-md-3 col-lg-offset-2 col-lg-3 control-label">Automovil:</label>
							<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
								<input name="dias_solicitados" class="form-control input-circle" type="text" id="dias_solicitados" value="<?php echo $datos['22']; ?>" disabled><p></p>
							</div>
						</div>
					</div>


					<div class="row">
						<div class="form-group">
							<label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2
							col-md-3 col-lg-offset-2 col-lg-3 control-label">Valor Automovil:</label>
							<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
								<input name="minutos_solicitados" class="form-control input-circle" type="text" id="minutos_solicitados" value="<?php echo $datos['2']; ?>" disabled><p></p>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="form-group">
							<label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2
							col-md-3 col-lg-offset-2 col-lg-3 control-label">Tipo adquiriencia:</label>
							<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
								<input name="horas_solicitadas" class="form-control input-circle" type="text" id="horas_solicitadas" value="<?php echo $datos['3']; ?>" disabled><p></p>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="form-group">
							<label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2
							col-md-3 col-lg-offset-2 col-lg-3 control-label">Terrenos:</label>
							<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
								<input name="saldo" class="form-control input-circle" type="text" id="saldo" value="<?php  echo $datos['4']; ?>" disabled><p></p>
							</div>
						</div>
					</div>
                    <div class="row">
						<div class="form-group">
							<label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2
							col-md-3 col-lg-offset-2 col-lg-3 control-label">Monto Terrenos:</label>
							<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
								<input name="saldo" class="form-control input-circle" type="text" id="saldo" value="<?php  echo ($datos['8']); ?>" disabled><p></p>
							</div>
						</div>
					</div>
                    <div class="row">
						<div class="form-group">
							<label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2
							col-md-3 col-lg-offset-2 col-lg-3 control-label">Dividendo por acciones:</label>
							<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
								<input name="saldo" class="form-control input-circle" type="text" id="saldo" value="<?php  echo ($datos['19']); ?>" disabled><p></p>
							</div>
						</div>
					</div>
                    <div class="row">
						<div class="form-group">
							<label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2
							col-md-3 col-lg-offset-2 col-lg-3 control-label">Monto dividendo por acciones:</label>
							<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
								<input name="saldo" class="form-control input-circle" type="text" id="saldo" value="<?php  echo ($datos['12']); ?>" disabled><p></p>
							</div>
						</div>
					</div>
                    <div class="row">
						<div class="form-group">
							<label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2
							col-md-3 col-lg-offset-2 col-lg-3 control-label">Intereses de cuentas:</label>
							<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
								<input name="saldo" class="form-control input-circle" type="text" id="saldo" value="<?php  echo ($datos['17']); ?>" disabled><p></p>
							</div>
						</div>
					</div>
                    <div class="row">
						<div class="form-group">
							<label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2
							col-md-3 col-lg-offset-2 col-lg-3 control-label">Monto intereses de cuentas:</label>
							<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
								<input name="saldo" class="form-control input-circle" type="text" id="saldo" value="<?php  echo ($datos['10']); ?>" disabled><p></p>
							</div>
						</div>
					</div>
                    <div class="row">
						<div class="form-group">
							<label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2
							col-md-3 col-lg-offset-2 col-lg-3 control-label">Herencia:</label>
							<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
								<input name="saldo" class="form-control input-circle" type="text" id="saldo" value="<?php  echo ($datos['18']); ?>" disabled><p></p>
							</div>
						</div>
					</div>  
					<div class="row">
						<div class="form-group">
							<label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2
							col-md-3 col-lg-offset-2 col-lg-3 control-label">Monto herencia:</label>
							<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
								<input name="saldo" class="form-control input-circle" type="text" id="saldo" value="<?php  echo ($datos['11']); ?>" disabled><p></p>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="form-group">
							<label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2
							col-md-3 col-lg-offset-2 col-lg-3 control-label">Comisiones:</label>
							<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
								<input name="saldo" class="form-control input-circle" type="text" id="saldo" value="<?php  echo ($datos['20']); ?>" disabled><p></p>
							</div>
						</div>
					</div> 
					<div class="row">
						<div class="form-group">
							<label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2
							col-md-3 col-lg-offset-2 col-lg-3 control-label">Monto comisiones:</label>
							<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
								<input name="saldo" class="form-control input-circle" type="text" id="saldo" value="<?php  echo ($datos['13']); ?>" disabled><p></p>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="form-group">
							<label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2
							col-md-3 col-lg-offset-2 col-lg-3 control-label">Servicios profesionales:</label>
							<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
								<input name="saldo" class="form-control input-circle" type="text" id="saldo" value="<?php  echo ($datos['5']); ?>" disabled><p></p>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="form-group">
							<label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2
							col-md-3 col-lg-offset-2 col-lg-3 control-label">Monto servicios profesionales:</label>
							<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
								<input name="saldo" class="form-control input-circle" type="text" id="saldo" value="<?php  echo ($datos['9']); ?>" disabled><p></p>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="form-group">
							<label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2
							col-md-3 col-lg-offset-2 col-lg-3 control-label">Dicha funcion presenta conflicto de Interes con la empresa?:</label>
							<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
								<input name="saldo" class="form-control input-circle" type="text" id="saldo" value="<?php  echo ($datos['19']); ?>" disabled><p></p>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="form-group">
							<label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2
							col-md-3 col-lg-offset-2 col-lg-3 control-label">Monto aproximado que devenga:</label>
							<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
								<input name="saldo" class="form-control input-circle" type="text" id="saldo" value="<?php  echo ($datos['15']); ?>" disabled><p></p>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="form-group">
							<label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2
							col-md-3 col-lg-offset-2 col-lg-3 control-label">Presenta usted declaracion de renta?::</label>
							<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
								<input name="saldo" class="form-control input-circle" type="text" id="saldo" value="<?php  echo ($datos['7']); ?>" disabled><p></p>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- FIN TIEMPO SOLICITADO-->
			<!-- INICIO USO TIEMPO -->
			<form action="" method="post" name="tiempo_compensatorio" id="tiempo_compensatorio" >
			<div class="form-body">

				<!-- <input name="btn_guardar" type="Hidden" id="op_tp" value="<?= $btn_guardar; ?>">
					<div class="portlet box blue">
						<div class="portlet-title">
							<H4>Vacaciones</H4>
						</div>
						<div class="portlet-body">
							<div class="row">
								<div class="form-group">
                                    <label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2
									col-md-3 col-lg-offset-2 col-lg-3 control-label">Fecha Inicio:</label>
                                    <div class="col-md-3">
                                            <input id="fecha_inicio" name="fecha_inicio" type="text" size="16" class="form-control input-circle" value="<?php  echo $caso['fecha_inicio']; ?>" disabled>
                                           
                                        <p></p>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
								<div class="form-group">
                                    <label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2
									col-md-3 col-lg-offset-2 col-lg-3 control-label">Fecha Reintegro:</label>
                                    <div class="col-md-3">
                                        <div class="iform-control input-circle">
                                            <input id="fecha_fin" name="fecha_fin" type="text" size="16" class="form-control input-circle" value="<?php  echo $caso['fecha_fin']; ?>" disabled>
                                           
                                        </div><p></p>
                                    </div>
                                </div>
                            </div>

							<div class="row">
								<div class="form-group">
									<label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2
									col-md-3 col-lg-offset-2 col-lg-3 control-label">Observaciones</label>
									<div class="col-xs-7 col-sm-7 col-md-7 col-lg-7">
										<textarea cols="5" rows="5" name="observacion"  class="form-control" type="text" id="observacion" value="<?php ?>" disabled><?php echo $caso['observacion']; ?></textarea>
									</div>
								</div>
							</div>
						</div>
					</div> -->
					<!-- FIN USO TIEMPO -->
					<!-- INICIO USO TIEMPO -->
					<div class="portlet box blue">
						<div class="portlet-title">
							<H4>Aprobación Jefe Inmediato</H4>
						</div>
						<div class="portlet-body">
							<div class="row">
								<div class="form-group">
                                    <label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2
									col-md-3 col-lg-offset-2 col-lg-3 control-label">Fecha:</label>
                                    <div class="col-md-3">
                                        <input name="fecha_aprobacion"  class="form-control input-circle" id="fecha_aprobacion" value="<?php echo date('d-m-Y'); ?>" readonly><p></p>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
								<div class="form-group">
                                    <label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2
									col-md-3 col-lg-offset-2 col-lg-3 control-label">Persona que aprueba:</label>
                                    <div class="col-md-3">
                                        <input id="persona_aprbacion" name="persona_aprbacion" type="text" size="16" class="form-control input-circle" value="<?php echo $jefe['apenom']; ?>" disabled>
                                            
                                        <p></p>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                            	<div class="form-group">
                            		<label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2
									col-md-3 col-lg-offset-2 col-lg-3 control-label">Aprobado?</label>                            		
                                <div class="col-md-3">
                                    <select id="aprobado" name="aprobado"  class="form-control input-circle">
                                    	<option value="1">SI</option>
                                    	<option value="0">NO</option>
                                    </select>
                                    <p></p>
                                </div>      
                                </div>                      	
                            </div>

							<div class="row">
								<div class="form-group">
									<label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2
									col-md-3 col-lg-offset-2 col-lg-3 control-label">Observaciones</label>
									<div class="col-xs-7 col-sm-7 col-md-7 col-lg-7">
										<textarea cols="5" rows="5" name="observacion_jefe"  class="form-control" type="text" id="observacion_jefe" value="<?php ?>" required></textarea>
									</div>
								</div>
							</div>
						</div>
					</div>
					<!-- FIN USO TIEMPO -->
					<br>
					<div class="row">
						<input type="Hidden" name="con" value="1">
						<input type="Hidden" name="tiempo" value="<?php ?>">
						<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
						</div>
						<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
							<button class="btn btn-primary" onclick="document.tiempo_compensatorio.submit();"> Guardar</button>
						</div>

						<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
							<a class="btn btn-danger" href="rhexpress_bandeja_aprobados.php"> Salir</a>
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
$(document).ready(function(){
	$.get("ajax/ajax_tiempo_compensatorio_disponible.php",function(res){
		tiempo  = parseInt(res);
		horas   = 8;
		dias    = tiempo/horas;
		dias    = parseInt(dias);
		hora    = parseInt(tiempo - (dias*horas));
		minutos = parseInt(tiempo - (dias*horas) - hora);
		//$("#").value(tiempo);
		$("#dias_disponibles").empty(dias);

		$("#dias_disponibles").val(dias);
		$("#horas_disponibles").val(hora);
		$("#minutos_disponibles").val(minutos);
		$("#tiempo_disponible").val(tiempo);
		//console.log(tiempo+" "+horas+" "+dias+" "+hora+" "+minutos);
	});
});
</script>
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="assets/pages/scripts/components-date-time-pickers.js" type="text/javascript"></script>
<script type="text/javascript">
    $(".form_datetime3").datetimepicker({format: 'dd-mm-yyyy hh:ii:ss'});
</script>     
<!-- END PAGE LEVEL SCRIPTS -->
<?php
 include("config/end.php"); ?>
