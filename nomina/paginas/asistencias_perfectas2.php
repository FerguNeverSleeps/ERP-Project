<?php
session_start();
error_reporting(0);
ini_set('display_errors', FALSE);
ini_set('display_startup_errors', FALSE);
date_default_timezone_set('America/Panama');

if (PHP_SAPI == 'cli')
    die('Este ejemplo solo se debe ejecutar desde un navegador Web');

include('../lib/common_excel.php');
include('lib/php_excel.php');

$conexion= new bd($_SESSION['bd']);

echo $proyecto = $_POST['nivel1'];
echo $fecha_ini = fecha_sql($_POST['fecha_inicio']);
echo $fecha_fin = fecha_sql($_POST['fecha_fin']);exit;

require_once 'phpexcel/Classes/PHPExcel.php';

$objPHPExcel = new PHPExcel();

$objPHPExcel->getProperties()->setCreator("Selectra")
                            ->setLastModifiedBy("Selectra")
                            ->setTitle("Asistencias perfectas");

$objPHPExcel->getDefaultStyle()->getFont()->setName('Calibri');
// $objPHPExcel->getActiveSheet()->getStyle('A2:A4')->getFont()->setName('Calibri');
$objPHPExcel->getDefaultStyle()->getFont()->setSize(7);


$sql = "SELECT e.nom_emp as empresa, e.rif, e.tel_emp as telefono, e.dir_emp as direccion, e.ciu_emp as ciudad, 
            e.edo_emp, e.imagen_izq as logo
        FROM   nomempresa e";
$res=$conexion->query($sql);
$fila=$res->fetch_array();
$logo=$fila['logo'];
$empresa=$fila['empresa'];

$sql2 = "SELECT c.ficha,c.fecha,c.dia_fiesta,c.turno_id,p.nombres,p.apellidos FROM 
nomcalendarios_personal AS c
INNER JOIN nompersonal as p ON p.Ficha=c.ficha
INNER JOIN nomturnos AS n ON n.turno_id=p.turno_id
WHERE fecha >='$fecha_ini' AND fecha <= '$fecha_fin'";
$res2=$conexion->query($sql2);




$meses = array('Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre');



$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_LEGAL);

$objPHPExcel->getActiveSheet()
            ->setCellValue('A1', strtoupper($empresa) )
            ->setCellValue('A2', 'ASISTENCIAS PERFECTAS')
            ->setCellValue('C1', 'USUARIO: '.$_SESSION['usuario'])
            ->setCellValue('C2', 'FECHA: '.date('Y-m-d'))
            ->setCellValue('C3', 'PERIODO: '.$fecha_ini.' -- '.$fecha_fin);
            
$objPHPExcel->getActiveSheet()->mergeCells('A1:B1');
$objPHPExcel->getActiveSheet()->mergeCells('A2:B2');
$objPHPExcel->getActiveSheet()->mergeCells('A3:B3');
$objPHPExcel->getActiveSheet()->mergeCells('A4:B4');

$objPHPExcel->getActiveSheet()->mergeCells('C1:F1');
$objPHPExcel->getActiveSheet()->mergeCells('C2:F2');
$objPHPExcel->getActiveSheet()->mergeCells('C3:F3');
$objPHPExcel->getActiveSheet()->getStyle('A1:F4')->getFont()->setSize(12);
//$objPHPExcel->getActiveSheet()->getStyle('A1:E4')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('A1:F4')->getFont()->getColor()->setRGB('ffffff');
$objPHPExcel->getActiveSheet()->getStyle('A1:F4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
cellColor('A1:F4', '1cc5d5');

if($proyecto == 0)
{
    $nombreProyecto = "Todos";
}
else
{
    $sql1= "SELECT * from nomnivel1 WHERE codorg='$proyecto'";
    $res1=$conexion->query($sql1);
    $row1=$res1->fetch_array();
    $nombreProyecto = $row1['descrip'];
}



$objPHPExcel->getActiveSheet()
            ->setCellValue('A5', 'PROYECTO' )
            ->setCellValue('B5', $nombreProyecto);

$objPHPExcel->getActiveSheet()->getStyle('A5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
$objPHPExcel->getActiveSheet()->getStyle('C5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

$objPHPExcel->getActiveSheet()
            ->setCellValue('A6', 'EMPLEADO' )
            ->setCellValue('B6', 'TURNO')
            ->setCellValue('C6', 'HORARIO')
            ->setCellValue('D6', 'FECHA')
            ->setCellValue('E6', 'ENTRADA')
            ->setCellValue('F6', 'SALIDA');
$objPHPExcel->getActiveSheet()->getStyle('A6:F6')->getFont()->getColor()->setRGB('ffffff');
$objPHPExcel->getActiveSheet()->getStyle('A6:F6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
cellColor('A6:F6', '000000');

//$objPHPExcel->getActiveSheet()->getStyle('C2:D5')->applyFromArray(allBordersThin());
//$objPHPExcel->getActiveSheet()->getStyle('C2:D5')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
if($proyecto == 0)
{
    $sql2= "SELECT np.ficha, np.suesal AS salario, 
        CONCAT_WS(' ', np.nombres, np.apellidos) AS nombre,
        SUBSTRING_INDEX(SUBSTRING_INDEX(np.nombres, ' ', 1), ' ', -1) as primer_nombre,
        CASE WHEN np.apellidos LIKE 'De %' THEN 
        SUBSTRING_INDEX(SUBSTRING_INDEX(np.apellidos, ' ', 2), ' ',  2) 
        ELSE      SUBSTRING_INDEX(SUBSTRING_INDEX(np.apellidos, ' ', 1), ' ', -1) 
        END as primer_apellido, np.cedula AS cedula, nc.des_car AS cargo, np.fecing AS fechaingreso, TIMESTAMPDIFF(YEAR,np.fecnac,CURDATE()) AS edad, rjd.entrada AS entrada, rjd.salida AS salida, nt.entrada AS entradaTurno, nt.salida AS salidaTurno, nt.descripcion AS turno, CONCAT(nt.entrada,' - ',nt.salida) AS horario, rjd.fecha AS fecha
        FROM   nompersonal np
        LEFT JOIN nomcargos nc ON (np.codcargo = nc.cod_cargo)
        LEFT JOIN nomcalendarios_personal ncp ON (ncp.ficha = np.ficha AND ncp.fecha BETWEEN '$fecha_ini' AND '$fecha_fin')
        LEFT JOIN nomturnos nt ON (nt.turno_id = ncp.turno_id)
        LEFT JOIN reloj_detalle rjd ON (rjd.ficha = np.ficha)
        WHERE (np.estado = 'REGULAR' OR np.estado = 'Activo') AND TIME_TO_SEC(rjd.entrada)<=TIME_TO_SEC(nt.entrada) and TIME_TO_SEC(rjd.salida)>=TIME_TO_SEC(nt.salida) AND rjd.fecha BETWEEN '$fecha_ini' AND '$fecha_fin'
        ORDER  BY np.ficha";
    

}
else
{
    $sql2= "SELECT np.ficha, np.suesal AS salario, 
        CONCAT_WS(' ', np.nombres, np.apellidos) AS nombre,
        SUBSTRING_INDEX(SUBSTRING_INDEX(np.nombres, ' ', 1), ' ', -1) as primer_nombre,
        CASE WHEN np.apellidos LIKE 'De %' THEN 
        SUBSTRING_INDEX(SUBSTRING_INDEX(np.apellidos, ' ', 2), ' ',  2) 
        ELSE      SUBSTRING_INDEX(SUBSTRING_INDEX(np.apellidos, ' ', 1), ' ', -1) 
        END as primer_apellido, np.cedula AS cedula, nc.des_car AS cargo, np.fecing AS fechaingreso, TIMESTAMPDIFF(YEAR,np.fecnac,CURDATE()) AS edad, rjd.entrada AS entrada, rjd.salida AS salida, nt.entrada AS entradaTurno, nt.salida AS salidaTurno, nt.descripcion AS turno, CONCAT(nt.entrada,' - ',nt.salida) AS horario, rjd.fecha AS fecha
        FROM   nompersonal np
        LEFT JOIN nomcargos nc ON (np.codcargo = nc.cod_cargo)
        LEFT JOIN nomcalendarios_personal ncp ON (ncp.ficha = np.ficha AND ncp.fecha BETWEEN '$fecha_ini' AND '$fecha_fin')
        LEFT JOIN nomturnos nt ON (nt.turno_id = ncp.turno_id)
        LEFT JOIN reloj_detalle rjd ON (rjd.ficha = np.ficha)
        WHERE (np.estado = 'REGULAR' OR np.estado = 'Activo') AND TIME_TO_SEC(rjd.entrada)<=TIME_TO_SEC(nt.entrada) and TIME_TO_SEC(rjd.salida)>=TIME_TO_SEC(nt.salida) AND rjd.fecha BETWEEN '$fecha_ini' AND '$fecha_fin' AND np.codnivel1 = '$proyecto'
        ORDER  BY np.ficha";
}
$res2=$conexion->query($sql2);

$ini=$i=7; $enc=false;
while($row2=$res2->fetch_array())
{	
    $enc=true;
    $ficha = $row2['ficha'];
    $primer_nombre = utf8_encode($row2['primer_nombre']);
    $apellido   = utf8_encode($row2['primer_apellido']);
    $trabajador = utf8_encode($row2['primer_nombre']).' '.utf8_encode($row2['primer_apellido']); 

    //$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':Q'.$i)->getFont()->setName('Calibri');
    $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':Y'.$i)->getFont()->setSize(6);

    $objPHPExcel->getActiveSheet()->setCellValue('A'.$i, $trabajador);
    $objPHPExcel->getActiveSheet()->setCellValue('B'.$i, $row2['turno']);
    $objPHPExcel->getActiveSheet()->setCellValue('C'.$i, $row2['horario']);
    $objPHPExcel->getActiveSheet()->setCellValue('D'.$i, $row2['fecha']);
    $objPHPExcel->getActiveSheet()->setCellValue('E'.$i, $row2['entrada']);
    $objPHPExcel->getActiveSheet()->setCellValue('F'.$i, $row2['salida']);
    
    
    $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':F'.$i)->applyFromArray(allBordersThin());

    $i++;
} 


$i++;	
$nivel++;


$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(60);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);


$objPHPExcel->getActiveSheet()->getRowDimension('8')->setRowHeight(18);

//===========================================================================
$objPHPExcel->setActiveSheetIndex(0); 
$objPHPExcel->getActiveSheet()->setSelectedCells('I30');



$NOMINA = str_replace(' ', '', $NOMINA);
$filename = "asistencias-perfectas";

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
