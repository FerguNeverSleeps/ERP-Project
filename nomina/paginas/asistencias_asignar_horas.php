<?php
require_once '../lib/config.php';
require_once '../lib/common.php';
//include ("../header.php");
include ("func_bd.php");
include("funciones_reloj_detalle.php");
$conexion = conexion();

$cod_enca=$_GET["cod_enca"];

$sql="select * from reloj_encabezado where cod_enca='$cod_enca'";
$result=mysqli_query($conexion, $sql);
$reloj_encabezado = mysqli_fetch_array($result,MYSQL_ASSOC);

class tiempo{
  private $temp,$min,$horas;
  public function aminutos($cad){
  	if(!$cad) return 0;
    $this->temp = explode(":",$cad);
    $this->min = ($this->temp[0]*60)+($this->temp[1]);
    return $this->min;
  }
  public function ahoras($cad){
    $this->temp = $cad;
    if($this->temp>59){
      $this->temp = $this->temp/60;
      $this->temp = explode(".",number_format($this->temp,2,".",""));
      $this->temp[0] = strlen($this->temp[0])==1 ? "0".$this->temp[0] : $this->temp[0];
      $this->temp[1] = (((substr($this->temp[1],0,2))*60)/100);
      $this->temp[1] = round($this->temp[1]);
      $this->horas = $this->temp[0].":".(strlen($this->temp[1])==1 ? "0".$this->temp[1] : $this->temp[1]);
    }
    elseif(($this->temp=="")||($this->temp==0))
      $this->horas = "00:00";    
    else
      $this->horas = "00:".(strlen($this->temp)==1 ? "0".$this->temp : $this->temp);
    return $this->horas;
  }
}

function formato_fecha($f){
	list($y,$m,$d)=explode("-",$f);
	return "$d/$m/$y";
}

function formato_fecha_bd($f){
	list($d,$m,$y)=explode("/",$f);
	return "$y-$m-$d";
}

$inicio=formato_fecha($reloj_encabezado["fecha_ini"]);
$fin=formato_fecha($reloj_encabezado["fecha_fin"]);
$tipo_nomina=$reloj_encabezado["tipo_nomina"];
$criterio="manual";//turno o manual

if(isset($_GET["procesar"]))
if($_GET["procesar"]==1):
	//ob_implicit_flush();
	header("X-Accel-Buffering: no");
	print "<div style='text-align: center; padding-top: 80px;'>";
    print     "<br>PROCESANDO<br><small>No cierre hasta culminar el proceso.</small>";
    print "</div>";

    //exit;
    //buscar dias en el calendario en el rango de fechas
    $sql="select fecha from nomcalendarios_tiposnomina where fecha BETWEEN '".formato_fecha_bd($_GET["inicio"])."' and '".formato_fecha_bd($_GET["fin"])."' and cod_tiponomina='$tipo_nomina'  order by fecha";
    $fecha=$db->Execute($sql);
    if(count($fecha)===0) {
    	print "No se encontro los dias en el calendario en el rango de fechas";
    	exit;
    }

    //verificar que las fechas esten detro del rango del encabezado
    for($i=0;$i<count($fecha);$i++){
    	if(!($fecha[$i]["fecha"]>=$reloj_encabezado["fecha_ini"] and $fecha[$i]["fecha"]<=$reloj_encabezado["fecha_fin"])){
    		print "<div style='text-align: center; padding-top: 80px;'>";
    		print "La fecha ".$fecha[$i]["fecha"]." se encuentra fuera del rango del encabezado (del ".$reloj_encabezado["fecha_ini"]." al ".$reloj_encabezado["fecha_fin"].").";
    		print "</div>";
    		exit;
    	}
    }

    $dispositivo_destino=trim($_GET["dispositivo_destino"]);
    
    $ficha=trim($_GET["ficha"],",");
    $ficha=explode(",",$ficha);

    $nompersonal=$db->Execute($sql);
    //print_r($nompersonal);exit;
    for($i=0;$i<count($fecha);$i++){
    	for($j=0;$j<count($ficha);$j++){
	    	//buscar el turno_id y verificar dia_fiesta
	    	$sql="SELECT CP.dia_fiesta, CP.turno_id, T.entrada, T.inicio_descanso, T.salida_descanso, T.salida FROM nomcalendarios_personal as CP, nomturnos as T WHERE T.turno_id=CP.turno_id and CP.fecha='".$fecha[$i]["fecha"]."' and CP.ficha LIKE '".$ficha[$j]."'";
	    	//print $sql;
	    	$calendario_persona=$db->Execute($sql);
	    	//print_r($calendario_persona);exit;
	    	//si es no laborable para la persona, saltar al siguinte registro (omitir)
	    	///////////////////////////if($calendario_persona[0]["dia_fiesta"]=='1') continue;
	    	//calcular la cantidad de horas del turno
    		$tiempo           = new tiempo();
    		$h_inicio_descanso="";
    		$h_salida_descanso="";
    		if($criterio=="turno"){
				$entrada          = $tiempo->aminutos(substr($calendario_persona[0]["entrada"], 0,5));
				$inicio_descanso  = $tiempo->aminutos(substr($calendario_persona[0]["inicio_descanso"], 0,5));
				$salida_descanso  = $tiempo->aminutos(substr($calendario_persona[0]["salida_descanso"], 0,5));
				$salida           = $tiempo->aminutos(substr($calendario_persona[0]["salida"], 0,5));
				$h_entrada        = $calendario_persona[0]["entrada"];
				$h_salida         = $calendario_persona[0]["salida"];
			}
			else{//criterio manual
				$entrada          = $tiempo->aminutos($_GET["entrada"]);
				$inicio_descanso  = $tiempo->aminutos($_GET["inicio_descanso"]);
				$salida_descanso  = $tiempo->aminutos($_GET["salida_descanso"]);
				$salida           = $tiempo->aminutos($_GET["salida"]);

				$h_entrada        = $_GET["entrada"].":00";
				$h_salida         = $_GET["salida"].":00";
				if($inicio_descanso)
					$h_inicio_descanso  = $_GET["inicio_descanso"].":00";
				if($inicio_descanso)
					$h_salida_descanso  = $_GET["salida_descanso"].":00";
			}
			$total_horas      = ($inicio_descanso - $entrada) + ($salida - $salida_descanso);
			$total_horas      = $tiempo->ahoras($total_horas);

			//buscar si la persona tiene registros para ese dia (reloj_detalle)
			$sql="select id from reloj_detalle where ficha='".$ficha[$j]."' and fecha='".$fecha[$i]["fecha"]."' and id_encabezado='$cod_enca'";
			$existe=$db->Execute($sql);
			//print_r($existe);
			if(!isset($existe[0]["id"])){				
			  	//si no existe, insertar el registro con el turno y las horas

			  	$data=[
			  		"id_encabezado"      => "'$cod_enca'",
			  		"marcacion_disp_id"  => "'$dispositivo_destino'",
		  			"ficha"              => "'".$ficha[$j]."'",
		  			"fecha"              => "'".$fecha[$i]["fecha"]."'",
		  			"entrada"            => "'$h_entrada'",
		  			"salida"             => "'$h_salida'",
		  			"salmuerzo"          => "'$h_inicio_descanso'",
		  			"ealmuerzo"          => "'$h_salida_descanso'",
		  			"ordinaria"          => "'$total_horas'",
					"extra"              => "'00:00'",
					"domingo"            => "'00:00'",
					"tardanza"           => "'00:00'",
					"extraext"           => "'00:00'",
					"extranoc"           => "'00:00'",
					"extraextnoc"        => "'00:00'",
					"nacional"           => "'00:00'",
					"descextra1"         => "'00:00'",
					"mixtodiurna"        => "'00:00'",
					"mixtoextdiurna"     => "'00:00'",
					"mixtonoc"           => "'00:00'",
					"mixtoextnoc"        => "'00:00'",
					"emergencia"         => "'00:00'",
					"descansoincompleto" => "'00:00'",
					"dialibre"           => "'00:00'",
					"estatus"            => "2"
		  		];
		  		//print_r($data);
			  	$result=$db->Insert("reloj_detalle",$data);
			  }
    	}
    }    
    //exit;
	?>
	<script type="text/javascript">
		window.onload=function(){
			window.opener.document.control_acceso_detalle2.submit();
			window.close();
		}
	</script>
<?php
exit;
endif;


?>


<!DOCTYPE html>
<html>
<head>
	<link rel="shortcut icon" href="../imagenes/logo.ico" />
	<title>.: AMAXONIA :.</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

	<link href="../../includes/assets/plugins/font-awesome/css/font-awesome.css" rel="stylesheet" type="text/css"/>
	<link href="../../includes/css/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css"/>
	<link href="../../includes/assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
	<link href="../../includes/assets/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css"/>
	<link href="../../includes/assets/plugins/bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet" type="text/css"/>
	<!-- END GLOBAL MANDATORY STYLES -->
	<!-- BEGIN PAGE LEVEL STYLES -->
	<link rel="stylesheet" type="text/css" href="../../includes/assets/plugins/select2/select2.css"/>
	<link rel="stylesheet" type="text/css" href="../../includes/assets/plugins/select2/select2-metronic.css"/>
	<link rel="stylesheet" href="../../includes/assets/plugins/data-tables/DT_bootstrap.css"/>
	<!-- END PAGE LEVEL STYLES -->
	<!-- BEGIN THEME STYLES -->
	<link href="../../includes/css/components.css" rel="stylesheet" type="text/css"/>
	<link href="../../includes/assets/css/style-metronic.css" rel="stylesheet" type="text/css"/>
	<link href="../../includes/assets/css/style.css" rel="stylesheet" type="text/css"/>
	<link href="../../includes/assets/css/style-responsive.css" rel="stylesheet" type="text/css"/>
	<link href="../../includes/assets/css/plugins.css" rel="stylesheet" type="text/css"/>
	<link href="../../includes/assets/css/custom.css" rel="stylesheet" type="text/css"/>
	<!-- END THEME STYLES -->
	<link href="../../includes/assets/css/custom-header4.css" rel="stylesheet" type="text/css"/>
	<link href="../../includes/assets/plugins/bootstrap-datepicker/css/datepicker3.css" rel="stylesheet" type="text/css" />
	<link href="../../includes/assets/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css" rel="stylesheet" type="text/css" />
	<link href="../../includes/assets/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet" type="text/css" />
	<link href="../../includes/assets/plugins/bootstrap-table/bootstrap-table.min.css" rel="stylesheet" type="text/css" />
	<link href="../../includes/assets/plugins/bootstrap-daterangepicker/daterangepicker.min.css" rel="stylesheet" type="text/css" />

	<link href="../../includes/assets/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css" rel="stylesheet" type="text/css" />



	<script src="../../includes/assets/plugins/jquery-1.10.2.min.js" type="text/javascript"></script>
	<script type="text/javascript" src="../../includes/assets/plugins/select2/select2.min.js"></script>
	<script type="text/javascript" src="../../includes/assets/plugins/select2/select2_locale_es.js"></script>
	<script type="text/javascript" src="../../includes/assets/plugins/bootstrap/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="../lib/jscalendar/calendar.js"></script> 
	<script type="text/javascript" src="../lib/jscalendar/lang/calendar-es.js"></script> 
	<script type="text/javascript" src="../lib/jscalendar/calendar-setup.js"></script> 
	<script src="../../includes/assets/plugins/uniform/jquery.uniform.min.js" type="text/javascript"></script>
	<script  language="JavaScript" type="text/javascript" src="ewp.js"></script>
	<script  language="JavaScript" type="text/javascript" src="../lib/common.js?1553466731"></script>
	<style type="text/css">
		.texto1 {
			padding: 50px 20px 20px 20px;
		    text-align: center;
		    font-size: large;
		}
		.control-label {
			font-weight: bold;
		}
	</style>
	<script src="../../includes/assets/plugins/moment.min.js" type="text/javascript"></script>
	<script src="../../includes/assets/plugins/bootstrap-daterangepicker/daterangepicker.min.js" type="text/javascript"></script>
	<script src="../../includes/assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js" type="text/javascript"></script>        
	<script src="../../includes/assets/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
	<script src="../../includes/assets/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js" type="text/javascript"></script>
	<script src="../../includes/assets/plugins/bootstrap-table/bootstrap-table.min.js" type="text/javascript"></script>
	<script src="../../includes/assets/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js" type="text/javascript"></script>
	<script src="../../includes/assets/plugins/bootbox/bootbox.min.js" type="text/javascript"></script>
	<!-- BEGIN PAGE LEVEL SCRIPTS -->
	<script src="../../includes/assets/scripts/core/app1.js"></script>
	<!-- <script src="../../includes/assets/scripts/custom/table-managed.js"></script> -->
	<script>
	jQuery(document).ready(function() {       
	   App.init();
	  // TableManaged.init();
	 // $('#sample_1').DataTable(); 
	});
	</script>
	<script type="text/javascript">
		window.onload=function(){
			$('select').select2({
	            width: "100%",
	            theme: "bootstrap"
	        });
			actualizar_horas();
		}
		function aminutos(cad){
			if(!cad) return 0;
			var temp = cad.split(":");
			return temp[0]*60+temp[1]*1;
		}
		function ahoras(temp){
			if(temp*1>59){
			  temp = temp*1/60;
			  temp = String(temp.toFixed(2)).split(".");
			  temp[0] = temp[0].length==1?"0"+temp[0]:temp[0];
			  temp[1] = (temp[1].substr(0,2)*60)/100;
			  temp[1] = String(Math.round(temp[1]));
			  horas = temp[0]+":"+(temp[1].length==1?"0"+temp[1]:temp[1]);
			}
			else if(temp==""||temp==0)
			  horas = "00:00";    
			else
			  horas = "00:"+(temp.length==1?"0"+temp:temp);
			return horas;
		}
		function actualizar_horas(){
			var inicio_descanso =$("#inicio_descanso").val();
			var entrada         =$("#entrada").val();
			var salida          =$("#salida").val();
			var salida_descanso =$("#salida_descanso").val();
			if(!entrada || !salida){
				return;
			}
			if(!inicio_descanso) inicio_descanso=0;
			if(!salida_descanso) salida_descanso=0;

			inicio_descanso =aminutos(inicio_descanso);
			entrada         =aminutos(entrada);
			salida          =aminutos(salida);
			salida_descanso =aminutos(salida_descanso);

			var total_horas      = (inicio_descanso - entrada) + (salida - salida_descanso);
			$("#total_horas").val(ahoras(total_horas));
		}
		function procesar(){
			var inicio=document.getElementById("txtFechaIni").value;
			var fin=document.getElementById("txtFechaFin").value;

			var ficha=$("#select_ficha").val();

			if(!ficha){
				alert("Debe seleccionar la(s) ficha(s) a la(s) cual(es) asignará las horas.");
				return;
			}
			ficha=ficha.join(",")

			var inicio_descanso     ='';
			var entrada             =$("#entrada").val();
			var salida              =$("#salida").val();
			var salida_descanso     ='';
			var dispositivo_destino =$("#dispositivo_destino").val();

			if(!entrada || !salida ){
				alert("Verifique las horas ingresadas. Hora de entrada/salida vacios.");
				return;
			}
			$("#modal_confirm").modal("hide");

			var href="?cod_enca=<?php print $cod_enca;?>&procesar=1&inicio="+inicio+"&fin="+fin+"&entrada="+entrada+"&inicio_descanso="+inicio_descanso+"&salida_descanso="+salida_descanso+"&salida="+salida+"&dispositivo_destino="+dispositivo_destino+"&ficha="+ficha;
			//console.log(href);return;
			document.getElementById("div_procesar").style.display="none";
			document.getElementById("cargando").style.display="";
			window.location.href=href;
		}
		function checking_proyecto(el){
			var value=$(el).val();
			var checked=$(el).prop("checked");	
			var ficha=$("#proyecto_"+value).val().split(",");
			for(var i=0;i<ficha.length;i++)
				$("#select_ficha option[value="+ficha[i]+"]").prop('selected', checked);
			$("#select_ficha").change();
			//$("#proyecto_"+value+" option").prop('selected', checked).change();
		}
		$.fn.datepicker.dates.es.format="dd/mm/yyyy";
	</script>
</head>


<body class="page-header-fixed page-full-width"  marginheight="50">
<?php
$sql="select idDispositivo, idProyecto, numProyecto, descripcionLarga, cod_dispositivo, nombre from proyectos, reloj_info where idDispositivo=id_dispositivo and idProyecto in (SELECT DISTINCT proyecto FROM nompersonal where estado like 'Activo' and tipnom='$tipo_nomina') order by cod_dispositivo";  
$proyectos=$db->Execute($sql);
?>
<div id='div_procesar' class="page-container" style="margin-bottom: 50px;">
  <!-- BEGIN CONTENT -->
  <div class="page-content-wrapper">
    <div class="page-content">
      <!-- BEGIN PAGE CONTENT-->
      <div class="row">
        <div class="col-md-12">
          <!-- BEGIN EXAMPLE TABLE PORTLET-->
          <div class="portlet box blue">
            <div class="portlet-title">
                <div class="caption" style="font-size: 18px;">Asignar Horas</div>
            </div>
            <div class="portlet-body">

            	
            	<div class="row">
					<div class="col-md-12">


						<div class="row">
							<div class="col-md-4">
								<div class="form-group">
									<label class="control-label col-md-3">Desde:</label>
									<div class="col-md-9">
										<div class="input-group date date-picker" data-provide="datepicker">
											<input type="text" class="form-control" name="txtFechaIni" id="txtFechaIni" value="<?php print $inicio;?>">
											<span class="input-group-btn">
												<button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
											</span>
										</div>
									</div>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label class="control-label col-md-3">Hasta:</label>
									<div class="col-md-9">
										<div class="input-group date date-picker" data-provide="datepicker">
											<input type="text" class="form-control" name="txtFechaFin" id="txtFechaFin" value="<?php print $fin;?>" >
											<span class="input-group-btn">
												<button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
											</span>
										</div>
									</div>
								</div>
							</div>
							<div class="col-md-4">								
									
							</div>
						</div>
						<br>
						<div class="row">
							<div class="col-md-4">
								<div class="form-group">
									<label class="control-label col-md-3">Entrada:</label>
									<div class="col-md-9">
										<input type="time" id="entrada" value="07:00" onchange="actualizar_horas()" class="form-control" > 
									</div>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label class="control-label col-md-3">Salida:</label>
									<div class="col-md-9">
										<input type="time" id="salida" value="15:00" onchange="actualizar_horas()" class="form-control" > 
									</div>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label class="control-label col-md-3">Total:</label>
									<div class="col-md-9">
										<input type="text" id="total_horas" disabled class="form-control" > 
									</div>
								</div>
							</div>
						</div>

						
						<br>
						<br>
						<?php
						$select_masivo="<select multiple id='select_ficha'>";
						print "<div class='container row form-group'>";
						for($i=0;$i<count($proyectos);$i++){
							print 	"<label class='control-label col-xs-12 col-md-4'>";
							print     "<label class='mt-checkbox mt-checkbox-outline'>";
							print        "<input type='checkbox' value='".$proyectos[$i]["idProyecto"]."' onchange='checking_proyecto(this)'>";
							print     "</label>".$proyectos[$i]["cod_dispositivo"]." ".$proyectos[$i]["nombre"]."";
							print   "</label>";
	
							$sql="SELECT * FROM nompersonal where estado like 'Activo' and tipnom='$tipo_nomina' and proyecto='".$proyectos[$i]["idProyecto"]."' order by ficha"; 
							$nompersonal=$db->Execute($sql);
							$select_masivo.="<optgroup label='".$proyectos[$i]["cod_dispositivo"]." ".$proyectos[$i]["nombre"]."'>";
							$proyecto_ficha="";
							for($j=0;$j<count($nompersonal);$j++) { 
								$proyecto_ficha.=$nompersonal[$j]["ficha"].",";
								$select_masivo.="<option value='".$nompersonal[$j]["ficha"]."'>".$nompersonal[$j]["ficha"]." ".$nompersonal[$j]["apenom"]."</option>";
							}
							$proyecto_ficha=trim($proyecto_ficha,",");
							$select_masivo.="</optgroup>";
							print "<input type='hidden' id='proyecto_".$proyectos[$i]["idProyecto"]."' value='$proyecto_ficha' >";
						}
						print "</div>";
						$select_masivo.="</select>";

						print "<div class='container row form-group'>";
						print "<label class='control-label col-md-12'>Colaboradores:</label>";
						print "<div class='col-md-12'>";
						print "$select_masivo";	
						print "</div>";
						print "</div>";
		            	?>
		            	
						<div class='form-group' style="text-align: center;">
							<div class="col-md-12">
								<br>		
				            	<br>
				            	<br>
								<button type="button" class="btn btn-success" style="font-size: 18px; font-weight: bold; border-radius: 5px !important;" data-toggle="modal" href="#modal_confirm"><i class="fa fa-gear" style="font-size: 20px; margin-right: 5px;"></i> Procesar</button>
				            	<br>								
				            	<br>								
							</div>
						</div>
					</div>
				</div>

            <!-- END PORTLET BODY-->
            </div>
        	<!-- END EXAMPLE TABLE PORTLET-->
          </div>
        </div>
      </div>
    <!-- END PAGE CONTENT-->
    </div>
  </div>
  <!-- END CONTENT -->
</div>

<div id='cargando' style='text-align: center; padding-top: 80px; display: none;'>
	<svg width="100px" height="100px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid" class="lds-rolling">
        <circle cx="50" cy="50" fill="none" ng-attr-stroke="{{config.color}}" ng-attr-stroke-width="{{config.width}}" ng-attr-r="{{config.radius}}" ng-attr-stroke-dasharray="{{config.dasharray}}" stroke="#555555" stroke-width="10" r="35" stroke-dasharray="164.93361431346415 56.97787143782138" transform="rotate(36 50 50)">
          <animateTransform attributeName="transform" type="rotate" calcMode="linear" values="0 50 50;360 50 50" keyTimes="0;1" dur="1s" begin="0s" repeatCount="indefinite"></animateTransform>
        </circle>
    </svg>
	<br>PROCESANDO<br><small>No cierre hasta culminar el proceso.</small>
</div>

<div class="modal fade" id="modal_confirm" tabindex="-1" role="basic" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Asignar de Horas - Procesar</h4>
            </div>
            <div class="modal-body">
            	<div class="row">
            		<div class="col-md-12">
		            	<div class="form-group">
							<label class="control-label col-md-12">Proyecto Destino: </label>
							<div class="col-md-12">
								<select id="dispositivo_destino">
								<?php
								for($i=0;$i<count($proyectos);$i++){
									print "<option value='".$proyectos[$i]["idDispositivo"]."'> ".$proyectos[$i]["cod_dispositivo"]." ".$proyectos[$i]["nombre"]."</option>";
								}
								?>
								</select>
							</div>
						</div>
					</div>
				</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn dark btn-outline" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-success" onclick="procesar()">Aceptar</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>


</body>
</html>