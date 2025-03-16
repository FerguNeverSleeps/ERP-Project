<?php
//-------------------------------------------------
session_start();
// error_reporting(E_ALL);
// ini_set('display_errors', TRUE);
// ini_set('display_startup_errors', TRUE);
//-------------------------------------------------
require_once "config/rhexpress_config.php";
//-------------------------------------------------
$ficha = $_SESSION['ficha_rhexpress'];
//-------------------------------------------------
$sql = "SELECT a.*,b.descripcion
        FROM reloj_detalle AS a
        LEFT JOIN nompersonal AS c ON a.ficha = c.ficha
        WHERE a.ficha='$ficha'
        ORDER BY a.fecha ASC";
$res1 = $conexion->query($sql);
//-------------------------------------------------
$consulta = " SELECT 
                rd.ficha AS ficha,
                rd.fecha AS fecha,
                rd.entrada AS entrada,
                rd.salmuerzo AS salmuerzo,
                rd.ealmuerzo AS ealmuerzo,
                rd.salida AS salida,
                rd.tardanza AS tardanza
            FROM reloj_detalle rd 
            WHERE rd.ficha = $ficha;";

$resultAusTard = $conexion->query($consulta);
//-------------------------------------------------
include("config/rhexpress_header2.php");

?>

<div class="row">
    <div class="col-md-12">
        <div class="portlet box blue">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-clock"></i>Marcaciones y Ausencias
                </div>
            </div>
            <div class="portlet-body">
                <div class="table-responsive">
                    <table class="table table-hover" style="font-size:11px" id="ausencia_marcacion">
                        <thead>
                            <tr>
                                <th>
                                    Ficha
                                </th>
                                <th>
                                    Fecha
                                </th>
                                <th>
                                    Entrada
                                </th>
                                <th>
                                    S. Almuerzo
                                </th>
                                <th>
                                    E. Almuerzo
                                </th>
                                <th>
                                    Salida
                                </th>
                                <th>
                                    Ausencia
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($fila = $resultAusTard->fetch_assoc()) {
                                /* $fecha_entrada1 = new Datetime($fila['entrada']);
                                $fecha_entrada_rd1 = new Datetime($fila['entrada_rd']);
                                $fecha_tolerancia_entrada1 = new Datetime($fila['tolerancia_entrada']);
                                $intervalo = $fecha_tolerancia_entrada1->diff($fecha_entrada_rd1);
 */
                            ?>
                                <tr>
                                    <td>
                                        <?php echo utf8_encode($fila['ficha']) ?>
                                    </td>
                                    <td>
                                        <?php echo utf8_encode($fila['fecha']) ?>
                                    </td>
                                    <td>
                                        <?php echo utf8_encode($fila['entrada']) ?>
                                    </td>
                                    <td>
                                        <?php echo utf8_encode($fila['salmuerzo']) ?>
                                    </td>
                                    <td>
                                        <?php echo utf8_encode($fila['ealmuerzo']) ?>
                                    </td>
                                    <td>
                                        <?php echo utf8_encode($fila['salida']) ?>
                                    </td>
                                    <td>
                                        <?php echo utf8_encode($fila['tardanza']) ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
$tabla = 0;
include("config/rhexpress_footer3.php");
?><script src="ajax/js/ausencia_marcacion.js"></script><?php
include("config/end.php"); ?>