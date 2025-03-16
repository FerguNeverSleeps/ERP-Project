<?php
date_default_timezone_set("America/Caracas");
session_start();
ob_start();

require_once '../../generalp.config.inc.php';
require_once '../lib/common.php';
error_reporting(E_ALL ^ E_DEPRECATED);
$conexion = conexion();
$jsondata = array();

$sql = "SELECT codigo, descripcion, frecuencia, inicio, turnotipo_id 
		FROM   nomturnos_rotacion";      

$res = query($sql, $conexion); 

$success = true; // Ésta variable permite saber si ocurrieron errores al rotar los horarios
				 // Si $success==true todo se ejecutó correctamente

// $jsondata["rotacion"]["codigo"] = array();

$jsondata["empleado"] = array();

while( $fila = fetch_array($res) ) // mysqli_fetch_array($res)
{
	$codigo_rotacion = $fila['codigo'];
	$descri_rotacion = $fila['descripcion'];
	$frecuencia      = $fila['frecuencia'];
	$inicio          = $fila['inicio'];
	$turnotipo_id    = $fila['turnotipo_id'];

	if($frecuencia == 'Semanal')
	{
		$dia_semana   = date("N");     // 1 = Lunes / 7 = domingo
		$fecha_actual = date('Y-m-j'); // fecha actual

		// Comprobamos si el día actual concuerda con el día de inicio de la rotación
		if($dia_semana == $inicio)
		{
			// $jsondata["rotacion"]["codigo"][] = $codigo_rotacion;
			//=================================================================================
			// Cargamos el array fechas con las fechas correspondientes a esta semana de trabajo
			$fechas   = array();
			$fechas[] = array('fecha'=>$fecha_actual, 'dia_sem'=> $dia_semana);

			$i=0;
			while($i<6)
			{
				$nuevafecha = strtotime ( '+1 day' , strtotime (  $fechas[$i]['fecha']  ) ) ;

				$fechas[]  = array('fecha'   =>  date ( 'Y-m-j' , $nuevafecha ), 
				                   'dia_sem' =>  date ( 'N' , $nuevafecha ) );

				$i++;
			}
			// var_dump($fechas);
			//=================================================================================

			$sql2  = "SELECT turno_actual, turno_sucesor, 
       				         (SELECT descripcion FROM nomturnos WHERE turno_id=n.turno_actual)  as desc_turno_actual,
       				         (SELECT descripcion FROM nomturnos WHERE turno_id=n.turno_sucesor) as desc_turno_sucesor
					  FROM   nomturnos_rotacion_detalle n
					  WHERE  codigo_rotacion='{$codigo_rotacion}'";
			$res2 =  query($sql2, $conexion); 

			$fichas_actualizadas = array();

			while( $fila2 = fetch_array($res2) )
			{
				$turno_actual  = $fila2['turno_actual'];
				$turno_sucesor = $fila2['turno_sucesor'];
				$desc_turno_actual  = $fila2['desc_turno_actual'];
				$desc_turno_sucesor = $fila2['desc_turno_sucesor'];

				// echo "<br><br><b>Turno Actual: $turno_actual  /  Turno Sucesor: $turno_sucesor</b><br><br>";

				// Consultamos primero los días laborables correspondientes al turno actual
				$sql3 = "SELECT dia1, dia2, dia3, dia4, dia5, dia6, dia7, dialibre 
						 FROM   nomturnos_horarios
						 WHERE  turno_id={$turno_sucesor}";

				$res3 =  query($sql3, $conexion); 

				while($fila3 = fetch_array($res3) )
				{
					$dia_libre = $fila3['dialibre'];

					$lun = ($fila3['dia1']==1 && $dia_libre==1) ? 1 :  0; // 0=Laborable / 1=No Laborable
					$mar = ($fila3['dia2']==1 && $dia_libre==1) ? 1 :  0;
					$mie = ($fila3['dia3']==1 && $dia_libre==1) ? 1 :  0;
					$jue = ($fila3['dia4']==1 && $dia_libre==1) ? 1 :  0;
					$vie = ($fila3['dia5']==1 && $dia_libre==1) ? 1 :  0;
					$sab = ($fila3['dia6']==1 && $dia_libre==1) ? 1 :  0;
					$dom = ($fila3['dia7']==1 && $dia_libre==1) ? 1 :  0;
				}

				// echo "Lunes = ".$lun." / ";
				// echo "Martes = ".$mar." / ";
				// echo "Miercoles = ".$mie." / ";
				// echo "Jueves = ".$jue." / ";
				// echo "Viernes = ".$vie." / ";
				// echo "Sabado = ".$sab." / ";
				// echo "Domingo = ".$dom."<br>";


				// Actualizamos el turno de cada trabajador por el correspondiente turno sucesor
				// Para todos aquellos trabajadores que esten asignados actualmente a este turno
				// Y cuyo turno no ha sido actualizado en este proceso
				$excluir_fichas = implode("','", $fichas_actualizadas);

				$sql3 = "SELECT ficha, apenom FROM nompersonal WHERE turno_id=".$turno_actual." AND ficha NOT IN ('".$excluir_fichas."')";

				$res3 = query($sql3, $conexion); 

				while( $fila3 = fetch_array($res3) )
				{
					$nombre = $fila3['apenom'];
					$ficha  = $fila3['ficha'];
					$fichas_actualizadas[] = $ficha;

					$sql4  = "UPDATE nompersonal SET turno_id={$turno_sucesor} WHERE ficha='{$ficha}'";

					if ( query($sql4, $conexion) === TRUE) 
					{
					    	// echo "Registro actualizado correctamente \n";

							// Vamos a recorrer los días de la semana para actualizar el calendario del trabajador	
							$i=0;
							while($i<7)
							{
								if( $fechas[$i]['dia_sem'] == 1 )
									$dia_fiesta = $lun;	
								if( $fechas[$i]['dia_sem'] == 2 )
									$dia_fiesta = $mar;	
								if( $fechas[$i]['dia_sem'] == 3 )
									$dia_fiesta = $mie;	
								if( $fechas[$i]['dia_sem'] == 4 )
									$dia_fiesta = $jue;	
								if( $fechas[$i]['dia_sem'] == 5 )
									$dia_fiesta = $vie;	
								if( $fechas[$i]['dia_sem'] == 6 )
									$dia_fiesta = $sab;	
								if( $fechas[$i]['dia_sem'] == 7 )
									$dia_fiesta = $dom;	

								// Consulto la fecha para ver si ya esta registrada en el calendario personal del trabajador
								$sql4 = "SELECT dia_fiesta FROM nomcalendarios_personal WHERE  ficha='{$ficha}' AND fecha='".$fechas[$i]['fecha']."'";
								$res4 = query($sql4, $conexion);

								if( ( num_rows($res4) > 0) && ($fila4 = fetch_array($res4)  ) )
								{
									// Actualizo la fecha en el calendario personal
									$dia_fiesta = ( $fila4['dia_fiesta']==3 ) ? $fila4['dia_fiesta'] : $dia_fiesta;

									$sql4 = "UPDATE nomcalendarios_personal SET    
									                dia_fiesta='{$dia_fiesta}',
									                turno_id='{$turno_sucesor}'
											 WHERE  ficha='{$ficha}' AND fecha='".$fechas[$i]['fecha']."'";
									//echo "<br>$sql4<br>";
									query($sql4, $conexion);																			
								}
								else
								{
									// Si la fecha no existe la registro en el calendario personal
									$sql4 = "INSERT INTO `nomcalendarios_personal` 
									         (`cod_empresa`, `ficha`, `fecha`, `dia_fiesta`, `descripcion_dia_fiesta`, `turno_id`) 
									         VALUES 
									         (0, '{$ficha}', '".$fechas[$i]['fecha']."', '{$dia_fiesta}', '', '{$turno_sucesor}')";

									query($sql4, $conexion);
								}

								$i++;
							}

							$array_empleado = array('ficha'               => $ficha, 
													'nombre'              => $nombre,
								                    'turno_anterior'      => $turno_actual, 
								                    'turno_actual'        => $turno_sucesor, 
								                    'codigo_rotacion'     => $codigo_rotacion,
								                    'descripcion'         => utf8_encode($descri_rotacion),
								                    'desc_turno_anterior' => utf8_encode($desc_turno_actual),
								                    'desc_turno_actual'   => utf8_encode($desc_turno_sucesor) );

							$jsondata["empleado"][] = $array_empleado;
					} 
					else 
					{
					    $success = false;
					}
				}

				/*

				$sql4  = "UPDATE nompersonal 
				          SET    turno_id={$turno_sucesor}
				          WHERE  turno_id={$turno_actual}  AND ficha NOT IN ('".$excluir_fichas."')";

				query($sql4, $conexion);
				*/			
			}
		}
	}

}

if($success)
	$jsondata["success"]  = true; 
else
	$jsondata["success"]  = false; 

//$jsondata["zona"] = date_default_timezone_get(); 
header('Content-type: application/json; charset=utf-8');
echo json_encode($jsondata);

exit();
?>