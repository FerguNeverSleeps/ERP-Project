<?php 
require_once '../../generalp.config.inc.php';
session_start();
ob_start();

// error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require_once '../lib/common.php';
$conexion=conexion();

$nivel= 1 ;
?>
<?php include("../header4.php"); // <html><head></head><body> ?>
<link href="../../includes/assets/css/custom-datatables.css" rel="stylesheet" type="text/css"/>
<div class="page-container">
	<!-- BEGIN SIDEBAR -->
	<!-- END SIDEBAR -->
	<!-- BEGIN CONTENT -->
	<div class="page-content-wrapper">
		<div class="page-content">
			<!-- BEGIN PAGE CONTENT-->
			<div class="row">
				<div class="col-md-12">
					<div class="tabbable tabbable-custom boxless tabbable-reversed">

						<div class="tab-content">
					  		
							<!-- BEGIN EXAMPLE TABLE PORTLET-->
							<div class="portlet box blue">
								<div class="portlet-title">
									<div class="caption">
									 Planificación Presupuestaria
									</div>
									<div class="actions">
										<a class="btn btn-sm blue"  onclick="javascript: window.location='presupuesto.php'">
											<i class="fa fa-arrow-left"></i>
											 Regresar
										</a>
									</div>
								</div>
								<div class="portlet-body">
									<div class="row">
										<div class="form-group">
											<label class="col-md-2"><h4>Nivel</h4></label>
											<div class="col-md-9">
											<?php
											$SQL   = "SELECT nomniv$nivel as nivel FROM nomempresa";
											$res   = query($SQL, $conexion);
											$fila1 = mysqli_fetch_array($res);
											echo "<h5>".$fila1[nivel]."</h5>";
											?>
											</div>
										</div>
									</div>

									<table class="table table-striped table-bordered table-hover" id="table_datatable">
										<thead>
										<tr>
											<th>Descripción</th>

											<th>&nbsp;</th>
										</tr>
										</thead>
										<tbody>
										<?php
	
											$SQL="SELECT * FROM nomnivel$nivel";
											$resultado=query($SQL,$conexion);										
											while ($rows = fetch_array($resultado)) {

												//echo $SQL,"<BR>";
												?>
												<tr class="odd gradeX">
													
													<td><?php echo $rows[descrip]; ?></td>

													<td style="text-align: center">
				
														<?php 
															echo "<a  href='presupuesto_cuentas_add.php?codigo=".$rows[codorg]."&nivel=".$nivel."' title='Agregar Presupuesto'>"; ?>
														<i class="fa fa-plus"></i>
														</a>
													</td>
												</tr>
											  <?php
											}
										?>
										</tbody>
									</table>
								</div>
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
window.scrollTo(0,0);
	 $(document).ready(function() { 
	 	//$('#table_datatable').DataTable(); 

            // begin first table
            $('#table_datatable').DataTable({
            	//"oSearch": {"sSearch": "Escriba frase para buscar"},
            	"iDisplayLength": 10,
                //"sPaginationType": "bootstrap",
            	
            	"oSearching": false,
            	"sPaginationType": "bootstrap_extended", 
            	//"sPaginationType": "full_numbers",
                "oLanguage": {
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
                    "oPaginate": {
                        "sPrevious": "P&aacute;gina Anterior",//"Prev",
                        "sNext": "P&aacute;gina Siguiente",//"Next",
                        "sPage": "P&aacute;gina",//"Page",
                        "sPageOf": "de",//"of"
                    }
                },
                /*
                "aLengthMenu": [
                    [5, 15, 20, -1],
                    [5, 15, 20, "Todos"] // change per page values here
                ],
                */
                "aLengthMenu": [ // set available records per page
                    [5, 10, 25, 50,  -1],
                    [5, 10, 25, 50, "Todos"]
                ],                
                "aoColumnDefs": [
                    { 'bSortable': false, 'aTargets': [2] },
                    { "bSearchable": false, "aTargets": [ 2 ] },
                    { 'bSortable': false, 'aTargets': [2] },
                    { "bSearchable": false, "aTargets": [ 2 ] },
                    { "sWidth": "8%", "aTargets": [2] },
                    { "sWidth": "8%", "aTargets": [2] }
                ],
				 "fnDrawCallback": function() {
				        $('#table_datatable_filter input').attr("placeholder", "Escriba frase para buscar");
				 }
                /*
	            "aoColumns": [
			      null,
			      null,
			      { "sWidth": "10px" },
			      { "sWidth": "10px" },
				]*/
            	/*
                "aoColumns": [
                  { "bSortable": false },
                  null,
                  { "bSortable": false, "sType": "text" },
                  null,
                  { "bSortable": false },
                  { "bSortable": false }
                ],*/
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