<?php 
session_start();
	
include ("../header4.php");
include("../lib/common.php");
include("func_bd.php");	
$conexion = new bd($_SESSION['bd_nomina']);
?>
<form id="form1" name="form1" method="post" action="">
<div class="page-container">
	<div class="page-wrapper-containter">
		<div class="page-content">
			<div class="row">
				<div class="col-md-2"></div>
				<div class="col-md-8">
					<div class="portlet box blue">
						<div class="portlet-title">
							<div class="caption">
								Contabilización a Innova
							</div>
							<div class="actions">

								<a class="btn btn-sm blue"  onclick="javascript: window.location='menu_ach.php?modulo=312'">
									<i class="fa fa-arrow-left"></i> Regresar
								</a>

							</div>
						</div>
						<div class="portlet-body">
							<div class="row">
								<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-center">
									<select name="codnom" id="codnom" class="form-control">
									<option value="">Seleccione una Planilla</option>
									<?php
										$query="SELECT codnom, descrip, codtip 
												FROM   nom_nominas_pago WHERE codtip='".$_SESSION['codigo_nomina']."'
												ORDER BY codnom DESC, codtip";
												
										$result=$conexion->query($query);
										while ($row = $result->fetch_array())
										{
											$codtip = $row['codtip']; // Tabla nomtipos_nomina
											// 1-Direccion 2-Fijos 3-Pensionados 4-Ingresos Pendientes
										?>
											<option value="<?php echo $row['codnom'];?>"><?php echo $row['descrip']; ?></option>
										<?php
										}	
									?>
									</select>
									<input type="hidden" name="tipnom" id="tipnom" value="<?php echo $codtip; ?>" >

								</div>
							</div>
							<div class="row">
								<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-center">&nbsp;</div>
							</div>
							<div class="row">
		                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-center" align="center">
									<div class="btn blue" id="generarReporte"><i class="fa fa-download"></i> Generar Reporte</div>
		                        </div>
		                    </div>
		                    <div class="row">
								<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-center">&nbsp;</div>
							</div>
							<div class="row">
								<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-center">&nbsp;</div>
							</div>
						</div>
					</div>
				</div>

			</div>
		</div>
	</div>
</div>
</form>

<script>

$(document).ready(function()
{
	$("#generarReporte").on("click",function(res){
		var codnom = document.getElementById("codnom").value; // PK nom_nominas_pago
		var tipnom = document.getElementById("tipnom").value; // PK nomtipos_nomina
		location.href = "txt_innova.php?codnom="+codnom+"&tipnom="+tipnom;
		/*let data = {};
		data.codnom = document.getElementById("codnom").value;
		data.tipnom = document.getElementById("tipnom").value;
		$.ajax({
			method: "GET",
			url: "txt_innova.php",
			data: data, 
			dataType: "json",      
			cache: false,
			}).done(function(data){
				console.log(data);

			}).fail(function(){
		});*/
	});
});
</script>
</body>
<?php
include ("../footer4.php");

 ?>
</html>
