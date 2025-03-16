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
$tipo_planilla     = $_GET[tipo_planilla];
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
        global $meses, $mes, $anio, $tam1, $tam2, $tam3,$ficha, $codnom,$fecha_inicio,$fecha_fin,$apenom,$clave_ir,$empresa;
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
        $this->SetFont('helvetica', 'B',9);
        $this->Cell(0,6,strtoupper($this->empresa), "", 1, 'C', 0,0,1,0,'','');
        $this->Cell(0,6,"INFORME DE TRANSACCIONES POR EMPLEADO", "", 1, 'C', 0,0,1,0,'','');
        /*$this->Cell(0,6,"DESDE EL EMPLEADO: ".$this->ficha." HASTA EL EMPLEADO: ".$ficha, "", 1, 'C', 0,0,1,0,'','');*/
        $this->Cell(0,6,"DESDE LA FECHA: ".$this->ConvertirFecha($this->fecha_inicio)." HASTA: ".$this->ConvertirFecha($this->fecha_fin), "", 1, 'C', 0,0,1,0,'','');

        //cabecera de la tabla
        $planilla = str_pad($codnom, 5,'0',STR_PAD_LEFT);
        $ancho = 4;
        /*$this->Ln(7);
        $this->SetFont('helvetica','',6);
        $this->Cell(160,$ancho,"Empleado: ".$ficha." ".$apenom, 'T', $ln, $align='L', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell(160,$ancho,"Clave ISR:".$this->clave_ir, 'T', $ln, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);*/
        $this->Ln(4);
        $this->SetFont('helvetica','',6);
        $this->Cell($tam1["Fecha"],$ancho,"", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam1["Plan"],$ancho,"", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam1["Trans"],$ancho,"", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam1["salareg"],$ancho,"Sal.Reg", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam1["sobretiempo"],$ancho,"", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam1["bonoprod"],$ancho,"", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam3["comision"],$ancho,"", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam1["altura"],$ancho,"", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam1["otros"],$ancho,"Otr. Ing", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
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
        $this->Cell($tam3["Neto"],$ancho,"", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Ln(4);
        $this->SetFont('helvetica','',6);
        $this->Cell($tam2["Fecha"],$ancho,"", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam2["Plan"],$ancho,"", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam2["Trans"],$ancho,"# Trans/", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam2["salareg"],$ancho,"Regular,", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam2["sobretiempo"],$ancho,"", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam2["bonoprod"],$ancho,"BonoProd", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam3["comision"],$ancho,"", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam2["altura"],$ancho,"", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam2["otros"],$ancho,"LLUVIA", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam2["vacacion"],$ancho,"", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam2["decimo"],$ancho,"XIII", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam2["util"],$ancho,"", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam2["Liq"],$ancho,"", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam2["Lic"],$ancho,"", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam2["SubTotal"],$ancho,"", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam2["Gastorep"],$ancho,"Gastos", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam2["Otros"],$ancho,"", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam2["Adelanto"],$ancho,"", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell(($tam2["SegSoc"]+$tam2["segeduc"]+$tam2["ISR"]),$ancho,"/--DESCUENTOS LEGALES--\\", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign); 
        $this->Cell($tam2["Acreed"],$ancho,"Descuento", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam2["Neto"],$ancho,"", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);

        $this->Ln(4);
        $this->SetFont('helvetica','',6);
        $border= 'B';
        $this->Cell($tam3["Fecha"],$ancho,"Cedula", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam3["Plan"],$ancho,"Apellidos y Nombres.", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam3["Trans"],$ancho,"T.Planilla", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam3["salareg"],$ancho,"y Ajustes", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam3["sobretiempo"],$ancho,"S/Tiempo", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam3["bonoprod"],$ancho,"BnF/GtF", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam3["comision"],$ancho,"Comision", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam3["altura"],$ancho,"16%Altu", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam3["otros"],$ancho,"Construc", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam3["vacacion"],$ancho,"Vacacion", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam3["decimo"],$ancho,"y Bono", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam3["util"],$ancho,"Part. Util", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam3["Liq"],$ancho,"Liquidac.", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam3["Lic"],$ancho,"Licen.", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam3["SubTotal"],$ancho,"Subtotal", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam3["Gastorep"],$ancho,"Repre.", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam3["Otros"],$ancho,"Otros", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam3["Adelanto"],$ancho,"Adelanto", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam3["SegSoc"],$ancho,"Seg.Soc.", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam3["segeduc"],$ancho,"Seg.Educ.", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam3["ISR"],$ancho,"ISR", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam3["Acreed"],$ancho,"Acreed.", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->Cell($tam3["Neto"],$ancho,"Neto", $border, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
        $this->SetFont('helvetica','B',9);
        $this->Ln(8);
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
$len = 13;
$tam1 = $tam2 = $tam3 =[
    "Fecha"       => $len+3,
    "Plan"        => $len+30,
    "Trans"       => $len-2,
    "salareg"     => $len,
    "comision"    => $len,
    "sobretiempo" => $len,
    "bonoprod"    => $len,
    "altura"      => $len,
    "otros"       => $len,
    "vacacion"    => $len,
    "decimo"      => $len,
    "util"        => $len,
    "Liq"         => $len,
    "Lic"         => $len,
    "SubTotal"    => $len,
    "Gastorep"    => $len,
    "Otros"       => $len,
    "Adelanto"    => $len,
    "SegSoc"      => $len,
    "segeduc"     => $len,
    "ISR"         => $len,
    "Acreed"      => $len,
    "Neto"        => $len,
    "total"        => 300
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

$consulta = "SELECT SUM(sa.salario_bruto) as total_salario_bruto,
SUM(sa.vacac) as total_vacac,
SUM(sa.xiii) as total_xiii,
SUM(sa.gtorep) as total_gtorep,
SUM(sa.xiii_gtorep) as total_xiii_gtorep,
SUM(sa.bono) as total_bono,
SUM(sa.otros_ing) as total_otros_ing,
SUM(sa.prima) as total_prima,
SUM(sa.s_s) as total_s_s,
SUM(sa.s_e) as total_s_e,
SUM(sa.islr) as total_islr,
SUM(sa.islr_gr) as total_islr_gr,
SUM(sa.acreedor_suma) as total_acreedor_suma,
SUM(sa.Neto) as total_Neto
from salarios_acumulados as sa LEFT JOIN nompersonal as b ON (sa.cedula=b.cedula) where sa.tipo_planilla='{$tipo_planilla}' 
   AND  sa.fecha_pago between '{$pdf->ConvertirFechaYMD($inicio)}' AND '{$pdf->ConvertirFechaYMD($fin)}'";
$restotal_ =$db->query($consulta);  
$totales = $restotal_->fetch_assoc();
$total_salario       = $totales['total_salario_bruto'];
$total_comision      = $totales['total_comision'];
$total_sobretiempo   = $totales['total_sobretiempo'];
$total_bono          = $totales['total_bono'];
$total_altura        = $totales['total_altura'];
$total_otros         = $totales['total_otros'];
$total_vacac         = $totales['total_vacac'];
$total_xiii          = $totales['total_xiii'];
$total_util          = $totales['total_util'];
$total_liquida       = $totales['total_liquida'];
$total_licencia      = $totales['total_licencia'];
$total_gtorep        = $totales['total_gtorep'];
$total_otros_ing     = $totales['total_otros_ing'];
$total_adelanto      = $totales['total_adelanto'];
$total_s_s           = $totales['total_s_s'];
$total_s_e           = $totales['total_s_e'];
$total_islr          = $totales['total_islr'];
$total_acreedor_suma = $totales['total_acreedor_suma'];
$total_Neto          = $totales['total_Neto'];

$total_subtotal += $totales['total_salario_bruto']+$totales['total_comision']+$totales['total_sobretiempo']+$totales['total_bono']+$totales['total_altura']+$totales['total_otros']+$totales['total_vacac']+$totales['total_xiii']+$totales['total_gtorep'];
  $strsql= "SELECT COUNT(*) from salarios_acumulados where tipo_planilla='{$tipo_planilla}'";
  $result =$db->query($strsql); 
  $fila = $result->fetch_array(); 
  $num_total_registros = $fila[0];
 // $strsql= "SELECT a.*, b.personal_id, b.cedula, b.ficha, b.apenom, c.descrip as planilla, d.descrip as tipo_planilla, e.descrip as frecuencia "
 $strsql= "SELECT b.personal_id, b.cedula, b.ficha, b.apenom,a.*, c.codnom, c.descrip as planilla "
        . " FROM salarios_acumulados as a"
        . " LEFT JOIN nompersonal as b ON (a.cedula=b.cedula)"
        . " LEFT JOIN nom_nominas_pago as c ON (a.cod_planilla=c.codnom AND a.tipo_planilla=c.tipnom)"
        . " LEFT JOIN nomtipos_nomina as d ON (a.tipo_planilla=d.codtip)"
        . " LEFT JOIN nomfrecuencias as e ON (a.frecuencia_planilla=e.codfre)"

        . " WHERE a.tipo_planilla='{$tipo_planilla}'"
        . " AND  a.fecha_pago between '{$pdf->ConvertirFechaYMD($inicio)}' AND '{$pdf->ConvertirFechaYMD($fin)}' "
        . " GROUP BY b.ficha "
        . " ORDER BY a.fecha_pago ASC ";
  $res_empleados =$db->query($strsql);   
/*
$db     = new Database($_SESSION['bd']);
$sql_empleado = "SELECT * FROM nompersonal WHERE cedula = '{$cedula}'";
$empleado = $db->query($sql_empleado)->fetch_object();*/

$sql_empresa = "SELECT * from nomempresa";

$res_empresa =$db->query($sql_empresa);   
$empresa     = $res_empresa->fetch_assoc();

$pdf->empresa = $empresa['nom_emp'];

$pdf->fecha_inicio = $inicio;
$pdf->fecha_fin    = $fin;
$pdf->ficha        = $ficha;
$pdf->clave_ir     = $empleado->clave_ir;
$apenom            = $empleado->apenom;

$pdf->SetMargins(PDF_MARGIN_LEFT, 50, PDF_MARGIN_RIGHT);
$pdf->SetTitle('DETALLE DE SALARIOS ACUMULADOS');
$pdf->SetDisplayMode('fullpage','two');
$pdf->AddPage('L');
$pdf->SetFont('helvetica', '',7);
$pdf->SetY(44);

$ln1=6;
$border_fila = 1;
$width = 4;
$subtotal = 0;


while($row_empleado = $res_empleados->fetch_assoc()) {
	$consulta = "SELECT SUM(salario_bruto) as salario_bruto,
	SUM(vacac) as vacac,
	SUM(xiii) as xiii,
	SUM(gtorep) as gtorep,
	SUM(xiii_gtorep) as xiii_gtorep,
	SUM(bono) as bono,
	SUM(otros_ing) as otros_ing,
	SUM(prima) as prima,
	SUM(s_s) as s_s,
	SUM(s_e) as s_e,
	SUM(islr) as islr,
	SUM(islr_gr) as islr_gr,
	SUM(acreedor_suma) as acreedor_suma,
	SUM(Neto) as Neto
	from salarios_acumulados where cedula='{$row_empleado[cedula]}' AND tipo_planilla='{$tipo_planilla}'
       AND  fecha_pago between '{$pdf->ConvertirFechaYMD($inicio)}' AND '{$pdf->ConvertirFechaYMD($fin)}'";
	$res =$db->query($consulta);   

	while($row = $res->fetch_assoc()){
	    $Yheight = $pdf->GetY();
	    $subtotal            = 0;
	    /*$total_salario       += $row['salario_bruto'];
	    $total_comision      += $row['comision'];
	    $total_sobretiempo   += $row['sobretiempo'];
	    $total_bono          += $row['bono'];
	    $total_altura        += $row['altura'];
	    $total_otros         += $row['otros'];
	    $total_vacac         += $row['vacac'];
	    $total_xiii          += $row['xiii'];
	    $total_util          += $row['util'];
	    $total_liquida       += $row['liquida'];
	    $total_licencia      += $row['licencia'];
	    $total_gtorep        += $row['gtorep'];
	    $total_otros_ing     += $row['otros_ing'];
	    $total_adelanto      += $row['adelanto'];
	    $total_s_s           += $row['s_s'];
	    $total_s_e           += $row['s_e'];
	    $total_islr          += $row['islr'];
	    $total_acreedor_suma += $row['acreedor_suma'];
	    $total_Neto          += $row['Neto'];*/
	    $codnom = (!is_null($row['codnom']) ) ? $row['codnom'] : '' ;

	    $subtotal += $row['salario_bruto']+$row['comision']+$row['sobretiempo']+$row['bono']+$row['altura']+$row['otros']+$row['vacac']+$row['xiii']+$row['gtorep'];
	    $pdf->Cell($tam1["Fecha"],$width,$row_empleado['cedula'], $border_fila, $ln, $align='L', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
	    $pdf->Cell($tam1["Plan"],$width,$row_empleado['apenom'], $border_fila, $ln, $align='L', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
	    $pdf->Cell($tam1["Trans"],$width,$tipo_planilla, $border_fila, $ln, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
	    $pdf->Cell($tam1["salareg"],$width,$row['salario_bruto'], $border_fila, $ln, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
	    $pdf->Cell($tam1["comision"],$width,$row['comision'], $border_fila, $ln, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
	    $pdf->Cell($tam1["sobretiempo"],$width,$row['sobretiempo'], $border_fila, $ln, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
	    $pdf->Cell($tam1["bonoprod"],$width,$row['bono'], $border_fila, $ln, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
	    $pdf->Cell($tam1["altura"],$width,$row['altura'], $border_fila, $ln, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
	    $pdf->Cell($tam1["otros"],$width,$row['otros'], $border_fila, $ln, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
	    $pdf->Cell($tam1["vacacion"],$width,$row['vacac'], $border_fila, $ln, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
	    $pdf->Cell($tam1["decimo"],$width,$row['xiii'], $border_fila, $ln, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
	    $pdf->Cell($tam1["util"],$width,$row['util'], $border_fila, $ln, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
	    $pdf->Cell($tam1["Liq"],$width,$row['liquida'], $border_fila, $ln, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
	    $pdf->Cell($tam1["Lic"],$width,$row['licencia'], $border_fila, $ln, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
	    $pdf->Cell($tam1["SubTotal"],$width,$subtotal, $border_fila, $ln, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
	    $pdf->Cell($tam1["Gastorep"],$width,$row['gtorep'], $border_fila, $ln, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
	    $pdf->Cell($tam1["Otros"],$width,$row['otros_ing'], $border_fila, $ln, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
	    $pdf->Cell($tam1["Adelanto"],$width,$row['adelanto'], $border_fila, $ln, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
	    $pdf->Cell($tam1["SegSoc"],$width,$row['s_s'], $border_fila, $ln, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
	    $pdf->Cell($tam1["segeduc"],$width,$row['s_e'], $border_fila, $ln, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
	    $pdf->Cell($tam1["ISR"],$width,$row['islr'], $border_fila, $ln, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
	    $pdf->Cell($tam1["Acreed"],$width,$row['acreedor_suma'], $border_fila, $ln, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
	    $pdf->Cell($tam1["Neto"],$width,$row['Neto'], $border_fila, $ln, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
		$pdf->Ln(4);
		if( $pdf->GetY() >172)
		{
		    $pdf->AddPage();
		        $pdf->SetY(44);

		    $pdf->SetTextColor(0);
		    $pdf->SetFillColor(255, 255, 255);
		    $pdf->SetTextColor(0,0,0);
		    $pdf->SetDrawColor(100, 100, 100);
		    
		}

	}
}
 $border_total = 0;
$pdf->SetFont('helvetica', 'B',7);
    $pdf->Cell(($tam1["Fecha"]*4+6.3),$width,"Totales", $border_total, $ln, $align='C', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
$pdf->SetFont('helvetica', '',7);
    $pdf->Cell($tam1["salareg"],$width,$total_salario, $border_total, $ln, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $pdf->Cell($tam1["comision"],$width,$total_comision, $border_total, $ln, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $pdf->Cell($tam1["sobretiempo"],$width,$total_sobretiempo, $border_total, $ln, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $pdf->Cell($tam1["bonoprod"],$width,$total_bono, $border_total, $ln, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $pdf->Cell($tam1["altura"],$width,$total_altura, $border_total, $ln, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $pdf->Cell($tam1["otros"],$width,$total_otros, $border_total, $ln, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $pdf->Cell($tam1["vacacion"],$width,$total_vacac, $border_total, $ln, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $pdf->Cell($tam1["decimo"],$width,$total_xiii, $border_total, $ln, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $pdf->Cell($tam1["util"],$width,$total_util, $border_total, $ln, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $pdf->Cell($tam1["Liq"],$width,$total_liquida, $border_total, $ln, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $pdf->Cell($tam1["Lic"],$width,$total_licencia, $border_total, $ln, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $pdf->Cell($tam1["SubTotal"],$width,$total_subtotal, $border_total, $ln, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $pdf->Cell($tam1["Gastorep"],$width,$total_gtorep, $border_total, $ln, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $pdf->Cell($tam1["Otros"],$width,$total_otros_ing, $border_total, $ln, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $pdf->Cell($tam1["Adelanto"],$width,$total_adelanto, $border_total, $ln, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $pdf->Cell($tam1["SegSoc"],$width,$total_s_s, $border_total, $ln, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $pdf->Cell($tam1["segeduc"],$width,$total_s_e, $border_total, $ln, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $pdf->Cell($tam1["ISR"],$width,$total_islr, $border_total, $ln, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $pdf->Cell($tam1["Acreed"],$width,$total_acreedor_suma, $border_total, $ln, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);
    $pdf->Cell($tam1["Neto"],$width,$total_Neto, $border_total, $ln, $align='R', $fill,$link,$stretch,$ignore_min_height,$calign,$valign);


$nombre_archivo = "DETALLE_SALARIOS_ACUMULADOS_".$mes."-".$anio ;
ob_clean();
$pdf->Output($nombre_archivo.'.pdf','I');
?>
