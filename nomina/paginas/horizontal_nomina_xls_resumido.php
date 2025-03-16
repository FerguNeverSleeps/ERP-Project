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

$empresa = $fila['empresa'];
$objPHPExcel->getActiveSheet()
            ->setCellValue('A2', strtoupper($empresa) )
            ->setCellValue('A3', 'PLANILLA QUINCENAL')
            ->setCellValue('A4', 'DEL '. $dia_ini .' AL '. $dia_fin .' de '. $mes_letras .' '. $anio );

//$objPHPExcel->getActiveSheet()->getStyle('A2:A4')->getFont()->setName('Calibri');
$objPHPExcel->getActiveSheet()->getStyle('A2:A4')->getFont()->setSize(14);
$objPHPExcel->getActiveSheet()->getStyle('A2:A4')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('A2:A4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->mergeCells('A2:Q2');
$objPHPExcel->getActiveSheet()->mergeCells('A3:Q3');
$objPHPExcel->getActiveSheet()->mergeCells('A4:Q4');

//$objPHPExcel->getActiveSheet()->getStyle('C2:D5')->applyFromArray(allBordersThin());
//$objPHPExcel->getActiveSheet()->getStyle('C2:D5')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$objPHPExcel->getActiveSheet()
            ->setCellValue('A6', 'SALARIOS')
            ->setCellValue('K7', 'TOTAL')
            ->setCellValue('Q7', 'ACREEDOR')
            ->setCellValue('R7', 'SALARIO')
            ->setCellValue('A8', 'No.')
            ->setCellValue('B8', 'NOMBRE')
            ->setCellValue('C8', 'SALARIO')
            ->setCellValue('D8', 'COMISIÓN')
            ->setCellValue('F8', 'USO DE AUTO')
            ->setCellValue('E8', 'H. EXTRA')
            ->setCellValue('G8', 'GASTOS REP.')
            ->setCellValue('H8', 'BONIFICACIÓN')

            ->setCellValue('I8', 'TARDANZA')
            ->setCellValue('J8', 'AUSENCIA')
            ->setCellValue('K8', 'SALARIO')
            ->setCellValue('L8', 'SEGURO SOCIAL')
            ->setCellValue('M8', 'S.EDUCATIVO')
            ->setCellValue('N8', 'IMP. s. RENTA')
            ->setCellValue('O8', 'IMP. s. RENTA GR')
            ->setCellValue('P8', 'DESC LEY')
            ->setCellValue('Q8', 'POR PAGAR')
            ->setCellValue('R8', 'A PAGAR');

cellColor('C7:K8', 'ffcc00');
cellColor('L7:R8', '92d050');

//$objPHPExcel->getActiveSheet()->getStyle('A6:Q8')->getFont()->setName('Calibri');
$objPHPExcel->getActiveSheet()->getStyle('A6:K8')->getFont()->setSize(12);
$objPHPExcel->getActiveSheet()->getStyle('A6:K8')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('A6:K8')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$objPHPExcel->getActiveSheet()->mergeCells('A6:K6');
$objPHPExcel->getActiveSheet()->getStyle('A6:R8')->applyFromArray(allBordersThin());

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
$total_salarios=$total_comision=$total_combustible=$total_reembolso=$total_uso_auto=$total_bono="=";
$total_extra_salario=$total_extra_gastosrep=$total_vacaciones=$total_cxc="=";
$total_segurosocial_salario=$total_tardanza=$total_seguro_educativo="=";
$total_isr_gastos=$total_isr_salario=$total_ausencia=$total_descuentos=$total_neto="=";
$total_salario=$MesXIII=$Total_Desc_Ley=$SeguroSocialMesXIII="=";
$check_deducciones = false;//
$check_asignaciones = false;
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
			WHERE  np.codnivel1=".$codorg." AND np.tipnom=".$codtip."
			AND    np.ficha IN (SELECT DISTINCT n.ficha FROM nom_movimientos_nomina n WHERE n.codnom=".$codnom." AND n.tipnom=np.tipnom)
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
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':R'.$i)->getFont()->setSize(12);

		$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, $row2['ficha']);
		$objPHPExcel->getActiveSheet()->setCellValue('B'.$i, $trabajador);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':R'.$i)->applyFromArray(allBordersThin());

		$sql3 = "SELECT
				 COALESCE((SELECT n.monto FROM nom_movimientos_nomina n 
  				  WHERE  n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon=100),0) as salario,
				 COALESCE((SELECT SUM(n.monto) AS monto FROM nom_movimientos_nomina n 
  				  WHERE  n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon in (141,157)), 0) as comision,
				 COALESCE((SELECT n.monto FROM nom_movimientos_nomina n 
  				  WHERE  n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon=199), 0) as tardanza,			 
				 COALESCE((SELECT n.monto FROM nom_movimientos_nomina n 
  				  WHERE  n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon=158), 0) as bono,		 
				 COALESCE((SELECT n.monto FROM nom_movimientos_nomina n 
  				  WHERE  n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon=200), 0) as seguro_social,
				COALESCE((SELECT  n.monto FROM nom_movimientos_nomina n 
  				  WHERE  n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon=201), 0) as seguro_educativo,
				COALESCE((SELECT  n.monto FROM nom_movimientos_nomina n 
  				  WHERE  n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon=208), 0) as seguro_social_xiii,
				COALESCE((SELECT  n.monto FROM nom_movimientos_nomina n 
  				  WHERE  n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon=145), 0) as reembolso,
				COALESCE((SELECT  SUM(n.monto) AS monto FROM nom_movimientos_nomina n 
  				  WHERE  n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon in (147,156)), 0) as uso_auto,
				COALESCE((SELECT  n.monto FROM nom_movimientos_nomina n 
  				  WHERE  n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon=203), 0) as ausencia,
 				COALESCE((SELECT  SUM(n.monto) FROM nom_movimientos_nomina n 
  				  WHERE  n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha."
  				  AND    n.codcon IN (202, 601, 605)), 0) as isr,
				COALESCE((SELECT  SUM(n.monto) FROM nom_movimientos_nomina n 
  				  WHERE  n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha."
  				  AND    n.codcon IN (207, 208, 606, 607)), 0) as isr_gastos,
				(SELECT  COALESCE(SUM(n.monto), 0) FROM nom_movimientos_nomina n 
  				  WHERE  n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND (n.codcon BETWEEN 500 AND 507 
  				OR n.codcon BETWEEN 508 AND 599))  as descuentos,
				(SELECT  COALESCE(SUM(n.monto), 0) FROM nom_movimientos_nomina n 
  				  WHERE  n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon=508)  as cxc,
				(SELECT  COALESCE(SUM(n.monto), 0) FROM nom_movimientos_nomina n 
  				  WHERE  n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon=114)  as vacaciones";
				 //echo $sql3;exit;
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
					AND (  n.codcon IN (106, 108, 142, 143, 153, 152)
					 OR    n.codcon IN (111,116,123, 124, 125, 126, 144, 149, 150, 151)
					 OR    n.codcon IN (107, 109, 152, 153, 120, 121, 127, 122,112, 113, 127, 128, 129, 130, 131, 132,133, 134, 135, 136, 138, 139) )";
			$res5 =$conexion->query($sql5);
			if($row5=$res5->fetch_array()){ $MONTO_EXTRA_SALARIO = $row5['total_extra_salario']; }

			$sql5 = "SELECT COALESCE(SUM(n.monto), 0) AS total_domingo_salario  
					 FROM   nom_movimientos_nomina n 
					 WHERE  n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha."
					 AND (  n.codcon IN (112, 113, 127, 128, 129, 130, 131, 132)
					  OR    n.codcon IN (133, 134, 135, 136, 138, 139) )";
			$res5 =$conexion->query($sql5);


			// Calcular AJUSTE
			$sql5= "SELECT COALESCE(SUM(monto), 0) AS ajuste
					FROM   nom_movimientos_nomina n 
					WHERE  n.codcon=604
					AND    n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha;

			$res5 = $conexion->query($sql5);
			if($row5 = $res5->fetch_array() ){ $AJUSTE = $row5['ajuste']; }

			//$impuesto = ($row3['isr_gastos'] >0) ? $row3['isr'] - $row3['isr_gastos'] : $row3['isr'];

			$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':O'.$i)->getAlignment()
										  ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->setCellValue('C'.$i, $row3['salario']);
			$objPHPExcel->getActiveSheet()->setCellValue('D'.$i, $row3['comision']);
			

			//======================================================================================
			// HORAS EXTRA SALARIO
			if( ($MONTO_EXTRA_SALARIO + $MONTO_DOMINGO_SALARIO) <= 0 && $row3['horas_extras'] > 0 )
			{
				$objPHPExcel->getActiveSheet()->setCellValue('E'.$i, $row3['horas_extras']);
				$HORAS_EXTRA_SALARIO = $row3['horas_extras'];
			}
			else
			{
				$objPHPExcel->getActiveSheet()->setCellValue('E'.$i, $MONTO_EXTRA_SALARIO);
				$HORAS_EXTRA_SALARIO = $MONTO_EXTRA_SALARIO + $MONTO_DOMINGO_SALARIO;			
			}


			// $objPHPExcel->getActiveSheet()->setCellValue('F'.$i, "=(C".$i."+F".$i.") * 9.75% ");
			// $objPHPExcel->getActiveSheet()->setCellValue('L'.$i, "=(E".$i."+G".$i.") * 9.75% ");
			if ($row3['mesxiii']!=0) {
				$mes13=$row3['mesxiii'];
				# code...
			}
			elseif ($row3['mesxiii2']!=0) {
				$mes13=$row3['mesxiii2'];
				# code...
			}
			$salario_trabajador=$row3['salario']+$row3['uso_auto']+$row3['comision']+$row3['bono']+$HORAS_EXTRA_SALARIO+$row3['reembolso']+$row3['vacaciones']-$row3['tardanza']-$row3['ausencia'];
			$objPHPExcel->getActiveSheet()->setCellValue('F'.$i, $row3['uso_auto']);
			$objPHPExcel->getActiveSheet()->setCellValue('G'.$i, $row3['reembolso']);
			$objPHPExcel->getActiveSheet()->setCellValue('H'.$i, $row3['bono']);
			$objPHPExcel->getActiveSheet()->setCellValue('I'.$i, $row3['tardanza']);
			$objPHPExcel->getActiveSheet()->setCellValue('J'.$i, $row3['ausencia']);
			$objPHPExcel->getActiveSheet()->setCellValue('K'.$i, $salario_trabajador);	
			$objPHPExcel->getActiveSheet()->setCellValue('L'.$i, $row3['seguro_social']);
			$objPHPExcel->getActiveSheet()->setCellValue('M'.$i, $row3['seguro_educativo']);
			$objPHPExcel->getActiveSheet()->setCellValue('N'.$i, $row3['isr']);
			$objPHPExcel->getActiveSheet()->setCellValue('O'.$i, $row3['isr_gastos']);
			$objPHPExcel->getActiveSheet()->setCellValue('P'.$i, $row3['descuentos']);			

			
			$neto2 = $row3['salario'] + $row3['vacaciones']+ $row3['reembolso']+ $row3['comision']+ $row3['bono'] + $HORAS_EXTRA_SALARIO 
				   - $row3['seguro_social'] - $row3['seguro_educativo'] - $row3['tardanza'] - $row3['ausencia']
				   - $row3['isr'] - $row3['isr_gastos'] - $row3['descuentos'] - $row3['cxc']- $AJUSTE;

			//$objPHPExcel->getActiveSheet()->setCellValue('Q'.$i, $neto);
			$objPHPExcel->getActiveSheet()->setCellValue('R'.$i, "=C$i+D$i+E$i+F$i+G$i+H$i-I$i-J$i-P$i-Q$i-".$AJUSTE);
			$objPHPExcel->getActiveSheet()->setCellValue('P'.$i, "=L$i+M$i+N$i+O$i");
			// =+C38+F38+G38+H38+I38+J38-K38-L38-M38-N38-O38-P38
			$neto = "='".$dia_fin." ".$mes_letras."'!R".$i; //$objPHPExcel->getActiveSheet()->getCell('Q'.$i)->getValue();
			

			
			

			$clave_isr 		= '-';			
			$ingreso_regular= $salario_trabajador;
			$otros_ingresos = $row3['vacaciones']+ $row3['reembolso']+ $row3['comision']+ $row3['bono'] + $HORAS_EXTRA_SALARIO;
			$total_Ingresos = $ingreso_regular+$otros_ingresos;

			$renta 			= '';
			$impuesto 		= $row3['isr'];
			$seguro_social = $row3['seguro_social']+$row3['seguro_educativo'];
			$total_retener =  $row3['seguro_social'] + $row3['seguro_educativo'] + $row3['tardanza'] + $row3['ausencia'] + $row3['isr'] +$row3['isr_gastos'] + $row3['descuentos'] + $row3['cxc']- $AJUSTE;

			
			$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':R'.$i)->getNumberFormat()
						->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_ACCOUNTING);
		}

		//$horas_extras[] = $HORAS_EXTRA_SALARIO;

		//SELECT codcon, descrip, tipcon FROM nomconceptos ORDER BY tipcon, codcon, descrip
		
		if(!$check_deducciones){
			$check_deducciones = true;
			$sql6 = "SELECT COALESCE( SUM( n.monto ) , 0 ) AS total_deduccion, c.codcon, c.descrip, c.tipcon
						FROM nom_movimientos_nomina AS n, nomconceptos AS c
						WHERE n.codcon = c.codcon
						AND n.codnom =".$codnom."
						AND n.tipnom =".$codtip."
						AND n.ficha =".$ficha."
						AND c.tipcon = 'D'
						GROUP BY c.codcon
						ORDER BY n.codcon ASC ";
			$res6 =$conexion->query($sql6);
				
			
			while($row6=$res6->fetch_array())
			{					
				$deduciones[] 	= array('cod' => $row6['codcon'],'descripcion'=>$row6['descrip'],'monto'=>$row6['total_deduccion']);
			}
		}

		if(!$check_asignaciones){
			$check_asignaciones = true;
			$sql7 = "SELECT COALESCE( SUM( n.monto ) , 0 ) AS total_deduccion, c.codcon, c.descrip, c.tipcon
						FROM nom_movimientos_nomina AS n, nomconceptos AS c
						WHERE n.codcon = c.codcon
						AND n.codnom =".$codnom."
						AND n.tipnom =".$codtip."
						AND n.ficha =".$ficha."
						AND c.tipcon = 'A'
						GROUP BY c.codcon
						ORDER BY n.codcon ASC ";
			$res7 =$conexion->query($sql7);
				
			
			while($row7=$res7->fetch_array())
			{					
				if($row7['codcon']==100 || $row7['codcon']==145){
					$row7['codcon'] =$row7['valor'];
				}

				$asignaciones[] 	= array('cod' => $row7['codcon'],'descripcion'=>$row7['descrip'],'monto'=>$row7['total_deduccion']);
			}
		}
		

		$trabajadores[] = array('nombre'=>$trabajador, 'primer_nombre'=> $primer_nombre, 'apellido'=>$apellido, 'neto'=> $neto,'ingreso_regular'=>$ingreso_regular,'otros_ingresos'=>$otros_ingresos,'total_Ingresos'=>$total_Ingresos, 'neto2'=>$neto2,'seguro_social'=>$seguro_social,'total_retener'=>$total_retener,'impuesto'=>$impuesto);

		$i++;
	}

	if($enc)
	{
		$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, 'Total de Salarios '. $row['descrip'] );

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
	$total_descuentos 				.= "+Q".$i;
	$objPHPExcel->getActiveSheet()->setCellValue('R'.$i, "=SUM(R".$ini.":R".($i-1).")");	
	$total_neto 				  	.= "+R".$i;

	//$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':Q'.$i)->getFont()->setName('Calibri');
		$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':R'.$i)->getFont()->setSize(12);
		$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':R'.$i)->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':R'.$i)->getNumberFormat()
									  ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_ACCOUNTING);

		//$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getFont()->setName('Calibri');
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getFont()->setSize(8);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':B'.$i);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':B'.$i)->getBorders()->getBottom()
	        		->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
		if($nivel==1)
		{
			$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':R'.$i)->applyFromArray(allBordersMedium());
		}
		else
		{
			$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':R'.$i)->applyFromArray(allBordersThin());
		}

		$i++;
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':R'.$i)->applyFromArray(allBordersMedium());	
		$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':R'.$i);
		$i++;	
		$nivel++;
	}
}

//$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':Q'.$i);
$i++;
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':R'.$i)->applyFromArray(allBordersMedium());	
//$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':Q'.$i)->getFont()->setName('Calibri');
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':R'.$i)->getFont()->setSize(12);
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':R'.$i)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()
			->setCellValue('B'.$i, 'Total')
			->setCellValue('C'.$i, $total_salarios)
			->setCellValue('D'.$i, $total_comision)
			->setCellValue('E'.$i, $total_extra_salario)
			->setCellValue('F'.$i, $total_uso_auto)
			->setCellValue('G'.$i, $total_reembolso)
			->setCellValue('H'.$i, $total_bono)
			->setCellValue('I'.$i, $total_tardanza)
			->setCellValue('J'.$i, $total_ausencia)
			->setCellValue('K'.$i, $total_salario)
			->setCellValue('L'.$i, $total_segurosocial_salario)
			->setCellValue('M'.$i, $total_seguro_educativo)
			->setCellValue('N'.$i, $total_isr_salario)
			->setCellValue('O'.$i, $total_isr_gastos)
			->setCellValue('P'.$i, $Total_Desc_Ley)
			->setCellValue('Q'.$i, $total_descuentos)
			->setCellValue('R'.$i, $total_neto);
		$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':R'.$i)->getNumberFormat()
									  ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_ACCOUNTING);

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

$i=$i+2;
$objPHPExcel->getActiveSheet()
			->setCellValue('A'.$i, 'Asiento de Planilla')
			->setCellValue('F'.$i, 'Asiento de Cuota Patronal');

$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':K'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':O'.$i)->getFont()->setName('Calibri');
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':K'.$i)->getFont()->setSize(14);
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':K'.$i)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':D'.$i);
$objPHPExcel->getActiveSheet()->mergeCells('F'.$i.':J'.$i);

$i++;

$objPHPExcel->getActiveSheet()
			->setCellValue('A'.$i, 'Código')
			->setCellValue('B'.$i, 'Nombre de la cuenta')
			->setCellValue('C'.$i, 'Débito')
			->setCellValue('D'.$i, 'Crédito')
			->setCellValue('F'.$i, 'Código')
			->setCellValue('G'.$i, 'Nombre de la cuenta')
			->setCellValue('I'.$i, 'Débito')
			->setCellValue('J'.$i, 'Crédito');

$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':J'.$i)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':K'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':D'.$i)->applyFromArray(allBordersMedium());
$objPHPExcel->getActiveSheet()->getStyle('F'.$i.':J'.$i)->applyFromArray(allBordersMedium());

$i++; $k=$i;
$objPHPExcel->getActiveSheet()
			->setCellValue('B'.$i, 'Gastos de Salario Regular')
			->setCellValue('C'.$i, "=C".$j ."-H".$j."-I".$j )
			->setCellValue('G'.$i, 'Gasto de Seg. Social')
			->setCellValue('I'.$i++, "=C".$k."*0.1225")
			->setCellValue('B'.$i, 'Gastos de Comisión')
			->setCellValue('C'.$i, "=D".$j )
			->setCellValue('G'.$i, 'Gasto de Seg. Educ.')
			->setCellValue('I'.$i++, "=C".$k."*0.015")
			->setCellValue('B'.$i, 'Extra de Salario')
			->setCellValue('C'.$i, "=E".$j )
			->setCellValue('G'.$i, 'Gasto de Riesgos Prof.')
			->setCellValue('I'.$i++, "=C".$k."*0.021" )
			->setCellValue('B'.$i, 'Uso de Auto')
			->setCellValue('C'.$i, "=F".$j )
			->setCellValue('G'.$i, 'Caja de Seg. Social * Pagar')
			->setCellValue('J'.$i++, "=SUM(I".($i-4).":I".($i-1).")" )
			->setCellValue('B'.$i, 'Gastos de Representación')
			->setCellValue('C'.$i++, "=G".$j )
			->setCellValue('B'.$i, 'Bonificación')
			->setCellValue('C'.$i++, "=H".$j )
			->setCellValue('B'.$i, 'Seguro Social * Pagar')
			->setCellValue('D'.$i++, "=L".$j)
			->setCellValue('B'.$i, 'Seguro Educ. por Pagar')
			->setCellValue('D'.$i++, "=M".$j )
			->setCellValue('B'.$i, 'Imp. S/ R empleado * pagar')
			->setCellValue('D'.$i++, "=N".$j)
			->setCellValue('B'.$i, 'Imp. S/ R GR * pagar')
			->setCellValue('D'.$i++, "=O".$j)
			->setCellValue('B'.$i, 'Desc. Empleado * Pagar')
			->setCellValue('D'.$i++, "=Q".$j )
			->setCellValue('B'.$i, 'Salario por Pagar')
			->setCellValue('D'.$i++, "=R".$j)
			->setCellValue('C'.$i, "=SUM(C".$k.":C".($i-1).")")
			->setCellValue('D'.$i, "=SUM(D".$k.":D".($i-1).")")
			->setCellValue('I'.$i, "=SUM(I".$k.":I".($i-1).")")
			->setCellValue('J'.$i, "=SUM(J".$k.":J".($i-1).")");

$objPHPExcel->getActiveSheet()->getStyle('A'.$k.':D'.($i-1))->applyFromArray(allBordersThin());	
$objPHPExcel->getActiveSheet()->getStyle('F'.$k.':J'.($i-1))->applyFromArray(allBordersThin());	

$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':D'.$i)->applyFromArray(allBordersMedium());
$objPHPExcel->getActiveSheet()->getStyle('F'.$i.':J'.$i)->applyFromArray(allBordersMedium());

$objPHPExcel->getActiveSheet()->getStyle('C'.$k.':D'.$i)->getNumberFormat()
							  ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_ACCOUNTING);
$objPHPExcel->getActiveSheet()->getStyle('F'.$k.':J'.$i)->getNumberFormat()
							  ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_ACCOUNTING);


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



$objPHPExcel->getActiveSheet()->setTitle($dia_fin.' '.$mes_letras);

//===========================================================================

/*$fecha_desde = explode('/', $desde);
$fecha_hasta = explode('/', $hasta);*/

$objPHPExcel->createSheet();
$objPHPExcel->setActiveSheetIndex(1); 

$objPHPExcel->getActiveSheet()->setTitle('Resumido pago');

$objPHPExcel->getActiveSheet()
			->setCellValue('B1', 'Control #.')
			->setCellValue('C1', strtoupper($empresa))
			->setCellValue('C2', 'Planilla Regular No.18')
			//->setCellValue('C1', '')
			->setCellValue('C3', strtoupper("Periodo ".$NOMINA." Del ".fecha($desde).' al '.fecha($hasta)))
			->setCellValue('J2', PHPExcel_Shared_Date::FormattedPHPToExcel($anio, $mes_numero, $dia_fin) )

			->setCellValue('B6', 'Núm.')
			->setCellValue('C6', 'Nombre')
			->setCellValue('D6', "Clave\r ISR")
			->setCellValue('E6', "Ingreso\r Regular")
			->setCellValue('F6', "Otros\r Ingresos")
			->setCellValue('G6', "Total\r Ingresos")
			->setCellValue('H6', "Impuesto\r /Renta")
			->setCellValue('I6', "Seguros\r Soc.+Edu.")
			->setCellValue('J6', "Total a \r Retener")			
			->setCellValue('k6', "Neto a\r Pagar"); 

$objPHPExcel->getActiveSheet()->getDefaultStyle()->getFont()->setName('Times New Roman');
//$objPHPExcel->getActiveSheet()->getStyle('B1:F1')->getFont()->setName('Times New Roman');
$objPHPExcel->getActiveSheet()->getStyle('C1:I1')->getFont()->setSize(16);
$objPHPExcel->getActiveSheet()->getStyle('C1:I1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->mergeCells('C1:I1');

$objPHPExcel->getActiveSheet()->getStyle('C3:I3')->getFont()->setSize(12);
$objPHPExcel->getActiveSheet()->getStyle('C3:I3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->mergeCells('C3:I3');

$objPHPExcel->getActiveSheet()->getStyle('C2:I2')->getFont()->setSize(12);
$objPHPExcel->getActiveSheet()->getStyle('C2:I2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->mergeCells('C2:I2');

cellColor('A1:k6', 'FFFFFF');



/*$objPHPExcel->getActiveSheet()->getStyle('C2:D2')->getFont()->setSize(9);
$objPHPExcel->getActiveSheet()->getStyle('C2:D2')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->mergeCells('C2:D2');*/

/*$objPHPExcel->getActiveSheet()->getStyle('F2')->getFont()->setSize(12);
$objPHPExcel->getActiveSheet()->getStyle('F2')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('F2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('F2')->applyFromArray(allBordersThin());*/





$objPHPExcel->getActiveSheet()->getStyle('J2')->getNumberFormat()
							  //->setFormatCode('dd-mmm-yy');
							  ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX15);	

$objPHPExcel->getActiveSheet()->getStyle('C3:F3')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

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

//$objPHPExcel->getActiveSheet()->mergeCells('B6:C6');

$objPHPExcel->getActiveSheet()->getStyle('B6:K6')->getFont()->setSize(10);
$objPHPExcel->getActiveSheet()->getStyle('B6:K6')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('B6:K6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('B6:K6')->getBorders()->getTop()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
$objPHPExcel->getActiveSheet()->getStyle('B6:K6')->getBorders()->getBottom()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

$objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(20);
$objPHPExcel->getActiveSheet()->getRowDimension('6')->setRowHeight(40);

//$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(false);
//$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth("5");  
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);

//$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth("10");

//$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
//$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth("10");


//$objPHPExcel->getActiveSheet()->getStyle('B6:K6')->getAlignment()->setWrapText(true);

$i=7;
$ini = 7;
$cont=1;
foreach ($trabajadores as $key => $row) 
{
    //echo $row['nombre'].' '.$row['neto'].'<br/>';
	$objPHPExcel->getActiveSheet()
				->setCellValue('B'.$i, $cont )
				->setCellValue('C'.$i, strtoupper($row['apellido'].', '.$row['primer_nombre']))
				->setCellValue('D'.$i, '')								
				->setCellValue('E'.$i, $row['ingreso_regular'])
				->setCellValue('F'.$i, $row['otros_ingresos'])
				->setCellValue('G'.$i, $row['total_Ingresos'])
				->setCellValue('H'.$i, $row['impuesto'])
				->setCellValue('I'.$i, $row['seguro_social'])
				->setCellValue('J'.$i, $row['total_retener'])
				
				->setCellValue('K'.$i, $row['neto'] );
	/*$TotalAPagar 			+=$row['neto'];
	$Totalingresoregular 	+= $row['ingreso_regular'];
	$TotalOtrosIngresos 	+= $row['otros_ingresos'];
	$TotalTotalIngreso 		+= $row['total_Ingresos'];
	$TotalImpuesto			+= $row['impuesto'];
	$TotalSeguro 			+= $row['seguro_social'];
	$TotalTotalRetener 		+= $row['total_retener'];*/

	cellColor('A'.$i.':K'.$i, 'FFFFFF');

	$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':F'.$i)->getFont()->setSize(9);

	//$objPHPExcel->getActiveSheet()->getStyle('C'.$i)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('C'.$i)->getFont()->setItalic(true);
	//$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':K'.$i)->getBorders()->getBottom()
        					      //->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);



	$objPHPExcel->getActiveSheet()->getStyle('C'.$i)->getNumberFormat()
							      ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle('D'.$i)->getNumberFormat()
							      ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle('E'.$i)->getNumberFormat()
							      ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle('F'.$i)->getNumberFormat()
							      ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle('G'.$i)->getNumberFormat()
							      ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);	
	$objPHPExcel->getActiveSheet()->getStyle('H'.$i)->getNumberFormat()
							      ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);	
	$objPHPExcel->getActiveSheet()->getStyle('I'.$i)->getNumberFormat()
							      ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);						      
	$i++;
	$cont++;
}

//cellColor('A'.$i.':K'.($i+2), 'FFFFFF');
$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':K'.$i)->getBorders()->getBottom()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$i++;
$objPHPExcel->getActiveSheet()->getStyle('C'.$i)->getFont()->setSize(10);
$objPHPExcel->getActiveSheet()->getStyle('C'.$i)->getFont()->setBold(true);
//$objPHPExcel->getActiveSheet()->mergeCells('C'.$i.':D'.$i);
$objPHPExcel->getActiveSheet()
			->setCellValue('B'.$i, 'Sub. total:')
			->setCellValue('E'.$i, "=SUM(E7:E".($i-2).")" )
			->setCellValue('F'.$i, "=SUM(F7:F".($i-2).")" )
			->setCellValue('G'.$i, "=SUM(G7:G".($i-2).")" )
			->setCellValue('H'.$i, "=SUM(H7:H".($i-2).")" )
			->setCellValue('I'.$i, "=SUM(I7:I".($i-2).")" )
			->setCellValue('J'.$i, "=SUM(J7:J".($i-2).")" )
			->setCellValue('K'.$i, "=SUM(K7:K".($i-2).")" );
$TotalAPagar=$objPHPExcel->getActiveSheet()->getCell('K'.$i)->getCalculatedValue();
	$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':K'.$i)->getBorders()->getBottom()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle('K'.$i)->getFont()->setBold(true);



			/*->setCellValue('H'.$i, "=SUM(H7:H".($i-2).")" )
			->setCellValue('I'.$i, "=SUM(I7:I".($i-2).")" )
			->setCellValue('J'.$i, "=SUM(J7:J".($i-2).")" )
			->setCellValue('K'.$i, "=SUM(K7:K".($i-2).")" );*/

//$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':K'.$i)->getBorders()->getBottom()
        					 // ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
//$objPHPExcel->getActiveSheet()->getStyle('K'.$i)->getFont()->setBold(true);

/*$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);*/

$TotalIngresoregular = $objPHPExcel->getActiveSheet()->getCell('G'.$i)->getCalculatedValue();
$TotalTotalRetener   = $objPHPExcel->getActiveSheet()->getCell('J'.$i)->getCalculatedValue();
/*TotalOtrosIngresos
TotalTotalIngreso
TotalImpuesto
TotalSeguro
TotalTotalRetener*/




			$importe=$TotalAPagar;
			/*
// indicamos todas las monedas posibles
 $monedas=array(100, 50, 20, 10, 5, 1, 0.5, 0.25, 0.10, 0.05, 0.01);
//Dolares
 
// creamos un array con la misma cantidad de monedas
// Este array contendra las monedas a devolver
$cambio=array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
 
// Recorremos todas las monedas
for($ixx=0; $ixx<count($monedas); $ixx++)
{
    // Si el importe actual, es superior a la moneda
    if($importe>=$monedas[$ixx])
    {
 
        // obtenemos cantidad de monedas
        $cambio[$ixx]=floor($importe/$monedas[$ixx]);
 
        // actualizamos el valor del importe que nos queda por didivir
        $importe=$importe-($cambio[$ixx]*$monedas[$ixx]);
    }
}
 $i+=2;
// Bucle para mostrar el resultado
for($ix=0; $ix<count($monedas); $ix++)
{

	$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':F'.$i)->getFont()->setSize(12);
	$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':F'.$i)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_JUSTIFY);

	$objPHPExcel->getActiveSheet()->mergeCells('B'.$i.':F'.$i);

    if($cambio[$ix]>0)
    {
        if($monedas[$ix]>=1)
           $objPHPExcel->getActiveSheet()
			->setCellValue('B'.$i, 'Hay: '.$cambio[$ix].' Billetes de: '.$monedas[$ix]);
            //echo "Hay: ".$cambio[$ix]." billetes de: ".$monedas[$ix]." &euro;<br>";
        else
        	$objPHPExcel->getActiveSheet()
			->setCellValue('B'.$i,  'Hay: '.$cambio[$ix].' Monedas de: '.$monedas[$ix]);
           // echo "Hay: ".$cambio[$ix]." monedas de: ".$monedas[$ix]." &euro;<br>";
		$i++;
    }
    
}

$i+=2;
$objPHPExcel->getActiveSheet()
			->setCellValue('B'.$i, 'TOTAL PLANILLA NETA QUINCENAL');

$objPHPExcel->getActiveSheet()->mergeCells('B'.$i.':F'.$i);

$objPHPExcel->getActiveSheet()->getStyle('B'.$i)->applyFromArray(
array(
	'font'      => array(
		'bold'         => true,
		'size'         => 12
	),
	'alignment' => array(
		'horizontal'  => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
		'vertical'    => PHPExcel_Style_Alignment::VERTICAL_CENTER,
		'wrap'        => true
	)
));

$objPHPExcel->getActiveSheet()->getStyle('F'.$i)->applyFromArray(
array(
	'font'      => array(
		'bold'         => true,
		'size'         => 16
	)
));*/

$objPHPExcel->getActiveSheet()->getStyle('E'.$i.':K'.$i)->applyFromArray(allBordersThin());

$objPHPExcel->getActiveSheet()->getStyle('E'.$i)->getNumberFormat()
							  ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
$objPHPExcel->getActiveSheet()->getStyle('F'.$i)->getNumberFormat()
							  ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
$objPHPExcel->getActiveSheet()->getStyle('G'.$i)->getNumberFormat()
							  ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
$objPHPExcel->getActiveSheet()->getStyle('H'.$i)->getNumberFormat()
							  ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
$objPHPExcel->getActiveSheet()->getStyle('I'.$i)->getNumberFormat()
							  ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
$objPHPExcel->getActiveSheet()->getStyle('J'.$i)->getNumberFormat()
							  ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);							  



$i++;$i++;

$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':K'.$i)->getFont()->setSize(9);
$objPHPExcel->getActiveSheet()
			->setCellValue('C'.$i, count($trabajadores).' Empleados')
			->setCellValue('D'.$i, "Horas" )
			->setCellValue('E'.$i, "Ingreso" )
			->setCellValue('G'.$i, "Total" )
			->setCellValue('H'.$i, "Retenciones" )
			->setCellValue('K'.$i, "Monto" );
$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':G'.$i)->getBorders()->getBottom()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

$objPHPExcel->getActiveSheet()->getStyle('K'.$i)->getBorders()->getBottom()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

$i++;

$k=$i;
foreach ($asignaciones as $key => $row) 
{
	$objPHPExcel->getActiveSheet()
				->setCellValue('D'.$k, $row['cod'])
				->setCellValue('E'.$k, strtoupper($row['descripcion']))
				->setCellValue('G'.$k, $row['monto']);
	$objPHPExcel->getActiveSheet()->getStyle('G'.$k)->getNumberFormat()
							  ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);	
	$objPHPExcel->getActiveSheet()->getStyle('D'.$k.':G'.$k)->getFont()->setSize(8);	
	
	$objPHPExcel->getActiveSheet()->mergeCells('E'.$k.':F'.$k);						  
	$k++;						  
}

/*$objPHPExcel->getActiveSheet()
				->setCellValue('H32', 'eeeeeeeee');
$objPHPExcel->getActiveSheet()->getStyle('H32')->getFont()->setSize(9);*/
$j=$i;

foreach ($deduciones as $key => $row) 
{
	$objPHPExcel->getActiveSheet()
				->setCellValue('H'.$j, $row['cod'])
				->setCellValue('I'.$j, strtoupper($row['descripcion']))
				->setCellValue('K'.$j, $row['monto']);
	$objPHPExcel->getActiveSheet()->getStyle('K'.$j)->getNumberFormat()
							  ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);	
	$objPHPExcel->getActiveSheet()->getStyle('H'.$j.':K'.$j)->getFont()->setSize(8);	
	
	$objPHPExcel->getActiveSheet()->mergeCells('I'.$j.':J'.$j);						  
	$j++;						  
}

if($j>=$k){
	$i=$j;	
}
else{
	$i=$k;	
}

$objPHPExcel->getActiveSheet()
			->setCellValue('B'.$i, 'Neto a Pagar:')
			->setCellValue('C'.$i, $TotalAPagar )//->setCellValue('K'.$i, "=SUM(K7:K".($i-2).")" );
			->setCellValue('E'.$i, "SubTotal Ingresos:" )
			->setCellValue('G'.$i, $TotalIngresoregular )				
			->setCellValue('H'.$i, "SubTotal Retenciones:" )						
			->setCellValue('K'.$i, $TotalTotalRetener );
$TotalAPagar=$objPHPExcel->getActiveSheet()->getCell('K'.$i)->getCalculatedValue();
	$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':K'.$i)->getBorders()->getBottom()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle('C'.$i)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('G'.$i)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('K'.$i)->getFont()->setBold(true);

$objPHPExcel->getActiveSheet()->getStyle('K'.$i)->getNumberFormat()
							  ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);	
$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':K'.$i)->getFont()->setSize(8);		
$objPHPExcel->getActiveSheet()->mergeCells('I'.$i.':J'.$i);	

$objPHPExcel->getActiveSheet()->mergeCells('H'.$i.':J'.$i);	
$objPHPExcel->getActiveSheet()->mergeCells('E'.$i.':F'.$i);	

$objPHPExcel->getActiveSheet()->getStyle('C'.$i)->getNumberFormat()
							  ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);	
$objPHPExcel->getActiveSheet()->getStyle('G'.$i)->getNumberFormat()
							  ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);	
$objPHPExcel->getActiveSheet()->getStyle('K'.$i)->getNumberFormat()
							  ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);									  							  

			
cellColor('A'.$ini.':K'.($i+2), 'FFFFFF');


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
$filename = "Horizontal_".$NOMINA."_Del_".fecha($desde).'_Hasta_'.fecha($hasta);

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
