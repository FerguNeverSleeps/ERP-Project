<?php
session_start();
error_reporting(0);
ini_set('display_errors', FALSE);
ini_set('display_startup_errors', FALSE);
date_default_timezone_set('America/Panama');

if (PHP_SAPI == 'cli')
	die('Este ejemplo solo se debe ejecutar desde un navegador Web');

include('../lib/common.php');
include('lib/php_excel.php');


if(isset($_GET['codnom']) && isset($_GET['codtip']))
{
	$codnom=$_GET['codnom'];
	$codtip=$_GET['codtip'];

	$conexion= new bd($_SESSION['bd']);

	$sql = "SELECT UPPER(t.descrip) as descrip 
			FROM   nomtipos_nomina t
			WHERE  t.codtip=".$codtip;
	$res=$conexion->query($sql);

	if($fila=$res->fetch_array())
	{
		$NOMINA = $fila['descrip']; 
	}

    require_once 'phpexcel/Classes/PHPExcel.php';

    $objPHPExcel = new PHPExcel();
    $objPHPExcel = PHPExcel_IOFactory::load("plantillas/descuentos_mmd.xlsx");

    $objPHPExcel->getProperties()->setCreator("Selectra")
                                ->setLastModifiedBy("Selectra")
                                ->setTitle("Horizontal de Planilla");

    $objPHPExcel->getDefaultStyle()->getFont()->setName('Calibri');
    // $objPHPExcel->getActiveSheet()->getStyle('A2:A4')->getFont()->setName('Calibri');
    $objPHPExcel->getDefaultStyle()->getFont()->setSize(11);


    $sql = "SELECT e.nom_emp as empresa, e.rif, e.tel_emp as telefono, e.dir_emp as direccion, e.ciu_emp as ciudad, 
                e.edo_emp, e.imagen_izq as logo
            FROM   nomempresa e";
    $res=$conexion->query($sql);
    $fila=$res->fetch_array();
    $logo=$fila['logo'];

    $sql2 = "SELECT np.periodo_ini as desde, np.periodo_fin as hasta, np.mes, np.anio,
                    DATE_FORMAT(np.periodo_ini,'%d') as dia_ini, DATE_FORMAT(np.periodo_fin, '%d') as dia_fin   
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
                ->setCellValue('C5', 'DEL '. $dia_ini .' AL '. $dia_fin .' de '. $mes_letras .' '. $anio );

    $objPHPExcel->getActiveSheet()->getStyle('C2:F5')->getFont()->setSize(14);
    $objPHPExcel->getActiveSheet()->getStyle('C2:F5')->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('C2:F5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->mergeCells('C2:F2');
    $objPHPExcel->getActiveSheet()->mergeCells('C3:F3');
    $objPHPExcel->getActiveSheet()->mergeCells('C4:F4');
    $objPHPExcel->getActiveSheet()->mergeCells('C5:F5');

    $objPHPExcel->getActiveSheet()->freezePane('A6'); // Inmovilizar Paneles

    $sql1 = "select distinct descrip from nom_movimientos_nomina where codcon >= 500 and codcon < 600";

    $res1=$conexion->query($sql1);

    $i=7; $nivel=1;

    while($prestamo=$res1->fetch_array())
    {    
        $descripAcreedor = utf8_encode($prestamo['descrip']);
        $objPHPExcel->getActiveSheet()->mergeCells('B'.$i.':C'.$i);
        $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':F'.$i)->getFont()->setSize(14)->setBold(true);

        $objPHPExcel->getActiveSheet()->setCellValue('B'.$i, strtoupper("ACREEDOR: ".$descripAcreedor));

        $objPHPExcel->getActiveSheet()->setCellValue('D'.$i, "T. Descontado");
        $objPHPExcel->getActiveSheet()->setCellValue('E'.$i, "Gasto Admon");
        $objPHPExcel->getActiveSheet()->setCellValue('F'.$i, "Total a Pagar");
        $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        
        $sql2 = "select distinct b.codorg,b.descrip from nompersonal a 
        left join nomnivel1 b on a.codnivel1=b.codorg 
        where a.ficha in (select distinct c.ficha from nom_movimientos_nomina c 
        where c.descrip = '$descripAcreedor') order by b.codorg";
        $res2=$conexion->query($sql2);
        $i++;

        while($nomnivel1=$res2->fetch_array())
        {    
            $codNivel1 = $nomnivel1['codorg'];
            $centroCostoDescrip = utf8_encode($nomnivel1['descrip']);
            $objPHPExcel->getActiveSheet()->mergeCells('B'.$i.':C'.$i);
            $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':F'.$i)->getFont()->setSize(12)->setBold(true);
            $objPHPExcel->getActiveSheet()->setCellValue('B'.$i, strtoupper($centroCostoDescrip));
            $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            $i++;

            $sql3 = "select a.apenom,a.cedula,c.codnom,c.codcon,c.descrip,c.monto from nompersonal a 
            left join nomnivel1 b on a.codnivel1=b.codorg 
            left join nom_movimientos_nomina c on a.codnivel1=b.codorg
            where c.descrip = '$descripAcreedor' and b.codorg = '$codNivel1' and c.codnom = '$codnom'";
            $res3=$conexion->query($sql3);
            $subTotalMonto = 0;
            $subTotalAdmon = 0;
            $subTotalTotal = 0;

            while($empleado=$res3->fetch_array())
            {    
                $cedula = $empleado['cedula'];
                $apenom = utf8_encode($empleado['apenom']);
                $objPHPExcel->getActiveSheet()->mergeCells('B'.$i.':C'.$i);
                $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':F'.$i)->getFont()->setSize(10);
                $objPHPExcel->getActiveSheet()->setCellValue('B'.$i, strtoupper($cedula." ".$apenom));
                
                $monto = $empleado['monto'];
                $admon = $monto * 0.02;
                $total = $monto + $admon;
                
                $subTotalMonto = $subTotalMonto + $monto;
                $subTotalAdmon = $subTotalAdmon + $admon;
                $subTotalTotal = $subTotalTotal + $total;

                $objPHPExcel->getActiveSheet()->setCellValue('D'.$i, $monto);
                $objPHPExcel->getActiveSheet()->setCellValue('E'.$i, $admon);
                $objPHPExcel->getActiveSheet()->setCellValue('F'.$i, $total);
                $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $i++;
            }
            $objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':C'.$i);
            $objPHPExcel->getActiveSheet()->setCellValue('A'.$i, "Total a pagar $descripAcreedor");
            $objPHPExcel->getActiveSheet()->setCellValue('D'.$i, $subTotalMonto);
            $objPHPExcel->getActiveSheet()->setCellValue('E'.$i, $subTotalAdmon);
            $objPHPExcel->getActiveSheet()->setCellValue('F'.$i, $subTotalTotal);
            
            $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':F'.$i)->getFont()->setSize(10)->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            $objPHPExcel->getActiveSheet()->getStyle('D'.$i.':F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $i++;
        }
    }

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
    $objWriter->save('php://output');
}
exit;
