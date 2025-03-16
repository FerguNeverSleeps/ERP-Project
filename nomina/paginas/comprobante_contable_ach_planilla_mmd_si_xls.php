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
	        default:
	            break;
	    }
	    return $f;
    }
//echo $_GET['codtip'];
if(isset($_GET['codnom']) && isset($_GET['codtip']))
{
	$codnom=$_GET['codnom'];
	$codtip=$_GET['codtip'];
        
        echo $codnom;
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

	$objPHPExcel->getProperties()->setCreator("AMAXONIA")
								->setLastModifiedBy("AMAXONIA")
								->setTitle("Comprobante Contable Consolidado");

	$objPHPExcel->getDefaultStyle()->getFont()->setName('Calibri');
	// $objPHPExcel->getActiveSheet()->getStyle('A2:A4')->getFont()->setName('Calibri');
	$objPHPExcel->getDefaultStyle()->getFont()->setSize(7);


	$sql = "SELECT e.nom_emp as empresa, e.rif, e.tel_emp as telefono, e.dir_emp as direccion, e.ciu_emp as ciudad, 
				e.edo_emp, e.imagen_izq as logo
			FROM   nomempresa e";
	$res=$conexion->query($sql);
	$fila=$res->fetch_array();
	$logo=$fila['logo'];

	$sql2 = "SELECT np.periodo_ini as desde, np.periodo_fin as hasta, np.frecuencia, np.fechapago as fechapago, np.mes, np.anio,
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
        $fechapago=formato_fecha($fila2['fechapago'],3);
        $dia_ini = $fila2['dia_ini'];
	$frecuencia = $fila2['frecuencia'];
	$dia_fin = $fila2['dia_fin']; 
	$mes_numero = $fila2['mes'];
	$mes_letras = $meses[$mes_numero - 1];
	$anio = $fila2['anio'];

	$empresa = $fila['empresa'];

	$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
	$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_LEGAL);

	$objPHPExcel->getActiveSheet()
				->setCellValue('A2', strtoupper(utf8_encode($empresa) ))
				->setCellValue('A3', strtoupper($NOMINA))
				->setCellValue('A4', 'DEL '. $desde1 .' AL '. $hasta1 )
                                    ->setCellValue('A5', "COMPROBANTE CONTABLE CONSOLIDADO");
        
        $objPHPExcel->getActiveSheet()->getStyle('A2:A5')->getFont()->setSize(10);
            $objPHPExcel->getActiveSheet()->getStyle('A2:A5')->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle('A2:A5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->mergeCells('A2:D2');
            $objPHPExcel->getActiveSheet()->mergeCells('A3:D3');
            $objPHPExcel->getActiveSheet()->mergeCells('A4:D4');
            $objPHPExcel->getActiveSheet()->mergeCells('A5:D5');

//        $sql = "SELECT DISTINCT np.codnivel1, nn.descrip as departamento , nn.ee as cuenta_contablex
//        FROM   nompersonal np
//        INNER JOIN nomnivel1 nn ON (nn.codorg=np.codnivel1)
//        INNER JOIN nom_movimientos_nomina nm ON (nm.ficha=np.ficha)
//        WHERE nm.codnom='".$codnom."' AND nm.tipnom='".$codtip."' 
//        ORDER BY np.codnivel1";
        
       
        $i=7; 
        $ini=$i; 
        
        $objPHPExcel->getActiveSheet()
//            ->setCellValue('A6', 'SALARIOS')
            ->setCellValue("A$i", 'FECHA')    
            ->setCellValue("B$i", 'NUMERO CUENTA')    
            ->setCellValue("C$i", 'NOMBRE CUENTA')    
            ->setCellValue("D$i", 'CONCEPTO')            
            ->setCellValue("E$i", 'DEBITO')
            ->setCellValue("F$i", 'CREDITO');

        $objPHPExcel->getActiveSheet()->getStyle("A$i:F$i")->getFont()->setName('Calibri');
        $objPHPExcel->getActiveSheet()->getStyle("A$i:F$i")->getFont()->setSize(10);
        $objPHPExcel->getActiveSheet()->getStyle("A$i:F$i")->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("A$i:F$i")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $objPHPExcel->getActiveSheet()->getStyle("A$i:F$i")->applyFromArray(allBordersThin());

   
        $consulta="SELECT SUM(nnn.neto) AS neto  "
                . "FROM nom_nomina_netos AS nnn "
                . "WHERE nnn.codnom='".$codnom."' AND nnn.tipnom='".$codtip."' AND nnn.neto>0 ";
    //		echo $consulta;
    //                exit;
        $res2=$conexion->query($consulta);
        $row2=$res2->fetch_array();
        $neto = $row2['neto'];
        $i++;
        $ini=$i; 

        $cuenta_contable_salario="21240101";
        $descripcion_salario="SALARIOS POR PAGAR";
        $concepto_salario="Salarios Por Pagar";
        
        if($frecuencia==16)
        {                       
            $cuenta_contable_salario="21.04.02.07";
            $descripcion_salario="VIATICOS POR PAGAR";
            $concepto_salario="Viaticos Por Pagar";
        }
        
        $cuenta_contable_banco="11.2.01";
        $descripcion_banco="Banco General-Corriente# 2701";
        $concepto_banco="Banco";

        $objPHPExcel->getActiveSheet()->setCellValueExplicit("A$i", $fechapago, PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->getActiveSheet()->setCellValueExplicit("B$i", $cuenta_contable_salario, PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->getActiveSheet()->setCellValueExplicit("C$i", $descripcion_salario, PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->getActiveSheet()->setCellValueExplicit("D$i", $concepto_salario, PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->getActiveSheet()->setCellValue('E'.$i, $neto);   

        $objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $objPHPExcel->getActiveSheet()->getStyle('B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $objPHPExcel->getActiveSheet()->getStyle('C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $objPHPExcel->getActiveSheet()->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':F'.$i)->applyFromArray(allBordersThin());
        $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':F'.$i)->getFont()->setSize(8);
        $objPHPExcel->getActiveSheet()->getStyle('E'.$i.':F'.$i)->getNumberFormat()
                                                                ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_ACCOUNTING);


        $i++;

        $objPHPExcel->getActiveSheet()->setCellValueExplicit("A$i", $fechapago, PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->getActiveSheet()->setCellValueExplicit("B$i", $cuenta_contable_banco, PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->getActiveSheet()->setCellValueExplicit("C$i", $descripcion_banco, PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->getActiveSheet()->setCellValueExplicit("D$i", $concepto_banco, PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->getActiveSheet()->setCellValue('F'.$i, $neto);   

        $objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $objPHPExcel->getActiveSheet()->getStyle('B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $objPHPExcel->getActiveSheet()->getStyle('C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $objPHPExcel->getActiveSheet()->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':F'.$i)->applyFromArray(allBordersThin());
        $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':F'.$i)->getFont()->setSize(8);
        $objPHPExcel->getActiveSheet()->getStyle('E'.$i.':F'.$i)->getNumberFormat()
                                                                ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_ACCOUNTING);
       	
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(40);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(40);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        
	$NOMINA = str_replace(' ', '_', $NOMINA);
	$filename = "comprobante_contable_consolidado_mmdsi_".$NOMINA."_Del_".fecha($desde).'_Hasta_'.fecha($hasta);

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
