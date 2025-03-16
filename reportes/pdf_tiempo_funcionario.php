<?php
session_start();
ob_start();

require('../../nomina/tcpdf/tcpdf.php');
include("../../nomina/lib/common.php");
date_default_timezone_set('America/Panama');
require_once('../../nomina/lib/database.php');
$db = new Database($_SESSION['bd']);
//--------------------------------------------------------------------------------------------------------------------------------------
//consulta de datos
$sql = "SELECT b.apenom as nombre, a.tipo_justificacion as justificativo, SUM(a.tiempo) as tiempo,SUM(a.dias) as dias,SUM(a.horas) as horas,SUM(a.minutos) as minutos FROM dias_incapacidad as a,nompersonal as b WHERE a.cod_user=b.ficha GROUP BY nombre";
$res = $db->query($sql);
function justificacion($jus)
{
	if($jus==5)
	{
		return "Incapacidad";
	}
	if($jus==6)
	{
		return "Incapacidad por discapacidad";
	}
	if($jus==8)
	{
		return "Incapacidad por familiar discapacitado";
	}
}

//------------------------------------------------------------------------------------------------------------------------------------
$sql2="SELECT * FROM nomempresa";
$res2 = $db->query($sql2);
$empresa = mysqli_fetch_array($res2);
//--------------------------------------------------------------------------------------------------------------------------------------
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('America/Panama');

function fechas($fecha,$formato)
{
    $meses = array("ENERO","FEBRERO","MARZO","ABRIL","MAYO","JUNIO","JULIO","AGOSTO","SEPTIEMBRE","OCTUBRE","NOVIEMBRE","DICIEMBRE");
    $separa = explode("-",$fecha);
    $anio = $separa[0];
    $mes = $separa[1];
    $dia = $separa[2];
    switch ($formato)
    {
        case 1:
            // ejemplo = DEL 01 DE ENERO DE 2016
            $f = 'DEL '.$dia." DE ".$meses[$mes-1]. " DE ".$anio ;
            break;
        case 2:
            // ejemplo = DEL 01 DE ENERO DE 2016
                $f = $dia." DIAS DEL MES DE ".$meses[$mes-1]. " DE ".$anio ;            
            break;
        default:
            $f = date('Y');
            break;
    }
    return $f;
}
class MYPDF extends TCPDF
{   
    public function Header() 
    {
        
    }
    public function Footer()
    {

    }
}
# creamos  un nuevo objeto (MiPDF) utilizando la clase TCPDF
$MiPDF=new MYPDF('L','mm','LEGAL');
#desactivamos encabezado y pie de página
# el método SetAuthor nos permite incluir el nombre del autor
$MiPDF->SetAuthor('Pedro Martinez');
// set margins
$MiPDF->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
# el método SetTitle nos permite incluir un título
$MiPDF->SetTitle('Reporte planilla SICLAR');
# el método SetDisplayMode nos permite incluir palabras claves
# separadas por espacios y dentro de una misma cadena
$MiPDF->SetDisplayMode('fullpage','two');
# creamos una página en blanco. Incluimos, para esta primera página
# un cambio de orientación respecto a la inicial
$MiPDF->AddPage('L');

    $fill = FALSE;
    $border=0;
    $ln=0;
    $fill = 0;
    $align='L';
    $link=0;
    $stretch=1;
    $ignore_min_height=0;
    $calign='';
    $valign='';
    $height=6;
    $ruta_img='../../nomina/imagenes/';
    $MiPDF->SetFont('helvetica', 'B', 10);
    $MiPDF->Image($ruta_img.'ginteven_logo.jpg', $x='15', $y='10', $w=25, $h=20, $type='jpg', $link='', $align='', $resize=false, $dpi=300, $palign='', $ismask=false, $imgmask=false, $border='LTB', $fitbox=false, $hidden=false, $fitonpage=false);

    $MiPDF->Multicell(205,5,'','T',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='40',$y='10',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

    $MiPDF->Multicell(205,5,$empresa['nom_emp'],0,$alineacion='C',$fondo=false,$salto_de_linea=0,$x='40',$y='15',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

    $MiPDF->SetFont('times','', 9);
    $MiPDF->Multicell(205,5,'TIEMPO POR FUNCIONARIO',0,$alineacion='C',$fondo=false,$salto_de_linea=0,$x='40',$y='20',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

    $MiPDF->Multicell(205,5,'','B',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='40',$y='25',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

    $MiPDF->Image($ruta_img.'ginteven_logo.jpg', $x='245', $y='10', $w=25, $h=20, $type='jpg', $link='', $align='', $resize=false, $dpi=300, $palign='', $ismask=false, $imgmask=false, $border='TRB', $fitbox=false, $hidden=false, $fitonpage=false);
    $MiPDF->SetFont('helvetica', 'B', 10);

    $MiPDF->Multicell(65,10,"Usuario:".$_SESSION['nombre'].' '.date('h:m:s'),$borde=1,$alineacion='C',$fondo=false,$salto_de_linea=0,$x='275',$y='10',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='B', $fitcell=true);
    $MiPDF->Multicell(65,10,'Fecha de creacion:'. date('d-m-Y'),$borde=1,$alineacion='C',$fondo=false,$salto_de_linea=1,$x='275',$y='20',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='B', $fitcell=true);

    $MiPDF->Cell(0, 6,"", $border='',1, $align, $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->SetFont('helvetica', 'B',8);
    $MiPDF->Cell(90,8,"Nombre", $border='LTRB', $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(90,8,"Justificacion", $border='LTRB', $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(36,8,"Tiempo", $border='LTRB', $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(36,8,"Dias", $border='LTRB', $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(36,8,"Horas", $border='LTRB', $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(36,8,"Minutos", $border='TRB', 1, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $i=0;
    $res = $db->query($sql);
    while ($data = fetch_array($res))
    {
        $MiPDF->Cell(90,8,$data['nombre'], $border='LRB', $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(90,8,justificacion($data['justificativo']), $border='LRB', $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(36,8,$data['tiempo'], $border='LRB', $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(36,8,$data['tiempo']/8, $border='LRB', $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(36,8,$data['horas'], $border='LRB', $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(36,8,$data['minutos'], $border='LRB', 1, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $i=$i+1;
    }
    $MiPDF->SetFont('helvetica', 'B',9);
    $MiPDF->Cell(40,8,"TOTAL POSICIONES DISPONIBLES: ", $border='TB', $ln, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(20,8,$i, $border='TB', $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(130,8,"", $border='TB', $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);

    $MiPDF->Output('tiempo_funcionario.pdf','I');
?>