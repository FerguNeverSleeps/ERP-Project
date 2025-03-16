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

$select = "select capital from nomempresa";
$result = sql_ejecutar($select);
$filaemp = fetch_array($result);
$empresa = $filaemp['capital'];
if($empresa=="pandora")
{
    $titulos = array("Ficha", "Fecha","Entrada","Salida","Entrada1","Salida1","Entrada2","Salida2","Ordinarias","Tardanza","Extras","Extras Ext", "Noc", "Noc Ext","Dom","Nac", "ExtNac", "NacNoc");
}
else
{
    $titulos = array("Ficha", "Fecha","Entrada","S. Almu","E. Almu","Salida","Ordinarias","Tardanza","Desc. Ext.","Extras","Extras Ext", "Noc", "Noc Ext","Dom","Nac", "ExtNac", "NacNoc");
}

$id = $_GET['id'];
$reg = $_GET['reg'];
$pagina = $_GET['pagina'];



$select = "select * from reloj_detalle where id=" . $id;
$result = sql_ejecutar($select);
$fila = fetch_array($result);

if ($_POST["guardar"]) {
    if ($_POST['ficha'] != "" && $_POST['fecha'] != "" ) {
    		$pagina = $_POST['pagina'];
        $cod = $_POST['id'];
        $mod = "update reloj_detalle set  fecha='" .$_POST['fecha'] . "', entrada='".$_POST['entrada']."', salmuerzo='".$_POST['salmuerzo']."', ealmuerzo='".$_POST['ealmuerzo']."', salida='".$_POST['salida']."', ordinaria='".$_POST['ordinaria']."', extra='".$_POST['extra']."', domingo='".$_POST['domingo']."', tardanza='".$_POST['tardanza']."', extraext='".$_POST['extraext']."', extranoc='".$_POST['extranoc']."', extraextnoc='".$_POST['extraextnoc']."', nacional= '".$_POST['nacional']."', extranac = '".$_POST['extranac']."', extranocnac = '".$_POST['extranocnac']."', descextra1 = '".$_POST['descextra1']."'    where id=" . $cod;
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

$select = "select apenom from nompersonal where ficha='".$fila[ficha]."'";
$result2 = sql_ejecutar($select);
$fila2 = fetch_array($result2);
?>
<form action="control_acceso_editar2.php?reg=<?php echo $reg ?>&id=<?php echo $id ?>" method="post" name="frmAgregar" id="frmAgregar" enctype="multipart/form-data">
    <?php titulo("Editar Control de Acceso", "", "control_acceso_detalle2.php?reg=$reg&pagina=$pagina", "acceso"); ?>
    <br><br><br><br><br><br><br><br>
    <div id="content">


<table width="100%" cellspacing="0" border="0" cellpadding="1" align="left">
<tbody>
<tr class="tb-head" >
<td>Empleado: 
<?php
echo $fila2[apenom];
?>
</td>
</tr>
</tbody>
</table>


<table width="100%" cellspacing="0" border="0" cellpadding="1" align="center">
<tbody>
<tr class="tb-head" >
<?php
foreach ($titulos as $nombre) {
 echo "<td><STRONG>$nombre</STRONG></td>";
}
?>
</tr>
         
<tr class="tb-fila">
<td>
<input name="id" type="hidden" id="id"  value="<?php echo $fila['id'] ?>" maxlength="60" />
<input name="empresa" type="hidden" id="empresa"  value="<?php echo $empresa ?>"  />
<input name="ficha" type="text" id="ficha" style="width:50px" value="<?= $fila['ficha'] ?>" maxlength="60" >
</td>
<td>
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

<?php
if($empresa=="pandora")
{
?>
<td>
<input name="entrada1" type="text" id="entrada1" style="width:50px" onblur="javascript:cargar_horas();" value="<?php echo $fila['entrada1']; ?>" maxlength="60" >
</td>

<td>
<input name="salida1" type="text" id="salida1" style="width:50px" onblur="javascript:cargar_horas();" value="<?php echo $fila['salida1']; ?>" maxlength="60" >
</td>
<?php
}
?>

<td>
<input name="ordinaria" type="text" id="ordinaria" style="width:50px" value="<?php echo $fila['ordinaria']; ?>" maxlength="60" >
</td>

<td>
<input name="tardanza" type="text" id="tardanza" style="width:50px" value="<?php echo $fila['tardanza']; ?>" maxlength="60" >
</td>

<?php
if($empresa=="jimmy")
{
?>
<td>
<input name="descextra1" type="text" id="descextra1" style="width:50px" value="<?php echo $fila['descextra1']; ?>" maxlength="60" >
</td>
<?php
}
?>


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

<td colspan="13" align="center">
Estado
<input name="estado" type="text" id="estado" style="width:150px" value="" maxlength="60" >
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