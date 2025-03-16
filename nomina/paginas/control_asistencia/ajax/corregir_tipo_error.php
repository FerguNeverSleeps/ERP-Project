<?php
require_once "../config/db.php";

$tipo_error    = (isset($_POST['tipo_error'])) ? $_POST['tipo_error'] : '';
$archivo_reloj = (isset($_POST['archivo']))    ? $_POST['archivo']    : '';
$codigo        = (isset($_POST['codigo']))     ? $_POST['codigo']     : '';

echo "Tipo de error: $tipo_error / Archivo: $archivo_reloj / Codigo: $codigo \n";

if($tipo_error==='All') // Corregir automaticamente todos los registros con error
{
	echo "\nCorregir tipo_error = 'All' \n";

	if(!empty($_POST['chk_id'])) 
	   $array = $_POST['chk_id'];

	foreach($array as $codigo) 
    {
    	$tipo_error = (isset($_POST['tipo_error_'.$codigo])) ? $_POST['tipo_error_'.$codigo] : '';

    	if($tipo_error!=='')
    	{
    		$total_entradas = (isset($_POST['total_entr_'.$codigo])) ? $_POST['total_entr_'.$codigo] : '';
    		$total_salidas  = (isset($_POST['total_sali_'.$codigo])) ? $_POST['total_sali_'.$codigo] : '';
    		corregir_error($tipo_error, $archivo_reloj, $codigo, $total_entradas, $total_salidas);
    	}
    }
}
else
{
	echo "\nCorregir tipo_error = '{$tipo_error}' \n";

	if(!empty($_POST['chk_id'])) 
		$array = $_POST['chk_id'];

	if(!empty($codigo))
		$array = array($codigo);	

    foreach($array as $codigo) 
    {
    	$total_entradas = (isset($_POST['total_entr'])) ? $_POST['total_entr'] : '';
    	$total_salidas  = (isset($_POST['total_sali'])) ? $_POST['total_sali'] : '';
    	corregir_error($tipo_error, $archivo_reloj, $codigo, $total_entradas, $total_salidas);                                 
    }	
}
?>

<?php

function corregir_error($tipo_error, $archivo_reloj, $codigo, $total_entradas, $total_salidas)
{
	global $conexion;

	echo "\nFuncion corregir_error() => Tipo de error: $tipo_error \n";

    // Buscar el registro con dicho codigo en el archivo de reloj
	$res  = $conexion->createQueryBuilder()
	                 ->select('codigo', 'ficha', 'fecha_hora', 'tipo_movimiento', 'dispositivo')
	                 ->from('caa_archivos_datos')
	                 ->where('archivo_reloj=:archivo AND codigo=:codigo') 
	                 ->setParameter('archivo', $archivo_reloj)
	                 ->setParameter('codigo',  $codigo)
	                 ->execute();

	if($tipo_error==='0') // Agregar Tipo de Movimiento
	{
		while( $fila = $res->fetch() )
		{
			echo "\nFicha: " . $fila['ficha'] . " / Fecha y Hora: " . $fila['fecha_hora'] . "\n";

			$ficha      = $fila['ficha'];
			$fecha_hora = $fila['fecha_hora'];
			//$tipo_movimiento = NULL;
			$tipo_movimiento = (isset($fila['tipo_movimiento']) && !empty($fila['tipo_movimiento'])) ? $fila['tipo_movimiento'] : NULL;
			$tipo_original   = $tipo_movimiento;

			$fecha   = date("Y-m-d", strtotime($fecha_hora));
			$hora    = date("H:i:s", strtotime($fecha_hora));
			$dia_sem = 'dia'. date("N", strtotime($fecha_hora));

			// Consultar el turno del trabajador para esa fecha
			$sql2     = "SELECT turno_id FROM nomcalendarios_personal WHERE ficha='{$ficha}' AND fecha='{$fecha}'";
			$turno_id = $conexion->query($sql2)->fetchColumn(); 

			$sql2 = "SELECT hora_desde, hora_hasta, entrada_desde, entrada_hasta, salida_desde, salida_hasta 
					 FROM   nomturnos_horarios 
					 WHERE  turno_id='{$turno_id}' AND {$dia_sem}=1 AND dialibre=0";

			$res2 = $conexion->query($sql2);

			while($fila2 = $res2->fetch())
			{
				$entrada_desde = $fila2['entrada_desde'];
				$entrada_hasta = $fila2['entrada_hasta'];
				$salida_desde  = $fila2['salida_desde'];
				$salida_hasta  = $fila2['salida_hasta'];

				echo "Entrada: $entrada_desde - $entrada_hasta \n Salida: $salida_desde - $salida_hasta";

				if($hora>=$entrada_desde && $hora<=$entrada_hasta) 
				{
					$tipo_movimiento = 'Entrada';
					break;
				}
				if($hora>=$salida_desde && $hora<=$salida_hasta)
				{
					$tipo_movimiento = 'Salida';
					break;
				}
			}

			echo "\nTipo Original = ".$tipo_original." / Tipo de Movimiento: ".$tipo_movimiento."\n";

			if(!empty($tipo_movimiento) && $tipo_original!=$tipo_movimiento)
			{
				$conexion->update('caa_archivos_datos', array('tipo_movimiento' => $tipo_movimiento,
															  'corregido' => 1),
														array('codigo' => $codigo) );

				echo "Tipo de Movimiento: $tipo_movimiento \n";
			}
		}                                  
	}
	else if($tipo_error==='1') // Incluir salida de una entrada
	{
		// Sucede cuando hay entradas sin salida

		while( $fila = $res->fetch() )
		{
			echo "\nFicha: " . $fila['ficha'] . " / Fecha y Hora: " . $fila['fecha_hora'] . "\n";

			$ficha      = $fila['ficha'];
			$fecha_hora = $fila['fecha_hora'];

			$fecha_completa =  DateTime::createFromFormat('Y-m-d H:i:s', $fecha_hora); // Fecha completa
			$fecha   = date("Y-m-d", strtotime($fecha_hora));
			$hora    = date("H:i:s", strtotime($fecha_hora));
			$dia_sem = 'dia'. date("N", strtotime($fecha_hora));

			// Consultar el turno del trabajador para esa fecha
			$sql2     = "SELECT turno_id FROM nomcalendarios_personal WHERE ficha='{$ficha}' AND fecha='{$fecha}'";
			$turno_id = $conexion->query($sql2)->fetchColumn(); 

			echo "Turno: ".$turno_id."\n";

			$sql2 = "SELECT hora_desde, hora_hasta, entrada_desde, entrada_hasta, salida_desde, salida_hasta 
					 FROM   nomturnos_horarios 
					 WHERE  turno_id='{$turno_id}' AND {$dia_sem}=1 AND dialibre=0";

			$res2 = $conexion->query($sql2);

			$salida_mayor = '';

			while($fila2 = $res2->fetch())
			{
				$entrada_desde = DateTime::createFromFormat('Y-m-d H:i:s', $fecha .' '. $fila2['entrada_desde']);
				$entrada_hasta = DateTime::createFromFormat('Y-m-d H:i:s', $fecha .' '. $fila2['entrada_hasta']);

				$salida = DateTime::createFromFormat('Y-m-d H:i:s', $fecha .' '. $fila2['hora_hasta']);

				if($entrada_hasta < $entrada_desde)
				{
					$entrada_hasta = $entrada_hasta->modify('+1 day');
				}

				if($salida < $entrada_hasta)
				{
					$salida = $salida->modify('+1 day');
				}

				if($salida_mayor==='' || $salida_mayor<$salida)
				{
					$salida_mayor=$salida;
				}

				echo "Entrada: " .$entrada_desde->format('d-m-Y H:i:s'). " - ".$entrada_hasta->format('d-m-Y H:i:s')." => Salida: ".$salida->format('d-m-Y H:i:s')."\n";

				if($fecha_completa>=$entrada_desde && $fecha_completa<=$entrada_hasta && $total_salidas>0)
				{
					echo "\nProcedemos a agregar la salida: " . $salida->format('d-m-Y H:i:s');

					$conexion->insert('caa_archivos_datos', array('ficha'           => $ficha, 
																  'fecha_hora'      => $salida->format('Y-m-d H:i:s'), 
																  'tipo_movimiento' => 'Salida', 
																  'dispositivo'     => NULL,
																  'archivo_reloj'   => $archivo_reloj,
																  'corregido'       => 1
																 ));
				}
			}

			echo "\nSalida mayor: ".$salida_mayor->format('d-m-Y H:i:s')."\n";

			if($total_salidas==0)
			{
					$conexion->insert('caa_archivos_datos', array('ficha'           => $ficha, 
																  'fecha_hora'      => $salida_mayor->format('Y-m-d H:i:s'), 
																  'tipo_movimiento' => 'Salida', 
																  'dispositivo'     => NULL,
																  'archivo_reloj'   => $archivo_reloj,
																  'corregido'       => 1
																 ));				
			}
		}                                  
	}
	else if($tipo_error==='2' || $tipo_error==='6') // Forzar al registro a convertirse en una salida
	{
		// Sucede cuando hay entradas dobles. 
		// Solución: forzar al registro a convertirse en una salida
		while( $fila = $res->fetch() )
		{
			$tipo_movimiento = 'Salida';
			$corregido = ($tipo_error==='2') ? 1 : 0;

			$conexion->update('caa_archivos_datos', array('tipo_movimiento' => $tipo_movimiento,
														  'corregido'       => $corregido),
													array('codigo' => $codigo) );

			echo "Tipo de Movimiento: $tipo_movimiento \n";	
		}                                  
	}
	else if($tipo_error==='3') // Incluir entrada
	{
		// Sucede cuando hay salidas sin entradas
		while( $fila = $res->fetch() )
		{
			echo "\nFicha: " . $fila['ficha'] . " / Fecha y Hora: " . $fila['fecha_hora'] . "\n";

			$ficha      = $fila['ficha'];
			$fecha_hora = $fila['fecha_hora'];

			$fecha_completa =  DateTime::createFromFormat('Y-m-d H:i:s', $fecha_hora); // Fecha completa
			$fecha   = date("Y-m-d", strtotime($fecha_hora));
			$hora    = date("H:i:s", strtotime($fecha_hora));
			$dia_sem = 'dia'. date("N", strtotime($fecha_hora));

			// Consultar el turno del trabajador para esa fecha
			$sql2     = "SELECT turno_id FROM nomcalendarios_personal WHERE ficha='{$ficha}' AND fecha='{$fecha}'";
			$turno_id = $conexion->query($sql2)->fetchColumn(); 

			echo "Turno: ".$turno_id."\n";

			$sql2 = "SELECT hora_desde, hora_hasta, entrada_desde, entrada_hasta, salida_desde, salida_hasta 
					 FROM   nomturnos_horarios 
					 WHERE  turno_id='{$turno_id}' AND {$dia_sem}=1 AND dialibre=0";

			$res2 = $conexion->query($sql2);

			$entrada_menor = '';

			while($fila2 = $res2->fetch())
			{
				$entrada_desde = DateTime::createFromFormat('Y-m-d H:i:s', $fecha .' '. $fila2['entrada_desde']);
				$entrada_hasta = DateTime::createFromFormat('Y-m-d H:i:s', $fecha .' '. $fila2['entrada_hasta']);
				$salida_desde  = DateTime::createFromFormat('Y-m-d H:i:s', $fecha .' '. $fila2['salida_desde']);
				$salida_hasta  = DateTime::createFromFormat('Y-m-d H:i:s', $fecha .' '. $fila2['salida_hasta']);

				$entrada = DateTime::createFromFormat('Y-m-d H:i:s', $fecha .' '. $fila2['hora_desde']);

				if($entrada_menor==='' || $entrada_menor>$entrada)
				{
					$entrada_menor=$entrada;
				}

				if($entrada_hasta < $entrada_desde)
				{
					$entrada_hasta = $entrada_hasta->modify('+1 day');
				}

				if($salida_desde < $entrada_hasta)
				{
					$salida_desde = $salida_desde->modify('+1 day');
					$salida_hasta = $salida_hasta->modify('+1 day'); 
				}

				echo "\nEntrada: " .$entrada_desde->format('d-m-Y H:i:s'). " - ".$entrada_hasta->format('d-m-Y H:i:s').
				     "\nSalida: ".$salida_desde->format('d-m-Y H:i:s'). " - ".$salida_hasta->format('d-m-Y H:i:s'). "\n";

				echo "\nTotal entradas: ".$total_entradas." - Total salidas: ".$total_salidas."\n";

				if($fecha_completa>=$salida_desde && $fecha_completa<=$salida_hasta && ($total_entradas>0 || $total_salidas>1) )
				{
					echo "\nProcedemos a agregar la entrada: " . $entrada->format('d-m-Y H:i:s');

					$conexion->insert('caa_archivos_datos', array('ficha'           => $ficha, 
																  'fecha_hora'      => $entrada->format('Y-m-d H:i:s'), 
																  'tipo_movimiento' => 'Entrada', 
																  'dispositivo'     => NULL,
																  'archivo_reloj'   => $archivo_reloj,
																  'corregido'       => 1
																 ));
				}
			}

			echo "\nEntrada menor: ".$entrada_menor->format('d-m-Y H:i:s')."\n";

			if($total_entradas==0 && $total_salidas==1)
			{
				$conexion->insert('caa_archivos_datos', array('ficha'           => $ficha, 
															  'fecha_hora'      => $entrada_menor->format('Y-m-d H:i:s'), 
															  'tipo_movimiento' => 'Entrada', 
															  'dispositivo'     => NULL,
															  'archivo_reloj'   => $archivo_reloj,
															  'corregido'       => 1
															 ));				
			}
		}                                  
	}
	else if($tipo_error==='4' || $tipo_error==='5') // Forzar al registro a convertirse en una entrada
	{
		// Sucede cuando hay salidas dobles. 
		while( $fila = $res->fetch() )
		{
			$tipo_movimiento = 'Entrada';
			$corregido = ($tipo_error==='4') ? 1 : 0;

			$conexion->update('caa_archivos_datos', array('tipo_movimiento' => $tipo_movimiento,
														  'corregido'       => $corregido),
													array('codigo' => $codigo) );

			echo "Tipo de Movimiento: $tipo_movimiento \n";	
		}                                  
	}
	else if($tipo_error==='7') // Eliminar el movimiento
	{
		// Puede existir registros duplicados con diferencias de pocos minutos
		while( $fila = $res->fetch() )
		{
			if($conexion->delete('caa_archivos_datos', array('codigo' => $codigo)))
			{
				echo "\n Movimiento $codigo eliminado";	
			}
			else
			{
				echo "\nNo se elimino";
			}
		} 		
	}
}
?>