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

    var $asignaciones;
    var $asignaciones_hextra;
    var $deducciones;
    var $descuentos;


    var $aausencia;
    var $aincapacidades;
    var $atardanza;
    var $aperm_rem;


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
    public function chequearAsignacion( $concepto, $monto ){
    	$conceptos = [90, 91, 92, 93, 94, 95, 96, 97, 98, 99, 100, 101, 102, 104, 114, 115, 119, 120, 121,  141, 145, 147, 148, 156, 157];
    	return in_array($concepto, $conceptos);
    }
    public function chequearAsignacionHextra( $concepto, $monto ){
    	$conceptos = [105, 106, 107, 108, 109, 110, 111, 112, 113, 116, 117, 122, 123, 124, 125, 126, 127, 128, 129, 130, 131, 132, 133, 134, 135, 136, 137, 138, 139, 142, 143, 144, 149, 150, 151, 152, 153, 154, 155];
    	return in_array($concepto, $conceptos);
    }
    public function chequearDeducciones($concepto){
        $conceptos = [200,201,202,203,204,205,206,207,208,209,210,211,212,213,214,215,216];
        return in_array($concepto, $conceptos);
    }
    public function chequearAusencias($concepto){
    	$conceptos = [190,198];
    	return in_array($concepto, $conceptos);

    }
    public function chequearPermisosRem($concepto){
    	$conceptos = [195,196];
    	return in_array($concepto, $conceptos);
    }
    public function chequearTardanzas($concepto){
    	$conceptos = [195,199];
    	return in_array($concepto, $conceptos);

    }
    public function chequearIncapacidades($concepto){
    	$conceptos = [140];
    	return in_array($concepto, $conceptos);
    	
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
    	
        $query="SELECT pe.tipnom AS tipnom,pe.foto AS foto,pe.fecing AS fec_ing,pe.cedula AS cedula,pe.ficha AS ficha,pe.apenom AS apenom,pe.suesal AS sueldopro,pe.codnivel1 AS codnivel1,pe.codnivel2 AS codnivel2,pe.codnivel3 AS codnivel3,car.des_car AS cargo , pe.clave_ir
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


            $query_rata = "SELECT valor, monto 
            FROM nom_movimientos_nomina 
            WHERE codnom='{$nomina_id}' 
            AND codcon = '100' 
            AND ficha = '".$fila["ficha"]."'
            AND tipnom='".$_SESSION['codigo_nomina']."' ;";
            $res_rata = query($query_rata,$conexion);
            $row_rata = fetch_array($res_rata);

            $rata_x_hora = ($row_rata["valor"] != 0 ) ? round($row_rata["monto"] / $row_rata["valor"] , 3) : 0;
            $clave_ir = ($fila["clave_ir"] != "") ? $fila["clave_ir"] : "A0";
            $n = 2;
            $i=0;
	        for ($i=0; $i < $n; $i++) { 
        

                 $val = ($i==0) ? $this->SetY(6) : $this->SetY(146);

                //$this->Cell(70,3,utf8_decode($var_encabezado),0,0,'L');
                $this->Cell(100,6,'QUINCENA DEL '.date('Y/m/d',strtotime($fetch5["periodo_ini"])).' AL '.date('Y/m/d',strtotime($fetch5["periodo_fin"])) ,0,0,'L');
                //$this->Cell(50,3,'RECIBO DE PAGO'.$ANIO,0,0,'C');
                $this->Cell(95,6,utf8_decode('Empleado N°:  '.$fila["ficha"]),0,0,'L');
                 if($i==0) { $this->SetY(12); }else{ $this->SetY(152);}
                $this->Cell(100,6,utf8_decode('Nombre: '.$fila["apenom"]),0,0,'L');
                $this->Cell(95,6,utf8_decode('Cédula: '.$fila["cedula"]),0,0,'L');

                 if($i==0) { $this->SetY(18); }else{ $this->SetY(158);}
                $this->Cell(100,6,utf8_decode('Clave ISR: '.$fila["clave_ir"]),0,0,'L');
                $this->Cell(95,6,utf8_decode('Rata x Hora: '.$rata_x_hora),0,1,'L');
                //$this->Cell(150,3,$_SESSION["nomina"]." - ".$fetch5["frecuencia"],0,0,'L');
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

                 if($i==0) { $this->SetY(25); }else{ $this->SetY(165);}

                $query="SELECT cod_car, descrip_corto AS des_car from nomcargos where cod_car='".$fila[codcargo]."'";
                $result=query($query,$conexion);
                $row = mysqli_fetch_array($result);
                $this->SetFont('Arial','',9);
                $this->SetAligns(array('L','L','L'));
                $this->Setceldas(array(0,0,0));
                $this->Setancho(array(6,6,6));
                $this->SetWidths(array(60,80,60));
                //$this->Row(array('Cargo: '.utf8_decode($row[des_car]),'Periodo del: '.fecha($fetch5['periodo_ini']).' al: '.fecha($fetch5['periodo_fin']),'Banco/Cuenta: '.$row[des_ban] .$row2[des_ban].'- '. $fila[cuentacob]));
                $this->Row(array('CARG: '.utf8_decode($row[des_car]),'PERIODO DEL: '.fecha($fetch5['periodo_ini']).' AL: '.fecha($fetch5['periodo_fin']),'CUENTA: '. $fila[cuentacob]));
                $this->SetWidths(array(10,10));
                $this->ln();
                
                 if($i==0) { $this->SetY(36); }else{ $this->SetY(171);}
                $this->Cell(30,6,utf8_decode('Concepto'),'',0,'C');
                $this->Cell(30,6,utf8_decode('Horas'),'',0,'C');
                $this->Cell(30,6,utf8_decode('Monto'),'',0,'C');
                $this->Cell(10,6,utf8_decode(''),'',0,'C');
                $this->Cell(30,6,utf8_decode('Deducciones'),'',0,'C');
                $this->Cell(30,6,utf8_decode(''),'',0,'C');
                $this->Cell(30,6,utf8_decode('Monto'),'',1,'C');
                
                $query="SELECT mn.codcon, c.descrip, mn.tipcon, mn.monto, mn.valor, c.descripexcel AS dcorta, c.ccosto as pr
                FROM nom_movimientos_nomina as mn
                LEFT join nompersonal as pe on mn.ficha = pe.ficha
                LEFT join nomconceptos as c on c.codcon = mn.codcon
                where pe.ficha = '$registro_id' and pe.tipnom =".$_SESSION['codigo_nomina']." and mn.codnom= '".$nomina_id."' and mn.tipnom='".$codtp."' and mn.tipcon<>'P' AND ( pe.estado  NOT LIKE '%De Baja%')
            	group by pe.apenom,pe.ficha,c.codcon order by pe.apenom, mn.tipcon";
                $result =query($query,$conexion);
            
                $sub_total_asig=0;
                $sub_total_dedu=0;
                $cadAsig = '';
                $cadDedu = '';
                $xx = 20;
                $deducciones = $asignaciones = $asignaciones_hextra = $descuentos = [];
				$this->asignaciones_hextra = $this->asignaciones = $this->ausencia = $this->tardanza = $this->perm_rem = $this->incapacidades =  0;
                while ($row = mysqli_fetch_array($result))
                {
                    //PRIMERA PARTE
                    $saldo=0;
                    if ($row[tipcon]=='A')
                    {
                    	if($this->chequearAsignacion( $row[codcon] ))
                    	{
                    		$asignaciones[] = $row;
    						$this->asignaciones += $row[monto] ;

                    	}
                    	if ($this->chequearAsignacionHextra( $row[codcon] )) {
                    		$asignaciones_hextra[] = $row;
    						$this->asignaciones_hextra += $row[monto] ;
                    	}
                        $asig= number_format($row[monto],2,',','.');
                        $sub_total_asig=$row[monto]+$sub_total_asig;
                    }
                    if ($row[tipcon]=='D')
                    {
                        $sub_total_dedu=$row[monto]+$sub_total_dedu;
                        if(($row[codcon]>=500)&&($row[codcon]<=599))
                        {
                            /*echo $consulta = "SELECT SUM(salfinal) as total FROM nomprestamos_detalles as pd inner join nomprestamos_cabecera as pc on (pd.numpre=pc.numpre) WHERE pd.ficha='".$fila[ficha]."' and pd.fechaven between '".$fetch5['periodo_ini']."' and '".$fetch5['periodo_fin']."' and pd.estadopre='Pendiente' and pc.codigopr='".number_format($row[pr],0,'','')."'";exit;
                            $resultsal=query($consulta,$conexion);
                            $rowsal = fetch_array($resultsal);*/
                            $descuento = array("monto" => $row[monto], "concepto" => $row[codcon], "descripcion" => utf8_decode($row[descrip]) );
                            array_push($descuentos, $descuento);
                            /*$saldo = $rowsal[total];
                            $saldo = number_format($saldo,2,'.',',');*/
                        }
                        else{
                            if ($this->chequearDeducciones( $row[codcon] )) {
                                $deducciones[] = $row;
                            }

                            if ($this->chequearAusencias( $row[codcon] )) {
                                $this->ausencia += $row[monto] ;
                            }
                            if ($this->chequearIncapacidades( $row[codcon] )) {
                                $this->incapacidades += $row[monto] ;
                            }
                            if ($this->chequearTardanzas( $row[codcon] )) {
                                $this->tardanza += $row[monto] ;
                            }
                            if ($this->chequearPermisosRem( $row[codcon] )) {
                                $this->perm_rem += $row[monto] ;
                            }

                        }
                    }                
                }
                $cantAsig = 0;
                if($i==0) { $xx = 42; }else{ $xx = 177; }
                foreach ($asignaciones as $key => $value) {
                    $asig = number_format($value[monto],2,',','.');
                    $this->SetXY(5,$xx+$cantAsig);
                    $this->Cell(5,6," ",0,0,'L');
                    $this->Cell(30,6,$value[codcon]." - ".utf8_decode($value[dcorta]),0,0,'L');
                    $this->Cell(30,6,$value[valor]." ",0,0,'L');
                    $this->Cell(15,6,$asig,0,1,'R');
                    $cantAsig+=3;
                }
                $cantDeduc = 0;
                if($i==0) { $xx = 42; }else{ $xx = 177; }
                foreach ($deducciones as $key => $value) {
                    $dedu = number_format($value[monto],2,',','.');
                    $this->SetXY(110,$xx+$cantDeduc);
                    $this->Cell(65,6,$value[codcon]."-".utf8_decode($value[dcorta]).": ",0,0,'L');
                    $this->Cell(15,6,$dedu,0,1,'R');
                    $cantDeduc+=3;
                }
                $cantDesc = 0;
                if($i==0) { $xx = 76; }else{ $xx = 206; }
                foreach ($descuentos as $key => $value) {
                    $desc = number_format($value[monto],2,',','.');
                    $this->SetXY(110,$xx+$cantDesc);
                    $this->Cell(65,6,$value[concepto]."-".utf8_decode($value[descripcion]).": ",0,0,'L');
                    $this->Cell(15,6,$desc,0,1,'R');
                    $cantDesc+=3;
                }
                $this->ln();

                if($i==0) { $this->SetY(70); }else{ $this->SetY(200);}

                $this->Cell(50,6,utf8_decode('Total Extra'),'',0,'L');
                $this->Cell(40,6,number_format($this->asignaciones_hextra,2,'.',','),'',0,'R');

                $this->Cell(15,6,'','',0,'L');
                //$this->SetXY(110,$xx+$cantDeduc);
                $this->Cell(50,6,utf8_decode('Descuentos'),'',0,'L');
                $this->Cell(50,6,'','',0,'L');



                if($i==0) { $this->SetY(76); }else{ $this->SetY(206);}

                $this->Cell(50,6,utf8_decode('Ausencia'),'',0,'L');
                $this->Cell(40,6,number_format($this->ausencia,2,'.',','),'',0,'R');

                $this->Cell(15,6,'','',0,'L');
                //$this->SetXY(110,$xx+$cantDeduc);

                if($i==0) { $this->SetY(82); }else{ $this->SetY(212);}

                $this->Cell(50,6,utf8_decode('Permisos Rem'),'',0,'L');
                $this->Cell(40,6,number_format($this->perm_rem,2,'.',','),'',0,'R');

                $this->Cell(15,6,'','',0,'L');

                if($i==0) { $this->SetY(88); }else{ $this->SetY(219);}

                $this->Cell(50,6,utf8_decode('Tardanzas'),'',0,'L');
                $this->Cell(40,6,number_format($this->tardanza,2,'.',','),'',0,'R');

                $this->Cell(15,6,'','',0,'L');

                if($i==0) { $this->SetY(94); }else{ $this->SetY(225);}

                $this->Cell(50,6,utf8_decode('Incapacidades'),'',0,'L');
                $this->Cell(40,6,number_format($this->incapacidades,2,'.',','),'',0,'R');

                $this->Cell(15,6,'','',0,'L');
                
                /*$this->Cell(70,6,utf8_decode($this->GetY()),'T',0,'C');
                $this->Cell(30,6,utf8_decode(''),'T',0,'C');
                $this->Cell(60,6,utf8_decode('CODIGO - DEDUCCIONES'),'T',0,'C');
                $this->Cell(35,6,utf8_decode(''),'T',1,'C');*/

                if ($i==0) {
                	$this->SetY(105);
                }else{
                	$this->SetY(240);
                } 
                
                $this->Cell(50,6,'Total Devengado ','',0,'L');
                $this->Cell(40,6,number_format($sub_total_asig,2,',','.'),' ',1,'R');

                $this->Cell(30,6,'',' ',0,'L');
                $this->Cell(35,6,'Neto a Pagar: ','',0,'R');
                $this->Cell(30,6,number_format($sub_total_asig-$sub_total_dedu,2,',','.'),' ',0,'L');
                $this->Cell(65,6,'Total Deducido.:   '.number_format($sub_total_dedu,2,',','.'),'',0,'R');

                $this->Ln(20);
                $this->Cell(35,6,'',0,0,'R');
                $this->Cell(45,6,'Recibe Conforme','T',0,'C');
                $this->Cell(45,6,'',0,0,'R');
                $this->Cell(45,6,utf8_decode('Cédula'),'T',0,'C');
                $this->Cell(20,6,'',0,1,'L');
                
                if($i == 0)
                {
                	$this->SetY(140);

                    $this->Cell(1,1,utf8_decode('--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------'),0,0,'L');
                }
                else
                {
                	$this->AddPage();
                }
                /*print_r($this->asignaciones_hextra);echo "<br>------<br>";
                print_r($asignaciones_hextra);echo "<br>";
                print_r($deducciones);echo "<br>";
                print_r($descuentos);echo "<br>";*/
            }//Fin For
		}//Fin While
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
