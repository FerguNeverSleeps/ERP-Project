<?php 
session_start();
ob_start();

require_once '../lib/common.php';
include ("func_bd.php");
$conexion=conexion();
// error_reporting(0);

$sql = "SELECT cod_log, descripcion, fecha_hora, modulo, url, accion, valor, usuario 
        FROM log_transacciones";
$res = query($sql, $conexion);
?>
<?php include("../header4.php"); // <html><head></head><body> ?>
<link href="../../includes/assets/css/custom-datatables.css" rel="stylesheet" type="text/css"/>
<style type="text/css">
  .portlet > .portlet-title > .actions > .btn.btn-sm {
    margin-top: -9px !important;
  }
</style>
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
								Log Transacciones
							</div>
						</div>
						<div class="portlet-body">
							<form action="" method="post" name="frmPrincipal" id="frmPrincipal">
							<table class="table table-striped table-bordered table-hover" id="table_datatable">
							<thead>
							<tr>
								<th>Id</th>
								<th>Descripci&oacute;n</th>
								<th>Fecha</th>
								<th>M&oacute;dulo</th>
								<th>Url</th>
								<th>Acci&oacute;n</th>
								<th>Valor</th>
								<th>Usuario</th>
							</tr>
							</thead>
							<tbody>
							<?php
								
								while( $fila=fetch_array($res) )  
								{ 
								?>
									<tr class="odd gradeX">
										<td><?php echo $fila['cod_log']; ?></td>
										<td><?php echo $fila['descripcion']; ?></td>
										<td><?php echo $fila['fecha_hora']; ?></td>
										<td><?php echo $fila['modulo']; ?></td>
										<td><?php echo $fila['url']; ?></td>
										<td><?php echo $fila['accion']; ?></td>
										<td><?php echo $fila['valor']; ?></td>
										<td><?php echo $fila['usuario']; ?></td>
									</tr>
								  <?php							
								}
							?>
							</tbody>
							</table>	
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
<?php include("../footer4.php"); ?>
<script type="text/javascript">
	 $(document).ready(function() { 

            $('#table_datatable').DataTable({

            	"iDisplayLength": 25,
                //"sPaginationType": "bootstrap",
            	"sPaginationType": "bootstrap_extended", 
                "oLanguage": {
                	"sSearch": "<img src='../../includes/imagenes/icons/magnifier.png' width='16' height='16' > Buscar:",
                    "sLengthMenu": "Mostrar _MENU_",
                    //"sInfo": "Showing page _PAGE_ of _PAGES_", // Mostrando 1 to 5 de 18 entradas
                    //"sInfoEmpty": "No hay registros para mostrar",
                    "sInfoEmpty": "",
                    //"sInfo": "",
                    "sInfo":"Total _TOTAL_ registros",
                    "sInfoFiltered": "",
          		    "sEmptyTable":  "No hay datos disponibles", // No hay datos para mostrar
                    "sZeroRecords": "No se encontraron registros",
                    "oPaginate": {
                        "sPrevious": "P&aacute;gina Anterior",//"Prev",
                        "sNext": "P&aacute;gina Siguiente",//"Next",
                        "sPage": "P&aacute;gina",//"Page",
                        "sPageOf": "de",//"of"
                    }
                },  
                "aaSorting": [[ 2, "desc" ]],             
                "aLengthMenu": [ // set available records per page
                    [5, 10, 25, 50,  -1],
                    [5, 10, 25, 50, "Todos"]
                ],                
				 "fnDrawCallback": function() {
				        $('#table_datatable_filter input').attr("placeholder", "Escriba frase para buscar");
				 }
            });

            $('#table_datatable').on('change', 'tbody tr .checkboxes', function(){
                 $(this).parents('tr').toggleClass("active");
            });

            $('#table_datatable_wrapper .dataTables_filter input').addClass("form-control input-medium input-inline"); 
            $('#table_datatable_wrapper .dataTables_length select').addClass("form-control input-xsmall"); 
	 });
</script>
</body>
</html>