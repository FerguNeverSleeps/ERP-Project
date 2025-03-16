<?php 
session_start();
include ("../header4.php");
include("../lib/common.php");
include("func_bd.php");
?>
<script>
    function exportar()
    {
        var fecha_inicio = document.formulario.fecha_inicio.value;
        var fecha_fin = document.formulario.fecha_fin.value;
        if(fecha_inicio == "" || fecha_fin == "")
        {
            alert('Por favor, seleccione una fecha');
        }
        else
        {
            location.href='excel_regiones_funcionarios.php?fecha_inicio='+fecha_inicio+'&fecha_fin='+fecha_fin; 
        }
    }
</script>
<script src="../../includes/assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
<link href="../../includes/assets/plugins/bootstrap-datepicker/css/datepicker.css" rel="stylesheet" type="text/css" />
<form id="formulario" name="formulario" method="post" action="">
    <div class="page-container">
        <div class="page-wrapper-containter">
            <div class="page-content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet box blue">
                            <div class="portlet-title">
                                <div class="caption">
                                    Parámetros del Reporte
                                </div>
                                <div class="actions">
                                    <a class="btn btn-sm blue"  onclick="javascript: window.location='submenu_reportes_integrantes.php'">
                                        <i class="fa fa-arrow-left"></i> Regresar
                                    </a>                                    
                                </div>
                            </div>
                            <div class="portlet-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="input-group date date-picker" data-provide="datepicker" data-date-end-date="0d">  
                                            <input name="fecha_inicio" type="text"  class="form-control" placeholder="Fecha Inicial" id="fecha_inicio" value="<?php date("Y-m-d"); ?>" maxlength="10">
                                            <span class="input-group-btn">
                                                <button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
                                            </span>
                                        </div>
                                    </div> 
                                    <div class="col-md-6">
                                        <div class="input-group date date-picker" data-provide="datepicker" data-date-end-date="0d">  
                                            <input name="fecha_fin" type="text"  class="form-control" placeholder="Fecha Final" id="fecha_fin" value="<?php date("Y-m-d"); ?>" maxlength="10">
                                            <span class="input-group-btn">
                                                <button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
                                            </span>
                                        </div>
                                    </div> 
                                </div>
                                <div class="row">
                                    <div class="col-md-12">&nbsp;</div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12" align="center">
                                        <div class="btn blue" onClick="javascript:exportar();"><i class="fa fa-download"></i> Exportar</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
</body>
<?php include ("../footer4.php");?>
</html>