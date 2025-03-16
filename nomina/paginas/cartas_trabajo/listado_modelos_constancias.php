<?php 
require_once('../../lib/database.php');

$db = new Database($_SESSION['bd']);

$codigo = isset($_POST['codigo']) ? $_POST['codigo'] : ''; 
$opcion = isset($_POST['opcion']) ? $_POST['opcion'] : '';

if ($opcion==3) //Se presiono el boton de Eliminar
{ 
  $sql = "DELETE FROM nomtipos_constancia WHERE codigo={$codigo}";  
  $res = $db->query($sql);   

  echo "<script> window.location.href='listado_modelos_constancias.php';</script>";    
}
  
$sql = "SELECT codigo, nombre, tipo_documento FROM nomtipos_constancia ORDER BY codigo";
$res = $db->query($sql);         
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="es" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="es" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="es" class="no-js">
<!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
<meta charset="utf-8"/>
<title>Modelos de Constancias</title>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<meta content="" name="description"/>
<meta content="" name="author"/>
<!-- BEGIN GLOBAL MANDATORY STYLES -->
<link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css"/>
<link href="../../../includes/assets/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
<link href="../../../includes/assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
<link href="../../../includes/assets/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css"/>
<!-- END GLOBAL MANDATORY STYLES -->
<!-- BEGIN PAGE LEVEL STYLES -->
<link rel="stylesheet" type="text/css" href="../../../includes/assets/plugins/select2/select2.css"/>
<link rel="stylesheet" type="text/css" href="../../../includes/assets/plugins/select2/select2-metronic.css"/>
<link rel="stylesheet" href="../../../includes/assets/plugins/data-tables/DT_bootstrap.css"/>
<!-- BEGIN THEME STYLES -->
<link href="../../../includes/assets/css/style-metronic.css" rel="stylesheet" type="text/css"/>
<link href="../../../includes/assets/css/style.css" rel="stylesheet" type="text/css"/>
<link href="../../../includes/assets/css/style-responsive.css" rel="stylesheet" type="text/css"/>
<link href="../../../includes/assets/css/plugins.css" rel="stylesheet" type="text/css"/>
<link href="../../../includes/assets/css/themes/default.css" rel="stylesheet" type="text/css" id="style_color"/>
<link href="../../../includes/assets/css/custom.css" rel="stylesheet" type="text/css"/>
<!-- END THEME STYLES -->
<link href="../../../includes/assets/css/custom-datatables.css" rel="stylesheet" type="text/css"/>
<style>
body { /* En uso */
    background-color: #ffffff !important;
}

.page-content-wrapper { /* En uso */
  background-color: white !important; 
}

.page-sidebar-closed .page-content { /* En uso */
  margin-left: 0px !important;
} 

.portlet > .portlet-title > .caption { /* En uso */
  font-family: helvetica, arial, verdana, sans-serif;
  font-size: 13px;
  font-weight: bold;
  line-height: 21px;
  margin-bottom: 5px;
}
</style>
<script type="text/javascript">
function enviar(opcion, id)
{  
  if(opcion=='2')
  {
    document.frmPrincipal.codigo.value=id;  
    document.frmPrincipal.action="editar_modelo_constancia.php";
    document.frmPrincipal.submit();   
  } 
  if (opcion=='3')
  {   // Opcion de Eliminar
    if (confirm("\u00BFEst\u00E1 seguro que desea eliminar el registro?"))
    {         
      document.frmPrincipal.codigo.value=id;
      document.frmPrincipal.opcion.value=opcion;
      document.frmPrincipal.submit();
    }   
  }
}
</script>
</head>
<!-- END HEAD -->
<!-- BEGIN BODY -->
<body class="page-full-width" marginheight="0">
<div class="page-container">
  <!-- BEGIN CONTENT -->
  <div class="page-content-wrapper">
    <div class="page-content">
      <!-- BEGIN PAGE HEADER-->
      <div class="row">
        <div class="col-md-12">
          <!-- BEGIN PAGE TITLE & BREADCRUMB-->
          <h3 class="page-title">Constancias</h3>
          <ul class="page-breadcrumb breadcrumb">
            <li><i class="glyphicon glyphicon-wrench"></i>
              <a style="text-decoration: none;">Configuraci&oacute;n</a><i class="fa fa-angle-right"></i>
            </li>
            <li><a href="../submenu_constancias.php">Constancias</a></li>
          </ul>
          <!-- END PAGE TITLE & BREADCRUMB-->
        </div>
      </div>
      <!-- END PAGE HEADER-->
      <!-- BEGIN PAGE CONTENT-->
      <div class="row">
        <div class="col-md-12">
          <!-- BEGIN EXAMPLE TABLE PORTLET-->
          <div class="portlet box blue">
            <div class="portlet-title">
              <div class="caption">
               <i class="fa fa-bars"></i> Modelos de constancias
              </div>
              <div class="actions">
                <a class="btn btn-sm blue active"  onclick="javascript: window.location='agregar_modelo_constancia.php'">
                  <i class="fa fa-plus"></i>
                  Agregar
                </a>
                <a class="btn btn-sm blue active"  onclick="javascript: window.location='../submenu_constancias.php'">
                  <i class="fa fa-arrow-left"></i> Regresar
                </a>
              </div>
            </div>
            <div class="portlet-body">
              <form action="" method="post" name="frmPrincipal" id="frmPrincipal">
              <table class="table table-striped table-bordered table-hover" id="table_datatable">
              <thead>
              <tr>
                <th>C&oacute;digo</th>
                <th>Nombre</th>
                <th>Tipo</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
              </tr>
              </thead>
              <tbody>
              <?php
                while($fila = $res->fetch_array())
                {                 
                ?>
                  <tr class="odd gradeX">
                    <td><?php echo $fila['codigo']; ?></td>
                    <td><?php echo $fila['nombre']; ?></td>
                    <td><?php echo $fila['tipo_documento']; ?></td>
                    <td style="text-align: center">
                        <a href="javascript:enviar('2',<?php echo($fila['codigo']); ?>);" title="Editar"><img src="../../../includes/imagenes/icons/pencil.png" alt="Editar" width="16" height="16"></a>
                    </td>
                    <td style="text-align: center">
                        <a href="javascript:enviar('3',<?php echo($fila['codigo']); ?>);" title="Eliminar"><img src="../../../includes/imagenes/icons/delete.png" alt="Eliminar" width="16" height="16"></a>
                    </td>
                  </tr>
                <?php                 
                }
              ?>
              </tbody>
              </table>
                  <input type="hidden" name="opcion" value="">  
                  <input type="hidden" name="codigo" value=""> 
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
<!-- BEGIN JAVASCRIPTS(Load javascripts at bottom, this will reduce page load time) -->
<!-- BEGIN CORE PLUGINS -->
<!--[if lt IE 9]>
<script src="../../../includes/assets/plugins/respond.min.js"></script>
<script src="../../../includes/assets/plugins/excanvas.min.js"></script> 
<![endif]-->
<script src="../../../includes/assets/plugins/jquery-1.10.2.min.js" type="text/javascript"></script>
<script src="../../../includes/assets/plugins/jquery-migrate-1.2.1.min.js" type="text/javascript"></script>
<script src="../../../includes/assets/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script src="../../../includes/assets/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js" type="text/javascript"></script>
<script src="../../../includes/assets/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
<script src="../../../includes/assets/plugins/jquery.blockui.min.js" type="text/javascript"></script>
<script src="../../../includes/assets/plugins/jquery.cokie.min.js" type="text/javascript"></script>
<script src="../../../includes/assets/plugins/uniform/jquery.uniform.min.js" type="text/javascript"></script>
<!-- END CORE PLUGINS -->
<!-- BEGIN PAGE LEVEL PLUGINS -->
<script type="text/javascript" src="../../../includes/assets/plugins/select2/select2.min.js"></script>
<script type="text/javascript" src="../../../includes/assets/plugins/select2/select2_locale_es.js"></script>
<script type="text/javascript" src="../../../includes/assets/plugins/data-tables/jquery.dataTables.js"></script>
<script type="text/javascript" src="../../../includes/assets/plugins/data-tables/DT_bootstrap.js"></script>
<script type="text/javascript" src="../../../includes/assets/plugins/data-tables/extensions/TableTools/js/dataTables.tableTools.min.js"></script>
<script type="text/javascript" src="../../../includes/assets/plugins/data-tables/extensions/ColReorder/js/dataTables.colReorder.min.js"></script>
<script type="text/javascript" src="../../../includes/assets/plugins/data-tables/extensions/Scroller/js/dataTables.scroller.min.js"></script>
<script type="text/javascript" src="../../../includes/assets/plugins/data-tables/plugins/bootstrap/dataTables.bootstrap.js"></script>
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="../../../includes/assets/scripts/core/app1.js"></script>
<script>
jQuery(document).ready(function() {       
   App.init();
});
</script>
<script type="text/javascript">
 $(document).ready(function() { 
      $('#table_datatable').DataTable({
        "iDisplayLength": 25,
        "sPaginationType": "bootstrap_extended", 
        "oLanguage": {
          "sSearch": "<img src='../../../includes/imagenes/icons/magnifier.png' width='16' height='16' > Buscar:",
            "sLengthMenu": "Mostrar _MENU_",
            "sInfoEmpty": "",
            "sInfo":"Total _TOTAL_ registros",
            "sInfoFiltered": "",
            "sEmptyTable":  "No hay datos disponibles",
            "sZeroRecords": "No se encontraron registros",
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
            { 'bSortable': false, 'aTargets': [3, 4] },
            { "bSearchable": false, "aTargets": [3, 4] },
            { "sWidth": "8%", "aTargets": [3, 4] },
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