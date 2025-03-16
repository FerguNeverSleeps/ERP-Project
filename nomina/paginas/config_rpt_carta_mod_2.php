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

<form action="cartas_trabajo/carta_trabajo_mod_2.php" method="post">
	<table width="500" height="200" border="0" align="center" class="row-br">
		<tr>
		    <td height="31" class="row-br">
			    <table width="500" border="0">
			        <tr>
				        <td width="400">
				        	<div align="left">
				        		<font color="#000066"><strong>Parametros de la Carta</strong></font>
				        	</div>
				        </td>
				        <td width="100">
				        	<div align="center">
				          		<?php btn('back','list_personal.php')  ?>
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
						            <strong>SPP:</strong>
				          		</font>
			          		</div>
			          	</td>
			          	<td width="250" height="40" colspan="4" align="center" valign="middle">
			          		<div align="left">
				          		<font size="2" face="Arial, Helvetica, sans-serif">
									<input type="text" name="referencia" required style="width:240px">
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
			          	<td width="250" height="40" colspan="4">
			          		<div align="right">
				          		<font size="2" face="Arial, Helvetica, sans-serif">
						            <strong>Deudor:</strong>
				          		</font>
			          		</div>
			          	</td>
			          	<td width="250" height="40" colspan="4" align="center" valign="middle">
			          		<div align="left">
				          		<font size="2" face="Arial, Helvetica, sans-serif">
									<input type="text" name="deudor" required style="width:240px">
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
			          	<td width="250" height="40" colspan="4">
			          		<div align="right">
				          		<font size="2" face="Arial, Helvetica, sans-serif">
						            <strong>Codeudor:</strong>
				          		</font>
			          		</div>
			          	</td>
			          	<td width="250" height="40" colspan="4" align="center" valign="middle">
			          		<div align="left">
				          		<font size="2" face="Arial, Helvetica, sans-serif">
									<input type="text" name="codeudor" required style="width:240px">
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
			          	<td width="250" height="40" colspan="4">
			          		<div align="right">
				          		<font size="2" face="Arial, Helvetica, sans-serif">
						            <strong>A quien va dirigida la carta:</strong>
				          		</font>
			          		</div>
			          	</td>
			          	<td width="250" height="40" colspan="4" align="center" valign="middle">
			          		<div align="left">
				          		<font size="2" face="Arial, Helvetica, sans-serif">
						            <input type="hidden" name="ficha" value="<?php echo $_GET['ficha']; ?>">
									<input type="hidden" name="tipnom" value="<?php echo $_GET['tipnom']; ?>">
									<input type="text" name="aquien" required style="width:240px">
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
						            <input type="submit" value="Generar Carta">
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