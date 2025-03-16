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
						IMPORTAR CONCEPTOS PLANILLA - EXCEL
					</div>
				</div>
				<div class="portlet-body form">

					<form action="importar_conceptos_planilla_xls.php" class="form-horizontal" id="form_excel" name="form_excel" method="post" enctype="multipart/form-data">
						<div class="form-body" id="blockui_portlet_body">

							<div class="form-group hide" style="margin-top: 20px">
								<label for="archivo" class="col-md-3 control-label">Archivo:</label>
								<div class="col-md-8">
									<input type="file" id="archivo1" name="archivo1" style="outline: none" class="required">
								</div>
							</div>

							<div class="form-group" style="margin-top: 20px">
								<label class="control-label col-md-2">Archivo</label>
								<div class="col-md-9">
									<div class="fileinput fileinput-new" data-provides="fileinput">
										<div class="input-group">
											<div class="form-control uneditable-input span3" data-trigger="fileinput">
												<!--<i class="fa fa-file fileinput-exists"></i>&nbsp;-->
												<span class="fileinput-filename">
												</span>
											</div>
											<span class="input-group-addon btn default btn-file">
												<span class="fileinput-new">
													 SELECCIONE
												</span>
												<span class="fileinput-exists">
													 CAMBIAR
												</span>
												<input type="file" id="archivo" name="archivo" class="required">														
											</span>
											<a href="#" class="input-group-addon btn default fileinput-exists" data-dismiss="fileinput">
												 ELIMINAR
											</a>
										</div>
									</div>
									<span class="help-block"></span>
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
			rules: {
				archivo: {
					required: true,
					extension: "xls|xlsx"
				}
			},
			messages: {
				archivo: {
					// required:  "Por favor, seleccione un archivo",
					 extension: "Extensión de archivo inválida"
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

	var fileinput = $('.fileinput').fileinput();
	fileinput.on('change.bs.fileinput', function(e, files){
	    $("#archivo").closest('.form-group').removeClass('has-error');
        $(".help-block").closest('.form-group').removeClass('has-error');
        $(".help-block").text('');
	})

});	
</script>
</body>
</html>