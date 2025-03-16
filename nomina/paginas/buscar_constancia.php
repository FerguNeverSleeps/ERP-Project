<?php
session_start();
ob_start();
include ("../header.php");
include("../lib/common.php");
?>

<?php

$conexion = conexion();

if(isset($_POST['enviar']))
{
    if(!empty($_POST['cedula']) && !empty($_POST['codigo'])){
        $cedula=$_POST['cedula'];
        $codigo_validacion=$_POST['codigo'];

        $consulta = "SELECT npc.codigo as codigo 
                     FROM   nompersonal_constancias npc, nompersonal np
                     WHERE  npc.ficha=np.ficha AND np.cedula='".$cedula."' AND npc.codigo_validacion='".$codigo_validacion."'";

        $resultado = query($consulta, $conexion);

        if($fila = fetch_array($resultado)){
            $codigo=$fila['codigo'];
           // echo "<script>alert('Se encontro la constancia');</script>";
            echo "<script>location.href='datos_constancia.php?codigo='+$codigo</script>";
        }else{
            echo "<script>alert('Constancia no encontrada');</script>";
        }
    }else{
        echo "<script>alert('Por favor, ingrese la cedula de identidad y codigo de la constancia');location.href='buscar_constancia.php';</script>";
    }
}
?>

<table align="center" width="100%" border="0">
    <tbody><tr><td colspan="3" class="tb-tit" align="right"><?echo btn("back","menu_consultas.php")?></td></tr></tbody>
</table>

<form id="form1" name="form1"  method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
    <table width="400" height="100" align="center" border="0" class="row-br">
        <tr>
            <td height="31" class="row-br"><table width="400" border="0">
                    <tr>
                        <td width="400"><div align="left"><font color="#000066"><strong>Buscar constancia</strong></font></div></td>
                    </tr>
                </table></td>
        </tr>
        <tr>
            <td width="200" height="100" align="center" class="ewTableAltRow">
                <table width="400" align="center" border="0">
                    <tr>
                        <td width="120" align="left" valign="middle">Cédula  de identidad:</td>
                        <td width="250" align="left" valign="middle"><input type="text" name="cedula" id="cedula" size="30" ></td>
                    </tr>
                    <tr>
                        <td width="120" align="left" valign="middle">Código constancia:</td>
                        <td width="250" align="left" valign="middle"><input type="text" name="codigo" id="codigo" size="30" ></td>
                    </tr>
                </table>
                <br>
                <table width="400" border="0">
                    <tr>
                        <td width="400"><div align="center"><input type="submit" name="enviar" id="enviar"  value="Enviar"></div></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</form>
</body>
</html>
