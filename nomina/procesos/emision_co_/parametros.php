<?php
session_start();
ob_start();
$termino = $_SESSION['termino'];
require_once "./modelo/conexion.php";
?>
<script>
    
    function Enviar(){                  
      
        
            document.formParametros.op_tp.value=2;
       
    }
</script>
<!--<script language="javascript" type="text/javascript" src="../datetimepicker.js">
    //Date Time Picker script- by TengYong Ng of http://www.rainforestnet.com
    //Script featured on JavaScript Kit (http://www.javascriptkit.com)
    //For this script, visit http://www.javascriptkit.com
</script>-->

<?php
//$registro_id=$_POST[registro_id];
$op_tp = isset($_POST['op_tp'])? $_POST['op_tp'] : '';
//$fecha_actual=date("Y-m-d");
//$validacion=0;

if ($op_tp == 2) {
   $query = "INSERT emision_col_parametros set 
                id_configuracion      = '1',
                grant_type            = '$_POST[grant_type]',
                client_id             = '$_POST[client_id]',
                client_secret         = '$_POST[client_secret]',
                bearer_token          = '$_POST[bearer_token]',
                token_expiracion      = '$_POST[token_expiracion]',
                username              = '$_POST[username]',
                password              = '$_POST[password]',
                secuencia_prefijo     = '$_POST[secuencia_prefijo]',
                secuencia_contador    = '$_POST[secuencia_contador]',
                id_software           = '$_POST[id_software]',
                pin_software          = '$_POST[pin_software]',
                razonsocial_software  = '$_POST[razonsocial_software]',
                nit_software          = '$_POST[nit_software]',
                dv_software           = '$_POST[dv_software]',
                razonsocial_empleador = '$_POST[razonsocial_empleador]',
                nit_empleador         = '$_POST[nit_empleador]',
                dv_empleador          = '$_POST[dv_empleador]',
                pais_empleador        = '$_POST[pais_empleador]',
                dep_empleador         = '$_POST[dep_empleador]',
                mun_empleador         = '$_POST[mun_empleador]',
                dir_empleador         = '$_POST[dir_empleador]',
                url_home              = '$_POST[url_home]',
                url_autorizada        = '$_POST[url_autorizada]',
                correlativo_prefijo   = '$_POST[correlativo_prefijo]',
                correlativo_contador  = '$_POST[correlativo_contador]'
             ON DUPLICATE KEY UPDATE 
                grant_type            = '$_POST[grant_type]',
                client_id             = '$_POST[client_id]',
                client_secret         = '$_POST[client_secret]',
                bearer_token          = '$_POST[bearer_token]',
                token_expiracion      = '$_POST[token_expiracion]',
                username              = '$_POST[username]',
                password              = '$_POST[password]',
                secuencia_prefijo     = '$_POST[secuencia_prefijo]',
                secuencia_contador    = '$_POST[secuencia_contador]',
                id_software           = '$_POST[id_software]',
                pin_software          = '$_POST[pin_software]',
                razonsocial_software  = '$_POST[razonsocial_software]',
                nit_software          = '$_POST[nit_software]',
                dv_software           = '$_POST[dv_software]',
                razonsocial_empleador = '$_POST[razonsocial_empleador]',
                nit_empleador         = '$_POST[nit_empleador]',
                dv_empleador          = '$_POST[dv_empleador]',
                pais_empleador        = '$_POST[pais_empleador]',
                dep_empleador         = '$_POST[dep_empleador]',
                mun_empleador         = '$_POST[mun_empleador]',
                dir_empleador         = '$_POST[dir_empleador]',
                url_home              = '$_POST[url_home]',
                url_autorizada        = '$_POST[url_autorizada]',
                correlativo_prefijo   = '$_POST[correlativo_prefijo]',
                correlativo_contador  = '$_POST[correlativo_contador]';";


    $conex = Conexion::conectar($_SESSION['bd']);
    $update = $conex->prepare( $query);
    $update -> execute();

    echo '<br><font color="green"><strong> Parámetros Actualizados. </strong></font>';
}

try
{
    $conex = Conexion::conectar($_SESSION['bd']);
    $query  = "SELECT * FROM  emision_col_parametros; ";
    $select = $conex->prepare( $query);
    $select -> execute();
    $rows = $select -> fetchAll( PDO::FETCH_ASSOC );
    $row = $rows[0];

    $grant_type            = $row["grant_type"];
    $client_id             = $row["client_id"];
    $client_secret         = $row["client_secret"];
    $bearer_token          = $row["bearer_token"];
    $token_expiracion      = $row["token_expiracion"];
    $username              = $row["username"];
    $password              = $row["password"];
    $secuencia_prefijo     = $row["secuencia_prefijo"];
    $secuencia_contado     = $row["secuencia_contado"];
    $cliente_id            = $row["cliente_id"];
    $id_software           = $row["id_software"];
    $pin_software          = $row["pin_software"];
    $razonsocial_software  = $row["razonsocial_software"];
    $nit_software          = $row["nit_software"];
    $dv_software           = $row["dv_software"];
    $razonsocial_empleador = $row["razonsocial_empleador"];
    $nit_empleador         = $row["nit_empleador"];
    $dv_empleador          = $row["dv_empleador"];
    $pais_empleador        = $row["pais_empleador"];
    $dep_empleador         = $row["dep_empleador"];
    $mun_empleador         = $row["mun_empleador"];
    $dir_empleador         = $row["dir_empleador"];
    $url_home              = $row["url_home"];
    $url_autorizada        = $row["url_autorizada"];
    $correlativo_prefijo   = $row["correlativo_prefijo"];
    $correlativo_contador  = $row["correlativo_contador"];
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
<script>
    function Enviar()
    {
        document.formParametros.op_tp.value=2;       
    }
</script>
</head>
<!-- END HEAD -->
<!-- BEGIN BODY -->
<body class="page-header-fixed page-full-width"  marginheight="0">
<meta lang="es">
<meta charset="utf-4">

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
            <form action="" enctype="multipart/form-data" method="post" name="formParametros" id="formParametros" role="form">
                <input name="op_tp" type="Hidden" id="op_tp" value="-1">
                <input name="registro_id" type="Hidden" id="registro_id" value="<?php echo $_POST[registro_id]; ?>">
                <div class="form-body">
                    <div class="row">                   
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                            <label>Grant Type:</label>  
                        </div>    
                        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                            <input class="form-control" name="grant_type" type="text" id="grant_type" value="<?php echo $grant_type; ?>" >
                        </div>
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                            <label for="txtnombre">Cliente ID:</label>
                        </div>
                        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                            <input class="form-control" name="client_id" type="text" id="client_id" value="<?php echo $client_id; ?>">
                        </div>                
                    </div>
                    <BR>
                    <div class="row">
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                            <label for="txtnombre">Usuario:</label>
                        </div>
                        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                            <input class="form-control" name="username" type="text" id="username" value="<?php echo $username; ?>">
                        </div>  
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                            <label for="txtnombre">Client Secret:</label>
                        </div>
                        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                            <input class="form-control" name="client_secret" type="text" id="client_secret" value="<?php echo $client_secret; ?>">
                        </div>             
                    </div>
                    <BR>
                    <div class="row">      
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                            <label for="txtnombre">Contraseña:</label>
                        </div>
                        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                            <input class="form-control" name="password" type="text" id="password" value="<?php echo $password; ?>">
                        </div>  
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                            <label for="txtnombre">Bearer Token:</label>
                        </div>
                        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                            <input class="form-control" name="token_enterprise" type="text" id="bearer_token" value="<?php echo $bearer_token; ?>">
                        </div>                
                    </div>
                    <BR>
                    <div class="row">
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                            <label for="txtnombre">Token expiración:</label>
                        </div>
                        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                            <input class="form-control" name="token_expiracion" type="text" id="cliente_id" value="<?php echo $token_expiracion; ?>">
                        </div>             
                    </div>
                    <hr>
                    <BR>
                    <div class="row">
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                            <label for="txtnombre">URL Home:</label>
                        </div>
                        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">

                            <input class="form-control" name="url_home" type="text" id="url_home" value="<?php echo $url_home; ?>">

                        </div>
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                            <label for="txtnombre">URL Autorizada:</label>
                        </div>
                        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">

                            <input class="form-control" name="url_autorizada" type="text" id="url_autorizada" value="<?php echo $url_autorizada; ?>">

                        </div>    
                    </div>
                    <BR>
                    <div class="row">  
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                            <label for="txtnombre">ID Software:</label>
                        </div>
                        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                            <input class="form-control" name="id_software" type="text" id="id_software" value="<?php echo $id_software; ?>">
                        </div>      
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                            <label for="txtnombre">PIN Software:</label>
                        </div>
                        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                            <input class="form-control" name="pin_software" type="text" id="pin_software" value="<?php echo $pin_software; ?>">
                        </div>         
                    </div>
                    <BR>
                    <div class="row">  
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                            <label for="txtnombre">Razón Social Software:</label>
                        </div>
                        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                            <input class="form-control" name="razonsocial_software" type="text" id="razonsocial_software" value="<?php echo $razonsocial_software; ?>">
                        </div>          
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                            <label for="txtnombre">NIT Software:</label>
                        </div>
                        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                            <input class="form-control" name="nit_software" type="text" id="nit_software" value="<?php echo $nit_software; ?>">
                        </div>      
                    </div>
                    <BR>
                    <div class="row"> 
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                            <label for="txtnombre">DV Software:</label>
                        </div>
                        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                            <input class="form-control" name="dv_software" type="text" id="dv_software" value="<?php echo $dv_software; ?>">
                        </div>
                    </div>
                    <BR>
                    <hr>
                    <BR>
                    <div class="row">
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                            <label for="txtnombre">Razón Social Empleador:</label>
                        </div>
                        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                            <input class="form-control" name="razonsocial_empleador" type="text" id="razonsocial_empleador" value="<?php echo $razonsocial_empleador; ?>">
                        </div>       
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                            <label for="txtnombre">NIT Empleador:</label>
                        </div>
                        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                            <input class="form-control" name="nit_empleador" type="text" id="nit_empleador" value="<?php echo $nit_empleador; ?>">
                        </div>       
                    </div>
                    <BR>
                    <div class="row">
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                            <label for="txtnombre">DV empleador:</label>
                        </div>
                        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                            <input class="form-control" name="dv_empleador" type="text" id="dv_empleador" value="<?php echo $dv_empleador; ?>">
                        </div>        
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                            <label for="txtnombre">Pais Empleador:</label>
                        </div>
                        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                            <input class="form-control" name="pais_empleador" type="text" id="pais_empleador" value="<?php echo $pais_empleador; ?>">
                        </div>   
                    </div>
                    <BR>
                    <div class="row"> 
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                            <label for="txtnombre">Departamento:</label>
                        </div>
                        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                            <input class="form-control" name="dep_empleador" type="text" id="dep_empleador" value="<?php echo $dep_empleador; ?>">
                        </div>
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                            <label for="txtnombre">Municipio Empleador:</label>
                        </div>
                        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                            <input class="form-control" name="mun_empleador" type="text" id="mun_empleador" value="<?php echo $mun_empleador; ?>">
                        </div>  
                    </div>
                    <BR>
                    <div class="row"> 
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                            <label for="txtnombre">Dirección:</label>
                        </div>
                        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                            <input class="form-control" name="dir_empleador" type="text" id="dir_empleador" value="<?php echo $dir_empleador; ?>">
                        </div>
                    </div>
                    <BR>
                    <hr>
                    <BR>
                    <div class="row">      
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                            <label for="txtnombre">Correlativo Prefijo:</label>
                        </div>
                        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">

                            <input class="form-control" name="correlativo_prefijo" type="text" id="correlativo_prefijo" value="<?php echo $correlativo_prefijo; ?>">

                        </div>      
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                            <label for="txtnombre">Correlativo Contador:</label>
                        </div>
                        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">

                            <input class="form-control" name="correlativo_contador" type="text" id="correlativo_contador" value="<?php echo $correlativo_contador; ?>">

                        </div>                
                    </div>
                    <BR>
                    <hr>
                    <BR>
                    
                   <div class="row">
                        <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
                        </div>                    
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">                            
                                 <?php //boton_metronic('ok', , 2); ?>
                                 <button type="button" class="btn green active" id="guardar" onClick="Enviar(); document.formParametros.submit();">Procesar</button>
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
