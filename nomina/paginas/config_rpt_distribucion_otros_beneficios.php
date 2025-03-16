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
								<?php echo $_SESSION["nomina"].' '; ?> - DISTRIBUCIÓN OTROS BENEFICIOS
							</div>
							<div class="actions">
								<a class="btn btn-sm blue"  onclick="javascript: window.location='submenu_reportes.php?modulo=45';">
									<i class="fa fa-arrow-left"></i> Regresar
								</a>				
							</div>
						</div>
						<div class="portlet-body form" id="blockui_portlet_body">

							<form class="form-horizontal" id="form_resumen_salarios_centro_costo" name="form_resumen_salarios_centro_costo" method="post">
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
                                                                                                <option value="01">Enero</option>
                                                                                                <option value="02">Febrero</option>
                                                                                                <option value="03">Marzo</option>
                                                                                                <option value="04">Abril</option>
                                                                                                <option value="05">Mayo</option>
                                                                                                <option value="06">Junio</option>
                                                                                                <option value="07">Julio</option>
                                                                                                <option value="08">Agosto</option>
                                                                                                <option value="09">Septiembre</option>
                                                                                                <option value="10">Octubre</option>
                                                                                                <option value="11">Noviembre</option>
                                                                                                <option value="12">Diciembre</option>
                                                                                        </select>
                                                                                </div>
                                                                            </div>
								</div>

								<div class="row">
									<div class="form-group">
										<label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2 col-md-3 col-lg-offset-2 col-lg-3 control-label">Fecha Inicio</label>
                                        <div class="col-md-3">
                                            <div class="input-group date date-picker" data-provide="datepicker" data-date-end-date="0d" data-date-format="dd/mm/yyyy"> 
				                                <input name="fecha_inicio" type="text" id="fecha_inicio" class="form-control" value="<?php echo "01/01/".date("Y") ?>" maxlength="60">
				                                <span class="input-group-btn">
				                                   <button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
				                                </span>
				                            </div>
                                    	</div>
                                	</div>
								</div>

								<div class="row">
									<div class="form-group">
										<label class="text-right col-xs-offset-2 col-xs-3 col-sm-offset-2 col-sm-3 col-md-offset-2 col-md-3 col-lg-offset-2 col-lg-3 control-label">Fecha Fin</label>
                                        <div class="col-md-3">
                                            <div class="input-group date date-picker" data-provide="datepicker" data-date-end-date="0d" data-date-format="dd/mm/yyyy"> 
				                                <input name="fecha_fin" type="text" id="fecha_fin" class="form-control" value="<?php echo "01/01/".date("Y") ?>" maxlength="60">
				                                <span class="input-group-btn">
				                                   <button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
				                                </span>
				                            </div>
                                    	</div>
                                	</div>
								</div>   

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
function leap(year){
	return ((year%4===0 && year%100!==0)||(year%400===0)?true:false);
}

//Dias de los meses
function monthDays(year){
	return [
		31,
		leap(year)?29:28,
		31,
		30,
		31,
		30,
		31,
		31,
		30,
		31,
		30,
		31
	];
}

function onChangeAnioMes(){
	var anio=$("#anio").val();
	if(!anio) {
		anio=moment().format("YYYY");
		$("#anio").val(anio);
		$("#anio").trigger("change.select2");
	}

	var mes=$("#mes").val();
	if(!mes){
		mes=moment().format("MM");
		$("#mes").val(mes);
		$("#mes").trigger("change.select2");
	}

	var fecha_inicio="01/"+mes+"/"+anio;
	var fecha_fin=monthDays(anio)[mes*1-1]+"/"+mes+"/"+anio;
	console.log(fecha_inicio);
	console.log(fecha_fin);
	//$("#fecha_inicio").datepicker("setDate",fecha_inicio);
    //$("#fecha_fin").datepicker("setDate",fecha_fin);

    $("#fecha_inicio").val(fecha_inicio);
    $("#fecha_fin").val(fecha_fin);
}

$(document).ready(function()
{

	$("#anio").change(function(){
		onChangeAnioMes();
	});

	$("#mes").change(function(){
		onChangeAnioMes();
	});


    $("#anio").select2();
	$("#mes").select2();
	$("#form_resumen_salarios_centro_costo").validate({
		rules: {
			anio: { required: true},
			mes: { required: true}
		},
	});

	mes=moment().format("MM");
	$("#mes").val(mes);
	$("#mes").trigger("change.select2");
	$("#mes").change();

	$("#btn-exportar").click(function(){

		
		App.blockUI({
                    target: '#page-container',
                    boxed: true,
                    message: 'Procesando...',
                });	

		var anio = $("#anio").val();
		var mes    = $("#mes").val();
		var fecha_inicio    = $("#fecha_inicio").val();
        var fecha_fin       = $("#fecha_fin").val();


        location.href ="rpt_distribucion_otros_beneficios.php?anio="+anio+"&mes="+mes+"&fecha_inicio="+fecha_inicio+"&fecha_fin="+fecha_fin;
	});
        
        
	
});

	
</script>
</body>
</html>