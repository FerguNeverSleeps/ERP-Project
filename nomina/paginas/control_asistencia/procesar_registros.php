<?php
session_start();
require_once "config/db.php";
/*$conexion =  mysqli_connect( $params['host'], $params['user'], $params['password'], $params['dbname'] ) or
        die( 'Could not open connection to server' );*/
mysqli_query($conexion, 'SET CHARACTER SET utf8');
//--------------------------------------------------------
$nivel1 = (isset($_GET['region'])) ? $_GET['region']  : $_SESSION['region'];
$nivel2 = (isset($_GET['dpto'])) ? $_GET['dpto']  : $_SESSION['departamento'];
$id_usuario=$_SESSION['id_usuario'];

$resDep = $conexion->query("SELECT * FROM departamento "
                        . " ORDER BY Descripcion ASC");

if( $_SESSION['acceso_dep']==0 && $_SESSION['departamento']!=0)
{
    $resDep = $conexion->query("SELECT * FROM departamento "
                        . " WHERE IdDepartamento = ".$_SESSION['departamento'].""
                        . " ORDER BY Descripcion ASC");
    

}

if( $_SESSION['acceso_dep']==1)
{
    $resDep = $conexion->query("SELECT * FROM departamento "
                        . " WHERE IdDepartamento IN (SELECT  id_departamento from usuario_departamento WHERE id_usuario=".$id_usuario.")"
                        . " ORDER BY Descripcion ASC");
    

}
//--------------------------------------------------------

//--------------------------------------------------------
$res = $conexion->query("SELECT MIN(fecha) as fecini,MAX(fecha) AS fecfin FROM caa_resumen WHERE estado = 0") or die(mysqli_error($conexion));
$fec_reg = mysqli_fetch_array($res);
$fecini  =$fec_reg['fecini'];
$fecfin  =$fec_reg['fecfin'];

$consulta  = "SELECT * FROM `nomempresa`";
$res_emp   = $conexion->query($consulta);
$dataEmp   = mysqli_fetch_array($res_emp);
?>
<!DOCTYPE html>
<html lang="es" class="no-js">
<head>
    <?php //include("../../../includes/dependencias.php");?>
    <!-- BEGIN GLOBAL MANDATORY STYLES
    <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css"/> -->
    <link href="../../../includes/assets/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
    <link href="../../../includes/assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <link href="../../../includes/assets/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css"/>
    <!-- END GLOBAL MANDATORY STYLES -->
    <!-- BEGIN PAGE LEVEL STYLES -->
    <link rel="stylesheet" type="text/css" href="../../../includes/assets/plugins/select2/select2.css"/>
    <link rel="stylesheet" type="text/css" href="../../../includes/assets/plugins/select2/select2-metronic.css"/>
    <link rel="stylesheet" href="../../../includes/assets/plugins/data-tables/DT_bootstrap.css"/>
    <link rel="stylesheet" type="text/css" href="../../../includes/assets/plugins/fancybox/source/jquery.fancybox.css" media="screen" />
    <!-- END PAGE LEVEL STYLES -->
    <!-- BEGIN THEME STYLES -->
    <link href="../../../includes/assets/css/style-metronic.css" rel="stylesheet" type="text/css"/>
    <link href="../../../includes/assets/css/style.css" rel="stylesheet" type="text/css"/>
    <link href="../../../includes/assets/css/style-responsive.css" rel="stylesheet" type="text/css"/>
    <link href="../../../includes/assets/css/plugins.css" rel="stylesheet" type="text/css"/>
    <!-- <link href="../../includes/assets/css/themes/default.css" rel="stylesheet" type="text/css" id="style_color"/> -->
    <link href="../../../includes/assets/css/custom.css" rel="stylesheet" type="text/css"/>
    <!-- END THEME STYLES -->
    <link href="../../../includes/assets/css/custom-header4.css" rel="stylesheet" type="text/css"/>
    <link href="../../../includes/assets/css/custom-datatables.css" rel="stylesheet" type="text/css"/>
</head>
<body style="background-color: #ccc;">
<div class="container-fluid">
<br>
<br>
    <!-- COMIENZO DEL CONTENIDO-->
    <div class="row">
        <div class="col-md-12">
            <!-- COMIENZO DE LA TABLA-->
            <div class="portlet box blue">
                <div class="portlet-title">
                    <div class="caption">
                        <img src="../../../includes/imagenes/21.png" width="22" height="22" class="icon"> Lista de Empleados
                    </div>
                    <div class="actions">
                        <?php if (isset($_SESSION['ver_rpt_marcaciones_caa'])): ?>   
                        
                        <a class="btn btn-sm blue" href="../../../reportes/form_marcaciones_globales.php">
                            <i class="fa fa-file-excel-o" aria-hidden="true"></i>
                            Reporte Marcaciones
                        </a>
                        
                        <?php endif ?>
                        <?php if (isset($_SESSION['ver_rpt_general_caa'])): ?>                            
                        <a class="btn btn-sm blue" href="../../../reportes/form_resumen_ausencias_tardanzas.php">
                            <i class="fa fa-file-excel-o" aria-hidden="true"></i>
                            Reporte Ausencias y Tardanzas
                        </a>
                        <?php endif ?>
                        <?php if (isset($_SESSION['ver_rpt_general_caa'])): ?>                            
                        <a class="btn btn-sm blue" href="../../../reportes/form_resumen_asistencias.php?opc=1">
                            <i class="fa fa-file-excel-o" aria-hidden="true"></i>
                            Reporte Incidencias
                        </a>
                        <?php endif ?>
                        <?php if (isset($_SESSION['ver_rpt_marcaciones_caa'])): ?>                            
                        <a class="btn btn-sm blue" href="../../../reportes/form_control_diario.php">
                            <i class="fa fa-file-excel-o" aria-hidden="true"></i>
                            Exportar Marcaciones
                        </a>
                        <?php endif ?>
                        <button type="button" class="btn btn-sm blue" data-toggle="modal" data-target="#filtral" data-region="<?php echo $gerencia['descrip'];?>">
                            <i class="fa fa-calendar" aria-hidden="true"></i>
                            Filtrar
                        </button>
                    </div>
                </div>
                <div class="portlet-body">
                    
                    <form action="" method="post" name="frmPrincipal" id="frmPrincipal">
                        <table class="table table-striped table-bordered table-hover" id="datatable">
                            <thead>
                                <tr>
                                    <th>FUNCIONARIO</th>
                                    <th>CEDULA</th>
                                    <th>Nº POSICION</th>
                                    <th>Nº MARCAR</th>
                                    <th>DEPARTAMENTO</th>
                                    <th>&nbsp;</th>
                                    <th>&nbsp;</th>
                                    <th>&nbsp;</th>
                                    <th>&nbsp;</th>
                                </tr>
                            </thead>
                            <tbody>
                            
                            </tbody>
                        </table>
                        <input name="registro_id" type="hidden" value="">
                        <input name="op" type="hidden" value="">    
                    </form>

            </div>
            <!-- FIN DE LA TABLA-->
        </div>
    </div>
    <!-- FIN DE LA PAGINA DE CONTENIDO-->
</div>
<!-- FIn de Ventana modal deexportacion -->

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
                    <h4 class="modal-title" id="AprobarLabel">Filtar los Registros</h4>
                    </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="fecini" class="form-control-label">Departamentos:</label>
                        <select name="dpto" id="dpto" class="form-control">
                            <option value="all" selected>VER TODOS</option>
                            <?php while ($dpto = mysqli_fetch_array($resDep)) {?>
                            <option value="<?php echo $dpto['IdDepartamento'] ?>"><?php echo $dpto['Descripcion'] ?></option>
                            <?php }?>
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
        <div class="modal fade" id="exampleModal3" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                            <div class="input-group date" id="datetimepicker2">
                                <input type="text" id="fecinicial2" name="fecinicial2" class="form-control input-sm" />
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>
                            <input type="hidden" id="ficha2">
                        </div>
                        <div class="form-group">
                            <label class="control-label">Fecha Final:</label>
                            <div class="input-group date" id="datetimepicker3">
                                <input type="text" id="fecfinal2" name="fecfinal2" class="form-control input-sm" />
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-sm blue" id="generar-marcaciones" data-dismiss="modal">Generar Reporte</button>
                </div>
            </div>
            </div>
        </div>
    </div>

    <script src="../../../includes/assets/plugins/jquery-1.10.2.min.js"></script>
    <script src="../../../includes/assets/plugins/jquery-migrate-1.2.1.min.js"></script>
    <script src="../../../includes/assets/plugins/bootstrap/js/bootstrap.min.js"></script>
    <script src="../../../includes/assets/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js"></script>
    <script src="../../../includes/assets/plugins/jquery-slimscroll/jquery.slimscroll.min.js"></script>
    <script src="../../../includes/assets/plugins/jquery.blockui.min.js"></script>
    <script src="../../../includes/assets/plugins/jquery.cokie.min.js"></script>
    <script src="../../../includes/assets/plugins/uniform/jquery.uniform.min.js"></script>
    <!-- END CORE PLUGINS -->
    <!-- BEGIN PAGE LEVEL PLUGINS -->
    <script src="../../../includes/assets/plugins/select2/select2.min.js"></script>
    <script src="../../../includes/assets/plugins/select2/select2_locale_es.js"></script>
    <script src="../../../includes/assets/plugins/data-tables/jquery.dataTables.js"></script>
    <script src="../../../includes/assets/plugins/data-tables/DT_bootstrap.js"></script>
    <script src="../../../includes/assets/plugins/bootbox/bootbox.min.js" type="text/javascript"></script>
    <!-- END PAGE LEVEL PLUGINS -->
    <!-- BEGIN PAGE LEVEL SCRIPTS -->
    <script src="../../../includes/assets/scripts/core/app1.js"></script>

    <script src="../../../includes/assets/plugins/moment/min/moment-with-locales.js"></script>
    <script src="../../../includes/assets/plugins/bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>
    <script src="../../../includes/assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
    <script>
        $(document).ready(function(){
            createEvents();
            var oTable = $('#datatable').DataTable({
                "bProcessing": true,
                "bServerSide": true,
                "sAjaxSource": "ajax/empleados.php?dpto_id=<?php echo ((isset($_GET['dpto_id']))?$_GET['dpto_id']:"all"); ?>", 
                "sDom": "<'row'<'col-md-3 col-sm-12'l><'col-md-9 col-sm-12'f>r><'table-scrollable't><'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>",
                "iDisplayLength": 10,
                "sPaginationType": "bootstrap_extended",
                "aaSorting": [[4, 'asc'], [ 2, "asc" ]], 
                "oLanguage": {
                    "sProcessing": "Cargando...",
                    "sSearch": "",
                    "sLengthMenu": "Mostrar _MENU_",
                    "sInfoEmpty": "",
                    "sInfo":"Total _TOTAL_ registros",
                    "sInfoFiltered": "",
                    "sEmptyTable":  "No hay datos disponibles", 
                    "sZeroRecords": "No se encontraron registros",
                    "oPaginate": {
                        "sPrevious": "P&aacute;gina Anterior",
                        "sNext":     "P&aacute;gina Siguiente",
                        "sPage":     "P&aacute;gina",
                        "sPageOf":   "de",
                    }
                },
                "aLengthMenu": [ 
                    [5, 10, 25, 50,  -1],
                    [5, 10, 25, 50, "Todos"]
                ],                
                "aoColumnDefs": [ 
                    { 'bSortable':   false, 'aTargets': [5, 6, 7, 8] },
                    { 'bSearchable': false, 'aTargets': [5, 6, 7, 8] },
                    { 'bVisible': false, 'aTargets': [5] },
                    { 'sWidth': "10%", "aTargets": [1,2,3] },
                    { "sClass": "text-center", "aTargets": [0, 1, 2, 3, 4] },
                ],
                "fnDrawCallback": function() {
                    $('#datatable_filter input').attr("placeholder", "Escriba frase para buscar");
                    createEvents();
                },
                initComplete: function(){
                    createEvents();
                }
            });

            $('#datatable_wrapper .dataTables_filter input').addClass("form-control input-medium input-inline"); 
            $('#datatable_wrapper .dataTables_length select').addClass("form-control input-small"); 
            $('#datatable_wrapper .dataTables_length select').select2({
                showSearchInput : false
            }); 

            $('#div_search_situ').insertBefore("#table_datatable_wrapper .dataTables_filter input");

            $('#datatable_wrapper .dataTables_filter input').after(' <a class="btn blue" id="btn-search"><i class="fa fa-search"></i> Buscar</a> ');

            $("#btn-search").click( function()
            {
                var valor_buscar =$('#search_situ').val();
                if(valor_buscar == 'Todos')
                    valor_buscar = '';

                    oTable.fnFilter( valor_buscar, 5 ); // Se filtra por la columna 5 - Situación
            });

            function createEvents() {
                $('.modalreporte').off().on('click', function(){
                    var url = $(this).attr('rel');
                    $('#reportepdf').attr('href', url);
                    var url = $(this).attr('rel2');
                    $('#reportepdf2').attr('href', url);
                    var url = $(this).attr('name');
                    $('#reporteexcel').attr('href', url);
                    $("#modal-reporte").modal('show');
                }); 
            }
            //FIN DEL DATATABLE
            $(function () {
                $('#datetimepicker0').datetimepicker({
                    format: 'DD-MM-YYYY'
                });
                $('#datetimepicker1').datetimepicker({
                    format: 'DD-MM-YYYY'
                });
                $('#datetimepicker2').datetimepicker({
                    format: 'DD-MM-YYYY'
                });
                $('#datetimepicker3').datetimepicker({
                    format: 'DD-MM-YYYY'
                });
            });
             $('#dpto').select2();
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
            $('#exampleModal3').on('show.bs.modal', function (event) {
              var button = $(event.relatedTarget)
              var ficha = button.data('ficha')
              var modal  = $(this)
              modal.find('.modal-title').text('Reporte De Marcaciones')
              modal.find('.modal-body #ficha2').val(ficha)
            });
            $("#generar").click(function(event){
                window.location.href = "asistencias_individuales.php?ficha="+$('#ficha').val()+"&&fecha1="+$('#fecinicial').val()+"&&fecha2="+$('#fecfinal').val();
            });
            $("#generar-marcaciones").click(function(event){
                window.location.href = "../../../reportes/pdf/marcaciones_personales.php?ficha="+$('#ficha2').val()+"&&fecha1="+$('#fecinicial2').val()+"&&fecha2="+$('#fecfinal2').val();
            });
            $("#filtro").click(function(event){
                window.location.href = "procesar_registros.php?dpto_id="+$('#dpto').val();
            });
        });
    </script>
</body>
</html>