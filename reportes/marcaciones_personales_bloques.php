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
$res = $db->query($sql_emp);
$empleado = mysqli_fetch_array($res);

$sql_per = "SELECT * FROM `caa_periodos` WHERE `ficha` ='$ficha' ORDER BY fecha_registro DESC";
$res_per = $db->query($sql_per);
//--------------------------------------------------------------------------------------------------------------------------------------
class MYPDF extends TCPDF {
    //Page header
    public function Header() {
        // Logo
        $image_file = "/var/www/html/asamblea_rrhh/includes/assets/img/logo_mini.jpg";
        // Set font
        $this->SetFont('helvetica', 'N', 10);
        $this->Cell(185, 25, '', 0, 1, 'C', 0, '', 0);
        // Image method signature:
        // Image($image_file, $x=10, $y=10, $w=15, $h=15, $type='', $link='', $align='C', $resize=false, $dpi=300, $palign='', $ismask=false, $imgmask=false, $border=0, $fitbox=false, $hidden=false, $fitonpage=false)
        $this->Image($image_file, $x=98, $y=12, $w=15,   $h=15, $type='', $link='', $align='C', $resize=false, $dpi=300, $palign='', $ismask=false, $imgmask=false, $border=0, $fitbox=false, $hidden=false, $fitonpage=false);

        //$this->Image($image_file, $x=165, $y=12, $w=15,   $h=15, $type='', $link='', $align='C', $resize=false, $dpi=300, $palign='', $ismask=false, $imgmask=false, $border=0, $fitbox=false, $hidden=false, $fitonpage=false);

        $this->Cell(185, 6, 'ASAMBLEA NACIONAL', 0, 1, 'C', 0, '', 0);
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
$pdf->SetKeywords('ASAMBLEA NACIONAL, Formas, Reporte, PDF');
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
$pdf->Cell(35, 6, 'Ficha:', 0, 0, 'R', 0, '', 0);
$pdf->Cell(150, 6, $ficha, 'B', 1, 'L', 0, '', 0);
$pdf->Cell(150, 3, '', 0, 1, 'L', 0, '', 0);
// ---------------------------------------------------------
$pdf->Cell(35, 6, 'Fecha', 1, 0, 'C', 0, '', 0);
$pdf->Cell(35, 6, 'Entrada', 1, 0, 'C', 0, '', 0);
$pdf->Cell(35, 6, 'Salida', 1, 0, 'C', 0, '', 0);
$pdf->Cell(35, 6, 'Tiempo', 1, 0, 'C', 0, '', 0);
$pdf->Cell(45, 6, 'Turno', 1, 1, 'C', 0, '', 0);

while ($periodos = mysqli_fetch_array($res_per)) {
    $sql_emp = "SELECT * FROM nomturnos WHERE turno_id = '".$periodos['turno_id']."'";
    $res = $db->query($sql_emp);
    $turno = mysqli_fetch_array($res);

    $pdf->Cell(35, 6, fecha($periodos['fecha_registro']), 1, 0, 'C', 0, '', 0);
    $pdf->Cell(35, 6, $periodos['entrada'], 1, 0, 'C', 0, '', 0);
    $pdf->Cell(35, 6, $periodos['salida'], 1, 0, 'C', 0, '', 0);
    $pdf->Cell(35, 6, $periodos['tiempo'], 1, 0, 'C', 0, '', 0);
    $pdf->Cell(45, 6, $truno['descripcion'], 1, 1, 'C', 0, '', 0);


}
//Close and output PDF document
$pdf->Output('control_diario_asistencias_bloques_trabajo.pdf', 'I');
?>