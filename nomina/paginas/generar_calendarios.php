<?php
session_start();
ob_start();
?>
<?
require_once '../lib/common.php';
include ("../header.php");

$conexion = conexion();

 if($_POST['POSTEO'] == 'SI')
 { 






 $ano = $_POST['ano'] ;   
 $aux = 0 ;
$consultay = "SELECT DISTINCT(YEAR(fecha)) as ano FROM nomcalendarios_tiposnomina WHERE cod_tiponomina=".$_SESSION['codigo_nomina']."";
$resultd = query($consultay,$conexion);
while($fetchv = fetch_array($resultd))
{
 if($ano == $fetchv[0]) $aux = 1 ; 
}

if($aux == 0) 
{ 
 $consulta = "INSERT INTO nomcalendarios_tiposnomina (cod_empresa,cod_tiponomina,fecha,dia_fiesta,descripcion_dia_fiesta,turno_id) VALUES" ;
 for($i=1;$i<=12;$i++)
 { 
  $ultimodia = date("d",(mktime(0,0,0,$i+1,1,$ano)-1));
    for($j=1;$j <= $ultimodia; $j++ )
    { 
        $fecha = $ano . "-" . $i . "-" . $j;   
        $consulta  .=  "('" . $_SESSION['cod_empresa'] . "','" . $_SESSION['codigo_nomina'] . "','" . $fecha . "','','','1'),";
    }

 }
$consulta .= '****';
$consulta = str_replace(',****', ';', $consulta)  ;
$resultado = query($consulta, $conexion);
echo "<script>alert('generado con exito!!')</script>" ; 
header("Location: submenu_calendarios.php") ;

}
else
{

echo "<script>alert('Estes años ya fue generado')</script>" ; 

}



  



 

}
?>

<script type="text/javascript">

    function enviar()
    {
        //document.frmPrincipal.op.value=1;
        var ano = document.form1.ano.value
        document.form1.submit();
      //  alert("Anio "+ano+" generado con exito!!");
      //  document.location.href = "submenu_calendarios.php"
    }

</script>

<?php

function vista_dia($dia, $mes, $ano) {

    $conexion = conexion();
    $laborable = "lightgray";
    $nolaborable = "red";
    $mediajornada = "magenta";

    $fecha = $ano . "-" . $mes . "-" . $dia;
    $consulta = "INSERT INTO nomcalendarios_tiposnomina (cod_empresa,cod_tiponomina,fecha,dia_fiesta,descripcion_dia_fiesta) VALUES ('" . $_SESSION['cod_empresa'] . "','" . $_SESSION['codigo_nomina'] . "','" . $fecha . "','','')";
    $resultado = query($consulta, $conexion);
    /*
      $fila=fetch_array($resultado);
      $color=$laborable;
      if($fila['dia_fiesta']=="1")
      {
      $color=$nolaborable;
      }
      elseif($fila['dia_fiesta']=="2")
      {
      $color=$mediajornada;
      }
     */
    //echo "<td align=\"center\" style=\"cursor : pointer; font-size : 12pt;\" title=\"".utf8_encode($fila['descripcion_dia_fiesta'])."\" bgcolor=\"$color\">$dia</td>";
}

function vista_calendario($mes, $ano) {
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
            vista_dia($i, $mes, $ano);
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

/*if (isset($_POST['ano'])) {
    $ano = $_POST['ano']; //$_GET['ano'];
    $bloques = 4;


    $i = 0;


    for ($mes = 1; $mes <= 12; $mes++) {
 
        vista_calendario($mes, $ano);
 
        $i++;
        if ($i == $bloques) {
             
            $i = 0;
        }
    }
}*/
//echo "</tr>";
?>
<form id="form1" name="form1" method="post" action="">
    <input type="hidden" name="POSTEO" value="SI">
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
