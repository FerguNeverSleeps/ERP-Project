<?php 
session_start();
ob_start();

require_once '../lib/common.php';
include ("../paginas/funciones_nomina.php");
include ("../paginas/func_bd.php");
// error_reporting(0);
$conexion=conexion();

$id=$_REQUEST['id'];	


$sql_detalle = "
	SELECT 
		R.*,
		P.apenom
    FROM   nomina_electronica_detalle_response R
    	left join nompersonal P on P.cedula=R.cedula and P.ficha=R.ficha
    WHERE  R.id_cabecera='$id'
	ORDER BY id_detalle_response ASC";
		


$res_procesadas = query($sql_detalle, $conexion);

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
							  <img src="../imagenes/21.png" width="20" height="20" class="icon"> Nomina Electronica / Detalle
							</div>
							<div class="actions">								
								
								<a class="btn btn-sm blue"  onclick="javascript: window.location='nomina_electronica_proceso_nomina_general_periodos_global.php'">
									<i class="fa fa-arrow-left"></i> Regresar
								</a>
							</div>
						</div>
						<div class="portlet-body">
							
						<ul class="nav nav-tabs">
                                                                <li class="active"><a href="#tab_procesadas" id="tab-procesadas" data-toggle="tab">Detalle</a>
								
						</ul>
						<div class="tab-content">
                                                                                                          
                                                                                                            
							<div class="tab-pane active" id="tab_procesadas">
								<form action="" method="post" name="frmPrincipal" id="frmPrincipal">
								<table class="table table-striped table-bordered table-hover" id="table_datatable_procesadas">
								<thead>
								<tr>
									<th>Documento</th>
                                    <th>Código</th>
                                    <th>Mensaje</th>
                                    <th>Resultado</th>
                                    <th>CUNE</th>
                                    <th>Cédula</th>                                                                        
									<th>Ficha</th>
									<th>Nombre / Apellido</th>
									<th></th>
								</tr>
								</thead>
								<tbody>
								<?php								
									while( $fila_procesadas=fetch_array($res_procesadas) )
									{ 	

										if(!mb_detect_encoding($fila_procesadas['apenom'],["UTF-8"],true))
											$fila_procesadas['apenom']=utf8_encode($fila_procesadas['apenom']);
										if(!mb_detect_encoding($fila_procesadas['mensaje'],["UTF-8"],true))
											$fila_procesadas['mensaje']=utf8_encode($fila_procesadas['mensaje']);
										if(!mb_detect_encoding($fila_procesadas['resultado'],["UTF-8"],true))
											$fila_procesadas['resultado']=utf8_encode($fila_procesadas['resultado']);
										if(!mb_detect_encoding($fila_procesadas['codigo'],["UTF-8"],true))
											$fila_procesadas['codigo']=utf8_encode($fila_procesadas['codigo']);

									?>
										<tr class="odd gradeX" id="num" opcion="<?php echo $fila_procesadas['id_detalle_response'];?>">
											<td><?php echo $fila_procesadas['consecutivoDocumento'];?></td>
                                            <td class="text-normal"><?php echo $fila_procesadas['codigo'];?></td>
                                            <td><?php echo $fila_procesadas['mensaje'];?></td>
                                            <td><?php echo $fila_procesadas['resultado'];?></td>
                                            <td><?php echo $fila_procesadas['cune'];?></td>
                                            <td><?php echo $fila_procesadas['cedula'];?></td>
                                            <td><?php echo $fila_procesadas['ficha'];?></td>
                                            <td><?php echo $fila_procesadas['apenom'];?></td>
                                            <td>                                            	
                                            	<a target="_blank" href="https://catalogo-vpfe-hab.dian.gov.co/document/searchqr?documentkey=<?php echo $fila_procesadas['cune']; ?>" title="Ver">
													<img src="../../includes/imagenes/icons/bar-code-16.png" width="16" height="16">
												</a>
                                            </td>
                                            <!--
											<?php
												if( $fila_procesadas['estatus']== 1)
												{
												?>
													<td style="text-align: center; width: 22px !important; white-space: nowrap !important; text-decoration: none;">

														
														<a href="nomina_electronica_proceso_detalle.php?id=<?php echo $fila_procesadas['id']; ?>&tipo_comprobante=1" title="Ver">
															<img src="../../includes/imagenes/icons/pencil.png" width="16" height="16">
														</a>
														&nbsp;
														<a href="nomina_electronica/index.php?accion=eliminar&id_nomina_electronica_cabecera=<?php echo $fila_procesadas['id']; ?>&tipo_comprobante=1" title="Anular" target="_blank">
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
											-->
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
                      
            
            
            $('#table_datatable_procesadas').DataTable({
            	"iDisplayLength": 25,
            	"bStateSave" : true,
                //"sPaginationType": "bootstrap",
            	"sPaginationType": "bootstrap_extended", 
            	"aaSorting": [[ 0, "desc" ]], 
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
				        $('#table_datatable_procesadas_filter input').attr("placeholder", "Escriba frase para buscar");
				 }
            });

            $('#table_datatable_procesadas').on('change', 'tbody tr .checkboxes', function(){
                 $(this).parents('tr').toggleClass("active");
            });
            $('#table_datatable_procesadas_wrapper .dataTables_filter input').addClass("form-control input-medium input-inline"); 
            $('#table_datatable_procesadas_wrapper .dataTables_length select').addClass("form-control input-xsmall"); 
            
            
            
            
	 });
</script>
</body>
</html>