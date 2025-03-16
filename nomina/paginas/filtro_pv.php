<?php 
session_start();
ob_start();
$termino=$_SESSION['termino'];
?>
<?
include "../lib/common.php";
include "../header.php";
include("func_bd.php");	
$conexion=conexion();

$opcion=$_GET['opcion'];
?>

<script>
function direccionar()
{
	var opcion=document.getElementById('opcion')
	
	if(opcion.value==1){
		document.form1.action="../fpdf/reporte_permisos.php";
		
	}else if(opcion.value==2){
		document.form1.action="../fpdf/reporte_vacaciones.php";
	}
	
	document.form1.submit();
}
function mostrar(){
	var d = document.getElementById("imp").value;
	if (d=='Codigo'){
		document.getElementById('codigo').style.display='block';
		document.getElementById('rango').style.display='none';	
	}

	if (d=='Todos'){
		document.getElementById('codigo').style.display='none';
		document.getElementById('rango').style.display='none';	
	}
}
window.onload = function(){
	document.getElementById('codigo').style.display='none';
	
}
</script>
<form id="form1" name="form1" method="post" action="">
<?titulo_mejorada("Parametros del reporte","","","submenu_reportes_integrantes.php")?>


	<table width="100%" height="229" align="center" border="0">
	<tr>
	<td  height="190" align="center" >

	<table width="100%" align="center" border="0">

	<TR>
	<td>
	<div align="center" >
	<select  name="opcion" id="opcion" >
		<option  value="1" selected>REPORTE PERMISOS</option>
		<option  value="2">REPORTE VACACIONES</option>
		
	</select>
	</div>
	</td>
	</TR>
	
	<tr>
	
		<td >
		<div align="center" >
		Fecha Inicio: &nbsp; 
		<input name="mesano" type="text"  id="mesano" style="width:100px" value="<?php echo date("d/m/Y") ?>" maxlength="60" >
		<input name="image2" type="image" id="mesano2" src="../lib/jscalendar/cal.gif" />
		<script type="text/javascript">Calendar.setup({inputField:"mesano",ifFormat:"%d/%m/%Y",button:"mesano2"});</script>
		</BR>
		Fecha FIN:&nbsp; &nbsp; &nbsp; 
		<input name="mesano1" type="text"  id="mesano1" style="width:100px" value="<?php echo date("d/m/Y") ?>" maxlength="60" >
		<input name="image2" type="image" id="mesano21" src="../lib/jscalendar/cal.gif" />
		<script type="text/javascript">Calendar.setup({inputField:"mesano1",ifFormat:"%d/%m/%Y",button:"mesano21"});</script>
		</div>
	</td>

	</tr>
	
	<tr>
	
	
	<td>
	<div align="center" >
	<select  name="imp" id="imp" onchange="mostrar();">
		<option  value="Todos" selected>Imprimir todo el Personal</option>
		<option  value="Codigo">Imprimir por Codigo</option>
		
	</select>
	</div>
	</td>
	
	
	
	</tr>
	
	<tr>
		<td>
		<div align="center" id='codigo'><font size="2" face="Arial, Helvetica, sans-serif">
		Codigo: <input type="text" maxlength="4" size="4" name="cod" id="cod" value="0">
		</font></div></td>
	</tr>

	

	</table>

<p>&nbsp;</p>
<table width="100%" border="0" align="center">
<tr>
<td><div align="center">
<?btn('ok','direccionar();',2); ?>
</div></td>
</tr>
</table>
</td>
</tr>
</table>
</form>