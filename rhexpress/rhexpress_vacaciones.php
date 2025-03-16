<?php
//-------------------------------------------------
session_start();
//-------------------------------------------------
require_once "config/rhexpress_config.php";
$conexion =  mysqli_connect( DB_HOST, DB_USUARIO, DB_CLAVE, $_SESSION['bd'] ) or die( 'No Hay Conexión con el Servidor de Mysql' );
        mysqli_query($conexion, 'SET CHARACTER SET utf8');
//-------------------------------------------------
$useruid = $_SESSION['useruid_rhexpress'];
//-------------------------------------------------
function fecFormat($fecha){
    $date = new DateTime($fecha);
    return $date->format('d-m-Y');
}
//$sql = "SELECT * FROM dias_incapacidad
//INNER JOIN periodos_vacaciones
//ON dias_incapacidad.id=periodos_vacaciones.id_dias_incapacidad
//WHERE dias_incapacidad.usr_uid='$useruid' ORDER BY fecha DESC";
$cedula=$_SESSION['cedula_rhexpress'];
$sql= "select * from periodos_vacaciones WHERE cedula='$cedula' and saldo>0";
$res1 = $conexion->query($sql);
//-------------------------------------------------
include("config/rhexpress_header.php");
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
                                <th>Período</th>
                                <th>Dias Acreditados</th>
                                <th>Dias Disponibles</th>
                                <th>Resueltas</th>
                                <!--                                <th>Fecha</th>-->
                                <th>Observacion</th>
                                <th>Opcion</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($fila=mysqli_fetch_array($res1)):
                                if($fila['saldo']>0)
                                      $diasDisponibles= $fila['saldo'];
                                    else
                                        $diasDisponibles = '&nbsp;';
                                    if($fila['resueltas']==1)
                                        $resueltas = 'SI';
                                    else
                                        $resueltas = 'NO';
                            ?>
                            <tr>
                                <td>
                                <?php
                                  $array_periodo_ini = explode('-',$fila['fini_periodo']);
                                  $array_periodo_fin = explode('-',$fila['ffin_periodo']);
                                  echo $array_periodo_ini[0].'  -  '.$array_periodo_fin[0]; ?>
                                </td>
                                <td>
                                  <?php echo $fila['dias']; ?>
                                </td>
                                <td><?php echo $diasDisponibles; ?>  </td>
                                <td><?php echo $resueltas; ?>  </td>
                                    <!--                                <td><?php if($fila['fecha']==NULL){echo 0;}else {echo fecFormat($fila['fecha']);} ?></td>-->
                                <td><?php echo $fila['descripcion']?></td>
                                <td>
                                    <!--                                    <a href="rhexpress_vacaciones_detalles.php?tipo=<? echo $fila['tipo_justificacion']   ;?>" class="btn btn-sm blue">
                                        <i class="fa fa-file-pdf-o" aria-hidden="true"></i> Detalles
                                    </a>-->
                                    <a href="reportes/pdf/rhexpress_reporte_vacaciones.php?id_periodo_vacacion=<? echo $fila['id_periodo_vacacion']   ;?>" class="btn btn-sm blue">
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
