<?php
require_once 'control_asistencia/config/db.php';
include ("func_bd.php");

$id = (isset($_POST['registro_id'])) ? $_POST['registro_id']  : '';

if( isset($_POST['btn-guardar']) )
{
	$nombre = ( isset($_POST['nombre']) ) ? $_POST['nombre']  : '' ;

	if( empty($id) )
        $conexion->insert('caa_tiporeloj', array('nombre' => $nombre));	
	else
		$conexion->update('caa_tiporeloj', array('nombre' => $nombre), array('codigo' => $id));

	activar_pagina("tipos_reloj.php");
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
								<?php echo (empty($id)) ? 'Agregar' : 'Editar'; ?> Tipo de Reloj
							</div>
						</div>
						<div class="portlet-body form">
							<form class="form-horizontal" id="formPrincipal" name="formPrincipal" method="post" role="form" style="margin-bottom: 5px;">
								<div class="form-body">
									<div class="form-group">
										<label class="col-md-1 control-label" for="nombre">Nombre:</label>
										<div class="col-md-11">
											<input type="text" class="form-control input-sm" 
										           id="nombre" name="nombre" value="<?php echo (isset($nombre)) ? $nombre : ''; ?>">
										</div>
									</div>
									<button type="submit" class="btn btn-sm blue active" id="btn-guardar" name="btn-guardar">Guardar</button>
									<button type="button" class="btn btn-sm default active" 
									        onclick="javascript: document.location.href='tipos_reloj.php'">Cancelar</button>
									
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
                nombre: { required: true }
            },
            messages: {
                nombre: { required: " " }
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