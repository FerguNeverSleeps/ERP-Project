<?php
error_reporting(E_ALL ^ E_DEPRECATED);
require_once "config/db.php";
require_once "libs/importar_archivos.php";
include ("../func_bd.php");

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
		$flash_message = "Â¡Error! Todos los campos son obligatorios";	
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
								Importar archivo de reloj
							</div>
						</div>
						<div class="portlet-body form" id="blockui_portlet_body">
							<form class="form-horizontal" role="form" id="formPrincipal" name="formPrincipal" method="post" enctype="multipart/form-data" style="margin-bottom: 5px;">
								<div class="form-body">
									<div class="form-group">
										<label class="col-md-2 control-label" for="configuracion">Configuraci&oacute;n:</label>
										<div class="col-md-5">
											<?php $res = $conexion->query( "SELECT codigo,descripcion FROM caa_configuracion" ); ?>

											<select class="form-control select2me" name="configuracion" id="configuracion">
												<option value="">Seleccione una configuraciÃ³n</option>
												<?php
													   while( $fila = mysqli_fetch_array($res) )
													{
														echo '<option value="'.$fila['codigo'].'" data-id="'.$fila['formato'].'" >'.$fila['descripcion'] .' (Formato '. $fila['formato'] .')</option>';	
													}
												?>
											</select>
										</div>
									</div>
									<div class="form-group">
										<label for="archivo" class="col-md-2 control-label">Archivo:</label>
										<div class="col-md-9">
											<input type="file" id="archivo" name="archivo" style="outline: none">
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-md-2" for="fecha_inicio">Fecha de inicio:</label>
										<div class="col-md-3">
											<div class="input-group input-medium date date-picker">
												<input type="text" class="form-control" name="fecha_inicio" id="fecha_inicio">
												<span class="input-group-btn">
													<button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
												</span>
											</div>
										</div>
										<div class="col-md-3">
											<div class="input-group input-small timepicker">
												<input name="hora_inicio" id="hora_inicio" type="text" class="form-control timepicker timepicker-no-seconds">
												<span class="input-group-btn">
													<button class="btn default" type="button" style="height: 34px;"><i class="fa fa-clock-o"></i></button>
												</span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label class="control-label col-md-2" for="fecha_fin">Fecha fin:</label>
										<div class="col-md-3">
											<div class="input-group input-medium date date-picker">
												<input type="text" class="form-control" name="fecha_fin" id="fecha_fin">
												<span class="input-group-btn">
													<button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
												</span>
											</div>
										</div>
										<div class="col-md-3">
											<div class="input-group input-small timepicker">
												<input name="hora_fin" id="hora_fin" type="text" class="form-control timepicker timepicker-no-seconds">
												<span class="input-group-btn">
													<button class="btn default" type="button" style="height: 34px;"><i class="fa fa-clock-o"></i></button>
												</span>
											</div>
										</div>
									</div>
									<div>
										<button type="submit" class="btn btn-sm blue active" id="btn-guardar" name="btn-guardar">Cargar</button>
										<button type="button" class="btn btn-sm default active" onclick="javascript: document.location.href='listado_archivos.php'">Cancelar</button>
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
<script src="web/js/agregar_archivo_reloj.js?<?php echo time(); ?>" type="text/javascript"></script>
</body>
</html>