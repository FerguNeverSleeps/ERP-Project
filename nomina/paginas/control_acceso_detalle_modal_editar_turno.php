<?php
require_once '../lib/config.php';
require_once '../lib/common.php';
include ("../header.php");
include ("func_bd.php");

$reloj_detalle_id=$_REQUEST["reloj_detalle_id"];
if(!$reloj_detalle_id){
	exit;
}



if(isset($_REQUEST["procesar"]) and $_REQUEST["procesar"]=="1" and $reloj_detalle_id){

	//print_r($_POST);

	//exit;

	$sql="select * from nomcalendarios_personal where ficha='".$_REQUEST["ficha"]."' and fecha='".$_REQUEST["fecha"]."'";
    $result_nomturno = sql_ejecutar($sql);
    $nomturno = fetch_array($result_nomturno);
    //si existe el turno
    if(isset($nomturno["id"]) and $nomturno["id"]){
        $sql="update nomcalendarios_personal set turno_id='".$_REQUEST["turno_id"]."' where id='".$nomturno["id"]."'";
        $result_nomturno = sql_ejecutar($sql);
    }
    else{
        $sql="insert into nomcalendarios_personal(ficha,fecha,turno_id) values('".$_REQUEST["ficha"]."','".$_REQUEST["fecha"]."','".$_REQUEST["turno_id"]."')";
        $result_nomturno = sql_ejecutar($sql);
    }

    //buscar el estatus anterior para mantenerlo al finalizar el recalcular
    $sql="select estatus from reloj_detalle where id='".$reloj_detalle_id."'";
    $result_estatus_ant = sql_ejecutar($sql);
    $estatus_ant = fetch_array($result_estatus_ant);
    if(isset($estatus_ant["estatus"]))
    	$estatus_ant=$estatus_ant["estatus"];
    else
    	$estatus_ant=0;

    //print "estatis[$estatus_ant]";exit;

    $sql="update reloj_detalle set turno='".$_REQUEST["turno_id"]."', estatus=0 where id='".$reloj_detalle_id."'";
    sql_ejecutar($sql);

    class tiempo{
        private $temp,$min,$horas;
        public function aminutos($cad)
        {
            $this->temp = explode(":",$cad);
            $this->min = ($this->temp[0]*60)+($this->temp[1]);
            return $this->min;
        }
        public function ahoras($cad)
        {
            $this->temp = $cad;
            if($this->temp>59)
            {
                $this->temp = $this->temp/60;
                $this->temp = explode(".",number_format($this->temp,2,".",""));
                $this->temp[0] = strlen($this->temp[0])==1 ? "0".$this->temp[0] : $this->temp[0];
                $this->temp[1] = (((substr($this->temp[1],0,2))*60)/100);
                $this->temp[1] = round($this->temp[1]);
                //$this->horas = $this->temp[0].":".(strlen($this->temp[1][0])==1 ? "0".$this->temp[1][0] : round(substr($this->temp[1][0],0,2).'.'.substr($this->temp[1][0],2,1)));
                $this->horas = $this->temp[0].":".(strlen($this->temp[1])==1 ? "0".$this->temp[1] : $this->temp[1]);
            }
            elseif(($this->temp=="")||($this->temp==0))
            {
                $this->horas = "00:00";
            }
            else
            {
                $this->horas = "00:".(strlen($this->temp)==1 ? "0".$this->temp : $this->temp);//$this->temp;
            }
            return $this->horas;
        }
    }
    include_once("funciones_reloj_detalle.php");
    $result=reloj_detalle__actualizar($reloj_detalle_id); 

    //colocar nuevamente el estatus que tenia
    $sql="update reloj_detalle set estatus='".$estatus_ant."' where id='".$reloj_detalle_id."'";
    sql_ejecutar($sql);

    if($_REQUEST["editar_turno_ajax"]=="1"){
    	exit;
    }


	print '<script type="text/javascript">
		window.onload=function(){
			window.opener.document.control_acceso_detalle2.submit();
			window.close();
		}
	</script>';
	exit;
}

$select = "select * from reloj_detalle where id='$reloj_detalle_id'";
$result = sql_ejecutar($select);
$fila = fetch_array($result);

if(!isset($fila["id"])){
	exit;
}

$ficha=$fila["ficha"];
$fecha=$fila["fecha"];

$select_turno = "SELECT  turno_id, descripcion FROM nomturnos ORDER BY descripcion ASC";
$result_turno = sql_ejecutar($select_turno);


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
		<form  action="control_acceso_detalle_modal_editar_turno.php" method="post" target="_self" onsubmit="mostrar_cargando();"  enctype="multipart/form-data">
			<input type="hidden" name="reloj_detalle_id" value="<?php print $reloj_detalle_id;?>">
			<input type="hidden" name="procesar" value="1">
			<input type="hidden" name="ficha" value="<?php print $ficha;?>">
			<input type="hidden" name="fecha" value="<?php print $fecha;?>">
	    	<div class="panel panel-info"> 
	            <div class="panel-body">
	            	<div class='row texto1'>
	            		<div class="col-md-12"><?php print "Editar turno para la ficha ".$fila["ficha"]." en la fecha ".$fila["fecha"].".";?> <br></div>
	            	</div>
			    	<div class="row">
		                <div class="col-md-4">
		                        Turno:   
		                </div>
		                <div class="col-md-5">
		                    <select id='select_turno_id' name="turno_id" class="form-control select2">
				                <option value="0">&nbsp;</option>
				                <?php                 
				                while($row = fetch_array($result_turno)){    
				                    $add="";   
				                    if($fila['turno']==$row["turno_id"])      {
				                        $add="selected";
				                    }       
				                    print "<option value='".$row["turno_id"]."' $add>".(strtoupper(trim($row["turno_id"]))!=strtoupper(trim($row["descripcion"]))?"".$row["turno_id"]." - ":"").$row["descripcion"]."</option>";
				                }
				                ?>
				            </select>
		                </div> 
		            </div>

		            <br><br>
		            <div class="row">
		                <div class="col-md-12" style="text-align: center;">
		                    <button type="submit" class="btn btn-default" style="white-space: nowrap;"><img src="../imagenes/generar.png" width="16" height="16"> Aceptar</button>
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