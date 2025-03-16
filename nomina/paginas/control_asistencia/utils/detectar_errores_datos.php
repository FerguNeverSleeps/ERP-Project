<?php

function detectar_errores($archivo_reloj, $fila, &$aux_ficha, &$aux_fecha, &$cont_entradas, &$cont_salidas, $entradas_anterior, $salidas_anterior, &$hora_desde1_anterior, &$hora_hasta1_anterior, &$total_entradas_fuera, &$total_salidas_fuera, &$tipo_anterior, &$total_entradas, &$total_salidas)
{
	global $conexion;

	// Datos que necesito del array $fila
	$ficha           = $fila['ficha'];
	$fecha_hora      = $fila['fecha_hora'];
	$tipo_movimiento = $fila['tipo_movimiento'];
	$corregido       = $fila['corregido'];

	// Inicializo las variables
	$tipo_error  = $class_text = $msj_error = '';
	$class_error = ($corregido==1) ? 'info' : '';

	// Descomponer el valor de la variable $fecha_hora
	$fecha_completa =  DateTime::createFromFormat('Y-m-d H:i:s', $fecha_hora); // Fecha completa
	$fecha   =  date('Y-m-d', strtotime($fecha_hora));    // Obtengo la fecha del movimiento
	$hora    =  date('H:i:s', strtotime($fecha_hora));    // Obtengo la hora del movimiento
	$dia_sem = 'dia' . date("N", strtotime($fecha_hora)); // Obtengo el dia de la semana del movimiento (1=lun / 7=dom)

	if($aux_ficha!=$ficha)
	{
	  	$aux_ficha = $ficha;
	  	$aux_fecha = $tipo_anterior = '';
	  	$entradas_anterior = $salidas_anterior = array();
	  	$hora_desde1_anterior = $hora_hasta1_anterior = '';
	}

	if($aux_fecha != $fecha)
	{
		$aux_fecha     = $fecha;
		$cont_entradas = $cont_salidas = 0;
		$total_entradas_fuera=$total_salidas_fuera=0;
	}

	$rango_entradas = $rango_salidas = array();

	// En primer lugar averiguamos el turno del trabajador para la fecha del movimiento
	$turno_id = obtenerTurnoPorFecha($aux_ficha, $aux_fecha);

	// Ahora averiguamos el horario del turno para dicho día (hora desde - hora hasta)
	obtenerHorarioTurnoDia($turno_id, $dia_sem, $aux_fecha, $hora_desde1, $hora_hasta1, $rango_entradas, $rango_salidas, $hora_desde1_anterior, $hora_hasta1_anterior);

	//======================================================================================================================
	// Calculamos el total de entradas y salidas registradas en el día (según horario de trabajo)
	$total_entradas=$total_salidas=0;
	if($hora_desde1 instanceof DateTime)
	{
		// Consultar cantidad de entradas y salidas válidas en el rango permitido (hora_desde - hora_hasta)
		// Esto no significa que el movimiento actual este dentro del rango permitido
		$sql = "SELECT 
			       (SELECT COUNT(*) FROM caa_archivos_datos ca 
			        WHERE  ca.ficha='{$aux_ficha}' 
			        AND    DATE_FORMAT(ca.fecha_hora,'%Y-%m-%d %H:%i:%s') 
			               BETWEEN '".$hora_desde1->format('Y-m-d H:i:s')."' AND '".$hora_hasta1->format('Y-m-d H:i:s')."' 
			        AND    ca.tipo_movimiento='Entrada'
			        AND    ca.archivo_reloj='{$archivo_reloj}') as total_entradas,
			       (SELECT COUNT(*) FROM caa_archivos_datos ca 
			        WHERE  ca.ficha='{$aux_ficha}' 
			        AND    DATE_FORMAT(ca.fecha_hora,'%Y-%m-%d %H:%i:%s') 
			               BETWEEN '".$hora_desde1->format('Y-m-d H:i:s')."' AND '".$hora_hasta1->format('Y-m-d H:i:s')."'  
			        AND    ca.tipo_movimiento='Salida'
			        AND    ca.archivo_reloj='{$archivo_reloj}') as total_salidas";
		//echo "<br>".$sql."<br>";
		$row = $conexion->query($sql)->fetch();

		$total_entradas = $row['total_entradas']; // Total de entradas en este rango (el rango puede terminar el dia siguiente)
		$total_salidas  = $row['total_salidas'];  // Total de salidas  en este rango (el rango puede terminar el dia siguiente)

		// Ahora comprobamos si la fecha que estoy procesando se encuentra dentro de los rangos permitidos
		$entrada_invalida = $salida_invalida = false;
		if(! ($fecha_completa>=$hora_desde1 && $fecha_completa<=$hora_hasta1) )
		{
			// No está dentro de las entradas y salidas válidas, debe contarse aparte
			if($tipo_movimiento=='Entrada')
			{
				$total_entradas_fuera++; // Entradas fuera del rango válido (por fuera de los extremos de entrada/salida)
				$entrada_invalida = true;
			} 
			if($tipo_movimiento=='Salida')
			{
				$total_salidas_fuera++; // Salidas fuera del rango válido (por fuera de los extremos de entrada/salida)
				$salida_invalida = true;
			}
		}
	}

	// Esto es solo para horarios que terminan al dia siguiente:
	if($total_entradas==0 && $hora_desde1_anterior instanceof DateTime)
	{
		// Puede ser que la entrada se haya dado el dia anterior (por lo que en el día actual solo se encuentra la salida)
		for($i=0; $i < count($salidas_anterior); $i++)
		{
			$entrada_desde = $entradas_anterior[$i]['desde'];
			$entrada_hasta = $entradas_anterior[$i]['hasta'];
			$salida_desde  = $salidas_anterior[$i]['desde'];
			$salida_hasta  = $salidas_anterior[$i]['hasta'];

			if($fecha_completa >= $salida_desde   &&   $fecha_completa <= $salida_hasta)
			{
				// Consultar si existe una entrada para esta salida en el dia anterior
				$sql = "SELECT COUNT(*) FROM caa_archivos_datos ca 
			            WHERE  ca.ficha='{$aux_ficha}' 
			            AND    DATE_FORMAT(ca.fecha_hora,'%Y-%m-%d %H:%i:%s') 
			                   BETWEEN '".$entrada_desde->format('Y-m-d H:i:s')."' AND '".$entrada_hasta->format('Y-m-d H:i:s')."' 
			            AND    ca.tipo_movimiento='Entrada'
			            AND    ca.archivo_reloj='{$archivo_reloj}'";

			    $num = $conexion->query($sql)->fetchColumn();

			    if($num>0)	$total_entradas++;
			}				
		}
	}

	$total_entradas = $total_entradas + $total_entradas_fuera;
	$total_salidas  = $total_salidas  + $total_salidas_fuera;
	//======================================================================================================================

	// echo "<br>Ficha: $ficha / Fecha: ".$fecha_completa->format('d-m-Y H:i:s')."<br>"
	// 	."Total entradas: $total_entradas / Total salidas: $total_salidas <br>";
		
	if(!empty($tipo_movimiento))
	{
		$esUnaEntradaValida = verificarEntrada($fecha_completa, $rango_entradas, $class_text, $tipo_movimiento);
		$esUnaSalidaValida  = verificarSalida($fecha_completa, $rango_salidas, $salidas_anterior, $class_text, $tipo_movimiento);

		if($tipo_movimiento=='Entrada')
		{
		    $cont_entradas++;
			//echo "cont_entradas: $cont_entradas / cont_salidas: $cont_salidas <br>";
	    	if(!$esUnaEntradaValida)
	    	{
	    		$msj_error   = 'Revisar si el movimiento es realmente una entrada (No se encuentra dentro del rango válido para una entrada)';
	    	}
		    if($esUnaSalidaValida)
		    {
		   		if($tipo_anterior=='Entrada' || $tipo_anterior!='Salida' || ($total_entradas+$total_salidas==1))
		   		{
		   			$class_error='danger'; $tipo_error=2; $msj_error = 'Convertir en salida';
		   		}
		   		else
		   		{
		   			$msj_error = 'Decidir si el movimiento es una entrada o una salida, y registrar si es necesario su movimiento pareja (Podría ser una entrada pero el movimiento se encuentra en el rango válido para una salida)';
		   			$class_error='warning';
		   		}	
		    }
		    else if($total_salidas==0)
		    {
		        if($total_entradas==1) // Error de entrada sin salida
		        { 
		         	if($esUnaEntradaValida)
		         	{
		         		$class_error='danger'; $tipo_error=1; $msj_error = 'Incluir salida'; 
		         	}
		         	elseif ($esUnaSalidaValida) 
		         	{
		         		$class_error='danger'; $tipo_error=2; $msj_error = 'Convertir en salida'; 
		         	}
		         	else
		         	{
		         		$msj_error   = 'Decidir si el movimiento es una entrada o una salida, y registrar si es necesario su movimiento pareja (No se encuentra dentro del rango válido para una entrada/salida)';
				        $class_error = 'warning'; 			
		         	}
		        } 

		        if($total_entradas==2 && $cont_entradas==2) // Error de entrada doble
		        { 
		         	if($esUnaEntradaValida)
		         	{
		         		$msj_error   = 'Decidir si el movimiento es una entrada o una salida (Debería ser una salida pero el movimiento se encuentra en el rango válido para una entrada)';
				        $class_error = 'warning'; 	
		         	}
		         	else
		         	{
		          		$class_error='danger'; $tipo_error=2; $msj_error = 'Convertir en salida'; 
		          	}
		        }

		        if($total_entradas==3) // Error de entrada triple
		        { 
		          	if($cont_entradas==2)
		          	{
		          		if($esUnaSalidaValida){
		          			$class_error='danger'; $tipo_error=2; $msj_error = 'Convertir en salida'; 
		          		}
		          		else if($esUnaEntradaValida)
		          		{
		          			$msj_error = 'Decidir si el movimiento es una entrada o una salida (Debería ser una salida pero el movimiento se encuentra dentro del rango válido para una entrada)';		       					
		          			$class_error='warning';		          			
		          		}
		          		else
		          		{
		          			$msj_error = 'Decidir si el movimiento es una entrada o una salida (Debería ser una salida pero no se encuentra dentro del rango válido)';		        					
		          			$class_error='warning';
		          		}
		          	}
		          	elseif ($cont_entradas%2!=0) 
		          	{
		          		if(!$esUnaEntradaValida)
		          		{
		          			$msj_error   = 'Decidir si el movimiento es una entrada o una salida, y registrar si es necesario su movimiento pareja (No se encuentra dentro del rango válido para una entrada/salida)';
				        	$class_error = 'warning'; 
		          		}
		          		else
		          		{
		          			if($cont_entradas==3)
		          			{
		          				$class_error='danger'; $tipo_error=1; $msj_error = 'Incluir salida'; 
		          			}
		          		}
		          	}
		        }

		  		if($total_entradas>=4)  // Error de 4 entradas ó más
		  		{
		  		 	if($cont_entradas%2==0)
		  		  	{
		  		  		if(!$esUnaEntradaValida && $esUnaSalidaValida)
		  		  		{
		  		  			$class_error='danger'; $tipo_error=2; $msj_error='Convertir en salida'; 
		  		  		}
		  		  		else
		  		  		{
		  		  			$msj_error = 'Decidir si el movimiento es una entrada o una salida (Debería ser una salida pero el movimiento se encuentra en el rango válido para una entrada)';
		  		  			$class_error='warning';		  		  		
		  		  		}
		  		  	}
		  		}
		    }
		    else if($total_entradas>$total_salidas)
		    {
		    	//echo "Total Entradas: ".$total_entradas. " / Total Salidas: " . $total_salidas."<br>";

		        if($cont_entradas >= ($total_entradas - $total_salidas) )
		        {	
		        	if($tipo_anterior==$tipo_movimiento)
		        	{
		        		if($cont_entradas%2==0)
		        		{
		        			if($total_entradas%2==0) // 2 entradas, 4 entradas
		        			{
			        			if(!$esUnaEntradaValida)
			        			{
			        				if($esUnaSalidaValida)
			        				{
			        					$class_error='danger'; $tipo_error=2; $msj_error = 'Convertir en salida';
			        				}
			        				else
			        				{
										$msj_error = 'Decidir si el movimiento es una entrada o una salida (hay dos entradas seguidas y el movimiento no se encuentra dentro del rango válido para una entrada/salida)';
		        						$class_error='warning'; // Ficha 11	29-10-2015 02:53:55 pm
			        				}			        				
			        			}     
			        			else
			        			{
		        					$msj_error = 'Revisar porque hay dos entradas seguidas';
		        					$class_error='warning';		        				
			        			}   				
		        			}
		        			else // 3 entradas, 5 entradas
		        			{
		        				if($esUnaEntradaValida)
		        				{
		        					// Ficha 11 =>	22-10-2015 01:20:49 pm	
		        					$msj_error = 'Decidir si el movimiento es una entrada o una salida (Debería ser una salida pero el movimiento se encuentra en el rango válido para una entrada)';
		        					$class_error='warning';
		        				}
		        				else
		        				{	// Ficha 11 =>	27-10-2015 01:33:10 pm
		        					$msj_error = 'Decidir si el movimiento es una entrada o una salida (hay dos entradas seguidas, debería ser una salida pero el movimiento no se encuentra dentro del rango válido para una entrada/salida)';
		        					$class_error='warning';		        					
		        				}
		        			}        			
		        		}

		        		if($cont_entradas%2!=0 && $total_entradas%2!=0)
		        		{
		        			// Pendiente ficha 9 => 28-10-2015 01:40:07 pm
		        			if($esUnaSalidaValida)
		        			{
		        				$class_error='danger'; $tipo_error=2; $msj_error = 'Convertir en salida';
		        			}		        	
		        			else
		        			{
		        				// Ficha 11 =>	22-10-2015 02:32:27 pm
		        				$msj_error   = 'Decidir si el movimiento es una entrada o una salida (hay dos entradas seguidas y el movimiento no se encuentra dentro del rango válido para una entrada)';
		        				$class_error = 'warning';		
		        			}
		        		}
		        	}
		        	else
		        	{
		        		if($cont_entradas%2==0)
		        		{
				         	if($esUnaEntradaValida && $total_entradas%2==0)
				         	{
				         		$class_error='danger'; $tipo_error=1; $msj_error = 'Incluir salida'; 
				         	}
				         	elseif ($esUnaSalidaValida) 
				         	{
				         		$class_error='danger'; $tipo_error=2; $msj_error = 'Convertir en salida'; 
				         	}	
				         	else{
				         		if($total_entradas%2==0)
				         		{	
				         			// Ficha 11 =>	21-10-2015 07:48:43 pm	
				         			$msj_error   = 'Decidir si el movimiento es una entrada o una salida, y registrar si es necesario su movimiento pareja (No se encuentra dentro del rango válido para una entrada)';
				         			$class_error='warning';
				         		}
				         	}	        			
		        		}	
		        		else
		        		{
		        			if(!$esUnaEntradaValida)
		        			{
				         		$msj_error   = 'Decidir si el movimiento es una entrada o una salida, y registrar si es necesario su movimiento pareja (No se encuentra dentro del rango válido para una entrada)';
		        				$class_error='warning';
		        			} 
		        		}
		        	}
		        }                                       
		    }
		    elseif($total_entradas==$total_salidas)
		    {
		        if($tipo_anterior==$tipo_movimiento && $cont_entradas%2==0)
		        {
		        	$msj_error = 'Verificar porque hay dos entradas seguidas';		  		  			
		        	$class_error='warning';		    	
		        }
		    }

			$tipo_anterior = 'Entrada';
		}
		else if($tipo_movimiento=='Salida')
		{
		   	$cont_salidas++;
		   //	echo "cont_entradas: $cont_entradas / cont_salidas: $cont_salidas <br>";
	    	if(!$esUnaSalidaValida)
	    	{
	    		$msj_error   = 'Revisar si el movimiento es realmente una salida (No se encuentra dentro del rango válido para una salida)';
	    	}
		   	if($esUnaEntradaValida)
		   	{
		   		if($tipo_anterior=='Salida' || $tipo_anterior!='Entrada')
		   		{
		   			$class_error='danger'; $tipo_error=4; $msj_error='Convertir en entrada';
		   		}
		   		else
		   		{
		   			$msj_error = 'Decidir si el movimiento es una entrada o una salida (Podría ser una salida pero el movimiento se encuentra en el rango válido para una entrada)';
		   			$class_error='warning';
		   		}		   		 
		   	}
		    else if($total_entradas==0)
		    {
		      	if($total_salidas==1 && !$salida_invalida) // Error de salida sin entrada
		      	{ 
		      	 	$class_error='danger'; $tipo_error=3; $msj_error='Incluir entrada'; 
			  	}

		      	if($total_salidas==2) // Error de salida doble && $cont_salidas==1
		      	{ 
		      	 	if($esUnaSalidaValida)
		      	 	{
		      	 		$class_error='danger'; $tipo_error=3; $msj_error='Incluir entrada'; 
		      	 	}
	          		else
	          			$class_error='warning';
			  	}

		     	if($total_salidas==3 && $cont_salidas>=2) // Error de salida triple
		      	{ 
		      		if($cont_salidas%2!=0)
		      		{
		      			$class_error='danger'; $tipo_error=4; $msj_error='Convertir en entrada'; 
		      		}
		     	}

		     	if($total_salidas>=4) // Error de 4 salidas ó más
		     	{ 
		      		if($cont_salidas%2!=0)
		      		{
		      			$class_error='danger'; $tipo_error=4; $msj_error='Convertir en entrada';
		      		}
			  	}
		    }
		    else if($total_salidas>$total_entradas)
		    {
		      	// No necesariamente tiene que ser un error, puede ser por ejemplo que sali en la madruga (02 am), 
		   	  	// entre a las 09 am y sali a las 04pm (2 salidas y 1 entrada)

		        if($cont_salidas >= ($total_salidas - $total_entradas) )
		        {
		        	if($tipo_anterior==$tipo_movimiento)
		        	{
		        		if($cont_salidas%2==0)
		        		{
		        			$class_error='danger'; $tipo_error=4; $msj_error='Convertir en entrada';

		        			if($esUnaSalidaValida)
		        			{
		        				$class_error='danger'; $tipo_error=3; $msj_error='Incluir entrada'; 
		        			}
		        		}
		        		else
		        		{
		        			if($esUnaSalidaValida)
		        			{
		        				$class_error='danger'; $tipo_error=3; $msj_error='Incluir entrada'; 
		        			}		        			
		        		}
		        	}
		        }   
		    }
		    elseif($total_entradas==$total_salidas)
		    {
		        if($tipo_anterior==$tipo_movimiento && $cont_salidas%2==0)
		        {
		        	$msj_error = 'Verificar porque hay dos salidas seguidas';		  		  			
		        	$class_error='warning';    	
		        }
		    }
		    $tipo_anterior = 'Salida';
		}
	}
	else
	{
		// Cómo no esta definido el tipo de movimiento la corrección sera agregar el tipo, a partir del rango de entrada o salida
		$class_error = 'warning';
		$class_text  = 'text-red';
		$tipo_error  = 0;

		if($tipo_anterior == 'Entrada')
		{
			// Si el anterior es una Entrada el registro actual debe ser una Salida
			$class_error='danger'; $tipo_error=2; $msj_error = 'Convertir en salida';
		}
		elseif ($tipo_anterior == 'Salida') 
		{
			// Si el anterior es una Salida el registro actual debe ser una Entrada
			$class_error='danger'; $tipo_error=4; $msj_error='Convertir en entrada'; 
		}
		$tipo_anterior='';
	}

	if ($hora_desde1 instanceof DateTime) 
	{
		$hora_desde1_anterior = $hora_desde1; 
		$hora_hasta1_anterior = $hora_hasta1;
	}
	
	return array ($tipo_error, $msj_error, $class_error, $class_text, $rango_entradas, $rango_salidas);
}

//======================================================================================================================
/**
  * Función para obtener el turno de un trabajador para una determinada fecha
  *
  * El turno se obtiene al buscar la ficha y la fecha enviada como parametros
  * en la tabla nomcalendarios_personal
  */
function obtenerTurnoPorFecha($ficha, $fecha)
{
	global $conexion;

	$sql = "SELECT turno_id 
	        FROM   nomcalendarios_personal 
	        WHERE  ficha='{$ficha}' AND fecha='{$fecha}';";

	return $conexion->query($sql)->fetchColumn();
}

/**
* Función para obtener el horario de un turno para un determinado día de la semana
*
* Se retorna la hora de inicio (hora desde) y la hora fin (hora hasta) del horario
* para dicho día de trabajo
*
*/

function obtenerHorarioTurnoDia($turno_id, $dia_sem, $fecha, &$hora_desde1, &$hora_hasta1, &$rango_entradas, &$rango_salidas, $hora_desde1_anterior, $hora_hasta1_anterior)
{
	global $conexion;

	// Recorremos los horarios del turno para dicho dia
	// Los horarios se guardan en bloques (Bloque 1 = 08:00am a 12:00pm / Bloque 2 = 02:00 pm a 06:00 pm)
	$sql = "SELECT hora_desde, hora_hasta, entrada_desde, entrada_hasta, salida_desde, salida_hasta
			FROM   nomturnos_horarios
			WHERE  turno_id='{$turno_id}' AND dialibre=0 AND {$dia_sem}=1";

	$res = $conexion->query($sql);

	$hora_desde1 = $hora_hasta1 = '';

	$inc=false;

	// En este ciclo se pretende obtener el rango permitido para el movimiento de acuerdo a la fecha
	// El movimiento puede estar dentro del rango permitido o no

	while($row = $res->fetch())
	{
		if($hora_desde1==='' && $hora_hasta1==='')
		{
			$hora_desde1 = DateTime::createFromFormat('Y-m-d H:i:s', $fecha.' '.$row['entrada_desde']); // $row['hora_desde']
			$hora_hasta1 = DateTime::createFromFormat('Y-m-d H:i:s', $fecha.' '.$row['salida_hasta']);  // $row['hora_hasta']

			if($hora_desde1>$hora_hasta1)
			{
				$hora_hasta1 = $hora_hasta1->modify('+1 day');
				$inc = true; // incrementó
			}
		}
		else
		{
			$hora_desde2 = DateTime::createFromFormat('Y-m-d H:i:s', $fecha.' '.$row['entrada_desde']); // $row['hora_desde']
			$hora_hasta2 = DateTime::createFromFormat('Y-m-d H:i:s', $fecha.' '.$row['salida_hasta']);  // $row['hora_hasta']

			if( $hora_desde1 > $hora_desde2 )
			{
				if( $hora_hasta1 > $hora_hasta2  &&  $inc )
				{
					// Por ejemplo: Bloque 1 de 08:00 pm a 12:00 am y Bloque 2 de 02:00 am a 06:00 am
					$hora_desde2 = $hora_desde2->modify('+1 day');
					$hora_hasta2 = $hora_hasta2->modify('+1 day');

					$hora_hasta1 = $hora_hasta2;
				}
				else if( $hora_hasta1 > $hora_hasta2  &&  !$inc )
				{
					// Si registran los bloques invertidos (Poco probable, pero es por si acaso) 
					// Por ejemplo: Bloque 1 de 01:30 pm a 04:30 pm y Bloque 2 de 07:30 am a 12:30 pm

					$hora_desde1 = $hora_desde2;
				}
			}
			else // $hora_desde1 < $hora_desde2
			{
				if($hora_hasta1 < $hora_hasta2)
				{
					// Por ejemplo: Bloque 1 de 07:30 am a 12:30 pm y Bloque 2 de 01:30 pm a 04:30 pm
					$hora_hasta1 = $hora_hasta2;
				}	
			}
		}	
		//======================================================================================================
			$ent_desde = DateTime::createFromFormat('Y-m-d H:i:s', $fecha.' '.$row['entrada_desde']);
			$ent_hasta = DateTime::createFromFormat('Y-m-d H:i:s', $fecha.' '.$row['entrada_hasta']);

			if($ent_hasta < $ent_desde)
			{
				$ent_hasta = $ent_hasta->modify('+1 day');
			}

			$rango_entradas[] = array('desde' => $ent_desde,	'hasta' => $ent_hasta);
		//---------------------------------------------------------------------------------------------------
			$sal_desde = DateTime::createFromFormat('Y-m-d H:i:s', $fecha.' '.$row['salida_desde']);
			$sal_hasta = DateTime::createFromFormat('Y-m-d H:i:s', $fecha.' '.$row['salida_hasta']); 

			if($sal_desde<$hora_desde1)
			{
				$sal_desde = $sal_desde->modify('+1 day');
			}

			if($sal_hasta<$hora_hasta1)
			{
				$sal_hasta = $sal_hasta->modify('+1 day');
			}
		
			$rango_salidas[] = array('desde' => $sal_desde,		'hasta' => $sal_hasta);
		//======================================================================================================
	}

	if (!($hora_desde1 instanceof DateTime)) 
	{
		// La hora_desde1 puede no estar definida en dos casos:
		// 1) No existe un horario definido para ese dia
		// 2) O se puede tratar de una salida del dia anterior (Ejemplo: un horario rotativo del turno R)
		$hora_desde1 = $hora_desde1_anterior; // Entrada desde de la fecha anterior
		$hora_hasta1 = $hora_hasta1_anterior; // Salida  hasta de la fecha anterior
	}
}

/**
* Función para verificar si una Entrada se encuentra dentro del rango permitido
*
* Se valida si un movimiento que dice ser una Entrada se encuentrada dentro del rango definido
* por las variables entrada_desde y entrada_hasta 
*/
function verificarEntrada($fecha_completa, $rango_entradas, &$class_text, $tipo_movimiento)
{
    // Si el movimiento dice que es una entrada debo comprobar si se encuentra efectivamente en el rango de entrada
    // Sino se encuentra dentro del rango de entrada, el unico que puede indicar si es una entrada o un error es el usuario
    $num=0;
	for($i=0; $i < count($rango_entradas); $i++)
	{
		$entrada_desde = $rango_entradas[$i]['desde'];
		$entrada_hasta = $rango_entradas[$i]['hasta'];

		if($fecha_completa >= $entrada_desde   &&   $fecha_completa <= $entrada_hasta)
		{
			$num++;
		}
	}

    if($num==0)
    {
        // Es una entrada que está fuera del rango válido para una entrada
        if($tipo_movimiento=='Entrada') $class_text='text-red';
        return false;
    }

    return true;
}

/**
* Función para verificar si una Salida se encuentra dentro del rango permitido
*
* Se valida si un movimiento que dice ser una Salida se encuentrada dentro del rango definido
* por las variables salida_desde y salida_hasta 
*/
function verificarSalida($fecha_completa, $rango_salidas, $salidas_anterior, &$class_text, $tipo_movimiento)
{
    // Si el movimiento dice que es una salida debo comprobar si se encuentra efectivamente en el rango de salida
    // Sino se encuentra dentro del rango de salida, el unico que puede indicar si es una salida o un error es el usuario
    $num=0;
	for($i=0; $i < count($rango_salidas); $i++)
	{
		$salida_desde = $rango_salidas[$i]['desde'];
		$salida_hasta = $rango_salidas[$i]['hasta'];

		if($fecha_completa >= $salida_desde   &&   $fecha_completa <= $salida_hasta)
		{
			$num++;
		}
	}

	for($i=0; $i < count($salidas_anterior); $i++)
	{
		$salida_desde = $salidas_anterior[$i]['desde'];
		$salida_hasta = $salidas_anterior[$i]['hasta'];

		if($fecha_completa >= $salida_desde   &&   $fecha_completa <= $salida_hasta)
		{
			$num++;
		}				
	}

    if($num==0)
    {

        // Es una salida que está fuera del rango válido para una salida
        if($tipo_movimiento=='Salida') $class_text='text-red';
        return false;
    }	

    return true;
}
//======================================================================================================================
?>