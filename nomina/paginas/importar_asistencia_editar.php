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
?>
<style>
.bordes {
    border: 1px dashed black;
}
</style>
<?php
$select = "select capital from nomempresa";
$result = sql_ejecutar($select);
$filaemp = fetch_array($result);
$empresa = $filaemp['capital'];
if($empresa=="pandora")
{
    $titulos = array("Ficha", "Fecha","Entrada","Salida","Entrada1","Salida1","Entrada2","Salida2","Tardanza","Extras","Extras Ext", "Noc", "Noc Ext","Dom","Nac", "ExtNac", "NacNoc");
}
elseif(($empresa=="electron")||($empresa=="zkteco"))
{
    $titulos = array("Ficha", "Fecha","Referencia","Concepto",);

}
else
{
    $titulos = array("Ficha", "Fecha","Referencia","Concepto",);
}

$id = $_GET['id'];
$reg = $_GET['reg'];
$pagina = $_GET['pagina'];



$select = "select * from caa_procesar_asistencia where id='$id'";
$result = sql_ejecutar($select);
$fila = fetch_array($result);

if ($_POST["guardar"]) {
    if ($_POST['ficha'] != "" && $_POST['fecha'] != "" ) {
    		$pagina = $_POST['pagina'];
        $cod = $_POST['id'];
        $idenc = $_POST['reg'];
        $tipo = $_POST['tipo'];

        if($tipo=="agregar")
        {
            $mod = "INSERT INTO caa_procesar_asistencia (id,id_encabezado, fecha, ficha, referencia, concepto) values ('','".$reg."','".$_POST[fecha]."','".$_POST[ficha]."', '".$_POST[referencia]."', '".$_POST[concepto]."')";
        }
        else
        {
            $mod = "update caa_procesar_asistencia set  fecha='" .$_POST['fecha'] . "', referencia='".$_POST['referencia']."', concepto='".$_POST['concepto']."' where id='$cod'";
        }
       $result = sql_ejecutar($mod);
        //echo $mod," ";
        header("Location: importar_asistencia_detalle.php?reg=$reg&pagina=$pagina");
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

if($_GET[tipo]=="agregar")
{
    $select = "select ficha, apenom from nompersonal";
    $result = sql_ejecutar($select);
}
?>
<form action="importar_asistencia_editar.php?reg=<?php echo $reg ?>&id=<?php echo $id ?>" method="post" name="frmAgregar" id="frmAgregar" enctype="multipart/form-data">
    <?php titulo("Editar Control de Acceso", "", "importar_asistencia_detalle.php?reg=$reg&pagina=$pagina", "acceso"); ?>
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
<input name="id" type="hidden" id="id"  value="<?php echo $fila['id'] ?>"/>
<input name="tipo" type="hidden" id="tipo"  value="<?php echo $_GET['tipo'] ?>"/>
<input name="pagina" type="hidden" id="pagina"  value="<?php echo $_GET['pagina'] ?>"/>
<input name="reg" type="hidden" id="reg"  value="<?php echo $_GET['reg'] ?>"/>
<input name="empresa" type="hidden" id="empresa"  value="<?php echo $empresa ?>"  />
<?php
if($_GET[tipo]=="agregar")
{
?>
    <select name="ficha" id="ficha">
    <?php while($fila = fetch_array($result)) {?>
    <option value="<?php echo $fila[ficha]?>"><?php echo $fila[ficha]." -- ".$fila[apenom]?></option>
    <?php }?>
    </select>
<?php
}
else
{
?>
    <input name="ficha" type="text" id="ficha" style="width:50px" value="<?php echo $fila['ficha'] ?>" maxlength="60" >
<?php
}
?>
</td>
<td>
<input name="fecha" type="text" id="fecha" style="width:80px" onblur="javascript:cargar_horas();" value="<?php echo $fila['fecha']; ?>" maxlength="60" >
</td>

<td>
<input name="referencia" type="text" id="referencia" style="width:50px" onblur="javascript:cargar_horas();" value="<?php echo $fila['referencia']; ?>" maxlength="60" >
</td>

<td>
<input name="concepto" type="text" id="concepto" style="width:50px" value="<?php echo $fila['concepto']; ?>" maxlength="60" >
</td>

 </tr>
</tbody>
</table>
<br>

<table width="100%" cellspacing="0" border="0" cellpadding="1" align="center">

<tr>
<tr class="tb-fila">
<td width="400"><div align="center">
<input type="hidden" name="pagina" id="pagina"  value="<?php echo $pagina?>">
<input type="submit" name="guardar" id="guardar"  value="Guardar">
</div></td>

</tr>

</tr>
</table>

</div>
</form>
</body>
</html>