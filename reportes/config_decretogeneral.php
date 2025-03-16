<?php include("../includes/dependencias2.php"); // <html><head></head><body> 
if( isset($_POST['enviar']) )
{
	$pd = $_POST['pd'];
	$nd = $_POST['nd'];
	require_once('../nomina/lib/database.php');
	$db = new Database($_SESSION['bd']);
	$sql = "SELECT num_decreto FROM nompersonal WHERE num_decreto = '$nd'";
	$res2 = $db->query($sql);
	if(mysqli_num_rows($res2)>0){
		header("location:pdf/decreto_general_minsa_pdf.php?pd=$pd&nd=$nd");
	}
	else
	{
		echo"<SCRIPT>alert('El numero de decreto no se encuentra, por favor verifique e intente de nuevo');
			 		 window.location.replace('config_resueltogeneral.php');</SCRIPT>";
	}
}
else
{
?>
<div class="page-container">
	<!-- BEGIN SIDEBAR -->
	<!-- END SIDEBAR -->
	<!-- BEGIN CONTENT -->
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
								<label>Parametros del Reporte - Decreto General</label>
							</div>
						</div>
						<div class="portlet-body form">
							<form class="form-horizontal" id="formPrincipal" name="formPrincipal" method="post" role="form" action="">
								<div class="form-body">
									<div class="form-group">
										<label class="col-md-5 control-label">P.D.</label>
										<div class="col-md-7">
											<input name="pd"  class="form-control" type="text" min="0" id="pd" maxlength="10" required>
										</div>	
									</div>
									<div class="form-group">
										<label class="col-md-5 control-label">Numero de Decreto</label>
										<div class="col-md-7">
											<input name="nd"  class="form-control" type="text" min="0" id="nd" maxlength="10" required>
										</div>	
									</div> <!--
									<div class="form-group">
										<label class="col-md-5 control-label">Fecha del Decreto:</label>
										<div class="col-md-7">
											<div class="input-group date date-picker" data-provide="datepicker" data-date-end-date="0d">
												<input name="fecha_con"  class="form-control" type="text" id="fecha_con" required>
												<span class="input-group-btn">
													<button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
												</span>
											</div>
										</div>	
									</div> -->
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
<?php } ?>
