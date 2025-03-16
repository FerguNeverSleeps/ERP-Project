<?php 
//header('Content-Type: text/html; charset=iso-8859-1');
if (!isset($_SESSION)) {
  session_start();
}
require('fpdf.php');
require_once '../lib/config.php';
require_once '../lib/common.php';
include('numerosALetras.class.php');
require_once '../paginas/funciones_nomina.php';


class PDF extends FPDF
{

//Cabecera de página
function Header()
{
        $Conn=conexion();
	$var_sql="select * from nomempresa";
	$rs = query($var_sql,$Conn);
	$row_rs = fetch_array($rs);
// 	$var_encabezado1=$row_rs['nom_emp'];
// 	$var_izquierda='../imagenes/'.$row_rs[imagen_izq];
// 	$var_derecha='../imagenes/'.$row_rs[imagen_der];
	
	$var_encabezado1=$row_rs['nom_emp'];
	$var_encabezado2=$row_rs['encabezado2'];
	$var_encabezado3=$row_rs['encabezado3'];
	$var_encabezado4=$row_rs['encabezado4'];
	$var_imagen_izq='../imagenes/'.$row_rs['imagen_izq'];
	$var_imagen_der='../imagenes/'.$row_rs['imagen_der'];
	$rif=$row_rs[rif];

	$this->SetFont("Arial","B",12);
	$this->Image($var_imagen_der,10,8,33);
 	$this->Ln(11);
	$this->Cell(55);
	
	$this->MultiCell(80,5,$var_encabezado1,0,'C');
	$this->SetFont("Arial","",9);
	$this->Cell(100,8,$rif,0,0,"L");
	//$this->Cell(100,8,$var_encabezado1,0,0,"C");
// 	$this->Image($var_imagen_der,170,15,33);

// 	$this->Image($var_izquierda,10,6,30,15);
// 	$this->Image($var_derecha,10,6,30,15);

	$this->Ln(10);
}
var $widths;
var $aligns;
var $celdas;
var $ancho;
var $nro_ocs;
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

function Footer(){
	$this->SetY(-40);
	$this->Cell(188,10,'','B',0);
	$conexion=conexion();
	$conOCS = 'select * from nomempresa';
	$resOCS = query($conOCS,$conexion);
	$filaOCS = fetch_array($resOCS);
	
	$direccion=$filaOCS['dir_emp'].' '.$filaOCS['ciu_emp'].' '.$filaOCS['edo_emp'];
	$this->Ln();
	$this->SetFont('Arial','I',8);
	$this->Cell(188,5,$direccion,'',0,'C');

	$telefono='Telefonos: '.$filaOCS['tel_emp'];//.' Fax: '.$filaOCS['fax_emp'];
	$this->Ln();
	$this->Cell(188,5,$telefono,'',0,'C');
	
	$this->SetY(-15);
	$this->SetFont('Arial','I',8);
	//$this->Cell(188,10,'Elaborado Por: '.$_SESSION['nombre'],0,0,'L');
	
}
	
function detalle(){

	$conexion=conexion();
	$conOCS = 'select * from nomempresa';
	$resOCS = query($conOCS,$conexion);
	$filaOCS = fetch_array($resOCS);
	$RRHH = $filaOCS['ger_rrhh'];

	$registro_id=$_GET[registro_id];
	$tipo=$_GET[tipN];
	$esta=$_GET[est];
	$query="select * from nompersonal where ficha = '$registro_id' and tipnom='$tipo'";
	$resultado = query($query,$conexion);
	$personal = fetch_array($resultado);
	
	$persona=$personal['apenom'];
	$cedula=$personal['cedula'];
	$fecha=$personal['fecing'];
	$fecha1=$personal['fecing'];

	list($anio,$mes,$dia)=explode("-",$fecha);
   	$fecha= $dia."/".$mes."/".$anio; 
	
	$id_cargo=$personal['codcargo'];
	$query="select * from nomcargos where cod_car = '$id_cargo'";
	$resultado1 = query($query,$conexion);
	$cargo = fetch_array($resultado1);
	$cargo=$cargo['des_car'];

	$id_gerencia=$personal['codnivel1'];
	$query="select * from nomnivel1 where codorg = '$id_gerencia'";
	$resultado2 = query($query,$conexion);
	$ger = fetch_array($resultado2);
	$gerencia=$ger['descrip'];
	
	$monto=$personal['suesal'];
	$ficha=$personal['ficha'];
	
	$monto=$monto;
	
	
	$query="select * from nomcampos_adic_personal where ficha ='$ficha' and id=5";
	$resultado4 = query($query,$conexion);	
	$extra= fetch_array($resultado4);
	$profesional=$extra['valor'];

	$query="select * from nomcampos_adic_personal where ficha ='$ficha' and id=11";
	$resultado4 = query($query,$conexion);	
	$extra= fetch_array($resultado4);
	$ant2=$extra['valor'];
	
	$fecha2=date("Y-m-d");
	
	//$anios=edad($fecha1,$fecha2)-3;
	if($anios>0)
		$mto=($anios*30)+40;
	else
		$mto=0;
// 	echo $mto;
	if($ant2!=0)
		$mto=$ant2;
	
	$mto=$mto+$profesional;
	
	$n = new numerosALetras();
	$salarioletras=$n->convertir($monto+ $compensacion);
	
	$salario=number_format($monto+ $compensacion,2,',','.');
	$sex=$personal['sexo'];
	if($sex=='Femenino'){
		$ciu='la ciudadana ';
		$ad='adscrita ';
	}
	else{
		$ciu='el ciudadano ';
		$ad='adscrito ';
	}

	$conOCS = 'select * from nomempresa';
	$resOCS = query($conOCS,$conexion);
	$filaOCS = fetch_array($resOCS);
	
	$enc=$filaOCS['ger_rrhh'];

	//$this->Ln(20);
	$this->SetFont('Arial','B',14);
	$this->Cell(188,7,'AUTORIDAD DE PASAPORTE',0,1,'C');
	$this->Cell(188,7,'DEPARTAMENTO DE RECURSOS HUMANOS',0,1,'C');
	$this->Cell(188,7,'CARTA DE TRABAJO',0,1,'C');
	$this->Ln(10);
	$this->SetFont('Arial','',10);
	$this->SetWidths(array(80,50,50));
	$this->SetAligns(array('L','L','L'));
    $this->Setceldas(array(0,0,0));
	$this->Setancho(array(5,5,5));
	$this->Row(array('Funcionario:'.utf8_decode($persona),utf8_decode('Cédula: ').$cedula,'Seguro Social:'.$personal['seguro_social']));
	$this->Row(array('N Empleado:'.$personal['ficha'],'Estatus: '.$personal['estado'],'Fecha de Ingreso:'.$fecha));
	
	$this->Cell(188,7,'Cargo:'.$cargo,0,1,'L');
	$this->Cell(188,7,utf8_decode('Ubicación:').$gerencia,0,1,'L');
	$this->Ln(5);
	$this->SetWidths(array(120,80));
	$this->SetAligns(array('L','L'));
    $this->Setceldas(array(0,0));
	$this->Setancho(array(5,5));
	$this->Row(array('Salario Mesual: '.$personal['suesal'],'Desc. 35%: '.number_format(($personal['suesal']*35/100),2,'.','')));
	$this->Row(array('Gastos de Representacion: ','Total Ingresos: '.number_format($personal['suesal'],2,'.','')));	
	
	$this->Cell(188,7,'Seguro Social:'.($personal['suesal']*0.0975),0,1,'L');
	$this->Cell(188,7,'Seguro Educativo:'.($personal['suesal']*0.0125),0,1,'L');
	$this->Cell(188,7,'Seguro Educativo:'.($personal['suesal']*0.0125),0,1,'L');
	
	if($personal['suesal']>11000){
			$islr=($personal['suesal']-11000)*0.15;
			}else{
				$islr=0;
			}
			
	$this->Cell(188,7,'Impuesto Sobre la Renta:'.$islr,0,1,'L');
	
	$this->SetFont('Arial','B',14);
	$this->Cell(188,7,'TOTAL DEDUCCIONES',0,1,'C');
	
	$this->SetFont('Arial','',12);
	
	$this->SetWidths(array(24,80,24,30,30));
	$this->SetAligns(array('C','L','C','R','R'));
    $this->Setceldas(array(1,1,1,1,1));
	$this->Setancho(array(5,5,5,5,5));
	$this->Row(array('Clave','Acreedor','Estado','Desc. Mensual','Saldo'));
	
	$prestamo="select nc.numpre,nc.estadopre,nc.mtocuota,np.descrip,nd.salinicial as monto,np.codigopr as tipo from nomprestamos_detalles nd inner join nomprestamos_cabecera nc on nd.numpre=nc.numpre and nc.estadopre='Pendiente' inner join nomprestamos np on np.codigopr = nc.codigopr where nd.ficha='".$ficha."' and nd.estadopre='Pendiente' order by nd.numcuo ASC ";
	
	$resultadop = query($prestamo,$conexion);	
	$pres=0;$tipo=0;
	while ($rs=fetch_array($resultadop)){
		$this->Setceldas(array(0,0,0,0,0));
		if($tipo!=$rs[tipo]){
			$this->SetFont('Arial','I',10);
			$this->Row(array($rs[numpre],utf8_decode($rs[descrip]),$rs[estadopre],$rs[mtocuota],number_format($rs[monto],2,'.','')));
			
			$pres+=$rs[mtocuota];
			$tipo=$rs[tipo];
		}
	}
	
	$this->Cell(188,7,'Total de Deducciones: '.number_format($pres,2,',','.'),0,1,'C');
	$this->Cell(188,7,'Salario Neto: '.number_format($personal['suesal']-$pres,2,',','.'),0,1,'C');
	
	$this->SetFont('Arial','B',14);
	$this->Cell(188,7,'OBSERVACIONES',1,1,'C');
	
	$this->SetFont('Arial','',12);
	$this->Cell(188,7,'NO HAY OBSERVACIONES',0,1,'L');
	
	$this->Ln(10);
	$this->Cell(60,7,$RRHH,'T',1,'L');
	
	
	
	
}
}
//Creación del objeto de la clase heredada

$pdf=new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Times','',12);
$pdf->detalle();
$pdf->Output();
?>
