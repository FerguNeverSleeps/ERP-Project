
<?php 
session_start();	
include ("../header4.php");
include("../lib/common.php");
include("func_bd.php");	
?>
<script>
function generar_reporte()
{
 	var fechaini = document.form1.mesano.value;  
 	var fechafin = document.form1.mesano1.value;  

  

	//alert('Codnom '+codnom+' Codtip '+codtip+ ' Departamento '+ck_dep+' Deduccion '+ck_ded);
	if(fechaini  != ''  && fechafin != '' )
	{
		
		location.href='rpt_acreedor.php?fechainicio='+fechaini+'&fechafinal='+fechafin ; 
	}
	else
	{
	 
		alert('Por favor, seleccione las fechas');	
	}
}
</script>
<form id="form1" name="form1" method="post" action="excel_reporte_anos_labor.php">
<div class="page-container">
	<div class="page-wrapper-containter">
		<div class="page-content">
			<div class="row">
				<div class="col-md-12">
					<div class="portlet box blue">
						<div class="portlet-title">
							<div class="caption">
							Parámetros del Reporte años de labor
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
							<!-- <div class="row">
								<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 text-right">Fecha Inicio</div>
								<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 text-center">
									<div class="input-group date date-picker" data-provide="datepicker" data-date-format="dd-mm-yyyy"> 
				                         <input name="mesano" type="text" id="mesano" class="form-control" value="" maxlength="60">
				                          <span class="input-group-btn">
				                            <button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
				                          </span>
				                        </div>


								</div>
							</div><br> -->
							<div class="row">
								<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-center">&nbsp;</div>
							</div>
							<div class="row">
								<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 text-right">Fecha Fin</div>
								<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 text-center">
									<div class="input-group date date-picker" data-provide="datepicker" data-date-format="dd-mm-yyyy"> 
				                         <input name="mesano1" type="text" id="mesano1" class="form-control" value="" maxlength="60">
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
                                    <div class="row">
                                     <input type="submit" value="Generar reporte" class="btn blue">
                                    </div>
                                        <!-- <div class="btn blue" onClick="javascript:generar_reporte();"><i class="fa fa-download"></i> Generar Reporte</div> -->
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

</body>
<?php
include ("../footer4.php");

 ?>
</html>

