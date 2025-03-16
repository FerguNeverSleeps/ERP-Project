<?php 
session_start();
ob_start();
error_reporting(0);
require_once '../lib/common.php';
include ("func_bd.php");
$conexion=conexion();
error_reporting(0);

$id  = (isset($_GET['id'])) ? $_GET['id'] : '';
 

if( isset($_POST['btn-guardar']) ){
	
	$descripcion = ( isset($_POST['descripcion']) ) ? utf8_decode($_POST['descripcion']) : '';
        $numero = ( isset($_POST['numero']) ) ? $_POST['numero'] : 0;
        $minimo = ( isset($_POST['minimo']) ) ? $_POST['minimo'] : 0;
        $maximo = ( isset($_POST['maximo']) ) ? $_POST['maximo'] : 0;

	if( empty($id) )
	{
		$sql = "INSERT INTO etapa (id_etapa,numero,minimo, maximo, descripcion) "
                        . "VALUES ('','{$numero}','{$minimo}','{$maximo}','{$descripcion}')";
	}
	else
	{
		$sql = "UPDATE etapa SET 
				numero  = '{$numero}',
                                minimo  = '{$minimo}',
                                maximo  = '{$maximo}',
				descripcion = '{$descripcion}'
				WHERE id_etapa=" . $id;
	}

	$res = query($sql, $conexion);
        echo $res;

	activar_pagina("etapas_lista.php");	 
}


if(isset($_GET['edit']))
{
	$sql = "SELECT * FROM etapa WHERE id_etapa=" . $id;

	$res = query($sql, $conexion);

	if( $fila=fetch_array($res)  )
	{
		
		$descripcion = $fila['descripcion'];
                $numero = $fila['numero'];
                $minimo = $fila['minimo'];
                $maximo = $fila['maximo'];
                
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
								Etapa
							</div>
						</div>
						<div class="portlet-body form">
							<form class="form-horizontal" name="formPrincipal" method="post" role="form" style="margin-bottom: 5px;">
								<div class="form-body">	
                                                                        <div class="form-group">
										<label class="col-md-2 control-label" for="cuenta">Número:</label>
										<div class="col-md-1">
											<input type="text" class="form-control input-sm" 
                                                                                               id="numero" name="numero" value="<?php echo utf8_encode ($numero); ?>" required>
										</div>
									</div>
                                                                        <div class="form-group">
										<label class="col-md-2 control-label" for="cuenta">Años Servicio (minimo):</label>
										<div class="col-md-1">
											<input type="text" class="form-control input-sm" 
                                                                                               id="minimo" name="minimo" value="<?php echo utf8_encode ($minimo); ?>" required>
										</div>
									</div>
                                                                        <div class="form-group">
										<label class="col-md-2 control-label" for="cuenta">Años Servicio (máximo):</label>
										<div class="col-md-1">
											<input type="text" class="form-control input-sm" 
                                                                                               id="maximo" name="maximo" value="<?php echo utf8_encode ($maximo); ?>" required>
										</div>
									</div>
                                                                        <div class="form-group">
										<label class="col-md-2 control-label" for="cuenta">Descripción:</label>
										<div class="col-md-4">
											<input type="text" class="form-control input-sm" 
                                                                                               id="descripcion" name="descripcion" value="<?php echo utf8_encode ($descripcion); ?>">
										</div>
									</div>
									<button type="submit" class="btn btn-sm blue active" id="btn-guardar" name="btn-guardar">Guardar</button>
									<button type="button" class="btn btn-sm default active" 
									        onclick="javascript: document.location.href='etapas_lista.php'">Cancelar</button>
									
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