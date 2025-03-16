<?php
error_reporting(E_ALL ^ E_DEPRECATED);
require_once "config/db.php";
require_once "utils/funciones_procesar.php";
//---------------------------------------------------------------
$conexion =  mysqli_connect( $params['host'], $params['user'], $params['password'], $params['dbname'] ) or
        die( 'Could not open connection to server' );
mysqli_query($conexion, 'SET CHARACTER SET utf8');

$ficha   = (isset($_GET['ficha'])) ? $_GET['ficha']:$_POST['ficha'];
$mes     = (isset($_GET['mes'])) ? $_GET['mes']:$_POST['mes'];
$tip_inc = (isset($_GET['tip_inc'])) ? $_GET['tip_inc']:$_POST['tip_inc'];
if (isset($_GET['fecha'])) 
{
	$fecha = date("d-m-Y", strtotime($_GET['fecha']));
}elseif (isset($_POST['fecha'])) 
{
	$fecha = date("d-m-Y", strtotime($_POST['fecha']));
}else
{
	$fecha = date('d-m-Y');
}

$accion = (isset($_GET['accion'])) ? $_GET['accion']:$_POST['accion'];
$inicio = isset($_GET['inicio']) ? $_GET['inicio'] : 0;
$titulo = (isset($accion) && $accion == 'add') ? "Agregar registro de Asistencia":"Editar registro de Asistencia";
//---------------------------------------------------------------
$fecha_c = date("Y-m-d", strtotime($fecha));
//---------------------------------------------------------------
$fec_sig = date ( 'Y-m-j' , strtotime ( '+1 day' , strtotime ( $fecha_c ) ) );
$fec_ant = date ( 'Y-m-j' , strtotime ( '-1 day' , strtotime ( $fecha_c ) ) );
//---------------------------------------------------------------
$res = $conexion->query("SELECT a.*,b.descrip AS gerencia,c.descrip AS dpto,d.descrip AS seccion
	FROM nompersonal AS a
	LEFT JOIN nomnivel1 AS b ON a.codnivel1 = b.codorg
	LEFT JOIN nomnivel2 AS c ON a.codnivel2 = c.codorg
	LEFT JOIN nomnivel3 AS d ON a.codnivel3 = d.codorg
	WHERE a.ficha ='$ficha'") or die(mysqli_error($conexion));
$empleado = mysqli_fetch_array($res);
//---------------------------------------------------------------
$res = $conexion->query("SELECT a.*,b.descripcion,b.entrada AS entrada_t,b.salida AS salida_t
	FROM caa_resumen AS a 
	LEFT JOIN nomturnos AS b ON a.turno_id = b.turno_id
	WHERE a.ficha = '$ficha' AND a.fecha = '$fecha_c'") or die(mysqli_error($conexion));
$registro = mysqli_fetch_array($res);
$tiempo   = diff_fechora($registro['fecha']." ".$registro['entrada_t'],$registro['fecha']." ".$registro['salida_t']);
//---------------------------------------------------------------
$tip_just = $conexion->query("SELECT * FROM `caa_incidencias` WHERE `tipo` = '$tip_inc'") or die(mysqli_error($conexion));
//---------------------------------------------------------------
$res4 = $conexion->query("SELECT * FROM `nomturnos`") or die(mysqli_error($conexion));
//---------------------------------------------------------------
if (isset($_POST['btn_enviar']))
{
	switch ($accion) {
		case 'add':
			//Agregar un nuevo registro a la '$ficha'
			if (!validar_registro($_POST['ficha'],$fecha_c,$conexion))
			{
				$res = $conexion->query("INSERT INTO `caa_resumen`(`ficha`, `fecha`, `entrada`, `salida`, `tiempo`, `tardanza`, `h_extra`, `recargo_25`, `recargo_50`, `jornada`, `turno_id`) VALUES ( '".$_POST['ficha']."', '".$fecha_c."', '".$_POST['entrada']."', '".$_POST['salida']."', '".$_POST['tiempo']."', '".$_POST['tardanza']."', '".$_POST['h_extra']."', '".$_POST['recargo_25']."', '".$_POST['recargo_50']."', '".$_POST['jornada']."', '".$_POST['turno_id']."')") or die(mysqli_error($conexion));
				if ($res) {
					header("location:gestion_incidencias.php?ficha=".$_POST['ficha']."&&fecha=".$_POST['fecha']."&&msj=1");
				}else{
					header("location:gestion_incidencias.php?ficha=".$_POST['ficha']."&&fecha=".$_POST['fecha']."&&msj=2");
				}
			}else{
				header("location:resumen_asistencias.php?ficha=".$_POST['ficha']."&&msj=".$fecha_c."&&ficha=".$ficha);
			}
			break;
		case 'edit':
			//Editar un registro a la '$ficha'
			$conexion->query("SET AUTOCOMMIT=0");
			$conexion->query("START TRANSACTION");

			$a1 = $conexion->query("UPDATE `caa_resumen` SET `entrada`='".$_POST['entrada']."', `salida`='".$_POST['salida']."', `tiempo`='".$_POST['tiempo']."', `tardanza`='".$_POST['tardanza']."', `h_extra`='".$_POST['h_extra']."', `recargo_25`='".$_POST['recargo_25']."', `recargo_50`='".$_POST['recargo_50']."', `jornada`='".$_POST['jornada']."', `ausencia`=0, `carga`=0 WHERE ficha = '".$_POST['ficha']."' AND fecha = '".$_POST['fecha_c']."'") or die(mysqli_error($conexion));
			$a2 = $conexion->query("DELETE FROM `caa_incidencias_empleados` WHERE ficha = '".$_POST['ficha']."' AND fecha = '".$_POST['fecha_c']."'") or die(mysqli_error($conexion));

			$sql  = "INSERT INTO caa_incidencias_empleados (ficha,fecha,id_incidencia) VALUES ";
			$sql .= ((isset($_POST['justificacion']))&&($_POST['justificacion']!=0))?"('".$_POST['ficha']."','".$_POST['fecha_c']."','".$_POST['justificacion']."'),":"";
			$sql .= (strtotime($_POST['h_extra']) != strtotime("00:00:00")) ? "(".$_POST['ficha'].",'".$_POST['fecha_c']."','26')":"";
			$sql .= (strtotime($_POST['recargo_25']) != strtotime("00:00:00")) ? ",(".$_POST['ficha'].",'".$_POST['fecha_c']."','27')":"";
			$sql .= (strtotime($_POST['recargo_50']) != strtotime("00:00:00")) ? ",(".$_POST['ficha'].",'".$_POST['fecha_c']."','28')":"";
			$a3 = $conexion->query($sql);

			if ($a1 and $a2 and $a3) {
			    $conexion->query("COMMIT");
			    header("location:gestion_incidencias.php?ficha=".$_POST['ficha']."&&fecha=".$_POST['fecha']."&&msj=1&&mes=".$_POST['mes']);
			} else {        
			    $conexion->query("ROLLBACK");
			    header("location:gestion_incidencias.php?ficha=".$_POST['ficha']."&&fecha=".$_POST['fecha']."&&msj=2&&mes=".$_POST['mes']);
			}
			break;
		default:
			# code...
			break;
	}
}
//---------------------------------------------------------------
include("vistas/layouts/header.php");
?>
<div class="container-fluid">
<!-- BEGIN CONTENT -->
<div class="">
	<div class="">
		<!-- BEGIN PAGE CONTENT-->
		<div class="row">
			<div class="col-sm-12 col-md-12 col-lg-12 ">
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
							<?php echo $titulo; ?>
						</div>
						<div class="actions">
			                <a class="btn btn-sm blue"  onclick="javascript: window.location='resumen_asistencias.php?ficha=<?php echo $ficha; ?>&&fecha=<?php echo $fecha;?>&&mes=<?php echo $mes; ?>'">
			                  <i class="fa fa-arrow-left"></i> Regresar
			                </a>
		              	</div>
					</div>
					<div class="portlet-body form" id="blockui_portlet_body">
						<form action="form_asistencia.php" class="form-horizontal" method="post" enctype="multipart/form-data" style="margin-bottom: 5px;">
							<div class="form-body">
								<div class="row">
									<div class="col-sm-12 col-md-12 col-lg-12">
										<div class="row">
											<div class="col-sm-5 col-md-5 col-lg-5">
												<div class="panel panel-default">
													<br>
													<div class="row">
													<div class="col-sm-12 col-md-12 col-lg-12">
														<div class="form-group" style="padding-left: 5%;padding-right: 5%;">
															<input type="hidden" id="mes" name="mes" value="<?php echo $mes; ?>">
															<input type="hidden" id="accion" name="accion" value="<?php echo $accion; ?>">
															<input type="hidden" id="ficha" name="ficha" value="<?php echo $ficha; ?>">
															<?php if ($accion == 'edit'): ?>
															<input type="hidden" id="fecha_c" name="fecha_c" value="<?php echo $fecha_c; ?>">
															<?php endif ?>

															<label class="col-sm-2 col-md-2 col-lg-2 control-label" for="">FICHA:</label>
															<div class="col-sm-2 col-md-2 col-lg-2">
																<input type="text" class="form-control input-sm" 
															       id="ficha" name="ficha" value="<?php echo $empleado['ficha'] ?>" readonly>
															</div>
													
															<label class="col-sm-2 col-md-2 col-lg-2 control-label" for="">FECHA:</label>
															<div class="col-sm-4 col-md-4 col-lg-4">
																<div class="form-group">
													                <?php if ($accion == 'add'): ?>	
													                <div class="input-group date" id="datetimepicker0">
													                    <input type="text" id="fecha" name="fecha" class="form-control input-sm" />
													                    <span class="input-group-addon">
													                        <span class="glyphicon glyphicon-calendar"></span>
													                    </span>
													                </div>
													                <?php else: ?>
																	<div class="input-group" id="">
													                    <input type="text" id="fecha" name="fecha" class="form-control input-sm" value="<?php echo $fecha;?>"readonly/>
													                   	<span class="input-group-addon">
													                        <span class="glyphicon glyphicon-calendar"></span>
													                    </span>
																	</div>
													                <?php endif ?>
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
															<label class="col-sm-3 col-md-3 col-lg-3 control-label" for="">DIRECCION:</label>
															<div class="col-sm-9 col-md-9 col-lg-9">
																<input type="text" class="form-control input-sm" 
															       id="" name="" value="<?php echo $empleado['gerencia'] ?>" readonly>
															</div>
														</div>

														<div class="form-group" style="padding-left: 5%;padding-right: 5%;">
															<label class="col-sm-3 col-md-3 col-lg-3 control-label" for="">DEPARTAMENTO:</label>
															<div class="col-sm-9 col-md-9 col-lg-9">
																<input type="text" class="form-control input-sm" 
															       id="" name="" value="<?php echo $empleado['dpto'] ?>" readonly>
															</div>
														</div>

														<div class="form-group" style="padding-left: 5%;padding-right: 5%;">
															<label class="col-sm-3 col-md-3 col-lg-3 control-label" for="">SECCION:</label>
															<div class="col-sm-9 col-md-9 col-lg-9">
																<input type="text" class="form-control input-sm" 
															       id="" name="" value="<?php echo $empleado['seccion'] ?>" readonly>
															</div>
														</div>

														<div class="form-group" style="padding-left: 5%;padding-right: 5%;">
															<label class="col-sm-3 col-md-3 col-lg-3 control-label" for="">TURNO:</label>
															<div class="col-sm-6 col-md-6 col-lg-6">
																<input type="hidden" id="turno_id" value="<?php echo $registro['turno_id'];?>">
																<input type="text" class="form-control input-sm" 
															       id="turno_desc" name="" value="<?php echo $registro['descripcion']." - ".$registro['entrada_t']." - ".$registro['salida_t']; ?>" readonly>
															</div>
															<div class="col-sm-3 col-md-3 col-lg-3">
																<button type="button" class="btn btn-sm blue" data-toggle="modal" data-target="#exampleModal3" data-turnoid="<?php echo $registro['turno_id'] ?>" data-fecha="<?php echo $fecha_c ?>" data-ficha="<?php echo $empleado['ficha'] ?>" data-turno="<?php echo $registro['descripcion']." - ".$registro['entrada_t']." - ".$registro['salida_t'] ?>" data-titulo="Cambiar Turno">Cambiar</button>
															</div>
														</div>

													</div>
													</div>
												</div>

											</div>

											<div class="col-sm-7 col-md-7 col-lg-7">
												<div class="panel panel-default">
													<br>
													<div class="row">
													<div class="col-sm-12 col-md-12 col-lg-12">

														<div class="form-group" style="padding-left: 5%;padding-right: 5%;padding-top: 0px;padding-bottom: 0px;">
															<label class="col-sm-12 col-md-12 col-lg-12 control-label" style="text-align: center;"><strong>Detalles Generales</strong></label>
														</div>
														
														<hr>

														<div class="form-group" style="padding-left: 5%;padding-right: 5%;">
															<div class="col-sm-3 col-md-3 col-lg-3">
																<label class="control-label" style="text-align: right;">
																<strong>Entrada:</strong></label>
															</div>
															<div class="col-sm-3 col-md-3 col-lg-3">
																<input type="hidden" id="fec_ent" name="fec_ent" value="<?php echo $registro['fecha'] ?>">
												                <div class='input-group date' id='datetimepicker1'>
												                    <input type="text" class="form-control input-sm" 
														       id="entrada" name="entrada" value="<?php echo ($tip_inc==4) ? $registro['entrada_t']:$registro['entrada']; ?>">
												                    <span class="input-group-addon">
												                        <span class="glyphicon glyphicon-time"></span>
												                    </span>
												                </div>
															</div>
															<div class="col-sm-3 col-md-3 col-lg-3">
																<label class="control-label" style="text-align: right;">
																<strong>Salida:</strong></label>
															</div>
															<div class="col-sm-3 col-md-3 col-lg-3">
																<input type="hidden" id="fec_sal" name="fec_sal" value="<?php echo ($registro['fec_sal']=="00:00:00")?$registro['fecha']:$registro['fecha'];?>">
												                <div class='input-group date' id='datetimepicker2'>
												                    <input type="text" class="form-control input-sm" 
														       id="salida" name="salida" value="<?php echo ($tip_inc==4) ? $registro['salida_t']:$registro['salida']; ?>" <?php echo ($tip_inc==4) ?"readonly":"";?>>
												                    <span class="input-group-addon">
												                        <span class="glyphicon glyphicon-time"></span>
												                    </span>
												                </div>
															</div>
														</div>

														<div class="form-group" style="padding-left: 5%;padding-right: 5%;">
															<div class="col-sm-3 col-md-3 col-lg-3">
																<label class="control-label" style="text-align: right;">
																<strong>H. Trabajadas:</strong></label>
															</div>
															<div class="col-sm-3 col-md-3 col-lg-3">
																
												                <div class='input-group date' id='datetimepicker3'>
												                    <input type="text" class="form-control input-sm" 
														       id="tiempo" name="tiempo" value="<?php echo ($tip_inc==4) ? $tiempo:$registro['tiempo']; ?>" readonly>
												                    <span class="input-group-addon">
												                        <span class="glyphicon glyphicon-time"></span>
												                    </span>
												                </div>
															</div>
															<div class="col-sm-3 col-md-3 col-lg-3">
																<label class="control-label" style="text-align: right;">
																<strong>Tardanza:</strong></label>
															</div>
															<div class="col-sm-3 col-md-3 col-lg-3">

												                <div class='input-group date' id='datetimepicker4'>
												                   <input type="text" class="form-control input-sm" 
														       id="tardanza" name="tardanza" value="<?php echo (isset($registro['tardanza'])) ? $registro['tardanza']:""; ?>" readonly>
												                    <span class="input-group-addon">
												                        <span class="glyphicon glyphicon-time"></span>
												                    </span>
												                </div>
															</div>
														</div>

														<div class="form-group" style="padding-left: 5%;padding-right: 5%;">
															<div class="col-sm-3 col-md-3 col-lg-3">
																<label class="control-label" style="text-align: right;">
																<strong>E. Extras:</strong></label>
															</div>
															<div class="col-sm-3 col-md-3 col-lg-3">

												                <div class='input-group date' id='datetimepicker5'>
												                    <input type="text" class="form-control input-sm" 
														       id="h_extra" name="h_extra" value="<?php echo (isset($registro['h_extra'])) ? $registro['h_extra']:""; ?>" readonly>
												                    <span class="input-group-addon">
												                        <span class="glyphicon glyphicon-time"></span>
												                    </span>
												                </div>
															</div>
															<div class="col-sm-3 col-md-3 col-lg-3">
																<label class="control-label" style="text-align: right;">
																<strong>Recargo 25%:</strong></label>
															</div>
															<div class="col-sm-3 col-md-3 col-lg-3">
																
												                <div class='input-group date' id='datetimepicker6'>
												                    <input type="text" class="form-control input-sm" 
														       id="recargo_25" name="recargo_25" value="<?php echo (isset($registro['recargo_25'])) ? $registro['recargo_25']:""; ?>" readonly>
												                    <span class="input-group-addon">
												                        <span class="glyphicon glyphicon-time"></span>
												                    </span>
												                </div>
															</div>
														</div>

														<div class="form-group" style="padding-left: 5%;padding-right: 5%;">
															<div class="col-sm-3 col-md-3 col-lg-3">
																<label class="control-label" style="text-align: right;">
																<strong>Recargo 50%:</strong></label>
															</div>
															<div class="col-sm-3 col-md-3 col-lg-3">
																
												                <div class='input-group date' id='datetimepicker7'>
												                    <input type="text" class="form-control input-sm" 
														       id="recargo_50" name="recargo_50" value="<?php echo (isset($registro['recargo_50'])) ? $registro['recargo_50']:""; ?>" readonly>
												                    <span class="input-group-addon">
												                        <span class="glyphicon glyphicon-time"></span>
												                    </span>
												                </div>
															</div>
															<div class="col-sm-3 col-md-3 col-lg-3">
																<label class="control-label" style="text-align: right;">
																<strong>Tipo Jornada:</strong></label>
															</div>
															<div class="col-sm-3 col-md-3 col-lg-3">
																<input type="text" class="form-control input-sm" 
															       id="jornada" name="jornada" value="<?php echo $registro['jornada'] ?>" readonly>
															</div>
														</div>

														<div class="form-group" style="padding-left: 5%;padding-right: 5%;">
															<div class="col-sm-3 col-md-3 col-lg-3">
																<label class="control-label" style="text-align: right;">
																<strong>Justificacion:</strong></label>
															</div>
															<div class="col-sm-3 col-md-3 col-lg-3">
																<select name="justificacion" id="justificacion" class="form-control input-sm" required>
																	<option value="0">SELECCIONE</option>
																	<?php while ($j=mysqli_fetch_array($tip_just)): ?>
																	<?php echo '<option value="'.$j['id'].'">'.$j['descripcion'].'</option>'; ?>
																	<?php endwhile ?>
																</select>
															</div>
															<div class="col-sm-3 col-md-3 col-lg-3">
																<a href="gestion_incidencias.php?ficha=<?php echo $ficha; ?>&&fecha=<?php echo $fecha_c; ?>" class="btn btn-sm blue pull-right">
																	Cancelar
																</a>
															</div>
															<div class="col-sm-3 col-md-3 col-lg-3">
																<input type="submit" name="btn_enviar" id="btn_enviar" class="btn btn-sm blue" value="Enviar">
															</div>
														</div>

													</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>

								<div class="text-center">
									<a class="btn btn-sm blue active" href="form_asistencia.php?ficha=<?php echo $ficha;?>&&fecha=<?php echo $fec_ant;?>">
					                	<i class="fa fa-arrow-left"></i> Dia Enterior
					                </a>
									<a class="btn btn-sm blue active" href="form_asistencia.php?ficha=<?php echo $ficha;?>&&fecha=<?php echo $fec_sig;?>">
					                	Dia Siguinte <i class="fa fa-arrow-right"></i>
					                </a>
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
<?php include("vistas/layouts/footer.php");?>
<script>$('select').selectpicker();</script>
<script src="../../../includes/assets/plugins/moment/min/moment-with-locales.js"></script>
<script src="../../../includes/assets/plugins/bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>
<script src="../../../includes/assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
<script src="utils/funciones_ajax.js"></script>

<div class="bd-example">
  	<div class="modal fade" id="exampleModal3" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	    <div class="modal-dialog" role="document">
	      	<div class="modal-content">
				<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title" id="exampleModalLabel"></h4>
				</div>
	        <div class="modal-body">
				<div class="form-group">
					<label for="turno" class="form-control-label">Turno Actual:</label>
					<input type="text" class="form-control" id="turno" readonly>
					<input type="hidden" id="turno_id">
					<input type="hidden" id="ficha">
				</div>
				<div class="form-group">
					<label for="registro" class="form-control-label">Turnos:</label>
					<select name="cam_turno" id="cam_turno" class="form-control">
						<option value="">Seleccione</option>
						<?php while ($t = mysqli_fetch_array($res4)): ?>
						<?php echo '<option value="'.$t['turno_id'].'">'.$t['descripcion'].'</option>'?>
						<?php endwhile ?>
					</select>
				</div>
	        </div>
	        <div class="modal-footer">
		        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
		        <button type="button" id="cambio_tur" class="btn btn-sm blue" data-dismiss="modal">Aprobar Cambio</button>
	        </div>
	      </div>
	    </div>
  	</div>
</div>

<script type="text/javascript">
$(document).ready(function(){

	moment.locale('es-do');
	moment("00:00:00", "HH:mm:ss");
    $(function () {
    	$('#datetimepicker0').datetimepicker({
            format: 'DD-MM-YYYY'
        });
        $('#datetimepicker1').datetimepicker({
            format: 'HH:mm:ss'
        });
        $('#datetimepicker2').datetimepicker({
            format: 'HH:mm:ss'
        });
    });

	$( "#entrada" ).blur(function(event){
	    calcularTiempo($('#ficha').val(),$('#fec_ent').val(),$('#fec_sal').val(),$('#entrada').val(),$('#salida').val(),$('#turno_id').val(),0);
	});
	$( "#salida" ).blur(function(event) {
	    validar_ea($('#fec_ent').val(), $('#fec_sal').val(), $('#entrada').val(), $('#salida').val());
	    calcularTiempo($('#ficha').val(), $('#fec_ent').val(), $('#fec_sal').val(), $('#entrada').val(), $('#salida').val(), $('#turno_id').val(),0);
	});
	$( "#fecha" ).blur(function(event) {
	    calcularTurno($('#ficha').val(),$('#fecha').val());
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
	    cambiarTurno( $('#turno_id').val(),$('#ficha').val(),$('#cam_turno').val() );
	});
	$( "#btn_enviar" ).click(function(event){
	    if ($('#turno_id').val() == 0) {

	    }
	});
});
</script>
</body>
</html>