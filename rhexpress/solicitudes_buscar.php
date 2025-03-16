
<?php
session_start();
header("Content-Type: text/html;charset=utf-8");
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Reporte_solicitudes.xls"');
header('Cache-Control: max-age=0');
//error_reporting(0);
//ini_set('display_errors', FALSE);
//ini_set('display_startup_errors', FALSE);
date_default_timezone_set('America/Panama');
require_once "config/rhexpress_config.php";

// require("../nomina/procesos/PHPMailer_5.2.4/class.phpmailer.php");
// include("../nomina/procesos/PHPMailer_5.2.4/class.smtp.php");
// comentario

$conexion =  mysqli_connect( DB_HOST, DB_USUARIO, DB_CLAVE, $_SESSION['bd'] ) or die( 'No Hay Conexión con el Servidor de Mysql' );
        mysqli_query($conexion, 'SET CHARACTER SET utf8');
		
if (PHP_SAPI == 'cli')
die('Este ejemplo solo se debe ejecutar desde un navegador Web');

include('../lib/common.php');
include('../nomina/paginas/lib/php_excel.php');


	$fecha_ini=$_POST['fecha_inicio'];
	$fecha_fin=$_POST['fecha_fin'];
	$dep      =$_POST['select_departamento'];
	
	
	require_once '../nomina/paginas/phpexcel/Classes/PHPExcel.php';

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
	$acumula_solicitudes_compras=0;
	$meses = array('Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre');	
	$empresa = $fila['empresa'];
	$objPHPExcel->getActiveSheet()
				->setCellValue('A2',strtoupper($empresa))
				->setCellValue('A3', 'Reporte general de solicitudes')
				->setCellValue('A4', 'DEL '. $fecha_ini .' AL '. $fecha_fin  );
				
	$objPHPExcel->getActiveSheet()->getStyle('A2:A4')->getFont()->setSize(14);
	$objPHPExcel->getActiveSheet()->getStyle('A7')->getFont()->setSize(7);
	$objPHPExcel->getActiveSheet()->getStyle('A2:A4')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('A2:A4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->mergeCells('A2:H2');
	$objPHPExcel->getActiveSheet()->mergeCells('A3:H3');
	$objPHPExcel->getActiveSheet()->mergeCells('A4:H4');
	$objPHPExcel->getActiveSheet()->mergeCells('A8:C8');
	$objPHPExcel->getActiveSheet()->getStyle('A2:C8')->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('C2:H5')->applyFromArray(allBordersThin());
	$objPHPExcel->getActiveSheet()->getStyle('C2:H5')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	$objPHPExcel->getActiveSheet()
				->setCellValue('A6', 'Tipos de Solicitudes')
				->setCellValue('A7', '#')
				->setCellValue('B7', 'Nombre')
				->setCellValue('C7', 'Ficha')
				->setCellValue('D7', 'Cargo')
				->setCellValue('E7', 'Solicitud de permisos')
				->setCellValue('F7', 'Solicitud de Vacaciones')
				->setCellValue('G7', 'Solicitud de Reclamos de pago')
				->setCellValue('H7', 'Solicitud de Actualización anual')     
				->setCellValue('I7', 'Solicitud de Préstamos y créditos')
				->setCellValue('J7', 'Total');
				
	cellColor('E7:J7', '4b8df8');	
	$objPHPExcel->getActiveSheet()->getStyle('A8:D8')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('A6:J7')->getFont()->setName('Calibri');
	$objPHPExcel->getActiveSheet()->getStyle('A6:J7')->getFont()->setSize(12);
	$objPHPExcel->getActiveSheet()->getStyle('A6:J7')->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getStyle('A6:J7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->mergeCells('A6:J6');
	$objPHPExcel->getActiveSheet()->getStyle('A6:J7')->applyFromArray(allBordersThin()); 

	$objPHPExcel->getActiveSheet()->freezePane('E8'); // Inmovilizar Paneles
	if ($dep!=0) 
	{
		$i=9;
		$sql    ="SELECT descrip FROM nomnivel1 WHERE codorg='1'";
		$result =$conexion->query($sql);
		$nombre_departamento  = mysqli_fetch_array($result);
				
		$sql2="SELECT x.cedula as cedula, 
				x.ficha, 
				x.apenom,
				x.codnivel1, 
				v.id_tipo_caso,
				tv.des_car as cargo 
				FROM nompersonal as x 
				LEFT JOIN solicitudes_casos as v ON x.cedula=v.cedula 
				LEFT JOIN nomcargos as tv ON x.codcargo=tv.cod_car
				WHERE x.estado <> 'egresado' 
				AND x.codnivel1='$dep'
				GROUP BY x.cedula";
				$res2=$conexion->query($sql2);
				$acumulamelas=0;
				$indice=1;
				while($row2=$res2->fetch_array())
				{	
					$cuentamelas=0;
					$cedula = $row2['cedula'];			
					$ficha = $row2['ficha'];			
					$cargo = utf8_encode($row2['cargo']);
					$trabajador= utf8_encode($row2['apenom']);					
					$objPHPExcel->getActiveSheet()->setCellValue('A'.'8',$nombre_departamento['0']);
					$objPHPExcel->getActiveSheet()->getStyle('A'.'8')->getFont()->setBold(true);
					$objPHPExcel->getActiveSheet()->mergeCells('A8:B8');
					$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':J'.$i)->applyFromArray(allBordersThin());
					$objPHPExcel->getActiveSheet()->getStyle('A'.'8')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':J'.$i)->getFont()->setSize(13);
					$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
					$objPHPExcel->getActiveSheet()->getStyle('B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
					$objPHPExcel->getActiveSheet()->getStyle('C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
					$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, $indice);
					$objPHPExcel->getActiveSheet()->setCellValue('B'.$i, $trabajador);
					$objPHPExcel->getActiveSheet()->setCellValue('C'.$i, $ficha);
					$objPHPExcel->getActiveSheet()->setCellValue('D'.$i, $cargo);
					// //PERMISOS
					$sql9="SELECT COUNT(id_solicitudes_casos)as numero FROM
					solicitudes_casos WHERE cedula ='$cedula' and id_tipo_caso='1' and fecha_registro between '$fecha_ini' and '$fecha_fin' and id_solicitudes_casos_status IN ('1','2')";
					$res212 = $conexion->query($sql9);
					$rowxj = mysqli_fetch_array($res212);
					if ($rowxj['0']>0) {				
						$objPHPExcel->getActiveSheet()->getStyle('E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$objPHPExcel->getActiveSheet()->setCellValue('E'.$i, $rowxj['0']);
						$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':J'.$i)->applyFromArray(allBordersThin());
					}
					// //VACACIONES
					$sql3="SELECT COUNT(id_solicitudes_casos)as numero FROM
					solicitudes_casos WHERE cedula ='$cedula' and id_tipo_caso='2' and fecha_registro between '$fecha_ini' and '$fecha_fin' and id_solicitudes_casos_status IN ('1','2')";
					$res3 = $conexion->query($sql3);
					$rowx = mysqli_fetch_array($res3);
					if ($rowx['0']>0) {						
						$objPHPExcel->getActiveSheet()->getStyle('F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$objPHPExcel->getActiveSheet()->setCellValue('F'.$i, $rowx['0']);
						$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':J'.$i)->applyFromArray(allBordersThin());
					}
					
				
					$sql4="SELECT COUNT(id_solicitudes_casos)as numero FROM
					solicitudes_casos WHERE cedula ='$cedula' and id_tipo_caso='5' and fecha_registro between '$fecha_ini' and '$fecha_fin' and id_solicitudes_casos_status IN ('1','2')";
					$res4 = $conexion->query($sql4);
					$row4 = mysqli_fetch_array($res4);
					if ($row4['0']>0) {					
						$objPHPExcel->getActiveSheet()->getStyle('G'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$objPHPExcel->getActiveSheet()->setCellValue('G'.$i, $row4['0']);
						$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':J'.$i)->applyFromArray(allBordersThin());
					}
					//RECLAMO DE PAGOS
					$sql1="SELECT COUNT(id_solicitudes_casos)as numero FROM
					solicitudes_casos WHERE cedula ='$cedula' and id_tipo_caso='7' and fecha_registro between '$fecha_ini' and '$fecha_fin' and id_solicitudes_casos_status IN ('1','2')";
					$res1 = $conexion->query($sql1);
					$rowh = mysqli_fetch_array($res1);
					if ($rowh['0']>0) {					
						$objPHPExcel->getActiveSheet()->getStyle('H'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$objPHPExcel->getActiveSheet()->setCellValue('H'.$i, $rowh['0']);
						$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':J'.$i)->applyFromArray(allBordersThin());
					}
					//ACTUALIZACION ANUAL-BIENES
					$sql5="SELECT COUNT(id_solicitudes_casos)as numero FROM
					solicitudes_casos WHERE cedula ='$cedula' and id_tipo_caso='6' and fecha_registro between '$fecha_ini' and '$fecha_fin' and id_solicitudes_casos_status IN ('1','2')";
					$res5 = $conexion->query($sql5);
					$rowhh = mysqli_fetch_array($res5);
					if ($rowhh['0']>0) {					
						$objPHPExcel->getActiveSheet()->getStyle('I'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$objPHPExcel->getActiveSheet()->setCellValue('I'.$i, $rowhh['0']);
						$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':J'.$i)->applyFromArray(allBordersThin());
					}
					//COMPRAS PRESTAMOS	
					
									
					$cuentamelas=$rowhh['0']+$rowh['0']+$row4['0']+ $rowx['0']+$rowxj['0'];
					$objPHPExcel->getActiveSheet()->getStyle('J'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->setCellValue('J'.$i, ($cuentamelas = $cuentamelas !='' ?  $cuentamelas :''	));
					//$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':H'.$i)->applyFromArray(allBordersThin());
					$acumulamelas=$cuentamelas+$acumulamelas;
					$acumula_solicitudes_compras=$rowhh['0']+$acumula_solicitudes_compras;
					$acumula_solicitudes_vacaciones=$rowxj['0']+$acumula_solicitudes_vacaciones;
					$acumula_solicitudes_permisos=$rowx['0']+$acumula_solicitudes_permisos;
					$acumula_solicitudes_actualizacion=$rowh['0']+$acumula_solicitudes_actualizacion;
					$acumula_solicitudes_reclamo=$row4['0']+$acumula_solicitudes_reclamo;
					$i++;
					$indice++;
				}
				// echo $cuentamelas;exit;
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getFont()->setBold(true);
				//$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':B'.$i);
				//$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':B'.$i)->applyFromArray(allBordersThin());
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				//$objPHPExcel->getActiveSheet()->setCellValue('A'.$i,$nombre_departamento['0']);
				$objPHPExcel->getActiveSheet()->setCellValue('J'.$i, ($acumulamelas = $acumulamelas !='' ?  $acumulamelas :''));
				$objPHPExcel->getActiveSheet()->getStyle('J'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				cellColor('A'.$i.':J'.$i, '4b8df8');	
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, 'Total:');
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getFont()->setBold(true);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':J'.$i)->applyFromArray(allBordersThin());
				
				$objPHPExcel->getActiveSheet()->setCellValue('I'.$i, $acumula_solicitudes_compras);		
				$objPHPExcel->getActiveSheet()->setCellValue('E'.$i, $acumula_solicitudes_vacaciones);		
				$objPHPExcel->getActiveSheet()->setCellValue('F'.$i, $acumula_solicitudes_permisos);		
				$objPHPExcel->getActiveSheet()->setCellValue('H'.$i, $acumula_solicitudes_actualizacion);		
				$objPHPExcel->getActiveSheet()->setCellValue('G'.$i, $acumula_solicitudes_reclamo);		
				$objPHPExcel->getActiveSheet()->getStyle('I'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('H'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('G'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':D'.$i);
				
				$i++;
		
	}
	else
	{
	$sql = "SELECT nn.codorg, nn.descrip
	FROM   nomnivel1 nn
	WHERE  nn.descrip NOT IN('Eventual', 'O&M')
	ORDER BY nn.codorg";
	$res=$conexion->query($sql);
	
	$i=8; $nivel=1;
	
	// $ini=8; $enc=false;
	$indice=1;
	while($row=$res->fetch_array())
	{
		
		$codorg = $row['codorg'];		
		$sql2="SELECT x.cedula as cedula, 
				x.ficha, 
				x.apenom, 
				x.codnivel1, 
				v.id_tipo_caso, 
				tv.des_car as cargo 
				FROM nompersonal as x 
				LEFT JOIN solicitudes_casos as v ON x.cedula=v.cedula 
				LEFT JOIN nomcargos as tv ON x.codcargo=tv.cod_car
				WHERE x.estado <> 'egresado' 
				AND x.codnivel1='$codorg'
				GROUP BY x.cedula";		

		$res2=$conexion->query($sql2);
		$acumulamelas=0;
		$subtotalvacaciones=0;
		$subtotalactualizacion=0;
		$subtotalreclamo=0;
		$subtotalpermisos=0;
		$subtotalcompras=0;
		$total_final=0;
		$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':D'.$i);
		$objPHPExcel->getActiveSheet()->setCellValue('A'.$i,utf8_encode($row['descrip']));	
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':D'.$i)->applyFromArray(allBordersThin());
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		//$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':B'.$i);
		$i++;	

		while($row2=$res2->fetch_array())
		{				
			$cuentamelas=0;
			$cedula = $row2['cedula'];
			$trabajador=$row2['apenom'];
			$ficha=$row2['ficha'];
			$cargo = utf8_decode($row2['cargo']);
			$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':H'.$i)->getFont()->setSize(12);
			$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			$objPHPExcel->getActiveSheet()->getStyle('B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			$objPHPExcel->getActiveSheet()->getStyle('C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			$objPHPExcel->getActiveSheet()->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, $indice);
			$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':J'.$i)->applyFromArray(allBordersThin());
			$objPHPExcel->getActiveSheet()->setCellValue('B'.$i, $trabajador);
			$objPHPExcel->getActiveSheet()->setCellValue('C'.$i, $ficha);
			$objPHPExcel->getActiveSheet()->setCellValue('D'.$i, $cargo);
			// $objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			// $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':I'.$i)->applyFromArray(allBordersThin());
			// //VACACIONES
			$sql3="SELECT COUNT(id_solicitudes_casos)as numero FROM
			solicitudes_casos WHERE cedula ='$cedula' and id_tipo_caso='2' and fecha_registro between '$fecha_ini' and '$fecha_fin' and id_solicitudes_casos_status IN ('1','2')";
			$res3 = $conexion->query($sql3);
			$rowx = mysqli_fetch_array($res3);
			if ($rowx['0']>0) {
				$objPHPExcel->getActiveSheet()->getStyle('F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->setCellValue('F'.$i, $rowx['0']);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':J'.$i)->applyFromArray(allBordersThin());
			}
			// //PERMISOS
			$sql9="SELECT COUNT(id_solicitudes_casos)as numero FROM
			solicitudes_casos WHERE cedula ='$cedula' and id_tipo_caso='1' and fecha_registro between '$fecha_ini' and '$fecha_fin' and id_solicitudes_casos_status IN ('1','2')";
			$res212 = $conexion->query($sql9);
			$rowxj = mysqli_fetch_array($res212);
			if ($rowxj['0']>0) {
				$objPHPExcel->getActiveSheet()->getStyle('E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->setCellValue('E'.$i, $rowxj['0']);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':J'.$i)->applyFromArray(allBordersThin());
			}
			// //VACACIONES
			$sql4="SELECT COUNT(id_solicitudes_casos)as numero FROM
			solicitudes_casos WHERE cedula ='$cedula' and id_tipo_caso='5' and fecha_registro between '$fecha_ini' and '$fecha_fin' and id_solicitudes_casos_status IN ('1','2')";
			$res4 = $conexion->query($sql4);
			$row4 = mysqli_fetch_array($res4);
			if ($row4['0']>0) {
				$objPHPExcel->getActiveSheet()->getStyle('G'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->setCellValue('G'.$i, $row4['0']);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':J'.$i)->applyFromArray(allBordersThin());
			}
			//RECLAMO DE PAGOS
			$sql1="SELECT COUNT(id_solicitudes_casos)as numero FROM
			solicitudes_casos WHERE cedula ='$cedula' and id_tipo_caso='7' and fecha_registro between '$fecha_ini' and '$fecha_fin' and id_solicitudes_casos_status IN ('1','2')";
			$res1 = $conexion->query($sql1);
			$rowh = mysqli_fetch_array($res1);
			if ($rowh['0']>0) {
				$objPHPExcel->getActiveSheet()->getStyle('H'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->setCellValue('H'.$i, $rowh['0']);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':J'.$i)->applyFromArray(allBordersThin());
			}
			//ACTUALIZACION ANUAL-BIENES
			$sql5="SELECT COUNT(id_solicitudes_casos)as numero FROM
			solicitudes_casos WHERE cedula ='$cedula' and id_tipo_caso='6' and fecha_registro between '$fecha_ini' and '$fecha_fin' and id_solicitudes_casos_status IN ('1','2')";
			$res5 = $conexion->query($sql5);
			$rowhh = mysqli_fetch_array($res5);
			if ($rowhh['0']>0) {
				$objPHPExcel->getActiveSheet()->getStyle('I'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->setCellValue('I'.$i, $rowhh['0']);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':J'.$i)->applyFromArray(allBordersThin());
			}	
				
			$cuentamelas=$rowhh['0']+$rowh['0']+$row4['0']+ $rowx['0']+$rowxj['0'];
			$objPHPExcel->getActiveSheet()->getStyle('J'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->setCellValue('J'.$i, ($cuentamelas = $cuentamelas !='' ?  $cuentamelas :''	));
			$acumulamelas=$cuentamelas+$acumulamelas;
			$acumula_solicitudes_compras=$rowhh['0']+$acumula_solicitudes_compras;
			$acumula_solicitudes_vacaciones=$rowx['0']+$acumula_solicitudes_vacaciones;
			$acumula_solicitudes_permisos=$rowxj['0']+$acumula_solicitudes_permisos;
			$acumula_solicitudes_actualizacion=$rowh['0']+$acumula_solicitudes_actualizacion;
			$acumula_solicitudes_reclamo=$row4['0']+$acumula_solicitudes_reclamo;
			$i++;
			$indice++;
			$subtotalvacaciones=$rowx['0']+$subtotalvacaciones;
			$subtotalactualizacion=$rowh['0']+$subtotalactualizacion;
			$subtotalreclamo=$row4['0']+$subtotalreclamo;
			$subtotalpermisos=$rowxj['0']+$subtotalpermisos;
			$subtotalcompras=$rowhh['0']+$subtotalcompras;
		}
		//echo $cuentamelas;
		//echo $acumulamelas; exit;
		// $i++;
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getFont()->setBold(true);		
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->setCellValue('J'.$i, ($acumulamelas = $acumulamelas !='' ?  $acumulamelas :''));
		$objPHPExcel->getActiveSheet()->getStyle('J'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		cellColor('A'.$i.':J'.$i, '4b8df8');	
		$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, 'Sub Total:');
		$objPHPExcel->getActiveSheet()->setCellValue('I'.$i, $subtotalcompras=$subtotalcompras <> 0 ? $subtotalcompras :'' );
		$objPHPExcel->getActiveSheet()->getStyle('I'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->setCellValue('G'.$i, $subtotalreclamo=$subtotalreclamo <> 0 ? $subtotalreclamo :'' );
		$objPHPExcel->getActiveSheet()->getStyle('G'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->setCellValue('E'.$i, $subtotalpermisos=$subtotalpermisos <> 0 ? $subtotalpermisos :'' );
		$objPHPExcel->getActiveSheet()->getStyle('E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->setCellValue('F'.$i, $subtotalvacaciones=$subtotalvacaciones <> 0 ? $subtotalvacaciones :'' );
		$objPHPExcel->getActiveSheet()->getStyle('F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->setCellValue('H'.$i, $subtotalactualizacion=$subtotalactualizacion <> 0 ? $subtotalactualizacion :'' );
		$objPHPExcel->getActiveSheet()->getStyle('H'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':D'.$i);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':J'.$i)->applyFromArray(allBordersThin());
		
		//$letra='C';
		//$trabajadores[] = array('nombre'=>$trabajador, 'primer_nombre'=> $primer_nombre, 'apellido'=>$apellido, 'neto'=> $neto, 'neto2'=>$neto2);
		//$ini=$i;
		$i++;	
		
	}
	
	$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getFont()->setBold(true);	
	$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, 'Total:');
	$total_final=$acumula_solicitudes_compras+$acumula_solicitudes_vacaciones+$acumula_solicitudes_permisos+$acumula_solicitudes_actualizacion+$acumula_solicitudes_reclamo;
	$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':D'.$i);
	$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':J'.$i)->applyFromArray(allBordersThin());
	$objPHPExcel->getActiveSheet()->setCellValue('I'.$i, $acumula_solicitudes_compras);		
	$objPHPExcel->getActiveSheet()->setCellValue('F'.$i, $acumula_solicitudes_vacaciones);		
	$objPHPExcel->getActiveSheet()->setCellValue('E'.$i, $acumula_solicitudes_permisos);		
	$objPHPExcel->getActiveSheet()->setCellValue('H'.$i, $acumula_solicitudes_actualizacion);		
	$objPHPExcel->getActiveSheet()->setCellValue('G'.$i, $acumula_solicitudes_reclamo);	
	$objPHPExcel->getActiveSheet()->setCellValue('J'.$i, $total_final);	
	$objPHPExcel->getActiveSheet()->getStyle('I'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('H'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('G'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('J'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		
}

//==================================================================================================
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(6);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(40);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(34);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);


$objPHPExcel->getActiveSheet()->setTitle($dia_fin.' '.$mes_letras);


$NOMINA = str_replace(' ', '', $NOMINA);
$filename = "Reporte_general_solicitudes".fecha($desde).'_Hasta_'.fecha($hasta);

ob_end_clean();

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
exit;
