<?php
error_reporting(E_ALL ^ E_DEPRECATED);
require_once "config/db.php";
$conexion =  mysqli_connect( $params['host'], $params['user'], $params['password'], $params['dbname'] ) or
        die( 'Could not open connection to server' );
mysqli_query($conexion, 'SET CHARACTER SET utf8');
$ficha   = $_GET['ficha'];
$fecha   = isset($_GET['fecha']) ? $_GET['fecha'] : date('d-m-Y');
$mes     = (isset($_GET['mes'])) ? $_GET['mes']:$_POST['mes'];

$res = $conexion->query("SELECT * FROM nompersonal WHERE ficha = '$ficha'") or die(mysqli_error($conexion));
$emp = mysqli_fetch_array($res);
$res = $conexion->query("SELECT a . * , c.descripcion
                        FROM caa_justificacion a
                        LEFT JOIN caa_incidencias_empleados b ON a.id_incidencia = b.id
                        LEFT JOIN caa_incidencias c ON b.id_incidencia = c.id
                        WHERE a.ficha = '$ficha'") or die(mysqli_error($conexion));
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
                        <img src="../../../includes/imagenes/21.png" width="22" height="22" class="icon"> Incidencias - <?= $emp['apenom'] ?>
                    </div>
                    <div class="actions">
                        <?php if ( isset($_SESSION['agregar_just_caa']) ): ?>                            
                        <a class="btn btn-primary" href="justificar_incidencias.php?ficha=<?php echo $ficha ?>&fecha=<?php echo $fecha ?>&&mes=<?php echo $mes; ?>">
                            <i class="fa fa-plus" aria-hidden="true"></i>
                            Agregar
                        </a>
                        <?php endif ?>
                        <a class="btn btn-primary" href="resumen_asistencias.php?ficha=<?php echo $ficha ?>&mes=<?php echo $mes;?>">
                            <i class="fa fa-hand-o-left" aria-hidden="true"></i>
                            Atras
                        </a>
                    </div>
                </div>
                <div class="portlet-body">


                    <table class="table table-striped table-bordered table-hover" id="table_datatable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Fecha</th>
                                <th>Incidencia</th>
                                <th>Justificacion</th>
                                <th>Estado</th>
                                <th align="center">Opciones</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php $i=0;while ($fila = mysqli_fetch_array($res)){ ?>
                        	<tr>
                                <td><?php echo $i+1; ?> </td>
                                <td><?php echo $fila['fecha']; ?> </td>
                                <td><?php echo $fila['descripcion']; ?> </td>
                                <td><?php echo $fila['justificacion']; ?> </td>
                                <td><?php echo ($fila['estado'] == 0)?"SIN APROBAR":"APROBADA"; ?></td>
                                <td align="left">
                                    <?php if ($fila['estado'] == 0 and isset($_SESSION['aprobar_just_caa']) ): ?>    
                                    <a class="btn btn-primary" href="utils/aprobar_justificacion.php?id=<?php echo $fila['id'] ?>&ficha=<?php echo $fila['ficha'] ?>&fecha=<?php echo $fila['fecha'] ?>&mes=<?php echo $fec; ?>&opc=1" title="Aprobar justificacion de Incidencia">
                                        <i class="fa fa-thumbs-o-up" aria-hidden="true"></i>
                                    </a>
                                    <?php endif ?>
                                    <?php if (isset($_SESSION['cancelar_just_caa'])): ?>                                        
                                    <a class="btn btn-danger" href="utils/aprobar_justificacion.php?id=<?= $fila['id'] ?>&ficha=<?= $ficha ?>&opc=2" title="Cancelar justificacion de Incidencia">
                                        <i class="fa fa-remove" aria-hidden="true"></i>
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
</body>
</html>