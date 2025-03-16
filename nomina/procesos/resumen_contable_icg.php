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
				case 3:
			$f = $dia."/".$mes."/".$anio;
			break;
	}
	return $f;
}
//echo $_GET['codtip'];
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

	$objPHPExcel->getProperties()->setCreator("Amaxonia")
								->setLastModifiedBy("Amaxonia")
								->setTitle("Resumen Contable CC");

	$objPHPExcel->getDefaultStyle()->getFont()->setName('Calibri');
	$objPHPExcel->getDefaultStyle()->getFont()->setSize(7);


	$sql = "SELECT e.nom_emp as empresa, e.rif, e.tel_emp as telefono, e.dir_emp as direccion, e.ciu_emp as ciudad, 
				e.edo_emp, e.imagen_izq as logo
			FROM   nomempresa e";
	$res=$conexion->query($sql);
	$fila=$res->fetch_array();
	$logo=$fila['logo'];

	$sql2 = "SELECT np.periodo_ini as desde, np.periodo_fin as hasta, np.fechapago as fechapago, np.mes, np.anio,
					DATE_FORMAT(np.periodo_ini,'%d') as dia_ini, DATE_FORMAT(np.periodo_fin, '%d') as dia_fin   
			FROM nom_nominas_pago np 
			WHERE  np.codnom='".$codnom."' AND np.tipnom='".$codtip."'";
	$res2=$conexion->query($sql2);
	$fila2=$res2->fetch_array();

	$meses = array('Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre');

	$desde=$fila2['desde'];
	$hasta=$fila2['hasta'];
        
    $desde1=formato_fecha($fila2['desde'],1);
	$hasta1=formato_fecha($fila2['hasta'],1);
    $fechapago=formato_fecha($fila2['fechapago'],3);
	$dia_ini = $fila2['dia_ini'];
	$dia_fin = $fila2['dia_fin']; 
	$mes_numero = $fila2['mes'];
	$mes_letras = $meses[$mes_numero - 1];
	$anio = $fila2['anio'];

	$empresa = $fila['empresa'];

	$hoja1 = $objPHPExcel -> getActiveSheet();

	$hoja1->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
	$hoja1->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_LEGAL);


	$sql = "SELECT DISTINCT np.codnivel1, nn.descrip as departamento , nn.ee as cuenta_contablex
	FROM   nompersonal np
	INNER JOIN nomnivel1 nn ON (nn.codorg=np.codnivel1)
	ORDER BY np.codnivel1";
	
	$res1=$conexion->query($sql);
	$i=1; 
	$ini=$i; $enc=false;$cont=0;
	$salario=$neto=0;
		
	//$hoja1->getStyle('A8:V8')->getAlignment()->setShrinkToFit(true);
	$hoja1->getStyle("A$i:Y$i")->getAlignment()->setWrapText(true);
	//$hoja1->getStyle('A2:A4')->getFont()->setName('Calibri');
	
	//$hoja1->getStyle('C2:D5')->applyFromArray(allBordersThin());
	//$hoja1->getStyle('C2:D5')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	$hoja1
		->setCellValue("A$i", 'FECHA')    
		->setCellValue("B$i", 'ASIENTO')    
		->setCellValue("C$i", 'CUENTA CONTABLE')   
		->setCellValue("D$i", 'CONCEPTO')
		->setCellValue("E$i", 'COMENTARIO')    
		->setCellValue("F$i", 'SERIE DOCUMENTO')    
		->setCellValue("G$i", 'NUMERO DOCUMENTO')            
		->setCellValue("H$i", 'DEBE')
		->setCellValue("I$i", 'HABER')
		->setCellValue("J$i", 'CENTRO COSTO');
		$i++;
	while($row1=$res1->fetch_array())
	{	
		$totalA=$totalD=0;
		$asignaciones=$deducciones=0;
		$codnivel1=$row1['codnivel1'];
		$departamento=$row1['departamento'];

		$hoja1->getStyle("A$i:J$i")->getFont()->setName('Calibri');
		$hoja1->getStyle("A$i:J$i")->getFont()->setSize(10);
		$hoja1->getStyle("A$i:J$i")->getFont()->setBold(true);
		$hoja1->getStyle("A$i:J$i")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

		$hoja1->getStyle("A$i:J$i")->applyFromArray(allBordersThin());

//	$hoja1->freezePane('B9');
		$bandera=0;
		$total_salarios=$total_comision=$total_combustible=$total_reembolso=$total_uso_auto=$total_bono="=";	

		$consulta="SELECT SUM(nm.monto) AS suma, nm.codcon, nm.descrip, nm.tipcon, c.ctacon, cc.Descrip as cuenta_contable, nm.codnivel1 as centro_costo 
		FROM nom_movimientos_nomina AS nm 
		LEFT JOIN nomconceptos AS c ON (nm.codcon=c.codcon) 
		LEFT JOIN cwconcue AS cc ON (c.ctacon=cc.Cuenta) 
		WHERE nm.codnom='".$codnom."' AND nm.tipnom='".$codtip."' AND nm.codnivel1='".$codnivel1."' 
		GROUP BY nm.codcon 
		ORDER BY nm.codcon";
		
		$res2=$conexion->query($consulta);
		$ini=$i; $enc=false;$cont=0;
		$salario=$neto=0;
		
		while($row2=$res2->fetch_array())
		{	
			$enc=true;
			$codcon = $row2['codcon'];
			$descrip = utf8_decode($row2['descrip']);
			$tipcon = $row2['tipcon'];
			$ctacon = $row2['ctacon'];
			$cuenta_contable = $row2['cuenta_contable'];
			$centro_costo = $row2['centro_costo'];
			$suma = $row2['suma'];
			if($codcon==100)
			{
				$salario=$suma;
				$bandera=1;
				$pos=$i;
			}
			if($codcon==169 || $codcon==103 || $codcon==177 || $codcon==178 || $codcon==188)
				$salario=$salario+$suma;
			if($codcon==190 || $codcon==198 || $codcon==199)
				$salario=$salario-$suma;


			$hoja1->setCellValueExplicit("A$i", $fechapago, PHPExcel_Cell_DataType::TYPE_STRING);
			$hoja1->setCellValueExplicit("C$i", $ctacon, PHPExcel_Cell_DataType::TYPE_STRING);
			$hoja1->setCellValueExplicit("E$i", $cuenta_contable, PHPExcel_Cell_DataType::TYPE_STRING);
			$hoja1->setCellValueExplicit("D$i", $codcon."-".utf8_encode($descrip), PHPExcel_Cell_DataType::TYPE_STRING);
			$hoja1->setCellValueExplicit("J$i", $centro_costo, PHPExcel_Cell_DataType::TYPE_STRING);
			

			if($tipcon=='A')
			{
				if($codcon!=169 && $codcon!=103 && $codcon!=177 && $codcon!=178 && $codcon!=188)
				{
					$hoja1->setCellValue('H'.$i, $suma);
//                                    $asignaciones=$asignaciones+$suma;
					
				}
				$totalA=$totalA+$suma;
			}
			if($tipcon=='D')
			{
				if($codcon!=190 && $codcon!=198 && $codcon!=199)
				{
						$hoja1->setCellValue('I'.$i, $suma);
//                                     $deducciones=$deducciones+$suma;
						
				}
				$totalD=$totalD+$suma;
					
			}
			if($tipcon=='P')
			{

				$hoja1->setCellValue('H'.$i, $suma);                                
				if($codcon==3000)
				{
					$ctacon=21240201;
				}
				if($codcon==3001)
				{
					$ctacon=21240201;
				}
					
				if($codcon==9005)
				{
					$ctacon=21240401;
				}
				
				if($codcon==9006)
				{
					$ctacon=21240402;
				}
				
				if($codcon==9007)
				{
					$ctacon=21240206;
				}
				
				if($codcon==9008)
				{
					$ctacon=21240205;
				}
				
				if($codcon==9009)
				{
					$ctacon=21240201;
				}
				
				
				$hoja1->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$hoja1->getStyle('B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$hoja1->getStyle('C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				$hoja1->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				$hoja1->getStyle('A'.$i.':J'.$i)->applyFromArray(allBordersThin());
				$hoja1->getStyle('A'.$i.':J'.$i)->getFont()->setSize(8);
				$hoja1->getStyle('H'.$i.':I'.$i)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_ACCOUNTING);

				$i++;                                
				
				$hoja1->setCellValueExplicit("A$i", $fechapago, PHPExcel_Cell_DataType::TYPE_STRING);
				$hoja1->setCellValueExplicit("C$i", $ctacon, PHPExcel_Cell_DataType::TYPE_STRING);
				$hoja1->setCellValueExplicit("E$i", $descrip_cuenta, PHPExcel_Cell_DataType::TYPE_STRING);
				$hoja1->setCellValueExplicit("D$i", utf8_encode($descrip)." Por Pagar", PHPExcel_Cell_DataType::TYPE_STRING);
				$hoja1->setCellValue('I'.$i, $suma);
				$hoja1->setCellValueExplicit("J$i", $centro_costo, PHPExcel_Cell_DataType::TYPE_STRING);

				$totalD=$totalD+$suma;
				$totalA=$totalA+$suma;
//                                    $asignaciones=$asignaciones+$suma;
//                                    $deducciones=$deducciones+$suma;
			}

			$hoja1->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$hoja1->getStyle('B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$hoja1->getStyle('C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			$hoja1->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			$hoja1->getStyle('A'.$i.':J'.$i)->applyFromArray(allBordersThin());
			$hoja1->getStyle('A'.$i.':J'.$i)->getFont()->setSize(8);
			$hoja1->getStyle('H'.$i.':I'.$i)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_ACCOUNTING);


			if($codcon!=190 && $codcon!=198 && $codcon!=199 && $codcon!=169
				&& $codcon!=103 && $codcon!=177 && $codcon!=178 && $codcon!=188)
			{
				$i++;$cont++;
			}
		}
		$neto=$totalA-$totalD;
//                    $totalA=$salario+$asignaciones;
//                    $totalD=$neto+$deducciones;
		if($bandera==1)
		{
			$hoja1->setCellValue('H'.$pos, number_format($salario, 2, '.', ''));
		}
	
		if($enc)
		{       
			$hoja1->setCellValueExplicit("A$i", $fechapago, PHPExcel_Cell_DataType::TYPE_STRING);
			$hoja1->setCellValueExplicit("C$i", "21240101", PHPExcel_Cell_DataType::TYPE_STRING);
			$hoja1->setCellValueExplicit("E$i", "SALARIOS POR PAGAR", PHPExcel_Cell_DataType::TYPE_STRING);
			$hoja1->setCellValueExplicit("D$i", " Total Salarios Por Pagar", PHPExcel_Cell_DataType::TYPE_STRING);
			$hoja1->setCellValue('I'.$i, $neto);   

			$hoja1->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$hoja1->getStyle('B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$hoja1->getStyle('C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			$hoja1->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			$hoja1->getStyle('A'.$i.':J'.$i)->applyFromArray(allBordersThin());
			$hoja1->getStyle('A'.$i.':J'.$i)->getFont()->setSize(8);
			$hoja1->getStyle('H'.$i.':I'.$i)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_ACCOUNTING);
			$hoja1->setCellValueExplicit("J$i", $centro_costo, PHPExcel_Cell_DataType::TYPE_STRING);


			/*$i++;
			
			$hoja1->setCellValue('A'.$i, 'TOTALES' );
			$hoja1->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$hoja1->SetCellValue("H".$i, "=SUM(H$ini:H".($i-1).")");
			$hoja1->SetCellValue("I".$i, "=SUM(I$ini:I".($i-1).")");

			$hoja1->getStyle('H'.$i.':I'.$i)->getFont()->setSize(10);
			$hoja1->getStyle('H'.$i.':I'.$i)->getFont()->setBold(true);
			$hoja1->getStyle('H'.$i.':I'.$i)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_ACCOUNTING);

			$hoja1->getStyle('A'.$i)->getFont()->setSize(10);
			$hoja1->getStyle('A'.$i)->getFont()->setBold(true);
			$hoja1->mergeCells('A'.$i.':G'.$i);
			$hoja1->getStyle('A'.$i.':J'.$i)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);

			$hoja1->getStyle('H'.$i.':I'.$i)->applyFromArray(allBordersThin());


			$i++;*/

			$i++;
		}
		//}
		$hoja1->setSelectedCells('F'.($i+20));

		$hoja1->getRowDimension('8')->setRowHeight(18);
		//$i++;
	}

	$hoja1->getColumnDimension('A')->setWidth(15);
	$hoja1->getColumnDimension('B')->setWidth(20);
	$hoja1->getColumnDimension('C')->setWidth(20);
	$hoja1->getColumnDimension('D')->setWidth(40);
	$hoja1->getColumnDimension('E')->setWidth(40);
	$hoja1->getColumnDimension('F')->setWidth(20);
	$hoja1->getColumnDimension('G')->setWidth(20);
	$hoja1->getColumnDimension('H')->setWidth(20);
	$hoja1->getColumnDimension('I')->setWidth(20);
	$hoja1->getColumnDimension('J')->setWidth(20);
        
	$NOMINA = str_replace(' ', '_', $NOMINA);
	$filename = "icg_x_cc_".$NOMINA."_Del_".fecha($desde).'_Hasta_'.fecha($hasta);

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
    ob_clean();
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->save('php://output');
}
exit;
