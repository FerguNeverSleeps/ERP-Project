<?php 
session_start();
ob_start();
$termino=$_SESSION['termino'];


include("../header.php");
include("../lib/common.php");
include("func_bd.php");


$conexion=conexion();
$consulta = "SELECT * FROM nomprestamos_cabecera ORDER BY numpre";
$resultado = query($consulta,$conexion);

while($fetch = fetch_array($resultado))
{

	/*if($fetch[monto]>0)
	{
		echo $numcuo=ceil($fetch[monto]/$fetch[mtocuota]);
		echo "<br>";
		$consulta = "update nomprestamos_cabecera set cuotas='$numcuo' where numpre='".$fetch[numpre]."'";
		$resultado1 = query($consulta,$conexion);

	}*/
	$i=1;
	$fecha1=fecha($fetch[fecpricup]);
	$numc=$fetch[cuotas];
	$mtoc=$fetch[mtocuota];
	$mto=$mto2=$fetch[monto];
	$diciembre=$fetch[diciembre];
	$ficha=$fetch[ficha];
	$numpre=$fetch[numpre];
	while($i<=$numc)
	{
		if($i==1)
		{
			$fecha=$fecha1;
			$cad=explode("/",$fecha1);
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
	 
			$mto2-= $mtoc;
		 
		
			/*?>
			<tr>
			<TD><input type="text" name="numcuo<?echo $i?>" id="numcuo<?echo $i?>" value="<?echo $i?>" size="4"></TD><TD><input type="text" name="vence<?echo $i?>" id="vence<?echo $i?>" value="<?echo $fecha1?>" size="8" align="right"></TD><TD><input type="text" name="salini<?echo $i?>" id="salini<?echo $i?>" size="8" value="<?echo $mto?>"></TD><TD><input type="text" name="amort<?echo $i?>" id="amort<?echo $i?>" size="8" value="<?echo $mtoc?>"></TD><TD><input type="text" name="mtocuo<?echo $i?>" id="mtocuo<?echo $i?>" size="8" value="<?echo $mtoc?>"></TD><TD><input type="text" name="salfin<?echo $i?>" id="salfin<?echo $i?>" size="7" value="<?echo round($mto2,2)?>"></TD>
			</tr>
			<?*/
			if(strstr($fecha1,"/"))
			{
				$fechaxxx=fecha_sql($fecha1);
			}
			else
			{
				$fechaxxx=$fecha1;
			}
			//$conexion=conexion();
			echo $consulta = "insert into nomprestamos_detalles (numpre,ficha,numcuo,fechaven,anioven,mesven,salinicial,montocuo,salfinal,estadopre,codnom) values ('$numpre','$ficha','$i','".$fechaxxx."','".$cad[2]."','".$cad[1]."','$mto','$mtoc','$mto2','Pendiente','1')";
			//$resultado1x = query($consulta,$conexion);
			echo "<br>";

			$mto-=$mtoc;
			$i+=1;
			//exit;
		}
	}

}
?>