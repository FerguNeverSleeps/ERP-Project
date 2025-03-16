<?php 
require_once '../../generalp.config.inc.php';
session_start();
ob_start();
// error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require_once '../lib/common.php';
$conexion=conexion();

$sql = "SELECT * FROM nombancos";
$res = query($sql, $conexion);

include("../header4.php"); // <html><head></head><body>
?>

<link href="../../includes/assets/css/custom-datatables.css" rel="stylesheet" type="text/css"/>
<div class="page-container">
    <div class="page-content-wrapper">
        <div class="page-content">
            <div class="row">
                <div class="col-md-12">
                    <div class="portlet box blue">
                        <div class="portlet-title">
                            <div class="caption">
                                <img src="../imagenes/21.png" width="22" height="22" class="icon"> Bancos
                            </div>
                            <div class="actions">
                                <a class="btn btn-sm blue"  onclick="javascript: window.location='bancos_agregar.php'">
                                    <i class="fa fa-plus"></i>Agregar
                                </a>
                                <!--<a class="btn btn-sm blue"  onclick="javascript: window.location='../paginas/menu_int.php?cod=282'">
                                    <i class="fa fa-arrow-left"></i> Regresar
                                </a>-->
                            </div>
                        </div>
                        <div class="portlet-body">
                            <table class="table table-striped table-bordered table-hover" id="table_datatable">
                                <thead>
                                    <tr>
                                        <th>C&oacute;digo</th>
                                        <th>Nombre</th>
                                        <th>Nro Cuenta</th>
                                        <th>Tipo</th>
                                        <th>Nro Cuenta Presupuestaria</th>
                                        <th>&nbsp;</th>
                                        <th>&nbsp;</th>
                                        <th>&nbsp;</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    while($fila=fetch_array($res))
                                    { 
                                        $codigo=$fila["cod_ban"];
                                        $des_ban=$fila["des_ban"];
                                        $cuentacob=$fila["cuentacob"];
                                        $tipocuenta=$fila["tipocuenta"];
                                        $ctacon=$fila["ctacon"];                            
                                        ?>
                                        <tr class="odd gradeX">
                                            <td><?php echo $codigo; ?></td>
                                            <td><?php echo $des_ban; ?></td>
                                            <td><?php echo $cuentacob; ?></td>
                                            <td><?php echo $tipocuenta; ?></td>
                                            <td><?php echo $ctacon; ?></td>
                                            <?php
                                            icono("bancos_modificar.php?codigo=".$codigo, "Editar", "edit.gif");
                                            icono("bancos_eliminar.php?codigo=".$codigo, "Eliminar", "delete.gif");
                                            icono("chequeras.php?codigo=".$codigo, "Chequeras", "chequera.png");
                                            ?>
                                        </tr>
                                        <?php                                 
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include("../footer4.php"); ?>
<script type="text/javascript">
    $(document).ready(function()
    {
        $('#table_datatable').DataTable(
        {
            //"oSearch": {"sSearch": "Escriba frase para buscar"},
            "iDisplayLength": 10,
            //"sPaginationType": "bootstrap",
            "sPaginationType": "bootstrap_extended", 
            //"sPaginationType": "full_numbers",
            "oLanguage":
            {
                "sSearch": "<img src='../../includes/imagenes/icons/magnifier.png' width='16' height='16' > Buscar:",
                "sLengthMenu": "Mostrar _MENU_",
                //"sInfo": "Showing page _PAGE_ of _PAGES_", // Mostrando 1 to 5 de 18 entradas
                //"sInfoEmpty": "No hay registros para mostrar",
                "sInfoEmpty": "",
                //"sInfo": "",
                "sInfo":"Total _TOTAL_ registros",
                "sInfoFiltered": "",
                "sEmptyTable":  "No hay datos disponibles",//"No data available in table",
                "sZeroRecords": "No se encontraron registros",//"No matching records found",
                /*"oPaginate": {
                "sPrevious": "Página Anterior",
                "sNext": "Página Siguiente"
                }*/
                "oPaginate":
                {
                    "sPrevious": "P&aacute;gina Anterior",//"Prev",
                    "sNext": "P&aacute;gina Siguiente",//"Next",
                    "sPage": "P&aacute;gina",//"Page",
                    "sPageOf": "de",//"of"
                }
            },
            "aLengthMenu":
            [ // set available records per page
                [5, 10, 25, 50,  -1],
                [5, 10, 25, 50, "Todos"]
            ],                
            "aoColumnDefs":
            [
                { 'bSortable': false, 'aTargets': [3] },
                { "bSearchable": false, "aTargets": [ 3 ] },
                { 'bSortable': false, 'aTargets': [2] },
                { "bSearchable": false, "aTargets": [ 2 ] },
                { "sWidth": "8%", "aTargets": [2] },
                { "sWidth": "8%", "aTargets": [3] }
            ],
            "fnDrawCallback": function()
            {
                $('#table_datatable_filter input').attr("placeholder", "Escriba frase para buscar");
            }
        });
        $('#table_datatable').on('change', 'tbody tr .checkboxes', function()
        {
            $(this).parents('tr').toggleClass("active");
        });
        $('#table_datatable_wrapper .dataTables_filter input').addClass("form-control input-medium input-inline"); 
        $('#table_datatable_wrapper .dataTables_length select').addClass("form-control input-xsmall"); 
    });
</script>
</body>
</html>