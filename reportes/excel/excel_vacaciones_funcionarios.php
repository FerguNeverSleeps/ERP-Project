<?php
require_once("../../nomina/lib/common.php");
require_once('../../nomina/lib/database.php');
require_once('../utilidades/funciones_reportes.php');
require_once '../../includes/phpexcel/Classes/PHPExcel.php';
$db       = new Database($_SESSION['bd']);

$sql      = "SELECT * FROM nomnivel1";
$res      = $db->query($sql,$conexion);
$rowCount = 2;
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
        'size'  => 14,
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

$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(30);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(40);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(40);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(40);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(40);
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(50);


while ($nivel1  = $res->fetch_assoc())
{
	$objPHPExcel->getActiveSheet()->mergeCells('A'.$rowCount.':I'.$rowCount);
	$objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, $nivel1['descrip']);
	$objPHPExcel->getActiveSheet()->getStyle('A'.$rowCount.':I'.$rowCount)->applyFromArray($titulo);
	$objPHPExcel->getActiveSheet()->getStyle('A'.$rowCount.':I'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$rowCount++;
	$i++;
	$sql2 = "SELECT * from nomnivel2";
	$res2 = $db->query($sql2);
	//echo $nivel1["descrip"],"<br>";

	$objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, "APELLIDOS Y NOMBRES");
	$objPHPExcel->getActiveSheet()->getStyle('A'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, "CEDULA");
    $objPHPExcel->getActiveSheet()->getStyle('B'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, "FECHA INGRESO");
    $objPHPExcel->getActiveSheet()->getStyle('C'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, "POSICION");
    $objPHPExcel->getActiveSheet()->getStyle('D'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, "TIEMPO COMPENSATORIO");
    $objPHPExcel->getActiveSheet()->getStyle('E'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, "INCAPACIDAD");
    $objPHPExcel->getActiveSheet()->getStyle('F'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowCount, "INCAPACIDAD POR DISCAPACIDAD");
	$objPHPExcel->getActiveSheet()->getStyle('G'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowCount, "VACACIONES");
	$objPHPExcel->getActiveSheet()->getStyle('H'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->SetCellValue('I'.$rowCount, "INCAPACIDAD POR FAMILIAR DISCAPACITADO");
    $objPHPExcel->getActiveSheet()->getStyle('I'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

	$objPHPExcel->getActiveSheet()->getStyle('A'.$rowCount.':I'.$rowCount)->applyFromArray($borders);

    $rowCount++;

	while ($nivel2 = $res2->fetch_assoc())
	{
		$sql3        = "SELECT np.nombres,np.apellidos, np.cedula,np.fecing,np.nomposicion_id,np.usuario_workflow,np.useruid,dias.tiempo 
        from nompersonal as np LEFT JOIN dias_incapacidad as dias on (np.useruid=dias.usr_uid)
        LEFT JOIN tipo_justificacion as tj on (tj.idtipo=dias.tipo_justificacion)
        where np.codnivel1 = '".$nivel1["codorg"]."' AND np.codnivel2 = '".$nivel2["codorg"]."' AND (np.estado != 'Egresado' OR np.estado != 'De Baja')";
        //echo $sql3,"<br>";
		$res3        = $db->query($sql3);
		$funcionario = $res3->fetch_assoc();
		$apenom = $funcionario["apellidos"]." , ".$funcionario["nombres"];
		$objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, utf8_decode($apenom));
		$objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, $funcionario["cedula"]);
		$objPHPExcel->getActiveSheet()->getStyle('B'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, $funcionario["fecing"]);
		$objPHPExcel->getActiveSheet()->getStyle('C'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, $funcionario["nomposicion_id"]);
		$objPHPExcel->getActiveSheet()->getStyle('D'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, $funcionario["tiempo"]);
		$objPHPExcel->getActiveSheet()->getStyle('E'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, "");
        $objPHPExcel->getActiveSheet()->getStyle('F'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowCount, "");
        $objPHPExcel->getActiveSheet()->getStyle('G'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);  
        $objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowCount, "");
        $objPHPExcel->getActiveSheet()->getStyle('H'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);        
        $objPHPExcel->getActiveSheet()->SetCellValue('I'.$rowCount, "");
        $objPHPExcel->getActiveSheet()->getStyle('I'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);        
		$objPHPExcel->getActiveSheet()->getStyle('A'.$rowCount.':I'.$rowCount)->applyFromArray($fonts);
		$rowCount++;

		//echo $funcionario["apenom"]," ",$funcionario["fecing"]," ",$funcionario["cedula"]," ",$funcionario["usuario_workflow"]," ",$funcionario["usruid"],"<br>";
	}
	$rowCount++;
	$rowCount++;

	//echo "<br>";


}
/*
//------------------------------------------------------------
$sql     = "SELECT * FROM nompersonal
            WHERE tiene_discapacidad = 1 AND estado != 'Egresado' AND estado != 'De Baja'";
$res     = $db->query($sql);
//------------------------------------------------------------
$dias = array('Lun','Mar','Mie','Jue','Vie','Sab','Dom');
//------------------------------------------------------------
//Estilos para filas
$borders1 = array(
    'borders' => array(
        'allborders' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN,
            'color' => array(
                'argb' => 'FF000000'
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
            ),
        )
    ),
    'font'  => array(
    'color' => array('rgb' => 'FF000000'),
    'size'  => 11,
    'name'  => 'Arial'
    )
);
//------------------------------------------------------------

//Fin encabezado tabla
$rowCount = 6;
$i=1;
while($datos = mysqli_fetch_array($res))
{
    $objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, $i);
    $objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, $datos['nombres']);
    $objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, $datos['apellidos']);
    $objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, $datos['cedula']);
    $objPHPExcel->getActiveSheet()->getStyle("B".$rowCount.':'."E".$rowCount)->applyFromArray($borders1);
    $rowCount++;
    $i++;
}*/
$objPHPExcel->getActiveSheet()->setTitle('Vacaciones de Funcionarios');

header('Content-Type: application/vnd.openxmlformats-   officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="funcionarios_vacaciones.xlsx"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
?>