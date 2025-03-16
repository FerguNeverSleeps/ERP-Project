<?php
session_start();
ob_start();

require('../../nomina/tcpdf/tcpdf.php');
include("../../nomina/lib/common.php");
date_default_timezone_set('America/Panama');
require_once('../../nomina/lib/database.php');
$db = new Database($_SESSION['bd']);

error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('America/Panama');

//datos recibidos
$pd = $_GET['pd'];
$nd = $_GET['nd'];
//--------------------------------------------------------------------------------------------------------------------------------------
//consulta de datos
$sql="SELECT a.*,b.des_car,b.gremio,c.descrip,d.* FROM nompersonal AS a,nomcargos AS b,nomnivel1 as c,nomposicion as d WHERE a.num_decreto = '$nd' AND a.codcargo = b.cod_car AND c.codorg=a.codnivel1 AND a.nomposicion_id=d.nomposicion_id";
$res = $db->query($sql);


$sql1="SELECT DISTINCT a.*,b.* FROM nompersonal as a,nomcargos as b WHERE a.num_decreto = '$nd' AND a.codcargo=b.cod_car";
$res1 = $db->query($sql1);
$decreto = mysqli_fetch_array($res1);

$sql2="SELECT a.descrip FROM nomnivel1 AS a WHERE a.codorg = '".$decreto['codnivel1']."'";
$res2 = $db->query($sql2);
$partida = mysqli_fetch_array($res2);

$sql3="SELECT * FROM nomposicion WHERE nomposicion_id = '".$decreto['nomposicion_id']."'";
$res3 = $db->query($sql3);
$transitorio = mysqli_fetch_array($res3);

//--------------------------------------------------------------------------------------------------------------------------------------
$sql4="SELECT * FROM nomempresa";
$res4 = $db->query($sql4);
$empresa = mysqli_fetch_array($res4);
//--------------------------------------------------------------------------------------------------------------------------------------

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
    $MiPDF->Cell(0, 6,"RESUELTO DE PERSONAL #".$pd, 0, 1,'C');
    setlocale(LC_TIME, 'es_ES.UTF-8');
    if(isset($decreto['fecha_decreto']))
    {
        $fecha_decreto_inicio = fechas($decreto['fecha_decreto'],1);
        $fecha_decreto_fin    = fechas($decreto['fecha_decreto_baja'],2);
    }else
    {
        $fecha_decreto_inicio = 'Sin definir';
        $fecha_decreto_fin    = 'Sin definir';
    }
    $MiPDF->Cell(0, 6,$fecha_decreto_inicio, 0, 1,'C');
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
    $MiPDF->Cell(40, 6,"PLANILLA: ".$decreto['tipnom'], 0, 1,'L');
    $MiPDF->Cell(30, 6,"REGION:", 0, 0,'L');
    $MiPDF->Cell(75, 6,$partida['descrip'], 0, 0,'L');
    if ($decreto['gremio'] == 1) {
        $gremio="T&G";
    }else{
        $gremio="ADM";
    }
    $MiPDF->Cell(75, 6,$gremio, 0, 1,'R');
    $MiPDF->Cell(0, 6,"", 0, 1,'C');


    $MiPDF->SetFont('helvetica', 'B', 8);
    $MiPDF->Cell(30, 6,"NOMBRE", $border='B', $ln, $align, $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(30, 6,"APELLIDO", $border='B', $ln, $align, $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(20, 6,"CODIGO", $border='B', $ln, $align, $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(20, 6,"CARGO", $border='B', $ln, $align, $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(20, 6,"SALARIO", $border='B', $ln, $align, $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(20, 6,"SOBRESUELDOS", $border='B', $ln, $align, $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(10, 6,"POSICION",$border='B',$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(20, 6,"CEDULA", $border='B',$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(20, 6,"SEGURO", $border='B',1,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $total=0;
    while($data = mysqli_fetch_array($res)){
    $MiPDF->Cell(30, 6,$data['nombres'], $border='', $ln, $align, $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(30, 6,$data['apellidos'], $border='', $ln, $align, $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(20, 6,$data['codcargo'], $border='', $ln, $align, $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(20, 6,$data['des_car'], $border='', $ln, $align, $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(20, 6,$data['suesal'], $border='', $ln, $align, $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(20, 6,$data['sueldopro'], $border='', $ln, $align, $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(10, 6,$data['nomposicion_id'],$border='',$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(20, 6,$data['cedula'], $border='',$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(20, 6,$data['seguro_social'], $border='',1,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $total=$total+$data['suesal'];
    }
    $MiPDF->Cell(30, 6,'', $border='B', $ln, $align, $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(32, 6,'TOTAL', $border='B', $ln, $align, $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(20, 6,$empresa['moneda'], $border='B', $ln, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(30, 6,'', $border='B', $ln, $align, $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(20, 6,$total, $border='B', $ln, $align, $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(20, 6,'',$border='B',$ln,$align='C',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(20, 6,'', $border='B',$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $MiPDF->Cell(18, 6,'', $border='B',1,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);

    $MiPDF->Cell(0, 6,"", 0, 1,'C');

    $MiPDF->Cell(75, 6,"PARTIDA TRANSITORIA No.", 0, 0,'L');
    if(isset($transitorio['partida'])){$par=$transitorio['partida'];}else{$par='Sin definir';}
    $MiPDF->Cell(75, 6,$par, 0, 1,'L');

    $MiPDF->Cell(75, 6,"PARTIDA - OTROS SOBRESUELDOS", 0, 0,'L');
    if(isset($transitorio['partida'])){$par=$transitorio['partida'];}else{$par='Sin definir';}
    $MiPDF->Cell(75, 6,$par, 0, 1,'L');

    $MiPDF->SetFont('helvetica', 'B', 12);

    $MiPDF->Cell(0, 6,"", 0, 1,'C');
    $MiPDF->Cell(0, 6,"PARAGRAFO: PARA LOS EFECTOS FISCALES, ESTE RESUELTO TENDRA VIGENCIA A PARTIR", 0, 1,'L');
    $MiPDF->Cell(0, 6,"                         ".$fecha_decreto_inicio." HASTA ".$fecha_decreto_fin, 0, 1,'L');
    $MiPDF->Cell(0, 6,"CUMPLASE", 0, 1,'L');
    $MiPDF->Cell(0, 6,"DADO EN LA CIUDAD DE PANAMA A LOS ".$fecha_decreto_inicio, 0, 1,'L');
    $MiPDF->Cell(0, 6,"", 0, 1,'C');
    $MiPDF->Cell(0, 6,"", 0, 1,'C');
    $MiPDF->Cell(0, 6,"_________________________________", 0, 1,'C');
    if(isset($empresa['pre_sid'])){$firma=$empresa['pre_sid'];}else{$firma='Sin definir';}
    $MiPDF->Cell(0, 6,$firma, 0, 1,'C');
    $MiPDF->Cell(0, 6,"MINISTRO DE SALUD", 0, 1,'C');

    $MiPDF->Output('resuelto_minsa_pdf.pdf','I');
?>