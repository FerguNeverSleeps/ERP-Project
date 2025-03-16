	<?php
//include ("../header.php");
//include("../lib/common.js");
include("../lib/common.php");
//include("func_bd.php");
$conexion=conexion();

class tiempo{
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

$opcion=$_GET['opcion'];
//$opcion="picadilly";
switch ($opcion)
{
	case "picadilly":
		$ent=$_GET[entrada];
		$salm=$_GET[salmuerzo];
		$ealm=$_GET[ealmuerzo];
		$sal=$_GET[salida];
		$fecha = $_GET[fecha];
		$ficha = $_GET[ficha];

		$query="select tur.descripcion from nomturnos tur join nompersonal per on tur.turno_id=per.turno_id where per.ficha='$ficha'";
		$result=query($query,$conexion);
		$fila = fetch_array($result);
		$turno = trim($fila[descripcion]);

		$entrada=$salmu=$ealmu=$salida=$regular=$ausen=$tardan=$incapac=$sobret=$feriado="";
		$entradaf=$salmuf=$ealmuf=$salidaf="";
		$tardanza=$extra=$domingo="";
		
		$extra=$regular=$regular1=$regular2=$regular3=$regular4="";
		$extra=$extra1=$extra2=$extra3=$extra4="";
		$extraNoc=$extraNoc1=$extraNoc2=$extraNoc3=$extraNoc4="";
		$extraExt=$extraExt1=$extraExt2=$extraExt3=$extraExt4="";
		$extraNocExt=$extraNocExt1=$extraNocExt2=$extraNocExt3=$extraNocExt4="";
		//$date = new DateTime($dates);
		//$fecha = $date->format('Y-m-d');
		//$hora = $date->format('H:i');
		//$ficha = $fila[ficha];
		

		if($turno=="2")
		{
			$hExt = new DateTime("03:00");
			$limiteExt = new DateTime("18:00");
			$limiteExtNoc = new DateTime("23:59");		
			$limite = new DateTime("07:30");
		}
		else
		{
			$hExt = new DateTime("03:00");
			$limiteExt = new DateTime("18:00");
			$limiteExtNoc = new DateTime("23:59");		
			$limite = new DateTime("08:00");	
		}
		
		if($ent!="")
		{
			$entrada = new DateTime($ent);
		} 
		if($salm!="") 
		{
			$salmu = new DateTime($salm);
		}
		if($ealm!="")
		{
			$ealmu = new DateTime($ealm);
		}
		if($sal!="") 
		{
			$salida = new DateTime($sal);
		} 
		
		
		
		$extra=$regular=$regular1=$regular2=$regular3="";
		if(($salida=="")&&($salmu!=""))
		{
			$salida=$salmu;
			$salmu="";			
		}
	
		if($entrada!="")
		{
			$entradaf=$entrada->format('H:i');
		}
		if($salmu!="")
		{
			$salmuf=$salmu->format('H:i');
		}
		if($ealmu!="")
		{
			$ealmuf=$ealmu->format('H:i');
		}
		if($salida!="")
		{
			$salidaf=$salida->format('H:i');
		}		
	
					if(($entrada!="")&&($salmu!=""))
					{
						$regular1 = $entrada->diff($salmu);
						if(($salmu<=$limiteExt) || ($regular1<$limite))
						{
							
							//$regular1= $aux1->format('%H:%I');
							if($regular1>$limite)
							{
								$extra1 = $regular1->diff($limite);
								//$extra1 = $auxExt1->format('%H:%I');
								//$extra2= new DateTime($extra1->format('%H:%I'));
								$regular1 = $limite;
								
								if($extra1>$hExt)
								{
									$extraExt1 = $hExt->diff($extra1);
									$extra1 = $hExt;
								}
							}
						}
						elseif(($salmu>$limiteExt) && ($regular1>$limite))
						{
							$regular1 = $entrada->diff($limiteExt);
							//$regular1= $aux1->format('%H:%I');
							
							$extraNoc1 = $limiteExt->diff($salmu);
							//$extraNoc1 = $auxExt1->format('%H:%I');
							if($extraNoc1>$hExt)
							{
								$extraNocExt1 = $hExt->diff($extraNoc1);
								$extraNoc1 = $hExt;
							}
						}
					}
					if(($ealmu!="")&&($salida!=""))
					{
						$regular2 = $ealmu->diff($salida);
							//$regular2 = $aux2->format('%H:%I');
						if($regular1!="") 
						{
							$auxreg = explode(":",$regular2->format('%H:%I'));
							$auxreg1 = "PT".$auxreg[0]."H".$auxreg[1]."M";
							$regular5x = new DateTime($regular1->format('%H:%I'));
							$regular2 = $regular5x->add(new DateInterval($auxreg1));
						}
						if(($salida<=$limiteExt) || ($regular2<$limite))
						{
							
							//$regularsum=$regular1->add($regular2);
							if($regular2>$limite)
							{
								$extra2 = $regular2->diff($limite);
								//echo $extra2 = $extra2->format('%H:%I');
								$extra2= new DateTime($extra2->format('%H:%I'));
								$regular2 = $limite;
								
								if($extra2>$hExt)
								{
									$extraExt2 = $hExt->diff($extra2);
									$extra2 = $hExt;
								}
							}
						}
						elseif(($salida>$limiteExt) && ($regular2>$limite))
						{
							
							/*$regular2 = $ealmu->diff($limiteExt);
							//$regular2= $aux2->format('%H:%I');
							
							if($regular1!="") 
							{
								$auxreg = explode(":",$regular2->format('%H:%I'));
								$auxreg1 = "PT".$auxreg[0]."H".$auxreg[1]."M";
								$regular5x = new DateTime($regular1->format('%H:%I'));
								$regular2 = $regular5x->add(new DateInterval($auxreg1));
							}*/
							//$regularsum=$regular1->add($regular2);
							if($regular2>$limite)
							{
								$extra2 = $regular2->diff($limite);
								$extra2xxxx= new DateTime($extra2->format('%H:%I'));

								//$extra2 = $extra2->diff($limite);
								//echo $extra2 = $extra2->format('%H:%I');
								$extraNoc2x = $limiteExt->diff($salida);
								$extraNoc2xxxx = new DateTime($extraNoc2x->format('%H:%I'));
								//echo $extra2->format('%H:%I');
								/*if($extra2<$extraNoc2x)
								{
									$extraNoc2x = $extra2;
									$extra2 = "";
								}
								else
								{*/
									$extraNoc2xx = new DateTime($extraNoc2x->format('%H:%I'));

									$extra2= new DateTime($extra2->format('%H:%I'));

									$extra2 = $extra2->diff($extraNoc2xx);

									$extra2= new DateTime($extra2->format('%H:%I'));
									$regular2 = $limite;
								//}
								
								if($extra2>$hExt)
								{
									$extraExt2 = $hExt->diff($extra2);
									$extra2 = $hExt;
								}
							}
							
							$extraNoc2 = $extraNoc2x;//$limiteExt->diff($salida);
							//echo $extraNoc2->format('%H:%I');
							//$extraNoc2x = new DateTime($extraNoc2->format('%H:%I'));
							//$extraNoc2 = $auxExt2->format('%H:%I');
							if($extraNoc2x>$hExt)
							{
								//exit;
								$extraNocExt2 = $hExt->diff($extraNoc2x);
								$extraNoc2 = $hExt;
							}
						}
					}
					if(($entrada!="")&&($salmu=="")&&($ealmu=="")&&($salida!=""))
					{
						$regular3 = $entrada->diff($salida);
						$regular3= new DateTime($regular3->format('%H:%I'));
						if(($salida<=$limiteExt) || ($regular3<$limite))
						{
							

							if($regular3>$limite)
							{
								$extra3 = $regular3->diff($limite);
								//$extra3 = $auxExt3->format('%H:%I');
								$regular3 = $limite;

								$extra3x = new DateTime($extra3->format('%H:%I'));								
								
								if($extra3x>$hExt)
								{
									$extraExt3 = $hExt->diff($extra3x);
									$extra3 = $hExt;
								}
							}
						}
						elseif(($salida>$limiteExt) && ($regular3>$limite))
						{
							$regular3 = $entrada->diff($limiteExt);
							$regular3x = new DateTime($regular3->format('%H:%I'));
							//$regular3= $aux3->format('%H:%I');
							
							if($regular3x>$limite)
							{
								$extra3 = $regular3x->diff($limite);
								//$extra3 = $auxExt3->format('%H:%I');
								$regular3 = $limite;

								$extraNoc3x = $limiteExt->diff($salida);
								$extraNoc3xx = new DateTime($extraNoc3x->format('%H:%I'));

								$extra3x = new DateTime($extra3->format('%H:%I'));		

								$extra3x = $extra3x->diff($extraNoc3xx);

								$extra3x = new DateTime($extra3->format('%H:%I'));		
								
								if($extra3x>$hExt)
								{
									$extraExt3 = $hExt->diff($extra3x);
									$extra3 = $hExt;
								}
							}
							
							$extraNoc3 = $limiteExt->diff($salida);
							
							$extraNoc3x = new DateTime($extraNoc3->format('%H:%I'));
							//$extraNoc3 = $auxExt3->format('%H:%I');
							if($extraNoc3x>$hExt)
							{
								$extraNocExt3 = $hExt->diff($extraNoc3x); 
								$extraNoc3 = $hExt;
							}
						}
					}
					
			
					if($regular3!="")
					{
						$regular = $regular3->format('%H:%I');
						if(strpos($regular,"%")!==false)
						{
							$regular=$regular3->format('H:i');
						}
						
						if($extra3!="")
							$extra = $extra3->format('%H:%I');
						if(strpos($extra,"%")!==false)
						{
							$extra=$extra3->format('H:i');
						}
					
						if($extraExt3!="")
							$extraExt = $extraExt3->format('%H:%I');


						
						//echo $extra3->format('%H:%I');
						if($extra3!="")
							$extra3 = new DateTime($extra3->format('%H:%I'));		
						if($extraNoc3!="")
						{
							if($extra3<$hExt)
							{
								$extraNoc = $extraNoc3->format('%H:%I');
			
								if(strpos($extraNoc,"%")!==false)
								{
									$extraNoc=$extraNoc3->format('H:i');
								}
							}
							elseif($extraNocExt3=="")
							{
								$extraNocExt = $extraNoc3->format('%H:%I');
							}
						}	
						//echo $extraNocExt3;
						if($extraNocExt3!="")
						{
							if($extra3<$hExt)
							{
								$extraNocExt = $extraNocExt3->format('%H:%I');
							}
							else
							{
								$auxreg = explode(":",$extraNoc3->format('H:i'));
								$auxreg1 = "PT".$auxreg[0]."H".$auxreg[1]."M";
								$extraNocExt5 = new DateTime($extraNocExt3->format('%H:%I'));
								$extraNocExt4 = $extraNocExt5->add(new DateInterval($auxreg1));
								$extraNocExt = $extraNocExt4->format('H:i');
							}
						}
					}
					elseif(($regular1!="")&&($regular2!=""))
					{
						
						/*$auxreg = explode(":",$regular2->format('%H:%I'));
						$auxreg1 = "PT".$auxreg[0]."H".$auxreg[1]."M";
						$regular5 = new DateTime($regular1->format('%H:%I'));
						$regular4 = $regular5->add(new DateInterval($auxreg1));
						$regular = $regular4->format('H:i');
						*/
						$regular = $regular2->format('H:i');
						/*
						$auxreg = explode(":",$extra1);
						$auxreg1 = "PT".($auxreg[0]=="" ? 0 : $auxreg[0])."H".($auxreg[1]=="" ? 0 : $auxreg[1])."M";
						$extra5 = new DateTime($extra2);
						$extra4 = $extra5->add(new DateInterval($auxreg1)); 
						$extra = $extra4->format('H:i');
						*/
						//echo $extra2->format('H:i');
						/*if($extra2<$extraNoc2)
						{
							$extraNoc2 = $extra2;
							$extra2 = "";
						}*/

						if($extra2!="")
							$extra = $extra2->format('%H:%I');
						if(strpos($extra,"%")!==false)
						{
							$extra=$extra2->format('H:i');
						}
						
						if(($extraExt1!="")&&($extraExt2!=""))
						{
							$auxreg = explode(":",$extraExt1);
							$auxreg1 = "PT".($auxreg[0]=="" ? 0 : $auxreg[0])."H".($auxreg[1]=="" ? 0 : $auxreg[1])."M";
							$extraExt5 = new DateTime($extraExt2);
							$extraExt4 = $extraExt5->add(new DateInterval($auxreg1));
							$extraExt = $extraExt4->format('H:i');
						}
						elseif(($extraExt1!="")||($extraExt2!=""))
						{
							$extraExt = $extraExt2->format('%H:%I');
							if(strpos($extraExt,"%")!==false)
							{
								$extraExt=$extraExt2->format('%H:%I');
							}
						}
					
						if(($extraNoc1!="")&&($extraNoc2!=""))
						{
							$auxreg = explode(":",$extraNoc1->format('%H:%I'));
							$auxreg1 = "PT".($auxreg[0]=="" ? 0 : $auxreg[0])."H".($auxreg[1]=="" ? 0 : $auxreg[1])."M";
							$extraNoc5 = new DateTime($extraNoc2->format('%H:%I'));
							$extraNoc4 = $extraNoc5->add(new DateInterval($auxreg1));
							
							if($extra2<$hExt)
							{
								$extraNoc = $extraNoc4->format('H:i');
							}
							elseif(($extraNocExt2=="")&&($extraNocExt1==""))
							{
								$extraNocExt = $extraNoc4->format('H:i');
							}
							//exit;
						}
						elseif($extraNoc2!="")
						{
							$auxreg = explode(":",$extra2->format('H:i'));
							$auxreg1 = "PT".($auxreg[0]=="" ? 0 : $auxreg[0])."H".($auxreg[1]=="" ? 0 : $auxreg[1])."M";
							try
							{
								$extraNoc5x = new DateTime($extraNoc2->format('H:i'));
							}
							catch(Exception $e)
							{
								$extraNoc5x = new DateTime($extraNoc2->format('%H:%I'));
							}
							$extraNoc4x = $extraNoc5x->add(new DateInterval($auxreg1));

							if($extraNoc4x<$hExt)
							{
								$extraNoc= $extraNoc2->format('%H:%I');
								if(strpos($extraNoc,"%")!==false)
								{
									$extraNoc= $extraNoc2->format('H:i');
								}
							}
							elseif($extraNoc4x>=$hExt)
							{
								$extraNoc22= $extra2->diff($hExt);
								$extraNoc= $extraNoc22->format('%H:%I');
								if(strpos($extraNoc,"%")!==false)
								{
									$extraNoc= $extraNoc22->format('H:i');
								}
								try
								{
									$extraNoc2x = new DateTime($extraNoc2->format('H:i'));
								}
								catch(Exception $e)
								{
									$extraNoc2x = new DateTime($extraNoc2->format('%H:%I'));	
								}
								$extraNoc22x = new DateTime($extraNoc22->format('%H:%I'));
								$extraNocExt22 = $extraNoc2x->diff($extraNoc22x);
								$extraNocExt= $extraNocExt22->format('%H:%I');
								if(strpos($extraNocExt,"%")!==false)
								{
									$extraNocExt= $extraNocExt22->format('H:i');
								}
							}
							elseif($extraNocExt2=="")
							{
								$extraNocExt= $extraNoc2->format('%H:%I');
								if(strpos($extraNocExt,"%")!==false)
								{
									$extraNocExt= $extraNoc2->format('H:i');
								}
							}
						}
						else
						{
							$extraNoc = "";	
						}
						
						if(($extraNocExt2!="")&&($extraNocExt1!=""))
						{
							if($extra2<$hExt)
							{
								$auxreg = explode(":",$extraNocExt1);
								$auxreg1 = "PT".($auxreg[0]=="" ? 0 : $auxreg[0])."H".($auxreg[1]=="" ? 0 : $auxreg[1])."M";
								$extraNocExt5 = new DateTime($extraNocExt2);
								$extraNocExt4 = $extraNocExt5->add(new DateInterval($auxreg1));
								$extraNocExt = $extraNocExt4->format('H:i');
							}
							else
							{
								$auxreg = explode(":",$extraNocExt1);
								$auxreg1 = "PT".($auxreg[0]=="" ? 0 : $auxreg[0])."H".($auxreg[1]=="" ? 0 : $auxreg[1])."M";
								$extraNocExt5 = new DateTime($extraNocExt2);
								$extraNocExt4 = $extraNocExt5->add(new DateInterval($auxreg1));

								$auxreg = explode(":",$extra2->format('%H:%I'));
								$auxreg1 = "PT".($auxreg[0]=="" ? 0 : $auxreg[0])."H".($auxreg[1]=="" ? 0 : $auxreg[1])."M";
								$extraNocExt6 = new DateTime($extraNocExt4);
								$extraNocExt7 = $extraNocExt6->add(new DateInterval($auxreg1));

								$extraNocExt = $extraNocExt7->format('H:i');
							}
						}
						elseif($extraNocExt2!="")
						{
							if($extra2<$hExt)
							{
								$extraNocExt= $extraNocExt2->format('%H:%I');
								if(strpos($extraNocExt,"%")!==false)
								{
										$extraNocExt= $extraNocExt2->format('H:i');
								}
							}
							else
							{
								$auxreg = explode(":",$extraNoc2->format('H:i'));
								$auxreg1 = "PT".$auxreg[0]."H".$auxreg[1]."M";
								$extraNocExt5 = new DateTime($extraNocExt2->format('%H:%I'));
								$extraNocExt4 = $extraNocExt5->add(new DateInterval($auxreg1));
								$extraNocExt = $extraNocExt4->format('H:i');
							}
						}
						/*else
						{
							$extraNocExt = "";	
						}*/
						if($extra2xxxx<$extraNoc2xxxx)
						{
							$extraNoc = $extra2xxxx->format('H:i');
							$extra = "";

							if($extra2xxxx>$hExt)
							{
								$extraNocExt = $hExt->diff($extra2xxxx);
								$extraNocExt = $extraNocExt->format('%H:%I');
								$extraNoc = $hExt->format('H:i');
							}
							else
							{
								$extraNocExt="";
							}
						}
					}
					elseif($regular1!="")
					{
						if($regular1!="")
							$regular = $regular1->format('%H:%I');
						 
						if($extra1!="")						
							$extra = $extra1->format('%H:%I');
						
						if($extraExt1!="")
							$extraExt = $extraExt1->format('%H:%I');
						
						if($extraNoc1!="")
							$extraNoc = $extraNoc1->format('%H:%I');

						if($extraNocExt1!="")
							$extraNocExt = $extraNocExt1->format('%H:%I');				
					}
					elseif($regular2)
					{
						if($regular2!="")
							$regular = $regular2->format('%H:%I');
						 
						if($extra2!="")
							$extra = $extra2->format('%H:%I');
							
						if($extraExt2!="")
							$extraExt = $extraExt2->format('%H:%I');
						
						if($extraNoc2!="")
							$extraNoc = $extraNoc2->format('%H:%I');
						if(strpos($extraNoc,"%")!==false)
						{
								$extraNoc= $extraNoc2->format('H:i');
						}
							
						if($extraNocExt2!="")
							$extraNocExt = $extraNocExt2->format('%H:%I');					
					}
					
					if($regular!="")
					{
						$regu = new DateTime($regular);
						//$limite = new DateTime("08:00");
						if($regu<$limite)
						{
							$tardanzas = $regu->diff($limite);
							$tardanza = $tardanzas->format('%H:%I');
							//$regular="08:00";			
						}
						if(($turno=="2")&&($regular=="07:30"))
						{
							$regular="08:00";
						}
					}


					
					if(date("w",strtotime($fecha))==0)
					{	
						$domingo=$regular;
						$regular="";
						
						//$extraDom2 = new DateTime($extraExt);
						if($extraExt!="")
						{
							$extraDom1 = new DateTime($extra);
							$auxreg = explode(":",$extraExt);
							$auxreg1 = "PT".$auxreg[0]."H".$auxreg[1]."M";
							$extraDom3 = $extraDom1->add(new DateInterval($auxreg1)); 
							$extraDom = $extraDom3->format('H:i');
						}
						elseif($extra!="")
							$extraDom=$extra;
						if($extraNocExt!="")
						{
							$extraNocDom1 = new DateTime($extraNoc);
							//$extraNocDom2 = new DateTime($extraNocExt);
							$auxreg = explode(":",$extraNocExt);
							$auxreg1 = "PT".$auxreg[0]."H".$auxreg[1]."M";
							$extraNocDom3 = $extraNocDom1->add(new DateInterval($auxreg1)); 
							$extraNocDom = $extraNocDom3->format('H:i');	
						}
						elseif($extraNoc!="")
							$extraNocDom=$extraNoc;
						
						$extra =	$extraDom;
						$extraNoc = $extraNocDom;
						//$extra =	"";
						//$extraNoc = "";
						
					}

					$query="select dia_fiesta from nomcalendarios_tiposnomina where fecha='$fecha'";
					$result=query($query,$conexion);
					$fetch = fetch_array($result);

					if($fetch[dia_fiesta]==3)
					{
						$nacional=$regular;
						$regular="";
						
						//$extraDom2 = new DateTime($extraExt);
						if($extraExt!="")
						{
							$extraDom1 = new DateTime($extra);
							$auxreg = explode(":",$extraExt);
							$auxreg1 = "PT".$auxreg[0]."H".$auxreg[1]."M";
							$extraDom3 = $extraDom1->add(new DateInterval($auxreg1)); 
							$extraNac = $extraDom3->format('H:i');
						}
						elseif($extra!="")
							$extraNac=$extra;
						if($extraNocExt!="")
						{
							/*
							$extraNocDom1 = new DateTime($extraNoc);
							//$extraNocDom2 = new DateTime($extraNocExt);
							$auxreg = explode(":",$extraNocExt);
							$auxreg1 = "PT".$auxreg[0]."H".$auxreg[1]."M";
							$extraNocDom3 = $extraNocDom1->add(new DateInterval($auxreg1)); 
							$extraNocNac = $extraNocDom3->format('H:i');	*/
							$extraNocNac=$extraNocExt;
						}
						elseif($extraNoc!="")
							$extraNocNac=$extraNoc;
						
						$extra =	"";
						//$extraNoc = $extraNocDom;
						$extraNoc = "";
						$extraExt="";
						$extraNocExt="";
					}

		echo "Finalizo--".$regular."--".$extra."--".$domingo."--".$tardanza."--".$extraExt."--".$extraNoc."--".$extraNocExt."--".$nacional."--".$extraNac."--".$extraNocNac;
	break;

	case "jimmy":
		$ent=$_GET[entrada];
		$salm=$_GET[salmuerzo];
		$ealm=$_GET[ealmuerzo];
		$sal=$_GET[salida];
		$fecha = $_GET[fecha];
		$ficha = $_GET[ficha];

		$query="select tur.* from nomturnos tur join nomcalendarios_personal per on tur.turno_id=per.turno_id where per.ficha='$ficha' and per.fecha='$fecha'";
		$result=query($query,$conexion);
		$fila = fetch_array($result);
		$turno = trim($fila[descripcion]);
		$libre = $fila[libre];
		$entrada0 = new DateTime((substr($fila[tolerancia_llegada],0,2)=="00" ? "24".substr($fila[tolerancia_llegada], 2,7) : $fila[tolerancia_llegada]));
		$entrada1 = new DateTime((substr($fila[entrada],0,2)=="00" ? "24".substr($fila[entrada], 2,7) : $fila[entrada]));
		$entradatol = new DateTime((substr($fila[tolerancia_entrada],0,2)=="00" ? "24".substr($fila[tolerancia_entrada], 2,7) : $fila[tolerancia_entrada]));
		$saldesc1 = new DateTime((substr($fila[inicio_descanso],0,2)=="00" ? "24".substr($fila[inicio_descanso], 2,7) : $fila[inicio_descanso]));
		//$entdesc1 = new DateTime((substr($fila[salida_descanso],0,2)=="00" ? "24".substr($fila[salida_descanso], 2,7) : $fila[salida_descanso]));
		$entdesc1 = new DateTime($fila[salida_descanso]);
		$entdesctol = new DateTime((substr($fila[tolerancia_descanso],0,2)=="00" ? "24".substr($fila[tolerancia_descanso], 2,7) : $fila[tolerancia_descanso]));
		$salida1 = new DateTime((substr($fila[salida],0,2)=="00" ? "24".substr($fila[salida], 2,7) : $fila[salida]));
		$salidatol = new DateTime((substr($fila[tolerancia_salida],0,2)=="00" ? "24".substr($fila[tolerancia_salida], 2,7) : $fila[tolerancia_salida]));

		$lim1 = $saldesc1->diff($entrada1);
		$lim2 = $salida1->diff($entdesc1);
		//echo $lim2->format('%H:%I');

		$auxreg = explode(":",$lim2->format('%H:%I'));
		$auxreg1 = "PT".$auxreg[0]."H".$auxreg[1]."M";
		$limx = new DateTime($lim1->format('%H:%I'));
		$lim = $limx->add(new DateInterval($auxreg1));

		$entrada=$salmu=$ealmu=$salida=$regular=$ausen=$tardan=$incapac=$sobret=$feriado="";
		$entradaf=$salmuf=$ealmuf=$salidaf="";
		$tardanza=$extra=$domingo="";
		
		$extra=$regular=$regular1=$regular2=$regular3=$regular4="";
		$extra=$extra1=$extra2=$extra3=$extra4="";
		$extraNoc=$extraNoc1=$extraNoc2=$extraNoc3=$extraNoc4="";
		$extraExt=$extraExt1=$extraExt2=$extraExt3=$extraExt4="";
		$extraNocExt=$extraNocExt1=$extraNocExt2=$extraNocExt3=$extraNocExt4="";
		
		$hExt = new DateTime("03:00");
		$limiteExt = new DateTime("18:00");
		$limiteExtFin = new DateTime("06:00");
		$limiteExtNoc = new DateTime("23:59");		
		$limite = $lim;

			
		
		
		if($ent!="")
		{
			$entrada = new DateTime($ent);
			//if($entrada<=$entradatol)
			//	$entrada=$entrada1;
		} 
		if($salm!="") 
		{
			$salmu = new DateTime($salm);
		}
		if($ealm!="")
		{
			$ealmu = new DateTime($ealm);
		}
		if($sal!="") 
		{
			$salida = new DateTime($sal);
			//if($salida>=$salidatol)
			//	$salida=$salidatol;
		} 
		
		
		
		$extra11=$extra=$regular=$regular1=$regular2=$regular3=$extrah="";
		$band = 0;
		//echo $entrada0->format('H:i');
		if(($entrada!="")&&($salmu!=""))
		{
			if($entrada<$entrada0)
			{
				if($entrada<$limiteExtFin)
				{
					$extraNoc1 = $entrada->diff($limiteExtFin);
					$extra1 = $entrada1->diff($limiteExtFin);
				}
				else
				{
					$extra1 = $entrada1->diff($entrada);
				}
				$regular1=$entrada1->diff($salmu);
			}
			elseif($entrada>$entradatol)
			{
				$tardanza1 = $entrada->diff($entrada1);
				$regular1=$entrada->diff($salmu);
			}
			elseif(($entrada>=$entrada0)&&($entrada<=$entradatol))
			{
				$regular1=$entrada1->diff($salmu);
			}

			//PARA LA HORA EXTRA Y TARDANZA DE ALMUERZO
			if($libre==0)
			{
				if($salmu<$saldesc1)
				{
					$tardanza3 = $salmu->diff($saldesc1);
				}
				elseif(($salmu>$saldesc1)&&($salmu<=$entdesc1))
				{
					$extrah1 = $saldesc1->diff($salmu);
					//$regular1=$entrada->diff($saldesc1);
				}
				/*elseif($salmu>$entdesc1)
				{
					$tardanza3 = $salmu->diff($saldesc1);
					//$regular1=$entrada->diff($saldesc1);
				}*/
			}
			
			$band=1;			
		}

		if(($ealmu!="")&&($salida!=""))
		{
			$regular2=$salida->diff($ealmu);

			//PARA LA HORA EXTRA Y TARDANZA DE ALMUERZO
			if($libre==0)
			{
				if($ealmu>$entdesctol)
				{
					$tardanza4 = $ealmu->diff($entdesc1);
				}
				elseif($ealmu<=$entdesc1)
				{
					$extrah2 = $entdesc1->diff($ealmu);
					$regular2=$entdesc1->diff($salida);
				}
				elseif($salmu>$entdesc1)
				{
					$extrah4 = $ealmu->diff($salmu);
					//$regular1=$entrada->diff($saldesc1);
				}
			}
			

			if($regular1)
			{
				$auxreg = explode(":",$regular2->format('%H:%I'));
				$auxreg1 = "PT".$auxreg[0]."H".$auxreg[1]."M";
				$regularx = new DateTime($regular1->format('%H:%I'));
				$regular = $regularx->add(new DateInterval($auxreg1));
			}
			else
			{
				$regular = $regular2;
				$regular = new DateTime($regular->format('%H:%I'));
			}

			if($salida>$salidatol)
			{
				//if(($salida>$limiteExt)&&($regular>$limite))
				if($salida>$limiteExt)
				{
					if($salida1<$limiteExt)
					{
						$extra2 = $salida1->diff($limiteExt);
						$extraNoc2 = $limiteExt->diff($salida);
						$regular = $limite;
					}
					else
					{
						$extraNoc2 = $salida1->diff($salida);
						$regular = $limite;
					}
				}
				//elseif(($salida<=$limiteExt)&&($regular>$limite))
				elseif($salida<=$limiteExt)
				{
					$extra2 = $salida1->diff($salida);
					$regular = $limite;
				}
			}
			elseif(($salida>=$salida1)&&($salida<=$salidatol))
			{
				$regular = $limite;
			}
			elseif($salida<$salida1)
			{
				$tardanza2 = $salida->diff($salida1);
			}

			if(($extra1!="")&&($extra2!=""))
			{
				$auxreg = explode(":",$extra2->format('%H:%I'));
				$auxreg1 = "PT".$auxreg[0]."H".$auxreg[1]."M";
				$extrax = new DateTime($extra1->format('%H:%I'));
				$extra = $extrax->add(new DateInterval($auxreg1));
				$extra = new DateTime($extra->format('H:i'));
				//echo $extra->format('H:i');
			}
			elseif($extra1!="")
			{
				$extra = $extra1;
				$extra = new DateTime($extra->format('%H:%I'));
			}
			elseif($extra2!="")
			{
				$extra = $extra2;
				$extra = new DateTime($extra->format('%H:%I'));
			}

			if(($extraNoc1!="")&&($extraNoc2!=""))
			{
				$auxreg = explode(":",$extraNoc2->format('%H:%I'));
				$auxreg1 = "PT".$auxreg[0]."H".$auxreg[1]."M";
				$extraxNoc = new DateTime($extraNoc1->format('%H:%I'));
				$extraNoc = $extraNocx->add(new DateInterval($auxreg1));
			}
			elseif($extraNoc1!="")
			{
				$extraNoc = $extraNoc1;
			}
			elseif($extraNoc2!="")
			{
				$extraNoc = $extraNoc2;
			}
			$band=1;
		}

		if(($entrada!="")&&($salida!="")&&($band==0))
		{
			if($entrada<$entrada0)
			{
				if($entrada<$limiteExtFin)
				{
					$extraNoc1 = $entrada->diff($limiteExtFin);
					$extra1 = $entrada1->diff($limiteExtFin);
				}
				else
				{
					$extra1 = $entrada1->diff($entrada);
				}
				//if($salida<=$salidatol)
				$regular=$entrada1->diff($salida);
				//else
			}
			elseif($entrada>$entradatol)
			{
				$tardanza1 = $entrada->diff($entrada1);
				$regular=$entrada->diff($salida);
			}
			elseif(($entrada>=$entrada0)&&($entrada<=$entradatol))
			{
				$regular=$entrada1->diff($salida);
			}
			//$regular = new DateTime($regular->format('%H:%I'));
			//$regular = new DateTime($regular->format('H:i'));
			$regular = new DateTime($regular->format('%H:%I'));
			if($salida>$salidatol)
			{

				if($salida>$limiteExt)
				{
					if($salida1<$limiteExt)
					{
						$extra2 = $salida1->diff($limiteExt);
						$extraNoc2 = $limiteExt->diff($salida);
						$regular = $limite;
					}
					else
					{
						$extraNoc2 = $salida1->diff($salida);
						$regular = $limite;
					}
				}
				elseif($salida<=$limiteExt)
				{
					$extra2 = $salida1->diff($salida);
					$regular = $limite;
				}
			}
			elseif(($salida>=$salida1)&&($salida<=$salidatol))
			{
				$regular = $limite;
			}
			elseif($salida<$salida1)
			{
				$tardanza2 = $salida->diff($salida1);

				if($nocturno==1)
				{
					if($entrada<$entrada1)
						$entradaxxx=$entrada1;
					$lim1x = $saldesc1->diff($entradaxxx);
					$lim2x = $salida->diff($entdesc1);

					$auxreg = explode(":",$lim2x->format('%H:%I'));
					$auxreg1 = "PT".$auxreg[0]."H".$auxreg[1]."M";
					$limxx = new DateTime($lim1x->format('%H:%I'));
					$limxxx = $limxx->add(new DateInterval($auxreg1));
					$regular = $limxxx;
					if($regular>=$limite)
						$regular=$limite;
				}
			}

			if(($extra1!="")&&($extra2!=""))
			{
				$auxreg = explode(":",$extra2->format('%H:%I'));
				$auxreg1 = "PT".$auxreg[0]."H".$auxreg[1]."M";
				$extrax = new DateTime($extra1->format('%H:%I'));
				$extra = $extrax->add(new DateInterval($auxreg1));

				$extra = new DateTime($extra->format('H:i'));
			}
			elseif($extra1!="")
			{
				$extra = $extra1;
				$extra = new DateTime($extra->format('%H:%I'));
			}
			elseif($extra2!="")
			{
				$extra = $extra2;
				$extra = new DateTime($extra->format('%H:%I'));
			}

			if(($extraNoc1!="")&&($extraNoc2!=""))
			{
				$auxreg = explode(":",$extraNoc2->format('%H:%I'));
				$auxreg1 = "PT".$auxreg[0]."H".$auxreg[1]."M";
				$extraNocx = new DateTime($extraNoc1->format('%H:%I'));
				$extraNoc = $extraNocx->add(new DateInterval($auxreg1));
			}
			elseif($extraNoc1!="")
			{
				$extraNoc = $extraNoc1;
			}
			elseif($extraNoc2!="")
			{
				$extraNoc = $extraNoc2;
			}
			if($regular==$limite)
			{
				$extrah1 = $saldesc1->diff($entdesc1);
			}
		}

		if(($libre==1) && ($salmu!="") && ($ealmu!=""))
		{
			$talmutol = $saldesc1->diff($entdesctol);
			$talmutol = new DateTime($talmutol->format('%H:%I'));

			$talmu = $saldesc1->diff($entdesc1);
			$talmu = new DateTime($talmu->format('%H:%I'));

			$talmur = $salmu->diff($ealmu);
			$talmur = new DateTime($talmur->format('%H:%I'));

			if($talmur>$talmutol)
			{
				$tardanza3 = $talmu->diff($talmur);
			}
			elseif($talmur<=$talmu)
			{
				$extrah1 = $talmu->diff($talmur);
			}
		}

		if(($extrah1!="")&&($extrah2!=""))
		{
			$auxreg = explode(":",$extrah2->format('%H:%I'));
			$auxreg1 = "PT".$auxreg[0]."H".$auxreg[1]."M";
			$extrahx = new DateTime($extrah1->format('%H:%I'));
			$extrah = $extrahx->add(new DateInterval($auxreg1));

			$extrah = $extrah->format('H:i');
		}
		elseif($extrah1!="")
		{
			//$extrah = $extrah1;
			$extrah = $extrah1->format('%H:%I');
		}
		elseif($extrah2!="")
		{
			//$extrah = $extrah2;
			$extrah = $extrah2->format('%H:%I');
		}

		if(($regular=="")&&($regular1!=""))
		{
			$regular = new DateTime($regular1->format('%H:%I'));
			//$regular = $regular1;
		}

		if($extraNoc)
			$extraNoc = new DateTime($extraNoc->format('%H:%I'));
		
		if($extra>$hExt)
		{
			$extraExt = $extra->diff($hExt);
			$extra = $hExt;
		}

		if($extraNoc>$hExt)
		{
			$extraNocExt = $extraNoc->diff($hExt);
			$extraNoc = $hExt;
		}

		if($regular)
			$regular = $regular->format('H:i');
		if($extra)
			$extra = $extra->format('H:i');
		if($extraExt)
			$extraExt = $extraExt->format('%H:%I');
		if($extraNoc)
			$extraNoc = $extraNoc->format('H:i');
		if($extraNocExt)
			$extraNocExt = $extraNocExt->format('%H:%I');

		if(($tardanza1!="")&&($tardanza2!=""))
		{
			$auxreg = explode(":",$tardanza2->format('%H:%I'));
			$auxreg1 = "PT".$auxreg[0]."H".$auxreg[1]."M";
			$tardanzax = new DateTime($tardanza1->format('%H:%I'));
			$tardanza = $tardanzax->add(new DateInterval($auxreg1));
			$tardanza = $tardanza->format('H:i');
			//$tardanza = $tardanza->format('%H:%I');
		}
		elseif($tardanza1)
			$tardanza = $tardanza1->format('%H:%I');
		elseif($tardanza2)
			$tardanza = $tardanza2->format('%H:%I');

		if($tardanza3)
		{
			if($tardanza)
			{
				$auxreg = explode(":",$tardanza);
				$auxreg1 = "PT".$auxreg[0]."H".$auxreg[1]."M";
				$tardanzax = new DateTime($tardanza3->format('%H:%I'));
				$tardanza = $tardanzax->add(new DateInterval($auxreg1));
				$tardanza = $tardanza->format('H:i');
			}
			else
				$tardanza = $tardanza3->format('%H:%I');
		}

		if($tardanza4)
		{
			if($tardanza)
			{
				$auxreg = explode(":",$tardanza);
				$auxreg1 = "PT".$auxreg[0]."H".$auxreg[1]."M";
				$tardanzax = new DateTime($tardanza4->format('%H:%I'));
				$tardanza = $tardanzax->add(new DateInterval($auxreg1));
				$tardanza = $tardanza->format('H:i');
			}
			else
				$tardanza = $tardanza4->format('%H:%I');
		}

		if($regular!="")
		{
			$limxx=$limite->format('H:i');
			$regu = new DateTime($regular);
			//if($regu<$limite)
			//{
				//$tardanzas = $regu->diff($limite);
				//$tardanza = $tardanzas;//->format('%H:%I');
			//}
			if(($limxx=="07:30")&&($regu>=$limxx)&&($salida>=$salida1))
			{
				$regular="08:00";
			}
		}
		
		if(date("w",strtotime($fecha))==0)
		{	
			$domingo=$regular;
			$regular="";
			
			//$extraDom2 = new DateTime($extraExt);
			if($extraExt!="")
			{
				$extraDom1 = new DateTime($extra);
				$auxreg = explode(":",$extraExt);
				$auxreg1 = "PT".$auxreg[0]."H".$auxreg[1]."M";
				$extraDom3 = $extraDom1->add(new DateInterval($auxreg1)); 
				$extraDom = $extraDom3->format('H:i');
			}
			elseif($extra!="")
				$extraDom=$extra;
			if($extraNocExt!="")
			{
				$extraNocDom1 = new DateTime($extraNoc);
				//$extraNocDom2 = new DateTime($extraNocExt);
				$auxreg = explode(":",$extraNocExt);
				$auxreg1 = "PT".$auxreg[0]."H".$auxreg[1]."M";
				$extraNocDom3 = $extraNocDom1->add(new DateInterval($auxreg1)); 
				$extraNocDom = $extraNocDom3->format('H:i');	
			}
			elseif($extraNoc!="")
				$extraNocDom=$extraNoc;
			
			$extra =	$extraDom;
			$extraNoc = $extraNocDom;
			//$extra =	"";
			//$extraNoc = "";
			
		}

		$query="select dia_fiesta from nomcalendarios_tiposnomina where fecha='$fecha'";
		$result=query($query,$conexion);
		$fetch = fetch_array($result);

		if($fetch[dia_fiesta]==3)
		{
			$nacional=$regular;
			$regular="";
			
			//$extraDom2 = new DateTime($extraExt);
			if($extraExt!="")
			{
				$extraDom1 = new DateTime($extra);
				$auxreg = explode(":",$extraExt);
				$auxreg1 = "PT".$auxreg[0]."H".$auxreg[1]."M";
				$extraDom3 = $extraDom1->add(new DateInterval($auxreg1)); 
				$extraNac = $extraDom3->format('H:i');
			}
			elseif($extra!="")
				$extraNac=$extra;
			if($extraNocExt!="")
			{
				/*
				$extraNocDom1 = new DateTime($extraNoc);
				//$extraNocDom2 = new DateTime($extraNocExt);
				$auxreg = explode(":",$extraNocExt);
				$auxreg1 = "PT".$auxreg[0]."H".$auxreg[1]."M";
				$extraNocDom3 = $extraNocDom1->add(new DateInterval($auxreg1)); 
				$extraNocNac = $extraNocDom3->format('H:i');	*/
				$extraNocNac=$extraNocExt;
			}
			elseif($extraNoc!="")
				$extraNocNac=$extraNoc;
			
			$extra =	"";
			//$extraNoc = $extraNocDom;
			$extraNoc = "";
			$extraExt="";
			$extraNocExt="";
		}

		echo "Finalizo--".$regular."--".$extra."--".$domingo."--".$tardanza."--".$extraExt."--".$extraNoc."--".$extraNocExt."--".$nacional."--".$extraNac."--".$extraNocNac."--".$extrah;
	break;

	case "pandora":
		$ent=$_GET[entrada];
		$salm=$_GET[salmuerzo];
		$ealm=$_GET[ealmuerzo];
		$sal=$_GET[salida];

		$ent1=$_GET[entrada1];
		$sal1=$_GET[salida1];

		$fecha = $_GET[fecha];
		$ficha = $_GET[ficha];

		$query="select tur.descripcion from nomturnos tur join nompersonal per on tur.turno_id=per.turno_id where per.ficha='$ficha'";
		$result=query($query,$conexion);
		$fila = fetch_array($result);
		$turno = trim($fila[descripcion]);

		$entrada=$salmu=$ealmu=$salida=$regular=$ausen=$tardan=$incapac=$sobret=$feriado="";
		$entradaf=$salmuf=$ealmuf=$salidaf="";
		$tardanza=$extra=$domingo="";
		$entrada1=$salida1="";
		
		$extra=$regular=$regular1=$regular2=$regular3=$regular4="";
		$extra=$extra1=$extra2=$extra3=$extra4="";
		$extraNoc=$extraNoc1=$extraNoc2=$extraNoc3=$extraNoc4="";
		$extraExt=$extraExt1=$extraExt2=$extraExt3=$extraExt4="";
		$extraNocExt=$extraNocExt1=$extraNocExt2=$extraNocExt3=$extraNocExt4="";
		//$date = new DateTime($dates);
		//$fecha = $date->format('Y-m-d');
		//$hora = $date->format('H:i');
		//$ficha = $fila[ficha];
		

		//if($turno=="2")
		//{
			$hExt = new DateTime("03:00");
			$limiteExt = new DateTime("22:00");
			$limiteExtNoc = new DateTime("23:59");		
			$limite = new DateTime("07:30");
		/*}
		else
		{
			$hExt = new DateTime("03:00");
			$limiteExt = new DateTime("18:00");
			$limiteExtNoc = new DateTime("23:59");		
			$limite = new DateTime("08:00");	
		}*/
		
		if($ent!="")
		{
			$entrada = new DateTime($ent);
		} 
		if($salm!="") 
		{
			$salmu = new DateTime($salm);
		}
		if($ealm!="")
		{
			$ealmu = new DateTime($ealm);
		}
		if($sal!="") 
		{
			$salida = new DateTime($sal);
		} 
		if($ent1!="")
		{
			$entrada1 = new DateTime($ent1);
		}
		if($sal1!="") 
		{
			$salida1 = new DateTime($sal1);
		} 
		
		
		$extra=$regular=$regular1=$regular2=$regular3="";
		if(($salida=="")&&($salmu!=""))
		{
			$salida=$salmu;
			$salmu="";			
		}
	
		if($entrada!="")
		{
			$entradaf=$entrada->format('H:i');
		}
		if($salmu!="")
		{
			$salmuf=$salmu->format('H:i');
		}
		if($ealmu!="")
		{
			$ealmuf=$ealmu->format('H:i');
		}
		if($salida!="")
		{
			$salidaf=$salida->format('H:i');
		}
	
		if(($entrada!="")&&($salmu!=""))
		{
			$regular1 = $entrada->diff($salmu);
			if(($salmu<=$limiteExt) || ($regular1<$limite))
			{
				
				//$regular1= $aux1->format('%H:%I');
				if($regular1>$limite)
				{
					$extra1 = $regular1->diff($limite);
					//$extra1 = $auxExt1->format('%H:%I');
					//$extra2= new DateTime($extra1->format('%H:%I'));
					$regular1 = $limite;
					
					if($extra1>$hExt)
					{
						$extraExt1 = $hExt->diff($extra1);
						$extra1 = $hExt;
					}
				}
			}
			elseif(($salmu>$limiteExt) && ($regular1>$limite))
			{
				$regular1 = $entrada->diff($limiteExt);
				//$regular1= $aux1->format('%H:%I');
				
				$extraNoc1 = $limiteExt->diff($salmu);
				//$extraNoc1 = $auxExt1->format('%H:%I');
				if($extraNoc1>$hExt)
				{
					$extraNocExt1 = $hExt->diff($extraNoc1);
					$extraNoc1 = $hExt;
				}
			}
		}
		if(($ealmu!="")&&($salida!=""))
		{
			$regular2 = $ealmu->diff($salida);
				//$regular2 = $aux2->format('%H:%I');
			if($regular1!="") 
			{
				$auxreg = explode(":",$regular2->format('%H:%I'));
				$auxreg1 = "PT".$auxreg[0]."H".$auxreg[1]."M";
				$regular5x = new DateTime($regular1->format('%H:%I'));
				$regular2 = $regular5x->add(new DateInterval($auxreg1));
			}
			if(($salida<=$limiteExt) || ($regular2<$limite))
			{
				
				//$regularsum=$regular1->add($regular2);
				if($regular2>$limite)
				{
					$extra2 = $regular2->diff($limite);
					//echo $extra2 = $extra2->format('%H:%I');
					$extra2= new DateTime($extra2->format('%H:%I'));
					$regular2 = $limite;
					
					if($extra2>$hExt)
					{
						$extraExt2 = $hExt->diff($extra2);
						$extra2 = $hExt;
					}
				}
			}
			elseif(($salida>$limiteExt) && ($regular2>$limite))
			{
				
				/*$regular2 = $ealmu->diff($limiteExt);
				//$regular2= $aux2->format('%H:%I');
				
				if($regular1!="") 
				{
					$auxreg = explode(":",$regular2->format('%H:%I'));
					$auxreg1 = "PT".$auxreg[0]."H".$auxreg[1]."M";
					$regular5x = new DateTime($regular1->format('%H:%I'));
					$regular2 = $regular5x->add(new DateInterval($auxreg1));
				}*/
				//$regularsum=$regular1->add($regular2);
				if($regular2>$limite)
				{
					$extra2 = $regular2->diff($limite);
					$extra2xxxx= new DateTime($extra2->format('%H:%I'));

					//$extra2 = $extra2->diff($limite);
					//echo $extra2 = $extra2->format('%H:%I');
					$extraNoc2x = $limiteExt->diff($salida);
					$extraNoc2xxxx = new DateTime($extraNoc2x->format('%H:%I'));
					//echo $extra2->format('%H:%I');
					/*if($extra2<$extraNoc2x)
					{
						$extraNoc2x = $extra2;
						$extra2 = "";
					}
					else
					{*/
						$extraNoc2xx = new DateTime($extraNoc2x->format('%H:%I'));

						$extra2= new DateTime($extra2->format('%H:%I'));

						$extra2 = $extra2->diff($extraNoc2xx);

						$extra2= new DateTime($extra2->format('%H:%I'));
						$regular2 = $limite;
					//}
					
					if($extra2>$hExt)
					{
						$extraExt2 = $hExt->diff($extra2);
						$extra2 = $hExt;
					}
				}
				
				$extraNoc2 = $extraNoc2x;//$limiteExt->diff($salida);
				//echo $extraNoc2->format('%H:%I');
				//$extraNoc2x = new DateTime($extraNoc2->format('%H:%I'));
				//$extraNoc2 = $auxExt2->format('%H:%I');
				if($extraNoc2x>$hExt)
				{
					//exit;
					$extraNocExt2 = $hExt->diff($extraNoc2x);
					$extraNoc2 = $hExt;
				}
			}
		}
		if(($entrada!="")&&($salmu=="")&&($ealmu=="")&&($salida!=""))
		{
			$regular3 = $entrada->diff($salida);
			$regular3= new DateTime($regular3->format('%H:%I'));
			if(($salida<=$limiteExt) || ($regular3<$limite))
			{
				

				if($regular3>$limite)
				{
					$extra3 = $regular3->diff($limite);
					//$extra3 = $auxExt3->format('%H:%I');
					$regular3 = $limite;

					$extra3x = new DateTime($extra3->format('%H:%I'));								
					
					if($extra3x>$hExt)
					{
						$extraExt3 = $hExt->diff($extra3x);
						$extra3 = $hExt;
					}
				}
			}
			elseif(($salida>$limiteExt) && ($regular3>$limite))
			{
				$regular3 = $entrada->diff($limiteExt);
				$regular3x = new DateTime($regular3->format('%H:%I'));
				//$regular3= $aux3->format('%H:%I');
				
				if($regular3x>$limite)
				{
					$extra3 = $regular3x->diff($limite);
					//$extra3 = $auxExt3->format('%H:%I');
					$regular3 = $limite;

					$extraNoc3x = $limiteExt->diff($salida);
					$extraNoc3xx = new DateTime($extraNoc3x->format('%H:%I'));

					$extra3x = new DateTime($extra3->format('%H:%I'));		

					$extra3x = $extra3x->diff($extraNoc3xx);

					$extra3x = new DateTime($extra3->format('%H:%I'));		
					
					if($extra3x>$hExt)
					{
						$extraExt3 = $hExt->diff($extra3x);
						$extra3 = $hExt;
					}
				}
				
				$extraNoc3 = $limiteExt->diff($salida);
				
				$extraNoc3x = new DateTime($extraNoc3->format('%H:%I'));
				//$extraNoc3 = $auxExt3->format('%H:%I');
				if($extraNoc3x>$hExt)
				{
					$extraNocExt3 = $hExt->diff($extraNoc3x); 
					$extraNoc3 = $hExt;
				}
			}
		}


		if(($entrada1!="")&&($salida1!=""))
		{
			$regular4 = $entrada1	->diff($salida1);
				//$regular2 = $aux2->format('%H:%I');
			if($regular2!="") 
			{
				$regularx=$regular2;
			}
			elseif($regular3!="") 
			{
				$regularx=$regular3;
			}
			
			if($regularx!="") 
			{
				$auxreg = explode(":",$regular4->format('%H:%I'));
				$auxreg1 = "PT".$auxreg[0]."H".$auxreg[1]."M";
				$regular5x = new DateTime($regularx->format('H:i'));
				$regular4 = $regular5x->add(new DateInterval($auxreg1));
			}
			
			if(($salida1<=$limiteExt) || ($regular4<$limite))
			{
				
				//$regularsum=$regular1->add($regular2);
				if($regular4>$limite)
				{
					$extra4 = $regular4->diff($limite);
					//echo $extra2 = $extra2->format('%H:%I');
					$extra4= new DateTime($extra4->format('%H:%I'));
					$regular4 = $limite;
					
					if($extra4>$hExt)
					{
						$extraExt4 = $hExt->diff($extra4);
						$extra4 = $hExt;
					}
				}
			}
			elseif(($salida1>$limiteExt) && ($regular4>$limite))
			{
				
				/*$regular2 = $ealmu->diff($limiteExt);
				//$regular2= $aux2->format('%H:%I');
				
				if($regular1!="") 
				{
					$auxreg = explode(":",$regular2->format('%H:%I'));
					$auxreg1 = "PT".$auxreg[0]."H".$auxreg[1]."M";
					$regular5x = new DateTime($regular1->format('%H:%I'));
					$regular2 = $regular5x->add(new DateInterval($auxreg1));
				}*/
				//$regularsum=$regular1->add($regular2);

				if($extra2!="") 
				{
					$extrax=$extra2;
				}
				elseif($extra3!="") 
				{
					$extrax=$extra3;
				}
				else
				{
					$extrax=new DateTime('00:00');
				}

				if($regular4>$limite)
				{
					$extra4 = $regular4->diff($limite);
					$extra4xxxx= new DateTime($extrax->format('H:i'));

					//$extra2 = $extra2->diff($limite);
					//echo $extra2 = $extra2->format('%H:%I');
					$extraNoc4x = $limiteExt->diff($salida1);
					$extraNoc4xxxx = new DateTime($extraNoc4x->format('%H:%I'));
					//echo $extra2->format('%H:%I');
					/*if($extra2<$extraNoc2x)
					{
						$extraNoc2x = $extra2;
						$extra2 = "";
					}
					else
					{*/
						$extraNoc4xx = new DateTime($extraNoc4x->format('%H:%I'));

						$extra4= new DateTime($extrax->format('H:i'));

						$extra4 = $extra4->diff($extraNoc4xx);

						$extra4= new DateTime($extra4->format('%H:%I'));
						$regular4 = $limite;
					//}
					
					if($extra4>$hExt)
					{
						$extraExt4 = $hExt->diff($extra4);
						$extra4 = $hExt;
					}
				}
				
				$extraNoc4 = $extraNoc4x;//$limiteExt->diff($salida);
				//echo $extraNoc2->format('%H:%I');
				//$extraNoc2x = new DateTime($extraNoc2->format('%H:%I'));
				//$extraNoc2 = $auxExt2->format('%H:%I');
				if($extraNoc4x>$hExt)
				{
					//exit;
					$extraNocExt4 = $hExt->diff($extraNoc4x);
					$extraNoc4 = $hExt;
				}
			}
		}
					

		if($regular4!="")
		{
			$regular = $regular4->format('%H:%I');
			if(strpos($regular,"%")!==false)
			{
				$regular=$regular4->format('H:i');
			}
			
			if($extra4!="")
				$extra = $extra4->format('%H:%I');
			if(strpos($extra,"%")!==false)
			{
				$extra=$extra4->format('H:i');
			}
		
			if($extraExt4!="")
				$extraExt = $extraExt4->format('%H:%I');


			
			//echo $extra3->format('%H:%I');
			if(($extra4!="")&&($extra4!="00:00"))
				$extra4 = new DateTime($extra4->format('H:i'));
			if($extraNoc4!="")
			{
				if($extra4<$hExt)
				{
					$extraNoc = $extraNoc4->format('%H:%I');

					if(strpos($extraNoc,"%")!==false)
					{
						$extraNoc=$extraNoc4->format('H:i');
					}
				}
				elseif($extraNocExt4=="")
				{
					$extraNocExt = $extraNoc4->format('%H:%I');
				}
			}	
			//echo $extraNocExt3;
			if($extraNocExt4!="")
			{
				if($extra4<$hExt)
				{
					$extraNocExt = $extraNocExt4->format('%H:%I');
				}
				else
				{
					$auxreg = explode(":",$extraNoc4->format('H:i'));
					$auxreg1 = "PT".$auxreg[0]."H".$auxreg[1]."M";
					$extraNocExt5 = new DateTime($extraNocExt4->format('%H:%I'));
					$extraNocExt6 = $extraNocExt5->add(new DateInterval($auxreg1));
					$extraNocExt = $extraNocExt6->format('H:i');
				}
			}
		}
		elseif($regular3!="")
		{
			$regular = $regular3->format('%H:%I');
			if(strpos($regular,"%")!==false)
			{
				$regular=$regular3->format('H:i');
			}
			
			if($extra3!="")
				$extra = $extra3->format('%H:%I');
			if(strpos($extra,"%")!==false)
			{
				$extra=$extra3->format('H:i');
			}
		
			if($extraExt3!="")
				$extraExt = $extraExt3->format('%H:%I');


			
			//echo $extra3->format('%H:%I');
			if($extra3!="")
				$extra3 = new DateTime($extra3->format('%H:%I'));		
			if($extraNoc3!="")
			{
				if($extra3<$hExt)
				{
					$extraNoc = $extraNoc3->format('%H:%I');

					if(strpos($extraNoc,"%")!==false)
					{
						$extraNoc=$extraNoc3->format('H:i');
					}
				}
				elseif($extraNocExt3=="")
				{
					$extraNocExt = $extraNoc3->format('%H:%I');
				}
			}	
			//echo $extraNocExt3;
			if($extraNocExt3!="")
			{
				if($extra3<$hExt)
				{
					$extraNocExt = $extraNocExt3->format('%H:%I');
				}
				else
				{
					$auxreg = explode(":",$extraNoc3->format('H:i'));
					$auxreg1 = "PT".$auxreg[0]."H".$auxreg[1]."M";
					$extraNocExt5 = new DateTime($extraNocExt3->format('%H:%I'));
					$extraNocExt4 = $extraNocExt5->add(new DateInterval($auxreg1));
					$extraNocExt = $extraNocExt4->format('H:i');
				}
			}
		}
		elseif(($regular1!="")&&($regular2!=""))
		{
			
			/*$auxreg = explode(":",$regular2->format('%H:%I'));
			$auxreg1 = "PT".$auxreg[0]."H".$auxreg[1]."M";
			$regular5 = new DateTime($regular1->format('%H:%I'));
			$regular4 = $regular5->add(new DateInterval($auxreg1));
			$regular = $regular4->format('H:i');
			*/
			$regular = $regular2->format('H:i');
			/*
			$auxreg = explode(":",$extra1);
			$auxreg1 = "PT".($auxreg[0]=="" ? 0 : $auxreg[0])."H".($auxreg[1]=="" ? 0 : $auxreg[1])."M";
			$extra5 = new DateTime($extra2);
			$extra4 = $extra5->add(new DateInterval($auxreg1)); 
			$extra = $extra4->format('H:i');
			*/
			//echo $extra2->format('H:i');
			/*if($extra2<$extraNoc2)
			{
				$extraNoc2 = $extra2;
				$extra2 = "";
			}*/

			if($extra2!="")
				$extra = $extra2->format('%H:%I');
			if(strpos($extra,"%")!==false)
			{
				$extra=$extra2->format('H:i');
			}
			
			if(($extraExt1!="")&&($extraExt2!=""))
			{
				$auxreg = explode(":",$extraExt1);
				$auxreg1 = "PT".($auxreg[0]=="" ? 0 : $auxreg[0])."H".($auxreg[1]=="" ? 0 : $auxreg[1])."M";
				$extraExt5 = new DateTime($extraExt2);
				$extraExt4 = $extraExt5->add(new DateInterval($auxreg1));
				$extraExt = $extraExt4->format('H:i');
			}
			elseif(($extraExt1!="")||($extraExt2!=""))
			{
				$extraExt = $extraExt2->format('%H:%I');
				if(strpos($extraExt,"%")!==false)
				{
					$extraExt=$extraExt2->format('%H:%I');
				}
			}
		
			if(($extraNoc1!="")&&($extraNoc2!=""))
			{
				$auxreg = explode(":",$extraNoc1->format('%H:%I'));
				$auxreg1 = "PT".($auxreg[0]=="" ? 0 : $auxreg[0])."H".($auxreg[1]=="" ? 0 : $auxreg[1])."M";
				$extraNoc5 = new DateTime($extraNoc2->format('%H:%I'));
				$extraNoc4 = $extraNoc5->add(new DateInterval($auxreg1));
				
				if($extra2<$hExt)
				{
					$extraNoc = $extraNoc4->format('H:i');
				}
				elseif(($extraNocExt2=="")&&($extraNocExt1==""))
				{
					$extraNocExt = $extraNoc4->format('H:i');
				}
				//exit;
			}
			elseif($extraNoc2!="")
			{
				$auxreg = explode(":",$extra2->format('H:i'));
				$auxreg1 = "PT".($auxreg[0]=="" ? 0 : $auxreg[0])."H".($auxreg[1]=="" ? 0 : $auxreg[1])."M";
				try
				{
					$extraNoc5x = new DateTime($extraNoc2->format('H:i'));
				}
				catch(Exception $e)
				{
					$extraNoc5x = new DateTime($extraNoc2->format('%H:%I'));
				}
				$extraNoc4x = $extraNoc5x->add(new DateInterval($auxreg1));

				if($extraNoc4x<$hExt)
				{
					$extraNoc= $extraNoc2->format('%H:%I');
					if(strpos($extraNoc,"%")!==false)
					{
						$extraNoc= $extraNoc2->format('H:i');
					}
				}
				elseif($extraNoc4x>=$hExt)
				{
					$extraNoc22= $extra2->diff($hExt);
					$extraNoc= $extraNoc22->format('%H:%I');
					if(strpos($extraNoc,"%")!==false)
					{
						$extraNoc= $extraNoc22->format('H:i');
					}
					try
					{
						$extraNoc2x = new DateTime($extraNoc2->format('H:i'));
					}
					catch(Exception $e)
					{
						$extraNoc2x = new DateTime($extraNoc2->format('%H:%I'));	
					}
					$extraNoc22x = new DateTime($extraNoc22->format('%H:%I'));
					$extraNocExt22 = $extraNoc2x->diff($extraNoc22x);
					$extraNocExt= $extraNocExt22->format('%H:%I');
					if(strpos($extraNocExt,"%")!==false)
					{
						$extraNocExt= $extraNocExt22->format('H:i');
					}
				}
				elseif($extraNocExt2=="")
				{
					$extraNocExt= $extraNoc2->format('%H:%I');
					if(strpos($extraNocExt,"%")!==false)
					{
						$extraNocExt= $extraNoc2->format('H:i');
					}
				}
			}
			else
			{
				$extraNoc = "";	
			}
			
			if(($extraNocExt2!="")&&($extraNocExt1!=""))
			{
				if($extra2<$hExt)
				{
					$auxreg = explode(":",$extraNocExt1);
					$auxreg1 = "PT".($auxreg[0]=="" ? 0 : $auxreg[0])."H".($auxreg[1]=="" ? 0 : $auxreg[1])."M";
					$extraNocExt5 = new DateTime($extraNocExt2);
					$extraNocExt4 = $extraNocExt5->add(new DateInterval($auxreg1));
					$extraNocExt = $extraNocExt4->format('H:i');
				}
				else
				{
					$auxreg = explode(":",$extraNocExt1);
					$auxreg1 = "PT".($auxreg[0]=="" ? 0 : $auxreg[0])."H".($auxreg[1]=="" ? 0 : $auxreg[1])."M";
					$extraNocExt5 = new DateTime($extraNocExt2);
					$extraNocExt4 = $extraNocExt5->add(new DateInterval($auxreg1));

					$auxreg = explode(":",$extra2->format('%H:%I'));
					$auxreg1 = "PT".($auxreg[0]=="" ? 0 : $auxreg[0])."H".($auxreg[1]=="" ? 0 : $auxreg[1])."M";
					$extraNocExt6 = new DateTime($extraNocExt4);
					$extraNocExt7 = $extraNocExt6->add(new DateInterval($auxreg1));

					$extraNocExt = $extraNocExt7->format('H:i');
				}
			}
			elseif($extraNocExt2!="")
			{
				if($extra2<$hExt)
				{
					$extraNocExt= $extraNocExt2->format('%H:%I');
					if(strpos($extraNocExt,"%")!==false)
					{
							$extraNocExt= $extraNocExt2->format('H:i');
					}
				}
				else
				{
					$auxreg = explode(":",$extraNoc2->format('H:i'));
					$auxreg1 = "PT".$auxreg[0]."H".$auxreg[1]."M";
					$extraNocExt5 = new DateTime($extraNocExt2->format('%H:%I'));
					$extraNocExt4 = $extraNocExt5->add(new DateInterval($auxreg1));
					$extraNocExt = $extraNocExt4->format('H:i');
				}
			}
			/*else
			{
				$extraNocExt = "";	
			}*/
			if($extra2xxxx<$extraNoc2xxxx)
			{
				$extraNoc = $extra2xxxx->format('H:i');
				$extra = "";

				if($extra2xxxx>$hExt)
				{
					$extraNocExt = $hExt->diff($extra2xxxx);
					$extraNocExt = $extraNocExt->format('%H:%I');
					$extraNoc = $hExt->format('H:i');
				}
				else
				{
					$extraNocExt="";
				}
			}
		}
		elseif($regular1!="")
		{
			if($regular1!="")
				$regular = $regular1->format('%H:%I');
			 
			if($extra1!="")						
				$extra = $extra1->format('%H:%I');
			
			if($extraExt1!="")
				$extraExt = $extraExt1->format('%H:%I');
			
			if($extraNoc1!="")
				$extraNoc = $extraNoc1->format('%H:%I');

			if($extraNocExt1!="")
				$extraNocExt = $extraNocExt1->format('%H:%I');				
		}
		elseif($regular2)
		{
			if($regular2!="")
				$regular = $regular2->format('%H:%I');
			 
			if($extra2!="")
				$extra = $extra2->format('%H:%I');
				
			if($extraExt2!="")
				$extraExt = $extraExt2->format('%H:%I');
			
			if($extraNoc2!="")
				$extraNoc = $extraNoc2->format('%H:%I');
			if(strpos($extraNoc,"%")!==false)
			{
					$extraNoc= $extraNoc2->format('H:i');
			}
				
			if($extraNocExt2!="")
				$extraNocExt = $extraNocExt2->format('%H:%I');					
		}
		
		if($regular!="")
		{
			$regu = new DateTime($regular);
			//$limite = new DateTime("08:00");
			if($regu<$limite)
			{
				$tardanzas = $regu->diff($limite);
				$tardanza = $tardanzas->format('%H:%I');
				//$regular="08:00";			
			}
			if($regular=="07:30")
			{
				$regular="08:00";
			}
		}


					
		if(date("w",strtotime($fecha))==0)
		{	
			$domingo=$regular;
			$regular="";
			
			//$extraDom2 = new DateTime($extraExt);
			if($extraExt!="")
			{
				$extraDom1 = new DateTime($extra);
				$auxreg = explode(":",$extraExt);
				$auxreg1 = "PT".$auxreg[0]."H".$auxreg[1]."M";
				$extraDom3 = $extraDom1->add(new DateInterval($auxreg1)); 
				$extraDom = $extraDom3->format('H:i');
			}
			elseif($extra!="")
				$extraDom=$extra;
			if($extraNocExt!="")
			{
				$extraNocDom1 = new DateTime($extraNoc);
				//$extraNocDom2 = new DateTime($extraNocExt);
				$auxreg = explode(":",$extraNocExt);
				$auxreg1 = "PT".$auxreg[0]."H".$auxreg[1]."M";
				$extraNocDom3 = $extraNocDom1->add(new DateInterval($auxreg1)); 
				$extraNocDom = $extraNocDom3->format('H:i');	
			}
			elseif($extraNoc!="")
				$extraNocDom=$extraNoc;
			
			$extra =	$extraDom;
			$extraNoc = $extraNocDom;
			//$extra =	"";
			//$extraNoc = "";
			
		}

		$query="select dia_fiesta from nomcalendarios_tiposnomina where fecha='$fecha'";
		$result=query($query,$conexion);
		$fetch = fetch_array($result);

		if($fetch[dia_fiesta]==3)
		{
			$nacional=$regular;
			$regular="";
			
			//$extraDom2 = new DateTime($extraExt);
			if($extraExt!="")
			{
				$extraDom1 = new DateTime($extra);
				$auxreg = explode(":",$extraExt);
				$auxreg1 = "PT".$auxreg[0]."H".$auxreg[1]."M";
				$extraDom3 = $extraDom1->add(new DateInterval($auxreg1)); 
				$extraNac = $extraDom3->format('H:i');
			}
			elseif($extra!="")
				$extraNac=$extra;
			if($extraNocExt!="")
			{
				/*
				$extraNocDom1 = new DateTime($extraNoc);
				//$extraNocDom2 = new DateTime($extraNocExt);
				$auxreg = explode(":",$extraNocExt);
				$auxreg1 = "PT".$auxreg[0]."H".$auxreg[1]."M";
				$extraNocDom3 = $extraNocDom1->add(new DateInterval($auxreg1)); 
				$extraNocNac = $extraNocDom3->format('H:i');	*/
				$extraNocNac=$extraNocExt;
			}
			elseif($extraNoc!="")
				$extraNocNac=$extraNoc;
			
			$extra =	"";
			//$extraNoc = $extraNocDom;
			$extraNoc = "";
			$extraExt="";
			$extraNocExt="";
		}

		echo "Finalizo--".$regular."--".$extra."--".$domingo."--".$tardanza."--".$extraExt."--".$extraNoc."--".$extraNocExt."--".$nacional."--".$extraNac."--".$extraNocNac;
	break;

	case "picadilly2":
		$ent=$_GET[entrada];
		$salm=$_GET[salmuerzo];
		$ealm=$_GET[ealmuerzo];
		$sal=$_GET[salida];
		$fecha = $_GET[fecha];
		$ficha = $_GET[ficha];

		$query="select tur.* from nomturnos tur join nomcalendarios_personal per on tur.turno_id=per.turno_id where per.ficha='$ficha' and per.fecha='$fecha'";
		$result=query($query,$conexion);
		$fila = fetch_array($result);
		$turno = trim($fila[descripcion]);
		$entrada0 = new DateTime($fila[tolerancia_llegada]);
		$entrada1 = new DateTime($fila[entrada]);
		$entradatol = new DateTime($fila[tolerancia_entrada]);
		$saldesc1 = new DateTime($fila[inicio_descanso]);
		$entdesc1 = new DateTime($fila[salida_descanso]);
		$entdesctol = new DateTime($fila[tolerancia_descanso]);
		$salida1 = new DateTime($fila[salida]);
		$salidatol = new DateTime($fila[tolerancia_salida]);

		$lim1 = $saldesc1->diff($entrada1);
		$lim2 = $salida1->diff($entdesc1);

		$auxreg = explode(":",$lim2->format('%H:%I'));
		$auxreg1 = "PT".$auxreg[0]."H".$auxreg[1]."M";
		$limx = new DateTime($lim1->format('%H:%I'));
		$lim = $limx->add(new DateInterval($auxreg1));

		$entrada=$salmu=$ealmu=$salida=$regular=$ausen=$tardan=$incapac=$sobret=$feriado="";
		$entradaf=$salmuf=$ealmuf=$salidaf="";
		$tardanza=$extra=$domingo="";
		
		$extra=$regular=$regular1=$regular2=$regular3=$regular4="";
		$extra=$extra1=$extra2=$extra3=$extra4="";
		$extraNoc=$extraNoc1=$extraNoc2=$extraNoc3=$extraNoc4="";
		$extraExt=$extraExt1=$extraExt2=$extraExt3=$extraExt4="";
		$extraNocExt=$extraNocExt1=$extraNocExt2=$extraNocExt3=$extraNocExt4="";
		
		$hExt = new DateTime("03:00");
		$limiteExt = new DateTime("18:00");
		$limiteExtFin = new DateTime("06:00");
		$limiteExtNoc = new DateTime("23:59");		
		$limite = $lim;

			
		
		
		if($ent!="")
		{
			$entrada = new DateTime($ent);
			//if($entrada<=$entradatol)
			//	$entrada=$entrada1;
		} 
		if($salm!="") 
		{
			$salmu = new DateTime($salm);
		}
		if($ealm!="")
		{
			$ealmu = new DateTime($ealm);
		}
		if($sal!="") 
		{
			$salida = new DateTime($sal);
			//if($salida>=$salidatol)
			//	$salida=$salidatol;
		} 
		
		
		
		$extra11=$extra=$regular=$regular1=$regular2=$regular3="";
		$band = 0;
		//echo $entrada0->format('H:i');
		if(($entrada!="")&&($salmu!=""))
		{
			if($entrada<$entrada0)
			{
				if($entrada<$limiteExtFin)
				{
					$extraNoc1 = $entrada->diff($limiteExtFin);
					$extra1 = $entrada1->diff($limiteExtFin);
				}
				else
				{
					$extra1 = $entrada1->diff($entrada);
				}
				$regular1=$entrada1->diff($salmu);
			}
			elseif($entrada>$entradatol)
			{
				$tardanza1 = $entrada->diff($entrada1);
				$regular1=$entrada->diff($salmu);
			}
			elseif(($entrada>=$entrada0)&&($entrada<=$entradatol))
			{
				$regular1=$entrada1->diff($salmu);
			}

			//PARA LA HORA EXTRA Y TARDANZA DE ALMUERZO
			/*
			if($salmu<$saldesc1)
			{
				$tardanza3 = $salmu->diff($saldesc1);
			}
			elseif($salmu>$saldesc1)
			{
				$extra3 = $entrada1->diff($entrada);
				$regular1=$entrada1->diff($saldesc1);
			}
			*/
			$band=1;			
		}

		if(($ealmu!="")&&($salida!=""))
		{
			$regular2=$salida->diff($ealmu);

			//PARA LA HORA EXTRA Y TARDANZA DE ALMUERZO
			/*
			if($ealmu>$entdesc1)
			{
				$tardanza4 = $ealmu->diff($entdesc1);
			}
			elseif($ealmu<$entdesc1)
			{
				$extra3 = $entdesc1->diff($ealmu);
				$regular1=$entdesc1->diff($salida);
			}
			*/

			if($regular1)
			{
				$auxreg = explode(":",$regular2->format('%H:%I'));
				$auxreg1 = "PT".$auxreg[0]."H".$auxreg[1]."M";
				$regularx = new DateTime($regular1->format('%H:%I'));
				$regular = $regularx->add(new DateInterval($auxreg1));
			}
			else
			{
				$regular = $regular2;
				$regular = new DateTime($regular->format('%H:%I'));
			}

			if($salida>$salidatol)
			{
				//if(($salida>$limiteExt)&&($regular>$limite))
				if($salida>$limiteExt)
				{
					if($salida1<$limiteExt)
					{
						$extra2 = $salida1->diff($limiteExt);
						$extraNoc2 = $limiteExt->diff($salida);
						$regular = $limite;
					}
					else
					{
						$extraNoc2 = $salida1->diff($salida);
						$regular = $limite;
					}
				}
				//elseif(($salida<=$limiteExt)&&($regular>$limite))
				elseif($salida<=$limiteExt)
				{
					$extra2 = $salida1->diff($salida);
					$regular = $limite;
				}
			}
			//elseif(($salida>=$salida1)&&($salida<=$salidatol)&&($regular>$limite))
			elseif(($salida>=$salida1)&&($salida<=$salidatol))
			{
				$regular = $limite;
			}
			elseif($salida<$salida1)
			{
				$tardanza2 = $salida->diff($salida1);
			}

			if(($extra1!="")&&($extra2!=""))
			{
				$auxreg = explode(":",$extra2->format('%H:%I'));
				$auxreg1 = "PT".$auxreg[0]."H".$auxreg[1]."M";
				$extrax = new DateTime($extra1->format('%H:%I'));
				$extra = $extrax->add(new DateInterval($auxreg1));
				$extra = new DateTime($extra->format('H:i'));
				//echo $extra->format('H:i');
			}
			elseif($extra1!="")
			{
				$extra = $extra1;
				$extra = new DateTime($extra->format('%H:%I'));
			}
			elseif($extra2!="")
			{
				$extra = $extra2;
				$extra = new DateTime($extra->format('%H:%I'));
			}

			if(($extraNoc1!="")&&($extraNoc2!=""))
			{
				$auxreg = explode(":",$extraNoc2->format('%H:%I'));
				$auxreg1 = "PT".$auxreg[0]."H".$auxreg[1]."M";
				$extraxNoc = new DateTime($extraNoc1->format('%H:%I'));
				$extraNoc = $extraNocx->add(new DateInterval($auxreg1));
			}
			elseif($extraNoc1!="")
			{
				$extraNoc = $extraNoc1;
			}
			elseif($extraNoc2!="")
			{
				$extraNoc = $extraNoc2;
			}
			$band=1;
		}

		if(($entrada!="")&&($salida!="")&&($band==0))
		{
			if($entrada<$entrada0)
			{
				if($entrada<$limiteExtFin)
				{
					$extraNoc1 = $entrada->diff($limiteExtFin);
					$extra1 = $entrada1->diff($limiteExtFin);
				}
				else
				{
					$extra1 = $entrada1->diff($entrada);
				}
				//if($salida<=$salidatol)
				$regular=$entrada1->diff($salida);
				//else
			}
			elseif($entrada>$entradatol)
			{
				$tardanza1 = $entrada->diff($entrada1);
				$regular=$entrada->diff($salida);
			}
			elseif(($entrada>=$entrada0)&&($entrada<=$entradatol))
			{
				$regular=$entrada1->diff($salida);
			}
			//$regular = new DateTime($regular->format('%H:%I'));
			//$regular = new DateTime($regular->format('H:i'));
			$regular = new DateTime($regular->format('%H:%I'));
			if($salida>$salidatol)
			{

				if(($salida>$limiteExt)&&($regular>$limite))
				{
					if($salida1<$limiteExt)
					{
						$extra2 = $salida1->diff($limiteExt);
						$extraNoc2 = $limiteExt->diff($salida);
						$regular = $limite;
					}
					else
					{
						$extraNoc2 = $salida1->diff($salida);
						$regular = $limite;
					}
				}
				elseif(($salida<=$limiteExt)&&($regular>$limite))
				{
					$extra2 = $salida1->diff($salida);
					$regular = $limite;
				}
			}
			elseif(($salida>=$salida1)&&($salida<=$salidatol))
			{
				$regular = $limite;
			}
			elseif($salida<$salida1)
			{
				$tardanza2 = $salida->diff($salida1);
			}

			if(($extra1!="")&&($extra2!=""))
			{
				$auxreg = explode(":",$extra2->format('%H:%I'));
				$auxreg1 = "PT".$auxreg[0]."H".$auxreg[1]."M";
				$extrax = new DateTime($extra1->format('%H:%I'));
				$extra = $extrax->add(new DateInterval($auxreg1));

				$extra = new DateTime($extra->format('H:i'));
			}
			elseif($extra1!="")
			{
				$extra = $extra1;
				$extra = new DateTime($extra->format('%H:%I'));
			}
			elseif($extra2!="")
			{
				$extra = $extra2;
				$extra = new DateTime($extra->format('%H:%I'));
			}

			if(($extraNoc1!="")&&($extraNoc2!=""))
			{
				$auxreg = explode(":",$extraNoc2->format('%H:%I'));
				$auxreg1 = "PT".$auxreg[0]."H".$auxreg[1]."M";
				$extraNocx = new DateTime($extraNoc1->format('%H:%I'));
				$extraNoc = $extraNocx->add(new DateInterval($auxreg1));
			}
			elseif($extraNoc1!="")
			{
				$extraNoc = $extraNoc1;
			}
			elseif($extraNoc2!="")
			{
				$extraNoc = $extraNoc2;
			}
		}

		if(($regular=="")&&($regular1!=""))
		{
			$regular = new DateTime($regular1->format('%H:%I'));
			//$regular = $regular1;
		}

		if($extraNoc)
			$extraNoc = new DateTime($extraNoc->format('%H:%I'));
		
		if($extra>$hExt)
		{
			$extraExt = $extra->diff($hExt);
			$extra = $hExt;
		}

		if($extraNoc>$hExt)
		{
			$extraNocExt = $extraNoc->diff($hExt);
			$extraNoc = $hExt;
		}

		if($regular)
			$regular = $regular->format('H:i');
		if($extra)
			$extra = $extra->format('H:i');
		if($extraExt)
			$extraExt = $extraExt->format('%H:%I');
		if($extraNoc)
			$extraNoc = $extraNoc->format('H:i');
		if($extraNocExt)
			$extraNocExt = $extraNocExt->format('%H:%I');

		if(($tardanza1!="")&&($tardanza2!=""))
		{
			$auxreg = explode(":",$tardanza2->format('%H:%I'));
			$auxreg1 = "PT".$auxreg[0]."H".$auxreg[1]."M";
			$tardanzax = new DateTime($tardanza1->format('%H:%I'));
			$tardanza = $tardanzax->add(new DateInterval($auxreg1));
			$tardanza = $tardanza->format('H:i');
			//$tardanza = $tardanza->format('%H:%I');
		}
		elseif($tardanza1)
			$tardanza = $tardanza1->format('%H:%I');
		elseif($tardanza2)
			$tardanza = $tardanza2->format('%H:%I');

		if($regular!="")
		{
			$limxx=$limite->format('H:i');
			$regu = new DateTime($regular);
			//if($regu<$limite)
			//{
				//$tardanzas = $regu->diff($limite);
				//$tardanza = $tardanzas;//->format('%H:%I');
			//}
			if(($limxx=="07:30")&&($regu>=$limxx)&&($salida>=$salida1))
			{
				$regular="08:00";
			}
		}
		
		if(date("w",strtotime($fecha))==0)
		{	
			$domingo=$regular;
			$regular="";
			
			//$extraDom2 = new DateTime($extraExt);
			if($extraExt!="")
			{
				$extraDom1 = new DateTime($extra);
				$auxreg = explode(":",$extraExt);
				$auxreg1 = "PT".$auxreg[0]."H".$auxreg[1]."M";
				$extraDom3 = $extraDom1->add(new DateInterval($auxreg1)); 
				$extraDom = $extraDom3->format('H:i');
			}
			elseif($extra!="")
				$extraDom=$extra;
			if($extraNocExt!="")
			{
				$extraNocDom1 = new DateTime($extraNoc);
				//$extraNocDom2 = new DateTime($extraNocExt);
				$auxreg = explode(":",$extraNocExt);
				$auxreg1 = "PT".$auxreg[0]."H".$auxreg[1]."M";
				$extraNocDom3 = $extraNocDom1->add(new DateInterval($auxreg1)); 
				$extraNocDom = $extraNocDom3->format('H:i');	
			}
			elseif($extraNoc!="")
				$extraNocDom=$extraNoc;
			
			$extra =	$extraDom;
			$extraNoc = $extraNocDom;
			//$extra =	"";
			//$extraNoc = "";
			
		}

		$query="select dia_fiesta from nomcalendarios_tiposnomina where fecha='$fecha'";
		$result=query($query,$conexion);
		$fetch = fetch_array($result);

		if($fetch[dia_fiesta]==3)
		{
			$nacional=$regular;
			$regular="";
			
			//$extraDom2 = new DateTime($extraExt);
			if($extraExt!="")
			{
				$extraDom1 = new DateTime($extra);
				$auxreg = explode(":",$extraExt);
				$auxreg1 = "PT".$auxreg[0]."H".$auxreg[1]."M";
				$extraDom3 = $extraDom1->add(new DateInterval($auxreg1)); 
				$extraNac = $extraDom3->format('H:i');
			}
			elseif($extra!="")
				$extraNac=$extra;
			if($extraNocExt!="")
			{
				/*
				$extraNocDom1 = new DateTime($extraNoc);
				//$extraNocDom2 = new DateTime($extraNocExt);
				$auxreg = explode(":",$extraNocExt);
				$auxreg1 = "PT".$auxreg[0]."H".$auxreg[1]."M";
				$extraNocDom3 = $extraNocDom1->add(new DateInterval($auxreg1)); 
				$extraNocNac = $extraNocDom3->format('H:i');	*/
				$extraNocNac=$extraNocExt;
			}
			elseif($extraNoc!="")
				$extraNocNac=$extraNoc;
			
			$extra =	"";
			//$extraNoc = $extraNocDom;
			$extraNoc = "";
			$extraExt="";
			$extraNocExt="";
		}

		echo "Finalizo--".$regular."--".$extra."--".$domingo."--".$tardanza."--".$extraExt."--".$extraNoc."--".$extraNocExt."--".$nacional."--".$extraNac."--".$extraNocNac;
	break;
	
	case "electron":
		$ent=$_GET[entrada];
		$salm=$_GET[salmuerzo];
		$ealm=$_GET[ealmuerzo];
		$sal=$_GET[salida];
		$salidaDS=$_GET[salida_diasiguiente];
		$entradaEmer = $_GET[ent_emer];
		$salidaEmer = $_GET[sal_emer];
		$fecha = $_GET[fecha];
		$ficha = $_GET[ficha];

		$query="select tur.* from nomturnos tur join nomcalendarios_personal per on tur.turno_id=per.turno_id where per.ficha='$ficha' and per.fecha='$fecha'";
		$result=query($query,$conexion);
		$fila = fetch_array($result);
		$turno = trim($fila[descripcion]);
		$libre = $fila[libre];
		$tipo = $fila[tipo];
		$descpago = $fila[descpago];

		$tiempo = new tiempo();
		//$entrada0 = $tiempo->aminutos(substr($fila[tolerancia_llegada],0,2)=="00" ? "24".substr($fila[tolerancia_llegada], 2,5) : substr($fila[tolerancia_llegada], 0,5));
		$entrada0 = $tiempo->aminutos(substr($fila[tolerancia_llegada], 0,5));
		//$entrada1 = $tiempo->aminutos(substr($fila[entrada],0,2)=="00" ? "24".substr($fila[entrada], 2,5) : substr($fila[entrada], 0,5));
		$entrada1 = $tiempo->aminutos(substr($fila[entrada], 0,5));
		//$entradatol = $tiempo->aminutos(substr($fila[tolerancia_entrada],0,2)=="00" ? "24".substr($fila[tolerancia_entrada], 2,5) : substr($fila[tolerancia_entrada], 0,5));
		$entradatol = $tiempo->aminutos(substr($fila[tolerancia_entrada], 0,5));

		$saldesc1 = $tiempo->aminutos(substr($fila[inicio_descanso],0,2)=="00" ? "24".substr($fila[inicio_descanso], 2,5) : substr($fila[inicio_descanso], 0,5));
		//$entdesc1 = new DateTime((substr($fila[salida_descanso],0,2)=="00" ? "24".substr($fila[salida_descanso], 2,7) : $fila[salida_descanso]));
		$entdesc1 = $tiempo->aminutos(substr($fila[salida_descanso], 0,5));
		$entdesctol = $tiempo->aminutos(substr($fila[tolerancia_descanso],0,2)=="00" ? "24".substr($fila[tolerancia_descanso], 2,5) : substr($fila[tolerancia_descanso], 0,5));
		$salida1 = $tiempo->aminutos(substr($fila[salida],0,2)=="00" ? "24".substr($fila[salida], 2,5) : substr($fila[salida], 0,5));
		$salidatol = $tiempo->aminutos(substr($fila[tolerancia_salida],0,2)=="00" ? "24".substr($fila[tolerancia_salida], 2,5) : substr($fila[tolerancia_salida], 0,5));

		$descanso = $entdesc1 - $saldesc1;
		$lim1 = $saldesc1 - $entrada1;
		$lim2 = $salida1 - $entdesc1;
		//echo $lim2->format('%H:%I');

		$lim = $lim1 + $lim2;

		$entrada=$salmu=$ealmu=$salida=$regular=$ausen=$tardan=$incapac=$sobret=$feriado="";
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

			
		
		
		if($ent!="")
		{
			$ent = substr($ent, 0,4)=="24" ? "00".substr($ent, 2,5) : substr($ent, 0,5);
			$entrada = $tiempo->aminutos($ent);
		} 
		if($salm!="") 
		{
			$salmu = $tiempo->aminutos($salm);
		}
		if($ealm!="")
		{
			$ealmu = $tiempo->aminutos($ealm);
		}
		if($sal!="") 
		{
			$salidaPDS = $tiempo->aminutos(substr($sal, 0,5));
			$sal = substr($sal, 0,2)=="00" ? "24".substr($sal, 2,5) : substr($sal, 0,5);
			$salida = $tiempo->aminutos($sal);
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
		$regular1 = $regular2 = 0;
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
			//$regular2 = $ealmu - $salida;

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
					/*if($salida1<$limiteExt)
					{
						$extra2 = $salida1->diff($limiteExt);
						$extraNoc2 = $limiteExt->diff($salida);
						$regular = $limite;
					}
					else
					{
						$extraNoc2 = $salida1->diff($salida);
						$regular = $limite;	
					}*/
					//$extraNoc2 = $salida - $salida1;
					//$salida = $limiteExt;
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
				//elseif(($salida<=$limiteExt)&&($regular>$limite))
				/*elseif($salida<=$limiteExt)
				{
					$extra2 = $salida1->diff($salida);
					$regular = $limite;
				}*/
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
			//echo $limiteExtFin;
			if($entrada<=$entrada0)
			{
				if($entrada < $limiteExtFin)
				{
					$extraNoc1 = $limiteExtFin-$entrada;
					//$extra1 = $entrada1-$limiteExtFin;
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
				//$regular=$entrada->diff($salida);
			}
			/*elseif(($entrada>=$entrada0)&&($entrada<=$entradatol))
			{
				$regular=$entrada1->diff($salida);
			}
			//$regular = new DateTime($regular->format('%H:%I'));
			//$regular = new DateTime($regular->format('H:i'));
			$regular = new DateTime($regular->format('%H:%I'));*/
			if($salida > $salidatol)
			{

				if($salida > $limiteExt)
				{
					$extraNoc2 = $salida - $salida1;
					$salida = $salida1;
					/*if($salida1<$limiteExt)
					{
						$extra2 = $salida1->diff($limiteExt);
						$extraNoc2 = $limiteExt->diff($salida);
						$regular = $limite;
					}
					else
					{
						$extraNoc2 = $salida1->diff($salida);
						$regular = $limite;
					}*/
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

				/*if($nocturno == 1)
				{
					if($entrada<$entrada1)
						$entradaxxx=$entrada1;
					$lim1x = $saldesc1->diff($entradaxxx);
					$lim2x = $salida->diff($entdesc1);

					$auxreg = explode(":",$lim2x->format('%H:%I'));
					$auxreg1 = "PT".$auxreg[0]."H".$auxreg[1]."M";
					$limxx = new DateTime($lim1x->format('%H:%I'));
					$limxxx = $limxx->add(new DateInterval($auxreg1));
					$regular = $limxxx;
					if($regular>=$limite)
						$regular=$limite;
				}*/
			}
			$regular = $salida - $entrada;
			if($descpago==0)
			{
				$regular -= $descanso;
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
				$extra += ($regular - $limite);
				$regular=$limite;
			}

			if(($extraNoc1 != "") && ($extraNoc2 != ""))
				$extraNoc = $extraNoc1 + $extraNoc2;
			elseif($extraNoc1!="")
				$extraNoc = $extraNoc1;
			elseif($extraNoc2!="")
				$extraNoc = $extraNoc2;

			/*if($regular==$limite)
			{
				$extrah1 = $saldesc1->diff($entdesc1);
			}*/
		}

		if(($libre == 1) && ($salmu != "") && ($ealmu != ""))
		{
			$talmutol = $saldesc1 - $entdesctol;
			
			$talmu = $saldesc1 - $entdesc1;

			$talmur = $salmu - $ealmu;

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

		//if($extraNoc)
		//	$extraNoc = $extraNoc;
		
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
			//$limxx=$limite->format('H:i');
			//$regu = new DateTime($regular);
			//if($regu<$limite)
			//{
				//$tardanzas = $regu->diff($limite);
				//$tardanza = $tardanzas;//->format('%H:%I');
			//}
			//420 son 07:00
			if(($limite==420)&&($regular>=$limite)&&($salida>=$salida1))
			{
				$regular=480;
			}
		}

		$emergencia = $descansoincompleto = 0;
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

		echo "Finalizo--".$tiempo->ahoras($regular)."--".$tiempo->ahoras($extra)."--".$tiempo->ahoras($domingo)."--".$tiempo->ahoras($tardanza)."--".$tiempo->ahoras($extraExt)."--".$tiempo->ahoras($extraNoc)."--".$tiempo->ahoras($extraNocExt)."--".$tiempo->ahoras($nacional)."--".$tiempo->ahoras($extrah)."--".$tiempo->ahoras($extraMixDiurna)."--".$tiempo->ahoras($extraExtMixDiurna)."--".$tiempo->ahoras($extraMixNoc)."--".$tiempo->ahoras($extraExtMixNoc)."--".$tiempo->ahoras($emergencia)."--".$tiempo->ahoras($descansoincompleto)."--".$tiempo->ahoras($dialibre);
	break;
	case "zkteco":
		$data=[
			'id'                  => $_GET[id],
			'marcacion_disp_id'   => $_GET[marcacion_disp_id],
			'cod_enca'            => $_GET[cod_enca],
			'entrada'             => $_GET[entrada],
			'salmuerzo'           => $_GET[salmuerzo],
			'ealmuerzo'           => $_GET[ealmuerzo],
			'salida'              => $_GET[salida],
			'salida_diasiguiente' => $_GET[salida_diasiguiente],
			'ent_emer'            => $_GET[ent_emer],
			'sal_emer'            => $_GET[sal_emer],
			'fecha'               => $_GET[fecha],
			'ficha'               => $_GET[ficha]
		];

		include("funciones_reloj_detalle.php");
		$retorno=reloj_detalle__calcular(NULL,$data);		
		
		echo "Finalizo--".$retorno['ordinaria']."--".$retorno['extra']."--".$retorno['domingo']."--".$retorno['tardanza']."--".$retorno['extraext']."--".$retorno['extranoc']."--".$retorno['extraextnoc']."--".$retorno['nacional']."--".$retorno['descextra1']."--".$retorno['mixtodiurna']."--".$retorno['mixtoextdiurna']."--".$retorno['mixtonoc']."--".$retorno['mixtoextnoc']."--".$retorno['emergencia']."--".$retorno['descansoincompleto']."--".$retorno['dialibre']."--".$retorno['acum_semanal']."--".$retorno['turno']."--".$retorno['turno_horas_reales']."--".$retorno['turno_horas_teoricas']."--".$retorno['horas_reales']."--".$retorno['horas_teoricas'];

		//si $_GET[id], retornar nuevamnete los campos entra, salmuerzo, ...
		//para actualizarlo en la interfaz, por si se da el caso de dia siguiente se ruedan las horas
	break;

	case "zkteco_old":
		$ent=$_GET[entrada];
		$salm=$_GET[salmuerzo];
		$ealm=$_GET[ealmuerzo];
		$sal=$_GET[salida];
		$salidaDS=$_GET[salida_diasiguiente];
		$entradaEmer = $_GET[ent_emer];
		$salidaEmer = $_GET[sal_emer];
		$fecha = $_GET[fecha];
		$ficha = $_GET[ficha];

		$query="select tur.* from nomturnos tur join nomcalendarios_personal per on tur.turno_id=per.turno_id where per.ficha='$ficha' and per.fecha='$fecha'";
		$result=query($query,$conexion);
		$fila = fetch_array($result);
		$turno = trim($fila[descripcion]);
		$libre = $fila[libre];
		$tipo = $fila[tipo];
		$descpago = $fila[descpago];

		$tiempo = new tiempo();
		//$entrada0 = $tiempo->aminutos(substr($fila[tolerancia_llegada],0,2)=="00" ? "24".substr($fila[tolerancia_llegada], 2,5) : substr($fila[tolerancia_llegada], 0,5));
		$entrada0 = $tiempo->aminutos(substr($fila[tolerancia_llegada], 0,5));
		//$entrada1 = $tiempo->aminutos(substr($fila[entrada],0,2)=="00" ? "24".substr($fila[entrada], 2,5) : substr($fila[entrada], 0,5));
		$entrada1 = $tiempo->aminutos(substr($fila[entrada], 0,5));
		//$entradatol = $tiempo->aminutos(substr($fila[tolerancia_entrada],0,2)=="00" ? "24".substr($fila[tolerancia_entrada], 2,5) : substr($fila[tolerancia_entrada], 0,5));
		$entradatol = $tiempo->aminutos(substr($fila[tolerancia_entrada], 0,5));

		$saldesc1 = $tiempo->aminutos(substr($fila[inicio_descanso],0,2)=="00" ? "24".substr($fila[inicio_descanso], 2,5) : substr($fila[inicio_descanso], 0,5));
		//$entdesc1 = new DateTime((substr($fila[salida_descanso],0,2)=="00" ? "24".substr($fila[salida_descanso], 2,7) : $fila[salida_descanso]));
		$entdesc1 = $tiempo->aminutos(substr($fila[salida_descanso], 0,5));
		$entdesctol = $tiempo->aminutos(substr($fila[tolerancia_descanso],0,2)=="00" ? "24".substr($fila[tolerancia_descanso], 2,5) : substr($fila[tolerancia_descanso], 0,5));
		$salida1 = $tiempo->aminutos(substr($fila[salida],0,2)=="00" ? "24".substr($fila[salida], 2,5) : substr($fila[salida], 0,5));
		$salidatol = $tiempo->aminutos(substr($fila[tolerancia_salida],0,2)=="00" ? "24".substr($fila[tolerancia_salida], 2,5) : substr($fila[tolerancia_salida], 0,5));

		$descanso = $entdesc1 - $saldesc1;
		$lim1 = $saldesc1 - $entrada1;
		$lim2 = $salida1 - $entdesc1;
		//echo $lim2->format('%H:%I');

		$lim = $lim1 + $lim2;

		$entrada=$salmu=$ealmu=$salida=$regular=$ausen=$tardan=$incapac=$sobret=$feriado="";
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

			
		
		
		if($ent!="")
		{
			$ent = substr($ent, 0,4)=="24" ? "00".substr($ent, 2,5) : substr($ent, 0,5);
			$entrada = $tiempo->aminutos($ent);
		} 
		if($salm!="") 
		{
			$salmu = $tiempo->aminutos($salm);
		}
		if($ealm!="")
		{
			$ealmu = $tiempo->aminutos($ealm);
		}
		if($sal!="") 
		{
			$salidaPDS = $tiempo->aminutos(substr($sal, 0,5));
			$sal = substr($sal, 0,2)=="00" ? "24".substr($sal, 2,5) : substr($sal, 0,5);
			$salida = $tiempo->aminutos($sal);
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
		$regular1 = $regular2 = 0;
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
			//$regular2 = $ealmu - $salida;

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
					/*if($salida1<$limiteExt)
					{
						$extra2 = $salida1->diff($limiteExt);
						$extraNoc2 = $limiteExt->diff($salida);
						$regular = $limite;
					}
					else
					{
						$extraNoc2 = $salida1->diff($salida);
						$regular = $limite;	
					}*/
					//$extraNoc2 = $salida - $salida1;
					//$salida = $limiteExt;
					if($salida1<=$limiteExt)
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
					$extra2 = $salida - $salida1;
					$salida = $salida1;
				}
				//elseif(($salida<=$limiteExt)&&($regular>$limite))
				/*elseif($salida<=$limiteExt)
				{
					$extra2 = $salida1->diff($salida);
					$regular = $limite;
				}*/
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
			//echo $limiteExtFin;
			if($entrada<=$entrada0)
			{
				if($entrada < $limiteExtFin)
				{
					$extraNoc1 = $limiteExtFin-$entrada;
					//$extra1 = $entrada1-$limiteExtFin;
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
				//$regular=$entrada->diff($salida);
			}
			elseif(($entrada>=$entrada0)&&($entrada<=$entradatol))
			{
				$entrada = $entrada1;
			}
			//$regular = new DateTime($regular->format('%H:%I'));
			//$regular = new DateTime($regular->format('H:i'));
			//$regular = new DateTime($regular->format('%H:%I'));
			if($salida > $salidatol)
			{
				if($salida > $limiteExt && $salidatol < $limiteExt)
				{
					$extra2 = $limiteExt - $salida1;
					$extraNoc2 = $salida - $limiteExt;
					$salida = $salida1;
				}
				elseif($salida > $limiteExt)
				{
					$extraNoc2 = $salida - $salida1;
					$salida = $salida1;
					/*if($salida1<$limiteExt)
					{
						$extra2 = $salida1->diff($limiteExt);
						$extraNoc2 = $limiteExt->diff($salida);
						$regular = $limite;
					}
					else
					{
						$extraNoc2 = $salida1->diff($salida);
						$regular = $limite;
					}*/
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

				/*if($nocturno == 1)
				{
					if($entrada<$entrada1)
						$entradaxxx=$entrada1;
					$lim1x = $saldesc1->diff($entradaxxx);
					$lim2x = $salida->diff($entdesc1);

					$auxreg = explode(":",$lim2x->format('%H:%I'));
					$auxreg1 = "PT".$auxreg[0]."H".$auxreg[1]."M";
					$limxx = new DateTime($lim1x->format('%H:%I'));
					$limxxx = $limxx->add(new DateInterval($auxreg1));
					$regular = $limxxx;
					if($regular>=$limite)
						$regular=$limite;
				}*/
			}
			if($salida <= $salidatol && $salida >= $salida1)
			{
				$salida = $salida1;
			}
			$regular = $salida - $entrada;
			if($descpago==0 && $regular > 480)
			{
				/*$auxregx = 0;
				$auxregx = $regular - 480;
				if($auxregx <= $descanso)
					$regular -= $auxregx;
				else
					$regular -= $descanso;
				*/
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
				$extra += ($regular - $limite);
				$regular=$limite;
			}

			if(($extraNoc1 != "") && ($extraNoc2 != ""))
				$extraNoc = $extraNoc1 + $extraNoc2;
			elseif($extraNoc1!="")
				$extraNoc = $extraNoc1;
			elseif($extraNoc2!="")
				$extraNoc = $extraNoc2;

			/*if($regular==$limite)
			{
				$extrah1 = $saldesc1->diff($entdesc1);
			}*/
		}

		if(($libre == 1) && ($salmu != "") && ($ealmu != ""))
		{
			$talmutol = $entdesctol - $saldesc1;
			
			$talmu =  $entdesc1 -$saldesc1;

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

		//if($extraNoc)
		//	$extraNoc = $extraNoc;
		
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

		//echo "1".$tardanza1;
		//echo "2".$tardanza2;
		//echo "++3".$tardanza3;
		//echo "++4".$tardanza4;
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
			//$limxx=$limite->format('H:i');
			//$regu = new DateTime($regular);
			//if($regu<$limite)
			//{
				//$tardanzas = $regu->diff($limite);
				//$tardanza = $tardanzas;//->format('%H:%I');
			//}
			//420 son 07:00
			if(($limite==420)&&($regular>=$limite)&&($salida>=$salida1))
			{
				$regular=480;
			}
			if($regular>=$limite)
			{
				$regular=$limite;
			}
		}

		$emergencia = $descansoincompleto = 0;
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

		echo "Finalizo--".$tiempo->ahoras($regular)."--".$tiempo->ahoras($extra)."--".$tiempo->ahoras($domingo)."--".$tiempo->ahoras($tardanza)."--".$tiempo->ahoras($extraExt)."--".$tiempo->ahoras($extraNoc)."--".$tiempo->ahoras($extraNocExt)."--".$tiempo->ahoras($nacional)."--".$tiempo->ahoras($extrah)."--".$tiempo->ahoras($extraMixDiurna)."--".$tiempo->ahoras($extraExtMixDiurna)."--".$tiempo->ahoras($extraMixNoc)."--".$tiempo->ahoras($extraExtMixNoc)."--".$tiempo->ahoras($emergencia)."--".$tiempo->ahoras($descansoincompleto)."--".$tiempo->ahoras($dialibre);
	break;
}

	
?>