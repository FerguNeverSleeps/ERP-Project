<?php 
session_start();
ob_start();
$termino=$_SESSION['termino'];
$nomina=$_GET['nomina'];



require('fpdf.php');
include("../lib/common.php");
include("../lib/monto_escrito.php");
include("pdf.php");
include("../paginas/funciones_nomina.php");


function fecha($value) { // fecha de YYYY/MM/DD a DD/MM/YYYY
 if ( ! empty($value) ) return substr($value,8,2) ."/". substr($value,5,2) ."/". substr($value,0,4);
}

class PDF extends FPDF
{
var $nominapdf;
var $fpdf;
function header(){

$conexion=conexion();
    	$var_sql="select * from nomempresa";
    	$rs = query($var_sql,$conexion);
    	$row_rs = fetch_array($rs);
    	$var_encabezado=$row_rs['nom_emp'];
            $var_rif=$row_rs['rif'];
    	$var_izquierda='../imagenes/'.$row_rs[imagen_izq];
    	$var_derecha='../imagenes/'.$row_rs[imagen_der];
    	//$this->Image($var_derecha,10,6,30,15);
    	
    	$this->SetFont('Arial','',12);
    	$date1=date('d/m/Y');
    	$date2=date('h:i a');	
            //$this->Ln(10);
           
            $this->Cell(70,4,utf8_decode($var_encabezado),0,0,'L');
    //	$this->Cell(70,5,utf8_decode($var_encabezado),0,0,'L');
    //	$this->Cell(50,4,'RECIBO DE PAGO'.$ANIO,0,0,'C');
    	//$this->Cell(70,4,'Fecha:  '.$date1,0,1,'R');
    //	$this->Cell(50,5,'Gobierno de Carabobo',0,0,'L');  
         // $this->Cell(70,4,utf8_decode($var_rif),0,0,'L');
            $this->Cell(150,4,'',0,0,'L');
            $this->Ln(3);
    //	
     	$this->SetFont("Arial","B",10);
     	
     	$this->Ln(2);
      	$this->Cell(45);
     	$this->Cell(100,6,utf8_decode("PAGO DE PRESTACIONES LABORABLES "),0,1,"C");
     	$this->Cell(45);
     	$this->Cell(100,6,utf8_decode("DE LIQUIDACIÓN"),0,1,"C");
     	$this->Ln(3);
}

//Hacer que sea multilinea sin que haga un salto de linea
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
//fin

function datos_empleado($ficha,$cod_nomina,$codnom){
	$conexion=conexion();
	$consulta="select * from nompersonal where ficha='".trim($ficha)."' AND tipnom = '".$cod_nomina."'";
	$query=query($consulta,$conexion);
	$rc=fetch_array($query);

    if ($rc['fecharetiro']=="0000-00-00" ) {
        $fecha_liquidacion=$rc['fecharetiro_tmp']; 
    } else {
        $fecha_liquidacion=$rc['fecharetiro']; 
    }

	$consulta="select * from nom_dato_liquidacion where ficha='".trim($ficha)."' AND tiponom = '".$cod_nomina."' and cedula='".$rc[cedula]."' AND codnom = '".$codnom."'";
	$query=query($consulta,$conexion);
	$fetch2=fetch_array($query);
	list($dia,$mes,$ano)=explode('-',$fetch2['tiempo_servicio']);
	
	$consulta="select descripcion from nom_motivos_retiros where codigo='$rc[motivo_liq_tmp]' ";
	$query=query($consulta,$conexion);
	$fetch33=fetch_array($query);
	
	$cod_cargo=$rc['codcargo'];
	$consuta2="select * from nomcargos where cod_car='$cod_cargo'";
	$query2=query($consuta2,$conexion);
	$rcc=fetch_array($query2);

	$this->SetFont('Arial','B',8);
	$this->Cell(28,5,utf8_decode('LUGAR'),'LTR',0);
	$this->Cell(105,5,'APELLIDO Y NOMBRES ','LTR',0);
	$this->Cell(65,5,utf8_decode('CÉDULA DE IDENTIDAD'),'LTR',0);
	//$this->Cell(69,5,utf8_decode('CARGO '),'LTR',0);
	
	$this->Ln();
	$this->SetFont('Arial','I',8);
	// llamado para hacer multilinea sin que haga salto de linea
	$this->SetWidths(array(28,105,65));
	$this->SetAligns(array('C','C','C'));
	$this->Setceldas(array('LRB','LRB','LRB'));
	$this->Setancho(array(5,5,5));
	//,utf8_decode($rcc['des_car']
	$this->Row(array("PANAMA",utf8_decode($rc['apenom']),$rc['cedula']));
	//fin

	$this->SetFont('Arial','B',8);
	$this->Cell(133,5,utf8_decode('DENOMINACIÓN DEL CARGO'),'LTR',0,'C');
	$this->Cell(32,5,'FECHA DE INGRESO',0);
	$this->Cell(33,5,utf8_decode('FECHA DE EGRESO'),'LTR',0);
	$this->Ln();
	$this->SetFont('Arial','I',8);
	// llamado para hacer multilinea sin que haga salto de linea
	$this->SetWidths(array(133,32,33));
	$this->SetAligns(array('C','C','C'));
	$this->Setceldas(array('LRB','LRB','LRB'));
	$this->Setancho(array(5,5,5));
	$this->Row(array(utf8_decode($rcc['des_car']),fecha($rc['fecing']),$fecha_liquidacion));
	// 	$this->Ln();

	$fecha1 = new DateTime($rc['fecing']);//fecha inicial
	$fecha2 = new DateTime($fecha_liquidacion);//fecha de cierre

	$intervalo = $fecha1->diff($fecha2);
	$anio = $intervalo->format('%Y');
	$mes = $intervalo->format('%m');
	$dia = $intervalo->format('%d');
	$this->SetFont('Arial','B',9);
	$this->Cell(162,5,utf8_decode('MOTIVO DEL EGRESO'),1,0,'C');
	$this->Cell(36,5,utf8_decode('TIEMPO DE SERVICIO'),1,1,'C');
	$this->Cell(162,4,utf8_decode(''),'LTR',0,'C');
	$this->SetFont('Arial','',7);
	$this->Cell(11,4,utf8_decode('AÑOS'),1,0,'C');
	$this->Cell(13,4,utf8_decode('MESES'),1,0,'C');
	$this->Cell(12,4,utf8_decode('DÍAS'),1,1,'C');
	$this->SetFont('Arial','B',9);
	$this->Cell(162,5,utf8_decode($fetch33[descripcion]),'LBR',0,'C');
	$this->Cell(11,5,utf8_decode($anio),1,0,'C');
	$this->Cell(13,5,utf8_decode($mes),1,0,'C');
	$this->Cell(12,5,utf8_decode($dia),1,1,'C');

	$this->Ln(2);


}
function otros_pagos_query($ficha,$cod_nomina,$tipo_nomina,$num){
	$conexion=conexion();
	if($num==1){
		$consulta="select * from nom_movimientos_nomina where codnom='$cod_nomina' and ((codcon>=4002 and codcon<=4006) or codcon=4009 or codcon=4010) and ficha='$ficha' and tipnom='$tipo_nomina'";
	}
	if($num==2){
		$consulta="select * from nom_movimientos_nomina where codnom='$cod_nomina' and (codcon>=4019 and codcon<=4027)  and ficha='$ficha' and tipnom='$tipo_nomina'";
	}
	$query=query($consulta,$conexion);
	return $query;
}
function imprimir_otros_pagos($ficha,$cod_nomina,$tipo_nomina,$pdf){
	$ro=$pdf->otros_pagos_query($ficha,$cod_nomina,$tipo_nomina,1);
	$total=num_rows($ro);
	$cont=0;

	
	$this->SetFont('Arial','B',8);
	$this->Cell(198,5,'OTROS PAGOS (A) ',1,1,'C');
	
	while($total!=$cont){
		$resul=fetch_array($ro);
		$des1=$resul['descrip'];
		$mon1=$resul['monto'];
		$cont+=1;
		if(($resul=fetch_array($ro))!=null){
			
			$des2=$resul['descrip'];
			$mon2=number_format($resul['monto'],2,',','.');
			$cont+=1;
		}
		else{
			$des2='';
			$mon2=' ';
		}	
		$this->SetFont('Arial','I',8);
		// llamado para hacer multilinea sin que haga salto de linea
		$this->SetWidths(array(70,30,70,28));
		$this->SetAligns(array('L','R','L','R'));
		$this->Setceldas(array(0,0,0,0));
		$this->Setancho(array(5,5,5,5));
		$this->Row(array(utf8_decode($des1),number_format($mon1,2,',','.'),utf8_decode($des2),$mon2));
		//fin
		
	}
	
}
function deducciones($total,$pdf){
	$this->SetFont('Arial','B',8);
	$this->Cell(198,5,'OTROS DEDUCCIONES ',1,1,'C');
	$this->SetFont('Arial','I',8);
	// llamado para hacer multilinea sin que haga salto de linea
        $this->SetWidths(array(108,30,30,30));
	$this->SetAligns(array('L','C','R','R'));
        $this->Setceldas(array(0,0,0,0));
	$this->Setancho(array(5,5,5,5));
	$resul=$pdf->buscar(2501);
	//Preaviso
	if($resul!=null){
		$this->Row(array(utf8_decode($resul['descrip']),number_format($resul['valor'],0,'.','.').utf8_decode(" Días X "),number_format(($total/30),2,',','.').'  Bs. = ',number_format($resul['monto'],2,',','.')));
		$totalD+=$resul['monto'];
	}	

	//Tarjeta Alimentaria
	
	$resul=$pdf->buscar(2503);
	$total=0;
	if($resul!=null){
        	$this->Row(array(utf8_decode($resul['descrip']),number_format($resul['valor'],0,'.','.').utf8_decode(" Días  "),'Bs. = ',number_format($resul['monto'],2,',','.')));
		$totalD+=$resul['monto'];
	}

	//Sueldo y Prepagado mas
	$resul2=$pdf->buscar(4001);
	$mon=$resul2['monto'];
	$resul2=$pdf->buscar(4002);
	$mon+=$resul2['monto'];
	$resul=$pdf->buscar(2504);
	if($resul!=null){
        	$this->Row(array(utf8_decode($resul['descrip']),number_format($resul['valor'],0,'.','.').utf8_decode(" Días X "),number_format(($mon/30),2,',','.').'  Bs. = ',number_format($resul['monto'],2,',','.')));
		$totalD+=$resul['monto'];
	}	

	//prima profesional pagado de mas
	$resul2=$pdf->buscar(4004);
	$resul=$pdf->buscar(2505);
	if($resul!=null){
        	$this->Row(array(utf8_decode($resul['descrip']),number_format($resul['valor'],0,'.','.').utf8_decode(" Días X "),number_format(($resul2['monto']/30),2,',','.').'  Bs. = ',number_format($resul['monto'],2,',','.')));
		$totalD+=$resul['monto'];
	}


	//otras primas
	$resul2=$pdf->buscar(4002);
	$resul=$pdf->buscar(2506);
	if($resul!=null){
        	$this->Row(array(utf8_decode($resul['descrip']),number_format($resul['valor'],0,'.','.').utf8_decode(" Días  "),'Bs. = ',number_format($resul['monto'],2,',','.')));
		$totalD+=$resul['monto'];
	}
	
	//total
	$this->SetFont('Arial','B',8);
	$this->Setceldas(array(0,0,0,'T'));
	$this->Row(array(utf8_decode("SUB-TOTAL DEDUCCIONES"),' ','',number_format($totalD,2,',','.')));
	return $total;

	
}

function asignaciones($total,$pdf){
	$this->SetFont('Arial','B',8);
	$this->Cell(198,5,'OTRAS ASIGNACIONES ',1,1,'C');
	$this->SetFont('Arial','I',8);
	// llamado para hacer multilinea sin que haga salto de linea
        $this->SetWidths(array(108,30,30,30));
	$this->SetAligns(array('L','C','R','R'));
        $this->Setceldas(array(0,0,0,0));
	$this->Setancho(array(5,5,5,5));
	$total=0;
	//sueldo
	$resul2=$pdf->buscar(4001);
	$resul=$pdf->buscar(4019);
	$total=0;
	if($resul!=null){
        	$this->Row(array(utf8_decode($resul['descrip']),number_format($resul['valor'],0,'.','.').utf8_decode(" Días X "),number_format(($resul2['monto']/30),2,',','.').'  Bs. = ',number_format($DEDUCCIONESresul['monto'],2,',','.')));
		$total+=$resul['monto'];
	}
	//aporte
	$resul2=$pdf->buscar(4008);
	$resul=$pdf->buscar(4020);
	if($resul!=null){
        	$this->Row(array(utf8_decode($resul['descrip']),number_format($resul['valor'],0,'.','.').utf8_decode(" Días X "),number_format(($resul2['monto']/30),2,',','.').'  Bs. = ',number_format($resul['monto'],2,',','.')));
		$total+=$resul['monto'];
	}	
	//compensacion
	$resul2=$pdf->buscar(4003);
	$resul=$pdf->buscar(4021);
	if($resul!=null){
        	$this->Row(array(utf8_decode($resul['descrip']),number_format($resul['valor'],0,'.','.').utf8_decode(" Días X "),number_format(($resul2['monto']/30),2,',','.').'  Bs. = ',number_format($resul['monto'],2,',','.')));
		$total+=$resul['monto'];
	}
	//prima servicio
	$resul2=$pdf->buscar(4002);
	$resul=$pdf->buscar(4028);
	if($resul!=null){
        	$this->Row(array(utf8_decode($resul['descrip']),number_format($resul['valor'],0,'.','.').utf8_decode(" Días X "),number_format(($resul2['monto']/30),2,',','.').'  Bs. = ',number_format($resul['monto'],2,',','.')));
		$total+=$resul['monto'];
	}
	//prima profecional
	$resul2=$pdf->buscar(4004);
	$resul=$pdf->buscar(4022);
	if($resul!=null){
        	$this->Row(array(utf8_decode($resul['descrip']),number_format($resul['valor'],0,'.','.').utf8_decode(" Días X "),number_format(($resul2['monto']/30),2,',','.').'  Bs. = ',number_format($resul['monto'],2,',','.')));
		$total+=$resul['monto'];
	}
	//prima jerarquia
	$resul2=$pdf->buscar(4005);
	$resul=$pdf->buscar(4023);
	if($resul!=null){
        	$this->Row(array(utf8_decode($resul['descrip']),number_format($resul['valor'],0,'.','.').utf8_decode(" Días X "),number_format(($resul2['monto']/30),2,',','.').'  Bs. = ',number_format($resul['monto'],2,',','.')));
		$total+=$resul['monto'];
	}
	//prima transporte
	$resul2=$pdf->buscar(4006);
	$resul=$pdf->buscar(4024);
	if($resul!=null){
        	$this->Row(array(utf8_decode($resul['descrip']),number_format($resul['valor'],0,'.','.').utf8_decode(" Días X "),number_format(($resul2['monto']/30),2,',','.').'  Bs. = ',number_format($resul['monto'],2,',','.')));
		$total+=$resul['monto'];
	}
	//prima especial
	$resul2=$pdf->buscar(4007);
	$resul=$pdf->buscar(4025);
	if($resul!=null){
        	$this->Row(array(utf8_decode($resul['descrip']),number_format($resul['valor'],0,'.','.').utf8_decode(" Días X "),number_format(($resul2['monto']/30),2,',','.').'  Bs. = ',number_format($resul['monto'],2,',','.')));
		$total+=$resul['monto'];
	}
	//otros pagos
	$resul=$pdf->buscar(4026);
	if($resul!=null){
        	$this->Row(array(utf8_decode($resul['descrip']),'','',number_format($resul['monto'],2,',','.')));
		$total+=$resul['monto'];
	}
	//clausula
	$resul=$pdf->buscar(4027);
	if($resul!=null){
        	$this->Row(array(utf8_decode($resul['descrip']),'','',number_format($resul['monto'],2,',','.')));
		$total+=$resul['monto'];
	}

	//total
	$this->SetFont('Arial','B',8);
	$this->Setceldas(array(0,0,0,'T'));
	$this->Row(array(utf8_decode("SUB-TOTAL ASIGNACIONES"),' ','',number_format($total,2,',','.')));
	
	return $total;
	

	
}
function conceptos_integrales($ficha,$cod_nomina,$tipo_nomina,$pdf){
	$conexion=conexion();
	
	
	
	$consulta="select * from nom_movimientos_nomina where codnom='$cod_nomina' and codcon=4001 and 'ficha=$ficha' and tipnom=$tipo_nomina";
	$query=query($consulta,$conexion);
	if(($sueldo=num_rows($query))!=0){
		$r=fetch_array($query);
		$sueldo=$r['monto'];
	}else{
		$sueldo=0;
	}
	

	$consulta="select * from nom_movimientos_nomina where codnom='$cod_nomina' and codcon=4003 and 'ficha=$ficha' and tipnom=$tipo_nomina";
	$query=query($consulta,$conexion);
	if(($aporte=num_rows($query))!=0){
		$r=fetch_array($query);
		$aporte=$r['monto'];
	}else{
		$aporte=0;
	}

	$consulta="select * from nom_movimientos_nomina where codnom='$cod_nomina' and codcon=4002 and 'ficha=$ficha' and tipnom=$tipo_nomina";
	$query=query($consulta,$conexion);
	if(($grep=num_rows($query))!=0){
		$r=fetch_array($query);
		$grep=$r['monto'];
	}else{
		$grep=0;
	}

	$consulta="select * from nom_movimientos_nomina where codnom='$cod_nomina' and codcon=4004 and 'ficha=$ficha' and tipnom=$tipo_nomina";
	$query=query($consulta,$conexion);
	if(($hext=num_rows($query))!=0){
		$r=fetch_array($query);
		$hext=$r['monto'];
	}else{
		$hext=0;
	}
	
	$consulta="select  monto from nom_movimientos_nomina where codnom='$cod_nomina' and  codcon=4006 and 'ficha=$ficha' and tipnom=$tipo_nomina";
	$query=query($consulta,$conexion);
	if(($primas=num_rows($query))!=0){
		$r=fetch_array($query);
		$dozu=$r['monto'];
	}else{
		$dozu=0;
	}

	$consulta="select  monto from nom_movimientos_nomina where codnom='$cod_nomina' and codcon=4007 and 'ficha=$ficha' and tipnom=$tipo_nomina";
	$query=query($consulta,$conexion);
	if(($primas=num_rows($query))!=0){
		$r=fetch_array($query);
		$doz=$r['monto'];
	}else{
		$doz=0;
	}
	//$this->Ln();
	$consulta="select * from nompersonal where ficha='".trim($ficha)."' AND tipnom = '".$tipo_nomina."'";
	$query=query($consulta,$conexion);
	$rc=fetch_array($query);
	
	$consulta="select * from nom_dato_liquidacion where ficha='".trim($ficha)."' and tiponom='$tipo_nomina' AND codnom = '".$cod_nomina."' and cedula='".$rc[cedula]."'";
	$query=query($consulta,$conexion);
	$fetch2=fetch_array($query);
	list($dia,$mes,$ano)=explode('-',$fetch2['tiempo_servicio']);

		/*
	
	$this->SetFont('Arial','B',8);
// 	$this->Cell(198,5,utf8_decode('SUELDO'),'LTR',1,'C');
	$this->Cell(20,5,utf8_decode('S. BÁSICO'),'LTR',0,'C');
	$this->Cell(20,5,utf8_decode('P. ANT. Bs.'),'LTR',0,'C');
	$this->Cell(20,5,utf8_decode('P. PROF. Bs.'),'LTR',0,'C');
	$this->Cell(30,5,utf8_decode('HEX. DOM. Y FER. Bs.'),'LTR',0,'C');
	$this->Cell(34,5,utf8_decode('S. BAS. + PRI. + H.D.F.'),'LTR',0,'C');
	$this->Cell(20,5,utf8_decode('DOZ. UTIL.'),'LTR',0,'C');
	$this->Cell(20,5,utf8_decode('DOZ. VAC.'),'LTR',0,'C');
	$this->Cell(34,5,utf8_decode('SUELDO INTEGRAL'),'LTR',1,'C');
	
	$this->SetFont('Arial','I',8);
	// llamado para hacer multilinea sin que haga salto de linea
        $this->SetWidths(array(20,20,20,30,34,20,20,34));
	$this->SetAligns(array('C','C','C','C','C','C','C','C','C'));
        $this->Setceldas(array(1,1,1,1,1,1,1,1,1));
	$this->Setancho(array(5,5,5,5,5,5,5,5,5));
        $this->Row(array(number_format($sueldo,2,',','.'),number_format($aporte,2,',','.'),number_format($grep,2,',','.'),number_format($hext,2,',','.'),number_format($sueldo+$aporte+$grep+$hext,2,',','.'),number_format($dozu,2,',','.'),number_format($doz,2,',','.'),number_format($sueldo+$aporte+$grep+$dozu+$doz+$hext,2,',','.')));
	
	
	$this->Ln(2);
	
	$ab=$pdf->buscar(4028);
	$cc=$pdf->buscar(4027);
	$ee=$pdf->buscar(4026);
	$this->SetFont('Arial','B',8);
	$this->Cell(66,5,utf8_decode('PRESTACIONES ART. 142 LETRA "a" y "b"'),'LTR',0,'C');
	$this->Cell(66,5,utf8_decode('PRESTACIONES ART. 142 LETRA "c"'),'LTR',0,'C');
	$this->Cell(66,5,utf8_decode('PRESTACIONES ART. 142 LETRA "e"'),'LTR',1,'C');
	$this->Cell(66,5,number_format($ab[monto],2,',','.'),1,0,'C');
	$this->Cell(66,5,number_format($cc[monto],2,',','.'),1,0,'C');
	$this->Cell(66,5,number_format($ee[monto],2,',','.'),1,1,'C');
	*/
	
	//fin
	return $sueldo+$aporte+$grep+$dozu+$doz;
	

}
function buscar($codcon){
	$conexion=conexion();
	$registro_id=$_GET['registro_id']. " ";
	$tipo_nomina=$_GET['codt'];
	$codigo_nomina=$_GET['codigo_nomina'];
	$consulta="select * from nom_movimientos_nomina where codnom=$codigo_nomina and codcon=$codcon and ficha=$registro_id and tipnom=$tipo_nomina";
	$query=query($consulta,$conexion);
	cerrar_conexion($conexion);
	return fetch_array($query);
}
function prestaciones($total,$pdf){
	$this->Ln(1);
	$this->SetFont('Arial','B',8);
	$this->Cell(198,5,utf8_decode('LIQUIDACIÓN'),0,1,'C');

	$this->SetFont('Arial','B',8);
	// llamado para hacer multilinea sin que haga salto de linea
        $this->SetWidths(array(12,156,30));
	$this->SetAligns(array('C','C','R'));
        $this->Setceldas(array(1,1,1));
		$this->SetWidths(array(20,148,30));

	$this->Row(array(utf8_decode('REF'),'CONCEPTO','MONTO $'));

	$this->SetFont('Arial','I',8);
	// llamado para hacer multilinea sin que haga salto de linea
        $this->SetWidths(array(12,156,30));
	$this->SetAligns(array('C','L','R'));
        $this->Setceldas(array(1,1,1));
	$this->SetWidths(array(20,148,30));

	
	//preaviso
	$total=0;
	$conexion=conexion();
	$registro_id=$_GET['registro_id']. " ";
	$tipo_nomina=$_GET['codt'];
	$codigo_nomina=$_GET['codigo_nomina'];
	$consulta="select nmn.*, nc.verref from nom_movimientos_nomina nmn join nomconceptos nc on (nmn.codcon=nc.codcon) where nmn.codnom='".trim($codigo_nomina)."' and nmn.ficha='$registro_id' and nmn.tipnom='$tipo_nomina' AND nmn.tipcon='A' and nmn.impdet='S' ORDER BY nmn.codcon DESC";
	$query=query($consulta,$conexion);
	while($resul=fetch_array($query))
	{
		if($resul['verref']==0)
			$ref="";
		else
			$ref=$resul['valor'];
		$this->Row(array(number_format($ref,2,'.',','),utf8_decode($resul['descrip']),number_format($resul['monto'],2,'.',',')));
		$total+=$resul['monto'];
	}
	/*
	$resul=$pdf->buscar(4011);
	$total=0;
	if($resul!=null){
        	$this->Row(array(utf8_decode($resul['descrip']),number_format($resul['valor'],0,'.','.').utf8_decode(" Días X "),number_format(($total/30),2,',','.').'  Bs. = ',number_format($resul['monto'],2,',','.')));
		$total+=$resul['monto'];
	}
	//antiguedad/clusula
	$resul=$pdf->buscar(4012);
	if($resul!=null){
        	$this->Row(array(utf8_decode($resul['descrip']),number_format($resul['valor'],0,'.','.').utf8_decode(" Días X "),number_format(($total/30),2,',','.').'  Bs. = ',number_format($resul['monto'],2,',','.')));
		$total+=$resul['monto'];
	}
	
	$this->Cell(198,5,'VACACIONES ART.223',0,1,'L');

	//vacaciones
	$resul=$pdf->buscar(4013);
	if($resul!=null){
        $this->Row(array(utf8_decode("a) ".$resul['descrip']),number_format($resul['valor'],0,'.','.').utf8_decode(" Meses "),'  Bs. = ',number_format($resul['monto'],2,',','.')));
		$total+=$resul['monto'];
	}
	
	$resul=$pdf->buscar(4014);
	if($resul!=null){
        	$this->Row(array(utf8_decode("b) ".$resul['descrip']),number_format($resul['valor'],0,'.','.').utf8_decode(" Meses "),'  Bs. = ',number_format($resul['monto'],2,',','.')));
		$total+=$resul['monto'];
	}
	$resul=$pdf->buscar(4015);
	if($resul!=null){
        	$this->Row(array(utf8_decode("c) ".$resul['descrip']),number_format($resul['valor'],0,'.','.').utf8_decode(" Meses "),'  Bs. = ',number_format($resul['monto'],2,',','.')));
		$total+=$resul['monto'];
	}
	$resul=$pdf->buscar(4016);
	if($resul!=null){
        	$this->Row(array(utf8_decode("d) ".$resul['descrip']),number_format($resul['valor'],0,'.','.').utf8_decode(" Meses "),'  Bs. = ',number_format($resul['monto'],2,',','.')));
		$total+=$resul['monto'];
	}
	//bonificacion fin de año
	$resul=$pdf->buscar(4017);
	if($resul!=null){
        	$this->Row(array(utf8_decode("".$resul['descrip']),number_format($resul['valor'],0,'.','.').utf8_decode(" Meses "),'  Bs. = ',number_format($resul['monto'],2,',','.')));
		$bono=$resul['monto'];
		$total+=$resul['monto'];
	}
	//sustitutiva
	$resul=$pdf->buscar(4018);
	if($resul!=null){
        	$this->Row(array(utf8_decode("".$resul['descrip']),number_format($resul['valor'],0,'.','.').utf8_decode(" Meses "),'  Bs. = ',number_format($resul['monto'],2,',','.')));
		$total+=$resul['monto'];
	}
	//INCE
	$resul=$pdf->buscar(2502);
	if($resul!=null){
        	$this->Row(array(utf8_decode("".$resul['descrip']),number_format($bono,0,'.','.').' X '.utf8_decode(" 0.50 %"),'  Bs. = ',number_format($resul['monto'],2,',','.')));
		$total+=$resul['monto'];
	}

	//anticipo
	$resul=$pdf->buscar(2601);
	if($resul!=null){
        	$this->Row(array(utf8_decode("".$resul['descrip']),number_format($resul['valor'],0,'.','.').utf8_decode(" Días "),'  Bs. = ',number_format($resul['monto'],2,',','.')));
		$total-=$resul['monto'];
	}
	*/
	//total
	$this->SetFont('Arial','B',8);
	$this->SetWidths(array(30,138,30));
	$this->Setceldas(array(0,0,1,'T'));
	$pdf->SetAligns(array('R','R','R'));
	$this->Row(array('',utf8_decode("TOTAL ASIGNACIONES: "),number_format($total,2,'.',',')));
	
	$this->Ln(2);
	$this->SetFont('Arial','I',8);
	// llamado para hacer multilinea sin que haga salto de linea
        $this->SetWidths(array(10,158,30));
	$this->SetAligns(array('C','L','R'));
        $this->Setceldas(array(1,1,1));
	$this->Setancho(array(5,5,5));	

	$total2=0;
	$consulta="select * from nom_movimientos_nomina where codnom=".trim($codigo_nomina)." and ficha=$registro_id and tipnom=$tipo_nomina AND tipcon='D' and impdet='S'";
	$query2=query($consulta,$conexion);
	while($resul2=fetch_array($query2))
	{
		$this->Row(array(number_format($resul2['valor'],0,'.','.'),utf8_decode($resul2['descrip']),number_format($resul2['monto'],2,'.',',')));
		$total2+=$resul2['monto'];
	}
	
	$this->SetFont('Arial','B',8);
	$this->SetWidths(array(30,138,30));
	$this->Setceldas(array(0,0,1,'T'));
	$pdf->SetAligns(array('R','R','R'));
	$this->Row(array('',utf8_decode("TOTAL DEDUCCIONES: "),number_format($total2,2,'.',',')));
	return $total-$total2;
	
	//fin
	
}

function pie_pagina($ficha,$cod_nomina,$codnom){
	$conexion=conexion();
	$consulta="select * from nompersonal where ficha='".trim($ficha)."' AND tipnom = '".$cod_nomina."'";
	$query=query($consulta,$conexion);
	$rc=fetch_array($query);
	
	$consulta="select observacion from nom_dato_liquidacion where ficha='".trim($ficha)."' AND tiponom = '".$cod_nomina."' and cedula='".$rc[cedula]."' AND codnom = '".$codnom."'";
	$query=query($consulta,$conexion);
	$fetch2=fetch_array($query);
	$observacion=$fetch2['observacion'];

	$this->SetY(-83);
	$this->SetFont('Arial','',8);
	// llamado para hacer multilinea sin que haga salto de linea
        $this->SetWidths(array(30,168));
	$this->SetAligns(array('C','L'));
        $this->Setceldas(array('LTB','TBR'));
	$this->Setancho(array(7,7));
	//,utf8_decode($rcc['des_car']
        $this->Row(array("OBSERVACIONES: ",utf8_decode($observacion)));
        $this->Ln();
        $this->SetFont('Arial','',8);
	// llamado para hacer multilinea sin que haga salto de linea
        $this->SetWidths(array(198));
	$this->SetAligns(array('J'));
        $this->Setceldas(array(0));
	$this->Setancho(array(5));
	//,utf8_decode($rcc['des_car']
        $this->Row(array(utf8_decode("YO _______________________ HAGO CONSTAR QUE HE RECIBIDO DE LA EMPRESA LA CANTIDAD INDICADA EN LA PRESENTE PLANILLA DE LIQUIDACIÓN EN LA CUAL ESTAN INCLUIDAS TODAS MIS PRESTACIONES Y ESTOY TOTALMENTE DE ACUERDO CON EL MONTO QUE ME CORRESPONDE Y DE CUALQUIER OBJECIÓN CON MI FIRMA QUEDA SIN EFECTO.")));
        $this->Ln();
        
        $this->Cell(66,5,"FECHA: _______________________",0,0,'C');
	$this->Cell(66,5,"NOMBRE: _______________________",0,0,'C');
	$this->Cell(66,5,"FIRMA: _______________________",0,1,'C');
	//fin
}

function Footer(){
	
	$this->SetY(-41);
//	liquidacion2($this->fpdf);
	//$this->Cell(0,5,utf8_decode('Página ').$this->PageNo().'/{nb}',0,1,'C');
}


}


//Creación del objeto de la clase heredada
$pdf=new PDF("P","mm","A4");
$pdf->AliasNbPages();
$pdf->AddPage('P','Letter');
$pdf->AddFont('Sanserif','','sanserif.php');
$pdf->SetFont('Sanserif','',12);


$registro_id=$_GET['registro_id']. " ";
$tipo_nomina=$_GET['codt'];
$codigo_nomina=$_GET['codigo_nomina'];
$pdf->fpdf=$pdf;
$pdf->datos_empleado($registro_id,$tipo_nomina,$codigo_nomina);
$total=$pdf->conceptos_integrales($registro_id,$codigo_nomina,$tipo_nomina,$pdf);
$tpres=$pdf->prestaciones($total,$pdf);
//$pdf->imprimir_otros_pagos($registro_id,$codigo_nomina,$tipo_nomina,$pdf);
//$tasig=$pdf->asignaciones($total,$pdf);
//$tdedu=$pdf->deducciones($total,$pdf);

$pdf->SetFont('Arial','B',8);
$pdf->Setceldas(array(0,0,1,'T'));
$pdf->SetWidths(array(108,60,30));
$pdf->Row(array('',utf8_decode("NETO A PAGAR"),number_format($tpres,2,'.',',')));
$pdf->pie_pagina($registro_id,$tipo_nomina,$codigo_nomina);


$pdf->Output();
?>
