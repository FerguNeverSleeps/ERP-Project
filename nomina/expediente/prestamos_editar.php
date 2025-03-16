<?php 
session_start();
ob_start();

include ("../header.php");
include("../lib/common.php");
include ("../paginas/func_bd.php");
//error_reporting(0);
$conexion=conexion();
$numpre=$fetch_expediente['numero_decreto'];

echo $POST["procesar"];


if(isset($_REQUEST["procesar"])):	
    
    $numpre=$_REQUEST['numpre'];
//    echo $numpre;
//    exit;
    $gastosadmon=$_REQUEST['gastos_admon'];
    $diciembre=$_REQUEST['diciembre'];
    $ruta_destino=$_REQUEST['ruta_destino'];
    $cuenta_destino=$_REQUEST['cuenta_destino'];
    $producto_destino=$_REQUEST['producto_destino'];
	//actuallizar el encabezado del prestamo
	$sql="UPDATE nomprestamos_cabecera_tmp "
        . "SET "
        . "gastos_admon='".$gastosadmon."',"
        . "ruta_destino='".$ruta_destino."',"
        . "cuenta_destino='".$cuenta_destino."',"
        . "producto_destino='".$producto_destino."',"
        . "diciembre='".$diciembre."'"
        . "WHERE  numpre=$_POST[numpre]";
	$resultado_update_cabecera=query($sql,$conexion);

	if($resultado_update_cabecera)
	{
		?>
		<script type="text/javascript">
		alert("PRESTAMO ACTUALIZADO EXITOSAMENTE!")
		window.location.href="prestamos_list.php"
 		</script>
		<?php	
	}
	else
	{
		?>
		<script type="text/javascript">
		alert("PRESTAMO NO GUARDADO !!!")
 		</script>
		<?php	
	}
	exit;
endif;
//echo $numpre;
?>
<?php include("../header4.php"); // <html><head></head><body> ?>
<link href="../../includes/assets/css/custom-datatables.css" rel="stylesheet" type="text/css"/>
<style type="text/css">
	.portlet > .portlet-title > .actions > .btn.btn-sm {
		margin-top: -9px !important;
	}

	.center {
		text-align: center;
	}
	label {
		font-weight: bold;
		font-size: 13px;
	}
	input[type="text"]{
		border: 1px solid silver;
	}
	.fondo { background-color: green;}
</style>
<script language="JavaScript" type="text/javascript">


function enviar(ficha,prestamo,cuota)
{
	//AbrirVentana("movimientos_nomina_pago_editar.php?nomina="+document.frmPrincipal.txtnomina.value+"&concepto="+id+"&ficha="+document.frmPrincipal.textfield.value+"&accion=modificar",660,700,0);
         AbrirVentana("prestamos_edit_cuota.php?ficha="+ficha+"&prestamo="+prestamo+"&cuota="+cuota+"&accion=modificar",450,650,0);
	
		

	
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
								Ver Pr&eacute;stamo
							</div>
						</div>
						<div class="portlet-body form">
							<!-- <div class="scroller" style="height:1200px"> -->
							<?php
									$consulta = "SELECT np.apenom, npp.descrip, nc.* 
									             FROM   nomprestamos_cabecera_tmp nc 
									             JOIN   nompersonal np   ON (np.ficha=nc.ficha) 
									             JOIN   nomprestamos npp ON (npp.codigopr=nc.codigopr) 
									             WHERE  nc.numpre=$numpre";
									$resultado = query($consulta,$conexion);
									$fetch     = fetch_array($resultado);
							?>
							<form action="" method="POST" name="form1" id="form1"  autocomplete="off" class="form-horizontal" style="margin-bottom: 0px;">
								<div class="form-body">
									<h3 style="font-size: 14px;margin-bottom: 0px;margin-top: 5px;"> <!-- class="form-section" -->
										<div class="row">
											<div class="col-md-3"><strong>N&uacute;mero de pr&eacute;stamo:</strong>&nbsp;&nbsp;<?php echo $fetch['numpre'];?><input type="hidden" name="numpre" id="numpre" value="<?php echo $fetch['numpre'];?>" disabled></div>
											<div class="col-md-2"><strong>Ficha:</strong>&nbsp;&nbsp;<?php echo $fetch['ficha']; ?></div>
											<div class="col-md-3"><strong><?php echo $fetch['apenom'];?></strong></div>
											<div class="col-md-4"><strong>Tipo:</strong>&nbsp;&nbsp;<?php echo $fetch['codigopr']; ?>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $fetch['descrip'];?></div>
										</div>
									</h3>
                                                                    <input type="hidden" id="numpre" name="numpre" value="<?php print $fetch['numpre'];?>">
                                                                    <input type="hidden" id="ficha" name="ficha" value="<?php print $fetch['ficha'];?>">
                                                                    <input type="hidden" id="procesar" name="procesar" value="1">
									<hr style="margin: 10px 0;">
									<div class="row">
										<div class="col-md-3">
											<div class="form-group">
												<label class="control-label col-md-6">Monto del pr&eacute;stamo:</label>
												<div class="col-md-6">
													<input type="text"  id="montopre" name="montopre" class="form-control input-sm" value="<?php echo $fetch['monto']; ?>" style="text-align: center" disabled>
												</div>
											</div>
										</div>
										<div class="col-md-3">
											<div class="form-group">
												<label class="control-label col-md-7">Cuotas a monto fijo:</label>
												<div class="col-md-5">
													<input type="text" name="montocuota" class="form-control input-sm" value="<?php echo $fetch['mtocuota']; ?>" style="text-align: center" disabled>
												</div>
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group">
												<label class="control-label col-md-8">N&uacute;mero de cuotas:</label>
												<div class="col-md-4">
													<input type="text" id="numcuota" name="numcuota" onblur="javascript:cuotas();" value="<?php echo $fetch['cuotas']; ?>" class="form-control input-sm center" disabled>
												</div>												
											</div>											
										</div>
										
									</div>
									<div class="row">
										<div class="col-md-6">
											<div class="form-group">
												<label class="control-label col-md-4">Descripci&oacute;n del pr&eacute;stamo:</label>
												<div class="col-md-8">
													<input type="text" class="form-control input-sm" name="descrip" id="descrip" value="<?php echo $fetch['detalle']; ?>"
													       style="margin-top: 9px" disabled>
												</div>
											</div>											
										</div>	
										<div class="col-md-6">
											<div class="form-group">
                                            <?php
                                                    
                                                            $frededu1 = ($fetch['frededu']=='1')  ? 'checked' : '';
                                                            $frededu2 = ($fetch['frededu']=='2')  ? 'checked' : '';
                                                            $frededu3 = ($fetch['frededu']=='3')  ? 'checked' : '';
                                                            $frededu4 = ($fetch['frededu']=='4')  ? 'checked' : '';
                                                    
                                            ?>
												<label class="control-label col-md-3">Frecuencia:</label>
												<div class="col-md-9">
														<div class="radio-list">														
                                                                                                                    
                                                                                                                        <label class="radio-inline">
															<input type="radio" name="frededu" id="frededu1" value="1" disabled <?php echo $frededu1; ?>> Semanal</label>
															<label class="radio-inline">
															<input type="radio" name="frededu" id="frededu2" value="2" disabled <?php echo $frededu2; ?>> Quincenal</label>
															<label class="radio-inline">
															<input type="radio" name="frededu" id="frededu3" value="3" disabled <?php echo $frededu3; ?>> Mensual</label>
                                                                                                                        <label class="radio-inline">
															<input type="radio" name="frededu" id="frededu4" value="4" disabled <?php echo $frededu4; ?>> Bisemanal</label>
														</div>
												</div>
											</div>											
										</div>										
									</div>
									<!--
									<div class="row">
										<div class="col-md-12">
											<div class="form-group">
												<label class="control-label col-md-2">Frecuencia de deducci&oacute;n:</label>
												<div class="col-md-8">
																<div class="radio-list">
																	<label class="radio-inline">
																	<input type="radio" name="frededu" value="1"/>
																	Semanal </label>
																	<label class="radio-inline">
																	<input type="radio" name="frededu" value="2" checked/>
																	Quincenal </label>
																	<label class="radio-inline">
																	<input type="radio" name="frededu" value="3"/>
																	Mensual </label>
																</div>
												</div>
											</div>											
										</div>										
									</div>
									-->
									<div class="row">
										<div class="col-md-6">
											<div class="form-group">
												<label class="control-label col-md-4">Fecha de aprobaci&oacute;n:</label>
												<div class="col-md-4">
													<input type="text" class="form-control input-sm center" name="fechaap" id="fechaap" value="<?php echo fecha($fetch['fechaapro']); ?>" disabled>
												</div>
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group">
												<label class="control-label col-md-6">Fecha de vencimiento 1era cuota:</label>
												<div class="col-md-4">
													<input type="text" class="form-control input-sm center" name="fecha1" id="fecha1" value="<?php echo fecha($fetch['fecpricup']); ?>" disabled>
												</div>
											</div>
										</div>										
									</div>
									<div class="row">
										<div class="col-md-6">
											<div class="form-group">
												<label class="control-label col-md-4">Monto de cuotas:</label>
												<div class="col-md-4">
													<input type="text" name="mcuota" id="mcuota" value="<?echo $fetch[mtocuota]?>" 
													       class="form-control input-sm center" style="font-size : 14pt; color: #31708f" disabled>
												</div>
											</div>											
										</div>	
                                                                                <div class="col-md-3">
                                                                                        <div class="form-group">
                                                                                                <label class="control-label col-md-7">¿Descuenta Diciembre?:</label>
                                                                                                <div class="col-md-5">
                                                                                                    <select class="form-control" name="diciembre" id="diciembre">
                                                                                                            <option value="1" <?php if($fetch['diciembre']==1) echo "selected" ;?>>No</option>
                                                                                                            <option value="0" <?php if($fetch['diciembre']==0) echo "selected" ;?>>Si</option>
                                                                                                    </select>
                                                                                                </div>	
                                                                                        </div>
                                                                                </div>
                                                                                <div class="col-md-3">
											<div class="form-group">
												<label class="control-label col-md-6">Gastos Admon. (%):</label>
												<div class="col-md-5">
													<input type="text" id="gastos_admon" name="gastos_admon" value="<?php echo $fetch['gastos_admon']; ?>" class="form-control input-sm center">
												</div>												
											</div>											
										</div>
									</div>
                                    <br>
									<?php 
										$consulta   = "SELECT * FROM nomprestamos_detalles_tmp 
										               WHERE numpre=$numpre";
										$resultado2 = query($consulta, $conexion);
									?>
									<div class="table-responsive">
										<table class="table table-hover table-bordered" id="table_datatable">
											<thead>
												<tr>
													<th class="center"># Cuota</th>
													<th class="center">Vence</th>
													<th class="center">Saldo inicio</th>
													<th class="center">Amortizado</th>
													<th class="center">Cuota</th>
													<th class="center">Saldo fin</th>
													<th class="center">Status</th>
												</tr>
											</thead>
											<tbody>												
													<?php
														while($fetch2=fetch_array($resultado2))
														{
															if($fetch2['estadopre']=='Cancelado'){?>
															<tr class="fondo">
																<td class="center"><?php echo $fetch2['numcuo']; ?></td>
																<td class="center"><?php echo fecha($fetch2['fechaven']); ?></td>
																<td class="center"><?php echo numero($fetch2['salinicial']); ?></td>
																<td class="center"><?php echo numero($fetch2['montocuo']); ?></td>
																<td class="center"><?php echo numero($fetch2['montocuo']); ?></td>
																<td class="center"><?php echo numero($fetch2['salfinal']); ?></td>
																<td class="center"><?php echo $fetch2['estadopre']; ?></td>                 
															</tr>
													<?php
														}elseif($fetch2['estadopre']=='Pendiente'){
													?>	
															<tr>
																<td class="center"><?php echo $fetch2['numcuo']; ?></td>
																<td class="center"><?php echo fecha($fetch2['fechaven']); ?></td>
																<td class="center"><?php echo numero($fetch2['salinicial']); ?></td>
																<td class="center"><?php echo numero($fetch2['montocuo']); ?></td>
																<td class="center"><?php echo numero($fetch2['montocuo']); ?></td>
																<td class="center"><?php echo numero($fetch2['salfinal']); ?></td>
																<td class="center"><?php echo $fetch2['estadopre']; ?></td>
															</tr>
													<?php }} ?>											
											</tbody>
										</table>
									</div>
								</div>
							</form>
							<!-- </div> -->
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
                  "sSearch": "<img src='../../includes/imagenes/icons/magnifier.png' width='16' height='16' > Buscar:",
                    "sLengthMenu": "Mostrar _MENU_",
                    //"sInfo": "Showing page _PAGE_ of _PAGES_", // Mostrando 1 to 5 de 18 entradas
                    //"sInfoEmpty": "No hay registros para mostrar",
                    "sInfoEmpty": "",
                    //"sInfo": "",
                    "sInfo":"Total _TOTAL_ registros",
                    "sInfoFiltered": "",
                  "sEmptyTable":  "No hay datos disponibles",
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
                    { 'bSortable': false, 'aTargets': [7] },
                    { "bSearchable": false, "aTargets": [7] },
                    { "sWidth": "8%", "aTargets": [7] },
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
            
            
            $('#ruta_destino').select2();
            $('#producto_destino').select2();
   });
</script>
</body>
</html>