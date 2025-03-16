<?php
include_once('clases/database.class.php');
include('obj_conexion.php');
//DECLARACION DE LIBRERIAS
require_once '../lib/common.php';
require_once '../paginas/func_bd.php';
include ("../paginas/funciones_nomina.php");
?>

<?php include("../header4.php"); 
$expediente=$_GET['codigo'];
$cedula = $_GET['cedula'];
$sqlExp = "SELECT * FROM  expediente_adjunto WHERE cod_expediente_det=".$expediente;
$rows = $db->query($sqlExp);
$resp = $db->fetch_all_array($sqlExp);


$consultap="SELECT * FROM nompersonal WHERE cedula='$cedula'";

$resultadop=$db->query($consultap);
$fetchCon=$db->fetch_array($resultadop);
$nombre=$fetchCon['apenom'];
 
// <html><head></head><body> ?>
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
								<img src="../imagenes/21.png" width="22" height="22" class="icon"> Expediente N°: <?php echo $expediente; echo " - Apellidos/Nombres: "; echo $nombre; echo " - Cédula: "; echo $cedula; ?>
							</div>
							<div class="actions">
								<a class="btn btn-sm blue"  onclick="javascript: window.location='expediente_agregar_adjunto.php?codigo=<?=$expediente?>&cedula=<?=$cedula?>'">
									<i class="fa fa-plus"></i>
									<!-- <img src="../imagenes/add.gif" width="16" height="16"> --> Agregar
								</a>
								<a class="btn btn-sm blue"  onclick="javascript: window.location='expediente_list.php?cedula=<?=$cedula?>'">
									<!-- <img src="../imagenes/atras.gif" width="16" height="16"> -->
									<i class="fa fa-arrow-left"></i> Regresar
								</a>								
							</div>
						</div>
						<div class="portlet-body">
							<div class="table-toolbar" style="display: none">
								<div class="btn-group">&nbsp;
									
								</div>
								<div class="btn-group pull-right">
									<button class="btn btn-sm blue" onclick="javascript: window.location='usuarios_add.php'">
									<i class="fa fa-plus"></i>
									<!-- <img src="../../includes/imagenes/icons/add.png"  width="16" height="16">--> Agregar
									</button>									
								</div>
							</div>
							<table class="table table-striped table-bordered table-hover" id="table_datatable_expediente_adjunto">
                                                            <thead>
                                                                <tr>
                                                                        <th>Archivo</th>
                                                                        <th>Nombre</th>
                                                                        <th>Fecha</th>
                                                                        <th>Descripci&oacute;n</th>
                                                                        <th>Principal</th>
                                                                        <th></th>
                                                                        <th></th>
                                                                        <th></th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <? for($i=0;$i<count($resp);$i++){ ?>
                                                                <tr class="odd gradeX">
                                                                    <td><?=$resp[$i]['archivo'] ?></td>
                                                                    <td><?=$resp[$i]['nombre_adjunto'] ?></td>
                                                                    <td><?=$resp[$i]['fecha'] ?></td>
                                                                    <td><?=$resp[$i]['descripcion'] ?></td>
                                                                    <td>
                                                                        <?
                                                                            if($resp[$i]['principal'] == 0)
                                                                            {
                                                                               echo "NO";
                                                                            }
                                                                            else
                                                                            {
                                                                                echo "SI";
                                                                            }
                                                                            ?>
                                                                    </td>
                                                                    <td><a data-target="#modal<?=$i?>" data-toggle="modal">
                                                                        <img title="Ver documento" src="../imagenes/documento-icono.png" width="22" height="22" border="0"></a></td>
                                                                    <td style="text-align: center">
                                                                        <a href="expediente_agregar_adjunto.php?codigo=<?php echo $resp[$i]['cod_expediente_det']; ?>&cedula=<?php echo $_GET[cedula]; ?>&adjunto=<?php echo $resp[$i]['id_adjunto']; ?>" title="Editar">
                                                                            <img src="../imagenes/editar-icono.png" width="22" height="22">
                                                                        </a>
                                                                    </td>
                                                                    <? $adjunto = $resp[$i]['id_adjunto'];
                                                                       icono("javascript:eliminar_adjunto('$adjunto')", "Eliminar", "eliminar-icono.png");?>
                                                                        <div id="modal<?=$i?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal" aria-hidden="true">
                                                                            <div class="modal-dialog">
                                                                                <div class="modal-content">
                                                                                    <div class="modal-body">
                                                                                        <embed src="navegador_archivos/archivos/<?=$cedula ?>/<?=$resp[$i]['archivo'] ?>" width="550" height="300">
                                                                                    </div>
                                                                                    <div class="modal-footer">
                                                                                        <button class="btn btn-default" type="button" data-dismiss="modal">Cerrar</button>                                                                                                      
                                                                                    </div>
                                                                                </div>
                                                                             </div>
                                                                        </div>
                                                                </tr>
                                                                <? } ?>
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
	 $(document).ready(function() { 
	 	//$('#table_datatable').DataTable(); 

            // begin first table
            $('#table_datatable_expediente_adjunto').DataTable({
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
                        "sPageOf": "de"//"of"
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
                    { 'aTargets': [0] },
                    { "aTargets": [1] },
                    { 'aTargets': [2] },
                    { "bSearchable": false, "aTargets": [5] },
                     { "bSearchable": false, "aTargets": [6] },
                     { "bSearchable": false, "aTargets": [7] },
                    { 'bSortable': false,"sWidth": "5%", "aTargets": [5] },
                    { 'bSortable': false,"sWidth": "5%", "aTargets": [6] },
                    { 'bSortable': false,"sWidth": "5%", "aTargets": [7] }          
                ],
		"fnDrawCallback": function() {
                    $('#table_datatable_expediente_adjunto_filter input').attr("placeholder", "Escriba frase para buscar");
                }
            });

            $('#table_datatable_expediente_adjunto').on('change', 'tbody tr .checkboxes', function(){
                 $(this).parents('tr').toggleClass("active");
            });

            $('#table_datatable_expediente_adjunto_wrapper .dataTables_filter input').addClass("form-control input-medium input-inline"); 
            $('#table_datatable_expediente_adjunto_wrapper .dataTables_length select').addClass("form-control input-xsmall");
	 });
         
         function eliminar_adjunto(codigo)
        {
//                alert(codigo);
                if (confirm("\u00BFSeguro desea eliminar este archivo?") == true)
                        window.location.href="expediente_adjunto_eliminar.php?cod_eliminar="+codigo;
        }
        
</script>
</body>
</html>