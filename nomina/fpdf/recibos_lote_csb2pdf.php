<?php 
session_start();
ob_start();
//$termino=$_SESSION['termino'];
//$tipo=$_GET['tipo'];
$nomina_id=$_GET['nomina_id'];
$codtp=$_GET['codt'];


require('fpdf.php');

include("../lib/common.php");

function fecha($value) { // fecha de YYYY/MM/DD a DD/MM/YYYY
    if ( ! empty($value) ) return substr($value,8,2) ."/". substr($value,5,2) ."/". substr($value,0,4);
}


class PDF extends FPDF
{

    function header(){
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

    function personas($nomina_id,$codtp){

        $conexion=conexion();

        $consulta3 = "SELECT nnp.periodo_ini, nnp.periodo_fin, nnp.periodo, nnp.descrip, nfr.descrip AS frecuencia
        FROM nom_nominas_pago nnp
        JOIN nomfrecuencias nfr ON (nnp.frecuencia = nfr.codfre)
        WHERE nnp.codnom = '".$nomina_id."' AND nnp.codtip = '".$codtp."'";
        $result5 = query($consulta3,$conexion);
        $fetch5 = fetch_array($result5);
    	
        $query="select * from nomempresa";		
        $result=query($query,$conexion);	
        $row = fetch_array ($result);	
        $nompre_empresa=$row[nom_emp];
        $ciudad=$row[ciu_emp];
        $gerente=$row[ger_rrhh];
    	
        $query="SELECT pe.tipnom AS tipnom,pe.foto AS foto,pe.fecing AS fec_ing,pe.cedula AS cedula,pe.ficha AS ficha,pe.apenom AS apenom,pe.suesal AS sueldopro,pe.codnivel1 AS codnivel1,pe.codnivel2 AS codnivel2,pe.codnivel3 AS codnivel3,car.des_car AS cargo 
        FROM nom_movimientos_nomina mn 
        LEFT JOIN nompersonal pe on mn.ficha = pe.ficha 
        LEFT JOIN nomcargos car on pe.codcargo = car.cod_car
        LEFT JOIN nomconceptos c on c.codcon = mn.codcon
        WHERE mn.codnom='".$nomina_id."' and mn.tipnom='".$_SESSION['codigo_nomina']."' AND ( pe.estado  NOT LIKE '%De Baja%')
        group by pe.ficha order by pe.ficha  "; 
        $result_lote=query($query,$conexion);	
        $totalbd=num_rows($result);
    	
        $cont = 0;
        $cont2 = 0;
        $xxx = 74;
        while ($fila=fetch_array($result_lote))
        {
            //ENCABEZADO
            //$this->Ln(3);
		    $conexion=conexion();
	    	$var_sql="select * from nomempresa";
	    	$rs = query($var_sql,$conexion);
	    	$row_rs = fetch_array($rs);
	    	$var_encabezado=strtoupper($row_rs['nom_emp']);
		    $var_rif=$row_rs['rif'];
	    	$this->SetFont('Arial','',9);
	    	$date1=date('d/m/Y');
	    	$date2=date('h:i a');		   
		    $this->Cell(70,3,utf8_decode($var_encabezado),0,0,'L');
	    	$this->Cell(50,3,'RECIBO DE PAGO'.$ANIO,0,0,'C');
	    	$this->Cell(70,3,'FECHA:  '.$date1,0,1,'R');
            $this->Cell(70,3,utf8_decode($var_rif),0,0,'L');
            $this->Cell(150,3,$_SESSION["nomina"]." - ".$fetch5["frecuencia"],0,0,'L');
            $this->Ln();
		    //////////////////////////////

        	//Datos personal
	    	$registro_id=$fila['ficha'];
	    	$query="select * from nompersonal where ficha = '$registro_id' and tipnom=$_SESSION[codigo_nomina]";
	    	$result=query($query,$conexion);	
	    	$fila = fetch_array ($result);	
	    	$cargo_id=$fila['codcargo'];
	    	//$ingreso=$fila['fecing'];
	    	
	    	$query="select des_car from nomcargos where cod_car = '$cargo_id'";		
	    	$result=query($query,$conexion);	
	    	$row = fetch_array ($result);	
	    	$nompre_cargo=$row[des_car];
	    	$sub_total_dedu=0;
		    //$this->Ln();
	  	
	    	$this->SetFont('Arial','',9);
	    	$this->SetWidths(array(60,25,40,30,60));
	    	$this->SetAligns(array('L','L','L','L','L'));
	    	$this->Setceldas(array(0,0,0,0,0));
	    	$this->Setancho(array(3,3,3,3,3));
	    	$query="select cod_ban,des_ban from nombancos where cod_ban='".$fila[codbancob]."'";
	    	$resultado=query($query,$conexion);
	    	$row2 = mysqli_fetch_array($resultado);
		    $this->Row(array(utf8_decode('NOMBRE: ').utf8_decode($fila[apenom]),'FICHA: '.$fila[ficha],utf8_decode('CEDULA: ').$fila[cedula],'SALARIO: '.number_format($fila[suesal],2,',','.'))); 
		    //quitar ; y poner , y descomentar aqui y Fecha ingreso arriba 'FECHA INGRESO: '.date("d/m/Y",strtotime($ingreso))));

		    $query="select cod_car, descrip_corto AS des_car from nomcargos where cod_car='".$fila[codcargo]."'";
	    	$result=query($query,$conexion);
	    	$row = mysqli_fetch_array($result);
            $this->SetFont('Arial','',9);
            $this->SetAligns(array('L','L','L'));
            $this->Setceldas(array(0,0,0));
            $this->Setancho(array(3,3,3));
	    	$this->SetWidths(array(60,80,60));
            //$this->Row(array('Cargo: '.utf8_decode($row[des_car]),'Periodo del: '.fecha($fetch5['periodo_ini']).' al: '.fecha($fetch5['periodo_fin']),'Banco/Cuenta: '.$row[des_ban] .$row2[des_ban].'- '. $fila[cuentacob]));
            $this->Row(array('CARG: '.utf8_decode($row[des_car]),'PERIODO DEL: '.fecha($fetch5['periodo_ini']).' AL: '.fecha($fetch5['periodo_fin']),'CUENTA: '. $fila[cuentacob]));
	    	$this->SetWidths(array(10,10));
	    	
            $this->Cell(70,5,utf8_decode('CODIGO - ASIGNACIONES'),'T',0,'C');
            $this->Cell(30,5,utf8_decode(''),'T',0,'C');
            $this->Cell(60,5,utf8_decode('CODIGO - DEDUCCIONES'),'T',0,'C');
            $this->Cell(35,5,utf8_decode(''),'T',1,'C');
			
	    	$query="SELECT mn.codcon, c.descrip, mn.tipcon, mn.monto, mn.valor, pe.apenom,c.formula, c.descripexcel AS dcorta
		    FROM nom_movimientos_nomina as mn
		    LEFT join nompersonal as pe on mn.ficha = pe.ficha
		    LEFT join nomconceptos as c on c.codcon = mn.codcon
		    where pe.ficha = '$registro_id' and pe.tipnom =".$_SESSION['codigo_nomina']." and mn.codnom= '".$nomina_id."' and mn.tipnom='".$codtp."' and mn.tipcon<>'P' AND ( pe.estado  NOT LIKE '%De Baja%')
		group by pe.apenom,pe.ficha,c.formula,c.codcon order by pe.apenom, mn.tipcon";
		    $result =query($query,$conexion);
		
	    	$sub_total_asig=0;
	    	$sub_total_dedu=0;
            $cadAsig = '';
            $cadDedu = '';
            $cantAsig = 0;
            $cantDeduc = 0;
            $xx = 20;
            while ($row = mysqli_fetch_array($result))
            {
                $saldo=0;
                if ($row[tipcon]=='A')
                {
                    $asig= number_format($row[monto],2,',','.');
                    $sub_total_asig=$row[monto]+$sub_total_asig;
                }
                if ($row[tipcon]=='D')
                {
                    $dedu= number_format($row[monto],2,',','.');
                    $sub_total_dedu=$row[monto]+$sub_total_dedu;
                    if(($row[codcon]>=500)&&($row[codcon]<=599))
                    {
                        $consulta = "SELECT SUM(salfinal) as total FROM nomprestamos_detalles as pd inner join nomprestamos_cabecera as pc on (pd.numpre=pc.numpre) WHERE pd.ficha='".$fila[ficha]."' and pd.fechaven between '".$fetch5['periodo_ini']."' and '".$fetch5['periodo_fin']."' and pd.estadopre='Pendiente' and pc.codigopr='".number_format($row[valor],0,'','')."'";
                        $resultsal=query($consulta,$conexion);
                        $rowsal = fetch_array($resultsal);
                        $saldo = $rowsal[total];
                        $saldo = number_format($saldo,2,'.',',');
                    }
                }

                if($cont2 == 0)
                    $xx = 20;
                else
                    $xx = 19;

                if ($row[tipcon]=='A')
                {
                    $this->SetY($xx+$cont+$cantAsig);
                    $this->Cell(55,6,$row[codcon]."-".utf8_decode($row[dcorta]).":".$row[valor]." ",0,0,'L');
                    $this->Cell(15,6,$asig,0,1,'R');
                    $cantAsig+=3;
                }
                elseif ($row[tipcon]=='D')
                {
                    $this->SetXY(103,$xx+$cont+$cantDeduc);
                    $this->Cell(65,6,$row[codcon]."-".utf8_decode($row[dcorta]).": ",0,0,'L');
                    $this->Cell(15,6,$dedu,0,1,'R');
                    $cantDeduc+=3;
                }                
            }
            
            if($cont2 == 2)
                $this->SetXY(10,255);
            elseif($cont2 == 0)
                $this->SetXY(10,$xxx+$cont);
            else
                $this->SetXY(10,$xxx+$cont+1);
            
            $this->Cell(35,5,'SUB - TOTALES ','T',0,'L');
            $this->Cell(30,5,'TOTAL ASIG.:   '.number_format($sub_total_asig,2,',','.'),'T',0,'R');
            $this->Cell(65,5,'TOTAL DEDUC.:   '.number_format($sub_total_dedu,2,',','.'),'T',0,'R');
            $this->Cell(35,5,'NETO: ','T',0,'R');
            $this->Cell(20,5,number_format($sub_total_asig-$sub_total_dedu,2,',','.'),'T',1,'L');

            $this->Ln(4);
            $this->Cell(35,5,'',0,0,'R');
            $this->Cell(35,5,'RECIBE CONFORME','T',0,'C');
            $this->Cell(65,5,'',0,0,'R');
            $this->Cell(35,5,'FECHA','T',0,'C');
            $this->Cell(20,5,'',0,1,'L');
            
            if($cont2 <= 1)
            {
                $this->Cell(1,1,utf8_decode('--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------'),0,0,'L');
            }
            $this->Ln(9);

            if($cont2 == 2)
                $cont+=0;
            else
                $cont+=93;
            //echo $cont;
            //echo "<br>";
            
            if($cont2 >= 2)
            {     
                $this->Ln(400);
                $cont = 0;
                $cont2 = 0;
            }
            else                
                $cont2++;
		}
	}
}


//Creación del objeto de la clase heredada
// $pdf = new PDF('L', 'mm', array(215,139));
$pdf=new PDF('P','mm','LETTER');
$pdf->SetMargins(5,5,5,5);

//$pdf->AddFont('Sanserif','','sanserif.php');
//$pdf->AddFont('SanserifB','','TEACPSSB.php');
$pdf->AliasNbPages();
$pdf->AddPage('P','LETTER');
$pdf-> SetAutoPageBreak(1 ,1);
//$pdf->SetFont('Sanserif','',9);
$pdf->AddFont('Sanserif','','sanserif.php');
$pdf->SetFont('Arial','',9);
$pdf->personas($nomina_id,$codtp);
$pdf->Output();
?>
