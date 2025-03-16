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
				<div class="col-md-12">
					<div class="portlet box blue">
						<div class="portlet-title">
							<div class="caption">
								Reporte de Anexo 03
							</div>
							<div class="actions">

								<a class="btn btn-sm blue"  onclick="javascript: window.location='submenu_reportes.php?modulo=45'">
									<i class="fa fa-arrow-left"></i> Regresar
								</a>

							</div>
						</div>
						<div class="portlet-body" id="blockui_portlet_body">
							<div class="row">
								<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-center">&nbsp;</div>
							</div>
							<div class="row">
								<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 text-right">Fecha Inicio</div>
								<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 text-center">
									<div class="input-group date date-picker" data-provide="datepicker" data-date-end-date="0d" data-date-format="dd/mm/yyyy"> 
				                         <input name="fecha_inicio" type="text" id="fecha_inicio" class="form-control" value="<?php echo "01/01/".date("Y") ?>" maxlength="60">
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
									<div class="input-group date date-picker" data-provide="datepicker" data-date-end-date="0d" data-date-format="dd/mm/yyyy"> 
				                         <input name="fecha_fin" type="text" id="fecha_fin" class="form-control" value="<?php echo date("d/m/Y") ?>" maxlength="60">
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
									<div class="btn blue" id="boton_generar"><i class="fa fa-download"></i> Generar Reporte</div>
									
									<?php
										if(isset($_GET['download']) && isset($_GET['filename']))
										{ 
										?>
											<a href="<?php echo $_GET['filename']; ?>" class="btn btn-sm red" id="a_download">
											<i class="fa fa-download"></i> Descargar</a>
										<?php
										}
									?>	
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
 
<script>
$(document).ready(function(){
	var codnom = $("#codnom").val();
	if( $("#a_download").length ) 
	{
		document.getElementById("a_download").click();
	}
	if( $("#a_download").length ) 
	{
		$("#a_download").hide();	
	}
	$("#boton_generar").on('click',function (){
		desglose();
	});
	function desglose()
	{
		/*var fecha_inicio = document.form1.fecha_inicio.value; // PK nomtipos_nomina
		var fecha_fin = document.form1.fecha_fin.value; // PK nomtipos_nomina
		var tipo_planilla = document.form1.tipo_planilla.value; // PK nomtipos_nomina*/
		
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
				//
				App.blockUI({
					target: '#blockui_portlet_body',
					boxed: true,
					message: 'Generando Reporte...',
				});	
				
				mesano =  document.form1.fecha_inicio.value;
				mesano1 =  document.form1.fecha_fin.value;
				codtip =  document.form1.tipo_planilla.value;
				location.href ='rpt_anexo03_preelaborada_xls.php?codtip='+codtip+'&mesano='+mesano+'&mesano1='+mesano1;

				$.get( "rpt_anexo03_preelaborada_xls.php", { codtip:codtip, mesano:mesano, mesano1:mesano1 }, function(file) {
					console.log(file);
					App.unblockUI('#blockui_portlet_body');

					window.setTimeout(function () {
						//location.href = 'config_rpt_anexo03.php';
						location.href = 'config_rpt_xiii_mes.php?download&filename='+file;

					}, 2000); 

				}); 
			}
		}
		//AbrirVentana('../../reportes/pdf/pdf_estimado_liquidaciones.php');
	}
});
</script>
</html>
