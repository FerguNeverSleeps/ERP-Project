<?php
require_once('../lib/database.php');
error_reporting(E_ALL);
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

	$objPHPExcel = new PHPExcel();

	$objPHPExcel->getProperties()->setCreator("Selectra Planilla")
								 ->setLastModifiedBy("Selectra Planilla")
								 ->setTitle("Reporte Servicios Profesionales");

	$sql = "SELECT LOWER(DATE_FORMAT(n.periodo_ini, ' de %M %Y')) as mes_inicio, 
	               DATE_FORMAT(n.periodo_ini, '%d') as dia_inicio, n.frecuencia as frecuencia
			FROM   nom_nominas_pago n 
			WHERE  n.codnom={$codnom} AND n.codtip={$_SESSION['codigo_nomina']}";
	$res = $db->query($sql);

	if($fila = $res->fetch_object())
	{
		$mes_inicio = traducir_mes($fila->mes_inicio); // de mayo 2016
		$mes_inicio = strtoupper($mes_inicio); // DE MAYO 2016

		if($fila->frecuencia == 2) // 1era Quincena
			$titulo_hoja0 = 'I' . $mes_inicio;
		else if($fila->frecuencia == 3) // 2da Quincena
			$titulo_hoja0 = 'II' . $mes_inicio;
		else
			$titulo_hoja0 = $fila->dia_inicio . $mes_inicio;
	}

	// Tipo de letra por defecto del Libro (Calibri, Cursiva, Tamaño 8)
	$objPHPExcel->getDefaultStyle()->getFont()->setName('Calibri')->setSize(8)->setItalic(true);

	// Obtener las variables y conceptos de la tabla caa_conceptos
	$conceptos = array();
	obtener_conceptos($conceptos);
	//==============================================================================================	
	// Contenido Hoja 0 => I DE MAYO

	// $objPHPExcel->getActiveSheet()->setCellValue('H1', 'PLANILLA SERVICIOS PROFESIONALES AGS')
	// 							  ->setCellValue('L1', '(AGS)')
	// 							  ->setCellValue('L2', $titulo_hoja0)
	// 							  ->setCellValue('R3', 'TOTAL')
	// 							  ->setCellValue('A4', 'CODIGO')
	// 							  ->setCellValue('B4', 'Documento de Identidad')
	// 							  ->setCellValue('C4', 'NOMBRE')
	// 							  ->setCellValue('J4', 'Total')
	// 							  ->setCellValue('L4', 'Total')
	// 							  ->setCellValue('N4', 'Total')
	// 							  ->setCellValue('O4', 'Total Horas')
	// 							  ->setCellValue('P4', 'Total')
	// 							  ->setCellValue('Q4', 'VALES')
	// 							  ->setCellValue('R4', 'PAGAR')
	// 							  ->setCellValue('D5', 'RATA X')
	// 							  ->setCellValue('E5', 'REGULAR')
	// 							  ->setCellValue('F5', 'TOTAL')
	// 							  ->setCellValue('G5', 'NOVENA')
	// 							  ->setCellValue('H5', 'T.NOVENA')
	// 							  ->setCellValue('I5', 'FERIADO')
	// 							  ->setCellValue('J5', 'Feriado')
	// 							  ->setCellValue('K5', 'Cumbre')
	// 							  ->setCellValue('L5', 'Cumbre')
	// 							  ->setCellValue('M5', 'DOMINGO')
	// 							  ->setCellValue('N5', 'Domingo')
	// 							  ->setCellValue('O5', 'Laboradas')
	// 							  ->setCellValue('P5', 'Bruto');

	// $objPHPExcel->getActiveSheet()->getStyle('A1:R5')->getFont()->setBold(true);
	// $objPHPExcel->getActiveSheet()->getStyle('A4:R16')->applyFromArray(allBorders(PHPExcel_Style_Border::BORDER_THIN));

	$objPHPExcel->getActiveSheet()->setCellValue('D19', 'PLANILLA SERVICIOS PROFESIONALES')
								  ->setCellValue('I19', 'BRENTWOOD')
								  ->setCellValue('N19', $titulo_hoja0)
								  ->setCellValue('R19', 'TOTAL')
								  ->setCellValue('A20', 'CODIGO')
								  ->setCellValue('B20', 'Documento de Identidad')
								  ->setCellValue('C20', 'NOMBRE')
								  ->setCellValue('J20', 'Total')
								  ->setCellValue('L20', 'Total')
								  ->setCellValue('N20', 'Total')
								  ->setCellValue('O20', 'Total Horas')
								  ->setCellValue('P20', 'Total')
								  ->setCellValue('Q20', 'VALES')
								  ->setCellValue('R20', 'PAGAR')
								  ->setCellValue('D21', 'RATA X')
								  ->setCellValue('E21', 'REGULAR')
								  ->setCellValue('F21', 'TOTAL')
								  ->setCellValue('G21', 'NOVENA')
								  ->setCellValue('H21', 'T.NOVENA')
								  ->setCellValue('I21', 'FERIADO')
								  ->setCellValue('J21', 'Feriado')
								  ->setCellValue('K21', 'Cumbre')
								  ->setCellValue('L21', 'Cumbre')
								  ->setCellValue('M21', 'DOMINGO')
								  ->setCellValue('N21', 'Domingo')
								  ->setCellValue('O21', 'Laboradas')
								  ->setCellValue('P21', 'Bruto');

	$objPHPExcel->getActiveSheet()->setCellValue('R17', '=SUM(R6:R16)');
	$objPHPExcel->getActiveSheet()->getStyle('R17')->getNumberFormat()->setFormatCode('"B/." * #,##0.00');

	$objPHPExcel->getActiveSheet()->getStyle('A19:R21')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('A20:R21')->applyFromArray(allBorders(PHPExcel_Style_Border::BORDER_THIN));

	$objPHPExcel->getActiveSheet()->getStyle('D3:R5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('R19')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('D20:R21')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

	//==============================================================================================	
	// Hoja de cálculo => DESGLOSE
	$objPHPExcel->createSheet();
	$objPHPExcel->setActiveSheetIndex(1);
	$objPHPExcel->getActiveSheet()->setTitle('DESGLOSE');

	$objPHPExcel->getActiveSheet()->getStyle('A1:U50')->getFont()->setItalic(false);

	$objPHPExcel->getActiveSheet()->getStyle('A1:U50')->applyFromArray(array('fill'=>array('type'=>PHPExcel_Style_Fill::FILL_SOLID,'color'=>array('rgb'=>'FFFFFF'))));

	$objPHPExcel->getActiveSheet()->setCellValue('F5', 'DESGLOSE DE BILLETES Y MONEDAS')
								  ->setCellValue('P5', 'QUINCENA')
								  ->setCellValue('C7', 'TRABAJADOR')
								  ->setCellValue('D7', 'CÓDIGO')
								  ->setCellValue('E7', 'SUELDO')
								  ->setCellValue('F7', 'BILLETES')
								  ->setCellValue('J7', 'MONEDAS')
								  ->setCellValue('O7', 'MONTO')
								  ->setCellValue('Q7', 'DETALLE EFECTIVO')
								  ->setCellValue('C8', 'SERVICIOS PROFESIONALES')
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

	// Escala área de impresión y orientación de la página
	//$objPHPExcel->getActiveSheet()->getPageSetup()->setScale(53);
	$objPHPExcel->getActiveSheet()->getPageSetup()->setScale(70);
	$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);

	// Nivel de zoom y Vista previa de salto de página	
	$objPHPExcel->getActiveSheet()->getSheetView()->setView(PHPExcel_Worksheet_SheetView::SHEETVIEW_PAGE_BREAK_PREVIEW);
	$objPHPExcel->getActiveSheet()->getSheetView()->setZoomScale(106);	

	//==============================================================================================	
	$objPHPExcel->setActiveSheetIndex(0);	

	$sql = "SELECT  p.ficha, p.cedula, p.suesal,
				    SUBSTRING_INDEX(p.nombres, ' ', 1) as primer_nombre,
 				    CASE WHEN SUBSTRING_INDEX(p.apellidos, ' ', 1)!='DE'    THEN SUBSTRING_INDEX(p.apellidos, ' ', 1)
 				         WHEN SUBSTRING_INDEX(p.apellidos, ' ', 2)!='DE LA' THEN SUBSTRING_INDEX(p.apellidos, ' ', 2) 
 				         ELSE SUBSTRING_INDEX(p.apellidos, ' ', 3) 
 				    END primer_apellido
		    FROM    nompersonal p
		    WHERE   p.tipemp='Contratado Servicios' 
		    AND     p.ficha IN (SELECT DISTINCT ficha FROM nom_movimientos_nomina WHERE codnom={$codnom})";
	$res = $db->query($sql);

	$i=22;
	$i7 = 9; // Variable i de la hoja 1 => DESGLOSE
	while($personal = $res->fetch_object())
	{
		$rata_x = $personal->suesal;

		$nombre_completo = $personal->primer_nombre.' '.$personal->primer_apellido;

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
		}	

		$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, $personal->ficha);
		$objPHPExcel->getActiveSheet()->setCellValue('B'.$i, $personal->cedula);
		$objPHPExcel->getActiveSheet()->setCellValue('C'.$i, $nombre_completo);
		$objPHPExcel->getActiveSheet()->setCellValue('D'.$i, $rata_x);
		$objPHPExcel->getActiveSheet()->setCellValue('E'.$i, ($horas_regular + $horas_extra));
		$objPHPExcel->getActiveSheet()->setCellValue('F'.$i, ($total_regular + $total_extra));
		$objPHPExcel->getActiveSheet()->setCellValue('G'.$i, $horas_novena);
		$objPHPExcel->getActiveSheet()->setCellValue('H'.$i, $total_novena);
		$objPHPExcel->getActiveSheet()->setCellValue('I'.$i, ($horas_nacional + $horas_extranac));
		$objPHPExcel->getActiveSheet()->setCellValue('J'.$i, ($total_nacional + $total_extranac));
		$objPHPExcel->getActiveSheet()->setCellValue('L'.$i, 0);
		$objPHPExcel->getActiveSheet()->setCellValue('M'.$i, $horas_domingo);
		$objPHPExcel->getActiveSheet()->setCellValue('N'.$i, $total_domingo);
		$objPHPExcel->getActiveSheet()->setCellValue('O'.$i, '=E'.$i.'+G'.$i.'+I'.$i.'+K'.$i.'+M'.$i);
		$objPHPExcel->getActiveSheet()->setCellValue('P'.$i, '=F'.$i.'+H'.$i.'+J'.$i.'+L'.$i.'+N'.$i);
		$objPHPExcel->getActiveSheet()->setCellValue('Q'.$i, ($total_otros == 0 ? '' : $total_otros));
		$objPHPExcel->getActiveSheet()->setCellValue('R'.$i, '=P'.$i.'-Q'.$i);

		$neto_colaborador = ($total_regular + $total_extra + $total_novena + $total_nacional + $total_extranac + $total_domingo) - 
		                    ($total_otros);

		$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':R'.$i)
							          ->applyFromArray(allBorders(PHPExcel_Style_Border::BORDER_THIN));

		$objPHPExcel->getActiveSheet()->getStyle('D'.$i)->getNumberFormat()->setFormatCode('"B/." * #,##0.00');
		$objPHPExcel->getActiveSheet()->getStyle('F'.$i)->getNumberFormat()->setFormatCode('"B/." * #,##0.00');
		$objPHPExcel->getActiveSheet()->getStyle('H'.$i)->getNumberFormat()->setFormatCode('"B/." * #,##0.00');
		$objPHPExcel->getActiveSheet()->getStyle('J'.$i)->getNumberFormat()->setFormatCode('"B/." * #,##0.00');
		$objPHPExcel->getActiveSheet()->getStyle('L'.$i)->getNumberFormat()->setFormatCode('"B/." * #,##0.00');
		$objPHPExcel->getActiveSheet()->getStyle('N'.$i)->getNumberFormat()->setFormatCode('"B/." * #,##0.00');
		$objPHPExcel->getActiveSheet()->getStyle('P'.$i)->getNumberFormat()->setFormatCode('"B/." * #,##0.00');
		$objPHPExcel->getActiveSheet()->getStyle('Q'.$i)->getNumberFormat()->setFormatCode('"B/." * #,##0.00');
		$objPHPExcel->getActiveSheet()->getStyle('R'.$i)->getNumberFormat()->setFormatCode('"B/." * #,##0.00');

		$objPHPExcel->getActiveSheet()->getStyle('E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('G'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('I'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('K'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('M'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('O'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);	

		//======================================================================================
		// Hoja de cálculo 7 => DESGLOSE
		$objPHPExcel->setActiveSheetIndex(1);

		desglosar_monedas_billetes($neto_colaborador, $cambio);

		$objPHPExcel->getActiveSheet()->setCellValue('C'.$i7, $nombre_completo)
									  ->setCellValue('D'.$i7, $personal->ficha)
									  ->setCellValue('E'.$i7, $neto_colaborador) // "='".$titulo_hoja0."'!R".($i-18))
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

		//======================================================================================
		$objPHPExcel->setActiveSheetIndex(0);
		$i++; $i7++;
	}

	if($i>22)
	{
		$objPHPExcel->getActiveSheet()->setCellValue('R'.$i, '=SUM(R22:R'.($i-1).')');
		$objPHPExcel->getActiveSheet()->getStyle('R'.$i)->getNumberFormat()->setFormatCode('"B/." * #,##0.00');
		$objPHPExcel->getActiveSheet()->getStyle('R'.$i)->getFont()->setSize(11);

		cellColor('I22:I'.($i-1), 'FFFF00');
		$objPHPExcel->getActiveSheet()->getStyle('A22:B'.($i-1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);		
		
	}

	for($j=1; $j<=$i; $j++)
	{
		$objPHPExcel->getActiveSheet()->getRowDimension($j)->setRowHeight(15);
	}

	$objPHPExcel->getActiveSheet()->getStyle('A1:R'.$j)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(7);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20); 
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(25);     
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(11);  
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(12); 
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(11); 
	$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(9);   
	$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(12); 
	$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(9);  
	$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(12);   
	$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(8); 
	$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(8);   
	$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(10);  
	$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(11); 
	$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(11); 
	$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(13); 
	$objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(11);
	$objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(18);        

	//==============================================================================================
	// Hoja de cálculo 1 => DESGLOSE
	$objPHPExcel->setActiveSheetIndex(1);

	if($i7>9)
	{
		$objPHPExcel->getActiveSheet()->getStyle('B9:O'.$i7)->getFont()->setSize(11);

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

		$objPHPExcel->getActiveSheet()->getStyle('E'.$i7)->getNumberFormat()->setFormatCode('"B/." * #,##0.00');
		$objPHPExcel->getActiveSheet()->getStyle('O'.$i7)->getNumberFormat()->setFormatCode('#,##0.00');
	}

	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(1); 
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(4); 
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(35); 
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(11); 
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(18); 
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(10); 
	$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(8); 
	$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(7);
	$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(8); 
	$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(7); 
	$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(7);
	$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(7); 
	$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(7); 
	$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(7); 
	$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(20); 
	$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(14); 
	$objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(11); 
	$objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(13); 
	$objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(6); 
	$objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(9); 
	$objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth(11); 
	//==============================================================================================
	//==============================================================================================
	// Ubicarse en la primera hoja

	$objPHPExcel->setActiveSheetIndex(0);
	$objPHPExcel->getActiveSheet()->setTitle($titulo_hoja0);

	$objPHPExcel->getActiveSheet()->removeRow(1,17);

	$objPHPExcel->getActiveSheet()->setSelectedCells('D145');

	$filename = str_replace(array("\"", "Á", "É", "Í", "Ó", "Ú", "á", "é", "í", "ó", "ú"), 
						    array('' , "A", "E", "I", "O", "U", "a", "e", "i", "o", "u"), 
						    $titulo_hoja0);

	$filename = 'excel/SERVICIO PROFESIONAL ' . $filename . '.xlsx';

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');	

	$objWriter->save($filename);

	echo $filename;
}
else
{
   echo "<script>alert('Acceso Denegado');</script>";  
   echo "<script>document.location.href = 'config_rpt_servicios_profesionales.php';</script>";
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

function traducir_mes($mes)
{
	$meses_in = array('january', 'february', 'march', 'april', 'may', 'june', 'july', 
					  'august', 'september', 'october', 'november', 'december');	

	$meses_es = array('enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio',
					  'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre');

	return str_replace($meses_in, $meses_es, $mes);	
}