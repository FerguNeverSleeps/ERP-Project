<?php
session_start();
ob_start();
$termino = $_SESSION['termino'];
include("../lib/common.php");
include("func_bd.php");
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
   
   
    $query = "UPDATE configuracion_sipe_css set                 
                con_sueldo                  ='$_POST[con_sueldo]',
                con_horas_extras            ='$_POST[con_horas_extras]',
                con_impuesto_renta          ='$_POST[con_impuesto_renta]',
                con_decimo_tercer_mes       ='$_POST[con_decimo_tercer_mes]',
                con_vacaciones              ='$_POST[con_vacaciones]',
                con_comisiones              ='$_POST[con_comisiones]',
                con_bonificaciones          ='$_POST[con_bonificaciones]',
                con_combustible             ='$_POST[con_combustible]',
                con_dieta                   ='$_POST[con_dieta]',
                con_salario_especies        ='$_POST[con_salario_especies]',
                con_viaticos                ='$_POST[con_viaticos]',
                con_gasto_representacion    ='$_POST[con_gasto_representacion]',
                con_impuesto_renta_gr       ='$_POST[con_impuesto_renta_gr]',
                con_decimo_tercer_mes_gr    ='$_POST[con_decimo_tercer_mes_gr]',
                con_prima_produccion        ='$_POST[con_prima_produccion]',
                con_dividendo               ='$_POST[con_dividendo]',
                con_beneficio_ingreso       ='$_POST[con_beneficio_ingreso]',
                con_gratificacion_aguinaldo ='$_POST[con_gratificacion_aguinaldo]',
                con_preaviso                ='$_POST[con_preaviso]',
                con_indemnizacion           ='$_POST[con_indemnizacion]',
                con_tardanza                ='$_POST[con_tardanza]',
                con_ausencia                ='$_POST[con_ausencia]'
                WHERE id_configuracion=1";


    $result = sql_ejecutar($query);

    echo '<br><font color="green"><strong> LA CONFIGURACION HA SIDO ACTUALIZADA. </strong></font>';
}

$query                          = "SELECT * FROM configuracion_sipe_css";
$result                         = sql_ejecutar($query);
$row                            = mysqli_fetch_array($result);
$codigo                         = $row["id_configuracion"];
$con_sueldo                     = $row["con_sueldo"];
$con_horas_extras               = $row["con_horas_extras"];
$con_impuesto_renta             = $row["con_impuesto_renta"];
$con_decimo_tercer_mes          = $row["con_decimo_tercer_mes"];
$con_vacaciones                 = $row["con_vacaciones"];
$con_comisiones                 = $row["con_comisiones"];
$con_bonificaciones             = $row["con_bonificaciones"];
$con_combustible                = $row["con_combustible"];
$con_dieta                      = $row["con_dieta"];
$con_salario_especies           = $row["con_salario_especies"];
$con_viaticos                   = $row["con_viaticos"];
$con_gasto_representacion       = $row["con_gasto_representacion"];
$con_impuesto_renta_gr          = $row["con_impuesto_renta_gr"];
$con_decimo_tercer_mes_gr       = $row["con_decimo_tercer_mes_gr"];
$con_prima_produccion           = $row["con_prima_produccion"];
$con_dividendo                  = $row["con_dividendo"];
$con_beneficio_ingreso          = $row["con_beneficio_ingreso"];
$con_gratificacion_aguinaldo    = $row["con_gratificacion_aguinaldo"];
$con_preaviso                   = $row["con_preaviso"];
$con_indemnizacion              = $row["con_indemnizacion"];
$con_tardanza                   = $row["con_tardanza"];
$con_ausencia                   = $row["con_ausencia"];

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
              <h4>SIPE CSS</h4>
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
                            <input class="form-control" name="txtcodigo" type="text" id="txtcodigo" disabled="disabled" value="<?php echo $codigo_empresa; ?>" >

                        </div>

                        
                    </div>
                    <BR>
                    <div class="row">
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                            <label for="txtnombre">SUELDO:</label>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">

                            <textarea rows="3" class="form-control" name="con_sueldo"  id="con_sueldo"> <?php echo $con_sueldo; ?> </textarea>

                        </div>                
                    </div>
                    <BR>
                    <div class="row">
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                            <label for="txtnombre">HORAS EXTRAS:</label>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">

                            <textarea rows="3" class="form-control" name="con_horas_extras" id="con_horas_extras"> <?php echo $con_horas_extras; ?></textarea>

                        </div>                
                    </div>
                    <BR>
                    <div class="row">
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                            <label for="txtnombre">ISR:</label>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">

                            <input class="form-control" name="con_impuesto_renta" type="text" id="con_impuesto_renta" value="<?php echo $con_impuesto_renta; ?>">

                        </div>                
                    </div>
                    <BR>
                    <div class="row">
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                            <label for="txtnombre">XIII MES:</label>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">

                            <input class="form-control" name="con_decimo_tercer_mes" type="text" id="con_decimo_tercer_mes" value="<?php echo $con_decimo_tercer_mes; ?>">

                        </div>                
                    </div>
                    <BR>
                    <div class="row">
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                            <label for="txtnombre">VACACIONES:</label>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">

                            <input class="form-control" name="con_vacaciones" type="text" id="con_vacaciones" value="<?php echo $con_vacaciones; ?>">

                        </div>                
                    </div>
                    <BR>
                    <div class="row">
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                            <label for="txtnombre">COMISIONES:</label>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">

                            <input class="form-control" name="con_comisiones" type="text" id="con_comisiones" value="<?php echo $con_comisiones; ?>">

                        </div>                
                    </div>
                    <BR>
                    <div class="row">
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                            <label for="txtnombre">BONIFICACIONES:</label>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">

                            <input class="form-control" name="con_bonificaciones" type="text" id="con_bonificaciones" value="<?php echo $con_bonificaciones; ?>">

                        </div>                
                    </div>
                    <BR>
                    <div class="row">
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                            <label for="txtnombre">COMBUSTIBLE:</label>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">

                            <input class="form-control" name="con_combustible" type="text" id="con_combustible" value="<?php echo $con_combustible; ?>">

                        </div>                
                    </div>
                    <BR>
                    <div class="row">
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                            <label for="txtnombre">DIETA:</label>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">

                            <input class="form-control" name="con_dieta" type="text" id="con_dieta" value="<?php echo $con_dieta; ?>">

                        </div>                
                    </div>
                    <BR>
                    <div class="row">
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                            <label for="txtnombre">SALARIO ESPECIES:</label>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">

                            <input class="form-control" name="con_salario_especies" type="text" id="con_salario_especies" value="<?php echo $con_salario_especies; ?>">

                        </div>                
                    </div>
                    <BR>
                    <div class="row">
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                            <label for="txtnombre">VIATICOS:</label>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">

                            <input class="form-control" name="con_viaticos" type="text" id="con_viaticos" value="<?php echo $con_viaticos; ?>">

                        </div>                
                    </div>
                    <BR>
                    <div class="row">
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                            <label for="txtnombre">GASTOS REPRESENTACION:</label>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">

                            <input class="form-control" name="con_gasto_representacion" type="text" id="con_gasto_representacion" value="<?php echo $con_gasto_representacion; ?>">

                        </div>                
                    </div>
                    <BR>
                    <div class="row">
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                            <label for="txtnombre">ISR GR:</label>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">

                            <input class="form-control" name="con_impuesto_renta_gr" type="text" id="con_impuesto_renta_gr" value="<?php echo $con_impuesto_renta_gr; ?>">

                        </div>                
                    </div>
                    <BR>
                    <div class="row">
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                            <label for="txtnombre">XIII MES GR:</label>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">

                            <input class="form-control" name="con_decimo_tercer_mes_gr" type="text" id="con_decimo_tercer_mes_gr" value="<?php echo $con_decimo_tercer_mes_gr; ?>">

                        </div>                
                    </div>
                    <BR>
                    <div class="row">
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                            <label for="txtnombre">PRIMA PRODUCCION:</label>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">

                            <input class="form-control" name="con_prima_produccion" type="text" id="con_prima_produccion" value="<?php echo $con_prima_produccion; ?>">

                        </div>                
                    </div>
                    <BR>
                    <div class="row">
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                            <label for="txtnombre">DIVIDENDO:</label>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">

                            <input class="form-control" name="con_dividendo" type="text" id="con_dividendo" value="<?php echo $con_dividendo; ?>">

                        </div>                
                    </div>
                    <BR>
                    <div class="row">
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                            <label for="txtnombre">BENEFICIO INGRESO:</label>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">

                            <input class="form-control" name="con_beneficio_ingreso" type="text" id="con_beneficio_ingreso" value="<?php echo $con_beneficio_ingreso; ?>">

                        </div>                
                    </div>
                    <BR>
                    <div class="row">
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                            <label for="txtnombre">AGUINALDO:</label>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">

                            <input class="form-control" name="con_gratificacion_aguinaldo" type="text" id="con_gratificacion_aguinaldo" value="<?php echo $con_gratificacion_aguinaldo; ?>">

                        </div>                
                    </div>
                    <BR>
                    <div class="row">
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                            <label for="txtnombre">PREAVISO:</label>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">

                            <input class="form-control" name="con_preaviso" type="text" id="con_preaviso" value="<?php echo $con_preaviso; ?>">

                        </div>                
                    </div>
                    <BR>
                    <div class="row">
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                            <label for="txtnombre">INDEMNIZACION:</label>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">

                            <input class="form-control" name="con_indemnizacion" type="text" id="con_indemnizacion" value="<?php echo $con_indemnizacion; ?>">

                        </div>                
                    </div>
                    <BR>
                    <div class="row">
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                            <label for="txtnombre">TARDANZA:</label>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">

                            <input class="form-control" name="con_tardanza" type="text" id="con_tardanza" value="<?php echo $con_tardanza; ?>">

                        </div>                
                    </div>
                    <BR>
                    <div class="row">
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                            <label for="txtnombre">AUSENCIA:</label>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">

                            <input class="form-control" name="con_ausencia" type="text" id="con_ausencia" value="<?php echo $con_ausencia; ?>">

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
