<?php
session_start();
error_reporting(0);
ini_set('display_errors', FALSE);
ini_set('display_startup_errors', FALSE);
date_default_timezone_set('America/Panama');

if (PHP_SAPI == 'cli')
    die('Este ejemplo solo se debe ejecutar desde un navegador Web');

include('../lib/common.php');
include('lib/php_excel.php');

$conexion= new bd($_SESSION['bd']);

require_once 'phpexcel/Classes/PHPExcel.php';

$objPHPExcel = new PHPExcel();

$objPHPExcel->getProperties()->setCreator("Selectra")
                            ->setLastModifiedBy("Selectra")
                            ->setTitle("Listado de colaboradores");

$objPHPExcel->getDefaultStyle()->getFont()->setName('Calibri');
// $objPHPExcel->getActiveSheet()->getStyle('A2:A4')->getFont()->setName('Calibri');
$objPHPExcel->getDefaultStyle()->getFont()->setSize(12);


$sql = "SELECT e.nom_emp as empresa, e.rif, e.tel_emp as telefono, e.dir_emp as direccion, e.ciu_emp as ciudad, 
            e.edo_emp, e.imagen_izq as logo
        FROM   nomempresa e";
$res=$conexion->query($sql);
$fila=$res->fetch_array();
$logo=$fila['logo'];

$meses = array('Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre');

$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_LEGAL);

$objPHPExcel->getActiveSheet()
            ->setCellValue('A2', strtoupper($empresa) )
            ->setCellValue('A3', 'LISTADO DE COLABORADORES');
$objPHPExcel->getActiveSheet()->mergeCells('A3:G3');

//$objPHPExcel->getActiveSheet()->getStyle('A8:V8')->getAlignment()->setShrinkToFit(true);
$objPHPExcel->getActiveSheet()->getStyle('A8:G8')->getAlignment()->setWrapText(true);
//$objPHPExcel->getActiveSheet()->getStyle('A2:A4')->getFont()->setName('Calibri');
$objPHPExcel->getActiveSheet()->getStyle('A2:A4')->getFont()->setSize(12);
$objPHPExcel->getActiveSheet()->getStyle('A2:A4')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('A2:A4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$objPHPExcel->getActiveSheet()->getStyle('A8:G8')->getFont()->setSize(12);
$objPHPExcel->getActiveSheet()->getStyle('A8:G8')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('A8:G8')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$objPHPExcel->getActiveSheet()->getStyle('A8:G8')->applyFromArray(allBordersThin());

$sql = "SELECT nn.codorg, nn.descrip,
            CASE WHEN nn.codorg=1 THEN 'Administrativos'
                WHEN nn.codorg=2 THEN 'Técnicos'
                WHEN nn.codorg=3 THEN 'Desarrollo Sostenible'
                WHEN nn.codorg=4 THEN 'Comercialización' 
            END as grupo
        FROM   nomnivel1 nn
        WHERE  nn.descrip NOT IN('Eventual', 'O&M') AND codorg in (SELECT np.codnivel1 FROM nompersonal np WHERE (np.estado = 'REGULAR' OR np.estado = 'Activo'))
        ORDER BY nn.codorg";
$res=$conexion->query($sql);
$i=8; $nivel=1;
// $monto_salarios=0;
$total_salarios=$total_comision=$total_combustible=$total_reembolso=$total_uso_auto=$total_bono="=";
$total_extra_salario=$total_extra_gastosrep=$total_vacaciones=$total_cxc="=";
$total_segurosocial_salario=$total_tardanza=$total_seguro_educativo="=";
$total_isr_gastos=$total_isr_salario=$total_ausencia=$total_descuentos=$total_neto="=";
$total_salario=$MesXIII=$Total_Desc_Ley=$SeguroSocialMesXIII=$total_desc_varios="=";
while($row=$res->fetch_array())
{
    if($i>8)
        $i+=3;
    
    $codorg = $row['codorg'];
    $objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':O'.$i);
    $objPHPExcel->getActiveSheet()->setCellValue('A'.$i, $row['descrip']);

    $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':G'.$i)->getFont()->setSize(12);
    $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':G'.$i)->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':O'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':O'.$i)->applyFromArray(allBordersThin());
    
    $i+=2;
    $objPHPExcel->getActiveSheet()
//            ->setCellValue('A6', 'SALARIOS')
        ->setCellValue('A'.$i, 'COD')
        ->setCellValue('B'.$i, 'NOMBRES Y APELLIDOS')
        ->setCellValue('C'.$i, 'CEDULA')
        ->setCellValue('D'.$i, 'CARGO')
        ->setCellValue('E'.$i, 'FECHA INGRESO')
        ->setCellValue('F'.$i, 'EMAIL')
        ->setCellValue('G'.$i, 'SITUACION')
        ->setCellValue('H'.$i, 'EDAD')
        ->setCellValue('I'.$i, 'DIRECCION')
        ->setCellValue('J'.$i, 'TELEFONO')
        ->setCellValue('K'.$i, 'SEXO')
        ->setCellValue('L'.$i, 'ESTADO CIVIL')
        ->setCellValue('M'.$i, 'FORMA PAGO')
        ->setCellValue('N'.$i, 'CUENTA COBRE')
        ->setCellValue('O'.$i, 'SUELDO');


    cellColor('A'.$i.':O'.$i, '92d050');

// $sql2= "SELECT np.ficha, np.suesal as salario, 
//            CONCAT_WS(' ', np.nombres, np.apellidos) as nombre, np.email as email, np.estado as estado,
//            SUBSTRING_INDEX(SUBSTRING_INDEX(np.nombres, ' ', 1), ' ', -1) as primer_nombre,
//            CASE WHEN np.apellidos LIKE 'De %' THEN 
//            SUBSTRING_INDEX(SUBSTRING_INDEX(np.apellidos, ' ', 2), ' ',  2) 
//            ELSE      SUBSTRING_INDEX(SUBSTRING_INDEX(np.apellidos, ' ', 1), ' ', -1) 
//            END as primer_apellido, 
//            np.cedula as cedula, nc.des_car as cargo, np.fecing as fechaingreso, np.inicio_periodo as fechaini, TIMESTAMPDIFF(YEAR,np.fecnac,CURDATE()) AS edad, pry.idProyecto, pry.descripcionLarga AS proyecto,  np.codnivel1
//            FROM   nompersonal np
//            left join nomcargos nc on (np.codcargo = nc.cod_car)
//            left join proyectos pry on (pry.idProyecto = np.proyecto)
//            WHERE (np.estado != 'Egresado')
//            AND np.codnivel1 = '$codorg'
//            ORDER  BY pry.descripcionLarga, np.ficha ASC, np.estado ASC"; 
 
 $sql2= "SELECT np.ficha, np.suesal as salario,np.direccion,np.telefonos,np.sexo,np.estado_civil,np.forcob,np.cuentacob,np.suesal,
            np.nombres, np.nombres2, np.apellidos, np.apellido_materno, np.apellido_casada, np.email as email, np.estado as estado,
            np.cedula as cedula, nc.des_car as cargo, np.fecing as fechaingreso, np.inicio_periodo as fechaini, TIMESTAMPDIFF(YEAR,np.fecnac,CURDATE()) AS edad, pry.idProyecto, pry.descripcionLarga AS proyecto,  np.codnivel1
            FROM   nompersonal np
            left join nomcargos nc on (np.codcargo = nc.cod_car)
            left join proyectos pry on (pry.idProyecto = np.proyecto)
            WHERE (np.estado != 'Egresado')
            AND np.codnivel1 = '$codorg'
            ORDER  BY pry.descripcionLarga, np.ficha ASC, np.estado ASC"; 

    $res2=$conexion->query($sql2);

    $ini=$i++; $enc=false;
    $pryAux = "";
    while($row2=$res2->fetch_array())
    {   
      if(($pryAux != $row2['proyecto']) && ($row2['codnivel1'] == '12'))
        {
            $objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':O'.$i);
            $objPHPExcel->getActiveSheet()->setCellValue('A'.$i,'PROYECTO: '.$row2['proyecto']);
            $pryAux = $row2['proyecto'];
            $i++;
        }  

        $enc=true;
        $ficha = $row2['ficha'];
        $primer_nombre = utf8_encode($row2['nombres']);
        $segundo_nombre   = utf8_encode($row2['nombres2']);
        $primer_apellido = utf8_encode($row2['apellidos']);
        $segundo_apellido   = utf8_encode($row2['apellido_materno']);
        $apellido_casada = utf8_encode($row2['apellido_casada']);
        $trabajador ="";
        
        if($primer_nombre!='' && $primer_nombre!=' ' && $primer_nombre!=NULL)
            $trabajador.=$primer_nombre;
        
        if($segundo_nombre!='' && $segundo_nombre!=' ' && $segundo_nombre!=NULL)
            $trabajador.=" ".$segundo_nombre;
        
        if($primer_apellido!='' && $primer_apellido!=' ' && $primer_apellido!=NULL)
            $trabajador.=" ".$primer_apellido;
        
        if($apellido_casada!='' && $apellido_casada!=' ' && $apellido_casada!=NULL)
            $trabajador.=" De ".$apellido_casada;
        else if($segundo_apellido!='' && $segundo_apellido!=' ' && $segundo_apellido!=NULL)
            $trabajador.=" ".$segundo_apellido;
        
        //$trabajador = $primer_nombre." ".$segundo_nombre." ".$primer_apellido." ".$segundo_apellido; // $row2['nombre']

        $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':Y'.$i)->getFont()->setSize(10);

        $objPHPExcel->getActiveSheet()->setCellValue('A'.$i, $row2['ficha']);
        $objPHPExcel->getActiveSheet()->setCellValue('B'.$i, $trabajador);
        $objPHPExcel->getActiveSheet()->setCellValue('C'.$i, $row2['cedula']);
        $objPHPExcel->getActiveSheet()->setCellValue('D'.$i, $row2['cargo']);
        $objPHPExcel->getActiveSheet()->setCellValue('E'.$i, $row2['fechaingreso']);
        $objPHPExcel->getActiveSheet()->setCellValue('F'.$i, $row2['email']);
        $objPHPExcel->getActiveSheet()->setCellValue('G'.$i, $row2['estado']);
        $objPHPExcel->getActiveSheet()->setCellValue('H'.$i, $row2['edad']);
        $objPHPExcel->getActiveSheet()->setCellValue('I'.$i, utf8_encode($row2['direccion']));
        $objPHPExcel->getActiveSheet()->setCellValue('J'.$i, $row2['telefonos']);
        $objPHPExcel->getActiveSheet()->setCellValue('K'.$i, $row2['sexo']);
        $objPHPExcel->getActiveSheet()->setCellValue('L'.$i, $row2['estado_civil']);
        $objPHPExcel->getActiveSheet()->setCellValue('M'.$i, $row2['forcob']);
        $objPHPExcel->getActiveSheet()->setCellValue('N'.$i, number_format($row2['cuentacob'],2,",","."));        
        $objPHPExcel->getActiveSheet()->setCellValue('O'.$i, $row2['suesal']);
        $objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':O'.$i)->applyFromArray(allBordersThin());   

        $i++;
    } 
    
    $i++;   
    $nivel++;
    
}

$i++;

$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(8);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(45);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(14);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(6);
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(11);
$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(8);
$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(13);

$objPHPExcel->getActiveSheet()->setSelectedCells('G'.($i+20));

$objPHPExcel->getActiveSheet()->getRowDimension('8')->setRowHeight(20);

//===========================================================================
$objPHPExcel->setActiveSheetIndex(0); 
$objPHPExcel->getActiveSheet()->setSelectedCells('I30');



$NOMINA = str_replace(' ', '', $NOMINA);
$filename = "listado-colaboradores";

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
