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

	$objPHPExcel->getProperties()->setCreator("Selectra")
								->setLastModifiedBy("Selectra")
								->setTitle("Horizontal de Planilla");

	$objPHPExcel->getDefaultStyle()->getFont()->setName('Calibri');
	// $objPHPExcel->getActiveSheet()->getStyle('A2:A4')->getFont()->setName('Calibri');
	$objPHPExcel->getDefaultStyle()->getFont()->setSize(11);


	$sql             = "SELECT e.nom_emp as empresa, e.rif, e.tel_emp as telefono, e.dir_emp as direccion, e.ciu_emp as ciudad, 
	e.edo_emp, e.imagen_izq as logo
	FROM   nomempresa e";
		$conexion= new bd($_SESSION['bd']);

	$res             =$conexion->query($sql);
	$fila            =$res->fetch_array();
	$logo            =$fila['logo'];
		$conexion= new bd($_SESSION['bd']);

	$sql2            = "SELECT np.periodo_ini as desde, np.periodo_fin as hasta, np.mes, DATE_FORMAT(np.periodo_fin,'%Y') as anio, DATE_FORMAT(np.periodo_ini,'%Y') as anio_decimo,
	DATE_FORMAT(np.periodo_ini,'%d') as dia_ini,
	DATE_FORMAT(np.periodo_ini,'%m') as mes_ini, 
	DATE_FORMAT(np.periodo_fin,'%d') as dia_fin,
	DATE_FORMAT(np.periodo_ini,'%m') as mes_fin, np.descrip
	FROM nom_nominas_pago np 
	WHERE  np.codnom =".$codnom." AND np.tipnom=".$codtip;
	$res2            =$conexion->query($sql2);
	$fila2           =$res2->fetch_array();

	$meses           = array('Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre');

	$desde          =$fila2['desde'];
	$hasta          =$fila2['hasta'];
	$dia_ini        = $fila2['dia_ini'];
	$mes_ini        = $fila2['mes_ini'];
	$dia_fin        = $fila2['dia_fin'];
	$mes_fin        = $fila2['mes_fin'];  
	$mes_numero_ini = $fila2['mes'];
	$mes_numero_fin = $fila2['mes'];
	$mes_letras     = $meses[$mes_numero_ini - 1];
	$mes_letras2    = $meses[$mes_numero_fin - 1];
	$anio           = $fila2['anio'];
	$anio_decimo    = $fila2['anio_decimo'];
	$descrip        = $fila2['descrip'];
	$temp_descrip   = explode('-', $descrip);
	$descrip        = strtoupper($temp_descrip[0]." - ".$temp_descrip[1]);

	$empresa        = $fila['empresa'];
	$objPHPExcel->getActiveSheet()
				->setCellValue('A2', strtoupper($empresa) )
				->setCellValue('A3', $descrip." - ".$fila2['hasta']);
			// ->setCellValue('A4', 'DEL '. $dia_ini .' de '. $mes_letras .' AL '. $dia_fin .' de '. $mes_letras2 .' '. $anio );
	$fuentes = array(
		'font'  => array(
			'bold'  => true,
			'color' => array('rgb' => '000'),
			'size'  => 11,
			'name'  => 'Verdana'
		)
	);
	$fuentes2 = array(
		'font'  => array(
			'bold'  => true,
			'color' => array('rgb' => '000'),
			'size'  => 9,
			'name'  => 'Verdana'
		)
	);
	//$objPHPExcel->getActiveSheet()->getStyle('A2:A4')->getFont()->setName('Calibri');
	$objPHPExcel->getActiveSheet()->getStyle('A2:J4')->getFont()->setSize(14);
	$objPHPExcel->getActiveSheet()->getStyle('A2:J4')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('A2:J4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->mergeCells('A2:J2');
	$objPHPExcel->getActiveSheet()->mergeCells('A3:J3');
	$objPHPExcel->getActiveSheet()->mergeCells('A4:J4');

	//$objPHPExcel->getActiveSheet()->getStyle('C2:D5')->applyFromArray(allBordersThin());
	//$objPHPExcel->getActiveSheet()->getStyle('C2:D5')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	$objPHPExcel->getActiveSheet()
				->setCellValue('A6', 'LIQUIDACIONES')
				->setCellValue('A7', 'No.')
				->setCellValue('B7', 'NOMBRE')
				->setCellValue('C7', 'SALARIO')           
				->setCellValue('D7', 'VACACIONES PROP.')           
				->setCellValue('E7', 'XIII PROP.')
				->setCellValue('F7', 'PRIMA ANTIG.')
				->setCellValue('G7', 'INDEMNIZACIÓN')
				->setCellValue('H7', 'FONDO CESAN 6%')
				->setCellValue('I7', 'FONDO ENF')
				->setCellValue('J7', 'PREAVISO')
				->setCellValue('K7', 'I.S.R.')
				->setCellValue('L7', 'SEGURO SOCIAL')
				->setCellValue('M7', 'SEGURO EDUCATIVO')
				->setCellValue('N7', 'OTROS DESC.')
				->setCellValue('O7', 'TOTAL');

	cellColor('C7:J7', 'ffcc00');
	cellColor('K7:O7', '92d050');

	//$objPHPExcel->getActiveSheet()->getStyle('A6:Q8')->getFont()->setName('Calibri');
	$objPHPExcel->getActiveSheet()->getStyle('A6:O7')->getFont()->setSize(12);
	$objPHPExcel->getActiveSheet()->getStyle('A6:O7')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('A6:O7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

	$objPHPExcel->getActiveSheet()->mergeCells('A6:O6');
	$objPHPExcel->getActiveSheet()->getStyle('A6:O7')->applyFromArray(allBordersThin());

	$objPHPExcel->getActiveSheet()->freezePane('C8'); // Inmovilizar Paneles

	$sql = "SELECT nn.codorg, nn.descrip,
				CASE WHEN nn.codorg=1 THEN 'Administrativos'
						WHEN nn.codorg=2 THEN 'Técnicos'
						WHEN nn.codorg=3 THEN 'Desarrollo Sostenible'
						WHEN nn.codorg=4 THEN 'Comercialización' 
				END as grupo
			FROM   nomnivel1 nn
			WHERE  nn.descrip NOT IN('Eventual', 'O&M')
			ORDER BY nn.codorg";
	$conexion= new bd($_SESSION['bd']);

	$res   =$conexion->query($sql);
	$sheet =0; // Contador de hojas de cálculo del libro

	$i     =8; 
	$nivel =1;
	// $monto_salarios=0;
	$total_salarios=$total_comision=$total_prima=$total_preaviso=$total_indemnizacion="=";
	$total_extra_salario=$total_extra_gastosrep=$total_vacaciones=$total_cxc="=";
	$total_segurosocial_salario=$total_tardanza=$total_seguro_educativo="=";
	$total_isr_gastos=$total_isr_salario=$total_xii=$total_descuentos=$total_neto="=";
	$total_salario=$MesXIII=$Total_Desc_Ley=$SeguroSocialMesXIII=$total_vac_gr=$total_cesantia=$total_enf="=";
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
				WHERE  np.codnivel1={$codorg} AND np.tipnom={$codtip}
				AND    np.ficha IN (SELECT DISTINCT n.ficha FROM nom_movimientos_nomina n WHERE n.codnom={$codnom} AND n.tipnom=np.tipnom)
				ORDER BY np.ficha";

		$res2=$conexion->query($sql2);

		$ini=$i; $enc=false;
		while($row2=$res2->fetch_array())
		{	$enc=true;
			$ficha = $row2['ficha'];
			$primer_nombre = utf8_encode($row2['primer_nombre']);
			$apellido   = utf8_encode($row2['primer_apellido']);
			$trabajador = utf8_encode($row2['primer_nombre']).' '.utf8_encode($row2['primer_apellido']); // $row2['nombre']

			//$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':Q'.$i)->getFont()->setName('Calibri');
			$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':O'.$i)->getFont()->setSize(12);

			$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, $row2['ficha']);
			$objPHPExcel->getActiveSheet()->setCellValue('B'.$i, $trabajador);
			$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':O'.$i)->applyFromArray(allBordersThin());

			$sql3 = "SELECT
					COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
					WHERE  n.codnom='{$codnom}' AND n.tipnom='{$codtip}' AND n.ficha='{$ficha}' AND n.codcon=90),0) as salario,
					COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
					WHERE  n.codnom='{$codnom}' AND n.tipnom='{$codtip}' AND n.ficha='{$ficha}' AND n.codcon in (91,117,157)), 0) as vac_prop,
					COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
					WHERE  n.codnom='{$codnom}' AND n.tipnom='{$codtip}' AND n.ficha='{$ficha}' AND n.codcon in (199,92)), 0) as decimo_prop,			 
					COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
					WHERE  n.codnom='{$codnom}' AND n.tipnom='{$codtip}' AND n.ficha='{$ficha}' AND n.codcon in (200)), 0) as seguro_social,			 
					COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
					WHERE  n.codnom='{$codnom}' AND n.tipnom='{$codtip}' AND n.ficha='{$ficha}' AND n.codcon in (205)), 0) as seguro_social_liq,
					COALESCE((SELECT  SUM(n.monto) FROM nom_movimientos_nomina n 
					WHERE  n.codnom='{$codnom}' AND n.tipnom='{$codtip}' AND n.ficha='{$ficha}' AND n.codcon in (201,206)), 0) as seguro_educativo,
					COALESCE((SELECT  SUM(n.monto) FROM nom_movimientos_nomina n 
					WHERE  n.codnom='{$codnom}' AND n.tipnom='{$codtip}' AND n.ficha='{$ficha}' AND n.codcon=208), 0) as seguro_social_vac,
					COALESCE((SELECT  SUM(n.monto) FROM nom_movimientos_nomina n 
					WHERE  n.codnom='{$codnom}' AND n.tipnom='{$codtip}' AND n.ficha='{$ficha}' AND n.codcon=213), 0) as islr_vac,
					COALESCE((SELECT  SUM(n.monto) FROM nom_movimientos_nomina n 
					WHERE  n.codnom='{$codnom}' AND n.tipnom='{$codtip}' AND n.ficha='{$ficha}' AND n.codcon=93), 0) as prima,
					COALESCE((SELECT  SUM(n.monto) FROM nom_movimientos_nomina n 
					WHERE  n.codnom='{$codnom}' AND n.tipnom='{$codtip}' AND n.ficha='{$ficha}' AND n.codcon in (94)), 0) as indem,
					COALESCE((SELECT  SUM(n.monto) FROM nom_movimientos_nomina n 
					WHERE  n.codnom='{$codnom}' AND n.tipnom='{$codtip}' AND n.ficha='{$ficha}' AND n.codcon in (95)), 0) as preaviso,
					COALESCE((SELECT  SUM(n.monto) FROM nom_movimientos_nomina n 
					WHERE  n.codnom='{$codnom}' AND n.tipnom='{$codtip}' AND n.ficha='{$ficha}' AND n.codcon in (96)), 0) as cesantia,
					COALESCE((SELECT  SUM(n.monto) FROM nom_movimientos_nomina n 
					WHERE  n.codnom='{$codnom}' AND n.tipnom='{$codtip}' AND n.ficha='{$ficha}' AND n.codcon in (97)), 0) as enf,
					COALESCE((SELECT  SUM(n.monto) FROM nom_movimientos_nomina n 
					WHERE  n.codnom='{$codnom}' AND n.tipnom='{$codtip}' AND n.ficha='{$ficha}'
					AND    n.codcon IN (202, 601, 605)), 0) as isr,
					COALESCE((SELECT  SUM(n.monto) FROM nom_movimientos_nomina n 
					WHERE  n.codnom='{$codnom}' AND n.tipnom='{$codtip}' AND n.ficha='{$ficha}'
					AND    n.codcon IN (207, 208, 606, 607)), 0) as isr_gastos,
					(SELECT  COALESCE(SUM(n.monto), 0) FROM nom_movimientos_nomina n 
					WHERE  n.codnom='{$codnom}' AND n.tipnom='{$codtip}' AND n.ficha='{$ficha}' AND (n.codcon BETWEEN 500 AND 507 
					OR n.codcon BETWEEN 508 AND 599))  as descuentos,
					(SELECT  COALESCE(SUM(n.monto), 0) FROM nom_movimientos_nomina n 
					WHERE  n.codnom='{$codnom}' AND n.tipnom='{$codtip}' AND n.ficha='{$ficha}' AND n.codcon=508)  as cxc,
					(SELECT  COALESCE(SUM(n.monto), 0) FROM nom_movimientos_nomina n 
					WHERE  n.codnom='{$codnom}' AND n.tipnom='{$codtip}' AND n.ficha='{$ficha}' AND n.codcon=114)  as vacaciones";
			$conexion= new bd($_SESSION['bd']);

			$res3 = $conexion->query($sql3);
			$neto=0;
			if($row3=$res3->fetch_array())
			{
				$MONTO_EXTRA_SALARIO = $MONTO_DOMINGO_SALARIO = 0;
				$MONTO_GR_EXTRA_SALARIO = $MONTO_GR_DOMINGO_SALARIO = 0;
				$AJUSTE = 0;

				$sql5 = "SELECT COALESCE(SUM(n.monto), 0) AS total_extra_salario  
						FROM   nom_movimientos_nomina n 
						WHERE  n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha."
						AND (  n.codcon IN (106, 108, 142, 143, 183, 184)
						OR    n.codcon IN (111,116,123, 124, 125, 126, 144, 149, 150, 151)
						OR    n.codcon IN (107, 109, 152, 153) )";
				$conexion= new bd($_SESSION['bd']);

				$res5 =$conexion->query($sql5);
				if($row5=$res5->fetch_array()){ $MONTO_EXTRA_SALARIO = $row5['total_extra_salario']; }

				$sql5 = "SELECT COALESCE(SUM(n.monto), 0) AS total_domingo_salario  
						FROM   nom_movimientos_nomina n 
						WHERE  n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha."
						AND (  n.codcon IN (112, 113, 127, 128, 129, 130, 131, 132)
						OR    n.codcon IN (133, 134, 135, 136, 138, 139) )";
				$conexion= new bd($_SESSION['bd']);
				$res5 =$conexion->query($sql5);


				// Calcular AJUSTE
				$sql5= "SELECT COALESCE(SUM(monto), 0) AS ajuste
						FROM   nom_movimientos_nomina n 
						WHERE  n.codcon=604
						AND    n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha;

				$conexion= new bd($_SESSION['bd']);
				$res5 = $conexion->query($sql5);
				$seguro_social = $row3['seguro_social'] + $row3['seguro_social_liq'];
				if($row5 = $res5->fetch_array() ){ $AJUSTE = $row5['ajuste']; }

				//$impuesto = ($row3['isr_gastos'] >0) ? $row3['isr'] - $row3['isr_gastos'] : $row3['isr'];

				$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':J'.$i)->getAlignment()
											->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$objPHPExcel->getActiveSheet()->setCellValue('C'.$i, $row3['salario']);
				$objPHPExcel->getActiveSheet()->setCellValue('D'.$i, $row3['vac_prop']);
				$objPHPExcel->getActiveSheet()->setCellValue('E'.$i, $row3['decimo_prop']);
				$objPHPExcel->getActiveSheet()->setCellValue('F'.$i, $row3['prima']);
				$objPHPExcel->getActiveSheet()->setCellValue('G'.$i, $row3['indem']);
				$objPHPExcel->getActiveSheet()->setCellValue('H'.$i, $row3['cesantia']);
				$objPHPExcel->getActiveSheet()->setCellValue('I'.$i, $row3['enf']);
				$objPHPExcel->getActiveSheet()->setCellValue('J'.$i, $row3['preaviso']);
				$objPHPExcel->getActiveSheet()->setCellValue('K'.$i, $row3['isr']);
				$objPHPExcel->getActiveSheet()->setCellValue('L'.$i, $seguro_social);
				$objPHPExcel->getActiveSheet()->setCellValue('M'.$i, $row3['seguro_educativo']);
				$objPHPExcel->getActiveSheet()->setCellValue('N'.$i, $row3['descuentos']);
				
				$neto2 = $row3['salario'] - $row3['seguro_social'];

				//$objPHPExcel->getActiveSheet()->setCellValue('M'.$i, $neto);
				$objPHPExcel->getActiveSheet()->setCellValue('O'.$i, "=C$i+D$i+E$i+F$i+G$i+H$i+I$i+J$i-K$i-L$i-M$i-N$i");
				// =+C38+F38+G38+H38+I38+J38-K38-L38-M38-N38-O38-P38
				$neto = "='".$dia_fin." ".$mes_letras."'!O".$i; //$objPHPExcel->getActiveSheet()->getCell('Q'.$i)->getValue();

				$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':O'.$i)->getNumberFormat()
							->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_ACCOUNTING);
			}

			$trabajadores[] = array('nombre'=>$trabajador, 'primer_nombre'=> $primer_nombre, 'apellido'=>$apellido, 'neto'=> $neto, 'neto2'=>$neto2);
			$i++;
		}

		if($enc)
		{
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, 'Total de Vacaciones '. $row['descrip'] );
			$objPHPExcel->getActiveSheet()->setCellValue('C'.$i, "=SUM(C".$ini.":C".($i-1).")");	
			$total_salario                .= "+C".$i;
			$objPHPExcel->getActiveSheet()->setCellValue('D'.$i, "=SUM(D".$ini.":D".($i-1).")");	
			$total_vacaciones             .= "+D".$i;
			$objPHPExcel->getActiveSheet()->setCellValue('E'.$i, "=SUM(E".$ini.":E".($i-1).")");	
			$total_xii                    .= "+E".$i;
			$objPHPExcel->getActiveSheet()->setCellValue('F'.$i, "=SUM(F".$ini.":F".($i-1).")");	
			$total_prima                  .= "+F".$i;
			$objPHPExcel->getActiveSheet()->setCellValue('G'.$i, "=SUM(G".$ini.":G".($i-1).")");	
			$total_indemnizacion          .= "+G".$i;
			$objPHPExcel->getActiveSheet()->setCellValue('H'.$i, "=SUM(H".$ini.":H".($i-1).")");	
			$total_cesantia    		      .= "+H".$i;
			$objPHPExcel->getActiveSheet()->setCellValue('I'.$i, "=SUM(I".$ini.":I".($i-1).")");	
			$total_enf			          .= "+I".$i;
			$objPHPExcel->getActiveSheet()->setCellValue('J'.$i, "=SUM(J".$ini.":J".($i-1).")");	
			$total_preaviso               .= "+J".$i;
			$objPHPExcel->getActiveSheet()->setCellValue('K'.$i, "=SUM(K".$ini.":K".($i-1).")");	
			$total_isr_salario   		  .= "+K".$i;
			$objPHPExcel->getActiveSheet()->setCellValue('L'.$i, "=SUM(L".$ini.":L".($i-1).")");	
			$total_segurosocial_salario   .= "+L".$i;
			$objPHPExcel->getActiveSheet()->setCellValue('M'.$i, "=SUM(M".$ini.":M".($i-1).")");	
			$total_seguro_educativo		  .= "+M".$i;
			$objPHPExcel->getActiveSheet()->setCellValue('N'.$i, "=SUM(N".$ini.":N".($i-1).")");	
			$total_descuentos   		  .= "+N".$i;
			$objPHPExcel->getActiveSheet()->setCellValue('O'.$i, "=SUM(O".$ini.":O".($i-1).")");	
			$total_neto                   .= "+O".$i;

			//$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':Q'.$i)->getFont()->setName('Calibri');
			$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':O'.$i)->getFont()->setSize(12);
			$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':O'.$i)->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':O'.$i)->getNumberFormat()
										->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_ACCOUNTING);
			//$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getFont()->setName('Calibri');
			$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getFont()->setSize(8);
			$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getFont()->setBold(true);
			//$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':B'.$i);
			$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':B'.$i)->getBorders()->getBottom()
						->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
			if($nivel==1)
			{
				$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':O'.$i)->applyFromArray(allBordersMedium());
			}
			else
			{
				$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':O'.$i)->applyFromArray(allBordersThin());
			}
			//$i++;
			//$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':O'.$i)->applyFromArray(allBordersMedium());	
			//$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':O'.$i);
			$i++;	
			$nivel++;
		}
	}

	//$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':Q'.$i);
	$i++;
	$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':O'.$i)->applyFromArray(allBordersMedium());	
	//$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':Q'.$i)->getFont()->setName('Calibri');
	$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':O'.$i)->getFont()->setSize(12);
	$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':O'.$i)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()
				->setCellValue('B'.$i, 'Total')
				->setCellValue('C'.$i, $total_salario)
				->setCellValue('D'.$i, $total_vacaciones)
				->setCellValue('E'.$i, $total_xii)
				->setCellValue('F'.$i, $total_prima)
				->setCellValue('G'.$i, $total_indemnizacion)
				->setCellValue('H'.$i, $total_cesantia)
				->setCellValue('I'.$i, $total_enf)
				->setCellValue('J'.$i, $total_preaviso)
				->setCellValue('K'.$i, $total_isr_salario)
				->setCellValue('L'.$i, $total_segurosocial_salario)
				->setCellValue('M'.$i, $total_seguro_educativo)
				->setCellValue('N'.$i, $total_descuentos)
				->setCellValue('O'.$i, $total_neto);
	$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':O'.$i)->getNumberFormat()
				->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_ACCOUNTING);
			$sheet++;



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



	$objPHPExcel->getActiveSheet()->setTitle($dia_fin.' '.$mes_letras);
	/*Formato de liquidación individual*/
	$sql = "SELECT nn.codorg, nn.descrip,
				CASE WHEN nn.codorg=1 THEN 'Administrativos'
						WHEN nn.codorg=2 THEN 'Técnicos'
						WHEN nn.codorg=3 THEN 'Desarrollo Sostenible'
						WHEN nn.codorg=4 THEN 'Comercialización' 
				END as grupo
			FROM   nomnivel1 nn
			WHERE  nn.descrip NOT IN('Eventual', 'O&M')
			ORDER BY nn.codorg";
	$conexion= new bd($_SESSION['bd']);

	$res   =$conexion->query($sql);

	while($row=$res->fetch_array())
	{
		$codorg   = $row['codorg'];
		
		$amaxonia = new bd($_SESSION['bd']);
		$sql2     = "SELECT np.ficha, CONCAT_WS(' ', np.nombres, np.apellidos) as nombre,
					SUBSTRING_INDEX(SUBSTRING_INDEX(np.nombres, ' ', 1), ' ', -1) as primer_nombre,
					CASE WHEN np.apellidos LIKE 'De %' THEN 
								SUBSTRING_INDEX(SUBSTRING_INDEX(np.apellidos, ' ', 2), ' ',  2) 
					ELSE      SUBSTRING_INDEX(SUBSTRING_INDEX(np.apellidos, ' ', 1), ' ', -1) 
					END as primer_apellido, np.suesal, np.fecing, np.fecharetiro
					FROM   nompersonal np
					WHERE  np.codnivel1=".$codorg." AND np.tipnom=".$codtip."
					AND    np.ficha IN (SELECT DISTINCT n.ficha FROM nom_movimientos_nomina n WHERE n.codnom=".$codnom." AND n.tipnom=np.tipnom)
					ORDER BY np.ficha";
		$res2     = $amaxonia->query($sql2);

		while ($row2=$res2->fetch_array()) 
		{
			$nombre = $row2['nombre'];
			$ficha = $row2['ficha'];
			$objPHPExcel->createSheet();
			$objPHPExcel->setActiveSheetIndex($sheet);
			$objPHPExcel->getActiveSheet()->setTitle( substr( utf8_encode($nombre ),0,31));
			$objPHPExcel->getActiveSheet()->setCellValue('A2', strtoupper($empresa) );
			$objPHPExcel->getActiveSheet()->getStyle('A2')->applyFromArray($fuentes);
			$objPHPExcel->getActiveSheet()->mergeCells('A2:F2');

			$ixx = 3;

			$objPHPExcel->getActiveSheet()->setCellValue('A'.$ixx, 'Nombre del Colaborador:'.utf8_encode($nombre ));
			$objPHPExcel->getActiveSheet()->setCellValue('D'.$ixx, 'Salario Mensual:'.$row2['suesal'] );
			$ixx++;
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$ixx, 'Fecha Inicio:'.$row2['fecing'] );
			$ixx++;
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$ixx, 'Fecha Salida:'.$row2['fecharetiro'] );
			$objPHPExcel->getActiveSheet()->setCellValue('D'.$ixx, 'Tramitado:'.date('d/m/Y') );
			$ixx+=2;


			/*/////////////////////////////////////////////*/
			/*                                             */
			/*  Cálculo de de Vacaciones Proporcional      */
			/*                                             */
			/*/////////////////////////////////////////////*/

			$objPHPExcel->getActiveSheet()->setCellValue('A'.$ixx, 'VACACIONES PROPORCIONALES');
			$objPHPExcel->getActiveSheet()->getStyle('A'.$ixx)->applyFromArray($fuentes);
			$ivv = $ixx;

			$ixx+=2;

			$objPHPExcel->getActiveSheet()->setCellValue('A'.$ixx, 'MESES');
			$objPHPExcel->getActiveSheet()->getStyle('A'.$ixx)->applyFromArray($fuentes);

			$im = $ixx += 2;
			$iy = $ixx -= 1;
			$ixx++;
			for ($ilm=0; $ilm < 12; $ilm++) 
			{
				$mes_letras = $meses[$ilm]; 
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$ixx, $mes_letras);
				$ixx++;

			}
			$fecha_ingreso = new DateTime($row2['fecing']);

			$dia_ing  = $fecha_ingreso->format('d');
			$mes_ing  = $fecha_ingreso->format('n');
			$anio_ing = $fecha_ingreso->format('Y');
			$anio_ant = $anio - 1;
			$anio_ant2 = $anio_ant - 1;
			$anio_ant3 = $anio_ant2 - 1;
			$anio_ant4 = $anio_ant3 - 1;
			$anio_ant5 = $anio_ant4 - 1;
			
			if($anio > $anio_ing)
				$fec_vac = $anio_ant."-".$mes_ing."-".$dia_ing;
			else
				$fec_vac = $anio_ing."-".$mes_ing."-".$dia_ing;

			$objPHPExcel->getActiveSheet()->setCellValue('C'.$iy, $anio_ant4);
			$objPHPExcel->getActiveSheet()->setCellValue('D'.$iy, $anio_ant3);
			$objPHPExcel->getActiveSheet()->setCellValue('E'.$iy, $anio_ant2);
			$objPHPExcel->getActiveSheet()->setCellValue('F'.$iy, $anio_ant);
			$objPHPExcel->getActiveSheet()->setCellValue('G'.$iy, $anio);

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
					'color' => array('rgb' => '000'),
					'size'  => 11,
					'name'  => 'Verdana'
				)
			);
			$objPHPExcel->getActiveSheet()->getStyle('C'.$iy.':G'.$iy)->applyFromArray($borders);

			for ($mes_=1; $mes_ <= 12; $mes_++) 
			{
				$amaxonia = new bd($_SESSION['bd']);
				$sql_anio = "	SELECT  COALESCE(SUM(monto),0) AS monto
								FROM nom_movimientos_nomina 
								WHERE ficha = {$ficha}  AND tipnom ={$codtip} AND anio = {$anio_ant} 
								AND mes = {$mes_} AND codcon in (90,100,102,106,107,108,109,11,112,113,114,115,116,119,120,121,122,123,124,125,125,126,127,128,129,130,131,132,133,134,135,136,136,139,141,142,143,144,149,150,151,152,153,157,158,624,626)
								AND codnom IN 
									(SELECT codnom 
										FROM nom_nominas_pago 
										WHERE anio={$anio_ant} AND mes={$mes_} AND frecuencia
									)";

				$sql_anio_acum = "
				SELECT
					(SUM( IF(YEAR(a.fecha_pago) ='{$anio_ant}' AND MONTH(a.fecha_pago)={$mes_}, salario_bruto,0) ) + SUM( IF(YEAR(a.fecha_pago) ='{$anio_ant}' AND MONTH(a.fecha_pago)={$mes_}, a.bono,0)) ) AS monto4, 
					(SUM( IF(YEAR(a.fecha_pago) ='{$anio}' AND MONTH(a.fecha_pago)={$mes_}, salario_bruto,0) )+SUM( IF(YEAR(a.fecha_pago) ='{$anio}' AND MONTH(a.fecha_pago)={$mes_}, a.bono,0)) ) AS monto5	
				FROM salarios_acumulados a LEFT JOIN nompersonal b ON a.ficha=b.ficha WHERE a.ficha = {$ficha} AND MONTH(a.fecha_pago)={$mes_} AND a.fecha_pago >= '{$fec_vac}'";

				//echo $sql_anio_acum."<br><br>";
				$fila_suel     = $amaxonia->query($sql_anio_acum)->fetch_object();

				$objPHPExcel->getActiveSheet()->setCellValue('C'.($iy+$mes_), 0.00);
				$objPHPExcel->getActiveSheet()->setCellValue('D'.($iy+$mes_), 0.00);
				$objPHPExcel->getActiveSheet()->setCellValue('E'.($iy+$mes_), 0.00);
				$objPHPExcel->getActiveSheet()->setCellValue('F'.($iy+$mes_), ( ($fila_suel->monto4==null) ? 0.00 : $fila_suel->monto4 ) );
				$objPHPExcel->getActiveSheet()->setCellValue('G'.($iy+$mes_), ( ($fila_suel->monto4==null) ? 0.00 : $fila_suel->monto5 ) );
				$objPHPExcel->getActiveSheet()->getStyle('C'.($iy+$mes_).':G'.($iy+$mes_))->getNumberFormat()->setFormatCode('#,##0.00');

			}
			//exit;

			$ixx++;
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$ixx, 'TOTALES');

			$objPHPExcel->getActiveSheet()->getStyle('A'.$ixx)->applyFromArray($fuentes);
			$objPHPExcel->getActiveSheet()->setCellValue('C'.$ixx, '=SUM(C'.($iy+1).':C'.($iy+12).')');
			$objPHPExcel->getActiveSheet()->setCellValue('D'.$ixx, '=SUM(D'.($iy+1).':D'.($iy+12).')');
			$objPHPExcel->getActiveSheet()->setCellValue('E'.$ixx, '=SUM(E'.($iy+1).':E'.($iy+12).')');
			$objPHPExcel->getActiveSheet()->setCellValue('F'.$ixx, '=SUM(F'.($iy+1).':F'.($iy+12).')');
			$objPHPExcel->getActiveSheet()->setCellValue('G'.$ixx, '=SUM(G'.($iy+1).':G'.($iy+12).')');
			$objPHPExcel->getActiveSheet()->getStyle('C'.($ixx).':G'.($ixx))->getNumberFormat()->setFormatCode('#,##0.00');

			//Cálculo de Décimo Proporcional
			
			$objPHPExcel->getActiveSheet()->setCellValue('I'.$ivv, 'DÉCIMO PROPORCIONAL');
			$objPHPExcel->getActiveSheet()->getStyle('I'.$ivv)->applyFromArray($fuentes);
			$ivv+=2;
			$objPHPExcel->getActiveSheet()->setCellValue('I'.$ivv, 'MESES');
			$objPHPExcel->getActiveSheet()->getStyle('I'.$ivv)->applyFromArray($fuentes);
			$ivv+=2;
			for ($ilm=0; $ilm < 12; $ilm++) 
			{
				$mes_letras = $meses[$ilm]; 
				$objPHPExcel->getActiveSheet()->setCellValue('I'.$ivv, $mes_letras);
				$ivv++;

			}

			$objPHPExcel->getActiveSheet()->setCellValue('J'.$iy, $anio_ant);
			$objPHPExcel->getActiveSheet()->setCellValue('K'.$iy, $anio);

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
					'color' => array('rgb' => '000'),
					'size'  => 11,
					'name'  => 'Verdana'
				)
			);
			$objPHPExcel->getActiveSheet()->getStyle('J'.$iy.':K'.$iy)->applyFromArray($borders);

			for ($mes_=1; $mes_ <= 12; $mes_++) 
			{
				$amaxonia = new bd($_SESSION['bd']);
				
				
				/*$sql_anio_acum = "	SELECT  COALESCE(SUM(monto),0) AS monto
							FROM nom_movimientos_nomina 
							WHERE ficha = {$ficha}  AND tipnom ={$codtip} AND anio = {$anio_decimo} 
							AND mes = {$mes_} AND codcon in (100)
							AND codnom IN 
								(SELECT codnom 
									FROM nom_nominas_pago 
									WHERE anio={$anio_decimo} AND mes={$mes_} AND periodo_ini > {$desde}
								)";*/
				
				$sql_anio_acum = "
				SELECT
					SUM( IF(YEAR(fecha_pago) ='{$anio_ant}' AND MONTH(fecha_pago)={$mes_}, salario_bruto,0)  ) AS monto1,
					SUM( IF(YEAR(fecha_pago) ='{$anio}' AND MONTH(fecha_pago)={$mes_}, salario_bruto,0)  ) AS monto2
				FROM salarios_acumulados WHERE ficha = {$ficha} AND fecha_pago > '{$desde}'";
				//echo $sql_anio_acum."<br><br>";
				//ACA SE IMPRIMEN LOS VALORES DEL DECIMOPROPORCIONAL
				$montoDecimo = 0.00;
				//if( $mes_ >= $mes_ini){
					$fila_suel   = $amaxonia->query($sql_anio_acum)->fetch_object();
					$montoDecimo1 = $fila_suel->monto1;
					$montoDecimo2 = $fila_suel->monto2;
				//}
				
				$objPHPExcel->getActiveSheet()->setCellValue('J'.($iy+$mes_), $montoDecimo1 );
				$objPHPExcel->getActiveSheet()->setCellValue('K'.($iy+$mes_), $montoDecimo2 );
				$objPHPExcel->getActiveSheet()->getStyle('J'.($iy+$mes_).':K'.($iy+$mes_))->getNumberFormat()->setFormatCode('#,##0.00');

			}
			//exit;
			$ivv++;
			$objPHPExcel->getActiveSheet()->setCellValue('I'.$ivv, 'TOTALES');

			$objPHPExcel->getActiveSheet()->getStyle('I'.$ivv)->applyFromArray($fuentes2);
			$objPHPExcel->getActiveSheet()->setCellValue('J'.$ivv, '=SUM(J'.($iy+1).':J'.($iy+12).')');
			$objPHPExcel->getActiveSheet()->setCellValue('K'.$ivv, '=SUM(K'.($iy+1).':K'.($iy+12).')');
			$objPHPExcel->getActiveSheet()->getStyle('J'.($ivv).":K".($ivv))->getNumberFormat()->setFormatCode('#,##0.00');
			
			//$objPHPExcel->getActiveSheet()->getStyle('I'.$ivv)->applyFromArray($fuentes2);
			//$objPHPExcel->getActiveSheet()->setCellValue('K24', '=SUM(G24+E28+E29)');
			//$objPHPExcel->getActiveSheet()->getStyle('J24')->getNumberFormat()->setFormatCode('#,##0.00');


			//  Cálculo de PRIMA ANTIGUEDAD
			
			$ipa =7;
			
			$objPHPExcel->getActiveSheet()->setCellValue('M'.$ipa, 'PRIMA ANTIGUEDAD');
			$objPHPExcel->getActiveSheet()->getStyle('M'.$ipa)->applyFromArray($fuentes);
			$ivv = $ipa;

			$ipa+=2;

			$objPHPExcel->getActiveSheet()->setCellValue('M'.$ipa, 'MESES');
			$objPHPExcel->getActiveSheet()->getStyle('M'.$ipa)->applyFromArray($fuentes);

			$im = $ipa += 2;
			$iy = $ipa -= 1;
			$ipa++;
			for ($ilm=0; $ilm < 12; $ilm++) 
			{
				$mes_letras = $meses[$ilm]; 
				$objPHPExcel->getActiveSheet()->setCellValue('M'.$ipa, $mes_letras);
				$ipa++;

			}
			$fecha_ingreso = new DateTime($row2['fecing']);

			$dia_ing  = $fecha_ingreso->format('d');
			$mes_ing  = $fecha_ingreso->format('n');
			$anio_ing = $fecha_ingreso->format('Y');
			$anio_ant = $anio - 1;
			$anio_ant2 = $anio_ant - 1;
			$anio_ant3 = $anio_ant2 - 1;
			$anio_ant4 = $anio_ant3 - 1;
			$anio_ant5 = $anio_ant4 - 1;

			$objPHPExcel->getActiveSheet()->setCellValue('O'.$iy, $anio_ant5);
			$objPHPExcel->getActiveSheet()->setCellValue('P'.$iy, $anio_ant4);
			$objPHPExcel->getActiveSheet()->setCellValue('Q'.$iy, $anio_ant3);
			$objPHPExcel->getActiveSheet()->setCellValue('R'.$iy, $anio_ant2);
			$objPHPExcel->getActiveSheet()->setCellValue('S'.$iy, $anio_ant);
			$objPHPExcel->getActiveSheet()->setCellValue('T'.$iy, $anio);

			$objPHPExcel->getActiveSheet()->getStyle('O'.$iy.':T'.$iy)->applyFromArray($borders);

			for ($mes_=1; $mes_ <= 12; $mes_++) 
			{
				$amaxonia = new bd($_SESSION['bd']);
				if (!$existe_tabla->existe) {

					$sql_anio_acum = "
					SELECT 
						( SUM( IF(YEAR(fecha_pago) ='{$anio_ant5}' AND MONTH(fecha_pago)={$mes_}, salario_bruto,0) )+SUM( IF(YEAR(fecha_pago) ='{$anio_ant5}' AND MONTH(fecha_pago)={$mes_} AND liquida='0.00', vacac,0) )+SUM( IF(YEAR(fecha_pago) ='{$anio_ant5}' AND MONTH(fecha_pago)={$mes_}, bono,0) )+SUM( IF(YEAR(fecha_pago) ='{$anio_ant5}' AND MONTH(fecha_pago)={$mes_} AND liquida='0.00', otros_ing , 0) ) ) AS monto1,
						( SUM( IF(YEAR(fecha_pago) ='{$anio_ant4}' AND MONTH(fecha_pago)={$mes_}, salario_bruto,0) )+SUM( IF(YEAR(fecha_pago) ='{$anio_ant4}' AND MONTH(fecha_pago)={$mes_} AND liquida='0.00', vacac,0) )+SUM( IF(YEAR(fecha_pago) ='{$anio_ant4}' AND MONTH(fecha_pago)={$mes_}, bono,0) )+SUM( IF(YEAR(fecha_pago) ='{$anio_ant4}' AND MONTH(fecha_pago)={$mes_} AND liquida='0.00', otros_ing , 0) ) ) AS monto2, 
						( SUM( IF(YEAR(fecha_pago) ='{$anio_ant3}' AND MONTH(fecha_pago)={$mes_}, salario_bruto,0) )+SUM( IF(YEAR(fecha_pago) ='{$anio_ant3}' AND MONTH(fecha_pago)={$mes_} AND liquida='0.00', vacac,0) )+SUM( IF(YEAR(fecha_pago) ='{$anio_ant3}' AND MONTH(fecha_pago)={$mes_}, bono,0) )+SUM( IF(YEAR(fecha_pago) ='{$anio_ant3}' AND MONTH(fecha_pago)={$mes_} AND liquida='0.00', otros_ing , 0) )) AS monto3, 
						( SUM( IF(YEAR(fecha_pago) ='{$anio_ant2}' AND MONTH(fecha_pago)={$mes_}, salario_bruto,0) )+SUM( IF(YEAR(fecha_pago) ='{$anio_ant2}' AND MONTH(fecha_pago)={$mes_} AND liquida='0.00', vacac,0) )+SUM( IF(YEAR(fecha_pago) ='{$anio_ant2}' AND MONTH(fecha_pago)={$mes_}, bono,0) )+SUM( IF(YEAR(fecha_pago) ='{$anio_ant2}' AND MONTH(fecha_pago)={$mes_} AND liquida='0.00', otros_ing , 0) )) AS monto4, 
						( SUM( IF(YEAR(fecha_pago) ='{$anio_ant}' AND MONTH(fecha_pago)={$mes_}, salario_bruto,0) )+SUM( IF(YEAR(fecha_pago) ='{$anio_ant}' AND MONTH(fecha_pago)={$mes_} AND liquida='0.00', vacac,0) )+SUM( IF(YEAR(fecha_pago) ='{$anio_ant}' AND MONTH(fecha_pago)={$mes_}, bono,0) )+SUM( IF(YEAR(fecha_pago) ='{$anio_ant}' AND MONTH(fecha_pago)={$mes_} AND liquida='0.00', otros_ing , 0) ) ) AS monto5,
						( SUM( IF(YEAR(fecha_pago) ='{$anio}' AND MONTH(fecha_pago)={$mes_}, salario_bruto,0) )+SUM( IF(YEAR(fecha_pago) ='{$anio}' AND MONTH(fecha_pago)={$mes_} AND liquida='0.00', vacac,0) )+SUM( IF(YEAR(fecha_pago) ='{$anio}' AND MONTH(fecha_pago)={$mes_}, bono,0) )+SUM( IF(YEAR(fecha_pago) ='{$anio}' AND MONTH(fecha_pago)={$mes_} AND liquida='0.00', otros_ing , 0) ) ) AS monto6	
					FROM salarios_acumulados WHERE ficha = {$ficha} AND MONTH(fecha_pago)={$mes_}";
				} 
				else 
				{
				
					$sql_anio_acum = "	SELECT  
						COALESCE(SUM( IF(anio ='{$anio_ant5}' AND mes ={$mes_}, monto,0) )) AS monto1,
						COALESCE(SUM( IF(anio ='{$anio_ant4}' AND mes ={$mes_}, monto,0) )) AS monto2,
						COALESCE(SUM( IF(anio ='{$anio_ant3}' AND mes ={$mes_}, monto,0) )) AS monto3,
						COALESCE(SUM( IF(anio ='{$anio_ant2}' AND mes ={$mes_}, monto,0) )) AS monto4,
						COALESCE(SUM( IF(anio ='{$anio_ant}' AND mes ={$mes_}, monto,0) )) AS monto5,
						COALESCE(SUM( IF(anio ='{$anio}' AND mes ={$mes_}, monto,0) )) AS monto6
						FROM nom_movimientos_nomina 
						WHERE ficha = {$ficha}  AND tipnom ={$codtip} AND codcon in (93)";
					
				}
				//echo $sql_anio_acum;exit;
				$fila_suel     = $amaxonia->query($sql_anio_acum)->fetch_object();


				$objPHPExcel->getActiveSheet()->setCellValue('O'.($iy+$mes_), $fila_suel->monto1);
				$objPHPExcel->getActiveSheet()->setCellValue('P'.($iy+$mes_), $fila_suel->monto2);
				$objPHPExcel->getActiveSheet()->setCellValue('Q'.($iy+$mes_), $fila_suel->monto3);
				$objPHPExcel->getActiveSheet()->setCellValue('R'.($iy+$mes_), $fila_suel->monto4);
				$objPHPExcel->getActiveSheet()->setCellValue('S'.($iy+$mes_), $fila_suel->monto5);
				$objPHPExcel->getActiveSheet()->setCellValue('T'.($iy+$mes_), $fila_suel->monto6);
				$objPHPExcel->getActiveSheet()->getStyle('O'.($iy+$mes_).':T'.($iy+$mes_))->getNumberFormat()->setFormatCode('#,##0.00');

			}
			

			$ipa++;
			$objPHPExcel->getActiveSheet()->setCellValue('M'.$ipa, 'TOTALES');

			$objPHPExcel->getActiveSheet()->getStyle('M'.$ipa)->applyFromArray($fuentes);
			$objPHPExcel->getActiveSheet()->setCellValue('O'.$ipa, '=SUM(O'.($iy+1).':O'.($iy+12).')');
			$objPHPExcel->getActiveSheet()->setCellValue('P'.$ipa, '=SUM(P'.($iy+1).':P'.($iy+12).')');
			$objPHPExcel->getActiveSheet()->setCellValue('Q'.$ipa, '=SUM(Q'.($iy+1).':Q'.($iy+12).')');
			$objPHPExcel->getActiveSheet()->setCellValue('R'.$ipa, '=SUM(R'.($iy+1).':R'.($iy+12).')');
			$objPHPExcel->getActiveSheet()->setCellValue('S'.$ipa, '=SUM(S'.($iy+1).':S'.($iy+12).')');
			$objPHPExcel->getActiveSheet()->setCellValue('T'.$ipa, '=SUM(T'.($iy+1).':T'.($iy+12).')');
			$objPHPExcel->getActiveSheet()->getStyle('O'.($ipa).':T'.($ipa))->getNumberFormat()->setFormatCode('#,##0.00');
			
			$objPHPExcel->getActiveSheet()->setCellValue('Q26', 'TOTAL GENERAL');
			$objPHPExcel->getActiveSheet()->setCellValue('T26', "=SUM(O24+P24+Q24+R24+S24+T24)");
			$objPHPExcel->getActiveSheet()->getStyle('T26')->getNumberFormat()->setFormatCode('#,##0.00');
			
			/*$objPHPExcel->getActiveSheet()->setCellValue('Q27', 'TOTAL FINAL');
			$objPHPExcel->getActiveSheet()->setCellValue('S27', '=SUM(E28+S26)');
			$objPHPExcel->getActiveSheet()->getStyle('S27')->getNumberFormat()->setFormatCode('#,##0.00');
			
			$objPHPExcel->getActiveSheet()->setCellValue('O29', 'PA');
			$objPHPExcel->getActiveSheet()->setCellValue('P29', '0.01923');*/

			$objPHPExcel->getActiveSheet()->setCellValue('S28', '=(P29*S27)');
			$objPHPExcel->getActiveSheet()->getStyle('S28')->getNumberFormat()->setFormatCode('#,##0.00');
		
			//  Cálculo de INDEMNIZACIÓN

			$sql_prima = "	SELECT coalesce(SUM(monto),0) as indemnizacion
							FROM nom_movimientos_nomina 
							WHERE ficha = {$ficha}  AND tipnom ={$codtip} AND codcon in (94)";
			
			$fila_prima = $amaxonia->query($sql_prima);

			if($fila_prima->num_rows > 0){

				$ipa =7;

				$objPHPExcel->getActiveSheet()->setCellValue('V'.$ipa, 'INDEMNIZACIÓN');
				$objPHPExcel->getActiveSheet()->getStyle('V'.$ipa)->applyFromArray($fuentes);
				$ivv = $ipa;

				$ipa+=2;

				$objPHPExcel->getActiveSheet()->setCellValue('V'.$ipa, 'MESES');
				$objPHPExcel->getActiveSheet()->getStyle('V'.$ipa)->applyFromArray($fuentes);

				$im = $ipa += 2;
				$iy = $ipa -= 1;
				$ipa++;
				for ($ilm=0; $ilm < 12; $ilm++) 
				{
					$mes_letras = $meses[$ilm]; 
					$objPHPExcel->getActiveSheet()->setCellValue('V'.$ipa, $mes_letras);
					$ipa++;

				}
				$fecha_ingreso = new DateTime($row2['fecing']);

				$dia_ing  = $fecha_ingreso->format('d');
				$mes_ing  = $fecha_ingreso->format('n');
				$anio_ing = $fecha_ingreso->format('Y');
				$anio_ant = $anio - 1;
				$anio_ant2 = $anio_ant - 1;
				$anio_ant3 = $anio_ant2 - 1;
				$anio_ant4 = $anio_ant3 - 1;
				$anio_ant5 = $anio_ant4 - 1;

				$objPHPExcel->getActiveSheet()->setCellValue('X'.$iy, $anio_ant5);
				$objPHPExcel->getActiveSheet()->setCellValue('Y'.$iy, $anio_ant4);
				$objPHPExcel->getActiveSheet()->setCellValue('Z'.$iy, $anio_ant3);
				$objPHPExcel->getActiveSheet()->setCellValue('AA'.$iy, $anio_ant2);
				$objPHPExcel->getActiveSheet()->setCellValue('AB'.$iy, $anio_ant);
				$objPHPExcel->getActiveSheet()->setCellValue('AC'.$iy, $anio);

				$objPHPExcel->getActiveSheet()->getStyle('V'.$iy.':AC'.$iy)->applyFromArray($borders);

				for ($mes_=1; $mes_ <= 12; $mes_++) 
				{
					$amaxonia = new bd($_SESSION['bd']);
					if (!$existe_tabla->existe) {

						$sql_anio_acum = "
						SELECT 
							SUM( IF(YEAR(fecha_pago) ='{$anio_ant5}' AND MONTH(fecha_pago)={$mes_}, salario_bruto,0) ) AS monto1,
							SUM( IF(YEAR(fecha_pago) ='{$anio_ant4}' AND MONTH(fecha_pago)={$mes_}, salario_bruto,0) ) AS monto2, 
							SUM( IF(YEAR(fecha_pago) ='{$anio_ant3}' AND MONTH(fecha_pago)={$mes_}, salario_bruto,0) ) AS monto3, 
							SUM( IF(YEAR(fecha_pago) ='{$anio_ant2}' AND MONTH(fecha_pago)={$mes_}, salario_bruto,0) ) AS monto4, 
							SUM( IF(YEAR(fecha_pago) ='{$anio_ant}' AND MONTH(fecha_pago)={$mes_}, salario_bruto,0) ) AS monto5,
							SUM( IF(YEAR(fecha_pago) ='{$anio}' AND MONTH(fecha_pago)={$mes_}, salario_bruto,0) ) AS monto6	
						FROM salarios_acumulados WHERE ficha = {$ficha} AND MONTH(fecha_pago)={$mes_}";
					} 
					else 
					{
					
						$sql_anio_acum = "	SELECT  
							COALESCE(SUM( IF(anio ='{$anio_ant5}' AND mes ={$mes_}, monto,0) )) AS monto1,
							COALESCE(SUM( IF(anio ='{$anio_ant4}' AND mes ={$mes_}, monto,0) )) AS monto2,
							COALESCE(SUM( IF(anio ='{$anio_ant3}' AND mes ={$mes_}, monto,0) )) AS monto3,
							COALESCE(SUM( IF(anio ='{$anio_ant2}' AND mes ={$mes_}, monto,0) )) AS monto4,
							COALESCE(SUM( IF(anio ='{$anio_ant}' AND mes ={$mes_}, monto,0) )) AS monto5,
							COALESCE(SUM( IF(anio ='{$anio}' AND mes ={$mes_}, monto,0) )) AS monto6
							FROM nom_movimientos_nomina 
							WHERE ficha = {$ficha}  AND tipnom ={$codtip} AND codcon in (94)";
						
					}
					$fila_suel     = $amaxonia->query($sql_anio_acum)->fetch_object();
					$objPHPExcel->getActiveSheet()->setCellValue('X'.($iy+$mes_), $fila_suel->monto1);
					$objPHPExcel->getActiveSheet()->setCellValue('Y'.($iy+$mes_), $fila_suel->monto2);
					$objPHPExcel->getActiveSheet()->setCellValue('Z'.($iy+$mes_), $fila_suel->monto3);
					$objPHPExcel->getActiveSheet()->setCellValue('AA'.($iy+$mes_), $fila_suel->monto4);
					$objPHPExcel->getActiveSheet()->setCellValue('AB'.($iy+$mes_), $fila_suel->monto5);
					$objPHPExcel->getActiveSheet()->setCellValue('AC'.($iy+$mes_), $fila_suel->monto6);
					$objPHPExcel->getActiveSheet()->getStyle('C'.($iy+$mes_).':AC'.($iy+$mes_))->getNumberFormat()->setFormatCode('#,##0.00');

				}
				

				$ipa++;
				$objPHPExcel->getActiveSheet()->setCellValue('V'.$ipa, 'TOTALES');

				$objPHPExcel->getActiveSheet()->getStyle('V'.$ipa)->applyFromArray($fuentes);
				$objPHPExcel->getActiveSheet()->setCellValue('X'.$ipa, '=SUM(X'.($iy+1).':X'.($iy+12).')');
				$objPHPExcel->getActiveSheet()->setCellValue('Y'.$ipa, '=SUM(Y'.($iy+1).':Y'.($iy+12).')');
				$objPHPExcel->getActiveSheet()->setCellValue('Z'.$ipa, '=SUM(Z'.($iy+1).':Z'.($iy+12).')');
				$objPHPExcel->getActiveSheet()->setCellValue('AA'.$ipa, '=SUM(AA'.($iy+1).':AA'.($iy+12).')');
				$objPHPExcel->getActiveSheet()->setCellValue('AB'.$ipa, '=SUM(AB'.($iy+1).':AB'.($iy+12).')');
				$objPHPExcel->getActiveSheet()->setCellValue('AC'.$ipa, '=SUM(AC'.($iy+1).':AC'.($iy+12).')');
				$objPHPExcel->getActiveSheet()->getStyle('V'.($ipa).':AC'.($ipa))->getNumberFormat()->setFormatCode('#,##0.00');
				
				$objPHPExcel->getActiveSheet()->setCellValue('AA26', 'TOTAL GENERAL');
				$objPHPExcel->getActiveSheet()->setCellValue('AC26', '=SUM(X24+Y24+Z24+AA24+AB24+AC24)');
				$objPHPExcel->getActiveSheet()->getStyle('AC26')->getNumberFormat()->setFormatCode('#,##0.00');
				
				$objPHPExcel->getActiveSheet()->setCellValue('AA27', 'TOTAL FINAL');
				$objPHPExcel->getActiveSheet()->setCellValue('AC27', '=SUM(E28+AC26)');
				$objPHPExcel->getActiveSheet()->getStyle('AC27')->getNumberFormat()->setFormatCode('#,##0.00');

				$objPHPExcel->getActiveSheet()->setCellValue('Z29', 'INDEN');
				$objPHPExcel->getActiveSheet()->setCellValue('AA29', '0.0654');

				$objPHPExcel->getActiveSheet()->setCellValue('AC28', '=(AA29*AC27)');
				$objPHPExcel->getActiveSheet()->getStyle('AC28')->getNumberFormat()->setFormatCode('#,##0.00');
			}


			/*/////////////////////////////////////////////*/
			/*                                             */
			/*  Cálculo de RESUMEN                         */
			/*                                             */
			/*/////////////////////////////////////////////*/



			$ivv++;
			$amaxonia = new bd($_SESSION['bd']);
			/*if ($existe_tabla->existe) {
				$sql_resumen = "
				SELECT 
					SUM(sal.salario_bruto) as salario,
					SUM(sal.vacac) as vacaciones,
					SUM(sal.xiii) as decimo_prop,
					SUM(sal.gtorep) as gtorep,
					SUM(sal.xiii_gtorep) as xiii_gtorep,
					SUM(sal.liquida) as liquida,
					SUM(sal.bono) as bono,
					SUM(sal.otros_ing) as otros_ing,
					SUM(sal.s_s) as seguro_social,
					SUM(sal.s_e) as seguro_educativo,
					SUM(sal.islr) as isr,
					SUM(sal.islr_gr) as isr_gastos,
					SUM(sal.acreedor_suma) as descuentos,
					SUM(sal.Neto) as Neto
				FROM 
					salarios_acumulados as sal
					INNER JOIN nom_movimientos_nomina as nmn on (sal.cod_planilla = nmn.codnom AND sal.tipo_planilla AND nmn.tipnom)
				WHERE 
					sal.cod_planilla='{$codnom}' AND sal.tipo_planilla='{$codtip}' AND sal.ficha='{$ficha}'
				";
			}
			else
			{*/
				$sql_resumen = "
				SELECT
				COALESCE((SELECT SUM(n.monto) as monto FROM nom_movimientos_nomina n 
				WHERE  n.codnom='{$codnom}' AND n.tipnom='{$codtip}' AND n.ficha='{$ficha}' AND n.codcon=90),0) as salario,
				COALESCE((SELECT SUM(n.monto) as monto FROM nom_movimientos_nomina n 
				WHERE  n.codnom='{$codnom}' AND n.tipnom='{$codtip}' AND n.ficha='{$ficha}' AND n.codcon in (103)),0) as horas_pendientes,
				COALESCE((SELECT  SUM(n.monto) FROM nom_movimientos_nomina n 
					WHERE  n.codnom='{$codnom}' AND n.tipnom='{$codtip}' AND n.ficha='{$ficha}' AND n.codcon in (96)), 0) as cesantia,
				COALESCE((SELECT  SUM(n.monto) FROM nom_movimientos_nomina n 
					WHERE  n.codnom='{$codnom}' AND n.tipnom='{$codtip}' AND n.ficha='{$ficha}' AND n.codcon in (89)), 0) as vac_vencidas,
				COALESCE((SELECT  SUM(n.monto) FROM nom_movimientos_nomina n 
					WHERE  n.codnom='{$codnom}' AND n.tipnom='{$codtip}' AND n.ficha='{$ficha}' AND n.codcon in (97)), 0) as enf,
				COALESCE((SELECT SUM(n.monto) as monto FROM nom_movimientos_nomina n 
				WHERE  n.codnom='{$codnom}' AND n.tipnom='{$codtip}' AND n.ficha='{$ficha}' AND n.codcon in (91,117,157)), 0) as vac_prop,
				COALESCE((SELECT SUM(n.monto) as monto FROM nom_movimientos_nomina n 
				WHERE  n.codnom='{$codnom}' AND n.tipnom='{$codtip}' AND n.ficha='{$ficha}' AND n.codcon in (199,92)), 0) as decimo_prop,
				COALESCE((SELECT SUM(n.monto) as monto FROM nom_movimientos_nomina n 
				WHERE  n.codnom='{$codnom}' AND n.tipnom='{$codtip}' AND n.ficha='{$ficha}' AND n.codcon in (200)), 0) as seguro_social,
				COALESCE((SELECT SUM(n.monto) as monto FROM nom_movimientos_nomina n 
				WHERE  n.codnom='{$codnom}' AND n.tipnom='{$codtip}' AND n.ficha='{$ficha}' AND n.codcon in (205)), 0) as seguro_social_liq,
				COALESCE((SELECT  SUM(n.monto) as monto FROM nom_movimientos_nomina n 
				WHERE  n.codnom='{$codnom}' AND n.tipnom='{$codtip}' AND n.ficha='{$ficha}' AND n.codcon in (201,206)), 0) as seguro_educativo,
				COALESCE((SELECT  n.monto FROM nom_movimientos_nomina n 
				WHERE  n.codnom='{$codnom}' AND n.tipnom='{$codtip}' AND n.ficha='{$ficha}' AND n.codcon=208), 0) as seguro_social_vac,
				COALESCE((SELECT  n.monto FROM nom_movimientos_nomina n 
				WHERE  n.codnom='{$codnom}' AND n.tipnom='{$codtip}' AND n.ficha='{$ficha}' AND n.codcon=213), 0) as islr_vac,
				COALESCE((SELECT  n.monto FROM nom_movimientos_nomina n 
				WHERE  n.codnom='{$codnom}' AND n.tipnom='{$codtip}' AND n.ficha='{$ficha}' AND n.codcon=93), 0) as prima,
				COALESCE((SELECT  n.monto FROM nom_movimientos_nomina n 
				WHERE  n.codnom='{$codnom}' AND n.tipnom='{$codtip}' AND n.ficha='{$ficha}' AND n.codcon in (94)), 0) as indem,
				COALESCE((SELECT  n.monto FROM nom_movimientos_nomina n 
				WHERE  n.codnom='{$codnom}' AND n.tipnom='{$codtip}' AND n.ficha='{$ficha}' AND n.codcon in (95)), 0) as preaviso,
				COALESCE((SELECT  SUM(n.monto) FROM nom_movimientos_nomina n 
				WHERE  n.codnom='{$codnom}' AND n.tipnom='{$codtip}' AND n.ficha='{$ficha}'
				AND    n.codcon IN (202, 601, 605)), 0) as isr,
				COALESCE((SELECT  SUM(n.monto) FROM nom_movimientos_nomina n 
				WHERE  n.codnom='{$codnom}' AND n.tipnom='{$codtip}' AND n.ficha='{$ficha}'
				AND    n.codcon IN (207, 208, 606, 607)), 0) as isr_gastos,
				(SELECT  COALESCE(SUM(n.monto), 0) FROM nom_movimientos_nomina n 
				WHERE  n.codnom='{$codnom}' AND n.tipnom='{$codtip}' AND n.ficha='{$ficha}' AND (n.codcon BETWEEN 500 AND 507 
				OR n.codcon BETWEEN 508 AND 599))  as descuentos,
				(SELECT  COALESCE(SUM(n.monto), 0) FROM nom_movimientos_nomina n 
				WHERE  n.codnom='{$codnom}' AND n.tipnom='{$codtip}' AND n.ficha='{$ficha}' AND n.codcon=508)  as cxc,
				(SELECT  COALESCE(SUM(n.monto), 0) FROM nom_movimientos_nomina n 
				WHERE  n.codnom='{$codnom}' AND n.tipnom='{$codtip}' AND n.ficha='{$ficha}' AND n.codcon=114)  as vacaciones";
			//}
			$fila_resumen     = $amaxonia->query($sql_resumen)->fetch_object();
			if ($existe_tabla->existe) {
				$sql_prima = "
					SELECT 
						coalesce(SUM(salario_bruto),0) as monto_prima
					FROM 
						salarios_acumulados
						
					WHERE 
						ficha='{$ficha}'
					";
			}
			else
			{

				$sql_prima = "	SELECT coalesce(SUM(monto),0) as monto_prima
							from nom_movimientos_nomina
							where ficha = {$ficha} AND codcon in (100)";
			}
			$fila_prima = $amaxonia->query($sql_prima)->fetch_object();

			$ixx+=2;

			$total_prima = $fila_prima->monto_prima * 0.01923;
			$objPHPExcel->getActiveSheet()->getStyle('A'.$ixx)->applyFromArray($fuentes2);
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$ixx,'RESÚMEN');
			$ixx++;
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$ixx,'SALARIO NO PAGADO');
			$objPHPExcel->getActiveSheet()->setCellValue('E'.$ixx, $fila_resumen->salario);
			$objPHPExcel->getActiveSheet()->getStyle('E'.$ixx)->getNumberFormat()->setFormatCode('#,##0.00');

			$iaa=$ix=$ixx;
			$ixx++;

			$objPHPExcel->getActiveSheet()->setCellValue('A'.$ixx,'HORAS PENDIENTES');
			$objPHPExcel->getActiveSheet()->setCellValue('E'.$ixx, $fila_resumen->horas_pendientes);	
			$objPHPExcel->getActiveSheet()->getStyle('E'.$ixx)->getNumberFormat()->setFormatCode('#,##0.00');
			$ixx++;
			$iaa++;

			$objPHPExcel->getActiveSheet()->setCellValue('A'.$ixx,'VACACIONES VENCIDAS');
			$objPHPExcel->getActiveSheet()->setCellValue('E'.$ixx, $fila_resumen->vac_vencidas);	
			$objPHPExcel->getActiveSheet()->getStyle('E'.$ixx)->getNumberFormat()->setFormatCode('#,##0.00');
			$ixx++;
			$iaa++;

			$objPHPExcel->getActiveSheet()->setCellValue('A'.$ixx,'VACACIONES PROPORCIONALES');
			$objPHPExcel->getActiveSheet()->setCellValue('E'.$ixx, $fila_resumen->vac_prop);	
			$objPHPExcel->getActiveSheet()->getStyle('E'.$ixx)->getNumberFormat()->setFormatCode('#,##0.00');
			$ixx++;
			$iaa++;

			$objPHPExcel->getActiveSheet()->setCellValue('A'.$ixx,'FONDO DE CESANTÍA');
			$objPHPExcel->getActiveSheet()->setCellValue('E'.$ixx, $fila_resumen->cesantia);
			$objPHPExcel->getActiveSheet()->getStyle('E'.$ixx)->getNumberFormat()->setFormatCode('#,##0.00');
			$ixx++;
			$iaa++;

			$objPHPExcel->getActiveSheet()->setCellValue('A'.$ixx,'FONDO ENF');
			$objPHPExcel->getActiveSheet()->setCellValue('E'.$ixx, $fila_resumen->enf);
			$objPHPExcel->getActiveSheet()->getStyle('E'.$ixx)->getNumberFormat()->setFormatCode('#,##0.00');
			$ixx++;
			$iaa++;


			$objPHPExcel->getActiveSheet()->setCellValue('A'.$ixx,'PRIMA ANTIGÜEDAD');
			$objPHPExcel->getActiveSheet()->setCellValue('E'.$ixx, $fila_resumen->prima);
			$objPHPExcel->getActiveSheet()->getStyle('E'.$ixx)->getNumberFormat()->setFormatCode('#,##0.00');
			$ixx++;
			$iaa++;


			$objPHPExcel->getActiveSheet()->setCellValue('A'.$ixx,'XIII MES PROPORCIONAL');
			$objPHPExcel->getActiveSheet()->setCellValue('E'.$ixx, $fila_resumen->decimo_prop);	
			$objPHPExcel->getActiveSheet()->getStyle('E'.$ixx)->getNumberFormat()->setFormatCode('#,##0.00');
			$ixx++;
			$iaa++;
			if ($fila_resumen->indem > 0) {
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$ixx,'INDEMNIZACIÓN');
				$objPHPExcel->getActiveSheet()->setCellValue('E'.$ixx, $fila_resumen->indem);	
				$objPHPExcel->getActiveSheet()->getStyle('E'.$ixx)->getNumberFormat()->setFormatCode('#,##0.00');
				$ixx++;
				$iaa++;
			}
			$isr_liq = ($fila_resumen->salario + $fila_resumen->vac_prop)*0.125;

			$subtotal = $fila_resumen->vac_vencidas + $fila_resumen->salario +$fila_resumen->horas_pendientes + $fila_resumen->cesantia + $fila_resumen->enf + $fila_resumen->prima + $fila_resumen->decimo_prop + $fila_resumen->indem + $fila_resumen->vac_prop;
			$objPHPExcel->getActiveSheet()->getStyle('A'.$ixx)->applyFromArray($fuentes2);
			$objPHPExcel->getActiveSheet()->getStyle('E'.$ixx)->applyFromArray($fuentes2);

			$objPHPExcel->getActiveSheet()->setCellValue('A'.$ixx,'SUBTOTAL');
			//$objPHPExcel->getActiveSheet()->setCellValue('E'.$ixx, '=SUM(A'.$ix.'A:'.$iaa.')');
			$objPHPExcel->getActiveSheet()->getStyle('E'.$ixx)->getNumberFormat()->setFormatCode('#,##0.00');

			$ixx++;
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$ixx,'SEGURO SOCIAL (S.S.)');
			$objPHPExcel->getActiveSheet()->setCellValue('E'.$ixx, $fila_resumen->seguro_social);
			$objPHPExcel->getActiveSheet()->getStyle('E'.$ixx)->getNumberFormat()->setFormatCode('#,##0.00');
					$ixx++;
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$ixx,'SEGURO SOCIAL (S.S.) 7.25% XIII');
			$objPHPExcel->getActiveSheet()->setCellValue('E'.$ixx, $fila_resumen->seguro_social_liq);
			$objPHPExcel->getActiveSheet()->getStyle('E'.$ixx)->getNumberFormat()->setFormatCode('#,##0.00');
			$ixx++;
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$ixx,'SEGURO EDUCATIVO (S.E.) LQ');
			$objPHPExcel->getActiveSheet()->setCellValue('E'.$ixx, $fila_resumen->seguro_educativo);
			$objPHPExcel->getActiveSheet()->getStyle('E'.$ixx)->getNumberFormat()->setFormatCode('#,##0.00');
			$ixx++;
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$ixx,'ISR');
			$objPHPExcel->getActiveSheet()->setCellValue('E'.$ixx, $fila_resumen->isr);
			$objPHPExcel->getActiveSheet()->getStyle('E'.$ixx)->getNumberFormat()->setFormatCode('#,##0.00');
			$ixx++;
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$ixx,'OTROS DESCUENTOS');
			$objPHPExcel->getActiveSheet()->setCellValue('E'.$ixx, $fila_resumen->descuentos);
			$objPHPExcel->getActiveSheet()->getStyle('E'.$ixx)->getNumberFormat()->setFormatCode('#,##0.00');
			$ixx++;
			$total_des =  $fila_resumen->seguro_social + $fila_resumen->seguro_educativo + $fila_resumen->descuentos +  $fila_resumen->isr + $fila_resumen->seguro_social_liq;
			$total = $subtotal - $total_des;
			$objPHPExcel->getActiveSheet()->getStyle('A'.$ixx)->applyFromArray($fuentes2);
			$objPHPExcel->getActiveSheet()->getStyle('E'.$ixx)->applyFromArray($fuentes2);

			$objPHPExcel->getActiveSheet()->setCellValue('A'.$ixx,'SALDO A PAGAR');
			$objPHPExcel->getActiveSheet()->setCellValue('E'.$ixx, $total);
			$objPHPExcel->getActiveSheet()->getStyle('E'.$ixx)->getNumberFormat()->setFormatCode('#,##0.00');

			$ixx++;

			$sheet++;
		}
		
	}

	$NOMINA = str_replace(' ', '', $NOMINA);
	$filename = "LIQUIDACIONES_DEL_".fecha($desde).'_HASTA_'.fecha($hasta);

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
	ob_clean();
	$objWriter->save('php://output');
}