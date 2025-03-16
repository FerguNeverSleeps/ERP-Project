<?php 
function get_turno_id($ficha,$fecha,$calendario)
{
	for ($i=0; $i < count($calendario); $i++){
    	if ( ( $ficha == $calendario[$i]['ficha'] ) && ( $fecha == $calendario[$i]['fecha'] ) ){
    		return $calendario[$i]['turno_id'];
    	}
	}
	return 1;
}
function get_turno($turno_id,$turnos)
{
	for ($i=0; $i < count($turnos); $i++){
    	if ( $turno_id == $turnos[$i]['turno_id'] ){
    		return $turnos[$i];
    	}
	}
	return $turnos[0];
}
//==========================================================================
function validar_e_s($reg)
{
	$num = count($reg);
	$i=$j=0;
	$itera = TRUE;
	while ($itera == TRUE){
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
	if ($reg[$i]['tipo_registro'] == 'Entrada'){
		$dat_sal = busc_sal($reg[$i]['ficha'],$reg[$i]['fecha'],$reg,$i+1);
		$salida = (empty($dat_sal[0])) ? array("00:00:00",0,"0000-00-00") : array($dat_sal[0],1,$dat_sal[2]);
		$result = array('ficha' => $reg[$i]['ficha'],'entrada' => $reg[$i]['hora'],'fec_ent' => $reg[$i]['fecha'],'salida' => $salida[0],'fec_sal' => $salida[2], 'tiempo' => cant_horas($reg[$i]['hora'],$salida[0]),'turno_id' => $reg[$i]['turno_id'],'estado' => $salida[1]);
		$sig_ind = ($dat_sal[1] == 0) ? $i+1 : $dat_sal[1];
		return array($result,$sig_ind);
	}else{
		$result = array('ficha' => $reg[$i]['ficha'],'entrada' => "00:00:00",'fec_ent' => "0000-00-00",'salida' => $reg[$i]['hora'],'fec_sal' => $reg[$i]['fecha'], 'tiempo' => cant_horas("00:00:00",$reg[$i]['hora']),'turno_id' => $reg[$i]['turno_id']);
		return array($result,$i+1);
	}
}
function get_mov($ficha,$reg,$i,$tur)
{
	if ( $i > 0 ){
		$ant = $i-1;
		if ( ($reg[$ant]['ficha'] != $ficha) ){
			return validar_entrada($ficha,$reg,$i,$tur);
		}elseif($reg[$ant]['ficha'] == $ficha){
			if( strtotime($reg[$ant]['fecha']) != strtotime($reg[$i]['fecha']) ) {
				return validar_entrada($ficha,$reg,$i,$tur);
			}else{
				if ( ( valTiempo( $reg,$i,$tur ) == FALSE ) ) {
					return validar_entrada($ficha,$reg,$i,$tur);
				}else{
					return "Salida";
				}
			}
		}
	}else{
		return validar_entrada($ficha,$reg,$i,$tur);
	}
}
//==========================================================================
function condicion($reg,$tip,$i)
{
    if ( $i < ( count($reg)-1 ) ) {
    	if ( ($reg[$i]['ficha']==$reg[$i+1]['ficha'])&&($reg[$i]['fecha']==$reg[$i+1]['fecha']) ) {
    		$result = ($reg[$i+1]['tipo_registro']!=$tip) ? TRUE : FALSE;
    	}

    }else{
    	$result=FALSE;	
    }
    return $result;
}
function busc_sal($ficha,$fecha,$reg,$pos)
{
	$res = array("00:00:00",0,"0000-00-00");
	for ($i=$pos; $i < count($reg); $i++) { 
		if( ($reg[$i]['tipo_registro']=='Salida')&&($ficha==$reg[$i]['ficha'])&&($fecha==$reg[$i]['fecha']) ){
			$res = array($reg[$i]['hora'],$i+1,$reg[$i]['fecha']);
		}
	}
	return $res;
}
//==========================================================================
function horas_extras($entrada,$salida,$turno)
{
    $params  = buscar_params($turno);
	$jornada = jornada($turno['tipo']);
    if (strtotime($salida) > strtotime($turno['tolerancia_salida'])){
    	$extra = date("H:i:s",strtotime("00:00:00")+strtotime($salida)-strtotime($turno['salida']));
        
        if ( strtotime($salida) >= strtotime($params[1]) ){
            $recargo_25 = conversorSegundosHoras(hora_seg(date("H:i:s",strtotime("00:00:00")+strtotime($params[1])-strtotime($params[0])))/4);
            $recargo_50 = conversorSegundosHoras(hora_seg(date("H:i:s",strtotime("00:00:00")+strtotime($salida)-strtotime($params[1])))/2);
            return array( $extra, $recargo_25, $recargo_50, $jornada );
		}elseif ( (strtotime($salida)>=strtotime($params[0]))&&(strtotime($salida)<strtotime($params[1])) ){
			$recargo_25 = conversorSegundosHoras(hora_seg(date("H:i:s",strtotime("00:00:00")+strtotime($salida)-strtotime($params[0])))/4);
			return array( $extra, $recargo_25, "00:00:00", $jornada );
		}elseif ( strtotime($salida) < strtotime($params[0]) ){
			return array( $extra, "00:00:00", "00:00:00", $jornada );
		}
    }
    return array( "00:00:00", "00:00:00", "00:00:00", $jornada );
}
function tardanza($entrada,$turno)
{
	if (strtotime($entrada) > strtotime($turno['tolerancia_entrada'])){
    	return date("H:i:s",strtotime("00:00:00")+strtotime($entrada)-strtotime($turno['entrada']));
    }else{
    	return date("H:i:s",strtotime("00:00:00"));
    }
}
//==========================================================================
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
function buscar_params($tur)
{
	if ($tur['tipo'] == 1){
		$params = array("18:00:00","21:00:00");
	}elseif($tur['tipo'] == 2){
		$params = array("06:00:00","09:00:00");
	}elseif($tur['tipo'] == 3){
		$p1 = date("H:i:s",strtotime("00:00:00")+strtotime($tur['salida'])+strtotime("01:00:00") );
		$p2 = date("H:i:s",strtotime("00:00:00")+strtotime($tur['salida'])+strtotime("04:00:00") );
		$params = array("19:00:00","22:00:00");
	}
	return $params;
}
function jornada($tipo)
{
	switch ($tipo){
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
	if( (isset($reg[$i-1]['fecha']))&&(isset($reg[$i]['fecha']))&&( strtotime($reg[$i-1]['fecha'])!=strtotime($reg[$i]['fecha'])) ) {
		return true;
	}else{
		return false;
	}
}
function validar_entrada($ficha,$reg,$i,$tur)
{
	if ( strtotime( $reg[$i]['hora'] ) < strtotime( $tur['salida'] ) ) {
		return "Entrada";
	}else{
		return "Salida";
	}
}
function valTiempo( $reg,$i,$tur ){
	if ( strtotime( $reg[$i]['hora'] ) > ( strtotime( '+30 minute' , strtotime ($reg[$i-1]['hora']) ) ) ) {
		return TRUE;
	}else{
		return FALSE;
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