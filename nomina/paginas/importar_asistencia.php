<?php
session_start();
ob_start();
?>
<?php
require_once '../lib/config.php';
require_once '../lib/common.php';
include ("../header.php");
include ("func_bd.php");
$conexion = conexion();

//echo $conexion;
$url = "importar_asistencia";
$modulo = "Control Asistencia";
$tabla = "caa_encabezado_procesar";
$titulos = array("Cod.","Fecha inicio", "Fecha fin", "Fecha Registro");
$indices = array("id_caa_encabezado","fecha_ini", "fecha_fin", "fecha_reg");

$conexion = conexion();
$cedula = @$_GET['cedula'];
$eliminar = @$_GET['eliminar'];
$tipob = @$_GET['tipo'];
$des = @$_GET['des'];
$pagina = @$_GET['pagina'];
$busqueda = @$_GET['busqueda'];

if ($_GET['vaciar'] == 1) {
  	$vaciar="TRUNCATE TABLE reloj_procesar";
	$query=query($vaciar,$conexion);
   ?>
	<script type="text/javascript">
	alert("Proceso Reiniciado Exitosamente!!");
	</script>
	<?php
}

if ($_GET['listo'] == 1) {
   //echo "<div align='center' style=\"background-color : #84225b; color : #fdfdfd; font-family : 'Arial Black'; font-size : 15px;\">Archivo Procesado Correctamente!</div>";
   ?>
	<script type="text/javascript">
	alert("Archivo cargado Exitosamente!!");
	</script>
	<?php
}

if ($_GET['listo'] == 2) {
    //echo "<div align='center' style=\"background-color : #84225b; color : #fdfdfd; font-family : 'Arial Black'; font-size : 15px;\">Existen registros de entradas y salidas incompletos, Por favor revise!</div>";
    
    ?>
	<script type="text/javascript">
	alert("Existen registros de entradas y salidas incompletos, Por favor revise los registros resaltados en ROJO!");
	</script>
	<?php
}

if ($_GET['listo'] == 3) {
   //echo "<div align='center' style=\"background-color : #84225b; color : #fdfdfd; font-family : 'Arial Black'; font-size : 15px;\">Archivo Procesado Correctamente!</div>";
   ?>
	<script type="text/javascript">
	alert("Archivo Procesado Exitosamente!!");
	</script>
	<?php
}

if (isset($_POST['buscar']) || $tipob != NULL) {
    if (!$tipob) {
        $tipob = $_POST['palabra'];
        $des = $_POST['buscar'];
        $busqueda = $_POST['busqueda'];
    }
    switch ($tipob) {
        case "exacta":
            $consulta = buscar_exacta($tabla, $des, $busqueda);
            break;
        case "todas":
            $consulta = buscar_todas($tabla, $des, $busqueda);
            break;
        case "cualquiera":
            $consulta = buscar_cualquiera($tabla, $des, $busqueda);
            break;
    }
} else {
    //echo "cod: ".$id;
    if ($_GET[accion] == 'eliminar') {
        $var_sql = "delete from " . $tabla . " WHERE id_caa_encabezado ='" . $_GET[id] . "'";
        $rs = query($var_sql, $conexion);
        echo $var_sql," ";
        $var_sql = "delete from caa_procesar_asistencia WHERE id_encabezado ='" . $_GET[id] . "'";
        echo $var_sql," ";
        
        $rs = query($var_sql, $conexion);
    }
    $consulta = "select * from ".$tabla." ORDER BY id_caa_encabezado DESC";
   // echo $consulta;
}
//echo $consulta." este es el valor que muestra ";
$num_paginas = obtener_num_paginas($consulta);
$pagina = obtener_pagina_actual($pagina, $num_paginas);
$resultado = paginacion($pagina, $consulta);

?>
<html>
    <head>
        <title></title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    </head>
    <body>
        <form name="<?php echo $url ?>" action="procesar_control_asistencia" method="post" target="_self">
            <?php //titulo($modulo, "cargar_control_acceso2.php", "menu_transacciones.php", "acceso"); ?>
            <?php titulo_mejorada($modulo, "acceso", "btn('add','cargar_control_acceso3.php');", "menu_transacciones.php"); ?>
            <!--
            <table class="tb-head" width="100%">
              <tr>
                    <td><input type="text" name="buscar" size="20"></td>
                    <TD><SELECT name="busqueda">
                    <option value="concepto">Concepto</option>
                    <option value="cod_trabajador">Cedula</option>
                    </SELECT></TD>
                    <td><? btn('search', $url, 1); ?></td>
                    <td><? btn('show_all', $url . ".php?pagina=" . $pagina); ?></td>
                    <td width="120"><input onclick="javascript:actualizar(this);" checked="true" type="radio" name="palabra" value="exacta">Palabra exacta</td>
                    <td width="140"><input onclick="javascript:actualizar(this);" type="radio" name="palabra" value="todas">Todas las palabras</td>
                    <td width="150"><input onclick="javascript:actualizar(this);" type="radio" name="palabra" value="cualquiera">Cualquier palabra</td>
                    <td colspan="3" width="386"></td>
              </tr>
            </table>
            -->
            <br/>
            <table width="100%" cellspacing="0" border="0" cellpadding="1" align="center">
                <tbody>
                    <tr class="tb-head" >
                        <?php
                        foreach ($titulos as $nombre) {
                            echo "<td><STRONG>$nombre</STRONG></td>";
                        }
                        ?>
                        <td></td><td></td><td></td><td></td><td></td><td></td>
                    </tr>
                    <?php
                    if ($num_paginas != 0) {
                        $i = 0;
                        while ($fila = mysqli_fetch_array($resultado)) {
									$color="";
									$consultass="select id_caa_encabezado from caa_encabezado_procesar where id_caa_encabezado='".$fila[id_caa_encabezado]."'";
									$res1=query($consultass,$conexion);
                                    $coll=0;
                        	if(num_rows($res1)>0)
                            {
                        		$color="style='color:green'";
                                $coll = 1;
                            }
                            $i++;
                            if ($i % 2 == 0) {
                                ?>
                                <tr class="tb-fila" <?php echo $color?>>
                                    <?
                                } else {
                                    echo"<tr $color>";
                                }
                                foreach ($indices as $campo) {
                                    //$nom_tabla=mysqli_fetch_field_direct($resultado, $campo)->name;
                                    switch ($campo) {
                                    	 case 'id_caa_encabezado':
                                            ?>
                                            <td><?php echo $fila[$campo]; ?></td>
                                            <?
                                            break;
                                        case 'fecha_reg':
                                            $fecha = $fila[$campo];
                                            ?>
                                            <td><? echo fecha($fecha); ?></td>
                                            <?
                                            break;
                                        default:
                                            $var = $fila[$campo];
                                            ?>
                                            <td><? echo fecha($var) ?></td>
                                            <?
                                            break;
                                    }
                                }
                                $id = $fila["id_caa_encabezado"];

                                icono("importar_asistencia_detalle.php?reg=" . $id . "&pagina=" . $pagina, "Detalle Control", "add.gif");

                                
                                    icono("javascript:if(confirm('Esta seguro que desea eliminar el registro ?')){document.location.href='importar_asistencia.php?id=" . $id . "&pagina=" . $pagina . "&accion=eliminar';}", "Eliminar Control", "delete.gif");
                               /* else
                                    echo "<td></td>";
                                
                                icono("../tcpdf/reportes/asistencia_personal.php?reg=".$id."&pagina=".$pagina."&fecha_ini=" . $fila['fecha_ini'] . "&fecha_fin=" . $fila['fecha_fin'] . "&fecha_reg=" . $fila['fecha_reg'], "Reporte de asistencia de personal", "ico_print.gif");
                                icono("rpt_horas_trabajadas_xls.php?reg=".$id."&pagina=".$pagina, "Reporte de Horas Trabajas Personal", "../img_sis/ico_excel.gif");
                                icono("rpt_horas_trabajadas_xls2.php?reg=".$id."&pagina=".$pagina, "Ausencias y Tardanzas", "../img_sis/47.png");
 
                                echo"</tr>";*/
                            }
                        } else {
                            echo"<tr><td>No existen registro con la busqueda especificada</td></tr>";
                        }
                        cerrar_conexion($conexion);
                        ?>
                </tbody>
            </table>
            <?php pie_pagina($url, $pagina, "&tipo=" . $tipob . "&des=" . $des . "&busqueda=" . $busqueda, $num_paginas); ?>
        </form>
    </body>

</html>