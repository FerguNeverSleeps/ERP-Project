<?php
session_start();
ob_start();
$termino = $_SESSION['termino'];
?>
<?php
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // Always modified
header("Cache-Control: private, no-store, no-cache, must-revalidate"); // HTTP/1.1
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache"); // HTTP/1.0
?>
<link rel="stylesheet" type="text/css" href="dialog_box.css" />

<?php
include ("../header.php");
include("../lib/common.php");
include("func_bd.php");
?>
<style>
.bordes {
    border: 1px dashed black;
}
#ficha_display::-webkit-calendar-picker-indicator,
#dispositivo_display::-webkit-calendar-picker-indicator {
    display: none;
}

.info1 input{
    text-align: center;
}

.estilo_porcentaje {
    color: #673ab7;
}
</style>
<?php
$host_ip = $_SERVER['REMOTE_ADDR'];
$select = "select capital, tipo_empresa from nomempresa";
$result = sql_ejecutar($select);
$filaemp = fetch_array($result);
$empresa = $filaemp['capital'];
$empresa_tipo = $filaemp['tipo_empresa'];
if($empresa=="pandora")
{
    $titulos = array("Ficha", "Fecha","Entrada","Salida","Entrada1","Salida1","Entrada2","Salida2","Tardanza","Extras","Extras Ext", "Noc", "Noc Ext","Dom","Nac", "ExtNac", "NacNoc");
}
elseif($empresa=="electron")
{
    $titulos = array("Ficha", "Fecha","Entrada","S. Almu","E. Almu","Salida","Salida DS","Ent. Emer.","Sal. Emer.","Tardanza","Desc. Ext.");
}
elseif($empresa=="zkteco")
{
    $titulos = array("Ficha", "Dispositivo", "Fecha","Entrada","S. Almu","E. Almu","Salida","Salida DS","Ent. Emer.","Sal. Emer.","Tardanza","Desc. Ext.");
}
else
{
    $titulos = array("Ficha", "Dispositivo", "Fecha","Entrada","S. Almu","E. Almu","Salida","Ordinarias","Tardanza","Desc. Ext.","Extras","Extras Ext", "Noc", "Noc Ext","Dom","Nac", "ExtNac", "NacNoc");
}

//accion editar turno 
if(isset($_REQUEST["editar_turno"]) and $_REQUEST["editar_turno"]=="1"){
    //print "entro: editar_turno";
    //buscar si existe (hacer update), sino hacer insert
    $sql="select * from nomcalendarios_personal where ficha='".$_REQUEST["ficha"]."' and fecha='".$_REQUEST["fecha"]."'";
    //print "SQL1: $sql";
    $result_nomturno = sql_ejecutar($sql);
    $nomturno = fetch_array($result_nomturno);
    //si existe el turno
    if(isset($nomturno["id"]) and $nomturno["id"]){
        $sql="update nomcalendarios_personal set turno_id='".$_REQUEST["turno_id"]."' where id='".$nomturno["id"]."'";
        //print "SQL2: $sql";
        $result_nomturno = sql_ejecutar($sql);
    }
    else{
        $sql="insert into nomcalendarios_personal(ficha,fecha,turno_id) values('".$_REQUEST["ficha"]."','".$_REQUEST["fecha"]."','".$_REQUEST["turno_id"]."')";
        //print "SQL3: $sql";
        $result_nomturno = sql_ejecutar($sql);
    }

    if(isset($_REQUEST["editar_turno_ajax"]) and $_REQUEST["editar_turno_ajax"]=="1"){
        exit;
    }

    //realizar el recalculo y actualizacion de horas de horas segun el turno nuevo
    if(isset($_REQUEST["id"]) and $_REQUEST["id"]){
        //buscar el estatus anterior para mantenerlo al finalizar el recalcular
        $sql="select estatus from reloj_detalle where id='".$_REQUEST["id"]."'";
        $result_estatus_ant = sql_ejecutar($sql);
        $estatus_ant = fetch_array($result_estatus_ant);
        if(isset($estatus_ant["estatus"]))
            $estatus_ant=$estatus_ant["estatus"];
        else
            $estatus_ant=0;

        //buscar si el turno es salida dia siguiente, si es asi, poner salida_diasiguiente=SI
        $sql="select * from nomturnos where turno_id='".$_REQUEST["turno_id"]."'";
        $result_turno = sql_ejecutar($sql);
        $turno = fetch_array($result_turno);
        $add.="";
        if(isset($turno["turno_id"])){
            if(substr($turno["salida"],0,5)<substr($turno["entrada"],0,5)){
                $add.=" ,salida_diasiguiente='SI'";
            }
            else{
                $add.=" ,salida_diasiguiente=''";
            }
        }

        $sql="update reloj_detalle set turno='".$_REQUEST["turno_id"]."', estatus=0 $add where id='".$_REQUEST["id"]."'";
        //print "SQL4: $sql";        
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
        $result=reloj_detalle__actualizar($_REQUEST["id"]);  

        //colocar nuevamente el estatus que tenia
        $sql="update reloj_detalle set estatus='".$estatus_ant."' where id='".$_REQUEST["id"]."'";
        sql_ejecutar($sql);
        //print "entro: functiones_reloj_detalle";      
        //print_r($result);
    }
    header("Location: control_acceso_editar2.php?reg=".$_REQUEST["reg"]."&id=".$_REQUEST["id"]."&pagina=".$_REQUEST["pagina"]."&buscar=".$_REQUEST["buscar"]."&buscar_dispositivo=".$_REQUEST["buscar_dispositivo"]."&txtFechaIni=".$_REQUEST['txtFechaIni']."&txtFechaFin=".$_REQUEST['txtFechaFin']);
    exit;
}



$id = $_GET['id'];
$reg = $_GET['reg'];
$pagina = $_GET['pagina'];

if(!$id and $_REQUEST['reg']){
    $cod_enca=$_REQUEST['reg'];
}
else if(!$id and $_REQUEST['cod_enca']){
    $cod_enca=$_REQUEST['cod_enca'];
}
else {
    $select = "
        select 
            RD.*,
            TT.descripcion nomturno_tipo,
            substring(T.entrada,1,5) nomturno_entrada,
            substring(T.salida,1,5) nomturno_salida,
            substring(T.inicio_descanso,1,5) nomturno_inicio_descanso,
            substring(T.salida_descanso,1,5) nomturno_salida_descanso
        from reloj_detalle RD 
            left join nomturnos T on RD.turno=T.turno_id 
            left join  nomturnos_tipo TT on T.tipo=TT.turnotipo_id
        where id='$id'";
    $result = sql_ejecutar($select);
    $fila = fetch_array($result);
    $cod_enca=$fila["id_encabezado"];    
}


$sql="select * from reloj_encabezado where cod_enca='".$cod_enca."'";
$result_enca = sql_ejecutar($sql);
$reloj_encabezado = fetch_array($result_enca);
$enca_fecha_ini=$reloj_encabezado["fecha_ini"];
$enca_fecha_fin=$reloj_encabezado["fecha_fin"];
$enca_codigo=$reloj_encabezado["cod_enca"];

print "<input type='hidden' id='enca_fecha_ini' value='$enca_fecha_ini' disabled> ";
print "<input type='hidden' id='enca_fecha_fin' value='$enca_fecha_fin' disabled> ";



if ($_POST["guardar"]) {
    if ($_POST['ficha'] != "" && $_POST['fecha'] != "" ) {
    		$pagina = $_POST['pagina'];
        $cod = $_POST['id'];
        $idenc = $_POST['reg'];
        $tipo = $_POST['tipo'];

        if($tipo=="agregar")
        {
            $capataz_res=sql_ejecutar("SELECT nap.valor FROM nomcampos_adic_personal as nap, nomcampos_adicionales as na WHERE nap.id=na.id and upper(descrip) like '%CAPATAZ%' AND upper(nap.valor) LIKE '%SI%' and nap.ficha='".$_POST[ficha]."'");  
            $capataz = fetch_array($capataz_res);  
            $capataz = isset($capataz["valor"])?"1":"0";

            $mod = "INSERT INTO reloj_detalle "
                    . "(id, "
                    . "id_encabezado, "
                    . "ficha, "
                    . "fecha, "
                    . "marcacion_disp_id, "
                    . "estatus, "
                    . "entrada, "
                    . "salmuerzo, "
                    . "ealmuerzo, "
                    . "salida,"
                    . "salida_diasiguiente, "
                    . "ent_emer, "
                    . "sal_emer, "
                    . "tardanza, "
                    . "descextra1, "
                    . "ordinaria, "
                    . "domingo, "
                    . "nacional, "
                    . "extra,  "
                    . "extraext, "
                    . "extranoc, "
                    . "extraextnoc, "
                    . "mixtodiurna, "
                    . "mixtoextdiurna, "
                    . "mixtonoc, "
                    . "mixtoextnoc, "
                    . "emergencia, "
                    . "descansoincompleto, "
                    . "observacion, "
                    . "dialibre,"
                    . "tarea,"
                    . "lluvia,"
                    . "paralizacion_lluvia,"
                    . "altura_menor,"
                    . "altura_mayor,"
                    . "profundidad,"
                    . "tunel,"
                    . "martillo,"
                    . "rastrilleo,"
                    . "otras,"
                    . "turno,"
                    . "horas_teoricas,"
                    . "horas_reales,"
                    . "capataz,"
                    . "descanso_contrato) "
                    . "values "
                    . "('',"
                    . "'$idenc',"
                    . "'".$_POST[ficha]."',"
                    . "'".$_POST[fecha]."',"
                    . "'".$_POST[dispositivo]."',"
                    . "'3',"
                    . "'".$_POST[entrada]."',"
                    . "'".$_POST[salmuerzo]."',"
                    . "'".$_POST[ealmuerzo]."',"
                    . "'".$_POST[salida]."',"
                    . "'".$_POST[salida_diasiguiente]."',"
                    . "'".$_POST[ent_emer]."',"
                    . "'".$_POST[sal_emer]."',"
                    . "'".$_POST[tardanza]."',"
                    . "'".$_POST[descextra1]."',"
                    . "'".$_POST[ordinaria]."',"
                    . "'".$_POST[domingo]."',"
                    . "'".$_POST[nacional]."',"
                    . "'".$_POST[extra]."',"
                    . "'".$_POST[extraext]."',"
                    . "'".$_POST[extranoc]."',"
                    . "'".$_POST[extraextnoc]."',"
                    . "'".$_POST[extramixdiurna]."',"
                    . "'".$_POST[extraextmixdiurna]."',"
                    . "'".$_POST[extramixnoc]."',"
                    . "'".$_POST[extraextmixnoc]."',"
                    . "'".$_POST[emergencia]."',"
                    . "'".$_POST[descansoincompleto]."',"
                    . "'".$_POST[observacion]."',"
                    . "'".$_POST[dialibre]."',"
                    . "'".$_POST[tarea]."',"
                    . "'".$_POST[lluvia]."',"
                    . "'".$_POST[paralizacion_lluvia]."',"
                    . "'".$_POST[altura_menor]."',"
                    . "'".$_POST[altura_mayor]."',"
                    . "'".$_POST[profundidad]."',"
                    . "'".$_POST[tunel]."',"
                    . "'".$_POST[martillo]."',"
                    . "'".$_POST[rastrilleo]."',"
                    . "'".$_POST[otras]."',"
                    . "'".$_POST[turno_id]."',"
                    . "'".$_POST[horas_teoricas]."',"
                    . "'".$_POST[horas_reales]."',"
                    . "'$capataz',"
                    . "'".$_POST[descanso_contrato]."')";


            $mod2 = "fecha ".$_POST['fecha'] . " -entrada " .$_POST['entrada']." -salmuerzo ".$_POST['salmuerzo']." -ealmuerzo ".$_POST['ealmuerzo']." -salida ".$_POST['salida']." -ordinaria ".$_POST['ordinaria']." -extra ".$_POST['extra']." -domingo ".$_POST['domingo']." -tardanza ".$_POST['tardanza']." -extraext ".$_POST['extraext']." -extranoc ".$_POST['extranoc']." -extraextnoc ".$_POST['extraextnoc']." -nacional  ".$_POST['nacional']." -extranac   ".$_POST['extranac']." -extranocnac   ".$_POST['extranocnac']." -descextra1   ".$_POST['descextra1']." -ent_emer  ".$_POST['ent_emer']." -sal_emer  ".$_POST['sal_emer']." -mixtodiurna  ".$_POST[extramixdiurna]." -mixtoextdiurna  ".$_POST[extraextmixdiurna]." -mixtonoc  ".$_POST[extramixnoc]." -mixtoextnoc  ".$_POST[extraextmixnoc]." -emergencia  ".$_POST[emergencia]." -descansoincompleto  ".$_POST[descansoincompleto]." - observacion  ".$_POST[observacion]." -dialibre ".$_POST[dialibre]; 

            $descripcion = "Agregar Marcacion Colaborador ".$_POST[ficha]." ".$mod2;
            $accion      = "insertar";
        }
        else
        {
            $mod = "UPDATE reloj_detalle SET  "
                    . "fecha='" .$_POST['fecha'] . "', "
                    . "marcacion_disp_id='" .$_POST['dispositivo'] . "', "
                    . "estatus='3', "
                    . "entrada='".$_POST['entrada']."', "
                    . "salmuerzo='".$_POST['salmuerzo']."', "
                    . "ealmuerzo='".$_POST['ealmuerzo']."', "
                    . "salida='".$_POST['salida']."',"
                    . "salida_diasiguiente='".$_POST['salida_diasiguiente']."', "
                    . "ordinaria='".$_POST['ordinaria']."', "
                    . "extra='".$_POST['extra']."', "
                    . "domingo='".$_POST['domingo']."', "
                    . "tardanza='".$_POST['tardanza']."', "
                    . "extraext='".$_POST['extraext']."', "
                    . "extranoc='".$_POST['extranoc']."', "
                    . "extraextnoc='".$_POST['extraextnoc']."', "
                    . "nacional= '".$_POST['nacional']."', "
                    . "extranac = '".$_POST['extranac']."', "
                    . "extranocnac = '".$_POST['extranocnac']."', "
                    . "descextra1 = '".$_POST['descextra1']."', "
                    . "ent_emer ='".$_POST['ent_emer']."', "
                    . "sal_emer = '".$_POST['sal_emer']."', "
                    . "mixtodiurna = '".$_POST[extramixdiurna]."', "
                    . "mixtoextdiurna = '".$_POST[extraextmixdiurna]."', "
                    . "mixtonoc = '".$_POST[extramixnoc]."', "
                    . "mixtoextnoc = '".$_POST[extraextmixnoc]."', "
                    . "emergencia = '".$_POST[emergencia]."', "
                    . "descansoincompleto = '".$_POST[descansoincompleto]."', "
                    . "observacion = '".$_POST[observacion]."', "
                    . "dialibre='".$_POST[dialibre]."', "
                    . "tarea='".$_POST[tarea]."', "
                    . "lluvia='".$_POST[lluvia]."', "
                    . "paralizacion_lluvia='".$_POST[paralizacion_lluvia]."', "
                    . "altura_menor='".$_POST[altura_menor]."', "
                    . "altura_mayor='".$_POST[altura_mayor]."', "
                    . "profundidad='".$_POST[profundidad]."', "
                    . "tunel='".$_POST[tunel]."', "
                    . "martillo='".$_POST[martillo]."', "
                    . "rastrilleo='".$_POST[rastrilleo]."', "
                    . "otras='".$_POST[otras]."', "
                    . "turno='".$_POST[turno_id]."', "
                    . "horas_teoricas='".$_POST[horas_teoricas]."', "
                    . "horas_reales='".$_POST[horas_reales]."', "
                    . "descanso_contrato='".$_POST[descanso_contrato]."' "
                    . "WHERE id='$cod'";

$mod2 = "fecha ".$_POST['fecha'] . " -entrada " .$_POST['entrada']." -salmuerzo ".$_POST['salmuerzo']." -ealmuerzo ".$_POST['ealmuerzo']." -salida ".$_POST['salida']." -ordinaria ".$_POST['ordinaria']." -extra ".$_POST['extra']." -domingo ".$_POST['domingo']." -tardanza ".$_POST['tardanza']." -extraext ".$_POST['extraext']." -extranoc ".$_POST['extranoc']." -extraextnoc ".$_POST['extraextnoc']." -nacional  ".$_POST['nacional']." -extranac   ".$_POST['extranac']." -extranocnac   ".$_POST['extranocnac']." -descextra1   ".$_POST['descextra1']." -ent_emer  ".$_POST['ent_emer']." -sal_emer  ".$_POST['sal_emer']." -mixtodiurna  ".$_POST[extramixdiurna]." -mixtoextdiurna  ".$_POST[extraextmixdiurna]." -mixtonoc  ".$_POST[extramixnoc]." -mixtoextnoc  ".$_POST[extraextmixnoc]." -emergencia  ".$_POST[emergencia]." -descansoincompleto  ".$_POST[descansoincompleto]." - observacion  ".$_POST[observacion]." -dialibre ".$_POST[dialibre]; 


            $descripcion = "Editar Marcacion Colaborador ".$_POST[ficha]." ".$mod2;
            $accion      = "editar";
            $edicion = "UPDATE reloj_encabezado SET fecha_edicion = NOW(), usuario_edicion = '".$_SESSION['usuario']."',status='Edicion' where cod_enca='$idenc'";
            $result  = sql_ejecutar($edicion);
        }
        $result  = sql_ejecutar($mod);
        $sql_log = "INSERT INTO log_transacciones (cod_log, descripcion, fecha_hora, modulo, url, accion, valor, usuario, host) 
        VALUES (NULL, '".$descripcion."', now(), 'Control Acceso', 'control_acceso_editar.php', '".$accion."','{$cod}','".$_SESSION['usuario'] ."', '".$host_ip."')";
        
        $res_log = sql_ejecutar($sql_log);
        header("Location: control_acceso_detalle2.php?reg=$reg&pagina=$pagina&buscar=".$_REQUEST["buscar"]."&buscar_dispositivo=".$_REQUEST["buscar_dispositivo"]."&txtFechaIni=".$_REQUEST['txtFechaIni']."&txtFechaFin=".$_REQUEST['txtFechaFin']);
    } else {
        ?>
        <script type="text/javascript">
            alert("ALERTA:  \n Debe introducir todos los datos para guardar.");
        </script>
        <?php
    }
}

$select_turno = "
    SELECT  
        T.turno_id, 
        T.descripcion ,
        TT.descripcion nomturno_tipo,
        substring(T.entrada,1,5) nomturno_entrada,
        substring(T.salida,1,5) nomturno_salida,
        substring(T.inicio_descanso,1,5) nomturno_inicio_descanso,
        substring(T.salida_descanso,1,5) nomturno_salida_descanso,
        substring(T.horas_teoricas,1,5)  nomturno_horas_teoricas,
        substring(T.horas_reales,1,5)    nomturno_horas_reales
    FROM nomturnos T
        left join  nomturnos_tipo TT on T.tipo=TT.turnotipo_id
    ORDER BY 
        T.turno_id ASC";
$result_turno = sql_ejecutar($select_turno);


$select = "select apenom from nompersonal where ficha='".$fila[ficha]."'";
$result2 = sql_ejecutar($select);

$select_dispositivo = "SELECT id_dispositivo, cod_dispositivo, nombre FROM reloj_info"
                    . " ORDER BY cod_dispositivo ASC";
$result_dispositivo = sql_ejecutar($select_dispositivo);

$fila2 = fetch_array($result2);

if($_GET[tipo]=="agregar")
{
    $select = "SELECT ficha, apenom FROM nompersonal WHERE tipnom='".$_SESSION[codigo_nomina]."'"
            . " ORDER BY ficha ASC";
//    echo $select;
    $result = sql_ejecutar($select);
}
?>
<script type="text/javascript">
    function validar_encabezado(){
        var enca_fecha_ini=document.getElementById("enca_fecha_ini").value;
        var enca_fecha_fin=document.getElementById("enca_fecha_fin").value;
        var fecha=document.getElementById("fecha").value;
        if(!(fecha>=enca_fecha_ini && fecha<=enca_fecha_fin)){
            alert("La fecha "+fecha+" se encuentra fuera del rango del encabezado (del "+enca_fecha_ini+" al "+enca_fecha_fin+").");
            return false;
        }
        return true;
    }

    var campo=["entrada","salmuerzo","ealmuerzo","salida","ent_emer","sal_emer","tardanza","descextra1","ordinaria","domingo","nacional","dialibre","extra","extraext","extranoc","extraextnoc","extramixdiurna","extraextmixdiurna","extramixnoc","extraextmixnoc","tarea","lluvia","paralizacion_lluvia","altura_menor","altura_mayor","profundidad","tunel","martillo","rastrilleo","otras","emergencia","descansoincompleto","descanso_contrato"];
    function llenar_00_00(){        
        for(var i=0; i<campo.length; i++) {
            if(!$("#"+campo[i]).val().trim()) $("#"+campo[i]).val("00:00");
        }
    };

    $(function() {
        llenar_00_00();
        for(var i=0; i<campo.length; i++) {
            $("#"+campo[i]).change(function(){
                if(!$(this).val().trim()) $(this).val("00:00");
            });
        }
    });
</script>

<form onsubmit='return validar_encabezado()' action="control_acceso_editar2.php?reg=<?php echo $reg ?>&id=<?php echo $id ?>" method="post" name="frmAgregar" id="frmAgregar" enctype="multipart/form-data">
    <?php 
        titulo("Editar Control de Acceso (Cod: $enca_codigo del $enca_fecha_ini al $enca_fecha_fin)", "", "control_acceso_detalle2.php?reg=$reg&pagina=$pagina&buscar=".$_REQUEST["buscar"]."&buscar_dispositivo=".$_REQUEST["buscar_dispositivo"]."&txtFechaIni=".$_REQUEST['txtFechaIni']."&txtFechaFin=".$_REQUEST['txtFechaFin'], "acceso"); 

    ?>
<div id="content">


<table width="100%" cellspacing="0" border="0" cellpadding="1" align="left">
<tbody>
<tr class="tb-head" >
<td>
<?php
echo $fila2[apenom];
?>
</td>
</tr>
</tbody>
</table>


<table width="100%" cellspacing="0" border="0" cellpadding="1" align="center">
<tbody>
<tr class="tb-head" >
<?php
foreach ($titulos as $nombre) {
 echo "<td><STRONG>$nombre</STRONG></td>";
}
?>
</tr>
         
<tr class="tb-fila">
<td style="padding-right: 10px;">
<input name="id" type="hidden" id="id"  value="<?php echo $fila['id'] ?>"/>
<input name="tipo" type="hidden" id="tipo"  value="<?php echo $_GET['tipo'] ?>"/>
<input name="pagina" type="hidden" id="pagina"  value="<?php echo $_GET['pagina'] ?>"/>
<input name="reg" type="hidden" id="reg"  value="<?php echo $_GET['reg'] ?>"/>
<input name="empresa" type="hidden" id="empresa"  value="<?php echo $empresa ?>"  />
<?php
if($_GET[tipo]=="agregar")
{
?>
<div style="display: flex; width: 100%;">
    <input type="hidden" name="ficha" id="ficha">
    <input type="text" id="ficha_display" list="listaficha" onchange="onChangeFicha(value)" style="width: 100%; border-right: none;" autocomplete="off">
    <img src="../imagenes/cancel.gif" style="background: #FFF; border: 1px solid silver; border-left: none; cursor: pointer;" onclick="onChangeFicha('')" />
    <datalist id="listaficha">
    <select>
    <?php 
    $lista_ficha=[];
    while($fila = fetch_array($result)){
        $tmp=trim($fila[ficha])." -- ".utf8_encode(trim($fila[apenom]));
        $lista_ficha[]=$tmp;
        print "<option>$tmp</option>";
    }
    ?>
    </select>
    </datalist>
    <script type="text/javascript">
        function onChangeFicha(value){
            var lista=<?php print json_encode($lista_ficha)?>;
            var value=String(value).split(" -- ");
            value=value[0];
            for(var i=0;i<lista.length;i++){
                var tmp2=String(lista[i]).split(" -- ");
                if(value==tmp2[0]){
                    document.getElementById("ficha").value=value;
                    return;
                }
            }
            document.getElementById("ficha_display").value="";
            document.getElementById("ficha").value="";
        }
    </script>  
</div> 
<?php
}
else
{
?>
    <input name="ficha" type="text" id="ficha" style="width:100%;" value="<?php echo $fila['ficha'] ?>" maxlength="60" autocomplete="off" readonly='true'>
<?php
}
?>
</td>
<td style="padding-right: 10px;">
    <div style="display: flex; width: 100%;">
        <input type="hidden" name="dispositivo" id="dispositivo">
        <input type="text" id="dispositivo_display" list="listadispositivo" onchange="onChangeDispositivo(value)" style="width: 100%; border-right: none;" autocomplete="off">
        <img src="../imagenes/cancel.gif" style="background: #FFF; border: 1px solid silver; border-left: none; cursor: pointer;" onclick="onChangeDispositivo('')" />
        <datalist id="listadispositivo">
        <select>
        <?php 
        $lista_dispositivo=[];
        while($fila_dispositivo = fetch_array($result_dispositivo)){
            $tmp=trim($fila_dispositivo[cod_dispositivo])." -- ".utf8_encode(trim($fila_dispositivo[nombre]));
            $lista_dispositivo[]=["id"=>$fila_dispositivo['id_dispositivo'], "display"=>$tmp];
            print "<option>$tmp</option>";
        }
        ?>
        </select>
        </datalist>
        <script type="text/javascript">
            var lista_dispositivo=<?php print json_encode($lista_dispositivo)?>;
            function onChangeDispositivo(value){     
                for(var i=0;i<lista_dispositivo.length;i++){
                    if(value==lista_dispositivo[i].display){
                        document.getElementById("dispositivo").value=lista_dispositivo[i].id;
                        return;
                    }
                }
                document.getElementById("dispositivo_display").value="";
                document.getElementById("dispositivo").value="";
            }
            var id_dispositivo_seleted="<?php print $fila['marcacion_disp_id']?>";
            $(function() {
                console.log(id_dispositivo_seleted);
                if(id_dispositivo_seleted){
                    for(var i=0;i<lista_dispositivo.length;i++){
                        if(id_dispositivo_seleted==lista_dispositivo[i].id){
                            document.getElementById("dispositivo").value=lista_dispositivo[i].id;
                            document.getElementById("dispositivo_display").value=lista_dispositivo[i].display;
                            return;
                        }
                    }
                }                
            });
        </script>
    </div>
</td>
<td style="display: flex;">
<input name="fecha" type="text" id="fecha" style="width:80px" onblur="javascript:cargar_horas();" value="<?php echo $fila['fecha']; ?>" maxlength="60" autocomplete="off"><input name="image2" type="image" id="d_fecha" src="../lib/jscalendar/cal.gif" style="height: 18px;width:18px;" /><script type="text/javascript">Calendar.setup({inputField:"fecha",ifFormat:"%Y-%m-%d",button:"d_fecha"});</script>

</td>

<td>
<input name="entrada" type="text" class="formato_time_ss" id="entrada" style="width:90px" step="1" onblur="javascript:cargar_horas();" value="<?php echo $fila['entrada']; ?>" autocomplete="off">
</td>

<td>
<input name="salmuerzo" type="text" class="formato_time_ss" id="salmuerzo" style="width:90px" step="1" onblur="javascript:cargar_horas();" value="<?php echo $fila['salmuerzo']; ?>" autocomplete="off">
</td>

<td>
<input name="ealmuerzo" type="text" class="formato_time_ss" id="ealmuerzo" style="width:90px" step="1" onblur="javascript:cargar_horas();" value="<?php echo $fila['ealmuerzo']; ?>"  autocomplete="off">
</td>

<td>
<input name="salida" type="text" class="formato_time_ss" id="salida" style="width:90px" step="1" onblur="javascript:cargar_horas();" value="<?php echo $fila['salida']; ?>"  autocomplete="off">
</td>

<td>
<!--<input name="salida_diasiguiente" type="text" id="salida_diasiguiente" style="width:50px" onblur="javascript:cargar_horas();" value="<?php echo $fila['salida_diasiguiente']; ?>" maxlength="60" autocomplete="off">-->
<select name="salida_diasiguiente" id="salida_diasiguiente" style="width:50px" onchange="javascript:cargar_horas();">
    <option value=""   <?php echo (strtoupper(trim($fila['salida_diasiguiente']))!="SI"?" selected":"");?>>NO</option>
    <option value="SI" <?php echo (strtoupper(trim($fila['salida_diasiguiente']))=="SI"?" selected":"");?>>SI</option>
</select>    
</td>
<?php
if($empresa=="electron" || $empresa=="zkteco")
{
?>
<td>
<input name="ent_emer" type="text" class="formato_time" id="ent_emer" style="width:90px" onblur="javascript:cargar_horas();" value="<?php echo $fila['ent_emer']; ?>" maxlength="60" autocomplete="off">
</td>

<td>
<input name="sal_emer" type="text" class="formato_time" id="sal_emer" style="width:90px" onblur="javascript:cargar_horas();" value="<?php echo $fila['sal_emer']; ?>" maxlength="60" autocomplete="off">
</td>
<?php
}
?>
<?php
if($empresa=="pandora")
{
?>
<td>
<input name="entrada1" type="text" class="formato_time" id="entrada1" style="width:90px" onblur="javascript:cargar_horas();" value="<?php echo $fila['entrada1']; ?>" maxlength="60" autocomplete="off">
</td>

<td>
<input name="salida1" type="text" class="formato_time" id="salida1" style="width:90px" onblur="javascript:cargar_horas();" value="<?php echo $fila['salida1']; ?>" maxlength="60" autocomplete="off">
</td>
<?php
}
?>

<?php
if($empresa=="jimmy" || $empresa=="electron" || $empresa=="zkteco")
{
?>

<td>
<input name="tardanza" type="text" class="formato_time" id="tardanza" style="width:90px" value="<?php echo $fila['tardanza']; ?>" maxlength="60" autocomplete="off">
</td>
<td>

<input name="descextra1" type="text" class="formato_time" id="descextra1" style="width:90px" value="<?php echo $fila['descextra1']; ?>" maxlength="60" autocomplete="off">
</td>
<?php
}
?>
 </tr>
</tbody>
</table>
<br>

<div style="display: flex; align-items: center; justify-content: center; flex-wrap: wrap;">
<table width="90%" cellspacing="10px"  cellpadding="2px" align="center"  class="bordes info1" style="margin-bottom: 10px; border: 2px solid #FF5722; background: #FF5722;">
    <tr>
        <td style="white-space: nowrap; padding-left: 10px; color: white;">
            <STRONG>Turno&nbsp;&nbsp;</STRONG>
        </td>
        <td style="display: flex; ">
            <input name="turno_id" type="text" id="turno_id" style="width:30px; background: #ececec; height: 35px;" value="<?php echo $fila['turno'] ?>" maxlength="60" autocomplete="off" readonly>
            <?php

            $horario_actual="";
            if($fila["nomturno_entrada"] and $fila["nomturno_salida"]){
                $horario_actual=$fila["nomturno_entrada"]." - ".$fila["nomturno_salida"];
                if($fila["nomturno_inicio_descanso"] and $fila["nomturno_salida_descanso"] and $fila["nomturno_inicio_descanso"]!="00:00" and $fila["nomturno_salida_descanso"]!="00:00" and $fila["nomturno_inicio_descanso"]!="00:00:00" and $fila["nomturno_salida_descanso"]!="00:00:00"){
                    $horario_actual=$fila["nomturno_entrada"]." - ".$fila["nomturno_inicio_descanso"]." / ".$fila["nomturno_salida_descanso"]." - ".$fila["nomturno_salida"];
                }
                if($fila["nomturno_tipo"]){
                    $horario_actual=$fila["nomturno_tipo"].": ".$horario_actual;
                }
            }

            ?>
            <select id="select_turno_id" style="min-width:80px; background: #FFF; flex:1; height: 35px;" onchange="onChangeTurno()" title='<?php print $horario_actual;?>'>
                <option value="0">&nbsp;</option>
                <?php      
                $turno_json=[];
                $turno_actual=""; 
                $turno_actual_input=""; 
                $turno_horas_reales="";          
                $turno_horas_teoricas="";          
                while($row = fetch_array($result_turno)){    
                    $add="";   
                    if($fila['turno']==$row["turno_id"]){
                        $add="selected";
                    }                           

                    if($row["nomturno_entrada"] and $row["nomturno_salida"]){
                        $horario=$row["nomturno_entrada"]." - ".$row["nomturno_salida"];
                        $horario_input="<input type='text' value='".$row["nomturno_entrada"]."' class='formato_time' style='width:50px;color:#000;font-weight: 400;'> - <input type='text' value='".$row["nomturno_salida"]."' class='formato_time' style='width:50px;color:#000;font-weight: 400;'>";
                        if($row["nomturno_inicio_descanso"] and $row["nomturno_salida_descanso"] and $row["nomturno_inicio_descanso"]!="00:00" and $row["nomturno_salida_descanso"]!="00:00" and $row["nomturno_inicio_descanso"]!="00:00:00" and $row["nomturno_salida_descanso"]!="00:00:00" and $row["nomturno_inicio_descanso"]!=$row["nomturno_salida_descanso"]){
                            $horario=$row["nomturno_entrada"]." - ".$row["nomturno_inicio_descanso"]." / ".$row["nomturno_salida_descanso"]." - ".$row["nomturno_salida"];
                            $horario_input="<input type='text' value='".$row["nomturno_entrada"]."' class='formato_time' style='width:50px;color:#000;font-weight: 400;'> - <input type='text' value='".$row["nomturno_inicio_descanso"]."' class='formato_time' style='width:50px;color:#000;font-weight: 400;'>  / <input type='text' value='".$row["nomturno_salida_descanso"]."' class='formato_time' style='width:50px;color:#000;font-weight: 400;'> - <input type='text' value='".$row["nomturno_salida"]."' class='formato_time' style='width:50px;color:#000;font-weight: 400;'>";
                        }
                        if($row["nomturno_tipo"]){
                            $horario=$row["nomturno_tipo"].": ".$horario;
                            $horario_input=$row["nomturno_tipo"].": ".$horario_input;
                        }
                    }

                    if($fila['turno']==$row["turno_id"]){
                        $turno_actual="$horario";                        
                        $turno_actual_input="$horario_input";                        
                        $turno_horas_reales=$row["nomturno_horas_reales"];          
                        $turno_horas_teoricas=$row["nomturno_horas_teoricas"]; 
                    }

                    $turno_json[$row["turno_id"]]=[
                        "horario"               => $horario_input,
                        "turno_horas_reales"    => $row["nomturno_horas_reales"],
                        "turno_horas_teoricas"  => $row["nomturno_horas_teoricas"]
                    ];
                    print "<option value='".$row["turno_id"]."' $add title='$horario'>".$row["turno_id"]." - ".$row["descripcion"]."</option>";
                }
                ?>
            </select>
            <script type="text/javascript">
                var turno_json=<?php print json_encode($turno_json)?>;
                function onChangeTurno(){
                    var id="<?php echo trim($id) ?>";
                    var ficha=document.getElementById("ficha").value;
                    var fecha=document.getElementById("fecha").value;
                    var turno_id=document.getElementById("select_turno_id").value;

                    ficha=$.trim(ficha);
                    fecha=$.trim(fecha);

                    if(!ficha){
                        return;
                    }
                    if(!moment(fecha, "YYYY-MM-DD", true).isValid()){
                        alert("La fecha es inválida.");
                        return;
                    }

                    if(confirm("¿Desea modificar el turno de la persona y realizar el recalculo correspondiente?")){
                        //alert("control_acceso_editar2.php?reg=<?php echo $reg ?>&id=<?php echo $id ?>&ficha="+ficha+"&fecha="+fecha+"&editar_turno=1");return;

                        if(!id){//si es nuevo

                            var ajax_editar_turno=abrirAjax()
                            ajax_editar_turno.open("GET", "control_acceso_editar2.php?turno_id="+turno_id+"&fecha="+fecha+"&ficha="+ficha+"&editar_turno=1&editar_turno_ajax=1", true);
                            ajax_editar_turno.onreadystatechange=function() 
                            {
                                if (ajax_editar_turno.readyState==1)
                                {
                                    cargar_horas();
                                }
                                if (ajax_editar_turno.readyState==4)
                                {
                                    cargar_horas();
                                }
                            }
                            ajax_editar_turno.send(null);                            
                        }
                        else{
                            window.location.href="control_acceso_editar2.php?reg=<?php echo $reg ?>&id=<?php echo $id ?>&ficha="+ficha+"&fecha="+fecha+"&editar_turno=1&turno_id="+turno_id+"<?php print "&pagina=$pagina&buscar=".$_REQUEST["buscar"]."&buscar_dispositivo=".$_REQUEST["buscar_dispositivo"]."&txtFechaIni=".$_REQUEST['txtFechaIni']."&txtFechaFin=".$_REQUEST['txtFechaFin']?>";
                        }

                    }
                    else{
                        document.getElementById("select_turno_id").value=document.getElementById("turno_id").value;
                    }

                    
                }
            </script>  
        </td>
        <td style="white-space: nowrap; padding: 0 20px; color: white; font-weight: 600;">
            <span id="turno_tipo">
            <?php 
                //print $fila["nomturno_tipo"]?$fila["nomturno_tipo"]:"";
                print "$turno_actual_input";
            ?>
            </span>           
            
        </td>
        <td style="white-space: nowrap; padding: 0 20px 0 0; color: white; font-weight: 600;">
            Horas Reales: <input name="turno_horas_reales" type="text" class="formato_time" id="turno_horas_reales" style="width:50px;color:#000;font-weight: 400;" value="<?php echo $turno_horas_reales; ?>" maxlength="60" autocomplete="off" readonly>&nbsp;&nbsp;&nbsp;&nbsp;Horas Teoricas: <input name="turno_horas_teoricas" type="text" class="formato_time" id="turno_horas_teoricas" style="width:50px;color:#000;font-weight: 400;" value="<?php echo $turno_horas_teoricas; ?>" maxlength="60" autocomplete="off" readonly>
            
        </td>
    </tr>    
</table>

<table width="90%" cellspacing="10px"  cellpadding="2px" align="center"  class="bordes info1" style="margin-bottom: 10px;">
<tr >
<td style="white-space: nowrap; padding: 5px 10px; ">
<STRONG>--Horas Reales:</STRONG>
</td>
<td style="border-right: 1px dashed;padding-right: 5px;">
<input name="horas_reales" type="text" class="formato_time" id="horas_reales" style="width:50px" value="<?php echo $fila['horas_reales']; ?>" maxlength="60" autocomplete="off">
<input name="horas_teoricas" type="hidden" class="formato_time" id="horas_teoricas" style="width:50px" value="<?php echo $fila['horas_teoricas']; ?>" maxlength="60" autocomplete="off">
</td>
<td style="white-space: nowrap; padding: 5px 10px;">
<STRONG>--Ordinaria:</STRONG>
</td>
<td>
<input name="ordinaria" type="text" class="formato_time" id="ordinaria" style="width:50px" value="<?php echo $fila['ordinaria']; ?>" maxlength="60" autocomplete="off">
</td>
<td style="white-space: nowrap; padding: 5px 10px;">
<STRONG>--Domingo:</STRONG>
</td>
<td>
<input name="domingo" type="text" class="formato_time" id="domingo" style="width:50px" value="<?php echo $fila['domingo']; ?>" maxlength="60" autocomplete="off">
</td>
<td style="white-space: nowrap; padding: 5px 10px;">
<STRONG>--Nacional:</STRONG>
</td>
<td>
<input name="nacional" type="text" class="formato_time" id="nacional" style="width:50px" value="<?php echo $fila['nacional']; ?>" maxlength="60" autocomplete="off">
</td>
<td style="white-space: nowrap; padding: 5px 10px;">
<STRONG>--Descanso:</STRONG>
</td>
<td>
<input name="dialibre" type="text" class="formato_time" id="dialibre" style="width:50px" value="<?php echo $fila['dialibre']; ?>" maxlength="60" autocomplete="off">
</td>
<td style="white-space: nowrap; padding: 5px 10px;">
<STRONG>--Acumulado Semanal:</STRONG>
</td>
<td>
<input name="acum_semanal" type="text" id="acum_semanal" style="width:50px" value="<?php echo '' ?>" maxlength="60" autocomplete="off" disabled>
</td>
</tr>
</table>
</div>
<br>
<table width="35%" cellspacing="10px" class="bordes" cellpadding="5px" align="center">
    <tr >
    <td style="padding: 5px 10px 0 10px;">
    Hora Extra Diurna
    </td>
    <td style="padding: 5px 10px 0 10px;">
    <input name="extra" type="text" class="formato_time" id="extra" style="width:100px" value="<?php echo $fila['extra']; ?>" maxlength="5" autocomplete="off">
    </td>
    <td id="porcentaje_extra" class="estilo_porcentaje" style="padding: 5px 10px 0 10px;"></td>
    </tr>
    <tr>
    <td style="padding: 0 10px;">
    Hora Extra Diurna con Exceso
    </td>
    <td style="padding: 0 10px;">
    <input name="extraext" type="text" class="formato_time" id="extraext" style="width:100px" value="<?php echo $fila['extraext']; ?>" maxlength="5" autocomplete="off">
    </td>
    <td id="porcentaje_extraext" class="estilo_porcentaje" style="padding: 0 10px;"></td>
    </tr>

    <tr>
    <td style="padding: 0 10px;">Hora Extra Nocturna
    </td>
    <td style="padding: 0 10px;">
    <input name="extranoc" type="text" class="formato_time" id="extranoc" style="width:100px" value="<?php echo $fila['extranoc']; ?>" maxlength="5" autocomplete="off">
    </td>
    <td id="porcentaje_extranoc" class="estilo_porcentaje" style="padding: 0 10px;"></td>
    </tr>
    <tr>
    <td style="padding: 0 10px;">Hora Extra Nocturna con Exceso
    </td>
    <td style="padding: 0 10px;">
    <input name="extraextnoc" type="text" class="formato_time" id="extraextnoc" style="width:100px" value="<?php echo $fila['extraextnoc']; ?>" maxlength="5" autocomplete="off">
    </td>
    <td id="porcentaje_extraextnoc" class="estilo_porcentaje" style="padding: 0 10px;"></td>    
    </tr>
    <tr>
    <td style="padding: 0 10px;">
    Hora Extra Mixto Diurna
    </td>
    <td style="padding: 0 10px;">
    <input name="extramixdiurna" type="text" class="formato_time" id="extramixdiurna" style="width:100px" value="<?php echo $fila['mixtodiurna']; ?>" maxlength="5" autocomplete="off">
    </td>
    <td id="porcentaje_extramixdiurna" class="estilo_porcentaje" style="padding: 0 10px;"></td>
    </tr>
    <tr>
    <td style="padding: 0 10px;">
    Hora Extra Mixto Diurna con Exceso
    </td>
    <td style="padding: 0 10px;">
    <input name="extraextmixdiurna" type="text" class="formato_time" id="extraextmixdiurna" style="width:100px" value="<?php echo $fila['mixtoextdiurna']; ?>" maxlength="5" autocomplete="off">
    </td>
    <td id="porcentaje_extraextmixdiurna" class="estilo_porcentaje" style="padding: 0 10px;"></td>
    </tr>
    <tr>
    <td style="padding: 0 10px;">Hora Extra Mixto Nocturna
    </td>
    <td style="padding: 0 10px;">
    <input name="extramixnoc" type="text" class="formato_time" id="extramixnoc" style="width:100px" value="<?php echo $fila['mixtonoc']; ?>" maxlength="5" autocomplete="off">
    </td>
    <td id="porcentaje_extramixnoc" class="estilo_porcentaje" style="padding: 0 10px;"></td>
    </tr>
    <tr>
    <td style="padding: 0 10px 5px 10px;">Hora Extra Mixto Nocturna con Exceso
    </td>
    <td style="padding: 0 10px 5px 10px;">
    <input name="extraextmixnoc" type="text" class="formato_time" id="extraextmixnoc" style="width:100px" value="<?php echo $fila['mixtoextnoc']; ?>" maxlength="5" autocomplete="off">
    </td>
    <td id="porcentaje_extraextmixnoc" class="estilo_porcentaje" style="padding: 0 10px 5px 10px;"></td>
    </tr>
</table>
<br>
<table width="30%" cellspacing="10px"  cellpadding="2px" align="center" style="<?php print ($empresa_tipo!=3?"display: none;":"");?>">
<tr >
<td>
<STRONG>-- Construcción</STRONG>
</td>
</tr>
</table>
<table width="35%" cellspacing="10px" class="bordes" cellpadding="5px" align="center" style="<?php print ($empresa_tipo!=3?"display: none;":"");?>">
    <tr >
    <td>
        Hora Tarea&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    </td>
    <td>
    <input name="tarea" type="text" class="formato_time" id="tarea" style="width:100px" value="<?php echo $fila['tarea']; ?>" maxlength="5" autocomplete="off">
    </td>
    </tr>
    <tr>
    <td>
    Hora Lluvia
    </td>
    <td>
    <input name="lluvia" type="text" class="formato_time" id="lluvia" style="width:100px" value="<?php echo $fila['lluvia']; ?>" maxlength="5" autocomplete="off">
    </td>
    </tr>

    <tr>
    <td>Hora Paralizacion Lluvia
    </td>
    <td>
    <input name="paralizacion_lluvia" type="text" class="formato_time" id="paralizacion_lluvia" style="width:100px" value="<?php echo $fila['paralizacion_lluvia']; ?>" maxlength="5" autocomplete="off">
    </td>
    </tr>
    <tr>
    <td>Hora Altura (16% < 125m)
    </td>
    <td>
    <input name="altura_menor" type="text" class="formato_time" id="altura_menor" style="width:100px" value="<?php echo $fila['altura_menor']; ?>" maxlength="5" autocomplete="off">
    </td>
    </tr>
    <tr>
    <td>
    Hora Altura (20% > 125m)
    </td>
    <td>
    <input name="altura_mayor" type="text" class="formato_time" id="altura_mayor" style="width:100px" value="<?php echo $fila['altura_mayor']; ?>" maxlength="5" autocomplete="off">
    </td>
    </tr>
    <tr>
    <td>
    Hora Profundidad (15%)
    </td>
    <td>
    <input name="profundidad" type="text" class="formato_time" id="profundidad" style="width:100px" value="<?php echo $fila['profundidad']; ?>" maxlength="5" autocomplete="off">
    </td>
    </tr>
    <tr>
    <td>Hora Tunel
    </td>
    <td>
    <input name="tunel" type="text" class="formato_time" id="tunel" style="width:100px" value="<?php echo $fila['tunel']; ?>" maxlength="5" autocomplete="off">
    </td>
    </tr>
    <tr> 
    <td>Hora Martillo (10%)
    </td>
    <td>
    <input name="martillo" type="text" class="formato_time" id="martillo" style="width:100px" value="<?php echo $fila['martillo']; ?>" maxlength="5" autocomplete="off">
    </td>
    </tr>
    <tr> 
    <td>Hora Rastrilleo (10%)
    </td>
    <td>
    <input name="rastrilleo" type="text" class="formato_time" id="rastrilleo" style="width:100px" value="<?php echo $fila['rastrilleo']; ?>" maxlength="5" autocomplete="off">
    </td>
    </tr>
</table>

<br>
<table width="30%" cellspacing="10px"  cellpadding="2px" align="center">
<tr >
<td>
<STRONG>-- Otras</STRONG>
</td>
</tr>
</table>
<table width="35%" cellspacing="10px" class="bordes" cellpadding="5px" align="center">
    <tr >
    <td>
    Otras Horas&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    </td>
    <td>
    <input name="otras" type="text" class="formato_time" id="otras" style="width:100px" value="<?php echo $fila['otras']; ?>" maxlength="5" autocomplete="off">
    </td>
    </tr>
    
</table>

<br>
<?php
if($empresa=="electron" || $empresa=="zkteco" )
{
?>
<table width="30%" cellspacing="10px"  cellpadding="2px" align="center">
<tr >
<td>
<STRONG>-- Emergencia</STRONG>
</td>
</tr>
</table>

<table width="35%" cellspacing="10px" class="bordes" cellpadding="5px" align="center">

<tr>
<td>Llamado de Emergencia&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
</td>
<td>
<input name="emergencia" type="text" class="formato_time" id="emergencia" style="width:100px" value="<?php echo $fila['emergencia']; ?>" maxlength="60" autocomplete="off">
</td>
</tr>
<tr>
<td>Descanso Incompleto
</td>
<td>
<input name="descansoincompleto" type="text" class="formato_time" id="descansoincompleto" style="width:100px" value="<?php echo $fila['descansoincompleto']; ?>" maxlength="60" autocomplete="off">
</td>
</tr>

</table>
<?php
}
?>

<br>
<?php
if($empresa=="zkteco" )
{
?>
<table width="30%" cellspacing="10px"  cellpadding="2px" align="center">
<tr >
<td>
<STRONG>-- Descanso (Contrato Colectivo)</STRONG>
</td>
</tr>
</table>

<table width="35%" cellspacing="10px" class="bordes" cellpadding="5px" align="center">


<tr>
<td>Descanso&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
</td>
<td>
<input name="descanso_contrato" type="text" class="formato_time" id="descanso_contrato" style="width:100px" value="<?php echo $fila['descanso_contrato']; ?>" maxlength="60" autocomplete="off">
</td>
</tr>

</table>
<?php
}
?>

<br>
<table width="35%" cellspacing="10px"  cellpadding="5px" align="center">

<tr>
<td colspan="13" align="center">
Observacion
<input name="observacion" type="text" id="observacion" style="width:300px" value="<?php echo $fila['observacion']; ?>" maxlength="60" autocomplete="off">
</td>

</tr>

<tr>
<td colspan="13" align="center">
Estado
<input name="estado" type="text" id="estado" style="width:150px" value="" maxlength="60" autocomplete="off">
</td>

</tr>
<tr>
<tr class="tb-fila">
<td width="400"><div align="center">
<input type="hidden" name="pagina" id="pagina"  value="<?php echo $pagina?>">
<!--Campos para manterter la busqueda actual en la lista de detalle-->
<input type="hidden" name="buscar" id="buscar"  value="<?php echo $_REQUEST["buscar"]?>">
<input type="hidden" name="buscar_dispositivo" id="buscar_dispositivo"  value="<?php echo $_REQUEST["buscar_dispositivo"]?>">
<input type="hidden" name="txtFechaIni" id="txtFechaIni"  value="<?php echo $_REQUEST['txtFechaIni']?>">
<input type="hidden" name="txtFechaFin" id="txtFechaFin"  value="<?php echo $_REQUEST['txtFechaFin']?>">
<input type="submit" name="guardar" id="guardar"  value="Guardar">
</div></td>

</tr>

</tr>
</table>

</div>
</form>
<script type="text/javascript" src="../libs/js/moment.min.js"></script>
<script type="text/javascript" src="../lib/jquery.maskedinput.js"></script>
<script type="text/javascript">
    jQuery(function($){
        $.mask.definitions['H']='[012]';
        $.mask.definitions['N']='[012345]';
        $.mask.definitions['n']='[0123456789]';

        $(".formato_time").mask("Hn:Nn");
        $(".formato_time_ss").mask("Hn:Nn:Nn");

        $(".formato_time, .formato_time_ss").live("blur",function(){
            var v=$(this).val();
            if(v.indexOf("_") == -1){              
                var h = v.split(":")[0];
                if(h*1>23)
                    $(this).val("");
            }
        }); 

        mostrar_porcentajes();       

    });
</script>
</body>
</html>