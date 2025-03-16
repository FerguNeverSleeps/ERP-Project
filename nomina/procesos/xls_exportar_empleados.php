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
require_once("../paginas/phpexcel/Classes/PHPExcel.php");
require_once("../paginas/phpexcel/Classes/PHPExcel/IOFactory.php");

if(isset($_POST['codnivel1']) || isset($_GET['codnivel1']))
{
	$codnivel1 = isset($_POST['codnivel1']) ? $_POST['codnivel1'] : $_GET['codnivel1'];

	$conexion = new bd($_SESSION['bd']);

	if (!file_exists("templates/exportar_colaboradores.xlsx")) {
		exit("Plantilla de excel no encontrada");
	}

	$sql = "SELECT descrip FROM nomnivel1 WHERE codorg='{$codnivel1}'";
	$res = $conexion->query($sql);
	$obj = $res->fetch_object();

	$objPHPExcel = new PHPExcel();

	// Leemos un archivo Excel
	$objReader   = PHPExcel_IOFactory::createReader('Excel2007');
	$objPHPExcel = $objReader->load("templates/exportar_colaboradores.xlsx");

	$objPHPExcel->setActiveSheetIndex(0);

	$sql = "SELECT  n.ficha, n.apellidos, n.nombres, n.apenom, n.cedula, n.lugarnac, n.suesal, n.hora_base,
				    n.nomposicion_id, n.estado, n.sexo, n.seguro_social, n.num_decreto, n.cta_presupuestaria, n.tipo_empleado, 
				    n.clave_ir, n.tipnom, n.codnivel1, DATE_FORMAT(n.fecnac, '%d/%m/%Y') as fecnac,
				    DATE_FORMAT(n.fecing, '%d/%m/%Y') as fecing 
		    FROM   nompersonal n
		    WHERE  n.codnivel1 = '{$codnivel1}'";	

	$res = $conexion->query($sql);

	$i=2;
	while($data = $res->fetch_object())
	{
		$objPHPExcel->getActiveSheet()->setCellValue("A".$i, $data->ficha); 				// Numero de Ficha - Requerido
		$objPHPExcel->getActiveSheet()->setCellValue("B".$i, $data->apellidos); 			// Apellidos
		$objPHPExcel->getActiveSheet()->setCellValue("C".$i, $data->nombres); 				// Nombres
		$objPHPExcel->getActiveSheet()->setCellValue("D".$i, $data->cedula); 				// Cedula
		$objPHPExcel->getActiveSheet()->setCellValue("E".$i, $data->fecnac);  				// Fecha Nac (dia/mes/año => 21/03/2015)
		$objPHPExcel->getActiveSheet()->setCellValue("F".$i, $data->lugarnac); 				// Lugar
		$objPHPExcel->getActiveSheet()->setCellValue("G".$i, $data->fecing);  				// Fecha de Ingreso
		$objPHPExcel->getActiveSheet()->setCellValue("H".$i, $data->suesal); 				// Salario
		$objPHPExcel->getActiveSheet()->setCellValue("I".$i, $data->hora_base); 			// Horas Base
		$objPHPExcel->getActiveSheet()->setCellValue("J".$i, $data->nomposicion_id); 		// Numero de Posicion
		$objPHPExcel->getActiveSheet()->setCellValue("K".$i, $data->cta_presupuestaria); 	// Cta. Presupuestaria
		$objPHPExcel->getActiveSheet()->setCellValue("L".$i, $data->estado); 				// Status
		$objPHPExcel->getActiveSheet()->setCellValue("M".$i, $data->sexo); 					// Sexo
		$objPHPExcel->getActiveSheet()->setCellValue("N".$i, $data->seguro_social); 		// Seguro Social
		$objPHPExcel->getActiveSheet()->setCellValue("O".$i, $data->num_decreto); 			// #Decreto
		$objPHPExcel->getActiveSheet()->setCellValue("P".$i, $data->tipo_empleado); 		// Titular o Interino
		$objPHPExcel->getActiveSheet()->setCellValue("Q".$i, $data->clave_ir); 				// Clave I/R
		$objPHPExcel->getActiveSheet()->setCellValue("R".$i, $data->tipnom); 				// Planilla
		$i++;
	}

	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('R')->setAutoSize(true);

	$objPHPExcel->getActiveSheet()->setSelectedCells('B500');

	$objPHPExcel->setActiveSheetIndex(0);

	$nivel = str_replace(array(' ',"\"", "Á", "É", "Í", "Ó", "Ú", "á", "é", "í", "ó", "ú"), 
						 array('' , '' , "A", "E", "I", "O", "U", "a", "e", "i", "o", "u"), $obj->descrip);

	## Descargar Archivo xlsx (Excel2007)
	// header('Content-Type: application/vnd.ms-excel');
	// header('Content-Disposition: attachment;filename="Colaboradores_'.$nivel.'.xlsx"');
	// header('Cache-Control: max-age=0');

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');	
	//$objWriter->save('php://output');
	$objWriter->save('excel/Colaboradores_'.$nivel.'.xlsx'); // str_replace('.php', '.xlsx', __FILE__)

	echo 'excel/Colaboradores_'.$nivel.'.xlsx';
}
else
{
   echo "<script>alert('Acceso Denegado');</script>";  
   echo "<script>document.location.href = 'exportar_empleados_excel.php';</script>";
}

exit;
