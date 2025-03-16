<?php
//=================================================================
//Se lee el archivo correspondiente y carga en la tabla
//verificando si se puede leer el archivo
if ( ( $fichero = fopen($directorio1.$archivo, "r" ) ) !== FALSE ) {
	echo "Iniciando la carga del archivo #".$directorio1.$archivo." <br>";
	$fila = 1;
	$error = false;
	$carga = 10;
	echo "Cargando <br>";
	while( ($linea = fgetcsv($fichero, 0, $delimitador)) !== FALSE )
	{
		if ($carga == $fila) {
			echo " :: ";
			$carga = $fila + 5;
		}

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

						if($objDT == false){ 
							$msj = "¡Error! Por favor, verifique el valor de la columna $attr de la fila $fila";
							$error=true;
						}

						if($attr=='tiempo'){
							$fecha = $objDT->format('Y-m-d');	
							$hora  = $objDT->format('H:i:s');
						}

						switch ($attr) {
							case 'fecha':
								$fecha = $objDT->format('Y-m-d');
								break;
							case 'hora':
								$hora  = $objDT->format('H:i:s');
						    	$hh24 = $objDT->format('H');
								break;
							case 'minutos':
								$min   = $objDT->format('i');
								break;
							case 'mes':
								$mes   = $objDT->format('m');
								break;
							case 'dia':
								$dia   = $objDT->format('d');
								break;
							case 'anio':
								$anio  = $objDT->format('Y');
								break;						
							default:
								# code...
								break;
						}
					}
	        		if( isset($parametros['dispositivo'])  &&  in_array($posicion, $parametros['dispositivo']) )
	        		{
	        			$dispositivo .= $columna;	   
	        		}
	        		$j++;
	        	}
	        }
	        if ($j > 1 && !$error)
	        {
	        	if(!empty($dia) && !empty($mes) && !empty($anio))
	        	{
	        		$fecha = $anio . '-' . $mes . '-' . $dia;
	        	}
	        	if(!empty($min))
	        	{
	        		$hora = $hh24 . ':'. $min . ':00';
	        	}
	        	//Busco el turno del empleado y proceso el tipo de movimiento del registro
				// Guardamos los datos obtenidos del archivo de reloj
				$conexion->query("INSERT INTO `caa_registros`(`fecha_registro`,`ficha`,`fecha`,`hora`,`turno_id`,`dispositivo`,`archivo_reloj`)VALUES('".$fecha_registro."','".$ficha."','".$fecha."','".$hora."','".get_turno_id( (int) $ficha,$fecha,$calendario)."','".$dispositivo."','".$last_id."')");
	        }
		}
	    $fila++;        
	}
	fclose($fichero);
}else{
	echo "No se pudo abrir el archivo <br>";
}
echo " <br> Se cerro el archivo <br>";
?>