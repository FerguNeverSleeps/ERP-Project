<?php
error_reporting(E_ALL^E_NOTICE);
include("../lib/common.php");

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

$fecha_actual= date("Y-m-d");
$fecha_anterior= date("Y-m-d", strtotime('-1 day')) ;

$datos_turno="SELECT 
     turno_id,
     descripcion,
     entrada,
     tolerancia_entrada,
     inicio_descanso,
     salida_descanso,
     tolerancia_descanso,
     salida,
     tolerancia_salida,
     tolerancia_llegada
FROM
     nomturnos
WHERE 
    turno_id=75";
$resultado_turno=query($datos_turno,$conexion);
$fila = fetch_array($resultado_turno);
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

$datos_detalle="SELECT     
        detalle.id,
        detalle.id_encabezado,
        detalle.ficha,
        detalle.fecha,
        detalle.entrada,
        detalle.salmuerzo,
        detalle.ealmuerzo,
        detalle.salida,
        calendario.ficha,
        calendario.fecha     
FROM
        reloj_detalle as detalle
        INNER JOIN nomcalendarios_personal as calendario ON detalle.ficha = calendario.ficha AND detalle.fecha = calendario.fecha     
WHERE 
        detalle.fecha='$fecha_anterior' AND calendario.turno_id=75
AND (
        (entrada<>'' and salmuerzo='' and ealmuerzo='' and salida='') 
        OR (entrada<>'' and salida<>'' and ent_emer<>'' and sal_emer='') 
        OR (entrada='' and salmuerzo='' and ealmuerzo='' and salida='' and ent_emer<>'' and sal_emer<>'')
        OR (entrada='' and salmuerzo='' and ealmuerzo='' and salida='' and ent_emer<>'' and sal_emer='')
    )
ORDER BY  detalle.ficha, detalle.fecha";

$resultado_detalle = query($query_fix,$conexion);
while($fetch_detalle=fetch_array($resultado_detalle))
{
    $id = $fetch_detalle[id];
    $ficha = $fetch_detalle[ficha];
    $fecha = $fetch_detalle[fecha];
    
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

    $entrada = $fetch_detalle[entrada];
    $salmu = $fetch_detalle[salmuerzo];
    $ealmu = $fetch_detalle[ealmuerzo];
    $salida = $fetch_detalle[salida];
    $salidaDS = $fetch_detalle[salida_diasiguiente];					
    $entradaEmer = $fetch_detalle[ent_emer];
    $salidaEmer = $fetch_detalle[sal_emer];
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
?>