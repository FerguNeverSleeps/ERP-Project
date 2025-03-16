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
$id_usuario=$_SESSION['id_usuario'];
//--------------------------------------------------------------------------------------------------------------------------------------
$sql     = "SELECT * FROM nomempresa";
$res     = $db->query($sql);
$empresa = mysqli_fetch_array($res);
//--------------------------------------------------------------------------------------------------------------------------------------
$fecha    = $_POST['fecha'];
$dpto     = $_POST['dpto'];
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
if($dpto == "all") {
    $sql     = "SELECT * FROM departamento";
    if( $_SESSION['acceso_dep']==0 && $_SESSION['departamento']!=0)
    {
        $sql     = "SELECT * FROM departamento WHERE IdDepartamento = ".$_SESSION['departamento']."";
//        $WHERE = " a.IdDepartamento = ".$_SESSION['departamento']."";

    }

    if( $_SESSION['acceso_dep']==1)
    {
        $sql     = "SELECT * FROM departamento WHERE IdDepartamento IN (SELECT  id_departamento from usuario_departamento WHERE id_usuario=".$id_usuario.")";
//        $WHERE = " a.IdDepartamento IN (SELECT  id_departamento from usuario_departamento WHERE id_usuario=".$id_usuario.")";

    }
    $res_uni = $db->query($sql);
}else{
    $sql     = "SELECT * FROM departamento WHERE IdDepartamento = '$dpto'";
    $res_uni = $db->query($sql);
}
//--------------------------------------------------------------------------------------------------------------------------------------
class MYPDF extends TCPDF {
    //Page header
    public function Header() {
        // Logo
        $image_file = "/var/www/html/asamblea_rrhh/includes/assets/img/logo_mini.jpg";
        // Set font
        $this->SetFont('helvetica', 'N', 10);
        $this->Cell(250, 25, '', 0, 1, 'C', 0, '', 0);
        // Image method signature:
        // Image($image_file, $x=10, $y=10, $w=15, $h=15, $type='', $link='', $align='C', $resize=false, $dpi=300, $palign='', $ismask=false, $imgmask=false, $border=0, $fitbox=false, $hidden=false, $fitonpage=false)
        $this->Image($image_file, $x=150, $y=12, $w=15,   $h=15, $type='', $link='', $align='C', $resize=false, $dpi=300, $palign='', $ismask=false, $imgmask=false, $border=0, $fitbox=false, $hidden=false, $fitonpage=false);

        //$this->Image($image_file, $x=165, $y=12, $w=15,   $h=15, $type='', $link='', $align='C', $resize=false, $dpi=300, $palign='', $ismask=false, $imgmask=false, $border=0, $fitbox=false, $hidden=false, $fitonpage=false);

        $this->Cell(250, 6, 'SERVICIO NACIONAL DE MIGRACIÓN', 0, 1, 'C', 0, '', 0);
        $this->Cell(250, 6, 'DIRECCIÓN DE RECURSOS HUMANOS', 0, 1, 'C', 0, '', 0);
        $this->Cell(250, 6, 'DEPARTAMENTO DE REGISTRO Y CONTROL DE DE RECURSOS HUMANOS', 0, 1, 'C', 0, '', 0);
        $this->Cell(250, 6, 'CONTROL DIARIO DE ASISTENCIA', 0, 1, 'C', 0, '', 0);
        $this->Cell(250, 6, '', 0, 1, 'C', 0, '', 0);
        $this->Cell(250, 6, '', 0, 1, 'L', 0, '', 0);
    }
    // Page footer
    public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-15);
        // Set font
        $this->SetFont('helvetica', 'I', 8);
        // Page number
        $this->Cell(100, 10, 'FORMA AL/DRH/DRCRH/10', 'T', false, 'L', 0, '', 0, false, 'T', 'M');
        $this->Cell(85, 10, 'Página '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 'T', false, 'R', 0, '', 0, false, 'T', 'M');
    }
}
// create new PDF document
$pdf = new MYPDF('L', PDF_UNIT, "A4", true, 'UTF-8', false);
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
$numUnidades=0;
while( $unidad = mysqli_fetch_array($res_uni) ){
    
    $sql_emp = "SELECT a.*,b.apenom, b.nomposicion_id, b.cedula"
            . " FROM caa_resumen a "
            . " LEFT JOIN nompersonal b ON a.ficha = b.ficha"
            . " WHERE  b.IdDepartamento = '".$unidad['IdDepartamento']."' AND fecha = '".fecha($fecha)."' "
            . " GROUP BY a.ficha"
            . " ORDER BY b.nomposicion_id ASC";
    $res_emp = $db->query($sql_emp);
    $rowcount=mysqli_num_rows($res_emp);

    if($rowcount>0){
    
        $pdf->Cell(35, 6, 'Unidad Administrativa:', 0, 0, 'R', 0, '', 0);
        $pdf->Cell(150, 6, $unidad['Descripcion'], 'B', 1, 'L', 0, '', 0);
        $pdf->Cell(35, 6, 'Fecha:', 0, 0, 'R', 0, '', 0);
        $pdf->Cell(150, 6, $fecha, 'B', 1, 'L', 0, '', 0);
        $pdf->Cell(150, 3, '', 0, 1, 'L', 0, '', 0);
        // ---------------------------------------------------------
        $numUnidades++;
        $pdf->Cell(20, 6, 'Cédula', 'LTR', 0, 'C', 0, '', 1);
        $pdf->Cell(20, 6, 'Posición', 'LTR', 0, 'C', 0, '', 1);
        $pdf->Cell(100, 6, 'Nombre y Apellido', 'LTR', 0, 'C', 0, '', 1);
        $pdf->Cell(20, 6, 'Entrada', 'LTR', 0, 'C', 0, '', 1);
        $pdf->Cell(20, 6, 'Salida', 'LTR', 0, 'C', 0, '', 1);
        $pdf->Cell(20, 6, 'Tardanza ', 'LTR', 0, 'C', 0, '', 1);
        $pdf->Cell(20, 6, 'Ausencia ', 'LTR', 0, 'C', 0, '', 1);
        $pdf->Cell(20, 6, 'Trabajadas ', 'LTR', 1, 'C', 0, '', 1);

//        $pdf->Cell(45, 6, 'Servidor', 'LRB', 0, 'C', 0, '', 1);
//        $pdf->Cell(28, 6, 'al Puesto de Trabajo', 'LRB', 0, 'C', 0, '', 1);
//        $pdf->Cell(28, 6, 'del Puesto de Trabajo', 'LRB', 0, 'C', 0, '', 1);
//        $pdf->Cell(28, 6, 'Tardanza', 'LRB', 0, 'C', 0, '', 1);
//        $pdf->Cell(28, 6, 'Ausencia', 'LRB', 0, 'C', 0, '', 1);
//        $pdf->Cell(28, 6, 'Trabajadas', 'LRB', 1, 'C', 0, '', 1);

        while ($empleados = mysqli_fetch_array($res_emp)) {
            
            $sql     = "SELECT * FROM nomturnos WHERE turno_id = '".$empleados['turno_id']."'";
            $res_tur = $db->query($sql);
            $turno = mysqli_fetch_array($res_tur);
            $pdf->Cell(20, 6, $empleados['cedula'], 1, 0, 'L', 0, '', 0);
            $pdf->Cell(20, 6, $empleados['nomposicion_id'], 1, 0, 'L', 0, '', 0);
            $pdf->Cell(100, 6, $empleados['apenom'], 1, 0, 'L', 0, '', 0);
            $pdf->Cell(20, 6, $empleados['entrada'], 1, 0, 'C', 0, '', 0);
            $pdf->Cell(20, 6, $empleados['salida'], 1, 0, 'C', 0, '', 0);
            $pdf->Cell(20, 6, $empleados['tardanza'], 1, 0, 'C', 0, '', 0);
            $pdf->Cell(20, 6, $empleados['h_ausencia'], 1, 0, 'C', 0, '', 0);
            $pdf->Cell(20, 6, $empleados['tiempo'], 1, 1, 'C', 0, '', 0);
        }
        $pdf->Cell(185, 6, "", 0, 1, 'C', 0, '', 1);
    }
}
if($numUnidades==1){
    $pdf->Cell(185, 6, 'SOLO SE ENCONTRARON MARCACIONES PARA ESTA UNIDAD ADMINISTRATIVA EN LA FECHA SOLICITADA.', 1, 1, 'C', 0, '', 1);
    $pdf->Cell(185, 6, "", 0, 1, 'C', 0, '', 1); 
}elseif($numUnidades>1){
    $pdf->Cell(185, 6, 'SOLO SE ENCONTRARON MARCACIONES PARA ESTAS UNIDADES ADMINISTRATIVAS EN LA FECHA SOLICITADA.', 1, 1, 'C', 0, '', 1);
    $pdf->Cell(185, 6, "", 0, 1, 'C', 0, '', 1); 
}else{
    $pdf->Cell(185, 6, 'NO SE ENCONTRARON MARCACIONES PARA NINGUNA UNIDAD ADMINISTRATIVA EN LA FECHA SOLICITADA.', 1, 1, 'C', 0, '', 1);
    $pdf->Cell(185, 6, "", 0, 1, 'C', 0, '', 1); 
}
$pdf->Cell(185, 6, '', 0, 1, 'L', 0, '', 0);
$pdf->Cell(35, 6, 'Observaciones:', 0, 0, 'R', 0, '', 0);
$pdf->Cell(150, 6, '', 'B', 1, 'L', 0, '', 0);
$pdf->Cell(35, 6, '', 0, 0, 'R', 0, '', 0);
$pdf->Cell(150, 6, '', 'B', 1, 'L', 0, '', 0);

$pdf->Cell(185, 6, '', 0, 1, 'L', 0, '', 0);
$pdf->Cell(50, 6, '', 0, 0, 'R', 0, '', 0);
$pdf->Cell(85, 6, 'Jefe de Unidad Administrativa', 'T', 1, 'C', 0, '', 0);
//Close and output PDF document
// clean the output buffer
ob_clean();
$pdf->Output('control_diario_asistencias.pdf', 'D');
?>