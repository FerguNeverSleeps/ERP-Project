<?php
require_once '../lib/config.php';
require_once '../lib/common.php';
include ("../header.php");
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
    print     "<br>PROCESANDO<br><small>No cierre hasta que culmine el proceso.</small>";
    print "</div>";

    //exit;
    //buscar dias feriados en el rango de fechas
    $sql="select fecha from nomcalendarios_tiposnomina where fecha BETWEEN '".formato_fecha_bd($_GET["inicio"])."' and '".formato_fecha_bd($_GET["fin"])."' and cod_tiponomina='$tipo_nomina'  order by fecha";
    $fecha=$db->Execute($sql);
    if(count($fecha)===0) {
    	print "No se encontro dias feriados en el rango de fechas";
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
    
    $proyecto_id=trim($_GET["proyecto_id"],",");
    //personas activas en el encabezado
    $sql="SELECT personal_id, proyecto, ficha FROM nompersonal where estado like 'Activo' and tipnom='$tipo_nomina' and proyecto in ($proyecto_id)";       

    $nompersonal=$db->Execute($sql);
    //print_r($nompersonal);exit;
    for($i=0;$i<count($fecha);$i++){
    	for($j=0;$j<count($nompersonal);$j++){
	    	//buscar el turno_id y verificar dia_fiesta
	    	$sql="SELECT CP.dia_fiesta, CP.turno_id, T.entrada, T.inicio_descanso, T.salida_descanso, T.salida FROM nomcalendarios_personal as CP, nomturnos as T WHERE T.turno_id=CP.turno_id and CP.fecha='".$fecha[$i]["fecha"]."' and CP.ficha LIKE '".$nompersonal[$j]["ficha"]."'";
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
			$sql="select id from reloj_detalle where ficha='".$nompersonal[$j]["ficha"]."' and fecha='".$fecha[$i]["fecha"]."' and id_encabezado='$cod_enca'";
			$existe=$db->Execute($sql);
			//print_r($existe);
			if(!isset($existe[0]["id"])){				
			  	//si no existe, insertar el registro con el turno y las horas
			  	//buscar en reloj_info el id_dispositivo con proyecto
			  	$sql="select idDispositivo from proyectos where idProyecto='".$nompersonal[$j]["proyecto"]."'";			  	
			  	$dispositivo_id=$db->Execute($sql);
			  	if(isset($dispositivo_id[0]["idDispositivo"]))
			  		$dispositivo_id=$dispositivo_id[0]["idDispositivo"];
			  	else
			  		$dispositivo_id=1;

			  	$data=[
			  		"id_encabezado"      => "'$cod_enca'",
			  		"marcacion_disp_id"  => "'$dispositivo_id'",
		  			"ficha"              => "'".$nompersonal[$j]["ficha"]."'",
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
	<title></title>
	<style type="text/css">
		.texto1 {
			padding: 50px 20px 20px 20px;
		    text-align: center;
		    font-size: large;
		}
	</style>
	<script type="text/javascript">
		window.onload=function(){
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

			var proyecto_id="";
			$("input[type=checkbox]:checked").each(function(){
				proyecto_id+=$(this).val()+",";
			});
			if(!proyecto_id){
				alert("Debe seleccionar el proyecto donde aplicará los cambios.");
				return;
			}
			var inicio_descanso =$("#inicio_descanso").val();
			var entrada         =$("#entrada").val();
			var salida          =$("#salida").val();
			var salida_descanso =$("#salida_descanso").val();

			if(!entrada || !salida ){
				alert("Verifique las horas ingresadas. Hora de entrada/salida vacios.");
				return;
			}

			window.location.href="?cod_enca=<?php print $cod_enca;?>&procesar=1&inicio="+inicio+"&fin="+fin+"&entrada="+entrada+"&inicio_descanso="+inicio_descanso+"&salida_descanso="+salida_descanso+"&salida="+salida+"&proyecto_id="+proyecto_id;
			document.getElementById("div_procesar").style.display="none";
			document.getElementById("cargando").style.display="";
		}
	</script>
</head>
<body>
	<div id='div_procesar'>
		<div class='texto1'>Asignar Horas <br><br><br>Asignar Horas Ordinarias<br> Seleccione Rango de Fechas</div>
		<div width="350" style="display: flex; align-items: center; justify-content: center; padding-left: 10px; height: 45px;">Desde: 
			<input name="txtFechaIni" type="text" id="txtFechaIni" style="width:100px;height:25px;font-size:12pt;" value="<?php print $inicio;?>" maxlength="60" onblur="javascript:actualizar('txtFechaIni','fila_edad');">
					<input name="image2" type="image" id="d_fechaini" src="../lib/jscalendar/cal.gif" style="height: 25px;width:25px;" />
				<script type="text/javascript">Calendar.setup({inputField:"txtFechaIni",ifFormat:"%d/%m/%Y",button:"d_fechaini"});</script>
					
			&#160; Hasta: 
			<input name="txtFechaFin" type="text" id="txtFechaFin" style="width:100px;height:25px;font-size:12pt;" value="<?php print $fin; ?>" maxlength="60" onblur="javascript:actualizar('txtFechaFin','fila_edad');">
					<input name="image2" type="image" id="d_fechafin" src="../lib/jscalendar/cal.gif"  style="height: 25px;width:25px;"/>
				<script type="text/javascript">Calendar.setup({inputField:"txtFechaFin",ifFormat:"%d/%m/%Y",button:"d_fechafin"});</script>	
                </div>
                <div style="display: flex; align-items: center; justify-content: center; padding-left: 10px; height: 45px;">
                	Entrada:          	<input type="time" id="entrada"         value="07:00" style="width:90px;height:25px;font-size:12pt;margin-right: 10px;" onchange="actualizar_horas()">
                	<span style="display: none;">
                	S. Almuerzo:      	<input type="time" id="inicio_descanso" value="" style="width:90px;height:25px;font-size:12pt;margin-right: 10px;" onchange="actualizar_horas()">
                	E. Almuerzo:      	<input type="time" id="salida_descanso" value="" style="width:90px;height:25px;font-size:12pt;margin-right: 10px;" onchange="actualizar_horas()">
                	</span>
                	Salida:          	<input type="time" id="salida"          value="15:00" style="width:90px;height:25px;font-size:12pt;margin-right: 10px;" onchange="actualizar_horas()">
                	Total Horas:        <input type="text" value="" id="total_horas" disabled style="width:60px;height:25px;font-size:12pt;margin-right: 10px;">
                </div>
                <br>
                <div class="container">
		            <div class="row">
		            	<?php
		            	$sql="select idDispositivo, idProyecto, numProyecto, descripcionLarga, cod_dispositivo, nombre from proyectos, reloj_info where idDispositivo=id_dispositivo and idProyecto in (SELECT DISTINCT proyecto FROM nompersonal where estado like 'Activo' and tipnom='$tipo_nomina') order by cod_dispositivo";  
						$proyectos=$db->Execute($sql);
						for($i=0;$i<count($proyectos);$i++){
							print "<div class='col-xs-6 col-sm-4 col-md-3'>";
							print "<div class='checkbox'>";
							print "<label><input type='checkbox' class='checkbox' name='proyecto_id' value='".$proyectos[$i]["idProyecto"]."'> ".$proyectos[$i]["cod_dispositivo"]." ".$proyectos[$i]["nombre"]."</label>";
							print "</div>";
							print "</div>";
						}
		            	?>
		            </div>
		        </div>
                <div style="text-align: center; padding: 30px 0 0 0;">
                    <button type="button" style="white-space: nowrap;" class="btn btn-default"  onclick="procesar()"><img src="../imagenes/generar.png" width="16" height="16"> Procesar</button>
                </div>
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