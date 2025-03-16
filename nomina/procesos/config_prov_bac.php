<?php 
session_start();
	
include ("../header4.php");
include("../lib/common.php");
include("../paginas/func_bd.php");	
?>
<form id="form1" name="form1" method="post" action="">
<div class="page-container">
	<div class="page-wrapper-containter">
		<div class="page-content">
			<div class="row">
				<div class="col-md-12">
					<div class="portlet box blue">
						<div class="portlet-title">
							<div class="caption">
								Parámetros del Reporte
							</div>
							<div class="actions">

								<a class="btn btn-sm blue"  onclick="javascript: window.location='submenu_reportes_nomina.php'">
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
									WHERE `codtip` = '".$_SESSION['codigo_nomina']."'
									GROUP BY anio";
									$result = sql_ejecutar($query);
									while ($row = fetch_array($result))
									{
										$codtip = $row['codtip'];
										?>
										<option value="<?php echo $row['anio'];?>"><?php echo $row['anio']; ?></option>
										<?php
									}	
									?>
									</select>
									<input type="hidden" name="codt" id="codt" value="" >
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
								<div id="quincena_box"></div>
							</div>
							<div class="row">
								<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">&nbsp;</div>
							</div>
							<div class="row">
								<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" align="center">
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
</body>
<?php
include ("../footer4.php");

 ?>
 <script type="text/javascript">
$(document).ready(function()
{
	$("#generar_rpt").hide();
	$("#generar_rpt2").hide();

	$("#anio").on("click",function()
	{
		$("#mes_box").empty();
		anio=$("#anio").val();
		nomina(anio);			
	});
	function nomina(anio)
	{
		$.get("../paginas/ajax/obtenerMesNomina.php",{anio:anio},function(res)
		{
			$("#mes_box").empty();
			$("#mes_box").append(res);
			$("#mes").change(function()
			{
				$("#quincena_box").empty();
				mes = $("#mes").val();
				$("#generar_rpt").show();
				$("#generar_rpt2").show();
				console.log(mes+" "+anio);
			});
		});
		
	}
	$("#generar_rpt").on("click", function(){
		mes = $("select#mes").val();
		anio = $("select#anio").val();

		var url = "excel_prov_bac.php?mes="+mes+"&anio="+anio; // the script where you handle the form input.
		window.open(url);	

	});

});
</script>
</html>
	
