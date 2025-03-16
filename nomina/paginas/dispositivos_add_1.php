<?php 
session_start();
ob_start();
	include ("../header4.php");
	include("../lib/common.php");
	include("func_bd.php");	
?>

<script>
function Enviar(){					
			
	if (document.frmPrincipal.registro_id.value==0){ 
		document.frmPrincipal.op_tp.value=1}
	else{ 
		document.frmPrincipal.op_tp.value=2}		
	
	if (document.frmPrincipal.txtdescripcion.value==0){
		document.frmPrincipal.op_tp.value=-1
		alert("Debe ingresar una descripción valida. Verifique...");}
}
</script>

<?php




?>
<body class="page-full-width"  marginheight="0">

<div class="page-container">
  <!-- BEGIN CONTENT -->
  <div class="page-content-wrapper">
    <div class="page-content">
      <!-- BEGIN PAGE CONTENT-->
      <div class="row">
        <div class="col-md-12">
          <div class="portlet box blue">
            <div class="portlet-title">
              <div class="caption">
                <?php    
                  if ($registro_id==0)
                  {
                   echo "Agregar Dispositivo";
                  }
                  else
                  {
                   echo "Modificar Dispositivo";
                  }
                ?>
              </div>
              <div class="actions">
                <a class="btn btn-sm blue"  onclick="javascript: window.location='marcaciones_list.php'">
                  <i class="fa fa-arrow-left"></i> Regresar
                </a>
              </div>
            </div>
            
        
            <div class="portlet-body">
              <form action="" method="post" name="frmPrincipal" id="frmPrincipal">
                <input name="op_tp" type="Hidden" id="op_tp" value="1">
                <input name="registro_id" type="Hidden" id="registro_id" value="<?php echo $registro_id; ?>">
                <input name="nombre_tabla" type="hidden" value="<?php echo $nombre_tabla; ?>">
                <div class="row">
                  <div class="col-md-3">Dispositivo:</div>
                  <div class="col-md-6">
                      <span id="id_emp"></span>
                    </div>
                </div>
                <br>
                <div class="row">
                  <div class="col-md-3"><label for="dispositivo">Dispositivo:</label></div>
                  <div class="col-md-6"><input class="form-control" name="dispositivo" type="text" id="dispositivo" value="<?php if ($registro_id!=0){ echo $subclave; }  ?>"></div>
                </div>
                <br>
                <div class="row">
                  <div class="col-md-3"><label for="fecha">Nombre:</label></div>
                  <div class="col-md-6"><input class="form-control" name="fecha" type="text" id="fecha" value="<?php if ($registro_id!=0){ echo $nombre; }  ?>"></div>
                </div>
                <br>
                <div class="row">
                  <div class="col-md-3"><label for="hora">Nivel 1:</label></div>
                  <div class="col-md-6"><input class="form-control" name="hora" type="text" id="hora" value="<?php if ($registro_id!=0){ echo $concepto; }  ?>"></div>
                </div>
                <br>
                <div class="row">
                  <div class="col-md-3"><label for="tipo">Tipo:</label></div>
                  <div class="col-md-6"><input class="form-control" name="tipo" type="text" id="tipo" value="<?php if ($registro_id!=0){ echo $subclave; }  ?>"></div>
                </div>
                <br>
                <div class="row">
                  <div class="col-md-3"><label for="estado">Estado:</label></div>
                  <div class="col-md-6"><input class="form-control" name="estado" type="text" id="estado" value="<?php if ($registro_id!=0){ echo $subclave; }  ?>"></div>
                </div>
                <br>
                <div class="row">
                &nbsp;
                </div>
                <div class="row">
                  <div class="col-md-offset-4 col-md-2"><?php boton_metronic('ok','Enviar(); document.frmPrincipal.submit();',2) ?></div>
                  <div class="col-md-1"> <?php boton_metronic('cancel','history.back();',2) ?> </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php   include ("../footer4.php");
 ?>
 <script type="text/javascript">
$(document).ready(function()
{
    $.get("ajax/getEmpleadosList.php",function(res)
    {
        $("#id_emp").empty();
        $("#id_emp").append(res);
        /*$("#mes").change(function()
        {
            $("#quincena_box").empty();
            mes = $("#mes").val();
            $("#generar_rpt").show();
            console.log(mes+" "+anio);
            $("#generar_rpt").on("click", function(){

                var url = "../../reportes/pdf/listado_acreedores_sanmiguelito.php?mes="+mes+"&anio="+anio; // the script where you handle the form input.
                window.open(url);
            });
        });*/
    });
});
</script>
</body>
</html>


