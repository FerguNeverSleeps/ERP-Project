
<?php 
require_once('../lib/database.php');

$db = new Database($_SESSION['bd']);

$registro_id=isset($_POST['registro_id']) ? $_POST['registro_id'] : '';	
$op=isset($_POST['op']) ? $_POST['op'] : '';

if ($op==3) //Se presiono el boton de Eliminar
{	
	$sql="DELETE FROM nompersonal WHERE personal_id='{$registro_id}'";	
		
	$res=$db->query($sql);	
	echo "<script> window.location.href='maestro_personal.php';</script>"; 
}  

$res1 = $db->query("SELECT tipo_empresa FROM nomempresa")->fetch_array();
$tipo_empresa = $res1['tipo_empresa']; 

$sql = "SELECT acceso_sueldo FROM ".SELECTRA_CONF_PYME.".nomusuarios WHERE login_usuario='".$_SESSION['usuario']."'";
$res = $db->query($sql);
$usuario = $res->fetch_object();

$user=$_SESSION["nombre"];

$res2 = $db->query("SELECT coduser FROM " . SELECTRA_CONF_PYME .".nomusuarios"." where descrip='$user'")->fetch_array();
$cod = $res2['coduser'];
//echo $sql="SELECT * FROM " . SELECTRA_CONF_PYME .".usuario_permisos"." where id_usuario='$cod' AND id_permiso='532'";exit;
$res3 = $db->query("SELECT * FROM " . SELECTRA_CONF_PYME .".usuario_permisos"." where id_usuario='$cod' AND id_permiso='532'")->fetch_array();
$verificando_permido=$res3['id_usuario'];
//  $sql = "SELECT coduser FROM " . SELECTRA_CONF_PYME .".nomusuarios"." where descrip='$user'";
//  $res=$db->query($sql);
//  echo $res;exit;
?>
<?php include("../header4.php"); ?>
<link href="../../includes/assets/css/custom-datatables.css" rel="stylesheet" type="text/css"/>
<style type="text/css">
/*.portlet > .portlet-title > .actions > .btn.btn-sm {
	margin-top: -9px !important;
}
*/
.portlet > .portlet-title > .actions > .btn {
    padding: 4px 10px !important;
    margin-top: -14px !important;
}
#table_datatable tbody tr td{
	vertical-align: middle;
}

#table_datatable_length .form-control {
    padding: 0px;
}

div.dataTables_filter label {
    float: left;
}

#table_datatable_wrapper .dataTables_filter input {
   margin-top: 5px;
   margin-bottom: 5px;
}

#table_datatable_wrapper div.dataTables_length label {
	margin-top: 5px;
}

@media (min-width: 513px) {
	#search_situ.input-small {
	    width: 122px !important;
	}
}

</style>
<script type="text/javascript">
function enviar(op, id){

	if (op==3){		// Opcion de Eliminar
		if (confirm("\u00BFEst\u00E1 seguro que desea eliminar el registro?"))
		{
			document.frmPrincipal.op.value=op;			
			document.frmPrincipal.registro_id.value=id;
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
								Personal
							</div>
							<div class="actions">	
                                                            <?php if ($_SESSION['acceso_editar'] == 1){?>
								<a class="btn btn-sm blue"  onclick="javascript: window.location='ag_integrantes.php'">
									<i class="fa fa-plus"></i>
									Agregar
								</a>
                                                            <?php } ?>
							</div>
						</div>
						<div class="portlet-body">
                                                    <div class="tabbable tabbable-custom tabbable-reversed"> <!-- boxless tabbable-reversed  -->
                                                        <ul class="nav nav-tabs">
                                                                <li class="active"><a href="#tab_0" id="tab-personal" data-toggle="tab">Activos</a></li>                                                                                                                                                                                          
                                                                <?php if (!empty($verificando_permido)){?>
                                                                    <li><a href="#tab_1" id="tab-egresados" data-toggle="tab">Egresados</a></li>                                                           
                                                                <?php } ?>
                                                        </ul>
                                                        <div class="tab-content">
                                                            <div class="tab-pane active" id="tab_0">
                                                                <div id="div_search_situ" style="display: inline;">
                                                                        <select id="search_situ" class="form-control input-inline input-small">
                                                                                <option value="Todos">Buscar por Situación</option>
                                                                                <?php 
                                                                                        $sql1 = "SELECT * FROM nomsituaciones";
                                                                                        $res1 = $db->query($sql1);

                                                                                        while($fila = $res1->fetch_array())
                                                                                        { ?>
                                                                                                <option value="<?php echo $fila['situacion']; ?>"><?php echo $fila['situacion']; ?></option>
                                                                                          <?php
                                                                                        }
                                                                                ?>										
                                                                        </select>
                                                                </div>
                                                                <!-- <div class="table-container"> -->
                                                                <form action="" method="post" name="frmPrincipal" id="frmPrincipal">
                                                                <table class="table table-striped table-bordered table-hover" id="table_datatable">
                                                                <thead>
                                                                <tr>
                                                                        <th>Foto</th>
                                                                        <th># Colab.</th>
                                                                        <th>C&eacute;dula</th>
                                                                        <th>Apellidos y nombres</th>
                                                                        <th>Situaci&oacute;n</th>
                                                                        <?php if($usuario->acceso_sueldo==1){?><th>Salario</th><?php } ?>                                                                        		
                                                                        <th>Fecha Ingreso</th>
                                                                        <th>&nbsp;</th>
                                                                        <th>&nbsp;</th>
                                                                        <th>&nbsp;</th>
                                                                        <th>&nbsp;</th>
                                                                        <th>&nbsp;</th>
                                                                        <th>&nbsp;</th>
                                                                        <th>&nbsp;</th>
                                                                        <th>&nbsp;</th>
                                                                               
                                                                </tr>
                                                                </thead>
                                                                <tbody>

                                                                </tbody>
                                                                </table>
                                                                    <input name="registro_id" type="hidden" value="">
                                                                    <input name="op" type="hidden" value="">	
                                                                </form>
                                                            </div>
                                                            <div class="tab-pane" id="tab_1">
                                                                <div id="div_search_situ" style="display: inline;">
                                                                        <select id="search_situ" class="form-control input-inline input-small">
                                                                                <option value="Todos">Buscar por Situación</option>
                                                                                <?php 
                                                                                        $sql1 = "SELECT * FROM nomsituaciones";
                                                                                        $res1 = $db->query($sql1);

                                                                                        while($fila = $res1->fetch_array())
                                                                                        { ?>
                                                                                                <option value="<?php echo $fila['situacion']; ?>"><?php echo $fila['situacion']; ?></option>
                                                                                          <?php
                                                                                        }
                                                                                ?>										
                                                                        </select>
                                                                </div>
                                                                <!-- <div class="table-container"> -->
                                                                <form action="" method="post" name="frmPrincipal" id="frmPrincipal">
                                                                <table class="table table-striped table-bordered table-hover" id="table_datatable_egresados">
                                                                <thead>
                                                                <tr>
                                                                        <th>Foto</th>
                                                                        <th># Colab.</th>
                                                                        <th>C&eacute;dula</th>
                                                                        <th>Apellidos y nombres</th>
                                                                        <th>Situaci&oacute;n</th>
                                                                        <?php if($usuario->acceso_sueldo==1){?><th>Salario</th><?php } ?>
                                                                        <th>Fecha Egreso</th>
                                                                        <th>Fecha Sus.</th>
                                                                        <th>&nbsp;</th>
                                                                        <th>&nbsp;</th>
                                                                        <th>&nbsp;</th>
                                                                        <th>&nbsp;</th>
                                                                        <th>&nbsp;</th>
                                                                        <th>&nbsp;</th>
                                                                        <th>&nbsp;</th>

                                                                </tr>
                                                                </thead>
                                                                <tbody>

                                                                </tbody>
                                                                </table>
                                                                    <input name="registro_id" type="hidden" value="">
                                                                    <input name="op" type="hidden" value="">	
                                                                </form>
                                                            </div>
                                                        </div>
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
	<div class="modal fade" id="modal-reporte">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title">Selecione Formato De Reporte</h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
							<a id="reportepdf" href="" class="btn btn-default btn-block">Pdf</a>
						</div>
						<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
							<a  id="reporteexcel" href="" class="btn btn-default btn-block">Excel</a>
						</div>
						<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
							<a  id="reportepdf2" href="" class="btn btn-default btn-block">Pdf 2</a>

						</div>
					</div>

				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
				</div>
			</div>
		</div>
	</div>
</div>
<?php include("../footer4.php"); ?>
<script type="text/javascript">
<?php
$data = "{ 'bSortable':   false, 'aTargets': [0, 7, 8, 9, 10, 11, 12, 13, 14] }, " .
        "{ 'bSearchable': false, 'aTargets': [0, 7, 8, 9, 10, 11, 12, 13, 14] }";

if($tipo_empresa=='1')
{
	$data = "{ 'bSortable':   false, 'aTargets': [0, 8, 9, 10, 11, 12, 13, 14] }, " .
	        "{ 'bSearchable': false, 'aTargets': [0, 8, 9, 10, 11, 12, 13, 14] }";
}

if($usuario->acceso_sueldo==0)
{
	$data = "{ 'bSortable':   false, 'aTargets': [0, 6, 7, 8, 9, 10, 11, 12] }, " .
	        "{ 'bSearchable': false, 'aTargets': [0, 6, 7, 8, 9, 10, 11, 12] }";
}
?>

var dataColumnDefs = [<?php echo $data; ?>];

$(document).ready(function() { 

 	var oTable = $('#table_datatable').DataTable({
		        "bProcessing": true,
		        "bServerSide": true,
                "bStateSave" : true,
                "sAjaxSource": "ajax/server_processing_listado_integrantes.php", 
                "sDom": "<'row'<'col-md-3 col-sm-12'l><'col-md-9 col-sm-12'f>r><'table-scrollable't><'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>",
                "iDisplayLength": 25,
            	"sPaginationType": "bootstrap_extended",
            	"aaSorting": [[ 1, "asc" ]], // Ordenar por columna 1 (Ficha) 
                "oLanguage": {
                	"sProcessing": "Cargando...",
                	"sSearch": "",
                    "sLengthMenu": "Mostrar _MENU_",
                    "sInfoEmpty": "",
                    "sInfo":"Total _TOTAL_ registros",
                    "sInfoFiltered": "",
          		    "sEmptyTable":  "No hay datos disponibles", 
                    "sZeroRecords": "No se encontraron registros",
                    "oPaginate": {
                        "sPrevious": "P&aacute;gina Anterior",
                        "sNext": "P&aacute;gina Siguiente",
                        "sPage": "P&aacute;gina",
                        "sPageOf": "de",
                    }
                },
                "aLengthMenu": [ 
                    [5, 10, 25, 50,  -1],
                    [5, 10, 25, 50, "Todos"]
                ],                
                "aoColumnDefs": dataColumnDefs,
				"fnDrawCallback": function() {
				        $('#table_datatable_filter input').attr("placeholder", "Escriba frase para buscar");
				    createEvents();

				},
				initComplete: function(){
				    createEvents();
				}
            });
            
            var oTable = $('#table_datatable_egresados').DataTable({
		        "bProcessing": true,
		        "bServerSide": true,
				"sAjaxSource": "ajax/server_processing_listado_egresados.php", 
 				"sDom": "<'row'<'col-md-3 col-sm-12'l><'col-md-9 col-sm-12'f>r><'table-scrollable't><'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>",
            	"iDisplayLength": 25,
            	"sPaginationType": "bootstrap_extended",
            	"aaSorting": [[ 1, "asc" ]], // Ordenar por columna 1 (Ficha) 
                "oLanguage": {
                	"sProcessing": "Cargando...",
                	"sSearch": "",
                    "sLengthMenu": "Mostrar _MENU_",
                    "sInfoEmpty": "",
                    "sInfo":"Total _TOTAL_ registros",
                    "sInfoFiltered": "",
          		    "sEmptyTable":  "No hay datos disponibles", 
                    "sZeroRecords": "No se encontraron registros",
                    "oPaginate": {
                        "sPrevious": "P&aacute;gina Anterior",
                        "sNext": "P&aacute;gina Siguiente",
                        "sPage": "P&aacute;gina",
                        "sPageOf": "de",
                    }
                },
                "aLengthMenu": [ 
                    [5, 10, 25, 50,  -1],
                    [5, 10, 25, 50, "Todos"]
                ],                
                "aoColumnDefs": dataColumnDefs,
				"fnDrawCallback": function() {
				        $('#table_datatable_filter input').attr("placeholder", "Escriba frase para buscar");
				    createEvents();

				},
				initComplete: function(){
				    createEvents();
				}
            });

            $('#table_datatable_wrapper .dataTables_filter input').addClass("form-control input-medium input-inline"); 
            $('#table_datatable_wrapper .dataTables_length select').addClass("form-control input-small"); 
            $('#table_datatable_wrapper .dataTables_length select').select2({
                showSearchInput : false //hide search box with special css class
            }); // initialize select2 dropdown

            $('#div_search_situ').insertBefore("#table_datatable_wrapper .dataTables_filter input");

            $('#table_datatable_wrapper .dataTables_filter input').after(' <a class="btn blue" id="btn-search"><i class="fa fa-search"></i> Buscar</a> ');

            
		    $("#btn-search").click( function()
		    {
		    	 var valor_buscar =$('#search_situ').val();

		    	 if( valor_buscar == 'Todos' )
		    	 {
		    	 	valor_buscar = '';
		    	 }

		    	 // Se filtra por la columna 4 - Situación
		    	 oTable.fnFilter( valor_buscar, 4 );
		    });

		    function createEvents() {
		    	$('.modalreporte').off().on('click', function(){

		    		var url = $(this).attr('rel');
		    		$('#reportepdf').attr('href', url);
		    		var url = $(this).attr('rel2');
		    		$('#reportepdf2').attr('href', url);
		    		var url = $(this).attr('name');
		    		$('#reporteexcel').attr('href', url);
		    	    $("#modal-reporte").modal('show');

		    	});
		    }

});
</script>
<!-- manuel -->
<script type="text/javascript" src="../../reporte_pub/js/jquery.fancybox.pack.js"></script>
<link rel="stylesheet" type="text/css" href="../../reporte_pub/css/jquery.fancybox.css" media="screen" />
<script>
    $(document).ready(function() {
        $('.fancybox').fancybox( {topRatio:0,width:1000} );
    });
</script>
</body>
</html>
