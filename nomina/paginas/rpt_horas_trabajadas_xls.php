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

function formatearHora($hora)
{
    $horas = explode(":",$hora);
    if(trim($horas[0])=="00")
        $hora = "24:".$horas[1];
    else
        $hora = $horas[0].":".$horas[1];
    return $hora;
}

class tiempo
{
    private $temp,$min,$horas;
    public function aminutos($cad)
    {
        $this->temp = explode(":",$cad);
        $this->min = ($this->temp[0]*60)+($this->temp[1]);
        return $this->min;
    }
    public function ahoras($cad)
    {
        $this->temp = $cad;
        if($this->temp>59)
        {
            $this->temp = $this->temp/60;
            $this->temp = explode(".",number_format($this->temp,2,".",""));
            $this->temp[0] = strlen($this->temp[0])==1 ? "0".$this->temp[0] : $this->temp[0];
            $this->temp[1] = (((substr($this->temp[1],0,2))*60)/100);
            $this->temp[1] = round($this->temp[1]);
            //$this->horas = $this->temp[0].":".(strlen($this->temp[1][0])==1 ? "0".$this->temp[1][0] : round(substr($this->temp[1][0],0,2).'.'.substr($this->temp[1][0],2,1)));
            $this->horas = $this->temp[0].":".(strlen($this->temp[1])==1 ? "0".$this->temp[1] : $this->temp[1]);
        }
        elseif(($this->temp=="")||($this->temp==0))
        {
            $this->horas = "00:00";
        }
        else
        {
            $this->horas = "00:".(strlen($this->temp)==1 ? "0".$this->temp : $this->temp);//$this->temp;
        }
        return $this->horas;
    }
}

$conexion = conexion();

$codtip   = $_GET['codtip'];
$id       = $_GET['reg'];

require_once 'phpexcel/Classes/PHPExcel.php';

$objPHPExcel = new PHPExcel();

$objPHPExcel->getProperties()->setCreator("Amaxonia")
                             ->setLastModifiedBy("Amaxonia")
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

$objPHPExcel->getActiveSheet()->setTitle('Asistencias');
$objPHPExcel->getActiveSheet()
            ->setCellValue('B2',  $fila['empresa'])
            ->setCellValue('B3', ' '.$fila['rif'].' Telefonos '.$fila['telefono'])
            ->setCellValue('B4', 'Direccion: '.utf8_encode($fila['direccion']))
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
$objPHPExcel->getActiveSheet()->getStyle('A10:R10')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('A10:R10')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(7);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(35);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(30);
$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(30);
$objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(20);
$objPHPExcel->getActiveSheet()       
            ->setCellValue('A10', 'FICHA')
            ->setCellValue('B10', 'CEDULA')
            ->setCellValue('C10', 'NOMBRE Y APELLIDO')
            ->setCellValue('D10', 'ORDINARIAS')
            ->setCellValue('E10', 'DOMINGO')
            ->setCellValue('F10', 'FERIADO')
            ->setCellValue('G10', 'EXT. DIURNA')
            ->setcellvalue('H10', 'EXT. DIA CON REC.')
            ->setcellvalue('I10', 'EXT. NOCT.')
            ->setcellvalue('J10', 'EXT. NOCT. CON REC.')
            ->setcellvalue('K10', 'EXT. MIX DIA')
            ->setcellvalue('L10', 'EXT. MIX DIA CON REC.')
            ->setcellvalue('M10', 'EXT. MIX NOCT.')
            ->setcellvalue('N10', 'EXT. MIX NOCT. CON REC.')
            ->setcellvalue('O10', 'DESCANSO')
            ->setcellvalue('P10', 'EMERGENCIA')
            ->setCellValue('Q10', 'DESCANSO INCOMPLETO')
            ->setCellValue('R10', 'TOTALES');
cellColor('A10:R10', 'FFFF00');
$objPHPExcel->getActiveSheet()->getStyle('A10:R10')->applyFromArray(allBordersThin());
$sql_personal = "SELECT rd.ficha, np.cedula, np.apenom, rd.id_encabezado,
ROUND((SUM(TIME_TO_SEC(rd.ordinaria)+TIME_TO_SEC(rd.domingo)+TIME_TO_SEC(rd.nacional))/3600),2) horas,
ROUND((SUM(TIME_TO_SEC(rd.extra)+TIME_TO_SEC(rd.extraext)+TIME_TO_SEC(rd.extranoc)+TIME_TO_SEC(rd.extraextnoc)+TIME_TO_SEC(rd.extranac)+TIME_TO_SEC(rd.extranocnac)+TIME_TO_SEC(rd.mixtodiurna)+TIME_TO_SEC(rd.mixtonoc)+TIME_TO_SEC(rd.mixtoextdiurna)+TIME_TO_SEC(rd.mixtoextnoc)+TIME_TO_SEC(rd.dialibre)+TIME_TO_SEC(rd.emergencia)+TIME_TO_SEC(rd.descansoincompleto))/3600),2) HorasExtras,
ROUND((SUM(TIME_TO_SEC(rd.extra))/3600),2) ext,
ROUND((SUM(TIME_TO_SEC(rd.ordinaria))/3600),2) ordinaria,
ROUND((SUM(TIME_TO_SEC(rd.domingo))/3600),2) domingo,
ROUND((SUM(TIME_TO_SEC(rd.nacional))/3600),2) nacional,
ROUND((SUM(TIME_TO_SEC(rd.extraext))/3600),2) extraext,
ROUND((SUM(TIME_TO_SEC(rd.extranoc))/3600),2) extranoc,
ROUND((SUM(TIME_TO_SEC(rd.extraextnoc))/3600),2) extraextnoc,
ROUND((SUM(TIME_TO_SEC(rd.extranac))/3600),2) extranac,
ROUND((SUM(TIME_TO_SEC(rd.extranocnac))/3600),2) extranocnac,
ROUND((SUM(TIME_TO_SEC(rd.mixtodiurna))/3600),2) mixtodiurna,
ROUND((SUM(TIME_TO_SEC(rd.mixtonoc))/3600),2) mixtonoc,
ROUND((SUM(TIME_TO_SEC(rd.mixtoextdiurna))/3600),2) mixtoextdiurna,
ROUND((SUM(TIME_TO_SEC(rd.mixtoextnoc))/3600),2) mixtoextnoc,
ROUND((SUM(TIME_TO_SEC(rd.dialibre))/3600),2) dialibre,
ROUND((SUM(TIME_TO_SEC(rd.emergencia))/3600),2) emergencia,
ROUND((SUM(TIME_TO_SEC(rd.descansoincompleto))/3600),2) descansoincompleto
FROM reloj_detalle as rd
LEFT JOIN nompersonal as np on (rd.ficha=np.ficha)
WHERE  rd.id_encabezado ='{$id}'
GROUP BY ficha
ORDER BY ficha";

$i         = $inicio = 11;

$res_personal      =query($sql_personal, $conexion);

$num_filas = num_rows($res_personal);

$contador  =0;
$horas_ordinaria_total=$horas_domingo_total=$horas_nacional_total=$horas_extra_total=$horas_extraext_total=$horas_extranoc_total=$horas_extraextnoc_total=0;
$horas_mixtodiurna_total=$horas_mixtoextdiurna_total=$horas_mixtonoc_total=$horas_mixtoextnoc_total=$horas_extranocnac_total=$horas_dialibre_total=0;
$horas_emergencia_total=$horas_descansoincompleto_total=$horas_totales_total=0;

while($fila_personal=fetch_array($res_personal))
{
    $ficha                    = $fila_personal['ficha'];
    $cedula                   = $fila_personal['cedula'];
    $nombre                   = utf8_encode($fila_personal['apenom']);    

    
    $horas_ordinarias=$horas_extras=$horas_ext=$horas_extraext=$horas_extranoc=$horas_extraextnoc=$horas_mixtodiurna=$horas_totales=0;
    $horas_mixtoextdiurna=$horas_mixtonoc=$horas_mixtoextnoc=$horas_extranocnac=$horas_dialibre=$horas_emergencia=$horas_descansoincompleto=0;
    
    
    $horas_ordinarias           = $fila_personal['horas'];
    $horas_ordinaria            = $fila_personal['ordinaria'];
    $horas_ordinaria_total+=$horas_ordinaria;
    $horas_domingo              = $fila_personal['domingo'];
    $horas_domingo_total+=$horas_domingo;
    $horas_nacional             = $fila_personal['nacional'];
    $horas_nacional_total+=$horas_nacional;
    $horas_extras               = $fila_personal['HorasExtras'];
    $horas_extra                = $fila_personal['ext'];
    $horas_extra_total+=$horas_extra;
    $horas_extraext             = $fila_personal['extraext'];
    $horas_extraext_total+=$horas_extraext;
    $horas_extranoc             = $fila_personal['extranoc'];
    $horas_extranoc_total+=$horas_extranoc;
    $horas_extraextnoc          = $fila_personal['extraextnoc'];
    $horas_extraextnoc_total+=$horas_extraextnoc;
    $horas_mixtodiurna          = $fila_personal['mixtodiurna'];
    $horas_mixtodiurna_total+=$horas_mixtodiurna;
    $horas_mixtoextdiurna       = $fila_personal['mixtoextdiurna'];
    $horas_mixtoextdiurna_total+=$horas_mixtoextdiurna;
    $horas_mixtonoc             = $fila_personal['mixtonoc'];
    $horas_mixtonoc_total+=$horas_mixtonoc;
    $horas_mixtoextnoc          = $fila_personal['mixtoextnoc'];
    $horas_mixtoextnoc_total+=$horas_mixtoextnoc;
    $horas_extranocnac          = $fila_personal['extranocnac'];
    $horas_extranocnac_total+=$horas_extranocnac;
    $horas_dialibre             = $fila_personal['dialibre'];
    $horas_dialibre_total+=$horas_dialibre;
    $horas_emergencia           = $fila_personal['emergencia'];
    $horas_emergencia_total+=$horas_emergencia;
    $horas_descansoincompleto   = $fila_personal['descansoincompleto'];
    $horas_descansoincompleto_total+=$horas_descansoincompleto;
    $horas_totales              = $horas_ordinarias+$horas_extras;
    $horas_totales_total+=$horas_totales;
    
        $objPHPExcel->getActiveSheet()->setCellValue('A'.$i, $ficha);
        $objPHPExcel->getActiveSheet()->setCellValue('B'.$i, $cedula);
        $objPHPExcel->getActiveSheet()->setCellValue('C'.$i, $nombre);
        $objPHPExcel->getActiveSheet()->setCellValue('D'.$i, $horas_ordinaria);
        $objPHPExcel->getActiveSheet()->setCellValue('E'.$i, $horas_domingo);
        $objPHPExcel->getActiveSheet()->setCellValue('F'.$i, $horas_nacional);
        $objPHPExcel->getActiveSheet()->setCellValue('G'.$i, $horas_ext);
        $objPHPExcel->getActiveSheet()->setCellValue('H'.$i, $horas_extraext);
        $objPHPExcel->getActiveSheet()->setCellValue('I'.$i, $horas_extranoc);
        $objPHPExcel->getActiveSheet()->setCellValue('J'.$i, $horas_extraextnoc);
        $objPHPExcel->getActiveSheet()->setCellValue('K'.$i, $horas_mixtodiurna);
        $objPHPExcel->getActiveSheet()->setCellValue('L'.$i, $horas_mixtoextdiurna);
        $objPHPExcel->getActiveSheet()->setCellValue('M'.$i, $horas_mixtonoc);
        $objPHPExcel->getActiveSheet()->setCellValue('N'.$i, $horas_mixtoextnoc);
        $objPHPExcel->getActiveSheet()->setCellValue('O'.$i, $horas_dialibre);
        $objPHPExcel->getActiveSheet()->setCellValue('P'.$i, $horas_emergencia);
        $objPHPExcel->getActiveSheet()->setCellValue('Q'.$i, $horas_descansoincompleto);
        $objPHPExcel->getActiveSheet()->setCellValue('R'.$i, $horas_totales);
        $fin=$i;
   
    $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':R'.$i)->applyFromArray(allBordersThin());
        //$objPHPExcel->getActiveSheet()->getStyle('E'.$i)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
    $i++;
    $contador++;
}
$fin=$inicio+$contador;
 $objPHPExcel->getActiveSheet()->setCellValue('A'.($i+1), '');
        $objPHPExcel->getActiveSheet()->setCellValue('B'.($i+1), '');
        $objPHPExcel->getActiveSheet()->setCellValue('C'.($i+1), 'TOTALES');
        $objPHPExcel->getActiveSheet()->setCellValue('D'.($i+1), $horas_ordinaria_total);
        $objPHPExcel->getActiveSheet()->setCellValue('E'.($i+1), $horas_domingo_total);
        $objPHPExcel->getActiveSheet()->setCellValue('F'.($i+1), $horas_nacional_total);
        $objPHPExcel->getActiveSheet()->setCellValue('G'.($i+1), $horas_extra_total);
        $objPHPExcel->getActiveSheet()->setCellValue('H'.($i+1), $horas_extraext_total);
        $objPHPExcel->getActiveSheet()->setCellValue('I'.($i+1), $horas_extranoc_total);
        $objPHPExcel->getActiveSheet()->setCellValue('J'.($i+1), $horas_extraextnoc_total);
        $objPHPExcel->getActiveSheet()->setCellValue('K'.($i+1), $horas_mixtodiurna_total);
        $objPHPExcel->getActiveSheet()->setCellValue('L'.($i+1), $horas_mixtoextdiurna_total);
        $objPHPExcel->getActiveSheet()->setCellValue('M'.($i+1), $horas_mixtonoc_total);
        $objPHPExcel->getActiveSheet()->setCellValue('N'.($i+1), $horas_mixtoextnoc_total);
        $objPHPExcel->getActiveSheet()->setCellValue('O'.($i+1), $horas_dialibre_total);
        $objPHPExcel->getActiveSheet()->setCellValue('P'.($i+1), $horas_emergencia_total);
        $objPHPExcel->getActiveSheet()->setCellValue('Q'.($i+1), $horas_descansoincompleto_total);
        $objPHPExcel->getActiveSheet()->setCellValue('R'.($i+1), $horas_totales_total);
        $objPHPExcel->getActiveSheet()->getStyle('A'.($i+1).':R'.($i+1))->applyFromArray(allBordersThin());
$i=$i+4;

$objPHPExcel->getActiveSheet()->mergeCells('B'.$i.':B'.($i+5));
$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':B'.($i+5))->applyFromArray(allBordersThin());
$objPHPExcel->getActiveSheet()->mergeCells('C'.$i.':C'.($i+5));
$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':C'.($i+5))->applyFromArray(allBordersThin());
$objPHPExcel->getActiveSheet()->mergeCells('D'.$i.':D'.($i+5));
$objPHPExcel->getActiveSheet()->getStyle('D'.$i.':D'.($i+5))->applyFromArray(allBordersThin());
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
/*
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

$objPHPExcel->getProperties()->setCreator("Amaxonia")
                             ->setLastModifiedBy("Amaxonia")
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

//$objDrawing->setHeight(36);
//$objPHPExcel->getActiveSheet()->getHeaderFooter()->addImage($objDrawing, PHPExcel_Worksheet_HeaderFooter::IMAGE_HEADER_LEFT);

$objPHPExcel->setActiveSheetIndex(0); 

$objPHPExcel->getActiveSheet()->setTitle('Asistencias');
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

$sql_personal = "SELECT rd.ficha, np.cedula, np.apenom
FROM reloj_detalle as rd
INNER JOIN nompersonal as np on (rd.ficha=np.ficha)
WHERE  rd.id_encabezado ='{$id}'
GROUP BY ficha";

$i         = $inicio = 11;

$res_personal      =query($sql_personal, $conexion);

$num_filas = num_rows($res);

$contador  =0;
while($fila_personal=fetch_array($res_personal))
{
    $ficha                    = $fila['ficha'];
    $cedula                   = $fila['cedula'];
    $nombre                   = utf8_encode($fila['apenom']);    
    
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
    WHERE  rd.id_encabezado ='{$id}'";


    while($fila=fetch_array($res))
    {
        
    }
    
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
*/
?>
