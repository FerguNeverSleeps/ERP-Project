<?php
session_start();
error_reporting(0);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('America/Panama');

if (PHP_SAPI == 'cli')
	die('Este ejemplo solo se debe ejecutar desde un navegador Web');

include('../../lib/common.php');
//include('lib/php_excel.php');
require_once 'phpexcel/Classes/PHPExcel.php';

if(!isset($_GET['mes']) or !isset($_GET['anio']) or !isset($_REQUEST["fecha_inicio"]) or !isset($_REQUEST["fecha_fin"])){
	exit;
}

if(!extension_loaded('zip')){
  print "Se requiere de la extension php-zip.";
  exit;
}

//function fecha_sql($fecha)
//{
//	$fecha_tmp = explode("/", $fecha);
//	return $fecha_tmp[2]."-".$fecha_tmp[1]."-".$fecha_tmp[0];
//}

function letra_mes($n){
	switch($n){
		case '1': case '01': return "ENERO";
		case '2': case '02': return "FEBRERO";
		case '3': case '03': return "MARZO";
		case '4': case '04': return "ABRIL";
		case '5': case '05': return "MAYO";
		case '6': case '06': return "JUNIO";
		case '7': case '07': return "JULIO";
		case '8': case '08': return "AGOSTO";
		case '9': case '09': return "SEPTIEMBRE";
		case '10': return "OCTUBRE";
		case '11': return "NOVIEMBRE";
		case '12': return "DICIEMBRE";
	}
	return ""; 
}


function conceptos_suman_restan($conceptos){	
	$tmp=explode(",", $conceptos);
	$suman=[];
	$restan=[];
	for($i=0; $i < count($tmp); $i++){ 
		$tmp[$i]=trim($tmp[$i]);
		if(substr($tmp[$i], 0,1)=="-")
			$restan[]=trim($tmp[$i],"-");
		else
			$suman[]=$tmp[$i];
	}
	return [
		"conceptos_suman"  => implode(",",$suman),
		"conceptos_restan" => implode(",",$restan)
	];
}

$columnas_excel=[
    "A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z",

    "AA","AB","AC","AD","AE","AF","AG","AH","AI","AJ","AK","AL","AM","AN","AO","AP","AQ","AR","AS","AT","AU","AV","AW","AX","AY","AZ",
    "BA","BB","BC","BD","BE","BF","BG","BH","BI","BJ","BK","BL","BM","BN","BO","BP","BQ","BR","BS","BT","BU","BV","BW","BX","BY","BZ",
    "CA","CB","CC","CD","CE","CF","CG","CH","CI","CJ","CK","CL","CM","CN","CO","CP","CQ","CR","CS","CT","CU","CV","CW","CX","CY","CZ",
    "DA","DB","DC","DD","DE","DF","DG","DH","DI","DJ","DK","DL","DM","DN","DO","DP","DQ","DR","DS","DT","DU","DV","DW","DX","DY","DZ",
    "EA","EB","EC","ED","EE","EF","EG","EH","EI","EJ","EK","EL","EM","EN","EO","EP","EQ","ER","ES","ET","EU","EV","EW","EX","EY","EZ",
    "FA","FB","FC","FD","FE","FF","FG","FH","FI","FJ","FK","FL","FM","FN","FO","FP","FQ","FR","FS","FT","FU","FV","FW","FX","FY","FZ",
    "GA","GB","GC","GD","GE","GF","GG","GH","GI","GJ","GK","GL","GM","GN","GO","GP","GQ","GR","GS","GT","GU","GV","GW","GX","GY","GZ",
    "HA","HB","HC","HD","HE","HF","HG","HH","HI","HJ","HK","HL","HM","HN","HO","HP","HQ","HR","HS","HT","HU","HV","HW","HX","HY","HZ",
    "IA","IB","IC","ID","IE","IF","IG","IH","II","IJ","IK","IL","IM","IN","IO","IP","IQ","IR","IS","IT","IU","IV","IW","IX","IY","IZ",
    "JA","JB","JC","JD","JE","JF","JG","JH","JI","JJ","JK","JL","JM","JN","JO","JP","JQ","JR","JS","JT","JU","JV","JW","JX","JY","JZ",
    "KA","KB","KC","KD","KE","KF","KG","KH","KI","KJ","KK","KL","KM","KN","KO","KP","KQ","KR","KS","KT","KU","KV","KW","KX","KY","KZ",
    "LA","LB","LC","LD","LE","LF","LG","LH","LI","LJ","LK","LL","LM","LN","LO","LP","LQ","LR","LS","LT","LU","LV","LW","LX","LY","LZ",
    "MA","MB","MC","MD","ME","MF","MG","MH","MI","MJ","MK","ML","MM","MN","MO","MP","MQ","MR","MS","MT","MU","MV","MW","MX","MY","MZ",
    "NA","NB","NC","ND","NE","NF","NG","NH","NI","NJ","NK","NL","NM","NN","NO","NP","NQ","NR","NS","NT","NU","NV","NW","NX","NY","NZ",
    "OA","OB","OC","OD","OE","OF","OG","OH","OI","OJ","OK","OL","OM","ON","OO","OP","OQ","OR","OS","OT","OU","OV","OW","OX","OY","OZ",
    "PA","PB","PC","PD","PE","PF","PG","PH","PI","PJ","PK","PL","PM","PN","PO","PP","PQ","PR","PS","PT","PU","PV","PW","PX","PY","PZ",
    "QA","QB","QC","QD","QE","QF","QG","QH","QI","QJ","QK","QL","QM","QN","QO","QP","QQ","QR","QS","QT","QU","QV","QW","QX","QY","QZ",
    "RA","RB","RC","RD","RE","RF","RG","RH","RI","RJ","RK","RL","RM","RN","RO","RP","RQ","RR","RS","RT","RU","RV","RW","RX","RY","RZ",
    "SA","SB","SC","SD","SE","SF","SG","SH","SI","SJ","SK","SL","SM","SN","SO","SP","SQ","SR","SS","ST","SU","SV","SW","SX","SY","SZ",
    "TA","TB","TC","TD","TE","TF","TG","TH","TI","TJ","TK","TL","TM","TN","TO","TP","TQ","TR","TS","TT","TU","TV","TW","TX","TY","TZ",
    "UA","UB","UC","UD","UE","UF","UG","UH","UI","UJ","UK","UL","UM","UN","UO","UP","UQ","UR","US","UT","UU","UV","UW","UX","UY","UZ",
    "VA","VB","VC","VD","VE","VF","VG","VH","VI","VJ","VK","VL","VM","VN","VO","VP","VQ","VR","VS","VT","VU","VV","VW","VX","VY","VZ",
    "WA","WB","WC","WD","WE","WF","WG","WH","WI","WJ","WK","WL","WM","WN","WO","WP","WQ","WR","WS","WT","WU","WV","WW","WX","WY","WZ",
    "XA","XB","XC","XD","XE","XF","XG","XH","XI","XJ","XK","XL","XM","XN","XO","XP","XQ","XR","XS","XT","XU","XV","XW","XX","XY","XZ",
    "YA","YB","YC","YD","YE","YF","YG","YH","YI","YJ","YK","YL","YM","YN","YO","YP","YQ","YR","YS","YT","YU","YV","YW","YX","YY","YZ",
    "ZA","ZB","ZC","ZD","ZE","ZF","ZG","ZH","ZI","ZJ","ZK","ZL","ZM","ZN","ZO","ZP","ZQ","ZR","ZS","ZT","ZU","ZV","ZW","ZX","ZY","ZZ",

    "AAA","AAB","AAC","AAD","AAE","AAF","AAG","AAH","AAI","AAJ","AAK","AAL","AAM","AAN","AAO","AAP","AAQ","AAR","AAS","AAT","AAU","AAV","AAW","AAX","AAY","AAZ",
    "ABA","ABB","ABC","ABD","ABE","ABF","ABG","ABH","ABI","ABJ","ABK","ABL","ABM","ABN","ABO","ABP","ABQ","ABR","ABS","ABT","ABU","ABV","ABW","ABX","ABY","ABZ",
    "ACA","ACB","ACC","ACD","ACE","ACF","ACG","ACH","ACI","ACJ","ACK","ACL","ACM","ACN","ACO","ACP","ACQ","ACR","ACS","ACT","ACU","ACV","ACW","ACX","ACY","ACZ",
    "ADA","ADB","ADC","ADD","ADE","ADF","ADG","ADH","ADI","ADJ","ADK","ADL","ADM","ADN","ADO","ADP","ADQ","ADR","ADS","ADT","ADU","ADV","ADW","ADX","ADY","ADZ",
    "AEA","AEB","AEC","AED","AEE","AEF","AEG","AEH","AEI","AEJ","AEK","AEL","AEM","AEN","AEO","AEP","AEQ","AER","AES","AET","AEU","AEV","AEW","AEX","AEY","AEZ",
    "AFA","AFB","AFC","AFD","AFE","AFF","AFG","AFH","AFI","AFJ","AFK","AFL","AFM","AFN","AFO","AFP","AFQ","AFR","AFS","AFT","AFU","AFV","AFW","AFX","AFY","AFZ",
    "AGA","AGB","AGC","AGD","AGE","AGF","AGG","AGH","AGI","AGJ","AGK","AGL","AGM","AGN","AGO","AGP","AGQ","AGR","AGS","AGT","AGU","AGV","AGW","AGX","AGY","AGZ",
    "AHA","AHB","AHC","AHD","AHE","AHF","AHG","AHH","AHI","AHJ","AHK","AHL","AHM","AHN","AHO","AHP","AHQ","AHR","AHS","AHT","AHU","AHV","AHW","AHX","AHY","AHZ",
    "AIA","AIB","AIC","AID","AIE","AIF","AIG","AIH","AII","AIJ","AIK","AIL","AIM","AIN","AIO","AIP","AIQ","AIR","AIS","AIT","AIU","AIV","AIW","AIX","AIY","AIZ",
    "AJA","AJB","AJC","AJD","AJE","AJF","AJG","AJH","AJI","AJJ","AJK","AJL","AJM","AJN","AJO","AJP","AJQ","AJR","AJS","AJT","AJU","AJV","AJW","AJX","AJY","AJZ",
    "AKA","AKB","AKC","AKD","AKE","AKF","AKG","AKH","AKI","AKJ","AKK","AKL","AKM","AKN","AKO","AKP","AKQ","AKR","AKS","AKT","AKU","AKV","AKW","AKX","AKY","AKZ",
    "ALA","ALB","ALC","ALD","ALE","ALF","ALG","ALH","ALI","ALJ","ALK","ALL","ALM","ALN","ALO","ALP","ALQ","ALR","ALS","ALT","ALU","ALV","ALW","ALX","ALY","ALZ",
    "AMA","AMB","AMC","AMD","AME","AMF","AMG","AMH","AMI","AMJ","AMK","AML","AMM","AMN","AMO","AMP","AMQ","AMR","AMS","AMT","AMU","AMV","AMW","AMX","AMY","AMZ",
    "ANA","ANB","ANC","AND","ANE","ANF","ANG","ANH","ANI","ANJ","ANK","ANL","ANM","ANN","ANO","ANP","ANQ","ANR","ANS","ANT","ANU","ANV","ANW","ANX","ANY","ANZ",
    "AOA","AOB","AOC","AOD","AOE","AOF","AOG","AOH","AOI","AOJ","AOK","AOL","AOM","AON","AOO","AOP","AOQ","AOR","AOS","AOT","AOU","AOV","AOW","AOX","AOY","AOZ",
    "APA","APB","APC","APD","APE","APF","APG","APH","API","APJ","APK","APL","APM","APN","APO","APP","APQ","APR","APS","APT","APU","APV","APW","APX","APY","APZ",
    "AQA","AQB","AQC","AQD","AQE","AQF","AQG","AQH","AQI","AQJ","AQK","AQL","AQM","AQN","AQO","AQP","AQQ","AQR","AQS","AQT","AQU","AQV","AQW","AQX","AQY","AQZ",
    "ARA","ARB","ARC","ARD","ARE","ARF","ARG","ARH","ARI","ARJ","ARK","ARL","ARM","ARN","ARO","ARP","ARQ","ARR","ARS","ART","ARU","ARV","ARW","ARX","ARY","ARZ",
    "ASA","ASB","ASC","ASD","ASE","ASF","ASG","ASH","ASI","ASJ","ASK","ASL","ASM","ASN","ASO","ASP","ASQ","ASR","ASS","AST","ASU","ASV","ASW","ASX","ASY","ASZ",
    "ATA","ATB","ATC","ATD","ATE","ATF","ATG","ATH","ATI","ATJ","ATK","ATL","ATM","ATN","ATO","ATP","ATQ","ATR","ATS","ATT","ATU","ATV","ATW","ATX","ATY","ATZ",
    "AUA","AUB","AUC","AUD","AUE","AUF","AUG","AUH","AUI","AUJ","AUK","AUL","AUM","AUN","AUO","AUP","AUQ","AUR","AUS","AUT","AUU","AUV","AUW","AUX","AUY","AUZ",
    "AVA","AVB","AVC","AVD","AVE","AVF","AVG","AVH","AVI","AVJ","AVK","AVL","AVM","AVN","AVO","AVP","AVQ","AVR","AVS","AVT","AVU","AVV","AVW","AVX","AVY","AVZ",
    "AWA","AWB","AWC","AWD","AWE","AWF","AWG","AWH","AWI","AWJ","AWK","AWL","AWM","AWN","AWO","AWP","AWQ","AWR","AWS","AWT","AWU","AWV","AWW","AWX","AWY","AWZ",
    "AXA","AXB","AXC","AXD","AXE","AXF","AXG","AXH","AXI","AXJ","AXK","AXL","AXM","AXN","AXO","AXP","AXQ","AXR","AXS","AXT","AXU","AXV","AXW","AXX","AXY","AXZ",
    "AYA","AYB","AYC","AYD","AYE","AYF","AYG","AYH","AYI","AYJ","AYK","AYL","AYM","AYN","AYO","AYP","AYQ","AYR","AYS","AYT","AYU","AYV","AYW","AYX","AYY","AYZ",
    "AZA","AZB","AZC","AZD","AZE","AZF","AZG","AZH","AZI","AZJ","AZK","AZL","AZM","AZN","AZO","AZP","AZQ","AZR","AZS","AZT","AZU","AZV","AZW","AZX","AZY","AZZ"
];


$anio          = intval($_GET['anio']);
$mes           = intval($_GET['mes']);
$fecha_inicio  = $_GET['fecha_inicio'];
$fecha_fin     = $_GET['fecha_fin'];

$ffecha_fin    =$fecha_fin;
$ffecha_inicio =$fecha_inicio;

$fecha_fin=explode("/",$fecha_fin);
$fecha_fin=$fecha_fin[2]."-".$fecha_fin[1]."-".$fecha_fin[0];

$fecha_inicio=explode("/",$fecha_inicio);
$fecha_inicio=$fecha_inicio[2]."-".$fecha_inicio[1]."-".$fecha_inicio[0];

//$anio=2020;
//$mes=9;

$conexion= new bd($_SESSION['bd']);

$sql = "SELECT e.nom_emp as nombre, e.rif, e.tel_emp as telefono, e.dir_emp as direccion, e.ciu_emp as ciudad, 
               e.edo_emp, e.imagen_izq as logo
		FROM   nomempresa e";
$res=$conexion->query($sql);
$empresa=$res->fetch_array();
$logo=$empresa['logo'];


if(!mb_detect_encoding($empresa['nombre'],["UTF-8"],true))
	$empresa['nombre']=utf8_encode($empresa['nombre']);

//cargar configuracion de reporte
$sql1 = "select * from config_reportes_planilla where id = 7";
$res=$conexion->query($sql1);
$configReporte=$res->fetch_array();
$sql_reporte = $configReporte['sql_reporte'];


$sql_reporte=str_replace("{anio}", $anio, $sql_reporte);
$sql_reporte=str_replace("{mes}", $mes, $sql_reporte);
$sql_reporte=str_replace("{fecha_inicio}", $fecha_inicio, $sql_reporte);
$sql_reporte=str_replace("{fecha_fin}", $fecha_fin, $sql_reporte);

if(!isset($configReporte['id'])){
	print "config_reportes_planilla id=7 no encontrado (el comprobante depende del query definido en id=7)";
	exit;
}

//cargar configuracion de reporte (columnas)
$sql1 = "select * from config_reportes_planilla_columnas where id_reporte = '".$configReporte['id']."'";
$res=$conexion->query($sql1);
$configReporteColumnas=[];
while($tmp=$res->fetch_array()){
	$configReporteColumnas[]=$tmp;
}

$sql_ejecutar=str_replace("{centro_costo}", "1", $sql_reporte);
//hacer los reemplazos de conceptos en sql_reporte del encabezado de reportes
for($i=0; $i <count($configReporteColumnas[$i]); $i++){ 
	if($configReporteColumnas[$i]["conceptos"]){
		$tmp=conceptos_suman_restan($configReporteColumnas[$i]["conceptos"]);
		$conceptos_suman  = $tmp["conceptos_suman"];
		$conceptos_restan = $tmp["conceptos_restan"];
		if(!$conceptos_suman)  $conceptos_suman  ="-1";
		if(!$conceptos_restan) $conceptos_restan ="-1";

		$sql_ejecutar = str_replace("{".$configReporteColumnas[$i]["nombre_corto"]."}",        "$conceptos_suman",   $sql_ejecutar);
		$sql_ejecutar = str_replace("{".$configReporteColumnas[$i]["nombre_corto"]."_restan}", "$conceptos_restan", $sql_ejecutar);
	}
	else{
		$sql_ejecutar = str_replace("{".$configReporteColumnas[$i]["nombre_corto"]."}",        "-1",   $sql_ejecutar);
		$sql_ejecutar = str_replace("{".$configReporteColumnas[$i]["nombre_corto"]."_restan}", "-1", $sql_ejecutar);
	}
}

$res2=$conexion->query($sql_ejecutar);
$data=$res2->fetch_array();

//print_r($data);exit;
//NOTA - OBSERVACION 
//REALIZAR ACA LAS MISMA OPERACION QUE SE HACEN EN EL EXCEL DEL REPORTE ID=7 PARA EL CALCULO DEL TOTAL
$porcentaje_ss=12.25;
$porcentaje_se=1.50;
$porcentaje_riesgo=0.98;

$total_salario=$data["salario1"]+$data["salario2"]+$data["liquidaciones"]+$data["vacaciones"]+$data["expediente"]
        +$data["gastos_representacion"];
$ss=$total_salario*($porcentaje_ss/100)+$data["xiii"]*0.1075;
$se=($total_salario-$data["expediente"]-$data["gastos_representacion"])*($porcentaje_se/100);
$riesgo=$total_salario*($porcentaje_riesgo/100);

$monto_total=$total_salario+$data["xiii"]+$ss+$se+$riesgo+$data["prima_antiguedad"]+$data["indemnizacion"]+$data["preaviso"]
        +$data["bonificacion"]+$data["viatico"];


$objPHPExcel = new PHPExcel();

$objPHPExcel->getProperties()->setCreator("AMAXONIA")
							 ->setLastModifiedBy("AMAXONIA")
							 ->setTitle("Comprobante Salarios Administrativo");


$sheet=$objPHPExcel->getActiveSheet();

$objPHPExcel->getDefaultStyle()->getFont()->setName('Calibri');	
$objPHPExcel->getDefaultStyle()->getFont()->setSize(10);


$ln=1;
$sheet->mergeCells("A{$ln}:F{$ln}");
$sheet->setCellValue("A{$ln}", $empresa['nombre']);
$ln++;
$sheet->mergeCells("A{$ln}:F{$ln}");
$sheet->setCellValue("A{$ln}", "Asiento Contable");


$ln+=2;
$sheet->mergeCells("A{$ln}:F{$ln}");
$sheet->setCellValue("A{$ln}", "DISTRIBUCIÓN DE SALARIOS ADMINISTRATIVOS - ".$empresa['nombre']);	
$sheet->getStyle("A{$ln}:F{$ln}")
        ->getFill()
        ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
        ->getStartColor()
        ->setRGB('FFFF00');

$sheet->getStyle("A1:A4")->getFont()->setBold(true);
$sheet->getStyle("A1:A4")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('A1:A2')->getFont()->setSize(12);


$ln=6;
//$sheet->getRowDimension($ln)->setRowHeight(60);
$sheet->setCellValue("A{$ln}", "Fecha");
$sheet->setCellValue("B{$ln}", "Número Cuenta");
$sheet->setCellValue("C{$ln}", "Nombre Cuenta");
$sheet->setCellValue("D{$ln}", "Concepto");
$sheet->setCellValue("E{$ln}", "Debito");
$sheet->setCellValue("F{$ln}", "Crédito");
$sheet->getStyle("A{$ln}:F{$ln}")->getFont()->setBold(true);
$sheet->getStyle("A{$ln}:F{$ln}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(50);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(50);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(12);
//agregar titulos de las columnas
$col_index=2;//C


$ctacon_credito="";
$ctacon_credito_descrip="";
$ctacon_detalle_debito="";
$ctacon_detalle_debito_descrip="";
$ctacon_detalle_credito="";
$ctacon_detalle_credito_descrip="";

switch($_SESSION["bd"]) {
	case 'apjv_rrhh':
		$ctacon_credito="411601";
		$ctacon_credito_descrip="INGRESO POR REEMBOLSO ADMINISTRATIVOS";

		$ctacon_detalle_debito="611313.01";
		$ctacon_detalle_debito_descrip="GASTO POR REEMBOLSO-ADMIN PJV, S. A. (ADMINISTRATIVOS)";

		$ctacon_detalle_credito="112301";
		$ctacon_detalle_credito_descrip=$empresa['nombre'];
		break;
}



$ln++;
$ln_inicio=$ln;
$n=1;

$ffecha=$_REQUEST["fecha_fin"];

$add="";
$sql = "
	SELECT
		nn.codorg,
		nn.porcentaje,
		upper(nn.markar) markar,
		upper(nn.descripcion_completa) descripcion_completa,
		upper(nn.descrip) descrip,
		nn.ee ctacon,
		cc.Descrip as cuenta_contable
	FROM nomnivel1 nn		
		LEFT JOIN cwconcue AS cc   ON nn.ee=cc.Cuenta
	WHERE  codorg!=1 AND nn.estatus!=0
	ORDER BY nn.ee";


$res=$conexion->query($sql);

//$monto_total=100000;

$centro_costo=[];
while($row=$res->fetch_array()){	
	if(!mb_detect_encoding($row["markar"],["UTF-8"],true))
		$row["markar"]=utf8_encode($row["markar"]);
	if(!mb_detect_encoding($row["descripcion_completa"],["UTF-8"],true))
		$row["descripcion_completa"]=utf8_encode($row["descripcion_completa"]);
	if(!mb_detect_encoding($row["descrip"],["UTF-8"],true))
		$row["descrip"]=utf8_encode($row["descrip"]);
	if(!mb_detect_encoding($row["cuenta_contable"],["UTF-8"],true))
		$row["cuenta_contable"]=utf8_encode($row["cuenta_contable"]);

	$monto=$row["porcentaje"]*$monto_total/100;
	$row["monto"]=$monto;
	$centro_costo[]=$row;

	$sheet->setCellValue("A{$ln}", $ffecha);
	$sheet->setCellValueExplicit("B{$ln}", $row["ctacon"],PHPExcel_Cell_DataType::TYPE_STRING);
	$sheet->setCellValue("C{$ln}", trim($row["descrip"],"- "));
	$sheet->setCellValue("D{$ln}", "CXC - ".trim($row["descrip"],"- "));
	$sheet->setCellValue("E{$ln}", $monto);
	$sheet->setCellValue("F{$ln}", "");	
	$ln++;
}

$sheet->setCellValue("A{$ln}", $ffecha);
$sheet->setCellValueExplicit("B{$ln}", $ctacon_credito,PHPExcel_Cell_DataType::TYPE_STRING);
$sheet->setCellValue("C{$ln}", $ctacon_credito_descrip);
$sheet->setCellValue("D{$ln}", "INGRESO POR REEMBOLSO");
$sheet->setCellValue("E{$ln}", "");
$sheet->setCellValue("F{$ln}", $monto_total);	
$ln++;
$sheet->getStyle("E".$ln_inicio.":F{$ln}")->getNumberFormat()->setFormatCode('#,##0.00');


$border=array(
  'borders' => array(
    'allborders' => array(
      'style' => PHPExcel_Style_Border::BORDER_THIN,
      'color' => array('rgb' => '000000')
    )
  )
);

$sheet->getStyle("A".($ln_inicio-1).":F".($ln-1))->applyFromArray($border);
$sheet->getStyle("A".($ln_inicio-1).":B".($ln-1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

//detalle (comprobantes separados)

//generar primer excel

$uid=uniqid();
$ruta="excel/tmp/{$uid}";
mkdir($ruta,0777, TRUE);

$filename = "comprobante_salarios_administrativo_".$anio."_".$mes;
$writer = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$writer->setPreCalculateFormulas(true);
ob_clean();
$writer->save("{$ruta}/{$filename}.xlsx");
//$writer->save('php://output');

//exit;



/*
$ln+=2;
$sheet->mergeCells("A{$ln}:F{$ln}");
$sheet->setCellValue("A{$ln}", "YO TE PAGO DE SALARIOS-SUCURSALES");	
$sheet->getStyle("A{$ln}:F{$ln}")
        ->getFill()
        ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
        ->getStartColor()
        ->setRGB('FFFF00');

$sheet->getStyle("A$ln")->getFont()->setBold(true);
$sheet->getStyle("A$ln")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
*/
//$sheet->getStyle("A$ln:F$ln")->applyFromArray($border);
//$sheet->getStyle("A$ln")->getFont()->setSize(12);
$ln++;

for($i=0; $i<count($centro_costo); $i++){ 

	$ln=1;
	$objPHPExcel = new PHPExcel();

	$objPHPExcel->getProperties()->setCreator("AMAXONIA")
								 ->setLastModifiedBy("AMAXONIA")
								 ->setTitle("Comprobante Salarios Administrativo");


	$sheet=$objPHPExcel->getActiveSheet();

	$objPHPExcel->getDefaultStyle()->getFont()->setName('Calibri');	
	$objPHPExcel->getDefaultStyle()->getFont()->setSize(10);


	$ln=1;
	$sheet->mergeCells("A{$ln}:F{$ln}");
	$sheet->setCellValue("A{$ln}", $empresa['nombre']);
	$ln++;
	$sheet->mergeCells("A{$ln}:F{$ln}");
	$sheet->setCellValue("A{$ln}", "Asiento Contable");


	$ln+=2;
	$sheet->mergeCells("A{$ln}:F{$ln}");
	$sheet->setCellValue("A{$ln}", "YO TE PAGO DE SALARIOS - ".$centro_costo[$i]["descrip"]);	
	$sheet->getStyle("A{$ln}:F{$ln}")
	        ->getFill()
	        ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
	        ->getStartColor()
	        ->setRGB('FFFF00');

	$sheet->getStyle("A1:A4")->getFont()->setBold(true);
	$sheet->getStyle("A1:A4")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$sheet->getStyle('A1:A2')->getFont()->setSize(12);


	$ln++;
	$sheet->mergeCells("A{$ln}:F{$ln}");
	$sheet->setCellValue("A{$ln}", $centro_costo[$i]["descrip"]);	
	$sheet->getStyle("A{$ln}:F{$ln}")->getFont()->setBold(true);
	$sheet->getStyle("A{$ln}:F{$ln}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	

	$ln=6;
	//$sheet->getRowDimension($ln)->setRowHeight(60);
	$sheet->setCellValue("A{$ln}", "Fecha");
	$sheet->setCellValue("B{$ln}", "Número Cuenta");
	$sheet->setCellValue("C{$ln}", "Nombre Cuenta");
	$sheet->setCellValue("D{$ln}", "Concepto");
	$sheet->setCellValue("E{$ln}", "Debito");
	$sheet->setCellValue("F{$ln}", "Crédito");
	$sheet->getStyle("A{$ln}:F{$ln}")->getFont()->setBold(true);
	$sheet->getStyle("A{$ln}:F{$ln}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

	$sheet->getColumnDimension('A')->setWidth(15);
	$sheet->getColumnDimension('B')->setWidth(20);
	$sheet->getColumnDimension('C')->setWidth(50);
	$sheet->getColumnDimension('D')->setWidth(50);
	$sheet->getColumnDimension('E')->setWidth(12);
	$sheet->getColumnDimension('F')->setWidth(12);

	$ln++;

	$sheet->setCellValue("A{$ln}", $ffecha);
	$sheet->setCellValueExplicit("B{$ln}", $ctacon_detalle_debito,PHPExcel_Cell_DataType::TYPE_STRING);
	$sheet->setCellValue("C{$ln}", $ctacon_detalle_debito_descrip);
	$sheet->setCellValue("D{$ln}", "GASTO POR REEMBOLSO - ".$centro_costo[$i]["descrip"]);
	$sheet->setCellValue("E{$ln}", $centro_costo[$i]["monto"]);
	$sheet->setCellValue("F{$ln}", "");	
	$ln++;

	$sheet->setCellValue("A{$ln}", $ffecha);
	$sheet->setCellValueExplicit("B{$ln}", $ctacon_detalle_credito,PHPExcel_Cell_DataType::TYPE_STRING);
	$sheet->setCellValue("C{$ln}", $ctacon_detalle_credito_descrip);
	$sheet->setCellValue("D{$ln}", "CXP - ".$centro_costo[$i]["descrip"]);
	$sheet->setCellValue("E{$ln}", "");
	$sheet->setCellValue("F{$ln}", $centro_costo[$i]["monto"]);	

	$sheet->getStyle("A".($ln-2).":B".($ln))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$sheet->getStyle("E".($ln-2).":F".($ln))->getNumberFormat()->setFormatCode('#,##0.00');
	$sheet->getStyle("A".($ln-2).":F".($ln))->applyFromArray($border);


	$writer = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$writer->setPreCalculateFormulas(true);
	ob_clean();
	$writer->save("{$ruta}/{$filename}__".$centro_costo[$i]["markar"].".xlsx");

	$ln+=3;
}

class Zip extends ZipArchive {
  
  public function addDirectory($dir,$localname="",$dir_original="") {
    if(!$dir_original)   
      $dir_original=$dir;    
    foreach(glob($dir . '/*') as $file) {
      if(is_dir($file))
        $this->addDirectory($file,$localname,$dir_original);
      else
        $this->addFile($file,str_replace($dir_original,$localname,$file));      
    }
  }
}

$zip = new Zip();
if (!$zip->open("{$ruta}.zip", ZIPARCHIVE::CREATE)) {
  print "Error al crear zip<br>Verifique los permisos sobre la carpeta nomina/paginas/excel/tmp/";
  exit;
}

$zip->addDirectory("{$ruta}","$filename");
$zip->close();

ob_clean();
header("Content-Description: File Transfer");
header("Content-Type: application/octet-stream");
header("Content-Disposition: attachment; filename=".$filename.".zip");
header("Expires: 0");
header("Cache-Control: must-revalidate");
header("Pragma: public");
header("Content-Length: " . filesize("{$ruta}.zip"));
readfile("{$ruta}.zip");
?>