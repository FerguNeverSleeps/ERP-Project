<?php 
if (!isset($_SESSION)) {
    session_start();
    ob_start();
}

require_once '../lib/common.php';
include ("func_bd.php");
//error_reporting(E_ALL ^ E_DEPRECATED);
$conexion=conexion();

// Evitar errores con acentos o caracteres especiales:
mysqli_query($conexion, "SET NAMES 'utf8'");
mysqli_query($conexion, "SET CHARACTER SET utf8 ");

$id = ( isset($_POST['horario_id']) ) ? $_POST['horario_id']  : '';

if( isset($_POST['turno_id']) )
{ 
	$dia1 = (isset($_POST['dia1'])) ? 1 : 0;
	$dia2 = (isset($_POST['dia2'])) ? 1 : 0;
	$dia3 = (isset($_POST['dia3'])) ? 1 : 0;
	$dia4 = (isset($_POST['dia4'])) ? 1 : 0;
	$dia5 = (isset($_POST['dia5'])) ? 1 : 0;
	$dia6 = (isset($_POST['dia6'])) ? 1 : 0;
	$dia7 = (isset($_POST['dia7'])) ? 1 : 0;

	$hora_inicio = (isset($_POST['hora_inicio'])) ? $_POST['hora_inicio'] : '00:00 am';
	$hora_fin    = (isset($_POST['hora_fin']))    ? $_POST['hora_fin']    : '00:00 am';
	$dia_libre   = (isset($_POST['dia_libre']))   ? $_POST['dia_libre']   : 0;
	$turno_id    = (isset($_POST['turno_id']))    ? $_POST['turno_id']    : '';

	$entrada_desde = (isset($_POST['entrada_desde'])) ? $_POST['entrada_desde'] : $hora_inicio;
	$entrada_hasta = (isset($_POST['entrada_hasta'])) ? $_POST['entrada_hasta'] : $hora_inicio;
	$salida_desde  = (isset($_POST['salida_desde']))  ? $_POST['salida_desde']  : $hora_fin;
	$salida_hasta  = (isset($_POST['salida_hasta']))  ? $_POST['salida_hasta']  : $hora_fin;
	$pago_desde    = (isset($_POST['pago_desde']))    ? $_POST['pago_desde']    : $hora_inicio;
	$pago_hasta    = (isset($_POST['pago_hasta']))    ? $_POST['pago_hasta']    : $hora_fin;
	$tolerancia_entrada = (isset($_POST['tolerancia_entrada'])) ? $_POST['tolerancia_entrada'] : '' ;
	$tolerancia_salida  = (isset($_POST['tolerancia_salida']))  ? $_POST['tolerancia_salida']  : '' ;

	if( empty($id) )
	{
		$sql = "INSERT INTO nomturnos_horarios 
		        (turno_id, dia1, dia2, dia3, dia4, dia5, dia6, dia7, hora_desde, hora_hasta, dialibre,
		         entrada_desde, entrada_hasta, salida_desde, salida_hasta, paga_desde, paga_hasta, tolerancia_entrada, tolerancia_salida) 
                VALUES 
                ({$turno_id}, 
                 {$dia1}, {$dia2}, {$dia3}, {$dia4}, {$dia5}, {$dia6}, {$dia7}, 
                 TIME(STR_TO_DATE('{$hora_inicio}','%h:%i %p')),
                 TIME(STR_TO_DATE('{$hora_fin}',   '%h:%i %p')),
                 {$dia_libre},
                 TIME(STR_TO_DATE('{$entrada_desde}','%h:%i %p')), TIME(STR_TO_DATE('{$entrada_hasta}','%h:%i %p')), 
                 TIME(STR_TO_DATE('{$salida_desde}','%h:%i %p')),  TIME(STR_TO_DATE('{$salida_hasta}','%h:%i %p')),
                 TIME(STR_TO_DATE('{$pago_desde}','%h:%i %p')),    TIME(STR_TO_DATE('{$pago_hasta}','%h:%i %p')),
                 NULLIF('{$tolerancia_entrada}',''), NULLIF('{$tolerancia_salida}','')
                )";
	}
	else
	{
		$sql = "UPDATE nomturnos_horarios 
				SET
				dia1={$dia1},
				dia2={$dia2},
				dia3={$dia3},
				dia4={$dia4},
				dia5={$dia5},
				dia6={$dia6},
				dia7={$dia7},
				hora_desde=TIME(STR_TO_DATE('{$hora_inicio}','%h:%i %p')),
				hora_hasta=TIME(STR_TO_DATE('{$hora_fin}',   '%h:%i %p')),
				dialibre={$dia_libre},
				entrada_desde=TIME(STR_TO_DATE('{$entrada_desde}','%h:%i %p')),
				entrada_hasta=TIME(STR_TO_DATE('{$entrada_hasta}','%h:%i %p')),
				salida_desde=TIME(STR_TO_DATE('{$salida_desde}','%h:%i %p')),
				salida_hasta=TIME(STR_TO_DATE('{$salida_hasta}','%h:%i %p')),
				paga_desde=TIME(STR_TO_DATE('{$pago_desde}','%h:%i %p')),  
				paga_hasta=TIME(STR_TO_DATE('{$pago_hasta}','%h:%i %p')),
				tolerancia_entrada=NULLIF('{$tolerancia_entrada}',''),
				tolerancia_salida=NULLIF('{$tolerancia_salida}','')
				WHERE turnohorario_id={$id}";

	}

	$res = query($sql, $conexion);
	activar_pagina("horarios_turno2.php?id=".$turno_id);
}

if( isset($_GET['turno_id']) )    // Agregar Horario
{
	$turno_id = $_GET['turno_id'];

	$sql = "SELECT descripcion FROM nomturnos WHERE  turno_id=" . $turno_id;

	$res = query($sql, $conexion);

	if( $fila=fetch_array($res)  )
		$turno       = $fila['descripcion'];
}

$lunes=$martes=$miercoles=$jueves=$viernes=$sabado=$domingo='';
$hora_inicio=$hora_fin=$dia_libre='';
$entrada_desde=$entrada_hasta=$salida_desde=$salida_hasta=$pago_desde=$pago_hasta=$tolerancia_entrada=$tolerancia_salida='';
if( isset($_POST['horario_id']) ) // Editar Horario
{
	$sql = "SELECT nth.*, nt.descripcion as turno,
	               date_format(hora_desde,'%h:%i:%s %p')  as hora_inicio,
	               date_format(hora_hasta,'%h:%i:%s %p')  as hora_fin,
	               date_format(entrada_desde,'%h:%i:%s %p')  as entrada_desde,
	               date_format(entrada_hasta,'%h:%i:%s %p')  as entrada_hasta,
	               date_format(salida_desde,'%h:%i:%s %p')   as salida_desde,
	               date_format(salida_hasta,'%h:%i:%s %p')   as salida_hasta,
	               date_format(paga_desde,'%h:%i:%s %p')  as paga_desde,
	               date_format(paga_hasta,'%h:%i:%s %p')  as paga_hasta
			FROM   nomturnos_horarios nth, nomturnos nt 
			WHERE  nth.turno_id=nt.turno_id
			AND    nth.turnohorario_id=" . $id;

	$res = query($sql, $conexion);

	if( $fila=fetch_array($res)  )
	{
		$turno_id    = $fila['turno_id'];
		$turno       = $fila['turno'];
		$lunes       = $fila['dia1'];
		$martes      = $fila['dia2'];
		$miercoles   = $fila['dia3'];
		$jueves      = $fila['dia4'];
		$viernes     = $fila['dia5'];
		$sabado      = $fila['dia6'];
		$domingo     = $fila['dia7'];
		$hora_inicio = $fila['hora_inicio'];
		$hora_fin    = $fila['hora_fin'];
		$dia_libre   = $fila['dialibre'];
		$entrada_desde = $fila['entrada_desde'];
		$entrada_hasta = $fila['entrada_hasta'];
		$salida_desde  = $fila['salida_desde'];
		$salida_hasta  = $fila['salida_hasta'];
		$pago_desde    = $fila['paga_desde'];
		$pago_hasta    = $fila['paga_hasta'];
		$tolerancia_entrada = $fila['tolerancia_entrada'];
		$tolerancia_salida  = $fila['tolerancia_salida'];
	}
}
?>
<?php include("../header4.php"); // <html><head></head><body> ?>
<link href="../../includes/assets/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css" rel="stylesheet" type="text/css"  />
<link href="../../includes/assets/css/custom-datatables.css" rel="stylesheet" type="text/css"/>
<style type="text/css">
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

@media (min-width:992px) {
	.quitar-padding {
	    padding-left: 0px !important;
	}

	.sin-paddinglefrig{
		padding-left: 0px; 
		padding-right: 0px;
	}
}

hr{
	margin-bottom: 10px;
}

h4{
    font-size: 13px !important;
    font-weight: bold !important;
    margin-bottom: 20px;
}
</style>
<div class="page-container">
	<!-- BEGIN SIDEBAR -->
	<!-- END SIDEBAR -->
	<!-- BEGIN CONTENT -->
	<div class="page-content-wrapper">
		<div class="page-content">
			<!-- BEGIN PAGE CONTENT-->
			<div class="row">
				<div class="col-md-11">
					<!-- BEGIN EXAMPLE TABLE PORTLET-->
					<div class="portlet box blue">
						<div class="portlet-title">
							<div class="caption">
								<?php echo (empty($id)) ? 'Agregar' : 'Editar'; ?> Horario / Turno <?php echo str_replace('Turno', '', $turno) ; ?>
							</div>
						</div>
						<div class="portlet-body form">
							<form class="form-horizontal" id="formPrincipal" name="formPrincipal" method="post" role="form" style="margin-bottom: 5px;">
								<div class="form-body">

									<div class="form-group">
										<div class="col-md-2">
											<div class="checkbox-list">
												<label><input type="checkbox" name="dia1" id="dia1" <?php echo ($lunes==1)     ? 'checked' : ''; ?> > Lunes</label>
												<label><input type="checkbox" name="dia2" id="dia2" <?php echo ($martes==1)    ? 'checked' : ''; ?> > Martes</label>
												<label><input type="checkbox" name="dia3" id="dia3" <?php echo ($miercoles==1) ? 'checked' : ''; ?> > Mi&eacute;rcoles</label>
												<label><input type="checkbox" name="dia4" id="dia4" <?php echo ($jueves==1)    ? 'checked' : ''; ?> > Jueves</label>
											</div>
										</div>
										<div class="col-md-2">
											<div class="checkbox-list">
												<label><input type="checkbox" name="dia5" id="dia5" <?php echo ($viernes==1) ? 'checked' : ''; ?> > Viernes</label>
												<label><input type="checkbox" name="dia6" id="dia6" <?php echo ($sabado==1)  ? 'checked' : ''; ?> > S&aacute;bado</label>
												<label><input type="checkbox" name="dia7" id="dia7" <?php echo ($domingo==1) ? 'checked' : ''; ?> > Domingo</label>
											</div>
										</div>

										<div class="col-md-8">
											<div class="row">
												<label class="col-md-2 control-label quitar-padding" for="hora_inicio" 
												       style="text-align: center; padding-right: 0px">Hora Inicio:</label>
												<div class="col-md-3 quitar-padding">
													<!--
													<input type="text" class="form-control input-sm"
												           id="hora_inicio" name="hora_inicio" value="<?php echo $hora_inicio; ?>" 
												           <?php echo ($dia_libre==1) ? 'disabled' : ''; ?> >
													-->
													<div class="input-group timepicker">
														<input name="hora_inicio" id="hora_inicio" type="text" class="form-control input-sm timepicker timepicker-no-seconds" 
														 <?php echo ($dia_libre==1) ? 'disabled' : ''; ?> value="<?php echo $hora_inicio; ?>" >
														<span class="input-group-btn">
															<button class="btn default" type="button" style="height: 28px;"><i class="fa fa-clock-o"></i></button>
														</span>
													</div>
												</div>
												<label class="col-md-2 control-label quitar-padding" for="hora_fin" style="text-align: center; padding-right: 0px">Hora Fin:</label>
												<div class="col-md-3 quitar-padding">
													<!--
													<input type="text" class="form-control input-sm"
												           id="hora_fin" name="hora_fin" value="<?php echo $hora_fin; ?>" <?php echo ($dia_libre==1) ? 'disabled' : ''; ?> >
												    -->
													
													<div class="input-group timepicker">
														<input name="hora_fin" id="hora_fin" type="text" class="form-control input-sm timepicker timepicker-no-seconds" 
														 <?php echo ($dia_libre==1) ? 'disabled' : ''; ?> value="<?php echo $hora_fin; ?>" >
														<span class="input-group-btn">
															<button class="btn default" type="button" style="height: 28px;"><i class="fa fa-clock-o"></i></button>
														</span>
													</div>												    
												</div>
											</div>
											<div class="row" style="margin-top: 15px">
												<label class="col-md-2 control-label" for="dia_libre" style="text-align: center;">D&iacute;a Libre:</label>
												<div class="col-md-10 radio-list">
													<label class="radio-inline">
													<input type="radio" name="dia_libre" id="dia_libre1" value="1" <?php echo ($dia_libre==1) ? 'checked' : ''; ?> > Si </label>
													<label class="radio-inline">
													<input type="radio" name="dia_libre" id="dia_libre2" value="0" <?php echo ($dia_libre==0) ? 'checked' : ''; ?> > No </label>
												</div>
											</div>
										</div>
									</div>

									<div id="div-rangos" style="display: <?php echo ($dia_libre==1) ? 'none' : 'block'; ?>" > 
										<hr>
										<div class="row">
											<div class="col-md-6">
												<h4>Entrada:</h4>
												<div class="form-group">
													<label class="col-md-2 control-label" for="entrada_desde" style="text-align: center;">Desde:</label>
													<div class="col-md-3 sin-paddinglefrig">
														<input type="text" class="form-control" id="entrada_desde" name="entrada_desde" 
														       value="<?php echo $entrada_desde; ?>"    <?php echo ($dia_libre==1) ? 'disabled' : ''; ?> >
													</div>
													<label class="col-md-2 control-label" for="entrada_hasta" style="text-align: center;">Hasta:</label>
													<div class="col-md-3 sin-paddinglefrig">
														<input type="text" class="form-control" id="entrada_hasta" name="entrada_hasta" 
														       value="<?php echo $entrada_hasta; ?>" <?php echo ($dia_libre==1) ? 'disabled' : ''; ?> >
													</div>										
												</div>											
											</div>
											<div class="col-md-6">
												<h4>Salida:</h4>
												<div class="form-group">
													<label class="col-md-2 control-label" for="salida_desde" style="text-align: center;">Desde:</label>
													<div class="col-md-3 sin-paddinglefrig">
														<input type="text" class="form-control" id="salida_desde" name="salida_desde" 
														       value="<?php echo $salida_desde; ?>"    <?php echo ($dia_libre==1) ? 'disabled' : ''; ?> >
													</div>
													<label class="col-md-2 control-label" for="salida_hasta" style="text-align: center;">Hasta:</label>
													<div class="col-md-3 sin-paddinglefrig">
														<input type="text" class="form-control" id="salida_hasta" name="salida_hasta" 
														       value="<?php echo $salida_hasta; ?>" <?php echo ($dia_libre==1) ? 'disabled' : ''; ?> >
													</div>										
												</div>
											</div>		
										</div>

										<div class="row">
											<div class="col-md-6">
												<h4>Pago:</h4>
												<div class="form-group">
													<label class="col-md-2 control-label" for="pago_desde" style="text-align: center;">Desde:</label>
													<div class="col-md-3 sin-paddinglefrig">
														<input type="text" class="form-control" id="pago_desde" name="pago_desde" 
														       value="<?php echo $pago_desde; ?>"    <?php echo ($dia_libre==1) ? 'disabled' : ''; ?> >
													</div>
													<label class="col-md-2 control-label" for="pago_hasta" style="text-align: center;">Hasta:</label>
													<div class="col-md-3 sin-paddinglefrig">
														<input type="text" class="form-control" id="pago_hasta" name="pago_hasta" 
														       value="<?php echo $pago_hasta; ?>" <?php echo ($dia_libre==1) ? 'disabled' : ''; ?> >
													</div>										
												</div>
											</div>
											<div class="col-md-6">
												<h4>Tolerancia en minutos:</h4>
												<div class="form-group">
													<label class="col-md-2 control-label" for="tolerancia_entrada" style="text-align: center;">Entrada:</label>
													<div class="col-md-3 sin-paddinglefrig">
														<input type="text" class="form-control" id="tolerancia_entrada" name="tolerancia_entrada" 
														       value="<?php echo $tolerancia_entrada; ?>"    <?php echo ($dia_libre==1) ? 'disabled' : ''; ?> >
													</div>
													<label class="col-md-2 control-label" for="tolerancia_salida" style="text-align: center;">Salida:</label>
													<div class="col-md-3 sin-paddinglefrig">
														<input type="text" class="form-control" id="tolerancia_salida" name="tolerancia_salida" 
														       value="<?php echo $tolerancia_salida; ?>" <?php echo ($dia_libre==1) ? 'disabled' : ''; ?> >
													</div>										
												</div>
											</div>
										</div>
									</div>

									<div style="text-align: center">
										<button type="submit" class="btn btn-sm blue active" id="btn-guardar" name="btn-guardar">Guardar</button>	&nbsp;
										<button type="button" class="btn btn-sm default active" 
										        onclick="javascript: document.location.href='horarios_turno2.php?id=<?php echo $turno_id; ?>'">Cancelar</button>
									</div>
								</div>
								<input type="hidden" name="horario_id" id="horario_id" value="<?php echo $id; ?>">
								<input type="hidden" name="turno_id"   id="turno_id"   value="<?php echo $turno_id; ?>">
							</form>
						</div>
					</div>
					<!-- END EXAMPLE TABLE PORTLET-->
				</div>
			</div>
			<!-- END PAGE CONTENT-->
		</div>
	</div>
	<!-- END CONTENT -->
</div>
<?php include("../footer4.php"); ?>
<script src="../../includes/assets/plugins/jquery-validation/dist/jquery.validate.min.js" type="text/javascript"></script>
<script src="../../includes/assets/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js"></script>
<script src="../../includes/assets/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {
	//$("#hora_inicio, #hora_fin").inputmask("hh:mm t",{ "placeholder": "hh:mm am" });
	$("#entrada_desde, #entrada_hasta, #salida_desde, #salida_hasta, #pago_desde, #pago_hasta").inputmask("hh:mm t",{ "placeholder": "hh:mm am" });

	$('input:radio[name="dia_libre"]').change(function(){
	    if($(this).val() == '1')
	    {
	       $("#hora_inicio, #hora_fin").prop('disabled', true).val('');
	       $("#entrada_desde, #entrada_hasta, #salida_desde, #salida_hasta, #pago_desde, #pago_hasta, #tolerancia_entrada, #tolerancia_salida").prop('disabled', true).val('');
	       $("#div-rangos").hide();
	    }
	    else
	    {
	       $("#hora_inicio, #hora_fin").prop('disabled', false);
	       $("#entrada_desde, #entrada_hasta, #salida_desde, #salida_hasta, #pago_desde, #pago_hasta, #tolerancia_entrada, #tolerancia_salida").prop('disabled', false);   
	       $("#div-rangos").show();	
	    }
	});

    $('#hora_inicio').timepicker({
        minuteStep:   1,
        //secondStep: 1,
        showSeconds:  false,
        showMeridian: true,
        defaultTime:  '', //'23:59:59'
    });

    $('#hora_fin').timepicker({
        minuteStep:   1,
        //secondStep: 1,
        showSeconds:  false,
        showMeridian: true,
        defaultTime: ''
    });

	
	$("#hora_inicio").change(function() {
		var hora_inicio = $(this).val();

		console.log("Hora de inicio: " + hora_inicio);

		if(hora_inicio!=''){
			$("#pago_desde, #entrada_desde, #entrada_hasta").val(hora_inicio);
		}
	});

	$("#hora_fin").change(function() {
		var hora_fin = $(this).val();

		console.log("Hora fin: " + hora_fin);

		if(hora_fin!=''){
			$("#pago_hasta, #salida_desde, #salida_hasta").val(hora_fin);
		}
	});
	

	$("#btn-guardar").click(function() {
		// Comprobar que se marque algun día
		var lun = $('#dia1').is(':checked');
		var mar = $('#dia2').is(':checked');
		var mie = $('#dia3').is(':checked');
		var jue = $('#dia4').is(':checked');
		var vie = $('#dia5').is(':checked');
		var sab = $('#dia6').is(':checked');
		var dom = $('#dia7').is(':checked');

		var dia_libre = $('#dia_libre1').is(':checked');

		if(!lun && !mar && !mie && !jue && !vie && !sab && !dom)
		{
			//console.log("Ninguno marcado");
			alert("Debe seleccionar al menos un dia de la semana");
			return false;
		}

		if( ! dia_libre )
		{
			var hora_ini = $("#hora_inicio").val();
			var hora_fin = $("#hora_fin").val();

			if(hora_ini=='' || hora_fin=='')
			{
				alert("Debe completar la hora de inicio y la hora fin");
				return false;
			}

			var entrada_desde = $("#entrada_desde").val();
			var entrada_hasta = $("#entrada_hasta").val();
			var salida_desde  = $("#salida_desde").val();
			var salida_hasta  = $("#salida_hasta").val();
			var pago_desde    = $("#pago_desde").val();
			var pago_hasta    = $("#pago_hasta").val();
			var tole_entrada  = $("#tolerancia_entrada").val();
			var tole_salida   = $("#tolerancia_salida").val();

			if(entrada_desde=='' || entrada_hasta=='')
			{
				alert("Debe indicar el lapso de entrada");
				return false;
			}

			if(salida_desde=='' || salida_hasta=='')
			{
				alert("Debe indicar el lapso de salida");
				return false;
			}

			if(pago_desde=='' || pago_hasta=='')
			{
				alert("Debe indicar el lapso de pago");
				return false;
			}

			if(tole_entrada=='' || tole_salida=='')
			{
				alert("Debe indicar la tolerancia de entrada y salida");
				return false;
			}

			if(isNaN(tole_entrada) || isNaN(tole_salida) || tole_entrada<0 || tole_salida<0)
			{
				alert("La tolerancia de entrada y salida debe ser un numero entero positivo");
				return false;
			}
			//$( "#formPrincipal" ).submit();
		}
	});
});
</script>
</body>
</html>