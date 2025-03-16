<?php 
session_start();
ob_start();
//$termino=$_SESSION['termino'];
//$tipo=$_GET['tipo'];
$nomina_id=$_GET['nomina_id'];
$codtp=$_GET['codt'];
$ficha_buscar=$_GET['ficha_buscar'];


require('fpdf.php');

include("../lib/common.php");

function fecha($value) { // fecha de YYYY/MM/DD a DD/MM/YYYY
 if ( ! empty($value) ) return substr($value,8,2) ."/". substr($value,5,2) ."/". substr($value,0,4);
}


class PDF extends FPDF
{
	var $ficha_buscar;

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
    	
    	$this->SetFont('Arial','',9);
    	$date1=date('d/m/Y');
    	$date2=date('h:i a');	
            //$this->Ln(10);
           
            $this->Cell(70,4,utf8_decode($var_encabezado),0,0,'L');
    //	$this->Cell(70,5,utf8_decode($var_encabezado),0,0,'L');
    	$this->Cell(50,4,'RECIBO DE PAGO'.$ANIO,0,0,'C');
    	$this->Cell(70,4,'Fecha:  '.$date1,0,1,'R');
    //	$this->Cell(50,5,'Gobierno de Carabobo',0,0,'L');  
          $this->Cell(70,4,utf8_decode($var_rif),0,0,'L');
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
        $pais=$row["pais"];
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
        WHERE mn.codnom='".$nomina_id."' and mn.tipnom='".$_SESSION['codigo_nomina']."' AND (pe.estado NOT LIKE '%Egresado%' AND pe.estado  NOT LIKE '%De Baja%')
        ";
        $query .= $consulta_WHERE;
        $query .= "group by pe.ficha 
        order by pe.ficha ASC";  
        $result_lote=query($query,$conexion);	
        $totalbd=num_rows($result);
    	
        while ($fila=fetch_array($result_lote))
        {
        	//Datos personal
    	$registro_id=$fila['ficha'];
    	$query="select * from nompersonal where ficha = '$registro_id' and tipnom=$_SESSION[codigo_nomina]";
    	$result=query($query,$conexion);	
    	$fila = fetch_array ($result);	
    	$cargo_id=$fila['codcargo'];
    	$ingreso=$fila['fecing'];
    	
    	$query="select des_car from nomcargos where cod_car = '$cargo_id'";		
    	$result=query($query,$conexion);	
    	$row = fetch_array ($result);	
    	$nompre_cargo=$row[des_car];
    	$sub_total_dedu=0;
        $this->Ln(3);
  	
    	$this->SetFont('Arial','',9);
    	$this->SetWidths(array(80,90,80));
    	$this->SetAligns(array('L','L','L'));
    	$this->Setceldas(array(0,0,0));
    	$this->Setancho(array(4,4,4));
    	$query="select cod_ban,des_ban from nombancos where cod_ban='".$fila[codbancob]."'";
    	$resultado=query($query,$conexion);
    	$row2 = mysqli_fetch_array($resultado);
        $this->Row(array('Ficha: '.$fila[ficha],utf8_decode('Nombre:').$fila[apenom],utf8_decode('Cédula:').$fila[cedula]));
          
                
        $this->SetWidths(array(60,123));
        $this->Row(array('Sueldo/salario: '.number_format($fila[suesal],2,',','.'),$_SESSION[nomina]));
    	$this->SetWidths(array(100,123));
    	$this->Row(array('Fecha de ingreso: '.date("d/m/Y",strtotime($ingreso)),'Periodo del: '.fecha($fetch5['periodo_ini']).' al: '.fecha($fetch5['periodo_fin'])));
    	$this->SetWidths(array(10,10));
    	$query="select cod_car,des_car from nomcargos where cod_car='".$fila[codcargo]."'";
    	$result=query($query,$conexion);
    	$row = mysqli_fetch_array($result);
    	$this->SetWidths(array(100,123));
    	$this->SetAligns(array('L','L'));
    	$this->Setceldas(array(0,0));
    	$this->Setancho(array(4,4));
    	$this->Row(array('Cargo: '.utf8_decode($row[des_car]),'Banco/Cuenta: '.$row[des_ban] .$row2[des_ban].'- '. $fila[cuentacob]));
    	$this->Ln(1);
    				
    	$query="SELECT mn.codcon, c.descrip, mn.tipcon, mn.monto, mn.valor, pe.apenom,c.formula, c.ccosto,mn.numpre
            FROM nom_movimientos_nomina as mn
            LEFT join nompersonal as pe on mn.ficha = pe.ficha
            LEFT join nomconceptos as c on c.codcon = mn.codcon
            where pe.ficha = '$registro_id' and pe.tipnom =".$_SESSION['codigo_nomina']." and mn.codnom= '".$nomina_id."' and mn.tipnom='".$codtp."' and mn.tipcon<>'P' AND (pe.estado NOT LIKE '%Egresado%' AND pe.estado  NOT LIKE '%De Baja%')
         order by pe.apenom, mn.tipcon";

        $this->Cell(1,1,utf8_decode('-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------'),0,0,'L');
        $this->Ln(1);

            $result =query($query,$conexion);
        $this->Cell(80,4,utf8_decode('Código y Descripción de Concepto'),0,0,'L');
        $this->Cell(25,4,utf8_decode('Ref'),0,0,'C');
        $this->Cell(31,4,utf8_decode('Asignación'),0,0,'R');
        $this->Cell(30,4,utf8_decode('Deducción'),0,0,'R');
        $this->Cell(25,4,utf8_decode('Saldo P.'),0,0,'R');  
        $this->Ln(3);

        $this->Cell(1,4,utf8_decode('-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------'),0,0,'L');
        $this->Ln(2);

    	$sub_total_asig=0;
        $sub_total_dedu=0;
        
    	while ($row = mysqli_fetch_array($result))
    	{
                $saldo=0;
                $saldo = number_format($saldo,2,'.',',');
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
                         $consulta = "SELECT salfinal as total 
                        FROM nomprestamos_detalles as pd 
                        inner join nomprestamos_cabecera as pc on (pd.numpre=pc.numpre) 
                        WHERE pd.ficha='".$fila[ficha]."' and pd.fechaven between '".$fetch5['periodo_ini']."' and '".$fetch5['periodo_fin']."' and pd.estadopre in ('Pendiente' , 'Cancelada')
                        and pc.codigopr='".$row[ccosto]."' AND pc.numpre = '".$row[numpre]."'";
                        $resultsal=query($consulta,$conexion);

                        $rowsal = fetch_array($resultsal);
                        $saldo = $rowsal[total];
                        //$saldo=0;
                        $saldo = number_format($saldo,2,'.',',');
                        //echo $consulta;exit;
                    }
                }
                $this->SetFont('Arial','',9);
                $this->SetWidths(array(80,25,31,30,25));
                $this->SetAligns(array('L','C','R','R','R'));
                $this->Setceldas(array(0,0,0,0,0));
                $this->Setancho(array(4,4,4,4,4));
                $this->Row(array($row[codcon] . ' - ' . utf8_decode($row[descrip]),$row[valor],$asig,$dedu,$saldo));
                $asig='';
                $dedu='';
    		
    	}
    	
    	$this->Cell(105,5,'Sub-Totales: ',0,0,'R');
    	$this->Cell(31,4,number_format($sub_total_asig,2,',','.'),'T',0,'R');
    	$this->Cell(30,4,number_format($sub_total_dedu,2,',','.'),'T',1,'R');
    	//	$this->Ln(1);
    	$this->Cell(120,4,'Neto a Depositar : ',0,0,'R');
    	$this->Cell(46,4,number_format($sub_total_asig-$sub_total_dedu,2,',','.'),0,1,'R');
            //$this->MultiCell(188,5,'Observaciones: '.$observacion,0);
        $this->Ln(3);
            //$this->Cell(188,1,'','TB',1);
    	$this->Cell(44,4,'RECIBE CONFORME',0,0,'C');
    	$this->Cell(40,4,'','B',0,'C');	
    	$this->Cell(44,4,'FECHA',0,0,'C');
        $this->Cell(40,4,'','B',0,'C');
            $this->Ln();
        if($pais != 4){
            //segundo recibo de pago 
            
            $this->SetY(-135);
            $this->Cell(1,0,utf8_decode('-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------'),0,0,'L');
            $this->Ln(1);
    	$var_sql="select * from nomempresa";
    	$rs = query($var_sql,$conexion);
    	$row_rs = fetch_array($rs);
    	$var_encabezado=$row_rs['nom_emp'];
            $var_rif=$row_rs['rif'];
            $date1=date('d/m/Y');
            $this->Cell(70,4,utf8_decode($var_encabezado),0,0,'L');
    	$this->Cell(50,4,'RECIBO DE PAGO'.$ANIO,0,0,'C');
    	$this->Cell(70,4,'Fecha:  '.$date1,0,1,'R');
    	$this->Ln(1);
            $this->Cell(70,4,utf8_decode($var_rif),0,0,'L');
            $this->Cell(118,4,'',0,0,'L');
    	
    //	
            
    	
            $registro_id=$fila['ficha'];
    	$query="select * from nompersonal where (estado NOT LIKE '%Egresado%' AND estado  NOT LIKE '%De Baja%') AND ficha = '$registro_id' and tipnom=$_SESSION[codigo_nomina] order by ficha";
    	$result=query($query,$conexion);	
    	$fila = fetch_array ($result);	
    	$cargo_id=$fila['codcargo'];
    	$ingreso=$fila['fecing'];
    	
    	$query="select des_car from nomcargos where cod_car = '$cargo_id'";		
    	$result=query($query,$conexion);	
    	$row = fetch_array ($result);	
    	$nompre_cargo=$row[des_car];
    	$sub_total_dedu=0;
    	
    	 $this->Ln(3);
    
        $this->SetFont('Arial','',9);
        $this->SetWidths(array(80,90,80));
        $this->SetAligns(array('L','L','L'));
        $this->Setceldas(array(0,0,0));
        $this->Setancho(array(4,4,4));
    	$query="select cod_ban,des_ban from nombancos where cod_ban='".$fila[codbancob]."'";
    	$resultado=query($query,$conexion);
    	$row2 = mysqli_fetch_array($resultado);
            $this->Row(array('Ficha: '.$fila[ficha],utf8_decode('Nombre:').$fila[apenom],utf8_decode('Cédula:').$fila[cedula]));
    	//$this->Row(array('Ficha: '.$fila[ficha],utf8_decode('Cédula: ').number_format($fila[cedula],0,'.','.')));
    	//$this->Row(array(number_format($fila[cedula],0,'.','.'),utf8_decode($fila[apenom]),$fila[ficha]));             
                
            $this->SetWidths(array(60,123));
            $this->Row(array('Sueldo/salario: '.number_format($fila[suesal],2,',','.'),$_SESSION[nomina]));
    	$this->SetWidths(array(100,123));
    	$this->Row(array('Fecha de ingreso: '.date("d/m/Y",strtotime($ingreso)),'Periodo del: '.fecha($fetch5['periodo_ini']).' al: '.fecha($fetch5['periodo_fin'])));
    	$this->SetWidths(array(10,10));
    	$query="select cod_car,des_car from nomcargos where cod_car='".$fila[codcargo]."'";
    	$result=query($query,$conexion);
    	$row = mysqli_fetch_array($result);
    	$this->SetWidths(array(100,123));
    	$this->SetAligns(array('L','L'));
    	$this->Setceldas(array(0,0));
    	$this->Setancho(array(4,4));
    	$this->Row(array('Cargo: '.utf8_decode($row[des_car]),'Banco/Cuenta: '.$row[des_ban] .$row2[des_ban].'- '. $fila[cuentacob]));
    	$this->Ln(1);
    				
    	$query="SELECT mn.codcon, c.descrip, mn.tipcon, mn.monto, mn.valor, pe.apenom,c.formula, c.ccosto,mn.numpre
            FROM nom_movimientos_nomina as mn
            LEFT join nompersonal as pe on mn.ficha = pe.ficha
            LEFT join nomconceptos as c on c.codcon = mn.codcon
            where pe.ficha = '$registro_id' and pe.tipnom =".$_SESSION['codigo_nomina']." and mn.codnom= '".$nomina_id."' and mn.tipnom='".$codtp."' and mn.tipcon<>'P' AND (pe.estado NOT LIKE '%Egresado%' AND pe.estado  NOT LIKE '%De Baja%')
         order by pe.apenom, mn.tipcon";

    	$this->Cell(1,1,utf8_decode('-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------'),0,0,'L');
        $this->Ln(1);

            $result =query($query,$conexion);
        $this->Cell(80,4,utf8_decode('Código y Descripción de Concepto'),0,0,'L');
        $this->Cell(25,4,utf8_decode('Ref'),0,0,'C');
        $this->Cell(31,4,utf8_decode('Asignación'),0,0,'R');
        $this->Cell(30,4,utf8_decode('Deducción'),0,0,'R');
        $this->Cell(25,4,utf8_decode('Saldo P.'),0,0,'R');  
        $this->Ln(3);

        $this->Cell(1,4,utf8_decode('-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------'),0,0,'L');
        $this->Ln(2);

    	

    	$sub_total_asig=0;
    	$sub_total_dedu=0;
        $cont=0;
    	while ($row = mysqli_fetch_array($result))
    	{
                $saldo=0;
                $saldo = number_format($saldo,2,'.',',');
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
                        $consulta = "SELECT salfinal as total 
                        FROM nomprestamos_detalles as pd 
                        inner join nomprestamos_cabecera as pc on (pd.numpre=pc.numpre) 
                        WHERE pd.ficha='".$fila[ficha]."' and pd.fechaven between '".$fetch5['periodo_ini']."' and '".$fetch5['periodo_fin']."' and pd.estadopre in ('Pendiente' , 'Cancelada')
                        and pc.codigopr='".$row[ccosto]."' AND pc.numpre = '".$row[numpre]."'";
                        $resultsal=query($consulta,$conexion);
                        $rowsal = fetch_array($resultsal);
                        $saldo = $rowsal[total];
                       // $saldo=0;
                        $saldo = number_format($saldo,2,'.',',');
                    }
                }
                $this->SetFont('Arial','',9);
                $this->SetWidths(array(80,25,31,30,25));
                $this->SetAligns(array('L','C','R','R','R'));
                $this->Setceldas(array(0,0,0,0,0));
                $this->Setancho(array(4,4,4,4,4));
                $this->Row(array($row[codcon] . ' - ' . utf8_decode($row[descrip]),$row[valor],$asig,$dedu,$saldo));
                $asig='';
                $dedu='';
    		
    	}
    	
    	$this->Cell(105,4,'Sub-Totales: ',0,0,'R');
    	$this->Cell(31,4,number_format($sub_total_asig,2,',','.'),'T',0,'R');
    	$this->Cell(30,4,number_format($sub_total_dedu,2,',','.'),'T',1,'R');
    	//	$this->Ln(1);
    	$this->Cell(120,4,'Neto a Depositar : ',0,0,'R');
    	$this->Cell(46,4,number_format($sub_total_asig-$sub_total_dedu,2,',','.'),0,1,'R');
            //$this->MultiCell(188,5,'Observaciones: '.$observacion,0);
            //$this->Ln(3);
            //$this->Cell(188,1,'','TB',1);
    	$this->Ln(1);
    	$this->Cell(44,4,'RECIBE CONFORME',0,0,'C');
    	$this->Cell(40,4,'','B',0,'C');	
    	$this->Cell(44,4,'FECHA',0,0,'C');
            $this->Cell(40,4,'','B',0,'C');
        $this->Ln(420);
        }
        else{
            $this->AddPage('P');
        }

        }
                  
     

    			

    	
    }





}
ob_clean();

//Creación del objeto de la clase heredada
// $pdf = new PDF('L', 'mm', array(215,139));
$pdf=new PDF('P','mm','LETTER');
$pdf->SetMargins(5,5,5,5);

$pdf->AddFont('Sanserif','','sanserif.php');
//$pdf->AddFont('SanserifB','','TEACPSSB.php');
$pdf->AliasNbPages();
$pdf->AddPage('P','LETTER');
$pdf-> SetAutoPageBreak(1 ,1);
//$pdf->SetFont('Sanserif','',9);
$pdf->SetFont('Arial','',9);
$pdf->ficha_buscar = $ficha_buscar;
$pdf->personas($nomina_id,$codtp);
$pdf->Output();
?>
