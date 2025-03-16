<?php 
session_start();	
include ("../header4.php");
include("../lib/common.php");
include("func_bd.php");	
?>
<script>
function desglose()
{
	var anio = document.form1.anio.value; // PK nomtipos_nomina
	var mes = document.form1.mes.value; // PK nomtipos_nomina
	var tipo_planilla = document.form1.tipo_planilla.value; // PK nomtipos_nomina
	if(anio=='')
	{
		alert('Por favor, seleccione una fecha de inicio');
	}
	else
	{
		
		if(mes=='')
		{
			alert('Por favor, seleccione una fecha final');
		}
		else{
			window.location='reportes_planilla/detalle_preelaborada.php?anio='+anio+'&mes='+mes;

		}
	}
	//AbrirVentana('../../reportes/pdf/pdf_estimado_liquidaciones.php');

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
                                Detalles SIPE por rango
							</div>
							<div class="actions">

								<a class="btn btn-sm blue"  onclick="javascript: window.location='submenu_reportes.php?modulo=45'">
									<i class="fa fa-arrow-left"></i> Regresar
								</a>

							</div>
						</div>
						<div class="portlet-body">
							<div class="row">
								<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-center">&nbsp;</div>
							</div>
							<div class="row">
								<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 text-right">Año</div>
								<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 text-center">
                                    <input name="anio" type="number" id="anio" max="<?php echo date("Y") ?>" class="form-control" value="<?php echo date("Y") ?>">

								</div>
							</div><br>
							<div class="row">
								<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-center">&nbsp;</div>
							</div>
							<div class="row">
								<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 text-right">Mes</div>
								<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 text-center">
                                    <input name="mes" type="number" id="mes" min="1" max="12" class="form-control" value="<?php echo date("m") ?>">


								</div>
							</div><br>
							<div class="row">
								<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-center">&nbsp;</div>
							</div>
							<div class="row">
		                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-center" align="center">
									<div class="btn blue" onClick="javascript:desglose();"><i class="fa fa-download"></i> Generar Reporte</div>
		                        </div>
		                    </div>
		                    <div class="row">
								<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-center">&nbsp;</div>
							</div>
							<div class="row">
								<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-center">&nbsp;</div>
							</div>
							<input type="hidden" name="tipo_planilla" id="tipo_planilla" value="<?= $_SESSION['codigo_nomina']; ?>" > 
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
