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

?>


<?php include("../includes/dependencias.php"); // <html><head></head><body> ?>
<meta charset="utf-8">
<link href="../includes/assets/css/custom-datatables.css" rel="stylesheet" type="text/css"/>
<script src="../nomina/libs/js/jquery-1.42.2.min.js"></script>
<link rel="stylesheet" href="../includes/chosen/chosen.css" type="text/css" /> 
    <script src="../includes/chosen/chosen.jquery.js"></script>

<!-- BEGIN PAGE CONTENT-->
<div class="row">
	<div class="col-md-12">
		<!-- BEGIN EXAMPLE TABLE PORTLET-->
		<div class="portlet box blue">
			<div class="portlet-title">
				<div class="caption">
					<!-- <i class="fa fa-globe"></i> -->
					<img src="../nomina/imagenes/21.png" width="22" height="22" class="icon"> Tiempos de funcionarios por tipo
				</div>
			</div>
			<div class="portlet-body">
			<form action="tiempo_funcionario_tipo_detalles.php" method="POST">
				<div class="form-group">
						<script>
        					$(document).ready(function(){
            					$(".planilla").chosen({disable_search_threshold: 5});
       						});
    					</script>
	  					<label for="pwd">Tipo:</label>
	  					<select class="planilla form-control" id="planilla" name="justificacion" data-placeholder="Seleccione la planilla" required>
	  						<option id="planilla" required selected>Seleccione el justificativo</option>
	  						<?php 
	  							require_once('../nomina/lib/database.php');
	  							$cons1 = "SELECT DISTINCT b.descripcion as descripcion, a.tipo_justificacion as tipojustificacion FROM dias_incapacidad as a, tipo_justificacion as b WHERE a.tipo_justificacion=b.idtipo";
	  							$res = $db->query($cons1);
	  							while($col=fetch_array($res)){
	  								$pl = $col["descripcion"];
	  								echo "<option value=".$col["tipojustificacion"].">$pl</option>";
	  							}
	  							mysqli_free_result($col);
	  							mysqli_close($res);
	  						?>
	  					</select>
					</div>
					<div class="form-group">
						<script>
        					$(document).ready(function(){
            					$(".planilla").chosen({disable_search_threshold: 5});
       						});
    					</script>
	  					<label for="pwd">Departamento:</label>
	  					<select class="planilla form-control" id="planilla" name="departamento" data-placeholder="Seleccione la planilla" required>
	  						<option id="planilla" required selected>Seleccione el departamento</option>
	  						<?php 
	  							require_once('../nomina/lib/database.php');
	  							$cons1 = "SELECT IdDepartamento,Descripcion FROM departamento";
	  							$res = $db->query($cons1);
	  							while($col=fetch_array($res)){
	  								$pl = $col["Descripcion"];
	  								echo "<option value=".$col["IdDepartamento"].">$pl</option>";
	  							}
	  							mysqli_free_result($col);
	  							mysqli_close($res);
	  						?>
	  					</select>
					</div>
				<input type="submit" name="" class="btn btn-primary">
				</form>
			</div>
		</div>
		<!-- END EXAMPLE TABLE PORTLET-->
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