<?php 
session_start();
ob_start();

require_once '../lib/common.php';
include ("../paginas/funciones_nomina.php");
include ("../paginas/func_bd.php");
// error_reporting(0);
$conexion=conexion();

$registro_id=$_POST['registro_id'];	
$op=$_POST['op'];

if ($op==3) //Se presiono el boton de Eliminar
{	
	$consulta = "SELECT * 
	             FROM   nomprestamos_detalles 
	             WHERE  numpre={$registro_id} AND estadopre='Cancelada'"; 

	$resultadoCon = query($consulta, $conexion); //  sql_ejecutar($query);

	if( num_rows($resultadoCon) > 0 )
		$estado = 'Anulada con PAGOS';
	else
		$estado = 'Anulada';
	
	$consulta = "UPDATE nomprestamos_cabecera SET 
	             estadopre = '$estado', 
	             usuarioanu = '{$_SESSION["nombre"]}', 
	             fechaanu = '".date('Y-m-d h:i:s')."' 
	             WHERE numpre = {$registro_id}";

	$resultado = query($consulta, $conexion);
		
	$consulta = "UPDATE nomprestamos_detalles SET 
	             estadopre='Anulada' 
	             WHERE numpre={$registro_id} AND estadopre='Pendiente'";

	$resultado = query($consulta, $conexion);	

	activar_pagina("prestamos_list.php");	 
}   

$sql = "SELECT nc.ficha, np.cedula, np.apenom, nc.monto, nc.codigopr, 
               nc.estadopre, npp.descrip, nc.numpre, nc.monto_acumulado, (nc.monto-nc.monto_acumulado) as saldo,
               nc.gastos_admon, tp.nom_tipos_prestamos as tipo
        FROM   nomprestamos_cabecera nc 
        JOIN   nompersonal np ON (np.ficha=nc.ficha) 
        JOIN   nomprestamos npp ON (npp.codigopr=nc.codigopr)
        JOIN   tipos_prestamos tp ON (tp.id_tipos_prestamos=nc.id_tipoprestamo)
        WHERE  np.tipnom='{$_SESSION['codigo_nomina']}' AND nc.estadopre in ('Pendiente')
		ORDER BY np.ficha, nc.codigopr";
		
$sql2 = "SELECT nc.ficha, np.cedula, np.apenom, nc.monto, nc.codigopr, 
nc.estadopre, npp.descrip, nc.numpre, nc.monto_acumulado, (nc.monto-nc.monto_acumulado) as saldo,
               nc.gastos_admon, tp.nom_tipos_prestamos as tipo
FROM   nomprestamos_cabecera nc 
JOIN   nompersonal np ON (np.ficha=nc.ficha) 
JOIN   nomprestamos npp ON (npp.codigopr=nc.codigopr)
JOIN   tipos_prestamos tp ON (tp.id_tipos_prestamos=nc.id_tipoprestamo)
WHERE  np.tipnom='{$_SESSION['codigo_nomina']}' AND nc.estadopre not in ('Pendiente')
ORDER BY np.ficha, nc.codigopr";
$res = query($sql, $conexion);
$res2 = query($sql2, $conexion);
$total = 0;
$total2 = 0;
$numpre=array();
$ficha=array();
$nomposicion_id=array();
$apenom=array();
$monto=array();
$monto_acumulado=array();
$saldo=array();
$gastos_admon=array();
$tipo=array();
$cedula=array();
$descrip=array();
$estadopre=array();

$numpre2=array();
$ficha2=array();
$nomposicion_id2=array();
$apenom2=array();
$monto2=array();
$monto_acumulado2=array();
$saldo2=array();
$gastos_admon2=array();
$tipo2=array();
$cedula2=array();
$descrip2=array();
$estadopre2=array();

while( $fila=fetch_array($res) )
{ 	array_push($numpre,$fila['numpre']);
	array_push($ficha,$fila['ficha']);
	array_push($cedula,$fila['cedula']);
	array_push($apenom,$fila['apenom']);
	array_push($monto,number_format($fila['monto'], 2, ',', '.'));
        array_push($monto_acumulado,number_format($fila['monto_acumulado'], 2, ',', '.'));
        array_push($saldo,number_format($fila['saldo'], 2, ',', '.'));
        array_push($gastos_admon,number_format($fila['gastos_admon'], 2, ',', '.'));
	array_push($descrip,$fila['descrip']);
        array_push($tipo,$fila['tipo']);
	array_push($estadopre,$fila['estadopre']);
	$total++;
}

while( $fila2=fetch_array($res2) )
{ 	array_push($numpre2,$fila2['numpre']);
	array_push($ficha2,$fila2['ficha']);
	array_push($cedula2,$fila2['cedula']);
	array_push($apenom2,$fila2['apenom']);
	array_push($monto2,number_format($fila2['monto'], 2, ',', '.'));
        array_push($monto_acumulado2,number_format($fila['monto_acumulado'], 2, ',', '.'));
        array_push($saldo2,number_format($fila['saldo'], 2, ',', '.'));
        array_push($gastos_admon2,number_format($fila['gastos_admon'], 2, ',', '.'));
	array_push($descrip2,$fila2['descrip']);
        array_push($tipo2,$fila2['tipo']);
	array_push($estadopre2,$fila2['estadopre']);
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
							  <img src="../imagenes/21.png" width="20" height="20" class="icon"> Pr&eacute;stamos
							</div>
							<div class="actions">
								<a class="btn btn-sm blue"  onclick="javascript: window.location='../../reportes/excel/resumen_prestamos.php'">
									<i class="fa fa-print"></i>
									Imprimir
								</a>
								<a class="btn btn-sm blue"  onclick="javascript: window.location='prestamos_agregar.php'">
									<i class="fa fa-plus"></i>
									Agregar
								</a>
								<a class="btn btn-sm blue"  onclick="javascript: window.location='menu_prestamos.php'">
									<i class="fa fa-arrow-left"></i> Regresar
								</a>
							</div>
						</div>
						<div class="portlet-body">
							
						<ul class="nav nav-tabs">
								<li class="active"><a href="#tab_0" id="tab-personal" data-toggle="tab">Pendientes</a></li>                                                               
								<li><a href="#tab_1" id="tab-egresados" data-toggle="tab">Anulados/Cancelados</a></li>
								
						</ul>
						<div class="tab-content">
							<div class="tab-pane active" id="tab_0">
								<form action="" method="post" name="frmPrincipal" id="frmPrincipal">
								<table class="table table-striped table-bordered table-hover" id="table_datatable">
								<thead>
								<tr>
									<th>Prest.</th>
									<th># Colab.</th>
									<th>Cédula</th>
									<th>Nombres y Apellidos</th>
                                                                        <th>Acreedor</th>
                                                                        <th>Tipo</th>
									<th>Gastos Adm.</th>
									<th>Acumulado</th>
									<th>Saldo</th>
                                                                        <th>Total</th>
									<th>Estado</th>
									<th>&nbsp;</th>
									<th>&nbsp;</th>
									<th>&nbsp;</th>
									<th>&nbsp;</th>
									<th>&nbsp;</th>
									<th>&nbsp;</th>
								</tr>
								</thead>
								<tbody>
								<?php								
									for($i=0;$i<$total;$i++)
									{ 	$codigo=$numpre[$i];
									?>
										<tr class="odd gradeX" id="num" opcion="<?php echo $fila['numpre'];?>">
											<td><?php echo $numpre[$i];?></td>
											<td><?php echo $ficha[$i];?></td>
											<td><?php echo $cedula[$i];?></td>
                                                                                        <td><?php echo utf8_encode($apenom[$i]);?></td>
                                                                                        <td><?php echo $descrip[$i];?></td>
                                                                                        <td><?php echo $tipo[$i];?></td>
											<td><?php echo $gastos_admon[$i];?></td>
											<td><?php echo $monto_acumulado[$i];?></td>
                                                                                        <td><?php echo $saldo[$i];?></td>
											<td><?php echo $monto[$i];?></td>
											<td><?php echo $estadopre[$i];?></td>
											<td style="text-align: center; width: 22px !important">
											<a href="prestamos_ver.php?numpre=<?php echo $codigo; ?>" title="Ver">
											<img src="../imagenes/view.gif" width="16" height="16"></a>
											</td>
											<?php
												if($estadopre[$i] == 'Pendiente')
												{
												?>
													<td style="text-align: center; width: 22px !important">
													<a href="prestamos_edit.php?numpre=<?php echo $codigo; ?>" title="Editar">
													<img src="../../includes/imagenes/icons/pencil.png" width="16" height="16"></a>
													</td>
													<td style="text-align: center; width: 22px !important">
														<a href="prestamos_refinanciar.php?numpre=<?php echo $codigo; ?>" title="Refinanciar Prestamo">
														<img src="../imagenes/35.png" alt="Refinanciar" width="16" height="16"></a>
													</td>
													<td style="text-align: center; width: 22px !important">
														<a href="javascript:enviar(<?php echo(3); ?>,<?php echo $codigo; ?>);" title="Cancelar Prestamo">
														<img src="../imagenes/29.png" alt="Cancelar" width="16" height="16"></a>
													</td>
													<td style="text-align: center; width: 22px !important">
														<a href="../tcpdf/reporte_prestamo.php?numpre=<?php echo $codigo; ?>" title="Reporte">
														<img src="../imagenes/pdf.png" alt="Reporte" width="16" height="16"></a>
													</td>
													<td style="text-align: center; width: 22px !important">
														<a href="prestamos_eliminar.php?numpre=<?php echo $codigo; ?>" title="Eliminar">
														<img src="../imagenes/delete.gif" alt="Eliminar" width="16" height="16"></a>
													</td>
												<?php
												}
												else
												{
												?>
													<td>&nbsp;</td>
													<td>&nbsp;</td>
													<td>&nbsp;</td>
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
									<th>Prest.</th>
									<th># Colab.</th>
									<th>Cédula</th>
									<th>Nombres y Apellidos</th>
                                                                        <th>Acreedor</th>
                                                                        <th>Tipo</th>
                                                                        <th>Gastos Adm.</th>
									<th>Acumulado</th>
									<th>Saldo</th>
                                                                        <th>Total</th>
									<th>Estado</th>
									<th>&nbsp;</th>
									<th>&nbsp;</th>
									<th>&nbsp;</th>
									<th>&nbsp;</th>
									<th>&nbsp;</th>
									<th>&nbsp;</th>
								</tr>
								</thead>
								<tbody>
								<?php								
									for($i=0;$i<$total2;$i++)
									{ 	$codigo2=$numpre2[$i];
									?>
										<tr class="odd gradeX">
											<td><?php echo $numpre2[$i];?></td>
											<td><?php echo $ficha2[$i];?></td>
											<td><?php echo $cedula2[$i];?></td>
											<td><?php echo utf8_encode($apenom2[$i]);?></td>
											<td><?php echo $descrip2[$i];?></td>
                                                                                        <td><?php echo $tipo2[$i];?></td>
                                                                                        <td><?php echo $gastos_admon2[$i];?></td>
											<td><?php echo $monto_acumulado2[$i];?></td>
                                                                                        <td><?php echo $saldo2[$i];?></td>
											<td><?php echo $monto2[$i];?></td>
											<td><?php echo $estadopre2[$i];?></td>
											<td style="text-align: center; width: 22px !important">
											<a href="prestamos_ver.php?numpre=<?php echo $codigo2; ?>" title="Ver">
											<img src="../imagenes/view.gif" width="16" height="16"></a>
											</td>
											<?php
												if($estadopre2[$i] == 'Pendiente')
												{
												?>
													<td style="text-align: center; width: 22px !important">
													<a href="prestamos_edit.php?numpre=<?php echo $codigo; ?>" title="Editar">
													<img src="../../includes/imagenes/icons/pencil.png" width="16" height="16"></a>
													</td>
													<td style="text-align: center; width: 22px !important">
														<a href="prestamos_refinanciar.php?numpre=<?php echo $codigo; ?>" title="Refinanciar Prestamo">
														<img src="../imagenes/35.png" alt="Refinanciar" width="16" height="16"></a>
													</td>
													<td style="text-align: center; width: 22px !important">
														<a href="javascript:enviar(<?php echo(3); ?>,<?php echo $codigo; ?>);" title="Cancelar Prestamo">
														<img src="../imagenes/29.png" alt="Cancelar" width="16" height="16"></a>
													</td>
													<td style="text-align: center; width: 22px !important">
														<a href="../tcpdf/reporte_prestamo.php?numpre=<?php echo $codigo; ?>" title="Reporte">
														<img src="../imagenes/pdf.png" alt="Reporte" width="16" height="16"></a>
													</td>
													<td style="text-align: center; width: 22px !important">
														<a href="prestamos_eliminar.php?numpre=<?php echo $codigo; ?>" title="Eliminar">
														<img src="../imagenes/delete.gif" alt="Eliminar" width="16" height="16"></a>
													</td>
												<?php
												}
												else
												{
												?>
													<td>&nbsp;</td>
													<td>&nbsp;</td>
													<td>&nbsp;</td>
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
		$("tr#num").each(function(){
			var numpre = $(this).attr("opcion");
			// ejecutamos la función click sobr el elemento que estamos clickando
			$(this).off("click").on("click",function(){
				$.get("saldo_prestamo.php",{numpre:numpre},function(res){
					$("#modal_saldos").empty();
					$("#modal_saldos").append(res);
				});
			});
		});

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
                    { 'bSortable': false, 'aTargets': [6] },
                    { "bSearchable": false, "aTargets": [6] },
                   // { "sWidth": "8%", "aTargets": [6] },
                    { 'bSortable': false, 'aTargets': [7] },
                    { "bSearchable": false, "aTargets": [7] },
                   // { "sWidth": "8%", "aTargets": [7] },
                    { 'bSortable': false, 'aTargets': [8] },
                    { "bSearchable": false, "aTargets": [8] },
                   // { "sWidth": "8%", "aTargets": [8] },
                    { 'bSortable': false, 'aTargets': [9] },
                    { "bSearchable": false, "aTargets": [9] },
                   // { "sWidth": "8%", "aTargets": [9] },
                    { 'bSortable': false, 'aTargets': [10] },
                    { "bSearchable": false, "aTargets": [10] },

                    { 'bSortable': false, 'aTargets': [11] },
                    { "bSearchable": false, "aTargets": [11] },

                    { 'bSortable': false, 'aTargets': [12] },
                    { "bSearchable": false, "aTargets": [12] },
                ],
				 "fnDrawCallback": function() {
				        $('#table_datatable_filter input').attr("placeholder", "Escriba frase para buscar");
				 }
            });

            $('#table_datatable').on('change', 'tbody tr .checkboxes', function(){
                 $(this).parents('tr').toggleClass("active");
            });


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
                    { 'bSortable': false, 'aTargets': [6] },
                    { "bSearchable": false, "aTargets": [6] },
                   // { "sWidth": "8%", "aTargets": [6] },
                    { 'bSortable': false, 'aTargets': [7] },
                    { "bSearchable": false, "aTargets": [7] },
                   // { "sWidth": "8%", "aTargets": [7] },
                    { 'bSortable': false, 'aTargets': [8] },
                    { "bSearchable": false, "aTargets": [8] },
                   // { "sWidth": "8%", "aTargets": [8] },
                    { 'bSortable': false, 'aTargets': [9] },
                    { "bSearchable": false, "aTargets": [9] },
                   // { "sWidth": "8%", "aTargets": [9] },
                    { 'bSortable': false, 'aTargets': [10] },
                    { "bSearchable": false, "aTargets": [10] },

                    { 'bSortable': false, 'aTargets': [11] },
                    { "bSearchable": false, "aTargets": [11] },

                    { 'bSortable': false, 'aTargets': [12] },
                    { "bSearchable": false, "aTargets": [12] },
                ],
				 "fnDrawCallback": function() {
				        $('#table_datatable2_filter input').attr("placeholder", "Escriba frase para buscar");
				 }
            });

            $('#table_datatable2').on('change', 'tbody tr .checkboxes', function(){
                 $(this).parents('tr').toggleClass("active");
            });
            $('#table_datatable_wrapper .dataTables_filter input').addClass("form-control input-medium input-inline"); 
            $('#table_datatable_wrapper .dataTables_length select').addClass("form-control input-xsmall"); 
	 });
</script>
</body>
</html>
