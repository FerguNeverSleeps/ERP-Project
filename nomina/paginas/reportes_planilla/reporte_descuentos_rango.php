<?php
session_start();
error_reporting(0);
ini_set('display_errors', FALSE);
ini_set('display_startup_errors', FALSE);
date_default_timezone_set('America/Panama');

if (PHP_SAPI == 'cli')
	die('Este ejemplo solo se debe ejecutar desde un navegador Web');

include('../../lib/common.php');
include('../lib/php_excel.php');
function ConvertirFechaYMD($fecha)
{
    return date("Y-m-d",strtotime($fecha));
}

if(isset($_GET['fecha_inicio']) && isset($_GET['fecha_fin']) && isset($_GET['tipo_planilla']))
{
	$fecha_inicio=ConvertirFechaYMD($_GET['fecha_inicio']);
	$fecha_fin=ConvertirFechaYMD($_GET['fecha_fin']);
    $codtip=$_GET['tipo_planilla'];

	$conexion= new bd($_SESSION['bd']);
    
    //cargar configuracion de reporte
    $sql1 = "select * from config_reportes_planilla where id = 2";
    $res=$conexion->query($sql1);
    $configReporte=$res->fetch_array();
    $sql_reporte = $configReporte['sql_reporte'];

    $sqlDesc = "select * from config_reportes_planilla_columnas 
				where id_reporte =".$configReporte['id']." and visible=1 order by col_orden asc";
                $resc=$conexion->query($sqlDesc);
    while($column=$resc->fetch_array()){
        $conceptos[$column['nombre_corto']] = $column['conceptos'];
    }

	$sql = "SELECT UPPER(t.descrip) as descrip 
			FROM   nomtipos_nomina t
			WHERE  t.codtip=".$codtip;
	$res=$conexion->query($sql);

	if($fila=$res->fetch_array())
	{
		$NOMINA = $fila['descrip']; 
	}

    require_once '../phpexcel/Classes/PHPExcel.php';

    $objPHPExcel = new PHPExcel();
    $objPHPExcel = PHPExcel_IOFactory::load("../plantillas/descuentos_mmd.xlsx");

    $objPHPExcel->getProperties()->setCreator("Selectra")
                                ->setLastModifiedBy("Selectra")
                                ->setTitle("Horizontal de Planilla");

    $objPHPExcel->getDefaultStyle()->getFont()->setName('Calibri');
    $objPHPExcel->getDefaultStyle()->getFont()->setSize(11);


    $sql = "SELECT e.nom_emp as empresa, e.rif, e.tel_emp as telefono, e.dir_emp as direccion, e.ciu_emp as ciudad, 
                e.edo_emp, e.imagen_izq as logo
            FROM   nomempresa e";
    $res=$conexion->query($sql);
    $fila=$res->fetch_array();
    $logo=$fila['logo'];

    $sql2 = "SELECT np.periodo_ini as desde, np.periodo_fin as hasta, np.mes,
                    DATE_FORMAT(np.periodo_ini,'%d-%m') as dia_ini, DATE_FORMAT(np.periodo_fin, '%d-%m') as dia_fin,
                    DATE_FORMAT(np.periodo_fin, '%Y') as anio   
            FROM nom_nominas_pago np 
            WHERE  np.codnom='$codnom' AND np.tipnom='$codtip'";
    $res2=$conexion->query($sql2);
    $fila2=$res2->fetch_array();

    $meses = array('Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre');

    $desde=$fecha_inicio;
    $hasta=$fecha_fin;
    $dia_ini = $fila2['dia_ini'];
    $dia_fin = $fila2['dia_fin']; 
    $mes_numero = $fila2['mes'];
    $mes_letras = $meses[$mes_numero - 1];
    $anio = $fila2['anio'];

    $empresa = utf8_encode($fila['empresa']);
    $objPHPExcel->getActiveSheet()
                ->setCellValue('C2', strtoupper($empresa) )
                ->setCellValue('C3', 'Descuentos por Acreedor')
                ->setCellValue('C4', 'Detallada por Rango de Fecha')
                ->setCellValue('C5', 'DEL '. $_GET['fecha_inicio'] .' AL '. $_GET['fecha_fin'] );

    $objPHPExcel->getActiveSheet()->getStyle('C2:F5')->getFont()->setSize(14);
    $objPHPExcel->getActiveSheet()->getStyle('C2:F5')->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('C2:F5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->mergeCells('C2:F2');
    $objPHPExcel->getActiveSheet()->mergeCells('C3:F3');
    $objPHPExcel->getActiveSheet()->mergeCells('C4:F4');
    $objPHPExcel->getActiveSheet()->mergeCells('C5:F5');

    $objPHPExcel->getActiveSheet()->freezePane('A6'); // Inmovilizar Paneles

    /*  DESCUENTOS  COMERCIALES */
    //and codcon NOT IN (527,528,529,530,531,533,534,535) excluir conceptos
    $sql1 = "select distinct descrip,codcon from nom_movimientos_nomina where codcon IN (".$conceptos['a_comerciales'].")
     and codnom in (SELECT np.codnom  
    FROM nom_nominas_pago np 
    WHERE np.tipnom=$codtip AND np.periodo_ini BETWEEN '$fecha_inicio' AND '$fecha_fin')";
    $res1=$conexion->query($sql1);

    $i=7; $nivel=1;
    $totalMonto = $totalMontoGen = 0;
    $totalAdmon = $totalAdmonGen = 0;
    $totalTotal = $totalTotalGen = 0;

    while($prestamo=$res1->fetch_array())
    {    
        $descripAcreedor = utf8_encode($prestamo['descrip']);
        $codcon = $prestamo['codcon'];

        $objPHPExcel->getActiveSheet()->mergeCells('B'.$i.':C'.$i);
        $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':F'.$i)->getFont()->setSize(14)->setBold(true);

        $objPHPExcel->getActiveSheet()->setCellValue('B'.$i, strtoupper("ACREEDOR: ".$descripAcreedor));

        $objPHPExcel->getActiveSheet()->setCellValue('D'.$i, "Total Descontado");
        $objPHPExcel->getActiveSheet()->setCellValue('E'.$i, "Gasto Admin (2%)");
        $objPHPExcel->getActiveSheet()->setCellValue('F'.$i, "Total a Pagar");
        $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

        $i++;

        $sql3 = "SELECT a.codnom,a.tipnom,a.ficha,b.apenom,b.cedula,d.gastos_admon, 
        COALESCE(( SUM(IF(a.codcon in ($codcon),a.monto,0)) ),0) as descuento, 
        COALESCE(( SUM(IF(a.codcon in ($codcon),a.monto,0)) ),0) as gasto_adm, 
        COALESCE(( SUM(IF(a.codcon in ($codcon),a.monto,0)) ),0) as total_pago 
        FROM nom_movimientos_nomina a 
        LEFT JOIN nompersonal b ON a.ficha=b.ficha 
        LEFT JOIN nomprestamos_cabecera d ON a.numpre=d.numpre 
        WHERE a.codcon='$codcon' AND a.monto != 0 AND a.codnom IN (
        SELECT np.codnom FROM nom_nominas_pago np 
        WHERE np.tipnom=$codtip AND np.periodo_ini BETWEEN '$fecha_inicio' AND '$fecha_fin') 
        GROUP BY a.ficha,a.numpre ORDER BY a.ficha,a.codnom";
        
        //echo $sql3;exit;
        $res3=$conexion->query($sql3);
        $subTotalMonto = 0;
        $subTotalAdmon = 0;
        $subTotalTotal = 0;

        while($empleado=$res3->fetch_array())
        {    
            $cedula = $empleado['cedula'];
            $apenom = utf8_encode($empleado['apenom']);
            //$objPHPExcel->getActiveSheet()->mergeCells('B'.$i.':C'.$i);
            $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':F'.$i)->getFont()->setSize(10);
            $objPHPExcel->getActiveSheet()->setCellValue('B'.$i, strtoupper($cedula));
            $objPHPExcel->getActiveSheet()->setCellValue('C'.$i, strtoupper($apenom));
            $objPHPExcel->getActiveSheet()->getStyle('B'.$i.':C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            
            $descuento = $empleado['descuento'];
            $admon = round( $descuento * ($empleado['gastos_admon'] / 100) ,2);
            $total = round( $descuento - $admon ,2);
            
            $subTotalMonto = $subTotalMonto + $descuento;
            $subTotalAdmon = $subTotalAdmon + $admon;
            $subTotalTotal = $subTotalTotal + $total;

            $totalMonto = $totalMonto + $descuento;
            $totalAdmon = $totalAdmon + $admon;
            $totalTotal = $totalTotal + $total;

            $objPHPExcel->getActiveSheet()->setCellValue('D'.$i, $descuento);
            $objPHPExcel->getActiveSheet()->setCellValue('E'.$i, round( number_format($admon, 2, '.', ''),2) );
            $objPHPExcel->getActiveSheet()->setCellValue('F'.$i, round( number_format($total, 2, '.', ''),2) );
            $objPHPExcel->getActiveSheet()->getStyle('D'.$i.':F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            $i++;
        }
        $objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':C'.$i);
        $objPHPExcel->getActiveSheet()->setCellValue('A'.$i, "Total a pagar $descripAcreedor");
        $objPHPExcel->getActiveSheet()->setCellValue('D'.$i, $subTotalMonto);
        $objPHPExcel->getActiveSheet()->setCellValue('E'.$i, round( number_format($subTotalAdmon, 2, '.', ''),2) );
        $objPHPExcel->getActiveSheet()->setCellValue('F'.$i, round( number_format($subTotalTotal, 2, '.', ''),2) );
        
        $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':F'.$i)->getFont()->setSize(10)->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $objPHPExcel->getActiveSheet()->getStyle('D'.$i.':F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $i=$i+2;
    }
    $i=$i+2;
    /* TOTAL DESCUENTOS  COMERCIALES */
    $totalMontoGen = $totalMonto;
    $totalAdmonGen = $totalAdmon;
    $totalTotalGen = $totalTotal;
    $objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':C'.$i);
    $objPHPExcel->getActiveSheet()->setCellValue('A'.$i, "TOTAL DESCUENTOS  COMERCIALES");
    $objPHPExcel->getActiveSheet()->setCellValue('D'.$i, $totalMonto);
    $objPHPExcel->getActiveSheet()->setCellValue('E'.$i, round( number_format($totalAdmon, 2, '.', ''),2) );
    $objPHPExcel->getActiveSheet()->setCellValue('F'.$i, round( number_format($totalTotal, 2, '.', ''),2) );
            
    $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':F'.$i)->getFont()->setSize(12)->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
    $objPHPExcel->getActiveSheet()->getStyle('D'.$i.':F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

    $i=$i+2;
    /* DESCUENTOS DE EMPRESA */
    //and codcon NOT IN (527,528,529,530,531,533,534,535) excluir conceptos
    $sql1 = "select distinct descrip,codcon from nom_movimientos_nomina where codcon IN (".$conceptos['a_empresa'].")
    and codnom in (SELECT np.codnom  
   FROM nom_nominas_pago np 
   WHERE np.tipnom=$codtip AND np.periodo_ini BETWEEN '$fecha_inicio' AND '$fecha_fin')";
   $res1=$conexion->query($sql1);

   
   $totalMonto = 0;
   $totalAdmon = 0;
   $totalTotal = 0;

   while($prestamo=$res1->fetch_array())
   {    
       $descripAcreedor = utf8_encode($prestamo['descrip']);
       $codcon = $prestamo['codcon'];

       $objPHPExcel->getActiveSheet()->mergeCells('B'.$i.':C'.$i);
       $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':F'.$i)->getFont()->setSize(14)->setBold(true);

       $objPHPExcel->getActiveSheet()->setCellValue('B'.$i, strtoupper("ACREEDOR: ".$descripAcreedor));

       $objPHPExcel->getActiveSheet()->setCellValue('D'.$i, "Total Descontado");
       $objPHPExcel->getActiveSheet()->setCellValue('E'.$i, "Gasto Admin (2%)");
       $objPHPExcel->getActiveSheet()->setCellValue('F'.$i, "Total a Pagar");
       $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

       $i++;

       $sql3 = "SELECT a.codnom,a.tipnom,a.ficha,b.apenom,b.cedula,d.gastos_admon, 
       COALESCE(( SUM(IF(a.codcon in ($codcon),a.monto,0)) ),0) as descuento, 
       COALESCE(( SUM(IF(a.codcon in ($codcon),a.monto,0)) ),0) as gasto_adm, 
       COALESCE(( SUM(IF(a.codcon in ($codcon),a.monto,0)) ),0) as total_pago 
       FROM nom_movimientos_nomina a 
       LEFT JOIN nompersonal b ON a.ficha=b.ficha 
       LEFT JOIN nomprestamos_cabecera d ON a.numpre=d.numpre 
       WHERE a.codcon='$codcon' AND a.monto != 0 AND a.codnom IN (
       SELECT np.codnom FROM nom_nominas_pago np 
       WHERE np.tipnom=$codtip AND np.periodo_ini BETWEEN '$fecha_inicio' AND '$fecha_fin') 
       GROUP BY a.ficha,a.numpre ORDER BY a.ficha,a.codnom";
       
       //echo $sql3;exit;
       $res3=$conexion->query($sql3);
       $subTotalMonto = 0;
       $subTotalAdmon = 0;
       $subTotalTotal = 0;

       while($empleado=$res3->fetch_array())
       {    
           $cedula = $empleado['cedula'];
           $apenom = utf8_encode($empleado['apenom']);
           //$objPHPExcel->getActiveSheet()->mergeCells('B'.$i.':C'.$i);
           $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':F'.$i)->getFont()->setSize(10);
           $objPHPExcel->getActiveSheet()->setCellValue('B'.$i, strtoupper($cedula));
           $objPHPExcel->getActiveSheet()->setCellValue('C'.$i, strtoupper($apenom));
           $objPHPExcel->getActiveSheet()->getStyle('B'.$i.':C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
           
           $descuento = $empleado['descuento'];
           $admon = round( $descuento * ($empleado['gastos_admon'] / 100) ,2);
           $total = round( $descuento - $admon ,2);
           
           $subTotalMonto = $subTotalMonto + $descuento;
           $subTotalAdmon = $subTotalAdmon + $admon;
           $subTotalTotal = $subTotalTotal + $total;

           $totalMonto = $totalMonto + $descuento;
           $totalAdmon = $totalAdmon + $admon;
           $totalTotal = $totalTotal + $total;

           $objPHPExcel->getActiveSheet()->setCellValue('D'.$i, $descuento);
           $objPHPExcel->getActiveSheet()->setCellValue('E'.$i, round( number_format($admon, 2, '.', ''),2) );
           $objPHPExcel->getActiveSheet()->setCellValue('F'.$i, round( number_format($total, 2, '.', ''),2) );
           $objPHPExcel->getActiveSheet()->getStyle('D'.$i.':F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
           $i++;
       }
       $objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':C'.$i);
       $objPHPExcel->getActiveSheet()->setCellValue('A'.$i, "Total a pagar $descripAcreedor");
       $objPHPExcel->getActiveSheet()->setCellValue('D'.$i, $subTotalMonto);
       $objPHPExcel->getActiveSheet()->setCellValue('E'.$i, round( number_format($subTotalAdmon, 2, '.', ''),2) );
       $objPHPExcel->getActiveSheet()->setCellValue('F'.$i, round( number_format($subTotalTotal, 2, '.', ''),2) );
       
       $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':F'.$i)->getFont()->setSize(10)->setBold(true);
       $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
       $objPHPExcel->getActiveSheet()->getStyle('D'.$i.':F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
       $i=$i+2;
   }
   $i=$i+2;
   /* TOTAL DESCUENTOS DE EMPRESA */
   
   $totalMontoGen += $totalMonto;
   $totalAdmonGen += $totalAdmon;
   $totalTotalGen += $totalTotal;
   $objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':C'.$i);
   $objPHPExcel->getActiveSheet()->setCellValue('A'.$i, "TOTAL DESCUENTOS DE EMPRESA");
   $objPHPExcel->getActiveSheet()->setCellValue('D'.$i, $totalMonto);
   $objPHPExcel->getActiveSheet()->setCellValue('E'.$i, round( number_format($totalAdmon, 2, '.', ''),2) );
   $objPHPExcel->getActiveSheet()->setCellValue('F'.$i, round( number_format($totalTotal, 2, '.', ''),2) );
           
   $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':F'.$i)->getFont()->setSize(12)->setBold(true);
   $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
   $objPHPExcel->getActiveSheet()->getStyle('D'.$i.':F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

   $i=$i+2;
   
   $objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':C'.$i);
   $objPHPExcel->getActiveSheet()->setCellValue('A'.$i, "TOTAL GENERAL");
   $objPHPExcel->getActiveSheet()->setCellValue('D'.$i, $totalMontoGen);
   $objPHPExcel->getActiveSheet()->setCellValue('E'.$i, round( number_format($totalAdmonGen, 2, '.', ''),2) );
   $objPHPExcel->getActiveSheet()->setCellValue('F'.$i, round( number_format($totalTotalGen, 2, '.', ''),2) );
           
   $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':F'.$i)->getFont()->setSize(12)->setBold(true);
   $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
   $objPHPExcel->getActiveSheet()->getStyle('D'.$i.':F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);


    //$objPHPExcel->getActiveSheet()->getStyle('B')->setAutoSize(true);
    //$objPHPExcel->getActiveSheet()->getStyle('C')->setAutoSize(true);
    //===========================================================================
    $objPHPExcel->setActiveSheetIndex(0); 
    $objPHPExcel->getActiveSheet()->setSelectedCells('I30');
    $filename = "Descuentos_Del_".fecha($desde).'_Hasta_'.fecha($hasta);

    // Redirect output to a client’s web browser (Excel5)
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="'.$filename.'.xls"');
    header('Cache-Control: max-age=0');
    // If you're serving to IE 9, then the following may be needed
    header('Cache-Control: max-age=1');

    // If you're serving to IE over SSL, then the following may be needed
    header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
    header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
    header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
    header ('Pragma: public'); // HTTP/1.0

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    /* Limpiamos el búfer */
    ob_end_clean();
    $objWriter->save('php://output');
}
exit;
