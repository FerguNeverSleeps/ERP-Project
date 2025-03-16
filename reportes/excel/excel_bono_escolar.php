<?php
include("../../nomina/lib/common.php");
require_once('../../nomina/lib/database.php');
$db = new Database($_SESSION['bd']);
require_once '../../includes/phpexcel/Classes/PHPExcel.php';

$objPHPExcel = new PHPExcel();
$objPHPExcel = PHPExcel_IOFactory::load("plantillas/dia_del_padre_tmp.xlsx");

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
where a.cedula is not null and a.codpar=3 and a.vive=1 and b.estado = 'Activo'
group by b.ficha
order by b.fecing,b.codnivel1 DESC";

$sql = "select * from nompersonal where ficha in (select ficha from nomfamiliares where codpar=3 group by ficha) order by ficha";
$result1 = $db->query($sql, $conexion);
//---------------------------------------------------------------
//titulo de la tabla
//Fin encabezado tabla
$rowCount = 5;
$count=0;
$b1=$b2=$b3=0;

$objPHPExcel->getActiveSheet()->freezePane('A5'); // Inmovilizar Paneles
$i=1;
while($row = fetch_array($result1)){      
    //----------------------------------------
    $objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, $i);
    $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, $row['ficha']);
    $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, utf8_encode($row['apenom']));
    $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, $row['estado']);
    $objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, fecha($row['fecing']));
    $objPHPExcel->getActiveSheet()->getStyle('B'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
    
    $rowCount++;
    
    $sql2 = "select a.*, TIMESTAMPDIFF(YEAR,a.fecha_nac,CURDATE()) AS edad 
    from nomfamiliares a 
    where a.codpar=3 and a.vive=1 and a.ficha = '".$row['ficha']."'
    order by a.fecha_nac";
    $result2 = $db->query($sql2, $conexion);

    while($row2 = fetch_array($result2)){
        //----------------------------------------
        if($row2['sexo']=='Femenino') 
            $sexo = "Hija";
        else 
            $sexo = "hijo";

            if ($row2['edad']<=18) {
            $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, $sexo);
            $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, utf8_encode($row2['nombre']." ".$row2['apellido']));
            $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, "");
            $objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, "");
            # code...
            $objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, $row2['edad']);
            $objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowCount, fecha($row2['fecha_nac']));
            $objPHPExcel->getActiveSheet()->getStyle('B'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        }
        $rowCount++;

    }  

    $rowCount++;
    $count++;
    $i++;

}

$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);

$objPHPExcel->getActiveSheet()->setTitle('COLABORADORES CON HIJOS');
header('Content-Type: application/vnd.openxmlformats-   officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="listado_bono_escolar.xlsx"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
/* Limpiamos el búfer */
ob_end_clean();
$objWriter->save('php://output');

?>
