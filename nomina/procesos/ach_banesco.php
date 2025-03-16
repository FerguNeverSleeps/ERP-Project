<?php 
session_start();
ob_start();
$termino=$_SESSION['termino'];
?>
<?
include "../lib/common.php";
include "../header.php";
$conexion=conexion();

$opcion=1;
?>

<script>
function direccionar_txt()
{
    var opcion=document.getElementById('opcion')
    if(opcion.value==1)
        document.location.href="txt_ach_banesco.php?codigo_nomina="+document.form1.cboTipoNomina.value
        
}
</script>
<form id="form1" name="form1" method="post" action="">
<?titulo_mejorada("Parametros","","","")?>

<?
if(($opcion==1))
{
?>
    <table width="100%" height="229" border="0">
    <tr>
    <td width="489" height="190" ><table width="520" border="0">
    <tr>
    <td width="467" height="40" colspan="4" align="center" valign="middle"><div align="left"><?echo $termino?>:<font size="2" face="Arial, Helvetica, sans-serif">
    <input type="hidden" name="opcion" id="opcion" value="<?php echo $opcion;?>">
    <select name="cboTipoNomina" id="select2" style="width:400px">
    <option>Seleccione una <?echo $termino?></option>
    <?php
    $query="select codnom,descrip,codtip from nom_nominas_pago where codtip='".$_SESSION['codigo_nomina']."' and status='C'";
    $result=query($query,$conexion);
    //ciclo para mostrar los datos
    while ($row = fetch_array($result))
    {       
    // Opcion de modificar, se selecciona la situacion del registro a modificar             
    ?>
    <option value="<?php echo $row['codnom'];?>"><?php echo $row['descrip'];?></option>
    <?
    }//fin del ciclo while
    ?>      
    </select>
    <input type="hidden" name="codt" id="codt" value="<? echo $fila['codtip']; ?>" >
    </font></div></td>
    </tr>
    </table>
<?
}

?>
<p>&nbsp;</p>
<table width="467" border="0">
<tr>
<td width="466"><div align="right">
<?btn('ok','direccionar_txt();',2); ?>
</div></td>
</tr>
</table>
</td>
</tr>
</table>
</form>

