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

if(!isset($_GET['mes']) or !isset($_GET['anio']) or !isset($_REQUEST["codcon"]) or !isset($_REQUEST["fecha_fin"])){
	exit;
}

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

$ffecha_fin =$fecha_fin;

$fecha_fin=explode("/",$fecha_fin);
$fecha_fin=$fecha_fin[2]."-".$fecha_fin[1]."-".$fecha_fin[0];
//$anio=2020;
//$mes=9;

$conexion= new bd($_SESSION['bd']);

$sql = "SELECT e.nom_emp as nombre, e.rif, e.tel_emp as telefono, e.dir_emp as direccion, e.ciu_emp as ciudad, 
               e.edo_emp, e.imagen_izq as logo
		FROM   nomempresa e";
$res=$conexion->query($sql);
$empresa=$res->fetch_array();
$logo=$empresa['logo'];


$sql_columnas="
	SELECT 	
		codcon, 
		descrip
	FROM   nomconceptos 
	WHERE  codcon in (".$_REQUEST["codcon"].")
	ORDER BY codcon ASC
";


$res=$conexion->query($sql_columnas);
$configReporteColumnas=[];
while($tmp=$res->fetch_array()){

	if(!mb_detect_encoding($tmp['descrip'],["UTF-8"],true))
		$tmp['descrip']=utf8_encode($tmp['descrip']);

	$configReporteColumnas[]=$tmp;
}

//print_r($configReporteColumnas);exit;


$objPHPExcel = new PHPExcel();

$objPHPExcel->getProperties()->setCreator("AMAXONIA")
							 ->setLastModifiedBy("AMAXONIA")
							 ->setTitle("Distribucion Otros Beneficios");


$sheet=$objPHPExcel->getActiveSheet();


if(!mb_detect_encoding($empresa['nombre'],["UTF-8"],true))
	$empresa['nombre']=utf8_encode($empresa['nombre']);






$ln=4;
$sheet->getRowDimension($ln)->setRowHeight(60);
$sheet->setCellValue("A{$ln}", "Jod ID");
$sheet->setCellValue("B{$ln}", "Nombre");

$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(50);
//agregar titulos de las columnas
$col_index=2;//C

$ultima_columna=$columnas_excel[$col_index+count($configReporteColumnas)];

for($i=0; $i<count($configReporteColumnas); $i++){ 	
	$columna=$columnas_excel[$col_index];

	if(!mb_detect_encoding($configReporteColumnas[$i]['descrip'],["UTF-8"],true))
		$configReporteColumnas[$i]['descrip']=utf8_encode($configReporteColumnas[$i]['descrip']);

	$sheet->getStyle("{$columna}{$ln}")->getFont()->setSize(9);
	$sheet->getColumnDimension($columna)->setWidth(15);
	$sheet->setCellValue("{$columna}{$ln}", $configReporteColumnas[$i]['descrip']);
	$sheet->getStyle("{$columna}{$ln}")->getAlignment()->setWrapText(true);
	$col_index++;
}
$sheet->getStyle("A".$ln.":{$ultima_columna}".$ln)->getFont()->setBold(true);
$sheet->getStyle("A".$ln.":{$ultima_columna}".$ln)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);



$columna=$columnas_excel[$col_index];
$ultima_columna=$columna;
$penultima_columna=$columnas_excel[$col_index-1];


$sheet->setCellValue("{$columna}{$ln}", "TOTAL");
$sheet->getStyle("{$columna}{$ln}")->getFont()->setSize(9);
$sheet->getColumnDimension("{$columna}")->setWidth(15);


$ln=2;
if($ultima_columna<"F"){
	$sheet->mergeCells("C{$ln}:F{$ln}");
}
else{
	$sheet->mergeCells("C{$ln}:{$ultima_columna}{$ln}");
}
$sheet->setCellValue("C{$ln}", "Saldos de Colaboradores de Cuentas por Cobrar al $ffecha_fin");
$sheet->getStyle("C{$ln}")->getFont()->setBold(true);
$sheet->getStyle("C{$ln}")->getFont()->setSize(14);
$sheet->getStyle("C{$ln}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getRowDimension($ln)->setRowHeight(50);


$ln=5;
$ln_inicio=$ln;
$n=1;

$add="";
if(isset($_REQUEST["nomnivel1"]) and $_REQUEST["nomnivel1"]){
	$add.=" and F.codnivel1 in (".$_REQUEST["nomnivel1"].")";
}

$sql="
	select 
		ficha,
		apenom
	from
		nompersonal
	where
		estado!='Egresado'
	order by
		ficha
";

$res=$conexion->query($sql);
while($persona=$res->fetch_array()){	
	$sw_econtro=false;
	$col_index=2;//C
	for($i=0; $i<count($configReporteColumnas); $i++){ 	
		$columna=$columnas_excel[$col_index];

		$sql="
			select 
				PD.salfinal monto
			from 
				nomprestamos_detalles PD,
				nom_movimientos_nomina M
			where
				PD.numpre=M.numpre and
				PD.numcuo=M.numcuo and
				PD.ficha=M.ficha and
				PD.ficha='".$persona["ficha"]."' and
				M.codcon = '".$configReporteColumnas[$i]["codcon"]."' and
				PD.estadopre='Cancelada' and
				PD.fechaven<='$fecha_fin'
				$add
			order by 
				PD.fechaven desc
			limit 1

		";

		$sql="
			select 
				sum(PD.montocuo) monto
			from nomprestamos_detalles PD 
				left join nomprestamos_cabecera PC on PC.numpre=PD.numpre and PC.ficha=PD.ficha
				left join nomprestamos P on P.codigopr=PC.codigopr
				left join nompersonal F on F.ficha=PD.ficha
			where
				PC.estadopre='Pendiente' and
				PD.ficha='".$persona["ficha"]."' and
				P.formula = '".$configReporteColumnas[$i]["codcon"]."' and
				PD.estadopre='Pendiente' and
				PD.fechaven>'$fecha_fin'
				$add				
		";
		//print $sql;exit;
		$res2=$conexion->query($sql);
		$tmp=$res2->fetch_array();

		$valor="";
		if(isset($tmp["monto"]) and $tmp["monto"]>0){
			$valor=$tmp["monto"];
			//print "{$columna}{$ln}:  $valor";
			//print_r($tmp["monto"]);exit;
			//print "exxxxx";print $sql;exit;
			$sw_econtro=true;
			$sheet->setCellValue("{$columna}{$ln}", $valor);
		}

		$col_index++;
	}

	if($sw_econtro){
		if(!mb_detect_encoding($persona["apenom"],["UTF-8"],true))
			$persona["apenom"]=utf8_encode($persona["apenom"]);

		$sheet->setCellValueExplicit("A{$ln}", str_pad($persona["ficha"],6,'0',STR_PAD_LEFT),PHPExcel_Cell_DataType::TYPE_STRING);
		$sheet->setCellValue("B{$ln}", $persona["apenom"]);
		$sheet->setCellValue("{$ultima_columna}{$ln}", "=SUM(C{$ln}:{$penultima_columna}{$ln})");
		$ln++;
	}

}

if($ln>$ln_inicio){
	$sheet->setCellValue("B$ln","Total");
	$sheet->getStyle("B$ln")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
	//totalizacion
	$col_index=2;//C
	for($i=0; $i <count($configReporteColumnas); $i++){ 
		$columna=$columnas_excel[$col_index];

		$sheet->setCellValue($columna.$ln,"=SUM(".$columna."$ln_inicio:".$columna.($ln-1).")");
		$col_index++;
	}
	$columna=$columnas_excel[$col_index];
	$sheet->setCellValue($columna.$ln,"=SUM(".$columna."$ln_inicio:".$columna.($ln-1).")");

	$sheet->getStyle("A".$ln.":{$ultima_columna}".$ln)->getFont()->setBold(true);


	$sheet->getStyle("C".$ln_inicio.":{$ultima_columna}$ln")->getFont()->setSize(11);
	$sheet->getStyle("C".$ln_inicio.":{$ultima_columna}$ln")->getNumberFormat()->setFormatCode('#,##0.00');
	$sheet->getStyle("C".$ln_inicio.":{$ultima_columna}$ln")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

	$sheet->getStyle("{$ultima_columna}".$ln_inicio.":{$ultima_columna}".$ln)->getFont()->setBold(true);
	$sheet->getStyle("A".$ln_inicio.":A$ln")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);	
}

$border=array(
  'borders' => array(
    'allborders' => array(
      'style' => PHPExcel_Style_Border::BORDER_THIN,
      'color' => array('rgb' => '000000')
    )
  )
);

$sheet->getStyle("A".($ln_inicio-1).":{$ultima_columna}".$ln)->applyFromArray($border);


$filename = "saldo_colaboradores_".$anio."_".$mes;

// Redirect output to a client’s web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"');
header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header ('Pragma: public'); // HTTP/1.0

$writer = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$writer->setPreCalculateFormulas(true);

/* Limpiamos el búfer */
ob_clean();
$writer->save('php://output');
?>