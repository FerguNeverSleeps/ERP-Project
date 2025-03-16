<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); /*// Turn on output buffering
?>
<?php include_once "ewcfg11.php" ?>
<?php include_once "ewmysql11.php" ?>
<?php include_once "phpfn11.php" ?>
<?php include_once "vig_asistencia_info.php" ?>
<?php include_once "vig_usuarios_info.php" ?>
<?php include_once "userfn11.php" ?>
<?php
if (!isset($conn)) $conn = ew_Connect();
if (!isset($GLOBALS["asi_usuarios"])) $GLOBALS["asi_usuarios"] = new cvig_usuarios;
$Security = new cAdvancedSecurity();
if (!$Security->IsLoggedIn()) $Security->AutoLogin();
if (!$Security->IsLoggedIn()) header("Location: login.php");


include_once("header.php"); */
function horastrabajadas($ficha, $fechaini, $fechafin, $opc)
{

	$limiteExtrasOrd    =18;
	$limiteExtrasOrdMin =$limiteExtrasOrd*60;
	if($opc==1)
		$consulta = "SELECT SUM(horas_trabajadas_num) AS horas_trabajadas_num FROM vig_asistencia WHERE id_empleado='$ficha'";
	elseif($opc==2)
		$consulta = "SELECT SUM(horas_extra_num) AS horas_extra_num FROM vig_asistencia WHERE id_empleado='$ficha'";
	elseif($opc==3)
		$consulta = "SELECT SUM(horas_extras_signo) AS horas_extras_signo FROM vig_asistencia WHERE id_empleado='$ficha'";
		elseif($opc==4)
		$consulta = "SELECT SUM(tardanza) AS tardanza FROM vig_asistencia WHERE id_empleado='$ficha'";
	elseif($opc==5)
		$consulta = "SELECT SUM(inasistencia) AS inasistencia FROM vig_asistencia WHERE id_empleado='$ficha'";

	$fetch    = $conn->GetArray($SQL);
	$horas    = (($fetch[horas_trabajadas_num]/60) == '' ? 0 : ($fetch[horas_trabajadas_num]/60));
	$horasmin = ($fetch[horas_trabajadas_num] == '' ? 0 : $fetch[horas_trabajadas_num]);

	if($opc==1)
	{
		$query       = "select ifnull(count(dia_fiesta),0) as dias from nomcalendarios_tiposnomina where fecha BETWEEN '$fechaini' and '$fechafin'  and dia_fiesta='3'";
		$result      = query($query,$conexion);
		$fetch       = fetch_array($result);
		$dias        = $fetch[dias] * 480;
		
		$consulta    = "SELECT SUM(horas_trabajadas_num) AS horas_trabajadas_num FROM vig_asistencia WHERE concepto='nacional' AND id_empleado='$ficha'";
		$resultado   = query($consulta, $conexion);
		$fetch       = fetch_array($resultado);
		$horasminnac = ($fetch[horas_trabajadas_num] == '' ? 0 : $fetch[horas_trabajadas_num]);
		
		
		$horas       = ($horasmin + $dias)/60;
	}

	if($opc       ==2)
	{
		$consulta     = "SELECT fecha, horas_extra_num  FROM `vig_asistencia` WHERE id_empleado='$ficha'   group by fecha order by fecha  ";
		$resultado    = query($consulta, $conexion);
		$extranoc     = $extra = $total = 0;
		while($fetch2 = fetch_array($resultado))
		{
			$extra        += $fetch2[horas_extra_num];
			$total        += $fetch2[horas_extra_num];
				
			if(($total/60)>$limiteExtrasOrd)
			{
			$horas        =$extra/60;
			break;
			}
		}
	}

	if($opc==3)
	{
		$consulta     = "SELECT fecha, horas_extras_signo  FROM `vig_asistencia` WHERE id_empleado='$ficha'   group by fecha order by fecha  ";
		$resultado    = query($consulta, $conexion);
		$i            = $extranoc = $extra = $total = 0;
		while($fetch2 = fetch_array($resultado))
			{
			$extra    += $fetch2[horas_extras_signo];
			$total    += $fetch2[horas_extras_signo];
	       if(($total/60)>$limiteExtrasOrd) 
	        {
	            if($i == 1)
	                $horasmin += $fetch2[horas_extras_signo];
	            $i = 1;
	        }
	    }
	    $horas = $horasmin/60;
	}

	if($opc==5)
	{
		$consulta     = "SELECT fecha, inasistencia  FROM `vig_asistencia` WHERE id_empleado='$ficha'   group by fecha order by fecha  ";
		$resultado    = query($consulta, $conexion);
		$extranoc     = $extra = $total = 0;
		while($fetch2 = fetch_array($resultado))
	    {
			$inasistencia += $fetch2[inasistencia];
			$total        += $fetch2[inasistencia];

	    }
	}

	if($opc==4)
	{
		$consulta  = "SELECT fecha, tardanza  FROM `vig_asistencia` WHERE id_empleado='$ficha'   group by fecha order by fecha  ";
		$resultado = query($consulta, $conexion);
		$j         = $i = $extranoc = $extra = $total = 0;
	    while($fetch2 = fetch_array($resultado))
	    {
			$tardanza += $fetch2[tardanza];
			$total    += $fetch2[tardanza]+$fetch2[extranoc];

	        
	    }
	    $horas = $horasmin/60;
	}



	return number_format($horas,2,'.','');
	break;
 }

function horastrabajadas2($ficha, $fechaini, $fechafin, $opc)
{

$limiteExtrasOrd=18;
            $limiteExtrasOrdMin=$limiteExtrasOrd*60;
            if($opc==1)
            	$consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='ordinarias' AND ficha='$ficha'";
            elseif($opc==2)
            	$consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='extras' AND ficha='$ficha'";
            elseif($opc==3)
            	$consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='domingos' AND ficha='$ficha'";
          	elseif($opc==4)
            	$consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='extrasext' AND ficha='$ficha'";
            elseif($opc==5)
            	$consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='extrasnoc' AND ficha='$ficha'";
            elseif($opc==6)
            	$consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='extrasextnoc' AND ficha='$ficha'";
            elseif($opc==7)
            	$consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='extrasdom' AND ficha='$ficha'";
            elseif($opc==8)
            	$consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='extrasnocdom' AND ficha='$ficha'";
            elseif($opc==9)
            	$consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='tardanza' AND ficha='$ficha'";
            elseif($opc==10)
                $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='nacional' AND ficha='$ficha'";
            elseif($opc==11)
                $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='extranac' AND ficha='$ficha'";
            elseif($opc==12)
                $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='extranocnac' AND ficha='$ficha'";
            $resultado = query($consulta, $conexion);
            $fetch = fetch_array($resultado);
            $horas = (($fetch[minutos]/60) == '' ? 0 : ($fetch[minutos]/60));
            $horasmin = ($fetch[minutos] == '' ? 0 : $fetch[minutos]);

            if($opc==1)
            {
                $query = "select ifnull(count(dia_fiesta),0) as dias from nomcalendarios_tiposnomina where fecha BETWEEN '$fechaini' and '$fechafin'  and dia_fiesta='3'";
                $result = query($query,$conexion);
                $fetch = fetch_array($result);
                $dias = $fetch[dias] * 480;

                $consulta = "SELECT SUM(minutos) AS minutos FROM reloj_procesar WHERE concepto='nacional' AND ficha='$ficha'";
                $resultado = query($consulta, $conexion);
                $fetch = fetch_array($resultado);
                $horasminnac = ($fetch[minutos] == '' ? 0 : $fetch[minutos]);

                
                //$horas = ($horasmin + $dias - $horasminnac)/60;
                $horas = ($horasmin + $dias)/60;
            }

            if($opc==2)
            {
                $consulta = "SELECT fecha, sum((case when concepto='extras' then minutos else 0 end)) as extra, sum((case when concepto='extrasnoc' then minutos else 0 end)) as extranoc  FROM `reloj_procesar` WHERE ficha='$ficha'   group by fecha order by fecha  ";
                $resultado = query($consulta, $conexion);
                $extranoc = $extra = $total = 0;
                while($fetch2 = fetch_array($resultado))
                {
                    $extra += $fetch2[extra];
                    $extranoc += $fetch2[extranoc]; 
                    $total += $fetch2[extra]+$fetch2[extranoc];

                    if(($total/60)>$limiteExtrasOrd)
            		{
            			$horas=$extra/60;
                        break;
            		}
                }
            }

        	if($opc==4)
            {
            	$consulta = "SELECT fecha, sum((case when concepto='extras' then minutos else 0 end)) as extra, sum((case when concepto='extrasnoc' then minutos else 0 end)) as extranoc  FROM `reloj_procesar` WHERE ficha='$ficha'   group by fecha order by fecha  ";
                $resultado = query($consulta, $conexion);
                $i = $extranoc = $extra = $total = 0;
                while($fetch2 = fetch_array($resultado))
                {
                    $extra += $fetch2[extra];
                    $extranoc += $fetch2[extranoc]; 
                    $total += $fetch2[extra]+$fetch2[extranoc];

                    if(($total/60)>$limiteExtrasOrd) 
                    {
                        if($i == 1)
                            $horasmin += $fetch2[extra];
                        $i = 1;
                    }
                }
                $horas = $horasmin/60;
            }

            if($opc==5)
            {
                $consulta = "SELECT fecha, sum((case when concepto='extras' then minutos else 0 end)) as extra, sum((case when concepto='extrasnoc' then minutos else 0 end)) as extranoc  FROM `reloj_procesar` WHERE ficha='$ficha'   group by fecha order by fecha  ";
                $resultado = query($consulta, $conexion);
                $extranoc = $extra = $total = 0;
                while($fetch2 = fetch_array($resultado))
                {
                    $extra += $fetch2[extra];
                    $extranoc += $fetch2[extranoc]; 
                    $total += $fetch2[extra]+$fetch2[extranoc];

                    if(($total/60)>$limiteExtrasOrd)
                    {
                        $extranoc -= $total - $limiteExtrasOrdMin;
                        $horas=$extranoc/60;
                        break;
                    }
                }
            }

            if($opc==6)
            {
                $consulta = "SELECT fecha, sum((case when concepto='extras' then minutos else 0 end)) as extra, sum((case when concepto='extrasnoc' then minutos else 0 end)) as extranoc  FROM `reloj_procesar` WHERE ficha='$ficha'   group by fecha order by fecha  ";
                $resultado = query($consulta, $conexion);
                $j = $i = $extranoc = $extra = $total = 0;
                while($fetch2 = fetch_array($resultado))
                {
                    $extra += $fetch2[extra];
                    $extranoc += $fetch2[extranoc]; 
                    $total += $fetch2[extra]+$fetch2[extranoc];

                    if(($total/60)>$limiteExtrasOrd)
                    {
                        if($i == 0)
                        {
                           $horasmin += $total - $limiteExtrasOrdMin;
                           $i = 1;
                        }
                        if($j == 1)
                            $horasmin += $fetch2[extranoc];
                        $j = 1;
                    }
                }
                $horas = $horasmin/60;
            }

            

            return number_format($horas,2,'.','');
 }
?>