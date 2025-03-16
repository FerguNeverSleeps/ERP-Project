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
									<label class='col-xs-2 col-sm-2 col-md-2 col-lg-2 control-label text-center'>Año</label>
									<div class="col-xs-10 col-sm-10 col-md-10 col-lg-10 text-center">

										<select name="anio" id="anio" class="form-control">
											<option value="">Seleccione un Año</option>
											<?php
											$query="SELECT anio
											FROM `nom_nominas_pago` 
											WHERE `codtip` = '".$_SESSION['codigo_nomina']."' AND `status` = 'C'
											GROUP BY anio";
											$result = sql_ejecutar($query);
											while ($row = fetch_array($result))
											{
												$codtip = $row['codtip']; // Tabla nomtipos_nomina
												// 1-Direccion 2-Fijos 3-Pensionados 4-Ingresos Pendientes
												?>
												<option value="<?php echo $row['anio'];?>"><?php echo $row['anio']; ?></option>
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
									<div id="mes_box"></div>
								</div>
								<div class="row">
									<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">&nbsp;</div>
								</div>
								<div class="row">
									<div id="quincena_box"></div>
								</div>
								<div class="row">
									<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">&nbsp;</div>
								</div>
								<div class="row">
									<div id="planilla_box"></div>
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
	$("#generar_rpt").hide();		
	$("#generar_rpt_xls").hide();
	$("#anio").on("click",function()
	{
		$("#mes_box").empty();
		$("#planilla_box").empty();
		anio = $("#anio").val();
		nomina(anio);
	});
	function nomina(anio)
	{
		$.get("ajax/obtenerMesPlanillaSipe.php",{anio:anio},function(res)
		{
			$("#mes_box").empty();
			$("#planilla_box").empty();
			$("#mes_box").append(res);
			$("#mes").change(function()
			{
				mes = $("#mes").val();
				tipo = $("#tipo").val();
				$.get("ajax/obtenerPlanillaCerradaMultiple.php",{tipo:tipo,anio:anio, mes:mes},function(res1){
					$("#quincena_box").empty();
					$("#planilla_box").empty();
					$("#planilla_box").append(res1	);
				    $('#tipos').multiSelect();
				    $('.ms-container').css('width','100%');
					mes   = $("#mes").val();
					anio  = $("#anio").val();
					tipos = $("select#tipos").val();
					$("#generar_rpt").show();
					//$("#generar_rpt_xls").show();
					$("#generar_rpt").off("click").on("click", function(){
						console.log(anio+" "+mes+" "+tipos);
						mes   = $("#mes").val();
						anio  = $("#anio").val();
						tipos = $("select#tipos").val();
						if(mes=== "" || tipos === "")
						{
							alert("Por favor, ingrese un valor válido");
						}
						else
						{
								var url =  "../../reportes/excel/rpt_suntrac.php?mes="+mes+"&anio="+anio+"&tipos="+tipos; 
							window.open(url);
						}
					});
					$("#generar_rpt_xls").on("click", function(){
						var url =  "../../reportes/excel/rpt_suntrac.php?mes="+mes+"&anio="+anio+"&tipos="+tipos; 
						window.open(url);
					});
				});
			});
		});
	}
});
</script>

</body>

</html>

