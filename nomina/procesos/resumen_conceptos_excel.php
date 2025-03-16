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

	$objPHPExcel->getDefaultStyle()->getFont()->setName('Calibri');
	// $objPHPExcel->getActiveSheet()->getStyle('A2:A4')->getFont()->setName('Calibri');
	$objPHPExcel->getDefaultStyle()->getFont()->setSize(7);


	$sql = "SELECT e.nom_emp as empresa, e.rif, e.tel_emp as telefono, e.dir_emp as direccion, e.ciu_emp as ciudad, 
				e.edo_emp, e.imagen_izq as logo
			FROM   nomempresa e";
	$res=$conexion->query($sql);
	$fila=$res->fetch_array();
	$logo=$fila['logo'];

	$sql2 = "SELECT np.periodo_ini as desde, np.periodo_fin as hasta, np.mes, np.anio,
					DATE_FORMAT(np.periodo_ini,'%d') as dia_ini, DATE_FORMAT(np.periodo_fin, '%d') as dia_fin   
			FROM nom_nominas_pago np 
			WHERE  np.codnom=".$codnom." AND np.tipnom=".$codtip;
	$res2=$conexion->query($sql2);
	$fila2=$res2->fetch_array();

	$meses = array('Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre');

	$desde=$fila2['desde'];
	$hasta=$fila2['hasta'];
        
        $desde1=formato_fecha($fila2['desde'],1);
	$hasta1=formato_fecha($fila2['hasta'],1);
	$dia_ini = $fila2['dia_ini'];
	$dia_fin = $fila2['dia_fin']; 
	$mes_numero = $fila2['mes'];
	$mes_letras = $meses[$mes_numero - 1];
	$anio = $fila2['anio'];

	$empresa = $fila['empresa'];

	$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
	$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_LEGAL);

	$objPHPExcel->getActiveSheet()
				->setCellValue('A2', strtoupper($empresa) )
				->setCellValue('A3', strtoupper($NOMINA))
				->setCellValue('A4', 'DEL '. $desde1 .' AL '. $hasta1 )
                                    ->setCellValue('A5', "RESUMEN CONTABLE");


	//$objPHPExcel->getActiveSheet()->getStyle('A8:V8')->getAlignment()->setShrinkToFit(true);
	$objPHPExcel->getActiveSheet()->getStyle('A8:Y8')->getAlignment()->setWrapText(true);
	//$objPHPExcel->getActiveSheet()->getStyle('A2:A4')->getFont()->setName('Calibri');
	$objPHPExcel->getActiveSheet()->getStyle('A2:A5')->getFont()->setSize(7);
	$objPHPExcel->getActiveSheet()->getStyle('A2:A5')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('A2:A5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->mergeCells('A2:D2');
	$objPHPExcel->getActiveSheet()->mergeCells('A3:D3');
	$objPHPExcel->getActiveSheet()->mergeCells('A4:D4');
        $objPHPExcel->getActiveSheet()->mergeCells('A5:D5');
	//$objPHPExcel->getActiveSheet()->getStyle('C2:D5')->applyFromArray(allBordersThin());
	//$objPHPExcel->getActiveSheet()->getStyle('C2:D5')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	$objPHPExcel->getActiveSheet()
//            ->setCellValue('A6', 'SALARIOS')
           
            ->setCellValue('A8', 'CONCEPTO')
            ->setCellValue('B8', 'CUENTA CONTABLE')
            ->setCellValue('C8', 'ASIGNACIONES')
            ->setCellValue('D8', 'DEDUCCIONES')
            ->setCellValue('E8', 'PATRONALES');

	$objPHPExcel->getActiveSheet()->getStyle('A8:E8')->getFont()->setName('Calibri');
	$objPHPExcel->getActiveSheet()->getStyle('A8:E8')->getFont()->setSize(7);
	$objPHPExcel->getActiveSheet()->getStyle('A8:E8')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('A8:E8')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

	$objPHPExcel->getActiveSheet()->getStyle('A8:E8')->applyFromArray(allBordersThin());

	$objPHPExcel->getActiveSheet()->freezePane('B9');
	$total_salarios=$total_comision=$total_combustible=$total_reembolso=$total_uso_auto=$total_bono="=";	

		$consulta="SELECT SUM(nm.monto) AS suma, nm.codcon, nm.descrip, nm.tipcon, c.ctacon "
                        . "FROM nom_movimientos_nomina AS nm "
                        . "LEFT JOIN nomconceptos AS c ON (nm.codcon=c.codcon) "
                        . "WHERE nm.codnom='".$codnom."' AND nm.tipnom='".$codtip."' "
                        . "GROUP BY nm.codcon";
//		echo $consulta;
//                exit;
		$res2=$conexion->query($consulta);
                $i=9; 
		$ini=$i; $enc=false;$cont=0;
		while($row2=$res2->fetch_array())
		{	$enc=true;
			$codcon = $row2['codcon'];
			$descrip = utf8_encode($row2['descrip']);
                        $tipcon = $row2['tipcon'];
                        $ctacon = $row2['ctacon'];
                        $suma = $row2['suma'];
			$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':E'.$i)->getFont()->setSize(8);

			$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, $codcon."-".$descrip);
                        $objPHPExcel->getActiveSheet()->setCellValueExplicit("B$i", $ctacon, PHPExcel_Cell_DataType::TYPE_STRING);
                        if($tipcon=='A')
                        {

                                $objPHPExcel->getActiveSheet()->setCellValue('C'.$i, $suma);
                                $totalA=$totalA+$suma;
                        }
                        if($tipcon=='D')
                        {

                                $objPHPExcel->getActiveSheet()->setCellValue('D'.$i, $suma);
                                $totalD=$totalD+$suma;
                        }
                        if($tipcon=='P')
                        {

                                $objPHPExcel->getActiveSheet()->setCellValue('E'.$i, $suma);
                                $totalP=$totalP+$suma;
                        }
			
			$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':E'.$i)->applyFromArray(allBordersThin());

			$i++;$cont++;
		}

		
		if($enc)
		{
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, 'TOTALES' );
                        $objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->setCellValue('C'.$i,$totalA);	
			$objPHPExcel->getActiveSheet()->setCellValue('D'.$i, $totalD);
			$objPHPExcel->getActiveSheet()->setCellValue('E'.$i, $totalP);


			$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':E'.$i)->getFont()->setSize(8);
			$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':E'.$i)->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':E'.$i)->getNumberFormat()
										->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_ACCOUNTING);

			$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getFont()->setSize(9);
			$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':B'.$i);
			$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':B'.$i)->getBorders()->getBottom()
						->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
			
				$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':E'.$i)->applyFromArray(allBordersThin());
			

			$i++;
		
			$i++;	
			$nivel++;
		}
	//}

	

	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(60);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
	$objPHPExcel->getActiveSheet()->setSelectedCells('F'.($i+20));

	$objPHPExcel->getActiveSheet()->getRowDimension('8')->setRowHeight(18);

	
	$NOMINA = str_replace(' ', '_', $NOMINA);
	$filename = "resumen_contable_".$NOMINA."_Del_".fecha($desde).'_Hasta_'.fecha($hasta);

	// Redirect output to a client’s web browser (Excel5)
	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename="'.$filename.'.xls"');
	header('Cache-Control: max-age=0');
	// If you're serving to IE 9, then the following may be needed
	header('Cache-Control: max-age=1');

	// If you're serving to IE over SSL, then the following may be needed
	header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
	header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
	header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
	header ('Pragma: public'); // HTTP/1.0

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->save('php://output');
}
exit;
