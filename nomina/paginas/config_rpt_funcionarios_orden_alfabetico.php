<?php 
require_once('../lib/database.php');

$db = new Database($_SESSION['bd']);	
?>
<?php include("../header4.php"); ?>
<link rel="stylesheet" type="text/css" href="../../includes/assets/plugins/jquery-multi-select/css/multi-select.css"/>
<style>
.form-horizontal .control-label {
    text-align: center;
    padding-top: 7px;
}
.portlet-title{
	padding-right: 5px !important;
}

label.error {
    color: #b94a48;
}

.margin-top-20{
	margin-top: 20px
}

.text-left
{
	text-align: left !important;
}

.ms-container {
    width: 100%;
}

.ms-container .ms-list {
    height: 200px;
}

.ms-container .ms-selectable li.ms-elem-selectable, .ms-container .ms-selection li.ms-elem-selection {
    cursor: pointer;
}
</style>
<div class="page-container">
	<div class="page-content-wrapper">
		<div class="page-content">
			<!-- BEGIN PAGE CONTENT-->
			<div class="row">
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
					<!-- BEGIN EXAMPLE TABLE PORTLET-->
					<div class="portlet box blue">
						<div class="portlet-title">
							<div class="caption">
								INFORME DE FUNCIONARIOS POR ORDEN ALFABÉTICO
							</div>
							<div class="actions">
								<a class="btn btn-sm blue"  onclick="javascript: window.location='submenu_reportes.php?modulo=45';">
									<i class="fa fa-arrow-left"></i> Regresar
								</a>				
							</div>
						</div>
						<div class="portlet-body form" id="blockui_portlet_body">

							<form class="form-horizontal" id="form1" name="form1" method="post">
								<div class="form-body">
									<div class="row">
										<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">POSICIÓN</div>
										<div class="col-xs-9 col-sm-9 col-md-9 col-lg-9">
											<input type="text" name="posicion" id="posicion" class="form-control" placeholder="Buscar por Posición...">
										</div>											
									</div>
									<div class="row">									
										<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">PRIMER NOMBRE</div>
										<div class="col-xs-9 col-sm-9 col-md-9 col-lg-9">
											<input type="text" name="nombre" id="nombre" class="form-control" placeholder="Buscar por nombre...">
										</div>
											
									</div>
									<div class="row">
										<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">APELLIDO PATERNO</div>
										<div class="col-xs-9 col-sm-9 col-md-9 col-lg-9">
											<input type="text" name="apellido" id="apellido" class="form-control" placeholder="Buscar por apellido">
										</div>
											
									</div>
									<div class="row">
										<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">CARGO</div>
										<div class="col-xs-9 col-sm-9 col-md-9 col-lg-9">
										<select name="cargo" id="cargo" class="form-control">
											<option value="">Seleccione el cargo...</option>

											<?php 
												$sentencia = "SELECT * from nomcargos";
												$resultado = $db->query($sentencia);
												while ($filas = $resultado->fetch_object()) 
												{
													echo "<option value='".$filas->cod_car."'>".$filas->des_car."</option>";
												}

												?>

											<?php ?>
										</select>
										</div>
									</div>
									<div class="row">
										<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">FUNCION</div>
										<div class="col-xs-9 col-sm-9 col-md-9 col-lg-9">
										<select name="funcion" id="funcion" class="form-control">
											<option value="">Seleccione una función...</option>
											<?php 
												$sentencia = "SELECT * from nomfuncion";
												$resultado = $db->query($sentencia);
												while ($filas = $resultado->fetch_object()) 
												{
													echo "<option value='".$filas->nomfuncion_id."'>".$filas->descripcion_funcion."</option>";
												}

												?>

											<?php ?>
										</select>
										</div>
									</div>
									<div class="row">
										<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">GENERO</div>
										<div class="col-xs-9 col-sm-9 col-md-9 col-lg-9">
										<select name="genero" id="genero" class="form-control">
											<option value="">Seleccione el género...</option>
											<option value="Masculino">Masculino</option>
											<option value="Femenino">Femenino</option>
										</select>
										</div>
									</div>
									<div class="row">
										<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">PERSONAL EXTERNO</div>
										<div class="col-xs-9 col-sm-9 col-md-9 col-lg-9">
										<select name="externo" id="externo" class="form-control">
											<option value="">Seleccione si es personal externo...</option>
											<option value="Si">Si</option>
											<option value="No">No</option>
										</select>
										</div>
									</div>
									<div class="row">
										<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">TIPO</div>
										<div class="col-xs-9 col-sm-9 col-md-9 col-lg-9">
										<select name="tipo" id="tipo" class="form-control">
											<option value="">Seleccione el tipo...</option>
											<?php 
												$sentencia = "SELECT * from nomsituaciones ORDER BY codigo ASC";
												$resultado = $db->query($sentencia);
												while ($filas = $resultado->fetch_object()) 
												{
													echo "<option value='".$filas->codigo."'>".$filas->situacion."</option>";
												}

												?>

											<?php ?>
										</select>
										</div>
									</div>
									<div class="row">
										<div class="form-group margin-top-20">
											<div class="col-md-1"></div>
											<label class="control-label col-md-11 text-left" style="padding-bottom: 10px">PROMOCIÓN</label>
											<div class="col-md-1"></div>
											<div class="col-md-10">
												<?php
													$sql = "SELECT id,descripcion
															FROM   promocion 
															ORDER BY id  ASC";	
													$res = $db->query($sql);
												?>
												<select multiple="multiple" class="multi-select" id="promocion" name="promocion[]">
													<?php
														while($promocion = $res->fetch_object())
														{
															?> <option value="<?php echo $promocion->id; ?>"><?php echo $promocion->descripcion; ?></option><?php
														}
													?>
												</select>
											</div>
										</div>
								</div>

								<div class="form-actions fluid">
									<div class="row">
										<div class="col-md-11 text-center">
											<button type="button" class="btn btn-sm blue active" id="btn-aceptar">Aceptar</button>
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
</div>
<?php include("../footer4.php"); ?>
<script src="../../includes/assets/plugins/jquery-multi-select/js/jquery.multi-select.js"></script>
<script src="../../includes/assets/plugins/jquery-validation/dist/jquery.validate.min.js"></script>
<script src="../../includes/assets/plugins/jquery-validation/localization/messages_es.js"></script>
<script src="../../includes/assets/plugins/jquery-validation/dist/additional-methods.min.js"></script>
<script>
$(document).ready(function(){

    $('#promocion').multiSelect();

	$("#btn-aceptar").on("click",function()
	{
		var promocion = $("#promocion").val();
		var posicion  = $("#posicion").val();
		var nombre    = $("#nombre").val();
		var apellido  = $("#apellido").val();
		var cargo     = $("#cargo").val();
		var funcion   = $("#funcion").val();
		var externo    = $("#externo").val();
		var genero    = $("#genero").val();
		console.log(posicion+" "+nombre+" "+apellido+" "+cargo+" "+funcion+" "+genero+" "+promocion );
		location.href ="listado_funcionarios_ordenados.php?posicion="+posicion+"&nombre="+nombre+"&apellido="+apellido+"&cargo="+cargo+"&funcion="+funcion+"&genero="+genero+"&promocion="+promocion+"&externo="+externo;
	});
});	


</script>
</body>
</html>