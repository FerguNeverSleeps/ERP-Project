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
<script language="javascript" type="text/javascript" src="../datetimepicker.js">
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

                            
                            <a class="btn btn-sm blue"  onclick="javascript: window.location='index.php'">
                                    <i class="fa fa-arrow-left"></i> Regresar
                            </a>
                    </div>
            </div>
            <div class="portlet-body">
            <form action="" enctype="multipart/form-data" method="post" name="frmEmpresas" id="frmEmpresas" role="form">
                <input name="op_tp" type="Hidden" id="op_tp" value="-1">
                <input name="registro_id" type="Hidden" id="registro_id" value="<?php echo $_POST[registro_id]; ?>">
                <div class="form-body">
                    <div class="row">                   
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                            <label>Grant Type:</label>  
                        </div>    
                        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                            <input class="form-control" name="grant_type" type="text" id="grant_type" disabled="disabled" value="<?php echo $codigo; ?>" >
                        </div>
                    </div>
                    
                    
                </div>
            <!-- END PORTLET BODY-->            
            </form>
            </div>
        <!-- END EXAMPLE TABLE PORTLET-->
        </div>
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
<script type="text/javascript" src="../../../includes/assets/plugins/data-tables/jquery.dataTables.js"></script>
<script type="text/javascript" src="../../../includes/assets/plugins/data-tables/DT_bootstrap.js"></script>
<!-- END PAGE LEVEL PLUGINS -->

<p>&nbsp;</p>  
</form>
<p>&nbsp;</p>
</body>
</html>
