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
<form id="form1" name="form1" method="POST" action="contabilizar_nomina2.php">
<?titulo_mejorada("Nominas  a contabilizar","","","../paginas/menu_procesos2.php")?>


	<table width="520" border="0" align="center">
        <tr>
	<br>
	<?
	$consulta="select * from nom_nominas_pago where status='C' and contabilizada=0
			   order by codnom desc";
	$resultado=query($consulta,$conexion);
	?>
	
	<?
	$i=0;
	while($fila=fetch_array($resultado))
	{
	?>
	<tr><td colspan="4" height="40">
	<input type="checkbox" name="nomina<?php echo $i?>" id="nomina<?php echo $i?>" value="<?php echo $fila[codnom];?>-<?php echo $fila[codtip];?>">
	&nbsp;&nbsp;&nbsp; <?php echo $fila[codnom]. " - ".$fila[descrip]?>
	</td></tr>
	<?
	$i++;		
	}?>
	<tr>
	<td width="467" height="40" colspan="4" align="center" valign="middle"><div align="left">Banco:<font size="2" face="Arial, Helvetica, sans-serif">
	<select name="banco" id="banco" style="width:200px">
	<option>Seleccione un banco</option>
	<?php
	$query="SELECT des_ban, ctacon FROM nombancos";
	$resultado33=query($query,$conexion);
	//ciclo para mostrar los datos
	while ($fetch = fetch_array($resultado33))
	{       
	// Opcion de modificar, se selecciona la situacion del registro a modificar             
	?>
	<option value="<?php echo $fetch['ctacon'];?>"><?php echo $fetch['des_ban'];?></option>
	<?
	}//fin del ciclo while
	?>      
	</select>
	</font></div></td>
	</tr>

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
