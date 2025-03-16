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


//CARGAR LOS CONCEPTOS DE PLANILLA
$sqlpla = "select * from config_reportes_planilla_columnas 
where visible = 1 and id_reporte =1 order by col_orden";
$resCol=$conexion->query($sqlpla);
$desc = [];
while($columnas=$resCol->fetch_array()){
	$desc[ $columnas['nombre_corto'] ] = $columnas['conceptos'];
	
}

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
            ->setCellValue('A2', utf8_encode(strtoupper($empresa)) )
            ->setCellValue('A3', 'PLANILLA VACACIONES')
            ->setCellValue('A4', 'DEL '. $dia_ini .' AL '. $dia_fin .' de '. $mes_letras .' '. $anio );

//$objPHPExcel->getActiveSheet()->getStyle('A2:A4')->getFont()->setName('Calibri');
$objPHPExcel->getActiveSheet()->getStyle('A2:M4')->getFont()->setSize(14);
$objPHPExcel->getActiveSheet()->getStyle('A2:M4')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('A2:M4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->mergeCells('A2:M2');
$objPHPExcel->getActiveSheet()->mergeCells('A3:M3');
$objPHPExcel->getActiveSheet()->mergeCells('A4:M4');

//$objPHPExcel->getActiveSheet()->getStyle('C2:D5')->applyFromArray(allBordersThin());
//$objPHPExcel->getActiveSheet()->getStyle('C2:D5')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$objPHPExcel->getActiveSheet()
            ->setCellValue('A6', 'VACACIONES')
            ->setCellValue('A7', 'No.')
            ->setCellValue('B7', 'NOMBRE')
            ->setCellValue('C7', 'VACACIONES')           
            ->setCellValue('D7', 'VACACIONES G.R.')           
            ->setCellValue('E7', 'SEGURO SOCIAL')
            ->setCellValue('F7', 'SEGURO EDUCATIVO')
            ->setCellValue('G7', 'I.S.R.')
            ->setCellValue('H7', 'I.S.R. VAC.')
            ->setCellValue('I7', 'TOTAL DEDUCCIONES')
            ->setCellValue('J7', 'DESCUENTO COMERCIAL')
            ->setCellValue('K7', 'DESCUENTO EMPRESA')
            ->setCellValue('L7', 'TOTAL C/E')
            ->setCellValue('M7', 'TOTAL');

cellColor('C7:D7', 'ffcc00');
cellColor('E7:M7', '92d050');

//$objPHPExcel->getActiveSheet()->getStyle('A6:Q8')->getFont()->setName('Calibri');
$objPHPExcel->getActiveSheet()->getStyle('A6:M7')->getFont()->setSize(12);
$objPHPExcel->getActiveSheet()->getStyle('A6:M7')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('A6:M7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$objPHPExcel->getActiveSheet()->mergeCells('A6:M6');
$objPHPExcel->getActiveSheet()->getStyle('A6:M7')->applyFromArray(allBordersThin());

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
$res=$conexion->query($sql);

$i=8; $nivel=1;
// $monto_salarios=0;
$total_salarios=$total_comision=$total_combustible=$total_reembolso=$total_uso_auto="=";
$total_extra_salario=$total_extra_gastosrep=$total_vacaciones=$total_cxc="=";
$total_segurosocial_salario=$total_tardanza=$total_seguro_educativo="=";
$total_isr_gastos=$total_isr_salario=$total_ausencia=$total_vac_gr=$descuento_empresa=$descuento_comercial=$total_descuentos=$total_deducciones=$total_neto="=";
$total_salario=$MesXIII=$Total_Desc_Ley=$SeguroSocialMesXIII="=";
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
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':M'.$i)->getFont()->setSize(12);

		$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, $row2['ficha']);
		$objPHPExcel->getActiveSheet()->setCellValue('B'.$i, $trabajador);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':M'.$i)->applyFromArray(allBordersThin());

		$sql3 = "SELECT
				 COALESCE((SELECT n.monto FROM nom_movimientos_nomina n 
  				  WHERE  n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon=114),0) as salario,
				 COALESCE((SELECT n.monto FROM nom_movimientos_nomina n 
  				  WHERE  n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon in (117,157)), 0) as comision,
				 COALESCE((SELECT n.monto FROM nom_movimientos_nomina n 
  				  WHERE  n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon=199), 0) as tardanza,			 
				 COALESCE((SELECT n.monto FROM nom_movimientos_nomina n 
  				  WHERE  n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon in (200,205)), 0) as seguro_social,
				COALESCE((SELECT  n.monto FROM nom_movimientos_nomina n 
  				  WHERE  n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon=201), 0) as seguro_educativo,
				COALESCE((SELECT  n.monto FROM nom_movimientos_nomina n 
  				  WHERE  n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon=208), 0) as seguro_social_vac,
  				COALESCE((SELECT  n.monto FROM nom_movimientos_nomina n 
  				  WHERE  n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon in (202,213,214) ), 0) as islr_vac,
				COALESCE((SELECT  n.monto FROM nom_movimientos_nomina n 
  				  WHERE  n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon=145), 0) as reembolso,
				COALESCE((SELECT  n.monto FROM nom_movimientos_nomina n 
  				  WHERE  n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon in (147,156)), 0) as uso_auto,
				COALESCE((SELECT  n.monto FROM nom_movimientos_nomina n 
  				  WHERE  n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon=203), 0) as ausencia,
 				COALESCE((SELECT  SUM(n.monto) FROM nom_movimientos_nomina n WHERE  n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon IN (202,213, 601, 605)), 0) as isr,
				COALESCE((SELECT  SUM(n.monto) FROM nom_movimientos_nomina n WHERE  n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon IN (207, 208, 606, 607)), 0) as isr_gastos,
				(SELECT  COALESCE(SUM(n.monto), 0) FROM nom_movimientos_nomina n WHERE  n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon IN (".$desc['seguro_social'].",".$desc['seguro_educativo'].",".$desc['isr']."))  as total_deducciones,
				(SELECT  COALESCE(SUM(n.monto), 0) FROM nom_movimientos_nomina n WHERE  n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon IN (".$desc['desc_empresa']."))  as descuento_empresa,
				(SELECT  COALESCE(SUM(n.monto), 0) FROM nom_movimientos_nomina n WHERE  n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon IN (".$desc['desc_acreedores']."))  as descuento_comercial,
				(SELECT  COALESCE(SUM(n.monto), 0) FROM nom_movimientos_nomina n WHERE  n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon IN (".$desc['desc_empresa'].",".$desc['desc_acreedores']."))  as total_descuentos,
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
					AND (  n.codcon IN (106, 108, 142, 143, 183, 184)
					 OR    n.codcon IN (111,116,123, 124, 125, 126, 144, 149, 150, 151)
					 OR    n.codcon IN (107, 109, 152, 153) )";
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

			$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':M'.$i)->getAlignment()
										  ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->setCellValue('C'.$i, $row3['vacaciones']);
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

			$objPHPExcel->getActiveSheet()->setCellValue('E'.$i, $row3['seguro_social']);
			$objPHPExcel->getActiveSheet()->setCellValue('F'.$i, $row3['seguro_educativo']);
			$objPHPExcel->getActiveSheet()->setCellValue('G'.$i, $row3['seguro_social_vac']);
			$objPHPExcel->getActiveSheet()->setCellValue('H'.$i, $row3['islr_vac']);
			$objPHPExcel->getActiveSheet()->setCellValue('I'.$i, $row3['total_deducciones']);
			$objPHPExcel->getActiveSheet()->setCellValue('J'.$i, $row3['descuento_comercial']);
			$objPHPExcel->getActiveSheet()->setCellValue('K'.$i, $row3['descuento_empresa']);
			$objPHPExcel->getActiveSheet()->setCellValue('L'.$i, $row3['total_descuentos']);
			
			$neto2 = $row3['salario'] - $row3['seguro_social'];

			//$objPHPExcel->getActiveSheet()->setCellValue('Q'.$i, $neto);
			$objPHPExcel->getActiveSheet()->setCellValue('M'.$i, "=C$i+D$i-I$i-L$i");
			// =+C38+F38+G38+H38+I38+J38-K38-L38-M38-N38-O38-P38
			$neto = "='".$dia_fin." ".$mes_letras."'!M".$i; //$objPHPExcel->getActiveSheet()->getCell('Q'.$i)->getValue();

			$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':M'.$i)->getNumberFormat()
						->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_ACCOUNTING);
		}

		$trabajadores[] = array('nombre'=>$trabajador, 'primer_nombre'=> $primer_nombre, 'apellido'=>$apellido, 'neto'=> $neto, 'neto2'=>$neto2);

		$i++;
	}

	if($enc)
	{
		$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, 'Total de Vacaciones '. utf8_encode( $row['descrip'] ));

		$objPHPExcel->getActiveSheet()->setCellValue('C'.$i, "=SUM(C".$ini.":C".($i-1).")");	
		$total_vacaciones				  .= "+C".$i;

		$objPHPExcel->getActiveSheet()->setCellValue('D'.$i, "=SUM(D".$ini.":D".($i-1).")");	
		$total_vac_gr					  .= "+D".$i;

		$objPHPExcel->getActiveSheet()->setCellValue('E'.$i, "=SUM(E".$ini.":E".($i-1).")");	
		$total_segurosocial_salario		  .= "+E".$i;

		$objPHPExcel->getActiveSheet()->setCellValue('F'.$i, "=SUM(F".$ini.":F".($i-1).")");	
		$total_seguro_educativo			  .= "+F".$i;

		$objPHPExcel->getActiveSheet()->setCellValue('G'.$i, "=SUM(G".$ini.":G".($i-1).")");	
		$total_isr_salario				  .= "+G".$i;

		$objPHPExcel->getActiveSheet()->setCellValue('H'.$i, "=SUM(H".$ini.":H".($i-1).")");	
		$total_isr_gastos 				  .= "+H".$i;

		$objPHPExcel->getActiveSheet()->setCellValue('I'.$i, "=SUM(I".$ini.":I".($i-1).")");	
		$total_deducciones 				  .= "+I".$i;

		$objPHPExcel->getActiveSheet()->setCellValue('J'.$i, "=SUM(J".$ini.":J".($i-1).")");	
		$descuento_comercial 			  .= "+J".$i;

		$objPHPExcel->getActiveSheet()->setCellValue('K'.$i, "=SUM(K".$ini.":K".($i-1).")");	
		$descuento_empresa 				  .= "+K".$i;

		$objPHPExcel->getActiveSheet()->setCellValue('L'.$i, "=SUM(L".$ini.":L".($i-1).")");	
		$total_descuentos 				  .= "+L".$i;

		$objPHPExcel->getActiveSheet()->setCellValue('M'.$i, "=SUM(M".$ini.":M".($i-1).")");	
		$total_neto 				 	  .= "+M".$i;

		//$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':Q'.$i)->getFont()->setName('Calibri');
		$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':M'.$i)->getFont()->setSize(12);
		$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':M'.$i)->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':M'.$i)->getNumberFormat()
									  ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_ACCOUNTING);

		//$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getFont()->setName('Calibri');
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getFont()->setSize(8);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':B'.$i);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':B'.$i)->getBorders()->getBottom()
	        		->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
		if($nivel==1)
		{
			$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':M'.$i)->applyFromArray(allBordersMedium());
		}
		else
		{
			$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':M'.$i)->applyFromArray(allBordersThin());
		}

		$i++;
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':M'.$i)->applyFromArray(allBordersMedium());	
		$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':M'.$i);
		$i++;	
		$nivel++;
	}
}

//$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':Q'.$i);
$i++;
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':M'.$i)->applyFromArray(allBordersMedium());	
//$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':Q'.$i)->getFont()->setName('Calibri');
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':M'.$i)->getFont()->setSize(12);
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':M'.$i)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()
			->setCellValue('B'.$i, 'Total')
			->setCellValue('C'.$i, $total_vacaciones)
			->setCellValue('D'.$i, $total_vac_gr)
			->setCellValue('E'.$i, $total_segurosocial_salario)
			->setCellValue('F'.$i, $total_seguro_educativo)
			->setCellValue('G'.$i, $total_isr_salario)
			->setCellValue('H'.$i, $total_isr_gastos)
			->setCellValue('I'.$i, $total_deducciones)
			->setCellValue('J'.$i, $descuento_comercial)
			->setCellValue('K'.$i, $descuento_empresa)
			->setCellValue('L'.$i, $total_descuentos)
			->setCellValue('M'.$i, $total_neto);
$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':M'.$i)->getNumberFormat()
			->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_ACCOUNTING);



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


$objPHPExcel->getActiveSheet()->setTitle($dia_fin.' '.$mes_letras);


$NOMINA = str_replace(' ', '', $NOMINA);
$filename = "VACACIONES_Del_".fecha($desde).'_Hasta_'.fecha($hasta);

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
