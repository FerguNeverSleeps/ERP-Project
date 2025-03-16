<?php 
session_start();

include ("../header.php");
include("../lib/common.php");
include("func_bd.php");	
?>
<script>
function horizontal_nomina_xls()
{
	var ficha = document.form1.ficha.value; // PK nomtipos_nomina
	 
	//alert('Codnom '+codnom+' Codtip '+codtip+ ' Departamento '+ck_dep+' Deduccion '+ck_ded);
	if(ficha=='')
	{
		alert('Por favor, seleccione una ficha');
	}
	else
	{
	 
			location.href='rpt_acumulado.php?ficha='+ficha ; 
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
		<td width="467" height="40"  valign="middle" align="left"  style="padding-left: 40px">Ficha:
			 <input type="text" name="ficha" id="ficha" value="" >
			<input type="hidden" name="codt" id="codt" value="<?php echo $codtip; ?>" >
		</td>
	</tr>



<tr>
	<td width="467" height="40"  valign="middle" align="left"  style="padding-left: 40px">
		Fecha Inicio: &nbsp; 
		<input name="mesano" type="text"  id="mesano" style="width:100px" value="<?php echo date("d/m/Y") ?>" maxlength="60" >
		<input name="image2" type="image" id="mesano2" src="../lib/jscalendar/cal.gif" />
		<script type="text/javascript">Calendar.setup({inputField:"mesano",ifFormat:"%d/%m/%Y",button:"mesano2"});</script>
		</BR>
		Fecha Fin:&nbsp; &nbsp; &nbsp; 
		<input name="mesano1" type="text"  id="mesano1" style="width:100px" value="<?php echo date("d/m/Y") ?>" maxlength="60" >
		<input name="image2" type="image" id="mesano21" src="../lib/jscalendar/cal.gif" />
		<script type="text/javascript">Calendar.setup({inputField:"mesano1",ifFormat:"%d/%m/%Y",button:"mesano21"});</script>
	</td>
</tr>



	<tr>
		<td style="padding-left: 40px">
			<input type="checkbox" name="departamento" id="departamento" value="1">&nbsp;&nbsp;Agrupar por Mes
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
