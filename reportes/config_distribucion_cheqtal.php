<?php
	session_start();
	ob_start();
	date_default_timezone_set('America/Panama');

	require_once('../nomina/lib/database.php');
	$db = new Database($_SESSION['bd']); 

	include("../includes/dependencias2.php");
	
	$db = new Database($_SESSION['bd']);
	$sql = "SELECT codnom,descrip,codtip FROM nom_nominas_pago WHERE codtip='".$_SESSION['codigo_nomina']."'";
	$res2 = $db->query($sql);
	
	if(mysqli_num_rows($res2)==0)
	{
		echo '<div class="alert alert-danger">Se debe cerrar al menos una planilla para realizar esta operacion..!!</div>';
	}
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
								<label>Seleccione una Planilla</label>
							</div>
							<div class="pull-right">
								<a class="btn btn-primary" href="../nomina/paginas/submenu_reportes.php?modulo=45">
									<i class="fa fa-arrow-left"></i>
									Atras
								</a>
							</div>
						</div>
						<div class="portlet-body form">
							<form class="form-horizontal" id="formPrincipal" name="formPrincipal" method="post" role="form" action="pdf/pdf_distribucion_cheqtal.php">
								<div class="form-body">
									<div class="form-group">
										<label class="col-md-3 control-label">Planilla :</label>
										<div class="col-md-9">
											<select name="planilla" id="planilla" class="form-control">
												<option value="">Seleccione</option>
												<?php while ($fila=mysqli_fetch_array($res2)): ?>
												<option value="<?= $fila['codnom'] ?>"><?= $fila['descrip'] ?></option>	
												<?php endwhile ?>
											</select>
										</div>
									</div>
									<div class="form-group">
										<div align="center" >
											<input name="enviar" class="btn btn-primary" value="Generar" type="submit">
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