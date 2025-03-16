<?php
session_start();
error_reporting(0);
ini_set('display_errors', FALSE);
ini_set('display_startup_errors', FALSE);
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
        
        echo $codnom;
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


        $i=0; 
        $ini=$i; $enc=false;$cont=0;
        $salario=$neto=0;      	
        $totalA=$totalD=0;
        $asignaciones=$deducciones=0;
        $bandera=0;
        $total_salarios=$total_comision=$total_combustible=$total_reembolso=$total_uso_auto=$total_bono="=";	

        $consulta="SELECT SUM(nm.monto) AS suma, nm.codcon, nm.descrip, nm.tipcon, c.ctacon, cc.Descrip as cuenta_contable  "
                . "FROM nom_movimientos_nomina AS nm "
                . "LEFT JOIN nomconceptos AS c ON (nm.codcon=c.codcon) "
                . "LEFT JOIN cwconcue AS cc ON (c.ctacon=cc.Cuenta) "
                . "WHERE nm.codnom='".$codnom."' AND nm.tipnom='".$codtip."'"
                . "GROUP BY nm.codcon "
                . "ORDER BY nm.codcon";
    //		echo $consulta;
    //                exit;
        $res2=$conexion->query($consulta);
        $i++;
        $ini=$i; $enc=false;$cont=0;
        $salario=$neto=0;

        while($row2=$res2->fetch_array())
        {	
                $enc=true;
                $codcon = $row2['codcon'];
                $descrip = utf8_decode($row2['descrip']);
                $tipcon = $row2['tipcon'];
                $ctacon = $row2['ctacon'];
                $cuenta_contable = $row2['cuenta_contable'];
                $suma = $row2['suma'];
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
               if($codcon==169 || $codcon==103 || $codcon==177 || $codcon==178 || $codcon==188)
                    $salario=$salario+$suma;
                if($codcon==190 || $codcon==198 || $codcon==199)
                    $salario=$salario-$suma;

                if($tipcon=='A')
                {
                    if($codcon!=169 && $codcon!=103 && $codcon!=177 && $codcon!=178 && $codcon!=188)
                    {
                        
                        $objPHPExcel->getActiveSheet()->setCellValueExplicit("A$i", $fechapago.","."0001"."#333".$codcon."-".$fechapago.",".",".$cont.",".$ctacon.",".$cuenta_contable.",".$suma.","."0003".","."FALSE".","."23".","."8".","."FALSE".","."0".","."0".",", PHPExcel_Cell_DataType::TYPE_STRING);
                    }
                    $totalA=$totalA+$suma;
                }
                if($tipcon=='D')
                {
                    if($codcon!=190 && $codcon!=198 && $codcon!=199)
                    {
                        $objPHPExcel->getActiveSheet()->setCellValueExplicit("A$i", $fechapago.","."0001"."#333".$codcon."-".$fechapago.",".",".$cont.",".$ctacon.",".$cuenta_contable.",".(-1)*$suma.","."0003".","."FALSE".","."23".","."8".","."FALSE".","."0".","."0".",", PHPExcel_Cell_DataType::TYPE_STRING);
//                                     $deducciones=$deducciones+$suma;

                    }
                    $totalD=$totalD+$suma;

                }
                if($tipcon=='P')
                {

                        $objPHPExcel->getActiveSheet()->setCellValueExplicit("A$i", $fechapago.","."0001"."#333".$codcon."-".$fechapago.",".",".$cont.",".$ctacon.",".$cuenta_contable.",".$suma.","."0003".","."FALSE".","."23".","."8".","."FALSE".","."0".","."0".",", PHPExcel_Cell_DataType::TYPE_STRING);                     
                        if($codcon==3000)
                        {
                            $ctacon=21240201;
                        }
                        if($codcon==3001)
                        {
                            $ctacon=21240201;
                        }

                        if($codcon==9005)
                        {
                            $ctacon=21240401;
                        }

                        if($codcon==9006)
                        {
                            $ctacon=21240402;
                        }

                        if($codcon==9007)
                        {
                            $ctacon=21240206;
                        }

                        if($codcon==9008)
                        {
                            $ctacon=21240205;
                        }

                        if($codcon==9009)
                        {
                            $ctacon=21240201;
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
                        
                        $objPHPExcel->getActiveSheet()->setCellValueExplicit("A$i", $fechapago.","."0001"."#333".$codcon."-".$fechapago.",".",".$cont.",".$ctacon.",".$descrip_cuenta." Por Pagar".",".(-1)*$suma.","."0003".","."FALSE".","."23".","."8".","."FALSE".","."0".","."0".",", PHPExcel_Cell_DataType::TYPE_STRING);
                        
                        $totalD=$totalD+$suma;
                        $totalA=$totalA+$suma;
//                                    $asignaciones=$asignaciones+$suma;
//                                    $deducciones=$deducciones+$suma;
                }

               


                if($codcon!=190 && $codcon!=198 && $codcon!=199 && $codcon!=169
                   && $codcon!=103 && $codcon!=177 && $codcon!=178 && $codcon!=188)
                {
                    $i++;$cont++;
                }
        }
        $neto=$totalA-$totalD;
//                    $totalA=$salario+$asignaciones;
//                    $totalD=$neto+$deducciones;
        if($bandera==1)
        {
            
            $objPHPExcel->getActiveSheet()->setCellValueExplicit("A$pos", $fechapago.","."0001"."#333".$codcon_salario."-".$fechapago.",".",".$cont.",".$ctacon_salario.",".$cuenta_contable_salario.",".$salario.","."0003".","."FALSE".","."23".","."8".","."FALSE".","."0".","."0".",", PHPExcel_Cell_DataType::TYPE_STRING);
        }

        if($enc)
        {         
            $objPHPExcel->getActiveSheet()->setCellValueExplicit("A$i", $fechapago.","."0001"."#333".$codcon_salario."-".$fechapago.",".",".$cont.","."21240101".","."Salario por Pagar".",".(-1)*$neto.","."0003".","."FALSE".","."23".","."8".","."FALSE".","."0".","."0".",", PHPExcel_Cell_DataType::TYPE_STRING);



            $i++;
        }
//}
        
	$NOMINA = str_replace(' ', '_', $NOMINA);
	$filename = "zoho_contable_".$NOMINA."_".fecha($desde).'_'.fecha($hasta);

	// Redirect output to a client’s web browser (Excel5)
	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename="'.$filename.'.txt"');
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
