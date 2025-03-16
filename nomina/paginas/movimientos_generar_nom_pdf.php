<?php
session_start();
ob_start();

$codigo = $_GET["nomina"];
require('../../nomina/tcpdf/tcpdf.php');
include("../../nomina/lib/common.php");
date_default_timezone_set('America/Panama');
require_once('../../nomina/lib/database.php');
$db = new Database($_SESSION['bd']);
//--------------------------------------------------------------------------------------------------------------------------------------
//consulta de datos
$sql="SELECT DISTINCT a.ficha as ficha,
        b.apenom as apenom,
        a.cedula as cedula,
        b.seguro_social as segurosocial,
        b.nomposicion_id as posicion,
        a.unidad as unidad,
        a.codcon as codcon,
        a.valor as valor,
        a.monto as monto,
        a.codnom as codnom,
        c.codtip as codtip,
        b.clave_ir as claveir,
        b.lugarnac as lugarnac,
        d.codorg as codorg
        FROM nom_movimientos_nomina as a, nompersonal as b, nomtipos_nomina as c, nomnivel1 as d WHERE a.codnivel1=d.codorg AND c.codnom=a.codnom AND a.ficha=b.ficha AND a.codnom='$codigo' GROUP BY a.ficha";

$result1 = $db->query($sql);

//------------------------------------------------------------
  //-----------------------------------------------------
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('America/Panama');

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
$MiPDF->SetTitle('Planilla de empleado por resuelto');
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
    $pag=1;
    $i=0;

    $MiPDF->Multicell(65,10,'Fecha: '. date('d/m/Y'),$borde=0,$alineacion='C',$fondo=false,$salto_de_linea=1,$x='0',$y='0',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='B', $fitcell=true);
    $MiPDF->Multicell(65,10,'REPUBLICA DE PANAMA',$borde=0,$alineacion='C',$fondo=false,$salto_de_linea=1,$x='150',$y='0',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='B', $fitcell=true);
    $MiPDF->Multicell(65,10,'Pagina No. '. $pag,$borde=0,$alineacion='C',$fondo=false,$salto_de_linea=1,$x='280',$y='0',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='B', $fitcell=true);
    $MiPDF->Multicell(65,10,'MINISTERIO DE SALUD',$borde=0,$alineacion='C',$fondo=false,$salto_de_linea=1,$x='150',$y='5',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='B', $fitcell=true);
    $MiPDF->Multicell(150,10,'PLANTILLA DE EMPLEADOS POR RESUELTO',$borde=0,$alineacion='C',$fondo=false,$salto_de_linea=1,$x='107',$y='10',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='B', $fitcell=true);

    $MiPDF->Cell(0, 6,"", $border='',1, $align, $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->SetFont('helvetica', 'B',8);
    $MiPDF->Cell(40,8,"NOMBRE", $border='T', $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(20,8,"CEDULA", $border='T', $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(15,8,"SEGURO", $border='T', $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(15,8,"PROV", $border='T', $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(15,8,"PLAN", $border='T', $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(15,8,"POSICION", $border='T', $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(10,8,"CLAVE",$border='T',$ln,$align='C',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(20,8,"SUELDO", $border='T', $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(20,8,"SEGURO", $border='T', $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(20,8,"IMPUESTO", $border='T', $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(20,8,"SEGURO", $border='T', $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(102,8,"OTRAS DEDUCCIONES",$border='T',$ln,$align='C',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(20,8,"SUELDO",$border='T',1,$align='C',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    //Salto de linea para segundos elementos
    $MiPDF->Cell(40,8,"", $border='B', $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(20,8,"", $border='B', $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(15,8,"SOC #", $border='B', $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(15,8,"", $border='B', $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(15,8,"", $border='B', $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(15,8,"", $border='B', $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(10,8,"I/R",$border='B',$ln,$align='C',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(20,8,"BRUTO", $border='B', $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(20,8,"SOCIAL", $border='B', $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(20,8,"S/RENTA", $border='B', $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(20,8,"EDUCATIVO", $border='B', $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(17,8,"CLA",$border='B',$ln,$align='C',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(17,8,"VALOR",$border='B',$ln,$align='C',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(17,8,"CLA",$border='B',$ln,$align='C',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(17,8,"VALOR",$border='B',$ln,$align='C',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(17,8,"CLA",$border='B',$ln,$align='C',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(17,8,"VALOR",$border='B',$ln,$align='C',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(20,8,"NETO",$border='B',1,$align='C',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);

    while ($data = fetch_array($result1))
    {  
        $ficha=$data['ficha'];
        //Salario
        $sqlnom = "SELECT SUM(monto) AS monto from nom_movimientos_nomina WHERE ficha='$ficha' AND codnom='$codigo' AND codcon=100";
        $resnom = $db->query($sqlnom);
        $datanom = fetch_array($resnom);
        $salario=$datanom['monto'];
        //Seguro Social
        $sqlnom = "SELECT SUM(monto) AS monto from nom_movimientos_nomina WHERE ficha='$ficha' AND codnom='$codigo' AND codcon=200";
        $resnom = $db->query($sqlnom);
        $datanom = fetch_array($resnom);
        $ss=$datanom['monto'];
        //Seguro educativo
        $sqlnom = "SELECT SUM(monto) AS monto from nom_movimientos_nomina WHERE ficha='$ficha' AND codnom='$codigo' AND codcon=201";
        $resnom = $db->query($sqlnom);
        $datanom = fetch_array($resnom);
        $se=$datanom['monto'];
        //Impuesto Sobre la Renta
        $sqlnom = "SELECT SUM(monto) AS monto from nom_movimientos_nomina WHERE ficha='$ficha' AND codnom='$codigo' AND codcon=202";
        $resnom = $db->query($sqlnom);
        $datanom = fetch_array($resnom);
        $islr=$datanom['monto'];
        //Siacap
        $sqlnom = "SELECT SUM(monto) AS monto from nom_movimientos_nomina WHERE ficha='$ficha' AND codnom='$codigo' AND codcon=204";
        $resnom = $db->query($sqlnom);
        $datanom = fetch_array($resnom);
        $siacap=$datanom['monto'];
    
        $total=$salario-$ss-$se-$islr-$siacap;

        //NOMBRE
        $MiPDF->Cell(40,8,$data['apenom'], $border='', $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        //CEDULA
        $MiPDF->Cell(20,8,$data['cedula'], $border='', $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        //SEGURO SOC #
        $MiPDF->Cell(15,8,$data['segurosocial'], $border='', $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        //PROV
        $MiPDF->Cell(15,8,$data['codorg'], $border='', $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        //PLAN
        $MiPDF->Cell(15,8,$data['codtip'], $border='', $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        //POSICION
        $MiPDF->Cell(15,8,$data['posicion'],$border='',$ln,$align='C',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        //CLAVE I/R
        $MiPDF->Cell(10,8,$data['claveir'], $border='', $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        //SUELDO BRUTO
        $MiPDF->Cell(20,8,$salario, $border='', $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        //-----------
        //SEGURO SOCIAL
        $MiPDF->Cell(20,8,$ss, $border='', $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        //IMPUESTO S/RENTA
        $MiPDF->Cell(20,8,$islr, $border='', $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        //SEGURO EDUCATIVO
        $MiPDF->Cell(20,8,$se,$border='',$ln,$align='C',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        //OTRAS DEDUCCIONES
        //CLA
        $MiPDF->Cell(17,8,"SIACAP",$border='',$ln,$align='C',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        //VALOR
        $MiPDF->Cell(17,8,$siacap,$border='',$ln,$align='C',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        //CLA
        $MiPDF->Cell(17,8,"",$border='',$ln,$align='C',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        //VALOR
        $MiPDF->Cell(17,8,"",$border='',$ln,$align='C',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        //CLA
        $MiPDF->Cell(17,8,"",$border='',$ln,$align='C',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        //VALOR
        $MiPDF->Cell(17,8,"",$border='',$ln,$align='C',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        //SUELDO NETO
        $MiPDF->Cell(20,8,$total,$border='',1,$align='C',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $i++;
    }
    $MiPDF->SetFont('helvetica', 'B',9);
    $MiPDF->Cell(40,8,"TOTAL POSICIONES DISPONIBLES: ", $border='TB', $ln, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(20,8,$i, $border='TB', $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(272,8,"", $border='TB', $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);

    $MiPDF->Output('planilla_adicional.pdf','I');
?>
