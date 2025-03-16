<?php
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('America/Panama');
include('../lib/common_excel.php');
include('lib/php_excel.php');
$conexion=conexion();
//require_once '../paginas/phpexcel/Classes/PHPExcel.php';
require_once 'phpexcel/Classes/PHPExcel.php';
$objPHPExcel = new PHPExcel();
// if (isset($_GET['valor_id'])) {

    $proyecto = $_POST['nivel1'];
    $fecha_ini = fecha_sql($_POST['fecha_inicio']);
    $fecha_fin = fecha_sql($_POST['fecha_fin']);
    //$id=$_GET['valor_id'];
    if ($proyecto=='0') {
        
        $query="SELECT c.ficha,c.fecha,n.descripcion,p.nombres,p.apellidos,nn.descrip, 
        CASE WHEN c.dia_fiesta='1' THEN 'normal'
               WHEN c.dia_fiesta='2' THEN 'no laboral'
                 WHEN c.dia_fiesta='3' THEN 'nacional'
                 ELSE 'laboral'
        END as tipo_dia
         FROM nomcalendarios_personal AS c 
         INNER JOIN nompersonal as p ON p.ficha=c.ficha 
         INNER JOIN nomturnos AS n ON n.turno_id=p.turno_id 
        INNER JOIN nomnivel1 as nn ON nn.codorg=p.codnivel1  
        WHERE c.fecha BETWEEN '$fecha_ini' and '$fecha_fin'";
    } else {
        
        $query="SELECT c.ficha,c.fecha,c.dia_fiesta,c.turno_id,p.nombres,p.apellidos,nn.descrip FROM 
        nomcalendarios_personal AS c
        INNER JOIN nompersonal as p ON p.ficha=c.ficha
        INNER JOIN nomturnos AS n ON n.turno_id=p.turno_id  
        INNER JOIN nomnivel1 as nn ON nn.codorg=p.codnivel1  
        WHERE c.fecha BETWEEN '$fecha_ini' and '$fecha_fin' and p.codnivel1='$proyecto'";
    }
    
    
    
    $result=$conexion->query($query);

    $objPHPExcel->getProperties()->setCreator("Selectra")
							 ->setLastModifiedBy("Selectra")
                             ->setTitle("Horizontal de Planilla");
                             
    $objPHPExcel->getDefaultStyle()->getFont()->setName('Calibri');    
    $objPHPExcel->getDefaultStyle()->getFont()->setSize(11);   

    $objPHPExcel->getActiveSheet()  
    ->setCellValue('A1', 'ficha')
    ->setCellValue('B1', 'fecha')
    ->setCellValue('C1', 'descripcion turno')
    ->setCellValue('D1', 'turno id')
    ->setCellValue('E1', 'nombres')
    ->setCellValue('F1', 'apellidos')
    ->setCellValue('G1', 'tipo dia');
    // ->setCellValue('H1', 'lt')
    // ->setCellValue('I1', 'domingos')
    // ->setCellValue('J1', 'feriados')
    // ->setCellValue('K1', 'libres')
    // ->setCellValue('L1', 'faltas')
    // ->setCellValue('M1', 'amonestaciones')
    // ->setCellValue('N1', 'h_totales')
    // ->setCellValue('O1', 'h_extras')
    // ->setCellValue('P1', 'h_domingos')
    // ->setCellValue('Q1', 'h_feriados')
    // ->setCellValue('R1', 'f_domingos')
    // ->setCellValue('S1', 'f_feriados')
    // ->setCellValue('T1', 'monto_pagar')
    // ->setCellValue('U1', 'tipo')
    // ->setCellValue('V1', 'numero_tc')
    // ->setCellValue('W1', 'pxc')
    // ->setCellValue('X1', 'valor_hora')
    // ->setCellValue('Y1', 'valor_hora_extra')
    // ->setCellValue('Z1', 'valor_hora_dom')
    // ->setCellValue('AA1', 'valor_hora_fer')
    // ->setCellValue('AB1', 't')
    // ->setCellValue('AC1', 's')
    // ->setCellValue('AD1', 'as24')
    // ->setCellValue('AE1', 'as12d')
    // ->setCellValue('AF1', 'as12m')
    // ->setCellValue('AG1', 'as12n')
    // ->setCellValue('AH1', 'as8d')
    // ->setCellValue('AI1', 'as8m')
    // ->setCellValue('AJ1', 'as8n')
    // ->setCellValue('AK1', 'rd24')
    // ->setCellValue('AL1', 'rd12d')
    // ->setCellValue('AM1', 'rd12n')
    // ->setCellValue('AN1', 'rd12m')
    // ->setCellValue('AO1', 'lt24')
    // ->setCellValue('AP1', 'lt12d')
    // ->setCellValue('AQ1', 'lt12n')
    // ->setCellValue('AR1', 'lt12m')
    // ->setCellValue('AS1', 'rd8d')
    // ->setCellValue('AT1', 'rd8m')
    // ->setCellValue('AU1', 'rd8n')
    // ->setCellValue('AV1', 'lt8d')
    // ->setCellValue('AW1', 'lt8m')
    // ->setCellValue('AX1', 'lt8n')
    // ->setCellValue('AY1', 'fer24')
    // ->setCellValue('AZ1', 'fer12d')
    // ->setCellValue('BA1', 'fer12m')
    // ->setCellValue('BB1', 'fer12n')
    // ->setCellValue('BC1', 'fer8d')
    // ->setCellValue('BD1', 'fer8m')
    // ->setCellValue('BE1', 'fer8n')
    // ->setCellValue('BF1', 'dom24')
    // ->setCellValue('BG1', 'dom12d')
    // ->setCellValue('BH1', 'dom12m')
    // ->setCellValue('BI1', 'dom12n')
    // ->setCellValue('BJ1', 'dom8d')
    // ->setCellValue('BK1', 'dom8m')
    // ->setCellValue('BL1', 'dom8n')
    // ->setCellValue('BM1', 'art119')
    // ->setCellValue('BN1', 'rol2x2')
    // ->setCellValue('BO1', 'rol2x2x2')
    // ->setCellValue('BP1', 'rol3x2')
    // ->setCellValue('BQ1', 'rol4x2')
    // ->setCellValue('BR1', 'rol5x2')
    // ->setCellValue('BS1', 'rol6x1')
    // ->setCellValue('BT1', 'rol_otro')
    // ->setCellValue('BU1', 'dateReg')
    // ->setCellValue('BV1', 'user');  

    $i=2;
    while ($row=$result->fetch_array()) {
        $objPHPExcel->getActiveSheet()->setCellValue('A'.$i, $row['ficha']);
        $objPHPExcel->getActiveSheet()->setCellValue('B'.$i, $row['fecha']);
        $objPHPExcel->getActiveSheet()->setCellValue('C'.$i, $row['descripcion']);
        $objPHPExcel->getActiveSheet()->setCellValue('D'.$i, $row['nombres']);
        $objPHPExcel->getActiveSheet()->setCellValue('E'.$i, $row['apellidos']);
        $objPHPExcel->getActiveSheet()->setCellValue('F'.$i, $row['descrip']);
        $objPHPExcel->getActiveSheet()->setCellValue('G'.$i, $row['tipo_dia']);
        // $objPHPExcel->getActiveSheet()->setCellValue('H'.$i, $row['lt']);
        // $objPHPExcel->getActiveSheet()->setCellValue('I'.$i, $row['domingos']);
        // $objPHPExcel->getActiveSheet()->setCellValue('J'.$i, $row['feriados']);
        // $objPHPExcel->getActiveSheet()->setCellValue('K'.$i, $row['libres']);
        // $objPHPExcel->getActiveSheet()->setCellValue('L'.$i, $row['faltas']);
        // $objPHPExcel->getActiveSheet()->setCellValue('M'.$i, $row['amonestaciones']);
        // $objPHPExcel->getActiveSheet()->setCellValue('N'.$i, $row['h_totales']);
        // $objPHPExcel->getActiveSheet()->setCellValue('O'.$i, $row['h_extras']);
        // $objPHPExcel->getActiveSheet()->setCellValue('P'.$i, $row['h_domingos']);
        // $objPHPExcel->getActiveSheet()->setCellValue('Q'.$i, $row['h_feriados']);
        // $objPHPExcel->getActiveSheet()->setCellValue('R'.$i, $row['f_domingos']);
        // $objPHPExcel->getActiveSheet()->setCellValue('S'.$i, $row['f_feriados']);
        // $objPHPExcel->getActiveSheet()->setCellValue('T'.$i, $row['monto_pagar']);
        // $objPHPExcel->getActiveSheet()->setCellValue('U'.$i, $row['tipo']);
        // $objPHPExcel->getActiveSheet()->setCellValue('V'.$i, $row['numero_tc']);
        // $objPHPExcel->getActiveSheet()->setCellValue('W'.$i, $row['pxc']);
        // $objPHPExcel->getActiveSheet()->setCellValue('X'.$i, $row['valor_hora']);
        // $objPHPExcel->getActiveSheet()->setCellValue('Y'.$i, $row['valor_hora_extra']);
        // $objPHPExcel->getActiveSheet()->setCellValue('Z'.$i, $row['valor_hora_dom']);
        // $objPHPExcel->getActiveSheet()->setCellValue('AA'.$i, $row['valor_hora_fer']);
        // $objPHPExcel->getActiveSheet()->setCellValue('AB'.$i, $row['t']);
        // $objPHPExcel->getActiveSheet()->setCellValue('AC'.$i, $row['s']);
        // $objPHPExcel->getActiveSheet()->setCellValue('AD'.$i, $row['as24']);
        // $objPHPExcel->getActiveSheet()->setCellValue('AE'.$i, $row['as12d']);
        // $objPHPExcel->getActiveSheet()->setCellValue('AF'.$i, $row['as12m']);
        // $objPHPExcel->getActiveSheet()->setCellValue('AG'.$i, $row['as12n']);
        // $objPHPExcel->getActiveSheet()->setCellValue('AH'.$i, $row['as8d']);
        // $objPHPExcel->getActiveSheet()->setCellValue('AI'.$i, $row['as8m']);
        // $objPHPExcel->getActiveSheet()->setCellValue('AJ'.$i, $row['as8n']);
        // $objPHPExcel->getActiveSheet()->setCellValue('AK'.$i, $row['rd24']);
        // $objPHPExcel->getActiveSheet()->setCellValue('AL'.$i, $row['rd12d']);
        // $objPHPExcel->getActiveSheet()->setCellValue('AM'.$i, $row['rd12n']);
        // $objPHPExcel->getActiveSheet()->setCellValue('AN'.$i, $row['rd12m']);
        // $objPHPExcel->getActiveSheet()->setCellValue('AO'.$i, $row['lt24']);
        // $objPHPExcel->getActiveSheet()->setCellValue('AP'.$i, $row['lt12d']);
        // $objPHPExcel->getActiveSheet()->setCellValue('AQ'.$i, $row['lt12n']);
        // $objPHPExcel->getActiveSheet()->setCellValue('AR'.$i, $row['lt12m']);
        // $objPHPExcel->getActiveSheet()->setCellValue('AS'.$i, $row['rd8d']);
        // $objPHPExcel->getActiveSheet()->setCellValue('AT'.$i, $row['rd8m']);
        // $objPHPExcel->getActiveSheet()->setCellValue('AU'.$i, $row['rd8n']);
        // $objPHPExcel->getActiveSheet()->setCellValue('AV'.$i, $row['lt8d']);
        // $objPHPExcel->getActiveSheet()->setCellValue('AW'.$i, $row['lt8m']);
        // $objPHPExcel->getActiveSheet()->setCellValue('AX'.$i, $row['lt8n']);
        // $objPHPExcel->getActiveSheet()->setCellValue('AY'.$i, $row['fer24']);
        // $objPHPExcel->getActiveSheet()->setCellValue('AZ'.$i, $row['fer12d']);
        // $objPHPExcel->getActiveSheet()->setCellValue('BA'.$i, $row['fer12m']);
        // $objPHPExcel->getActiveSheet()->setCellValue('BB'.$i, $row['fer12n']);
        // $objPHPExcel->getActiveSheet()->setCellValue('BC'.$i, $row['fer8d']);
        // $objPHPExcel->getActiveSheet()->setCellValue('BD'.$i, $row['fer8m']);
        // $objPHPExcel->getActiveSheet()->setCellValue('BE'.$i, $row['fer8n']);
        // $objPHPExcel->getActiveSheet()->setCellValue('BF'.$i, $row['dom24']);
        // $objPHPExcel->getActiveSheet()->setCellValue('BG'.$i, $row['dom12d']);
        // $objPHPExcel->getActiveSheet()->setCellValue('BH'.$i, $row['dom12m']);
        // $objPHPExcel->getActiveSheet()->setCellValue('BI'.$i, $row['dom12n']);
        // $objPHPExcel->getActiveSheet()->setCellValue('BJ'.$i, $row['dom8d']);
        // $objPHPExcel->getActiveSheet()->setCellValue('BK'.$i, $row['dom8m']);
        // $objPHPExcel->getActiveSheet()->setCellValue('BL'.$i, $row['dom8n']);
        // $objPHPExcel->getActiveSheet()->setCellValue('BM'.$i, $row['art119']);
        // $objPHPExcel->getActiveSheet()->setCellValue('BN'.$i, $row['rol2x2']);
        // $objPHPExcel->getActiveSheet()->setCellValue('BO'.$i, $row['rol2x2x2']);
        // $objPHPExcel->getActiveSheet()->setCellValue('BP'.$i, $row['rol3x2']);
        // $objPHPExcel->getActiveSheet()->setCellValue('BQ'.$i, $row['rol4x2']);
        // $objPHPExcel->getActiveSheet()->setCellValue('BR'.$i, $row['rol5x2']);
        // $objPHPExcel->getActiveSheet()->setCellValue('BS'.$i, $row['rol6x1']);
        // $objPHPExcel->getActiveSheet()->setCellValue('BT'.$i, $row['rol_otro']);
        // $objPHPExcel->getActiveSheet()->setCellValue('BU'.$i, $row['dateReg']);
        // $objPHPExcel->getActiveSheet()->setCellValue('BV'.$i, $row['user']);                
        $i++;
    }         

    // Redirect output to a client’s web browser (Excel5)
    $filename = "marcaciones";
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

exit;
?>