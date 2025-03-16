<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
require_once("funciones_importacion.php");
require_once("../nomina/lib/common.php");
require_once("../includes/phpexcel/Classes/PHPExcel.php");
require_once("../includes/phpexcel/Classes/PHPExcel/IOFactory.php");
require_once("../includes/dependencias.php");
$conexion = new bd($_SESSION['bd']);
// Inicializar variables
$success  = true;
$mensaje  = '';
// Validar posibles errores:
//$ignorar = $_POST['ignorar'];
if($_FILES['archivo']['name'] != '')
{
	$name	  = $_FILES['archivo']['name'];
	$tname 	  = $_FILES['archivo']['tmp_name'];
	$type 	  = $_FILES['archivo']['type'];

	if($type == 'application/vnd.ms-excel')
		$ext = 'xls'; // Extension excel 97
	else if($type == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
	{
		$ext = 'xlsx'; // Extension excel 2007 y 2010
	}else{
		if (function_exists('finfo_file')) 
		{
			$type  = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $tname);

			if($type == 'application/vnd.ms-excel'){
				$ext = 'xls'; // Extension excel 97
			}else if($type == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'){
				$ext = 'xlsx'; // Extension excel 2007 y 2010		
			}
		}
		if(!isset($ext))
		{
			$mensaje = "¡Error! Extensión de archivo inválida.";
			$success = false;
		}
	}

	if(isset($ext))
	{
		$xls         = 'Excel5';
		$xlsx        = 'Excel2007';
		// Creando el lector
		$objReader   = PHPExcel_IOFactory::createReader($$ext);
		// Cargamos el archivo
		$objPHPExcel = $objReader->load($tname);
		$objPHPExcel->setActiveSheetIndex(0);
		$mensaje     ="";
		$i           =2;
		$editadas    = 0;
		$nuevas      = 0;
		$total       = 0;
		$filas       = "";
		while($objPHPExcel->getActiveSheet()->getCell("B".$i)->getValue() != '')
		{
			$descripcion_funcion        = $objPHPExcel->getActiveSheet()->getCell("C".$i)->getCalculatedValue();
			$funcion          = array(
			'nomfuncion_id'   => $objPHPExcel->getActiveSheet()->getCell("B".$i)->getCalculatedValue(),
			'descripcion_funcion' => $descripcion_funcion
			);
			//=====================================================================================================
			## Registro del empleado
			$res = $conexion->query("SELECT nomfuncion_id FROM nomfuncion WHERE nomfuncion_id = '".$funcion['nomfuncion_id']."'");
			echo "SELECT nomfuncion_id FROM nomfuncion WHERE nomfuncion_id = '".$funcion['nomfuncion_id']."'";
			echo "<br>";
			if ($pos = mysqli_fetch_array($res))
			{
				$sql1="UPDATE nomfuncion SET descripcion_funcion = '".$funcion['descripcion_funcion']."' WHERE nomfuncion_id = '".$funcion['nomfuncion_id']."'";
				$editadas++;
			}else{
				$sql1="INSERT INTO nomfuncion (nomfuncion_id,descripcion_funcion) 
				VALUES ('".$funcion['nomfuncion_id']."','".$funcion['descripcion_funcion']."')";
				$nuevas++;
			}
			$res = $conexion->query($sql1);
			$i++;
			$total++;
		}
	}
}else{
	$mensaje = "Error al cargar el archivo";
	$success = false;
}
?>
<div class="row">
	<div class="col-sm-8 col-md-8 col-lg-8 col-sm-offset-2 col-md-offset-2 col-lg-offset-2">
		<div class="portlet box blue">
			<div class="portlet-title">
				<div class="caption">
					RESULTADO DE IMPORTACION
				</div>
				<div class="actions">
	                <a class="btn btn-sm blue"  onclick="javascript: window.location='importar_funcion.php'">
	                  <i class="fa fa-arrow-left"></i> REGRESAR
	                </a>
              	</div>
			</div>
			<div class="portlet-body form" id="blockui_portlet_body">
				<div class="form-body">
					<div class="row">
						<div class="form-group" style="padding-left: 5%;padding-right: 5%;text-align:right;">
							<label class="col-sm-5 col-md-5 col-lg-5 control-label">FUNCIONES NUEVAS:</label>
							<div class="col-sm-7 col-md-7 col-lg-7">
								<input type="text" class="form-control input-sm" value="<?php echo $nuevas; ?>" readonly>
							</div>
						</div>
						<br>

						<div class="form-group" style="padding-left: 5%;padding-right: 5%;text-align:right;">
							<label class="col-sm-5 col-md-5 col-lg-5 control-label">FUNCIONES MODIFICADAS:</label>
							<div class="col-sm-7 col-md-7 col-lg-7">
								<input type="text" class="form-control input-sm" value="<?php echo $editadas; ?>" readonly>
							</div>
						</div>
						<br>

						<div class="form-group" style="padding-left: 5%;padding-right: 5%;text-align:right;">
							<label class="col-sm-5 col-md-5 col-lg-5 control-label">REGISTROS PROCESADOS:</label>
							<div class="col-sm-7 col-md-7 col-lg-7">
								<input type="text" class="form-control input-sm" value="<?php echo $total; ?>" readonly>
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