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
include ("../header4.php");
include("../lib/common.php");
include("func_bd.php");
require_once("../../procesos/funciones_importacion.php");
require_once("../../includes/phpexcel/Classes/PHPExcel.php");
require_once("../../includes/phpexcel/Classes/PHPExcel/IOFactory.php");
$conexion=conexion();

function formatearHora($hora)
{
	$horas = explode(":",$hora);
	if(trim($horas[0])=="00")
		$hora = "24:".$horas[1];
	else
		$hora = $horas[0].":".$horas[1];
	return $hora;
}

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

$select = "select capital from nomempresa";
$result = query($select,$conexion);
$filaemp = fetch_array($result);
$empresa = $filaemp['capital'];
$fechaini = $_POST['txtFechaInicio'];
$fechafin = $_POST['txtFechaFin'];
 

if ($_POST["procesar"] and $empresa !='excel') 
{
	//echo "txt log","<br>";
	$archivo_tipo="AMAXONIA";
	if(isset($_POST["archivo_tipo"]))
		$archivo_tipo=$_POST["archivo_tipo"];

	if(!isset($_POST["cod_enca"])):
		$fechaInicio = new DateTime($_POST['txtFechaInicio']);
		$fechaFin = new DateTime($_POST['txtFechaFin']);

		$fechaI = $fechaInicio->format('Y-m-d');
		$fechaF = $fechaFin->format('Y-m-d');

		$insert = "INSERT INTO reloj_encabezado (cod_enca, fecha_reg, fecha_ini, fecha_fin, status, usuario_creacion, tipo_nomina)
	      VALUES('', '" . date("Y-m-d") . "' ,'{$fechaInicio->format('Y-m-d')}', '{$fechaFin->format('Y-m-d')}','Pendiente','".$_SESSION['usuario']."','".$_POST['tipnom']."')";
	    $result = query($insert,$conexion);

	    $select = "SELECT MAX(cod_enca) as cod FROM reloj_encabezado";
	    $result = query($select,$conexion);
	    $row = fetch_array($result);
	    $idenc=$row[cod];
	else:
		//cuando recibe cod_enca, agregar los archivos a un encabezado ya creado
		$idenc=$_POST["cod_enca"];
		$sql="select * from reloj_encabezado where cod_enca='$idenc'";
		$result=mysqli_query($conexion, $sql);
		$reloj_encabezado = mysqli_fetch_array($result,MYSQL_ASSOC);
		$fechaI=$reloj_encabezado["fecha_ini"];
		$fechaF=$reloj_encabezado["fecha_fin"];
	endif;

    if($_FILES["archivo"]["name"]!="")
    {
	if(((isset($_POST['txtFechaFin']) && isset($_POST['txtFechaInicio'])) or isset($_POST["cod_enca"])) && $_FILES["archivo"]["name"]!="") 
	    {
		$allowedExts = array("txt","log","dat","csv","xls","xlsx");
		$temp = explode(".", $_FILES["archivo"]["name"]);
		$extension = end($temp);
		//(($_FILES["archivo"]["type"] == "text/plain"))&& 
		if (($_FILES["archivo"]["size"] < 2000000) && in_array($extension, $allowedExts))
		{
			if ($_FILES["archivo"]["error"] > 0)
		   {
		   	$mensajefoto="Error Numero: " . $_FILES["archivo"]["error"] . "<br>";
		   }
		  	else
		   {
			   move_uploaded_file($_FILES["archivo"]["tmp_name"],"txt/" . $_FILES["archivo"]["name"]);
		      $archivo="txt/" . $_FILES["archivo"]["name"];
		      $archivo_nombre=$_FILES["archivo"]["name"];
		    }
		}
		else
		{
			$mensajefoto="Archivo invalido";
			exit;
		}
		//echo $mensajefoto;
		//exit;
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
				$cadena=" 9999 31/12/2021 23:00
";
				$lineax = fopen($archivo, "r+");
				$cursor = 0;
				fseek($lineax, $cursor, SEEK_END);
				if($linea === "\n" || $linea === "\r")
					$cadena=$cadena;
				else
					$cadena="\n".$cadena;
				fwrite($lineax,$cadena);
				fclose($lineax);
				$kk=0;
				//$linecount=$linecount-2;
				while(!feof($f))
				{
					$data = array_values( array_filter(explode(" ", str_replace("	"," ",fgets($f)))));

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
					$kk++;
				}
			break;

			case "jimmy2":
				$cadena="9999	2021-12-31 23:00:00	0
";
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
					$data = array_values( array_filter(explode(" ", str_replace("	"," ",fgets($f)))));

					$ficha = $data[0];
					if(($ficha == "")||($ficha == "0"))
						continue;

					$fechas = $data[1];
					
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

					$consulta="INSERT INTO reloj_datos (id, ficha, fecha, hora) VALUES ('', '$ficha','$fechas','$horas');";
					$resultado = query($consulta,$conexion);
				}
				//exit;
				$kk=0;
				$consulta="SELECT * FROM reloj_datos WHERE 1 ORDER BY ficha asc, fecha asc, id asc;";
				$resultadox = query($consulta,$conexion);
				//$linecount=$linecount-2;
				//while(!feof($f))
				while($fetchx=fetch_array($resultadox))
				{
					/*$data = array_values( array_filter(explode(" ", str_replace("	"," ",fgets($f)))));

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
				  
				  	//$fecha=explode("/",$fechass);
				  	//$fechas = $fecha[2]."-".$fecha[1]."-".$fecha[0];
				  	$fechas = $fechass;
					$dates = trim($fechas." ".$horas);	*/

					$ficha = $fetchx[ficha];
					$fechas = $fetchx[fecha];
					$horas = $fetchx[hora];
					$horasx = explode(":",$horas);
					$disp_id = $fetchx[disp_id];
					//if($horasx[0]=="00")
					//	$horas="24:".$horasx[1].":".$horasx[2];
					$horas=$horasx[0].":".$horasx[1];
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
					$kk++;
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

			case "electron":
				$cadena=" 1, 1,  ,99999999,          ,12,59,12,31,99,  ,  ,  ,  ,  ,          ,          ,        ,               ,";
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
					$data = array_values( array_filter(explode(",", str_replace("	"," ",fgets($f)))));

					$ficha = $data[3];
					$disp_id = $data[0];
					if($ficha == "")
						continue;


					$fechass = $data[8]."/".$data[7]."/20".$data[9];
					//if(trim($data[5])=="00")
					//	$data[5]="24";
					$horas = trim($data[5]).":".trim($data[6]).":00";
					if(strlen($horas)==4)
						$horas = "0".$horas.":00";
					elseif(strlen($horas)==5)
						$horas = $horas.":00";
				  	
				  	$fecha=explode("/",$fechass);
				  	$fechas = $fecha[2]."-".$fecha[1]."-".$fecha[0];

					$consulta="INSERT INTO reloj_datos (id, ficha, fecha, hora, disp_id) VALUES ('', '$ficha','$fechas','$horas', '$disp_id');";
					$resultado = query($consulta,$conexion);
				}

				$consulta="SELECT * FROM reloj_datos WHERE fecha between '$fechaI' and '$fechaF' ORDER BY ficha asc, fecha asc, id asc;";
				$resultadox = query($consulta,$conexion);

				$continue = $i = $kk = 0;
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
					//if($horasx[0]=="00")
					//	$horas="24:".$horasx[1].":".$horasx[2];
					$horas=$horasx[0].":".$horasx[1];
					$dates = trim($fechas." ".$horas);	

					//AQUI EMPIEZA				

					$date = new DateTime($dates);
					$fecha = $date->format('Y-m-d');
					//echo "<br>";
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
					
					
					if((($fecha!=$fechaaux)&&($nocturno==0))||(($ficha!=$fichaaux)&&($nocturno==0))||(($nocturno==1)&&($j>=2)))
					{
						$query="select tur.* from nomturnos tur join nomcalendarios_personal per on tur.turno_id=per.turno_id where per.ficha='$fichaaux' and per.fecha='$fechaaux'";
						$result=query($query,$conexion);
						$fila = fetch_array($result);
						$turno = trim($fila[descripcion]);
						$libre = $fila[libre];
						$tipo = $fila[tipo];
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

						if($ealmu>$salida1)
						{
							$entradaEmerf = $ealmuf;
							$entradaEmer = $ealmu;
							$salidaEmerf = $salidaf;
							$salidaEmer = $salida;

							$salidaf = $salmuf;
							$salida = $salmu;

							$ealmuf = "";
							$ealmu = "";
							$salmuf = "";
							$salmu = "";
						}
						
						if(($entrada1-$entrada)>=90)
						{

							if(($salmu>0)&&($entradaEmer>0))
							{
								$xf = $entradaEmerf;
								$x = $entradaEmer;
								$yf = $salidaEmerf;
								$y = $salidaEmer;								


								$entradaEmerf = $entradaf;
								$entradaEmer = $entrada;
								$salidaEmerf = $salmuf;
								$salidaEmer = $salmu;

								$entradaf = $ealmuf;
								$entrada = $ealmu;
								$salmuf = $salidaf;
								$salmu = $salida;

								$ealmuf = $xf;
								$ealmu = $x;
								$salidaf = $yf;
								$salida = $y;
							}
							elseif($salmu>0)
							{
								$xf = $entradaEmerf;
								$x = $entradaEmer;
								$yf = $salidaEmerf;
								$y = $salidaEmer;								


								$entradaEmerf = $entradaf;
								$entradaEmer = $entrada;
								$salidaEmerf = $salmuf;
								$salidaEmer = $salmu;

								$entradaf = $ealmuf;
								$entrada = $ealmu;
								$salmuf = "";
								$salmu = "";

								$ealmuf = "";
								$ealmu = "";
								
							}
							else
							{
								$entradaEmerf = $entradaf;
								$entradaEmer = $entrada;
								$salidaEmerf = $salidaf;
								$salidaEmer = $salida;

								$entradaf = "";
								$entrada = "";
								$salidaf = "";
								$salida = "";
							}
						}
						
						
						//$query="INSERT INTO reloj_detalle (id, id_encabezado, ficha, fecha, entrada, salmuerzo, ealmuerzo, salida, ordinaria, extra, extraext, extranoc, extraextnoc, domingo, tardanza, nacional, extranac, extranocnac, descextra1, mixtodiurna, mixtoextdiurna, mixtonoc, mixtoextnoc, dialibre, emergencia, descansoincompleto, ent_emer, sal_emer) values ('','$idenc',".$fichaaux.",'".$fechaaux."', '".$entradaf."', '".$salmuf."', '".$ealmuf."', '".$salidaf."', '".$tiempo->ahoras($regular)."', '".$tiempo->ahoras($extra)."', '".$tiempo->ahoras($extraExt)."', '".$tiempo->ahoras($extraNoc)."', '".$tiempo->ahoras($extraNocExt)."', '".$tiempo->ahoras($domingo)."', '".$tiempo->ahoras($tardanza)."', '".$tiempo->ahoras($nacional)."', '".$tiempo->ahoras($extraNac)."', '".$tiempo->ahoras($extraNocNac)."','".$tiempo->ahoras($extrah)."','".$tiempo->ahoras($extraMixDiurna)."','".$tiempo->ahoras($extraExtMixDiurna)."','".$tiempo->ahoras($extraMixNoc)."','".$tiempo->ahoras($extraExtMixNoc)."','00:00','".$tiempo->ahoras($emergencia)."','".$tiempo->ahoras($descansoincompleto)."', '".$entradaEmerf."', '".$salidaEmerf."')";
						$query="INSERT INTO reloj_detalle (id, id_encabezado, ficha, fecha, entrada, salmuerzo, ealmuerzo, salida, ent_emer, sal_emer) values ('','$idenc',".$fichaaux.",'".$fechaaux."', '".$entradaf."', '".$salmuf."', '".$ealmuf."', '".$salidaf."', '".$entradaEmerf."', '".$salidaEmerf."')";
						//echo "<br>";
						query($query,$conexion);
				  		//echo "<br>";
						//exit;

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
					

					//$turno = $fila[turno_id];
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
					$i++;
					$kk++;
				}
				//exit;
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
						//$query_fixxx = "UPDATE reloj_detalle SET salida = entrada, entrada = '".$fetch_fix[ent_emer]."' WHERE id = '".($fetch_fix[id]+1)."' and ficha = '$fetch_fix[ficha]'";
						$query_fixxx = "UPDATE reloj_detalle SET entrada = '".$fetch_fix[ent_emer]."' WHERE id = '".($fetch_fix[id])."' and ficha = '$fetch_fix[ficha]'";
						$result_fixxx = query($query_fixxx,$conexion);
						$query_fixxx = "UPDATE reloj_detalle SET ent_emer = '' WHERE id = '".($fetch_fix[id])."' and ficha = '$fetch_fix[ficha]'";
						$result_fixxx = query($query_fixxx,$conexion);

						//$query_fixxx = "DELETE FROM  reloj_detalle  WHERE id = '".$fetch_fix[id]."'";
						//$result_fixxx = query($query_fixxx,$conexion);
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

						/*if(($fetch_fix[ficha]=='21')&&($fetch_fix[fecha]=="2015-07-21"))
						{
							echo $salida1." - ".substr($filaTur[salida],0,2)." - ".$entradamin2." - ".$fetch_fixx[entrada];
							exit;
						}*/

						if((($fetch_fixx[entrada]!='') &&  ($fetch_fixx[salida]=='') && ($fetch_fixx[ent_emer]=='') && ($fetch_fixx[sal_emer]=='')) && (($entradamin>=($entrada12x-60))&&($entradamin<=$entrada12x))&&($entrada12x>=1380))
						{
							//if(substr($fetch_fix[entrada],0,2)=='23')
								$entx=substr($filaTur2[entrada], 0,5);
							//else
							//	$entx=$fetch_fix[entrada];
							
							$query_fixxx = "UPDATE reloj_detalle SET salida = entrada, entrada = '".$entx."' WHERE id = '".$fetch_fixx[id]."'";
							$result_fixxx = query($query_fixxx,$conexion);

							$query_fixxx = "delete from reloj_detalle  WHERE id = '".$fetch_fix[id]."'";
							$result_fixxx = query($query_fixxx,$conexion);
							$xxx=1;
						}
						elseif((($fetch_fixx[entrada]=='') &&  ($fetch_fixx[salida]!='') && ($fetch_fixx[ent_emer]=='') && ($fetch_fixx[sal_emer]=='')) && (($entradamin>=($entrada12x-60))&&($entradamin<=$entrada12x))&&($entrada12x>=1380))
						{
							//if(substr($fetch_fix[entrada],0,2)=='23')
								$entx=substr($filaTur2[entrada], 0,5);//$entx="00:00";
							//else
							//	$entx=$fetch_fix[entrada];
							$query_fixxx = "UPDATE reloj_detalle SET  entrada = '".$entx."' WHERE id = '".$fetch_fixx[id]."'";
							$result_fixxx = query($query_fixxx,$conexion);
							
							//$query_fixxx = "UPDATE reloj_detalle SET ent_emer = ''  WHERE id = '".$fetch_fix[id]."'";
							//$result_fixxx = query($query_fixxx,$conexion);
							$query_fixxx = "delete from reloj_detalle  WHERE id = '".$fetch_fix[id]."'";
							$result_fixxx = query($query_fixxx,$conexion);
							$xxx=1;
						}
						elseif((($fetch_fixx[entrada]!='') &&  ($fetch_fixx[salida]=='') && ($fetch_fixx[ent_emer]=='') && ($fetch_fixx[sal_emer]=='')) && (($entradamin>=($entrada1-60))&&($entradamin<=($entrada1+120)))&&((($entradamin2+1440)>=$salida1)&&(($entradamin2+1440)<=($salida1+120))))
						{
							//if(substr($fetch_fix[entrada],0,2)=='23')
								$entx=substr($filaTur[salida],0,5);
							//else
							//	$entx=$fetch_fix[entrada];
							
							
							//$query_fixxx = "UPDATE reloj_detalle SET ent_emer = ''  WHERE id = '".$fetch_fix[id]."'";
							//$result_fixxx = query($query_fixxx,$conexion);
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
							//if(substr($fetch_fix[entrada],0,2)=='23')
								$entx=substr($filaTur[salida],0,5);
							//else
							//	$entx=$fetch_fix[entrada];
							
							
							//$query_fixxx = "UPDATE reloj_detalle SET ent_emer = ''  WHERE id = '".$fetch_fix[id]."'";
							//$result_fixxx = query($query_fixxx,$conexion);
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
							//if(substr($fetch_fix[entrada],0,2)=='23')
								$entx=substr($filaTur[salida],0,5);
							//else
							//	$entx=$fetch_fix[entrada];
							
							$query_fixxx = "UPDATE reloj_detalle SET  ent_emer = salida, salida = entrada, entrada = '".$fetch_fix[entrada]."' WHERE id = '".$fetch_fixx[id]."'";
							$result_fixxx = query($query_fixxx,$conexion);
							//$query_fixxx = "UPDATE reloj_detalle SET ent_emer = ''  WHERE id = '".$fetch_fix[id]."'";
							//$result_fixxx = query($query_fixxx,$conexion);
							//$query_fixxx = "UPDATE reloj_detalle SET  entrada = '' WHERE id = '".$fetch_fix[id]."'";
							//$result_fixxx = query($query_fixxx,$conexion);

							$query_fixxx = "DELETE FROM reloj_detalle  WHERE id = '".$fetch_fix[id]."'";
							$result_fixxx = query($query_fixxx,$conexion);
							
							
							$xxx=1;
						}



						/*elseif(($fetch_fixx[entrada]!='') &&  ($fetch_fixx[salida]!='') && ($fetch_fixx[ent_emer]!='') && ($fetch_fixx[sal_emer]==''))
						{
							if(substr($fetch_fix[ent_emer],0,2)=='23')
								$entx="00:00";
							else
								$entx=$fetch_fix[ent_emer];
							$query_fixxx = "UPDATE reloj_detalle SET sal_emer = ent_emer, ent_emer = salida, salida = entrada, entrada = '".$entx."' WHERE id = '".$fetch_fixx[id]."'";
							$result_fixxx = query($query_fixxx,$conexion);
							$query_fixxx = "UPDATE reloj_detalle SET ent_emer = ''  WHERE id = '".$fetch_fix[id]."'";
							$result_fixxx = query($query_fixxx,$conexion);
							$xxx=1;
						}
						elseif(($fetch_fixx[entrada]!='') && ($fetch_fixx[salmuerzo]=='') && ($fetch_fixx[ealmuerzo]=='') &&  ($fetch_fixx[salida]!='') && ($fetch_fixx[ent_emer]=='') && ($fetch_fixx[sal_emer]=='')&&(substr($fetch_fix[entrada],0,2)=='23'))
						{
							
							$entx="00:00";
						
						
							$query_fixxx = "UPDATE reloj_detalle SET  ent_emer = salida, salida = entrada, entrada = '".$entx."' WHERE id = '".$fetch_fixx[id]."'";
							$result_fixxx = query($query_fixxx,$conexion);
							$query_fixxx = "DELETE FROM reloj_detalle  WHERE id = '".$fetch_fix[id]."'";
							$result_fixxx = query($query_fixxx,$conexion);
							$xxx=1;
							$entx="";
							
						}
						elseif((($fetch_fixx[entrada]!='') &&  ($fetch_fixx[salida]!=''))&&(((substr($fetch_fixx[entrada],0,2)=='00')&&(($salida1>=1440)&&($salida1<1500)))||((substr($fetch_fixx[entrada],0,2)=='01')&&(($salida1>0)&&($salida1<120)))))
						{
							
								
							$salx="00:00";

							$query_fixxx = "UPDATE reloj_detalle SET salida_diasiguiente = '".$fetch_fixx[entrada]."' WHERE id = '".$fetch_fix[id]."'";
							$result_fixxx = query($query_fixxx,$conexion);

							$query_fixxx = "UPDATE reloj_detalle SET salida = '".$salx."' WHERE id = '".$fetch_fix[id]."'";
							$result_fixxx = query($query_fixxx,$conexion);

							$query_fixxx = "UPDATE reloj_detalle SET entrada=salida WHERE id = '".$fetch_fixx[id]."'";
							$result_fixxx = query($query_fixxx,$conexion);

							$query_fixxx = "UPDATE reloj_detalle SET salida='' WHERE id = '".$fetch_fixx[id]."'";
							$result_fixxx = query($query_fixxx,$conexion);
							$xxx=1;
							
						}
						/*elseif(((substr($fetch_fixx[entrada],0,2)=='00')||(substr($fetch_fixx[entrada],0,2)=='01')||(substr($fetch_fixx[entrada],0,2)=='02'))&&(($salida1>=1320)&&($salida1<1499)))
						{
							$entx="00:00";
						
							$query_fixxx = "UPDATE reloj_detalle SET salida = '$entx' WHERE id = '".$fetch_fix[id]."'";
							$result_fixxx = query($query_fixxx,$conexion);
							
							$query_fixxx = "UPDATE reloj_detalle SET entrada = '' WHERE id = '".$fetch_fixx[id]."'";
							$result_fixxx = query($query_fixxx,$conexion);
							$xxx=1;
						}*/
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
							//$query_fixxx = "UPDATE reloj_detalle SET ent_emer = ''  WHERE id = '".$fetch_fix[id]."'";
							//$result_fixxx = query($query_fixxx,$conexion);
							$query_fixxx = "UPDATE reloj_detalle SET  ent_emer = '' WHERE id = '".$fetch_fix[id]."'";
							$result_fixxx = query($query_fixxx,$conexion);

							//$query_fixxx = "DELETE FROM reloj_detalle  WHERE id = '".$fetch_fix[id]."'";
							//$result_fixxx = query($query_fixxx,$conexion);
							
							
							$xxx=1;
						}

					}
					/*elseif(($fetch_fix[entrada]!='') &&  ($fetch_fix[salida]!='') && ($fetch_fix[ent_emer]!='') && ($fetch_fix[sal_emer]==''))
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
							$query_fixxx = "UPDATE reloj_detalle SET salida = entrada, entrada = '".$entx."' WHERE id = '".$fetch_fixx[id]."'";
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
						$query_fixxx = "UPDATE reloj_detalle SET salida = entrada, entrada = '".$fetch_fix[ent_emer]."' WHERE id = '".($fetch_fix[id]+1)."'";
						$result_fixxx = query($query_fixxx,$conexion);

						$query_fixxx = "DELETE FROM  reloj_detalle  WHERE id = '".$fetch_fix[id]."'";
						$result_fixxx = query($query_fixxx,$conexion);
						$xxx=1;
					}
					/*elseif(($fetch_fix[entrada]!='') && ($fetch_fix[salmuerzo]=='') && ($fetch_fix[ealmuerzo]=='') && ($fetch_fix[salida]=='') && ($fetch_fix[ent_emer]=='') && ($fetch_fix[sal_emer]==''))
					{
						$query_fixx = "SELECT * FROM reloj_detalle WHERE id = '".($fetch_fix['id']+1)."' and ficha = '$fetch_fix[ficha]'";
						$result_fixx = query($query_fixx,$conexion);
						$fetch_fixx=fetch_array($result_fixx);
						echo $salida1;
						echo "<br>";
						if($fetch_fix[ficha]=='21')
							exit;
						
					}*/
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
					//$entrada0 = $tiempo->aminutos(substr($fila[tolerancia_llegada],0,2)=="00" ? "24".substr($fila[tolerancia_llegada], 2,5) : substr($fila[tolerancia_llegada], 0,5));
					$entrada0 = $tiempo->aminutos(substr($fila[tolerancia_llegada], 0,5));
					//$entrada1 = $tiempo->aminutos(substr($fila[entrada],0,2)=="00" ? "24".substr($fila[entrada], 2,5) : substr($fila[entrada], 0,5));
					$entrada1 = $tiempo->aminutos(substr($fila[entrada], 0,5));
					//$entradatol = $tiempo->aminutos(substr($fila[tolerancia_entrada],0,2)=="00" ? "24".substr($fila[tolerancia_entrada], 2,5) : substr($fila[tolerancia_entrada], 0,5));
					$entradatol = $tiempo->aminutos(substr($fila[tolerancia_entrada], 0,5));

					$saldesc1 = $tiempo->aminutos(substr($fila[inicio_descanso],0,2)=="00" ? "24".substr($fila[inicio_descanso], 2,3) : substr($fila[inicio_descanso], 0,5));
					//$entdesc1 = new DateTime((substr($fila[salida_descanso],0,2)=="00" ? "24".substr($fila[salida_descanso], 2,7) : $fila[salida_descanso]));
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
						
						if($entrada<$entrada0)
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
						//$acumExtras += $extra + $extraNoc + $extrah + $extraMixDiurna + $extraMixNoc;
						if($acumExtras+$extra>540)
						{
							$extraAux = $extra;
							$extra = 540 - $acumExtras;
							$extraExt += ($acumExtras + $extraAux) - 540;
							//$acumExtras += $extraExt;
							$acumExtras += $extraAux;
						}

						if($acumExtras+$extraNoc>540)
						{
							$extraNocAux = $extraNoc;
							$extraNoc = 540 - $acumExtras;
							$extraExtNoc += ($acumExtras + $extraNocAux) - 540;
							//$acumExtras += $extraExtNoc;
							$acumExtras += $extraNocAux;
						}

						if($acumExtras+$extraMixDiurna>540)
						{
							$extraMixDiurnaAux = $extraMixDiurna;
							$extraMixDiurna = 540 - $acumExtras;
							$extraExtMixDiurna += ($acumExtras + $extraMixDiurnaAux) - 540;
							//$acumExtras += $extraExtMixDiurna;
							$acumExtras += $extraMixDiurnaAux;
						}

						if($acumExtras+$extraMixNoc>540)
						{
							$extraMixNocAux = $extraMixNoc;
							$extraMixNoc = 540 - $acumExtras;
							$extraExtMixNoc += ($acumExtras + $extraMixNocAux) - 540;
							//$acumExtras += $extraExtMixNoc;
							$acumExtras += $extraMixNocAux;
						}
					}
					elseif($acumExtras>=540)
					{
						//$acumExtras += $extra + $extraNoc + $extrah + $extraMixDiurna + $extraMixNoc;
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
						//$acumExtras += $extra + $extraext + $extraNoc + $extraExtNoc + $extrah + $extraMixDiurna + $extraExtMixDiurna + $extraMixNoc + $extraExtMixNoc;
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

			break;
			case "zkteco":
				include("funciones_reloj_detalle.php");
				$data=[];
				$encabezado_id=$idenc;
				if($archivo_tipo=="FORMATO_A"){
					//formato usado en itesa
					//nombre del archivo=dispositivo
					//[FICHA][tabulador][FECHA(Y-m-d)][espacio][24H][tabulador][DISPOSITIVO]
					//Buscar el id del dispositivo por el codigo				    
					$dispositivo_codigo=explode("_", $archivo_nombre);
					$dispositivo_codigo=$dispositivo_codigo[0];
					$dispositivo_id=$db->Execute("SELECT id_dispositivo FROM reloj_info WHERE TRIM(TRAILING '\n' FROM  cod_dispositivo)='$dispositivo_codigo'");
					if(!isset($dispositivo_id[0]["id_dispositivo"])) {
						print "Dispositivo $dispositivo_codigo no existe en la base de datos.";
						exit;
					}
					$dispositivo_id=$dispositivo_id[0]["id_dispositivo"];
					if(in_array($dispositivo_id,["",NULL,0])) $dispositivo_id=1;
					while(($linea = fgets($f)) !== false):
					    //Dividir la linea por tabuladores
					    $linea=explode("\t",$linea);
					    $ficha=trim($linea[0]);
					    //Si la ficha es "", "9999", NULL o 0 saltar al siguiente registro
					    if(in_array($ficha, ["","9999",NULL,0])) continue;
					    //buscar si la ficha esta activa
					    $persona=$db->Execute("select estado from nompersonal where ficha='$ficha'");
					    $activo=false;
				        if(isset($persona[0]["estado"]))
				          if(trim(strtoupper($persona[0]["estado"]))!="EGRESADO")
				          	$activo=true;
				        if(!$activo) continue;

					    //Tomar la fecha y la hora, la fecha llevarla al format YYYY-MM-DD
					    $fecha_hora=explode(" ",trim($linea[1]));
					    $fecha=$fecha_hora[0];
					    //si la fecha del registro del txt no corresponde a la fecha del encabezado saltar
					    if(!($fecha>=$fechaI and $fecha<=$fechaF)) continue;
					    //llevar la hora a 24H
					    $hora=$fecha_hora[1];
					    $data[]=["ficha"=>$ficha, "fecha"=>$fecha, "hora"=>$hora, "dispositivo_id"=>$dispositivo_id];
					endwhile;
				}
				else if($archivo_tipo=="FORMATO_B"){
					//formato usado en relojin 
					//[FICHA][tabulador][FECHA(m/d/Y)][espacio][12H][espacio][AM|PM][tabulador][DISPOSITIVO]
					while(($linea = fgets($f)) !== false):
					    //Dividir la linea por tabuladores
					    $linea=explode("\t",$linea);
					    $ficha=trim($linea[0]);
					    //Si la ficha es "", "9999", NULL o 0 saltar al siguiente registro
					    if(in_array($ficha, ["","9999",NULL,0])) continue;
					    //buscar si la ficha esta activa
					    $persona=$db->Execute("select estado from nompersonal where ficha='$ficha'");
					    $activo=false;
				        if(isset($persona[0]["estado"]))
				          if(trim(strtoupper($persona[0]["estado"]))!="EGRESADO")
				          	$activo=true;
				        if(!$activo) continue;				        
					    //Buscar el id del dispositivo por el codigo
					    $dispositivo_codigo=trim($linea[2]);
					    $dispositivo_id=$db->Execute("SELECT id_dispositivo FROM reloj_info WHERE TRIM(TRAILING '\n' FROM  cod_dispositivo)='$dispositivo_codigo'");
					    $dispositivo_id=$dispositivo_id[0]["id_dispositivo"];
					    if(in_array($dispositivo_id,["",NULL,0])) $dispositivo_id=1;
					    //Tomar la fecha y la hora, la fecha llevarla al format YYYY-MM-DD
					    $fecha_hora=explode(" ",trim($linea[1]));
					    $fecha=explode("/",$fecha_hora[0]);
					    $fecha=$fecha[2]."-".str_pad($fecha[0],2,"0",STR_PAD_LEFT)."-".str_pad($fecha[1],2,"0",STR_PAD_LEFT);
					    //si la fecha del registro del txt no corresponde a la fecha del encabezado saltar
					    if(!($fecha>=$fechaI and $fecha<=$fechaF)) continue;
					    //llevar la hora a 24H
					    $hora=date("H:i:s", strtotime($fecha_hora[1]." ".strtoupper(str_replace(".", "", trim($fecha_hora[2])))));
					    $data[]=["ficha"=>$ficha, "fecha"=>$fecha, "hora"=>$hora, "dispositivo_id"=>$dispositivo_id];
					endwhile;		
				}
                else if($archivo_tipo=="FORMATO_C"){
					//FORMATO ANVIZ					
					while(($linea = fgets($f)) !== false):
					    //Dividir la linea por tabuladores					    
					    $ficha=trim(substr($linea, 55, 21));
					    //Si la ficha es "", "9999", NULL o 0 saltar al siguiente registro
					    if(in_array($ficha, ["","9999",NULL,0])) continue;
					    //buscar si la ficha esta activa
					    $persona=$db->Execute("select estado from nompersonal where ficha='$ficha'");
					    $activo=false;
				        if(isset($persona[0]["estado"]))
				          if(trim(strtoupper($persona[0]["estado"]))!="EGRESADO")
				          	$activo=true;
				        //if(!$activo) continue;				        
					    //Buscar el id del dispositivo por el codigo
					    $dispositivo_codigo=trim(substr($linea, 109, 12));
					    $dispositivo_id=$db->Execute("SELECT id_dispositivo FROM reloj_info WHERE TRIM(TRAILING '\n' FROM  cod_dispositivo)='$dispositivo_codigo'");
					    $dispositivo_id=$dispositivo_id[0]["id_dispositivo"];
					    if(in_array($dispositivo_id,["",NULL,0])) $dispositivo_id=1;
					    //Tomar la fecha y la hora, la fecha llevarla al format YYYY-MM-DD					    
                        $fecha_hora=trim(substr($linea, 76, 20));
                        $fecha_hora=str_replace("a. m.", "AM", $fecha_hora);
                        $fecha_hora=str_replace("p. m.", "PM", $fecha_hora);

                        $fecha_hora=explode(" ",trim($fecha_hora));
					    $fecha=explode("/",$fecha_hora[0]);
					    $fecha="20".$fecha[2]."-".str_pad($fecha[1],2,"0",STR_PAD_LEFT)."-".str_pad($fecha[0],2,"0",STR_PAD_LEFT); 
					    //si la fecha del registro del txt no corresponde a la fecha del encabezado saltar
					    //print "if(!($fecha>=$fechaI and $fecha<=$fechaF))";
					    if(!($fecha>=$fechaI and $fecha<=$fechaF)) continue;
					    //llevar la hora a 24H
					    $hora=date("H:i:s", strtotime($fecha_hora[1]." ".strtoupper(str_replace(".", "", trim($fecha_hora[2])))));
					    $data[]=["ficha"=>$ficha, "fecha"=>$fecha, "hora"=>$hora, "dispositivo_id"=>$dispositivo_id];
					endwhile;
				}
                else if($archivo_tipo=="FORMATO_D"){
					//FORMATO EXCEL	
					$xls         = 'Excel5';
					$xlsx        = 'Excel2007';

					$objReader   = PHPExcel_IOFactory::createReader($$extension);
					$objPHPExcel = $objReader->load($archivo);
					$objPHPExcel->setActiveSheetIndex(0);
					//  Get worksheet dimensions
					$sheet = $objPHPExcel->getSheet(0);
					$highestRow = $sheet->getHighestRow();
					$highestColumn = $sheet->getHighestColumn();
					//echo $highestRow;exit;

					$i=2;
					$fecha=null;
					$hora=null;
					$data = [];
					for($i=2;$i<$highestRow;$i++){

						$ficha = trim($objPHPExcel->getActiveSheet()->getCell('A' . $i)->getValue());
						$cellB = $objPHPExcel->getActiveSheet()->getCell('B' . $i)->getValue();
						$cellC = $objPHPExcel->getActiveSheet()->getCell('C' . $i)->getValue();
						$dispositivo_cod = $objPHPExcel->getActiveSheet()->getCell('D' . $i)->getValue();
						$cellE = $objPHPExcel->getActiveSheet()->getCell('E' . $i)->getValue();
						$cellF = $objPHPExcel->getActiveSheet()->getCell('F' . $i)->getValue();

						//echo "FILA ".$i."<br>";
						//echo "Col A: ".$ficha . ", Col B: ".$cellB . ", Col C: ".$cellC . ", Col D: ".$dispositivo_cod . ", Col E: ".$cellE . ", Col F: ".$cellF . "<br>";
						$fecha = date("Y-m-d", strtotime($cellC));
						$hora = date("H:i:s", strtotime($cellC));

						$dispositivo=$db->Execute("SELECT id_dispositivo FROM reloj_info WHERE TRIM(TRAILING '\n' FROM  cod_dispositivo)='$dispositivo_cod'");
						$dispositivo_id=1;
						if(isset($dispositivo[0]["estado"])){
							$dispositivo_id=$dispositivo[0]["id_dispositivo"];
							if(in_array($dispositivo_id,["",NULL,0]))
								$dispositivo_id=1;
						}

						
						//buscar si la ficha esta activa
						$persona=$db->Execute("select estado from nompersonal where ficha='$ficha'");
						$activo=false;
						if(isset($persona[0]["estado"]))
							if(trim(strtoupper($persona[0]["estado"]))!="EGRESADO")
								$activo=true;
							
						if($activo){
							$data[] = array(
								'ficha'          => $ficha,
								'fecha'          => $fecha,
								'hora'           => $hora,
								'dispositivo_id' => $dispositivo_id
							);
						}
						
						$fecha=null;
						$hora=null;
						$dispositivo_id=null;
						$i++;
					}
					//fin validacion se se consigue la extension del archivo
					//Fin carga de archivo en FORMATO_D
					//var_dump($data);exit;
				}
				else {
					//formato amaxonia
					//[FICHA][tabulador][AMAXONIA][tabulador][FECHA(m/d/Y)][espacio][12H][espacio][a.m|p.m][tabulador][Entrada|Salida][tabulador][DISPOSITIVO][tabulador][0]
					while(($linea = fgets($f)) !== false):
					    //Dividir la linea por tabuladores
					    $linea=explode("\t",$linea);
					    $ficha=trim($linea[0]);
					    //Si la ficha es "", "9999", NULL o 0 saltar al siguiente registro
					    if(in_array($ficha, ["","9999",NULL,0])) continue;
					    //buscar si la ficha esta activa
					    $persona=$db->Execute("select estado from nompersonal where ficha='$ficha'");
					    $activo=false;
				        if(isset($persona[0]["estado"]))
				          if(trim(strtoupper($persona[0]["estado"]))!="EGRESADO")
				          	$activo=true;
				        if(!$activo) continue;
					    //Buscar el id del dispositivo por el codigo
					    $dispositivo_codigo=trim($linea[4]);
					    $dispositivo_id=$db->Execute("SELECT id_dispositivo FROM reloj_info WHERE TRIM(TRAILING '\n' FROM  cod_dispositivo)='$dispositivo_codigo'");
					    $dispositivo_id=$dispositivo_id[0]["id_dispositivo"];
					    if(in_array($dispositivo_id,["",NULL,0])) $dispositivo_id=1;
					    //Tomar la fecha y la hora, la fecha llevarla al format YYYY-MM-DD
					    $fecha_hora=explode(" ",trim($linea[2]));
					    $fecha=explode("/",$fecha_hora[0]);
					    $fecha=$fecha[2]."-".$fecha[0]."-".$fecha[1];
					    //si la fecha del registro del txt no corresponde a la fecha del encabezado saltar
					    if(!($fecha>=$fechaI and $fecha<=$fechaF)) continue;
					    //llevar la hora a 24H
					    $hora=date("H:i:s", strtotime($fecha_hora[1]." ".strtoupper(str_replace(".", "", trim($fecha_hora[2])))));
					    $data[]=["ficha"=>$ficha, "fecha"=>$fecha, "hora"=>$hora, "dispositivo_id"=>$dispositivo_id];
					endwhile;					
				}				
				//ordenar el arreglo data, con la funcion data_ordenar
				usort($data, "data_ordernar");

				//regitrar el lote en reloj_detalle
				//print "\nAgregando reloj_detalle.";
				reloj_detalle__registrar_lote($data,$encabezado_id);
				//mover el txt de pendiente a procesados
				fclose($f);
			break;

			case "zkteco_old":
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

                                        $consulta_personal="SELECT personal_id FROM nompersonal WHERE ficha='$ficha';";
                                                        //echo "<br>";
                                        //echo $consulta_personal;
                                        //exit;
                                        $resultado_personal = query($consulta_personal,$conexion);
                                        $personal = fetch_array($resultado_personal);
                                        $personal_id=$personal[personal_id];

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
                                        
					$consulta="INSERT INTO reloj_datos (id, ficha, fecha, hora, disp_id) VALUES ('', '$ficha','$fechas','$horas', '$disp_id');";
					$resultado = query($consulta,$conexion);
                                        
                                          //INSERCION RELOJ_MARCACIONES
                                        if($ficha!="9999" && $personal_id!="" && $personal_id!=NULL && $personal_id!=0)
                                        {
                                            $consulta_marcaciones="INSERT INTO reloj_marcaciones (id, id_empleado, ficha_empleado, fecha, hora, dispositivo, tipo, estatus)
                                               VALUES ('','$personal_id','$ficha','$fechas','$horas', '$id_dispositivo', '$tipo_marcacion', '$estatus');";
                                            //echo $consulta_marcaciones;                                        
                                            $resultado_marcaciones = query($consulta_marcaciones,$conexion);
                                        }
				}
                                //exit;
				$consulta="SELECT * FROM reloj_datos WHERE fecha between '$fechaI' and '$fechaF' ORDER BY ficha asc, fecha asc, hora asc;";
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
					//if($horasx[0]=="00")
					//	$horas="24:".$horasx[1].":".$horasx[2];
					$horas=$horasx[0].":".$horasx[1];
					$dates = trim($fechas." ".$horas);	

					//AQUI EMPIEZA				

					$date = new DateTime($dates);
					$fecha = $date->format('Y-m-d');
					//echo "<br>";
					//$ficha = $fila[ficha];
					 //exit;
					if($i==0)
					{
						$fichaaux=$ficha;
						$fechaaux=$fecha;
						//$apenomaux=$fila[apenom];
					}
					$i = 1;
					
					$query="select tur.* from nomturnos tur join nomcalendarios_personal per on tur.turno_id=per.turno_id where per.ficha='$fichaaux' and per.fecha='$fechaaux'";
					$result=query($query,$conexion);
					$filax = fetch_array($result);
					$nocturno = $filax[nocturno];
					if($nocturno == "")
						$nocturno = 0;
					
					//echo $fecha." ".$fechaaux." ".$horas." ".$nocturno." ".$ficha." ".$fichaaux." ".$j;
					//echo "<br>";
					if((($fecha!=$fechaaux)&&($nocturno==0))||(($ficha!=$fichaaux)&&($nocturno==0))||(($nocturno==1)&&($j>=2)))
					{
						$query="select tur.* from nomturnos tur join nomcalendarios_personal per on tur.turno_id=per.turno_id where per.ficha='$fichaaux' and per.fecha='$fechaaux'";
						$result=query($query,$conexion);
						$fila = fetch_array($result);
						$turno = trim($fila[descripcion]);
						$libre = $fila[libre];
						$tipo = $fila[tipo];
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



						/*if($entradaEmer!="")
						{
							$entradaEmerf = $entradaEmer;
							$entradaEmer = $tiempo->aminutos($entradaEmer);
						}
						if($salidaEmer!="")
						{
							$salidaEmerf = $salidaEmer;
							$salidaEmer = $tiempo->aminutos($salidaEmer);
						}

						if($ealmu>$salida1)
						{
							$entradaEmerf = $ealmuf;
							$entradaEmer = $ealmu;
							$salidaEmerf = $salidaf;
							$salidaEmer = $salida;

							$salidaf = $salmuf;
							$salida = $salmu;

							$ealmuf = "";
							$ealmu = "";
							$salmuf = "";
							$salmu = "";
						}
						
						if(($entrada1-$entrada)>=900000)
						{

							if(($salmu>0)&&($entradaEmer>0))
							{
								$xf = $entradaEmerf;
								$x = $entradaEmer;
								$yf = $salidaEmerf;
								$y = $salidaEmer;								


								$entradaEmerf = $entradaf;
								$entradaEmer = $entrada;
								$salidaEmerf = $salmuf;
								$salidaEmer = $salmu;

								$entradaf = $ealmuf;
								$entrada = $ealmu;
								$salmuf = $salidaf;
								$salmu = $salida;

								$ealmuf = $xf;
								$ealmu = $x;
								$salidaf = $yf;
								$salida = $y;
							}
							elseif($salmu>0)
							{
								$xf = $entradaEmerf;
								$x = $entradaEmer;
								$yf = $salidaEmerf;
								$y = $salidaEmer;								


								$entradaEmerf = $entradaf;
								$entradaEmer = $entrada;
								$salidaEmerf = $salmuf;
								$salidaEmer = $salmu;

								$entradaf = $ealmuf;
								$entrada = $ealmu;
								$salmuf = "";
								$salmu = "";

								$ealmuf = "";
								$ealmu = "";
								
							}
							else
							{
								$entradaEmerf = $entradaf;
								$entradaEmer = $entrada;
								$salidaEmerf = $salidaf;
								$salidaEmer = $salida;

								$entradaf = "";
								$entrada = "";
								$salidaf = "";
								$salida = "";
							}
						}*/
						
						$query="INSERT INTO reloj_detalle (id, id_encabezado, ficha, fecha, entrada, salmuerzo, ealmuerzo, salida, ent_emer, sal_emer) values ('','$idenc',".$fichaaux.",'".$fechaaux."', '".$entradaf."', '".$salmuf."', '".$ealmuf."', '".$salidaf."', '".$entradaEmerf."', '".$salidaEmerf."')";
						/*echo "<br>";
						if($fichaaux==9 && $fechaaux=='2015-10-30')
						{
							echo $query;
							exit;
						}*/
						query($query,$conexion);
				  		//echo "<br>";
						//exit;

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
					
					/*if($fichaaux==9 && $fechaaux=='2015-10-30')
					{
						echo $horas;
						echo "<br>";
						echo $j;
						echo "<br>";
					}*/

					//$turno = $fila[turno_id];
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
				//exit;
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
						//$query_fixxx = "UPDATE reloj_detalle SET salida = entrada, entrada = '".$fetch_fix[ent_emer]."' WHERE id = '".($fetch_fix[id]+1)."' and ficha = '$fetch_fix[ficha]'";
						$query_fixxx = "UPDATE reloj_detalle SET entrada = '".$fetch_fix[ent_emer]."' WHERE id = '".($fetch_fix[id])."' and ficha = '$fetch_fix[ficha]'";
						$result_fixxx = query($query_fixxx,$conexion);
						$query_fixxx = "UPDATE reloj_detalle SET ent_emer = '' WHERE id = '".($fetch_fix[id])."' and ficha = '$fetch_fix[ficha]'";
						$result_fixxx = query($query_fixxx,$conexion);

						//$query_fixxx = "DELETE FROM  reloj_detalle  WHERE id = '".$fetch_fix[id]."'";
						//$result_fixxx = query($query_fixxx,$conexion);
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

						/*if(($fetch_fix[ficha]=='21')&&($fetch_fix[fecha]=="2015-07-21"))
						{
							echo $salida1." - ".substr($filaTur[salida],0,2)." - ".$entradamin2." - ".$fetch_fixx[entrada];
							exit;
						}*/

						if((($fetch_fixx[entrada]!='') &&  ($fetch_fixx[salida]=='') && ($fetch_fixx[ent_emer]=='') && ($fetch_fixx[sal_emer]=='')) && (($entradamin>=($entrada12x-60))&&($entradamin<=$entrada12x))&&($entrada12x>=1380))
						{
							//if(substr($fetch_fix[entrada],0,2)=='23')
								$entx=substr($filaTur2[entrada], 0,5);
							//else
							//	$entx=$fetch_fix[entrada];
							
							$query_fixxx = "UPDATE reloj_detalle SET salida = entrada, entrada = '".$entx."' WHERE id = '".$fetch_fixx[id]."'";
							$result_fixxx = query($query_fixxx,$conexion);

							$query_fixxx = "delete from reloj_detalle  WHERE id = '".$fetch_fix[id]."'";
							$result_fixxx = query($query_fixxx,$conexion);
							$xxx=1;
						}
						elseif((($fetch_fixx[entrada]=='') &&  ($fetch_fixx[salida]!='') && ($fetch_fixx[ent_emer]=='') && ($fetch_fixx[sal_emer]=='')) && (($entradamin>=($entrada12x-60))&&($entradamin<=$entrada12x))&&($entrada12x>=1380))
						{
							//if(substr($fetch_fix[entrada],0,2)=='23')
								$entx=substr($filaTur2[entrada], 0,5);//$entx="00:00";
							//else
							//	$entx=$fetch_fix[entrada];
							$query_fixxx = "UPDATE reloj_detalle SET  entrada = '".$entx."' WHERE id = '".$fetch_fixx[id]."'";
							$result_fixxx = query($query_fixxx,$conexion);
							
							//$query_fixxx = "UPDATE reloj_detalle SET ent_emer = ''  WHERE id = '".$fetch_fix[id]."'";
							//$result_fixxx = query($query_fixxx,$conexion);
							$query_fixxx = "delete from reloj_detalle  WHERE id = '".$fetch_fix[id]."'";
							$result_fixxx = query($query_fixxx,$conexion);
							$xxx=1;
						}
						elseif((($fetch_fixx[entrada]!='') &&  ($fetch_fixx[salida]=='') && ($fetch_fixx[ent_emer]=='') && ($fetch_fixx[sal_emer]=='')) && (($entradamin>=($entrada1-60))&&($entradamin<=($entrada1+120)))&&((($entradamin2+1440)>=$salida1)&&(($entradamin2+1440)<=($salida1+120))))
						{
							//if(substr($fetch_fix[entrada],0,2)=='23')
								$entx=substr($filaTur[salida],0,5);
							//else
							//	$entx=$fetch_fix[entrada];
							
							
							//$query_fixxx = "UPDATE reloj_detalle SET ent_emer = ''  WHERE id = '".$fetch_fix[id]."'";
							//$result_fixxx = query($query_fixxx,$conexion);
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
							//if(substr($fetch_fix[entrada],0,2)=='23')
								$entx=substr($filaTur[salida],0,5);
							//else
							//	$entx=$fetch_fix[entrada];
							
							
							//$query_fixxx = "UPDATE reloj_detalle SET ent_emer = ''  WHERE id = '".$fetch_fix[id]."'";
							//$result_fixxx = query($query_fixxx,$conexion);
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
							//if(substr($fetch_fix[entrada],0,2)=='23')
								$entx=substr($filaTur[salida],0,5);
							//else
							//	$entx=$fetch_fix[entrada];
							
							$query_fixxx = "UPDATE reloj_detalle SET  ent_emer = salida, salida = entrada, entrada = '".$fetch_fix[entrada]."' WHERE id = '".$fetch_fixx[id]."'";
							$result_fixxx = query($query_fixxx,$conexion);
							//$query_fixxx = "UPDATE reloj_detalle SET ent_emer = ''  WHERE id = '".$fetch_fix[id]."'";
							//$result_fixxx = query($query_fixxx,$conexion);
							//$query_fixxx = "UPDATE reloj_detalle SET  entrada = '' WHERE id = '".$fetch_fix[id]."'";
							//$result_fixxx = query($query_fixxx,$conexion);

							$query_fixxx = "DELETE FROM reloj_detalle  WHERE id = '".$fetch_fix[id]."'";
							$result_fixxx = query($query_fixxx,$conexion);
							
							
							$xxx=1;
						}



						/*elseif(($fetch_fixx[entrada]!='') &&  ($fetch_fixx[salida]!='') && ($fetch_fixx[ent_emer]!='') && ($fetch_fixx[sal_emer]==''))
						{
							if(substr($fetch_fix[ent_emer],0,2)=='23')
								$entx="00:00";
							else
								$entx=$fetch_fix[ent_emer];
							$query_fixxx = "UPDATE reloj_detalle SET sal_emer = ent_emer, ent_emer = salida, salida = entrada, entrada = '".$entx."' WHERE id = '".$fetch_fixx[id]."'";
							$result_fixxx = query($query_fixxx,$conexion);
							$query_fixxx = "UPDATE reloj_detalle SET ent_emer = ''  WHERE id = '".$fetch_fix[id]."'";
							$result_fixxx = query($query_fixxx,$conexion);
							$xxx=1;
						}
						elseif(($fetch_fixx[entrada]!='') && ($fetch_fixx[salmuerzo]=='') && ($fetch_fixx[ealmuerzo]=='') &&  ($fetch_fixx[salida]!='') && ($fetch_fixx[ent_emer]=='') && ($fetch_fixx[sal_emer]=='')&&(substr($fetch_fix[entrada],0,2)=='23'))
						{
							
							$entx="00:00";
						
						
							$query_fixxx = "UPDATE reloj_detalle SET  ent_emer = salida, salida = entrada, entrada = '".$entx."' WHERE id = '".$fetch_fixx[id]."'";
							$result_fixxx = query($query_fixxx,$conexion);
							$query_fixxx = "DELETE FROM reloj_detalle  WHERE id = '".$fetch_fix[id]."'";
							$result_fixxx = query($query_fixxx,$conexion);
							$xxx=1;
							$entx="";
							
						}
						elseif((($fetch_fixx[entrada]!='') &&  ($fetch_fixx[salida]!=''))&&(((substr($fetch_fixx[entrada],0,2)=='00')&&(($salida1>=1440)&&($salida1<1500)))||((substr($fetch_fixx[entrada],0,2)=='01')&&(($salida1>0)&&($salida1<120)))))
						{
							
								
							$salx="00:00";

							$query_fixxx = "UPDATE reloj_detalle SET salida_diasiguiente = '".$fetch_fixx[entrada]."' WHERE id = '".$fetch_fix[id]."'";
							$result_fixxx = query($query_fixxx,$conexion);

							$query_fixxx = "UPDATE reloj_detalle SET salida = '".$salx."' WHERE id = '".$fetch_fix[id]."'";
							$result_fixxx = query($query_fixxx,$conexion);

							$query_fixxx = "UPDATE reloj_detalle SET entrada=salida WHERE id = '".$fetch_fixx[id]."'";
							$result_fixxx = query($query_fixxx,$conexion);

							$query_fixxx = "UPDATE reloj_detalle SET salida='' WHERE id = '".$fetch_fixx[id]."'";
							$result_fixxx = query($query_fixxx,$conexion);
							$xxx=1;
							
						}
						/*elseif(((substr($fetch_fixx[entrada],0,2)=='00')||(substr($fetch_fixx[entrada],0,2)=='01')||(substr($fetch_fixx[entrada],0,2)=='02'))&&(($salida1>=1320)&&($salida1<1499)))
						{
							$entx="00:00";
						
							$query_fixxx = "UPDATE reloj_detalle SET salida = '$entx' WHERE id = '".$fetch_fix[id]."'";
							$result_fixxx = query($query_fixxx,$conexion);
							
							$query_fixxx = "UPDATE reloj_detalle SET entrada = '' WHERE id = '".$fetch_fixx[id]."'";
							$result_fixxx = query($query_fixxx,$conexion);
							$xxx=1;
						}*/
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
							//$query_fixxx = "UPDATE reloj_detalle SET ent_emer = ''  WHERE id = '".$fetch_fix[id]."'";
							//$result_fixxx = query($query_fixxx,$conexion);
							$query_fixxx = "UPDATE reloj_detalle SET  ent_emer = '' WHERE id = '".$fetch_fix[id]."'";
							$result_fixxx = query($query_fixxx,$conexion);

							//$query_fixxx = "DELETE FROM reloj_detalle  WHERE id = '".$fetch_fix[id]."'";
							//$result_fixxx = query($query_fixxx,$conexion);
							
							
							$xxx=1;
						}

					}
					/*elseif(($fetch_fix[entrada]!='') &&  ($fetch_fix[salida]!='') && ($fetch_fix[ent_emer]!='') && ($fetch_fix[sal_emer]==''))
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
							$query_fixxx = "UPDATE reloj_detalle SET salida = entrada, entrada = '".$entx."' WHERE id = '".$fetch_fixx[id]."'";
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
						$query_fixxx = "UPDATE reloj_detalle SET salida = entrada, entrada = '".$fetch_fix[ent_emer]."' WHERE id = '".($fetch_fix[id]+1)."'";
						$result_fixxx = query($query_fixxx,$conexion);

						$query_fixxx = "DELETE FROM  reloj_detalle  WHERE id = '".$fetch_fix[id]."'";
						$result_fixxx = query($query_fixxx,$conexion);
						$xxx=1;
					}
					/*elseif(($fetch_fix[entrada]!='') && ($fetch_fix[salmuerzo]=='') && ($fetch_fix[ealmuerzo]=='') && ($fetch_fix[salida]=='') && ($fetch_fix[ent_emer]=='') && ($fetch_fix[sal_emer]==''))
					{
						$query_fixx = "SELECT * FROM reloj_detalle WHERE id = '".($fetch_fix['id']+1)."' and ficha = '$fetch_fix[ficha]'";
						$result_fixx = query($query_fixx,$conexion);
						$fetch_fixx=fetch_array($result_fixx);
						echo $salida1;
						echo "<br>";
						if($fetch_fix[ficha]=='21')
							exit;
						
					}*/
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
					//$entrada0 = $tiempo->aminutos(substr($fila[tolerancia_llegada],0,2)=="00" ? "24".substr($fila[tolerancia_llegada], 2,5) : substr($fila[tolerancia_llegada], 0,5));
					$entrada0 = $tiempo->aminutos(substr($fila[tolerancia_llegada], 0,5));
					//$entrada1 = $tiempo->aminutos(substr($fila[entrada],0,2)=="00" ? "24".substr($fila[entrada], 2,5) : substr($fila[entrada], 0,5));
					$entrada1 = $tiempo->aminutos(substr($fila[entrada], 0,5));
					//$entradatol = $tiempo->aminutos(substr($fila[tolerancia_entrada],0,2)=="00" ? "24".substr($fila[tolerancia_entrada], 2,5) : substr($fila[tolerancia_entrada], 0,5));
					$entradatol = $tiempo->aminutos(substr($fila[tolerancia_entrada], 0,5));

					$saldesc1 = $tiempo->aminutos(substr($fila[inicio_descanso],0,2)=="00" ? "24".substr($fila[inicio_descanso], 2,3) : substr($fila[inicio_descanso], 0,5));
					//$entdesc1 = new DateTime((substr($fila[salida_descanso],0,2)=="00" ? "24".substr($fila[salida_descanso], 2,7) : $fila[salida_descanso]));
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

						/*if($regular==$limite)
						{
							$extrah1 = $saldesc1->diff($entdesc1);
						}*/
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
						//$acumExtras += $extra + $extraNoc + $extrah + $extraMixDiurna + $extraMixNoc;
						if($acumExtras+$extra>540)
						{
							$extraAux = $extra;
							$extra = 540 - $acumExtras;
							$extraExt += ($acumExtras + $extraAux) - 540;
							//$acumExtras += $extraExt;
							$acumExtras += $extraAux;
						}

						if($acumExtras+$extraNoc>540)
						{
							$extraNocAux = $extraNoc;
							$extraNoc = 540 - $acumExtras;
							$extraExtNoc += ($acumExtras + $extraNocAux) - 540;
							//$acumExtras += $extraExtNoc;
							$acumExtras += $extraNocAux;
						}

						if($acumExtras+$extraMixDiurna>540)
						{
							$extraMixDiurnaAux = $extraMixDiurna;
							$extraMixDiurna = 540 - $acumExtras;
							$extraExtMixDiurna += ($acumExtras + $extraMixDiurnaAux) - 540;
							//$acumExtras += $extraExtMixDiurna;
							$acumExtras += $extraMixDiurnaAux;
						}

						if($acumExtras+$extraMixNoc>540)
						{
							$extraMixNocAux = $extraMixNoc;
							$extraMixNoc = 540 - $acumExtras;
							$extraExtMixNoc += ($acumExtras + $extraMixNocAux) - 540;
							//$acumExtras += $extraExtMixNoc;
							$acumExtras += $extraMixNocAux;
						}
					}
					elseif($acumExtras>=540)
					{
						//$acumExtras += $extra + $extraNoc + $extrah + $extraMixDiurna + $extraMixNoc;
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
						//$acumExtras += $extra + $extraext + $extraNoc + $extraExtNoc + $extrah + $extraMixDiurna + $extraExtMixDiurna + $extraMixNoc + $extraExtMixNoc;
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

			break;

		}
		fclose($f);
		

		if(isset($_POST["cod_enca"])){
			print '<script type="text/javascript">
				window.onload=function(){
					window.opener.document.control_acceso_detalle2.submit();
					window.close();
				}
			</script>';
			exit;
		}
		else
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
    else 
    {
        header("Location:control_acceso2.php");
    }
}
/*****************************************************************************************************************/
/*****************************************************************************************************************/
/*****************************************************************************************************************/
/*****************************************************************************************************************/
/*****************************************************************************************************************/
/*****************************************************************************************************************/
/*****************************************************************************************************************/
/*****************************************************************************************************************/
/*****************************************************************************************************************/
/*****************************************************************************************************************/
/*****************************************************************************************************************/
/*****************************************************************************************************************/
/*****************************************************************************************************************/
/*****************************************************************************************************************/
/*****************************************************************************************************************/
/*****************************************************************************************************************/
/*****************************************************************************************************************/
/*****************************************************************************************************************/
/*****************************************************************************************************************/
/*****************************************************************************************************************/
/***************************************                                   ***************************************/
/***************************************                                   ***************************************/
/***************************************                                   ***************************************/
/***************************************     LECTURA    DESDE    EXCEL     ***************************************/
/***************************************                                   ***************************************/
/***************************************                                   ***************************************/
/***************************************                                   ***************************************/
/*****************************************************************************************************************/
/*****************************************************************************************************************/
/*****************************************************************************************************************/
/*****************************************************************************************************************/
/*****************************************************************************************************************/
/*****************************************************************************************************************/
/*****************************************************************************************************************/
/*****************************************************************************************************************/
/*****************************************************************************************************************/
/*****************************************************************************************************************/
/*****************************************************************************************************************/
/*****************************************************************************************************************/
/*****************************************************************************************************************/
/*****************************************************************************************************************/
/*****************************************************************************************************************/
/*****************************************************************************************************************/
/*****************************************************************************************************************/
/*****************************************************************************************************************/
/*****************************************************************************************************************/
/*****************************************************************************************************************/
if($_POST["procesar"] and $empresa == 'excel')
{
	$fechaInicio = new DateTime($_POST['txtFechaInicio']);
	$fechaFin    = new DateTime($_POST['txtFechaFin']);

	$conexion2   = new bd($_SESSION['bd']);	
	$fechaI      = $fechaInicio->format('Y-m-d');
	$fechaF      = $fechaFin->format('Y-m-d');	
	$insert      = "INSERT INTO reloj_encabezado
      VALUES('', '" . date("Y-m-d") . "' ,'{$fechaInicio->format('Y-m-d')}', '{$fechaFin->format('Y-m-d')}','Pendiente','".$_SESSION['usuario']."','','','','')";
	$result      = $conexion2->query($insert);
	//echo $insert,"<br>";
	
	$conexion2   = new bd($_SESSION['bd']);
	$select      = "SELECT MAX(cod_enca) as cod FROM reloj_encabezado";
	$row         = $conexion2->query($select)->fetch_assoc();
	$idenc       = $row['cod'];

	$conexion2   = new bd($_SESSION['bd']);	
	$truncar     ="TRUNCATE TABLE reloj_datos;";
	$res2        = $conexion2->query($truncar);

	if($_FILES['archivo']['name'] != '')
	{
		$name	  = $_FILES['archivo']['name'];
		$tname 	  = $_FILES['archivo']['tmp_name'];
		$type 	  = $_FILES['archivo']['type'];

		if($type == 'application/vnd.ms-excel')
			$ext = 'xls'; // Extension excel 97
		else if($type == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
		{
			$ext = 'xlsx'; // Extension excel 2007 y 2010
		}
		else
		{
			if (function_exists('finfo_file')) 
			{
				$type  = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $tname);

				if($type == 'application/vnd.ms-excel'){
					$ext = 'xls'; // Extension excel 97
				}else if($type == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'){
					$ext = 'xlsx'; // Extension excel 2007 y 2010		
				}
			}
			if(!isset($ext))
			{
				$mensaje = "¡Error! Extensión de archivo inválida.";
				$success = false;
			}
		}

		if(isset($ext))
		{
			$xls         = 'Excel5';
			$xlsx        = 'Excel2007';
			// Creando el lector
			$objReader   = PHPExcel_IOFactory::createReader($$ext);
			// Cargamos el archivo
			$objPHPExcel = $objReader->load($tname);
			$objPHPExcel->setActiveSheetIndex(0);
			$mensaje     ="";
			$i           =2;
			$editadas    = 0;
			$nuevas      = 0;
			$total       = 0;
			$filas       = "";
			while($objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue() != '')
			{

				$cell = $objPHPExcel->getActiveSheet()->getCell('B' . $i);
				$InvDate= $cell->getValue();
				if(PHPExcel_Shared_Date::isDateTime($cell)) {
				     $fecha = date("Y-m-d", PHPExcel_Shared_Date::ExcelToPHP($InvDate)); 
				}
				$celda = $objPHPExcel->getActiveSheet()->getCell('C' . $i);
				$InvDate= $celda->getValue();
				if(PHPExcel_Shared_Date::isDateTime($celda)) {
				     $HoraInicio = date("H:i:s", PHPExcel_Shared_Date::ExcelToPHP($InvDate)); 
				     //$HoraInicio = date("H:i:s ",strtotime('-2 hours', strtotime ( $HoraInicio ))); 

				}
				$celda2 = $objPHPExcel->getActiveSheet()->getCell('D' . $i);
				$InvDate= $celda2->getValue();
				if(PHPExcel_Shared_Date::isDateTime($celda2)) {
				     $HoraFin = date("H:i:s", PHPExcel_Shared_Date::ExcelToPHP($InvDate)); 
				    // $HoraFin = date("H:i:s ",strtotime('-2 hours', strtotime ( $HoraFin ))); 
				}
				
				$funciones     = $objPHPExcel->getActiveSheet()->getCell("C".$i)->getCalculatedValue();
				$registros = array(
						'ficha'       => $objPHPExcel->getActiveSheet()->getCell("A".$i)->getCalculatedValue(),
						'fecha'       => $fecha,
						'hora_inicio' => $HoraInicio,
						'hora_fin'    => $HoraFin
					);
				$i++;
				//=====================================================================================================
				## Registro del empleado


				//$resultado = query($consulta,$conexion);
				$tiempo = new tiempo();
				$conexion2    = new bd($_SESSION['bd']);
				$h_inicio1  = $tiempo->aminutos('08:00');
				$h_inicio2  = $tiempo->aminutos(substr($registros['hora_inicio'], 0,5));
				if ($h_inicio2 < $h_inicio1) {
					$sql2="INSERT INTO reloj_datos (id, ficha, fecha, hora) VALUES ('', '{$registros['ficha']}','{$registros['fecha']}','{$registros['hora_inicio']}');";
				}
				else
				{
					$sql2="INSERT INTO reloj_datos (id, ficha, fecha, hora) VALUES ('', '{$registros['ficha']}','{$registros['fecha']}','08:00:00');";
				}
				
				//print_r($registros);echo "<br>";
				$res2 = $conexion2->query($sql2);
				$conexion2    = new bd($_SESSION['bd']);

				$sql1="INSERT INTO reloj_datos (id, ficha, fecha, hora) VALUES ('', '{$registros['ficha']}','{$registros['fecha']}','{$registros['hora_fin']}');";
				//print_r($registros);echo "<br>";
				$res = $conexion2->query($sql1);
				//echo $sql1,"<br>";
				
			}
			$conexion=conexion();

			$consulta="SELECT * FROM reloj_datos WHERE fecha between '$fechaI' and '$fechaF' ORDER BY ficha asc, fecha asc, hora asc;";
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
					//if($horasx[0]=="00")
					//	$horas="24:".$horasx[1].":".$horasx[2];
					$horas=$horasx[0].":".$horasx[1];
					$dates = trim($fechas." ".$horas);	

					//AQUI EMPIEZA				

					$date = new DateTime($dates);
					$fecha = $date->format('Y-m-d');
					//echo "<br>";
					//$ficha = $fila[ficha];
					 //exit;
					if($i==0)
					{
						$fichaaux=$ficha;
						$fechaaux=$fecha;
						//$apenomaux=$fila[apenom];
					}
					$i = 1;
					
					$query="select tur.* from nomturnos tur join nomcalendarios_personal per on tur.turno_id=per.turno_id where per.ficha='$fichaaux' and per.fecha='$fechaaux'";
					$result=query($query,$conexion);
					$filax = fetch_array($result);
					$nocturno = $filax[nocturno];
					if($nocturno == "")
						$nocturno = 0;
					
					//echo $fecha." ".$fechaaux." ".$horas." ".$nocturno." ".$ficha." ".$fichaaux." ".$j;
					//echo "<br>";
					if((($fecha!=$fechaaux)&&($nocturno==0))||(($ficha!=$fichaaux)&&($nocturno==0))||(($nocturno==1)&&($j>=2)))
					{
						$query="select tur.* from nomturnos tur join nomcalendarios_personal per on tur.turno_id=per.turno_id where per.ficha='$fichaaux' and per.fecha='$fechaaux'";
						$result=query($query,$conexion);
						$fila = fetch_array($result);
						$turno = trim($fila[descripcion]);
						$libre = $fila[libre];
						$tipo = $fila[tipo];
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



						/*if($entradaEmer!="")
						{
							$entradaEmerf = $entradaEmer;
							$entradaEmer = $tiempo->aminutos($entradaEmer);
						}
						if($salidaEmer!="")
						{
							$salidaEmerf = $salidaEmer;
							$salidaEmer = $tiempo->aminutos($salidaEmer);
						}

						if($ealmu>$salida1)
						{
							$entradaEmerf = $ealmuf;
							$entradaEmer = $ealmu;
							$salidaEmerf = $salidaf;
							$salidaEmer = $salida;

							$salidaf = $salmuf;
							$salida = $salmu;

							$ealmuf = "";
							$ealmu = "";
							$salmuf = "";
							$salmu = "";
						}
						
						if(($entrada1-$entrada)>=900000)
						{

							if(($salmu>0)&&($entradaEmer>0))
							{
								$xf = $entradaEmerf;
								$x = $entradaEmer;
								$yf = $salidaEmerf;
								$y = $salidaEmer;								


								$entradaEmerf = $entradaf;
								$entradaEmer = $entrada;
								$salidaEmerf = $salmuf;
								$salidaEmer = $salmu;

								$entradaf = $ealmuf;
								$entrada = $ealmu;
								$salmuf = $salidaf;
								$salmu = $salida;

								$ealmuf = $xf;
								$ealmu = $x;
								$salidaf = $yf;
								$salida = $y;
							}
							elseif($salmu>0)
							{
								$xf = $entradaEmerf;
								$x = $entradaEmer;
								$yf = $salidaEmerf;
								$y = $salidaEmer;								


								$entradaEmerf = $entradaf;
								$entradaEmer = $entrada;
								$salidaEmerf = $salmuf;
								$salidaEmer = $salmu;

								$entradaf = $ealmuf;
								$entrada = $ealmu;
								$salmuf = "";
								$salmu = "";

								$ealmuf = "";
								$ealmu = "";
								
							}
							else
							{
								$entradaEmerf = $entradaf;
								$entradaEmer = $entrada;
								$salidaEmerf = $salidaf;
								$salidaEmer = $salida;

								$entradaf = "";
								$entrada = "";
								$salidaf = "";
								$salida = "";
							}
						}*/
						
						$query="INSERT INTO reloj_detalle (id, id_encabezado, ficha, fecha, entrada, salmuerzo, ealmuerzo, salida, ent_emer, sal_emer) values ('','$idenc',".$fichaaux.",'".$fechaaux."', '".$entradaf."', '".$salmuf."', '".$ealmuf."', '".$salidaf."', '".$entradaEmerf."', '".$salidaEmerf."')";
						/*echo "<br>";
						if($fichaaux==9 && $fechaaux=='2015-10-30')
						{
							echo $query;
							exit;
						}*/
						query($query,$conexion);
				  		//echo "<br>";
						//exit;

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
					
					/*if($fichaaux==9 && $fechaaux=='2015-10-30')
					{
						echo $horas;
						echo "<br>";
						echo $j;
						echo "<br>";
					}*/

					//$turno = $fila[turno_id];
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
				//exit;
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
						//$query_fixxx = "UPDATE reloj_detalle SET salida = entrada, entrada = '".$fetch_fix[ent_emer]."' WHERE id = '".($fetch_fix[id]+1)."' and ficha = '$fetch_fix[ficha]'";
						$query_fixxx = "UPDATE reloj_detalle SET entrada = '".$fetch_fix[ent_emer]."' WHERE id = '".($fetch_fix[id])."' and ficha = '$fetch_fix[ficha]'";
						$result_fixxx = query($query_fixxx,$conexion);
						$query_fixxx = "UPDATE reloj_detalle SET ent_emer = '' WHERE id = '".($fetch_fix[id])."' and ficha = '$fetch_fix[ficha]'";
						$result_fixxx = query($query_fixxx,$conexion);

						//$query_fixxx = "DELETE FROM  reloj_detalle  WHERE id = '".$fetch_fix[id]."'";
						//$result_fixxx = query($query_fixxx,$conexion);
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

						/*if(($fetch_fix[ficha]=='21')&&($fetch_fix[fecha]=="2015-07-21"))
						{
							echo $salida1." - ".substr($filaTur[salida],0,2)." - ".$entradamin2." - ".$fetch_fixx[entrada];
							exit;
						}*/

						if((($fetch_fixx[entrada]!='') &&  ($fetch_fixx[salida]=='') && ($fetch_fixx[ent_emer]=='') && ($fetch_fixx[sal_emer]=='')) && (($entradamin>=($entrada12x-60))&&($entradamin<=$entrada12x))&&($entrada12x>=1380))
						{
							//if(substr($fetch_fix[entrada],0,2)=='23')
								$entx=substr($filaTur2[entrada], 0,5);
							//else
							//	$entx=$fetch_fix[entrada];
							
							$query_fixxx = "UPDATE reloj_detalle SET salida = entrada, entrada = '".$entx."' WHERE id = '".$fetch_fixx[id]."'";
							$result_fixxx = query($query_fixxx,$conexion);

							$query_fixxx = "delete from reloj_detalle  WHERE id = '".$fetch_fix[id]."'";
							$result_fixxx = query($query_fixxx,$conexion);
							$xxx=1;
						}
						elseif((($fetch_fixx[entrada]=='') &&  ($fetch_fixx[salida]!='') && ($fetch_fixx[ent_emer]=='') && ($fetch_fixx[sal_emer]=='')) && (($entradamin>=($entrada12x-60))&&($entradamin<=$entrada12x))&&($entrada12x>=1380))
						{
							//if(substr($fetch_fix[entrada],0,2)=='23')
								$entx=substr($filaTur2[entrada], 0,5);//$entx="00:00";
							//else
							//	$entx=$fetch_fix[entrada];
							$query_fixxx = "UPDATE reloj_detalle SET  entrada = '".$entx."' WHERE id = '".$fetch_fixx[id]."'";
							$result_fixxx = query($query_fixxx,$conexion);
							
							//$query_fixxx = "UPDATE reloj_detalle SET ent_emer = ''  WHERE id = '".$fetch_fix[id]."'";
							//$result_fixxx = query($query_fixxx,$conexion);
							$query_fixxx = "delete from reloj_detalle  WHERE id = '".$fetch_fix[id]."'";
							$result_fixxx = query($query_fixxx,$conexion);
							$xxx=1;
						}
						elseif((($fetch_fixx[entrada]!='') &&  ($fetch_fixx[salida]=='') && ($fetch_fixx[ent_emer]=='') && ($fetch_fixx[sal_emer]=='')) && (($entradamin>=($entrada1-60))&&($entradamin<=($entrada1+120)))&&((($entradamin2+1440)>=$salida1)&&(($entradamin2+1440)<=($salida1+120))))
						{
							//if(substr($fetch_fix[entrada],0,2)=='23')
								$entx=substr($filaTur[salida],0,5);
							//else
							//	$entx=$fetch_fix[entrada];
							
							
							//$query_fixxx = "UPDATE reloj_detalle SET ent_emer = ''  WHERE id = '".$fetch_fix[id]."'";
							//$result_fixxx = query($query_fixxx,$conexion);
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
							//if(substr($fetch_fix[entrada],0,2)=='23')
								$entx=substr($filaTur[salida],0,5);
							//else
							//	$entx=$fetch_fix[entrada];
							
							
							//$query_fixxx = "UPDATE reloj_detalle SET ent_emer = ''  WHERE id = '".$fetch_fix[id]."'";
							//$result_fixxx = query($query_fixxx,$conexion);
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
							//if(substr($fetch_fix[entrada],0,2)=='23')
								$entx=substr($filaTur[salida],0,5);
							//else
							//	$entx=$fetch_fix[entrada];
							
							$query_fixxx = "UPDATE reloj_detalle SET  ent_emer = salida, salida = entrada, entrada = '".$fetch_fix[entrada]."' WHERE id = '".$fetch_fixx[id]."'";
							$result_fixxx = query($query_fixxx,$conexion);
							//$query_fixxx = "UPDATE reloj_detalle SET ent_emer = ''  WHERE id = '".$fetch_fix[id]."'";
							//$result_fixxx = query($query_fixxx,$conexion);
							//$query_fixxx = "UPDATE reloj_detalle SET  entrada = '' WHERE id = '".$fetch_fix[id]."'";
							//$result_fixxx = query($query_fixxx,$conexion);

							$query_fixxx = "DELETE FROM reloj_detalle  WHERE id = '".$fetch_fix[id]."'";
							$result_fixxx = query($query_fixxx,$conexion);
							
							
							$xxx=1;
						}



						/*elseif(($fetch_fixx[entrada]!='') &&  ($fetch_fixx[salida]!='') && ($fetch_fixx[ent_emer]!='') && ($fetch_fixx[sal_emer]==''))
						{
							if(substr($fetch_fix[ent_emer],0,2)=='23')
								$entx="00:00";
							else
								$entx=$fetch_fix[ent_emer];
							$query_fixxx = "UPDATE reloj_detalle SET sal_emer = ent_emer, ent_emer = salida, salida = entrada, entrada = '".$entx."' WHERE id = '".$fetch_fixx[id]."'";
							$result_fixxx = query($query_fixxx,$conexion);
							$query_fixxx = "UPDATE reloj_detalle SET ent_emer = ''  WHERE id = '".$fetch_fix[id]."'";
							$result_fixxx = query($query_fixxx,$conexion);
							$xxx=1;
						}
						elseif(($fetch_fixx[entrada]!='') && ($fetch_fixx[salmuerzo]=='') && ($fetch_fixx[ealmuerzo]=='') &&  ($fetch_fixx[salida]!='') && ($fetch_fixx[ent_emer]=='') && ($fetch_fixx[sal_emer]=='')&&(substr($fetch_fix[entrada],0,2)=='23'))
						{
							
							$entx="00:00";
						
						
							$query_fixxx = "UPDATE reloj_detalle SET  ent_emer = salida, salida = entrada, entrada = '".$entx."' WHERE id = '".$fetch_fixx[id]."'";
							$result_fixxx = query($query_fixxx,$conexion);
							$query_fixxx = "DELETE FROM reloj_detalle  WHERE id = '".$fetch_fix[id]."'";
							$result_fixxx = query($query_fixxx,$conexion);
							$xxx=1;
							$entx="";
							
						}
						elseif((($fetch_fixx[entrada]!='') &&  ($fetch_fixx[salida]!=''))&&(((substr($fetch_fixx[entrada],0,2)=='00')&&(($salida1>=1440)&&($salida1<1500)))||((substr($fetch_fixx[entrada],0,2)=='01')&&(($salida1>0)&&($salida1<120)))))
						{
							
								
							$salx="00:00";

							$query_fixxx = "UPDATE reloj_detalle SET salida_diasiguiente = '".$fetch_fixx[entrada]."' WHERE id = '".$fetch_fix[id]."'";
							$result_fixxx = query($query_fixxx,$conexion);

							$query_fixxx = "UPDATE reloj_detalle SET salida = '".$salx."' WHERE id = '".$fetch_fix[id]."'";
							$result_fixxx = query($query_fixxx,$conexion);

							$query_fixxx = "UPDATE reloj_detalle SET entrada=salida WHERE id = '".$fetch_fixx[id]."'";
							$result_fixxx = query($query_fixxx,$conexion);

							$query_fixxx = "UPDATE reloj_detalle SET salida='' WHERE id = '".$fetch_fixx[id]."'";
							$result_fixxx = query($query_fixxx,$conexion);
							$xxx=1;
							
						}
						/*elseif(((substr($fetch_fixx[entrada],0,2)=='00')||(substr($fetch_fixx[entrada],0,2)=='01')||(substr($fetch_fixx[entrada],0,2)=='02'))&&(($salida1>=1320)&&($salida1<1499)))
						{
							$entx="00:00";
						
							$query_fixxx = "UPDATE reloj_detalle SET salida = '$entx' WHERE id = '".$fetch_fix[id]."'";
							$result_fixxx = query($query_fixxx,$conexion);
							
							$query_fixxx = "UPDATE reloj_detalle SET entrada = '' WHERE id = '".$fetch_fixx[id]."'";
							$result_fixxx = query($query_fixxx,$conexion);
							$xxx=1;
						}*/
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
							//$query_fixxx = "UPDATE reloj_detalle SET ent_emer = ''  WHERE id = '".$fetch_fix[id]."'";
							//$result_fixxx = query($query_fixxx,$conexion);
							$query_fixxx = "UPDATE reloj_detalle SET  ent_emer = '' WHERE id = '".$fetch_fix[id]."'";
							$result_fixxx = query($query_fixxx,$conexion);

							//$query_fixxx = "DELETE FROM reloj_detalle  WHERE id = '".$fetch_fix[id]."'";
							//$result_fixxx = query($query_fixxx,$conexion);
							
							
							$xxx=1;
						}

					}
					/*elseif(($fetch_fix[entrada]!='') &&  ($fetch_fix[salida]!='') && ($fetch_fix[ent_emer]!='') && ($fetch_fix[sal_emer]==''))
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
							$query_fixxx = "UPDATE reloj_detalle SET salida = entrada, entrada = '".$entx."' WHERE id = '".$fetch_fixx[id]."'";
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
						$query_fixxx = "UPDATE reloj_detalle SET salida = entrada, entrada = '".$fetch_fix[ent_emer]."' WHERE id = '".($fetch_fix[id]+1)."'";
						$result_fixxx = query($query_fixxx,$conexion);

						$query_fixxx = "DELETE FROM  reloj_detalle  WHERE id = '".$fetch_fix[id]."'";
						$result_fixxx = query($query_fixxx,$conexion);
						$xxx=1;
					}
					/*elseif(($fetch_fix[entrada]!='') && ($fetch_fix[salmuerzo]=='') && ($fetch_fix[ealmuerzo]=='') && ($fetch_fix[salida]=='') && ($fetch_fix[ent_emer]=='') && ($fetch_fix[sal_emer]==''))
					{
						$query_fixx = "SELECT * FROM reloj_detalle WHERE id = '".($fetch_fix['id']+1)."' and ficha = '$fetch_fix[ficha]'";
						$result_fixx = query($query_fixx,$conexion);
						$fetch_fixx=fetch_array($result_fixx);
						echo $salida1;
						echo "<br>";
						if($fetch_fix[ficha]=='21')
							exit;
						
					}*/
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
					//$entrada0 = $tiempo->aminutos(substr($fila[tolerancia_llegada],0,2)=="00" ? "24".substr($fila[tolerancia_llegada], 2,5) : substr($fila[tolerancia_llegada], 0,5));
					$entrada0 = $tiempo->aminutos(substr($fila[tolerancia_llegada], 0,5));
					//$entrada1 = $tiempo->aminutos(substr($fila[entrada],0,2)=="00" ? "24".substr($fila[entrada], 2,5) : substr($fila[entrada], 0,5));
					$entrada1 = $tiempo->aminutos(substr($fila[entrada], 0,5));
					//$entradatol = $tiempo->aminutos(substr($fila[tolerancia_entrada],0,2)=="00" ? "24".substr($fila[tolerancia_entrada], 2,5) : substr($fila[tolerancia_entrada], 0,5));
					$entradatol = $tiempo->aminutos(substr($fila[tolerancia_entrada], 0,5));

					$saldesc1 = $tiempo->aminutos(substr($fila[inicio_descanso],0,2)=="00" ? "24".substr($fila[inicio_descanso], 2,3) : substr($fila[inicio_descanso], 0,5));
					//$entdesc1 = new DateTime((substr($fila[salida_descanso],0,2)=="00" ? "24".substr($fila[salida_descanso], 2,7) : $fila[salida_descanso]));
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

						/*if($regular==$limite)
						{
							$extrah1 = $saldesc1->diff($entdesc1);
						}*/
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
						$nacional=$regular+$extra;
						$regular="";
						$tardanza = "";
						$extra = "";
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
						//$acumExtras += $extra + $extraNoc + $extrah + $extraMixDiurna + $extraMixNoc;
						if($acumExtras+$extra>540)
						{
							$extraAux = $extra;
							$extra = 540 - $acumExtras;
							$extraExt += ($acumExtras + $extraAux) - 540;
							//$acumExtras += $extraExt;
							$acumExtras += $extraAux;
						}

						if($acumExtras+$extraNoc>540)
						{
							$extraNocAux = $extraNoc;
							$extraNoc = 540 - $acumExtras;
							$extraExtNoc += ($acumExtras + $extraNocAux) - 540;
							//$acumExtras += $extraExtNoc;
							$acumExtras += $extraNocAux;
						}

						if($acumExtras+$extraMixDiurna>540)
						{
							$extraMixDiurnaAux = $extraMixDiurna;
							$extraMixDiurna = 540 - $acumExtras;
							$extraExtMixDiurna += ($acumExtras + $extraMixDiurnaAux) - 540;
							//$acumExtras += $extraExtMixDiurna;
							$acumExtras += $extraMixDiurnaAux;
						}

						if($acumExtras+$extraMixNoc>540)
						{
							$extraMixNocAux = $extraMixNoc;
							$extraMixNoc = 540 - $acumExtras;
							$extraExtMixNoc += ($acumExtras + $extraMixNocAux) - 540;
							//$acumExtras += $extraExtMixNoc;
							$acumExtras += $extraMixNocAux;
						}
					}
					elseif($acumExtras>=540)
					{
						//$acumExtras += $extra + $extraNoc + $extrah + $extraMixDiurna + $extraMixNoc;
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
						//$acumExtras += $extra + $extraext + $extraNoc + $extraExtNoc + $extrah + $extraMixDiurna + $extraExtMixDiurna + $extraMixNoc + $extraExtMixNoc;
					}
					

					
					$query="UPDATE reloj_detalle SET ordinaria = '".$tiempo->ahoras($regular)."', extra = '".$tiempo->ahoras($extra)."', extraext = '".$tiempo->ahoras($extraExt)."', extranoc =  '".$tiempo->ahoras($extraNoc)."',  extraextnoc = '".$tiempo->ahoras($extraNocExt)."', domingo = '".$tiempo->ahoras($domingo)."', tardanza = '".$tiempo->ahoras($tardanza)."', nacional = '".$tiempo->ahoras($nacional)."', extranac = '".$tiempo->ahoras($extraNac)."',  extranocnac = '".$tiempo->ahoras($extraNocNac)."', descextra1 = '".$tiempo->ahoras($extrah)."', mixtodiurna = '".$tiempo->ahoras($extraMixDiurna)."', mixtoextdiurna = '".$tiempo->ahoras($extraExtMixDiurna)."', mixtonoc = '".$tiempo->ahoras($extraMixNoc)."', mixtoextnoc = '".$tiempo->ahoras($extraExtMixNoc)."', dialibre = '".$tiempo->ahoras($dialibre)."',  emergencia = '".$tiempo->ahoras($emergencia)."', descansoincompleto = '".$tiempo->ahoras($descansoincompleto)."' WHERE id = '".$fetchx[id]."' ";
					//echo $query,"<br>";
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
			$conexion2  = new bd($_SESSION['bd']);
			$query2     = "SELECT * from reloj_detalle WHERE id_encabezado  = '{$idenc}' ORDER by id";
			$res_det    = $conexion2 -> query($query2);
			$semana_aux = $acumSemana = $extraExtMixDiurna = $extra = $extraNoc = $ficha_aux = 0 ;

			while ($fila_det = $res_det->fetch_assoc()) 
			{
			//print_r($fila_det);

				$semana   = date("W",strtotime($fila_det['fecha']));
				$ficha_x = $fila_det['ficha'];
				if (($semana == $semana_sig) OR ($semana_sig != ($semana+1) ))
				{
					$acumSemana = 0;
					$extraExtMixDiurna = 0;
					$semana_sig = $acumSemana = $extraExtMixDiurna = $extra = $extraNoc = 0 ;
				}	
				
				//echo $semana," - ",$semana_sig," - ",$fila_det['fecha']," - ",$acumSemana," - ",$extraExtMixDiurna," - ",$extra," - ",$extraNoc," ",$ficha_x," ",$ficha_aux," ",$sw," 1<br>";
				$extra    = $tiempo->aminutos(substr($fila_det[extra], 0,5));
				$extraNoc = $tiempo->aminutos(substr($fila_det[extranoc], 0,5));
				if ($acumSemana <= 540) 
				{

					$acumSemana += $extra;

					if ($acumSemana>540) 
					{
						$extraExtMixDiurna += $extra;
						$extra ="";
					}
					$acumSemana += $extraNoc;

					if ($acumSemana>540) 
					{
						$extraExtMixDiurna += $extraNoc;
						$extraNoc ="";
					}

				}
				else
				{
					if ($acumSemana>540) 
					{
						$extraExtMixDiurna += $extra;
						$extra ="";
					}
					if ($acumSemana>540) 
					{
						$extraExtMixDiurna += $extraNoc;
						$extraNoc ="";
					}

					//echo $extraExtMixDiurna,"<br>";
				}
				# code...
				$conexion2  = new bd($_SESSION['bd']);
				$ficha_aux = $ficha_x;

				$semana_sig = $semana+1;
				$query="
				UPDATE reloj_detalle 
				SET  extra = '".$tiempo->ahoras($extra)."', 
				extranoc   = '".$tiempo->ahoras($extraNoc)."',  
				extraext   = '".$tiempo->ahoras($extraExtMixDiurna)."' 
				WHERE id   = '".$fila_det['id']."' ";
				//echo $query,'<br>';
				$conexion2 -> query($query);
			}
		}
	}
	else{
		$mensaje = "Error al cargar el archivo";
		$success = false;
	}
	if(isset($_POST["cod_enca"])){
		print '<script type="text/javascript">
			window.onload=function(){
				window.opener.document.control_acceso_detalle2.submit();
				window.close();
			}
		</script>';
		exit;
	}
	else
		header("Location:control_acceso2.php");

}
?>
<link href="../../includes/assets/plugins/bootstrap-datepicker/css/datepicker.css" rel="stylesheet" type="text/css" />
<script src="../../includes/assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
<form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post" name="frmAgregar" id="frmAgregar" enctype="multipart/form-data">
<div class="page-container">
	<div class="page-content-wrapper">
		<div class="page-content">
	        <div class="row">
	            <div class="col-md-12">
	                <div class="portlet box blue">
	                    <div class="portlet-title">
	                        <div class="caption">
	                            Parametros Control de Acceso
	                        </div>
	                    </div>
	                    <div class="portlet-body">
	                        


	                        <div class="row">
	                        <div class="col-md-2"></div> 
	                        <div class="col-md-8">
	                        <div class="panel panel-info"> 
	                            <div class="panel-body">
	                                <div class="row">
	                                    <div class="col-md-4">
	                                            Fecha de Inicio:  
	                                    </div>
	                                    <div class="col-md-5">
	                                        <div class="input-group date date-picker" data-provide="datepicker">  
	                                             <input name="txtFechaInicio" type="text"  class="form-control" placeholder="Inserte fecha" id="txtFechaInicio" value="" maxlength="10">
	                                             <span class="input-group-btn">
	                                                    <button class="btn default" type="button"><i class="fa fa-calendar"></i></button>
	                                             </span>
	                                        </div>
	                                    </div> 
	                                </div>
	                                <br>
	                                <div class="row">  
	                                    <div class="col-md-4">
	                                            Fecha de Fin:   
	                                    </div>
	                                    <div class="col-md-5">
	                                        <div class="input-group date date-picker" data-provide="datepicker">  
	                                             <input name="txtFechaFin" type="text"  class="form-control" placeholder="Inserte fecha" id="txtFechaFin" value="" maxlength="10">
	                                             <span class="input-group-btn">
	                                                    <button class="btn default" type="button"><i class="fa fa-calendar"></i></button>
	                                             </span>
	                                        </div>
	                                    </div> 
	                                </div>
	                                <br>
                                        <div class="row">
	                                    <div class="col-md-4">
	                                            Tipo Planilla:   
	                                    </div>
	                                    <div class="col-md-7">
	                                        <?php 
                                                        $select_planilla = "SELECT codtip, descrip FROM nomtipos_nomina WHERE 1";
                                                        $result_planilla = query($select_planilla,$conexion);
                                                        //$filaemp = fetch_array($result);
                                                ?>
                                                <select name="tipnom" id="tipnom" class="form-control select2">
                                                        <?php
//                                                                if($res->num_rows==0)
                                                                        echo "<option value=''>Seleccione una planilla</option>";

                                                                while($fila = fetch_array($result_planilla))
                                                                {
                                                                    echo "<option value='".$fila['codtip']."'>".$fila['descrip']."</option>";
                                                                }
                                                        ?>
                                                </select>
	                                    </div> 
	                                </div>
	                                <br>
	                                <div class="row">
	                                    <div class="col-md-4">
	                                            Formato:   
	                                    </div>
	                                    <div class="col-md-5">
	                                        <select id='archivo_tipo' name="archivo_tipo" class="form-control select2">
	                                        	<option value='AMAXONIA'>FORMATO AMAXONIA</option>
	                                        	<option value='FORMATO_A'>FORMATO ITESA</option>
	                                        	<option value='FORMATO_B'>FORMATO RELOJIN</option>
	                                        	<option value='FORMATO_C'>FORMATO ANVIZ</option>
	                                        </select>
	                                    </div> 
	                                </div>
	                                <br>
	                                <div class="row">
	                                    <div class="col-md-4">
	                                            Archivo:   
	                                    </div>
	                                    <div class="col-md-5">
	                                        <input type="file" name="archivo" id="archivo">  
	                                    </div> 
	                                </div>
	                                <br>

	                                <div class="row">
	                                    <div class="col-md-5"></div>
	                                    <div class="col-md-5">
	                                        <input type="submit" name="procesar" id="procesar" value="Procesar">  
	                                    </div> 
	                                </div>
	                            </div>
	                        </div>
	                        </div>
	                        </div>



	                    </div>
	                </div>
	            </div>
	        </div>
        </div>
    </div>
</div>
</form>

<!--
<form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post" name="frmAgregar" id="frmAgregar" enctype="multipart/form-data">
    <?php
    titulo("Parametros Control de Acceso", "", "control_acceso2.php", "acceso");
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
</form> -->
</body>
</html>
