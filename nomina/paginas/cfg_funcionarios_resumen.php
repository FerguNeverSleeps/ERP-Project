<?php 
session_start();
	
include ("../header4.php");
include("../lib/common.php");
include("func_bd.php");	
?>
<script>
function desglose()
{

	var tipo_empleado = document.form1.tipo_empleado.value; // PK nom_nominas_pago
	//../../reportes/excel/excel_funcionarios_transitorios.php
	location.href='../../reportes/excel/excel_funcionarios_transitorios.php?tipo_empleado='+tipo_empleado; 
}
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
								Parámetros del Reporte
							</div>
							<div class="actions">

								<a class="btn btn-sm blue"  onclick="javascript: window.location='../submenu_reportes_integrantes.php'">
									<i class="fa fa-arrow-left"></i> Regresar
								</a>

							</div>
						</div>
						<div class="portlet-body">
							<div class="row">
								<div class="col-md-12 text-center">
									<select name="tipo_empleado" id="select2" class="form-control">
									<option value="">Seleccione una Planilla</option>
									<?php
										$query="SELECT * FROM tipoempleado
												ORDER BY IdTipoEmpleado DESC";
												
										$result=sql_ejecutar($query);
										while ($row = fetch_array($result))
										{
											$codtip = $row['codtip']; // Tabla nomtipos_nomina
											// 1-Direccion 2-Fijos 3-Pensionados 4-Ingresos Pendientes
										?>
											<option value="<?php echo $row['Descripcion'];?>"><?php echo $row['Descripcion']; ?></option>
										<?php
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
