<?php
session_start();
ob_start();
$termino = $_SESSION['termino'];
include("../lib/common.php");
include("../paginas/func_bd.php");
?>
<script>
    
    function Enviar(){                  
      
        
            document.frmEmpresas.op_tp.value=2;
       
    }
</script>
<script language="javascript" type="text/javascript" src="datetimepicker.js">
    //Date Time Picker script- by TengYong Ng of http://www.rainforestnet.com
    //Script featured on JavaScript Kit (http://www.javascriptkit.com)
    //For this script, visit http://www.javascriptkit.com
</script>

<?php
//$registro_id=$_POST[registro_id];
$op_tp = $_POST['op_tp'];
//$fecha_actual=date("Y-m-d");
//$validacion=0;

if ($op_tp == 2) {
   
   
    $query = "UPDATE zoho_contable_parametros set                 
                id_organizacion    ='$_POST[id_organizacion]',
                usuario            ='$_POST[usuario]',
                contrasenia        ='$_POST[contrasenia]',
                cliente_nombre     ='$_POST[cliente_nombre]',
                cliente_id         ='$_POST[cliente_id]',
                cliente_secreto    ='$_POST[cliente_secreto]',
                url_home           ='$_POST[url_home]',
                url_autorizada     ='$_POST[url_autorizada]',
                tags_id            ='$_POST[tags_id]'
                WHERE id_configuracion=1";


    $result = sql_ejecutar($query);

    echo '<br><font color="green"><strong> LA CONFIGURACION HA SIDO ACTUALIZADA. </strong></font>';
}

$query                          = "SELECT * FROM  zoho_contable_parametros";
$result                         = sql_ejecutar($query);
$row                            = mysqli_fetch_array($result);
$codigo                         = $row["id_configuracion"];
$id_organizacion                = $row["id_organizacion"];
$usuario                        = $row["usuario"];
$contrasenia                    = $row["contrasenia"];
$cliente_nombre                 = $row["cliente_nombre"];
$cliente_secreto                = $row["cliente_secreto"];
$cliente_id                     = $row["cliente_id"];
$url_home                       = $row["url_home"];
$url_autorizada                 = $row["url_autorizada"];
$tags_id                        = $row["tags_id"];

//msgbox($for_recibo_liq);
?>

<!-- BEGIN GLOBAL MANDATORY STYLES 
<link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css"/>-->
<link href="../../includes/assets/plugins/font-awesome/css/font-awesome.css" rel="stylesheet" type="text/css"/>
<link href="../../includes/css/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css"/>
<link href="../../includes/assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
<link href="../../includes/assets/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css"/>
<link href="../../includes/assets/plugins/bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet" type="text/css"/>
<!-- END GLOBAL MANDATORY STYLES -->
<!-- BEGIN PAGE LEVEL STYLES -->
<link rel="stylesheet" type="text/css" href="../../includes/assets/plugins/select2/select2.css"/>
<link rel="stylesheet" type="text/css" href="../../includes/assets/plugins/select2/select2-metronic.css"/>
<link rel="stylesheet" href="../../includes/assets/plugins/data-tables/DT_bootstrap.css"/>
<!-- END PAGE LEVEL STYLES -->
<!-- BEGIN THEME STYLES -->
<link href="../../includes/css/components.css" rel="stylesheet" type="text/css"/>
<link href="../../includes/assets/css/style-metronic.css" rel="stylesheet" type="text/css"/>
<link href="../../includes/assets/css/style.css" rel="stylesheet" type="text/css"/>
<link href="../../includes/assets/css/style-responsive.css" rel="stylesheet" type="text/css"/>
<link href="../../includes/assets/css/plugins.css" rel="stylesheet" type="text/css"/>
<style type="text/css">
  .tile-icon {
    color: white;
    line-height: 125px; 
    font-size: 80px;
}
</style>
<!-- <link href="../../includes/assets/css/themes/default.css" rel="stylesheet" type="text/css" id="style_color"/> -->
<link href="../../includes/assets/css/custom.css" rel="stylesheet" type="text/css"/>
<!-- END THEME STYLES -->
<link href="../../includes/assets/css/custom-header4.css" rel="stylesheet" type="text/css"/>
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
              <h4>ZOHO CONTABLE - PARAMETROS</h4>
            </div>
            <div class="portlet-body">
            <form action="" enctype="multipart/form-data" method="post" name="frmEmpresas" id="frmEmpresas" role="form">
                <input name="op_tp" type="Hidden" id="op_tp" value="-1">
                <input name="registro_id" type="Hidden" id="registro_id" value="<?php echo $_POST[registro_id]; ?>">
                <div class="form-body">
                    <div class="row">                   
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                            <label>CODIGO:</label>  
                        </div>    
                        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                            <input class="form-control" name="txtcodigo" type="text" id="txtcodigo" disabled="disabled" value="<?php echo $codigo; ?>" >

                        </div>

                        
                    </div>
                    <BR>
                    <div class="row">
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                            <label for="txtnombre">ID ORGANIZACION:</label>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">

                            <!--<textarea rows="1" class="form-control" name="id_organizacion"  id="id_organizacion"> <?php echo $id_organizacion; ?> </textarea>-->
                            <input class="form-control" name="id_organizacion" type="text" id="id_organizacion" value="<?php echo $id_organizacion; ?>">

                        </div>                
                    </div>
                    <BR>
                    <div class="row">
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                            <label for="txtnombre">USUARIO:</label>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">

                            <!--<textarea rows="1" class="form-control" name="usuario" id="usuario"> <?php echo $usuario; ?></textarea>-->
                            <input class="form-control" name="usuario" type="text" id="usuario" value="<?php echo $usuario; ?>">

                        </div>                
                    </div>
                    <BR>
                    <div class="row">
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                            <label for="txtnombre">CONTRASEÑA:</label>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">

                            <input class="form-control" name="contrasenia" type="text" id="contrasenia" value="<?php echo $contrasenia; ?>">

                        </div>                
                    </div>
                    <BR>
                    <div class="row">
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                            <label for="txtnombre">CLIENTE NOMBRE:</label>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">

                            <input class="form-control" name="cliente_nombre" type="text" id="cliente_nombre" value="<?php echo $cliente_nombre; ?>">

                        </div>                
                    </div>
                    <BR>
                    <div class="row">
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                            <label for="txtnombre">CLIENTE ID:</label>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">

                            <input class="form-control" name="cliente_id" type="text" id="cliente_id" value="<?php echo $cliente_id; ?>">

                        </div>                
                    </div>
                    <BR>
                    <div class="row">
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                            <label for="txtnombre">CLIENTE SECRETO:</label>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">

                            <input class="form-control" name="cliente_secreto" type="text" id="cliente_secreto" value="<?php echo $cliente_secreto; ?>">

                        </div>                
                    </div>
                    <BR>
                    <div class="row">
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                            <label for="txtnombre">URL HOME:</label>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">

                            <input class="form-control" name="url_home" type="text" id="url_home" value="<?php echo $url_home; ?>">

                        </div>                
                    </div>
                    <BR>
                    <div class="row">
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                            <label for="txtnombre">URL AUTORIZADA:</label>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">

                            <input class="form-control" name="url_autorizada" type="text" id="url_autorizada" value="<?php echo $url_autorizada; ?>">

                        </div>                
                    </div>
                    <BR>
                    <div class="row">
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                            <label for="txtnombre">TAGS ID:</label>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">

                            <input class="form-control" name="tags_id" type="text" id="tags_id" value="<?php echo $tags_id; ?>">

                        </div>                
                    </div>
                    <BR>
                    
                    
                   <div class="row">
                        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                        </div>                    
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">                            
                                 <?php boton_metronic('ok', 'Enviar(); document.frmEmpresas.submit();', 2) ?>
                        </div>                    

                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                                <?php boton_metronic('cancel', 'history.back();', 2) ?>
                        </div>                    
                        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">                            
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
<script src="../../includes/assets/plugins/respond.min.js"></script>
<script src="../../includes/assets/plugins/excanvas.min.js"></script> 
<![endif]-->
<script src="../../includes/assets/plugins/jquery-1.10.2.min.js" type="text/javascript"></script>
<script src="../../includes/assets/plugins/jquery-migrate-1.2.1.min.js" type="text/javascript"></script>
<script src="../../includes/assets/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script src="../../includes/assets/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js" type="text/javascript"></script>
<script src="../../includes/assets/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
<script src="../../includes/assets/plugins/jquery.blockui.min.js" type="text/javascript"></script>
<script src="../../includes/assets/plugins/jquery.cokie.min.js" type="text/javascript"></script>
<script src="../../includes/assets/plugins/uniform/jquery.uniform.min.js" type="text/javascript"></script>
<!-- END CORE PLUGINS -->
<!-- BEGIN PAGE LEVEL PLUGINS -->
<script type="text/javascript" src="../../includes/assets/plugins/select2/select2.min.js"></script>
<script type="text/javascript" src="../../includes/assets/plugins/data-tables/jquery.dataTables.js"></script>
<script type="text/javascript" src="../../includes/assets/plugins/data-tables/DT_bootstrap.js"></script>
<!-- END PAGE LEVEL PLUGINS -->

<p>&nbsp;</p>  
</form>
<p>&nbsp;</p>
</body>
</html>
