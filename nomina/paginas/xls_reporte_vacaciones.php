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
include('lib/phpexcel_conceptos.php');
require_once("phpexcel/Classes/PHPExcel.php");

if(isset($_POST['mes']) && isset($_POST['periodo']) && isset($_POST['anio']))
{
	$mes     = $_POST['mes'];     // Número del mes: 1-Enero / 12-Diciembre
	$periodo = $_POST['periodo']; // Número del período: 1- 1 al 30 / 2- 16 al 15
	$anio    = $_POST['anio'];    // Año actual:  date('Y')
	$modulo    = $_POST['modulo'];    // Año actual:  date('Y')

	$mes_act_mayus = get_nombre_mes($mes-1, 'uppercase'); // Mes Actual
	$mes_sig_mayus = get_nombre_mes($mes, 'uppercase');   // Mes Siguiente

	$db = new Database($_SESSION['bd']);

	$periodo_ini = new DateTime();
	$periodo_ini->setTime(0, 0, 0);

	$periodo_fin = new DateTime();
	$periodo_fin->setTime(0, 0, 0);
	$SQL = "SELECT nom_emp,ger_rrhh, jefe_planilla, jefe_registro FROM nomempresa";
	if($empresa = $db->query($SQL)->fetch_object())
	$nom_emp =  strtoupper($empresa->nom_emp);
	$ger_rrhh =  strtoupper($empresa->ger_rrhh);
	$jefe_planilla =  strtoupper($empresa->jefe_planilla);
	$jefe_registro =  strtoupper($empresa->jefe_registro);

	if($periodo==1)
	{
		// Período 1 - 01 al 30
		$periodo_ini->setDate($anio, $mes, 1);
		$periodo_fin->setDate($anio, $mes, 1);
		//$periodo_fin->setDate($anio, $mes, 30);

		$nombre_periodo = " 01 DE " . $mes_act_mayus . " AL 30 DE " . $mes_act_mayus . " " . $anio; 

		//$last_day = date("t", strtotime($anio.'-'.$mes.'-1')); // Ultimo día del mes
		//$last_day = $periodo_ini->format( 't' );
	}
	else if($periodo==2)
	{
		// Período 2 - 16 al 15
		$periodo_ini->setDate($anio, $mes, 16);
		$periodo_fin->setDate($anio, $mes, 16);
		//$periodo_fin->setDate($anio, $mes+1, 15);

		$nombre_periodo = " 16 DE " . $mes_act_mayus . ($mes==12 ? " {$anio}" : "") . " AL 15 DE " . $mes_sig_mayus . " " . ($mes<12 ? $anio : $anio+1); 
	}
	else if($periodo==3)
	{
		// Todos (01 al 30 / 16 al 15)
		$periodo_ini->setDate($anio, $mes, 1);
		$periodo_fin->setDate($anio, $mes, 16);
		//$periodo_fin->setDate($anio, $mes+1, 15);
	}

	//echo "\nPeriodo Inicio: " . $periodo_ini->format('Y-m-d H:i:s') . " /\nPeriodo Fin: ". $periodo_fin->format('Y-m-d H:i:s')."\n"; 

	$condicion_regular   = obtener_conceptos('regular');
	$condicion_nacional  = obtener_conceptos('nacional');
	$condicion_sueldodiv = obtener_conceptos('sueldodiv');

	$objPHPExcel = new PHPExcel();

	$objPHPExcel->getProperties()->setCreator("Selectra Planilla")
								 ->setLastModifiedBy("Selectra Planilla")
								 ->setTitle("Reporte de Vacaciones");

	$objPHPExcel->getActiveSheet()->getDefaultStyle()->getFont()->setName('Calibri')->setSize(12);

	$sql = "SELECT  p.ficha, p.cedula, p.fecing, p.codnivel1, p.tipemp,
					SUBSTRING_INDEX(p.nombres, ' ', 1) as primer_nombre,
	 				CASE WHEN SUBSTRING_INDEX(p.apellidos, ' ', 1)!='DE'    THEN SUBSTRING_INDEX(p.apellidos, ' ', 1)
	 				     WHEN SUBSTRING_INDEX(p.apellidos, ' ', 2)!='DE LA' THEN SUBSTRING_INDEX(p.apellidos, ' ', 2) 
	 				     ELSE SUBSTRING_INDEX(p.apellidos, ' ', 3) 
	 				END primer_apellido,
	 				CASE WHEN DATE_FORMAT(p.fecing,'%d') BETWEEN 1 AND 15 THEN 1
	 				ELSE 2 END as quincena
			FROM    nompersonal p
			WHERE   p.estado!='Egresado'
			ORDER BY 8, p.fecing ASC"; // DATE_FORMAT(p.fecing,'%d-%m'), p.fecing ASC

	$res = $db->query($sql);

	$sheet=0; // Contador de hojas de cálculo del libro
	$empleados = array();
	while($personal = $res->fetch_object())
	{
		$nombre_completo = $personal->primer_nombre.' '.$personal->primer_apellido;

		$fecha_vac = get_fecha_vacaciones($personal->fecing, $anio);		

		if($fecha_vac >= $periodo_ini  &&  $fecha_vac <= $periodo_fin)
		{
			//echo "\nFicha:{$personal->ficha} => Fecha Vacaciones: " . $fecha_vac->format('Y-m-d H:i:s').".\n";
			$fecha_ingreso = new DateTime($personal->fecing);

			$dia_ing  = $fecha_ingreso->format('d');
			$mes_ing  = $fecha_ingreso->format('n');
			$anio_ing = $fecha_ingreso->format('Y');

			$fecha_entrada = $dia_ing .'-'. substr(get_nombre_mes($mes_ing-1), 0, 3) .'-'. $anio_ing; 
			// strtolower($fecha_ingreso->format('d-M-Y'))

			if($periodo==3)
			{
				if($fecha_vac->format('j') == 1)
				{
					$nombre_periodo = " 01 DE " . $mes_act_mayus . " AL 30 DE " . $mes_act_mayus . " " . $anio;
				}
				else
				{
					$nombre_periodo = " 16 DE " . $mes_act_mayus . ($mes==12 ? " {$anio}" : "") . " AL 15 DE " . $mes_sig_mayus . " " . ($mes<12 ? $anio : $anio+1); 
				}
			}

			if($sheet>0)
			{
				$objPHPExcel->createSheet();
				$objPHPExcel->setActiveSheetIndex($sheet);
			}

			// Nombre hoja del libro
			$title = trim($nombre_completo);
			$title = str_replace(array("\"", "?", "Á", "É", "Í", "Ó", "Ú", "á", "é", "í", "ó", "ú"), 
						         array(''  , '',  "A", "E", "I", "O", "U", "a", "e", "i", "o", "u"), 
						         $title);
			$objPHPExcel->getActiveSheet()->setTitle($title);

			if($personal->tipemp=='Contratado Servicios')
			{
				$objPHPExcel->getActiveSheet()->setCellValue('A3', 'S/P');
				$objPHPExcel->getActiveSheet()->getStyle('A3')->getFont()->setBold(true)->setItalic(true);
			}
			else
			{
				$objDrawing = new PHPExcel_Worksheet_Drawing();
				$objDrawing->setName('Logo');
			    $objDrawing->setDescription('Logo Brentwood');
				$objDrawing->setCoordinates('A2');
				$objDrawing->setPath('imagenes/logo_excel.png');
				$objDrawing->setOffsetX(10);
				//$objDrawing->setOffsetY(0);
				$objDrawing->setResizeProportional(true);
				$objDrawing->setHeight(38);
				$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
			}

			if($mes_ing!=1) $anio--; // Año anterior

			$objPHPExcel->getActiveSheet()->setCellValue('A5', 'NOMBRE')
										  ->setCellValue('B5', $nombre_completo)
										  ->setCellValue('D5', 'FECHA ENTRADA')
										  ->setCellValue('F5', $fecha_entrada)
										  ->setCellValue('A6', 'CODIGO')
										  ->setCellValue('B6', $personal->ficha)
										  ->setCellValue('D6', 'CEDULA')
										  ->setCellValue('F6', $personal->cedula)
										  ->setCellValue('A8', 'PERIODO CORRESPONDE:')
										  ->setCellValue('C8', $nombre_periodo)
										  ->setCellValue('A10', 'Año '.$anio);

			//$objPHPExcel->getActiveSheet()->getStyle('F5')->getNumberFormat()->setFormatCode('dd-mmm-yy');

			$i=11;
			for ($j=$mes_ing; $j<=12 && $mes_ing!=1 ; $j++) 
			{ 
				$frecuencia = array(2, 3);
				$dividir = false;

				if($j==$mes_ing && $fecha_vac->format('j') != 1) // Ignorar Primera Quincena
				{
					$frecuencia = array(3);
					$dividir = true;
				}

				$total = 0;

				if($personal->codnivel1==2) // Administrativos
				{
					$sql2 = "SELECT COALESCE(SUM(n.monto),0) as total
						     FROM   nom_movimientos_nomina n
							 WHERE  n.ficha={$personal->ficha}
							 AND    {$condicion_regular} 
							 AND    n.codnom IN (SELECT codnom 
							   				     FROM nom_nominas_pago 
							                     WHERE anio={$anio} AND mes={$j} AND frecuencia IN(" . implode(",", $frecuencia) . "))";

					if($fila2 = $db->query($sql2)->fetch_object())
						$total = $fila2->total;
				}
				else // Vigilantes
				{
					if( $j==$mes_ing   &&   $anio==$anio_ing) // && ($anio==2015 || ($anio==2016 && in_array($j, array(1, 2))))  
					{
						// Se deben considerar los días que no trabajo el primer mes (tomar el concepto 100 y 122)
						$sql2 = "SELECT COALESCE(SUM(n.monto),0) as total
							     FROM   nom_movimientos_nomina n
								 WHERE  n.ficha={$personal->ficha}
								 AND    {$condicion_regular}
								 AND    {$condicion_nacional} 
								 AND    n.codnom IN (SELECT codnom 
								   				     FROM nom_nominas_pago 
								                     WHERE anio={$anio} AND mes={$j} AND frecuencia IN(" . implode(",", $frecuencia) . "))";

						if($fila2 = $db->query($sql2)->fetch_object())
							$total = $fila2->total;
					}
					else
					{
						// Sino tomar lo que esta en la nueva tabla

						$total = get_sueldo_minimo($anio, $j); // Sueldo Mínimo Mensual

						if($dividir)
							$total /= 2; //Tomar solo una quincena						
					}
				}

				$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, get_nombre_mes($j-1, 'capitalize'))
										      ->setCellValue('B'.$i, $total);

				$i++;
			}

			 if($mes_ing!=1) $anio++; // Año actual

			for ($j=1; $j<=$mes; $j++) 
			{ 
				$frecuencia = array(2, 3); // Primera y Segunda Quincena
				$dividir = false;

				if($j==1 && $mes_ing==1 && $fecha_vac->format('j') == 16)
				{
					$frecuencia = array(2); // Tomar en cuenta solo la Primera Quincena
					$dividir = true;					
				}

				if($j==$mes)
				{
					if($fecha_vac->format('j') == 1)
						break;
					else
					{
						$frecuencia = array(2); // Tomar en cuenta solo la Primera Quincena
						$dividir = true;
					}
				}

				if($j==1 && $mes_ing!=1)
				{
					$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, 'Año '.$anio);
					$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getFont()->setBold(true);
					$i++;
				}

				$total = 0;

				if($personal->codnivel1==2) // Administrativos
				{
					$sql2 = "SELECT COALESCE(SUM(n.monto),0) as total
						     FROM   nom_movimientos_nomina n
							 WHERE  n.ficha={$personal->ficha}
							 AND    {$condicion_regular} 
							 AND    n.codnom IN (SELECT codnom 
							   				     FROM nom_nominas_pago 
							                     WHERE anio={$anio} AND mes={$j} AND frecuencia IN(" . implode(",", $frecuencia) . "))";

					if($fila2 = $db->query($sql2)->fetch_object())
						$total = $fila2->total;
				}	
				else // Vigilantes
				{

					if($j==$mes_ing   &&   $anio==$anio_ing) // &&   $anio==2016
					{
						// Se deben considerar los días que no trabajo el primer mes (tomar el concepto 100 y 122)
						$sql2 = "SELECT COALESCE(SUM(n.monto),0) as total
							     FROM   nom_movimientos_nomina n
								 WHERE  n.ficha={$personal->ficha}
								 AND    {$condicion_regular}
								 AND    {$condicion_nacional} 
								 AND    n.codnom IN (SELECT codnom 
								   				     FROM nom_nominas_pago 
								                     WHERE anio={$anio} AND mes={$j} AND frecuencia IN(" . implode(",", $frecuencia) . "))";

						if($fila2 = $db->query($sql2)->fetch_object())
							$total = $fila2->total;
					}
					else
					{
						// Sino tomar lo que esta en la nueva tabla

						$total = get_sueldo_minimo($anio, $j); // Sueldo Mínimo Mensual

						if($dividir)
							$total /= 2; //Tomar solo una quincena
					}
				}	

				$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, get_nombre_mes($j-1, 'capitalize'))
										      ->setCellValue('B'.$i, $total);

				$i++;
			}

			$i++;

			$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, 'TOTAL ACUMULADO')
										  ->setCellValue('B'.$i, '=SUM(B11:B'.($i-1).')');

			$total_acumulado = "='{$title}'!B".$i;

			$objPHPExcel->getActiveSheet()->getStyle('A1:G'.$i)->getFont()->setItalic(true);

			$objPHPExcel->getActiveSheet()->getStyle('B'.$i)->getNumberFormat()->setFormatCode('"B/." * #,##0.00');
			$objPHPExcel->getActiveSheet()->getStyle('B11:B'.($i-1))->getNumberFormat()->setFormatCode('#,##0.00');

			$objPHPExcel->getActiveSheet()->getStyle('A10:B'.$i)->applyFromArray(allBorders());
			

			$objPHPExcel->getActiveSheet()->getStyle('B5:B6')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('F5:F6')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('A8:G8')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('A10')->getFont()->setBold(true);

			$i++;
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, 'VACACIONES BRUTAS')
										  ->setCellValue('C'.$i, '=B'.($i-1).'/11')
										  ->setCellValue('A'.($i+2), ' DEDUCCIONES')
										  ->setCellValue('D'.($i+2), 'SEGURO SOCIAL')
										  ->setCellValue('F'.($i+2), ( $personal->tipemp!='Contratado Servicios' ? '=C'.$i.'*9.75%' : 0 ) )
										  ->setCellValue('D'.($i+3), 'SEGURO EDUCATIVO')
										  ->setCellValue('F'.($i+3), ( $personal->tipemp!='Contratado Servicios' ? '=C'.$i.'*1.25%' : 0 ) )
										  ->setCellValue('D'.($i+4), 'TOTAL DEDUCCIONES')
										  ->setCellValue('F'.($i+4), '=F'.($i+2).'+F'.($i+3));

			$vacaciones_brutas = "='{$title}'!C".$i;
			$seguro_social     = "='{$title}'!F".($i+2);
			$seguro_educativo  = "='{$title}'!F".($i+3);

			$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':A'.($i+2))->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('D'.($i+4))->getFont()->setBold(true);

			$objPHPExcel->getActiveSheet()->getStyle('C'.$i)->applyFromArray(borderBottom(PHPExcel_Style_Border::BORDER_THIN));
			$objPHPExcel->getActiveSheet()->getStyle('F'.($i+3))->applyFromArray(borderBottom(PHPExcel_Style_Border::BORDER_THIN));

			$objPHPExcel->getActiveSheet()->getStyle('C'.$i)->getNumberFormat()->setFormatCode('"B/." * #,##0.00');
			$objPHPExcel->getActiveSheet()->getStyle('F'.($i+2).':F'.($i+4))->getNumberFormat()->setFormatCode('"B/." * #,##0.00');

			$i+=7;
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, 'VACACIONES NETAS')
										  ->setCellValue('C'.$i, '=C'.($i-7).'-F'.($i-3));

										  
				$objPHPExcel->getActiveSheet()->setCellValue('A'.($i+2), 'Preparado por:'.$ger_rrhh);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':A'.($i+2))->getFont()->setBold(true);
				$objPHPExcel->getActiveSheet()->setCellValue('C'.($i+2), 'Revisado por:'.$jefe_registro);
				$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':C'.($i+2))->getFont()->setBold(true);
				$objPHPExcel->getActiveSheet()->setCellValue('E'.($i+2), 'Aprobado por:'.$jefe_planilla);
				$objPHPExcel->getActiveSheet()->getStyle('E'.$i.':E'.($i+2))->getFont()->setBold(true);


			$vacaciones_netas  = "='{$title}'!C".$i;

			$empleados[] = array('nombre' => $nombre_completo,  'unidad' => $personal->ficha, 
				                 'fecing' => $fecha_entrada,
			                     'cedula' => $personal->cedula, 'fecvac' => $nombre_periodo, 
			                     'monto'  => $total_acumulado, 'vac' => $vacaciones_brutas, 
			                     'ss' => $seguro_social, 'se' => $seguro_educativo, 'neto' => $vacaciones_netas);

			$objPHPExcel->getActiveSheet()->getStyle('A'.($i-6).':G'.$i)->getFont()->setItalic(true);

			$objPHPExcel->getActiveSheet()->getStyle('C'.$i)->getNumberFormat()->setFormatCode('"B/." * #,##0.00');
			$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':C'.$i)->getFont()->setBold(true);

			$objPHPExcel->getActiveSheet()->getStyle('A'.($i+2))->getFont()->setBold(true)->setItalic(true);
			$objPHPExcel->getActiveSheet()->getStyle('A'.($i+2).':G'.($i+3))->getFont()->setSize(11);

			$objPHPExcel->getActiveSheet()->getStyle('C'.$i)->applyFromArray(borderBottom(PHPExcel_Style_Border::BORDER_DOUBLE));
			//$objPHPExcel->getActiveSheet()->getStyle('D'.($i+2).':F'.($i+2))->applyFromArray(borderBottom(PHPExcel_Style_Border::BORDER_THIN));
			//$objPHPExcel->getActiveSheet()->getStyle('D'.($i+3).':F'.($i+3))->applyFromArray(borderBottom(PHPExcel_Style_Border::BORDER_THIN));

			$objPHPExcel->getActiveSheet()->getStyle('B6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

			$objPHPExcel->getActiveSheet()->getStyle('F5:F6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('C'.($i+2).':C'.($i+3))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(18);
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(17);
			$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(13);
			$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
			$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
			$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(13);
			$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(11);

			// Nivel de zoom y Vista previa de salto de página	
			$objPHPExcel->getActiveSheet()->getSheetView()->setView(PHPExcel_Worksheet_SheetView::SHEETVIEW_PAGE_BREAK_PREVIEW);
			$objPHPExcel->getActiveSheet()->getSheetView()->setZoomScale(93);	

			// Escala área de impresión
			//$objPHPExcel->getActiveSheet()->getPageSetup()->setScale(85);
			//$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);

			//==============================================================================================
			// Servicios Profesionales S/P (Administrativos)

			if($personal->codnivel1==2)
			{
				$i+=4;

				$objPHPExcel->getActiveSheet()->setBreak('A'.$i, PHPExcel_Worksheet::BREAK_ROW);

				$i+=3;

				if($mes_ing!=1) $anio--; // Año anterior

				$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, 'S/P')
											  ->setCellValue('A'.($i+1), 'NOMBRE')
											  ->setCellValue('B'.($i+1), $nombre_completo)
											  ->setCellValue('D'.($i+1), 'FECHA ENTRADA')
											  ->setCellValue('F'.($i+1), $fecha_entrada)
											  ->setCellValue('A'.($i+2), 'CODIGO')
											  ->setCellValue('B'.($i+2), $personal->ficha)
											  ->setCellValue('D'.($i+2), 'CEDULA')
											  ->setCellValue('F'.($i+2), $personal->cedula)
											  ->setCellValue('A'.($i+4), 'PERIODO CORRESPONDE:')
											  ->setCellValue('C'.($i+4), $nombre_periodo)
											  ->setCellValue('A'.($i+6), 'Año '.$anio);

				$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getFont()->setSize(14)->setBold(true);

				$objPHPExcel->getActiveSheet()->getStyle('B'.($i+1).':B'.($i+2))->getFont()->setBold(true);
				$objPHPExcel->getActiveSheet()->getStyle('F'.($i+1).':F'.($i+2))->getFont()->setBold(true);
				$objPHPExcel->getActiveSheet()->getStyle('A'.($i+4).':F'.($i+6))->getFont()->setBold(true);

				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':F'.($i+6))->getFont()->setItalic(true);

				$objPHPExcel->getActiveSheet()->getStyle('B'.($i+2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				$objPHPExcel->getActiveSheet()->getStyle('F'.($i+1).':F'.($i+2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

				$i+=7; $aux_i = $i; $acumulado=0;
				for ($j=$mes_ing; $j<=12 && $mes_ing!=1; $j++) 
				{ 
					$frecuencia = array(2, 3);

					if($j==$mes_ing && $fecha_vac->format('j') != 1) // Ignorar Primera Quincena
						$frecuencia = array(3);

					$sql2 = "SELECT COALESCE(SUM(n.monto),0) as total
						     FROM   nom_movimientos_nomina n
							 WHERE  n.ficha={$personal->ficha}
							 AND    {$condicion_sueldodiv} 
							 AND    n.codnom IN (SELECT codnom 
							   				     FROM   nom_nominas_pago 
							                     WHERE  anio={$anio} AND mes={$j} AND frecuencia IN(" . implode(",", $frecuencia) . "))";
					$total = $db->query($sql2)->fetch_object()->total;

					$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, get_nombre_mes($j-1, 'capitalize'))
											      ->setCellValue('B'.$i, $total);
					$acumulado += $total;

					$i++;
				}

				if($mes_ing!=1) $anio++; // Año actual

				for ($j=1; $j<=$mes; $j++) 
				{ 
					$frecuencia = array(2, 3); // Primera y Segunda Quincena

					if($j==$mes)
					{
						if($fecha_vac->format('j') == 1)
							break;
						else
							$frecuencia = array(2); // Tomar en cuenta solo la Primera Quincena
					}

					if($j==1 && $mes_ing!=1)
					{
						$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, 'Año '.$anio);
						$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getFont()->setBold(true);
						$i++;
					}

					$sql2 = "SELECT COALESCE(SUM(n.monto),0) as total
						     FROM   nom_movimientos_nomina n
							 WHERE  n.ficha={$personal->ficha}
							 AND    {$condicion_sueldodiv} 
							 AND    n.codnom IN (SELECT codnom 
							   				     FROM   nom_nominas_pago 
							                     WHERE  anio={$anio} AND mes={$j} AND frecuencia IN(" . implode(",", $frecuencia) . "))";
					$total = $db->query($sql2)->fetch_object()->total;

					$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, get_nombre_mes($j-1, 'capitalize'))
											      ->setCellValue('B'.$i, $total);

					$acumulado += $total;

					$i++;
				}

				$i++;
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, 'TOTAL ACUMULADO')
											  ->setCellValue('B'.$i, '=SUM(B'.$aux_i.':B'.($i-1).')');

				$total_acumulado = "='{$title}'!B".$i;

				$objPHPExcel->getActiveSheet()->getStyle('A'.$aux_i.':G'.$i)->getFont()->setItalic(true);

				$objPHPExcel->getActiveSheet()->getStyle('B'.$aux_i.':B'.($i-1))->getNumberFormat()->setFormatCode('#,##0.00');
				$objPHPExcel->getActiveSheet()->getStyle('B'.$i)->getNumberFormat()->setFormatCode('"B/." * #,##0.00');

				$objPHPExcel->getActiveSheet()->getStyle('A'.($aux_i-1).':B'.$i)->applyFromArray(allBorders());

				$i++;
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, 'VACACIONES BRUTAS')
											  ->setCellValue('C'.$i, '=B'.($i-1).'/11')
											  ->setCellValue('A'.($i+2), ' DEDUCCIONES')
											  ->setCellValue('D'.($i+2), 'SEGURO SOCIAL')
											  ->setCellValue('D'.($i+3), 'SEGURO EDUCATIVO')
											  ->setCellValue('D'.($i+4), 'TOTAL DEDUCCIONES')
											  ->setCellValue('F'.($i+4), '=F'.($i+2).'+F'.($i+3));

				$vacaciones_brutas = "='{$title}'!C".$i;

				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':A'.($i+2))->getFont()->setBold(true);
				$objPHPExcel->getActiveSheet()->getStyle('D'.($i+4))->getFont()->setBold(true);

				$objPHPExcel->getActiveSheet()->getStyle('C'.$i)->applyFromArray(borderBottom(PHPExcel_Style_Border::BORDER_THIN));
				$objPHPExcel->getActiveSheet()->getStyle('F'.($i+3))->applyFromArray(borderBottom(PHPExcel_Style_Border::BORDER_THIN));

				$objPHPExcel->getActiveSheet()->getStyle('C'.$i)->getNumberFormat()->setFormatCode('"B/." * #,##0.00');
				$objPHPExcel->getActiveSheet()->getStyle('F'.($i+2).':F'.($i+4))->getNumberFormat()->setFormatCode('"B/." * #,##0.00');		

				$i+=7;
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, 'VACACIONES NETAS')
											  ->setCellValue('C'.$i, '=C'.($i-7).'-F'.($i-3));
									  
				$objPHPExcel->getActiveSheet()->setCellValue('A'.($i+2), 'Preparado por:'.$ger_rrhh);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':A'.($i+2))->getFont()->setBold(true);
				$objPHPExcel->getActiveSheet()->setCellValue('C'.($i+2), 'Revisado por:'.$jefe_registro);
				$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':C'.($i+2))->getFont()->setBold(true);
				$objPHPExcel->getActiveSheet()->setCellValue('E'.($i+2), 'Aprobado por:'.$jefe_planilla);
				$objPHPExcel->getActiveSheet()->getStyle('E'.$i.':E'.($i+2))->getFont()->setBold(true);

				$vacaciones_netas  = "='{$title}'!C".$i;

				if($acumulado>0)
				{
					$empleados[] = array('nombre' => $nombre_completo,  'unidad' => $personal->ficha, 
						                 'fecing' => $fecha_entrada,
					                     'cedula' => $personal->cedula, 'fecvac' => $nombre_periodo, 
					                     'monto'  => $total_acumulado, 'vac' => $vacaciones_brutas, 
					                     'ss' => 0, 'se' => 0, 'neto' => $vacaciones_netas);
				}

				$objPHPExcel->getActiveSheet()->getStyle('A'.($i-6).':G'.$i)->getFont()->setItalic(true);

				$objPHPExcel->getActiveSheet()->getStyle('C'.$i)->getNumberFormat()->setFormatCode('"B/." * #,##0.00');
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':C'.$i)->getFont()->setBold(true);

				$objPHPExcel->getActiveSheet()->getStyle('A'.($i+2))->getFont()->setBold(true)->setItalic(true);
				$objPHPExcel->getActiveSheet()->getStyle('A'.($i+2).':G'.($i+3))->getFont()->setSize(11);

				$objPHPExcel->getActiveSheet()->getStyle('C'.$i)->applyFromArray(borderBottom(PHPExcel_Style_Border::BORDER_DOUBLE));

				$objPHPExcel->getActiveSheet()->getStyle('C'.($i+2).':C'.($i+3))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);				
			}

			$objPHPExcel->getActiveSheet()->setSelectedCells('F'.$i);

			$sheet++;
		}
	}
	//==============================================================================================
	// Hoja de cálculo => TOTAL BRENT

	if($sheet>0)
	{
		$objPHPExcel->createSheet();
		$objPHPExcel->setActiveSheetIndex($sheet);
	}

	$objPHPExcel->getActiveSheet()->setTitle('TOTAL VACACIONES');
	

	$objPHPExcel->getActiveSheet()->setCellValue('A1', $nom_emp)
								  ->setCellValue('A2', 'NOMBRE')
								  ->setCellValue('B2', 'UNIDAD')
								  ->setCellValue('C2', 'FECHA DE INGRESO')
								  ->setCellValue('D2', 'CEDULA')
								  ->setCellValue('E2', 'FECHA DE VAC')
								  ->setCellValue('F2', 'MONTO ACUMULADO')
								  ->setCellValue('G2', 'VAC')
								  ->setCellValue('H2', 'SS')
								  ->setCellValue('I2', 'SE')
								  ->setCellValue('J2', 'NETO');

	$objPHPExcel->getActiveSheet()->mergeCells('A1:J1');

	cellColor('A1:J1',  '0F243E');  // Celdas con color de fondo	
	colorTexto('A1:J1', 'FFFFFF'); // Celdas con color de texto diferente

	$objPHPExcel->getActiveSheet()->getStyle('A1:J2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('A1:J2')->getFont()->setBold(true);

	$objPHPExcel->getActiveSheet()->getStyle('A2:J2')->getAlignment()->setWrapText(true); 
	$objPHPExcel->getActiveSheet()->getStyle('A2:J2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

	$i=3;
	foreach ($empleados as $empleado) 
	{
		$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, $empleado['nombre'])
								      ->setCellValue('B'.$i, $empleado['unidad'])
								      ->setCellValue('C'.$i, $empleado['fecing'])
								      ->setCellValue('D'.$i, $empleado['cedula'])
								      ->setCellValue('E'.$i, $empleado['fecvac'])
								      ->setCellValue('F'.$i, $empleado['monto'])
								      ->setCellValue('G'.$i, $empleado['vac'])
								      ->setCellValue('H'.$i, $empleado['ss'])
								      ->setCellValue('I'.$i, $empleado['se'])
								      ->setCellValue('J'.$i, $empleado['neto']);
		$i++;
	}

	$objPHPExcel->getActiveSheet()->getStyle('A1:J2')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('A2:J'.($i-1))->getFont()->setSize(11);

	$objPHPExcel->getActiveSheet()->getStyle('A1:J'.($i-1))->applyFromArray(allBorders());

	$objPHPExcel->getActiveSheet()->getStyle('J'.($i+1))->getFont()->setBold(true)->setSize(11);
	$objPHPExcel->getActiveSheet()->getStyle('J'.($i+1))->getNumberFormat()->setFormatCode('"B/." * #,##0.00');

	if($i>3)
	{
		$objPHPExcel->getActiveSheet()->setCellValue('J'.($i+1), '=SUM(J3:J'.($i-1).')');

		$objPHPExcel->getActiveSheet()->getStyle('F3:J'.($i-1))->getNumberFormat()->setFormatCode('#,##0.00');

		$objPHPExcel->getActiveSheet()->getStyle('B3:D'.($i-1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	}
	else
	{
		$objPHPExcel->getActiveSheet()->setCellValue('J'.($i+1), 0);
	}

	$objPHPExcel->getActiveSheet()->setSelectedCells('L'.($i+1));

	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(23);  
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(12); 
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(12); 
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(14); 
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(36); 
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(13); 
	$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(9); 
	$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(10); 
	$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(8); 
	$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(13); 

	//==============================================================================================
	//Ubicarse en la primera hoja
	$objPHPExcel->setActiveSheetIndex(0);

	$nom_periodo = '';

	if($periodo==1)
		$nom_periodo = ' - Periodo 1 al 30';
	else if($periodo==2)
		$nom_periodo = ' - Periodo 16 al 15';

	$filename = 'excel/VACACIONES '.$mes_act_mayus.' '.$anio. $nom_periodo. '.xlsx';

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');	

	$objWriter->save($filename);

	echo $filename;
}
else
{
   echo "<script>alert('Acceso Denegado');</script>";  
   echo "<script>document.location.href = 'config_rpt_vacaciones.php?modulo='".$modulo.";</script>";
}

exit;