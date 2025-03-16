<?php
require_once("../../nomina/lib/common.php");
require_once('../../nomina/lib/database.php');
require_once('../utilidades/funciones_reportes.php');
require_once '../../includes/phpexcel/Classes/PHPExcel.php';

$db          = new Database($_SESSION['bd']);
 $tipo_empleado = (isset($_GET['tipo_empleado'])) ? $_GET['tipo_empleado'] : 0 ;
$objPHPExcel = new PHPExcel();

$titulo = array(
    'borders' => array(
        'allborders' => array(
          'style' => PHPExcel_Style_Border::BORDER_THIN,
          'color' => array('argb' => 'FF000000'),
    'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
    )
    ),
    'font'  => array(
        'bold'  => true,
        'color' => array('rgb' => '000000'),
        'size'  => 16,
        'name'  => 'Arial',
        'center' => true
    )
);
$borders = array(
    'borders' => array(
        'allborders' => array(
          'style' => PHPExcel_Style_Border::BORDER_THIN,
          'color' => array('argb' => 'FF000000'),
    'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
    )
    ),
    'font'  => array(
        'bold'  => true,
        'color' => array('rgb' => '000000'),
        'size'  => 12,
        'name'  => 'Calibri',
        'center' => true
    )
);

$fonts = array(
    'borders' => array(
        'allborders' => array(
          'style' => PHPExcel_Style_Border::BORDER_THIN,
          'color' => array('argb' => 'FF000000'),
    'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
    )
    ),
    'font'  => array(
        'bold'  => false,
        'color' => array('rgb' => '000000'),
        'size'  => 12,
        'name'  => 'Arial',
        'center' => true
    )
);

    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(50);
    $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(80);
    $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(80);
    $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(80);
    $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(25);
    $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(25);
    $rowCount    = 2;
    $objPHPExcel->getActiveSheet()->mergeCells('A'.$rowCount.':J'.$rowCount);
    $objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, "FUNCIONARIOS ".strtoupper($tipo_empleado));
    $objPHPExcel->getActiveSheet()->getStyle('A'.$rowCount.':J'.$rowCount)->applyFromArray($titulo);
    $objPHPExcel->getActiveSheet()->getStyle('A'.$rowCount.':J'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $rowCount++;

    $objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, "CÉDULA");
    $objPHPExcel->getActiveSheet()->getStyle('A'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, "NOMBRE");
    $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, "APELLIDOS");
    $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, "POSICION");
    $objPHPExcel->getActiveSheet()->getStyle('D'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, "FUNCION");
    $objPHPExcel->getActiveSheet()->getStyle('E'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, "DIRECCIÓN");
    $objPHPExcel->getActiveSheet()->getStyle('F'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowCount, "DEPARTAMENTO");
    $objPHPExcel->getActiveSheet()->getStyle('G'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowCount, "SECCIÓN");
    $objPHPExcel->getActiveSheet()->getStyle('H'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->SetCellValue('I'.$rowCount, "FECHA INGRESO");
    $objPHPExcel->getActiveSheet()->getStyle('I'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->SetCellValue('J'.$rowCount, "SALARIO");
    $objPHPExcel->getActiveSheet()->getStyle('J'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->getStyle('A'.$rowCount.':J'.$rowCount)->applyFromArray($borders);
    $rowCount++;
    $num++;

	$sql3        = "SELECT * ,nv1.descrip as direccion,nv2.descrip as departamento,nv3.descrip as seccion 
                   FROM nompersonal 
                        LEFT JOIN nomnivel1 as nv1 on (nompersonal.codnivel1=nv1.codorg) 
                   LEFT JOIN nomnivel2 as nv2 on (nompersonal.codnivel2=nv2.codorg)
                   LEFT JOIN nomnivel3 as nv3 on (nompersonal.codnivel3=nv3.codorg)
                   LEFT JOIN  nomfuncion ON nompersonal.nomfuncion_id=nomfuncion.nomfuncion_id
              
                   WHERE nomfuncion.descripcion_funcion LIKE '%$tipo_empleado%' AND nompersonal.estado<>'De Baja'";
   //echo $sql3,"<br>";exit(1);
	$res3        = $db->query($sql3);
	$num =  $res3 -> num_rows;
    if ($num < 1 ) 
    {
        echo "<script>alert('No hay registros para generar el reporte')</script>";
        echo "<script>location.href = '../../nomina/paginas/cfg_resumen_funcion.php';</script>";
    } 
    else 
    {
       while ( $funcionario = $res3->fetch_assoc()) 
        {
               
                    
            $apenom = $funcionario["apellidos"]." , ".$funcionario["nombres"];
            $objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, $funcionario["cedula"]);
            $objPHPExcel->getActiveSheet()->getStyle('A'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, ($funcionario["nombres"]));
            $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, ($funcionario["apellidos"]));
            $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, $funcionario["nomposicion_id"]);
            $objPHPExcel->getActiveSheet()->getStyle('D'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, $funcionario["descripcion_funcion"]);

            $objPHPExcel->getActiveSheet()->getStyle('E'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, $funcionario["direccion"]);
            $objPHPExcel->getActiveSheet()->getStyle('F'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowCount, $funcionario["departamento"]);
            $objPHPExcel->getActiveSheet()->getStyle('G'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowCount, $funcionario["seccion"]);
            $objPHPExcel->getActiveSheet()->getStyle('H'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->SetCellValue('I'.$rowCount, $funcionario["fecing"]);
            $objPHPExcel->getActiveSheet()->getStyle('I'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY);
            $objPHPExcel->getActiveSheet()->SetCellValue('J'.$rowCount, $funcionario["suesal"]);
            $objPHPExcel->getActiveSheet()->getStyle('J'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle('J'.$rowCount)->getNumberFormat()->setFormatCode('#,##0.00');

            $objPHPExcel->getActiveSheet()->getStyle('A'.$rowCount.':J'.$rowCount)->applyFromArray($fonts);
           
            $rowCount++;
        }

        $objPHPExcel->getActiveSheet()->setTitle('Funcionarios segun Funcion');

        header('Content-Type: application/vnd.openxmlformats-   officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="funcionarios_funcion.xlsx"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
    }
    
    
?>