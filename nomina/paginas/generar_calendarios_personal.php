<?php
session_start();
ob_start();
?>
<?
require_once '../lib/common.php';
include ("../header.php");
?>

<script type="text/javascript">

    function enviar()
    {
        //document.frmPrincipal.op.value=1;
        var ano = document.form1.ano.value
        document.form1.submit();
       alert("Anio "+ano+" generado con exito!!");
        //document.location.href = "submenu_calendarios.php"
    }

</script>

<?php

function vista_dia($dia, $mes, $ano, $ficha) {

    $conexion = conexion();
    $laborable = "lightgray";
    $nolaborable = "red";
    $mediajornada = "magenta";

    $fecha = $ano . "-" . $mes . "-" . $dia;
    echo $consulta = "INSERT INTO nomcalendarios_personal (cod_empresa,ficha,fecha,dia_fiesta,descripcion_dia_fiesta) VALUES ('".$_SESSION['cod_empresa']."','".$ficha. "','" . $fecha . "','0','')";
    $resultado = query($consulta, $conexion);
}

function vista_calendario($mes, $ano, $ficha) {
//estados

    $fecha_lunes = $ano . "-" . $mes . "-01";

    $num_dias_mes = date("t", strtotime($fecha_lunes));

    $dia_inicio = date("N", strtotime($fecha_lunes));


    $dia = 1;
    //echo "<TR>";
    for ($i = 1; $i < $dia_inicio; $i++) {
        //	echo "<TD></TD>";
    }
    for ($i = 1; $i <= $num_dias_mes; $i++) {
        $marca = 0;
        if ($dia_inicio <= 7) {
            vista_dia($i, $mes, $ano, $ficha);
        } else {
            $marca = 1;
            //echo "</TR><TR>";
            $dia_inicio = 1;
            $i--;
        }
        if ($marca == 0) {
            $dia_inicio++;
        }
    }
    for ($i = $dia_inicio; $i <= 7; $i++) {
        //echo "<TD></TD>";
    }
    //echo "</tr>";
}

if (isset($_POST['ano'])) {
    $ano = $_POST['ano']; //$_GET['ano'];
    $bloques = 4;

    $conexion=conexion();   
    $consulta="SELECT ficha FROM nompersonal WHERE estado='Activo'";
    $resultado=query($consulta,$conexion);
    while($fetch=fetch_array($resultado))
    {
        $ficha = $fetch[ficha];
        $i = 0;
        for ($mes = 1; $mes <= 12; $mes++) {
            //echo "<TD>";
            vista_calendario($mes, $ano, $ficha);
            //echo "</TD>";
            $i++;
            if ($i == $bloques) {
                //	echo "</TR><TR valign=\"top\" align=\"center\">";
                $i = 0;
            }
        }
    }
}
//echo "</tr>";
?>
<form id="form1" name="form1" method="post" action="">
    <table width="807" height="150" border="0" class="row-br">
        <tr>
            <td height="31" class="tb-tit">
                <table width="789" border="0">
                    <tr>
                        <td width="762"><div align="left"><font color="#000066"><strong>Generar calendario</strong></font></div></td>
                        <td width="17"><div align="center"><?php btn('back', 'submenu_calendarios.php') ?></div></td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td width="489" height="150" class="ewTableAltRow">
                <table width="520" border="0">
                    <TR>
                        <TD class="tb-fila" width="200">Seleccione a&#241;o a generar: </TD>
                        <TD>
                            <INPUT type="text" name="ano" id="ano" size="15" maxlength="12" value="<? echo date("Y") ?>">
                        </TD>
                    </TR>
                </table>
            </td>
        </tr>
        <tr><TD>
                <table width="100%" border="0">
                    <tr>
                        <td width="466">
                            <div align="center">
<?php
btn('ok', 'enviar();', 2);
?>
                            </div></td>
                    </tr>
                </table>
            </TD></tr>
    </table>



</form>

</body>
</html>
