<?php 
session_start();
ob_start();

require_once '../../lib/common.php';
include ("../../paginas/funciones_nomina.php");
include ("../../paginas/func_bd.php");
// error_reporting(0);
$conexion=conexion();

$id=$_POST['id'];	
$op=$_POST['op'];

if ($op==3) //Se presiono el boton de Eliminar
{	
	$consulta = "DELETE 
	             FROM   emision_col_detalle 
	             WHERE  id_encabezado={$id} "; 

	$resultadoCon = query($consulta, $conexion); //  sql_ejecutar($query);

	$consulta = "DELETE 
	             FROM   emision_col_cabecera
	             WHERE  id={$id} "; 

	$resultado = query($consulta, $conexion); 

	activar_pagina("emision_col_proceso.php");	 
}   
query("SET names UTF8;", $conexion);
$sql_pendientes = "SELECT id_cabecera,
		descripcion,
		CAST(fecha_inicio as date ) fecha_inicio,
		CAST(fecha_fin as date ) fecha_fin,
		CAST(fecha_creacion as date ) fecha_creacion,
		usuario_creacion
	FROM   emision_col_cabecera
	WHERE  estatus=0
	ORDER BY fecha_creacion DESC";

$sql_procesadas = "SELECT *
        FROM   emision_col_cabecera
        WHERE  estatus=1
	ORDER BY fecha_creacion DESC";
		
$sql_anuladas = "SELECT *
        FROM   emision_col_cabecera
        WHERE  estatus=2
	ORDER BY fecha_creacion DESC";

$res_pendientes = query($sql_pendientes, $conexion);
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

$tab_pendientes_active="active";
$tab_procesados_active="";
$tab_anulados_active="";


if(isset($_REQUEST["tab_active"])){
	if($_REQUEST["tab_active"]=="anulados"){
		$tab_pendientes_active="";
		$tab_procesados_active="";
		$tab_anulados_active="active";
	}
}


?>
<?php include("../../header_emision.php"); // <html><head></head><body> ?>
<link href="../../../includes/assets/css/custom-datatables.css" rel="stylesheet" type="text/css"/>
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
								<img src="../../imagenes/21.png" width="20" height="20" class="icon"> Nomina Emisión Electronica / Global
							</div>
							<div class="actions">
								<a class="btn btn-sm green" id="generarNomina">
									<i class="fa fa-cogs"></i> Generar
								</a>
								<a class="btn btn-sm blue"  onclick="javascript: window.location='index.php'">
									<i class="fa fa-arrow-left"></i> Regresar
								</a>
							</div>
						</div>
						<div class="portlet-body">
							
						<ul class="nav nav-tabs">
							<li class="<?php print $tab_pendientes_active;?>"><a href="#tab_pendientes" id="tab-pendientes" data-toggle="tab">Pendientes</a></li>
                            <li class="<?php print $tab_procesados_active;?>"><a href="#tab_procesadas" id="tab-procesadas" data-toggle="tab">Procesados</a></li>
							<li class="<?php print $tab_anulados_active;?>"  ><a href="#tab_anuladas" id="tab-anuladas" data-toggle="tab">Anulados</a></li>
						</ul>
						<div class="tab-content">
                            <div class="tab-pane <?php print $tab_pendientes_active;?>" id="tab_pendientes">
								<form action="" method="post" name="frmPrincipal" id="frmPrincipal">
								<table class="table table-striped table-bordered table-hover" id="table_datatable_pendientes">
								<thead>
								<tr>
									<th>ID</th>
                                    <th>Descripcion</th>
                                    <th>Fecha Inicio Periodo</th>
                                    <th>Fecha Fin Periodo</th>
                                    <th>Fecha Procesamiento</th>
                                    <th>Usuario Procesamiento</th>                                                                        
									<th>&nbsp;</th>
								</tr>
								</thead>
								<tbody>
								<?php								
									while( $fila_pendientes=fetch_array($res_pendientes) )
									{ 	
									?>
										<tr class="odd gradeX" id="num" opcion="<?php echo $fila_procesadas['id_cabecera'];?>">
											<td><?php echo $fila_pendientes['id_cabecera'];?></td>
                                            <td class="text-normal"><?php echo $fila_pendientes['descripcion'];?></td>
                                            <td><?php echo $fila_pendientes['fecha_inicio'];?></td>
                                            <td><?php echo $fila_pendientes['fecha_fin'];?></td>
                                            <td><?php echo $fila_pendientes['fecha_creacion'];?></td>
                                            <td><?php echo $fila_pendientes['usuario_creacion'];?></td>
											<?php
												if( $fila_pendientes['estatus']== 0)
												{
												?>
													<td style="text-align: center; width: 22px !important; white-space: nowrap !important; text-decoration: none;">

														<!--<a href="nomina_individual_consulta.php?id_cabecera=<?php echo $fila_pendientes['id_cabecera']; ?>" onclick="refrescar(<?php echo $fila_pendientes['id_cabecera']; ?>)" title="Refrescar"><img src="../../../includes/imagenes/icons/arrow_refresh.png" width="16" height="16"></a>
														&nbsp;-->
														<a onclick="onVer(<?= $fila_pendientes['id_cabecera'] ?>)" title="Ver"><img src="../../../includes/imagenes/icons/eye.png" width="16" height="16"></a>
														&nbsp;
														<a href="#" title="Anular"><img src="../../../includes/imagenes/icons/delete.png" alt="Anular" width="16" height="16" onclick="onAnular('anular.php?id_cabecera=<?php echo $fila_pendientes['id_cabecera']; ?>')"></a>
													</td>
												<?php
												}
												else if( $fila_pendientes['estatus']== 2)
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
									<input name="registro_id_0" type="hidden" value="">
									<input name="op0" type="hidden" value="">	
								</form>
							</div>                                                            
                                                                                                            
							<div class="tab-pane <?php print $tab_procesados_active;?>" id="tab_procesadas">
								<form action="" method="post" name="frmPrincipal" id="frmPrincipal">
								<table class="table table-striped table-bordered table-hover" id="table_datatable_procesadas">
								<thead>
								<tr>
									<th>ID</th>
									<th>Descripcion</th>
									<th>Fecha Inicio Periodo</th>
									<th>Fecha Fin Periodo</th>
									<th>Fecha Procesamiento</th>
									<th>Usuario Procesamiento</th>                                                                        
									<th>&nbsp;</th>
								</tr>
								</thead>
								<tbody>
								<?php								
									while( $fila_procesadas=fetch_array($res_procesadas) )
									{ 	
									?>
										<tr class="odd gradeX" id="num" opcion="<?php echo $fila_procesadas['id_cabecera'];?>">
											<td><?php echo $fila_procesadas['id_cabecera'];?></td>
											<td class="text-normal"><?php echo $fila_procesadas['descripcion'];?></td>
											<td><?php echo $fila_procesadas['fecha_inicio'];?></td>
											<td><?php echo $fila_procesadas['fecha_fin'];?></td>
											<td><?php echo $fila_procesadas['fecha_creacion'];?></td>
											<td><?php echo $fila_procesadas['usuario_creacion'];?></td>
											<?php
												if( $fila_procesadas['estatus']== 1)
												{
												?>
													<td style="text-align: center; width: 22px !important; white-space: nowrap !important; text-decoration: none;">

														
													<a href="nomina_individual_detalle.php?id_cabecera=<?php echo $fila_procesadas['id_cabecera']; ?>" title="Ver"><img src="../../../includes/imagenes/icons/eye.png" width="16" height="16"></a>
														&nbsp;
														<a href="#" title="Anular"><img src="../../../includes/imagenes/icons/delete.png" alt="Anular" width="16" height="16" onclick="onAnular('nomina_electronica/index.php?accion=anular&id_cabecera=<?php echo $fila_procesadas['id_cabecera']; ?>')"></a>
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
                                                    
							<div class="tab-pane <?php print $tab_anulados_active;?>" id="tab_anuladas">
                                                            <form action="" method="post" name="frmPrincipal2" id="frmPrincipal2">
								<table class="table table-striped table-bordered table-hover" id="table_datatable_anuladas">
								<thead>
								<tr>
									<th>ID</th>
									<th>Descripcion</th>
									<th>Fecha Inicio Periodo</th>
									<th>Fecha Fin Periodo</th>
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
											<td><?php echo $fila_anuladas['descripcion'];?></td>
											<td><?php echo $fila_anuladas['fecha_inicio'];?></td>
											<td><?php echo $fila_anuladas['fecha_fin'];?></td>
											<td><?php echo $fila_anuladas['fecha_anulacion'];?></td>
											<td><?php echo $fila_anuladas['usuario_anulacion'];?></td>
											<?php
												if( $fila_anuladas['estatus']== 2)
												{
												?>
													<td style="text-align: center; width: 22px !important;">
													<a href="nomina_individual_detalle_anulados.php?id_cabecera=<?php echo $fila_anuladas['id_cabecera']; ?>" title="Ver"><img src="../../includes/imagenes/icons/eye.png" width="16" height="16"></a>
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
<div id="modal-procesar" class="modal fade" tabindex="-1" aria-hidden="true" data-keyboard="false" data-backdrop="static">
	<div class="modal-dialog" style="width: 90%">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="boton_cerrar"></button>
				<h4 class="modal-title" style="font-size: 16px;">Nomina Electrónica - Anular</h4>
			</div>
			<div class="modal-body" id="cargar_contendio">
				
			</div>
		</div>
	</div>
</div>

<!--=====================================
=          Generar Nómina Modal        =
======================================-->
<!--MODAL-->
<div class="modal fade" id="modalGenerarNominaIndividual" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" style="width: 900px">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
        <h4 class="modal-title">Generación Nómina Electrónica</h4>
      </div>
      <div class="modal-body">
						<div class="portlet-body form" id="blockui_portlet_body">

							<form class="form-horizontal" id="form_nomina_electronica_proceso_nomina_general_periodos_global_procesar" name="form_nomina_electronica_proceso_nomina_general_periodos_global_procesar" method="post">
								<input type="hidden" id="codtip" name="codtip" value="<?= $_SESSION['codigo_nomina'];?>">
								<div class="row">&nbsp;</div>
								<div class="row">
									<div class="form-group">
										<label class="text-right col-xs-4 col-sm-4 col-md-4 col-lg-4 control-label">Año:</label>
                    <div class="col-md-5">
                      <select name="anio" id="anio" class="form-control">
                        <option value="">Seleccione Año...</option>
                      </select>
                    </div>
                  </div><p></p>
                </div>
								<div class="row">
									<div class="form-group">
                    <label class="text-right col-xs-4 col-sm-4 col-md-4 col-lg-4 control-label">Mes:</label>
                    <div class="col-md-5">
                      <select name="mes" id="mes" class="form-control">
                        <option value="01">Enero</option>
                        <option value="02">Febrero</option>
                        <option value="03">Marzo</option>
                        <option value="04">Abril</option>
                        <option value="05">Mayo</option>
                        <option value="06">Junio</option>
                        <option value="07">Julio</option>
                        <option value="08">Agosto</option>
                        <option value="09">Septiembre</option>
                        <option value="10">Octubre</option>
                        <option value="11">Noviembre</option>
                        <option value="12">Diciembre</option>
                      </select>
                    </div>
                  </div>
                </div>

								<div class="row">
									<div class="form-group">
										<label class="text-right col-xs-4 col-sm-4 col-md-4 col-lg-4 control-label">Fecha Inicio</label>
                    <div class="col-md-5">
                      <div class="input-group date date-picker" data-provide="datepicker"  data-date-format="dd/mm/yyyy"> 
                        <input name="fecha_inicio" type="text" id="fecha_inicio" class="form-control" value="<?php echo "01/".date("m")."/".date("Y") ?>" maxlength="60">
                        <span class="input-group-btn">
                          <button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
                        </span>
                      </div>
                    </div>
                  </div>
								</div>

								<div class="row">
									<div class="form-group">
										<label class="text-right col-xs-4 col-sm-4 col-md-4 col-lg-4 control-label">Fecha Fin</label>
                    <div class="col-md-5">
                      <div class="input-group date date-picker" data-provide="datepicker"  data-date-format="dd/mm/yyyy"> 
                        <input name="fecha_fin" type="text" id="fecha_fin" class="form-control" value="<?php echo "15/".date("m")."/".date("Y") ?>" maxlength="60">
                        <span class="input-group-btn">
                          <button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
                        </span>
                      </div>
                    </div>
                  </div>
								</div>
                                                                
                <div class="row">
									<div class="form-group">
										<label class="text-right col-xs-4 col-sm-4 col-md-4 col-lg-4 control-label">Descripci&oacute;n</label>
                    <div class="col-md-5">
                      <textarea name="descripcion" id="descripcion" class="form-control" rows="3" style="resize:vertical;"></textarea>
                    </div>
                  </div>
								</div>

							</form>

						</div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn dark" data-dismiss="modal">Cerrar</button>&nbsp;
        <button type="button" class="btn green active" id="btn-procesar">Procesar</button>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
 

<?php include("../../footer_emision.php"); ?>
<script type="text/javascript">

	function onAnular(url){
		if(confirm("Proceso de anulación de los registros asociados a la nómina.\n¿Desea continuar?")){
			//window.location.href=url;

			var procesando=`
	        	<div id='cargando' style='text-align: center; padding-top: 80px; padding-bottom: 100px;'>
					<svg width="100px" height="100px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid" class="lds-rolling">
			            <circle cx="50" cy="50" fill="none" ng-attr-stroke="{{config.color}}" ng-attr-stroke-width="{{config.width}}" ng-attr-r="{{config.radius}}" ng-attr-stroke-dasharray="{{config.dasharray}}" stroke="#555555" stroke-width="10" r="35" stroke-dasharray="164.93361431346415 56.97787143782138" transform="rotate(36 50 50)">
			              <animateTransform attributeName="transform" type="rotate" calcMode="linear" values="0 50 50;360 50 50" keyTimes="0;1" dur="1s" begin="0s" repeatCount="indefinite"></animateTransform>
			            </circle>
			        </svg>
					<br>ANULANDO<br><small>No cierre hasta que culmine el proceso.</small>
				</div>
			`;
			$("#cargar_contendio").html(procesando);
			$("#boton_cerrar").css("display","none");

	        //location.href ="nomina_electronica/index.php?anio="+anio+"&mes="+mes+"&fecha_inicio="+fecha_inicio+"&fecha_fin="+fecha_fin+"&codtip="+codtip;
	        $("#modal-procesar").modal("show");	        
	        $("#cargar_contendio").load( url, function() {
			 	$("#boton_cerrar").css("display","");
			 	window.location.href="nomina_individual.php?tab_active=tab_anuladas";
			});

		}
	}
    function refrescar(id){
        
        var datos = new FormData();
        datos.append("ConsultarNomina","yes");
        datos.append("id_cabecera",id);
        $.ajax({
			url : "ajax/Payroll.ajax.php",
            method: "POST",
            data : datos,
            cache: false,
            contentType : false,
            processData : false ,
            dataType : "json" ,
            success: function(respuesta){
                alert(respuesta.mensaje);
            }
        });

	}
    function onVer(id){
		
		$.blockUI({ 
                message: '<span><img src="../../paginas/imagenes/loader_1.gif"  width="25%" height="25%"></span><br><span><h4>Consultando Nómina...<h4></b></span>',
                css: { top: '8%', left: '35%', right: '' } 
        });
		location. href="nomina_individual_detalle.php?id_cabecera="+id;
    }



	$(document).ready(function() {      
                      
            $('#table_datatable_pendientes').DataTable({
            	"iDisplayLength": 25,
            	"bStateSave" : true,
                //"sPaginationType": "bootstrap",
            	"sPaginationType": "bootstrap_extended", 
            	"aaSorting": [[ 0, "desc" ]], 
                "oLanguage": {
                	"sSearch": "<img src='../../../includes/imagenes/icons/magnifier.png' width='16' height='16' > Buscar:",
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
            
            $('#table_datatable_procesadas').DataTable({
            	"iDisplayLength": 25,
            	"bStateSave" : true,
                //"sPaginationType": "bootstrap",
            	"sPaginationType": "bootstrap_extended", 
            	"aaSorting": [[ 0, "desc" ]], 
                "oLanguage": {
                	"sSearch": "<img src='../../../includes/imagenes/icons/magnifier.png' width='16' height='16' > Buscar:",
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
            
            
            
            $('#table_datatable_anuladas').DataTable({
            	"iDisplayLength": 25,
            	"bStateSave" : true,
                //"sPaginationType": "bootstrap",
            	"sPaginationType": "bootstrap_extended", 
            	"aaSorting": [[ 0, "desc" ]], 
                "oLanguage": {
                	"sSearch": "<img src='../../../includes/imagenes/icons/magnifier.png' width='16' height='16' > Buscar:",
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
<script src="./js/nomina_individual.js?<?= date("smh") ?>"></script>
</body>
</html>