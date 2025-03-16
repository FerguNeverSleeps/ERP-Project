<?php
session_start();
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('America/Panama');

require('../../nomina/tcpdf/tcpdf.php');
include("../../nomina/lib/common.php");
require_once('../../nomina/lib/database.php');
$db       = new Database($_SESSION['bd']);
//--------------------------------------------------------------------------------------------------------------------------------------
$sql     = "SELECT * FROM nomempresa";
$res     = $db->query($sql);
$empresa = mysqli_fetch_array($res);
//--------------------------------------------------------------------------------------------------------------------------------------
$ficha    = $_GET['ficha'];
$fecha1   = fecha($_GET['fecha1']);
$fecha2   = fecha($_GET['fecha2']);
//--------------------------------------------------------------------------------------------------------------------------------------
function fecha($fecha){
    if ( (!empty($fecha)) || ($fecha != NULL) )  {
        $separa  = explode("-", $fecha);
        $dia     = $separa[0];
        $mes     = $separa[1];
        $anio    = $separa[2];
        $fecha_n = $anio."-".$mes."-".$dia;
        return $fecha_n;
    }else{
        return '0000-00-00';
    }
}
//--------------------------------------------------------------------------------------------------------------------------------------
$sql_emp = "SELECT * FROM nompersonal WHERE ficha = '$ficha'";
echo $sql_emp."<br>";
$res = $db->query($sql_emp);
$empleado = mysqli_fetch_array($res);

$sql_emp = "SELECT * FROM `caa_registros` WHERE `ficha` ='$ficha' AND `fecha_registro` >='$fecha1' AND `fecha_registro` <='$fecha2' ORDER BY id ASC";
echo $sql_emp."<br>";
$res_emp = $db->query($sql_emp);
//--------------------------------------------------------------------------------------------------------------------------------------
class MYPDF extends TCPDF {
    //Page header
    public function Header() {
        // Logo
        $image_file = "/var/www/html/migracion_rrhh/includes/imagenes/logo_selectra1.png";
        // Set font
        $this->SetFont('helvetica', 'N', 10);
        $this->Cell(185, 25, '', 0, 1, 'C', 0, '', 0);
        // Image method signature:
        // Image($image_file, $x=10, $y=10, $w=15, $h=15, $type='', $link='', $align='C', $resize=false, $dpi=300, $palign='', $ismask=false, $imgmask=false, $border=0, $fitbox=false, $hidden=false, $fitonpage=false)
        $this->Image($image_file, $x=98, $y=12, $w=15,   $h=15, $type='', $link='', $align='C', $resize=false, $dpi=300, $palign='', $ismask=false, $imgmask=false, $border=0, $fitbox=false, $hidden=false, $fitonpage=false);

        //$this->Image($image_file, $x=165, $y=12, $w=15,   $h=15, $type='', $link='', $align='C', $resize=false, $dpi=300, $palign='', $ismask=false, $imgmask=false, $border=0, $fitbox=false, $hidden=false, $fitonpage=false);

        $this->Cell(185, 6, 'SERVICIO NACIONAL DE MIGRACION', 0, 1, 'C', 0, '', 0);
        $this->Cell(185, 6, 'DIRECCION DE RECURSOS HUMANOS', 0, 1, 'C', 0, '', 0);
        $this->Cell(185, 6, 'DEPARTAMENTO DE REGISTRO Y CONTROL DE DE RECURSOS HUMANOS', 0, 1, 'C', 0, '', 0);
        $this->Cell(185, 6, 'CONTROL DE MARCACIONES POR EMPLEADO', 0, 1, 'C', 0, '', 0);
        $this->Cell(185, 6, '', 0, 1, 'C', 0, '', 0);
        $this->Cell(185, 6, '', 0, 1, 'L', 0, '', 0);
    }
    // Page footer
    public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-15);
        // Set font
        $this->SetFont('helvetica', 'I', 8);
        // Page number
        $this->Cell(0, 10, 'Página '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }
}
// create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, "A4", true, 'UTF-8', false);
// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Pedro Martinez');
$pdf->SetTitle('Control diario de Asistencia');
$pdf->SetSubject('Control de Asistencia');
$pdf->SetKeywords('MINISTERIO DE AMBIENTE, Formas, Reporte, PDF');
// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);
// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, 60, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
    require_once(dirname(__FILE__).'/lang/eng.php');
    $pdf->setLanguageArray($l);
}
// add a page
$pdf->AddPage();
$pdf->SetFont('times', 'N', 10);
$pdf->Cell(35, 6, 'Funcionario:', 0, 0, 'R', 0, '', 0);
$pdf->Cell(150, 6, $empleado['apenom']." ".$empleado['cedula'], 'B', 1, 'L', 0, '', 0);

$pdf->Cell(35, 6, 'Numero de posicion:', 0, 0, 'R', 0, '', 0);
$pdf->Cell(30, 6, $empleado['nomposicion_id'], 'B', 0, 'L', 0, '', 0);

$pdf->Cell(30, 6, 'Inicio:', 0, 0, 'R', 0, '', 0);
$pdf->Cell(30, 6, $_GET['fecha1'], 'B', 0, 'L', 0, '', 0);

$pdf->Cell(30, 6, 'Final:', 0, 0, 'R', 0, '', 0);
$pdf->Cell(30, 6, $_GET['fecha2'], 'B', 1, 'L', 0, '', 0);

$pdf->Cell(150, 3, '', 0, 1, 'L', 0, '', 0);
// ---------------------------------------------------------
$pdf->Cell(9, 6, 'N°', 1, 0, 'C', 0, '', 0);
$pdf->Cell(44, 6, 'Fecha', 1, 0, 'C', 0, '', 0);
$pdf->Cell(44, 6, 'Hora', 1, 0, 'C', 0, '', 0);
$pdf->Cell(44, 6, 'Tipo de Registro', 1, 0, 'C', 0, '', 0);
$pdf->Cell(44, 6, 'Dispositivo', 1, 1, 'C', 0, '', 0);
$i=1;
while ($empleados = mysqli_fetch_array($res_emp)) {
    $pdf->Cell(9, 6, $i, 1, 0, 'C', 0, '', 0);
    $pdf->Cell(44, 6, fecha($empleados['fecha']), 1, 0, 'C', 0, '', 0);
    $pdf->Cell(44, 6, $empleados['hora'], 1, 0, 'C', 0, '', 0);
    $pdf->Cell(44, 6, $empleados['tipo_registro'], 1, 0, 'C', 0, '', 0);
    $pdf->Cell(44, 6, $empleados['dispositivo'], 1, 1, 'C', 0, '', 0);
    $i++;
}
ob_clean();
//Close and output PDF document
$pdf->Output('control_diario_asistencias.pdf', 'I');
?>