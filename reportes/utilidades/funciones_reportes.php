<?php
//------------------------------------------------------------
function sumeDiaFecha($fecha,$operacion)
{
	return date( 'Y-m-d' , strtotime( $operacion , strtotime( $fecha ) ) );
}
//------------------------------------------------------------
function tipo_incidencia($ficha,$fecha,$inc)
{
	for ($i=0; $i < count($inc); $i++)
	{
		if ( ($ficha == $inc[$i]['ficha']) && ($fecha == $inc[$i]['fecha']) )
		{
			return array($inc[$i]['acronimo'],$inc[$i]['codigo']);
		}
	}
	return array("","");
}
function cuentaInc($cuenta,$inc)
{
	if (!empty($inc[1]))
	{
		switch ($inc[1])
		{
			case 1:
				$cuenta[1] = $cuenta[1] + 1;
				break;
			case 2:
				$cuenta[2] = $cuenta[2] + 1;
				break;
			case 3:
				$cuenta[3] = $cuenta[3] + 1;
				break;
			case 4:
				$cuenta[4] = $cuenta[4] + 1;
				break;
			case 9:
				$cuenta[5] = $cuenta[5] + 1;
				break;
			default:
				break;
		}
	}
	return $cuenta;
}
//------------------------------------------------------------
function getDia($fecha,$opc){
	$txt = ($opc == 1) ? "first day of this month" : "last day of this month" ;
	$fecha = new DateTime($fecha);
	return $fecha->modify($txt)->format('Y-m-d');
}
//------------------------------------------------------------
function cargaIncidencias( $ficha, $fecha, $db )
{
	$res = $db->query("SELECT * FROM caa_incidencias_empleados a 
						LEFT JOIN caa_incidencias b ON a.id_incidencia = b.id 
						WHERE a.ficha = '$ficha' AND a.fecha = '$fecha'
						ORDER BY a.fecha");
	$i=0;
	if ( mysqli_num_rows($res) != 0 ) {
		while ( $row = mysqli_fetch_array($res) ) {
			if ($i==0) {
				$inc .= $row['acronimo'];
			}else{
				$inc .= "/".$row['acronimo'];				
			}
			$i++;
		}
	}else{
		$inc = "";
	}
	return $inc;
}
//------------------------------------------------------------
function getIncidencias( $ficha, $fecha, $inc )
{
	$res = "s";
	for ($i=0; $i < count($inc); $i++) {
		if ( ( $ficha == $inc[$i]['ficha'] )&&( $ficha == $inc[$i]['fecha'] ) ){
			$res .= $inc[$i]['acronimo'];
		}
	}
	return $res;
}
?>