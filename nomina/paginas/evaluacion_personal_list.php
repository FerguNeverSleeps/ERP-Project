<?php 
require_once '../../generalp.config.inc.php';
session_start();
ob_start();

// error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require_once '../lib/common.php';
$conexion=conexion();

$sql = "SELECT * FROM nom_eval_personal";
$res = query($sql, $conexion);
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
					<!-- BEGIN EXAMPLE TABLE PORTLET-->
					<div class="portlet box blue">
						<div class="portlet-title">
							<div class="caption">
								<!-- <i class="fa fa-globe"></i> -->
								<img src="../imagenes/21.png" width="22" height="22" class="icon"> Evaluación 360
							</div>
							<div class="actions">
								<a class="btn btn-sm blue"  onclick="javascript: window.location='evaluacion_personal.php'">
									<i class="fa fa-plus"></i>
									<!-- <img src="../imagenes/add.gif" width="16" height="16"> --> Agregar
								</a>
							</div>
						</div>
						<div class="portlet-body">
							<div class="table-toolbar" style="display: none">
								<div class="btn-group">&nbsp;
									<!--<button id="sample_editable_1_new" class="btn green">
									Add New <i class="fa fa-plus"></i>
									</button>-->
								</div>
								<div class="btn-group pull-right">
									<button class="btn btn-sm blue" onclick="javascript: window.location='evaluacion_personal.php'">
									<i class="fa fa-plus"></i>
									<!-- <img src="../../includes/imagenes/icons/add.png"  width="16" height="16">--> Agregar
									</button>
									<!--
									<button class="btn dropdown-toggle" data-toggle="dropdown">Tools <i class="fa fa-angle-down"></i>
									</button>
									<ul class="dropdown-menu pull-right">
										<li>
											<a href="#">
												 Print
											</a>
										</li>
										<li>
											<a href="#">
												 Save as PDF
											</a>
										</li>
										<li>
											<a href="#">
												 Export to Excel
											</a>
										</li>
									</ul>
									-->
								</div>
							</div>
							<table class="table table-striped table-bordered table-hover" id="table_datatable">
							<thead>
							<tr>
								<th>Evaluado</th>
								<th>Fecha</th>
								<th>Estado</th>
								<th>&nbsp;</th>
							</tr>
							</thead>
							<tbody>
							<?php
								while($fila=fetch_array($res))
								{ 
									$codigo=$fila["id"];								
								?>
									<tr class="odd gradeX">
										<td><?php $sql2 = "SELECT apenom FROM nompersonal where personal_id ='".$fila["id_personal"]."'";
										$res2 = query($sql2, $conexion);
										$fila2=fetch_array($res2);
										echo $fila2[apenom]; ?></td>
										<td><?php 	echo $fila[fecha_personal]; ?></td>
										<td><?php 	echo $fila[Estado]; ?></td>

										<td style="text-align: center">
										<?php // icono("usuarios_delete.php?codigo=".$codigo, "Eliminar", "delete.gif"); ?>
											<a href="ver_eval_personal.php?codigo=<?php echo $codigo; ?>" title="Eliminar">
												<img src="../../includes/imagenes/icons/eye.png" width="16" height="16">
											</a>
										<?php // icono("usuarios_delete.php?codigo=".$codigo, "Eliminar", "delete.gif"); ?>
											<a href="aprobar_eval_personal.php?codigo=<?php echo $codigo; ?>" title="Aprobar">
												<img src="../../includes/imagenes/ico_list.gif" width="16" height="16">
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
	 	//$('#table_datatable').DataTable(); 

            // begin first table
            $('#table_datatable').DataTable({
            	//"oSearch": {"sSearch": "Escriba frase para buscar"},
            	"iDisplayLength": 10,
                //"sPaginationType": "bootstrap",
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
                    { 'bSortable': false, 'aTargets': [3] },
                    { "bSearchable": false, "aTargets": [ 3 ] },
                    { 'bSortable': false, 'aTargets': [2] },
                    { "bSearchable": false, "aTargets": [ 2 ] },
                    { "sWidth": "8%", "aTargets": [2] },
                    { "sWidth": "8%", "aTargets": [3] }
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