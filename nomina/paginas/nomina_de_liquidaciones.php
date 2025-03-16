<?php
require_once('../lib/database.php');

$db = new Database($_SESSION['bd']);

$termino     = isset($_SESSION['termino'])  ? $_SESSION['termino']  : '' ;
$registro_id = isset($_POST['registro_id']) ? $_POST['registro_id'] : '' ;
$op          = isset($_POST['op'])          ? $_POST['op'] : '';

if ($op==3) // Se presiono el boton de Eliminar
{
	$sql = "DELETE FROM nom_nominas_pago
	        WHERE codnom='{$registro_id}' AND tipnom='{$_SESSION['codigo_nomina']}' 
	        AND   codtip='{$_SESSION['codigo_nomina']}'";
	$db->query($sql);

	$sql = "DELETE FROM nom_movimientos_nomina
	        WHERE codnom='{$registro_id}'
	        AND   tipnom='{$_SESSION['codigo_nomina']}'";
	$db->query($sql);

	echo "<script> window.location.href = 'nomina_de_liquidaciones.php'; </script>";
}

$sql = "SELECT codnom, tipnom, descrip, status, periodo_ini, periodo_fin, fechapago, codtip
		FROM   nom_nominas_pago
        WHERE  tipnom='{$_SESSION['codigo_nomina']}' AND codtip='{$_SESSION['codigo_nomina']}'
        AND    frecuencia=10
        ORDER BY codnom, descrip";
$res = $db->query($sql);

$sql_max = "SELECT max(codnom) as maximo
            FROM   nom_nominas_pago
            WHERE  tipnom='{$_SESSION['codigo_nomina']}'";
$fila_max = $db->query($sql_max)->fetch_assoc();
?>
<?php include("../header4.php"); ?>
<link href="../../includes/assets/css/custom-datatables.css" rel="stylesheet" type="text/css"/>
<style type="text/css">
.portlet > .portlet-title > .actions > .btn.btn-sm {
	margin-top: -9px !important;
}

td.icono{
	padding-left: 0px !important;
	padding-right: 0px !important;
	width: 25px !important;
	text-align: center;
}

table.table thead .sorting_asc {
    background-position: right 13px;    
}

table.table thead .sorting_desc {
    background-position: right 5px;
}
</style>
<script type="text/javascript">
function buscarPersonal(id, codtip, bandera)
{
	if(bandera==0)
	{
		document.location.href = "buscar_empleado2.php?codigo_nomina="+id+"&codt="+codtip;
	}
	else if(bandera==1)
	{
		document.location.href = "movimientos_nomina_liquidaciones.php?codigo_nomina="+id+"&codt="+codtip+"&bandera="+bandera;	
	}
}

function abrirVentana(pagina, alto, ancho, left=0, top=0) 
{
	window.open(pagina, 'mainWindow', 'width='+ ancho +', height='+ alto +', left='+ left +', top='+top);
}

function cerrarVentana()
{
	window.close();
}

function showProcesando()
{
    App.blockUI({
        target: '#blockui_portlet_body',
        boxed: true,
        message: 'Procesando'
    });
}

function enviar(op, id, nomina, codtip)
{	
	if (op==2) // Opcion de Modificar
	{	 	
		$("#registro_id").val(id);
		$("#op").val(op);
		$("#frmPrincipal").attr("action", "ag_nomina_liquidaciones.php").submit();	
	}
	if (op==3) // Opcion de Eliminar
	{		
		if (confirm("\u00BFEst\u00E1 seguro que desea eliminar el registro?"))
		{
			showProcesando();

			$("#registro_id").val(id);
			$("#op").val(op);
			$("#frmPrincipal").submit();	
		}		
	}
	if (op==6) // Cerrar Nómina
	{	
		if (confirm("\u00BFEst\u00E1 seguro que desea cerrar esta <?php echo $termino; ?>?"))
		{
			showProcesando();

			$.get( "cerrar_nomina_liquidaciones.php", { codigo_nomina: id }, function() {
					document.location.href = "nomina_de_liquidaciones.php";
				})
				.fail(function() {
					console.log( "La solicitud ha fallado");
				});
		}
	}
	if (op==7) // Abrir Nómina
	{	
		showProcesando();

		$.get( "abrir_nomina.php", { codigo_nomina: id }, function() {
				document.location.href = "nomina_de_liquidaciones.php";
			})
			.fail(function() {
				console.log( "La solicitud ha fallado");
			});
	}
        
        if(op==100)
	{		// Generar Nómina
		document.frmPrincipal.registro_id.value=id;
		document.frmPrincipal.codigo_nomina.value=nomina;
		abrirVentana('movimientos_nomina_patronales_recalcular.php?registro_id='+id+'&codigo_nomina='+nomina,250,900,0);
	}
}
</script>
<div class="page-container">
	<!-- BEGIN CONTENT -->
	<div class="page-content-wrapper">
		<div class="page-content">
			<!-- BEGIN PAGE CONTENT-->
			<div class="row">
				<div class="col-md-12">
					<div class="portlet box blue">
						<div class="portlet-title">
							<div class="caption">
								<a href="nomina_de_liquidaciones.php"><img src="images/Clipboard.png" width="23" height="21"/></a> Lista de <?php echo $termino; ?>s de Liquidaci&oacute;n: <?php echo $_SESSION['nomina']; ?>
							</div>
							<div class="actions">
							<?php if($_SESSION['planilla_liquidacion_agregar']):?>
								<a class="btn btn-sm blue"  onclick="javascript: window.location='ag_nomina_liquidaciones.php'">
									<i class="fa fa-plus"></i> Agregar
								</a>
							<?php endif;?>
								<a class="btn btn-sm blue"  onclick="javascript: window.location='menu_transacciones.php'">
									<i class="fa fa-arrow-left"></i> Regresar
								</a>
							</div>
						</div>
						<div class="portlet-body" id="blockui_portlet_body">
							<form method="post" name="frmPrincipal" id="frmPrincipal">
								<table class="table table-striped table-bordered table-hover" id="table_datatable">
									<thead>
										<tr>
											<th><?php echo $termino; ?></th>
											<th>Tipo <?php echo $termino; ?></th>
											<th class="text-center">Descripci&oacute;n</th>
											<th>Estado</th>
											<th>Inicio</th>
											<th>Final</th>
											<th>Pago</th>
											<th>&nbsp;</th>
											<th>&nbsp;</th>
											<th>&nbsp;</th>
											<th>&nbsp;</th>
                                                                                        <th>&nbsp;</th>
										</tr>
									</thead>
									<tbody>
									<?php
										while( $fila = $res->fetch_assoc() )
										{
											$sql2 = "SELECT codcon 
											         FROM   nom_movimientos_nomina 
											         WHERE  tipnom='{$_SESSION['codigo_nomina']}' AND codnom='{$fila['codnom']}' ";
											$res2 = $db->query($sql2);

											$bandera = 0;
											if($res2->num_rows > 0)
											{
												$bandera = 1;
											}
											?>
											<tr class="odd gradeX">
												<td><?php echo $fila['codnom']; ?></td>
												<td><?php echo $fila['tipnom']; ?></td>
												<td><?php echo $fila['descrip']; ?></td>
												<td><?php echo $fila['status']; ?></td>
												<td><?php echo date("d/m/Y", strtotime($fila['periodo_ini'])); ?></td>
												<td><?php echo date("d/m/Y", strtotime($fila['periodo_fin'])); ?></td>
												<td><?php echo date("d/m/Y", strtotime($fila['fechapago'])); ?></td>
												<td><a href="javascript: buscarPersonal(<?php echo($fila['codnom']); ?>,<?php echo $fila['codtip']; ?>,<?php echo $bandera; ?>);"><img src="images/view.gif" title="Movimientos" width="16" height="16"></a></td>	
												<?php
												if( $fila["status"] == 'A' )
												{
												?>
													<td>
														<?php if($_SESSION['planilla_liquidacion_editar']):?>
														<a href="javascript: enviar(2, <?php echo $fila['codnom']; ?>, 0, 0);" title="Consultar <?php echo $termino; ?>"><img src="img_sis/ico_list.gif" width="15" height="15"></a>
														<?php endif;?>
													</td>
													<td>
														<?php if($_SESSION['planilla_liquidacion_eliminar']):?>
														<a href="javascript: enviar(3, <?php echo $fila['codnom']; ?>, 0, 0);" title="Eliminar <?php echo $termino; ?>"><img src="../imagenes/delete.gif" width="16" height="16"></a>
														<?php endif;?>
													</td>
													<td>
														<?php if($_SESSION['planilla_liquidacion_cerrar']):?>
														<a href="javascript: enviar(6, <?php echo $fila['codnom']; ?>, 0, 0);" title="Cerrar <?php echo $termino; ?>"><img src="../imagenes/thumbs-up-icon.png" width="16" height="16"></a>
														<?php endif;?>
													</td>
                                                                                                        <td>
														<?php if($_SESSION['planilla_liquidacion_cerrar']):?>
														<a href="javascript: enviar(100, <?php echo $fila['codnom']; ?>, 0, 0);" title="Recalcular Patronales <?php echo $termino; ?>"><img src="imagenes/txt.png" width="16" height="16"></a>
														<?php endif;?>
													</td>
												<?php
												}
												else
												{
													if(  $_SESSION['planilla_liquidacion_abrir'])
													{
														?> <td><a href="javascript: enviar(7, <?php echo $fila['codnom']; ?>, 0, 0)" title="Abrir <?php echo $termino; ?>"><img src="../imagenes/ok.gif" width="16" height="16"></a></td> <?php
													}
													else
													{ 
														?> <td>&nbsp;</td> <?php 
													} 
													?>
													<td>&nbsp;</td>
													<td>&nbsp;</td>
                                                                                                        <?php
                                                                                                        if(  $_SESSION['planilla_liquidacion_abrir'])
													{
														?> <td><a href="javascript: enviar(100, <?php echo $fila['codnom']; ?>, 0, 0)" title="Recalcular Patronales <?php echo $termino; ?>"><img src="imagenes/txt.png" width="16" height="16"></a></td> <?php
													}
													else
													{ 
														?> <td>&nbsp;</td> <?php 
													} 
												
												}?>
											</tr>
										  <?php
										}
									?>
									</tbody>
								</table>
							    <input type="hidden" id="codigo_nomina" name="codigo_nomina" value="">
								<input type="hidden" id="registro_id"   name="registro_id"   value="">
								<input type="hidden" id="op" name="op" value="">
							</form>
						</div>
					</div>
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
   		"aaSorting":         [[0, 'desc']],
    	"iDisplayLength":    25,
    	"bStateSave" : true,
    	"sPaginationType":   "bootstrap_extended",
        "oLanguage": {
        	"sSearch":       "<img src='../../includes/imagenes/icons/magnifier.png' width='16' height='16'> Buscar:",
            "sLengthMenu":   "Mostrar _MENU_",
            "sInfoEmpty":    "",
            //"sInfo":         "Total _TOTAL_ registros",
            "sInfoFiltered": "",
  		    "sEmptyTable":   "No hay datos disponibles",
            "sZeroRecords":  "No se encontraron registros",
            "oPaginate": {
                "sPrevious": "P&aacute;gina Anterior",
                "sNext":     "P&aacute;gina Siguiente",
                "sPage":     "P&aacute;gina",
                "sPageOf":   "de",
            }
        },
        "aLengthMenu": [
            [5, 10, 25, 50,  -1],
            [5, 10, 25, 50, "Todos"]
        ],
        "aoColumnDefs": [
        	{ 'bSortable': false,      'aTargets': [1, 2, 3, 4, 5, 6, 7, 8, 9, 10] },
            { 'bSearchable': false,    'aTargets': [7, 8, 9, 10] },
            { 'sClass': 'icono',       'aTargets': [7, 8, 9, 10] },
            { 'sClass': 'text-center', 'aTargets': [0, 1, 3, 4, 5, 6] }
        ],
		"fnDrawCallback": function() {
		    $('#table_datatable_filter input').attr("placeholder", "Escriba frase para buscar");
		},
		"fnInfoCallback": function (oSettings, iStart, iEnd, iMax, iTotal, sPre) {
	        if (iTotal == 1)
	            return "Total " + iTotal + " registro";
	        else 
	            return "Total " + iTotal + " registros";
		}
	});

	$('#table_datatable_wrapper .dataTables_filter input').addClass("form-control input-medium input-inline");
	$('#table_datatable_wrapper .dataTables_length select').addClass("form-control input-xsmall");
});
</script>
</body>
</html>