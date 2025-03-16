<? 
session_start();
ob_start();
$termino=$_SESSION['termino'];

header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=comparar_nominas.xls");

include("../lib/common.php");
include("funciones_nomina.php");
//echo $_POST['mesano'];

$ma1=explode("/",$_POST['mesano']);

$ma2=explode("/",$_POST['mesano1']);

$conexion=conexion();




?>
<table align="center" width="743"  border="0">
  <tbody>
    <tr>
      <td align="center" style="font-size : 16px;" colspan=11><strong><? echo $_SESSION["nombre_empresa_nomina"];?></strong></td>
    </tr>
	<tr><td align="center" style="font-size : 16px;" colspan=11>ANEXO 03</TD></tr>
	<tr><TD align="left" colspan=11>FECHA INICIO : <?php echo $_POST['mesano']; ?> - FECHA FIN : <?php echo $_POST['mesano1']; ?></TD></tr>
	<tr><TD align="left" colspan=11>FECHA : <?php echo date('d/m/Y'); ?></TD></tr>
  </tbody>
</table>
<table align="center" width="743" border="1">
<tbody height="30">
	<tr style="font-weight : bold;">
		<td align="center" >NRO FICHA</td>
		<td align="center" >NOMBRE DEL EMPLEADO</td>
		<td align="center" >CEDULA</td>
		<td align="center" >DV</td>
		<td align="center" >MESES TRABAJADOS</td>
		<td align="center" >SALARIO</td>
		<td align="center" >XIII MES</td>
		<td align="center" >TOTAL REMUNERACION</td>
		<?php
		//SACAR DEDUCCIONES
		$deducc="select * from nomconceptos nc inner join nom_movimientos_nomina nmn on nmn.codcon=nc.codcon where nc.tipcon='D' and nc.descrip NOT LIKE '%IMPUESTO%' group by nc.codcon order by nc.descrip DESC";
		$resultado_dd=query($deducc,$conexion);
		$arraydedu;
		$i=0;
		while ($fila=fetch_array($resultado_dd)){
			echo '<td align="center" >'.strtoupper($fila[descrip]).'</td>';
			$arraydedu[$i]=$fila[codcon];
			$i++;
		}
	
		?>
		
		<td align="center" >TOTAL DEDUCC.</td>
		<td align="center" >RENTA NETA</td>
		<td align="center" >IMPUESTO SOBRE LA RENTA</td>
		
	</tr>
	
<?php
	$consulta="select * from nompersonal np where np.estado='Activo'";	
	$resultado_mov=query($consulta,$conexion);
	
		
		$totalsala=0;
		$totalmes=0;
		$totalremu=0;
		$totaldedu;
		$totaldedu2=0;
		$totalrenta=0;
		$totalimp=0;
		
		
	
	while($fila=fetch_array($resultado_mov)){
		echo '<tr style="font-weight : bold;">';
		echo '<td align="center" >'.$fila[ficha].'</td>';
		echo '<td align="left" >'.$fila[apenom].'</td>';
		echo '<td align="left" >'.$fila[cedula].'</td>';
		echo '<td align="center" >'.$fila[dv].'</td>';
		$inicio='';
		if($fila[fecing]<fecha_sql($_POST['mesano'])){
			$inicio=fecha_sql($_POST['mesano']);
			}else{
				$inicio=$fila[fecing];
			}
		$meses=floor(antiguedad($inicio,fecha_sql($_POST['mesano1']),'D')/30);
		
		
		echo '<td align="center" >'.$meses.'</td>';
		
		
		///// movimientos//////		
		
		$consulta_gene = "SELECT SUM(monto) as suma FROM nom_movimientos_nomina nmp inner join nom_nominas_pago nnp on nnp.codnom=nmp.codnom and nnp.frecuencia in (2,3,7) and nnp.anio=nmp.anio  and nmp.tipnom=nnp.tipnom  inner join nomconceptos nc on nc.codcon=nmp.codcon WHERE nc.descrip LIKE 'SALARIO%'  and nmp.anio='".$ma1[2]."' and nmp.ficha= ".$fila[ficha];
		$resultado_gene = query($consulta_gene,$conexion);
		$fetch_gene = fetch_array($resultado_gene);
		$sueldo=$fetch_gene[suma];
		$totalsala+=$sueldo;
		echo '<td align="right" >'.number_format($sueldo,2,',','.').'</td>';
		
		$consulta_gene = "SELECT SUM(monto) as suma FROM nom_movimientos_nomina nmp inner join nom_nominas_pago nnp on nnp.codnom=nmp.codnom and nnp.frecuencia in (2,3,7) and nnp.anio=nmp.anio  and nmp.tipnom=nnp.tipnom  inner join nomconceptos nc on nc.codcon=nmp.codcon WHERE  nc.descrip LIKE 'XIII MES' and nmp.anio='".$ma1[2]."' and nmp.ficha= ".$fila[ficha];
		$resultado_gene = query($consulta_gene,$conexion);
		$fetch_gene = fetch_array($resultado_gene);
		$xmes=$fetch_gene[suma];
		$totalmes+=$xmes;
		echo '<td align="right" >'.number_format($xmes,2,',','.').'</td>';
		
		echo '<td align="right" >'.number_format($sueldo+$xmes,2,',','.').'</td>';
		
		$totalremu+=$sueldo+$xmes;
		
		
				
		$totaldinamico=0;
		for($w=0;$w<$i;$w++){
			$consulta_gene = "SELECT SUM(monto) as suma FROM nom_movimientos_nomina nmp inner join nom_nominas_pago nnp on nnp.codnom=nmp.codnom and nnp.frecuencia in (2,3,7) and nnp.anio=nmp.anio and nmp.tipnom=nnp.tipnom WHERE nmp.codcon='".$arraydedu[$w]."' and nmp.anio='".$ma1[2]."' and nmp.ficha= ".$fila[ficha];
			$resultado_gene = query($consulta_gene,$conexion);
			$fetch_gene = fetch_array($resultado_gene);
		
			echo '<td align="right" >'.number_format($fetch_gene[suma],2,',','.').'</td>';
			$totaldinamico+=$fetch_gene[suma];
			$totaldedu[$w]+=$fetch_gene[suma];
		}
		
		echo '<td align="right" >'.number_format($totaldinamico,2,',','.').'</td>';
		$totaldedu2+=$totaldinamico;
		$renta=($sueldo+$xmes-$totaldinamico);
		echo '<td align="right" >'.number_format($renta,2,',','.').'</td>';
		$totalrenta+=$renta;
		if($renta>11000){
			$impuesto=($renta-11000)*15/100;
			echo '<td align="right" >'.number_format($impuesto,2,',','.').'</td>';
			$totalimp+=$impuesto;
		}else{
			echo '<td align="right" >'.number_format(0,2,',','.').'</td>';
			
		}
		
		echo '</tr>';
		
	}

	////////// TOTAL ////////
		echo '<tr><td></td><td></td><td></td><td></td><td></td>';
		echo '<td align="right" >'.number_format($totalsala,2,',','.').'</td>';
		echo '<td align="right" >'.number_format($totalmes,2,',','.').'</td>';
		echo '<td align="right" >'.number_format($totalremu,2,',','.').'</td>';
		
		//IMPRESION DINAMICA
		for($w=0;$w<$i;$w++){
			echo '<td align="right" >'.number_format($totaldedu[$w],2,',','.').'</td>';
		}
		echo '<td align="right" >'.number_format($totaldedu2,2,',','.').'</td>';
		echo '<td align="right" >'.number_format($totalrenta,2,',','.').'</td>';
		echo '<td align="right" >'.number_format($totalimp,2,',','.').'</td>';
		
		echo '</tr>';
?>