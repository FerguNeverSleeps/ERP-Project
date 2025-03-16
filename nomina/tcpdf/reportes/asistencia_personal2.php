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
date_default_timezone_set('America/Panama');

//require_once('../../../Clases/Cls_conexion.php');
//$informes = new Cls_conexion();
//$informes->conectar_bd();


// Extend the TCPDF class to create custom Header and Footer
class MYPDF extends TCPDF {
	public $fechainicio, $fechafinal, $fecharegistro;
	//Page header
	public function Header() {
		// Logo
		$Conn=conexion();
		$var_sql="select * from nomempresa";
		$rs = query($var_sql,$Conn);
		$row_rs = fetch_array($rs);
		$var_encabezado1=$row_rs['nom_emp'];
		$var_izquierda='../../imagenes/'.$row_rs[imagen_izq];
		$var_derecha='../../imagenes/'.$row_rs[imagen_der];

		$image_file = K_PATH_IMAGES.'../../vista/Encabezado.png';
		//$this->Image($image_file, 10, 10, 190, '', 'JPG', '', '', false, 300, '', false, false, 0, false, false, false);
//		$this->Cell(0, 20, 'HOJA N/TOTAL DE HOJAS '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
		$this->SetFont('times', 8);
		$fecha = date("d")."-".date("m")."-".date("Y");
		$html=
		'<table width="100%" border="0" cellpadding="1" cellspacing="0">
			<tr>
				<td colspan="1" width="25%" valign="middle" align="left" style="border-top-color:#FFF;border-bottom-color:#FFF;border-left-color:#FFF;border-right-color:#FFF;">
					<img  width="150" height="40" src="'.$var_izquierda.'" />
				</td>
				<td colspan="3" width="50%" valign="middle" align="center" style="border-top-color:#FFF;border-bottom-color:#FFF;border-left-color:#FFF;border-right-color:#FFF;font-size:18px">
					LISTADO DE MARCACIONES CALCULADAS
				</td>
				<td colspan="1" width="25%" valign="middle" align="center" style="border-top-color:#FFF;border-bottom-color:#FFF;border-left-color:#FFF;border-right-color:#FFF;">
					<img width="150" height="40" src="'.$var_derecha.'" />
				</td>
			</tr>
  		
		<tr>
			
			<td width="100%" style="border-left-color:#FFF;border-bottom-color:#FFF;font-size:12x" align="center">
				<div>PERIODO BISEMANAL DEL '.$this->CambiarFecha($this->fechainicio).' AL'.$this->CambiarFecha($this->fechafinal).'</div>
			</td>
			
		</tr>
		
		</table>
		<BR />';
		$this->writeHTML($html);
		$this->SetFont('times', 7);
		//Linea 1
		$this->cell(50,5,"",1,0);
		$this->cell(42,5,"",1,0);
		$this->cell(15,5,"",1,0);
		$this->cell(231,5,"Distribución de la jornada extraordinaria por tipo de dia y recargo",1,1);

		//Línea 2
		$this->cell(50,5,"Marcaciones por Empleado",1,0);
		$this->cell(42,5,"Jornada Ordinaria",1,0);
		$this->cell(15,5,"TOTAL",1,0);
		$this->cell(66,5,"Día Regular",1,0);
		$this->cell(81,5,"Día domingo/descanso",1,0);
		$this->cell(84,5,"Nacional / Duelo",1,1);

		//Linea 3
		$this->cell(50,5,"",1,0);
		$this->cell(42,5,"",1,0);
		$this->cell(15,5,"HORAS",1,0);
		$this->cell(33,5,"1 A 3 Horas",1,0);
		$this->cell(33,5,"+3 días, +9 Semana",1,0);

		$this->cell(12,5,"Regular",1,0);
		$this->cell(33,5,"1 A 3 Horas",1,0);
		$this->cell(36,5,"+3 días, +9 Semana",1,0);

		$this->cell(12,5,"Regular",1,0);
		$this->cell(36,5,"1 A 3 Horas",1,0);
		$this->cell(36,5,"+3 días, +9 Semana",1,1);
		//Línea 
		$this->cell(5,5,"D",1,0);

		$this->cell(15,5,"Fecha",1,0);
		$this->cell(15,5,"Entrada",1,0);
		$this->cell(15,5,"Salida",1,0);
		$this->cell(14,5,"H. Reg.",1,0);
		$this->cell(14,5,"Tard.",1,0);
		$this->cell(14,5,"Aus.",1,0);
		$this->cell(15,5,"",1,"C",0);
		$this->cell(11,5,"1.25",1,"C",0);
		$this->cell(11,5,"1.5",1,"C",0);
		$this->cell(11,5,"1.75",1,"C",0);
		$this->cell(11,5,"2.1875",1,"C",0);
		$this->cell(11,5,"2.625",1,"C",0);
		$this->cell(11,5,"3.0625",1,"C",0);
		$this->cell(12,5,"1.5",1,"C",0);
		$this->cell(11,5,"1.875",1,"C",0);
		$this->cell(11,5,"2.25",1,"C",0);
		$this->cell(11,5,"2.625",1,"C",0);
		$this->cell(12,5,"3.28125",1,"C",0);
		$this->cell(12,5,"3.9375",1,"C",0);
		$this->cell(12,5,"4.59735",1,"C",0);
		$this->cell(12,5,"2.5",1,"C",0);
		$this->cell(12,5,"3.125",1,"C",0);
		$this->cell(12,5,"3.75",1,"C",0);
		$this->cell(12,5,"4.375",1,"C",0);
		$this->cell(12,5,"5.46875",1,"C",0);
		$this->cell(12,5,"6.5625",1,"C",0);
		$this->cell(12,5,"7.65625",1,"C",1	);
		
		// Set font
		// Page number
		
}
public function ColoredTable($reg) 
{
		
	$conexion=conexion();
    if($this->getAliasNumPage() <= 1)
	{
		$this->SetY(45);
	}
    
	// Data
	$fill = 1;
	$border='B';
	$ln=0;
	$fill = 0;
	$align='C';
	$link=0;
	$stretch=1;
	$ignore_min_height=0;
	$calign='T';
	$valign='T';
	$height=5.75;//alto de cada columna
	$Y=47;
	$this->SetY($Y);
	$YMax=260;
    $sql1 = "SELECT DISTINCT np.codnivel1, nn.descrip as departamento
         FROM   nompersonal np
         INNER JOIN nomnivel1 nn ON nn.codorg=np.codnivel1
         ORDER BY np.codnivel1";

    $res1=query($sql1, $conexion);
    
	    while($row=fetch_array($res1))
	    {		   
            $codnivel1=$row['codnivel1'];
            $departamento=$row['departamento'];


			// Color and font restoration
			$this->SetFont('helvetica', '', 10);

			$this->SetFillColor(224, 235, 255);
			$this->SetTextColor(0);
			$this->SetFont('');
		
			
			
			$sql = "SELECT DISTINCT np.ficha, np.apenom as nombre, nc.des_car as cargo
                    FROM   reloj_detalle rd, nompersonal np, nomcargos nc
                    WHERE   rd.ficha=np.ficha AND nc.cod_car=np.codcargo AND rd.id_encabezado=".$reg." 
                    AND np.codnivel1=".$codnivel1." ORDER BY np.apenom";
            $res=query($sql, $conexion);
            
            while($fila=fetch_array($res))
            {
            	$header = array('Fecha', 'Turno', 'Entr.','SA','EA','Sal.','Sal. DS','Desc.','Ord.','Dom','Nac','Tard.','EM','DNT','Extra','ExtraExt','ExtraNoc','NocExt');

				$this->Cell(50, 7, $row['departamento'], 1, 0, 'C', 1);
				$this->Ln();

				$this->SetFillColor(255, 255, 255);
				$this->SetTextColor(0,0,0);
				$this->SetDrawColor(100, 100, 100);
				$this->SetLineWidth(0.3);
				// Header
				$w = array(20, 13, 13,13,13,13,13,13,13,13,13,13,13,13,13,17,17,13);
				$num_headers = count($header);
				
				for($i = 0; $i < $num_headers; ++$i) {
					$this->Cell($w[$i], 7, $header[$i], 1, 0, 'C', 1);
				}
				$this->Ln();
                $ficha=$fila['ficha'];
                $cargo=$fila['cargo'];
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
                 reloj_detalle.hora_inicio,
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
                 reloj_detalle.domingo,
                 reloj_detalle.nacional,
                 reloj_detalle.emergencia,
                 reloj_detalle.descansoincompleto,
                 reloj_detalle.tardanza
            	FROM
                 nomturnos LEFT JOIN nompersonal ON nomturnos.turno_id = nompersonal.turno_id
                 LEFT JOIN reloj_detalle ON nompersonal.ficha = reloj_detalle.ficha
                 LEFT JOIN reloj_encabezado ON reloj_detalle.id_encabezado = reloj_encabezado.cod_enca
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
	                $fecha = $date->format('d-m-Y');
	                $hora = $date->format('H:i');
	                $ficha = $fila[ficha];

	                if($i==0)
	                {	
                        $this->Cell(110, $altura, $fila[ficha]." - ".$fila[apenom]." - ".$cargo, 0,$ln,'L',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
	                  $this->Ln();
						$fichaaux=$fila[ficha];
                        $fechaaux=$fecha;
                        $apenomaux=$fila[apenom];
	                }
	                if($ficha!=$fichaaux)
	                {
	                    //TOTALES
	                	$this->Cell(110, $altura, $fila[ficha]." ".$fila[apenom], $border=0,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
	                	$this->Ln();
	                    $fichaaux=$ficha;

	                    $apenomaux=$fila[apenom];

	                }
	                if($fila[entrada] == '00:00'){
                		$fila[entrada] = '';
                	}
                	if($fila[salmuerzo] == '00:00'){
                		$fila[salmuerzo] = '';
                	}
                    if($fila[ealmuerzo] == '00:00'){
                		$fila[ealmuerzo] = '';
                	}
                	if($fila[salida] == '00:00'){
                		$fila[salida] = '';
                	}
                	if($fila[domingo] == '00:00'){
                		$fila[domingo] = '';
                	}
                	if($fila[nacional] == '00:00'){
                		$fila[nacional] = '';
                	}
                    if($fila[ealmuerzo] == '00:00'){
                		$fila[ealmuerzo] = '';
                	}
                	if($fila[tardanza] == '00:00'){
                		$fila[tardanza] = '';
                	}
                	if($fila[emergencia] == '00:00'){
                		$fila[emergencia] = '';
                	}
                	if($fila[descansoincompleto] == '00:00'){
                		$fila[descansoincompleto] = '';
                	}
                	if($fila[salida_diasiguiente] == '00:00'){
                		$fila[salida_diasiguiente] = '';
                	}
                	if($fila[extra] == '00:00'){
                		$fila[extra] = '';
                	}
                	if($fila[extraext] == '00:00'){
                		$fila[extraext] = '';
                	}
                	if($fila[extranoc] == '00:00'){
                		$fila[extranoc] = '';
                	}
                	if($fila[extraextnoc] == '00:00'){
                		$fila[extraextnoc] = '';
                	}
                	if($fila[ordinaria] == '00:00'){
                		$fila[ordinaria] = '';
                	}
	                //$this->Row(array($fecha,$turno,$fila[entrada],$fila[salmuerzo],$fila[ealmuerzo],$fila[salida],$fila[ordinaria],$fila[domingo],$fila[nacional],$fila[tardanza],$fila[emergencia],$fila[descansoincompleto]));
	                /*13echo $fecha." ".$turno." ".$fila[entrada]." ".
	                $fila[salmuerzo]." ".$fila[ealmuerzo]." ".$fila[salida]." ".$fila[ordinaria]." ".$fila[domingo]." ".
	                $fila[nacional]." ".$fila[tardanza]." ".$fila[emergencia]." ".$fila[descansoincompleto]."<br>";*/
	                
	            }//Fin While
	        }//Fin While
	    }//Fin While
    }//fin function
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
	function dia_semana($dia)
	{
		$Dias = array('L','M','Mi','J','V','S','D');
		return $Dias[($dia-1)];
	}
public function CuerpoTablaHtml($reg,$fecha_inicio,$fecha_fin,$fecha_registro)
{
	$conexion=conexion();
    if($this->getAliasNumPage() <= 1)
	{
		$Y=55;
		$this->SetTopMargin ($Y);
		$this->SetTopMargin ($Y);

	}
	else
	{
		$Y=51;
		$this->SetTopMargin($Y);
		$this->SetTopMargin ($Y);

	}

	$fill = 1;
	$border=0;
	$ln=0;
	$fill = 0;
	$align='C';
	$link=0;
	$stretch=1;
	$ignore_min_height=0;
	$calign='T';
	$valign='M';
	$height=5.75;//alto de cada columna
	$YMax=260;
	$altura=5;
	$sql1 = "   SELECT DISTINCT np.codnivel1, nn.descrip as departamento
                FROM   reloj_detalle rd, nompersonal np, nomcargos nc,nomnivel1 nn
                WHERE   rd.ficha=np.ficha AND nc.cod_car=np.codcargo AND nn.codorg=np.codnivel1 AND rd.id_encabezado=".$reg." 
                Group BY nn.descrip";

	$res1=query($sql1, $conexion);
    
		while($row=fetch_array($res1))
		{		   
			$codnivel1=$row['codnivel1'];
			$departamento=$row['departamento'];


			// Color and font restoration
			$this->SetFont('helveticab', '', 8);

			$this->SetFillColor(224, 235, 255);
			$this->SetTextColor(0);
		
			
			
			$sql = "SELECT DISTINCT np.ficha, np.apenom as nombre, nc.des_car as cargo, suesal, hora_base
					FROM   reloj_detalle rd
					LEFT JOIN nompersonal np on (rd.ficha = np.ficha)
					LEFT JOIN nomcargos nc on (nc.cod_car = np.codcargo)
					WHERE  rd.id_encabezado='{$reg}'  AND np.codnivel1='{$codnivel1}' 
					ORDER BY np.ficha";
            $res=query($sql, $conexion);
            //$this->Cell(50, 7, $row['departamento'], 1, 0, 'C', 1);
			$this->Ln();
            while($fila=fetch_array($res))
            {
				//$header = array('Fecha', 'Turno', 'Entr.','SA','EA','Sal.','Sal. DS','Desc.','Ord.','Dom','Nac','Tard.','EM','DNT','Extra','ExtraExt','ExtraNoc','NocExt');

				

				$this->SetFillColor(255, 255, 255);
				$this->SetTextColor(0,0,0);
				$this->SetDrawColor(100, 100, 100);
				$this->SetLineWidth(0.3);
				// Header

				$ficha     = $fila['ficha'];
				$cargo     = $fila['cargo'];
				$suesal    = $fila['suesal'];
				$hora_base = $fila['hora_base'];
				$consulta  = "SELECT
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
                 reloj_detalle.hora_inicio,
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
                 reloj_detalle.tardanza
            	FROM
                 nomturnos LEFT JOIN nompersonal ON nomturnos.turno_id = nompersonal.turno_id
                 LEFT JOIN reloj_detalle ON nompersonal.ficha = reloj_detalle.ficha
                 LEFT JOIN reloj_encabezado ON reloj_detalle.id_encabezado = reloj_encabezado.cod_enca
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
            	$w = array(5, 15);
            	$semana_sig = 0;
            	$total_horas = $total_decimal = $total_extas_nocturnas = $total_horas_extendidas = $total_dinero = $total_extras = 0;

	            while($fila=fetch_array($query))
	            {
					$pos    = 0;
					$turno  = $fila[descripcion];
					$date   = new DateTime($fila[fecha]);
					$fecha  = $date->format('d-m-Y');
					$hora   = $date->format('H:i');
					$dia    = $date->format('N');
					$semana = $date->format('W');;
					$dia    = $this->dia_semana($dia);
					$ficha  = $fila[ficha];
					$cuerpo = array($dia,$fecha);

					

					//hora_inicio
					array_push($w, 15);
					array_push($cuerpo, $fila[hora_inicio]);
					$total_horas_extras = $fila['extra']+$fila['extraext']+$fila['extranoc'];
					$total_horas_extras = number_format($total_horas_extras,2);
					$total_horas        += $total_horas_extras;

					//Salida
					array_push($w, 15);
					array_push($cuerpo, $fila[salida]);

					//Salida al dia siguiente
					array_push($w, 14);
					array_push($cuerpo, '');

					array_push($w, 14);
					array_push($cuerpo, '');

					array_push($w, 14);
					array_push($cuerpo, '');

					//Horas Extras
					array_push($w, 15);					
					array_push($cuerpo, $total_horas_extras);
					$extra = ($fila['extra'] != "00:00") ? $fila['extra'] : '' ;

					array_push($w, 11);
					$extra = ($fila['extra'] != "00:00") ? $fila['extra'] : '' ;
					array_push($cuerpo, $fila[extra]);
					$extra = number_format($extra,2);

					$total_extras += $extra;

					//Horas Extras Nocturnas
					array_push($w, 11);
					$extranoc = ($fila['extranoc'] != "00:00") ? number_format($fila['extranoc'],2) : '' ;
					$total_extas_nocturnas += $extranoc;
					array_push($cuerpo, $extranoc);	

					//Horas extras nocturnas con recargo
					array_push($w, 11);
					$extraextnoc = ($fila[extraextnoc] != "00:00") ? $fila[extraextnoc] : '' ;
					array_push($cuerpo, $extraextnoc);

					///Horas Extras Mixto diurna
					array_push($w, 11);
					$mixtodiurna = ($fila[mixtodiurna] != "00:00") ? $fila[mixtodiurna] : '' ;
					array_push($cuerpo, $fmixtodiurna);

					//Horas Extras Diurnas Con recargo
					array_push($w, 11);
					$extraext = ($fila['extraext'] != "00:00") ? number_format($fila['extraext'],2) : '' ;
					$total_horas_extendidas += $extraext;

					array_push($cuerpo, $extraext);

					//Hora Extra Mixto Diurna con Recargo
					array_push($w, 11);
					$mixtoextdiurna = ($fila[mixtoextdiurna] != "00:00") ? $fila[mixtoextdiurna] : '' ;
					array_push($cuerpo, $mixtoextdiurna);

					//Hora Extra Mixto Nocturna
					array_push($w, 12);
					$mixtonoc = ($fila[mixtonoc] != "00:00") ? $fila[mixtonoc] : '' ;
					array_push($cuerpo, $mixtonoc);

					//Hora Extra Mixto Nocturna con Recargo
					array_push($w, 11);
					$mixtoextnoc = ($fila[mixtoextnoc] != "00:00") ? $fila[mixtoextnoc] : '' ;
					array_push($cuerpo, $mixtoextnoc);

					//Descanso Extra
					array_push($w, 11);
					$descextra1 = ($fila[descextra1] != "00:00") ? $fila[descextra1] : '' ;
					array_push($cuerpo, $descextra1);
						            	
					//Llamado de Emergencia
					array_push($w, 11);
					$ent_emer = ($fila[ent_emer] != "00:00") ? $fila[ent_emer] : '' ;
					array_push($cuerpo, $ent_emer);

					//Llamado de Emergencia

					array_push($w, 12);
					$sal_emer = ($fila[sal_emer] != "00:00") ? $fila[sal_emer] : '' ;
					array_push($cuerpo, $sal_emer);

					//observacion
					array_push($w, 12);
					$observacion = ($fila[observacion] != "00:00") ? $fila[observacion] : '' ;
					array_push($cuerpo, $observacion);

					array_push($w, 12);
					array_push($cuerpo, '');
					
					array_push($w, 12);
					array_push($cuerpo, '');
					
					array_push($w, 12);
					array_push($cuerpo, '');
					
					array_push($w, 12);
					array_push($cuerpo, '');
					
					array_push($w, 12);
					array_push($cuerpo, '');
					
					array_push($w, 12);
					array_push($cuerpo, '');
					
					array_push($w, 12);
					array_push($cuerpo, '');
					
					array_push($w, 13);
					array_push($cuerpo, '');
	            		        
					$rata_por_hora = (($suesal/4.3333)/$hora_base);
					$rata_por_hora = number_format($rata_por_hora,2);
					if($i==0)
					{	
						$this->SetFont('helvetica', 'B', 9);
						$this->SetTextColor(0);
					    $this->Cell(92, $altura, $fila[ficha]." - ".$fila[apenom]." - ".$cargo, 1,$ln,'L',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
						$this->Cell(92, $altura," Rata x Hora ".$rata_por_hora, 0,$ln,$align='L',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
					  	$this->Ln();
						$fichaaux=$fila[ficha];
					    $fechaaux=$fecha;
					    $apenomaux=$fila[apenom];
					}
					if($ficha!=$fichaaux)
					{
						$this->SetFont('helvetica', 'B', 9);
						$this->SetTextColor(0);
					    //TOTALES
						$this->Cell(92, $altura,$fila[ficha]." - ".$fila[apenom]." - ".$cargo, $border=1,$ln,'L',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
						$this->Cell(96, $altura," Rata x Hora ".$rata_por_hora, 0,$ln,$align='L',$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
						$this->Ln();
					    

					}
					$this->SetFont('helvetica', '', 8);
					$this->SetTextColor(0);
					$num_headers = count($cabecera);
					if(	$this->GetY() >172)
					{
						$this->AddPage();
					}				
					 
					$turno=$entrada=$salmu=$ealmu=$salida=$regular=$ausen=$tardan=$incapac=$sobret=$feriado="";
	                $entradaf=$salmuf=$ealmuf=$salidaf="";
	                //print_r($cuerpo);exit;
	                if ($semana_sig == $semana) 
						{
							$this->Ln();
						}
	               	
					for ($ixx=0; $ixx <count($cuerpo) ; $ixx++) { 
						$ancho=11;
						if($ixx==0)
						{
							$ancho=16;
						}
						/*if($ixx==18)
						{
							$this->Ln();		
						}*/
						
						
						$this->Cell($w[$ixx], $altura, $cuerpo[$ixx], 1,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);


					}
	                $this->Ln();
					$semana_sig = $semana +1;

	                $fechaaux=$fecha;
	                $j=0;               


	                $j++;
	                $i++;
	             }//fin del while
	             $w2 = array( '0'  => 5, '1'  => 45, '2'  => 14, '3'  => 14, '4'  => 14, '5'  => 15, '6'  => 11, '7'  => 11, '8'  => 11, '9'  => 11, '10' => 11, '11' => 11, '12' => 12, '13' => 11, '14' => 11, '15' => 11, '16' => 12, '17' => 12, '18' => 12, '19' => 12, '20' => 12, '21' => 12, '22' => 12, '23' => 12, '24' => 12, '25' => 13
	             );
				$total_horas            = number_format($total_horas,2);
				$total_extras           = number_format($total_extras,2);
				$total_extas_nocturnas  = number_format($total_extas_nocturnas,2);
				$total_horas_extendidas = number_format($total_horas_extendidas,2);
				$cuerpo2                = array(
					'0'  => '', '1'  => 'TOTALES EN HORAS', '2'  => '', '3'  => '', '4'  => '', '5'  => $total_horas, '6'  => $total_extras, '7'  => $total_extas_nocturnas, '8'  => '', '9'  => '', '10' => $total_horas_extendidas, '11' => '', '12' => '', '13' => '', '14' => '', '15' => '', '16' => '', '17' => '', '18' => '', '19' => '', '20' => '', '21' => '', '22' => '', '23' => '', '24' => '', '25' => ''
	             );
	             for ($jxx=0; $jxx < count($cuerpo2); $jxx++) 
	             { 
	             	if(	$this->GetY() >172)
					{
						$this->AddPage();
					}
					if ($jxx==0) {
						$bordes = 0;
					}
					else
					{
						$bordes = 1;
					}
					$this->Cell($w2[$jxx], $altura, $cuerpo2[$jxx], $bordes,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
	
	             }
	             $this-> Ln();
	             $cuerpo2                = array(
					'0'  => '', '1'  => 'TOTALES EN DECIMALES', '2'  => '', '3'  => '', '4'  => '', '5'  => $total_horas, '6'  => $total_extras, '7'  => $total_extas_nocturnas, '8'  => '', '9'  => '', '10' => $total_horas_extendidas, '11' => '', '12' => '', '13' => '', '14' => '', '15' => '', '16' => '', '17' => '', '18' => '', '19' => '', '20' => '', '21' => '', '22' => '', '23' => '', '24' => '', '25' => ''
	             );
	             for ($jxx=0; $jxx < count($cuerpo2); $jxx++) 
	             { 
	             	if(	$this->GetY() >172)
					{
						$this->AddPage();
					}
					if ($jxx==0) {
						$bordes = 0;
					}
					else
					{
						$bordes = 1;
					}
					$this->Cell($w2[$jxx], $altura, $cuerpo2[$jxx], $bordes,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);
	
	             }

	             $this-> Ln();
				$total_extras           = $total_extras * $rata_por_hora * 1.25;
				$total_extas_nocturnas  = $total_extas_nocturnas * $rata_por_hora * 1.5;
				$total_horas_extendidas = $total_horas_extendidas * $rata_por_hora * 2.625;
				$total_horas            = number_format($total_horas,2);
				$total_extras           = number_format($total_extras,2);
				$total_extas_nocturnas  = number_format($total_extas_nocturnas,2);
				$total_horas_extendidas = number_format($total_horas_extendidas,2);
				$total_horas  = $total_extras + $total_extas_nocturnas + $total_horas_extendidas;
	             $cuerpo2                = array(
					'0'  => '', '1'  => 'TOTALES EN DINERO', '2'  => '', '3'  => '', '4'  => '', '5'  => $total_horas, '6'  => $total_extras, '7'  => $total_extas_nocturnas, '8'  => '', '9'  => '', '10' => $total_horas_extendidas, '11' => '', '12' => '', '13' => '', '14' => '', '15' => '', '16' => '', '17' => '', '18' => '', '19' => '', '20' => '', '21' => '', '22' => '', '23' => '', '24' => '', '25' => ''
	             );
	             for ($jxx=0; $jxx < count($cuerpo2); $jxx++) 
	             { 
	             	if(	$this->GetY() >172)
					{
						$this->AddPage();
					}
					if ($jxx==0) {
						$bordes = 0;
					}
					else
					{
						$bordes = 1;
					}
					$this->Cell($w2[$jxx], $altura, $cuerpo2[$jxx], $bordes,$ln,$align,$fill,$link,$stretch,$ignore_min_height,$calign,$valign);

	
	             }
	             $this->Ln();
	             $this->Ln();

	            
	             		

	        }// Fin del While		
			
		}//Fin del While
	
	}

}//fin clase
		
		
		

// create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', true);
$pdf->fechainicio=$_REQUEST['fecha_ini'];
$pdf->fechafinal=$_REQUEST['fecha_fin'];
$pdf->fecharegistro=$_REQUEST['fecha_reg'];

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Selectra');
$pdf->SetTitle('Asistencia Personal');
$pdf->SetSubject('Reporte Asistencia Personal');
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
$pdf->SetFont('helvetica', 'BI', 14);

// add a page
$pdf->AddPage('L', 'LEGAL');

$reg=$_REQUEST['reg'];


//$pdf->ColoredTable($reg);
$pdf->CuerpoTablaHtml($reg,$fecha_inicio,$fecha_fin,$fecha_registro);

// ---------------------------------------------------------

//Close and output PDF document
$pdf->Output('Informe_Asistencia_Personal.pdf', 'I');

//============================================================+
// END OF FILE                                                
//============================================================+
//$informes->desconectar_bd();