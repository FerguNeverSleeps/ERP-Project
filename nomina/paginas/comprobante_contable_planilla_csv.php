<?php
session_start();
error_reporting(0);
ini_set('display_errors', FALSE);
ini_set('display_startup_errors', FALSE);
date_default_timezone_set('America/Panama');

if (PHP_SAPI == 'cli')
	die('Este ejemplo solo se debe ejecutar desde un navegador Web');

include('../lib/common.php');
include('../paginas/lib/php_excel.php');

function formato_fecha($fecha,$formato)
    {
	    if (empty($fecha))
	    {
	    	$fecha = date('Y-m-d');
	    }
	    $meses = array("ENERO","FEBRERO","MARZO","ABRIL","MAYO","JUNIO","JULIO","AGOSTO","SEPTIEMBRE","OCTUBRE","NOVIEMBRE","DICIEMBRE");
        $separa = explode("-",$fecha);
        $dia = $separa[2];
	    $mes = $separa[1];
	    $anio = $separa[0];
	    switch ($formato)
	    {
	        case 1:
	            $f = $dia." de ".$meses[$mes-1]." de ".$anio;
	            break;
	        case 2:
	            $f = $dia." del mes ".$meses[$mes-1]." de ".$anio;
	            break;
	        default:
	            break;
	    }
	    return $f;
    }

if(isset($_GET['codnom']) && isset($_GET['codtip']))
{
	$codnom=$_GET['codnom'];
	$codtip=$_GET['codtip'];

	$conexion= new bd($_SESSION['bd']);

	$sql = "SELECT UPPER(t.descrip) as descrip 
			FROM   nomtipos_nomina t
			WHERE  t.codtip=".$codtip;
	$res=$conexion->query($sql);

	if($fila=$res->fetch_array())
	{
		$NOMINA = $fila['descrip']; 
	}

	require_once '../paginas/phpexcel/Classes/PHPExcel.php';

	$objPHPExcel = new PHPExcel();

	$objPHPExcel->getProperties()->setCreator("Selectra")
								->setLastModifiedBy("Selectra")
								->setTitle("Horizontal de Planilla");

	


	$sql = "SELECT e.nom_emp as empresa, e.rif, e.tel_emp as telefono, e.dir_emp as direccion, e.ciu_emp as ciudad, 
				e.edo_emp, e.imagen_izq as logo
			FROM   nomempresa e";
	$res=$conexion->query($sql);
	$fila=$res->fetch_array();
	$logo=$fila['logo'];

	$sql2 = "SELECT np.periodo_ini as desde, np.periodo_fin as hasta, np.mes, np.anio, DATE_FORMAT(np.fechapago, '%d/%m/%Y') as fecha_pago, np.codnom as planilla,
					DATE_FORMAT(np.periodo_ini,'%d') as dia_ini, DATE_FORMAT(np.periodo_fin, '%d') as dia_fin, f.descrip as frecuencia  
			FROM nom_nominas_pago np
                        LEFT JOIN nomfrecuencias f ON (np.frecuencia=f.codfre)
                        LEFT JOIN nomtipos_nomina t ON (np.tipnom=t.codtip)
			WHERE  np.codnom=".$codnom." AND np.tipnom=".$codtip;
	$res2=$conexion->query($sql2);
	$fila2=$res2->fetch_array();

	$meses = array('Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre');

	$desde=$fila2['desde'];
	$hasta=$fila2['hasta'];
        
        $desde1=formato_fecha($fila2['desde'],1);
	$hasta1=formato_fecha($fila2['hasta'],1);
        $fecha_pago=$fila2['fecha_pago'];
	$dia_ini = $fila2['dia_ini'];
	$dia_fin = $fila2['dia_fin']; 
	$mes_numero = $fila2['mes'];
	$mes_letras = $meses[$mes_numero - 1];
	$anio = $fila2['anio'];
        $frecuencia = $fila2['frecuencia'];
        $codigo = $fila2['planilla'];

	$empresa = $fila['empresa'];

	$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
	$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_LEGAL);

//	$objPHPExcel->getActiveSheet()
//				->setCellValue('A2', strtoupper($empresa) )
//				->setCellValue('A3', strtoupper($NOMINA))
//				->setCellValue('A4', 'DEL '. $desde1 .' AL '. $hasta1 )
//                                    ->setCellValue('A5', "RESUMEN CONTABLE");
//
//
//	//$objPHPExcel->getActiveSheet()->getStyle('A8:V8')->getAlignment()->setShrinkToFit(true);
//	$objPHPExcel->getActiveSheet()->getStyle('A8:Y8')->getAlignment()->setWrapText(true);
//	//$objPHPExcel->getActiveSheet()->getStyle('A2:A4')->getFont()->setName('Calibri');
//	$objPHPExcel->getActiveSheet()->getStyle('A2:A5')->getFont()->setSize(10);
//	$objPHPExcel->getActiveSheet()->getStyle('A2:A5')->getFont()->setBold(true);
//	$objPHPExcel->getActiveSheet()->getStyle('A2:A5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//	$objPHPExcel->getActiveSheet()->mergeCells('A2:D2');
//	$objPHPExcel->getActiveSheet()->mergeCells('A3:D3');
//	$objPHPExcel->getActiveSheet()->mergeCells('A4:D4');
//        $objPHPExcel->getActiveSheet()->mergeCells('A5:D5');
//	//$objPHPExcel->getActiveSheet()->getStyle('C2:D5')->applyFromArray(allBordersThin());
//	//$objPHPExcel->getActiveSheet()->getStyle('C2:D5')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	$objPHPExcel->getActiveSheet()
            ->setCellValue('A1', 'FECHA CONTABLE') 
            ->setCellValue('B1', 'CUENTA CONTABLE')    
            ->setCellValue('C1', 'CONCEPTO')            
            ->setCellValue('D1', 'DEBITO')
            ->setCellValue('E1', 'CREDITO');

//	$objPHPExcel->getActiveSheet()->getStyle('A8:D8')->getFont()->setName('Calibri');
//	$objPHPExcel->getActiveSheet()->getStyle('A8:D8')->getFont()->setSize(10);
//	$objPHPExcel->getActiveSheet()->getStyle('A8:D8')->getFont()->setBold(true);
//	$objPHPExcel->getActiveSheet()->getStyle('A8:D8')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//
//	$objPHPExcel->getActiveSheet()->getStyle('A8:D8')->applyFromArray(allBordersThin());

//	$objPHPExcel->getActiveSheet()->freezePane('B9');
	$total_salarios=$total_comision=$total_combustible=$total_reembolso=$total_uso_auto=$total_bono="=";	
        $bandera=0;

		$consulta="SELECT SUM(nm.monto) AS suma, nm.codcon, nm.descrip, nm.tipcon, c.ctacon "
                        . "FROM nom_movimientos_nomina AS nm "
                        . "LEFT JOIN nomconceptos AS c ON (nm.codcon=c.codcon) "
                        . "WHERE nm.codnom='".$codnom."' AND nm.tipnom='".$codtip."' "
                        . "GROUP BY nm.codcon "
                        . "ORDER BY nm.codcon";
//		echo $consulta;
//                exit;
		$res2=$conexion->query($consulta);
                $i=2; 
		$ini=$i; $enc=false;$cont=0;
                $salario=$neto=0;
		while($row2=$res2->fetch_array())
		{	$enc=true;
			$codcon = $row2['codcon'];
			$descrip = utf8_encode($row2['descrip']);
                        $tipcon = $row2['tipcon'];
                        $ctacon = $row2['ctacon'];
                        $suma = $row2['suma'];
                        if($codcon==100)
                        {
                            $salario=$suma;
                            $bandera=1;
                        }
                        if($codcon==198 || $codcon==199)
                            $salario=$salario-$suma;
                        
			
			
                        if($codcon!=198 && $codcon!=199)
                        {
                            $objPHPExcel->getActiveSheet()->setCellValueExplicit("A$i", $fecha_pago, PHPExcel_Cell_DataType::TYPE_STRING);
                            $objPHPExcel->getActiveSheet()->setCellValueExplicit("B$i", $ctacon, PHPExcel_Cell_DataType::TYPE_STRING);
                            $objPHPExcel->getActiveSheet()->setCellValueExplicit("C$i", $codcon."-".utf8_decode($descrip)."-".$codigo."-".$frecuencia, PHPExcel_Cell_DataType::TYPE_STRING);
                        }
                        
                        if($tipcon=='A')
                        {

                                $objPHPExcel->getActiveSheet()->setCellValue('D'.$i, number_format($suma, 2, '.', ''));
                                $totalA=$totalA+$suma;
                        }
                        if($tipcon=='D')
                        {
                                if($codcon!=198 && $codcon!=199)
                                {
                                     $objPHPExcel->getActiveSheet()->setCellValue('E'.$i, number_format($suma, 2, '.', ''));
                                }
                                $totalD=$totalD+$suma;
                        }
                        if($tipcon=='P')
                        {

                                $objPHPExcel->getActiveSheet()->setCellValue('D'.$i, number_format($suma, 2, '.', ''));                                
                                if($codcon==3000)
                                    $ctacon=2142000001;
                                if($codcon==3001)
                                    $ctacon=2142000002;
                                if($codcon==9009)
                                    $ctacon=2142000001;
                                
//                                $objPHPExcel->getActiveSheet()->getStyle('B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//                                $objPHPExcel->getActiveSheet()->getStyle('C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
//                                $objPHPExcel->getActiveSheet()->getStyle('B'.$i.':E'.$i)->applyFromArray(allBordersThin());
//                                $objPHPExcel->getActiveSheet()->getStyle('B'.$i.':E'.$i)->getFont()->setSize(8);
                                    $objPHPExcel->getActiveSheet()->getStyle('D'.$i.':E'.$i)->getNumberFormat()
										->setFormatCode('#,##0.00');
                                
                                $i++;  
                                $objPHPExcel->getActiveSheet()->setCellValueExplicit("A$i", $fecha_pago, PHPExcel_Cell_DataType::TYPE_STRING);
                                $objPHPExcel->getActiveSheet()->setCellValueExplicit("B$i", $ctacon, PHPExcel_Cell_DataType::TYPE_STRING);
                                $objPHPExcel->getActiveSheet()->setCellValueExplicit("C$i", utf8_encode($descrip)." Por Pagar-".$codigo."-".$frecuencia, PHPExcel_Cell_DataType::TYPE_STRING);
                                $objPHPExcel->getActiveSheet()->setCellValue('E'.$i, number_format($suma, 2, '.', ''));
                                
                                $totalD=$totalD+$suma;
                                $totalA=$totalA+$suma;
                            
                        }
			
//			$objPHPExcel->getActiveSheet()->getStyle('B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//                        $objPHPExcel->getActiveSheet()->getStyle('C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
//			$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':E'.$i)->applyFromArray(allBordersThin());
//                        $objPHPExcel->getActiveSheet()->getStyle('B'.$i.':E'.$i)->getFont()->setSize(8);
                        $objPHPExcel->getActiveSheet()->getStyle('D'.$i.':E'.$i)->getNumberFormat()
										->setFormatCode('#,##0.00');

                        
                        if($codcon!=198 && $codcon!=199)
                        {
                            $i++;$cont++;
                        }
		}
                $neto=$totalA-$totalD;
                if($bandera==1)
                {
                    $objPHPExcel->getActiveSheet()->setCellValue('D2', number_format($salario, 2, '.', ''));
                }
                
		if($enc)
		{
			$objPHPExcel->getActiveSheet()->setCellValueExplicit("A$i", $fecha_pago, PHPExcel_Cell_DataType::TYPE_STRING);
                        $objPHPExcel->getActiveSheet()->setCellValueExplicit("B$i", "2112000102", PHPExcel_Cell_DataType::TYPE_STRING);
                        $objPHPExcel->getActiveSheet()->setCellValueExplicit("C$i", "Total Salarios Por Pagar-".$codigo."-".$frecuencia, PHPExcel_Cell_DataType::TYPE_STRING);
                        $objPHPExcel->getActiveSheet()->setCellValue('E'.$i, number_format($neto, 2, '.', ''));   
                        
//                        $objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//                        $objPHPExcel->getActiveSheet()->getStyle('B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
//			$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':D'.$i)->applyFromArray(allBordersThin());
//                        $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':D'.$i)->getFont()->setSize(8);
                        $objPHPExcel->getActiveSheet()->getStyle('D'.$i.':E'.$i)->getNumberFormat()
										->setFormatCode('#,##0.00');

                        
                        $i++;
                        $totalD=$totalD+$neto;
//                        $objPHPExcel->getActiveSheet()->setCellValue('A'.$i, 'TOTALES' );
//                        $objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
//			$objPHPExcel->getActiveSheet()->setCellValue('C'.$i,$totalA);	
//			$objPHPExcel->getActiveSheet()->setCellValue('D'.$i, $totalD);
//
//
//			$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':D'.$i)->getFont()->setSize(10);
//			$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':D'.$i)->getFont()->setBold(true);
//			$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':D'.$i)->getNumberFormat()
//										->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_ACCOUNTING);
//
//			$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getFont()->setSize(10);
//			$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getFont()->setBold(true);
//			$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':B'.$i);
//			$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':B'.$i)->getBorders()->getBottom()
//						->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
//			
//				$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':D'.$i)->applyFromArray(allBordersThin());
			

			$i++;
		
			$i++;	
			$nivel++;
		}
	//}

	

//	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(30);
//	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(50);
//	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
//	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
//	$objPHPExcel->getActiveSheet()->setSelectedCells('F'.($i+20));
//
//	$objPHPExcel->getActiveSheet()->getRowDimension('8')->setRowHeight(18);

	
	$NOMINA = str_replace(' ', '_', $NOMINA);
	$filename = "resumen_contable_".$NOMINA."_".$frecuencia."_Del_".fecha($desde).'_Hasta_'.fecha($hasta);

	// Redirect output to a client’s web browser (Excel5)
	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename="'.$filename.'.txt"');
	header('Cache-Control: max-age=0');
	// If you're serving to IE 9, then the following may be needed
	header('Cache-Control: max-age=1');

	// If you're serving to IE over SSL, then the following may be needed
	header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
	header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
	header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
	header ('Pragma: public'); // HTTP/1.0

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'CSV');
        $objWriter->setSheetIndex(0);   // Select which sheet.
        $objWriter->setDelimiter("\t"); 
        $objWriter->setEnclosure(''); 
        $objWriter->setLineEnding("\r\n"); 
	$objWriter->save('php://output');
}
exit;