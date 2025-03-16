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
               Hoja de Tiempo
              </div>
			  <div class="actions">

				<a class="btn btn-sm blue"  onclick="javascript: window.location='submenu_reportes_asistencia.php?modulo=396'">
					<i class="fa fa-arrow-left"></i> Regresar
				</a>

			</div>
            </div>
           
            <div class="portlet-body">
                <form action="" method="post" name="frmPrincipal" id="frmPrincipal">
                                        
					<div class="form-group">
						<div class="row">&nbsp;</div>
                                                <div class="row">
                                                        <div class="form-group">
                                                                <label class="text-left col-xs-offset-1 col-xs-2 col-sm-offset-1 col-sm-2 col-md-offset-1
                                                                col-md-2 col-lg-offset-1 col-lg-2 control-label">Nivel (1):</label>
                                                                <div class="col-md-8">
                                                                        <select name="nivel1" id="nivel1" class="form-control">
                                                                                <option value="">Seleccione...</option>
                                                                                <?php 
                                                                                        $sentencia = "SELECT * FROM  nomnivel1 "
                                                                                                    . "ORDER BY codorg ASC";
                                                                                        $resultado = $db->query($sentencia);
                                                                                        while ($filas = $resultado->fetch_object()) 
                                                                                        {
                                                                                                echo "<option value='".$filas->codorg."'>".$filas->codorg." - ".$filas->descrip."</option>";
                                                                                        }

                                                                                        ?>

                                                                                <?php ?>
                                                                        </select>
                                                                </div>
                                                        </div><p></p>
                                                </div>
                                                <div class="row">&nbsp;</div>
                                                <div class="row">
                                                        <div class="form-group">
                                                                <label class="text-left col-xs-offset-1 col-xs-2 col-sm-offset-1 col-sm-2 col-md-offset-1
                                                                col-md-2 col-lg-offset-1 col-lg-2 control-label">Colaborador:</label>
                                                                <div class="col-md-8">
                                                                        <select name="colaborador" id="colaborador" class="form-control">
                                                                                <option value="">Seleccione...</option>
                                                                                <?php 
                                                                                        $sentencia = "SELECT personal_id, cedula, ficha, apenom "
                                                                                                    . "FROM  nompersonal "
                                                                                                    . "ORDER BY ficha  ASC";
                                                                                        $resultado = $db->query($sentencia);
                                                                                        while ($filas = $resultado->fetch_object()) 
                                                                                        {
                                                                                                echo "<option value='".$filas->ficha."'>".$filas->ficha." - ".$filas->apenom."</option>";
                                                                                        }

                                                                                        ?>

                                                                                <?php ?>
                                                                        </select>
                                                                </div>
                                                        </div><p></p>
                                                </div>
                                                <div class="row">&nbsp;</div>
                                                <div class="row">
                                                        <label class="text-left col-xs-offset-1 col-xs-2 col-sm-offset-1 col-sm-2 col-md-offset-1
                                                                col-md-2 col-lg-offset-1 col-lg-2 control-label">Fecha Inicio:</label>
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
                                                </div>
                                                
                                                <p></p>

                                                <div class="form-actions fluid">
                                                        <div class="row">
                                                                <div class="col-md-11 text-center">
                                                                        <button type="submit" class="btn btn-sm green active" id="btn-procesar">Procesar</button>&nbsp;

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
    
function abrirVentana(pagina, alto, ancho, left=0, top=0) 
{
    window.open(pagina, 'mainWindow', 'width='+ ancho +', height='+ alto +', left='+ left +', top='+top);
}

$(document).ready(function() 
{ 
    $("#nivel1").select2();
    $("#colaborador").select2();
    
    $("#btn-procesar").click(function(){	

	    var nivel1          = $("#nivel1").val();
            var colaborador     = $("#colaborador").val();
            var fecha_inicio    = $("#fecha_inicio").val();
            var fecha_fin       = $("#fecha_fin").val();
//            alert(nivel1);
//	   	if(!nivel1)
//	   	{
//	   		alert('Debe seleccionar al menos una planilla');
//	   		return false;
//	   	}

		abrirVentana('../tcpdf/reportes/pdf_hoja_tiempo.php?nivel1='+nivel1+'&colaborador='+colaborador+'&fecha_inicio='+fecha_inicio+'&fecha_fin='+fecha_fin, 660, 1200);
	});
    
                                    
});  


</script>
</body>
</html>