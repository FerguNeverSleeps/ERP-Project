<?php 
session_start();

include ("../header.php");
include("../lib/common.php");
include("func_bd.php");	
?>
<script>
function horizontal_nomina_xls()
{
	var codnom = document.form1.cboTipoNomina.value; // PK nom_nominas_pago
	var codtip = document.form1.codt.value; // PK nomtipos_nomina
	var ck_dep = document.getElementById('departamento').checked;
	var ck_ded = document.getElementById('deduccion').checked;
	//alert('Codnom '+codnom+' Codtip '+codtip+ ' Departamento '+ck_dep+' Deduccion '+ck_ded);
	if(codnom==''){
		alert('Por favor, seleccione una nomina');
	}else{
		if(ck_dep && ck_ded)
			location.href='horizontal_nomina_agrupado_xls.php?codnom='+codnom+'&codtip='+codtip;
		else if(ck_dep)
			location.href='horizontal_nomina_departamento_xls.php?codnom='+codnom+'&codtip='+codtip;
		else if(ck_ded)
			location.href='horizontal_nomina_deduccion_xls.php?codnom='+codnom+'&codtip='+codtip;
		else
			location.href='horizontal_nomina_xls.php?codnom='+codnom+'&codtip='+codtip; 
	}
}
</script>

<form id="form1" name="form1" method="post" action="">
<table width="807" height="229" border="0" class="row-br">
<tr>
<td height="31" class="row-br">
<table width="789" border="0">
<tr>
<td width="762"><div align="left"><font color="#000066"><strong>Parámetros del Reporte</strong></font></div></td>
<td width="17"><div align="center">
<?php btn('back','submenu_reportes.php?modulo=45')  ?>
</div>
</td>
</tr>
</table>
</td>
</tr>

<tr>
<td width="489" height="190" class="ewTableAltRow">
<br>
<table width="520" border="0">
	<tr>
		<td width="467" height="40"  valign="middle" align="left"  style="padding-left: 40px">N&oacute;mina:
			<select name="cboTipoNomina" id="select2" style="width:400px">
			<option value="">Seleccione una n&oacute;mina</option>
			<?php
				$query="SELECT codnom, descrip, codtip 
				        FROM   nom_nominas_pago WHERE codtip='".$_SESSION['codigo_nomina']."'";
						
				$result=sql_ejecutar($query);
				while ($row = fetch_array($result))
				{
					$codtip = $row['codtip']; // Tabla nomtipos_nomina
					// 1-Direccion 2-Fijos 3-Pensionados 4-Ingresos Pendientes
				?>
					<option value="<?php echo $row['codnom'];?>"><?php echo $row['descrip']; ?></option>
				<?php
				}	
			?>
			</select>
			<input type="hidden" name="codt" id="codt" value="<?php echo $codtip; ?>" >
		</td>
	</tr>
	<tr>
		<td style="padding-left: 40px">
			<input type="checkbox" name="departamento" id="departamento" value="1">&nbsp;&nbsp;Agrupar por departamento
		</td>
	</tr>
	<tr>
		<td style="padding-left: 40px">
			<input type="checkbox" name="deduccion" id="deduccion" value="1">&nbsp;&nbsp;Agrupar deducciones y patronales
		</td>
	</tr>
</table>
<p>&nbsp;</p>
<table width="467" border="0">
<tr>
<td width="466" align="right">
<?php
$valor=$_GET['opcion'];
switch($valor)
{
	default:
		btn('xls','horizontal_nomina_xls();',2);
		break;
}
?>
</td>
</tr>
</table>
</td>
</tr>
</table>
</form>
</body>
</html>
