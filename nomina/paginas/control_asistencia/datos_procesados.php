<?php
error_reporting(E_ALL ^ E_DEPRECATED);
require_once "config/db.php";
require_once "libs/importar_archivos.php";
include ("../func_bd.php");

$archivo_reloj = $_GET['archivo'];

if( isset($_POST['btn-guardar']) )
{
	$configuracion = (isset($_POST['configuracion']))  ? $_POST['configuracion'] : '';
	$fecha_inicio  = (!empty($_POST['fecha_inicio']))  ? $_POST['fecha_inicio']  : date('d-m-Y');
	$fecha_fin     = (!empty($_POST['fecha_fin']))     ? $_POST['fecha_fin']     : date('d-m-Y');
	$hora_inicio   = (!empty($_POST['fecha_inicio']) && !empty($_POST['hora_inicio']))  ? $_POST['hora_inicio']   : '00:00:00';
	$hora_fin      = (!empty($_POST['fecha_fin'])    && !empty($_POST['hora_fin']) )    ? $_POST['hora_fin']      : '23:59:59';

	if(!empty($configuracion) && !empty($_FILES['archivo']))
	{
			$fecha_inicio = DateTime::createFromFormat('d-m-Y H:i:s', "{$fecha_inicio} {$hora_inicio}");
			$fecha_inicio = $fecha_inicio->format('Y-m-d H:i:s');
			$fecha_fin    = DateTime::createFromFormat('d-m-Y H:i:s', "{$fecha_fin} {$hora_fin}");
			$fecha_fin    = $fecha_fin->format('Y-m-d H:i:s');
			$extension    = obtener_extension($_FILES['archivo']['name']);

			if( in_array($extension, array("csv", "txt", "log")) )
				require("utils/cargar_archivo_txt.php"); 
	}
	else
	{
		$flash_message = "¡Error! Todos los campos son obligatorios";	
		$flash_class   = 'alert-danger';
	}
}
?>
<?php include("vistas/layouts/header.php"); ?>
<link href="web/css/agregar_archivo_reloj.css?<?php echo time(); ?>" rel="stylesheet" type="text/css"/>
<body class="page-header-fixed page-full-width" marginheight="0">
<div class="page-container">
<!-- BEGIN CONTENT -->
<div class="page-content-wrapper">
	<div class="page-content">
		<!-- BEGIN PAGE CONTENT-->
		<div class="row">
			<div class="col-md-12">
	          <?php
	              if(!empty($flash_message))
	              {
	                ?> <div class="alert <?php echo $flash_class ; ?>"><button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button><?php echo $flash_message; ?></div> <?php
	              }
	          ?>
				<!-- BEGIN EXAMPLE TABLE PORTLET-->
				<div class="portlet box blue">
					<div class="portlet-title">
						<div class="caption">
							Datos Procesados
						</div>
						<div class="actions">
			                <a class=" btn btn-sm blue"  onclick="javascript: window.location='#'">
			                  <i class="fa fa-plus"></i>
			                  Agregar
			                </a>
			                <a class="btn btn-sm blue"  onclick="javascript: window.location='mantenimiento_datos.php?archivo=<?php echo $archivo_reloj; ?>'">
			                  <i class="fa fa-arrow-left"></i> Regresar
			                </a>
		              	</div>
					</div>
					<div class="portlet-body form" id="blockui_portlet_body">
						<form class="form-horizontal" role="form" id="formPrincipal" name="formPrincipal" method="post" enctype="multipart/form-data" style="margin-bottom: 5px;">
							<div class="form-body">
								<div class="row">
									<div class="col-sm-12 col-md-12 col-lg-12">
										<div class="row">
											<div class="col-sm-7 col-md-7 col-lg-7">
												<div class="panel panel-default">
													<br>
													<div class="row">
													<div class="col-sm-12 col-md-12 col-lg-12">

														<div class="form-group" style="padding-left: 5%;padding-right: 5%;">
															<label class="col-sm-2 col-md-2 col-lg-2 control-label" for="">EMPLEADO:</label>
															<div class="col-sm-4 col-md-4 col-lg-4">
																<input type="text" class="form-control input-sm" 
															       id="" name="" value="" >
															</div>

															<label class="col-sm-2 col-md-2 col-lg-2 control-label" for="">FECHA:</label>
															<div class="col-sm-4 col-md-4 col-lg-4">
																<div class="input-group date date-picker">
																	<input name="fecha" id="fecha" class="form-control input-sm" type="text" value="<?php echo isset($fecha) ? $fecha : ''; ?>" />
																	<span class="input-group-btn">
																		<button class="btn default" type="button" style="height: 28px;"><i class="fa fa-calendar"></i></button>
																	</span>
																</div>
															</div>
														</div>
													
													<hr>

														<div class="form-group" style="padding-left: 5%;padding-right: 5%;">
															<label class="col-sm-4 col-md-4 col-lg-4 control-label" for="">EMPLEADO:</label>
															<div class="col-sm-8 col-md-8 col-lg-8">
																<input type="text" class="form-control input-sm" 
															       id="" name="" value="" >
															</div>
														</div>

														<div class="form-group" style="padding-left: 5%;padding-right: 5%;">
															<label class="col-sm-4 col-md-4 col-lg-4 control-label" for="">DIRECCION:</label>
															<div class="col-sm-8 col-md-8 col-lg-8">
																<input type="text" class="form-control input-sm" 
															       id="" name="" value="" >
															</div>
														</div>

														<div class="form-group" style="padding-left: 5%;padding-right: 5%;">
															<label class="col-sm-4 col-md-4 col-lg-4 control-label" for="">DEPARTAMENTO:</label>
															<div class="col-sm-8 col-md-8 col-lg-8">
																<input type="text" class="form-control input-sm" 
															       id="" name="" value="" >
															</div>
														</div>

														<div class="form-group" style="padding-left: 5%;padding-right: 5%;">
															<label class="col-sm-4 col-md-4 col-lg-4 control-label" for="">SECCION:</label>
															<div class="col-sm-8 col-md-8 col-lg-8">
																<input type="text" class="form-control input-sm" 
															       id="" name="" value="" >
															</div>
														</div>

														<div class="form-group" style="padding-left: 5%;padding-right: 5%;">
															<label class="col-sm-4 col-md-4 col-lg-4 control-label" for="">CALENDARIO:</label>
															<div class="col-sm-8 col-md-8 col-lg-8">
																<input type="text" class="form-control input-sm" 
															       id="" name="" value="" >
															</div>
														</div>

													</div>
													</div>
												</div>

											</div>

											<div class="col-sm-5 col-md-5 col-lg-5">
												<div class="panel panel-default">
													<br>
													<div class="row">
													<div class="col-sm-12 col-md-12 col-lg-12">

														<div class="form-group" style="padding-left: 5%;padding-right: 5%;padding-top: 4px;padding-bottom: 4px;">
															<label class="col-sm-3 col-md-3 col-lg-3 control-label" for=""><strong>Sentido</strong></label>
															<label class="col-sm-3 col-md-3 col-lg-3 control-label" for=""><strong>Hora</strong></label>
															<label class="col-sm-3 col-md-3 col-lg-3 control-label" for=""><strong>Inc.</strong></label>
															<label class="col-sm-3 col-md-3 col-lg-3 control-label" for=""><strong>Term.</strong></label>
														</div>
														
														<hr>

														<div class="form-group" style="padding-left: 5%;padding-right: 5%;">
															<div class="col-sm-3 col-md-3 col-lg-3">
																<input type="text" class="form-control input-sm" 
															       id="" name="" value="" >
															</div>
															<div class="col-sm-3 col-md-3 col-lg-3">
																<input type="text" class="form-control input-sm" 
															       id="" name="" value="" >
															</div>
															<div class="col-sm-3 col-md-3 col-lg-3">
																<input type="text" class="form-control input-sm" 
															       id="" name="" value="" >
															</div>
															<div class="col-sm-3 col-md-3 col-lg-3">
																<input type="text" class="form-control input-sm" 
															       id="" name="" value="" >
															</div>
														</div>

														<div class="form-group" style="padding-left: 5%;padding-right: 5%;">
															<div class="col-sm-3 col-md-3 col-lg-3">
																<input type="text" class="form-control input-sm" 
															       id="" name="" value="" >
															</div>
															<div class="col-sm-3 col-md-3 col-lg-3">
																<input type="text" class="form-control input-sm" 
															       id="" name="" value="" >
															</div>
															<div class="col-sm-3 col-md-3 col-lg-3">
																<input type="text" class="form-control input-sm" 
															       id="" name="" value="" >
															</div>
															<div class="col-sm-3 col-md-3 col-lg-3">
																<input type="text" class="form-control input-sm" 
															       id="" name="" value="" >
															</div>
														</div>

														<div class="form-group" style="padding-left: 5%;padding-right: 5%;">
															<div class="col-sm-3 col-md-3 col-lg-3">
																<input type="text" class="form-control input-sm" 
															       id="" name="" value="" >
															</div>
															<div class="col-sm-3 col-md-3 col-lg-3">
																<input type="text" class="form-control input-sm" 
															       id="" name="" value="" >
															</div>
															<div class="col-sm-3 col-md-3 col-lg-3">
																<input type="text" class="form-control input-sm" 
															       id="" name="" value="" >
															</div>
															<div class="col-sm-3 col-md-3 col-lg-3">
																<input type="text" class="form-control input-sm" 
															       id="" name="" value="" >
															</div>
														</div>

														<div class="form-group" style="padding-left: 5%;padding-right: 5%;">
															<div class="col-sm-3 col-md-3 col-lg-3">
																<input type="text" class="form-control input-sm" 
															       id="" name="" value="" >
															</div>
															<div class="col-sm-3 col-md-3 col-lg-3">
																<input type="text" class="form-control input-sm" 
															       id="" name="" value="" >
															</div>
															<div class="col-sm-3 col-md-3 col-lg-3">
																<input type="text" class="form-control input-sm" 
															       id="" name="" value="" >
															</div>
															<div class="col-sm-3 col-md-3 col-lg-3">
																<input type="text" class="form-control input-sm" 
															       id="" name="" value="" >
															</div>
														</div>

														<div class="form-group" style="padding-left: 5%;padding-right: 5%;padding-bottom: 4px;">
															<label class="col-sm-12 col-md-12 col-lg-12 control-label" for=""><strong>Descripcion del contenido de este panel</strong></label>
														</div>

													</div>
													</div>
												</div>
											</div>

											<div class="col-sm-12 col-md-12 col-lg-12">
												<div class="panel panel-default">
													<br>
													<div class="row">
													<div class="col-sm-12 col-md-12 col-lg-12">

														<div class="form-group" style="padding-left: 5%;padding-right: 5%;">
															
															<div class="col-sm-5 col-md-5 col-lg-5">
																<input type="text" class="form-control input-sm" 
															       id="" name="" value="" >
															</div>
															<div class="col-sm-1 col-md-1 col-lg-1"></div>

															<label class="col-sm-1 col-md-1 col-lg-1 control-label" for="">TEORICAS:</label>
															<div class="col-sm-2 col-md-2 col-lg-2">
																<input type="text" class="form-control input-sm" 
															       id="" name="" value="" >
															</div>

															<label class="col-sm-1 col-md-1 col-lg-1 control-label" for="">SALDO:</label>
															<div class="col-sm-2 col-md-2 col-lg-2">
																<input type="text" class="form-control input-sm" 
															       id="" name="" value="" >
															</div>

														</div>
													
													<hr>

														<div class="form-group" style="padding-left: 5%;padding-right: 5%;">
															<label class="col-sm-1 col-md-1 col-lg-1 control-label" for=""><strong>Mov</strong></label>
															<label class="col-sm-1 col-md-1 col-lg-1 control-label" for=""><strong>Hora</strong></label>
															<label class="col-sm-2 col-md-2 col-lg-2 control-label" for=""><strong>Periodo</strong></label>
															<label class="col-sm-1 col-md-1 col-lg-1 control-label" for=""><strong></strong></label>
															<label class="col-sm-1 col-md-1 col-lg-1 control-label" for=""><strong>Cont.</strong></label>
															<label class="col-sm-1 col-md-1 col-lg-1 control-label" for=""><strong>Trab.</strong></label>
															<label class="col-sm-1 col-md-1 col-lg-1 control-label" for=""><strong>Pres.</strong></label>
															<label class="col-sm-1 col-md-1 col-lg-1 control-label" for=""><strong>Ause.</strong></label>
															<label class="col-sm-1 col-md-1 col-lg-1 control-label" for=""><strong>Retr.</strong></label>
															<label class="col-sm-1 col-md-1 col-lg-1 control-label" for=""><strong>Extr.</strong></label>
															<label class="col-sm-1 col-md-1 col-lg-1 control-label" for=""><strong>Abse.</strong></label>
														</div>

														<div class="form-group" style="padding-left: 5%;padding-right: 5%;">
															<label class="col-sm-1 col-md-1 col-lg-1 control-label" for="">E/A</label>
															<label class="col-sm-1 col-md-1 col-lg-1 control-label" for="">8:13</label>
															<label class="col-sm-2 col-md-2 col-lg-2 control-label" for="">Tardanza Ju</label>
															<label class="col-sm-1 col-md-1 col-lg-1 control-label" for=""></label>
															<label class="col-sm-1 col-md-1 col-lg-1 control-label" for="">4:50</label>
															<label class="col-sm-1 col-md-1 col-lg-1 control-label" for="">4:50</label>
															<label class="col-sm-1 col-md-1 col-lg-1 control-label" for="">4:50</label>
															<label class="col-sm-1 col-md-1 col-lg-1 control-label" for="">4:50</label>
															<label class="col-sm-1 col-md-1 col-lg-1 control-label" for="">4:50</label>
															<label class="col-sm-1 col-md-1 col-lg-1 control-label" for="">4:50</label>
															<label class="col-sm-1 col-md-1 col-lg-1 control-label" for="">4:50</label>
														</div>

														<div class="form-group" style="padding-left: 5%;padding-right: 5%;">
															<label class="col-sm-1 col-md-1 col-lg-1 control-label" for="">Horario :</label>
															<label class="col-sm-1 col-md-1 col-lg-1 control-label" for="">Oficina</label>
															<label class="col-sm-2 col-md-2 col-lg-2 control-label" for="">7:00 AM - 16:00 PM</label>
															<label class="col-sm-1 col-md-1 col-lg-1 control-label" for=""></label>
															<label class="col-sm-1 col-md-1 col-lg-1 control-label" for="">4:50</label>
															<label class="col-sm-1 col-md-1 col-lg-1 control-label" for="">4:50</label>
															<label class="col-sm-1 col-md-1 col-lg-1 control-label" for="">4:50</label>
															<label class="col-sm-1 col-md-1 col-lg-1 control-label" for="">4:50</label>
															<label class="col-sm-1 col-md-1 col-lg-1 control-label" for="">4:50</label>
															<label class="col-sm-1 col-md-1 col-lg-1 control-label" for="">4:50</label>
															<label class="col-sm-1 col-md-1 col-lg-1 control-label" for="">4:50</label>
														</div>

													</div>
													</div>
												</div>

											</div>
										</div>
									</div>
								</div>

								<div class="text-center">
									<button type="submit" class="btn btn-sm blue active" id="btn-guardar" name="btn-guardar">Guardar</button>
									<button type="button" class="btn btn-sm default active" onclick="javascript: document.location.href='mantenimiento_datos.php?archivo=<?php echo $archivo_reloj; ?>'">Cancelar</button>
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
<script src="web/js/agregar_movimiento_reloj.js?<?php echo time(); ?>" type="text/javascript"></script>
</body>
</html>