<?php
// Include the main TCPDF library (search for installation path).
require('vendor/tcpdf/tcpdf.php');
date_default_timezone_set('America/Panama');

setlocale(LC_MONETARY, 'en_GB');

require_once('../../lib/database.php');
require ('numeros_letras_class.php');
$db = new Database($_SESSION['bd']);
// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT,'LEGAL', true, 'UTF-8', false);

$ficha=$_GET['ficha'];
$tipnom=$_GET['tipnom'];
//--------------------------------------------------------------------------------------------------------------------------------------
//consulta de datos

$sql="SELECT a.*,b.des_car FROM nompersonal AS a,nomcargos AS b WHERE a.ficha = '$ficha' AND a.codcargo = b.cod_car";
$res2 = $db->query($sql);
$data = mysqli_fetch_array($res2);

$sql="SELECT des_car FROM nomcargos WHERE cod_car =".$data['codcargo'];
$res2 = $db->query($sql);
$cargo = mysqli_fetch_array($res2);

$sql="SELECT Descripcion FROM departamento WHERE IdDepartamento =".$data['IdDepartamento'];
$res2 = $db->query($sql);
$dpto = mysqli_fetch_array($res2);

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
        case 3:
            // ejemplo = DEL 01 DE ENERO DE 2016
                $f = array('dia' => $dia,
                                'mes' => $mes,
                                'anio'=> $anio);
            break;
        case 4:
            // ejemplo = DEL 01 DE ENERO DE 2016
            if (!empty($fecha)){
                $f = array('dia' => $dia,
                                'mes' => $meses[$mes-1],
                                'anio'=> $anio);
            }else{
                $f = array('dia' => " ",
                                'mes' => " ",
                                'anio'=> " ");
            }
            break;
        default:
            $f = $separa;
            break;
    }
    return $f;
}
function nombres($nombres)
{
    $separa = explode(" ",$nombres);
    $nombres = array('primer' => $separa[0],
                     'segundo' => $separa[1]);
    return $nombres;
}
function validar($dato)
{
    if( !empty($dato) )
    {
        return $dato;
    }
    else
    {
        return ' '.$dato;
    }
}
//--------------------------------------------------------------------------------------------------------------------------------------
// set document information
// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
    require_once(dirname(__FILE__).'/lang/eng.php');
    $pdf->setLanguageArray($l);
}
// ---------------------------------------------------------
// set font
$pdf->SetFont('times', 'B', 10);
// add a page
$pdf->AddPage();
// set cell padding
$pdf->setCellPaddings(0,0,0,0);
// set cell margins
$pdf->setCellMargins(0,0,0,0);
// set color for background
$pdf->SetFillColor(255, 255, 127);
// set some text for example
$ruta_img='../../imagenes/';
$pdf->Image($ruta_img.$empresa['imagen_izq'], $x='10', $y='10', $w=25, $h=20, $type='jpg', $link='', $align='', $resize=false, $dpi=300, $palign='', $ismask=false, $imgmask=false, $border='LTB', $fitbox=false, $hidden=false, $fitonpage=false);
$pdf->Multicell(85,5,'MINISTERIO DE SALUD','T',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='35',$y='10',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);
$pdf->Multicell(85,5,'ACCION DE PERSONAL',0,$alineacion='C',$fondo=false,$salto_de_linea=0,$x='35',$y='15',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);
$pdf->SetFont('times','', 9);
$pdf->Multicell(85,5,'DIRECCION DE RECURSOS HUMANOS',0,$alineacion='C',$fondo=false,$salto_de_linea=0,$x='35',$y='20',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);
$pdf->Multicell(85,5,'Departamento de Clasificacion y Retribucion de Puestos','B',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='35',$y='25',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=20,$alineacion_vertical='T', $fitcell=false);
$pdf->Image($ruta_img.$empresa['imagen_izq'], $x='120', $y='10', $w=25, $h=20, $type='jpg', $link='', $align='', $resize=false, $dpi=300, $palign='', $ismask=false, $imgmask=false, $border='TRB', $fitbox=false, $hidden=false, $fitonpage=false);
$pdf->Multicell(55,10,"#".$data['num_decreto'],$borde=1,$alineacion='C',$fondo=false,$salto_de_linea=0,$x='145',$y='10',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(55,10,'Numero correlativo de la accion y Fecha',$borde=1,$alineacion='C',$fondo=false,$salto_de_linea=0,$x='145',$y='20',$reseth=true,$ajuste_horizontal=0,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='T', $fitcell=true);


$pdf->Multicell(35,10,'  UNIDAD EJECUTORA:',$borde='L',$alineacion='L',$fondo=false,$salto_de_linea=0,$x='10',$y='30',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(95,10,$partida['descrip'],$borde='B',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='45',$y='30',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='B', $fitcell=true);

$pdf->Multicell(20,10,'fecha de la solicitud',$borde='LRB',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='145',$y='30',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='B', $fitcell=true);

$fecha=fechas($data['sfecing'],4);
$pdf->Multicell(10,5,$fecha['dia'],$borde='B',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='165',$y='30',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(15,5,$fecha['mes'],$borde='LB',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='175',$y='30',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(10,5,$fecha['anio'],$borde='LRB',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='190',$y='30',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);

$pdf->Multicell(10,5,'Dia',$borde='B',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='165',$y='35',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(15,5,'Mes',$borde='LB',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='175',$y='35',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(10,5,'Año',$borde='LRB',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='190',$y='35',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);

$pdf->Multicell(35,10,'  DEPARTAMENTO:',$borde='L',$alineacion='L',$fondo=false,$salto_de_linea=0,$x='10',$y='40',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(95,10,$dpto['Descripcion'],$borde='B',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='45',$y='40',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(20,10,'SERVICIO',$borde=0,$alineacion='C',$fondo=false,$salto_de_linea=0,$x='140',$y='40',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(40,10,'',$borde='RB',$alineacion='L',$fondo=false,$salto_de_linea=0,$x='160',$y='40',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='B', $fitcell=true);

$pdf->Multicell(190,5,'',$borde='LRB',$alineacion='L',$fondo=false,$salto_de_linea=0,$x='10',$y='45',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='B', $fitcell=true);

$pdf->Multicell(190,5,'  SOLICITUD DE ACCION',$borde='LR',$alineacion='L',$fondo=false,$salto_de_linea=0,$x='10',$y='55',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);

$pdf->Multicell(190,90,'',$borde='R',$alineacion='L',$fondo=false,$salto_de_linea=0,$x='10',$y='55',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=90,$alineacion_vertical='B', $fitcell=true);

$pdf->Multicell(5,10,'',$borde='L',$alineacion='L',$fondo=false,$salto_de_linea=0,$x='10',$y='60',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='B', $fitcell=true);

//FILA #1 SECCION 2----------------------------------------------------------------------------
$pdf->Multicell(5,5,'X',$borde=1,$alineacion='C',$fondo=false,$salto_de_linea=0,$x='15',$y='65',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(5,5,'X',$borde=1,$alineacion='C',$fondo=false,$salto_de_linea=0,$x='20',$y='65',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(30,10,'Nombramiento Permanente',$borde=0,$alineacion='C',$fondo=false,$salto_de_linea=0,$x='25',$y='65',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='T', $fitcell=true);

$pdf->Multicell(5,5,'',$borde=1,$alineacion='B',$fondo=false,$salto_de_linea=0,$x='55',$y='65',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(5,5,'',$borde=1,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='60',$y='65',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(30,10,'Vacaciones',$borde=0,$alineacion='C',$fondo=false,$salto_de_linea=0,$x='60',$y='65',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='T', $fitcell=true);

$pdf->Multicell(5,5,'',$borde=1,$alineacion='B',$fondo=false,$salto_de_linea=0,$x='85',$y='65',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(5,5,'',$borde=1,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='90',$y='65',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(30,10,'Licencia con sueldo',$borde=0,$alineacion='C',$fondo=false,$salto_de_linea=0,$x='95',$y='65',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='T', $fitcell=true);

$pdf->Multicell(5,5,'',$borde=1,$alineacion='B',$fondo=false,$salto_de_linea=0,$x='125',$y='65',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(5,5,'',$borde=1,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='130',$y='65',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(30,5,'Prorroga de',$borde=0,$alineacion='C',$fondo=false,$salto_de_linea=0,$x='135',$y='65',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='T', $fitcell=true);
$pdf->Multicell(35,5,'',$borde='B',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='165',$y='65',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='T', $fitcell=true);
//FILA #1 SECCION 2----------------------------------------------------------------------------

//FILA #2 SECCION 2----------------------------------------------------------------------------
$pdf->Multicell(5,5,'',$borde=1,$alineacion='B',$fondo=false,$salto_de_linea=0,$x='15',$y='75',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(5,5,'',$borde=1,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='20',$y='75',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(30,10,'Nombramiento Interino',$borde=0,$alineacion='C',$fondo=false,$salto_de_linea=0,$x='25',$y='75',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='T', $fitcell=true);

$pdf->Multicell(5,5,'',$borde=1,$alineacion='B',$fondo=false,$salto_de_linea=0,$x='55',$y='75',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(5,5,'',$borde=1,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='60',$y='75',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(30,10,'Traslado',$borde=0,$alineacion='C',$fondo=false,$salto_de_linea=0,$x='60',$y='75',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='T', $fitcell=true);

$pdf->Multicell(5,5,'',$borde=1,$alineacion='B',$fondo=false,$salto_de_linea=0,$x='85',$y='75',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(5,5,'',$borde=1,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='90',$y='75',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(30,10,'Licencia sin sueldo',$borde=0,$alineacion='C',$fondo=false,$salto_de_linea=0,$x='95',$y='75',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='T', $fitcell=true);

$pdf->Multicell(5,5,'',$borde=1,$alineacion='B',$fondo=false,$salto_de_linea=0,$x='125',$y='75',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(5,5,'',$borde=1,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='130',$y='75',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(30,5,'Reincorporacion',$borde='',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='135',$y='75',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='T', $fitcell=true);
$pdf->Multicell(35,5,'',$borde='B',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='165',$y='75',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='T', $fitcell=true);
//FILA #2 SECCION 2----------------------------------------------------------------------------

//FILA #3 SECCION 2----------------------------------------------------------------------------
$pdf->Multicell(5,5,'',$borde=1,$alineacion='B',$fondo=false,$salto_de_linea=0,$x='15',$y='85',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(5,5,'',$borde=1,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='20',$y='85',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(30,10,'Contrato',$borde=0,$alineacion='C',$fondo=false,$salto_de_linea=0,$x='25',$y='85',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='T', $fitcell=true);

$pdf->Multicell(5,5,'',$borde=1,$alineacion='B',$fondo=false,$salto_de_linea=0,$x='55',$y='85',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(5,5,'',$borde=1,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='60',$y='85',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(30,10,'Jubilacion',$borde=0,$alineacion='C',$fondo=false,$salto_de_linea=0,$x='60',$y='85',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='T', $fitcell=true);

$pdf->Multicell(5,5,'',$borde=1,$alineacion='B',$fondo=false,$salto_de_linea=0,$x='85',$y='85',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(5,5,'',$borde=1,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='90',$y='85',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(30,10,'Gravidez',$borde=0,$alineacion='C',$fondo=false,$salto_de_linea=0,$x='95',$y='85',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='T', $fitcell=true);

$pdf->Multicell(5,5,'',$borde=1,$alineacion='B',$fondo=false,$salto_de_linea=0,$x='125',$y='85',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(5,5,'',$borde=1,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='130',$y='85',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(30,5,'Cambio en el nombre',$borde=0,$alineacion='C',$fondo=false,$salto_de_linea=0,$x='135',$y='85',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='T', $fitcell=true);
$pdf->Multicell(35,10,'',$borde='R',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='165',$y='75',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='T', $fitcell=true);
//FILA #3 SECCION 2----------------------------------------------------------------------------

//FILA #4 SECCION 2----------------------------------------------------------------------------
$pdf->Multicell(5,5,'',$borde=1,$alineacion='B',$fondo=false,$salto_de_linea=0,$x='15',$y='95',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(5,5,'',$borde=1,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='20',$y='95',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(30,10,'Cambio de categoria',$borde=0,$alineacion='C',$fondo=false,$salto_de_linea=0,$x='25',$y='95',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='T', $fitcell=true);

$pdf->Multicell(5,5,'',$borde=1,$alineacion='B',$fondo=false,$salto_de_linea=0,$x='55',$y='95',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(5,5,'',$borde=1,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='60',$y='95',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(30,10,'Renuncia',$borde=0,$alineacion='C',$fondo=false,$salto_de_linea=0,$x='60',$y='95',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='T', $fitcell=true);

$pdf->Multicell(5,5,'',$borde=1,$alineacion='B',$fondo=false,$salto_de_linea=0,$x='85',$y='95',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(5,5,'',$borde=1,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='90',$y='95',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(30,10,'Matrimonio',$borde=0,$alineacion='C',$fondo=false,$salto_de_linea=0,$x='95',$y='95',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='T', $fitcell=true);

$pdf->Multicell(5,5,'',$borde=1,$alineacion='B',$fondo=false,$salto_de_linea=0,$x='125',$y='95',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(5,5,'',$borde=1,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='130',$y='95',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(30,5,'Sancion',$borde=0,$alineacion='C',$fondo=false,$salto_de_linea=0,$x='135',$y='95',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='T', $fitcell=true);
$pdf->Multicell(35,10,'',$borde='R',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='165',$y='95',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='T', $fitcell=true);
//FILA #4 SECCION 2----------------------------------------------------------------------------

//FILA #5 SECCION 2----------------------------------------------------------------------------
$pdf->Multicell(5,5,'',$borde=1,$alineacion='B',$fondo=false,$salto_de_linea=0,$x='15',$y='105',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(5,5,'',$borde=1,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='20',$y='105',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(30,10,'Ascenso',$borde=0,$alineacion='C',$fondo=false,$salto_de_linea=0,$x='25',$y='105',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='T', $fitcell=true);

$pdf->Multicell(5,5,'',$borde=1,$alineacion='B',$fondo=false,$salto_de_linea=0,$x='55',$y='105',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(5,5,'',$borde=1,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='60',$y='105',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(30,10,'Destitucion',$borde=0,$alineacion='C',$fondo=false,$salto_de_linea=0,$x='60',$y='105',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='T', $fitcell=true);

$pdf->Multicell(5,5,'',$borde=1,$alineacion='B',$fondo=false,$salto_de_linea=0,$x='85',$y='105',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(5,5,'',$borde=1,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='90',$y='105',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(30,10,'Modificacion de resolucion',$borde=0,$alineacion='C',$fondo=false,$salto_de_linea=0,$x='95',$y='105',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='T', $fitcell=true);

$pdf->Multicell(5,5,'',$borde=1,$alineacion='B',$fondo=false,$salto_de_linea=0,$x='125',$y='105',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(5,5,'',$borde=1,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='130',$y='105',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);

$pdf->Multicell(30,5,'Otro',$borde=0,$alineacion='C',$fondo=false,$salto_de_linea=0,$x='135',$y='105',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='T', $fitcell=true);
$pdf->Multicell(35,5,'',$borde='RB',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='165',$y='105',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='T', $fitcell=true);
//FILA #5 SECCION 2----------------------------------------------------------------------------

//FILA #6 SECCION 2----------------------------------------------------------------------------
$pdf->Multicell(5,5,'',$borde=1,$alineacion='B',$fondo=false,$salto_de_linea=0,$x='15',$y='115',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(5,5,'',$borde=1,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='20',$y='115',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(30,10,'Aumento',$borde=0,$alineacion='C',$fondo=false,$salto_de_linea=0,$x='25',$y='115',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='T', $fitcell=true);

$pdf->Multicell(5,5,'',$borde=1,$alineacion='B',$fondo=false,$salto_de_linea=0,$x='55',$y='115',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(5,5,'',$borde=1,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='60',$y='115',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(30,10,'Suspension',$borde=0,$alineacion='C',$fondo=false,$salto_de_linea=0,$x='60',$y='115',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='T', $fitcell=true);

$pdf->Multicell(5,5,'',$borde=1,$alineacion='B',$fondo=false,$salto_de_linea=0,$x='85',$y='115',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(5,5,'',$borde=1,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='90',$y='115',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);

$pdf->Multicell(30,10,'Revocatoria',$borde=0,$alineacion='C',$fondo=false,$salto_de_linea=0,$x='95',$y='115',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='T', $fitcell=true);

$pdf->Multicell(5,5,'',$borde=1,$alineacion='B',$fondo=false,$salto_de_linea=0,$x='125',$y='115',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(5,5,'',$borde=1,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='130',$y='115',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);

$pdf->Multicell(30,10,'Eventual',$borde=0,$alineacion='C',$fondo=false,$salto_de_linea=0,$x='135',$y='115',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='T', $fitcell=true);

//FILA #6 SECCION 2----------------------------------------------------------------------------

//bordes---------------------------------------------------------------------------------------
$pdf->Multicell(5,10,'',$borde='R',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='195',$y='60',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(25,90,'',$borde='L',$alineacion='R',$fondo=false,$salto_de_linea=0,$x='10',$y='70',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(5,10,'',$borde='R',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='195',$y='70',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='B', $fitcell=true);
//bordes---------------------------------------------------------------------------------------


$pdf->Multicell(190,4,'',$borde='LRB',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='10',$y='120',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=4,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(190,1,'',$borde='LRB',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='10',$y='124',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=1,$alineacion_vertical='B', $fitcell=true);

$pdf->Multicell(25,10,'N° de planilla:',$borde='L',$alineacion='R',$fondo=false,$salto_de_linea=0,$x='10',$y='125',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(40,10,$data['tipnom'],$borde='B',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='35',$y='125',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(35,10,'N° de Empleado:',$borde=0,$alineacion='R',$fondo=false,$salto_de_linea=0,$x='75',$y='125',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(29,10,$data['nomposicion_id'],$borde='RB',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='110',$y='125',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(60,10,'   SOLICITADO',$borde='LR',$alineacion='L',$fondo=false,$salto_de_linea=0,$x='140',$y='125',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='B', $fitcell=true);

$pdf->Multicell(25,10,'Reemplaza a:',$borde='L',$alineacion='R',$fondo=false,$salto_de_linea=0,$x='10',$y='135',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(104,10,'',$borde='RB',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='35',$y='135',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(60,10,'',$borde='LR',$alineacion='L',$fondo=false,$salto_de_linea=0,$x='140',$y='135',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(50,10,'',$borde='B',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='145',$y='135',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='B', $fitcell=true);

$pdf->Multicell(129,5,'          Solicitado a partir de:',$borde='L',$alineacion='L',$fondo=false,$salto_de_linea=0,$x='10',$y='145',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(50,5,'Firma empleado',$borde=0,$alineacion='C',$fondo=false,$salto_de_linea=0,$x='145',$y='145',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='T', $fitcell=true);
$pdf->Multicell(60,5,'',$borde='R',$alineacion='L',$fondo=false,$salto_de_linea=0,$x='140',$y='145',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);

$pdf->Multicell(35,5,'   Explicacion de Solicitud:',$borde='L',$alineacion='R',$fondo=false,$salto_de_linea=0,$x='10',$y='150',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);

if(isset($data['observaciones'])){$obs=$data['observaciones'];}else{$obs='Sin definir';}
$pdf->Multicell(94,10,$obs,$borde='RB',$alineacion='J',$fondo=false,$salto_de_linea=0,$x='45',$y='150',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(50,5,'',$borde='B',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='145',$y='150',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='T', $fitcell=true);
$pdf->Multicell(60,5,'',$borde='LR',$alineacion='L',$fondo=false,$salto_de_linea=0,$x='140',$y='150',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);

$pdf->Multicell(129,5,'',$borde='LRB',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='10',$y='155',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(60,5,'Jefe de Servicios o Departamento',$borde='LR',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='140',$y='155',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='T', $fitcell=true);

$pdf->Multicell(129,5,'',$borde='LRB',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='10',$y='160',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(60,5,'',$borde='LR',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='140',$y='160',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='T', $fitcell=true);
$pdf->Multicell(50,5,'',$borde='B',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='145',$y='160',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);

$pdf->Multicell(129,5,'',$borde='LRB',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='10',$y='165',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(60,5,'Jefe de Recursos Humanos Unidad Ejecutiva',$borde='LR',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='140',$y='165',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='T', $fitcell=true);

$pdf->Multicell(129,5,'',$borde='LRB',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='10',$y='170',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(60,5,'',$borde='LR',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='140',$y='170',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='T', $fitcell=true);
$pdf->Multicell(50,5,'',$borde='B',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='145',$y='170',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);

$pdf->Multicell(129,4,' Partida:'.$transitorio['partida'],$borde='LRB',$alineacion='L',$fondo=false,$salto_de_linea=0,$x='10',$y='175',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=4,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(60,4,'Director Nacional/Regional',$borde='LRB',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='140',$y='175',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=4,$alineacion_vertical='T', $fitcell=true);
$pdf->Multicell(190,1,'',$borde='LR',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='10',$y='179',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=1,$alineacion_vertical='B', $fitcell=true);

$pdf->Multicell(43,4,'DETALLES',$borde='LTRB',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='10',$y='180',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=4,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(43,4,'SITUACION ACTUAL',$borde='LTRB',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='53',$y='180',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=4,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(43,4,'SUTIACION PROPUESTA',$borde='LTRB',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='96',$y='180',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=4,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(129,1,'',$borde='LB',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='10',$y='184',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=1,$alineacion_vertical='B', $fitcell=true);

$pdf->Multicell(60,5,'',$borde='LTR',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='140',$y='180',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='T', $fitcell=true);

$pdf->Multicell(5,5,'1',$borde='LTB',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='10',$y='185',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(38,5,'Primer Nombre',$borde='LTB',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='15',$y='185',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(43,5,'',$borde='LTRB',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='53',$y='185',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);

$nombres=nombres($data['nombres']);

$pdf->Multicell(43,5,$nombres['primer'],$borde='TRB',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='96',$y='185',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(60,5,'',$borde='LR',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='140',$y='185',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='T', $fitcell=true);
$pdf->Multicell(50,5,'',$borde='B',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='145',$y='185',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='T', $fitcell=true);

$pdf->Multicell(43,5,'Segundo Nombre',$borde='LTB',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='10',$y='190',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(43,5,'',$borde='LTRB',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='53',$y='190',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(43,5,$nombres['segundo'],$borde='TRB',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='96',$y='190',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(60,5,$empresa['ger_rrhh'],$borde='LR',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='140',$y='190',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='C', $fitcell=true);

$pdf->Multicell(43,5,'Apellido Paterno',$borde='LTB',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='10',$y='195',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(43,5,'',$borde='LTRB',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='53',$y='195',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);

$apellidos=nombres($data['apellidos']);
$pdf->Multicell(43,5,validar($apellidos['primer']),$borde='TRB',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='96',$y='195',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(60,5,'Directora Nal. de Recursos Humanos',$borde='LRB',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='140',$y='195',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='T', $fitcell=true);


$pdf->Multicell(43,5,'Apellido de casada',$borde='LTB',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='10',$y='200',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(43,5,'',$borde='LTRB',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='53',$y='200',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
if ($data['sexo'] != 'Masculino'){
    $ap_cas=validar($data['apellido_casada']);
}else{
    $ap_cas='';
}
$pdf->Multicell(43,5,$ap_cas,$borde='TRB',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='96',$y='200',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(60,5,'',$borde='LR',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='140',$y='200',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='T', $fitcell=true);

$pdf->Multicell(43,4,'Inicial apellido materno',$borde='LTB',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='10',$y='205',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=4,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(43,4,'',$borde='LTRB',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='53',$y='205',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=4,$alineacion_vertical='B', $fitcell=true);

validar($data['apellido_materno']);
if (isset($data['apellido_materno']))
{
    $inicial=validar($data['apellido_materno']);
}
$pdf->Multicell(43,4,$inicial[0],$borde='TRB',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='96',$y='205',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=4,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(60,5,'',$borde='LR',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='140',$y='205',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='T', $fitcell=true);
$pdf->Multicell(129,1,'',$borde='LB',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='10',$y='209',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=1,$alineacion_vertical='B', $fitcell=true);

$pdf->Multicell(5,10,'2',$borde='LTB',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='10',$y='210',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(38,10,'Titulo Cargo',$borde='LTB',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='15',$y='210',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(43,10,'',$borde='LTRB',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='53',$y='210',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(43,10,$cargo['des_car'],$borde='TRB',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='96',$y='210',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(60,10,'',$borde='LR',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='140',$y='210',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='T', $fitcell=true);
$pdf->Multicell(50,10,'',$borde='B',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='145',$y='210',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='T', $fitcell=true);

$pdf->Multicell(43,5,'Categoria',$borde='LTB',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='10',$y='220',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(43,5,'',$borde='LTRB',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='53',$y='220',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(43,5,'',$borde='TRB',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='96',$y='220',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(60,5,'Director General/Regional de Salud',$borde='LRB',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='140',$y='220',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='T', $fitcell=true);

$pdf->Multicell(43,4,'Horas',$borde='LTB',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='10',$y='225',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=4,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(43,4,'',$borde='LTRB',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='53',$y='225',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=4,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(43,4,round($data['hora_base'],0),$borde='TRB',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='96',$y='225',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=4,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(129,1,'',$borde='LB',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='10',$y='229',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=1,$alineacion_vertical='B', $fitcell=true);

$pdf->Multicell(60,5,'El Ministro de Salud',$borde='LR',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='140',$y='225',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='T', $fitcell=true);

$pdf->Multicell(5,5,'3',$borde='LTB',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='10',$y='230',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(38,5,'Sueldo Mensual',$borde='LTB',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='15',$y='230',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(43,5,'',$borde='LTRB',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='53',$y='230',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);


$sueldo = number_format($data['suesal'],2);

$pdf->Multicell(43,5, "B/ ".$sueldo, $borde='TRB',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='96',$y='230',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(60,5,'En uso de sus facultades legales,',$borde='LR',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='140',$y='230',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='T', $fitcell=true);

$pdf->Multicell(43,5,'Numero de Seguro Social',$borde='LTB',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='10',$y='235',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(43,5,'',$borde='LTRB',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='53',$y='235',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(43,5,$data['seguro_social'],$borde='TRB',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='96',$y='235',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(60,5,'RESUELVE:',$borde='LR',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='140',$y='235',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);

$pdf->Multicell(43,5,'Cedula',$borde='LTB',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='10',$y='240',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(43,5,'',$borde='LTRB',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='53',$y='240',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(43,5,$data['cedula'],$borde='TRB',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='96',$y='240',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(60,5,'',$borde='LRB',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='140',$y='240',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);

$pdf->Multicell(43,5,'Sobre sueldo, zona Apartada',$borde='LTB',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='10',$y='245',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(43,5,'',$borde='LTRB',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='53',$y='245',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(43,5,'',$borde='TRB',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='96',$y='245',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(60,5,'NOMBRAR',$borde='LRB',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='140',$y='245',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);

$pdf->Multicell(43,5,'Sobre sueldo, por jefatura',$borde='LTB',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='10',$y='250',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(43,5,'',$borde='LTRB',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='53',$y='250',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(43,5,'',$borde='TRB',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='96',$y='250',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(5,5,'a:',$borde='L',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='140',$y='250',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(55,5,$nombres['primer'].' '.$apellidos['primer'],$borde='RB',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='145',$y='250',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);

$pdf->Multicell(43,5,'Otros sueldo (6% Bienal)',$borde='LTB',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='10',$y='255',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(43,5,'',$borde='LTRB',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='53',$y='255',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(43,5,'',$borde='TRB',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='96',$y='255',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(60,5,'',$borde='LRB',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='140',$y='255',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);

$pdf->Multicell(43,5,'Cantidad de dias solicitados',$borde='LTB',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='10',$y='260',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(43,5,'',$borde='LTRB',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='53',$y='260',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(43,5,'',$borde='TRB',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='96',$y='260',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(60,5,'De acuerdo a las especificaciones que',$borde='LR',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='140',$y='260',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);

$pdf->Multicell(43,5,'Fecha de nacimiento',$borde='LTB',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='10',$y='265',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(43,5,'',$borde='LTRB',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='53',$y='265',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);

if (isset($data['fecnac'])){
    $fecnac=fechas($data['fecnac'],3);   
}else{
    $fecnac = array('dia' => '00',
                    'mes' => '00',
                    'anio'=> '0000');
}
$pdf->Multicell(12,5,$fecnac['dia'],$borde='TRB',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='96',$y='265',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(19,5,$fecnac['mes'],$borde='TRB',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='108',$y='265',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(12,5,$fecnac['anio'],$borde='TRB',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='127',$y='265',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);

$pdf->Multicell(60,5,'anteceden',$borde='LR',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='140',$y='265',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);

$pdf->Multicell(43,4,'Sexo',$borde='LTB',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='10',$y='270',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=4,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(43,4,'',$borde='LTRB',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='53',$y='270',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=4,$alineacion_vertical='B', $fitcell=true);
$sexo=$data['sexo'];
$pdf->Multicell(43,4,$sexo[0],$borde='TRB',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='96',$y='270',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=4,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(129,1,'',$borde='LB',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='10',$y='',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=1,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(60,5,'',$borde='LR',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='140',$y='270',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);

$pdf->Multicell(64,5,'Departamento de formulacion y',$borde='LTR',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='10',$y='275',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(64,5,'Departamento de Recursos Humanos',$borde='LTR',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='75',$y='275',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);

$presidente=validar($empresa['pre_sid']);
$pdf->Multicell(60,5,$presidente,$borde='LR',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='140',$y='275',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(50,5,'',$borde='B',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='145',$y='275',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);

$pdf->Multicell(64,5,'Control presupuestario',$borde='LR',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='10',$y='280',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(64,5,'Regional',$borde='LR',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='75',$y='280',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(60,5,'Ministro de Salud',$borde='LRB',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='140',$y='280',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);

$pdf->Multicell(30,5,'Procede',$borde='L',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='10',$y='285',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(34,5,'',$borde='RB',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='40',$y='285',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(30,5,'Preparado por:',$borde='L',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='75',$y='285',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(34,5,'',$borde='RB',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='105',$y='285',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(40,5,'Fecha en que rige la accion:',$borde='L',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='140',$y='285',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(20,5,'',$borde='R',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='180',$y='285',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);

$pdf->Multicell(64,5,'',$borde='LR',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='10',$y='290',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(30,5,'Jefe de RRHH:',$borde='L',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='75',$y='290',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(34,5,'',$borde='RB',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='105',$y='290',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);

$pdf->Multicell(10,5,'',$borde='LB',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='140',$y='290',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(5,5,'de',$borde=0,$alineacion='C',$fondo=false,$salto_de_linea=0,$x='150',$y='290',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(30,5,'',$borde='B',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='155',$y='290',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(5,5,'de',$borde=0,$alineacion='C',$fondo=false,$salto_de_linea=0,$x='185',$y='290',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(10,5,'',$borde='RB',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='190',$y='290',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);


$pdf->Multicell(30,5,'Firma:',$borde='L',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='10',$y='295',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(34,5,'',$borde='RB',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='40',$y='295',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(30,5,'Nombre:',$borde='L',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='75',$y='295',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(34,5,'',$borde='RB',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='105',$y='295',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);

$pdf->Multicell(10,5,'Hasta',$borde='L',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='140',$y='295',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(10,5,'',$borde='B',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='150',$y='295',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(5,5,'de',$borde=0,$alineacion='C',$fondo=false,$salto_de_linea=0,$x='160',$y='295',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(15,5,'',$borde='B',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='165',$y='295',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(5,5,'de',$borde=0,$alineacion='C',$fondo=false,$salto_de_linea=0,$x='180',$y='295',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(15,5,'',$borde='RB',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='185',$y='295',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);

$pdf->Multicell(30,5,'',$borde='L',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='10',$y='300',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='T', $fitcell=true);
$pdf->Multicell(34,5,'Analista',$borde='R',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='40',$y='300',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='T', $fitcell=true);
$pdf->Multicell(64,5,'',$borde='LR',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='75',$y='300',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(54,5,'',$borde='B',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='79',$y='300',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(60,5,'Jefe(a) del Depto. de Clasif y Ret. de Puestos',$borde='LRB',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='140',$y='300',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);

$pdf->Multicell(30,5,'Firma:',$borde='L',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='10',$y='305',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='T', $fitcell=true);
$pdf->Multicell(34,5,'',$borde='RB',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='40',$y='305',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='T', $fitcell=true);
$pdf->Multicell(64,5,'Analista',$borde='LR',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='75',$y='305',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='T', $fitcell=true);
$pdf->Multicell(30,5,'Numero secuencial:',$borde='L',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='140',$y='305',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(30,5,'',$borde='RB',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='170',$y='305',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);

$pdf->Multicell(30,5,'',$borde='L',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='10',$y='310',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='T', $fitcell=true);
$pdf->Multicell(34,5,'Jefe',$borde='R',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='40',$y='310',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='T', $fitcell=true);
$pdf->Multicell(64,5,'',$borde='LR',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='75',$y='310',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='T', $fitcell=true);
$pdf->Multicell(54,5,'',$borde='B',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='80',$y='310',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='T', $fitcell=true);
$pdf->Multicell(30,5,'Fecha:',$borde='L',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='140',$y='310',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(30,5,'',$borde='RB',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='170',$y='310',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);

$pdf->Multicell(30,5,'',$borde='LB',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='10',$y='315',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='T', $fitcell=true);
$pdf->Multicell(34,5,'',$borde='RB',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='40',$y='315',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='T', $fitcell=true);
$pdf->Multicell(64,5,'Jefe del Departamento',$borde='LRB',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='75',$y='315',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='T', $fitcell=true);
$pdf->Multicell(60,5,'',$borde='LRB',$alineacion='C',$fondo=false,$salto_de_linea=0,$x='140',$y='315',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);

$pdf->SetFont('times','',9);


// move pointer to last page
$pdf->lastPage();
// ---------------------------------------------------------
//Close and output PDF document
$pdf->Output('example_005.pdf', 'I');

/*

$pdf->Multicell(5,15,'',$borde='L',$alineacion='L',$fondo=false,$salto_de_linea=0,$x='10',$y='60',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=15,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(5,5,'',$borde=1,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='15',$y='65',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(5,5,'',$borde=1,$alineacion='L',$fondo=false,$salto_de_linea=0,$x='20',$y='65',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=5,$alineacion_vertical='B', $fitcell=true);
$pdf->Multicell(20,10,'Nombramiento Permanente',$borde=0,$alineacion='R',$fondo=false,$salto_de_linea=0,$x='25',$y='65',$reseth=true,$ajuste_horizontal=1,$ishtml=false,$autopadding=true,$maxh=10,$alineacion_vertical='T', $fitcell=true);

*/
