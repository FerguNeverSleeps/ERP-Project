<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
require_once("../../procesos/funciones_importacion.php");
require_once("../../nomina/lib/common.php");
require_once("../../includes/phpexcel/Classes/PHPExcel.php");
require_once("../../includes/phpexcel/Classes/PHPExcel/IOFactory.php");
require_once("../../includes/dependencias.php");
$conexion = new bd($_SESSION['bd']);
$success  = true;
$mensaje  = '';
$ficha = ($_POST['ficha1']) ? $_POST['ficha1'] : '' ;
$ficha_nueva = ($_POST['ficha2']) ? $_POST['ficha2'] : '' ;

$consulta     ="";
$empleado     = array(
'ficha'       => $ficha,
'ficha_nueva' => $ficha_nueva
);

$consulta   = "UPDATE nompersonal SET
ficha       = '".$empleado['ficha_nueva']."'
WHERE ficha = '".$empleado['ficha']."'; ";
$res = $conexion->query($consulta);
//echo $consulta, "<br>";

$consulta   = "UPDATE reloj_detalle SET
ficha       = '".$empleado['ficha_nueva']."'
WHERE ficha = '".$empleado['ficha']."'; ";
//echo $consulta, "<br>";
$res = $conexion->query($consulta);

$consulta   = "UPDATE nom_movimientos_nomina SET
ficha       = '".$empleado['ficha_nueva']."'
WHERE ficha = '".$empleado['ficha']."'; ";
//echo $consulta, "<br>";
$res = $conexion->query($consulta);

$consulta   = "UPDATE nom_nomina_netos SET
ficha       = '".$empleado['ficha_nueva']."'
WHERE ficha = '".$empleado['ficha']."'; ";
//echo $consulta, "<br>";
$res = $conexion->query($consulta);

$consulta   = "UPDATE caa_archivos_datos SET
ficha       = '".$empleado['ficha_nueva']."'
WHERE ficha = '".$empleado['ficha']."'; ";
//echo $consulta, "<br>";
$res = $conexion->query($consulta);

$consulta   = "UPDATE caa_incidencias_empleados SET
ficha       = '".$empleado['ficha_nueva']."'
WHERE ficha = '".$empleado['ficha']."'; ";
//echo $consulta, "<br>";
$res = $conexion->query($consulta);

$consulta   = "UPDATE caa_justificacion SET
ficha       = '".$empleado['ficha_nueva']."'
WHERE ficha = '".$empleado['ficha']."'; ";
//echo $consulta, "<br>";
$res = $conexion->query($consulta);

$consulta   = "UPDATE caa_periodos SET
ficha       = '".$empleado['ficha_nueva']."'
WHERE ficha = '".$empleado['ficha']."'; ";
//echo $consulta, "<br>";
$res = $conexion->query($consulta);

$consulta   = "UPDATE caa_registros SET
ficha       = '".$empleado['ficha_nueva']."'
WHERE ficha = '".$empleado['ficha']."'; ";
//echo $consulta, "<br>";
$res = $conexion->query($consulta);

$consulta   = "UPDATE caa_resumen SET
ficha       = '".$empleado['ficha_nueva']."'
WHERE ficha = '".$empleado['ficha']."'; ";
//echo $consulta, "<br>";
$res = $conexion->query($consulta);

$consulta   = "UPDATE nomfamiliares SET
ficha       = '".$empleado['ficha_nueva']."'
WHERE ficha = '".$empleado['ficha']."'; ";
//echo $consulta, "<br>";
$res = $conexion->query($consulta);

$consulta   = "UPDATE nompersonal_constancias SET
ficha       = '".$empleado['ficha_nueva']."'
WHERE ficha = '".$empleado['ficha']."'; ";
//echo $consulta, "<br>";
$res = $conexion->query($consulta);

$consulta   = "UPDATE nomprestamos_cabecera SET
ficha       = '".$empleado['ficha_nueva']."'
WHERE ficha = '".$empleado['ficha']."'; ";
//echo $consulta, "<br>";
$res = $conexion->query($consulta);

$consulta   = "UPDATE nomprestamos_detalles SET
ficha       = '".$empleado['ficha_nueva']."'
WHERE ficha = '".$empleado['ficha']."'; ";
//echo $consulta, "<br>";
$res = $conexion->query($consulta);

$consulta   = "UPDATE nom_progvacaciones SET
ficha       = '".$empleado['ficha_nueva']."'
WHERE ficha = '".$empleado['ficha']."';";
//echo $consulta, "<br>";
$res = $conexion->query($consulta);
$total++;
$i++;

?>
<div class="row">
	<div class="col-sm-8 col-md-8 col-lg-8 col-sm-offset-2 col-md-offset-2 col-lg-offset-2">
		<div class="portlet box blue">
			<div class="portlet-title"> 
				<div class="caption"> 
					RESULTADO DE ACTUALIZACIÓN 
				</div> 
				<div class="actions">
	                <a class="btn btn-sm blue"  onclick="javascript: window.location='importar_calendario_empleados.php'">
	                  <i class="fa fa-arrow-left"></i> REGRESAR
	                </a>
              	</div>
			</div>
			<div class="portlet-body form" id="blockui_portlet_body">
				<div class="form-body">
					<div class="row">
						<div class="form-group" style="padding-left: 5%;padding-right: 5%;text-align:right;">
							<label class="col-sm-3 col-md-3 col-lg-3 control-label">FICHA ANTERIOR:</label>
							<div class="col-sm-2 col-md-2 col-lg-2">
								<input type="text" class="form-control input-sm" value="<?php echo $empleado['ficha']; ?>" readonly>
							</div>
							<label class="col-sm-3 col-md-3 col-lg-3 control-label">FICHA NUEVA:</label>
							<div class="col-sm-2 col-md-2 col-lg-2">
								<input type="text" class="form-control input-sm" value="<?php echo $empleado['ficha_nueva']; ?>" readonly>
							</div>
						</div>
						<br>
					</div>
				</div>
			</div>
		</div>
		<!-- END EXAMPLE TABLE PORTLET-->
	</div>
</div>