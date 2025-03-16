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

	$sql_nomina = "SELECT np.periodo_ini as desde, np.periodo_fin as hasta, np.mes, np.anio, DATE_FORMAT(np.fechapago, '%d-%m-%Y') as fecha_pago, np.codnom as planilla,
					DATE_FORMAT(np.periodo_ini,'%d') as dia_ini, DATE_FORMAT(np.periodo_fin, '%d') as dia_fin, f.descrip as frecuencia  
			FROM nom_nominas_pago np
                        LEFT JOIN nomfrecuencias f ON (np.frecuencia=f.codfre)
                        LEFT JOIN nomtipos_nomina t ON (np.tipnom=t.codtip)
			WHERE  np.codnom=".$codnom." AND np.tipnom=".$codtip;
	$res_nomina=$conexion->query($sql_nomina);
	$fila2=$res_nomina->fetch_array();

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

	$total_salarios=$total_comision=$total_combustible=$total_reembolso=$total_uso_auto=$total_bono="=";	
        $bandera=0;
        $linea='';
		$consulta="SELECT SUM(nm.monto) AS suma, nm.codcon, nm.descrip, nm.tipcon, "
                        . "c.ctacon as cuenta, cw.Descrip as cuenta_contable "
                        . "FROM nom_movimientos_nomina AS nm "
                        . "LEFT JOIN nomconceptos AS c ON (nm.codcon=c.codcon) "
                        . "LEFT JOIN cwconcue AS cw ON (c.ctacon=cw.Cuenta) "
                        . "WHERE nm.codnom='".$codnom."' AND nm.tipnom='".$codtip."' AND  nm.codcon!='523' "
                        . "GROUP BY c.ctacon "
                        . "ORDER BY nm.codcon";
//		echo $consulta;
//                exit;
		$res2=$conexion->query($consulta);
                $i=1; 
		$ini=$i; $enc=false;$cont=0;
                $salario=$neto=0;
		while($row2=$res2->fetch_array())
		{	$enc=true;
			$codcon = $row2['codcon'];
			$descrip = utf8_encode($row2['descrip']);
                        $tipcon = $row2['tipcon'];
                        $ctacon = $row2['ctacon'];
                        $cuenta = $row2['cuenta'];
                        $cuenta_contable = utf8_encode($row2['cuenta_contable']);
                        if($cuenta_contable=='' || $cuenta_contable==' ')
                        {
                            $cuenta_contable=$descrip;
                        }
                        $suma = $row2['suma'];
                        if($codcon==100)
                        {
                            $salario=$suma;
                            $bandera=1;
                        }
                        if($codcon==198 || $codcon==199)
                            $salario=$salario-$suma;                        
                        
                        $linea=$fecha_pago.",".$codigo."#333".$codcon."-".$fecha_pago.",".","."22,".$cuenta.",".$cuenta_contable.",";
                        
//                        $objPHPExcel->getActiveSheet()->setCellValueExplicit("A$i", $fecha_pago.",", PHPExcel_Cell_DataType::TYPE_STRING);
//                        $objPHPExcel->getActiveSheet()->setCellValueExplicit("B$i", $codigo."#333".$codcon."-".$fecha_pago.",", PHPExcel_Cell_DataType::TYPE_STRING);
//                        $objPHPExcel->getActiveSheet()->setCellValueExplicit("C$i", ',', PHPExcel_Cell_DataType::TYPE_STRING);
//                        $objPHPExcel->getActiveSheet()->setCellValueExplicit("D$i", '22,', PHPExcel_Cell_DataType::TYPE_STRING);
//                        $objPHPExcel->getActiveSheet()->setCellValueExplicit("E$i", $cuenta.",", PHPExcel_Cell_DataType::TYPE_STRING);
//                        $objPHPExcel->getActiveSheet()->setCellValueExplicit("F$i", $cuenta_contable.",", PHPExcel_Cell_DataType::TYPE_STRING);
//                        
                        
                        
                        if($tipcon=='A')
                        {

//                                $objPHPExcel->getActiveSheet()->setCellValue('G'.$i, number_format($suma, 2, '.', '').",");
                                $totalA=$totalA+$suma;
                                $linea.=number_format($suma, 2, '.', '').",";
                        }
                        if($tipcon=='D')
                        {
                               
//                                $objPHPExcel->getActiveSheet()->setCellValue('G'.$i, number_format((-1)*$suma, 2, '.', '').",");                            
                                $totalD=$totalD+$suma;
                                $linea.=number_format((-1)*$suma, 2, '.', '').",";
                        }
                        
//                        $objPHPExcel->getActiveSheet()->setCellValueExplicit("H$i", '003,', PHPExcel_Cell_DataType::TYPE_STRING);
//                        
//                        $objPHPExcel->getActiveSheet()->setCellValueExplicit("I$i", 'FALSE,', PHPExcel_Cell_DataType::TYPE_STRING);
//                        
//                        $objPHPExcel->getActiveSheet()->setCellValueExplicit("J$i", '23,', PHPExcel_Cell_DataType::TYPE_STRING);
//                        
//                        $objPHPExcel->getActiveSheet()->setCellValueExplicit("K$i", '8,', PHPExcel_Cell_DataType::TYPE_STRING);
//
//                        $objPHPExcel->getActiveSheet()->setCellValueExplicit("L$i", 'FALSE,', PHPExcel_Cell_DataType::TYPE_STRING);
//                        
//                        $objPHPExcel->getActiveSheet()->setCellValueExplicit("M$i", '0,', PHPExcel_Cell_DataType::TYPE_STRING);
//                        
//                        $objPHPExcel->getActiveSheet()->setCellValueExplicit("N$i", '0,', PHPExcel_Cell_DataType::TYPE_STRING);
                        
                        //$linea.="003,"."FALSE,"."23,"."8,"."FALSE,"."0,","0,";
                        
                        $linea.="003,FALSE,23,8,FALSE,0,0,";
                        
                        if($tipcon=='P')
                        {

//                                $objPHPExcel->getActiveSheet()->setCellValue('D'.$i, number_format($suma, 2, '.', ''));                                
                                if($codcon==3000)
                                    $ctacon=2142000001;
                                if($codcon==3001)
                                    $ctacon=2142000002;
                                if($codcon==9009)
                                    $ctacon=2142000001;
                                

//                                    $objPHPExcel->getActiveSheet()->getStyle('D'.$i.':E'.$i)->getNumberFormat()
//										->setFormatCode('#,##0.00');
                                
                                $i++;  
//                                $objPHPExcel->getActiveSheet()->setCellValueExplicit("A$i", $fecha_pago, PHPExcel_Cell_DataType::TYPE_STRING);
//                                $objPHPExcel->getActiveSheet()->setCellValueExplicit("B$i", $ctacon, PHPExcel_Cell_DataType::TYPE_STRING);
//                                $objPHPExcel->getActiveSheet()->setCellValueExplicit("C$i", utf8_encode($descrip)." Por Pagar-".$codigo."-".$frecuencia, PHPExcel_Cell_DataType::TYPE_STRING);
//                                $objPHPExcel->getActiveSheet()->setCellValue('E'.$i, number_format($suma, 2, '.', ''));
                                
                                $totalD=$totalD+$suma;
                                $totalA=$totalA+$suma;
                            
                        }
			
                        $objPHPExcel->getActiveSheet()->setCellValueExplicit("A$i", $linea, PHPExcel_Cell_DataType::TYPE_STRING);


                        
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
                        

                        $objPHPExcel->getActiveSheet()->getStyle('D'.$i.':E'.$i)->getNumberFormat()
										->setFormatCode('#,##0.00');

                        
                        $i++;
                        $totalD=$totalD+$neto;

			

			$i++;
		
			$i++;	
			$nivel++;
		}
                
        

		$consulta_descuentos="SELECT nm.monto AS suma, nm.codcon, nm.descrip, nm.tipcon, np.apenom, "
                        . "c.ctacon as cuenta, cw.Descrip as cuenta_contable "
                        . "FROM nom_movimientos_nomina AS nm "
                        . "LEFT JOIN nomconceptos AS c ON (nm.codcon=c.codcon) "
                        . "LEFT JOIN nompersonal AS np ON (nm.ficha=np.ficha) "
                        . "LEFT JOIN cwconcue AS cw ON (c.ctacon=cw.Cuenta) "
                        . "WHERE nm.codnom='".$codnom."' AND nm.tipnom='".$codtip."' AND  nm.codcon='523'"
                        . "GROUP BY nm.codcon "
                        . "ORDER BY nm.codcon";
//		echo $consulta;
//                exit;
		$res_descuentos=$conexion->query($consulta_descuentos);
                $linea='';
		while($row_descuentos=$res_descuentos->fetch_array())
		{	$enc=true;
			$codcon = $row2['codcon'];
			$descrip = utf8_encode($row2['apenom']);
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
 
                        $objPHPExcel->getActiveSheet()->getStyle('D'.$i.':E'.$i)->getNumberFormat()
										->setFormatCode('#,##0.00');

                        
                        $i++;
                        $totalD=$totalD+$neto;
			

			$i++;
		
			$i++;	
			$nivel++;
		}
	//}


	
	$NOMINA = str_replace(' ', '_', $NOMINA);
	$filename = "zoho_contable_".$NOMINA."_".$frecuencia."_".fecha($desde).'_'.fecha($hasta);

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

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'CSV');
        $objWriter->setSheetIndex(0);   // Select which sheet.
        $objWriter->setDelimiter("\t"); 
        $objWriter->setEnclosure(''); 
        $objWriter->setLineEnding("\r\n"); 
	$objWriter->save('php://output');
}
exit;