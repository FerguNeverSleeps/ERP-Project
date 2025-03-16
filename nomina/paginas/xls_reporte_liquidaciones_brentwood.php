<?php
require_once('../lib/database.php');

error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('America/Panama');

set_time_limit(0);

if (PHP_SAPI == 'cli')
	die('Este ejemplo solo se debe ejecutar desde un navegador Web');

include('lib/php_excel.php');
include('lib/phpexcel_conceptos.php');
require_once("phpexcel/Classes/PHPExcel.php");

if(1) // isset($_POST[''])
{
	$db = new Database($_SESSION['bd']);

	$objPHPExcel = new PHPExcel();

	$objPHPExcel->getProperties()->setCreator("Selectra Planilla")
								 ->setLastModifiedBy("Selectra Planilla")
								 ->setTitle("Reporte de Liquidaciones");

	$objPHPExcel->getDefaultStyle()->getFont()->setName('Calibri')->setSize(10)->setItalic(true);

	$objPHPExcel->getActiveSheet()->setTitle('Hoja 1');

	$objPHPExcel->getActiveSheet()->getPageSetup()->setPrintArea('A1:G30');

	$objPHPExcel->getActiveSheet()->getSheetView()->setZoomScale(95);
	$objPHPExcel->getActiveSheet()->getSheetView()->setView(PHPExcel_Worksheet_SheetView::SHEETVIEW_PAGE_BREAK_PREVIEW);

	$objDrawing = new PHPExcel_Worksheet_Drawing();
	$objDrawing->setName('Logo');
    $objDrawing->setDescription('Logo Brentwood');
	$objDrawing->setCoordinates('A1');
	$objDrawing->setPath('imagenes/logo_excel.png');
	$objDrawing->setOffsetX(10);
	$objDrawing->setOffsetY(13);
	$objDrawing->setResizeProportional(false);
	$objDrawing->setHeight(35.15); // 35,15 px  = 0.93 cm
	$objDrawing->setWidth(163.28); // 163,28 px = 4.32 cm
	$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());

	$objPHPExcel->getActiveSheet()->setCellValue('A4',  'CALCULO DE LIQUIDACIÓN')
								  ->setCellValue('A5',  ' NOMBRE:')
								  ->setCellValue('B5',  'ESTEBAN VIALETTE')
								  ->setCellValue('D5',  ' TIEMPO LABORADO')
								  ->setCellValue('A6',  ' CEDULA:')
								  ->setCellValue('B6',  '08-246-872')
								  ->setCellValue('D6',  'DEL')
								  ->setCellValue('E6',  '24-nov-2014')
								  ->setCellValue('F6',  'AL')
								  ->setCellValue('G6',  '02-abr-2016')
								  ->setCellValue('A7',  ' Liquidación por:')
								  ->setCellValue('B7',  'Artículo 213')
								  ->setCellValue('B9',  'TOTAL SALARIOS DEVENGADO')
								  ->setCellValue('B11', 'SALARIOS PENDIENTES POR PAGAR')
								  ->setCellValue('D12', ' S. Social')
								  ->setCellValue('E12', ' x')
								  ->setCellValue('F12', 0.0975) // 9,75%
								  ->setCellValue('D13', ' S. Educativo')
								  ->setCellValue('E13', ' x')
								  ->setCellValue('F13', 0.0125) // 1,25%
								  ->setCellValue('B16', 'VACACIONES PROPORCIONALES')
								  ->setCellValue('F16', 0.0909) // 0,09
								  ->setCellValue('D17', ' S. Social')
								  ->setCellValue('E17', ' x')
								  ->setCellValue('F17', 0.0975) // 9,75%
								  ->setCellValue('D18', ' S. Educativo')
								  ->setCellValue('E18', ' x')
								  ->setCellValue('F18', 0.0125) // 1,25%
								  ->setCellValue('B21', 'DECIMO TERCER MES PROPORCIONAL')
								  ->setCellValue('E21', 'x')
								  ->setCellValue('F21', 0.08334) // B/. 0,08
								  ->setCellValue('E22', 'S. Social')
								  ->setCellValue('F22', 0.0725) // 7,25%
								  ->setCellValue('B25', 'PRIMA DE ANTIGÜEDAD')
								  ->setCellValue('E25', 'x')
								  ->setCellValue('F25', 0.01923) // B/. 0,02
								  ->setCellValue('A27', ' TOTAL A RECIBIR')
								  ->setCellValue('A29', ' ELABORADA POR: Dto. de Planilla')
								  ->setCellValue('E29', ' RECIBIDO POR: _________________')
								  ->setCellValue('E30', ' FECHA:________________________');

	$objPHPExcel->getActiveSheet()->setCellValue('D9', 10604.73)
								  ->setCellValue('G11', 0)
								  ->setCellValue('G12', 0)
								  ->setCellValue('G13', 0)
								  ->setCellValue('G14', 0)
								  ->setCellValue('D16', 2689.925)
								  ->setCellValue('G16', 244.5141825)
								  ->setCellValue('G17', 23.84013279375)
								  ->setCellValue('G18', 3.05642728125)
								  ->setCellValue('G19', 217.617622425)
								  ->setCellValue('D21', 0)
								  ->setCellValue('G21', 0)
								  ->setCellValue('G22', 0)
								  ->setCellValue('G23', 0)
								  ->setCellValue('D25', 10849.2391825)
								  ->setCellValue('G25', 208.630869479475)
								  ->setCellValue('G27', 426.248491904475);


	$objPHPExcel->getActiveSheet()->mergeCells('A4:G4');
	$objPHPExcel->getActiveSheet()->mergeCells('A27:D27');

	$objPHPExcel->getActiveSheet()->getStyle('A4:G7')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('B9:B25')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('A27:G30')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('G11')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('G14')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('G19')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('D21')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('G23')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('D25')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('G25')->getFont()->setBold(true);

	$objPHPExcel->getActiveSheet()->getStyle('A4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('E21:E25')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);


	$objPHPExcel->getActiveSheet()->getStyle('G13')->applyFromArray(borderBottom(PHPExcel_Style_Border::BORDER_MEDIUM));
	$objPHPExcel->getActiveSheet()->getStyle('G18')->applyFromArray(borderBottom(PHPExcel_Style_Border::BORDER_MEDIUM));
	$objPHPExcel->getActiveSheet()->getStyle('G22')->applyFromArray(borderBottom(PHPExcel_Style_Border::BORDER_MEDIUM));
	$objPHPExcel->getActiveSheet()->getStyle('A27:D27')->applyFromArray(allBorders(PHPExcel_Style_Border::BORDER_MEDIUM));
	$objPHPExcel->getActiveSheet()->getStyle('G27')->applyFromArray(allBorders(PHPExcel_Style_Border::BORDER_MEDIUM));
	

	$currencyPanama = '_-[$B/.-180A] * #,##0.00_ ;_-[$B/.-180A] * -#,##0.00 ;_-[$B/.-180A] * "-"??_ ;_-@_ ';

	$objPHPExcel->getActiveSheet()->getStyle('D9')->getNumberFormat()->setFormatCode($currencyPanama);
	$objPHPExcel->getActiveSheet()->getStyle('G11')->getNumberFormat()->setFormatCode($currencyPanama);
	$objPHPExcel->getActiveSheet()->getStyle('G14')->getNumberFormat()->setFormatCode($currencyPanama);
	$objPHPExcel->getActiveSheet()->getStyle('D16')->getNumberFormat()->setFormatCode($currencyPanama);
	$objPHPExcel->getActiveSheet()->getStyle('G19')->getNumberFormat()->setFormatCode($currencyPanama);	
	$objPHPExcel->getActiveSheet()->getStyle('F21')->getNumberFormat()->setFormatCode($currencyPanama);
	$objPHPExcel->getActiveSheet()->getStyle('G23')->getNumberFormat()->setFormatCode($currencyPanama);
	$objPHPExcel->getActiveSheet()->getStyle('D25')->getNumberFormat()->setFormatCode($currencyPanama);
	$objPHPExcel->getActiveSheet()->getStyle('F25:G25')->getNumberFormat()->setFormatCode($currencyPanama);
	$objPHPExcel->getActiveSheet()->getStyle('G27')->getNumberFormat()->setFormatCode($currencyPanama);

	$currencyPanama2 = '"B/." #,##0.00';
	$objPHPExcel->getActiveSheet()->getStyle('D21')->getNumberFormat()->setFormatCode($currencyPanama2);

	$objPHPExcel->getActiveSheet()->getStyle('F12:F13')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
	$objPHPExcel->getActiveSheet()->getStyle('F17:F18')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
	$objPHPExcel->getActiveSheet()->getStyle('F22')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);

	//$objPHPExcel->getActiveSheet()->getStyle('F12')->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_TEXT);
	$objPHPExcel->getActiveSheet()->getStyle('G12:G13')->getNumberFormat()->setFormatCode('#,##0.00');
	$objPHPExcel->getActiveSheet()->getStyle('F16:G16')->getNumberFormat()->setFormatCode('#,##0.00');
	$objPHPExcel->getActiveSheet()->getStyle('G17:G18')->getNumberFormat()->setFormatCode('#,##0.00');
	$objPHPExcel->getActiveSheet()->getStyle('G21:G22')->getNumberFormat()->setFormatCode('#,##0.00');

	//==============================================================================================
	// Cálculo y consulta de salarios devengados por mes

	$objPHPExcel->getActiveSheet()->setCellValue('D33', 'Acum Salario/ VACACIONES PROPORCIONALES')
								  ->setCellValue('D48', 'Acum Salario/ XIII MES PROPORCIONAL')
								  ->setCellValue('F50', ' Se le pagó XIII mes')
								  ->setCellValue('D51', 'Vac.Proporcionales')
								  ->setCellValue('E52', 0);

	$objPHPExcel->getActiveSheet()->getStyle('D33')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('D48')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('E52')->getFont()->setBold(true);

	$objPHPExcel->getActiveSheet()->getStyle('D50:F51')->getFont()->setItalic(false);

	$objPHPExcel->getActiveSheet()->mergeCells('D51:E51');

	$objPHPExcel->getActiveSheet()->getStyle('D50:E51')->applyFromArray(allBorders(PHPExcel_Style_Border::BORDER_THIN));

	$objPHPExcel->getActiveSheet()->getStyle('E52')->getNumberFormat()->setFormatCode($currencyPanama2);

	$fecha_ingreso = new DateTime('2014-11-24');
	$fecha_egreso  = new DateTime('2016-04-02');

	$anio_ing = $fecha_ingreso->format('Y');
	$anio_egr = $fecha_egreso->format('Y');
	$mes_ing  = $fecha_ingreso->format('n');
	$mes_egr  = $fecha_egreso->format('n');

	$i=33;
	for ($anio=$anio_ing; $anio<=$anio_egr ; $anio++) 
	{ 
		$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, 'Año ' . $anio);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':B'.$i);

		$mes_ini = ($anio==$anio_ing) ? $mes_ing : 1;
		$mes_fin = ($anio==$anio_egr) ? $mes_egr : 12;

		$i_aux = $i + 1;

		for ($mes=$mes_ini; $mes<=$mes_fin ; $mes++) 
		{ 
			$i++;

			$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, get_nombre_mes($mes-1, 'capitalize'))
										  ->setCellValue('B'.$i, 0);
		}

		$objPHPExcel->getActiveSheet()->getStyle('B'.$i_aux.':B'.$i)->getNumberFormat()->setFormatCode($currencyPanama);

		$i++;
	}

	if($i>33)
	{
		$objPHPExcel->getActiveSheet()->getStyle('A33:B'.$i)->applyFromArray(allBorders(PHPExcel_Style_Border::BORDER_THIN));

		$objPHPExcel->getActiveSheet()->setCellValue('B'.($i+1), '=SUM(B34:B'.$i.')' );
		$objPHPExcel->getActiveSheet()->getStyle('B'.($i+1))->getFont()->setSize(11)->setItalic(false)->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('B'.($i+1))->getNumberFormat()->setFormatCode($currencyPanama);

	}

	for ($row=1; $row<=$i; $row++) 
	{ 
		$objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(15);
	}


	//==============================================================================================

	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(14.95); // 14.29 + 0.66
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(13.95); // 13.29 + 0.66
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(11.37); // 10.71 + 0.66
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(13.37); // 12.71 + 0.66
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(11.37); // 10.71 + 0.66
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(11.37); // 10.71 + 0.66
	$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(10.8);  // 10.14 + 0.66

	//==============================================================================================
	// Ubicarse en la primera hoja

	// $objPHPExcel->setActiveSheetIndex(0);
	$objPHPExcel->getActiveSheet()->setSelectedCells('I43');

	// $filename = 'excel/Reporte de Liquidaciones.xlsx';

	// $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');	

	// $objWriter->save($filename);

	// echo $filename;

	//==============================================================================================
	// Redirect output to a client’s web browser (Excel2007)
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="Reporte de Liquidaciones.xlsx"');
	header('Cache-Control: max-age=0');
	// If you're serving to IE 9, then the following may be needed
	header('Cache-Control: max-age=1');
	// If you're serving to IE over SSL, then the following may be needed
	header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
	header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
	header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
	header ('Pragma: public'); // HTTP/1.0
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save('php://output');

}
else
{
   echo "<script>alert('Acceso Denegado');</script>";  
   echo "<script>document.location.href = 'config_rpt_liquidaciones_brentwood.php';</script>";
}

exit;