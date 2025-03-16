<?php
require_once "config/db.php";
include ("../func_bd.php");

$id = (isset($_POST['registro_id'])) ? $_POST['registro_id']  : '';

if( isset($_POST['btn-guardar']) )
{
	$tipo_reloj       = ( isset($_POST['tipo_reloj']) )       ?  $_POST['tipo_reloj']        : NULL ; 
	$descripcion      = ( isset($_POST['descripcion']) )      ?  $_POST['descripcion']       : NULL ;
	$formato          = ( isset($_POST['formato']) )          ?  $_POST['formato']           : NULL ; 
	$delimitador      = ( isset($_POST['delimitador']) )      ?  $_POST['delimitador']       : ' ' ;
	$primera_linea    = ( isset($_POST['primera_linea']) )    ?  1  : 0 ; 
	$ignorar_columnas = ( isset($_POST['ignorar_columnas']) ) ?  1  : 0 ; 
	$filas_vacias     = ( isset($_POST['filas_vacias']) )     ?  0  : 1 ; 
	$valor_entrada    = ( isset($_POST['valor_entrada']) && trim($_POST['valor_entrada']) != '' )    ?  $_POST['valor_entrada']  : NULL; 
	$valor_salida     = ( isset($_POST['valor_salida'])  && trim($_POST['valor_salida'])  != ''  )   ?  $_POST['valor_salida']   : NULL; 
	$ubicacion_fecha  = ( isset($_POST['fecha_hora']) )       ?  $_POST['fecha_hora']        : '' ;

	if($ubicacion_fecha=='unica')
		$campos = array('numero', 'tipo_movimiento', 'dispositivo', 'posicion_tiempo');
	if($ubicacion_fecha=='separada')
		$campos = array('numero', 'tipo_movimiento', 'dispositivo', 'posicion_fecha', 'posicion_hora');
	if($ubicacion_fecha=='multiple')
		$campos = array('numero', 'tipo_movimiento', 'dispositivo', 'posicion_dia', 'posicion_mes', 'posicion_anio', 'posicion_hora', 'posicion_minutos');

	if( empty($id) )
	{
		$res = $conexion->query("INSERT INTO `caa_configuracion` (`descripcion`, `formato`, `delimitador`, `primera_linea`, `ignorar_columnas`, `filas_vacias`, `valor_entrada`, `valor_salida`, `tipo_reloj`) VALUES ('$descripcion', '$formato', '$delimitador', '$primera_linea', '$ignorar_columnas', '$filas_vacias', '$valor_entrada', '$valor_salida', '$tipo_reloj')");	
        if($resultado)
        {
	        $id = $conexion->lastInsertId();
	        require_once "utils/agregar_parametros.php";	$msj="Formato de reloj agregado";
        }
	}
	else
	{
		"UPDATE `caa_configuracion` SET `descripcion` = '$descripcion', `formato` = '$formato', `delimitador` = '$delimitador', `primera_linea` = '$primera_linea', `ignorar_columnas` = '$ignorar_columnas', `filas_vacias` = '$filas_vacias', `valor_entrada` = '$valor_entrada', `valor_salida` = '$valor_salida', `tipo_reloj` = '$tipo_reloj' WHERE `codigo` = $id";

 		$conexion->query("DELETE a.* FROM caa_parametros a WHERE a.configuracion =".$registro_id);
		require_once "utils/agregar_parametros.php";	$msj="Formato de reloj actualizado";     		
	}

	activar_pagina("configuracion_reloj.php?msj=".$msj);
}

$primera_linea = $ignorar_columnas = $filas_vacias = 0;
if( !empty($id) )
{
	$res = $conexion->query("SELECT * FROM caa_configuracion WHERE codigo =".$id);
	if($fila = mysqli_fetch_array($res))
	{
		$descripcion   		= $fila['descripcion'];
		$formato       		= $fila['formato'];
		$delimitador   		= $fila['delimitador'];
		$primera_linea 		= $fila['primera_linea'];
		$ignorar_columnas 	= $fila['ignorar_columnas'];
		$filas_vacias     	= $fila['filas_vacias'];
		$valor_entrada 		= $fila['valor_entrada'];
		$valor_salida  		= $fila['valor_salida'];
		$tipo_reloj    		= $fila['tipo_reloj'];

		// Consultar parametros de la configuracion
		$res = $conexion->query("SELECT * FROM caa_parametros WHERE configuracion =".$id);
		/*$res = $conexion->createQueryBuilder()
					    ->select('codigo',  'nombre', 'posicion', 'formato')
					    ->from('caa_parametros', 'cp')
					    ->where('configuracion = ?')
					    ->setParameter(0, $id)
					    ->execute();*/

		while($fila = mysqli_fetch_array($res))
		{
			$parametro = $fila['nombre'];

			if($parametro=='dispositivo')
				${$parametro}[] =  $fila['posicion'];
			elseif($parametro=='tiempo' || $parametro=='fecha' || $parametro=='hora'|| $parametro=='minutos' || $parametro=='dia' || $parametro=='mes' || $parametro=='anio')
			{
				${"posicion_" . $parametro} = $fila['posicion'];
				${"formato_"  . $parametro} = $fila['formato'];
			}
			else
			 	$$parametro = $fila['posicion'];
		}
	}
}
?>
<?php include("vistas/layouts/header.php"); ?>
<link href="web/css/agregar_formato_reloj.css" rel="stylesheet" type="text/css"/>
<body class="page-header-fixed page-full-width" marginheight="0">
<div class="page-container">
	<!-- BEGIN CONTENT -->
	<div class="page-content-wrapper">
		<div class="page-content">
			<!-- BEGIN PAGE CONTENT-->
			<div class="row">
				<div class="col-md-12">
					<!-- BEGIN EXAMPLE TABLE PORTLET-->
					<div class="portlet box blue">
						<div class="portlet-title">
							<div class="caption">
								<?php echo (empty($id)) ? 'Agregar nuevo' : 'Editar'; ?> formato de reloj
							</div>
						</div>
						<div class="portlet-body form">
							<form class="form-horizontal" id="formPrincipal" name="formPrincipal" method="post" role="form" style="margin-bottom: 5px;">
								<div class="form-body">
									<div class="form-group label-validate">
										<label class="col-md-2 control-label" for="tipo_reloj">Tipo de reloj:</label>
										<div class="col-md-5">
											<?php $res = $conexion->query("SELECT codigo,nombre FROM caa_tiporeloj"); ?>
											<select class="form-control input-large select2me" name="tipo_reloj" id="tipo_reloj">
												<option value="">Seleccione un tipo de reloj</option>
												<?php
													while($fila = mysqli_fetch_array($res))
													{
														if($fila['codigo']==$tipo_reloj)
															echo '<option value="'.$fila['codigo'].'" selected>'.$fila['nombre'].'</option>';			
														else
															echo '<option value="'.$fila['codigo'].'">'.$fila['nombre'].'</option>';	
													}
												?>
											</select>
										</div>
									</div>
									<div class="form-group label-validate">
										<label class="col-md-2 control-label" for="descripcion">Descripci&oacute;n:</label>
										<div class="col-md-9">
											<input type="text" class="form-control input-sm" id="descripcion" name="descripcion" value="<?php echo (isset($descripcion)) ? $descripcion : ''; ?>">
										</div>
									</div>
									<div class="form-group label-validate">
										<label class="col-md-2 control-label" for="formato">Formato:</label>
										<div class="col-md-5">
											<?php 	$formatosList = array("csv"=>"CSV", "txt"=>"TXT", "log"=>"LOG");	?>
											<select class="form-control input-large select2me" name="formato" id="formato">
												<option value="">Seleccione un formato</option>
												<?php
													foreach ($formatosList as $clave => $valor) 
													{
														if($formato == $clave)
															echo '<option value="'.$clave.'" selected>'.$valor.'</option>';			
														else
															echo '<option value="'.$clave.'">'.$valor.'</option>';	
													}
												?>
											</select>
										</div>
									</div>
									<hr>
									<h4>Opciones del formato:</h4>
									<hr>
									<div class="form-group label-validate">
										<label class="col-md-2 control-label" for="delimitador"> Columnas separadas por:</label>
										<div class="col-md-2">
											<input type="text" class="form-control input-sm" id="delimitador" name="delimitador" value="<?php echo (isset($delimitador)) ? $delimitador : ''; ?>">
										</div>
									</div>
									<div class="form-group">
										<div class="checkbox-list col-md-12">
											<?php
												$checked1 = ($primera_linea==1)     ? 'checked' : '';
												$checked2 = ($ignorar_columnas==1)  ? 'checked' : '';
												$checked3 = ($filas_vacias==0)      ? 'checked' : '';
											?>
											<label><input type="checkbox" id="primera_linea"    name="primera_linea"    value="Si" <?php echo $checked1; ?> > 
												La primera l&iacute;nea del archivo contiene los nombres de columnas (si no est&aacute; activado la primera l&iacute;nea ser&aacute; parte de los datos) 
											</label>
											<label><input type="checkbox" id="ignorar_columnas" name="ignorar_columnas" value="Si" <?php echo $checked2; ?> > 
												Ignorar columnas que solo contienen espacios en blanco al momento de indicar el orden (posici&oacute;n de las columnas) 
											</label>
											<label><input type="checkbox" id="filas_vacias"     name="filas_vacias"     value="No" <?php echo $checked3; ?> > 
												No importar filas vac&iacute;as 
											</label>
										</div>
									</div>
									<hr>
									<h4>Par&aacute;metros del formato:</h4>
									<hr>
									<div class="form-group">
										<div class="label-validate">
											<label class="col-md-3 control-label" for="numero"> N&uacute;mero del trabajador ubicado en columna:</label>
											<div class="col-md-2">
												<select class="form-control input-xsmall select2me" name="numero" id="numero">
													<option value='' class="text-center">-</option>
													<?php
														for ($i=1; $i < 21 ; $i++) 
														{ 
															if( isset($numero) && $numero == $i )
																echo "<option value='$i' selected>$i</option>";			
															else
																echo "<option value='$i'>$i</option>";	
														}
													?>
												</select>
											</div>
										</div>
										<label class="col-md-5 control-label" for="valor_entrada" style="text-align: right">- Valor que indica una entrada (número o palabra):</label>
										<div class="col-md-2">
											<input type="text" class="form-control input-xsmall" id="valor_entrada" name="valor_entrada" value="<?php echo (isset($valor_entrada)) ? $valor_entrada : ''; ?>">
										</div>									
									</div>
									<div class="form-group">
										<label class="col-md-3 control-label" for="tipo_movimiento"> Tipo de movimiento ubicado en columna:</label>
										<div class="col-md-2">
											<select class="form-control input-xsmall select2me" name="tipo_movimiento" id="tipo_movimiento">
												<option value='' class="text-center">-</option>
												<?php
													for ($i=1; $i < 21 ; $i++) 
													{ 
														if( isset($tipo_movimiento) && $tipo_movimiento == $i )
															echo "<option value='$i' selected>$i</option>";			
														else
															echo "<option value='$i'>$i</option>";	
													}
												?>
											</select>
										</div>
										<label class="col-md-5 control-label" for="valor_salida" style="text-align: right">- Valor que indica una salida (número o palabra):&nbsp;&nbsp;&nbsp;&nbsp;</label>
										<div class="col-md-2">
											<input type="text" class="form-control input-xsmall" id="valor_salida" name="valor_salida" value="<?php echo (isset($valor_salida)) ? $valor_salida : ''; ?>">
										</div>
									</div>
									<div class="form-group" style="margin-bottom: 25px;">
										<label class="col-md-3 control-label" for="dispositivo"> Nombre del dispositivo ubicado en columna:</label>
										<div class="col-md-2">
											<select class="form-control input-xsmall select2" name="dispositivo[]" id="dispositivo" multiple>
												<?php
													for ($i=1; $i < 21 ; $i++) 
													{ 
														if( isset($dispositivo) && in_array($i, $dispositivo) )
															echo "<option value='$i' selected>$i</option>";		
														else
															echo "<option value='$i'>$i</option>";	
													}
												?>
											</select>
										</div>								
									</div>
									<div class="form-group">
										<label class="col-md-12 control-label margen-inferior" for="fecha_hora"> Fecha y hora disponible en:</label>		
										<div class="radio-list col-md-6">
											<?php
												$checked1 = ( empty($id) ||
															  isset($posicion_tiempo) )  ? 'checked' : '';
												$checked2 = ( isset($posicion_fecha) )   ? 'checked' : '';
												$checked3 = ( isset($posicion_dia) )     ? 'checked' : '';
												$display1 = ( empty($id) || 
															  isset($posicion_tiempo) )  ? 'block'   : 'none';
												$display2 = ( isset($posicion_fecha) )   ? 'block'   : 'none';
												$display3 = ( isset($posicion_dia) )     ? 'block'   : 'none';
											?>
											<label><input type="radio" name="fecha_hora" id="fecha_hora1" value="unica"    <?php echo $checked1; ?> > Una única columna con el valor de fecha y hora.</label>
											<label><input type="radio" name="fecha_hora" id="fecha_hora2" value="separada" <?php echo $checked2; ?> > Fecha y hora separadas en columnas diferentes.</label>
											<label><input type="radio" name="fecha_hora" id="fecha_hora3" value="multiple" <?php echo $checked3; ?> > Día, mes, año, hora y minutos separados en columnas diferentes.</label>
										</div>	
										<div id="div_fecha_hora1" class="col-md-6" style="display: <?php echo $display1; ?>"><?php if(!empty($checked1)) require_once("vistas/_template_fecha_unica.php"); ?></div>	
										<div id="div_fecha_hora3" class="col-md-6" style="display: <?php echo $display3; ?>"><?php if(!empty($checked3)) require_once("vistas/_template_fecha_multiple_1.php"); ?></div>		
									</div>
									<div id="div_fecha_hora2" style="display: <?php echo $display2; ?>"><?php if(!empty($checked2)) require_once("vistas/_template_fecha_hora_separadas.php"); ?></div>
									<div id="div_fecha_hora4" style="display: <?php echo $display3; ?>"><?php if(!empty($checked3)) require_once("vistas/_template_fecha_multiple_2.php"); ?></div>

									<div class="form-actions text-center">
										<button type="submit" class="btn btn-sm blue active" id="btn-guardar" name="btn-guardar">Guardar</button>
										<button type="button" class="btn btn-sm default active" onclick="javascript: document.location.href='configuracion_reloj.php'">Cancelar</button>
									</div>									
								</div>
								<input type="hidden" name="registro_id" id="registro_id" value="<?php echo $id; ?>">
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
<script src="web/js/agregar_formato_reloj.js?<?php echo time(); ?>" type="text/javascript"></script>
</body>
</html>