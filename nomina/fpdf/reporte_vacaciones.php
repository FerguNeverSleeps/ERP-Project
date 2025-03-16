<?php 
if (!isset($_SESSION)) {
  session_start();
}
require('fpdf.php');
require_once '../lib/config.php';
require_once '../lib/common.php';

include('numerosALetras.class.php');
function fecha($value) { // fecha de YYYY/MM/DD a DD/MM/YYYY
  if ( ! empty($value) ) return substr($value,8,2) ."/". substr($value,5,2) ."/". substr($value,0,4);
  }
class PDF extends FPDF
{

//Cabecera de página
function Header()
{
	$Conn=conexion();
	$var_sql="select * from nomempresa";
	$rs = query($var_sql,$Conn);
	$row_rs = fetch_array($rs);
	$var_encabezado1=$row_rs['nom_emp'];
	$var_izquierda='../imagenes/'.$row_rs[imagen_izq];
	$var_derecha='../imagenes/'.$row_rs[imagen_der];

	
   
	$this->Image($var_izquierda,10,6,30,15);
	$this->Image($var_derecha,170,6,30,15);
	$this->Ln(10);
	$this->SetFont('Arial','B',12);
	$this->Cell(188,5,'LISTADO DE VACACIONES',0,1,'C');
	if($_POST[imp]=='Todos')
		$this->Cell(188,5,'TODO EL PERSONAL',0,1,'C');
	else
		$this->Cell(188,5,'POR PERSONA',0,1,'C');
	$this->Ln(10);
	

}

function Footer(){
	$this->SetY(-15);
	$this->SetFont('Arial','I',8);
	$this->Cell(0,5,utf8_decode('Página ').$this->PageNo().'/{nb}',0,1,'C');
	$this->Cell(188,10,'Elaborado Por: '.$_SESSION['nombre'],0,0,'L');

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
        $this->MultiCell($w,5,$data[$i],$this->celdas[$i],$a);
        //Put the position to the right of the cell
        $this->SetXY($x+$w,$y);
    }
    //Go to the next line
    $this->Ln($h);
}
//fin

function detalle($mes){
	$conexion=conexion();
	if($_POST[imp]=='Todos'){
		$consulta='select np.ficha, np.cedula,np.apenom,ne.*  from nomexpediente ne inner join nompersonal np on np.cedula=ne.cedula  where tipo_registro="Vacaciones" and ne.fecha BETWEEN "'.fecha_sql($_POST[mesano]).'" and "'.fecha_sql($_POST[mesano1]).'" order by np.ficha';
	}else{
		$consulta='select np.ficha, np.cedula,np.apenom,ne.*  from nomexpediente ne inner join nompersonal np on np.cedula=ne.cedula where tipo_registro="Vacaciones" and np.ficha="'.$_POST[cod].'" and ne.fecha BETWEEN "'.fecha_sql($_POST[mesano]).'" and "'.fecha_sql($_POST[mesano1]).'" order by np.ficha';
	}
	
	$query=query($consulta,$conexion);
	
		
	$this->SetWidths(array(20,20,20,20,30,30,30));
	$this->SetAligns(array('C','C','C','C','C','C','C'));
	$this->Setceldas(array(1,1,1,1,1,1,1));
	$this->Setancho(array(5,5,5,5,5,5,5));

	

	
	$persona='';
	while($fila=fetch_array($query)){
		if($persona!=$fila[ficha]){
			$this->SetFont('Arial','B',10);
			$this->Cell(188,5,$fila[ficha].' - ('.$fila[cedula].') : '.strtoupper($fila[apenom]),0,1,'L');
			$this->SetFont('Arial','B',8);
			$this->Row(array('FECHA','DURACION','SALIDA','RETORNO','RESTANTES (1)','RESTANTES (2)','RESTANTES (3)'));
			$persona=$fila[ficha];
		}
		
		$this->SetFont('Arial','I',8);
		$this->Row(array(fecha($fila[fecha]),$fila[dias],fecha($fila[fecha_salida]),fecha($fila[fecha_retorno]),$fila[hasta],$fila[desde],$fila[horas]));
		
	}
	//$this->Ln(300);
	//$this->SetFont("Arial","B",11);
	//$this->Cell(188,5,utf8_decode('TOTAL CUMPLEAÑOS: ').num_rows($query),0,1,'C');

}
}
//Creación del objeto de la clase heredada
$pdf=new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Times','',12);
$mes=$_GET['mes'];
$pdf->detalle($mes);

$pdf->Output();
?>