<?php 
require_once '../../generalp.config.inc.php';
session_start();
ob_start();

//error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require_once '../lib/common.php';
include ("func_bd.php");
$conexion=conexion();

if( isset($_POST['btn-guardar']) )
{
	$codorg  = ( isset($_POST['codigo'])  ) ? $_POST['codigo']  : '';
	$descrip = ( isset($_POST['descrip']) ) ? $_POST['descrip'] : '';

	$sql = "UPDATE nomprofesiones SET descrip='".$descrip."' WHERE codorg='".$codorg."'";
	$res = query($sql, $conexion);

	activar_pagina("maestro_profesion.php");    
}

if( isset($_GET['codigo']) || isset($_POST['codigo']) )
{
	$codigo = ( isset($_GET['codigo']) ) ? $_GET['codigo'] : $_POST['codigo'] ;

	$sql = "SELECT * FROM nomprofesiones WHERE codorg=".$codigo;
	$res = query($sql, $conexion);

	if( $fila = fetch_array($res) )
	{
		$descripcion = $fila['descrip'];
	}
}
?>
<?php include("../header4.php"); // <html><head></head><body> ?>
<link href="../../includes/assets/css/custom-datatables.css" rel="stylesheet" type="text/css"/>
<style type="text/css">
.portlet > .portlet-body.blue, .portlet.blue {
    background-color: #ffffff !important;
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
								<i class="fa fa-reorder"></i> Modificar Profesi&oacute;n
							</div>
						</div>
						<div class="portlet-body form">
							<form name="form-profesion" method="post" role="form" style="margin-bottom: 5px;">
								<input type="hidden" id="codigo" name="codigo" value="<?php echo $codigo; ?>">
								<div class="form-body">
									<div class="form-group">
										<label for="descrip">C&oacute;digo</label>
										<input type="text" class="form-control" 
										       id="codorg" name="codorg" value="<?php echo $codigo; ?>" disabled>
									</div>
									<div class="form-group">
										<label for="descrip">Descripci&oacute;n</label>
										<input type="text" class="form-control" 
										       id="descrip" name="descrip" value="<?php echo $descripcion; ?>" required>
									</div>
									<button type="submit" class="btn blue" id="btn-guardar" name="btn-guardar">Guardar</button>
									<button type="button" class="btn default" 
									        onclick="javascript: document.location.href='maestro_profesion.php'">Cancelar</button>
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
<script type="text/javascript">
	$(document).ready(function() {
		$( "#btn-guardar" ).click(function() {
		    var descripcion = $('#descrip').val();

		    if( descripcion == '' )
		    {
		    	alert('Debe llenar los campos obligatorios');
		    	return false;
		    }
		});
	});
</script>
</body>
</html>