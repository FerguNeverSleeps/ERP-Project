<?php
//-------------------------------------------------
session_start();
//-------------------------------------------------
require_once "config/rhexpress_config.php";
$conexion =  mysqli_connect( DB_HOST, DB_USUARIO, DB_CLAVE, $_SESSION['bd'] ) or die( 'No Hay Conexión con el Servidor de Mysql' );
        mysqli_query($conexion, 'SET CHARACTER SET utf8');
//-------------------------------------------------
$ficha = $_SESSION['ficha_rhexpress'];
//-------------------------------------------------
$sql = "SELECT codorg,descrip FROM nomnivel1";
$res1 = $conexion->query($sql);
//-------------------------------------------------
include("config/rhexpress_header2.php");
?>
<div class="row">
    <div class="col-md-12">
        <!-- BEGIN SAMPLE TABLE PORTLET-->
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-social-dribbble font-blue"></i>
                    <span class="caption-subject font-blue bold uppercase">Busqueda</span>
                </div>
                <!--
                <div class="actions">
                    <a class="btn btn-circle btn-icon-only btn-default" href="reportes/pdf/rhexpress_reporte_general.php">
                        <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                    </a>
                </div>
                -->
            </div>
            <div class="portlet-body">
           
               <div class="row">
                    <div class="form-group">
                        <div class="col-md-6">
                            <label for="cedula">Busqueda por cedula</label>
                            <!-- busqueda por cedula -->
                            <input type="text" name="por_cedula" id="por_cedula" class="form-control">
                            <button id="buscar" class="btn btn-default" type="button" style="margin-top: 20px;">Buscar
                            <i class="fa fa-search" aria-hidden="true"></i> 
                            </button>
                        </div>
                    </div>
               </div>
               <hr>
                <form name="solicitudes_buscar" action="solicitudes_buscar.php" target="_blank" method="POST">
                    <div class="row" style="margin-top: 20px;">
                        <div class="form-group"> 
                            <div class="col-md-6">
                                <label for="departamento">Departamento</label>
                                <select name="select_departamento" id="select_departamento" class="form-control">
                                    <option value="0">Todos</option>
                                    <?php 
                                        while ($fila=mysqli_fetch_array($res1)) {
                                            echo '<option value="'.$fila['codorg'].'">'.$fila['descrip'].'</option>';
                                        }
                                    ?>
                                </select>
                            </div>                
                        </div>                
                    </div>
                    <div class="row" style="margin-top: 20px;">
                        <div class="form-group">
                            <div class="col-md-6">                            
                                <label class="">Fecha Inicio</label>
                                
                                <div class="input-group date date-picker"  data-date-format="yyyy-mm-dd">
                                    <input  name="fecha_inicio" id="fecha_inicio" type="text" class="form-control"  >
                                    <span class="input-group-btn">
                                        <button class="btn default" type="button">
                                            <i class="fa fa-calendar"></i>
                                        </button>
                                    </span>
                                </div>
                                <span class="help-block" style="text-align:left; color:red;"> Seleccione un rango de fechas Valido*</span>
                            
                        </div>
                        <div class="col-md-6">
                            <label class="">Fecha Fin</label>
                                
                                    <div class="input-group date date-picker"  data-date-format="yyyy-mm-dd">
                                        <input  name="fecha_fin" id="fecha_fin" type="text" class="form-control" >
                                        <span class="input-group-btn">
                                            <button class="btn default" type="button">
                                                <i class="fa fa-calendar"></i>
                                            </button>
                                        </span>
                                    </div>
                                    <span class="help-block" style="text-align:left; color:red;"> Seleccione un rango de fechas Valido*</span>
                                
                            </div>
                        </div>
                    </div>
                   
                    <button id="buscar" class="btn btn-default" type="submit" style="margin-top: 20px;">Generar Reporte
                        <i class="fa fa-file-excel-o" aria-hidden="true"></i> 
                    </button>
                    
                </form>                
                
                <div class="table-scrollable">                
                    <table class="table table-hover" id="busqueda_solicitudes" style="text-align: center;">
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th>Cedula</th>
                                <th>Tipo solicitud</th>
                                <th>Estado de solicitud</th>
                                <th>Departamento</th>
                                <th>Fecha de registro</th>
                                <th>Observacion del jefe</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- END SAMPLE TABLE PORTLET-->
    </div>
</div>

<div class="bd-example">
    <div class="modal fade" id="incidencias" tabindex="-10" role="dialog" aria-labelledby="asusenciaLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="TituloIncidencias"> </h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <!-- Begin: Demo Datatable 2 -->
                            <div class="portlet light portlet-fit portlet-datatable bordered">
                                <div class="portlet-title">
                                    <div class="caption">
                                        <i class="icon-settings font-dark"></i>
                                        <span class="caption-subject font-dark sbold uppercase">Detalles de Incidencias</span>
                                    </div>
                                </div>
                                <div class="portlet-body">
                                    <div class="table-container">
                                        <div class="table-actions-wrapper">
                                            <span> </span>
                                        </div>
                                        <table class="table table-striped table-bordered table-hover table-checkable" id="datatable_ajax">
                                            <thead>
                                                <tr>
                                                    <th> Fecha </th>
                                                    <th> Incidencia </th>
                                                    <th> Codigo </th>
                                                </tr>
                                            </thead>
                                            <tbody> </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <!-- End: Demo Datatable 2 -->
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $tabla = 0; ?>
<?php include("config/rhexpress_footer3.php");

?>
<script type="text/javascript">
$(document).ready(function()
{

    
    $("#fecha_inicio, #fecha_fin").datepicker({
    	format: 'yyyy-mm-dd',
    	autoclose: true
    });
    $("#busqueda_solicitudes_filter").hide();
    // $("#conceptos,#frecuencias").multiSelect();
	// $('.ms-container').css('width','90%');
    // $("#generar_rpt").on("click", function(){
	// 	var conceptos    = $("select#conceptos").val();
	// 	var frecuencias  = $("select#frecuencias").val();
	// 	var fecha_inicio = $("#fecha_inicio").val();
	// 	var fecha_fin    = $("#fecha_fin").val();
	// 	var ficha        = $("#ficha").val();
        
    //     if(fecha_inicio === "" || fecha_fin === "")
    //     {
    //         alert("Por favor, ingrese un rango de fechas válido");
    //     }
    //     if(fecha_inicio!== "" && fecha_fin !== "" )
    //     {
    //         //console.log(planilla+" => "+conceptos+" => "+codtip+" => "+frecuencia+" => "+fecha_inicio+" => "+fecha_fin);
    //         var url =  "../fpdf/rpt_conceptos_federal.php?conceptos="+conceptos+"&fecha_inicio="+fecha_inicio+"&fecha_fin="+fecha_fin+"&ficha="+ficha+"&frecuencias="+frecuencias; 
    //         window.open(url);
    //     }
    // });
});
</script>
<script src="ajax/js/get_solicitudes_casos.js"></script><?php

 include("config/end.php"); ?>
