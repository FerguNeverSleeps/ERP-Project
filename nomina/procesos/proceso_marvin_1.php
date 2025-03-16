<?php 
session_start();
ob_start();

require_once '../lib/common.php';
include ("../paginas/funciones_nomina.php");
include ("../paginas/func_bd.php");
// error_reporting(0);
$conexion=conexion();

$id=$_POST['id'];	
$op=$_POST['op'];

if ($op==3) //Se presiono el boton de Eliminar
{	
	$consulta = "DELETE 
	             FROM   marvin_encabezado_detalle 
	             WHERE  id_encabezado={$id} "; 

	$resultadoCon = query($consulta, $conexion); //  sql_ejecutar($query);

	$consulta = "DELETE 
	             FROM   marvin_encabezado
	             WHERE  id={$id} "; 

	$resultado = query($consulta, $conexion); 

	activar_pagina("proceso_marvin.php");	 
}   

$sql = "SELECT *
        FROM   marvin_encabezado
        WHERE  estatus=0
	ORDER BY fecha ASC";
		
$sql2 = "SELECT *
        FROM   marvin_encabezado
        WHERE  estatus=1
	ORDER BY fecha ASC";
$res = query($sql, $conexion);
$res2 = query($sql2, $conexion);
$total = 0;
$total2 = 0;
$id=array();
$fecha=array();
$fecha_inicio=array();
$fecha_fin=array();
$estatus=array();

$id2=array();
$fecha2=array();
$fecha_inicio2=array();
$fecha_fin2=array();
$estatus2=array();

while( $fila=fetch_array($res) )
{ 	array_push($id,$fila['id']);
	array_push($fecha,$fila['fecha']);
	array_push($fecha_inicio,$fila['fecha_inicio']);
	array_push($fecha_fin,$fila['fecha_fin']);
	$total++;
}

while( $fila2=fetch_array($res2) )
{ 	array_push($id2,$fila2['id']);
	array_push($fecha2,$fila2['fecha']);
	array_push($fecha_inicio2,$fila2['fecha_inicio']);
	array_push($fecha_fin2,$fila2['fecha_fin']);
	$total2++;
}
?>
<?php include("../header4.php"); // <html><head></head><body> ?>
<link href="../../includes/assets/css/custom-datatables.css" rel="stylesheet" type="text/css"/>
<style type="text/css">
  .portlet > .portlet-title > .actions > .btn.btn-sm {
    margin-top: -9px !important;
  }
</style>
<script type="text/javascript">
function enviar(op,id)
{
	if (op==3)
	{		// Opcion cancelar préstamo
		if (confirm("\u00BFEst\u00E1 seguro que desea finalizar este pr\u00E9stamo?"))
		{		
			console.log(op+" "+id);			
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
							  <img src="../imagenes/21.png" width="20" height="20" class="icon"> Marvin - Procesamiento
							</div>
							<div class="actions">
<!--								<a class="btn btn-sm blue"  onclick="javascript: window.location='../../reportes/excel/resumen_prestamos.php'">
									<i class="fa fa-print"></i>
									Imprimir
								</a>-->
								<a class="btn btn-sm blue"  onclick="javascript: window.location='proceso_marvin_agregar.php'">
									<i class="fa fa-plus"></i>
									Agregar
								</a>
<!--								<a class="btn btn-sm blue"  onclick="javascript: window.location='menu_prestamos.php'">
									<i class="fa fa-arrow-left"></i> Regresar
								</a>-->
							</div>
						</div>
						<div class="portlet-body">
							
						<ul class="nav nav-tabs">
								<li class="active"><a href="#tab_0" id="tab-personal" data-toggle="tab">Pendientes</a></li>                                                               
								<li><a href="#tab_1" id="tab-egresados" data-toggle="tab">Procesados</a></li>
								
						</ul>
						<div class="tab-content">
							<div class="tab-pane active" id="tab_0">
								<form action="" method="post" name="frmPrincipal" id="frmPrincipal">
								<table class="table table-striped table-bordered table-hover" id="table_datatable">
								<thead>
								<tr>
									<th>ID</th>
                                                                        <th>Fecha</th>
                                                                        <th>Inicio</th>
                                                                        <th>Fin</th>
									<th>&nbsp;</th>
									<th>&nbsp;</th>
								</tr>
								</thead>
								<tbody>
								<?php								
									for($i=0;$i<$total;$i++)
									{ 	$id=$id[$i];
									?>
										<tr class="odd gradeX" id="num" opcion="<?php echo $id;?>">
											<td><?php echo $id;?></td>
											<td><?php echo $fecha[$i];?></td>
											<td><?php echo $fecha_inicio[$i];?></td>
                                                                                        <td><?php echo $fecha_fin[$i];?></td>
											<?php
												if($estatus[$i] == 0)
												{
												?>
													<td style="text-align: center; width: 22px !important">
													<a href="proceso_marvin_editar.php?id=<?php echo $id; ?>" title="Editar">
													<img src="../../includes/imagenes/icons/pencil.png" width="16" height="16"></a>
													</td>
													
													<td style="text-align: center; width: 22px !important">
														<a href="proceso_marvin_eliminar.php?id=<?php echo $id; ?>" title="Eliminar">
														<img src="../imagenes/delete.gif" alt="Eliminar" width="16" height="16"></a>
													</td>
												<?php
												}
												else
												{
												?>
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
							<div class="tab-pane" id="tab_1">
							<form action="" method="post" name="frmPrincipal2" id="frmPrincipal2">
								<table class="table table-striped table-bordered table-hover" id="table_datatable2">
								<thead>
								<tr>
									<th>ID</th>
                                                                        <th>Fecha</th>
                                                                        <th>Inicio</th>
                                                                        <th>Fin</th>
									<th>&nbsp;</th>
									<th>&nbsp;</th>
								</tr>
								</thead>
								<tbody>
								<?php								
									for($i=0;$i<$total2;$i++)
									{ 	$id2=$id2[$i];
									?>
										<tr class="odd gradeX" id="num" opcion="<?php echo $id2;?>">
											<td><?php echo $id2;?></td>
											<td><?php echo $fecha2[$i];?></td>
											<td><?php echo $fecha_inicio2[$i];?></td>
                                                                                        <td><?php echo $fecha_fin2[$i];?></td>
											
											<?php
												if($estatus2[$i] == 0)
												{
												?>
													<td style="text-align: center; width: 22px !important">
													<a href="proceso_marvin_editar.php?id=<?php echo $id2; ?>" title="Editar">
													<img src="../../includes/imagenes/icons/pencil.png" width="16" height="16"></a>
													</td>
													
													<td style="text-align: center; width: 22px !important">
														<a href="proceso_marvin_eliminar.php?id=<?php echo $id2; ?>" title="Eliminar">
														<img src="../imagenes/delete.gif" alt="Eliminar" width="16" height="16"></a>
													</td>
												<?php
												}
												else
												{
												?>
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
									<input name="registro_id_2" type="hidden" value="">
									<input name="op_2" type="hidden" value="">	
								</form>
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
	 $(document).ready(function() { 
		

            $('#table_datatable').DataTable({
            	"iDisplayLength": 25,
            	"bStateSave" : true,
                //"sPaginationType": "bootstrap",
            	"sPaginationType": "bootstrap_extended", 
            	"aaSorting": [[ 1, "asc" ]], 
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
                "aLengthMenu": [ // set available records per page
                    [5, 10, 25, 50,  -1],
                    [5, 10, 25, 50, "Todos"]
                ],                
                "aoColumnDefs": [
                    { 'bSortable': false, 'aTargets': [4] },
                    { "bSearchable": false, "aTargets": [4] },
                   // { "sWidth": "8%", "aTargets": [6] },
                    { 'bSortable': false, 'aTargets': [5] },
                    { "bSearchable": false, "aTargets": [5] },
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
            
            $('#table_datatable2').DataTable({
            	"iDisplayLength": 25,
            	"bStateSave" : true,
                //"sPaginationType": "bootstrap",
            	"sPaginationType": "bootstrap_extended", 
            	"aaSorting": [[ 1, "asc" ]], 
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
                "aLengthMenu": [ // set available records per page
                    [5, 10, 25, 50,  -1],
                    [5, 10, 25, 50, "Todos"]
                ],                
                "aoColumnDefs": [
                    { 'bSortable': false, 'aTargets': [4] },
                    { "bSearchable": false, "aTargets": [4] },
                   // { "sWidth": "8%", "aTargets": [6] },
                    { 'bSortable': false, 'aTargets': [5] },
                    { "bSearchable": false, "aTargets": [5] },
                ],
				 "fnDrawCallback": function() {
				        $('#table_datatable2_filter input').attr("placeholder", "Escriba frase para buscar");
				 }
            });

            $('#table_datatable2').on('change', 'tbody tr .checkboxes', function(){
                 $(this).parents('tr').toggleClass("active");
            });
            $('#table_datatable2_wrapper .dataTables_filter input').addClass("form-control input-medium input-inline"); 
            $('#table_datatable2_wrapper .dataTables_length select').addClass("form-control input-xsmall"); 
	 });
</script>
</body>
</html>