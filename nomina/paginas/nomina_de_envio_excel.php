<?php
	/** Error reporting */
	error_reporting(E_ALL);
	ini_set('display_errors', TRUE);
	ini_set('display_startup_errors', TRUE);
	date_default_timezone_set('Europe/London');

	if (PHP_SAPI == 'cli')
		die('This example should only be run from a Web Browser');

	require_once '../lib/common.php';
	// include ("func_bd.php");
	require_once 'phpexcel/Classes/PHPExcel.php';

	// Crear un nuevo objeto PHPExcel
	$objPHPExcel = new PHPExcel();

	// Agregar una hoja de cálculo
	$objPHPExcel->setActiveSheetIndex(0);
	$sheet = $objPHPExcel->getActiveSheet();

	// Agregar encabezados de columnas y aplicar formato en negrita y centrado
	$sheet->setCellValue('A1', 'Cedula')->getStyle('A1')->getFont()->setBold(true);
	$sheet->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$sheet->setCellValue('B1', 'Colaborador')->getStyle('B1')->getFont()->setBold(true);
	$sheet->getStyle('B1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$sheet->setCellValue('C1', 'Valor Base')->getStyle('C1')->getFont()->setBold(true);
	$sheet->getStyle('C1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$sheet->setCellValue('D1', 'Porcentaje')->getStyle('D1')->getFont()->setBold(true);
	$sheet->getStyle('D1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$sheet->setCellValue('E1', 'Proyecto / Centro de Costo')->getStyle('E1')->getFont()->setBold(true);
	$sheet->getStyle('E1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$sheet->setCellValue('F1', 'Cuenta Mayor')->getStyle('F1')->getFont()->setBold(true);
	$sheet->getStyle('F1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$sheet->setCellValue('G1', 'Cuenta Contable')->getStyle('G1')->getFont()->setBold(true);
	$sheet->getStyle('G1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$sheet->setCellValue('H1', 'Concepto')->getStyle('H1')->getFont()->setBold(true);
	$sheet->getStyle('H1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$sheet->setCellValue('I1', 'Valor Real')->getStyle('I1')->getFont()->setBold(true);
	$sheet->getStyle('I1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$sheet->setCellValue('J1', 'Fecha Inicio')->getStyle('J1')->getFont()->setBold(true);
	$sheet->getStyle('J1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$sheet->setCellValue('K1', 'Fecha Fin')->getStyle('K1')->getFont()->setBold(true);
	$sheet->getStyle('K1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);


	// Cambiar el ancho de las columnas
	$sheet->getColumnDimension('A')->setWidth(10);  // Cedula
	$sheet->getColumnDimension('B')->setWidth(42);  // Colaborador
	$sheet->getColumnDimension('C')->setWidth(11);  // Valor Base
	$sheet->getColumnDimension('D')->setWidth(11);  // Porcentaje
	$sheet->getColumnDimension('E')->setWidth(48);  // Proyecto Centro de Costo
	$sheet->getColumnDimension('F')->setWidth(13);  // Cuenta MayoR
	$sheet->getColumnDimension('G')->setWidth(16);  // Cuenta Contable
	$sheet->getColumnDimension('H')->setWidth(38);  // Concepto
	$sheet->getColumnDimension('I')->setWidth(11);  // Valor Real
	$sheet->getColumnDimension('J')->setWidth(11);  // Fecha Inicio
	$sheet->getColumnDimension('K')->setWidth(11);  // Fecha Fin


	$codigoNomina = $_GET['codnom'];
	$conexion = new bd($_SESSION['bd']);

	// Tu consulta SQL
	$sql = "SELECT npc.cedula, np.apenom, npc.quincena AS valor_base, npc.porcentaje, LEFT(npc.cuenta_contable, 4) AS mayorCtaCon,
			        npc.cuenta_contable, npc.concepto, npc.monto AS valor_real,
			        npc.fecha_inicio, npc.fecha_fin, npc.codigo_proyecto, pc.descripcion
			    FROM nomina_proyecto_cc npc
			        INNER JOIN nompersonal np ON np.cedula = npc.cedula
			        INNER JOIN nom_nominas_pago nmp ON nmp.codnom = npc.cod_nomina
			        LEFT JOIN proyecto_cc pc ON pc.codigo = npc.codigo_proyecto
			    WHERE npc.cod_nomina = $codigoNomina
			    ORDER BY npc.cod_nomina DESC, npc.cedula, npc.ID ASC";
	$res = $conexion->query($sql, "utf8");

	$row = 2; // Comenzar desde la fila 2 para los datos

	while ($fila = $res->fetch_assoc()) {
    $cedula = $fila['cedula'];
    $apenom = $fila['apenom'];
    $valor_base = $fila['valor_base'];
    $porcentaje = $fila['porcentaje'];
    $descripcion = $fila['descripcion'];
    $mayorCtaCon = $fila['mayorCtaCon'];
    $cuenta_contable = $fila['cuenta_contable'];
    $concepto = $fila['concepto'];
    $valor_real = $fila['valor_real'];
    $fecha_inicio = $fila['fecha_inicio'];
    $fecha_fin = $fila['fecha_fin'];

    if ($fila['codigo_proyecto'] != 'ADMINISTRATIVO') {
        $descripcion = $fila['codigo_proyecto'] . " - " . $fila['descripcion'];
    }

    // Agregar los datos a la hoja de cálculo
    $sheet->setCellValue("A$row", $cedula);
    $sheet->setCellValue("B$row", $apenom);
    $sheet->setCellValue("C$row", $valor_base);
    $sheet->setCellValue("D$row", $porcentaje);
    $sheet->setCellValue("E$row", $descripcion);
    $sheet->setCellValue("F$row", $mayorCtaCon);
    $sheet->setCellValue("G$row", $cuenta_contable);
    $sheet->setCellValue("H$row", $concepto);
    $sheet->setCellValue("I$row", $valor_real);
    $sheet->setCellValue("J$row", $fecha_inicio);
    $sheet->setCellValue("K$row", $fecha_fin);

    $row++;
	}

	// Configurar el encabezado de respuesta para la descarga del archivo Excel
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="archivo_excel.xlsx"');
	header('Cache-Control: max-age=0');

	// Crear un objeto Writer y guardar el archivo Excel en la salida
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save('php://output');
