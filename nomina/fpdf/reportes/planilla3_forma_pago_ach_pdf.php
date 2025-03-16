<?php 
session_start();
ob_start();
$termino=$_SESSION['termino'];
$codnom=$_GET['codnom'];
$codtip=$_GET['codtip'];


require('../fpdf.php');
include("../../lib/common.php");


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
	
	
	$this->SetFont('Arial','',10);
	
	$codnom=$_GET['codnom'];
        $codtip=$_GET['codtip'];
//	$consulta = "SELECT periodo_ini,periodo_fin FROM nom_nominas_pago WHERE codnom=$nomina_id and tipnom=$tipnom";
        $consulta_nomina = "SELECT np.periodo_ini as desde, np.periodo_fin as hasta,  ntn.descrip as descrip_planilla
		 FROM nom_nominas_pago np 
		 INNER JOIN nomtipos_nomina ntn on (np.tipnom = ntn.codtip)
		 WHERE  np.codnom=".$codnom." AND np.tipnom=".$codtip;
	$resultado_nomina = query($consulta_nomina,$conexion);
	$fetch = fetch_array($resultado_nomina);
	$fecha_ini = fecha($fetch['desde']);
	$fecha_fin = fecha($fetch['hasta']);
	$date1=date('d/m/Y');
	$date2=date('h:i a');	
        $descrip_planilla = $fetch['descrip_planilla'];
        
	$this->Cell(150,5,utf8_decode($var_encabezado),0,0,'L');
	$this->Cell(38,5,'Fecha:  '.$date1,0,1,'R');
	$this->Cell(150,5,'',0,0,'L');
	$this->Cell(38,5,'Hora: '.$date2,0,1,'R');
        $this->Cell(188,6,''.$descrip_planilla.' III',0,1,'C');
//	if($this->nomina==4){
		$this->Cell(120,6,'FORMA DE PAGO: ACH',0,0,'C');
//	}else{
//     		$this->Cell(188,8,'PAGOS COLABORADORES AL BANCO',0,1,'C');
//	}
	
	$this->Cell(20,6,'LAPSO DEL '.$fecha_ini.' AL '.$fecha_fin,0,1,'C');
//	if($this->nomina==3){
//		$this->Cell(188,8,''.$descrip_planilla.' I',0,1,'C');
//	}else{
//     		$this->Cell(188,8,''.$_SESSION['nomina'].' ',0,1,'C');
//	}
	$this->Ln(5);
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
	$codnom=$_GET['codnom'];
        $codtip=$_GET['codtip'];
        $sql_empleados = "SELECT DISTINCT np.ficha, np.cedula, np.apenom as nombre, np.suesal as sueldo, np.cuentacob as cuenta
		FROM   nom_movimientos_nomina nm 
		INNER JOIN nompersonal np ON (nm.ficha=np.ficha)
		WHERE  nm.codnom=".$codnom." AND nm.tipnom=".$codtip." AND (nm.codcon >=300 AND nm.codcon <=399) AND nm.monto >= 0
                       AND (np.forcob LIKE '%Deposito%' OR np.forcob LIKE '%deposito%'
                            OR np.forcob LIKE '%Tarjeta%' OR np.forcob LIKE '%tarjeta%')
		ORDER BY np.ficha";
//        echo $sql_empleados;
//        exit;
//	$consulta="select * from nom_nomina_netos where codnom=$nomina and tipnom=$cod and cta_ban<>''";
	$resultado_empleados=query($sql_empleados,$conexion);
//	cerrar_conexion($conexion);
	// llamado para hacer multilinea sin que haga salto de linea
	
	$this->SetFont('Arial','',9);
        $this->SetWidths(array(15,20,75,20,45));
	$this->SetAligns(array('C','C','C','C','C'));
        $this->Setceldas(array('B','B','B','B','B'));
	$this->Setancho(array(5,5,5,5,5,5));
        $this->Row(array('Cod. ',utf8_decode('Cédula'),'Apellidos Nombres del Trabajador','Neto Pago','Numero de Cuenta Bancaria'));
	
	// fin

	$cantidad_registros=40;
	$totalwhile=num_rows($resultado_empleados);
	$contar=1;
	$conta=1;
	$total_monto=0;
	while($fila_empleados=fetch_array($resultado_empleados))
	{
		
		$this->SetFont('Arial','',8);
//		$consulta="select apenom,codbancob from nompersonal where ficha='".$fila['ficha']."' and cedula='".$fila[cedula]."'";
//		$conexion=conexion();
//		$resultado_personal=query($consulta,$conexion);
//		$fila_personal=fetch_array($resultado_personal);
//
//                $query="select cod_ban,des_ban from nombancos where cod_ban='".$fila_personal['codbancob']."'";
//                $resultado_banco=query($query,$conexion);
//                $row2 = mysqli_fetch_array($resultado_banco);     
                
                $ficha=$fila_empleados['ficha'];
                $cedula=$fila_empleados['cedula'];
                $nombre=$fila_empleados['nombre'];
                $sueldo=$fila_empleados['sueldo'];
                $cuenta=$fila_empleados['cuenta'];
                
                $sql_movimientos="SELECT nm.codcon, nm.tipcon, nm.monto 
                                FROM nom_movimientos_nomina nm 
                                WHERE nm.ficha=".$ficha." AND nm.codnom=".$codnom." AND nm.tipnom=".$codtip." AND nm.monto > 0 
                                    AND (nm.codcon >=300 AND nm.codcon <=399)
                                ORDER BY nm.tipcon, nm.codcon";
                $resultado_movimientos=query($sql_movimientos,$conexion);
                $asignaciones=$deducciones=$neto=0;
                while($fila_movimientos=fetch_array($resultado_movimientos))
                {
                    if($fila_movimientos['tipcon']==='A')
                    {
                        $asignaciones+=$fila_movimientos['monto'];
                    }
                    
                    if($fila_movimientos['tipcon']==='D')
                    {
                        $deducciones+=$fila_movimientos['monto'];
                    }
                }
                $neto=$asignaciones-$deducciones;
		$this->Setceldas(array(0,0,0,0,0));
		$this->SetAligns(array('C','L','L','R','L'));
		$this->Row(array($ficha,$cedula,utf8_decode($nombre),number_format($neto,2,',','.'),$cuenta));
		$total_monto+=$neto;
		if($conta==$cantidad_registros){
			$this->Ln(300);
			
			$this->SetFont('Arial','',9);
                         $this->SetWidths(array(15,20,75,20,45));
                        $this->SetAligns(array('C','C','C','C','C'));
                        $this->Setceldas(array('B','B','B','B','B'));
                        $this->Setancho(array(5,5,5,5,5,5));
                        $this->Row(array('Cod. ',utf8_decode('Cédula'),'Apellidos Nombres del Trabajador','Neto Pago','Numero de Cuenta Bancaria'));
	
	// fin
			$conta=0;
			// fin
			
		}
		$conta++;
		$contar++;

		
	}
	$this->SetFont('Arial','B',7);
        $this->SetWidths(array(10,20,63,18,8,8,8,8,8,10,10,10,10));
        $this->SetAligns(array('C','C','C','R','R','R','R','R','R','R','R','R','R'));
        $this->Setceldas(array('T','T','T','T','T','T','T','T','T','T','T','T','T'));
        $this->Setancho(array(5,5,5,5,5,5,5,5,5,5,5,5,5));
        $this->Row(array('','','','','','','','','','','','',''));
	$this->Ln(2);
	
	$this->SetFont('Arial','B',9);
	$this->Cell(100,5,'CANTIDAD DE EMPLEADOS: '.$totalwhile,0,0,'C');
	$this->Cell(88,5,'TOTAL: '.number_format($total_monto,2,',','.'),0,1,'C');
}
function Footer(){
	$this->SetY(-15);
	$this->Cell(0,10,utf8_decode('Página ').$this->PageNo().'/{nb}',0,0,'C');
}


}


//Creación del objeto de la clase heredada
$pdf=new PDF();
$pdf->AliasNbPages();
$pdf->AddPage('P','Letter');
$pdf->AddFont('Sanserif','','sanserif.php');
$pdf->SetFont('Sanserif','',12);

$pdf->nominapdf=$nomina;

$pdf->Cuentas($nomina);

$pdf->Output();
?>