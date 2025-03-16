<?php
require_once('../lib/database.php');

error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('America/Panama');

set_time_limit(0);

if (PHP_SAPI == 'cli')
	die('Este ejemplo solo se debe ejecutar desde un navegador Web');

// Include PHPExcel
include('lib/php_excel.php');
require_once("phpexcel/Classes/PHPExcel.php");
require_once("phpexcel/Classes/PHPExcel/IOFactory.php");

if(isset($_POST['codnom1']) && isset($_POST['codnom2']))
{
	$codnom1 = $_POST['codnom1']; // Primera Quincena - Frecuencia 2
	$codnom2 = $_POST['codnom2']; // Segunda Quincena - Frecuencia 3

	$db = new Database($_SESSION['bd']);

	// Datos de la empresa
	$empresa = $db->query("SELECT nom_emp FROM nomempresa")->fetch_object();

	// Datos de las planillas
	$sql = "SELECT descrip, DATE_FORMAT(periodo_ini, '%M de %Y') as mes
			FROM   nom_nominas_pago
			WHERE  codnom='{$codnom1}' AND codtip = '{$_SESSION['codigo_nomina']}'";
	$res = $db->query($sql);
	$planilla = $res->fetch_object();
	$mes_plan = traducir_mes($planilla->mes);

	$objPHPExcel = new PHPExcel();

	$objPHPExcel->getProperties()->setCreator("Selectra Planilla")
								 ->setLastModifiedBy("Selectra Planilla")
								 ->setTitle("Asiento de Planilla");

	// Tipo de letra por defecto del Libro
	$objPHPExcel->getActiveSheet()->getDefaultStyle()->getFont()->setName('Times New Roman')->setSize(10); 

	// Color de fondo por defecto del Libro
	colorFondoLibro('FFFFFF'); // Hojas Fondo Blanco

	// Seleccionar Departamentos
	$sql = "SELECT n1.codorg, n1.descrip FROM nomnivel1 n1 ORDER BY 1";
	$res = $db->query($sql);

	$hoja=0;
	while($nivel = $res->fetch_object())
	{
		if($hoja>0)
			$objPHPExcel->createSheet();

		$objPHPExcel->setActiveSheetIndex($hoja);
		$objPHPExcel->getActiveSheet()->setTitle( str_replace('/', '-', $nivel->descrip) );

		$objPHPExcel->getActiveSheet()->setCellValue('A1', strtoupper($empresa->nom_emp))
									  ->setCellValue('A2', 'CONSOLIDADO DE ASIENTO DE PLANILLA ' . strtoupper($nivel->descrip))
									  ->setCellValue('A3', strtoupper($mes_plan))
									  ->setCellValue('F3', 'DEBITO')
									  ->setCellValue('H3', 'CREDITO')
									  ->setCellValue('L3', 'CUENTA');

		$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(20)->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('A2:A3')->getFont()->setSize(14);

		$objPHPExcel->getActiveSheet()->getStyle('F3:L3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);	

		$objPHPExcel->getActiveSheet()->getStyle('A4:D4')->applyFromArray(borderBottom(PHPExcel_Style_Border::BORDER_THIN));
		$objPHPExcel->getActiveSheet()->getStyle('F4')->applyFromArray(borderBottom(PHPExcel_Style_Border::BORDER_THIN));
		$objPHPExcel->getActiveSheet()->getStyle('H4')->applyFromArray(borderBottom(PHPExcel_Style_Border::BORDER_THIN));
		$objPHPExcel->getActiveSheet()->getStyle('L4')->applyFromArray(borderBottom(PHPExcel_Style_Border::BORDER_THIN));		
		$objPHPExcel->getActiveSheet()->getStyle('A5:D5')->applyFromArray(borderBottom(PHPExcel_Style_Border::BORDER_THIN));
		$objPHPExcel->getActiveSheet()->getStyle('F5')->applyFromArray(borderBottom(PHPExcel_Style_Border::BORDER_THIN));
		$objPHPExcel->getActiveSheet()->getStyle('H5')->applyFromArray(borderBottom(PHPExcel_Style_Border::BORDER_THIN));
		$objPHPExcel->getActiveSheet()->getStyle('L5')->applyFromArray(borderBottom(PHPExcel_Style_Border::BORDER_THIN));

		// Ancho de las columnas
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(38);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(24);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(2);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(6);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(2);
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(18);
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(3);
		$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(18);
		$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(0);
		//$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(0);
		$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setVisible(FALSE);
		$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(2);
		$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(16);

		// Alto filas
		$objPHPExcel->getActiveSheet()->getRowDimension(5)->setRowHeight(7);

		$objPHPExcel->getActiveSheet()->getSheetView()->setZoomScale(115);

		// Consultar cuentas
		$sql1 = "SELECT cc.codigo, cc.descripcion, cc.concepto, cc.tipo, nc.ctacon as cuenta
				 FROM   con_comprobante cc
				 LEFT JOIN nomconceptos nc ON nc.codcon=cc.concepto";
		$res1 = $db->query($sql1);

		$i = 7;
		while($fila1 = $res1->fetch_array())
		{
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, $fila1['descripcion']);
			$objPHPExcel->getActiveSheet()->setCellValue('L'.$i, $fila1['cuenta']);

			if($fila1['concepto'] != '')
			{
				// Si es solo un concepto
				$sql2 = "SELECT SUM(monto) as monto 
						 FROM   nom_movimientos_nomina
						 WHERE  codcon='{$fila1['concepto']}' AND codnom IN ($codnom1, $codnom2)";	

				$res2 = $db->query($sql2)->fetch_object();

				$monto = isset($res2->monto) ? $res2->monto : '';

				$letra = ($fila1['tipo']=='D') ? 'F' : 'H' ;

				$objPHPExcel->getActiveSheet()->setCellValue($letra . $i, $monto);
			}

			$i++;
		}

		$objPHPExcel->getActiveSheet()->setCellValue('F'.($i+1), '=SUM(F7:F'.($i-1).')')
									  ->setCellValue('H'.($i+1), '=SUM(H7:H'.($i-1).')')
									  ->setCellValue('F'.($i+3), 'Ajuste:')
									  ->setCellValue('H'.($i+3), '=H'.($i+1).'-F'.($i+1))
									  ->setCellValue('B'.($i+5), 'CONTABILIZADO');

		$objPHPExcel->getActiveSheet()->getStyle('F'.($i+1).':H'.($i+1))->getFont()->setBold(true);

		$objPHPExcel->getActiveSheet()->getStyle('F7:F'.($i+1))->getNumberFormat()->setFormatCode('"$" * #,##0.00');
		$objPHPExcel->getActiveSheet()->getStyle('H7:H'.($i+1))->getNumberFormat()->setFormatCode('"$" * #,##0.00');
		$objPHPExcel->getActiveSheet()->getStyle('H'.($i+3))->getNumberFormat()->setFormatCode('#,##0.00');

		$objPHPExcel->getActiveSheet()->getStyle('F'.($i+1))->applyFromArray(borderTop(PHPExcel_Style_Border::BORDER_THIN));
		$objPHPExcel->getActiveSheet()->getStyle('H'.($i+1))->applyFromArray(borderTop(PHPExcel_Style_Border::BORDER_THIN));
		$objPHPExcel->getActiveSheet()->getStyle('F'.($i+1))->applyFromArray(borderBottom(PHPExcel_Style_Border::BORDER_DOUBLE));
		$objPHPExcel->getActiveSheet()->getStyle('H'.($i+1))->applyFromArray(borderBottom(PHPExcel_Style_Border::BORDER_DOUBLE));

		$objPHPExcel->getActiveSheet()->setSelectedCells('A65');

		$hoja++;
	}

	//==============================================================================================
	// Ubicarse en la primera hoja

	$objPHPExcel->setActiveSheetIndex(0);
	$objPHPExcel->getActiveSheet()->setSelectedCells('A65');

	$mes_plan = str_replace(array("\"", ' ', "Á", "É", "Í", "Ó", "Ú", "á", "é", "í", "ó", "ú"), 
						    array('' , '', "A", "E", "I", "O", "U", "a", "e", "i", "o", "u"), 
						    $mes_plan);

	$filename = 'excel/AsientoPlanilla'.ucwords($mes_plan).'.xlsx';

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');	

	$objWriter->save($filename);

	echo $filename;
}
else
{
   echo "<script>alert('Acceso Denegado');</script>";  
   echo "<script>document.location.href = 'config_rpt_asiento_planilla.php';</script>";
}

exit;

function traducir_mes($fecha)
{
	$fecha = strtolower($fecha);

	$meses_in = array('january', 'february', 'march', 'april', 'may', 'june', 'july', 
					  'august', 'september', 'october', 'november', 'december');	

	$meses_es = array('enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio',
					  'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre');

	return str_replace($meses_in, $meses_es, $fecha);	
}