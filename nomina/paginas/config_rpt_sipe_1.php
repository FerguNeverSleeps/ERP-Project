<?php 
require_once('../lib/database.php');

$db = new Database($_SESSION['bd']);	
?>
<?php include("../header4.php"); ?>
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
</style>
<div class="page-container">
	<div class="page-content-wrapper">
		<div class="page-content">
			<!-- BEGIN PAGE CONTENT-->
			<div class="row">
				<div class="col-md-12">
					<!-- BEGIN EXAMPLE TABLE PORTLET-->
					<div class="portlet box blue">
						<div class="portlet-title">
							<div class="caption">
								Par&aacute;metros del Reporte
							</div>
							<div class="actions">
								<a class="btn btn-sm blue"  onclick="javascript: window.location='submenu_reportes.php?modulo=45';">
									<i class="fa fa-arrow-left"></i> Regresar
								</a>				
							</div>
						</div>
						<div class="portlet-body form" id="blockui_portlet_body">

							<form class="form-horizontal" id="form_excel" name="form_excel" method="post">
								<div class="row">&nbsp;</div>
								<div class="row">
									<div class="form-group">
										<label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2
									col-md-3 col-lg-offset-2 col-lg-3 control-label">Fecha Inicio:</label>
									<div class="col-md-3">
										<div class="input-group date date-picker">
											<input type="text" class="form-control input-circle-left" name="fecha_inicio" id="fecha_inicio" readonly="" value="<?= date('01-m-Y'); ?>">
											<span class="input-group-btn">
												<button class="btn default" type="button">
												    <i class="fa fa-calendar"></i>
												</button>
											</span>
											</div>
										</div>
									</div>
								</div><p></p>
								<div class="row">
									<div class="form-group">
										<label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2
									col-md-3 col-lg-offset-2 col-lg-3 control-label">Fecha Fin:</label>
									<div class="col-md-3">
										<div class="input-group date date-picker">
											<input type="text" class="form-control input-circle-left" name="fecha_fin" id="fecha_fin" readonly="" value="<?= date('30-m-Y'); ?>">
											<span class="input-group-btn">
												<button class="btn default" type="button">
												    <i class="fa fa-calendar"></i>
												</button>
											</span>
											</div>
										</div>
									</div>
								</div><p></p>

								<div class="form-actions fluid">
									<div class="row">
										<div class="col-md-11 text-center">
											<button type="button" class="btn btn-sm blue active" id="btn-exportar">Exportar</button>&nbsp;
											<?php
												if(isset($_GET['download']) && isset($_GET['filename']))
												{ 
												?>
													<a href="<?php echo $_GET['filename']; ?>" class="btn btn-sm red" id="a_download">
													<i class="fa fa-download"></i> Descargar</a>
												<?php
												}
											?>	
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
<script src="../../includes/assets/plugins/jquery-validation/dist/jquery.validate.min.js"></script>
<script src="../../includes/assets/plugins/jquery-validation/localization/messages_es.js"></script>
<script src="../../includes/assets/plugins/jquery-validation/dist/additional-methods.min.js"></script>
<script>
$(document).ready(function(){
	$("#form_excel").validate({
		rules: {
			fecha_inicio: { required: true},
			fecha_fin: { required: true}
		},
	});


	if( $("#a_download").length ) 
	{	
		// Descarga automática del archivo
		document.getElementById("a_download").click();
	}

	$("#btn-exportar").click(function(){

		var validacion = $('#form_excel').valid();
		if(!validacion) return false;	

		if( $("#a_download").length ) 
		{
			$("#a_download").hide();	
		}

		App.blockUI({
            target: '#page-container',
            boxed: true,
            message: 'Procesando...',
        });	

		var fecha_inicio = $("#fecha_inicio").val();
		var fecha_fin    = $("#fecha_fin").val();

	    $.get( "xls_reporte_sipe.php", { fecha_inicio: fecha_inicio, fecha_fin: fecha_fin }, function(file) {

		    window.setTimeout(function () {
	            App.unblockUI('#blockui_portlet_body');
	            $("#fecha_inicio").select2("val", "");
	            $("#fecha_fin").select2("val", "");
	            location.href = 'config_rpt_sipe.php?download&filename='+file;
	        }, 2000, file); 

		}); 
	});
	$('.date-picker').datepicker({
        orientation: "left",
        language: 'es',
        autoclose: true
    });

});	
</script>
</body>
</html>