<?php 
require_once '../generalp.config.inc.php';
session_start();
ob_start();
$codigo = $_GET["codigo"];
// error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
include("../nomina/lib/common.php");
date_default_timezone_set('America/Panama');
require_once('../nomina/lib/database.php');
//require ('numeros_letras_class.php');
$db = new Database($_SESSION['bd']);

$sql = "SELECT DISTINCT personal_id,apenom FROM nompersonal WHERE IdDepartamento='$codigo'";
//$sql = "SELECT personal_id,apenom FROM nompersonal WHERE IdDepartamento='$codigo'";
$res = $db->query($sql);

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
					<img src="../nomina/imagenes/21.png" width="22" height="22" class="icon"> Tiempos por Departamento
				</div>
        <div class="pull-right">
          <!-- Trigger the modal with a button -->
          <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal">
            <i class="fa fa-cloud-download"></i>
            Exportar
          </button>
        </div>
				<div class="pull-right">
            <a class="btn btn-primary" href="tiempo_departamento.php">
            <i class="fa fa-arrow-left"></i>
            Regresar
            </a>&nbsp;
						<!-- Trigger the modal with a button -->
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
				<table class="table table-striped table-bordered table-hover" id="table">
        <thead>
          <tr>
             <th>Posicion</th>
             <th>Nombre del Empleado</th>
          </tr>
        </thead>
        <tbody>
        <?php
          while($fila=fetch_array($res)){ 	
          ?>
            <tr class="odd gradeX">
              <td><?php echo $fila['personal_id']?></td>
              <td><?php echo $fila['apenom']?></td>														
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
<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Opciones de Exportacion</h4>
      </div>
        <div class="modal-body">
        <a class="btn btn-primary" href="<?php echo $_SESSION['LIVEURL']; ?>/reportes/pdf/pdf_tiempo_departamento_detalles.php?codigo=<?php echo $codigo; ?>">
      <i class="fa fa-file-pdf-o"></i>
      PDF
      </a>&nbsp;
      <a class="btn btn-primary" href="<?php echo $_SESSION['LIVEURL']; ?>/reportes/word/word_tiempo_departamento_detalles.php?codigo=<?php echo $codigo; ?>">
      <i class="fa fa-file-word-o"></i>
      WORD
      </a>&nbsp;
      <a class="btn btn-primary" href="<?php echo $_SESSION['LIVEURL']; ?>/reportes/excel/excel_tiempo_departamento_detalles.php?codigo=<?php echo $codigo; ?>">
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

<script type="text/javascript">
   $(document).ready(function() { 
    //$('#table_datatable').DataTable();
            // begin first table
            $('#datatable').DataTable({
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
                $('#datatable_filter input').attr("placeholder", "Escriba frase para buscar");
         }
            });

            $('#datatable').on('change', 'tbody tr .checkboxes', function(){
                 $(this).parents('tr').toggleClass("active");
            });

            $('#datatable_wrapper .dataTables_filter input').addClass("form-control input-medium input-inline"); 
            $('#datatable_wrapper .dataTables_length select').addClass("form-control input-xsmall"); 
   });
</script>
</body>
</html>