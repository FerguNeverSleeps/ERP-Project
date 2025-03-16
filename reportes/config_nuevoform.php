<?php include("../includes/dependencias2.php"); // <html><head></head><body> 
if( isset($_POST['enviar']) )
{
	$pd = $_POST['pd'];
	$nd = $_POST['nd'];
	$date = date_create($_POST['fecha']);
	$fecha = date_format($date,'Y-m-d');
	$ficha = $_POST['ficha'];
	require_once('../nomina/lib/database.php');
	$db = new Database($_SESSION['bd']);
	$sql = "SELECT * FROM nompersonal WHERE num_decreto = '$nd' AND ficha='$ficha' AND fecha_decreto='$fecha'";
	$res = $db->query($sql);
	$valor1 = mysqli_num_rows($res);

	if($valor1>0){
		header("location:pdf/decreto.php?pd=$pd&nd=$nd&fecha=$fecha");
	}
	else
	{
			echo"<SCRIPT>alert('No hay datos asociados a los registros ingresados, por favor verifique e intente de nuevo');window.location.replace('config_nuevoform.php');</SCRIPT>";
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
								<label>Parametros del Reporte</label>
							</div>
						</div>
						<div class="portlet-body form">
							<form class="form-horizontal" method="post" action="word/decreto_asamblea.php">
								<input type="hidden" name="ficha" value="<?php echo $_GET['ficha'] ?>">
								<div class="form-body">
									<div class="form-group">
										<label class="col-md-5 control-label">P.D.</label>
										<div class="col-md-7">
											<input name="pd"  class="form-control" type="number" id="pd" maxlength="10" required>
										</div>	
									</div>
									<div class="form-group">
										<label class="col-md-5 control-label">Numero de Decreto</label>
										<div class="col-md-7">
											<input name="nd"  class="form-control" type="number" id="pd" maxlength="10" required>
										</div>	
									</div>
									<div class="form-group">
										<label class="col-md-5 control-label">Fecha del Decreto:</label>
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
<?php } ?>
