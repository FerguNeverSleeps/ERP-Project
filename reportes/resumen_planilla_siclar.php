<?php
session_start();
ob_start();
date_default_timezone_set('America/Panama');

require_once('../nomina/lib/database.php');
$db = new Database($_SESSION['bd']);

$sql = "SELECT DISTINCT b.Descripcion as descripcion, SUM(a.suesal) as saltotal,COUNT(a.personal_id) as todos FROM nompersonal AS a, departamento AS b WHERE a.IdDepartamento = b.IdDepartamento GROUP BY descripcion";
$result = $db->query($sql);

?>
<?php include("../includes/dependencias.php");?>
<style>
	tbody{
		font-size: 0.8em;
	}
</style>
<div class="row">
	<div class="col-sm-12 col-md-12 col-lg-12">
		<!-- COMIENZO DEL CONTENIDO-->
		<div class="row">
			<div class="col-md-12">
				<!-- COMIENZO DE LA TABLA-->
				<div class="portlet box blue">
					<div class="portlet-title">
						<div class="caption">
							<img src="../nomina/imagenes/21.png" width="22" height="22" class="icon"> Resumen de Planilla SICLAR
						</div>
						<div class="pull-right">
							<a class="btn btn-primary" href="planilla_siclar.php">
								<i class="fa fa-arrow-left"></i>
								Atras
							</a>&nbsp;
							<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal">
								<i class="fa fa-cloud-download"></i>
								Exportar
							</button>							
						</div>
					</div>
					<div class="portlet-body">
						<table class="table table-striped table-bordered table-hover" id="datatable">
							<thead>
								<tr>
									<th>Departamento</th>
									<th>Total de Salarios</th>
									<th>Total de Colaboradores</th>
								</tr>
							</thead>
							<tbody>
								<?php
								while($fila=mysqli_fetch_array($result))
								{
									?>
									<tr>
										<td><?php echo $fila["descripcion"];?></td>
										<td><?php echo $fila["saltotal"];?></td>
										<td><?php echo $fila["todos"];?></td>
									</tr>
									<?php
								}
								?>
							</tbody>
						</table>
					</div>
				</div>
				<!-- FIN DE LA TABLA-->
			</div>
		</div>
		<!-- FIN DE LA PAGINA DE CONTENIDO-->
	</div>
</div>
<!-- Ventana modal deexportacion -->
<!-- Modal -->
<div id="myModal" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Opciones de Exportacion</h4>
			</div>
			<div class="modal-body">
				<a class="btn btn-primary" href="<?php echo $_SESSION['LIVEURL']; ?>/reportes/pdf/pdf_resumen_planilla_siclar.php">
					<i class="fa fa-print"></i>
					PDF
				</a>&nbsp;
				<a class="btn btn-primary" href="<?php echo $_SESSION['LIVEURL']; ?>/reportes/word/word_resumen_planilla_siclar.php">
					<i class="fa fa-print"></i>
					WORD
				</a>&nbsp;
				<a class="btn btn-primary" href="<?php echo $_SESSION['LIVEURL']; ?>/reportes/excel/excel_resumen_planilla_siclar.php">
					<i class="fa fa-print"></i>
					EXCEL
				</a>
			</div>
			<div class="modal-footer">
				<a class="btn btn-primary" data-dismiss="modal">
					<i class="fa fa-remove"></i>
					Cerrar
				</a>
			</div>
		</div>
	</div>
</div>
<!-- FIn de Ventana modal deexportacion -->
<script type="text/javascript">
	$(document).ready(function() { 
    //$('#table_datatable').DataTable();
            // begin first table
            $('#datatable').DataTable({
              //"oSearch": {"sSearch": "Escriba frase para buscar"},
              "iDisplayLength": 10,
                //"sPaginationType": "bootstrap",
                "sPaginationType": "bootstrap_extended", 
              //"sPaginationType": "full_numbers",
              "oLanguage": {
              	"sSearch": "<img src='../includes/imagenes/icons/magnifier.png' width='16' height='16' > Buscar:",
              	"sLengthMenu": "Mostrar _MENU_",
                    //"sInfo": "Showing page _PAGE_ of _PAGES_", // Mostrando 1 to 5 de 18 entradas
                    //"sInfoEmpty": "No hay registros para mostrar",
                    "sInfoEmpty": "",
                    //"sInfo": "",
                    "sInfo":"Total _TOTAL_ registros",
                    "sInfoFiltered": "",
                  "sEmptyTable":  "No hay datos disponibles",//"No data available in table",
                    "sZeroRecords": "No se encontraron registros",//"No matching records found",
                    "oPaginate": {
                        "sPrevious": "P&aacute;gina Anterior",//"Prev",
                        "sNext": "P&aacute;gina Siguiente",//"Next",
                        "sPage": "P&aacute;gina",//"Page",
                        "sPageOf": "de",//"of"
                    },
                },
                "aLengthMenu": [ // set available records per page
                [5, 10, 25, 50,  -1],
                [5, 10, 25, 50, "Todos"]
                ],                
                "aoColumnDefs": [
                { 'bSortable': false, 'aTargets': [3] },
                { "bSearchable": false, "aTargets": [ 3 ] },
                { 'bSortable': false, 'aTargets': [2] },
                { "bSearchable": false, "aTargets": [ 2 ] },
                { "sWidth": "8%", "aTargets": [2] },
                { "sWidth": "8%", "aTargets": [3] }
                ],
                "fnDrawCallback": function() {
                	$('#datatable_filter input').attr("placeholder", "Escriba frase para buscar");
                }
            });

            $('#datatable').on('change', 'tbody tr .checkboxes', function(){
            	$(this).parents('tr').toggleClass("active");
            });

            $('#datatable .dataTables_filter input').addClass("form-control input-medium input-inline"); 
            $('#datatable .dataTables_length select').addClass("form-control input-xsmall"); 
        });
    </script>
</body>
</html>