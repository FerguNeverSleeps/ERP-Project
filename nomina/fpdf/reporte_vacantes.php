<?php 
session_start();
ob_start();
$termino=$_SESSION['termino'];
$nomina=$_GET['nomina'];



require('fpdf.php');
include("../lib/common.php");


function fecha($value) { // fecha de YYYY/MM/DD a DD/MM/YYYY
 if ( ! empty($value) ) return substr($value,8,2) ."/". substr($value,5,2) ."/". substr($value,0,4);
}

class PDF extends FPDF
{
var $nominapdf;
function header(){
	$conexion=conexion();
	$var_sql="select * from nomempresa";
	$rs = query($var_sql,$conexion);
	$row_rs = fetch_array($rs);
	$var_encabezado=$row_rs['nom_emp'];
	
	
	$this->SetFont('Arial','',9);
	
	$tipnom=$_SESSION['codigo_nomina'];

	$fecha_ini = fecha($fetch['periodo_ini']);
	$fecha_fin = fecha($fetch['periodo_fin']);
	$date1=date('d/m/Y');
	$date2=date('h:i a');	

	$this->Cell(150,5,utf8_decode($var_encabezado),0,1,'L');
// 	$this->Cell(38,5,'Fecha:  '.$date1,0,1,'R');
	$this->Cell(150,5,'RECURSOS HUMANOS',0,0,'L');
	$this->Cell(38,5,'Hora: '.$date2,0,1,'R');
	
	$this->Cell(188,5,'RELACION DE CARGOS AL '.$date1,0,1,'C');

	
//	$this->Cell(188,8,'NOMINA: '.$_SESSION['nomina'],0,1,'C');
	
	$this->Ln(3);
	$this->SetFont('Arial','',9);
        $this->SetWidths(array(20,80,30,30,20));
	$this->SetAligns(array('R','L','C','C','C'));
        $this->Setceldas(array(0,0,0,0,0));
	$this->Setancho(array(5,5,5,5,5));
        $this->Row(array(utf8_decode('CODIGO'),'DESCRIPCION','CANT. PRESUP.','CANT. OCUP.','VACANTE'));
}

//Hacer que sea multilinea sin que haga un salto de linea


function Cuentas($nomina){
	
	$conexion=conexion();
	$cod=$_SESSION['codigo_nomina'];
	//$consulta="select * from nomcargos where perfil<>0 order by des_car asc";
	$consulta="select * from nomcargos where perfil<>0 order by cod_car+0";
	$resultado_cuenta=query($consulta,$conexion);
	cerrar_conexion($conexion);
	// llamado para hacer multilinea sin que haga salto de linea
	
	
        $total1=$total2=$total3=0;
	while($fila=fetch_array($resultado_cuenta))
	{
		
		$this->SetFont('Arial','',9);
		
		//$consulta="select count(codcargo) as ocup from nompersonal where tipnom=$_SESSION[codigo_nomina] and codcargo='".$fila[cod_car]."'";
		$consulta="select count(codcargo) as ocup from nompersonal where codcargo='".$fila[cod_car]."' and estado<>'Egresado'";
		$conexion=conexion();
		$resultado_personal=query($consulta,$conexion);
		$fila_personal=fetch_array($resultado_personal);
		$this->SetWidths(array(20,80,30,30,20));
		$this->SetAligns(array('R','L','C','C','C'));
		$this->Setceldas(array(0,0,0,0,0));
		$this->Setancho(array(5,5,5,5,5));
		$this->Row(array($fila['cod_car'].'   ',utf8_decode($fila['des_car']),$fila['perfil'],$fila_personal['ocup'],($fila['perfil']-$fila_personal['ocup'])));
		$total1+=$fila['perfil'];
		$total2+=$fila_personal['ocup'];
		$total3+=$fila['perfil']-$fila_personal['ocup'];
	}
	$this->SetFont('Arial','',10);
	$this->SetWidths(array(100,30,30,20));
	$this->SetAligns(array('C','C','C','C'));
	$this->Setceldas(array(0,0,0,0));
	$this->Setancho(array(5,5,5,5));
	$this->Row(array('TOTALES',$total1,$total2,$total3));
	
	$this->Ln(2);
	
	//$this->SetFont('Arial','',12);
	//$this->Cell(100,5,'CANTIDAD DE PERSONAS: '.$totalwhile,0,0,'C');
	//$this->Cell(88,5,'TOTAL MONTO: '.number_format($total_monto,2,',','.'),0,1,'C');
}
function Footer(){
	$this->SetY(-15);
	$this->Cell(0,10,utf8_decode('Página ').$this->PageNo().'/{nb}',0,0,'C');
}


}


//Creación del objeto de la clase heredada
$pdf=new PDF();
$pdf->AliasNbPages();
$pdf->AddPage('P','Letter','mm');
$pdf->AddFont('Sanserif','','sanserif.php');
$pdf->SetFont('Sanserif','',10);

$pdf->nominapdf=$nomina;

$pdf->Cuentas($nomina);

$pdf->Output();
?>