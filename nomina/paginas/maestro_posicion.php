<?php 
session_start();
ob_start();

require_once '../lib/common.php';
include ("func_bd.php");
//error_reporting(0);

$registro_id=$_POST['registro_id'];	
$op=$_POST['op'];


?>
<?php include("../header4.php"); // <html><head></head><body> ?>
<link href="../../includes/assets/css/custom-datatables.css" rel="stylesheet" type="text/css"/>
<style type="text/css">
  .portlet > .portlet-title > .actions > .btn.btn-sm {
    margin-top: -9px !important;
  }
</style>
<script type="text/javascript">
function enviar(op,id){

	if (op==3){		// Opcion de Eliminar
		if (confirm("\u00BFEst\u00E1 seguro que desea eliminar el registro ?"))
		{					
			document.frmPrincipal.registro_id.value=id;
			document.frmPrincipal.op.value=op;
  			document.frmPrincipal.submit();
		}		
	}
}
</script>
<div class="page-container">
	<!-- BEGIN SIDEBAR -->
	<!-- END SIDEBAR -->
	<!-- BEGIN CONTENT -->
	<div class="page-content-wrapper">
		<div class="page-content">
			<!-- BEGIN PAGE CONTENT-->
			<div class="row">
				<div class="col-md-12">
					<!-- BEGIN EXAMPLE TABLE PORTLET-->
					<div class="portlet box blue">
						<div class="portlet-title">
							<div class="caption">
								POSICIONES
							</div>
							<div class="actions">

								<a class="btn btn-sm blue"  data-toggle="modal" data-target="#Reporte" >
									<img height="19px" width="19px" src="../../includes/imagenes/icons/logo_excel.png" alt="">
									EXPORTAR POR PLANILLA
								</a>
								<a class="btn btn-sm blue"  onclick="javascript: window.location='../../reportes/excel/excel_posiciones.php'">
									<img height="19px" width="19px" src="../../includes/imagenes/icons/logo_excel.png" alt="">
									EXPORTAR
								</a>
								<a class="btn btn-sm blue"  onclick="javascript: window.location='ag_maestro_posicion.php'">
									<i class="fa fa-plus" aria-hidden="true"></i>
									AGREGAR
								</a>

							</div>
						</div>
						<div class="portlet-body">
							<form action="" method="post" name="frmPrincipal" id="frmPrincipal">
							<table class="table table-striped table-bordered table-hover" id="table_datatable">
							<thead>
							<tr>
								<th>POSICIÓN</th>
								<th>SUELDO MENSUAL</th>
								<th>SUELDO ANUAL</th>
								<th>PARTIDA</th>
								<th>GASTOS REP.</th>
								<th>CARGO.</th>
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
					</div>
					<!-- END EXAMPLE TABLE PORTLET-->
				</div>
			</div>
			<!-- END PAGE CONTENT-->
		</div>
	</div>
	<!-- END CONTENT -->
</div>

<div class="bd-example">
    <div class="modal fade" id="Reporte" tabindex="-1" role="dialog" aria-labelledby="AprobarLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
	    	<form class="form-horizontal" method="post" action="../../reportes/excel/excel_posiciones_planilla.php">

                <div class="modal-header">
	                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	                <span aria-hidden="true">&times;</span>
	                </button>
	                <h4 class="modal-title" id="AprobarLabel">Parametros del Reporte</h4>
                </div>
	            <div class="modal-body">
	                
    				<div class="form-body">
    					<div class="form-group">
                            <label class="col-md-5 control-label">Tipos:</label>
                            <div class="col-md-7">
                            	<select name="tip" id="tip" class="form-control" required>
                            	    <option value="-">Seleccione</option>
                            	    <option value="001"> Planilla 001 </option>
                            	    <option value="002"> Planilla 002 </option>
                            	    <option value="080"> Planilla 080 </option>
                            	</select>
    						</div>
                        </div>    					
    				</div>	    				

	            </div>
	            <div class="modal-footer">
	                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
	                <input type="submit" class="btn btn-md blue" value="Generar Reporte">
	            </div>

	    	</form>
          	</div>
        </div>
    </div>
</div>


<script src="../../includes/assets/plugins/jquery-1.10.2.min.js" type="text/javascript"></script>
<script src="../../includes/assets/plugins/jquery-migrate-1.2.1.min.js" type="text/javascript"></script>
<script src="../../includes/assets/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script src="../../includes/assets/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js" type="text/javascript"></script>
<script src="../../includes/assets/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
<script src="../../includes/assets/plugins/jquery.blockui.min.js" type="text/javascript"></script>
<script src="../../includes/assets/plugins/jquery.cokie.min.js" type="text/javascript"></script>
<script src="../../includes/assets/plugins/uniform/jquery.uniform.min.js" type="text/javascript"></script>

<script src="../../includes/assets/plugins/bootstrap-switch/js/bootstrap-switch.min.js" type="text/javascript"></script>

<!-- END CORE PLUGINS -->
<!-- BEGIN PAGE LEVEL PLUGINS -->
<script type="text/javascript" src="../../includes/assets/plugins/select2/select2.min.js"></script>
<script type="text/javascript" src="../../includes/assets/plugins/select2/select2_locale_es.js"></script>
<script type="text/javascript" src="../../includes/assets/plugins/data-tables/media/js/jquery.dataTables.js"></script>

<script type="text/javascript" src="../../includes/assets/plugins/data-tables/extensions/TableTools/js/dataTables.tableTools.min.js"></script>
<script type="text/javascript" src="../../includes/assets/plugins/data-tables/extensions/ColReorder/js/dataTables.colReorder.min.js"></script>
<script type="text/javascript" src="../../includes/assets/plugins/data-tables/extensions/Scroller/js/dataTables.scroller.min.js"></script>
<script type="text/javascript" src="../../includes/assets/plugins/data-tables/plugins/bootstrap/dataTables.bootstrap.js"></script>

<!-- END PAGE LEVEL PLUGINS -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="../../includes/assets/plugins/metronic.js" type="text/javascript"></script>

<script type="text/javascript">
	 $(document).ready(function() { 

        var TableAdvanced = function () {

	    var initTable2 = function () {
	        var table = $('#table_datatable');

	        /* Table tools samples: https://www.datatables.net/release-datatables/extras/TableTools/ */

	        /* Set tabletools buttons and button container */

	        $.extend(true, $.fn.DataTable.TableTools.classes, {
	            "container": "btn-group tabletools-btn-group pull-right",
	            "buttons": {
	                "normal": "btn btn-sm default",
	                "disabled": "btn btn-sm default disabled"
	            }
	        });

	        var oTable = table.dataTable({
	        "processing": true,

	            // Internationalisation. For more info refer to http://datatables.net/manual/i18n
	            "paging":   true,
	            "ordering": true,
	            "info":     true,
	            "searching":     true,
	            "language": {
	                "aria": {
	                    "sortAscending": ": activate to sort column ascending",
	                    "sortDescending": ": activate to sort column descending"
	                },
	                "emptyTable": "No hay datos disponibles en la tabla",
	                "info": "Mostrando _START_ de _END_ de  _TOTAL_ registros",
	                "infoEmpty": "No se encontraron registros",
	                "infoFiltered": "(filtrados de _MAX_ total registros)",
	                "lengthMenu": "Mostrando _MENU_ entradas",
	                "search": "Buscar registro:",
	                "zeroRecords": "No se encontraron registros"
	            },

	            "order": [
	                [0, 'asc']
	            ],
	            "lengthMenu": [
	                [5,10, 25, 50, -1],
	                [5,10, 25, 50, "Todo"] // change per page values here
	            ],
	            "columns": [
				    { "width": "10%" },
				    { "width": "15%" },
				    { "width": "15%" },
				    { "width": "15%" },
				    { "width": "10%" },
				    { "width": "10%" },
				    { "width": "5%" },
				    { "width": "5%" }
				  ],

	            // set the initial value
	            "pageLength": 10,
	            "serverSide": true,
	            "ajax": {
	                    "url": "ajax/server_processing_listado_posiciones.php", // ajax source
	                }
	        });


	    }

	    return {

	        //main function to initiate the module
	        init: function () {

	            if (!jQuery().dataTable) {
	                return;
	            }

	            //ListarTabla();
	            initTable2();

	        }

	    };

	}();

    TableAdvanced.init();	

	 });
</script>
</body>
</html>