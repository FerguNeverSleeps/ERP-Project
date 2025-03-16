<?php
session_start();
ob_start();
?>
<?php
require_once '../lib/config.php';
require_once '../lib/common.php';
include ("../header.php");
include ("func_bd.php");
$conexion = conexion();

$id=$_REQUEST["id"];
if(isset($_REQUEST["capataz"]) and $id){
    sql_ejecutar("update reloj_detalle set capataz='".$_REQUEST["capataz"]."' where id='$id'");
}








$sql="select * from reloj_encabezado where cod_enca='".$_REQUEST['reg']."'";
$result_enca = sql_ejecutar($sql);
$reloj_encabezado = fetch_array($result_enca);
$enca_fecha_ini=$reloj_encabezado["fecha_ini"];
$enca_fecha_fin=$reloj_encabezado["fecha_fin"];
$enca_status=$reloj_encabezado["status"];
$enca_status=trim($enca_status);
$enca_status=strtolower($enca_status);
$enca_codigo=$reloj_encabezado["cod_enca"];


$readonly=false;
if($enca_status=="aprobado" or $enca_status=="preaprobado"){
    $readonly=true;
}

//echo $conexion;
$url = "control_acceso_detalle2";
$modulo = "Control de Asistencia (Cod: $enca_codigo del $enca_fecha_ini al $enca_fecha_fin)";
$tabla = "reloj_detalle";

//$titulos = array("Ficha", "Nombre", "Dispositivo", "Fecha","Turno","Entrada","S. Almu","E. Almu","Inicio Ext.","Salida","Ordinarias","Tardanza","Desc. Ext.","Extras","Extras Ext", "Noc", "Noc Ext","Dom","Nac");
//$indices = array("ficha", "apenom","nombre", "fecha","turno","entrada","salmuerzo","ealmuerzo","hora_inicio","salida","ordinaria","tardanza","descextra1","extra", "extraext", "extranoc", "extraextnoc","domingo","nacional");

$titulos = array("Ficha", "Nombre", "Dispositivo", "Fecha","Turno","Entrada","S. Almu","E. Almu","Salida","Ordinarias","Tardanza","Extras","Extras Ext", "Noc", "Noc Ext","Dom","Nac");
$indices = array("ficha", "apenom","nombre", "fecha","turno","entrada","salmuerzo","ealmuerzo","salida","ordinaria","tardanza","extra", "extraext", "extranoc", "extraextnoc","domingo","nacional");

$conexion = conexion();
$cedula = @$_GET['cedula'];
$eliminar = @$_GET['eliminar'];
$tipob = @$_GET['tipo'];
$des = @$_GET['des'];
$pagina = @$_GET['pagina'];
$busqueda = @$_GET['busqueda'];
$reg = $_GET['reg'];
$sWhere = " ";
if(isset($_SESSION['region']) AND $_SESSION['region']!= '0')
{
    $sWhere .= "AND nompersonal.codnivel1 = '{$_SESSION['region']}' ";
}
if(isset($_SESSION['departamento']) AND $_SESSION['departamento']!= '0')
{
    $sWhere .= "AND nompersonal.codnivel2 = '{$_SESSION['departamento']}' ";
}
if(isset($_SESSION['nivel3']) AND $_SESSION['nivel3']!= '0')
{
    $sWhere .= "AND nompersonal.codnivel3 = '{$_SESSION['nivel3']}' ";
}
if(isset($_SESSION['nivel4']) AND $_SESSION['nivel4']!= '0')
{
    $sWhere .= "AND nompersonal.codnivel4 = '{$_SESSION['nivel4']}' ";
}
if(isset($_SESSION['nivel5']) AND $_SESSION['nivel5']!= '0')
{
    $sWhere .= "AND nompersonal.codnivel5 = '{$_SESSION['nivel5']}' ";
}

/*
if (isset($_REQUEST['buscar']) || $tipob != NULL) {

    if (!$tipob) {
        $tipob = $_REQUEST['palabra'];
        $des = $_REQUEST['buscar'];
        $dispositivo = $_REQUEST['buscar_dispositivo'];
        $fini = fecha_sql($_REQUEST['txtFechaIni']);
        $ffin = fecha_sql($_REQUEST['txtFechaFin']);
        $busqueda = $_REQUEST['busqueda'];
        $reg = $_REQUEST['reg'];
    }

//codigo innecesario
//la solucion a todo este codigo esta luego con la variable $add en el sql para cada caso
//si todo funciona borrar todo este segmento


    if ($busqueda == 'ficha') {
        
        
        $consulta = "SELECT *,nompersonal.apenom,reloj_info.cod_dispositivo, reloj_info.nombre "
                        . " FROM " . $tabla . " "
                        . " INNER JOIN nompersonal on " . $tabla . ".ficha=nompersonal.ficha "
                        . " LEFT JOIN reloj_info on " . $tabla . ".marcacion_disp_id=reloj_info.id_dispositivo "
                        . " WHERE id_encabezado=" . $reg . " "
                        . " AND nompersonal.ficha=".$des."  "
                        . " AND fecha BETWEEN '$fini' AND '$ffin' "
                        . " AND reloj_info.cod_dispositivo=".$dispositivo."  "
                        . " ORDER by reloj_info.cod_dispositivo ASC, nompersonal.ficha, fecha ASC";
        
        if(($_REQUEST['buscar']=='' || $_REQUEST['buscar']=="" || $_REQUEST['buscar']==NULL) && 
           ($_REQUEST['txtFechaIni']=='' || $_REQUEST['txtFechaIni']=="" || $_REQUEST['txtFechaIni']==NULL) &&
           ($_REQUEST['txtFechaFin']=='' || $_REQUEST['txtFechaFin']=="" || $_REQUEST['txtFechaFin']==NULL) &&
           ($_REQUEST['buscar_dispositivo']=='' || $_REQUEST['buscar_dispositivo']=="" || $_REQUEST['buscar_dispositivo']==NULL) )
        {
             $consulta = "SELECT *,nompersonal.apenom,reloj_info.cod_dispositivo, reloj_info.nombre"
                        . " FROM " . $tabla . " "
                        . " INNER JOIN nompersonal on " . $tabla . ".ficha=nompersonal.ficha "
                        . " LEFT JOIN reloj_info on " . $tabla . ".marcacion_disp_id=reloj_info.id_dispositivo "
                        . " WHERE id_encabezado=" . $reg." ".$sWhere." "
                        . " ORDER by reloj_info.cod_dispositivo ASC, nompersonal.ficha, fecha ASC";
//            echo $consulta;
        }
        else if(($_REQUEST['buscar']=='' || $_REQUEST['buscar']=="" || $_REQUEST['buscar']==NULL) && 
           ($_REQUEST['txtFechaIni']=='' || $_REQUEST['txtFechaIni']=="" || $_REQUEST['txtFechaIni']==NULL) &&
           ($_REQUEST['txtFechaFin']=='' || $_REQUEST['txtFechaFin']=="" || $_REQUEST['txtFechaFin']==NULL))
        {
             $consulta = "SELECT *,nompersonal.apenom,reloj_info.cod_dispositivo, reloj_info.nombre"
                        . " FROM " . $tabla . " "
                        . " INNER JOIN nompersonal on " . $tabla . ".ficha=nompersonal.ficha "
                        . " LEFT JOIN reloj_info on " . $tabla . ".marcacion_disp_id=reloj_info.id_dispositivo "
                        . " WHERE id_encabezado=" . $reg." ".$sWhere." "
                        . " AND reloj_info.cod_dispositivo=".$dispositivo."  "
                        . " ORDER by reloj_info.cod_dispositivo ASC, nompersonal.ficha, fecha ASC";
//            echo $consulta;
        }
        else if(($_REQUEST['buscar']=='' || $_REQUEST['buscar']=="" || $_REQUEST['buscar']==NULL) &&
           ($_REQUEST['buscar_dispositivo']=='' || $_REQUEST['buscar_dispositivo']=="" || $_REQUEST['buscar_dispositivo']==NULL) )
        {
             $consulta = "SELECT *,nompersonal.apenom,reloj_info.cod_dispositivo, reloj_info.nombre"
                        . " FROM " . $tabla . " "
                        . " INNER JOIN nompersonal on " . $tabla . ".ficha=nompersonal.ficha "
                        . " LEFT JOIN reloj_info on " . $tabla . ".marcacion_disp_id=reloj_info.id_dispositivo "
                        . " WHERE id_encabezado=" . $reg." ".$sWhere." "
                        . " AND fecha BETWEEN '$fini' AND '$ffin' "
                        . " ORDER by reloj_info.cod_dispositivo ASC, nompersonal.ficha, fecha ASC";
//            echo $consulta;
        }
        else if($_REQUEST['buscar']=='' || $_REQUEST['buscar']=="" || $_REQUEST['buscar']==NULL)
        {
            $consulta = "SELECT *,nompersonal.apenom,reloj_info.cod_dispositivo, reloj_info.nombre "
                        . " FROM " . $tabla . " "
                        . " INNER JOIN nompersonal on " . $tabla . ".ficha=nompersonal.ficha "
                        . " LEFT JOIN reloj_info on " . $tabla . ".marcacion_disp_id=reloj_info.id_dispositivo "
                        . " WHERE id_encabezado=" . $reg . " "
                        . " AND fecha BETWEEN '$fini' AND '$ffin' "
                        . " AND reloj_info.cod_dispositivo=".$dispositivo."  "
                        . " ORDER by reloj_info.cod_dispositivo ASC, nompersonal.ficha, fecha ASC";
        }
        else if(($_REQUEST['txtFechaIni']=='' || $_REQUEST['txtFechaIni']=="" || $_REQUEST['txtFechaIni']==NULL) &&
           ($_REQUEST['txtFechaFin']=='' || $_REQUEST['txtFechaFin']=="" || $_REQUEST['txtFechaFin']==NULL) )
        {
            $consulta = "SELECT *,nompersonal.apenom,reloj_info.cod_dispositivo, reloj_info.nombre "
                        . " FROM " . $tabla . " "
                        . " INNER JOIN nompersonal on " . $tabla . ".ficha=nompersonal.ficha "
                        . " LEFT JOIN reloj_info on " . $tabla . ".marcacion_disp_id=reloj_info.id_dispositivo "
                        . " WHERE id_encabezado=" . $reg . " "
                        . " AND nompersonal.ficha=".$des."  "
                        . " AND reloj_info.cod_dispositivo=".$dispositivo."  "
                        . " ORDER by reloj_info.cod_dispositivo ASC, nompersonal.ficha, fecha ASC";
                        
                        //print "entro";
        }
        
//        echo $consulta;
        
    } else {
        switch ($tipob) {
            case "exacta":
                $consulta = buscar_exacta($tabla, $des, $busqueda);
                break;
            case "todas":
                $consulta = buscar_todas($tabla, $des, $busqueda);
                break;
            case "cualquiera":
                $consulta = buscar_cualquiera($tabla, $des, $busqueda);
                break;
        }
    }
    //echo $consulta;
} else {
    $consulta = "SELECT *,nompersonal.apenom,reloj_info.cod_dispositivo, reloj_info.nombre"
            . " FROM " . $tabla . " "
            . " INNER JOIN nompersonal on " . $tabla . ".ficha=nompersonal.ficha "
            . " LEFT JOIN reloj_info on " . $tabla . ".marcacion_disp_id=reloj_info.id_dispositivo "
            . " WHERE id_encabezado=" . $reg." ".$sWhere." "
            . " ORDER by reloj_info.cod_dispositivo ASC, nompersonal.ficha, fecha ASC";
   //echo $consulta;
}

*/

$reg = $_REQUEST['reg'];

//solución a todo el segmento anterios
$add="";
if(isset($_REQUEST["buscar"]))
    if($_REQUEST["buscar"])
        $add.=" AND (nompersonal.ficha='".$_REQUEST["buscar"]."' or upper(nompersonal.apenom) like '%".strtoupper($_REQUEST["buscar"])."%')";

if(isset($_REQUEST["buscar_dispositivo"]))
    if($_REQUEST["buscar_dispositivo"])
        $add.=" AND (trim(TRAILING '\n' FROM reloj_info.cod_dispositivo) like '".$_REQUEST["buscar_dispositivo"]."' or upper(reloj_info.nombre) like '%".strtoupper($_REQUEST["buscar_dispositivo"])."%') ";

if(isset($_REQUEST["txtFechaIni"]) and isset($_REQUEST["txtFechaFin"]))
    if($_REQUEST["txtFechaIni"] and $_REQUEST["txtFechaFin"]){
        $fini = fecha_sql($_REQUEST['txtFechaIni']);
        $ffin = fecha_sql($_REQUEST['txtFechaFin']);
        $add.=" AND fecha BETWEEN '$fini' AND '$ffin' ";
    }

$consulta = "SELECT " . $tabla . ".*,nompersonal.apenom,reloj_info.cod_dispositivo, reloj_info.nombre, nomturnos.descripcion as turno, ".$tabla.".turno turno_id "
                        . " FROM " . $tabla . " "
                        . " INNER JOIN nompersonal on " . $tabla . ".ficha=nompersonal.ficha "
                        . " LEFT JOIN reloj_info on " . $tabla . ".marcacion_disp_id=reloj_info.id_dispositivo "
                        . " LEFT JOIN nomturnos on (" . $tabla . ".turno=nomturnos.turno_id)  "
                        . " WHERE id_encabezado=" . $reg." ".$sWhere." $add"
                        . " ORDER by nompersonal.ficha ASC, reloj_info.cod_dispositivo ASC, " . $tabla . ".fecha ASC";

//fin solucion al segmento anterior
//print $consulta;

if ($_GET['accion'] == 'eliminar') {
    $id = $_GET['id'];
    $var_sql = "delete from " . $tabla . " WHERE id ='" . $id . "'";
    $rs = query($var_sql, $conexion);
}
#echo $consulta . " este es el valor que muestra ";
$num_paginas = obtener_num_paginas($consulta);
$pagina = obtener_pagina_actual($pagina, $num_paginas);

$resultado = paginacion($pagina, $consulta);

$select_turno = "
    SELECT  
        T.turno_id, 
        T.descripcion ,
        TT.descripcion nomturno_tipo,
        substring(T.entrada,1,5) nomturno_entrada,
        substring(T.salida,1,5) nomturno_salida,
        substring(T.inicio_descanso,1,5) nomturno_inicio_descanso,
        substring(T.salida_descanso,1,5) nomturno_salida_descanso
    FROM nomturnos T
        left join  nomturnos_tipo TT on T.tipo=TT.turnotipo_id
    ORDER BY 
        T.turno_id ASC";
$result_turno = sql_ejecutar($select_turno);

$select_turno="<select class='select-turno'>";
$select_turno.="<option value='0'>&nbsp;</option>";
while($row = fetch_array($result_turno)){   
    //$select_turno.= "<option value='".$row["turno_id"]."'>".(strtoupper(trim($row["turno_id"]))!=strtoupper(trim($row["descripcion"]))?"".$row["turno_id"]." - ":"").$row["descripcion"]."</option>";

    if($row["nomturno_entrada"] and $row["nomturno_salida"]){
        $horario=$row["nomturno_entrada"]." - ".$row["nomturno_salida"];
        if($row["nomturno_inicio_descanso"] and $row["nomturno_salida_descanso"] and $row["nomturno_inicio_descanso"]!="00:00" and $row["nomturno_salida_descanso"]!="00:00" and $row["nomturno_inicio_descanso"]!="00:00:00" and $row["nomturno_salida_descanso"]!="00:00:00" and $row["nomturno_inicio_descanso"]!=$row["nomturno_salida_descanso"]){
            $horario=$row["nomturno_entrada"]." - ".$row["nomturno_inicio_descanso"]." / ".$row["nomturno_salida_descanso"]." - ".$row["nomturno_salida"];
        }
        if($row["nomturno_tipo"]){
            $horario=$row["nomturno_tipo"].": ".$horario;
        }
    }

    $select_turno.= "<option value='".$row["turno_id"]."' $add title='$horario'>".$row["turno_id"]." - ".$row["descripcion"]."</option>";
}
//$select_turno.="<option value='-1'>[Cancelar/Cerrar Edición]</option>";
$select_turno.="<select/>";

?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title></title>
        <style type="text/css">
            .alinear{
                display: flex; 
                align-items: center; 
                padding-left: 10px; 
                height: 30px;
            }
            .botones_buscar{
                padding-left: 20px;
            }
            .botones_buscar > table {
                width: 100%;
                margin: 6px 0;
            }
            .botones_acciones{
                text-align: right;
                padding-right: 10px;
            }

            .botones_acciones > button {
                width: 120px;
            }
            .tabla-detalle {

            }
            .tabla-detalle tr {
                cursor: pointer;
                border: 1px solid #E0E0E0;
            }

            .tabla-detalle td {
                padding: 1px 2px;
            }

            .tabla-detalle .fila-base {
                background: #FFF;
                min-height: 22px;
            }

            .tabla-detalle .fila-base:hover {
                background: #fffad2 !important;
            }

            .tabla-detalle .fila-base.fila-par {
                background: #F5F5F5;
            }

            .estatus-1{/*Generado por nacional*/
                color: #8bc34a;
            }
            .estatus-2{/*Generado por asignar horas*/
                color: #ffc107;
            }
            .estatus-3{/*Edicion manual*/
                color: #2196f3;
            }
            .estatus-4{/*Procesado*/
                color: #009688;
            }

            
            .page-content {
                padding: 0px !important;
            }

            .celda-turno {                
                padding-right: 25px;
                position: relative;
                border-left: 1px solid rgba(0,0,0,0);
                border-right: 1px solid rgba(0,0,0,0);
                width: 150px;
            }            

            .celda-turno:hover {
                color: #FF5722 !important;
                border: 1px solid #FFAB91;
            }

            .celda-turno > i.fa-pencil{
                opacity: 0;
                color: #FF5722;
                position: absolute;
                right: 5px;
                top: 3px;
                font-size: 18px;
            }

            .celda-turno:hover > i.fa-pencil {
                opacity: 1;
            }

            select.select-turno {
                width: 100%;                
                height: 100%;                
                background-color: #FFF;
                outline: #FF5722 solid 2px;
                outline: none;
                border: none;
                flex: 1;
            }

            select.select-turno:focus {      
                border: none;          
                outline: #FF5722 solid 2px;
                outline: none;
            }

            .celda-turno .container-select-turno {
                position: absolute;
                width: 100%;
                height: 100%;
                top: 0;
                left: 0;
                border: none;
                background: #FF5722;
                outline: 2px solid #FF5722;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .celda-turno .container-select-turno i{
                width: 20px;
                /*height: 100%;*/
                color: white;
                line-height: 100%;
                font-size: 16px;
                text-align: center;
                vertical-align: middle;
                cursor: pointer;
                margin-top: -2px;
            }
            
        </style>
        <script type="text/javascript">
            var global_select_turno="<?php print $select_turno;?>";
            function recalcular_horas(){
                window.open("asistencias_recalcular_horas.php?cod_enca=<?php print $reg;?>","VentanaRecalcularHoras", "width=600,height=350,scrollbars=no");
            }
            function cargar_txt(){
                window.open("asistencias_cargar_txt.php?cod_enca=<?php print $reg;?>","VentanaCargarTXT", "width=600,height=400,scrollbars=no");
            }
            function generar_nacional(){
                window.open("asistencias_generar_nacional.php?cod_enca=<?php print $reg;?>","VentanaGenerarNacional", "width=700,height=750,scrollbars=no");
            }
             function asignar_horas(){
                window.open("asistencias_asignar_horas.php?cod_enca=<?php print $reg;?>","VentanaAsignarHoras", "width=1000,height=750,scrollbars=no");
            }
            function actualizar_capataz(id,value){
                if(value===true) v=1;
                else v=0;
                <?php
                print "window.location.href='?id='+id+'&capataz='+v+'&pagina=" . $pagina . "&reg=" . $reg. "&buscar=".$_REQUEST["buscar"]."&buscar_dispositivo=".$_REQUEST["buscar_dispositivo"]."&txtFechaIni=".$_REQUEST['txtFechaIni']."&txtFechaFin=".$_REQUEST['txtFechaFin']."';";
                ?>
            }
            function modal_editar_turno(reloj_detalle_id){
                window.open("control_acceso_detalle_modal_editar_turno.php?reloj_detalle_id="+reloj_detalle_id,"VentanaEditarTurno", "width=600,height=400,scrollbars=no");
            }

            function inline_editar_turno(reloj_detalle_id){
                //borrar los select abiertos anteriormente
                $(".celda-turno > .container-select-turno").remove();

                var row=$("#row-"+reloj_detalle_id);
                //console.log(row.data("registro-turno"));                

                var celda_turno=row.find(".celda-turno");
                celda_turno.append("<div class='container-select-turno'>"+global_select_turno+"<i class='fa fa-close'></i></div>");
                console.log(celda_turno.find("select.select-turno"));

                var select_emb=celda_turno.find("select.select-turno");
                var turno_actual=row.data("registro-turno");
                console.log("Turno Actual: "+turno_actual);
                $(select_emb).val(turno_actual);

                $(select_emb).change(function(){
                    var nuevo_turno_id=$(select_emb).val();
                    if(nuevo_turno_id=="-1"){
                        console.log("elimimar select");
                        $(".celda-turno > .container-select-turno").remove();
                        return;
                    }
                    if(nuevo_turno_id=="0"){
                        $(select_emb).val(turno_actual);
                        alert("Debe seleccionar un turno válido.");
                        return;
                    }

                    if(confirm("¿Desea modificar el turno de la persona y realizar el recalculo correspondiente?")){

                        var ficha=row.data("registro-ficha");
                        var fecha=row.data("registro-fecha");
                        var reloj_detalle_id=row.data("registro-id");

                        ficha=$.trim(ficha);
                        fecha=$.trim(fecha);

                        if(!ficha){
                            return;
                        }
                        //if(!moment(fecha, "YYYY-MM-DD", true).isValid()){
                        //    alert("La fecha es inválida.");
                        //    return;
                        //}

                        var ajax_editar_turno=abrirAjax()
                        ajax_editar_turno.open("GET", "control_acceso_detalle_modal_editar_turno.php?reloj_detalle_id="+reloj_detalle_id+"&turno_id="+nuevo_turno_id+"&fecha="+fecha+"&ficha="+ficha+"&procesar=1&editar_turno_ajax=1", true);
                        ajax_editar_turno.onreadystatechange=function() 
                        {
                            if (ajax_editar_turno.readyState==1)
                            {
                                window.document.control_acceso_detalle2.submit();
                            }
                            if (ajax_editar_turno.readyState==4)
                            {
                                window.document.control_acceso_detalle2.submit();
                            }
                        }
                        ajax_editar_turno.send(null);

                    }
                });//change select

                var icono_cerrar=celda_turno.find("i.fa-close");
                console.log(icono_cerrar);
                $(icono_cerrar).click(function(){
                    $(".celda-turno .container-select-turno").remove();
                });


            }
        </script>
    </head>
    <body>
        <form name="<?php echo $url ?>" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" target="_self">
            <?php //titulo($modulo, "cargar_control_acceso_detalle.php?reg=$reg&pagina=$pagina", "control_acceso2.php", "acceso"); 
            $btnAgregar="";
            if($_SESSION["ca_det_agr"] and $readonly==false)
                $btnAgregar= "control_acceso_editar2.php?reg=$reg&pagina=$pagina&tipo=agregar&buscar=".$_REQUEST["buscar"]."&buscar_dispositivo=".$_REQUEST["buscar_dispositivo"]."&txtFechaIni=".$_REQUEST['txtFechaIni']."&txtFechaFin=".$_REQUEST['txtFechaFin'];

            titulo($modulo, $btnAgregar, "control_acceso2.php", "acceso"); 
            ?>
            <table width="100%">                
                <tr height="20">                    
                    <td width="1">
                        <div class="alinear">
                            <input  style="height:25px;font-size:12pt;" type="text" name="buscar"  id="buscar" size="10" value="<?php echo $_REQUEST['buscar']; ?>" autocomplete="off">
                            <SELECT name="busqueda" style="height:25px;">
                                <option value="ficha">Ficha</option>
                            </SELECT>
                        </div>
                    </td>                    
                    <td width="1">
                        <div class="alinear">
                            <input  style="height:25px;font-size:12pt;" type="text" name="buscar_dispositivo"  id="buscar_dispositivo" size="10" value="<?php echo $_REQUEST['buscar_dispositivo']; ?>"  autocomplete="off">
                            <SELECT name="busqueda_dispositivo" style="height:25px;">
                                <option value="dispositivo">Dispositivo</option>
                            </SELECT> 
                        </div>                       
                    </td>
                    <td width="1" class='botones_buscar' rowspan="2">
                        <?php btn('show_all', $url . ".php?pagina=" . $pagina . "&reg=" . $reg); ?>
                        <?php btn('search', $url, 1); ?>
                    </td>                    
                    <td rowspan="2" class='botones_acciones'>
                        <div>
                            <?php if($_SESSION["ca_det_txt"] and $readonly==false):?>
                            <button type="button" style="white-space: nowrap;" onclick="cargar_txt()"><img src="../imagenes/icon_txt.png" width="16" height="16"> Cargar TXT&nbsp;&nbsp;&nbsp;</button>
                            <?php endif;?>
                            <?php if($_SESSION["ca_det_rec"] and $readonly==false):?>
                            <button type="button" style="white-space: nowrap;" onclick="recalcular_horas()"><img src="../imagenes/generar.png" width="16" height="16"> Recalcular</button>
                            <?php endif;?>
                            <?php if($_SESSION["ca_det_nac"] and $readonly==false):?>
                            <button type="button" style="white-space: nowrap;" onclick="generar_nacional()"><img src="../imagenes/generar.png" width="16" height="16"> Nacional&nbsp;&nbsp;&nbsp;</button>
                            <?php endif;?>
                            <?php if($_SESSION["ca_det_asig"] and $readonly==false):?>
                            <button type="button" style="white-space: nowrap;" onclick="asignar_horas()"><img src="../imagenes/generar.png" width="16" height="16"> Asignar Horas</button>
                            <?php endif;?>
                        </div>
                    </td>
                </tr>                
                <tr>
                    <td>    
                        <div style="padding: 5px 0 0 10px; margin-bottom: -3px; display: none;">Desde:</div>
                        <div class="alinear">
        					<input name="txtFechaIni" type="text" id="txtFechaIni" size="10" style="height:25px;font-size:12pt;" placeholder="Desde" value="<?php echo $_REQUEST['txtFechaIni']; ?>" maxlength="60" onblur="javascript:actualizar('txtFechaIni','fila_edad');">
                  			<input name="image2" type="image" id="d_fechaini" src="../lib/jscalendar/cal.gif" style="height: 25px;width:25px;" />
          					<script type="text/javascript">Calendar.setup({inputField:"txtFechaIni",ifFormat:"%d/%m/%Y",button:"d_fechaini"});</script>
                        </div>
              		</td>
                    <td>
                        <div style="padding: 5px 0 0 10px; margin-bottom: -3px; display: none;">Hasta:</div>
                        <div class="alinear">
        					<input name="txtFechaFin" type="text" id="txtFechaFin" size="10" style="height:25px;font-size:12pt;" placeholder="Hasta" value="<?php echo $_REQUEST['txtFechaFin']; ?>" maxlength="60" onblur="javascript:actualizar('txtFechaFin','fila_edad');">
                  			<input name="image2" type="image" id="d_fechafin" src="../lib/jscalendar/cal.gif"  style="height: 25px;width:25px;"/>
          					<script type="text/javascript">Calendar.setup({inputField:"txtFechaFin",ifFormat:"%d/%m/%Y",button:"d_fechafin"});</script>
                        </div>          								
				    </td>
                </tr>                
            </table>
            <input type="hidden" id="reg" name="reg" value="<?php echo $reg ?>"/>
            
            <table width="100%" cellspacing="0" border="0" cellpadding="1" align="center" class="tabla-detalle">
                <tbody>
                    <tr class="tb-head" height="30">
                        <?php
                        foreach ($titulos as $nombre) {
                            echo "<td><STRONG>$nombre</STRONG></td>";
                        }
                        ?>
<!--                        <td  style='width: 1px; text-align:center;'><STRONG>Capataz</STRONG></td>-->
                        <td></td>
                        <td></td>
                    </tr>
                    <?php
                    if ($num_paginas != 0) {
                        $i = 0;
                        while ($fila = mysqli_fetch_array($resultado))
                        {
                        	$color="";
									if(($fila[entrada]=="")||($fila[salida]==""))
										$color="color:red;";
									if((($fila[salmuerzo]!="")&&($fila[ealmuerzo]==""))||(($fila[salmuerzo]=="")&&($fila[ealmuerzo]!="")))
										$color="color:red;";
                            $i++;
                            $cls="fila-base";
                            if ($i % 2 == 0)
                                $cls.=" fila-par";
                                //$cls.=" tb-fila";
                            if($fila["estatus"]=="1")
                                $cls.=" estatus-1";
                            else if($fila["estatus"]=="2")
                                $cls.=" estatus-2";                                                                       
                            else if($fila["estatus"]=="3")
                                $cls.=" estatus-3"; 
                            else if($fila["estatus"]=="4")
                                $cls.=" estatus-4";  
                                
                            $id = $fila["id"];
                            print "<tr class='$cls' id='row-".$id."' data-registro-id='$id' data-registro-ficha='".$fila["ficha"]."' data-registro-fecha='".$fila["fecha"]."' data-registro-turno='".$fila["turno_id"]."'>"; 
                                foreach ($indices as $campo) {
                                    //$nom_tabla=mysqli_fetch_field_direct($resultado, $campo)->name;
                                    $add_icon="";
                                    $cls_celda="";
                                    $estilo="";
                                    $var = $fila[$campo];
                                    if($campo=="apenom")
                                        $var = utf8_encode($fila[$campo]);
                                    else if($campo=="fecha")
                                        $estilo = "white-space: nowrap;";                                    
                                    if(!in_array($campo, ["ficha"/*,"apenom","nombre","fecha"*/]))
                                        $estilo.="$color";

                                    if($campo=="turno" and $_SESSION["ca_det_edit"] and $readonly==false){
                                        $cls_celda.="celda-turno";
                                        $add_icon="&nbsp;<i class='fa fa-pencil icon-editar-turno' onclick=\"inline_editar_turno('".$id."')\"></i>";
                                        //$add_icon="&nbsp;<i class='fa fa-pencil icon-editar-turno' onclick=\"modal_editar_turno('".$id."')\"></i>";
                                    }
                                    
                                    print "<td style=\"$estilo\" class='$cls_celda'>{$var}{$add_icon}</td>";
                                }
//                                print "<td style='width: 1px; text-align:center;'><input type='checkbox' onchange='actualizar_capataz($id,this.checked)' ".($fila["capataz"]=="1"?"checked":"")."></td>";

                                if($_SESSION["ca_det_edit"] and $readonly==false)
                                icono("control_acceso_editar2.php?id=" . $id . "&pagina=" . $pagina . "&reg=" . $reg. "&buscar=".$_REQUEST["buscar"]."&buscar_dispositivo=".$_REQUEST["buscar_dispositivo"]."&txtFechaIni=".$_REQUEST['txtFechaIni']."&txtFechaFin=".$_REQUEST['txtFechaFin'], "Editar Control", "edit.gif");

                                if($_SESSION["ca_det_elim"] and $readonly==false)
                                icono("javascript:if(confirm('Esta seguro que desea eliminar el registro ?')){document.location.href='control_acceso_detalle2.php?id=" . $id . "&pagina=" . $pagina . "&accion=eliminar&reg=" . $reg . "&buscar=".$_REQUEST["buscar"]."&buscar_dispositivo=".$_REQUEST["buscar_dispositivo"]."&txtFechaIni=".$_REQUEST['txtFechaIni']."&txtFechaFin=".$_REQUEST['txtFechaFin']."';}", "Eliminar Control", "delete.gif");

                                if($readonly==true){
                                    print "<td></td>";
                                    print "<td><img src='' height='22' width='22' style='opacity: 0;'/></td>";
                                }

                                echo"</tr>";
                            }
                        } else {
                            echo"<tr><td>No existen registro con la busqueda especificada</td></tr>";
                        }
                        cerrar_conexion($conexion);
                        ?>
                </tbody>
            </table>
            <?php /*pie_pagina($url, $pagina, "&tipo=" . $tipob . "&des=" . $des . "&busqueda=" . $busqueda."&reg=".$reg, $num_paginas); */?>
            <?php pie_pagina($url, $pagina, "reg=".$reg."&buscar=".$_REQUEST["buscar"]."&buscar_dispositivo=".$_REQUEST["buscar_dispositivo"]."&txtFechaIni=".$_REQUEST['txtFechaIni']."&txtFechaFin=".$_REQUEST['txtFechaFin'], $num_paginas); ?>
        </form>
        <div style="margin: 10px 20px; background: #FFF;border: 1px solid #CCCCCC; padding:5px; font-weight: bold; width: 150px;">
            <span class="estatus-3">Edición Manual</span><br>
            <span class="estatus-1">Día Nacional</span><br>
            <span class="estatus-2">Asignar Horas</span>
        </div>
    </body>
</html>
