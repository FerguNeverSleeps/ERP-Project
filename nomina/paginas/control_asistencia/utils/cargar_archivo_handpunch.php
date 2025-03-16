<?php
//Cargamos las Variables
$fecha_inicio  = date('Y-m-d');
$fecha_fin     = date('Y-m-d');
$configuracion = 3;
// Inicio de la transacción
//cargando archivos necesarios para buscar turnos
//------------------------------------------------------------
//se carga el calendario de personal completo
$cal = $conexion2->query("SELECT * FROM `nomcalendarios_personal`");
$i=0;
while ( $fila = mysqli_fetch_array($cal) )
{
	$calendario[$i] = array('ficha' => $fila['ficha'],'fecha' => $fila['fecha'],'turno_id' => $fila['turno_id']);
	$i++;
}
//------------------------------------------------------------
//se cargan todos los turnos registrados
$tur = $conexion2->query("SELECT * FROM `nomturnos`");
$i=0;
while ( $fila = mysqli_fetch_array($tur) )
{
	$t_extra = date("H:i:s",strtotime("00:00:00")+strtotime($reg['salida'])+strtotime("01:00:00"));
	$turnos[$i] = array('turno_id' => $fila['turno_id'],'descripcion' => $fila['descripcion'],'entrada' => $fila['entrada'],'tolerancia_entrada' => $fila['tolerancia_entrada'],'salida' => $fila['salida'],'tolerancia_salida' => $fila['tolerancia_salida'],'tolerancia_extra' => $t_extra,'libre' => $fila['libre'],'tipo' => $fila['tipo']);
	$i++;
}
//----------------------------------
$conexion2->query("SET AUTOCOMMIT=0");
$conexion2->query("START TRANSACTION");

// Registramos los datos generales del archivo de reloj
$conexion2->query("INSERT INTO `caa_archivos_reloj` (`fecha_registro`, `fecha_inicio`, `fecha_fin`, `configuracion` ) 
		VALUES ('".date('Y-m-d H:i:s')."','".$fecha_inicio."','".$fecha_fin."','".$configuracion."' )");

$max_id = $conexion2->query("SELECT MAX(codigo) as id FROM `caa_archivos_reloj`");
$max_id = mysqli_fetch_array($max_id);
$last_id = $max_id['id'];

echo "Paso 1: Se cargo el archivo".$directorio1.$archivo." en la tabla caa_archivos_reloj con el id :". $last_id." <br>";
// Consultamos la configuracion del formato de reloj seleccionado por el usuario

$res = $conexion2->query( "SELECT * FROM `caa_configuracion` WHERE codigo = '".$configuracion."'" );

if( $fila = mysqli_fetch_array($res) )
{
	$delimitador      = str_replace("\\t", "\t", $fila['delimitador']);
	$primera_linea    = $fila['primera_linea'];
	$ignorar_columnas = $fila['ignorar_columnas'];
	$filas_vacias     = $fila['filas_vacias'];
	$valor_entrada    = $fila['valor_entrada'];
	$valor_salida     = $fila['valor_salida'];
}

// Consultamos los parametros de esta configuracion
$res = $conexion2->query( "SELECT * FROM `caa_parametros` WHERE configuracion = '".$configuracion."'" );
$parametros = array();
while( $fila = mysqli_fetch_array($res) )
{
	if( $fila['nombre'] == 'dispositivo' ) // Si el nombre del parametro es dispositivo
	{	
		$parametros['dispositivo'][] = $fila['posicion'];
	}
	else{
		$parametros[$fila['nombre']] = $fila['posicion'];
	}
	if(!empty($fila['formato'])) $parametros['formato_'.$fila['nombre']] = $fila['formato'];
}
// Leemos el archivo cargado por el usuario
//=================================================================
$fichero = fopen($directorio1.$archivo, "r");
$fila = 1; $error = false;
if (!$fichero) {
	echo "Paso 2: Cargar datos leidos del archivo en ".$directorio1.$archivo." <br>";
}
//=================================================================
$conexion2->query("DELETE FROM `caa_registros` WHERE fecha_registro = '$fecha_registro'");
//=================================================================
$conexion2->query("ALTER TABLE `caa_registros` AUTO_INCREMENT =1");
//=================================================================
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
			$conexion2->query("INSERT INTO `caa_registros`(`fecha_registro`,`ficha`,`fecha`,`hora`,`turno_id`,`dispositivo`,`archivo_reloj`)VALUES('".$fecha_registro."','".$ficha."','".$fecha."','".$hora."','".get_turno_id( (int) $ficha,$fecha,$calendario)."','".$dispositivo."','".$last_id."')");
        	echo "Ficha: ".$ficha." - fecha: ".$fecha." - hora: ".$hora." <br>";
        }
	}
    $fila++;        
}

fclose($fichero);
if($error)
{
	$conexion2->query("ROLLBACK");
	$proceso = FALSE;
	echo "Ocurrio un error en la transaccion (Paso 2)<br>";
} 					
else
{
	$conexion2->query("COMMIT");
	$proceso = TRUE;
	echo "No ocurrio ningun error en la transaccion (Paso 2) <br>";
}