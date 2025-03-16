<?php
require_once('../lib/database.php');

error_reporting(1);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('America/Panama');

set_time_limit(0);
$total_vacaciones = $total_regular1 = $total_regular2 = $total_xiiimes = $total_isr = $total_nacional1 = $total_nacional2 = 0;
$total_vacaciones_liq   = $total_xiiimes_liq  = $total_prima_antiguedad  = $total_se = $total_ss = 0;

if (PHP_SAPI == 'cli')
	die('Este ejemplo solo se debe ejecutar desde un navegador Web');

// Include PHPExcel
include('lib/php_excel.php');
include('lib/phpexcel_conceptos.php');
require_once("phpexcel/Classes/PHPExcel.php");

if(isset($_REQUEST['fecha_inicio']) && isset($_REQUEST['fecha_fin']))
{
	$fecha_inicio = $_REQUEST['fecha_inicio'];
	$fecha_fin    = $_REQUEST['fecha_fin'];
	$fecha_inicio = date('Y-m-d', strtotime($fecha_inicio)) ;
	$fecha_fin    = date('Y-m-d', strtotime($fecha_fin)) ;

	$db = new Database($_SESSION['bd']);

	$sql = "SELECT codnom,tipnom 
			FROM nom_nominas_pago 
			WHERE periodo_ini >= '{$fecha_inicio}' 
			AND periodo_fin <= '{$fecha_fin}'
                        AND tipnom = '{$_SESSION['codigo_nomina']}'";
	$res = $db->query($sql);
//        echo $sql;
//        exit;
	$nominas = array();

	while ( $obj = $res->fetch_object()) {
		array_push($nominas,$obj->codnom);
	}
	
	for ($jxx=0; $jxx < count($nominas); $jxx++) 
	{ 
		if($jxx==(count($nominas)-1))
		{
			$codigos .= $nominas[$jxx];
		}
		else
		{
			$codigos .= $nominas[$jxx].",";
		}
	}
        
//	while ($obj = $res->fetch_object() ) 
//	{

		$nombre_mes = array("Enero", "Febrero", "Marzo",      "Abril",   "Mayo",      "Junio",
			                "Julio", "Agosto",  "Septiembre", "Octubre", "Noviembre", "Diciembre");

		// Consultar mes de las planillas
		$sql = "SELECT DATE_FORMAT(periodo_ini, '%M') as nom_mes, DATE_FORMAT(periodo_ini, '%Y') as anio, mes
				FROM   nom_nominas_pago
				WHERE  codnom IN ({$codigos}) AND tipnom = '{$_SESSION['codigo_nomina']}'";
		$res = $db->query($sql);

		if($planilla1 = $res->fetch_object())
		{
			$mes_planilla  = traducir_mes($planilla1->nom_mes, 'capitalize');
			$anio_planilla = $planilla1->anio;

			$mes_siguiente = $nombre_mes[ ($planilla1->mes == 12 ? 0 : $planilla1->mes) ];
		}

		$objPHPExcel = new PHPExcel();

		$objPHPExcel->getProperties()->setCreator("Selectra Planilla")
									 ->setLastModifiedBy("Selectra Planilla")
									 ->setTitle("Reporte SIPE");

		// Tipo de letra por defecto del Libro
		$objPHPExcel->getActiveSheet()->getDefaultStyle()->getFont()->setName('Century Gothic')->setSize(11); 

		// Color de fondo por defecto del Libro
		//colorFondoLibro('FFFFFF'); // Hojas Fondo Blanco
		//==============================================================================================
		$objPHPExcel->getActiveSheet()->setTitle('Hoja 1');

		$objPHPExcel->getActiveSheet()->getPageSetup()->setScale(33);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);

		$objPHPExcel->getActiveSheet()->getSheetView()->setZoomScale(80);
		$objPHPExcel->getActiveSheet()->getSheetView()->setView(PHPExcel_Worksheet_SheetView::SHEETVIEW_PAGE_BREAK_PREVIEW);

		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(8);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(10);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(16);
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(18);
		$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(13);
		$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(17);
		$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(17);
		$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(35);
		$objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('V')->setWidth(20);

		/*$objDrawing = new PHPExcel_Worksheet_Drawing();
		$objDrawing->setName('Logo');
	    $objDrawing->setDescription('Logo Brentwood');
		$objDrawing->setCoordinates('A1');
		$objDrawing->setPath('imagenes/logo_excel.png');
		$objDrawing->setOffsetX(30);
		$objDrawing->setOffsetY(10);
		$objDrawing->setResizeProportional(false);
		$objDrawing->setHeight(32.50); // 32.50px = 0.86 cm
		$objDrawing->setWidth(168.94); // 168.94px = 4.47 cm
		$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());*/

		$objPHPExcel->getActiveSheet()->setCellValue('A1', 'SIPE ' . strtoupper($mes_planilla) . ' ' . $anio_planilla )
									  ->setCellValue('A2', 'PLANILLA REGULAR')
									  ->setCellValue('Q2', 'VACACIONES')
									  ->setCellValue('T2', 'LIQUIDACIONES')
									  ->setCellValue('A3', 'Código')
									  ->setCellValue('B3', 'Cédula')
									  ->setCellValue('C3', 'Nombre')
									  ->setCellValue('D3', 'I Quincena')
									  ->setCellValue('E3', 'II Quincena')
									  ->setCellValue('F3', 'Horas Extras')
									  ->setCellValue('G3', 'XIII MES')
									  ->setCellValue('H3', 'XIII MES GP')
									  ->setCellValue('I3', 'Comisión')
									  ->setCellValue('J3', 'Gastos Rep.')
									  ->setCellValue('K3', 'Vacaciones')
									  ->setCellValue('L3', strtoupper($mes_planilla))
									  ->setCellValue('M3', 'ISR')
									  ->setCellValue('N3', 'S.E.')
									  ->setCellValue('O3', 'S.S.')
									  ->setCellValue('P3', 'Fecha de Vacaciones')
									  ->setCellValue('Q3', 'Monto Vacaciones')
									  ->setCellValue('R3', 'Vacaciones Proporcionales')
									  ->setCellValue('S3', 'XIII MES Proporcional')
									  ->setCellValue('T3', 'Prima de Antigüedad')
									  ->setCellValue('U3', 'Indem/prima/preaviso')
									  ->setCellValue('V3', 'Observación');

		cellColor('A1:O2',  '0F243E');  // Fondo Azul Oscuro	
		colorTexto('A1:V2', 'FFFFFF');  // Texto Blanco

		cellColor('P2', '16365C');
		cellColor('S2', '366092');

		$objPHPExcel->getActiveSheet()->mergeCells('A1:V1');
		$objPHPExcel->getActiveSheet()->mergeCells('A2:O2');
		$objPHPExcel->getActiveSheet()->mergeCells('P2:R2');
		$objPHPExcel->getActiveSheet()->mergeCells('S2:V2');

		$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(14);
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

		$objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(36);

		$objPHPExcel->getActiveSheet()->getStyle('A1:V2')->getFont()->setName('Calibri');

		$objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setSize(12);


		$objPHPExcel->getActiveSheet()->getStyle('A1:V3')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('A1:V3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

		$objPHPExcel->getActiveSheet()->getStyle('A2:O2')->applyFromArray(borderTop(PHPExcel_Style_Border::BORDER_MEDIUM));
		$objPHPExcel->getActiveSheet()->getStyle('H2')->applyFromArray(borderRight(PHPExcel_Style_Border::BORDER_MEDIUM));
		$objPHPExcel->getActiveSheet()->getStyle('P2')->applyFromArray(borderRight(PHPExcel_Style_Border::BORDER_MEDIUM));
		$objPHPExcel->getActiveSheet()->getStyle('T2')->applyFromArray(borderRight(PHPExcel_Style_Border::BORDER_MEDIUM));

		$objPHPExcel->getActiveSheet()->getStyle('A3:V3')->applyFromArray(allBorders());
		$objPHPExcel->getActiveSheet()->getStyle('A3:V3')->getAlignment()->setWrapText(true); 

		
		//==============================================================================================

		// Obtener las variables y conceptos de la tabla caa_conceptos

		$conceptos = obtener_conceptos( array('regular',
	'domingo',
	'nacional',
	'ausencia',
	'ss',
	'se',
	'otros',
	'acreedores',
	'extra',
	'tardanza',
	'extradom',
	'extranac',
	'novena',
	'bono',
	'isr') );

		// Consultar los departamentos disponibles en BRENTWOOD
		$sql = "SELECT codorg, descrip FROM nomnivel1 ORDER BY codorg";
		$res = $db->query($sql);

		$i = 4; 

		while($codnivel1 = $res->fetch_object())
		{
			if($codnivel1->codorg==2)
			{
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, 'ADMINISTRATIVOS'); // $codnivel1->descrip
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getFont()->setBold(true);
				$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':C'.$i);
				$objPHPExcel->getActiveSheet()->mergeCells('D'.$i.':P'.$i);
				//$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				cellColor('A'.$i.':V'.$i,  '0F243E');
				colorTexto('A'.$i.':V'.$i, 'FFFFFF');

				$i_aux = $i;

				$i++;
			}
                        
//                        $sql1 = "SELECT  p.ficha, p.cedula, p.suesal, p.fecing, p.estado,
//							 SUBSTRING_INDEX(p.nombres, ' ', 1) as primer_nombre,
//			 				 CASE WHEN SUBSTRING_INDEX(p.apellidos, ' ', 1)!='DE'    THEN SUBSTRING_INDEX(p.apellidos, ' ', 1)
//			 				      WHEN SUBSTRING_INDEX(p.apellidos, ' ', 2)!='DE LA' THEN SUBSTRING_INDEX(p.apellidos, ' ', 2) 
//			 				      ELSE SUBSTRING_INDEX(p.apellidos, ' ', 3) 
//			 				 END primer_apellido,
//			 				 p.fechavac, (p.fechareivac - INTERVAL 1 DAY) as fechareivac
//					 FROM    nompersonal p
//					 WHERE   p.codnivel1='{$codnivel1->codorg}' 
//					 AND     p.ficha IN (SELECT ficha FROM nom_movimientos_nomina WHERE codnom IN ({$codigos}))";
//                                         
			$sql1 = "SELECT  p.ficha, p.cedula, p.suesal, p.fecing, p.estado,
							 SUBSTRING_INDEX(p.nombres, ' ', 1) as primer_nombre,
			 				 CASE WHEN SUBSTRING_INDEX(p.apellidos, ' ', 1)!='DE'    THEN SUBSTRING_INDEX(p.apellidos, ' ', 1)
			 				      WHEN SUBSTRING_INDEX(p.apellidos, ' ', 2)!='DE LA' THEN SUBSTRING_INDEX(p.apellidos, ' ', 2) 
			 				      ELSE SUBSTRING_INDEX(p.apellidos, ' ', 3) 
			 				 END primer_apellido,
			 				 p.fechavac, (p.fechareivac - INTERVAL 1 DAY) as fechareivac
					 FROM    nompersonal p
                                         WHERE   p.codnivel1='{$codnivel1->codorg}' 
                                         AND  p.ficha IN 
                                         (SELECT DISTINCT ficha FROM nom_movimientos_nomina 
                                         WHERE codnom IN ({$codigos})
                                         AND tipnom = '{$_SESSION['codigo_nomina']}')";
                                         
//                         echo $sql1;
//                        exit;
			$res1 = $db->query($sql1);

			while($personal = $res1->fetch_object())
			{
				$sql2 = "";

				foreach($conceptos as $concepto)
				{
					if($sql2!="") $sql2 .= "UNION ";

					if($concepto["variable"]=='regular'  ||  $concepto["variable"]=='nacional')
					{
						$sql2 .= "SELECT COALESCE(SUM(n.valor),0) as horas, COALESCE(SUM(n.monto),0) as total, '{$concepto["variable"]}1' as variable
								  FROM   nom_movimientos_nomina n
								  WHERE  n.codnom IN ({$codigos}) AND n.ficha='{$personal->ficha}' 
								  AND    {$concepto["condicion"]} 
								  UNION
								  SELECT COALESCE(SUM(n.valor),0) as horas, COALESCE(SUM(n.monto),0) as total, '{$concepto["variable"]}2' as variable
								  FROM   nom_movimientos_nomina n
								  WHERE  n.codnom IN ({$codigos}) AND n.ficha='{$personal->ficha}' 
								  AND    {$concepto["condicion"]} ";					
					}
					else
					{
						$sql2 .= "SELECT COALESCE(SUM(n.valor),0) as horas, COALESCE(SUM(n.monto),0) as total, 
						                 '{$concepto["variable"]}' as variable
								  FROM   nom_movimientos_nomina n
								  WHERE  n.codnom IN ({$codigos}) AND n.ficha='{$personal->ficha}'  AND    {$concepto["condicion"]} ";
//                                                if($concepto["variable"]=="isr")
//                                                {
//                                                    echo $sql2;
//                                                    exit;
//                                                }
					}
				}

				$res2 = $db->query($sql2);

				while($fila2 = $res2->fetch_object())
				{
					$var_horas = "horas_".$fila2->variable;
					$var_total = "total_".$fila2->variable;

					$$var_horas = $fila2->horas; // Variable de nombre variable (variables variables)
					$$var_total = $fila2->total; // Variable de nombre variable (variables variables)			
				}

				$nombre_completo = $personal->primer_nombre . ' ' . $personal->primer_apellido;

				$fecha_vac = get_fecha_vacaciones($personal->fecing, $anio_planilla);

				if($fecha_vac->format('j') == 1)
				{
					$nombre_periodo = "01 al 30 de {$mes_planilla}";
				}
				else
				{
					$nombre_periodo = "16 {$mes_planilla} al 15 {$mes_siguiente}"; 
				}
				$SQL = "SELECT ifnull(sum(monto),0) as monto FROM nom_movimientos_nomina  where ficha='{$personal->ficha}' AND codcon IN (106,107,108,109,112,113,115,116,119,120,121,122,123,124,125,125,126,127,128,129,130,131,132,133,134,135,136,136,139,142,143,144,149,150,151,152,153,154,628) "
                                . "AND codnom IN ({$codigos})";
				$total_horas_extras = $db->query($SQL)->fetch_assoc();
				$SQL2 = "SELECT ifnull(sum(monto),0) as monto FROM nom_movimientos_nomina  where ficha='{$personal->ficha}' AND codcon IN (102) "
                                . "AND codnom IN ({$codigos}) ";
				$total_xiii = $db->query($SQL2)->fetch_assoc();
				$SQL2 = "SELECT ifnull(sum(monto),0) as monto FROM nom_movimientos_nomina  where ficha='{$personal->ficha}' AND codcon IN (101) "
                                . "AND codnom IN ({$codigos})";
				$total_xiii_gp = $db->query($SQL2)->fetch_assoc();
				$SQL2 = "SELECT ifnull(sum(monto),0) as monto FROM nom_movimientos_nomina  where ficha='{$personal->ficha}' AND codcon IN (157) "
                                . "AND codnom IN ({$codigos})";
				$total_comision = $db->query($SQL2)->fetch_assoc();

				$SQL3 = "SELECT ifnull(sum(monto),0) as monto FROM nom_movimientos_nomina  where ficha='{$personal->ficha}' AND codcon IN (102) "
                                . "AND codnom IN ({$codigos})";
				$total_xiii2 = $db->query($SQL3)->fetch_assoc();

				$SQL4 = "SELECT ifnull(sum(monto),0) as monto FROM nom_movimientos_nomina  where ficha='{$personal->ficha}' AND codcon IN (93) "
                                . "AND codnom IN ({$codigos})";
				$total_prima = $db->query($SQL4)->fetch_assoc();

				$SQL5 = "SELECT ifnull(sum(monto),0) as monto FROM nom_movimientos_nomina  where ficha='{$personal->ficha}' AND codcon IN (102)"
                                . " AND codnom IN ({$codigos})";
				$total_vac = $db->query($SQL5)->fetch_assoc();
				$SQL6 = "SELECT ifnull(sum(monto),0) as monto FROM nom_movimientos_nomina  where ficha='{$personal->ficha}' AND codcon IN (145) "
                                . "AND codnom IN ({$codigos})";
				$total_gr = $db->query($SQL6)->fetch_assoc();
				$SQL7 = "SELECT ifnull(sum(monto),0) as monto FROM nom_movimientos_nomina  where ficha='{$personal->ficha}' AND codcon IN (156) "
                                . "AND codnom IN ({$codigos})";
				$total_ua = $db->query($SQL7)->fetch_assoc();

				$en_vacaciones = ($personal->fechavac>=$periodo_ini && $personal->fechavac<=$periodo_fin && 
					              $personal->estado!='Egresado') ? true : false;
				$liquidado = ($personal->fechavac>=$periodo_ini && $personal->fechavac<=$periodo_fin && 
					              $personal->estado!='Egresado') ? true : false;
				
//				if($personal->estado == 'Egresado')
//				{
//					$sql_vac = "SELECT codnom
//					FROM nom_nominas_pago 
//					WHERE '{$personal->fechavac}'>=periodo_ini AND '{$personal->fechavac}'<= periodo_fin AND frecuencia = 8 
//					AND codnom IN ({$codigos})";
//                                        echo $sql_vac;
//                                        exit;
//					$codnom_vac = $db->query($sql_vac)->fetch_assoc();
//					$codigo = $codnom_vac['codnom'];
//
//					$vac_sql = "SELECT
//					COALESCE((SELECT SUM(n.monto) as monto FROM nom_movimientos_nomina n 
//						WHERE  ficha='{$personal->ficha}' AND n.codnom in ($codigo) AND n.codcon in (114)), 0) as monto_vac,
//					COALESCE((SELECT SUM(n.monto) AS monto FROM nom_movimientos_nomina n 
//						WHERE  ficha='{$personal->ficha}' AND n.codnom in ($codigo) AND n.codcon in (117)), 0) as prop_vac";
//					$monto_vac = $db->query($sql_vac)->fetch_assoc();
//
//					//echo $vac_sql;
//
//
//				}

				$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, $personal->ficha)
				->setCellValue('B'.$i, $personal->cedula)
				->setCellValue('C'.$i, $nombre_completo)
				->setCellValue('D'.$i, ($codnivel1->codorg==2)? $total_regular1 : ($total_regular1 + $total_nacional1) )
//				->setCellValue('E'.$i, ($codnivel1->codorg==2) ? $total_regular2 : ($total_regular2 + $total_nacional2) )
				->setCellValue('F'.$i, ($total_horas_extras['monto']==0 ? '' :$total_horas_extras['monto']))
				->setCellValue('G'.$i, ($total_xiii['monto']==0 ? '' : $total_xiii['monto']))
				->setCellValue('H'.$i, ($total_xiii_gp['monto']==0 ? '' : $total_xiii_gp['monto']))
				->setCellValue('I'.$i, ($total_comision['monto']==0 ? '' : $total_comision['monto']))
				->setCellValue('J'.$i, ($total_gr['monto']==0 ? '' : $total_gr['monto']))
				->setCellValue('K'.$i, ($monto_vac['monto_vac']==0 ? '' : $monto_vac['monto_vac']))
				->setCellValue('L'.$i, "=D".$i."+E".$i."+F".$i."+G".$i."+H".$i."+I".$i."+J".$i."+K".$i)
				->setCellValue('M'.$i, ($total_isr==0 ? 0.00 : $total_isr))
				->setCellValue('N'.$i, ($total_se==0 ? 0.00: $total_se ))
				->setCellValue('O'.$i, ($total_ss==0 ? 0.00: $total_ss ))
				->setCellValue('P'.$i, ($en_vacaciones ? $nombre_periodo : '' ))
				->setCellValue('Q'.$i, ($total_vac['monto']==0     ? '' : $total_vac['monto']))
				->setCellValue('R'.$i, ($total_vac_liq['monto']==0     ? '' : $total_vac_liq['monto']))
				->setCellValue('S'.$i, ($total_xiiimes_liq==0    ? '' : $total_xiiimes_liq))
				->setCellValue('V'.$i, $personal->estado);

				if($i%88==0)
				{
					// Add a page break 88-89
					$objPHPExcel->getActiveSheet()->setBreak('A'.$i, PHPExcel_Worksheet::BREAK_ROW);
				}

				$i++;
			}
		}

		if($i>4)
		{
			$objPHPExcel->getActiveSheet()->getStyle('A4:V'.($i+5))->applyFromArray(allBorders());

			$objPHPExcel->getActiveSheet()->getStyle('F4:F'.($i-1))->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('L4:L'.($i-1))->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('A4:C'.($i-1))->getFont()->setSize(10);

			$objPHPExcel->getActiveSheet()->getStyle('H4:H'.($i-1))->getFont()->setSize(10)->setBold(true)->setItalic(true);
			$objPHPExcel->getActiveSheet()->getStyle('I4:I'.($i-1))->getFont()->setSize(10)->setBold(true)->setItalic(true);
			$objPHPExcel->getActiveSheet()->getStyle('J4:J'.($i-1))->getFont()->setSize(10)->setBold(true)->setItalic(true);

			$objPHPExcel->getActiveSheet()->getStyle('A'.$i_aux)->getFont()->setSize(11)->setName('Calibri');

			$objPHPExcel->getActiveSheet()->getStyle('A4:A'.($i-1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			// $objPHPExcel->getActiveSheet()->getStyle('H3:H'.($i-1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

			// $objPHPExcel->getActiveSheet()->getStyle('H3:H'.($i-1))->getAlignment()->setWrapText(true); 

			$objPHPExcel->getActiveSheet()->getStyle('D4:E'.($i-1))->getNumberFormat()->setFormatCode('#,##0.00');
			$objPHPExcel->getActiveSheet()->getStyle('K'.($i+5).':L'.($i+5))->getNumberFormat()->setFormatCode('#,##0.00');
			$objPHPExcel->getActiveSheet()->getStyle('F4:H'.($i+6))->getNumberFormat()->setFormatCode('"B/." * #,##0.00');
			$objPHPExcel->getActiveSheet()->getStyle('I4:I'.($i+6))->getNumberFormat()->setFormatCode('"B/." * #,##0.00');
			$objPHPExcel->getActiveSheet()->getStyle('J4:J'.($i+6))->getNumberFormat()->setFormatCode('"B/." * #,##0.00');
			$objPHPExcel->getActiveSheet()->getStyle('K4:O'.($i+6))->getNumberFormat()->setFormatCode('"B/." * #,##0.00');

			$objPHPExcel->getActiveSheet()->setCellValue('F'.($i+5), '=SUM(F4:F'.($i+4).')')
										  ->setCellValue('G'.($i+5), '=SUM(G4:G'.($i+4).')')
										  ->setCellValue('H'.($i+5), '=SUM(H4:H'.($i+4).')')
										  ->setCellValue('I'.($i+5), '=SUM(I4:I'.($i+4).')')
										  ->setCellValue('J'.($i+5), '=SUM(J4:J'.($i+4).')')
										  ->setCellValue('L'.($i+5), '=SUM(L4:L'.($i+4).')')
										  ->setCellValue('M'.($i+5), '=SUM(M4:M'.($i+4).')')
										  ->setCellValue('N'.($i+5), '=SUM(N4:N'.($i+4).')')
										  ->setCellValue('O'.($i+5), '=SUM(O4:O'.($i+4).')')
										  ->setCellValue('G'.($i+6), '=G'.($i+5).'+N'.($i+5))
										  ->setCellValue('H'.($i+6), '=SUM(H'.($i+5).'+I'.($i+5).'+J'.($i+5).')')
										  ->setCellValue('L'.($i+6), '=L'.($i+5).'-M'.($i+5).'-N'.($i+5).'-O'.($i+5));

		//$objPHPExcel->getActiveSheet()->getPageSetup()->setPrintArea('A1:Q'.($i+6));
		}
//	}


	$sql = "SELECT codnom,tipnom 
			FROM nom_nominas_pago 
			WHERE periodo_ini >= '{$fecha_inicio}' 
			AND periodo_fin <= '{$fecha_fin}'
                        AND tipnom = '{$_SESSION['codigo_nomina']}'";
	$res = $db->query($sql);
	$nominas = array();

	while ( $obj = $res->fetch_object()) {
		array_push($nominas,$obj->codnom);
	}
	
	for ($jxx=0; $jxx < count($nominas); $jxx++) 
	{ 
		if($jxx==(count($nominas)-1))
		{
			$codigos .= $nominas[$jxx];
		}
		else
		{
			$codigos .= $nominas[$jxx].",";
		}
	}



	$ixx = $i+8;
	cellColor('C'.$ixx.':F'.$ixx,  '0F243E');  // Fondo Azul Oscuro	
	colorTexto('C'.$ixx.':F'.$ixx, 'FFFFFF');  // Texto Blanco
	$codtip = $_SESSION['codigo_nomina'];
	$sql3 = "SELECT
	COALESCE((SELECT SUM(n.monto) as monto FROM nom_movimientos_nomina n 
		WHERE  n.codnom in ($codigos) AND n.codcon in (3000)), 0) as ss_patronal,
	COALESCE((SELECT SUM(n.monto) AS monto FROM nom_movimientos_nomina n 
		WHERE  n.codnom in ($codigos) AND n.codcon in (3001)), 0) as se_patronal,
	COALESCE((SELECT SUM(n.monto) as monto FROM nom_movimientos_nomina n 
		WHERE  n.codnom in ($codigos)  AND n.codcon in (3002)), 0) as ss_xiii_patronal,
	COALESCE((SELECT SUM(n.monto) as monto FROM nom_movimientos_nomina n 
		WHERE  n.codnom in ($codigos)  AND n.codcon in (9009)), 0) as riesgo,
	COALESCE((SELECT SUM(n.monto) as monto FROM nom_movimientos_nomina n 
		WHERE  n.codnom in ($codigos)  AND n.codcon in (201)), 0) as ss_emp,
	COALESCE((SELECT SUM(n.monto) as monto FROM nom_movimientos_nomina n 
		WHERE  n.codnom in ($codigos)  AND n.codcon in (202)), 0) as se_emp,
	COALESCE((SELECT SUM(n.monto) as monto FROM nom_movimientos_nomina n 
		WHERE  n.codnom in ($codigos)  AND n.codcon in (205)), 0) as ss_xiii_emp";
		//echo $sql3;exit();
	$conceptos = $db->query($sql3)->fetch_assoc();

	$objPHPExcel->getActiveSheet()->getStyle('C'.$ixx.':F'.$ixx)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('C'.$ixx.':F'.$ixx)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);


	$total_neto = $objPHPExcel->getActiveSheet()->getCell("L".($i+6))->getValue();;
	$riesgo      = round(($total_neto*0.021),2);
	$objPHPExcel->getActiveSheet()->setCellValue('D'.$ixx, 'S.S. PATRONAL');
	$objPHPExcel->getActiveSheet()->setCellValue('E'.$ixx, 'S.S. EMPLEADO');
	$objPHPExcel->getActiveSheet()->setCellValue('F'.$ixx, 'TOTAL');
	$ixx++;

	$objPHPExcel->getActiveSheet()->setCellValue('C'.$ixx, 'S. SOCIAL');
	$objPHPExcel->getActiveSheet()->setCellValue('D'.$ixx, $conceptos['ss_patronal']);
	$objPHPExcel->getActiveSheet()->setCellValue('E'.$ixx, '=O'.($ixx-4));
	$objPHPExcel->getActiveSheet()->setCellValue('F'.$ixx, '=D'.$ixx.'+E'.$ixx);
	$ixx++;
	$objPHPExcel->getActiveSheet()->setCellValue('C'.$ixx, 'S. SOCIAL XIII');
	$objPHPExcel->getActiveSheet()->setCellValue('D'.$ixx, $conceptos['ss_xiii_patronal']);
	$objPHPExcel->getActiveSheet()->setCellValue('E'.$ixx, $conceptos['ss_xiii_emp']);
	$objPHPExcel->getActiveSheet()->setCellValue('F'.$ixx, '=D'.$ixx.'+E'.$ixx);
	$ixx++;
	$objPHPExcel->getActiveSheet()->setCellValue('C'.$ixx, 'S. EDUCATIVO');
	$objPHPExcel->getActiveSheet()->setCellValue('D'.$ixx, $conceptos['se_patronal']);
	$objPHPExcel->getActiveSheet()->setCellValue('E'.$ixx, '=N'.($ixx-6));
	$objPHPExcel->getActiveSheet()->setCellValue('F'.$ixx, '=D'.$ixx.'+E'.$ixx);
	$ixx++;
	$objPHPExcel->getActiveSheet()->setCellValue('C'.$ixx, 'RIESGO PROF.');
	$objPHPExcel->getActiveSheet()->setCellValue('D'.$ixx, '=L'.($ixx-6).'*0.021');
	$objPHPExcel->getActiveSheet()->setCellValue('F'.$ixx, '=D'.($ixx));
	$ixx++;
	$objPHPExcel->getActiveSheet()->setCellValue('C'.$ixx, 'ISR');
	$objPHPExcel->getActiveSheet()->setCellValue('E'.$ixx, '=M'.($ixx-8));

	$ixx++;
	$objPHPExcel->getActiveSheet()->setCellValue('C'.$ixx, 'TOTALES')
								  ->setCellValue('D'.$ixx, '=SUM(D'.($ixx-1).':D'.($ixx-5).')')
								  ->setCellValue('E'.$ixx, '=SUM(E'.($ixx-1).':E'.($ixx-5).')')
								  ->setCellValue('F'.$ixx, '=SUM(F'.($ixx-1).':F'.($ixx-5).')');
	$ixx++;

	//$objPHPExcel->getActiveSheet()->insertNewRowBefore(2,1); // A partir de fila 2 - agregue una fila

	//==============================================================================================
	// Ubicarse en la primera hoja

	$objPHPExcel->setActiveSheetIndex(0);
	$objPHPExcel->getActiveSheet()->setSelectedCells('A65');

	$filename = 'excel/Reporte SIPE '.$mes_planilla. ' '.$anio_planilla.'.xlsx';

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');	

	$objWriter->save($filename);

	echo $filename;
}
else
{
   echo "<script>alert('Acceso Denegado');</script>";  
   echo "<script>document.location.href = 'config_rpt_sipe_brentwood.php';</script>";
}

exit;