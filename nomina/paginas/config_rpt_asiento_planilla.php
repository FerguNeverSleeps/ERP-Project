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
				<div class="col-md-10">
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
								<div class="form-body">
									<div class="form-group margin-top-20">
										<label class="col-md-2 control-label">Planilla No 1</label>
										<div class="col-md-9">
											<?php 
											$sql = "SELECT codnom, descrip, codtip 
													FROM   nom_nominas_pago 
													WHERE  codtip = '{$_SESSION['codigo_nomina']}' AND status='C' AND frecuencia=2";	
											$res = $db->query($sql); 
											?>	
											<select name="codnom1" id="codnom1" class="form-control select2me" data-placeholder="Seleccione una Planilla">
												<option value="">Seleccione una Planilla</option>
												<?php
													while($planilla = $res->fetch_object())
													{
														?><option value="<?php echo $planilla->codnom; ?>"><?php echo $planilla->descrip; ?></option><?php
													}
												?>
											</select>							
										</div>
									</div>
									<div class="form-group margin-top-20">
										<label class="col-md-2 control-label">Planilla No 2</label>
										<div class="col-md-9">
											<?php 
											$sql = "SELECT codnom, descrip, codtip 
													FROM   nom_nominas_pago 
													WHERE  codtip = '{$_SESSION['codigo_nomina']}' AND status='C' AND frecuencia=3";	
											$res = $db->query($sql); 
											?>	
											<select name="codnom2" id="codnom2" class="form-control select2me" data-placeholder="Seleccione una Planilla">
												<option value="">Seleccione una Planilla</option>
												<?php
													while($planilla = $res->fetch_object())
													{
														?><option value="<?php echo $planilla->codnom; ?>"><?php echo $planilla->descrip; ?></option><?php
													}
												?>
											</select>							
										</div>
									</div>
								</div>

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
			codnom1: { required: true},
			codnom2: { required: true}
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
            target: '#blockui_portlet_body',
            boxed: true,
            message: 'Procesando...',
        });	

	    var codnom1 = $("#codnom1").val();
	    var codnom2 = $("#codnom2").val();

	    $.post( "xls_reporte_asiento_planilla.php", { codnom1: codnom1, codnom2: codnom2 }, function(file) {

		    window.setTimeout(function () {
	            App.unblockUI('#blockui_portlet_body');
	            $("#codnom1").select2("val", "");
	            $("#codnom2").select2("val", "");
	            location.href = 'config_rpt_asiento_planilla.php?download&filename='+file;
	        }, 2000, file); 

		}); 
	});
});	
</script>
</body>
</html>