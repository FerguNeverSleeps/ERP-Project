<?php
error_reporting(E_ALL ^ E_DEPRECATED);
require_once "config/db.php";
$conexion =  mysqli_connect( $params['host'], $params['user'], $params['password'], $params['dbname'] ) or
        die( 'Could not open connection to server' );
mysqli_query($conexion, 'SET CHARACTER SET utf8');

$ficha = $_GET['ficha'];
$fec   = ($_GET['mes']) ? $_GET['mes']:date('Y-m-d');
//-------------------------------------------------------
$meses = array('01' => 'Enero','02' => 'Febrero','03' => 'Marzo','04' => 'Abril','05' => 'Mayo','06' => 'Junio','07' => 'Julio','08' => 'Agosto','09' => 'Septiembre','10' => 'Octubre','11' => 'Noviembre','12' => 'Diciembre');
$mes = $meses[date("m", strtotime($fec))];
$fecha = new DateTime($fec);
//-------------------------------------------------------
$fecha->modify('first day of this month');
$fecha1 = $fecha->format('Y-m-d');
//-------------------------------------------------------
$fecha->modify('last day of this month');
$fecha2 = $fecha->format('Y-m-d');
//-------------------------------------------------------
$res = $conexion->query("SELECT * FROM nompersonal WHERE ficha='$ficha'") or die(mysqli_error($conexion));
$empleado = mysqli_fetch_array($res);

$datos = $conexion->query("SELECT a.*,b.descripcion
FROM caa_resumen AS a 
LEFT JOIN nomturnos AS b ON a.turno_id = b.turno_id
WHERE a.ficha = '$ficha' AND a.fecha BETWEEN '$fecha1' AND '$fecha2'
ORDER BY a.ficha,a.fecha ASC") or die(mysqli_error($conexion));

$res = $conexion->query("SELECT MIN(fecha) as fecini,MAX(fecha) AS fecfin FROM caa_resumen WHERE estado = 0 AND ficha = '$ficha'") or die(mysqli_error($conexion));
$fec_reg = mysqli_fetch_array($res);
$fecini  =$fec_reg['fecini'];
$fecfin  =$fec_reg['fecfin'];
?>
    <?php include("../../../includes/dependencias.php");?>

    <!-- COMIENZO DEL CONTENIDO-->
    <div class="row">
        <div class="col-md-12">
            <!-- COMIENZO DE LA TABLA-->
            <?php if ((isset($_GET['msj']))&&($_GET['msj'] == 1)): ?>
            <div class="alert alert-success fade in">
                <a href="#" class="close" data-dismiss="alert">&times;</a>
                <strong>Exito!</strong> Proceso exitoso..!!
            </div>
            <?php elseif ((isset($_GET['msj']))&&($_GET['msj'] != 1)): ?>
            <div class="alert alert-danger fade in">
                <a href="#" class="close" data-dismiss="alert">&times;</a>
                <strong>Error!</strong> Ocurrion un problema en el proceso..!!
            </div>
            <?php endif ?>
            <div class="portlet box blue">
                <div class="portlet-title">
                    <div class="caption">
                        <img src="../../../includes/imagenes/21.png" width="22" height="22" class="icon"> Resumen de Asistencias - <?php echo $empleado['apenom']." , Funcionario: ".$empleado['ficha'].",  Mes: ".$mes; ?>
                    </div>
                    <div class="actions">
                        <a class="btn btn-sm blue" href="registro_manual.php">
                            <i class="fa fa-hand-o-left" aria-hidden="true"></i>
                            Atras
                        </a>
                        <button type="button" class="btn btn-sm blue" data-toggle="modal" data-target="#Aprobar" data-ficha="<?php echo $ficha;?>">
                            <i class="fa fa-calendar" aria-hidden="true"></i>
                            Filtrar
                        </button>
                    </div>
                </div>
                <div class="portlet-body">


                    <table class="table table-striped table-bordered table-hover" id="table_datatable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Fecha</th>
                                <th>Entrada</th>
                                <th>Salida</th>
                                <th>Tiempo</th>
                                <th>Tardanza</th>
                                <th>H. Extra</th>
                                <th>Recargo 25%</th>
                                <th>Recargo 50%</th>
                                <th>Ausencia</th>
                                <th>Estado</th>
                                <th>Opciones</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php $i=0;while ($fila = mysqli_fetch_array($datos)){ ?>
                        	<tr>
                                <td><?php echo $i+1; ?></td>
                                <td><?php echo date("d-m-Y", strtotime($fila['fecha']));?></td>
                                <td><?php echo $fila['entrada']; ?></td>
                                <td><?php echo $fila['salida']; ?></td>
                                <td><?php echo $fila['tiempo']; ?></td>
                                <td><?php echo ($fila['tardanza'] !="00:00:00")? "<strong>".$fila['tardanza']."</strong>":$fila['tardanza'] ; ?></td>
                                <td><?php echo ($fila['h_extra'] !="00:00:00")? "<strong>".$fila['h_extra']."</strong>":$fila['h_extra'] ; ?></td>
                                <td><?php echo ($fila['recargo_25'] !="00:00:00")? "<strong>".$fila['recargo_25']."</strong>":$fila['recargo_25'] ; ?></td>
                                <td><?php echo ($fila['recargo_50'] !="00:00:00")? "<strong>".$fila['recargo_50']."</strong>":$fila['recargo_50'] ; ?></td>
                                <td><?php echo ($fila['ausencia'] == 0) ? "No" : "<strong>Si</strong>"; ?></td>
                                <td><?php echo ($fila['estado'] == 0) ? "Sin Procesar" : "Procesada"; ?></td>
                                <td>
                                    <?php if (isset($_SESSION['acceso_gestionar_inc_caa'] )): ?>
                                	<a class="btn btn-primary" href="gestion_incidencias.php?ficha=<?php echo $fila['ficha'] ?>&fecha=<?php echo $fila['fecha'] ?>&mes=<?php echo $fec; ?>" title="Ver Detalles de Asistencia">
            						<i class="fa fa-clock-o" aria-hidden="true"></i>
            						</a>
                                    <?php endif ?>
                                    <?php if (isset($_SESSION['acceso_justificar_inc_caa'] )): ?>
                                    <a class="btn btn-success" href="list_justificaciones.php?ficha=<?php echo $fila['ficha'] ?>&fecha=<?php echo $fila['fecha'] ?>&mes=<?php echo $fec; ?>" title="Justificar Incidencias">
                                    <i class="fa fa-thumbs-o-up" aria-hidden="true"></i>
                                    </a>
                                    <?php endif ?>
                                    <?php if ( $fila['ausencia']==1 ): ?>
                                    <a class="btn btn-success" href="form_asistencia.php?ficha=<?php echo $fila['ficha'] ?>&fecha=<?php echo $fila['fecha'] ?>&mes=<?php echo $fec; ?>&accion=edit&tip_inc=4" title="Editar Registro">
                                    <i class="fa fa-edit" aria-hidden="true"></i>
                                    </a>
                                    <?php elseif ( $fila['carga']==1 ): ?>
                                    <a class="btn btn-success" href="form_asistencia.php?ficha=<?php echo $fila['ficha'] ?>&fecha=<?php echo $fila['fecha'] ?>&mes=<?php echo $fec; ?>&accion=edit&tip_inc=3" title="Editar Registro">
                                    <i class="fa fa-edit" aria-hidden="true"></i>
                                    </a>
                                    <?php endif ?>
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
                        "url": "../ajax/server_side/empleados_noreloj.php" // ajax source
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
        <div class="modal fade" id="Aprobar" tabindex="-1" role="dialog" aria-labelledby="AprobarLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="AprobarLabel">Filtar los Registro</h4>
                    </div>
                <div class="modal-body">
                    <input type="hidden" id="ficha" value="<?php echo $ficha ?>">
                    <div class="form-group">
                        <label for="fecini" class="form-control-label">Mes:</label>
                        <select name="mes" id="mes" class="form-control">
                            <option value="-">Seleccione</option>
                            <option value="2016-01-15">Enero</option>
                            <option value="2016-02-15">Febrero</option>
                            <option value="2016-03-15">Marzo</option>
                            <option value="2016-04-15">Abril</option>
                            <option value="2016-05-15">Mayo</option>
                            <option value="2016-06-15">Junio</option>
                            <option value="2016-07-15">Julio</option>
                            <option value="2016-08-15">Agosto</option>
                            <option value="2016-09-15">Septiembre</option>
                            <option value="2016-10-15">Octubre</option>
                            <option value="2016-11-15">Noviembre</option>
                            <option value="2016-12-15">Diciembre</option>
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

    <div class="bd-example">
        <div class="modal fade" id="Ausencia" tabindex="-1" role="dialog" aria-labelledby="AsusenciaLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="TituloAsusencia">Agragar Ausencia</h4>
                    </div>
                <form action="reg_ausencia.php" method="POST">
                <div class="modal-body">
                        <div class="form-group">
                        <div class="form-group">
                            <label for="fecha" class="form-control-label">Fecha:</label>
                            <input type="hidden" name="ficha" value="<?php echo $ficha; ?>">
                            <div class="input-group">
                                <div class="input-group date" id="datetimepicker0">
                                    <input type="text" class="form-control" id="fecha" name="fecha">
                                    <span class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </span>
                                </div>
                            </div>
                            <input type="hidden" id="accion" name="accion">
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <input type="submit" name="enviar_form" class="btn btn-sm blue" value="Registrar Asusencia">
                </div>
                </form>
              </div>
            </div>
        </div>
    </div>
    <script src="../../../includes/assets/plugins/jquery.js"></script>
    <script src="../../../includes/assets/plugins/moment/min/moment-with-locales.js"></script>
    <script src="../../../includes/assets/plugins/bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>
    <script src="../../../includes/assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
    <script>
    $('#Aprobar').on('show.bs.modal', function (event) {
      var button = $(event.relatedTarget)
      var ficha = button.data('ficha')
      var modal  = $(this)
      //modal.find('.modal-title #AprobarLabel').text('Aprobar todos los Registro')
      modal.find('.modal-body #ficha').val("sdfsdf")
    });
    $('#Ausencia').on('show.bs.modal', function (event) {
      var button = $(event.relatedTarget)
      var ficha  = button.data('ficha2')
      var accion = button.data('accion')
      var modal  = $(this)
      modal.find('.modal-body #ficha2').val(ficha)
      modal.find('.modal-body #accion').val(accion)
    });
    $(document).ready(function(){

        moment.locale('es-do');
        moment("00:00:00", "HH:mm:ss");
        $(function () {
            $('#datetimepicker0').datetimepicker({
                format: 'DD-MM-YYYY'
            });
        });
        $("#filtro").click(function(event){
            window.location.href = "resumen_asistencias_manuales.php?ficha="+$('#ficha').val()+"&&mes="+$('#mes').val();
        });
    });
    </script>