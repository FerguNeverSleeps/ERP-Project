<?php
session_start();
/** Error reporting */
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);

define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

/** Include PHPExcel */
require_once("../lib/common.php");
require_once("../paginas/phpexcel/Classes/PHPExcel.php");
require_once("../paginas/phpexcel/Classes/PHPExcel/IOFactory.php");

if(isset($_POST['codnivel1']))
{
	$codnivel1 = $_POST['codnivel1'];

	$conexion = new bd($_SESSION['bd']);

	if (!file_exists("templates/exportar_ficha_colaboradores.xlsx")) 
	{
		exit("Plantilla de excel no encontrada");
	}

	$sql = "SELECT descrip FROM nomnivel1 WHERE codorg='{$codnivel1}'";
	$res = $conexion->query($sql);
	$obj = $res->fetch_object();

	$objPHPExcel = new PHPExcel();

	// Leemos un archivo Excel
	$objReader   = PHPExcel_IOFactory::createReader('Excel2007');
	$objPHPExcel = $objReader->load("templates/exportar_ficha_colaboradores.xlsx");

	$objPHPExcel->setActiveSheetIndex(0);

	$sql = "SELECT n.tipnom as planilla, n.ficha, n.apellidos, n.nombres, n.cedula, 
				   CASE WHEN n.nacionalidad = 1 THEN 'Panameño'
				   		WHEN n.nacionalidad = 2 THEN 'Extranjero'
				   		WHEN n.nacionalidad = 3 THEN 'Nacionalizado'
				   END as nacionalidad, n.sexo, n.estado_civil, 
				   DATE_FORMAT(n.fecnac, '%d/%m/%Y') as fecha_nacimiento, n.lugarnac, p.descrip as profesion, n.estado as situacion,
				   DATE_FORMAT(n.fecing, '%d/%m/%Y') as fecha_ingreso, n.forcob as forma_pago, n.seguro_social, c.des_car as cargo,
				   ca.descrip as categoria, n.nomposicion_id as posicion, n.tipemp as tipo_contrato,
				   DATE_FORMAT(n.inicio_periodo, '%d/%m/%Y') as fecha_inicio, DATE_FORMAT(n.fin_periodo, '%d/%m/%Y') as fecha_fin,
				   t.descripcion as turno, n.num_decreto, n.num_decreto_baja, n.telefonos, n.hora_base, n.suesal,
				   n1.descrip as region, n2.descrip as subregion					
		    FROM   nompersonal n
		    LEFT JOIN nomprofesiones p ON n.codpro    = p.codorg
		    LEFT JOIN nomcargos c      ON n.codcargo  = c.cod_car
		    LEFT JOIN nomcategorias ca ON n.codcat    = ca.codorg
		    LEFT JOIN nomturnos t      ON n.turno_id  = t.turno_id
		    LEFT JOIN nomnivel1 n1     ON n.codnivel1 = n1.codorg 
		    LEFT JOIN nomnivel2 n2     ON n.codnivel2 = n2.codorg
		    WHERE  n.codnivel1 = '{$codnivel1}'";	

	$res = $conexion->query($sql);

	$i=2;
	while($data = $res->fetch_object())
	{
		$objPHPExcel->getActiveSheet()->setCellValue("A".$i, $data->planilla); 			// Planilla
		$objPHPExcel->getActiveSheet()->setCellValue("B".$i, $data->ficha); 			// Ficha
		$objPHPExcel->getActiveSheet()->setCellValue("C".$i, $data->apellidos); 		// Apellidos
		$objPHPExcel->getActiveSheet()->setCellValue("D".$i, $data->nombres); 			// Nombres
		$objPHPExcel->getActiveSheet()->setCellValue("E".$i, $data->cedula);  			// Cédula
		$objPHPExcel->getActiveSheet()->setCellValue("F".$i, $data->nacionalidad); 		// Nacionalidad
		$objPHPExcel->getActiveSheet()->setCellValue("G".$i, $data->sexo);  			// Sexo
		$objPHPExcel->getActiveSheet()->setCellValue("H".$i, $data->estado_civil); 		// Estado Civil
		$objPHPExcel->getActiveSheet()->setCellValue("I".$i, $data->fecha_nacimiento);	// Fecha de Nacimiento (dia/mes/año => 06/03/2016)
		$objPHPExcel->getActiveSheet()->setCellValue("J".$i, $data->lugarnac); 			// Lugar de Nacimiento
		$objPHPExcel->getActiveSheet()->setCellValue("K".$i, $data->profesion); 		// Profesión
		$objPHPExcel->getActiveSheet()->setCellValue("L".$i, $data->situacion); 		// Situación
		$objPHPExcel->getActiveSheet()->setCellValue("M".$i, $data->fecha_ingreso); 	// Fecha de Ingreso
		$objPHPExcel->getActiveSheet()->setCellValue("N".$i, $data->forma_pago); 		// Forma de Pago
		$objPHPExcel->getActiveSheet()->setCellValue("O".$i, $data->seguro_social); 	// Seguro Social
		$objPHPExcel->getActiveSheet()->setCellValue("P".$i, $data->cargo); 			// Cargo del Puesto
		$objPHPExcel->getActiveSheet()->setCellValue("Q".$i, $data->categoria); 		// Categoría
		$objPHPExcel->getActiveSheet()->setCellValue("R".$i, $data->posicion); 			// Posición
		$objPHPExcel->getActiveSheet()->setCellValue("S".$i, $data->tipo_contrato);		// Tipo de Contrato
		$objPHPExcel->getActiveSheet()->setCellValue("T".$i, $data->fecha_inicio);		// Fecha de Inicio
		$objPHPExcel->getActiveSheet()->setCellValue("U".$i, $data->fecha_fin);			// Fecha Fin
		$objPHPExcel->getActiveSheet()->setCellValue("V".$i, $data->turno);				// Turnos
		$objPHPExcel->getActiveSheet()->setCellValue("W".$i, $data->num_decreto);		// Número de Decreto
		$objPHPExcel->getActiveSheet()->setCellValue("X".$i, $data->num_decreto_baja);	// Decreto de Baja
		$objPHPExcel->getActiveSheet()->setCellValue("Y".$i, $data->telefonos);			// Teléfono
		$objPHPExcel->getActiveSheet()->setCellValue("Z".$i, $data->hora_base);			// Hora Base Trabajada
		$objPHPExcel->getActiveSheet()->setCellValue("AA".$i, $data->suesal);			// Salario
		$objPHPExcel->getActiveSheet()->setCellValue("AB".$i, $data->region);			// Región
		$objPHPExcel->getActiveSheet()->setCellValue("AC".$i, $data->subregion);		// Sub-Región
		$i++;
	}

	$letra='A';
	while($letra <= 'Z')
	{
		$objPHPExcel->getActiveSheet()->getColumnDimension($letra)->setAutoSize(true);
		$letra++;
	}
	$objPHPExcel->getActiveSheet()->getColumnDimension("AA")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("AB")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension("AC")->setAutoSize(true);

	$objPHPExcel->getActiveSheet()->setSelectedCells('B500');

	$objPHPExcel->setActiveSheetIndex(0);

	$nivel = str_replace(array(' ',"\"", "Á", "É", "Í", "Ó", "Ú", "á", "é", "í", "ó", "ú"), 
						 array('' , '' , "A", "E", "I", "O", "U", "a", "e", "i", "o", "u"), 
						 $obj->descrip);

	$filename = 'excel/Ficha_Colaboradores_'.$nivel.'.xlsx';

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');	

	$objWriter->save($filename);

	echo $filename;
}
else
{
   echo "<script>alert('Acceso Denegado');</script>";  
   echo "<script>document.location.href = 'exportar_ficha_colaboradores_excel.php';</script>";
}

exit;
