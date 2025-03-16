<?php
//-------------------------------------------------
session_start();

// error_reporting(E_ALL);
// ini_set("display_errors", 1);
//-------------------------------------------------
require_once "config/rhexpress_config.php";
$conexion =  mysqli_connect(DB_HOST, DB_USUARIO, DB_CLAVE, $_SESSION['bd']) or die('No Hay Conexión con el Servidor de Mysql');
mysqli_query($conexion, 'SET CHARACTER SET utf8');
//-------------------------------------------------
$useruid = $_SESSION['useruid_rhexpress'];
//-------------------------------------------------
function fecFormat($fecha)
{
    $date = new DateTime($fecha);
    return $date->format('d-m-Y');
}
//$sql = "SELECT * FROM dias_incapacidad
//INNER JOIN periodos_vacaciones
//ON dias_incapacidad.id=periodos_vacaciones.id_dias_incapacidad
//WHERE dias_incapacidad.usr_uid='$useruid' ORDER BY fecha DESC";
$cedula = $_SESSION['cedula_rhexpress'];

// Buscamos la persona logeada por cedula
$sql_colaborador = "SELECT * FROM nompersonal WHERE cedula='" . $cedula . "'";
$res_colaborador = $conexion->query($sql_colaborador);
$colaborador = $res_colaborador->fetch_object();

/* $sql = "select * from periodos_vacaciones WHERE cedula='$cedula' and saldo>0";
$res1 = $conexion->query($sql); */

$sql = "SELECT periodo, desoper, ddisfrute, DATE_FORMAT(fecha_venc, '%d/%m/%Y') AS fecha_venc, 												     
        DATE_FORMAT(fechavac, '%d/%m/%Y') AS fechavac, DATE_FORMAT(fechareivac, '%d/%m/%Y') AS fechareivac, estado,
        tipooper, dpagob , saldo_vacaciones,dias_solic_ppagar,saldo_dias_pdisfrutar,	dias_solic_pdisfrutar
FROM nom_progvacaciones 
WHERE ceduda=$cedula'
ORDER BY periodo DESC, tipooper DESC";
$res1 = $conexion->query($sql);
//-------------------------------------------------
include("config/rhexpress_header2.php");
?>
<div class="row">
    <div class="col-md-12">
        <!-- BEGIN SAMPLE TABLE PORTLET-->
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-social-dribbble font-green"></i>
                    <span class="caption-subject font-green bold uppercase">Resumen de Vacaciones</span>
                </div>
                <!--
                <div class="actions">
                    <a class="btn btn-circle btn-icon-only btn-default" href="reportes/pdf/rhexpress_reporte_general.php">
                        <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                    </a>
                </div>
                -->
            </div>
            <div class="portlet-body">
                <div class="table-scrollable">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th class="text-center">A&ntilde;o</th>
                                <th class="text-center">Operaci&oacute;n</th>
                                <th class="text-center">Fecha Inicio</th>
                                <th class="text-center">Fecha Fin</th>
                                <th class="text-center">D&iacute;as Solic. Pagar</th>
                                <th class="text-center">Saldo días Vac</th>
                                <th class="text-center">Días Disfrute Solic.</th>
                                <th class="text-center">Saldo días disfrute</th>
                                <th class="text-center">Estado</th>
                                <th class="text-center"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql2 = "SELECT periodo, desoper, ddisfrute, DATE_FORMAT(fecha_venc, '%d/%m/%Y') AS fecha_venc, 												     
                                        DATE_FORMAT(fechavac, '%d/%m/%Y') AS fechavac, DATE_FORMAT(fechareivac, '%d/%m/%Y') AS fechareivac, estado,
                                        tipooper, dpagob , saldo_vacaciones,dias_solic_ppagar,saldo_dias_pdisfrutar,	dias_solic_pdisfrutar
                                    FROM nom_progvacaciones 
                                    WHERE ficha = '$colaborador->ficha' AND ceduda = '$colaborador->cedula'
                                    ORDER BY periodo DESC, tipooper DESC";
                            $res2 = $conexion->query($sql2);
                            $total = $i = $anio2 = $diasdisfrutados = 0;
                            $sql3 = "SELECT periodo
                                    FROM nom_progvacaciones
                                    WHERE ficha = '$colaborador->ficha' AND ceduda = '$colaborador->cedula' AND estado<>'Pagado'";
                            $res3 = $conexion->query($sql3);
                            $cant =  $res3->num_rows;
                            while ($fila2 = $res2->fetch_assoc()) {
                                $anio = $fila2['periodo'];
                                if ($i == 0) // En vacaciones_persona.php la condición es $i==0
                                    $anio2 = $anio;
                                $anio2 = $anio;
                                $i++;
                            ?>
                                <tr>
                                    <td class="text-center"><?php echo $fila2['periodo']; ?></td>
                                    <td class="text-center"><?php echo $fila2['desoper']; ?></td>
                                    <td class="text-center"><?php echo $fila2['fechavac']; ?></td>
                                    <td class="text-center"><?php echo $fila2['fechareivac']; ?></td>
                                    <td class="text-center"><?php echo ($fila2['dias_solic_ppagar']   != 0) ? $fila2['dias_solic_ppagar']   : '-'; ?></td>
                                    <td class="text-center"><?php echo ($fila2['saldo_vacaciones']  != 0) ? $fila2['saldo_vacaciones']  : '-'; ?></td>
                                    <td class="text-center"><?php echo ($fila2['dias_solic_pdisfrutar']  != 0) ? $fila2['dias_solic_pdisfrutar']  : '-'; ?></td>
                                    <td class="text-center"><?php echo ($fila2['saldo_dias_pdisfrutar']  != 0) ? $fila2['saldo_dias_pdisfrutar']  : '-'; ?></td>
                                    <td class="text-center"><?php echo $fila2['estado']; ?></td>
                                    <td class="text-center">
                                        <?php
                                        if ($fila2['fechavac'] == "00/00/0000" or $fila2['fechareivac'] == "00/00/0000") {
                                            echo " ";
                                        } else {
                                        ?>
                                        <!-- LINK BASE -->
                                        <!-- http://planilla.test/nomina/tcpdf/reportes/resumido_vacaciones.php?anio=2023&ficha=1&cedula=8-807-2013 -->
                                        <a href="<?php echo '../nomina/tcpdf/reportes/resumido_vacaciones_portal.php?anio=' . $anio . '&ficha=' . $colaborador->ficha . '&cedula=' . $colaborador->cedula; ?>" title="Imprimir" target="_blank"><img src="../../includes/imagenes/icons/report.png" alt="Imprimir" width="16" height="16"></a><?php } ?>
                                    </td>
                                </tr>
                                <?php
                                if ($fila2['tipooper'] == "DA")
                                    $total += $fila2['ddisfrute'];            // Días de vacaciones adicionales
                                elseif ($fila2['tipooper'] == "DV") {
                                    $total += $fila2['ddisfrute'];             // Días de vacaciones
                                    $diasdisfrutados += $fila2['dpagob'];
                                }
                            }
                            if ($res2->num_rows <= 0) {
                                ?>
                                <tr>
                                    <td colspan="11" class="text-center">No se encontraron datos</td>
                                </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- END SAMPLE TABLE PORTLET-->
    </div>
</div>

<div class="bd-example">
    <div class="modal fade" id="detalles" tabindex="-10" role="dialog" aria-labelledby="asusenciaLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="TituloDetalles"> </h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <!-- Begin: Demo Datatable 2 -->
                            <div class="portlet light portlet-fit portlet-datatable bordered">
                                <div class="portlet-title">
                                    <div class="caption">
                                        <i class="icon-settings font-dark"></i>
                                        <span class="caption-subject font-dark sbold uppercase">Detalles de Registro</span>
                                    </div>
                                </div>
                                <div class="portlet-body">
                                    <div class="table-container">
                                        <div class="table-actions-wrapper">
                                            <span> </span>
                                        </div>
                                        <table class="table table-striped table-bordered table-hover table-checkable" id="datatable_ajax">
                                            <thead>
                                                <tr>
                                                    <th> Fecha </th>
                                                    <th> Observacion </th>
                                                    <th> Dias </th>
                                                </tr>
                                            </thead>
                                            <tbody> </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <!-- End: Demo Datatable 2 -->
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $tabla = 1; ?>
<?php include("config/rhexpress_footer.php");
include("config/end.php"); ?>