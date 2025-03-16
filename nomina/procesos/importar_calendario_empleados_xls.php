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
date_default_timezone_set('America/Panama');
$conexion = new bd($_SESSION['bd']);
// Inicializar variables
$success  = true;
$mensaje  = '';
	//echo "0";

// Validar posibles errores:
//$ignorar = $_POST['ignorar'];
if($_FILES['archivo']['name'] != '')
{
	//echo "1";
	//print_r($_FILES);
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
	if ($ext == "") {
		$archivo_tmp = explode(".", $name);
		$nombre_archivo = $archivo_tmp[0];
		$ext = $archivo_tmp[1];
	}

	if(isset($ext))
	{
		//echo "2";

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
		$total       = $act = $upd = 0;
		$filas       = "";
		$i_fila      = 1;
		$j_columna   = "A";
		$i_fecha = $i_fila;
		$i_fila++;//Se pasa a la siguiente  fila

		while($objPHPExcel->getActiveSheet()->getCell("B".$i_fila)->getValue() != '')
		{	
			$ficha    = $objPHPExcel->getActiveSheet()->getCell(("B".$i_fila))->getValue();
			$j_columna   = "C";

			while ( $objPHPExcel->getActiveSheet()->getCell($j_columna.$i_fila) != '') 
			{
				$turno    = $objPHPExcel->getActiveSheet()->getCell(($j_columna.$i_fila))->getValue();				
				 $cell    = $objPHPExcel->getActiveSheet()->getCell(($j_columna.$i_fecha));
				$InvDate = $cell->getValue();
				if(PHPExcel_Shared_Date::isDateTime($cell)) 
				{
				$fecha = \PHPExcel_Style_NumberFormat::toFormattedString($InvDate, 'YYYY-MM-DD');

				}		
				$j_columna++;
				$conexion = new bd($_SESSION['bd']);
				$sql_tn     = "SELECT dia_fiesta from nomcalendarios_tiposnomina where fecha = '".$fecha."' ;";
				$result_tn  = $conexion->query($sql_tn);
				$dia_fiesta = $result_tn->fetch_assoc();
				$sql        = "SELECT ficha from nomcalendarios_personal where fecha = '".$fecha."' AND ficha = '".$ficha."'";
				$result     = $conexion->query($sql);
				if($result->num_rows > 0)
				{
					$conexion = new bd($_SESSION['bd']);
					$update_s = "UPDATE nomcalendarios_personal 
					SET turno_id = '".$turno."' , 
					dia_fiesta = '".$dia_fiesta["dia_fiesta"]."' 
					WHERE fecha = '".$fecha."' 
					AND ficha = '".$ficha."'";
					$total++;				
					$resu        = $conexion->query($update_s);
					if ($resu) {
						$act++;
					}
				}
				else
				{
					$conexion = new bd($_SESSION['bd']);
					$insert_s = "INSERT INTO nomcalendarios_personal (fecha,turno_id,ficha,dia_fiesta) 
						     VALUES ('".$fecha."','".$turno."','".$ficha."','".$dia_fiesta["dia_fiesta"]."')";
					$total++;				
					$resi        = $conexion->query($insert_s);
					if ($resi) {
						$upd++;
					}

				}
			}
			$i_fila++;
		}		
	}
}else{
	$mensaje = "Error al cargar el archivo";
	$success = false;
}
if(!success) 
{ 
	echo '
	<div class="row">
		<div class="col-sm-8 col-md-8 col-lg-8 col-sm-offset-2 col-md-offset-2 col-lg-offset-2">
			<div class="alert-danger">HUBO UN ERROR AL CARGAR SU ARCHIVO</div>
		</div>
	</div>
';
}
else
{
	echo '
	<div class="row">
		<div class="col-sm-8 col-md-8 col-lg-8 col-sm-offset-2 col-md-offset-2 col-lg-offset-2">
			<div class="alert-success">ARCHIVO IMPORTADO CON ÉXITO</div>
		</div>
	</div>';
} ?>
<div class="row">
	<div class="col-sm-8 col-md-8 col-lg-8 col-sm-offset-2 col-md-offset-2 col-lg-offset-2">
		<div class="portlet box blue">
			<div class="portlet-title"> 
				<div class="caption"> 
					RESULTADO DE IMPORTACION 
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
							<label class="col-sm-5 col-md-5 col-lg-5 control-label">REGISTROS INSERTADOS:</label>
							<div class="col-sm-7 col-md-7 col-lg-7">
								<input type="text" class="form-control input-sm" value="<?php echo $upd; ?>" readonly>
							</div>
						</div>
						<br>

						<div class="form-group" style="padding-left: 5%;padding-right: 5%;text-align:right;">
							<label class="col-sm-5 col-md-5 col-lg-5 control-label">REGISTROS MODIFICADOS:</label>
							<div class="col-sm-7 col-md-7 col-lg-7">
								<input type="text" class="form-control input-sm" value="<?php echo $act; ?>" readonly>
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
