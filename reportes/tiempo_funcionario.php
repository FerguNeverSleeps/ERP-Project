<?php 
require_once '../generalp.config.inc.php';
session_start();
ob_start();

// error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
include("../nomina/lib/common.php");
date_default_timezone_set('America/Panama');
require_once('../nomina/lib/database.php');
//require ('numeros_letras_class.php');
$db = new Database($_SESSION['bd']);

//$sql = "SELECT * FROM departamento";
$sql = "SELECT b.apenom as nombre, c.descripcion as descripcion, SUM(a.tiempo) as tiempo,SUM(a.dias) as dias,SUM(a.horas) as horas,SUM(a.minutos) as minutos FROM dias_incapacidad as a,nompersonal as b,tipo_justificacion as c WHERE a.cod_user=b.ficha AND a.tipo_justificacion=c.idtipo GROUP BY b.apenom";
$res = $db->query($sql);

$z=0;
$nombre=array();$justificativo=array();$tiempo=array();$dias=array();$horas=array();$minutos=array();
while($fila = fetch_array($res)){
	array_push($nombre,$fila["nombre"]);
	array_push($justificativo,$fila["descripcion"]);
	array_push($tiempo,$fila["tiempo"]);
	array_push($dias,$fila["tiempo"]/8);
	array_push($horas,$fila["horas"]);
	array_push($minutos,$fila["minutos"]);
	$z++;
}
?>


<?php include("../includes/dependencias.php"); // <html><head></head><body> ?>
<meta charset="utf-8">
<link href="../includes/assets/css/custom-datatables.css" rel="stylesheet" type="text/css"/>

<!-- BEGIN PAGE CONTENT-->
<div class="row">
	<div class="col-md-12">
		<!-- BEGIN EXAMPLE TABLE PORTLET-->
		<div class="portlet box blue">
			<div class="portlet-title">
				<div class="caption">
					<!-- <i class="fa fa-globe"></i> -->
					<img src="../nomina/imagenes/21.png" width="22" height="22" class="icon"> Tiempos por Funcionarios
				</div>
				<div class="pull-right">
							<!-- Trigger the modal with a button -->
							<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal">
							<i class="fa fa-cloud-download"></i>
							Exportar
							</button>
		                </div>
			</div>
			<div class="portlet-body">
				<div class="table-toolbar" style="display: none">
					<div class="btn-group">&nbsp;
						<!--<button id="sample_editable_1_new" class="btn green">
						Add New <i class="fa fa-plus"></i>
						</button>-->
					</div>
				</div>
				<table class="table table-striped table-bordered table-hover" id="table_datatable">
				<thead>
				<tr>
					<th>Nombre</th>
					<th>Justificacion</th>
					<th>Tiempo</th>
					<th>Dias</th>
					<th>horas</th>
					<th>Minutos</th>
				</tr>
				</thead>
				<tbody>
						<?php for ($x=0;$x<$z;$x++){ ?>
						<tr class="odd gradeX">
							<td><?php echo $nombre[$x]; ?></td>
							<td><?php echo $justificativo[$x];?></td>
							<td><?php echo $tiempo[$x];?></td>
							<td><?php echo $dias[$x]; ?></td>
							<td><?php echo $horas[$x];?></td>
							<td><?php echo $minutos[$x];?></td>						
						</tr>
						<?php } ?>
				</tbody>
				</table>
			</div>
		</div>
		<!-- END EXAMPLE TABLE PORTLET-->
	</div>
</div>
<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      	<div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal">&times;</button>
	        <h4 class="modal-title">Opciones de Exportacion</h4>
	    </div>
      	<div class="modal-body">
	    	<a class="btn btn-primary" href="<?php echo $_SESSION['LIVEURL']; ?>/reportes/pdf/pdf_tiempo_funcionario.php">
			<i class="fa fa-file-pdf-o"></i>
			PDF
			</a>&nbsp;
			<a class="btn btn-primary" href="<?php echo $_SESSION['LIVEURL']; ?>/reportes/word/word_tiempo_funcionario.php">
			<i class="fa fa-file-word-o"></i>
			WORD
			</a>&nbsp;
			<a class="btn btn-primary" href="<?php echo $_SESSION['LIVEURL']; ?>/reportes/excel/excel_tiempo_funcionario.php">
			<i class="fa fa-file-excel-o"></i>
			EXCEL
			</a>
      	</div>
      	<div class="modal-footer">
        	<a class="btn btn-primary" data-dismiss="modal">
			<i class="fa fa-remove"></i>
			Cerrar
			</a>
      	</div>
    </div>

  </div>
</div>
<!-- END PAGE CONTENT-->

<?php //include("../nomina/footer4.php"); ?>

<script type="text/javascript">
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
                  "sSearch": "<img src='../includes/imagenes/icons/magnifier.png' width='16' height='16' > Buscar:",
                    "sLengthMenu": "Mostrar _MENU_",
                    //"sInfo": "Showing page _PAGE_ of _PAGES_", // Mostrando 1 to 5 de 18 entradas
                    //"sInfoEmpty": "No hay registros para mostrar",
                    "sInfoEmpty": "",
                    //"sInfo": "",
                    "sInfo":"Total _TOTAL_ registros",
                    "sInfoFiltered": "",
                  "sEmptyTable":  "No hay datos disponibles",//"No data available in table",
                    "sZeroRecords": "No se encontraron registros",//"No matching records found",
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
                    { 'bSortable': false, 'aTargets': [3] },
                    { "bSearchable": false, "aTargets": [ 3 ] },
                    { 'bSortable': false, 'aTargets': [2] },
                    { "bSearchable": false, "aTargets": [ 2 ] },
                    { "sWidth": "8%", "aTargets": [2] },
                    { "sWidth": "8%", "aTargets": [3] }
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
   });
</script>
</body>
</html>