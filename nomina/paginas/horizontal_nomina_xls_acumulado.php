<?php

/*
	NOTAS
	ELIMINAR LA HOJA EN BLANCA SE CREAR --LISTO
	

	REPORTE ACUMULADO:
	-> MOSTRAR ACUMULADO DEL AÑO EN ATRIBUTO EN LA BDD
	-> FORNULA PARA EL C´ALCULO DE HORAS --LISTO
			$rata_por_hora = (($suesal/4.3333)/$hora_base);
			$rata_por_hora = number_format($rata_por_hora,2);
	
	
	REPORTE RESUMIDO:
	-> CAMPBIAR NOMBRE DE LOS ARCHIVOS  "LISTADO-PLANILLA_FORMATO_RESUMIDO_("FECHA")" --listo
	-> CALCULAR EL NETO A PAGAR EN LAS 2 HOJAS -Listo
	-> MOSTAR LAS HORAS en las 2 p´aginas
	-> CONTROLAR LAS VACACIONES POR EL CAMPO estado 'VACACIONES' --listo
	-> 

	

	REPORTE VACACIONES
	-> VERIFICAR LOS EMPLEADOS QUE ESTEN DE VACACIONES
	-> EL C´ALCULO DE VACACIONES SE TOMAN EN CUENTA EL CONCEPTO 100 (SALARIO) Y LOS OTROS INGRESOS (HORAS EXTRAS, TRABAJO DOMINGO, ETC)
	-> RETROCEDER 11 MESES DESDE UNA FECHA Y SUMAR LOS CONSEPTOS 100 Y 606 
	-> MOSTRAR 11 MESES PARA ATR´AS.
*/
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
							 ->setLastModifiedBy("Selectra");

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
				DATE_FORMAT(np.periodo_ini,'%d') as dia_ini, DATE_FORMAT(np.periodo_fin, '%d') as dia_fin   , np.status
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
$estatus_mov_nomina='';

//$anio = date('Y');


if($fila2['status']=="A"){
	$estatus_mov_nomina ="Aprobado";
}

else if($fila2['status']=="P"){
	$estatus_mov_nomina ="Pendiente";
}

else{
	$estatus_mov_nomina = "";
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
$check_deducciones = false;//
$check_asignaciones = false;
$ASIGNACIONES_ARRAY = array();
$ACUMULADO_ASIGNACIONES_ARRAY= array();
$DEDUCCIONES_ARRAY = array();
$ACUMULADO_DEDUCCIONES_ARRAY= array();
$GRUPOS=array();
$GRUPOS_CANTIDAD=array();

$CANTIDAD_PAGO_CHEQUE=0;
$CANTIDAD_PAGO_TRANSFERENCA=0;
$CANTIDAD_PAGO_EFECTIVO=0;

$MONTO_TOTAL_TRANSFERENCIA = 0;
$MONTO_TOTAL_CHEQUE = 0;
$MONTO_TOTAL_EFECTIVO = 0;


while($row=$res->fetch_array())
{
	$codorg = $row['codorg'];

	$sql2= "SELECT np.ficha,np.cedula, np.cuentacob,  np.suesal , np.hora_base, CONCAT_WS(' ', np.nombres, np.apellidos) as nombre, np.estado, nc.des_car, np.estado,
				   SUBSTRING_INDEX(SUBSTRING_INDEX(np.nombres, ' ', 1), ' ', -1) as primer_nombre, 
				   CASE WHEN np.apellidos LIKE 'De %' THEN 
						     SUBSTRING_INDEX(SUBSTRING_INDEX(np.apellidos, ' ', 2), ' ',  2) 
				   ELSE      SUBSTRING_INDEX(SUBSTRING_INDEX(np.apellidos, ' ', 1), ' ', -1) 
				   END as primer_apellido
			FROM   nompersonal np, nomcargos nc
			WHERE  np.codnivel1=".$codorg." AND np.tipnom=".$codtip." AND nc.cod_car = np.codcargo  
			AND    np.ficha IN (SELECT DISTINCT n.ficha FROM nom_movimientos_nomina n WHERE n.codnom=".$codnom." AND n.tipnom=np.tipnom)
			ORDER BY np.ficha";

	$res2=$conexion->query($sql2);


	$ini=$i; $enc=false;

	$GRUPOS[] = $row['grupo'];

	$cantidad_empleados=0;

	
	//$GRUPOS_CANTIDAD[$row['grupo']]=count($res2->fetch_array());
	
	while($row2=$res2->fetch_array())
	{	$enc=true;
		$ficha 			= $row2['ficha'];
		$cedula 		= $row2['cedula'];
		$cuentacob 		= $row2['cuentacob'];
		$suesal 		= $row2['suesal'];
		$hora_base 		= $row2['hora_base'];
		$rata_por_hora 	= (($suesal/4.3333)/$hora_base);
		$rata_por_hora	= number_format($rata_por_hora,2);

		$refcheque = '';
		$cargo = $row2['des_car'];
		$primer_nombre = utf8_encode($row2['primer_nombre']);
		$apellido   = utf8_encode($row2['primer_apellido']);
		$trabajador = utf8_encode($row2['primer_nombre']).' '.utf8_encode($row2['primer_apellido']); // $row2['nombre']

		//$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':Q'.$i)->getFont()->setName('Calibri');
		$cantidad_empleados++;
		$GRUPOS_CANTIDAD[$row['grupo']]=$cantidad_empleados;
		
		

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
  				  WHERE  n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon=114)  as vacaciones,


  				
  				COALESCE(  (SELECT  COALESCE(SUM(n.monto), 0) FROM nom_movimientos_nomina n 
  				  WHERE   n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon=606 and anio='".$anio."'),0)  as acumulado_salario,
  				COALESCE(  (SELECT  COALESCE(SUM(n.monto), 0) FROM nom_movimientos_nomina n 
  				  WHERE   n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon=607 and anio='".$anio."'),0)  as acumulado_salario_retroactivo,
  				COALESCE(  (SELECT  COALESCE(SUM(n.monto), 0) FROM nom_movimientos_nomina n 
  				  WHERE   n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon=609 and anio='".$anio."'),0)  as acumulado_decimo_salario,
  				COALESCE(  (SELECT  COALESCE(SUM(n.monto), 0) FROM nom_movimientos_nomina n 
  				  WHERE   n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon=610 and anio='".$anio."'),0)  as acumulado_vacaciones,  				
  				COALESCE(   (SELECT  COALESCE(SUM(n.monto), 0) FROM nom_movimientos_nomina n 
  				  WHERE   n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon=612 and anio='".$anio."'),0)  as acumulado_gr,
  				COALESCE(  (SELECT  COALESCE(SUM(n.monto), 0) FROM nom_movimientos_nomina n 
  				  WHERE   n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon=620 and anio='".$anio."'),0)  as acumulado_bonificacion,


  				COALESCE((SELECT  COALESCE(SUM(n.monto), 0) FROM nom_movimientos_nomina n 
  				  WHERE   n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon=605 and anio='".$anio."'),0)  as d_acumulado_isl, 
  				COALESCE(  (SELECT  COALESCE(SUM(n.monto), 0) FROM nom_movimientos_nomina n 
  				  WHERE   n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon=611 and anio='".$anio."'),0)  as d_acumulado_isl_gr,
				COALESCE(  (SELECT  COALESCE(SUM(n.monto), 0) FROM nom_movimientos_nomina n 
  				  WHERE   n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon=621 and anio='".$anio."'),0)  as d_acumulado_ss,
  				COALESCE(  (SELECT  COALESCE(SUM(n.monto), 0) FROM nom_movimientos_nomina n 
  				  WHERE   n.tipnom=".$codtip." AND n.ficha=".$ficha." AND n.codcon=622 and anio='".$anio."'),0)  as d_acumulado_se  				  
  				  

  				  ";
				 //echo $sql3;exit;
		$res3 = $conexion->query($sql3);
		$neto=0;

		$acumulado_salario 				= 0;
		$acumulado_salario_retroactivo	= 0;
		$acumulado_decimo_salario 		= 0;
		$acumulado_vacaciones			= 0;	
		$acumulado_gr 					= 0;
		$acumulado_bonificacion 		= 0;
		

		$acumulado_isl 					= 0;
		$acumulado_isl_gr				= 0;

		if($row3=$res3->fetch_array())
		{
			
			$MONTO_EXTRA_SALARIO = $MONTO_DOMINGO_SALARIO = 0;
			$MONTO_GR_EXTRA_SALARIO = $MONTO_GR_DOMINGO_SALARIO = 0;
			$AJUSTE = 0;


			$acumulado_salario 				= $row3['acumulado_salario'];
			$acumulado_salario_retroactivo	= $row3['acumulado_salario_retroactivo'];
			$acumulado_decimo_salario 		= $row3['acumulado_decimo_salario'];
			$acumulado_vacaciones			= $row3['acumulado_vacaciones'];			
			$acumulado_gr 					= $row3['acumulado_gr'];
			$acumulado_bonificacion 		= $row3['acumulado_bonificacion'];

			//deducciones
			$acumulado_isl 					= $row3['d_acumulado_isl'];
			$acumulado_isl_gr				= $row3['d_acumulado_isl_gr'];

			

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
			

			
			$neto2 = $row3['salario'] + $row3['vacaciones']+ $row3['reembolso']+ $row3['comision']+ $row3['bono'] + $HORAS_EXTRA_SALARIO 
				   - $row3['seguro_social'] - $row3['seguro_educativo'] - $row3['tardanza'] - $row3['ausencia']
				   - $row3['isr'] - $row3['isr_gastos'] - $row3['descuentos'] - $row3['cxc']- $AJUSTE;

			//$objPHPExcel->getActiveSheet()->setCellValue('Q'.$i, $neto);
			
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

			$sql7 = "SELECT COALESCE( SUM( n.monto ) , 0 ) AS total_asignacion, c.codcon, c.descrip, c.tipcon
						FROM nom_movimientos_nomina AS n, nomconceptos AS c
						WHERE n.codcon = c.codcon
						AND n.codnom =".$codnom."
						AND n.tipnom =".$codtip."
						AND n.ficha =".$ficha."
						AND c.tipcon = 'A'
						GROUP BY c.codcon
						ORDER BY n.codcon ASC ";
			$res7 =$conexion->query($sql7);
			$asignaciones = $conexion->query($sql7);
			
			

			$dd=0;
			$asignaciones_monto 	= 0;
			$deducciones_monto  	= 0;
			while($asig=$res7->fetch_array())
			{
				//$dd++;
				$asignaciones_monto += $asig['total_asignacion'];
				$key_to_index = $asig['codcon'];							
				if($key_to_index=='100'){//sueldo
					$acumulado = $acumulado_salario;
				}
				/*else if($key_to_index=='202'){//IMPUESTO SOBRE LA RENTA (I.S.R.)
					$acumulado = $acumulado_isl;
				}*/
				else if($key_to_index=='114'){//VACACIONES
					$acumulado = $acumulado_vacaciones;
				}
				/*else if($key_to_index=='208'){//IMPUESTO SOBRE LA RENTA (I.S.R.) GR
					$acumulado = $acumulado_isl_gr;
				}*/	
				else {//
					$acumulado = '0.00';
				}
				
				$ACUMULADO_ASIGNACIONES_ARRAY[$ficha][$key_to_index] = $acumulado;


				//$ASIGNACIONES_ARRAY[$key_to_index]['acumulado']	= $key_to_index;

				if(is_array($ASIGNACIONES_ARRAY[$key_to_index][$row['grupo']])) {//Modificamos
					# code...
					$ASIGNACIONES_ARRAY[$key_to_index][$row['grupo']]['hora']+=$rata_por_hora;
					$ASIGNACIONES_ARRAY[$key_to_index][$row['grupo']]['periodo']+=$asig['total_asignacion'];
					$ASIGNACIONES_ARRAY[$key_to_index][$row['grupo']]['mes']+=($asig['total_asignacion']*2);
					$ASIGNACIONES_ARRAY[$key_to_index][$row['grupo']]['anho']+=$acumulado;
					//$ASIGNACIONES_ARRAY[$key_to_index][$row['grupo']]['acumulado']+=$acumulado;

				}
				else{ //no existe, lo agregamos
					//$ASIGNACIONES_ARRAY[$key_to_index]['hora'] 	+=0;

					//$dd++;
					$ASIGNACIONES_ARRAY[$key_to_index][$row['grupo']]= array('hora' 	=> $rata_por_hora,
																			'ingreso' 	=>$asig['descrip'],
																			'periodo' 	=>$asig['total_asignacion'],
																			'mes' 		=>($asig['total_asignacion']*2),
																			'anho' 		=>$acumulado
																			//'acumulado' =>$acumulado
																	
												);

				}

				//$refcheque = $row8['cheque'];
			}


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
			$deducciones = $conexion->query($sql6);

			

			while($deduc=$res6->fetch_array())
			{
				//$dd++;
				$deducciones_monto  	+= $deduc['total_deduccion'];
				$key_to_index = $deduc['codcon'];				

				
				if($key_to_index=='202'){//IMPUESTO SOBRE LA RENTA (I.S.R.)
					$acumulado = $acumulado_isl;
				}				
				else if($key_to_index=='208'){//IMPUESTO SOBRE LA RENTA (I.S.R.) GR
					$acumulado = $acumulado_isl_gr;
				}	
				else {//
					$acumulado = '0.00';
				}
				
				$ACUMULADO_DEDUCCIONES_ARRAY[$ficha][$key_to_index] = $acumulado;

				if(is_array($DEDUCCIONES_ARRAY[$key_to_index][$row['grupo']])) {//Modificamos

					# code...
					$DEDUCCIONES_ARRAY[$key_to_index][$row['grupo']]['periodo']+=$deduc['total_deduccion'];
					$DEDUCCIONES_ARRAY[$key_to_index][$row['grupo']]['mes']+=($deduc['total_deduccion']*2);
					$DEDUCCIONES_ARRAY[$key_to_index][$row['grupo']]['anho']+=$acumulado;
					
				}
				else{ //no existe, lo agregamos
					//$ASIGNACIONES_ARRAY[$key_to_index]['hora'] 	+=0;
					//$dd++;
					$DEDUCCIONES_ARRAY[$key_to_index][$row['grupo']]= array('hora' 		=> 0,
																			'ingreso' 	=>$deduc['descrip'],
																			'periodo' 	=>$deduc['total_deduccion'],
																			'mes' 		=>($deduc['total_deduccion']*2),
																			'anho' 		=>$acumulado   
																	
												);

				}

				//$ACUMULADO_DEDUCCIONES_ARRAY[$ficha][$deduc['codcon']] = 0.00;


				//$refcheque = $row8['cheque'];
			}

			$sql8 ="SELECT  n.refcheque  as cheque  FROM nom_movimientos_nomina n  WHERE n.codcon=100 AND n.codnom=".$codnom." AND n.tipnom=".$codtip." AND n.ficha=".$ficha;
	  		$res8 =$conexion->query($sql8);


	  		while($row8=$res8->fetch_array())
			{
				$refcheque = $row8['cheque'];


				if ($row8['cheque']!=0) {			
					$CANTIDAD_PAGO_CHEQUE++;
					$MONTO_TOTAL_CHEQUE += ($asignaciones_monto-$deducciones_monto);
				}
				else{
					$CANTIDAD_PAGO_TRANSFERENCA++;
					$MONTO_TOTAL_TRANSFERENCIA += ($asignaciones_monto-$deducciones_monto);
				}					
					///CANTIDAD_PAGO_EFECTIV
					//$MONTO_TOTAL_EFECTIVO
			}
			

			
			
			
  			

					
		}


		//$horas_extras[] = $HORAS_EXTRA_SALARIO;

		//SELECT codcon, descrip, tipcon FROM nomconceptos ORDER BY tipcon, codcon, descrip
		
		/*if(!$check_deducciones){
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



		}*/

		/*if(!$check_asignaciones){
			$check_asignaciones = true;*/
			
			/*while($row7=$res7->fetch_array())
			{					
				if($row7['codcon']==100 || $row7['codcon']==145){
					$row7['codcon'] =$row7['valor'];
				}

				$asignaciones[] 	= array('cod' => $row7['codcon'],'descripcion'=>$row7['descrip'],'monto'=>$row7['total_deduccion']);
			}*/
		/*}*/
		

		$trabajadores[] = array('ficha'=>$ficha,'cedula'=>$cedula,'nombre'=>$trabajador, 'primer_nombre'=> $primer_nombre, 'apellido'=>$apellido, 'neto'=> $neto,'ingreso_regular'=>$ingreso_regular,'otros_ingresos'=>$otros_ingresos,'total_Ingresos'=>$total_Ingresos, 'neto2'=>$neto2,'seguro_social'=>$seguro_social,'total_retener'=>$total_retener,'impuesto'=>$impuesto,'asignaciones'=>$asignaciones,'deducciones'=>$deducciones,'cuenta'=>$cuentacob,'cheque'=>$refcheque,'cargo'=>$cargo,'estado'=>$row2['estado']);

		$i++;
	}

	
}

//$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':Q'.$i);


array_multisort($aux, SORT_ASC, $trabajadores);

if($NOMINA != 'TEMPORAL'){
$j = $i;
$i = $i + 2; 
$i++;
$ss=$i;
$i++;
$se=$i;



} // Fin if NOMINA!='TEMPORAL'
//==================================================================================================


//===========================================================================

/*$fecha_desde = explode('/', $desde);
$fecha_hasta = explode('/', $hasta);*/

//$objPHPExcel->createSheet();
//$objPHPExcel->setActiveSheetIndex(1); 

$objPHPExcel->getActiveSheet()->setTitle('Resumido pago');

$objPHPExcel->getActiveSheet()
			->setCellValue('B1', 'Control #.')
			->setCellValue('C1', strtoupper($empresa))
			->setCellValue('C2', 'Planilla Regular No.18')
			//->setCellValue('C1', '')
			->setCellValue('C3', strtoupper($NOMINA." Del ".fecha($desde).' al '.fecha($hasta)))
			->setCellValue('J1', 'Ref: MM-704')
			->setCellValue('J2', PHPExcel_Shared_Date::FormattedPHPToExcel($anio, $mes_numero, $dia_fin) )
			->setCellValue('J3', $estatus_mov_nomina);


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

$objPHPExcel->getActiveSheet()->getStyle('J1:J3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

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
/*$objPHPExcel->getActiveSheet()->getStyle('B6:K6')->getBorders()->getBottom()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);*/

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
$cont_vacaciones 	= 0;
$cont_activos 		= 0;



foreach ($trabajadores as $key => $row) 
{
	$check_vacacion = '';

	if($row['estado']=='Vacaciones'){
		$check_vacacion = '<<<En Vac.>>>';//
		$cont_vacaciones++;
	}
	else
		$cont_activos++;

    //echo $row['nombre'].' '.$row['neto'].'<br/>';
	$objPHPExcel->getActiveSheet()
				->setCellValue('B'.$i, strtoupper($row['ficha'].' - '.$row['apellido'].', '.$row['primer_nombre']) )
				->setCellValue('C'.$i, '')
				->setCellValue('D'.$i, strtoupper($row['cargo']))				
				->setCellValue('H'.$i, '')
				->setCellValue('I'.$i, 'Número Emp.:')
				->setCellValue('J'.$i, $row['ficha'])
				->setCellValue('K'.$i, '');

	$objPHPExcel->getActiveSheet()->mergeCells('B'.$i.':C'.$i);	
	$objPHPExcel->getActiveSheet()->mergeCells('D'.$i.':G'.$i);	

	
    $objPHPExcel->getActiveSheet()->getStyle('B'.$i.':C'.$i)->getFont()->setSize(12);
    $objPHPExcel->getActiveSheet()->getStyle('B'.$i.':C'.$i)->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('B'.$i.':C'.$i)->getFont()->setItalic(true);
 	$i++;

    $objPHPExcel->getActiveSheet()
				->setCellValue('B'.$i, 'Cédula No.:' )
				->setCellValue('C'.$i, $row['cedula'])
				->setCellValue('D'.$i, '')
				->setCellValue('E'.$i, 'Clave ISR:')
				->setCellValue('F'.$i, '')
				->setCellValue('G'.$i, '')
				->setCellValue('H'.$i, '')
				->setCellValue('I'.$i, 'Tarifa x Hora:')
				->setCellValue('J'.$i, '')
				->setCellValue('K'.$i, '');
    $objPHPExcel->getActiveSheet()->getStyle('B'.$i.':K'.$i)->getBorders()->getBottom()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);	

    $objPHPExcel->getActiveSheet()->getStyle('B'.$i.':K'.$i)->getFont()->setSize(12);
    $objPHPExcel->getActiveSheet()->getStyle('B'.$i.':K'.$i)->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('B'.$i.':K'.$i)->getFont()->setItalic(true);


	$i++;
    $objPHPExcel->getActiveSheet()
				->setCellValue('B'.$i, 'Horas' )
				->setCellValue('C'.$i, 'Ingresos')
				->setCellValue('D'.$i, 'Periodo')
				->setCellValue('E'.$i, 'Mes')
				->setCellValue('F'.$i, 'Año')
				
				->setCellValue('G'.$i, 'Retenciones')
				//->setCellValue('H'.$i, 'Periodo')
				->setCellValue('I'.$i, 'Periodo')
				->setCellValue('J'.$i, 'Mes')
				->setCellValue('K'.$i, 'Año');

	//$objPHPExcel->getActiveSheet()->mergeCells('C'.$i.':D'.$i);
	$objPHPExcel->getActiveSheet()->mergeCells('G'.$i.':H'.$i);
	$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':K'.$i)->getFont()->setSize(9);
    $objPHPExcel->getActiveSheet()->getStyle('B'.$i.':K'.$i)->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('D'.$i.':F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
    $objPHPExcel->getActiveSheet()->getStyle('I'.$i.':K'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
    //$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':K'.$i)->getFont()->setItalic(true);		
    $i++;
    $x=$i;
    $y=$i;

    $TOTAL_ASIGNACION_1 =0;
    $TOTAL_ASIGNACION_2 =0;
    $TOTAL_ASIGNACION_3 =0;

    $TOTAL_DEDUCCIONES_1 =0;
    $TOTAL_DEDUCCIONES_2 =0;
    $TOTAL_DEDUCCIONES_3 =0;

    while($row2=$row['asignaciones']->fetch_array())
	{//$row7['codcon'],'descripcion'=>$row7['descrip'],'monto'=>$row7['total_deduccion']
		
		$objPHPExcel->getActiveSheet()
					->setCellValue('B'.$i, $row2['hora'])
					->setCellValue('C'.$x, strtoupper($row2['descrip']))
					->setCellValue('D'.$x, $row2['total_asignacion'])
					->setCellValue('E'.$x, ($row2['total_asignacion']*2))
					->setCellValue('F'.$x, $ACUMULADO_ASIGNACIONES_ARRAY[$row['ficha']][$row2['codcon']]);
		$objPHPExcel->getActiveSheet()->getStyle('D'.$x.':F'.$x)->getNumberFormat()
								  ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);	
		$objPHPExcel->getActiveSheet()->getStyle('C'.$x.':F'.$x)->getFont()->setSize(7);			
		$objPHPExcel->getActiveSheet()->getStyle('E'.$x)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$TOTAL_ASIGNACION_1+=$row2['total_asignacion'];
		$TOTAL_ASIGNACION_2+=($row2['total_asignacion']*2);
		$TOTAL_ASIGNACION_3+=$ACUMULADO_ASIGNACIONES_ARRAY[$row['ficha']][$row2['codcon']];

		$x++;
	}


	while($row3=$row['deducciones']->fetch_array())
	{//$row7['codcon'],'descripcion'=>$row7['descrip'],'monto'=>$row7['total_deduccion']
		
		$objPHPExcel->getActiveSheet()
					//->setCellValue('D'.$i, $row2['cod'])
					->setCellValue('G'.$y, strtoupper($row3['descrip']))
					->setCellValue('I'.$y, $row3['total_deduccion'])
					->setCellValue('J'.$y, ($row3['total_deduccion']*2))
					->setCellValue('K'.$y, $ACUMULADO_DEDUCCIONES_ARRAY[$row['ficha']][$row3['codcon']]);


		$objPHPExcel->getActiveSheet()->getStyle('I'.$y.':K'.$y)->getNumberFormat()
								  ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);	

		$TOTAL_DEDUCCIONES_1+=$row3['total_deduccion'];
		$TOTAL_DEDUCCIONES_2+=($row3['total_deduccion']*2);
		$TOTAL_DEDUCCIONES_3+=$ACUMULADO_DEDUCCIONES_ARRAY[$row['ficha']][$row3['codcon']];

		$objPHPExcel->getActiveSheet()->getStyle('G'.$y.':K'.$y)->getFont()->setSize(7);			
		$objPHPExcel->getActiveSheet()->getStyle('I'.$y)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$y++;
	}

	if($x>=$y)
		$i=$x;
	else
		$i=$y;

	$i++;
	$objPHPExcel->getActiveSheet()					
				->setCellValue('B'.$i, $check_vacacion)
				->setCellValue('C'.$i, 'Totales:')
				->setCellValue('D'.$i, $TOTAL_ASIGNACION_1)
				->setCellValue('E'.$i, $TOTAL_ASIGNACION_2)
				->setCellValue('F'.$i, $TOTAL_ASIGNACION_3)
				->setCellValue('G'.$i, 'Totales')
				->setCellValue('H'.$i, '' )
				->setCellValue('I'.$i, $TOTAL_DEDUCCIONES_1)
				->setCellValue('J'.$i, $TOTAL_DEDUCCIONES_2)
				->setCellValue('K'.$i, $TOTAL_DEDUCCIONES_3);

	$objPHPExcel->getActiveSheet()->getStyle('D'.$i.':F'.$i)->getNumberFormat()
								  ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);	
	$objPHPExcel->getActiveSheet()->getStyle('I'.$i.':K'.$i)->getNumberFormat()
								  ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);	

	/*$objPHPExcel->getActiveSheet()->getStyle('I'.$i.':K'.$i)->getNumberFormat()
								  ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);	*/
	$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':K'.$i)->getFont()->setSize(7);			
	$objPHPExcel->getActiveSheet()->getStyle('C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
	$objPHPExcel->getActiveSheet()->getStyle('G'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
	$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':K'.$i)->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('B'.$i.':K'.$i)->getFont()->setItalic(true);


	$i++;
	$objPHPExcel->getActiveSheet()					
				->setCellValue('B'.$i, 'Cuenta No.:')
				->setCellValue('C'.$i, $row['cuenta'])
				->setCellValue('E'.$i, 'Cheque No.:')
				->setCellValue('F'.$i, $row['cheque'])
				->setCellValue('I'.$i, 'Neto a Pagar x Periodo:')
				->setCellValue('J'.$i, ($TOTAL_ASIGNACION_1-$TOTAL_DEDUCCIONES_1));
	$objPHPExcel->getActiveSheet()->getStyle('J'.$i)->getNumberFormat()
								  ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);	
	$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':K'.$i)->getFont()->setSize(10);			
	//$objPHPExcel->getActiveSheet()->getStyle('I'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
	$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':K'.$i)->getFont()->setBold(true);
    $objPHPExcel->getActiveSheet()->getStyle('B'.$i.':K'.$i)->getFont()->setItalic(true);

///$i++;
     $objPHPExcel->getActiveSheet()->getStyle('B'.$i.':K'.$i)->getBorders()->getBottom()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_DASHED);	

	/*$objPHPExcel->getActiveSheet()
				->setCellValue('B'.($i+1),'' )
				->setCellValue('C'.$i, strtoupper($row['apellido'].', '.$row['primer_nombre']))
				->setCellValue('D'.$i, '')								
				->setCellValue('E'.$i, $row['ingreso_regular'])
				->setCellValue('F'.$i, $row['otros_ingresos'])
				->setCellValue('G'.$i, $row['total_Ingresos'])
				->setCellValue('H'.$i, $row['impuesto'])
				->setCellValue('I'.$i, $row['seguro_social'])
				->setCellValue('J'.$i, $row['total_retener'])
				
				->setCellValue('K'.$i, $row['neto'] );*/

	/*$TotalAPagar 			+=$row['neto'];
	$Totalingresoregular 	+= $row['ingreso_regular'];
	$TotalOtrosIngresos 	+= $row['otros_ingresos'];
	$TotalTotalIngreso 		+= $row['total_Ingresos'];
	$TotalImpuesto			+= $row['impuesto'];
	$TotalSeguro 			+= $row['seguro_social'];
	$TotalTotalRetener 		+= $row['total_retener'];*/

	cellColor('A'.$i.':K'.$i, 'FFFFFF');

	/*$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':F'.$i)->getFont()->setSize(9);*/

	//$objPHPExcel->getActiveSheet()->getStyle('C'.$i)->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('C'.$i)->getFont()->setItalic(true);
	//$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':K'.$i)->getBorders()->getBottom()
        					      //->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);



		


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
$total_a_asignacion 	= 0;
$total_a_deducciones 	= 0;
$total_a_pagar 			= 0;

foreach ($GRUPOS as $grupo) {
	if($GRUPOS_CANTIDAD[$grupo]>=1){

		
		$i++;
		$objPHPExcel->getActiveSheet()
						->setCellValue('B'.$i, 'Sub total' )
						->setCellValue('C'.$i, $grupo)
						
						//->setCellValue('H'.$i, 'Periodo')
						->setCellValue('I'.$i, $GRUPOS_CANTIDAD[$grupo].' Empleados');
						

			//$objPHPExcel->getActiveSheet()->mergeCells('C'.$i.':D'.$i);
		$objPHPExcel->getActiveSheet()->mergeCells('G'.$i.':H'.$i);
		$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':K'.$i)->getFont()->setSize(9);
		$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':K'.$i)->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('D'.$i.':F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$objPHPExcel->getActiveSheet()->getStyle('I'.$i.':K'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

		$i++;
		$objPHPExcel->getActiveSheet()
					->setCellValue('B'.$i, 'Horas' )
					->setCellValue('C'.$i, 'Ingresos')
					->setCellValue('D'.$i, 'Periodo')
					->setCellValue('E'.$i, 'Mes')
					->setCellValue('F'.$i, 'Año')
						
					->setCellValue('G'.$i, 'Retenciones')
					//->setCellValue('H'.$i, 'Periodo')
					->setCellValue('I'.$i, 'Periodo')
					->setCellValue('J'.$i, 'Mes')
					->setCellValue('K'.$i, 'Año');

			//$objPHPExcel->getActiveSheet()->mergeCells('C'.$i.':D'.$i);
		$objPHPExcel->getActiveSheet()->mergeCells('G'.$i.':H'.$i);
		$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':K'.$i)->getFont()->setSize(9);
		$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':K'.$i)->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('D'.$i.':F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$objPHPExcel->getActiveSheet()->getStyle('I'.$i.':K'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		//$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':K'.$i)->getFont()->setItalic(true);		
		$i++;

		$xx=$i;
		$yy=$i;
		$ii=$i;
		foreach ($ASIGNACIONES_ARRAY as $key => $value) {			
			//foreach ($value as $key2 =>$asignacion) {
				$objPHPExcel->getActiveSheet()
							->setCellValue('B'.$xx, $value[$grupo]['hora'])
							->setCellValue('C'.$xx, strtoupper($value[$grupo]['ingreso']))
							->setCellValue('D'.$xx, $value[$grupo]['periodo'])
							->setCellValue('E'.$xx, $value[$grupo]['mes'])
							->setCellValue('F'.$xx, $value[$grupo]['anho']);
				$objPHPExcel->getActiveSheet()->getStyle('B'.$xx.':F'.$xx)->getNumberFormat()
										  ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);	
				$objPHPExcel->getActiveSheet()->getStyle('B'.$xx.':F'.$xx)->getFont()->setSize(7);			
				$objPHPExcel->getActiveSheet()->getStyle('E'.$xx)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);			
			//}
			$xx++;	
				
		}

		foreach ($DEDUCCIONES_ARRAY as $key => $value) {
			
			//foreach ($value as $key2 =>$asignacion) {
				
				$objPHPExcel->getActiveSheet()
							//->setCellValue('D'.$i, $row2['cod'])
							->setCellValue('G'.$yy, strtoupper($value[$grupo]['ingreso']))
							->setCellValue('I'.$yy, $value[$grupo]['periodo'])
							->setCellValue('J'.$yy, $value[$grupo]['mes'])
							->setCellValue('K'.$yy, $value[$grupo]['anho']);

				$objPHPExcel->getActiveSheet()->getStyle('I'.$yy.':K'.$yy)->getNumberFormat()
										  	  ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);	
				$objPHPExcel->getActiveSheet()->getStyle('G'.$yy.':K'.$yy)->getFont()->setSize(7);			
				$objPHPExcel->getActiveSheet()->getStyle('I'.$yy)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$yy++;
			//}			
		}

		if($xx>$yy)
			$i=$xx;
		else
			$i=$yy;

		$i++;
		$objPHPExcel->getActiveSheet()
					->setCellValue('B'.$i, '')
					->setCellValue('D'.$i, "=SUM(D".$ii.":D".($i-1).")" )
					->setCellValue('E'.$i, "=SUM(E".$ii.":E".($i-1).")" )
					->setCellValue('F'.$i, "=SUM(F".$ii.":F".($i-1).")" )
					//->setCellValue('G'.$i, "=SUM(G".$ii.":G".($i-1).")" )
					//->setCellValue('H'.$i, "=SUM(H".$ii.":H".($i-1).")" )
					->setCellValue('I'.$i, "=SUM(I".$ii.":I".($i-1).")" )
					->setCellValue('J'.$i, "=SUM(J".$ii.":J".($i-1).")" )
					->setCellValue('K'.$i, "=SUM(K".$ii.":K".($i-1).")" );
		$total_a_asignacion += $objPHPExcel->getActiveSheet()->getCell('D'.$i)->getCalculatedValue();
		$total_a_deducciones += $objPHPExcel->getActiveSheet()->getCell('I'.$i)->getCalculatedValue();
		$total_a_pagar = $total_a_asignacion-$total_a_deducciones;

		$objPHPExcel->getActiveSheet()->getStyle('D'.$i.':F'.$i)->getBorders()->getTop()
		        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

		$objPHPExcel->getActiveSheet()->getStyle('I'.$i.':K'.$i)->getBorders()->getTop()
		        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

		$objPHPExcel->getActiveSheet()->getStyle('K'.$i)->getFont()->setBold(true);

		$objPHPExcel->getActiveSheet()->getStyle('D'.$i.':F'.$i)->getNumberFormat()
										  	  ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);	

		$objPHPExcel->getActiveSheet()->getStyle('I'.$i.':K'.$i)->getNumberFormat()
										  	  ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);	

		$i++;
		$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':K'.$i)->getBorders()->getBottom()
	        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_DASHED);

		//$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':K'.$i)->getBorders()->getTop()
		        					  //->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);	
	}	        					  								  	  								  	
}
$i++;
$i++;
$i++;
$objPHPExcel->getActiveSheet()
			->setCellValue('B'.$i, 'Neto a Pagar x Periodo:')
			->setCellValue('K'.$i, $total_a_pagar );
$objPHPExcel->getActiveSheet()->mergeCells('B'.$i.':J'.$i);
$objPHPExcel->getActiveSheet()->getStyle('B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':K'.$i)->getBorders()->getBottom()
	        				  ->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
$objPHPExcel->getActiveSheet()->getStyle('K'.$i)->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':K'.$i)->getFont()->setSize(14);
$objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(20);
$objPHPExcel->getActiveSheet()->getStyle('K'.$i)->getNumberFormat()
							->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);	
$i++;
$i++;

$objPHPExcel->getActiveSheet()
			->setCellValue('B'.$i, 'CONTROL DE EMPLEADOS')
			/*->setCellValue('C'.$i, '')
			->setCellValue('D'.$i, '')
			->setCellValue('E'.$i, '')*/
			//->setCellValue('F'.$i, '')
			->setCellValue('G'.$i, 'CONTROL DE PLANILLA')
			/*->setCellValue('H'.$i, '')
			->setCellValue('I'.$i, '')
			->setCellValue('J'.$i, '')
			->setCellValue('K'.$i, '')*/;
$objPHPExcel->getActiveSheet()->mergeCells('B'.$i.':E'.$i);
$objPHPExcel->getActiveSheet()->mergeCells('G'.$i.':K'.$i);

$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('G'.$i.':K'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':K'.$i)->getFont()->setSize(14);
$objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(20);
$i++;

$objPHPExcel->getActiveSheet()
			->setCellValue('B'.$i, 'Activos')
			->setCellValue('C'.$i, 'Vacaciones')
			->setCellValue('D'.$i, 'Licencias')
			->setCellValue('E'.$i, 'Total')
		//->setCellValue('F'.$i, '')
			->setCellValue('G'.$i, 'Efectivo')
			->setCellValue('H'.$i, 'Cheques')
			//->setCellValue('I'.$i, '')
			->setCellValue('J'.$i, 'Transferencias')
			->setCellValue('K'.$i, 'Total');
$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':K'.$i)->getFont()->setSize(14);
$objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(20);
$objPHPExcel->getActiveSheet()->mergeCells('H'.$i.':I'.$i);
$i++;



$objPHPExcel->getActiveSheet()
			->setCellValue('B'.$i, $cont_activos)
			->setCellValue('C'.$i, $cont_vacaciones)
			->setCellValue('D'.$i, '0')
			->setCellValue('E'.$i, count($trabajadores))
		//->setCellValue('F'.$i, '')
			->setCellValue('G'.$i, $CANTIDAD_PAGO_EFECTIVO)
			->setCellValue('H'.$i, $CANTIDAD_PAGO_CHEQUE)
			//->setCellValue('I'.$i, '')
			->setCellValue('J'.$i, $CANTIDAD_PAGO_TRANSFERENCA)
			->setCellValue('K'.$i, count($trabajadores));
$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':K'.$i)->getFont()->setSize(14);
$objPHPExcel->getActiveSheet()->getStyle('B'.($i-1).':K'.$i)->applyFromArray(allBordersThin());
$objPHPExcel->getActiveSheet()->mergeCells('H'.$i.':I'.$i);

$objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(20);


$i++;


$objPHPExcel->getActiveSheet()
			->setCellValue('B'.$i, '')
			->setCellValue('C'.$i, '')
			->setCellValue('D'.$i, '')
			->setCellValue('E'.$i, '')
		//->setCellValue('F'.$i, '')
			->setCellValue('G'.$i, '0')
			->setCellValue('H'.$i, $MONTO_TOTAL_CHEQUE)
			//->setCellValue('I'.$i, '')
			->setCellValue('J'.$i, $MONTO_TOTAL_TRANSFERENCIA)
			->setCellValue('K'.$i, $total_a_pagar);
$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':K'.$i)->getFont()->setSize(14);
$objPHPExcel->getActiveSheet()->getStyle('B'.($i-1).':K'.$i)->applyFromArray(allBordersThin());
$objPHPExcel->getActiveSheet()->getStyle('G'.$i.':K'.$i)->getNumberFormat()
							->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
$objPHPExcel->getActiveSheet()->mergeCells('H'.$i.':I'.$i);

$objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(20);
	/*$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':K'.$i)->getBorders()->getBottom()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_DASHED);*/



			/*->setCellValue('H'.$i, "=SUM(H7:H".($i-2).")" )
			->setCellValue('I'.$i, "=SUM(I7:I".($i-2).")" )
			->setCellValue('J'.$i, "=SUM(J7:J".($i-2).")" )
			->setCellValue('K'.$i, "=SUM(K7:K".($i-2).")" );*/

//$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':K'.$i)->getBorders()->getBottom()
        					 // ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
//$objPHPExcel->getActiveSheet()->getStyle('K'.$i)->getFont()->setBold(true);

/*$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);*/

//$TotalIngresoregular = $objPHPExcel->getActiveSheet()->getCell('G'.$i)->getCalculatedValue();
//$TotalTotalRetener   = $objPHPExcel->getActiveSheet()->getCell('J'.$i)->getCalculatedValue();
/*TotalOtrosIngresos
TotalTotalIngreso
TotalImpuesto
TotalSeguro
TotalTotalRetener*/




			//$importe=$TotalAPagar;
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



$i++;
$i++;
$i++;
$i++;
$i++;
$i++;
 $objPHPExcel->getActiveSheet()			
			->setCellValue('B'.$i,'Elaborado por' )
			->setCellValue('H'.$i,'Aprobado por' );
$objPHPExcel->getActiveSheet()->mergeCells('B'.$i.':D'.$i);	
$objPHPExcel->getActiveSheet()->mergeCells('H'.$i.':J'.$i);	

$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':D'.$i)->getBorders()->getTop()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

$objPHPExcel->getActiveSheet()->getStyle('H'.$i.':J'.$i)->getBorders()->getTop()
        					  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('H'.$i.':J'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
							  							  

			
cellColor('A'.$ini.':K'.($i+1), 'FFFFFF');


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

$objPHPExcel->getActiveSheet()->setSelectedCells('I30');
//LISTADO-PLANILLA_FORMATO_RESUMIDO
$NOMINA = str_replace(' ', '', $NOMINA);
$filename = "Listado_Planilla_Formato_Acumulado_Del_".fecha($desde).'_Hasta_'.fecha($hasta);

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
