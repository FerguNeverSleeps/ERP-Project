<?php

//session_start();
//error_reporting(0);
//ini_set('display_errors', FALSE);
//ini_set('display_startup_errors', FALSE);
//date_default_timezone_set('America/Panama');


//include('../../lib/common.php');

function formato_fecha($fecha,$formato){
    if (empty($fecha))
    {
    	$fecha = date('Y-m-d');
    }
    $meses = array("ENERO","FEBRERO","MARZO","ABRIL","MAYO","JUNIO","JULIO","AGOSTO","SEPTIEMBRE","OCTUBRE","NOVIEMBRE","DICIEMBRE");
    $separa = explode("-",$fecha);
    $dia = $separa[2];
    $mes = $separa[1];
    $anio = $separa[0];
    switch ($formato)
    {
        case 1:
            $f = $dia." de ".$meses[$mes-1]." de ".$anio;
            break;
        case 2:
            $f = $dia." del mes ".$meses[$mes-1]." de ".$anio;
            break;
        default:
                case 3:
            $f = $dia."/".$mes."/".$anio;
            break;
        default:
            break;
    }
    return $f;
}

function comprobante_contable_planilla($codnom,$codtip){  
	$RETORNO=[];
	$conexion= new bd($_SESSION['bd']);

	$sql = "SELECT UPPER(t.descrip) as descrip 
			FROM   nomtipos_nomina t
			WHERE  t.codtip=".$codtip;
	$res=$conexion->query($sql);

	if($fila=$res->fetch_array())
	{
		$NOMINA = $fila['descrip']; 
	}	

	$sql = "SELECT e.nom_emp as empresa, e.rif, e.tel_emp as telefono, e.dir_emp as direccion, e.ciu_emp as ciudad, 
				e.edo_emp, e.imagen_izq as logo
			FROM   nomempresa e";
	$res=$conexion->query($sql);
	$fila=$res->fetch_array();
	$logo=$fila['logo'];

	$sql2 = "SELECT np.periodo_ini as desde, np.periodo_fin as hasta, np.frecuencia, np.fechapago as fechapago, np.mes, np.anio,
			DATE_FORMAT(np.periodo_ini,'%d') as dia_ini, DATE_FORMAT(np.periodo_fin, '%d') as dia_fin, np.descrip, F.descrip frecuencia_descripcion
                FROM nom_nominas_pago np 
                    left join nomfrecuencias F on F.codfre=np.frecuencia
                WHERE  np.codnom=".$codnom." AND np.tipnom=".$codtip;
	$res2=$conexion->query($sql2);
	$fila2=$res2->fetch_array();

	$meses = array('Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre');

	$desde=$fila2['desde'];
	$hasta=$fila2['hasta'];
        
    $desde1=formato_fecha($fila2['desde'],1);
	$hasta1=formato_fecha($fila2['hasta'],1);
    $fechapago=formato_fecha($fila2['fechapago'],3);
    $dia_ini = $fila2['dia_ini'];
	$frecuencia = $fila2['frecuencia'];
    $frecuencia_descripcion=$fila2["frecuencia_descripcion"];
	$dia_fin = $fila2['dia_fin']; 
	$mes_numero = $fila2['mes'];
	$mes_letras = $meses[$mes_numero - 1];
	$anio = $fila2['anio'];

	$empresa = $fila['empresa'];

	$RETORNO["empresa"]=strtoupper(utf8_encode($empresa) );
	$RETORNO["descripcion"]=strtoupper("(AMX A)-{$anio}/{$mes_letras}-{$codnom}-".$fila2['descrip']);
	$RETORNO["rango_fecha"]='DEL '. $desde1 .' AL '. $hasta1;
	$RETORNO["fecha_pago"]=$fila2['fechapago'];
	$RETORNO["ffecha_pago"]=$fechapago;
	$RETORNO["detalle"]=[];
	
        
    $sql = "SELECT DISTINCT np.codnivel1, nn.descrip as departamento , nn.ee as cuenta_contablex, nn.markar
    FROM   nompersonal np
    INNER JOIN nomnivel1 nn ON (nn.codorg=np.codnivel1)
    ORDER BY np.codnivel1";
    
    $res1=$conexion->query($sql);
    $i=7; 
    $ini=$i; $enc=false;$cont=0;
    $salario=$neto=0;

    $linea=0;
    while($row1=$res1->fetch_array())
    {	
        $totalA=$totalD=0;
        $asignaciones=$deducciones=0;
        $codnivel1=$row1['codnivel1'];
        $departamento=$row1['departamento'];
        $markar=$row1['markar'];
       

        $i++;
        //$objPHPExcel->getActiveSheet()->getStyle('A8:V8')->getAlignment()->setShrinkToFit(true);


        $bandera=0;
        $total_salarios=$total_comision=$total_combustible=$total_reembolso=$total_uso_auto=$total_bono="=";	

                $consulta="SELECT SUM(nm.monto) AS suma, nm.codcon, nm.descrip, nm.tipcon, c.ctacon, cc.id cuenta_id, cc.Descrip as cuenta_contable  "
                        . "FROM nom_movimientos_nomina AS nm "
                        . "LEFT JOIN nomconceptos AS c ON (nm.codcon=c.codcon) "
                        . "LEFT JOIN cwconcue AS cc ON (c.ctacon=cc.Cuenta) "
                        . "WHERE nm.codnom='".$codnom."' AND nm.tipnom='".$codtip."' AND nm.codnivel1='".$codnivel1."' AND nm.monto>0 "
                        . "GROUP BY nm.codcon "
                        . "ORDER BY nm.codcon";
//		echo $consulta;
//                exit;
                $res2=$conexion->query($consulta);
                $i++;
                $ini=$i; $enc=false;$cont=0;
                $salario=$neto=0;
                
                $j=0;
                while($row2=$res2->fetch_array())
                {	
                        $enc=true;
                        $codcon = $row2['codcon'];
                        $descrip = utf8_decode($row2['descrip']);
                        $tipcon = $row2['tipcon'];
                        $ctacon = $row2['ctacon'];
                        $cuenta_contable = $row2['cuenta_contable'];
                        $cuenta_id = $row2['cuenta_id'];
                        $suma = $row2['suma'];
//                            if($codcon==100)
//                            {
//                                $salario=$suma;
//                                $bandera=1;
//                                $pos=$i;
//                            }
                        if($codcon==100 || $codcon==169 || $codcon==103 || $codcon==177 || $codcon==178 )
                        {
                            $salario=$salario+$suma;
                            if($j==0)
                            {
                                $bandera=1;
                                $pos=$i;
                                $i++;
                                $j++;
                                $pos=$linea;
                                $linea++;

                            }
                        }
                            
                        if($codcon==188 || $codcon==190 || $codcon==198 || $codcon==199)
                            $salario=$salario-$suma;



                        if($codcon!=190 && $codcon!=198 && $codcon!=199 && $codcon!=169
                           && $codcon!=100 && $codcon!=103 && $codcon!=177 && $codcon!=178 && $codcon!=188)
                        {
                        	$RETORNO["detalle"][$linea]=[];
                            $RETORNO["detalle"][$linea]["markar"]=$markar;
                            $RETORNO["detalle"][$linea]["departamento"]=$departamento;
                            $RETORNO["detalle"][$linea]["departamento_id"]=$codnivel1;
                            $RETORNO["detalle"][$linea]["fecha_pago"]=$fechapago;
                            $RETORNO["detalle"][$linea]["cuenta"]=$ctacon;
                            $RETORNO["detalle"][$linea]["cuenta_descripcion"]=$cuenta_contable;
                            $RETORNO["detalle"][$linea]["cuenta_id"]=$cuenta_id;
                            $RETORNO["detalle"][$linea]["concepto"]=$codcon."-".utf8_encode($descrip);
                        }

                        if($tipcon=='A')
                        {
                            if($codcon!=100 && $codcon!=169 && $codcon!=103 && $codcon!=177 && $codcon!=178)
                            {
                                //$objPHPExcel->getActiveSheet()->setCellValue('E'.$i, $suma);
                                $RETORNO["detalle"][$linea]["debito"]=$suma;
                                
                            }
                            $totalA=$totalA+$suma;
                        }
                        if($tipcon=='D')
                        {
                            if($codcon!=188 && $codcon!=190 && $codcon!=198 && $codcon!=199)
                            {
                                //$objPHPExcel->getActiveSheet()->setCellValue('F'.$i, $suma);
                                $RETORNO["detalle"][$linea]["credito"]=$suma;
//                                     $deducciones=$deducciones+$suma;
                                 
                            }
                            $totalD=$totalD+$suma;
                                
                        }
                        if($tipcon=='P')
                        {

                                //$objPHPExcel->getActiveSheet()->setCellValue('E'.$i, $suma);                                
                        		$RETORNO["detalle"][$linea]["debito"]=$suma;

                                if($codcon==3000)
                                {
                                    $ctacon="21.04.02.01";
                                }
                                if($codcon==3001)
                                {
                                    $ctacon="21.04.02.01";
                                }
                                if($codcon==3002)
                                {
                                    $ctacon="21.04.02.01";
                                }
                                    
                                if($codcon==9005)
                                {
                                    $ctacon="21.04.04.01";
                                }
                                
                                if($codcon==9006)
                                {
                                    $ctacon="21.04.04.02";
                                }
                                
                                if($codcon==9007)
                                {
                                    $ctacon="21.04.02.06";
                                }
                                
                                if($codcon==9008)
                                {
                                    $ctacon="21.04.02.05";
                                }
                                
                                if($codcon==9009)
                                {
                                    $ctacon="21.04.02.01";
                                }
                                
                                $consulta_cuenta_con="SELECT nc.cta_contab_reserva as ctacon  "
                                            . "FROM nomconceptos AS nc  "
                                            . "WHERE nc.codcon='".$codcon."'";
//                    		echo $consulta_cuenta;
//                                    exit;
                                $res_cuenta_con=$conexion->query($consulta_cuenta_con);
                                $row_cuenta_con=$res_cuenta_con->fetch_array();                 
                                $ctacon = $row_cuenta_con['ctacon'];
                                
                                
                                $consulta_cuenta="SELECT cc.id cuenta_id, cc.Descrip as cuenta_contable  "
                                        . "FROM cwconcue AS cc  "
                                        . "WHERE cc.Cuenta='".$ctacon."'";
//                    		echo $consulta_cuenta;
//                                    exit;
                                $res_cuenta=$conexion->query($consulta_cuenta);
                                $row_cuenta=$res_cuenta->fetch_array();                 
                                $descrip_cuenta = utf8_decode($row_cuenta['cuenta_contable']);
                                $cuenta_id = $row_cuenta['cuenta_id'];
                                
                               
                                $i++;                                
                                

                                $linea++;
                                $RETORNO["detalle"][$linea]=[];
                                $RETORNO["detalle"][$linea]["markar"]=$markar;
                                $RETORNO["detalle"][$linea]["departamento"]=$departamento;
                                $RETORNO["detalle"][$linea]["departamento_id"]=$codnivel1;
                                $RETORNO["detalle"][$linea]["fecha_pago"]=$fechapago;
	                            $RETORNO["detalle"][$linea]["cuenta"]=$ctacon;
	                            $RETORNO["detalle"][$linea]["cuenta_descripcion"]=$descrip_cuenta;
                                $RETORNO["detalle"][$linea]["cuenta_id"]=$cuenta_id;
	                            $RETORNO["detalle"][$linea]["concepto"]=utf8_encode($descrip)." Por Pagar";
	                            $RETORNO["detalle"][$linea]["credito"]=$suma;

                                $totalD=$totalD+$suma;
                                $totalA=$totalA+$suma;
//                                    $asignaciones=$asignaciones+$suma;
//                                    $deducciones=$deducciones+$suma;
                        }                        


                        if($codcon!=190 && $codcon!=198 && $codcon!=199 && $codcon!=169
                           && $codcon!=100 && $codcon!=103 && $codcon!=177 && $codcon!=178 && $codcon!=188)
                        {
                            $i++;$cont++;
                            $linea++;
                        }
                }
                $neto=$totalA-$totalD;
//                    $totalA=$salario+$asignaciones;
//                    $totalD=$neto+$deducciones;
                if($bandera==1)
                {
                    $consulta_cuenta_salario="SELECT c.codcon as codcon_salario, c.descrip as descrip_salario, c.ctacon as ctacon_salario, "
                                        . "cc.id cuenta_id, cc.Descrip as cuenta_contable_salario  "
                                        . "FROM nomconceptos as c "
                                        . "LEFT JOIN cwconcue as cc ON (c.ctacon=cc.Cuenta) "
                                        . "WHERE c.codcon='100'";
//                    		echo $consulta_cuenta;
//                                    exit;
                    $res_cuenta_salario=$conexion->query($consulta_cuenta_salario);
                    $row_cuenta_salario=$res_cuenta_salario->fetch_array();                 
                    $cuenta_contable_salario = utf8_decode($row_cuenta_salario['cuenta_contable_salario']);
                    $ctacon_salario = $row_cuenta_salario['ctacon_salario'];
                    $codcon_salario = $row_cuenta_salario['codcon_salario'];
                    $descrip_salario = utf8_decode($row_cuenta_salario['descrip_salario']);
                    $cuenta_id = $row_cuenta_salario['cuenta_id'];
                    

                    $RETORNO["detalle"][$pos]=[];
                    $RETORNO["detalle"][$pos]["markar"]=$markar;
                    $RETORNO["detalle"][$pos]["departamento"]=$departamento;
                    $RETORNO["detalle"][$pos]["departamento_id"]=$codnivel1;
                    $RETORNO["detalle"][$pos]["fecha_pago"]=$fechapago;
                    $RETORNO["detalle"][$pos]["cuenta"]=$ctacon_salario;
                    $RETORNO["detalle"][$pos]["cuenta_descripcion"]=$cuenta_contable_salario;
                    $RETORNO["detalle"][$pos]["cuenta_id"]=$cuenta_id;
                    $RETORNO["detalle"][$pos]["concepto"]=$codcon_salario."-".utf8_encode($descrip_salario);
                    $RETORNO["detalle"][$pos]["debito"]=$salario;
                }
            
                if($enc)
                {      
                    
                    
                    if($_SESSION['bd']==="mmdngsa_rrhh")
                    {                       
                        $cuenta_contable="21240101";
                        $descripcion="Salarios Por Pagar";
                        if($frecuencia==16)
                        {
                            $cuenta_contable="2124.02.07";
                            $descripcion="Viaticos por Pagar";
                        }
                    }

                    if($_SESSION['bd']==="masmedan_planilla")
                    {
                        $cuenta_contable="21.24";
                        $descripcion="Salarios Por Pagar";
                        if($frecuencia==16)
                        {
                            $cuenta_contable="21.04.02.07";
                            $descripcion="Viaticos por Pagar";
                        }
                    }
                    
                    if($_SESSION['bd']==="ammdpa_rrhh")
                    {
                        $cuenta_contable="213.01";
                        $descripcion="Salarios Por Pagar";
                        if($frecuencia==16)
                        {
                            $cuenta_contable="21.04.02.07";
                            $descripcion="Viaticos por Pagar";
                        }
                    }
                    
                   
                        
                        $consulta_cuenta="SELECT cc.id cuenta_id, cc.Descrip as cuenta_contable  "
                                        . "FROM cwconcue AS cc  "
                                        . "WHERE cc.Cuenta='".$cuenta_contable."'";
//                          echo $consulta_cuenta;
//                                    exit;
                        $res_cuenta=$conexion->query($consulta_cuenta);
                        $row_cuenta=$res_cuenta->fetch_array();                 
                        $descrip_cuenta = utf8_decode($row_cuenta['cuenta_contable']);
                        $cuenta_id = $row_cuenta['cuenta_id'];

                	//print "\n<br>entro $departamento $linea ($neto). ";
                        
                    	$RETORNO["detalle"][$linea]=[];
                    	$RETORNO["detalle"][$linea]["markar"]=$markar;
                        $RETORNO["detalle"][$linea]["departamento"]=$departamento;  
                        $RETORNO["detalle"][$linea]["departamento_id"]=$codnivel1;
                        $RETORNO["detalle"][$linea]["fecha_pago"]=$fechapago;
	                    $RETORNO["detalle"][$linea]["cuenta"]=$cuenta_contable;
	                    $RETORNO["detalle"][$linea]["cuenta_descripcion"]=$descripcion;
                        $RETORNO["detalle"][$linea]["cuenta_id"]=$cuenta_id;
	                    $RETORNO["detalle"][$linea]["concepto"]="Total Salarios Por Pagar";
	                    $RETORNO["detalle"][$linea]["credito"]=$neto;


                        $i++;
                        
                       	//$linea++;

                        $i++;

                        $i++;	
                        $nivel++;
                }

        $i++;
        $linea++;
    }     

    ksort($RETORNO["detalle"]);

    return $RETORNO;
}

function comprobante_contable_pdf($data){
	include('../../fpdf/fpdf.php');

    $pdf=new FPDF();
    $pdf->AddPage('P','Letter');
    $pdf->SetFont('Helvetica','',9);
    $pdf->setFillColor(255,255,255);

    $pdf->SetFont('Helvetica','B',7.5);
    $pdf->Cell(190,4,utf8_decode($data["empresa"]),0,1,'L');
    $pdf->Cell(190,4,utf8_decode($data["descripcion"]),0,1,'L');


    $T=[
    	20,
    	25,
    	55,
    	55,
    	20,
    	20
    ];
    $total_general_debito=0;
    $total_general_credito=0;
    $centro_costo=" ";
    foreach($data["detalle"] as $i => $value){
		if($data["detalle"][$i]["markar"]!=$centro_costo){
    		$pdf->SetFont('Helvetica','B',7.5);

			if($i!=0){
				$pdf->Cell($T[0]+$T[1]+$T[2]+$T[3],4,"TOTAL",1,0,'R',1);
				$pdf->Cell($T[4],4,number_format($total_debito,2,",","."),1,0,'R',1);
	    		$pdf->Cell($T[5],4,number_format($total_credito,2,",","."),1,1,'R',1);				
			}

			$centro_costo=$data["detalle"][$i]["markar"];

    		$pdf->Ln(5);
			$pdf->Cell(190,4,utf8_decode($centro_costo),0,1,'L');

			$pdf->setFillColor(235,235,235);
    		$pdf->Cell($T[0],4,"FECHA",1,0,'C',1);
    		$pdf->Cell($T[1],4,"CUENTA",1,0,'C',1);
    		$pdf->Cell($T[2],4,"NOMBRE CUENTA",1,0,'C',1);
    		$pdf->Cell($T[3],4,"CONCEPTO",1,0,'C',1);
    		$pdf->Cell($T[4],4,"DEBITO",1,0,'C',1);
    		$pdf->Cell($T[5],4,"CREDITO",1,1,'C',1);

    		$total_debito=0;
    		$total_credito=0;
		}
		$pdf->setFillColor(255,255,255);
		$pdf->SetFont('Helvetica','',7.5);
		$pdf->Cell($T[0],4,utf8_decode($data["detalle"][$i]["fecha_pago"]),1,0,'C',1);
		$pdf->Cell($T[1],4,utf8_decode($data["detalle"][$i]["cuenta"]),1,0,'C',1);
		$pdf->Cell($T[2],4,utf8_decode($data["detalle"][$i]["cuenta_descripcion"]),1,0,'L',1);
		$pdf->Cell($T[3],4,utf8_decode($data["detalle"][$i]["concepto"]),1,0,'L',1);
		$pdf->Cell($T[4],4,((isset($data["detalle"][$i]["debito"]) and $data["detalle"][$i]["debito"])?number_format($data["detalle"][$i]["debito"],2,",","."):""),1,0,'R',1);
		$pdf->Cell($T[5],4,((isset($data["detalle"][$i]["credito"]) and $data["detalle"][$i]["credito"])?number_format($data["detalle"][$i]["credito"],2,",","."):""),1,1,'R',1);

		$total_debito+=$data["detalle"][$i]["debito"];
    	$total_credito+=$data["detalle"][$i]["credito"];

    	$total_general_debito+=$data["detalle"][$i]["debito"];
    	$total_general_credito+=$data["detalle"][$i]["credito"];
    }

    $pdf->SetFont('Helvetica','B',7.5);
    $pdf->Cell($T[0]+$T[1]+$T[2]+$T[3],4,"TOTAL",1,0,'R',1);
	$pdf->Cell($T[4],4,number_format($total_debito,2,",","."),1,0,'R',1);
	$pdf->Cell($T[5],4,number_format($total_credito,2,",","."),1,1,'R',1);

	$pdf->Ln(3);
    $pdf->SetFont('Helvetica','B',7.5);
    $pdf->Cell($T[0]+$T[1]+$T[2]+$T[3],4,"TOTAL GENERAL",1,0,'R',1);
	$pdf->Cell($T[4],4,number_format($total_general_debito,2,",","."),1,0,'R',1);
	$pdf->Cell($T[5],4,number_format($total_general_credito,2,",","."),1,1,'R',1);

	if(!file_exists("tmp/")){
		mkdir("tmp",0777,true);
	}
	$archivo="tmp/".uniqid().".pdf";
    $pdf->Output("$archivo","F");
    return $archivo;
}


function comprobante_contable_ach($codnom,$codtip){  
    $RETORNO=[];
    $conexion= new bd($_SESSION['bd']);

    $sql = "SELECT UPPER(t.descrip) as descrip 
            FROM   nomtipos_nomina t
            WHERE  t.codtip=".$codtip;
    $res=$conexion->query($sql);

    if($fila=$res->fetch_array())
    {
        $NOMINA = $fila['descrip']; 
    }

        $sql = "SELECT e.nom_emp as empresa, e.rif, e.tel_emp as telefono, e.dir_emp as direccion, e.ciu_emp as ciudad, 
                e.edo_emp, e.imagen_izq as logo
            FROM   nomempresa e";
    $res=$conexion->query($sql);
    $fila=$res->fetch_array();
    $logo=$fila['logo'];

    $sql2 = "SELECT np.periodo_ini as desde, np.periodo_fin as hasta, np.frecuencia, np.fechapago as fechapago, np.mes, np.anio,
            DATE_FORMAT(np.periodo_ini,'%d') as dia_ini, DATE_FORMAT(np.periodo_fin, '%d') as dia_fin, np.descrip, F.descrip frecuencia_descripcion
                FROM nom_nominas_pago np 
                    left join nomfrecuencias F on F.codfre=np.frecuencia
                WHERE  np.codnom=".$codnom." AND np.tipnom=".$codtip;
    $res2=$conexion->query($sql2);
    $fila2=$res2->fetch_array();

    $meses = array('Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre');

    $desde=$fila2['desde'];
    $hasta=$fila2['hasta'];
        
    $desde1=formato_fecha($fila2['desde'],1);
    $hasta1=formato_fecha($fila2['hasta'],1);
    $fechapago=formato_fecha($fila2['fechapago'],3);
    $dia_ini = $fila2['dia_ini'];
    $frecuencia = $fila2['frecuencia'];
    $frecuencia_descripcion = $fila2['frecuencia_descripcion'];
    $dia_fin = $fila2['dia_fin']; 
    $mes_numero = $fila2['mes'];
    $mes_letras = $meses[$mes_numero - 1];
    $anio = $fila2['anio'];

    $empresa = $fila['empresa'];

    $RETORNO["empresa"]=strtoupper(utf8_encode($empresa) );
    $RETORNO["descripcion"]=strtoupper("(AMX A)-ACH-{$anio}/{$mes_letras}-{$codnom}-".$fila2['descrip']);
    $RETORNO["rango_fecha"]='DEL '. $desde1 .' AL '. $hasta1;
    $RETORNO["fecha_pago"]=$fila2['fechapago'];
    $RETORNO["ffecha_pago"]=$fechapago;
    $RETORNO["detalle"]=[];



    $consulta="SELECT SUM(nnn.neto) AS neto  "
                . "FROM nom_nomina_netos AS nnn "
                . "WHERE nnn.codnom='".$codnom."' AND nnn.tipnom='".$codtip."' AND nnn.neto>0 ";

    $res2=$conexion->query($consulta);
    $row2=$res2->fetch_array();
    $neto = $row2['neto'];
    
    if($_SESSION['bd']==="mmdngsa_rrhh")
    {                       
        $cuenta_contable_salario="21240101";
        $descripcion_salario="Salarios Por Pagar";
        $concepto_salario="Salarios Por Pagar";
        
        if($frecuencia==16)
        {                       
            $cuenta_contable_salario="2124.02.07";
            $descripcion_salario="Viaticos Por Pagar";
            $concepto_salario="Viaticos Por Pagar";
        }
                        
        $cuenta_contable_banco="112012";
        $descripcion_banco="Banco General-Corriente# 2545";
        $concepto_banco="Banco";
    }
    
    if($_SESSION['bd']==="masmedan_planilla")
    {
        $cuenta_contable_salario="21.24";
        $descripcion_salario="Salarios Por Pagar";
        $concepto_salario="Salarios Por Pagar";
        
        if($frecuencia==16)
        {                       
            $cuenta_contable_salario="21.04.02.07";
            $descripcion_salario="Viaticos Por Pagar";
            $concepto_salario="Viaticos Por Pagar";
        }
        
        $cuenta_contable_banco="11.2.01";
        $descripcion_banco="Banco General-Corriente# 2701";
        $concepto_banco="Banco";
    }
    
    if($_SESSION['bd']==="ammdpa_rrhh")
    {
        $cuenta_contable_salario="213.01";
        $descripcion_salario="Salarios Por Pagar";
        $concepto_salario="Salarios Por Pagar";
        
        if($frecuencia==16)
        {                       
            $cuenta_contable_salario="21.04.02.07";
            $descripcion_salario="Viaticos Por Pagar";
            $concepto_salario="Viaticos Por Pagar";
        }
        
        $cuenta_contable_banco="11.2.01";
        $descripcion_banco="Banco General-Corriente# 2701";
        $concepto_banco="Banco";
    } 



    $consulta_cuenta="SELECT cc.id cuenta_id, cc.Descrip as cuenta_contable  "
                                        . "FROM cwconcue AS cc  "
                                        . "WHERE cc.Cuenta='".$cuenta_contable_salario."'";
    $res_cuenta=$conexion->query($consulta_cuenta);
    $row_cuenta=$res_cuenta->fetch_array();    
    $cuenta_id = $row_cuenta['cuenta_id'];

    $pos=0;    
    $RETORNO["detalle"][$pos]=[];
    $RETORNO["detalle"][$pos]["markar"]="";
    $RETORNO["detalle"][$pos]["departamento"]="";
    $RETORNO["detalle"][$pos]["departamento_id"]="";
    $RETORNO["detalle"][$pos]["fecha_pago"]=$fechapago;
    $RETORNO["detalle"][$pos]["cuenta"]=$cuenta_contable_salario;
    $RETORNO["detalle"][$pos]["cuenta_descripcion"]=$descripcion_salario;
    $RETORNO["detalle"][$pos]["cuenta_id"]=$cuenta_id;
    $RETORNO["detalle"][$pos]["concepto"]=$concepto_salario;
    $RETORNO["detalle"][$pos]["debito"]=$neto;




    $consulta_cuenta="SELECT cc.id cuenta_id, cc.Descrip as cuenta_contable  "
                                        . "FROM cwconcue AS cc  "
                                        . "WHERE cc.Cuenta='".$cuenta_contable_banco."'";
    $res_cuenta=$conexion->query($consulta_cuenta);
    $row_cuenta=$res_cuenta->fetch_array();    
    $cuenta_id = $row_cuenta['cuenta_id'];

    $pos++;
    $RETORNO["detalle"][$pos]=[];
    $RETORNO["detalle"][$pos]["markar"]="";
    $RETORNO["detalle"][$pos]["departamento"]="";
    $RETORNO["detalle"][$pos]["departamento_id"]="";
    $RETORNO["detalle"][$pos]["fecha_pago"]=$fechapago;
    $RETORNO["detalle"][$pos]["cuenta"]=$cuenta_contable_banco;
    $RETORNO["detalle"][$pos]["cuenta_descripcion"]=$descripcion_banco;
    $RETORNO["detalle"][$pos]["cuenta_id"]=$cuenta_id;
    $RETORNO["detalle"][$pos]["concepto"]=$concepto_banco;
    $RETORNO["detalle"][$pos]["credito"]=$neto;

    return $RETORNO;
}


//print_r(comprobante_contable(46,1));
//print_r(comprobante_contable_planilla(16,1));
//$tmp=comprobante_contable_ach(16,1);
//print_r($tmp);
//print comprobante_contable_pdf($tmp);

?>