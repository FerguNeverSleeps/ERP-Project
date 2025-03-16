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
$config_rpt_id=(isset($_GET['config_rpt_id'])?$_GET['config_rpt_id']:$_POST['config_rpt_id']);
$op_tp = $_POST['op_tp'];

$query      ="SELECT * FROM config_reportes_planilla where id = '$config_rpt_id'";
$result     = sql_ejecutar($query);
$config_rpt = mysqli_fetch_array($result);
$tipo_consulta = (isset( $config_rpt['tipo_consulta'])) ?  $config_rpt['tipo_consulta'] : 1;

$query1      ="SELECT * FROM config_reportes_planilla_columnas where id_reporte = '$config_rpt_id' order by col_orden";

if ($op_tp == 2) {
    $result2     = sql_ejecutar($query1);

    $sql_reporte="";
    if( $tipo_consulta == 1)
        $sql_reporte = "SELECT a.codnom,a.tipnom,a.ficha,b.apenom,b.cedula,c.codorg,c.descrip,gastos_admon, ";
    else if( $tipo_consulta == 2)
        $sql_reporte = "SELECT a.apenom,a.ficha,a.cedula,c.codorg,c.descrip, ";

    while ( $inputs = mysqli_fetch_array($result2) ) {
        
        $id = $inputs['id'];
        $col = $inputs['nombre_corto'];
        $tip = $inputs['tipo'];
        $conceptos = trim($_POST[$col]);

        $add_update="";
        if(isset($_REQUEST["col_letra_{$id}"])){
            $add_update.=", col_letra='".$_REQUEST["col_letra_{$id}"]."'";
        }
        if(isset($_REQUEST["formula_valor_{$id}"])){
            $add_update.=", formula_valor='".$_REQUEST["formula_valor_{$id}"]."'";
        }

        $q = "UPDATE config_reportes_planilla_columnas set conceptos ='$conceptos' $add_update WHERE id=$id";

        //$sql_reporte .= " COALESCE(( SELECT n.monto FROM nom_movimientos_nomina n WHERE n.codcon in ($conceptos) ),0) as $col,";
        if( $tip == 1){
            $sql_reporte .= " COALESCE(( SUM(IF(a.codcon in ($conceptos),a.monto,0)) ),0) as $col,";
        }elseif( $tipo_consulta == 1){
            $sql_reporte .= " COALESCE(( SUM(IF(a.codcon in ($conceptos),a.monto,0)) ),0) as $col,";
        }else{
            $select = $inputs['select'];
            if($select == '' || $select == null){
                $sql_reporte .= " COALESCE(IF(b.id = $conceptos,b.valor,0)) as $col,";
            }else{
                if($col == 'concepto_ahorro'){
                    $select = str_replace("replace","n.codcon=$conceptos and n.ficha= replacenom ",$select);//n.codcon = 590 and n.ficha=14 AND codnom=1
                }else{
                    $select = str_replace("replace","na.id=$conceptos and na.ficha= replacecam ",$select);
                }
                $sql_reporte .= " COALESCE((".$select."),0) as $col,";

            }
        }
        $res = sql_ejecutar($q);
    }
    $sql_reporte .= "..";
    $sql_reporte = str_replace(",..","",$sql_reporte);
    
    if( $tipo_consulta == 1){
        $sql_reporte .= " FROM nom_movimientos_nomina a 
        LEFT JOIN nompersonal b ON a.ficha=b.ficha 
        LEFT JOIN nomnivel1 c ON b.codnivel1=c.codorg 
        WHERE replace
        GROUP BY a.codnom,a.ficha 
        ORDER BY c.codorg,b.apenom"; 

        $sql = "UPDATE config_reportes_planilla SET sql_reporte = '$sql_reporte' WHERE id = $config_rpt_id";
        $res = sql_ejecutar($sql);
    }
    else if( $tipo_consulta == 2){
        $sql_reporte .= " FROM nompersonal a 
        LEFT JOIN nomcampos_adic_personal b on a.ficha=b.ficha
        LEFT JOIN nomnivel1 c ON a.codnivel1=c.codorg 
        LEFT JOIN nom_movimientos_nomina d ON a.ficha=d.ficha 
        WHERE replace
        GROUP BY a.ficha";

        $sql = "UPDATE config_reportes_planilla SET sql_reporte = '$sql_reporte' WHERE id = $config_rpt_id";
        $res = sql_ejecutar($sql);
    }
    else if( $tipo_consulta == 3){
        if(isset($_REQUEST["sql_reporte"])){
            $sql_reporte=$_REQUEST["sql_reporte"];
            $sql = "UPDATE config_reportes_planilla SET sql_reporte = \"$sql_reporte\" WHERE id = $config_rpt_id";
            $res = sql_ejecutar($sql);            
        }
    }  
    
    $query      ="SELECT * FROM config_reportes_planilla where id = '$config_rpt_id'";
    $result     = sql_ejecutar($query);
    $config_rpt = mysqli_fetch_array($result);
    $tipo_consulta = (isset( $config_rpt['tipo_consulta'])) ?  $config_rpt['tipo_consulta'] : 1;


    //print_r($_REQUEST);
    //echo $sql_reporte;exit;
    echo "<br><font color='green'><strong> LA CONFIGURACION HA SIDO ACTUALIZADA. </strong></font>";
}


$result1     = sql_ejecutar($query1);
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
                    <h4>Configuracion de <?php echo $config_rpt['descrip']; ?></h4>
                </div>
                <div class="actions">
                    <a class="btn btn-sm blue"  onclick="javascript: window.location='config_reportes_planilla.php'">
                        <i class="fa fa-arrow-left"></i> Regresar
                    </a>
                </div>
            </div>
            <div class="portlet-body">
            <form action="config_reportes_planilla_columnas.php" enctype="multipart/form-data" method="post" name="frmEmpresas" id="frmEmpresas" role="form">
                <input name="op_tp" type="Hidden" id="op_tp" value="-1">
                <input name="registro_id" type="Hidden" id="registro_id" value="<?php echo $_POST[registro_id]; ?>">
                <div class="form-body">
                    <input type = "hidden" name="config_rpt_id" id="config_rpt_id" value="<?php echo $config_rpt['id']; ?>" >
                    <?php if($tipo_consulta==3):?>
                    <label style="font-weight: 600;">Consulta Principal</label>
                    <textarea class="form-control" name="sql_reporte" style="height: 200px; font-family: monospace; font-size: 12px;"><?php print $config_rpt["sql_reporte"]?></textarea>
                    <br>
                    <?php 
                    endif;
                    if(mysqli_num_rows($result1)>0):
                    ?>
                    <br>

                    <div class="row" style="font-weight: 600; font-size: 10px; text-align: center;">
                        <div class="col-xs-2" >Nombre</div>
                        <div class="col-xs-10" style="display: flex; align-items: center;">
                            <div style="width: 50px; text-align: center; padding: 0;">Columna</div>
                            <div style="flex:1;">Conceptos</div>
                            <div style="flex:0.6;">Formula/Valor</div>
                        </div>
                    </div>
                    <?php 
                    endif;
                    while ( $columna = mysqli_fetch_array($result1) ) { 
                        if(!mb_detect_encoding($columna['nombre'],["UTF-8"],true))
                            $columna['nombre']=utf8_encode($columna['nombre']);
                    ?>
                        <div class="row">
                            <div class="col-xs-2">
                                <label style="margin-bottom: 0px;" for="<?= $columna['nombre_corto']; ?>"><?= $columna['nombre']; ?>:</label><br>
                                <span style="font-size: 10px; color: gray;"><?= $columna['nombre_corto']; ?></span>
                            </div>
                            <div class="col-xs-10" style="display: flex; align-items: stretch;">
                                <input type="text" class="form-control" style="width: 50px; text-align: center; padding: 0;height: auto;" value="<?= $columna['col_letra']; ?>"  name='col_letra_<?= $columna['id']; ?>'/>
                                <?php if( $columna['textarea_rows']<=1):?>
                                    <input type="text" class="form-control" name="<?= $columna['nombre_corto']; ?>"  id="<?= $columna['nombre_corto']; ?>" value="<?= $columna['conceptos']; ?>" style='flex:1;'>
                                <?php else:?>
                                    <textarea rows="<?= $columna['textarea_rows']; ?>" class="form-control" name="<?= $columna['nombre_corto']; ?>"  id="<?= $columna['nombre_corto']; ?>" style='flex:1;'><?= $columna['conceptos']; ?></textarea>
                                <?php endif;?>
                                <input type="text" class="form-control" style="flex: 0.6;height: auto;" value="<?= $columna['formula_valor']; ?>" name='formula_valor_<?= $columna['id']; ?>' />
                            </div>                
                        </div>
                        <br>
                    <?php } ?>
                    
                    <div class="row">
                        <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
                        </div>                    
                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">                            
                            <?php boton_metronic('ok', 'Enviar(); document.frmEmpresas.submit();', 2) ?>
                        </div>                    

                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                            
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
