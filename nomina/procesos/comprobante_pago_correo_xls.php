<?php
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('America/Panama');

if (PHP_SAPI == 'cli')
	die('Este ejemplo solo se debe ejecutar desde un navegador Web');

include('../lib/common_excel.php');
include('../paginas/lib/php_excel.php');

if(isset($_GET['codnom']) && isset($_GET['codtip']))
{
	$codnom=$_GET['codnom']; // Código de la nómina - nom_nominas_pago
	$codtip=$_GET['codtip']; // Tipo de nómina - nomtipos_nomina

	$conexion=conexion();

	// Creación del directorio que contiene los comprobantes de pago
	$directorio = "recibos/nomina_".$codnom."_tipo_".$codtip."_".date("Y_m_d_H_m");

	/* if(!file_exists($directorio))
	{ */
		if(file_exists($directorio) || mkdir($directorio, 0777, true) )
		{
				// echo "Se ha creado el directorio: ".$directorio;
			 	echo "<div align='center' style=\"background-color : #84225b; color : #fdfdfd; font-family : 'Arial Black'; font-size : 15px;\">Envio de correo Procesado Correctamente!</div>";
			 	echo "<br>";

				require_once '../paginas/phpexcel/Classes/PHPExcel.php';
				require("PHPMailer_5.2.4/class.phpmailer.php");
				include("PHPMailer_5.2.4/class.smtp.php");

				// Consultar nombre de la empresa
				$sql  = "SELECT e.nom_emp as empresa FROM nomempresa e";
				$res  = query($sql, $conexion);
				if( $fila = fetch_array($res) ) $empresa = $fila['empresa'];

				// Consultar nombre de la nómina
				$sql = "SELECT UPPER(descrip) as descrip FROM nomtipos_nomina WHERE codtip=".$codtip;
				$res=query($sql, $conexion);
				if( $fila = fetch_array($res) )
				{
					$NOMINA = $fila['descrip'];
					//$NOMINA   = str_replace(' ', '', $NOMINA);
				} 

				// Consultar fecha de inicio y fecha fin de la quincena
				$meses = array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
					           'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');

				$sql = "SELECT np.periodo_ini as desde, np.periodo_fin as hasta, np.mes, np.anio,
								DATE_FORMAT(np.periodo_ini, '%d') as dia_ini, 
								DATE_FORMAT(np.periodo_fin, '%d') as dia_fin,
								DATE_FORMAT(np.periodo_ini, '%m') as mes_ini,
								DATE_FORMAT(np.periodo_fin, '%m') as mes_fin   
						 FROM nom_nominas_pago np 
				         WHERE  np.codnom=".$codnom." AND np.tipnom=".$codtip;
				$res=query($sql, $conexion);
				if($fila=fetch_array($res))
				{
					$desde      = $fila['desde']; 			// Fecha de inicio de la quincena
					$hasta      = $fila['hasta'];			// Fecha fin de la quincena
					$dia_ini    = $fila['dia_ini'];			// Dia inicio
					$dia_fin    = $fila['dia_fin']; 		// Dia fin
					$mes_ini    = $fila['mes_ini'];
					$mes_fin	= $fila['mes_fin'];
					$mes_numero = $fila['mes'];				// Mes del 1 al 12
					$mes_initxt = $meses[$mes_ini - 1];
					$mes_fintxt = $meses[$mes_fin - 1];
					$mes_letras = $meses[$mes_numero - 1]; 	// Nombre del mes
					$anio       = $fila['anio'];			// Año quincena
				}

				//----------------------------------------------------------------------------------------	
				// Estilos personalizados de texto
				$styleTextGreen = array(
					'alignment' => array(
			            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
			        ),
				    'font'      => array(
				        'bold'       => true,
				        'color'      => array('rgb' => '008000'),
				        'size'       => 11,
				    )
				);

				$styleTextBlackBold = array(
					'alignment' => array(
			            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
			            'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER
			        ),
					'borders' => array(
					    'allborders' => array(
					         'style' => PHPExcel_Style_Border::BORDER_THIN
					    )
					),
				    'font'      => array(
				        'bold'       => true,
				        'size'       => 10,
				    )
				);

				$styleTextGranate = array(
				    'font'      => array(
				        'bold'       => true,
				        'color'      => array('rgb' => '800000'),
				        'size'       => 12,
				    )
				);	

				$styleTextGranateSmall = array(
				    'font'      => array(
				        'color'      => array('rgb' => '800000'),
				        'size'       => 8,
				    )
				);

				$styleTextNavyBlue = array(
				    'font'      => array(
				        'bold'       => true,
				        'color'      => array('rgb' => '000080'),
				        'size'       => 11,
				    ),
					'numberformat'   => array(
				        'code'       => '"B/." #,##0.00',
				    )    
				);

			//=================================================================================================================
				$sql= "SELECT np.ficha, np.cedula, CONCAT_WS(' ', np.nombres, np.apellidos) as nombre,
							  SUBSTRING_INDEX(SUBSTRING_INDEX(np.nombres, ' ', 1), ' ', -1) as primer_nombre,
							  CASE WHEN np.apellidos LIKE 'De %' THEN 
								        SUBSTRING_INDEX(SUBSTRING_INDEX(np.apellidos, ' ', 2), ' ',  2) 
							  ELSE      SUBSTRING_INDEX(SUBSTRING_INDEX(np.apellidos, ' ', 1), ' ', -1) 
							  END as primer_apellido,
							  np.email
						FROM  nompersonal np
						WHERE np.ficha!=4 AND np.tipnom=".$codtip."
						AND   np.ficha IN (SELECT DISTINCT n.ficha 
											FROM nomvis_per_movimiento n 
										    WHERE n.codnom=".$codnom." AND n.tipnom=np.tipnom)
						AND   np.email!=''
						ORDER BY np.nombres";
				// AND    np.ficha IN (SELECT DISTINCT n.ficha FROM nom_movimientos_nomina n WHERE n.codnom=".$codnom." AND n.tipnom=np.tipnom)
				$res=query($sql, $conexion); 
				while($row=fetch_array($res))
				{

						$objPHPExcel = new PHPExcel();

						$objPHPExcel->getProperties()->setCreator("Selectra")
													 ->setLastModifiedBy("Selectra")
													 ->setTitle("Comprobante de Pago");
						//----------------------------------------------------------------------------------------						 
						$objPHPExcel->getDefaultStyle()->getFont()
													   ->setName('Times New Roman')
													   ->setSize(12);
						$objPHPExcel->getDefaultStyle()->getAlignment()
											  	   	->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

						// Color de fondo por defecto de las hojas del libro
						colorFondoLibro('FFFFFF'); // Blanco

					//----------------------------------------------------------------------------------------
						$ficha           = $row['ficha'];
						$primer_nombre   = $row['primer_nombre'];
						$primer_apellido = $row['primer_apellido'];
						$nombre_completo = $row['nombre'];
						$cedula          = $row['cedula'];
						$email           = $row['email'];

						// Nombre hoja del libro
						$objPHPExcel->getActiveSheet()->setTitle($dia_fin.' '.$mes_letras);
					//----------------------------------------------------------------------------------------
						// Logo Empresa
						// Agregar logo a hoja actual
						$objDrawing = new PHPExcel_Worksheet_Drawing();
						$objDrawing->setName('Logo');
					    $objDrawing->setDescription('Logo Empresa');
						$objDrawing->setCoordinates('A1');
						$objDrawing->setPath('../paginas/imagenes/logo_empresa.png');
						//$objDrawing->setHeight(38);
						//$objDrawing->setWidth(116);
						$objDrawing->setOffsetX(0);
						$objDrawing->setOffsetY(0);
						$objDrawing->setResizeProportional(true);
						$objDrawing->setHeight(45);
						$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
					//----------------------------------------------------------------------------------------
						$objPHPExcel->getActiveSheet()
									->setCellValue('A1', 'COMPROBANTE DE PAGO')
									->setCellValue('A3', 'Empresa')
									->setCellValue('B3',  strtoupper($empresa) )
									->setCellValue('G3', 'De')
									->setCellValue('I3',  PHPExcel_Shared_Date::FormattedPHPToExcel($anio, $mes_ini, $dia_ini) )
									->setCellValue('J3', 'A')
									->setCellValue('K3',  PHPExcel_Shared_Date::FormattedPHPToExcel($anio, $mes_fin, $dia_fin) )
									->setCellValue('A5', 'Empleado')
									->setCellValue('B5',  $primer_apellido.', '.$primer_nombre)
									->setCellValue('G5', 'Clave')
									->setCellValue('I5', 'A00');

						$objPHPExcel->getActiveSheet()->mergeCells('A1:K1');
						$objPHPExcel->getActiveSheet()->mergeCells('B3:E3');
						$objPHPExcel->getActiveSheet()->mergeCells('B5:D5');

						$objPHPExcel->getActiveSheet()->getRowDimension(3)->setRowHeight(21);
						$objPHPExcel->getActiveSheet()->getRowDimension(4)->setRowHeight(21);
						$objPHPExcel->getActiveSheet()->getRowDimension(5)->setRowHeight(21);
						$objPHPExcel->getActiveSheet()->getRowDimension(6)->setRowHeight(12);
						$objPHPExcel->getActiveSheet()->getRowDimension(7)->setRowHeight(24);

						$objPHPExcel->getActiveSheet()->getStyle('A1')
													  ->getFont()
													  ->setSize(22)
													  ->setBold(true);

						$objPHPExcel->getActiveSheet()->getStyle('A1')
													  ->getAlignment()
													  ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

						$objPHPExcel->getActiveSheet()->getStyle("A3")->applyFromArray($styleTextGreen);
						$objPHPExcel->getActiveSheet()->getStyle("G3")->applyFromArray($styleTextGreen);
						$objPHPExcel->getActiveSheet()->getStyle("J3")->applyFromArray($styleTextGreen);
						$objPHPExcel->getActiveSheet()->getStyle("A5")->applyFromArray($styleTextGreen);
						$objPHPExcel->getActiveSheet()->getStyle("G5")->applyFromArray($styleTextGreen);
						$objPHPExcel->getActiveSheet()->getStyle("B3:E3")->applyFromArray($styleTextBlackBold);
						$objPHPExcel->getActiveSheet()->getStyle("I3")->applyFromArray($styleTextBlackBold);
						$objPHPExcel->getActiveSheet()->getStyle("K3")->applyFromArray($styleTextBlackBold);
						$objPHPExcel->getActiveSheet()->getStyle("B5:D5")->applyFromArray($styleTextBlackBold);
						$objPHPExcel->getActiveSheet()->getStyle("I5")->applyFromArray($styleTextBlackBold);

						$objPHPExcel->getActiveSheet()->getStyle('I3')->getNumberFormat()
												      ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX15);	
						$objPHPExcel->getActiveSheet()->getStyle('K3')->getNumberFormat()
												      ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX15);

						$objPHPExcel->getActiveSheet()->getStyle('A8:K8')->applyFromArray( borderTop() );

						$i=8;
						//==============================================================================================
						// A partir de aqui las celdas pueden variar su contenido

						$SALARIO = $GASTOS_REP = $TOTAL_EXTRA_DIURNAS = $TOTAL_GASTO_REP_DIURNAS = 0;
						$TOTAL_EXTRA_MIXTAS    = $TOTAL_GASTO_REP_MIXTAS = 0;
						$TOTAL_EXTRA_NOCTURNAS = $TOTAL_GASTO_REP_NOCTURNAS = 0;
						$TOTAL_EXTRA_NACIONAL  = $TOTAL_GASTO_REP_NACIONAL = 0;
						$TOTAL_EXTRA_DOMINGO   = $TOTAL_GASTO_REP_DOMINGO = 0;
						$HORAS_EXTRA = $HORAS_EXTRA_GASTO_REP = 0;
						$DIETA = $COMBUSTIBLE = 0;

						// Consultar el salario
						$sql2 = "SELECT COALESCE(n.monto, 0) AS salario  
								 FROM   nom_movimientos_nomina n 
							  	 WHERE  n.codnom=".$codnom." AND n.ficha=".$ficha." 
							  	 AND    n.codcon=100"; // n.descrip LIKE 'SALARIO'

						$res2 = query($sql2, $conexion);
						if($row2 = fetch_array($res2) ){ $SALARIO = $row2['salario']; }

						// Consultar gastos de representación
						$sql2 = "SELECT COALESCE(n.monto, 0) AS gastos_rep  
								 FROM   nom_movimientos_nomina n 
							  	 WHERE  n.codnom=".$codnom." AND n.ficha=".$ficha." 
							  	 AND    n.codcon=145"; // n.descrip LIKE 'GASTOS DE REPRESENTACION'

						$res2 = query($sql2, $conexion);
						if($row2 = fetch_array($res2) ){ $GASTOS_REP = $row2['gastos_rep']; }

						// Consultar Combustible - codcon 147
						$sql2 = "SELECT COALESCE(n.monto, 0) AS combustible  
								 FROM   nom_movimientos_nomina n 
							  	 WHERE  n.codnom=".$codnom." AND n.ficha=".$ficha." 
							  	 AND    n.codcon=147"; // n.descrip LIKE 'COMBUSTIBLE'
						$res2 = query($sql2, $conexion);
						if($row2 = fetch_array($res2)){ $COMBUSTIBLE = $row2['combustible']; }

						// Consultar Dieta - codcon 148
						$sql2 = "SELECT COALESCE(n.monto, 0) AS dieta  
								 FROM   nom_movimientos_nomina n 
							  	 WHERE  n.codnom=".$codnom." AND n.ficha=".$ficha." 
							  	 AND    n.codcon=148"; // n.descrip LIKE 'DIETA'
						$res2 = query($sql2, $conexion);
						if($row2 = fetch_array($res2)){ $DIETA = $row2['dieta']; }

						// Consultar Total Horas Extras Diurnas
						// Fórmula Excel ==> Diurnas 25% + Diurnas 25% * 75% + Descanso interrumpido 
						// 106  HORA EXTRA DIURNA (25%)
						// 108  HORA EXTRA DIURNA RECARGO (25%/75%)
						// 142  HORA EXTRA DIURNA Descanso (50 / 25%)
						// 143  Hora Extra Diurna Descanso Recargo(50/25/75%)
						// 183  HORA EXTRA DIURNA Descanso (50 / 25%)      - Por verificar si debe incluirse
						// 184  Hora Extra EXT Diurna Descanso (50/25/75%) - Por verificar si debe incluirse
						$sql2 = "SELECT COALESCE(SUM(n.monto), 0) AS total_extra_diurnas  
								 FROM   nom_movimientos_nomina n 
								 WHERE  n.codnom=".$codnom." AND n.ficha=".$ficha." 
								 AND    n.codcon IN (106, 108, 142, 143, 183, 184)";

						$res2 = query($sql2, $conexion);
						if($row2 = fetch_array($res2) )
						{ $TOTAL_EXTRA_DIURNAS = $row2['total_extra_diurnas']; }

						// Consultar Gastos de Representacion Horas Extras Diurnas
						// 156  HORA EXTRA DIURNA GR (25%)
						// 157  HORA EXTRA EXT DIURNA GR (25%/75%)
						$sql2 = "SELECT COALESCE(SUM(n.monto), 0) AS gastos_rep_extra_diurnas  
								 FROM   nom_movimientos_nomina n 
								 WHERE  n.codnom=".$codnom." AND n.ficha=".$ficha." 
								 AND    n.codcon IN (156, 157)";

						$res2 = query($sql2, $conexion);
						if($row2 = fetch_array($res2) )
						{ $TOTAL_GASTO_REP_DIURNAS = $row2['gastos_rep_extra_diurnas']; }

						// Consultar Total Horas Extras Mixtas
						// 144, 149, 150, 151, 185, 186, 187, 188 corresponden a Descanso
						$sql2 = "SELECT COALESCE(SUM(n.monto), 0) AS total_extra_mixtas  
								 FROM   nom_movimientos_nomina n 
								 WHERE  n.codnom=".$codnom." AND n.ficha=".$ficha." 
								 AND    n.codcon IN (SELECT c.codcon FROM nomconceptos c 
										             WHERE  c.descrip LIKE '%Mixt%' AND c.descrip LIKE '%Hora Extra%'   
										             AND    c.descrip NOT LIKE '%GR%' 
										             AND    c.descrip NOT LIKE '%Domingo%' 
										             AND    c.descrip NOT LIKE '%Nacional%')";

						$res2 = query($sql2, $conexion);
						if($row2 = fetch_array($res2) )
						{ $TOTAL_EXTRA_MIXTAS = $row2['total_extra_mixtas']; }

						// Consultar Gastos de Representación Horas Extras Mixtas
						$sql2 = "SELECT COALESCE(SUM(n.monto), 0) AS gastos_rep_extra_mixtas  
								 FROM   nom_movimientos_nomina n 
								 WHERE  n.codnom=".$codnom." AND n.ficha=".$ficha." 
								 AND    n.codcon IN (SELECT c.codcon FROM nomconceptos c 
										             WHERE  c.descrip LIKE '%Mixt%' AND c.descrip LIKE '%Hora Extra%'   
										             AND    c.descrip LIKE '%GR%' 
										             AND    c.descrip NOT LIKE '%Domingo%' 
										             AND    c.descrip NOT LIKE '%Nacional%')";

						$res2 = query($sql2, $conexion);
						if($row2 = fetch_array($res2) )
						{ $TOTAL_GASTO_REP_MIXTAS = $row2['gastos_rep_extra_mixtas'];  }


						// Consultar Total Horas Extra Nocturnas
						$sql2 = "SELECT COALESCE(SUM(n.monto), 0) AS total_extra_nocturnas   
								 FROM   nom_movimientos_nomina n 
								 WHERE  n.codnom=".$codnom." AND n.ficha=".$ficha." 
								 AND    n.codcon IN (SELECT c.codcon FROM nomconceptos c 
													 WHERE  c.descrip LIKE '%Nocturn%' AND c.descrip LIKE '%Hora Extra%'  
													 AND    c.descrip NOT LIKE '%Mixto%' 
													 AND    c.descrip NOT LIKE '%GR%'
													 AND    c.descrip NOT LIKE '%Domingo%'
													 AND    c.descrip NOT LIKE '%Nacional%')";

						$res2 = query($sql2, $conexion);
						if($row2 = fetch_array($res2) )
						{ $TOTAL_EXTRA_NOCTURNAS = $row2['total_extra_nocturnas']; }

						// Consultar Total Gastos de Representacion Extra Nocturnas
						$sql2 = "SELECT COALESCE(SUM(n.monto), 0) AS gastos_rep_extra_nocturnas   
								 FROM   nom_movimientos_nomina n 
								 WHERE  n.codnom=".$codnom." AND n.ficha=".$ficha." 
								 AND    n.codcon IN (SELECT c.codcon FROM nomconceptos c 
													 WHERE  c.descrip LIKE '%Nocturn%' AND c.descrip LIKE '%Hora Extra%' 
													 AND    c.descrip NOT LIKE '%Mixto%' 
													 AND    c.descrip LIKE '%GR%'
													 AND    c.descrip NOT LIKE '%Domingo%'
													 AND    c.descrip NOT LIKE '%Nacional%')";

						$res2 = query($sql2, $conexion);
						if($row2 = fetch_array($res2) )
						{ $TOTAL_GASTO_REP_NOCTURNAS = $row2['gastos_rep_extra_nocturnas']; }

						// Consultar Total Horas Extra Domingo
						$sql2 = "SELECT COALESCE(SUM(n.monto), 0) AS total_extra_domingo  
								 FROM   nom_movimientos_nomina n 
								 WHERE  n.codnom=".$codnom." AND n.ficha=".$ficha." 
								 AND    n.codcon IN (SELECT c.codcon
													 FROM nomconceptos c 
													 WHERE c.descrip LIKE '%Domingo%' AND c.descrip LIKE '%Hora Extra%'
													 AND   c.descrip NOT LIKE '%GR%')";

						$res2 = query($sql2, $conexion);
						if($row2 = fetch_array($res2) )
						{ $TOTAL_EXTRA_DOMINGO = $row2['total_extra_domingo']; }

						// Consultar Total Gastos de Representacion Extra Domingo
						$sql2 = "SELECT COALESCE(SUM(n.monto), 0) AS gastos_rep_extra_domingo   
								 FROM   nom_movimientos_nomina n 
								 WHERE  n.codnom=".$codnom." AND n.ficha=".$ficha." 
								 AND    n.codcon IN (SELECT c.codcon
													 FROM nomconceptos c 
													 WHERE c.descrip LIKE '%Domingo%' AND c.descrip LIKE '%Hora Extra%'
													 AND   c.descrip LIKE '%GR%')";

						$res2 = query($sql2, $conexion);
						if($row2 = fetch_array($res2) )
						{ $TOTAL_GASTO_REP_DOMINGO = $row2['gastos_rep_extra_domingo']; }

						// Consultar Total Horas Extra Nacional
						$sql2 = "SELECT COALESCE(SUM(n.monto), 0) AS total_extra_nacional
								 FROM   nom_movimientos_nomina n 
								 WHERE  n.codnom=".$codnom." AND n.ficha=".$ficha." 
								 AND    n.codcon IN (SELECT c.codcon 
													 FROM   nomconceptos c 
													 WHERE  c.descrip LIKE '%Nacional%' AND c.descrip LIKE '%Hora Extra%'
													 AND    c.descrip NOT LIKE '%GR%')";

						$res2 = query($sql2, $conexion);
						if($row2 = fetch_array($res2) )
						{ $TOTAL_EXTRA_NACIONAL = $row2['total_extra_nacional']; }

						// Consultar Total Gastos de Representacion Extra Nacional
						$sql2 = "SELECT COALESCE(SUM(n.monto), 0) AS gastos_rep_extra_nacional
								 FROM   nom_movimientos_nomina n 
								 WHERE  n.codnom=".$codnom." AND n.ficha=".$ficha." 
								 AND    n.codcon IN (SELECT c.codcon 
													 FROM   nomconceptos c 
													 WHERE  c.descrip LIKE '%Nacional%' AND c.descrip LIKE '%Hora Extra%'
													 AND    c.descrip LIKE '%GR%')";

						$res2 = query($sql2, $conexion);
						if($row2 = fetch_array($res2) )
						{ $TOTAL_GASTO_REP_NACIONAL = $row2['gastos_rep_extra_nacional']; }

						// Calcular Total Horas Extras 
						$sql2= "SELECT COALESCE(SUM(monto), 0) AS horas_extras
								FROM   nom_movimientos_nomina n 
								WHERE  n.codcon=600
								AND    n.codnom=".$codnom." AND n.ficha=".$ficha;

						$res2 = query($sql2, $conexion);
						if($row2 = fetch_array($res2) )
						{ $HORAS_EXTRA = $row2['horas_extras']; }

						// Calcular Total Horas Extras GASTOS REPRESENTACION
						$sql2= "SELECT COALESCE(SUM(monto), 0) AS horas_extras_gastos_rep
								FROM   nom_movimientos_nomina n 
								WHERE  n.codcon=602
								AND    n.codnom=".$codnom." AND n.ficha=".$ficha;

						$res2 = query($sql2, $conexion);
						if($row2 = fetch_array($res2) )
						{ $HORAS_EXTRA_GASTO_REP = $row2['horas_extras_gastos_rep']; }

						$SEGURO_SOCIAL = $SEGURO_EDUCATIVO = $ISR_SALARIO = $ISR_GASTOS_REP = $DESCUENTO_PAGAR = 0;

						// Consultar Seguro Social
						$sql2= "SELECT COALESCE(SUM(monto), 0) AS seguro_social
								FROM   nom_movimientos_nomina n 
								WHERE  n.codcon IN (200, 208)
								AND    n.codnom=".$codnom." AND n.ficha=".$ficha; // n.descrip LIKE '%Seguro Social%'

						$res2 = query($sql2, $conexion);
						if($row2 = fetch_array($res2) )
						{ $SEGURO_SOCIAL = $row2['seguro_social']; }

						// Consultar Seguro Educativo
						$sql2= "SELECT COALESCE(SUM(monto), 0) AS seguro_educativo
								FROM   nom_movimientos_nomina n 
								WHERE  n.codcon=201
								AND    n.codnom=".$codnom." AND n.ficha=".$ficha; // n.descrip LIKE '%Seguro Educativo%'

						$res2 = query($sql2, $conexion);
						if($row2 = fetch_array($res2) )
						{ $SEGURO_EDUCATIVO = $row2['seguro_educativo']; }

						// Consultar Impuesto Sobre la Renta e Impuesto Sobre la Renta Gastos Representacion 
						$sql2 = "SELECT n.codcon
								 FROM   nom_movimientos_nomina n 
								 WHERE  n.codcon IN (202, 207)
								 AND    n.codnom=".$codnom." AND n.ficha=".$ficha;
						$res2 = query($sql2, $conexion);
						if($row2 = fetch_array($res2) )
						{
								// Si entra aqui es que se encontraron el concepto 202 y 207
								// Calculo el impuesto sobre la renta
								$sql2= "SELECT COALESCE(SUM(monto), 0) AS isr_salario
										FROM   nom_movimientos_nomina n 
										WHERE  n.codcon=202
										AND    n.codnom=".$codnom." AND n.ficha=".$ficha; // n.descrip LIKE 'IMPUESTO SOBRE LA RENTA (I.S.R.)%'

								$res2 = query($sql2, $conexion);
								if($row2 = fetch_array($res2) )
								{ $ISR_SALARIO = $row2['isr_salario']; }

								// Calculo el gasto de representacion
								$sql2= "SELECT COALESCE(SUM(monto), 0) AS isr_gastos_rep
										FROM   nom_movimientos_nomina n 
										WHERE  n.codcon=207
										AND    n.codnom=".$codnom." AND n.ficha=".$ficha; // n.descrip LIKE 'IMPUESTO SOBRE LA RENTA GR%'

								$res2 = query($sql2, $conexion);
								if($row2 = fetch_array($res2) )
								{ $ISR_GASTOS_REP = $row2['isr_gastos_rep']; }
						}
						else
						{
								// Calculo el impuesto sobre la renta
								$sql2= "SELECT COALESCE(SUM(monto), 0) AS isr_salario
										FROM   nom_movimientos_nomina n 
										WHERE  n.codcon=601
										AND    n.codnom=".$codnom." AND n.ficha=".$ficha;

								$res2 = query($sql2, $conexion);
								if($row2 = fetch_array($res2) )
								{ $ISR_SALARIO = $row2['isr_salario']; }

								// Calculo el gasto de representacion
								$sql2= "SELECT COALESCE(SUM(monto), 0) AS isr_gastos_rep
										FROM   nom_movimientos_nomina n 
										WHERE  n.codcon IN (606, 607)
										AND    n.codnom=".$codnom." AND n.ficha=".$ficha;

								$res2 = query($sql2, $conexion);
								if($row2 = fetch_array($res2) )
								{ $ISR_GASTOS_REP = $row2['isr_gastos_rep']; }
						}

						// Calcular Descuentos por Pagar
						$sql2 = "SELECT  COALESCE(SUM(n.monto), 0) AS descuentos_por_pagar
								 FROM    nom_movimientos_nomina n 
				  				 WHERE   n.codnom=".$codnom." AND n.ficha=".$ficha." 
				  				 AND     n.codcon BETWEEN 501 AND 599";
						$res2 = query($sql2, $conexion);
						if($row2 = fetch_array($res2) )
						{ $DESCUENTO_PAGAR = $row2['descuentos_por_pagar']; }

						// Calcular AJUSTE
						$sql2= "SELECT COALESCE(SUM(monto), 0) AS ajuste
								FROM   nom_movimientos_nomina n 
								WHERE  n.codcon=604
								AND    n.codnom=".$codnom." AND n.ficha=".$ficha;

						$res2 = query($sql2, $conexion);
						if($row2 = fetch_array($res2) )
						{ $AJUSTE = $row2['ajuste']; }

						$SUBTOTAL_SALARIO_BRUTO = $SUBTOTAL_GASTOS_REP_BRUTO = $SALARIO_NETO = '=';

						$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, 'Salario Bruto Quincenal:'); 
						$objPHPExcel->getActiveSheet()->setCellValue('K'.$i, $SALARIO); 
						$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($styleTextGranate);
						$objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(21);
						$SUBTOTAL_SALARIO_BRUTO .= '+K'.$i;
						$i++;

						if($DIETA > 0)
						{
							$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, 'Dieta:');
							$objPHPExcel->getActiveSheet()->setCellValue('K'.$i, $DIETA);
							$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($styleTextGranate);
							$SUBTOTAL_SALARIO_BRUTO .= '+K'.$i;
							$i++;
						}

						if($COMBUSTIBLE > 0)
						{
							$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, 'Combustible:');
							$objPHPExcel->getActiveSheet()->setCellValue('K'.$i, $COMBUSTIBLE);
							$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($styleTextGranate);
							$SUBTOTAL_SALARIO_BRUTO .= '+K'.$i;
							$i++;
						}

						$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, 'Gasto de Representación Quincenal:');
						$objPHPExcel->getActiveSheet()->setCellValue('K'.$i, $GASTOS_REP);
						$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($styleTextGranate);
						$SUBTOTAL_GASTOS_REP_BRUTO .= '+K'.$i;
						$i++;

						$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, 'Ausencia/Tardanza:');
						//$objPHPExcel->getActiveSheet()->setCellValue('K'.$i, 0);
						$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($styleTextGranate);
						$objPHPExcel->getActiveSheet()->getStyle('K'.$i)->applyFromArray( borderBottom() );
						$i++;

						$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, 'Sobretiempo:');
						//$objPHPExcel->getActiveSheet()->setCellValue('K'.$i, 0);
						$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($styleTextGranate);
						$objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(23);
						$i++;

						$sobreTiempo = $TOTAL_EXTRA_DIURNAS + $TOTAL_EXTRA_MIXTAS + $TOTAL_EXTRA_NOCTURNAS + 
									   $TOTAL_EXTRA_DOMINGO + $TOTAL_EXTRA_NACIONAL;

						$sobreTiempoGR = $TOTAL_GASTO_REP_DIURNAS + $TOTAL_GASTO_REP_MIXTAS + $TOTAL_EXTRA_NOCTURNAS +
										 $TOTAL_GASTO_REP_DOMINGO + $TOTAL_GASTO_REP_NACIONAL;

						if( $sobreTiempo <=0 && $sobreTiempoGR <=0 && ($HORAS_EXTRA > 0 || $HORAS_EXTRA_GASTO_REP > 0 ) )
						{
							$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, 'Horas Extra');
							$objPHPExcel->getActiveSheet()->setCellValue('K'.$i, $HORAS_EXTRA);
							$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($styleTextGranate);
							$objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(23);
							$SUBTOTAL_SALARIO_BRUTO .= '+K'.$i;
							$i++;

							$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, 'Horas Extra Gastos de Representación');
							$objPHPExcel->getActiveSheet()->setCellValue('K'.$i, $HORAS_EXTRA_GASTO_REP);
							$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($styleTextGranate);
							$objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(23);
							$SUBTOTAL_GASTOS_REP_BRUTO .= '+K'.$i;
							$i++;
						}

						$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, 'Salario -Diurnas');
						$objPHPExcel->getActiveSheet()->setCellValue('K'.$i, $TOTAL_EXTRA_DIURNAS);
						$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($styleTextGranate);
						$objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(23);
						$SUBTOTAL_SALARIO_BRUTO .= '+K'.$i;
						$i++;

						$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, 'Gasto de Representación - Diurnas:');
						$objPHPExcel->getActiveSheet()->setCellValue('K'.$i, $TOTAL_GASTO_REP_DIURNAS);
						$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($styleTextGranate);
						$objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(23);
						$SUBTOTAL_GASTOS_REP_BRUTO .= '+K'.$i;
						$i++;

						$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, 'Salario - Mixtas o prolongación de la jornada nocturna');
						$objPHPExcel->getActiveSheet()->setCellValue('K'.$i, $TOTAL_EXTRA_MIXTAS);
						$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($styleTextGranate);
						$objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(23);
						$SUBTOTAL_SALARIO_BRUTO .= '+K'.$i;
						$i++;

						$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, 'Gasto de Representación - Mixtas o prolongación de la jornada nocturna');
						$objPHPExcel->getActiveSheet()->setCellValue('K'.$i, $TOTAL_GASTO_REP_MIXTAS);
						$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($styleTextGranate);
						$objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(23);
						$SUBTOTAL_GASTOS_REP_BRUTO .= '+K'.$i;
						$i++;

						$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, 'Salario - Nocturnas');
						$objPHPExcel->getActiveSheet()->setCellValue('K'.$i, $TOTAL_EXTRA_NOCTURNAS);
						$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($styleTextGranate);
						$objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(23);
						$SUBTOTAL_SALARIO_BRUTO .= '+K'.$i;
						$i++;

						$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, 'Gasto de Representación - Nocturnas:');
						$objPHPExcel->getActiveSheet()->setCellValue('K'.$i, $TOTAL_GASTO_REP_NOCTURNAS);
						$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($styleTextGranate);
						$objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(23);
						$SUBTOTAL_GASTOS_REP_BRUTO .= '+K'.$i;
						$i++;

						$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, 'Salario - Domingo / Nacional /compensatorio');
						$objPHPExcel->getActiveSheet()->setCellValue('K'.$i, ($TOTAL_EXTRA_DOMINGO + $TOTAL_EXTRA_NACIONAL) );
						$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($styleTextGranate);
						$objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(23);
						$SUBTOTAL_SALARIO_BRUTO .= '+K'.$i;
						$i++;

						$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, 'Gasto de Representación - Domingo / Nacional/ compensatorio');
						$objPHPExcel->getActiveSheet()->setCellValue('K'.$i, ($TOTAL_GASTO_REP_DOMINGO + $TOTAL_GASTO_REP_NACIONAL ) );
						$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($styleTextGranate);
						$objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(23);
						$SUBTOTAL_GASTOS_REP_BRUTO .= '+K'.$i;
						$i++;

						$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, 'Subtotal Salario Bruto:');
						$objPHPExcel->getActiveSheet()->setCellValue('K'.$i, $SUBTOTAL_SALARIO_BRUTO);
						$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($styleTextGranate);
						$objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(23);
						$SALARIO_NETO .= '+K'.$i;
						$i++;

						$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':K'.$i)->applyFromArray( borderTop() );
						$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':K'.$i)->applyFromArray( borderBottom() );

						$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, 'Subtotal Gasto de Representación Bruto:');
						$objPHPExcel->getActiveSheet()->setCellValue('K'.$i, $SUBTOTAL_GASTOS_REP_BRUTO);
						$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($styleTextGranate);
						$SALARIO_NETO .= '+K'.$i;

						$objPHPExcel->getActiveSheet()->getStyle('J8:J'.$i)->applyFromArray( borderRight() );
						$objPHPExcel->getActiveSheet()->getStyle('K8:K'.$i)->applyFromArray( borderRight() );
						$objPHPExcel->getActiveSheet()->getStyle('K8:K'.$i)->applyFromArray( $styleTextNavyBlue );	
						/*$objPHPExcel->getActiveSheet()->getStyle('K8:K'.$i)->getNumberFormat()
													  					   ->setFormatCode('"B/." #,##0.00');*/
						$j=$i+1; $i=$i+2;	$aux1=$i;

						$objPHPExcel->getActiveSheet()->setCellValue('B'.$i, 'Seguro Social');
						$objPHPExcel->getActiveSheet()->setCellValue('F'.$i, $SEGURO_SOCIAL);
						$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':F'.$i)->applyFromArray( borderTop() );
						$objPHPExcel->getActiveSheet()->getStyle('B'.$i)->applyFromArray($styleTextGranateSmall);	
						$objPHPExcel->getActiveSheet()->getStyle('F'.$i)->applyFromArray($styleTextNavyBlue);
						$objPHPExcel->getActiveSheet()->getStyle('F'.$i)->getFont()->setSize(9);	
						$objPHPExcel->getActiveSheet()->getStyle('B'.$i)->applyFromArray( borderLeft() );
						$objPHPExcel->getActiveSheet()->getStyle('F'.$i)->applyFromArray( borderLeft() );
						$objPHPExcel->getActiveSheet()->getStyle('F'.$i)->applyFromArray( borderRight() );
						$i++;

						$objPHPExcel->getActiveSheet()->setCellValue('B'.$i, 'Seguro Educativo');
						$objPHPExcel->getActiveSheet()->setCellValue('F'.$i, $SEGURO_EDUCATIVO);
						$objPHPExcel->getActiveSheet()->getStyle('B'.$i)->applyFromArray($styleTextGranateSmall);
						$objPHPExcel->getActiveSheet()->getStyle('F'.$i)->applyFromArray($styleTextNavyBlue);
						$objPHPExcel->getActiveSheet()->getStyle('F'.$i)->getFont()->setSize(9);		
						$objPHPExcel->getActiveSheet()->getStyle('B'.$i)->applyFromArray( borderLeft() );
						$objPHPExcel->getActiveSheet()->getStyle('F'.$i)->applyFromArray( borderLeft() );
						$objPHPExcel->getActiveSheet()->getStyle('F'.$i)->applyFromArray( borderRight() );
						$i++;

						$objPHPExcel->getActiveSheet()->setCellValue('B'.$i, 'Impuesto Sobre la Renta Salario');
						$objPHPExcel->getActiveSheet()->setCellValue('F'.$i, $ISR_SALARIO);
						$objPHPExcel->getActiveSheet()->getStyle('B'.$i)->applyFromArray($styleTextGranateSmall);
						$objPHPExcel->getActiveSheet()->getStyle('F'.$i)->applyFromArray($styleTextNavyBlue);
						$objPHPExcel->getActiveSheet()->getStyle('F'.$i)->getFont()->setSize(9);		
						$objPHPExcel->getActiveSheet()->getStyle('B'.$i)->applyFromArray( borderLeft() );
						$objPHPExcel->getActiveSheet()->getStyle('F'.$i)->applyFromArray( borderLeft() );
						$objPHPExcel->getActiveSheet()->getStyle('F'.$i)->applyFromArray( borderRight() );
						$i++;

						$objPHPExcel->getActiveSheet()->setCellValue('B'.$i, 'Impuesto Sobre la Renta Gasto Representación');
						$objPHPExcel->getActiveSheet()->setCellValue('F'.$i, $ISR_GASTOS_REP);
						$objPHPExcel->getActiveSheet()->getStyle('B'.$i)->applyFromArray($styleTextGranateSmall);	
						$objPHPExcel->getActiveSheet()->getStyle('F'.$i)->applyFromArray($styleTextNavyBlue);
						$objPHPExcel->getActiveSheet()->getStyle('F'.$i)->getFont()->setSize(9);	
						$objPHPExcel->getActiveSheet()->getStyle('B'.$i)->applyFromArray( borderLeft() );
						$objPHPExcel->getActiveSheet()->getStyle('F'.$i)->applyFromArray( borderLeft() );
						$objPHPExcel->getActiveSheet()->getStyle('F'.$i)->applyFromArray( borderRight() );
						$i++;

						$objPHPExcel->getActiveSheet()->setCellValue('B'.$i, 'Descuento por pagar');
						$objPHPExcel->getActiveSheet()->setCellValue('F'.$i, $DESCUENTO_PAGAR);
						$objPHPExcel->getActiveSheet()->getStyle('B'.$i)->applyFromArray($styleTextGranateSmall);
						$objPHPExcel->getActiveSheet()->getStyle('F'.$i)->applyFromArray($styleTextNavyBlue);
						$objPHPExcel->getActiveSheet()->getStyle('F'.$i)->getFont()->setSize(9);		
						$objPHPExcel->getActiveSheet()->getStyle('B'.$i)->applyFromArray( borderLeft() );
						$objPHPExcel->getActiveSheet()->getStyle('F'.$i)->applyFromArray( borderLeft() );
						$objPHPExcel->getActiveSheet()->getStyle('F'.$i)->applyFromArray( borderRight() );
						$aux2=$i;
						$i++;

						if($AJUSTE > 0)
						{
							$objPHPExcel->getActiveSheet()->setCellValue('B'.$i, 'Ajuste');
							$objPHPExcel->getActiveSheet()->setCellValue('F'.$i, $AJUSTE);
							$objPHPExcel->getActiveSheet()->getStyle('B'.$i)->applyFromArray($styleTextGranateSmall);
							$objPHPExcel->getActiveSheet()->getStyle('F'.$i)->applyFromArray($styleTextNavyBlue);
							$objPHPExcel->getActiveSheet()->getStyle('F'.$i)->getFont()->setSize(9);		
							$objPHPExcel->getActiveSheet()->getStyle('B'.$i)->applyFromArray( borderLeft() );
							$objPHPExcel->getActiveSheet()->getStyle('F'.$i)->applyFromArray( borderLeft() );
							$objPHPExcel->getActiveSheet()->getStyle('F'.$i)->applyFromArray( borderRight() );
							$aux2=$i;
							$i++;			
						}

						$objPHPExcel->getActiveSheet()->setCellValue('B'.$i, 'Total Otros Descuentos:');
						$objPHPExcel->getActiveSheet()->setCellValue('F'.$i, "=SUM(F".$aux1.":F".$aux2.")");	
						$SALARIO_NETO .= '-F'.$i;	
						$objPHPExcel->getActiveSheet()->getStyle('B'.$i)->applyFromArray($styleTextGranate);
						$objPHPExcel->getActiveSheet()->getStyle('B'.$i)->getFont()->setSize(9);
						$objPHPExcel->getActiveSheet()->getStyle('F'.$i)->applyFromArray($styleTextNavyBlue);
						$objPHPExcel->getActiveSheet()->getStyle('F'.$i)->getFont()->setSize(9);		
						$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':F'.$i)->applyFromArray( borderTop() );		
						$objPHPExcel->getActiveSheet()->getStyle('B'.$i)->applyFromArray( borderLeft() );
						$objPHPExcel->getActiveSheet()->getStyle('F'.$i)->applyFromArray( borderLeft() );
						$objPHPExcel->getActiveSheet()->getStyle('F'.$i)->applyFromArray( borderRight() );
						$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':F'.$i)->applyFromArray( borderBottom() );
						$i=$i+2;

						$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, 'Salario Neto:');
						$objPHPExcel->getActiveSheet()->setCellValue('K'.$i,  $SALARIO_NETO);
						$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($styleTextGranate);
						$objPHPExcel->getActiveSheet()->getStyle('K'.$i)->applyFromArray($styleTextNavyBlue);
						$objPHPExcel->getActiveSheet()->getStyle('K'.$i)->getFont()->setSize(10);
						$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':K'.$i)->applyFromArray( borderTop() );
						$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':K'.$i)->applyFromArray( borderBottom() );
						$objPHPExcel->getActiveSheet()->getStyle('K'.$j.':K'.$i)->applyFromArray( borderRight() );
						$objPHPExcel->getActiveSheet()->getStyle('J'.$i)->applyFromArray( borderRight() );

						$i=$i+2;

						$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, 'TOTAL A PAGAR');
						$objPHPExcel->getActiveSheet()->setCellValue('K'.$i, '=K'.($i-2));
						$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($styleTextGranate);
						$objPHPExcel->getActiveSheet()->getStyle('K'.$i)->getFont()->setSize(14);
						$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':J'.$i)->getAlignment()
											  	      ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$objPHPExcel->getActiveSheet()->getStyle('K'.$i)->applyFromArray($styleTextNavyBlue);
						$objPHPExcel->getActiveSheet()->getStyle('K'.$i)->getFont()->setSize(14);
						$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':K'.$i)->applyFromArray(allBordersThin());
						$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':J'.$i);	

						$i=$i+2;
						$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, 'Firma del Patrono');
						$objPHPExcel->getActiveSheet()->setCellValue('D'.$i, 'Firma y Cédula del Empleado');
						$objPHPExcel->getActiveSheet()->setCellValue('I'.$i, 'Fecha');
						$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':J'.$i)->applyFromArray($styleTextGranate);
						$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':J'.$i)->getFont()->setSize(10);
						$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':J'.$i)->getAlignment()
											  	      ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':J'.$i)->getAlignment()
											  	      ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
						$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':J'.$i)->getAlignment()->setWrapText(true); 
						$objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(29);

						$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':B'.$i);
						$objPHPExcel->getActiveSheet()->mergeCells('D'.$i.':E'.$i);
						$objPHPExcel->getActiveSheet()->mergeCells('I'.$i.':J'.$i);

						$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':B'.$i)->applyFromArray(allBordersThin());
						$objPHPExcel->getActiveSheet()->getStyle('D'.$i.':E'.$i)->applyFromArray(allBordersThin());
						$objPHPExcel->getActiveSheet()->getStyle('I'.$i.':J'.$i)->applyFromArray(allBordersThin());

						$i++;

						$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':B'.($i+1));
						$objPHPExcel->getActiveSheet()->mergeCells('D'.$i.':E'.($i+1));
						$objPHPExcel->getActiveSheet()->mergeCells('I'.$i.':J'.($i+1));
						$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':B'.($i+1))->applyFromArray(allBordersThin());
						$objPHPExcel->getActiveSheet()->getStyle('D'.$i.':E'.($i+1))->applyFromArray(allBordersThin());
						$objPHPExcel->getActiveSheet()->getStyle('I'.$i.':J'.($i+1))->applyFromArray(allBordersThin());

						$sql2= "SELECT p.suesal AS salario_mensual, 
									   COALESCE( (SELECT ap.valor 
									   			  FROM   nomcampos_adic_personal ap 
									   			  WHERE  ap.id=1 AND ap.ficha=p.ficha), 0) AS gastos_representacion 
								FROM   nompersonal p
								WHERE  p.ficha=".$ficha;
						$res2 = query($sql2, $conexion);
						if($row2 = fetch_array($res2) )
						{ 
							$SALARIO_MENSUAL    = $row2['salario_mensual']; 
							$GASTOS_REP_MENSUAL = $row2['gastos_representacion']; 
						}

						$i=$i+3; $aux3=$i;
						$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, 'Base Para el calculo');
						$i++;	$aux6=$i;
						$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, 'Salario Mensual');
						$objPHPExcel->getActiveSheet()->setCellValue('B'.$i, $SALARIO_MENSUAL);
						$objPHPExcel->getActiveSheet()->setCellValue('C'.$i, 'Salario por Hora');
						$objPHPExcel->getActiveSheet()->setCellValue('E'.$i,  "=B$i/207.84");
						$objPHPExcel->getActiveSheet()->setCellValue('G'.$i, 'Salario por Minutos');
						$objPHPExcel->getActiveSheet()->setCellValue('I'.$i,  "=E$i/60");
						$objPHPExcel->getActiveSheet()->getStyle('E'.$i)->getNumberFormat()
									->setFormatCode('0.00'); 
									// ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_ACCOUNTING);
						$objPHPExcel->getActiveSheet()->getStyle('I'.$i)->getNumberFormat()
									->setFormatCode('0.000');
						$i++;	$aux7=$i;
						$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, 'G. de Rep. Mensual');
						$objPHPExcel->getActiveSheet()->setCellValue('B'.$i, $GASTOS_REP_MENSUAL);
						$objPHPExcel->getActiveSheet()->setCellValue('C'.$i, 'G. de Rep. Por Hora');
						$objPHPExcel->getActiveSheet()->setCellValue('E'.$i,  "=B$i/207.84");
						$objPHPExcel->getActiveSheet()->setCellValue('G'.$i, 'G. de Rep. Por Minutos');
						$objPHPExcel->getActiveSheet()->setCellValue('I'.$i,  "=E$i/60");
						$objPHPExcel->getActiveSheet()->getStyle('E'.$i)->getNumberFormat()
									->setFormatCode('0.00'); 
									//->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_ACCOUNTING);
						$objPHPExcel->getActiveSheet()->getStyle('I'.$i)->getNumberFormat()
									->setFormatCode('0.000'); 

						$i=$i+3;
						$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, 'Fecha')
													  ->setCellValue('B'.$i, 'Horas Extras');
						$objPHPExcel->getActiveSheet()->getStyle('B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$objPHPExcel->getActiveSheet()->mergeCells('B'.$i.':I'.$i);
						$objPHPExcel->getActiveSheet()->getStyle('B'.$i)->getFont()->setBold(true);
						$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':I'.$i)->applyFromArray(allBordersMedium());

						$i++; 
						$objPHPExcel->getActiveSheet()->setCellValue('B'.$i, 'Diurnas')
													  ->setCellValue('B'.($i+1), '25%')
													  ->setCellValue('C'.$i, 'Mixtas')
													  ->setCellValue('C'.($i+1), '75%')
													  ->setCellValue('D'.$i, 'Nocturno')
													  ->setCellValue('D'.($i+1), '50%')
													  ->setCellValue('E'.$i, 'Domingo')
													  ->setCellValue('E'.($i+1), '50%')
													  ->setCellValue('F'.$i, 'Nacional')
													  ->setCellValue('F'.($i+1), '1,5%')
													  ->setCellValue('G'.$i, 'Diurnas')
													  ->setCellValue('G'.($i+1), '25% * 75%')
													  ->setCellValue('H'.$i, 'Mixtas')
													  ->setCellValue('H'.($i+1), '75% * 75%')
													  ->setCellValue('I'.$i, 'Nocturno')
													  ->setCellValue('I'.($i+1), '50% * 75%');

						$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':I'.($i+1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

						$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':I'.($i+1))->applyFromArray(allBordersThin());

						$i=$i+3; $aux4=$i;
						//=============================================================================================
						$sql2= "SELECT   r.fecha, 
										 r.extra    AS 'diurna_25',  r.extraext AS 'diurna_25_75', 
									     r.mixtonoc AS 'mixtas_75',  r.extranoc AS 'nocturna_50',
									     r.domingo  AS 'domingo_50', r.nacional AS 'nacional_1.5',
									     r.mixtoextnoc AS 'mixtas_75_75', r.extraextnoc AS 'nocturna_50_75'   
								FROM     reloj_detalle r 
								WHERE    r.fecha BETWEEN '".$desde."' AND '".$hasta."'
								AND      r.ficha=".$ficha."
								ORDER BY r.fecha";

						$res2 = query($sql2, $conexion);
						while($row2 = fetch_array($res2) )
						{
							$fecha = $row2['fecha'];
							list($aniof, $mesf, $diaf) = explode("-", $fecha);

							$diurna_25    = $row2['diurna_25']; 
							list($horas, $minutos) = explode(":", $diurna_25);
							$EXTRA_DIURNA_25 = $horas * 60 + $minutos;

							$diurna_25_75 = $row2['diurna_25_75'];
							list($horas, $minutos) = explode(":", $diurna_25_75);
							$EXTRA_DIURNA_25_75 = $horas * 60 + $minutos;

							$mixtas_75   = $row2['mixtas_75'];
							list($horas, $minutos) = explode(":", $mixtas_75);
							$EXTRA_MIXTA_75 = $horas * 60 + $minutos; 

							$nocturna_50 = $row2['nocturna_50'];
							list($horas, $minutos) = explode(":", $nocturna_50);
							$EXTRA_NOCTURNA_50 = $horas * 60 + $minutos; 

							$domingo_50 = $row2['domingo_50'];
							list($horas, $minutos) = explode(":", $domingo_50);
							$EXTRA_DOMINGO_50 = $horas * 60 + $minutos;

							$nacional = $row2['nacional_1.5'];
							list($horas, $minutos) = explode(":", $nacional);
							$EXTRA_NACIONAL = $horas * 60 + $minutos;

							$mixtas_75_75 = $row2['mixtas_75_75'];
							list($horas, $minutos) = explode(":", $mixtas_75_75);
							$EXTRA_MIXTA_75_75 = $horas * 60 + $minutos;

							$nocturna_50_75 = $row2['nocturna_50_75'];
							list($horas, $minutos) = explode(":", $nocturna_50_75);
							$EXTRA_NOCTURNA_50_75 = $horas * 60 + $minutos;

							$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, PHPExcel_Shared_Date::FormattedPHPToExcel($aniof, $mesf, $diaf) );
							$objPHPExcel->getActiveSheet()->setCellValue('B'.$i, $EXTRA_DIURNA_25 );
							$objPHPExcel->getActiveSheet()->setCellValue('C'.$i, $EXTRA_MIXTA_75 );
							$objPHPExcel->getActiveSheet()->setCellValue('D'.$i, $EXTRA_NOCTURNA_50 );
							$objPHPExcel->getActiveSheet()->setCellValue('E'.$i, $EXTRA_DOMINGO_50 );
							$objPHPExcel->getActiveSheet()->setCellValue('F'.$i, $EXTRA_NACIONAL );
							$objPHPExcel->getActiveSheet()->setCellValue('G'.$i, $EXTRA_DIURNA_25_75);
							$objPHPExcel->getActiveSheet()->setCellValue('H'.$i, $EXTRA_MIXTA_75_75);
							$objPHPExcel->getActiveSheet()->setCellValue('I'.$i, $EXTRA_NOCTURNA_50_75);

							$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getNumberFormat()
												      	  ->setFormatCode('[$-200A]dddd dd" de "mmmm');	

							$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':I'.$i)->applyFromArray(allBordersThin());
							$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':I'.$i)
														  ->getNumberFormat()
														  ->setFormatCode('0');
							$i++;			
						}

						if($i==$aux4)
						{
							$i=$i+3;
							$objPHPExcel->getActiveSheet()->getStyle('B'.$aux4.':I'.$i)->applyFromArray(allBordersThin());

							$i++;
						}

						//=============================================================================================

						$i++; $aux5= $i-2;
						$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, 'Tiempo Total')
													  ->setCellValue('B'.$i, "=SUM(B$aux4:B$aux5)")
													  ->setCellValue('C'.$i, "=SUM(C$aux4:C$aux5)")
													  ->setCellValue('D'.$i, "=SUM(D$aux4:D$aux5)")
													  ->setCellValue('E'.$i, "=SUM(E$aux4:E$aux5)")
													  ->setCellValue('F'.$i, "=SUM(F$aux4:F$aux5)")
													  ->setCellValue('G'.$i, "=SUM(G$aux4:G$aux5)")
													  ->setCellValue('H'.$i, "=SUM(H$aux4:H$aux5)")
													  ->setCellValue('I'.$i, "=SUM(I$aux4:I$aux5)");

						$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':I'.$i)->applyFromArray(allBordersMedium());

						$i=$i+2;
						$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, 'Monto en Salario')
													  ->setCellValue('B'.$i, '=I'.$aux6.'*1.25*B'.($i-2) )
													  ->setCellValue('C'.$i, '=I'.$aux6.'*1.75*C'.($i-2) )
													  ->setCellValue('D'.$i, '=I'.$aux6.'*1.5*D'.($i-2) )
													  ->setCellValue('E'.$i, '=I'.$aux6.'*0.5*E'.($i-2) )
													  ->setCellValue('F'.$i, '=I'.$aux6.'*1.5*F'.($i-2) )
													  ->setCellValue('G'.$i, '=I'.$aux6.'*1.25*1.75*G'.($i-2) )
													  ->setCellValue('H'.$i, '=I'.$aux6.'*1.75*1.75*H'.($i-2) )
													  ->setCellValue('I'.$i, '=I'.$aux6.'*1.5*1.75*I'.($i-2) );

						$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':I'.$i)
													  ->getNumberFormat()
													  ->setFormatCode('0.00');

						$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':I'.($i+1))->applyFromArray(allBordersThin());

						$i++;

						$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, 'Monto en G. de Rep')
													  ->setCellValue('B'.$i, '=I'.$aux7.'*1.25*B'.($i-3) )
													  ->setCellValue('C'.$i, '=I'.$aux7.'*1.75*C'.($i-3) )
													  ->setCellValue('D'.$i, '=I'.$aux7.'*1.5*D'.($i-3) )
													  ->setCellValue('E'.$i, '=I'.$aux7.'*0.5*E'.($i-3) )
													  ->setCellValue('F'.$i, '=I'.$aux7.'*1.5*F'.($i-3) )
													  ->setCellValue('G'.$i, '=I'.$aux7.'*1.25*1.75*G'.($i-3) )
													  ->setCellValue('H'.$i, '=I'.$aux7.'*1.75*1.75*H'.($i-3) )
													  ->setCellValue('I'.$i, '=I'.$aux7.'*1.5*1.75*I'.($i-3) );	

						$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':I'.$i)
													  ->getNumberFormat()
													  ->setFormatCode('0.00');
						$i++;
						$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, 'Total')
													  ->setCellValue('B'.$i, '=SUM(B'.($i-2).':B'.($i-1).')' )
													  ->setCellValue('C'.$i, '=SUM(C'.($i-2).':C'.($i-1).')' )
													  ->setCellValue('D'.$i, '=SUM(D'.($i-2).':D'.($i-1).')' )
													  ->setCellValue('E'.$i, '=SUM(E'.($i-2).':E'.($i-1).')' )
													  ->setCellValue('F'.$i, '=SUM(F'.($i-2).':F'.($i-1).')' )
													  ->setCellValue('G'.$i, '=SUM(G'.($i-2).':G'.($i-1).')' )
													  ->setCellValue('H'.$i, '=SUM(H'.($i-2).':H'.($i-1).')' )
													  ->setCellValue('I'.$i, '=SUM(I'.($i-2).':I'.($i-1).')' );	

						$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':I'.$i)
													  ->getNumberFormat()
													  ->setFormatCode('0.00');

						$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':I'.$i)->applyFromArray(allBordersMedium());

						$objPHPExcel->getActiveSheet()->getStyle("A".$aux3.":AB".$i)->getFont()->setSize(10);

						$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(21);
						$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(10);
						$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(9);
						$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(9);
						$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(9);
						$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(9);
						$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(9);
						$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(9);
						$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(9);
						$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(9);
						$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(14);

						$objPHPExcel->getActiveSheet()->setSelectedCells('K100');

					//----------------------------------------------------------------------------------------
						$objPHPExcel->setActiveSheetIndex(0);

						$filename = $directorio."/". str_replace(' ', '_', $nombre_completo) .
						            "_Del_".fecha($desde, '-').'_Hasta_'.fecha($hasta, '-')  . ".xls";
						// $filename = "Comprobante_Pago_".$NOMINA."_Del_".fecha($desde, '-').'_Hasta_'.fecha($hasta, '-'); 

						$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
						$objWriter->save($filename);

						chmod($directorio, 0777);
						chmod($filename, 0777);
					//----------------------------------------------------------------------------------------
						if(!empty($email))
						{
							echo $email."<br>";

							$mail = new PHPMailer();
							$mail->IsSMTP();
							$mail->SMTPAuth = true;
							$mail->SMTPDebug = 0;
        					$mail->Host = "smtp.gmail.com";
					        $mail->Port = 587;
					        $mail->SMTPSecure = 'tls';

					        // El correo y contraseña de donde saldran los mensajes.
					        $mail->Username = "planillaexpresspanama@gmail.com";
					        $mail->Password = "S3l3ctr4";

					        //Indicamos cual es nuestra dirección de correo y el nombre que 
					        //queremos que vea el usuario que lee nuestro correo

					        $mail->SetFrom("planillaexpresspanama@gmail.com", "Planillaexpress");

					        // Asunto de mensaje
					        $tit = 'Comprobante de Pago - Desde '.fecha($desde, '-').' Hasta '.fecha($hasta, '-');

					        $mail->Subject = $tit; 

					        $mail->Body = "Adjunto Comprobante de Pago";
					        $mail->IsHTML(true);

					        // Se asigna la dirección de correo a donde se enviará el mensaje.
					           $mail->AddAddress ($email, $nombre_completo);
					        // $mail->AddAddress('marianna.pessolano@gmail.com', 'Marianna Pessolano');

					        // Si hay archivos adjuntos se mandan así.
        					$mail->AddAttachment($filename);
        					
					        // Comprobamos que el correo se ha enviado.
					        if( !$mail->Send() ) 
					        {
					            echo $filename." ".$email."<br>Mailer Error: " . $mail->ErrorInfo . "<br><br>";
					        } 
					        else 
					        {
					            echo $filename." enviado a $email <br><br>";
					        }
					        
					        $mail->ClearAddresses();
					        $mail->ClearAttachments();
						}
				}			
			//=================================================================================================================
		}
		else
		{
			echo "No se pudo crear el directorio por favor vuelva a intentar";
			//exit(0);			
		}
	/* }
	else
	{
		echo "<div align='center' style=\"background-color : #84225b; color : #fdfdfd; font-family : 'Arial Black'; font-size : 15px;\">El directorio ya fue creado anteriormente</div>";
		//exit(0);			
	}
	*/
}
