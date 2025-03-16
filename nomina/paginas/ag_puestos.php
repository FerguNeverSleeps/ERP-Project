<?php 
require_once '../lib/common.php';
include ("func_bd.php");

$conexion = new bd($_SESSION['bd']);

$id  = (isset($_GET['id'])) ? $_GET['id'] : '';

if( isset($_POST['btn-guardar']) )
{
	$codigo      = ( isset($_POST['codigo']) )      ? $_POST['codigo']      : '' ;
	$descripcion = ( isset($_POST['descripcion']) ) ? $_POST['descripcion'] : '' ; 

	if($id==='')
		$sql = "INSERT INTO nompuestos (codigo, descripcion) VALUES ('{$codigo}','{$descripcion}')";
	else
	{
		$sql = "UPDATE nompuestos SET 
					   codigo  = '{$codigo}',
					   descripcion  = '{$descripcion}'
				WHERE  codigo  = '{$id}'";
	}

	$res = $conexion->query($sql);
	activar_pagina("puestos_trabajo.php");	 
}

if($id!=='')
{
	$sql = "SELECT codigo, descripcion FROM nompuestos WHERE codigo='{$id}'";
	$res = $conexion->query($sql, "utf8");

	if( $fila = $res->fetch_assoc() )
	{
		$codigo      = $fila['codigo'];
		$descripcion = $fila['descripcion'];
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
    margin-bottom: 3px;
}

.form-horizontal .control-label {
    text-align: left;
    padding-top: 3px;
}

.form-body{
	padding-bottom: 5px;
}
</style>
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
								<?php echo ($id==='') ? 'Agregar' : 'Editar'; ?> Puesto de Trabajo
							</div>
						</div>
						<div class="portlet-body form">
							<form class="form-horizontal" id="formPrincipal" name="formPrincipal" method="post" role="form" style="margin-bottom: 5px;">								
								<div class="form-body">
									<div class="form-group">
										<label class="col-md-2 control-label" for="codigo">C&oacute;digo:</label>
										<div class="col-md-9">
											<input type="text" class="form-control input-sm" 
										       id="codigo" name="codigo" value="<?php echo $codigo; ?>">
										</div>
									</div>
									<div class="form-group">
										<label class="col-md-2 control-label" for="descripcion">Descripci&oacute;n:</label>
										<div class="col-md-9">
											<input type="text" class="form-control input-sm" 
										       id="descripcion" name="descripcion" value="<?php echo $descripcion; ?>">
										</div>
									</div>
									<button type="submit" class="btn btn-sm blue active" id="btn-guardar" name="btn-guardar">Guardar</button>
									<button type="button" class="btn btn-sm default active" 
									        onclick="javascript: document.location.href='puestos_trabajo.php'">Cancelar</button>
									
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
<?php include("../footer4.php"); ?>
<script src="../../includes/assets/plugins/jquery-validation/dist/jquery.validate.min.js" type="text/javascript"></script>
<script type="text/javascript">
	$(document).ready(function() {

		$('#formPrincipal').validate({
	            rules: {
	                codigo: { required: true, digits: true },
	                descripcion: { required: true }
	            },
	            messages: {
	                codigo: { required: " ",
	                		  digits:   "Ingrese solo dígitos" },
	                descripcion: { required: " " }
	            },
	            highlight: function (element) { 
	                $(element)
	                    .closest('.form-group').addClass('has-error');
	            },
	            success: function (label) {
	                label.closest('.form-group').removeClass('has-error');
	                label.remove();
	            },
	    });
	});
</script>
</body>
</html>