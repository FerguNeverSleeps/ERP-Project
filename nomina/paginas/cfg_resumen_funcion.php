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
	location.href='../../reportes/excel/excel_funcionarios_funcion.php?tipo_empleado='+tipo_empleado; 
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
								<div class="col-md-8 text-center">
								 
									<input class="form-control input input-lg" type="text" name="tipo_empleado">
									 

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
