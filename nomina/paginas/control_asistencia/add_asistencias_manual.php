<?php
error_reporting(E_ALL ^ E_DEPRECATED);
require_once "config/db.php";
require_once "libs/importar_archivos.php";

//---------------------------------------------------------------
$conexion =  mysqli_connect( $params['host'], $params['user'], $params['password'], $params['dbname'] ) or die( 'Could not open connection to server' );
mysqli_query($conexion, 'SET CHARACTER SET utf8');

$ficha   = $_GET['ficha'];
$fecha   = isset($_GET['fecha']) ? $_GET['fecha'] : date('d-m-Y');
$mes     = (isset($_GET['mes'])) ? $_GET['mes']:$_POST['mes'];
//---------------------------------------------------------------
$fecha_c = date("Y-m-d", strtotime( $fecha ));
//---------------------------------------------------------------
$fec_sig = date ( 'Y-m-d' , strtotime ( '+1 day' , strtotime ( $fecha_c ) ) );
$fec_ant = date ( 'Y-m-d' , strtotime ( '-1 day' , strtotime ( $fecha_c ) ) );
//---------------------------------------------------------------
$res = $conexion->query("SELECT a.*,b.descrip AS gerencia,c.descrip AS dpto,d.descrip AS seccion
	FROM nompersonal AS a
	LEFT JOIN nomnivel1 AS b ON a.codnivel1 = b.codorg
	LEFT JOIN nomnivel2 AS c ON a.codnivel2 = c.codorg
	LEFT JOIN nomnivel3 AS d ON a.codnivel3 = d.codorg
	WHERE a.ficha ='$ficha'
	") or die(mysqli_error($conexion));
$empleado = mysqli_fetch_array($res);
//---------------------------------------------------------------
$res = $conexion->query("SELECT a.*,b.descripcion,b.entrada AS entrada_t,b.salida AS salida_t
	FROM caa_resumen AS a 
	LEFT JOIN nomturnos AS b ON a.turno_id = b.turno_id
	WHERE a.ficha = '$ficha' AND a.fecha = '$fecha_c'") or die(mysqli_error($conexion));
$registro = mysqli_fetch_array($res);
//---------------------------------------------------------------
$res = $conexion->query("SELECT a.turno_id,b.descripcion,b.entrada AS entrada_t,b.salida AS salida_t
	FROM nomcalendarios_personal AS a
	LEFT JOIN nomturnos AS b ON a.turno_id = b.turno_id
	WHERE a.ficha = '$ficha' AND a.fecha = '$fecha_c'") or die(mysqli_error($conexion));
	$turno = mysqli_fetch_array($res);
//---------------------------------------------------------------
$res2 = $conexion->query("SELECT a.*,b.descripcion,b.acronimo
	FROM caa_incidencias_empleados AS a
	LEFT JOIN caa_incidencias AS b ON a.id_incidencia = b.id
	WHERE a.ficha = '$ficha' AND a.fecha = '$fecha_c' 
	ORDER BY a.fecha DESC") or die(mysqli_error($conexion));
//---------------------------------------------------------------
$res3 = $conexion->query("SELECT * FROM caa_incidencias WHERE tipo = 2 AND id NOT IN (SELECT id_incidencia 
	FROM caa_incidencias_empleados WHERE ficha='$ficha' AND fecha='$fecha_c')") or die(mysqli_error($conexion));
//---------------------------------------------------------------
$res4 = $conexion->query("SELECT * FROM nomturnos") or die(mysqli_error($conexion));
//---------------------------------------------------------------
$dias = array( "Domingo", "Lunes", "Martes", "Miercoles"," Jueves", "Viernes", "Sabado" );
//---------------------------------------------------------------
?>
<?php include("vistas/layouts/header.php"); ?>
<body class="page-header-fixed page-full-width" marginheight="0">
<script type="text/javascript">
function ConfirmDemo() {
//Ingresamos un mensaje a mostrar
var mensaje = confirm("¿Seguro desea Eliminar el registro?");
}
</script>
<div class="page-container">
<!-- BEGIN CONTENT -->
<div class="page-content-wrapper">
	<div class="page-content">
		<!-- BEGIN PAGE CONTENT-->
		<div class="row">
			<div class="hidden-xs col-md-3 col-lg-3">
			</div>
			<div class="col-sm-12 col-md-6 col-lg-6">
	          	<?php if ((isset($_GET['msj']))&&($_GET['msj'] == 1)): ?>
	            <div class="alert alert-success fade in">
	                <a href="#" class="close" data-dismiss="alert">&times;</a>
	                <strong>Exito!</strong> Proceso exitoso..!!
	            </div>
	            <?php elseif ((isset($_GET['msj']))&&($_GET['msj'] != 1)): ?>
	            <div class="alert alert-danger fade in">
	                <a href="#" class="close" data-dismiss="alert">&times;</a>
	                <strong>Error!</strong> Ocurrion un problema en el proceso..!!
	            </div>
	            <?php endif ?>
				<!-- BEGIN EXAMPLE TABLE PORTLET-->
				<div class="portlet box blue">
					<div class="portlet-title">
						<div class="caption">
							Registrar Asistencia Manual
						</div>
						<div class="actions">
			                <a class="btn btn-sm blue"  onclick="javascript: window.location='registro_manual.php?modulo=365'">
			                  <i class="fa fa-arrow-left"></i> Regresar
			                </a>
		              	</div>
					</div>
					<div class="portlet-body form" id="blockui_portlet_body">
						<form action="procesar_asistencias_manuales.php" class="form-horizontal" method="post" enctype="multipart/form-data" style="margin-bottom: 5px;">
							<div class="form-body">
								<div class="row">
									<div class="col-sm-12 col-md-12 col-lg-12">
										<div class="row">
											<div class="col-sm-12 col-md-12 col-lg-12">
												<div class="panel panel-default">
													<br>
													<div class="row">
														<div class="col-sm-12 col-md-12 col-lg-12">
															<div class="form-group" style="padding-left: 5%;padding-right: 5%;">
																<label class="col-sm-2 col-md-2 col-lg-2 control-label" for="">FICHA:</label>
																<div class="col-sm-2 col-md-2 col-lg-2">
																	<input type="text" class="form-control input-sm" 
																       id="ficha" name="ficha" value="<?php echo $empleado['ficha'] ?>" readonly>
																</div>
																<input type="hidden" name="mes" id="mes" value="<?= $mes ?>">
																<label class="col-sm-2 col-md-2 col-lg-2 control-label" for="">FECHA:</label>
																<div class="col-sm-4 col-md-4 col-lg-4">
																	<div class="input-group">
																		<input  class="form-control input-sm" type="text" id="fecha_registro" name="fecha_registro" value="<?php echo date("Y-m-d", strtotime($fecha)); ?>">
																		<span class="input-group-btn">
																			<button class="btn default" type="button" style="height: 28px;"><i class="fa fa-calendar"></i></button>
																		</span>
																	</div>
																</div>
															</div>
															<hr>
															<div class="form-group" style="padding-left: 5%;padding-right: 5%;">
																<label class="col-sm-3 col-md-3 col-lg-3 control-label" for="">EMPLEADO:</label>
																<div class="col-sm-9 col-md-9 col-lg-9">
																	<input type="text" class="form-control input-sm" 
																       id="" name="" value="<?php echo $empleado['apenom'] ?>" readonly>
																</div>
															</div>

															<div class="form-group" style="padding-left: 5%;padding-right: 5%;">
																<label class="col-sm-3 col-md-3 col-lg-3 control-label" for="">CEDULA:</label>
																<div class="col-sm-9 col-md-9 col-lg-9">
																	<input type="text" class="form-control input-sm" 
																       id="" name="" value="<?php echo $empleado['cedula'] ?>" readonly>
																</div>
															</div>

															<div class="form-group" style="padding-left: 5%;padding-right: 5%;">
																<label class="col-sm-3 col-md-3 col-lg-3 control-label" for="">ENTRADA:</label>
																<div class="col-sm-9 col-md-9 col-lg-9">
																	<input type="text" class="form-control input-sm time" id="entrada" name="entrada" value="<?php echo $empleado['dpto'] ?>" required>
																</div>
															</div>

															<div class="form-group" style="padding-left: 5%;padding-right: 5%;">
																<label class="col-sm-3 col-md-3 col-lg-3 control-label" for="">SALIDA:</label>
																<div class="col-sm-9 col-md-9 col-lg-9">
																	<input type="text" class="form-control input-sm time" id="salida" name="salida" value="<?php echo $empleado['seccion'] ?>" required>
																</div>
															</div>

															<div class="form-group" style="padding-left: 5%;padding-right: 5%;">
																<label class="col-sm-3 col-md-3 col-lg-3 control-label" for="">TURNO:</label>
																<div class="col-sm-6 col-md-6 col-lg-6">
																	<!--<input type="text" class="form-control input-sm" id="turno_desc" name="" value="<?php echo $turno['descripcion']." - ".$turno['entrada_t']." - ".$turno['salida_t']; ?>" readonly>-->

																	<select name="turno_id" id="turno_id" class="form-control input-sm">
																		<option value="#">Seleccione</option>
																		<?php while ($t = mysqli_fetch_array($res4)): ?>
																		<?php echo '<option value="'.$t['turno_id'].'">'.$t['descripcion'].'</option>'?>
																		<?php endwhile ?>
																	</select>
																</div>
																<div class="col-sm-3 col-md-3 col-lg-3">
																	<input type="submit" class="btn btn-sm blue pull-right" id="enviar_inc" name="enviar_inc" value="Agregar">
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
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
<?php include("vistas/layouts/footer.php"); ?>
<script>$('select').selectpicker();</script>

<script src="utils/funciones_ajax.js"></script>

<script>
$(document).ready(function(){
	$('.time').mask('Hh:Mm:Ss', {
		translation: {
			'H': {
				pattern: /[0-2]/, optional: true
			},
			'h': {
				pattern: /[0-9]/, optional: true
			},
			'M': {
				pattern: /[0-5]/, optional: true
			},
			'm': {
				pattern: /[0-9]/, optional: true
			},
			'S': {
				pattern: /[0-5]/, optional: true
			},
			's': {
				pattern: /[0-9]/, optional: true
			}
		}
	});
	$('#exampleModal1').on('show.bs.modal', function (event) {
		var button   = $(event.relatedTarget)
		var titulo   = button.data('titulo')
		var campo    = button.data('campo')
		var registro = button.data('registro')
		var ficha    = button.data('ficha')
		var fecha    = button.data('fecha')
		var fecha_c  = button.data('fecha_c')
		var modal = $(this)
		modal.find('.modal-title').text(titulo)
		modal.find('.modal-body #aprob_campo').val(campo)
		modal.find('.modal-body #aprob_registro').val(registro)
		modal.find('.modal-body #aprob_ficha').val(ficha)
		modal.find('.modal-body #aprob_fecha').val(fecha)
	});
	$('#exampleModal3').on('show.bs.modal', function (event) {
		var button   = $(event.relatedTarget)
		var titulo   = button.data('titulo')
		var ficha    = button.data('ficha')
		var turno    = button.data('turno')
		var turno_id = button.data('turnoid')
		var modal = $(this)
		modal.find('.modal-title').text(titulo)
		modal.find('.modal-body #ficha').val(ficha)
		modal.find('.modal-body #turno').val(turno)
		modal.find('.modal-body #turno_id').val(turno_id)
	});
	$( "#cambio_tur" ).click(function(event){
	    console.log('entra');
	    cambiarTurno( $('#turno_id').val(), $('#ficha').val(), $('#cam_turno').val(),1 );
	});

	$( "#aprobar_tiempo" ).click(function(event){
		window.location.href = 'utils/aprobar_tiempo.php?aprob_ficha='+$('#aprob_ficha').val()+'&aprob_fecha='+$('#aprob_fecha').val()+'&aprob_campo='+$('#aprob_campo').val()+'&mes='+$('#mes').val();
	    console.log( 'utils/aprobar_tiempo.php?aprob_ficha='+$('#aprob_ficha').val()+'&aprob_fecha='+$('#aprob_fecha').val()+'&aprob_campo='+$('#aprob_campo').val()+'&mes='+$('#mes').val() );
	});
});
</script>
</body>
</html>