<?php 
require_once '../../generalp.config.inc.php';
session_start();
ob_start();

error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require_once '../lib/common.php';
include ("func_bd.php");
$conexion=conexion();

$codigo=  isset($_GET["codigo"]) ? $_GET["codigo"] : null ;

$sql = "SELECT * FROM nomcursos WHERE id=".$codigo;
$res = query($sql, $conexion);
?>


<html lang="es">
<!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
<meta charset="UTF-8">
<!-- BEGIN GLOBAL MANDATORY STYLES -->
<link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css"/>
<link href="../../includes/assets/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
<link href="../../includes/assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
<link href="../../includes/assets/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css"/>
<!-- END GLOBAL MANDATORY STYLES -->
<!-- BEGIN PAGE LEVEL STYLES -->
<link rel="stylesheet" type="text/css" href="../../includes/assets/plugins/select2/select2.css"/>
<link rel="stylesheet" type="text/css" href="../../includes/assets/plugins/select2/select2-metronic.css"/>
<link rel="stylesheet" href="../../includes/assets/plugins/data-tables/DT_bootstrap.css"/>
<link rel="stylesheet" type="text/css" href="../../includes/assets/plugins/clockface/css/clockface.css"/>
<link rel="stylesheet" type="text/css" href="../../includes/assets/plugins/bootstrap-datepicker/css/datepicker3.css"/>
<link rel="stylesheet" type="text/css" href="../../includes/assets/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css"/>
<link rel="stylesheet" type="text/css" href="../../includes/assets/plugins/bootstrap-colorpicker/css/colorpicker.css"/>
<link rel="stylesheet" type="text/css" href="../../includes/assets/plugins/bootstrap-daterangepicker/daterangepicker-bs3.css"/>
<link rel="stylesheet" type="text/css" href="../../includes/assets/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css"/>
<!-- END PAGE LEVEL STYLES -->
<!-- BEGIN THEME STYLES -->
<link href="../../includes/assets/css/style-metronic.css" rel="stylesheet" type="text/css"/>
<link href="../../includes/assets/css/style.css" rel="stylesheet" type="text/css"/>
<link href="../../includes/assets/css/style-responsive.css" rel="stylesheet" type="text/css"/>
<link href="../../includes/assets/css/plugins.css" rel="stylesheet" type="text/css"/>
<!-- <link href="../../includes/assets/css/themes/default.css" rel="stylesheet" type="text/css" id="style_color"/> -->
<link href="../../includes/assets/css/custom.css" rel="stylesheet" type="text/css"/>
<!-- END THEME STYLES -->
<link href="../../includes/assets/css/custom-header4.css" rel="stylesheet" type="text/css"/>
</head>
<!-- END HEAD -->
<!-- BEGIN BODY -->
<body class="page-full-width"  marginheight="0">
<input id="codigo" type="hidden" value="<?php echo $codigo; ?>">
<div class="page-container">
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
								Capacitación de Personal
							</div>							
							<div class="actions">
								<a class="btn btn-sm blue"  onclick="javascript: window.location='capacitacion_list.php'">
									<i class="fa fa-arrow-left"></i>
									 Regresar
								</a>
							</div>
						</div>
						<div class="portlet-body">
							<div class="row">
								<div class="form-group">
									<label class="col-md-2"><h4>Nombre del curso</h4></label>
									<div class="col-md-9">
									<?php
									$sql   = "SELECT * FROM nomcursos WHERE id=".$codigo;
									$res   = query($sql, $conexion);
									$fila1 = mysqli_fetch_array($res);
									echo "<h5>".$fila1[nombre_curso]."</h5>";
									?>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="form-group">
									<label class="col-md-2"><h4>Agregar colaborador</h4></label>
									<div class="col-md-9">
									<?php
									$tabla="nompersonal";
									$consulta="select * from ".$tabla." WHERE estado='ACTIVO'";
									$resultado=query($consulta,$conexion);
									?>
										<select id="personal" name="personal" class="form-control select2_category" autofocus>
										<option value=''>Seleccione...</option>
										<?php 
										while($fila=mysqli_fetch_array($resultado)){
										echo "<option value='".$fila["personal_id"]."'>".$fila["nombres"]." ".$fila["apellidos"]."</option>";}
										?>
										</select>
									</div>
								</div>
							</div>
							<div>
								<div class="form-actions text-center">
									<a id="sig1" class="btn blue button-next">AGREGAR</a>
								</div>
							</div>
							<div class="form-action">
								<table class="table table-striped table-bordered table-hover" id="table_datatable">
									<thead>
									<tr>
										<th>Cédula</th>
										<th>Nombres y Apellidos</th>
										<th>&nbsp;</th>
									</tr>
									</thead>
									<tbody>
									<?php
										$SQL = "SELECT a.id, b.cedula, b.apenom, c.nombre_curso
									FROM nomcursos_personal AS a, nompersonal AS b, nomcursos AS c
									WHERE a.id_personal = b.personal_id
									AND a.id_curso = c.id AND id_curso=".$codigo;
										$res = query($SQL, $conexion);
										while($filas=fetch_array($res))
										{ 
											$id=$filas["id"];								
										?>
											<tr class="odd gradeX">
												<td><?php 										
												echo $filas[cedula]; ?></td>
												<td><?php
												echo $filas[apenom]; ?></td>
												<td style="text-align: center">
			
												<?php // icono("usuarios_delete.php?codigo=".$codigo, "Eliminar", "delete.gif"); 
													echo "<a  href='capacitacion_delete.php?codigo=".$codigo."&id=".$id."' title='Eliminar integrantes'>"; ?>
													<i class="fa fa-times"></i>
													</a>
												</td>
											</tr>
										  <?php
										}
									?>
									</tbody>
								</table>

							</div>
							<!-- END PORTLET BODY-->	
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



<!-- BEGIN JAVASCRIPTS(Load javascripts at bottom, this will reduce page load time) -->
<!-- BEGIN CORE PLUGINS -->
<!--[if lt IE 9]>
<script src="../../includes/assets/plugins/respond.min.js"></script>
<script src="../../includes/assets/plugins/excanvas.min.js"></script> 
<![endif]-->
<script src="../../includes/assets/plugins/jquery-1.10.2.min.js" type="text/javascript"></script>
<script src="../../includes/assets/plugins/jquery-migrate-1.2.1.min.js" type="text/javascript"></script>
<script src="../../includes/assets/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script src="../../includes/assets/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js" type="text/javascript"></script>
<script src="../../includes/assets/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
<script src="../../includes/assets/plugins/jquery.blockui.min.js" type="text/javascript"></script>
<script src="../../includes/assets/plugins/jquery.cokie.min.js" type="text/javascript"></script>
<script src="../../includes/assets/plugins/uniform/jquery.uniform.min.js" type="text/javascript"></script>
<script src="../../includes/assets/scripts/core/app1.js"></script>

<!-- END CORE PLUGINS -->
<!-- BEGIN PAGE LEVEL PLUGINS -->
<script type="text/javascript" src="../../includes/assets/plugins/select2/select2.min.js"></script>
<script type="text/javascript" src="../../includes/assets/plugins/data-tables/jquery.dataTables.js"></script>
<script type="text/javascript" src="../../includes/assets/plugins/data-tables/DT_bootstrap.js"></script>
<!-- END PAGE LEVEL PLUGINS -->
<script type="text/javascript" src="../../includes/assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
<script type="text/javascript" src="../../includes/assets/plugins/bootstrap-datepicker/js/locales/bootstrap-datepicker.es.js"></script>
<script type="text/javascript" src="../../includes/assets/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js"></script>
<script type="text/javascript" src="../../includes/assets/plugins/clockface/js/clockface.js"></script>
<script type="text/javascript" src="../../includes/assets/plugins/bootstrap-daterangepicker/moment.min.js"></script>
<script type="text/javascript" src="../../includes/assets/plugins/bootstrap-daterangepicker/daterangepicker.js"></script>
<script type="text/javascript" src="../../includes/assets/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.js"></script>
<script type="text/javascript" src="../../includes/assets/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"></
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="../../includes/assets/scripts/core/app1.js"></script>
<!-- <script src="../../includes/assets/scripts/custom/table-managed.js"></script> -->
<script>
jQuery(document).ready(function() {      
window.scrollTo(0,0);
   $("#sig1").on("click", function()
   {
   		var codigo=$("#codigo").val();
   		var personal=$("#personal").val();
	
		if(personal =='')
		{
			alert("Agregue un colaborador");
		}
		else
		{
			location.href="capacitar.php?codigo="+codigo+"&personal="+personal;
		}
   });

    $('#table_datatable').DataTable({
    	"iDisplayLength": 10,
    	
    	"oSearching": false,
    	"sPaginationType": "bootstrap_extended", 
        "oLanguage": {
        	"sSearch": "<img src='../../includes/imagenes/icons/magnifier.png' width='16' height='16' > Buscar:",
            "sLengthMenu": "Mostrar _MENU_",
            "sInfoEmpty": "",
            "sInfo":"Total _TOTAL_ registros",
            "sInfoFiltered": "",
  		    "sEmptyTable":  "No hay datos disponibles",//"No data available in table",
            "sZeroRecords": "No se encontraron registros",//"No matching records found",

            "oPaginate": {
                "sPrevious": "P&aacute;gina Anterior",//"Prev",
                "sNext": "P&aacute;gina Siguiente",//"Next",
                "sPage": "P&aacute;gina",//"Page",
                "sPageOf": "de",//"of"
            }
        },
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