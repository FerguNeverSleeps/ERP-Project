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
				<div class="col-md-9">
					<div class="alert" style="padding: 10px 15px; display: none"></div>
				</div>
				<div class="col-md-9">
					<!-- BEGIN EXAMPLE TABLE PORTLET-->
					<div class="portlet box blue">
						<div class="portlet-title">
							<div class="caption">
								Importar colaboradores desde Excel
							</div>
						</div>
						<div class="portlet-body form" id="blockui_portlet_body">

							<form action="xls_agregar_empleado.php" class="form-horizontal" id="form_excel" name="form_excel" method="post" enctype="multipart/form-data">
								<div class="form-body">

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
															 Seleccione
														</span>
														<span class="fileinput-exists">
															 Cambiar
														</span>
														<input type="file" id="archivo" name="archivo" class="required">														
													</span>
													<a href="#" class="input-group-addon btn default fileinput-exists" data-dismiss="fileinput">
														 Eliminar
													</a>
												</div>
											</div>
											<span class="help-block"></span>
										</div>
									</div>
								</div>

									<div class="form-actions fluid">
										<div class="row">
											<div class="col-md-offset-3 col-md-3">
													<button type="button" class="btn blue active" id="btn-exportar" name="btn-exportar"><i class="fa fa-download"></i>&nbsp;Exportar Plantilla</button>
											</div>
											<div class="col-md-3">
													<button type="button" class="btn blue active" id="btn-cargar" name="btn-cargar"><i class="fa fa-upload"></i>&nbsp;Cargar</button>
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
	$("#btn-exportar").click(function(){
		location.href = "../../plantillas/Plantilla_Importar_Colaboradores.xlsx";
	});

	$("#btn-cargar").click(function(){

		$(".alert").hide();

		var validacion = $('#form_excel').valid();

		if(!validacion) return false;

		App.blockUI({
            target: '#blockui_portlet_body',
            boxed: true,
            message: 'Procesando...',
        });

		var formData = new FormData();
		formData.append("archivo", document.getElementById("archivo").files[0]);

		var alert_close = '<button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>';

        $.ajax({
        	url:  'xls_agregar_empleado_marpesca.php',
        	type: 'POST',
        	data: formData,
        	contentType: false,
   		    processData: false,
   		    cache: false,
        	success: function(data){
        		resultado = data.split("%&&%");
        		partes    = resultado[1].split("&&&&");
        		[success, mensaje] = partes; 

        		if(success==='1')
	                $(".alert").removeClass( "alert-danger" ).addClass( "alert-info" );
        		else
        			$(".alert").removeClass( "alert-info" ).addClass( "alert-danger" );

        		$(".alert").html(mensaje + alert_close);

	            window.setTimeout(function () {
	                App.unblockUI('#blockui_portlet_body');
	                $(".alert").show();
	                $('.fileinput').fileinput('clear');
	            }, 2000);  
        	},
        	error: function(data){
				$(".alert").removeClass( "alert-info" ).addClass( "alert-danger" );
	            $(".alert").html("¡Ocurrió un error al realizar la petición!" + alert_close);

	           // console.log('Resultado Error: ');
	           // console.log(data);
        		App.unblockUI('#blockui_portlet_body');
        	}
        });
	});
});	
</script>
</body>
</html>