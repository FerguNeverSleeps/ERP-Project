<?php
session_start();
ob_start();

require('../nomina/tcpdf/tcpdf.php');
include("../nomina/lib/common.php");
date_default_timezone_set('America/Panama');
require_once('../nomina/lib/database.php');
$db = new Database($_SESSION['bd']);

//--------------------------------------------------------------------------------------------------------------------------------------
//consulta de datos
$sql="SELECT * FROM nomposicion WHERE nomposicion_id NOT IN(SELECT DISTINCT nomposicion_id FROM nompersonal)
GROUP BY nomposicion_id";
$res1 = $db->query($sql);
//--------------------------------------------------------------------------------------------------------------------------------------
$sql="SELECT * FROM nomempresa";
$res2 = $db->query($sql);
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
# incluyendo en este caso los valores a utilizar por el constructor
$MiPDF=new MYPDF('P','mm','LETTER');
#desactivamos encabezado y pie de página
# el método SetAuthor nos permite incluir el nombre del autor
$MiPDF->SetAuthor('Pedro Martinez');
# el método SetCreator nos permite incluir el nombre de la
# aplicacion que genera el pdf
// set margins
$MiPDF->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$MiPDF->SetCreator('clase TCPDF');
# el método SetTitle nos permite incluir un título
$MiPDF->SetTitle('Reporte de Vacantes');
# el método SetDisplayMode nos permite incluir palabras claves
# separadas por espacios y dentro de una misma cadena
$MiPDF->SetDisplayMode('fullpage','two');
# creamos una página en blanco. Incluimos, para esta primera página
# un cambio de orientación respecto a la inicial
$MiPDF->Addpage('H');

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
    $ruta_img='../nomina/imagenes/';
    $MiPDF->SetFont('helvetica', 'B', 10);
    $MiPDF->Image($ruta_img.$empresa['imagen_izq'], $x='15', $y='10', $w=25, $h=20, $type='png', $link='', $align='', $resize=false, $dpi=300, $palign='', $ismask=false, $imgmask=false, $border='LTB', $fitbox=false, $hidden=false, $fitonpage=false);
    $MiPDF->Multicell(85,5,$empresa['nom_emp'],'T',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='40',$y='10',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);
    $MiPDF->Multicell(85,5,'REPORTE DE VACANTES',0,$alineacion='C',$fondo=false,$salto_de_linea=0,$x='40',$y='15',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);
    $MiPDF->SetFont('times','', 9);
    $MiPDF->Multicell(85,5,'DIRECCION DE RECURSOS HUMANOS',0,$alineacion='C',$fondo=false,$salto_de_linea=0,$x='40',$y='20',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);
    $MiPDF->Multicell(85,5,'Departamento de Clasificacion y Retribucion de Puestos','B',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='40',$y='25',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);
  
    $MiPDF->SetFont('helvetica', 'B', 10);
    $MiPDF->Multicell(55,10,"Usuario:".$_SESSION['nombre'].' '.date('h:m:s'),$borde=1,$alineacion='C',$fondo=false,$salto_de_linea=0,$x='150',$y='10',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='B', $fitcell=true);
    $MiPDF->Multicell(55,10,'Fecha de creacion:'. date('d-m-Y'),$borde=1,$alineacion='C',$fondo=false,$salto_de_linea=1,$x='150',$y='20',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='B', $fitcell=true);

    $MiPDF->Cell(0, 12,"", $border='',1, $align, $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->SetFont('helvetica', 'B',9);
    $MiPDF->Cell(25,8,"N° POSICION", $border='TB', $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(35,8,"SUELDO PROPUESTO", $border='TB', $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(35,8,"SUELDO ANUAL", $border='TB', $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(30,8,"PARTIDA", $border='TB', $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(45,8,"CARGO", $border='TB', $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(20,8,"ESTADO",$border='TB',1,$align='C',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $i=0;
    while ($data = fetch_array($res1))
    {
        $sql="SELECT des_car FROM nomcargos WHERE cod_cargo = '".$data['cargo_id']."'";
        $res = $db->query($sql);
        $cargo = fetch_array($res);
        if (empty($cargo['des_car'])) {
            $cargo['des_car']='Sin definir cargo';
        }

        $MiPDF->Cell(25,8,$data['nomposicion_id'], $border='R', $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(35,8,$data['sueldo_propuesto'].' '.$empresa['moneda'], $border='R', $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(35,8,$data['sueldo_anual'].' '.$empresa['moneda'], $border='R', $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(30,8,$data['partida'], $border='R', $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(45,8,$cargo['des_car'], $border='R', $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $MiPDF->Cell(20,8,'Disponible',$border='',1,$align='C',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $i=$i+1;
    }
    $MiPDF->SetFont('helvetica', 'B',9);
    $MiPDF->Cell(40,8,"TOTAL POSICIONES DISPONIBLES: ", $border='TB', $ln, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(20,8,$i, $border='TB', $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(130,8,"", $border='TB', $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);

    $MiPDF->Output('posiciones_vacante_minsa_pdf.pdf','I');
?>