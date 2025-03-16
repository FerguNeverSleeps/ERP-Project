<?php
session_start();
ob_start();

require('../../nomina/tcpdf/tcpdf.php');
include("../../nomina/lib/common.php");
date_default_timezone_set('America/Panama');
require_once('../../nomina/lib/database.php');
$db     = new Database($_SESSION['bd']);
//------------------------------------------------------------------------------------------------------------
//consulta de datos
$amaxonia   = new bd($bd);
$codnom     = $_GET[nomina_id];
$tipnom     = $_GET[codt];
$mes        = $_GET[mes];
$anio       = $_GET[anio];
$chequera   = $_GET[codigo];
$ficha      = $_GET[ficha];
$cedula = $_GET[cedula];
$quincena   = $_GET[quincena];
$inicio     = $_GET["fecha_inicio"];
$fin        = $_GET["fecha_fin"];
//------------------------------------------------------------
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('America/Panama');

$meses=["ENERO","FEBRERO","MARZO","ABRIL","MAYO","JUNIO","JULIO","AGOSTO","SEPTIEMBRE","OCTUBRE","NOVIEMBRE","DICIEMBRE"];

class RPT_PDF extends TCPDF
{
    public function Header() 
    {
        global $meses, $mes, $anio, $tam1, $tam2, $tam3,$ficha, $codnom,$fecha_inicio,$fecha_fin,$apenom,$clave_ir;
        $fill              = FALSE;
        $border            =0;
        $ln                =0;
        $fill              =0;
        $align             ='L';
        $link              =0;
        $stretch           =0;
        $ignore_min_height =0;
        $calign            ='';
        $valign            ='';
        $height            =6;
        $ruta_img='../../includes/assets/img/logo_selectra.jpg';
        $this->SetFont('helvetica', 'B', 10);
        //$this->Image($ruta_img, $x='25', $y='8', $w=20, $h=20, $type='jpg', $link='', $align='', $resize=false, $dpi=300, $palign='', $ismask=false, $imgmask=false, $border='', $fitbox=false, $hidden=false, $fitonpage=false);        
        $reg               ="";
        $pag               =0;

        $db     = new Database($_SESSION['bd']);
        $sql_empresa = "SELECT * FROM nomempresa";
        $empresa = $db->query($sql_empresa)->fetch_object();
        $this->SetFont('helvetica', '',6);
        $this->Ln(7);
        //numero de paginas
        $this->Cell(145,5,"Fecha Impresión", "", 0, 'L', 0,0,1,0,'','');
        $this->Cell(145,5,"", "",1,0,0,0,0);
        //fecha de impresion
        $this->Cell(145,5,date("d/m/Y H:s:i"), "", 0, 'L', 0,0,1,0,'','');
        $this->Cell(145,5,"", "","",0,0,0,0);
        $this->Cell(30,5,'Página '.$this->getAliasNumPage().' de '.$this->getAliasNbPages(), "", 1, $align='L', 0,0,0,0,'','');

        //identificación del reporte
        $this->SetY(10);
        $this->SetFont('helvetica', 'B',8);
        $this->Cell(0,6,$empresa->nom_emp, "", 1, 'C', 0,0,1,0,'','');
        $this->Cell(0,6,"INFORME DE TRANSACCIONES POR EMPLEADO", "", 1, 'C', 0,0,1,0,'','');
        $this->Cell(0,6,"DESDE EL EMPLEADO: ".$this->ficha." HASTA EL EMPLEADO: ".$ficha, "", 1, 'C', 0,0,1,0,'','');
        $this->Cell(0,6,"Y DESDE LA FECHA: ".$this->ConvertirFecha($this->fecha_inicio)." HASTA: ".$this->ConvertirFecha($this->fecha_fin), "", 1, 'C', 0,0,1,0,'','');

        //cabecera de la tabla
        $planilla = str_pad($codnom, 5,'0',STR_PAD_LEFT);
        $ancho = 1;
        $this->Ln(8);
        $this->SetFont('helvetica','',5);
        $this->Cell(160,$ancho,"Empleado: ".$ficha." ".$apenom, 'T', $ln, $align='L', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell(160,$ancho,"Clave ISR:".$this->clave_ir, 'T', $ln, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Ln(4);
        $this->SetFont('helvetica','',5);
        $this->Cell($tam1["Fecha"],$ancho,"", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam1["Plan"],$ancho,"", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam1["Plan"],$ancho,"", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam1["Trans"],$ancho,"", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam1["salareg"],$ancho,"Sal.Reg", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam1["sobretiempo"],$ancho,"", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam1["bonoprod"],$ancho,"", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam3["comisiones"],$ancho,"", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam3["donaciones"],$ancho,"", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam3["viaticos"],$ancho,"", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam1["gratificaciones"],$ancho,"", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam3["bonoprod"],$ancho,"", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam1["otros"],$ancho,"", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam1["vacacion"],$ancho,"", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam1["decimo"],$ancho,"", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam1["util"],$ancho,"", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam1["Liq"],$ancho,"", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam1["Lic"],$ancho,"", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam1["SubTotal"],$ancho,"", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam1["Gastorep"],$ancho,"", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam1["Otros"],$ancho,"", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam3["Adelanto"],$ancho,"", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam3["SegSoc"],$ancho,"", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam3["segeduc"],$ancho,"", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam3["ISR"],$ancho,"", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam3["Acreed"],$ancho,"", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam3["desc_empresa"],$ancho,"", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam3["Neto"],$ancho,"", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Ln();
        $this->SetFont('helvetica','',5);
        $this->Cell($tam2["Fecha"],$ancho,"", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam2["Plan"],$ancho,"", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam2["Plan"],$ancho,"", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam2["Trans"],$ancho,"# Trans/", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam2["salareg"],$ancho,"Regular,", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam2["sobretiempo"],$ancho,"", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam2["bonoprod"],$ancho,"", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam3["comisiones"],$ancho,"", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam3["donaciones"],$ancho,"", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam3["viaticos"],$ancho,"", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam1["gratificaciones"],$ancho,"", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam3["bonoprod"],$ancho,"", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam2["altura"],$ancho,"", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam2["otros"],$ancho,"Prima", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam2["vacacion"],$ancho,"", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign); 
        $this->Cell($tam2["decimo"],$ancho,"", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell(($tam2["SegSoc"]+$tam2["segeduc"]+$tam2["ISR"]),$ancho,"/--DESCUENTOS LEGALES--\\", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam2["Liq"],$ancho,"", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam2["Acreed"],$ancho,"Descuento", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam3["desc_empresa"],$ancho,"Desc.", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam2["Neto"],$ancho,"", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);

        $this->Ln();
        $this->SetFont('helvetica','',5);
        $border= 'B';
        $this->Cell($tam3["Fecha"],$ancho,"Fecha", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam3["Plan"],$ancho,"#Plan.", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam3["Plan"],$ancho,"Frecuencia", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam3["Trans"],$ancho,"T.Planilla", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam3["salareg"],$ancho,"y Ajustes", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam3["sobretiempo"],$ancho,"Vacac", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam3["bonoprod"],$ancho,"XII.", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam3["altura"],$ancho,"G.Rep.", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam3["otros"],$ancho,"XIII G.Rep", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam3["comisiones"],$ancho,"Comision", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam3["viaticos"],$ancho,"Viáticos", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam3["gratificaciones"],$ancho,"Gratif.", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam3["bonoprod"],$ancho,"Bonif.", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam3["donaciones"],$ancho,"Prod.", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam3["Otros"],$ancho,"Otros", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam3["donaciones"],$ancho,"Donac.", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);

        $this->Cell($tam3["SegSoc"],$ancho,"Seg.Soc.", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam3["segeduc"],$ancho,"Seg.Educ.", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam3["ISR"],$ancho,"ISR", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam3["ISR"],$ancho,"ISR G.R.", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        
        $this->Cell($tam3["Acreed"],$ancho,"Acreed.", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam3["desc_empresa"],$ancho,"Empresa", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam3["Liq"],$ancho,"Liquidac.", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam3["Neto"],$ancho,"Neto", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);

        /*$this->Cell($tam3["util"],$ancho,"Part. Util", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam3["Lic"],$ancho,"Licen.", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam3["SubTotal"],$ancho,"Subtotal", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam3["Gastorep"],$ancho,"Repre.", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam3["Adelanto"],$ancho,"Adelanto", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);*****/
        $this->SetFont('helvetica','B',9);
        $this->Ln();
    }
    public function ConvertirFecha($fecha)
    {
        return date("d/m/Y",strtotime($fecha));
    }
    public function ConvertirFechaYMD($fecha)
    {
        return date("Y-m-d",strtotime($fecha));
    }
}
$len = 13.5;
$tam1 = $tam2 = $tam3 =[
    "Fecha"           => $len,
    "Plan"            => $len,
    "Trans"           => $len,
    "salareg"         => $len,
    "comisiones"      => $len,
    "sobretiempo"     => $len,
    "bonoprod"        => $len,
    "altura"          => $len,
    "donaciones"      => $len,
    "comisiones"      => $len,
    "viaticos"        => $len,
    "gratificaciones" => $len,
    "otros"           => $len,
    "vacacion"        => $len,
    "decimo"          => $len,
    "util"            => $len,
    "Liq"             => $len,
    "Lic"             => $len,
    "SubTotal"        => $len,
    "Gastorep"        => $len,
    "Otros"           => $len,
    "Adelanto"        => $len,
    "SegSoc"          => $len,
    "segeduc"         => $len,
    "ISR"             => $len,
    "Acreed"          => $len,
    "Empresa"         => $len,
    "Neto"            => $len,
    "desc_empresa"    => $len
];



$fill              = FALSE;
$border            =0;
$ln                =0;
$fill              = 0;
$align             ='L';
$link              =0;
$stretch           =0;
$ignore_min_height =0;
$calign            ='';
$valign            ='';


$pdf=new RPT_PDF('L','mm','LEGAL');
  $strsql= "SELECT COUNT(*) from salarios_acumulados where cedula='{$cedula}'";
  $result =$db->query($strsql); 
  $fila = $result->fetch_array(); 
  $num_total_registros = $fila[0];
  
 // $strsql= "SELECT a.*, b.personal_id, b.cedula, b.ficha, b.apenom, c.descrip as planilla, d.descrip as tipo_planilla, e.descrip as frecuencia "
	$strsql= "SELECT
		a.*,
		b.personal_id,
		b.cedula,
		b.ficha,
		b.apenom,
		c.codnom,
		c.descrip AS planilla,
		d.descrip AS tip_planilla,
		e.descrip desc_frec_planilla 
	FROM
		salarios_acumulados AS a
		LEFT JOIN nompersonal AS b ON ( a.cedula = b.cedula )
		LEFT JOIN nom_nominas_pago AS c ON ( a.cod_planilla = c.codnom AND a.tipo_planilla = c.tipnom )
		LEFT JOIN nomtipos_nomina AS d ON ( a.tipo_planilla = d.codtip )
		LEFT JOIN nomfrecuencias AS e ON ( a.frecuencia_planilla = e.codfre ) 
		WHERE a.cedula='{$cedula}' 
	AND  a.fecha_pago between '{$pdf->ConvertirFechaYMD($inicio)}' AND '{$pdf->ConvertirFechaYMD($fin)}' 
	ORDER BY a.fecha_pago ASC ";
	$res =$db->query($strsql);


$db     = new Database($_SESSION['bd']);
$sql_empleado = "SELECT * FROM nompersonal WHERE cedula = '{$cedula}'";
$empleado = $db->query($sql_empleado)->fetch_object();


$pdf->fecha_inicio = $inicio;

$pdf->fecha_fin    = $fin;

$pdf->ficha        = $ficha;

$pdf->clave_ir     = $empleado->clave_ir;

$apenom            = $empleado->apenom;

$pdf->SetMargins(PDF_MARGIN_LEFT, 50, PDF_MARGIN_RIGHT);

$pdf->SetTitle('DETALLE DE SALARIOS ACUMULADOS');

$pdf->SetDisplayMode('fullpage','two');

$pdf->AddPage('L');

$pdf->SetFont('helvetica', '',5);

$pdf->SetY(52.5);

$ln1=6;
$border_fila = 1;
$width = 4;
$subtotal = 0;
$total_salario         += 0;
$total_comisiones      += 0;
$total_donaciones      += 0;
$total_viaticos        += 0;
$total_gratificaciones += 0;
$total_comisiones      += 0;
$total_sobretiempo     += 0;
$total_bono            += 0;
$total_altura          += 0;
$total_otros           += 0;
$total_vacac           += 0;
$total_xiii            += 0;
$total_util            += 0;
$total_liquida         += 0;
$total_licencia        += 0;
$total_gtorep          += 0;
$total_otros_ing       += 0;
$total_adelanto        += 0;
$total_s_s             += 0;
$total_s_e             += 0;
$total_islr            += 0;
$total_acreedor_suma   += 0;
$total_Neto            += 0;

while($row = $res->fetch_assoc()) {
    $Yheight = $pdf->GetY();
    $subtotal            = 0;
    $total_salario         += $row['salario_bruto'];
    $total_comisiones      += $row['comisiones'];
    $total_donaciones      += $row['donaciones'];
    $total_viaticos        += $row['viaticos'];
    $total_gratificaciones += $row['gratificaciones'];
    $total_comisiones      += $row['comisiones'];
    $total_sobretiempo     += $row['sobretiempo'];
    $total_bono            += $row['bono'];
    $total_prima           += $row['prima'];
    $total_otros           += $row['otros'];
    $total_vacac           += $row['vacac'];
    $total_xiii            += $row['xiii'];
    $total_util            += $row['util'];
    $total_liquida         += $row['liquida'];
    $total_licencia        += $row['licencia'];
    $total_gtorep          += $row['gtorep'];
    $total_otros_ing       += $row['otros_ing'];
    $total_adelanto        += $row['adelanto'];
    $total_s_s             += $row['s_s'];
    $total_s_e             += $row['s_e'];
    $total_islr            += $row['islr'];
    $total_desc_empresa    += $row['desc_empresa'];
    $total_acreedor_suma   += $row['acreedor_suma'];
    $total_Neto            += $row['Neto'];
    
    $codnom                 = (!is_null($row['codnom']) ) ? $row['codnom'] : '' ;

    $subtotal += $row['salario_bruto']+$row['comisiones']+$row['sobretiempo']+$row['bono']+$row['prima']+$row['otros']+$row['vacac']+$row['xiii']+$row['gtorep'];
    $pdf->Cell($tam1["Fecha"],$width,date('d-m-Y',strtotime($row['fecha_pago'])), $border_fila, $ln, $align='L', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $pdf->Cell($tam1["Plan"],$width,$codnom, $border_fila, $ln, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $pdf->Cell($tam1["Trans"],$width,$row['desc_frec_planilla'], $border_fila, $ln, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $pdf->Cell($tam1["Trans"],$width,$row['tip_planilla'], $border_fila, $ln, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $pdf->Cell($tam1["salareg"],$width,number_format($row['salario_bruto'],2,".",","), $border_fila, $ln, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $pdf->Cell($tam1["vacacion"],$width,number_format($row['vacac'],2,".",","), $border_fila, $ln, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $pdf->Cell($tam1["decimo"],$width,number_format($row['xiii'],2,".",","), $border_fila, $ln, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $pdf->Cell($tam1["Gastorep"],$width,number_format($row['gtorep'],2,".",","), $border_fila, $ln, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $pdf->Cell($tam1["decimo"],$width,number_format($row['xiii_gtorep'],2,".",","), $border_fila, $ln, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $pdf->Cell($tam1["comisiones"],$width,number_format($row['comisiones'],2,".",","), $border_fila, $ln, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $pdf->Cell($tam1["viaticos"],$width,number_format($row['viaticos'],2,".",","), $border_fila, $ln, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $pdf->Cell($tam1["gratificaciones"],$width,number_format($row['gratificaciones'],2,".",","), $border_fila, $ln, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $pdf->Cell($tam1["bonoprod"],$width,number_format($row['bono'],2,".",","), $border_fila, $ln, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $pdf->Cell($tam1["sobretiempo"],$width,number_format($row['prima'],2,".",","), $border_fila, $ln, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $pdf->Cell($tam1["otros"],$width,number_format($row['otros'],2,".",","), $border_fila, $ln, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $pdf->Cell($tam1["donaciones"],$width,number_format($row['donaciones'],2,".",","), $border_fila, $ln, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);

    $pdf->Cell($tam1["SegSoc"],$width,number_format($row['s_s'],2,".",","), $border_fila, $ln, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $pdf->Cell($tam1["segeduc"],$width,number_format($row['s_e'],2,".",","), $border_fila, $ln, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $pdf->Cell($tam1["ISR"],$width,number_format($row['islr'],2,".",","), $border_fila, $ln, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $pdf->Cell($tam1["ISR"],$width,number_format($row['islr_gr'],2,".",","), $border_fila, $ln, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $pdf->Cell($tam1["Acreed"],$width,number_format($row['acreedor_suma'],2,".",","), $border_fila, $ln, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $pdf->Cell($tam1["desc_empresa"],$width,number_format($row['desc_empresa'],2,".",","), $border_fila, $ln, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $pdf->Cell($tam1["Liq"],$width,number_format($row['liquida'],2,".",","), $border_fila, $ln, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $pdf->Cell($tam1["Neto"],$width,number_format($row['Neto'],2,".",","), $border_fila, $ln, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $pdf->Ln(4);
        if( $pdf->GetY() >172)
            {
                $pdf->AddPage();
                    $pdf->SetY(57);

                $pdf->SetTextColor(0);
                $pdf->SetFillColor(255, 255, 255);
                $pdf->SetTextColor(0,0,0);
                $pdf->SetDrawColor(100, 100, 100);
                
            }
}
 $border_total = 0;
$pdf->SetFont('helvetica', 'B',7);
    $pdf->Cell(($tam1["Fecha"]*4),$width,"Totales", $border_total, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $pdf->SetFont('helvetica', '',5);
    $pdf->Cell($tam1["salareg"],$width,number_format($total_salario,2,".",","), $border_total, $ln, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $pdf->Cell($tam1["vacacion"],$width,number_format($total_vacac,2,".",","), $border_total, $ln, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $pdf->Cell($tam1["decimo"],$width,number_format($total_xiii,2,".",","), $border_total, $ln, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $pdf->Cell($tam1["Gastorep"],$width,number_format($total_gtorep,2,".",","), $border_total, $ln, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $pdf->Cell($tam1["Gastorep"],$width,number_format($total_xiii_gtorep,2,".",","), $border_total, $ln, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $pdf->Cell($tam1["comisiones"],$width,number_format($total_comisiones,2,".",","), $border_total, $ln, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $pdf->Cell($tam1["viaticos"],$width,number_format($total_viaticos,2,".",","), $border_total, $ln, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $pdf->Cell($tam1["gratificaciones"],$width,number_format($total_gratificaciones,2,".",","), $border_total, $ln, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $pdf->Cell($tam1["bonoprod"],$width,number_format($total_bono,2,".",","), $border_total, $ln, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $pdf->Cell($tam1["altura"],$width,number_format($total_prima,2,".",","), $border_total, $ln, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $pdf->Cell($tam1["otros"],$width,number_format($total_otros,2,".",","), $border_total, $ln, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $pdf->Cell($tam1["donaciones"],$width,number_format($total_donaciones,2,".",","), $border_total, $ln, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);

    $pdf->Cell($tam1["SegSoc"],$width,number_format($total_s_s,2,".",","), $border_total, $ln, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $pdf->Cell($tam1["segeduc"],$width,number_format($total_s_e,2,".",","), $border_total, $ln, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $pdf->Cell($tam1["ISR"],$width,number_format($total_islr,2,".",","), $border_total, $ln, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $pdf->Cell($tam1["ISR"],$width,number_format($total_islr_gr,2,".",","), $border_total, $ln, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $pdf->Cell($tam1["Acreed"],$width,number_format($total_acreedor_suma,2,".",","), $border_total, $ln, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $pdf->Cell($tam1["desc_empresa"],$width,number_format($total_desc_empresa,2,".",","), $border_total, $ln, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);

    $pdf->Cell($tam1["Liq"],$width,number_format($total_liquida,2,".",","), $border_total, $ln, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $pdf->Cell($tam1["Neto"],$width,number_format($total_Neto,2,".",","), $border_fila, $ln, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);


$nombre_archivo = "DETALLE_SALARIOS_ACUMULADOS_".$mes."-".$anio ;
ob_clean();
$pdf->Output($nombre_archivo.'.pdf','I');
?>