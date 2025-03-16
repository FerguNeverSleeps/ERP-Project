<?php
include("../../nomina/lib/common.php");
require_once('../../nomina/lib/database.php');
$db = new Database($_SESSION['bd']);
require_once '../../includes/phpexcel/Classes/PHPExcel.php';

$objPHPExcel = new PHPExcel();
$objPHPExcel = PHPExcel_IOFactory::load("plantillas/plantilla4.xlsx");
//------------------------------------------------------------
/*$sqlV = "SELECT a.foto,a.nomposicion_id, b.codorg, b.descrip as departamento, DATE_FORMAT(a.fecing,'%Y-%m-%d') as fecha_permanencia,a.apellidos,a.ficha,
a.nombres,a.apenom,a.cedula,CONCAT (YEAR(CURDATE()),'-',DATE_FORMAT(DATE_ADD(a.fecing, INTERVAL '11' MONTH),'%m-%d')) as dias_vac 
from nompersonal as a
LEFT JOIN dias_incapacidad dias on (a.cedula = dias.cedula)
LEFT JOIN nomnivel1 b ON a.codnivel1=b.codorg
WHERE (MONTH(DATE_ADD(a.fecing, INTERVAL '11' MONTH)) = MONTH(CURDATE()) OR dias.tipo_justificacion = 7)  AND estado <> 'Vacaciones'
GROUP BY a.ficha
ORDER BY dias_vac ASC";*/
$sqlV = "SELECT a.foto,a.nomposicion_id,x.Descripcion, b.codorg, b.descrip as departamento, DATE_FORMAT(a.fecing,'%Y-%m-%d') as fecha_permanencia,a.apellidos,a.ficha,
a.nombres,a.apenom,a.cedula,CONCAT (YEAR(CURDATE()),'-',DATE_FORMAT(DATE_ADD(a.fecing, INTERVAL '11' MONTH),'%m-%d')) as dias_vac 
from nompersonal as a
LEFT JOIN dias_incapacidad dias on (a.cedula = dias.cedula)
LEFT JOIN nomnivel1 b ON a.codnivel1=b.codorg
LEFT JOIN departamento as x ON a.IdDepartamento=x.IdDepartamento
WHERE (MONTH(DATE_ADD(a.fecing, INTERVAL '11' MONTH)) = MONTH(CURDATE())) AND a.estado <> 'Vacaciones' AND a.estado <> 'Egresado'  OR (MONTH(DATE_ADD(a.fecing, INTERVAL '10' MONTH)) = MONTH(CURDATE())) AND estado <> 'Vacaciones' AND  estado <> 'Egresado' 
GROUP BY a.ficha
ORDER BY dias_vac ASC";
$result2 = $db->query($sqlV, $conexion);
$i=0;

//--------------------------------------------------------------
//titulo de la tabla
$objPHPExcel->getActiveSheet()->SetCellValue('B1', "LISTADO DE COLABORADORES CON DERECHO A VACACIONES");
$objPHPExcel->getActiveSheet()->SetCellValue('D1', "Usuario: ".$_SESSION['nombre']);
$objPHPExcel->getActiveSheet()->SetCellValue('F1', "Fecha: ".date('d-m-Y'));

//Encabezado de la tabla
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(25);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
$borders = array(
    'borders' => array(
        'allborders' => array(
          'style' => PHPExcel_Style_Border::BORDER_THIN,
          'color' => array('argb' => 'FF000000'),
    'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
    )
    ),
    'font'  => array(
        'bold'  => true,
        'color' => array('rgb' => 'FFFFFF'),
        'size'  => 12,
        'name'  => 'Verdana'
    )
);
$objPHPExcel->getActiveSheet()->getStyle('B2:G2')->applyFromArray($borders);
$objPHPExcel->getActiveSheet()->SetCellValue('B2', "No.");
$objPHPExcel->getActiveSheet()->SetCellValue('C2', "Nombre Funcionario");
$objPHPExcel->getActiveSheet()->SetCellValue('D2', "Cédula");
$objPHPExcel->getActiveSheet()->SetCellValue('E2', "I. Labores");
$objPHPExcel->getActiveSheet()->SetCellValue('F2', "F. Vacaciones");
//Fin encabezado tabla
$rowCount = 3;
$count=0;

while($row = mysqli_fetch_array($result2))
{
    //----------------------------------------    
    $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, $row['ficha']);
    $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, $row['apenom']);
    $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, $row['cedula']);
    $objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, date('d-m-Y',strtotime($row['fecha_permanencia'])));
    $objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, date('d-m-Y',strtotime($row['dias_vac'])));
    $borders = array(
        'borders' => array(
            'allborders' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('argb' => 'FF000000'),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
            )
        ),
    );
    $desde = "B".$rowCount;
    $hasta = "G".$rowCount;
    $objPHPExcel->getActiveSheet()->getStyle($desde.':'.$hasta)->applyFromArray($borders);
    $rowCount++;
    $count++;
}

$final = $rowCount-1;
/*$objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount,'Total:');
$objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowCount, '=CONTAR(G2:G'.$final.')');*/
$objPHPExcel->getActiveSheet()->setTitle('Lista de Posiciones');
// clean the output buffer
ob_clean();
header('Content-Type: application/vnd.openxmlformats-   officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="lista_colab_derecho_vacacion.xlsx"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
?>
