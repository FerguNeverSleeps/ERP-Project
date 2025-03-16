<?php
//-------------------------------------------------
session_start();
//-------------------------------------------------
require_once "config/rhexpress_config.php";
//-------------------------------------------------
$cedula=$_SESSION['cedula_rhexpress'];
// $cod_user = $_SESSION['pos_rhexpress'];
$tipo     = (isset($_GET['tipo'])) ? $_GET['tipo']:"";
$ext = (isset($_GET['tipo'])) ? " AND tipo_justificacion='$tipo'" : "";
//-------------------------------------------------
$sql = "SELECT fecha,tiempo,observacion,dias,horas,minutos FROM dias_incapacidad WHERE cedula='$cedula'".$ext;
$res = $conexion->query($sql);
//-------------------------------------------------
include("config/rhexpress_header.php");
?>
<div class="row">
    <div class="col-md-12">
        <!-- BEGIN EXAMPLE TABLE PORTLET-->
        <div class="portlet box green">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-globe"></i>Row & Column Reordering </div>
                <div class="tools"> </div>
            </div>
            <div class="portlet-body">
                <table class="table table-striped table-bordered table-hover dt-responsive" width="100%" id="sample_3" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Tiempo</th>
                            <th>Observacion</th>
                            <th>Dias</th>
                            <th>horas</th>
                            <th>Minutos</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if ($fila=mysqli_fetch_array($res)): ?>
                        <tr>
                            <td><?php echo $fila['fecha']; ?></td>
                            <td><?php echo ($fila['tiempo']==NULL) ? 0 : $fila['tiempo']; ?></td>
                            <td><?php echo ($fila['observacion']==NULL) ? "" : $fila['observacion']; ?></td>
                            <td><?php echo ($fila['dias']==NULL) ? 0 : $fila['dias']; ?></td>
                            <td><?php echo ($fila['horas']==NULL) ? 0 : $fila['horas']; ?></td>
                            <td><?php echo ($fila['minutos']==NULL) ? 0 : $fila['minutos']; ?></td>
                        </tr>
                    <?php endif ?>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- END EXAMPLE TABLE PORTLET-->
    </div>
</div>
<?php include("config/rhexpress_footer.php");
 include("config/end.php"); ?>
