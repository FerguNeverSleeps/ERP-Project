<?php 
require_once '../../generalp.config.inc.php';
session_start();
ob_start();

require_once '../lib/common.php';
error_reporting(E_ALL ^ E_DEPRECATED);
include ("../paginas/func_bd.php");
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
               <?php echo $_SESSION["nomina"].' '; ?> - ZOHO Contable
              </div>
			  <div class="actions">

				<a class="btn btn-sm blue"  onclick="javascript: window.location='menu_ach.php?modulo=312'">
					<i class="fa fa-arrow-left"></i> Regresar
				</a>

			</div>
            </div>
           
            <div class="portlet-body">
                <form action="zoho_contable_csv.php" method="get" name="frmPrincipal" id="frmPrincipal">
                                        
					<div class="form-group">
						<!--<div class="row">
							<label class="col-md-2 control-label" for="txtcodigo">Nivel 1:</label>
							<div class="col-md-8">
								<div class="input-group">
									<span id="id_nivel1"></span>
								</div> 
							</div>
						</div>-->
						
						<div class="row">
							<label class="col-md-2 control-label" for="txtcodigo"></label>
							<div class="col-md-8">
								<div class="input-group">
									<span id="id_nomina"></span>
								</div> 
							</div>
						</div>
                                                <br>
						<div class="row">
                                                    <div class="col-md-10" style="text-align: center;">
								<button type="submit" id="bt_filtra" class="btn blue button-next">Generar</button>
							</div>
						</div>  
					</div>

                        <input name="registro_id" type="hidden" value="">
                        <input name="op" type="hidden" value="">  
						<input type="hidden" name="codtip" id="codtip" value="<?php echo $_SESSION['codigo_nomina']; ?>" >
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
<script src="../../includes/assets/plugins/jquery-validation/dist/jquery.validate.min.js"></script>
<script src="../../includes/assets/plugins/jquery-validation/localization/messages_es.js"></script>
<script src="../../includes/assets/plugins/jquery-validation/dist/additional-methods.min.js"></script>
<script type="text/javascript">
$(document).ready(function() 
{ 
    
    /*$.get("ajax/obtenerNivel1.php",function(res)
    {
        $("#id_nivel1").empty();
        $("#id_nivel1").append(res);
    });*/
    
	$.get("../paginas/ajax/obtenerNomina.php",function(res)
    {
         
        $("#id_nomina").empty();
        $("#id_nomina").append(res);
        $("#nomina").select2();
    });
                                    
});  
</script>
</body>
</html>