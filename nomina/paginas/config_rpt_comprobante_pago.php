<?php 
session_start();

include("../header.php");
include("../lib/common.php");
include("func_bd.php");	
?>
<script>
function reporte_xls()
{
	var codnom = document.form1.codnom.value; // Código de la nómina - nom_nominas_pago
	var codtip = document.form1.codtip.value; // Tipo de nómina - nomtipos_nomina

	if(codnom=='')
	{
		alert('Por favor, seleccione una planilla');
	}
	else
	{
		//console.log("codnom = "+codnom+" - codtip = "+codtip);
		location.href='comprobante_pago_xls.php?codnom='+codnom+'&codtip='+codtip; 
	}
}
</script>

<form id="form1" name="form1" method="post" action="">
	<table width="807" height="229" border="0" class="row-br">
		<tr>
			<td height="31" class="row-br">
				<table width="789" border="0">
					<tr>
						<td width="762"><div align="left"><font color="#000066"><strong>Par&aacute;metros del Reporte</strong></font></div></td>
						<td width="17"><div align="center"><?php btn('back','submenu_reportes.php?modulo=45'); ?></div></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td width="489" height="190" class="ewTableAltRow">
				<br>
				<table width="520" border="0">
					<tr>
						<td width="467" height="40"  valign="middle" align="left"  style="padding-left: 40px">Planilla:
							<select name="codnom" id="codnom" style="width:400px">
								<option value="">Seleccione una Planilla</option>
								<?php
									$query="SELECT codnom, descrip, codtip 
									        FROM   nom_nominas_pago WHERE codtip='".$_SESSION['codigo_nomina']."'";
											
									$result=sql_ejecutar($query);

									while ($row = fetch_array($result))
									{
										$codtip = $row['codtip']; // Tabla nomtipos_nomina
										?>
										<option value="<?php echo $row['codnom'];?>"><?php echo $row['descrip']; ?></option>
										<?php
									}	
								?>
							</select>
							<input type="hidden" name="codtip" id="codtip" value="<?php echo $codtip; ?>" >
						</td>
					</tr>
				</table>
				<p>&nbsp;</p>
				<table width="310" border="0">
					<tr>
						<td align="right">
							<?php
								btn('xls','reporte_xls();',2);
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
