<?php 
session_start();
ob_start();
$termino=$_SESSION['termino'];
$nomina_id=$_GET['nomina'];
$codtip = $_GET['codt'];



require('fpdf.php');
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
	
	
	$this->SetFont('Arial','',12);
	
	$tipnom=$_SESSION['codigo_nomina'];
	$nomina_id=$_GET['nomina'];
	$consulta = "SELECT periodo_ini,periodo_fin FROM nom_nominas_pago WHERE codnom=$nomina_id and tipnom=$tipnom";
	$resultado = query($consulta,$conexion);
	$fetch = fetch_array($resultado);
	$fecha_ini = fecha($fetch['periodo_ini']);
	$fecha_fin = fecha($fetch['periodo_fin']);
	$date1=date('d/m/Y');
	$date2=date('h:i a');	

	$this->Cell(150,5,utf8_decode($var_encabezado),0,0,'L');
	$this->Cell(0,5,'Fecha:  '.$date1,0,1,'R');
	$this->Cell(0,5,'Hora: '.$date2,0,1,'R');
	$this->Cell(0,8,'RECAPITULACION DE PAGO',0,1,'C');
	
	if($this->nomina==3){
		$this->Cell(0,8,$_SESSION['nomina'].' SEMANAL',0,1,'C');
	}else{
     		$this->Cell(0,8,$_SESSION['nomina'].' ',0,1,'C');
	}
	
	$this->Cell(0,5,'LAPSO DEL '.$fecha_ini.' AL '.$fecha_fin,0,1,'C');
	
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


function Cuentas($nomina_id,$codtip){
	$codt=$codtip;
	$conexion = conexion();
   	$query = "select  npos.nomposicion_id as posicion, cue.CodCue, cue.Denominacion as descue from nompersonal as per LEFT join nom_movimientos_nomina as nom on (per.ficha = nom.ficha)  join nomposicion npos on (npos.nomposicion_id=per.nomposicion_id) left join cwprecue cue on (cue.CodCue=npos.partida) where  nom.codnom = '$nomina_id' and nom.tipnom = '$codt' and per.tipnom='$codt'  group by cue.CodCue order by cue.CodCue";

    $result = query($query, $conexion);


    $query = "select * from nom_nominas_pago where codnom = '" . $nomina_id . "' AND codtip= '" . $codt . "' ";
    $result2 = query($query, $conexion);
    $fila2 = fetch_array($result2);
    $contador = 1;
    $num = 1;
    $posicion= "";
    $persona = 0;
    $totalpersonas = 0;
    $totalbd = num_rows($result);
    $pers = 0;
    $CANTIDAD = 52;
    while ($fila = fetch_array($result)) 
    {

        if ($posicion != $fila['CodCue']) {
            
            if ($posicion != "")
            {

                $this->Ln(250);
                $this->SetFont('Arial', '', 8);
                //$this->Cell(100, 5,$_SESSION['nomina'], 0, 1, 'L');
                //$this->Cell(100, 5, $fila['CodCue'].'  '.$fila['descue'], 0, 1, 'L');
            }
            else
            {
                $this->SetFont('Arial', '', 8);
                //$this->Cell(100, 5,$_SESSION['nomina'], 0, 1, 'L');
                //$this->Cell(100, 5, $fila['CodCue'].'  '.$fila['descue'], 0, 1, 'L');
            }
            $posicion=$fila['CueCue'];
        }



        //if ($gertmp != $fila['codnivel3']) {
            $persona+=1;



            $sficha = $fila['ficha'];

            
            $squincenal=0;
            $query = "select sum(monto) as monto from nom_movimientos_nomina nom join nompersonal per  on (per.ficha = nom.ficha)
        where  nom.tipnom='".$codt."' and nom.codnom = '" . $nomina_id . "' AND nom.codcon=100 and per.nomposicion_id='".$fila[posicion]."'";
            $result2 = query($query, $conexion);                
            $fetch2=fetch_array($result2);
            $squincenal=$fetch2[monto];


            $isr=0;
            $query = "select sum(monto) as monto from nom_movimientos_nomina nom join nompersonal per  on (per.ficha = nom.ficha)
        where nom.tipnom='".$codt."' and nom.codnom = '" . $nomina_id . "' AND nom.codcon=202 and per.nomposicion_id='".$fila[posicion]."'";
            $result3 = query($query, $conexion);                
            $fetch3=fetch_array($result3);
            $isr=$fetch3[monto];

            $ss=0;
            $query = "select sum(monto) as monto from nom_movimientos_nomina nom join nompersonal per  on (per.ficha = nom.ficha)
        where nom.tipnom='".$codt."' and nom.codnom = '" . $nomina_id . "' AND nom.codcon=200 and per.nomposicion_id='".$fila[posicion]."'";
            $result4 = query($query, $conexion);                
            $fetch4=fetch_array($result4);
            $ss=$fetch4[monto];

            $se=0;
            $query = "select sum(monto) as monto from nom_movimientos_nomina nom join nompersonal per  on (per.ficha = nom.ficha)
        where  nom.tipnom='".$codt."' and nom.codnom = '" . $nomina_id . "' AND nom.codcon=201 and per.nomposicion_id='".$fila[posicion]."'";
            $result5 = query($query, $conexion);
            $fetch5=fetch_array($result5);
            $se=$fetch5[monto];


            //FALTA COLOCAR EL CONCEPTO DE ESTE
            $fc=0;
            $query = "select sum(monto) as monto from nom_movimientos_nomina nom join nompersonal per  on (per.ficha = nom.ficha)
        where  nom.tipnom='".$codt."' and nom.codnom = '" . $nomina_id . "' AND nom.codcon=20 and per.nomposicion_id='".$fila[posicion]."'";
            $result6 = query($query, $conexion);
            $fetch6=fetch_array($result6);
            $fc=$fetch6[monto];


            $ot=0;
            $query = "select sum(monto) as monto from nom_movimientos_nomina nom join nompersonal per  on (per.ficha = nom.ficha)
        where  nom.tipnom='".$codt."' and nom.codnom = '" . $nomina_id . "' AND per.nomposicion_id='".$fila[posicion]."' AND tipcon='D' and codcon not in (202,200,201)";
            $result7 = query($query, $conexion);
            $fetch7=fetch_array($result7);
            $ot=$fetch7[monto];

            $asig=0;
            $query = "select sum(monto) as monto from nom_movimientos_nomina nom join nompersonal per  on (per.ficha = nom.ficha)
        where nom.tipnom='".$codt."' and nom.codnom = '" . $nomina_id . "' AND per.nomposicion_id='".$fila[posicion]."' AND tipcon='A' ";
            $result8 = query($query, $conexion);
            $fetch8=fetch_array($result8);
            $asig=$fetch8[monto];

            $pat=0;
            $query = "select sum(monto) as monto from nom_movimientos_nomina nom join nompersonal per  on (per.ficha = nom.ficha)
        where nom.tipnom='".$codt."' and nom.codnom = '" . $nomina_id . "' AND per.nomposicion_id='".$fila[posicion]."' AND tipcon='P' ";
            $result9 = query($query, $conexion);
            $fetch9=fetch_array($result9);
            $pat=$fetch9[monto];

            $total_deduccion = $isr+$ss+$se+$fc+$ot;
            $monto_cheque =  $squincenal-$total_deduccion;

            $this->SetFont('Arial', '', 8);
            $this->SetWidths(array(12, 20, 20, 20,20,20, 20,20,20, 20));
            $this->SetAligns(array('L', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C' ));
            $this->Setceldas(array('0', 1, 1, 1, 1, 1, 1, 1, 1, 1));
            $this->Setancho(array(5, 5, 5, 5, 5, 5, 5, 5, 5, 5));
            $this->Row(array('Partida', 'BRUTO SIN PATRONAL', 'BRUTO MAS PATRONAL', 'ISR                   ', 'SEG. SOCIAL           ', 'S. EDU.                ', 'FONDO C           ', 'OTRAS DEDUC', 'TOTAL DEDUC', 'MONTO NETO', ));

            $this->Cell(0,5,$fila[CodCue].' '.$fila[descue],0,1,'L');

            $this->SetWidths(array(12, 20, 20, 20,20,20, 20,20,20, 20));
            $this->SetAligns(array('L', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R' ));
            $this->Setceldas(array('0', '0', '0', '0', '0', '0', '0', '0', '0', '0'));
            $this->Setancho(array(5, 5, 5, 5, 5, 5, 5, 5, 5, 5));
            $this->Row(array('', $asig, $pat, $isr, $ss, $se, $fc, $ot, $total_deduccion, ($asig-$total_deduccion)));

			$consulta="SELECT DISTINCT(codcon) FROM nom_movimientos_nomina nom  WHERE codnom='".$nomina_id."' AND tipnom='".$codtip."' and tipcon='P' ";	
			$resultado_mov=query($consulta,$conexion);

			$cantidad_registros=20;
			$totalwhile=num_rows($resultado_mov);
			$contar=1;
			$conta=1;
			$totalA=0;
			$totalD=0;
			$totalP=0;
			while($totalwhile>=$contar)
			{
				
				$this->SetFont('Arial','',8);
				$fila=fetch_array($resultado_mov);
				$conexion=conexion();
				$consulta6 = "SELECT SUM(monto) as suma FROM nom_movimientos_nomina WHERE codnom='".$nomina_id."' AND tipnom='".$codtip."' AND codcon = '".$fila['codcon']."' ";
				$resultado6 = query($consulta6,$conexion);
				$fetch6 = fetch_array($resultado6);

				$consulta7 = "SELECT descrip,tipcon FROM nom_movimientos_nomina WHERE codnom='".$nomina_id."' AND tipnom='".$codtip."' AND codcon = '".$fila['codcon']."' ";	
				$resultado7 =query($consulta7,$conexion);
				$fetch7 = fetch_array($resultado7);
	
				$this->Setceldas(array(0,0));
				$this->SetAligns(array('L','R'));
				
				$this->Row(array($fila[codcon].' - '. utf8_decode($fetch7[descrip]),number_format($fetch6['suma'],2,',','.')));
				$totalP=$totalP+$fetch6['suma'];
			
				$conta++;
				$contar++;

				
			}
			$this->Ln(2);
			
			/*$this->SetFont('Arial','',12);
			$this->Cell(120,5,'TOTALES:  ','T',0,'R');
			$this->Cell(24,5,number_format($totalP,2,',','.'),'T',1,'R');
            $this->SetWidths(array(270));
            $this->SetAligns(array('C' ));
            $this->Setceldas(array('B'));
            $this->Setancho(array(2));
            $this->Row(array(''));*/
    }

    ///////////////////////////////////////////////


	$this->Ln(300);

	
	$conexion=conexion();
	$consulta="SELECT DISTINCT(codcon) FROM nom_movimientos_nomina WHERE codnom='".$nomina_id."' AND tipnom='".$codtip."' and tipcon='P'";	
	$resultado_mov=query($consulta,$conexion);

	// llamado para hacer multilinea sin que haga salto de linea
	
	$this->SetFont('Arial','',10);
	$this->SetWidths(array(80,40,24,24,24));
    /*$this->SetWidths(array(80,40,24,24,24));
	$this->SetAligns(array('C','C','C','C','C'));
    $this->Setceldas(array('TB','TB','TB','TB','TB'));
	$this->Setancho(array(5,5,5,5,5));
    $this->Row(array(utf8_decode('Código y Descripción del Concepto'),utf8_decode('Cod. Presup.'),'Asignaciones','Deducciones','Patronales'));
	*/
	// fin

	$cantidad_registros=20;
	$totalwhile=num_rows($resultado_mov);
	$contar=1;
	$conta=1;
	$totalA=0;
	$totalD=0;
	$totalP=0;
	while($fila=fetch_array($resultado_mov))
	{
		
		$this->SetFont('Arial','',8);
		
		$conexion=conexion();
		$consulta6 = "SELECT SUM(monto) as suma FROM nom_movimientos_nomina WHERE codnom='".$nomina_id."' AND tipnom='".$codtip."' AND codcon = '".$fila['codcon']."' ";
		$resultado6 = query($consulta6,$conexion);
		$fetch6 = fetch_array($resultado6);

		$consulta7 = "SELECT descrip,tipcon FROM nom_movimientos_nomina WHERE codnom='".$nomina_id."' AND tipnom='".$codtip."' AND codcon = '".$fila['codcon']."' ";	
		$resultado7 =query($consulta7,$conexion);
		$fetch7 = fetch_array($resultado7);

		$consulta8 = "SELECT ctacon1 FROM nomconceptos WHERE codcon = '".$fila['codcon']."' ";	
		$resultado8 = query($consulta8,$conexion);
		$fetch8 = fetch_array($resultado8);
		
		$this->Setceldas(array(0,0,0,0,0));
		$this->SetAligns(array('L','C','R','R','R'));
		if($fetch7['tipcon']=='A')
		{
			
			$this->Row(array($fila[codcon].' - '. utf8_decode($fetch7[descrip]),'',number_format($fetch6['suma'],2,',','.'),'',''));
			$totalA=$totalA+$fetch6['suma'];
		}
		elseif($fetch7['tipcon']=='D')
		{
			
			$this->Row(array($fila[codcon].' - '. utf8_decode($fetch7[descrip]),'',number_format($fetch6['suma'],2,',','.'),'',''));
			$totalD=$totalD+$fetch6['suma'];
		}
		elseif($fetch7['tipcon']=='P')
		{
			$this->Row(array($fila[codcon].' - '. utf8_decode($fetch7[descrip]),'',number_format($fetch6['suma'],2,',','.'),'',''));
			$totalP=$totalP+$fetch6['suma'];
		}
		
		$conta++;
		$contar++;

		
	}
	$this->Row(array('CONTRIBUCION PATRONAL','',number_format($totalP,2,',','.'),'',''));
	$this->Ln(2);
	
	$consulta="SELECT DISTINCT(codcon) FROM nom_movimientos_nomina WHERE codnom='".$nomina_id."' AND tipnom='".$codtip."' and tipcon in ('A','D') order by tipcon asc";
	$resultado_mov=query($consulta,$conexion);

	// llamado para hacer multilinea sin que haga salto de linea
	
	$this->SetFont('Arial','',10);
    /*$this->SetWidths(array(80,40,24,24,24));
	$this->SetAligns(array('C','C','C','C','C'));
    $this->Setceldas(array('TB','TB','TB','TB','TB'));
	$this->Setancho(array(5,5,5,5,5));
    $this->Row(array(utf8_decode('Código y Descripción del Concepto'),utf8_decode('Cod. Presup.'),'Asignaciones','Deducciones','Patronales'));
	*/
	// fin

	$cantidad_registros=20;
	$totalwhile=num_rows($resultado_mov);
	$contar=1;
	$conta=1;
	$totalA=0;
	$totalD=0;
	$totalP=0;
    $codcons= array(200,201,202,208);
	while($fila=fetch_array($resultado_mov))
	{
		
		$this->SetFont('Arial','',8);
		
		$conexion=conexion();
		$consulta6 = "SELECT SUM(monto) as suma FROM nom_movimientos_nomina WHERE codnom='".$nomina_id."' AND tipnom='".$codtip."' AND codcon = '".$fila['codcon']."' ";
		$resultado6 = query($consulta6,$conexion);
		$fetch6 = fetch_array($resultado6);

		$consulta7 = "SELECT descrip,tipcon FROM nom_movimientos_nomina WHERE codnom='".$nomina_id."' AND tipnom='".$codtip."' AND codcon = '".$fila['codcon']."' ";	
		$resultado7 =query($consulta7,$conexion);
		$fetch7 = fetch_array($resultado7);

		$consulta8 = "SELECT ctacon1 FROM nomconceptos WHERE codcon = '".$fila['codcon']."' ";	
		$resultado8 = query($consulta8,$conexion);
		$fetch8 = fetch_array($resultado8);
		
		$this->Setceldas(array(0,0,0,0,0));
		$this->SetAligns(array('L','C','R','R','R'));
		if($fetch7['tipcon']=='A')
		{
			
			$this->Row(array($fila[codcon].' - '. utf8_decode($fetch7[descrip]),'',number_format($fetch6['suma'],2,',','.'),'',''));
			$totalA=$totalA+$fetch6['suma'];
		}
		elseif($fetch7['tipcon']=='D')
		{
			if(in_array($fila['codcon'], $codcons))
			    $this->Row(array($fila[codcon].' - '. utf8_decode($fetch7[descrip]),'',number_format($fetch6['suma'],2,',','.'),'',''));
			$totalD=$totalD+$fetch6['suma'];
		}
		elseif($fetch7['tipcon']=='P')
		{
			$this->Row(array($fila[codcon].' - '. utf8_decode($fetch7[descrip]),'',number_format($fetch6['suma'],2,',','.'),'',''));
			$totalP=$totalP+$fetch6['suma'];
		}
		
		
		$conta++;
		$contar++;

		
	}
    $this->Row(array('TOTAL DEDUCCIONES','',number_format($totalD,2,',','.'),'',''));
	$this->Row(array('TOTAL MONTO NETO','',number_format(($totalA-$totalD),2,',','.'),'',''));
	//$this->Ln(5);

    $this->SetY(-90);
    $this->Cell(70,5,'1 PREPARADO POR:',0,0,'L');
    $this->Cell(70,5,'2 REVISADO POR:',0,0,'L');
    $this->Cell(70,5,'3 REGISTRADO POR:',0,1,'L');

    $this->Ln(15);
    $this->Cell(60,5,'','T',0,'C');
    $this->Cell(10,5,'',0,0,'C');
    $this->Cell(60,5,'','T',0,'C');
    $this->Cell(10,5,'',0,0,'C');
    $this->Cell(60,5,'','T',1,'C');

    $this->Cell(70,5,'JEFA DE PLANILLA',0,0,'L');
    $this->Cell(70,5,'DIRECCION DE RRHH',0,0,'L');
    $this->Cell(70,5,'DIRECTOR DE PLANIFICACION E. Y PRESUPUESTO',0,1,'L');
    $this->Ln(5);

    $this->Cell(70,5,'4 ORDENADO POR:',0,0,'L');
    $this->Cell(70,5,'5 AUTORIZADO POR:',0,0,'L');
    $this->Cell(70,5,'6 EXAMINADO POR:',0,1,'L');

    $this->Ln(15);
    $this->Cell(60,5,'','T',0,'C');
    $this->Cell(10,5,'',0,0,'C');
    $this->Cell(60,5,'','T',0,'C');
    $this->Cell(10,5,'',0,0,'C');
    $this->Cell(70,5,'','T',1,'C');

    $this->Cell(70,5,'TESORERO MUNICIPAL',0,0,'L');
    $this->Cell(70,5,'ALCALDE DEL DISTRITO',0,0,'L');
    $this->Cell(70,5,'AUDITOR CONTRALORIA',0,1,'L');
	
	$this->SetFont('Arial','',12);




}
function Footer(){
	/*$this->SetY(-45);
	$this->SetFont('Arial','',10);
	$conexion=conexion();
	$query="select * from nomempresa";		
	$result=query($query,$conexion);	
	$row = fetch_array ($result);	
	$gerente=$row['ger_rrhh'];
	$this->Cell(70,5,'');
	$this->Cell(48,5,'','T',0);
	$this->Cell(70,5,'',0,1);
	$this->Cell(70,5,'');
	$this->Cell(48,5,'COORDINADOR DE RRHH',0,0,'C');
	$this->Cell(70,5,'',0,1);
	$this->Cell(70,5,'');
	$this->Cell(48,5,$gerente,0,0,'C');
	$this->Cell(70,5,'',0,1);*/
	$this->SetY(-15);
	$this->Cell(0,10,utf8_decode('Página ').$this->PageNo().'/{nb}',0,0,'C');
}


}


//Creación del objeto de la clase heredada
$pdf=new PDF();
$pdf->AliasNbPages();
$pdf->AddPage('L','Letter');
$pdf->AddFont('Sanserif','','sanserif.php');
$pdf->SetFont('Sanserif','',12);

$pdf->nominapdf=$nomina_id;

$pdf->Cuentas($nomina_id,$codtip);

$pdf->Output();
?>
