<?php 
session_start();
ob_start();
//$termino=$_SESSION['termino'];
include ("../header.php");
include("../lib/common.php");
include("../paginas/func_bd.php");
include("../../includes/dependencias.php");

$i=1;
$mto=$mto2=$_GET['montopre'];
$mtoc=$_GET['montocuota'];
$numc=$_GET['numcuota'];
$ficha=$_GET['ficha'];
$numpre=$_GET['numpre'];
$fecha1=$_GET['fecha1'];
$frededu=$_GET['frededu'];
$diciembre=$_GET['diciembre'];
$periodo=$_GET['periodo'];
$periodo_bi=$_GET['periodo_bi'];
if($frededu=="1")
{
?>
	<div id="divcuo">
	<table class="table" width="100%" border="0">
	<TR class="tb-head" style="font-weight : bold;">
	<div class="caption">
		<TD width="16%"># Cuota</TD><TD  width="16%">Vence</TD><TD  width="16%">Saldo inicio</TD><TD  width="16%">Amortizado</TD><TD  width="16%">Cuota</TD><TD  width="20%">Saldo fin</TD>			
	</div>
	</TR>
	<?
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
				if($cad[2] == 31)
					$cad[2]--;
				$fecha1=$cad[2]."/0".$cad[1]."/".$cad[0];
			}else{
				if($cad[2] == 31)
					$cad[2]--;
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
			$mto2-= $mtoc;

			?>
			<tr>
			<TD  width="16%"><input type="text" name="numcuo<?echo $i?>" id="numcuo<?echo $i?>" value="<?echo $i?>" size="4"></TD><TD width="16%"><input type="text" name="vence<?echo $i?>" id="vence<?echo $i?>" value="<?echo $fecha1?>" size="8" align="right"></TD><TD width="16%"><input type="text" name="salini<?echo $i?>" id="salini<?echo $i?>" size="8" value="<?echo $mto?>"></TD><TD width="16%"><input type="text" name="amort<?echo $i?>" id="amort<?echo $i?>" size="8" value="<?echo $mtoc?>"></TD><TD width="16%"><input type="text" name="mtocuo<?echo $i?>" id="mtocuo<?echo $i?>" size="8" value="<?echo $mtoc?>"></TD><TD width="20%"><input type="text" name="salfin<?echo $i?>" id="salfin<?echo $i?>" size="7" value="<?echo round($mto2,2)?>"></TD>
			</tr>
			<?
			$mto-=$mtoc;
			$i+=1;
		}
	}
	?>
	</table>
	</div>
<?php
}
elseif($frededu=="2")
{
?>
	<div id="divcuo">
	<table class="table" width="100%" border="0">
	<TR class="tb-head"  style="font-weight : bold;">
	<TD width="16%"># Cuota</TD><TD  width="16%">Vence</TD><TD  width="16%">Saldo inicio</TD><TD  width="16%">Amortizado</TD><TD  width="16%">Cuota</TD><TD  width="20%">Saldo fin</TD>		
	</TR>
	<?
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
					if($cad[0] == 31)
						$cad[0]--;
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
		 
		
			?>
			<tr>
			<TD width="16%"><input type="text" name="numcuo<?echo $i?>" id="numcuo<?echo $i?>" value="<?echo $i?>" size="4"></TD><TD width="16%"><input type="text" name="vence<?echo $i?>" id="vence<?echo $i?>" value="<?echo $fecha1?>" size="8" align="right"></TD><TD width="16%"><input type="text" name="salini<?echo $i?>" id="salini<?echo $i?>" size="8" value="<?echo $mto?>"></TD><TD width="16%"><input type="text" name="amort<?echo $i?>" id="amort<?echo $i?>" size="8" value="<?echo $mtoc?>"></TD><TD width="16%"><input type="text" name="mtocuo<?echo $i?>" id="mtocuo<?echo $i?>" size="8" value="<?echo $mtoc?>"></TD><TD width="20%"><input type="text" name="salfin<?echo $i?>" id="salfin<?echo $i?>" size="7" value="<?echo round($mto2,2)?>"></TD>
			</tr>
			<?
			$mto-=$mtoc;
			$i+=1;
		}
	}
	?>
	</table>
	</div>
<?php
}/*
elseif($frededu=="4")
{
?>
	<div id="divcuo">
	<table class="table" width="100%" border="0">
	<TR class="tb-head"  style="font-weight : bold;">
	<TD width="16%"># Cuota</TD><TD  width="16%">Vence</TD><TD  width="16%">Saldo inicio</TD><TD  width="16%">Amortizado</TD><TD  width="16%">Cuota</TD><TD  width="20%">Saldo fin</TD>		
	</TR>
	<?
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
	 
				$mto2-= $mtoc;
		 
		
			?>
			<tr>
			<TD width="16%"><input type="text" name="numcuo<?echo $i?>" id="numcuo<?echo $i?>" value="<?echo $i?>" size="4"></TD><TD width="16%"><input type="text" name="vence<?echo $i?>" id="vence<?echo $i?>" value="<?echo $fecha1?>" size="8" align="right"></TD><TD width="16%"><input type="text" name="salini<?echo $i?>" id="salini<?echo $i?>" size="8" value="<?echo $mto?>"></TD><TD width="16%"><input type="text" name="amort<?echo $i?>" id="amort<?echo $i?>" size="8" value="<?echo $mtoc?>"></TD><TD width="16%"><input type="text" name="mtocuo<?echo $i?>" id="mtocuo<?echo $i?>" size="8" value="<?echo $mtoc?>"></TD><TD width="20%"><input type="text" name="salfin<?echo $i?>" id="salfin<?echo $i?>" size="7" value="<?echo round($mto2,2)?>"></TD>
			</tr>
			<?
			$mto-=$mtoc;
			$i+=1;
		}
	}
	?>
	</table>
	</div>
<?php
}*/
elseif($frededu=="3")
{
?>
	<div id="divcuo">
	<table class="table" width="100%" border="0">
	<TR class="tb-head"  style="font-weight : bold;">
	<TD width="16%"># Cuota</TD><TD  width="16%">Vence</TD><TD  width="16%">Saldo inicio</TD><TD  width="16%">Amortizado</TD><TD  width="16%">Cuota</TD><TD  width="20%">Saldo fin</TD>
	</TR>
	<?
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
				if($cad[2] == 31)
					$cad[2]--;
				$fecha1=$cad[2]."/0".$cad[1]."/".$cad[0];
			}else{
				if($cad[2] == 31)
					$cad[2]--;
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
	 
			$mto2-= $mtoc;
			?>
			<tr>
			<TD width="16%"><input type="text" name="numcuo<?echo $i?>" id="numcuo<?echo $i?>" value="<?echo $i?>" size="4"></TD><TD width="16%"><input type="text" name="vence<?echo $i?>" id="vence<?echo $i?>" value="<?echo $fecha1?>" size="8" align="right"></TD><TD width="16%"><input type="text" name="salini<?echo $i?>" id="salini<?echo $i?>" size="8" value="<?echo $mto?>"></TD><TD width="16%"><input type="text" name="amort<?echo $i?>" id="amort<?echo $i?>" size="8" value="<?echo $mtoc?>"></TD><TD width="16%"><input type="text" name="mtocuo<?echo $i?>" id="mtocuo<?echo $i?>" size="8" value="<?echo $mtoc?>"></TD><TD width="20%"><input type="text" name="salfin<?echo $i?>" id="salfin<?echo $i?>" size="7" value="<?echo round($mto2,2)?>"></TD>
			</tr>
			<?
			$mto-=$mtoc;
			$i+=1;
		}
	}
	?>
	</table>
	</div>
<?php
}

if($frededu=="4")
{
	
	$bd = new bd($_SESSION['bd']);
	$consulta_periodos="SELECT numBisemana,fechaInicio,fechaFin from bisemanas where numBisemana = '".$periodo."'";
	$resultado_periodos=$bd->query($consulta_periodos);
	$fecha_periodo=$resultado_periodos->fetch_array();
?>
	<div id="divcuo">
	<table class="table" width="100%" border="0">
	<TR class="tb-head" style="font-weight : bold;">
	<div class="caption">
		<TD width="16%"># Cuota</TD><TD  width="16%">Vence</TD><TD  width="16%">Saldo inicio</TD><TD  width="16%">Amortizado</TD><TD  width="16%">Cuota</TD><TD  width="20%">Saldo fin</TD>			
	</div>
	</TR>
	<?
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
			$fecha1 = date('Y-m-d', strtotime($fecha. ' + 15 days'));

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
			$mto2-= $mtoc;

			?>
			<tr>
			<TD  width="16%"><input type="text" name="numcuo<?echo $i?>" id="numcuo<?echo $i?>" value="<?echo $i?>" size="4"></TD><TD width="16%"><input type="text" name="vence<?echo $i?>" id="vence<?echo $i?>" value="<?echo $fecha1?>" size="8" align="right"></TD><TD width="16%"><input type="text" name="salini<?echo $i?>" id="salini<?echo $i?>" size="8" value="<?echo $mto?>"></TD><TD width="16%"><input type="text" name="amort<?echo $i?>" id="amort<?echo $i?>" size="8" value="<?echo $mtoc?>"></TD><TD width="16%"><input type="text" name="mtocuo<?echo $i?>" id="mtocuo<?echo $i?>" size="8" value="<?echo $mtoc?>"></TD><TD width="20%"><input type="text" name="salfin<?echo $i?>" id="salfin<?echo $i?>" size="7" value="<?echo round($mto2,2)?>"></TD>
			</tr>
			<?
			$mto-=$mtoc;
			$i+=1;
		}
	}
	?>
	</table>
	</div>
<?php
}

if($frededu=="5")
{
?>
	<div id="divcuo">
	<table class="table" width="100%" border="0">
	<TR class="tb-head" style="font-weight : bold;">
	<div class="caption">
		<TD width="16%"># Cuota</TD><TD width="16%">Vence</TD><TD width="16%">Saldo inicio</TD><TD width="16%">Amortizado</TD><TD width="16%">Cuota</TD><TD width="20%">Saldo fin</TD>			
	</div>
	</TR>
	<?
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
			$fecha=$cad[2]."/".$cad[1]."/".$cad[0];
			$fecha1  = date('d/m/Y', strtotime(' + '.$periodo_bi.' days', strtotime($fecha)));

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

			?>
			<tr>
			<TD  width="16%"><input type="text" name="numcuo<?echo $i?>" id="numcuo<?echo $i?>" value="<?echo $i?>" size="4"></TD><TD width="16%"><input type="text" name="vence<?echo $i?>" id="vence<?echo $i?>" value="<?echo $fecha1?>" size="8" align="right"></TD><TD width="16%"><input type="text" name="salini<?echo $i?>" id="salini<?echo $i?>" size="8" value="<?echo $mto?>"></TD><TD width="16%"><input type="text" name="amort<?echo $i?>" id="amort<?echo $i?>" size="8" value="<?echo $mtoc?>"></TD><TD width="16%"><input type="text" name="mtocuo<?echo $i?>" id="mtocuo<?echo $i?>" size="8" value="<?echo $mtoc?>"></TD><TD width="20%"><input type="text" name="salfin<?echo $i?>" id="salfin<?echo $i?>" size="7" value="<?echo round($mto2,2)?>"></TD>
			</tr>
			<?
			$mto-=$mtoc;
			$i+=1;
		}
	}
	?>
	</table>
	</div>
<?php
}
?>