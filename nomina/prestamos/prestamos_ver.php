<?php 
session_start();
ob_start();

require_once '../lib/common.php';
include ("../paginas/func_bd.php");
//error_reporting(0);
$conexion=conexion();
$consulta_Q   = "SELECT count(*) cantidad FROM nomprestamos_detalles  WHERE numpre={$_GET['numpre']} AND estadopre in ('Cancelada')";
$resultado_Q = query($consulta_Q, $conexion);
$fetch_total=fetch_array($resultado_Q);

$consulta_S   = "SELECT sum(montocuo) suma FROM nomprestamos_detalles  WHERE numpre={$_GET['numpre']} AND estadopre in ('Cancelada')";
$resultado_S = query($consulta_S, $conexion);
$fetch_suma=fetch_array($resultado_S);
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
								Ver Pr&eacute;stamo
							</div>
							<div class="actions">
								<a class="btn btn-sm blue"  onclick="javascript: window.location='prestamos_list.php'">
									<i class="fa fa-arrow-left"></i> Regresar
								</a>
							</div>
						</div>
						<div class="portlet-body form">
							<!-- <div class="scroller" style="height:1200px"> -->
							<?php
									$consulta = "SELECT np.apenom, npp.descrip, nc.* 
									             FROM   nomprestamos_cabecera nc 
									             JOIN   nompersonal np   ON (np.ficha=nc.ficha) 
									             JOIN   nomprestamos npp ON (npp.codigopr=nc.codigopr) 
									             WHERE  nc.numpre={$_GET['numpre']}";
									$resultado = query($consulta,$conexion);
									$fetch     = fetch_array($resultado);
							?>
							<form action="#" class="form-horizontal" style="margin-bottom: 0px;">
								<div class="form-body">
									<h3 style="font-size: 14px;margin-bottom: 0px;margin-top: 5px;"> <!-- class="form-section" -->
										<div class="row">
											<div class="col-md-3"><strong>N&uacute;mero de pr&eacute;stamo:</strong>&nbsp;&nbsp;<?php echo $fetch['numpre'];?><input type="hidden" name="numpre" id="numpre" value="<?php echo $fetch['numpre'];?>" disabled></div>
											<div class="col-md-2"><strong>Ficha:</strong>&nbsp;&nbsp;<?php echo $fetch['ficha']; ?></div>
											<div class="col-md-3"><strong><?php echo $fetch['apenom'];?></strong></div>
											<div class="col-md-4"><strong>Tipo:</strong>&nbsp;&nbsp;<?php echo $fetch['codigopr']; ?>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $fetch['descrip'];?></div>
										</div>
									</h3>
									<hr style="margin: 10px 0;">
									<div class="row">
										<div class="col-md-4">
											<div class="form-group">
												<label class="control-label col-md-6">Monto del pr&eacute;stamo:</label>
												<div class="col-md-6">
													<input type="text"  id="montopre" name="montopre" class="form-control input-sm" value="<?php echo $fetch['monto']; ?>" style="text-align: center" disabled>
												</div>
											</div>
										</div>
										<div class="col-md-4">
											<div class="form-group">
												<label class="control-label col-md-7">Cuotas a monto fijo:</label>
												<div class="col-md-5">
													<input type="text" name="montocuota" class="form-control input-sm" value="<?php echo $fetch['mtocuota']; ?>" style="text-align: center" disabled>
												</div>
											</div>
										</div>
										<div class="col-md-3">
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
															<input type="radio" name="frededu" id="frededu1" value="1" disabled="disabled" <?php echo $frededu1; ?>> Semanal</label>
															<label class="radio-inline">
															<input type="radio" name="frededu" id="frededu2" value="2" disabled="disabled" <?php echo $frededu2; ?>> Quincenal</label>
															<label class="radio-inline">
															<input type="radio" name="frededu" id="frededu3" value="3" disabled="disabled" <?php echo $frededu3; ?>> Mensual</label>
                                                                                                                        <label class="radio-inline">
															<input type="radio" name="frededu" id="frededu4" value="4" disabled="disabled" <?php echo $frededu4; ?>> Bisemanal</label>
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
										<div class="col-md-4">
											<div class="form-group">
												<label class="control-label col-md-6">Monto de cuotas:</label>
												<div class="col-md-6">
													<input type="text" name="mcuota" id="mcuota" value="<?echo $fetch[mtocuota]?>" 
													       class="form-control input-sm center" style="font-size : 14pt; color: #31708f" disabled>
												</div>
											</div>
										</div>	
										<div class="col-md-4">
											<div class="form-group">
												<label class="control-label col-md-6">Cantidad cuotas:</label>
												<div class="col-md-4">
													<input type="text" name="mcuota" id="mcuota" value="<?echo $fetch_total[cantidad]?>" 
													       class="form-control input-sm center" style="font-size : 14pt; color: #31708f" disabled>
												</div>
											</div>
										</div>
										<div class="col-md-3">
											<div class="form-group">
												<label class="control-label col-md-6">Monto de Total Cancelado:</label>
												<div class="col-md-6">
													<input type="text" name="mcuota" id="mcuota" value="<?echo $fetch_suma[suma]?>" 
													       class="form-control input-sm center" style="font-size : 14pt; color: #31708f" disabled>
												</div>
											</div>
										</div>									
									</div>
									<?php 
										$consulta   = "SELECT * FROM nomprestamos_detalles 
										               WHERE numpre={$_GET['numpre']}";
										$resultado2 = query($consulta, $conexion);
									?>
									<div class="table-responsive">
										<table class="table table-striped table-hover table-bordered">
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
													?>
															<tr>
																<td class="center"><?php echo $fetch2['numcuo']; ?></td>
																<td class="center"><?php echo fecha($fetch2['fechaven']); ?></td>
																<td class="center"><?php echo numero($fetch2['salinicial']); ?></td>
																<td class="center"><?php echo numero($fetch2['montocuo']); ?></td>
																<td class="center"><?php echo numero($fetch2['montocuo']); ?></td>
																<td class="center"><?php echo numero($fetch2['salfinal']); ?></td>
																<td class="center" style="position: relative;"><?php echo $fetch2['estadopre'].($fetch2['detalle']?"<i class='fa fa-info' title='".$fetch2['detalle']."' style='position:absolute; right:10px; top:8px; font-size:20px; color:#ff5722; cursor:pointer;' onclick=\"alert('".$fetch2['detalle']."')\"></i>":"")?></td>
															</tr>
													<?php
														}
													?>												
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
</body>
</html>