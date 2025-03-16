<?php
error_reporting(E_ALL ^ E_DEPRECATED);
require_once "config/db.php";
include ("../func_bd.php");

$archivo_reloj=(isset($_GET['archivo'])) ? $_GET['archivo'] : '' ;
$id = (isset($_POST['registro_id'])) ? $_POST['registro_id'] : '';

if( isset($_POST['btn-guardar']) )
{
	$ficha           = (isset($_POST['ficha']))           ? $_POST['ficha']           : '';
	$tipo_movimiento = (isset($_POST['tipo_movimiento'])) ? $_POST['tipo_movimiento'] : NULL;
	$dispositivo     = (isset($_POST['dispositivo']))     ? $_POST['dispositivo']     : NULL;
	$fecha           = (isset($_POST['fecha']))           ? $_POST['fecha']           : '';	
	$hora            = (isset($_POST['hora']))            ? $_POST['hora']            : '';

	// Conversión de la fecha
	$fecha = DateTime::createFromFormat('d-m-Y', $fecha);
	$fecha = $fecha->format('Y-m-d');

	// Conversión de la hora
	$hora = DateTime::createFromFormat('h:i:s a', $hora);
	$hora = $hora->format('H:i:s');

	// Validar que la fecha y hora se encuentre dentro del rango permitido por el archivo de reloj

	$sql = "SELECT fecha_inicio, fecha_fin, 
       			   (SELECT COUNT(*) FROM caa_archivos_reloj ca 
		            WHERE ca.codigo=c.codigo 
		            AND   '{$fecha} {$hora}' BETWEEN ca.fecha_inicio AND ca.fecha_fin) as rango 
			FROM   caa_archivos_reloj c 
			WHERE  c.codigo={$archivo_reloj}";

	$res = $conexion->query($sql);

	if($fila = $res->fetch())
	{
		$rango     = $fila['rango'];
		$fecha_ini = date('d-m-Y h:i a', strtotime($fila['fecha_inicio']));
		$fecha_fin = date('d-m-Y h:i a', strtotime($fila['fecha_fin']));

		if($rango==1)
		{
			try {

				if(empty($id))
				{
					// Registramos el nuevo movimiento
					$conexion->insert('caa_archivos_datos', array('ficha'           => $ficha, 
																  'fecha_hora'      => $fecha .' '.$hora, 
																  'tipo_movimiento' => $tipo_movimiento, 
																  'dispositivo'     => $dispositivo,
																  'archivo_reloj'   => $archivo_reloj
																 ));
					$msj = "Registro agregado exitosamente";
				}
				else
				{
					// Actualizamos el registro
					$conexion->update('caa_archivos_datos', array('ficha'           => $ficha, 
																  'fecha_hora'      => $fecha .' '.$hora, 
																  'tipo_movimiento' => $tipo_movimiento, 
																  'dispositivo'     => $dispositivo,
																  'archivo_reloj'   => $archivo_reloj
																 ), 
															array('codigo' => $id));
					$msj = "Registro actualizado";
				}	

				activar_pagina("mantenimiento_datos.php?archivo=".$archivo_reloj."&msj=".$msj);
			} 
			catch (Exception $e) {
			  $flash_message = "<strong>¡Ha ocurrido un error!</strong><p>" . $e->getMessage() ."</p>";
			  $flash_class   = 'alert-danger';
			}
		}
		else
		{
		  	$flash_message = "<p>La fecha seleccionada se encuentra fuera del rango establecido para este archivo de reloj (Fecha de inicio: ".$fecha_ini." / Fecha fin: ".$fecha_fin.").</p>";
		  	$flash_class   = 'alert-danger';		
		}
	}
}

if(!empty($id))
{
	$res = $conexion->createQueryBuilder()
				    ->select('ficha', 'fecha_hora', 'tipo_movimiento', 'dispositivo')
				    ->from('caa_archivos_datos')
				    ->where('codigo = :codigo')
				    ->setParameter('codigo', $id)
				    ->execute(); 

	if($fila = $res->fetch())
	{
		$ficha           = $fila['ficha'];
		$tipo_movimiento = $fila['tipo_movimiento'];
		$dispositivo     = $fila['dispositivo'];
		$fecha 			 = date('d-m-Y', strtotime($fila['fecha_hora']));
		$hora  			 = date('h:i:s a', strtotime($fila['fecha_hora']));
	}	
}
?>
<?php include("vistas/layouts/header.php"); ?>
<link href="web/css/agregar_movimiento_reloj.css?<?php echo time(); ?>" rel="stylesheet" type="text/css"/>
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
								<?php echo (empty($id)) ? 'Agregar registro al archivo de reloj' : 'Editar registro del archivo de reloj'; ?>
							</div>
						</div>
						<div class="portlet-body form" id="blockui_portlet_body">
							<form class="form-horizontal" role="form" id="formPrincipal" name="formPrincipal" method="post" style="margin-bottom: 5px;">
								<div class="form-body">
									<div class="form-group">
										<div class="form-group2">
											<label class="col-md-1 control-label" for="ficha">Ficha:</label>
											<div class="col-md-4">
												<?php $res = $conexion->createQueryBuilder()->select('ficha', 'apenom', 'nombres', 'apellidos')
																							->from('nompersonal')
																							->where('tipnom = ?')
																							->setParameter(0, $_SESSION['codigo_nomina'])
																							->execute(); 
												?>
												<select class="form-control input-medium select2me" name="ficha" id="ficha">
													<option value="">Seleccione una ficha</option>
													<?php
														while($fila = $res->fetch())
														{
															if($fila['ficha'] == $ficha)
																echo '<option value="'.$fila['ficha'].'" selected>'.$fila['ficha'] .' - '. $fila['apenom'] .'</option>';	
															else
																echo '<option value="'.$fila['ficha'].'">'.$fila['ficha'] .' - '. $fila['apenom'] .'</option>';	
														}
													?>
												</select>
											</div>
										</div>
										<div class="form-group2">
											<label class="col-md-2 control-label" for="tipo_movimiento" style="text-align: right">Tipo de movimiento:</label>
											<div class="col-md-4">
												<?php $tiposList = array("Entrada", "Salida"); ?>
												<select class="form-control input-medium select2me" name="tipo_movimiento" id="tipo_movimiento">
													<option value="">Seleccione un tipo</option>
													<?php
														foreach ($tiposList as $tipo) 
														{
															if($tipo==$tipo_movimiento)
																echo "<option value='$tipo' selected>$tipo</option>";
															else
																echo "<option value='$tipo'>$tipo</option>";
														}
													?>
												</select>
											</div>
										</div>
									</div>
									<div class="form-group">
										<div class="form-group2">
											<label class="control-label col-md-1" for="fecha">Fecha:</label>
											<div class="col-md-4">
												<div class="input-group input-medium date date-picker">
													<input name="fecha" id="fecha" class="form-control" size="10" type="text" value="<?php echo isset($fecha) ? $fecha : ''; ?>" />
													<span class="input-group-btn">
														<button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
													</span>
												</div>
											</div>
										</div>
										<label class="col-md-2 control-label" for="dispositivo" style="text-align: right">Dispositivo:</label>
										<div class="col-md-4">
											<input name="dispositivo" id="dispositivo" class="form-control input-medium" size="60" type="text" value="<?php echo isset($dispositivo) ? $dispositivo : ''; ?>"/>
										</div>
									</div>
									<div class="form-group form-group2">
										<label class="control-label col-md-1" for="hora">Hora:</label>
										<div class="col-md-3">
											<div class="input-group input-medium timepicker">
												<input name="hora" id="hora" type="text" class="form-control timepicker timepicker-no-seconds" value="<?php echo isset($hora) ? $hora: ''; ?>">
												<span class="input-group-btn">
													<button class="btn default" type="button" style="height: 34px;"><i class="fa fa-clock-o"></i></button>
												</span>
											</div>
										</div>
									</div>
									<div class="text-center">
										<button type="submit" class="btn btn-sm blue active" id="btn-guardar" name="btn-guardar">Guardar</button>
										<button type="button" class="btn btn-sm default active" onclick="javascript: document.location.href='mantenimiento_datos.php?archivo=<?php echo $archivo_reloj; ?>'">Cancelar</button>
									</div>									
								</div>
								<input type="hidden" name="registro_id" value="<?php echo $id; ?>">
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