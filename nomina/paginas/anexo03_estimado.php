<? 
session_start();
ob_start();
$termino=$_SESSION['termino'];

//header("Content-type: application/vnd.ms-excel");
//header("Content-Disposition: attachment; filename=comparar_nominas.xls");

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
      <td align="center" style="font-size : 16px;"><strong><? echo $_SESSION["nombre_empresa_nomina"];?></strong></td>
    </tr>
	<tr><td align="center" style="font-size : 16px;">ANEXO 03</TD></tr>
	<tr><TD align="left">FECHA INICIO : <?php echo $_POST['mesano']; ?> - FECHA FIN : <?php echo $_POST['mesano1']; ?></TD></tr>
	<tr><TD align="left">FECHA : <?php echo date('d/m/Y'); ?></TD></tr>
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
		<td align="center" >REMUNERAC. RECIBIDAS</td>
		<td align="center" >SALARIO</td>
		<td align="center" >XIII MES</td>
		<td align="center" >TOTAL A RECIBIR</td>
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
	
		$totalremu=0;
		$totalsala=0;
		$totalmes=0;
		$totaldedu;
		$totalimpc=0;
		$totalimpr=0;
		$totalimpx=0;
		
	
	while($fila=fetch_array($resultado_mov)){
		echo '<tr style="font-weight : bold;">';
		echo '<td align="center" >'.$fila[ficha].'</td>';
		echo '<td align="center" >'.$fila[apenom].'</td>';
		echo '<td align="center" >'.$fila[cedula].'</td>';
		echo '<td align="center" >'.$fila[dv].'</td>';
		
		$meses=antiguedad($fila[fecing],fecha_sql($_POST['mesano1']),'M');
		$tras=antiguedad(fecha_sql($_POST['mesano']),fecha_sql($_POST['mesano1']),'M');
		if($meses>$tras)
			$meses=$tras;
		
		echo '<td align="center" >'.$meses.'</td>';
		
		
		///// movimientos//////		
		
		$consulta_gene = "SELECT SUM(monto) as suma FROM nom_movimientos_nomina nmp inner join nom_nominas_pago nnp on nnp.codnom=nmp.codnom and nnp.frecuencia in (2,3,7) and nnp.anio=nmp.anio  and nmp.tipnom=nnp.tipnom  inner join nomconceptos nc on nc.codcon=nmp.codcon WHERE (nc.descrip LIKE 'SALARIO%' or  nc.descrip LIKE 'XIII MES') and nmp.anio='".$ma1[2]."' and nmp.ficha= ".$fila[ficha];
		$resultado_gene = query($consulta_gene,$conexion);
		$fetch_gene = fetch_array($resultado_gene);
	
		echo '<td align="center" >'.number_format($fetch_gene[suma],2,',','.').'</td>';
		
		$totalremu+=$fetch_gene[suma];
		
		
		$faltante=antiguedad(date('Y-m-d'),fecha_sql($_POST['mesano1']),'M');
		echo '<td align="center" >'.number_format(($fila[suesal]*$faltante),2,',','.').'</td>';
		echo '<td align="center" >'.number_format(((($fila[suesal]*12)/12)/3),2,',','.').'</td>';
		$asig=($fetch_gene[suma]+($fila[suesal]*$faltante)+((($fila[suesal]*12)/12)/3));
		echo '<td align="center" >'.number_format($asig,2,',','.').'</td>';
		
		$totalsala+=$fila[suesal]*$faltante;
		$totalmes+=((($fila[suesal]*12)/12)/3);
		
		
		$totaldinamico=0;
		for($w=0;$w<$i;$w++){
			$consulta_gene = "SELECT SUM(monto) as suma FROM nom_movimientos_nomina nmp inner join nom_nominas_pago nnp on nnp.codnom=nmp.codnom and nnp.frecuencia in (2,3,7) and nnp.anio=nmp.anio and nmp.tipnom=nnp.tipnom WHERE nmp.codcon='".$arraydedu[$w]."' and nmp.anio='".$ma1[2]."' ";
			$resultado_gene = query($consulta_gene,$conexion);
			$fetch_gene = fetch_array($resultado_gene);
		
			echo '<td align="center" >'.number_format($fetch_gene[suma],2,',','.').'</td>';
			$totaldinamico+=$fetch_gene[suma];
		}
		
		echo '<td align="center" >'.number_format($totaldinamico,2,',','.').'</td>';
		$renta=($asig-$totaldinamico);
		echo '<td align="center" >'.number_format($renta,2,',','.').'</td>';
		
		if($renta>11000){
			$impuesto=($renta-11000)*15/100;
			echo '<td align="center" >'.number_format($impuesto,2,',','.').'</td>';
			
		}else{
			echo '<td align="center" >'.number_format(0,2,',','.').'</td>';
			
		}
		
		
			

		
		
		
	}
	////////// TOTAL ////////
		echo '<tr><td></td><td></td>';
		echo '<td align="center" >'.number_format($totalpol1,2,',','.').'</td>';
		echo '<td align="center" >'.number_format($totaladmin1,2,',','.').'</td>';
		echo '<td align="center" >'.number_format($totalobrero1,2,',','.').'</td>';
		echo '<td align="center" >'.number_format($totalcont1,2,',','.').'</td>';
		echo '<td align="center" >'.number_format($totalpen1,2,',','.').'</td>';
		echo '<td align="center" >'.number_format($totaldire1,2,',','.').'</td>';
		echo '<td align="center" >'.number_format($totaljubi1,2,',','.').'</td>';
		
		echo '<td align="center" >'.number_format($totalpol2,2,',','.').'</td>';
		echo '<td align="center" >'.number_format($totaladmin2,2,',','.').'</td>';
		echo '<td align="center" >'.number_format($totalobrero2,2,',','.').'</td>';
		echo '<td align="center" >'.number_format($totalcont2,2,',','.').'</td>';
		echo '<td align="center" >'.number_format($totalpen2,2,',','.').'</td>';
		echo '<td align="center" >'.number_format($totaldire2,2,',','.').'</td>';
		echo '<td align="center" >'.number_format($totaljubi2,2,',','.').'</td>';
		
		echo '<td align="center" >'.number_format(($totalpol1-$totalpol2),2,',','.').'</td>';
		echo '<td align="center" >'.number_format(($totaladmin1-$totaladmin2),2,',','.').'</td>';
		echo '<td align="center" >'.number_format(($totalobrero1-$totalobrero2),2,',','.').'</td>';
		echo '<td align="center" >'.number_format(($totalcont1-$totalcont2),2,',','.').'</td>';
		echo '<td align="center" >'.number_format(($totalpen1-$totalpen2),2,',','.').'</td>';
		echo '<td align="center" >'.number_format(($totaldire1-$totaldire2),2,',','.').'</td>';
		echo '<td align="center" >'.number_format(($totaljubi1-$totaljubi2),2,',','.').'</td>';
		echo '</tr>';
?>