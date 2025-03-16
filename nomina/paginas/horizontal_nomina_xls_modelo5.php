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
	$objPHPExcel->getDefaultStyle()->getFont()->setSize(7);


	$sql = "SELECT e.nom_emp as empresa, e.rif, e.tel_emp as telefono, e.dir_emp as direccion, e.ciu_emp as ciudad, 
				e.edo_emp, e.imagen_izq as logo
			FROM   nomempresa e";
	$res=$conexion->query($sql);
	$fila=$res->fetch_array();
	$logo=$fila['logo'];

	$sql2 = "SELECT np.periodo_ini as desde, np.periodo_fin as hasta, np.mes, np.anio, status as status, 
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

	$empresa = $fila['empresa'];

	$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
	$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_LEGAL);

	$objPHPExcel->getActiveSheet()
				->setCellValue('A2', strtoupper($empresa) )
				->setCellValue('A3', 'PLANILLA QUINCENAL')
				->setCellValue('A4', 'DEL '. $dia_ini .' AL '. $dia_fin .' de '. $mes_letras .' '. $anio );


	//$objPHPExcel->getActiveSheet()->getStyle('A8:V8')->getAlignment()->setShrinkToFit(true);
	$objPHPExcel->getActiveSheet()->getStyle('A8:Y8')->getAlignment()->setWrapText(true);
	//$objPHPExcel->getActiveSheet()->getStyle('A2:A4')->getFont()->setName('Calibri');
	$objPHPExcel->getActiveSheet()->getStyle('A2:A4')->getFont()->setSize(7);
	$objPHPExcel->getActiveSheet()->getStyle('A2:A4')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('A2:A4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->mergeCells('A2:S2');
	$objPHPExcel->getActiveSheet()->mergeCells('A3:S3');
	$objPHPExcel->getActiveSheet()->mergeCells('A4:S4');

	//$objPHPExcel->getActiveSheet()->getStyle('C2:D5')->applyFromArray(allBordersThin());
	//$objPHPExcel->getActiveSheet()->getStyle('C2:D5')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	$objPHPExcel->getActiveSheet()
//            ->setCellValue('A6', 'SALARIOS')
            ->setCellValue('C6', 'ASIGNACIONES')
            ->setCellValue('Q6', 'DEDUCCIONES')
            ->setCellValue('K7', '')
            ->setCellValue('Q7', '')
            ->setCellValue('R7', '')
            ->setCellValue('S7', '')
            ->setCellValue('D7', 'HORAS')
            ->setCellValue('A8', 'No.')
            ->setCellValue('B8', 'NOMBRE')
            ->setCellValue('C8', 'SAL * HORAS')
            ->setCellValue('D8', 'Reg')
            ->setCellValue('E8', '1.25')
            ->setCellValue('F8', '1.50')
			->setCellValue('G8', 'Domingo o nac')
			->setCellValue('H8', '2.18')
			->setCellValue('I8', '2.625')
			->setCellValue('J8', 'OTRAS HORAS')
			->setCellValue('K8', 'HORAS LLUVIA / ALTURA')
			->setCellValue('L8', 'NO PAGA')
			->setCellValue('M8', 'AUS P. HORAS')
			->setCellValue('N8', 'C.M.')
			->setCellValue('O8', 'O. INGRESOS')
            ->setCellValue('P8', 'SAL REG')
            ->setCellValue('Q8', 'BNF 14% COMIS')
            ->setCellValue('R8', 'SOBRE TIEMPO')
            ->setCellValue('S8', 'SAL BRUTO')
            ->setCellValue('T8', 'S.S.')
            ->setCellValue('U8', 'S.E.')
            ->setCellValue('V8', 'I.S.R.')
            ->setCellValue('W8', 'OTRAS DEDUC')
            ->setCellValue('X8', 'TOTAL DEDUC')
            ->setCellValue('Y8', 'TOTAL NETO');

	cellColor('C7:S8', '92d050');
	cellColor('T7:Y8', 'ffcc00');

	//$objPHPExcel->getActiveSheet()->getStyle('A6:Q8')->getFont()->setName('Calibri');
	$objPHPExcel->getActiveSheet()->getStyle('A6:K8')->getFont()->setSize(7);
	$objPHPExcel->getActiveSheet()->getStyle('A6:K8')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('A6:K8')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

	$objPHPExcel->getActiveSheet()->getStyle('L6:Y8')->getFont()->setSize(7);
	$objPHPExcel->getActiveSheet()->getStyle('L6:Y8')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('L6:Y8')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);


	$objPHPExcel->getActiveSheet()->mergeCells('C6:R6');
	$objPHPExcel->getActiveSheet()->mergeCells('S6:U6');
	$objPHPExcel->getActiveSheet()->mergeCells('D7:I7');
	$objPHPExcel->getActiveSheet()->getStyle('A6:Y8')->applyFromArray(allBordersThin());

	$objPHPExcel->getActiveSheet()->freezePane('C9'); // Inmovilizar Paneles

	/*$sql = "SELECT nn.codorg, nn.descrip,
				CASE WHEN nn.codorg=1 THEN 'Administrativos'
						WHEN nn.codorg=2 THEN 'Técnicos'
						WHEN nn.codorg=3 THEN 'Desarrollo Sostenible'
						WHEN nn.codorg=4 THEN 'Comercialización' 
				END as grupo
			FROM   nomnivel1 nn
			WHERE  nn.descrip NOT IN('Eventual', 'O&M')
			ORDER BY nn.codorg";
	$res=$conexion->query($sql);
	*/
	$i=9; $nivel=1;
	// $monto_salarios=0;
	$total_salarios=$total_comision=$total_combustible=$total_reembolso=$total_uso_auto=$total_bono="=";
	$total_extra_salario=$total_extra_gastosrep=$total_vacaciones=$total_cxc="=";
	$total_segurosocial_salario=$total_tardanza=$total_seguro_educativo="=";
	$total_isr_gastos=$total_isr_salario=$total_ausencia=$total_descuentos=$total_neto="=";
	$total_salario=$MesXIII=$Total_Desc_Ley=$SeguroSocialMesXIII=$total_desc_varios="=";
	//while($row=$res->fetch_array())
	//{
		$codorg = $row['codorg'];

		if($status == "A")
		{
			$sql2= "SELECT np.ficha, np.suesal as salario, np.hora_base as base,
				CONCAT_WS(' ', np.nombres, np.apellidos) as nombre,
				SUBSTRING_INDEX(SUBSTRING_INDEX(np.nombres, ' ', 1), ' ', -1) as primer_nombre,
				CASE WHEN np.apellidos LIKE 'De %' THEN 
				SUBSTRING_INDEX(SUBSTRING_INDEX(np.apellidos, ' ', 2), ' ',  2) 
				ELSE      SUBSTRING_INDEX(SUBSTRING_INDEX(np.apellidos, ' ', 1), ' ', -1) 
				END as primer_apellido
				FROM nom_movimientos_nomina n
				JOIN nompersonal np ON (n.ficha = np.ficha AND n.cedula = np.cedula)
				WHERE  n.codnom = '".$codnom."' AND n.tipnom = '".$codtip."'
				GROUP BY n.ficha
				ORDER  BY n.ficha";
		}
		else
		{
			$sql2= "SELECT np.ficha, nnn.suesal as salario, np.hora_base as base,
				CONCAT_WS(' ', np.nombres, np.apellidos) as nombre,
				SUBSTRING_INDEX(SUBSTRING_INDEX(np.nombres, ' ', 1), ' ', -1) as primer_nombre,
				CASE WHEN np.apellidos LIKE 'De %' THEN 
				SUBSTRING_INDEX(SUBSTRING_INDEX(np.apellidos, ' ', 2), ' ',  2) 
				ELSE      SUBSTRING_INDEX(SUBSTRING_INDEX(np.apellidos, ' ', 1), ' ', -1) 
				END as primer_apellido
				FROM nom_movimientos_nomina n
				JOIN nompersonal np ON (n.ficha = np.ficha AND n.cedula = np.cedula)
				JOIN nom_nomina_netos nnn ON (np.ficha = nnn.ficha AND nnn.codnom = '".$codnom."' AND nnn.tipnom = '".$codtip."' )
				WHERE  n.codnom = '".$codnom."' AND n.tipnom = '".$codtip."'
				GROUP BY n.ficha
				ORDER  BY n.ficha";
		}
			/*$sql2= "SELECT np.ficha, np.suesal as salario, np.hora_base as base,
				CONCAT_WS(' ', np.nombres, np.apellidos) as nombre,
				SUBSTRING_INDEX(SUBSTRING_INDEX(np.nombres, ' ', 1), ' ', -1) as primer_nombre,
				CASE WHEN np.apellidos LIKE 'De %' THEN 
				SUBSTRING_INDEX(SUBSTRING_INDEX(np.apellidos, ' ', 2), ' ',  2) 
				ELSE      SUBSTRING_INDEX(SUBSTRING_INDEX(np.apellidos, ' ', 1), ' ', -1) 
				END as primer_apellido
				FROM   nompersonal np
				WHERE  np.tipnom=".$codtip."
				AND    np.ficha IN (SELECT DISTINCT n.ficha FROM nom_movimientos_nomina n WHERE n.codnom=".$codnom." AND n.tipnom=np.tipnom)
				AND    (np.estado = 'REGULAR' OR np.estado = 'Activo')
				ORDER  BY np.ficha";
			*/
		$res2=$conexion->query($sql2);

		$ini=$i; $enc=false;
		while($row2=$res2->fetch_array())
		{	$enc=true;
			$ficha = $row2['ficha'];
			$primer_nombre = utf8_encode($row2['primer_nombre']);
			$apellido   = utf8_encode($row2['primer_apellido']);
			$trabajador = utf8_encode($row2['primer_nombre']).' '.utf8_encode($row2['primer_apellido']); // $row2['nombre']

			//$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':Q'.$i)->getFont()->setName('Calibri');
			$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':Y'.$i)->getFont()->setSize(6);

			$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, $row2['ficha']);
			$objPHPExcel->getActiveSheet()->setCellValue('B'.$i, $trabajador);
			$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':Y'.$i)->applyFromArray(allBordersThin());

			$sql3 = "SELECT
					COALESCE((SELECT SUM(n.valor) AS monto FROM nom_movimientos_nomina n 
					WHERE  n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon in (100)),0) as regular,
					COALESCE((SELECT SUM(n.valor) AS monto FROM nom_movimientos_nomina n 
					WHERE  n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon=163), 0) as unopunto25,
					COALESCE((SELECT SUM(n.valor) AS monto FROM nom_movimientos_nomina n 
					WHERE  n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon in (164,165)), 0) as unopunto5,
					COALESCE((SELECT SUM(n.valor) AS monto FROM nom_movimientos_nomina n 
					WHERE  n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon IN (166,167)), 0) as domingonac,
					COALESCE((SELECT SUM(n.valor) AS monto FROM nom_movimientos_nomina n 
					WHERE  n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon=168), 0) as dospunto18,
					COALESCE((SELECT SUM(n.valor) AS monto FROM nom_movimientos_nomina n 
					WHERE  n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon=172), 0) as dospunto625,
					COALESCE((SELECT SUM(n.valor) AS monto FROM nom_movimientos_nomina n 
					WHERE  n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon in (110,112,113,114,120,121,123,124,125,126,127,128,129,130,131,132,133,134,135,136,137,138,139,142,143,144,149,150,151,152,153,173)), 0) as lasdemas,
					COALESCE((SELECT SUM(n.valor) AS monto FROM nom_movimientos_nomina n 
					WHERE  n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon in (159,160,161,162)), 0) as lluvia,
					COALESCE((SELECT SUM(n.valor) AS monto FROM nom_movimientos_nomina n 
					WHERE  n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon in (174,199,203)), 0) as nopaga,
					COALESCE((SELECT SUM(n.valor) AS monto FROM nom_movimientos_nomina n 
					WHERE  n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon in (170,171)), 0) as ausencia,
					COALESCE((SELECT SUM(n.valor) AS monto FROM nom_movimientos_nomina n 
					WHERE  n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon=169), 0) as cm,
					COALESCE((SELECT SUM(n.monto) AS monto FROM nom_movimientos_nomina n 
					WHERE  n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon in (115,141,147,145,157,158,159,160,161,162)), 0) as otros,
					COALESCE((SELECT SUM(n.monto) AS monto FROM nom_movimientos_nomina n 
					WHERE  n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon in (174,199,203)), 0) as nopaga_monto,
					COALESCE((SELECT SUM(n.monto) AS monto FROM nom_movimientos_nomina n 
					WHERE  n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon=146), 0) as com14,
					COALESCE((SELECT SUM(n.monto) AS monto FROM nom_movimientos_nomina n 
					WHERE  n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon in (163,164,165,166,167,168,172,173)), 0) as sobretiempo,

					COALESCE((SELECT SUM(n.monto) AS monto FROM nom_movimientos_nomina n 
					WHERE  n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon=200), 0) as sss,
					COALESCE((SELECT SUM(n.monto) AS monto FROM nom_movimientos_nomina n 
					WHERE  n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon=201), 0) as sse,
					COALESCE((SELECT SUM(n.monto) AS monto FROM nom_movimientos_nomina n 
					WHERE  n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon in (202,208,215)), 0) as isr,
					COALESCE((SELECT SUM(n.monto) AS monto FROM nom_movimientos_nomina n 
					WHERE  n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND ((n.codcon BETWEEN 500 AND 599) OR (n.codcon BETWEEN 216 AND 225) OR (n.codcon in (204,207)))), 0) as descuentos";
			$res3 = $conexion->query($sql3);
			$neto=0;
			if($row3=$res3->fetch_array())
			{
				$MONTO_EXTRA_SALARIO = $MONTO_DOMINGO_SALARIO = 0;
				$MONTO_GR_EXTRA_SALARIO = $MONTO_GR_DOMINGO_SALARIO = 0;
				$AJUSTE = 0;

				

				$regular = 0;
				$uno25 = 0;
				$uno5 = 0;
				$uno75 = 0;
				$lasdemas = 0;
				$sumaDeHorasExt = 0;

				$regular =  $row3['nopaga_monto'];
				$regular2 = ($row2['salario']/2)/$row2['base'];
				$regular3 = ($row2['base'])-$row3['cm']-$row3['nopaga'];
				$regular4 = ($row2['salario']/2) - $regular;
				$uno25 = $regular2 * $row3['unopunto25'] * 1.25;
				$uno5 = $regular2 * $row3['unopunto5'] * 1.5;
				$uno75 = $regular2 * $row3['unopunto75'] * 1.75;
				$lasdemas = $regular2 * $row3['lasdemas'] * 2.625;

				$sumaDeHorasExt = $uno25 + $uno5 + $uno75 + $lasdemas;

				$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':O'.$i)->getAlignment()
											->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$objPHPExcel->getActiveSheet()->setCellValue('C'.$i, $regular2);
				$objPHPExcel->getActiveSheet()->setCellValue('D'.$i, $regular3);
				$objPHPExcel->getActiveSheet()->setCellValue('E'.$i, $row3['unopunto25']);
				$objPHPExcel->getActiveSheet()->setCellValue('F'.$i, $row3['unopunto5']);
				$objPHPExcel->getActiveSheet()->setCellValue('G'.$i, $row3['domingonac']);
				$objPHPExcel->getActiveSheet()->setCellValue('H'.$i, $row3['dospunto18']);
				$objPHPExcel->getActiveSheet()->setCellValue('I'.$i, $row3['dospunto625']);
				$objPHPExcel->getActiveSheet()->setCellValue('J'.$i, $row3['lasdemas']);
				$objPHPExcel->getActiveSheet()->setCellValue('K'.$i, $row3['lluvia']);
				$objPHPExcel->getActiveSheet()->setCellValue('L'.$i, $row3['nopaga']);
				$objPHPExcel->getActiveSheet()->setCellValue('M'.$i, $row3['ausencia']);
				$objPHPExcel->getActiveSheet()->setCellValue('N'.$i, $row3['cm']);
				$objPHPExcel->getActiveSheet()->setCellValue('O'.$i, $row3['otros']);
				$objPHPExcel->getActiveSheet()->setCellValue('P'.$i, $regular4);	
				$objPHPExcel->getActiveSheet()->setCellValue('Q'.$i, $row3['com14']);
				$objPHPExcel->getActiveSheet()->setCellValue('R'.$i, $row3['sobretiempo']);
				//$salBruto = $regular + $row3['com14'] + $row3['sobretiempo'] + $row3['otrosingresos'];
				$salBruto = $regular4 + $row3['com14'] + $row3['sobretiempo'] + $row3['otros'];
				$objPHPExcel->getActiveSheet()->setCellValue('S'.$i, $salBruto);
				//DEDUCCIONES
				$objPHPExcel->getActiveSheet()->setCellValue('T'.$i, $row3['sss']);
				$objPHPExcel->getActiveSheet()->setCellValue('U'.$i, $row3['sse']);
				$objPHPExcel->getActiveSheet()->setCellValue('V'.$i, $row3['isr']);
				$objPHPExcel->getActiveSheet()->setCellValue('W'.$i, $row3['descuentos']);
				$totalDeducciones = 0;
				$totalDeducciones = $row3['sss'] + $row3['sse'] + $row3['isr'] + $row3['descuentos'];
				$objPHPExcel->getActiveSheet()->setCellValue('X'.$i, $totalDeducciones);
				$objPHPExcel->getActiveSheet()->setCellValue('Y'.$i, (($salBruto) - $totalDeducciones));
			
				//$neto2 = $row3['salario'] + $row3['vacaciones']+ $row3['reembolso']+ $row3['comision']+ $row3['bono'] + $HORAS_EXTRA_SALARIO - $row3['seguro_social'] - $row3['seguro_educativo'] - $row3['tardanza'] - $row3['ausencia']- $row3['isr'] - $row3['isr_gastos'] - $row3['descuentos_varios'] - $row3['descuentos'] - $row3['cxc']- $AJUSTE;


				$neto = "='".$dia_fin." ".$mes_letras."'!S".$i; 

				$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':Y'.$i)->getNumberFormat()
							->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_ACCOUNTING);
			}

			$trabajadores[] = array('nombre'=>$trabajador, 'primer_nombre'=> $primer_nombre, 'apellido'=>$apellido, 'neto'=> $neto, 'neto2'=>$neto2);

			$i++;
		}

		
		if($enc)
		{
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, 'Totales ' );

			$objPHPExcel->getActiveSheet()->setCellValue('C'.$i, "=SUM(C".$ini.":C".($i-1).")");	
			$total_salarios				  	.= "+C".$i;
			$objPHPExcel->getActiveSheet()->setCellValue('D'.$i, "=SUM(D".$ini.":D".($i-1).")");	
			$total_comision				  	.= "+D".$i;
			$objPHPExcel->getActiveSheet()->setCellValue('E'.$i, "=SUM(E".$ini.":E".($i-1).")");	
			$total_extra_salario 		  	.= "+E".$i;
			$objPHPExcel->getActiveSheet()->setCellValue('F'.$i, "=SUM(F".$ini.":F".($i-1).")");	
			$total_uso_auto	 			  	.= "+F".$i;
			$objPHPExcel->getActiveSheet()->setCellValue('G'.$i, "=SUM(G".$ini.":G".($i-1).")");	
			$total_reembolso			  	.= "+G".$i;
			$objPHPExcel->getActiveSheet()->setCellValue('H'.$i, "=SUM(H".$ini.":H".($i-1).")");	
			$total_bono				  		.= "+H".$i;
			$objPHPExcel->getActiveSheet()->setCellValue('I'.$i, "=SUM(I".$ini.":I".($i-1).")");	
			$total_tardanza   			  	.= "+I".$i;
			$objPHPExcel->getActiveSheet()->setCellValue('J'.$i, "=SUM(J".$ini.":J".($i-1).")");	
			$total_ausencia 				.= "+J".$i;
			$objPHPExcel->getActiveSheet()->setCellValue('K'.$i, "=SUM(K".$ini.":K".($i-1).")");	
			$total_salario   				.= "+K".$i;
			$objPHPExcel->getActiveSheet()->setCellValue('L'.$i, "=SUM(L".$ini.":L".($i-1).")");	
			$total_segurosocial_salario  	.= "+L".$i;
			$objPHPExcel->getActiveSheet()->setCellValue('M'.$i, "=SUM(M".$ini.":M".($i-1).")");	
			$total_seguro_educativo 		.= "+M".$i;
			$objPHPExcel->getActiveSheet()->setCellValue('N'.$i, "=SUM(N".$ini.":N".($i-1).")");	
			$total_isr_salario	 		  	.= "+N".$i;
			$objPHPExcel->getActiveSheet()->setCellValue('O'.$i, "=SUM(O".$ini.":O".($i-1).")");	
			$total_isr_gastos				.= "+O".$i;
			$objPHPExcel->getActiveSheet()->setCellValue('P'.$i, "=SUM(P".$ini.":P".($i-1).")");	
			$Total_Desc_Ley  			  	.= "+P".$i;
			$objPHPExcel->getActiveSheet()->setCellValue('Q'.$i, "=SUM(Q".$ini.":Q".($i-1).")");	
			$total_desc_varios 				.= "+Q".$i;
			$objPHPExcel->getActiveSheet()->setCellValue('R'.$i, "=SUM(R".$ini.":R".($i-1).")");	
			$total_descuentos			  	.= "+R".$i;
			$objPHPExcel->getActiveSheet()->setCellValue('S'.$i, "=SUM(S".$ini.":S".($i-1).")");
			$total_neto 				  	.= "+S".$i;

			$objPHPExcel->getActiveSheet()->setCellValue('T'.$i, "=SUM(T".$ini.":T".($i-1).")");
			$objPHPExcel->getActiveSheet()->setCellValue('U'.$i, "=SUM(U".$ini.":U".($i-1).")");
			$objPHPExcel->getActiveSheet()->setCellValue('V'.$i, "=SUM(V".$ini.":V".($i-1).")");
			$objPHPExcel->getActiveSheet()->setCellValue('W'.$i, "=SUM(W".$ini.":W".($i-1).")");
			$objPHPExcel->getActiveSheet()->setCellValue('X'.$i, "=SUM(X".$ini.":X".($i-1).")");
			$objPHPExcel->getActiveSheet()->setCellValue('Y'.$i, "=SUM(Y".$ini.":Y".($i-1).")");


			$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':Y'.$i)->getFont()->setSize(6);
			$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':Y'.$i)->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':Y'.$i)->getNumberFormat()
										->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_ACCOUNTING);

			$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getFont()->setSize(6);
			$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':B'.$i);
			$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':B'.$i)->getBorders()->getBottom()
						->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
			if($nivel==1)
			{
				$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':Y'.$i)->applyFromArray(allBordersMedium());
			}
			else
			{
				$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':Y'.$i)->applyFromArray(allBordersThin());
			}

			$i++;
		
			$i++;	
			$nivel++;
		}
	//}

	$i++;
	
//	$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':F'.$i)->applyFromArray(allBordersThin());
//	$objPHPExcel->getActiveSheet()->getStyle('F'.$i)->getNumberFormat()
//								->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(6);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(17);
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(9);
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(9);
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(9);
	$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(9);
	$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(9);
	$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(9);
	$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(9);
	$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(9);
	$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(9);
	$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(9);
	$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(9);
	$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(9);
	$objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(9);
	$objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(9);
	$objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(9);
	$objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(9);
	$objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension('V')->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension('W')->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension('X')->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension('Y')->setWidth(10);
	$objPHPExcel->getActiveSheet()->setSelectedCells('F'.($i+20));

	$objPHPExcel->getActiveSheet()->getRowDimension('8')->setRowHeight(18);

	//===========================================================================
	$objPHPExcel->setActiveSheetIndex(0); 
	$objPHPExcel->getActiveSheet()->setSelectedCells('I30');



	$NOMINA = str_replace(' ', '', $NOMINA);
	$filename = "Horizontal_".$NOMINA."_Del_".fecha($desde).'_Hasta_'.fecha($hasta);

	// Redirect output to a client’s web browser (Excel5)
	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename="'.$filename.'_itesa.xls"');
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
