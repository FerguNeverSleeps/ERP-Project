<?php 
session_start();
ob_start();

require_once '../lib/common.php';
include ("func_bd.php");
//error_reporting(0);
$conexion=conexion();

$registro_id=$_POST['registro_id'];	
$op=$_POST['op'];

if ($op==3) // Se presiono el boton de Eliminar
{	
	$query  = "DELETE FROM nomaumentos_det WHERE cod_aumento=" . $registro_id;			
	$result = sql_ejecutar($query); // query($query,$conexion);
	activar_pagina("aumento_list.php");		 
}   
$sql = "SELECT * FROM nomaumentos_det";
$res = query($sql, $conexion);
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
  			// window.location.href="aumento_list.php?cod_eliminar="+valor
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
								<img src="../imagenes/21.png" width="22" height="22" class="icon"> Aumentos
							</div>
							<div class="actions">
								<a class="btn btn-sm blue"  onclick="javascript: window.location='aumento_agregar.php'">
									<i class="fa fa-plus"></i>
									Agregar
								</a>
								<a class="btn btn-sm blue"  onclick="javascript: window.location='menu_personal.php'">
									<i class="fa fa-arrow-left"></i> Regresar
								</a>
							</div>
						</div>
						<div class="portlet-body">
							<form action="" method="post" name="frmPrincipal" id="frmPrincipal">
							<table class="table table-striped table-bordered table-hover" id="table_datatable">
							<thead>
							<tr>
								<th>C&oacute;digo</th>
								<th>Descripci&oacute;n</th>
								<th>Estatus</th>
								<th>Fecha aplica</th>
								<th>&nbsp;</th>
								<th>&nbsp;</th>
							</tr>
							</thead>
							<tbody>
							<?php
								
								while( $fila=fetch_array($res) )  // $fila=fetch_array($resultado)
								{
									 $codigo=$fila['cod_aumento'];
								?>
									<tr class="odd gradeX">
										<td><?php echo $fila['cod_aumento']; ?></td>
										<td><?php echo $fila['descripcion']; ?></td>
										<td><?php echo $fila['estatus']; ?></td>
										<td><?php echo $fila['fecha_aplica']; ?></td>
										<?php
											if($fila['estatus']=="Pendiente")
											{ ?>
													<td style="text-align: center">
												      <a href="aumento_agregar.php?cod_modificar=<?php echo $codigo; ?>" title="Editar">
												      <img src="../../includes/imagenes/icons/pencil.png" width="16" height="16"></a>
													</td>
													<td style="text-align: center">
													      <a href="javascript:enviar(<?php echo(3); ?>,<?php echo $codigo; ?>);" title="Eliminar">
													      <img src="../../includes/imagenes/icons/delete.png" alt="Eliminar" width="16" height="16"></a>
								                    </td>
											  <?php
											}
											else
											{ ?>
													<td>&nbsp;</td>
													<td>&nbsp;</td>
											  <?php
											}
										?>
									</tr>
								  <?php							
								}
							?>
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
<?php include("../footer4.php"); ?>
<script type="text/javascript">
	 $(document).ready(function() { 

            $('#table_datatable').DataTable({
            	"iDisplayLength": 25,
                //"sPaginationType": "bootstrap",
            	"sPaginationType": "bootstrap_extended", 
                "oLanguage": {
                	"sSearch": "<i class='fa fa-search' style='color: #428bca;'></i> Buscar:",
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
                "aLengthMenu": [ // set available records per page
                    [5, 10, 25, 50,  -1],
                    [5, 10, 25, 50, "Todos"]
                ],                
                "aoColumnDefs": [
                    { 'bSortable': false, 'aTargets': [4] },
                    { "bSearchable": false, "aTargets": [4] },
                    { "sWidth": "8%", "aTargets": [4] },
                    { 'bSortable': false, 'aTargets': [5] },
                    { "bSearchable": false, "aTargets": [5] },
                    { "sWidth": "8%", "aTargets": [5] },
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