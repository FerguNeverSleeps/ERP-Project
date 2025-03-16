<?php
error_reporting(E_ALL^E_NOTICE);
include("common.php");

function formatearHora($hora)
{
	$horas = explode(":",$hora);
	if(trim($horas[0])=="00")
		$hora = "24:".$horas[1];
	else
		$hora = $horas[0].":".$horas[1];
	return $hora;
}

class tiempo
{
	private $temp,$min,$horas;
	public function aminutos($cad)
	{
		$this->temp = explode(":",$cad);
		$this->min = ($this->temp[0]*60)+($this->temp[1]);
		return $this->min;
	}
	public function ahoras($cad)
	{
		$this->temp = $cad;
		if($this->temp>59)
		{
			$this->temp = $this->temp/60;
			$this->temp = explode(".",number_format($this->temp,2,".",""));
			$this->temp[0] = strlen($this->temp[0])==1 ? "0".$this->temp[0] : $this->temp[0];
			$this->temp[1] = (((substr($this->temp[1],0,2))*60)/100);
			$this->temp[1] = round($this->temp[1]);
			//$this->horas = $this->temp[0].":".(strlen($this->temp[1][0])==1 ? "0".$this->temp[1][0] : round(substr($this->temp[1][0],0,2).'.'.substr($this->temp[1][0],2,1)));
			$this->horas = $this->temp[0].":".(strlen($this->temp[1])==1 ? "0".$this->temp[1] : $this->temp[1]);
		}
		elseif(($this->temp=="")||($this->temp==0))
		{
			$this->horas = "00:00";
		}
		else
		{
			$this->horas = "00:".(strlen($this->temp)==1 ? "0".$this->temp : $this->temp);//$this->temp;
		}
		return $this->horas;
	}
}

$conexion=conexion();
$pendientes = 'C:\\AmaxoniaZKTECO\pendientes\\';
$procesados = 'C:\\AmaxoniaZKTECO\procesados\\';

$encabezados = array();
$enc = array();
//$fechaInicio = new DateTime($_POST['txtFechaInicio']);
//$fechaFin = new DateTime($_POST['txtFechaFin']);

//$fechaInicio = new DateTime("16-03-2018");
//$fechaFin = new DateTime("31-03-2018");

//$fechaI = $fechaInicio->format('Y-m-d');
//$fechaF = $fechaFin->format('Y-m-d');

//$insert = "INSERT INTO reloj_encabezado
//         VALUES('', '" . date("Y-m-d") . "' ,'{$fechaInicio->format('Y-m-d')}', '{$fechaFin->format('Y-m-d')}','Pendiente','amaxonia','','','','')";
//$result = query($insert,$conexion);


if (is_dir($pendientes)) 
{
    if ($dh = opendir($pendientes)) 
    {               
        while (($file = readdir($dh)) !== false) 
        {//escaneamos el directorio
            if(filetype($file)!='dir')
            {
                $archivo    = $pendientes.$file;
                $linecount = 0;
		        $handle = fopen($archivo, "r");
		        while(!feof($handle)){
		          $line = fgets($handle);
		          $linecount++;
		        }
		        fclose($handle);
		        //echo $linecount;
		        //exit;
		        $f = fopen($archivo, "r");
		        $i=0;

                
                $fechaFin = new DateTime("31-12-2099");
				$fechaFx = $fechaFin->format('m/d/Y');
				$cadena="9999     	xxx xxxxx     	".$fechaFx." 11:11:11 a. m. 	Entrada	Suzuki      	0               ";
				$lineax = fopen($archivo, "r+");
				$cursor = -1;
				fseek($lineax, $cursor, SEEK_END);
				if($linea === "\n" || $linea === "\r")
					$cadena=$cadena;
				else
					$cadena="\n".$cadena;
				fwrite($lineax,$cadena);
				fclose($lineax);
				$consulta="TRUNCATE TABLE reloj_datos;";
				$resultado = query($consulta,$conexion);                                

				while(!feof($f))
				{
					$data = array_values( array_filter(explode("	", fgets($f))));

					$ficha = trim($data[0]);
                    $tipo = trim($data[3]);
                    if($tipo=="Entrada")
                    {
                        $tipo_marcacion=0;
                    }
                    if($tipo=="Salida")
                    {
                        $tipo_marcacion=1;
                    }
                    if($tipo=="Sal. Des.")
                    {
                        $tipo_marcacion=2;
                    }
                    if($tipo=="Ent. Des.")
                    {
                        $tipo_marcacion=3;
                    }
                    if($tipo=="Ent. Ext.")
                    {
                        $tipo_marcacion=4;
                    }
                    if($tipo=="Sal. Ext.")
                    {
                        $tipo_marcacion=5;
                    }
                    $cod_dispositivo = trim($data[4]);

                    $consulta_personal="SELECT personal_id, tipnom FROM nompersonal WHERE ficha='$ficha';";
                                    //echo "<br>";
                    //echo $consulta_personal;
                    //exit;
                    $resultado_personal = query($consulta_personal,$conexion);
                    $personal = fetch_array($resultado_personal);
                    $personal_id=$personal[personal_id];
                    $tipnom=$personal[tipnom];

                    $consulta_dispositivo="SELECT id_dispositivo FROM reloj_info WHERE cod_dispositivo='$cod_dispositivo';";
                                    //echo "<br>";
                    $resultado_dispositivo = query($consulta_dispositivo,$conexion);
                    $dispositivo = fetch_array($resultado_dispositivo);
                    $id_dispositivo=$dispositivo[id_dispositivo];
                    if($id_dispositivo=="" || $id_dispositivo==NULL || $id_dispositivo==0)
                        $id_dispositivo=1;
                                        
					if($ficha == "")
						continue;

					$datesx = explode(" ",trim($data[2]));
					$fechass = $datesx[0];
					$horas = $datesx[1];
					$apm = trim($datesx[2]);
					$fecha=explode("/",$fechass);
				  	$fechas = $fecha[2]."-".$fecha[0]."-".$fecha[1];

				  	$horasxx = explode(":", $horas);
					
					if($apm == "a.m.")
						$horas = $horas;
					elseif(($apm == "p.m." && $horasxx[0]!=12) || ($apm == "p.m." && $horasxx[0]==0))
						$horas = ($horasxx[0]+12).":".$horasxx[1].":".$horasxx[2];
				  	
					preg_match_all('!\d!', $ficha, $matches);
					$ficha = (int)implode('',$matches[0]);
                                        $estatus = 0;

                    //$consulta_marcaciones = "DELETE FROM reloj_marcaciones  WHERE DATE(`fecha`) = DATE('$fechas') AND hora='$horas' AND id_empleado = '".$personal_id."' AND dispositivo = '".$id_dispositivo."'";
                    $consulta_marcaciones = "DELETE FROM reloj_marcaciones  WHERE fecha = '".$fechas."' AND hora='".$horas."' AND id_empleado = '".$personal_id."' AND dispositivo = '".$id_dispositivo."'";
                    //echo $consulta_detalle;
				    $result_marcaciones = query($consulta_marcaciones,$conexion);
                    
                    //$select = "SELECT cod_enca as cod,fecha_ini, fecha_fin, tipo_nomina FROM reloj_encabezado 
                    //            WHERE tipo_nomina =  '$tipnom' AND DATE('$fechas') BETWEEN DATE(`fecha_ini`) AND DATE(`fecha_fin`)";
                    $select = "SELECT cod_enca as cod,fecha_ini, fecha_fin, tipo_nomina FROM reloj_encabezado 
                                WHERE tipo_nomina =  '".$tipnom."' AND '".$fechas."' BETWEEN fecha_ini AND fecha_fin";
                    $result = query($select,$conexion);
                    $row = fetch_array($result);
                   
                    $idenc=$row[cod];
                    $fecha_ini=$row[fecha_ini];
                    $fecha_fin=$row[fecha_fin];
                    //echo $idenc;
                    $fechaInicio = new DateTime($fecha_ini);
                    $fechaFin = new DateTime($fecha_fin);

                    $fechaI = $fechaInicio->format('Y-m-d');
                    $fechaF = $fechaFin->format('Y-m-d');
                                
					$consulta="INSERT INTO reloj_datos (id, ficha, fecha, hora, disp_id) VALUES ('', '".$ficha."','".$fechas."','".$horas."', '".$id_dispositivo."')";
					$resultado = query($consulta,$conexion);
                                        
                     //INSERCION RELOJ_MARCACIONES
                    if($ficha!="9999")
                    {
                        $consulta_marcaciones="INSERT INTO reloj_marcaciones (id, id_empleado, ficha_empleado, fecha, hora, dispositivo, tipo, estatus)
                            VALUES ('','$personal_id','$ficha','$fechas','$horas', '$id_dispositivo', '$tipo_marcacion', '$estatus');";
                        //echo $consulta_marcaciones;                                        
                        $resultado_marcaciones = query($consulta_marcaciones,$conexion);
                    }

                    if($idenc!=0 && $idenc!=NULL && $idenc!='')
                    {
                    $consulta_detalle = "DELETE FROM reloj_detalle  WHERE ficha =  '".$ficha."' AND fecha = '".$fechas."' AND id_encabezado = '".$idenc."'";
                    //echo $consulta_detalle;
				    $result_detalle = query($consulta_detalle,$conexion);
                    }
				}


                //PRIMER BLOQUE
				//$consulta="SELECT * FROM reloj_datos WHERE fecha between '$fechaI' and '$fechaF' ORDER BY ficha asc, fecha asc, hora asc;";
                $consulta="SELECT * FROM reloj_datos ORDER BY ficha asc, fecha asc, hora asc;";
				//echo "<br>";
				$resultadox = query($consulta,$conexion);

				$continue = $j = $i = $kk = 0;
				//$linecount=$linecount-2;
				$acumExtras = 0;
				$disp_id = "";
				$disp_ids = "";
				while($fetchx=fetch_array($resultadox))
				{
					if($continue>=1)
					{
						$continue--;
						continue;
					}
					$idfetch = $fetchx[id];
					$horas = "";
					$ficha = $fetchx[ficha];
					$fechas = $fetchx[fecha];
					$horas = $fetchx[hora];
					$horasx = explode(":",$horas);
					$disp_id = $fetchx[disp_id];
					$horas=$horasx[0].":".$horasx[1];
					$dates = trim($fechas." ".$horas);	

                    /*
                    $consulta_personal="SELECT personal_id,tipnom FROM nompersonal WHERE ficha='".$ficha."'";
                                    //echo "<br>";
                    //echo $consulta_personal;
                    //exit;
                    $resultado_personal = query($consulta_personal,$conexion);
                    $personal = fetch_array($resultado_personal);
                    $personal_id=$personal[personal_id];
                    $tipnom=$personal[tipnom];

                    $select = "SELECT cod_enca as cod,fecha_ini, fecha_fin, tipo_nomina FROM reloj_encabezado 
                                WHERE tipo_nomina =  '".$tipnom."' AND '".$fechas."' BETWEEN fecha_ini AND fecha_fin";
                    $result = query($select,$conexion);
                    $row = fetch_array($result);
                    $idenc=$row[cod];
                    if($idenc!=0 && $idenc!=NULL && $idenc!='')                    
                    {
                        $enc[]=$idenc;
                    }
                    if($ficha==2472)
                    {
                        echo $select;
                    }
                    

                    $fecha_ini=$row[fecha_ini];
                    $fecha_fin=$row[fecha_fin];

                    $fechaInicio = new DateTime($fecha_ini);
                    $fechaFin = new DateTime($fecha_fin);

                    $fechaI = $fechaInicio->format('Y-m-d');
                    $fechaF = $fechaFin->format('Y-m-d');
                    */

					//AQUI EMPIEZA				

					$date = new DateTime($dates);
					$fecha = $date->format('Y-m-d');
					
					if($i==0)
					{
						$fichaaux=$ficha;
						$fechaaux=$fecha;
						
					}
					$i = 1;
					
					$query="select tur.* from nomturnos tur join nomcalendarios_personal per on tur.turno_id=per.turno_id where per.ficha='$fichaaux' and per.fecha='$fechaaux'";
					$result=query($query,$conexion);
					$filax = fetch_array($result);
					$nocturno = $filax[nocturno];
					if($nocturno == "")
						$nocturno = 0;
					if((($fecha!=$fechaaux)&&($nocturno==0))||(($ficha!=$fichaaux)&&($nocturno==0))||(($nocturno==1)&&($j>=2)))
					{
						$query="select tur.* from nomturnos tur join nomcalendarios_personal per on tur.turno_id=per.turno_id where per.ficha='$fichaaux' and per.fecha='$fechaaux'";
						$result=query($query,$conexion);
						$fila = fetch_array($result);
						$turno = trim($fila[descripcion]);
						$libre = $fila[libre];
						$tipo = $fila[tipo];
						$tiempo = new tiempo();
						$entrada0 = $tiempo->aminutos(substr($fila[tolerancia_llegada], 0,5));
						$entrada1 = $tiempo->aminutos(substr($fila[entrada], 0,5));
						$entradatol = $tiempo->aminutos(substr($fila[tolerancia_entrada], 0,5));

						$saldesc1 = $tiempo->aminutos(substr($fila[inicio_descanso],0,2)=="00" ? "24".substr($fila[inicio_descanso], 2,5) : substr($fila[inicio_descanso], 0,5));
						$entdesc1 = $tiempo->aminutos(substr($fila[salida_descanso], 0,5));
						$entdesctol = $tiempo->aminutos(substr($fila[tolerancia_descanso],0,2)=="00" ? "24".substr($fila[tolerancia_descanso], 2,5) : substr($fila[tolerancia_descanso], 0,5));
						$salida1 = $tiempo->aminutos(substr($fila[salida],0,2)=="00" ? "24".substr($fila[salida], 2,5) : substr($fila[salida], 0,5));
						$salidatol = $tiempo->aminutos(substr($fila[tolerancia_salida],0,2)=="00" ? "24".substr($fila[tolerancia_salida], 2,5) : substr($fila[tolerancia_salida], 0,5));

						
						$lim1 = $saldesc1 - $entrada1;
						$lim2 = $salida1 - $entdesc1;
						//echo $lim2->format('%H:%I');

						$lim = $lim1 + $lim2;

						$regular=$ausen=$tardan=$incapac=$sobret=$feriado="";
						$entradaf=$salmuf=$ealmuf=$salidaf="";
						$tardanza=$extra=$domingo="";
						
						$extra=$regular=$regular1=$regular2=$regular3=$regular4="";
						$extra=$extra1=$extra2=$extra3=$extra4="";
						$extraNoc=$extraNoc1=$extraNoc2=$extraNoc3=$extraNoc4="";
						$extraExt=$extraExt1=$extraExt2=$extraExt3=$extraExt4="";
						$extraNocExt=$extraNocExt1=$extraNocExt2=$extraNocExt3=$extraNocExt4="";
						
						$hExt = $tiempo->aminutos("03:00");
						$limiteExt = $tiempo->aminutos("18:00");
						$limiteExtFin = $tiempo->aminutos("06:00");
						$limiteExtNoc = $tiempo->aminutos("24:00");		
						$limite = $lim;

						if(($salida=="")&&($salmu!="")&&($ealmu==""))
						{
							$salida = $salmu;
							$salmu = "";
						}
						
						
						if($entrada!="")
						{
							$entradaf = $entrada;
							$entrada = substr($entrada, 0,2)=="24" ? "00".substr($entrada, 2,5) : substr($entrada, 0,5);
							$entrada = $tiempo->aminutos($entrada);
						} 
						if($salmu!="") 
						{
							$salmuf = $salmu;
							$salmu = $tiempo->aminutos($salmu);
						}
						if($ealmu!="")
						{
							$ealmuf = $ealmu;
							$ealmu = $tiempo->aminutos($ealmu);
						}
						if($salida!="") 
						{
							$salida = substr($salida, 0,2)=="00" ? "24".substr($salida, 2,5) : substr($salida, 0,5);
							$salidaf = $salida;
							$salida = $tiempo->aminutos($salida);
						}
								
                         $consulta_personal="SELECT personal_id,tipnom FROM nompersonal WHERE ficha='".$fichaaux."'";
                                    //echo "<br>";
                        //echo $consulta_personal;
                        //exit;
                        $resultado_personal = query($consulta_personal,$conexion);
                        $personal = fetch_array($resultado_personal);
                        $personal_id=$personal[personal_id];
                        $tipnom=$personal[tipnom];

                        $select = "SELECT cod_enca as cod,fecha_ini, fecha_fin, tipo_nomina FROM reloj_encabezado 
                                    WHERE tipo_nomina =  '".$tipnom."' AND '".$fechaaux."' BETWEEN fecha_ini AND fecha_fin";
                        $result = query($select,$conexion);
                        $row = fetch_array($result);
                        $idenc=$row[cod];
                        if($idenc!=0 && $idenc!=NULL && $idenc!='')                    
                        {
                            $enc[]=$idenc;
                        }
                        if($ficha==2472)
                        {
                            //echo $select;
                        }
                    

                        $fecha_ini=$row[fecha_ini];
                        $fecha_fin=$row[fecha_fin];

                        $fechaInicio = new DateTime($fecha_ini);
                        $fechaFin = new DateTime($fecha_fin);

                        $fechaI = $fechaInicio->format('Y-m-d');
                        $fechaF = $fechaFin->format('Y-m-d');
				
						$query="INSERT INTO reloj_detalle (id, id_encabezado, ficha, fecha, entrada, salmuerzo, ealmuerzo, salida, ent_emer, sal_emer) values ('','$idenc',".$fichaaux.",'".$fechaaux."', '".$entradaf."', '".$salmuf."', '".$ealmuf."', '".$salidaf."', '".$entradaEmerf."', '".$salidaEmerf."')";
						
						query($query,$conexion);
				  		

						$salidaEmer = $salidaEmerf = $entradaEmer = $entradaEmerf = $emergencia=$entrada=$salmu=$ealmu=$salida=$regular=$ausen=$tardan=$incapac=$sobret=$feriado="";
						$entradaf=$salmuf=$ealmuf=$salidaf="";
						$extrah1=$extrah2=$extrah3=$extra1=$extra2=$extra3=$tardanza=$extra=$extraExt=$extraNoc=$extraNocExt=$domingo="";
						$tardanza1=$tardanza2=$tardanza3=$extraNoc1=$extraNoc2=$extraNoc3=$nacional=$extraNac=$extraNocNac="";
						$extraDom="";
						$extraNocDom="";
						$extrah="";
						$extraMixDiurna="";
						$extraExtMixDiurna="";
						$extraMixNoc="";
						$extraExtMixNoc="";
						$disp_ids = "";
						if($ficha!=$fichaaux)
						{
							$fichaaux=$ficha;	
							$acumExtras = 0;
						}
						if($fecha==date('Y-m-d', strtotime($fecha. ' + 7 days')))
							$acumExtras = 0;

						$fechaaux=$fecha;
						$j=0;
					}					
					
					if($nocturno==0)
					{
						if($j==0)
						{
							$entrada = $horas;
							$disp_ids .= ",".$disp_id;
						} 
						elseif($j==1) 
						{
							$salmu = $horas;
							$disp_ids .= ",".$disp_id;
						}
						elseif($j==2) 
						{
							$ealmu = $horas;
							$disp_ids .= ",".$disp_id;
						}
						elseif($j==3) 
						{
							$salida = $horas;
							$disp_ids .= ",".$disp_id;
						}
						elseif($j==4)
						{
							$entradaEmer = $horas;
							$disp_ids .= ",".$disp_id;
						}
						elseif($j==5)
						{
							$salidaEmer = $horas;
							$disp_ids .= ",".$disp_id;
						}
					}
					else
					{
						if($j==0)
						{
							$entrada = $horas;
							$disp_ids .= ",".$disp_id;
						} 
						elseif($j==1)
						{
							$salida = $horas;
							$disp_ids .= ",".$disp_id;
						}
						elseif($j==2)
						{
							$entradaEmer = $horas;
							$disp_ids .= ",".$disp_id;
						}
						elseif($j==3)
						{
							$salidaEmer = $horas;
							$disp_ids .= ",".$disp_id;
						}
					}
			
					$j++;
					//$i++;
					$kk++;
				}
                //FIN PRIMER BLOQUE

				//exit;

                $encabezados = array_unique($enc);
                //SEGUNDO BLOQUE
                foreach($encabezados as $idenc)
 	            {
				    //echo $idenc;
                    $xxx=0;
				    $query_fix = "SELECT * FROM reloj_detalle WHERE id_encabezado = '$idenc' and ((entrada<>'' and salmuerzo='' and ealmuerzo='' and salida='') or (entrada<>'' and salida<>'' and ent_emer<>'' and sal_emer='') or (entrada='' and salmuerzo='' and ealmuerzo='' and salida='' and ent_emer<>'' and sal_emer<>'') or (entrada='' and salmuerzo='' and ealmuerzo='' and salida='' and ent_emer<>'' and sal_emer='')) ORDER BY id ASC";
				    $result_fix = query($query_fix,$conexion);
				    while($fetch_fix=fetch_array($result_fix))
				    {

					    if(($fetch_fix[entrada]!='') &&  ($fetch_fix[salida]!='') && ($fetch_fix[ent_emer]!='') && ($fetch_fix[sal_emer]==''))
					    {
						    //echo "<br>";
						    $query_fixx = "SELECT * FROM reloj_detalle WHERE id = '".($fetch_fix['id']+1)."'  and ficha = '$fetch_fix[ficha]'";
						    $result_fixx = query($query_fixx,$conexion);
						    $fetch_fixx=fetch_array($result_fixx);
						    if(($fetch_fixx[entrada]!='') &&  ($fetch_fixx[salida]=='') && ($fetch_fixx[ent_emer]=='') && ($fetch_fixx[sal_emer]==''))
						    {
							    if(substr($fetch_fix[ent_emer],0,2)=='23')
								    $entx="00:00";
							    else
								    $entx=$fetch_fix[ent_emer];
							    $query_fixxx = "UPDATE reloj_detalle SET salida = entrada, entrada = '".$entx."' WHERE id = '".$fetch_fixx[id]."' and ficha = '$fetch_fix[ficha]'";
							    $result_fixxx = query($query_fixxx,$conexion);

							    $query_fixxx = "UPDATE reloj_detalle SET ent_emer = ''  WHERE id = '".$fetch_fix[id]."'";
							    $result_fixxx = query($query_fixxx,$conexion);
							    $xxx=1;
						    }
					    }
					    elseif(($fetch_fix[entrada]=='') && ($fetch_fix[salmuerzo]=='') && ($fetch_fix[ealmuerzo]=='') && ($fetch_fix[salida]=='') && ($fetch_fix[ent_emer]!='') && ($fetch_fix[sal_emer]!=''))
					    {
						    $query_fixxx = "UPDATE reloj_detalle SET salida = sal_emer, entrada = ent_emer, sal_emer='', ent_emer='' WHERE id = '".$fetch_fix[id]."'";
						    $result_fixxx = query($query_fixxx,$conexion);
						    $xxx=1;
					    }
					    elseif(($fetch_fix[entrada]=='') && ($fetch_fix[salmuerzo]=='') && ($fetch_fix[ealmuerzo]=='') && ($fetch_fix[salida]=='') && ($fetch_fix[ent_emer]!='') && ($fetch_fix[sal_emer]==''))
					    {
						    $query_fixxx = "UPDATE reloj_detalle SET entrada = '".$fetch_fix[ent_emer]."' WHERE id = '".($fetch_fix[id])."' and ficha = '$fetch_fix[ficha]'";
						    $result_fixxx = query($query_fixxx,$conexion);
						    $query_fixxx = "UPDATE reloj_detalle SET ent_emer = '' WHERE id = '".($fetch_fix[id])."' and ficha = '$fetch_fix[ficha]'";
						    $result_fixxx = query($query_fixxx,$conexion);

						
						    $xxx=1;
					    }
					
					    if($xxx==1)
					    {
						    unset($result_fix);
						    $query_fix = "SELECT * FROM reloj_detalle WHERE id_encabezado = '$idenc' and ((entrada<>'' and salmuerzo='' and ealmuerzo='' and salida='') or (entrada<>'' and salida<>'' and ent_emer<>'' and sal_emer='') or (entrada='' and salmuerzo='' and ealmuerzo='' and salida='' and ent_emer<>'' and sal_emer<>'') or (entrada='' and salmuerzo='' and ealmuerzo='' and salida='' and ent_emer<>'' and sal_emer='')) ORDER BY id ASC";
						    $result_fix = query($query_fix,$conexion);
						    $xxx=0;
					    }
				    }
				
				    $xxx=0;
				    $query_fix = "SELECT * FROM reloj_detalle WHERE id_encabezado = '$idenc' and ((entrada<>'' and salmuerzo='' and ealmuerzo='' and salida='') or (entrada<>'' and salida<>'' and ent_emer<>'' and sal_emer='') or (entrada='' and salmuerzo='' and ealmuerzo='' and salida='' and ent_emer<>'' and sal_emer<>'') or (entrada='' and salmuerzo='' and ealmuerzo='' and salida='' and ent_emer<>'' and sal_emer='')) ORDER BY id ASC";
				    $result_fix = query($query_fix,$conexion);
				    while($fetch_fix=fetch_array($result_fix))
				    {
					    $ficha = $fetch_fix[ficha];
					    $fecha = $fetch_fix[fecha];
					    $query="select tur.* from nomturnos tur join nomcalendarios_personal per on tur.turno_id=per.turno_id where per.ficha='$ficha' and per.fecha='$fecha'";
					    $resulttur=query($query,$conexion);
					
					    $filaTur = fetch_array($resulttur);
					    $turno = trim($filaTur[descripcion]);
					    $libre = $filaTur[libre];
					    $tipo = $filaTur[tipo];
					    $descpago = $filaTur[descpago];
					    $tiempo = new tiempo();
					
					    $entrada1 = $tiempo->aminutos(substr($filaTur[entrada], 0,5));
					    $saldesc1 = $tiempo->aminutos(substr($filaTur[inicio_descanso],0,2)=="00" ? "24".substr($filaTur[inicio_descanso], 2,3) : substr($filaTur[inicio_descanso], 0,5));
					    $entdesc1 = $tiempo->aminutos(substr($filaTur[salida_descanso], 0,5));
					    $salida1 = $tiempo->aminutos(substr($filaTur[salida],0,2)=="00" ? "24".substr($filaTur[salida], 2,3) : substr($filaTur[salida], 0,5));
					

					    if(($fetch_fix[entrada]!='') && ($fetch_fix[salmuerzo]=='') && ($fetch_fix[ealmuerzo]=='') && ($fetch_fix[salida]=='') && ($fetch_fix[ent_emer]=='') && ($fetch_fix[sal_emer]==''))
					    {
						    $it = 1;
						    do
						    {
							    $query_fixx = "SELECT * FROM reloj_detalle WHERE id = '".($fetch_fix['id']+$it)."' and ficha = '$ficha'";
							    $result_fixx = query($query_fixx,$conexion);
							    $fetch_fixx = fetch_array($result_fixx);
							    $fecha2 = $fetch_fixx[fecha];
							    $it++;
						    }
						    while(($fecha2=="")&&($it<=10));

						    $query="select tur.* from nomturnos tur join nomcalendarios_personal per on tur.turno_id=per.turno_id where per.ficha='$ficha' and per.fecha='$fecha2'";
						    $resulttur2=query($query,$conexion);
						
						    $filaTur2 = fetch_array($resulttur2);
						    $turno2 = trim($filaTur2[descripcion]);
						    $libre2 = $filaTur2[libre];
						    $tipo2 = $filaTur2[tipo];
						    $descpago2 = $filaTur2[descpago];
												
						    $entrada12 = $tiempo->aminutos(substr($filaTur2[entrada], 0,5));
						    $saldesc12 = $tiempo->aminutos(substr($filaTur2[inicio_descanso],0,2)=="00" ? "24".substr($filaTur2[inicio_descanso], 2,3) : substr($filaTur2[inicio_descanso], 0,5));
						    $entdesc12 = $tiempo->aminutos(substr($filaTur2[salida_descanso], 0,5));
						    $salida12 = $tiempo->aminutos(substr($filaTur2[salida],0,2)=="00" ? "24".substr($filaTur2[salida], 2,3) : substr($filaTur2[salida], 0,5));

						    $entradamin = $tiempo->aminutos($fetch_fix[entrada]);
						    $entradamin2 = $tiempo->aminutos($fetch_fixx[entrada]);
						    $salidamin = $tiempo->aminutos($fetch_fix[salida]);

						    if($entrada12==0)
							    $entrada12x=1440;
						    else
							    $entrada12x=$entrada12;


						    if((($fetch_fixx[entrada]!='') &&  ($fetch_fixx[salida]=='') && ($fetch_fixx[ent_emer]=='') && ($fetch_fixx[sal_emer]=='')) && (($entradamin>=($entrada12x-60))&&($entradamin<=$entrada12x))&&($entrada12x>=1380))
						    {
							
								    $entx=substr($filaTur2[entrada], 0,5);
							
							
							    $query_fixxx = "UPDATE reloj_detalle SET salida = entrada, entrada = '".$entx."' WHERE id = '".$fetch_fixx[id]."'";
							    $result_fixxx = query($query_fixxx,$conexion);

							    $query_fixxx = "delete from reloj_detalle  WHERE id = '".$fetch_fix[id]."'";
							    $result_fixxx = query($query_fixxx,$conexion);
							    $xxx=1;
						    }
						    elseif((($fetch_fixx[entrada]=='') &&  ($fetch_fixx[salida]!='') && ($fetch_fixx[ent_emer]=='') && ($fetch_fixx[sal_emer]=='')) && (($entradamin>=($entrada12x-60))&&($entradamin<=$entrada12x))&&($entrada12x>=1380))
						    {
							
								    $entx=substr($filaTur2[entrada], 0,5);//$entx="00:00";
							
							    $query_fixxx = "UPDATE reloj_detalle SET  entrada = '".$entx."' WHERE id = '".$fetch_fixx[id]."'";
							    $result_fixxx = query($query_fixxx,$conexion);
							
							
							    $query_fixxx = "delete from reloj_detalle  WHERE id = '".$fetch_fix[id]."'";
							    $result_fixxx = query($query_fixxx,$conexion);
							    $xxx=1;
						    }
						    elseif((($fetch_fixx[entrada]!='') &&  ($fetch_fixx[salida]=='') && ($fetch_fixx[ent_emer]=='') && ($fetch_fixx[sal_emer]=='')) && (($entradamin>=($entrada1-60))&&($entradamin<=($entrada1+120)))&&((($entradamin2+1440)>=$salida1)&&(($entradamin2+1440)<=($salida1+120))))
						    {
							
								    $entx=substr($filaTur[salida],0,5);
							
							    $query_fixxx = "UPDATE reloj_detalle SET  salida = '".$entx."', salida_diasiguiente = '".$fetch_fixx[entrada]."' WHERE id = '".$fetch_fix[id]."'";
							    $result_fixxx = query($query_fixxx,$conexion);

							    $query_fixxx = "UPDATE reloj_detalle SET  entrada = salida WHERE id = '".$fetch_fixx[id]."'";
							    $result_fixxx = query($query_fixxx,$conexion);

							    $query_fixxx = "UPDATE reloj_detalle SET  salida = '' WHERE id = '".$fetch_fixx[id]."'";
							    $result_fixxx = query($query_fixxx,$conexion);
							    $xxx=1;
						    }
						    elseif((($fetch_fixx[entrada]!='') &&  ($fetch_fixx[salida]!='') && ($fetch_fixx[ent_emer]=='') && ($fetch_fixx[sal_emer]=='')) && (($entradamin>=($entrada1-60))&&($entradamin<=($entrada1+120)))&&((($entradamin2+1440)>=$salida1)&&(($entradamin2+1440)<=($salida1+120))))
						    {
							
								    $entx=substr($filaTur[salida],0,5);
							
							    $query_fixxx = "UPDATE reloj_detalle SET  salida = '".$entx."', salida_diasiguiente = '".$fetch_fixx[entrada]."' WHERE id = '".$fetch_fix[id]."'";
							    $result_fixxx = query($query_fixxx,$conexion);

							    $query_fixxx = "UPDATE reloj_detalle SET  entrada = salida WHERE id = '".$fetch_fixx[id]."'";
							    $result_fixxx = query($query_fixxx,$conexion);
							
							    $query_fixxx = "UPDATE reloj_detalle SET  salida = '' WHERE id = '".$fetch_fixx[id]."'";
							    $result_fixxx = query($query_fixxx,$conexion);
							    $xxx=1;
						    }
						    elseif((($fetch_fixx[entrada]!='') &&  ($fetch_fixx[salida]!='') && ($fetch_fixx[ent_emer]=='') && ($fetch_fixx[sal_emer]=='')) && (($entradamin>=($entrada12+1440-60))&&($entradamin<=($entrada12+1560)))&&((($entradamin2)>=$salida12-60)&&(($entradamin2)<=($salida12+60))))
						    {
							
								    $entx=substr($filaTur[salida],0,5);
							
							
							    $query_fixxx = "UPDATE reloj_detalle SET  ent_emer = salida, salida = entrada, entrada = '".$fetch_fix[entrada]."' WHERE id = '".$fetch_fixx[id]."'";
							    $result_fixxx = query($query_fixxx,$conexion);

							    $query_fixxx = "DELETE FROM reloj_detalle  WHERE id = '".$fetch_fix[id]."'";
							    $result_fixxx = query($query_fixxx,$conexion);
							
							
							    $xxx=1;
						    }


						
					    }
					    elseif((($fetch_fix[entrada]!='') &&  ($fetch_fix[salida]!='') && ($fetch_fix[ent_emer]!='') && ($fetch_fix[sal_emer]=='')))
					    {
						    $it = 1;
						    do
						    {
							    $query_fixx = "SELECT * FROM reloj_detalle WHERE id = '".($fetch_fix['id']+$it)."' and ficha = '$ficha'";
							    $result_fixx = query($query_fixx,$conexion);
							    $fetch_fixx = fetch_array($result_fixx);
							    $fecha2 = $fetch_fixx[fecha];
							    $it++;
						    }
						    while(($fecha2=="")&&($it<=10));

						    $query="select tur.* from nomturnos tur join nomcalendarios_personal per on tur.turno_id=per.turno_id where per.ficha='$ficha' and per.fecha='$fecha2'";
						    $resulttur2=query($query,$conexion);
						
						    $filaTur2 = fetch_array($resulttur2);
						    $turno2 = trim($filaTur2[descripcion]);
						    $libre2 = $filaTur2[libre];
						    $tipo2 = $filaTur2[tipo];
						    $descpago2 = $filaTur2[descpago];
												
						    $entrada12 = $tiempo->aminutos(substr($filaTur2[entrada], 0,5));
						    $saldesc12 = $tiempo->aminutos(substr($filaTur2[inicio_descanso],0,2)=="00" ? "24".substr($filaTur2[inicio_descanso], 2,3) : substr($filaTur2[inicio_descanso], 0,5));
						    $entdesc12 = $tiempo->aminutos(substr($filaTur2[salida_descanso], 0,5));
						    $salida12 = $tiempo->aminutos(substr($filaTur2[salida],0,2)=="00" ? "24".substr($filaTur2[salida], 2,3) : substr($filaTur2[salida], 0,5));

						    $entradamin = $tiempo->aminutos($fetch_fix[ent_emer]);
						    $entradamin2 = $tiempo->aminutos($fetch_fixx[entrada]);
						    $salidamin = $tiempo->aminutos($fetch_fix[salida]);

						    if($entrada12==0)
							    $entrada12x=1440;
						    else
							    $entrada12x=$entrada12;

					 	    if((($entradamin>=($entrada12x-60))&&($entradamin<=($entrada12x+120)))&&((($entradamin2)>=$salida12-60)&&(($entradamin2)<=($salida12+60))))
						    {
							
							
							    $query_fixxx = "UPDATE reloj_detalle SET  ent_emer = salida, salida = entrada, entrada = '".$fetch_fix[ent_emer]."' WHERE id = '".$fetch_fixx[id]."'";
							    $result_fixxx = query($query_fixxx,$conexion);
							    $query_fixxx = "UPDATE reloj_detalle SET  ent_emer = '' WHERE id = '".$fetch_fix[id]."'";
							    $result_fixxx = query($query_fixxx,$conexion);					
							
							
							    $xxx=1;
						    }

					    }
					
					    if($xxx==1)
					    {
						    unset($result_fix);
						    $query_fix = "SELECT * FROM reloj_detalle WHERE id_encabezado = '$idenc' and ((entrada<>'' and salmuerzo='' and ealmuerzo='' and salida='') or (entrada<>'' and salida<>'' and ent_emer<>'' and sal_emer='') or (entrada='' and salmuerzo='' and ealmuerzo='' and salida='' and ent_emer<>'' and sal_emer<>'') or (entrada='' and salmuerzo='' and ealmuerzo='' and salida='' and ent_emer<>'' and sal_emer='')) ORDER BY id ASC";
						    $result_fix = query($query_fix,$conexion);
						    $xxx=0;
					    }
				    }

				    $query_fix = "SELECT * FROM reloj_detalle WHERE id_encabezado = '$idenc' and SUBSTRING(entrada,1,2)='23' ";
				    $result_fix = query($query_fix,$conexion);
				    while($fetch_fix=fetch_array($result_fix))
				    {
					    $ficha = $fetch_fix[ficha];
					    $fecha = $fetch_fix[fecha];
					    $query="select tur.* from nomturnos tur join nomcalendarios_personal per on tur.turno_id=per.turno_id where per.ficha='$ficha' and per.fecha='$fecha'";
					    $resulttur=query($query,$conexion);
					
					    $filaTur = fetch_array($resulttur);
					
						
					    if(substr($filaTur[entrada], 0,5)=="00:00")
					    {
						    $entx = substr($filaTur[entrada], 0,5);
						    $query_fixxx = "UPDATE reloj_detalle SET  entrada = '".$entx."' WHERE id = '".$fetch_fix[id]."'";
						    $result_fixxx = query($query_fixxx,$conexion);
					    }

				    }

				    $consulta="SELECT * FROM reloj_detalle WHERE id_encabezado = '$idenc' ORDER BY  id asc;";
				    $resultadox = query($consulta,$conexion);

				    $continue = $i = $kk = 0;
				    //$linecount=$linecount-2;
				    $band = $acumExtras = 0;
				    $disp_id = "";
				    $disp_ids = "";
				    while($fetchx=fetch_array($resultadox))
				    {
					    $ficha = $fetchx[ficha];
					    $fecha = $fetchx[fecha];
					    $query="select tur.* from nomturnos tur join nomcalendarios_personal per on tur.turno_id=per.turno_id where per.ficha='$ficha' and per.fecha='$fecha'";
					    $result=query($query,$conexion);
					
					    $fila = fetch_array($result);
					    $turno = trim($fila[descripcion]);
					    $libre = $fila[libre];
					    $tipo = $fila[tipo];
					    $descpago = $fila[descpago];
					    $tiempo = new tiempo();
					    $entrada0 = $tiempo->aminutos(substr($fila[tolerancia_llegada], 0,5));
					    $entrada1 = $tiempo->aminutos(substr($fila[entrada], 0,5));
					    $entradatol = $tiempo->aminutos(substr($fila[tolerancia_entrada], 0,5));

					    $saldesc1 = $tiempo->aminutos(substr($fila[inicio_descanso],0,2)=="00" ? "24".substr($fila[inicio_descanso], 2,3) : substr($fila[inicio_descanso], 0,5));
					    $entdesc1 = $tiempo->aminutos(substr($fila[salida_descanso], 0,5));
					    $entdesctol = $tiempo->aminutos(substr($fila[tolerancia_descanso],0,2)=="00" ? "24".substr($fila[tolerancia_descanso], 2,3) : substr($fila[tolerancia_descanso], 0,5));
					    $salida1 = $tiempo->aminutos(substr($fila[salida],0,2)=="00" ? "24".substr($fila[salida], 2,3) : substr($fila[salida], 0,5));
					    $salidatol = $tiempo->aminutos(substr($fila[tolerancia_salida],0,2)=="00" ? "24".substr($fila[tolerancia_salida], 2,3) : substr($fila[tolerancia_salida], 0,5));
					    $descanso = $entdesc1 - $saldesc1;
					
					    $lim1 = $saldesc1 - $entrada1;
					    $lim2 = $salida1 - $entdesc1;
					    //echo $lim2->format('%H:%I');

					    $lim = $lim1 + $lim2;

					    $dialibre = $regular=$ausen=$tardan=$incapac=$sobret=$feriado="";
					    $entradaf=$salmuf=$ealmuf=$salidaf="";
					    $tardanza=$extra=$domingo="";
					
					    $extra=$regular=$regular1=$regular2=$regular3=$regular4="";
					    $extra=$extra1=$extra2=$extra3=$extra4="";
					    $extraNoc=$extraNoc1=$extraNoc2=$extraNoc3=$extraNoc4="";
					    $extraExt=$extraExt1=$extraExt2=$extraExt3=$extraExt4="";
					    $extraNocExt=$extraNocExt1=$extraNocExt2=$extraNocExt3=$extraNocExt4="";
					
					    $hExt = $tiempo->aminutos("03:00");
					    $limiteExt = $tiempo->aminutos("18:00");
					    $limiteExtFin = $tiempo->aminutos("06:00");
					    $limiteExtNoc = $tiempo->aminutos("24:00");		
					    $limite = $lim;

					    $entrada = $fetchx[entrada];
					    $salmu = $fetchx[salmuerzo];
					    $ealmu = $fetchx[ealmuerzo];
					    $salida = $fetchx[salida];
					    $salidaDS = $fetchx[salida_diasiguiente];					
					    $entradaEmer = $fetchx[ent_emer];
					    $salidaEmer = $fetchx[sal_emer];
					    $regular1 = $regular2 = 0;


					    if($entrada!="")
					    {
						    $entradaf = $entrada;
						    $entrada = substr($entrada, 0,2)=="24" ? "00".substr($entrada, 2,5) : substr($entrada, 0,5);
						    $entrada = $tiempo->aminutos($entrada);
					    } 
					    if($salmu!="") 
					    {
						    $salmuf = $salmu;
						    $salmu = $tiempo->aminutos($salmu);
					    }
					    if($ealmu!="")
					    {
						    $ealmuf = $ealmu;
						    $ealmu = $tiempo->aminutos($ealmu);
					    }
					    if($salida!="") 
					    {
						    $salidaPDS = $tiempo->aminutos(substr($salida, 0,5));
						    $salida = substr($salida, 0,2)=="00" ? "24".substr($salida, 2,3) : substr($salida, 0,5);
						    $salidaf = $salida;
						    $salida = $tiempo->aminutos($salida);
					    }
					    if($entradaEmer!="")
					    {
						    $entradaEmerf = $entradaEmer;
						    $entradaEmer = $tiempo->aminutos($entradaEmer);
					    }
					    if($salidaEmer!="")
					    {
						    $salidaEmerf = $salidaEmer;
						    $salidaEmer = $tiempo->aminutos($salidaEmer);
					    }
					    if($salidaDS!="")
					    {
						    $salidaDSf = $salidaDS;
						    $salidaDS = $tiempo->aminutos($salidaDS);
					    }
					    $extra11=$extra=$regular=$regular1=$regular2=$regular3=$extrah="";
					    $tardanza1=$tardanza2=$tardanza3=$tardanza4="";
					    $extrah1=$extrah2=$extrah3=$extrah4="";

					    $band = 0;
					    //echo $entrada0->format('H:i');
					    if(($entrada != "") && ($salmu != ""))
					    {
						
						    if($entrada < $entrada0)
						    {
							    if($entrada < $limiteExtFin)
							    {
								    $extraNoc1 = $limiteExtFin-$entrada;
								    $extra1 = $entrada1-$limiteExtFin;
							    }
							    else
							    {
								    $extra1 = $entrada1-$entrada;
							    }
							    $regular1 = $salmu - $entrada1;
						    }
						    elseif($entrada > $entradatol)
						    {
							    $tardanza1 = $entrada-$entrada1;
							    $regular1 = $salmu - $entrada;
						    }
						    elseif(($entrada >= $entrada0) && ($entrada <= $entradatol))
						    {
							    $regular1 = $salmu - $entrada1;
						    }
						
						    //PARA LA HORA EXTRA Y TARDANZA DE ALMUERZO
						    if($libre == 0)
						    {
							    if($salmu < $saldesc1)
							    {
								    $tardanza3 = $salmu - $saldesc1;
							    }
							    elseif(($salmu > $saldesc1) && ($salmu <= $entdesc1))
							    {
								    $extrah1 = $saldesc1->diff($salmu);
							    }
						    }
						    $band=1;			
					    }
					    if(($ealmu != "") && ($salida != ""))
					    {
						    //PARA LA HORA EXTRA Y TARDANZA DE ALMUERZO
						    if($libre == 0)
						    {
							    if($ealmu > $entdesctol)
							    {
								    $tardanza4 = $ealmu - $entdesc1;
							    }
							    elseif($ealmu <= $entdesc1)
							    {
								    $extrah2 = $entdesc1 - $ealmu;
								    ////////AQUI
								    //$regular2 = $salida - $entdesc1;
								    $ealmu = $entdesc1;
							    }
							    elseif($salmu > $entdesc1)
							    {
								    $extrah4 = $salmu - $ealmu;
							    }
						    }

						    if($salida > $salidatol)
						    {
							    if($salida > $limiteExt)
							    {
								
								
								    if($salida1<$limiteExt)
								    {
									    $extraNoc2 = $salida - $limiteExt;
									    $extra2 = $limiteExt - $salida1;
								    }
								    else
								    {
									    $extraNoc2 = $salida - $salida1;
								    }
								    $salida = $salida1;
							    }
							    else
							    {
								    $extra2 = $salida1 - $salida;
								    $salida = $salida1;
							    }
							
						    }
						    elseif(($salida >= $salida1) && ($salida <= $salidatol))
						    {
							    $salida = $salida1;
						    }
						    elseif($salida < $salida1)
						    {
							    $tardanza2 = $salida1 - $salida;
						    }
						    $regular2 = $salida - $ealmu;
						

						    if(($regular1>0)&&($regular2>0))
							    $regular = $regular1 + $regular2;
						    elseif($regular1>0)
							    $regular = $regular1;
						    elseif($regular2>0)
							    $regular = $regular2;
						
						    ///////////////////

						    if(($extra1 != "") && ($extra2 != ""))
							    $extra = $extra1 + $extra2;
						    elseif($extra1 != "")
							    $extra = $extra1;
						    elseif($extra2 != "")
							    $extra = $extra2;

						    if(($extraNoc1 != "") && ($extraNoc2 != ""))
							    $extraNoc = $extraNoc2 + $extraNoc1;
						    elseif($extraNoc1 != "")
							    $extraNoc = $extraNoc1;
						    elseif($extraNoc2 != "")
							    $extraNoc = $extraNoc2;
						    $band=1;
					    }

					    if(($entrada !== "") && ($salida !== "") && ($band == 0))
					    {
						
						    if($salida > $limiteExt && $salidatol < $limiteExt)
						    {
							    $extra2 = $limiteExt - $salida1;
							    $extraNoc2 = $salida - $limiteExt;
							    $salida = $salida1;
						    }
						    elseif($entrada<$entrada0)
						    {
							    if($entrada < $limiteExtFin)
							    {
								    $extraNoc1 = $limiteExtFin-$entrada;
							    }
							    else
							    {
								    $extra1 = $entrada1-$entrada;
							    }
							    $entrada = $entrada1;
						    }
						    elseif($entrada > $entradatol)
						    {
							    $tardanza1 = $entrada - $entrada1;
						    }
						    elseif(($entrada>=$entrada0)&&($entrada<=$entradatol))
						    {
							    $entrada = $entrada1;
						    }
						    if($salida > $salidatol)
						    {

							    if($salida > $limiteExt)
							    {
								    $extraNoc2 = $salida - $salida1;
								    $salida = $salida1;
							
							    }
							    elseif($salida <= $limiteExt)
							    {
								    $extra2 = $salida - $salida1;
								    $salida = $salida1;
							    }
						    }
						    elseif($salida < $salida1)
						    {
							    $tardanza2 = $salida1 - $salida;

						    }
						    if($salida <= $salidatol && $salida >= $salida1)
						    {
							    $salida = $salida1;
						    }
						    $regular = $salida - $entrada;
						    if($descpago==0 && $regular > 480)
						    {
							
							    $regular = $limite;
						    }
						    if(($extra1 != "") && ($extra2 != ""))
							    $extra = $extra1 + $extra2;							
						    elseif($extra1 != "")
							    $extra = $extra1;
						    elseif($extra2 != "")
							    $extra = $extra2;

						    if($salidaDS)
						    {
							    $regular += ($salidaDS - $salidaPDS);
						    }

						    if($regular>$limite)
						    {
							    if($extra!="")
								    $extra += ($regular - $limite);
							    else
								    $extra = ($regular - $limite);
							    $regular=$limite;
						    }


						    if(($extraNoc1 != "") && ($extraNoc2 != ""))
							    $extraNoc = $extraNoc1 + $extraNoc2;
						    elseif($extraNoc1!="")
							    $extraNoc = $extraNoc1;
						    elseif($extraNoc2!="")
							    $extraNoc = $extraNoc2;
					    }

					    if(($libre == 1) && ($salmu != "") && ($ealmu != ""))
					    {
						    $talmutol = $entdesctol - $saldesc1;
						
						    $talmu = $entdesc1 - $saldesc1;

						    $talmur = $ealmu - $salmu;

						    if($talmur>$talmutol)
						    {
							    $tardanza3 = $talmur - $talmu;
						    }
						    elseif($talmur<=$talmu)
						    {
							    $extrah1 = $talmu - $talmur;
						    }
					    }

					    if(($extrah1 != "") && ($extrah2 != ""))
					    {
						    $extrah = $extrah1 + $extrah2;
					    }
					    elseif($extrah1 != "")
						    $extrah = $extrah1;
					    elseif($extrah2 != "")
						    $extrah = $extrah2;

					    if(($regular == "") && ($regular1 != ""))
						    $regular = $regular1;

				
					
					    if($extra > $hExt)
					    {
						    $extraExt = $extra - $hExt;
						    $extra = $hExt;
					    }

					    if($extraNoc>$hExt)
					    {
						    $extraNocExt = $extraNoc - $hExt;
						    $extraNoc = $hExt;
					    }

					    if(($tardanza1 != "") && ($tardanza2 != ""))
						    $tardanza = $tardanza1 + $tardanza2;
					    elseif($tardanza1)
						    $tardanza = $tardanza1;
					    elseif($tardanza2)
						    $tardanza = $tardanza2;

					    if($tardanza3)
					    {
						    if($tardanza)
							    $tardanza += $tardanza3;
						    else
							    $tardanza = $tardanza3;
					    }

					    if($tardanza4)
					    {
						    if($tardanza)
							    $tardanza += $tardanza4;
						    else
							    $tardanza = $tardanza4;
					    }
					

					    if($regular!="")
					    {
						
						    if(($limite==420)&&($regular>=$limite)&&($salida>=$salida1))
						    {
							    $regular=480;
						    }

						    if($regular>=$limite)
						    {
							    $regular=$limite;
						    }
					    }
					    $descansoincompleto = 0;
					    if(($entradaEmer != "") && ($salidaEmer != ""))
					    {
						    $emergencia = $salidaEmer - $entradaEmer;
						    if($emergencia >= 180 )
						    {
							    $regular += $emergencia;
							    $descansoincompleto = $entradaEmer - $salida;
							    $emergencia = 0;
						    }
					    }

					    if($regular <= 0)
						    $regular = "";

					    if($extra <= 0)
						    $extra = "";

					    if($tipo==6)
					    {	
						    $dialibre=$regular;
						    $regular="";
						
						    if($tipo==3)
						    {
							    $extraMixDiurna=$extra;
							    $extraExtMixDiurna=$extraExt;
							

							    $extra = "";
							    $extraNoc = "";
							    $extraExt="";
							    $extraNocExt="";
						    }
						    elseif($tipo==4)
						    {
							    $extraMixNoc=$extra;
							    $extraExtMixNoc=$extraExt;
							

							    $extra = "";
							    $extraNoc = "";
							    $extraExt="";
							    $extraNocExt="";
						    }

					    }
					
					    if(date("w",strtotime($fecha))==0)
					    {	
						    $domingo=$regular;
						    $regular="";
						
						    if($tipo==3)
						    {
							    $extraMixDiurna=$extra;
							    $extraExtMixDiurna=$extraExt;
							

							    $extra = "";
							    $extraNoc = "";
							    $extraExt="";
							    $extraNocExt="";
						    }
						    elseif($tipo==4)
						    {
							    $extraMixNoc=$extra;
							    $extraExtMixNoc=$extraExt;
							

							    $extra = "";
							    $extraNoc = "";
							    $extraExt="";
							    $extraNocExt="";
						    }

					    }

					    $query="select dia_fiesta from nomcalendarios_tiposnomina where fecha='$fecha'";
					    $result=query($query,$conexion);
					    $fetch = fetch_array($result);

					    if($fetch[dia_fiesta]==3)
					    {
						    $nacional=$regular;
						    $regular="";
						
						    if($tipo==3)
						    {
							    $extraMixDiurna=$extra;
							    $extraExtMixDiurna=$extraExt;
							    $extraMixNoc=$extraNoc;
							    $extraExtMixNoc=$extraExtNoc;

							    $extra = "";
							    $extraNoc = "";
							    $extraExt="";
							    $extraNocExt="";
						    }
					    }
					    if(($tipo==3)&&(date("w",strtotime($fecha))!=0))
					    {
						    $extraMixDiurna=$extra;
						    $extraExtMixDiurna=$extraExt;

						    $extra = "";
						    $extraNoc = "";
						    $extraExt="";
						    $extraNocExt="";
					    }
					    if(($tipo==4)&&(date("w",strtotime($fecha))!=0))
					    {
						    $extraMixNoc=$extra;
						    $extraExtMixNoc=$extraExt;

						    $extra = "";
						    $extraNoc = "";
						    $extraExt="";
						    $extraNocExt="";
					    }
					

					    if($acumExtras<540)
					    {
						
						    if($acumExtras+$extra>540)
						    {
							    $extraAux = $extra;
							    $extra = 540 - $acumExtras;
							    $extraExt += ($acumExtras + $extraAux) - 540;
							    $acumExtras += $extraAux;
						    }

						    if($acumExtras+$extraNoc>540)
						    {
							    $extraNocAux = $extraNoc;
							    $extraNoc = 540 - $acumExtras;
							    $extraExtNoc += ($acumExtras + $extraNocAux) - 540;
							    $acumExtras += $extraNocAux;
						    }

						    if($acumExtras+$extraMixDiurna>540)
						    {
							    $extraMixDiurnaAux = $extraMixDiurna;
							    $extraMixDiurna = 540 - $acumExtras;
							    $extraExtMixDiurna += ($acumExtras + $extraMixDiurnaAux) - 540;
							    $acumExtras += $extraMixDiurnaAux;
						    }

						    if($acumExtras+$extraMixNoc>540)
						    {
							    $extraMixNocAux = $extraMixNoc;
							    $extraMixNoc = 540 - $acumExtras;
							    $extraExtMixNoc += ($acumExtras + $extraMixNocAux) - 540;
							    $acumExtras += $extraMixNocAux;
						    }
					    }
					    elseif($acumExtras>=540)
					    {
						    if($acumExtras+$extra>540)
						    {
							
							    $extraExt += $extra;
							    $acumExtras += $extraExt;
							    $extra = 0;
						    }

						    if($acumExtras+$extraNoc>540)
						    {
							
							    $extraExtNoc += $extraNoc;
							    $acumExtras += $extraExtNoc;
							    $extraNoc = 0;
						    }

						    if($acumExtras+$extraMixDiurna>540)
						    {
							    $extraExtMixDiurna += $extraMixDiurna;
							    $acumExtras += $extraExtMixDiurna;
							    $extraMixDiurna = 0;
						    }

						    if($acumExtras+$extraMixNoc>540)
						    {
							    $extraExtMixNoc += $extraMixNoc;
							    $acumExtras += $extraExtMixNoc;
							    $extraMixNoc = 0;
						    }
					    }
					

					
					    $query="UPDATE reloj_detalle SET ordinaria = '".$tiempo->ahoras($regular)."', extra = '".$tiempo->ahoras($extra)."', extraext = '".$tiempo->ahoras($extraExt)."', extranoc =  '".$tiempo->ahoras($extraNoc)."',  extraextnoc = '".$tiempo->ahoras($extraNocExt)."', domingo = '".$tiempo->ahoras($domingo)."', tardanza = '".$tiempo->ahoras($tardanza)."', nacional = '".$tiempo->ahoras($nacional)."', extranac = '".$tiempo->ahoras($extraNac)."',  extranocnac = '".$tiempo->ahoras($extraNocNac)."', descextra1 = '".$tiempo->ahoras($extrah)."', mixtodiurna = '".$tiempo->ahoras($extraMixDiurna)."', mixtoextdiurna = '".$tiempo->ahoras($extraExtMixDiurna)."', mixtonoc = '".$tiempo->ahoras($extraMixNoc)."', mixtoextnoc = '".$tiempo->ahoras($extraExtMixNoc)."', dialibre = '".$tiempo->ahoras($dialibre)."',  emergencia = '".$tiempo->ahoras($emergencia)."', descansoincompleto = '".$tiempo->ahoras($descansoincompleto)."' WHERE id = '".$fetchx[id]."' ";
					    //echo "<br>";
					    query($query,$conexion);
			  		    //echo "<br>";
					    //exit;

					    $dialibre = $salidaEmer = $salidaEmerf = $entradaEmer = $entradaEmerf = $emergencia=$entrada=$salmu=$ealmu=$salida=$regular=$ausen=$tardan=$incapac=$sobret=$feriado="";
					    $regular1 = $regular2 = $regular3 = $regular4 = $regular5x = $regularx = $entradaf=$salmuf=$ealmuf=$salidaf="";
					    $extrah1=$extrah2=$extrah3=$extra1=$extra2=$extra3=$tardanza=$extra=$extraExt=$extraNoc=$extraNocExt=$domingo="";
					    $tardanza1=$tardanza2=$tardanza3=$extraNoc1=$extraNoc2=$extraNoc3=$nacional=$extraNac=$extraNocNac="";
					    $extraDom="";
					    $extraNocDom="";
					    $extrah="";
					    $extraMixDiurna="";
					    $extraExtMixDiurna="";
					    $extraMixNoc="";
					    $extraExtMixNoc="";
					    $band = 0;
					    $j=0;
					
				    }
                }
                fclose($f);
                $archivo_pendiente    = $pendientes.$file;
                $archivo_procesado    = $procesados.$file;
                rename($archivo_pendiente, $archivo_procesado);
            }
        }
    }
}
?>