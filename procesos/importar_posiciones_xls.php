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
		$xls  = 'Excel5';
		$xlsx = 'Excel2007';
		// Creando el lector
		$objReader = PHPExcel_IOFactory::createReader($$ext);
		// Cargamos el archivo
		$objPHPExcel = $objReader->load($tname);
		$objPHPExcel->setActiveSheetIndex(0);
		$numRows = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
		$mensaje="";
		$i=9;
		$bandera = 0;
		$editadas = 0;
		$nuevas   = 0;
		$total   = 0;
		$filas = "";
		$itera = TRUE;
		for ($i = 9; $i <= $numRows; $i++) 
		{
			if ($objPHPExcel->getActiveSheet()->getCell("J".$i)->getValue() != '') {
				$bandera = 0;
				$sueldos = array(
					'sueldo_propuesto' => $objPHPExcel->getActiveSheet()->getCell("M".$i)->getCalculatedValue(),
					'sueldo_1'         => $objPHPExcel->getActiveSheet()->getCell("M".$i)->getCalculatedValue(),
					'mes_1'            => $objPHPExcel->getActiveSheet()->getCell("N".$i)->getCalculatedValue(),
					'sueldo_2'         => $objPHPExcel->getActiveSheet()->getCell("O".$i)->getCalculatedValue(),
					'mes_2'            => $objPHPExcel->getActiveSheet()->getCell("P".$i)->getCalculatedValue(),
					'sueldo_3'         => $objPHPExcel->getActiveSheet()->getCell("Q".$i)->getCalculatedValue(),
					'mes_3'            => $objPHPExcel->getActiveSheet()->getCell("R".$i)->getCalculatedValue(),
					'sueldo_4'         => $objPHPExcel->getActiveSheet()->getCell("S".$i)->getCalculatedValue(),
					'mes_4'            => $objPHPExcel->getActiveSheet()->getCell("T".$i)->getCalculatedValue(),
					'sueldo_anual'     => $objPHPExcel->getActiveSheet()->getCell("M".$i)->getCalculatedValue()*12
				);
				$posiciones = array(
	            'nomposicion_id'                => $objPHPExcel->getActiveSheet()->getCell("J".$i)->getCalculatedValue(),
	            'cargo_id'                      => $objPHPExcel->getActiveSheet()->getCell("K".$i)->getCalculatedValue(),
	            'sueldo_propuesto'              => $sueldos['sueldo_propuesto'],

	            'sueldo_1'                      => $sueldos['sueldo_1'],
	            'mes_1'                         => $sueldos['mes_1'],
	            'sueldo_2'                      => $sueldos['sueldo_2'],
	            'mes_2'                         => $sueldos['mes_2'],
	            'sueldo_3'                      => $sueldos['sueldo_3'],
	            'mes_3'                         => $sueldos['mes_3'],
	            'sueldo_4'                      => $sueldos['sueldo_4'],
	            'mes_4'                         => $sueldos['mes_4'],

	        	'sueldo_anual'                  => $sueldos['sueldo_anual'],

	            'partida'                       => $objPHPExcel->getActiveSheet()->getCell("U".$i)->getCalculatedValue(),

	            'sobresueldo_otros_1'           => $objPHPExcel->getActiveSheet()->getCell("V".$i)->getCalculatedValue()/12,
	            'sueldo_propuesto_80'           => $objPHPExcel->getActiveSheet()->getCell("V".$i)->getCalculatedValue()/12,
	            'partida080'                    => $objPHPExcel->getActiveSheet()->getCell("W".$i)->getCalculatedValue(),
	            'mes_ot_1'                      => 12,

	            'gastos_representacion'         => $objPHPExcel->getActiveSheet()->getCell("X".$i)->getCalculatedValue()/12,
	            'sueldo_propuesto_30'           => $objPHPExcel->getActiveSheet()->getCell("X".$i)->getCalculatedValue()/12,
	            'partida030'                    => $objPHPExcel->getActiveSheet()->getCell("Y".$i)->getCalculatedValue(),
	            'mes_gasto_1'                   => 12,
	            
	            'dieta'                         => 0,
	            'partida011'                    => 0,

	            'combustible'                   => 0,
	            'partida012'                    => 0,


	            'sobresueldo_antiguedad_1'      => 0,
	            'sueldo_propuesto_11'           => 0,
	            'partida011'                    => 0,
	            'mes_ant_1'                     => 0,

	            'sobresueldo_zona_apartada_1'   => 0,
	            'mes_za_1'                      => 0,
	            'sueldo_propuesto_12'           => 0,

	            'sobresueldo_jefatura_1'        => $objPHPExcel->getActiveSheet()->getCell("Z".$i)->getCalculatedValue()/12,
	            'sueldo_propuesto_13'           => $objPHPExcel->getActiveSheet()->getCell("Z".$i)->getCalculatedValue()/12,
	            'partida013'                    => $objPHPExcel->getActiveSheet()->getCell("AA".$i)->getCalculatedValue(),
	            'mes_jef_1'                     => 12,

	            'sobresueldo_esp_1'             => 0,
	            'mes_esp_1'                     => 0,
	            'sueldo_propuesto_19'           => 0,
	            );
				//=====================================================================================================
				## Registro del empleado
				$res = $conexion->query("SELECT nomposicion_id FROM nomposicion WHERE nomposicion_id = '".$posiciones['nomposicion_id']."'");
				if ($pos = mysqli_fetch_array($res))
				{
					$sql1="UPDATE nomposicion SET 
						sueldo_propuesto = '".$posiciones['sueldo_propuesto']."',
						sueldo_1 = '".$posiciones['sueldo_1']."',
						mes_1 = '".$posiciones['mes_1']."',
						sueldo_2 = '".$posiciones['sueldo_2']."',
						mes_2 = '".$posiciones['mes_2']."',
						sueldo_3 = '".$posiciones['sueldo_3']."',
						mes_3 = '".$posiciones['mes_3']."',
						sueldo_4 = '".$posiciones['sueldo_4']."',
						mes_4 = '".$posiciones['mes_4']."',
						sueldo_anual = '".$posiciones['sueldo_anual']."',
						partida = '".$posiciones['partida']."',
						partida011 = '".$posiciones['partida011']."',
						partida012 = '".$posiciones['partida012']."',
						partida013 = '".$posiciones['partida013']."',
						partida019 = '".$posiciones['partida019']."',
						partida080 = '".$posiciones['partida080']."',
						partida030 = '".$posiciones['partida030']."',
						sobresueldo_antiguedad_1 = '".$posiciones['sobresueldo_antiguedad_1']."',
						mes_ant_1 = '".$posiciones['mes_ant_1']."',
						sueldo_propuesto_11 = '".$posiciones['sueldo_propuesto_11']."',
						sobresueldo_zona_apartada_1 = '".$posiciones['sobresueldo_zona_apartada_1']."',
						mes_za_1 = '".$posiciones['mes_za_1']."',
						sueldo_propuesto_12 = '".$posiciones['sueldo_propuesto_12']."',
						sobresueldo_jefatura_1 = '".$posiciones['sobresueldo_jefatura_1']."',
						mes_jef_1 = '".$posiciones['mes_jef_1']."',
						sueldo_propuesto_13 = '".$posiciones['sueldo_propuesto_13']."',
						sobresueldo_esp_1 = '".$posiciones['sobresueldo_esp_1']."',
						mes_esp_1 = '".$posiciones['mes_esp_1']."',
						sueldo_propuesto_19 = '".$posiciones['sueldo_propuesto_19']."',
						sobresueldo_otros_1 = '".$posiciones['sobresueldo_otros_1']."',
						mes_ot_1 = '".$posiciones['mes_ot_1']."',
						sueldo_propuesto_80 = '".$posiciones['sueldo_propuesto_80']."',
						gastos_representacion = '".$posiciones['gastos_representacion']."',
						mes_gasto_1 = '".$posiciones['mes_gasto_1']."',
						sueldo_propuesto_30 = '".$posiciones['sueldo_propuesto_30']."',
						cargo_id = '".$posiciones['cargo_id']."'
						WHERE nomposicion_id = '".$posiciones['nomposicion_id']."'";
					$editadas++;
				}else{
				  $sql1="INSERT INTO nomposicion (
					nomposicion_id,
					sueldo_propuesto,
					sueldo_1,
					mes_1,
					sueldo_2,
					mes_2,
					sueldo_3,
					mes_3,
					sueldo_4,
					mes_4,
					sueldo_anual,
					partida,
					partida011,
					partida012,
					partida013,
					partida019,
					partida080,
					partida030,
					sobresueldo_antiguedad_1,
					mes_ant_1,
					sueldo_propuesto_11,
					sobresueldo_zona_apartada_1,
					mes_za_1,
					sueldo_propuesto_12,
					sobresueldo_jefatura_1,
					mes_jef_1,
					sueldo_propuesto_13,
					sobresueldo_esp_1,
					mes_esp_1,
					sueldo_propuesto_19,
					sobresueldo_otros_1,
					mes_ot_1,
					sueldo_propuesto_80,
					gastos_representacion,
					mes_gasto_1,
					sueldo_propuesto_30,
					cargo_id ) VALUES (
					'".$posiciones['nomposicion_id']."',
					'".$posiciones['sueldo_propuesto']."',
					'".$posiciones['sueldo_1']."',
					'".$posiciones['mes_1']."',
					'".$posiciones['sueldo_2']."',
					'".$posiciones['mes_2']."',
					'".$posiciones['sueldo_3']."',
					'".$posiciones['mes_3']."',
					'".$posiciones['sueldo_4']."',
					'".$posiciones['mes_4']."',
					'".$posiciones['sueldo_anual']."',
					'".$posiciones['partida']."',
					'".$posiciones['partida011']."',
					'".$posiciones['partida012']."',
					'".$posiciones['partida013']."',
					'".$posiciones['partida019']."',
					'".$posiciones['partida080']."',
					'".$posiciones['partida030']."',
					'".$posiciones['sobresueldo_antiguedad_1']."',
					'".$posiciones['mes_ant_1']."',
					'".$posiciones['sueldo_propuesto_11']."',
					'".$posiciones['sobresueldo_zona_apartada_1']."',
					'".$posiciones['mes_za_1']."',
					'".$posiciones['sueldo_propuesto_12']."',
					'".$posiciones['sobresueldo_jefatura_1']."',
					'".$posiciones['mes_jef_1']."',
					'".$posiciones['sueldo_propuesto_13']."',
					'".$posiciones['sobresueldo_esp_1']."',
					'".$posiciones['mes_esp_1']."',
					'".$posiciones['sueldo_propuesto_19']."',
					'".$posiciones['sobresueldo_otros_1']."',
					'".$posiciones['mes_ot_1']."',
					'".$posiciones['sueldo_propuesto_80']."',
					'".$posiciones['gastos_representacion']."',
					'".$posiciones['mes_gasto_1']."',
					'".$posiciones['sueldo_propuesto_30']."',
					'".$posiciones['cargo_id']."'
					)";
					$nuevas++;
				}

				//Actualizar nompersonal

              //  $sql1="UPDATE `nompersonal` SET  `sueldopro`='".$posiciones['sueldo_propuesto']."',`suesal`='".$posiciones['sueldo_propuesto']."',`gastos_representacion`='".$posiciones['gastos_representacion']."',`otros`='".$posiciones['sueldo_propuesto_80']."' WHERE nomposicion_id = '".$posiciones['nomposicion_id']."' and  estado NOT LIKE '%Baja%'";//

                 $sql2="UPDATE `nompersonal` SET  `sueldopro`='".$posiciones['sueldo_propuesto']."',`suesal`='".$posiciones['sueldo_propuesto']."',`gastos_representacion`='".$posiciones['gastos_representacion']."',`otros`='".$posiciones['sueldo_propuesto_80']."',  estado ='REGULAR' WHERE nomposicion_id = '".$posiciones['nomposicion_id']."' and  estado ='REGULAR' OR  estado ='RESERVADO'";
				//
				$res = $conexion->query($sql1);
				$res2 = $conexion->query($sql2);
				$total++;

			}else{
				$bandera++;
			}
			if ($bandera > 100) {
				$i = $numRows;
			}
		}
	}
}else{
	$mensaje = "Error al cargar el archivo";
	$success = false;
}
?>
<div class="row">
	<div class="col-sm-6 col-md-6 col-lg-6 col-sm-offset-3 col-md-offset-3 col-lg-offset-3">
		<div class="portlet box blue">
			<div class="portlet-title">
				<div class="caption">
					Resultado de Importacion
				</div>
				<div class="actions">
	                <a class="btn btn-sm blue"  onclick="javascript: window.location='importar_posiciones.php'">
	                  <i class="fa fa-arrow-left"></i> Regresar
	                </a>
              	</div>
			</div>
			<div class="portlet-body form" id="blockui_portlet_body">
				<div class="form-body">
					<div class="row">
						<div class="form-group" style="padding-left: 5%;padding-right: 5%;text-align:right;">
							<label class="col-sm-5 col-md-5 col-lg-5 control-label">POSICIONES NUEVAS:</label>
							<div class="col-sm-7 col-md-7 col-lg-7">
								<input type="text" class="form-control input-sm" value="<?php echo $nuevas; ?>" readonly>
							</div>
						</div>
						<br>

						<div class="form-group" style="padding-left: 5%;padding-right: 5%;text-align:right;">
							<label class="col-sm-5 col-md-5 col-lg-5 control-label">POSICIONES MODIFICADAS:</label>
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
	</div>
</div>