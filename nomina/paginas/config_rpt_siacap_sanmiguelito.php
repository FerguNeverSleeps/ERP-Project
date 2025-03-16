<?php 
session_start();
ob_start();
$termino=$_SESSION['termino'];
?>
<?php
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // Always modified
header("Cache-Control: private, no-store, no-cache, must-revalidate"); // HTTP/1.1 
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache"); // HTTP/1.0
?>

<?php
include ("../header.php");
include("../lib/common.php");
include("func_bd.php");	
?>
<script>
function AbrirListPersonal()
{
AbrirVentana('list_personal.php',660,800,0);
}
</script>

<form action="../fpdf/rpt_siacap_sanmiguelitopdf.php" method="post">
	<table width="500" height="200" border="0" align="center" class="row-br">
		<tr>
		    <td height="31" class="row-br">
			    <table width="500" border="0">
			        <tr>
				        <td width="400">
				        	<div align="left">
				        		<font color="#000066"><strong>Parametros del Reporte</strong></font>
				        	</div>
				        </td>
				        <td width="100">
				        	<div align="center">
				          		<?php btn('back','submenu_reportes.php?modulo=45')  ?>
				        	</div>
				        </td>
			      </tr>
			    </table>
			</td>
		</tr>
		<tr>
	        <td width="500" height="10" class="ewTableAltRow">
		        <table width="500" border="0">
			        <tr>
			          	<td width="250" height="40" colspan="4">
			          		<div align="right">
				          		<font size="2" face="Arial, Helvetica, sans-serif">
						            <strong>Seleccione el año:</strong>
				          		</font>
			          		</div>
			          	</td>
			          	<td width="250" height="40" colspan="4" align="center" valign="middle">
			          		<div align="left">
				          		<font size="2" face="Arial, Helvetica, sans-serif">
									<input type="number" min="2000" max="<?php echo date('Y'); ?>" name="anio" id="anio" style="width:225px" placeholder="Valor maximo: <?php echo date('Y'); ?>">
				          		</font>
			          		</div>
			          	</td>
			        </tr>
			        <tr>
			          	<td width="250" height="40" colspan="4">
			          		<div align="right">
				          		<font size="2" face="Arial, Helvetica, sans-serif">
						            <strong>Seleccione mes del reporte:</strong>
				          		</font>
			          		</div>
			          	</td>
			          	<td width="250" height="40" colspan="4" align="center" valign="middle">
			          		<div align="left">
				          		<font size="2" face="Arial, Helvetica, sans-serif">
									<select name="mes" id="mes" style="width:225px">
			                            <option value="1">Seleccione un Mes</option>
			                            <option value="1">Enero</option>
			                            <option value="2">Febrero</option>
			                            <option value="3">Marzo</option>
			                            <option value="4">Abril</option>
			                            <option value="5">Mayo</option>
			                            <option value="6">Junio</option>
			                            <option value="7">Julio</option>
			                            <option value="8">Agosto</option>
			                            <option value="9">Septiembre</option>
			                            <option value="10">Octubre</option>
			                            <option value="11">Noviembre</option>
			                            <option value="12">Diciembre</option>
									</select>
				          		</font>
			          		</div>
			          	</td>
			        </tr>
		    	</table>
	        </td>
	    </tr>
	    <tr>
	        <td width="500" height="10" class="ewTableAltRow">
		        <table width="500" border="0">
			        <tr>
			          	<td width="225" height="40" colspan="4">
			          		<div align="right">
				          		<font size="2" face="Arial, Helvetica, sans-serif">
						            <input type="submit" value="Generar Reporte">
				          		</font>
			          		</div>
			          	</td>
			          	<td width="50" height="40" colspan="4" align="center" valign="middle">
			          	</td>
			          	<td width="225" height="40" colspan="4" align="center" valign="middle">
			          		<div align="left">
				          		<input type="reset" value="Limpiar">
			          		</div>
			          	</td>
			        </tr>
		    	</table>
	        </td>
	    </tr>
	</table>
</form>
</body>
</html>