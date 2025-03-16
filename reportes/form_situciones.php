<?php 
include("../includes/dependencias2.php");
require_once('../nomina/lib/database.php');

$db = new Database($_SESSION['bd']);
$res = $db->query("SELECT * FROM nomsituaciones");
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
							<form class="form-horizontal" method="post" action="excel/funcionarios_situacion.php">
								<div class="form-body">
									<div class="form-group">
										<label class="col-md-5 control-label">Situacion:</label>
										<div class="col-md-7">
											<select name="situacion" id="situacion" class="form-control" required>
												<option value="Seleccione">Seleccione</option>
												<?php
												while ($sit = mysqli_fetch_array($res)){
												echo '<option value="'.$sit['situacion'].'">'.$sit['situacion'].'</option>';
												}
												?>
											</select>
										</div>
									</div>
									<div class="form-group">
										<div align="center" >
											<a href="../nomina/paginas/submenu_reportes_integrantes.php"></a>
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