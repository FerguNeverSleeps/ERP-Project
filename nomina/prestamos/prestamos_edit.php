<?php 
session_start();
ob_start();

require_once '../lib/common.php';
include ("../paginas/func_bd.php");
//error_reporting(0);
$conexion=conexion();
$numpre=$_GET['numpre'];

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
	$sql="UPDATE nomprestamos_cabecera "
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
								Editar Pr&eacute;stamo
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
                                                                        
                                                                        <div class="row">
										<div class="col-md-12">
											<div class="form-group">
												<label class="control-label col-md-2">Ruta:</label>
												<div class="col-md-4">
													<select name="ruta_destino" id="ruta_destino" class="form-control">
                                                                                                                <option value="0">Seleccione...</option>
                                                                                                                <option value="000000013" <?php $ruta_destino=$fetch['ruta_destino']; if($ruta_destino=="000000013") echo "selected" ;?>>BANCO NACIONAL DE PANAMA</option>
                                                                                                                <option value="000001575" <?php if($ruta_destino=="000001575") echo "selected" ;?>>BANCO LAFISE PANAMÁ, S.A.</option>
                                                                                                                <option value="000001384" <?php if($ruta_destino=="000001384") echo "selected" ;?>>BAC INTERNATIONAL BANK</option>
                                                                                                                <option value="000001083" <?php if($ruta_destino=="000001083") echo "selected" ;?>>BANCO ALIADO</option>
                                                                                                                <option value="000001504" <?php if($ruta_destino=="000001504") echo "selected" ;?>>BANCO AZTECA</option>
                                                                                                                <option value="000001562" <?php if($ruta_destino=="000001562") echo "selected" ;?>>BANCO DELTA</option>
                                                                                                                <option value="000001724" <?php if($ruta_destino=="000001724") echo "selected" ;?>>BANCO FICOHSA PANAMA</option>
                                                                                                                <option value="000000071" <?php if($ruta_destino=="000000071") echo "selected" ;?>>BANCO GENERAL</option>
                                                                                                                <option value="000001517" <?php if($ruta_destino=="000001517") echo "selected" ;?>>BANCO PICHINCHA PANAMA</option>
                                                                                                                <option value="000001258" <?php if($ruta_destino=="000001258") echo "selected" ;?>>CANAL BANK</option>
                                                                                                                <option value="000001588" <?php if($ruta_destino=="000001588") echo "selected" ;?>>BANESCO</option>
                                                                                                                <option value="000001614" <?php if($ruta_destino=="000001614") echo "selected" ;?>>BANISI, S.A.</option>
                                                                                                                <option value="000001164" <?php if($ruta_destino=="000001164") echo "selected" ;?>>BANK OF CHINA</option>
                                                                                                                <option value="000001685" <?php if($ruta_destino=="000001685") echo "selected" ;?>>BALBOA BANK & TRUST</option>
                                                                                                                <option value="000001397" <?php if($ruta_destino=="000001397") echo "selected" ;?>>BCT BANK</option>
                                                                                                                <option value="000000518" <?php if($ruta_destino=="000000518") echo "selected" ;?>>BICSA</option>
                                                                                                                <option value="000002529" <?php if($ruta_destino=="000002529") echo "selected" ;?>>CACECHI</option>
                                                                                                                <option value="000000770" <?php if($ruta_destino=="000000770") echo "selected" ;?>>CAJA DE AHORROS</option>
                                                                                                                <option value="000001591" <?php if($ruta_destino=="000001591") echo "selected" ;?>>CAPITAL BANK</option>
                                                                                                                <option value="000000039" <?php if($ruta_destino=="000000039") echo "selected" ;?>>CITIBANK</option>
                                                                                                                <option value="000002532" <?php if($ruta_destino=="000002532") echo "selected" ;?>>COEDUCO</option>
                                                                                                                <option value="000002516" <?php if($ruta_destino=="000002516") echo "selected" ;?>>COOESAN</option>
                                                                                                                <option value="000002503" <?php if($ruta_destino=="000002503") echo "selected" ;?>>COOPEDUC</option>
                                                                                                                <option value="000005005" <?php if($ruta_destino=="000005005") echo "selected" ;?>>COOPERATIVA CRISTOBAL</option>
                                                                                                                <option value="000000712" <?php if($ruta_destino=="000000712") echo "selected" ;?>>COOPERATIVA DE PROFESIONALES</option>
                                                                                                                <option value="000002545" <?php if($ruta_destino=="000002545") echo "selected" ;?>>COOPEVE</option>
                                                                                                                <option value="000001106" <?php if($ruta_destino=="000001106") echo "selected" ;?>>CREDICORP BANK</option>
                                                                                                                <option value="000001151" <?php if($ruta_destino=="000001151") echo "selected" ;?>>GLOBAL BANK</option>
                                                                                                                <option value="000000026" <?php if($ruta_destino=="000000026") echo "selected" ;?>>BANISTMO S.A.</option>
                                                                                                                <option value="000001630" <?php if($ruta_destino=="000001630") echo "selected" ;?>>MERCANTIL BANK</option>
                                                                                                                <option value="000001067" <?php if($ruta_destino=="000001067") echo "selected" ;?>>METROBANK S.A.</option>
                                                                                                                <option value="000001478" <?php if($ruta_destino=="000001478") echo "selected" ;?>>MMG BANK</option>
                                                                                                                <option value="000000372" <?php if($ruta_destino=="000000372") echo "selected" ;?>>MULTIBANK</option>
                                                                                                                <option value="000001672" <?php if($ruta_destino=="000001672") echo "selected" ;?>>PRIVAL BANK</option>
                                                                                                                <option value="000000424" <?php if($ruta_destino=="000000424") echo "selected" ;?>>THE BANK OF NOVA SCOTIA</option>
                                                                                                                <option value="000001494" <?php if($ruta_destino=="000001494") echo "selected" ;?>>ST. GEORGES BANK</option>
                                                                                                                <option value="000000408" <?php if($ruta_destino=="000000408") echo "selected" ;?>>TOWERBANK</option>
                                                                                                                <option value="000001708" <?php if($ruta_destino=="000001708") echo "selected" ;?>>UNIBANK</option>
                                                                                                                <option value="000001740" <?php if($ruta_destino=="000001740") echo "selected" ;?>>ALLBANK</option>
                                                                                                                <option value="000001656" <?php if($ruta_destino=="000001656") echo "selected" ;?>>BBP BANK, S.A.</option>
                                                                                                                <option value="000005018" <?php if($ruta_destino=="000005018") echo "selected" ;?>>EDIOACC, R.L</option>
                                                                                                                <option value="000000916" <?php if($ruta_destino=="000000916") echo "selected" ;?>>BANCO DEL PACÍFICO (PANAMÁ), S.A.</option>
                                                                                                                <option value="000001805" <?php if($ruta_destino=="000001805") echo "selected" ;?>>ATLAS BANK</option>
                                                                                                                <option value="000005021" <?php if($ruta_destino=="000005021") echo "selected" ;?>>ECASESO</option>
                                                                                                                <option value="000005034" <?php if($ruta_destino=="000005034") echo "selected" ;?>>COOPRAC, R.L.</option>
                                                                                                                <option value="000000181" <?php if($ruta_destino=="000000181") echo "selected" ;?>>DAVIVIENDA</option>
                                                                                                        </select>
												</div>
											</div>											
                                                                                        
                                                                                        <div class="form-group">
												<label class="control-label col-md-2">Producto:</label>
												<div class="col-md-4">
													<select name="producto_destino" id="producto_destino" class="form-control">
                                                                                                                <option value="0" <?php $producto_destino=$fetch['producto_destino']; if($producto_destino=="0") echo "selected" ;?>>Seleccione...</option>
                                                                                                                <option value="04" <?php $producto_destino=$fetch['producto_destino']; if($producto_destino=="04") echo "selected" ;?>>CUENTA DE AHORROS</option>
                                                                                                                <option value="03-1" <?php $producto_destino=$fetch['producto_destino']; if($producto_destino=="03-1") echo "selected" ;?>>CUENTA CORRIENTE</option>
                                                                                                                <option value="03-2" <?php $producto_destino=$fetch['producto_destino']; if($producto_destino=="03-2") echo "selected" ;?>>TARJETAS DE CREDITO Y PREPAGADAS</option>
                                                                                                                <option value="07" <?php $producto_destino=$fetch['producto_destino']; if($producto_destino=="07") echo "selected" ;?>>PRESTAMOS</option>
                                                                                                        </select>
												</div>												
											</div>	
                                                                                    
                                                                                        <div class="form-group">
                                                                                                <label class="control-label col-md-2">Cuenta:</label>
                                                                                                <div class="col-md-4">
                                                                                                    <input class="form-control" name="cuenta_destino" type="text" id="cuenta_destino" value="<?php  echo $fetch['cuenta_destino'];  ?>">
                                                                                                </div>	
                                                                                        </div>
                                                                              
																				
										</div>
									</div>
                                                                        <div class="row">
                                                                             <div class="col-md-6">
                                                                                 &nbsp;
                                                                            </div>
                                                                            <div class="col-md-6">
                                                                                    <br>
<!--                                                                                    <input class="btn btn-primary" value="Generar Cuotas" onclick="javascript:generar_cuotas();" title="Debe generar las cuotas antes de enviar" required>-->
                                                                                    <input class="btn btn-primary" value="Guardar" onclick="javascript:document.form1.submit();">
                                                                            </div>
                                                                        </div>
                                                                        <br>
									<?php 
										$consulta   = "SELECT * FROM nomprestamos_detalles 
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
													<th class="center"></th>
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
																<td class="center"><a class="btn btn-primary" href="actualizar_prestamo.php?ficha=<?php echo $fetch2['ficha'];?>&numcuo=<?php echo $fetch2['numcuo'];?>&numpre=<?php echo $numpre;?>&pre=cancelado"><i class="fa fa-exchange" aria-hidden="true" title="Colocar como Prestamo Pendiente"></i></a>
                                                                                                                                
																</td>
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
																<td class="center"><a class="btn btn-primary" href="actualizar_prestamo.php?ficha=<?php echo $fetch2['ficha'];?>&numcuo=<?php echo $fetch2['numcuo'];?>&numpre=<?php echo $numpre;?>&pre=pendiente"><i class="fa fa-money" aria-hidden="true" title="Cancelar Cuota"></i></a>
                                                                                                                                <a class="btn btn-primary" href="javascript:enviar(<?php echo $fetch2['ficha']; ?>,<?php echo $numpre; ?>,<?php echo $fetch2['numcuo']; ?>);"> <i class="fa fa-pencil" aria-hidden="true" title="Editar Cuota"></i></a>
																</td>
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