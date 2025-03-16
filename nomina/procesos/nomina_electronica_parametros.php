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
   
   
    $query = "UPDATE nomina_electronica_parametros set 
                usuario                         ='$_POST[usuario]',
                contrasenia                     ='$_POST[contrasenia]',
                token_enterprise                ='$_POST[token_enterprise]',
                token_password                  ='$_POST[token_password]',
                nit_empleador                   ='$_POST[nit_empleador]',
                url_home                        ='$_POST[url_home]',
                url_autorizada                  ='$_POST[url_autorizada]',
                id_software                     ='$_POST[id_software]',
                correlativo_prefijo             ='$_POST[correlativo_prefijo]',
                correlativo_contador            ='$_POST[correlativo_contador]',
                correlativo_anulados_prefijo    ='$_POST[correlativo_anulados_prefijo]',
                correlativo_anulados_contador   ='$_POST[correlativo_anulados_contador]'
             WHERE id_configuracion=1";


    $result = sql_ejecutar($query);

    echo '<br><font color="green"><strong> Parámetros Actualizados. </strong></font>';
}

$query                          = "SELECT * FROM  nomina_electronica_parametros";
$result                         = sql_ejecutar($query);
$row                            = mysqli_fetch_array($result);
$codigo                         = $row["id_configuracion"];
$token_enterprise               = $row["token_enterprise"];
$usuario                        = $row["usuario"];
$contrasenia                    = $row["contrasenia"];
$token_password                 = $row["token_password"];
$id_software                    = $row["id_software"];
$nit_empleador                  = $row["nit_empleador"];
$url_home                       = $row["url_home"];
$url_autorizada                 = $row["url_autorizada"];
$correlativo_prefijo            = $row["correlativo_prefijo"];
$correlativo_contador           = $row["correlativo_contador"];
$correlativo_anulados_prefijo   = $row["correlativo_anulados_prefijo"];
$correlativo_anulados_contador  = $row["correlativo_anulados_contador"];
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
                    <div class="caption">
                      <img src="../imagenes/21.png" width="20" height="20" class="icon"> Nomina Electronica / Parametros
                    </div>
                    <div class="actions">

                            
                            <a class="btn btn-sm blue"  onclick="javascript: window.location='nomina_electronica_menu.php'">
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
                            <label>Codigo:</label>  
                        </div>    
                        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                            <input class="form-control" name="txtcodigo" type="text" id="txtcodigo" disabled="disabled" value="<?php echo $codigo; ?>" >

                        </div>

                        
                    </div>
                    <BR>
                    
                    <div class="row">
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                            <label for="txtnombre">Usuario:</label>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">

                            <!--<textarea rows="1" class="form-control" name="usuario" id="usuario"> <?php echo $usuario; ?></textarea>-->
                            <input class="form-control" name="usuario" type="text" id="usuario" value="<?php echo $usuario; ?>">

                        </div>                
                    </div>
                    <BR>
                    <div class="row">
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                            <label for="txtnombre">Contraseña:</label>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">

                            <input class="form-control" name="contrasenia" type="text" id="contrasenia" value="<?php echo $contrasenia; ?>">

                        </div>                
                    </div>
                    <BR>
                    <div class="row">
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                            <label for="txtnombre">Token Enterprise:</label>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">

                            <input class="form-control" name="token_enterprise" type="text" id="token_enterprise" value="<?php echo $token_enterprise; ?>">

                        </div>                
                    </div>
                    <BR>
                    <div class="row">
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                            <label for="txtnombre">Token Password:</label>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">

                            <input class="form-control" name="token_password" type="text" id="cliente_id" value="<?php echo $token_password; ?>">

                        </div>                
                    </div>
                    <BR>
                    <div class="row">
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                            <label for="txtnombre">ID Software:</label>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">

                            <input class="form-control" name="id_software" type="text" id="id_software" value="<?php echo $id_software; ?>">

                        </div>                
                    </div>
                    <BR>
                    <div class="row">
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                            <label for="txtnombre">NIT Empleador:</label>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">

                            <input class="form-control" name="nit_empleador" type="text" id="nit_empleador" value="<?php echo $nit_empleador; ?>">

                        </div>                
                    </div>
                    <BR>
                    <div class="row">
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                            <label for="txtnombre">URL Home:</label>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">

                            <input class="form-control" name="url_home" type="text" id="url_home" value="<?php echo $url_home; ?>">

                        </div>                
                    </div>
                    <BR>
                    <div class="row">
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                            <label for="txtnombre">URL Autorizada:</label>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">

                            <input class="form-control" name="url_autorizada" type="text" id="url_autorizada" value="<?php echo $url_autorizada; ?>">

                        </div>                
                    </div>
                    <BR>
                    <div class="row">
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                            <label for="txtnombre">Correlativo Prefijo:</label>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">

                            <input class="form-control" name="correlativo_prefijo" type="text" id="correlativo_prefijo" value="<?php echo $correlativo_prefijo; ?>">

                        </div>                
                    </div>
                    <BR>
                    <div class="row">
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                            <label for="txtnombre">Correlativo Contador:</label>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">

                            <input class="form-control" name="correlativo_contador" type="text" id="correlativo_contador" value="<?php echo $correlativo_contador; ?>">

                        </div>                
                    </div>
                    <BR>
                    
                    <div class="row">
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                            <label for="txtnombre">Correlativo Anulados Prefijo:</label>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">

                            <input class="form-control" name="correlativo_anulados_prefijo" type="text" id="correlativo_anulados_prefijo" value="<?php echo $correlativo_anulados_prefijo; ?>">

                        </div>                
                    </div>
                    <BR>
                    <div class="row">
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                            <label for="txtnombre">Correlativo Anulados Contador:</label>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">

                            <input class="form-control" name="correlativo_anulados_contador" type="text" id="correlativo_anulados_contador" value="<?php echo $correlativo_anulados_contador; ?>">

                        </div>                
                    </div>
                    <BR>
                    
                   <div class="row">
                        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                        </div>                    
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">                            
                                 <?php boton_metronic('ok', 'Enviar(); document.frmEmpresas.submit();', 2) ?>
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
