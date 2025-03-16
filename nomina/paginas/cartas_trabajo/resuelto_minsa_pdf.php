<?php
if(!isset($_SESSION)) session_start();

require_once('../../lib/database.php');
require ('numeros_letras_class.php');
$db = new Database($_SESSION['bd']);

$ficha=$_GET['ficha'];
$tipnom=$_GET['tipnom'];
//--------------------------------------------------------------------------------------------------------------------------------------
//consulta de datos
$sql="SELECT a.*,b.des_car,b.gremio FROM nompersonal AS a,nomcargos AS b WHERE a.ficha = '$ficha' AND a.codcargo = b.cod_car";
$res2 = $db->query($sql);
$data = mysqli_fetch_array($res2);

$sql="SELECT a.descrip FROM nomnivel1 AS a WHERE a.codorg = '".$data['codnivel1']."'";
$res2 = $db->query($sql);
$partida = mysqli_fetch_array($res2);

$sql="SELECT * FROM nomposicion WHERE nomposicion_id = '".$data['nomposicion_id']."'";
$res2 = $db->query($sql);
$transitorio = mysqli_fetch_array($res2);
//--------------------------------------------------------------------------------------------------------------------------------------
$sql="SELECT * FROM nomempresa";
$res2 = $db->query($sql);
$empresa = mysqli_fetch_array($res2);
//--------------------------------------------------------------------------------------------------------------------------------------
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('America/Panama');

require('vendor/tcpdf/tcpdf.php');
require_once('../../lib/database.php');

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
$MiPDF->SetTitle('Reporte de Salarios');
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
    $height=6;//alto de cada columna
    $MiPDF->SetFont('helvetica', 'B', 14);
    $MiPDF->Cell(0, 6,"RESUELTO DE PERSONAL #".$data['num_decreto'], 0, 1,'C');
    setlocale(LC_TIME, 'es_ES.UTF-8');
    if(isset($data['fecha_decreto']))
    {
        $fecha_decreto = fechas($data['fecha_decreto'],1);
    }else
    {
        $fecha_decreto='Sin definir';
    }
    $MiPDF->Cell(0, 6,$fecha_decreto, 0, 1,'C');
    $MiPDF->Cell(0, 6,"", 0, 1,'C');
    $MiPDF->Cell(0, 6,"POR EL CUAL SE REALIZA NOMBRAMIENTO DE PERSONAL", 0, 1,'C');
    $MiPDF->Cell(0, 6,"CON CARACTER EVENTUAL EN EL MINISTERIO DE SALUD", 0, 1,'C');
    $MiPDF->Cell(0, 6,"", 0, 1,'C');
    $MiPDF->Cell(0, 6,strtoupper($empresa['nom_emp']), 0, 1,'C');
    $MiPDF->Cell(0, 6,"EN USO DE SUS FACULTADES LEGALES", 0, 1,'C');
    $MiPDF->Cell(0, 6,"RESUELVE:", 0, 1,'C');
    $MiPDF->Cell(0, 18,"", 0, 1,'C');
    $MiPDF->SetFont('helvetica', 'B', 12);
    $MiPDF->Cell(0, 6,"ARTICULO PRIMERO: NOMBRESE CON CARACTER EVENTUAL AL SIGUIENTE PERSONAL", 0, 1,'C');
    $MiPDF->Cell(0, 12,"", 0, 1,'C');
    $MiPDF->Cell(40, 6,"PLANILLA: ".$data['tipnom'], 0, 1,'L');
    $MiPDF->Cell(30, 6,"REGION:", 0, 0,'L');
    $MiPDF->Cell(75, 6,$partida['descrip'], 0, 0,'L');
    if ($data['gremio'] == 1) {
        $gremio="T&G";
    }else{
        $gremio="ADM";
    }
    $MiPDF->Cell(75, 6,$gremio, 0, 1,'R');
    $MiPDF->Cell(0, 6,"", 0, 1,'C');


    $MiPDF->SetFont('helvetica', 'B', 10);
    $MiPDF->Cell(30, 6,"NOMBRE", $border='B', $ln, $align, $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(30, 6,"APELLIDO", $border='B', $ln, $align, $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(20, 6,"CODIGO", $border='B', $ln, $align, $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(30, 6,"CARGO", $border='B', $ln, $align, $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(20, 6,"SALARIO", $border='B', $ln, $align, $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(20, 6,"POSICION",$border='B',$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(20, 6,"CEDULA", $border='B',$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(20, 6,"SEGURO", $border='B',1,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);

    $MiPDF->Cell(30, 6,$data['nombres'], $border='', $ln, $align, $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(30, 6,$data['apellidos'], $border='', $ln, $align, $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(20, 6,$data['codcargo'], $border='', $ln, $align, $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(30, 6,$data['des_car'], $border='', $ln, $align, $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(20, 6,$data['suesal'], $border='', $ln, $align, $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(20, 6,$data['nomposicion_id'],$border='',$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(20, 6,$data['cedula'], $border='',$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(20, 6,$data['seguro_social'], $border='',1,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);

    $MiPDF->Cell(30, 6,'', $border='B', $ln, $align, $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(32, 6,'TOTAL', $border='B', $ln, $align, $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(20, 6,$empresa['moneda'], $border='B', $ln, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(30, 6,'', $border='B', $ln, $align, $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(20, 6,$data['suesal'], $border='B', $ln, $align, $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(20, 6,'1',$border='B',$ln,$align='C',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(20, 6,'', $border='B',$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(18, 6,'', $border='B',1,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);

    $MiPDF->Cell(0, 6,"", 0, 1,'C');

    $MiPDF->Cell(60, 6,"PARTIDA TRANSITORIO No.", 0, 0,'L');
    if(isset($transitorio['partida'])){$par=$transitorio['partida'];}else{$par='Sin definir';}
    $MiPDF->Cell(130, 6,$par, 0, 1,'L');
    $MiPDF->Cell(0, 6,"", 0, 1,'C');

	$MiPDF->SetFont('helvetica', 'B', 12);

    if(isset($data['observaciones'])){$obs=$data['observaciones'];}else{$obs='Sin definir';}

    $MiPDF->Multicell(0,12,$obs,$borde='',$alineacion='L',$fondo=false,$salto_de_linea=1,$x='',$y='',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='B', $fitcell=true);
    $MiPDF->Cell(0, 6,"", 0, 1,'C');
    $MiPDF->Cell(0, 6,"CUMPLASE", 0, 1,'L');
    $MiPDF->Cell(0, 6,"DADO EN LA CIUDAD DE PANAMA A LOS ".$fecha_decreto, 0, 1,'L');
    $MiPDF->Cell(0, 6,"", 0, 1,'C');
    $MiPDF->Cell(0, 6,"", 0, 1,'C');
    $MiPDF->Cell(0, 6,"_________________________________", 0, 1,'C');
    if(isset($empresa['pre_sid'])){$firma=$empresa['pre_sid'];}else{$firma='Sin definir';}
    $MiPDF->Cell(0, 6,$firma, 0, 1,'C');
    $MiPDF->Cell(0, 6,"MINISTRO DE SALUD", 0, 1,'C');

	$MiPDF->Output('resuelto_minsa_pdf.pdf','I');
?>