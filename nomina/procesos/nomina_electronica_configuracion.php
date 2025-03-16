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

<?php

$op_tp = $_REQUEST['op_tp'];


$query1      ="SELECT * FROM nomina_electronica_configuracion "
            . "ORDER by tipo ASC, nombre ASC";

if ($op_tp == 2) 
{
    $result2     = sql_ejecutar($query1);

   

    while ( $inputs = mysqli_fetch_array($result2) ) {
        
        $id = $inputs['id_configuracion'];
        $col = $inputs['nombre_corto'];
        $tip = $inputs['tipo'];
        $conceptos = trim($_POST[$col]);

        $add_update="";
        

        $q = "UPDATE nomina_electronica_configuracion set conceptos ='$conceptos' $add_update WHERE id_configuracion=$id";

       
        $res = sql_ejecutar($q);
    }
    

    //print_r($_REQUEST);
    //echo $sql_reporte;exit;
    echo "<br><font color='green'><strong> Configuración de Conceptos Actualizada. </strong></font>";
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
                      <img src="../imagenes/21.png" width="20" height="20" class="icon"> Nomina Electronica / Configuracion
                    </div>
                    <div class="actions">

                            
                            <a class="btn btn-sm blue"  onclick="javascript: window.location='nomina_electronica_menu.php'">
                                    <i class="fa fa-arrow-left"></i> Regresar
                            </a>
                    </div>
            </div>
            <div class="portlet-body">
            <form action="nomina_electronica_configuracion.php" enctype="multipart/form-data" method="post" name="frmEmpresas" id="frmEmpresas" role="form">
                <input name="op_tp" type="Hidden" id="op_tp" value="-1">
                <input name="registro_id" type="Hidden" id="registro_id" value="<?php echo $_POST[registro_id]; ?>">
                <div class="form-body">
                    <?php

                    $DATA_DEVENGADOS=[];
                    $DATA_DEDUCCIONES=[];
                    while($row = mysqli_fetch_array($result1) ) { 
                        if(!mb_detect_encoding($row['nombre'],["UTF-8"],true))
                            $row['nombre']=utf8_encode($row['nombre']);
                        if($row["tipo"]=="2")
                            $DATA_DEVENGADOS[]=$row;
                        else
                            $DATA_DEDUCCIONES[]=$row;
                    }
                    ?>
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#tab_devengados" id="tab-devengados" data-toggle="tab">Devengados</a></li>
                        <li><a href="#tab_deducciones" id="tab-deducciones" data-toggle="tab">Deducciones</a></li>                                
                    </ul>
                    <div class="tab-content">                                                              
                        <div class="tab-pane active" id="tab_devengados">                           
                            <br>
                            <div class="row" style="font-weight: 600; font-size: 10px; text-align: center;">
                                <div class="col-xs-2" style="align-items: center;text-align: center;">Nombre</div>
                                <div class="col-xs-10" style="display: flex; align-items: center;">
                                    <div style="width: 50px; text-align: center; padding: 0;">Columna</div>
                                    <div style="flex:1;">Conceptos</div>
                                    <div style="flex:0.6;">Formula/Valor</div>
                                </div>
                            </div>
                            <?php                             
                            for($i=0; $i<count($DATA_DEVENGADOS); $i++): 
                                $columna=$DATA_DEVENGADOS[$i];
                            ?>
                                <div class="row">                                   
                                    <div class="col-xs-2">
                                        <label style="margin-bottom: 0px;" for="<?= $columna['nombre_corto']; ?>"><?= $columna['nombre']; ?></label><br>
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
                            <?php
                            endfor;
                            ?>
                        </div>
                        <div class="tab-pane" id="tab_deducciones">
                            <br>
                            <div class="row" style="font-weight: 600; font-size: 10px; text-align: center;">
                                <div class="col-xs-2" style="align-items: center;text-align: center;">Nombre</div>
                                <div class="col-xs-10" style="display: flex; align-items: center;">
                                    <div style="width: 50px; text-align: center; padding: 0;">Columna</div>
                                    <div style="flex:1;">Conceptos</div>
                                    <div style="flex:0.6;">Formula/Valor</div>
                                </div>
                            </div>
                            <?php                             
                            for($i=0; $i<count($DATA_DEDUCCIONES); $i++): 
                                $columna=$DATA_DEDUCCIONES[$i];
                            ?>
                                <div class="row">                                   
                                    <div class="col-xs-2">
                                        <label style="margin-bottom: 0px;" for="<?= $columna['nombre_corto']; ?>"><?= $columna['nombre']; ?></label><br>
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
                            <?php
                            endfor;
                            ?>
                        </div>
                    </div>                    
                    
                    
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
