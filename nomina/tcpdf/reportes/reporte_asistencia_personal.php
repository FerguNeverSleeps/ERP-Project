<?php 
if (!isset($_SESSION)) {
  session_start();
}
require('fpdf.php');
require_once '../../lib/config.php';
require_once '../../lib/common.php';
$reg=$_REQUEST['reg'];
$conexion=conexion();
        
    $sql1 = "SELECT DISTINCT np.codnivel1, nn.descrip as departamento
         FROM   nompersonal np
         INNER JOIN nomnivel1 nn ON nn.codorg=np.codnivel1
         ORDER BY np.codnivel1";

    $res1=query($sql1, $conexion);
    $html=" <table width='650' border='1' align='center' cellpadding='1' cellspacing='0'>";

    while($row=fetch_array($res1))
    {
   
            $codnivel1=$row['codnivel1'];
            $departamento=$row['departamento'];
            $Row=array('UNIDAD',$departamento);
            
            $sql = "SELECT DISTINCT np.ficha, np.apenom as nombre
                                FROM   reloj_detalle rd, nompersonal np
                                WHERE   rd.ficha=np.ficha  AND rd.id_encabezado=".$reg." AND np.codnivel1=".$codnivel1." ORDER BY np.apenom";
            $res=query($sql, $conexion);
            
             while($fila=fetch_array($res))
            {
                    $ficha=$fila['ficha'];
                    $consulta="SELECT
                     reloj_encabezado.cod_enca,
                     reloj_encabezado.fecha_reg,
                     reloj_encabezado.fecha_ini,
                     reloj_encabezado.fecha_fin,
                     reloj_detalle.id,
                     reloj_detalle.id_encabezado,
                     reloj_detalle.ficha,
                     reloj_detalle.fecha,
                     nomturnos.turno_id,
                     nomturnos.descripcion,
                     nomturnos.entrada,
                     nomturnos.tolerancia_entrada,
                     nomturnos.inicio_descanso,
                     nomturnos.salida_descanso,
                     nomturnos.tolerancia_descanso,
                     nomturnos.salida,
                     nomturnos.tolerancia_salida,
                     nompersonal.apenom,
                     reloj_detalle.entrada,
                     reloj_detalle.salmuerzo,
                     reloj_detalle.ealmuerzo,
                     reloj_detalle.salida,
                     reloj_detalle.ordinaria,
                     reloj_detalle.extra,
                     reloj_detalle.extraext,
                     reloj_detalle.extranoc,
                     reloj_detalle.extraextnoc,
                     reloj_detalle.domingo,
                     reloj_detalle.nacional,
                     reloj_detalle.emergencia,
                     reloj_detalle.descansoincompleto,
                     reloj_detalle.tardanza
                FROM
                     nomturnos INNER JOIN nompersonal ON nomturnos.turno_id = nompersonal.turno_id
                     INNER JOIN reloj_detalle ON nompersonal.ficha = reloj_detalle.ficha
                     INNER JOIN reloj_encabezado ON reloj_detalle.id_encabezado = reloj_encabezado.cod_enca
                WHERE
                     reloj_encabezado.cod_enca = '$reg' AND reloj_detalle.ficha ='$ficha'
                ORDER BY
                     ficha ASC,
                     fecha ASC";
                        $query=query($consulta,$conexion);

                        $fichaaux="";
                        $fechaaux="";
                        $apenomaux="";
                        $i=0;
                        $j=0;
                        while($fila=fetch_array($query))
                        {
                    $date = new DateTime($fila[fecha]);
                    $fecha = $date->format('Y-m-d');
                    $hora = $date->format('H:i');
                    $ficha = $fila[ficha];

                    if($i==0)
                    {
                            
                    	$nombreficha="</td>".$fila[fila]." ". $fila[apenom]."</td>";

                            $fichaaux=$fila[ficha];
                            $fechaaux=$fecha;
                            $apenomaux=$fila[apenom];
                    }
                    if($ficha!=$fichaaux)
                    {
                        //TOTALES
                      
                    	$nombreficha="</td>".$fila[fila]." ". $fila[apenom]."</td>";
                    	echo $fila[ficha]." ". $fila[apenom]."<br>";

                        $fichaaux=$ficha;

                        $apenomaux=$fila[apenom];

                    }
                	if($fila[entrada] == '00:00'){
                		$cabecera .= "";
                		$cuerpo .= "";
                	}
                	else
                	{
                		$cabecera .= "
                		<td>Entrada</td>";
                		$cuerpo .= "<td>".$fila[entrada]."</td><br>
                		";
                	}
                	if($fila[salmuerzo] == '00:00'){
                		$cabecera .= "";
                		$cuerpo .= "";
                	}
                	else
                	{
                		$cabecera .= "<td>S. Almuerzo</td>";
                		$cuerpo .= "<td>".$fila[salmuerzo]."</td><br>
                		";
                	}
                    if($fila[ealmuerzo] == '00:00'){
                		$cabecera .= "";
                		$cuerpo .= "";
                	}
                	else
                	{
                		$cabecera .= "<td>E. Almuerzo</td>";
                		$cuerpo .= "<td>".$fila[ealmuerzo]."</td><br>
                		";
                	}
                	if($fila[salida] == '00:00'){
                		$cabecera .= "";
                		$cuerpo .= "";
                	}
                	else
                	{
                		$cabecera .= "<td>Salida</td>";
                		$cuerpo .= "<td>".$fila[salida]."</td><br>
                		";
                	}
                	if($fila[domingo] == '00:00'){
                		$cabecera .= "";
                		$cuerpo .= "";
                	}
                	else
                	{
                		$cabecera .= "<td>Domingos</td>";
                		$cuerpo .= "<td>".$fila[domingo]."</td><br>
                		";
                	}
                    if($fila[nacional] == '00:00'){
                		$cabecera .= "";
                		$cuerpo .= "";
                	}
                	else
                	{
                		$cabecera .= "<td>E. Almuerzo</td>";
                		$cuerpo .= "<td>".$fila[nacional]."</td><br>
                		";
                	}
                	if($fila[tardanza] == '00:00'){
                		$cabecera .= "";
                		$cuerpo .= "";
                	}
                	else
                	{
                		$cabecera .= "<td>Tardanza</td>";
                		$cuerpo .= "<td>".$fila[tardanza]."</td><br>
                		";
                	}
                	if($fila[emergencia] == '00:00'){
                		$cabecera .= "";
                		$cuerpo .= "";
                	}
                	else
                	{
                		$cabecera .= "<td>Emergencia</td>";
                		$cuerpo .= "<td>".$fila[emergencia]."</td><br>
                		";
                	}
                	if($fila[descansoincompleto] == '00:00'){
                		$cabecera .= "";
                		$cuerpo .= "";
                	}
                	else
                	{
                		$cabecera .= "<td>Descanso Incompleto</td><br>";
                		$cuerpo .= "<td>".$fila[descansoincompleto]."</td><br>
                		";
                	}
                                //print_r($fila);
                                //$this->Row(array($fecha,$turno,$fila[entrada],$fila[salmuerzo],$fila[ealmuerzo],$fila[salida],$fila[ordinaria],$fila[domingo],$fila[nacional],$fila[tardanza],$fila[emergencia],$fila[descansoincompleto]));
                               /* echo $fecha." ".$turno." ".$fila[entrada]." ".$fila[salmuerzo]." ".$fila[ealmuerzo]." ".
                                $fila[salida]." ".$fila[ordinaria]." ".$fila[domingo]." ".$fila[nacional]." ".$fila[tardanza]." ".
                                $fila[emergencia]." ".$fila[descansoincompleto]."<br>";*/
                                $turno=$entrada=$salmu=$ealmu=$salida=$regular=$ausen=$tardan=$incapac=$sobret=$feriado="";
                                $entradaf=$salmuf=$ealmuf=$salidaf="";
                                

                                $fechaaux=$fecha;
                                $j=0;


                                $turno = $fila[turno_id];


                                $j++;
                                $i++;
                        }
				    		$html.="
				    		<tr>".$nombreficha."</tr>";
				    		$html.="<tr>
				    			<td>Fecha</td>
				    			<td>Turno</td>
				    		".$cabecera."
				    		</tr>
				    		
				    			<td>".$fecha."</td>".
				    			"<td>".$turno."</td>".
				    			$cuerpo.
				    		"</tr>";
				    	
            }	

    }
    $html.="</table>";
	echo $html;

/*
    $html=" <table width='650' border='1' align='center' cellpadding='1' cellspacing='0'>
    		<tr>
    			<td>Fecha</td>
    			<td>Turno</td>
    		".$cabecera."
    		</tr>
    		
    			<td>".$fecha."</td>".
    			"<td>".$turno."</td>".
    			$cuerpo.
    		"</tr>
    		</table>
    	";
    echo $html;*/
    


