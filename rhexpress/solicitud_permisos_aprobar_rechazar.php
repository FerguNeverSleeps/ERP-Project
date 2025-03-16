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

$cedula = isset($_SESSION['cedula_rhexpress'])? $_SESSION['cedula_rhexpress']: '';

// Datos basicos para el envio de correo
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
$correo_adicional1=$res['correo_adicional1'];
$correo_adicional2=$res['correo_adicional2'];
$correo_adicional3=$res['correo_adicional3'];
$correo_adicional4=$res['correo_adicional4'];

// Calculo de tiempo
$sql2        = "SELECT IFNULL(SUM(tiempo), 0) AS tiempo
FROM dias_incapacidad
WHERE cedula = '{$cedula}' AND tipo_justificacion = 5";
$time        = $conexion->query($sql2)->fetch_assoc();
$tiempo      = intval($time['tiempo']);
$horas       = 8;
$dias        = intval($tiempo/$horas);
$hora        = ($tiempo - ($dias*$horas));
$minutos     = ($tiempo - ($dias*$horas) - $hora);
//echo $tiempo," ",$dias," ",$horas," ",$minutos;
$con         = (isset($_REQUEST['con'])) ? $_REQUEST['con'] : 0 ;
$id          = (isset($_REQUEST['id'])) ? $_REQUEST['id'] : 0 ;

$consulta_sql = "SELECT solicitudes_casos.*,nompersonal.*
FROM solicitudes_casos 
LEFT JOIN nompersonal on (solicitudes_casos.cedula = nompersonal.cedula)
WHERE solicitudes_casos.id_solicitudes_casos = '{$id}'";
$caso= $conexion->query($consulta_sql)->fetch_assoc();
//$caso= $conexion->query($consulta_sql)->fetch_assoc();
$correo_colab=  $caso['email'];

if ($con == 1)
{
	$conexion->query("START TRANSACTION;");

	$SQL_1          = "SELECT  * FROM  nompersonal
	WHERE cedula    = '".$_SESSION['cedula_rhexpress']."'";
	
	$persona           = $conexion->query($SQL_1)->fetch_assoc();
	$fecha_aprobacion  = (isset($_REQUEST['fecha_aprobacion'])) ? date('Y-m-d') : '' ;
	$aprobado          = (isset($_REQUEST['aprobado'])) ? $_REQUEST['aprobado'] : '' ;
	$tipo_permiso          = (isset($_REQUEST['tipo_permiso'])) ? $_REQUEST['tipo_permiso'] : '' ;
	$observacion_jefe  = (isset($_REQUEST['observacion_jefe'])) ? $_REQUEST['observacion_jefe'] : '' ;
	if ($aprobado == 1) 
	{
		$fecha_aprobacion  = (isset($_REQUEST['fecha_aprobacion'])) ? date('Y-m-d') : date('Y-m-d') ;	

		$update = "UPDATE solicitudes_casos
			SET fecha_aprobado_jefe     = '{$fecha_aprobacion}', 
			observacion_jefe            = '{$observacion_jefe}',  
			aprobado                    = '{$aprobado}',
			id_solicitudes_casos_status = '2' 
			WHERE id_solicitudes_casos  = '{$id}'";

			$tiempo       = $caso['tiempo'];
			$horas_solic  = $caso['horas'];
			$minutos_solic= $caso['minutos'];
			$cedula       = $caso['cedula'];
			$horas_dia    = 8;
			$dias         = intval($tiempo/$horas_dia);
			$hora         = ($tiempo - ($dias*$horas));
			$minutos      = ($tiempo - ($dias*$horas) - $hora) * 60;
			$tiempo_nuevo = (($dias * $horas_dia) + $hora + ($minutos / 60))*(-1);
			$fechamashorafin=$caso['fin_permiso'];
			$horas2=explode(" ",$fechamashorafin);
			$hora_insert=$horas2[1];
			$fecha_insert=$horas2[0];
			//////////////////////
			$fechamashora=$caso['inicio_permiso'];
			$horas=explode(" ",$fechamashora);
			$hora_inicio_insert=$horas[1];
			$fecha_inicio_insert=$horas[0];

			$conexion->query($q);
			$aprobacion = $conexion->query($update);

			$qexp ="INSERT INTO expediente (cedula, descripcion, fecha, usuario, horas,dias, minutos, tipo,subtipo,nivel_actual,desde,hasta,fecha_inicio,fecha_fin,duracion,registro)
			VALUES ('{$cedula}', '{$observacion_jefe}', '{$fecha_aprobacion}', '{$cedula}', '{$horas_solic}','{$dias}', '{$minutos_solic}','4', '{$tipo_permiso}','{$nivel1['numero_departamento']}','{$hora_inicio_insert}','{$hora_insert}','{$fecha_inicio_insert}','{$fecha_insert}','{$tiempo}','{$id}')";
			$resultado_exp= $conexion->query($qexp);

			if($resultado_exp)
			{
				$ultimo_id = $conexion-> insert_id;
	
				if(isset($caso["archivo_tardanza"]) AND $caso["archivo_tardanza"] != ""){
	
					$archivo_caso = $caso["archivo_tardanza"];
					$observaciones = $caso["observaciones_tardanza"];
				}
	
				if(isset($caso["archivo_ausencia"]) AND $caso["archivo_ausencia"] != ""){
	
					$archivo_caso = $caso["archivo_ausencia"];
					$observaciones = $caso["observaciones_ausencia"];
				}

				if(isset($caso["archivo_permiso"]) AND $caso["archivo_permiso"] != ""){

					$archivo_caso = $caso["archivo_permiso"];
					$observaciones = $caso["observaciones_permiso"];
				}
	
				if($archivo_caso != "")
				{
					// REGISTRAR EXPEDIENTE
					//$descripcion_registro="DOCUMENTO";
					$consulta_doc="INSERT INTO expediente
							   (cod_expediente_det, cedula, descripcion, tipo, subtipo, fecha)
							   VALUES  
								('','{$cedula}','{$archivo}','13','','".$fecha."');";
	
					//$resultado_doc= $conexion->query($consulta_doc);
					if (!file_exists("../nomina/expediente/navegador_archivos/archivos/".$cedula)) 
					{
						mkdir("../nomina/expediente/navegador_archivos/archivos/".$cedula, 0777, true);							
					}

					$archivo = explode("/",$archivo_caso);
					$archivo = basename($archivo[2]);
					$archivo = str_replace(' ', '', strtolower($archivo));
					$nombre_archivo =   time() . '_' . $archivo ;
					$archivo = "../nomina/expediente/navegador_archivos/archivos/" . $cedula . "/" .$nombre_archivo ;

					chmod( $archivo , 777);

					if ( !copy($archivo_caso, $archivo )) {
						$conexion->query("ROLLBACK;");
						echo $archivo, "\n ";
						echo $archivo_caso;
						exit("¡Error! Al mover el archivo");
					}else{
						$direccion_archivo = $nombre_archivo;
					}
					//REGISTRAR DOCUMENTO
					$consulta = "INSERT INTO expediente_adjunto 
									(id_adjunto, nombre_adjunto, descripcion, archivo,  principal,  fecha,  cod_expediente_det) 
									VALUES 
									('', '{$nombre_archivo}', '{$observaciones}', '{$direccion_archivo}', '1','{$fecha_aprobacion}', '{$ultimo_id}');";
					$resultado=$conexion->query($consulta);
					
				}
				$conexion->query("COMMIT;");
	
			}
			else
			{
				$conexion->query("ROLLBACK;");
			}
			//echo $qexp;
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
			if($correo_adicional1!=""){$mail->AddAddress ($correo_adicional1);}
			if($correo_adicional2!=""){$mail->AddAddress ($correo_adicional2);}
			if($correo_adicional3!=""){$mail->AddAddress ($correo_adicional3);}
			if($correo_adicional4!=""){$mail->AddAddress ($correo_adicional4);}
			$mail->AddAttachment($ruta);
			if(!$mail->Send()) {
				echo $ruta." ".$email." Mailer Error: " . $mail->ErrorInfo;
			}
			echo "<script>alert('SOLICITUD APROBADA');location.href='rhexpress_bandeja_aprobados.php';</script>";
	}
	else if ($aprobado == 0)
	{
		$update = "UPDATE solicitudes_casos
			SET observacion_jefe            = '{$observacion_jefe}',
				id_solicitudes_casos_status = '3' 
			WHERE id_solicitudes_casos = '{$id}'";

			$aprobacion = $conexion->query($update);

			$conexion->query("COMMIT;");
			//echo $qexp;
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
			$asunto="SOLICITUD Nº: ".$id."  RECHAZADA";
			$mail->Body = $asunto;
			$mail->IsHTML(true);
			// Se asigna la dirección de correo a donde se enviará el mensaje.
			$mail->AddAddress ($email);
			$mail->AddAddress ($email2);
			$mail->AddAddress ($email3);
			$mail->AddAddress ($email4);
			if($correo_adicional1!=""){$mail->AddAddress ($correo_adicional1);}
			if($correo_adicional2!=""){$mail->AddAddress ($correo_adicional2);}
			if($correo_adicional3!=""){$mail->AddAddress ($correo_adicional3);}
			if($correo_adicional4!=""){$mail->AddAddress ($correo_adicional4);}
			$mail->AddAttachment($ruta);
			if(!$mail->Send()) {
				echo $ruta." ".$email." Mailer Error: " . $mail->ErrorInfo;
			}
			echo "<script>alert('SOLICITUD RECHAZADA');location.href='rhexpress_bandeja_rechazados.php';</script>";
	}
	
	$mail->ClearAddresses();
	$mail->ClearAttachments();

}
?>
<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
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
				<h4> Datos del Colaborador</h4>
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
					<H4>Tiempo Solicitado</H4>
				</div>
				<div class="portlet-body">
					<div class="row">
						<div class="form-group">
							<label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2
							col-md-3 col-lg-offset-2 col-lg-3 control-label">Día:</label>
							<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
								<input name="dias_solicitados" class="form-control input-circle" type="text" id="dias_solicitados" value="<?php echo $caso['dias']; ?>" disabled><p></p>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="form-group">
							<label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2
							col-md-3 col-lg-offset-2 col-lg-3 control-label">Horas:</label>
							<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
								<input name="horas_solicitadas" class="form-control input-circle" type="text" id="horas_solicitadas" value="<?php echo $caso['horas']; ?>" disabled><p></p>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="form-group">
							<label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2
							col-md-3 col-lg-offset-2 col-lg-3 control-label">Minutos:</label>
							<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
								<input name="minutos_solicitados" class="form-control input-circle" type="text" id="minutos_solicitados" value="<?php echo $caso['minutos']; ?>" disabled><p></p>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="form-group">
							<label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2
							col-md-3 col-lg-offset-2 col-lg-3 control-label">Tiempo Disponible:</label>
							<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
								<input name="tiempo_disponible" class="form-control input-circle" type="text" id="tiempo_disponible" value="<?php  echo $caso['horas']; ?>" disabled><p></p>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- FIN TIEMPO SOLICITADO-->
			<!-- INICIO USO TIEMPO -->
			<form action="" method="post" name="tiempo_compensatorio" id="tiempo_compensatorio" >
				<div class="form-body">
					
					<input type="hidden" name="tipo_permiso" value="<?php echo $caso['id_tipo_permiso']; ?>">
					<input name="btn_guardar" type="Hidden" id="op_tp" value="<?= $btn_guardar; ?>">
					<div class="portlet box blue">
						<div class="portlet-title">
							<H4>Detalles de Uso del Tiempo</H4>
						</div>
						<div class="portlet-body">
							<div class="row">
								<div class="form-group">
                                    <label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2
									col-md-3 col-lg-offset-2 col-lg-3 control-label">El tiempo se hará efectivo desde el:</label>
                                	<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                                            <input id="fecha_inicio" name="fecha_inicio" type="text" size="16" class="form-control input-circle" value="<?php  echo $caso['inicio_permiso']; ?>" disabled>
                                           
                                        <p></p>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
								<div class="form-group">
                                    <label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2
									col-md-3 col-lg-offset-2 col-lg-3 control-label">El tiempo se hará efectivo desde el:</label>
                                	<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                                        <div class="iform-control input-circle">
                                            <input id="fecha_fin" name="fecha_fin" type="text" size="16" class="form-control input-circle" value="<?php  echo $caso['fin_permiso']; ?>" disabled>
                                           
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
					</div>
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
                                	<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                                        <input name="fecha_aprobacion"  class="form-control input-circle" id="fecha_aprobacion" value="<?php echo date('d-m-Y'); ?>" disabled><p></p>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
								<div class="form-group">
                                    <label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2
									col-md-3 col-lg-offset-2 col-lg-3 control-label">Persona que aprueba:</label>
                                	<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                                        <input id="persona_aprbacion" name="persona_aprbacion" type="text" size="16" class="form-control input-circle" value="<?php echo $jefe['apenom']; ?>">
                                            
                                        <p></p>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                            	<div class="form-group">
                            		<label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2
									col-md-3 col-lg-offset-2 col-lg-3 control-label">Aprobado?</label>                            		
                                	<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
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
</script>
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="assets/pages/scripts/components-date-time-pickers.js" type="text/javascript"></script>
<script type="text/javascript">
    $(".form_datetime3").datetimepicker({format: 'dd-mm-yyyy hh:ii:ss'});
</script>     
<!-- END PAGE LEVEL SCRIPTS -->
<?php
 include("config/end.php"); ?>
