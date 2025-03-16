<?php 
session_start();
ob_start();

require_once '../lib/common.php';
include ("func_bd.php");
//error_reporting(E_ALL ^ E_DEPRECATED);
$conexion=conexion();

// Evitar errores con acentos o caracteres especiales:
mysqli_query($conexion, "SET NAMES 'utf8'");
mysqli_query($conexion, "SET CHARACTER SET utf8 ");

$id = ( isset($_POST['registro_id']) ) ? $_POST['registro_id']  : '';

if( isset($_POST['btn-guardar']) )
{
	$codigo      = ( isset( $_POST['codigo'] ) )      ? $_POST['codigo']      : '';
	$descripcion = ( isset( $_POST['descripcion'] ) ) ? $_POST['descripcion'] : '';
	$frecuencia  = ( isset( $_POST['frecuencia'] ) )  ? $_POST['frecuencia']  : '';
	$inicio      = ( isset( $_POST['inicio'] ) )      ? $_POST['inicio']      : '';
	$tipo_turno  = ( isset( $_POST['tipo'] ) )        ? $_POST['tipo']        : '';

	if( empty($id) )
	{
		$sql = "INSERT INTO nomturnos_rotacion
		        (codigo, descripcion, frecuencia, inicio, turnotipo_id) 
                VALUES 
                ( '{$codigo}', '{$descripcion}', '{$frecuencia}', {$inicio}, {$tipo_turno} )";

        $res = query($sql, $conexion);
	}
	else
	{
		$sql = "UPDATE nomturnos_rotacion
				SET
				codigo='{$codigo}',
				descripcion='{$descripcion}',
				frecuencia='{$frecuencia}',
				inicio={$inicio},
				turnotipo_id={$tipo_turno}
				WHERE codigo={$id}";

		$res = query($sql, $conexion);
	}

	//---------------------------------------------------------------------------------------------

	// Primero borro todos los elementos anteriores
	$sql = "DELETE FROM nomturnos_rotacion_detalle WHERE codigo_rotacion='{$codigo}'";
	$res = query($sql, $conexion);

	$array_turnos = array();

	// Ahora guardamos el detalle de la rotación de los turnos
	foreach ($_POST as $clave => $valor)
	{
		if( strpos($clave, 'turno_actual_') !== false )
		{
			$partes = explode('_', $clave);
			$numero = $partes[2];

			if(!empty($_POST['turno_actual_'.$numero]) && !empty($_POST['turno_sucesor_'.$numero]) &&
			   !(in_array(array($_POST['turno_actual_'.$numero], $_POST['turno_sucesor_'.$numero]), $array_turnos)) )
			{
				$sql = "INSERT INTO `nomturnos_rotacion_detalle` 
				        (`codigo_rotacion`, `turno_actual`, `turno_sucesor`) 
				        VALUES 
				        ('{$codigo}', " . $_POST['turno_actual_'.$numero] . " , " . $_POST['turno_sucesor_'.$numero] . ")";

				$res = query($sql, $conexion);

				$array_turnos[] = array($_POST['turno_actual_'.$numero], $_POST['turno_sucesor_'.$numero]);
			}
		}
	}
	//---------------------------------------------------------------------------------------------

	activar_pagina("horarios_rotativos.php");
}

$descripcion=$tipo_id='';

if( isset($_POST['registro_id']) )
{
	$sql = "SELECT codigo, descripcion, frecuencia, inicio, turnotipo_id
			FROM   nomturnos_rotacion
			WHERE  codigo='{$id}'";
	$res = query($sql, $conexion);

	if( $fila=fetch_array($res)  )
	{
		$codigo      = $fila['codigo'];
		$descripcion = $fila['descripcion'];
		$frecuencia  = $fila['frecuencia'];
		$inicio      = $fila['inicio'];
		$tipo_id     = $fila['turnotipo_id'];
	}
}
else
{
	$sql = "SELECT COALESCE(MAX(codigo),0) + 1 as codigo FROM nomturnos_rotacion";

	$res = query($sql, $conexion);

	if( $fila=fetch_array($res) )
	{
		$codigo = $fila['codigo'];
	}
}
?>
<?php include("../header4.php"); // <html><head></head><body> ?>
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
}

#tbody_turnos tr td {
	text-align: center;
	vertical-align: middle;
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
						<div class="portlet-title" style="padding-top: 5px;">
							<div class="caption" style="width: 100%; text-align: center">
								Configuraci&oacute;n Horarios Rotativos
							</div>
						</div>
						<div class="portlet-body form">
							<form class="form-horizontal" id="formPrincipal" name="formPrincipal" method="post" role="form" style="margin-bottom: 5px;">
								<div class="form-body">

									<div class="form-group">
											<div class="form-group2">
												<label class="col-md-1 control-label" for="codigo"
												       style="text-align: center; padding-right: 0px">C&oacute;digo:</label>
												<div class="col-md-2">
													<input type="text" class="form-control input-sm"
												           id="codigo" name="codigo" value="<?php echo $codigo; ?>">
												</div>
											</div>
											<div class="form-group2">
												<label class="col-md-1 control-label" for="descripcion"
												       style="text-align: center; padding-right: 0px">Descripci&oacute;n:</label>
												<div class="col-md-7">
													<input type="text" class="form-control input-sm"
												           id="descripcion" name="descripcion" value="<?php echo $descripcion; ?>">
												</div>
											</div>
									</div>

									<div class="form-group">

											<label class="col-md-1 control-label" for="frecuencia"
											       style="text-align: right; padding-right: 5px">Frecuencia:</label>
											<div class="col-md-2">
												<?php 
													$sql  = "SHOW COLUMNS FROM nomturnos_rotacion LIKE 'frecuencia'";
													$res  = query($sql, $conexion);
													$fila = fetch_array($res);

													$cadenavalor = $fila[1]; 
													$cadenavalor = str_replace("enum", "", $cadenavalor); 
													$cadenavalor = str_replace("(", "", $cadenavalor); 
													$cadenavalor = str_replace(")", "", $cadenavalor); 
													$cadenavalor = str_replace("'", "", $cadenavalor); 
													$frecuencias = explode(",", $cadenavalor); // Arreglo de frecuencias
												?>
												<select name="frecuencia" id="frecuencia" class="select2 form-control">
													<option value="">Seleccione</option>
													<?php
														foreach ($frecuencias as $valor) 
														{
																if($frecuencia == $valor)
																{
																	?>
																		<option value="<?php echo $valor; ?>" selected><?php echo $valor; ?></option>
																	<?php
																}
																else
																{
																	?>
																		<option value="<?php echo $valor; ?>"><?php echo $valor; ?></option>
																	<?php
																}
														}
													?>
												</select>
											</div>

											<label class="col-md-1 control-label" for="inicio"
											       style="text-align: center; padding-right: 0px">Inicio:</label>
											<div class="col-md-3">
												<select name="inicio" id="inicio" class="select2 form-control">
													<option value="">Seleccione</option>
													<?php 

														if($frecuencia=='Semanal')
														{
															$days_week = array('1'=>'Lunes',
																			   '2'=>'Martes',
																			   '3'=>'Miércoles',
																			   '4'=>'Jueves',
																			   '5'=>'Viernes',
																			   '6'=>'Sábado',
																			   '7'=>'Domingo');

															foreach ($days_week as $clave => $valor) 
															{
																if($inicio==$clave)
																{
																	echo "<option value='".$clave."' selected>".$valor."</option>";
																}
																else
																{
																	echo "<option value='".$clave."'>".$valor."</option>";
																}
															}
														}
														else if($frecuencia=='Diaria')
														{
															for($i=1; $i<=31; $i++)
															{
																if($inicio == $i)
																{
																	echo "<option value='".$i."' selected>$i</option>";
																}
																else
																{
																	echo "<option value='".$i."'>$i</option>";
																}																
															}
														}
														else if($frecuencia=='Mensual')
														{
																$meses = array('1' =>'Enero',    '2' =>'Febrero',   '3' =>'Marzo',
																			   '4' =>'Abril',    '5' =>'Mayo',      '6' =>'Junio',
																			   '7' =>'Julio',    '8' =>'Agosto',    '9' =>'Septiembre',
																			   '10'=>'Octubre',  '11'=>'Noviembre', '12'=>'Diciembre');

																foreach ($meses as $clave => $valor) 
																{
																	if($inicio == $clave)
																	{
																		echo "<option value='".$clave."' selected>$valor</option>";
																	}
																	else
																	{
																		echo "<option value='".$clave."'>$valor</option>";
																	}																	
																}
														}
													?>
												</select>
											</div>
										
											<label class="col-md-1 control-label" for="tipo"
											       style="text-align: center; padding-right: 0px">Tipo:</label>
											<div class="col-md-3">
												<?php 
													$sql = "SELECT turnotipo_id, descripcion FROM nomturnos_tipo WHERE rotativo=1";
													$res = query($sql, $conexion);
												?>
												<select name="tipo" id="tipo" class="select2 form-control">
													<option value="">Seleccione un tipo de turno</option>
													<?php
													while( $fila = fetch_array($res) )
													{ 
														if( $tipo_id == $fila['turnotipo_id'] )
														{ ?>
															<option value="<?php echo $fila['turnotipo_id']; ?>" selected><?php echo $fila['descripcion']; ?></option>
														  <?php
														}
														else
														{ ?>
															<option value="<?php echo $fila['turnotipo_id']; ?>"><?php echo $fila['descripcion']; ?></option>
														  <?php
														}
													}
													?>
												</select>
											</div>
									</div>

									<div class="row" style="margin-top: 30px">
										<div class="col-md-12">
												<div class="table-responsive">
													<table id="table_turnos" class="table table-bordered table-striped" align="center" style="width: 83%">
														<thead>
															<tr>
																<th style="text-align: center; width: 45%">Turno Actual</th>
																<th style="text-align: center; width: 45%">Turno Sucesor</th>
																<th>&nbsp;</th>
															</tr>
														</thead>
														<tbody id="tbody_turnos">
															<?php
																$sql = "SELECT turno_actual, turno_sucesor
																		FROM   nomturnos_rotacion_detalle WHERE codigo_rotacion='{$codigo}'";
																$res = query($sql, $conexion);

																$sql2 = "SELECT turno_id, descripcion FROM nomturnos n WHERE n.tipo='{$tipo_id}'";
																$res2 = query($sql2, $conexion);
																$cont_turnos=num_rows($res2);

																$i=1; 
																while( ($fila = fetch_array($res)) || ($i<=$cont_turnos))
																{ 
																	$turno_actual  = (isset($fila['turno_actual']))  ? $fila['turno_actual'] : '';
																	$turno_sucesor = (isset($fila['turno_sucesor'])) ? $fila['turno_sucesor'] : '';
																?>
																	<tr id="tr_turno_<?php echo $i; ?>">
																		<td>
																			<select name="turno_actual_<?php echo $i; ?>" id="turno_actual_<?php echo $i; ?>" class="select2 form-control input-large"
																			        style="margin: 0px auto;" >
																				<option value="">Seleccione un turno</option>
																				<?php 
																					$res2 = query($sql2, $conexion);
																					while( $fila2 = fetch_array($res2) )
																					{
																						( $fila2['turno_id'] == $turno_actual ) ? $selected='selected' : $selected='';
																					?>
																						<option value="<?php echo $fila2['turno_id'] ?>" <?php echo $selected; ?> ><?php echo $fila2['descripcion']; ?></option>
																					<?php
																					}
																				?>
																			</select>
																		</td>
																		<td>
																			<select name="turno_sucesor_<?php echo $i; ?>" id="turno_sucesor_<?php echo $i; ?>" class="select2 form-control input-large"
																			        style="margin: 0px auto;" >
																				<option value="">Seleccione un turno</option>
																				<?php 
																					$res2 = query($sql2, $conexion);
																					while( $fila2 = fetch_array($res2) )
																					{
																						( $fila2['turno_id'] == $turno_sucesor ) ? $selected='selected' : $selected='';
																					?>
																						<option value="<?php echo $fila2['turno_id'] ?>" <?php echo $selected; ?> ><?php echo $fila2['descripcion']; ?></option>
																					<?php
																					}
																				?>
																			</select>
																		</td>
																		<td>
																			<a id="limpiar_<?php echo $i; ?>" style="cursor: pointer" title="Reiniciar"  onclick="reiniciarLista(<?php echo $i; ?>)"><img src="../../includes/imagenes/icons/delete.png" alt="Reiniciar" width="16" height="16"></a>
																		</td>
																	</tr>
																<?php	
																	$i++;																
																}

																if($i==1)
																{
																?>
																	<tr>
																		<td colspan="3" style="text-align: center">Por favor, seleccione un tipo de turno
																		</td>
																	</tr>
																<?php
																}
															?>
														</tbody>
													</table>
												</div>		
										</div>
									</div>

									<div style="text-align: center; margin-top: 10px">
										<button type="submit" class="btn btn-sm blue active" id="btn-guardar" name="btn-guardar">Guardar</button>&nbsp;
										<button type="button" class="btn btn-sm default active" 
										        onclick="javascript: document.location.href='horarios_rotativos.php'">Cancelar</button>
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

		jQuery.validator.addMethod("mynumber", function (value, element) {
		    return this.optional(element) || /^\d+$/.test(value);
		}, "Ingresar un número válido");


		$('#formPrincipal').validate({
	            rules: {
	                codigo: {
	                    required: true,
	                    mynumber: true
	                },
	                descripcion: { required: true },
	                frecuencia: { required: true},
	                inicio: { required: true},
	                tipo: { required: true}
	            },

	            messages: {
	            	codigo: { required: " " }, 
	                descripcion: { required: " " },
	                frecuencia: { required: " "},
	                inicio: { required: " "},
	                tipo: { required: " "}
	            },

	            highlight: function (element) { // hightlight error inputs
	                $(element)
	                    .closest('.form-group2').addClass('has-error'); // set error class to the control group
	            },

	            success: function (label) {
	                label.closest('.form-group2').removeClass('has-error');
	                label.remove();
	            }
	    });

	$("#tipo").select2({
	 	placeholder: 'Seleccione un tipo de turno',
        allowClear: true,
    });

	$("#frecuencia").select2({
	 	placeholder: 'Seleccione',
        allowClear: true,
    });

	$("#inicio").select2({
	 	placeholder: 'Seleccione',
        allowClear: true,
    });

	$("#frecuencia").change(function() {
		var frecuencia = $(this).val();

		//$('#inicio option:eq(0)');
		$("#inicio").select2("val", ""); 

        $.ajax({
            method: "POST",
            url:    "../libs/php/ajax/ajax_horario_rotativo.php",
            data: { frecuencia: frecuencia }
        })
		.done(function(data) {
		     $("#inicio").html(data);
		});
	});

	$("#tipo").change(function() {
		var tipo_turno = $(this).val();

		if(tipo_turno == '')
		{
			$("#table_turnos > tbody").empty(); // Eliminar filas del tbody

			var tbody    = document.getElementById("tbody_turnos"); 
			var rowCount = tbody.rows.length;
            var row      = tbody.insertRow(0);

			var celda1   = row.insertCell(0);
			celda1.colSpan = 3;
			celda1.innerHTML = 'Por favor, seleccione un tipo de turno';
		}
		else
		{
			// Limpiar todos los combobox de la tabla detalle de turnos
			$.ajax({
				method: "POST",
				url:    "../libs/php/ajax/ajax_consultar_turnos.php",
				data:   { tipo_turno: tipo_turno }
			})
			.done(function(result){

				var result = result.split("/SCRIPT>");
				result = result[1];

				var data   = result.split("&&&&&");

				//console.log("data[0] => " + data[0]);
				//console.log("data[1] => " + data[1]);
				var table    = document.getElementById("table_turnos");
				var tbody    = document.getElementById("tbody_turnos"); 
				var rowCount = tbody.rows.length;

				for(i=0; i<rowCount; i++) 
				{
					tbody.deleteRow(i);
					rowCount--;   
					i--;               
	            }

	      
				for (i = 1; i <= data[1]; i++) 
				{ 
						var rowCount = tbody.rows.length;
			            var row      = tbody.insertRow(rowCount);
			            row.id = "tr_turno_"+i;

			            var celda1   = row.insertCell(0);
			            var celda2   = row.insertCell(1);
			            var celda3   = row.insertCell(2);

			          	var selectActual = document.createElement("select");
						selectActual.id  = "turno_actual_" + i;
						//selectActual.setAttribute('id',   'turno_actual_'+i);
						selectActual.className = "select2 form-control input-large";
						selectActual.setAttribute('name', 'turno_actual_' + i);
						selectActual.style.cssText = 'margin: 0px auto;';

						celda1.appendChild(selectActual);

	/*
						celda1.innerHTML = '<select name="turno_actual_' + i + '" id="turno_actual_' + i + '" '+ 
						                           'class="select2 form-control input-large" '+
												   'style="margin: 0px auto;" ><option value="">Seleccione un turno</option></select>';
	*/

						$("#turno_actual_" + i).html(data[0]);

						var selectSucesor = document.createElement("select");
						selectSucesor.id  = "turno_sucesor_" + i;
						selectSucesor.className = "select2 form-control input-large";
						selectSucesor.setAttribute('name', 'turno_sucesor_' + i);
						selectSucesor.style.cssText = 'margin: 0px auto;';

						celda2.appendChild(selectSucesor);
						$("#turno_sucesor_" + i).html(data[0]);

			             celda3.innerHTML = '<a id="limpiar_'+i+'" style="cursor: pointer" title="Reiniciar"><img src="../../includes/imagenes/icons/delete.png" alt="Reiniciar" width="16" height="16"></a>' ;
						// celda3.innerHTML = '<a id="limpiar_'+i+'" style="cursor: pointer" title="Reiniciar"><span class="glyphicon glyphicon-trash"></span></a>' ;

						var newLimpiar = document.getElementById("limpiar_" + i);
						newLimpiar.onclick = function () {
						   var id    = this.id;
						   var parte = id.split("_");
						   var i = parte[1];

						   document.getElementById("turno_actual_"  + i).selectedIndex = 0;
						   document.getElementById("turno_sucesor_" + i).selectedIndex = 0;
						};				

				}
				
			});

		}

	});
});

function reiniciarLista(i)
{
   document.getElementById("turno_actual_"  + i).selectedIndex = 0;
   document.getElementById("turno_sucesor_" + i).selectedIndex = 0;
}

$("#btn-guardar").click(function() {

	var frecuencia = $("#frecuencia").val();
	var inicio     = $("#inicio").val();
	var tipo_turno = $("#tipo").val();

	if(frecuencia == '')
	{
		$('#frecuencia').addClass('has-error');
		alert("Debe seleccionar la frecuencia de rotacion de los turnos");
		return false;
	}

	if(inicio == '')
	{
		alert("Debe seleccionar el inicio de la rotacion");
		return false;
	}

	if(tipo_turno == '')
	{
		alert("Debe seleccionar el tipo de turno de la rotacion");
		return false;
	}

	var cont_turnos = 0;
	var combinacion = new Array();  //[];
	var turnos_act  = new Array();
	// var turnos_suc  = new Array(); 

	validar = true;

	// Comprobar que al menos se indico una rotacion
	$("#tbody_turnos tr").each(function(){
		var id = this.id;
		var parte = id.split("_");
		var i = parte[2];
		// console.log("id => " + this.id);
		var turno_actual  = $("#turno_actual_"  + i).val();
		var turno_sucesor = $("#turno_sucesor_" + i).val();

		if(turno_actual !== '' && turno_sucesor !== '')
		{
			if(inArray([turno_actual, turno_sucesor], combinacion))
			{
				validar = false;
				return false;
			}
			else
			{
				combinacion[cont_turnos] = [turno_actual, turno_sucesor];
				turnos_act[cont_turnos]  = turno_actual;
				//turnos_suc[cont_turnos]  = turno_sucesor;
				cont_turnos++;
			}		
		}
	});

	//------------------------------------------------------------------------------------
	// Verificar si existen turnos actuales con relaciones uno a muchos
	/*
	for (var i = 0; i< turnos_act.length-1; i++)
	{
		for (var j =i+1; j<turnos_act.length; j++)
		{
			if (turnos_act[i] == turnos_act[j])
			{
				alert("¡Existen turnos actuales relacionados simultaneamente a varios turnos sucesores! Por favor, verifique");
				return false;
			}		
		}
	}*/

	var sorted_arr = turnos_act.sort(); 
	                            
	for (var i = 0; i < turnos_act.length - 1; i++) 
	{
	    if (sorted_arr[i + 1] == sorted_arr[i]) 
	    {
			alert("¡Existen turnos actuales relacionados simultaneamente a varios turnos sucesores! "+
				  // "Por ejemplo, el turno " + sorted_arr[i] +
				  "Por favor, verifique");
			return false;
	    }
	}

	//------------------------------------------------------------------------------------

	if(cont_turnos < 1)
	{
		alert("Debe indicar al menos una rotacion de los turnos");

		return false;
	}

	// Validar combinaciones de los turnos
	if(!validar)
	{
		alert("¡Existen combinaciones de turnos repetidas! Por favor, verifique");
		return false;		
	}
});

//====================================================================
// Equivalente a la función in_array de PHP
function arrayCompare(a1, a2) 
{
    if (a1.length != a2.length) return false;
    var length = a2.length;
    for (var i = 0; i < length; i++) 
    {
        if (a1[i] !== a2[i]) return false;
    }
    return true;
}

function inArray(needle, haystack) 
{
    var length = haystack.length;
    for(var i = 0; i < length; i++) 
    {
        if(typeof haystack[i] == 'object') 
        {
            if(arrayCompare(haystack[i], needle)) return true;
        } 
        else 
        {
            if(haystack[i] == needle) return true;
        }
    }
    return false;
}
//====================================================================
</script>
</script>
</body>
</html>