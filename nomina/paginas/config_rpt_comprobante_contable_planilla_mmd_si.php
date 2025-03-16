<?php 
require_once('../lib/database.php');

$db = new Database($_SESSION['bd']);	
?>
<?php include("../header4.php"); ?>
<link rel="stylesheet" type="text/css" href="../../includes/assets/plugins/jquery-multi-select/css/multi-select.css"/>
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

.text-left
{
	text-align: left !important;
}

.ms-container {
    width: 100%;
}

.ms-container .ms-list {
    height: 300px;
}

.ms-container .ms-selectable li.ms-elem-selectable, .ms-container .ms-selection li.ms-elem-selection {
    cursor: pointer;
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
								Diario Centro de Costo
							</div>
							<div class="actions">
								<a class="btn btn-sm blue"  onclick="javascript: window.location='submenu_reportes.php?modulo=45';">
									<i class="fa fa-arrow-left"></i> Regresar
								</a>				
							</div>
						</div>
						<div class="portlet-body form" id="blockui_portlet_body">

							<form class="form-horizontal" id="form1" name="form1" method="post">
								<div class="form-body">
									<div class="form-group margin-top-20">
										<div class="col-md-1"></div>
										<label class="control-label col-md-11 text-left" style="padding-bottom: 10px">Planillas:</label>
										<div class="col-md-1"></div>
										<div class="col-md-10">
											<?php
												$sql = "SELECT codnom, descrip
														FROM   nom_nominas_pago 
														WHERE  codtip = '{$_SESSION['codigo_nomina']}'
														ORDER BY codnom DESC";	
												$res = $db->query($sql);
											?>
                                                                                    <input type="hidden" name="codtip" id="codtip" value="<?php echo  $_SESSION[codigo_nomina];?>">
											<select multiple="multiple" class="multi-select" id="codnom" name="codnom[]">
												<?php
													while($planilla = $res->fetch_object())
													{
														?> <option value="<?php echo $planilla->codnom; ?>"><?php echo $planilla->codnom. " - ".$planilla->descrip; ?></option><?php
													}
												?>
											</select>
										</div>
									</div>
								</div>

								<div class="form-actions fluid">
									<div class="row">
										<div class="col-md-11 text-center">
											<button type="button" class="btn btn-sm blue active" id="btn-aceptar">Aceptar</button>
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
<script src="../../includes/assets/plugins/jquery-multi-select/js/jquery.multi-select.js"></script>
<script src="../../includes/assets/plugins/jquery-validation/dist/jquery.validate.min.js"></script>
<script src="../../includes/assets/plugins/jquery-validation/localization/messages_es.js"></script>
<script src="../../includes/assets/plugins/jquery-validation/dist/additional-methods.min.js"></script>
<script>
$(document).ready(function(){

    $('#codnom').multiSelect();

	$("#btn-aceptar").click(function(){	

	    var codnom = $("#codnom").val();
            var codtip = $("#codtip").val();
            
//            alert(codnom);
//            alert(codtip);
                    
	   	if(!codnom)
	   	{
	   		alert('Debe seleccionar al menos una planilla');
	   		return false;
	   	}
                
                location.href ="comprobante_contable_planilla_mmd_si_xls.php?codnom="+codnom+"&codtip="+codtip;
		//abrirVentana('../tcpdf/reportes/pdf_reporte_resumen_planilla.php?nomina=' + codnom, 660, 800);
	});
});	

</script>
</body>
</html>