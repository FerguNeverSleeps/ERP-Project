<?php
session_start();
//DECLARACION DE LIBRERIAS
require_once '../lib/common.php';
require_once '../paginas/func_bd.php';
include ("../paginas/funciones_nomina.php");
//include ("../header.php");

include ("../header4.php");
?>
<SCRIPT  language="JavaScript" type="text/javascript" src="../lib/common.js?<?php echo time(); ?>"></SCRIPT>
<link rel="stylesheet" type="text/css" href="../css/jquery-ui-1.7.2.custom.css" />
<!-- BEGIN PAGE LEVEL STYLES -->
<link rel="stylesheet" type="text/css" href="../../includes/assets/plugins/select2/select2.css"/>
<link rel="stylesheet" type="text/css" href="../../includes/assets/plugins/select2/select2-metronic.css"/>
<!-- END PAGE LEVEL SCRIPTS -->
<!--<script type="text/javascript" src="../lib/jquery.js"></script> -->
<!--<script type="text/javascript" src="../lib/jquery-1.3.2.min.js"></script>-->
<script src="../../includes/assets/plugins/jquery-1.10.2.min.js" type="text/javascript"></script>
<script type="text/javascript" src="../lib/jquery-ui.min.js"></script>

<script src="../../lib/jquery.maskedinput.js" type="text/javascript"></script>
<script src="../../includes/assets/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<!-- BEGIN PAGE LEVEL PLUGINS -->
<script src="../../includes/assets/plugins/jquery-validation/dist/jquery.validate.min.js" type="text/javascript"></script>
<script type="text/javascript" src="../../includes/assets/plugins/select2/select2.min.js"></script>
<script type="text/javascript" src="../../includes/assets/plugins/select2/select2_locale_es.js"></script>
<!-- END PAGE LEVEL PLUGINS -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="../../includes/assets/scripts/core/app.js" type="text/javascript"></script>
<script src="../../includes/assets/plugins/jquery-validation/dist/jquery.validate.min.js"></script>
<script src="../../includes/assets/plugins/jquery-validation/localization/messages_es.js"></script>
<script src="../../includes/assets/plugins/fancybox/source/jquery.fancybox.pack.js"></script>
<script src="../../includes/js/scripts/momentjs/moment-with-locales.js"></script>
<script src="../../includes/js/scripts/momentjs/readable-range-es.js"></script>
<script src="lib/jquery.price_format.2.0.js"></script>
<script src="../lib/moment-with-locales.js"></script>

<script type="text/javascript">

function confirmar_reporte(msg)
{
	
	if(confirm(msg) == true) 
	{            
            var cedula = document.getElementById('funcionario').value;
            AbrirVentana('pdf/reporte_periodos_vacaciones_pdf.php?cedula='+cedula,660,800,0);
//            document.formulario1.opcion.value=1;
//            document.formulario1.submit();
	}
        
        
}

</script>

<?php


?>


<link href="../../includes/assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
<link href="../../includes/assets/plugins/bootstrap-datepicker/css/datepicker.css" rel="stylesheet" type="text/css"/>
<div class="page-container">
	<div class="page-content-wrapper">
		<div class="page-content">
			<div class="row">
				<div class="col-md-12">
                                    <!-- BEGIN EXAMPLE TABLE PORTLET-->
                                    <div class="portlet box blue">
                                        <div class="portlet-title">
                                            <div class="caption">
                                                Períodos Vacaciones
                                            </div>
                                        </div>
                                        <div class="portlet-body form ">

                                            <FORM name="formulario1" id="formulario1" class="form-horizontal" action="" method="POST" enctype="multipart/form-data">

                                                <div class="form-body">

                                                    <!--<TD height="25" class="tb-head" align="left"><strong> TIPO DE REGISTRO </strong>-->
                                                   

                                                    <input type="hidden" name="opcion" id="opcion" value="0">
                                                    <label class="col-md-1 control-label" for="txtcodigo">Funcionario:</label>
                                                    <div class="row"> 
                                                        <div class="col-md-8">
                                                                <select id="funcionario" name="funcionario" class="form-control">

                                                                </select>
                                                        </div>					
                                                        
                                                    </div>
                                                    
                                                    <div class="row">
								<div class="col-md-12">&nbsp;</div>
                                                    </div>
                                                    <div class="row">
								<div class="col-md-12">&nbsp;</div>
                                                    </div>
                                                    <div class="row">
								<div class="col-md-12">&nbsp;</div>
                                                    </div>
                                                    <div class="row">
								<div class="col-md-12">&nbsp;</div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-2">
                                                        </div>                    
                                                        <div class="col-md-2">                            
                                                                  <?php boton_metronic('cancel',"submenu_reportes_expediente.php",0); ?>
                                                        </div>                    

                                                        <div class="col-md-2">
                                                                <?php boton_metronic('ok',"confirmar_reporte('\u00BFDesea Generar Reporte de Períodos de Vacaciones?');",2); ?>
                                                        </div>                    
                                                        <div class="col-md-2">                            
                                                        </div>

                                                    </div>
                                                </div>
                                            </FORM>                                                    
                                        </div>                                                     
                                    </div>                                        
                                </div>
                        </div>
                </div>
        </div>
</div>

</BODY>

<script src="../../includes/assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
<script src="../../includes/assets/plugins/bootstrap-datepicker/js/locales/bootstrap-datepicker.es.js"></script>

<script type="text/javascript">

$( document ).ready(function() 
{
	$.get("ajax/obtener_funcionarios.php",function(res){
		$("#funcionario").append(res);
	});
        
         $("#funcionario").select2({
            language: "es",              
             allowClear: true,
        });
});
 </script>
 
</html>