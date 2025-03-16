<?php
require_once('../lib/database.php');

$db = new Database($_SESSION['bd']);

$sql = "SELECT e.nivel1
		FROM nomempresa e";
$res = $db->query($sql);
$empresa = $res->fetch_object();
?>
<?php include("../header4.php"); ?>
<style>
.portlet > .portlet-body.blue, .portlet.blue {
    background-color: #ffffff !important;
}

.form-horizontal .control-label {
    text-align: center;
    padding-top: 3px;
}

.form-body{
	padding-bottom: 5px;
}

.form-horizontal{
	margin-bottom: 0px;
}

label.error {
    color: #b94a48;
}
</style>
<div class="page-container">
	<div class="page-content-wrapper">
		<div class="page-content">
			<!-- BEGIN PAGE CONTENT-->

			<div class="row">
				<div class="col-md-9">
					<div class="alert" style="padding: 10px 15px; display: none"></div>
				</div>
				<div class="col-md-9">
					<!-- BEGIN EXAMPLE TABLE PORTLET-->
					<div class="portlet box blue">
						<div class="portlet-title">
							<div class="caption">
								Exportar colaboradores a Excel
							</div>
						</div>
						<div class="portlet-body form" id="blockui_portlet_body">

							<form action="xls_exportar_empleados.php" class="form-horizontal" id="form_excel" name="form_excel" method="post" enctype="multipart/form-data">
								<div class="form-body">

									<div class="form-group" style="margin-top: 20px">
										<label class="col-md-2 control-label"><?php echo (isset($empresa->nomniv1) && $empresa->nomniv1!='') ? $empresa->nomniv1 : 'Nivel 1';   ?></label>
										<div class="col-md-9">
										<?php 
											$sql = "SELECT codorg, CONCAT_WS(' ', codorg, descrip, markar) as descrip 
													FROM   nomnivel1";												
											$res = $db->query($sql);
										?>	
											<select name="codnivel1" id="codnivel1" class="form-control select2me" data-placeholder="Seleccione <?php echo $empresa->nomniv1; ?>">
												<?php
													echo "<option value=''>Seleccione ".$empresa->nomniv1."</option>";

													while($fila = $res->fetch_assoc())
													{
														echo "<option value='{$fila['codorg']}'>".$fila['descrip']."</option>";
													}
												?>
											</select>							
										</div>
									</div>
								</div>

								<div class="form-actions fluid">
									<div class="row">
										<div class="col-md-11 text-center">
											<!--<div class="col-md-offset-4 col-md-8">-->
												<button type="button" class="btn btn-sm blue active" id="btn-exportar">Exportar</button>&nbsp;
											<!--</div>-->
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
				codnivel1: { required: true}
			},
	});

	if( $("#a_download").length ) 
	{
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

	    var codnivel1 = $("#codnivel1").val();
	 //    location.href = 'xls_exportar_empleados.php?codnivel1='+codnivel1;

	 //    // Consultar cuantos registros tiene el codnivel1
	 //    $.post( "ajax_codnivel.php", { codnivel1: codnivel1 }, function( tiempo ) {
		//     window.setTimeout(function () {
	 //            App.unblockUI('#blockui_portlet_body');
	 //            $("#codnivel1").select2("val", "");
	 //        }, tiempo); 
		// }); 

	    $.post( "xls_exportar_empleados.php", { codnivel1: codnivel1 }, function(file) {
	    	//console.log(file);
		    window.setTimeout(function () {
	            App.unblockUI('#blockui_portlet_body');
	            $("#codnivel1").select2("val", "");
	            //console.log('exportar_empleados_excel.php?download&filename'+file);
	            location.href = 'exportar_empleados_excel.php?download&filename='+file;
	        }, 2000, file); 
		}); 
	});
});	
</script>
</body>
</html>