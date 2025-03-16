<?php
error_reporting(E_ALL ^ E_DEPRECATED);
require_once "config/db.php";
$conexion =  mysqli_connect( $params['host'], $params['user'], $params['password'], $params['dbname'] ) or
        die( 'Could not open connection to server' );
mysqli_query($conexion, 'SET CHARACTER SET utf8');
//--------------------------------------------------------
$nivel1 = (isset($_GET['region'])) ? $_GET['region'] : $_SESSION['region'];
$nivel2 = (isset($_GET['dpto'])) ? $_GET['dpto'] : $_SESSION['departamento'];
//--------------------------------------------------------
$res = $conexion->query("SELECT codorg,descrip FROM nomnivel1 WHERE codorg = '".$_SESSION['region']."'") or die(mysqli_error($conexion));
$gerencia = mysqli_fetch_array($res);
//--------------------------------------------------------
$dep = $conexion->query("SELECT codorg,descrip FROM nomnivel2 WHERE gerencia = '".$nivel1."' ORDER BY codorg ASC") or die(mysqli_error($conexion));
//--------------------------------------------------------
if ($nivel2 != "0") {
    $empleado = $conexion->query("SELECT * FROM nompersonal WHERE codnivel1 = '".$nivel1."' AND codnivel2 = '".$nivel2."' AND estado != 'Egresado' AND estado != 'De Baja' AND marca_reloj = 0 ORDER BY ficha ASC") or die(mysqli_error($conexion));
}else{
    $empleado = $conexion->query("SELECT * FROM nompersonal WHERE codnivel1 = '".$nivel1."' AND estado != 'Egresado' AND estado != 'De Baja' AND marca_reloj = 0 ORDER BY ficha ASC") or die(mysqli_error($conexion));
}
//--------------------------------------------------------
$res = $conexion->query("SELECT MIN(fecha) as fecini,MAX(fecha) AS fecfin FROM caa_resumen WHERE estado = 0") or die(mysqli_error($conexion));
$fec_reg = mysqli_fetch_array($res);
$fecini  =$fec_reg['fecini'];
$fecfin  =$fec_reg['fecfin'];
?>
<!DOCTYPE html>
<html lang="en" class="no-js">
<head>
    <?php include("../../../includes/dependencias.php");?>
</head>
<body style="background-color: #ccc;">
<div class="container">
<br>
<br>
    <!-- COMIENZO DEL CONTENIDO-->
    <div class="row">
        <div class="col-md-12">
            <!-- COMIENZO DE LA TABLA-->
            <div class="portlet box blue">
                <div class="portlet-title">
                    <div class="caption">
                        <img src="../../../includes/imagenes/21.png" width="22" height="22" class="icon"> Lista de Empleados que no marcan
                    </div>
                    <div class="actions">
                    </div>
                </div>
                <div class="portlet-body">
                    <table class="table table-striped table-bordered table-hover" id="table_datatable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nombre</th>
                                <th>Cedula</th>
                                <th>Ficha</th>
                                <th>Posicion</th>
                                <th>Opciones</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php $i=0;while ($fila = mysqli_fetch_array($empleado)){ ?>
                        	<tr>
                                <td><?php echo $i+1; ?></td>
                                <td><?php echo $fila['apenom']; ?></td>
                                <td><?php echo $fila['cedula']; ?></td>
                                <td><?php echo $fila['ficha']; ?></td>
                                <td><?php echo $fila['nomposicion_id']; ?></td>
                                <td>
                                    <a class="btn btn-primary" href="add_asistencias_manual.php?ficha=<?php echo $fila['ficha'] ?>" title="Ver resumen de asistencias">
                                    <i class="fa fa-thumbs-o-up" aria-hidden="true"></i>
                                    </a>
            					</td>
                            </tr>
                        <?php $i++;}?>
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- FIN DE LA TABLA-->
        </div>
    </div>
    <!-- FIN DE LA PAGINA DE CONTENIDO-->
</div>
<!-- FIn de Ventana modal deexportacion -->

<script>
    var TableDatatablesRowreorder = function () {
            var table = $('#table_datatable');
            
            var oTable = table.dataTable({

                "processing": true,
                "paging":   true,
                "ordering": true,
                "searching": true,
                // Internationalisation. For more info refer to http://datatables.net/manual/i18n
                "language": {
                    "aria": {
                        "sortAscending": ": activate to sort column ascending",
                        "sortDescending": ": activate to sort column descending"
                    },
                    //"emptyTable": "No data available in table",
                    "emptyTable": "No hay datos disponibles en la tabla",
                    //"info": "Showing _START_ to _END_ of _TOTAL_ entries",
                    "info": "Mostrando _START_ de _END_ de  _TOTAL_ registros",
                    //"infoEmpty": "No entries found",
                    "infoEmpty": "No se encontraron registros",
                    //"infoFiltered": "(filtered1 from _MAX_ total entries)",
                    "infoFiltered": "(filtrados de _MAX_ total registros)",
                    //"lengthMenu": "_MENU_ entries",
                    "lengthMenu": "Mostrando _MENU_ entradas",
                    //"search": "Search:",
                    "search": "<img src='../dependencias/imagenes/icons/magnifier.png' width='16' height='16' > Buscar:",
                    //"zeroRecords": "No matching records found"
                    "zeroRecords": "No se encontraron registros"
                },

                // setup colreorder extension: http://datatables.net/extensions/colreorder/
                colReorder: {
                    reorderCallback: function () {
                        console.log( 'callback' );
                    }
                },

                // setup rowreorder extension: http://datatables.net/extensions/rowreorder/
                rowReorder: {

                },

                "order": [
                    [0, 'asc']
                ],
                
                "lengthMenu": [
                    [5, 10, 15, 20, -1],
                    [5, 10, 15, 20, "Todos"] // change per page values here
                ],
                // set the initial value
                "pageLength": 10,
                "serverSide": false,
                "ajax": {
                        "url": "../ajax/server_side/empleados.php" // ajax source
                    }
            });

        return {

            //main function to initiate the module
            init: function () {

                if (!jQuery().dataTable) {
                    return;
                }
            }

        };

    }();

    jQuery(document).ready(function() {
        TableDatatablesRowreorder.init();
    });
    </script>

    <div class="bd-example">
        <div class="modal fade" id="exampleModal1" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="exampleModalLabel">Aprobar los Registro</h4>
                    </div>
                <div class="modal-body">
                    <form>
                        <div class="form-group">
                            <label for="fecini" class="form-control-label">Fecha Inicial:</label>
                            <input type="text" class="form-control" id="fecini" readonly>
                        </div>
                        <div class="form-group">
                            <label for="fecfin" class="form-control-label">Fecha Final:</label>
                            <input type="text" class="form-control" id="fecfin" readonly>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <a href="utils/aprobar_asistencia.php?tipo=3" class="btn btn-sm blue">
                        Aprobar Registros
                    </a>
                </div>
              </div>
            </div>
        </div>
    </div>

    <div class="bd-example">
        <div class="modal fade" id="exampleModal2" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="exampleModalLabel"></h4>
                    </div>
                <div class="modal-body">
                    <form>
                        <div class="form-group">
                            <label class="control-label">Fecha Inicial:</label>
                            <div class="input-group date" id="datetimepicker1">
                                <input type="text" id="fecinicial" name="fecinicial" class="form-control input-sm" />
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>
                            <input type="hidden" id="ficha">
                        </div>
                        <div class="form-group">
                            <label class="control-label">Fecha Final:</label>
                            <div class="input-group date" id="datetimepicker0">
                                <input type="text" id="fecfinal" name="fecfinal" class="form-control input-sm" />
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-sm blue" id="generar" data-dismiss="modal">Generar Reporte</button>
                </div>
              </div>
            </div>
        </div>
    </div>

    <div class="bd-example">
        <div class="modal fade" id="filtral" tabindex="-1" role="dialog" aria-labelledby="AprobarLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="AprobarLabel">Filtar los Registro</h4>
                    </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="region" class="form-control-label">Gerencia:</label>
                        <input type="text" class="form-control" id="region" value="<?php echo $gerencia['descrip'] ?>" readonly>
                        <input type="hidden" class="form-control" id="codregion" value="<?php echo $gerencia['codorg'] ?>">
                    </div>
                    <div class="form-group">
                        <label for="fecini" class="form-control-label">Departamentos:</label>
                        <select name="dpto" id="dpto" class="form-control">
                            <option value="0" selected>Seleccione</option>
                            <?php while ($dpto = mysqli_fetch_array($dep)): ?>
                                <option value="<?php echo $dpto['codorg'] ?>"><?php echo $dpto['descrip'] ?></option>
                            <?php endwhile ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-sm blue" data-dismiss="modal" id="filtro">Filtrar Registros</button>
                </div>
              </div>
            </div>
        </div>
    </div>

    <script src="../../../includes/assets/plugins/moment/min/moment-with-locales.js"></script>
    <script src="../../../includes/assets/plugins/bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>
    <script src="../../../includes/assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
    <script>
    $(document).ready(function(){
        $(function () {
            $('#datetimepicker0').datetimepicker({
                format: 'DD-MM-YYYY'
            });
            $('#datetimepicker1').datetimepicker({
                format: 'DD-MM-YYYY'
            });
        });
        $('#filtral').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget)
            var region = button.data('region')
            var modal  = $(this)
            modal.find('.modal-body #region').val(region)
        });
        $('#exampleModal1').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget)
            var fecini = button.data('fecini')
            var fecfin = button.data('fecfin')
            var modal  = $(this)
            modal.find('.modal-title').text('Aprobar todos los Registro')
            modal.find('.modal-body #fecini').val(fecini)
            modal.find('.modal-body #fecfin').val(fecfin)
        });
        $('#exampleModal2').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget)
            var ficha = button.data('ficha')
            var modal  = $(this)
            modal.find('.modal-title').text('Reporte De Asistencias')
            modal.find('.modal-body #ficha').val(ficha)
        });
        $("#generar").click(function(event){
            window.location.href = "../../../reportes/excel/asistencias_individuales.php?ficha="+$('#ficha').val()+"&&fecha1="+$('#fecinicial').val()+"&&fecha2="+$('#fecfinal').val();
        });
        $("#filtro").click(function(event){
            window.location.href = "procesar_registros.php?region="+$('#codregion').val()+"&&dpto="+$('#dpto').val();
        });
    });
    </script>
</body>
</html>