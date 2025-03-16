<?php
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('America/Panama');

if (PHP_SAPI == 'cli')
	die('Este ejemplo solo se debe ejecutar desde un navegador Web');

include('../lib/database.php');
include('lib/php_excel.php');

if(isset($_GET['codnom']) && isset($_GET['codtip']))
{
	$codnom         =$_GET['codnom'];
	$codtip         =$_GET['codtip'];
	
	$db             = new Database($_SESSION['bd']);
	//echo $_SESSION['bd'],"<br>";
	$sql            = "SELECT UPPER(t.descrip) as descrip 
	FROM   nomtipos_nomina t
	WHERE  t.codtip =".$codtip;
	$res            =$db->query($sql);
	if($fila=$res->fetch_assoc())
	{
		$NOMINA = $fila['descrip']; 
	}

	require_once 'phpexcel/Classes/PHPExcel.php';

	$objPHPExcel = new PHPExcel();

	$objPHPExcel->getProperties()->setCreator("Selectra")
								 ->setLastModifiedBy("Selectra")
								 ->setTitle("DESGLOSE DE BILLETES Y MONEDAS");

	$objPHPExcel->getDefaultStyle()->getFont()->setName('Calibri');
	// $objPHPExcel->getActiveSheet()->getStyle('A2:A4')->getFont()->setName('Calibri');
	$objPHPExcel->getDefaultStyle()->getFont()->setSize(11);


	$sql             = "SELECT e.nom_emp as empresa, e.rif, e.tel_emp as telefono, e.dir_emp as direccion, e.ciu_emp as ciudad, 
	e.edo_emp, e.imagen_izq as logo
	FROM   nomempresa e";
	$res             =$db->query($sql);
	$fila            =$res->fetch_assoc();
	$logo            =$fila['logo'];
	
	$sql2            = "SELECT np.periodo_ini as desde, np.periodo_fin as hasta, np.mes, np.anio,
	DATE_FORMAT(np.periodo_ini,'%d') as dia_ini, DATE_FORMAT(np.periodo_fin, '%d') as dia_fin   
	FROM nom_nominas_pago np 
	WHERE  np.codnom =".$codnom." AND np.tipnom=".$codtip;
	$res2            =$db->query($sql2);
	$fila2           =$res2->fetch_assoc();
	
	$meses           = array('Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre');
	
	$desde           =$fila2['desde'];
	$hasta           =$fila2['hasta'];
	$dia_ini         = $fila2['dia_ini'];
	$dia_fin         = $fila2['dia_fin']; 
	$mes_numero      = $fila2['mes'];
	$mes_letras      = $meses[$mes_numero - 1];
	$anio            = $fila2['anio'];
	
	
	$empresa         = $fila['empresa'];
	$objPHPExcel->getActiveSheet()
	            ->setCellValue('A2', strtoupper($empresa) )
	            ->setCellValue('A3', 'DESGLOSE DE MONEDAS')
	            ->setCellValue('A4', 'DEL '. $dia_ini .' AL '. $dia_fin .' de '. $mes_letras .' '. $anio );

	//$objPHPExcel->getActiveSheet()->getStyle('A2:A4')->getFont()->setName('Calibri');
	$objPHPExcel->getActiveSheet()->getStyle('A2:A4')->getFont()->setSize(14);
	$objPHPExcel->getActiveSheet()->getStyle('A2:A4')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('A2:A4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->mergeCells('A2:O2');
	$objPHPExcel->getActiveSheet()->mergeCells('A3:O3');
	$objPHPExcel->getActiveSheet()->mergeCells('A4:O4');


	//$objPHPExcel->getActiveSheet()->getStyle('C2:D5')->applyFromArray(allBordersThin());
	//$objPHPExcel->getActiveSheet()->getStyle('C2:D5')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);


	$styleArray = array(
			'font'  => array(
			'bold'  => false,
			'color' => array('rgb' => 'FFFFFF'),
			'size'  => 12,
			'name'  => 'Calibri'
	    ));
	$objPHPExcel->getActiveSheet()->mergeCells('B7:C7');

	$objPHPExcel->getActiveSheet()
	            ->setCellValue('B7', 'TRABAJADOR')
	            ->setCellValue('D7', 'CODIGO')
	            ->setCellValue('E7', 'SUELDO')
	            ->setCellValue('F7', 'BILLETES')
	            ->setCellValue('J7', 'MONEDAS')
	            ->setCellValue('O7', 'MONTO');
	$objPHPExcel->getActiveSheet()->mergeCells('F7:I7');
	$objPHPExcel->getActiveSheet()->mergeCells('J7:N7');


	$objPHPExcel->getActiveSheet()->getStyle('B7')->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle('D7')->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle('E7')->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle('F7')->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle('J7')->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle('O7')->applyFromArray($styleArray);
	cellColor('B7:O7', '0f243e');
	$objPHPExcel->getActiveSheet()
	            ->setCellValue('B8', 'REGULAR AGENTES')
	            ->setCellValue('F8', '20')
	            ->setCellValue('G8', '10')
	            ->setCellValue('H8', '5')
	            ->setCellValue('I8', '1')
	            ->setCellValue('J8', '50c')
	            ->setCellValue('K8', '25c')
	            ->setCellValue('L8', '10c')
	            ->setCellValue('M8', '5c')
	            ->setCellValue('N8', '1c')
	            ->setCellValue('O8', '');
	$objPHPExcel->getActiveSheet()->mergeCells('B8:C8');

	$objPHPExcel->getActiveSheet()->getStyle('B8')->applyFromArray($styleArray);

	$objPHPExcel->getActiveSheet()->getStyle('C8')->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle('D8')->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle('E8')->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle('F8')->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle('G8')->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle('H8')->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle('I8')->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle('J8')->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle('K8')->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle('L8')->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle('M8')->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle('N8')->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle('O8')->applyFromArray($styleArray);
	cellColor('B8:O8', '0f243e');

	$objPHPExcel->getActiveSheet()->getStyle('A7:O8')->getFont()->setSize(12);
	$objPHPExcel->getActiveSheet()->getStyle('A7:O8')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('A7:O8')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

	$objPHPExcel->getActiveSheet()->getStyle('A7:O8')->applyFromArray(allBordersThin());



	$i              =9;
	$fin=$i;
	$consulta       = "	SELECT nn.codorg, nn.descrip
						FROM   nomnivel1 nn
						WHERE  nn.descrip NOT IN('Eventual', 'O&M')
						ORDER BY nn.codorg";
	$result         =$db->query($consulta);
		$i              =9;

	while($filas=$result->fetch_assoc()) //Inicio
	{
		$codorg = $filas['codorg'];
		//echo $filas['codorg'], " ",$filas['descrip'],"<br>";
		$sql2= "SELECT np.ficha, CONCAT_WS(' ', np.nombres, np.apellidos) as nombre,
					   SUBSTRING_INDEX(SUBSTRING_INDEX(np.nombres, ' ', 1), ' ', -1) as primer_nombre,
					   CASE WHEN np.apellidos LIKE 'De %' THEN 
							     SUBSTRING_INDEX(SUBSTRING_INDEX(np.apellidos, ' ', 2), ' ',  2) 
					   ELSE      SUBSTRING_INDEX(SUBSTRING_INDEX(np.apellidos, ' ', 1), ' ', -1) 
					   END as primer_apellido
				FROM   nompersonal np
				WHERE  np.codnivel1=".$codorg." AND np.tipnom=".$codtip." AND np.tipemp='Fijo'
				AND    np.ficha IN (SELECT DISTINCT n.ficha FROM nom_movimientos_nomina n WHERE n.codnom=".$codnom." AND n.tipnom=np.tipnom)
				ORDER BY np.ficha";

		$res2 =$db->query($sql2);
			$salario_departamento=0;	

		while($row2= $res2->fetch_assoc()) //Inicio
		{	
			$ficha          = $row2['ficha'];
			$sql3           = "	SELECT n.monto as salario 
			FROM nom_movimientos_nomina n 
			WHERE  n.codnom =".$codnom." AND n.ficha=".$ficha." AND n.codcon=100";
			$res3           =$db->query($sql3);
			while($row3 = $res3->fetch_assoc()) //Inicio
			{
				$objPHPExcel->getActiveSheet()->setCellValue('B'.$i, $row2['nombre']);
				$objPHPExcel->getActiveSheet()->setCellValue('D'.$i, $ficha);
				$objPHPExcel->getActiveSheet()->setCellValue('E'.$i, $row3['salario']);
				$total_salario+=$row3['salario'];
				$salario_departamento+=$row3['salario'];
				// indicamos todas las monedas posibles
				$monedas      =array(20, 10, 5, 1, 0.5, 0.25, 0.10, 0.05, 0.01);
				//Dolares
				
				// creamos un array con la misma cantidad de monedas
				// Este array contendra las monedas a devolver
				$cambio       =array(0,0,0,0,0,0,0,0,0);
				
				// Recorremos todas las monedas
				$importe=$row3['salario'];
				for($ixx      =0; $ixx<count($monedas); $ixx++)
				{
				// Si el importe actual, es superior a la moneda
					if($importe   >=$monedas[$ixx])
					{
					
					// obtenemos cantidad de monedas
						$cambio[$ixx] =floor($importe/$monedas[$ixx]);
						// actualizamos el valor del importe que nos queda por didivir
						$importe      =$importe-($cambio[$ixx]*$monedas[$ixx]);
						switch ($ixx) {
							case 0:
								$total_b20+=$cambio[$ixx];
								break;
							case 1:
								$total_b10+=$cambio[$ixx];
								break;
							case 2:
								$total_b5+=$cambio[$ixx];
								break;
							case 3:
								$total_b1+=$cambio[$ixx];
								break;
							case 4:
								$total_m50+=$cambio[$ixx];
								break;
							case 5:
								$total_m25+=$cambio[$ixx];
								break;
							case 6:
								$total_m10+=$cambio[$ixx];
								break;
							case 7:
								$total_m5+=$cambio[$ixx];
								break;
							case 8:
								$total_m1+=$cambio[$ixx];
								break;
						}
					}
					

				}
				//echo $filas['codorg'], " ", $row2['nombre']," ", $row2['ficha']," ",$row3['salario'];

				$Caracter=F;			
				$lenght=count($cambio);
				for ($jxx=0; $jxx <  count($cambio) ; $jxx++) {
 
						$objPHPExcel->getActiveSheet()->setCellValue($Caracter.$i, $cambio[$jxx]);
										$Caracter++;

				}
				$objPHPExcel->getActiveSheet()->setCellValue($Caracter.$i, $row3['salario']);
				$i++;			

			}
		}
	$objPHPExcel->getActiveSheet()->setCellValue('B'.$i, 'Total de Salarios '. $filas['descrip'] );
	$objPHPExcel->getActiveSheet()->setCellValue('O'.$i, $salario_departamento);
	cellColor('B'.$i.':O'.$i, 'ffff66');
	$objPHPExcel->getActiveSheet()->getStyle('O'.$i)->getFont()->setSize(12);
	$objPHPExcel->getActiveSheet()->getStyle('O'.$i)->getFont()->setBold(true);
	$i++;
	$i++;

	$objPHPExcel->getActiveSheet()
	            ->setCellValue('B'.$i, 'REGULAR AGENTES HORAS EXTRAS');

	$objPHPExcel->getActiveSheet()->mergeCells('B'.$i.':F'.$i);

	$objPHPExcel->getActiveSheet()->getStyle('B'.$i)->applyFromArray($styleArray);

	$objPHPExcel->getActiveSheet()->getStyle('C'.$i)->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle('D'.$i)->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle('E'.$i)->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle('F'.$i)->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle('G'.$i)->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle('H'.$i)->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle('I'.$i)->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle('J'.$i)->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle('K'.$i)->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle('L'.$i)->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle('M'.$i)->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle('N'.$i)->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle('O'.$i)->applyFromArray($styleArray);
	cellColor('B'.$i.':O'.$i, '0f243e');

	$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':O'.$i)->getFont()->setSize(12);
	$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':O'.$i)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':O'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

	$i++;
	$i++;
	$sql2= "SELECT np.ficha, CONCAT_WS(' ', np.nombres, np.apellidos) as nombre,
					   SUBSTRING_INDEX(SUBSTRING_INDEX(np.nombres, ' ', 1), ' ', -1) as primer_nombre,
					   CASE WHEN np.apellidos LIKE 'De %' THEN 
							     SUBSTRING_INDEX(SUBSTRING_INDEX(np.apellidos, ' ', 2), ' ',  2) 
					   ELSE      SUBSTRING_INDEX(SUBSTRING_INDEX(np.apellidos, ' ', 1), ' ', -1) 
					   END as primer_apellido
				FROM   nompersonal np
				WHERE  np.codnivel1=".$codorg." AND np.tipnom=".$codtip."
				AND    np.ficha IN (SELECT DISTINCT n.ficha FROM nom_movimientos_nomina n WHERE n.codnom=".$codnom." AND n.tipnom=np.tipnom)
				ORDER BY np.ficha";

		$res2 =$db->query($sql2);
		$salario_departamento=0;
		while($row2= $res2->fetch_assoc())//Inicio
		{	
			$ficha          = $row2['ficha'];
			$sql3           = "	SELECT n.monto as salario 
			FROM nom_movimientos_nomina n 
			WHERE  n.codnom =".$codnom." AND n.ficha=".$ficha." AND n.codcon=106";
			$res3           =$db->query($sql3);
			while($row3 = $res3->fetch_assoc())//Inicio
			{
				$objPHPExcel->getActiveSheet()->setCellValue('B'.$i, $row2['nombre']);
				$objPHPExcel->getActiveSheet()->setCellValue('D'.$i, $ficha);
				$objPHPExcel->getActiveSheet()->setCellValue('E'.$i, $row3['salario']);
				$total_servicios_profesionales+=$row3['salario'];
				$salario_departamento+=$row3['salario'];
				// indicamos todas las monedas posibles
				$monedas      =array(20, 10, 5, 1, 0.5, 0.25, 0.10, 0.05, 0.01);
				//Dolares
				
				// creamos un array con la misma cantidad de monedas
				// Este array contendra las monedas a devolver
				$cambio       =array(0,0,0,0,0,0,0,0,0);
				
				// Recorremos todas las monedas
				$importe=$row3['salario'];
				for($ixx      =0; $ixx<count($monedas); $ixx++)
				{
				// Si el importe actual, es superior a la moneda
					if($importe   >=$monedas[$ixx])
					{
					
					// obtenemos cantidad de monedas
						$cambio[$ixx] =floor($importe/$monedas[$ixx]);
						// actualizamos el valor del importe que nos queda por didivir
						$importe      =$importe-($cambio[$ixx]*$monedas[$ixx]);
						switch ($ixx) {
							case 0:
								$total_b20+=$cambio[$ixx];
								break;
							case 1:
								$total_b10+=$cambio[$ixx];
								break;
							case 2:
								$total_b5+=$cambio[$ixx];
								break;
							case 3:
								$total_b1+=$cambio[$ixx];
								break;
							case 4:
								$total_m50+=$cambio[$ixx];
								break;
							case 5:
								$total_m25+=$cambio[$ixx];
								break;
							case 6:
								$total_m10+=$cambio[$ixx];
								break;
							case 7:
								$total_m5+=$cambio[$ixx];
								break;
							case 8:
								$total_m1+=$cambio[$ixx];
								break;
						}
					}
					

				}
				//echo $filas['codorg'], " ", $row2['nombre']," ", $row2['ficha']," ",$row3['salario'];

				$Caracter=F;			
				$lenght=count($cambio);
				for ($jxx=0; $jxx <  count($cambio) ; $jxx++) {
 
						$objPHPExcel->getActiveSheet()->setCellValue($Caracter.$i, $cambio[$jxx]);
										$Caracter++;

				}
				$objPHPExcel->getActiveSheet()->setCellValue($Caracter.$i, $row3['salario']);
				$i++;			

			}
		}

	$objPHPExcel->getActiveSheet()->mergeCells('B'.$i.':F'.$i);

	$objPHPExcel->getActiveSheet()->getStyle('B'.$i)->applyFromArray($styleArray);

	$objPHPExcel->getActiveSheet()->getStyle('C'.$i)->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle('D'.$i)->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle('E'.$i)->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle('F'.$i)->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle('G'.$i)->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle('H'.$i)->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle('I'.$i)->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle('J'.$i)->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle('K'.$i)->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle('L'.$i)->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle('M'.$i)->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle('N'.$i)->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle('O'.$i)->applyFromArray($styleArray);
	cellColor('B'.$i.':O'.$i, '0f243e');

	$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':O'.$i)->getFont()->setSize(12);
	$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':O'.$i)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':O'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

	$i++;
	$i++;
	$sql2= "SELECT np.ficha, CONCAT_WS(' ', np.nombres, np.apellidos) as nombre,
					   SUBSTRING_INDEX(SUBSTRING_INDEX(np.nombres, ' ', 1), ' ', -1) as primer_nombre,
					   CASE WHEN np.apellidos LIKE 'De %' THEN 
							     SUBSTRING_INDEX(SUBSTRING_INDEX(np.apellidos, ' ', 2), ' ',  2) 
					   ELSE      SUBSTRING_INDEX(SUBSTRING_INDEX(np.apellidos, ' ', 1), ' ', -1) 
					   END as primer_apellido
				FROM   nompersonal np
				WHERE  np.codnivel1=".$codorg." AND np.tipnom=".$codtip."
				AND    np.ficha IN (SELECT DISTINCT n.ficha FROM nom_movimientos_nomina n WHERE n.codnom=".$codnom." AND n.tipnom=np.tipnom)
				ORDER BY np.ficha";

		$res2 =$db->query($sql2);
		$salario_departamento=0;
		while($row2= $res2->fetch_assoc())//Inicio
		{	
			$ficha          = $row2['ficha'];
			$sql3           = "	SELECT n.monto as salario 
			FROM nom_movimientos_nomina n 
			WHERE  n.codnom =".$codnom." AND n.ficha=".$ficha." AND n.codcon=102";
			$res3           =$db->query($sql3);
			while($row3 = $res3->fetch_assoc())//Inicio
			{
				$objPHPExcel->getActiveSheet()->setCellValue('B'.$i, $row2['nombre']);
				$objPHPExcel->getActiveSheet()->setCellValue('D'.$i, $ficha);
				$objPHPExcel->getActiveSheet()->setCellValue('E'.$i, $row3['salario']);
				$total_servicios_profesionales+=$row3['salario'];
				$salario_departamento+=$row3['salario'];
				// indicamos todas las monedas posibles
				$monedas      =array(20, 10, 5, 1, 0.5, 0.25, 0.10, 0.05, 0.01);
				//Dolares
				
				// creamos un array con la misma cantidad de monedas
				// Este array contendra las monedas a devolver
				$cambio       =array(0,0,0,0,0,0,0,0,0);
				
				// Recorremos todas las monedas
				$importe=$row3['salario'];
				for($ixx      =0; $ixx<count($monedas); $ixx++)
				{
				// Si el importe actual, es superior a la moneda
					if($importe   >=$monedas[$ixx])
					{
					
					// obtenemos cantidad de monedas
						$cambio[$ixx] =floor($importe/$monedas[$ixx]);
						// actualizamos el valor del importe que nos queda por didivir
						$importe      =$importe-($cambio[$ixx]*$monedas[$ixx]);
						switch ($ixx) {
							case 0:
								$total_b20+=$cambio[$ixx];
								break;
							case 1:
								$total_b10+=$cambio[$ixx];
								break;
							case 2:
								$total_b5+=$cambio[$ixx];
								break;
							case 3:
								$total_b1+=$cambio[$ixx];
								break;
							case 4:
								$total_m50+=$cambio[$ixx];
								break;
							case 5:
								$total_m25+=$cambio[$ixx];
								break;
							case 6:
								$total_m10+=$cambio[$ixx];
								break;
							case 7:
								$total_m5+=$cambio[$ixx];
								break;
							case 8:
								$total_m1+=$cambio[$ixx];
								break;
						}
					}
					

				}
				//echo $filas['codorg'], " ", $row2['nombre']," ", $row2['ficha']," ",$row3['salario'];

				$Caracter=F;			
				$lenght=count($cambio);
				for ($jxx=0; $jxx <  count($cambio) ; $jxx++) {
 
						$objPHPExcel->getActiveSheet()->setCellValue($Caracter.$i, $cambio[$jxx]);
										$Caracter++;

				}
				$objPHPExcel->getActiveSheet()->setCellValue($Caracter.$i, $row3['salario']);
				$i++;			

			}
		}

	$objPHPExcel->getActiveSheet()->setCellValue('B'.$i, 'XIII '. $filas['descrip'] );
	$objPHPExcel->getActiveSheet()->setCellValue('O'.$i, $salario_departamento);
	cellColor('B'.$i.':O'.$i, 'ffff66');
	$objPHPExcel->getActiveSheet()->getStyle('O'.$i)->getFont()->setSize(12);
	$objPHPExcel->getActiveSheet()->getStyle('O'.$i)->getFont()->setBold(true);
	$i++;
	$i++;
	$objPHPExcel->getActiveSheet()
	            ->setCellValue('B'.$i, 'CONTRATADOS SERVICIOS PROFESIONALES ');

	$objPHPExcel->getActiveSheet()->mergeCells('B'.$i.':F'.$i);

	$objPHPExcel->getActiveSheet()->getStyle('B'.$i)->applyFromArray($styleArray);

	$objPHPExcel->getActiveSheet()->getStyle('C'.$i)->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle('D'.$i)->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle('E'.$i)->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle('F'.$i)->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle('G'.$i)->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle('H'.$i)->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle('I'.$i)->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle('J'.$i)->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle('K'.$i)->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle('L'.$i)->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle('M'.$i)->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle('N'.$i)->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle('O'.$i)->applyFromArray($styleArray);
	cellColor('B'.$i.':O'.$i, '0f243e');

	$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':O'.$i)->getFont()->setSize(12);
	$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':O'.$i)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':O'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

	$i++;
	$sql2= "SELECT np.ficha, CONCAT_WS(' ', np.nombres, np.apellidos) as nombre,
					   SUBSTRING_INDEX(SUBSTRING_INDEX(np.nombres, ' ', 1), ' ', -1) as primer_nombre,
					   CASE WHEN np.apellidos LIKE 'De %' THEN 
							     SUBSTRING_INDEX(SUBSTRING_INDEX(np.apellidos, ' ', 2), ' ',  2) 
					   ELSE      SUBSTRING_INDEX(SUBSTRING_INDEX(np.apellidos, ' ', 1), ' ', -1) 
					   END as primer_apellido
				FROM   nompersonal np
				WHERE  np.codnivel1=".$codorg." AND np.tipnom=".$codtip." AND np.tipemp='Contratado Servicios'
				AND    np.ficha IN (SELECT DISTINCT n.ficha FROM nom_movimientos_nomina n WHERE n.codnom=".$codnom." AND n.tipnom=np.tipnom)
				ORDER BY np.ficha";

		$res2 =$db->query($sql2);
		$salario_departamento=0;
		while($row2= $res2->fetch_assoc())
		{	
			$ficha          = $row2['ficha'];
			$sql3           = "	SELECT n.monto as salario 
			FROM nom_movimientos_nomina n 
			WHERE  n.codnom =".$codnom." AND n.ficha=".$ficha." AND n.codcon=145";
			$res3           =$db->query($sql3);
			while($row3 = $res3->fetch_assoc())
			{
				$objPHPExcel->getActiveSheet()->setCellValue('B'.$i, $row2['nombre']);
				$objPHPExcel->getActiveSheet()->setCellValue('D'.$i, $ficha);
				$objPHPExcel->getActiveSheet()->setCellValue('E'.$i, $row3['salario']);
				$total_horas_extras+=$row3['salario'];
				$salario_departamento+=$row3['salario'];
				// indicamos todas las monedas posibles
				$monedas      =array(20, 10, 5, 1, 0.5, 0.25, 0.10, 0.05, 0.01);
				//Dolares
				
				// creamos un array con la misma cantidad de monedas
				// Este array contendra las monedas a devolver
				$cambio       =array(0,0,0,0,0,0,0,0,0);
				
				// Recorremos todas las monedas
				$importe=$row3['salario'];
				for($ixx      =0; $ixx<count($monedas); $ixx++)
				{
				// Si el importe actual, es superior a la moneda
					if($importe   >=$monedas[$ixx])
					{
					
					// obtenemos cantidad de monedas
						$cambio[$ixx] =floor($importe/$monedas[$ixx]);
						// actualizamos el valor del importe que nos queda por didivir
						$importe      =$importe-($cambio[$ixx]*$monedas[$ixx]);
						switch ($ixx) {
							case 0:
								$total_b20+=$cambio[$ixx];
								break;
							case 1:
								$total_b10+=$cambio[$ixx];
								break;
							case 2:
								$total_b5+=$cambio[$ixx];
								break;
							case 3:
								$total_b1+=$cambio[$ixx];
								break;
							case 4:
								$total_m50+=$cambio[$ixx];
								break;
							case 5:
								$total_m25+=$cambio[$ixx];
								break;
							case 6:
								$total_m10+=$cambio[$ixx];
								break;
							case 7:
								$total_m5+=$cambio[$ixx];
								break;
							case 8:
								$total_m1+=$cambio[$ixx];
								break;
						}
					}
					

				}
				//echo $filas['codorg'], " ", $row2['nombre']," ", $row2['ficha']," ",$row3['salario'];

				$Caracter=F;			
				$lenght=count($cambio);
				for ($jxx=0; $jxx <  count($cambio) ; $jxx++) {
 
						$objPHPExcel->getActiveSheet()->setCellValue($Caracter.$i, $cambio[$jxx]);
										$Caracter++;

				}
				$objPHPExcel->getActiveSheet()->setCellValue($Caracter.$i, $row3['salario']);
				$i++;			

			}
		}


	$objPHPExcel->getActiveSheet()->setCellValue('B'.$i, 'Total de Servicios Profesionales Contratados '. $filas['descrip'] );
	$objPHPExcel->getActiveSheet()->setCellValue('O'.$i, $salario_departamento);
	cellColor('B'.$i.':O'.$i, 'ffff66');
	$objPHPExcel->getActiveSheet()->getStyle('O'.$i)->getFont()->setSize(12);
	$objPHPExcel->getActiveSheet()->getStyle('O'.$i)->getFont()->setBold(true);
	$i++;
	$i++;	
	
	$total=$total_salario+$total_horas_extras+$total_servicios_profesionales;
	$objPHPExcel->getActiveSheet()->mergeCells('M'.$i.':N'.$i);
	$objPHPExcel->getActiveSheet()->setCellValue('M'.$i, 'TOTAL');
	$objPHPExcel->getActiveSheet()->setCellValue('O'.$i, $total);
	cellColor('B'.$i.':O'.$i, '0f243e');
	$styleArray = array(
			'font'  => array(
			'bold'  => true,
			'color' => array('rgb' => 'FFFFFF'),
			'size'  => 12,
			'name'  => 'Calibri'
	    ));
	$objPHPExcel->getActiveSheet()->getStyle('M'.$i)->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle('O'.$i)->applyFromArray($styleArray);

$objPHPExcel->getActiveSheet()
	            ->setCellValue('B'.$i, 'REGULAR AGENTES SERVICIOS PROFESIONALES');

	$objPHPExcel->getActiveSheet()->mergeCells('B'.$i.':F'.$i);

	$objPHPExcel->getActiveSheet()->getStyle('B'.$i)->applyFromArray($styleArray);

	$objPHPExcel->getActiveSheet()->getStyle('C'.$i)->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle('D'.$i)->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle('E'.$i)->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle('F'.$i)->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle('G'.$i)->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle('H'.$i)->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle('I'.$i)->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle('J'.$i)->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle('K'.$i)->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle('L'.$i)->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle('M'.$i)->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle('N'.$i)->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle('O'.$i)->applyFromArray($styleArray);
	cellColor('B'.$i.':O'.$i, '0f243e');

	$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':O'.$i)->getFont()->setSize(12);
	$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':O'.$i)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':O'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

	$i++;
	$sql2= "SELECT np.ficha, CONCAT_WS(' ', np.nombres, np.apellidos) as nombre,
					   SUBSTRING_INDEX(SUBSTRING_INDEX(np.nombres, ' ', 1), ' ', -1) as primer_nombre,
					   CASE WHEN np.apellidos LIKE 'De %' THEN 
							     SUBSTRING_INDEX(SUBSTRING_INDEX(np.apellidos, ' ', 2), ' ',  2) 
					   ELSE      SUBSTRING_INDEX(SUBSTRING_INDEX(np.apellidos, ' ', 1), ' ', -1) 
					   END as primer_apellido
				FROM   nompersonal np
				WHERE  np.codnivel1=".$codorg." AND np.tipnom=".$codtip." 
				AND    np.ficha IN (SELECT DISTINCT n.ficha FROM nom_movimientos_nomina n WHERE n.codnom=".$codnom." AND n.tipnom=np.tipnom)
				ORDER BY np.ficha";

		$res2 =$db->query($sql2);
		$salario_departamento=0;
		while($row2= $res2->fetch_assoc())
		{	
			$ficha          = $row2['ficha'];
			$sql3           = "	SELECT n.monto as salario 
			FROM nom_movimientos_nomina n 
			WHERE  n.codnom =".$codnom." AND n.ficha=".$ficha." AND n.codcon=145";
			$res3           =$db->query($sql3);
			while($row3 = $res3->fetch_assoc())
			{
				$objPHPExcel->getActiveSheet()->setCellValue('B'.$i, $row2['nombre']);
				$objPHPExcel->getActiveSheet()->setCellValue('D'.$i, $ficha);
				$objPHPExcel->getActiveSheet()->setCellValue('E'.$i, $row3['salario']);
				$total_horas_extras+=$row3['salario'];
				$salario_departamento+=$row3['salario'];
				// indicamos todas las monedas posibles
				$monedas      =array(20, 10, 5, 1, 0.5, 0.25, 0.10, 0.05, 0.01);
				//Dolares
				
				// creamos un array con la misma cantidad de monedas
				// Este array contendra las monedas a devolver
				$cambio       =array(0,0,0,0,0,0,0,0,0);
				
				// Recorremos todas las monedas
				$importe=$row3['salario'];
				for($ixx      =0; $ixx<count($monedas); $ixx++)
				{
				// Si el importe actual, es superior a la moneda
					if($importe   >=$monedas[$ixx])
					{
					
					// obtenemos cantidad de monedas
						$cambio[$ixx] =floor($importe/$monedas[$ixx]);
						// actualizamos el valor del importe que nos queda por didivir
						$importe      =$importe-($cambio[$ixx]*$monedas[$ixx]);
						switch ($ixx) {
							case 0:
								$total_b20+=$cambio[$ixx];
								break;
							case 1:
								$total_b10+=$cambio[$ixx];
								break;
							case 2:
								$total_b5+=$cambio[$ixx];
								break;
							case 3:
								$total_b1+=$cambio[$ixx];
								break;
							case 4:
								$total_m50+=$cambio[$ixx];
								break;
							case 5:
								$total_m25+=$cambio[$ixx];
								break;
							case 6:
								$total_m10+=$cambio[$ixx];
								break;
							case 7:
								$total_m5+=$cambio[$ixx];
								break;
							case 8:
								$total_m1+=$cambio[$ixx];
								break;
						}
					}
					

				}
				//echo $filas['codorg'], " ", $row2['nombre']," ", $row2['ficha']," ",$row3['salario'];

				$Caracter=F;			
				$lenght=count($cambio);
				for ($jxx=0; $jxx <  count($cambio) ; $jxx++) {
 
						$objPHPExcel->getActiveSheet()->setCellValue($Caracter.$i, $cambio[$jxx]);
										$Caracter++;

				}
				$objPHPExcel->getActiveSheet()->setCellValue($Caracter.$i, $row3['salario']);
				$i++;			

			}
		}


	$objPHPExcel->getActiveSheet()->setCellValue('B'.$i, 'Total de Servicios Profesionales '. $filas['descrip'] );
	$objPHPExcel->getActiveSheet()->setCellValue('O'.$i, $salario_departamento);
	cellColor('B'.$i.':O'.$i, 'ffff66');
	$objPHPExcel->getActiveSheet()->getStyle('O'.$i)->getFont()->setSize(12);
	$objPHPExcel->getActiveSheet()->getStyle('O'.$i)->getFont()->setBold(true);
	$i++;
	$i++;	
	}
	$total=$total_salario+$total_horas_extras+$total_servicios_profesionales;
	$objPHPExcel->getActiveSheet()->mergeCells('M'.$i.':N'.$i);
	$objPHPExcel->getActiveSheet()->setCellValue('M'.$i, 'TOTAL');
	$objPHPExcel->getActiveSheet()->setCellValue('O'.$i, $total);
	cellColor('B'.$i.':O'.$i, '0f243e');
	$styleArray = array(
			'font'  => array(
			'bold'  => true,
			'color' => array('rgb' => 'FFFFFF'),
			'size'  => 12,
			'name'  => 'Calibri'
	    ));
	$objPHPExcel->getActiveSheet()->getStyle('M'.$i)->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle('O'.$i)->applyFromArray($styleArray);



	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(1);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(10);

	$objPHPExcel->getActiveSheet()->setTitle("Desglose Planilla");	
	//===========================================================================
	//===========================================================================
	//===========================================================================
	//===========================================================================

	$objPHPExcel->createSheet();
	$objPHPExcel->setActiveSheetIndex(1); 
	$objPHPExcel->getActiveSheet()->setTitle('Bancos');

	$objPHPExcel->getDefaultStyle()->getFont()->setName('Calibri');
	// $objPHPExcel->getActiveSheet()->getStyle('A2:A4')->getFont()->setName('Calibri');
	$objPHPExcel->getDefaultStyle()->getFont()->setSize(11);
	
	$empresa         = $fila['empresa'];
	$objPHPExcel->getActiveSheet()
	            ->setCellValue('B2', strtoupper($empresa) )
	            ->setCellValue('B3', 'DESGLOSE DE MONEDAS')
	            ->setCellValue('B4', 'DEL '. $dia_ini .' AL '. $dia_fin .' de '. $mes_letras .' '. $anio );
	$objPHPExcel->getActiveSheet()->getStyle('B2:B4')->getFont()->setSize(14);
	$objPHPExcel->getActiveSheet()->getStyle('B2:B4')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('B2:B4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->mergeCells('B2:F2');
	$objPHPExcel->getActiveSheet()->mergeCells('B3:F3');
	$objPHPExcel->getActiveSheet()->mergeCells('B4:F4');
	cellColor('B7:F7', '0f243e');
	$objPHPExcel->getActiveSheet()->mergeCells('B7:F7');
	$styleArray = array(
			'font'  => array(
			'bold'  => false,
			'color' => array('rgb' => 'FFFFFF'),
			'size'  => 12,
			'name'  => 'Calibri'
	    ));

	$objPHPExcel->getActiveSheet()->setCellValue('B7', 'DETALLE EFECTIVO');
	$objPHPExcel->getActiveSheet()->mergeCells('D8:F8');
	$objPHPExcel->getActiveSheet()->getStyle('B7')->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle('B7')->getFont()->setSize(14);
	$objPHPExcel->getActiveSheet()->getStyle('B7')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('B7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->setCellValue('C8', 'MONTO');	
	$objPHPExcel->getActiveSheet()->setCellValue('D8', 'CANTIDAD');	
	$objPHPExcel->getActiveSheet()->getStyle('C8:D8')->applyFromArray($styleArray);

	$objPHPExcel->getActiveSheet()->getStyle('C8:D8')->getFont()->setSize(12);
	$objPHPExcel->getActiveSheet()->getStyle('C8:D8')->getFont()->setBold(true);	
	cellColor('B8:F8', '0f243e');
	$objPHPExcel->getActiveSheet()->mergeCells('E9:F9');

	$objPHPExcel->getActiveSheet()->setCellValue('B9', '20');	
	$objPHPExcel->getActiveSheet()->setCellValue('C9', $total_b20*20);	
	$objPHPExcel->getActiveSheet()->setCellValue('D9', $total_b20);	
	$objPHPExcel->getActiveSheet()->mergeCells('E9:F9');
	$objPHPExcel->getActiveSheet()->setCellValue('E9', 'BILLETES DE 20');

	$objPHPExcel->getActiveSheet()->setCellValue('B10', '10');	
	$objPHPExcel->getActiveSheet()->setCellValue('C10', $total_b10*10);	
	$objPHPExcel->getActiveSheet()->setCellValue('D10', $total_b10);	
	$objPHPExcel->getActiveSheet()->mergeCells('E10:F10');
	$objPHPExcel->getActiveSheet()->setCellValue('E10', 'BILLETES DE 10');

	$objPHPExcel->getActiveSheet()->setCellValue('B11', '5');	
	$objPHPExcel->getActiveSheet()->setCellValue('C11', $total_b5*5);	
	$objPHPExcel->getActiveSheet()->setCellValue('D11', $total_b5);	
	$objPHPExcel->getActiveSheet()->mergeCells('E11:F11');
	$objPHPExcel->getActiveSheet()->setCellValue('E11', 'BILLETES DE 5');

	$objPHPExcel->getActiveSheet()->setCellValue('B12', '1');	
	$objPHPExcel->getActiveSheet()->setCellValue('C12', $total_b1*1);	
	$objPHPExcel->getActiveSheet()->setCellValue('D12', $total_b1);	
	$objPHPExcel->getActiveSheet()->mergeCells('E12:F12');
	$objPHPExcel->getActiveSheet()->setCellValue('E12', 'BILLETES DE 1');

	$objPHPExcel->getActiveSheet()->setCellValue('B13', '0.50');	
	$objPHPExcel->getActiveSheet()->setCellValue('C13', $total_m50*0.5);	
	$objPHPExcel->getActiveSheet()->setCellValue('D13', $total_m50);	
	$objPHPExcel->getActiveSheet()->mergeCells('E13:F13');
	$objPHPExcel->getActiveSheet()->setCellValue('E13', 'MONEDAS DE 50c');

	$objPHPExcel->getActiveSheet()->setCellValue('B14', '0.25');	
	$objPHPExcel->getActiveSheet()->setCellValue('C14', $total_m25*0.25);	
	$objPHPExcel->getActiveSheet()->setCellValue('D14', $total_m25);	
	$objPHPExcel->getActiveSheet()->mergeCells('E14:F14');
	$objPHPExcel->getActiveSheet()->setCellValue('E14', 'MONEDAS DE 25c');

	$objPHPExcel->getActiveSheet()->setCellValue('B15', '0.10');	
	$objPHPExcel->getActiveSheet()->setCellValue('C15', $total_m10*0.10);	
	$objPHPExcel->getActiveSheet()->setCellValue('D15', $total_m10);	
	$objPHPExcel->getActiveSheet()->mergeCells('E15:F15');
	$objPHPExcel->getActiveSheet()->setCellValue('E15', 'MONEDAS DE 10c');

	$objPHPExcel->getActiveSheet()->setCellValue('B16', '0.05');	
	$objPHPExcel->getActiveSheet()->setCellValue('C16', $total_m5*0.05);	
	$objPHPExcel->getActiveSheet()->setCellValue('D16', $total_m5);	
	$objPHPExcel->getActiveSheet()->mergeCells('E16:F16');
	$objPHPExcel->getActiveSheet()->setCellValue('E16', 'MONEDAS DE 5c');

	$objPHPExcel->getActiveSheet()->setCellValue('B17', '0.01');	
	$objPHPExcel->getActiveSheet()->setCellValue('C17', $total_m1*0.01);	
	$objPHPExcel->getActiveSheet()->setCellValue('D17', $total_m1);	
	$objPHPExcel->getActiveSheet()->mergeCells('E17:F17');
	$objPHPExcel->getActiveSheet()->setCellValue('E17', 'MONEDAS DE 1c');
	$objPHPExcel->getActiveSheet()->setCellValue('B18', 'TOTAL');

	$objPHPExcel->getActiveSheet()->setCellValue('C18','=SUM(C9:C17)');
	cellColor('B18:F18', '0f243e');
	$styleArray = array(
			'font'  => array(
			'bold'  => true,
			'color' => array('rgb' => 'FFFFFF'),
			'size'  => 12,
			'name'  => 'Calibri'
	    ));
	$objPHPExcel->getActiveSheet()->getStyle('B18:F18')->applyFromArray($styleArray);

	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);

	//===========================================================================
	//===========================================================================
	//===========================================================================
	//===========================================================================
	$objPHPExcel->createSheet();
	$objPHPExcel->setActiveSheetIndex(2); 
	$sql             = "SELECT e.nom_emp as empresa, e.rif, e.tel_emp as telefono, e.dir_emp as direccion, e.ciu_emp as ciudad, 
	e.edo_emp, e.imagen_izq as logo
	FROM   nomempresa e";
	$res             =$db->query($sql);
	$fila            =$res->fetch_assoc();
	$logo            =$fila['logo'];
	
	$sql2            = "SELECT np.periodo_ini as desde, np.periodo_fin as hasta, np.mes, np.anio,
	DATE_FORMAT(np.periodo_ini,'%d') as dia_ini, DATE_FORMAT(np.periodo_fin, '%d') as dia_fin   
	FROM nom_nominas_pago np 
	WHERE  np.codnom =".$codnom." AND np.tipnom=".$codtip;
	$res2            =$db->query($sql2);
	$fila2           =$res2->fetch_assoc();
	
	$meses           = array('Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre');
	
	$desde           =$fila2['desde'];
	$hasta           =$fila2['hasta'];
	$dia_ini         = $fila2['dia_ini'];
	$dia_fin         = $fila2['dia_fin']; 
	$mes_numero      = $fila2['mes'];
	$mes_letras      = $meses[$mes_numero - 1];
	$anio            = $fila2['anio'];
	
	
	$empresa         = $fila['empresa'];
	$objPHPExcel->getActiveSheet()
	            ->setCellValue('A2', strtoupper($empresa) )
	            ->setCellValue('A3', 'DESGLOSE DE MONEDAS')
	            ->setCellValue('A4', 'DEL '. $dia_ini .' AL '. $dia_fin .' de '. $mes_letras .' '. $anio );

	//$objPHPExcel->getActiveSheet()->getStyle('A2:A4')->getFont()->setName('Calibri');
	$objPHPExcel->getActiveSheet()->getStyle('A2:A4')->getFont()->setSize(14);
	$objPHPExcel->getActiveSheet()->getStyle('A2:A4')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('A2:A4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->mergeCells('A2:O2');
	$objPHPExcel->getActiveSheet()->mergeCells('A3:O3');
	$objPHPExcel->getActiveSheet()->mergeCells('A4:O4');


	//$objPHPExcel->getActiveSheet()->getStyle('C2:D5')->applyFromArray(allBordersThin());
	//$objPHPExcel->getActiveSheet()->getStyle('C2:D5')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);


	$styleArray = array(
			'font'  => array(
			'bold'  => false,
			'color' => array('rgb' => 'FFFFFF'),
			'size'  => 12,
			'name'  => 'Calibri'
	    ));
	$objPHPExcel->getActiveSheet()->mergeCells('B7:C7');

	$objPHPExcel->getActiveSheet()
	            ->setCellValue('B7', 'TRABAJADOR')
	            ->setCellValue('D7', 'CODIGO')
	            ->setCellValue('E7', 'SUELDO')
	            ->setCellValue('F7', 'BILLETES')
	            ->setCellValue('J7', 'MONEDAS')
	            ->setCellValue('O7', 'MONTO');
	$objPHPExcel->getActiveSheet()->mergeCells('F7:I7');
	$objPHPExcel->getActiveSheet()->mergeCells('J7:N7');


	$objPHPExcel->getActiveSheet()->getStyle('B7')->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle('D7')->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle('E7')->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle('F7')->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle('J7')->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle('O7')->applyFromArray($styleArray);
	cellColor('B7:O7', '0f243e');
	$objPHPExcel->getActiveSheet()
	            ->setCellValue('B8', 'REGULAR AGENTES')
	            ->setCellValue('F8', '20')
	            ->setCellValue('G8', '10')
	            ->setCellValue('H8', '5')
	            ->setCellValue('I8', '1')
	            ->setCellValue('J8', '50c')
	            ->setCellValue('K8', '25c')
	            ->setCellValue('L8', '10c')
	            ->setCellValue('M8', '5c')
	            ->setCellValue('N8', '1c')
	            ->setCellValue('O8', '');
	$objPHPExcel->getActiveSheet()->mergeCells('B8:C8');

	$objPHPExcel->getActiveSheet()->getStyle('B8')->applyFromArray($styleArray);

	$objPHPExcel->getActiveSheet()->getStyle('C8')->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle('D8')->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle('E8')->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle('F8')->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle('G8')->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle('H8')->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle('I8')->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle('J8')->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle('K8')->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle('L8')->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle('M8')->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle('N8')->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle('O8')->applyFromArray($styleArray);
	cellColor('B8:O8', '0f243e');

	$objPHPExcel->getActiveSheet()->getStyle('A7:O8')->getFont()->setSize(12);
	$objPHPExcel->getActiveSheet()->getStyle('A7:O8')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('A7:O8')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

	$objPHPExcel->getActiveSheet()->getStyle('A7:O8')->applyFromArray(allBordersThin());


	
	$i         =9;
	$fin       =$i;
	$consulta  = "	SELECT nn.codorg, nn.descrip
					FROM   nomnivel1 nn
					WHERE  nn.descrip NOT IN('Eventual', 'O&M')
					ORDER BY nn.codorg";
	$result    =$db->query($consulta);
	$i         =9;
	$total_b20 =$total_b10=$total_b5=$total_b1=$total_m50=$total_m25=$total_m10=$total_m5=$total_m1=$total_salario=0;

	while($filas=$result->fetch_assoc())
	{
		$codorg = $filas['codorg'];
		//echo $filas['codorg'], " ",$filas['descrip'],"<br>";
		$sql2= "SELECT np.ficha, CONCAT_WS(' ', np.nombres, np.apellidos) as nombre,
					   SUBSTRING_INDEX(SUBSTRING_INDEX(np.nombres, ' ', 1), ' ', -1) as primer_nombre,
					   CASE WHEN np.apellidos LIKE 'De %' THEN 
							     SUBSTRING_INDEX(SUBSTRING_INDEX(np.apellidos, ' ', 2), ' ',  2) 
					   ELSE      SUBSTRING_INDEX(SUBSTRING_INDEX(np.apellidos, ' ', 1), ' ', -1) 
					   END as primer_apellido
				FROM   nompersonal np
				WHERE  np.codnivel1=".$codorg." AND np.tipnom=".$codtip."
				AND    np.ficha IN (SELECT DISTINCT n.ficha FROM nom_movimientos_nomina n WHERE n.codnom=".$codnom." AND n.tipnom=np.tipnom)
				ORDER BY np.ficha";

		$res2 =$db->query($sql2);
			$salario_departamento=0;	
		$kxx=1;
		while($row2= $res2->fetch_assoc())
		{	
			$ficha          = $row2['ficha'];
			$sql3           = "	SELECT n.monto as salario 
			FROM nom_movimientos_nomina n 
			WHERE  n.codnom =".$codnom." AND n.ficha=".$ficha." AND n.codcon=148";
			$res3           =$db->query($sql3);
			while($row3 = $res3->fetch_assoc())
			{
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, $kxx++);
				$objPHPExcel->getActiveSheet()->setCellValue('B'.$i, $row2['nombre']);
				$objPHPExcel->getActiveSheet()->setCellValue('D'.$i, $ficha);
				$objPHPExcel->getActiveSheet()->setCellValue('E'.$i, $row3['salario']);
				$total_bonos+=$row3['salario'];
				$salario_departamento+=$row3['salario'];
				// indicamos todas las monedas posibles
				$monedas      =array(20, 10, 5, 1, 0.5, 0.25, 0.10, 0.05, 0.01);
				//Dolares
				
				// creamos un array con la misma cantidad de monedas
				// Este array contendra las monedas a devolver
				$cambio       =array(0,0,0,0,0,0,0,0,0);
				
				// Recorremos todas las monedas
				$importe=$row3['salario'];
				for($ixx      =0; $ixx<count($monedas); $ixx++)
				{
				// Si el importe actual, es superior a la moneda
					if($importe   >=$monedas[$ixx])
					{
					
					// obtenemos cantidad de monedas
						$cambio[$ixx] =floor($importe/$monedas[$ixx]);
						// actualizamos el valor del importe que nos queda por didivir
						$importe      =$importe-($cambio[$ixx]*$monedas[$ixx]);
						switch ($ixx) {
							case 0:
								$total_b20 +=$cambio[$ixx];
								break;
							case 1:
								$total_b10 +=$cambio[$ixx];
								break;
							case 2:
								$total_b5  +=$cambio[$ixx];
								break;
							case 3:
								$total_b1  +=$cambio[$ixx];
								break;
							case 4:
								$total_m50 +=$cambio[$ixx];
								break;
							case 5:
								$total_m25 +=$cambio[$ixx];
								break;
							case 6:
								$total_m10 +=$cambio[$ixx];
								break;
							case 7:
								$total_m5  +=$cambio[$ixx];
								break;
							case 8:
								$total_m1  +=$cambio[$ixx];
								break;
						}
					}
					

				}
				//echo $filas['codorg'], " ", $row2['nombre']," ", $row2['ficha']," ",$row3['salario'];

				$Caracter=F;			
				$lenght=count($cambio);
				for ($jxx=0; $jxx <  count($cambio) ; $jxx++) {
 
						$objPHPExcel->getActiveSheet()->setCellValue($Caracter.$i, $cambio[$jxx]);
										$Caracter++;

				}
				$objPHPExcel->getActiveSheet()->setCellValue($Caracter.$i, $row3['salario']);
				$i++;			

			}
		}
	$objPHPExcel->getActiveSheet()->setCellValue('B'.$i, 'Total de Bonos '. $filas['descrip'] );
	$objPHPExcel->getActiveSheet()->setCellValue('O'.$i, $salario_departamento);
	cellColor('B'.$i.':O'.$i, 'ffff66');
	$objPHPExcel->getActiveSheet()->getStyle('O'.$i)->getFont()->setSize(12);
	$objPHPExcel->getActiveSheet()->getStyle('O'.$i)->getFont()->setBold(true);
	$i++;
	$i++;
	}
	$objPHPExcel->getActiveSheet()->mergeCells('M'.$i.':N'.$i);
	$objPHPExcel->getActiveSheet()->setCellValue('M'.$i, 'TOTAL');
	$objPHPExcel->getActiveSheet()->setCellValue('O'.$i, $total_bonos);
	cellColor('B'.$i.':O'.$i, '0f243e');
	$styleArray = array(
			'font'  => array(
			'bold'  => true,
			'color' => array('rgb' => 'FFFFFF'),
			'size'  => 12,
			'name'  => 'Calibri'
	    ));
	$objPHPExcel->getActiveSheet()->getStyle('M'.$i)->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle('O'.$i)->applyFromArray($styleArray);



	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(3);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(10);
	$objPHPExcel->getActiveSheet()->setTitle('Bonos');

	//===========================================================================
	//===========================================================================
	//===========================================================================
	//===========================================================================

	$objPHPExcel->createSheet();
	$objPHPExcel->setActiveSheetIndex(3); 
	$objPHPExcel->getActiveSheet()->setTitle('Bancos');

	$objPHPExcel->getDefaultStyle()->getFont()->setName('Calibri');
	// $objPHPExcel->getActiveSheet()->getStyle('A2:A4')->getFont()->setName('Calibri');
	$objPHPExcel->getDefaultStyle()->getFont()->setSize(11);
	
	$empresa         = $fila['empresa'];
	$objPHPExcel->getActiveSheet()
	            ->setCellValue('B2', strtoupper($empresa) )
	            ->setCellValue('B3', 'DESGLOSE DE MONEDAS')
	            ->setCellValue('B4', 'DEL '. $dia_ini .' AL '. $dia_fin .' de '. $mes_letras .' '. $anio );
	$objPHPExcel->getActiveSheet()->getStyle('B2:B4')->getFont()->setSize(14);
	$objPHPExcel->getActiveSheet()->getStyle('B2:B4')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('B2:B4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->mergeCells('B2:F2');
	$objPHPExcel->getActiveSheet()->mergeCells('B3:F3');
	$objPHPExcel->getActiveSheet()->mergeCells('B4:F4');
	cellColor('B7:F7', '0f243e');
	$objPHPExcel->getActiveSheet()->mergeCells('B7:F7');
	$styleArray = array(
			'font'  => array(
			'bold'  => false,
			'color' => array('rgb' => 'FFFFFF'),
			'size'  => 12,
			'name'  => 'Calibri'
	    ));
	$objPHPExcel->getActiveSheet()->setCellValue('B7', 'DETALLE EFECTIVO');
	$objPHPExcel->getActiveSheet()->mergeCells('D8:F8');
	$objPHPExcel->getActiveSheet()->getStyle('B7')->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle('B7')->getFont()->setSize(14);
	$objPHPExcel->getActiveSheet()->getStyle('B7')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('B7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->setCellValue('C8', 'MONTO');	
	$objPHPExcel->getActiveSheet()->setCellValue('D8', 'CANTIDAD');	
	$objPHPExcel->getActiveSheet()->getStyle('C8:D8')->applyFromArray($styleArray);

	$objPHPExcel->getActiveSheet()->getStyle('C8:D8')->getFont()->setSize(12);
	$objPHPExcel->getActiveSheet()->getStyle('C8:D8')->getFont()->setBold(true);	
	cellColor('B8:F8', '0f243e');
	$objPHPExcel->getActiveSheet()->mergeCells('E9:F9');

	$objPHPExcel->getActiveSheet()->setCellValue('B9', '20');	
	$objPHPExcel->getActiveSheet()->setCellValue('C9', $total_b20*20);	
	$objPHPExcel->getActiveSheet()->setCellValue('D9', $total_b20);	
	$objPHPExcel->getActiveSheet()->mergeCells('E9:F9');
	$objPHPExcel->getActiveSheet()->setCellValue('E9', 'BILLETES DE 20');

	$objPHPExcel->getActiveSheet()->setCellValue('B10', '10');	
	$objPHPExcel->getActiveSheet()->setCellValue('C10', $total_b10*10);	
	$objPHPExcel->getActiveSheet()->setCellValue('D10', $total_b10);	
	$objPHPExcel->getActiveSheet()->mergeCells('E10:F10');
	$objPHPExcel->getActiveSheet()->setCellValue('E10', 'BILLETES DE 10');

	$objPHPExcel->getActiveSheet()->setCellValue('B11', '5');	
	$objPHPExcel->getActiveSheet()->setCellValue('C11', $total_b5*5);	
	$objPHPExcel->getActiveSheet()->setCellValue('D11', $total_b5);	
	$objPHPExcel->getActiveSheet()->mergeCells('E11:F11');
	$objPHPExcel->getActiveSheet()->setCellValue('E11', 'BILLETES DE 5');

	$objPHPExcel->getActiveSheet()->setCellValue('B12', '1');	
	$objPHPExcel->getActiveSheet()->setCellValue('C12', $total_b1*1);	
	$objPHPExcel->getActiveSheet()->setCellValue('D12', $total_b1);	
	$objPHPExcel->getActiveSheet()->mergeCells('E12:F12');
	$objPHPExcel->getActiveSheet()->setCellValue('E12', 'BILLETES DE 1');

	$objPHPExcel->getActiveSheet()->setCellValue('B13', '0.50');	
	$objPHPExcel->getActiveSheet()->setCellValue('C13', $total_m50*0.5);	
	$objPHPExcel->getActiveSheet()->setCellValue('D13', $total_m50);	
	$objPHPExcel->getActiveSheet()->mergeCells('E13:F13');
	$objPHPExcel->getActiveSheet()->setCellValue('E13', 'MONEDAS DE 50c');

	$objPHPExcel->getActiveSheet()->setCellValue('B14', '0.25');	
	$objPHPExcel->getActiveSheet()->setCellValue('C14', $total_m25*0.25);	
	$objPHPExcel->getActiveSheet()->setCellValue('D14', $total_m25);	
	$objPHPExcel->getActiveSheet()->mergeCells('E14:F14');
	$objPHPExcel->getActiveSheet()->setCellValue('E14', 'MONEDAS DE 25c');

	$objPHPExcel->getActiveSheet()->setCellValue('B15', '0.10');	
	$objPHPExcel->getActiveSheet()->setCellValue('C15', $total_m10*0.10);	
	$objPHPExcel->getActiveSheet()->setCellValue('D15', $total_m10);	
	$objPHPExcel->getActiveSheet()->mergeCells('E15:F15');
	$objPHPExcel->getActiveSheet()->setCellValue('E15', 'MONEDAS DE 10c');

	$objPHPExcel->getActiveSheet()->setCellValue('B16', '0.05');	
	$objPHPExcel->getActiveSheet()->setCellValue('C16', $total_m5*0.05);	
	$objPHPExcel->getActiveSheet()->setCellValue('D16', $total_m5);	
	$objPHPExcel->getActiveSheet()->mergeCells('E16:F16');
	$objPHPExcel->getActiveSheet()->setCellValue('E16', 'MONEDAS DE 5c');

	$objPHPExcel->getActiveSheet()->setCellValue('B17', '0.01');	
	$objPHPExcel->getActiveSheet()->setCellValue('C17', $total_m1*0.01);	
	$objPHPExcel->getActiveSheet()->setCellValue('D17', $total_m1);	
	$objPHPExcel->getActiveSheet()->mergeCells('E17:F17');
	$objPHPExcel->getActiveSheet()->setCellValue('E17', 'MONEDAS DE 1c');
	$objPHPExcel->getActiveSheet()->setCellValue('B18', 'TOTAL');

	$objPHPExcel->getActiveSheet()->setCellValue('C18','=SUM(C9:C17)');
	cellColor('B18:F18', '0f243e');
	$styleArray = array(
			'font'  => array(
			'bold'  => true,
			'color' => array('rgb' => 'FFFFFF'),
			'size'  => 12,
			'name'  => 'Calibri'
	    ));
	$objPHPExcel->getActiveSheet()->getStyle('B18:F18')->applyFromArray($styleArray);

	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
	$objPHPExcel->getActiveSheet()->setTitle('Desglose Bonos');

	//===========================================================================
	//===========================================================================
	//===========================================================================
	//===========================================================================





$objPHPExcel->setActiveSheetIndex(0); 
$NOMINA = str_replace(' ', '', $NOMINA);
$filename = "Desglose_Billetes_".$NOMINA."_Del_".fecha($desde).'_Hasta_'.fecha($hasta);

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
