<?php 
session_start();
ob_start();
error_reporting(0);
require_once '../lib/common.php';
include ("func_bd.php");
$conexion=conexion();
error_reporting(0);

$id  = (isset($_GET['id'])) ? $_GET['id'] : '';

if( isset($_POST['btn-guardar']) )
{
	$cuenta  = ( isset($_POST['cuenta'])  ) ? $_POST['cuenta']  : '';
	$descrip = ( isset($_POST['descrip']) ) ? $_POST['descrip'] : '';
	$id_interno = ( isset($_POST['id_interno']) ) ? $_POST['id_interno'] : '';
	$Tipo = ( isset($_POST['Tipo']) ) ? $_POST['Tipo'] : '';


	if( empty($id) )
	{
		$sql = "INSERT INTO cwconcue (id,Cuenta,Descrip,id_interno,Tipo) VALUES (NULL,'{$cuenta}','{$descrip}','{$id_interno}','{$Tipo}')";
	}
	else
	{
		$sql = "UPDATE cwconcue SET 
				Cuenta  = '{$cuenta}', 
				Descrip = '{$descrip}' , 
				id_interno = '{$id_interno}'  , 
				Tipo = '{$Tipo}' 
				WHERE id=" . $id;
	}

	$res = query($sql, $conexion);

	activar_pagina("cuenta_contable.php");	 
}

$cuenta = $descrip = '';

if(isset($_GET['edit']))
{
	$sql = "SELECT Cuenta, Descrip,id_interno, Tipo FROM cwconcue WHERE id=" . $id;

	$res = query($sql, $conexion);

	if( $fila=fetch_array($res)  )
	{
		$cuenta  = $fila['Cuenta'];
		$descrip = $fila['Descrip'];
		$id_interno = $fila['id_interno'];
		$Tipo = $fila['Tipo'];
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
								Cuenta Contable
							</div>
						</div>
						<div class="portlet-body form">
							<form class="form-horizontal" name="formPrincipal" method="post" role="form" style="margin-bottom: 5px;">
								<div class="form-body">
									<div class="form-group">
										<label class="col-md-1 control-label" for="id_interno">ID Interno:</label>
										<div class="col-md-11">
											<input type="text" class="form-control input-sm" 
										       id="id_interno" name="id_interno" value="<?php echo $id_interno; ?>" required>
										</div>
									</div>
									<div class="form-group">
										<label class="col-md-1 control-label" for="cuenta">Cuenta:</label>
										<div class="col-md-11">
											<input type="text" class="form-control input-sm" 
										       id="cuenta" name="cuenta" value="<?php echo $cuenta; ?>" required>
										</div>
									</div>
									<div class="form-group">
										<label class="col-md-1 control-label" for="descrip">Descripci&oacute;n:</label>
										<div class="col-md-11">
											<input type="text" class="form-control input-sm" 
										       id="descrip" name="descrip" value="<?php echo $descrip; ?>" required>
										</div>
									</div>
									<div class="form-group">
										<label class="col-md-1 control-label" for="Tipo">Tipo de Cuenta:</label>
										<div class="col-md-11">
											<input type="text" class="form-control input-sm" 
										       id="Tipo" name="Tipo" value="<?php echo $Tipo; ?>" required>
										</div>
									</div>
									
									<button type="submit" class="btn btn-sm blue active" id="btn-guardar" name="btn-guardar">Guardar</button>
									<button type="button" class="btn btn-sm default active" 
									        onclick="javascript: document.location.href='cuenta_contable.php'">Cancelar</button>
									
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
			var cuenta      = $('#cuenta').val();
		    var descripcion = $('#descrip').val();

		    if( cuenta == '' || descripcion == '' )
		    {
		    	alert('Debe llenar los campos obligatorios');
		    	return false;
		    }
		});
	});
</script>
</body>
</html>