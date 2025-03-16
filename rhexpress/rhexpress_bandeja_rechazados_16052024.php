<?php
//-------------------------------------------------
session_start();
//-------------------------------------------------
require_once "config/rhexpress_config.php";
$conexion =  mysqli_connect( DB_HOST, DB_USUARIO, DB_CLAVE, $_SESSION['bd'] ) or die( 'No Hay Conexión con el Servidor de Mysql' );
        mysqli_query($conexion, 'SET CHARACTER SET utf8');
//-------------------------------------------------
$cedula      = $_SESSION['cedula_rhexpress'];
$sql_jefe    = "SELECT IdJefe,uid_subjefe,IdDepartamento FROM departamento where IdJefe = '{$cedula}' OR uid_subjefe = '{$cedula}'";
$sql_otrodep = "SELECT id_jefe,idDepartamento,cedula_funcionario FROM departamento_funcionario where id_jefe = '{$cedula}' OR uid_subjefe = '{$cedula}'";
$esjefe      = $conexion -> query($sql_jefe) -> num_rows;
$jefes       = $conexion -> query($sql_jefe) -> fetch_assoc();
/*$es_otdep    = $conexion -> query($sql_otrodep) -> num_rows;
$jefes_otdep = $conexion -> query($sql_otrodep) -> fetch_assoc();*/
$res2 = $conexion->query($sql_jefe);
$valores_departamentos="";

while ($filax=mysqli_fetch_array($res2)) {
    //     # code...
    $valores_departamentos=$valores_departamentos.$filax['IdDepartamento'].",";
}
$valores_departamentos=trim($valores_departamentos,",");

if ($esjefe) 
{
    if ($jefes['IdJefe'] == $cedula) 
    {
        // "es jefe departamento";
            $sql = "SELECT * 
            FROM solicitudes_casos as sc 
            LEFt JOIN solicitudes_tipos as st on (sc.id_tipo_caso = st.id_solicitudes_tipos)
            LEFT JOIN solicitudes_estatus as se on (sc.id_solicitudes_casos_status = se.id_solicitudes_estatus)
            LEFT JOIN nompersonal as np on (sc.cedula = np.cedula)
            LEFT JOIN departamento as dep on (dep.IdDepartamento = np.IdDepartamento)
            WHERE se.id_solicitudes_estatus in (3,5) AND dep.IdDepartamento IN ($valores_departamentos)
            ORDER BY sc.id_solicitudes_casos  DESC";

        
    }
    elseif ($jefes['uid_subjefe'] == $cedula)
    {
        // "es director";
        $sql = "SELECT * 
            FROM solicitudes_casos as sc 
            RIGHT JOIN solicitudes_tipos as st on (sc.id_tipo_caso = st.id_solicitudes_tipos)
            RIGHT JOIN solicitudes_estatus as se on (sc.id_solicitudes_casos_status = se.id_solicitudes_estatus)
            RIGHT JOIN nompersonal as np on (sc.cedula = np.cedula)
            RIGHT JOIN departamento as dep on (np.jefe = dep.IdJefe)
            WHERE se.id_solicitudes_estatus in (3,5)
            ORDER BY sc.id_solicitudes_casos  DESC;";
    }

    
}
else
{
    $sql = "SELECT * 
    FROM solicitudes_casos as sc 
    LEFt JOIN solicitudes_tipos as st on (sc.id_tipo_caso = st.id_solicitudes_tipos)
    LEFT JOIN solicitudes_estatus as se on (sc.id_solicitudes_casos_status = se.id_solicitudes_estatus)
    LEFT JOIN nompersonal as np on (sc.cedula = np.cedula)
    LEFT JOIN departamento as dep on (dep.IdDepartamento = np.IdDepartamento)
    WHERE se.id_solicitudes_estatus in (3,5) AND np.cedula = '{$cedula}'
    
    ORDER BY sc.id_solicitudes_casos  DESC";
}
if ($esDirector) 
{
    # code...
}
//echo $esjefe," ",$sql;
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
                    <i class="icon-social-dribbble font-blue"></i>
                    <span class="caption-subject font-blue bold uppercase">CASOS RECHAZADOS</span>
                </div>
                <!-- <div class="actions">
                    <a class="btn btn-circle btn-icon-only btn-default" href="reportes/pdf/rhexpress_reporte_general.php">
                        <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                    </a>
                </div> -->
            </div>
            <div class="portlet-body">
                <div class="table-scrollable">
                    <table class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Colaborador</th>
                                <th>Cédula</th>
                                <th>Nombres y Apellidos</th>
                                <th>Departamentos</th>
                                <th>Tipo de Caso</th>
                                <th>Fecha</th>
                                <th>Estatus</th>
                                <th></th>
                                <th></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php while($fila=mysqli_fetch_array($res1)): ?>
                            <tr>
                                <td><?php echo $fila['id_solicitudes_casos']?></td>
                                <td><?php echo $fila['ficha']?></td>
                                <td><?php echo $fila['cedula']?></td>
                                <td><?php echo $fila['apenom']?></td>
                                <td><?php echo $fila['Descripcion']?></td>
                                <td><?php echo $fila['descrip_solicitudes_tipos']?></td>
                                <td><?php echo $fila['fecha_registro']?></td>
                                <td><?php echo $fila['descrip_solicitudes_estatus']?></td>
                                <td><i class="fa fa-file-pdf-o" aria-hidden="true"></i></td>
                                <td><i class="fa fa-edit" aria-hidden="true"></i></td>
                                <td><i class="fa fa-ban" aria-hidden="true"></i></td>
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
