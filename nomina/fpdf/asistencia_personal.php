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
//	$this->Image($var_derecha,170,6,30,15);
	$this->Ln(10);
	$this->SetFont('Arial','B',12);
	$this->Cell(188,5,'INFORME DE ASISTENCIA DEL PERSONAL',0,1,'C');
	
	$this->Ln(10);
	
	$this->SetFont('Arial','B',8);
	$this->SetWidths(array(20,60));
	$this->SetAligns(array('R','L'));
	$this->Setceldas(array(0,0));
	$this->Setancho(array(5,5));

	$this->Row(array('No.','Colaborador'));
	$this->SetFont('Arial','B',8);
	$this->SetWidths(array(17,12,15,15,15,15,15,15,15,15,15,15,15,15));
	$this->SetAligns(array('C','C','C','C','C','C','C','C','C','C','C','C','C'));
	$this->Setceldas(array(0,0,0,0,0,0,0,0,0,0,0,0,0,0));
	$this->Setancho(array(5,5,5,5,5,5,5,5,5,5,5,5,5,5));
	$this->Row(array('Fecha','Turno','E', 'SA', 'EA', 'S', 'Ord.', 'Dom.', 'Nac.','T','EM','DNT'));
	
/*	$this->SetWidths(array(0));
	$this->SetAligns(array('C'));
	$this->Setceldas(array("T"));
	$this->Setancho(array(2));	
	$this->Row(array(""));
*/

}

function Footer(){
	$this->SetY(-15);
	$this->SetFont('Arial','I',8);
	$this->Cell(0,5,utf8_decode('Página ').$this->PageNo().'/{nb}',0,1,'C');
   // $this->Cell(189,10,'E=Entrada S=Salida SA=Salida Almuerzo EA=Entrada Almuerzo',0,1,'L');
	$this->Cell(188,10,'Elaborado Por: '.$_SESSION['nombre'],0,0,'L');

//'E', 'SA', 'EA', 'S', 'H', 'D', 'N','T','EM','DNT'
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

function detalle($reg){
	$conexion=conexion();
        
    $sql1 = "SELECT DISTINCT np.codnivel1, nn.descrip as departamento
         FROM   nompersonal np
         INNER JOIN nomnivel1 nn ON nn.codorg=np.codnivel1
         ORDER BY np.codnivel1";

    $res1=query($sql1, $conexion);
    
    while($row=fetch_array($res1))
    {
   
            $codnivel1=$row['codnivel1'];
            $departamento=$row['departamento'];
            $this->Row(array('UNIDAD',$departamento));
            
            $sql = "SELECT DISTINCT np.ficha, np.apenom as nombre
                                FROM   reloj_detalle rd, nompersonal np
                                WHERE   rd.ficha=np.ficha  AND rd.id_encabezado=".$reg." AND np.codnivel1='".$codnivel1."'
                                ORDER BY np.apenom";
            $res=query($sql, $conexion);
            
             while($fila=fetch_array($res))
            {
                    $ficha=$fila['ficha'];
                    $consulta="SELECT
                     reloj_encabezado.cod_enca,
                     reloj_encabezado.fecha_reg,
                     reloj_encabezado.fecha_ini,
                     reloj_encabezado.fecha_fin,
                     reloj_detalle.id,
                     reloj_detalle.id_encabezado,
                     reloj_detalle.ficha,
                     reloj_detalle.fecha,
                     nomturnos.turno_id,
                     nomturnos.descripcion,
                     nomturnos.entrada,
                     nomturnos.tolerancia_entrada,
                     nomturnos.inicio_descanso,
                     nomturnos.salida_descanso,
                     nomturnos.tolerancia_descanso,
                     nomturnos.salida,
                     nomturnos.tolerancia_salida,
                     nompersonal.apenom,
                     reloj_detalle.entrada,
                     reloj_detalle.salmuerzo,
                     reloj_detalle.ealmuerzo,
                     reloj_detalle.salida,
                     reloj_detalle.ordinaria,
                     reloj_detalle.extra,
                     reloj_detalle.extraext,
                     reloj_detalle.extranoc,
                     reloj_detalle.extraextnoc,
                     reloj_detalle.domingo,
                     reloj_detalle.nacional,
                     reloj_detalle.emergencia,
                     reloj_detalle.descansoincompleto,
                     reloj_detalle.tardanza
                FROM
                     nomturnos INNER JOIN nompersonal ON nomturnos.turno_id = nompersonal.turno_id
                     INNER JOIN reloj_detalle ON nompersonal.ficha = reloj_detalle.ficha
                     INNER JOIN reloj_encabezado ON reloj_detalle.id_encabezado = reloj_encabezado.cod_enca
                WHERE
                     reloj_encabezado.cod_enca = '$reg' AND reloj_detalle.ficha ='$ficha'
                ORDER BY
                     ficha ASC,
                     fecha ASC";
                        $query=query($consulta,$conexion);

                        $fichaaux="";
                        $fechaaux="";
                        $apenomaux="";
                        $i=0;
                        $j=0;
                        while($fila=fetch_array($query))
                        {
                                $date = new DateTime($fila[fecha]);
                                $fecha = $date->format('Y-m-d');
                                $hora = $date->format('H:i');
                                $ficha = $fila[ficha];

                                if($i==0)
                                {
                                        $this->SetFont('Arial','B',8);
                                        $this->SetWidths(array(20,60));
                                        $this->SetAligns(array('R','L'));
                                        $this->Setceldas(array(0,0));
                                        $this->Setancho(array(5,5));
                                        $this->Row(array($fila[ficha],$fila[apenom]));

                                        $fichaaux=$fila[ficha];
                                        $fechaaux=$fecha;
                                        $apenomaux=$fila[apenom];
                                }
                                if($ficha!=$fichaaux)
                                {


                                        //TOTALES
                                        $this->SetWidths(array(0));
                                        $this->SetAligns(array('C'));
                                        $this->Setceldas(array("B"));
                                        $this->Setancho(array(5));	
                                        $this->Row(array(""));

                                        $this->SetFont('Arial','B',8);
                                        $this->SetWidths(array(20,60));
                                        $this->SetAligns(array('R','L'));
                                        $this->Setceldas(array(0,0));
                                        $this->Setancho(array(5,5));
                                        $this->Row(array($fila[ficha],$fila[apenom]));

                                        $fichaaux=$ficha;

                                        $apenomaux=$fila[apenom];

                                }


                                $this->SetFont('Arial','',8);
                                $this->SetWidths(array(17,12,15,15,15,15,15,15,15,15,15,15,15,15));
                        $this->SetAligns(array('C','C','C','C','C','C','C','C','C','C','C','C','C'));
                        $this->Setceldas(array(0,0,0,0,0,0,0,0,0,0,0,0,0,0));
                        $this->Setancho(array(5,5,5,5,5,5,5,5,5,5,5,5,5,5));
                                $this->Row(array($fecha,$turno,$fila[entrada],$fila[salmuerzo],$fila[ealmuerzo],$fila[salida],$fila[ordinaria],$fila[domingo],$fila[nacional],$fila[tardanza],$fila[emergencia],$fila[descansoincompleto]));
                                $turno=$entrada=$salmu=$ealmu=$salida=$regular=$ausen=$tardan=$incapac=$sobret=$feriado="";
                                $entradaf=$salmuf=$ealmuf=$salidaf="";


                                $fechaaux=$fecha;
                                $j=0;


                                $turno = $fila[turno_id];


                                $j++;
                                $i++;	
                        }
                //$this->Ln(300);
                //$this->SetFont("Arial","B",11);
                //$this->Cell(188,5,utf8_decode('TOTAL : ').num_rows($query),0,1,'C');
            }
    }
}
}
//Creación del objeto de la clase heredada
$pdf=new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Times','',12);
$reg=$_GET['reg'];
$pdf->detalle($reg);

$pdf->Output();
?>