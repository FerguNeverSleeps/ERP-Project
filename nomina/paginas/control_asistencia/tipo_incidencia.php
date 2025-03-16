<?php
error_reporting(E_ALL ^ E_DEPRECATED);
require_once "config/db.php";
require_once "libs/importar_archivos.php";

//-------------------------------fecha_c--------------------------------
$conexion =  mysqli_connect( $params['host'], $params['user'], $params['password'], $params['dbname'] ) or
        die( 'Could not open connection to server' );
mysqli_query($conexion, 'SET CHARACTER SET utf8');

if (isset($_POST['enviar_form']))
{
	$codigo      = $_POST['codigo'];
	$descripcion = $_POST['descripcion'];
	$acronimo    = $_POST['acronimo'];
	$tipo        = $_POST['tipo'];
	switch ($_POST['accion']) {
		case 1:
			$sql="INSERT INTO caa_incidencias (codigo,descripcion,acronimo,tipo) 
				VALUES ('".$codigo."','".$descripcion."','".$acronimo."','".$tipo."')";
			$res = $conexion->query($sql) or die(mysqli_error($conexion));
			header("location:list_incidencias.php?msj=1");
			break;
		case 2:
			$id = $_POST['id'];
			$sql="UPDATE caa_incidencias SET codigo = '".$codigo."', descripcion = '".$descripcion."', acronimo = '".$acronimo."', tipo = '".$tipo."' WHERE id = '".$id."'";
			$res = $conexion->query($sql) or die(mysqli_error($conexion));
			header("location:list_incidencias.php?msj=2");
			break;
		case 3:
			$id = $_POST['id'];
			$sql="DELETE FROM caa_incidencias WHERE id ='$id'";
			$res = $conexion->query($sql) or die(mysqli_error($conexion));
			header("location:list_incidencias.php?msj=3");
			break;
		default:
			# code...
			break;
	}
}
switch ($_GET['tip']) {
	case 1:
		$titulo = "Registrar Incidencia";
		break;
	case 2:
		$sql="SELECT * FROM caa_incidencias WHERE id = '".$_GET['id']."'";
		$res = $conexion->query($sql) or die(mysqli_error($conexion));
		$tip = mysqli_fetch_array($res);
		$titulo = "Editar Incidencia";
		break;
	case 3:
		$sql="SELECT * FROM caa_incidencias WHERE id = '".$_GET['id']."'";
		$res = $conexion->query($sql) or die(mysqli_error($conexion));
		$tip = mysqli_fetch_array($res);
		$titulo = "Eliminar Incidencia";
		break;
	default:
		# code...
		break;
}
function tipo_inc($tipo)
{
	switch ($tipo) {
		case 1:
			return "NORMAL";
			break;
		case 2:
			return "JUSTIFICACION";
			break;
		case 3:
			return "APROBACION";
			break;
		case 4:
			return "PERMISOS";
			break;
		default:
			return "NORMAL";
			break;
	}
}
?>
<?php include("vistas/layouts/header.php"); ?>
<body class="page-header-fixed page-full-width" marginheight="0">
<script type="text/javascript">
function ConfirmDemo() {
//Ingresamos un mensaje a mostrar
var mensaje = confirm("¿Seguro desea Eliminar el registro?");
}
</script>
<div class="page-container">
<!-- BEGIN CONTENT -->
<div class="page-content-wrapper">
	<div class="page-content">
		<!-- BEGIN PAGE CONTENT-->
		<div class="row">
			<div class="col-sm-8 col-md-8 col-lg-8">
	          <?php
	              if(!empty($flash_message))
	              {
	                ?> <div class="alert <?php echo $flash_class ; ?>"><button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button><?php echo $flash_message; ?></div> <?php
	              }
	          ?>
				<!-- BEGIN EXAMPLE TABLE PORTLET-->
				<div class="portlet box blue">
					<div class="portlet-title">
						<div class="caption">
							<?php echo $titulo; ?>
						</div>
						<div class="actions">
			                <a class="btn btn-sm blue"  onclick="javascript: window.location='list_incidencias.php'">
			                  <i class="fa fa-arrow-left"></i> Regresar
			                </a>
		              	</div>
					</div>
					<div class="portlet-body form" id="blockui_portlet_body">
						<form action="tipo_incidencia.php" class="form-horizontal" method="post" enctype="multipart/form-data" style="margin-bottom: 5px;">
							<div class="form-body">
								<div class="row">
									<input type="hidden" name="accion" value="<?php echo $_GET['tip']; ?>">
									<input type="hidden" name="id" value="<?php echo $_GET['id']; ?>">
									<div class="form-group" style="padding-left: 5%;padding-right: 5%;">
										<label class="col-sm-4 col-md-4 col-lg-4 control-label" for="">CODIGO:</label>
										<div class="col-sm-8 col-md-8 col-lg-8">
											<input type="text" class="form-control input-sm" 
										       id="codigo" name="codigo" value="<?php echo (isset($tip['codigo'])) ? $tip['codigo']:""; ?>" <?php echo ($_GET['tip'] > 2) ? "readonly":"";?>>
										</div>
									</div>

									<div class="form-group" style="padding-left: 5%;padding-right: 5%;">
										<label class="col-sm-4 col-md-4 col-lg-4 control-label" for="">DESCRIPCION:</label>
										<div class="col-sm-8 col-md-8 col-lg-8">
											<input type="text" class="form-control input-sm" 
										       id="descripcion" name="descripcion" value="<?php echo (isset($tip['descripcion'])) ? $tip['descripcion']:""; ?>" <?php echo ($_GET['tip'] > 2) ? "readonly":"";?>>
										</div>
									</div>

									<div class="form-group" style="padding-left: 5%;padding-right: 5%;">
										<label class="col-sm-4 col-md-4 col-lg-4 control-label" for="">ACRONIMO:</label>
										<div class="col-sm-8 col-md-8 col-lg-8">
											<input type="text" class="form-control input-sm" 
										       id="acronimo" name="acronimo" value="<?php echo (isset($tip['acronimo'])) ? $tip['acronimo']:""; ?>" <?php echo ($_GET['tip'] > 2) ? "readonly":"";?>>
										</div>
									</div>

									<div class="form-group" style="padding-left: 5%;padding-right: 5%;">
										<label class="col-sm-4 col-md-4 col-lg-4 control-label" for="">TIPO:</label>
										<div class="col-sm-8 col-md-8 col-lg-8">
											<?php if ($_GET['tip'] <= 2): ?>
											<select name="tipo" id="tipo" class="form-control input-sm">
												<?php if (isset($tip['codigo'])): ?>
												<option value="<?php echo $tip['codigo'] ?>"><?php echo tipo_inc($tip['tipo']) ?></option>
												<?php else: ?>
												<option value="">SELECCIONE</option>
												<?php endif ?>												
												<option value="1">NORMAL</option>
												<option value="2">JUSTIFICACION</option>
												<option value="3">APROBACION</option>
												<option value="4">PERMISOS</option>
											</select>
											<?php else: ?>
											<input type="text" name="tipo" id="tipo" class="form-control input-sm" value="<?php echo tipo_inc($tip['tipo']); ?>">
											<?php endif ?>
										</div>
									</div>
								</div>

								<div class="text-center">
									<?php if ($_GET['tip'] == 1) { ?>
									<input type="reset" class="btn btn-sm blue active" value="Limpiar">
									<input type="submit" class="btn btn-sm blue active" name="enviar_form" value="Agregar">
									<?php }elseif ($_GET['tip'] == 2) { ?>
									<input type="reset" class="btn btn-sm blue active" value="Limpiar">
									<input type="submit" class="btn btn-sm blue active" name="enviar_form" value="Editar">
									<?php }elseif ($_GET['tip'] == 3) { ?>
									<input type="submit" class="btn btn-sm blue active" name="enviar_form" value="Eliminar">
									<?php } ?>
								</div>

							</div>
						</form>
					</div>
				</div>
				<!-- END EXAMPLE TABLE PORTLET-->
			</div>
		</div>
		<!-- END PAGE CONTENT-->
	</div>
</div>
<!-- END CONTENT -->
</div>
<?php include("vistas/layouts/footer.php"); ?>
<script>$('select').selectpicker();</script>
<script src="web/js/agregar_movimiento_reloj.js?<?php echo time(); ?>" type="text/javascript"></script>

</body>
</html>