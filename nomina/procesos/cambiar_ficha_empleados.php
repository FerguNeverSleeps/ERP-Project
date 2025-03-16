<?php
require_once("../../nomina/lib/common.php");
include("../../nomina/paginas/func_bd.php");
include("../../includes/dependencias.php");
?>
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
<div class="container">
	<!-- BEGIN PAGE CONTENT-->
<BR>
	<div class="row">
		<div class="col-md-offset-2 col-md-8">
			<div class="alert" style="padding: 10px 15px; display: none"></div>
		</div>
		<div class="col-md-offset-2 col-md-8">
			<!-- BEGIN EXAMPLE TABLE PORTLET-->
			<div class="portlet box blue">
				<div class="portlet-title">
					<div class="caption">
						CAMBIAR FICHAS
					</div>
				</div>
				<div class="portlet-body form">

					<form action="cambiar_ficha_empleados_xls.php" class="form-horizontal" id="form_excel" name="form_excel" method="post" enctype="multipart/form-data">
						<div class="form-body" id="blockui_portlet_body">

							<div class="form-group" style="margin-top: 20px">
								<label for="archivo" class="col-md-3 control-label">Ficha Anterior:</label>
								<div class="col-md-8">
									<input type="text" id="ficha1" name="ficha1" style="outline: none" class="required">
								</div>
							</div>
							<div class="form-group" style="margin-top: 20px">
								<label for="archivo" class="col-md-3 control-label">Ficha Nueva:</label>
								<div class="col-md-8">
									<input type="text" id="ficha2" name="ficha2" style="outline: none" class="required">
								</div>
							</div>
						</div>

							<div class="form-actions fluid">
								<div class="row">
									<div class="col-md-12 text-center">
										<input type="submit" class="btn blue active" id="btn-cargar" name="btn-cargar" value="Cargar">
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
<script>
$(document).ready(function(){
	$("#form_excel").validate({
			ficha1: {
				number: {
					required: true,
      				number: true
				}
			},
			ficha2: {
				number: {
					required: true,
      				number: true
				}
			},
			messages: {
				ficha1: {
					 required: "Rellene este campo"
				}
			},
			errorPlacement: function (error, element) {
				//console.log(error.text());
                $(element).closest('.form-group').addClass('has-error').find('.help-block').html(error.text()); 
		    },
		    highlight: function (element) {
                $(element).closest('.form-group').addClass('has-error'); 
		    },
		    success: function (label, element) {
		    	$(element).closest('.form-group').removeClass('has-error'); 
                label.closest('.form-group').removeClass('has-error');
                label.text('');
               // label.remove();                
		    }
	});
});	
</script>
</body>
</html>