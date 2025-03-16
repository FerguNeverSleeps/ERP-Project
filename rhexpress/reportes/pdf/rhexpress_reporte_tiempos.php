<?php
//-------------------------------------------------
session_start();
//-------------------------------------------------
require_once "../../assets/global/tcpdf/tcpdf.php";
//-------------------------------------------------
require_once "../../config/rhexpress_config.php";
// require_once "config/rhexpress_config.php";
$conexion =  mysqli_connect( DB_HOST, DB_USUARIO, DB_CLAVE, $_SESSION['bd'] ) or die( 'No Hay Conexión con el Servidor de Mysql' );
        mysqli_query($conexion, 'SET CHARACTER SET utf8');
// var_dump($conexion);
//-------------------------------------------------
$useruid = $_SESSION['useruid_rhexpress'];
$tipo = $_GET['tipo'];
//-------------------------------------------------
$sql = "SELECT * FROM `dias_incapacidad` WHERE `tipo_justificacion` = '$tipo' AND `cedula` = '".$_SESSION['cedula_rhexpress']."'";
$res = $conexion->query($sql);

//-------------------------------------------------

class MYPDF extends TCPDF {

    //Page header
    public function Header() {
        // Logo
		$url_base="../../../nomina/paginas/";
		if (file_exists($url_base.$_SESSION['foto_rhexpress'])) {
		    $image_file = $url_base.$_SESSION['foto_rhexpress'];
		} else {
		    $image_file = '../../assets/pages/img/login/silueta.jpg';
		}
        $this->Image($image_file, 10, 10, 15, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
        // Set font
        $this->SetFont('helvetica', 'B', 20);
        // Title
        $this->Cell(0, 15, 'Reporte General - Tiempos', 0, false, 'C', 0, '', 0, false, 'M', 'M');
    }

    // Page footer
    public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-15);
        // Set font
        $this->SetFont('helvetica', 'I', 8);
        // Page number
        $this->Cell(0, 10, 'Pagina '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }
}
$pdf = new MYPDF("L", PDF_UNIT, "Legal", true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetTitle('Reporte General - Tiempos');
// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
    require_once(dirname(__FILE__).'/lang/eng.php');
    $pdf->setLanguageArray($l);
}
// ---------------------------------------------------------
// set font
$pdf->SetFont('times', '', 11);
// add a page
$pdf->AddPage();
//Cell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=0, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M')
$pdf->Ln(10);
$pdf->Cell(0, 1, '', "B", 1, 'L', 0, '', 0);
// test Cell stretching
$pdf->SetFont('times', 'B', 12);
$pdf->Cell(135, 8, 'Funcionario', "B", 0, 'L', 0, '', 0);
$pdf->Cell(100, 8, 'Cedula', "B", 0, 'L', 0, '', 0);
$pdf->Cell(100, 8, 'Posicion', "B", 1, 'L', 0, '', 0);
$pdf->SetFont('times', '', 10);
$pdf->Cell(135, 6, $_SESSION['nombre_rhexpress'], "B", 0, 'L', 0, '', 0);
$pdf->Cell(100, 6, $_SESSION['cedula_rhexpress'], "B", 0, 'L', 0, '', 0);
$pdf->Cell(100, 6, $_SESSION['pos_rhexpress'], "B", 1, 'L', 0, '', 0);
$pdf->SetFont('times', 'B', 12);
$pdf->Cell(135, 8, 'Gerencia', "B", 0, 'L', 0, '', 0);
$pdf->Cell(100, 8, 'Departamento', "B", 0, 'L', 0, '', 0);
$pdf->Cell(100, 8, 'Cargo', "B", 1, 'L', 0, '', 0);
$pdf->SetFont('times', '', 10);
$pdf->Cell(135, 6, $_SESSION['gerencia_rhexpress'], "B", 0, 'L', 0, '', 0);
$pdf->Cell(100, 6, $_SESSION['dpto_rhexpress'], "B", 0, 'L', 0, '', 0);
$pdf->Cell(100, 6, $_SESSION['cargo_rhexpress'], "B", 1, 'L', 0, '', 0);

$pdf->Ln(5);
$pdf->SetFont('times', 'B', 16);
$pdf->Cell(0, 8, 'DETALLES', 0, 1, 'C', 0, '', 0);
$pdf->Ln(5);
$pdf->SetFont('times', 'B', 12);
$pdf->Cell(20, 8, "Fecha", "BT", 0, 'L', 0, '', 0);
$pdf->Cell(265, 8, "Observacion", "BT", 0, 'L', 0, '', 0);
$pdf->Cell(15, 8, "Dias", "BT", 0, 'L', 0, '', 0);
$pdf->Cell(15, 8, "horas", "BT", 0, 'L', 0, '', 0);
$pdf->Cell(20, 8, "Minutos", "BT", 1, 'L', 0, '', 0);
$pdf->SetFont('times', '', 8);
while($fila=mysqli_fetch_array($res))
{
    // echo $fila['fecha'] . "<br>";
    // echo $fila['observacion'] . "<br>";
    // echo $fila['dias'] . "<br>";
    // echo $fila['horas'] . "<br>";
    // echo $fila['minutos'] . "<br>";
	$pdf->Cell(20, 6, $fila['fecha'], "B", 0, 'L', 0, '', 0);
	$pdf->Cell(265, 6, $fila['observacion'], "B", 0, 'L', 0, '', 0);
	$pdf->Cell(15, 6, $fila['dias'], "B", 0, 'L', 0, '', 0);
	$pdf->Cell(15, 6, $fila['horas'], "B", 0, 'L', 0, '', 0);
    $pdf->Cell(20, 6, $fila['minutos'], "B", 1, 'L', 0, '', 0);
}
//Close and output PDF document
$pdf->Output('reporte_tiempos.pdf', 'I');
?>