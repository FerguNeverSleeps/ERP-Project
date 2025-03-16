<?php 
if (!isset($_SESSION)) {
  session_start();
}
require('fpdf.php');
require_once '../lib/config.php';
require_once '../lib/common.php';

class PDF extends FPDF
{

function Header()
{
       

        $this->SetFont('Arial','',14);
     	$this->Cell(200,8,"CONSOLIDADO DE NOMINAS",0,0,"C");
     
     	$this->Ln(10);
     	$this->SetFont('Arial','B',10);
	$this->SetWidths(array(70,30,30,30,30));
	$this->SetAligns(array('C','C','C','C','C'));
	$this->Setceldas(array(1,1,1,1,1));
	$this->Setancho(array(5,5,5,5,5));
	//$this->SetX(-208);
	$this->Row(array('NOMINA','ASIGNACION','DEDUCCION','PATRONAL','TOTAL'));

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

        $a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';

        //Save the current position

        $x=$this->GetX();

        $y=$this->GetY();

        //Draw the border

        $this->Rect($x,$y,$w,$h);

        //Print the text

        $this->MultiCell($w,5,$data[$i],0,$a);

        //Put the position to the right of the cell

        $this->SetXY($x+$w,$y);

    }

    //Go to the next line

    $this->Ln($h);

}
//fin

function personas($pdf)
{

	$Conn=conexion();
	$i=0;
	while($i<$_POST[cant])
	{
		$k="nomina".$i;
		if($_POST[$k]!='')
		{
			$codigo=explode("-",$_POST[$k]);
			
			$var_sql="select descrip from nom_nominas_pago where codnom='$codigo[0]' and codtip='$codigo[1]'";
			$rs = query($var_sql,$Conn);
			$row_rs = fetch_array($rs);
			$nomina=$row_rs[descrip];
		
			$var_sql="select sum(monto) as monto from nom_movimientos_nomina where codnom='$codigo[0]' and tipnom='$codigo[1]' and tipcon='A'";
			$rs2 = query($var_sql,$Conn);
			$row_rs2 = fetch_array($rs2);
			$asig=$row_rs2[monto];

			$var_sql="select sum(monto) as monto from nom_movimientos_nomina where codnom='$codigo[0]' and tipnom='$codigo[1]' and tipcon='D'";
			$rs3 = query($var_sql,$Conn);
			$row_rs3 = fetch_array($rs3);
			$ded=$row_rs3[monto];

			$var_sql="select sum(monto) as monto from nom_movimientos_nomina where codnom='$codigo[0]' and tipnom='$codigo[1]' and tipcon='P'";
			$rs4 = query($var_sql,$Conn);
			$row_rs4 = fetch_array($rs4);
			$pat=$row_rs4[monto];

			$total=$asig-$ded;

			$this->SetFont('Arial','',9);
			$this->SetWidths(array(70,30,30,30,30));
			$this->SetAligns(array('C','C','C','C','C'));
			$this->Setceldas(array(1,1,1,1,1));
			$this->Setancho(array(5,5,5,5,5));
			//$this->SetX(-208);
			$this->Row(array($nomina,numero($asig),numero($ded),numero($pat),numero($total)));

			$tota+=$asig;
			$totd+=$ded;
			$totp+=$pat;
			$tott+=$total;
		}
		$i++;

	}
	
		$this->SetFont('Arial','',9);
		$this->SetWidths(array(70,30,30,30,30));
		$this->SetAligns(array('C','C','C','C','C'));
		$this->Setceldas(array(1,1,1,1,1));
		$this->Setancho(array(5,5,5,5,5));
		//$this->SetX(-208);
		$this->Row(array("TOTAL GRAL.",numero($tota),numero($totd),numero($totp),numero($tott)));

	
}

function Footer(){
	$this->SetY(-15);
	$this->Cell(0,10,utf8_decode('Página ').$this->PageNo().'/{nb}',0,0,'C');
}

}

//Creación del objeto de la clase heredada
$pdf=new PDF('P','mm','LETTER');
$pdf->AliasNbPages();
$pdf->AddPage('P','LETTER');
$pdf->AddFont('Sanserif','','sanserif.php');
$pdf->SetFont('Sanserif','',10);

$pdf->personas($pdf);
$pdf->Output();
?>
