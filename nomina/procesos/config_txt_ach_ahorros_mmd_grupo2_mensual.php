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
								<?php echo $_SESSION["nomina"].' '; ?> - ACH BANCO GENERAL - AHORROS MENSUAL (GRUPO 2)
							</div>
							<div class="actions">
								<a class="btn btn-sm blue"  onclick="javascript: window.location='menu_ach.php?modulo=312';">
									<i class="fa fa-arrow-left"></i> Regresar
								</a>				
							</div>
						</div>
						<div class="portlet-body form" id="blockui_portlet_body">

							<form class="form-horizontal" id="form_ach_ahorros_mmd_grupo2_mensual" name="form_ach_ahorros_mmd_grupo2_mensual" method="post">
								<div class="row">&nbsp;</div>
								<div class="row">
									<div class="form-group">
										<label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2
                                                                                col-md-3 col-lg-offset-2 col-lg-3 control-label">Año:</label>
                                                                                <div class="col-md-3">
                                                                                        <select name="anio" id="anio" class="form-control">
                                                                                                <option value="">Seleccione Año...</option>
                                                                                                <?php 
                                                                                                        $sentencia = "SELECT DISTINCT(YEAR(fechapago)) as anio FROM nom_nominas_pago"
                                                                                                                    . " WHERE  `fechapago` !=  '0000-00-00'";
                                                                                                        $resultado = $db->query($sentencia);
                                                                                                        while ($filas = $resultado->fetch_object()) 
                                                                                                        {
                                                                                                                echo "<option value='".$filas->anio."'>".$filas->anio."</option>";
                                                                                                        }

                                                                                                        ?>

                                                                                                <?php ?>
                                                                                        </select>
                                                                                </div>
                                                                        </div><p></p>
                                                                </div>
								<div class="row">
									<div class="form-group">
										<label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2
                                                                                col-md-3 col-lg-offset-2 col-lg-3 control-label">Mes:</label>
                                                                                <div class="col-md-3">
                                                                                        <select name="mes" id="mes" class="form-control">
                                                                                                <option value="0">Seleccione el Mes...</option>
                                                                                                <option value="1">Enero</option>
                                                                                                <option value="2">Febrero</option>
                                                                                                <option value="3">Marzo</option>
                                                                                                <option value="4">Abril</option>
                                                                                                <option value="5">Mayo</option>
                                                                                                <option value="6">Junio</option>
                                                                                                <option value="7">Julio</option>
                                                                                                <option value="8">Agosto</option>
                                                                                                <option value="9">Septiembre</option>
                                                                                                <option value="10">Octubre</option>
                                                                                                <option value="11">Noviembre</option>
                                                                                                <option value="12">Diciembre</option>
                                                                                        </select>
                                                                                </div>
                                                                            </div>
								</div><p></p>

								<div class="form-actions fluid">
									<div class="row">
										<div class="col-md-11 text-center">
											<button type="button" class="btn btn-sm green active" id="btn-exportar">Procesar</button>&nbsp;
											
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
$(document).ready(function()
{
        $("#anio").select2();
	$("#mes").select2();
	$("#form_ach_ahorros_mmd_grupo2_mensual").validate({
		rules: {
			anio: { required: true},
			mes: { required: true}
		},
	});


	$("#btn-exportar").click(function(){

		
		App.blockUI({
                    target: '#page-container',
                    boxed: true,
                    message: 'Procesando...',
                });	

		var anio = $("#anio").val();
		var mes    = $("#mes").val();

                location.href ="txt_ach_ahorros_mmd_grupo2_mensual.php?anio="+anio+"&mes="+mes;
	});
        
        
	
});

	
</script>
</body>
</html>