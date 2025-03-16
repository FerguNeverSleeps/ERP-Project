<?php
session_start();
ob_start();
$termino = $_SESSION['termino'];
require_once "./modelo/conexion.php";
?>
<script>
    
    function Enviar(){                  
      
        
            document.frmEmpresas.op_tp.value=2;
       
    }
</script>
<?php
//$registro_id=$_POST[registro_id];
$op_tp = isset($_POST['op_tp'])? $_POST['op_tp'] : '';
//$fecha_actual=date("Y-m-d");
//$validacion=0;


try
{
    $conex = Conexion::conectar($_SESSION['bd']);
    $query  = "SELECT * FROM  nomina_electronica_parametros; ";
    $select = $conex->prepare( $query);
    $select -> execute();
    $rows = $select -> fetchAll( PDO::FETCH_ASSOC );
    $row = $rows[0];

    $codigo                        = $row["id_configuracion"];
    $token_enterprise              = $row["token_enterprise"];
    $usuario                       = $row["usuario"];
    $contrasenia                   = $row["contrasenia"];
    $token_password                = $row["token_password"];
    $id_software                   = $row["id_software"];
    $nit_empleador                 = $row["nit_empleador"];
    $url_home                      = $row["url_home"];
    $url_autorizada                = $row["url_autorizada"];
    $correlativo_prefijo           = $row["correlativo_prefijo"];
    $correlativo_contador          = $row["correlativo_contador"];
    $correlativo_anulados_prefijo  = $row["correlativo_anulados_prefijo"];
    $correlativo_anulados_contador = $row["correlativo_anulados_contador"];
}
catch(Exception $th)
{
    return $th->getMessage();
}
//msgbox($for_recibo_liq);
?>

<!-- BEGIN GLOBAL MANDATORY STYLES 
<link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css"/>-->
<link href="../../../includes/assets/plugins/font-awesome/css/font-awesome.css" rel="stylesheet" type="text/css"/>
<link href="../../../includes/css/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css"/>
<link href="../../../includes/assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
<link href="../../../includes/assets/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css"/>
<link href="../../../includes/assets/plugins/bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet" type="text/css"/>
<!-- END GLOBAL MANDATORY STYLES -->
<!-- BEGIN PAGE LEVEL STYLES -->
<link rel="stylesheet" type="text/css" href="../../../includes/assets/plugins/select2/select2.css"/>
<link rel="stylesheet" type="text/css" href="../../../includes/assets/plugins/select2/select2-metronic.css"/>
<link rel="stylesheet" href="../../../includes/assets/plugins/data-tables/DT_bootstrap.css"/>
<!-- END PAGE LEVEL STYLES -->
<!-- BEGIN THEME STYLES -->
<link href="../../../includes/css/components.css" rel="stylesheet" type="text/css"/>
<link href="../../../includes/assets/css/style-metronic.css" rel="stylesheet" type="text/css"/>
<link href="../../../includes/assets/css/style.css" rel="stylesheet" type="text/css"/>
<link href="../../../includes/assets/css/style-responsive.css" rel="stylesheet" type="text/css"/>
<link href="../../../includes/assets/css/plugins.css" rel="stylesheet" type="text/css"/>
<style type="text/css">
  .tile-icon {
    color: white;
    line-height: 125px; 
    font-size: 80px;
}
</style>
<!-- <link href="../../../includes/assets/css/themes/default.css" rel="stylesheet" type="text/css" id="style_color"/> -->
<link href="../../../includes/assets/css/custom.css" rel="stylesheet" type="text/css"/>
<!-- END THEME STYLES -->
<link href="../../../includes/assets/css/custom-header4.css" rel="stylesheet" type="text/css"/>
</head>
<!-- END HEAD -->
<!-- BEGIN BODY -->
<body class="page-header-fixed page-full-width"  marginheight="0">
<meta lang="es">
<meta charset="utf-8">

<div class="page-container">
  <!-- BEGIN CONTENT -->
  <div class="page-content-wrapper">
    <div class="page-content">
      <!-- BEGIN PAGE CONTENT-->
      <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
          <!-- BEGIN EXAMPLE TABLE PORTLET-->
          <div class="portlet box blue">
            <div class="portlet-title">
              <div class="caption">
                  <img src="../../imagenes/21.png" width="20" height="20" class="icon"> Nomina Emisión Electronica / Parametros
              </div>
              <div class="actions">
                <a class="btn btn-sm green" id="generarNomina">
                    <i class="fa fa-cogs"></i> Generar
                  </a>                    
                  <a class="btn btn-sm blue"  onclick="javascript: window.location='index.php'">
                    <i class="fa fa-arrow-left"></i> Regresar
                  </a>
              </div>
            </div>
            <div class="portlet-body">
              <div id="listadoNominaIndividual"></div>
              <!-- END PORTLET BODY-->            
            </div>
          </div>
        <!-- END EXAMPLE TABLE PORTLET-->
        </div>
      </div>
    </div>
    <!-- END PAGE CONTENT-->
  </div>
    <!-- END CONTENT -->
</div>

<!--=====================================
=          Generar Nómina Modal        =
======================================-->
<!--MODAL-->
<div class="modal fade" id="modalGenerarNominaIndividual" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" style="width: 900px">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
        <h4 class="modal-title">Generación Nómina Electrónica</h4>
      </div>
      <div class="modal-body">
						<div class="portlet-body form" id="blockui_portlet_body">

							<form class="form-horizontal" id="form_nomina_electronica_proceso_nomina_general_periodos_global_procesar" name="form_nomina_electronica_proceso_nomina_general_periodos_global_procesar" method="post">
								<input type="hidden" id="codtip" name="codtip" value="<?= $_SESSION['codigo_nomina'];?>">
								<div class="row">&nbsp;</div>
								<div class="row">
									<div class="form-group">
										<label class="text-right col-xs-4 col-sm-4 col-md-4 col-lg-4 control-label">Año:</label>
                    <div class="col-md-5">
                      <select name="anio" id="anio" class="form-control">
                        <option value="">Seleccione Año...</option>
                      </select>
                    </div>
                  </div><p></p>
                </div>
								<div class="row">
									<div class="form-group">
                    <label class="text-right col-xs-4 col-sm-4 col-md-4 col-lg-4 control-label">Mes:</label>
                    <div class="col-md-5">
                      <select name="mes" id="mes" class="form-control">
                        <option value="01">Enero</option>
                        <option value="02">Febrero</option>
                        <option value="03">Marzo</option>
                        <option value="04">Abril</option>
                        <option value="05">Mayo</option>
                        <option value="06">Junio</option>
                        <option value="07">Julio</option>
                        <option value="08">Agosto</option>
                        <option value="09">Septiembre</option>
                        <option value="10">Octubre</option>
                        <option value="11">Noviembre</option>
                        <option value="12">Diciembre</option>
                      </select>
                    </div>
                  </div>
                </div>

								<div class="row">
									<div class="form-group">
										<label class="text-right col-xs-4 col-sm-4 col-md-4 col-lg-4 control-label">Fecha Inicio</label>
                    <div class="col-md-5">
                      <div class="input-group date date-picker" data-provide="datepicker"  data-date-format="dd/mm/yyyy"> 
                        <input name="fecha_inicio" type="text" id="fecha_inicio" class="form-control" value="<?php echo "01/".date("m")."/".date("Y") ?>" maxlength="60">
                        <span class="input-group-btn">
                          <button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
                        </span>
                      </div>
                    </div>
                  </div>
								</div>

								<div class="row">
									<div class="form-group">
										<label class="text-right col-xs-4 col-sm-4 col-md-4 col-lg-4 control-label">Fecha Fin</label>
                    <div class="col-md-5">
                      <div class="input-group date date-picker" data-provide="datepicker"  data-date-format="dd/mm/yyyy"> 
                        <input name="fecha_fin" type="text" id="fecha_fin" class="form-control" value="<?php echo "15/".date("m")."/".date("Y") ?>" maxlength="60">
                        <span class="input-group-btn">
                          <button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
                        </span>
                      </div>
                    </div>
                  </div>
								</div>
                                                                
                <div class="row">
									<div class="form-group">
										<label class="text-right col-xs-4 col-sm-4 col-md-4 col-lg-4 control-label">Descripci&oacute;n</label>
                    <div class="col-md-5">
                      <textarea name="descripcion" id="descripcion" class="form-control" rows="3" style="resize:vertical;"></textarea>
                    </div>
                  </div>
								</div>

							</form>

						</div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn dark" data-dismiss="modal">Cerrar</button>&nbsp;
        <button type="button" class="btn green active" id="btn-procesar">Procesar</button>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
 



<!-- BEGIN JAVASCRIPTS(Load javascripts at bottom, this will reduce page load time) -->
<!-- BEGIN CORE PLUGINS -->
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

<script src="../../../includes/assets/plugins/moment.min.js" type="text/javascript"></script>
<script src="../../../includes/assets/plugins/bootstrap-daterangepicker/daterangepicker.min.js" type="text/javascript"></script>
<script src="../../../includes/assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js" type="text/javascript"></script>        
<script src="../../../includes/assets/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
<script src="../../../includes/assets/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js" type="text/javascript"></script>
<script src="../../../includes/assets/plugins/bootstrap-table/bootstrap-table.min.js" type="text/javascript"></script>
<script src="../../../includes/assets/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js" type="text/javascript"></script>
<script src="../../../includes/assets/plugins/bootbox/bootbox.min.js" type="text/javascript"></script>
<script src="../../../includes/assets/plugins/blockui/blockui.js" type="text/javascript"></script>
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="../../../includes/assets/scripts/core/app1.js"></script>

<p>&nbsp;</p>  
</form>
<p>&nbsp;</p>
</body>
<script src="./js/nomina_individual.js?<?= date("smh") ?>"></script>
</html>
