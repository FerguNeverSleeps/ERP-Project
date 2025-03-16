<?php 
require_once '../generalp.config.inc.php';
session_start();
ob_start();

// error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
include("../nomina/lib/common.php");
date_default_timezone_set('America/Panama');
require_once('../nomina/lib/database.php');
//require ('numeros_letras_class.php');
$db = new Database($_SESSION['bd']);

//$sql = "SELECT * FROM departamento";
$sql = "SELECT * FROM nomnivel1";
$res = $db->query($sql);


?>

<?php include("../includes/dependencias.php"); // <html><head></head><body> ?>
<meta charset="utf-8">
<link href="../includes/assets/css/custom-datatables.css" rel="stylesheet" type="text/css"/>

<!-- BEGIN PAGE CONTENT-->
<!-- BEGIN EXAMPLE TABLE PORTLET-->



<br>
<br>
<br>

<div class="row">
    <div class="col-md-offset-2 col-md-8">
        <div class="portlet box blue">
          <div class="portlet-title">
            <div class="caption">
              Exportar funcionarios por a Excel
            </div>
          </div>
          <div class="portlet-body form" id="blockui_portlet_body">

            <form action="excel/excel_funcionario_region.php" class="form-horizontal" id="form_excel" name="form_excel" method="post" enctype="multipart/form-data">
              <div class="form-body">

                <div class="form-group" style="margin-top: 20px">
                  <label class="col-md-2 control-label">Region</label>
                  <div class="col-md-9">

                    <select name="codnivel1" id="codnivel1" class="form-control select2me" data-placeholder="Seleccione <?php echo $empresa->nomniv1; ?>">
                      <?php
                        echo "<option value=''>Seleccione ".$empresa->nomniv1."</option>";
                        echo "<option value='-1'>TODAS LAS REGIONES</option>";
                        while($fila = $res->fetch_assoc())
                        {
                          echo "<option value='{$fila['codorg']}'>".$fila['descrip']."</option>";
                        }
                      ?>
                    </select>             
                  </div>
                </div>
              </div>

              <div class="form-actions fluid">
                <div class="row">
                  <div class="col-md-11 text-center">
                    <!--<div class="col-md-offset-4 col-md-8">-->
                      <button type="button" class="btn btn-sm blue active" id="btn-exportar">Exportar</button>&nbsp;
                    <!--</div>-->
                    <?php
                      if(isset($_GET['download']) && isset($_GET['filename']))
                      { 
                      ?>
                        <a href="<?php echo $_GET['filename']; ?>" class="btn btn-sm red" id="a_download">
                        <i class="fa fa-download"></i> Descargar</a>
                      <?php
                      }
                    ?>  
                  </div>
                </div>
              </div>

            </form>

          </div>
        </div>
        <!-- END EXAMPLE TABLE PORTLET-->


    </div>
    

</div>
<!-- END PAGE CONTENT-->

<?php //include("../nomina/footer4.php"); ?>

<script type="text/javascript">

   $(document).ready(function(){
    if( $("#a_download").length ) 
    {
      document.getElementById("a_download").click();
    }

    $("#btn-exportar").click(function(){



        var codnivel1 = $("#codnivel1").val();




         $.blockUI({
             message: 'Procesando...',
         }); 

        $.post( "excel/excel_funcionario_region.php", { codnivel1: codnivel1 }, function(file) {
          console.log(file);
           window.setTimeout(function () {
              $.unblockUI('#blockui_portlet_body');
                  
                 $("#codnivel1").select2("val", "");
                 console.log('exportar_empleados_excel.php?download&filename'+file);
                 location.href = 'excel/'+file;
           }, 2000, file); 
      }); 
    });
   });  

</script>
</body>
</html>