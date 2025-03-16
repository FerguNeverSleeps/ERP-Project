<?php

error_reporting(E_ALL^E_NOTICE);

include("../../nomina/lib/common.php");
require_once('../../nomina/lib/database.php');
$db = new Database($_SESSION['bd']);
require_once '../../includes/phpexcel/Classes/PHPExcel.php';

$objPHPExcel = new PHPExcel();
$objPHPExcel = PHPExcel_IOFactory::load("plantillas/dia_del_madre_tmp.xlsx");

function fecha($value, $separador='/') 
{ 
    if($separador!='/' && $separador!='-')
        $separador='/';
  		
    if ( ! empty($value) )
        return substr($value,8,2) . $separador . substr($value,5,2) . $separador . substr($value,0,4);
    return $value;
}

$sql = "select b.apenom,b.cedula,a.ficha,b.codnivel1,c.descrip,b.fecing,
TIMESTAMPDIFF(MONTH,b.fecing,CURDATE()) AS antiguedad 
from nomfamiliares a 
left join nompersonal b on a.ficha=b.ficha
left join nomnivel1 c on b.codnivel1=c.codorg
where a.cedula is not null and a.codpar in (3) and a.vive=1 and b.estado = 'Activo' and b.sexo='Femenino'
group by b.ficha
order by b.fecing DESC";

//$sql = "select a.* from nompersonal a where ficha in (select ficha from nomfamiliares where codpar in (1,2,3) group by ficha) order by ficha";
$result1 = $db->query($sql, $conexion);
//---------------------------------------------------------------
//titulo de la tabla
//Fin encabezado tabla
$rowCount = 5;
$count=0;
$b1=$b2=$b3=0;

$objPHPExcel->getActiveSheet()->freezePane('A5'); // Inmovilizar Paneles

while($row = fetch_array($result1)){
    if($row['antiguedad'] < 6 && $b1==0){
        $rowCount++;
        $b1=1;
        $objPHPExcel->getActiveSheet()->mergeCells('A'.$rowCount.':C'.$rowCount);
        $objPHPExcel->getActiveSheet()->getStyle('A'.$rowCount.':C'.$rowCount)->getFont()->setSize(14);
        $objPHPExcel->getActiveSheet()->getStyle('A'.$rowCount.':C'.$rowCount)->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, "Antiguedad Menor a 24 Meses");
        
        $objPHPExcel->getActiveSheet()->getStyle('A'.$rowCount.':E'.$rowCount)->getAlignment()
        ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $rowCount=$rowCount+2;

    }
    if($row['antiguedad'] >= 6 && $row['antiguedad'] < 24 && $b2==0){
        $rowCount++;
        $b2=1;
        if($b1==1){
            $objPHPExcel->getActiveSheet()->mergeCells('A'.$rowCount.':C'.$rowCount);
            $objPHPExcel->getActiveSheet()->getStyle('A'.$rowCount.':C'.$rowCount)->getFont()->setSize(14);
            $objPHPExcel->getActiveSheet()->getStyle('A'.$rowCount.':C'.$rowCount)->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, "Total Antiguedad Menor a 6 Meses:");

            $objPHPExcel->getActiveSheet()->mergeCells('D'.$rowCount.':E'.$rowCount);
            $objPHPExcel->getActiveSheet()->getStyle('D'.$rowCount.':E'.$rowCount)->getFont()->setSize(14);
            $objPHPExcel->getActiveSheet()->getStyle('D'.$rowCount.':E'.$rowCount)->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, $count);
        
            $objPHPExcel->getActiveSheet()->getStyle('A'.$rowCount.':E'.$rowCount)->getAlignment()
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        }

        $count=0;
        $rowCount=$rowCount+2;

        $objPHPExcel->getActiveSheet()->mergeCells('A'.$rowCount.':C'.$rowCount);
        $objPHPExcel->getActiveSheet()->getStyle('A'.$rowCount.':C'.$rowCount)->getFont()->setSize(14);
        $objPHPExcel->getActiveSheet()->getStyle('A'.$rowCount.':C'.$rowCount)->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, "Antiguedad Menor a 24 Meses");
        
        $objPHPExcel->getActiveSheet()->getStyle('A'.$rowCount.':E'.$rowCount)->getAlignment()
        ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $rowCount=$rowCount+2;
    }
    
    if($row['antiguedad'] > 24 && $b3==0){
        $rowCount++;
        $b3=1;
        if($b2==1){
            $objPHPExcel->getActiveSheet()->mergeCells('A'.$rowCount.':C'.$rowCount);
            $objPHPExcel->getActiveSheet()->getStyle('A'.$rowCount.':C'.$rowCount)->getFont()->setSize(14);
            $objPHPExcel->getActiveSheet()->getStyle('A'.$rowCount.':C'.$rowCount)->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, "Total Antiguedad Menor a 24 Meses:");
        
            $objPHPExcel->getActiveSheet()->mergeCells('D'.$rowCount.':E'.$rowCount);
            $objPHPExcel->getActiveSheet()->getStyle('D'.$rowCount.':E'.$rowCount)->getFont()->setSize(14);
            $objPHPExcel->getActiveSheet()->getStyle('D'.$rowCount.':E'.$rowCount)->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, $count);
            
            $objPHPExcel->getActiveSheet()->getStyle('A'.$rowCount.':E'.$rowCount)->getAlignment()
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        }

        $count=0;
        $rowCount=$rowCount+2;

        $objPHPExcel->getActiveSheet()->mergeCells('A'.$rowCount.':C'.$rowCount);
        $objPHPExcel->getActiveSheet()->getStyle('A'.$rowCount.':C'.$rowCount)->getFont()->setSize(14);
        $objPHPExcel->getActiveSheet()->getStyle('A'.$rowCount.':C'.$rowCount)->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, "Antiguedad Mayor a 24 Meses");
        
        $objPHPExcel->getActiveSheet()->getStyle('A'.$rowCount.':E'.$rowCount)->getAlignment()
        ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

        $rowCount=$rowCount+2;
    }

    $objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, utf8_encode($row['descrip']));
    $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, $row['ficha']);
    $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, utf8_encode($row['apenom']));
    $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, fecha($row['fecing']));
    $objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, $row['antiguedad']);
        
    $objPHPExcel->getActiveSheet()->getStyle('A'.$rowCount.':E'.$rowCount)->getAlignment()
    ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
    
    $rowCount++;
    $count++;
}

if($b3==1){
    $objPHPExcel->getActiveSheet()->mergeCells('A'.$rowCount.':C'.$rowCount);
    $objPHPExcel->getActiveSheet()->getStyle('A'.$rowCount.':C'.$rowCount)->getFont()->setSize(14);
    $objPHPExcel->getActiveSheet()->getStyle('A'.$rowCount.':C'.$rowCount)->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, "Total Antiguedad Mayor a 24 Meses:");
        
    $objPHPExcel->getActiveSheet()->mergeCells('D'.$rowCount.':E'.$rowCount);
    $objPHPExcel->getActiveSheet()->getStyle('D'.$rowCount.':E'.$rowCount)->getFont()->setSize(14);
    $objPHPExcel->getActiveSheet()->getStyle('D'.$rowCount.':E'.$rowCount)->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, $count);
            
    $objPHPExcel->getActiveSheet()->getStyle('A'.$rowCount.':E'.$rowCount)->getAlignment()
    ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
}

$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);

$objPHPExcel->getActiveSheet()->setTitle('COLABORADORES CON HIJOS-MADRES');
header('Content-Type: application/vnd.openxmlformats-   officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="listado_dia_madres.xlsx"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
/* Limpiamos el búfer */
ob_end_clean();
$objWriter->save('php://output');

exit;
?>

