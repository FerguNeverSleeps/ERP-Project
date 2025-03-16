<?php
//-------------------------------------------------
session_start();
//-------------------------------------------------
require_once "config/rhexpress_config.php";
include("config/rhexpress_header2.php");
//-------------------------------------------------

$SQL = "SELECT  nompersonal.apenom FROM departamento
JOIN nompersonal on (nompersonal.cedula = departamento.IdJefe)
WHERE departamento.IdJefe = '".$_SESSION['_Jefe']."'";
//echo $SQL;

$cedula = isset($_SESSION['cedula_rhexpress'])? $_SESSION['cedula_rhexpress']: '';
$res1   = $conexion->query($SQL);
$jefe   = mysqli_fetch_assoc($res1);

$sql2   = "SELECT ifnull(ROUND( SUM( tiempo ) , 2 ),0) AS  tiempo, dias, horas, minutos
FROM dias_incapacidad
WHERE cedula = '{$cedula}' AND tipo_justificacion = 3";
$time    = $conexion->query($sql2)->fetch_assoc();
$tiempo  = $time['tiempo'];

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
	$fecha_registro    = (isset($_REQUEST['fecha_registro'])) ? $_REQUEST['fecha_registro'] : '' ;
	$fecha_inicio      = (isset($_REQUEST['fecha_inicio'])) ? $_REQUEST['fecha_inicio'] : '' ;
	$fecha_fin         = (isset($_REQUEST['fecha_fin'])) ? $_REQUEST['fecha_fin'] : '' ;
	$observacion       = (isset($_REQUEST['observacion'])) ? $_REQUEST['observacion'] : '' ;
	$dias              = (isset($_REQUEST['dias_solicitados'])) ? $_REQUEST['dias_solicitados'] : '' ;
	$horas             = (isset($_REQUEST['horas_solicitadas'])) ? $_REQUEST['horas_solicitadas'] : '' ;
	$minutos           = (isset($_REQUEST['minutos_solicitados'])) ? $_REQUEST['minutos_solicitados'] : '' ;
	$tiempo_solicitado = (isset($_REQUEST['tiempo_solicitado'])) ? $_REQUEST['tiempo_solicitado'] : '' ;
	$hora_inicio       = (isset($_REQUEST['hora_inicio'])) ? $_REQUEST['hora_inicio'] : '00:00:00' ;
	$hora_fin          = (isset($_REQUEST['hora_fin'])) ? $_REQUEST['hora_fin'] : '00:00:00' ;
	$fecha_registro    = date('Y-m-d', strtotime($fecha_registro)) ; // resta 6 mes
	$fecha_ini         = date('Y-m-d H:i:s', strtotime($fecha_registro)) ; // resta 6 mes
	$fecha_fini        = date('Y-m-d H:i:s', strtotime($fecha_registro)) ; // resta 6 mes



	$insertar = "INSERT INTO solicitudes_casos (id_solicitudes_casos, cedula, id_tipo_caso, id_departamento, fecha_registro, fecha_inicio, fecha_fin, observacion, id_solicitudes_casos_status, tiempo,dias, horas, minutos)
	VALUES ('','".$_SESSION['cedula_rhexpress']."','4','".$persona['IdDepartamento']."','{$fecha_registro}','{$fecha_ini}','{$fecha_fini}','{$observacion}','1','{$tiempo_solicitado}','{$dias}','{$horas}','{$minutos}');";
	//echo $insertar,"<br>";

	$insertado = $conexion->query($insertar);
	$horas_dia    = 8;
	$dias         = intval($tiempo/$horas_dia);
	$hora         = ($tiempo - ($dias*$horas));
	$minutos      = ($tiempo - ($dias*$horas) - $hora) * 60;
	$tiempo_nuevo = (($dias * $horas_dia) + $hora + ($minutos / 60))*(-1);


	$q    = "INSERT INTO dias_incapacidad (cod_user, tipo_justificacion, fecha, tiempo, observacion,  usr_uid)
	VALUES ('".$persona['nomposicion_id']."', '3', '{$fecha_registro}', '{$tiempo_nuevo}', '{$observacion}',  '$cedula')";
	$conexion->query($q);

	$qexp ="INSERT INTO expediente (cedula, descripcion, fecha, usuario, horas, minutos, tipo,subtipo)
	VALUES ('{$cedula}', '{$observacion}', '$fecha', '$codigo_usuario', '$horas', '$minutos','27', '60')";
if ($insertado) 
  	{
    	//echo "<script>alert('SOLICITUD APROBADA');</script>";
     	echo "<script>alert('SOLICITUD ENVIADA EXITOSAMENTE');location.href='rhexpress_bandeja_entrada.php';</script>";
 	}
  	else
  	{
    	echo "<script>alert('HUBO UN ERROR AL PROCESAR SU SOLICITUD');location.href='rhexpress_bandeja_entrada.php';</script>";
    	//echo "<script>alert('SOLICITUD RECHAZADA');;</script>";
  	}

}
  	
?>
<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <!-- BEGIN SAMPLE TABLE PORTLET-->
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-hourglass font-blue"></i>
                    <span class="caption-subject font-blue bold uppercase">SOLICITUD DE TIEMPO EXTRAORDINARIO</span>
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
	            	<!--<div class="row">
	            		<div class="text-right">
	            			<a id="ocultar">Ocultar</a>
							<a id="mostrar">Mostrar</a>
	            		</div>
	            		
	            	</div>-->
	            	<div id="contenido">
	            		<div class="row">
						<div class="form-group">
							<label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2 col-md-3 col-lg-offset-2 col-lg-3 control-label">Nombre:</label>
							<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
								<input name="txtdescripcion"  class="form-control input-circle" type="text" id="txtdescripcion" value="<?= $_SESSION['nombre_rhexpress'] ?>" disabled><p></p>
							</div>
						</div>
					</div>

					<!-- <div class="row">
						<div class="form-group">
							<label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2 col-md-3 col-lg-offset-2 col-lg-3 control-label">Posición:</label>
							<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
								<input name="posicion"  class="form-control input-circle" type="text" id="posicion" value="<?= $_SESSION['pos_rhexpress'] ?>"><p></p>
							</div>
						</div>
					</div> -->

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
					<div class="row">
						<div class="form-group">
							<label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2
							col-md-3 col-lg-offset-2 col-lg-3 control-label">Tiempo Acumulado (Horas):</label>
							<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
								<input name="tiempo_acumulado" class="form-control input-circle" type="text" id="tiempo_acumulado" value="<?php echo $time['tiempo'];?>" disabled><p></p>
							</div>
						</div>
					</div>
	            	</div>

					
				</div>

			</div>
			<!-- FN DATOS -->


			<form action="" enctype="multipart/form-data" method="post" name="tiempo_compensatorio" id="tiempo_compensatorio" role="form" >
				<input name="btn_guardar" type="Hidden" id="op_tp" value="<?= $btn_guardar; ?>">
				<div class="form-body">
					<!-- INICIO USO TIEMPO -->
					<div class="portlet box blue">
						<div class="portlet-title">
							<H4>Detalles de Uso del Tiempo</H4>
						</div>
						<div class="portlet-body">
							<div class="row">
								<div class="form-group">
									<label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2
								col-md-3 col-lg-offset-2 col-lg-3 control-label">Fecha:</label>
								<div class="col-md-3">
									<div class="input-group date date-picker">
										<input type="text" class="form-control input-circle-left" name="fecha_registro" id="fecha_registro" readonly="">
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
									col-md-3 col-lg-offset-2 col-lg-3 control-label">Horas:</label>
									<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
										<input name="horas_solicitadas" class="form-control input-circle" type="text" id="horas_solicitadas" value="0"><p></p>
									</div>
								</div>
							</div>

							<div class="row">
								<div class="form-group">
									<label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2
									col-md-3 col-lg-offset-2 col-lg-3 control-label">Minutos:</label>
									<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
										<input name="minutos_solicitados" class="form-control input-circle" type="text" id="minutos_solicitados" value="0"><p></p>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="form-group">
									<label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2
									col-md-3 col-lg-offset-2 col-lg-3 control-label">Tiempo Solicitado (Horas):</label>
									<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3" >
										<input name="tiempo_solicitado" class="form-control input-circle" type="text" id="tiempo_solicitado" value="0" readonly><p></p>
									</div>
								</div>
							</div>


							<div class="row">
								<div class="form-group">
									<label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2
									col-md-3 col-lg-offset-2 col-lg-3 control-label">Observaciones</label>
									<div class="col-xs-7 col-sm-7 col-md-7 col-lg-7">
										<textarea cols="5" rows="5" name="observacion"  class="form-control" type="text" id="observacion" value="<?php ?>"></textarea>
									</div>
								</div>
							</div>
						</div>
					</div>
					<!-- FIN USO TIEMPO -->
					<br>
					<div class="row">
						<input type="Hidden" name="con" value="1">
						<input type="Hidden" name="dias_solicitados" id="dias_solicitados" value="0">
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
	$("#ocultar").show();
	$("#mostrar").hide();
	function muestra()
	{
		$("#ocultar").on("click",function()
		{
			$("#mostrar").show();
			$("#ocultar").hide();
			$("#contenido").hide();
			esconde();
		});
	}
	function esconde()
	{
		$("#mostrar").on("click",function()
		{
			$("#mostrar").show();
			$("#contenido").show();
			$("#ocultar").hide();
			muestra();
		});

	}
	$("#ocultar").on("click",function()
	{
		$("#mostrar").show();
		$("#ocultar").hide();
		$("#contenido").hide();
		muestra();
	});
	$("#mostrar").on("click",function()
	{
		$("#mostrar").show();
		$("#contenido").show();
		$("#ocultar").hide();
		esconde();
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
<script src="assets/pages/scripts/components-date-time-pickers.js" type="text/javascript"></script>
<script type="text/javascript">
    $(".form_datetime3").datetimepicker({format: 'dd-mm-yyyy hh:ii:ss'});
</script>     
<!-- END PAGE LEVEL SCRIPTS -->
<?php
 include("config/end.php"); ?>
