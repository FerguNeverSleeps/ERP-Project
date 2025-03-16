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

	$meses = array(); 
    $meses[1] = "Enero"; 
    $meses[2] = "Febrero"; 
    $meses[3] = "Marzo"; 
    $meses[4] = "Abril";
    $meses[5] = "Mayo";
    $meses[6] = "Junio";
    $meses[7] = "Julio";
    $meses[8] = "Agosto";
    $meses[9] = "Septiembre";
    $meses[10] = "Octubre";
    $meses[11] = "Noviembre";
    $meses[12] = "Diciembre";
   
	//$this->Image($var_izquierda,10,6,30,15);
	//$this->Image($var_derecha,170,6,30,15);
	$this->Ln(10);
	$this->SetFont('Arial','B',12);
	$this->Cell(188,5,utf8_decode('LISTADO DE COLABORADORES DE CUMPLEAÑOS'),0,1,'C');
	$this->Cell(188,5,'MES : '.$meses[$_GET[mes]],0,1,'C');
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
	$consulta="select apenom,fecnac,cedula,ficha,n7.codorg AS c7,n6.codorg AS c6,n5.codorg AS c5,n4.codorg AS c4,n3.codorg AS c3,n2.codorg AS c2,n1.codorg AS c1,n7.descrip as dn7,n7.ee as cn7,n6.descrip as dn6,n6.ee as cn6,n5.descrip as dn5,n5.ee as cn5,n4.descrip as dn4,n4.ee as cn4,n3.descrip as dn3,n3.ee as cn3,n2.descrip as dn2,n2.ee as cn2,n1.descrip as dn1,n1.ee as cn1
				from nompersonal as np 
				left join nomnivel7 n7 on n7.codorg=codnivel7
				left join nomnivel6 n6 on n6.codorg=codnivel6
				left join nomnivel5 n5 on n5.codorg=codnivel5
				left join nomnivel4 n4 on n4.codorg=codnivel4
				left join nomnivel3 n3 on n3.codorg=codnivel3
				left join nomnivel2 n2 on n2.codorg=codnivel2
				left join nomnivel1 n1 on n1.codorg=codnivel1
				where MONTH(np.fecnac)=$mes and np.estado <> 'Egresado'
				ORDER BY dn7,dn6,dn5,dn4,dn3,dn2,dn1,np.apenom ASC";
	$query=query($consulta,$conexion);
	
				

		
	$this->SetWidths(array(30,25,40,10,70));
	$this->SetAligns(array('C','C','C','L','L'));
	$this->Setceldas(array(1,1,1,1,1));
	$this->Setancho(array(5,5,5,5,5));

	

	
	$departamento='';
	while($fila=fetch_array($query)){

				//fecha de nacimiento
				$fecha =  $fecha = explode("-",$fila[fecnac]); 

				$anonaz= $fecha[0]; //año
				$mesnaz= $fecha[1]; //mes
				$dianaz= $fecha[2]; //dia;

				//fecha actual

				$dia=date(j);
				$mes=date(n);
				$ano=date(Y);


//si el mes es el mismo pero el día inferior aun no ha cumplido años, le quitaremos un año al actual

if (($mesnaz == $mes) && ($dianaz > $dia)) {
$ano=($ano-1); }

//si el mes es superior al actual tampoco habrá cumplido años, por eso le quitamos un año al actual

if ($mesnaz > $mes) {
$ano=($ano-1);}

//ya no habría mas condiciones, ahora simplemente restamos los años y mostramos el resultado como su edad

$edad=($ano-$anonaz);



 

		if($departamento!=$fila[dn7] and $fila[dn7]!=null){
				$this->SetFont('Arial','B',8);
				$this->Cell(188,5,strtoupper($fila[dn7]),0,1,'C');
				$this->Row(array('CEDULA','FICHA','FECHA DE NACIMIENTO','EDAD','NOMBRE Y APELLIDO'));
				
				$departamento=$fila[dn7];
		}else if($fila[dn7==null]){
			if($departamento!=$fila[dn6] and $fila[dn6]!=null){
				$this->SetFont('Arial','B',8);
				$this->Cell(188,5,strtoupper($fila[dn6]),0,1,'C');
				$this->Row(array('CEDULA','FICHA','FECHA DE NACIMIENTO','EDAD','NOMBRE Y APELLIDO'));
				$departamento=$fila[dn6];
			}else if($fila[dn6==null]){
				if($departamento!=$fila[dn5] and $fila[dn5]!=null){
					$this->SetFont('Arial','B',8);
					$this->Cell(188,5,strtoupper($fila[dn5]),0,1,'C');
					$this->Row(array('CEDULA','FICHA','FECHA DE NACIMIENTO','EDAD','NOMBRE Y APELLIDO'));
					$departamento=$fila[dn5];
				}else if($fila[dn5==null]){
					if($departamento!=$fila[dn4] and $fila[dn4]!=null){
						$this->SetFont('Arial','B',8);
							$this->Cell(188,5,strtoupper($fila[dn4]),0,1,'C');
							$this->Row(array('CEDULA','FICHA','FECHA DE NACIMIENTO','EDAD','NOMBRE Y APELLIDO'));
							$departamento=$fila[dn4];
					}else if($fila[dn4==null]){
						if($departamento!=$fila[dn3] and $fila[dn3]!=null){
							$this->SetFont('Arial','B',8);
								$this->Cell(188,5,strtoupper($fila[dn3]),0,1,'C');
								$this->Row(array('CEDULA','FICHA','FECHA DE NACIMIENTO','EDAD','NOMBRE Y APELLIDO'));
								$departamento=$fila[dn3];
						}else if($fila[dn3==null]){
							if($departamento!=$fila[dn2] and $fila[dn2]!=null){
								$this->SetFont('Arial','B',8);
									$this->Cell(188,5,strtoupper($fila[dn2]),0,1,'C');
									$this->Row(array('CEDULA','FICHA','FECHA DE NACIMIENTO','EDAD','NOMBRE Y APELLIDO'));
									$departamento=$fila[dn2];
							}else if($fila[dn2==null]){
								if($departamento!=$fila[dn1] and $fila[dn1]!=null){
									$this->SetFont('Arial','B',8);
									$this->Cell(188,5,strtoupper($fila[dn1]),0,1,'C');
									$this->Row(array('CEDULA','FICHA','FECHA DE NACIMIENTO','EDAD','NOMBRE Y APELLIDO'));
									$departamento=$fila[dn1];
								}		
							}		
						}		
					}		
				}		
			}		
		}
		$this->SetFont('Arial','I',8);
		$this->Row(array($fila[cedula],$fila[ficha],fecha($fila[fecnac]),$edad,$fila[apenom]));
		
	}
	//$this->Ln(300);
	$this->SetFont("Arial","B",11);
	$this->Cell(188,5,utf8_decode('TOTAL CUMPLEAÑOS: ').num_rows($query),0,1,'C');

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