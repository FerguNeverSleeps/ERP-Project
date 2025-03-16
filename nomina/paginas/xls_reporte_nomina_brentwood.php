<?php
require_once('../lib/database.php');
//error_reporting(E_ALL);
date_default_timezone_set('America/Panama');

ini_set("memory_limit", "-1");
set_time_limit(0);

if (PHP_SAPI == 'cli')
	die('Este script solo se debe ejecutar desde un navegador Web');

/** Include PHPExcel */
include('lib/php_excel.php');
require_once("phpexcel/Classes/PHPExcel.php");

if(isset($_POST['codnom']))
{
	$codnom = $_POST['codnom'];

	$db = new Database($_SESSION['bd']);

	$sql = "SELECT n.descrip, DATE_FORMAT(periodo_ini, ' de %M %Y') as fecha_inicio, DATE_FORMAT(periodo_ini, '%d') as dia_ini,
			       f.codfre as frecuencia
			FROM   nom_nominas_pago n 
			INNER JOIN nomfrecuencias f ON n.frecuencia=f.codfre
			WHERE n.codnom='{$codnom}' AND codtip = '{$_SESSION['codigo_nomina']}'";
	$res = $db->query($sql);

	if($obj = $res->fetch_object())
	{
		$fecha_inicio = traducir_mes(strtolower($obj->fecha_inicio));
		$fecha_inicio = strtoupper($fecha_inicio);

		if($obj->frecuencia == 2) // 1ra Quincena
			$titulo_hoja0 = 'I' . $fecha_inicio;
		else if ($obj->frecuencia == 3) // 2da Quincena
			$titulo_hoja0 = 'II' . $fecha_inicio;
		else
			$titulo_hoja0 = $obj->dia_ini . $fecha_inicio; 
	}

	$objPHPExcel = new PHPExcel();

	$objPHPExcel->getProperties()->setCreator("Selectra Planilla")
								 ->setLastModifiedBy("Selectra Planilla")
								 ->setTitle("Reporte de Planilla");

	// Tipo de letra por defecto de todo el libro
	$objPHPExcel->getDefaultStyle()->getFont()->setName('Calibri')->setSize(11); 

	//==============================================================================================
	//==============================================================================================
	// Creación de Hojas de Cálculo
	// Hoja de cálculo => COMPROBANTES ADV
	$objPHPExcel->createSheet();
	$objPHPExcel->setActiveSheetIndex(1);	
	$objPHPExcel->getActiveSheet()->setTitle('COMPROBANTES ADV');

	$objPHPExcel->getActiveSheet()->getSheetView()->setZoomScale(70);
	$objPHPExcel->getActiveSheet()->getPageSetup()->setScale(68);
	$objPHPExcel->getActiveSheet()->getSheetView()->setView(PHPExcel_Worksheet_SheetView::SHEETVIEW_PAGE_BREAK_PREVIEW);

	// Ancho de las columnas de la Hoja 1 
	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(17);
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(13);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(16);
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(17);
	$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(25);
	
	//==============================================================================================
	// Hoja de cálculo => EXTRAS	
	$objPHPExcel->createSheet();
	$objPHPExcel->setActiveSheetIndex(2);	
	$objPHPExcel->getActiveSheet()->setTitle('EXTRAS');	

	$objPHPExcel->getActiveSheet()->setCellValue('A1', 'PLANILLA BRENTWOOD')
								  ->setCellValue('A2', 'CORRESPONDE A LA ' . $titulo_hoja0)
								  ->setCellValue('A3', 1)
								  ->setCellValue('C3', 2)
								  ->setCellValue('D3', 3)
								  ->setCellValue('E3', 4)
								  ->setCellValue('F3', 5)
								  ->setCellValue('G3', 6)
								  ->setCellValue('H3', 7)
								  ->setCellValue('I3', 8)
								  ->setCellValue('J3', 9)
								  ->setCellValue('K3', 11)
								  ->setCellValue('L3', 12)
								  ->setCellValue('M3', 13)
								  ->setCellValue('N3', 14)
								  ->setCellValue('O3', 15)
								  ->setCellValue('A5', 'CODIGO')
								  ->setCellValue('B5', 'CEDULA')
								  ->setCellValue('C5', 'NOMBRE')
								  ->setCellValue('D5', 'RATA X')
								  ->setCellValue('E5', 'REGULAR')
								  ->setCellValue('F5', 'TOTAL')
								  ->setCellValue('G5', 'extras con recargo')
								  ->setCellValue('H5', 'T. NOVENA')
								  ->setCellValue('I5', 'FERIADO')
								  ->setCellValue('J5', 'T. FERIADO')
								  ->setCellValue('K5', 'DOMINGO')
								  ->setCellValue('L5', 'T. DOMINGO')
								  ->setCellValue('M5', 't. horas')
								  ->setCellValue('N5', 'BRUTO')
								  ->setCellValue('O5', 'BONO')
								  ->setCellValue('P5', 'NETO');

	$objPHPExcel->getActiveSheet()->mergeCells('A1:P1');
	$objPHPExcel->getActiveSheet()->mergeCells('A2:P2');

	cellColor('A5:P5', '0F243E');  // Celdas con color de fondo	
	colorTexto('A5:P5', 'FFFFFF'); // Celdas con color de texto diferente

	$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(10);

	$objPHPExcel->getActiveSheet()->getStyle('A1:P2')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('A5:P5')->getFont()->setBold(true);	

	// Celdas con texto alineado horizontalmente en el centro
	$objPHPExcel->getActiveSheet()->getStyle('A1:P5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);	

	//==============================================================================================
	// Hoja de cálculo => TOTAL A PAGAR AGS -BRENTWOOD
	$objPHPExcel->createSheet();
	$objPHPExcel->setActiveSheetIndex(3);	
	$objPHPExcel->getActiveSheet()->setTitle('TOTAL A PAGAR');	

	//==============================================================================================
	// Hoja de cálculo => ADMINISTRATIVO	
	$objPHPExcel->createSheet();
	$objPHPExcel->setActiveSheetIndex(4);	
	$objPHPExcel->getActiveSheet()->setTitle('ADMINISTRATIVO');		

	$objPHPExcel->getActiveSheet()->setCellValue('A2', 'ADMINISTRATIVOS BRENTWOOD ' . $titulo_hoja0)
								  ->setCellValue('D2', 'DETALLE PARA SOBRE DE EXTRAS')
								  ->setCellValue('A3', 'NOMBRE')
								  ->setCellValue('B3', 'REGULAR TOTAL')
								  ->setCellValue('C3', 'S/P TOTAL')
								  ->setCellValue('D3', 'SP')
								  ->setCellValue('E3', 'LIBRE')
								  ->setCellValue('F3', 'NACIONAL')
								  ->setCellValue('G3', 'EXTRA')
								  ->setCellValue('H3', 'BONO');

	$objPHPExcel->getActiveSheet()->getStyle('A2:H3')->getFont()->setName('Century Gothic')->setSize(11);

	// Celdas en cursiva
	$objPHPExcel->getActiveSheet()->getStyle('D2:H2')->getFont()->setItalic(true);

	// Celdas con texto alineado horizontalmente en el centro
	$objPHPExcel->getActiveSheet()->getStyle('B3:H3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

	// Celdas combinadas
	$objPHPExcel->getActiveSheet()->mergeCells('A2:C2');

	// Celdas en negrita
	$objPHPExcel->getActiveSheet()->getStyle('A3:H3')->getFont()->setBold(true);

	// Celdas con borde completo
	$objPHPExcel->getActiveSheet()->getStyle('A2:C2')->applyFromArray(borderExterno(PHPExcel_Style_Border::BORDER_MEDIUM));
	$objPHPExcel->getActiveSheet()->getStyle('D2:H2')->applyFromArray(borderExterno(PHPExcel_Style_Border::BORDER_MEDIUM));
	$objPHPExcel->getActiveSheet()->getStyle('A3:H3')->applyFromArray(allBorders(PHPExcel_Style_Border::BORDER_THIN));

	// Celdas con borde derecho
	$objPHPExcel->getActiveSheet()->getStyle('C3')->applyFromArray(borderRight(PHPExcel_Style_Border::BORDER_MEDIUM));

	// Ajustar Texto
	$objPHPExcel->getActiveSheet()->getStyle('A3:H3')->getAlignment()->setWrapText(true); 

	// Ancho de las columnas
	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(30); 
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(14); 
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(16); 
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(12); 
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(12); 
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(13); 
	$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(12); 
	$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(12); 

	//==============================================================================================
	// Hoja de cálculo => VALES	 
	$objPHPExcel->createSheet();
	$objPHPExcel->setActiveSheetIndex(5);	
	$objPHPExcel->getActiveSheet()->setTitle('VALES');	

	$objPHPExcel->getActiveSheet()->setCellValue('A3', 'VALES DESCONTADOS '.$titulo_hoja0);
	
	cellColor('A3:B3', '0F243E');  // Celdas con color de fondo	
	colorTexto('A3:B3', 'FFFFFF'); // Celdas con color de texto diferente

	$objPHPExcel->getActiveSheet()->mergeCells('A3:B3'); // Combinar celdas

	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(28);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(12);

	//==============================================================================================
	// Hoja de cálculo => Hoja 1 
	$objPHPExcel->createSheet();
	$objPHPExcel->setActiveSheetIndex(6);	
	$objPHPExcel->getActiveSheet()->setTitle('Hoja 1');

	$objPHPExcel->getActiveSheet()->setCellValue('A1', 'PLANILLA '.$titulo_hoja0.'-BRENTWOOD')
								  ->setCellValue('A2', 'CÓDIGO')
								  ->setCellValue('B2', 'NOMBRE')
								  ->setCellValue('C2', 'REGULAR')
								  ->setCellValue('D2', 'BRUTO EXTRA')
								  ->setCellValue('F2', 'TOTAL EXTRA');

	cellColor('A1', '0F243E');  // Celdas con color de fondo	
	colorTexto('A1', 'FFFFFF'); // Celdas con color de texto diferente

	$objPHPExcel->getActiveSheet()->getStyle('A2:F2')->getFont()->setBold(true);

	$objPHPExcel->getActiveSheet()->mergeCells('A1:F1');

	$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('C2:F2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

	// Nivel de zoom y Vista previa de salto de página
	$objPHPExcel->getActiveSheet()->getSheetView()->setZoomScale(76);	
	$objPHPExcel->getActiveSheet()->getSheetView()->setView(PHPExcel_Worksheet_SheetView::SHEETVIEW_PAGE_BREAK_PREVIEW);

	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(9);  
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(29); 
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(14); 
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15); 
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(13); 
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(16); 
	//==============================================================================================
	// Hoja de cálculo => DESGLOSE
	$objPHPExcel->createSheet();
	$objPHPExcel->setActiveSheetIndex(7);
	$objPHPExcel->getActiveSheet()->setTitle('DESGLOSE');

	$objPHPExcel->getActiveSheet()->getStyle('A1:U289')->applyFromArray(array('fill'=>array('type'=>PHPExcel_Style_Fill::FILL_SOLID,'color'=>array('rgb'=>'FFFFFF'))));


	$objPHPExcel->getActiveSheet()->setCellValue('F5', 'DESGLOSE DE BILLETES Y MONEDAS')
								  ->setCellValue('P5', 'QUINCENA')
								  ->setCellValue('C7', 'TRABAJADOR')
								  ->setCellValue('D7', 'CÓDIGO')
								  ->setCellValue('E7', 'SUELDO')
								  ->setCellValue('F7', 'BILLETES')
								  ->setCellValue('J7', 'MONEDAS')
								  ->setCellValue('O7', 'MONTO')
								  ->setCellValue('Q7', 'DETALLE EFECTIVO')
								  ->setCellValue('C8', 'REGULAR AGENTES')
								  ->setCellValue('F8', '20')
								  ->setCellValue('G8', '10')
								  ->setCellValue('H8', '5')
								  ->setCellValue('I8', '1')
								  ->setCellValue('J8', '50c')
								  ->setCellValue('K8', '25c')
								  ->setCellValue('L8', '10c')
								  ->setCellValue('M8', '5c')
								  ->setCellValue('N8', '1c')
								  ->setCellValue('Q8', 'Monto ')
								  ->setCellValue('S8', ' Unidades')
								  ->setCellValue('Q9', 20)
								  ->setCellValue('T9', 'Billetes de 20')
								  ->setCellValue('Q10', 10)
								  ->setCellValue('T10', 'Billetes de 10')
								  ->setCellValue('Q11', 5)
								  ->setCellValue('T11', 'Billetes de 5')
								  ->setCellValue('Q12', 1)
								  ->setCellValue('T12', 'Billetes de 1')
								  ->setCellValue('Q13', 0.50)
								  ->setCellValue('T13', 'Monedas de 50c')
								  ->setCellValue('Q14', 0.25)
								  ->setCellValue('T14', 'Monedas de 25c')
								  ->setCellValue('Q15', 0.10)
								  ->setCellValue('T15', 'Monedas de 10c')
								  ->setCellValue('Q16', 0.05)
								  ->setCellValue('T16', 'Monedas de 5c')
								  ->setCellValue('Q17', 0.01)
								  ->setCellValue('T17', 'Monedas de 1c')
								  ->setCellValue('Q19', 'TOTAL')
								  ->setCellValue('T19', '=SUM(R9:R18)');

	
	cellColor('C7:O8', '0F243E');   // Celdas con color de fondo	
	colorTexto('C7:U8', 'FFFFFF');  // Celdas con color de texto diferente

	cellColor('Q7:U8', '0F243E');   // Celdas con color de fondo

	cellColor('Q19:U19', '0F243E');  // Celdas con color de fondo
	colorTexto('Q19:U19', 'FFFFFF'); // Celdas con color de texto diferente

	$objPHPExcel->getActiveSheet()->mergeCells('F7:I7');
	$objPHPExcel->getActiveSheet()->mergeCells('J7:N7');
	$objPHPExcel->getActiveSheet()->mergeCells('Q7:U7');
	$objPHPExcel->getActiveSheet()->mergeCells('Q8:R8');
	$objPHPExcel->getActiveSheet()->mergeCells('S8:U8');
	$objPHPExcel->getActiveSheet()->mergeCells('Q19:S19');
	$objPHPExcel->getActiveSheet()->mergeCells('T18:U18');
	$objPHPExcel->getActiveSheet()->mergeCells('T19:U19');

	$objPHPExcel->getActiveSheet()->getStyle('C7:Q7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('Q19')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

	$objPHPExcel->getActiveSheet()->getStyle('Q8')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

	$objPHPExcel->getActiveSheet()->getStyle('F5')->getFont()->setName('Square721 BT')->setSize(18)->setUnderline(true);
	$objPHPExcel->getActiveSheet()->getStyle('P5')->getFont()->setName('Arial')->setSize(10);
	$objPHPExcel->getActiveSheet()->getStyle('C7:O8')->getFont()->setName('Square721 BT')->setSize(10);
	$objPHPExcel->getActiveSheet()->getStyle('Q7:T19')->getFont()->setName('Square721 BT')->setSize(10);
	$objPHPExcel->getActiveSheet()->getStyle('Q19:T19')->getFont()->setSize(12);

	$objPHPExcel->getActiveSheet()->getStyle('C7:U8')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('S9:S18')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('Q19:T19')->getFont()->setBold(true);

	$objPHPExcel->getActiveSheet()->getStyle('C7:O8')->applyFromArray(allBorders());
	$objPHPExcel->getActiveSheet()->getStyle('Q7:U19')->applyFromArray(allBorders());
	//$objPHPExcel->getActiveSheet()->getStyle('Q19')->applyFromArray(borderRight(PHPExcel_Style_Border::BORDER_NONE));

	$objPHPExcel->getActiveSheet()->getStyle('Q9:Q18')->getNumberFormat()->setFormatCode('"B/." * #,##0.00');
	$objPHPExcel->getActiveSheet()->getStyle('R9:R18')->getNumberFormat()->setFormatCode('#,##0.00');
	$objPHPExcel->getActiveSheet()->getStyle('T19')->getNumberFormat()->setFormatCode('#,##0.00');

	//==============================================================================================
	// Hoja de cálculo => I DE MES ANIO
	$objPHPExcel->setActiveSheetIndex(0);
	$objPHPExcel->getActiveSheet()->setTitle($titulo_hoja0);

	$objPHPExcel->getActiveSheet()->setCellValue('I1', 'PLANILLA BRENTWOOD')
								  ->setCellValue('Q1', 16)
								  ->setCellValue('R1', 17)
								  ->setCellValue('S1', 18)
								  ->setCellValue('T1', 19)
								  ->setCellValue('A2', 'CORRESPONDE A LA ' . $titulo_hoja0 )
								  ->setCellValue('R3', 'DEDUCCIONES')
								  ->setCellValue('N4', 'SALARIO')
								  ->setCellValue('U4', 'TOTAL')
								  ->setCellValue('V4', 'SALARIO')
								  ->setCellValue('A5', 'CODIGO')
								  ->setCellValue('B5', 'CEDULA')
								  ->setCellValue('C5', 'COMPROBANTE')
								  ->setCellValue('D5', 'NOMBRE')
								  ->setCellValue('E5', 'RATA X')
								  ->setCellValue('F5', 'REGULAR')
								  ->setCellValue('G5', 'TOTAL')
								  ->setCellValue('H5', 'NOVENA')
								  ->setCellValue('I5', 'T. NOVENA')
								  ->setCellValue('J5', 'NACIONAL')
								  ->setCellValue('K5', 'T. NACIONAL')
								  ->setCellValue('L5', 'Cumbre')
								  ->setCellValue('M5', 'Total Cumbre')
								  ->setCellValue('N5', 'DOMINGO')
								  ->setCellValue('O5', 'T. DOMINGO')
								  ->setCellValue('P5', 't. horas')
								  ->setCellValue('Q5', 'BRUTO')
								  ->setCellValue('R5', 'TARDANZAS')
								  ->setCellValue('S5', 'AUSENCIAS')
								  ->setCellValue('T5', 'T. DEVENGADO')
								  ->setCellValue('U5', 'SOCIAL')
								  ->setCellValue('V5', 'EDUCATIVO')
								  ->setCellValue('W5', 'T. AUSENCIAS')
								  ->setCellValue('X5', 'OTROS')
								  ->setCellValue('Y5', 'ACREEDOR')
								  ->setCellValue('Z5', 'ISR')
								  ->setCellValue('AA5', 'T. DESCUENTO')
								  ->setCellValue('AB5', 'NETO');

	
	cellColor('A5:AB5', '0F243E');  // Celdas con color de fondo	
	colorTexto('A5:AB5', 'FFFFFF'); // Celdas con color de texto diferente

	// Celdas combinadas (Merge)
	$objPHPExcel->getActiveSheet()->mergeCells('I1:M1');	
	$objPHPExcel->getActiveSheet()->mergeCells('A2:X2');
	$objPHPExcel->getActiveSheet()->mergeCells('R3:U3');

	$objPHPExcel->getActiveSheet()->getStyle('U4:V4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('E5:AB5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);							  

	// Celdas con texto en negrita
	$objPHPExcel->getActiveSheet()->getStyle('A1:T2')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('R3')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('A4:AB5')->getFont()->setBold(true);

	// Celdas con tamaño de texto diferente
	$objPHPExcel->getActiveSheet()->getStyle('E5:AB5')->getFont()->setSize(9); 
	$objPHPExcel->getActiveSheet()->getStyle('U4:V4')->getFont()->setSize(9); 

	// Celdas con texto alineado horizontalmente en el centro
	$objPHPExcel->getActiveSheet()->getStyle('I1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);	
	$objPHPExcel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);	
	$objPHPExcel->getActiveSheet()->getStyle('R3:U3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

	// Celdas contexto alineado verticalmente en el centro
	$objPHPExcel->getActiveSheet()->getStyle('A2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);	

	// Celdas con borde completo
	$objPHPExcel->getActiveSheet()->getStyle('R3:AA3')->applyFromArray(borderExterno());
	$objPHPExcel->getActiveSheet()->getStyle('R4:U4')->applyFromArray(borderExterno());
	$objPHPExcel->getActiveSheet()->getStyle('V4:AA4')->applyFromArray(borderExterno());

	$objPHPExcel->getActiveSheet()->getRowDimension(2)->setRowHeight(23); 

	$i=1; $letra='A';
	while($i<=17)
	{
		$objPHPExcel->getActiveSheet()->setCellValue($letra.'3', $i);

		if(in_array($i, array(7, 8, 9, 11, 12, 13, 14, 15, 16)))
			$objPHPExcel->getActiveSheet()->getStyle($letra.'3')->getFont()->setBold(true);

		$letra++; 
		$i++;		
	}

	//----------------------------------------------------------------------------------------------
	// Escala de color escalonada (Escala de 3 colores)
	$objConditional = new PHPExcel_Style_Conditional();

	$red    = new PHPExcel_Style_Color("F8696B"); // Valor más bajo
	$yellow = new PHPExcel_Style_Color("FFEB84"); // Percentil (Punto medio)
	$green  = new PHPExcel_Style_Color("63BE7B"); // Valor más alto

	$objConditional->setConditionType(PHPExcel_Style_Conditional::CONDITION_COLORSCALE)
				   ->setColorScaleStop(1,  $red,     PHPExcel_Style_Conditional::STOP_MIN,        0)
       			   ->setColorScaleStop(2,  $yellow,  PHPExcel_Style_Conditional::STOP_PERCENTILE, 50)
       			   ->setColorScaleStop(3,  $green,   PHPExcel_Style_Conditional::STOP_MAX,        100);
	//----------------------------------------------------------------------------------------------

	// Obtener las variables y conceptos de la tabla caa_conceptos
	$conceptos = array();
	obtener_conceptos($conceptos);

	// Consultar los departamentos disponibles en BRENTWOOD
	$sql = "SELECT codorg, descrip
			FROM   nomnivel1
			ORDER BY 1";
			// WHERE codorg IN (SELECT DISTINCT codnivel1 FROM nom_movimientos_nomina WHERE codnom={$codnom})

	$res = $db->query($sql);

	$i=6; 
	$i1 = 3; // Variable i de la hoja 1 => COMPROBANTES ADV
	$i2 = 6; // Variable i de la hoja 2 => EXTRAS	
	$i5 = 4; // Variable i de la hoja 5 => VALES
	$i6 = $i6_ant = 3; // Variable i de la hoja 6 => Hoja 1
	$i7 = 9; // Variable i de la hoja 7 => DESGLOSE

	$numero_comprobante = 1;
	$total_vales = 0;
	$extra_vigil = $extra_admin = '';
	$extras_personal = array();
	while($codnivel1 = $res->fetch_object())
	{
		$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, $codnivel1->descrip);
		$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':AB'.$i);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getFont()->setBold(true);
		cellColor('A'.$i.':AB'.$i, '0F243E');  // Color de fondo de las celdas
		colorTexto('A'.$i.':AB'.$i, 'FFFFFF'); // Color de texto

		$objPHPExcel->setActiveSheetIndex(2)->setCellValue('A'.$i2, $codnivel1->descrip);
		$objPHPExcel->setActiveSheetIndex(2)->mergeCells('A'.$i2.':P'.$i2);
		$objPHPExcel->setActiveSheetIndex(2)->getStyle('A'.$i2)->getFont()->setBold(true);
		cellColor('A'.$i2.':P'.$i2, '0F243E');  // Color de fondo de las celdas
		colorTexto('A'.$i2.':P'.$i2, 'FFFFFF'); // Color de texto

		if($codnivel1->codorg==2)
		{
			$objPHPExcel->setActiveSheetIndex(7)->setCellValue('C'.$i7, 'REGULAR ADMINISTRATIVOS');
			cellColor('B'.$i7.':O'.$i7, '0F243E');
			colorTexto('C'.$i7.':O'.$i7, 'FFFFFF');
			$objPHPExcel->getActiveSheet()->mergeCells('C'.$i7.':O'.$i7);
			$objPHPExcel->getActiveSheet()->getStyle('C'.$i7)->getFont()->setBold(true);

			$i++; $i2++; $i7++;

			$objPHPExcel->setActiveSheetIndex(0)->mergeCells('A'.$i.':AB'.$i);
			cellColor('A'.$i.':AB'.$i, '0F243E');


			$objPHPExcel->setActiveSheetIndex(2)->setCellValue('D'.$i2, 'SUELDO')
										  		->setCellValue('F'.$i2, 'SALARIO DIVIDIDO')
										  		->setCellValue('G'.$i2, 'EXTRAS')
										  		->setCellValue('H'.$i2, 'TOTAL EXTRAS')
										  		->setCellValue('I'.$i2, 'FERIADO')
										  		->setCellValue('J'.$i2, 'T.FERIADO')
										  		->setCellValue('K'.$i2, 'DOMINGO Y LIBRE')
										  		->setCellValue('L'.$i2, 'T.DOMINGO')
										  		->setCellValue('M'.$i2, 'TOTAL HORAS')
										  		->setCellValue('N'.$i2, 'BRUTO')
										  		->setCellValue('O'.$i2, 'BONO')
										  		->setCellValue('P'.$i2, 'NETO');

			cellColor('A'.$i2.':P'.$i2, '0F243E');  // Color de fondo de las celdas
			colorTexto('A'.$i2.':P'.$i2, 'FFFFFF'); // Color de texto

			$objPHPExcel->getActiveSheet()->mergeCells('A'.$i2.':C'.$i2);
			$objPHPExcel->getActiveSheet()->getStyle('A'.$i2.':P'.$i2)->getFont()->setBold(true)->setSize(8);
			$objPHPExcel->getActiveSheet()->getStyle('A'.$i2.':P'.$i2)->getAlignment()
																	  ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('A'.$i2.':P'.$i2)->applyFromArray(borderExterno());			
		}

		$objPHPExcel->setActiveSheetIndex(0);		

		$sql1 = "SELECT  p.ficha, p.cedula, p.suesal,
						 SUBSTRING_INDEX(p.nombres, ' ', 1) as primer_nombre,
		 				 CASE WHEN SUBSTRING_INDEX(p.apellidos, ' ', 1)!='DE'    THEN SUBSTRING_INDEX(p.apellidos, ' ', 1)
		 				      WHEN SUBSTRING_INDEX(p.apellidos, ' ', 2)!='DE LA' THEN SUBSTRING_INDEX(p.apellidos, ' ', 2) 
		 				      ELSE SUBSTRING_INDEX(p.apellidos, ' ', 3) 
		 				 END primer_apellido
				 FROM    nompersonal p
				 WHERE   p.codnivel1='{$codnivel1->codorg}' 
				 AND     p.tipemp<>'Contratado Servicios' 
				 AND     p.ficha IN (SELECT DISTINCT ficha FROM nom_movimientos_nomina WHERE codnom={$codnom})";

		$res1 = $db->query($sql1);

		$j=0;
		while($personal = $res1->fetch_object())
		{
			$i++; $i2++; 

			if($j==0) $i2_ant = $i2;

			$rata_x = $personal->suesal; // Si no es un administrativo

			// $horas_regular = $horas_domingo = $horas_nacional = $horas_tardanza = $horas_ausencia = 0;
			// $total_regular = $total_domingo = $total_nacional = $total_tardanza = $total_ausencia = 0;
			// $horas_extra = $horas_novena = $horas_extranac = $horas_extradom = $horas_extradmon = 0;
			// $total_extra = $total_novena = $total_extranac = $total_extradom = $total_extradmon = 0;
			// $total_ss = $total_se = $total_otros = $total_acreedores = $total_sueldodiv = $total_bono = 0;

			$sql2 = "";
			foreach ($conceptos as $concepto) 
			{
				if($sql2!="") $sql2 .= "UNION ";

				$sql2 .= "SELECT COALESCE(SUM(n.valor),0) as horas, COALESCE(SUM(n.monto),0) as total, 
				                 '{$concepto["variable"]}' as variable
				          FROM   nom_movimientos_nomina n
				          WHERE  n.codnom={$codnom} AND n.ficha={$personal->ficha} 
				          AND    {$concepto["condicion"]} ";
			}

			$res2 = $db->query($sql2);

			while($fila2 = $res2->fetch_object())
			{
				$var_horas = "horas_{$fila2->variable}";
				$var_total = "total_{$fila2->variable}";

				$$var_horas = $fila2->horas; // Variable de nombre variable (variables variables)
				$$var_total = $fila2->total; // Variable de nombre variable (variables variables)

				if($fila2->variable == "regular")
				{
					if($codnivel1->codorg == 2)
						$rata_x = $$var_total; // Condición solo para administrativos
				}
			}

			// A la variable otros se agrega el monto del impuesto sobre la renta
			// $total_otros += $total_isr;

			$nombre_completo = $personal->primer_nombre.' '.$personal->primer_apellido;

			$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, $personal->ficha)
										  ->setCellValue('B'.$i, $personal->cedula)
										  ->setCellValue('C'.$i, $numero_comprobante)
										  ->setCellValue('D'.$i, $nombre_completo)
										  ->setCellValue('E'.$i, $rata_x)
										  ->setCellValue('F'.$i, $horas_regular)
										  ->setCellValue('G'.$i, $total_regular)  // '=E'.$i.'*F'.$i
										  ->setCellValue('J'.$i, ($codnivel1->codorg==2 ? '' : $horas_nacional))
										  ->setCellValue('K'.$i, ($codnivel1->codorg==2 ? '' : $total_nacional)) // '=E'.$i.'*J'.$i.'*2.5'
										  ->setCellValue('P'.$i, ($codnivel1->codorg==2 ? '' : '=F'.$i.'+J'.$i)) // Horas Regular + Horas Nacional
										  ->setCellValue('Q'.$i, '=G'.$i.'+K'.$i) // Total Regular + Total Nacional
										  ->setCellValue('R'.$i, $horas_tardanza)
										  ->setCellValue('S'.$i, $horas_ausencia)
										  ->setCellValue('T'.$i, '=Q'.$i) // Bruto
										  ->setCellValue('U'.$i, $total_ss) // '=T'.$i.'*9.75%'
										  ->setCellValue('V'.$i, $total_se) // '=T'.$i.'*1.25%'
										  ->setCellValue('W'.$i, ($total_ausencia +  $total_tardanza))
										  ->setCellValue('X'.$i, ( $total_otros == 0       ? '' : $total_otros ))
										  ->setCellValue('Y'.$i, ( $total_acreedores == 0  ? '' : $total_acreedores ))
										  ->setCellValue('Z'.$i, ( $total_isr == 0  ? '' : $total_isr ))
										  ->setCellValue('AA'.$i, '=U'.$i.'+V'.$i.'+W'.$i.'+X'.$i.'+Y'.$i.'+Z'.$i) // Seguro Social + Seguro Educativo + Total Ausencias + Otros + Acreedores + ISR
										  ->setCellValue('AB'.$i, '=T'.$i.'-AA'.$i); // T. Devengado - T. Descuento

			$neto_colaborador = ($total_regular + ($codnivel1->codorg==2 ? 0 : $total_nacional) ) - 
			                    ($total_ss + $total_se + $total_ausencia +  $total_tardanza + $total_otros + $total_acreedores + $total_isr);

			$total_vales += $total_otros;

			if($j%2!=0)
				cellColor('A'.$i.':AA'.$i, 'D9D9D9');

			cellColor('X'.$i.':Z'.$i, 'CCECFF'); // Azul columnas otros, acreedores e isr

			//==========================================================================================
			//==========================================================================================
			// Con los datos de la primera hoja de cálculo debemos crear las otras hojas de cálculo
			// Hoja de cálculo 1 => COMPROBANTES ADV
			$objPHPExcel->setActiveSheetIndex(1);

			$objPHPExcel->getActiveSheet()
						->setCellValue('A'.($i1),    'BRENTWOOD')
						->setCellValue('A'.($i1+1),  $titulo_hoja0)
						->setCellValue('A'.($i1+3),  'COMPROBANTE DE PAGO No ')
						->setCellValue('G'.($i1+3),  "='".$titulo_hoja0."'!C".$i) // $numero_comprobante);
					    ->setCellValue('A'.($i1+5),  'CODIGO')
					    ->setCellValue('B'.($i1+5),	 "='".$titulo_hoja0."'!A".$i) // $personal->ficha
					    ->setCellValue('D'.($i1+5),  "='".$titulo_hoja0."'!D".$i) 
					    ->setCellValue('F'.($i1+5),  'CEDULA')
					    ->setCellValue('G'.($i1+5),  "='".$titulo_hoja0."'!B".$i) // $personal->cedula
						->setCellValue('A'.($i1+6),  'SALARIO BRUTO')
				    	->setCellValue('D'.($i1+6),  "='".$titulo_hoja0."'!Q".$i)  // "='".$titulo_hoja0."'!T".$i
					    ->setCellValue('A'.($i1+7),  'RxH')
					    ->setCellValue('D'.($i1+7),  ($codnivel1->codorg==2 ? 0 : "='".$titulo_hoja0."'!E".$i)) // $rata_x
					    ->setCellValue('F'.($i1+7),  'TOTAL HORAS ')
					    ->setCellValue('G'.($i1+7),  "='".$titulo_hoja0."'!P".$i)
					    ->setCellValue('A'.($i1+9),  'SALARIO')
					    ->setCellValue('E'.($i1+9),  'DEDUCCIONES')
					    ->setCellValue('A'.($i1+10), 'REGULAR')
					    ->setCellValue('D'.($i1+10),  "='".$titulo_hoja0."'!G".$i) // ($codnivel1->codorg==2 ? 0 : "='".$titulo_hoja0."'!G".$i)
					    ->setCellValue('E'.($i1+10), 'SEGURO SOCIAL')
					    ->setCellValue('G'.($i1+10), "='".$titulo_hoja0."'!U".$i)
					    ->setCellValue('A'.($i1+11), 'DOMINGO')
					    ->setCellValue('D'.($i1+11), 0) // "='EXTRAS'!L".$i2
					    ->setCellValue('E'.($i1+11), 'SEGURO EDUCATIVO')
					    ->setCellValue('G'.($i1+11), "='".$titulo_hoja0."'!V".$i)
					    ->setCellValue('A'.($i1+12), 'NACIONALES')
					    ->setCellValue('D'.($i1+12), "='".$titulo_hoja0."'!K".$i)		    
					    ->setCellValue('E'.($i1+12), 'ACREEDORES')
					    ->setCellValue('G'.($i1+12), "='".$titulo_hoja0."'!Y".$i)
					    ->setCellValue('E'.($i1+13), 'OTROS')
					    ->setCellValue('G'.($i1+13), "='".$titulo_hoja0."'!X".$i)
					    ->setCellValue('E'.($i1+14), 'IMPUESTO SOBRE LA RENTA')
					    ->setCellValue('G'.($i1+14), "='".$titulo_hoja0."'!Z".$i)		
					    ->setCellValue('E'.($i1+15), 'AUSENCIAS')
					    ->setCellValue('F'.($i1+15), '('.$horas_ausencia.')')
					    ->setCellValue('G'.($i1+15), $total_ausencia)
					    ->setCellValue('E'.($i1+16), 'TARDANZAS')
					    ->setCellValue('F'.($i1+16), '('.$horas_tardanza.')')
					    ->setCellValue('G'.($i1+16), $total_tardanza)
						->setCellValue('A'.($i1+17), 'TOTAL SALARIO')
					    ->setCellValue('D'.($i1+17), "='".$titulo_hoja0."'!T".$i) // "=D".($i1+10)."+D".($i1+11)."+D".($i1+12)
					    ->setCellValue('E'.($i1+17), 'TOTAL DEDUCCIONES')
					    ->setCellValue('G'.($i1+17), "='".$titulo_hoja0."'!AA".$i) //"+G".($i1+15)."+G".($i1+16))
					    ->setCellValue('E'.($i1+19), 'TOTAL NETO RECIBIDO:')
					    ->setCellValue('G'.($i1+19), '=D'.($i1+17).'-G'.($i1+17)) //"='".$titulo_hoja0."'!Z".$i
					    ->setCellValue('A'.($i1+22), 'RECIBIDO CONFORME ')
					    ->setCellValue('E'.($i1+22), 'FECHA ');			    			

			// Celdas con modificaciones en la fuente (tipo diferente, tamaño)
			$objPHPExcel->getActiveSheet()->getStyle('A'.($i1+3).':G'.($i1+3))->getFont()->setName('Arial');
			$objPHPExcel->getActiveSheet()->getStyle('A'.($i1+5).':G'.($i1+5))->getFont()->setName('Arial')->setSize(12);
			$objPHPExcel->getActiveSheet()->getStyle('A'.($i1+7).':G'.($i1+7))->getFont()->setName('Arial')->setSize(12);
			$objPHPExcel->getActiveSheet()->getStyle('A'.($i1+10).':A'.($i1+12))->getFont()->setName('Consolas');
			$objPHPExcel->getActiveSheet()->getStyle('E'.($i1+10).':E'.($i1+16))->getFont()->setName('Consolas');
			$objPHPExcel->getActiveSheet()->getStyle('A'.$i1)->getFont()->setName('Arial')->setSize(28)->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('A'.($i1+1))->getFont()->setSize(12)->setBold(true);	
			$objPHPExcel->getActiveSheet()->getStyle('A'.($i1+3))->getFont()->setSize(10)->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('G'.($i1+3))->getFont()->setSize(12);
			$objPHPExcel->getActiveSheet()->getStyle('G'.($i1+19))->getFont()->setSize(16);
			
			// Celdas combinadas 
			$objPHPExcel->getActiveSheet()->mergeCells('A'.$i1.':G'.$i1);
			$objPHPExcel->getActiveSheet()->mergeCells('A'.($i1+1).':G'.($i1+1));
			$objPHPExcel->getActiveSheet()->mergeCells('A'.($i1+3).':F'.($i1+3)); 
			$objPHPExcel->getActiveSheet()->mergeCells('A'.($i1+9).':C'.($i1+9)); 
			$objPHPExcel->getActiveSheet()->mergeCells('E'.($i1+9).':G'.($i1+9)); 

			// Celdas en negrita
			$objPHPExcel->getActiveSheet()->getStyle('A'.($i1+5))->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('F'.($i1+5))->getFont()->setBold(true);			
			$objPHPExcel->getActiveSheet()->getStyle('A'.($i1+6).':G'.($i1+9))->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('D'.($i1+10).':D'.($i1+16))->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('G'.($i1+10).':G'.($i1+16))->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('A'.($i1+17).':G'.($i1+22))->getFont()->setBold(true);

			// Celdas con borde completo 
			$objPHPExcel->getActiveSheet()->getStyle('G'.($i1+3))->applyFromArray(allBorders(PHPExcel_Style_Border::BORDER_MEDIUM));
			$objPHPExcel->getActiveSheet()->getStyle('B'.($i1+5))->applyFromArray(allBorders(PHPExcel_Style_Border::BORDER_MEDIUM));
			$objPHPExcel->getActiveSheet()->getStyle('G'.($i1+7))->applyFromArray(allBorders(PHPExcel_Style_Border::BORDER_MEDIUM));

			// Celdas con borde superior
			$objPHPExcel->getActiveSheet()->getStyle('A'.($i1+9).':G'.($i1+9))->applyFromArray(borderTop(PHPExcel_Style_Border::BORDER_MEDIUM));
			$objPHPExcel->getActiveSheet()->getStyle('A'.($i1+17).':G'.($i1+17))->applyFromArray(borderTop(PHPExcel_Style_Border::BORDER_MEDIUM));
		    $objPHPExcel->getActiveSheet()->getStyle('A'.($i1+20).':G'.($i1+20))->applyFromArray(borderTop(PHPExcel_Style_Border::BORDER_THICK));

			// Celdas con borde inferior
			$objPHPExcel->getActiveSheet()->getStyle('A'.($i1+1).':G'.($i1+1))->applyFromArray(borderBottom(PHPExcel_Style_Border::BORDER_THICK)); 
			$objPHPExcel->getActiveSheet()->getStyle('A'.($i1+20).':G'.($i1+20))->applyFromArray(borderBottom(PHPExcel_Style_Border::BORDER_THICK));
			$objPHPExcel->getActiveSheet()->getStyle('B'.($i1+22).':D'.($i1+22))->applyFromArray(borderBottom(PHPExcel_Style_Border::BORDER_THIN));
			$objPHPExcel->getActiveSheet()->getStyle('F'.($i1+22).':G'.($i1+22))->applyFromArray(borderBottom(PHPExcel_Style_Border::BORDER_THIN));			
			$objPHPExcel->getActiveSheet()->getStyle('A'.($i1+24).':G'.($i1+24))->applyFromArray(borderBottom(PHPExcel_Style_Border::BORDER_THICK));

			// Celdas con borde izquierdo
			$objPHPExcel->getActiveSheet()->getStyle('A'.($i1+9).':A'.($i1+20))->applyFromArray(borderLeft(PHPExcel_Style_Border::BORDER_MEDIUM));
			$objPHPExcel->getActiveSheet()->getStyle('E'.($i1+9).':E'.($i1+20))->applyFromArray(borderLeft(PHPExcel_Style_Border::BORDER_MEDIUM));
			
			// Celdas con borde derecho
			$objPHPExcel->getActiveSheet()->getStyle('G'.($i1+9).':G'.($i1+20))->applyFromArray(borderRight(PHPExcel_Style_Border::BORDER_MEDIUM));

			// Celdas con texto alineado al centro
			$objPHPExcel->getActiveSheet()->getStyle('A'.$i1.':G'.($i1+1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);	
			$objPHPExcel->getActiveSheet()->getStyle('G'.($i1+3).':G'.($i1+7))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);	
			$objPHPExcel->getActiveSheet()->getStyle('A'.($i1+9).':G'.($i1+9))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('F'.($i1+15).':F'.($i1+16))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

			// Celdas con texto alineado a la izquierda
			$objPHPExcel->getActiveSheet()->getStyle('B'.($i1+5))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

			// Celdas con texto alineado a la derecha
			$objPHPExcel->getActiveSheet()->getStyle('A'.($i1+3))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('E'.($i1+22))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

			// Formato Personalizado Celdas
			$objPHPExcel->getActiveSheet()->getStyle('D'.($i1+6))->getNumberFormat()->setFormatCode('"B/." * #,##0.00');
			$objPHPExcel->getActiveSheet()->getStyle('D'.($i1+10).':D'.($i1+17))->getNumberFormat()->setFormatCode('#,##0.00');
			$objPHPExcel->getActiveSheet()->getStyle('G'.($i1+10).':G'.($i1+19))->getNumberFormat()->setFormatCode('#,##0.00');	

			if(($i1+26)%64==0)
			{
				// Add a page break 64-65 / 128-129 / 192-193
				$objPHPExcel->getActiveSheet()->setBreak('A'.($i1+26), PHPExcel_Worksheet::BREAK_ROW);
			}
			else
			{	// 32-33 / 96-97 / 160-161 
				$objPHPExcel->getActiveSheet()->getStyle('A'.($i1+29).':G'.($i1+29))
				                              ->applyFromArray(borderBottom(PHPExcel_Style_Border::BORDER_MEDIUMDASHDOTDOT));
			}

			if($i1%2==0)
				$i1 += 29;
			else
				$i1 += 35; 
			//==========================================================================================
			//==========================================================================================
			// Hoja de cálculo 2 => EXTRAS
			$objPHPExcel->setActiveSheetIndex(2);

			$objPHPExcel->getActiveSheet()->setCellValue('A'.$i2, $personal->ficha)
										  ->setCellValue('B'.$i2, $personal->cedula)
										  ->setCellValue('C'.$i2, $nombre_completo)
										  ->setCellValue('D'.$i2, ($codnivel1->codorg==2 ? $total_regular : $rata_x))
										  ->setCellValue('E'.$i2, ($codnivel1->codorg==2 ? $horas_regular : $horas_extra))
										  ->setCellValue('F'.$i2, ($codnivel1->codorg==2 ? $total_sueldodiv : $total_extra))  // : '=D'.$i2.'*E'.$i2											  
										  ->setCellValue('G'.$i2, ($codnivel1->codorg==2 ? $horas_extradmon : $horas_novena))
										  ->setCellValue('H'.$i2, ($codnivel1->codorg==2 ? $total_extradmon : $total_novena)) // : '=D'.$i2.'*G'.$i2.'*1.25'						  
										  ->setCellValue('I'.$i2, ($codnivel1->codorg==2 ? ($horas_nacional + $horas_extranac) : $horas_extranac))
										  ->setCellValue('J'.$i2, ($codnivel1->codorg==2 ? ($total_nacional + $total_extranac) : $total_extranac)) // : '=D'.$i2.'*I'.$i2.'*2.5'
										  ->setCellValue('K'.$i2, ($horas_domingo + $horas_extradom)) 
										  ->setCellValue('L'.$i2, ($total_domingo + $total_extradom)) // '=D'.$i2.'*K'.$i2.'*1.5'
										  ->setCellValue('M'.$i2, ($codnivel1->codorg==2 ? '=G'.$i2.'+I'.$i2.'+K'.$i2 : '=E'.$i2.'+G'.$i2.'+I'.$i2.'+K'.$i2))
										  ->setCellValue('N'.$i2, '=F'.$i2.'+H'.$i2.'+J'.$i2.'+L'.$i2)
										  ->setCellValue('O'.$i2, $total_bono)
										  ->setCellValue('P'.$i2, '=N'.$i2.'+O'.$i2);

			$bruto_extras = $objPHPExcel->getActiveSheet()->getCell('N'.$i2)->getFormattedValue();
			$bono_extras  = $objPHPExcel->getActiveSheet()->getCell('O'.$i2)->getFormattedValue();

			$extras_personal[$codnivel1->codorg][] = array("nombre"=>$nombre_completo, "codigo"=>$personal->ficha, "neto_extras"=>($bruto_extras+$bono_extras));

			if($j%2!=0) cellColor('A'.$i2.':P'.$i2, 'D9D9D9');

			//======================================================================================
			// Hoja de cálculo 4 => Administrativos
			if($codnivel1->codorg==2)
			{
				$objPHPExcel->setActiveSheetIndex(4);		

				$objPHPExcel->getActiveSheet()->setCellValue('A'.($j+4), $nombre_completo)
											  ->setCellValue('B'.($j+4), "='".$titulo_hoja0."'!AB".$i)
											  ->setCellValue('C'.($j+4), "='EXTRAS'!P".$i2)
											  ->setCellValue('D'.($j+4), "='EXTRAS'!F".$i2)
											  ->setCellValue('E'.($j+4), "='EXTRAS'!L".$i2)
											  ->setCellValue('F'.($j+4), "='EXTRAS'!J".$i2)
											  ->setCellValue('G'.($j+4), "='EXTRAS'!H".$i2)
											  ->setCellValue('H'.($j+4), "='EXTRAS'!O".$i2);		
			}
			//======================================================================================
			// Hoja de cálculo 5 => VALES
			if($total_otros>0)
			{
				$objPHPExcel->setActiveSheetIndex(5);

				$objPHPExcel->getActiveSheet()->setCellValue('A'.$i5, $nombre_completo)
											  ->setCellValue('B'.$i5, $total_otros);
				$i5++;
			}
			//======================================================================================
			// Hoja de cálculo 6 => Hoja 1
			if($codnivel1->codorg!=2)
			{
				$objPHPExcel->setActiveSheetIndex(6);

				if($i6%45==0)
				{
					$objPHPExcel->getActiveSheet()->getStyle('A'.$i6_ant.':F'.($i6-1))->getFont()->setName('Century Gothic');

					// Add a page break
					$objPHPExcel->getActiveSheet()->setBreak('A'.($i6-1), PHPExcel_Worksheet::BREAK_ROW);

					$objPHPExcel->getActiveSheet()->setCellValue('A'.$i6, 'CÓDIGO')
												  ->setCellValue('B'.$i6, 'NOMBRE')
												  ->setCellValue('C'.$i6, 'REGULAR')
												  ->setCellValue('D'.$i6, 'BRUTO EXTRA')
												  ->setCellValue('F'.$i6, 'TOTAL EXTRA');
					cellColor('A'.$i6.':F'.$i6, '0F243E');  // Celdas con color de fondo	
					colorTexto('A'.$i6.':F'.$i6, 'FFFFFF'); // Celdas con color de texto diferente	

					$objPHPExcel->getActiveSheet()->getStyle('C'.$i6.':F'.$i6)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

					$i6++;	
					$i6_ant = $i6;			
				}

				$objPHPExcel->getActiveSheet()->setCellValue('A'.$i6, $personal->ficha)
											  ->setCellValue('B'.$i6, $nombre_completo)
											  ->setCellValue('C'.$i6, "='".$titulo_hoja0."'!AB".$i)
											  ->setCellValue('F'.$i6, "='EXTRAS'!P".$i2);

				if($bruto_extras > 0   &&   $bono_extras > 0)
				{
					$objPHPExcel->getActiveSheet()->setCellValue('D'.$i6, "='EXTRAS'!N".$i2)
												  ->setCellValue('E'.$i6, "='EXTRAS'!O".$i2);
				}

				$i6++;
			}
			//======================================================================================
			// Hoja de cálculo 7 => DESGLOSE
			$objPHPExcel->setActiveSheetIndex(7);

			desglosar_monedas_billetes($neto_colaborador, $cambio);

			$objPHPExcel->getActiveSheet()->setCellValue('C'.$i7, $nombre_completo)
										  ->setCellValue('D'.$i7, $personal->ficha)
										  ->setCellValue('E'.$i7, "='".$titulo_hoja0."'!AB".$i)
										  ->setCellValue('F'.$i7, $cambio[0])
										  ->setCellValue('G'.$i7, $cambio[1])
										  ->setCellValue('H'.$i7, $cambio[2])
										  ->setCellValue('I'.$i7, $cambio[3])
										  ->setCellValue('J'.$i7, $cambio[4])
										  ->setCellValue('K'.$i7, $cambio[5])
										  ->setCellValue('L'.$i7, $cambio[6])
										  ->setCellValue('M'.$i7, $cambio[7])
										  ->setCellValue('N'.$i7, $cambio[8])
										  ->setCellValue('O'.$i7, '=F'.$i7.'*20+G'.$i7.'*10+H'.$i7.'*5+I'.$i7.'*1+J'.$i7.'*0.5+K'.$i7.'*0.25+L'.$i7.'*0.1+M'.$i7.'*0.05+N'.$i7.'*0.01');
										  //->setCellValue('P'.$i7, ($cambio[0]*20+$cambio[1]*10+$cambio[2]*5+$cambio[3]*1+$cambio[4]*0.5+$cambio[5]*0.25+$cambio[6]*0.1+$cambio[7]*0.05+$cambio[8]*0.01));
	
			//======================================================================================
			$objPHPExcel->setActiveSheetIndex(0);
			$numero_comprobante++;
			$j++; $i7++;
		} // Fin while $personal 

		if($codnivel1->codorg==2)
		{
			$objPHPExcel->setActiveSheetIndex(4);

			$objPHPExcel->getActiveSheet()->setCellValue('B'.($j+4), '=SUM(B4:B'.($j+3).')')
										  ->setCellValue('C'.($j+4), '=SUM(C4:C'.($j+3).')');

			$objPHPExcel->getActiveSheet()->getStyle('A4:H'.($j+3))->getFont()->setName('Century Gothic');
			$objPHPExcel->getActiveSheet()->getStyle('B4:C'.($j+3))->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('B4:H'.($j+4))->getNumberFormat()->setFormatCode('"B/." * #,##0.00');				
			$objPHPExcel->getActiveSheet()->getStyle('A4:H'.($j+3))->applyFromArray(allBorders(PHPExcel_Style_Border::BORDER_THIN));	
			$objPHPExcel->getActiveSheet()->getStyle('C4:C'.($j+3))->applyFromArray(borderRight(PHPExcel_Style_Border::BORDER_MEDIUM));

			$objPHPExcel->getActiveSheet()->setSelectedCells('B'.($j+7));
		}

		$objPHPExcel->setActiveSheetIndex(2);
		$objPHPExcel->getActiveSheet()->setCellValue('Q'.$i2, '=SUM(P'.$i2_ant.':P'.$i2.')');
		$objPHPExcel->getActiveSheet()->getStyle('Q'.$i2)->getNumberFormat()->setFormatCode('"B/." * #,##0.00');

		if($codnivel1->codorg==1)
			$extra_vigil = "='EXTRAS'!Q".$i2;
		else
			$extra_admin = "='EXTRAS'!Q".$i2;	

		$objPHPExcel->setActiveSheetIndex(0);

		$i++; $i2++; 
	}

	$objPHPExcel->setActiveSheetIndex(7);

	foreach ($extras_personal as $codnivel1 => $departamento) 
	{
		$objPHPExcel->getActiveSheet()->setCellValue('C'.$i7, ($codnivel1==1 ? 'EXTRAS AGENTES BRENTWOOD' : 'EXTRAS ADMINISTRATIVOS'));
		cellColor('B'.$i7.':O'.$i7, '0F243E');
		colorTexto('C'.$i7.':O'.$i7, 'FFFFFF');
		$objPHPExcel->getActiveSheet()->mergeCells('C'.$i7.':O'.$i7);
		$objPHPExcel->getActiveSheet()->getStyle('C'.$i7)->getFont()->setBold(true);

		foreach ($departamento as $personal) 
		{
			$i7++;

			desglosar_monedas_billetes($personal["neto_extras"], $cambio);

			$objPHPExcel->getActiveSheet()->setCellValue('C'.$i7, $personal["nombre"])
										  ->setCellValue('D'.$i7, $personal["codigo"])
										  ->setCellValue('E'.$i7, $personal["neto_extras"])
										  ->setCellValue('F'.$i7, $cambio[0])
										  ->setCellValue('G'.$i7, $cambio[1])
										  ->setCellValue('H'.$i7, $cambio[2])
										  ->setCellValue('I'.$i7, $cambio[3])
										  ->setCellValue('J'.$i7, $cambio[4])
										  ->setCellValue('K'.$i7, $cambio[5])
										  ->setCellValue('L'.$i7, $cambio[6])
										  ->setCellValue('M'.$i7, $cambio[7])
										  ->setCellValue('N'.$i7, $cambio[8])
										  ->setCellValue('O'.$i7, '=F'.$i7.'*20+G'.$i7.'*10+H'.$i7.'*5+I'.$i7.'*1+J'.$i7.'*0.5+K'.$i7.'*0.25+L'.$i7.'*0.1+M'.$i7.'*0.05+N'.$i7.'*0.01');
		}

		$i7++;
	}

	$objPHPExcel->getActiveSheet()->setCellValue('C'.$i7, 'VALES');
	cellColor('B'.$i7.':O'.$i7, '0F243E');
	colorTexto('C'.$i7, 'FFFFFF');
	$objPHPExcel->getActiveSheet()->mergeCells('C'.$i7.':O'.$i7);
	$objPHPExcel->getActiveSheet()->getStyle('C'.$i7)->getFont()->setBold(true);

	$i7++;

	desglosar_monedas_billetes($total_vales, $cambio);

	$objPHPExcel->getActiveSheet()->setCellValue('C'.$i7, 'VALES Y PRÉSTAMOS')
								  ->setCellValue('E'.$i7, "='VALES'!B".$i5)
								  ->setCellValue('F'.$i7, $cambio[0])
								  ->setCellValue('G'.$i7, $cambio[1])
								  ->setCellValue('H'.$i7, $cambio[2])
								  ->setCellValue('I'.$i7, $cambio[3])
								  ->setCellValue('J'.$i7, $cambio[4])
								  ->setCellValue('K'.$i7, $cambio[5])
								  ->setCellValue('L'.$i7, $cambio[6])
								  ->setCellValue('M'.$i7, $cambio[7])
								  ->setCellValue('N'.$i7, $cambio[8])
								  ->setCellValue('O'.$i7, '=F'.$i7.'*20+G'.$i7.'*10+H'.$i7.'*5+I'.$i7.'*1+J'.$i7.'*0.5+K'.$i7.'*0.25+L'.$i7.'*0.1+M'.$i7.'*0.05+N'.$i7.'*0.01');
	$i7++;

	$objPHPExcel->getActiveSheet()->setCellValue('C'.$i7, 'RECLAMOS');
	cellColor('B'.$i7.':O'.$i7, '0F243E');
	colorTexto('C'.$i7, 'FFFFFF');
	$objPHPExcel->getActiveSheet()->mergeCells('C'.$i7.':O'.$i7);
	$objPHPExcel->getActiveSheet()->getStyle('C'.$i7)->getFont()->setBold(true);

	$i7++;

	$objPHPExcel->getActiveSheet()->setCellValue('C'.$i7, 'RECLAMOS')
								  ->setCellValue('E'.$i7, 0)
								  ->setCellValue('F'.$i7, 0)
								  ->setCellValue('G'.$i7, 0)
								  ->setCellValue('H'.$i7, 0)
								  ->setCellValue('I'.$i7, 0)
								  ->setCellValue('J'.$i7, 0)
								  ->setCellValue('K'.$i7, 0)
								  ->setCellValue('L'.$i7, 0)
								  ->setCellValue('M'.$i7, 0)
								  ->setCellValue('N'.$i7, 0)
								  ->setCellValue('O'.$i7, '=F'.$i7.'*20+G'.$i7.'*10+H'.$i7.'*5+I'.$i7.'*1+J'.$i7.'*0.5+K'.$i7.'*0.25+L'.$i7.'*0.1+M'.$i7.'*0.05+N'.$i7.'*0.01');
	$i7++;
	//----------------------------------------------------------------------------------------------
	// Escala de color escalonada (Escala de 3 colores)
	$objPHPExcel->setActiveSheetIndex(0);

	if($i>6)
	{
		$conditionalStyles = $objPHPExcel->getActiveSheet()->getStyle('AB7:AB'.$i)->getConditionalStyles();

		array_push($conditionalStyles, $objConditional);

		$objPHPExcel->getActiveSheet()->getStyle('AB7:AB'.$i)->setConditionalStyles($conditionalStyles);
		//----------------------------------------------------------------------------------------------	

		$objPHPExcel->getActiveSheet()->getStyle('A6:AB'.($i-1))->applyFromArray(allBorders());

		$objPHPExcel->getActiveSheet()->setCellValue('AB'.$i, '=SUM(AB7:AB'.($i-1).')');
		$objPHPExcel->getActiveSheet()->getStyle('AB'.$i)->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('AB'.$i)->applyFromArray(borderRight(PHPExcel_Style_Border::BORDER_THIN));

		$neto_hoja0 = "='".$titulo_hoja0."'!AB".$i;

		$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':AB'.$i)->applyFromArray(borderTop(PHPExcel_Style_Border::BORDER_DOUBLE));
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':AB'.$i)->applyFromArray(borderBottom(PHPExcel_Style_Border::BORDER_THIN));

		$objPHPExcel->getActiveSheet()->getSheetView()->setZoomScale(70);

		// Celdas en negrita
		$objPHPExcel->getActiveSheet()->getStyle('T7:T'.($i-1))->getFont()->setBold(true);

		// Celdas con formato de numero
		$objPHPExcel->getActiveSheet()->getStyle('E7:E'.($i-1))->getNumberFormat()->setFormatCode('"B/." * #,##0.00');
		$objPHPExcel->getActiveSheet()->getStyle('G7:G'.($i-1))->getNumberFormat()->setFormatCode('"B/." * #,##0.00');
		$objPHPExcel->getActiveSheet()->getStyle('K7:K'.($i-1))->getNumberFormat()->setFormatCode('"B/." * #,##0.00');
		$objPHPExcel->getActiveSheet()->getStyle('P7:Q'.($i-1))->getNumberFormat()->setFormatCode('"B/." * #,##0.00');
		$objPHPExcel->getActiveSheet()->getStyle('T7:W'.($i-1))->getNumberFormat()->setFormatCode('"B/." * #,##0.00');
		$objPHPExcel->getActiveSheet()->getStyle('AA7:AA'.($i-1))->getNumberFormat()->setFormatCode('"B/." * #,##0.00');

		$objPHPExcel->getActiveSheet()->getStyle('X7:Z'.($i-1))->getNumberFormat()->setFormatCode('#,##0.00');
		$objPHPExcel->getActiveSheet()->getStyle('AB7:AB'.$i)->getNumberFormat()->setFormatCode('#,##0.00');

		// Celdas alineadas a la izquierda
		$objPHPExcel->getActiveSheet()->getStyle('A7:B'.($i-1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

		// Celdas alineadas al centro
		$objPHPExcel->getActiveSheet()->getStyle('C7:C'.($i-1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('F7:F'.($i-1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('J7:J'.($i-1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('R7:S'.($i-1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	}

	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(11); 
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(14); 
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(12); 
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(39); 
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(17); 
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(14); 
	$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(16); 
	$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(0);  
	$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(0);   
	$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(16);
	$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(14); 
	$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(0);  
	$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(0);  
	$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(0);  
	$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(0);  
	$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(14); 
	$objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(16); 
	$objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(13);
	$objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(12); 
	$objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(20); 
	$objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth(14); 
	$objPHPExcel->getActiveSheet()->getColumnDimension('V')->setWidth(14); 
	$objPHPExcel->getActiveSheet()->getColumnDimension('W')->setWidth(14); 
	$objPHPExcel->getActiveSheet()->getColumnDimension('X')->setWidth(11); 
	$objPHPExcel->getActiveSheet()->getColumnDimension('Y')->setWidth(12);
	$objPHPExcel->getActiveSheet()->getColumnDimension('Z')->setWidth(12);  
	$objPHPExcel->getActiveSheet()->getColumnDimension('AA')->setWidth(15); 
	$objPHPExcel->getActiveSheet()->getColumnDimension('AB')->setWidth(21); 

	//==============================================================================================
	// Hoja de cálculo 2 => EXTRAS

	$objPHPExcel->setActiveSheetIndex(2);

	$objPHPExcel->getActiveSheet()->getStyle('A5:P'.($i2-1))->applyFromArray(allBorders());
	$objPHPExcel->getActiveSheet()->getStyle('P'.$i2)->applyFromArray(allBorders());

	$objPHPExcel->getActiveSheet()->setCellValue('P'.$i2, '=SUM(P7:P'.($i2-1).')');
	$objPHPExcel->getActiveSheet()->getStyle('P'.$i2)->getFont()->setBold(true);

	cellColor('P'.$i2, 'FFC7CE');  // Color de fondo de las celdas
	colorTexto('P'.$i2, '9C0006'); // Color de texto

	$objPHPExcel->getActiveSheet()->getStyle('A'.$i2.':P'.$i2)->applyFromArray(borderTop(PHPExcel_Style_Border::BORDER_DOUBLE));
	$objPHPExcel->getActiveSheet()->getStyle('A'.$i2.':P'.$i2)->applyFromArray(borderBottom(PHPExcel_Style_Border::BORDER_THIN));

	$objPHPExcel->getActiveSheet()->getSheetView()->setZoomScale(73);

	// Celdas con formato de numero
	$objPHPExcel->getActiveSheet()->getStyle('D7:D'.($i2-1))->getNumberFormat()->setFormatCode('"B/." * #,##0.00');
	$objPHPExcel->getActiveSheet()->getStyle('F7:F'.($i2-1))->getNumberFormat()->setFormatCode('"B/." * #,##0.00');
	$objPHPExcel->getActiveSheet()->getStyle('H7:H'.($i2-1))->getNumberFormat()->setFormatCode('"B/." * #,##0.00');
	$objPHPExcel->getActiveSheet()->getStyle('J7:J'.($i2-1))->getNumberFormat()->setFormatCode('"B/." * #,##0.00');
	$objPHPExcel->getActiveSheet()->getStyle('L7:O'.($i2-1))->getNumberFormat()->setFormatCode('"B/." * #,##0.00');
	$objPHPExcel->getActiveSheet()->getStyle('P7:P'.$i2)->getNumberFormat()->setFormatCode('#,##0.00');

	$objPHPExcel->getActiveSheet()->getStyle('A7:A'.($i2-1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	$objPHPExcel->getActiveSheet()->getStyle('E7:E'.($i2-1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('G7:G'.($i2-1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('I7:I'.($i2-1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('K7:K'.($i2-1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(9); 
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(14); 
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(26); 
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(13); 
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(16); 
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(18); 
	$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(21);
	$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(22); 
	$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(13);  
	$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(13); 
	$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(18); 
	$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(14); 
	$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(17);  
	$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(14); 
	$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(14);  
	$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(16); 
	$objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(15); 

	//==============================================================================================
	// Hoja de cálculo => TOTAL A PAGAR AGS -BRENTWOOD
	$objPHPExcel->setActiveSheetIndex(3);

	$objPHPExcel->getActiveSheet()
				->setCellValue('A2', 'BRENTWOOD')
				->setCellValue('A3', 'REGULAR')
				->setCellValue('C3', $neto_hoja0)
				->setCellValue('A4', 'EXTRAS')
				->setCellValue('C4', $extra_vigil)
				->setCellValue('A5', 'SERVICIOS PROFESIONALES')
				->setCellValue('C5', $extra_admin)
				->setCellValue('A6', 'VALES Y PRESTAMOS')
				->setCellValue('A7', 'RECLAMOS')
				->setCellValue('A8', 'TOTAL')
				->setCellValue('C8', '=SUM(C3:C7)');
				// ->setCellValue('A13', 'AGS')
				// ->setCellValue('A14', 'REGULAR')
				// ->setCellValue('A15', 'EXTRAS')
				// ->setCellValue('A16', 'SERVICIOS PROFESIONALES')
				// ->setCellValue('A17', 'VALES DESCONTADOS')
				// ->setCellValue('A18', 'RECLAMOS')
				// ->setCellValue('A19', 'TOTAL')
				// ->setCellValue('C19', '');

	// Celdas con color de fondo
	cellColor('A2:C2', '95B3D7'); // Azul
	//cellColor('A13:C13', 'DA9694'); // Granate

	// Celdas en negrita
	$objPHPExcel->getActiveSheet()->getStyle('A2:A8')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('C8')->getFont()->setBold(true)->setSize(12);
	//$objPHPExcel->getActiveSheet()->getStyle('A13:A19')->getFont()->setBold(true);
	//$objPHPExcel->getActiveSheet()->getStyle('C19')->getFont()->setBold(true)->setSize(14);

	// Celdas combinadas
	$objPHPExcel->getActiveSheet()->mergeCells('A2:C2');
	$objPHPExcel->getActiveSheet()->mergeCells('A3:B3');
	$objPHPExcel->getActiveSheet()->mergeCells('A4:B4');
	$objPHPExcel->getActiveSheet()->mergeCells('A5:B5');
	$objPHPExcel->getActiveSheet()->mergeCells('A6:B6');
	$objPHPExcel->getActiveSheet()->mergeCells('A7:B7');
	$objPHPExcel->getActiveSheet()->mergeCells('A8:B8');
	// $objPHPExcel->getActiveSheet()->mergeCells('A13:C13');
	// $objPHPExcel->getActiveSheet()->mergeCells('A14:B14');
	// $objPHPExcel->getActiveSheet()->mergeCells('A15:B15');
	// $objPHPExcel->getActiveSheet()->mergeCells('A16:B16');
	// $objPHPExcel->getActiveSheet()->mergeCells('A17:B17');
	// $objPHPExcel->getActiveSheet()->mergeCells('A18:B18');
	// $objPHPExcel->getActiveSheet()->mergeCells('A19:B19');

	// Celdas con borde completo
	$objPHPExcel->getActiveSheet()->getStyle('A2:C2')->applyFromArray(allBorders(PHPExcel_Style_Border::BORDER_MEDIUM));
	$objPHPExcel->getActiveSheet()->getStyle('A3:C8')->applyFromArray(allBorders(PHPExcel_Style_Border::BORDER_THIN));
	//$objPHPExcel->getActiveSheet()->getStyle('A13:C13')->applyFromArray(allBorders(PHPExcel_Style_Border::BORDER_MEDIUM));
	//$objPHPExcel->getActiveSheet()->getStyle('A14:C19')->applyFromArray(allBorders(PHPExcel_Style_Border::BORDER_THIN));

	// Celdas con formato de numero
	$objPHPExcel->getActiveSheet()->getStyle('C3:C8')->getNumberFormat()->setFormatCode('#,##0.00');
	//$objPHPExcel->getActiveSheet()->getStyle('C14:C19')->getNumberFormat()->setFormatCode('"B/." * #,##0.00');

	// Celdas alineadas horizontalmente al centro
	$objPHPExcel->getActiveSheet()->getStyle('A2:A19')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

	// Ancho de las columnas
	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(19); 
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(17); 
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(21); 
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(11); 
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(11); 
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(11); 
	$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(23); 
	$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(18); 

	// Nivel de zoom y Vista previa de salto de página
	$objPHPExcel->getActiveSheet()->getSheetView()->setZoomScale(98);	
	$objPHPExcel->getActiveSheet()->getSheetView()->setView(PHPExcel_Worksheet_SheetView::SHEETVIEW_PAGE_BREAK_PREVIEW);

	// Ajustar área de impresión
	//$objPHPExcel->getActiveSheet()->getPageSetup()->setScale(63);
	$objPHPExcel->getActiveSheet()->getPageSetup()->setPrintArea('A1:F40');

	$objPHPExcel->getActiveSheet()->setSelectedCells('F14');
	//----------------------------------------------------------------------------------------------
	// Borde hoja de cálculo => VALES
	$objPHPExcel->setActiveSheetIndex(5);

	$objPHPExcel->getActiveSheet()->getStyle('A3:B'.($i5-1))->applyFromArray(allBorders(PHPExcel_Style_Border::BORDER_THIN));

	if($i5>4)
	{
		$objPHPExcel->getActiveSheet()->setCellValue('B'.$i5, '=SUM(B4:B'.($i5-1).')');		

		$objPHPExcel->getActiveSheet()->getStyle('B4:B'.$i5)->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('B'.$i5)->getFont()->setSize(12);

		$objPHPExcel->getActiveSheet()->getStyle('B4:B'.($i5-1))->getNumberFormat()->setFormatCode('#,##0.00');
		$objPHPExcel->getActiveSheet()->getStyle('B'.$i5)->getNumberFormat()->setFormatCode('"B/." * #,##0.00');

		$objPHPExcel->setActiveSheetIndex(3)->setCellValue('C6', "='VALES'!B".$i5);
	}

	//----------------------------------------------------------------------------------------------

	$objPHPExcel->setActiveSheetIndex(6);

	// Escala área de impresión
	$objPHPExcel->getActiveSheet()->getPageSetup()->setScale(94);

	$objPHPExcel->getActiveSheet()->getStyle('A1:F'.($i6-1))->applyFromArray(allBorders(PHPExcel_Style_Border::BORDER_THIN));

	if($i6>3)
	{
		$objPHPExcel->getActiveSheet()->setCellValue('C'.$i6, '=SUM(C3:C'.($i6-1).')');
		$objPHPExcel->getActiveSheet()->setCellValue('F'.$i6, '=SUM(F3:F'.($i6-1).')');

		$objPHPExcel->getActiveSheet()->getStyle('C3:C'.($i6-1))->getNumberFormat()->setFormatCode('#,##0.00');
		$objPHPExcel->getActiveSheet()->getStyle('D3:E'.($i6-1))->getNumberFormat()->setFormatCode('"B/." * #,##0.00');
		$objPHPExcel->getActiveSheet()->getStyle('F3:F'.($i6-1))->getNumberFormat()->setFormatCode('#,##0.00');

		$objPHPExcel->getActiveSheet()->getStyle('C'.$i6.':F'.$i6)->getNumberFormat()->setFormatCode('"B/." * #,##0.00');
		$objPHPExcel->getActiveSheet()->getStyle('C'.$i6.':F'.$i6)->getFont()->setBold(true);

		$objPHPExcel->getActiveSheet()->getStyle('A'.$i6_ant.':F'.($i6-1))->getFont()->setName('Century Gothic');
	}

	//----------------------------------------------------------------------------------------------

	$objPHPExcel->setActiveSheetIndex(7);

	// Escala área de impresión
	//$objPHPExcel->getActiveSheet()->getPageSetup()->setScale(53);
	$objPHPExcel->getActiveSheet()->getPageSetup()->setScale(60);
	$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);

	// Nivel de zoom y Vista previa de salto de página	
	$objPHPExcel->getActiveSheet()->getSheetView()->setView(PHPExcel_Worksheet_SheetView::SHEETVIEW_PAGE_BREAK_PREVIEW);
	$objPHPExcel->getActiveSheet()->getSheetView()->setZoomScale(106);	

	if($i7>9)
	{
		$objPHPExcel->getActiveSheet()->getStyle('B9:O'.($i7-1))->applyFromArray(allBorders());

		$objPHPExcel->getActiveSheet()->getStyle('D9:D'.($i7-1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('F8:N'.($i7-1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

		$objPHPExcel->getActiveSheet()->setCellValue('R9',  '=SUM(F9:F'.($i7-1).')*Q9')
									  ->setCellValue('R10', '=SUM(G9:G'.($i7-1).')*Q10')
									  ->setCellValue('R11', '=SUM(H9:H'.($i7-1).')*Q11')
									  ->setCellValue('R12', '=SUM(I9:I'.($i7-1).')*Q12')
									  ->setCellValue('R13', '=SUM(J9:J'.($i7-1).')*Q13')
									  ->setCellValue('R14', '=SUM(K9:K'.($i7-1).')*Q14')
									  ->setCellValue('R15', '=SUM(L9:L'.($i7-1).')*Q15')
									  ->setCellValue('R16', '=SUM(M9:M'.($i7-1).')*Q16')
									  ->setCellValue('R17', '=SUM(N9:N'.($i7-1).')*Q17')
									  ->setCellValue('S9',  '=R9/Q9')
									  ->setCellValue('S10', '=R10/Q10')
									  ->setCellValue('S11', '=R11/Q11')
									  ->setCellValue('S12', '=R12/Q12')
									  ->setCellValue('S13', '=R13/Q13')
									  ->setCellValue('S14', '=R14/Q14')
									  ->setCellValue('S15', '=R15/Q15')
									  ->setCellValue('S16', '=R16/Q16')
									  ->setCellValue('S17', '=R17/Q17')
									  ->setCellValue('E'.$i7, '=SUM(E9:E'.($i7-1).')')
									  ->setCellValue('O'.$i7, '=SUM(O9:O'.($i7-1).')');
									  //->setCellValue('P'.$i7, '=SUM(O9:O'.($i7-1).')');

		$objPHPExcel->getActiveSheet()->getStyle('E'.$i7)->getNumberFormat()->setFormatCode('"B/." * #,##0.00');
		$objPHPExcel->getActiveSheet()->getStyle('O'.$i7)->getNumberFormat()->setFormatCode('#,##0.00');
	}

	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(1); 
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(4); 
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(33); 
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(11); 
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(18); 
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(9); 
	$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(7); 
	$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(6);
	$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(7); 
	$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(6); 
	$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(6);
	$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(6); 
	$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(6); 
	$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(6); 
	$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(17); 
	$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(12); 
	$objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(11); 
	$objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(13); 
	$objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(6); 
	$objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(9); 
	$objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth(11); 
	
	//==============================================================================================
	//==============================================================================================
	// Ubicarse en la primera hoja

	$objPHPExcel->setActiveSheetIndex(0);
	$objPHPExcel->getActiveSheet()->setSelectedCells('D145');

	$filename = str_replace(array("\"", "Á", "É", "Í", "Ó", "Ú", "á", "é", "í", "ó", "ú"), 
						    array('' , "A", "E", "I", "O", "U", "a", "e", "i", "o", "u"), 
						    $titulo_hoja0);

	$filename = 'excel/' . $filename . '.xlsx';

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');	

	$objWriter->save($filename);

	echo $filename;
}
else
{
   echo "<script>alert('Acceso Denegado');</script>";  
   echo "<script>document.location.href = 'config_rpt_nomina_brentwood.php';</script>";
}

exit;

// Obtener el desglose en billetes y monedas de una determinada cantidad en dólares
function desglosar_monedas_billetes($cantidad, &$cambio)
{
	$monedas = array(20, 10, 5, 1, 0.5, 0.25, 0.10, 0.05, 0.01);
	$cambio  = array(0, 0, 0, 0, 0, 0, 0, 0, 0);

	foreach ($monedas as $i=>$moneda) 
	{
		//echo "<br>Indice: $i => Moneda: $moneda <br>";
		$cambio[$i] = 0;

		while((round($cantidad-$moneda,2)>=0))
		{
			$cantidad = round(($cantidad - $moneda),2);
			$cambio[$i]++;	
		}
	}
}

// Obtener primero las variables y conceptos disponibles en la tabla caa_conceptos
function obtener_conceptos(&$conceptos)
{
	global $db;

	$sql = "SELECT variable, concepto FROM caa_conceptos ORDER BY variable";
	$res = $db->query($sql);

	while($fila = $res->fetch_object())
	{
		$condicion = '';

		// Puede ser un único concepto, varios conceptos separados por coma, o dos conceptos separados por dos puntos
		$tiene_comas = strpos($fila->concepto, ',');
		$tiene_rango = strpos($fila->concepto, ':');

		if($tiene_comas !== false)
		{
			$condicion = " n.codcon IN({$fila->concepto}) ";
		}
		else if($tiene_rango !== false)
		{
			$codcon = explode(':', $fila->concepto);

			$condicion = " n.codcon BETWEEN {$codcon[0]} AND {$codcon[1]} ";
		}
		else
		{
			$condicion = " n.codcon = {$fila->concepto} ";
		}

		$conceptos[] = array("variable" => $fila->variable, "condicion" => $condicion);
	}
}

function str_replace_first($from, $to, $subject)
{
    $from = '/'.preg_quote($from, '/').'/';

    return preg_replace($from, $to, $subject, 1);
}

function traducir_mes($mes)
{
	$meses_in = array('january', 'february', 'march', 'april', 'may', 'june', 'july', 
					  'august', 'september', 'october', 'november', 'december');	
	$meses_es = array('enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio',
					  'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre');

	return str_replace($meses_in, $meses_es, $mes);	
}