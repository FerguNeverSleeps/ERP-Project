<?php 
if (!isset($_SESSION)) {
  session_start();
  ob_start();
}

require('fpdf.php');
require_once('../../lib/database.php');
$db = new Database($_SESSION['bd']);
       
//include_once('../clases/database.class.php');
//include('../obj_conexion.php');
global $fecha_inicio;
global $fecha_fin;
$fecha_inicio   = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : NULL;
$fecha_fin      = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : NULL;



class PDF extends FPDF 
{
   

var $usuario;
var $pdff;
var $odp;
var $tipo;
var $dbC ; //echo DB_HOST.' '.DB_USUARIO.' '.DB_CLAVE.' '.$_SESSION['bd'];	;
//Cabecera de página
function Header()
{		
        $fecha_inicio = $GLOBALS['fecha_inicio'];
        $fecha_fin = $GLOBALS['fecha_fin'];
        
        // Arial bold 10
        $this->SetFont('Arial','B',10);       
        // Title
        //$this->Cell(80);
        $this->Cell(280,6,'FEDERAL SECURITY',0,0,'C');
        $this->Ln(2);
        $this->Cell(280,12,'UNIDAD RECURSOS HUMANOS',0,0,'C');
        $this->Ln(2);
        $this->cell(280,18,'VENCIMIENTO DE IMPLEMENTOS / HERRAMIENTAS - EMPLEADO : '.$fecha_inicio.' / '.$fecha_fin,0,0,'C');
        $this->Ln();
        
}


//Hacer que sea multilinea sin que haga un salto de linea

var $widths;
var $aligns;

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


function imprimir_datos($pdf,$dataNomPersonal)
{          
        $fecha_inicio   = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : NULL;
        $fecha_fin      = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : NULL;
        $this->Ln(3);
//        $this->SetFont('Arial','B',10);
//        $this->cell(280,8,'REPORTE DE LICENCIAS - PERIODO: '.$fecha_inicio.' / '.$fecha_fin,0,0,'C');
//        $this->Ln();
        $this->SetFont('Arial','',6);
        $this->SetWidths(array(30,15,10,10,35,15,30,20,30,30,25,20));
        $this->SetAligns(array('C','C','C','C','C','C','C','C','C','C','C','C'));
        $this->Row(array('NOMBRE', utf8_decode('CÉDULA'), 'PLAN.', 'POS','CARGO','SAL.',utf8_decode('REGIÓN'),'TIEMPO','PERIODO','TIPO DE LIC.','RESOL.','MOTIVO'));
        // Carga de datos  
        $this->SetAligns(array('L','C','C','C','L','R','L','C','C','L','C','L'));
        while($exp = $dataNomPersonal->fetch_assoc()){
                //$array_fecha=explode('-',$analisis['fecha']);
                $this->Row(array(utf8_decode($exp['apenom']), $exp['cedula'], $exp['tipnom'],$exp['nomposicion_id'] ,$exp['des_car'],$exp['suesal'],$exp['descrip'],$exp['meses'],$exp['fecha_inicio'].' AL '.$exp['fecha_fin'],  utf8_decode($exp['nombre_subtipo']),$exp['numero_resolucion'],$exp['descripcion']));
                
        }                     
        $this->Ln();                
}



// Tabla simple


function BasicTable($header, $data,$cell,$align)
{    
    $j=0;
    // Cabecera
    foreach($header as $col){
        $this->Cell($cell[$j],7,$col,1,0,'C');
        $j++;
    }
    $this->Ln();
    // Datos
    foreach($data as $row)
    {
        $i=0;
        foreach($row as $col){
            $this->Cell($cell[$i],6,$col,1,0,$align[$i]);
            $i++;
        }
            
        $this->Ln();
    }
}




function BasicTable2($header, $data,$cell,$alto)
{   
    
    // Cabecera                      
    $this->Row($header);   
    //
    //Cuerpo
        foreach($data as $row)
        {      
                $this->Row($row);                                             
        }               
    // Datos
    
}




//Pie de página
function Footer()
{
   
    	//Posición: a  cm del final
   	//$this->SetY(-47);
    	
	

	//reajustar posicion tabla
	//$x=$this->GetX();
        //$y=$this->GetY();
        
	//$this->SetXY($x,$y-8);
//	$bool=validar_firma("ODP".$this->tipo);
//	if ($bool==true){
//		firma_dinamica("ODP".$this->tipo,$this->pdff,6,10);
//	}else{
//		odp($this->pdff);
//	}

    	//$this->SetFont('Arial','I',8);
    	//$this->Cell(0,5,'Elaborado Por: ',0,1,'L');
        
        //echo " Entro Analisis Cambio Categoria PDF - FOOTER ";
//	$this->Cell(0,5,utf8_decode('Página ').$this->PageNo().'/{nb}',0,1,'C');
}
}

//Creación del objeto de la clase heredada
$pdf=new PDF();
$pdf->AliasNbPages();
$pdf->AddPage('L');
/*
 *   15-> Con Sueldo
         16-> Sin Sueldo
         17 -> Especiales
 * */

$fecha_inicio   = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : NULL;
$fecha_fin      = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : NULL;
$tipo_con_sueldo    =   15;
$tipo_sin_sueldo    =   16;
$tipo_especialidad  =   17;



//SELECT * FROM expediente as exp, nompersonal as nom, expediente_tipo as expTipo, expediente_subTipo as expSub WHERE (exp.fecha_inicio>=2016-01-01 OR exp.fecha_fin<=2016-02-30) AND nom.cedula=exp.cedula and exp.tipo=expTipo.id_expediente_tipo	 AND (expTipo.id_expediente_tipo=15 OR expTipo.id_expediente_tipo=16 OR expTipo.id_expediente_tipo=17) AND exp.subtipo=expSub.id_expediente_subtipo
$sql1   = "SELECT * FROM expediente as exp, nompersonal as nom, expediente_tipo as expTipo, expediente_subtipo as expSub  ,nomcargos as nomCar, nomnivel1 as nomN1 WHERE (exp.fecha>='".$fecha_inicio."' AND exp.fecha<='".$fecha_fin."') AND nom.cedula=exp.cedula and exp.tipo=expTipo.id_expediente_tipo AND (expTipo.id_expediente_tipo=".$tipo_con_sueldo." OR expTipo.id_expediente_tipo=".$tipo_sin_sueldo." OR expTipo.id_expediente_tipo=".$tipo_especialidad.") AND exp.subtipo=expSub.id_expediente_subtipo AND nomCar.cod_car=nom.codcargo AND nomN1.codorg=nom.codnivel1 ";
//echo $sql1;
//echo $exp['meses'];
//exit;
$dataNompersonal = $db->query($sql1);

$pdf->imprimir_datos($pdf,$dataNompersonal);


ob_end_clean();
$pdf->Output();
?>

