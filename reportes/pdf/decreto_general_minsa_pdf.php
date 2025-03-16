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
$pd   = $_GET['pd'];
$nd   = $_GET['nd'];
$fecha_con =  date('d-m-Y');
$fechax = explode("-",$fecha_con);
$anio=$fechax[2];
$mes=$fechax[1];
$dia=$fechax[0];
$fecha=$anio.'-'.$mes.'-'.$dia;
$f=fechas($fecha_con,1);
$sql = "SELECT a.apenom,a.cedula,a.seguro_social,a.tipnom,a.suesal,b.des_car,c.nomposicion_id,c.partida,d.descrip FROM nompersonal AS a,nomcargos AS b,nomposicion as c, nomnivel1 as d WHERE a.codcargo = b.cod_car and a.nomposicion_id = c.nomposicion_id and a.codnivel1=d.codorg and a.num_decreto='$nd' ORDER BY d.descrip";
$res1 = $db->query($sql);

$sql2 = "SELECT pre_sid FROM nomempresa";
$res2 = $db->query($sql2);
$data2 = fetch_array($res2);
$ministro=strtoupper($data2['pre_sid']);

$sql3 = "SELECT a.num_decreto FROM nompersonal AS a WHERE a.num_decreto='$nd' LIMIT 1";
$res3 = $db->query($sql3);
$data3 = fetch_array($res3);
$num_decreto=strtoupper($data3['num_decreto']);

function fechas($fecha,$formato)
{
    $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
    $separa = explode("-",$fecha);
    $dia = $separa[0];
    $mes = $separa[1];
    $anio = $separa[2];
    switch ($formato)
    {
        case 1:
            // ejemplo = DEL 01 DE ENERO DE 2016
            $f = '( De '.$dia.' De '.$meses[$mes-1].' )';
            break;
        case 2:
            // ejemplo = DEL 01 DE ENERO DE 2016
                $f = $dia." dias del mes de ".$meses[$mes-1]. " de ".$anio ;            
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
$MiPDF=new MYPDF('P','mm','A4');
#desactivamos encabezado y pie de página
# el método SetAuthor nos permite incluir el nombre del autor
$MiPDF->SetAuthor('Aquiles Penott');
// set margins
$MiPDF->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
# el método SetTitle nos permite incluir un título
$MiPDF->SetTitle('Decreto');
# el método SetDisplayMode nos permite incluir palabras claves
# separadas por espacios y dentro de una misma cadena
$MiPDF->SetDisplayMode('fullpage','two');
# creamos una página en blanco. Incluimos, para esta primera página
# un cambio de orientación respecto a la inicial
$MiPDF->AddPage('P');
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

$MiPDF->SetFont('times','', 10);
$MiPDF->Multicell(350,5,'P.D.',0,$alineacion='C',$fondo=false,$salto_de_linea=0,$x='0',$y='10',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(360,5,$pd,0,$alineacion='C',$fondo=false,$salto_de_linea=0,$x='0',$y='10',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->SetFont('times','', 12);
$MiPDF->Multicell(210,5,'Decreto Numero '.$nd.' de '.$anio,0,$alineacion='C',$fondo=false,$salto_de_linea=0,$x='0',$y='20',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(210,5,$f,0,$alineacion='C',$fondo=false,$salto_de_linea=0,$x='0',$y='25',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->SetFont('times','', 12);

$MiPDF->Multicell(210,5,'Por medio del cual se efectuan Nombramientos Permanentes en el',0,$alineacion='C',$fondo=false,$salto_de_linea=0,$x='0',$y='30',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->SetFont('times','B', 12);

$MiPDF->Multicell(210,5,'Ministerio de Salud',0,$alineacion='C',$fondo=false,$salto_de_linea=0,$x='0',$y='35',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->SetFont('times','B', 12);

$MiPDF->Multicell(210,5,'EL PRESIDENTE DE LA REPUBLICA',0,$alineacion='C',$fondo=false,$salto_de_linea=0,$x='0',$y='55',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->SetFont('times','', 12);

$MiPDF->Multicell(210,5,'en uso de sus facultades constitucionales y legales',0,$alineacion='C',$fondo=false,$salto_de_linea=1,$x='0',$y='60',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(210,5,'DECRETA:',0,$alineacion='C',$fondo=false,$salto_de_linea=0,$x='0',$y='75',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$MiPDF->Multicell(210,5,'',0,$alineacion='C',$fondo=false,$salto_de_linea=1,$x='0',$y='80',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);

$i=1;
$region=NULL;
while ($data = fetch_array($res1))
{
	if($region!=$data['descrip'])
	{
		$MiPDF->SetFont('times','B', 12);
		$str0 = strtoupper($data['descrip']);
		$MiPDF->Multicell(210,5,$str0,0,$alineacion='C',$fondo=false,$salto_de_linea=1,$x='0',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);
		$MiPDF->Multicell(30,5,'',0,$alineacion='R',$fondo=false,$salto_de_linea=1,$x='20',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);
	}
	$MiPDF->SetFont('times','B', 12);
	$str = strtoupper($data['apenom']);
	$MiPDF->Multicell(30,5,'Articulo '.$i.':',0,$alineacion='R',$fondo=false,$salto_de_linea=0,$x='20',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=50,$alineacion_vertical='T', $fitcell=false);
	$MiPDF->SetFont('times','', 12);
	$MiPDF->Multicell(120,5,'Nombrar Permanente a '.$str.' cedula N° '.$data['cedula'].', seguro social N° '.$data['seguro_social'].', como '.$data['des_car'].' posicion N° '.$data['nomposicion_id'].' planilla '.$data['tipnom'].' con un sueldo mensual de B/. '.$data['suesal'].' (Partida Presupuestaria N° '.$data['partida'].')',0,$alineacion='J',$fondo=false,$salto_de_linea=1,$x='50',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=50,$alineacion_vertical='T', $fitcell=false);
	$MiPDF->Multicell(30,5,'',0,$alineacion='R',$fondo=false,$salto_de_linea=1,$x='20',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);
    $region=$data['descrip'];
	$i++;
}
$MiPDF->SetFont('times','B', 12);
$MiPDF->Multicell(30,5,'Paragrafo: ',0,$alineacion='R',$fondo=false,$salto_de_linea=0,$x='21',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=50,$alineacion_vertical='T', $fitcell=false);
$MiPDF->SetFont('times','', 12);
$MiPDF->Multicell(120,5,'Para los efectos fiscales, estos nombramientos regirán a partir de la Toma de Posesión.                             ',0,$alineacion='J',$fondo=false,$salto_de_linea=1,$x='50',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=50,$alineacion_vertical='T', $fitcell=false);
$MiPDF->Multicell(30,5,'',0,$alineacion='R',$fondo=false,$salto_de_linea=1,$x='20',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);
$MiPDF->SetFont('times','B', 12);
$MiPDF->Multicell(120,5,'COMUNIQUESE Y CÚMPLASE',0,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='28',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=50,$alineacion_vertical='T', $fitcell=false);
$MiPDF->Multicell(30,5,'',0,$alineacion='R',$fondo=false,$salto_de_linea=1,$x='20',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);
$MiPDF->SetFont('times','', 12);
$hoy=date("d-m-Y");
$f=fechas($hoy,2);
$MiPDF->Multicell(140,5,'Dado en la Ciudad de Panamá, a los '.$f,0,$alineacion='L',$fondo=false,$salto_de_linea=1,$x='28',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=50,$alineacion_vertical='T', $fitcell=false);
$MiPDF->SetFont('times','B', 12);
$MiPDF->Multicell(120,25,'',0,$alineacion='C',$fondo=false,$salto_de_linea=1,$x='0',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=50,$alineacion_vertical='T', $fitcell=false);
$MiPDF->Multicell(210,5,'JUAN CARLOS VALERA R.',0,$alineacion='C',$fondo=false,$salto_de_linea=1,$x='0',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=50,$alineacion_vertical='T', $fitcell=false);
$MiPDF->SetFont('times','', 12);
$MiPDF->Multicell(210,5,'Presidente de la República',0,$alineacion='C',$fondo=false,$salto_de_linea=1,$x='0',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=50,$alineacion_vertical='T', $fitcell=false);
$MiPDF->Multicell(120,25,'',0,$alineacion='C',$fondo=false,$salto_de_linea=1,$x='0',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=50,$alineacion_vertical='T', $fitcell=false);
$MiPDF->Multicell(120,25,'',0,$alineacion='C',$fondo=false,$salto_de_linea=1,$x='0',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=50,$alineacion_vertical='T', $fitcell=false);
$MiPDF->SetFont('times','B', 12);
$MiPDF->Multicell(210,5,$ministro,0,$alineacion='C',$fondo=false,$salto_de_linea=1,$x='0',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=50,$alineacion_vertical='T', $fitcell=false);
$MiPDF->Multicell(210,5,'Ministro de Salud',0,$alineacion='C',$fondo=false,$salto_de_linea=1,$x='0',$y='',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=50,$alineacion_vertical='T', $fitcell=false);
$MiPDF->Output('resumen_decreto.pdf','I');
?>