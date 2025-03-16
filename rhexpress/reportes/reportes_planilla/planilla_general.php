<?php
session_start();
error_reporting(0);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('America/Panama');
//echo "hola planilla general";exit;
if (PHP_SAPI == 'cli')
	die('Este ejemplo solo se debe ejecutar desde un navegador Web');

include('../../../lib/common.php');
include('../../../nomina/paginas/lib/php_excel.php');

//echo "antes de entrar";exit;
if(isset($_GET['codnom']))
{
	$codnom=$_GET['codnom'];
	$codtip=1;

	$conexion= new bd($_SESSION['bd']);

	$sql = "SELECT UPPER(t.descrip) as descrip 
			FROM   nomtipos_nomina t
			WHERE  t.codtip=".$codtip;
	$res=$conexion->query($sql);

	if($fila=$res->fetch_array())
	{
		$NOMINA = $fila['descrip']; 
	}

require_once '../../../nomina/paginas/phpexcel/Classes/PHPExcel.php';

$objPHPExcel = new PHPExcel();
$objPHPExcel = PHPExcel_IOFactory::load("../../../nomina/paginas/plantillas/planilla_mmd.xlsx");

$objPHPExcel->getProperties()->setCreator("Selectra")
							 ->setLastModifiedBy("Selectra")
							 ->setTitle("Horizontal de Planilla");

$objPHPExcel->getDefaultStyle()->getFont()->setName('Calibri');
// $objPHPExcel->getActiveSheet()->getStyle('A2:A4')->getFont()->setName('Calibri');
$objPHPExcel->getDefaultStyle()->getFont()->setSize(11);

//cargar configuracion de reporte
$sql1 = "select * from config_reportes_planilla where id = 1";
$res=$conexion->query($sql1);
$configReporte=$res->fetch_array();
$sql_reporte = $configReporte['sql_reporte'];


$sql = "SELECT e.nom_emp as empresa, e.rif, e.tel_emp as telefono, e.dir_emp as direccion, e.ciu_emp as ciudad, 
               e.edo_emp, e.imagen_izq as logo
		FROM   nomempresa e";
$res=$conexion->query($sql);
$fila=$res->fetch_array();
$logo=$fila['logo'];

$sql2 = "SELECT np.periodo_ini as desde, np.periodo_fin as hasta, np.mes, np.anio,np.status,
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
$status = $fila2['status'];
if($status=='A')
	$titulo1 = strtoupper($configReporte['titulo1']);
else
	$titulo1 = strtoupper($configReporte['titulo3']);

$empresa = utf8_encode($fila['empresa']);
$objPHPExcel->getActiveSheet()
            ->setCellValue('C2', strtoupper($empresa) )
            ->setCellValue('C3', $titulo1)
            ->setCellValue('C4', strtoupper($configReporte['titulo2']))
            ->setCellValue('C5', 'Numero Nomina:'.$codnom.'    del '. $dia_ini .' al '. $dia_fin .' de '. $mes_letras .' '. $anio );

//$objPHPExcel->getActiveSheet()->getStyle('A2:A4')->getFont()->setName('Calibri');
$objPHPExcel->getActiveSheet()->getStyle('C2:P5')->getFont()->setSize(14);
$objPHPExcel->getActiveSheet()->getStyle('C2:P5')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('C2:P5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->mergeCells('C2:P2');
$objPHPExcel->getActiveSheet()->mergeCells('C3:P3');
$objPHPExcel->getActiveSheet()->mergeCells('C4:P4');
$objPHPExcel->getActiveSheet()->mergeCells('C5:P5');

//$objPHPExcel->getActiveSheet()->getStyle('C2:D5')->applyFromArray(allBordersThin());
//$objPHPExcel->getActiveSheet()->getStyle('C2:D5')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

$excelLetra = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V');
//columnas reporte
$sqlIng = "select * from config_reportes_planilla_columnas where visible = 1 and tipo = 0 and id_reporte =".$configReporte['id']." order by col_orden";
$resCol=$conexion->query($sqlIng);

$colExcelIni = 2;
$colExcelActual = 2;
$filaExcelIni = 8;
$filaExcelActual = 8;

$objPHPExcel->getActiveSheet()
            ->setCellValue('A'.$filaExcelActual, '# EMPLEADO')
            ->setCellValue('B'.$filaExcelActual, 'NOMBRE');

//Añadir columnas para ingresos
//$objPHPExcel->getActiveSheet()->setCellValue('C6', 'INGRESOS');

while($columnas1=$resCol->fetch_array()){
    $cell = $excelLetra[$colExcelActual].$filaExcelActual;
    $objPHPExcel->getActiveSheet()->setCellValue($cell, $columnas1['nombre']);
    $colExcelActual++;
}

$numColFinIng = $colExcelActual;
$colFinIng = $excelLetra[$colExcelActual].'8';
//cellColor('C6:'.$excelLetra[($numColFinIng-1)].'8', '92d050');

//descuentos de planilla
//$objPHPExcel->getActiveSheet()->setCellValue($excelLetra[$colExcelActual].'6', 'DEDUCCIONES');
$sqlDesc = "select * from config_reportes_planilla_columnas where  visible = 1 and tipo = 1 and id_reporte =".$configReporte['id']." order by col_orden asc";
$resCol=$conexion->query($sqlDesc);

$numColIniDescuentos = $colExcelActual;
$colIniDescuentos = $excelLetra[$colExcelActual].$filaExcelActual;
while($columnas2=$resCol->fetch_array()){
    $cell = $excelLetra[$colExcelActual].$filaExcelActual;
    $objPHPExcel->getActiveSheet()->setCellValue($cell, $columnas2['nombre']);
    $colExcelActual++;
}
$colFinDesc=$excelLetra[$colExcelActual].'8';
//cellColor($excelLetra[$numColIniDescuentos].'6:'.$excelLetra[($colExcelActual-2)].'8', 'ffcc00');
$objPHPExcel->getActiveSheet()->setCellValue($colFinDesc, '          ');
$objPHPExcel->getActiveSheet()->getStyle('A'.$filaExcelActual.':'.$colFinDesc)->getBorders()->getBottom()
					->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
					
$objPHPExcel->getActiveSheet()->getStyle('A6:'.$colFinDesc )->getFont()->setSize(12);
$objPHPExcel->getActiveSheet()->getStyle('A6:'.$colFinDesc )->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('A6:'.$colFinDesc )->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$objPHPExcel->getActiveSheet()->freezePane('C9'); // Inmovilizar Paneles

$sql = "SELECT nn.codorg, nn.descrip,
			   CASE WHEN nn.codorg=1 THEN 'Administrativos'
			        WHEN nn.codorg=2 THEN 'Técnicos'
			        WHEN nn.codorg=3 THEN 'Desarrollo Sostenible'
			        WHEN nn.codorg=4 THEN 'Comercialización' 
			   END as grupo
		FROM   nomnivel1 nn
		WHERE  nn.descrip NOT IN('Eventual', 'O&M')
		ORDER BY nn.codorg";
$res=$conexion->query($sql);

$i=9; $nivel=1;
// $monto_salarios=0;
$totalesAdmon = [];
$totalesOtros = [];
$granTotal = [];
$total_salarios=$total_comision=$total_combustible=$total_reembolso=$total_uso_auto=$total_bono=$total_incapacidad="=";
$total_extra_salario=$total_extra_gastosrep=$total_vacaciones=$total_cxc="=";
$total_segurosocial_salario=$total_tardanza=$total_seguro_educativo="=";
$total_isr_gastos=$total_isr_salario=$total_ausencia=$total_descuentos=$total_neto="=";
$total_salario=$MesXIII=$Total_Desc_Ley=$SeguroSocialMesXIII=$total_desc_varios="=";
while($row=$res->fetch_array())
{
	$codorg = $row['codorg'];

	$sql2= "SELECT np.ficha, CONCAT_WS(' ', np.nombres, np.apellidos) as nombre,
				   SUBSTRING_INDEX(SUBSTRING_INDEX(np.nombres, ' ', 1), ' ', -1) as primer_nombre,
				   CASE WHEN np.apellidos LIKE 'De %' THEN 
						     SUBSTRING_INDEX(SUBSTRING_INDEX(np.apellidos, ' ', 2), ' ',  2) 
				   ELSE      SUBSTRING_INDEX(SUBSTRING_INDEX(np.apellidos, ' ', 1), ' ', -1) 
				   END as primer_apellido
			FROM   nompersonal np
			WHERE  np.codnivel1=".$codorg." AND np.tipnom=".$codtip." and np.estado = 'Activo'
			ORDER  BY np.ficha";

	$res2=$conexion->query($sql2);

	$ini=$i; $enc=false;
	if($res2)
	{
		while($row2=$res2->fetch_array())
		{	
			if(!$enc){
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, $row['descrip'] );
				$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':B'.$i);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':B'.$i)->getFont()->setSize(12);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':B'.$i)->getFont()->setBold(true);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$i++;
			}
			$enc=true;
			$ficha = $row2['ficha'];
			$primer_nombre = utf8_encode($row2['primer_nombre']);
			$apellido   = utf8_encode($row2['primer_apellido']);
			$trabajador = utf8_encode($row2['primer_nombre']).' '.utf8_encode($row2['primer_apellido']); // $row2['nombre']
			
			$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':P'.$i)->getFont()->setSize(12);

			$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, $row2['ficha']);
			$objPHPExcel->getActiveSheet()->setCellValue('B'.$i, $trabajador);
			$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

			$where = "a.codnom=".$codnom." AND a.tipnom=".$codtip." AND a.ficha=".$ficha;
				
			$sql3 = str_replace("replace", $where, $sql_reporte);

			$res3 = $conexion->query($sql3);
			
			$neto=0;
			if($row3=$res3->fetch_array())
			{
				$MONTO_EXTRA_SALARIO = $MONTO_DOMINGO_SALARIO = 0;
				$MONTO_GR_EXTRA_SALARIO = $MONTO_GR_DOMINGO_SALARIO = 0;

				//imprimir campos reporte
				$sqlDesc = "select * from config_reportes_planilla_columnas 
				where id_reporte =".$configReporte['id']." and visible=1 order by col_orden asc";
				$resc=$conexion->query($sqlDesc);
				
				$colExcelActual = 2;
				
				$salario_resta = (isset($row3['salario_resta'])) ? $row3['salario_resta']:0;
				$ingresos_resta = (isset($row3['ingresos_resta'])) ? $row3['ingresos_resta']:0;

				$row3['salario'] = $row3['salario']-$salario_resta;
				$row3['total_ingresos'] = $row3['total_ingresos']-$salario_resta-$ingresos_resta;
				$row3['salario_neto'] = $row3['total_ingresos'] - $row3['total_deducciones']; 

				while($column=$resc->fetch_array()){
					if($column['nombre_corto']!='salario_neto'){
						$cell = $excelLetra[$colExcelActual].$i;
						$objPHPExcel->getActiveSheet()->setCellValue($cell, $row3[ $column['nombre_corto'] ]);
						$granTotal[ $column['nombre_corto'] ] = $granTotal[ $column['nombre_corto'] ] + $row3[ $column['nombre_corto'] ];
						if($codorg==1)
							$totalesAdmon[ $column['nombre_corto'] ] = $totalesAdmon[ $column['nombre_corto'] ] + $row3[ $column['nombre_corto'] ];
						else
							$totalesOtros[ $column['nombre_corto'] ] = $totalesOtros[ $column['nombre_corto'] ] + $row3[ $column['nombre_corto'] ];
					}else{                    
						$cell = $excelLetra[$colExcelActual].$i;
						$neto = $row3['total_ingresos'] - $row3['total_deducciones'];            
						$objPHPExcel->getActiveSheet()->setCellValue($cell, $neto );
						$granTotal[ $column['nombre_corto'] ] = $granTotal[ $column['nombre_corto'] ] + $neto;
						if($codorg==1)
							$totalesAdmon[ $column['nombre_corto'] ] = $totalesAdmon[ $column['nombre_corto'] ] + $neto;
						else
							$totalesOtros[ $column['nombre_corto'] ] = $totalesOtros[ $column['nombre_corto'] ] + $neto;

					}
					$colExcelActual++;
				}
				$objPHPExcel->getActiveSheet()->setCellValue($excelLetra[$colExcelActual].$i, '          ');
				$objPHPExcel->getActiveSheet()->getStyle($excelLetra[$colExcelActual].$i)->getBorders()->getBottom()
									->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);

				$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':'.$excelLetra[($colExcelActual-1)].$i)->getNumberFormat()
							->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_ACCOUNTING);
			}
			$trabajadores[] = array('nombre'=>$trabajador, 'primer_nombre'=> $primer_nombre, 'apellido'=>$apellido, 'neto'=> $neto);

			$i++;
		}
	
		if($enc)
		{
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, 'Sub Total Centro de Costos: ' );
			//calcular subtotales de reporte
			//imprimir campos reporte
			$sqlDesc = "select * from config_reportes_planilla_columnas 
			where id_reporte =".$configReporte['id']." and visible = 1 order by col_orden asc";
			$resc2=$conexion->query($sqlDesc);
			
			$colExcelActual = 1;

			while($column2=$resc2->fetch_array()){
				$colExcelActual++;
				$col = $excelLetra[$colExcelActual];
				$objPHPExcel->getActiveSheet()->setCellValue($col.$i, "=SUM(".$col.$ini.":".$col.($i-1).")");
			}

			$col = $excelLetra[$colExcelActual];
			$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':'.$col.$i)->getFont()->setSize(12);
			$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':'.$col.$i)->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':'.$col.$i)->getNumberFormat()
										->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_ACCOUNTING);

			$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getFont()->setSize(12);
			$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':B'.$i);
			$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':'.$col.$i)->getBorders()->getBottom()
						->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
			$i=$i+2;	
			$nivel++;
		}
	}
}

$i++;
//$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':'.$col.$i)->applyFromArray(allBordersMedium());
$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':'.$col.$i)->getFont()->setSize(12);
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':'.$col.$i)->getFont()->setBold(true);

//imprimir campos reporte
$sqlDesc = "select * from config_reportes_planilla_columnas 
where id_reporte =".$configReporte['id']." and visible = 1 order by col_orden asc";
$resc3=$conexion->query($sqlDesc);

$colExcelActual = 1;

$objPHPExcel->getActiveSheet()->setCellValue('B'.$i, 'Total Administracion');

while($column3=$resc3->fetch_array()){
    $colExcelActual++;
    $col = $excelLetra[$colExcelActual];    
    $objPHPExcel->getActiveSheet()->setCellValue($col.$i, $totalesAdmon[ $column3['nombre_corto'] ]);
}

$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':'.$col.$i)->getBorders()->getBottom()
	        		->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
$i=$i+2;
//imprimir campos reporte
$sqlDesc = "select * from config_reportes_planilla_columnas 
where id_reporte =".$configReporte['id']." and visible = 1 order by col_orden asc";
$resc3=$conexion->query($sqlDesc);

$colExcelActual = 1;

$objPHPExcel->getActiveSheet()->setCellValue('B'.$i, 'Total Otros');

while($column4=$resc3->fetch_array()){
    $colExcelActual++;
    $col = $excelLetra[$colExcelActual];    
    $objPHPExcel->getActiveSheet()->setCellValue($col.$i, $totalesOtros[ $column4['nombre_corto'] ]);
}

$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':'.$col.$i)->getBorders()->getBottom()
	        		->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
$i=$i+2;
//imprimir campos reporte
$sqlDesc = "select * from config_reportes_planilla_columnas 
where id_reporte =".$configReporte['id']." and visible = 1 order by col_orden asc";
$resc3=$conexion->query($sqlDesc);

$colExcelActual = 1;

$objPHPExcel->getActiveSheet()->setCellValue('B'.$i, 'Gran Total');

while($column5=$resc3->fetch_array()){
    $colExcelActual++;
    $col = $excelLetra[$colExcelActual];    
    $objPHPExcel->getActiveSheet()->setCellValue($col.$i, $granTotal[ $column5['nombre_corto'] ]);
}

$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':'.$col.$i)->getBorders()->getBottom()
	        		->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
$i=$i+2;

$objPHPExcel->getActiveSheet()->setCellValue('B'.$i, 'TOTAL EMPLEADOS:');
$objPHPExcel->getActiveSheet()->setCellValue('C'.$i, count($trabajadores));
$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':F'.$i)->getFont()->setBold(true);

$i=$i+2;
$objPHPExcel->getActiveSheet()->setCellValue('B'.$i, 'Elaborado por:');
$objPHPExcel->getActiveSheet()->setCellValue('F'.$i, 'Autorizado por:');

$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':D'.$i)->getBorders()->getBottom()
	        		->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
$objPHPExcel->getActiveSheet()->getStyle('G'.$i.':H'.$i)->getBorders()->getBottom()
	        		->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);

$objPHPExcel->getActiveSheet()->mergeCells('C'.$i.':D'.$i);
$objPHPExcel->getActiveSheet()->mergeCells('G'.$i.':H'.$i);

$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':F'.$i)->getFont()->setBold(true);



foreach ($trabajadores as $key => $registro) 
{
    $aux[$key] = $registro['apellido'];
}

array_multisort($aux, SORT_ASC, $trabajadores);

if($NOMINA != 'TEMPORAL'){
$j = $i;
$i = $i + 2; 
$i++;
$ss=$i;
$i++;
$se=$i;

$i=$i+4;


} // Fin if NOMINA!='TEMPORAL'
//==================================================================================================
//$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(32);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('R')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('S')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('T')->setAutoSize(true);



$objPHPExcel->getActiveSheet()->setTitle($dia_fin.' '.$mes_letras);

//===========================================================================

$objPHPExcel->createSheet();
$objPHPExcel->setActiveSheetIndex(1); 

$objPHPExcel->getActiveSheet()->setTitle('Resumido pago');

$objPHPExcel->getActiveSheet()
			->setCellValue('B1', '                  CONSOLIDADO DE PAGO DE PLANILLA ')
			->setCellValue('B3', 'Empresa')
			->setCellValue('C3', strtoupper($empresa) )
			->setCellValue('F3', PHPExcel_Shared_Date::FormattedPHPToExcel($anio, $mes_numero, $dia_fin) )
			->setCellValue('B6', 'Nombre del Empleado')
			->setCellValue('F6', 'Neto a Pagar'); 

$objPHPExcel->getActiveSheet()->getDefaultStyle()->getFont()->setName('Times New Roman');
//$objPHPExcel->getActiveSheet()->getStyle('B1:F1')->getFont()->setName('Times New Roman');
$objPHPExcel->getActiveSheet()->getStyle('B1:F1')->getFont()->setSize(16);
$objPHPExcel->getActiveSheet()->getStyle('B1:F1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->mergeCells('B1:F1');

cellColor('A1:F6', 'FFFFFF');

$objPHPExcel->getActiveSheet()->getStyle('C3:D3')->getFont()->setSize(12);
$objPHPExcel->getActiveSheet()->getStyle('C3:D3')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->mergeCells('C3:D3');

$objPHPExcel->getActiveSheet()->getStyle('F3')->getFont()->setSize(12);
$objPHPExcel->getActiveSheet()->getStyle('F3')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('F3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('F3')->applyFromArray(allBordersThin());

$objPHPExcel->getActiveSheet()->getStyle('F3')->getNumberFormat()
							  //->setFormatCode('dd-mmm-yy');
							  ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX15);	

$objPHPExcel->getActiveSheet()->getStyle('B3:F3')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

$objPHPExcel->getActiveSheet()->getStyle('B3')->applyFromArray(
array(
	'font'      => array(
		//'name'         => 'Times New Roman',
		'bold'         => true,
		'color'        => array('rgb' => '800000'),
		'italic'       => true,
		'size'         => 16
	),
	'alignment' => array(
		'horizontal'  => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
		'vertical'    => PHPExcel_Style_Alignment::VERTICAL_CENTER,
		//'wrap'        => true
	)
));

$objPHPExcel->getActiveSheet()->getStyle('E3')->applyFromArray(
array(
	'font'      => array(
		'bold'         => true,
		'color'        => array('rgb' => '800000'),
		'size'         => 16
	),
	'alignment' => array(
		'horizontal'  => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
		'vertical'    => PHPExcel_Style_Alignment::VERTICAL_CENTER,
		'wrap'        => true
	)
));

$objPHPExcel->getActiveSheet()->mergeCells('B6:C6');

$objPHPExcel->getActiveSheet()->getStyle('B6:F6')->getFont()->setSize(14);
$objPHPExcel->getActiveSheet()->getStyle('B6:F6')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('B6:F6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('B6:F6')->getBorders()->getTop()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
$objPHPExcel->getActiveSheet()->getStyle('B6:F6')->getBorders()->getBottom()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$i=7;
$cont=1;
foreach ($trabajadores as $key => $row) 
{
    //echo $row['nombre'].' '.$row['neto'].'<br/>';
	$objPHPExcel->getActiveSheet()
				->setCellValue('A'.$i, $cont )
				->setCellValue('B'.$i, $row['apellido'].', '.$row['primer_nombre'])
				->setCellValue('F'.$i, $row['neto'] );
	$TotalAPagar+=$row['neto'];
	cellColor('A'.$i.':E'.$i, 'FFFFFF');

	$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':F'.$i)->getFont()->setSize(10);
	$objPHPExcel->getActiveSheet()->getStyle('B'.$i)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('B'.$i)->getFont()->setItalic(true);
	$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':F'.$i)->getBorders()->getBottom()
        					      ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

	$objPHPExcel->getActiveSheet()->getStyle('F'.$i)->getNumberFormat()
							      ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$i++;
	$cont++;
}

cellColor('A'.$i.':F'.($i+2), 'FFFFFF');
$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':F'.$i)->getBorders()->getBottom()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$i++;
$objPHPExcel->getActiveSheet()->getStyle('C'.$i)->getFont()->setSize(12);
$objPHPExcel->getActiveSheet()->getStyle('C'.$i)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->mergeCells('C'.$i.':D'.$i);
$objPHPExcel->getActiveSheet()
			->setCellValue('C'.$i, 'TOTAL A PAGAR')
			->setCellValue('F'.$i, $TotalAPagar );
$TotalAPagar=$objPHPExcel->getActiveSheet()->getCell('F'.$i)->getCalculatedValue();
	$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':F'.$i)->getBorders()->getBottom()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle('F'.$i)->getFont()->setBold(true);

$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$importe=$TotalAPagar;	

$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':F'.$i)->applyFromArray(allBordersThin());
$objPHPExcel->getActiveSheet()->getStyle('F'.$i)->getNumberFormat()
							  ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(7);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(19);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(17);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(17);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(18);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(17);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(30);
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(18);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(17);
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(18);
$objPHPExcel->getActiveSheet()->setSelectedCells('F'.($i+20));


//===========================================================================
$objPHPExcel->setActiveSheetIndex(0); 
$objPHPExcel->getActiveSheet()->setSelectedCells('I30');



$NOMINA = str_replace(' ', '', $NOMINA);
$filename = $configReporte['nombre_archivo']."_".$NOMINA."_Del_".fecha($desde).'_Hasta_'.fecha($hasta);

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
