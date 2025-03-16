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
	if($('#fecha_inicio').val()=="" || $('#fecha_fin').val()=="")
	{
		
            alert("Falta una fecha");
            return false;
	}
        
	
	if(confirm(msg) == true) 
	{            
            var fecha_1 = moment(document.getElementById('fecha_1').value, 'DD/MM/YYYY');
            var fecha_2 = moment(document.getElementById('fecha_2').value, 'DD/MM/YYYY');
//            alert(fecha_1);
//            alert(fecha_2);
            var f1 = moment(fecha_1).format('YYYY-MM-DD');
            var f2 = moment(fecha_2).format('YYYY-MM-DD');
//            alert(f1);
//            alert(f2);
            AbrirVentana('pdf/reporte_vencimiento_implementos_empleado_pdf.php?fecha_inicio='+f1+'&fecha_fin='+f2,660,800,0);
//            document.formulario1.opcion.value=1;
//            document.formulario1.submit();
	}
        
        
}

</script>

<?php

if($_POST['opcion']==1)
{
    if($_POST['fecha_inicio']!='')
        $fecha_inicio=fecha_sql($_POST['fecha_inicio']);
    if($_POST['fecha_fin']!='')
        $fecha_fin=fecha_sql($_POST['fecha_fin']);
//    echo $fecha_inicio;
//    echo $fecha_fin;
//    exit;2016-01-01&fecha_fin=2016-02-30
    window.open("pdf/reporte_vencimiento_implementos_empleado_pdf.php?fecha_inicio=2016-01-01&fecha_fin=2016-02-30");
    //include 'pdf/reporte_licencia_periodo_pdf.php?fecha_inicio='.$fecha_inicio.'&fecha_fin='.$fecha_fin.';  
            
	//activar_pagina("expediente_list.php?cedula=$cedula");
}


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
                                                Vencimiento de Implementos / Herramientas - Empleado
                                            </div>
                                        </div>
                                        <div class="portlet-body form ">

                                            <FORM name="formulario1" id="formulario1" class="form-horizontal" action="" method="POST" enctype="multipart/form-data">

                                                <div class="form-body">

                                                    <!--<TD height="25" class="tb-head" align="left"><strong> TIPO DE REGISTRO </strong>-->
                                                    <input type="hidden" name="editar" id="editar" value="<? echo $editar;?>">
                                                    <input type="hidden" name="cedula" id="cedula" value="<? echo $cedula;?>">
                                                    <input type="hidden" name="codigo" id="codigo" value="<? echo $_GET['codigo'];?>">

                                                    <input type="hidden" name="opcion" id="opcion" value="0">

                                                    <div class="form-group">
                                                        <label class="col-md-3 control-label" for="txtcodigo">Fecha Inicio:</label>
                                                        <div class="col-md-5">
                                                            <div class="input-group date date-picker" data-provide="datepicker" data-date-format="dd/mm/yyyy">
                                                                <input size="10" type="text" name="fecha_1" class="form-control" placeholder="(dd/mm/aaaa)" id="fecha_1" value="">
                                                                <span class="input-group-btn">
                                                                    <button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>

                                                     <div class="form-group">
                                                        <label class="col-md-3 control-label" for="txtcodigo">Fecha Fin:</label>
                                                        <div class="col-md-5">
                                                            <div class="input-group date date-picker" data-provide="datepicker" data-date-format="dd/mm/yyyy">
                                                                <input size="10" type="text" name="fecha_2" class="form-control" placeholder="(dd/mm/aaaa)" id="fecha_2" value="">
                                                                <span class="input-group-btn">
                                                                    <button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>   

                                                    <div class="row">
                                                        <div class="col-md-4">
                                                        </div>                    
                                                        <div class="col-md-2">                            
                                                                  <?php boton_metronic('cancel',"submenu_reportes_expediente.php",0); ?>
                                                        </div>                    

                                                        <div class="col-md-2">
                                                                <?php boton_metronic('ok',"confirmar_reporte('\u00BFDesea Generar Reporte?');",2); ?>
                                                        </div>                    
                                                        <div class="col-md-4">                            
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
</html>