<?php
if (!isset($_SESSION)) {
  session_start();
}
require('../../nomina/fpdf/fpdf.php');

require_once '../../nomina/lib/config.php';
require_once '../../nomina/lib/pdfcommon.php';
require_once '../../nomina/lib/common.php';

include ("../../nomina/paginas/funciones_nomina.php");

error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED);
$cantidad_registros=13;
//error_reporting(0);
$conexion=conexion();

function fecha($value) { // fecha de YYYY/MM/DD a DD/MM/YYYY
 if ( ! empty($value) ) return substr($value,8,2) ."/". substr($value,5,2) ."/". substr($value,0,4);
}

class PDF extends FPDF
{
	//Cabecera de página
	function header(){
		global $var_encabezado, $var_imagen_izq, $periodo_ini, $periodo_fin;
		
		
		$logo = "../../nomina/imagenes/".$var_imagen_izq;
		
		$this->SetFont('Arial','B',12);
		//$this->SetTextColor(24,13,195);
		$this->Cell(50,20, $this->Image($logo,$this->rMargin,$this->tMargin+10,40) ,0,0,'C');

		$this->SetXY($this->rMargin+45,$this->tMargin+10);
		$this->Cell(100,5,utf8_decode("PLANILLA QUINCENAL"),0,1,'L');

		$this->SetFont('Arial','',10);
		$this->SetX($this->rMargin+45);
		$this->Cell(100,5,utf8_decode("DESGLOSE DE NOMINA POR FORMA DE PAGO"),0,1,'L');
		
		$this->SetX($this->rMargin+45);
		$this->Cell(100,5,utf8_decode("Periodo de ".$periodo_ini." al ".$periodo_fin),0,1,'L');

		$this->SetY(50);
	}

	//Pie de página
	function Footer()
	{
		//Posición: a 1,5 cm del final
		$this->SetY(-15);

		$this->SetFont('Arial','I',8);
		$this->Cell(0,5,utf8_decode('Página ').$this->PageNo().'/{nb}',0,1,'C');
		$this->Cell(0,5,'Elaborado Por: '.$_SESSION['nombre'],0,0,'L');

		//Número de página
		// $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
	}

}

$conexion=conexion();

$codnom=$_GET['codnom'];
$codtip=$_GET['codtip'];

global $var_encabezado, $var_imagen_izq, $periodo_ini, $periodo_fin;
$var_sql="select * from nomempresa";
$rs = query($var_sql,$conexion);
$row_rs = fetch_array($rs);

$var_encabezado=$row_rs['nom_emp'];
$var_imagen_izq=$row_rs['imagen_izq'];


$consulta1 = "SELECT periodo_ini, periodo_fin, periodo FROM nom_nominas_pago WHERE codnom = '".$codnom."' AND codtip = '".$codtip."'";
$result1 = query($consulta1,$conexion);
$nomina = fetch_array($result1);



$periodo_ini=date("d/m/Y",strtotime($nomina['periodo_ini']));
$periodo_fin=date("d/m/Y",strtotime($nomina['periodo_fin']));




$date1=date("d/m/Y");
$date2=date("h:m:s");



//Creación del objeto de la clase heredada
$pdf=new PDF('P','mm','Letter');
$pdf->SetMargins(20, 15);
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial','',10);


	

$t=[25,25,75,25,25];

$sql="
	SELECT 
		nnn.*, 
		np.apenom, 
		np.telefonos,
		nnn.forcob,
		n1.markar,
		n1.descripcion_completa,
		n1.descripcion_corta 
  FROM nom_nomina_netos nnn 
  	JOIN nompersonal np ON (np.cedula=nnn.cedula  AND np.ficha=nnn.ficha) 
  	LEFT JOIN nomnivel1 n1 on n1.codorg=nnn.codnivel1
  WHERE 
  	nnn.codnom='$codnom' AND 
  	nnn.tipnom='$codtip'      
  ORDER BY 
  	n1.markar,
        if(nnn.forcob LIKE 'Efectivo',1,2) ASC,
  	nnn.cedula";

$res2=query($sql,$conexion);
//print_r($res2);exit;

if($res2->num_rows==0){
	print "Sin datos que mostrar.";
	exit;
}

$codnivel1_actual="";

$total=[
	"efectivo"=>0,
	"transferencia"=>0
];

$total_general=[
	"efectivo"=>0,
	"transferencia"=>0
];

$c=0;
$c_efectivo=0;
$c_transferencia=0;
while($row=fetch_array($res2)){

	if($codnivel1_actual!=$row["codnivel1"]){
		if($codnivel1_actual!=""){

			//repetir al final (fuera del bucle)
			$pdf->SetFont('Arial','B',10);
			$pdf->Cell($t[0],5,utf8_decode(""),'LRTB',0,'C',1);
			$pdf->Cell($t[1],5,utf8_decode(""),'LRTB',0,'C',1);
			$pdf->Cell($t[2],5,utf8_decode("Total por Forma de Pago"),'LRTB',0,'L',1);
			$pdf->Cell($t[3],5,utf8_decode(number_format($total["efectivo"],2,",",".")),'LRTB',0,'R',1);
			$pdf->Cell($t[4],5,utf8_decode(number_format($total["transferencia"],2,",",".")),'LRTB',1,'R',1);

			$pdf->SetFont('Arial','B',10);
			$pdf->Cell($t[0],8,utf8_decode("$c"),'LRTB',0,'C',1);
			$pdf->Cell($t[1],8,utf8_decode(""),'LRTB',0,'C',1);
			$pdf->Cell($t[2],8,utf8_decode("Total por Centro Costo"),'LRTB',0,'L',1);
			$pdf->Cell($t[3],8,utf8_decode(""),'LRTB',0,'R',1);
			$pdf->Cell($t[4],8,utf8_decode(number_format($total["efectivo"]+$total["transferencia"],2,",",".")),'LRTB',1,'R',1);

			$total["efectivo"]=0;
			$total["transferencia"]=0;
		}
	

		$centro=$row["markar"];
		if($row["descripcion_completa"])
			$centro.=" - ".$row["descripcion_completa"];
		if($row["descripcion_corta"])
			$centro.=" (".$row["descripcion_corta"].")";
		$pdf->Ln(5);
		$pdf->SetFont('Arial','B',10);


		$pdf->Cell(180,5,$centro,0,1,'L');

		$pdf->SetFillColor(215,215,215);
		$pdf->SetFont('Arial','I',10);
		$pdf->Cell($t[0],5,utf8_decode("Ficha"),'LRTB',0,'C',1);
		$pdf->Cell($t[1],5,utf8_decode("Cédula"),'LRTB',0,'C',1);
		$pdf->Cell($t[2],5,utf8_decode("Nombre"),'LRTB',0,'C',1);
		$pdf->Cell($t[3],5,utf8_decode("Efectivo"),'LRTB',0,'C',1);
		$pdf->Cell($t[4],5,utf8_decode("Transferencia"),'LRTB',1,'C',1);
		$pdf->SetFillColor(255,255,255);
		$pdf->SetFont('Arial','',10);
		$codnivel1_actual=$row["codnivel1"];
	}

	
	
	$pdf->Cell($t[0],5,utf8_decode($row["ficha"]),'LRTB',0,'C',1);
	$pdf->Cell($t[1],5,utf8_decode($row["cedula"]),'LRTB',0,'C',1);
	$pdf->Cell($t[2],5,$row["apenom"],'LRTB',0,'L',1);
	$efectivo="";
	$transferencia="";
	if(strtoupper(trim($row["forcob"]))=="EFECTIVO"){
		$efectivo=number_format($row["neto"],2,",",".");
		$total["efectivo"]+=$row["neto"];
		$total_general["efectivo"]+=$row["neto"];
		$c_efectivo++;
	}
	else{
		$transferencia=number_format($row["neto"],2,",",".");
		$total["transferencia"]+=$row["neto"];
		$total_general["transferencia"]+=$row["neto"];
		$c_transferencia++;
	}
	$pdf->Cell($t[3],5,utf8_decode("$efectivo"),'LRTB',0,'R',1);
	$pdf->Cell($t[4],5,utf8_decode("$transferencia"),'LRTB',1,'R',1);
	$c++;
}

$pdf->SetFont('Arial','B',10);
$pdf->Cell($t[0],5,utf8_decode(""),'LRTB',0,'C',1);
$pdf->Cell($t[1],5,utf8_decode(""),'LRTB',0,'C',1);
$pdf->Cell($t[2],5,utf8_decode("Total por Forma de Pago"),'LRTB',0,'L',1);
$pdf->Cell($t[3],5,utf8_decode(number_format($total["efectivo"],2,",",".")),'LRTB',0,'R',1);
$pdf->Cell($t[4],5,utf8_decode(number_format($total["transferencia"],2,",",".")),'LRTB',1,'R',1);

$pdf->SetFont('Arial','B',10);
$pdf->Cell($t[0],8,utf8_decode("$c"),'LRTB',0,'C',1);
$pdf->Cell($t[1],8,utf8_decode(""),'LRTB',0,'C',1);
$pdf->Cell($t[2],8,utf8_decode("Total por Centro Costo"),'LRTB',0,'L',1);
$pdf->Cell($t[3],8,utf8_decode(""),'LRTB',0,'R',1);
$pdf->Cell($t[4],8,utf8_decode(number_format($total["efectivo"]+$total["transferencia"],2,",",".")),'LRTB',1,'R',1);


$pdf->Ln(3);

$pdf->Cell($t[0]+$t[1]+$t[2],5,utf8_decode("                      Total por Forma de Pago"),'',0,'L',1);
$pdf->Cell($t[3],5,utf8_decode(number_format($total_general["efectivo"],2,",",".")),'TB',0,'R',1);
$pdf->Cell(3,5,utf8_decode(""),'',0,'L',1);
$pdf->Cell($t[4]-3,5,utf8_decode(number_format($total_general["transferencia"],2,",",".")),'TB',1,'R',1);

$pdf->Cell($t[0]+$t[1]+$t[2],1,utf8_decode(""),'',0,'L',1);
$pdf->Cell($t[3],1,"",'TB',0,'R',1);
$pdf->Cell(3,1,utf8_decode(""),'',0,'L',1);
$pdf->Cell($t[4]-3,1,"",'TB',1,'R',1);

$pdf->Cell($t[0]+$t[1]+$t[2],5,utf8_decode("                      Total de Empleados por Forma de Pago"),'',0,'L',1);
$pdf->Cell($t[3],5,utf8_decode($c_efectivo),'',0,'R',1);
$pdf->Cell(3,5,utf8_decode(""),'',0,'L',1);
$pdf->Cell($t[4]-3,5,utf8_decode($c_transferencia),'',1,'R',1);


$pdf->Cell($t[0]+$t[1]+$t[2],8,utf8_decode("                      Gran Total"),'',0,'L',1);
$pdf->Cell($t[3],8,utf8_decode(""),'',0,'R',1);
$pdf->Cell(3,8,utf8_decode(""),'',0,'L',1);
$pdf->Cell($t[4]-3,8,utf8_decode(number_format($total_general["efectivo"]+$total_general["transferencia"],2,",",".")),'TB',1,'R',1);

$pdf->Cell($t[0]+$t[1]+$t[2],1,utf8_decode(""),'',0,'L',1);
$pdf->Cell($t[3],1,"",'',0,'R',1);
$pdf->Cell(3,1,utf8_decode(""),'',0,'L',1);
$pdf->Cell($t[4]-3,1,"",'TB',1,'R',1);


$pdf->Cell($t[0]+$t[1]+$t[2],5,utf8_decode("                      Total de Empleados"),'',0,'L',1);
$pdf->Cell($t[3],5,utf8_decode(""),'',0,'R',1);
$pdf->Cell(3,5,utf8_decode(""),'',0,'L',1);
$pdf->Cell($t[4]-3,5,utf8_decode($c_efectivo+$c_transferencia),'',1,'R',1);


$pdf->Output();
?>