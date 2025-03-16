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
$conexion->query("SET names utf8;");
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

			$ficha          = $objPHPExcel->getActiveSheet()->getCell($letra.$i)->getValue(); // ficha
			$letra++;
			$sueldo         = $objPHPExcel->getActiveSheet()->getCell($letra.$i)->getValue(); // sueldo
			$letra++;
			$descripcion    = $objPHPExcel->getActiveSheet()->getCell($letra.$i)->getValue(); // descripcion
            $descripcion    = utf8_decode($descripcion);
			$i++;

			/**** Se formatean las fechas */
			$fecha_pago = date('Y-m-d',PHPExcel_Shared_Date::ExcelToPHP($fecha_pago)) ;
			
			$select = "SELECT suesal, cedula from nompersonal where ficha = '{$ficha}'";
            $res_pers = $conexion->query($select);
            $fila = $res_pers->fetch_assoc();
            $sueldo_ant = $fila['suesal'];
            $cedula = $fila['cedula'];
			
            $sql="UPDATE nompersonal SET suesal = {$sueldo}, sueldopro = {$sueldo} 
            WHERE ficha = 
                '{$ficha}'";	
                $insertados++;
				
			$res = $conexion->query($sql);

			if(!$res)
			{
				$success = false;

				$mensaje = "¡Error al registrar los empleados! (Código: ".$conexion->getCodigoError().") ".$conexion->getMensajeError();
				

				break;
			}	
            
            $fecha_creacion = date('Y-m-d');
           $insert = "INSERT INTO expediente (cedula, descripcion, monto, monto_nuevo, estatus, tipo, fecha, fecha_creacion, fecha_aprobacion, numero_accion, usuario_creacion) 
            values 
            ('{$cedula}', '{$descripcion}', '{$sueldo_ant}', '{$sueldo}', '1', '31', '$fecha_creacion', '$fecha_creacion','$fecha_creacion', '33', 'amaxonia')";
			$res = $conexion->query($insert);
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
