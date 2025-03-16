<?php
session_start();
ob_start();

require_once '../lib/common.php';
include ("func_bd.php");
//error_reporting(E_ALL ^ E_DEPRECATED);
$conexion=new bd($_SESSION['bd']);

$termino=(isset($_SESSION['termino']))?$_SESSION['termino']:'';
$registro_id=(isset($_POST['registro_id'])) ? $_POST['registro_id'] : '';
$op=(isset($_POST['op'])) ? $_POST['op'] : '';

if ($_GET['cedula']) //Se presiono el boton de Eliminar
{
	$sql = "DELETE FROM nomelegibles
	        WHERE cedula='".$_GET['cedula']."'";
	$conexion->query($sql);

	activar_pagina("elegibles_list.php");
}

$sql = "SELECT * FROM nomelegibles";
$res = $conexion->query($sql, "utf8");
?>
<?php include("../header4.php"); ?>
<link href="../../includes/assets/css/custom-datatables.css" rel="stylesheet" type="text/css"/>
<style type="text/css">
  .portlet > .portlet-title > .actions > .btn.btn-sm {
    margin-top: -9px !important;
  }
  .text-middle{
  	vertical-align: middle !important
  }

  .ajustar-texto
  {
  	white-space: normal !important;
  }

  td.icono
  {
  	padding-left: 0px !important;
  	padding-right: 0px !important;
  	width: 10px !important;
  }
</style>
<script type="text/javascript" src="../lib/common.js"></script>
<script type="text/javascript">
function GenerarNomina()
{
	AbrirVentana('barraprogreso_1.php', 150, 500, 0);
}

function CerrarVentana()
{
	javascript:window.close();
}

function showProcesando(){
	//console.log("Mostrar Procesando");
    App.blockUI({
        target: '#blockui_portlet_body',
        boxed: true,
        message: 'Procesando'
    });
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
								<a href="nomina_de_pago.php"><img src="images/Clipboard.png" width="23" height="21"/></a>
								Elegibles
							</div>
							<div class="actions">
								<!--
								<a class="btn btn-sm blue" data-toggle="modal" data-target="#myModal">
									<i class="fa fa-cloud-upload"></i>
									Adjuntar Curriculo
								</a>
								-->
								<a class="btn btn-sm blue"  onclick="javascript: window.location='elegibles_buscar.php?ban=1'">
									<i class="fa fa-search"></i> Búsqueda Avanzada</a>
								<a class="btn btn-sm blue"  onclick="javascript: window.location='agregar_curriculo.php?ban=1'">
									<i class="fa fa-plus"></i>
									Agregar
								</a>
							</div>
						</div>
						<div class="portlet-body" id="blockui_portlet_body">
							<form action="" method="post" name="frmPrincipal" id="frmPrincipal">
							<table class="table table-striped table-bordered table-hover" id="table_datatable">
							<thead>
							<tr>
								<th class="text-center text-middle">N°</th>
								<th class="text-center text-middle">Apellidos</th>
								<th class="text-center text-middle">Nombres</th>
								<th class="text-center text-middle">Cedula</th>
								<th class="text-center text-middle">Correo</th>
								<th class="text-center text-middle">Fecha de Registro</th>
								<th class="text-center text-middle">Acciones</th>
							</tr>
							</thead>
							<tbody>
							<?php
								$i=1;
								while( $fila = $res->fetch_assoc() )
								{
								?>
									<tr class="odd gradeX">
										<td class="text-center ajustar-texto" style="max-width: 20px !important;">
											<?php echo $i++;?>
										</td>
										<td class="text-center ajustar-texto" style="max-width: 100px !important;">
											<?php echo $fila['apellidos']; ?>
										</td>
										<td class="text-center ajustar-texto" style="max-width: 100px !important;">
											<?php echo $fila['nombres']; ?>
										</td>
										<td class="text-center ajustar-texto" style="max-width: 100px !important;">
											<?php echo $fila['cedula']; ?>
										</td>
										<td class="text-center ajustar-texto" style="max-width: 100px !important;">
											<?php echo $fila['email']; ?>
										</td>
										<td class="text-center ajustar-texto" style="max-width: 100px !important;">
											<?php echo $fila['fecha_reg']; ?>
										</td>
										<td class="text-center ajustar-texto" style="max-width: 80px !important;">
<!--
                 							<a href="incorporar_persona.php?cedula=<?php echo $fila['cedula'];?>" title="Añadir"> 
                 							<img src="../../includes/imagenes/icons/chart_organisation_add.png" alt="Añadir" width="16" height="16"></a>
-->
											<a href="editar_curriculo.php?cedula=<?php echo $fila['cedula'];?>" title="Editar"> 
                 							<img src="../../includes/imagenes/icons/pencil.png" alt="Editar" width="16" height="16"></a>
                 							<a href="elegibles_list.php?cedula=<?php echo $fila['cedula'];?>" title="Eliminar"> 
                 							<img src="../../includes/imagenes/icons/delete.png" alt="Eliminarr" width="16" height="16"></a>
										</td>
									</tr>
								  <?php
								}
							?>
							</tbody>
							</table>
							    <input name="codigo_nomina" type="hidden" value="">
								<input name="registro_id" type="hidden" value="">
								<input name="op" type="hidden" value="">
								<input name="marcar_todos" type="hidden" value="1">
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
    	"sPaginationType": "bootstrap_extended",
        "oLanguage": {
        	"sSearch": "<img src='../../includes/imagenes/icons/magnifier.png' width='16' height='16' > Buscar:",
            "sLengthMenu": "Mostrar _MENU_",
            "sInfoEmpty": "",
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
        "aLengthMenu": [ // set available records per page
            [5, 10, 25, 50,  -1],
            [5, 10, 25, 50, "Todos"]
        ],
        "aoColumnDefs": [
        	{ 'bSortable': false, 'aTargets': [0, 1, 2, 3, 4, 5] }, // 'bVisible': false,
            { 'bSortable': false, 'bSearchable': false, 'aTargets': [0, 1, 2, 3, 4, 5] }
        ],
		"fnDrawCallback": function() {
		    $('#table_datatable_filter input').attr("placeholder", "Escriba frase para buscar");
		}
	});

	$('#table_datatable_wrapper .dataTables_filter input').addClass("form-control input-medium input-inline");
	$('#table_datatable_wrapper .dataTables_length select').addClass("form-control input-xsmall");
});
</script>
</body>
</html>

<!-- Ventana modal deexportacion -->
<!-- Modal -->
<!--
<div id="myModal" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<form action="importar_curriculo.php" method="post" enctype="multipart/form-data">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Importar Curriculo</h4>
			</div>
			<div class="modal-body">
				<div class="form-group">
  					<label for="usr">Nombre:</label>
                    <input type="text" class="form-control" name="nombres" id="nombres" placeholder="Ingrese su Nombre" required>
				</div>
				<div class="form-group">
  					<label for="usr">Apellido:</label>
                    <input type="text" class="form-control" name="apellidos" id="apellidos" placeholder="Ingrese su Apellido" required>
				</div>
				<div class="form-group">
  					<label for="usr">Cedula:</label>
                    <input type="text" class="form-control" name="cedula" id="cedula" placeholder="Ingrese su Cedula" required>
				</div>
				<div class="form-group">
  					<label for="usr">Correo:</label>
                    <input type="email" class="form-control" name="email" id="email" placeholder="Ingrese su Correo" required>
				</div>
				<div class="form-group">
  					<label for="usr">Archivo:</label>
                    <input type="file" name="archivo" id="archivo" required>
				</div>
			</div>
			<div class="modal-footer">
				<input type="submit" class="btn btn-primary" name="importar" value="Enviar">
				<a class="btn btn-primary" data-dismiss="modal">
					<i class="fa fa-remove"></i>
					Cerrar
				</a>
			</div>
		</div>
		</form>
	</div>
</div>
-->
<!-- FIn de Ventana modal deexportacion -->
