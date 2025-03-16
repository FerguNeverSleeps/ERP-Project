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
               <?php echo $_SESSION['termino'].' '; ?>Quincenal
              </div>
			  <div class="actions">

				<a class="btn btn-sm blue"  onclick="javascript: window.location='submenu_reportes.php?modulo=45'">
					<i class="fa fa-arrow-left"></i> Regresar
				</a>

			</div>
            </div>
           
            <div class="portlet-body">
                <form action="horizontal_nomina_xls_modelo5.php" method="get" name="frmPrincipal" id="frmPrincipal">
                                        
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
							<label class="col-md-2 control-label" for="txtcodigo"><?php echo $_SESSION['termino'].' '; ?></label>
							<div class="col-md-8">
								<div class="input-group">
									<span id="id_nomina"></span>
								</div> 
							</div>
						</div>
						<div class="row">
							<div class="col-md-5">
								<button type="submit" id="bt_filtra" class="btn blue button-next">Buscar</button>
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
<script type="text/javascript">
$(document).ready(function() 
{ 
    
    /*$.get("ajax/obtenerNivel1.php",function(res)
    {
        $("#id_nivel1").empty();
        $("#id_nivel1").append(res);
    });*/
    
	$.get("ajax/obtenerNomina.php",function(res)
    {
        $("#id_nomina").empty();
        $("#id_nomina").append(res);
    });
                                    
});  
</script>
</body>
</html>