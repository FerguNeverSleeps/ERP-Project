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
$objPHPExcel = PHPExcel_IOFactory::load("plantillas/planilla_mmd.xlsx");

$objPHPExcel->getProperties()->setCreator("Selectra")
							 ->setLastModifiedBy("Selectra")
							 ->setTitle("Horizontal de Planilla");

$objPHPExcel->getDefaultStyle()->getFont()->setName('Calibri');
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
            ->setCellValue('C3', 'BORRADOR DE PLANILLA QUINCENAL')
            ->setCellValue('C4', 'Detallada por Centro de Costo')
            ->setCellValue('C5', 'Numero Nomina #'.$codnom.' del '. $dia_ini .' al '. $dia_fin .' de '. $mes_letras .' '. $anio );

//$objPHPExcel->getActiveSheet()->getStyle('A2:A4')->getFont()->setName('Calibri');
$objPHPExcel->getActiveSheet()->getStyle('C2:Q5')->getFont()->setSize(14);
$objPHPExcel->getActiveSheet()->getStyle('C2:Q5')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('C2:Q5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->mergeCells('C2:Q2');
$objPHPExcel->getActiveSheet()->mergeCells('C3:Q3');
$objPHPExcel->getActiveSheet()->mergeCells('C4:Q4');
$objPHPExcel->getActiveSheet()->mergeCells('C5:Q5');

//$objPHPExcel->getActiveSheet()->getStyle('C2:D5')->applyFromArray(allBordersThin());
//$objPHPExcel->getActiveSheet()->getStyle('C2:D5')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$objPHPExcel->getActiveSheet()
            ->setCellValue('A8', 'No.')
            ->setCellValue('B8', 'NOMBRE')
            ->setCellValue('C6', 'INGRESOS')
            ->setCellValue('C8', 'SALARIO REGULAR')
            //->setCellValue('D8', 'COMISIÓN')
            ->setCellValue('D8', 'H. EXTRA')
            //->setCellValue('F8', 'USO DE AUTO')
            ->setCellValue('E8', 'GASTOS REP.')
            ->setCellValue('F8', 'VIATICOS')
            ->setCellValue('G8', 'TARDANZA')
            ->setCellValue('H8', 'AUSENCIA')
            ->setCellValue('I8', 'INCAPACIDAD')
            ->setCellValue('J7', 'TOTAL')
            ->setCellValue('J8', 'INGRESOS')
            
            ->setCellValue('K6', 'DEDUCCIONES')
            ->setCellValue('K8', 'SEG. SOCIAL')
            ->setCellValue('L8', 'SEG. EDUCATIVO')
            ->setCellValue('M8', 'IMP. s. RENTA')
            ->setCellValue('N8', 'DESC LEY')
            ->setCellValue('O7', 'DESCUENTOS')
            ->setCellValue('O8', 'VARIOS')
            ->setCellValue('P7', 'ACREEDOR')
            ->setCellValue('P8', 'POR PAGAR')
            ->setCellValue('Q7', 'NETO')
            ->setCellValue('Q8', 'A PAGAR');
            
cellColor('C6:J8', '92d050');
cellColor('K6:P8', 'ffcc00');


//$objPHPExcel->getActiveSheet()->getStyle('A6:Q8')->getFont()->setName('Calibri');
$objPHPExcel->getActiveSheet()->getStyle('A6:R8')->getFont()->setSize(12);
$objPHPExcel->getActiveSheet()->getStyle('A6:R8')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('A6:R8')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$objPHPExcel->getActiveSheet()->mergeCells('C6:J6');
$objPHPExcel->getActiveSheet()->mergeCells('K6:P6');
$objPHPExcel->getActiveSheet()->getStyle('A6:Q8')->applyFromArray(allBordersThin());

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
			WHERE  np.codnivel1=".$codorg." AND np.tipnom=".$codtip."
			AND    np.ficha IN (SELECT DISTINCT n.ficha FROM nom_movimientos_nomina n WHERE n.codnom=".$codnom." AND n.tipnom=np.tipnom)
			ORDER  BY np.ficha";

	$res2=$conexion->query($sql2);

	$ini=$i; $enc=false;
	while($row2=$res2->fetch_array())
	{	$enc=true;
		$ficha = $row2['ficha'];
		$primer_nombre = utf8_encode($row2['primer_nombre']);
		$apellido   = utf8_encode($row2['primer_apellido']);
		$trabajador = utf8_encode($row2['primer_nombre']).' '.utf8_encode($row2['primer_apellido']); // $row2['nombre']

		//$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':Q'.$i)->getFont()->setName('Calibri');
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':Q'.$i)->getFont()->setSize(12);

		$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, $row2['ficha']);
		$objPHPExcel->getActiveSheet()->setCellValue('B'.$i, $trabajador);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':Q'.$i)->applyFromArray(allBordersThin());

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
  				  WHERE  n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon=198), 0) as ausencia,
                                COALESCE((SELECT  n.monto FROM nom_movimientos_nomina n 
  				  WHERE  n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon in (140,169)), 0) as incapacidad,
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
  				  WHERE  n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon=114)  as vacaciones,
				(SELECT  COALESCE(SUM(n.monto), 0) FROM nom_movimientos_nomina n 
  				  WHERE  n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon in (299))  as descuentos_varios";
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

			$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':P'.$i)->getAlignment()
										  ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->setCellValue('C'.$i, $row3['salario']);
			//$objPHPExcel->getActiveSheet()->setCellValue('D'.$i, $row3['comision']);  // COMISIONES
			

			//======================================================================================
			// HORAS EXTRA SALARIO
			if( ($MONTO_EXTRA_SALARIO + $MONTO_DOMINGO_SALARIO) <= 0 && $row3['horas_extras'] > 0 )
			{
				$objPHPExcel->getActiveSheet()->setCellValue('D'.$i, $row3['horas_extras']);
				$HORAS_EXTRA_SALARIO = $row3['horas_extras'];
			}
			else
			{
				$objPHPExcel->getActiveSheet()->setCellValue('D'.$i, $MONTO_EXTRA_SALARIO);
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
			$salario_trabajador=$row3['salario']+$row3['uso_auto']+$row3['comision']+$row3['bono']+$HORAS_EXTRA_SALARIO+$row3['reembolso']+$row3['vacaciones']+$row3['incapacidad']-$row3['tardanza']-$row3['ausencia'];
			//$objPHPExcel->getActiveSheet()->setCellValue('F'.$i, $row3['uso_auto']);
			$objPHPExcel->getActiveSheet()->setCellValue('E'.$i, $row3['reembolso']);//GASTOS REP.
			$objPHPExcel->getActiveSheet()->setCellValue('F'.$i, $row3['bono']);//VIATICOS
			$objPHPExcel->getActiveSheet()->setCellValue('G'.$i, $row3['tardanza']);
			$objPHPExcel->getActiveSheet()->setCellValue('H'.$i, $row3['ausencia']);
            $objPHPExcel->getActiveSheet()->setCellValue('I'.$i, $row3['incapacidad']);
			$objPHPExcel->getActiveSheet()->setCellValue('J'.$i, $salario_trabajador);                        
			$objPHPExcel->getActiveSheet()->setCellValue('K'.$i, $row3['seguro_social']);
			$objPHPExcel->getActiveSheet()->setCellValue('L'.$i, $row3['seguro_educativo']);
			$objPHPExcel->getActiveSheet()->setCellValue('M'.$i, $row3['isr']);
			//$objPHPExcel->getActiveSheet()->setCellValue('N'.$i, $row3['isr_gastos']);
			$objPHPExcel->getActiveSheet()->setCellValue('N'.$i, $row3['descuentos_varios']);
			$objPHPExcel->getActiveSheet()->setCellValue('O'.$i, $row3['descuentos']);

			
			$neto2 = $row3['salario'] + $row3['vacaciones']+ $row3['reembolso']+ $row3['comision']+ $row3['bono']+ $row3['incapacidad'] + $HORAS_EXTRA_SALARIO 
				   - $row3['seguro_social'] - $row3['seguro_educativo'] - $row3['tardanza'] - $row3['ausencia']
				   - $row3['isr'] - $row3['isr_gastos'] - $row3['descuentos_varios'] - $row3['descuentos'] - $row3['cxc']- $AJUSTE;

			//$objPHPExcel->getActiveSheet()->setCellValue('Q'.$i, $neto);
			$objPHPExcel->getActiveSheet()->setCellValue('Q'.$i, "=C$i+D$i+E$i+F$i-G$i-H$i+I$i-N$i-O$i-P$i-".$AJUSTE);
			$objPHPExcel->getActiveSheet()->setCellValue('N'.$i, "=K$i+L$i+M$i");
			// =+C38+F38+G38+H38+I38+J38-K38-L38-M38-N38-O38-P38
			$neto = "='".$dia_fin." ".$mes_letras."'!T".$i; //$objPHPExcel->getActiveSheet()->getCell('Q'.$i)->getValue();

			$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':Q'.$i)->getNumberFormat()
						->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_ACCOUNTING);
		}

		$trabajadores[] = array('nombre'=>$trabajador, 'primer_nombre'=> $primer_nombre, 'apellido'=>$apellido, 'neto'=> $neto, 'neto2'=>$neto2);

		$i++;
	}

	if($enc)
	{
		$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, 'Sub Total Centro de Costos: '. $row['descrip'] );

	$objPHPExcel->getActiveSheet()->setCellValue('C'.$i, "=SUM(C".$ini.":C".($i-1).")");	
	$total_salarios				  	.= "+C".$i;
	$objPHPExcel->getActiveSheet()->setCellValue('D'.$i, "=SUM(D".$ini.":D".($i-1).")");	
	$total_extra_salario				  	.= "+D".$i;
	$objPHPExcel->getActiveSheet()->setCellValue('E'.$i, "=SUM(E".$ini.":E".($i-1).")");	
	$total_reembolso 		  	.= "+E".$i;
	$objPHPExcel->getActiveSheet()->setCellValue('F'.$i, "=SUM(F".$ini.":F".($i-1).")");	
	$total_bono	 			  	.= "+F".$i;
	$objPHPExcel->getActiveSheet()->setCellValue('G'.$i, "=SUM(G".$ini.":G".($i-1).")");	
	$total_tardanza			  	.= "+G".$i;
	$objPHPExcel->getActiveSheet()->setCellValue('H'.$i, "=SUM(H".$ini.":H".($i-1).")");	
	$total_ausencia				  		.= "+H".$i;
	$objPHPExcel->getActiveSheet()->setCellValue('I'.$i, "=SUM(I".$ini.":I".($i-1).")");	
	$total_incapacidad   			  	.= "+I".$i;
	$objPHPExcel->getActiveSheet()->setCellValue('J'.$i, "=SUM(J".$ini.":J".($i-1).")");	
	$total_salario 				.= "+J".$i;
        $objPHPExcel->getActiveSheet()->setCellValue('K'.$i, "=SUM(K".$ini.":K".($i-1).")");	
	$total_segurosocial_salario 				.= "+K".$i;
	$objPHPExcel->getActiveSheet()->setCellValue('L'.$i, "=SUM(L".$ini.":L".($i-1).")");	
	$total_seguro_educativo   				.= "+L".$i;
	$objPHPExcel->getActiveSheet()->setCellValue('M'.$i, "=SUM(M".$ini.":M".($i-1).")");	
	$total_isr_salario  	.= "+M".$i;
	$objPHPExcel->getActiveSheet()->setCellValue('N'.$i, "=SUM(N".$ini.":N".($i-1).")");	
	$Total_Desc_Ley 		.= "+N".$i;
	$objPHPExcel->getActiveSheet()->setCellValue('O'.$i, "=SUM(O".$ini.":O".($i-1).")");	
	$total_desc_varios	 		  	.= "+O".$i;
	$objPHPExcel->getActiveSheet()->setCellValue('P'.$i, "=SUM(P".$ini.":P".($i-1).")");	
	$total_descuentos				.= "+P".$i;
	$objPHPExcel->getActiveSheet()->setCellValue('Q'.$i, "=SUM(Q".$ini.":Q".($i-1).")");	
	$total_neto  			  	.= "+Q".$i;

	//$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':Q'.$i)->getFont()->setName('Calibri');
		$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':Q'.$i)->getFont()->setSize(12);
		$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':Q'.$i)->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':Q'.$i)->getNumberFormat()
									  ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_ACCOUNTING);

		//$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getFont()->setName('Calibri');
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getFont()->setSize(8);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':B'.$i);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':B'.$i)->getBorders()->getBottom()
	        		->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
		if($nivel==1)
		{
			$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':Q'.$i)->applyFromArray(allBordersMedium());
		}
		else
		{
			$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':Q'.$i)->applyFromArray(allBordersThin());
		}

		$i++;
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':Q'.$i)->applyFromArray(allBordersMedium());	
		$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':Q'.$i);
		$i++;	
		$nivel++;
	}
}

//$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':Q'.$i);
$i++;
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':Q'.$i)->applyFromArray(allBordersMedium());	
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':Q'.$i)->getFont()->setSize(12);
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':Q'.$i)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()
			->setCellValue('B'.$i, 'Total')
			->setCellValue('C'.$i, $total_salarios)
			->setCellValue('D'.$i, $total_extra_salario)
			->setCellValue('E'.$i, $total_reembolso)
			->setCellValue('F'.$i, $total_bono)
			->setCellValue('G'.$i, $total_tardanza)
			->setCellValue('H'.$i, $total_ausencia)
            ->setCellValue('I'.$i, $total_incapacidad)
			->setCellValue('J'.$i, $total_salario)
			->setCellValue('K'.$i, $total_segurosocial_salario)
			->setCellValue('L'.$i, $total_seguro_educativo)
			->setCellValue('M'.$i, $total_isr_salario)
			->setCellValue('N'.$i, $Total_Desc_Ley)
			->setCellValue('O'.$i, $total_descuentos)
			->setCellValue('P'.$i, $total_desc_varios)
			->setCellValue('Q'.$i, $total_neto);
		$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':Q'.$i)->getNumberFormat()
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
			->setCellValue('C'.$i, "=C".$j ."-I".$j."-J".$j )
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
			->setCellValue('D'.$i++, "=M".$j)
			->setCellValue('B'.$i, 'Seguro Educ. por Pagar')
			->setCellValue('D'.$i++, "=N".$j )
			->setCellValue('B'.$i, 'Imp. S/ R empleado * pagar')
			->setCellValue('D'.$i++, "=O".$j)
			->setCellValue('B'.$i, 'Imp. S/ R GR * pagar')
			->setCellValue('D'.$i++, "=P".$j)
			->setCellValue('B'.$i, 'Desc. Empleado * Pagar')
			->setCellValue('D'.$i++, "=S".$j."+R".$j)
			->setCellValue('B'.$i, 'Salario por Pagar')
			->setCellValue('D'.$i++, "=T".$j)
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
			->setCellValue('F'.$i, "=SUM(F7:F".($i-2).")" );
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
