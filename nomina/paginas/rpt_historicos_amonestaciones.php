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

if(!isset($_GET['mes']) or !isset($_GET['anio'])){
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

$anio = intval($_GET['anio']);
$mes  = $_GET['mes'];

//$anio=2020;
//$mes=9;

$conexion= new bd($_SESSION['bd']);

$sql = "SELECT e.nom_emp as nombre, e.rif, e.tel_emp as telefono, e.dir_emp as direccion, e.ciu_emp as ciudad, 
               e.edo_emp, e.imagen_izq as logo
		FROM   nomempresa e";
$res=$conexion->query($sql);
$empresa=$res->fetch_array();
$logo=$empresa['logo'];



$objPHPExcel = new PHPExcel();
//$objPHPExcel = PHPExcel_IOFactory::load("plantillas/historicos_amonestaciones.xlsx");

$objPHPExcel->getProperties()->setCreator("AMAXONIA")
							 ->setLastModifiedBy("AMAXONIA")
							 ->setTitle("Historico Amonestaciones");

$objPHPExcel->getDefaultStyle()->getFont()->setName('Times New Roman');
//$objPHPExcel->getDefaultStyle()->getFont()->setSize(11);

$sheet=$objPHPExcel->getActiveSheet();


if(!mb_detect_encoding($empresa['nombre'],["UTF-8"],true))
	$empresa['nombre']=utf8_encode($empresa['nombre']);




$sheet->getColumnDimension("A")->setWidth(5);
$sheet->getColumnDimension("B")->setWidth(50);
$sheet->getColumnDimension("C")->setWidth(40);

$columna_mes=[];

$ln=5;
$col_width=6;

$col_index=3;//D
for($i=1; $i <=12 ; $i++) { 
	$letra_inicio=$columnas_excel[$col_index];
	$columna_mes["$i"]["81"]=$columnas_excel[$col_index];//REGISTRO DE INCIDENCIA
	$sheet->setCellValue($columnas_excel[$col_index].($ln+1), "R");
	$sheet->getColumnDimension($columnas_excel[$col_index])->setWidth($col_width);

	$col_index++;
	$columna_mes["$i"]["5"]=$columnas_excel[$col_index];//AMONESTACIONES
	$sheet->setCellValue($columnas_excel[$col_index].($ln+1), "A");
	$sheet->getColumnDimension($columnas_excel[$col_index])->setWidth($col_width);

	$col_index++;
	$letra_fin=$columnas_excel[$col_index];
	$columna_mes["$i"]["T"]=$columnas_excel[$col_index];
	$sheet->setCellValue($columnas_excel[$col_index].($ln+1), "Total");
	$sheet->getColumnDimension($columnas_excel[$col_index])->setWidth($col_width);

	$col_index++;


	$sheet->mergeCells("{$letra_inicio}{$ln}:{$letra_fin}{$ln}");
	$sheet->setCellValue("{$letra_inicio}{$ln}", letra_mes($i));
}

$col_index_total=$col_index;

$letra_inicio=$columnas_excel[$col_index];
$sheet->setCellValue($columnas_excel[$col_index].($ln+1), "RI");
$sheet->getColumnDimension($columnas_excel[$col_index])->setWidth($col_width);
$col_index++;

$sheet->setCellValue($columnas_excel[$col_index].($ln+1), "A");
$sheet->getColumnDimension($columnas_excel[$col_index])->setWidth($col_width);
$col_index++;

$letra_fin=$columnas_excel[$col_index];
$sheet->setCellValue($columnas_excel[$col_index].($ln+1), "Total");
$sheet->getColumnDimension($columnas_excel[$col_index])->setWidth($col_width);
$col_index++;

$sheet->mergeCells("{$letra_inicio}{$ln}:{$letra_fin}{$ln}");
$sheet->setCellValue("{$letra_inicio}{$ln}", "TOTAL");

$col_final=$letra_fin;

$sheet->getStyle("A{$ln}:$col_final".($ln+1))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('AAFFFFCC');
$sheet->getStyle("A{$ln}:$col_final".($ln+1))->getFont()->setBold(true);
$sheet->getStyle("A{$ln}:$col_final".($ln+1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);


$sheet->mergeCells("A3:{$col_final}4");
$sheet->setCellValue("A3", "HISTORICO DE LLAMADOS DE ATENCION O AMONESTACION   ".$anio."");
$sheet->getStyle("A3:{$col_final}4")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('AAFFFF99');
$sheet->getStyle("A3:{$col_final}4")->getFont()->setBold(true);
$sheet->getStyle("A3:{$col_final}4")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);


$add="";
if($mes){
	$add.=" and MONTH(E.fecha) in ($mes)";
}

/*
$sql="
	select 
		P.personal_id,
		P.codnivel1,
		N1.descrip nivel1,
		P.apenom,
		ifnull(des_car,'N/A') cargo,
		MONTH(E.fecha) mes,
		E.tipo,		
		count(*) n
	FROM
		nompersonal P
			left join nomcargos C on C.cod_cargo=P.codcargo,
		nomnivel1 N1,
		expediente E
	WHERE 
		N1.codorg=P.codnivel1 and
		P.cedula=E.cedula and
		E.tipo in (5,81) and
		YEAR(E.fecha)=$anio
		$add
	GROUP BY
		personal_id,
		apenom,
		cargo,
		codnivel1,
		nivel1,
		mes,
		E.tipo
	ORDER BY
		N1.markar,
		apenom
";
*/
$sql="
	select 
		P.personal_id,
		P.codnivel1,
		N1.descrip nivel1,
		P.apenom,
		ifnull(des_car,'N/A') cargo,
		MONTH(E.fecha) mes,
		E.tipo,		
		if(E.tipo in (5,81), count(E.tipo),0) n
	FROM
		nompersonal P
			left join nomcargos C on C.cod_cargo=P.codcargo
			left join nomnivel1 N1 on N1.codorg=P.codnivel1 
			left join expediente E on P.cedula=E.cedula and E.tipo in (5,81) and YEAR(E.fecha)=$anio $add
	WHERE 
		P.estado<>'Egresado'
	GROUP BY
		personal_id,
		apenom,
		cargo,
		codnivel1,
		nivel1,
		mes,
		E.tipo
	ORDER BY
		N1.markar,
		apenom 
";

//print $sql;exit;

$nivel="";
$persona="";

$ln=6;
$ln_inicio=$ln+1;
$c_nivel=1;
$c_persona=1;

$ln_inicio_centro_costo=$ln_inicio;

$total_mes=[];
$totalgeneral_mes=[];
$ln_total_centro_costo=[];

$res=$conexion->query($sql);
while($row=$res->fetch_array()){
	if($nivel!=$row["codnivel1"]){
		if(!mb_detect_encoding($row['nivel1'],["UTF-8"],true))
			$row['nivel1']=utf8_encode($row['nivel1']);

		if($nivel!=""){//omitir la primera vez
			//crear linea de totales
			$sheet->setCellValue("C{$ln}", "Total");
			$sheet->getStyle("A{$ln}:{$col_final}{$ln}")->getFont()->setBold(true);
			$sheet->getStyle("A{$ln}:{$col_final}{$ln}")->getFont()->setSize(12);

			
			$col_index=3;//D
			for($i=1; $i <=12 ; $i++) { 	
				$sheet->setCellValue($columnas_excel[$col_index].($ln), "=SUM(".$columnas_excel[$col_index].$ln_inicio_centro_costo.":".$columnas_excel[$col_index].($ln-1).")");
				$col_index++;
				$sheet->setCellValue($columnas_excel[$col_index].($ln), "=SUM(".$columnas_excel[$col_index].$ln_inicio_centro_costo.":".$columnas_excel[$col_index].($ln-1).")");
				$col_index++;
				$sheet->setCellValue($columnas_excel[$col_index].($ln), "=SUM(".$columnas_excel[$col_index].$ln_inicio_centro_costo.":".$columnas_excel[$col_index].($ln-1).")");
				$col_index++;
			}
			$sheet->setCellValue($columnas_excel[$col_index].($ln), "=SUM(".$columnas_excel[$col_index].$ln_inicio_centro_costo.":".$columnas_excel[$col_index].($ln-1).")");
			$col_index++;
			$sheet->setCellValue($columnas_excel[$col_index].($ln), "=SUM(".$columnas_excel[$col_index].$ln_inicio_centro_costo.":".$columnas_excel[$col_index].($ln-1).")");
			$col_index++;
			$sheet->setCellValue($columnas_excel[$col_index].($ln), "=SUM(".$columnas_excel[$col_index].$ln_inicio_centro_costo.":".$columnas_excel[$col_index].($ln-1).")");
			$col_index++;

			$sheet->getStyle("D".$ln.":{$col_final}$ln")->getNumberFormat()->setFormatCode('0;-0;;@');
			$ln_total_centro_costo[]=$ln;
			$ln++;
			$ln_inicio_centro_costo=$ln+1;
		}

		$sheet->setCellValue("A{$ln}", "$c_nivel");
		$sheet->setCellValue("B{$ln}", $row['nivel1']);
		$sheet->getStyle("A{$ln}:B{$ln}")->getFont()->setBold(true);
		$sheet->getStyle("A{$ln}:B{$ln}")->getFont()->setSize(12);

		$sheet->getStyle("A{$ln}:{$col_final}{$ln}")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('AAFFFFCC');

		$c_nivel++;
		$ln++;
		$total_mes=[];
		$nivel=$row["codnivel1"];
	}

	if($persona!=$row["personal_id"]){
		$persona=$row["personal_id"];
		if(!mb_detect_encoding($row['apenom'],["UTF-8"],true))
			$row['apenom']=utf8_encode($row['apenom']);

		if(!mb_detect_encoding($row['cargo'],["UTF-8"],true))
			$row['cargo']=utf8_encode($row['cargo']);

		$sheet->setCellValue("A{$ln}", "$c_persona");
		$sheet->setCellValue("B{$ln}", $row['apenom']);
		$sheet->setCellValue("C{$ln}", $row['cargo']);
		//ubicar el registro en la columna que le corresponda al mes
		$mes  = $row['mes'];
		$tipo = $row["tipo"];
		if($mes and $tipo){
			$sheet->setCellValue($columna_mes["$mes"]["$tipo"]."{$ln}", $row['n']);
			$sheet->setCellValue($columna_mes["$mes"]["T"]."{$ln}","=+".$columna_mes["$mes"]["81"]."{$ln}+".$columna_mes["$mes"]["5"]."{$ln}");
		}
		$sheet->getStyle("A{$ln}:{$col_final}{$ln}")->getFont()->setSize(12);


		//total de la linea

		$sheet->setCellValue($columnas_excel[$col_index_total]."{$ln}", "=+".
			$columna_mes["1" ]["81"]."{$ln}+".
			$columna_mes["2" ]["81"]."{$ln}+".
			$columna_mes["3" ]["81"]."{$ln}+".
			$columna_mes["4" ]["81"]."{$ln}+".
			$columna_mes["5" ]["81"]."{$ln}+".
			$columna_mes["6" ]["81"]."{$ln}+".
			$columna_mes["7" ]["81"]."{$ln}+".
			$columna_mes["8" ]["81"]."{$ln}+".
			$columna_mes["9" ]["81"]."{$ln}+".
			$columna_mes["10"]["81"]."{$ln}+".
			$columna_mes["11"]["81"]."{$ln}+".
			$columna_mes["12"]["81"]."{$ln}"
		);

		$sheet->setCellValue($columnas_excel[$col_index_total+1]."{$ln}", "=+".
			$columna_mes["1" ]["5"]."{$ln}+".
			$columna_mes["2" ]["5"]."{$ln}+".
			$columna_mes["3" ]["5"]."{$ln}+".
			$columna_mes["4" ]["5"]."{$ln}+".
			$columna_mes["5" ]["5"]."{$ln}+".
			$columna_mes["6" ]["5"]."{$ln}+".
			$columna_mes["7" ]["5"]."{$ln}+".
			$columna_mes["8" ]["5"]."{$ln}+".
			$columna_mes["9" ]["5"]."{$ln}+".
			$columna_mes["10"]["5"]."{$ln}+".
			$columna_mes["11"]["5"]."{$ln}+".
			$columna_mes["12"]["5"]."{$ln}"
		);

		$sheet->setCellValue($columnas_excel[$col_index_total+2]."{$ln}", "=+".
			$columna_mes["1" ]["T"]."{$ln}+".
			$columna_mes["2" ]["T"]."{$ln}+".
			$columna_mes["3" ]["T"]."{$ln}+".
			$columna_mes["4" ]["T"]."{$ln}+".
			$columna_mes["5" ]["T"]."{$ln}+".
			$columna_mes["6" ]["T"]."{$ln}+".
			$columna_mes["7" ]["T"]."{$ln}+".
			$columna_mes["8" ]["T"]."{$ln}+".
			$columna_mes["9" ]["T"]."{$ln}+".
			$columna_mes["10"]["T"]."{$ln}+".
			$columna_mes["11"]["T"]."{$ln}+".
			$columna_mes["12"]["T"]."{$ln}"
		);

		$sheet->getStyle($columnas_excel[$col_index_total].$ln.":".$columnas_excel[$col_index_total+2].$ln)->getNumberFormat()->setFormatCode('0;-0;;@');

		$c_persona++;
		$ln++;
	}
	else{
		//ubicar el registro en la columna que le corresponda al mes
		$mes  = $row['mes'];
		$tipo = $row["tipo"];
		if($mes and $tipo){
			$sheet->setCellValue($columna_mes["$mes"]["$tipo"]."{$ln}", $row['n']);
			$sheet->setCellValue($columna_mes["$mes"]["T"]."{$ln}","=+".$columna_mes["$mes"]["81"]."{$ln}+".$columna_mes["$mes"]["5"]."{$ln}");
		}		
	}

	if($mes){
		if(!isset($total_mes[$mes]))
			$total_mes[$mes]=0;
		$total_mes[$mes]+=$row['n'];

		if(!isset($totalgeneral_mes[$mes]))
			$totalgeneral_mes[$mes]=0;
		$totalgeneral_mes[$mes]+=$row['n'];		
	}
}//fin while



$sheet->setCellValue("C{$ln}", "Total");
$sheet->getStyle("A{$ln}:{$col_final}{$ln}")->getFont()->setBold(true);
$sheet->getStyle("A{$ln}:{$col_final}{$ln}")->getFont()->setSize(12);


$col_index=3;//D
for($i=1; $i <=12 ; $i++) { 	
	$sheet->setCellValue($columnas_excel[$col_index].($ln), "=SUM(".$columnas_excel[$col_index].$ln_inicio_centro_costo.":".$columnas_excel[$col_index].($ln-1).")");
	$col_index++;
	$sheet->setCellValue($columnas_excel[$col_index].($ln), "=SUM(".$columnas_excel[$col_index].$ln_inicio_centro_costo.":".$columnas_excel[$col_index].($ln-1).")");
	$col_index++;
	$sheet->setCellValue($columnas_excel[$col_index].($ln), "=SUM(".$columnas_excel[$col_index].$ln_inicio_centro_costo.":".$columnas_excel[$col_index].($ln-1).")");
	$col_index++;
}
$sheet->setCellValue($columnas_excel[$col_index].($ln), "=SUM(".$columnas_excel[$col_index].$ln_inicio_centro_costo.":".$columnas_excel[$col_index].($ln-1).")");
$col_index++;
$sheet->setCellValue($columnas_excel[$col_index].($ln), "=SUM(".$columnas_excel[$col_index].$ln_inicio_centro_costo.":".$columnas_excel[$col_index].($ln-1).")");
$col_index++;
$sheet->setCellValue($columnas_excel[$col_index].($ln), "=SUM(".$columnas_excel[$col_index].$ln_inicio_centro_costo.":".$columnas_excel[$col_index].($ln-1).")");
$col_index++;

$sheet->getStyle("D".$ln.":{$col_final}$ln")->getNumberFormat()->setFormatCode('0;-0;;@');
$ln_total_centro_costo[]=$ln;

$ln++;

function concatenarSuma($col,$array_ln){
    $str="";
    for($i=0; $i < count($array_ln); $i++) { 
        $str.="+".$col.$array_ln[$i];
    }
    return $str;
}

$sheet->setCellValue("C{$ln}", "Total General");
$sheet->getStyle("A{$ln}:{$col_final}{$ln}")->getFont()->setBold(true);
$sheet->getStyle("A{$ln}:{$col_final}{$ln}")->getFont()->setSize(12);

$col_index=3;//D
for($i=1; $i <=12 ; $i++) { 	
	$sheet->setCellValue($columnas_excel[$col_index].($ln), "=".concatenarSuma($columnas_excel[$col_index],$ln_total_centro_costo));
	$col_index++;
	$sheet->setCellValue($columnas_excel[$col_index].($ln), "=".concatenarSuma($columnas_excel[$col_index],$ln_total_centro_costo));
	$col_index++;
	$sheet->setCellValue($columnas_excel[$col_index].($ln), "=".concatenarSuma($columnas_excel[$col_index],$ln_total_centro_costo));
	$col_index++;
}
$sheet->setCellValue($columnas_excel[$col_index].($ln), "=".concatenarSuma($columnas_excel[$col_index],$ln_total_centro_costo));
$col_index++;
$sheet->setCellValue($columnas_excel[$col_index].($ln), "=".concatenarSuma($columnas_excel[$col_index],$ln_total_centro_costo));
$col_index++;
$sheet->setCellValue($columnas_excel[$col_index].($ln), "=".concatenarSuma($columnas_excel[$col_index],$ln_total_centro_costo));
$col_index++;

$sheet->getStyle("D".$ln.":{$col_final}$ln")->getNumberFormat()->setFormatCode('0;-0;;@');


$sheet->getStyle("A".($ln_inicio-1).":A$ln")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle("B".($ln_inicio-1).":B$ln")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$sheet->getStyle("D".$ln_inicio.":{$col_final}{$ln}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$border=array(
  'borders' => array(
    'allborders' => array(
      'style' => PHPExcel_Style_Border::BORDER_THIN,
      'color' => array('rgb' => '000000')
    )
  )
);

$sheet->getStyle("D".($ln_inicio-2).":{$col_final}".$ln)->applyFromArray($border);
$sheet->getStyle("A".($ln_inicio-1).":{$col_final}".$ln)->applyFromArray($border);
$sheet->getStyle("A1:{$col_final}{$ln}")->getFont()->setName('Times New Roman');



//ocultar columnas
$mes=$_GET['mes'];
if($mes){
	$mes=explode(",", $mes);

	if(!(in_array("01", $mes) or in_array("1", $mes))){
		$sheet->getColumnDimension($columna_mes["1"]["81"])->setVisible(false);
		$sheet->getColumnDimension($columna_mes["1"]["5" ])->setVisible(false);
		$sheet->getColumnDimension($columna_mes["1"]["T" ])->setVisible(false);
	}

	if(!(in_array("02", $mes) or in_array("2", $mes))){
		$sheet->getColumnDimension($columna_mes["2"]["81"])->setVisible(false);
		$sheet->getColumnDimension($columna_mes["2"]["5" ])->setVisible(false);
		$sheet->getColumnDimension($columna_mes["2"]["T" ])->setVisible(false);
	}

	if(!(in_array("03", $mes) or in_array("3", $mes))){	
		$sheet->getColumnDimension($columna_mes["3"]["81"])->setVisible(false);
		$sheet->getColumnDimension($columna_mes["3"]["5" ])->setVisible(false);
		$sheet->getColumnDimension($columna_mes["3"]["T" ])->setVisible(false);
	}

	if(!(in_array("04", $mes) or in_array("4", $mes))){
		$sheet->getColumnDimension($columna_mes["4"]["81"])->setVisible(false);
		$sheet->getColumnDimension($columna_mes["4"]["5" ])->setVisible(false);
		$sheet->getColumnDimension($columna_mes["4"]["T" ])->setVisible(false);
	}

	if(!(in_array("05", $mes) or in_array("5", $mes))){
		$sheet->getColumnDimension($columna_mes["5"]["81"])->setVisible(false);
		$sheet->getColumnDimension($columna_mes["5"]["5" ])->setVisible(false);
		$sheet->getColumnDimension($columna_mes["5"]["T" ])->setVisible(false);
	}

	if(!(in_array("06", $mes) or in_array("6", $mes))){
		$sheet->getColumnDimension($columna_mes["6"]["81"])->setVisible(false);
		$sheet->getColumnDimension($columna_mes["6"]["5" ])->setVisible(false);
		$sheet->getColumnDimension($columna_mes["6"]["T" ])->setVisible(false);
	}

	if(!(in_array("07", $mes) or in_array("7", $mes))){
		$sheet->getColumnDimension($columna_mes["7"]["81"])->setVisible(false);
		$sheet->getColumnDimension($columna_mes["7"]["5" ])->setVisible(false);
		$sheet->getColumnDimension($columna_mes["7"]["T" ])->setVisible(false);
	}

	if(!(in_array("08", $mes) or in_array("8", $mes))){
		$sheet->getColumnDimension($columna_mes["8"]["81"])->setVisible(false);
		$sheet->getColumnDimension($columna_mes["8"]["5" ])->setVisible(false);
		$sheet->getColumnDimension($columna_mes["8"]["T" ])->setVisible(false);
	}

	if(!(in_array("09", $mes) or in_array("9", $mes))){
		$sheet->getColumnDimension($columna_mes["9"]["81"])->setVisible(false);
		$sheet->getColumnDimension($columna_mes["9"]["5" ])->setVisible(false);
		$sheet->getColumnDimension($columna_mes["9"]["T" ])->setVisible(false);
	}

	if(!in_array("10", $mes)){
		$sheet->getColumnDimension($columna_mes["10"]["81"])->setVisible(false);
		$sheet->getColumnDimension($columna_mes["10"]["5" ])->setVisible(false);
		$sheet->getColumnDimension($columna_mes["10"]["T" ])->setVisible(false);
	}

	if(!in_array("11", $mes)){
		$sheet->getColumnDimension($columna_mes["11"]["81"])->setVisible(false);
		$sheet->getColumnDimension($columna_mes["11"]["5" ])->setVisible(false);
		$sheet->getColumnDimension($columna_mes["11"]["T" ])->setVisible(false);
	}

	if(!in_array("12", $mes)){
		$sheet->getColumnDimension($columna_mes["12"]["81"])->setVisible(false);
		$sheet->getColumnDimension($columna_mes["12"]["5" ])->setVisible(false);
		$sheet->getColumnDimension($columna_mes["12"]["T" ])->setVisible(false);
	}

}



$filename = "historicos_amonestaciones"."_".$anio;

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
ob_end_clean();
$writer->save('php://output');
?>