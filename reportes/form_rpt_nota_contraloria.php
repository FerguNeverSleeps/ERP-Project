<?php 
include("../includes/dependencias2.php");
?>
<div class="page-container">
	<div class="page-content-wrapper">
		<div class="page-content">
			<!-- BEGIN PAGE CONTENT-->
			<div class="row">
				<div class="col-md-3"></div>
				<div class="col-md-6">
					<!-- BEGIN EXAMPLE TABLE PORTLET-->
					<div class="portlet box blue">
						<div class="portlet-title">
							<div class="caption">
								<label>Parametros del Documento</label>
							</div>
						</div>
						<div class="portlet-body form">
							<form class="form-horizontal" method="post" action="word/nota_contraloria_asamblea.php">
								<input type="hidden" name="ficha" value="<?php echo $_GET['ficha'] ?>">
								<input type="hidden" name="tipnom" value="<?php echo $_GET['tipnom'] ?>">
								<div class="form-body">
									<div class="form-group">
										<label class="col-md-5 control-label">Quincena:</label>
										<div class="col-md-7">
											<select name="quincena" id="quincena" class="form-control" required>
												<option value="Seleccione">Seleccione</option>
												<option value="primera quincena">1ra Quincena</option>
												<option value="segunda quincena">2da Quincena</option>
											</select>
										</div>	
									</div>
									<div class="form-group">
										<label class="col-md-5 control-label">Fecha de ejecucion de Tramite:</label>
										<div class="col-md-7">
											<div class="input-group date date-picker" data-provide="datepicker" data-date-end-date="0d">
												<input name="fecha"  class="form-control" type="text" id="fecha" required>
												<span class="input-group-btn">
													<button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
												</span>
											</div>
										</div>	
									</div>
									<div class="form-group">
										<div align="center" >
												<input name="enviar" class="btn btn-primary" type="submit">
										</div>	
									</div>
									
								</div>
							</form>
						</div>
					</div>
					<!-- END EXAMPLE TABLE PORTLET-->
				</div>
				<div class="col-md-3"></div>
			</div>
			<!-- END PAGE CONTENT-->
		</div>
	</div>
	<!-- END CONTENT -->
</div>