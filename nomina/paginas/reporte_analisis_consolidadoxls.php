<?php 
session_start();
ob_start();
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=prestamos_saldos.xls");
include("../lib/common.php");
// include("../header.php");
// include("func_bd.php");


list($mes,$ano)=explode('/',$_POST[mesano]);
$frecuencia=$_POST[frecuencia];
$codcon1=$_POST['concepto'];
$nomina=$_POST['nomina'];

$conexion=conexion();
$var_sql = "SELECT imagen_izq, imagen_der,nom_emp FROM nomempresa";
$rs = query($var_sql,$conexion);
$row_rs = mysqli_fetch_array($rs);
$emp = $row_rs['nom_emp'];

$consulta="select * from nomconceptos where codcon='".$concepto_id."'";
$resultado=query($consulta,$conexion);
$fila_concepto=fetch_array($resultado);

echo $encabezado;
$date1=date('d/m/Y');
$date2=date('h:i a');	
$datos="<TABLE width='743' align='center' border='0'>
<TR>
<TD align='left' colspan='5'><strong>$emp </strong></TD>
</TR>
<TR>
<TD align='LEFT' colspan='3'><strong>RECURSOS HUMANOS</strong></td><TD align='right' colspan='2'><strong>Fecha: </strong>$date1</TD>
</TR>
<TR>
<TD align='right'  colspan='5'><strong>Hora: </strong>$date2</TD>
</TR>
</TABLE>";
echo $datos;

$con1=explode(',',$_POST['concepto']);
$consulta="select * from nomconceptos WHERE codcon='$con1[0]'";
$resultado33=query($consulta,$conexion);
$fetch33 = fetch_array($resultado33);


$consulta="select descrip from nomfrecuencias WHERE codfre in ($frecuencia)";
$resultado32=query($consulta,$conexion);
while($fetch32 = fetch_array($resultado32))
{
	$frecc.=$fetch32[descrip].', ';
}
$frecc=substr($frecc,0,-2);

$consulta="select descrip from nomtipos_nomina WHERE codtip in ($nomina)";
$resultado34=query($consulta,$conexion);
while($fetch34 = fetch_array($resultado34))
{
	$nomm.=$fetch34[descrip].', ';
}
$nomm=substr($nomm,0,-2);
?>
<table align="center">
<tbody>
<tr>
<td align="left" style="font-size : 16px;"  colspan='5'><strong>NOMINA(S): <?php echo $nomm?></strong></td>
</tr>
<tr>
<td align="center" style="font-size : 16px;"  colspan='5'><strong><?php echo $fetch33['descrip']?></strong></td>
</tr>
<tr>
<td align="center" style="font-size : 14px;"  colspan='5'><strong><?php echo utf8_decode($frecc).' de '.$_POST[mesano]?></strong></td>
</tr>

</tbody>
</table>

<table align="center"  >
<tbody>
<tr style="border-bottom-style : solid; border-bottom-width : 1px; font-weight : bold;">
<td align="left" >CODIGO</td>
<td align="left" >CEDULA</td>
<td align="center" >APELLIDOS Y NOMBRES</td>
<td align="center" >REF</td>
<td align="center">MONTO</td>
</tr>
<?php
$consulta="SELECT np.apenom, nmn.codnom, nmn.codcon, nmn.ficha, nmn.valor, sum(nmn.monto) as monto, nmn.cedula, nmn.tipnom "
        . "FROM nom_movimientos_nomina nmn "
        . "join nom_nominas_pago nmp on (nmn.codnom=nmp.codnom and nmn.tipnom=nmp.tipnom) "
        . "JOIN nompersonal np on (np.cedula=nmn.cedula) WHERE nmn.codcon in ($codcon1) and nmp.frecuencia in ($frecuencia) "
        . "and nmn.mes='$mes' and nmn.anio='$ano' and nmn.tipnom in ($nomina) "
        . "group by nmn.cedula "
        . "order by nmn.ficha";
$resultado=query($consulta,$conexion);

$totalwhile=num_rows($resultado);

$totalPrest=$totalSaldo=0;
while($fila=fetch_array($resultado))
{
	$conexion=conexion();
	if($fila['monto']!=0)
	{
//		$cedula="";
//		if($fila['nacionalidad']==0)
//			$cedula.="V";
//		else
//			$cedula.="E";
//		
//		if(strlen($fila['cedula'])==6)
//		{
//			$cedula.="00";
//		}
//		elseif(strlen($fila['cedula'])==7)
//		{
//			$cedula.="0";
//		}
		
		$cedula=$fila['cedula'];
		?>
		<tr>
		<td><?php echo $fila['ficha'];?></td>
		<td><?php echo $cedula?></td>
		<td><?php echo utf8_decode($fila['apenom'])?></td>
                <td align="center" ><?php echo number_format($fila['valor'],2,'.','')?></td>
		<td align="center" ><?php echo number_format($fila['monto'],2,'.','')?></td>
		</tr>
		<?php 
		$total+=$fila['monto'];
	}
}
?>

</tbody>
</table>

<table align="center" width="700"  style="border-bottom-style : double;">
<tbody>
<tr >
<td align="center" width="50"></td>
<td align="center" width="100"></td>
<td align="center" width="350"></td>
<td align="center" width="150"></td>
</tr>

<tr>
<td colspan="5"> <hr  style="border-bottom-style : dotted; border-left-style : dotted; border-right-style : dotted; border-top-style : dotted;"></td>
</tr>

<tr style="font-weight : bold;">

<td align="left" colspan="3">CANTIDAD DE PERSONAS: <?php echo number_format($totalwhile,0)?></td>
<td align="center"><?php echo number_format($total,2,',','.')?></td>
</tr>

</tbody>
</table>
<br>
<br>
<table align="center" width="700" border="1" >
<tbody>
<tr>
<td align="center" height="80" valign="bottom">R.R.H.H.</td>
<td align="center" valign="bottom">ADMINISTRACI&Oacute;N</td>
<td align="center" valign="bottom">PRESIDENTE</td>
</tr>
</tbody>
</table>
</div>
</body>
</html>
