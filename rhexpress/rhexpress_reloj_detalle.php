<?php
//-------------------------------------------------
session_start();
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
include("config/rhexpress_header2.php");
?>
<div class="row">
    <div class="col-md-12">
        <!-- BEGIN SAMPLE TABLE PORTLET-->
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-social-dribbble font-green"></i>
                    <span class="caption-subject font-green bold uppercase">Marcaciones Reloj</span>
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
                    <table class="table table-hover" id="marcaciones_relojes">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Entrada</th>
                                <th>S. almuerzo</th>
                                <th>E. almuerzo</th>                               
                                <th>Salida</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- END SAMPLE TABLE PORTLET-->
    </div>
</div>

<div class="bd-example">
    <div class="modal fade" id="incidencias" tabindex="-10" role="dialog" aria-labelledby="asusenciaLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="TituloIncidencias"> </h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <!-- Begin: Demo Datatable 2 -->
                            <div class="portlet light portlet-fit portlet-datatable bordered">
                                <div class="portlet-title">
                                    <div class="caption">
                                        <i class="icon-settings font-dark"></i>
                                        <span class="caption-subject font-dark sbold uppercase">Detalles de Incidencias</span>
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
                                                    <th> Incidencia </th>
                                                    <th> Codigo </th>
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
<?php $tabla = 0; ?>
<?php include("config/rhexpress_footer3.php");
?><script src="ajax/js/marcaciones_relojes.js"></script><?php

 include("config/end.php"); ?>
