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
$tipo=$_GET['tipo'];

$SQL = "SELECT  nompersonal.apenom,nompersonal.email FROM departamento
JOIN nompersonal on (nompersonal.cedula = departamento.IdJefe)
WHERE departamento.IdJefe = '".$_SESSION['_Jefe']."'";
//echo $SQL;

$var_sql="SELECT * FROM nomempresa";
$resul_nomempresa=$conexion->query($var_sql);
$res=mysqli_fetch_assoc($resul_nomempresa);
$correo_sistemas2=$res['correo_sistemas2'];
$correo_sistemas2_password=$res['correo_sistemas2_password'];
$correo_sistemas2_remitente=$res['correo_sistemas2_remitente'];
$correo_sistemas2_host=$res['correo_sistemas2_host'];
$correo_sistemas2_puerto=$res['correo_sistemas2_puerto'];
$correo_sistemas2_modo=$res['correo_sistemas2_modo'];

$cedula = isset($_SESSION['cedula_rhexpress'])? $_SESSION['cedula_rhexpress']: '';
$res1   = $conexion->query($SQL);
$jefe   = mysqli_fetch_assoc($res1);
$correo_jefe=$jefe['email'];
$sql2   = "SELECT ifnull(ROUND( SUM( tiempo ) , 2 ),0) AS  tiempo, dias, horas, minutos
FROM dias_incapacidad
WHERE cedula = '{$cedula}' AND tipo_justificacion = 7";
$time    = $conexion->query($sql2)->fetch_assoc();
$tiempo  = $time['tiempo']*(-1);

$sql_nivel1="SELECT sexo,n.descrip as centro_costo FROM nompersonal as p 
LEFT JOIN nomnivel1 as n on p.codnivel1=n.codorg
WHERE cedula ='".$_SESSION['cedula_rhexpress']."'";
$res_nivel1   = $conexion->query($sql_nivel1);
$nivel1=mysqli_fetch_assoc($res_nivel1);


$sql="SELECT id_solicitudes_campos,nombre,tipo_campo,valores_por_Defecto,orden 
FROM solicitudes_campos 
where solicitudes_tipo_id =$tipo ORDER BY `orden` ASC";
$result=$conexion->query($sql);

$sql="SELECT id_solicitudes_campos
FROM solicitudes_campos WHERE solicitudes_tipo_id = '6'";
$cantidad_campos = $conexion->query($sql);

$sql="SELECT correlativo FROM solicitudes_tipos WHERE id_solicitudes_tipos='6'";
$correlativo_numero=$conexion->query($sql)->fetch_assoc();
$c=$correlativo_numero['correlativo'];

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
	$SQL_1       = "SELECT  * FROM  nompersonal
	WHERE cedula = '".$_SESSION['cedula_rhexpress']."'";

	$persona           = $conexion->query($SQL_1)->fetch_assoc();
	
	$tiempo            = (isset($_REQUEST['tiempo'])) ? $_REQUEST['tiempo'] : '' ;
	$fecha_registro    = (isset($_REQUEST['fecha'])) ? $_REQUEST['fecha'] : '' ;
	$tipo_solicitud_pcd    = (isset($_REQUEST['tipo_prestamo'])) ? $_REQUEST['tipo_prestamo'] : '' ;
	$fecha_inicio      = (isset($_REQUEST['fecha_inicio'])) ? $_REQUEST['fecha_inicio'] : '' ;
	$fecha_fin         = (isset($_REQUEST['fecha_fin'])) ? $_REQUEST['fecha_fin'] : '' ;
	$observacion       = (isset($_REQUEST['observacion'])) ? $_REQUEST['observacion'] : '' ;
	$dias              = (isset($_REQUEST['dias_solicitados'])) ? $_REQUEST['dias_solicitados'] : '' ;
	$horas             = (isset($_REQUEST['horas_solicitadas'])) ? $_REQUEST['horas_solicitadas'] : '' ;
	$minutos           = (isset($_REQUEST['minutos_solicitados'])) ? $_REQUEST['minutos_solicitados'] : '' ;
	$tiempo_solicitado = (isset($_REQUEST['tiempo_solicitado'])) ? $_REQUEST['tiempo_solicitado'] : '' ;
	$hora_inicio       = (isset($_REQUEST['hora_inicio'])) ? $_REQUEST['hora_inicio'] : '00:00:00' ;
	$hora_fin          = (isset($_REQUEST['hora_fin'])) ? $_REQUEST['hora_fin'] : '00:00:00' ;
	$fecha_ini         = date('Y-m-d H:i:s', strtotime($fecha_inicio)) ; // resta 6 mes
	$fecha_fini        = date('Y-m-d H:i:s', strtotime($fecha_fin)) ; // resta 6 mes


	if (isset($_POST['fecha'])) {
		$c++;		
		
		$insertar = "INSERT INTO solicitudes_casos (id_solicitudes_casos, cedula, id_tipo_caso, id_departamento,id_tipo_solicitud, fecha_registro, fecha_inicio, fecha_fin, observacion, id_solicitudes_casos_status, tiempo,dias, horas, minutos)
		VALUES ('','".$_SESSION['cedula_rhexpress']."','6','".$persona['IdDepartamento']."',{$tipo_solicitud_pcd},NOW(),'{$fecha_ini}','{$fecha_fini}','{$observacion}','1','{$dias}','{$dias}','{$horas}','{$minutos}');";
		$insertado = $conexion->query($insertar);
		
		$ultimoId=mysqli_insert_id($conexion);	
	
		$horas_dia    = 8;
		$dias         = intval($tiempo/$horas_dia);
		$hora         = ($tiempo - ($dias*$horas));
		$minutos      = ($tiempo - ($dias*$horas) - $hora) * 60;
		$tiempo_nuevo = (($dias * $horas_dia) + $hora + ($minutos / 60))*(-1);
	
		if ($insertado) 
		  {
			$actualizar_correlativo="UPDATE solicitudes_tipos SET correlativo=$c
			WHERE id_solicitudes_tipos='6'";
			$actualizado=$conexion->query($actualizar_correlativo);

			while ($campos_valores=$cantidad_campos->fetch_assoc()) {
				//$sql="INSERT INTO ";
				//$campos_valores['id_solicitudes_campos']=$_POST['id_solicitudes_campos'];
				$campo = $campos_valores['id_solicitudes_campos'];
				$valor = $_POST[$campo];
				$solicitudes_valores="INSERT INTO solicitudes_valores(id_solicitudes_valores,solicitudes_casos_id,solicitudes_campos_id,valores) VALUES ('',$ultimoId,$campo,'$valor')";
				$valores=$conexion->query($solicitudes_valores);	
			}
			
		
			// //enviando correo al jefe
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
			
			$asunto="SOLICITUD DE PRESTAMOS COMPRAS Y CREDITOS DEL COLABORADOR ".$_SESSION['nombre_rhexpress'].", POR FAVOR DIRIGIRSE BANDEJA DE ENTRADA PARA APROBAR O RECHAZAR.";
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

			 echo "<script>alert('SOLICITUD DE PRESTAMOS COMPRAS CREDITOS GUARDADA EXITOSAMENTE');location.href='rhexpress_bandeja_entrada.php';</script>";
		 }
		  else
		  {
			echo "<script>alert('HUBO UN ERROR AL PROCESAR SU SOLICITUD');location.href='rhexpress_bandeja_entrada.php';</script>";
			//echo "<script>alert('SOLICITUD RECHAZADA');;</script>";
		  }
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
                    <span class="caption-subject font-blue bold uppercase">SOLICITUD DE PRESTAMOS, COMPRAS Y CREDITOS</span>
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
				<h4> Datos del Colaborador</h4>
			</div>
			<div class="portlet-body">
				<form action="" enctype="multipart/form-data" method="post" name="solicitud_vacaciones" id="solicitud_vacaciones" role="form" >
					<div class="row">
						<div class="form-group">
							<label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2 col-md-3 col-lg-offset-2 col-lg-3 control-label">Fecha de Solicitud:</label>
							<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
								<input name="fecha"  class="form-control input-circle" id="fecha" value="<?php echo date('d-m-Y'); ?>" readonly><p></p>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="form-group">
							<label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2 col-md-3 col-lg-offset-2 col-lg-3 control-label">Nivel Organizacional</label>
							<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
								<input name="txtdescripcion"  class="form-control input-circle" type="text" id="txtdescripcion" value="<?php echo $nivel1['centro_costo']; ?>" disabled><p></p>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="form-group">
							<label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2 col-md-3 col-lg-offset-2 col-lg-3 control-label">Colaborador:</label>
							<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
								<input name="colaborador"  class="form-control input-circle" type="text" id="colaborador" value="<?= $_SESSION['ficha_rhexpress'] ?>" disabled><p></p>
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

					<!-- <div class="row">
						<div class="form-group">
							<label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2
							col-md-3 col-lg-offset-2 col-lg-3 control-label">Unidad Administrativa:</label>
							<div class="col-xs-7 col-sm-7 col-md-7 col-lg-7">
								<input name="unidad_administrativa"  class="form-control input-circle" type="text" id="unidad_administrativa" value="<?= $_SESSION['gerencia_rhexpress'] ?>"><p></p>
							</div>
						</div>
					</div> -->

					<div class="row">
						<div class="form-group">
							<label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2
							col-md-3 col-lg-offset-2 col-lg-3 control-label">Departamento Workflow:</label>
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
					<p></p>
					<div class="row">
						<div class="form-group">
							<label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2
							col-md-3 col-lg-offset-2 col-lg-3 control-label">Tipo de Solicitud:</label>
							<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
								<select name="tipo_prestamo" class="form-control input-circle" type="text" id="tipo_prestamo">
									<option value="">Seleccione...</option>
									<option value="1">Préstamo</option>
									<option value="2">De contado</option>
									<option value="3">Crédito</option>																											
								</select>									
							</div>
						</div>
					</div>
					<p></p>
					<?php
						while($campos=mysqli_fetch_array($result)) {
							if ($campos['tipo_campo']=='text') {?>	
							<div class="row" id="<?php echo $campos['id_solicitudes_campos']?>">
								<div class="form-group">						
									<label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2 col-md-3 col-lg-offset-2 col-lg-3 control-label"><?php echo $campos['nombre']?></label>
									<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
										<input name="<?php echo $campos['id_solicitudes_campos']?>"  class="form-control input-circle" style="margin-top:12px;" type="text" id="<?php echo $campos['id_solicitudes_campos']?>" ><p></p>
									</div>
								</div>
							</div>
							<?php
							}
							if ($campos['tipo_campo']=='number') {?>
							<div class="row">
								<div class="form-group">	
									<label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2 col-md-3 col-lg-offset-2 col-lg-3 control-label"><?php echo $campos['nombre']?></label>
									<div class="col-xs-7 col-sm-7 col-md-7 col-lg-7">
										<input name="<?php echo $campos['id_solicitudes_campos']?>"  class="form-control input-circle" type="number" id="<?php echo $campos['id_solicitudes_campos']?>" value="0"><p></p>
									</div>
								</div>
							</div>
							<?php
							}
							if ($campos['tipo_campo']=='textarea') {?>
							<div class="row" id="<?php echo $campos['id_solicitudes_campos']?>">
								<div class="form-group">	
									<label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2 col-md-3 col-lg-offset-2 col-lg-3 control-label"><?php echo $campos['nombre']?></label>
									<div class="col-xs-7 col-sm-7 col-md-7 col-lg-7">
										<textarea cols="5" rows="5" name="<?php echo $campos['id_solicitudes_campos']?>"  class="form-control input-circle" type="text" id="<?php echo $campos['id_solicitudes_campos']?>" value="<?php ?>" ></textarea>
									</div>
								</div>
							</div>
								<?php
							}
							if ($campos['tipo_campo']=='select') {?>
								<div class="row">
									<div class="form-group">
										<select name="tipo_campo" class="form-control" type="text" id="tipo_solicitud" >
											<option value="<?php echo $campos['tipo_campo'] ?>"><?php echo $campos['tipo_campo'] ?></option>
												<option value="text">text</option>
												<option value="select">select</option>
												<option value="number">number</option>
												<option value="checkbox">checkbox</option>
											<option value="textarea">textarea</option>                                                  										                                                  										                                                  										                                                  										                                                  										                                                  										                                                  										                                                  										
										</select>
									</div>
								</div>
								<?php
							}	
							if ($campos['tipo_campo']=='checkbox') {?>
							<?php $campo=explode(",",$campos['valores_por_Defecto']);
								$campo['0'];
								$campo['1'];
							?>
								<div class="row" id="<?php echo $campos['id_solicitudes_campos']?>">
									<div class="form-group">
										<label class="form-check form-check-inline text-right col-xs-offset-2 col-xs-6 col-sm-offset-2 col-sm-6 col-md-offset-2 col-md-4 col-lg-offset-2 col-lg-3 control-label" id="check">
										<?php echo $campos['nombre']?></label>
										<div class="form-check form-check-inline">
											<input class="form-check-input" type="radio" name="<?php echo $campos['id_solicitudes_campos']?>" value="1">
											<label class="form-check-label" for="inlineRadio1"><?php echo $campo['0']?></label>
										
											<input class="form-check-input" type="radio" name="<?php echo $campos['id_solicitudes_campos']?>" value="2">
											<label class="form-check-label" for="inlineRadio2"><?php echo $campo['1']?></label>
										</div>
									</div>
								</div>
								<?php
							}						
						}
					?>
					<!-- <div class="row">
						<div class="form-group">
							<label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2 col-md-3 col-lg-offset-2 col-lg-3 control-label">Quincena que reclama:</label>
							<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
								<input name="quincena"  class="form-control input-circle" type="text" id="cedula" ><p></p>
							</div>
						</div>
					</div>  -->

					<!-- <div class="row">
						<div class="form-group">
							<label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2
							col-md-3 col-lg-offset-2 col-lg-3 control-label">Monto:</label>
							<div class="col-xs-7 col-sm-7 col-md-7 col-lg-7">
								<input name="monto"  class="form-control input-circle" type="text" id="unidad_administrativa"><p></p>
							</div>
						</div>
					</div> -->
                    <div class="row">
								<div class="form-group">
									<!-- <label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2
									col-md-3 col-lg-offset-2 col-lg-3 control-label">Comentario del encargado:</label>
									<div class="col-xs-7 col-sm-7 col-md-7 col-lg-7">
										<textarea cols="5" rows="5" name="comentario"  class="form-control" type="text" id="observacion" value="<?php ?>"></textarea>
									</div> -->
								</div>
							</div>
					<!-- <div class="row">
						<div class="form-group">
							<label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2
							col-md-3 col-lg-offset-2 col-lg-3 control-label">Departamento Adscrito:</label>
							<div class="col-xs-7 col-sm-7 col-md-7 col-lg-7">
								<input name="unidad_administrativa"  class="form-control input-circle" type="text" id="unidad_administrativa"
								value="<?= $_SESSION['_Departamento'] ?>"><p></p>
							</div>
						</div>
					</div> -->

					<!-- <div class="row">
						<div class="form-group">
							<label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2
							col-md-3 col-lg-offset-2 col-lg-3 control-label">Jefe Inmediato:</label>
							<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
								<input name="jefe"  class="form-control input-circle" type="text" id="jefe"
								value="<?php echo $jefe['apenom']; ?>">
							</div>
						</div>
					</div><p></p> -->
					<!-- <div class="row">
						<div class="form-group">
							<label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2
							col-md-3 col-lg-offset-2 col-lg-3 control-label">Tiempo Acumulado (Días):</label>
							<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
								<input name="tiempo_acumulado" class="form-control input-circle" type="text" id="tiempo_acumulado" value="<?php echo $time['tiempo'];?>"><p></p>
							</div>
						</div>
					</div> -->
				</div>

			</div>
			<!-- FN DATOS -->


			
				<input name="btn_guardar" type="Hidden" id="op_tp" value="<?= $btn_guardar; ?>">
				<div class="form-body">
					
					<!-- FIN USO TIEMPO -->
					<br>
						<div class="row">
							<input type="Hidden" name="con" value="1">
							<input type="Hidden" name="tiempo" value="<?php ?>">
							<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
							</div>
							<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
								<button class="btn btn-primary" onclick="document.solicitud_prestamos_compras_creditos.submit();"> Guardar</button>
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
	// function getMontoPrestamo()
	// {
	// 	var monto = document.getElementById('4').value;
	// 	monto=monto/6;
	// 	document.getElementById('5').value=monto;
	// }
	// function getmonto() {
	// 	var monto=$("#4").val();
	// 	monto=monto/6;
	// 	$("#5").val(monto);
		
	// }
	
	// $("#5").on('keyup',function(){
	// 	getmonto();
	// });
	/*$("#minutos_solicitados").on('keyup',function(){
		variables()
	});*/
	


	$("#4").hide();
	$("#5").hide();
	$("#6").hide();
	$("#7").hide();
	$("#8").hide();
	$("#9").hide();
	$("#10").hide();
	$("#11").hide();
	$("#12").hide();
	$("#13").hide();
	$("#14").hide();
	$("#15").hide();
	$("#37").hide();
	$("#38").hide();
	$("#42").hide();
	$("#43").hide();
	$("#44").hide();

	/*$("#4").on('blur',function(){
		console.log($('#4').val());
		console.log("lo hizo");
	});*/
	
	$("#tipo_prestamo").change(function(){
		if ($(this).val() == "1") 
		{
			$("#4").show();
			$("#5").show();
			$("#6").show();
			$("#7").hide();
			$("#8").hide();
			$("#9").hide();
			$("#10").hide();
			$("#11").hide();
			$("#42").hide();
			$("#13").hide();
			$("#14").hide();
			$("#15").hide();
			$("#37").hide();			
			$("#12").hide();
			$("#43").hide();
			$("#44").hide();			
		}
		if ($(this).val() == "2") 
		{
			$("#4").show();
			$("#42").show();
			$("#5").hide();
			$("#6").show();
			$("#7").hide();
			$("#8").show();
			$("#9").show();
			$("#10").show();
			$("#11").show();
			$("#12").show();
			$("#13").show();
			$("#14").show();
			$("#15").show();
			$("#37").show();
			$("#43").show();
			$("#42").show();
		}
		if($(this).val() == "3")
		{
			$("#4").show();
			$("#5").hide();
			$("#6").show();
			$("#7").hide();
			$("#8").hide();
			$("#9").hide();
			$("#10").hide();
			$("#11").show();
			$("#12").hide();
			$("#13").hide();
			$("#14").show();
			$("#15").show();
			$("#37").show();	
			$("#42").show();
			$("#43").show();
		}
		
	});
	
	$('#solicitud_vacaciones #6').blur(function(e){
		// var selectedOption = $("input:radio[name=12]:checked").val()
		// if (selectedOption=='1') {
		// 	var monto=$("input:text[name=4]").val();
		// 	var total=0;
		// 	total=monto*10/100;
		// 	$("input:text[name=9]").val(Number(monto)-Number(total));
		// 	$('#solicitud_vacaciones #42').hide();
		// 	$('#solicitud_vacaciones #14').hide();
		// 	$('#solicitud_vacaciones #13').hide();
		// 	$('#solicitud_vacaciones #8').hide();
		// }else{
		// 	var monto=$("input:text[name=4]").val();
		// 	var total=0;
		// 	total=monto*20/100;
		// 	$("input:text[name=8]").val(Number(monto)-Number(total));
		// 	$('#solicitud_vacaciones #43').hide();
		// 	$('#solicitud_vacaciones #15').hide();
		// 	$('#solicitud_vacaciones #9').hide();
		// }
		// var monto=$("input:text[name=9]").val();
		// var total=0;
		// total=monto*7/100;
		// $("input:text[name=10]").val(Number(monto)+Number(total));
			

	});
	$('#solicitud_vacaciones #4').blur(function(e){
		let montoPrestamo = parseInt(e.target.value);			
		var e = document.getElementById("tipo_prestamo");
		var strUser = e.options[e.selectedIndex].value;

		if (strUser=='1') {
			if (montoPrestamo > 150 || montoPrestamo =='') {
				alert('el monto ingresado no puede ser mayor a 150 o estar vacio');
			} else {
				$('#solicitud_vacaciones #5').val(parseFloat(montoPrestamo/6).toFixed(2));
			}
		 }	
		});
		$('#solicitud_vacaciones #6').blur(function(e){
			var e = document.getElementById("tipo_prestamo");
			var strUser = e.options[e.selectedIndex].value;
			if(strUser=='2'){
	 			var selectedOption = $("input:radio[name=12]:checked").val()
	 			if (selectedOption=='1') {
	 				var monto=$("input:text[name=4]").val();
	 				var total=0;
	 				total=monto*10/100;
	 				$("input:text[name=9]").val(Number(monto)-Number(total));
	 				$('#solicitud_vacaciones #42').hide();
	 				$('#solicitud_vacaciones #14').hide();
	 				$('#solicitud_vacaciones #13').hide();
	 				$('#solicitud_vacaciones #8').hide();
					var i=$("input:text[name=9]").val();
					var totali=0;
					totali=i*7/100;
					$("input:text[name=10]").val(Number(i)+Number(totali));
	 			}else{
	 			var monto=$("input:text[name=4]").val();
	 			var total=0;
	 			total=monto*20/100;
	 			$("input:text[name=8]").val(Number(monto)-Number(total));
	 			$('#solicitud_vacaciones #43').hide();
	 			$('#solicitud_vacaciones #15').hide();
	 			$('#solicitud_vacaciones #9').hide();
				 var i=$("input:text[name=8]").val();
					var totali=0;
					totali=i*7/100;
					$("input:text[name=10]").val(Number(i)+Number(totali));
	 			}			
	 		}
		});
		$('#solicitud_vacaciones #37').blur(function(e){
			var e = document.getElementById("tipo_prestamo");
			var strUser = e.options[e.selectedIndex].value;
			if(strUser=='3'){
				var selectedOption = $("input:radio[name=12]:checked").val()
	 			if (selectedOption=='1') {	 			
	 				$('#solicitud_vacaciones #42').hide();
	 				$('#solicitud_vacaciones #14').hide();
					 var i=$("input:text[name=4]").val();
						var totali=0;
						totali=i*7/100;
						$("input:text[name=10]").val(Number(i)+Number(totali));
	 			}else{	 			
	 			$('#solicitud_vacaciones #43').hide();
	 			$('#solicitud_vacaciones #15').hide();	 			
				 var i=$("input:text[name=4]").val();
				 var totali=0;
				 totali=i*7/100;
				 $("input:text[name=10]").val(Number(i)+Number(totali));
				}
			}
	 				
		});
	// 	}else if(strUser=='2'){
	// 			var selectedOption = $("input:radio[name=12]:checked").val()
	// 			if (selectedOption=='1') {
	// 				var monto=$("input:text[name=4]").val();
	// 				var total=0;
	// 				total=monto*10/100;
	// 				$("input:text[name=9]").val(Number(monto)-Number(total));
	// 				$('#solicitud_vacaciones #42').hide();
	// 				$('#solicitud_vacaciones #14').hide();
	// 				$('#solicitud_vacaciones #13').hide();
	// 				$('#solicitud_vacaciones #8').hide();
	// 			}else{
	// 			var monto=$("input:text[name=4]").val();
	// 			var total=0;
	// 			total=monto*20/100;
	// 			$("input:text[name=8]").val(Number(monto)-Number(total));
	// 			$('#solicitud_vacaciones #43').hide();
	// 			$('#solicitud_vacaciones #15').hide();
	// 			$('#solicitud_vacaciones #9').hide();
	// 			}
	// 		var monto=$("input:text[name=9]").val();
	// 		var total=0;
	// 		total=monto*7/100;
	// 		$("input:text[name=10]").val(Number(monto)+Number(total));
			
	// 	}else if(strUser=='3'){
	// 		var selectedOption = $("input:radio[name=12]:checked").val()
	// 			if (selectedOption=='1') {
	// 			var monto=$("input:text[name=4]").val();
	// 			var total=0;
	// 			total=monto*10/100;
	// 			$("input:text[name=9]").val(Number(monto)-Number(total));
	// 			$('#solicitud_vacaciones #42').hide();
	// 			$('#solicitud_vacaciones #14').hide();
	// 			$('#solicitud_vacaciones #13').hide();
	// 			$('#solicitud_vacaciones #8').hide();
	// 		}else{
	// 			var monto=$("input:text[name=4]").val();
	// 			var total=0;
	// 			total=monto*20/100;
	// 			$("input:text[name=8]").val(Number(monto)-Number(total));
	// 			$('#solicitud_vacaciones #43').hide();
	// 			$('#solicitud_vacaciones #15').hide();
	// 			$('#solicitud_vacaciones #9').hide();
	// 		}
	// 	}
	// });
});

// $("#4").on('blur',function(){
// 	console.log(monto);
// 	monto=e.target.value;
// 	console.log(monto);
// 	$("#5").val(monto);
// 	//console.log(monto);
// 	//document.getElementById("5").value=monto;
// 	// current.value=monto;
// });
</script>
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="assets/pages/scripts/components-date-time-pickers.js" type="text/javascript"></script>
<script type="text/javascript">
    $(".form_datetime3").datetimepicker({format: 'dd-mm-yyyy hh:ii:ss'});
</script>     
<!-- END PAGE LEVEL SCRIPTS -->
<?php
 include("config/end.php"); ?>
