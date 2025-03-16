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
	             FROM   zoho_contable_detalle 
	             WHERE  id_encabezado={$id} "; 

	$resultadoCon = query($consulta, $conexion); //  sql_ejecutar($query);

	$consulta = "DELETE 
	             FROM   zoho_contable_cabecera
	             WHERE  id={$id} "; 

	$resultado = query($consulta, $conexion); 

	activar_pagina("zoho_contable_proceso_ach_planilla.php");	 
}   

$sql_pendientes = "
	SELECT 
		nnp.*
    FROM nom_nominas_pago as nnp
    	LEFT JOIN zoho_contable_cabecera as zcc ON (nnp.codnom=zcc.cod_planilla AND nnp.codtip=zcc.tipo_planilla AND zcc.tipo=2 AND zcc.estatus!=2)
    WHERE  
    	status='C' AND
    	zcc.cod_planilla IS NULL
	ORDER BY 
		codnom DESC";


$sql_fallidas = "SELECT *
        FROM   zoho_contable_cabecera
        WHERE  estatus=0 AND tipo=2
	ORDER BY fecha_creacion DESC";

$sql_procesadas = "SELECT *
        FROM   zoho_contable_cabecera
        WHERE  estatus=1 AND tipo=2
	ORDER BY fecha_creacion DESC";
		
$sql_anuladas = "SELECT *
        FROM   zoho_contable_cabecera
        WHERE  estatus=2 AND tipo=2
	ORDER BY fecha_creacion DESC";

$res_pendientes = query($sql_pendientes, $conexion);
$res_fallidas = query($sql_fallidas, $conexion);
$res_procesadas = query($sql_procesadas, $conexion);
$res_anuladas = query($sql_anuladas, $conexion);

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
?>
<?php include("../header4.php"); // <html><head></head><body> ?>
<link href="../../includes/assets/css/custom-datatables.css" rel="stylesheet" type="text/css"/>
<style type="text/css">
  .portlet > .portlet-title > .actions > .btn.btn-sm {
    margin-top: -9px !important;
  }
  .table_datatable {

  }
  #table_datatable td.text-normal {
  	white-space: normal;
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
							  <img src="../imagenes/21.png" width="20" height="20" class="icon"> ZOHO CONTABLE - Proceso ACH
							</div>
							<div class="actions">
<!--								<a class="btn btn-sm blue"  onclick="javascript: window.location='../../reportes/excel/resumen_prestamos.php'">
									<i class="fa fa-print"></i>
									Imprimir
								</a>-->
<!--								<a class="btn btn-sm blue"  onclick="javascript: window.location='proceso_marvin_agregar.php'">
									<i class="fa fa-plus"></i>
									Agregar
								</a>-->
<!--								<a class="btn btn-sm blue"  onclick="javascript: window.location='menu_prestamos.php'">
									<i class="fa fa-arrow-left"></i> Regresar
								</a>-->
							</div>
						</div>
						<div class="portlet-body">
							
						<ul class="nav nav-tabs">
                                                                <li class="active"><a href="#tab_pendientes" id="tab-pendientes" data-toggle="tab">Pendientes</a></li>
                                                                <li><a href="#tab_fallidas" id="tab-fallidas" data-toggle="tab">Fallidas</a></li>
								<li><a href="#tab_procesadas" id="tab-procesadas" data-toggle="tab">Procesadas</a></li>                                                               
								<li><a href="#tab_anuladas" id="tab-anuladas" data-toggle="tab">Anuladas</a></li>
								
						</ul>
						<div class="tab-content">
                                                        <div class="tab-pane active" id="tab_pendientes">
								<form action="" method="post" name="frmPrincipal" id="frmPrincipal">
								<table class="table table-striped table-bordered table-hover" id="table_datatable_pendientes">
								<thead>
								<tr>									
                                                                        <th>Fecha</th>
                                                                        <th>Cod. Planilla</th>
                                                                        <th>Tipo Planilla</th>
                                                                        <th>Descripcion</th>
                                                                        <th>Fecha Inicio</th>
                                                                        <th>Fecha Fin</th>
									<th width="1%">&nbsp;</th>
								</tr>
								</thead>
								<tbody>
								<?php								
									while( $fila_pendientes=fetch_array($res_pendientes) )
									{ 	
									?>
										<tr class="odd gradeX" id="num" opcion="<?php echo $fila_pendientes['codnom'];?>">
											<td><?php echo $fila_pendientes['fechapago'];?></td>
                                                                                        <td><?php echo $fila_pendientes['codnom'];?></td>
                                                                                        <td><?php echo $fila_pendientes['codtip'];?></td>
                                                                                        <td class="text-normal"><?php echo $fila_pendientes['descrip'];?></td>
                                                                                        <td><?php echo $fila_pendientes['periodo_ini'];?></td>
                                                                                        <td><?php echo $fila_pendientes['periodo_fin'];?></td>
											<?php
												if( $fila_pendientes['status']== 'C' )
												{
												?>
													<td style="text-align: center; width: 22px !important; white-space: nowrap !important; text-decoration: none;">

														<a href="zoho_contable/index.php?codnom=<?php echo $fila_pendientes['codnom'];?>&codtip=<?php echo $fila_pendientes['codtip'];?>&tipo_comprobante=2" target="_blank" title="Enviar">
															<img src="../../includes/imagenes/icons/lightning.png" width="16" height="16">
														</a>
														&nbsp;
														
													</td>
												<?php
												}
												else if( $fila_fallidas['status']!=='C')
												{
												?>

													

												<?php
												}


												else
												{
												?>
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
                                                    
                                                        
                                                        <div class="tab-pane" id="tab_fallidas">
								<form action="" method="post" name="frmPrincipal" id="frmPrincipal">
								<table class="table table-striped table-bordered table-hover" id="table_datatable_fallidas">
								<thead>
								<tr>
									<th>ID</th>
                                                                        <th>Fecha</th>
                                                                        <th>Cod. Planilla</th>
                                                                        <th>Tipo Planilla</th>
                                                                        <th>ID Zoho</th>
                                                                        <th>Cod. Zoho</th>
                                                                        <th>Descripcion</th>
                                                                        <th>Estus Zoho</th>
                                                                        <th>Mensaje Zoho</th>
                                                                        <th>Tipo Asiento</th>
                                                                        <th>Fecha Envio</th>
                                                                        <th>Usuario Envio</th>                                                                        
									<th>&nbsp;</th>
								</tr>
								</thead>
								<tbody>
								<?php								
									while( $fila_fallidas=fetch_array($res_fallidas) )
									{ 	
									?>
										<tr class="odd gradeX" id="num" opcion="<?php echo $fila_fallidas['id'];?>">
											<td><?php echo $fila_fallidas['id'];?></td>
											<td><?php echo $fila_fallidas['fecha_creacion'];?></td>
                                                                                        <td><?php echo $fila_fallidas['cod_planilla'];?></td>
                                                                                        <td><?php echo $fila_fallidas['tipo_planilla'];?></td>
                                                                                        <td><?php echo $fila_fallidas['id_zoho'];?></td>
                                                                                        <td><?php echo $fila_fallidas['cod_zoho'];?></td>
                                                                                        <td class="text-normal"><?php echo $fila_fallidas['descripcion'];?></td>
                                                                                        <td><?php echo $fila_fallidas['estatus_zoho'];?></td>
                                                                                        <td class="text-normal"><?php echo $fila_fallidas['mensaje_zoho'];?></td>
                                                                                        <td><?php echo $fila_fallidas['tipo_asiento'];?></td>
                                                                                        <td><?php echo $fila_fallidas['fecha_creacion'];?></td>
                                                                                        <td><?php echo $fila_fallidas['usuario_creacion'];?></td>
											<?php
												if( $fila_fallidas['estatus']== 0 )
												{
												?>
													<td style="text-align: center; width: 22px !important; white-space: nowrap !important; text-decoration: none;">

														<a href="zoho_contable/index.php?codnom=<?php echo $fila_fallidas['cod_planilla'];?>&codtip=<?php echo $fila_fallidas['tipo_planilla'];?>&id_zoho_contable_cabecera=<?php echo $fila_fallidas['id'];?>&tipo_comprobante=2" target="_blank" title="Reenviar">
															<img src="../../includes/imagenes/icons/lightning.png" width="16" height="16">
														</a>
														&nbsp;
														<a href="zoho_contable_proceso_detalle.php?id=<?php echo $fila_fallidas['id']; ?>" title="Ver">
															<img src="../../includes/imagenes/icons/pencil.png" width="16" height="16">
														</a>
														&nbsp;
<!--														<a href="zoho_contable/index.php?accion=eliminar&id_zoho_contable_cabecera=<?php echo $fila_fallidas['id']; ?>" title="Anular" target="_blank">
															<img src="../imagenes/delete.gif" alt="Anular" width="16" height="16">
														</a>-->
													</td>
												<?php
												}
												else if( $fila_fallidas['estatus']== 1)
												{
												?>

													

												<?php
												}


												else
												{
												?>
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
                                                    
                                                    
							<div class="tab-pane" id="tab_procesadas">
								<form action="" method="post" name="frmPrincipal" id="frmPrincipal">
								<table class="table table-striped table-bordered table-hover" id="table_datatable_procesadas">
								<thead>
								<tr>
									<th>ID</th>
                                                                        <th>Fecha</th>
                                                                        <th>Cod. Planilla</th>
                                                                        <th>Tipo Planilla</th>
                                                                        <th>ID Zoho</th>
                                                                        <th>Cod. Zoho</th>
                                                                        <th>Descripcion</th>
                                                                        <th>Estus Zoho</th>
                                                                        <th>Mensaje Zoho</th>
                                                                        <th>Tipo Asiento</th>
                                                                        <th>Fecha Procesmiento</th>
                                                                        <th>Usuario Procesamiento</th>                                                                        
									<th>&nbsp;</th>
								</tr>
								</thead>
								<tbody>
								<?php								
									while( $fila_procesadas=fetch_array($res_procesadas) )
									{ 	
									?>
										<tr class="odd gradeX" id="num" opcion="<?php echo $fila_procesadas['id'];?>">
											<td><?php echo $fila_procesadas['id'];?></td>
											<td><?php echo $fila_procesadas['fecha_creacion'];?></td>
                                                                                        <td><?php echo $fila_procesadas['cod_planilla'];?></td>
                                                                                        <td><?php echo $fila_procesadas['tipo_planilla'];?></td>
                                                                                        <td><?php echo $fila_procesadas['id_zoho'];?></td>
                                                                                        <td><?php echo $fila_procesadas['cod_zoho'];?></td>
                                                                                        <td class="text-normal"><?php echo $fila_procesadas['descripcion'];?></td>
                                                                                        <td><?php echo $fila_procesadas['estatus_zoho'];?></td>
                                                                                        <td class="text-normal"><?php echo $fila_procesadas['mensaje_zoho'];?></td>
                                                                                        <td><?php echo $fila_procesadas['tipo_asiento'];?></td>
                                                                                        <td><?php echo $fila_procesadas['fecha_creacion'];?></td>
                                                                                        <td><?php echo $fila_procesadas['usuario_creacion'];?></td>
											<?php
												if( $fila_procesadas['estatus']== 1)
												{
												?>
													<td style="text-align: center; width: 22px !important; white-space: nowrap !important; text-decoration: none;">

														
														<a href="zoho_contable_proceso_detalle.php?id=<?php echo $fila_procesadas['id']; ?>&tipo_comprobante=2" title="Ver">
															<img src="../../includes/imagenes/icons/pencil.png" width="16" height="16">
														</a>
														&nbsp;
														<a href="zoho_contable/index.php?accion=eliminar&id_zoho_contable_cabecera=<?php echo $fila_procesadas['id']; ?>&tipo_comprobante=2" title="Anular" target="_blank">
															<img src="../imagenes/delete.gif" alt="Anular" width="16" height="16">
														</a>
													</td>
												<?php
												}
												else if( $fila_procesadas['estatus']== 2)
												{
												?>

													

												<?php
												}


												else
												{
												?>
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
                                                    
							<div class="tab-pane" id="tab_anuladas">
							<form action="" method="post" name="frmPrincipal2" id="frmPrincipal2">
								<table class="table table-striped table-bordered table-hover" id="table_datatable_anuladas">
								<thead>
								<tr>
									<th>ID</th>
                                                                        <th>Fecha</th>
                                                                        <th>Cod. Planilla</th>
                                                                        <th>Tipo Planilla</th>
                                                                        <th>ID Zoho</th>
                                                                        <th>Cod. Zoho</th>
                                                                        <th>Descripcion</th>
                                                                        <th>Estus Zoho</th>
                                                                        <th>Mensaje Zoho</th>
                                                                        <th>Tipo Asiento</th>
                                                                        <th>Fecha Anulacion</th>
                                                                        <th>Usuario Anulacion</th>                                                                        
									<th>&nbsp;</th>
								</tr>
								</thead>
								<tbody>
								<?php								
									while( $fila_anuladas=fetch_array($res_anuladas) )
									{ 	
									?>
										<tr class="odd gradeX" id="num" opcion="<?php echo $fila_anuladas['id'];?>">
											<td><?php echo $fila_anuladas['id'];?></td>
											<td><?php echo $fila_anuladas['fecha_creacion'];?></td>
                                                                                        <td><?php echo $fila_anuladas['cod_planilla'];?></td>
                                                                                        <td><?php echo $fila_anuladas['tipo_planilla'];?></td>
                                                                                        <td><?php echo $fila_anuladas['id_zoho'];?></td>
                                                                                        <td><?php echo $fila_anuladas['cod_zoho'];?></td>
                                                                                        <td><?php echo $fila_anuladas['descripcion'];?></td>
                                                                                        <td><?php echo $fila_anuladas['estatus_zoho'];?></td>
                                                                                        <td><?php echo $fila_anuladas['mensaje_zoho'];?></td>
                                                                                        <td><?php echo $fila_anuladas['tipo_asiento'];?></td>
                                                                                        <td><?php echo $fila_anuladas['fecha_anulacion'];?></td>
                                                                                        <td><?php echo $fila_anuladas['usuario_anulacion'];?></td>
											<?php
												if( $fila_anuladas['estatus']== 2)
												{
												?>
													<td style="text-align: center; width: 22px !important">
													<a href="zoho_contable_proceso_detalle.php?id=<?php echo $fila['id']; ?>&tipo_comprobante=2" title="Editar">
													<img src="../../includes/imagenes/icons/pencil.png" width="16" height="16"></a>
													</td>
													
													
												<?php
												}
												else
												{
												?>
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
             
             
             $('#table_datatable_pendientes').DataTable({
            	"iDisplayLength": 25,
            	"bStateSave" : true,
                //"sPaginationType": "bootstrap",
            	"sPaginationType": "bootstrap_extended", 
            	"aaSorting": [[ 1, "desc" ]], 
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
                    { 'bSortable': false, 'aTargets': [6] },
                    { "bSearchable": false, "aTargets": [6] },
                ],
				 "fnDrawCallback": function() {
				        $('#table_datatable_pendientes_filter input').attr("placeholder", "Escriba frase para buscar");
				 }
            });

            $('#table_datatable_pendientes').on('change', 'tbody tr .checkboxes', function(){
                 $(this).parents('tr').toggleClass("active");
            });

            $('#table_datatable_pendientes_wrapper .dataTables_filter input').addClass("form-control input-medium input-inline"); 
            $('#table_datatable_pendientes_wrapper .dataTables_length select').addClass("form-control input-xsmall"); 
            
            
            

            $('#table_datatable_fallidas').DataTable({
            	"iDisplayLength": 25,
            	"bStateSave" : true,
                //"sPaginationType": "bootstrap",
            	"sPaginationType": "bootstrap_extended", 
            	"aaSorting": [[ 1, "desc" ]], 
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
                    { 'bSortable': false, 'aTargets': [12] },
                    { "bSearchable": false, "aTargets": [12] },
                   // { "sWidth": "8%", "aTargets": [6] },
                    //{ 'bSortable': false, 'aTargets': [14] },
                    //{ "bSearchable": false, "aTargets": [14] },
                ],
				 "fnDrawCallback": function() {
				        $('#table_datatable_fallidas_filter input').attr("placeholder", "Escriba frase para buscar");
				 }
            });

            $('#table_datatable_fallidas').on('change', 'tbody tr .checkboxes', function(){
                 $(this).parents('tr').toggleClass("active");
            });

            $('#table_datatable_fallidas_wrapper .dataTables_filter input').addClass("form-control input-medium input-inline"); 
            $('#table_datatable_fallidas_wrapper .dataTables_length select').addClass("form-control input-xsmall"); 
            
            
            
            $('#table_datatable_procesadas').DataTable({
            	"iDisplayLength": 25,
            	"bStateSave" : true,
                //"sPaginationType": "bootstrap",
            	"sPaginationType": "bootstrap_extended", 
            	"aaSorting": [[ 1, "desc" ]], 
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
                    { 'bSortable': false, 'aTargets': [12] },
                    { "bSearchable": false, "aTargets": [12] },
                ],
				 "fnDrawCallback": function() {
				        $('#table_datatable_procesadas_filter input').attr("placeholder", "Escriba frase para buscar");
				 }
            });

            $('#table_datatable_procesadas').on('change', 'tbody tr .checkboxes', function(){
                 $(this).parents('tr').toggleClass("active");
            });
            $('#table_datatable_procesadas_wrapper .dataTables_filter input').addClass("form-control input-medium input-inline"); 
            $('#table_datatable_procesadas_wrapper .dataTables_length select').addClass("form-control input-xsmall"); 
            
            
            $('#table_datatable_anuladas').DataTable({
            	"iDisplayLength": 25,
            	"bStateSave" : true,
                //"sPaginationType": "bootstrap",
            	"sPaginationType": "bootstrap_extended", 
            	"aaSorting": [[ 1, "desc" ]], 
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
                    { 'bSortable': false, 'aTargets': [12] },
                    { "bSearchable": false, "aTargets": [12] },
                   // { "sWidth": "8%", "aTargets": [6] },
                ],
				 "fnDrawCallback": function() {
				        $('#ttable_datatable_anuladas_filter input').attr("placeholder", "Escriba frase para buscar");
				 }
            });

            $('#table_datatable_anuladas').on('change', 'tbody tr .checkboxes', function(){
                 $(this).parents('tr').toggleClass("active");
            });
            $('#table_datatable_anuladas_wrapper .dataTables_filter input').addClass("form-control input-medium input-inline"); 
            $('#table_datatable_anuladas_wrapper .dataTables_length select').addClass("form-control input-xsmall"); 
            
	 });
</script>
</body>
</html>