<?php 
require_once '../../generalp.config.inc.php';
session_start();
ob_start();

require_once '../lib/common.php';
error_reporting(E_ALL ^ E_DEPRECATED);
include ("func_bd.php");
//$conexion    = new bd( $_SESSION['bd']);



?>
<?php include("../header4.php"); // <html><head></head><body> ?>
<link href="../../includes/assets/css/custom-datatables.css" rel="stylesheet" type="text/css"/>
<!--
<script type="text/javascript" src="ewp.js"></script>
-->

<div class="page-container">
  <!-- BEGIN SIDEBAR -->
  <!-- END SIDEBAR -->
  <!-- BEGIN CONTENT -->
  <div class="page-content-wrapper">
    <div class="page-content">
      <!-- BEGIN PAGE CONTENT-->
      <div class="row">
        <div class="col-md-12">
          <!-- BEGIN EXAMPLE TABLE PORTLET-->
          <div class="portlet box blue">
            <div class="portlet-title">
              <div class="caption">
               Asistencias perfectas
              </div>
            </div>
           
            <div class="portlet-body">
                <form action="asistencias_perfectas.php" method="post" name="frmPrincipal" id="frmPrincipal">
                    <div class="row">
                        <div class="col-md-10">                        
                            <div class="form-group">
                               <div class="row">
                                    <label class="col-md-2 control-label" for="txtcodigo">Nivel 1:</label>
                                    <div class="col-md-8">
                                        <div class="input-group">
                                           <span id="id_nivel1"></span>
                                        </div> 
                                    </div>
                               </div>
                                <br>
                                <div class="row" style="vertical-align: text-top;">
                                    <label class="col-md-2 control-label" for="txtcodigo">Fecha Inicio:</label>
                                    <div class="col-md-3">
                                        <div class="input-group date date-picker" data-provide="datepicker" data-date-format="dd/mm/yyyy">
                                            <input size="10" type="text" name="fecha_inicio" class="form-control" placeholder="(dd/mm/aaaa)" id="fecha_inicio" value="">
                                            <span class="input-group-btn">
                                                <button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
                                            </span>
                                        </div>
                                    </div>


                                    <label class="col-md-2 control-label" for="txtcodigo">Fecha Fin: </label>
                                    <div class="col-md-3">
                                        <div class="input-group date date-picker" data-provide="datepicker" data-date-format="dd/mm/yyyy">
                                            <input size="10" type="text" class="form-control" placeholder="(dd/mm/aaaa)" name="fecha_fin" id="fecha_fin" value="">
                                            <span class="input-group-btn">
                                                <button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
                                            </span>    
                                        </div>
                                    </div>
                                    <button type="submit" id="bt_filtra" class="btn btn-sm btn-default">Buscar</button>
                                </div>
                            </div>  
                        </div>
                    </div>             

                        <input name="registro_id" type="hidden" value="">
                        <input name="op" type="hidden" value="">  
              </form>
            </div>
          </div>
          <!-- END EXAMPLE TABLE PORTLET-->
        </div>
      </div>
      <!-- END PAGE CONTENT-->
    </div>
  </div>
  <!-- END CONTENT -->
</div>
<?php include("../footer4.php"); ?>
<script type="text/javascript">
$(document).ready(function() 
{ 
    
    $.get("ajax/obtenerNivel1.php",function(res)
    {
        /*$("#id_empleado").select2({
                                language: "es",              
                                 allowClear: true,
                            });*/
        $("#id_nivel1").empty();
        $("#id_nivel1").append(res);
    });
    
    
                                    
});  
</script>
</body>
</html>