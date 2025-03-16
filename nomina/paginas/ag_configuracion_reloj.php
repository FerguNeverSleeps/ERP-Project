<?php
require_once 'control_asistencia/db.php';
include ("func_bd.php");

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

	
	$campos = array('numero', 'tipo_movimiento', 'dispositivo');

	if($ubicacion_fecha=='unica')
		$campos[] = 'posicion_tiempo';
	if($ubicacion_fecha=='separada')
	{
		$campos[] = 'posicion_fecha';
		$campos[] = 'posicion_hora';
	}
	if($ubicacion_fecha=='multiple')
	{
		$campos[] = 'posicion_dia';
		$campos[] = 'posicion_mes';
		$campos[] = 'posicion_anio';
		$campos[] = 'posicion_hora2';
		$campos[] = 'posicion_minutos';
	}

	//var_dump($_POST);

	if( empty($id) )
	{	
        $resultado = $conexion->insert('caa_configuracion', array('descripcion'   	 => $descripcion, 
				        										  'formato'       	 => $formato, 
				        										  'delimitador'   	 => $delimitador,
				        										  'primera_linea'    => $primera_linea,
				        										  'ignorar_columnas' => $ignorar_columnas,
				        										  'filas_vacias'	 => $filas_vacias,
				        										  'valor_entrada'	 => $valor_entrada,
				        										  'valor_salida'	 => $valor_salida,
				        										  'tipo_reloj'		 => $tipo_reloj
				        										 ));	
        if($resultado)
        {
		        $last_id = $conexion->lastInsertId();

		        foreach ($campos as $campo) 
		        {
				    if(isset($_POST[$campo]) && !empty($_POST[$campo]))
				    {
				    	$parametro = str_replace('posicion_', '', $campo);

				    	$posicion  = $_POST[$campo];
				    	$formato_p = isset($_POST['formato_'.$parametro]) ? $_POST['formato_'.$parametro] : NULL;

				    	$parametro = str_replace('hora2', 'hora', $parametro); 

				    	if($campo=='dispositivo')
				    	{
				    		foreach ($posicion as $valor) {
						    	$conexion->insert('caa_parametros',   array('nombre'   		=> $parametro,
						    												'posicion' 		=> $valor,
						    												'formato'  	    => $formato_p,
						    												'configuracion' => $last_id));
				    		}
				    	}
				    	else
				    	{
					    	$conexion->insert('caa_parametros',   array('nombre'   		=> $parametro,
					    												'posicion' 		=> $posicion,
					    												'formato'  	    => $formato_p,
					    												'configuracion' => $last_id));
				    	}
				    }
				}
        }
	}
	//else
		//$conexion->update('caa_tiporeloj', array('nombre' => $nombre), array('codigo' => $id));

	activar_pagina("configuracion_reloj.php");
}

if( !empty($id) )
{
	$qbr = $conexion->createQueryBuilder()
				    ->select('nombre')
				    ->from('caa_tiporeloj', 'c')
				    ->where('codigo = ?')
				    ->setParameter(0, $id);

	$res = $qbr->execute(); 

	if($fila = $res->fetch())
	{
		$nombre = $fila['nombre'];
	}
}
?>
<?php include("../header4.php"); ?>
<link href="../../includes/assets/css/custom-datatables.css" rel="stylesheet" type="text/css"/>
<style type="text/css">
.portlet > .portlet-body.blue, .portlet.blue {
    background-color: #ffffff !important;
}

.portlet > .portlet-title > .caption {
    font-size: 13px;
    font-weight: bold;
    font-family: helvetica, arial, verdana, sans-serif;
}

.form-horizontal .control-label {
    text-align: left;
    padding-top: 3px;
}

.form-body{
	padding-bottom: 5px;
}

h4{
	font-size: 13px !important;
	font-weight: bold !important;
}

.margen-inferior{
	padding-bottom: 10px
}

label[for=posicion_dia], label[for=posicion_mes], label[for=posicion_anio]
{
    padding-left: 5px;
    padding-right: 0px;
}

.radio-list label{
	padding-bottom: 18px;
}

.radio-list label:last-child{
	padding-bottom: 10px;
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
				<div class="col-md-12">
					<!-- BEGIN EXAMPLE TABLE PORTLET-->
					<div class="portlet box blue">
						<div class="portlet-title">
							<div class="caption">
								<?php echo (empty($id)) ? 'Agregar' : 'Editar'; ?> nuevo formato de reloj
							</div>
						</div>
						<div class="portlet-body form">
							<form class="form-horizontal" id="formPrincipal" name="formPrincipal" method="post" role="form" style="margin-bottom: 5px;">
								<div class="form-body">
									<div class="form-group label-validate">
										<label class="col-md-2 control-label" for="tipo_reloj">Tipo de reloj:</label>
										<div class="col-md-5">
											<?php
													$qbr = $conexion->createQueryBuilder()
																    ->select('codigo', 'nombre')
																    ->from('caa_tiporeloj');

													$res = $qbr->execute(); 																
											?>
											<select class="form-control" name="tipo_reloj" id="tipo_reloj">
												<option value="">Seleccione un tipo de reloj</option>
												<?php
													while($fila = $res->fetch())
													{
														?>
															<option value="<?php echo $fila['codigo']; ?>"><?php echo $fila['nombre']; ?></option>
														<?php
													}
												?>
											</select>
										</div>
									</div>
									<div class="form-group label-validate">
										<label class="col-md-2 control-label" for="descripcion">Descripci&oacute;n:</label>
										<div class="col-md-9">
											<input type="text" class="form-control input-sm" 
										           id="descripcion" name="descripcion" value="<?php echo (isset($descripcion)) ? $descripcion : ''; ?>">
										</div>
									</div>
									<div class="form-group label-validate">
										<label class="col-md-2 control-label" for="formato">Formato:</label>
										<div class="col-md-5">
											<select class="form-control" name="formato" id="formato">
												<option value="">Seleccione un formato</option>
												<option value="csv">CSV</option>
												<option value="txt">TXT</option>
												<option value="log">LOG</option>
												<!--<option value="excel">Excel</option>-->
											</select>
										</div>
									</div>
									<hr>
									<h4>Opciones del formato:</h4>
									<hr>
									<div class="form-group label-validate">
										<label class="col-md-2 control-label" for="delimitador"> Columnas separadas por:</label>
										<div class="col-md-2">
											<input type="text" class="form-control input-sm" 
										           id="delimitador" name="delimitador" value="<?php echo (isset($delimitador)) ? $delimitador : ''; ?>">
										</div>
									</div>
									<div class="form-group">
										<div class="checkbox-list col-md-12">
											<label>
											<input type="checkbox" id="primera_linea" name="primera_linea" value="Si"> La primera línea del archivo contiene los nombres de columnas (si no está activado la primera línea será parte de los datos) </label>
											<label>
											<input type="checkbox" id="ignorar_columnas" name="ignorar_columnas" value="Si"> Ignorar columnas que solo contienen espacios en blanco al momento de indicar el orden (posición de las columnas) </label>
											<label>
											<input type="checkbox" id="filas_vacias" name="filas_vacias" value="No" checked> No importar filas vacías </label>
										</div>
									</div>
									<hr>
									<h4>Par&aacute;metros del formato:</h4>
									<hr>
									<div class="form-group">
										<div class="label-validate">
											<label class="col-md-3 control-label" for="numero"> Número del trabajador ubicado en columna:</label>
											<div class="col-md-2">
												<select class="form-control input-xsmall" name="numero" id="numero">
													<option value='' class="text-center">-</option>
													<?php
														for ($i=1; $i < 21 ; $i++) 
														{ 
															echo "<option value='$i'>$i</option>";
														}
													?>
												</select>
											</div>
										</div>
										<label class="col-md-5 control-label" for="valor_entrada" style="text-align: right">- Valor que indica una entrada (número o palabra):</label>
										<div class="col-md-2">
											<input type="text" class="form-control input-xsmall" 
										           id="valor_entrada" name="valor_entrada" value="<?php echo (isset($valor_entrada)) ? $valor_entrada : ''; ?>">
										</div>									
									</div>
									<div class="form-group">
										<label class="col-md-3 control-label" for="tipo_movimiento"> Tipo de movimiento ubicado en columna:</label>
										<div class="col-md-2">
											<select class="form-control input-xsmall" name="tipo_movimiento" id="tipo_movimiento">
												<option value='' class="text-center">-</option>
												<?php
													for ($i=1; $i < 21 ; $i++) 
													{ 
														echo "<option value='$i'>$i</option>";
													}
												?>
											</select>
										</div>
										<label class="col-md-5 control-label" for="valor_salida" style="text-align: right">- Valor que indica una salida (número o palabra):&nbsp;&nbsp;&nbsp;&nbsp;</label>
										<div class="col-md-2">
											<input type="text" class="form-control input-xsmall" 
									          	   id="valor_salida" name="valor_salida" value="<?php echo (isset($valor_salida)) ? $valor_salida : ''; ?>">
										</div>
									</div>
									<div class="form-group" style="margin-bottom: 25px;">
										<label class="col-md-3 control-label" for="dispositivo"> Nombre del dispositivo ubicado en columna:</label>
										<div class="col-md-2">
											<select class="form-control input-xsmall select2" name="dispositivo[]" id="dispositivo" multiple>
												<!--<option value='' class="text-center">-</option>-->
												<?php
													for ($i=1; $i < 21 ; $i++) 
													{ 
														echo "<option value='$i'>$i</option>";
													}
												?>
											</select>
										</div>								
									</div>
									<div class="form-group">
										<label class="col-md-12 control-label margen-inferior" for="fecha_hora"> Fecha y hora disponible en:</label>		
										<div class="radio-list col-md-6">
											<label>
											<input type="radio" name="fecha_hora" id="fecha_hora1" value="unica" checked> Una única columna con el valor de fecha y hora.</label>
											<label>
											<input type="radio" name="fecha_hora" id="fecha_hora2" value="separada"> Fecha y hora separadas en columnas diferentes.</label>
											<label>
											<input type="radio" name="fecha_hora" id="fecha_hora3" value="multiple"> Día, mes, año, hora y minutos separados en columnas diferentes.</label>
										</div>	
										<div class="col-md-6" id="div_fecha_hora1">
											<div class="row tiempo-validate margen-inferior">
												<label class="col-md-5 control-label" for="posicion_tiempo"> Ubicado en columna:</label>
												<div class="col-md-2">
													<select class="form-control input-xsmall" name="posicion_tiempo" id="posicion_tiempo">
														<option value='' class="text-center">-</option>
														<?php
															for ($i=1; $i < 21 ; $i++) 
															{ 
																echo "<option value='$i'>$i</option>";
															}
														?>
													</select>													
												</div>
											</div>	
											<div class="row tiempo-validate">
												<label class="col-md-5 control-label" for="formato_tiempo"> Formato de fecha y hora:</label>
												<div class="col-md-6">	
													<select class="form-control" name="formato_tiempo" id="formato_tiempo">
														<option value="">Seleccione un formato</option>
														<option value="m/d/Y h:i:s a">MM/dd/yyyy hh:mm:ss a.m.</option>
														<option value="Y-m-d H:i:s">yyyy-MM-dd hh:mm:ss</option>
													</select>											
												</div>
											</div>			
										</div>	
										<div class="col-md-6" id="div_fecha_hora3" style="display: none">
											<div class="row margen-inferior">
												<div class="tiempo-validate">
													<label class="col-md-4 control-label" for="posicion_dia"> Día ubicado en columna:</label>
													<div class="col-md-3">
														<select class="form-control" name="posicion_dia" id="posicion_dia">
															<option value='' class="text-center">-</option>
															<?php
																for ($i=1; $i < 21 ; $i++) 
																{ 
																	echo "<option value='$i'>$i</option>";
																}
															?>
														</select>													
													</div>
												</div>
												<label class="col-md-2 control-label" for="formato_dia"> Formato:</label>
												<div class="col-md-3">	
													<select class="form-control" name="formato_dia" id="formato_dia">
														<option value="d">dd</option>
														<option value="j">d</option>
													</select>											
												</div>											
											</div>	
											<div class="row margen-inferior">
												<div class="tiempo-validate">
													<label class="col-md-4 control-label" for="posicion_mes"> Mes ubicado en columna:</label>
													<div class="col-md-3">
														<select class="form-control" name="posicion_mes" id="posicion_mes">
															<option value='' class="text-center">-</option>
															<?php
																for ($i=1; $i < 21 ; $i++) 
																{ 
																	echo "<option value='$i'>$i</option>";
																}
															?>
														</select>													
													</div>
												</div>
												<label class="col-md-2 control-label" for="formato_mes"> Formato:</label>
												<div class="col-md-3">	
													<select class="form-control" name="formato_mes" id="formato_mes">
														<option value="m">MM</option>
														<option value="n">M</option>
													</select>										
												</div>	
											</div>	
											<div class="row">
												<div class="tiempo-validate">
													<label class="col-md-4 control-label" for="posicion_anio"> Año ubicado en columna:</label>
													<div class="col-md-3">
														<select class="form-control" name="posicion_anio" id="posicion_anio">
															<option value='' class="text-center">-</option>
															<?php
																for ($i=1; $i < 21 ; $i++) 
																{ 
																	echo "<option value='$i'>$i</option>";
																}
															?>
														</select>													
													</div>
												</div>
												<label class="col-md-2 control-label" for="formato_anio"> Formato:</label>
												<div class="col-md-3">	
													<select class="form-control" name="formato_anio" id="formato_anio">
														<option value="Y">yyyy</option>
														<option value="y">yy</option>
													</select>												
												</div>	
											</div>			
										</div>		
									</div>
									<div id="div_fecha_hora2" style="display: none">
										<div class="form-group">
											<div class="tiempo-validate">
												<label class="col-md-2 control-label" for="posicion_fecha" style="padding-left: 40px"> Fecha ubicado en columna:</label>		
												<div class="col-md-2">
														<select class="form-control input-xsmall" name="posicion_fecha" id="posicion_fecha">
															<option value='' class="text-center">-</option>
															<?php
																for ($i=1; $i < 21 ; $i++) 
																{ 
																	echo "<option value='$i'>$i</option>";
																}
															?>
														</select>					
												</div>	
												<label class="col-md-1 control-label" for="formato_fecha"> Formato:</label>
												<div class="col-md-2" style="padding-right: 2px;">
														<select class="form-control" name="formato_fecha" id="formato_fecha">
															<option value="">Seleccione</option>
															<option value='d/m/Y'>dd/MM/yyyy</option>
															<option value='d-m-Y'>dd-MM-yyyy</option>
															<option value='Y/m/d'>yyyy/MM/dd</option>
															<option value='Y-m-d'>yyyy-MM-dd</option>
														</select>					
												</div>
											</div>
											<label class="col-md-3 control-label" for="posicion_indicador" style="text-align: right"> Indicador a.m./p.m. ubicado en columna:</label>
											<div class="col-md-2">		
													<select class="form-control input-xsmall" name="posicion_indicador" id="posicion_indicador">
														<option value='' class="text-center">-</option>
														<?php
															for ($i=1; $i < 21 ; $i++) 
															{ 
																echo "<option value='$i'>$i</option>";
															}
														?>
													</select>				
											</div>
										</div>
										<div class="form-group">
											<div class="tiempo-validate">
												<label class="col-md-2 control-label" for="posicion_hora" style="padding-left: 40px"> Hora ubicado en columna:</label>	
												<div class="col-md-2">
														<select class="form-control input-xsmall" name="posicion_hora" id="posicion_hora">
															<option value='' class="text-center">-</option>
															<?php
																for ($i=1; $i < 21 ; $i++) 
																{ 
																	echo "<option value='$i'>$i</option>";
																}
															?>
														</select>					
												</div>	
												<label class="col-md-1 control-label" for="formato_hora"> Formato:</label>
												<div class="col-md-2" style="padding-right: 2px;">
														<select class="form-control" name="formato_hora" id="formato_hora">
															<option value="">Seleccione</option>
															<option value='H:i'>hh:mm</option>
															<option value='G:i'>h:mm</option>
															<option value="h:i a">hh:mm a.m./p.m.</option>
															<option value="g:i a">h:mm a.m./p.m.</option>
														</select>					
												</div>
											</div>
											<label class="col-md-3 control-label" for="formato_indicador" style="text-align: right"> Formato indicador a.m./p.m.:</label>
											<div class="col-md-2">
													<select class="form-control input-small" name="formato_indicador" id="formato_indicador">
														<option value=""> - </option>
														<option value="a">a.m./p.m.</option>
														<option value="a">am/pm</option>
														<option value="a">A.M./P.M.</option>
														<option value="a">AM/PM</option>
													</select>					
											</div>
										</div>
									</div>
									<div id="div_fecha_hora4" style="display: none">
										<div class="form-group">
											<div class="tiempo-validate">
												<label class="col-md-2 control-label" for="posicion_hora2" style="padding-left: 40px"> Hora ubicado en columna:</label>		
												<div class="col-md-2">
														<select class="form-control input-xsmall" name="posicion_hora2" id="posicion_hora2">
															<option value='' class="text-center">-</option>
															<?php
																for ($i=1; $i < 21 ; $i++) 
																{ 
																	echo "<option value='$i'>$i</option>";
																}
															?>
														</select>					
												</div>
											</div>	
											<label class="col-md-1 control-label" for="formato_hora2"> Formato:</label>
											<div class="col-md-2" style="padding-right: 2px;">
													<select class="form-control" name="formato_hora2" id="formato_hora2">
														<option value='H'>hh</option>
														<option value='G'>h</option>
													</select>					
											</div>											
											<label class="col-md-3 control-label" for="posicion_indicador2" style="text-align: right"> Indicador a.m./p.m. ubicado en columna:</label>
											<div class="col-md-2">		
													<select class="form-control" name="posicion_indicador2" id="posicion_indicador2">
														<option value='' class="text-center">-</option>
														<?php
															for ($i=1; $i < 21 ; $i++) 
															{ 
																echo "<option value='$i'>$i</option>";
															}
														?>
													</select>				
											</div>
										</div>
										<div class="form-group">
											<div class="tiempo-validate">
												<label class="col-md-2 control-label" for="posicion_minutos" style="padding-left: 40px"> Minutos ubicado en columna:</label>	
												<div class="col-md-2">
														<select class="form-control input-xsmall" name="posicion_minutos" id="posicion_minutos">
															<option value='' class="text-center">-</option>
															<?php
																for ($i=1; $i < 21 ; $i++) 
																{ 
																	echo "<option value='$i'>$i</option>";
																}
															?>
														</select>					
												</div>	
											</div>
											<label class="col-md-1 control-label" for="formato_minutos"> Formato:</label>
											<div class="col-md-2" style="padding-right: 2px;">
													<select class="form-control" name="formato_minutos" id="formato_minutos">
														<option value='i'>mm</option>
													</select>					
											</div>
											
											<label class="col-md-3 control-label" for="formato_indicador2" style="text-align: right"> Formato indicador a.m./p.m.:</label>
											<div class="col-md-2">
													<select class="form-control" name="formato_indicador2" id="formato_indicador2">
														<option value=""> - </option>
														<option value="a">a.m./p.m.</option>
														<option value="a">am/pm</option>
														<option value="a">A.M./P.M.</option>
														<option value="a">AM/PM</option>
													</select>					
											</div>
										</div>
									</div>
								<div class="form-actions text-center">
									<button type="submit" class="btn btn-sm blue active" id="btn-guardar" name="btn-guardar">Guardar</button>
									<button type="button" class="btn btn-sm default active" 
									        onclick="javascript: document.location.href='configuracion_reloj.php'">Cancelar</button>
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
<?php include("../footer4.php"); ?>
<script src="../../includes/assets/plugins/jquery-validation/dist/jquery.validate.min.js" type="text/javascript"></script>
<script type="text/javascript">
$(document).ready(function() {

    $('#dispositivo').select2();

	$('#formPrincipal').validate({
            rules: {
                descripcion: { required: true },
                formato:     { required: true },
               // delimitador: { required: true },
                tipo_reloj:  { required: true },
                numero:      { required: true }
            },
            messages: {
                descripcion: { required: " " },
                formato:     { required: " " },
               // delimitador: { required: " " },
                tipo_reloj:  { required: " " },
                numero:      { required: " " }
            },
            highlight: function (element) { 
                $(element)
                    .closest('.label-validate').addClass('has-error'); 
            },
            success: function (label) {
                label.closest('.label-validate').removeClass('has-error');
                label.remove();
            },
    });

    $("input[name=fecha_hora]:radio").change(function () {

    	var valor = this.value;

		$('.tiempo-validate').each(function(i, obj) {
		    $(this).removeClass('has-error'); 
		});

    	$("#div_fecha_hora1, #div_fecha_hora2, #div_fecha_hora3, #div_fecha_hora4").hide();

    	if(valor=='unica')
    		$("#div_fecha_hora1").show();
    	else if(valor=='separada')
    		$("#div_fecha_hora2").show();
    	else if(valor=='multiple')
    		$("#div_fecha_hora3, #div_fecha_hora4").show();
    });

    $("#btn-guardar").click(function(){

		$('.tiempo-validate').each(function(i, obj) {
		    $(this).removeClass('has-error'); 
		});

        var validacion = $('#formPrincipal').valid();

        //if(!validacion) return false;

    	var elementos = document.getElementsByName("fecha_hora");

		for (var i=0 ; i< elementos.length ; i++)
		{
			  if( elementos[i].checked ) 
			  {
			  		if(elementos[i].value == 'unica')
			  		{
			  			var posicion_tiempo = $("#posicion_tiempo").val();
			  			var formato_tiempo  = $("#formato_tiempo").val();

			  			if(posicion_tiempo=='' || formato_tiempo=='')
			  			{
				  			if(posicion_tiempo=='') 
				  				$("#posicion_tiempo").closest('.tiempo-validate').addClass('has-error'); 
				  			if(formato_tiempo=='')
				  				$("#formato_tiempo").closest('.tiempo-validate').addClass('has-error'); 

				  			return false;
			  			}
			  		}
			  		else if(elementos[i].value == 'separada')
			  		{
			  			var posicion_fecha = $("#posicion_fecha").val();
			  			var formato_fecha  = $("#formato_fecha").val();
			  			var posicion_hora  = $("#posicion_hora").val();
			  			var formato_hora   = $("#formato_hora").val();

			  			if(posicion_fecha=='' || formato_fecha=='' || posicion_hora=='' || formato_hora=='')
			  			{
			  				if(posicion_fecha=='' || formato_fecha=='')
			  					$("#posicion_fecha").closest('.tiempo-validate').addClass('has-error'); 
			  				if(posicion_hora=='' || formato_hora=='')
			  					$("#posicion_hora").closest('.tiempo-validate').addClass('has-error'); 

			  				return false;
			  			}
			  		}
			  		else if(elementos[i].value == 'multiple')
			  		{
			  			var posicion_dia     = $("#posicion_dia").val();
			  			var posicion_mes     = $("#posicion_mes").val();
			  			var posicion_anio    = $("#posicion_anio").val();
			  			var posicion_hora    = $("#posicion_hora2").val();
			  			var posicion_minutos = $("#posicion_minutos").val();

			  			if(posicion_dia=='' || posicion_mes=='' || posicion_anio=='' || posicion_hora=='' || posicion_minutos=='')
			  			{
			  				if(posicion_dia=='') 	 $("#posicion_dia").closest('.tiempo-validate').addClass('has-error'); 
			  				if(posicion_mes=='') 	 $("#posicion_mes").closest('.tiempo-validate').addClass('has-error'); 
			  				if(posicion_anio=='') 	 $("#posicion_anio").closest('.tiempo-validate').addClass('has-error'); 
			  				if(posicion_hora=='') 	 $("#posicion_hora2").closest('.tiempo-validate').addClass('has-error'); 
			  				if(posicion_minutos=='') $("#posicion_minutos").closest('.tiempo-validate').addClass('has-error'); 

			  				return false;
			  			}
			  		}
			  }
		}
    });

});
</script>
</body>
</html>