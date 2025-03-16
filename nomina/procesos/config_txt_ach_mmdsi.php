<?php 
require_once('../lib/database.php');

$db = new Database($_SESSION['bd']);	
?>
<?php include("../header4.php"); ?>
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
               <?php echo $_SESSION["nomina"].' '; ?> - ACH BANCO GENERAL - MAS ME DAN SERVICIOS INTEGRALES
              </div>
			  <div class="actions">

				<a class="btn btn-sm blue"  onclick="javascript: window.location='menu_ach.php?modulo=312'">
					<i class="fa fa-arrow-left"></i> Regresar
				</a>

			</div>
            </div>
           
            <div class="portlet-body">
                <form action="txt_ach_general_mmdsi.php" method="get" name="frmPrincipal" id="frmPrincipal">
                                        
					<div class="form-group">
						<div class="row">&nbsp;</div>
                                                <div class="row">
                                                        <div class="form-group">
                                                                <label class="text-right col-xs-offset-1 col-xs-3 col-sm-offset-1 col-sm-3 col-md-offset-1
                                                                col-md-3 col-lg-offset-1 col-lg-3 control-label">Planilla:</label>
                                                                <div class="col-md-6">
                                                                        <select name="codnom" id="codnom" class="form-control">
                                                                                <option value="">Seleccione...</option>
                                                                                <?php 
                                                                                        $sentencia = "SELECT codnom, descrip, codtip FROM   nom_nominas_pago WHERE codtip='".$_SESSION['codigo_nomina']."'";
                                                                                        $resultado = $db->query($sentencia);
                                                                                        while ($filas = $resultado->fetch_object()) 
                                                                                        {
                                                                                                echo "<option value='".$filas->codnom."'>".$filas->descrip."</option>";
                                                                                        }

                                                                                        ?>

                                                                                <?php ?>
                                                                        </select>
                                                                </div>
                                                        </div><p></p>
                                                </div>
                                                <p></p>

                                                <div class="form-actions fluid">
                                                        <div class="row">
                                                                <div class="col-md-11 text-center">
                                                                        <button type="submit" class="btn btn-sm green active" id="btn-exportar">Procesar</button>&nbsp;

                                                                </div>
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
    $("#codnom").select2();
    
                                    
});  
</script>
</body>
</html>