<?php
/**
 * Creates an example PDF TEST document using TCPDF
 * @package com.tecnick.tcpdf
 * @abstract TCPDF - Example: Custom Header and Footer
 * @author Nicola Asuni
 * @since 2008-03-04
 */

//require_once('../../config/lang/eng.php');
require_once('../tcpdf.php');
require_once '../../lib/config.php';
require_once '../../lib/common.php';

$fecha_inicio= fecha_sql($_REQUEST['fecha_inicio']);
$fecha_fin=fecha_sql($_REQUEST['fecha_fin']);
$colaborador=$_REQUEST['colaborador'];
$nivel1=$_REQUEST['nivel1'];

if((!$_REQUEST['fecha_inicio']) || (!$_REQUEST['fecha_fin'])) {
	echo "Por favor seleccione las fechas";
	exit;
}

if((!$_REQUEST['colaborador']) && (!$_REQUEST['nivel1'])) {
	echo "Por favor seleccione el nivel o el colaborador";
	exit;
}

// Extend the TCPDF class to create custom Header and Footer
class MYPDF extends TCPDF 
{
    public $fechainicio, $fechafinal, $fecharegistro, $colaborador;
    //Page header
    public function Header() 
    {
		// Logo
		$Conn=conexion();
		$var_sql="select * from nomempresa";
		$rs = query($var_sql,$Conn);
		$row_rs = fetch_array($rs);
		$var_encabezado1= utf8_encode($row_rs[nom_emp]);

		$sql = "SELECT np.ficha, 
				np.apenom as nombre, 
				nc.des_car as cargo,
				nn1.descrip AS nivel
			FROM nompersonal np, nomcargos nc, nomnivel1 nn1
			WHERE np.ficha = ".$this->colaborador." 
			AND nc.cod_car = np.codcargo 
			AND nn1.codorg = np.codnivel1 
			ORDER BY np.apenom";
		$res=query($sql, $Conn);
		$datosPersonal = fetch_array($res);

		$this->SetFont('helvetica', 'BI', 12);
		$html='<table width="1024" border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td colspan="1" width="25%" valign="middle" align="left" style="border-top-color:#FFF;border-bottom-color:#FFF;border-left-color:#FFF;border-right-color:#FFF;">
					
				</td>
				<td colspan="3" width="50%" valign="middle" align="center" style="border-top-color:#FFF;border-bottom-color:#FFF;border-left-color:#FFF;border-right-color:#FFF;font-size:12px">
					'.$var_encabezado1.'
				</td>
				<td colspan="1" width="25%" valign="middle" align="center" style="border-top-color:#FFF;border-bottom-color:#FFF;border-left-color:#FFF;border-right-color:#FFF;">
					
				</td>
			</tr>
			<tr>
				<td colspan="1" width="25%" valign="middle" align="left" style="border-top-color:#FFF;border-bottom-color:#FFF;border-left-color:#FFF;border-right-color:#FFF;">
					
				</td>
				<td colspan="3" width="50%" valign="middle" align="center" style="border-top-color:#FFF;border-bottom-color:#FFF;border-left-color:#FFF;border-right-color:#FFF;font-size:10px">
					Hoja de Tiempo
				</td>
				<td colspan="1" width="25%" valign="middle" align="center" style="border-top-color:#FFF;border-bottom-color:#FFF;border-left-color:#FFF;border-right-color:#FFF;">
					
				</td>
			</tr>
            <tr>
				<td colspan="1" width="25%" valign="middle" align="left" style="border-top-color:#FFF;border-bottom-color:#FFF;border-left-color:#FFF;border-right-color:#FFF;">
				</td>
				<td colspan="3" width="50%" valign="middle" align="center" style="border-top-color:#FFF;border-bottom-color:#FFF;border-left-color:#FFF;border-right-color:#FFF;font-size:10px">
					Desde: '.$this->fechainicio.' Hasta: '.$this->fechafinal.'
				</td>
				<td colspan="1" width="25%" valign="middle" align="center" style="border-top-color:#FFF;border-bottom-color:#FFF;border-left-color:#FFF;border-right-color:#FFF;">
				</td>
			</tr>
			<tr>
				<td colspan="1" width="25%" valign="middle" align="left" style="border-top-color:#FFF;border-bottom-color:#FFF;border-left-color:#FFF;border-right-color:#FFF;">
				</td>
				<td colspan="3" width="50%" valign="middle" align="center" style="border-top-color:#FFF;border-bottom-color:#FFF;border-left-color:#FFF;border-right-color:#FFF;font-size:10px">
					Empleado: '.utf8_encode($datosPersonal["nombre"]).'
				</td>
				<td colspan="1" width="25%" valign="middle" align="center" style="border-top-color:#FFF;border-bottom-color:#FFF;border-left-color:#FFF;border-right-color:#FFF;">
				</td>
			</tr>
			<tr>
				<td colspan="1" width="25%" valign="middle" align="left" style="border-top-color:#FFF;border-bottom-color:#FFF;border-left-color:#FFF;border-right-color:#FFF;">
				</td>
				<td colspan="3" width="50%" valign="middle" align="center" style="border-top-color:#FFF;border-bottom-color:#FFF;border-left-color:#FFF;border-right-color:#FFF;font-size:10px">
					Número de empleado: '.$this->colaborador.'
				</td>
				<td colspan="1" width="25%" valign="middle" align="center" style="border-top-color:#FFF;border-bottom-color:#FFF;border-left-color:#FFF;border-right-color:#FFF;">
				</td>
			</tr>
			<tr>
				<td colspan="1" width="25%" valign="middle" align="left" style="border-top-color:#FFF;border-bottom-color:#FFF;border-left-color:#FFF;border-right-color:#FFF;">
				</td>
				<td colspan="3" width="50%" valign="middle" align="center" style="border-top-color:#FFF;border-bottom-color:#FFF;border-left-color:#FFF;border-right-color:#FFF;font-size:10px">
					Nivel 1: '.$datosPersonal["nivel"].'
				</td>
				<td colspan="1" width="25%" valign="middle" align="center" style="border-top-color:#FFF;border-bottom-color:#FFF;border-left-color:#FFF;border-right-color:#FFF;">
				</td>
			</tr>
        </table>
        <BR />';
		$html .= '<table width="1024" border="1" cellpadding="0" cellspacing="0">
            <tr>
				<td  valign="middle" align="center" style="width:150px">
					Fecha
				</td>
				<td  valign="middle" align="left" style="width:200px">
					 Marcaciones
				</td>
				<td  valign="middle" align="center" style="width:100px">
					Trab
				</td>
				<td  valign="middle" align="center" style="width:100px">
					Tard
				</td>
				<td  valign="middle" align="center" style="width:100px">
					Aus
				</td>
				<td  valign="middle" align="center" style="width:100px">
					Inci
				</td>
				<td  valign="middle" align="center" style="width:100px">
					Extra
				</td>
				<td  valign="middle" align="center" style="width:100px">
					Aprob
				</td>
			</tr>
			
        </table>
        <BR />';
        $this->writeHTML($html);		
    }

	
    function CambiarFecha($fecha)
	{
		if($fecha!='')
		{
		$otra_fecha= explode("-", $fecha);// en la posición 1 del arreglo se encuentra el mes en texto.. lo comparamos y cambiamos
	  //$buena= $otra_fecha[0]."/".$otra_fecha[1]."/".$otra_fecha[2];// volvemos a armar la fecha

		$buena= $otra_fecha[2]."-".$otra_fecha[1]."-".$otra_fecha[0];// volvemos a armar la fecha
	  	return $buena;
	  }
	}

	public function permisos($cedula, $fecha) 
	{
		$conexion = conexion();
		$sql = "SELECT horas,
				minutos,
				duracion
				FROM expediente 
				WHERE cedula = '$cedula'
				AND fecha = '$fecha' 
				AND tipo = 4 
				AND subtipo IN (133,17,18,19,20,21)";
		$query = query($sql,$conexion);
		if($fetch = fetch_array($query))
		{
			$fetch['duracion_formated'] = gmdate('H:i', floor($fetch['duracion'] * 3600));
			return $fetch;
		}
		return false;
	}
       
    public function CuerpoTablaHtml($fecha_inicio,$fecha_fin)
    {
		$conexion=conexion();
		
		$Y = 48;
		$this->SetTopMargin($Y);

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
			nompersonal.cedula,
			reloj_detalle.entrada,
			reloj_detalle.salmuerzo,
			reloj_detalle.ealmuerzo,
			reloj_detalle.salida,
			reloj_detalle.ordinaria,
			reloj_detalle.dialibre,
			reloj_detalle.ent_emer,
			reloj_detalle.sal_emer,
			reloj_detalle.salida_diasiguiente,
			reloj_detalle.extra,
			reloj_detalle.extraext,
			reloj_detalle.extranoc,
			reloj_detalle.extraextnoc,
			reloj_detalle.mixtodiurna,
			reloj_detalle.mixtonoc,
			reloj_detalle.mixtoextdiurna,
			reloj_detalle.mixtoextnoc,
			reloj_detalle.observacion,
			reloj_detalle.domingo,
			reloj_detalle.descextra1,
			reloj_detalle.nacional,
			reloj_detalle.emergencia,
			reloj_detalle.descansoincompleto,
			reloj_detalle.tardanza,
			SUBSTR(SEC_TO_TIME((TIME_TO_SEC(CAST(extra AS TIME)) + TIME_TO_SEC(CAST(extraext AS TIME)) + TIME_TO_SEC(CAST(extranoc AS TIME)) + TIME_TO_SEC(CAST(extraextnoc AS TIME)) + TIME_TO_SEC(CAST(mixtodiurna AS TIME)) + TIME_TO_SEC(CAST(mixtonoc AS TIME)) + TIME_TO_SEC(CAST(mixtoextdiurna AS TIME)) + TIME_TO_SEC(CAST(mixtoextnoc AS TIME)))),1,5) AS total_extras,
			SUBSTR(SEC_TO_TIME((TIME_TO_SEC(CAST(ordinaria AS TIME)) + TIME_TO_SEC(CAST(domingo AS TIME)) + TIME_TO_SEC(CAST(nacional AS TIME)))),1,5) AS total_trabajadas
		FROM
			nomturnos 
			INNER JOIN nompersonal ON nomturnos.turno_id = nompersonal.turno_id
			LEFT JOIN reloj_detalle ON nompersonal.ficha = reloj_detalle.ficha
			INNER JOIN reloj_encabezado ON reloj_detalle.id_encabezado = reloj_encabezado.cod_enca
		WHERE
				(reloj_detalle.fecha >= '$fecha_inicio' AND reloj_detalle.fecha <= '$fecha_fin')
				AND reloj_detalle.ficha = ".$this->colaborador."
		ORDER BY
				ficha ASC,
				fecha ASC";
		$query=query($consulta,$conexion);

		$html = '<table width="1024" border="0" cellpadding="0" cellspacing="0">';
		$dia = ['', 'LUN', 'MAR', 'MIE', 'JUE', 'VIE', 'SAB', 'DOM'];
		$fechaIni = new DateTime($fecha_inicio);
		$totalAus = 0;
		$totalInci = 0;
		while($fila=fetch_array($query))
		{

			$date = new DateTime($fila["fecha"]);
			$fecha = $date->format('d-m-Y');
			$diaLetras = $dia[$date->format('N')];
			$ficha = $fila["ficha"];

			$permisosArr = $this->permisos($fila['cedula'], $fechaIni->format('Y-m-d'));
			if($permisosArr) 
			{
				$permisosArrFormated = $permisosArr['duracion_formated'];
				$totalInci += $permisosArr['duracion'];
			} 
			else 
			{
				$permisosArrFormated = '00:00';
			}
			
			if($fechaIni->format('d-m-Y') == $fecha) 
			{
				$html .= '<tr>
						<td  valign="middle" align="left" style="width:150px">
							'.$fecha.' - '.$diaLetras.'
						</td>
						<td  valign="middle" align="left" style="width:200px">
							'.$fila["entrada"].' - '.$fila["salida"].'
						</td>
						<td  valign="middle" align="center" style="width:100px">
							'.$fila["total_trabajadas"].'
						</td>
						<td  valign="middle" align="center" style="width:100px">
							'.$fila["tardanza"].'
						</td>
						<td  valign="middle" align="center" style="width:100px">
							00:00
						</td>
						<td  valign="middle" align="center" style="width:100px">
							'.$permisosArrFormated.'
						</td>
						<td  valign="middle" align="center" style="width:100px">
							'.$fila["total_extras"].'
						</td>
						<td  valign="middle" align="center" style="width:100px">
							'.$fila["total_extras"].'
						</td>
					</tr>';
					$fechaIni->add(new DateInterval('P1D'));
			}
			else
			{
				do
				{
					$permisosArr = $this->permisos($fila['cedula'], $fechaIni->format('Y-m-d'));
					$permisosArr = $permisosArr ? $permisosArr['duracion_formated'] : '00:00';

					$diaLetras = $dia[$fechaIni->format('N')];
					$fechaMos = $fechaIni->format('d-m-Y');
					$sql = "SELECT turno_id AS turno
							FROM nomcalendarios_personal 
							WHERE fecha = '".$fechaIni->format('Y-m-d')."' 
							AND ficha='".$ficha."'";
					$query2 = query($sql,$conexion);
					$turno = fetch_array($query2);
					if($turno["turno"] <> 11)
					{
						$horaMos = "08:00";
						$totalAus += 8;
						$text = "AUSENCIA";
					}
					else
					{
						$horaMos = "00:00";
						$text = "LIBRE";
					}
					$html .= '<tr>
							<td  valign="middle" align="left" style="width:150px">
								'.$fechaMos.' - '.$diaLetras.'
							</td>
							<td  valign="middle" align="left" style="width:200px">
								'.$text.'
							</td>
							<td  valign="middle" align="center" style="width:100px">
								00:00
							</td>
							<td  valign="middle" align="center" style="width:100px">
								00:00
							</td>
							<td  valign="middle" align="center" style="width:100px">
								'.$horaMos.'
							</td>
							<td  valign="middle" align="center" style="width:100px">
								'.$permisosArrFormated.'
							</td>
							<td  valign="middle" align="center" style="width:100px">
								00:00
							</td>
							<td  valign="middle" align="center" style="width:100px">
								00:00
							</td>
						</tr>';
					$fechaIni->add(new DateInterval('P1D'));
				}
				while($fechaIni->format('d-m-Y') != $fecha);

				$permisosArr = $this->permisos($fila['cedula'], $fechaIni->format('Y-m-d'));
				$permisosArr = $permisosArr ? $permisosArr['duracion_formated'] : '00:00';

				$diaLetras = $dia[$fechaIni->format('N')];
				$html .= '<tr>
						<td  valign="middle" align="left" style="width:150px">
							'.$fecha.' - '.$diaLetras.'
						</td>
						<td  valign="middle" align="left" style="width:200px">
							'.$fila["entrada"].' - '.$fila["salida"].'
						</td>
						<td  valign="middle" align="center" style="width:100px">
							'.$fila["total_trabajadas"].'
						</td>
						<td  valign="middle" align="center" style="width:100px">
							'.$fila["tardanza"].'
						</td>
						<td  valign="middle" align="center" style="width:100px">
							00:00
						</td>
						<td  valign="middle" align="center" style="width:100px">
							'.$permisosArrFormated.'
						</td>
						<td  valign="middle" align="center" style="width:100px">
							'.$fila["total_extras"].'
						</td>
						<td  valign="middle" align="center" style="width:100px">
							'.$fila["total_extras"].'
						</td>
					</tr>';
				$fechaIni->add(new DateInterval('P1D'));
			}
			$fechaAux = $fila["fecha"];
		}
		if($fechaAux < $fecha_fin)
		{
			$fechaIni = new DateTime($fechaAux);
			$fechaIni->add(new DateInterval('P1D'));
			do
			{
				$permisosArr = $this->permisos($fila['cedula'], $fechaIni->format('Y-m-d'));
				$permisosArr = $permisosArr ? $permisosArr['duracion_formated'] : '00:00';

				$diaLetras = $dia[$fechaIni->format('N')];
				$fechaMos = $fechaIni->format('d-m-Y');
				$sql = "SELECT turno_id AS turno
						FROM nomcalendarios_personal 
						WHERE fecha = '".$fechaIni->format('Y-m-d')."' 
						AND ficha='".$ficha."'";
				$query2 = query($sql,$conexion);
				$turno = fetch_array($query2);
				if($turno["turno"] <> 11)
				{
					$horaMos = "08:00";
					$totalAus += 8;
					$text = "AUSENCIA";
				}
				else
				{
					$horaMos = "00:00";
					$text = "LIBRE";
				}
				$html .= '<tr>
						<td  valign="middle" align="left" style="width:150px">
							'.$fechaMos.' - '.$diaLetras.'
						</td>
						<td  valign="middle" align="left" style="width:200px">
							'.$text.'
						</td>
						<td  valign="middle" align="center" style="width:100px">
							00:00
						</td>
						<td  valign="middle" align="center" style="width:100px">
							00:00
						</td>
						<td  valign="middle" align="center" style="width:100px">
							'.$horaMos.'
						</td>
						<td  valign="middle" align="center" style="width:100px">
							'.$permisosArrFormated.'
						</td>
						<td  valign="middle" align="center" style="width:100px">
							00:00
						</td>
						<td  valign="middle" align="center" style="width:100px">
							00:00
						</td>
					</tr>';
				$fechaIni->add(new DateInterval('P1D'));
			}
			while($fechaIni->format('Y-m-d') <= $fecha_fin);
		}

		$sql = "SELECT SUBSTR(SEC_TO_TIME(SUM(TIME_TO_SEC(CAST(ordinaria AS TIME)) + TIME_TO_SEC(CAST(domingo AS TIME)) + TIME_TO_SEC(CAST(nacional AS TIME)))),1,5) as trabajadas, 
				((SUM(TIME_TO_SEC(CAST(ordinaria AS TIME)) + TIME_TO_SEC(CAST(domingo AS TIME)) + TIME_TO_SEC(CAST(nacional AS TIME))))/60)/60 as trabajadas2,
				SUBSTR(SEC_TO_TIME(SUM(TIME_TO_SEC(CAST(tardanza AS TIME)))),1,5) as tardanza, 
				((SUM(TIME_TO_SEC(CAST(tardanza AS TIME))))/60)/60 as tardanza2,
				SUBSTR(SEC_TO_TIME(SUM(TIME_TO_SEC(CAST(extra AS TIME)) + TIME_TO_SEC(CAST(extraext AS TIME)) + TIME_TO_SEC(CAST(extranoc AS TIME)) + TIME_TO_SEC(CAST(extraextnoc AS TIME)) + TIME_TO_SEC(CAST(mixtodiurna AS TIME)) + TIME_TO_SEC(CAST(mixtonoc AS TIME)) + TIME_TO_SEC(CAST(mixtoextdiurna AS TIME)) + TIME_TO_SEC(CAST(mixtoextnoc AS TIME)))),1,5) as extra,
				SUBSTR(SEC_TO_TIME(SUM(TIME_TO_SEC(CAST(extra AS TIME)) + TIME_TO_SEC(CAST(extraext AS TIME)))),1,5) as extradiu,
				SUBSTR(SEC_TO_TIME(SUM(TIME_TO_SEC(CAST(domingo AS TIME)))),1,5) as domingo,
				SUBSTR(SEC_TO_TIME(SUM(TIME_TO_SEC(CAST(nacional AS TIME)))),1,5) as nacional,
				SUBSTR(SEC_TO_TIME(SUM(TIME_TO_SEC(CAST(extranoc AS TIME)))),1,5) as extranoc,
				IFNULL(SUBSTR(SEC_TO_TIME(SUM(CASE WHEN weekday(fecha) = 6 THEN TIME_TO_SEC(CAST(mixtodiurna AS TIME)) END)),1,5), '00:00') AS mixtodiurnadom,
				((SUM(TIME_TO_SEC(CAST(extra AS TIME)) + TIME_TO_SEC(CAST(extraext AS TIME)) + TIME_TO_SEC(CAST(extranoc AS TIME)) + TIME_TO_SEC(CAST(extraextnoc AS TIME)) + TIME_TO_SEC(CAST(mixtodiurna AS TIME)) + TIME_TO_SEC(CAST(mixtonoc AS TIME)) + TIME_TO_SEC(CAST(mixtoextdiurna AS TIME)) + TIME_TO_SEC(CAST(mixtoextnoc AS TIME))))/60)/60 as extra2
				FROM reloj_detalle 
				WHERE ficha = ".$this->colaborador."
				AND fecha BETWEEN '$fecha_inicio' AND '$fecha_fin'";
		$query=query($sql,$conexion);
		$fila=fetch_array($query);

		$porc225 = "00:00";
		$porc175 = "00:00";
		$porc766 = "00:00";
		$porc125 = "00:00";
		if($fila["domingo"] != "00:00") {
			$porc225 = $fila["extranoc"];
		}
		elseif($fila["nacional"] != "00:00") {
			$porc766 = $fila["extranoc"];
		}
		else {
			$porc175 = $fila["extranoc"];
			$porc125 = $fila["extradiu"];
		}

		$html .= '<tr>
				<td  valign="middle" align="center" style="width:150px">
				</td>
				<td  valign="middle" align="right" style="width:200px;font-weight:bold;">
					Totales para el empleado:
				</td>
				<td  valign="middle" align="center" style="width:100px">
					'.$fila["trabajadas"].'
				</td>
				<td  valign="middle" align="center" style="width:100px">
					'.$fila["tardanza"].'
				</td>
				<td  valign="middle" align="center" style="width:100px">
					'.$totalAus.':00
				</td>
				<td  valign="middle" align="center" style="width:100px">
					'.gmdate('H:i', floor($totalInci * 3600)).'
				</td>
				<td  valign="middle" align="center" style="width:100px">
					'.$fila["extra"].'
				</td>
				<td  valign="middle" align="center" style="width:100px">
					'.$fila["extra"].'
				</td>
			</tr>';
		$html .= '<tr>
			<td colspan="2" valign="middle" align="right" style="width:350px;font-weight:bold;">
				Totales para el empleado formato decimal:
			</td>
			<td  valign="middle" align="center" style="width:100px">
				'.number_format($fila["trabajadas2"],2,'.',',').'
			</td>
			<td  valign="middle" align="center" style="width:100px">
				'.number_format($fila["tardanza2"],2,'.',',').'
			</td>
			<td  valign="middle" align="center" style="width:100px">
				'.number_format($totalAus,2,'.',',').'
			</td>
			<td  valign="middle" align="center" style="width:100px">
				'.number_format($totalInci,2,'.',',').'
			</td>
			<td  valign="middle" align="center" style="width:100px">
				'.number_format($fila["extra2"],2,'.',',').'
			</td>
			<td  valign="middle" align="center" style="width:100px">
				'.number_format($fila["extra2"],2,'.',',').'
			</td>
		</tr>';
		$html .= '</table>
		<BR />
		<BR />
		<BR />';

		$html .= '
		<table width="1024" border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td  valign="middle" align="left" style="width:320px;font-weight:bold;" colspan="2">
					Totales para horas extras aprobadas
				</td>
				<td  valign="middle" align="left" style="width:320px;font-weight:bold;" colspan="2">
					Totales para horas en Domingo y dia libre
				</td>
				<td  valign="middle" align="left" style="width:320px;font-weight:bold;" colspan="2">
					Totales para horas Dia nacional o festivo
				</td>
			</tr>

			<tr>
				<td  valign="middle" align="left" style="width:230px;" >
				</td>

				<td  valign="middle" align="left" style="width:90px;" >
				</td>
				
				<td  valign="middle" align="left" style="width:230px;" >
					Domingo o Día Libre (+150%):
				</td>
				<td  valign="middle" align="left" style="width:90px;" >
					'.$fila["domingo"].'
				</td>

				<td  valign="middle" align="left" style="width:230px;" >
					Día Nacional o Festivo (+150%):
				</td>
				<td  valign="middle" align="left" style="width:90px;" >
					'.$fila["nacional"].'
				</td>
			</tr>

			<tr>
				<td  valign="middle" align="left" style="width:230px;" >
				</td>
				<td  valign="middle" align="left" style="width:90px;" >
				</td>
				
				<td  valign="middle" align="left" style="width:230px;" >
					Día compensatorio (+50%):
				</td>
				<td  valign="middle" align="left" style="width:90px;" >
					00:00
				</td>

				<td  valign="middle" align="left" style="width:230px;" >
				</td>
				<td  valign="middle" align="left" style="width:90px;" >
				</td>
			</tr>

			<tr>
				<td  valign="middle" align="left" style="width:230px;" >
					125%:
				</td>
				<td  valign="middle" align="left" style="width:90px;" >
					'.$porc125.'
				</td>
				
				<td  valign="middle" align="left" style="width:230px;" >
					125% x 150%  (188%):
				</td>
				<td  valign="middle" align="left" style="width:90px;" >
					00:00
				</td>

				<td  valign="middle" align="left" style="width:230px;" >
					125% x 250% (313%):
				</td>
				<td  valign="middle" align="left" style="width:90px;" >
					00:00
				</td>
			</tr>

			<tr>
				<td  valign="middle" align="left" style="width:230px;" >
					150%:
				</td>
				<td  valign="middle" align="left" style="width:90px;" >
					00:00
				</td>
				
				<td  valign="middle" align="left" style="width:230px;" >
					150% x 150%  (225%):
				</td>
				<td  valign="middle" align="left" style="width:90px;" >
					'.$fila["mixtodiurnadom"].'
				</td>

				<td  valign="middle" align="left" style="width:230px;" >
					150% x 250% (375%):
				</td>
				<td  valign="middle" align="left" style="width:90px;" >
					00:00
				</td>
			</tr>

			<tr>
				<td  valign="middle" align="left" style="width:230px;" >
					175%:
				</td>
				<td  valign="middle" align="left" style="width:90px;" >
					'.$porc175.'
				</td>
				
				<td  valign="middle" align="left" style="width:230px;" >
					175% x 150%  (263%):
				</td>
				<td  valign="middle" align="left" style="width:90px;" >
					00:00
				</td>

				<td  valign="middle" align="left" style="width:230px;" >
					175% x 250% (438%):
				</td>
				<td  valign="middle" align="left" style="width:90px;" >
					00:00
				</td>
			</tr>

			<tr>
				<td  valign="middle" align="left" style="width:230px;" >
					125% x 175% (219%):
				</td>
				<td  valign="middle" align="left" style="width:90px;" >
					00:00
				</td>
				
				<td  valign="middle" align="left" style="width:230px;" >
					125% x 150% x 175%  (328%):
				</td>
				<td  valign="middle" align="left" style="width:90px;" >
					00:00
				</td>

				<td  valign="middle" align="left" style="width:230px;" >
					125% x 250% x 175% (547%):
				</td>
				<td  valign="middle" align="left" style="width:90px;" >
					00:00
				</td>
			</tr>

			<tr>
				<td  valign="middle" align="left" style="width:230px;" >
					150% x 175% (263%):
				</td>
				<td  valign="middle" align="left" style="width:90px;" >
					00:00
				</td>
				
				<td  valign="middle" align="left" style="width:230px;" >
					150% x 150% x 175%  (394%):
				</td>
				<td  valign="middle" align="left" style="width:90px;" >
					00:00
				</td>

				<td  valign="middle" align="left" style="width:230px;" >
					150% x 250% x 175% (656%):
				</td>
				<td  valign="middle" align="left" style="width:90px;" >
					00:00
				</td>
			</tr>

			<tr>
				<td  valign="middle" align="left" style="width:230px;" >
					175% x 175% (306%):
				</td>
				<td  valign="middle" align="left" style="width:90px;" >
					00:00
				</td>
				
				<td  valign="middle" align="left" style="width:230px;" >
					175% x 150% x 175%  (459%):
				</td>
				<td  valign="middle" align="left" style="width:90px;" >
					00:00
				</td>

				<td  valign="middle" align="left" style="width:230px;" >
					175% x 250% x 175% (766%):
				</td>
				<td  valign="middle" align="left" style="width:90px;" >
					'.$porc766.'
				</td>
			</tr>
		</table>';
		$html .= '<BR />';
		$this->writeHTML($html);
    }

}//fin clase

// create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', true);
$pdf->fechainicio=$_REQUEST['fecha_inicio'];
$pdf->fechafinal=$_REQUEST['fecha_fin'];

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('AMAXONIA');
$pdf->SetTitle('Hoja de Tiempo');
$pdf->SetSubject('Hoja de Tiempo');
$pdf->SetKeywords('TCPDF, PDF, reporte, asistencia, personal');

// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);
$pdf->SetFooterData(false);
// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

//set margins
$pdf->SetMargins(10, 10, 10,true);
$pdf->SetLeftMargin(10);
$pdf->SetHeaderMargin(10);
$pdf->SetRightMargin(10);
$pdf->setPrintFooter(false);
//set auto page breaks
$pdf->SetAutoPageBreak(TRUE, 15	);

//set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

//set some language-dependent strings
$pdf->setLanguageArray($l);

// ---------------------------------------------------------

// set font
$pdf->SetFont('helvetica', '', 12);

$fecha_inicio= fecha_sql($_REQUEST['fecha_inicio']);
$fecha_fin=fecha_sql($_REQUEST['fecha_fin']);
$colaborador=utf8_encode($_REQUEST['colaborador']);
$nivel1=$_REQUEST['nivel1'];
$pdf->colaborador=utf8_encode($colaborador);
//$pdf->ColoredTable($reg);
if($colaborador) {
	// add a page
	$pdf->AddPage('L', 'Letter');
	$pdf->CuerpoTablaHtml($fecha_inicio,$fecha_fin);
}
else {
	$conexion=conexion();
	$sql = "SELECT ficha 
	FROM nompersonal
	WHERE codnivel1 = '$nivel1'
	AND ficha IN (SELECT DISTINCT ficha 
					FROM reloj_detalle
					WHERE fecha BETWEEN '$fecha_inicio' AND '$fecha_fin')
	ORDER BY apenom";
	$res=query($sql, $conexion);
	while($fila = fetch_array($res)) {
		$pdf->colaborador = $fila["ficha"];
		$pdf->AddPage('L', 'Letter');
		$pdf->CuerpoTablaHtml($fecha_inicio, $fecha_fin);
	}
}

// ---------------------------------------------------------

//Close and output PDF document
$pdf->Output('hoja_tiempo.pdf', 'I');

//============================================================+
// END OF FILE                                                
//============================================================+
//$informes->desconectar_bd();
