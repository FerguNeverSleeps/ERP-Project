<?php 
session_start();
ob_start();
$termino=$_SESSION['termino'];
$ficha=$_GET['ficha'];
$tipnom=$_GET['tipnom'];
?>
<?php
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // Always modified
header("Cache-Control: private, no-store, no-cache, must-revalidate"); // HTTP/1.1 
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache"); // HTTP/1.0
?>

<?php
include ("../header.php");
include("../lib/common.php");
include("func_bd.php");	
?>
<script>
function AbrirListPersonal()
{
AbrirVentana('list_personal.php',660,800,0);
}
</script>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Resuelto</title>
</head>
<body>
	<div class="container">
	<div class="row">
		<br><br><br><br>
		<div class="col-sm-12 col-md-12 col-lg-12">
			<div class="hidden-xs col-sm-2 col-md-2 col-lg-2"></div>
			<div class="row">
				<div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
					<div class="panel panel-default">
						<div class="panel-heading">
							<div class="row">
								<div class="col-xs-12 col-sm-10 col-md-10 col-md-10 pull-left">
									<h3><p>Parametros de la resolucion</p></h3>
								</div>
								<div class="col-xs-12 col-sm-2 col-md-2 col-md-2 pull-right">
									<br>
									<a href="list_personal.php"><input type="button" class="btn btn-primary" value="Atras"></a>
								</div>
							</div>
						</div>
						<div class="panel-body">
							<form action="cartas_trabajo/resuelto_minsa_pdf.php" method="post">
							<div class="row">
								<div class="form-group">
									<div class="col-xs-12 col-sm-6 col-md-6 col-md-6 pull-left">
										<label><h3>Resuelto numero:</h3></label>
									</div>
									<div class="col-xs-12 col-sm-6 col-md-6 col-md-6 pull-right">
										<input type="hidden" name="ficha" value="<?php echo $ficha; ?>">
										<input type="hidden" name="tipnom" value="<?php echo $tipnom; ?>">
										<input type="text" class="input input-lg form-control" name="num_res" placeholder="Numero de resolucion" required>
									</div>
								</div>
							</div>
							<hr>
							<div class="row">
								<div class="form-group">
									<div class="col-xs-12 col-sm-6 col-md-6 col-md-6">
										<input type="submit" class="btn btn-primary form-control" value="Generar">
									</div>
								</div>
							</div>
							</form>
						</div>
					</div>	
				</div>
			</div>
			<div class="hidden-xs col-sm-2 col-md-2 col-lg-2"></div>
		</div>
	</div>
	</div>	
</body>
</html>