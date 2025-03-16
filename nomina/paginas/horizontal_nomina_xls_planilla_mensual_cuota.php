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


$sql = "SELECT e.nom_emp as empresa, e.rif, e.tel_emp as telefono, e.dir_emp as direccion, e.ciu_emp as ciudad, e.pre_sid, 
               e.edo_emp, e.imagen_izq as logo
		FROM   nomempresa e";
$res=$conexion->query($sql);
$fila=$res->fetch_array();
$logo=$fila['logo'];


$direccion_fiscal_empresa	= $fila['direccion'];
$cedula_juridica 			= $fila['rif'];
$representante_legal		= $fila['pre_sid'];
$correlativo 				= '1000';
$lugar_de_trabajo 			= $fila['direccion'];
$actividad_economica = $fila['actividad_economica'];
$cedula_natural     = $fila['cedula_natural'];
$representante_licencia     = $fila['representante_licencia'];
$representante_telef     = $fila['representante_telef'];
$numero_patronal     = $fila['numero_patronal'];


$sql2 = "SELECT np.periodo_ini as desde, np.periodo_fin as hasta, np.mes, np.anio,
				DATE_FORMAT(np.periodo_ini,'%d') as dia_ini, DATE_FORMAT(np.periodo_fin, '%d') as dia_fin, np.status    
		 FROM nom_nominas_pago np 
         WHERE  np.codnom IN ({$codnom}) AND np.tipnom=".$codtip;
$res2=$conexion->query($sql2);
//$fila2=$res2->fetch_array();

$meses = array('Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre');

$cont =0;
while ($fila2=$res2->fetch_array()) {	
	if($cont==0){
		$fecha_desde = strtotime($fila2['desde']);
		$fecha_hasta = strtotime($fila2['hasta']);
		$desde = $fila2['desde'];
		$hasta = $fila2['hasta'];
	}
	if($cont>0){
		if(strtotime($fila2['desde']) < $fecha_desde){
			$desde = $fila2['desde'];
		}
		if(strtotime($fila2['hasta']) > $fecha_hasta){
			$hasta = $fila2['hasta'];
		}
		/*$fecha_desde = strtotime($fila2['desde']);
		$fecha_hasta = strtotime($fila2['hasta']);*/
	}
	$cont++;
	//$desde=$fila2['desde'];
	//$hasta=$fila2['hasta'];
	$dia_ini = $fila2['dia_ini'];
	$dia_fin = $fila2['dia_fin']; 
	$mes_numero = $fila2['mes'];
	$mes_letras = $meses[$mes_numero - 1];
	$anio = $fila2['anio'];

	if($fila2['status']=="A"){
		$estatus_mov_nomina ="Aprobado";
	}

	else if($fila2['status']=="P"){
		$estatus_mov_nomina ="Pendiente";
	}

	else{
		$estatus_mov_nomina = "";
	}
}




$empresa = $fila['empresa'];


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
//$check_deducciones = false;//
//$check_asignaciones = false;
//$total_seguro_educativo2 ='';
//$total_segurosocial_salario2='';
//$ASIGNACIONES_ARRAY = array();
//$DEDUCCIONES_ARRAY = array();
//$ASIGNACIONES_ARRAY_GENERAL = array();
//$DEDUCCIONES_ARRAY_GENERAL = array();

//$CANTIDAD_PAGO_CHEQUE=0;
//$CANTIDAD_PAGO_TRANSFERENCA=0;
//$CANTIDAD_PAGO_EFECTIVO=0;
//$CANTIDAD_VACACIONES=0;
//$CANTIDAD_ACTIVO=0;
//$MONTO_TOTAL_TRANSFERENCIA = 0;
//$MONTO_TOTAL_CHEQUE 	= 0;
//$MONTO_TOTAL_EFECTIVO 	= 0;
$RIESGOS_PROFECIONAL 			= 0;
$SEGURO_EDUCATIVO_EMPLEADO 		= 0;
$SEGURO_EDUCATIVO_PATRONAL 		= 0;
$SEGURO_SOCIAL_EMPLEADO 		= 0;
$SEGURO_SOCIAL_PATRONAL 		= 0;
$IMPUESTO_SOBRE_RENTA_EMPLEADO 	= 0;
$IMPUESTO_SOBRE_RENTA_PATRONAL 	= 0;






while($row=$res->fetch_array())
{
	$codorg = $row['codorg'];
	$grupo 	= $row['grupo'];

	$sql2= "SELECT np.ficha, np.cedula,  np.suesal , np.hora_base, CONCAT_WS(' ', np.nombres, np.apellidos) as nombre, np.estado, SUBSTRING(np.sexo,1,1) as sexo_short, np.sexo as sexo_long, np.seguro_social, 
				   SUBSTRING_INDEX(SUBSTRING_INDEX(np.nombres, ' ', 1), ' ', -1) as primer_nombre,
				   CASE WHEN np.apellidos LIKE 'De %' THEN 
						     SUBSTRING_INDEX(SUBSTRING_INDEX(np.apellidos, ' ', 2), ' ',  2) 
				   ELSE      SUBSTRING_INDEX(SUBSTRING_INDEX(np.apellidos, ' ', 1), ' ', -1) 
				   END as primer_apellido
			FROM   nompersonal np
			WHERE  np.codnivel1=".$codorg." AND np.tipnom=".$codtip."
			AND    np.ficha IN (SELECT DISTINCT n.ficha FROM nom_movimientos_nomina n WHERE n.codnom IN ({$codnom}) AND n.tipnom=np.tipnom)
			ORDER BY np.ficha";

	$res2=$conexion->query($sql2);

	$ini=$i; $enc=false;
	
	while($row2=$res2->fetch_array())
	{	$enc=true;
		$ficha = $row2['ficha'];
		$cedula = $row2['cedula'];
		$primer_nombre = utf8_encode($row2['primer_nombre']);
		$apellido   = utf8_encode($row2['primer_apellido']);
		$trabajador = utf8_encode($row2['primer_nombre']).' '.utf8_encode($row2['primer_apellido']); // $row2['nombre']
		$sexo_short 	= $row2['sexo_short'];
		$sexo_long 		= $row2['sexo_long'];
		$numero_seguro_social 	= $row2['seguro_social'];
		$check_vacacion = '';
		$suesal 		= $row2['suesal'];
		$hora_base 		= $row2['hora_base'];
		$rata_por_hora 	= (($suesal/4.3333)/$hora_base);
		$rata_por_hora	= number_format($rata_por_hora,2);
		$asignaciones_monto 	= 0;
		$deducciones_monto  	= 0;
		$salario_100 			= 0;
		$monto_vacaciones 		= 0;
		$gastos_representacion	= 0;
		

		/*else{
			$check_vacacion = '';//
		}*/

		//$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':Q'.$i)->getFont()->setName('Calibri');
		

		$sql3 = "SELECT
				 COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
  				  WHERE  n.codnom IN ({$codnom}) AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon=100),0) as salario,
				 COALESCE((SELECT SUM(n.monto) AS monto FROM nom_movimientos_nomina n 
  				  WHERE  n.codnom IN ({$codnom}) AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon in (141,157)), 0) as comision,  				
				 COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
  				  WHERE  n.codnom IN ({$codnom}) AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon=198), 0) as tardanza,			 
				 COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
  				  WHERE  n.codnom IN ({$codnom}) AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon=158), 0) as bono,		 
				 COALESCE((SELECT SUM(n.monto) FROM nom_movimientos_nomina n 
  				  WHERE  n.codnom IN ({$codnom}) AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon=200), 0) as seguro_social,
				COALESCE((SELECT  SUM(n.monto) FROM nom_movimientos_nomina n 
  				  WHERE  n.codnom IN ({$codnom}) AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon=201), 0) as seguro_educativo,
				COALESCE((SELECT  SUM(n.monto) FROM nom_movimientos_nomina n 
  				  WHERE  n.codnom IN ({$codnom}) AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon=208), 0) as isr_gastos_representativos,
				COALESCE((SELECT  SUM(n.monto) FROM nom_movimientos_nomina n 
  				  WHERE  n.codnom IN ({$codnom}) AND n.ficha=".$ficha." AND n.codcon=145), 0) as reembolso,
				COALESCE((SELECT  SUM(n.monto) AS monto FROM nom_movimientos_nomina n 
  				  WHERE  n.codnom IN ({$codnom}) AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon in (147,156)), 0) as uso_auto,
				COALESCE((SELECT  SUM(n.monto) FROM nom_movimientos_nomina n 
  				  WHERE  n.codnom IN ({$codnom}) AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon=199), 0) as ausencia,
 				COALESCE((SELECT  SUM(n.monto) FROM nom_movimientos_nomina n 
  				  WHERE  n.codnom IN ({$codnom}) AND n.tipnom=".$codtip." AND n.ficha=".$ficha."
  				  AND    n.codcon IN (202, 601, 605)), 0) as isr,
				COALESCE((SELECT  SUM(n.monto) FROM nom_movimientos_nomina n 
  				  WHERE  n.codnom IN ({$codnom}) AND n.tipnom=".$codtip." AND n.ficha=".$ficha."
  				  AND    n.codcon IN (207, 208, 606, 607)), 0) as isr_gastos,
				(SELECT  COALESCE(SUM(n.monto), 0) FROM nom_movimientos_nomina n 
  				  WHERE  n.codnom IN ({$codnom}) AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND (n.codcon BETWEEN 500 AND 507 
  				OR n.codcon BETWEEN 508 AND 599))  as descuentos,
				(SELECT  COALESCE(SUM(n.monto), 0) FROM nom_movimientos_nomina n 
  				  WHERE  n.codnom IN ({$codnom}) AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon=508)  as cxc,
				(SELECT  COALESCE(SUM(n.monto), 0) FROM nom_movimientos_nomina n 
  				  WHERE  n.codnom IN ({$codnom}) AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon=114)  as vacaciones,
  				COALESCE((SELECT  SUM(n.monto) FROM nom_movimientos_nomina n 
  				  WHERE  n.codnom IN ({$codnom}) AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon=175), 0) as prima_produccion,
  				COALESCE((SELECT  SUM(n.monto) FROM nom_movimientos_nomina n 
  				  WHERE  n.codnom IN ({$codnom}) AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon=102), 0) as xiii_mes,
  				COALESCE((SELECT  SUM(n.monto) FROM nom_movimientos_nomina n 
  				  WHERE  n.codnom IN ({$codnom}) AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon=9009), 0) as riesgos_profeconal,
  				COALESCE((SELECT  SUM(n.monto) FROM nom_movimientos_nomina n 
  				  WHERE  n.codnom IN ({$codnom}) AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon=9003), 0) as seguro_social_patronal,

  				COALESCE((SELECT  SUM(n.monto) FROM nom_movimientos_nomina n 
  				  WHERE  n.codnom IN ({$codnom}) AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon=9004), 0) as seguro_educativo_patronal
  				  
  				  ";
  				  //
				 //echo $sql3;exit;
		$res3 = $conexion->query($sql3);
		$neto=0;

		if($row3=$res3->fetch_array())
		{
			$MONTO_EXTRA_SALARIO = $MONTO_DOMINGO_SALARIO = 0;
			$MONTO_GR_EXTRA_SALARIO = $MONTO_GR_DOMINGO_SALARIO = 0;
			$AJUSTE = 0;
			$gastos_representacion 	= 0;
			$prima_produccion 		= 0;
			$isr_gastos_representativos=0;
			$xiii_mes 				= 0;



			$sql5 = "SELECT COALESCE(SUM(n.monto), 0) AS total_extra_salario  
					FROM   nom_movimientos_nomina n 
					WHERE  n.codnom IN ({$codnom}) AND n.tipnom=".$codtip." AND n.ficha=".$ficha."
					AND (  n.codcon IN (106, 108, 142, 143, 153, 152)
					 OR    n.codcon IN (111,116,123, 124, 125, 126, 144, 149, 150, 151)
					 OR    n.codcon IN (107, 109, 152, 153, 120, 121, 127, 122,112, 113, 127, 128, 129, 130, 131, 132,133, 134, 135, 136, 138, 139) )";
			$res5 =$conexion->query($sql5);
			if($row5=$res5->fetch_array()){ $MONTO_EXTRA_SALARIO = $row5['total_extra_salario']; }

			$sql5 = "SELECT COALESCE(SUM(n.monto), 0) AS total_domingo_salario  
					 FROM   nom_movimientos_nomina n 
					 WHERE  n.codnom IN ({$codnom}) AND n.tipnom=".$codtip." AND n.ficha=".$ficha."
					 AND (  n.codcon IN (112, 113, 127, 128, 129, 130, 131, 132)
					  OR    n.codcon IN (133, 134, 135, 136, 138, 139) )";
			$res5 =$conexion->query($sql5);


			// Calcular AJUSTE
			$sql5= "SELECT COALESCE(SUM(monto), 0) AS ajuste
					FROM   nom_movimientos_nomina n 
					WHERE  n.codcon=604
					AND    n.codnom IN ({$codnom}) AND n.tipnom=".$codtip." AND n.ficha=".$ficha;

			$res5 = $conexion->query($sql5);
			if($row5 = $res5->fetch_array() ){ $AJUSTE = $row5['ajuste']; }

			//$impuesto = ($row3['isr_gastos'] >0) ? $row3['isr'] - $row3['isr_gastos'] : $row3['isr'];

			
			

			//======================================================================================
			// HORAS EXTRA SALARIO
			if( ($MONTO_EXTRA_SALARIO + $MONTO_DOMINGO_SALARIO) <= 0 && $row3['horas_extras'] > 0 )
			{
				
				$HORAS_EXTRA_SALARIO = $row3['horas_extras'];
			}
			else
			{
				
				$HORAS_EXTRA_SALARIO = $MONTO_EXTRA_SALARIO + $MONTO_DOMINGO_SALARIO;			
			}

		
			$salario_trabajador=$row3['salario']+$row3['uso_auto']+$row3['comision']+$row3['bono']+$HORAS_EXTRA_SALARIO+$row3['reembolso']+$row3['vacaciones']-$row3['tardanza']-$row3['ausencia'];
					
			
			
			$neto2 = $row3['salario'] + $row3['vacaciones']+ $row3['reembolso']+ $row3['comision']+ $row3['bono'] + $HORAS_EXTRA_SALARIO 
				   - $row3['seguro_social'] - $row3['seguro_educativo'] - $row3['tardanza'] - $row3['ausencia']
				   - $row3['isr'] - $row3['isr_gastos'] - $row3['descuentos'] - $row3['cxc']- $AJUSTE;

			//$objPHPExcel->getActiveSheet()->setCellValue('Q'.$i, $neto);
			
			// =+C38+F38+G38+H38+I38+J38-K38-L38-M38-N38-O38-P38
			$neto = "='".$dia_fin." ".$mes_letras."'!R".$i; //$objPHPExcel->getActiveSheet()->getCell('Q'.$i)->getValue();
						
			$clave_isr 		= '-';			
			$ingreso_regular= $salario_trabajador;
			
			$otros_ingresos = $row3['comision']+ $row3['bono'];
			$total_Ingresos = $ingreso_regular+$otros_ingresos;

			$renta 			= '';
			$impuesto 		= $row3['isr'];
			$seguro_educativo = $row3['seguro_educativo'];
			$seguro_social = $row3['seguro_social'];
			$total_retener =  $row3['seguro_social'] + $row3['seguro_educativo'] + $row3['tardanza'] + $row3['ausencia'] + $row3['isr'] +$row3['isr_gastos'] + $row3['descuentos'] + $row3['cxc']- $AJUSTE;
			//----------------------------------------
			$gastos_representacion 	= $row3['reembolso'];	
			$monto_vacaciones 		= $row3['vacaciones'];	
			$salario_100 			= $row3['salario'];	
			$prima_produccion 		= $row3['prima_produccion'];	 
			$isr_gastos_representativos = $row3['isr_gastos_representativos'];
			$xiii_mes 				= $row3['xiii_mes'];
			$RIESGOS_PROFECIONAL 	+= $row3['riesgos_profeconal'];
			$SEGURO_EDUCATIVO_EMPLEADO += $row3['seguro_educativo'];
			$SEGURO_SOCIAL_EMPLEADO += $row3['seguro_social'];
			$SEGURO_SOCIAL_PATRONAL += $row3['seguro_social_patronal'];
			$SEGURO_EDUCATIVO_PATRONAL += $row3['seguro_educativo_patronal'];
			$IMPUESTO_SOBRE_RENTA_EMPLEADO += $row3['isr'];
			$IMPUESTO_SOBRE_RENTA_PATRONAL += 0;
			//------------------------------------------
		}

		$trabajadores[] = array('ficha'=>$ficha, 'cedula'=>$cedula,'sexo_short'=>$sexo_short,'sexo_long'=>$sexo_long, 'numero_seguro_social'=>$numero_seguro_social, 'nombre'=>$trabajador, 'primer_nombre'=> $primer_nombre, 'apellido'=>$apellido, 'neto'=> $neto,'salario_100'=>$salario_100,'ingreso_regular'=>$ingreso_regular,'otros_ingresos'=>$otros_ingresos,'total_Ingresos'=>$total_Ingresos, 'neto2'=>$neto2,'seguro_social'=>$seguro_social,'seguro_educativo'=>$seguro_educativo,'total_retener'=>$total_retener,'impuesto'=>$impuesto,'vacaciones'=>$monto_vacaciones,'horas_extras'=>$HORAS_EXTRA_SALARIO, 'desc_grupo'=>$grupo,'cod_grupo'=>$row['grupo'],'xiii'=>$xiii_mes);

		if($gastos_representacion>0){
			$trabajadores_gastos_representacion[] = array('ficha'=>$ficha, 'cedula'=>$cedula,'sexo_short'=>$sexo_short,'sexo_long'=>$sexo_long, 'numero_seguro_social'=>$numero_seguro_social, 'nombre'=>$trabajador, 'primer_nombre'=> $primer_nombre, 'apellido'=>$apellido, 'monto'=>$gastos_representacion,'impuesto'=>$isr_gastos_representativos);			
		}

		if($prima_produccion >0){
			$trabajadores_prima_produccion[] = array('ficha'=>$ficha, 'cedula'=>$cedula,'sexo_short'=>$sexo_short,'sexo_long'=>$sexo_long, 'numero_seguro_social'=>$numero_seguro_social, 'nombre'=>$trabajador, 'primer_nombre'=> $primer_nombre, 'apellido'=>$apellido, 'monto'=>$$prima_produccion ,'impuesto'=>$impuesto);
		}
		

		$i++;
	}


	//$trabajadores2[$codorg]=$trabajadores;
	//$trabajadores =array();
	
}

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

}


//===========================================================================

/*$fecha_desde = explode('/', $desde);
$fecha_hasta = explode('/', $hasta);*/


$sql = "SELECT UPPER(t.descrip) as descrip 
			FROM   nomtipos_nomina t
			WHERE  t.codtip=".$codtip;
	$res=$conexion->query($sql);

	if($fila=$res->fetch_array())
	{
		$NOMINA = $fila['descrip']; 
	}

$objPHPExcel->createSheet();
$objPHPExcel->setActiveSheetIndex(0);

$objPHPExcel->getActiveSheet()->setTitle('Cuota de pago especifico');

$objPHPExcel->getActiveSheet()
			->setCellValue('B1', '')
			->setCellValue('C1', strtoupper($empresa))
			->setCellValue('C2', 'Planilla Mensual de Cuotas, Aportes e Impuesto sobre la Renta')
			//->setCellValue('C1', '')
			->setCellValue('C3', strtoupper("Periodo del ".fecha($desde).' al '.fecha($hasta)))
			->setCellValue('O1', 'Ref:MM-771' )
			->setCellValue('O2', '')
			->setCellValue('O3', $estatus_mov_nomina);
//$telefono_empresa

$objPHPExcel->getActiveSheet()
			->setCellValue('B5', 'NOMBRE DEL PATRONO:')//B:C;			
			->setCellValue('E5', $empresa)			
			->setCellValue('I5', 'LUGAR DE TRABAJO:')		
			->setCellValue('L5', $lugar_de_trabajo );
$objPHPExcel->getActiveSheet()->mergeCells('B5:D5');
$objPHPExcel->getActiveSheet()->mergeCells('E5:G5');
$objPHPExcel->getActiveSheet()->mergeCells('I5:K5');
$objPHPExcel->getActiveSheet()->mergeCells('L5:P5');


$objPHPExcel->getActiveSheet()
			->setCellValue('B6', 'DIRECCION FISCAL:')//B:C;			
			->setCellValue('E6', $direccion_fiscal_empresa)			
			->setCellValue('I6', 'ACTIVIDAD ECONOMICA:')		
			->setCellValue('L6', $actividad_economica);
$objPHPExcel->getActiveSheet()->mergeCells('B6:D6');
$objPHPExcel->getActiveSheet()->mergeCells('E6:G6');
$objPHPExcel->getActiveSheet()->mergeCells('I6:K6');
$objPHPExcel->getActiveSheet()->mergeCells('L6:P6');


$objPHPExcel->getActiveSheet()
			->setCellValue('B7', 'CEDULA JURIDICA:')//B:C;			
			->setCellValue('E7', $cedula_juridica)			
			->setCellValue('I7', 'CEDULA NATURAL:')		
			->setCellValue('L7', $cedula_natural);
$objPHPExcel->getActiveSheet()->mergeCells('B7:D7');
$objPHPExcel->getActiveSheet()->mergeCells('E7:G7');
$objPHPExcel->getActiveSheet()->mergeCells('I7:K7');
$objPHPExcel->getActiveSheet()->mergeCells('L7:P7');


$objPHPExcel->getActiveSheet()
			->setCellValue('B8', 'REPRESENTANTE LEGAL:')//B:C;			
			->setCellValue('E8', $representante_legal)			
			->setCellValue('I8', 'LICENCIA:')		
			->setCellValue('L8', $representante_licencia)
			->setCellValue('N8', 'TELEFONO:')
			->setCellValue('O8', $representante_telef);
$objPHPExcel->getActiveSheet()->mergeCells('B8:D8');
$objPHPExcel->getActiveSheet()->mergeCells('E8:G8');
$objPHPExcel->getActiveSheet()->mergeCells('I8:K8');
$objPHPExcel->getActiveSheet()->mergeCells('L8:M8');
$objPHPExcel->getActiveSheet()->mergeCells('O8:P8');

$objPHPExcel->getActiveSheet()
			->setCellValue('B9', 'CORRELATIVO:')//B:C;			
			//->setCellValue('E9', $correlativo)			
			->setCellValue('I9', 'No. PATRONAL:')		
			->setCellValue('L9', $numero_patronal);
$objPHPExcel->getActiveSheet()->mergeCells('B9:D9');
$objPHPExcel->getActiveSheet()->mergeCells('E9:G9');
$objPHPExcel->getActiveSheet()->mergeCells('I9:K9');
$objPHPExcel->getActiveSheet()->mergeCells('L9:P9');
$objPHPExcel->getActiveSheet()->setCellValueExplicit('E9', $correlativo,PHPExcel_Cell_DataType::TYPE_STRING);


$objPHPExcel->getActiveSheet()->getStyle('B5:P5')->getBorders()->getTop()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
$objPHPExcel->getActiveSheet()->getStyle('B5:B9')->getBorders()->getLeft()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);  
$objPHPExcel->getActiveSheet()->getStyle('K5:P9')->getBorders()->getRight()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
$objPHPExcel->getActiveSheet()->getStyle('B9:P9')->getBorders()->getBottom()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);

$objPHPExcel->getActiveSheet()
			->setCellValue('B10', "c\ro")//B:C;			
			->setCellValue('C10', "No.\rSeguro")			
			->setCellValue('D10', "No.\rCedula")		
			->setCellValue('E10', 'Apellido')
			->setCellValue('F10', 'Nombre')			
			->setCellValue('G10', 'S TC D')		
			->setCellValue('H10', "No.\rEmp.")
			->setCellValue('I10', 'Salario')
			->setCellValue('J10', 'X')
			->setCellValue('K10', 'Vac.')
			->setCellValue('L10', 'Horas Ext.')
			->setCellValue('M10', "Monto\rI/R")
			->setCellValue('N10', "Clave\rI/R")
			->setCellValue('O10', "Decimo\rT.Mes")
			->setCellValue('P10', "Otros\rIngresos");

$objPHPExcel->getActiveSheet()->getStyle('B10:P10')->getBorders()->getBottom()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);			

			
/*$objPHPExcel->getActiveSheet()->mergeCells('B10:C10');
$objPHPExcel->getActiveSheet()->mergeCells('D10:F10');
$objPHPExcel->getActiveSheet()->mergeCells('G10:H10');
$objPHPExcel->getActiveSheet()->mergeCells('I10:K10');        */					  

//$objPHPExcel->getActiveSheet()->getStyle('B5:K9')->applyFromArray(allBordersThin()); 

$objPHPExcel->getActiveSheet()->getStyle('C1:N1')->getFont()->setSize(16);
$objPHPExcel->getActiveSheet()->getStyle('C1:L1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->mergeCells('C1:N1');

$objPHPExcel->getActiveSheet()->getStyle('C3:N3')->getFont()->setSize(12);
$objPHPExcel->getActiveSheet()->getStyle('C3:N3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->mergeCells('C3:N3');

$objPHPExcel->getActiveSheet()->getStyle('C2:N2')->getFont()->setSize(12);
$objPHPExcel->getActiveSheet()->getStyle('C2:N2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->mergeCells('C2:N2');

$objPHPExcel->getActiveSheet()->getStyle('J1:J3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
$objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(20);

$objPHPExcel->getActiveSheet()->mergeCells('O1:P1');
$objPHPExcel->getActiveSheet()->mergeCells('P2:P2');
$objPHPExcel->getActiveSheet()->mergeCells('O3:P3');


/*$objPHPExcel->getActiveSheet()->getStyle('B10:N10')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_JUSTIFY);*/
$objPHPExcel->getActiveSheet()->getStyle('B5:P9')->getFont()->setSize(8);
$objPHPExcel->getActiveSheet()->getStyle('B10:P10')->getFont()->setSize(8);
$objPHPExcel->getActiveSheet()->getStyle('B10:P10')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

$objPHPExcel->getActiveSheet()->getRowDimension(10)->setRowHeight(30);

$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(false);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(3);

$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(false);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(10);

$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(false);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(10);

$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(false);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);

$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(false);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);

$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(false);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(10);

$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(false);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(8);

$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(false);
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(10);

$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(false);
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(4);

$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(false);
$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(10);

$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(false);
$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(10);

$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setAutoSize(false);
$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(10);

$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setAutoSize(false);
$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(10);

$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setAutoSize(false);
$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(10);

$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setAutoSize(false);
$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(10);

cellColor('A1:P11', 'FFFFFF');

$i =11; 
$objPHPExcel->getActiveSheet()
				->setCellValue('B'.$i, "EXCEPCION")//B:C;							
				//->setCellValue('E'.$i, "03")	
				->setCellValue('F'.$i, 'SALARIO');	
$objPHPExcel->getActiveSheet()->setCellValueExplicit('E'.$i, "03",PHPExcel_Cell_DataType::TYPE_STRING);

$objPHPExcel->getActiveSheet()->mergeCells('B'.$i.':D'.$i);
$objPHPExcel->getActiveSheet()->mergeCells('F'.$i.':P'.$i);
$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':P'.$i)->getFont()->setSize(10);
$i++;			
$ini_i=$i;

$SUMA_TOTAL_SALARIO 	= 0;
$SUMA_TOTAL_VACIONES	= 0;
$SUMA_TOTAL_H_EXTRAS	= 0;
$SUMA_TOTAL_IMPUESTO 	= 0;
$SUMA_TOTAL_XIII 		= 0;
$SUMA_TOTAL_OTROS_I 	= 0;


$SUMA_TOTAL_SALARIO_03 	= 0;
$SUMA_TOTAL_IMPUESTO_03 = 0;
$SUMA_TOTAL_XIII_03 	= 0;
$SUMA_TOTAL_OTROS_I_03 	= 0;

$SUMA_TOTAL_SALARIO_73 	= 0;
$SUMA_TOTAL_IMPUESTO_73 = 0;
$SUMA_TOTAL_XIII_73 	= 0;
$SUMA_TOTAL_OTROS_I_73 	= 0;

$SUMA_TOTAL_SALARIO_81 	= 0;
$SUMA_TOTAL_IMPUESTO_81 = 0;
$SUMA_TOTAL_XIII_81 	= 0;
$SUMA_TOTAL_OTROS_I_81 	= 0;



foreach ($trabajadores as $key => $row) 
{
	$objPHPExcel->getActiveSheet()
				->setCellValue('B'.$i, "-")//B:C;
				->setCellValueExplicit('C'.$i, $row["numero_seguro_social"],PHPExcel_Cell_DataType::TYPE_STRING)		
				->setCellValue('D'.$i, $row["cedula"])	
				->setCellValue('E'.$i, strtoupper($row['apellido']))
				->setCellValue('F'.$i, strtoupper($row['primer_nombre']))			
				->setCellValue('G'.$i, strtoupper($row['sexo_short'].' 03 '))//		
				->setCellValue('H'.$i, $row['ficha'])
				->setCellValue('I'.$i, $row['salario_100'])
				->setCellValue('J'.$i, '')
				->setCellValue('K'.$i, $row['vacaciones'])//vaca
				->setCellValue('L'.$i, $row['horas_extras'])//horas extras
				->setCellValue('M'.$i, $row['impuesto'])
				->setCellValue('N'.$i, "")
				->setCellValue('O'.$i, $row['xiii'])
				->setCellValue('P'.$i, $row['otros_ingresos']);			
	$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':P'.$i)->getFont()->setSize(7);
	
	$objPHPExcel->getActiveSheet()->getStyle('I'.$i)->getNumberFormat()
							      ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle('J'.$i)->getNumberFormat()
							      ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle('K'.$i)->getNumberFormat()
							      ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle('L'.$i)->getNumberFormat()
							      ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle('M'.$i)->getNumberFormat()
							      ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle('N'.$i)->getNumberFormat()
							      ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle('O'.$i)->getNumberFormat()
							      ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle('P'.$i)->getNumberFormat()
							      ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$i++;
}

$i++;

	$objPHPExcel->getActiveSheet()
				->setCellValue('B'.$i, "")//B:C;			
				->setCellValue('C'.$i, "")			
				->setCellValue('D'.$i, "")	
				->setCellValue('E'.$i, "")
				->setCellValue('F'.$i, "Sub total salario")					
				->setCellValue('H'.$i, "")
				->setCellValue('I'.$i, "=SUM(I".$ini_i.":I".($i-1).")")
				->setCellValue('J'.$i, "")
				->setCellValue('K'.$i, "=SUM(K".$ini_i.":K".($i-1).")")
				->setCellValue('L'.$i, "=SUM(L".$ini_i.":L".($i-1).")")
				->setCellValue('M'.$i, "=SUM(M".$ini_i.":M".($i-1).")")
				->setCellValue('N'.$i, "")
				->setCellValue('O'.$i, "=SUM(O".$ini_i.":O".($i-1).")")
				->setCellValue('P'.$i, "=SUM(P".$ini_i.":P".($i-1).")");
	$objPHPExcel->getActiveSheet()->mergeCells('F'.$i.':G'.$i);	
	$SUMA_TOTAL_SALARIO 	= $objPHPExcel->getActiveSheet()->getCell('I'.$i)->getCalculatedValue();
	$SUMA_TOTAL_VACIONES	= $objPHPExcel->getActiveSheet()->getCell('K'.$i)->getCalculatedValue();
	$SUMA_TOTAL_H_EXTRAS	= $objPHPExcel->getActiveSheet()->getCell('L'.$i)->getCalculatedValue();
	$SUMA_TOTAL_IMPUESTO 	= $objPHPExcel->getActiveSheet()->getCell('M'.$i)->getCalculatedValue();
	$SUMA_TOTAL_XIII 		= $objPHPExcel->getActiveSheet()->getCell('O'.$i)->getCalculatedValue();
	$SUMA_TOTAL_OTROS_I 	= $objPHPExcel->getActiveSheet()->getCell('P'.$i)->getCalculatedValue();


	$SUMA_TOTAL_SALARIO_03 	= $SUMA_TOTAL_SALARIO;	
	$SUMA_TOTAL_IMPUESTO_03 = $SUMA_TOTAL_IMPUESTO;
	$SUMA_TOTAL_XIII_03 	= $SUMA_TOTAL_XIII;
	$SUMA_TOTAL_OTROS_I_03 	= $SUMA_TOTAL_OTROS_I;

	$objPHPExcel->getActiveSheet()->getStyle('F'.$i.':G'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

	$objPHPExcel->getActiveSheet()->getStyle('I'.$i)->getNumberFormat()
							      ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);
	$objPHPExcel->getActiveSheet()->getStyle('J'.$i)->getNumberFormat()
							      ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle('K'.$i)->getNumberFormat()
							      ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle('L'.$i)->getNumberFormat()
							      ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

	$objPHPExcel->getActiveSheet()->getStyle('M'.$i)->getNumberFormat()
							      ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	/*$objPHPExcel->getActiveSheet()->getStyle('N'.$i)->getNumberFormat()
							      ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);*/

	$objPHPExcel->getActiveSheet()->getStyle('O'.$i)->getNumberFormat()
							      ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle('P'.$i)->getNumberFormat()
							      ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);	
	$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':P'.$i)->getFont()->setSize(8);


$i++;$i++;
$objPHPExcel->getActiveSheet()
				->setCellValue('B'.$i, "EXCEPCION")//B:C;							
				->setCellValue('E'.$i, '73')	
				->setCellValue('F'.$i, 'GASTOS DE REPRESENTACION');	

$objPHPExcel->getActiveSheet()->mergeCells('B'.$i.':D'.$i);
$objPHPExcel->getActiveSheet()->mergeCells('F'.$i.':P'.$i);
$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':P'.$i)->getFont()->setSize(10);

$i++;
$ini_i_2=$i;
foreach ($trabajadores_gastos_representacion as $key => $row) 
{
	$objPHPExcel->getActiveSheet()
				->setCellValue('B'.$i, "-")//B:C;
				->setCellValueExplicit('C'.$i, $row["numero_seguro_social"],PHPExcel_Cell_DataType::TYPE_STRING)		
				->setCellValue('D'.$i, $row["cedula"])	
				->setCellValue('E'.$i, strtoupper($row['apellido']))
				->setCellValue('F'.$i, strtoupper($row['primer_nombre']))			
				->setCellValue('G'.$i, strtoupper($row['sexo_short'].' 03 '))//		
				->setCellValue('H'.$i, $row['ficha'])
				->setCellValue('I'.$i, $row['monto'])
				->setCellValue('J'.$i, '')//
				->setCellValue('K'.$i, '0')//vaca
				->setCellValue('L'.$i, '0')//horas extras
				->setCellValue('M'.$i, $row['impuesto'])
				->setCellValue('N'.$i, "")
				->setCellValue('O'.$i, "")
				->setCellValue('P'.$i, '');			
	$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':P'.$i)->getFont()->setSize(7);
	
	$objPHPExcel->getActiveSheet()->getStyle('I'.$i)->getNumberFormat()
							      ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle('J'.$i)->getNumberFormat()
							      ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle('K'.$i)->getNumberFormat()
							      ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle('L'.$i)->getNumberFormat()
							      ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle('M'.$i)->getNumberFormat()
							      ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle('N'.$i)->getNumberFormat()
							      ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	/*$objPHPExcel->getActiveSheet()->getStyle('O'.$i)->getNumberFormat()
							      ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle('P'.$i)->getNumberFormat()
							      ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);*/
	$i++;
}
$i++;

	$objPHPExcel->getActiveSheet()
				->setCellValue('B'.$i, "")//B:C;			
				->setCellValue('C'.$i, "")			
				->setCellValue('D'.$i, "")	
				->setCellValue('E'.$i, "")
				->setCellValue('F'.$i, "Sub total salario")					
				->setCellValue('H'.$i, "")
				->setCellValue('I'.$i, "=SUM(I".$ini_i_2.":I".($i-1).")")
				->setCellValue('J'.$i, "")
				->setCellValue('K'.$i, "=SUM(K".$ini_i_2.":K".($i-1).")")
				->setCellValue('L'.$i, "=SUM(L".$ini_i_2.":L".($i-1).")")
				->setCellValue('M'.$i, "=SUM(M".$ini_i_2.":M".($i-1).")")
				->setCellValue('N'.$i, "")
				->setCellValue('O'.$i, "")
				->setCellValue('P'.$i, "");
	$objPHPExcel->getActiveSheet()->mergeCells('F'.$i.':G'.$i);	

	$SUMA_TOTAL_SALARIO 	+= $objPHPExcel->getActiveSheet()->getCell('I'.$i)->getCalculatedValue();
	$SUMA_TOTAL_VACIONES	+= $objPHPExcel->getActiveSheet()->getCell('K'.$i)->getCalculatedValue();
	$SUMA_TOTAL_H_EXTRAS	+= $objPHPExcel->getActiveSheet()->getCell('L'.$i)->getCalculatedValue();
	$SUMA_TOTAL_IMPUESTO 	+= $objPHPExcel->getActiveSheet()->getCell('M'.$i)->getCalculatedValue();
	$SUMA_TOTAL_XIII 		+= $objPHPExcel->getActiveSheet()->getCell('O'.$i)->getCalculatedValue();
	$SUMA_TOTAL_OTROS_I 	+= $objPHPExcel->getActiveSheet()->getCell('P'.$i)->getCalculatedValue();

	$SUMA_TOTAL_SALARIO_73  = $objPHPExcel->getActiveSheet()->getCell('I'.$i)->getCalculatedValue();
	$SUMA_TOTAL_IMPUESTO_73	= $objPHPExcel->getActiveSheet()->getCell('M'.$i)->getCalculatedValue();
	$SUMA_TOTAL_XIII_73 	= $objPHPExcel->getActiveSheet()->getCell('O'.$i)->getCalculatedValue();
	$SUMA_TOTAL_OTROS_I_73 	= $objPHPExcel->getActiveSheet()->getCell('P'.$i)->getCalculatedValue();

	$objPHPExcel->getActiveSheet()->getStyle('F'.$i.':G'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

	$objPHPExcel->getActiveSheet()->getStyle('I'.$i)->getNumberFormat()
							      ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);
	$objPHPExcel->getActiveSheet()->getStyle('J'.$i)->getNumberFormat()
							      ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle('K'.$i)->getNumberFormat()
							      ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle('L'.$i)->getNumberFormat()
							      ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

	$objPHPExcel->getActiveSheet()->getStyle('M'.$i)->getNumberFormat()
							      ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':P'.$i)->getFont()->setSize(8);							      
	/*$objPHPExcel->getActiveSheet()->getStyle('N'.$i)->getNumberFormat()
							      ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);*/

	/*$objPHPExcel->getActiveSheet()->getStyle('O'.$i)->getNumberFormat()
							      ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle('P'.$i)->getNumberFormat()
							      ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);	
	$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':P'.$i)->getFont()->setSize(8);*/


$i++;$i++;
$objPHPExcel->getActiveSheet()
				->setCellValue('B'.$i, "EXCEPCION")//B:C;03							
				->setCellValue('E'.$i, '81')	
				->setCellValue('F'.$i, 'PRIMAS DE PRODUCCION');	

$objPHPExcel->getActiveSheet()->mergeCells('B'.$i.':D'.$i);
$objPHPExcel->getActiveSheet()->mergeCells('F'.$i.':P'.$i);
$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':P'.$i)->getFont()->setSize(10);


$i++;
$ini_i_3=$i;
foreach ($trabajadores_prima_produccion as $key => $row) 
{
	$objPHPExcel->getActiveSheet()
				->setCellValue('B'.$i, "-")//B:C;
				->setCellValueExplicit('C'.$i, $row["numero_seguro_social"],PHPExcel_Cell_DataType::TYPE_STRING)		
				->setCellValue('D'.$i, $row["cedula"])	
				->setCellValue('E'.$i, strtoupper($row['apellido']))
				->setCellValue('F'.$i, strtoupper($row['primer_nombre']))			
				->setCellValue('G'.$i, strtoupper($row['sexo_short'].' 03 '))//		
				->setCellValue('H'.$i, $row['ficha'])
				->setCellValue('I'.$i, $row['monto'])
				->setCellValue('J'.$i, '')
				->setCellValue('K'.$i, '')//vaca
				->setCellValue('L'.$i, '')//horas extras
				->setCellValue('M'.$i, '')
				->setCellValue('N'.$i, "")
				->setCellValue('O'.$i, "")
				->setCellValue('P'.$i, '');			
	$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':P'.$i)->getFont()->setSize(7);
	
	$objPHPExcel->getActiveSheet()->getStyle('I'.$i)->getNumberFormat()
							      ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle('J'.$i)->getNumberFormat()
							      ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle('K'.$i)->getNumberFormat()
							      ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle('L'.$i)->getNumberFormat()
							      ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle('M'.$i)->getNumberFormat()
							      ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	/*$objPHPExcel->getActiveSheet()->getStyle('N'.$i)->getNumberFormat()
							      ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle('O'.$i)->getNumberFormat()
							      ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle('P'.$i)->getNumberFormat()
							      ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);*/
	$i++;
}
$i++;

	$objPHPExcel->getActiveSheet()
				->setCellValue('B'.$i, "")//B:C;			
				->setCellValue('C'.$i, "")			
				->setCellValue('D'.$i, "")	
				->setCellValue('E'.$i, "")
				->setCellValue('F'.$i, "Sub total salario")					
				->setCellValue('H'.$i, "")
				->setCellValue('I'.$i, "=SUM(I".$ini_i_3.":I".($i-1).")")
				->setCellValue('J'.$i, "")
				->setCellValue('K'.$i, "=SUM(K".$ini_i_3.":K".($i-1).")")
				->setCellValue('L'.$i, "=SUM(L".$ini_i_3.":L".($i-1).")")
				->setCellValue('M'.$i, "=SUM(M".$ini_i_3.":M".($i-1).")")
				->setCellValue('N'.$i, "")
				->setCellValue('O'.$i, "")
				->setCellValue('P'.$i, "");
	$objPHPExcel->getActiveSheet()->mergeCells('F'.$i.':G'.$i);	
	$SUMA_TOTAL_SALARIO 	+= $objPHPExcel->getActiveSheet()->getCell('I'.$i)->getCalculatedValue();
	$SUMA_TOTAL_VACIONES	+= $objPHPExcel->getActiveSheet()->getCell('K'.$i)->getCalculatedValue();
	$SUMA_TOTAL_H_EXTRAS	+= $objPHPExcel->getActiveSheet()->getCell('L'.$i)->getCalculatedValue();
	$SUMA_TOTAL_IMPUESTO 	+= $objPHPExcel->getActiveSheet()->getCell('M'.$i)->getCalculatedValue();
	$SUMA_TOTAL_XIII 		+= $objPHPExcel->getActiveSheet()->getCell('O'.$i)->getCalculatedValue();
	$SUMA_TOTAL_OTROS_I 	+= $objPHPExcel->getActiveSheet()->getCell('P'.$i)->getCalculatedValue();

	$SUMA_TOTAL_SALARIO_81  = $objPHPExcel->getActiveSheet()->getCell('I'.$i)->getCalculatedValue();
	$SUMA_TOTAL_IMPUESTO_81	= $objPHPExcel->getActiveSheet()->getCell('M'.$i)->getCalculatedValue();
	$SUMA_TOTAL_XIII_81 	= $objPHPExcel->getActiveSheet()->getCell('O'.$i)->getCalculatedValue();
	$SUMA_TOTAL_OTROS_I_81 	= $objPHPExcel->getActiveSheet()->getCell('P'.$i)->getCalculatedValue();

	$objPHPExcel->getActiveSheet()->getStyle('F'.$i.':G'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

	$objPHPExcel->getActiveSheet()->getStyle('I'.$i)->getNumberFormat()
							      ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);
	$objPHPExcel->getActiveSheet()->getStyle('J'.$i)->getNumberFormat()
							      ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle('K'.$i)->getNumberFormat()
							      ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle('L'.$i)->getNumberFormat()
							      ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

	$objPHPExcel->getActiveSheet()->getStyle('M'.$i)->getNumberFormat()
							      ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	/*$objPHPExcel->getActiveSheet()->getStyle('N'.$i)->getNumberFormat()
							      ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);*/

	/*$objPHPExcel->getActiveSheet()->getStyle('O'.$i)->getNumberFormat()
							      ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle('P'.$i)->getNumberFormat()
							      ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);	*/
	$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':P'.$i)->getFont()->setSize(8);

cellColor('A11:P'.($i+2), 'FFFFFF');


/*$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(7);
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
*/

//------------------------------------------------------------------------------------------

//$objPHPExcel->createSheet();
$objPHPExcel->setActiveSheetIndex(1); 
$objPHPExcel->getActiveSheet()->setTitle('Cuota de pago general');

/*
$objPHPExcel->getActiveSheet()
			->setCellValue('B1', '')
			->setCellValue('C1', strtoupper($empresa))
			->setCellValue('C2', 'Planilla Mensual de Cuotas, Aportes e Impuesto sobre la Renta')
			//->setCellValue('C1', '')
			->setCellValue('C3', strtoupper($NOMINA." Del ".fecha($desde).' al '.fecha($hasta)))
			->setCellValue('O1', 'Ref:MM-771' )
			->setCellValue('O2', '')
			->setCellValue('O3', $estatus_mov_nomina);
//$telefono_empresa

$objPHPExcel->getActiveSheet()
			->setCellValue('B5', 'NOMBRE DEL PATRONO:')//B:C;			
			->setCellValue('E5', $empresa)			
			->setCellValue('I5', 'LUGAR DE TRABAJO:')		
			->setCellValue('L5', $lugar_de_trabajo );
$objPHPExcel->getActiveSheet()->mergeCells('B5:D5');
$objPHPExcel->getActiveSheet()->mergeCells('E5:G5');
$objPHPExcel->getActiveSheet()->mergeCells('I5:K5');
$objPHPExcel->getActiveSheet()->mergeCells('L5:P5');


$objPHPExcel->getActiveSheet()
			->setCellValue('B6', 'DIRECCION FISCAL:')//B:C;			
			->setCellValue('E6', $direccion_fiscal_empresa)			
			->setCellValue('I6', 'ACTIVIDAD ECONOMICA:')		
			->setCellValue('L6', $actividad_economica);
$objPHPExcel->getActiveSheet()->mergeCells('B6:D6');
$objPHPExcel->getActiveSheet()->mergeCells('E6:G6');
$objPHPExcel->getActiveSheet()->mergeCells('I6:K6');
$objPHPExcel->getActiveSheet()->mergeCells('L6:P6');


$objPHPExcel->getActiveSheet()
			->setCellValue('B7', 'CEDULA JURIDICA:')//B:C;			
			->setCellValue('E7', $cedula_juridica)			
			->setCellValue('I7', 'CEDULA NATURAL:')		
			->setCellValue('L7', $cedula_natural);
$objPHPExcel->getActiveSheet()->mergeCells('B7:D7');
$objPHPExcel->getActiveSheet()->mergeCells('E7:G7');
$objPHPExcel->getActiveSheet()->mergeCells('I7:K7');
$objPHPExcel->getActiveSheet()->mergeCells('L7:P7');


$objPHPExcel->getActiveSheet()
			->setCellValue('B8', 'REPRESENTANTE LEGAL:')//B:C;			
			->setCellValue('E8', $representante_legal)			
			->setCellValue('I8', 'LICENCIA:')		
			->setCellValue('L8', $licencia_empresa)
			->setCellValue('N8', 'TELEFONO:')
			->setCellValue('O8', $telefono_empresa);
$objPHPExcel->getActiveSheet()->mergeCells('B8:D8');
$objPHPExcel->getActiveSheet()->mergeCells('E8:G8');
$objPHPExcel->getActiveSheet()->mergeCells('I8:K8');
$objPHPExcel->getActiveSheet()->mergeCells('L8:M8');
$objPHPExcel->getActiveSheet()->mergeCells('O8:P8');

$objPHPExcel->getActiveSheet()
			->setCellValue('B9', 'CORRELATIVO:')//B:C;			
			//->setCellValue('E9', $correlativo)			
			->setCellValue('I9', 'No. PATRONAL:')		
			->setCellValue('L9', $numero_patronal);
$objPHPExcel->getActiveSheet()->mergeCells('B9:D9');
$objPHPExcel->getActiveSheet()->mergeCells('E9:G9');
$objPHPExcel->getActiveSheet()->mergeCells('I9:K9');
$objPHPExcel->getActiveSheet()->mergeCells('L9:P9');
$objPHPExcel->getActiveSheet()->setCellValueExplicit('E9', $correlativo,PHPExcel_Cell_DataType::TYPE_STRING);
*/
$objPHPExcel->getActiveSheet()			
			->setCellValue('B1', strtoupper($empresa))
			->setCellValue('B2', 'Planilla Mensual de Cuotas, Aportes e Impuesto sobre la Renta')
			//->setCellValue('B1', '')
			->setCellValue('B3', strtoupper("Periodo del ".fecha($desde).' al '.fecha($hasta)))
			->setCellValue('O1', 'Ref:MM-771')
			->setCellValue('O2', '')
			->setCellValue('O3', $estatus_mov_nomina);								
			//->setCellValue('J2', PHPExcel_Shared_Date::FormattedPHPToExcel($anio, $mes_numero, $dia_fin) )

$objPHPExcel->getActiveSheet()
			->setCellValue('B5', 'NOMBRE DEL PATRONO : ')//B:C;			
			->setCellValue('E5', $empresa)			
			->setCellValue('I5', 'LUGAR DE TRABAJO')		
			->setCellValue('L5', $lugar_de_trabajo );
$objPHPExcel->getActiveSheet()->mergeCells('B5:D5');
$objPHPExcel->getActiveSheet()->mergeCells('E5:G5');
$objPHPExcel->getActiveSheet()->mergeCells('I5:K5');
$objPHPExcel->getActiveSheet()->mergeCells('L5:P5');


$objPHPExcel->getActiveSheet()
			->setCellValue('B6', 'DIRECCION FISCAL : ')//B:C;			
			->setCellValue('E6', $direccion_fiscal_empresa)			
			->setCellValue('I6', 'ACTIVIDAD ECONOMICA')		
			->setCellValue('L6', $actividad_economica);
$objPHPExcel->getActiveSheet()->mergeCells('B6:D6');
$objPHPExcel->getActiveSheet()->mergeCells('E6:G6');
$objPHPExcel->getActiveSheet()->mergeCells('I6:K6');
$objPHPExcel->getActiveSheet()->mergeCells('L6:P6');


$objPHPExcel->getActiveSheet()
			->setCellValue('B7', 'CEDULA JURIDICA : ')//B:C;			
			->setCellValue('E7', $cedula_juridica)			
			->setCellValue('I7', 'CEDULA NATURAL')		
			->setCellValue('L7', $cedula_natural);
$objPHPExcel->getActiveSheet()->mergeCells('B7:D7');
$objPHPExcel->getActiveSheet()->mergeCells('E6:G6');
$objPHPExcel->getActiveSheet()->mergeCells('I6:K6');
$objPHPExcel->getActiveSheet()->mergeCells('L6:P6');

$objPHPExcel->getActiveSheet()
			->setCellValue('B8', 'REPRESENTANTE LEGAL:')//B:C;			
			->setCellValue('E8', $representante_legal)			
			->setCellValue('I8', 'LICENCIA:')		
			->setCellValue('L8', $licencia_empresa)
			->setCellValue('N8', 'TELEFONO:')
			->setCellValue('O8', $telefono_empresa);
$objPHPExcel->getActiveSheet()->mergeCells('B8:D8');
$objPHPExcel->getActiveSheet()->mergeCells('E8:G8');
$objPHPExcel->getActiveSheet()->mergeCells('I8:K8');
$objPHPExcel->getActiveSheet()->mergeCells('L8:M8');
$objPHPExcel->getActiveSheet()->mergeCells('O8:P8');

$objPHPExcel->getActiveSheet()
			->setCellValue('B9', 'CORRELATIVO : ')//B:C;			
			->setCellValueExplicit('E9', $correlativo,PHPExcel_Cell_DataType::TYPE_STRING)		
			->setCellValue('I9', 'No. PATRONAL : ')		
			->setCellValue('L9', $numero_patronal);
$objPHPExcel->getActiveSheet()->mergeCells('B9:D9');
$objPHPExcel->getActiveSheet()->mergeCells('E9:G9');
$objPHPExcel->getActiveSheet()->mergeCells('I9:K9');
$objPHPExcel->getActiveSheet()->mergeCells('L9:P9');
//$objPHPExcel->getActiveSheet()->setCellValueExplicit('E9', $correlativo,PHPExcel_Cell_DataType::TYPE_STRING);



/*$objPHPExcel->getActiveSheet()->getStyle('B5:K5')->getBorders()->getTop()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
$objPHPExcel->getActiveSheet()->getStyle('B9:K9')->getBorders()->getBottom()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM); 		*/			  


$objPHPExcel->getActiveSheet()->getStyle('B5:P5')->getBorders()->getTop()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
$objPHPExcel->getActiveSheet()->getStyle('B5:B9')->getBorders()->getLeft()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);  
$objPHPExcel->getActiveSheet()->getStyle('K5:P9')->getBorders()->getRight()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
$objPHPExcel->getActiveSheet()->getStyle('B9:P9')->getBorders()->getBottom()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);

$objPHPExcel->getActiveSheet()
			->setCellValue('B10', "c\ro")//B:C;			
			->setCellValue('C10', "No.\rSeguro")			
			->setCellValue('D10', "No.\rCedula")		
			->setCellValue('E10', 'Apellido')
			->setCellValue('F10', 'Nombre')			
			->setCellValue('G10', 'S TC D')		
			->setCellValue('H10', "No.\rEmp.")
			->setCellValue('I10', 'Salario')
			->setCellValue('J10', 'X')
			->setCellValue('K10', 'Vac.')
			->setCellValue('L10', 'Horas Ext.')
			->setCellValue('M10', "Monto\rI/R")
			->setCellValue('N10', "Clave\rI/R")
			->setCellValue('O10', "Decimo\rT.Mes")
			->setCellValue('P10', "Otros\rIngresos");

$objPHPExcel->getActiveSheet()->getStyle('B10:P10')->getBorders()->getBottom()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);		        					  

/*$objPHPExcel->getActiveSheet()			
			->setCellValue('B6', 'Núm.')
			->setCellValue('C6', 'Nombre')
			->setCellValue('D6', "Clave\r ISR")
			->setCellValue('E6', "Ingreso\r Regular")
			->setCellValue('F6', "Otros\r Ingresos")
			->setCellValue('G6', "Total\r Ingresos")
			->setCellValue('H6', "Impuesto\r /Renta")
			->setCellValue('I6', "Seguros\r Soc.+Edu.")
			->setCellValue('J6', "Total a \r Retener")			
			->setCellValue('k6', "Neto a\r Pagar"); */

$objPHPExcel->getActiveSheet()->getDefaultStyle()->getFont()->setName('Times New Roman');
//$objPHPExcel->getActiveSheet()->getStyle('B1:F1')->getFont()->setName('Times New Roman');
$objPHPExcel->getActiveSheet()->getStyle('B1:I1')->getFont()->setSize(16);
$objPHPExcel->getActiveSheet()->getStyle('B1:I1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$objPHPExcel->getActiveSheet()->mergeCells('B1:I1');

$objPHPExcel->getActiveSheet()->getStyle('B3:I3')->getFont()->setSize(12);
$objPHPExcel->getActiveSheet()->getStyle('B3:I3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$objPHPExcel->getActiveSheet()->mergeCells('B3:I3');

$objPHPExcel->getActiveSheet()->getStyle('B2:I2')->getFont()->setSize(12);
$objPHPExcel->getActiveSheet()->getStyle('B2:I2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$objPHPExcel->getActiveSheet()->mergeCells('B2:I2');


/*$objPHPExcel->getActiveSheet()->getStyle('J2')->getNumberFormat()							  
							  ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX15);	*/
$objPHPExcel->getActiveSheet()->getStyle('B5:P9')->getFont()->setSize(8);
$objPHPExcel->getActiveSheet()->getStyle('B10:P10')->getFont()->setSize(8);

$objPHPExcel->getActiveSheet()->getStyle('J1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
$objPHPExcel->getActiveSheet()->mergeCells('J1:K1');
$objPHPExcel->getActiveSheet()->getStyle('J2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
$objPHPExcel->getActiveSheet()->mergeCells('J2:K2');
$objPHPExcel->getActiveSheet()->getStyle('J3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
$objPHPExcel->getActiveSheet()->mergeCells('J3:K3');
							  


$objPHPExcel->getActiveSheet()->getRowDimension(10)->setRowHeight(30);

$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(false);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(3);

$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(false);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(10);

$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(false);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(10);

$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(false);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);

$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(false);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);

$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(false);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(10);

$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(false);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(8);

$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(false);
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(10);

$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(false);
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(4);

$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(false);
$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(10);

$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(false);
$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(10);

$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setAutoSize(false);
$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(10);

$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setAutoSize(false);
$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(10);

$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setAutoSize(false);
$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(10);

$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setAutoSize(false);
$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(10);

cellColor('A1:P11', 'FFFFFF');
//------------------------------------------------

$objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(30);
//$objPHPExcel->getActiveSheet()->getRowDimension('6')->setRowHeight(40);

//$i++;
$r = 11;
$objPHPExcel->getActiveSheet()
			->setCellValue('B'.$r, "TOTALES   FINALES")//B:C;			
			/*->setCellValue('C'.$r, "")			
			->setCellValue('D'.$r, "")	*/	
			->setCellValue('E'.$r, '')
			->setCellValue('F'.$r, '')			
			->setCellValue('G'.$r, '')		
			->setCellValue('H'.$r, "")
			->setCellValue('I'.$r, $SUMA_TOTAL_SALARIO)//
			->setCellValue('J'.$r, '')
			->setCellValue('K'.$r, $SUMA_TOTAL_VACIONES)//
			->setCellValue('L'.$r, $SUMA_TOTAL_H_EXTRAS)//
			->setCellValue('M'.$r, $SUMA_TOTAL_IMPUESTO)//
			->setCellValue('N'.$r, "")//
			->setCellValue('O'.$r, $SUMA_TOTAL_XIII)//
			->setCellValue('P'.$r, $SUMA_TOTAL_OTROS_I);//

 $objPHPExcel->getActiveSheet()->mergeCells('B'.$r.':D'.$r);

 $objPHPExcel->getActiveSheet()->getStyle('I'.$r)->getNumberFormat()
							      ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);
	$objPHPExcel->getActiveSheet()->getStyle('J'.$r)->getNumberFormat()
							      ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle('K'.$r)->getNumberFormat()
							      ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle('L'.$r)->getNumberFormat()
							      ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

	$objPHPExcel->getActiveSheet()->getStyle('M'.$r)->getNumberFormat()
							      ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	/*$objPHPExcel->getActiveSheet()->getStyle('N'.$r)->getNumberFormat()
							      ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);*/

	$objPHPExcel->getActiveSheet()->getStyle('O'.$r)->getNumberFormat()
							      ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
	$objPHPExcel->getActiveSheet()->getStyle('P'.$r)->getNumberFormat()
							      ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);	
	$objPHPExcel->getActiveSheet()->getStyle('B'.$r.':P'.$r)->getFont()->setSize(10);
	$objPHPExcel->getActiveSheet()->getStyle('B'.$r.':P'.$r)->getFont()->setBold(true);


 $r++;  $r++;  $r++; $r++;  $r++;
 $objPHPExcel->getActiveSheet()			
			->setCellValue('B'.$r,'Sello y Firma Autorizada');
$objPHPExcel->getActiveSheet()->mergeCells('B'.$r.':F'.$r);	
$objPHPExcel->getActiveSheet()->getStyle('B'.$r.':F'.$r)->getFont()->setSize(10);

$objPHPExcel->getActiveSheet()->getStyle('B'.$r.':F'.$r)->getBorders()->getTop()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$r++; 
$objPHPExcel->getActiveSheet()->getStyle('B'.$r.':P'.$r)->getBorders()->getBottom()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

$objPHPExcel->getActiveSheet()->getStyle('B'.$r.':F'.$r)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$r++; $r++; 




$objPHPExcel->getActiveSheet()
			->setCellValue('B'.$r, "RESUMEN")//B:C;						
			->setCellValue('G'.$r, 'Salario')					
			->setCellValue('I'.$r, "Monto\rI/R")			
			->setCellValue('K'.$r, "Decimo\rT.Mes")			
			->setCellValue('M'.$r, "Otros\rIngresos");

$objPHPExcel->getActiveSheet()->mergeCells('B'.$r.':F'.$r);
$objPHPExcel->getActiveSheet()->mergeCells('G'.$r.':H'.$r);
$objPHPExcel->getActiveSheet()->mergeCells('I'.$r.':J'.$r);
$objPHPExcel->getActiveSheet()->mergeCells('K'.$r.':L'.$r);
$objPHPExcel->getActiveSheet()->mergeCells('M'.$r.':N'.$r);

$objPHPExcel->getActiveSheet()->getStyle('B'.$r.':P'.$r)->getFont()->setSize(10);
$objPHPExcel->getActiveSheet()->getStyle('B'.$r.':P'.$r)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('B'.$r.':F'.$r)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('G'.$r.':P'.$r)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
$objPHPExcel->getActiveSheet()->getStyle('B'.$r.':P'.$r)->getBorders()->getBottom()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getRowDimension($r)->setRowHeight(30);  




$r++;
$objPHPExcel->getActiveSheet()
			->setCellValue('B'.$r, "Subtotal Salario - 3")//B:C;						
			->setCellValue('G'.$r, $SUMA_TOTAL_SALARIO_03)					
			->setCellValue('I'.$r, $SUMA_TOTAL_IMPUESTO_03)			
			->setCellValue('K'.$r, $SUMA_TOTAL_XIII_03 )			
			->setCellValue('M'.$r, $SUMA_TOTAL_OTROS_I_03);   
$objPHPExcel->getActiveSheet()->getStyle('B'.$r.':P'.$r)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);			
$objPHPExcel->getActiveSheet()->getStyle('G'.$r.':M'.$r)->getNumberFormat()
							      ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
$objPHPExcel->getActiveSheet()->mergeCells('B'.$r.':F'.$r);
$objPHPExcel->getActiveSheet()->mergeCells('G'.$r.':H'.$r);
$objPHPExcel->getActiveSheet()->mergeCells('I'.$r.':J'.$r);
$objPHPExcel->getActiveSheet()->mergeCells('K'.$r.':L'.$r);
$objPHPExcel->getActiveSheet()->mergeCells('M'.$r.':N'.$r);

$r++;
$objPHPExcel->getActiveSheet()
			->setCellValue('B'.$r, "Subtotal Gastos de representacion - 73")//B:C;						
			->setCellValue('G'.$r, $SUMA_TOTAL_SALARIO_73)					
			->setCellValue('I'.$r, $SUMA_TOTAL_IMPUESTO_73)			
			->setCellValue('K'.$r, $SUMA_TOTAL_XIII_73)			
			->setCellValue('M'.$r, $SUMA_TOTAL_OTROS_I_73);
$objPHPExcel->getActiveSheet()->getStyle('B'.$r.':P'.$r)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
$objPHPExcel->getActiveSheet()->getStyle('G'.$r.':M'.$r)->getNumberFormat()
							      ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
$objPHPExcel->getActiveSheet()->mergeCells('B'.$r.':F'.$r);
$objPHPExcel->getActiveSheet()->mergeCells('G'.$r.':H'.$r);
$objPHPExcel->getActiveSheet()->mergeCells('I'.$r.':J'.$r);
$objPHPExcel->getActiveSheet()->mergeCells('K'.$r.':L'.$r);
$objPHPExcel->getActiveSheet()->mergeCells('M'.$r.':N'.$r);

$r++;
$objPHPExcel->getActiveSheet()
			->setCellValue('B'.$r, "Subtotal Certificaciones y Aguinaldo de Navidad- 74")//B:C;						
			->setCellValue('G'.$r, '')					
			->setCellValue('I'.$r, "")			
			->setCellValue('K'.$r, "")			
			->setCellValue('M'.$r, "");
$objPHPExcel->getActiveSheet()->getStyle('B'.$r.':P'.$r)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

$objPHPExcel->getActiveSheet()->mergeCells('B'.$r.':F'.$r);
$objPHPExcel->getActiveSheet()->mergeCells('G'.$r.':H'.$r);
$objPHPExcel->getActiveSheet()->mergeCells('I'.$r.':J'.$r);
$objPHPExcel->getActiveSheet()->mergeCells('K'.$r.':L'.$r);
$objPHPExcel->getActiveSheet()->mergeCells('M'.$r.':N'.$r);

$r++;
$objPHPExcel->getActiveSheet()
			->setCellValue('B'.$r, "Subtotal Bonificaciones - 75")//B:C;						
			->setCellValue('G'.$r, '')					
			->setCellValue('I'.$r, "")			
			->setCellValue('K'.$r, "")			
			->setCellValue('M'.$r, "");
$objPHPExcel->getActiveSheet()->getStyle('B'.$r.':P'.$r)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

$objPHPExcel->getActiveSheet()->mergeCells('B'.$r.':F'.$r);
$objPHPExcel->getActiveSheet()->mergeCells('G'.$r.':H'.$r);
$objPHPExcel->getActiveSheet()->mergeCells('I'.$r.':J'.$r);
$objPHPExcel->getActiveSheet()->mergeCells('K'.$r.':L'.$r);
$objPHPExcel->getActiveSheet()->mergeCells('M'.$r.':N'.$r);

$r++;
$objPHPExcel->getActiveSheet()
			->setCellValue('B'.$r, "Subtotal Dietas - 80")//B:C;						
			->setCellValue('G'.$r, '')					
			->setCellValue('I'.$r, "")			
			->setCellValue('K'.$r, "")			
			->setCellValue('M'.$r, "");
$objPHPExcel->getActiveSheet()->getStyle('B'.$r.':P'.$r)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

$objPHPExcel->getActiveSheet()->mergeCells('B'.$r.':F'.$r);
$objPHPExcel->getActiveSheet()->mergeCells('G'.$r.':H'.$r);
$objPHPExcel->getActiveSheet()->mergeCells('I'.$r.':J'.$r);
$objPHPExcel->getActiveSheet()->mergeCells('K'.$r.':L'.$r);
$objPHPExcel->getActiveSheet()->mergeCells('M'.$r.':N'.$r);

$r++;
$objPHPExcel->getActiveSheet()
			->setCellValue('B'.$r, "Subtotal Primas de Produccion - 81")//B:C;						
			->setCellValue('G'.$r, $SUMA_TOTAL_SALARIO_81)					
			->setCellValue('I'.$r, $SUMA_TOTAL_IMPUESTO_81)			
			->setCellValue('K'.$r, $SUMA_TOTAL_XIII_81)			
			->setCellValue('M'.$r, $SUMA_TOTAL_OTROS_I_81);
$objPHPExcel->getActiveSheet()->getStyle('B'.$r.':P'.$r)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

$objPHPExcel->getActiveSheet()->getStyle('G'.$r.':M'.$r)->getNumberFormat()
							  ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

$objPHPExcel->getActiveSheet()->mergeCells('B'.$r.':F'.$r);
$objPHPExcel->getActiveSheet()->mergeCells('G'.$r.':H'.$r);
$objPHPExcel->getActiveSheet()->mergeCells('I'.$r.':J'.$r);
$objPHPExcel->getActiveSheet()->mergeCells('K'.$r.':L'.$r);
$objPHPExcel->getActiveSheet()->mergeCells('M'.$r.':N'.$r);


$r++;
$objPHPExcel->getActiveSheet()
			->setCellValue('B'.$r, "Subtotal Salario en Especie- 82")//B:C;						
			->setCellValue('G'.$r, '')					
			->setCellValue('I'.$r, "")			
			->setCellValue('K'.$r, "")			
			->setCellValue('M'.$r, "");
$objPHPExcel->getActiveSheet()->getStyle('B'.$r.':P'.$r)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

$objPHPExcel->getActiveSheet()->mergeCells('B'.$r.':F'.$r);
$objPHPExcel->getActiveSheet()->mergeCells('G'.$r.':H'.$r);
$objPHPExcel->getActiveSheet()->mergeCells('I'.$r.':J'.$r);
$objPHPExcel->getActiveSheet()->mergeCells('K'.$r.':L'.$r);
$objPHPExcel->getActiveSheet()->mergeCells('M'.$r.':N'.$r);
$r++;
$objPHPExcel->getActiveSheet()
			->setCellValue('B'.$r, "Subtotal Combustible - 84")//B:C;						
			->setCellValue('G'.$r, '')					
			->setCellValue('I'.$r, "")			
			->setCellValue('K'.$r, "")			
			->setCellValue('M'.$r, "");
$objPHPExcel->getActiveSheet()->getStyle('B'.$r.':P'.$r)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
$objPHPExcel->getActiveSheet()->mergeCells('B'.$r.':F'.$r);
$objPHPExcel->getActiveSheet()->mergeCells('G'.$r.':H'.$r);
$objPHPExcel->getActiveSheet()->mergeCells('I'.$r.':J'.$r);
$objPHPExcel->getActiveSheet()->mergeCells('K'.$r.':L'.$r);
$objPHPExcel->getActiveSheet()->mergeCells('M'.$r.':N'.$r);

$r++;
$objPHPExcel->getActiveSheet()
			->setCellValue('B'.$r, "Subtotal Excelenetes Bonificaciones - 85")//B:C;						
			->setCellValue('G'.$r, '')					
			->setCellValue('I'.$r, "")			
			->setCellValue('K'.$r, "")			
			->setCellValue('M'.$r, "");
$objPHPExcel->getActiveSheet()->getStyle('B'.$r.':P'.$r)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
$objPHPExcel->getActiveSheet()->mergeCells('B'.$r.':F'.$r);
$objPHPExcel->getActiveSheet()->mergeCells('G'.$r.':H'.$r);
$objPHPExcel->getActiveSheet()->mergeCells('I'.$r.':J'.$r);
$objPHPExcel->getActiveSheet()->mergeCells('K'.$r.':L'.$r);
$objPHPExcel->getActiveSheet()->mergeCells('M'.$r.':N'.$r);


$r++;
$objPHPExcel->getActiveSheet()
			->setCellValue('B'.$r, "Total Final")//B:C;						
			->setCellValue('G'.$r, "=SUM(G".($r-9).":G".($r-1).")")					
			->setCellValue('I'.$r, "=SUM(I".($r-9).":I".($r-1).")")			
			->setCellValue('K'.$r, "=SUM(K".($r-9).":K".($r-1).")")			
			->setCellValue('M'.$r, "=SUM(M".($r-9).":M".($r-1).")");																											
$objPHPExcel->getActiveSheet()->mergeCells('B'.$r.':F'.$r);
$objPHPExcel->getActiveSheet()->mergeCells('G'.$r.':H'.$r);
$objPHPExcel->getActiveSheet()->mergeCells('I'.$r.':J'.$r);
$objPHPExcel->getActiveSheet()->mergeCells('K'.$r.':L'.$r);
$objPHPExcel->getActiveSheet()->mergeCells('M'.$r.':N'.$r);

$objPHPExcel->getActiveSheet()->getStyle('B'.$r.':P'.$r)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

$objPHPExcel->getActiveSheet()->getStyle('G'.$r.':M'.$r)->getNumberFormat()
							  ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);


$r++;
$r++;
$objPHPExcel->getActiveSheet()->getStyle('B'.$r.':N'.$r)->getBorders()->getTop()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()
			->setCellValue('B'.$r, "CUADRE DE CUENTAS X PAGAR A LA C.S.S");//
			/*->setCellValue('O'.$r, $SUMA_TOTAL_XIII)//
			->setCellValue('P'.$r, $SUMA_TOTAL_OTROS_I);//*/
$objPHPExcel->getActiveSheet()->mergeCells('B'.$r.':N'.$r);

$objPHPExcel->getActiveSheet()->getStyle('B'.$r.':N'.$r)->getBorders()->getLeft()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle('B'.$r.':N'.$r)->getBorders()->getRight()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

$r++;
$objPHPExcel->getActiveSheet()
			->setCellValue('B'.$r, 'SS Empleados: 9.75%    '.'Patronal: 12.25% + 0.25%')//B:C;			
			/*->setCellValue('C'.$r, "")			
			->setCellValue('D'.$r, "")	*/	
			//->setCellValue('E'.$r, '')
			//->setCellValue('F'.$r, '')			
			->setCellValue('G'.$r, 'XIII MES Empleado: 7.25%  '.'Patronal: 10.75%')		
			//->setCellValue('H'.$r, "")
			//->setCellValue('I'.$r, "")//
			//->setCellValue('J'.$r, '')
			//->setCellValue('K'.$r, "")//
			//->setCellValue('L'.$r, "")//
			->setCellValue('M'.$r, "100% del G.R");//
			//->setCellValue('N'.$r, "");//	

$objPHPExcel->getActiveSheet()->mergeCells('B'.$r.':F'.$r);
$objPHPExcel->getActiveSheet()->mergeCells('G'.$r.':L'.$r);
$objPHPExcel->getActiveSheet()->mergeCells('M'.$r.':N'.$r);
$objPHPExcel->getActiveSheet()->getStyle('B'.$r.':N'.$r)->getBorders()->getLeft()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle('B'.$r.':N'.$r)->getBorders()->getRight()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);


$objPHPExcel->getActiveSheet()->getStyle('B'.$r.':F'.$r)->getBorders()->getRight()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle('B'.$r.':F'.$r)->getBorders()->getBottom()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle('B'.$r.':F'.$r)->getBorders()->getTop()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);   

$objPHPExcel->getActiveSheet()->getStyle('G'.$r.':L'.$r)->getBorders()->getRight()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle('G'.$r.':L'.$r)->getBorders()->getBottom()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle('G'.$r.':L'.$r)->getBorders()->getTop()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

$objPHPExcel->getActiveSheet()->getStyle('N'.$r.':N'.$r)->getBorders()->getRight()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle('M'.$r.':N'.$r)->getBorders()->getBottom()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle('M'.$r.':N'.$r)->getBorders()->getTop()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);        					          					       					  


$r++;

$objPHPExcel->getActiveSheet()
			->setCellValue('B'.$r, 'Detalle')//B:C;			
			/*->setCellValue('C'.$r, "")			
			->setCellValue('D'.$r, "")	*/	
			->setCellValue('E'.$r, "Impuesto\rS/Renta")
			//->setCellValue('F'.$r, '')			
			->setCellValue('G'.$r, "Seguro\rSocial")		
			//->setCellValue('H'.$r, "")
			->setCellValue('I'.$r, "Seguro\rEducativo")//
			//->setCellValue('J'.$r, '')
			->setCellValue('K'.$r, "Riesgos\rProfecionales")//
			//->setCellValue('L'.$r, "")//
			->setCellValue('M'.$r, "TOTAL");//
			//->setCellValue('N'.$r, "");//	

$objPHPExcel->getActiveSheet()->mergeCells('B'.$r.':D'.$r);
$objPHPExcel->getActiveSheet()->mergeCells('E'.$r.':F'.$r);
$objPHPExcel->getActiveSheet()->mergeCells('G'.$r.':H'.$r);
$objPHPExcel->getActiveSheet()->mergeCells('I'.$r.':J'.$r);
$objPHPExcel->getActiveSheet()->mergeCells('K'.$r.':L'.$r);
$objPHPExcel->getActiveSheet()->mergeCells('M'.$r.':N'.$r);

$objPHPExcel->getActiveSheet()->getStyle('B'.($r-3).':N'.$r)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$objPHPExcel->getActiveSheet()->getStyle('B'.$r.':N'.$r)->getBorders()->getLeft()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle('B'.$r.':N'.$r)->getBorders()->getRight()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);


$objPHPExcel->getActiveSheet()->getStyle('B'.$r.':D'.$r)->getBorders()->getRight()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle('B'.$r.':D'.$r)->getBorders()->getLeft()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle('B'.$r.':D'.$r)->getBorders()->getBottom()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle('B'.$r.':D'.$r)->getBorders()->getTop()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);  



$objPHPExcel->getActiveSheet()->getStyle('E'.$r.':F'.$r)->getBorders()->getRight()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle('E'.$r.':F'.$r)->getBorders()->getLeft()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle('E'.$r.':F'.$r)->getBorders()->getBottom()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle('E'.$r.':F'.$r)->getBorders()->getTop()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);  


$objPHPExcel->getActiveSheet()->getStyle('G'.$r.':H'.$r)->getBorders()->getRight()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle('G'.$r.':H'.$r)->getBorders()->getLeft()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle('G'.$r.':H'.$r)->getBorders()->getBottom()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle('G'.$r.':H'.$r)->getBorders()->getTop()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);  

$objPHPExcel->getActiveSheet()->getStyle('I'.$r.':J'.$r)->getBorders()->getRight()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle('I'.$r.':J'.$r)->getBorders()->getLeft()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle('I'.$r.':J'.$r)->getBorders()->getBottom()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle('I'.$r.':J'.$r)->getBorders()->getTop()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN); 

$objPHPExcel->getActiveSheet()->getStyle('K'.$r.':L'.$r)->getBorders()->getRight()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle('K'.$r.':L'.$r)->getBorders()->getLeft()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle('K'.$r.':L'.$r)->getBorders()->getBottom()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle('K'.$r.':L'.$r)->getBorders()->getTop()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN); 

$objPHPExcel->getActiveSheet()->getStyle('M'.$r.':N'.$r)->getBorders()->getRight()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle('M'.$r.':N'.$r)->getBorders()->getLeft()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle('M'.$r.':N'.$r)->getBorders()->getBottom()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle('N'.$r.':N'.$r)->getBorders()->getTop()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN); 


$objPHPExcel->getActiveSheet()->getRowDimension($r)->setRowHeight(30);

$r++;


$objPHPExcel->getActiveSheet()
			->setCellValue('B'.$r, 'Retencion a Empleado')//B:C;			
			/*->setCellValue('C'.$r, "")			
			->setCellValue('D'.$r, "")	*/	
			->setCellValue('E'.$r, $IMPUESTO_SOBRE_RENTA_EMPLEADO)
			//->setCellValue('F'.$r, '')			
			->setCellValue('G'.$r, $SEGURO_SOCIAL_EMPLEADO)		
			//->setCellValue('H'.$r, "")
			->setCellValue('I'.$r, $SEGURO_EDUCATIVO_EMPLEADO)//
			//->setCellValue('J'.$r, '')
			->setCellValue('K'.$r, "0")//
			//->setCellValue('L'.$r, "")//
			->setCellValue('M'.$r, "=SUM(E".$r.":L".$r.")");//
			//->setCellValue('N'.$r, "");//	

$objPHPExcel->getActiveSheet()->mergeCells('B'.$r.':D'.$r);
$objPHPExcel->getActiveSheet()->mergeCells('E'.$r.':F'.$r);
$objPHPExcel->getActiveSheet()->mergeCells('G'.$r.':H'.$r);
$objPHPExcel->getActiveSheet()->mergeCells('I'.$r.':J'.$r);
$objPHPExcel->getActiveSheet()->mergeCells('K'.$r.':L'.$r);
$objPHPExcel->getActiveSheet()->mergeCells('M'.$r.':N'.$r);
$objPHPExcel->getActiveSheet()->getStyle('E'.$r.':M'.$r)->getNumberFormat()
							  ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

$objPHPExcel->getActiveSheet()->getStyle('B'.$r.':N'.$r)->getBorders()->getLeft()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle('B'.$r.':N'.$r)->getBorders()->getRight()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);


$objPHPExcel->getActiveSheet()->getStyle('B'.$r.':D'.$r)->getBorders()->getRight()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle('B'.$r.':D'.$r)->getBorders()->getLeft()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle('B'.$r.':D'.$r)->getBorders()->getBottom()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle('B'.$r.':D'.$r)->getBorders()->getTop()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);  



$objPHPExcel->getActiveSheet()->getStyle('E'.$r.':F'.$r)->getBorders()->getRight()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle('E'.$r.':F'.$r)->getBorders()->getLeft()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle('E'.$r.':F'.$r)->getBorders()->getBottom()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle('E'.$r.':F'.$r)->getBorders()->getTop()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);  


$objPHPExcel->getActiveSheet()->getStyle('G'.$r.':H'.$r)->getBorders()->getRight()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle('G'.$r.':H'.$r)->getBorders()->getLeft()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle('G'.$r.':H'.$r)->getBorders()->getBottom()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle('G'.$r.':H'.$r)->getBorders()->getTop()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);  

$objPHPExcel->getActiveSheet()->getStyle('I'.$r.':J'.$r)->getBorders()->getRight()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle('I'.$r.':J'.$r)->getBorders()->getLeft()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle('I'.$r.':J'.$r)->getBorders()->getBottom()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle('I'.$r.':J'.$r)->getBorders()->getTop()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN); 

$objPHPExcel->getActiveSheet()->getStyle('K'.$r.':L'.$r)->getBorders()->getRight()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle('K'.$r.':L'.$r)->getBorders()->getLeft()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle('K'.$r.':L'.$r)->getBorders()->getBottom()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle('K'.$r.':L'.$r)->getBorders()->getTop()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN); 

$objPHPExcel->getActiveSheet()->getStyle('M'.$r.':N'.$r)->getBorders()->getRight()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle('M'.$r.':N'.$r)->getBorders()->getLeft()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle('M'.$r.':N'.$r)->getBorders()->getBottom()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle('N'.$r.':N'.$r)->getBorders()->getTop()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN); 
$r++;

$objPHPExcel->getActiveSheet()
			->setCellValue('B'.$r, 'Aportaciones Patronales')//B:C;			
			/*->setCellValue('C'.$r, "")			
			->setCellValue('D'.$r, "")	*/	
			->setCellValue('E'.$r, $IMPUESTO_SOBRE_RENTA_PATRONAL)
			//->setCellValue('F'.$r, '')			
			->setCellValue('G'.$r, $SEGURO_SOCIAL_PATRONAL)		
			//->setCellValue('H'.$r, "")
			->setCellValue('I'.$r, $SEGURO_EDUCATIVO_PATRONAL)//
			//->setCellValue('J'.$r, '')
			->setCellValue('K'.$r, $RIESGOS_PROFECIONAL)//
			//->setCellValue('L'.$r, "")//
			->setCellValue('M'.$r, "=SUM(E".$r.":L".$r.")");//
			//->setCellValue('N'.$r, "");//	

$objPHPExcel->getActiveSheet()->mergeCells('B'.$r.':D'.$r);
$objPHPExcel->getActiveSheet()->mergeCells('E'.$r.':F'.$r);
$objPHPExcel->getActiveSheet()->mergeCells('G'.$r.':H'.$r);
$objPHPExcel->getActiveSheet()->mergeCells('I'.$r.':J'.$r);
$objPHPExcel->getActiveSheet()->mergeCells('K'.$r.':L'.$r);
$objPHPExcel->getActiveSheet()->mergeCells('M'.$r.':N'.$r);

$objPHPExcel->getActiveSheet()->getStyle('E'.$r.':M'.$r)->getNumberFormat()
							  ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

$objPHPExcel->getActiveSheet()->getStyle('B'.$r.':N'.$r)->getBorders()->getLeft()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle('B'.$r.':N'.$r)->getBorders()->getRight()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);


$objPHPExcel->getActiveSheet()->getStyle('B'.$r.':D'.$r)->getBorders()->getRight()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle('B'.$r.':D'.$r)->getBorders()->getLeft()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle('B'.$r.':D'.$r)->getBorders()->getBottom()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle('B'.$r.':D'.$r)->getBorders()->getTop()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);  



$objPHPExcel->getActiveSheet()->getStyle('E'.$r.':F'.$r)->getBorders()->getRight()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle('E'.$r.':F'.$r)->getBorders()->getLeft()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle('E'.$r.':F'.$r)->getBorders()->getBottom()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle('E'.$r.':F'.$r)->getBorders()->getTop()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);  


$objPHPExcel->getActiveSheet()->getStyle('G'.$r.':H'.$r)->getBorders()->getRight()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle('G'.$r.':H'.$r)->getBorders()->getLeft()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle('G'.$r.':H'.$r)->getBorders()->getBottom()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle('G'.$r.':H'.$r)->getBorders()->getTop()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);  

$objPHPExcel->getActiveSheet()->getStyle('I'.$r.':J'.$r)->getBorders()->getRight()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle('I'.$r.':J'.$r)->getBorders()->getLeft()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle('I'.$r.':J'.$r)->getBorders()->getBottom()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle('I'.$r.':J'.$r)->getBorders()->getTop()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN); 

$objPHPExcel->getActiveSheet()->getStyle('K'.$r.':L'.$r)->getBorders()->getRight()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle('K'.$r.':L'.$r)->getBorders()->getLeft()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle('K'.$r.':L'.$r)->getBorders()->getBottom()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle('K'.$r.':L'.$r)->getBorders()->getTop()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN); 

$objPHPExcel->getActiveSheet()->getStyle('M'.$r.':N'.$r)->getBorders()->getRight()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle('M'.$r.':N'.$r)->getBorders()->getLeft()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle('M'.$r.':N'.$r)->getBorders()->getBottom()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle('N'.$r.':N'.$r)->getBorders()->getTop()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN); 
$r++;

$objPHPExcel->getActiveSheet()
			->setCellValue('B'.$r, 'Totales por pagar')//B:C;			
			/*->setCellValue('C'.$r, "")			
			->setCellValue('D'.$r, "")	*/	
			->setCellValue('E'.$r, "=SUM(E".($r-2).":E".($r-1).")")
			//->setCellValue('F'.$r, '')
			->setCellValue('G'.$r, "=SUM(G".($r-2).":G".($r-1).")")
			//->setCellValue('H'.$r, "")
			->setCellValue('I'.$r, "=SUM(I".($r-2).":I".($r-1).")")//
			//->setCellValue('J'.$r, '')
			->setCellValue('K'.$r, "=SUM(K".($r-2).":K".($r-1).")")//
			//->setCellValue('L'.$r, "")//
			->setCellValue('M'.$r, "=SUM(M".($r-2).":M".($r-1).")");//
			//->setCellValue('N'.$r, "");//	

$objPHPExcel->getActiveSheet()->mergeCells('B'.$r.':D'.$r);
$objPHPExcel->getActiveSheet()->mergeCells('E'.$r.':F'.$r);
$objPHPExcel->getActiveSheet()->mergeCells('G'.$r.':H'.$r);
$objPHPExcel->getActiveSheet()->mergeCells('I'.$r.':J'.$r);
$objPHPExcel->getActiveSheet()->mergeCells('K'.$r.':L'.$r);
$objPHPExcel->getActiveSheet()->mergeCells('M'.$r.':N'.$r);

$objPHPExcel->getActiveSheet()->getStyle('E'.$r.':M'.$r)->getNumberFormat()
							  ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

$objPHPExcel->getActiveSheet()->getStyle('B'.$r.':N'.$r)->getBorders()->getLeft()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle('B'.$r.':N'.$r)->getBorders()->getRight()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);


$objPHPExcel->getActiveSheet()->getStyle('B'.$r.':D'.$r)->getBorders()->getRight()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle('B'.$r.':D'.$r)->getBorders()->getLeft()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle('B'.$r.':D'.$r)->getBorders()->getBottom()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle('B'.$r.':D'.$r)->getBorders()->getTop()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);  



$objPHPExcel->getActiveSheet()->getStyle('E'.$r.':F'.$r)->getBorders()->getRight()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle('E'.$r.':F'.$r)->getBorders()->getLeft()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle('E'.$r.':F'.$r)->getBorders()->getBottom()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle('E'.$r.':F'.$r)->getBorders()->getTop()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);  


$objPHPExcel->getActiveSheet()->getStyle('G'.$r.':H'.$r)->getBorders()->getRight()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle('G'.$r.':H'.$r)->getBorders()->getLeft()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle('G'.$r.':H'.$r)->getBorders()->getBottom()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle('G'.$r.':H'.$r)->getBorders()->getTop()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);  

$objPHPExcel->getActiveSheet()->getStyle('I'.$r.':J'.$r)->getBorders()->getRight()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle('I'.$r.':J'.$r)->getBorders()->getLeft()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle('I'.$r.':J'.$r)->getBorders()->getBottom()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle('I'.$r.':J'.$r)->getBorders()->getTop()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN); 

$objPHPExcel->getActiveSheet()->getStyle('K'.$r.':L'.$r)->getBorders()->getRight()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle('K'.$r.':L'.$r)->getBorders()->getLeft()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle('K'.$r.':L'.$r)->getBorders()->getBottom()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle('K'.$r.':L'.$r)->getBorders()->getTop()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN); 

$objPHPExcel->getActiveSheet()->getStyle('M'.$r.':N'.$r)->getBorders()->getRight()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle('M'.$r.':N'.$r)->getBorders()->getLeft()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle('M'.$r.':N'.$r)->getBorders()->getBottom()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle('N'.$r.':N'.$r)->getBorders()->getTop()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN); 

$r++; 
$objPHPExcel->getActiveSheet()
			->setCellValue('B'.$r, '* Montos calculados por diferencia: Totales Menos Retenciones a Empleados');

$objPHPExcel->getActiveSheet()->mergeCells('B'.$r.':N'.$r);
$objPHPExcel->getActiveSheet()->getStyle('B'.$r.':B'.$r)->getFont()->setSize(9);

$r++; 
$objPHPExcel->getActiveSheet()
			->setCellValue('B'.$r, 'Prima de produccion que excede del 50% del salario:0.00');
$objPHPExcel->getActiveSheet()->mergeCells('B'.$r.':N'.$r);
$objPHPExcel->getActiveSheet()->getStyle('B'.$r.':B'.$r)->getFont()->setSize(9);


cellColor('A11:P'.$r, 'FFFFFF');
//===========================================================================
//$objPHPExcel->setActiveSheetIndex(0); 
$objPHPExcel->getActiveSheet()->setSelectedCells('I30');



$NOMINA = str_replace(' ', '', $NOMINA);
//LISTADO-PLANILLA_FORMATO_RESUMIDO
$filename = "Planilla_mensual_cuotas_aportes_impuesto_del_".fecha($desde).'_Hasta_'.fecha($hasta);

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
