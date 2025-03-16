<?php 
require_once('../lib/database.php');

$db = new Database($_SESSION['bd']);	
?>
<?php include("../header4.php"); ?>
<link rel="stylesheet" type="text/css" href="../../includes/assets/plugins/jquery-multi-select/css/multi-select.css"/>
<style>
.form-horizontal .control-label {
    /*text-align: center;*/
    padding-top: 7px;
    font-weight: 600;
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
				<div class="col-md-12">
					<!-- BEGIN EXAMPLE TABLE PORTLET-->
					<div class="portlet box blue">
						<div class="portlet-title">
							<div class="caption">
								<?php echo $_SESSION["nomina"].' '; ?> - REPORTE DE INCIDENTES DE COLABORADORES
							</div>
							<div class="actions">
								<a class="btn btn-sm blue"  onclick="javascript: window.location='submenu_reportes.php?modulo=45';">
									<i class="fa fa-arrow-left"></i> Regresar
								</a>				
							</div>
						</div>
						<div class="portlet-body form" id="blockui_portlet_body">

							<form class="form-horizontal" id="formulario" name="formulario" method="post">
								<div class="row">&nbsp;</div>
								<div class="row">
									<div class="form-group">
										<label class="text-right col-xs-offset-1 col-xs-3 col-sm-offset-1 col-sm-3 col-md-offset-1
                                                                                col-md-3 col-lg-offset-1 col-lg-3 control-label">Año:</label>
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
										<label class="text-right col-xs-offset-1 col-xs-3 col-sm-offset-1 col-sm-3 col-md-offset-1
                                                                                col-md-3 col-lg-offset-1 col-lg-3 control-label">Mes:</label>
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
										<label class="text-right col-xs-offset-1 col-xs-3 col-sm-offset-1 col-sm-3 col-md-offset-1 col-md-3 col-lg-offset-1 col-lg-3 control-label">Fecha Inicio:</label>
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
										<label class="text-right col-xs-offset-1 col-xs-3 col-sm-offset-1 col-sm-3 col-md-offset-1 col-md-3 col-lg-offset-1 col-lg-3 control-label">Fecha Fin:</label>
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

								<div class="row">
									<div class="form-group">
										<label class="text-right col-xs-offset-1 col-xs-3 col-sm-offset-1 col-sm-3 col-md-offset-1 col-md-3 col-lg-offset-1 col-lg-3 control-label">Centros de Costos:</label>
                                        <div class="col-md-7">
                                            
                                        	<?php
												$sql = "SELECT * FROM nomnivel1";
												$res = $db->query($sql);

												?>
												<select multiple="multiple" class="multi-select" id="nomnivel1" name="nomnivel1[]">
													<?php
														$vacio_nomnivel1=[];
														while($row = $res->fetch_object())
														{											
															$vacio_nomnivel1[]=$row->codorg;
															$descrip=$row->descrip;
															$descrip=utf8_encode($row->descrip);
															//print_r($nomconceptos);
															?> <option value="<?php echo $row->codorg; ?>"><?php echo $descrip; ?></option><?php
														}
													?>
												</select>
												<input type="hidden" id="nomnivel1_vacio" value="<?php print implode(",", $vacio_nomnivel1)?>">
												
                                    	</div>
                                	</div>
								</div> 

								<div class="row">
									<div class="form-group">
										<label class="text-right col-xs-offset-1 col-xs-3 col-sm-offset-1 col-sm-3 col-md-offset-1 col-md-3 col-lg-offset-1 col-lg-3 control-label">Conceptos:</label>
                                        <div class="col-md-7">
                                            
                                        	<?php
														$sql = "select sql_reporte from config_reportes_planilla where id=10";	
														$res = $db->query($sql);
														$sql_reporte = $res->fetch_object();

														if(isset($sql_reporte->sql_reporte)){
															$sql_reporte=$sql_reporte->sql_reporte;
															//print_r($sql_reporte);

															$res = $db->query($sql_reporte);

															?>
															<select multiple="multiple" class="multi-select" id="codcon" name="codcon[]">
																<?php
																	$vacio_codcon=[];
																	while($nomconceptos = $res->fetch_object())
																	{											
																		$vacio_codcon[]=$nomconceptos->codcon;
																		$descrip=utf8_decode($nomconceptos->descrip);
																		//print_r($nomconceptos);
																		?> <option value="<?php echo $nomconceptos->codcon; ?>"><?php echo $descrip; ?></option><?php
																	}
																?>
															</select>
															<input type="hidden" id="codcon_vacio" value="<?php print implode(",", $vacio_codcon)?>">
															<?php
														}
														else{
															print "config_reportes_planilla id=10 no encontrado";
														}
													?>





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
<script src="../../includes/assets/plugins/jquery-multi-select/js/jquery.multi-select.js"></script>

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

	$('#codcon').multiSelect();
	$('#nomnivel1').multiSelect();

	$("#anio").change(function(){
		onChangeAnioMes();
	});

	$("#mes").change(function(){
		onChangeAnioMes();
	});


    $("#anio").select2();
	$("#mes").select2();
	$("#formulario").validate({
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
        var codcon = $("#codcon").val();

        if(!codcon || codcon.lenght===0){
        	codcon = $("#codcon_vacio").val();
        }

        var nomnivel1 = $("#nomnivel1").val();

        if(!nomnivel1 || nomnivel1.lenght===0){
        	nomnivel1 = "";
        }


        location.href ="rpt_incidentes_colaboradores.php?anio="+anio+"&mes="+mes+"&fecha_inicio="+fecha_inicio+"&fecha_fin="+fecha_fin+"&codcon="+codcon+"&nomnivel1="+nomnivel1;
	});
        
        
	
});

	
</script>
</body>
</html>