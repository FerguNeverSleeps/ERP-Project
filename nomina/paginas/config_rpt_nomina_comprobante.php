<?php 
session_start();

include ("../header.php");
include("../lib/common.php");
include("func_bd.php");	
?>
<script>
function VerRecibo()
{
	AbrirVentana('rpt_reporte_de_nomina.php?nomina_id='+document.form1.cboTipoNomina.value+'&codt='+document.form1.codt.value,660,800,0);
}

function comprobante_diario_xls()
{
	var codnom = document.form1.cboTipoNomina.value; // PK nom_nominas_pago
	var codtip = document.form1.codt.value; // PK nomtipos_nomina
	
	if(codnom==''){
		alert('Por favor, seleccione una nomina');
	}else{
		//AbrirVentana('comprobante_diario_xls.php?codnom='+codnom+'&codtip='+codtip, 660, 800, 0);
        location.href='comprobante_diario_xls.php?codnom='+codnom+'&codtip='+codtip;	
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
		<td width="467" height="40"  align="center" valign="middle" align="left">Planilla:
			<select name="cboTipoNomina" id="select2" style="width:400px">
			<option value="">Seleccione planilla</option>
			<?php
				$query="SELECT codnom, descrip, codtip 
				        FROM   nom_nominas_pago WHERE codtip='".$_SESSION['codigo_nomina']."'";
						
				$result=sql_ejecutar($query);
				while ($row = fetch_array($result))
				{
					$codtip = $row['codtip']; // Tabla nomtipos_nomina
					// 1-Direccion 2-Fijos 3-Pensionados 4-Ingresos Pendientes
				?>
					<option value="<?php echo $row['codnom'];?>"><?php echo "COD.: ".$row['codnom']. " / ".$row['descrip']; ?></option>
				<?php
				}	
			?>
			</select>
			<input type="hidden" name="codt" id="codt" value="<?php echo $codtip; ?>" >
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
	case "general":
		btn('ok','resumen_conceptos_xls();',2);
		break;
	case "analisis":
		btn('ok','analisis_conceptos();',2);
		echo "<br>";
		btn("xls",'analisis_conceptosxls();',2);
		break;
	default:
		btn('xls','comprobante_diario_xls();',2);
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
