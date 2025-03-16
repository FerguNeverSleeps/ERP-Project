<?php
session_start();
ob_start();
$termino = $_SESSION['termino'];
?>
<?php
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // Always modified
header("Cache-Control: private, no-store, no-cache, must-revalidate"); // HTTP/1.1
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache"); // HTTP/1.0
?>
<link rel="stylesheet" type="text/css" href="dialog_box.css" />

<?php
include ("../header.php");
include("../lib/common.php");
include("func_bd.php");

$titulos = array( "Fecha","Entrada","S. Almu","E. Almu","Salida","Ordinarias","Tardanza","Extras","Extras Ext", "Noc", "Noc Ext","Dom","Nac", "ExtNac", "NacNoc");

$reg = $_GET['reg'];
$pagina = $_GET['pagina'];

$select = "select ficha, apenom from nompersonal";
$result = sql_ejecutar($select);


if ($_POST["guardar"]) {
    if ($_POST['ficha'] != "" && $_POST['fecha'] != "" ) {
    		$pagina = $_POST['pagina'];
        $cod = $_POST['id'];
        $mod = "insert into reloj_detalle (id_encabezado, ficha, fecha, entrada, salmuerzo, ealmuerzo, salida, ordinaria, extra, extraext, extranoc, extraextnoc, domingo, tardanza, nacional, extranac, extranocnac) values ('".$_POST[reg]."', '".$_POST[ficha]."', '".$_POST[fecha]."', '".$_POST[entrada]."', '".$_POST[salmuerzo]."', '".$_POST[ealmuerzo]."', '".$_POST[salida]."', '".$_POST[ordinaria]."', '".$_POST[extra]."', '".$_POST[extraext]."', '".$_POST[extranoc]."', '".$_POST[extraextnoc]."', '".$_POST[domingo]."', '".$_POST[tardanza]."', '".$_POST[nacional]."', '".$_POST[extranac]."', '".$_POST[extranocnac]."')";
        $result = sql_ejecutar($mod);
        header("Location: control_acceso_detalle2.php?reg=$reg&pagina=$pagina");
    } else {
        ?>
        <script type="text/javascript">
            alert("ALERTA:  \n Debe introducir todos los datos para guardar.");
        </script>
        <?php
    }
}
?>
<form action="cargar_control_acceso_detalle.php?reg=<?php echo $reg ?>&id=<?php echo $id ?>" method="post" name="frmAgregar" id="frmAgregar" enctype="multipart/form-data">
<?php titulo("Editar Control de Acceso", "", "control_acceso_detalle2.php?reg=$reg&pagina=$pagina", "acceso"); ?>
<br><br><br><br><br><br><br><br>
<div id="content">
<table  width="400" align=center border="0" >
<tr class="tb-fila">
<td  ><font size="2" face="Arial, Helvetica, sans-serif">  </font></td>
<td>

<input name="reg" type="hidden" id="reg"  value="<?php echo $reg ?>" />
</td>
</tr>
<tr >
<td  ><font size="2" face="Arial, Helvetica, sans-serif">Ficha Trabajador:</font></td>
<td colspan="14">
<select name="ficha" id="ficha">
<?php while($fila = fetch_array($result)) {?>
<option value="<?php echo $fila[ficha]?>"><?php echo $fila[ficha]." -- ".$fila[apenom]?></option>
<?php }?>
</select>
</td>
</tr>

<tr class="tb-head" >
<?php
foreach ($titulos as $nombre) {
 echo "<td><STRONG>$nombre</STRONG></td>";
}
?>
</tr>
         
<tr class="tb-fila">

<tr >

<td>
    <input name="id" type="hidden" id="id"  value="<?php echo $fila['id'] ?>" maxlength="60" />
<input name="fecha" type="text" id="fecha" style="width:80px" onblur="javascript:cargar_horas();" value="<?php echo $fila['fecha']; ?>" maxlength="60" >
</td>

<td>
<input name="entrada" type="text" id="entrada" style="width:50px" onblur="javascript:cargar_horas();" value="<?php echo $fila['entrada']; ?>" maxlength="60" >
</td>

<td>
<input name="salmuerzo" type="text" id="salmuerzo" style="width:50px" onblur="javascript:cargar_horas();" value="<?php echo $fila['salmuerzo']; ?>" maxlength="60" >
</td>

<td>
<input name="ealmuerzo" type="text" id="ealmuerzo" style="width:50px" onblur="javascript:cargar_horas();" value="<?php echo $fila['ealmuerzo']; ?>" maxlength="60" >
</td>

<td>
<input name="salida" type="text" id="salida" style="width:50px" onblur="javascript:cargar_horas();" value="<?php echo $fila['salida']; ?>" maxlength="60" >
</td>

<td>
<input name="ordinaria" type="text" id="ordinaria" style="width:50px" value="<?php echo $fila['ordinaria']; ?>" maxlength="60" >
</td>

<td>
<input name="tardanza" type="text" id="tardanza" style="width:50px" value="<?php echo $fila['tardanza']; ?>" maxlength="60" >
</td>

<td>
<input name="extra" type="text" id="extra" style="width:50px" value="<?php echo $fila['extra']; ?>" maxlength="60" >
</td>

<td>
<input name="extraext" type="text" id="extraext" style="width:50px" value="<?php echo $fila['extraext']; ?>" maxlength="60" >
</td>

<td>
<input name="extranoc" type="text" id="extranoc" style="width:50px" value="<?php echo $fila['extranoc']; ?>" maxlength="60" >
</td>

<td>
<input name="extraextnoc" type="text" id="extraextnoc" style="width:50px" value="<?php echo $fila['extraextnoc']; ?>" maxlength="60" >
</td>

<td>
<input name="domingo" type="text" id="domingo" style="width:50px" value="<?php echo $fila['domingo']; ?>" maxlength="60" >
</td>

<td>
<input name="nacional" type="text" id="nacional" style="width:50px" value="<?php echo $fila['nacional']; ?>" maxlength="60" >
</td>

<td>
<input name="extranac" type="text" id="extranac" style="width:50px" value="<?php echo $fila['extranac']; ?>" maxlength="60" >
</td>

<td>
<input name="extranocnac" type="text" id="extranocnac" style="width:50px" value="<?php echo $fila['extranocnac']; ?>" maxlength="60" >
</td>
</tr>
           
</table>
<table align=center>
<tr>
<tr class="tb-fila">
<td width="400"><div align="center">
<input type="hidden" name="pagina" id="pagina"  value="<?php echo $pagina?>">
<input type="submit" name="guardar" id="guardar"  value="Guardar">
</div></td>

</tr>

</tr>
</table>
</td>
</tr>
</table>
</div>
</form>
</body>
</html>