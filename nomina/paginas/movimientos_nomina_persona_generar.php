<?php 
require_once('../lib/database.php');
include("../lib/common.php");

$db = new Database($_SESSION['bd']);    
?>
<?php include("../header4.php"); 
include ("func_bd.php");
include ("funciones_nomina.php");
?>
<style>
.form-horizontal .control-label {
    text-align: center;
    padding-top: 7px;
}
.portlet-title{
    padding-right: 5px !important;
}

label.error {
    color: #b94a48;
}

.margin-top-20{
    margin-top: 20px
}
</style>
<div class="page-container">
    <div class="page-content-wrapper">
        <div class="page-content">
            <!-- BEGIN PAGE CONTENT-->
            <div class="row">
                <div class="col-md-12">
                    <!-- BEGIN EXAMPLE TABLE PORTLET-->
                    <div class="portlet box blue">
                        <div class="portlet-title">
                            <div class="caption">
                                Movimientos Planilla - Generar <?php echo " / Nombre ".$_GET['nombre']." 
							 - Nº: ".$_GET['ficha']." - Cedula: ".$_GET['cedula'] ?>
                            </div>
                        </div>
                        <div class="portlet-body form" id="blockui_portlet_body">

                            <form class="form-horizontal" id="form_generar" name="form_generar" method="post">
                                <div class="form-body">
                                    <div class="form-group margin-top-20">
                                        <div class="col-md-12 text-center">
                                            <strong>Presione para Generar Conceptos</strong> 
                                            <br> <br>                                            
                                            INFO: ESTA ACCIÓN REGENERA TODOS LOS CONCEPTOS. USAR CON CAUTELA
                                        </div>
                                        
                                    </div>
                                    <div id="mensaje"></div>
                                </div>

                                <div class="form-actions fluid">
                                    <div class="row">
                                    
                                    <div class=col-md-5 text-center">&nbsp;</div>
                                    
                                    <div class="col-md-1 text-center">
                                        <button type="button" class="btn btn-sm blue active" id="btn-generar">Generar</button>&nbsp;
                                    </div>

                                        <div class="col-md-1 text-center">
                                             
                                       
                                            <button type="button" class="btn btn-sm red active" id="btn-salir">Salir</button>&nbsp;
                                             
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" id="codnom" name="codnom" value="<?= $_SESSION['codigo_nomina'];?>">
                                <input type="hidden" id="nomina" name="nomina" value="<?= $_GET['nomina'];?>">
                                <input type="hidden" id="ficha" name="ficha" value="<?= $_GET['ficha'];?>">


                                <input type="hidden" id="continua_laborando" name="continua_laborando" value="<?= $_GET['continua_laborando'];?>">
                                <input type="hidden" id="ultimo_ingreso" name="ultimo_ingreso" value="<?= $_GET['ultimo_ingreso'];?>">



                            </form>
                        </div>
                    </div>
                    <!-- END EXAMPLE TABLE PORTLET-->
                </div>
            </div>
            <!-- END PAGE CONTENT-->
        </div>
    </div>
</div>
<?php include("../footer4.php"); ?>
<script src="../../includes/assets/plugins/jquery-validation/dist/jquery.validate.min.js"></script>
<script src="../../includes/assets/plugins/jquery-validation/localization/messages_es.js"></script>
<script src="../../includes/assets/plugins/jquery-validation/dist/additional-methods.min.js"></script>
<script>
$(document).ready(function(){
   
    $("#btn-salir").click(function(){
        window.close();
        win.close();
    });
    $("#btn-generar").click(function(){

        App.blockUI({
            target: '#blockui_portlet_body',
            boxed: true,
            message: 'Procesando Planilla...',
        }); 

        var codnom = $("#codnom").val();
        var nomina = $("#nomina").val();
        var ficha  = $("#ficha").val();
        var continua_laborando  = $("#continua_laborando").val();
        var ultimo_ingreso      = $("#ultimo_ingreso").val();

        $.get( "generar_movimientos_nomina.php", { codnom: codnom, nomina:nomina, ficha:ficha,continua_laborando:continua_laborando,ultimo_ingreso:ultimo_ingreso }, function(file) {
            $("#mensaje").empty();
            $("#mensaje").append(file);

            window.setTimeout(function () {
                App.unblockUI('#blockui_portlet_body');
                opener.window.location.reload();
            }, 2000, file); 

             
        }); 
        $('#btn-generar').hide();
    });
}); 
</script>
</body>
</html>