<?php
session_start();
ob_start();

require_once('../lib/database.php');

$db = new Database($_SESSION['bd']);

$codnomm = isset($_GET['codigo_nomina']) ? $_GET['codigo_nomina'] : '';
$codtt   = isset($_GET['codt'])          ? $_GET['codt']          : '';

$codigo_nomina = isset($_SESSION['codigo_nomina']) ? $_SESSION['codigo_nomina'] : '';

$sql = "SELECT   ficha, cedula, apenom 
        FROM     nompersonal 
        WHERE    tipnom={$codigo_nomina} AND estado<>'Egresado' 
        ORDER BY ficha, cedula";
$res = $db->query($sql);
?>
<?php include("../header4.php"); ?>
<link href="../../includes/assets/css/custom-datatables.css" rel="stylesheet" type="text/css"/>
<style type="text/css">
.portlet > .portlet-title > .actions > .btn.btn-sm {
  margin-top: -9px !important;
}

table.table thead .sorting_asc {
    background-position: right 13px;    
}

table.table thead .sorting_desc {
    background-position: right 5px;
}

.cursor-pointer{
  cursor: pointer;
}
</style>
<script type="text/javascript">
function Aceptar(ficha, codnom, codts)
{
    document.location.href = "movimientos_nomina_liquidaciones.php?ficha="+ficha+"&codigo_nomina="+codnom+"&codt="+codts;
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
                Personal disponible seg&uacute;n tipo de n&oacute;mina
              </div>
              <div class="actions">
                <a class="btn btn-sm blue"  onclick="javascript: window.location='nomina_de_liquidaciones.php'">
                  <i class="fa fa-arrow-left"></i> Regresar
                </a>
              </div>
            </div>
            <div class="portlet-body" id="blockui_portlet_body">

              <form method="post" name="frmPrincipal" id="frmPrincipal">

                  <table class="table table-striped table-bordered table-hover" id="table_datatable">
                    <thead>
                      <tr>
                        <th>Ficha</th>
                        <th>C&eacute;dula</th>
                        <th>Apellido y Nombre</th>
                      </tr>
                    </thead>
                    <tbody>
                    <?php
                      while( $fila = $res->fetch_assoc() )
                      {
                        ?>
                        <tr class="odd gradeX cursor-pointer" onclick="Aceptar('<?php echo($fila['ficha']); ?>','<?php echo $codnomm; ?>','<?php echo $codtt; ?>');">
                          <td><?php echo $fila['ficha']; ?></td>
                          <td><?php echo $fila['cedula']; ?></td>
                          <td><?php echo $fila['apenom'];  ?></td> 
                        </tr>
                        <?php
                      }
                    ?>
                    </tbody>
                  </table>

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
        "iDisplayLength":    25,
        "sPaginationType":   "bootstrap_extended",
        "oLanguage": {
            "sSearch":       "<img src='../../includes/imagenes/icons/magnifier.png' width='16' height='16' > Buscar:",
            "sLengthMenu":   "Mostrar _MENU_",
            "sInfoEmpty":    "",
            "sInfo":         "Total _TOTAL_ registros",
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
        "fnDrawCallback": function() {
            $('#table_datatable_filter input').attr("placeholder", "Escriba frase para buscar");
        }
  });

  $('#table_datatable_wrapper .dataTables_filter input').addClass("form-control input-medium input-inline");
  $('#table_datatable_wrapper .dataTables_length select').addClass("form-control input-xsmall");

});
</script>
</body>
</html>
