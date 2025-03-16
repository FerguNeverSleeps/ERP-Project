<?php
//-------------------------------------------------
session_start();
//-------------------------------------------------
require_once "config/rhexpress_config.php";
$conexion =  mysqli_connect( DB_HOST, DB_USUARIO, DB_CLAVE, $_SESSION['bd'] ) or die( 'No Hay Conexión con el Servidor de Mysql' );
        mysqli_query($conexion, 'SET CHARACTER SET utf8');
//-------------------------------------------------
$cedula=$_SESSION['cedula_rhexpress'];
 $sql = "SELECT b.descripcion as justificacion, b.idtipo as idtipo, SUM(a.tiempo) as tiempo, SUM(a.horas) as horas, "
         . "SUM(a.minutos) as minutos , SUM(a.dias) as dias,a.id FROM dias_incapacidad as a, tipo_justificacion as b "
         . "WHERE a.tipo_justificacion=b.idtipo AND a.cedula='$cedula' GROUP BY a.tipo_justificacion";
// $sql="SELECT b.descripcion as justificacion, b.idtipo as idtipo, SUM(a.tiempo) as tiempo, SUM(a.horas) as horas, SUM(a.minutos) as minutos , SUM(a.dias) as dias,a.id FROM dias_incapacidad as a, tipo_justificacion as b 
// WHERE a.tipo_justificacion=b.idtipo AND a.cedula='$cedula' AND b.idtipo='7'";
$res1 = $conexion->query($sql);
//-------------------------------------------------
// $sql2="SELECT tiempo FROM dias_incapacidad WHERE cedula='$cedula' and observacion='SALDO INICIAL'";
// $res = $conexion->query($sql2)->fetch_assoc();
// $total=$res['tiempo'];
include("config/rhexpress_header2.php");
?>
<div class="row">
    <div class="col-md-12">
        <!-- BEGIN SAMPLE TABLE PORTLET-->
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-social-dribbble font-green"></i>
                    <span class="caption-subject font-green bold uppercase">Resumen de tiempos</span>
                </div>
                <div class="actions">
                    <a class="btn btn-circle btn-icon-only btn-default" href="reportes/pdf/rhexpress_reporte_general.php">
                        <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                    </a>
                </div>
            </div>
            <div class="portlet-body">
                <div class="table-scrollable">
                    <table class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Tipo</th>
                                <th>Tiempo</th>
<!--                                <th>Horas</th>
                                <th>Minutos</th>-->
                                <th>Opcion</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php while($fila=mysqli_fetch_array($res1)): ?>
                            <tr>
                                <td><?php echo $fila['justificacion']?></td>
                                <td><?php if($fila['tiempo']==NULL){echo 0;}else {echo number_format($fila['tiempo'], 2, ",", ".");} if($fila['justificacion']=="VACACIONES"){echo " (Dias)";} else {echo " (Horas)";} ?></td>
                                <td>
                                    <button type="button" id="botdetalles" class="btn btn-sm blue" data-toggle="modal" data-target="#detalles" data-idtipo="<?php echo $fila['idtipo'];?>" data-justificacion="<?php echo $fila['justificacion'];?>">
                                    <i class="fa fa-clock-o" aria-hidden="true"></i> Detalles
                                    </button>
                                    <a href="reportes/pdf/rhexpress_reporte_tiempos.php?tipo=<? echo $fila['idtipo'];?>" class="btn btn-sm blue">
                                        <i class="fa fa-file-pdf-o" aria-hidden="true"></i> Imprimir
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile ?>
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
                                                    <th> Dias   &nbsp;&nbsp; </th>
                                                    <th> Horas </th>
                                                    <th> Minutos </th>
                                                    <th> Fecha </th>
                                                    <th> Observacion </th>

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
