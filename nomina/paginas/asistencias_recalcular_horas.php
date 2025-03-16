<?php
require_once '../lib/config.php';
require_once '../lib/common.php';
include ("../header.php");
include ("func_bd.php");
$conexion = conexion();

$cod_enca=$_GET["cod_enca"];

$sql="select * from reloj_encabezado where cod_enca='$cod_enca'";
$result=mysqli_query($conexion, $sql);
$reloj_encabezado = mysqli_fetch_array($result,MYSQL_ASSOC);

class tiempo{
  private $temp,$min,$horas;
  public function aminutos($cad){
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


if(isset($_GET["procesar"]))
if($_GET["procesar"]==1):
	//ob_implicit_flush();
	header("X-Accel-Buffering: no");
	print "<div style='text-align: center; padding-top: 80px;'>";
    print     "<br>PROCESANDO<br><small>No cierre hasta que culmine el proceso.</small>";
    print "</div>";

    include("funciones_reloj_detalle.php");

    $sql="select id from reloj_detalle where id_encabezado='$cod_enca' and fecha between '".formato_fecha_bd($_GET["inicio"])."' and '".formato_fecha_bd($_GET["fin"])."' and estatus=0 order by ficha, fecha";
    $result=mysqli_query($conexion, $sql);
	while($reloj_detalle = mysqli_fetch_array($result,MYSQL_ASSOC)){
		reloj_detalle__actualizar($reloj_detalle["id"],true);  		
	}
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
		function procesar(){
			var inicio=document.getElementById("txtFechaIni").value;
			var fin=document.getElementById("txtFechaFin").value;
			window.location.href="?cod_enca=<?php print $cod_enca;?>&procesar=1&inicio="+inicio+"&fin="+fin;
			document.getElementById("div_procesar").style.display="none";
			document.getElementById("cargando").style.display="";
		}
	</script>
</head>
<body>
	<div id='div_procesar'>
		<div class='texto1'>Recalcular Horas <br>seleccione Rango de Fechas</div>
		<div width="350" style="display: flex; align-items: center; justify-content: center; padding-left: 10px; height: 45px;">Desde: 
			<input name="txtFechaIni" type="text" id="txtFechaIni" style="width:100px;height:25px;font-size:12pt;" value="<?php print $inicio;?>" maxlength="60" onblur="javascript:actualizar('txtFechaIni','fila_edad');">
					<input name="image2" type="image" id="d_fechaini" src="../lib/jscalendar/cal.gif" style="height: 25px;width:25px;" />
				<script type="text/javascript">Calendar.setup({inputField:"txtFechaIni",ifFormat:"%d/%m/%Y",button:"d_fechaini"});</script>
					
			&#160; Hasta: 
			<input name="txtFechaFin" type="text" id="txtFechaFin" style="width:100px;height:25px;font-size:12pt;" value="<?php print $fin; ?>" maxlength="60" onblur="javascript:actualizar('txtFechaFin','fila_edad');">
					<input name="image2" type="image" id="d_fechafin" src="../lib/jscalendar/cal.gif"  style="height: 25px;width:25px;"/>
				<script type="text/javascript">Calendar.setup({inputField:"txtFechaFin",ifFormat:"%d/%m/%Y",button:"d_fechafin"});</script>	
	    </div>
	    <div style="text-align: center; padding: 30px 0 0 0;">
	    	<button type="button" class="btn btn-default" style="white-space: nowrap;" onclick="procesar()"><img src="../imagenes/generar.png" width="16" height="16"> Procesar</button>
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

