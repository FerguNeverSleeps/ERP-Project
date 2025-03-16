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
$sql = "SELECT Descripcion,IdDepartamento FROM departamento WHERE Descripcion IS NOT NULL AND IdDepartamento IS NOT NULL GROUP BY Descripcion";
$res = $db->query($sql);

$sql2 = "SELECT a.Descripcion as Descripcion,count(b.IdDepartamento) as total FROM departamento as a,nompersonal as b WHERE a.IdDepartamento=b.IdDepartamento GROUP BY Descripcion";
$res2 = $db->query($sql2);

while($col=fetch_array($res2))
{
	$dep=$col['Descripcion'];
	$departamento[$dep]=$col['total'];
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
					<img src="../nomina/imagenes/21.png" width="22" height="22" class="icon"> Tiempos por departamentos
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
					<th>Departamento</th>
					<th>Total de Empleados</th>
					<th></th>
				</tr>
				</thead>
				<tbody>
				<?php
					while($fila=fetch_array($res))
					{ 
						$dep=$fila['Descripcion'];	
						$idd = $fila['IdDepartamento'];
						echo "
						<tr class='odd gradeX'>
							<td>".$dep."</td>
							<td>";if(isset($departamento[$dep])){echo $departamento[$dep];}else{echo "0";}echo "</td>			
							<td style='text-align: center'>"; 
								echo "<a href='tiempo_departamento_detalles.php?codigo=".$idd."'>
									<img src='../includes/imagenes/icons/clock.png' width='16' height='16'>
								</a>
							</td>							
						</tr>";								
					}
					?>
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
	    	<a class="btn btn-primary" href="<?php echo $_SESSION['LIVEURL']; ?>/reportes/pdf/pdf_tiempo_departamento.php">
			<i class="fa fa-file-pdf-o"></i>
			PDF
			</a>&nbsp;
			<a class="btn btn-primary" href="<?php echo $_SESSION['LIVEURL']; ?>/reportes/word/word_tiempo_departamento.php">
			<i class="fa fa-file-word-o"></i>
			WORD
			</a>&nbsp;
			<a class="btn btn-primary" href="<?php echo $_SESSION['LIVEURL']; ?>/reportes/excel/excel_tiempo_departamento.php">
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