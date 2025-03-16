<?php 
session_start();	
include ("../header4.php");
include("../lib/common.php");
include("func_bd.php");	
?>
<script>
function desglose()
{
	var fecha_inicio = document.form1.fecha_inicio.value; // PK nomtipos_nomina
	var fecha_fin = document.form1.fecha_fin.value; // PK nomtipos_nomina
	var tipo_planilla = document.form1.tipo_planilla.value; // PK nomtipos_nomina
	if(fecha_inicio=='')
	{
		alert('Por favor, seleccione una fecha de inicio');
	}
	else
	{
		
		if(fecha_fin=='')
		{
			alert('Por favor, seleccione una fecha final');
		}
		else{
			window.location='reportes_planilla/reporte_ahorros_rango.php?fecha_inicio='+fecha_inicio+'&fecha_fin='+fecha_fin+'&tipo_planilla='+tipo_planilla;

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
                                Ahorros MMD por Rango
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
								<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 text-right">Fecha Inicio</div>
								<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 text-center">
									<div class="input-group date date-picker" data-provide="datepicker" data-date-end-date="0d" data-date-format="dd-mm-yyyy"> 
				                         <input name="fecha_inicio" type="text" id="fecha_inicio" class="form-control" value="" maxlength="60">
				                          <span class="input-group-btn">
				                            <button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
				                          </span>
				                        </div>

								</div>
							</div><br>
							<div class="row">
								<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-center">&nbsp;</div>
							</div>
							<div class="row">
								<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 text-right">Fecha Fin</div>
								<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 text-center">
									<div class="input-group date date-picker" data-provide="datepicker" data-date-format="dd-mm-yyyy"> 
				                         <input name="fecha_fin" type="text" id="fecha_fin" class="form-control" value="" maxlength="60">
				                          <span class="input-group-btn">
				                            <button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
				                          </span>
				                        </div>


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
