<?php
error_reporting(E_ALL ^ E_DEPRECATED);
require_once "config/db.php";
$conexion =  mysqli_connect( $params['host'], $params['user'], $params['password'], $params['dbname'] ) or
        die( 'Could not open connection to server' );
mysqli_query($conexion, 'SET CHARACTER SET utf8');

$res = $conexion->query("SELECT * FROM caa_incidencias") or die(mysqli_error($conexion));
function tipo_inc($tipo)
{
    switch ($tipo) {
        case 1:
            return "NORMAL";
            break;
        case 2:
            return "JUSTIFICACION";
            break;
        case 3:
            return "APROBACION";
            break;
        case 4:
            return "PERMISOS";
            break;
        default:
            return "NORMAL";
            break;
    }
}			    
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
                        <img src="../../../includes/imagenes/21.png" width="22" height="22" class="icon"> Lista de Incidencias
                    </div>
                    <div class="actions">
                        <a class="btn btn-primary" href="tipo_incidencia.php?tip=1">
                            <i class="fa fa-plus" aria-hidden="true"></i>
                            Agregar
                        </a>
                        <a class="btn btn-sm blue"  onclick="javascript: window.location='../submenu_tipos.php?modulo=71'">
                          <i class="fa fa-arrow-left"></i> Regresar
                        </a>
                    </div>
                </div>
                <div class="portlet-body">


                    <table class="table table-striped table-bordered table-hover" id="table_datatable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Codigo</th>
                                <th>Decripcion</th>
                                <th>Acronimo</th>
                                <th>Tipo</th>
                                <th align="center">Opciones</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php $i=0;while ($fila = mysqli_fetch_array($res)){ ?>
                        	<tr>
                                <td><?php echo $i+1; ?></td>
                                <td><?php echo $fila['codigo']; ?></td>
                                <td><?php echo $fila['descripcion']; ?></td>
                                <td><?php echo $fila['acronimo']; ?></td>
                                <td><?php echo tipo_inc($fila['tipo']); ?></td>
                                <td align="center">
                                	<a class="btn btn-primary" href="tipo_incidencia.php?id=<?php echo $fila['id'] ?>&&tip=2" title="Editar tipo de Incidencia">
            						    <i class="fa fa-edit" aria-hidden="true"></i>
            						</a>
                                    <a class="btn btn-danger" href="tipo_incidencia.php?id=<?php echo $fila['id'] ?>&&tip=3" title="Eliminar tipo de Incidencia">
                                        <i class="fa fa-remove" aria-hidden="true"></i>
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
</body>
</html>