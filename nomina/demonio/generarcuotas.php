<?php
require_once '../../generalp.config.inc.php';
session_start();
ob_start();

include("../lib/common.php");
date_default_timezone_set('America/Panama');
$db  = new bd($_SESSION['bd']);
$sql = "select * from nomprestamos_cabecera";
$a1  = $db->query($sql);
//echo $sql,"<br>";
while ($fila=fetch_array($a1)) 
{
	$numpre    = $fila['numpre'];
	$ficha     = $fila['ficha'];
	$fecha1     = $fila['fecpricup'];
	$cuotas    = $fila['cuotas'];
	$diciembre = $fila['diciembre'];
	$mto       = $mto2=$fila['monto'];
	$mtoc      = $fila['mtocuota'];
	$numcuo    = $i = 0;
	$cad =array();
	while($i<=$cuotas)
	{
		$numcuo++;
		if($i==0)
		{
			$fecha1     = $fila['fecpricup'];
			$fecha = explode("-", $fecha1);
			$fecha1 = $fecha[2]."-".$fecha[1]."-".$fecha[0];
		}

		else
		{
			$cad=explode("-",$fecha1);
			// Se busca la fecha antes de la primera quincena
			;
			if($cad[0]<=15)
			{
				$fecha_lunes  =$cad[2]."-".$cad[1]."-01";
				$num_dias_mes =date("t",strtotime($fecha_lunes));
				$cad[0]       =$num_dias_mes;
				
				if(strlen($cad[1])==1){
					$fecha1=$cad[0]."-0".$cad[1]."-".$cad[2];
				}else{
					$fecha1=$cad[0]."-".$cad[1]."-".$cad[2];
				}
			}
			// Se busca la fecha despues de la primera quincena
			elseif($cad[0]>=15)
			{
				if($cad[1]<12)
					$cad[1]+=1;
				elseif($cad[1]==12)
				{
					$cad[1] =1;
					$cad[2] +=1;
				}
				if(strlen($cad[1])==1){
					$fecha1="15-0".$cad[1]."-".$cad[2];
				}else{
					$fecha1="15-".$cad[1]."-".$cad[2];
				}

			}
		}
		if(($cad[1]==12)&&($diciembre==1))
		{
			continue;
		}
		else
		{
			if($i==$cuotas)
			{
				if($mto<$mtoc)
					$mtoc=$mto;
			}
			$mto2       -= $mtoc;
			$salinicial = round($mto,2);
			$salfinal   = round($mto2,2);
			$mtocuota   = round($mtoc,2);
			$fecha = explode("-", $fecha1);
			$fechas = $fecha[2]."-".$fecha[1]."-".$fecha[0];

			$sql = "insert into nomprestamos_detalles (numpre, ficha,numcuo,salinicial, montocuo,salfinal,fechaven)values ({$numpre},{$ficha},{$numcuo},{$salinicial},{$mtocuota},{$salfinal},'{$fechas}')";
			//echo $sql,"<br>";
			$consulta = $db->query($sql);
			if ($consulta) {
			    $db->query("COMMIT");
			} else {        
			    $db->query("ROLLBACK");
			    exit;
			}

			
			$mto-=$mtoc;
			$i+=1;
		}
	}

}
echo "Cuotas generadas";
/*
if($frededu=="1")
{
	while($i<=$numc)
	{
		$kk=0;
		if($i==1)
		{
			$fecha=$fecha1;
		}
		else
		{
			$cad=explode("/",$fecha1);
			$fecha=$cad[2]."-".$cad[1]."-".$cad[0];
			$fecha1 = date('Y-m-d', strtotime($fecha. ' + 7 days'));

			$cad=explode("-",$fecha1);
			if(strlen($cad[1])==1){
				$fecha1=$cad[2]."/0".$cad[1]."/".$cad[0];
			}else{
				$fecha1=$cad[2]."/".$cad[1]."/".$cad[0];
			}
		}
		if(($cad[1]==12)&&($diciembre==1))
		{
			continue;
		}
		else
		{
			if($i==$numc)
			{
				if($mto<$mtoc)
					$mtoc=$mto;
			}
			$mto2       -= $mtoc;
			$salinicial = round($mto,2);
			$salfinal   = round($mto2,2);
			$mtocuota   = round($mtoc,2);

			$sql = "insert into nomprestamos_detalle (numpre, ficha,numcuo,salinicial, montocou,salfinal,fechaven)values ({$numpre},{$ficha},{$numcuo},{$salinicial},{$mtocuota},{$salfinal},{$fecha1})";
			echo $sql,"<br>";
			
			$mto-=$mtoc;
			$i+=1;
		}
	}
	
}
elseif($frededu=="2")
{

	while($i<=$numc)
	{
		if($i==1)
		{
			$fecha=$fecha1;
		}
		else
		{
			$cad=explode("/",$fecha1);
			if($cad[0]<=15)
			{
				$fecha_lunes=$cad[2]."-".$cad[1]."-01";
				$num_dias_mes=date("t",strtotime($fecha_lunes));
				$cad[0]=$num_dias_mes;
				
				if(strlen($cad[1])==1){
					$fecha1=$cad[0]."/0".$cad[1]."/".$cad[2];
				}else{
					$fecha1=$cad[0]."/".$cad[1]."/".$cad[2];
				}
			}
			elseif($cad[0]>=15)
			{
				if($cad[1]<12)
					$cad[1]+=1;
				elseif($cad[1]==12)
				{
					$cad[1]=1;
					$cad[2]+=1;
				}
				if(strlen($cad[1])==1){
					$fecha1="15/0".$cad[1]."/".$cad[2];
				}else{
					$fecha1="15/".$cad[1]."/".$cad[2];
				}

			}
		}
		if(($cad[1]==12)&&($diciembre==1))
		{
			continue;
		}
		else
		{
			if($i==$numc)
			{
				if($mto<$mtoc)
					$mtoc=$mto;
			}
	 
			$mto2       -= $mtoc;
			$salinicial = round($mto,2);
			$salfinal   = round($mto2,2);
			$mtocuota   = round($mtoc,2);

			$sql = "insert into nomprestamos_detalle (numpre, ficha,numcuo,salinicial, montocou,salfinal,fechaven)values ({$numpre},{$ficha},{$numcuo},{$salinicial},{$mtocuota},{$salfinal},{$fecha1})";
			echo $sql,"<br>";
			$mto-=$mtoc;
			$i+=1;
		}
	}
	
}
elseif($frededu=="3")
{

	while($i<=$numc)
	{
		if($i==1)
		{
			$fecha=$fecha1;
			$bi=0;
			$cad=explode("/",$fecha1);
			$dia1=$cad[0];
			if(($cad[1]==2)&&(($cad[0]==28)||($cad[0]==29)))
				$bi=1;
		}
		else
		{
			$cad=explode("/",$fecha1);
			$fecha=$cad[2]."-".$cad[1]."-".$cad[0];
			if(($cad[0]<=29)&&($bi==0))
			{
				$cad[1]=$cad[1]+1;
				if($cad[1]==13)
				{
					$cad[1]=1;
					$cad[2]=$cad[2]+1;
				}
				if(($cad[1]==2)&&($dia1>=28))
					$cad[0] = cal_days_in_month(CAL_GREGORIAN, $cad[1], $cad[2]);
				else
					$cad[0]= $dia1;
				$fecha1=$cad[2]."-".$cad[1]."-".$cad[0];
			}
			else
			{
				$cad[1]=$cad[1]+1;
				if($cad[1]==13)
				{
					$cad[1]=1;
					$cad[2]=$cad[2]+1;
				}
				$cad[0] = cal_days_in_month(CAL_GREGORIAN, $cad[1], $cad[2]);
				$fecha1=$cad[2]."-".$cad[1]."-".$cad[0];
			}
			
			$cad=explode("-",$fecha1);
			if(strlen($cad[1])==1){
				$fecha1=$cad[2]."/0".$cad[1]."/".$cad[0];
			}else{
				$fecha1=$cad[2]."/".$cad[1]."/".$cad[0];
			}
		}
		if(($cad[1]==12)&&($diciembre==1))
		{
			continue;
		}
		else
		{
			if($i==$numc)
			{
				if($mto<$mtoc)
					$mtoc=$mto;
			}
	 
			$mto2       -= $mtoc;
			$salinicial = round($mto,2);
			$salfinal   = round($mto2,2);
			$mtocuota   = round($mtoc,2);

			$sql = "insert into nomprestamos_detalle (numpre, ficha,numcuo,salinicial, montocou,salfinal,fechaven)values ({$numpre},{$ficha},{$numcuo},{$salinicial},{$mtocuota},{$salfinal},{$fecha1})";
			echo $sql,"<br>";
			$mto-=$mtoc;
			$i+=1;
		}
	}

}*/


?>