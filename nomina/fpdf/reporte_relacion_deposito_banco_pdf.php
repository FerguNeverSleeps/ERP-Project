<?php
session_start();
ob_start();
$termino=$_SESSION['termino'];
$nomina_id=$_GET['nomina_id'];

require('fpdf.php');
require('pdf.php');
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
	$nomina_id=$_GET['nomina_id'];
	$consulta = "SELECT periodo_ini,periodo_fin,frecuencia,anio,mes FROM nom_nominas_pago WHERE codnom=$nomina_id and tipnom=$tipnom";
	$resultado = query($consulta,$conexion);
	$fetch = fetch_array($resultado);
	$fecha_ini = fecha($fetch['periodo_ini']);
        $fecha_fin = fecha($fetch['periodo_fin']);
        $frecuencia = $fetch['frecuencia'];
	$date1=date('d/m/Y');
	$date2=date('h:i a');
        $this->Cell(150,5,utf8_decode($var_encabezado),0,0,'L');
        $this->Cell(38,5,'Fecha: '.$date1,0,1,'R');
        $this->Cell(150,5,utf8_decode('Coordinacion de Recursos Humanos'),0,0,'L');
        $this->Cell(38,5,'Hora: '.$date2,0,1,'R');
        
	$this->Cell(188,5,'RELACION DE DEPOSITOS NETOS  '.$termino,0,1,'C');
	/*if($frecuencia==3){
                if($fetch['mes']<10)
                {    $mes='0'.$fetch['mes'];}
                else{$mes=$fetch['mes'];}
                $fecha_ini=fecha($fetch['anio'].'-'.$mes.'-01');
		$this->Cell(188,5,'DESDE '.$fecha_ini.' AL '.$fecha_fin,0,1,'C');
	        $this->Cell(188,5,'NOMINA DE '.$_SESSION['nomina'].' MENSUAL',0,1,'C');
	}else{
        */      $this->Cell(188,5,'DESDE '.$fecha_ini.' AL '.$fecha_fin,0,1,'C');
	        $this->Cell(188,5,'NOMINA DE '.$_SESSION['nomina'].' ',0,1,'C');
	//}
	$this->Ln(5);

        	// llamado para hacer multilinea sin que haga salto de linea

	$this->SetFont('Arial','',10);
        $this->SetWidths(array(16,20,43,20,44,25,25));
	$this->SetAligns(array('C','R','C','C','L','C','C'));
        $this->Setceldas(array('TB','TB','TB','TB','TB','TB','TB'));
	$this->Setancho(array(5,5,5,5,5,5,5));
        $this->Row(array('# FICHA','MONTO','CUENTA',utf8_decode('CÉDULA'),'NOMBRES','BANCO','TIPO DE CUENTA'));

        $this->Ln(2);
        $this->SetAligns(array('C','R','C','C','L','C','C'));
        $this->Setceldas(array(0,0,0,0,0,0));
	// fin

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


function Cuentas($nomina){

	$conexion=conexion();
	$cod=$_SESSION['codigo_nomina'];
	$consulta="select * from nom_nomina_netos as nn left join nompersonal as np on nn.cedula=np.cedula where nn.codnom=$nomina and nn.tipnom=$cod  order by np.codbancob,np.nombres";
	$resultado_cuenta=query($consulta,$conexion);
	cerrar_conexion($conexion);

	$cantidad_registros=35;
	$totalwhile=num_rows($resultado_cuenta);
	$contar=1;
	$conta=1;
	$total_monto=0;
	while($totalwhile>=$contar)
	{

		$this->SetFont('Arial','',9);
		$fila=fetch_array($resultado_cuenta);
		//$consulta="select apenom,forcob,codbancob,nacionalidad from nompersonal where ficha='".$fila['ficha']."' and tipnom=$_SESSION[codigo_nomina]";
		$conexion=conexion();
		//$resultado_personal=query($consulta,$conexion);
		//$fila_personal=fetch_array($resultado_personal);
		if($fila['nacionalidad']==1)
                    $detalles="E";
                elseif($fila['nacionalidad']==0)
                    $detalles="V";
                
                $cedula=$detalles.$fila['cedula'];
                $query="select cod_ban,des_ban from nombancos where cod_ban='".$fila['codbancob']."'";
                $resultado_banco=query($query,$conexion);
                $row2 = mysqli_fetch_array($resultado_banco);
        
                $this->SetAligns(array('C','R','C','C','L','C','C'));
                $this->Setceldas(array(0,0,0,0,0,0,0));
                $this->Row(array($fila['ficha'],number_format($fila['neto'],2,',','.'),$fila['cta_ban'],$cedula,utf8_decode($fila['apenom']),$row2['des_ban'],$fila['forcob']));
		$total_monto+=$fila['neto'];
		/*if($conta==$cantidad_registros){
			$this->Ln(300);

			$this->SetFont('Arial','',10);
			// llamado para hacer multilinea sin que haga salto de linea
			$this->SetWidths(array(20,20,85,43,25));
			$this->SetAligns(array('C','C','C','C','C'));
			$this->Setceldas(array('TB','TB','TB','TB','TB'));
			$this->Setancho(array(5,5,5,5,5));
			$this->Row(array('# FICHA',utf8_decode('CÉDULA'),'APELLIDOS Y NOMBRES','CUENTA','MONTO'));
			$conta=0;
			// fin

		}*/
		$conta++;
		$contar++;


	}
	$this->Ln(2);

	$this->SetFont('Arial','',10);
	$this->Cell(100,5,'CANTIDAD DE PERSONAS: '.$totalwhile,0,0,'C');
	$this->Cell(88,5,'TOTAL MONTO: '.number_format($total_monto,2,',','.'),0,1,'C');
}

function finalizar($pdf){

        $this->SetY(-45);
	$bool=validar_firma("RESUMENNOMINA");
	if ($bool==true){
                firma_dinamica("RESUMENNOMINA",  $pdf,6,5);
        }
}

function Footer(){
	$this->SetY(-15);
	$this->Cell(0,10,utf8_decode('Página ').$this->PageNo().'/{nb}',0,0,'C');
}


}


//Creación del objeto de la clase heredada
$pdf=new PDF();
$pdf->AliasNbPages();
$pdf->AddPage('P','A4');
$pdf->AddFont('Sanserif','','sanserif.php');
$pdf->SetFont('Sanserif','',12);

$pdf->nominapdf=$nomina_id;

$pdf->Cuentas($nomina_id);
$pdf->finalizar($pdf);
$pdf->Output();
?>
