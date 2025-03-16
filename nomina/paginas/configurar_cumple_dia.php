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
	if(opcion.value==1)
		AbrirVentana('../fpdf/reporte_cumple.php?mes='+document.form1.mes.value,660,800,0);
	else
	if(opcion.value==2)
		AbrirVentana('../fpdf/reporte_madre_padre.php?repre='+document.form1.representante.value,660,800,0);
}
</script>
<form id="form1" name="form1" method="post" action="">
<?titulo_mejorada("Parametros del reporte","","","submenu_reportes_integrantes.php")?>

<?
if($opcion==1)
{
?>
	<table width="100%" height="229" align="center" border="0">
	<tr>
	<td  height="190" align="center" >
	<input type="hidden" name="opcion" id="opcion" value="<?php echo $opcion;?>">
	<table width="100%" align="center" border="0">
	<tr>
	<td height="40" colspan="4" align="center" valign="middle">
	<div align="center"><font size="2" face="Arial, Helvetica, sans-serif">
	Mes: <?php 
   $meses = array(); 
   $meses[1] = "Enero"; 
   $meses[2] = "Febrero"; 
   $meses[3] = "Marzo"; 
   $meses[4] = "Abril";
   $meses[5] = "Mayo";
   $meses[6] = "Junio";
   $meses[7] = "Julio";
   $meses[8] = "Agosto";
   $meses[9] = "Septiembre";
   $meses[10] = "Octubre";
   $meses[11] = "Noviembre";
   $meses[12] = "Diciembre";
   
   echo "<select name=\"mes\">";            
   for($mes=1; $mes<=12; $mes++){ 
	   
      if (date("m") == $mes){ 
         echo "<option value=\"$mes\" selected>$meses[$mes]</option>"; 
      } 
      else { 
         echo "<option value=\"$mes\">$meses[$mes]</option>"; 
      } 
   } 
   echo"</select>"; 
?>
	
	
	
	</font></div></td>
	</tr>
	</table>
<?
}else{
?>
	<table width="100%" height="229" align="center" border="0">
	<tr>
	<td  height="190" align="center" >
	<input type="hidden" name="opcion" id="opcion" value="<?php echo $opcion;?>">
	<table width="100%" align="center" border="0">
	<tr>
	<td height="40" colspan="4" align="center" valign="middle">
	<div align="center"><font size="2" face="Arial, Helvetica, sans-serif">
	Representante: 
	<select name='representante'>
		<option value='Femenino' >Madre </option>
		<option value='Masculino' >Padre </option>
	</select>
	
	</font></div></td>
	</tr>
	</table>
<?

}
?>
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