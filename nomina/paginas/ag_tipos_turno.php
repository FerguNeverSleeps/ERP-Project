<?php 
session_start();
ob_start();

require_once '../lib/common.php'; 
//error_reporting(E_ALL ^ E_DEPRECATED);
include ("func_bd.php");
$conexion=conexion();

// Evitar errores con acentos o caracteres especiales:
mysqli_query($conexion, "SET NAMES 'utf8'");
mysqli_query($conexion, "SET CHARACTER SET utf8 ");

$id = ( isset($_POST['registro_id']) ) ? $_POST['registro_id']  : '';

if( isset($_POST['btn-guardar']) )
{
	$descripcion = ( isset($_POST['descripcion']) ) ? $_POST['descripcion']  : '' ;
	$rotativo    = ( isset($_POST['rotativo']) )    ? $_POST['rotativo']     : 0 ; 

	if( empty($id) )
	{
		$sql = "INSERT INTO nomturnos_tipo (descripcion, rotativo) 
                VALUES ('{$descripcion}', {$rotativo})";
	}
	else
	{
		$sql = "UPDATE nomturnos_tipo SET 
				descripcion  = '{$descripcion}',
				rotativo     =  {$rotativo}
				WHERE turnotipo_id = " . $id;
	}

	$res = query($sql, $conexion);
	activar_pagina("tipos_turno.php");
}

$descripcion = ''; $rotativo = 0;

if( isset($_POST['registro_id']) )
{
	$sql = "SELECT turnotipo_id, descripcion, rotativo 
            FROM   nomturnos_tipo 
            WHERE  turnotipo_id = " . $id;

	$res = query($sql, $conexion);

	if( $fila=fetch_array($res)  )
	{
		$descripcion = $fila['descripcion'];
		$rotativo    = $fila['rotativo'];
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
    /* border: 1px solid silver; */
}

.btn{
	/* border-radius: 3px !important; */
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
								<?php echo (empty($id)) ? 'Agregar' : 'Editar'; ?> Tipo de Turno
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
									<div class="form-group">
										<label class="col-md-1 control-label" for="rotativo">Rotativo:</label>
										<div class="col-md-11 radio-list">
											<label class="radio-inline">
											<input type="radio" name="rotativo" id="rotativo" value="1" <?php echo ($rotativo==1) ? 'checked' : ''; ?> > Si </label>
											<label class="radio-inline">
											<input type="radio" name="rotativo" id="rotativo" value="0" <?php echo ($rotativo==0) ? 'checked' : ''; ?> > No </label>
										</div>
									</div>
									<button type="submit" class="btn btn-sm blue active" id="btn-guardar" name="btn-guardar">Guardar</button>
									<button type="button" class="btn btn-sm default active" 
									        onclick="javascript: document.location.href='tipos_turno.php'">Cancelar</button>
									
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
	$('#formPrincipal').validate({
            rules: {
                descripcion: {
                    required: true
                }
            },

            messages: {
                descripcion: { required: " " }
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
});
</script>
</body>
</html>