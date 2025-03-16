<?php
session_start();
/** Error reporting */
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
//date_default_timezone_set('America/Caracas');

define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

/** Include PHPExcel */
require_once("../lib/common.php");
//require_once("../paginas/func_bd.php");
require_once("../paginas/phpexcel/Classes/PHPExcel.php");
require_once("../paginas/phpexcel/Classes/PHPExcel/IOFactory.php");

$conexion = new bd($_SESSION['bd']);

// Inicializar variables
$success  = true;
$mensaje  = '';
$actualizados = $insertados = 0;
$plantilla = array(
"ficha",
"cedula",
"fecha_pago",
"cod_planilla",
"tipo_planilla",
"frecuencia_planilla",
"salario_bruto",
"vacac",
"xiii",
"gtorep",
"xiii_gtorep",
"liquida",
"bono",
"otros_ing",
"prima",
"s_s",
"islr",
"islr_gr",
"acreedor_suma",
"Neto",
"estatus"
);
// Validar posibles errores: 

if($_FILES['archivo']['name'] != '')
{
	$name	  = $_FILES['archivo']['name'];
	$tname 	  = $_FILES['archivo']['tmp_name'];
	$type 	  = $_FILES['archivo']['type'];

	if($type == 'application/vnd.ms-excel')
		$ext = 'xls'; // Extension excel 97
	else if($type == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
		$ext = 'xlsx'; // Extension excel 2007 y 2010
	else{

		if (function_exists('finfo_file')) 
		{
			$type  = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $tname);

			if($type == 'application/vnd.ms-excel')
				$ext = 'xls'; // Extension excel 97
			else if($type == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
				$ext = 'xlsx'; // Extension excel 2007 y 2010		
		}
		// else
		// 	alert("¡Error! No se encuentra disponible la funcion finfo_file. Active la extensión fileinfo en su archivo php.ini");

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

		$i=2;
		while($objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue() != '')
		{
			/** Se obtienen los datos del excel */
			$letra            = "A";

			$ficha               = $objPHPExcel->getActiveSheet()->getCell($letra.$i)->getValue(); // Ficha
			$letra++;
			$cedula              = $objPHPExcel->getActiveSheet()->getCell($letra.$i)->getValue(); // Cedula
			$letra++;
			$fecha_pago          = $objPHPExcel->getActiveSheet()->getCell($letra.$i)->getValue(); // Fecha Pago
			$letra++;
			$cod_planilla        = $objPHPExcel->getActiveSheet()->getCell($letra.$i)->getValue(); // Num Planilla
			$letra++;
			$tipo_planilla       = $objPHPExcel->getActiveSheet()->getCell($letra.$i)->getValue(); // Tipo Planilla
			$letra++;
			$frecuencia_planilla = $objPHPExcel->getActiveSheet()->getCell($letra.$i)->getValue(); // Frecuencia
			$letra++;
			$salario_bruto       = $objPHPExcel->getActiveSheet()->getCell($letra.$i)->getValue(); // Salario Bruto
			$letra++;
			$vacac               = $objPHPExcel->getActiveSheet()->getCell($letra.$i)->getValue(); // Vacaciones
			$letra++;
			$xii                 = $objPHPExcel->getActiveSheet()->getCell($letra.$i)->getValue(); // Decimo
			$letra++;
			$gtorep             = $objPHPExcel->getActiveSheet()->getCell($letra.$i)->getValue(); // Gasto Representación
			$letra++;
			$xii_gtorep          = $objPHPExcel->getActiveSheet()->getCell($letra.$i)->getValue(); // Decimo Gasto Rep.
			$letra++;
			$liquida             = $objPHPExcel->getActiveSheet()->getCell($letra.$i)->getValue(); // Liquidacion
			$letra++;
			$bono                = $objPHPExcel->getActiveSheet()->getCell($letra.$i)->getValue(); // Bono
			$letra++;
			$otros_ing           = $objPHPExcel->getActiveSheet()->getCell($letra.$i)->getValue(); // Otros ingresos
			$letra++;
			$prima               = $objPHPExcel->getActiveSheet()->getCell($letra.$i)->getValue(); // Prima
			$letra++;
			$s_s                 = $objPHPExcel->getActiveSheet()->getCell($letra.$i)->getValue(); // Seguro social
			$letra++;
			$s_e                 = $objPHPExcel->getActiveSheet()->getCell($letra.$i)->getValue(); // Seguros Educativo
			$letra++;
			$islr                = $objPHPExcel->getActiveSheet()->getCell($letra.$i)->getValue(); // Impuesto sobre la renta
			$letra++;
			$islr_gr             = $objPHPExcel->getActiveSheet()->getCell($letra.$i)->getValue(); // ISLR Gasto Respresentacion
			$letra++;
			$acreedor_suma       = $objPHPExcel->getActiveSheet()->getCell($letra.$i)->getValue(); // Acreedores
			$letra++;
			$Neto                = $objPHPExcel->getActiveSheet()->getCell($letra.$i)->getValue(); // NEto
			$letra++;
			$estatus             = $objPHPExcel->getActiveSheet()->getCell($letra.$i)->getValue(); // Estatus
			$letra++;
			$i++;

			/**** Se formatean las fechas */
			$fecha_pago = date('Y-m-d',PHPExcel_Shared_Date::ExcelToPHP($fecha_pago));

			// Ultimo SQL
			$sql_max = "SELECT MAX(id) AS ultimo_id FROM salarios_acumulados";
			$res_consulta_max = $conexion->query($sql_max);
			$row_max = $res_consulta_max->fetch_assoc();
			$ultimo_id = $row_max['ultimo_id'] + 1; // sumamos 1 al último ID de la tabla

			/** consulta para verificiar si existen los datos */
			$sql_consulta = "SELECT id from salarios_acumulados where ficha = '{$ficha}' AND fecha_pago = '{$fecha_pago}' AND cod_planilla = '{$cod_planilla}' AND tipo_planilla = '{$tipo_planilla}'";
			$res_consulta = $conexion->query($sql_consulta);
			$fila_consulta = $res_consulta->num_rows;
			if($fila_consulta==0){
			
				$sql="INSERT INTO salarios_acumulados
						(id,
						ficha,
						cedula,
						fecha_pago,
						cod_planilla,
						tipo_planilla,
						frecuencia_planilla,
						salario_bruto,
						vacac,
						xiii,
						gtorep,
						xiii_gtorep,
						liquida,
						bono,
						otros_ing,
						prima,
						s_s,
						s_e,
						islr,
						islr_gr,
						acreedor_suma,
						Neto,
						estatus
						) 
					VALUES (
					'{$ultimo_id}',
					'{$ficha}',
					'{$cedula}',
					'{$fecha_pago}',
					'{$cod_planilla}',
					'{$tipo_planilla}',
					'{$frecuencia_planilla}',
					'{$salario_bruto}',
					'{$vacac}',
					'{$xiii}',
					'{$gtorep}',
					'{$xiii_gtorep}',
					'{$liquida}',
					'{$bono}',
					'{$otros_ing}',
					'{$prima}',
					'{$s_s}',
					'{$s_e}',
					'{$islr}',
					'{$islr_gr}',
					'{$acreedor_suma}',
					'{$Neto}',
					'{$estatus}'
					)";	
					$insertados++;

					$demo = $sql;

			}
			else
			{
				$sql = "UPDATE salarios_acumulados SET 
				salario_bruto ='{$salario_bruto}',
				vacac ='{$vacac}',
				xiii ='{$xiii}',
				gtorep ='{$gtorep}',
				xiii_gtorep ='{$xiii_gtorep}',
				liquida ='{$liquida}',
				bono ='{$bono}',
				otros_ing ='{$otros_ing}',
				prima ='{$prima}',
				s_s ='{$s_s}',
				s_e ='{$s_e}',
				islr ='{$islr}',
				islr_gr ='{$islr_gr}',
				acreedor_suma ='{$acreedor_suma}',
				Neto ='{$Neto}'
				WHERE ficha = '{$ficha}' AND fecha_pago = '{$fecha_pago}' AND cod_planilla = '{$cod_planilla}' AND tipo_planilla = '{$tipo_planilla}'
				";
				$actualizados++;
			}
				
			$res = $conexion->query($sql);

			if(!$res)
			{
				$success = false;

				$mensaje = "¡Error al registrar los empleados! (Código: ".$conexion->getCodigoError().") ".$conexion->getMensajeError();
				
				if($conexion->getCodigoError() == '1062')
				{
					$mensaje = "¡Error! Existen empleados duplicados (Ficha No: ".$ficha.")";
				}

				break;
			}	
		}

		if($i==2)
			$mensaje = "Error al importar los datos. La celda A$i no contiene datos.";
		else if($success)
			$mensaje = "Datos importados exitosamente";
	}
}
else
{
	$mensaje = "Error al cargar el archivo";
	$success = false;
}
$respuesta = array(
	"mensaje" => $mensaje,
	"insertados" => $insertados,
	"actualizados" => $actualizados,
	"success" => $success
);

echo  json_encode($respuesta);
?>
