<?php 
require_once '../../generalp.config.inc.php';
session_start();
ob_start();

//error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require_once '../lib/common.php';
include ("func_bd.php");
//$conexion=conexion();
$id_pagina=$_GET['id_pagina'] ? $_GET['id_pagina'] : ''; 

if ($op==3) //Se presiono el boton de Eliminar
{ 
  $query  = "DELETE FROM nom_modulos WHERE cod_modulo=$registro_id";     
  $result = sql_ejecutar($query);
  activar_pagina("reportes_crud_list.php");          
}    
if($op == 1)
{
   
    activar_pagina("ag_reportes_crud.php"); 
}
$strsql = "SELECT np.* FROM nom_paginas np LEFT JOIN  nom_modulos nm ON np.cod_modulo = nm.cod_modulo WHERE np.id_pagina = '{$id_pagina}';";
$result = sql_ejecutar($strsql);
$paginas = fetch_array($result);
?>
<?php include("../header4.php"); // <html><head></head><body> ?>
<link href="../../includes/assets/css/custom-datatables.css" rel="stylesheet" type="text/css"/>
<style>
    .mb15{
        margin-bottom: 15px;
        margin-top: 15px;
    }

/* Feedback states */
input.form-control.has-info  {
  border-color: #157fcc;
  -webkit-box-shadow: none;
  box-shadow: none;
}
input.form-control.has-info:focus {
  border-color: #157fcc;
  -webkit-box-shadow: none;
  box-shadow: none;
}
</style>
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
               Reportes
              </div>
              <div class="actions">
                <a class="btn btn-sm blue"  onclick="javascript: window.location='reportes_crud_list.php'">
                  <i class="fa fa-arrow-left"></i> Regresar
                </a>
              </div>
            </div>
            <div class="portlet-body">
              <form action="" method="post" name="frmReportes" id="frmReportes">
                  <div class="row mb15">
                      <div class="col-md-3 text-right">Modulo Padre</div>
                      <div class="col-md-3 text-left has-info"><select id="moduloPadre" class="form-control has-info">
                      <option value="45">Reportes</option>
                      <option value="294">Integrantes (Personal</option>
                      <option value="295">Configuración</option>
                      </select></div>

                  </div>
                  <div class="row mb15">
                      <div class="col-md-3 text-right">Descripción</div>
                      <div class="col-md-6 text-left"><input class="form-control has-info" placeholder="Descripción" id="descripcion" name="descripcion" value="<?= utf8_encode($paginas["descripcion"]); ?>"></div>
                  </div>
                  <div class="row mb15">
                      <div class="col-md-3 text-right">Archivo</div>
                      <div class="col-md-6 text-left"><input class="form-control has-info" placeholder="Archivo" id="archivo" name="archivo" value="<?= $paginas["archivo"]; ?>"></div>
                  </div>
                  <div class="row mb15">
                      <div class="col-md-3 text-right">Activo</div>
                      <div class="col-md-6 text-left"><input class="form-control has-info" placeholder="Activo" id="activo" name="activo" value="<?= $paginas["activo"]; ?>"></div>
                  </div>
                  <div class="row mb15">
                      <div class="col-md-3 text-right">Icono</div>
                      <div class="col-md-6 text-left"><input class="form-control has-info" placeholder="Icono" id="icono" name="icono" value="<?= $paginas["icono"]; ?>"></div>
                  </div>
                  <div class="row mb15">
                      <div class="col-md-3 text-right">Orden</div>
                      <div class="col-md-6 text-left"><input class="form-control has-info" placeholder="Orden" id="orden" name="orden" value="<?= $paginas["orden"]; ?>"></div>
                  </div>
                  <div class="row mb15">
                      <div class="col-md-5 text-right">&nbsp;</div>
                      <div class="col-md-6 text-left"><a id="btnEditar" class="btn btn-success" >Editar</a></div>
                  </div>
              
                  <input name="registro_id" type="hidden" value="<?= $id_pagina ?>">
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
<script type="text/javascript">
$(document).ready(function() {
    $("#btnEditar").off().on("click", function(){
        
        var datos = new FormData();
        datos.append("accion", "EditarReporte" );

        datos.append("cod_modulo", $("#moduloPadre :selected").val() );
        datos.append("descripcion", $("#descripcion").val() );
        datos.append("archivo", $("#archivo").val() );
        datos.append("activo", $("#activo").val() );
        datos.append("icono", $("#icono").val() );
        datos.append("orden", $("#orden").val() );
        datos.append("id_pagina", $("#id_pagina").val() );
        if($("#descripcion").val() == "")
        {
            alert("Escriba la descripcion");
        }
        if($("#archivo").val() == "")
        {
            alert("Escriba la ruta del archivo");
        }
        if($("#activo").val() == "")
        {
            alert("Escriba si está activo");
        }
        $.ajax({
            url : "ajax/ajaxReportes.php",
            method: "POST",
            data: datos,
            cache: false,
            contentType : false,
            processData : false,
            dataType : "json" ,
            success: function(resp){
                if(resp["success"] == 1){
                    alert(resp["mensaje"]);
                }else{
                    alert(resp["mensaje"]);
                }    
                window.location.href = "ag_reportes_crud.php";
            }
        });
    });
});
</script>
<?php include("../footer4.php"); ?>
</body>
</html>