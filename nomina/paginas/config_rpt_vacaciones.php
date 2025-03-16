<?php 
require_once('../lib/database.php');

$db = new Database($_SESSION['bd']);	
$modulo = $_GET['modulo'];
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
								<a class="btn btn-sm blue"  href="submenu_reportes.php?modulo=<?= $modulo ?>">
									<i class="fa fa-arrow-left"></i> Regresar
								</a>				
							</div>
						</div>
						<div class="portlet-body form" id="blockui_portlet_body">

							<form class="form-horizontal" id="form_excel" name="form_excel" method="post">
								<div class="form-body">
									<div class="form-group margin-top-20">
										<label class="col-md-2 control-label">Mes</label>
										<div class="col-md-9">
											<?php
											$anio_actual = date('Y'); 
											$mes_actual  = date('n'); // Número

											$meses = array("Enero", "Febrero", "Marzo",      "Abril",   "Mayo",      "Junio",
												           "Julio", "Agosto",  "Septiembre", "Octubre", "Noviembre", "Diciembre");
											?>	
											<select name="mes" id="mes" class="form-control select2me" data-placeholder="Seleccione un mes">
												<option value="">Seleccione un mes</option>
												<?php
													foreach($meses as $i => $mes)
													{
														$i+=1;

														if($i<=$mes_actual)
														{
															echo "<option value='$i'>$mes $anio_actual</option>";
														}
													}
												?>
											</select>	
											<input type="hidden" name="anio" id="anio" value="<?php echo $anio_actual; ?>">
										</div>
									</div>
									<div class="form-group margin-top-20">
										<label class="col-md-2 control-label">Per&iacute;odo</label>
										<div class="col-md-9">
											<select name="periodo" id="periodo" class="form-control select2me" data-placeholder="Seleccione un período">
												<option value="">Seleccione un período</option>
												<option value="1">01 al 30 (Período 1)</option>
												<option value="2">16 al 15 (Período 2)</option>
												<option value="3">Todos &nbsp;(01 al 30 / 16 al 15)</option>
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
								<input type="hidden" name="modulo" id="modulo" value="<?= $modulo ?>">

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
			mes:     { required: true},
			periodo: { required: true}
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

		var mes     = $("#mes").val();
		var periodo = $("#periodo").val();
		var anio    = $("#anio").val();
		var modulo  = $("#modulo").val();

	    $.post( "xls_reporte_vacaciones.php", { mes: mes, periodo: periodo, anio: anio, modulo: modulo }, function(file) {

		    window.setTimeout(function () {
	            App.unblockUI('#blockui_portlet_body');
	            $("#mes").select2("val", "");
	            $("#periodo").select2("val", "");
	            location.href = 'config_rpt_vacaciones.php?download&filename='+file;
	        }, 2000, file); 

		}); 
	});
});	
</script>
</body>
</html>