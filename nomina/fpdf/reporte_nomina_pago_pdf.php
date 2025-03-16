<?php 
session_start();
ob_start();
$termino=$_SESSION['termino'];
$tipo=$_GET['tipo'];
$nomina_id=$_GET['nomina_id'];
$codt = $_SESSION['codigo_nomina'];


require('fpdf.php');
include("../lib/common.php");
include("../lib/pdf.php");



function fecha($value) { // fecha de YYYY/MM/DD a DD/MM/YYYY
 if ( ! empty($value) ) return substr($value,8,2) ."/". substr($value,5,2) ."/". substr($value,0,4);
}

class PDF extends FPDF
{

function header(){
	$Conn=conexion();
	$var_sql="select * from nomempresa";
	$rs = query($var_sql,$Conn);
	$row_rs = fetch_array($rs);
	$var_encabezado=$row_rs['nom_emp'];
		
	$query="select * from nom_nominas_pago where codnom = '".$_GET['nomina_id']."' AND codtip= '".$_SESSION['codigo_nomina']."' ";		
	$result2=query($query,$Conn);	
	$fila2 = fetch_array($result2);

        $this->SetFont("Arial","",8);
     	
        $date1=date('d/m/Y');
	$date2=date('h:i a');

        $this->Cell(150,4,utf8_decode('Republica Bolivariana de Venezuela'),0,0,'L');
        $this->Cell(38,4,'Fecha: '.$date1,0,1,'R');
        $this->Cell(150,4,utf8_decode($var_encabezado),0,0,'L');
        $this->Cell(38,4,'Hora: '.$date2,0,1,'R');
        $this->Cell(150,4,utf8_decode('Gerencia Ejecutiva / Coordinacion de Recursos Humanos'),0,0,'L');
        $this->Cell(38,4,'',0,1,'R');
        $this->Cell(150,4,utf8_decode('Reporte de Nomina '.$_SESSION['nomina']),0,0,'C');
        $this->Cell(38,4,'Quincena: '.fecha($fila2['periodo_fin']),0,1,'R');
        $this->Ln(4);

        $this->SetFont('Arial','',10);
        $this->Cell(102,4,'Concepto',1,0,'C');
        $this->Cell(20,4,'Ref',1,0,'C');
        $this->Cell(33,4,'Asignaciones',1,0,'C');
        $this->Cell(33,4,'Deducciones',1,1,'C');
        $this->ln(4);
     	
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
    $h=3*$nb;
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

function personas($nomina_id,$codt,$pdf){

	$conexion=conexion();

	$query = "select * from nomnivel1 inner join nompersonal on codorg=codnivel1 group by codnivel1 order by codorg";
	$result=query($query,$conexion);
	$contador=1;
	$num=1;
	$gerencia="";
	$persona=$persona2=0;
	$totalpersonascoord=0;
	$totalpersonasgerencia=0;
	$pers=$pers2=0;
	//$CANTIDAD=72;
        while ($fila = fetch_array($result))
	{
            $query="select per.*,car.* from nompersonal as per LEFT join nom_movimientos_nomina as nom on (per.ficha = nom.ficha)  left join nomcargos as car on per.codcargo = car.cod_car where  nom.codnom = '$nomina_id' and nom.tipnom = ".$codt." and per.tipnom=".$codt." and per.codnivel1='".$fila['codorg']."' group by per.ficha order by per.codnivel1,per.cedula";
            $result4=query($query,$conexion);
            $totalbdgerencia=num_rows($result4);
            if($totalbdgerencia>0)
            {
                $this->SetFont("Arial","",8);
                $this->Cell(100,4,"GERENCIA: ".$fila['codorg']."  ".utf8_decode($fila['descrip']));
                $this->Ln(4);
            }
            //echo "<br>";
            $consulta_n4="SELECT * FROM nomnivel2 WHERE gerencia='".$fila['codorg']."' order by codorg";
            $resultado_n4=query($consulta_n4,$conexion);

                while($fila2 = fetch_array($resultado_n4))
                {

                    $query="select per.*,car.* from nompersonal as per LEFT join nom_movimientos_nomina as nom on (per.ficha = nom.ficha)  left join nomcargos as car on per.codcargo = car.cod_car where  nom.codnom = '$nomina_id' and nom.tipnom = ".$codt." and per.tipnom=".$codt." and per.codnivel1='".$fila['codorg']."' and per.codnivel2='".$fila2['codorg']."' group by per.ficha order by per.codnivel1,per.codnivel2,per.cedula";
                    $result3=query($query,$conexion);
                    $totalbdcoord=num_rows($result3);

                    if(($fila2['codorg']<>0)&&($totalbdcoord>0))
                    {
                        $this->Cell(100,5,"COORDINACION: ".$fila2['codorg']."  ".utf8_decode($fila2['descrip']));
                        $this->Ln();
                    }
                    while ($fila3 = fetch_array($result3))
                    {
                        $sficha = $fila3['ficha'];
                        $query="select * from nom_movimientos_nomina as mn
                            inner join nompersonal as pe on mn.ficha = pe.ficha
                            inner join nomconceptos as c on c.codcon = mn.codcon
                            where pe.ficha = '".$sficha."' and pe.tipnom='".$codt."' and mn.codnom = '".$nomina_id."' and mn.tipnom = '".$codt."' and mn.tipcon<>'P' order by pe.cedula,mn.tipcon,c.codcon";
                        $result1=query($query,$conexion);

                        $this->SetFont('Arial','',9);
                        //$this->SetWidths(array(98,50,40));
                        //$this->SetAligns(array('L','L','L'));
                        //$this->Setceldas(array('LT','T','TR'));
                        //$this->Setancho(array(5,5,5));
                        //$this->Row(array('Nombre: '.utf8_decode($fila3['apellidos']).", ".utf8_decode($fila3['nombres']),utf8_decode('Cédula: '.number_format($fila3['cedula'],0,',','.')),' Sueldo Basico: '.number_format($fila3['suesal'],2,',','.')));
                        $this->Cell(98,5,'Nombre: '.utf8_decode($fila3['apellidos']).", ".utf8_decode($fila3['nombres']),'LT',0,'L');
                        $this->Cell(50,5,utf8_decode('Cédula: '.number_format($fila3['cedula'],0,',','.')),'T',0,'L');
                        $this->Cell(40,5,' Sueldo Basico: '.number_format($fila3['suesal'],2,',','.'),'RT',0,'L');
                        $this->ln(4);
                        //$this->SetWidths(array(95,58,35));
                        //$this->SetAligns(array('L','L','L'));
                        //$this->Setceldas(array('LB','B','BR'));
                        //$this->Row(array(utf8_decode('Cargo: '.$fila3['des_car']),"Nro Cuenta: ".$fila3[cuentacob], "Fec Ing: ".fecha($fila3['fecing'])));
                        $this->Cell(95,5,utf8_decode('Cargo: '.$fila3['des_car']),'BL',0,'L');
                        $this->Cell(58,5,"Nro Cuenta: ".$fila3[cuentacob],'B',0,'L');
                        $this->Cell(35,5,"Fec Ing: ".fecha($fila3['fecing']),'RB',0,'L');
                        $this->Ln();

                        $pers+=1;
                        $pers2+=1;
                        $persona+=1;
                        $sub_total_asig=0;
                        $sub_total_dedu=0;
                        $sub_total_pat=0;

                        while ($row = fetch_array($result1))
                        {
                            $contador++;
                            if ($row['tipcon']=='A')
                            {
                                $valor1= number_format($row['monto'],2,',','.');
                                $valor2="";
                                $sub_total_asig=$row['monto']+$sub_total_asig;
                                $total_asig=$row['monto']+$total_asig;
                                $total_asig_gerencia=$row['monto']+$total_asig_gerencia;
                                $total_asig_coord=$row['monto']+$total_asig_coord;
                            }
                            if ($row['tipcon']=='D')
                            {
                                $valor2= number_format($row['monto'],2,',','.');
                                $valor1="";
                                $sub_total_dedu=$row['monto']+$sub_total_dedu;
                                $total_dedu=$row['monto']+$total_dedu;
                                $total_deduc_gerencia=$row['monto']+$total_deduc_gerencia;
                                $total_deduc_coord=$row['monto']+$total_deduc_coord;
                            }
                            // llamado para hacer multilinea sin que haga salto de linea
                            $this->SetFont('Arial','I',9);
                            //$this->SetWidths(array(10,92,20,33,33));
                            //$this->SetAligns(array('R','L','C','R','R'));
                            //$this->Setceldas(array(0,0,0,0.0));
                            //$this->Setancho(array(5,5,5,5,5));
                            //$this->Row(array($row[codcon],$row[descrip],$row[valor],$valor1,$valor2));
                            $this->Cell(10,5,$row[codcon],0,0,'L');
                            $this->Cell(92,5,$row[descrip],0,0,'L');
                            $this->Cell(20,5,$row[valor],0,0,'R');
                            $this->Cell(33,5,$valor1,0,0,'R');
                            $this->Cell(33,5,$valor2,0,0,'R');
                            $this->Ln(3);
                        }
                        $this->Ln(1);
                        $this->SetFont("Arial","I",9);
                        $this->Cell(122,5,'TOTAL: ',0,0,'R');
                        $this->Cell(33,5,number_format($sub_total_asig,2,',','.'),'T',0,'R');
                        $this->Cell(33,5,number_format($sub_total_dedu,2,',','.'),'T',0,'R');
                        $this->Ln(4);
                        $this->SetFont("Arial","I",10);
                        $this->Cell(122,5,'NETO: ',0,0,'R');
                        $this->Cell(33,5,number_format($sub_total_asig-$sub_total_dedu,2,',','.'),0,0,'R');
                        $this->Cell(33,5,'',0,0,'R');
                        $this->Ln(8);
                        $this->SetFont("Arial","I",8);
                        $totalpersonas=$totalpersonas+1;
                        $totalpersonascoord=$totalpersonascoord+1;
                        $totalpersonasgerencia=$totalpersonasgerencia+1;
                        
                        if($totalpersonascoord==$totalbdcoord)
                        {
                            $this->SetFont('Arial','I',8);
                            $this->Ln(3);
                            $this->MultiCell(188,5,'Total Coord -->     Total Personas: '.$totalpersonascoord.' Total Asignaciones: '.number_format($total_asig_coord,2,',','.').'     Total Deducciones: '.number_format($total_deduc_coord,2,',','.'). '     Total : '.number_format(($total_asig_coord-$total_deduc_coord),2,',','.'),0,'R');
                            $total_asig_coord=$total_deduc_coord=0;
                            $totalpersonascoord=0;
                        }
                    }
                }
                $query="select per.*,car.* from nompersonal as per LEFT join nom_movimientos_nomina as nom on (per.ficha = nom.ficha)  left join nomcargos as car on per.codcargo = car.cod_car where  nom.codnom = '$nomina_id' and nom.tipnom = ".$codt." and per.tipnom=".$codt." and per.codnivel1='".$fila['codorg']."' and per.codnivel2=0 group by per.ficha order by per.codnivel1,per.codnivel2,per.cedula";
                $result3=query($query,$conexion);
                $totalbdcoord=num_rows($result3);

                while ($fila3 = fetch_array($result3))
                {
                    $sficha = $fila3['ficha'];
                    $query="select * from nom_movimientos_nomina as mn
                	inner join nompersonal as pe on mn.ficha = pe.ficha
                        inner join nomconceptos as c on c.codcon = mn.codcon
                        where pe.ficha = '".$sficha."' and pe.tipnom='".$codt."' and mn.codnom = '".$nomina_id."' and mn.tipnom = '".$codt."' and mn.tipcon<>'P' order by pe.cedula,mn.tipcon,c.codcon";
                    $result1=query($query,$conexion);

                    $this->SetFont('Arial','',9);
                    //$this->SetWidths(array(98,50,40));
                    //$this->SetAligns(array('L','L','L'));
                    //$this->Setceldas(array('LT','T','TR'));
                    //$this->Setancho(array(5,5,5));
                    //$this->Row(array('Nombre: '.utf8_decode($fila3['apellidos']).", ".utf8_decode($fila3['nombres']),utf8_decode('Cédula: '.number_format($fila3['cedula'],0,',','.')),' Sueldo Basico: '.number_format($fila3['suesal'],2,',','.')));
                    $this->Cell(98,5,'Nombre: '.utf8_decode($fila3['apellidos']).", ".utf8_decode($fila3['nombres']),'LT',0,'L');
                    $this->Cell(50,5,utf8_decode('Cédula: '.number_format($fila3['cedula'],0,',','.')),'T',0,'L');
                    $this->Cell(40,5,' Sueldo Basico: '.number_format($fila3['suesal'],2,',','.'),'RT',0,'L');
                    $this->ln(4);
                    //$this->SetWidths(array(95,58,35));
                    //$this->SetAligns(array('L','L','L'));
                    //$this->Setceldas(array('LB','B','BR'));
                    //$this->Row(array(utf8_decode('Cargo: '.$fila3['des_car']),"Nro Cuenta: ".$fila3[cuentacob], "Fec Ing: ".fecha($fila3['fecing'])));
                    $this->Cell(95,5,utf8_decode('Cargo: '.$fila3['des_car']),'BL',0,'L');
                    $this->Cell(58,5,"Nro Cuenta: ".$fila3[cuentacob],'B',0,'L');
                    $this->Cell(35,5,"Fec Ing: ".fecha($fila3['fecing']),'RB',0,'L');
                    $this->Ln();

                    $pers+=1;
                    $pers2+=1;
                    $persona+=1;
                    $sub_total_asig=0;
                    $sub_total_dedu=0;
                    $sub_total_pat=0;

                    while ($row = fetch_array($result1))
                    {
			$contador++;
			if ($row['tipcon']=='A')
			{
                            $valor1= number_format($row['monto'],2,',','.');
                            $valor2="";
                            $sub_total_asig=$row['monto']+$sub_total_asig;
                            $total_asig=$row['monto']+$total_asig;
                            $total_asig_gerencia=$row['monto']+$total_asig_gerencia;
                            $total_asig_coord=$row['monto']+$total_asig_coord;
			}
			if ($row['tipcon']=='D')
			{
                            $valor2= number_format($row['monto'],2,',','.');
                            $valor1="";
                            $sub_total_dedu=$row['monto']+$sub_total_dedu;
                            $total_dedu=$row['monto']+$total_dedu;
                            $total_deduc_gerencia=$row['monto']+$total_deduc_gerencia;
                            $total_deduc_coord=$row['monto']+$total_deduc_coord;
			}
                        // llamado para hacer multilinea sin que haga salto de linea
                        $this->SetFont('Arial','I',9);
			//$this->SetWidths(array(10,92,20,33,33));
                            //$this->SetAligns(array('R','L','C','R','R'));
                            //$this->Setceldas(array(0,0,0,0.0));
                            //$this->Setancho(array(5,5,5,5,5));
                            //$this->Row(array($row[codcon],$row[descrip],$row[valor],$valor1,$valor2));
                            $this->Cell(10,5,$row[codcon],0,0,'L');
                            $this->Cell(92,5,$row[descrip],0,0,'L');
                            $this->Cell(20,5,$row[valor],0,0,'R');
                            $this->Cell(33,5,$valor1,0,0,'R');
                            $this->Cell(33,5,$valor2,0,0,'R');
                            $this->Ln(3);
                    }
                    $this->Ln(1);
                    $this->SetFont("Arial","I",9);
                    $this->Cell(122,5,'TOTAL: ',0,0,'R');
                    $this->Cell(33,5,number_format($sub_total_asig,2,',','.'),'T',0,'R');
                    $this->Cell(33,5,number_format($sub_total_dedu,2,',','.'),'T',0,'R');
                    $this->Ln(4);
                    $this->SetFont("Arial","I",10);
                    $this->Cell(122,5,'NETO: ',0,0,'R');
                    $this->Cell(33,5,number_format($sub_total_asig-$sub_total_dedu,2,',','.'),0,0,'R');
                    $this->Cell(33,5,'',0,0,'R');
                    $this->Ln(8);
                    $this->SetFont("Arial","I",8);
                    $totalpersonascoord=$totalpersonascoord+1;
                    $totalpersonasgerencia=$totalpersonasgerencia+1;

                    if (($totalpersonascoord==$totalbdcoord)){
                        $this->SetFont('Arial','I',8);
			$this->Ln(3);
			$this->MultiCell(188,5,'Total Coord -->     Total Personas: '.$totalpersonascoord.' Total Asignaciones: '.number_format($total_asig_coord,2,',','.').'     Total Deducciones: '.number_format($total_deduc_coord,2,',','.'). '     Total : '.number_format(($total_asig_coord-$total_deduc_coord),2,',','.'),0,'R');
			$this->Ln(3);
                        $totalpersonascoord=0;
                        $persona=0;
                        $total_asig_coord=$total_deduc_coord=0;
                    }
                }
                //echo "TPG: ".$totalpersonasgerencia."  TBDG: ".$totalbdgerencia;
                //echo "<br>";

                if (($totalpersonasgerencia==$totalbdgerencia)&&($totalbdgerencia>0)){
                    $this->SetFont('Arial','I',8);
                    $this->Ln(3);
                    $this->MultiCell(188,5,'Total Gerencia -->     Total Personas: '.$totalpersonasgerencia.' Total Asignaciones: '.number_format($total_asig_gerencia,2,',','.').'     Total Deducciones: '.number_format($total_deduc_gerencia,2,',','.'). '     Total : '.number_format(($total_asig_gerencia-$total_deduc_gerencia),2,',','.'),0,'R');
                    $this->Ln(3);
                    $totalpersonasgerencia=0;
                    $persona=0;
                    $total_asig_gerencia=$total_deduc_gerencia=0;
                }
        }
}
                

function Vacaciones(){
	
	$conexion=conexion();
	$consulta_vac="SELECT ficha, apenom FROM nompersonal WHERE tipnom =".$_SESSION['codigo_nomina']." and estado='Vacaciones' ORDER BY ficha";
	$resultado_vac=query($consulta_vac,$conexion);

	$this->Ln(20);
	$this->SetFont('Arial','I',12);
	if(num_rows($resultado_vac)!=0){
		$this->Ln(300);
		$this->Cell(188,8,'PERSONAL DE VACACIONES',0,0,'C');
		$this->Ln(10);
		$this->SetFont('Arial','',10);
		$cantidad_registros=40;
		$totalwhile=num_rows($resultado_vac);
		$contar=1;
		while($totalwhile>=$contar)
		{
			$fetchvac=fetch_array($resultado_vac);
			$this->Cell(60,5,$fetchvac['ficha'],0,0,'R');
			$this->Cell(100,5,'   '.$fetchvac['apenom'],0,0,'L');
			$this->Ln();
			if($contar==$cantidad_registros){
				$this->Ln(300);
				$this->SetFont('Arial','I',12);
				$this->Cell(188,8,'PERSONAL DE VACACIONES',0,0,'C');
				$this->SetFont('Arial','I',10);
				$this->Ln(10);
				
			}
			$contar++;
	
			
		}
	}
}
function Footer(){
	$this->SetY(-15);
	$this->Cell(0,10,utf8_decode('Página ').$this->PageNo().'/{nb}',0,0,'C');
}

function firmas($total_asig,$total_dedu,$cantidad){

	$conexion=conexion();
	$consulta_vac="SELECT ficha, apenom FROM nompersonal WHERE tipnom =".$_SESSION['codigo_nomina']." and estado='Vacaciones' ORDER BY ficha";

	
	
	$resultado_vac=query($consulta_vac,$conexion);
	
	$consultaa="SELECT ficha, apenom FROM nompersonal WHERE tipnom =".$_SESSION['codigo_nomina']." and estado<>'Egresado'";
	$resultadooo=query($consultaa,$conexion);
	$cant_personal+=num_rows($resultadooo);
	
	$query="select * from nom_nominas_pago where codnom = '".$_GET['nomina_id']."' AND codtip= '".$_SESSION['codigo_nomina']."' ";		
	$result2=query($query,$conexion);	
	$fila2 = fetch_array($result2);
	
	$this->SetFont('Arial','',10);
	$queda=$cantidad-10;
	if($queda<0){
		$this->Ln(300);
		$this->Cell(188,8,'Desde: '.fecha($fila2['periodo_ini']).' Hasta: '.fecha($fila2['periodo_fin']).' Pago: '.fecha($fila2['fechapago']),0,0,'C');
		$this->Ln();
	}
	
    
	$this->Cell(50,5,'Cant. de Personas: '.$cant_personal,0,1,'L');
	
	$this->Cell(188,5,'Total Generales: '.number_format($total_asig,2,',','.').'       '.number_format($total_dedu,2,',','.'),0,1,'R');

 	$this->Cell(188,5,'Neto: '.number_format($total_asig-$total_dedu,2,',','.'),0,1,'R');
        $this->Ln(10);
}
function finalizar($pdf){

        $this->SetY(-45);
	$bool=validar_firma("ANALISIS_CONCEPTO");
	if ($bool==true){
		firma_dinamica("ANALISIS_CONCEPTO",$pdf,6,5);
	}
    }

}


//Creación del objeto de la clase heredada
$pdf=new PDF();
$pdf->AliasNbPages();
$pdf->AddPage('P','A4');
$pdf->AddFont('Sanserif','','sanserif.php');
$pdf->SetFont('Sanserif','',10);

$pdf->personas($nomina_id,$codt,$pdf);
$pdf->finalizar($pdf);
//$pdf->Vacaciones();

$pdf->Output();
?>
