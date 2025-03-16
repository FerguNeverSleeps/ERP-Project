<?php
session_start();
error_reporting(0);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('America/Caracas');
if (PHP_SAPI == 'cli')
	die('Este ejemplo solo se debe ejecutar desde un navegador Web');

include('../../lib/common_excel.php');
$conexion = new bd($_SESSION["bd_nomina"]);
$codnom=$_GET['codnom'];
$codtip=$_GET['codtip'];

function allBordersThin(){
	$style = array('borders'=>array(
			'allborders'=>array('style'=> PHPExcel_Style_Border::BORDER_THIN)
	));
	return $style;
}
function fecha($value) { // fecha de YYYY/MM/DD a DD/MM/YYYY
  if ( ! empty($value) ) return substr($value,8,2) ."/". substr($value,5,2) ."/". substr($value,0,4);
  }

require_once '../phpexcel/Classes/PHPExcel.php';

$objPHPExcel = new PHPExcel();

$objPHPExcel->getProperties()->setCreator("Selectra")
							 ->setLastModifiedBy("Selectra")
							 ->setTitle("Horizontal de Planilla")
							 ->setSubject("Office 2007 XLSX Test Document");

$sql = "SELECT e.nom_emp as empresa, e.rif, e.tel_emp as telefono, e.dir_emp as direccion, e.ciu_emp as ciudad, 
               e.edo_emp, e.imagen_izq as logo
		FROM   nomempresa e";
$res = $conexion->query($sql);
$fila = $res->fetch_assoc();
$logo=$fila['logo'];
$sql2 = "SELECT np.periodo_ini as desde, np.periodo_fin as hasta,  ntn.descrip as descrip_planilla
		 FROM nom_nominas_pago np 
		 INNER JOIN nomtipos_nomina ntn on (np.tipnom = ntn.codtip)
		 WHERE  np.codnom=".$codnom." AND np.tipnom=".$codtip;
$res2 = $conexion->query($sql2);

$fila2=$res2->fetch_assoc();

$objDrawing = new PHPExcel_Worksheet_Drawing();
/*$objDrawing->setName('Logo');
$objDrawing->setDescription('Logo');
$objDrawing->setCoordinates('B2');
$objDrawing->setPath('../imagenes/'.$logo);
//$objDrawing->setResizeProportional(true);
$objDrawing->setHeight(80);
//$objDrawing->setWidth(220);
$objDrawing->setOffsetX(0);
$objDrawing->setOffsetY(0);
$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
*/
//$objDrawing->setHeight(36);
//$objPHPExcel->getActiveSheet()->getHeaderFooter()->addImage($objDrawing, PHPExcel_Worksheet_HeaderFooter::IMAGE_HEADER_LEFT);

$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('E2',  $fila['empresa'])
            ->setCellValue('E3', 'RUC '.$fila['rif'].' Telefonos '.$fila['telefono'])
            ->setCellValue('E4', 'Direccion: '.$fila['direccion'])
            ->setCellValue('E7', 'RESUMEN DE PLANILLA 2')
            ->setCellValue('E8', 'Desde '. fecha($fila2['desde']).' Hasta '.fecha($fila2['hasta']));

$objPHPExcel->getActiveSheet()->getStyle('E7')->getFont()->setName('Arial');
$objPHPExcel->getActiveSheet()->getStyle('E7')->getFont()->setSize(14);
//$objPHPExcel->getActiveSheet()->mergeCells('C2:D5');
$objPHPExcel->getActiveSheet()->mergeCells('E2:P2');
$objPHPExcel->getActiveSheet()->mergeCells('E3:P3');
$objPHPExcel->getActiveSheet()->mergeCells('E4:P4');
$objPHPExcel->getActiveSheet()->mergeCells('E5:P5');
$objPHPExcel->getActiveSheet()->mergeCells('E7:K7');
$objPHPExcel->getActiveSheet()->mergeCells('E8:K8');
$objPHPExcel->getActiveSheet()->mergeCells('E9:K9');
//$objPHPExcel->getActiveSheet()->getStyle('C2:D5')->applyFromArray(allBordersThin());
//$objPHPExcel->getActiveSheet()->getStyle('C2:D5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//$objPHPExcel->getActiveSheet()->getStyle('C2:D5')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('E7:K7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('E8:K8')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('E9:K9')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('E7:K7')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('E2')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('E3')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('E4')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('B10:E10')->getFont()->setBold(true);

$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('E5', $fila2['descrip_planilla'])
            ->setCellValue('B10', 'FICHA')
            ->setCellValue('C10', 'CEDULA')
            ->setCellValue('D10', 'NOMBRE')
            ->setCellValue('E10', 'SUELDO');

$sql="SELECT DISTINCT nm.codcon, nm.descrip as descripcion, nm.tipcon 
	FROM nom_movimientos_nomina nm 
	  WHERE nm.codnom=".$codnom." AND nm.tipnom=".$codtip."  AND ((nm.codcon >=210 AND nm.codcon <=299) OR (nm.codcon>=500 AND nm.codcon<=599))  AND nm.monto > 0
	  ORDER BY  nm.codcon,nm.tipcon";
$res= $conexion->query($sql);
$d=$p=1; $letra='F'; $letra_tb=$letra_td=$letra_tp=$letra_t='';
while($fila=$res->fetch_assoc()){
	$tipcon=$fila[tipcon];
    $descripcion=$fila['descripcion'];

	if($tipcon=='A'){
		  $objPHPExcel->setActiveSheetIndex(0)->setCellValue($letra.'10', trim($descripcion));
          $objPHPExcel->getActiveSheet()->getColumnDimension($letra)->setWidth(17);
		  $objPHPExcel->getActiveSheet()->getStyle($letra.'10')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		  $objPHPExcel->getActiveSheet()->getStyle($letra.'10')->getFont()->setBold(true);
		  $objPHPExcel->getActiveSheet()->mergeCells($letra.'10:'.(++$letra).'10');
          $objPHPExcel->getActiveSheet()->getColumnDimension($letra)->setWidth(17);
		  $letra++;
	}
	if($tipcon=='D'){
		if($d==1){
			$letra_tb=$letra;
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue($letra.'10', 'TOTAL BRUTO');
			$objPHPExcel->getActiveSheet()->getStyle($letra.'10')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle($letra.'10')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getColumnDimension($letra)->setAutoSize(true);
			$letra++;
		}
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue($letra.'10', $descripcion);
        $objPHPExcel->getActiveSheet()->getColumnDimension($letra)->setWidth(17);
		$objPHPExcel->getActiveSheet()->getStyle($letra.'10')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle($letra.'10')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->mergeCells($letra.'10:'.(++$letra).'10');
        $objPHPExcel->getActiveSheet()->getColumnDimension($letra)->setWidth(17);
		$d++; $letra++;
	}
	if($tipcon=='P'){
	   if($p==1){
        	$letra_td=$letra;
        	$objPHPExcel->getActiveSheet()->setCellValue($letra.'10', 'TOTAL A DEDUCIR');
        	$objPHPExcel->getActiveSheet()->getStyle($letra.'10')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        	$objPHPExcel->getActiveSheet()->getStyle($letra.'10')->getFont()->setBold(true);
        	$objPHPExcel->getActiveSheet()->getColumnDimension($letra)->setAutoSize(true);
        	$letra++; $p++;
           
	   }
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue($letra.'10', $descripcion);
        $objPHPExcel->getActiveSheet()->getColumnDimension($letra)->setWidth(17);
		$objPHPExcel->getActiveSheet()->getStyle($letra.'10')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle($letra.'10')->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->mergeCells($letra.'10:'.(++$letra).'10');
        $objPHPExcel->getActiveSheet()->getColumnDimension($letra)->setWidth(17);
		$letra++;
	}
} 
	if($d==1 && $p==1){
				$letra_tb=$letra;
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue($letra.'10', 'TOTAL BRUTO');
				$objPHPExcel->getActiveSheet()->getStyle($letra.'10')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle($letra.'10')->getFont()->setBold(true);
				$objPHPExcel->getActiveSheet()->getColumnDimension($letra)->setAutoSize(true);
				$letra++;

		        $letra_td=$letra;
	        	$objPHPExcel->getActiveSheet()->setCellValue($letra.'10', 'TOTAL A DEDUCIR');
	        	$objPHPExcel->getActiveSheet()->getStyle($letra.'10')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	        	$objPHPExcel->getActiveSheet()->getStyle($letra.'10')->getFont()->setBold(true);
	        	$objPHPExcel->getActiveSheet()->getColumnDimension($letra)->setAutoSize(true);
	        	$letra++; $p++;			
	}
	$letra_tp=$letra;
	$objPHPExcel->getActiveSheet()->setCellValue($letra.'10', 'TOTAL PATRONAL');
	$objPHPExcel->getActiveSheet()->getStyle($letra.'10')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle($letra.'10')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension($letra)->setAutoSize(true);
    $letra++;
    
	$letra_t=$letra;
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue($letra.'10', 'TOTAL');
	$objPHPExcel->getActiveSheet()->getStyle($letra.'10')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle($letra.'10')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension($letra)->setAutoSize(true);

	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);

$sql = "SELECT DISTINCT np.ficha, np.cedula, np.apenom as nombre, np.suesal as sueldo
		FROM   nom_movimientos_nomina nm 
		INNER JOIN nompersonal np ON (nm.ficha=np.ficha)
		WHERE  nm.codnom=".$codnom." AND nm.tipnom=".$codtip." AND nm.codcon = '210' AND nm.monto > 0	
		ORDER BY np.ficha";
$res = $conexion -> query($sql);
$i=11; $total_bruto=0; $total_deducir=0; $total_patronales=0; $total=0;
while($fila=$res->fetch_assoc()){
	$ficha=$fila['ficha'];
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('B'.$i, $fila['ficha']);
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('C'.$i, $fila['cedula']);
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('D'.$i, utf8_encode($fila['nombre']) );
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('E'.$i, $fila['sueldo']);
    $objPHPExcel->getActiveSheet()->getStyle('E'.$i)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

	$sql2="SELECT DISTINCT nm.codcon, nm.descrip, nm.tipcon 
	FROM nom_movimientos_nomina nm 
		  WHERE nm.codnom=".$codnom." AND nm.tipnom=".$codtip." AND ((nm.codcon >=210 AND nm.codcon <=299) OR (nm.codcon>=500 AND nm.codcon<=599))  AND nm.monto > 0
		  ORDER BY nm.tipcon, nm.codcon";
	$res2 = $conexion -> query($sql2);
	$d=$p=1; $letra='F'; $total_asignacion=0; $total_deduccion=0; $total_patronal=0;
	while($fila2=$res2->fetch_assoc()){
		$codcon=$fila2['codcon'];

		$sql3 = "SELECT nm.tipcon, nm.valor as ref, nm.monto, nm.descrip
				 FROM   nom_movimientos_nomina nm
				 WHERE  nm.codnom=".$codnom." AND nm.tipnom=".$codtip."  AND nm.ficha='".$ficha."' AND nm.codcon=".$codcon;
		$res3=$conexion -> query($sql3);

		if($fila3=$res3->fetch_object()){
			if($fila3->tipcon=='A'){
			  $objPHPExcel->setActiveSheetIndex(0)->setCellValue($letra.$i, $fila3->ref);
			  $objPHPExcel->setActiveSheetIndex(0)->setCellValue(++$letra.$i, $fila3->monto);              
              $objPHPExcel->getActiveSheet()->getStyle($letra.$i)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);  
		  	  $letra++;		
		  	  $total_asignacion += $fila3->monto;	
			}
			if($fila3->tipcon=='D'){
				// $objPHPExcel->getActiveSheet()->setCellValue('B10', '=AVERAGE(B2:C4)');
				if($d==1){
					$objPHPExcel->getActiveSheet()->setCellValue($letra.$i, $total_asignacion);
                    $objPHPExcel->getActiveSheet()->getStyle($letra.$i)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
					$letra++; $d++;
				}
			  	$objPHPExcel->setActiveSheetIndex(0)->setCellValue($letra.$i, $fila3->ref);
			  	$objPHPExcel->setActiveSheetIndex(0)->setCellValue(++$letra.$i, $fila3->monto);
                $objPHPExcel->getActiveSheet()->getStyle($letra.$i)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
		  	  	$letra++;
		  	  	$total_deduccion += $fila3->monto;	
			}
			if($fila3->tipcon=='P'){
				if($p==1){
					$objPHPExcel->getActiveSheet()->setCellValue($letra.$i, $total_deduccion);
                    $objPHPExcel->getActiveSheet()->getStyle($letra.$i)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
					$letra++; $p++;
				}
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue($letra.$i, $fila3->ref);
			  	$objPHPExcel->setActiveSheetIndex(0)->setCellValue(++$letra.$i, $fila3->monto);
                $objPHPExcel->getActiveSheet()->getStyle($letra.$i)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
		  	  	$letra++;
                $total_patronal += $fila3->monto;
		  	  	//$total_deduccion += $fila3->monto;
			}
		}else{
			if($fila2[tipcon] == 'A' || $fila2[tipcon] == 'P'){
                if($p==1 && $fila2[tipcon] == 'P'){
			        $objPHPExcel->getActiveSheet()->setCellValue($letra.$i, $total_deduccion);
                    $objPHPExcel->getActiveSheet()->getStyle($letra.$i)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
					$letra++; $p++;
                }
			    $objPHPExcel->setActiveSheetIndex(0)->setCellValue($letra.$i, 0);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue(++$letra.$i, 0);
				$letra++; //$letra++;
			}
			if($fila2[tipcon]=='D'){
				if($d==1){
					$objPHPExcel->getActiveSheet()->setCellValue($letra.$i, $total_asignacion);
                    $objPHPExcel->getActiveSheet()->getStyle($letra.$i)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
					$letra++; $d++;
				}
  			  	$objPHPExcel->setActiveSheetIndex(0)->setCellValue($letra.$i, 0);
			  	$objPHPExcel->setActiveSheetIndex(0)->setCellValue(++$letra.$i, 0);
				$letra++; //$letra++;
			}
		}
	}

	$total_bruto += $total_asignacion;
	$total_deducir += $total_deduccion;
    $total_patronales += $total_patronal;
	$total += ($total_asignacion - $total_deduccion);

	if($d==1 && $p==1){
		$objPHPExcel->getActiveSheet()->setCellValue($letra.$i, $total_asignacion);
        $objPHPExcel->getActiveSheet()->getStyle($letra.$i)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
		$letra++; $d++;

		$objPHPExcel->getActiveSheet()->setCellValue($letra.$i, $total_deduccion);
        $objPHPExcel->getActiveSheet()->getStyle($letra.$i)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
		$letra++; $p++;
	}

	$objPHPExcel->getActiveSheet()->setCellValue($letra.$i, $total_patronal);
    $objPHPExcel->getActiveSheet()->getStyle($letra.$i)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$letra++;
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue($letra.$i, ($total_asignacion - $total_deduccion));
    $objPHPExcel->getActiveSheet()->getStyle($letra.$i)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

	$i++;
}


$objPHPExcel->getActiveSheet()->setCellValue($letra_tb.$i, $total_bruto);
//$objPHPExcel->getActiveSheet()->setCellValue('W'.$letra_td.$i, $total_deducir);
$objPHPExcel->getActiveSheet()->setCellValue($letra_tp.$i, $total_patronales);
$objPHPExcel->getActiveSheet()->setCellValue($letra_t.$i, $total);
$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':'.$letra_t.$i)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle($letra_tb.$i)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
$objPHPExcel->getActiveSheet()->getStyle($letra_td.$i)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
$objPHPExcel->getActiveSheet()->getStyle($letra_tp.$i)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
$objPHPExcel->getActiveSheet()->getStyle($letra_t.$i)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
$objPHPExcel->getActiveSheet()->getStyle('B10:'.$letra_t.$i)->applyFromArray(allBordersThin());

$i=$i+7;

$objPHPExcel->getActiveSheet()->mergeCells('E'.$i.':G'.($i+4));
$objPHPExcel->getActiveSheet()->getStyle('E'.$i.':G'.($i+4))->applyFromArray(allBordersThin());
$objPHPExcel->getActiveSheet()->mergeCells('H'.$i.':J'.($i+4));
$objPHPExcel->getActiveSheet()->getStyle('H'.$i.':J'.($i+4))->applyFromArray(allBordersThin());
$objPHPExcel->getActiveSheet()->mergeCells('K'.$i.':M'.($i+4));
$objPHPExcel->getActiveSheet()->getStyle('K'.$i.':M'.($i+4))->applyFromArray(allBordersThin());
$i=$i+5;
$objPHPExcel->getActiveSheet()->mergeCells('E'.$i.':G'.$i);
$objPHPExcel->getActiveSheet()->getStyle('E'.$i.':G'.$i)->applyFromArray(allBordersThin());
$objPHPExcel->getActiveSheet()->setCellValue('E'.$i, 'RECIBIDO POR');
$objPHPExcel->getActiveSheet()->getStyle('E'.$i)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->mergeCells('H'.$i.':J'.$i);
$objPHPExcel->getActiveSheet()->getStyle('H'.$i.':J'.$i)->applyFromArray(allBordersThin());
$objPHPExcel->getActiveSheet()->setCellValue('H'.$i, 'AUTORIZADO POR');
$objPHPExcel->getActiveSheet()->getStyle('H'.$i)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('H'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->mergeCells('K'.$i.':M'.$i);
$objPHPExcel->getActiveSheet()->getStyle('K'.$i.':M'.$i)->applyFromArray(allBordersThin());
$objPHPExcel->getActiveSheet()->setCellValue('K'.$i, 'PREPARADO POR');
$objPHPExcel->getActiveSheet()->getStyle('K'.$i)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('K'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);


$objPHPExcel->getActiveSheet()->setTitle('Reporte');
$objPHPExcel->getActiveSheet()->setSelectedCells('B100');

// Redirect output to a client’s web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="Reporte_PLANILLA2.xls"');
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
exit;






