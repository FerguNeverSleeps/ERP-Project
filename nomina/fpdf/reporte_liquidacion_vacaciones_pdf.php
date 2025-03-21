<?php
session_start();
ob_start();
$termino=$_SESSION['termino'];
$nomina_id=$_GET['codigo_nomina'];
$codtp=$_GET['codt'];
$registro_id=$_GET['registro_id'];

require('fpdf.php');
include('numerosALetras.class.php');
include("../lib/common.php");

function fecha($value) { // fecha de YYYY/MM/DD a DD/MM/YYYY
 if ( ! empty($value) ) return substr($value,8,2) ."/". substr($value,5,2) ."/". substr($value,0,4);
}


class PDF extends FPDF
{

function header(){
	
	$this->SetFont('Arial','',8);

        $this->Cell(70,4,'',0,0,'L');
//	$this->Cell(70,5,utf8_decode($var_encabezado),0,0,'L');
	$this->Cell(50,4,'LIQUIDACION DE VACACIONES',0,0,'C');
	$this->Cell(70,4,'',0,1,'R');
//	$this->Cell(50,5,'Gobierno de Carabobo',0,0,'L');
	$this->Ln(3);
}

//Hacer que sea multilinea sin que haga un salto de linea
var $widths;
var $aligns;
var $celdas;
var $ancho;



function SetWidths($w)
{
    //Set the array of column widths
    $this->widths=$w;
}
function SetAligns($a)
{
    //Set the array of column alignments
    $this->aligns=$a;
}
// Marco de la celda
function Setceldas($cc)
{

    $this->celdas=$cc;
}
// Ancho de la celda
function Setancho($aa)
{
    $this->ancho=$aa;
}
function CheckPageBreak($h)
{
    //If the height h would cause an overflow, add a new page immediately
    if($this->GetY()+$h>$this->PageBreakTrigger)
        $this->AddPage($this->CurOrientation);
}

function NbLines($w,$txt)
{
    //Computes the number of lines a MultiCell of width w will take
    $cw=&$this->CurrentFont['cw'];
    if($w==0)
        $w=$this->w-$this->rMargin-$this->x;
    $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
    $s=str_replace("\r",'',$txt);
    $nb=strlen($s);
    if($nb>0 and $s[$nb-1]=="\n")
        $nb--;
    $sep=-1;
    $i=0;
    $j=0;
    $l=0;
    $nl=1;
    while($i<$nb)
    {
        $c=$s[$i];
        if($c=="\n")
        {
            $i++;
            $sep=-1;
            $j=$i;
            $l=0;
            $nl++;
            continue;
        }
        if($c==' ')
            $sep=$i;
        $l+=$cw[$c];
        if($l>$wmax)
        {
            if($sep==-1)
            {
                if($i==$j)
                    $i++;
            }
            else
                $i=$sep+1;
            $sep=-1;
            $j=$i;
            $l=0;
            $nl++;
        }
        else
            $i++;
    }
    return $nl;
}
function Row($data)
{
    //Calculate the height of the row
    $nb=0;
    for($i=0;$i<count($data);$i++)
        $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
    $h=5*$nb;
    //Issue a page break first if needed
    $this->CheckPageBreak($h);
    //Draw the cells of the row
    for($i=0;$i<count($data);$i++)
    {
        $w=$this->widths[$i];
        $a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'C';
        //Save the current position
        $x=$this->GetX();
        $y=$this->GetY();
        //Draw the border
        //$this->Rect($x,$y,$w,$h);
        //Print the text
        $this->MultiCell($w,$this->ancho[$i],$data[$i],$this->celdas[$i],$a);
        //Put the position to the right of the cell
        $this->SetXY($x+$w,$y);
    }
    //Go to the next line
    $this->Ln($h);
}
//fin

function personas($nomina_id,$codtp,$registro_id){

    $conexion=conexion();
    $consulta3 = "SELECT periodo_ini, periodo_fin, periodo FROM nom_nominas_pago WHERE codnom = '".$nomina_id."' AND codtip = '".$codtp."'";
    $result5 = query($consulta3,$conexion);
    $fetch5 = fetch_array($result5);
	
    $query="select * from nomempresa";		
    $result=query($query,$conexion);	
    $row = fetch_array ($result);	
    $nompre_empresa=$row[nom_emp];
    $rif=$row[rif];
    $gerente=$row[ger_rrhh];
	
    $query="select * from nomvis_per_movimiento where codnom='".$nomina_id."' and tipnom='".$_SESSION['codigo_nomina']."' and ficha=".$registro_id;		
    $result_lote=query($query,$conexion);	
    $totalbd=num_rows($result);
	
    while ($fila=fetch_array($result_lote))
    {
    	//Datos personal
        
	$registro_id=$fila['ficha'];
	$query="select * from nompersonal where ficha = '$registro_id' and tipnom=$_SESSION[codigo_nomina]";
	$result=query($query,$conexion);	
	$fila = fetch_array ($result);	
	$cargo_id=$fila['codcargo'];
	$ingreso=$fila['fecing'];
	
	$query="select des_car from nomcargos where cod_car = '$cargo_id'";		
	$result=query($query,$conexion);	
	$row = fetch_array ($result);	
	$nompre_cargo=$row[des_car];
	$sub_total_dedu=0;
	
	$this->SetFont('Arial','',8);
	$this->SetWidths(array(100,88));
	$this->SetAligns(array('L','L'));
	$this->Setceldas(array(0,0));
	$this->Setancho(array(5,5));
	$query="select cod_ban,des_ban from nombancos where cod_ban='".$fila[codbancob]."'";
	$resultado=query($query,$conexion);
	$row2 = mysqli_fetch_array($resultado);
        $this->Row(array(utf8_decode('Nombre del Trabajador: ').utf8_decode($fila[apenom]),utf8_decode('Cédula: ').number_format($fila[cedula],0,'.','.')));
	//$this->Row(array('Ficha: '.$fila[ficha],utf8_decode('Cédula: ').number_format($fila[cedula],0,'.','.')));
	//$this->Row(array(number_format($fila[cedula],0,'.','.'),utf8_decode($fila[apenom]),$fila[ficha]));             
        $query="select cod_car,des_car from nomcargos where cod_car='".$fila[codcargo]."'";
	$result=query($query,$conexion);
	$row = mysqli_fetch_array($result);
	$this->SetWidths(array(100,88));
	$this->SetAligns(array('L','L'));
	$this->Setceldas(array(0,0));
	$this->Setancho(array(5,5));
	$this->Row(array('Cargo: '.utf8_decode($row[des_car]),'Departamento: '.$row[codnivel2]));
	    
        $this->SetWidths(array(100,88));
	$this->Row(array('Fecha de Ingreso: '.date("d/m/Y",strtotime($ingreso)),'Fecha de inicio: '.fecha($fetch5['periodo_ini']).' Fecha Termino: '.fecha($fetch5['periodo_fin'])));
	$this->SetWidths(array(100,88));
        $this->Row(array('Sueldo Mensual: '.number_format($fila[suesal],2,',','.'),'Sueldo diario: '.number_format($fila[suesal]/30,2,',','.')));
	
        $this->SetWidths(array(10,10));
	$this->Ln(1);
				
	$query="select * from nom_movimientos_nomina as mn
                    inner join
                    nompersonal as pe on mn.ficha = pe.ficha
                    inner join
                    nomconceptos as c on c.codcon = mn.codcon
                    where pe.ficha = '$registro_id' and pe.tipnom =".$_SESSION['codigo_nomina']." and mn.codnom= '".$nomina_id."' and mn.tipnom='".$codtp."' and mn.tipcon<>'P'
                    group by pe.apenom,pe.ficha,c.formula,c.codcon order by pe.apenom, mn.tipcon";

	$result =query($query,$conexion);
        
        $this->Cell(1,0,utf8_decode('-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------'),0,0,'L');
        $this->Cell(1,16,utf8_decode('------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------'),0,0,'L');
        $this->Cell(188,7,utf8_decode('INDEMNIZACIONES'),0,0,'C');
	$this->Ln(10);

	$sub_total_asig=0;
	while ($row = mysqli_fetch_array($result))
	{
            $anio=$row['anio'];
            if ($row[tipcon]=='A')
            {
		$asig= number_format($row[monto],2,',','.');
		$sub_total_asig=$row[monto]+$sub_total_asig;
                $this->SetFont('Arial','',8);
                //$this->SetWidths(array(95,18,15,25,10,25));
                //$this->SetAligns(array('L','C','C','R','C','R'));
                //$this->Setceldas(array(0,'B',0,'B',0,'B'));
                //$this->Setancho(array(3,3,3,3,3,3));
                
                $this->Cell(95,5,$row[codcon] . ' - ' . utf8_decode($row[descrip]),0,0,'L');
                $this->Cell(18,5,$row[valor],'B',0,'C');
                $this->Cell(15,5,'Dias a Bs: ',0,0,'R');
                $this->Cell(25,5,number_format($fila[suesal]/30,2,',','.'),'B',0,'R');
                $this->Cell(10,5,' = Bs: ',0,0,'R');
                $this->Cell(25,5,$asig,'B',1,'R');
	
                //$this->Row(array($row[codcon] . ' - ' . utf8_decode($row[descrip]),$row[valor],'Dias a Bs: ',number_format($fila[suesal]/30,2,',','.')," = Bs. ",$asig));
            }
            $asig='';
	}
	
	$this->Cell(158,5,'Sub-Total: ',0,0,'R');
	$this->Cell(30,5,number_format($sub_total_asig,2,',','.'),'B',1,'R');
	
        $result =query($query,$conexion);
        
        $this->Ln(5);
        
        
	$sub_total_dedu=0;
	while ($row = mysqli_fetch_array($result))
	{
            if($sub_total_dedu<>0)
            {
                $this->Cell(1,0,utf8_decode('-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------'),0,0,'L');
                $this->Cell(1,16,utf8_decode('------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------'),0,0,'L');
                $this->Cell(188,7,utf8_decode('DEDUCCIONES'),0,0,'C');
                $this->Ln(10);
            }
            if ($row[tipcon]=='D')
            {
            	$dedu= number_format($row[monto],2,',','.');
		$sub_total_dedu=$row[monto]+$sub_total_dedu;
                $this->SetFont('Arial','',8);
                $this->SetWidths(array(95,93));
                $this->SetAligns(array('L','R'));
                $this->Setceldas(array(0,1));
                $this->Setancho(array(5,5));
                $this->Row(array($row[codcon] . ' - ' . utf8_decode($row[descrip]),$dedu));
            }
		
            $dedu='';
		
	}
        if($sub_total_dedu<>0)
        {
            $this->Cell(158,5,'Sub-Total: ',0,0,'R');
            $this->Cell(30,5,number_format($sub_total_dedu,2,',','.'),'B',1,'R');
	}
        
        //	$this->Ln(1);
	$this->Cell(158,5,'Neto a Depositar Bs.: ',0,0,'R');
	$this->Cell(30,5,number_format($sub_total_asig-$sub_total_dedu,2,',','.'),'B',1,'R');
        //$this->MultiCell(188,5,'Observaciones: '.$observacion,0);
        //$this->Ln(3);
        //$this->Cell(188,1,'','TB',1);
        $netoapagar=$sub_total_asig-$sub_total_dedu;
	$this->Ln(3);
        $contenido=' He recibido de '.$nompre_empresa.' Rif Nº '.$rif;
        $this->Cell(188,5,  utf8_decode($contenido),0,1,'L');
	
        $n = new numerosALetras();
	$pagoletras=$n->convertir($netoapagar);
	
        $contenido2=' La cantidad de:  '.$pagoletras;
        $this->Cell(188,5,  utf8_decode($contenido2),0,1,'L');
	$this->Ln(4);
        
        $contenido3=' Correspondiente al pago de las vacaciones del periodo:  '.($anio-1)." - ".$anio;
        $this->Cell(188,5,  utf8_decode($contenido3),0,1,'L');
	$this->Ln(4);
        
        $this->Cell(63,4,'Realizado Por:',0,0,'C');
	$this->Cell(63,4,'Revisado Por:',0,0,'C');	
	$this->Cell(63,4,'Aprovado Por:',0,0,'C');
        $this->Ln(4);
        
        $this->Cell(60,4,'Coordinadora Administrativa:',0,0,'C');
	$this->Cell(4,4,'',0,0,'C');	
	$this->Cell(60,4,'Coordinadora Administrativa:',0,0,'C');	
	$this->Cell(4,4,'',0,0,'C');	
	$this->Cell(60,4,'Director Medico',0,0,'C');
        $this->Ln(4);

        $this->Cell(60,4,'Lcda Marianela Lara',0,0,'C');
	$this->Cell(4,4,'',0,0,'C');	
	$this->Cell(60,4,'Lcda Marianela Lara',0,0,'C');	
	$this->Cell(4,4,'',0,0,'C');	
	$this->Cell(60,4,'Dr Alejandro Oquendo',0,0,'C');
        $this->Ln(10);
        
        /*
        $this->Cell(60,4,'','B',0,'C');
	$this->Cell(4,4,'',0,0,'C');	
	$this->Cell(60,4,'','B',0,'C');	
	$this->Cell(4,4,'',0,0,'C');	
	$this->Cell(60,4,'','B',0,'C');
        $this->Ln(3);
        */
        $this->Cell(60,4,'Firma','T',0,'C');
	$this->Cell(4,4,'',0,0,'C');	
	$this->Cell(60,4,'Firma','T',0,'C');	
	$this->Cell(4,4,'',0,0,'C');	
	$this->Cell(60,4,'Firma','T',0,'C');
        $this->Ln(10);
        
        $this->Cell(44,4,'RECIBE CONFORME',0,0,'C');
	$this->Cell(40,4,'','B',0,'C');	
	$this->Cell(44,4,'FECHA',0,0,'C');
        $this->Cell(40,4,'','B',0,'C');
    }
    
}
 


function Footer(){
// 	$this->SetY(-15);
// 	$this->Cell(0,10,utf8_decode('Página ').$this->PageNo().'/{nb}',0,0,'C');
}


}


//Creación del objeto de la clase heredada
// $pdf = new PDF('L', 'mm', array(215,139));
$pdf=new PDF();
$pdf->AliasNbPages();
$pdf->AddPage('P','A4');
$pdf->SetFont('Arial','',9);
$pdf->personas($nomina_id,$codtp,$registro_id);
$pdf->Output();
?>
