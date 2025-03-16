<?php 
session_start();
include ("../header4.php");
include("../lib/common.php");
include("func_bd.php");	
?>
<script>
function desglose()
{
	var id_bisemana = $("#select_bisemana").val(); 
	if(!id_bisemana) return;
	window.open('horizontal_nomina_xls_patronal_bisemanal.php?id_bisemana='+id_bisemana); 
}
$(document).ready(function() { 
	$("#select_bisemana").select2();

});
</script>
<form id="form1" name="form1" method="post" action="">
<div class="page-container">
	<div class="page-wrapper-containter">
		<div class="page-content">
			<div class="row">
				<div class="col-md-12">
					<div class="portlet box blue">
						<div class="portlet-title">
							<div class="caption">
								Planilla Bisemanal
							</div>
							<div class="actions">

								<a class="btn btn-sm blue"  href="#" onclick="window.history.go(-1);">
									<i class="fa fa-arrow-left"></i> Regresar
								</a>

							</div>
						</div>
						<div class="portlet-body">
							<div class="row">
								<div class="col-md-12">
									<select id="select_bisemana" class="form-control">
									<option value="">Seleccione una Bisemana</option>
									<?php
										$query="SELECT *, year(fechaInicio) anio FROM  bisemanas order by anio desc, numBisemana desc";												
										$result=sql_ejecutar($query);
										while ($row = fetch_array($result))
										{
											//verificar si hay datos en la bisemana
											$sql="select count(*) t from reloj_detalle where id_encabezado in (select cod_enca from reloj_encabezado where fecha_ini='".$row["fechaInicio"]."' and fecha_fin='".$row["fechaFin"]."')";
											$result2=sql_ejecutar($sql);
											$row2 = fetch_array($result2);
											if(!$row2["t"]) continue;

											print "<option value=".$row['idBisemanas'].">".$row["anio"]." - Bisemana #".str_pad($row['numBisemana'],2,"0",STR_PAD_LEFT ).": Del ".date("d/m/Y",strtotime($row["fechaInicio"]))." al ".date("d/m/Y",strtotime($row["fechaFin"]))."</option>";
										
										}	
									?>
									</select>
									

								</div>
							</div>
							<div class="row">
								<div class="col-md-12">&nbsp;</div>
							</div>
							<div class="row">
		                        <div class="col-md-5">
		                        </div>                    
		                        <div class="col-md-1">                            
                                	<div  class="btn blue button-next" onClick="javascript:desglose()">Exportar</div>
		                        </div>                    

		                        
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
</html>
