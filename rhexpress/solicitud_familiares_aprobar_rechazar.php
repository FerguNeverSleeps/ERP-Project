<?php
//-------------------------------------------------
session_start();
//-------------------------------------------------
require_once "config/rhexpress_config.php";
include("config/rhexpress_header2.php");
require("../nomina/procesos/PHPMailer_5.2.4/class.phpmailer.php");
include("../nomina/procesos/PHPMailer_5.2.4/class.smtp.php");
//-------------------------------------------------

//echo $SQL;

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

$consulta_sql = "SELECT solicitudes_casos.*,nompersonal.*
FROM solicitudes_casos 
LEFT JOIN nompersonal on (solicitudes_casos.cedula = nompersonal.cedula)
WHERE solicitudes_casos.id_solicitudes_casos = '{$id}'";

$caso         = $conexion->query($consulta_sql)->fetch_assoc();

$SQL = "SELECT  nompersonal.apenom FROM departamento
JOIN nompersonal on (nompersonal.cedula = departamento.IdJefe)
WHERE departamento.IdJefe = '".$caso['IdJefe']."'";
$res1        = $conexion->query($SQL);
$jefe        = mysqli_fetch_assoc($res1);
if ($con == 1)
{
	$SQL_1          = "SELECT  * FROM  nompersonal
	WHERE cedula    = '".$_SESSION['cedula_rhexpress']."'";
	
	$persona           = $conexion->query($SQL_1)->fetch_assoc();
	
	$fecha_aprobacion  = (isset($_REQUEST['fecha_aprobacion'])) ? date('Y-m-d') : '' ;
	$aprobado          = (isset($_REQUEST['aprobado'])) ? $_REQUEST['aprobado'] : '' ;
	$observacion_jefe  = (isset($_REQUEST['observacion_jefe'])) ? $_REQUEST['observacion_jefe'] : '' ;
	if ($aprobado == 1) 
	{

		$update = "UPDATE solicitudes_casos
			SET fecha_aprobado_jefe     = '{$fecha_aprobacion}', 
			observacion_jefe            = '{$observacion_jefe}',  
			aprobado                    = '{$aprobado}',
			id_solicitudes_casos_status = '2' 
			WHERE id_solicitudes_casos  = '{$id}'";
	}
	else
	{
		$update = "UPDATE solicitudes_casos
		SET observacion_jefe            = '{$observacion_jefe}', 
		 id_solicitudes_casos_status = '3' 
		WHERE id_solicitudes_casos      = '{$id}'";	
	}
	//echo $update;
	$aprobacion = $conexion->query($update);


if ($aprobado) 
{

	// Import PHPMailer classes into the global namespace
	// These must be at the top of your script, not inside a function

	$mail             = new PHPMailer();
	$mail->IsSMTP();
	$email            ='jmpulgar.jose@gmail.com';
	$email1           ='josegutierrez76@gmail.com';
	$mail             = new PHPMailer();
	$mail->SMTPAuth   = true;
	$mail->isSMTP();
	$mail->SMTPDebug  = 1;
	$mail->Host       = "smtp.gmail.com";
	$mail->Port       = 465;
	$mail->SMTPSecure = 'ssl';
	$mail->Username   = "planillaexpresspanama@gmail.com";
	$mail->Password   = "S3l3ctr4";
	$mail->From       = "planillaexpresspanama@gmail.com";
	$mail->FromName   = "RRHH Solicitudes";
	$tit='SOLICITUD ';

	$mail->Subject = $tit;
	if ($aprobado) 
	{
		$asunto="SOLICITUD Nº: ".$id." APROBADA";
	}
	else
	{
		$asunto="SOLICITUD Nº: ".$id." RECHAZADA";
	}

	$mail->Body = $asunto;
	$mail->IsHTML(true);


	$mail->AddAddress ($email);
	$mail->AddAddress ($email1);
	$mail->AddAttachment($ruta);

	if(!$mail->Send()) {
		echo $ruta." ".$email." Mailer Error: " . $mail->ErrorInfo;
	} else {
		
	  	if ($aprobado) 
	  	{
	    	//echo "<script>alert('SOLICITUD APROBADA');</script>";
	     	echo "<script>alert('SOLICITUD APROBADA');location.href='rhexpress_bandeja_aprobados.php';</script>";
	 	}
	  	else
	  	{
	    	echo "<script>alert('SOLICITUD RECHAZADA');location.href='rhexpress_bandeja_rechazados.php';</script>";
	    	//echo "<script>alert('SOLICITUD RECHAZADA');;</script>";
	  	}
	//echo "Message enviado!";
	}
	$mail->ClearAddresses();
	$mail->ClearAttachments();


}


}
?>
<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <!-- BEGIN SAMPLE TABLE PORTLET-->
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-social-dribbble font-blue"></i>
                    <span class="caption-subject font-blue bold uppercase">SOLICITUD DE TIEMPO COMPENSATORIO</span>
                </div>
                <div class="actions">
                    <a class="btn btn-circle btn-icon-only btn-default" href="rhexpress_bandeja_aprobados.php">
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

					<div class="row">
						<div class="form-group">
							<label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2 col-md-3 col-lg-offset-2 col-lg-3 control-label">Posición:</label>
							<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
								<input name="posicion"  class="form-control input-circle" type="text" id="posicion" value="<?php echo $caso['nomposicion_id']; ?>" disabled><p></p>
							</div>
						</div>
					</div>

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
								<input name="unidad_administrativa"  class="form-control input-circle" type="text" id="unidad_administrativa" value="<?= $_SESSION['dpto_rhexpress'] ?>" disabled><p></p>
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
								<input name="tiempo_disponible" class="form-control input-circle" type="text" id="tiempo_disponible" value="<?php  echo intval($caso['tiempo']); ?>" disabled><p></p>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- FIN TIEMPO SOLICITADO-->
			<!-- INICIO USO TIEMPO -->
			<form action="" method="post" name="tiempo_compensatorio" id="tiempo_compensatorio" >
			<div class="form-body">

				<input name="btn_guardar" type="Hidden" id="op_tp" value="<?= $btn_guardar; ?>">
					<div class="portlet box blue">
						<div class="portlet-title">
							<H4>Detalles de Uso del Tiempo</H4>
						</div>
						<div class="portlet-body">
							<div class="row">
								<div class="form-group">
                                    <label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2
									col-md-3 col-lg-offset-2 col-lg-3 control-label">Fecha:</label>
                                    <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                                        <input id="fecha_inicio" name="fecha_inicio" type="text" size="16" class="form-control input-circle" value="<?= date('d-m-Y', strtotime($caso['fecha_registro'])); ?>" disabled>
                                       
                                    </div>
                                </div>
                            </div><p></p>
                           

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

                                        <input name="fecha_aprobacion"  class="form-control input-circle" id="fecha_aprobacion" value="<?php echo date('d-m-Y'); ?>" readonly ><p></p>
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
