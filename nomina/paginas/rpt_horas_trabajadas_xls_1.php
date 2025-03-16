<?php
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('America/Panama');

if (PHP_SAPI == 'cli')
	die('Este ejemplo solo se debe ejecutar desde un navegador Web');

include('../lib/common_excel.php');
include('lib/php_excel.php');
include("funciones_nomina.php");
$conexion = conexion();

$codtip   = $_GET['codtip'];
$id       = $_GET['reg'];

require_once 'phpexcel/Classes/PHPExcel.php';

$objPHPExcel = new PHPExcel();

$objPHPExcel->getProperties()->setCreator("Selectra")
							 ->setLastModifiedBy("Selectra")
							 ->setTitle("Fondo de Cesantia")
							 ->setSubject("Office 2007 XLSX Test Document");

$sql = "SELECT e.nom_emp as empresa, e.rif, e.tel_emp as telefono, e.dir_emp as direccion, e.ciu_emp as ciudad, 
               e.edo_emp, e.imagen_izq as logo
		FROM   nomempresa e";

$res       = query($sql, $conexion);

$fila      = fetch_array($res);
$logo      = $fila['logo'];

$sql2      = "SELECT fecha_ini,fecha_fin FROM reloj_encabezado WHERE cod_enca=".$id;

$res2      = query($sql2, $conexion);

$fila2     = fetch_array($res2);

$fecha_ini = $fila2['fecha_ini'];
$fecha_fin = $fila2['fecha_fin'];

//$sql2 = "SELECT np.periodo_ini as desde, np.periodo_fin as hasta  FROM nom_nominas_pago np 
//         WHERE  np.codnom=".$codnom." AND np.tipnom=".$codtip;
//$res2=query($sql2, $conexion);
//$fila2=fetch_array($res2);

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

$objPHPExcel->setActiveSheetIndex(0); 

$objPHPExcel->getActiveSheet()->setTitle('Anexo 03');
$objPHPExcel->getActiveSheet()
            ->setCellValue('B2',  $fila['empresa'])
            ->setCellValue('B3', 'RIF '.$fila['rif'].' Telefonos '.$fila['telefono'])
            ->setCellValue('B4', 'Direccion: '.$fila['direccion'])
            ->setCellValue('B6', 'REPORTE DE HORAS TRABAJADAS (ORDINARIAS Y EXTRAS)')
            ->setCellValue('B7', 'FECHA INICIO : '.$fecha_ini.' -  FECHA FIN:'.$fecha_fin)
             ->setCellValue('B8', 'Fecha: '.date('d/m/Y'));    

$objPHPExcel->getActiveSheet()->getStyle('B6')->getFont()->setName('Arial');
$objPHPExcel->getActiveSheet()->getStyle('B6')->getFont()->setSize(14);
//$objPHPExcel->getActiveSheet()->mergeCells('C2:D5');
$objPHPExcel->getActiveSheet()->mergeCells('B2:E2');
$objPHPExcel->getActiveSheet()->mergeCells('B3:E3');
$objPHPExcel->getActiveSheet()->mergeCells('B4:E4');
$objPHPExcel->getActiveSheet()->mergeCells('B6:I6');
$objPHPExcel->getActiveSheet()->mergeCells('B7:E7');
$objPHPExcel->getActiveSheet()->mergeCells('B8:E8');
//$objPHPExcel->getActiveSheet()->mergeCells('F10:G10');
//$objPHPExcel->getActiveSheet()->mergeCells('F11:G11');
//$objPHPExcel->getActiveSheet()->getStyle('C2:D5')->applyFromArray(allBordersThin());
//$objPHPExcel->getActiveSheet()->getStyle('C2:D5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//$objPHPExcel->getActiveSheet()->getStyle('C2:D5')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('B6:E6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('B7:E7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('B8:E8')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('B6:E6')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('B2')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('B3')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('B4')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('A10:P10')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('A10:P10')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(7);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(25);
$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(20);
$objPHPExcel->getActiveSheet()       
            ->setCellValue('A10', 'FICHA')
            ->setCellValue('B10', 'CEDULA')
            ->setCellValue('C10', 'NOMBRE Y APELLIDO')
            ->setCellValue('D10', 'HORAS ORDINARIAS')
            ->setCellValue('E10', 'EXT. DIURNA')
            ->setcellvalue('F10', 'EXT. DIA CON REC.')
            ->setcellvalue('G10', 'EXT. NOCT.')
            ->setcellvalue('H10', 'EXT. NOCT. CON REC.')
            ->setcellvalue('I10', 'EXT. MIX DIA')
            ->setcellvalue('J10', 'EXT. MIX DIA CON REC.')
            ->setcellvalue('K10', 'EXT. MIX NOCT.')
            ->setcellvalue('L10', 'EXT. MIX NOCT. CON REC.')
            ->setcellvalue('M10', 'DESCANSO')
            ->setcellvalue('N10', 'EMERGENCIA')
            ->setCellValue('O10', 'DESCANSO INCOMPLETO')
            ->setCellValue('P10', 'HORAS TOTALES');
cellColor('A10:P10', 'FFFF00');
$objPHPExcel->getActiveSheet()->getStyle('A10:P10')->applyFromArray(allBordersThin());
$sql = "SELECT rd.ficha, np.cedula, np.apenom, SUM( ordinaria + domingo + nacional ) AS horas , 
sum(extra+extraext+extranoc+extraextnoc+extranac+extranocnac+mixtodiurna+mixtonoc+mixtoextdiurna+mixtoextnoc+dialibre+emergencia+descansoincompleto) as HorasExtras,
if(extra          != 0,sum(extra),0) ext,
if(extraext       != 0,sum(extraext),0) extraext,
if(extranoc       != 0,sum(extranoc),0) extranoc,
if(extraextnoc    != 0,sum(extraextnoc),0) extraextnoc,
if(extranac       != 0,sum(extranac),0) extranac,
if(extranocnac    != 0,sum(extranocnac),0) extranocnac,
if(mixtodiurna    != 0,sum(mixtodiurna),0) mixtodiurna,
if(mixtonoc       != 0,sum(mixtonoc),0) mixtonoc,
if(mixtoextdiurna != 0,sum(mixtoextdiurna),0) mixtoextdiurna,
if(mixtoextnoc    != 0,sum(mixtoextnoc),0) mixtoextnoc,
if(dialibre       != 0,sum(dialibre),0) dialibre,
if(emergencia     != 0,sum(emergencia),0) emergencia,
if(descansoincompleto     != 0,sum(descansoincompleto),0) descansoincompleto
FROM reloj_detalle as rd
INNER JOIN nompersonal as np on (rd.ficha=np.ficha)
WHERE  rd.id_encabezado ='{$id}'
GROUP BY ficha";
$i         = $inicio = 11;

$res       =query($sql, $conexion);

$num_filas = num_rows($res);

$contador  =0;
while($fila=fetch_array($res))
{
    $ficha                    = $fila['ficha'];
    $cedula                   = $fila['cedula'];
    $nombre                   = utf8_encode($fila['apenom']);
    $horas_ordinarias         = $fila['horas'];
    $horas_extras             = $fila['HorasExtras'];
    $horas_extra              = $fila['horas'];
    $horas_ext                = $fila['ext'];
    $horas_extraext           = $fila['extraext'];
    $horas_extranoc           = $fila['extranoc'];
    $horas_extraextnoc        = $fila['extraextnoc'];
    $horas_mixtodiurna        = $fila['mixtodiurna'];
    $horas_mixtoextdiurna     = $fila['mixtoextdiurna'];
    $horas_mixtonoc           = $fila['mixtonoc'];
    $horas_mixtoextnoc        = $fila['mixtoextnoc'];
    $horas_extranocnac        = $fila['extranocnac'];
    $horas_dialibre           = $fila['dialibre'];
    $horas_emergencia         = $fila['emergencia'];
    $horas_descansoincompleto = $fila['descansoincompleto'];
    $horas_totales            = $horas_ordinarias+$horas_extras;
    if($contador<$num_filas-1)
    {    
        $objPHPExcel->getActiveSheet()->setCellValue('A'.$i, $ficha);
        $objPHPExcel->getActiveSheet()->setCellValue('B'.$i, $cedula);
        $objPHPExcel->getActiveSheet()->setCellValue('C'.$i, $nombre);
        $objPHPExcel->getActiveSheet()->setCellValue('D'.$i, $horas_ordinarias);
        $objPHPExcel->getActiveSheet()->setCellValue('E'.$i, $horas_ext);
        $objPHPExcel->getActiveSheet()->setCellValue('F'.$i, $horas_extraext);
        $objPHPExcel->getActiveSheet()->setCellValue('G'.$i, $horas_extranoc);
        $objPHPExcel->getActiveSheet()->setCellValue('H'.$i, $horas_extraextnoc);
        $objPHPExcel->getActiveSheet()->setCellValue('I'.$i, $horas_mixtodiurna);
        $objPHPExcel->getActiveSheet()->setCellValue('J'.$i, $horas_mixtoextdiurna);
        $objPHPExcel->getActiveSheet()->setCellValue('K'.$i, $horas_mixtonoc);
        $objPHPExcel->getActiveSheet()->setCellValue('L'.$i, $horas_mixtoextnoc);
        $objPHPExcel->getActiveSheet()->setCellValue('M'.$i, $horas_dialibre);
        $objPHPExcel->getActiveSheet()->setCellValue('N'.$i, $horas_emergencia);
        $objPHPExcel->getActiveSheet()->setCellValue('O'.$i, $horas_descansoincompleto);
        $objPHPExcel->getActiveSheet()->setCellValue('P'.$i, $horas_totales);
        $fin=$i;
    }
    else
    {
        $objPHPExcel->getActiveSheet()->setCellValue('A'.$i, '');
        $objPHPExcel->getActiveSheet()->setCellValue('B'.$i, '');
        $objPHPExcel->getActiveSheet()->setCellValue('C'.$i, 'TOTALES');
        $objPHPExcel->getActiveSheet()->setCellValue('D'.$i, '=SUM(D11:D'.($i-1).')');
        $objPHPExcel->getActiveSheet()->setCellValue('E'.$i, '=SUM(E11:E'.($i-1).')');
        $objPHPExcel->getActiveSheet()->setCellValue('F'.$i, '=SUM(F11:F'.($i-1).')');
        $objPHPExcel->getActiveSheet()->setCellValue('G'.$i, '=SUM(G11:G'.($i-1).')');
        $objPHPExcel->getActiveSheet()->setCellValue('H'.$i, '=SUM(H11:H'.($i-1).')');
        $objPHPExcel->getActiveSheet()->setCellValue('I'.$i, '=SUM(I11:I'.($i-1).')');
        $objPHPExcel->getActiveSheet()->setCellValue('J'.$i, '=SUM(J11:J'.($i-1).')');
        $objPHPExcel->getActiveSheet()->setCellValue('K'.$i, '=SUM(K11:K'.($i-1).')');
        $objPHPExcel->getActiveSheet()->setCellValue('L'.$i, '=SUM(L11:L'.($i-1).')');
        $objPHPExcel->getActiveSheet()->setCellValue('M'.$i, '=SUM(M11:M'.($i-1).')');
        $objPHPExcel->getActiveSheet()->setCellValue('N'.$i, '=SUM(N11:N'.($i-1).')');
        $objPHPExcel->getActiveSheet()->setCellValue('O'.$i, '=SUM(O11:O'.($i-1).')');
        $objPHPExcel->getActiveSheet()->setCellValue('P'.$i, '=SUM(P11:P'.($i-1).')');
   }
    
    $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':P'.$i)->applyFromArray(allBordersThin());
        //$objPHPExcel->getActiveSheet()->getStyle('E'.$i)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
    $i++;
    $contador++;
}
//$objPHPExcel->getActiveSheet()->setCellValue('C'.$i, "TOTALES");
$i=$i+3;

$objPHPExcel->getActiveSheet()->mergeCells('B'.$i.':B'.($i+4));
$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':B'.($i+4))->applyFromArray(allBordersThin());
$objPHPExcel->getActiveSheet()->mergeCells('C'.$i.':C'.($i+4));
$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':C'.($i+4))->applyFromArray(allBordersThin());
$objPHPExcel->getActiveSheet()->mergeCells('D'.$i.':D'.($i+4));
$objPHPExcel->getActiveSheet()->getStyle('D'.$i.':D'.($i+4))->applyFromArray(allBordersThin());
$i=$i+5;

$objPHPExcel->getActiveSheet()->getStyle('B'.$i)->applyFromArray(allBordersThin());
$objPHPExcel->getActiveSheet()->setCellValue('B'.$i, 'RECIBIDO POR');
$objPHPExcel->getActiveSheet()->getStyle('B'.$i)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$objPHPExcel->getActiveSheet()->getStyle('C'.$i)->applyFromArray(allBordersThin());
$objPHPExcel->getActiveSheet()->setCellValue('C'.$i, 'AUTORIZADO POR');
$objPHPExcel->getActiveSheet()->getStyle('C'.$i)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$objPHPExcel->getActiveSheet()->getStyle('D'.$i)->applyFromArray(allBordersThin());
$objPHPExcel->getActiveSheet()->setCellValue('D'.$i, 'PREPARADO POR');
$objPHPExcel->getActiveSheet()->getStyle('D'.$i)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

//$objPHPExcel->getActiveSheet()->setSelectedCells('B100');


$objPHPExcel->setActiveSheetIndex(0);

// Redirect output to a client’s web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="Reporte_Horas_Trabajadas.xls"');
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




=======
<?php
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('America/Panama');

if (PHP_SAPI == 'cli')
	die('Este ejemplo solo se debe ejecutar desde un navegador Web');

include('../lib/common_excel.php');
include('lib/php_excel.php');
include("funciones_nomina.php");
$conexion = conexion();

$codtip   = $_GET['codtip'];
$id       = $_GET['reg'];

require_once 'phpexcel/Classes/PHPExcel.php';

$objPHPExcel = new PHPExcel();

$objPHPExcel->getProperties()->setCreator("Selectra")
							 ->setLastModifiedBy("Selectra")
							 ->setTitle("Fondo de Cesantia")
							 ->setSubject("Office 2007 XLSX Test Document");

$sql = "SELECT e.nom_emp as empresa, e.rif, e.tel_emp as telefono, e.dir_emp as direccion, e.ciu_emp as ciudad, 
               e.edo_emp, e.imagen_izq as logo
		FROM   nomempresa e";

$res       = query($sql, $conexion);

$fila      = fetch_array($res);
$logo      = $fila['logo'];

$sql2      = "SELECT fecha_ini,fecha_fin FROM reloj_encabezado WHERE cod_enca=".$id;

$res2      = query($sql2, $conexion);

$fila2     = fetch_array($res2);

$fecha_ini = $fila2['fecha_ini'];
$fecha_fin = $fila2['fecha_fin'];

//$sql2 = "SELECT np.periodo_ini as desde, np.periodo_fin as hasta  FROM nom_nominas_pago np 
//         WHERE  np.codnom=".$codnom." AND np.tipnom=".$codtip;
//$res2=query($sql2, $conexion);
//$fila2=fetch_array($res2);

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

$objPHPExcel->setActiveSheetIndex(0); 

$objPHPExcel->getActiveSheet()->setTitle('Anexo 03');
$objPHPExcel->getActiveSheet()
            ->setCellValue('B2',  $fila['empresa'])
            ->setCellValue('B3', 'RIF '.$fila['rif'].' Telefonos '.$fila['telefono'])
            ->setCellValue('B4', 'Direccion: '.$fila['direccion'])
            ->setCellValue('B6', 'REPORTE DE HORAS TRABAJADAS (ORDINARIAS Y EXTRAS)')
            ->setCellValue('B7', 'FECHA INICIO : '.$fecha_ini.' -  FECHA FIN:'.$fecha_fin)
             ->setCellValue('B8', 'Fecha: '.date('d/m/Y'));    

$objPHPExcel->getActiveSheet()->getStyle('B6')->getFont()->setName('Arial');
$objPHPExcel->getActiveSheet()->getStyle('B6')->getFont()->setSize(14);
//$objPHPExcel->getActiveSheet()->mergeCells('C2:D5');
$objPHPExcel->getActiveSheet()->mergeCells('B2:E2');
$objPHPExcel->getActiveSheet()->mergeCells('B3:E3');
$objPHPExcel->getActiveSheet()->mergeCells('B4:E4');
$objPHPExcel->getActiveSheet()->mergeCells('B6:I6');
$objPHPExcel->getActiveSheet()->mergeCells('B7:E7');
$objPHPExcel->getActiveSheet()->mergeCells('B8:E8');
//$objPHPExcel->getActiveSheet()->mergeCells('F10:G10');
//$objPHPExcel->getActiveSheet()->mergeCells('F11:G11');
//$objPHPExcel->getActiveSheet()->getStyle('C2:D5')->applyFromArray(allBordersThin());
//$objPHPExcel->getActiveSheet()->getStyle('C2:D5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//$objPHPExcel->getActiveSheet()->getStyle('C2:D5')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('B6:E6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('B7:E7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('B8:E8')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('B6:E6')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('B2')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('B3')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('B4')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('A10:P10')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('A10:P10')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(7);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(25);
$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(20);
$objPHPExcel->getActiveSheet()       
            ->setCellValue('A10', 'FICHA')
            ->setCellValue('B10', 'CEDULA')
            ->setCellValue('C10', 'NOMBRE Y APELLIDO')
            ->setCellValue('D10', 'HORAS ORDINARIAS')
            ->setCellValue('E10', 'EXT. DIURNA')
            ->setcellvalue('F10', 'EXT. DIA CON REC.')
            ->setcellvalue('G10', 'EXT. NOCT.')
            ->setcellvalue('H10', 'EXT. NOCT. CON REC.')
            ->setcellvalue('I10', 'EXT. MIX DIA')
            ->setcellvalue('J10', 'EXT. MIX DIA CON REC.')
            ->setcellvalue('K10', 'EXT. MIX NOCT.')
            ->setcellvalue('L10', 'EXT. MIX NOCT. CON REC.')
            ->setcellvalue('M10', 'DESCANSO')
            ->setcellvalue('N10', 'EMERGENCIA')
            ->setCellValue('O10', 'DESCANSO INCOMPLETO')
            ->setCellValue('P10', 'HORAS TOTALES');
cellColor('A10:P10', 'FFFF00');
$objPHPExcel->getActiveSheet()->getStyle('A10:P10')->applyFromArray(allBordersThin());
$sql = "SELECT rd.ficha, np.cedula, np.apenom, SUM( ordinaria + domingo + nacional ) AS horas , 
sum(extra+extraext+extranoc+extraextnoc+extranac+extranocnac+mixtodiurna+mixtonoc+mixtoextdiurna+mixtoextnoc+dialibre+emergencia+descansoincompleto) as HorasExtras,
if(extra          != 0,sum(extra),0) ext,
if(extraext       != 0,sum(extraext),0) extraext,
if(extranoc       != 0,sum(extranoc),0) extranoc,
if(extraextnoc    != 0,sum(extraextnoc),0) extraextnoc,
if(extranac       != 0,sum(extranac),0) extranac,
if(extranocnac    != 0,sum(extranocnac),0) extranocnac,
if(mixtodiurna    != 0,sum(mixtodiurna),0) mixtodiurna,
if(mixtonoc       != 0,sum(mixtonoc),0) mixtonoc,
if(mixtoextdiurna != 0,sum(mixtoextdiurna),0) mixtoextdiurna,
if(mixtoextnoc    != 0,sum(mixtoextnoc),0) mixtoextnoc,
if(dialibre       != 0,sum(dialibre),0) dialibre,
if(emergencia     != 0,sum(emergencia),0) emergencia,
if(descansoincompleto     != 0,sum(descansoincompleto),0) descansoincompleto
FROM reloj_detalle as rd
INNER JOIN nompersonal as np on (rd.ficha=np.ficha)
WHERE  rd.id_encabezado ='{$id}'
GROUP BY ficha";
$i         = $inicio = 11;

$res       =query($sql, $conexion);

$num_filas = num_rows($res);

$contador  =0;
while($fila=fetch_array($res))
{
    $ficha                    = $fila['ficha'];
    $cedula                   = $fila['cedula'];
    $nombre                   = utf8_encode($fila['apenom']);
    $horas_ordinarias         = $fila['horas'];
    $horas_extras             = $fila['HorasExtras'];
    $horas_extra              = $fila['horas'];
    $horas_ext                = $fila['ext'];
    $horas_extraext           = $fila['extraext'];
    $horas_extranoc           = $fila['extranoc'];
    $horas_extraextnoc        = $fila['extraextnoc'];
    $horas_mixtodiurna        = $fila['mixtodiurna'];
    $horas_mixtoextdiurna     = $fila['mixtoextdiurna'];
    $horas_mixtonoc           = $fila['mixtonoc'];
    $horas_mixtoextnoc        = $fila['mixtoextnoc'];
    $horas_extranocnac        = $fila['extranocnac'];
    $horas_dialibre           = $fila['dialibre'];
    $horas_emergencia         = $fila['emergencia'];
    $horas_descansoincompleto = $fila['descansoincompleto'];
    $horas_totales            = $horas_ordinarias+$horas_extras;
    if($contador<$num_filas-1)
    {    
        $objPHPExcel->getActiveSheet()->setCellValue('A'.$i, $ficha);
        $objPHPExcel->getActiveSheet()->setCellValue('B'.$i, $cedula);
        $objPHPExcel->getActiveSheet()->setCellValue('C'.$i, $nombre);
        $objPHPExcel->getActiveSheet()->setCellValue('D'.$i, $horas_ordinarias);
        $objPHPExcel->getActiveSheet()->setCellValue('E'.$i, $horas_ext);
        $objPHPExcel->getActiveSheet()->setCellValue('F'.$i, $horas_extraext);
        $objPHPExcel->getActiveSheet()->setCellValue('G'.$i, $horas_extranoc);
        $objPHPExcel->getActiveSheet()->setCellValue('H'.$i, $horas_extraextnoc);
        $objPHPExcel->getActiveSheet()->setCellValue('I'.$i, $horas_mixtodiurna);
        $objPHPExcel->getActiveSheet()->setCellValue('J'.$i, $horas_mixtoextdiurna);
        $objPHPExcel->getActiveSheet()->setCellValue('K'.$i, $horas_mixtonoc);
        $objPHPExcel->getActiveSheet()->setCellValue('L'.$i, $horas_mixtoextnoc);
        $objPHPExcel->getActiveSheet()->setCellValue('M'.$i, $horas_dialibre);
        $objPHPExcel->getActiveSheet()->setCellValue('N'.$i, $horas_emergencia);
        $objPHPExcel->getActiveSheet()->setCellValue('O'.$i, $horas_descansoincompleto);
        $objPHPExcel->getActiveSheet()->setCellValue('P'.$i, $horas_totales);
        $fin=$i;
    }
    else
    {
        $objPHPExcel->getActiveSheet()->setCellValue('A'.$i, '');
        $objPHPExcel->getActiveSheet()->setCellValue('B'.$i, '');
        $objPHPExcel->getActiveSheet()->setCellValue('C'.$i, 'TOTALES');
        $objPHPExcel->getActiveSheet()->setCellValue('D'.$i, '=SUM(D11:D'.($i-1).')');
        $objPHPExcel->getActiveSheet()->setCellValue('E'.$i, '=SUM(E11:E'.($i-1).')');
        $objPHPExcel->getActiveSheet()->setCellValue('F'.$i, '=SUM(F11:F'.($i-1).')');
        $objPHPExcel->getActiveSheet()->setCellValue('G'.$i, '=SUM(G11:G'.($i-1).')');
        $objPHPExcel->getActiveSheet()->setCellValue('H'.$i, '=SUM(H11:H'.($i-1).')');
        $objPHPExcel->getActiveSheet()->setCellValue('I'.$i, '=SUM(I11:I'.($i-1).')');
        $objPHPExcel->getActiveSheet()->setCellValue('J'.$i, '=SUM(J11:J'.($i-1).')');
        $objPHPExcel->getActiveSheet()->setCellValue('K'.$i, '=SUM(K11:K'.($i-1).')');
        $objPHPExcel->getActiveSheet()->setCellValue('L'.$i, '=SUM(L11:L'.($i-1).')');
        $objPHPExcel->getActiveSheet()->setCellValue('M'.$i, '=SUM(M11:M'.($i-1).')');
        $objPHPExcel->getActiveSheet()->setCellValue('N'.$i, '=SUM(N11:N'.($i-1).')');
        $objPHPExcel->getActiveSheet()->setCellValue('O'.$i, '=SUM(O11:O'.($i-1).')');
        $objPHPExcel->getActiveSheet()->setCellValue('P'.$i, '=SUM(P11:P'.($i-1).')');
   }
    
    $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':P'.$i)->applyFromArray(allBordersThin());
        //$objPHPExcel->getActiveSheet()->getStyle('E'.$i)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
    $i++;
    $contador++;
}
//$objPHPExcel->getActiveSheet()->setCellValue('C'.$i, "TOTALES");
$i=$i+3;

$objPHPExcel->getActiveSheet()->mergeCells('B'.$i.':B'.($i+4));
$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':B'.($i+4))->applyFromArray(allBordersThin());
$objPHPExcel->getActiveSheet()->mergeCells('C'.$i.':C'.($i+4));
$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':C'.($i+4))->applyFromArray(allBordersThin());
$objPHPExcel->getActiveSheet()->mergeCells('D'.$i.':D'.($i+4));
$objPHPExcel->getActiveSheet()->getStyle('D'.$i.':D'.($i+4))->applyFromArray(allBordersThin());
$i=$i+5;

$objPHPExcel->getActiveSheet()->getStyle('B'.$i)->applyFromArray(allBordersThin());
$objPHPExcel->getActiveSheet()->setCellValue('B'.$i, 'RECIBIDO POR');
$objPHPExcel->getActiveSheet()->getStyle('B'.$i)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$objPHPExcel->getActiveSheet()->getStyle('C'.$i)->applyFromArray(allBordersThin());
$objPHPExcel->getActiveSheet()->setCellValue('C'.$i, 'AUTORIZADO POR');
$objPHPExcel->getActiveSheet()->getStyle('C'.$i)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$objPHPExcel->getActiveSheet()->getStyle('D'.$i)->applyFromArray(allBordersThin());
$objPHPExcel->getActiveSheet()->setCellValue('D'.$i, 'PREPARADO POR');
$objPHPExcel->getActiveSheet()->getStyle('D'.$i)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

//$objPHPExcel->getActiveSheet()->setSelectedCells('B100');


$objPHPExcel->setActiveSheetIndex(0);

// Redirect output to a client’s web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="Reporte_Horas_Trabajadas.xls"');
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




>>>>>>> a33804069bbf47ebcb1a2f35d05f81c33e86b2d5
