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


if(isset($_GET['codnom']) && isset($_GET['codtip']))
{
	$codnom=$_GET['codnom'];
    $codtip=$_GET['codtip'];

	$conexion= new bd($_SESSION['bd']);
    
    //cargar configuracion de reporte
    $sql1 = "select * from config_reportes_planilla where id = 5";
    $res=$conexion->query($sql1);
    $configReporte=$res->fetch_array();
    $sql_reporte = $configReporte['sql_reporte'];

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
            WHERE  np.codnom=".$codnom." AND np.tipnom=".$codtip;
    $res2=$conexion->query($sql2);
    $fila2=$res2->fetch_array();

    $meses = array('Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre');

    $desde=$fila2['desde'];
    $hasta=$fila2['hasta'];
    $dia_ini = $fila2['dia_ini'];
    $dia_fin = $fila2['dia_fin']; 
    $mes_numero = $fila2['mes'];
    $mes_letras = $meses[$mes_numero - 1];
    $anio = $fila2['anio'];

    $empresa = utf8_encode($fila['empresa']);
    $objPHPExcel->getActiveSheet()
                ->setCellValue('C2', strtoupper($empresa) )
                ->setCellValue('C3', 'Descuentos por Acreedor')
                ->setCellValue('C4', 'Detallada por Centro de Costo')
                ->setCellValue('C5', 'Hasta el '. $dia_fin .' del '. $anio );

    $objPHPExcel->getActiveSheet()->getStyle('C2:F5')->getFont()->setSize(14);
    $objPHPExcel->getActiveSheet()->getStyle('C2:F5')->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('C2:F5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->mergeCells('C2:F2');
    $objPHPExcel->getActiveSheet()->mergeCells('C3:F3');
    $objPHPExcel->getActiveSheet()->mergeCells('C4:F4');
    $objPHPExcel->getActiveSheet()->mergeCells('C5:F5');

    $objPHPExcel->getActiveSheet()->freezePane('A6'); // Inmovilizar Paneles

    $i=7; $nivel=1;
    $totalCuotas = 0;
    $totalSaldos = 0;
    $totalAcreedor = 0;
  

    $objPHPExcel->getActiveSheet()->mergeCells('B'.$i.':C'.$i);
    $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':F'.$i)->getFont()->setSize(14)->setBold(true);

    $objPHPExcel->getActiveSheet()->setCellValue('B'.$i, strtoupper(""));

    $objPHPExcel->getActiveSheet()->setCellValue('D'.$i, "ACREEDOR AHORRO");
    $objPHPExcel->getActiveSheet()->setCellValue('E'.$i, "CUOTA AHORRO");
    $objPHPExcel->getActiveSheet()->setCellValue('F'.$i, "SALDO AHORRO");
    $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
    
    $sql2 = "select distinct a.codorg,a.descrip 
    from nomnivel1 a inner join nom_movimientos_nomina b on a.codorg=b.codnivel1 where b.codcon= 590 and b.codnom=$codnom order by a.codorg";
    $res2=$conexion->query($sql2);
    $i++;
    $totalCuotas = 0;
    $totalSaldos = 0;

    while($nomnivel1=$res2->fetch_array())
    {    
        $codNivel1 = $nomnivel1['codorg'];
        $centroCostoDescrip = utf8_encode($nomnivel1['descrip']);
        $objPHPExcel->getActiveSheet()->mergeCells('B'.$i.':C'.$i);
        $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':F'.$i)->getFont()->setSize(12)->setBold(true);
        $objPHPExcel->getActiveSheet()->setCellValue('B'.$i, strtoupper($centroCostoDescrip));
        $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $i++;

        
        //$sql3 = str_replace("replace",$where,$sql_reporte);
        $sql3 = "SELECT a.ficha FROM `nom_movimientos_nomina` a WHERE `codnom` = $codnom AND `codcon` = 590 and a.codnivel1='$codNivel1'";
        $res3=$conexion->query($sql3);
        $subTotalCuotas = 0;
        $subTotalSaldos = 0;

        while($empleado=$res3->fetch_array())
        {    
            $ficha = $empleado['ficha'];
            
            $replacenom = $ficha." AND codnom=".$codnom;
            $where = "a.ficha = " . $ficha . " AND d.codnom=" . $codnom;
        
            $sql4 = str_replace("replacenom",$replacenom,$sql_reporte);
            $sql4 = str_replace("replacecam",$ficha,$sql4);
            $sql4 = str_replace("replace",$where,$sql4);
            $res4=$conexion->query($sql4);
            
            
            if($res4){
                $ahorros=$res4->fetch_array();
                $concepto_ahorro = $ahorros['concepto_ahorro'];
                $acreedor_ahorro = $ahorros['acreedor_ahorro'];
                $cuota_ahorro = $ahorros['cuota_ahorro'];
                $saldo_ahorro = $ahorros['saldo_ahorro'];

                $subTotalCuotas = $subTotalCuotas + $cuota_ahorro;
                $subTotalSaldos = $subTotalSaldos + $saldo_ahorro;
                
                $cedula = $ahorros['cedula'];
                $apenom = utf8_encode($ahorros['apenom']);
                
                $objPHPExcel->getActiveSheet()->mergeCells('B'.$i.':C'.$i);
                $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':F'.$i)->getFont()->setSize(10);
                $objPHPExcel->getActiveSheet()->setCellValue('B'.$i, strtoupper($cedula." ".$apenom));

                $objPHPExcel->getActiveSheet()->setCellValue('D'.$i, $acreedor_ahorro );
                $objPHPExcel->getActiveSheet()->setCellValue('E'.$i, $cuota_ahorro );
                $objPHPExcel->getActiveSheet()->setCellValue('F'.$i, $saldo_ahorro );
                $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $i++;
            }
        }

        $totalCuotas = $totalCuotas + $subTotalCuotas;
        $totalSaldos = $totalSaldos + $subTotalSaldos;
        
        $objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':C'.$i);
        $objPHPExcel->getActiveSheet()->setCellValue('A'.$i, "SubTotal Ahorrado $centroCostoDescrip");
        $objPHPExcel->getActiveSheet()->setCellValue('E'.$i, $subTotalCuotas);
        $objPHPExcel->getActiveSheet()->setCellValue('F'.$i, $subTotalSaldos);
        
        $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':F'.$i)->getFont()->setSize(10)->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $objPHPExcel->getActiveSheet()->getStyle('D'.$i.':F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $i++;
    }
    $i=$i+2;
    $objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':C'.$i);
    $objPHPExcel->getActiveSheet()->setCellValue('A'.$i, "GRAN TOTAL AHORRADO");
    $objPHPExcel->getActiveSheet()->setCellValue('E'.$i, $totalCuotas);
    $objPHPExcel->getActiveSheet()->setCellValue('F'.$i, $totalSaldos);
            
    $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':F'.$i)->getFont()->setSize(12)->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
    $objPHPExcel->getActiveSheet()->getStyle('D'.$i.':F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    //===========================================================================
    $objPHPExcel->setActiveSheetIndex(0); 
    $objPHPExcel->getActiveSheet()->setSelectedCells('I30');
    $filename = "Ahorros_Hasta_".fecha($hasta);

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
