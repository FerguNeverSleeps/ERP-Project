<?php
session_start();
ob_start();
error_reporting(1);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('America/Panama');

if (PHP_SAPI == 'cli')
	die('Este ejemplo solo se debe ejecutar desde un navegador Web');

include('../lib/common.php');
include('../paginas/lib/php_excel.php');

function formato_fecha($fecha,$formato)
    {
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
//echo $_GET['codtip'];
if(isset($_GET['codnom']) && isset($_GET['codtip']))
{
	$codnom=$_GET['codnom'];
	$codtip=$_GET['codtip'];
        
        //echo $codnom;
	$conexion= new bd($_SESSION['bd']);

	$sql = "SELECT UPPER(t.descrip) as descrip 
			FROM   nomtipos_nomina t
			WHERE  t.codtip=".$codtip;
	$res=$conexion->query($sql);

	if($fila=$res->fetch_array())
	{
		$NOMINA = $fila['descrip']; 
	}

	require_once '../paginas/phpexcel/Classes/PHPExcel.php';

	$objPHPExcel = new PHPExcel();

	$objPHPExcel->getProperties()->setCreator("Selectra")
								->setLastModifiedBy("Selectra")
								->setTitle("Comprobante Contable");

	$objPHPExcel->getDefaultStyle()->getFont()->setName('Calibri');
	// $objPHPExcel->getActiveSheet()->getStyle('A2:A4')->getFont()->setName('Calibri');
	$objPHPExcel->getDefaultStyle()->getFont()->setSize(7);


	$sql = "SELECT e.nom_emp as empresa, e.rif, e.tel_emp as telefono, e.dir_emp as direccion, e.ciu_emp as ciudad, 
				e.edo_emp, e.imagen_izq as logo
			FROM   nomempresa e";
	$res=$conexion->query($sql);
	$fila=$res->fetch_array();
	$logo=$fila['logo'];

	$sql2 = "SELECT np.periodo_ini as desde, np.periodo_fin as hasta, np.fechapago as fechapago, np.mes, np.anio,
					DATE_FORMAT(np.periodo_ini,'%d') as dia_ini, DATE_FORMAT(np.periodo_fin, '%d') as dia_fin   
			FROM nom_nominas_pago np 
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
	$dia_fin = $fila2['dia_fin']; 
	$mes_numero = $fila2['mes'];
	$mes_letras = $meses[$mes_numero - 1];
	$anio = $fila2['anio'];

	$empresa = $fila['empresa'];

	$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
	$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_LEGAL);
        
        
        //===========================================BLOQUE CONTEO========================================================
        //================================================================================================================
        
        
        
        $enc=false;$cont=0;
        $bandera=0;
        
        $consulta1_1="SELECT SUM(nm.monto) AS suma, nm.codcon, nm.descrip, nm.tipcon, c.ctacon, cc.Descrip as cuenta_contable  "
                            . "FROM nom_movimientos_nomina AS nm "
                            . "LEFT JOIN nomconceptos AS c ON (nm.codcon=c.codcon) "
                            . "LEFT JOIN cwconcue AS cc ON (c.ctacon=cc.Cuenta) "
                            . "WHERE nm.codnom='".$codnom."' AND nm.tipnom='".$codtip."' AND nm.codnivel1='1' AND nm.monto>0 "
                            . "AND (nm.codcon!=180 AND (nm.codcon<527 OR  (nm.codcon>538 AND nm.codcon<3000))) "
                            . "GROUP BY nm.codcon "
                            . "ORDER BY nm.codcon";
//		echo $consulta;
//                exit;
        $res1_1=$conexion->query($consulta1_1);
        
        while($row1_1=$res1_1->fetch_array())
        {	
                $enc=true;
                $codcon = $row1_1['codcon'];
                $tipcon = $row1_1['tipcon'];
                if($codcon==100)
                {
                    
                    $bandera=1;
                    $pos=$i;
//                    $cont++;
                }
                


                if($tipcon=='A')
                {
                    if($codcon!=103 && $codcon!=169 && $codcon!=171 && $codcon!=177 && $codcon!=178 && $codcon!=604)
                    {                        
                        $cont++;
                    }
                    
                }
                if($tipcon=='D')
                {
                    if($codcon!=184 && $codcon!=185 && $codcon!=187 && $codcon!=190 && $codcon!=198 && $codcon!=199)
                    {
                        $cont++;
                    }
                    

                }
                if($tipcon=='P')
                {
                    $cont++;
                    $cont++;

                }                            

                
        }

        $consulta1_2="SELECT SUM(nm.monto) AS suma, nm.codcon, nm.descrip, nm.tipcon, nm.ficha, "
                            . "c.ctacon, cc.Descrip as cuenta_contable  "
                            . "FROM nom_movimientos_nomina AS nm "
                            . "LEFT JOIN nomconceptos AS c ON (nm.codcon=c.codcon) "
                            . "LEFT JOIN cwconcue AS cc ON (c.ctacon=cc.Cuenta) "
                            . "WHERE nm.codnom='".$codnom."' AND nm.tipnom='".$codtip."' AND nm.codnivel1='1' AND nm.monto>0 "
                            . "AND (nm.codcon=180) "
                            . "GROUP BY nm.ficha, nm.codcon "
                            . "ORDER BY nm.ficha ASC, nm.codcon ASC";
//		echo $consulta;
//                exit;
        $res1_2=$conexion->query($consulta1_2);

        while($row1_2=$res1_2->fetch_array())
        {	
                $enc=true;
                $codcon = $row1_2['codcon'];
                $tipcon = $row1_2['tipcon'];
                
                if($tipcon=='A')
                {
                    if($codcon!=103 && $codcon!=169 && $codcon!=171 && $codcon!=177 && $codcon!=178 && $codcon!=604)
                    {
                        $cont++;


                    }
                   
                }
                
                if($tipcon=='D')
                {
                    if($codcon!=184 && $codcon!=185 && $codcon!=187 && $codcon!=190 && $codcon!=198 && $codcon!=199)
                    {

                        $cont++;


                    }
                    

                }
        }
        
        $consulta1_4="SELECT SUM(nm.monto) AS suma, nm.codcon, nm.descrip, nm.tipcon, nm.ficha, "
                            . "c.ctacon, cc.Descrip as cuenta_contable  "
                            . "FROM nom_movimientos_nomina AS nm "
                            . "LEFT JOIN nomconceptos AS c ON (nm.codcon=c.codcon) "
                            . "LEFT JOIN cwconcue AS cc ON (c.ctacon=cc.Cuenta) "
                            . "WHERE nm.codnom='".$codnom."' AND nm.tipnom='".$codtip."' AND nm.codnivel1='1' AND nm.monto>0 "
                            . "AND (nm.codcon>=527 AND nm.codcon<=538) "
                            . "GROUP BY nm.ficha, nm.codcon "
                            . "ORDER BY nm.ficha ASC, nm.codcon ASC";
//		echo $consulta;
//                exit;
        $res1_4=$conexion->query($consulta1_4);

        while($row1_4=$res1_4->fetch_array())
        {	
                $enc=true;
                $codcon = $row1_4['codcon'];
                $tipcon = $row1_4['tipcon'];
                
                if($tipcon=='D')
                {
                    if($codcon!=184 && $codcon!=185 && $codcon!=187 && $codcon!=190 && $codcon!=198 && $codcon!=199)
                    {
                        $cont++;

                    }
                    

                }
                              

        }
        
        $consulta1_3="SELECT SUM(nm.monto) AS suma, nm.codcon, nm.descrip, nm.tipcon, nm.ficha, "
                            . "c.ctacon, cc.Descrip as cuenta_contable  "
                            . "FROM nom_movimientos_nomina AS nm "
                            . "LEFT JOIN nomconceptos AS c ON (nm.codcon=c.codcon) "
                            . "LEFT JOIN cwconcue AS cc ON (c.ctacon=cc.Cuenta) "
                            . "WHERE nm.codnom='".$codnom."' AND nm.tipnom='".$codtip."' AND nm.codnivel1='1' AND nm.monto>0 "
                            . "AND nm.codcon>=3000  "
                            . "GROUP BY nm.codcon "
                            . "ORDER BY nm.codcon";
//		echo $consulta;
//                exit;
        $res1_3=$conexion->query($consulta1_3);

        while($row1_3=$res1_3->fetch_array())
        {	
                $enc=true;
                $codcon = $row1_3['codcon'];
                $tipcon = $row1_3['tipcon'];
                
                if($tipcon=='P')
                {

                        $cont++;$cont++;


                }                            

                
        }
        
//     echo $cont++; echo "<br>";
//SUCURSALES

        $consulta2_1="SELECT SUM(nm.monto) AS suma, nm.codcon, nm.descrip, nm.tipcon, c.cta_contab_reserva as ctacon, cc.Descrip as cuenta_contable  "
                            . "FROM nom_movimientos_nomina AS nm "
                            . "LEFT JOIN nomconceptos AS c ON (nm.codcon=c.codcon) "
                            . "LEFT JOIN cwconcue AS cc ON (c.cta_contab_reserva=cc.Cuenta) "
                            . "WHERE nm.codnom='".$codnom."' AND nm.tipnom='".$codtip."' AND nm.codnivel1!='1' AND nm.monto>0 "
                            . "AND (nm.codcon!=180 AND (nm.codcon<527 OR  (nm.codcon>538 AND nm.codcon<3000))) "
                            . "GROUP BY nm.codcon "
                            . "ORDER BY nm.codcon";
//		echo $consulta;
//                exit;
        $res2_1=$conexion->query($consulta2_1);
//                    $i++;
        

        $j=0;$k=0;
        while($row2_1=$res2_1->fetch_array())
        {	
                $enc=true;
               $codcon = $row2_1['codcon'];
               $tipcon = $row2_1['tipcon'];
               
                if($codcon==100)
                {
//                    $cont++;
                    $bandera=1;
                }

                if($codcon==501)
                {
                    
                    $bandera2=1;
                    $pos2=$i;
//                    $cont++;

                }
                
                if($codcon>=501 && $codcon<=526)
                {
                    $descuentos=$descuentos+$suma;
                    if($k==0)
                    {
                        $bandera2=1;
                        $cont++;
                    }

                }

                if($tipcon=='A')
                {
                    if($codcon!=103 && $codcon!=169 && $codcon!=171 && $codcon!=177 && $codcon!=178 && $codcon!=604)
                    {
                        $cont++;
      
                    }
                    
                }
                if($tipcon=='D')
                {
                    if(($codcon!=184 && $codcon!=185 && $codcon!=187 && $codcon!=190 && $codcon!=198 && $codcon!=199)
                            &&($codcon<501 || $codcon>526))
                    {
                        $cont++;

                    }
                    

                }
                if($tipcon=='P')
                {

                       $cont++;
                       $cont++;

                }

        }
        
//        echo $cont++; echo "<br>";
        
        $consulta2_2="SELECT SUM(nm.monto) AS suma, nm.codcon, nm.descrip, nm.tipcon, nm.ficha, "
                            . "c.cta_contab_reserva as ctacon, cc.Descrip as cuenta_contable  "
                            . "FROM nom_movimientos_nomina AS nm "
                            . "LEFT JOIN nomconceptos AS c ON (nm.codcon=c.codcon) "
                            . "LEFT JOIN cwconcue AS cc ON (c.cta_contab_reserva=cc.Cuenta) "
                            . "WHERE nm.codnom='".$codnom."' AND nm.tipnom='".$codtip."' AND nm.codnivel1!='1' AND nm.monto>0 "
                            . "AND (nm.codcon=180) "
                            . "GROUP BY nm.ficha, nm.codcon "
                            . "ORDER BY nm.ficha ASC, nm.codcon ASC";
//		echo $consulta;
//                exit;
        $res2_2=$conexion->query($consulta2_2);

        while($row2_2=$res2_2->fetch_array())
        {	
                $enc=true;
                $codcon = $row2_2['codcon'];
                $tipcon = $row2_2['tipcon'];
               
                if($tipcon=='A')
                {
                    if($codcon!=103 && $codcon!=169 && $codcon!=171 && $codcon!=177 && $codcon!=178 && $codcon!=604)
                    {
                        $cont++;


                    }
                    
                }
                            
                if($tipcon=='D')
                {
                    if(($codcon!=184 && $codcon!=185 && $codcon!=187 && $codcon!=190 && $codcon!=198 && $codcon!=199)
                            &&($codcon<=501 || $codcon>526))
                    {
                        $cont++;


                    }
                    

                }                

        }
        
//        echo $cont++; echo "<br>";
        
        $consulta2_4="SELECT SUM(nm.monto) AS suma, nm.codcon, nm.descrip, nm.tipcon, nm.ficha, "
                            . "c.cta_contab_reserva as ctacon, cc.Descrip as cuenta_contable  "
                            . "FROM nom_movimientos_nomina AS nm "
                            . "LEFT JOIN nomconceptos AS c ON (nm.codcon=c.codcon) "
                            . "LEFT JOIN cwconcue AS cc ON (c.cta_contab_reserva=cc.Cuenta) "
                            . "WHERE nm.codnom='".$codnom."' AND nm.tipnom='".$codtip."' AND nm.codnivel1!='1' AND nm.monto>0 "
                            . "AND (nm.codcon>=527 AND nm.codcon<=538) "
                            . "GROUP BY nm.ficha, nm.codcon "
                            . "ORDER BY nm.ficha ASC, nm.codcon ASC";
//		echo $consulta;
//                exit;
        $res2_4=$conexion->query($consulta2_4);

        while($row2_4=$res2_4->fetch_array())
        {	
                $enc=true;
                $codcon = $row2_4['codcon'];
                $tipcon = $row2_4['tipcon'];
                if($tipcon=='D')
                {
                    if(($codcon!=184 && $codcon!=185 && $codcon!=187 && $codcon!=190 && $codcon!=198 && $codcon!=199)
                            &&($codcon<=501 || $codcon>526))
                    {
                        $cont++;

                    }
                    

                }                

        }
        
//        echo $cont++; echo "<br>";
        
        $consulta2_3="SELECT SUM(nm.monto) AS suma, nm.codcon, nm.descrip, nm.tipcon, c.cta_contab_reserva as ctacon, cc.Descrip as cuenta_contable  "
                            . "FROM nom_movimientos_nomina AS nm "
                            . "LEFT JOIN nomconceptos AS c ON (nm.codcon=c.codcon) "
                            . "LEFT JOIN cwconcue AS cc ON (c.cta_contab_reserva=cc.Cuenta) "
                            . "WHERE nm.codnom='".$codnom."' AND nm.tipnom='".$codtip."' AND nm.codnivel1!='1' AND nm.monto>0 "
                            . "AND nm.codcon>=3000 "
                            . "GROUP BY nm.codcon "
                            . "ORDER BY nm.codcon";
//		echo $consulta;
//                exit;
        $res2_3=$conexion->query($consulta2_3);

        while($row2_3=$res2_3->fetch_array())
        {	
                $enc=true;
                $codcon = $row2_3['codcon'];
                $tipcon = $row2_3['tipcon'];
               
                if($tipcon=='P')
                {

                      $cont++;
                      $cont++;

                }


        }
        
//        echo $cont++; echo "<br>";

        if($enc)
        {       
           $cont++;


        }  
        
//        echo $cont++; echo "<br>";
//        
//        exit;
        //============================================BLOQUE COMPROBANTE==================================================
        //============================================BLOQUE COMPROBANTE==================================================
        $i=0;         
        $i++;
        $totalA=$totalD=0;
        $asignaciones=$deducciones=0;
        $ini=$i; $enc=false;
        $salario=$neto=0;
        $bandera=0;
        //PERSONAL ADMINISTRATIVO        

        $consulta1_1="SELECT ROUND(SUM(nm.monto), 2) AS suma, nm.codcon, nm.descrip, nm.tipcon, c.ctacon, cc.Descrip as cuenta_contable  "
                            . "FROM nom_movimientos_nomina AS nm "
                            . "LEFT JOIN nomconceptos AS c ON (nm.codcon=c.codcon) "
                            . "LEFT JOIN cwconcue AS cc ON (c.ctacon=cc.Cuenta) "
                            . "WHERE nm.codnom='".$codnom."' AND nm.tipnom='".$codtip."' AND nm.codnivel1='1' AND nm.monto>0 "
                            . "AND (nm.codcon!=180 AND (nm.codcon<527 OR  (nm.codcon>538 AND nm.codcon<3000))) "
                            . "GROUP BY nm.codcon "
                            . "ORDER BY nm.codcon";
//		echo $consulta;
//                exit;
        $res1_1=$conexion->query($consulta1_1);
        
        while($row1_1=$res1_1->fetch_array())
        {	
                $enc=true;
                $codcon = $row1_1['codcon'];
                $descrip = utf8_decode($row1_1['descrip']);
                $tipcon = $row1_1['tipcon'];
                $ctacon = $row1_1['ctacon'];
                $cuenta_contable = utf8_decode($row1_1['cuenta_contable']);
                $suma = $row1_1['suma'];
                if($codcon==100)
                {
                    $salario=$suma;
                    $bandera=1;
                    $pos=$i;
                    $codcon_salario=$codcon;
                    $descrip_salario=$descrip;
                    $ctacon_salario=$ctacon;
                    $cuenta_contable_salario = $cuenta_contable;
                }
                if($codcon==103 || $codcon==169 || $codcon==171 || $codcon==177 || $codcon==178 || $codcon==604)
                    $salario=$salario+$suma;
                if($codcon==184 || $codcon==185 || $codcon==187 || $codcon==190 || $codcon==198 || $codcon==199)
                    $salario=$salario-$suma;


                if($tipcon=='A')
                {
                    if($codcon!=103 && $codcon!=169 && $codcon!=171 && $codcon!=177 && $codcon!=178 && $codcon!=604)
                    {                        
                        $objPHPExcel->getActiveSheet()->setCellValueExplicit("A$i", $fechapago.","."0001"."#333".$codcon."-".$fechapago.",".",".$cont.",".$ctacon.",".$cuenta_contable.",".$suma.","."".","."FALSE".","."23".","."8".","."FALSE".","."0".","."0"."", PHPExcel_Cell_DataType::TYPE_STRING);
                    }
                    $totalA=$totalA+$suma;
                }
                if($tipcon=='D')
                {
                    if($codcon!=184 && $codcon!=185 && $codcon!=187 && $codcon!=190 && $codcon!=198 && $codcon!=199)
                    {

                        $objPHPExcel->getActiveSheet()->setCellValueExplicit("A$i", $fechapago.","."0001"."#333".$codcon."-".$fechapago.",".",".$cont.",".$ctacon.",".$cuenta_contable.",".(-1)*$suma.","."".","."FALSE".","."23".","."8".","."FALSE".","."0".","."0"."", PHPExcel_Cell_DataType::TYPE_STRING);
//                                     $deducciones=$deducciones+$suma;

                    }
                    $totalD=$totalD+$suma;

                }
                if($tipcon=='P')
                {

                        $objPHPExcel->getActiveSheet()->setCellValueExplicit("A$i", $fechapago.","."0001"."#333".$codcon."-".$fechapago.",".",".$cont.",".$ctacon.",".$cuenta_contable.",".$suma.","."".","."FALSE".","."23".","."8".","."FALSE".","."0".","."0"."", PHPExcel_Cell_DataType::TYPE_STRING);                   
                        if($codcon==3000)
                        {
                            $ctacon=212502.01;
                        }
                        if($codcon==3001)
                        {
                            $ctacon=212502.02;
                        }
                        
                        if($codcon==3002)
                        {
                            $ctacon=212502.01;
                        }
                        
                        if($codcon==9005)
                        {
                            $ctacon=222401.01;
                        }

                        if($codcon==9006)
                        {
                            $ctacon=222401.02;
                        }

                        if($codcon==9007)
                        {
                            $ctacon=212502.06;
                        }

                        if($codcon==9008)
                        {
                            $ctacon=212502.05;
                        }

                        if($codcon==9009)
                        {
                            $ctacon=212502.03;
                        }

                        $consulta_cuenta="SELECT cc.Descrip as cuenta_contable  "
                                . "FROM cwconcue AS cc  "
                                . "WHERE cc.Cuenta='".$ctacon."'";
//                    		echo $consulta_cuenta;
//                                    exit;
                        $res_cuenta=$conexion->query($consulta_cuenta);
                        $row_cuenta=$res_cuenta->fetch_array();                 
                        $descrip_cuenta = utf8_decode($row_cuenta['cuenta_contable']);

                        $i++;                                

                        $objPHPExcel->getActiveSheet()->setCellValueExplicit("A$i", $fechapago.","."0001"."#333".$codcon."-".$fechapago.",".",".$cont.",".$ctacon.",".$descrip_cuenta." Por Pagar".",".(-1)*$suma.","."".","."FALSE".","."23".","."8".","."FALSE".","."0".","."0"."", PHPExcel_Cell_DataType::TYPE_STRING);


                        $totalD=$totalD+$suma;
                        $totalA=$totalA+$suma;
//                                    $asignaciones=$asignaciones+$suma;
//                                    $deducciones=$deducciones+$suma;
                }                            

                 if($codcon!=103 && $codcon!=169 && $codcon!=171 && $codcon!=177 && $codcon!=178 && $codcon!=604
                   && $codcon!=184 && $codcon!=185 && $codcon!=187 && $codcon!=190 && $codcon!=198 && $codcon!=199)
                {
                    $i++;
                }
        }

        $consulta1_2="SELECT ROUND(SUM(nm.monto), 2) AS suma, nm.codcon, nm.descrip, nm.tipcon, nm.ficha, "
                            . "c.ctacon, cc.Descrip as cuenta_contable  "
                            . "FROM nom_movimientos_nomina AS nm "
                            . "LEFT JOIN nomconceptos AS c ON (nm.codcon=c.codcon) "
                            . "LEFT JOIN cwconcue AS cc ON (c.ctacon=cc.Cuenta) "
                            . "WHERE nm.codnom='".$codnom."' AND nm.tipnom='".$codtip."' AND nm.codnivel1='1' AND nm.monto>0 "
                            . "AND (nm.codcon=180) "
                            . "GROUP BY nm.ficha, nm.codcon "
                            . "ORDER BY nm.ficha ASC, nm.codcon ASC";
//		echo $consulta;
//                exit;
        $res1_2=$conexion->query($consulta1_2);

        while($row1_2=$res1_2->fetch_array())
        {	
                $enc=true;
                $codcon = $row1_2['codcon'];
                $descrip = utf8_decode($row1_2['descrip']);
                $tipcon = $row1_2['tipcon'];
                $ctacon = $row1_2['ctacon'];
                $cuenta_contable = utf8_decode($row1_2['cuenta_contable']);
                $suma = $row1_2['suma'];
                $ficha = $row1_2['ficha'];
                
                if($tipcon=='A')
                {
                    if($codcon!=103 && $codcon!=169 && $codcon!=171 && $codcon!=177 && $codcon!=178 && $codcon!=604)
                    {
                        $objPHPExcel->getActiveSheet()->setCellValueExplicit("A$i", $fechapago.","."0001"."#333".$codcon."-".$fechapago.",".",".$cont.",".$ctacon.",".$cuenta_contable.",".$suma.",".str_pad($ficha, 6, "0", STR_PAD_LEFT).","."FALSE".","."23".","."8".","."FALSE".","."0".","."0"."", PHPExcel_Cell_DataType::TYPE_STRING);
//                                    $asignaciones=$asignaciones+$suma;

                    }
                    $totalA=$totalA+$suma;
                }
                
                if($tipcon=='D')
                {
                    if($codcon!=184 && $codcon!=185 && $codcon!=187 && $codcon!=190 && $codcon!=198 && $codcon!=199)
                    {

                        $objPHPExcel->getActiveSheet()->setCellValueExplicit("A$i", $fechapago.","."0001"."#333".$codcon."-".$fechapago.",".",".$cont.",".$ctacon.",".$cuenta_contable.",".(-1)*$suma.",".str_pad($ficha, 6, "0", STR_PAD_LEFT).","."FALSE".","."23".","."8".","."FALSE".","."0".","."0"."", PHPExcel_Cell_DataType::TYPE_STRING);
//                                     $deducciones=$deducciones+$suma;

                    }
                    $totalD=$totalD+$suma;

                }
                              

                 if($codcon!=103 && $codcon!=169 && $codcon!=171 && $codcon!=177 && $codcon!=178 && $codcon!=604
                   && $codcon!=184 && $codcon!=185 && $codcon!=187 && $codcon!=190 && $codcon!=198 && $codcon!=199)
                {
                    $i++;
                }
        }
        
        $consulta1_4="SELECT ROUND(SUM(nm.monto), 2) AS suma, nm.codcon, nm.descrip, nm.tipcon, nm.ficha, "
                            . "c.ctacon, cc.Descrip as cuenta_contable  "
                            . "FROM nom_movimientos_nomina AS nm "
                            . "LEFT JOIN nomconceptos AS c ON (nm.codcon=c.codcon) "
                            . "LEFT JOIN cwconcue AS cc ON (c.ctacon=cc.Cuenta) "
                            . "WHERE nm.codnom='".$codnom."' AND nm.tipnom='".$codtip."' AND nm.codnivel1='1' AND nm.monto>0 "
                            . "AND (nm.codcon>=527 AND nm.codcon<=538) "
                            . "GROUP BY nm.ficha, nm.codcon "
                            . "ORDER BY nm.ficha ASC, nm.codcon ASC";
//		echo $consulta;
//                exit;
        $res1_4=$conexion->query($consulta1_4);

        while($row1_4=$res1_4->fetch_array())
        {	
                $enc=true;
                $codcon = $row1_4['codcon'];
                $descrip = utf8_decode($row1_4['descrip']);
                $tipcon = $row1_4['tipcon'];
                $ctacon = $row1_4['ctacon'];
                $cuenta_contable = utf8_decode($row1_4['cuenta_contable']);
                $suma = $row1_4['suma'];
                $ficha = $row1_4['ficha'];
                
                if($tipcon=='D')
                {
                    if($codcon!=184 && $codcon!=185 && $codcon!=187 && $codcon!=190 && $codcon!=198 && $codcon!=199)
                    {

                        $objPHPExcel->getActiveSheet()->setCellValueExplicit("A$i", $fechapago.","."0001"."#333".$codcon."-".$fechapago.",".",".$cont.",".$ctacon.",".$cuenta_contable.",".(-1)*$suma.",".str_pad($ficha, 6, "0", STR_PAD_LEFT).","."FALSE".","."23".","."8".","."FALSE".","."0".","."0"."", PHPExcel_Cell_DataType::TYPE_STRING);
//                                     $deducciones=$deducciones+$suma;

                    }
                    $totalD=$totalD+$suma;

                }
                              

                 if($codcon!=103 && $codcon!=169 && $codcon!=171 && $codcon!=177 && $codcon!=178 && $codcon!=604
                   && $codcon!=184 && $codcon!=185 && $codcon!=187 && $codcon!=190 && $codcon!=198 && $codcon!=199)
                {
                    $i++;
                }
        }
        
        $consulta1_3="SELECT ROUND(SUM(nm.monto), 2) AS suma, nm.codcon, nm.descrip, nm.tipcon, nm.ficha, "
                            . "c.ctacon, cc.Descrip as cuenta_contable  "
                            . "FROM nom_movimientos_nomina AS nm "
                            . "LEFT JOIN nomconceptos AS c ON (nm.codcon=c.codcon) "
                            . "LEFT JOIN cwconcue AS cc ON (c.ctacon=cc.Cuenta) "
                            . "WHERE nm.codnom='".$codnom."' AND nm.tipnom='".$codtip."' AND nm.codnivel1='1' AND nm.monto>0 "
                            . "AND nm.codcon>=3000  "
                            . "GROUP BY nm.codcon "
                            . "ORDER BY nm.codcon";
//		echo $consulta;
//                exit;
        $res1_3=$conexion->query($consulta1_3);

        while($row1_3=$res1_3->fetch_array())
        {	
                $enc=true;
                $codcon = $row1_3['codcon'];
                $descrip = utf8_decode($row1_3['descrip']);
                $tipcon = $row1_3['tipcon'];
                $ctacon = $row1_3['ctacon'];
                $cuenta_contable = utf8_decode($row1_3['cuenta_contable']);
                $suma = $row1_3['suma'];
                $ficha = $row1_3['ficha'];
                
                if($tipcon=='P')
                {

                        $objPHPExcel->getActiveSheet()->setCellValueExplicit("A$i", $fechapago.","."0001"."#333".$codcon."-".$fechapago.",".",".$cont.",".$ctacon.",".$cuenta_contable.",".$suma.","."".","."FALSE".","."23".","."8".","."FALSE".","."0".","."0"."", PHPExcel_Cell_DataType::TYPE_STRING);                   
                        if($codcon==3000)
                        {
                            $ctacon=212502.01;
                        }
                        if($codcon==3001)
                        {
                            $ctacon=212502.02;
                        }
                        
                        if($codcon==3002)
                        {
                            $ctacon=212502.01;
                        }
                        
                        if($codcon==9005)
                        {
                            $ctacon=222401.01;
                        }

                        if($codcon==9006)
                        {
                            $ctacon=222401.02;
                        }

                        if($codcon==9007)
                        {
                            $ctacon=212502.06;
                        }

                        if($codcon==9008)
                        {
                            $ctacon=212502.05;
                        }

                        if($codcon==9009)
                        {
                            $ctacon=212502.03;
                        }

                        $consulta_cuenta="SELECT cc.Descrip as cuenta_contable  "
                                . "FROM cwconcue AS cc  "
                                . "WHERE cc.Cuenta='".$ctacon."'";
//                    		echo $consulta_cuenta;
//                                    exit;
                        $res_cuenta=$conexion->query($consulta_cuenta);
                        $row_cuenta=$res_cuenta->fetch_array();                 
                        $descrip_cuenta = utf8_decode($row_cuenta['cuenta_contable']);

                        $i++;                                

                        $objPHPExcel->getActiveSheet()->setCellValueExplicit("A$i", $fechapago.","."0001"."#333".$codcon."-".$fechapago.",".",".$cont.",".$ctacon.",".$descrip_cuenta." Por Pagar".",".(-1)*$suma.","."".","."FALSE".","."23".","."8".","."FALSE".","."0".","."0"."", PHPExcel_Cell_DataType::TYPE_STRING);


                        $totalD=$totalD+$suma;
                        $totalA=$totalA+$suma;
//                                    $asignaciones=$asignaciones+$suma;
//                                    $deducciones=$deducciones+$suma;
                }                            

                 if($codcon!=103 && $codcon!=169 && $codcon!=171 && $codcon!=177 && $codcon!=178 && $codcon!=604
                   && $codcon!=184 && $codcon!=185 && $codcon!=187 && $codcon!=190 && $codcon!=198 && $codcon!=199)
                {
                    $i++;
                }
        }
        
        $neto=$totalA-$totalD;
//                    $totalA=$salario+$asignaciones;
//                    $totalD=$neto+$deducciones;
        if($bandera==1)
        {

            $objPHPExcel->getActiveSheet()->setCellValueExplicit("A$pos", $fechapago.","."0001"."#333".$codcon_salario."-".$fechapago.",".",".$cont.",".$ctacon_salario.",".$cuenta_contable_salario.",".number_format($salario, 2, '.', '').","."".","."FALSE".","."23".","."8".","."FALSE".","."0".","."0"."", PHPExcel_Cell_DataType::TYPE_STRING);
        }

//SUCURSALES

        $consulta2_1="SELECT ROUND(SUM(nm.monto), 2) AS suma, nm.codcon, nm.descrip, nm.tipcon, c.cta_contab_reserva as ctacon, cc.Descrip as cuenta_contable  "
                            . "FROM nom_movimientos_nomina AS nm "
                            . "LEFT JOIN nomconceptos AS c ON (nm.codcon=c.codcon) "
                            . "LEFT JOIN cwconcue AS cc ON (c.cta_contab_reserva=cc.Cuenta) "
                            . "WHERE nm.codnom='".$codnom."' AND nm.tipnom='".$codtip."' AND nm.codnivel1!='1' AND nm.monto>0 "
                            . "AND (nm.codcon!=180 AND (nm.codcon<527 OR  (nm.codcon>538 AND nm.codcon<3000))) "
                            . "GROUP BY nm.codcon "
                            . "ORDER BY nm.codcon";
//		echo $consulta;
//                exit;
        $res2_1=$conexion->query($consulta2_1);
//                    $i++;
        
        $descuentos=$salario=$neto=0;
        $bandera=$bandera2=0;
        $pos=$pos2=0;
        
        $j=0;$k=0;
        while($row2_1=$res2_1->fetch_array())
        {	
                $enc=true;
                $codcon = $row2_1['codcon'];
                $descrip = utf8_decode($row2_1['descrip']);
                $tipcon = $row2_1['tipcon'];
                $ctacon = $row2_1['ctacon'];
                $cuenta_contable = utf8_decode($row2_1['cuenta_contable']);
                $suma = $row2_1['suma'];
                if($codcon==100)
                {
                    $salario=$suma;
                    $bandera=1;
                    $pos=$i;
                    $codcon_salario=$codcon;
                    $descrip_salario=$descrip;
                    $ctacon_salario=$ctacon;
                    $cuenta_contable_salario = $cuenta_contable;
                }

//                if($codcon==501)
//                {
//                    $descuentos=$suma;
//                    $bandera2=1;
//                    $pos2=$i;
//                    $codcon_descuentos=$codcon;
//                    $descrip_descuentos=$descrip;
//                    $ctacon_descuentos=$ctacon;
//                    $cuenta_contable_descuentos = $cuenta_contable;
//                }

                if($codcon>=501 && $codcon<=526)
                {
                    $descuentos=$descuentos+$suma;
                    if($k==0)
                    {
                        $bandera2=1;
                        $pos2=$i;
                        $codcon_descuentos=$codcon;
                        $descrip_descuentos=$descrip;
                        $ctacon_descuentos=$ctacon;
                        $cuenta_contable_descuentos = $cuenta_contable;
                        $i++;
                        $k++;
                    }
                    
                }

                if($codcon==103 || $codcon==169 || $codcon==171 || $codcon==177 || $codcon==178 || $codcon==604)
                    $salario=$salario+$suma;

                if($codcon==184 || $codcon==185 || $codcon==187 || $codcon==190 || $codcon==198 || $codcon==199)
                    $salario=$salario-$suma;


                if($tipcon=='A')
                {
                    if($codcon!=103 && $codcon!=169 && $codcon!=171 && $codcon!=177 && $codcon!=178 && $codcon!=604)
                    {
                        $objPHPExcel->getActiveSheet()->setCellValueExplicit("A$i", $fechapago.","."0001"."#333".$codcon."-".$fechapago.",".",".$cont.",".$ctacon.",".$cuenta_contable.",".$suma.","."".","."FALSE".","."23".","."8".","."FALSE".","."0".","."0"."", PHPExcel_Cell_DataType::TYPE_STRING);
//                                  
                    }
                    $totalA=$totalA+$suma;
                }
                if($tipcon=='D')
                {
                    if(($codcon!=184 && $codcon!=185 && $codcon!=187 && $codcon!=190 && $codcon!=198 && $codcon!=199)
                            &&($codcon<501 || $codcon>526))
                    {
                        $objPHPExcel->getActiveSheet()->setCellValueExplicit("A$i", $fechapago.","."0001"."#333".$codcon."-".$fechapago.",".",".$cont.",".$ctacon.",".$cuenta_contable.",".(-1)*$suma.","."".","."FALSE".","."23".","."8".","."FALSE".","."0".","."0"."", PHPExcel_Cell_DataType::TYPE_STRING);
//                       

                    }
                    $totalD=$totalD+$suma;

                }
                if($tipcon=='P')
                {

                        $objPHPExcel->getActiveSheet()->setCellValueExplicit("A$i", $fechapago.","."0001"."#333".$codcon."-".$fechapago.",".",".$cont.",".$ctacon.",".$cuenta_contable.",".$suma.","."".","."FALSE".","."23".","."8".","."FALSE".","."0".","."0"."", PHPExcel_Cell_DataType::TYPE_STRING);                                                
                       if($codcon==3000)
                        {
                            $ctacon=212502.01;
                        }
                        if($codcon==3001)
                        {
                            $ctacon=212502.02;
                        }
                        
                        if($codcon==3002)
                        {
                            $ctacon=212502.01;
                        }
                        
                        if($codcon==9005)
                        {
                            $ctacon=222401.01;
                        }

                        if($codcon==9006)
                        {
                            $ctacon=222401.02;
                        }

                        if($codcon==9007)
                        {
                            $ctacon=212502.06;
                        }

                        if($codcon==9008)
                        {
                            $ctacon=212502.05;
                        }

                        if($codcon==9009)
                        {
                            $ctacon=212502.03;
                        }

                        $consulta_cuenta="SELECT cc.Descrip as cuenta_contable  "
                                . "FROM cwconcue AS cc  "
                                . "WHERE cc.Cuenta='".$ctacon."'";
//                    		echo $consulta_cuenta;
//                                    exit;
                        $res_cuenta=$conexion->query($consulta_cuenta);
                        $row_cuenta=$res_cuenta->fetch_array();                 
                        $descrip_cuenta = utf8_decode($row_cuenta['cuenta_contable']);


                        $i++;                                

                        $objPHPExcel->getActiveSheet()->setCellValueExplicit("A$i", $fechapago.","."0001"."#333".$codcon."-".$fechapago.",".",".$cont.",".$ctacon.",".$descrip_cuenta." Por Pagar".",".(-1)*$suma.","."".","."FALSE".","."23".","."8".","."FALSE".","."0".","."0"."", PHPExcel_Cell_DataType::TYPE_STRING);

                        $totalD=$totalD+$suma;
                        $totalA=$totalA+$suma;
//                                    $asignaciones=$asignaciones+$suma;
//                                    $deducciones=$deducciones+$suma;
                }


                if(($codcon!=103 && $codcon!=169 && $codcon!=171 && $codcon!=177 && $codcon!=178 && $codcon!=604
                   && $codcon!=184 && $codcon!=185 && $codcon!=187 && $codcon!=190 && $codcon!=198 && $codcon!=199)&&($codcon<501 || $codcon>526))
                {
                    $i++;
                }
        }
        
        
        $consulta2_2="SELECT ROUND(SUM(nm.monto), 2) AS suma, nm.codcon, nm.descrip, nm.tipcon, nm.ficha, "
                            . "c.cta_contab_reserva as ctacon, cc.Descrip as cuenta_contable  "
                            . "FROM nom_movimientos_nomina AS nm "
                            . "LEFT JOIN nomconceptos AS c ON (nm.codcon=c.codcon) "
                            . "LEFT JOIN cwconcue AS cc ON (c.cta_contab_reserva=cc.Cuenta) "
                            . "WHERE nm.codnom='".$codnom."' AND nm.tipnom='".$codtip."' AND nm.codnivel1!='1' AND nm.monto>0 "
                            . "AND (nm.codcon=180) "
                            . "GROUP BY nm.ficha, nm.codcon "
                            . "ORDER BY nm.ficha ASC, nm.codcon ASC";
//		echo $consulta;
//                exit;
        $res2_2=$conexion->query($consulta2_2);
//                    $i++;
       
        

        while($row2_2=$res2_2->fetch_array())
        {	
                $enc=true;
                $codcon = $row2_2['codcon'];
                $descrip = utf8_decode($row2_2['descrip']);
                $tipcon = $row2_2['tipcon'];
                $ctacon = $row2_2['ctacon'];
                $cuenta_contable = utf8_decode($row2_2['cuenta_contable']);
                $suma = $row2_2['suma'];
                $ficha = $row2_2['ficha'];
               
                if($tipcon=='A')
                {
                    if($codcon!=103 && $codcon!=169 && $codcon!=171 && $codcon!=177 && $codcon!=178 && $codcon!=604)
                    {
                        $objPHPExcel->getActiveSheet()->setCellValueExplicit("A$i", $fechapago.","."0001"."#333".$codcon."-".$fechapago.",".",".$cont.",".$ctacon.",".$cuenta_contable.",".$suma.",".str_pad($ficha, 6, "0", STR_PAD_LEFT).","."FALSE".","."23".","."8".","."FALSE".","."0".","."0"."", PHPExcel_Cell_DataType::TYPE_STRING);
//                                    $asignaciones=$asignaciones+$suma;

                    }
                    $totalA=$totalA+$suma;
                }
                            
                if($tipcon=='D')
                {
                    if(($codcon!=184 && $codcon!=185 && $codcon!=187 && $codcon!=190 && $codcon!=198 && $codcon!=199)
                            &&($codcon<=501 || $codcon>526))
                    {
                        $objPHPExcel->getActiveSheet()->setCellValueExplicit("A$i", $fechapago.","."0001"."#333".$codcon."-".$fechapago.",".",".$cont.",".$ctacon.",".$cuenta_contable.",".(-1)*$suma.",".str_pad($ficha, 6, "0", STR_PAD_LEFT).","."FALSE".","."23".","."8".","."FALSE".","."0".","."0"."", PHPExcel_Cell_DataType::TYPE_STRING);
//                       

                    }
                    $totalD=$totalD+$suma;

                }                


                if(($codcon!=103 && $codcon!=169 && $codcon!=171 && $codcon!=177 && $codcon!=178 && $codcon!=604
                   && $codcon!=184 && $codcon!=185 && $codcon!=187 && $codcon!=190 && $codcon!=198 && $codcon!=199)&&($codcon<=501 || $codcon>526))
                {
                    $i++;
                }
        }
        
        $consulta2_4="SELECT ROUND(SUM(nm.monto), 2) AS suma, nm.codcon, nm.descrip, nm.tipcon, nm.ficha, "
                            . "c.cta_contab_reserva as ctacon, cc.Descrip as cuenta_contable  "
                            . "FROM nom_movimientos_nomina AS nm "
                            . "LEFT JOIN nomconceptos AS c ON (nm.codcon=c.codcon) "
                            . "LEFT JOIN cwconcue AS cc ON (c.cta_contab_reserva=cc.Cuenta) "
                            . "WHERE nm.codnom='".$codnom."' AND nm.tipnom='".$codtip."' AND nm.codnivel1!='1' AND nm.monto>0 "
                            . "AND (nm.codcon>=527 AND nm.codcon<=538) "
                            . "GROUP BY nm.ficha, nm.codcon "
                            . "ORDER BY nm.ficha ASC, nm.codcon ASC";
//		echo $consulta;
//                exit;
        $res2_4=$conexion->query($consulta2_4);
//                    $i++;
       
        

        while($row2_4=$res2_4->fetch_array())
        {	
                $enc=true;
                $codcon = $row2_4['codcon'];
                $descrip = utf8_decode($row2_4['descrip']);
                $tipcon = $row2_4['tipcon'];
                $ctacon = $row2_4['ctacon'];
                $cuenta_contable = utf8_decode($row2_4['cuenta_contable']);
                $suma = $row2_4['suma'];
                $ficha = $row2_4['ficha'];
               
                if($tipcon=='D')
                {
                    if(($codcon!=184 && $codcon!=185 && $codcon!=187 && $codcon!=190 && $codcon!=198 && $codcon!=199)
                            &&($codcon<=501 || $codcon>526))
                    {
                        $objPHPExcel->getActiveSheet()->setCellValueExplicit("A$i", $fechapago.","."0001"."#333".$codcon."-".$fechapago.",".",".$cont.",".$ctacon.",".$cuenta_contable.",".(-1)*$suma.",".str_pad($ficha, 6, "0", STR_PAD_LEFT).","."FALSE".","."23".","."8".","."FALSE".","."0".","."0"."", PHPExcel_Cell_DataType::TYPE_STRING);
//                       

                    }
                    $totalD=$totalD+$suma;

                }                


                if(($codcon!=103 && $codcon!=169 && $codcon!=171 && $codcon!=177 && $codcon!=178 && $codcon!=604
                   && $codcon!=184 && $codcon!=185 && $codcon!=187 && $codcon!=190 && $codcon!=198 && $codcon!=199)&&($codcon<=501 || $codcon>526))
                {
                    $i++;
                }
        }
        
        $consulta2_3="SELECT ROUND(SUM(nm.monto), 2) AS suma, nm.codcon, nm.descrip, nm.tipcon, c.cta_contab_reserva as ctacon, cc.Descrip as cuenta_contable  "
                            . "FROM nom_movimientos_nomina AS nm "
                            . "LEFT JOIN nomconceptos AS c ON (nm.codcon=c.codcon) "
                            . "LEFT JOIN cwconcue AS cc ON (c.cta_contab_reserva=cc.Cuenta) "
                            . "WHERE nm.codnom='".$codnom."' AND nm.tipnom='".$codtip."' AND nm.codnivel1!='1' AND nm.monto>0 "
                            . "AND nm.codcon>=3000 "
                            . "GROUP BY nm.codcon "
                            . "ORDER BY nm.codcon";
//		echo $consulta;
//                exit;
        $res2_3=$conexion->query($consulta2_3);
//                    $i++;
        
        

        while($row2_3=$res2_3->fetch_array())
        {	
                $enc=true;
                $codcon = $row2_3['codcon'];
                $descrip = utf8_decode($row2_3['descrip']);
                $tipcon = $row2_3['tipcon'];
                $ctacon = $row2_3['ctacon'];
                $cuenta_contable = utf8_decode($row2_3['cuenta_contable']);
                $suma = $row2_3['suma'];
                $ficha = $row2_3['ficha'];
                
                if($tipcon=='P')
                {

                        $objPHPExcel->getActiveSheet()->setCellValueExplicit("A$i", $fechapago.","."0001"."#333".$codcon."-".$fechapago.",".",".$cont.",".$ctacon.",".$cuenta_contable.",".$suma.","."".","."FALSE".","."23".","."8".","."FALSE".","."0".","."0"."", PHPExcel_Cell_DataType::TYPE_STRING);                                                
                       if($codcon==3000)
                        {
                            $ctacon=212502.01;
                        }
                        if($codcon==3001)
                        {
                            $ctacon=212502.02;
                        }
                        
                        if($codcon==3002)
                        {
                            $ctacon=212502.01;
                        }
                        
                        if($codcon==9005)
                        {
                            $ctacon=222401.01;
                        }

                        if($codcon==9006)
                        {
                            $ctacon=222401.02;
                        }

                        if($codcon==9007)
                        {
                            $ctacon=212502.06;
                        }

                        if($codcon==9008)
                        {
                            $ctacon=212502.05;
                        }

                        if($codcon==9009)
                        {
                            $ctacon=212502.03;
                        }

                        $consulta_cuenta="SELECT cc.Descrip as cuenta_contable  "
                                . "FROM cwconcue AS cc  "
                                . "WHERE cc.Cuenta='".$ctacon."'";
//                    		echo $consulta_cuenta;
//                                    exit;
                        $res_cuenta=$conexion->query($consulta_cuenta);
                        $row_cuenta=$res_cuenta->fetch_array();                 
                        $descrip_cuenta = utf8_decode($row_cuenta['cuenta_contable']);


                        $i++;                                

                        $objPHPExcel->getActiveSheet()->setCellValueExplicit("A$i", $fechapago.","."0001"."#333".$codcon."-".$fechapago.",".",".$cont.",".$ctacon.",".$descrip_cuenta." Por Pagar".",".(-1)*$suma.","."".","."FALSE".","."23".","."8".","."FALSE".","."0".","."0"."", PHPExcel_Cell_DataType::TYPE_STRING);

                        $totalD=$totalD+$suma;
                        $totalA=$totalA+$suma;
//                                    $asignaciones=$asignaciones+$suma;
//                                    $deducciones=$deducciones+$suma;
                }


                if(($codcon!=103 && $codcon!=169 && $codcon!=171 && $codcon!=177 && $codcon!=178 && $codcon!=604
                   && $codcon!=184 && $codcon!=185 && $codcon!=187 && $codcon!=190 && $codcon!=198 && $codcon!=199)&&($codcon<=501 || $codcon>526))
                {
                    $i++;
                }
        }
        
        $neto=$totalA-$totalD;
//                    $totalA=$salario+$asignaciones;
//                    $totalD=$neto+$deducciones;
        if($bandera==1)
        {

            $objPHPExcel->getActiveSheet()->setCellValueExplicit("A$pos", $fechapago.","."0001"."#333".$codcon_salario."-".$fechapago.",".",".$cont.",".$ctacon_salario.",".$cuenta_contable_salario.",".number_format($salario, 2, '.', '').","."".","."FALSE".","."23".","."8".","."FALSE".","."0".","."0"."", PHPExcel_Cell_DataType::TYPE_STRING);
        }
        if($bandera2==1)
        {
            $consulta_cuenta_descuento="SELECT c.codcon as codcon_descuento, c.descrip as descrip_descuento, c.cta_contab_reserva as ctacon_descuento, "
                                            . "cc.Descrip as cuenta_contable_descuento  "
                                            . "FROM nomconceptos as c "
                                            . "LEFT JOIN cwconcue as cc ON (c.cta_contab_reserva=cc.Cuenta) "
                                            . "WHERE c.codcon='501'";
//                    		echo $consulta_cuenta;
//                                    exit;
            $res_cuenta_descuento=$conexion->query($consulta_cuenta_descuento);
            $row_cuenta_descuento=$res_cuenta_descuento->fetch_array();                 
            $cuenta_contable_descuento = utf8_decode($row_cuenta_descuento['cuenta_contable_descuento']);
            $ctacon_descuento = $row_cuenta_descuento['ctacon_descuento'];
            $codcon_descuento = $row_cuenta_descuento['codcon_descuento'];
            $descrip_descuento = utf8_decode($row_cuenta_descuento['descrip_descuento']);
            $objPHPExcel->getActiveSheet()->setCellValueExplicit("A$pos2", $fechapago.","."0001"."#333".$codcon_descuento."-".$fechapago.",".",".$cont.",".$ctacon_descuento.",".$cuenta_contable_descuento.",".number_format((-1)*$descuentos, 2, '.', '').","."".","."FALSE".","."23".","."8".","."FALSE".","."0".","."0"."", PHPExcel_Cell_DataType::TYPE_STRING);
        }

        if($enc)
        {       
            $objPHPExcel->getActiveSheet()->setCellValueExplicit("A$i", $fechapago.","."0001"."#333".$codcon_salario."-".$fechapago.",".",".$cont.","."212501".","."Salario por Pagar".",".number_format((-1)*$neto, 2, '.', '').","."".","."FALSE".","."23".","."8".","."FALSE".","."0".","."0"."", PHPExcel_Cell_DataType::TYPE_STRING);
                $i++;	

        }
  
        
	$NOMINA = str_replace(' ', '_', $NOMINA);
	$filename = "peachtree_contable_".$NOMINA."_".fecha($desde).'_'.fecha($hasta);

	// Redirect output to a client’s web browser (Excel5)
	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename="'.$filename.'.csv"');
	header('Cache-Control: max-age=0');
	// If you're serving to IE 9, then the following may be needed
	header('Cache-Control: max-age=1');

	// If you're serving to IE over SSL, then the following may be needed
	header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
	header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
	header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
	header ('Pragma: public'); // HTTP/1.0
        ob_end_clean();
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'CSV');
        $objWriter->setSheetIndex(0);   // Select which sheet.
        $objWriter->setDelimiter(","); 
        $objWriter->setEnclosure(''); 
        $objWriter->setLineEnding("\r\n"); 
	$objWriter->save('php://output');
}
exit;
