<?php
function  cantidad_meses($fecha1,$fecha2)
{
    //$fecha1 = '2014-07-01';
    //$fecha2 = '2016-07-01';

    
    $anio_ini = date("Y", strtotime($fecha1));
    $mes_ini = date("m", strtotime($fecha1));

    $anio_fin = date("Y", strtotime($fecha2));
    $mes_fin = date("m", strtotime($fecha2));
    
    if ($anio_ini == $anio_fin)
    {
       $meses = ($mes_fin-$mes_ini) + 1;
    } else 
    {
       $meses = ((($anio_fin - $anio_ini) * 12) - $mes_ini) + 1 + $mes_fin;
    }
    return $meses;
}

function  cantidad_dias_mes_fecha($fecha1)
{
    
    $anio = date("Y", strtotime($fecha1));
    $mes = date("m", strtotime($fecha1));

    $dias= cal_days_in_month ( CAL_GREGORIAN, $mes, $anio );
    return $dias;
}

function  cantidad_dias_mes($mes, $anio)
{    
    $dias= cal_days_in_month ( CAL_GREGORIAN, $mes, $anio );
    return $dias;
}

function dias_transcurridos($fecha1,$fecha2)
{
	$dias	= (strtotime($fecha1)-strtotime($fecha2))/86400;
	$dias 	= abs($dias); $dias = floor($dias);		
	return $dias;
}
?>

