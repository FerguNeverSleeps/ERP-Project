<?php 
session_start();
ob_start();
//error_reporting(0);
require_once '../lib/common.php';
include ("func_bd.php");
$conexion=conexion();
//error_reporting(0);

$id  = (isset($_GET['id'])) ? $_GET['id'] : '';

if( isset($_POST['btn-guardar']) )
{
	$descripcion = ( isset($_POST['descripcion']) )      ? $_POST['descripcion']      : '' ;

	if( empty($id) )
	{
		$sql = "INSERT INTO nomfuncion (descripcion_funcion) VALUES ('{$descripcion}')";

	}
	else
	{
		$sql = "UPDATE nomfuncion 
		        SET    descripcion_funcion = '{$descripcion}'
				WHERE  nomfuncion_id =" . $id;
	}

	$res = query($sql, $conexion);

	activar_pagina("maestro_funcion.php");	 
}

$descripcion = '';

if(isset($_GET['edit']))
{
	$sql = "SELECT * FROM nomfuncion WHERE nomfuncion_id = " . $id;

	$res = query($sql, $conexion);

	if( $fila=fetch_array($res)  )
	{
		$descripcion = $fila['descripcion_funcion'];
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

.form-control {
    border: 1px solid silver;
}

.btn{
	border-radius: 3px !important;
}

.form-body{
	padding-bottom: 5px;
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
								Funci&oacute;n
							</div>
						</div>
						<div class="portlet-body form">
							<form class="form-horizontal" id="formPrincipal" name="formPrincipal" method="post" role="form" style="margin-bottom: 5px;">
								<div class="form-body">
									<div class="form-group">
										<label class="col-md-1 control-label" for="descripcion">Descripci&oacute;n:</label>
										<div class="col-md-11">
											<input type="text" class="form-control input-sm" 
										       id="descripcion" name="descripcion" value="<?php echo $descripcion; ?>">
										</div>
									</div>						
									<button type="submit" class="btn btn-sm blue active" id="btn-guardar" name="btn-guardar">Guardar</button>
									<button type="button" class="btn btn-sm default active" 
									        onclick="javascript: document.location.href='maestro_funcion.php'">Cancelar</button>
									
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
	                descripcion: {
	                    required: true
	                },
	            },

	            messages: {
	                descripcion: { required: " " },
	            },

	            highlight: function (element) { // hightlight error inputs
	                $(element)
	                    .closest('.form-group').addClass('has-error'); // set error class to the control group
	            },

	            success: function (label) {
	                label.closest('.form-group').removeClass('has-error');
	                label.remove();
	            },
	    });

		$( "#btn-guardar" ).click(function() {
			/*
		    var descripcion = $('#descripcion').val();

		    if( descripcion == '' )
		    {
		    	alert('Debe llenar los campos obligatorios');
		    	return false;
		    }
		    */
		});
	});
</script>
</body>
</html>