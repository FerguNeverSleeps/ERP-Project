<?php
session_start();
 ob_start();
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
				DATE_FORMAT(np.periodo_ini,'%d') as dia_ini, DATE_FORMAT(np.periodo_fin, '%d') as dia_fin   , np.status, descrip
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

$NOMINA = $fila2['descrip'];//'PLANILLA DE VACACIONES '; 


$fecha = $anio.'-'.$mes_numero.'-'.$dia_ini;//date('Y-m-j');
$nuevafecha = strtotime ( '-11 month' , strtotime ( $fecha ) ) ;
$nuevafecha = date ( 'Y-m' , $nuevafecha );
$anho_mes=explode('-', $nuevafecha);

$anho_fin =  $anho_mes[0];
$mes_fin  = $anho_mes[1];

$anho_ini =  $anio;
$mes_ini  = $mes_numero;


if($fila2['status']=='A')
	$estatus_mov_nomina ='Aprobado';

else if($fila2['status']=='P')
	$estatus_mov_nomina ='Pendiente';

else
	$estatus_mov_nomina ='';

$empresa = $fila['empresa'];

$i=9; $nivel=1;
// $monto_salarios=0;
$total_salarios=$total_comision=$total_combustible=$total_reembolso=$total_uso_auto=$total_bono="=";
$total_extra_salario=$total_extra_gastosrep=$total_vacaciones=$total_cxc="=";
$total_segurosocial_salario=$total_tardanza=$total_seguro_educativo="=";
$total_isr_gastos=$total_isr_salario=$total_ausencia=$total_descuentos=$total_neto="=";
$total_salario=$MesXIII=$Total_Desc_Ley=$SeguroSocialMesXIII="=";

/*luego lo eliminamos*/
$SEGURO_SOCIAL 		= 0;
$SEGURO_EDUCATIVO  	= 0;
$IMPUESTO_RENTA 	= 0;
$BANK 	 	 		= 0; 

$ASIGNACIONES_ARRAY = array();
$DEDUCCIONES_ARRAY = array();

	$sql2= "SELECT np.ficha, np.cedula, CONCAT_WS(' ', np.nombres, np.apellidos) as nombre,
				   SUBSTRING_INDEX(SUBSTRING_INDEX(np.nombres, ' ', 1), ' ', -1) as primer_nombre,
				   CASE WHEN np.apellidos LIKE 'De %' THEN 
						     SUBSTRING_INDEX(SUBSTRING_INDEX(np.apellidos, ' ', 2), ' ',  2) 
				   ELSE      SUBSTRING_INDEX(SUBSTRING_INDEX(np.apellidos, ' ', 1), ' ', -1) 
				   END as primer_apellido, np.suesal
			FROM   nompersonal np
			WHERE  np.tipnom=".$codtip."
			AND    np.ficha IN (SELECT DISTINCT n.ficha FROM nom_movimientos_nomina n WHERE n.codnom=".$codnom." AND n.tipnom=np.tipnom)
			ORDER BY np.ficha";
	$res2=$conexion->query($sql2);

	$ini=$i; $enc=false;
	while($row2=$res2->fetch_array())
	{	$enc=true;
		$ficha = $row2['ficha'];
		$primer_nombre = utf8_encode($row2['primer_nombre']);
		$apellido = utf8_encode($row2['primer_apellido']);

		$sql3 = "SELECT COALESCE((SELECT SUM(n.monto) AS monto FROM nom_movimientos_nomina n 
  				  WHERE  DATE_FORMAT(CONCAT(n.anio,'-',n.mes,'-01'), '%Y-%m') BETWEEN '".$anho_fin."-".$mes_fin."' AND '".$anho_ini."-".$mes_ini."' AND n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon=100),0) as salario,

				 COALESCE((SELECT SUM(n.monto) AS monto FROM nom_movimientos_nomina n 
  				  WHERE DATE_FORMAT(CONCAT(n.anio,'-',n.mes,'-01'), '%Y-%m') BETWEEN '".$anho_fin."-".$mes_fin."' AND '".$anho_ini."-".$mes_ini."' AND n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon in (141,157)), 0) as comision,

				 COALESCE((SELECT n.monto FROM nom_movimientos_nomina n 
  				  WHERE DATE_FORMAT(CONCAT(n.anio,'-',n.mes,'-01'), '%Y-%m') BETWEEN '".$anho_fin."-".$mes_fin."' AND '".$anho_ini."-".$mes_ini."' AND n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon=199), 0) as tardanza,

				 COALESCE((SELECT n.monto FROM nom_movimientos_nomina n 
  				  WHERE DATE_FORMAT(CONCAT(n.anio,'-',n.mes,'-01'), '%Y-%m') BETWEEN '".$anho_fin."-".$mes_fin."' AND '".$anho_ini."-".$mes_ini."' AND n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon=158), 0) as bono,	

				 COALESCE((SELECT n.monto FROM nom_movimientos_nomina n 
  				  WHERE DATE_FORMAT(CONCAT(n.anio,'-',n.mes,'-01'), '%Y-%m') BETWEEN '".$anho_fin."-".$mes_fin."' AND '".$anho_ini."-".$mes_ini."' AND n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon=200), 0) as seguro_social,

				COALESCE((SELECT  n.monto FROM nom_movimientos_nomina n 
  				  WHERE DATE_FORMAT(CONCAT(n.anio,'-',n.mes,'-01'), '%Y-%m') BETWEEN '".$anho_fin."-".$mes_fin."' AND '".$anho_ini."-".$mes_ini."' AND n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon=201), 0) as seguro_educativo,
				
				COALESCE((SELECT  n.monto FROM nom_movimientos_nomina n 
  				  WHERE DATE_FORMAT(CONCAT(n.anio,'-',n.mes,'-01'), '%Y-%m') BETWEEN '".$anho_fin."-".$mes_fin."' AND '".$anho_ini."-".$mes_ini."' AND n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon=145), 0) as reembolso,

				COALESCE((SELECT  SUM(n.monto) AS monto FROM nom_movimientos_nomina n 
  				  WHERE DATE_FORMAT(CONCAT(n.anio,'-',n.mes,'-01'), '%Y-%m') BETWEEN '".$anho_fin."-".$mes_fin."' AND '".$anho_ini."-".$mes_ini."' AND n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon in (147,156)), 0) as uso_auto,

				COALESCE((SELECT  n.monto FROM nom_movimientos_nomina n 
  				  WHERE DATE_FORMAT(CONCAT(n.anio,'-',n.mes,'-01'), '%Y-%m') BETWEEN '".$anho_fin."-".$mes_fin."' AND '".$anho_ini."-".$mes_ini."' AND n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon=203), 0) as ausencia,

 				COALESCE((SELECT  SUM(n.monto) FROM nom_movimientos_nomina n 
  				  WHERE DATE_FORMAT(CONCAT(n.anio,'-',n.mes,'-01'), '%Y-%m') BETWEEN '".$anho_fin."-".$mes_fin."' AND '".$anho_ini."-".$mes_ini."' AND n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha."
  				  AND    n.codcon = 213), 0) as isr,

				COALESCE((SELECT  SUM(n.monto) FROM nom_movimientos_nomina n 
  				  WHERE DATE_FORMAT(CONCAT(n.anio,'-',n.mes,'-01'), '%Y-%m') BETWEEN '".$anho_fin."-".$mes_fin."' AND '".$anho_ini."-".$mes_ini."' AND n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha."
  				  AND    n.codcon IN (207, 208, 606, 607)), 0) as isr_gastos,

				(SELECT  COALESCE(SUM(n.monto), 0) FROM nom_movimientos_nomina n 
  				  WHERE DATE_FORMAT(CONCAT(n.anio,'-',n.mes,'-01'), '%Y-%m') BETWEEN '".$anho_fin."-".$mes_fin."' AND '".$anho_ini."-".$mes_ini."' AND n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND (n.codcon BETWEEN 500 AND 507 
  				OR n.codcon BETWEEN 508 AND 599))  as descuentos,

				(SELECT COALESCE(SUM(n.monto), 0) FROM nom_movimientos_nomina n 
  				  WHERE DATE_FORMAT(CONCAT(n.anio,'-',n.mes,'-01'), '%Y-%m') BETWEEN '".$anho_fin."-".$mes_fin."' AND '".$anho_ini."-".$mes_ini."' AND n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon=508)  as cxc,

				(SELECT  COALESCE(SUM(n.monto), 0) FROM nom_movimientos_nomina n 
  				  WHERE DATE_FORMAT(CONCAT(n.anio,'-',n.mes,'-01'), '%Y-%m') BETWEEN '".$anho_fin."-".$mes_fin."' AND '".$anho_ini."-".$mes_ini."' AND n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon=114)  as vacaciones, 

  				(SELECT  COALESCE(n.valor, 0) FROM nom_movimientos_nomina n 
  				  WHERE n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon=114)  as dias_vacaciones,
  				    

  				  (SELECT  COALESCE(SUM(n.monto), 0) FROM nom_movimientos_nomina n 
  				  WHERE DATE_FORMAT(CONCAT(n.anio,'-',n.mes,'-01'), '%Y-%m') BETWEEN '".$anho_fin."-".$mes_fin."' AND '".$anho_ini."-".$mes_ini."' AND n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon=506)  as bank ,

  				(SELECT  COALESCE(SUM(n.monto), 0) FROM nom_movimientos_nomina n 
  				  WHERE DATE_FORMAT(CONCAT(n.anio,'-',n.mes,'-01'), '%Y-%m') BETWEEN '".$anho_fin."-".$mes_fin."' AND '".$anho_ini."-".$mes_ini."' AND n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon=516)  as cooperativa_ahorro ,

  				(SELECT  COALESCE(SUM(n.monto), 0) FROM nom_movimientos_nomina n 
  				  WHERE DATE_FORMAT(CONCAT(n.anio,'-',n.mes,'-01'), '%Y-%m') BETWEEN '".$anho_fin."-".$mes_fin."' AND '".$anho_ini."-".$mes_ini."' AND n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon=522)  as internacional_seguro
  				";
  		
		$res3 = $conexion->query($sql3);
		$neto=0;
		if($row3=$res3->fetch_array())
		{
			
			$sql_check= "SELECT  continua_laborando, ultimo_ingreso
					FROM   nom_movimientos_nomina n 
					WHERE  n.codcon=114 
					AND    n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha;

			$res_check = $conexion->query($sql_check);
			$row_check = $res_check->fetch_array();

			// Calcular AJUSTE
			$sql5= "SELECT COALESCE(SUM(monto), 0) AS ajuste
					FROM   nom_movimientos_nomina n 
					WHERE   DATE_FORMAT(CONCAT(n.anio,'-',n.mes,'-01'), '%Y-%m') BETWEEN '".$anho_fin."-".$mes_fin."' AND '".$anho_ini."-".$mes_ini."' AND n.codcon=604 
					AND    n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha;

			$res5 = $conexion->query($sql5);
			if($row5 = $res5->fetch_array() ){ $AJUSTE = $row5['ajuste']; }

			//SE CARGA LA DATA ACUMULADA DE SALARIO_ACUMULADO
			// Calcular AJUSTE
			/*$sql7= "SELECT SUM(n.monto) AS salario, n.anio, n.mes, DATE_FORMAT(CONCAT(n.anio,'-',n.mes,'-01'), '%Y-%m') as fecha, n.codnom, n.tipnom
					FROM   nom_movimientos_nomina n 
					WHERE  DATE_FORMAT(CONCAT(n.anio,'-',n.mes,'-01'), '%Y-%m') 
					BETWEEN '".$anho_fin."-".$mes_fin."' AND '".$anho_ini."-".$mes_ini."' 
					AND (n.codcon in (90,95,100,110,115,169,159,160,161,162,171,105,106,107,108,109,111,112,113,116,120,121,122,123,124,125,126,127,128,129,130,131,132,133,134,135,136,137,138,139,142,143,144,149,150,151,152,153,154,155,163,164,165,166,167,168,172,174) ) 
					AND n.ficha=".$ficha.' GROUP BY codnom';*/

			//NUEVO QUERY de VACACIONEs TOMANDO DATOS DESDE SALARIOS ACUMULADOS ULTIMAS @@ QUINCENAS COBRADAS (SALARIO_BRUTO > 0)

			/*$sql7 = "SELECT `ficha`,`salario_bruto` salario,DATE_FORMAT(`fecha_pago`, '%Y') anio ,DATE_FORMAT(`fecha_pago`, '%m') mes ,
			DATE_FORMAT(`fecha_pago`, '%Y-%m') fecha ,cod_planilla codnom,tipo_planilla tipnom
			FROM salarios_acumulados 
			WHERE fecha_pago < '".$desde."' AND `ficha`='".$ficha."' AND `salario_bruto` > 0 
			ORDER BY `salarios_acumulados`.`fecha_pago` DESC LIMIT 22";exit;*/
			
			//$fecha_inicio = $fila2["periodo_ini"];
			$fechames11 = date('Y-m-d', strtotime('-11 month', strtotime ( $desde ))) ; // resta 11 mes   

			$sql7 = "SELECT (sum(salario_bruto)+SUM(bono)+SUM(otros_ing)+SUM(prima)) as salario, ficha ,DATE_FORMAT(`fecha_pago`, '%Y') anio ,DATE_FORMAT(`fecha_pago`, '%m') mes ,
					DATE_FORMAT(`fecha_pago`, '%Y-%m') fecha ,cod_planilla codnom,tipo_planilla tipnom 
					FROM salarios_acumulados 
					WHERE ficha='".$ficha."' and (fecha_pago>='$fechames11' and fecha_pago<='$desde')
					group by codnom
					order by fecha desc"; 

			$res7 = $conexion->query($sql7);

			$SALARIO_VACACIONES = $res7;					

			//$SALARIO_BASE_ =	0;
			

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
			$dd=0;
			while($deduc=$res6->fetch_array())
			{
				//	
					$DEDUCCIONES_ARRAY[$ficha][$dd]= array('hora' 		=> 0,
															'ingreso' 	=>$deduc['descrip'],
															'periodo' 	=>$deduc['total_deduccion'],
															'mes' 		=>($deduc['total_deduccion']*2),
															'anho' 		=>(($deduc['total_deduccion']*2)*12)
												
														);
					$dd++;				
			}

			$ASIGNACIONES_ARRAY[$ficha][]= array('hora' 	=> 0,
												'ingreso' 	=>'Vacaciones',
												'periodo' 	=>$row3['vacaciones'],
												'mes' 		=>0,
												'anho' 		=>0									
												);
		}

		$trabajadores[] = array('ficha'=>$ficha,'cedula'=>$row2['cedula'],'nombre'=>$trabajador, 'primer_nombre'=> $primer_nombre, 'apellido'=>$apellido, 'continua_laborando'=>$row_check['continua_laborando'],'ultimo_ingreso'=>$row_check['ultimo_ingreso'],'neto'=> 0, 'neto2'=>0, 'sueldo'=> $row2['suesal'],'dias_vacaciones'=> $row3['dias_vacaciones'],'salario_vacaciones'=>$SALARIO_VACACIONES,'ajuste'=>$AJUSTE,'salario_base'=>$row2['suesal'],'seguro_social'=>$SEGURO_SOCIAL,'seguro_educativo'=>$SEGURO_EDUCATIVO,'isr'=>$IMPUESTO_RENTA,'bank'=>$BANK,'vacaciones'=>$vacaciones);

		$i++;
	}	
//} fin while

//$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':Q'.$i);

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


$i++;


} // Fin if NOMINA!='TEMPORAL'

//===========================================================================

/*$objPHPExcel->createSheet();
$objPHPExcel->setActiveSheetIndex(1); */
$objPHPExcel->getActiveSheet()->setTitle('PLANILLAS_VACACIONES');

$objPHPExcel->getActiveSheet()
			//->setCellValue('B1', '')
			->setCellValue('B1', utf8_encode(strtoupper($empresa)) )
			->setCellValue('B2', 'Planilla de Vacaciones')
			//->setCellValue('C1', '')
			->setCellValue('B3', utf8_encode(strtoupper($NOMINA)) )
			->setCellValue('H1', 'Ref:MM-VAC' )
			->setCellValue('H2', 'Cálculo Detallado')
			->setCellValue('H3', $estatus_mov_nomina);								
			//->setCellValue('J2', PHPExcel_Shared_Date::FormattedPHPToExcel($anio, $mes_numero, $dia_fin) )
			

//$objPHPExcel->getActiveSheet()->mergeCells('H6:I6');	
$objPHPExcel->getActiveSheet()->mergeCells('B6:D6');	

$objPHPExcel->getActiveSheet()->getDefaultStyle()->getFont()->setName('Times New Roman');
//$objPHPExcel->getActiveSheet()->getStyle('B1:F1')->getFont()->setName('Times New Roman');
$objPHPExcel->getActiveSheet()->getStyle('B1:G1')->getFont()->setSize(16);
$objPHPExcel->getActiveSheet()->getStyle('B1:B1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->mergeCells('B1:G1');

$objPHPExcel->getActiveSheet()->getStyle('B3:G3')->getFont()->setSize(12);
$objPHPExcel->getActiveSheet()->getStyle('B3:G3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->mergeCells('B3:G3');

$objPHPExcel->getActiveSheet()->getStyle('B2:G2')->getFont()->setSize(12);
$objPHPExcel->getActiveSheet()->getStyle('B2:G2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->mergeCells('B2:G2');
$objPHPExcel->getActiveSheet()->getRowDimension('3')->setRowHeight(30);
cellColor('A1:H6', 'FFFFFF');




$objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(20);
//$objPHPExcel->getActiveSheet()->getRowDimension('6')->setRowHeight(40);
$objPHPExcel->getActiveSheet()->getStyle('B3')->applyFromArray(
array(
	'font'      => array(
		//'name'         => 'Times New Roman',
		'bold'         => true,
		//'color'        => array('rgb' => '800000'),
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


$i=7;

$cont=1;
foreach ($trabajadores as $key => $row) 
{
	

   
	$objPHPExcel->getActiveSheet()
				->setCellValue('A'.$i, $cont )
				->setCellValue('B'.$i, $row['apellido'].', '.$row['primer_nombre'])
				->setCellValue('G'.$i, 'Ced.:' )
				->setCellValue('H'.$i, $row['cedula'] );
	$objPHPExcel->getActiveSheet()->getStyle('G'.$i.':H'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
	//$TotalAPagar+=$row['neto'];

	$i++; 
	$i++;

	$objPHPExcel->getActiveSheet()->getStyle('B'.($i).':H'.($i))->getBorders()->getBottom()
        					      ->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);

    $objPHPExcel->getActiveSheet()->getStyle('B'.($i).':H'.($i))->getBorders()->getTop()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);

	$objPHPExcel->getActiveSheet()->getStyle('B'.($i))->getBorders()->getLeft()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);  

    $objPHPExcel->getActiveSheet()->getStyle('H'.($i))->getBorders()->getRight()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);  
    $continua_laborando = '';
	$ultimo_ingreso 	= '';  					  
    if($row['continua_laborando']==1)
    	$continua_laborando = '*';
    if($row['ultimo_ingreso']==1)
    	$ultimo_ingreso 	= '*';
    
	$objPHPExcel->getActiveSheet()				
				->setCellValue('B'.($i), 'Opciones:')
				->setCellValue('C'.($i), '['.$continua_laborando.'] Cobra y continúa laborando.')
				->setCellValue('E'.($i), '['.$ultimo_ingreso.'] Ultimo ingreso del año (Cuadrar Anexo 03).' );
	
	$objPHPExcel->getActiveSheet()->mergeCells('C'.$i.':D'.$i);
	$objPHPExcel->getActiveSheet()->mergeCells('E'.$i.':G'.$i);

    //---------------------------------------------------------------------------------------------------------
	$i++;
	$i++;
	$objPHPExcel->getActiveSheet()				
				->setCellValue('B'.($i), strtoupper('Promedios desde '.fecha($desde).' al '.fecha($hasta)));
	$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':G'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);				
	$objPHPExcel->getActiveSheet()->mergeCells('B'.$i.':G'.$i);	

	$objPHPExcel->getActiveSheet()->getStyle('B'.($i).':H'.($i))->getBorders()->getTop()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);			
	$objPHPExcel->getActiveSheet()->getStyle('B'.($i))->getBorders()->getLeft()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);  
    $objPHPExcel->getActiveSheet()->getStyle('H'.($i))->getBorders()->getRight()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	//----------------------------------------------------------------------------------------------------------				
	$i++;				
	$objPHPExcel->getActiveSheet()				
				->setCellValue('B'.($i), 'Salarios')
				->setCellValue('F'.($i), 'Gastos de Representación');	
	$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);				
	$objPHPExcel->getActiveSheet()->mergeCells('B'.$i.':D'.$i);	
	$objPHPExcel->getActiveSheet()->mergeCells('F'.$i.':G'.$i);			
	$objPHPExcel->getActiveSheet()->getStyle('B'.($i))->getBorders()->getLeft()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);  

    $objPHPExcel->getActiveSheet()->getStyle('H'.($i))->getBorders()->getRight()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);		
	
    $objPHPExcel->getActiveSheet()->getStyle('B'.($i).':H'.($i))->getBorders()->getBottom()
        					      ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	
	//----------------------------------------------------------------------------------------------------------
	$i++;
	$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':D'.$i)->applyFromArray(allBordersThin());
	$objPHPExcel->getActiveSheet()				
				->setCellValue('B'.($i), 'Fecha')
				->setCellValue('C'.($i), 'Tipo de Planilla')
				->setCellValue('D'.($i), 'Salario');
	$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);					
	


    $SUB_TOTAL			= 0;
    $AJUSTAR_ACUMULADO	= 0;
    $ACUMULADO 			= 0;
    $PROMEDIO 			= 0;
    $SALARIO_BASE 		= 0;
    $contador 			= 0;
	while($valor=$row['salario_vacaciones']->fetch_array()){
		$i++;
		$contador++;
		$objPHPExcel->getActiveSheet()				
					->setCellValue('B'.($i), $valor['mes'].'-'.$valor['anio'])
					->setCellValue('C'.($i), 'Regular')
					->setCellValue('D'.($i), $valor['salario']);
		$SUB_TOTAL+=$valor['salario'];					
		
		$objPHPExcel->getActiveSheet()->getStyle('D'.$i)->getNumberFormat()
							      ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
		//$objPHPExcel->getActiveSheet()->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		$objPHPExcel->getActiveSheet()->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':D'.$i)->applyFromArray(allBordersThin());			      
	}

	$i++;

	$AJUSTAR_ACUMULADO	= $row['ajuste'];
	$ACUMULADO 			= $AJUSTAR_ACUMULADO+$SUB_TOTAL;
	$PROMEDIO 			= 0;
	$SALARIO_BASE 		= $row['salario_base'];//$SUB_TOTAL/$contador;//por ahora

	$TOTAL_INGRESO 		= 0;
	$TOTAL_RETENCION 	= 0;

	$PROMEDIO = $ACUMULADO/11;


	$i++;
	$objPHPExcel->getActiveSheet()				
				->setCellValue('B'.($i), 'Sub Total:')		
				->setCellValue('D'.($i), $SUB_TOTAL);
	$objPHPExcel->getActiveSheet()->mergeCells('B'.$i.':C'.$i);
	$objPHPExcel->getActiveSheet()->getStyle('B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
	

	$objPHPExcel->getActiveSheet()->getStyle('D'.$i)->getNumberFormat()
							      ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

	$objPHPExcel->getActiveSheet()				
				->setCellValue('F'.($i), 'Sub Total:')			
				->setCellValue('H'.($i), '0.00');
	$objPHPExcel->getActiveSheet()->mergeCells('F'.$i.':G'.$i);
	$objPHPExcel->getActiveSheet()->getStyle('F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
	$objPHPExcel->getActiveSheet()->getStyle('H'.$i)->getNumberFormat()
							      ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

	//---------------------
	$i++;
	$objPHPExcel->getActiveSheet()				
				->setCellValue('B'.($i), 'Ajustar Acumulado:')			
				->setCellValue('D'.($i), $AJUSTAR_ACUMULADO);		
	$objPHPExcel->getActiveSheet()->mergeCells('B'.$i.':C'.$i);				
	$objPHPExcel->getActiveSheet()->getStyle('B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
	$objPHPExcel->getActiveSheet()->getStyle('D'.$i)->getNumberFormat()
							      ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

	$objPHPExcel->getActiveSheet()				
				->setCellValue('F'.($i), 'Ajustar Acumulado:')			
				->setCellValue('H'.($i), '0.00');
	$objPHPExcel->getActiveSheet()->mergeCells('F'.$i.':G'.$i);
	$objPHPExcel->getActiveSheet()->getStyle('F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
	$objPHPExcel->getActiveSheet()->getStyle('H'.$i)->getNumberFormat()
							      ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	//---------------------

	$i++;
	$objPHPExcel->getActiveSheet()				
				->setCellValue('B'.($i), 'Acumulado:')			
				->setCellValue('D'.($i), $ACUMULADO );	
	$objPHPExcel->getActiveSheet()->getStyle('D'.$i)->getNumberFormat()
							      ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->mergeCells('B'.$i.':C'.$i);
	$objPHPExcel->getActiveSheet()->getStyle('B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

	$objPHPExcel->getActiveSheet()				
				->setCellValue('F'.($i), 'Acumulado:')			
				->setCellValue('H'.($i), '0.00');
	$objPHPExcel->getActiveSheet()->mergeCells('F'.$i.':G'.$i);
	$objPHPExcel->getActiveSheet()->getStyle('F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
	$objPHPExcel->getActiveSheet()->getStyle('H'.$i)->getNumberFormat()
							      ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	//---------------------

	$i++;
	$objPHPExcel->getActiveSheet()				
				->setCellValue('B'.($i), 'Promedio = Acumulado / 11.0')			
				->setCellValue('D'.($i), $PROMEDIO);	
	$objPHPExcel->getActiveSheet()->getStyle('D'.$i)->getNumberFormat()
							      ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->mergeCells('B'.$i.':C'.$i);		
	$objPHPExcel->getActiveSheet()->getStyle('B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

	$objPHPExcel->getActiveSheet()				
				->setCellValue('F'.($i), 'Promedio = Acumulado / 11.0:')			
				->setCellValue('H'.($i), '0.00');
	$objPHPExcel->getActiveSheet()->mergeCells('F'.$i.':G'.$i);
	$objPHPExcel->getActiveSheet()->getStyle('F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
	$objPHPExcel->getActiveSheet()->getStyle('H'.$i)->getNumberFormat()
							      ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	//---------------------

	$i++;
	$objPHPExcel->getActiveSheet()				
				->setCellValue('B'.($i), 'Salario Base:')			
				->setCellValue('D'.($i), /*$row['salario_base']*/$SALARIO_BASE);	
	$objPHPExcel->getActiveSheet()->getStyle('D'.$i)->getNumberFormat()
							      ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);				
	$objPHPExcel->getActiveSheet()->mergeCells('B'.$i.':C'.$i);		
	$objPHPExcel->getActiveSheet()->getStyle('B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);		

	$objPHPExcel->getActiveSheet()				
				->setCellValue('F'.($i), 'Gasto de Representación:')			
				->setCellValue('H'.($i), '0.00');
	$objPHPExcel->getActiveSheet()->mergeCells('F'.$i.':G'.$i);
	$objPHPExcel->getActiveSheet()->getStyle('F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
	$objPHPExcel->getActiveSheet()->getStyle('H'.$i)->getNumberFormat()
							      ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);


	$objPHPExcel->getActiveSheet()->getStyle('D'.($i-5).':D'.$i)->applyFromArray(allBordersThin());

	$objPHPExcel->getActiveSheet()->getStyle('H'.($i-5).':H'.$i)->applyFromArray(allBordersThin());

	
	$i++;			
	$i++;		
	$objPHPExcel->getActiveSheet()				
				->setCellValue('B'.($i), 'Transacciones Generadas para '.number_format($row['dias_vacaciones'], 0, ',', '').' dias de Vacaciones Tomadas');			
	$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':H'.$i)->getFont()->setSize(14);
	$objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(20);

	$objPHPExcel->getActiveSheet()->mergeCells('B'.$i.':H'.$i);		
	$objPHPExcel->getActiveSheet()->getStyle('B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

	$objPHPExcel->getActiveSheet()->getStyle('B'.($i).':H'.$i)->applyFromArray(allBordersThin());

	$i++;
	$objPHPExcel->getActiveSheet()				
				->setCellValue('B'.($i), 'Detalle')			
				->setCellValue('E'.($i), 'Ingreso')
				->setCellValue('F'.($i), 'Retencion')
				->setCellValue('G'.($i), 'Saldo')
				->setCellValue('H'.($i), 'Documento');	
	$objPHPExcel->getActiveSheet()->mergeCells('B'.$i.':D'.$i);		
	$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':H'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);	

	$objPHPExcel->getActiveSheet()->getStyle('B'.($i).':H'.$i)->applyFromArray(allBordersThin());
	$i++;


	foreach ($ASIGNACIONES_ARRAY[$row['ficha']] as $key => $value) {			
			//foreach ($value as $key2 =>$asignacion) {
		$objPHPExcel->getActiveSheet()				
					->setCellValue('B'.($i), strtoupper($value['ingreso']))			
					->setCellValue('E'.($i), $value['periodo'])
					->setCellValue('F'.($i), '')
					->setCellValue('G'.($i), '')
					->setCellValue('H'.($i), '');	
		$objPHPExcel->getActiveSheet()->mergeCells('B'.$i.':D'.$i);		
		$objPHPExcel->getActiveSheet()->getStyle('D'.$i.':H'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$objPHPExcel->getActiveSheet()->getStyle('D'.$i.':H'.$i)->getNumberFormat()
							      ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);	

		$TOTAL_INGRESO += $value['periodo'];
		$objPHPExcel->getActiveSheet()->getStyle('B'.($i).':H'.$i)->applyFromArray(allBordersThin());				
		$i++;
	}	

		foreach ($DEDUCCIONES_ARRAY[$row['ficha']] as $key => $value) {		
			$objPHPExcel->getActiveSheet()				
						->setCellValue('B'.($i), strtoupper($value['ingreso']))			
						->setCellValue('E'.($i), '')
						->setCellValue('F'.($i), $value['periodo'])
						->setCellValue('G'.($i), '')
						->setCellValue('H'.($i), '');	
			$objPHPExcel->getActiveSheet()->mergeCells('B'.$i.':D'.$i);		
			$objPHPExcel->getActiveSheet()->getStyle('D'.$i.':H'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			$objPHPExcel->getActiveSheet()->getStyle('D'.$i.':H'.$i)->getNumberFormat()
								      ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);	
			$TOTAL_RETENCION += $value['periodo'];
			$objPHPExcel->getActiveSheet()->getStyle('B'.($i).':H'.$i)->applyFromArray(allBordersThin());
			$i++;
			//}			
		}						    
	
	$objPHPExcel->getActiveSheet()				
				->setCellValue('B'.($i), 'Totales:')
				->setCellValue('E'.($i), $TOTAL_INGRESO)
				->setCellValue('F'.($i), $TOTAL_RETENCION)
				->setCellValue('G'.($i), 'Neto a Pagar:')
				->setCellValue('H'.($i), $TOTAL_INGRESO-$TOTAL_RETENCION);	
	$objPHPExcel->getActiveSheet()->mergeCells('B'.$i.':D'.$i);		
	$objPHPExcel->getActiveSheet()->getStyle('D'.$i.':H'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
	$objPHPExcel->getActiveSheet()->getStyle('D'.$i.':H'.$i)->getNumberFormat()
							      ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);	

	$objPHPExcel->getActiveSheet()->getStyle('B'.($i).':H'.$i)->applyFromArray(allBordersThin());
	
	$i++;
	$i++;
	$i++;
	$i++;
	$i++;
	$i++;

	$objPHPExcel->getActiveSheet()			
				->setCellValue('B'.$i,'Elaborado por' )
				->setCellValue('F'.$i,'Aprobado por' );
	$objPHPExcel->getActiveSheet()->mergeCells('B'.$i.':D'.$i);	
	$objPHPExcel->getActiveSheet()->mergeCells('F'.$i.':H'.$i);	

	$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':D'.$i)->getBorders()->getTop()
	        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('F'.$i.':H'.$i)->getBorders()->getTop()
	        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('F'.$i.':H'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

	cellColor('A'.$i.':E'.$i, 'FFFFFF');

	$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':F'.$i)->getFont()->setSize(12);
	$objPHPExcel->getActiveSheet()->getStyle('B'.$i)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('B'.$i)->getFont()->setItalic(true);
	/*$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':F'.$i)->getBorders()->getBottom()
        					      ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);*/

	$objPHPExcel->getActiveSheet()->getStyle('F'.$i)->getNumberFormat()
							      ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$i++;
	$i++;
	$i++;

	$cont++;
}



cellColor('A6'.':H'.($i+2), 'FFFFFF');
$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':F'.$i)->getBorders()->getBottom()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$i++;
$objPHPExcel->getActiveSheet()->getStyle('C'.$i)->getFont()->setSize(12);
$objPHPExcel->getActiveSheet()->getStyle('C'.$i)->getFont()->setBold(true);


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
//$objPHPExcel->setActiveSheetIndex(0); 
$objPHPExcel->getActiveSheet()->setSelectedCells('I30');



$NOMINA = str_replace(' ', '', $NOMINA);
$filename = "PLANILLA_VACACIONES_DEL_".fecha($desde).'_Hasta_'.fecha($hasta);

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
ob_end_clean();
flush();
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
}
exit;
