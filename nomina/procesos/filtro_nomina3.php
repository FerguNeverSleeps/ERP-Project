<?php 
session_start();
ob_start();
$termino=$_SESSION['termino'];
?>
<?
include "../lib/common.php";
include "../header.php";
//include("func_bd.php");	
$conexion=conexion();

?>
<form id="form1" name="form1" method="post" action="orden_nomina3.php">
<?titulo_mejorada("Nominas","","","../paginas/menu_procesos2.php")?>


	<table width="520" border="0" align="center">
        <tr>
	<br>
	<?
	$consulta="select * from nom_nominas_pago where status='C' and comprometida=0";
	$resultado=query($consulta,$conexion);
	?>
	
	<?
	$i=0;
	while($fila=fetch_array($resultado))
	{
	?>
	<tr><td colspan="4" height="40">
	<input type="checkbox" name="nomina<?php echo $i?>" id="nomina<?php echo $i?>" value="<?php echo $fila[codnom];?>-<?php echo $fila[codtip];?>">
	&nbsp;&nbsp;&nbsp; <?php echo $fila[descrip]?>
	</td></tr>
	<?
	$i++;		
	}?>
	</table>

<p>&nbsp;</p>
<table width="467" border="0">
<tr>
<td width="466">
<input type="hidden" name="cant" id="cant" value="<?php echo $i;?>">
<div align="right">
<?btn('ok','form1',1); ?>
</div></td>
</tr>
</table>
</td>
</tr>
</table>
</form>
