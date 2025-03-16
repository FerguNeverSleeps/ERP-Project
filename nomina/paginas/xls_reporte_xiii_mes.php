<?php
require_once('../lib/database.php');

error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('America/Panama');

ini_set("memory_limit", "-1");
set_time_limit(0);

if (PHP_SAPI == 'cli')
	die('Este ejemplo solo se debe ejecutar desde un navegador Web');

// Include PHPExcel
include('lib/php_excel.php');
include('lib/phpexcel_conceptos.php');
require_once('phpexcel/Classes/PHPExcel.php');

if(isset($_POST['codnom']))
{
	$codnom = $_POST['codnom']; // Segunda Quincena (Inicio del período) - Frecuencia 3

	$hoy = date('d/m/Y');

	$descrip_xiii = $nombre_periodo = '';

	$db = new Database($_SESSION['bd']);

	// Primero se debe obtener el mes y año de la planilla seleccionada como inicio del período (XIII Mes)
	$sql = "SELECT periodo_ini, anio, mes,
	               DATE_FORMAT(periodo_ini, '%d/%m/%Y') AS ini_xiii,
	    		   DATE_FORMAT(DATE_ADD(periodo_ini, INTERVAL 4 MONTH), '15/%m/%Y') AS fin_xiii
			FROM   nom_nominas_pago
			WHERE  codnom={$codnom}";
	$res = $db->query($sql);

	if($nomina = $res->fetch_object())
	{
		$fecha_ini = $nomina->periodo_ini; // Fecha Inicio del Periodo - XIII MES (Ej.: 2016-04-16)

		$anio = $nomina->anio;

		$meses[0]   = $nomina->mes - 1;    // Abril (2da quincena)

		if( $nomina->mes == 12 )  // Si el mes donde se inicia el periodo es diciembre (2da quincena)
		{
			$anio += 1;           // Agregar un año más para obtener los siguientes meses (enero, febrero, marzo, abril)
			$nomina->mes = 0;			
		}

		$meses[1] = $nomina->mes;     // Mayo   (1ra quincena + 2da quincena)
		$meses[2] = $nomina->mes + 1; // Junio  (1ra quincena + 2da quincena)
		$meses[3] = $nomina->mes + 2; // Julio  (1ra quincena + 2da quincena)
		$meses[4] = $nomina->mes + 3; // Agosto (1ra quincena)

		$descrip_xiii   = "PLANILLA REGULAR - XIII MES - DEL {$nomina->ini_xiii} AL {$nomina->fin_xiii}";
		$nombre_periodo = "Periodo del {$nomina->ini_xiii} al {$nomina->fin_xiii}";
	}

	$mes_reporte = get_nombre_mes($meses[4], 'uppercase');

	// Datos de la Empresa
	$sql     = "SELECT nom_emp, rif FROM nomempresa";
	$empresa = $db->query($sql)->fetch_object();

	// Obtener datos de los conceptos 
	$regular    = get_datos_concepto('regular');
	$sueldodiv  = get_datos_concepto('sueldodiv'); // Servicios Profesionales
	$xiiimes    = get_datos_concepto('xiiimes');
	$xiiimes_sp = get_datos_concepto('xiiimes_sp');
	$xiiimes_ss = get_datos_concepto('xiiimes_ss');

	//==============================================================================================
	$objPHPExcel = new PHPExcel();

	$objPHPExcel->getProperties()->setCreator("Selectra Planilla")
								 ->setLastModifiedBy("Selectra Planilla")
								 ->setTitle("Estimado XIII Mes");

	// Tipo de letra por defecto del Libro
	$objPHPExcel->getActiveSheet()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(11);

	//==============================================================================================
	$objPHPExcel->getActiveSheet()->setTitle('RESUMEN');

	$objPHPExcel->getActiveSheet()->getStyle('A2:B9')->getFont()->setName('Century Gothic');

	$objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('A9:B9')->getFont()->setBold(true);

	$objPHPExcel->getActiveSheet()->getStyle('A2:A9')->getFont()->setItalic(true);

	$objPHPExcel->getActiveSheet()->getStyle('A9')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

	$objPHPExcel->getActiveSheet()->getStyle('B5:B9')->getNumberFormat()->setFormatCode('"B/." * #,##0.00');

	cellColor( 'B9', '0F243E'); // Celdas con color de fondo	
	colorTexto('B9', 'FFFFFF'); // Celdas con color de texto diferente

	$objPHPExcel->getActiveSheet()->getStyle('A5:B9')->applyFromArray(allBorders());

	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(38);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);

	$objPHPExcel->getActiveSheet()->setCellValue('A2', 'ESTIMADO XIII MES CORRESPONDE A '.$mes_reporte.' '.$anio)
								  ->setCellValue('A5', 'BRENTWOOD')
								  ->setCellValue('A6', 'SERVICIOS PROFESIONALES')
								  ->setCellValue('A7', 'ADMINISTRATIVOS')
								  ->setCellValue('A8', 'ADMINISTRATIVOS(SERV PROF)')
								  ->setCellValue('A9', 'TOTAL BRENTWOOD')
								  ->setCellValue('B9', '=SUM(B5:B8)');	

	$total_hoja1 = $total_hoja2_1 = $total_hoja2_2 = $total_hoja3 = 0;

	//==============================================================================================
	$objPHPExcel->createSheet();
	$objPHPExcel->setActiveSheetIndex(1);	
	$objPHPExcel->getActiveSheet()->setTitle('BRENTWOOD');	

	$objPHPExcel->createSheet();
	$objPHPExcel->setActiveSheetIndex(2);	
	$objPHPExcel->getActiveSheet()->setTitle('ADMINISTRATIVOS BRENT');

	$objPHPExcel->createSheet();
	$objPHPExcel->setActiveSheetIndex(3);	
	$objPHPExcel->getActiveSheet()->setTitle('SERVICIOS PROFESIONALES');

	//==============================================================================================
	// Hoja de cálculo => DESGLOSE
	$objPHPExcel->createSheet();
	$objPHPExcel->setActiveSheetIndex(4);
	$objPHPExcel->getActiveSheet()->setTitle('DESGLOSE');	

	$objPHPExcel->getActiveSheet()->getStyle('A1:U289')->applyFromArray(array('fill'=>array('type'=>PHPExcel_Style_Fill::FILL_SOLID,'color'=>array('rgb'=>'FFFFFF'))));

	$objPHPExcel->getActiveSheet()->setCellValue('F5', 'DESGLOSE DE BILLETES Y MONEDAS')
								  ->setCellValue('C7', 'TRABAJADOR')
								  ->setCellValue('D7', 'CÓDIGO')
								  ->setCellValue('E7', 'XIII MES')
								  ->setCellValue('F7', 'BILLETES')
								  ->setCellValue('J7', 'MONEDAS')
								  ->setCellValue('O7', 'MONTO')
								  ->setCellValue('Q7', 'DETALLE EFECTIVO')
								  ->setCellValue('C8', 'BRENTWOOD')
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

	$objPHPExcel->getActiveSheet()->getStyle('Q9:Q18')->getNumberFormat()->setFormatCode('"B/." * #,##0.00');
	$objPHPExcel->getActiveSheet()->getStyle('R9:R18')->getNumberFormat()->setFormatCode('#,##0.00');
	$objPHPExcel->getActiveSheet()->getStyle('T19')->getNumberFormat()->setFormatCode('#,##0.00');	

	//==============================================================================================
	// Hoja de cálculo => RECIBOS
	$objPHPExcel->createSheet();
	$objPHPExcel->setActiveSheetIndex(5);
	$objPHPExcel->getActiveSheet()->setTitle('RECIBOS');	

	//$objPHPExcel->getActiveSheet()->getSheetView()->setZoomScale(70);
	$objPHPExcel->getActiveSheet()->getPageSetup()->setScale(80);
	$objPHPExcel->getActiveSheet()->getSheetView()->setView(PHPExcel_Worksheet_SheetView::SHEETVIEW_PAGE_BREAK_PREVIEW);

	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(11); 
	$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(11); 
	$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(11); 
	//==============================================================================================
	// Hoja de Cálculo => BRENTWOOD
	$objPHPExcel->setActiveSheetIndex(1);	

	$objPHPExcel->getActiveSheet()->getStyle('E2:M3')->getFont()->setSize(16)->setBold(true);

	$objPHPExcel->getActiveSheet()->getStyle('A5:M5')->getFont()->setSize(9)->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('E5:M5')->getFont()->setName('Arial');

	$objPHPExcel->getActiveSheet()->getStyle('E2:E3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('E5:M5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

	$objPHPExcel->getActiveSheet()->mergeCells('E2:M2');
	$objPHPExcel->getActiveSheet()->mergeCells('E3:M3');

	cellColor( 'A5:M5', '4F81BD'); // Celdas con color de fondo	
	colorTexto('A5:M5', 'FFFFFF'); // Celdas con color de texto diferente

	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(7);
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(13);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(26);
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(11);
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(11);
	$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(11);
	$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(11);
	$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(11);
	$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(11);
	$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(14);
	$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(11);
	$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(11);

	$objPHPExcel->getActiveSheet()->setCellValue('E2', 'XIII MES CORRESPONDE A '.$mes_reporte.' '.$anio)
					              ->setCellValue('E3', 'AGENCIA BRENTWOOD')
					              ->setCellValue('A5', 'No.')
					              ->setCellValue('B5', 'CODIGO')
					              ->setCellValue('C5', 'CEDULA')
					              ->setCellValue('D5', 'NOMBRE')
					              ->setCellValue('E5', 'II '. substr(get_nombre_mes($meses[0], 'uppercase'), 0, 3) )
					              ->setCellValue('F5', get_nombre_mes($meses[1], 'uppercase') )
					              ->setCellValue('G5', get_nombre_mes($meses[2], 'uppercase') )
					              ->setCellValue('H5', get_nombre_mes($meses[3], 'uppercase') )
					              ->setCellValue('I5', 'I '. substr(get_nombre_mes($meses[4], 'uppercase'), 0, 3) )
					              ->setCellValue('J5', 'TOTAL')
					              ->setCellValue('K5', '12')
					              ->setCellValue('L5', '7.25%')
					              ->setCellValue('M5', 'A PAGAR');

	$sql = "SELECT  p.ficha, p.cedula,
					SUBSTRING_INDEX(p.nombres, ' ', 1) AS primer_nombre,
	 				CASE WHEN SUBSTRING_INDEX(p.apellidos, ' ', 1)!='DE'    THEN SUBSTRING_INDEX(p.apellidos, ' ', 1)
	 				     WHEN SUBSTRING_INDEX(p.apellidos, ' ', 2)!='DE LA' THEN SUBSTRING_INDEX(p.apellidos, ' ', 2) 
	 				     ELSE SUBSTRING_INDEX(p.apellidos, ' ', 3) 
	 				END primer_apellido, p.suesal, p.fecing,
	 				(SELECT des_car FROM nomcargos WHERE cod_car=p.codcargo) AS cargo,
	 				DATE_FORMAT(p.fecing,'%d/%m/%Y') AS fecha_ingreso
			FROM    nompersonal p
			INNER JOIN nomcampos_adic_personal  n ON n.ficha=p.ficha
			WHERE   n.id=115 AND n.valor='SI'
			AND     p.codnivel1='1' 
			AND     p.tipemp<>'Contratado Servicios' AND p.estado<>'Egresado'
			ORDER BY p.ficha";
	$res = $db->query($sql);

	$i1=6; $cont=1;
	$i4=9;
	$i5=3; // Variable Hoja RECIBOS
	while($personal = $res->fetch_object())
	{
		$FICHA = $personal->ficha;

		$nombre_completo = $personal->primer_nombre.' '.$personal->primer_apellido;

		$total_mes0 = $total_mes1 = $total_mes2 = $total_mes3 = $total_mes4 = 0;

		$sueldo_actual = 208 * $personal->suesal; 		

		$total_mes  = xiii($fecha_ini, $personal->fecing, $sueldo_actual, $regular->codcon);

		foreach($total_mes as $i => $valor) 
		{
			$var_total  = "total_mes{$i}";
			$$var_total = $valor;
		}

		$objPHPExcel->getActiveSheet()->setCellValue('A'.$i1, $cont)
									  ->setCellValue('B'.$i1, $personal->ficha)
									  ->setCellValue('C'.$i1, $personal->cedula)
									  ->setCellValue('D'.$i1, $nombre_completo)
									  ->setCellValue('E'.$i1, $total_mes0)
									  ->setCellValue('F'.$i1, $total_mes1)
									  ->setCellValue('G'.$i1, $total_mes2)
									  ->setCellValue('H'.$i1, $total_mes3)
									  ->setCellValue('I'.$i1, $total_mes4)
									  ->setCellValue('J'.$i1, '=E'.$i1.'+F'.$i1.'+G'.$i1.'+H'.$i1.'+I'.$i1)
									  ->setCellValue('K'.$i1, '=IF(J'.$i1.'>0, J'.$i1.'/12, 0)') 
									  ->setCellValue('L'.$i1, '=K'.$i1.' * 7.25%')
									  ->setCellValue('M'.$i1, '=ROUND(K'.$i1.'-L'.$i1.', 2)');

		$total_xiiimes = $objPHPExcel->getActiveSheet()->getCell('M'.$i1)->getFormattedValue();

		$objPHPExcel->setActiveSheetIndex(4);

		desglosar_monedas_billetes($total_xiiimes, $cambio);

		$objPHPExcel->getActiveSheet()->setCellValue('C'.$i4, $nombre_completo)
									  ->setCellValue('D'.$i4, $personal->ficha)
									  ->setCellValue('E'.$i4, "='BRENTWOOD'!M".$i1) // $total_xiiimes
									  ->setCellValue('F'.$i4, $cambio[0])
									  ->setCellValue('G'.$i4, $cambio[1])
									  ->setCellValue('H'.$i4, $cambio[2])
									  ->setCellValue('I'.$i4, $cambio[3])
									  ->setCellValue('J'.$i4, $cambio[4])
									  ->setCellValue('K'.$i4, $cambio[5])
									  ->setCellValue('L'.$i4, $cambio[6])
									  ->setCellValue('M'.$i4, $cambio[7])
									  ->setCellValue('N'.$i4, $cambio[8])
									  ->setCellValue('O'.$i4, '=F'.$i4.'*20+G'.$i4.'*10+H'.$i4.'*5+I'.$i4.'*1+J'.$i4.'*0.5+K'.$i4.'*0.25+L'.$i4.'*0.1+M'.$i4.'*0.05+N'.$i4.'*0.01');
		$i4++;
		//============================================================================================================================
		// RECIBO

		$objPHPExcel->setActiveSheetIndex(5); //$i5 = 3;

		$objPHPExcel->getActiveSheet()->setCellValue('A'.$i5, $empresa->nom_emp)
									  ->setCellValue('I'.$i5, 'Fecha:')
									  ->setCellValue('J'.$i5, $hoy)
									  ->setCellValue('A'.($i5+1), $empresa->rif)
									  ->setCellValue('A'.($i5+2), 'RECIBO DE PAGO')
									  ->setCellValue('A'.($i5+4), 'Ficha:')
									  ->setCellValue('B'.($i5+4), $personal->ficha)
									  ->setCellValue('D'.($i5+4), 'Nombre:')
									  ->setCellValue('E'.($i5+4), $nombre_completo)
									  ->setCellValue('I'.($i5+4), 'Cédula:')
									  ->setCellValue('J'.($i5+4), $personal->cedula)
									  ->setCellValue('A'.($i5+5), 'Sueldo:')
									  ->setCellValue('B'.($i5+5), $personal->suesal)
									  ->setCellValue('F'.($i5+5), $descrip_xiii)
									  ->setCellValue('A'.($i5+6), 'Fecha de Ingreso:')
									  ->setCellValue('C'.($i5+6), $personal->fecha_ingreso) // '19/06/1990'
									  ->setCellValue('H'.($i5+6), $nombre_periodo) // 'Periodo del 16/04/2016 al 15/08/2016'
									  ->setCellValue('A'.($i5+7), 'Cargo:')
									  ->setCellValue('B'.($i5+7), $personal->cargo)
									  ->setCellValue('H'.($i5+7), '') //'Banco/Cuenta:-'
									  ->setCellValue('A'.($i5+9), 'Código')
									  ->setCellValue('B'.($i5+9), 'Descripción de Concepto')
									  ->setCellValue('E'.($i5+9), 'Ref')
									  ->setCellValue('G'.($i5+9), 'Asignación')
									  ->setCellValue('I'.($i5+9), 'Deducción')
									  ->setCellValue('K'.($i5+9), 'Saldo P.')
									  ->setCellValue('A'.($i5+10), $xiiimes->codcon)
									  ->setCellValue('B'.($i5+10), $xiiimes->descrip)
									  ->setCellValue('E'.($i5+10), 0.00)
									  ->setCellValue('G'.($i5+10), "='BRENTWOOD'!K".$i1)
									  ->setCellValue('K'.($i5+10), 0.00)
									  ->setCellValue('A'.($i5+11), $xiiimes_ss->codcon)
									  ->setCellValue('B'.($i5+11), $xiiimes_ss->descrip)
									  ->setCellValue('E'.($i5+11), 0.00)
									  ->setCellValue('I'.($i5+11), "='BRENTWOOD'!L".$i1)
									  ->setCellValue('K'.($i5+11), 0.00)
									  ->setCellValue('E'.($i5+12), 'Sub-Totales:')
									  ->setCellValue('G'.($i5+12), '=SUM(G'.($i5+10).':G'.($i5+11).')')
									  ->setCellValue('I'.($i5+12), '=SUM(I'.($i5+10).':I'.($i5+11).')')
									  ->setCellValue('E'.($i5+14), 'Neto a depositar Bs.:')
									  ->setCellValue('I'.($i5+14), '=G'.($i5+12).'-I'.($i5+12))
									  ->setCellValue('A'.($i5+17), 'RECIBE CONFORME')
									  ->setCellValue('G'.($i5+17), 'FECHA    ');

		$objPHPExcel->getActiveSheet()->mergeCells('A'.$i5.':F'.$i5);
		$objPHPExcel->getActiveSheet()->mergeCells('J'.$i5.':K'.$i5);
		$objPHPExcel->getActiveSheet()->mergeCells('A'.($i5+1).':C'.($i5+1));
		$objPHPExcel->getActiveSheet()->mergeCells('A'.($i5+2).':K'.($i5+2));
		$objPHPExcel->getActiveSheet()->mergeCells('E'.($i5+4).':G'.($i5+4));
		$objPHPExcel->getActiveSheet()->mergeCells('J'.($i5+4).':K'.($i5+4));
		$objPHPExcel->getActiveSheet()->mergeCells('F'.($i5+5).':K'.($i5+5));
		$objPHPExcel->getActiveSheet()->mergeCells('A'.($i5+6).':B'.($i5+6));
		$objPHPExcel->getActiveSheet()->mergeCells('H'.($i5+6).':K'.($i5+6));
		$objPHPExcel->getActiveSheet()->mergeCells('B'.($i5+7).':D'.($i5+7));
		$objPHPExcel->getActiveSheet()->mergeCells('H'.($i5+7).':K'.($i5+7));
		$objPHPExcel->getActiveSheet()->mergeCells('B'.($i5+9).':D'.($i5+9));
		$objPHPExcel->getActiveSheet()->mergeCells('E'.($i5+12).':F'.($i5+12));
		$objPHPExcel->getActiveSheet()->mergeCells('E'.($i5+14).':G'.($i5+14));
		$objPHPExcel->getActiveSheet()->mergeCells('A'.($i5+17).':C'.($i5+17));
		$objPHPExcel->getActiveSheet()->mergeCells('G'.($i5+17).':H'.($i5+17));

		$objPHPExcel->getActiveSheet()->getStyle('I'.$i5)->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('A'.($i5+2))->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('A'.($i5+4))->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('D'.($i5+4))->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('I'.($i5+4))->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('A'.($i5+5))->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('A'.($i5+6))->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('A'.($i5+7))->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('A'.($i5+9).':K'.($i5+9))->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('E'.($i5+12))->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('E'.($i5+14))->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('A'.($i5+17).':K'.($i5+17))->getFont()->setBold(true);

		$objPHPExcel->getActiveSheet()->getStyle('J'.$i5)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);	
		$objPHPExcel->getActiveSheet()->getStyle('A'.($i5+2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('J'.($i5+4))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);	
		$objPHPExcel->getActiveSheet()->getStyle('A'.($i5+9).':A'.($i5+11))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('E'.($i5+9).':E'.($i5+12))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('G'.($i5+9).':K'.($i5+9))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('E'.($i5+14))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A'.($i5+17))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

		$objPHPExcel->getActiveSheet()->getStyle('B'.($i5+4).':B'.($i5+5))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

		$objPHPExcel->getActiveSheet()->getStyle('I'.$i5.':I'.($i5+4))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$objPHPExcel->getActiveSheet()->getStyle('F'.($i5+5).':K'.($i5+7))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$objPHPExcel->getActiveSheet()->getStyle('G'.($i5+17))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

		$objPHPExcel->getActiveSheet()->getStyle('A'.($i5+4).':K'.($i5+7))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A'.($i5+9).':K'.($i5+9))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

		$objPHPExcel->getActiveSheet()->getStyle('B'.($i5+5))->getNumberFormat()->setFormatCode('#,##0.00');
		$objPHPExcel->getActiveSheet()->getStyle('G'.($i5+10).':K'.($i5+12))->getNumberFormat()->setFormatCode('#,##0.00');
		$objPHPExcel->getActiveSheet()->getStyle('I'.($i5+14))->getNumberFormat()->setFormatCode('#,##0.00');

		$objPHPExcel->getActiveSheet()->getStyle('A'.($i5+9).':K'.($i5+9))->applyFromArray(borderTop(PHPExcel_Style_Border::BORDER_THIN));	
		$objPHPExcel->getActiveSheet()->getStyle('G'.($i5+12).':I'.($i5+12))->applyFromArray(borderTop(PHPExcel_Style_Border::BORDER_THIN));

		$objPHPExcel->getActiveSheet()->getStyle('A'.($i5+9).':K'.($i5+9))->applyFromArray(borderBottom(PHPExcel_Style_Border::BORDER_THIN));
		$objPHPExcel->getActiveSheet()->getStyle('D'.($i5+17).':F'.($i5+17))->applyFromArray(borderBottom(PHPExcel_Style_Border::BORDER_THIN));
		$objPHPExcel->getActiveSheet()->getStyle('I'.($i5+17).':K'.($i5+17))->applyFromArray(borderBottom(PHPExcel_Style_Border::BORDER_THIN));

		$objPHPExcel->getActiveSheet()->getRowDimension(7)->setRowHeight(18);
		$objPHPExcel->getActiveSheet()->getRowDimension(8)->setRowHeight(18); // Alto: 18,00 (24px píxeles)  
		$objPHPExcel->getActiveSheet()->getRowDimension(9)->setRowHeight(18);
		$objPHPExcel->getActiveSheet()->getRowDimension(10)->setRowHeight(18);
		$objPHPExcel->getActiveSheet()->getRowDimension(12)->setRowHeight(21);

		if( ($i5-3) % 56 == 0 )
			$objPHPExcel->getActiveSheet()->setBreak('A'.($i5-3), PHPExcel_Worksheet::BREAK_ROW);

		if( ($i5+25) % 28 == 0)
		{
			$objPHPExcel->getActiveSheet()->getStyle('A'.($i5+25).':K'.($i5+25))->applyFromArray(borderBottom(PHPExcel_Style_Border::BORDER_MEDIUMDASHDOTDOT));	
			$i5 += 6;
		}

		$i5 += 25;
		//============================================================================================================================

		$objPHPExcel->setActiveSheetIndex(1);
		$i1++; $cont++;

		unset($FICHA);
	}

	if($i1>6)
	{
		$objPHPExcel->getActiveSheet()->getStyle('A5:M'.$i1)->applyFromArray(allBorders());
		$objPHPExcel->getActiveSheet()->getStyle('E6:M'.($i1-1))->getNumberFormat()->setFormatCode('#,##0.00');

		$objPHPExcel->getActiveSheet()->mergeCells('A'.$i1.':M'.$i1);
		cellColor('A'.$i1.':M'.$i1, '4F81BD'); // Celdas con color de fondo	

		$objPHPExcel->getActiveSheet()->getStyle('M'.($i1+2))->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->setCellValue('M'.($i1+2), '=SUM(M6:M'.($i1-1).')');
		$objPHPExcel->getActiveSheet()->getStyle('M'.($i1+2))->getNumberFormat()->setFormatCode('#,##0.00');		

		$total_hoja1 = "='BRENTWOOD'!M".($i1+2);
	}
	else
	{
		$objPHPExcel->getActiveSheet()->getStyle('A5:M6')->applyFromArray(allBorders());		
		$objPHPExcel->getActiveSheet()->getStyle('A6')->getFont()->setSize(10)->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('A6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->mergeCells('A6:M6');
		$objPHPExcel->getActiveSheet()->setCellValue('A6', 'No se encontraron datos');
		//$objPHPExcel->getActiveSheet()->setSelectedCells('A12');
	}

	$objPHPExcel->getActiveSheet()->setSelectedCells('A'.($i1+2));

	//==============================================================================================
	// Hoja de Cálculo => ADMINISTRATIVOS BRENT
	$objPHPExcel->setActiveSheetIndex(2);	

	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(7);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(19);
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(14);
	$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(11);
	$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(12);
	$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(11);
	$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(14);

	$objPHPExcel->getActiveSheet()->setCellValue('A1', 'PLANILLA DECIMO')
								  ->setCellValue('A2', 'ADMINISTRATIVO')
								  ->setCellValue('A3', substr(get_nombre_mes($meses[4]), 0, 3) .'-'. substr($anio, -2) )
								  ->setCellValue('A5', 'CODIGO')
								  ->setCellValue('B5', ' NOMBRE')
								  ->setCellValue('C5', '2-'. substr(get_nombre_mes($meses[0]), 0, 3) )
								  ->setCellValue('D5', get_nombre_mes($meses[1], 'capitalize') )
								  ->setCellValue('E5', get_nombre_mes($meses[2], 'capitalize') )
								  ->setCellValue('F5', get_nombre_mes($meses[3], 'capitalize') )
								  ->setCellValue('G5', '1-'. substr(get_nombre_mes($meses[4]), 0, 3) )
								  ->setCellValue('H5', 'TOTAL')
								  ->setCellValue('I5', '12')
								  ->setCellValue('J5', '7.25%')
								  ->setCellValue('K5', 'A PAGAR');

	$objPHPExcel->getActiveSheet()->mergeCells('A1:K1');
	$objPHPExcel->getActiveSheet()->mergeCells('A2:K2');
	$objPHPExcel->getActiveSheet()->mergeCells('A3:K3');

	$objPHPExcel->getActiveSheet()->getStyle('A1:K5')->getFont()->setSize(9)->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('A1:A3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

	$objPHPExcel->getActiveSheet()->getStyle('A1:A3')->getFont()->setItalic(true);

	$objPHPExcel->getActiveSheet()->getStyle('C5:K5')->getFont()->setName('Arial')->setItalic(true);
	$objPHPExcel->getActiveSheet()->getStyle('C5:K5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

	cellColor( 'A5:K5', '4F81BD'); // Celdas con color de fondo	
	colorTexto('A5:K5', 'FFFFFF'); // Celdas con color de texto diferente

	$objPHPExcel->setActiveSheetIndex(4)->setCellValue('C'.$i4, 'ADMINISTRATIVOS');
	cellColor('B'.$i4.':O'.$i4, '0F243E');
	colorTexto('C'.$i4.':O'.$i4, 'FFFFFF');
	$objPHPExcel->getActiveSheet()->mergeCells('C'.$i4.':O'.$i4);
	$objPHPExcel->getActiveSheet()->getStyle('C'.$i4)->getFont()->setBold(true);
	$i4++;

	$objPHPExcel->setActiveSheetIndex(2);

	$sql = "SELECT  p.ficha, p.cedula,
					SUBSTRING_INDEX(p.nombres, ' ', 1) AS primer_nombre,
	 				CASE WHEN SUBSTRING_INDEX(p.apellidos, ' ', 1)!='DE'    THEN SUBSTRING_INDEX(p.apellidos, ' ', 1)
	 				     WHEN SUBSTRING_INDEX(p.apellidos, ' ', 2)!='DE LA' THEN SUBSTRING_INDEX(p.apellidos, ' ', 2) 
	 				     ELSE SUBSTRING_INDEX(p.apellidos, ' ', 3) 
	 				END primer_apellido, p.suesal, p.fecing,
	 				(SELECT des_car FROM nomcargos WHERE cod_car=p.codcargo) AS cargo,
	 				DATE_FORMAT(p.fecing,'%d/%m/%Y') AS fecha_ingreso
			FROM    nompersonal p
			INNER JOIN nomcampos_adic_personal  n ON n.ficha=p.ficha
			WHERE   n.id=115 AND n.valor='SI'
			AND     p.codnivel1='2' 
			AND     p.tipemp<>'Contratado Servicios' AND p.estado<>'Egresado'
			ORDER BY p.ficha";
	$res = $db->query($sql);

	$i2=6; 
	while($personal = $res->fetch_object())
	{
		$FICHA  = $personal->ficha;

		$nombre_completo = $personal->primer_nombre.' '.$personal->primer_apellido;

		$total_mes0 = $total_mes1 = $total_mes2 = $total_mes3 = $total_mes4 = 0;

		$sueldo_actual = $personal->suesal;

		$total_mes = xiii($fecha_ini, $personal->fecing, $sueldo_actual, $regular->codcon);

		foreach($total_mes as $i => $valor) 
		{
			$var_total  = "total_mes{$i}";
			$$var_total = $valor;
		}		

		$objPHPExcel->getActiveSheet()->setCellValue('A'.$i2, $personal->ficha)
									  ->setCellValue('B'.$i2, $nombre_completo)
									  ->setCellValue('C'.$i2, $total_mes0)
									  ->setCellValue('D'.$i2, $total_mes1)
									  ->setCellValue('E'.$i2, $total_mes2)
									  ->setCellValue('F'.$i2, $total_mes3)
									  ->setCellValue('G'.$i2, $total_mes4)
									  ->setCellValue('H'.$i2, '=C'.$i2.'+D'.$i2.'+E'.$i2.'+F'.$i2.'+G'.$i2)	
									  ->setCellValue('I'.$i2, '=IF(H'.$i2.'>0, H'.$i2.'/12, 0)')
									  ->setCellValue('J'.$i2, '=I'.$i2.' * 7.25%') 
									  ->setCellValue('K'.$i2, '=ROUND(I'.$i2.'-J'.$i2.', 2)');

		$background_color = ($i2 % 2 == 0) ? 'B8CCE4' : 'DCE6F1';
		cellColor('A'.$i2.':K'.$i2, $background_color);

		$total_xiiimes = $objPHPExcel->getActiveSheet()->getCell('K'.$i2)->getFormattedValue();

		$objPHPExcel->setActiveSheetIndex(4);	

		desglosar_monedas_billetes($total_xiiimes, $cambio);

		$objPHPExcel->getActiveSheet()->setCellValue('C'.$i4, $nombre_completo)
									  ->setCellValue('D'.$i4, $personal->ficha)
									  ->setCellValue('E'.$i4, "='ADMINISTRATIVOS BRENT'!K".$i2) // $total_xiiimes 
									  ->setCellValue('F'.$i4, $cambio[0])
									  ->setCellValue('G'.$i4, $cambio[1])
									  ->setCellValue('H'.$i4, $cambio[2])
									  ->setCellValue('I'.$i4, $cambio[3])
									  ->setCellValue('J'.$i4, $cambio[4])
									  ->setCellValue('K'.$i4, $cambio[5])
									  ->setCellValue('L'.$i4, $cambio[6])
									  ->setCellValue('M'.$i4, $cambio[7])
									  ->setCellValue('N'.$i4, $cambio[8])
									  ->setCellValue('O'.$i4, '=F'.$i4.'*20+G'.$i4.'*10+H'.$i4.'*5+I'.$i4.'*1+J'.$i4.'*0.5+K'.$i4.'*0.25+L'.$i4.'*0.1+M'.$i4.'*0.05+N'.$i4.'*0.01');
		$i4++;
		//============================================================================================================================
		// RECIBO

		$objPHPExcel->setActiveSheetIndex(5);

		$objPHPExcel->getActiveSheet()->setCellValue('A'.$i5, $empresa->nom_emp)
									  ->setCellValue('I'.$i5, 'Fecha:')
									  ->setCellValue('J'.$i5, $hoy)
									  ->setCellValue('A'.($i5+1), $empresa->rif)
									  ->setCellValue('A'.($i5+2), 'RECIBO DE PAGO')
									  ->setCellValue('A'.($i5+4), 'Ficha:')
									  ->setCellValue('B'.($i5+4), $personal->ficha)
									  ->setCellValue('D'.($i5+4), 'Nombre:')
									  ->setCellValue('E'.($i5+4), $nombre_completo)
									  ->setCellValue('I'.($i5+4), 'Cédula:')
									  ->setCellValue('J'.($i5+4), $personal->cedula)
									  ->setCellValue('A'.($i5+5), 'Sueldo:')
									  ->setCellValue('B'.($i5+5), $personal->suesal)
									  ->setCellValue('F'.($i5+5), $descrip_xiii)
									  ->setCellValue('A'.($i5+6), 'Fecha de Ingreso:')
									  ->setCellValue('C'.($i5+6), $personal->fecha_ingreso) // '19/06/1990'
									  ->setCellValue('H'.($i5+6), $nombre_periodo) // 'Periodo del 16/04/2016 al 15/08/2016'
									  ->setCellValue('A'.($i5+7), 'Cargo:')
									  ->setCellValue('B'.($i5+7), $personal->cargo)
									  ->setCellValue('H'.($i5+7), '') // 'Banco/Cuenta:-'
									  ->setCellValue('A'.($i5+9), 'Código')
									  ->setCellValue('B'.($i5+9), 'Descripción de Concepto')
									  ->setCellValue('E'.($i5+9), 'Ref')
									  ->setCellValue('G'.($i5+9), 'Asignación')
									  ->setCellValue('I'.($i5+9), 'Deducción')
									  ->setCellValue('K'.($i5+9), 'Saldo P.')
									  ->setCellValue('A'.($i5+10), $xiiimes->codcon)
									  ->setCellValue('B'.($i5+10), $xiiimes->descrip)
									  ->setCellValue('E'.($i5+10), 0.00)
									  ->setCellValue('G'.($i5+10), "='ADMINISTRATIVOS BRENT'!I".$i2)
									  ->setCellValue('K'.($i5+10), 0.00)
									  ->setCellValue('A'.($i5+11), $xiiimes_ss->codcon)
									  ->setCellValue('B'.($i5+11), $xiiimes_ss->descrip)
									  ->setCellValue('E'.($i5+11), 0.00)
									  ->setCellValue('I'.($i5+11), "='ADMINISTRATIVOS BRENT'!J".$i2)
									  ->setCellValue('K'.($i5+11), 0.00)
									  ->setCellValue('E'.($i5+12), 'Sub-Totales:')
									  ->setCellValue('G'.($i5+12), '=SUM(G'.($i5+10).':G'.($i5+11).')')
									  ->setCellValue('I'.($i5+12), '=SUM(I'.($i5+10).':I'.($i5+11).')')
									  ->setCellValue('E'.($i5+14), 'Neto a depositar Bs.:')
									  ->setCellValue('I'.($i5+14), '=G'.($i5+12).'-I'.($i5+12))
									  ->setCellValue('A'.($i5+17), 'RECIBE CONFORME')
									  ->setCellValue('G'.($i5+17), 'FECHA    ');

		$objPHPExcel->getActiveSheet()->mergeCells('A'.$i5.':F'.$i5);
		$objPHPExcel->getActiveSheet()->mergeCells('J'.$i5.':K'.$i5);
		$objPHPExcel->getActiveSheet()->mergeCells('A'.($i5+1).':C'.($i5+1));
		$objPHPExcel->getActiveSheet()->mergeCells('A'.($i5+2).':K'.($i5+2));
		$objPHPExcel->getActiveSheet()->mergeCells('E'.($i5+4).':G'.($i5+4));
		$objPHPExcel->getActiveSheet()->mergeCells('J'.($i5+4).':K'.($i5+4));
		$objPHPExcel->getActiveSheet()->mergeCells('F'.($i5+5).':K'.($i5+5));
		$objPHPExcel->getActiveSheet()->mergeCells('A'.($i5+6).':B'.($i5+6));
		$objPHPExcel->getActiveSheet()->mergeCells('H'.($i5+6).':K'.($i5+6));
		$objPHPExcel->getActiveSheet()->mergeCells('B'.($i5+7).':D'.($i5+7));
		$objPHPExcel->getActiveSheet()->mergeCells('H'.($i5+7).':K'.($i5+7));
		$objPHPExcel->getActiveSheet()->mergeCells('B'.($i5+9).':D'.($i5+9));
		$objPHPExcel->getActiveSheet()->mergeCells('E'.($i5+12).':F'.($i5+12));
		$objPHPExcel->getActiveSheet()->mergeCells('E'.($i5+14).':G'.($i5+14));
		$objPHPExcel->getActiveSheet()->mergeCells('A'.($i5+17).':C'.($i5+17));
		$objPHPExcel->getActiveSheet()->mergeCells('G'.($i5+17).':H'.($i5+17));

		$objPHPExcel->getActiveSheet()->getStyle('I'.$i5)->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('A'.($i5+2))->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('A'.($i5+4))->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('D'.($i5+4))->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('I'.($i5+4))->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('A'.($i5+5))->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('A'.($i5+6))->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('A'.($i5+7))->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('A'.($i5+9).':K'.($i5+9))->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('E'.($i5+12))->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('E'.($i5+14))->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('A'.($i5+17).':K'.($i5+17))->getFont()->setBold(true);

		$objPHPExcel->getActiveSheet()->getStyle('J'.$i5)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);	
		$objPHPExcel->getActiveSheet()->getStyle('A'.($i5+2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);	
		$objPHPExcel->getActiveSheet()->getStyle('J'.($i5+4))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);	
		$objPHPExcel->getActiveSheet()->getStyle('A'.($i5+9).':A'.($i5+11))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('E'.($i5+9).':E'.($i5+12))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('G'.($i5+9).':K'.($i5+9))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('E'.($i5+14))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A'.($i5+17))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

		$objPHPExcel->getActiveSheet()->getStyle('B'.($i5+4).':B'.($i5+5))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

		$objPHPExcel->getActiveSheet()->getStyle('I'.$i5.':I'.($i5+4))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$objPHPExcel->getActiveSheet()->getStyle('F'.($i5+5).':K'.($i5+7))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$objPHPExcel->getActiveSheet()->getStyle('G'.($i5+17))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

		$objPHPExcel->getActiveSheet()->getStyle('A'.($i5+4).':K'.($i5+7))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A'.($i5+9).':K'.($i5+9))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

		$objPHPExcel->getActiveSheet()->getStyle('B'.($i5+5))->getNumberFormat()->setFormatCode('#,##0.00');
		$objPHPExcel->getActiveSheet()->getStyle('G'.($i5+10).':K'.($i5+12))->getNumberFormat()->setFormatCode('#,##0.00');
		$objPHPExcel->getActiveSheet()->getStyle('I'.($i5+14))->getNumberFormat()->setFormatCode('#,##0.00');

		$objPHPExcel->getActiveSheet()->getStyle('A'.($i5+9).':K'.($i5+9))->applyFromArray(borderTop(PHPExcel_Style_Border::BORDER_THIN));	
		$objPHPExcel->getActiveSheet()->getStyle('G'.($i5+12).':I'.($i5+12))->applyFromArray(borderTop(PHPExcel_Style_Border::BORDER_THIN));

		$objPHPExcel->getActiveSheet()->getStyle('A'.($i5+9).':K'.($i5+9))->applyFromArray(borderBottom(PHPExcel_Style_Border::BORDER_THIN));
		$objPHPExcel->getActiveSheet()->getStyle('D'.($i5+17).':F'.($i5+17))->applyFromArray(borderBottom(PHPExcel_Style_Border::BORDER_THIN));
		$objPHPExcel->getActiveSheet()->getStyle('I'.($i5+17).':K'.($i5+17))->applyFromArray(borderBottom(PHPExcel_Style_Border::BORDER_THIN));

		$objPHPExcel->getActiveSheet()->getRowDimension(7)->setRowHeight(18);
		$objPHPExcel->getActiveSheet()->getRowDimension(8)->setRowHeight(18); // Alto: 18,00 (24px píxeles)  
		$objPHPExcel->getActiveSheet()->getRowDimension(9)->setRowHeight(18);
		$objPHPExcel->getActiveSheet()->getRowDimension(10)->setRowHeight(18);
		$objPHPExcel->getActiveSheet()->getRowDimension(12)->setRowHeight(21);

		if( ($i5-3) % 56 == 0 )
			$objPHPExcel->getActiveSheet()->setBreak('A'.($i5-3), PHPExcel_Worksheet::BREAK_ROW);

		if( ($i5+25) % 28 == 0)
		{
			$objPHPExcel->getActiveSheet()->getStyle('A'.($i5+25).':K'.($i5+25))->applyFromArray(borderBottom(PHPExcel_Style_Border::BORDER_MEDIUMDASHDOTDOT));	
			$i5 += 6;
		}

		$i5 += 25;
		//============================================================================================================================

		$objPHPExcel->setActiveSheetIndex(2);
		$i2++;

		unset($FICHA);
	}

	if($i2>6)
	{
		$objPHPExcel->getActiveSheet()->getStyle('A5:K'.$i2)->applyFromArray(allBorders());
		$objPHPExcel->getActiveSheet()->getStyle('C6:K'.($i2-1))->getNumberFormat()->setFormatCode('#,##0.00');

		$objPHPExcel->getActiveSheet()->getStyle('A6:K'.$i2)->getFont()->setSize(9)->setItalic(true);

		$objPHPExcel->getActiveSheet()->mergeCells('B'.$i2.':J'.$i2);
		cellColor('A'.$i2.':K'.$i2, '4F81BD'); // Celdas con color de fondo	
		colorTexto('A'.$i2.':K'.$i2, 'FFFFFF');

		$objPHPExcel->getActiveSheet()->getStyle('A'.$i2.':K'.$i2)->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('K'.$i2)->getNumberFormat()->setFormatCode('"B/." * #,##0.00');

		$objPHPExcel->getActiveSheet()->setCellValue('A'.$i2, 'Total')
									  ->setCellValue('K'.$i2, '=SUM(K6:K'.($i2-1).')');

		$total_hoja2_1 = "='ADMINISTRATIVOS BRENT'!K".$i2;
	}
	else
	{		
		$objPHPExcel->getActiveSheet()->getStyle('A5:K6')->applyFromArray(allBorders());		
		$objPHPExcel->getActiveSheet()->getStyle('A6')->getFont()->setSize(10)->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('A6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->mergeCells('A6:K6');
		$objPHPExcel->getActiveSheet()->setCellValue('A6', 'No se encontraron datos');
		$i2++;
	}

	// ADMINISTRATIVOS BRENT - SERVICIO PROFESIONAL
	$i2 += 2; 

	$objPHPExcel->getActiveSheet()->setCellValue('A'.$i2, 'SERVICIO PROFESIONAL (Salario Dividido)')
								  ->setCellValue('A'.($i2+2), 'CODIGO')
								  ->setCellValue('B'.($i2+2), ' NOMBRE')
								  ->setCellValue('C'.($i2+2), '02-'. substr(get_nombre_mes($meses[0]), 0, 3) )
								  ->setCellValue('D'.($i2+2), get_nombre_mes($meses[1], 'capitalize') )
								  ->setCellValue('E'.($i2+2), get_nombre_mes($meses[2], 'capitalize') )
								  ->setCellValue('F'.($i2+2), get_nombre_mes($meses[3], 'capitalize') )
								  ->setCellValue('G'.($i2+2), '01-'. substr(get_nombre_mes($meses[4]), 0, 3) )
								  ->setCellValue('H'.($i2+2), 'TOTAL')
								  ->setCellValue('I'.($i2+2), '12')
								  ->setCellValue('J'.($i2+2), 'A PAGAR');

	$objPHPExcel->getActiveSheet()->mergeCells('A'.$i2.':J'.$i2);

	$objPHPExcel->getActiveSheet()->getStyle('A'.$i2.':J'.($i2+2))->getFont()->setSize(9)->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('A'.$i2)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('A'.$i2)->getFont()->setItalic(true);

	$objPHPExcel->getActiveSheet()->getStyle('C'.($i2+2).':J'.($i2+2))->getFont()->setName('Arial')->setItalic(true);
	$objPHPExcel->getActiveSheet()->getStyle('C'.($i2+2).':J'.($i2+2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

	cellColor( 'A'.($i2+2).':J'.($i2+2), '4F81BD'); // Celdas con color de fondo	
	colorTexto('A'.($i2+2).':J'.($i2+2), 'FFFFFF'); // Celdas con color de texto diferente

	$i2 += 3;
	$i2_aux = $i2;

	$objPHPExcel->setActiveSheetIndex(4)->setCellValue('C'.$i4, 'SERVICIOS PROFESIONALES ADMINISTRATIVOS');
	cellColor('B'.$i4.':O'.$i4, '0F243E');
	colorTexto('C'.$i4.':O'.$i4, 'FFFFFF');
	$objPHPExcel->getActiveSheet()->mergeCells('C'.$i4.':O'.$i4);
	$objPHPExcel->getActiveSheet()->getStyle('C'.$i4)->getFont()->setBold(true);
	$i4++;

	$objPHPExcel->setActiveSheetIndex(2);

	$sql = "SELECT  p.ficha, p.cedula,
					SUBSTRING_INDEX(p.nombres, ' ', 1) AS primer_nombre,
	 				CASE WHEN SUBSTRING_INDEX(p.apellidos, ' ', 1)!='DE'    THEN SUBSTRING_INDEX(p.apellidos, ' ', 1)
	 				     WHEN SUBSTRING_INDEX(p.apellidos, ' ', 2)!='DE LA' THEN SUBSTRING_INDEX(p.apellidos, ' ', 2) 
	 				     ELSE SUBSTRING_INDEX(p.apellidos, ' ', 3) 
	 				END primer_apellido, p.suesal, p.fecing,
	 				(SELECT des_car FROM nomcargos WHERE cod_car=p.codcargo) AS cargo,
	 				DATE_FORMAT(p.fecing,'%d/%m/%Y') AS fecha_ingreso
			FROM    nompersonal p
			WHERE   p.codnivel1='2' 
			AND     p.tipemp<>'Contratado Servicios' AND p.estado<>'Egresado'
			ORDER BY p.ficha";
	$res = $db->query($sql);

	while($personal = $res->fetch_object())
	{
		$FICHA = $personal->ficha;

		$nombre_completo = $personal->primer_nombre.' '.$personal->primer_apellido;

		$total_mes0 = $total_mes1 = $total_mes2 = $total_mes3 = $total_mes4 = 0;

		$sql2 = "SELECT valor 
		 		 FROM   nomcampos_adic_personal 
		 		 WHERE  id=1 AND ficha='{$FICHA}' AND tiponom={$_SESSION['codigo_nomina']}";
		$res2 = $db->query($sql2);

		if($fila2 = $res2->fetch_object())
		{
			$T01 = $fila2->valor;

			$total_mes = xiii($fecha_ini, $personal->fecing, $T01, $sueldodiv->codcon);

			if($T01 > 0)
			{
				foreach($total_mes as $i => $valor) 
				{
					$var_total  = "total_mes{$i}";
					$$var_total = $valor;
				}
			}
		}

		$total_meses = $total_mes0 + $total_mes1 + $total_mes2 + $total_mes3 + $total_mes4;

		if( $total_meses > 0 )
		{
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$i2, $personal->ficha)
										  ->setCellValue('B'.$i2, $nombre_completo)
										  ->setCellValue('C'.$i2, $total_mes0)
										  ->setCellValue('D'.$i2, $total_mes1)
										  ->setCellValue('E'.$i2, $total_mes2)
										  ->setCellValue('F'.$i2, $total_mes3)
										  ->setCellValue('G'.$i2, $total_mes4)
										  ->setCellValue('H'.$i2, '=C'.$i2.'+D'.$i2.'+E'.$i2.'+F'.$i2.'+G'.$i2)
										  ->setCellValue('I'.$i2, '=IF(H'.$i2.'>0, H'.$i2.'/12, 0)')
										  ->setCellValue('J'.$i2, '=ROUND(I'.$i2.', 2)');

			$total_xiiimes = $objPHPExcel->getActiveSheet()->getCell('J'.$i2)->getFormattedValue();
			
			$objPHPExcel->setActiveSheetIndex(4);

			desglosar_monedas_billetes($total_xiiimes, $cambio);

			$objPHPExcel->getActiveSheet()->setCellValue('C'.$i4, $nombre_completo)
										  ->setCellValue('D'.$i4, $personal->ficha)
										  ->setCellValue('E'.$i4, "='ADMINISTRATIVOS BRENT'!J".$i2) // $total_xiiimes
										  ->setCellValue('F'.$i4, $cambio[0])
										  ->setCellValue('G'.$i4, $cambio[1])
										  ->setCellValue('H'.$i4, $cambio[2])
										  ->setCellValue('I'.$i4, $cambio[3])
										  ->setCellValue('J'.$i4, $cambio[4])
										  ->setCellValue('K'.$i4, $cambio[5])
										  ->setCellValue('L'.$i4, $cambio[6])
										  ->setCellValue('M'.$i4, $cambio[7])
										  ->setCellValue('N'.$i4, $cambio[8])
										  ->setCellValue('O'.$i4, '=F'.$i4.'*20+G'.$i4.'*10+H'.$i4.'*5+I'.$i4.'*1+J'.$i4.'*0.5+K'.$i4.'*0.25+L'.$i4.'*0.1+M'.$i4.'*0.05+N'.$i4.'*0.01');
			$i4++;
			//============================================================================================================================
			// RECIBO

			$objPHPExcel->setActiveSheetIndex(5);

			$objPHPExcel->getActiveSheet()->setCellValue('A'.$i5, $empresa->nom_emp)
										  ->setCellValue('I'.$i5, 'Fecha:')
										  ->setCellValue('J'.$i5, $hoy)
										  ->setCellValue('A'.($i5+1), $empresa->rif)
										  ->setCellValue('A'.($i5+2), 'RECIBO DE PAGO')
										  ->setCellValue('A'.($i5+4), 'Ficha:')
										  ->setCellValue('B'.($i5+4), $personal->ficha)
										  ->setCellValue('D'.($i5+4), 'Nombre:')
										  ->setCellValue('E'.($i5+4), $nombre_completo)
										  ->setCellValue('I'.($i5+4), 'Cédula:')
										  ->setCellValue('J'.($i5+4), $personal->cedula)
										  ->setCellValue('A'.($i5+5), 'Sueldo:')
										  ->setCellValue('B'.($i5+5), $personal->suesal)
										  ->setCellValue('F'.($i5+5), $descrip_xiii)
										  ->setCellValue('A'.($i5+6), 'Fecha de Ingreso:')
										  ->setCellValue('C'.($i5+6), $personal->fecha_ingreso) // '19/06/1990'
										  ->setCellValue('H'.($i5+6), $nombre_periodo) // 'Periodo del 16/04/2016 al 15/08/2016'
										  ->setCellValue('A'.($i5+7), 'Cargo:')
										  ->setCellValue('B'.($i5+7), $personal->cargo)
										  ->setCellValue('H'.($i5+7), '') // 'Banco/Cuenta:-'
										  ->setCellValue('A'.($i5+9), 'Código')
										  ->setCellValue('B'.($i5+9), 'Descripción de Concepto')
										  ->setCellValue('E'.($i5+9), 'Ref')
										  ->setCellValue('G'.($i5+9), 'Asignación')
										  ->setCellValue('I'.($i5+9), 'Deducción')
										  ->setCellValue('K'.($i5+9), 'Saldo P.')
										  ->setCellValue('A'.($i5+10), $xiiimes_sp->codcon)
										  ->setCellValue('B'.($i5+10), $xiiimes_sp->descrip)
										  ->setCellValue('E'.($i5+10), 0.00)
										  ->setCellValue('G'.($i5+10), "='ADMINISTRATIVOS BRENT'!I".$i2)
										  ->setCellValue('K'.($i5+10), 0.00)
										  ->setCellValue('A'.($i5+11), '')
										  ->setCellValue('B'.($i5+11), '')
										  ->setCellValue('E'.($i5+11), '')
										  ->setCellValue('I'.($i5+11), '')
										  ->setCellValue('K'.($i5+11), '')
										  ->setCellValue('E'.($i5+12), 'Sub-Totales:')
										  ->setCellValue('G'.($i5+12), '=SUM(G'.($i5+10).':G'.($i5+11).')')
										  ->setCellValue('I'.($i5+12), '=SUM(I'.($i5+10).':I'.($i5+11).')')
										  ->setCellValue('E'.($i5+14), 'Neto a depositar Bs.:')
										  ->setCellValue('I'.($i5+14), '=G'.($i5+12).'-I'.($i5+12))
										  ->setCellValue('A'.($i5+17), 'RECIBE CONFORME')
										  ->setCellValue('G'.($i5+17), 'FECHA    ');

			$objPHPExcel->getActiveSheet()->mergeCells('A'.$i5.':F'.$i5);
			$objPHPExcel->getActiveSheet()->mergeCells('J'.$i5.':K'.$i5);
			$objPHPExcel->getActiveSheet()->mergeCells('A'.($i5+1).':C'.($i5+1));
			$objPHPExcel->getActiveSheet()->mergeCells('A'.($i5+2).':K'.($i5+2));
			$objPHPExcel->getActiveSheet()->mergeCells('E'.($i5+4).':G'.($i5+4));
			$objPHPExcel->getActiveSheet()->mergeCells('J'.($i5+4).':K'.($i5+4));
			$objPHPExcel->getActiveSheet()->mergeCells('F'.($i5+5).':K'.($i5+5));
			$objPHPExcel->getActiveSheet()->mergeCells('A'.($i5+6).':B'.($i5+6));
			$objPHPExcel->getActiveSheet()->mergeCells('H'.($i5+6).':K'.($i5+6));
			$objPHPExcel->getActiveSheet()->mergeCells('B'.($i5+7).':D'.($i5+7));
			$objPHPExcel->getActiveSheet()->mergeCells('H'.($i5+7).':K'.($i5+7));
			$objPHPExcel->getActiveSheet()->mergeCells('B'.($i5+9).':D'.($i5+9));
			$objPHPExcel->getActiveSheet()->mergeCells('E'.($i5+12).':F'.($i5+12));
			$objPHPExcel->getActiveSheet()->mergeCells('E'.($i5+14).':G'.($i5+14));
			$objPHPExcel->getActiveSheet()->mergeCells('A'.($i5+17).':C'.($i5+17));
			$objPHPExcel->getActiveSheet()->mergeCells('G'.($i5+17).':H'.($i5+17));

			$objPHPExcel->getActiveSheet()->getStyle('I'.$i5)->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('A'.($i5+2))->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('A'.($i5+4))->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('D'.($i5+4))->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('I'.($i5+4))->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('A'.($i5+5))->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('A'.($i5+6))->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('A'.($i5+7))->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('A'.($i5+9).':K'.($i5+9))->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('E'.($i5+12))->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('E'.($i5+14))->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('A'.($i5+17).':K'.($i5+17))->getFont()->setBold(true);

			$objPHPExcel->getActiveSheet()->getStyle('J'.$i5)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);	
			$objPHPExcel->getActiveSheet()->getStyle('A'.($i5+2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('J'.($i5+4))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);	
			$objPHPExcel->getActiveSheet()->getStyle('A'.($i5+9).':A'.($i5+11))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('E'.($i5+9).':E'.($i5+12))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('G'.($i5+9).':K'.($i5+9))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('E'.($i5+14))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('A'.($i5+17))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

			$objPHPExcel->getActiveSheet()->getStyle('B'.($i5+4).':B'.($i5+5))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

			$objPHPExcel->getActiveSheet()->getStyle('I'.$i5.':I'.($i5+4))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('F'.($i5+5).':K'.($i5+7))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('G'.($i5+17))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

			$objPHPExcel->getActiveSheet()->getStyle('A'.($i5+4).':K'.($i5+7))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('A'.($i5+9).':K'.($i5+9))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

			$objPHPExcel->getActiveSheet()->getStyle('B'.($i5+5))->getNumberFormat()->setFormatCode('#,##0.00');
			$objPHPExcel->getActiveSheet()->getStyle('G'.($i5+10).':K'.($i5+12))->getNumberFormat()->setFormatCode('#,##0.00');
			$objPHPExcel->getActiveSheet()->getStyle('I'.($i5+14))->getNumberFormat()->setFormatCode('#,##0.00');

			$objPHPExcel->getActiveSheet()->getStyle('A'.($i5+9).':K'.($i5+9))->applyFromArray(borderTop(PHPExcel_Style_Border::BORDER_THIN));	
			$objPHPExcel->getActiveSheet()->getStyle('G'.($i5+12).':I'.($i5+12))->applyFromArray(borderTop(PHPExcel_Style_Border::BORDER_THIN));

			$objPHPExcel->getActiveSheet()->getStyle('A'.($i5+9).':K'.($i5+9))->applyFromArray(borderBottom(PHPExcel_Style_Border::BORDER_THIN));
			$objPHPExcel->getActiveSheet()->getStyle('D'.($i5+17).':F'.($i5+17))->applyFromArray(borderBottom(PHPExcel_Style_Border::BORDER_THIN));
			$objPHPExcel->getActiveSheet()->getStyle('I'.($i5+17).':K'.($i5+17))->applyFromArray(borderBottom(PHPExcel_Style_Border::BORDER_THIN));

			$objPHPExcel->getActiveSheet()->getRowDimension(7)->setRowHeight(18);
			$objPHPExcel->getActiveSheet()->getRowDimension(8)->setRowHeight(18); // Alto: 18,00 (24px píxeles)  
			$objPHPExcel->getActiveSheet()->getRowDimension(9)->setRowHeight(18);
			$objPHPExcel->getActiveSheet()->getRowDimension(10)->setRowHeight(18);
			$objPHPExcel->getActiveSheet()->getRowDimension(12)->setRowHeight(21);

			if( ($i5-3) % 56 == 0 )
				$objPHPExcel->getActiveSheet()->setBreak('A'.($i5-3), PHPExcel_Worksheet::BREAK_ROW);

			if( ($i5+25) % 28 == 0)
			{
				$objPHPExcel->getActiveSheet()->getStyle('A'.($i5+25).':K'.($i5+25))->applyFromArray(borderBottom(PHPExcel_Style_Border::BORDER_MEDIUMDASHDOTDOT));	
				$i5 += 6;
			}

			$i5 += 25;
			//============================================================================================================================

			$objPHPExcel->setActiveSheetIndex(2);
			$i2++;
		}

		unset($FICHA);
	}

	if($i2>$i2_aux)
	{
		$objPHPExcel->getActiveSheet()->getStyle('A'.($i2_aux-1).':J'.$i2)->applyFromArray(allBorders());
		$objPHPExcel->getActiveSheet()->getStyle('C'.$i2_aux.':I'.($i2-1))->getNumberFormat()->setFormatCode('#,##0.00');		

		$objPHPExcel->getActiveSheet()->getStyle('A'.$i2_aux.':J'.$i2)->getFont()->setSize(9)->setItalic(true);

		$objPHPExcel->getActiveSheet()->getStyle('A'.$i2.':J'.$i2)->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('J'.$i2_aux.':J'.$i2)->getNumberFormat()->setFormatCode('"B/." * #,##0.00');

		$objPHPExcel->getActiveSheet()->mergeCells('B'.$i2.':I'.$i2);
		cellColor('A'.$i2.':J'.$i2, '4F81BD'); // Celdas con color de fondo	
		colorTexto('A'.$i2.':J'.$i2, 'FFFFFF');

		$objPHPExcel->getActiveSheet()->setCellValue('A'.$i2, 'Total')
									  ->setCellValue('J'.$i2, '=SUM(J'.$i2_aux.':J'.($i2-1).')');

		$objPHPExcel->getActiveSheet()->getStyle('A6:A'.$i2)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

		$total_hoja2_2 = "='ADMINISTRATIVOS BRENT'!J".$i2;
	}
	else
	{
		$objPHPExcel->getActiveSheet()->getStyle('A'.($i2-1).':J'.$i2)->applyFromArray(allBorders());		
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i2)->getFont()->setSize(10)->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i2)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->mergeCells('A'.$i2.':J'.$i2);
		$objPHPExcel->getActiveSheet()->setCellValue('A'.$i2, 'No se encontraron datos');		
	}

	$objPHPExcel->getActiveSheet()->setSelectedCells('A'.($i2+2));
	//==============================================================================================
	// Hoja de Cálculo => SERVICIOS PROFESIONALES
	$objPHPExcel->setActiveSheetIndex(3);	

	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(7);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(11);
	$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(11);
	$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(12);
	$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(13);
	$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);

	$objPHPExcel->getActiveSheet()->setCellValue('A2', 'XIII MES CORRESPONDE A '.$mes_reporte.' '.$anio)
								  ->setCellValue('C3', 'AGENCIA BRENTWOOD')
								  ->setCellValue('G3', 'SERVICIO PROFESIONAL')
								  ->setCellValue('A5', 'CODIGO')
								  ->setCellValue('B5', ' NOMBRE')
								  ->setCellValue('C5', '02-'. substr(get_nombre_mes($meses[0]), 0, 3) )
								  ->setCellValue('D5', get_nombre_mes($meses[1], 'capitalize') )
								  ->setCellValue('E5', get_nombre_mes($meses[2], 'capitalize') )
								  ->setCellValue('F5', get_nombre_mes($meses[3], 'capitalize') )
								  ->setCellValue('G5', '01-'. substr(get_nombre_mes($meses[4]), 0, 3) )
								  ->setCellValue('H5', 'TOTAL')
								  ->setCellValue('I5', '12')
								  ->setCellValue('J5', 'A PAGAR');

	$objPHPExcel->getActiveSheet()->mergeCells('A2:J2');
	$objPHPExcel->getActiveSheet()->getStyle('A2:J3')->getFont()->setSize(9)->setBold(true)->setItalic(true);
	$objPHPExcel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

	$objPHPExcel->getActiveSheet()->getStyle('C5:J5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('A5:J5')->getFont()->setSize(9)->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('C5:J5')->getFont()->setName('Arial')->setItalic(true);

	cellColor( 'A5:J5', '4F81BD'); // Celdas con color de fondo	
	colorTexto('A5:J5', 'FFFFFF'); // Celdas con color de texto diferente

	$objPHPExcel->setActiveSheetIndex(4)->setCellValue('C'.$i4, 'SERVICIOS PROFESIONALES');
	cellColor('B'.$i4.':O'.$i4, '0F243E');
	colorTexto('C'.$i4.':O'.$i4, 'FFFFFF');
	$objPHPExcel->getActiveSheet()->mergeCells('C'.$i4.':O'.$i4);
	$objPHPExcel->getActiveSheet()->getStyle('C'.$i4)->getFont()->setBold(true);
	$i4++;

	$objPHPExcel->setActiveSheetIndex(3);

	$sql = "SELECT  p.ficha, p.cedula,
					SUBSTRING_INDEX(p.nombres, ' ', 1) AS primer_nombre,
	 				CASE WHEN SUBSTRING_INDEX(p.apellidos, ' ', 1)!='DE'    THEN SUBSTRING_INDEX(p.apellidos, ' ', 1)
	 				     WHEN SUBSTRING_INDEX(p.apellidos, ' ', 2)!='DE LA' THEN SUBSTRING_INDEX(p.apellidos, ' ', 2) 
	 				     ELSE SUBSTRING_INDEX(p.apellidos, ' ', 3) 
	 				END primer_apellido, p.suesal, p.fecing, p.codnivel1,
	 				(SELECT des_car FROM nomcargos WHERE cod_car=p.codcargo) AS cargo,
	 				DATE_FORMAT(p.fecing,'%d/%m/%Y') AS fecha_ingreso
			FROM    nompersonal p
			INNER JOIN nomcampos_adic_personal  n ON n.ficha=p.ficha
			WHERE   n.id=115 AND n.valor='SI'
			AND     p.tipemp='Contratado Servicios' AND p.estado<>'Egresado'
			ORDER BY p.ficha";
	$res = $db->query($sql);

	$i3=6;
	while($personal = $res->fetch_object())
	{
		$FICHA  = $personal->ficha;

		$nombre_completo = $personal->primer_nombre.' '.$personal->primer_apellido;

		$total_mes0 = $total_mes1 = $total_mes2 = $total_mes3 = $total_mes4 = 0;

		if($personal->codnivel1 == 1)
			$sueldo_actual = 208 * $personal->suesal; 
		else
			$sueldo_actual = $personal->suesal; 

		$total_mes = xiii($fecha_ini, $personal->fecing, $sueldo_actual, $regular->codcon);

		foreach($total_mes as $i => $valor) 
		{
			$var_total  = "total_mes{$i}";
			$$var_total = $valor;
		}

		$objPHPExcel->getActiveSheet()->setCellValue('A'.$i3, $personal->ficha)
									  ->setCellValue('B'.$i3, $nombre_completo)
									  ->setCellValue('C'.$i3, $total_mes0)
									  ->setCellValue('D'.$i3, $total_mes1)
									  ->setCellValue('E'.$i3, $total_mes2)
									  ->setCellValue('F'.$i3, $total_mes3)
									  ->setCellValue('G'.$i3, $total_mes4)
									  ->setCellValue('H'.$i3, '=SUM(C'.$i3.':G'.$i3.')')
									  ->setCellValue('I'.$i3, '=IF(H'.$i3.'>0, H'.$i3.'/12, 0)')
									  ->setCellValue('J'.$i3, '=ROUND(I'.$i3.', 2)');

		$total_xiiimes = $objPHPExcel->getActiveSheet()->getCell('J'.$i3)->getFormattedValue();
		
		$objPHPExcel->setActiveSheetIndex(4);

		desglosar_monedas_billetes($total_xiiimes, $cambio);

		$objPHPExcel->getActiveSheet()->setCellValue('C'.$i4, $nombre_completo)
									  ->setCellValue('D'.$i4, $personal->ficha)
									  ->setCellValue('E'.$i4, "='SERVICIOS PROFESIONALES'!J".$i3) // $total_xiiimes 
									  ->setCellValue('F'.$i4, $cambio[0])
									  ->setCellValue('G'.$i4, $cambio[1])
									  ->setCellValue('H'.$i4, $cambio[2])
									  ->setCellValue('I'.$i4, $cambio[3])
									  ->setCellValue('J'.$i4, $cambio[4])
									  ->setCellValue('K'.$i4, $cambio[5])
									  ->setCellValue('L'.$i4, $cambio[6])
									  ->setCellValue('M'.$i4, $cambio[7])
									  ->setCellValue('N'.$i4, $cambio[8])
									  ->setCellValue('O'.$i4, '=F'.$i4.'*20+G'.$i4.'*10+H'.$i4.'*5+I'.$i4.'*1+J'.$i4.'*0.5+K'.$i4.'*0.25+L'.$i4.'*0.1+M'.$i4.'*0.05+N'.$i4.'*0.01');
		$i4++;
		//============================================================================================================================
		// RECIBO

		$objPHPExcel->setActiveSheetIndex(5);

		$objPHPExcel->getActiveSheet()->setCellValue('A'.$i5, $empresa->nom_emp)
									  ->setCellValue('I'.$i5, 'Fecha:')
									  ->setCellValue('J'.$i5, $hoy)
									  ->setCellValue('A'.($i5+1), $empresa->rif)
									  ->setCellValue('A'.($i5+2), 'RECIBO DE PAGO')
									  ->setCellValue('A'.($i5+4), 'Ficha:')
									  ->setCellValue('B'.($i5+4), $personal->ficha)
									  ->setCellValue('D'.($i5+4), 'Nombre:')
									  ->setCellValue('E'.($i5+4), $nombre_completo)
									  ->setCellValue('I'.($i5+4), 'Cédula:')
									  ->setCellValue('J'.($i5+4), $personal->cedula)
									  ->setCellValue('A'.($i5+5), 'Sueldo:')
									  ->setCellValue('B'.($i5+5), $personal->suesal)
									  ->setCellValue('F'.($i5+5), $descrip_xiii)
									  ->setCellValue('A'.($i5+6), 'Fecha de Ingreso:')
									  ->setCellValue('C'.($i5+6), $personal->fecha_ingreso) // '19/06/1990'
									  ->setCellValue('H'.($i5+6), $nombre_periodo) // 'Periodo del 16/04/2016 al 15/08/2016'
									  ->setCellValue('A'.($i5+7), 'Cargo:')
									  ->setCellValue('B'.($i5+7), $personal->cargo)
									  ->setCellValue('H'.($i5+7), '') // 'Banco/Cuenta:-'
									  ->setCellValue('A'.($i5+9), 'Código')
									  ->setCellValue('B'.($i5+9), 'Descripción de Concepto')
									  ->setCellValue('E'.($i5+9), 'Ref')
									  ->setCellValue('G'.($i5+9), 'Asignación')
									  ->setCellValue('I'.($i5+9), 'Deducción')
									  ->setCellValue('K'.($i5+9), 'Saldo P.')
									  ->setCellValue('A'.($i5+10), $xiiimes_sp->codcon)
									  ->setCellValue('B'.($i5+10), $xiiimes_sp->descrip)
									  ->setCellValue('E'.($i5+10), 0.00)
									  ->setCellValue('G'.($i5+10), "='SERVICIOS PROFESIONALES'!I".$i3)
									  ->setCellValue('K'.($i5+10), 0.00)
									  ->setCellValue('A'.($i5+11), '')
									  ->setCellValue('B'.($i5+11), '')
									  ->setCellValue('E'.($i5+11), '')
									  ->setCellValue('I'.($i5+11), '')
									  ->setCellValue('K'.($i5+11), '')
									  ->setCellValue('E'.($i5+12), 'Sub-Totales:')
									  ->setCellValue('G'.($i5+12), '=SUM(G'.($i5+10).':G'.($i5+11).')')
									  ->setCellValue('I'.($i5+12), '=SUM(I'.($i5+10).':I'.($i5+11).')')
									  ->setCellValue('E'.($i5+14), 'Neto a depositar Bs.:')
									  ->setCellValue('I'.($i5+14), '=G'.($i5+12).'-I'.($i5+12))
									  ->setCellValue('A'.($i5+17), 'RECIBE CONFORME')
									  ->setCellValue('G'.($i5+17), 'FECHA    ');

		$objPHPExcel->getActiveSheet()->mergeCells('A'.$i5.':F'.$i5);
		$objPHPExcel->getActiveSheet()->mergeCells('J'.$i5.':K'.$i5);
		$objPHPExcel->getActiveSheet()->mergeCells('A'.($i5+1).':C'.($i5+1));
		$objPHPExcel->getActiveSheet()->mergeCells('A'.($i5+2).':K'.($i5+2));
		$objPHPExcel->getActiveSheet()->mergeCells('E'.($i5+4).':G'.($i5+4));
		$objPHPExcel->getActiveSheet()->mergeCells('J'.($i5+4).':K'.($i5+4));
		$objPHPExcel->getActiveSheet()->mergeCells('F'.($i5+5).':K'.($i5+5));
		$objPHPExcel->getActiveSheet()->mergeCells('A'.($i5+6).':B'.($i5+6));
		$objPHPExcel->getActiveSheet()->mergeCells('H'.($i5+6).':K'.($i5+6));
		$objPHPExcel->getActiveSheet()->mergeCells('B'.($i5+7).':D'.($i5+7));
		$objPHPExcel->getActiveSheet()->mergeCells('H'.($i5+7).':K'.($i5+7));
		$objPHPExcel->getActiveSheet()->mergeCells('B'.($i5+9).':D'.($i5+9));
		$objPHPExcel->getActiveSheet()->mergeCells('E'.($i5+12).':F'.($i5+12));
		$objPHPExcel->getActiveSheet()->mergeCells('E'.($i5+14).':G'.($i5+14));
		$objPHPExcel->getActiveSheet()->mergeCells('A'.($i5+17).':C'.($i5+17));
		$objPHPExcel->getActiveSheet()->mergeCells('G'.($i5+17).':H'.($i5+17));

		$objPHPExcel->getActiveSheet()->getStyle('I'.$i5)->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('A'.($i5+2))->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('A'.($i5+4))->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('D'.($i5+4))->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('I'.($i5+4))->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('A'.($i5+5))->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('A'.($i5+6))->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('A'.($i5+7))->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('A'.($i5+9).':K'.($i5+9))->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('E'.($i5+12))->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('E'.($i5+14))->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('A'.($i5+17).':K'.($i5+17))->getFont()->setBold(true);

		$objPHPExcel->getActiveSheet()->getStyle('J'.$i5)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);	
		$objPHPExcel->getActiveSheet()->getStyle('A'.($i5+2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);		
		$objPHPExcel->getActiveSheet()->getStyle('J'.($i5+4))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);	
		$objPHPExcel->getActiveSheet()->getStyle('A'.($i5+9).':A'.($i5+11))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('E'.($i5+9).':E'.($i5+12))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('G'.($i5+9).':K'.($i5+9))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('E'.($i5+14))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A'.($i5+17))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

		$objPHPExcel->getActiveSheet()->getStyle('B'.($i5+4).':B'.($i5+5))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

		$objPHPExcel->getActiveSheet()->getStyle('I'.$i5.':I'.($i5+4))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$objPHPExcel->getActiveSheet()->getStyle('F'.($i5+5).':K'.($i5+7))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$objPHPExcel->getActiveSheet()->getStyle('G'.($i5+17))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

		$objPHPExcel->getActiveSheet()->getStyle('A'.($i5+4).':K'.($i5+7))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A'.($i5+9).':K'.($i5+9))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

		$objPHPExcel->getActiveSheet()->getStyle('B'.($i5+5))->getNumberFormat()->setFormatCode('#,##0.00');
		$objPHPExcel->getActiveSheet()->getStyle('G'.($i5+10).':K'.($i5+12))->getNumberFormat()->setFormatCode('#,##0.00');
		$objPHPExcel->getActiveSheet()->getStyle('I'.($i5+14))->getNumberFormat()->setFormatCode('#,##0.00');

		$objPHPExcel->getActiveSheet()->getStyle('A'.($i5+9).':K'.($i5+9))->applyFromArray(borderTop(PHPExcel_Style_Border::BORDER_THIN));	
		$objPHPExcel->getActiveSheet()->getStyle('G'.($i5+12).':I'.($i5+12))->applyFromArray(borderTop(PHPExcel_Style_Border::BORDER_THIN));

		$objPHPExcel->getActiveSheet()->getStyle('A'.($i5+9).':K'.($i5+9))->applyFromArray(borderBottom(PHPExcel_Style_Border::BORDER_THIN));
		$objPHPExcel->getActiveSheet()->getStyle('D'.($i5+17).':F'.($i5+17))->applyFromArray(borderBottom(PHPExcel_Style_Border::BORDER_THIN));
		$objPHPExcel->getActiveSheet()->getStyle('I'.($i5+17).':K'.($i5+17))->applyFromArray(borderBottom(PHPExcel_Style_Border::BORDER_THIN));

		$objPHPExcel->getActiveSheet()->getRowDimension(7)->setRowHeight(18);
		$objPHPExcel->getActiveSheet()->getRowDimension(8)->setRowHeight(18); // Alto: 18,00 (24px píxeles)  
		$objPHPExcel->getActiveSheet()->getRowDimension(9)->setRowHeight(18);
		$objPHPExcel->getActiveSheet()->getRowDimension(10)->setRowHeight(18);
		$objPHPExcel->getActiveSheet()->getRowDimension(12)->setRowHeight(21);

		if( ($i5-3) % 56 == 0 )
			$objPHPExcel->getActiveSheet()->setBreak('A'.($i5-3), PHPExcel_Worksheet::BREAK_ROW);

		if( ($i5+25) % 28 == 0)
		{
			$objPHPExcel->getActiveSheet()->getStyle('A'.($i5+25).':K'.($i5+25))->applyFromArray(borderBottom(PHPExcel_Style_Border::BORDER_MEDIUMDASHDOTDOT));	
			$i5 += 6;
		}

		$i5 += 25;
		//============================================================================================================================

		$objPHPExcel->setActiveSheetIndex(3);
		$i3++;

		unset($FICHA);
	}

	if($i3>6)
	{
		$objPHPExcel->getActiveSheet()->getStyle('A5:J'.$i3)->applyFromArray(allBorders());
		$objPHPExcel->getActiveSheet()->getStyle('C6:J'.$i3)->getNumberFormat()->setFormatCode('"B/." * #,##0.00');

		$objPHPExcel->getActiveSheet()->getStyle('A6:J'.$i3)->getFont()->setSize(8)->setItalic(true);

		$objPHPExcel->getActiveSheet()->mergeCells('B'.$i3.':I'.$i3);
		cellColor( 'A'.$i3.':J'.$i3, '4F81BD'); // Celdas con color de fondo	
		colorTexto('A'.$i3.':J'.$i3, 'FFFFFF');

		$objPHPExcel->getActiveSheet()->getStyle('A'.$i3.':J'.$i3)->getFont()->setBold(true);

		$objPHPExcel->getActiveSheet()->setCellValue('A'.$i3, 'Total')
									  ->setCellValue('J'.$i3, '=SUM(J6:J'.($i3-1).')');

		$objPHPExcel->getActiveSheet()->getStyle('J'.$i3)->getFont()->setSize(9);

		$objPHPExcel->getActiveSheet()->getStyle('A5:A'.$i3)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

		$total_hoja3 = "='SERVICIOS PROFESIONALES'!J".$i3;
	}
	else
	{
		$objPHPExcel->getActiveSheet()->getStyle('A5:J6')->applyFromArray(allBorders());		
		$objPHPExcel->getActiveSheet()->getStyle('A6')->getFont()->setSize(9)->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('A6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->mergeCells('A6:J6');
		$objPHPExcel->getActiveSheet()->setCellValue('A6', 'No se encontraron datos');
	}

	$objPHPExcel->getActiveSheet()->setSelectedCells('A'.($i3+2));

	//==============================================================================================
	$objPHPExcel->setActiveSheetIndex(4);

	// Escala área de impresión
	$objPHPExcel->getActiveSheet()->getPageSetup()->setScale(50);
	$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);

	// Nivel de zoom y Vista previa de salto de página	
	$objPHPExcel->getActiveSheet()->getSheetView()->setView(PHPExcel_Worksheet_SheetView::SHEETVIEW_PAGE_BREAK_PREVIEW);
	$objPHPExcel->getActiveSheet()->getSheetView()->setZoomScale(106);	

	if($i4>9)
	{
		$objPHPExcel->getActiveSheet()->getStyle('B9:O'.($i4-1))->applyFromArray(allBorders());

		$objPHPExcel->getActiveSheet()->getStyle('D9:D'.($i4-1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('F8:N'.($i4-1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

		$objPHPExcel->getActiveSheet()->setCellValue('R9',  '=SUM(F9:F'.($i4-1).')*Q9')
									  ->setCellValue('R10', '=SUM(G9:G'.($i4-1).')*Q10')
									  ->setCellValue('R11', '=SUM(H9:H'.($i4-1).')*Q11')
									  ->setCellValue('R12', '=SUM(I9:I'.($i4-1).')*Q12')
									  ->setCellValue('R13', '=SUM(J9:J'.($i4-1).')*Q13')
									  ->setCellValue('R14', '=SUM(K9:K'.($i4-1).')*Q14')
									  ->setCellValue('R15', '=SUM(L9:L'.($i4-1).')*Q15')
									  ->setCellValue('R16', '=SUM(M9:M'.($i4-1).')*Q16')
									  ->setCellValue('R17', '=SUM(N9:N'.($i4-1).')*Q17')
									  ->setCellValue('S9',  '=R9/Q9')
									  ->setCellValue('S10', '=R10/Q10')
									  ->setCellValue('S11', '=R11/Q11')
									  ->setCellValue('S12', '=R12/Q12')
									  ->setCellValue('S13', '=R13/Q13')
									  ->setCellValue('S14', '=R14/Q14')
									  ->setCellValue('S15', '=R15/Q15')
									  ->setCellValue('S16', '=R16/Q16')
									  ->setCellValue('S17', '=R17/Q17')
									  ->setCellValue('E'.$i4, '=SUM(E9:E'.($i4-1).')')
									  ->setCellValue('O'.$i4, '=SUM(O9:O'.($i4-1).')');

		$objPHPExcel->getActiveSheet()->getStyle('E9:E'.($i4-1))->getNumberFormat()->setFormatCode('#,##0.00');	
								  
		$objPHPExcel->getActiveSheet()->getStyle('E'.$i4)->getNumberFormat()->setFormatCode('"B/." * #,##0.00');
		$objPHPExcel->getActiveSheet()->getStyle('O9:O'.$i4)->getNumberFormat()->setFormatCode('#,##0.00');
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
	//Ubicarse en la primera hoja
	$objPHPExcel->setActiveSheetIndex(0);

	$objPHPExcel->getActiveSheet()->setCellValue('B5', $total_hoja1)
								  ->setCellValue('B6', $total_hoja3)
								  ->setCellValue('B7', $total_hoja2_1)
								  ->setCellValue('B8', $total_hoja2_2);	

	$objPHPExcel->getActiveSheet()->setSelectedCells('A65');

	$filename = 'excel/ESTIMADO XIII MES '.$mes_reporte. ' '.$anio.'.xlsx';

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');	

	$objWriter->save($filename);

	echo $filename;
}
else
{
   echo "<script>alert('Acceso Denegado');</script>";  
   echo "<script>document.location.href = 'config_rpt_xiii_mes.php';</script>";
}

exit;