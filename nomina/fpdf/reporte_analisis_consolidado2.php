<?php 
session_start();
ob_start();
$termino=$_SESSION['termino'];

require('fpdf.php');
include("../lib/common.php");
include("../lib/pdf.php");

class PDF extends FPDF
{
// var $nominapdf;
// var $fpdf;
function header(){
	$conexion=conexion();
	$var_sql="select * from nomempresa";
	$rs = query($var_sql,$conexion);
	$row_rs = fetch_array($rs);
	$var_encabezado=$row_rs['nom_emp'];
	
	$codcon1=explode(',',$_POST['concepto']);
	$consulta="select * from nomconceptos WHERE codcon='$codcon1[0]'";
	$resultado33=query($consulta,$conexion);
	$fetch33 = fetch_array($resultado33);
	
	
	$consulta="select descrip from nomfrecuencias WHERE codfre in ($_POST[frecuencia])";
	$resultado32=query($consulta,$conexion);
	while($fetch32 = fetch_array($resultado32))
	{
		$frecc.=$fetch32[descrip].', ';
	}
	
	$consulta="select descrip from nomtipos_nomina WHERE codtip in ($_POST[nomina])";
	$resultado34=query($consulta,$conexion);
	while($fetch34 = fetch_array($resultado34))
	{
		$nomm.=$fetch34[descrip].', ';
	}
	$nomm=substr($nomm,0,-2);
	$this->SetFont('Arial','',12);
	$date1=date('d/m/Y');
	$date2=date('h:i a');	

	$this->Cell(150,5,utf8_decode($var_encabezado),0,0,'L');
	$this->Cell(38,5,'Fecha:  '.$date1,0,1,'R');
	$this->Cell(150,5,'RECURSOS HUMANOS',0,0,'L');
	$this->Cell(38,5,'Hora: '.$date2,0,1,'R');
	$this->Cell(150,5,$fetch33['descrip'],0,1,'L');
	$this->Cell(188,5,'NOMINA(S): '.utf8_decode($nomm),0,1,'L');
	$this->Cell(188,10,utf8_decode($frecc).' de '.$_POST[mesano],0,1,'C');
	
	$this->SetFont('Arial','',10);
        $this->SetWidths(array(12,25,125,25));
	$this->SetAligns(array('C','R','L','R'));
        $this->Setceldas(array(0,0,0,0));
	$this->Setancho(array(5,5,5,5,5));
        $this->Row(array('COD.',utf8_decode('CÉDULA'),'APELLIDOS Y NOMBRES','MONTO'));
	
}

function movimientos($codcon1,$nomina,$frecuencia,$mes,$ano)
{
	$conexion=conexion();
	$consulta="SELECT np.apenom, nmn.codnom, nmn.codcon, nmn.ficha, sum(nmn.monto) as monto, nmn.cedula, nmn.tipnom FROM nom_movimientos_nomina nmn join nom_nominas_pago nmp on (nmn.codnom=nmp.codnom and nmn.tipnom=nmp.tipnom) JOIN nompersonal np on (np.cedula=nmn.cedula) WHERE nmn.codcon in ($codcon1) and nmp.frecuencia in ($frecuencia) and nmn.mes='$mes' and nmn.anio='$ano' and nmn.tipnom in ($nomina) group by nmn.cedula order by nmn.ficha";
	$resultado=query($consulta,$conexion);
	
	$totalwhile=num_rows($resultado);

	$totalPrest=$totalSaldo=0;
	while($fila=fetch_array($resultado))
	{
		$this->SetFont('Arial','',10);
// 		$conexion=conexion();
// 		$consulta="SELECT sum(monto) as monto FROM nom_movimientos_nomina WHERE codcon in ($codcon2) and mes='$mes' and anio='$ano' and tipnom='$fila[tipnom]' and ficha='$fila[ficha]'";
// 		$resultado_mov=query($consulta,$conexion);
// 		$fila_mov=fetch_array($resultado_mov);
		if($fila['monto']!=0)
		{
			$cedula="";
			if($fila['nacionalidad']==0)
				$cedula.="V";
			else
				$cedula.="E";
			
			if(strlen($fila['cedula'])==6)
			{
				$cedula.="00";
			}
			elseif(strlen($fila['cedula'])==7)
			{
				$cedula.="0";
			}
			
			$cedula.=$fila['cedula'];
			$this->SetFont('Arial','',9);
			$this->SetWidths(array(12,25,125,25));
			$this->SetAligns(array('C','R','L','R'));
			$this->Setceldas(array(0,0,0,0));
			$this->Setancho(array(5,5,5,5,5));
			$this->Row(array($fila['ficha'],$cedula,utf8_decode($fila['apenom']),number_format($fila['monto'],2,',','.')));
			$total+=$fila['monto'];
		}
	}
	$this->Ln(2);
	
	$this->SetFont('Arial','',10);
	$this->Cell(162,5,'CANTIDAD DE PERSONAS: '.$totalwhile,0,'C');
	$this->Cell(25,5,number_format($total,2,',','.'),0,0,'R');	
}

function Footer()
{

	
	$this->SetY(-20);
	//echo '<br>';
    	$this->SetFont('Arial','',8);
    	$this->Cell(0,5,'Elaborado Por: '.$_SESSION['nombre'],0,1,'L');
	$this->Cell(0,5,utf8_decode('Página ').$this->PageNo().'/{nb}',0,1,'C');
	
}
function finalizar(){

	$bool=validar_firma("PATRONALES");
	if ($bool==true){
		//firma_dinamica("ODP".$this->tipo,$this->pdff,6,10);
	}else{
		patronales($this->fpdf);
	}
}

}
list($mes,$ano)=explode('/',$_POST[mesano]);
$frecuencia=$_POST[frecuencia];
$codcon1=$_POST['concepto'];
$nomina=$_POST['nomina'];

$pdf=new PDF();
$pdf->AliasNbPages();
$pdf->AddPage('P','Letter');
$pdf->AddFont('Sanserif','','sanserif.php');
$pdf->SetFont('Sanserif','',12);


$pdf->movimientos($codcon1,$nomina,$frecuencia,$mes,$ano);
//$pdf->finalizar();
$pdf->Output();
?>