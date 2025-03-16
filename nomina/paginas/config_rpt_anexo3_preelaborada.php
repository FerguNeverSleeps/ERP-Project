<?php 
session_start();

include ("../header.php");
include("../lib/common.php");
include("func_bd.php");	
?>
<script>
function fondo_cesantia_trimestral_xls()
{
	var num_trimestre = document.form1.cboNumTrimestre.value; // PK nom_nominas_pago
	var codtip = document.form1.codt.value;
	if(num_trimestre==''){
		alert('Por favor, seleccione un trimestre');
	}else{
            if(num_trimestre=='1')
            {
		location.href='fondo_cesantia_trimestre1_xls.php?codtip='+codtip;
                //location.href='fondo_cesantia_xls.php?num_semestre='+num_semestre;
            }
            if(num_trimestre=='2')
            {
		location.href='fondo_cesantia_trimestre2_xls.php?codtip='+codtip;
                //location.href='fondo_cesantia_xls.php?num_semestre='+num_semestre;
            }
            if(num_trimestre=='3')
            {
		location.href='fondo_cesantia_trimestre3_xls.php?codtip='+codtip;
                //location.href='fondo_cesantia_xls.php?num_semestre='+num_semestre;
            }
            if(num_trimestre=='4')
            {
		location.href='fondo_cesantia_trimestre4_xls.php?codtip='+codtip;
                //location.href='fondo_cesantia_xls.php?num_semestre='+num_semestre;
            }
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
		<td width="467" height="40"  valign="middle" align="left"  style="padding-left: 40px">Trimestre:
			<select name="cboNumTrimestre" id="cboNumSemestre" style="width:400px">
			<option value="">Seleccione Trimestre</option>
			<option value="1">1er Trimestre</option>
                        <option value="2">2do Trimestre</option>
                        <option value="3">3er Trimestre</option>
                         <option value="4">4to Trimestre</option>
			</select>
                        <?php
				$query="SELECT codnom, descrip, codtip 
				        FROM   nom_nominas_pago WHERE codtip='".$_SESSION['codigo_nomina']."'";
						
				$result=sql_ejecutar($query);
				while ($row = fetch_array($result))
				{
					$codtip = $row['codtip']; // Tabla nomtipos_nomina
					// 1-Direccion 2-Fijos 3-Pensionados 4-Ingresos Pendientes
				
				}	
			?>
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
	default:
		btn('xls','fondo_cesantia_trimestral_xls();',2);
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
