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
$sql = "SELECT DISTINCT b.Descripcion as descripcion, SUM(a.suesal) as saltotal,COUNT(a.personal_id) as todos FROM nompersonal AS a
LEFT JOIN departamento b ON a.IdDepartamento=b.IdDepartamento
WHERE a.estado NOT LIKE '%Baja%' AND a.codcat !=  '11' GROUP BY b.Descripcion";
$res1 = $db->query($sql);
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
    $ruta_img='../../includes/imagenes/';
    $MiPDF->SetFont('helvetica', 'B', 10);
    $MiPDF->Image($ruta_img.'logo_selectra1.png', $x='15', $y='10', $w=25, $h=20, $type='jpg', $link='', $align='', $resize=false, $dpi=300, $palign='', $ismask=false, $imgmask=false, $border='LTB', $fitbox=false, $hidden=false, $fitonpage=false);

    $MiPDF->Multicell(205,5,'','T',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='40',$y='10',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

    $MiPDF->Multicell(205,5,$empresa['nom_emp'],0,$alineacion='C',$fondo=false,$salto_de_linea=0,$x='40',$y='15',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

    $MiPDF->SetFont('times','', 9);
    $MiPDF->Multicell(205,5,'PLANILLA SICLAR',0,$alineacion='C',$fondo=false,$salto_de_linea=0,$x='40',$y='20',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

    $MiPDF->Multicell(205,5,'','B',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='40',$y='25',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

    $MiPDF->Image($ruta_img.'logo_selectra1.png', $x='245', $y='10', $w=25, $h=20, $type='jpg', $link='', $align='', $resize=false, $dpi=300, $palign='', $ismask=false, $imgmask=false, $border='TRB', $fitbox=false, $hidden=false, $fitonpage=false);
    $MiPDF->SetFont('helvetica', 'B', 10);

    $MiPDF->Multicell(65,10,"Usuario:".$_SESSION['nombre'].' '.date('h:m:s'),$borde=1,$alineacion='C',$fondo=false,$salto_de_linea=0,$x='275',$y='10',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='B', $fitcell=true);
    $MiPDF->Multicell(65,10,'Fecha de creacion:'. date('d-m-Y'),$borde=1,$alineacion='C',$fondo=false,$salto_de_linea=1,$x='275',$y='20',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='B', $fitcell=true);

    $MiPDF->Cell(0, 6,"", $border='',1, $align, $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->SetFont('helvetica', 'B',8);
    $MiPDF->Cell(108,8,"Descripcion", $border='LTRB', $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(108,8,"Total de Salarios", $border='TRB', $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(108,8,"Total de Colaboradores", $border='TRB',1, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);

    $i=0;
    $sueTot=$perTot=0.00;
    while ($data = fetch_array($res1))
    {
        $sueTot+=$data["saltotal"];
        $perTot+=$data["todos"];
        $fn = $data["fecnac"];
        $objetofn = date_create_from_format('Y-m-d',$fn);
        $nuevofn = date_format($objetofn,"d-m-Y");
        $fi = $data["fecing"];
        $objetofi = date_create_from_format('Y-m-d',$fi);
        $nuevofi = date_format($objetofi,"d-m-Y");
        $MiPDF->Cell(108,8,$data['descripcion'], $border='LR', $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(108,8,number_format($data['saltotal'],2), $border='R', $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(108,8,$data['todos'], $border='R', 1, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);

        $i=$i+1;
    }

    $MiPDF->Cell(108,8,'TOTALES', $border='LTR', $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(108,8,number_format($sueTot,2), $border='TR', $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(108,8,$perTot, $border='TR', 1, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);

    $MiPDF->SetFont('helvetica', 'B',9);
    $MiPDF->Cell(40,8,"TOTAL POSICIONES DISPONIBLES: ", $border='LTB', $ln, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(20,8,$i, $border='TB', $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(264,8,"", $border='TBR', $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    ob_clean();
    $MiPDF->Output('resumen_planilla_siclar.pdf','D');
?>
