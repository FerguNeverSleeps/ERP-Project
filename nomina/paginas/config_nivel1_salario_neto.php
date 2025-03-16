<?php 
session_start();	
include ("../header4.php");
include("../lib/common.php");
include("func_bd.php");	
?>
<form id="form1" name="form1" method="post" action="">
	<div class="page-container">
		<div class="page-wrapper-containter">
			<div class="page-content">
				<div class="row">
					<div class="col-xs-12 col-sm-12 col-md-8 col-lg-8 col-md-offset-2 col-lg-offset-2">
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
									<div id="mes_box"></div>
								</div>
								<div class="row">
									<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">&nbsp;</div>
								</div>
								<div class="row">
									<div class="col-xs-7 col-sm-7 col-md-7 col-lg-7" align="right">
										<div class="btn blue" id="generar_rpt"><i class="fa fa-download"></i> Generar Reporte</div>
									</div>
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
		anio=$("#anio").val();
		nomina(anio);			
	});
	function nomina(anio)
	{
		$.get("ajax/obtenerMesNomina.php",{anio:anio},function(res)
		{
			$("#mes_box").empty();
			$("#mes_box").append(res);
			$("#planilla_box").empty();
			$("#mes").change(function()
			{	
				mes = $("#mes").val();
				anio = $("#anio").val();
				$("#generar_rpt").show();
				var url='../../reportes/excel/xls_salarios_netos.php?mes='+mes+'&anio='+anio; 
				$("#generar_rpt").off("click").on("click", function(){
					if(mes=== "" || anio === "")
					{
						alert("Por favor, ingrese un valor válido");
					}
					else
					{
						window.open(url);
					}	
				});
			});
		});
	}
});
</script>

</body>

</html>

