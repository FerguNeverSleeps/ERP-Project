<?php
require_once("../lib/common.php");
include("../paginas/func_bd.php");

?>
<?php include("../header4.php"); ?>
<link rel="stylesheet" type="text/css" href="../../includes/assets/plugins/bootstrap-fileinput/bootstrap-fileinput.css"/>
<style>
.portlet > .portlet-body.blue, .portlet.blue {
    background-color: #ffffff !important;
}

.portlet > .portlet-title > .caption {
    /*margin-bottom: 3px;*/
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
/*    margin-top: 15px;*/
}
</style>
<div class="page-container">
	<div class="page-content-wrapper">
		<div class="page-content">
			<!-- BEGIN PAGE CONTENT-->

			<div class="row">
				<div class="col-md-12">
					<div class="alert" style="padding: 10px 15px; display: none"></div>
				</div>
				<div class="col-md-12">
					<!-- BEGIN EXAMPLE TABLE PORTLET-->
					<div class="portlet box blue">
						<div class="portlet-title">
							<div class="caption">
								Calcular tiempos de incapacidad
							</div>
						</div>
						<div class="portlet-body form" id="blockui_portlet_body">

							<form action="tiempos_incapacidad.php" class="form-horizontal" id="form_tiempos" name="form_tiempos" method="post" enctype="multipart/form-data">
								<div class="form-body">

									<div class="form-group" style="margin-top: 20px">
										<label for="archivo" class="col-md-12 text-center">Calcular tiempos de incapacidad</label>
																			</div>

								</div>

									<div class="form-actions fluid">
										<div class="row">
											<div class="col-md-12 text-center">
													<button type="button" class="btn blue active" id="btn-cargar" name="btn-cargar">Calcular</button>
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
<script type="text/javascript" src="../../includes/assets/plugins/bootstrap-fileinput/bootstrap-fileinput.js"></script>
<script src="../../includes/assets/plugins/jquery-validation/dist/jquery.validate.min.js"></script>
<script src="../../includes/assets/plugins/jquery-validation/localization/messages_es.js"></script>
<script src="../../includes/assets/plugins/jquery-validation/dist/additional-methods.min.js"></script>
<script>
$(document).ready(function(){

$("#btn-cargar").on('click',function(){
	location.href='tiempos_incapacidad.php';
});
	
});	
</script>
</body>
</html>