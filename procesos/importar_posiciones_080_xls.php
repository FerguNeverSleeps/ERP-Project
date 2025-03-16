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
		$errores_actualizados = 0;
		$errores_registrados = 0;
		$registrados = 0;
		$actualizados = 0;
		$errores_actualizados = 0;
		$errores_registrados = 0;
		$total   = 0;
		$filas = "";
		$itera = TRUE;
		for ($i = 9; $i <= $numRows; $i++) 
		{
			if ($objPHPExcel->getActiveSheet()->getCell("R".$i)->getValue() != '') {
				$bandera = 0;
				$sueldos = array(
					'sueldo_propuesto' => $objPHPExcel->getActiveSheet()->getCell("R".$i)->getCalculatedValue(),
					'sueldo_1'         => $objPHPExcel->getActiveSheet()->getCell("R".$i)->getCalculatedValue(),
					'mes_1'            => 0,
					'sueldo_2'         => 0,
					'mes_2'            => 0,
					'sueldo_3'         => 0,
					'mes_3'            => 0,
					'sueldo_4'         => 0,
					'mes_4'            => 0,
					'sueldo_anual'     => $objPHPExcel->getActiveSheet()->getCell("R".$i)->getCalculatedValue()*12
				);
				$posiciones = array(
	            'tipnom'                      => $objPHPExcel->getActiveSheet()->getCell("C".$i)->getCalculatedValue(),
	            'codigo_diputado'             => $objPHPExcel->getActiveSheet()->getCell("C".$i)->getCalculatedValue(),
	            'nomposicion_id'              => $objPHPExcel->getActiveSheet()->getCell("E".$i)->getCalculatedValue(),
	            'cargo_id'                    => $objPHPExcel->getActiveSheet()->getCell("J".$i)->getCalculatedValue(),
	            'sueldo_propuesto'            => $sueldos['sueldo_propuesto'],

	            'sueldo_1'                    => $sueldos['sueldo_1'],
	            'mes_1'                       => $sueldos['mes_1'],
	            'sueldo_2'                    => $sueldos['sueldo_2'],
	            'mes_2'                       => $sueldos['mes_2'],
	            'sueldo_3'                    => $sueldos['sueldo_3'],
	            'mes_3'                       => $sueldos['mes_3'],
	            'sueldo_4'                    => $sueldos['sueldo_4'],
	            'mes_4'                       => $sueldos['mes_4'],

	        	'sueldo_anual'                => $sueldos['sueldo_anual'],

	            'partida'                     => $objPHPExcel->getActiveSheet()->getCell("B".$i)->getCalculatedValue(),

	            'sobresueldo_otros_1'         => 0,
	            'sueldo_propuesto_80'         => 0,
	            'partida080'                  => 0,
	            'mes_ot_1'                    => 0,

	            'gastos_representacion'       => 0,
	            'sueldo_propuesto_30'         => 0,
	            'partida030'                  => 0,
	            'mes_gasto_1'                 => 0,
	            
	            'dieta'                       => 0,
	            'partida011'                  => 0,

	            'combustible'                 => 0,
	            'partida012'                  => 0,


	            'sobresueldo_antiguedad_1'    => 0,
	            'sueldo_propuesto_11'         => 0,
	            'partida011'                  => 0,
	            'mes_ant_1'                   => 0,

	            'sobresueldo_zona_apartada_1' => 0,
	            'mes_za_1'                    => 0,
	            'sueldo_propuesto_12'         => 0,

	            'sobresueldo_jefatura_1'      => 0,
	            'sueldo_propuesto_13'         => 0,
	            'partida013'                  => 0,
	            'mes_jef_1'                   => 0,

	            'sobresueldo_esp_1'           => 0,
	            'mes_esp_1'                   => 0,
	            'sueldo_propuesto_19'         => 0,
	            );
				//=====================================================================================================
				## Registro de la posicion
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
				//=====================================================================================================
				## Registro del funcionario
				$empleado = array(
					'tipnom'           => $objPHPExcel->getActiveSheet()->getCell("C".$i)->getCalculatedValue(),
					'diputado'         => $objPHPExcel->getActiveSheet()->getCell("D".$i)->getCalculatedValue(),
					'apenom'           => $objPHPExcel->getActiveSheet()->getCell("F".$i)->getCalculatedValue()." ".$objPHPExcel->getActiveSheet()->getCell("G".$i)->getCalculatedValue(),
					'nomposicion_id'   => $objPHPExcel->getActiveSheet()->getCell("E".$i)->getCalculatedValue(),
					'nombres'          => $objPHPExcel->getActiveSheet()->getCell("F".$i)->getCalculatedValue(),
					'apellidos'        => $objPHPExcel->getActiveSheet()->getCell("G".$i)->getCalculatedValue(),
					'cedula'           => $objPHPExcel->getActiveSheet()->getCell("H".$i)->getCalculatedValue(),
					'seguro_social'    => $objPHPExcel->getActiveSheet()->getCell("I".$i)->getCalculatedValue(),
					'codcargo'         => ( $objPHPExcel->getActiveSheet()->getCell("J".$i)->getCalculatedValue() == "DEPORTIVO" ) ? "1071020":"3021090" ,
					'num_decreto'      => $objPHPExcel->getActiveSheet()->getCell("K".$i)->getCalculatedValue(),
					'fecha_decreto'    => $objPHPExcel->getActiveSheet()->getCell("L".$i)->getCalculatedValue(),
					'num_resolucion'   => $objPHPExcel->getActiveSheet()->getCell("O".$i)->getCalculatedValue(),
					'fecha_resolucion' => $objPHPExcel->getActiveSheet()->getCell("P".$i)->getCalculatedValue(),
					'sueldopro'        => $objPHPExcel->getActiveSheet()->getCell("R".$i)->getCalculatedValue(),
					'suesal'           => $objPHPExcel->getActiveSheet()->getCell("R".$i)->getCalculatedValue(),
					'codigo_diputado'  => $objPHPExcel->getActiveSheet()->getCell("C".$i)->getCalculatedValue(),
				);

				$res_e = $conexion->query("SELECT * FROM nompersonal WHERE cedula = '".$empleado['cedula']."'");
				$emp = mysqli_fetch_array($res_e);
				if (!empty($emp))
				{
                	$sql2="UPDATE `nompersonal` SET  `tipnom`='".$empleado['tipnom']."',`apenom`='".$empleado['apenom']."', `nombres`='".$empleado['nombres']."', `apellidos`='".$empleado['apellidos']."', `cedula`='".$empleado['cedula']."', `seguro_social`='".$empleado['seguro_social']."', `codcargo`='".$empleado['codcargo']."', `num_decreto`='".$empleado['num_decreto']."', `fecha_decreto`='".$empleado['fecha_decreto']."', `num_resolucion`='".$empleado['num_resolucion']."', `fecha_resolucion`='".$empleado['fecha_resolucion']."', `sueldopro`='".$empleado['sueldopro']."',`suesal`='".$empleado['suesal']."',`codigo_diputado` = '".$empleado['codigo_diputado']."' WHERE nomposicion_id = '".$empleado['nomposicion_id']."'";
					if ($conexion->query($sql2)) {
                		$actualizados++;
                	}else{
                		$errores_actualizados++;
                	}
                }else{
                	$sql2="INSERT INTO `nompersonal` (
					tipnom,
					apenom,
					nomposicion_id,
					nombres,
					apellidos,
					cedula,
					seguro_social,
					codcargo,
					num_decreto,
					fecha_decreto,
					num_resolucion,
					fecha_resolucion,
					sueldopro,
					suesal,
					codigo_diputado ) VALUES (
					'".$posiciones['tipnom']."',
					'".$posiciones['apenom']."',
					'".$posiciones['nomposicion_id']."',
					'".$posiciones['nombres']."',
					'".$posiciones['apellidos']."',
					'".$posiciones['cedula']."',
					'".$posiciones['seguro_social']."',
					'".$posiciones['codcargo']."',
					'".$posiciones['num_decreto']."',
					'".$posiciones['fecha_decreto']."',
					'".$posiciones['num_resolucion']."',
					'".$posiciones['fecha_resolucion']."',
					'".$posiciones['sueldopro']."',
					'".$posiciones['suesal']."',
					'".$posiciones['codigo_diputado']."'
					)";
					if ($conexion->query($sql2)) {
						$res_max = $conexion->query("SELECT MAX(personal_id) as personal_id FROM nompersonal");
						$row  = mysqli_fetch_array($res_max);
						$ficha  =$row['personal_id'];
						$res_d = $conexion->query("UPDATE `nompersonal` SET `ficha` = '".$ficha."' WHERE `personal_id` = '".$ficha."'");
						$registrados++;
					}else{
						$errores_registrados++;
					}
                }
				$res_d = $conexion->query("UPDATE `nompersonal` SET `codigo_diputado` = '".$posiciones['codigo_diputado']."' WHERE `apenom` = '".$posiciones['diputado']."'");
                $res1 = $conexion->query($sql1);
				$total++;
				//echo $sql1."<br>";
				//echo $sql2."<br>";
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
					Resultado de Importacion - Planilla 080
				</div>
				<div class="actions">
	                <a class="btn btn-sm blue"  onclick="javascript: window.location='importar_posiciones_080.php'">
	                  <i class="fa fa-arrow-left"></i> Regresar
	                </a>
              	</div>
			</div>
			<div class="portlet-body form" id="blockui_portlet_body">
				<div class="form-body">
					<div class="row">
						
						<div class="form-group" style="padding-left: 5%;padding-right: 5%;text-align:right;">
							<label class="col-sm-5 col-md-5 col-lg-5 control-label">FUNCIONARIOS NUEVOS:</label>
							<div class="col-sm-7 col-md-7 col-lg-7">
								<input type="text" class="form-control input-sm" value="<?php echo $registrados; ?>" readonly>
							</div>
						</div>
						<br>

						<div class="form-group" style="padding-left: 5%;padding-right: 5%;text-align:right;">
							<label class="col-sm-5 col-md-5 col-lg-5 control-label">FUNCIONARIOS ACTUALIZADOS:</label>
							<div class="col-sm-7 col-md-7 col-lg-7">
								<input type="text" class="form-control input-sm" value="<?php echo $actualizados; ?>" readonly>
							</div>
						</div>

						<div class="form-group" style="padding-left: 5%;padding-right: 5%;text-align:right;">
							<label class="col-sm-5 col-md-5 col-lg-5 control-label">POSICIONES NUEVAS:</label>
							<div class="col-sm-7 col-md-7 col-lg-7">
								<input type="text" class="form-control input-sm" value="<?php echo $nuevas; ?>" readonly>
							</div>
						</div>
						<br>

						<div class="form-group" style="padding-left: 5%;padding-right: 5%;text-align:right;">
							<label class="col-sm-5 col-md-5 col-lg-5 control-label">POSICIONES ACTUALIZADAS:</label>
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