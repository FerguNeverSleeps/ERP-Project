<?php 
require_once('../lib/database.php');

$db = new Database($_SESSION['bd']);

$termino     = isset($_SESSION['termino'])  ? $_SESSION['termino']  : '';
$registro_id = isset($_POST['registro_id']) ? $_POST['registro_id'] : '';	
$op          = isset($_POST['op'])          ? $_POST['op']          : '';

if ($op==3) // Se presiono el boton de eliminar
{
	$sql = "DELETE FROM nom_nominas_pago 
	        WHERE codnom='{$registro_id}' AND tipnom='{$_SESSION['codigo_nomina']}' 
	        AND   codtip='{$_SESSION['codigo_nomina']}'";			
	$db->query($sql);

	$sql = "DELETE FROM nom_movimientos_nomina 
	        WHERE codnom='{$registro_id}' 
	        AND   tipnom='{$_SESSION['codigo_nomina']}'";
	$db->query($sql);
	
	echo "<script> window.location.href = 'nomina_de_vacaciones.php'; </script>";		 
} 

$sql = "SELECT codnom, tipnom, descrip, status, periodo_ini, periodo_fin, fechapago, codtip 
		FROM   nom_nominas_pago 
        WHERE  tipnom='{$_SESSION['codigo_nomina']}' AND codtip='{$_SESSION['codigo_nomina']}' 
        AND    frecuencia=8
        ORDER BY codnom, descrip";
$res = $db->query($sql);		

$sql_max = "SELECT max(codnom) as maximo 
            FROM   nom_nominas_pago 
            WHERE  tipnom='{$_SESSION['codigo_nomina']}' AND frecuencia=8";
$fila_max = $db->query($sql_max)->fetch_assoc();
?>
<?php include("../header4.php"); ?>
<link href="../../includes/assets/css/custom-datatables.css" rel="stylesheet" type="text/css"/>
<style type="text/css">
.portlet > .portlet-title > .actions > .btn.btn-sm {
	margin-top: -9px !important;
}

.ajustar-texto {
	white-space: normal !important;
}

.text-middle {
	vertical-align: middle !important
}

td.icono {
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
<script>
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
	if(op==2) // Opcion de Modificar
	{	 	
		$("#registro_id").val(id);
		$("#op").val(op);
		$("#frmPrincipal").attr("action", "ag_nomina_vacaciones.php").submit();	
		// document.frmPrincipal.registro_id.value=id;		
		// document.frmPrincipal.op.value=op;
		// document.frmPrincipal.action="ag_nomina_vacaciones.php";
		// document.frmPrincipal.submit();		
	}

	if(op==3)	// Opcion de Eliminar
	{		
		if (confirm("\u00BFEst\u00E1 seguro que desea eliminar el registro?"))
		{
			showProcesando();

			$("#registro_id").val(id);
			$("#op").val(op);
			$("#frmPrincipal").submit();	

			// document.frmPrincipal.registro_id.value=id;
			// document.frmPrincipal.op.value=op;
  			// document.frmPrincipal.submit();
		}		
	}
	
	if(op==4)	// Generar Nómina
	{	
		$("#registro_id").val(id);
		$("#codigo_nomina").val(nomina);
		// document.frmPrincipal.registro_id.value=id;
		// document.frmPrincipal.codigo_nomina.value=nomina;
		abrirVentana('barraprogreso_vacaciones.php?registro_id='+id+'&codigo_nomina='+nomina, 280, 580);
	}
	
	if(op==5) // Movimiento de Nómina
	{	
		document.location.href = "movimientos_nomina_vacaciones.php?codigo_nomina="+id+"&codt="+codtip+"&vac="+1;	
	}

	if(op==6) // Cerrar Nómina
	{
		if(confirm("\u00BFEst\u00E1 seguro que desea cerrar esta <?php echo $termino; ?>?"))
		{
			showProcesando();

			$.get( "cerrar_nomina_vacaciones.php", { codigo_nomina: id }, function() {
					document.location.href = "nomina_de_vacaciones.php";
				})
				.fail(function() {
					console.log( "La solicitud ha fallado");
				});

			// var cerrar_nomina=abrirAjax();
			// cerrar_nomina.open("GET", "cerrar_nomina.php?codigo_nomina="+id, true);
			// cerrar_nomina.onreadystatechange=function() 
			// {
			// 		if (cerrar_nomina.readyState==4)
			// 		{
			// 			document.location.href="nomina_de_vacaciones.php"
			// 		}
			// }
			// cerrar_nomina.send(null);
		}
	}
	if(op==7) // Abrir Nómina
	{	
		showProcesando();

		$.get( "abrir_nomina.php", { codigo_nomina: id }, function() {
				document.location.href = "nomina_de_vacaciones.php";
			})
			.fail(function() {
				console.log( "La solicitud ha fallado");
			});
		
		// var nomina=abrirAjax();
		// nomina.open("GET", "abrir_nomina.php?codigo_nomina="+id, true);
		// nomina.onreadystatechange=function() 
		// {
		// 	if (nomina.readyState==4)
		// 	{
		// 		document.location.href="nomina_de_vacaciones.php";
		// 	}
		// }
		// nomina.send(null);		
	}
        
        if(op==100)
	{		// Generar Nómina
		$("#registro_id").val(id);
		$("#codigo_nomina").val(nomina);
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
					<!-- BEGIN EXAMPLE TABLE PORTLET-->
					<div class="portlet box blue">
						<div class="portlet-title">
							<div class="caption">
								<a href="nomina_de_vacaciones.php"><img src="images/Clipboard.png" width="23" height="21"/></a> Lista de <?php echo $termino; ?>s de Vacaciones: <?php echo $_SESSION['nomina']; ?>
							</div>
							<div class="actions">
								<?php if($_SESSION['planilla_vacaciones_agregar']):?>
								<a class="btn btn-sm blue"  onclick="javascript: window.location='ag_nomina_vacaciones.php'">
									<i class="fa fa-plus"></i> Agregar
								</a>
								<?php endif;?>
								<a class="btn btn-sm blue"  onclick="javascript: window.location='submenu_vacaciones.php'">
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
                                                                                        <th>&nbsp;</th>
										</tr>
									</thead>
									<tbody>
									<?php								
										while( $fila = $res->fetch_assoc() )
										{ 
										?>
											<tr class="odd gradeX">
												<td><?php echo $fila['codnom']; ?></td>
												<td><?php echo $fila['tipnom']; ?></td>
												<td><?php echo $fila['descrip']; ?></td>
												<td><?php echo $fila['status']; ?></td>
												<td><?php echo date("d/m/Y", strtotime($fila['periodo_ini'])); ?></td>
												<td><?php echo date("d/m/Y", strtotime($fila['periodo_fin'])); ?></td>
												<td><?php echo date("d/m/Y", strtotime($fila['fechapago'])); ?></td>
												<td><a href="javascript: enviar(5, <?php echo $fila['codnom']; ?>, <?php echo $_SESSION['codigo_nomina']; ?>, <?php echo $fila['codtip']; ?>);" title="Movimientos"><img src="images/view.gif" width="16" height="16"></a></td>

												<?php
												if($fila["status"]=="A")
												{
												?>
													<td>
													<?php if($_SESSION['planilla_vacaciones_generar']):?>
														<a href="javascript: enviar(4, <?php echo $fila['codnom']; ?>, <?php echo $_SESSION['codigo_nomina']?>, 0);" title="Generar <?php echo $termino; ?>"><img src="img_sis/ico_propiedades.gif" width="15" height="15"></a>
													<?php endif;?>
													</td>
													<td>
													<?php if($_SESSION['planilla_vacaciones_editar']):?>
														<a href="javascript: enviar(2, <?php echo $fila['codnom']; ?>, 0, 0);" title="Consutar <?php echo $termino; ?>"><img src="img_sis/ico_list.gif"   width="15" height="15"></a>
													<?php endif;?>
													</td>
													<td>
													<?php if($_SESSION['planilla_vacaciones_eliminar']):?>
														<a href="javascript: enviar(3, <?php echo $fila['codnom']; ?>, 0, 0);" title="Eliminar <?php echo $termino; ?>"><img src="../imagenes/delete.gif" width="16" height="16"></a>
													<?php endif;?>
													</td>
													<td>
													<?php if($_SESSION['planilla_vacaciones_cerrar']):?>
														<a href="javascript: enviar(6, <?php echo $fila['codnom']; ?>, 0, 0);" title="Cerrar <?php echo $termino; ?>"><img src="../imagenes/thumbs-up-icon.png" width="16" height="16"></a>
													<?php endif;?>
													</td>
                                                                                                        <td>
													<?php if($_SESSION['planilla_vacaciones_cerrar']):?>
														<a href="javascript: enviar(100, <?php echo $fila['codnom']; ?>, 0, 0);" title="Recalcular Patronales <?php echo $termino; ?>"><img src="imagenes/txt.png" width="16" height="16"></a>
													<?php endif;?>
													</td>
												<?php
												}
												else
												{
													if( $_SESSION['planilla_vacaciones_abrir'])
													{
														?> <td><a href="javascript: enviar(7, <?php echo $fila['codnom']; ?>, 0, 0);" title="Abrir <?php echo $termino; ?>"><img src="../imagenes/ok.gif" width="16" height="16"></a></td> <?php												
													}
													else
													{ 
														?> <td>&nbsp;</td> <?php 
													} 
													?>
													<td>&nbsp;</td>
													<td>&nbsp;</td>
													<td>&nbsp;</td>
                                                                                                        <?php
                                                                                                        if( $_SESSION['planilla_vacaciones_abrir'])
													{
														?> <td><a href="javascript: enviar(100, <?php echo $fila['codnom']; ?>, 0, 0);" title="Recalcular Patronales <?php echo $termino; ?>"><img src="imagenes/txt.png" width="16" height="16"></a></td> <?php												
													}
													else
													{ 
														?> <td>&nbsp;</td> <?php 
													}
                                                                                                        ?>
												<?php
												}
												?>
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
$(document).ready(function(){ 

   	$('#table_datatable').DataTable({
   		"aaSorting":         [[0, 'desc']],
    	"iDisplayLength":    25,
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
                "sNext": 	 "P&aacute;gina Siguiente",
                "sPage": 	 "P&aacute;gina",
                "sPageOf":   "de",
            }
        },
        "aLengthMenu": [ 
            [5, 10, 25, 50,  -1],
            [5, 10, 25, 50, "Todos"]
        ],
        "aoColumnDefs": [
            { 'bSortable': false, 'bSearchable': false, 'aTargets': [7, 8, 9, 10, 11, 12] },
            { 'sWidth': '30%', 'aTargets': [2] },           
            { 'sClass': 'icono text-middle',         'aTargets': [7, 8, 9, 10, 11, 12] },
            { 'sClass': 'text-center text-middle',   'aTargets': [0, 1, 3, 4, 5, 6] },
            { 'sClass': 'ajustar-texto text-middle', 'aTargets': [2] },
        ],
		"fnDrawCallback": function() {
		    $('#table_datatable_filter input').attr("placeholder", "Escriba frase para buscar");
		},
		"fnInfoCallback": function (oSettings, iStart, iEnd, iMax, iTotal, sPre) {
			// "sInfo": "Total _TOTAL_ registros",
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