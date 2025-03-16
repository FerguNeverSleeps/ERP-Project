<?php
session_start();
ob_start();
$termino = $_SESSION['termino'];
?>
<?php
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // Always modified
header("Cache-Control: private, no-store, no-cache, must-revalidate"); // HTTP/1.1
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache"); // HTTP/1.0
?>
<link rel="stylesheet" type="text/css" href="dialog_box.css" />

<?php
include ("../header.php");
include("../lib/common.php");
include("func_bd.php");
$conexion=conexion();

$select = "select capital from nomempresa";
$result = query($select,$conexion);
$filaemp = fetch_array($result);
$empresa = $filaemp['capital'];

if ($_POST["procesar"]) 
{
    if ($_POST['txtFechaFin'] != "" && $_POST['txtFechaInicio'] != "" && $_FILES["archivo"]["name"]!="") 
    {
    	$fechaInicio = new DateTime($_POST['txtFechaInicio']);
		
		$fechaFin = new DateTime($_POST['txtFechaFin']);

		$insert = "INSERT INTO reloj_encabezado
          VALUES('', '" . date("Y-m-d") . "' ,'{$fechaInicio->format('Y-m-d')}', '{$fechaFin->format('Y-m-d')}')";
        $result = query($insert,$conexion);

        $select = "SELECT MAX(cod_enca) as cod FROM reloj_encabezado";
        $result = query($select,$conexion);
        $row = fetch_array($result);
        $idenc=$row[cod];

		$allowedExts = array("txt");
		$temp = explode(".", $_FILES["archivo"]["name"]);
		$extension = end($temp);
		if ((($_FILES["archivo"]["type"] == "text/plain"))
		&& ($_FILES["archivo"]["size"] < 2000000)
		&& in_array($extension, $allowedExts))
		{
			if ($_FILES["archivo"]["error"] > 0)
		   {
		   	$mensajefoto="Error Numero: " . $_FILES["archivo"]["error"] . "<br>";
		   }
		  	else
		   {
			   move_uploaded_file($_FILES["archivo"]["tmp_name"],"txt/" . $_FILES["archivo"]["name"]);
		      $archivo="txt/" . $_FILES["archivo"]["name"];
		    }
		}
		else
		{
			$mensajefoto="Archivo invalido";
		}
		$f = fopen($archivo, "r");
		$i=0;
		
    	switch ($empresa)
		{
			case "picadilly":

					
				
				while(!feof($f))
				{
					$data = array_values( array_filter(explode(" ", fgets($f))));

					$ficha = $data[0];
					if($ficha == "")
						continue;

					$query="select tur.descripcion from nomturnos tur join nompersonal per on tur.turno_id=per.turno_id where per.ficha='$ficha'";
					$result=query($query,$conexion);
					$fila = fetch_array($result);
					$turno = trim($fila[descripcion]);

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

					$fechass = $data[1];
					$horas = $data[2];
					if(strlen($horas)==4)
						$horas = "0".$horas.":00";
					elseif(strlen($horas)==5)
						$horas = $horas.":00";
				  	
				  
					$horass=explode(":",$data1[2]);
					if((($data[3]=="p.m.") || ($data[3]=="P.M.") || ($data[3]=="PM") || ($data[3]=="pm") || ($data[3]=="PM.") || ($data[3]=="pm.")) && ($horass[0] < 12 ))
					{
						$horas=($horass[0]+12).":".$horass[1].":".$horass[2];
					}	
				  
				  	$fecha=explode("/",$fechass);
				  
				  	$fechas = $fecha[2]."-".$fecha[1]."-".$fecha[0];
			
					$dates = trim($fechas." ".$horas);	
					

					//AQUI EMPIEZA				
					
					$date = new DateTime($dates);
					$dateh = new DateTime($horas);
					$fecha = $date->format('Y-m-d');
					$hora = $date->format('H:i');
					//$ficha = $fila[ficha];
					 
					if($i==0)
					{
						$fichaaux=$ficha;
						$fechaaux=$fecha;
						//$apenomaux=$fila[apenom];
					}
					
					if($fecha!=$fechaaux)
					{
						$extra=$regular=$regular1=$regular2=$regular3=$regular4="";
						$extra=$extra1=$extra2=$extra3=$extra4="";
						$extraNoc=$extraNoc1=$extraNoc2=$extraNoc3=$extraNoc4="";
						$extraExt=$extraExt1=$extraExt2=$extraExt3=$extraExt4="";
						$extraNocExt=$extraNocExt1=$extraNocExt2=$extraNocExt3=$extraNocExt4="";
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
								//echo $extraNoc2->format('%H:%I');
									$extraNoc2xx = new DateTime($extraNoc2x->format('%H:%I'));

									$extra2= new DateTime($extra2->format('%H:%I'));

									$extra2 = $extra2->diff($extraNoc2xx);

									$extra2= new DateTime($extra2->format('%H:%I'));
									$regular2 = $limite;
									
									if($extra2>$hExt)
									{
										$extraExt2 = $hExt->diff($extra2);
										$extra2 = $hExt;
									}
								}
								
								$extraNoc2 = $extraNoc2x;// $limiteExt->diff($salida);
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
								
								//$regular3= $aux3->format('%H:%I');
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


							
							/*$extraNocExt = $extraNocExt3->format('%H:%I');*/
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
									$extraNoc5x = new DateTime($extraNoc2->format('%H:%I'));
								}
								catch(Exception $e)
								{
									$extraNoc5x = new DateTime($extraNoc2->format('H:i'));	
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
										$extraNoc2x = new DateTime($extraNoc2->format('%H:%I'));
									}
									catch(Exception $e)
									{
										$extraNoc2x = new DateTime($extraNoc2->format('H:i'));
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
						
						if(date("w",strtotime($fechaaux))==0)
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

						$query="select dia_fiesta from nomcalendarios_tiposnomina where fecha='$fechaaux'";
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

						$query="INSERT INTO reloj_detalle (id, id_encabezado, ficha, fecha, entrada, salmuerzo, ealmuerzo, salida, ordinaria, extra, extraext, extranoc, extraextnoc, domingo, tardanza, nacional, extranac, extranocnac) values ('','$idenc',".$fichaaux.",'".$fechaaux."', '$entradaf', '$salmuf', '$ealmuf', '$salidaf', '$regular', '$extra', '$extraExt', '$extraNoc', '$extraNocExt', '$domingo', '$tardanza', '$nacional', '$extraNac', '$extraNocNac')";
						query($query,$conexion);
				  		//echo "<br>";
						//exit;
						$entrada=$salmu=$ealmu=$salida=$regular=$ausen=$tardan=$incapac=$sobret=$feriado="";
						$entradaf=$salmuf=$ealmuf=$salidaf="";
						$tardanza=$extra=$extraExt=$extraNoc=$extraNocExt=$domingo="";
						$nacional=$extraNac=$extraNocNac="";
						$extraDom="";
						$extraNocDom="";
						if($ficha!=$fichaaux)
						{
							$fichaaux=$ficha;	
							//$apenomaux=$fila[apenom];
						}
						$fechaaux=$fecha;
						$j=0;
					}
					
					//$turno = $fila[turno_id];
					if($j==0)
					{
						$entrada = $dateh;
					} 
					elseif($j==1) 
					{
						$salmu = $dateh;
					}
					elseif($j==2) 
					{
						$ealmu = $dateh;
					}
					elseif($j==3) 
					{
						$salida = $dateh;
					}
			
					$j++;
					$i++;	
				}
			break;
			case "jimmy":
				while(!feof($f))
				{
					$data = array_values( array_filter(explode(" ", fgets($f))));

					$ficha = $data[0];
					if($ficha == "")
						continue;


					$fechass = $data[1];
					$horas = trim($data[2]);
					if(strlen($horas)==4)
						$horas = "0".$horas.":00";
					elseif(strlen($horas)==5)
						$horas = $horas.":00";
				  	
				  
					$horass=explode(":",$horas);
					if((($data[3]=="p.m.") || ($data[3]=="P.M.") || ($data[3]=="PM") || ($data[3]=="pm") || ($data[3]=="PM.") || ($data[3]=="pm.")) && ($horass[0] < 12 ))
					{
						$horas=($horass[0]+12).":".$horass[1].":".$horass[2];
					}
					elseif($horass[0] < 12)
					{
						$horas=$horass[0].":".$horass[1].":".$horass[2];
					}
				  
				  	$fecha=explode("/",$fechass);
				  	$fechas = $fecha[2]."-".$fecha[1]."-".$fecha[0];
					$dates = trim($fechas." ".$horas);	
					

					//$query="select tur.descripcion from nomturnos tur join nompersonal per on tur.turno_id=per.turno_id where per.ficha='$ficha'";
					
					//AQUI EMPIEZA				

					$date = new DateTime($dates);
					$dateh = new DateTime($horas);
					$fecha = $date->format('Y-m-d');
					$hora = $date->format('H:i');
					//$ficha = $fila[ficha];
					 //exit;
					if($i==0)
					{
						$fichaaux=$ficha;
						$fechaaux=$fecha;
						//$apenomaux=$fila[apenom];
					}
					$query="select tur.* from nomturnos tur join nomcalendarios_personal per on tur.turno_id=per.turno_id where per.ficha='$fichaaux' and per.fecha='$fechaaux'";
					$result=query($query,$conexion);
					$filax = fetch_array($result);
					$nocturno = $filax[nocturno];

					if((($fecha!=$fechaaux)&&($nocturno==0))||(($nocturno==1)&&($j>=2)))
					{
						$query="select tur.* from nomturnos tur join nomcalendarios_personal per on tur.turno_id=per.turno_id where per.ficha='$fichaaux' and per.fecha='$fechaaux'";
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
						$nocturno = $fila[nocturno];

						$lim1 = $saldesc1->diff($entrada1);
						$lim2 = $salida1->diff($entdesc1);

						$auxreg = explode(":",$lim2->format('%H:%I'));
						$auxreg1 = "PT".$auxreg[0]."H".$auxreg[1]."M";
						$limx = new DateTime($lim1->format('%H:%I'));
						$lim = $limx->add(new DateInterval($auxreg1));
						
						
						$hExt = new DateTime("03:00");
						$limiteExt = new DateTime("18:00");
						$limiteExtFin = new DateTime("06:00");
						$limiteExtNoc = new DateTime("23:59");		
						$limite = $lim;


						$extra=$regular=$regular1=$regular2=$regular3=$regular4="";
						$extra=$extra1=$extra2=$extra3=$extra4="";
						$extraNoc=$extraNoc1=$extraNoc2=$extraNoc3=$extraNoc4="";
						$extraExt=$extraExt1=$extraExt2=$extraExt3=$extraExt4="";
						$extraNocExt=$extraNocExt1=$extraNocExt2=$extraNocExt3=$extraNocExt4="";
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
				
						$extra11=$extra=$regular=$regular1=$regular2=$regular3=$extrah="";
						$tardanza1=$tardanza2=$tardanza3=$tardanza4="";
						$extrah1=$extrah2=$extrah3=$extrah4="";
						
						$band = 0;
						//echo $entrada;
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
						
						if(date("w",strtotime($fechaaux))==0)
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

						$query="select dia_fiesta from nomcalendarios_tiposnomina where fecha='$fechaaux'";
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

						$query="INSERT INTO reloj_detalle (id, id_encabezado, ficha, fecha, entrada, salmuerzo, ealmuerzo, salida, ordinaria, extra, extraext, extranoc, extraextnoc, domingo, tardanza, nacional, extranac, extranocnac, descextra1) values ('','$idenc',".$fichaaux.",'".$fechaaux."', '$entradaf', '$salmuf', '$ealmuf', '$salidaf', '$regular', '$extra', '$extraExt', '$extraNoc', '$extraNocExt', '$domingo', '$tardanza', '$nacional', '$extraNac', '$extraNocNac','$extrah')";
						query($query,$conexion);
				  		//echo "<br>";
						//exit;
						$entrada=$salmu=$ealmu=$salida=$regular=$ausen=$tardan=$incapac=$sobret=$feriado="";
						$entradaf=$salmuf=$ealmuf=$salidaf="";
						$tardanza=$extra=$extraExt=$extraNoc=$extraNocExt=$domingo="";
						$nacional=$extraNac=$extraNocNac="";
						$extraDom="";
						$extraNocDom="";
						$extrah="";
						if($ficha!=$fichaaux)
						{
							$fichaaux=$ficha;	
							//$apenomaux=$fila[apenom];
						}
						$fechaaux=$fecha;
						$j=0;
					}
					
					//$turno = $fila[turno_id];
					if($nocturno==0)
					{
						if($j==0)
						{
							$entrada = $dateh;
						} 
						elseif($j==1) 
						{
							$salmu = $dateh;
						}
						elseif($j==2) 
						{
							$ealmu = $dateh;
						}
						elseif($j==3) 
						{
							$salida = $dateh;
						}
					}
					else
					{
						if($j==0)
						{
							$entrada = $dateh;
						} 
						elseif($j==1)
						{
							$salida = $dateh;
						}
					}
			
					$j++;
					$i++;	
				}
				
			break;

			case "picadilly2":

					
				
				while(!feof($f))
				{
					$data = array_values( array_filter(explode(" ", fgets($f))));

					$ficha = $data[0];
					if($ficha == "")
						continue;


					$fechass = $data[1];
					$horas = trim($data[2]);
					if(strlen($horas)==4)
						$horas = "0".$horas.":00";
					elseif(strlen($horas)==5)
						$horas = $horas.":00";
				  	
				  
					$horass=explode(":",$horas);
					if((($data[3]=="p.m.") || ($data[3]=="P.M.") || ($data[3]=="PM") || ($data[3]=="pm") || ($data[3]=="PM.") || ($data[3]=="pm.")) && ($horass[0] < 12 ))
					{
						$horas=($horass[0]+12).":".$horass[1].":".$horass[2];
					}
					elseif($horass[0] < 12)
					{
						$horas=$horass[0].":".$horass[1].":".$horass[2];
					}
				  
				  	$fecha=explode("/",$fechass);
				  	$fechas = $fecha[2]."-".$fecha[1]."-".$fecha[0];
					$dates = trim($fechas." ".$horas);	
					

					//$query="select tur.descripcion from nomturnos tur join nompersonal per on tur.turno_id=per.turno_id where per.ficha='$ficha'";
					
					//AQUI EMPIEZA				
					
					$date = new DateTime($dates);
					$dateh = new DateTime($horas);
					$fecha = $date->format('Y-m-d');
					$hora = $date->format('H:i');
					//$ficha = $fila[ficha];
					 //exit;
					if($i==0)
					{
						$fichaaux=$ficha;
						$fechaaux=$fecha;
						//$apenomaux=$fila[apenom];
					}
					
					if($fecha!=$fechaaux)
					{
						$query="select tur.* from nomturnos tur join nomcalendarios_personal per on tur.turno_id=per.turno_id where per.ficha='$fichaaux' and per.fecha='$fechaaux'";
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
						
						
						$hExt = new DateTime("03:00");
						$limiteExt = new DateTime("18:00");
						$limiteExtFin = new DateTime("06:00");
						$limiteExtNoc = new DateTime("23:59");		
						$limite = $lim;


						$extra=$regular=$regular1=$regular2=$regular3=$regular4="";
						$extra=$extra1=$extra2=$extra3=$extra4="";
						$extraNoc=$extraNoc1=$extraNoc2=$extraNoc3=$extraNoc4="";
						$extraExt=$extraExt1=$extraExt2=$extraExt3=$extraExt4="";
						$extraNocExt=$extraNocExt1=$extraNocExt2=$extraNocExt3=$extraNocExt4="";
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
				
						$extra11=$extra=$regular=$regular1=$regular2=$regular3="";

						
						$band = 0;
						//echo $entrada;
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

							$band=1;			
						}

						if(($ealmu!="")&&($salida!=""))
						{
							$regular2=$salida->diff($ealmu);

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

						
						if(date("w",strtotime($fechaaux))==0)
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

						$query="select dia_fiesta from nomcalendarios_tiposnomina where fecha='$fechaaux'";
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

						$query="INSERT INTO reloj_detalle (id, id_encabezado, ficha, fecha, entrada, salmuerzo, ealmuerzo, salida, ordinaria, extra, extraext, extranoc, extraextnoc, domingo, tardanza, nacional, extranac, extranocnac) values ('','$idenc',".$fichaaux.",'".$fechaaux."', '$entradaf', '$salmuf', '$ealmuf', '$salidaf', '$regular', '$extra', '$extraExt', '$extraNoc', '$extraNocExt', '$domingo', '$tardanza', '$nacional', '$extraNac', '$extraNocNac')";
						query($query,$conexion);
				  		//echo "<br>";
						//exit;
						$entrada=$salmu=$ealmu=$salida=$regular=$ausen=$tardan=$incapac=$sobret=$feriado="";
						$entradaf=$salmuf=$ealmuf=$salidaf="";
						$tardanza=$extra=$extraExt=$extraNoc=$extraNocExt=$domingo="";
						$nacional=$extraNac=$extraNocNac="";
						$extraDom="";
						$extraNocDom="";
						if($ficha!=$fichaaux)
						{
							$fichaaux=$ficha;	
							//$apenomaux=$fila[apenom];
						}
						$fechaaux=$fecha;
						$j=0;
					}
					
					//$turno = $fila[turno_id];
					if($j==0)
					{
						$entrada = $dateh;
					} 
					elseif($j==1) 
					{
						$salmu = $dateh;
					}
					elseif($j==2) 
					{
						$ealmu = $dateh;
					}
					elseif($j==3) 
					{
						$salida = $dateh;
					}
			
					$j++;
					$i++;	
				}
			break;
			
		}
		fclose($f);
		header("Location:control_acceso2.php");
    } 
    else 
    {
        ?>
        <script type="text/javascript">
            alert("ALERTA:\nDebe introducir todos los datos.");
        </script>
        <?php
    }
}
?>
<form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post" name="frmAgregar" id="frmAgregar" enctype="multipart/form-data">
    <?php
    titulo("Migracion de Empleados", "", "migrar_empleados.php", "acceso");
    ?><br><br><br><br><br><br><br><br>
    <div id="content" >
    <table width="400" align=center border="1" >
    <tr>
    <td>
        <table width="400" align=center border="0" >
            <tr class="tb-fila">
                <td><font size="2" face="Arial, Helvetica, sans-serif">Fecha de Inicio:</font></td>
                <td>
                    <input name="txtFechaInicio" type="text" id="txtFechaInicio" style="width:100px" value="" maxlength="60" onblur="javascript:actualizar('txtFechaInicio','fila_edad');">
                    <input name="image2" type="image" id="d_fechainicio" src="../lib/jscalendar/cal.gif" alt=""/>
                    <script type="text/javascript">Calendar.setup({inputField:"txtFechaInicio",ifFormat:"%Y-%m-%d",button:"d_fechainicio"});</script>
                </td>
            </tr>
            <tr >
            <td><font size="2" face="Arial, Helvetica, sans-serif">Fecha de Fin:</font></td>
            <td>
                <input name="txtFechaFin" type="text" id="txtFechaFin" style="width:100px" value="" maxlength="60" onblur="javascript:actualizar('txtFechaFin','fila_edad');">
                <input name="image2" type="image" id="d_fechafin" src="../lib/jscalendar/cal.gif" alt=""/>
                <script type="text/javascript">Calendar.setup({inputField:"txtFechaFin",ifFormat:"%Y-%m-%d",button:"d_fechafin"});</script>
            </td>
            </tr>
            
            
				<tr class="tb-fila">
            <td><font size="2" face="Arial, Helvetica, sans-serif">Archivo: </font></td>
            <td><input type="file" name="archivo" id="archivo">  
            </td>
            </tr>          
            
        </table>
        <table align=center>
            <tr >
                <td width="400">
                    <div align="center">
                        <input type="submit" name="procesar" id="procesar" value="Procesar">
                    </div>
                </td>
            </tr>
        </table>
        </td>
        </tr>
                </table>
    </div>
</form>
</body>
</html>