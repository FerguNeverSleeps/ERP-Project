<?php
session_start();
ob_start();
include("../header.php");
include("func_bd.php");
include("../lib/common.php");
date_default_timezone_set('America/Caracas');
?>
<html>
<head>

<script language="JavaScript" type="text/javascript">
function AbrirConstancia(ficha,tipnom,codigo_validacion,codigo)
{
   if(codigo != ''){
       AbrirVentana('../tcpdf/pdf_constancia.php?registro_id='+ficha+'&tipn='+tipnom+'&est=No'+'&const_id='+codigo+'&sv='+codigo_validacion,660,800,0);
      // location.href='../tcpdf/pdf_constancia.php?registro_id='+ficha+'&tipn='+tipnom+'&est=No'+'&const_id='+codigo;
   }
}

function Enviar(){
    if (document.getElementById("validar").checked){
        var r = confirm("¿Esta seguro que desea validar la constancia?");
        if(! (r == true)){
            return false;
        }else{
            document.getElementById("validar").checked = true;
        }
    }else{
        alert('Marque la opcion Si para proceder a validar la constancia');
        return false;
    }
}
</script>
<?php
$conexion = conexion();
$codigo= $_GET['codigo']; // codigo de la constancia

$consulta="SELECT npc.codigo, npc.tipo_constancia, npc.codigo_validacion as codigo_validacion, npc.ficha,
                  DATE_FORMAT(npc.fecha_emision ,'%d-%m-%Y %h:%i %p') as fecha_emision, npc.validada,
                  DATE_FORMAT(npc.fecha_validacion ,'%d-%m-%Y %h:%i %p') as fecha_validacion,
                  ntc.nombre as nombre_constancia, np.cedula, np.apenom, np.tipnom
           FROM   nompersonal_constancias npc, nomtipos_constancia ntc, nompersonal np
           WHERE  npc.tipo_constancia=ntc.codigo AND npc.ficha=np.ficha
           AND    npc.codigo='".$codigo."'";

$result=query($consulta, $conexion);
$fila=fetch_array($result);
$ficha=$fila['ficha'];
$tipnom=$fila['tipnom'];
$tipo_constancia=$fila['tipo_constancia'];
$codigo_validacion=$fila['codigo_validacion'];

if(isset($_POST['enviar'])){
    $codigo=$_POST['codigo'];
    $fecha_validacion = date("Y-m-d H:i:s");

    // Antes de validar tengo que constantar que no exceda el limite mensual establecido
    $sql='SELECT cantidad_mensual, tipo_validacion FROM nomconf_constancia';
    $result=query($sql, $conexion);
    $fila=fetch_array($result);
    $limite=(int) $fila['cantidad_mensual'];
    $tipo_validacion=$fila['tipo_validacion'];

    if($tipo_validacion=='General'){
        // Debo contar cuantas cartas tienes validadas el usuario de cualquier modelo
        $sql="SELECT COUNT(*) as cantidad FROM nompersonal_constancias 
              WHERE ficha=$ficha AND validada='Si' AND MONTH(fecha_validacion) = MONTH(CURDATE())";
        $result=query($sql, $conexion);
        $fila=fetch_array($result);
        $cantidad_validadas=(int) $fila['cantidad'];
    }else{
        // Debo contar cuantas cartas tienes validadas el usuario para este modelo en particular
        $sql="SELECT COUNT(*) as cantidad FROM nompersonal_constancias 
              WHERE ficha=$ficha AND validada='Si' AND MONTH(fecha_validacion) = MONTH(CURDATE()) AND tipo_constancia=".$tipo_constancia;
        $result=query($sql, $conexion);
        $fila=fetch_array($result);
        $cantidad_validadas=(int) $fila['cantidad'];
    }

    if($cantidad_validadas < $limite){
            $consulta="UPDATE nompersonal_constancias SET validada='Si', fecha_validacion='".$fecha_validacion."'
               WHERE codigo='".$codigo."'";
            $result=query($consulta, $conexion);
    }else{
        echo "<script>alert('Ya se valido el numero maximo de constancias para este mes');</script>";
    }

     echo "<script>location.href='datos_constancia.php?codigo='+$codigo</script>";
}
?>
<form method="post" name="frmValidarConstancia" id="frmValidarConstancia">
<input name="codigo" type="hidden" id="codigo" value="<?php echo $codigo; ?>">

<table align="center" width="1100" border="0">
<tbody>
<tr>
    <td height="32" class="tb-tit"><font color="#000066"><strong>Constancia</strong></font></td>

    <td colspan="3" class="tb-tit" align="right"><? echo btn("back","menu_consultas.php")?></td></tr>
<tr><td colspan="3"><br></td></tr>
<?php
?>

<!-- *********************************************** -->
<tr><td colspan="4">
    <table class="dvtContentSpace" style="border-bottom:0px;" border="0" cellpadding="3" cellspacing="0" width="100%">
        <tbody><tr>
            <table class="small" border="0" cellpadding="0" cellspacing="0" width="100%">
                <tbody>
                <tr>
                    <td colspan="2" class="dvInnerHeader">
                        <b>Datos de la Constancia</b>
                    </td>
                </tr>
                </tbody>
            </table>
            <table class="small" border="0" cellpadding="0" cellspacing="0" width="100%">
                <tbody>
                <tr style="height: 25px;">
                    <td class="dvtCellLabel" width="25%">Tipo de constancia</td>
                    <td class="dvtCellInfo" align="left"><?php echo utf8_encode($fila['nombre_constancia']); ?></td>
                </tr>
                <tr style="height: 25px;">
                    <td class="dvtCellLabel" width="25%">No de ficha</td>
                    <td class="dvtCellInfo" align="left"><?php echo $fila['ficha'];?></td>
                </tr>
                <tr style="height: 25px;">
                    <td class="dvtCellLabel" width="25%">Trabajador</td>
                    <td class="dvtCellInfo" align="left"><?php echo utf8_encode($fila['apenom']);?></td>
                </tr>
                <tr style="height: 25px;">
                    <td class="dvtCellLabel" width="25%">Fecha de emisión</td>
                    <td class="dvtCellInfo" align="left"><?php echo $fila['fecha_emision']; ?></td>
                </tr>
                <tr style="height: 25px;">
                    <td class="dvtCellLabel" width="25%">Constancia validada</td>
                    <td class="dvtCellInfo" align="left"><?php echo $fila['validada']; ?></td>
                </tr>

                <?php

                if(isset($fila['validada']) && $fila['validada']=='Si'){?>
                    <tr style="height: 25px;">
                        <td class="dvtCellLabel" width="25%">Fecha de validación</td>
                        <td class="dvtCellInfo" align="left"><?php echo $fila['fecha_validacion']; ?></td>
                    </tr>
                <?php
                }else{?>
                    <tr style="height: 25px;">
                        <td class="dvtCellLabel" width="25%">¿Desea validar la constancia?</td>
                        <td class="dvtCellInfo" align="left"><label><input type="checkbox" id="validar" name="validar" value="Si">Sí</label></td>
                    </tr>
                <?php
                }
                ?>
                </tbody>
            </table>
        </tr>

        </tbody>
    </table>
    </td>
</tr>

<!-- ******************************************************** -->
</tbody>
</table>
<br>
<table align="center" width="1100" border="0" class="">
    <tr>
        <td height="50" align="center" class="tb-tit" >
            <input style="display: none" type="button" onclick="javascript:AbrirConstancia('<?php echo $ficha; ?>', '<?php echo $tipnom; ?>', '<?php echo $codigo_validacion; ?>', '<?php echo $codigo; ?>');" name="Constancia" value="Ver Constancia">
            <?php

            if($fila['validada']=='No'){?>
                <input type="submit" name="enviar" id="enviar" value="Validar Constancia" onclick="javascript: return Enviar();">
            <?php
            }
            ?>
        </td>
    </tr>
</table>

</form>
</body>
</html>