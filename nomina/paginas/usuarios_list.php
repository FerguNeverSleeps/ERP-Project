<?php 
require_once '../../generalp.config.inc.php';
session_start();
ob_start();
// error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require_once '../lib/common.php';
$conexion=conexion();
$constantes = get_defined_constants();
if($constantes['USUARIO_EMPRESA'] == $_SESSION['usuario'] OR $constantes['USUARIO_EMPRESA2'] == $_SESSION['usuario'] OR $constantes['USUARIO_EMPRESA3'] == $_SESSION['usuario']){
	$sql = "SELECT * FROM " . SELECTRA_CONF_PYME . ".nomusuarios";
	$res = query($sql, $conexion);
}
else
{
	$sql = "SELECT * FROM " . $constantes['SELECTRA_CONF_PYME'] . ".nomusuarios nu
	INNER JOIN " . $constantes['SELECTRA_CONF_PYME'] . ".nomusuario_empresa nue ON (nue.id_usuario = nu.coduser) 
	INNER JOIN " . $constantes['SELECTRA_CONF_PYME'] . ".nomempresa ne  ON (nue.id_empresa= ne.codigo)
	WHERE nue.id_empresa = '".$_SESSION['codigo_nomempresa']."' and nu.login_usuario <> 'admin'
	GROUP BY nu.coduser";
	$res = query($sql, $conexion);
}
?>
<?php include("../header4.php"); // <html><head></head><body> ?>
<link href="../../includes/assets/css/custom-datatables.css" rel="stylesheet" type="text/css"/>
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
								<!-- <i class="fa fa-globe"></i> -->
								<img src="../imagenes/21.png" width="22" height="22" class="icon"> Usuarios
							</div>
							<div class="actions">
								<a class="btn btn-sm blue"  onclick="javascript: window.location='xls_listado_usuarios.php'">
									<i class="fa fa-download"></i><i class="far fa-file-excel"></i>
									<!-- <img src="../imagenes/add.gif" width="16" height="16"> --> Imprimir Excel
								</a>
								<a class="btn btn-sm blue"  onclick="javascript: window.location='usuarios_add.php?accion=Crear'">
									<i class="fa fa-plus"></i>
									<!-- <img src="../imagenes/add.gif" width="16" height="16"> --> Agregar
								</a>
							</div>
						</div>
						<div class="portlet-body">
							<table class="table table-striped table-bordered table-hover" id="table_datatable">
							<thead>
							<tr>
								<th>Usuario</th>
								<th>Nombre</th>
								<th>&nbsp;</th>
								<th>&nbsp;</th>
								<th>&nbsp;</th>
								<th>&nbsp;</th>
								<th>&nbsp;</th>
							</tr>
							</thead>
							<tbody>
							<?php
								while($fila=fetch_array($res))
								{ 
									$codigo=$fila["coduser"];								
								?>
									<tr class="odd gradeX">
										<td><?php echo $fila['login_usuario']; ?></td>
										<td><?php echo $fila['descrip']; ?></td>
										<td style="text-align: center">
											<a href="usuarios_edit.php?codigo=<?php echo $codigo; ?>&accion=Editar" title="Editar">
											<img src="../../includes/imagenes/icons/pencil.png" width="16" height="16">
											</a>
										</td>
										<td style="text-align: center">
											<a href="usuarios_delete.php?codigo=<?php echo $codigo; ?>" title="Eliminar">
												<img src="../../includes/imagenes/icons/delete.png" width="16" height="16">
											</a>		
										</td>
										<td style="text-align: center">
											<a href="usuarios_reiniciar.php?codigo=<?php echo $codigo; ?>&accion=reiniciar" title="Reiniciar permisos">
											<img src="../../includes/imagenes/icons/arrow_refresh_small.png" width="16" height="16">
											</a>
										</td>
										<td style="text-align: center">
											<a href="usuarios_clonar.php?codigo=<?php echo $codigo; ?>&accion=clonar" title="Clonar Usuario">
											<img src="../../includes/imagenes/icons/user_go.png" width="16" height="16">
											</a>
										</td>
										<td style="text-align: center">
											<div>
										<?php

					                    if($fila["estado"] == 1 )
					                      echo '<button class="btn btn-success btn-xs btnActivar" coduser ='.$fila["coduser"].' estado= "0">Activado</button>';
					                    else
					                      echo '<button class="btn btn-danger btn-xs btnActivar" coduser ='.$fila["coduser"].' estado= "1">Desactivado </button>';
						                 ?>
											</div></td>
									</tr>
								  <?php									
								}
							?>
							</tbody>
							</table>
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

/*=============================================
=         ACTIVAR USUARIO        =
=============================================*/
$(document).on("click",".btnActivar",function(){
	var coduser = $(this).attr("coduser");
	var estado = $(this).attr("estado");

	var datos = new FormData();
	datos.append("usuarioActivarDesactivar","yes");
	datos.append("coduser",coduser);
	datos.append("estado",estado);

	$.ajax({
		url : "ajax/usuarios.ajax.php",
		method: "POST",
		data : datos,
		cache: false,
		contentType : false,
		processData : false ,
		success: function(respuesta){
			let resp = JSON.parse(respuesta);
			if(resp.data == 1){
				alert(resp.mensaje);
				window.location = "usuarios_list.php";

			}
			else
			{
				alert(resp.mensaje);
			}

		}


	});


	if(estado == 0 ){
		$(this).removeClass('btn-danger');
		$(this).addClass('btn-success');

		$(this).html('Activado');
		$(this).attr('estado' , 1 );
		$(this).attr('coduser' , coduser );

	}else {
		$(this).removeClass('btn-success');
		$(this).addClass('btn-danger');

		$(this).html('Desactivado');
		$(this).attr('estado' , 0 );
		$(this).attr('coduser' , coduser );


	}



});

	 $(document).ready(function() { 
	 	//$('#table_datatable').DataTable(); 

            // begin first table
            $('#table_datatable').DataTable({
            	//"oSearch": {"sSearch": "Escriba frase para buscar"},
            	"iDisplayLength": 10,
                //"sPaginationType": "bootstrap",
            	"sPaginationType": "bootstrap_extended", 
            	//"sPaginationType": "full_numbers",
                "oLanguage": {
                	"sSearch": "<img src='../../includes/imagenes/icons/magnifier.png' width='16' height='16' > Buscar:",
                    "sLengthMenu": "Mostrar _MENU_",
                    //"sInfo": "Showing page _PAGE_ of _PAGES_", // Mostrando 1 to 5 de 18 entradas
                    //"sInfoEmpty": "No hay registros para mostrar",
                    "sInfoEmpty": "",
                    //"sInfo": "",
                    "sInfo":"Total _TOTAL_ registros",
                    "sInfoFiltered": "",
          		    "sEmptyTable":  "No hay datos disponibles",//"No data available in table",
                    "sZeroRecords": "No se encontraron registros",//"No matching records found",
                    /*"oPaginate": {
                        "sPrevious": "Página Anterior",
                        "sNext": "Página Siguiente"
                    }*/
                    "oPaginate": {
                        "sPrevious": "P&aacute;gina Anterior",//"Prev",
                        "sNext": "P&aacute;gina Siguiente",//"Next",
                        "sPage": "P&aacute;gina",//"Page",
                        "sPageOf": "de",//"of"
                    }
                },
                /*
                "aLengthMenu": [
                    [5, 15, 20, -1],
                    [5, 15, 20, "Todos"] // change per page values here
                ],
                */
                "aLengthMenu": [ // set available records per page
                    [5, 10, 25, 50,  -1],
                    [5, 10, 25, 50, "Todos"]
                ],                
                "aoColumnDefs": [
                    { 'bSortable': false, 'aTargets': [3] },
                    { "bSearchable": false, "aTargets": [ 3 ] },
                    { 'bSortable': false, 'aTargets': [2] },
                    { "bSearchable": false, "aTargets": [ 2 ] },
                    { "sWidth": "8%", "aTargets": [2] },
                    { "sWidth": "8%", "aTargets": [3] },
                    { "sWidth": "8%", "aTargets": [6] }
                ],
				 "fnDrawCallback": function() {
				        $('#table_datatable_filter input').attr("placeholder", "Escriba frase para buscar");
				 }
                /*
	            "aoColumns": [
			      null,
			      null,
			      { "sWidth": "10px" },
			      { "sWidth": "10px" },
				]*/
            	/*
                "aoColumns": [
                  { "bSortable": false },
                  null,
                  { "bSortable": false, "sType": "text" },
                  null,
                  { "bSortable": false },
                  { "bSortable": false }
                ],*/
            });

            $('#table_datatable').on('change', 'tbody tr .checkboxes', function(){
                 $(this).parents('tr').toggleClass("active");
            });

            $('#table_datatable_wrapper .dataTables_filter input').addClass("form-control input-medium input-inline"); 
            $('#table_datatable_wrapper .dataTables_length select').addClass("form-control input-xsmall"); 
	 });
</script>

</body>
</html>
