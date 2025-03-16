<?php 
session_start();
ob_start();
$termino=$_SESSION['termino'];
?>
<?php
include ("../header.php");
include("../lib/common.php");
include ("../paginas/func_bd.php") ;
$conexion=conexion();

$consulta="SELECT * FROM nomprestamos_cabecera";
$resultado=query($consulta,$conexion);
while($fetch=fetch_array($resultado))
{
	$i=1;
	$numc=$fetch[cuotas];
	$fecha1=$fetch[fecpricup];
	$mto=$mto2=$fetch[monto];
	$mtoc=$fetch[mtocuota];
	$ficha=$fetch[ficha];
	$numpre=$fetch[numpre];

	while($i<=$numc)
	{
		if($i==1)
		{
			$fecha=$fecha1;
			$cad=explode("-",$fecha1);
		}
		else
		{
			$cad=explode("-",$fecha1);
			if($cad[2]<=15)
			{
				$fecha_lunes=$cad[0]."-".$cad[1]."-01";
				$num_dias_mes=date("t",strtotime($fecha_lunes));
				$cad[2]=$num_dias_mes;
				
				if(strlen($cad[1])==1){
					$fecha1=$cad[0]."-0".$cad[1]."/".$cad[2];
				}else{
					$fecha1=$cad[0]."-".$cad[1]."-".$cad[2];
				}
			}
			elseif($cad[2]>=15)
			{
				if($cad[1]<12)
					$cad[1]+=1;
				elseif($cad[1]==12)
				{
					$cad[1]=1;
					$cad[0]+=1;
				}
				if(strlen($cad[1])==1){
					$fecha1=$cad[0]."-0".$cad[1]."-15";
				}else{
					$fecha1=$cad[0]."-".$cad[1]."-15";
				}

			}
		}
		if($i==$numc)
		{
			if($mto<$mtoc)
				$mtoc=$mto;
		}
		$mto2-=$mtoc;
		
		$consulta="insert into nomprestamos_detalles (numpre,ficha, numcuo,fechaven, anioven, mesven, salinicial, montocuo, salfinal, estadopre, codnom) values ('$numpre', '$ficha', '$i', '$fecha1', '$cad[0]', '$cad[1]', '$mto', '$mtoc','$mto2','Pendiente','1')";
		$resultado2=query($consulta,$conexion);
		//echo "<br>";
		$mto-=$mtoc;
		$i+=1;	
	}
}