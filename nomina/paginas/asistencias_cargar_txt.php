<?php
require_once '../lib/config.php';
require_once '../lib/common.php';
include ("../header.php");
include ("func_bd.php");

$cod_enca=$_GET["cod_enca"];
?>

<!DOCTYPE html>
<html>
<head>
	<title></title>
	<style type="text/css">
		.texto1 {
			padding: 30px 20px 20px 20px;
		    text-align: center;
		    font-size: large;
		    font-weight: bold;
		}
		body {
			padding: 30px 20px 0 20px;
		}
	</style>
	<script type="text/javascript">
		function mostrar_cargando(){
			document.getElementById("div_procesar").style.display="none";
			document.getElementById("cargando").style.display="";
			return true;
		}
	</script>
</head>
<body>
	<div id='div_procesar'>	
		<form  action="cargar_control_acceso2.php" method="post" target="_self" onsubmit="mostrar_cargando();"  enctype="multipart/form-data">
			<input type="hidden" name="cod_enca" value="<?php print $cod_enca;?>">
			<input type="hidden" name="procesar" value="1">
	    	<div class="panel panel-info"> 
	            <div class="panel-body">
	            	<div class='row texto1'>
	            		<div class="col-md-12">Cargar TXT<br></div>
	            	</div>
			    	<div class="row">
		                <div class="col-md-4">
		                        Formato:   
		                </div>
		                <div class="col-md-5">
		                    <select id='archivo_tipo' name="archivo_tipo" class="form-control select2">
		                    	<option value='AMAXONIA'>FORMATO AMAXONIA</option>
		                    	<option value='FORMATO_A'>FORMATO ITESA</option>
		                    	<option value='FORMATO_B'>FORMATO RELOJIN</option>
                                        <option value='FORMATO_C'>FORMATO ANVIZ</option>
                                        <option value='FORMATO_D'>FORMATO EXCEL</option>

		                    </select>
		                </div> 
		            </div>
		            <br>
		            <div class="row">
		                <div class="col-md-4">
		                        Archivo:   
		                </div>
		                <div class="col-md-5">
		                    <input type="file" name="archivo" id="archivo">  
		                </div> 
		            </div>
		            <br>
		            <br><br>
		            <div class="row">
		                <div class="col-md-12" style="text-align: center;">
		                    <button type="submit" class="btn btn-default" style="white-space: nowrap;"><img src="../imagenes/generar.png" width="16" height="16"> Procesar</button>
		                </div> 
		            </div>
		            <br>
		        </div>
		    </div>
		</form>	    
	</div>
   	<div id='cargando' style='text-align: center; padding-top: 80px; display: none;'>
		<svg width="100px" height="100px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid" class="lds-rolling">
            <circle cx="50" cy="50" fill="none" ng-attr-stroke="{{config.color}}" ng-attr-stroke-width="{{config.width}}" ng-attr-r="{{config.radius}}" ng-attr-stroke-dasharray="{{config.dasharray}}" stroke="#555555" stroke-width="10" r="35" stroke-dasharray="164.93361431346415 56.97787143782138" transform="rotate(36 50 50)">
              <animateTransform attributeName="transform" type="rotate" calcMode="linear" values="0 50 50;360 50 50" keyTimes="0;1" dur="1s" begin="0s" repeatCount="indefinite"></animateTransform>
            </circle>
        </svg>
		<br>PROCESANDO<br><small>No cierre hasta que culmine el proceso.</small>
	</div>



</body>
</html>
