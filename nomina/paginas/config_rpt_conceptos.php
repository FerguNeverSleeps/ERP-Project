<?php 
session_start();	
include ("../header4.php");
include("../lib/common.php");
include("func_bd.php");	
?>
<script>
function desglose()
{

	var codnom = document.form1.cboTipoNomina.value; // PK nom_nominas_pago
	var codtip = document.form1.codt.value; // PK nomtipos_nomina
	location.href='excel/excel_planilla_adicional.php?codnom='+codnom+'&codtip='+codtip; 
}
</script>
<link rel="stylesheet" type="text/css" href="../../includes/assets/plugins/jquery-multi-select/css/multi-select.css"/>
<form id="form1" name="form1" method="post" action="">
	<div class="page-container">
		<div class="page-wrapper-containter">
			<div class="page-content">
				<div class="row">
					<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

					</div>
						<div class="portlet box blue">
							<div class="portlet-title">
								<div class="caption">
									Parámetros del Reporte
								</div>
								<div class="actions">
									<a class="btn btn-sm blue"  onclick="javascript: window.location='submenu_reportes.php?modulo=45'">
										<i class="fa fa-arrow-left"></i> Regresar
									</a>
								</div>
							</div>
							<div class="portlet-body">
								<div class="row">
									<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">&nbsp;</div>
								</div>
								<div class="row">
									<label class='col-xs-5 col-sm-5 col-md-5 col-lg-5 control-label text-right'>N° Colaborador</label>
                                    <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 text-center">
                                    	<input  name="ficha" id="ficha" type="text" class="form-control">
                                    </div>
								</div><br>
								<div class="row">
									<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">&nbsp;</div>
								</div>
								<div class="row">
                                    <label class='col-xs-5 col-sm-5 col-md-5 col-lg-5 control-label text-right'>Fecha Inicio</label>
                                    <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 text-center">
                                        <div class="input-group input-medium date date-picker"  data-date-format="yyyy-mm-dd">
                                            <input  name="fecha_inicio" id="fecha_inicio" type="text" class="form-control" readonly="">
                                            <span class="input-group-btn">
                                                <button class="btn default" type="button">
                                                    <i class="fa fa-calendar"></i>
                                                </button>
                                            </span>
                                        </div>
                                        <span class="help-block" style="text-align:left"> Seleccione un rango de fechas </span>
                                    </div>
								</div>
								<div class="row">
									<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">&nbsp;</div>
								</div>
								<div class="row">
                                    <label class='col-xs-5 col-sm-5 col-md-5 col-lg-5 control-label text-right'>Fecha Fin</label>
                                    <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 text-center">
                                        <div class="input-group input-medium date date-picker"  data-date-format="yyyy-mm-dd">
                                            <input  name="fecha_fin" id="fecha_fin" type="text" class="form-control" readonly="">
                                            <span class="input-group-btn">
                                                <button class="btn default" type="button">
                                                    <i class="fa fa-calendar"></i>
                                                </button>
                                            </span>
                                        </div>
                                        <span class="help-block" style="text-align:left"> Seleccione un rango de fechas </span>
                                    </div>
								</div>
								<div class="row">
									<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">&nbsp;</div>
								</div>
								<div class="row">
                                    <label class='col-xs-3 col-sm-3 col-md-3 col-lg-3 control-label text-right'>Conceptos</label>
									<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 text-center">

										<select name="conceptos" id="conceptos" class="form-control" multiple="multiple">
											<?php
											$query="SELECT codcon, descrip
											FROM `nomconceptos` 
                                            ORDER BY codcon
											";
											$result = sql_ejecutar($query);
											while ($row = fetch_array($result))
											{
												$codcon = $row['codcon'];
												?>
												<option value="<?php echo $row['codcon'];?>"><?php echo $row['codcon'].'-'.$row['descrip']; ?></option>
												<?php
											}	
											?>
										</select>
									</div>
                                </div>
								<div class="row">
									<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">&nbsp;</div>
								</div>
								<div class="row">
									<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">&nbsp;</div>
								</div>
								
								<div class="row">
									<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6" align="right">
										<div class="btn blue" id="generar_rpt"><i class="fa fa-download"></i> Generar Reporte</div>
									</div>
									<!--<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6" align="left">
										<div class="btn blue" id="generar_rpt_xls"><img src="../../includes/imagenes/ico_export.gif">
 Generar Excel</div>
									</div>-->
								</div>
								<div class="row">
									<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">&nbsp;</div>
								</div>								
								
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<input type="hidden" name="tipo" id="tipo" value="<?= $_SESSION['codigo_nomina'] ?>">
</form>
<?php
include ("../footer4.php");
 ?>
<script src="../../includes/assets/plugins/jquery-multi-select/js/jquery.multi-select.js"></script>
<script type="text/javascript">
$(document).ready(function()
{

    
    $("#fecha_inicio, #fecha_fin").datepicker({
    	format: 'yyyy-mm-dd',
    	autoclose: true
    });
    $("#conceptos,#frecuencias").multiSelect();
	$('.ms-container').css('width','90%');
    $("#generar_rpt").on("click", function(){
		var conceptos    = $("select#conceptos").val();
		var frecuencias  = $("select#frecuencias").val();
		var fecha_inicio = $("#fecha_inicio").val();
		var fecha_fin    = $("#fecha_fin").val();
		var ficha        = $("#ficha").val();
        
        if(fecha_inicio === "" || fecha_fin === "")
        {
            alert("Por favor, ingrese un rango de fechas válido");
        }
        if(fecha_inicio!== "" && fecha_fin !== "" )
        {
            //console.log(planilla+" => "+conceptos+" => "+codtip+" => "+frecuencia+" => "+fecha_inicio+" => "+fecha_fin);
            var url =  "../fpdf/rpt_conceptos_federal.php?conceptos="+conceptos+"&fecha_inicio="+fecha_inicio+"&fecha_fin="+fecha_fin+"&ficha="+ficha+"&frecuencias="+frecuencias; 
            window.open(url);
        }
    });
});
</script>

</body>

</html>

