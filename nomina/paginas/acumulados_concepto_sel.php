<?php
date_default_timezone_set('America/Panama');
session_start();
ob_start();
require_once '../../generalp.config.inc.php';
include ("../header4.php");
require_once '../lib/common.php';
require_once('../lib/database.php');
require_once('../../configuracion/funciones_generales.php');
$db1 = new Database($_SESSION['bd']);
$db = new Database(SELECTRA_CONF_PYME);
//-----------------------------------------------------

$consulta  = "SELECT ficha, apenom FROM nompersonal";
$empleados = $db1->query($consulta);

$consulta  = "SELECT codcon,descrip FROM `nomvis_acumulados` GROUP BY codcon,descrip";
$conceptos = $db1->query($consulta);

?>

<form name="sampleform" method="post" target="_self" action="<?php echo $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data">
<div class="page-container">
  <div class="page-content-wrapper">

    <div class="row">
      <div class="col-md-12">
        <div class="portlet box blue">
          <div class="portlet-title">
            <div class="caption">
              <img src="../imagenes/21.png" width="22" height="22" class="icon"> Acumulados por concepto
            </div>
          </div>
          <div class="portlet-body">
            <div class="row">
              <div class="col-lg-12">

                <div class="col-lg-4">
                  <div class="form-group">
                    <label for="usr">Empleado:</label>

										<select name="ficha" id="ficha" class="form-control select2">
                      <option value="0">Seleccione una empleado</option>
                        <?php while ($emp=mysqli_fetch_array($empleados)): ?>
  	                      <option value="<?php echo $emp['ficha'] ?>" > <?php echo "Ficha: ".$emp['ficha'].", ".$emp['apenom'] ?> </option>
  	                    <?php endwhile ?>
										</select>
                  </div>
                </div>

	                <div class="col-lg-4">
	                  <div class="form-group">
	                    <label for="usr">Concepto:</label>
	                    <select name="concepto" id="concepto" class="form-control select2">
  	                      <option value="all">Seleccione una todos</option>
    	                    <?php while ($con=mysqli_fetch_array($conceptos)): ?>
    	                      <option value="<?php echo $con['codcon'] ?>" > <?php echo $con['descrip']." (".$con['codcon'].")" ?> </option>
    	                    <?php endwhile ?>
	                    </select>
	                  </div>
	                </div>

                <div class="col-lg-4">
                  <div class="form-group">
                    <label for="usr">Año:</label>
                    <input type="number" name="anio" id="anio" maxlength="4" class="form-control" required>
                  </div>
                </div>
								<div id="resCon">
								</div>
              </div>
            </div>
            <hr>
            <div align="right">
              <input class="btn btn-primary" id="aceptar" value="Enviar">
              <input class="btn btn-primary" id="imprimir" value="Imprimir">
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</form>

<?php include("../footer4.php"); ?>
<script src="../../includes/assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
<script src="../../includes/assets/plugins/jquery-validation/dist/jquery.validate.min.js"></script>
<script src="../../includes/assets/plugins/jquery-validation/localization/messages_es.js"></script>
<script src="../../includes/assets/plugins/fancybox/source/jquery.fancybox.pack.js"></script>
<script src="../../includes/js/scripts/momentjs/moment-with-locales.js"></script>
<script src="../../includes/js/scripts/momentjs/readable-range-es.js"></script>
<script>
$(document).ready(function(){
  $("#ficha").select2();
  $("#concepto").select2();
	$( "#aceptar" ).click(function(event){
	    $.ajax({
	      url  : 'buscar_concepto_acumulados.php',
	      type : 'GET',
	      data : {
	          ficha:$( "#ficha" ).find('option:selected').val(),
						concepto:$( "#concepto" ).val(),
						anio:$( "#anio" ).val(),
	          ajax    : true
	      },
	      success : function(response) {
	          // Limpiamos el select
						$("#resCon").empty();
						$("#resCon").append(response);
	      },
	      error : function(xhr, status) {
	          console.log(xhr);
	      },
	      complete : function(xhr, status) {
	      }
	    });
	});

	$( "#imprimir" ).click(function(event){
      var ficha=$( "#ficha" ).find('option:selected').val();
			var concepto=$( "#concepto" ).find('option:selected').val();
			var anio=$( "#anio" ).val();
      var url = "../../reportes/excel/rpt_concepto_acumulados.php?ficha="+ficha+"&concepto="+concepto+"&anio="+anio;
      window.open(url, '_blank');
	});

});
</script>
</body>
</html>
