<?php
// Obtener el desglose en billetes y monedas de una determinada cantidad en dólares
function desglosar_monedas_billetes($cantidad, &$cambio)
{
    $monedas = array(20, 10, 5, 1, 0.5, 0.25, 0.10, 0.05, 0.01);
    $cambio  = array(0, 0, 0, 0, 0, 0, 0, 0, 0);

    foreach ($monedas as $i=>$moneda) 
    {
        //echo "<br>Indice: $i => Moneda: $moneda <br>";
        $cambio[$i] = 0;

        while((round($cantidad-$moneda,2)>=0))
        {
            $cantidad = round(($cantidad - $moneda),2);
            $cambio[$i]++;  
        }
    }
}

function get_fecha_vacaciones($fecha_ingreso, $anio)
{
	$fecha_ingreso = new DateTime($fecha_ingreso);
	
	$mes  = $fecha_ingreso->format('m');
	$dia  = $fecha_ingreso->format('d');

	if($fecha_ingreso->format('Y')!=$anio && $mes>1)
	{
		$anio = $anio - 1 ;
	}

	$fecha = new DateTime();
	$fecha->setDate($anio, $mes, $dia);
	
	if($dia>=1 && $dia<=15)
	{
		$fecha_vac = date_create($fecha->format('Y-m-1'));
	}
	else
	{
		$fecha_vac = date_create($fecha->format('Y-m-16'));
	}
	
	$fecha_vac->modify('+11 month');
	$fecha_vac->setTime(0, 0, 0);
	
	return $fecha_vac;
}

function get_sueldo_minimo($anio, $mes)
{
	global $db;

	$sueldo = 0; // Sueldo Mínimo Mensual

	$sql = "SELECT s.monto, s.fecha_aplicacion
			FROM   sueldos_minimos s
			WHERE (YEAR(s.fecha_aplicacion)={$anio} AND  MONTH(s.fecha_aplicacion) <= {$mes}) 
			OR     YEAR(s.fecha_aplicacion)<{$anio}
			ORDER BY s.fecha_aplicacion DESC
			LIMIT 1";

	$res = $db->query($sql);

	if($fila = $res->fetch_object())
	{
		$sueldo = $fila->monto;
	}

	return $sueldo;
}

/**
* Esta función permite obtener los conceptos disponibles en caa_conceptos a partir del atributo variable
* Se pueden obtener los conceptos para todas las variables, para algunas variables o para una variable específica
*
* $variable puede ser igual a:
* - asterico * (que significa todas las variables)
* - un arreglo de variables como por ejemplo array('regular', 'nacional') => $conceptos = obtener_conceptos( array('regular', 'nacional') );
* - un valor específico como por ejemplo 'regular' => $condicion_regular = obtener_conceptos('regular');
*
* $tabla es el alias de la tabla en la que se van a utilizar los conceptos y condiciones 
* SQL devueltos por la función (generalmente es el alias de la tabla nom_movimientos_nomina)
*/
function obtener_conceptos($variable='*', $tabla='n')
{
	global $db;

	$condicion = '';
	$conceptos = array();
	$esun_concepto = false; // falso significa que se van a consultar varios conceptos

	if(is_array($variable))
	{
		// Se consultan algunas variables en caa_conceptos (varios conceptos)
		$in  = "'".implode("','", $variable)."'";

		$sql = "SELECT variable, concepto FROM caa_conceptos WHERE variable IN({$in}) ORDER BY variable";
	}
	else if($variable=='*')
	{
		// Por defecto, se consultan todas las variables en caa_conceptos (todos los conceptos)
		$sql = "SELECT variable, concepto FROM caa_conceptos ORDER BY variable";
	}
	else
	{
		// Se consulta una variable en específico
		$sql = "SELECT concepto FROM caa_conceptos WHERE variable='{$variable}'";

		$esun_concepto = true; // verdadero significa que se va a consultar un solo concepto
	}

	$res = $db->query($sql);

	while($fila = $res->fetch_object())
	{
		/* La variable puede tener un único concepto, varios conceptos separados por coma (,), 
		   o un rango representado por dos conceptos separados por dos puntos (:) */
		$tiene_comas = strpos($fila->concepto, ',');
		$tiene_rango = strpos($fila->concepto, ':');

		if($tiene_comas !== false)
		{
			// Son varios conceptos separados por coma
			$condicion = " {$tabla}.codcon IN({$fila->concepto}) ";
		}
		else if($tiene_rango !== false)
		{
			// Es un rango de conceptos
			$codcon = explode(':', $fila->concepto);

			$condicion = " {$tabla}.codcon BETWEEN '{$codcon[0]}' AND '{$codcon[1]}' ";
		}
		else
		{
			// Un solo concepto
			$condicion = " {$tabla}.codcon = {$fila->concepto} ";
		}

		if(!$esun_concepto)
			$conceptos[] = array("variable" => $fila->variable, "condicion" => $condicion);
	}

	return ($esun_concepto) ? $condicion : $conceptos;
}

function get_datos_concepto($variable)
{
	global $db;

	$concepto = (object)[]; // stdClass Object ( )

	$sql = "SELECT codcon, descrip, tipcon 
			FROM   nomconceptos 
			WHERE  codcon=(SELECT concepto FROM caa_conceptos WHERE variable='{$variable}')";

	$res = $db->query($sql);

	if($res->num_rows > 0)
	{
		$concepto = $res->fetch_object();	
		// stdClass Object ( [codcon] => 96 [descrip] => XIII MES Servicios Profesionales [tipcon] => A )	
	}

	return $concepto;
}

function get_nombre_mes($numero, $convert='lowercase')
{
	$nombre_mes = array('enero', 'febrero', 'marzo',      'abril',   'mayo',      'junio', 
		                'julio', 'agosto',  'septiembre', 'octubre', 'noviembre', 'diciembre');

	$numero = ($numero>=12) ? 0 : $numero; // Número del mes: 1-Enero / 12-Diciembre

	$mes = $nombre_mes[$numero];

	if($convert=='uppercase')
		$mes = strtoupper($mes);
	else if($convert=='capitalize')
		$mes = ucfirst($mes); 

	return $mes;
}

function traducir_mes($mes, $convert='lowercase')
{
	$mes = strtolower($mes);

	$meses_in = array('january', 'february', 'march', 'april', 'may', 'june', 'july', 
					  'august', 'september', 'october', 'november', 'december');	

	$meses_es = array('enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio',
					  'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre');

	$mes = str_replace($meses_in, $meses_es, $mes);

	if($convert=='uppercase')
		$mes = strtoupper($mes);
	else if($convert=='capitalize')
		$mes = ucfirst($mes);    

	return $mes;	
}

//================================================================================
// Brentwood INICIO

// Indica el Sueldo minimo de una fecha, parametro Fecha LM
function minimo($fechafin)
{
    global $db;

    $sql = "SELECT s.monto, s.fecha_aplicacion
            FROM   sueldos_minimos s
            WHERE '{$fechafin}'>= s.fecha_aplicacion 
            ORDER BY s.fecha_aplicacion DESC";

    $minimos = $db->query($sql)->fetch_assoc();

    $sueldo  = ($minimos['monto'] == '') ? 0 : $minimos['monto'] ;

    return $sueldo;
}

// Devuelve el monto de un concepto en la quincena correspondiente a una fecha, parametros, concepto y fecha LM
function conceptoperiodo($codcon, $fecha)
{
    global $db, $FICHA;

    $sql = "SELECT COALESCE(SUM(n.monto), 0) AS total_concepto, p.codnom, p.periodo_ini, p.periodo_fin
            FROM   nom_movimientos_nomina n
            RIGHT OUTER JOIN nom_nominas_pago p ON (n.codnom = p.codnom)
            WHERE  ficha ='{$FICHA}' AND codcon = '{$codcon}' 
            AND    p.periodo_ini <= '{$fecha}' AND p.periodo_fin >= '{$fecha}'";

    $fila = $db->query($sql)->fetch_assoc();

    return $fila['total_concepto'];
}

// Calcula el XIII mes de un periodo para un colaborador, (Reglas Brentwod sueldo Minimo), parametros fecha inicio y fin,
// sueldo si no se quiere basar en sueldo minimo y concepto de sueldo LM
function xiii($fecha_inicio, $fecha_ingreso, $sueldo_actual, $concepto_calculo)
{
    // Convertimos la fecha de inicio en objeto
    $fecha_obj = DateTime::createFromFormat('Y-m-d', $fecha_inicio);

    // Buscamos el valor del sueldo minimo para la fecha
    $fecha_minimos = array($fecha_inicio);

    $minimo[0] = ($sueldo_actual > 0) ? $sueldo_actual : minimo($fecha_inicio) ;    

    // Buscamos el sueldo minimo para cada fecha y lo acumulamos
    for($j=1; $j<=4; $j++) 
    {
        $fecha_mes = $fecha_obj->add(new DateInterval('P1M'));

        $fecha_minimos[$j] = $fecha_mes->format("Y-m-d");

        $minimo[$j] = ($sueldo_actual > 0) ? $sueldo_actual : minimo($fecha_minimos[$j]) ;
    }

    $minimo[4] /= 2;

    // Si la fecha de ingreso es anterior al rango de fecha a procesar se le paga completo
    if( $fecha_ingreso <= $fecha_inicio )
    	$minimo[0] /= 2;     

    // Si la fecha de ingreso es posterior al inicio del rango se busca cuanto gano la quincena que empezo
    if( $fecha_ingreso > $fecha_inicio )
    {
        $xiii = conceptoperiodo($concepto_calculo, $fecha_ingreso);

        $dia_inicio = substr($fecha_ingreso, -2, 2);

        $minimo[0] = 0;         

        if( $fecha_ingreso < $fecha_minimos[1] )  // Caso 1
        { 
            if( $dia_inicio >= 16 ) 
                $minimo[0] = $xiii; 
            else
                $minimo[1] = $xiii + ($minimo[1]/2); 
        }
        else if( $fecha_ingreso < $fecha_minimos[2] )  // Caso 2
        {
            if( $dia_inicio >= 16 )
                $minimo[1] = $xiii;
            else
            {
                $minimo[1] = 0;
                $minimo[2] = $xiii + ($minimo[2]/2); 
            }
        }
        else if( $fecha_ingreso < $fecha_minimos[3] )  // Caso 3
        {
            if( $dia_inicio >= 16 )
            {
                $minimo[1] = 0;
                $minimo[2] = $xiii;
            } 
            else
            {
                $minimo[1] = $minimo[2] = 0;
                $minimo[3] = $xiii + ($minimo[3]/2);
            }
        }
        else if( $fecha_ingreso < $fecha_minimos[4] )  // Caso 4
        {
            if( $dia_inicio >= 16 )
            {
                $minimo[1] = $minimo[2] = 0;
                $minimo[3] = $xiii;
            }
        }
    }

    return $minimo;
}
// Brentwood FIN