<?php 
session_start();
ob_start();
//$termino=$_SESSION['termino'];
//$tipo=$_GET['tipo'];
$nomina_id=$_GET['nomina_id'] ? $_GET['nomina_id'] : '';
$codtp=$_GET['codt'] ? $_GET['codt'] : '';

$ficha_buscar=$_GET['ficha_buscar'] ? $_GET['ficha_buscar'] : '';

require('../fpdf.php');
include("../../lib/common.php");

function fecha($value) { // fecha de YYYY/MM/DD a DD/MM/YYYY
 if ( ! empty($value) ) return substr($value,8,2) ."/". substr($value,5,2) ."/". substr($value,0,4);
}

class PDF extends FPDF
{
    var $ficha_buscar;
    var $nomempresa,$rif;
    function header(){

        $conexion=conexion();
        $var_sql="select * from nomempresa";
        $rs = query($var_sql,$conexion);
        $row_rs = fetch_array($rs);
        $this->nomempresa =$row_rs['nom_emp'];
        $this->rif=$row_rs['rif'];
        $var_izquierda='../../imagenes/'.$row_rs[imagen_izq];
        $var_derecha='../../imagenes/'.$row_rs[imagen_der];
        //$this->Image($var_derecha,10,6,30,15);

        $this->SetFont('Arial','',9);
        $date1=date('d/m/Y');
        $date2=date('h:i a');	
        //$this->Ln(10);

        $this->Cell(70,4,'',0,0,'L');
        //	$this->Cell(70,5,utf8_decode($var_encabezado),0,0,'L');
        $this->Cell(50,4,'',0,0,'C');
        $this->Cell(70,4,'',0,1,'R');
        //	$this->Cell(50,5,'Gobierno de Carabobo',0,0,'L');  
        $this->Cell(70,4,'',0,0,'L');
        $this->Cell(150,4,'',0,0,'L');
        $this->Ln(3);
    //	
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

    function personas($nomina_id,$codtp){

        $conexion=conexion();

        $consulta3 = "SELECT periodo_ini, periodo_fin, periodo FROM nom_nominas_pago WHERE codnom = '".$nomina_id."' AND codtip = '".$codtp."'";
        $result5 = query($consulta3,$conexion);
        $fetch5 = fetch_array($result5);
    	
        $query="select * from nomempresa";		
        $result=query($query,$conexion);	
        $row = fetch_array ($result);	
        $nompre_empresa=$row[nom_emp];
        $ciudad=$row[ciu_emp];
        $gerente=$row[ger_rrhh];
    	
        if ($this->ficha_buscar != "" && $this->ficha_buscar != null) {
            $consulta_WHERE = "  AND  pe.ficha =   '".$this->ficha_buscar."' ";
        }
        else{
            $consulta_WHERE = " ";
    
        }
    	
        $query="SELECT pe.tipnom AS tipnom,pe.foto AS foto,pe.fecing AS fec_ing,pe.cedula AS cedula,pe.ficha AS ficha,pe.apenom AS apenom,pe.suesal AS sueldopro,pe.codnivel1 AS codnivel1,pe.codnivel2 AS codnivel2,pe.codnivel3 AS codnivel3,car.des_car AS cargo 
        FROM nom_movimientos_nomina mn 
        LEFT JOIN nompersonal pe on mn.ficha = pe.ficha 
        LEFT JOIN nomcargos car on pe.codcargo = car.cod_car
        LEFT JOIN nomconceptos c on c.codcon = mn.codcon
        WHERE mn.codnom='".$nomina_id."' and mn.tipnom='".$codtp."'
        AND mn.codcon in (210) ";
        $query .= $consulta_WHERE;
        $query .= "group by pe.ficha 
        order by pe.ficha ASC"; 
        $result_lote=query($query,$conexion);	
        $totalbd=num_rows($result);
    	
        while ($fila=fetch_array($result_lote))
        {
        	//Datos personal
            $registro_id=$fila['ficha'];
            $query="SELECT * from nompersonal where ficha = '$registro_id' and tipnom=$codtp";
            $result=query($query,$conexion);	
            $fila = fetch_array ($result);	
            $cargo_id=$fila['codcargo'];
            $ingreso=$fila['fecing'];
            
            $query="SELECT des_car from nomcargos where cod_car = '$cargo_id'";		
            $result=query($query,$conexion);	
            $row = fetch_array ($result);	
            $nompre_cargo=$row[des_car];
            $sub_total_dedu=0;
            $this->Ln(3);
        
            $this->SetFont('Arial','',9);
            $this->SetWidths(array(70,90,80));
            $this->SetAligns(array('L','L','L'));
            $this->Setceldas(array(0,0,0));
            $this->Setancho(array(4,4,4));
            $query="SELECT cod_ban,des_ban from nombancos where cod_ban='".$fila[codbancob]."'";
            $resultado=query($query,$conexion);
            $row2 = mysqli_fetch_array($resultado);
            $this->Row(array(utf8_decode('CÓDIGO:').$fila[ficha],utf8_decode('NOMBRE:').utf8_decode($fila[apenom]),utf8_decode('CÉDULA:').$fila[cedula]));
            $this->Ln(85);
                     
            $query="SELECT mn.codcon, c.descrip, mn.tipcon, mn.monto, mn.valor, pe.apenom,c.formula
                FROM nom_movimientos_nomina as mn
                LEFT join nompersonal as pe on mn.ficha = pe.ficha
                LEFT join nomconceptos as c on c.codcon = mn.codcon
                where pe.ficha = '$registro_id' and pe.tipnom =".$codtp." AND mn.codnom= '".$nomina_id."' and mn.tipnom='".$codtp."' and mn.tipcon<>'P' 
            
                AND (mn.codcon >=210 AND mn.codcon <=299)
                group by pe.apenom,pe.ficha,c.formula,c.codcon order by pe.apenom, mn.tipcon";
                $result =query($query,$conexion);

            $sub_total_asig=0;
            $sub_total_dedu=0;
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
                    }
                }
                $saldo = number_format($saldo,2,'.',',');                  
                $asig='';
                $dedu='';
            }
            
            $neto = $sub_total_asig-$sub_total_dedu;
            $this->Ln(18);
            $this->SetWidths(array(60,60,60,60,60,60));
            //$this->Cell(188,1,'','TB',1);
            $this->Cell(35,4,'CONFORME: ',0,0,'L');
            $this->Cell(75,4,'','B',0,'L');	
            $this->Cell(5,4,'',0,0,'L');
            $this->Cell(15,4,'FECHA: ',0,0,'R');
            $this->Cell(30,4,'','B',0,'R');
            $dedu= number_format(($neto),2,',','.');
            $codigo = str_replace(',','', str_replace('.','',$registro_id.$dedu) );
            $this->Cell(40,4,$codigo,0,0,'C');
            $this->Ln(1);
            $this->SetWidths(array(60,60,60,60,60,60));
            //segundo recibo de pago 
                
            if($this->GetY()<135)
            {            
                $this->SetY(-135);
                $this->Cell(1,1,utf8_decode('-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------'),0,0,'L');

                $this->Ln();
                $this->SetFont('Arial','',9);
                $date1=date('d/m/Y');
                $date2=date('h:i a');	
                //$this->Ln(10);

                $this->Cell(70,4,utf8_decode(''),0,0,'L');
                //	$this->Cell(70,5,utf8_decode($var_encabezado),0,0,'L');
                $this->Cell(50,4,'',0,0,'C');
                $this->Cell(70,4,'',0,1,'R');
                //	$this->Cell(50,5,'Gobierno de Carabobo',0,0,'L');  
                $this->Cell(70,4,utf8_decode(''),0,0,'L');
                $this->Cell(150,4,'',0,0,'L');
                $this->Ln(2);
            }
            else{
                $this->Ln(410);
            }
        }
    }
}
ob_clean();

//Creación del objeto de la clase heredada
// $pdf = new PDF('L', 'mm', array(215,139));
$pdf=new PDF('P','mm','LETTER');
$pdf->SetMargins(5,5,5,5);
$pdf->ficha_buscar = $ficha_buscar;

$pdf->AddFont('Sanserif','','sanserif.php');
//$pdf->AddFont('SanserifB','','TEACPSSB.php');
$pdf->AliasNbPages();
$pdf->AddPage('P','LETTER');
$pdf-> SetAutoPageBreak(1 ,1);
//$pdf->SetFont('Sanserif','',9);
$pdf->SetFont('Arial','',9);
$pdf->personas($nomina_id,$codtp);
$pdf->Output();
?>
