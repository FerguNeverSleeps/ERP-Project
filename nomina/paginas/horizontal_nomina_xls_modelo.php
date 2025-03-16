<?php
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('America/Panama');

if (PHP_SAPI == 'cli')
	die('Este ejemplo solo se debe ejecutar desde un navegador Web');

include('../lib/common_excel.php');
include('lib/php_excel.php');

if(isset($_GET['codnom']) && isset($_GET['codtip']))
{
	$codnom=$_GET['codnom'];
	$codtip=$_GET['codtip'];

	$conexion=conexion();

	$sql = "SELECT UPPER(t.descrip) as descrip 
			FROM   nomtipos_nomina t
			WHERE  t.codtip=".$codtip;
	$res=query($sql, $conexion);

	if($fila=fetch_array($res))
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
$res=query($sql, $conexion);
$fila=fetch_array($res);
$logo=$fila['logo'];

$sql2 = "SELECT np.periodo_ini as desde, np.periodo_fin as hasta, np.mes, np.anio,
				DATE_FORMAT(np.periodo_ini,'%d') as dia_ini, DATE_FORMAT(np.periodo_fin, '%d') as dia_fin   
		 FROM nom_nominas_pago np 
         WHERE  np.codnom=".$codnom." AND np.tipnom=".$codtip;
$res2=query($sql2, $conexion);
$fila2=fetch_array($res2);

$meses = array('Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre');

$desde=$fila2['desde'];
$hasta=$fila2['hasta'];
$dia_ini = $fila2['dia_ini'];
$dia_fin = $fila2['dia_fin']; 
$mes_numero = $fila2['mes'];
$mes_letras = $meses[$mes_numero - 1];
$anio = $fila2['anio'];

/*
$objDrawing = new PHPExcel_Worksheet_Drawing();
$objDrawing->setName('Logo');
$objDrawing->setDescription('Logo');
$objDrawing->setCoordinates('B2');
$objDrawing->setPath('../imagenes/'.$logo);
//$objDrawing->setResizeProportional(true);
$objDrawing->setHeight(80);
//$objDrawing->setWidth(220);
$objDrawing->setOffsetX(0);
$objDrawing->setOffsetY(0);
$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
$objDrawing->setHeight(36);
$objPHPExcel->getActiveSheet()->getHeaderFooter()->addImage($objDrawing, PHPExcel_Worksheet_HeaderFooter::IMAGE_HEADER_LEFT);
*/
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
            ->setCellValue('F7', 'Gasto')
            ->setCellValue('G7', 'Tiempo Extra')
            ->setCellValue('I7', 'Domingo/Dia Nacional')
            ->setCellValue('K7', 'Seguro Social')
            ->setCellValue('M7', 'Seguro')
            ->setCellValue('N7', 'Imp. s/renta')
            ->setCellValue('P7', 'Descuentos')
            ->setCellValue('A8', 'No.')
            ->setCellValue('B8', 'Nombre')
            ->setCellValue('C8', 'Salario')
            ->setCellValue('D8', 'Dieta')
            ->setCellValue('E8', 'Combustible')
            ->setCellValue('F8', 'Represe.')
            ->setCellValue('G8', 'Salario')
            ->setCellValue('H8', 'G.Represe.')
            ->setCellValue('I8', 'Salario')
            ->setCellValue('J8', 'G.Represe.')
            ->setCellValue('K8', 'Salario')
            ->setCellValue('L8', 'G. Repre')
            ->setCellValue('M8', 'Educa.')
            ->setCellValue('N8', 'Salario')
            ->setCellValue('O8', 'G. Repre')
            ->setCellValue('P8', 'por Pagar')
            ->setCellValue('Q8', 'Neto');

//$objPHPExcel->getActiveSheet()->getStyle('A6:Q8')->getFont()->setName('Calibri');
$objPHPExcel->getActiveSheet()->getStyle('A6:Q8')->getFont()->setSize(12);
$objPHPExcel->getActiveSheet()->getStyle('A6:Q8')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('A6:Q8')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$objPHPExcel->getActiveSheet()->mergeCells('A6:Q6');
$objPHPExcel->getActiveSheet()->mergeCells('G7:H7');
$objPHPExcel->getActiveSheet()->mergeCells('I7:J7');
$objPHPExcel->getActiveSheet()->mergeCells('K7:L7');
$objPHPExcel->getActiveSheet()->mergeCells('N7:O7');

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
$res=query($sql, $conexion);

$i=9; $nivel=1;
// $monto_salarios=0;
$total_salarios=$total_dietas=$total_combustible=$total_gastosrep="=";
$total_extra_salario=$total_extra_gastosrep=$total_domingo_salario=$total_domingo_gastosrep="=";
$total_segurosocial_salario=$total_segurosocial_gastosrep=$total_seguro_educativo="=";
$total_isr_salario=$total_isr_gastosrep=$total_descuentos=$total_neto="=";
while($row=fetch_array($res))
{
	$codorg = $row['codorg'];

	$sql2= "SELECT np.ficha, CONCAT_WS(' ', np.nombres, np.apellidos) as nombre,
				   SUBSTRING_INDEX(SUBSTRING_INDEX(np.nombres, ' ', 1), ' ', -1) as primer_nombre,
				   CASE WHEN np.apellidos LIKE 'De %' THEN 
						     SUBSTRING_INDEX(SUBSTRING_INDEX(np.apellidos, ' ', 2), ' ',  2) 
				   ELSE      SUBSTRING_INDEX(SUBSTRING_INDEX(np.apellidos, ' ', 1), ' ', -1) 
				   END as primer_apellido
			FROM   nompersonal np
			WHERE  np.codnivel1=".$codorg." AND np.ficha!=4 AND np.tipnom=".$codtip."
			AND    np.ficha IN (SELECT DISTINCT n.ficha FROM nom_movimientos_nomina n WHERE n.codnom=".$codnom." AND n.tipnom=np.tipnom)
			ORDER BY np.ficha";

	$res2=query($sql2, $conexion);

	$ini=$i; $enc=false;
	while($row2=fetch_array($res2))
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
  				  WHERE  n.codnom=".$codnom." AND n.ficha=".$ficha." AND n.codcon=100),0) as salario,
				 COALESCE((SELECT n.monto FROM nom_movimientos_nomina n 
  				  WHERE  n.codnom=".$codnom." AND n.ficha=".$ficha." AND n.codcon=148), 0) as dieta,
				 COALESCE((SELECT n.monto FROM nom_movimientos_nomina n 
  				  WHERE  n.codnom=".$codnom." AND n.ficha=".$ficha." AND n.codcon=147), 0) as combustible,
				 COALESCE(
				 (SELECT n.monto FROM nom_movimientos_nomina n 
  				  WHERE  n.codnom=".$codnom." AND n.ficha=".$ficha." AND n.codcon=145),0) as gastos_rep,				 
				 COALESCE((SELECT n.monto FROM nom_movimientos_nomina n 
  				  WHERE  n.codnom=".$codnom." AND n.ficha=".$ficha." AND n.codcon=200), 0) as seguro_social,
				COALESCE((SELECT  n.monto FROM nom_movimientos_nomina n 
  				  WHERE  n.codnom=".$codnom." AND n.ficha=".$ficha." AND n.codcon=201), 0) as seguro_educativo,
				 COALESCE((SELECT n.monto FROM nom_movimientos_nomina n 
  				  WHERE  n.codnom=".$codnom." AND n.ficha=".$ficha." AND n.codcon=208), 0) as seguro_social_gastos_rep,
 				COALESCE((SELECT  SUM(n.monto) FROM nom_movimientos_nomina n 
  				  WHERE  n.codnom=".$codnom." AND n.ficha=".$ficha."
  				  AND    n.codcon IN (202, 601, 605)), 0) as isr,
				COALESCE((SELECT  n.monto FROM nom_movimientos_nomina n 
  				  WHERE  n.codnom=".$codnom." AND n.ficha=".$ficha."
  				  AND    n.codcon IN (207, 606, 607)), 0) as isr_gastos,
				(SELECT  COALESCE(SUM(n.monto), 0) FROM nom_movimientos_nomina n 
  				  WHERE  n.codnom=".$codnom." AND n.ficha=".$ficha." AND n.codcon BETWEEN 501 AND 599)  as descuentos,
				COALESCE((SELECT  n.monto FROM nom_movimientos_nomina n 
				 WHERE   n.codnom=".$codnom." AND n.ficha=".$ficha." 
				 AND     n.codcon=600),0) as horas_extras,
				COALESCE((SELECT  n.monto FROM nom_movimientos_nomina n 
				 WHERE   n.codnom=".$codnom." AND n.ficha=".$ficha." 
				 AND     n.codcon=602),0) as horas_extras_gr";
		$res3 = query($sql3, $conexion);
		$neto=0;
		if($row3=fetch_array($res3))
		{
			$MONTO_EXTRA_SALARIO = $MONTO_DOMINGO_SALARIO = 0;
			$MONTO_GR_EXTRA_SALARIO = $MONTO_GR_DOMINGO_SALARIO = 0;
			$AJUSTE = 0;

			$sql5 = "SELECT COALESCE(SUM(n.monto), 0) AS total_extra_salario  
					FROM   nom_movimientos_nomina n 
					WHERE  n.codnom=".$codnom." AND n.ficha=".$ficha."
					AND (  n.codcon IN (106, 108, 142, 143, 183, 184)
					 OR    n.codcon IN (123, 124, 125, 126, 144, 149, 150, 151)
					 OR    n.codcon IN (107, 109, 152, 153) )";
			$res5 = query($sql5, $conexion);
			if($row5=fetch_array($res5)){ $MONTO_EXTRA_SALARIO = $row5['total_extra_salario']; }

			$sql5 = "SELECT COALESCE(SUM(n.monto), 0) AS total_domingo_salario  
					 FROM   nom_movimientos_nomina n 
					 WHERE  n.codnom=".$codnom." AND n.ficha=".$ficha."
					 AND (  n.codcon IN (112, 113, 127, 128, 129, 130, 131, 132)
					  OR    n.codcon IN (133, 134, 135, 136, 138, 139) )";
			$res5 = query($sql5, $conexion);
			if($row5=fetch_array($res5)){ $MONTO_DOMINGO_SALARIO = $row5['total_domingo_salario']; }

			$sql5 = "SELECT COALESCE(SUM(n.monto), 0) AS gastos_rep_extra_salario  
					 FROM   nom_movimientos_nomina n 
					 WHERE  n.codnom=".$codnom." AND n.ficha=".$ficha."
					 AND (   n.codcon IN (156, 157)
					 OR      n.codcon IN (158, 159, 160, 161, 185, 186, 187, 188) 
					 OR      n.codcon IN (162, 163, 189, 190) )";
			$res5 = query($sql5, $conexion);
			if($row5=fetch_array($res5)){ $MONTO_GR_EXTRA_SALARIO = $row5['gastos_rep_extra_salario']; }

			$sql5 = "SELECT COALESCE(SUM(n.monto), 0) AS gastos_rep_domingo_salario  
					 FROM   nom_movimientos_nomina n 
					 WHERE  n.codnom=".$codnom." AND n.ficha=".$ficha." 
					 AND (   n.codcon IN (165, 166, 167, 168, 169, 170, 171, 172)
					 OR      n.codcon IN (175, 176, 177, 178, 179, 181) )";
			$res5 = query($sql5, $conexion);
			if($row5=fetch_array($res5)){ $MONTO_GR_DOMINGO_SALARIO = $row5['gastos_rep_domingo_salario']; }

			// Calcular AJUSTE
			$sql5= "SELECT COALESCE(SUM(monto), 0) AS ajuste
					FROM   nom_movimientos_nomina n 
					WHERE  n.codcon=604
					AND    n.codnom=".$codnom." AND n.ficha=".$ficha;

			$res5 = query($sql5, $conexion);
			if($row5 = fetch_array($res5) ){ $AJUSTE = $row5['ajuste']; }

			// $impuesto = ($row3['isr_gastos'] >0) ? $row3['isr'] - $row3['isr_gastos'] : $row3['isr'];

			$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':Q'.$i)->getAlignment()
										  ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->setCellValue('C'.$i, $row3['salario']);
			$objPHPExcel->getActiveSheet()->setCellValue('D'.$i, $row3['dieta']);
			$objPHPExcel->getActiveSheet()->setCellValue('E'.$i, $row3['combustible']);
			$objPHPExcel->getActiveSheet()->setCellValue('F'.$i, $row3['gastos_rep']);
			

			//======================================================================================
			// HORAS EXTRA SALARIO
			if( ($MONTO_EXTRA_SALARIO + $MONTO_DOMINGO_SALARIO) <= 0 && $row3['horas_extras'] > 0 )
			{
				$objPHPExcel->getActiveSheet()->setCellValue('G'.$i, $row3['horas_extras']);
				$HORAS_EXTRA_SALARIO = $row3['horas_extras'];
			}
			else
			{
				$objPHPExcel->getActiveSheet()->setCellValue('G'.$i, $MONTO_EXTRA_SALARIO);
				$objPHPExcel->getActiveSheet()->setCellValue('I'.$i, $MONTO_DOMINGO_SALARIO);	
				$HORAS_EXTRA_SALARIO = $MONTO_EXTRA_SALARIO + $MONTO_DOMINGO_SALARIO;			
			}
			//======================================================================================
			// HORAS EXTRA GASTOS DE REPRESENTACION
			if( ($MONTO_GR_EXTRA_SALARIO + $MONTO_GR_DOMINGO_SALARIO) <= 0 && $row3['horas_extras_gr'] > 0 )
			{
				$objPHPExcel->getActiveSheet()->setCellValue('H'.$i, $row3['horas_extras_gr']);
				$HORAS_GR_SALARIO = $row3['horas_extras_gr'];
			}
			else
			{
				$objPHPExcel->getActiveSheet()->setCellValue('H'.$i, $MONTO_GR_EXTRA_SALARIO);
				$objPHPExcel->getActiveSheet()->setCellValue('J'.$i, $MONTO_GR_DOMINGO_SALARIO);	
				$HORAS_GR_SALARIO = $MONTO_GR_EXTRA_SALARIO + $MONTO_GR_DOMINGO_SALARIO;
			}
			//======================================================================================

			// $objPHPExcel->getActiveSheet()->setCellValue('K'.$i, "=(C".$i."+G".$i.") * 9.75% ");
			// $objPHPExcel->getActiveSheet()->setCellValue('L'.$i, "=(F".$i."+H".$i.") * 9.75% ");

			$objPHPExcel->getActiveSheet()->setCellValue('K'.$i, $row3['seguro_social']);
			$objPHPExcel->getActiveSheet()->setCellValue('L'.$i, $row3['seguro_social_gastos_rep']);			

			$objPHPExcel->getActiveSheet()->setCellValue('M'.$i, $row3['seguro_educativo']);


			$objPHPExcel->getActiveSheet()->setCellValue('N'.$i, $row3['isr']);
			$objPHPExcel->getActiveSheet()->setCellValue('O'.$i, $row3['isr_gastos']);
			$objPHPExcel->getActiveSheet()->setCellValue('P'.$i, $row3['descuentos']);

			
			$neto2 = $row3['salario'] + $row3['dieta'] + $row3['combustible'] + $row3['gastos_rep'] 
			       + $HORAS_EXTRA_SALARIO + $HORAS_GR_SALARIO
				   - $row3['seguro_social'] - $row3['seguro_social_gastos_rep'] - $row3['seguro_educativo'] 
				   - $row3['isr'] - $row3['isr_gastos'] - $row3['descuentos'] - $AJUSTE;

			//$objPHPExcel->getActiveSheet()->setCellValue('Q'.$i, $neto);
			$objPHPExcel->getActiveSheet()->setCellValue('Q'.$i, "=C$i+D$i+E$i+F$i+G$i+H$i+I$i+J$i-K$i-L$i-M$i-N$i-O$i-P$i-".$AJUSTE);
			// =+C38+F38+G38+H38+I38+J38-K38-L38-M38-N38-O38-P38
			$neto = "='".$dia_fin." ".$mes_letras."'!Q".$i; //$objPHPExcel->getActiveSheet()->getCell('Q'.$i)->getValue();

			$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':Q'.$i)->getNumberFormat()
						->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_ACCOUNTING);
		}

		$trabajadores[] = array('nombre'=>$trabajador, 'primer_nombre'=> $primer_nombre, 'apellido'=>$apellido, 'neto'=> $neto, 'neto2'=>$neto2);

		$i++;
	}

	if($enc)
	{
		$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, 'Total de Salarios '. $row['grupo'] );

		$objPHPExcel->getActiveSheet()->setCellValue('C'.$i, "=SUM(C".$ini.":C".($i-1).")");	$total_salarios      		  .= "+C".$i;
		$objPHPExcel->getActiveSheet()->setCellValue('D'.$i, "=SUM(D".$ini.":D".($i-1).")");	$total_dietas        		  .= "+D".$i;
		$objPHPExcel->getActiveSheet()->setCellValue('E'.$i, "=SUM(E".$ini.":E".($i-1).")");	$total_combustible   		  .= "+E".$i;
		$objPHPExcel->getActiveSheet()->setCellValue('F'.$i, "=SUM(F".$ini.":F".($i-1).")");	$total_gastosrep     		  .= "+F".$i;
		$objPHPExcel->getActiveSheet()->setCellValue('G'.$i, "=SUM(G".$ini.":G".($i-1).")");	$total_extra_salario 		  .= "+G".$i;
		$objPHPExcel->getActiveSheet()->setCellValue('H'.$i, "=SUM(H".$ini.":H".($i-1).")");	$total_extra_gastosrep 		  .= "+H".$i;
		$objPHPExcel->getActiveSheet()->setCellValue('I'.$i, "=SUM(I".$ini.":I".($i-1).")");	$total_domingo_salario 		  .= "+I".$i;
		$objPHPExcel->getActiveSheet()->setCellValue('J'.$i, "=SUM(J".$ini.":J".($i-1).")");	$total_domingo_gastosrep 	  .= "+J".$i;
		$objPHPExcel->getActiveSheet()->setCellValue('K'.$i, "=SUM(K".$ini.":K".($i-1).")");	$total_segurosocial_salario   .= "+K".$i;
		$objPHPExcel->getActiveSheet()->setCellValue('L'.$i, "=SUM(L".$ini.":L".($i-1).")");	$total_segurosocial_gastosrep .= "+L".$i;
		$objPHPExcel->getActiveSheet()->setCellValue('M'.$i, "=SUM(M".$ini.":M".($i-1).")");	$total_seguro_educativo 	  .= "+M".$i;
		$objPHPExcel->getActiveSheet()->setCellValue('N'.$i, "=SUM(N".$ini.":N".($i-1).")");	$total_isr_salario 			  .= "+N".$i;
		$objPHPExcel->getActiveSheet()->setCellValue('O'.$i, "=SUM(O".$ini.":O".($i-1).")");	$total_isr_gastosrep 		  .= "+O".$i;
		$objPHPExcel->getActiveSheet()->setCellValue('P'.$i, "=SUM(P".$ini.":P".($i-1).")");	$total_descuentos 			  .= "+P".$i;
		$objPHPExcel->getActiveSheet()->setCellValue('Q'.$i, "=SUM(Q".$ini.":Q".($i-1).")");	$total_neto 				  .= "+Q".$i;
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

//==================================================================================================
// Ahora mostramos los empleados de otros departamentos

$sql4 = "SELECT np.ficha, CONCAT_WS(' ', np.nombres, np.apellidos) as nombre, np.codnivel1,
			    SUBSTRING_INDEX(SUBSTRING_INDEX(np.nombres, ' ', 1), ' ', -1) as primer_nombre,
				CASE WHEN np.apellidos LIKE 'De %' THEN 
					      SUBSTRING_INDEX(SUBSTRING_INDEX(np.apellidos, ' ', 2), ' ',  2) 
				ELSE      SUBSTRING_INDEX(SUBSTRING_INDEX(np.apellidos, ' ', 1), ' ', -1) 
				END as primer_apellido
		 FROM   nompersonal np
		 WHERE  np.codnivel1 NOT BETWEEN 1 AND 4
		 AND    np.tipnom=".$codtip."
		 AND    np.ficha IN (SELECT DISTINCT n.ficha FROM nom_movimientos_nomina n WHERE n.codnom=".$codnom." AND n.tipnom=np.tipnom)
		 ORDER BY np.ficha";

$res4=query($sql4, $conexion);

$ini=$i; $enc=false;
while($row4=fetch_array($res4))
{
	$enc=true;
	$ficha = $row4['ficha'];
	$primer_nombre = utf8_encode($row4['primer_nombre']);
	$apellido   = utf8_encode($row4['primer_apellido']);
	$trabajador = utf8_encode($row4['primer_nombre']).' '.utf8_encode($row4['primer_apellido']); // $row4['nombre']

	//$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':Q'.$i)->getFont()->setName('Calibri');
	$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':Q'.$i)->getFont()->setSize(12);

	$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, $row4['ficha']);
	$objPHPExcel->getActiveSheet()->setCellValue('B'.$i, $trabajador);
	$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':Q'.$i)->applyFromArray(allBordersThin());

	$sql3 = "SELECT
			 COALESCE((SELECT n.monto FROM nom_movimientos_nomina n 
				  WHERE  n.codnom=".$codnom." AND n.ficha=".$ficha." AND n.codcon=100),0) as salario,
			 COALESCE((SELECT n.monto FROM nom_movimientos_nomina n 
				  WHERE  n.codnom=".$codnom." AND n.ficha=".$ficha." AND n.codcon=148), 0) as dieta,
			 COALESCE((SELECT n.monto FROM nom_movimientos_nomina n 
				  WHERE  n.codnom=".$codnom." AND n.ficha=".$ficha." AND n.codcon=147), 0) as combustible,
			 COALESCE(
			 (SELECT n.monto FROM nom_movimientos_nomina n 
				  WHERE  n.codnom=".$codnom." AND n.ficha=".$ficha." AND n.codcon=145),0) as gastos_rep,				 
			 COALESCE((SELECT n.monto FROM nom_movimientos_nomina n 
				  WHERE  n.codnom=".$codnom." AND n.ficha=".$ficha." AND n.codcon=200), 0) as seguro_social,
			COALESCE((SELECT  n.monto FROM nom_movimientos_nomina n 
				  WHERE  n.codnom=".$codnom." AND n.ficha=".$ficha." AND n.codcon=201), 0) as seguro_educativo,
			 COALESCE((SELECT n.monto FROM nom_movimientos_nomina n 
				  WHERE  n.codnom=".$codnom." AND n.ficha=".$ficha." AND n.codcon=208), 0) as seguro_social_gastos_rep,
				COALESCE((SELECT  SUM(n.monto) FROM nom_movimientos_nomina n 
				  WHERE  n.codnom=".$codnom." AND n.ficha=".$ficha."
				  AND    n.codcon IN (202, 601, 605)), 0) as isr,
			COALESCE((SELECT  n.monto FROM nom_movimientos_nomina n 
				  WHERE  n.codnom=".$codnom." AND n.ficha=".$ficha."
				  AND    n.codcon IN (207, 606, 607)), 0) as isr_gastos,
			(SELECT  COALESCE(SUM(n.monto), 0) FROM nom_movimientos_nomina n 
				  WHERE  n.codnom=".$codnom." AND n.ficha=".$ficha." AND n.codcon BETWEEN 501 AND 599)  as descuentos,
			COALESCE((SELECT  n.monto FROM nom_movimientos_nomina n 
			 WHERE   n.codnom=".$codnom." AND n.ficha=".$ficha." 
			 AND     n.codcon=600),0) as horas_extras,
			COALESCE((SELECT  n.monto FROM nom_movimientos_nomina n 
			 WHERE   n.codnom=".$codnom." AND n.ficha=".$ficha." 
			 AND     n.codcon=602),0) as horas_extras_gr";
	$res3 = query($sql3, $conexion);
	$neto=0;
	if($row3=fetch_array($res3))
	{
			$MONTO_EXTRA_SALARIO = $MONTO_DOMINGO_SALARIO = 0;
			$MONTO_GR_EXTRA_SALARIO = $MONTO_GR_DOMINGO_SALARIO = 0;
			$AJUSTE = 0;

			$sql5 = "SELECT COALESCE(SUM(n.monto), 0) AS total_extra_salario  
					FROM   nom_movimientos_nomina n 
					WHERE  n.codnom=".$codnom." AND n.ficha=".$ficha."
					AND (  n.codcon IN (106, 108, 142, 143, 183, 184)
					 OR    n.codcon IN (123, 124, 125, 126, 144, 149, 150, 151)
					 OR    n.codcon IN (107, 109, 152, 153) )";
			$res5 = query($sql5, $conexion);
			if($row5=fetch_array($res5)){ $MONTO_EXTRA_SALARIO = $row5['total_extra_salario']; }

			$sql5 = "SELECT COALESCE(SUM(n.monto), 0) AS total_domingo_salario  
					 FROM   nom_movimientos_nomina n 
					 WHERE  n.codnom=".$codnom." AND n.ficha=".$ficha."
					 AND (  n.codcon IN (112, 113, 127, 128, 129, 130, 131, 132)
					  OR    n.codcon IN (133, 134, 135, 136, 138, 139) )";
			$res5 = query($sql5, $conexion);
			if($row5=fetch_array($res5)){ $MONTO_DOMINGO_SALARIO = $row5['total_domingo_salario']; }

			$sql5 = "SELECT COALESCE(SUM(n.monto), 0) AS gastos_rep_extra_salario  
					 FROM   nom_movimientos_nomina n 
					 WHERE  n.codnom=".$codnom." AND n.ficha=".$ficha."
					 AND (   n.codcon IN (156, 157)
					 OR      n.codcon IN (158, 159, 160, 161, 185, 186, 187, 188) 
					 OR      n.codcon IN (162, 163, 189, 190) )";
			$res5 = query($sql5, $conexion);
			if($row5=fetch_array($res5)){ $MONTO_GR_EXTRA_SALARIO = $row5['gastos_rep_extra_salario']; }

			$sql5 = "SELECT COALESCE(SUM(n.monto), 0) AS gastos_rep_domingo_salario  
					 FROM   nom_movimientos_nomina n 
					 WHERE  n.codnom=".$codnom." AND n.ficha=".$ficha." 
					 AND (   n.codcon IN (165, 166, 167, 168, 169, 170, 171, 172)
					 OR      n.codcon IN (175, 176, 177, 178, 179, 181) )";
			$res5 = query($sql5, $conexion);
			if($row5=fetch_array($res5)){ $MONTO_GR_DOMINGO_SALARIO = $row5['gastos_rep_domingo_salario']; }

			// Calcular AJUSTE
			$sql5= "SELECT COALESCE(SUM(monto), 0) AS ajuste
					FROM   nom_movimientos_nomina n 
					WHERE  n.codcon=604
					AND    n.codnom=".$codnom." AND n.ficha=".$ficha;

			$res5 = query($sql5, $conexion);
			if($row5 = fetch_array($res5) ){ $AJUSTE = $row5['ajuste']; }

			// $impuesto = ($row3['isr_gastos'] >0) ? $row3['isr'] - $row3['isr_gastos'] : $row3['isr'];

			$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':Q'.$i)->getAlignment()
										  ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->setCellValue('C'.$i, $row3['salario']);
			$objPHPExcel->getActiveSheet()->setCellValue('D'.$i, $row3['dieta']);
			$objPHPExcel->getActiveSheet()->setCellValue('E'.$i, $row3['combustible']);
			$objPHPExcel->getActiveSheet()->setCellValue('F'.$i, $row3['gastos_rep']);
			

			//======================================================================================
			// HORAS EXTRA SALARIO
			if( ($MONTO_EXTRA_SALARIO + $MONTO_DOMINGO_SALARIO) <= 0 && $row3['horas_extras'] > 0 )
			{
				$objPHPExcel->getActiveSheet()->setCellValue('G'.$i, $row3['horas_extras']);
				$HORAS_EXTRA_SALARIO = $row3['horas_extras'];
			}
			else
			{
				$objPHPExcel->getActiveSheet()->setCellValue('G'.$i, $MONTO_EXTRA_SALARIO);
				$objPHPExcel->getActiveSheet()->setCellValue('I'.$i, $MONTO_DOMINGO_SALARIO);	
				$HORAS_EXTRA_SALARIO = $MONTO_EXTRA_SALARIO + $MONTO_DOMINGO_SALARIO;			
			}
			//======================================================================================
			// HORAS EXTRA GASTOS DE REPRESENTACION
			if( ($MONTO_GR_EXTRA_SALARIO + $MONTO_GR_DOMINGO_SALARIO) <= 0 && $row3['horas_extras_gr'] > 0 )
			{
				$objPHPExcel->getActiveSheet()->setCellValue('H'.$i, $row3['horas_extras_gr']);
				$HORAS_GR_SALARIO = $row3['horas_extras_gr'];
			}
			else
			{
				$objPHPExcel->getActiveSheet()->setCellValue('H'.$i, $MONTO_GR_EXTRA_SALARIO);
				$objPHPExcel->getActiveSheet()->setCellValue('J'.$i, $MONTO_GR_DOMINGO_SALARIO);	
				$HORAS_GR_SALARIO = $MONTO_GR_EXTRA_SALARIO + $MONTO_GR_DOMINGO_SALARIO;
			}
			//======================================================================================

			// $objPHPExcel->getActiveSheet()->setCellValue('K'.$i, "=(C".$i."+G".$i.") * 9.75% ");
			// $objPHPExcel->getActiveSheet()->setCellValue('L'.$i, "=(F".$i."+H".$i.") * 9.75% ");

			$objPHPExcel->getActiveSheet()->setCellValue('K'.$i, $row3['seguro_social']);
			$objPHPExcel->getActiveSheet()->setCellValue('L'.$i, $row3['seguro_social_gastos_rep']);			

			$objPHPExcel->getActiveSheet()->setCellValue('M'.$i, $row3['seguro_educativo']);


			$objPHPExcel->getActiveSheet()->setCellValue('N'.$i, $row3['isr']);
			$objPHPExcel->getActiveSheet()->setCellValue('O'.$i, $row3['isr_gastos']);
			$objPHPExcel->getActiveSheet()->setCellValue('P'.$i, $row3['descuentos']);

			
			$neto2 = $row3['salario'] + $row3['dieta'] + $row3['combustible'] + $row3['gastos_rep'] 
			       + $HORAS_EXTRA_SALARIO + $HORAS_GR_SALARIO - $AJUSTE
				   - $row3['seguro_social'] - $row3['seguro_social_gastos_rep'] - $row3['seguro_educativo'] 
				   - $row3['isr'] - $row3['isr_gastos'] - $row3['descuentos'];

			//$objPHPExcel->getActiveSheet()->setCellValue('Q'.$i, $neto);
			$objPHPExcel->getActiveSheet()->setCellValue('Q'.$i, "=C$i+D$i+E$i+F$i+G$i+H$i+I$i+J$i-K$i-L$i-M$i-N$i-O$i-P$i-".$AJUSTE);
			// =+C38+F38+G38+H38+I38+J38-K38-L38-M38-N38-O38-P38
			$neto = "='".$dia_fin." ".$mes_letras."'!Q".$i; //$objPHPExcel->getActiveSheet()->getCell('Q'.$i)->getValue();

			$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':Q'.$i)->getNumberFormat()
						->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_ACCOUNTING);	
	}
	$trabajadores[] = array('nombre'=>$trabajador, 'primer_nombre'=> $primer_nombre, 'apellido'=>$apellido, 'neto'=> $neto, 'neto2'=>$neto2);
	$i++;
}

if($enc)
{
	$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, 'Total de Otros Salarios');

	$objPHPExcel->getActiveSheet()->setCellValue('C'.$i, "=SUM(C".$ini.":C".($i-1).")");	$total_salarios      		  .= "+C".$i;
	$objPHPExcel->getActiveSheet()->setCellValue('D'.$i, "=SUM(D".$ini.":D".($i-1).")");	$total_dietas        		  .= "+D".$i;
	$objPHPExcel->getActiveSheet()->setCellValue('E'.$i, "=SUM(E".$ini.":E".($i-1).")");	$total_combustible   		  .= "+E".$i;
	$objPHPExcel->getActiveSheet()->setCellValue('F'.$i, "=SUM(F".$ini.":F".($i-1).")");	$total_gastosrep     		  .= "+F".$i;
	$objPHPExcel->getActiveSheet()->setCellValue('G'.$i, "=SUM(G".$ini.":G".($i-1).")");	$total_extra_salario 		  .= "+G".$i;
	$objPHPExcel->getActiveSheet()->setCellValue('H'.$i, "=SUM(H".$ini.":H".($i-1).")");	$total_extra_gastosrep 		  .= "+H".$i;
	$objPHPExcel->getActiveSheet()->setCellValue('I'.$i, "=SUM(I".$ini.":I".($i-1).")");	$total_domingo_salario 		  .= "+I".$i;
	$objPHPExcel->getActiveSheet()->setCellValue('J'.$i, "=SUM(J".$ini.":J".($i-1).")");	$total_domingo_gastosrep 	  .= "+J".$i;
	$objPHPExcel->getActiveSheet()->setCellValue('K'.$i, "=SUM(K".$ini.":K".($i-1).")");	$total_segurosocial_salario   .= "+K".$i;
	$objPHPExcel->getActiveSheet()->setCellValue('L'.$i, "=SUM(L".$ini.":L".($i-1).")");	$total_segurosocial_gastosrep .= "+L".$i;
	$objPHPExcel->getActiveSheet()->setCellValue('M'.$i, "=SUM(M".$ini.":M".($i-1).")");	$total_seguro_educativo 	  .= "+M".$i;
	$objPHPExcel->getActiveSheet()->setCellValue('N'.$i, "=SUM(N".$ini.":N".($i-1).")");	$total_isr_salario 			  .= "+N".$i;
	$objPHPExcel->getActiveSheet()->setCellValue('O'.$i, "=SUM(O".$ini.":O".($i-1).")");	$total_isr_gastosrep 		  .= "+O".$i;
	$objPHPExcel->getActiveSheet()->setCellValue('P'.$i, "=SUM(P".$ini.":P".($i-1).")");	$total_descuentos 			  .= "+P".$i;
	$objPHPExcel->getActiveSheet()->setCellValue('Q'.$i, "=SUM(Q".$ini.":Q".($i-1).")");	$total_neto 				  .= "+Q".$i;
}
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

//$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':Q'.$i)->applyFromArray(allBordersThin());

$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':Q'.$i)->applyFromArray(allBordersMedium());

$i++;
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':Q'.$i)->applyFromArray(allBordersThin());	
//$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':Q'.$i);
$i++;
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':Q'.$i)->applyFromArray(allBordersThin());	
//$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':Q'.$i)->getFont()->setName('Calibri');
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':Q'.$i)->getFont()->setSize(12);
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':Q'.$i)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()
			->setCellValue('B'.$i, 'Total')
			->setCellValue('C'.$i, $total_salarios)
			->setCellValue('D'.$i, $total_dietas)
			->setCellValue('E'.$i, $total_combustible)
			->setCellValue('F'.$i, $total_gastosrep)
			->setCellValue('G'.$i, $total_extra_salario)
			->setCellValue('H'.$i, $total_extra_gastosrep)
			->setCellValue('I'.$i, $total_domingo_salario)
			->setCellValue('J'.$i, $total_domingo_gastosrep)
			->setCellValue('K'.$i, $total_segurosocial_salario)
			->setCellValue('L'.$i, $total_segurosocial_gastosrep)
			->setCellValue('M'.$i, $total_seguro_educativo)
			->setCellValue('N'.$i, $total_isr_salario)
			->setCellValue('O'.$i, $total_isr_gastosrep)
			->setCellValue('P'.$i, $total_descuentos)
			->setCellValue('Q'.$i, $total_neto);

$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':Q'.$i)->getNumberFormat()
							  ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_ACCOUNTING);
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':Q'.$i)->getAlignment()
							  ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':Q'.$i)->getBorders()->getBottom()
    		->setBorderStyle(PHPExcel_Style_Border::BORDER_DOUBLE);

foreach ($trabajadores as $key => $registro) 
{
    $aux[$key] = $registro['apellido'];
}

array_multisort($aux, SORT_ASC, $trabajadores);

if($NOMINA != 'TEMPORAL'){
$j = $i;
$i = $i + 2; 
$objPHPExcel->getActiveSheet()
			->setCellValue('B'.$i, 'Cuota Patronal');
$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':K'.$i)->getAlignment()
							  ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':K'.$i)->getFont()->setName('Calibri');
$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':K'.$i)->getFont()->setSize(14);
$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':K'.$i)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->mergeCells('B'.$i.':K'.$i);
$i++;
$ss=$i;
$objPHPExcel->getActiveSheet()
			->setCellValue('B'.$i, 'Seguro Social')
			->setCellValue('C'.$i, "=(C".$j.") * 12.25% ")
			->setCellValue('F'.$i, "=(F".$j.") * 12.25% ")
			->setCellValue('G'.$i, "=(G".$j.") * 12.25% ")
			->setCellValue('H'.$i, "=(H".$j.") * 12.25% ")
			->setCellValue('I'.$i, "=(I".$j.") * 12.25% ")
			->setCellValue('J'.$i, "=(J".$j.") * 12.25% ")
			->setCellValue('K'.$i, "=SUM(C".$i.":J".$i.")");

$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':K'.$i)->applyFromArray(allBordersThin());	
$i++;
$se=$i;
$objPHPExcel->getActiveSheet()
			->setCellValue('B'.$i, 'Seguro Educativo')
			->setCellValue('C'.$i, "=(C".$j.") * 1.5% ")
			->setCellValue('G'.$i, "=(G".$j.") * 1.5% ")
			->setCellValue('I'.$i, "=(I".$j.") * 1.5% ")
			->setCellValue('K'.$i, "=SUM(C".$i.":J".$i.")");

$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':K'.$i)->applyFromArray(allBordersThin());	
$i++;
$rp=$i;
$objPHPExcel->getActiveSheet()
		    ->setCellValue('B'.$i, 'Riesgo Profesional')
		    ->setCellValue('C'.$i, "=(C".$j.") * 5.67% ")
		    ->setCellValue('F'.$i, "=(F".$j.") * 5.67% ")
		    ->setCellValue('G'.$i, "=(G".$j.") * 5.67% ")
		    ->setCellValue('H'.$i, "=(H".$j.") * 5.67% ")
		    ->setCellValue('I'.$i, "=(I".$j.") * 5.67% ")
		    ->setCellValue('J'.$i, "=(J".$j.") * 5.67% ")
		    ->setCellValue('K'.$i, "=SUM(C".$i.":J".$i.")");

$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':K'.$i)->applyFromArray(allBordersThin());	

$i++;
$objPHPExcel->getActiveSheet()
			->setCellValue('C'.$i, "=SUM(C".($i-3).":C".($i-1).")")
			->setCellValue('D'.$i, 0)
			->setCellValue('E'.$i, 0)
			->setCellValue('F'.$i, "=SUM(F".($i-3).":F".($i-1).")")
			->setCellValue('G'.$i, "=SUM(G".($i-3).":G".($i-1).")")
			->setCellValue('H'.$i, "=SUM(H".($i-3).":H".($i-1).")")
			->setCellValue('I'.$i, "=SUM(I".($i-3).":I".($i-1).")")
			->setCellValue('J'.$i, "=SUM(J".($i-3).":J".($i-1).")")
			->setCellValue('K'.$i, "=SUM(K".($i-3).":K".($i-1).")");

$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':K'.$i)->applyFromArray(allBordersMedium());	
$objPHPExcel->getActiveSheet()->getStyle('C'.($i-3).':K'.$i)->getNumberFormat()
							  ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_ACCOUNTING);

$i=$i+2;
$objPHPExcel->getActiveSheet()
			->setCellValue('A'.$i, 'Asiento de Planilla')
			->setCellValue('I'.$i, 'Asiento de Cuota Patronal');

$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':O'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':O'.$i)->getFont()->setName('Calibri');
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':O'.$i)->getFont()->setSize(14);
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':O'.$i)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':D'.$i);
$objPHPExcel->getActiveSheet()->mergeCells('I'.$i.':O'.$i);

$i++;

$objPHPExcel->getActiveSheet()
			->setCellValue('A'.$i, 'Código')
			->setCellValue('B'.$i, 'Nombre de la cuenta')
			->setCellValue('C'.$i, 'Débito')
			->setCellValue('D'.$i, 'Crédito')
			->setCellValue('J'.$i, 'Código')
			->setCellValue('K'.$i, 'Nombre de la cuenta')
			->setCellValue('N'.$i, 'Débito')
			->setCellValue('O'.$i, 'Crédito');

$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':O'.$i)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':O'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':D'.$i)->applyFromArray(allBordersMedium());
$objPHPExcel->getActiveSheet()->getStyle('J'.$i.':O'.$i)->applyFromArray(allBordersMedium());

$i++; $k=$i;
$objPHPExcel->getActiveSheet()
			->setCellValue('B'.$i, 'Gastos de Salario')
			->setCellValue('C'.$i, "=C".$j )
			->setCellValue('K'.$i, 'Gasto de Seguro Social')
			->setCellValue('N'.$i++, "=K".$ss )
			->setCellValue('B'.$i, 'Gastos de Dieta')
			->setCellValue('C'.$i, "=D".$j )
			->setCellValue('K'.$i, 'Gasto de Seguro Educativo')
			->setCellValue('N'.$i++, "=K".$se )
			->setCellValue('B'.$i, 'Gastos de Combustible')
			->setCellValue('C'.$i, "=E".$j )
			->setCellValue('K'.$i, 'Gasto de Riesgos Profesionales')
			->setCellValue('N'.$i++, "=K".$rp )
			->setCellValue('B'.$i, 'Gastos de Representación')
			->setCellValue('C'.$i, "=F".$j )
			->setCellValue('K'.$i, 'Caja de Seguro Social * Pagar')
			->setCellValue('O'.$i++, "=SUM(N".($i-4).":N".($i-1).")" )
			->setCellValue('B'.$i, 'Extra de Salario')
			->setCellValue('C'.$i++, "=G".$j )
			->setCellValue('B'.$i, 'Extra de Gasto de Rep.')
			->setCellValue('C'.$i++, "=H".$j )
			->setCellValue('B'.$i, 'Domingo/Dia Nacional (Salario)')
			->setCellValue('C'.$i++, 0 )
			->setCellValue('B'.$i, 'Domingo/Dia Nacional (G. Represt)')
			->setCellValue('C'.$i++, 0 )
			->setCellValue('B'.$i, 'Seguro Social * Pagar')
			->setCellValue('D'.$i++, "=K".$j."+L".$j )
			->setCellValue('B'.$i, 'Seguro Educ. por Pagar')
			->setCellValue('D'.$i++, "=M".$j )
			->setCellValue('B'.$i, 'Imp. S/ R empleado * pagar')
			->setCellValue('D'.$i++, "=N".$j."+O".$j )
			->setCellValue('B'.$i, 'Desc. Empleado * Pagar')
			->setCellValue('D'.$i++, "=P".$j )
			->setCellValue('B'.$i, 'Salario por Pagar')
			->setCellValue('D'.$i++, "=Q".$j )
			->setCellValue('C'.$i, "=SUM(C".$k.":C".($i-1).")")
			->setCellValue('D'.$i, "=SUM(D".$k.":D".($i-1).")")
			->setCellValue('N'.$i, "=SUM(N".$k.":N".($i-1).")")
			->setCellValue('O'.$i, "=SUM(O".$k.":O".($i-1).")");

$objPHPExcel->getActiveSheet()->getStyle('A'.$k.':D'.($i-1))->applyFromArray(allBordersThin());	
$objPHPExcel->getActiveSheet()->getStyle('J'.$k.':O'.($i-1))->applyFromArray(allBordersThin());	

$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':D'.$i)->applyFromArray(allBordersMedium());
$objPHPExcel->getActiveSheet()->getStyle('N'.$i.':O'.$i)->applyFromArray(allBordersMedium());

$objPHPExcel->getActiveSheet()->getStyle('C'.$k.':D'.$i)->getNumberFormat()
							  ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_ACCOUNTING);
$objPHPExcel->getActiveSheet()->getStyle('N'.$k.':O'.$i)->getNumberFormat()
							  ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_ACCOUNTING);

$kk = $k-1;

while($kk < $i)
{
	$objPHPExcel->getActiveSheet()->mergeCells('K'.$kk.':M'.$kk);
	$kk++;
}

$i = $i + 4;

$objPHPExcel->getActiveSheet()
            ->setCellValue('A'.$i++, strtoupper($empresa) )
            ->setCellValue('A'.$i++, 'PLANILLA QUINCENAL')
            ->setCellValue('A'.$i++, 'DEL '. $dia_ini .' AL '. $dia_fin .' de '. $mes_letras .' '. $anio )
            ->setCellValue('A'.$i++, 'Resumen de Pagos a Realizar' );

$objPHPExcel->getActiveSheet()->getStyle('A'.($i-4).':C'.$i)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('A'.($i-4).':C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->mergeCells('A'.($i-4).':C'.($i-4));
$objPHPExcel->getActiveSheet()->mergeCells('A'.($i-3).':C'.($i-3));
$objPHPExcel->getActiveSheet()->mergeCells('A'.($i-2).':C'.($i-2));
$objPHPExcel->getActiveSheet()->mergeCells('A'.($i-1).':C'.($i-1));

$i++;

$objPHPExcel->getActiveSheet()
            ->setCellValue('B'.$i, 'Colaborador')
            ->setCellValue('C'.$i, 'Monto');

$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':C'.$i)->applyFromArray(allBordersMedium());
$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$i++;
$m=$i;

foreach ($trabajadores as $key => $row) 
{
    //echo $row['nombre'].' '.$row['neto'].'<br/>';
	$objPHPExcel->getActiveSheet()
				->setCellValue('B'.$i, $row['nombre'])
				->setCellValue('C'.$i, $row['neto'] );

	$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':C'.$i)->applyFromArray(allBordersThin());	

	$objPHPExcel->getActiveSheet()->getStyle('C'.$i)->getNumberFormat()
							      ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_ACCOUNTING);
	$i++;
}

$objPHPExcel->getActiveSheet()
			->setCellValue('B'.$i, 'Gran Total')
			->setCellValue('C'.$i, "=SUM(C".$m.":C".($i-1).")" );
$objPHPExcel->getActiveSheet()->getStyle('B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('C'.$i)->getNumberFormat()
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


$objPHPExcel->getActiveSheet()->setTitle($dia_fin.' '.$mes_letras);

//===========================================================================

$objPHPExcel->createSheet();
$objPHPExcel->setActiveSheetIndex(1); 

$objPHPExcel->getActiveSheet()->setTitle('Resumido pago');

$objPHPExcel->getActiveSheet()
			->setCellValue('B1', '                  CONSOLIDADO DE PAGO DE PLANILLA ')
			->setCellValue('B3', 'Empresa')
			->setCellValue('C3', strtoupper($empresa) )
			->setCellValue('E3', 'Fecha de Pago')
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

	cellColor('A'.$i.':F'.$i, 'FFFFFF');

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
$i=$i+2;
$objPHPExcel->getActiveSheet()
			->setCellValue('B'.$i, 'TOTAL PLANILLA NETA QUINCENAL')
			->setCellValue('F'.$i, "=SUM(F7:F".($i-3).")" );

$objPHPExcel->getActiveSheet()->mergeCells('B'.$i.':E'.$i);

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
));

$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':F'.$i)->applyFromArray(allBordersThin());
$objPHPExcel->getActiveSheet()->getStyle('F'.$i)->getNumberFormat()
							  ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(7);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(19);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(17);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(17);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(17);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(18);

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
