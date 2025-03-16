<?php
list($exito, $msj, $archivo) = cargar_archivo('archivos/', 200000000, $_FILES["archivo"]); // 2000 Kb

if($exito)
{
	// Inicio de la transacción
	$conexion->beginTransaction();

	// Registramos los datos generales del archivo de reloj
	$conexion->insert('caa_archivos_reloj', array('fecha_registro' => date('Y-m-d H:i:s'), 'fecha_inicio' => $fecha_inicio, 'fecha_fin' => $fecha_fin, 'configuracion' => $configuracion));
	$last_id = $conexion->lastInsertId();

	// Consultamos la configuracion del formato de reloj seleccionado por el usuario
	$res = $conexion->createQueryBuilder()
				    ->select('delimitador',  'primera_linea', 'ignorar_columnas', 'filas_vacias', 'valor_entrada', 'valor_salida')
				    ->from('caa_configuracion')
				    ->where('codigo = ?')
				    ->setParameter(0, $configuracion)
				    ->execute();

	if($fila = $res->fetch())
	{
		$delimitador      = str_replace("\\t", "\t", $fila['delimitador']);
		$primera_linea    = $fila['primera_linea'];
		$ignorar_columnas = $fila['ignorar_columnas'];
		$filas_vacias     = $fila['filas_vacias'];
		$valor_entrada    = $fila['valor_entrada'];
		$valor_salida     = $fila['valor_salida'];
	}

	// Consultamos los parametros de esta configuracion
	$res = $conexion->createQueryBuilder()
				    ->select('codigo',  'nombre', 'posicion', 'formato')
				    ->from('caa_parametros')
				    ->where('configuracion = ?')
				    ->setParameter(0, $configuracion)
				    ->execute();

	// $parametros = array('numero'=>'', 'tipo_movimiento'=>'', 'dispositivo'=>array(), 
	// 					   'tiempo'=>'', 'formato_tiempo' =>'',
	// 					   'dia'   =>'', 'formato_dia'    =>'', 'mes'    =>'', 'formato_mes'    =>'', 'anio'=>'', 'formato_anio'=>'',
	// 					   'hora'  =>'', 'formato_hora'   =>'', 'minutos'=>'', 'formato_minutos'=>'',
	// 					   'fecha' =>'', 'formato_fecha'  =>'', 'hora'   =>'', 'formato_hora'   =>'' ); 
	$parametros = array();
	while($fila = $res->fetch())
	{
		if( $fila['nombre'] == 'dispositivo' ) // Si el nombre del parametro es dispositivo
			$parametros['dispositivo'][] = $fila['posicion'];
		else
			$parametros[$fila['nombre']] = $fila['posicion'];

		if(!empty($fila['formato'])) $parametros['formato_'.$fila['nombre']] = $fila['formato'];
	}

	// Leemos el archivo cargado por el usuario
	$fichero = fopen($archivo, "r");

	$fila = 1; $error = false;
	while( ($linea = fgetcsv($fichero, 0, $delimitador)) !== FALSE )
	{
		$linea_vacia = (count($linea)==1 && is_null($linea[0]) && $filas_vacias==0) ? true : false;

		if( ($primera_linea!=1 || ($primera_linea==1 && $fila>1)) && !$linea_vacia )
		{
			$ficha = $tipo_movimiento = $dispositivo = NULL; 				// Inicializar variables
			$fecha = $hora = $hh24  = $min = $dia = $mes = $anio = NULL;    // Inicializar variables

	        for ($i=0, $j=1; $i < count($linea); $i++) 
	        {	
	        	$columna  = trim($linea[$i]);	

	        	if( $columna!=''   ||   ($columna=='' && $ignorar_columnas==0) )
	        	{
	        		$posicion = ($ignorar_columnas==1) ? $j : $i+1;

	        		if( $posicion == $parametros['numero'])
	        			$ficha = $columna;
		        	if( (isset($parametros['tiempo'])   &&  $posicion == $parametros['tiempo'])   || 
		        		(isset($parametros['fecha'])    &&  $posicion == $parametros['fecha'])    || 
		        		(isset($parametros['hora'])     &&  $posicion == $parametros['hora'])     ||
	        		    (isset($parametros['minutos'])  &&  $posicion == $parametros['minutos'])  || 
	        		    (isset($parametros['mes'])      &&  $posicion == $parametros['mes'])      || 
	        		    (isset($parametros['dia'])      &&  $posicion == $parametros['dia'])      ||
	        		    (isset($parametros['anio'])     &&  $posicion == $parametros['anio']) )
	        		{
	        				$columna  = strtolower($columna);

	        				$attr = 'tiempo';
	        				if( isset($parametros['fecha'])   &&  $posicion == $parametros['fecha'] )   $attr = 'fecha';  
	        				if( isset($parametros['hora'])    &&  $posicion == $parametros['hora'] )    $attr = 'hora';  
	        				if( isset($parametros['minutos']) &&  $posicion == $parametros['minutos'] ) $attr = 'minutos'; 
	        				if( isset($parametros['mes'])     &&  $posicion == $parametros['mes'] )     $attr = 'mes'; 
	        				if( isset($parametros['dia'])     &&  $posicion == $parametros['dia'] )     $attr = 'dia';
	        				if( isset($parametros['anio'])    &&  $posicion == $parametros['anio'] )    $attr = 'anio';  

	        				$formato = str_replace('/', '-', $parametros["formato_$attr"]);

	        				$columna = str_replace(array('/', 'a.m.', 'a.m', 'p.m.', 'p.m'),  
	        					                   array('-', 'am'  , 'am' , 'pm'  , 'pm' ), 
	        					                   $columna
	        					                  );

							$objDT   = DateTime::createFromFormat($formato, $columna);

							if($objDT == false){ $msj = "¡Error! Por favor, verifique el valor de la columna $attr de la fila $fila"; $error=true; break; }

							if($attr=='tiempo')
							{
								$fecha = $objDT->format('Y-m-d');	
								$hora  = $objDT->format('H:i:s');
							}
							if($attr=='fecha')    $fecha = $objDT->format('Y-m-d');
							if($attr=='hora'){    $hora  = $objDT->format('H:i:s');		$hh24 = $objDT->format('H'); 	} 	
							if($attr=='minutos')  $min   = $objDT->format('i'); 
							if($attr=='mes')      $mes   = $objDT->format('m');
							if($attr=='dia')      $dia   = $objDT->format('d');
							if($attr=='anio')     $anio  = $objDT->format('Y'); 				
	        		}
	        		if( isset($parametros['tipo_movimiento'])  &&  $posicion == $parametros['tipo_movimiento'])
	        		{
	        				$tipo_movimiento = $columna;

	        				if($tipo_movimiento==$valor_entrada) $tipo_movimiento='Entrada';
	        				else if($tipo_movimiento==$valor_salida)  $tipo_movimiento='Salida';
	        				else $tipo_movimiento = NULL;
	        		}
	        		if( isset($parametros['dispositivo'])  &&  in_array($posicion, $parametros['dispositivo']) )
	        			$dispositivo .= $columna;
	   
	        		$j++;
	        	}
	        }	

	        if ($j > 1 && !$error)
	        {
	        	if(!empty($dia) && !empty($mes) && !empty($anio))
	        		$fecha = $anio . '-' . $mes . '-' . $dia;
	        	if(!empty($min))
	        		$hora = $hh24 . ':'. $min . ':00';

				// Guardamos los datos obtenidos del archivo de reloj
				$conexion->insert('caa_archivos_datos',  array('ficha'           => $ficha,
															   'fecha_hora'      => $fecha .' '.$hora,
															   'tipo_movimiento' => $tipo_movimiento,
															   'dispositivo'     => $dispositivo,
															   'archivo_reloj'   => $last_id
															   ));
	        } 
		}
        $fila++;        
	}

	fclose($fichero);

	if($error)
	{
		$conexion->rollback();
		$flash_message = $msj;	
		$flash_class   = 'alert-danger';
		echo "<script>var errorCargando=true;</script>";
	} 					
	else
	{
		// Ahora consultamos si la fecha minima y fecha maxima encontradas en los registros
		// se encuentran dentro del rango indicado por el usuario (fecha inicio y fecha fin)
		$res = $conexion->createQueryBuilder()
					    ->select('MIN(fecha_hora)', 'MAX(fecha_hora)')
					    ->from('caa_archivos_datos')
					    ->where('archivo_reloj = :archivo')
					    ->having("MIN(fecha_hora) BETWEEN '{$fecha_inicio}' AND '{$fecha_fin}' ")
					    ->andHaving("MAX(fecha_hora) BETWEEN '{$fecha_inicio}' AND '{$fecha_fin}' ")
					    ->setParameter('archivo', $last_id)
					    ->execute()
					    ->fetch();

		if(!$res)
		{
			$res = $conexion->createQueryBuilder()
						    ->select('MIN(fecha_hora) as minima', 'MAX(fecha_hora) as maxima')
						    ->from('caa_archivos_datos')
						    ->where('archivo_reloj = :archivo')
						    ->setParameter('archivo', $last_id)
						    ->execute();

			if($fila = $res->fetch())
			{
				$fecha_minima = $fila['minima'];
				$fecha_maxima = $fila['maxima'];

				$conexion->update('caa_archivos_reloj', array('fecha_inicio' => $fecha_minima, 'fecha_fin' => $fecha_maxima), array('codigo' => $last_id));	
			}
		}

		$conexion->commit();
		activar_pagina("listado_archivos.php?msj="."Archivo importado exitosamente");
	} 								
}
else
{
  $flash_message = $msj;	
  $flash_class   = 'alert-danger';
}