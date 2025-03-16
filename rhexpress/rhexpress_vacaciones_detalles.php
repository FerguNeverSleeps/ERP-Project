<?php
//-------------------------------------------------
session_start();
//-------------------------------------------------
require_once "config/rhexpress_config.php";
//-------------------------------------------------
$cod_user = $_SESSION['pos_rhexpress'];
$tipo     = (isset($_GET['tipo'])) ? $_GET['tipo']:"";
$ext = (isset($_GET['tipo'])) ? " AND tipo_justificacion='$tipo'" : "";
//-------------------------------------------------
$sql = "SELECT fecha,tiempo,observacion,dias,horas,minutos FROM dias_incapacidad WHERE cod_user='$cod_user'".$ext;
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
                    <span class="caption-subject font-green bold uppercase">Detalles de Vacaciones <?=$sql?></span>
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
                                <th>Observacion</th>
                                <th>Fecha</th>
                                <th>Tiempo (dias)</th>
                                <th>Fecha Vencimiento </th>
                                <th>Opcion</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php while($fila=mysqli_fetch_array($res1)): ?>
                            <tr>
                                <td><?php echo $fila['observacion']?></td>
                                <td><?php if($fila['fecha']==NULL){echo 0;}else {echo fecFormat($fila['fecha']);} ?></td>
                                <td><?php if($fila['tiempo']==NULL){echo 0;}else {echo $fila['tiempo'];} ?></td>
                                <td><?php if($fila['fecha_vence']==NULL){echo 0;}else {echo fecFormat($fila['fecha_vence']);} ?></td>
                                <td>
                                    <a href="reportes/pdf/rhexpress_reporte_detalles.php?tipo=<? echo $fila['idtipo']   ;?>" class="btn btn-sm blue">
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
<?php $tabla = 1; ?>
<?php include("config/rhexpress_footer.php");
 include("config/end.php"); ?>
