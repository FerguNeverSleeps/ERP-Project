<?php 
//Funciones necesarias para procesar los registros de E/S que se encuentras en la TABLA "caa_archivos_datos"

function validar_entrada($ficha,$reg,$i,$tur)
{
	if ((isset($reg[$i]['hora']))&&(date('H:i:s',strtotime($reg[$i]['hora']))>date('H:i:s',strtotime($tur['salida'])))){
		return true;
	}else{
		return false;
	}
}

function validar_incidencia( $ficha,$fecha,$inc,$conexion )
{
	$res = $conexion->query("SELECT * FROM caa_incidencias_empleados WHERE ficha = '$ficha' AND fecha = '$fecha' AND id_incidencia = '$inc'");
	if ( mysqli_num_rows($res) > 0 ) {
		return true;
	}else{
		return false;
	}
}

function validar_asistencia($ficha,$fecha,$conexion)
{
	$res = $conexion->query("SELECT * FROM caa_resumen WHERE ficha = '$ficha' AND fecha = '$fecha'");
	if (mysqli_fetch_array($res)) {
		return true;
	}else{
		return false;
	}
}

function get_mov($ficha,$reg,$i,$tur)
{
	$j = $i-1;
	if ( (isset($reg[$j]['ficha']))||(!empty($reg[$j]['ficha'])) ){
		if ( ($reg[$j]['ficha'] != $ficha) ){
			if (validar_entrada($ficha,$reg,$i,$tur)){
				return "Entrada";
			}else{
				return "Salida";
			}
		}elseif($reg[$j]['ficha'] == $ficha){
			if (diff_fec($reg,$i)){
				if ( (validar_entrada($ficha,$reg,$i,$tur)) ){
					return "Entrada";
				}else{
					return "Salida";
				}
			}else{
				if ($reg[$j]['tipo_registro'] == "Salida"){
					return "Entrada";
				}else{
					return "Salida";
				}
			}
		}
	}else{
		return "Entrada";
	}
}
function proc_fech($fec_hor)
{
	$dat = explode(" ", $fec_hor);
	return array($dat[0],$dat[count($dat)-1]);
}

function condicion($reg,$tip,$i)
{
    $result = ( ($reg[$i-1]['ficha']==$reg[$i]['ficha']) && ( ($reg[$i]['ficha']==$reg[$i+1]['ficha'])||($reg[$i+1]['tipo_registro']!=$tip)) ) ? TRUE : FALSE;
    return $result;
}

function busc_sal($reg,$i,$cant)
{
	if( ($reg[$i]['tipo_registro']=='Salida') && (condicion($reg,'Salida',$i)) )
	{
		return array($reg[$i]['hora'],$i+1,$reg[$i]['fecha']);
	}elseif ($cant < 5){
		busc_sal($reg,$i+1,$cant+1);
	}else{
		return array("00:00:00",0,"0000-00-00");
	}
}
function cant_horas($entrada,$salida)
{
	if( strtotime($salida) > strtotime($entrada) ){
		return date("H:i:s",strtotime("00:00:00")+strtotime($salida)-strtotime($entrada));
	}else{
		return date("H:i:s",strtotime("00:00:00"));
	}
}

function diff_fechora($entrada,$salida)
{
	$dteStart = new DateTime($entrada);
	$dteEnd   = new DateTime($salida);
	if ($dteStart > $dteEnd) {
		return "00:00:00";
	}else{
		$dteDiff  = $dteStart->diff($dteEnd);
		return $dteDiff->format("%H:%I:%S");
	}
}

function diff_fec($reg,$i)
{
	if ( date('Y-m-j',$reg[$i-1]['fecha']) != date('Y-m-j',$reg[$i]['fecha']) ){
		return true;
	}else{
		return false;
	}
}

function ausencia($tiempo,$tur)
{
    if ( (strtotime($tiempo) == strtotime("00:00:00") ) && ($tur['tipo']!=6)){
        return cant_horas($tur['entrada'],$tur['salida']);
    }else{
        return strtotime("00:00:00");
    }
}

function tardanza($entrada,$turno)
{
	if (strtotime($entrada) > strtotime($turno['tolerancia_entrada']))
    {
    	return date("H:i:s",strtotime("00:00:00")+strtotime($entrada)-strtotime($turno['entrada']));
    }else{
    	return date("H:i:s",strtotime("00:00:00"));
    }
}

function retraso($reg,$tur)
{
	if( strtotime($reg['entrada']) > strtotime($tur['tolerancia_entrada']) )
	{
		return date("H:i:s",strtotime("00:00:00")+strtotime($reg['entrada'])-strtotime($tur['entrada']));
	}else{
		return date("H:i:s",strtotime("00:00:00"));
	}
}

function extra($reg,$tur)
{
	if( strtotime($reg['salida']) > strtotime($tur['tolerancia_extra']) )
	{
		return date("H:i:s",strtotime("00:00:00")+strtotime($reg['salida'])-strtotime($tur['salida']));
	}else{
		return date("H:i:s",strtotime("00:00:00"));
	}
}

function get_turno_id($ficha,$fecha,$calendario)
{
	for ($i=0; $i < count($calendario); $i++)
	{
    	if ( ( $ficha == $calendario[$i]['ficha'] ) && ( $fecha == $calendario[$i]['fecha'] ) )
    	{
    		return $calendario[$i]['turno_id'];
    	}
	}
	return 1;
}

function get_turno($turno_id,$turnos)
{
	for ($i=0; $i < count($turnos); $i++)
	{
    	if ( $turno_id == $turnos[$i]['turno_id'] )
    	{
    		return $turnos[$i];
    	}
	}
	return $turnos[0];
}

function get_dia($ficha,$fecha,$cal)
{
	for ($i=0; $i < count($cal); $i++)
	{
    	if ( ( $fecha == $cal[$i]['fecha'] ) && ( $ficha == $cal[$i]['ficha'] ) )
    	{
    		return $cal[$i]['dia_fiesta'];
    	}
	}
	$d=date('N', strtotime($fecha));
	return (($d<6)?0:$d);
}
function proc_reg($hora,$turno)
{
	$mov = ( strtotime($hora) < strtotime($turno['salida']) ) ? "Entrada" : "Salida";
	switch ($mov) {
		case 'Entrada':
			$incidencia['tipo']   = ( strtotime($hora) > strtotime($turno['tolerancia_entrada']) ) ? "Retardo Entr" : "Normal";
			$incidencia['tiempo'] = cant_horas(strtotime($turno['entrada'],strtotime($hora)));
			break;
		case 'Salida':
			$incidencia['tipo'] = ( strtotime($hora) > strtotime($turno['tolerancia_salida']) ) ? "Tiempo Extr" : "Normal";
			$incidencia['tiempo'] = cant_horas(strtotime($turno['salida']),strtotime($hora));
			break;
		default:
			# code...
			break;
	}
	$res = array(
		'tipo_registro' => $mov,
		'turno_id'      => $turno['turno_id'],
		'turno'         => $turno['descripcion'],
		'incidencia'    => $incidencia['tipo'],
		'tiempo'        => $incidencia['tiempo'],
		);
	return $res;
}

function validar_e_s($reg)
{
	$num = count($reg);
	$i=$j=0;
	$itera = TRUE;
	while ($itera == TRUE)
	{
		$res = get_reg($reg,$j);
		$datos[$i] = $res[0];
		$j = $res[1];
		$itera = ( $j < $num ) ? TRUE : FALSE;
		$i++;
	}
	return $datos;
}

function get_reg($reg,$i)
{
	if ($reg[$i]['tipo_registro'] == 'Entrada')
	{
		$dat_sal = busc_sal($reg,$i+1,1);
		$salida = (empty($dat_sal[0])) ? array("00:00:00",0,"0000-00-00") : array($dat_sal[0],1,$dat_sal[2]);
		$result = array('ficha' => $reg[$i]['ficha'],'entrada' => $reg[$i]['hora'],'fec_ent' => $reg[$i]['fecha'],'salida' => $salida[0],'fec_sal' => $salida[2], 'tiempo' => cant_horas($reg[$i]['hora'],$salida[0]),'turno_id' => $reg[$i]['turno_id'],'id_archivo' => $reg[$i]['id_archivo'],'estado' => $salida[1]);
		$sig_ind = ($dat_sal[1] == 0) ? $i+1 : $dat_sal[1];
		return array($result,$sig_ind);
	}else{
		$result = array('ficha' => $reg[$i]['ficha'],'entrada' => "00:00:00",'fec_ent' => "0000-00-00",'salida' => $reg[$i]['hora'],'fec_sal' => $reg[$i]['fecha'], 'tiempo' => cant_horas("00:00:00",$reg[$i]['hora']),'turno_id' => $reg[$i]['turno_id'],'id_archivo' => $reg[$i]['id_archivo']);
		return array($result,$i+1);
	}
}

function buscar_params($tur)
{
	if ($tur['tipo'] == 1)
	{
		$params = array("18:00:00","21:00:00");
	}elseif($tur['tipo'] == 2)
	{
		$params = array("06:00:00","09:00:00");
	}elseif($tur['tipo'] == 3)
	{
		$p1 = date("H:i:s",strtotime("00:00:00")+strtotime($tur['salida'])+strtotime("01:00:00") );
		$p2 = date("H:i:s",strtotime("00:00:00")+strtotime($tur['salida'])+strtotime("04:00:00") );
		//$params = array( date("H:i:s",strtotime("00:00:00")+strtotime($p2) ), date("H:i:s",strtotime("00:00:00")+strtotime($p2)) );
		$params = array("19:00:00","22:00:00");
	}
	return $params;
}

function hora_seg($hora)
{
    $parse = explode(":", $hora);
    $h = (int) $parse[0] * 3600 + (int) $parse[1] * 60 + (int) $parse[2];    
    return $h;
}

function conversorSegundosHoras($tiempo_en_segundos)
{
    $horas = floor($tiempo_en_segundos / 3600);
    $minutos = floor(($tiempo_en_segundos - ($horas * 3600)) / 60);
    $segundos = $tiempo_en_segundos - ($horas * 3600) - ($minutos * 60);
    return date("H:i:s",strtotime($horas.':'.$minutos.":".$segundos));
}

function jornada($tipo)
{
	switch ($tipo)
	{
		case 1:
			return "Diurna";
			break;
		case 2:
			return "Nocturna";
			break;
		default:
			return "Mixta";
			break;
	}
}

function horas_extras($entrada,$salida,$turno)
{
    $params  = buscar_params($turno);
	$jornada = jornada($turno['tipo']);
    if (strtotime($salida) > strtotime($turno['tolerancia_salida']))
    {
    	$extra = date("H:i:s",strtotime("00:00:00")+strtotime($salida)-strtotime($turno['salida']));
        
        if ( strtotime($salida) >= strtotime($params[1]) )
        {
            $recargo_25 = conversorSegundosHoras(hora_seg(date("H:i:s",strtotime("00:00:00")+strtotime($params[1])-strtotime($params[0])))/4);
            $recargo_50 = conversorSegundosHoras(hora_seg(date("H:i:s",strtotime("00:00:00")+strtotime($salida)-strtotime($params[1])))/2);
            return array( $extra, $recargo_25, $recargo_50, $jornada );
		}elseif ( (strtotime($salida)>=strtotime($params[0]))&&(strtotime($salida)<strtotime($params[1])) )
		{
			$recargo_25 = conversorSegundosHoras(hora_seg(date("H:i:s",strtotime("00:00:00")+strtotime($salida)-strtotime($params[0])))/4);
			return array( $extra, $recargo_25, "00:00:00", $jornada );
		}elseif ( strtotime($salida) < strtotime($params[0]) )
		{
			return array( $extra, "00:00:00", "00:00:00", $jornada );
		}
    }
    return array( "00:00:00", "00:00:00", "00:00:00", $jornada );
}

function validar_just($ficha,$fecha,$id_incidencia,$conex)
{
	$res = $conex->query("SELECT * FROM caa_justificacion WHERE ficha = '$ficha' AND fecha='$fecha'  AND id_incidencia='$id_incidencia'");
	if ( $dat = mysqli_fetch_array($res) ) {
		return true;
	}else{
		return false;
	}
}
function get_jornada($tipo)
{
	switch ($tipo){
			case 1:
				return "Diurna";
				break;
			case 2:
				return "Nocturna";
				break;
			case 3:
				return "Mixta";
				break;
			default:
				return "Diurna";
				break;
		}
}
?>