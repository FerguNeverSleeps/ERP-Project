<?php
session_start();
ob_start();
include ("../header4.php");

require_once '../lib/config.php';
require_once '../lib/common.php';
//include ("../header.php");
include ("func_bd.php");
require("../procesos/PHPMailer_5.2.4/class.phpmailer.php");
include("../procesos/PHPMailer_5.2.4/class.smtp.php");
$conexion=conexion();
$host_ip = $_SERVER['REMOTE_ADDR'];

//echo $conexion;
$url      = "control_acceso2";
$modulo   = "Control de Acceso";
$tabla    = "reloj_encabezado";
$titulos  = array("Cod.","Fecha inicio", "Fecha fin", "Fecha Registro");
$indices  = array("cod_enca","fecha_ini", "fecha_fin", "fecha_reg");

$conexion = conexion();
$cedula   = @$_GET['cedula'];
$eliminar = @$_GET['eliminar'];
$tipob    = @$_GET['tipo'];
$des      = @$_GET['des'];
$pagina   = @$_GET['pagina'];
$busqueda = @$_GET['busqueda'];


error_reporting(E_ALL ^ E_DEPRECATED);
if ($_GET['vaciar'] == 1) {
  	$vaciar="TRUNCATE TABLE reloj_procesar";
	$query=query($vaciar,$conexion);
   ?>
	<script type="text/javascript">
	alert("Proceso Vaciado Exitosamente!!");
	</script>
	<?php
}

if ($_GET['listo'] == 1) {
   //echo "<div align='center' style=\"background-color : #84225b; color : #fdfdfd; font-family : 'Arial Black'; font-size : 15px;\">Archivo Procesado Correctamente!</div>";
   ?>
	<script type="text/javascript">
	alert("Archivo cargado Exitosamente!!");
	</script>
	<?php
}

if ($_GET['listo'] == 2) {
    //echo "<div align='center' style=\"background-color : #84225b; color : #fdfdfd; font-family : 'Arial Black'; font-size : 15px;\">Existen registros de entradas y salidas incompletos, Por favor revise!</div>";
    
    ?>
	<script type="text/javascript">
	alert("Existen registros de entradas y salidas incompletos, Por favor revise los registros resaltados en ROJO!");
	</script>
	<?php
}

if ($_GET['listo'] == 3) {
   //echo "<div align='center' style=\"background-color : #84225b; color : #fdfdfd; font-family : 'Arial Black'; font-size : 15px;\">Archivo Procesado Correctamente!</div>";
   ?>
	<script type="text/javascript">
	alert("Archivo Procesado Exitosamente!!");
	</script>
	<?php
}

if ($_GET['listo'] == 4) {
   //echo "<div align='center' style=\"background-color : #84225b; color : #fdfdfd; font-family : 'Arial Black'; font-size : 15px;\">Archivo Procesado Correctamente!</div>";
   ?>
	<script type="text/javascript">
	alert("DesPreaprobacion Exitosa");
	</script>
	<?php
}

if ($_GET['listo'] == 5) {
   //echo "<div align='center' style=\"background-color : #84225b; color : #fdfdfd; font-family : 'Arial Black'; font-size : 15px;\">Archivo Procesado Correctamente!</div>";
   ?>
	<script type="text/javascript">
	alert("Desaprobacion Exitosa");
	</script>
	<?php
}

$add="";
if(isset($_REQUEST["search_anio"]))
{
    if($_REQUEST["search_anio"]!=="Todos")
    {
        $add=" (a.fecha_ini like '".$_REQUEST["search_anio"]."-%' or a.fecha_fin like '".$_REQUEST["search_anio"]."-%') AND ";
    }
}


if (isset($_POST['buscar']) || $tipob != NULL) {
    if (!$tipob) {
        $tipob = $_POST['palabra'];
        $des = $_POST['buscar'];
        $busqueda = $_POST['busqueda'];
    }
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
} else {
    //echo "cod: ".$id;
    if ($_GET[accion] == 'eliminar')     
    {
        $datos="SELECT
            A.cod_enca,
            A.fecha_reg,
            A.fecha_ini,
            A.fecha_fin
        FROM
             reloj_encabezado as A
        WHERE A.cod_enca='$_GET[id]'";
        
        $rs = query($datos,$conexion);
        $fila=fetch_array($rs);
        $cod_enca=$fila[cod_enca];
        $fecha_reg=$fila[fecha_reg];
        $fecha_ini=$fila[fecha_ini];
        $fecha_fin=$fila[fecha_fin];
        
        $query               = "select * from nomempresa";
        $result              = query($query,$conexion);
        $row                 = fetch_array($result);
        global $fecha_reg, $fecha_ini, $fecha_fin;
        $correo_rrhh     = $row[correo_rrhh];
        $correo_planilla     = $row[correo_planilla];
        $correo_sistemas1     = $row[correo_sistemas1];
        $correo_sistemas2     = $row[correo_sistemas2];
        $correo_sistemas2_password     = $row[correo_sistemas2_password];
        //PARAMETROS ENVIO CORREO
        $mail             = new PHPMailer();
        $mail->IsSMTP();
        $email1            =$correo_rrhh;
        $email2           =$correo_planilla;
        $email3           =$correo_sistemas1;
        //$email4           ='epoperez@gmail.com';

        $mail->SMTPAuth   = true;
        $mail->isSMTP();
        $mail->SMTPDebug  = 0;
        $mail->Host       = "smtp.gmail.com";
        $mail->Port       = 465;
        $mail->SMTPSecure = 'ssl';
        $mail->Username   = $correo_sistemas2;
        $mail->Password   = $correo_sistemas2_password;
        $mail->From       = $correo_sistemas2;
        $mail->FromName   = "Amaxonia Planilla";
        $tit='Control de Asistencias - Eliminacion de Encabezado';

        $mail->Subject = $tit;
        $mail->AddAddress ($email1);

        //$mail->AddAddress ($email1);
        if($email1!=="" && $email1!==0)
        {
        //    echo "AQUI";
        //    exit;
            $mail->AddAddress ($email1);
        }
        //echo $email1;
        //exit;
        if($email2!=="" && $email2!==0)
        {
            $mail->AddAddress ($email2);
        }
        if($email3!=="" && $email3!==0)
        {
            $mail->AddAddress ($email3);
        }
        
        $descripcion = "Eliminar registro de reloj encabezado";
        $var_sql = "delete from " . $tabla . " WHERE cod_enca ='" . $_GET[id] . "'";
        $rs      = query($var_sql, $conexion);
        
        $var_sql = "delete from reloj_detalle WHERE id_encabezado ='" . $_GET[id] . "'";
        $rs      = query($var_sql, $conexion);
        
        $asunto="El Encabezado de Control de Asistencias Código <strong>".$cod_enca."</strong><br> "
            . "de Fecha: <strong>".$fecha_reg."</strong> del: <strong>".$fecha_ini."</strong> al: <strong>".$fecha_fin."</strong><br> "
            . "Ha sido Eliminado por el usuario: <strong>".$_SESSION['usuario']."</strong><br>"
            . "En la fecha: <strong>".date('Y-m-d H:i:s')."</strong>";

        $mail->Body = $asunto;
        $mail->IsHTML(true);

        if (!$mail->send()) {
            $msg = "Mailer Error: " . $mail->ErrorInfo;
        } else {
            $msg = "Message sent!";
        }
        $mail->ClearAddresses();

        $sql_log  = "INSERT INTO log_transacciones 
                    (cod_log, 
                    descripcion, 
                    fecha_hora, 
                    modulo, 
                    url, 
                    accion, 
                    valor, 
                    usuario, 
                    host) 
                    VALUES 
                    (NULL, 
                    'Control de Asistencia - Eliminacion Encabezado', 
                    now(), 
                    'Control Acceso', 
                    'control_acceso2.php',
                    'eliminar',
                    '{$_GET[id]}',"
                    . "'".$_SESSION['usuario'] ."', "
                    . "'".$host_ip."')";
        $res_log  = query($sql_log,$conexion);
//        $sql_log = "INSERT INTO log_transacciones (cod_log, descripcion, fecha_hora, modulo, url, accion, valor, usuario, host) 
//        VALUES (NULL, '".$descripcion."', now(), 'Control Acceso', 'control_acceso2.php', 'Eliminar','$_GET[id]','".$_SESSION['nombre'] ."','".$host_ip."')";
//        
//        $res_log = query($sql_log,$conexion);
        
    }

$consulta = "select a. * , b.descrip as planilla, (select numBisemana from bisemanas where fechaInicio=a.fecha_ini and fechaFin=a.fecha_fin limit 1) bisemana "
                . "FROM reloj_encabezado AS a
                   LEFT JOIN nomtipos_nomina AS b ON b.codtip = a.tipo_nomina
                   "
                . "ORDER BY cod_enca DESC";
    //echo $consulta;
}
//echo $consulta." este es el valor que muestra ";



$num_paginas  = obtener_num_paginas($consulta);
$pagina       = obtener_pagina_actual($pagina, $num_paginas);
$resultado    =mysqli_query($conexion,$consulta); 
//$resultado2 = paginacion($pagina, $consulta);



$consultass   ="select id_encabezado from reloj_procesar";
$res1         =mysqli_query($conexion,$consultass); 
$i=0;
while($enca = mysqli_fetch_array($res1))
{
    $id_encabezado[$i]=$enca['id_encabezado'];
    $i++;
}
$totaly=$i;

?>
    <div class="page-container">
        <div class="page-content-wrapper">
            <div class="page-content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet box blue">
                            <div class="portlet-title">
                                <div class="caption">
                                    Control de Asistencia - Períodos
                                </div>
                                <div class="actions">
                                    <?php if($_SESSION['ca_enc_agr']):?>
                                    <a class="btn btn-sm blue"  onclick="javascript: window.location='cargar_control_acceso2.php'">
                                         <i class="fa fa-plus"></i> Agregar
                                    </a>
                                    <?php endif;?>
                                    <?php if($_SESSION['eliminar_control']):?>
                                    <a class="btn btn-sm blue"  onclick="javascript: window.location='control_acceso2.php?vaciar=1'">
                                        <i class="fa fa-trash"></i> Vaciar Proceso
                                    </a>
                                    <?php endif;?>
<!--                                    <a class="btn btn-sm blue"  onclick="javascript: window.location='menu_transacciones.php'">
                                        <i class="fa fa-arrow-left"></i> Regresar
                                    </a>-->
                                </div>
                            </div>
                            <div class="portlet-body">
                                <div id="div_search_situ" style="display: inline;">
                                        <select id="search_anio" class="form-control input-inline input-small" onchange="cargar_anio()">
                                                <option value="Todos">Año</option>
                                                <?php 
                                                        $sql_anio = "SELECT DISTINCT(YEAR(fecha_reg)) as anio FROM reloj_encabezado";
                                                        $res_anio  =mysqli_query($conexion,$sql_anio); 

                                                        while($fila = mysqli_fetch_array($res_anio))
                                                        { ?>
                                                                <option value="<?php echo $fila['anio']; ?>" <?php if($_REQUEST["search_anio"]==$fila['anio']) echo "selected";?> ><?php echo $fila['anio']; ?></option>
                                                          <?php
                                                        }
                                                ?>										
                                        </select>
                                </div>
                                <form name="<?php echo $url ?>" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" target="_self">
                                    <table class="table table-striped table-bordered table-response" id="table_datatable">
                                        <thead>
                                            <tr>
                                                <th>Cod</th>
                                                <th>Tipo Planilla</th>
                                                <th>Fecha Inicio</th>
                                                <th>Fecha Fin</th>
                                                <th>Fecha Creación</th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody style="font-size:11px;">
                                        <?php 
                                        while ($fila = mysqli_fetch_array($resultado)) 
                                        {
                                            $coll=0;
                                            $j=0;
                                            while($j<$totaly)
                                            {
                                                if($id_encabezado[$j]==$fila['cod_enca'])
                                                {
                                                    $coll = 1; 
                                                }
                                                $j++;
                                            }
                                            if($coll==1){ $color="style='color:green'"; }
                                            else { $color="style='color:black'"; }
                                        ?>
                                        <tr <?php echo $color ?>>
                                            <td><?php echo $fila['cod_enca']; ?></td>
                                            <td><?php echo $fila['planilla'].($fila['bisemana']?" #".$fila['bisemana']:""); ?></td>
                                            <td><?php echo fecha($fila['fecha_ini']); ?></td>
                                            <td><?php echo fecha($fila['fecha_fin']); ?></td>
                                            <td><?php echo fecha($fila['fecha_reg']); ?></td>
                                            <?php 
                                                $id = $fila['cod_enca'];
                                                if($_SESSION['detalle_control'])
                                                {
                                                    icono("control_acceso_detalle2.php?reg=".$id."&pagina=".$pagina,"Detalle Control","add.gif");
                                                }
                                                else 
                                                {
                                                     echo "<td></td>";

                                                }
                                                
                                                if($_SESSION['control_acceso_preaprobar'])
                                                {
                                                    if($fila['status']=="Pendiente" || $fila['status']=="Edicion")
                                                    {
                                                        if($coll != 1 )
                                                        {
                                                          icono("control_acceso_preaprobar.php?reg=" . $id . "&fecha_ini=" . $fila['fecha_ini'] . "&fecha_fin=" . $fila['fecha_fin'] . "&fecha_reg=" . $fila['fecha_reg'], "Preaprobar", "../paginas/imagenes/ok.gif");
                                                        }
                                                        else 
                                                        {

                                                            echo "<td></td>";
                                                            echo "<td></td>";

                                                        }
                                                    }
                                                    
                                                    if($fila['status']=="Preaprobado")
                                                    {
                                                        if($coll != 1 )
                                                        {
                                                          icono("control_acceso_despreaprobar.php?reg=" . $id . "&fecha_ini=" . $fila['fecha_ini'] . "&fecha_fin=" . $fila['fecha_fin'] . "&fecha_reg=" . $fila['fecha_reg'], "Des-Preaparobar", "../paginas/imagenes/back.gif");
                                                        }
                                                        else 
                                                        {
                                                             echo "<td></td>";
                                                             echo "<td></td>";

                                                        }
                                                    }
                                                    
                                                    if($fila['status']=="Aprobado")
                                                    {
                                                       
                                                        echo "<td></td>"; 
                                                        echo "<td></td>";                                                       
                                                    }
                                                }
                                                else 
                                                {

                                                    echo "<td></td>";

                                                }
                                                
                                               
                                                
                                                //if($_SESSION['procesar_planilla'] && $fila['status']=="Preaprobado")
                                                if($_SESSION['procesar_planilla'])
                                                {
                                                    if($fila['status']=="Preaprobado")
                                                    {
                                                        if($coll != 1 )
                                                        {
                                                          icono("procesar_para_nomina2.php?reg=" . $id . "&fecha_ini=" . $fila['fecha_ini'] . "&fecha_fin=" . $fila['fecha_fin'] . "&fecha_reg=" . $fila['fecha_reg'], "Aprobar para Planilla", "../paginas/imagenes/thumbs-up-icon.png");
                                                          
                                                          icono("procesar_para_nomina_panama.php?reg=" . $id . "&fecha_ini=" . $fila['fecha_ini'] . "&fecha_fin=" . $fila['fecha_fin'] . "&fecha_reg=" . $fila['fecha_reg'], "Aprobar para Planilla", "../paginas/imagenes/1.png");
                                                        }
                                                        else 
                                                        {                                                     echo "<td></td>";

                                                             echo "<td></td>";

                                                        }
                                                    }
                                                    if($fila['status']=="Aprobado")
                                                    {
                                                        if($coll != 1 )
                                                        {
                                                          icono("control_acceso_desaprobar.php?reg=" . $id . "&fecha_ini=" . $fila['fecha_ini'] . "&fecha_fin=" . $fila['fecha_fin'] . "&fecha_reg=" . $fila['fecha_reg'], "Desaprobar", "../paginas/imagenes/cancel.gif");
                                                        }
                                                        else 
                                                        {                                              

                                                             echo "<td></td>";

                                                        }
                                                    }
                                                    if($fila['status']=="Pendiente" || $fila['status']=="Edicion")
                                                    {                                                     echo "<td></td>";

                                                        
                                                        echo "<td></td>";

                                                        
                                                    }
                                                }
                                                else
                                                {
                                                     echo "<td></td>";                                                     echo "<td></td>";


                                                }                                               
                                                
                                                

                                                if($_SESSION['eliminar_control'])
                                                {
                                                    if($fila['status']!=="Aprobado")
                                                    {
                                                        if($coll == 0 )
                                                        {
                                                            icono("javascript:if(confirm('Esta seguro que desea eliminar el registro ?')){document.location.href='control_acceso2.php?id=" . $id . "&pagina=" . $pagina . "&accion=eliminar';}", "Eliminar Control", "delete.gif");
                                                        }
                                                        else 
                                                        {
                                                             echo "<td></td>";

                                                        }
                                                    }
                                                    else
                                                    {
                                                         echo "<td></td>";

                                                    }
                                                }
                                                else
                                                {
                                                     echo "<td></td>";

                                                }
                                                
                                                if($_SESSION['reporte_asistencia_personal'])
                                                {
                                                  icono("../tcpdf/reportes/asistencia_personal.php?reg=".$id."&pagina=".$pagina."&fecha_ini=" . $fila['fecha_ini'] . "&fecha_fin=" . $fila['fecha_fin'] . "&fecha_reg=" . $fila['fecha_reg'], "Reporte de asistencia de personal", "ico_print.gif");
                                                }
                                                else 
                                                {
                                                     echo "<td></td>";

                                                }
                                                
                                                if($_SESSION['reporte_horas_extras_det'])
                                                {
                                                  icono("../../reportes/excel/excel_horas_extras_xls.php?reg=".$id."&pagina=".$pagina, "Reporte de Horas Extras", "../img_sis/logo_terpel.jpg");
                                                }
                                                else 
                                                {
                                                     echo "<td></td>";

                                                }
                                                
                                                if($_SESSION['reporte_horas_trabajadas'])
                                                {
                                                  icono("rpt_horas_trabajadas_xls.php?reg=".$id."&pagina=".$pagina, "Reporte de Horas Trabajadas Personal", "../img_sis/ico_excel.gif");
                                                }
                                                else 
                                                {
                                                     echo "<td></td>";

                                                }
                                                
                                                if($_SESSION['ausencias_tardanzas'])
                                                {
                                                    $funcion = "enviar(2,'".$id."','".$fila['fecha_ini']."','".$fila['fecha_fin']."');";
                                                    
                                                    echo '<td><a href="javascript:'.$funcion.'" title="Ausencias y Tardanzas"><img src="../img_sis/47.png" width="24" height="24"></a></td>';
                                                 // icono("rpt_horas_trabajadas_xls2.php?reg=".$id."&pagina=".$pagina, "Ausencias y Tardanzas", "../img_sis/47.png");
                                                }
                                                else 
                                                {
                                                     echo "<td></td>";

                                                }
                                                
                                                if($_SESSION['reporte_horas_extras'])
                                                {
                                                  icono("../tcpdf/reportes/asistencia_personal2.php?reg=".$id."&pagina=".$pagina."&fecha_ini=" . $fila['fecha_ini'] . "&fecha_fin=" . $fila['fecha_fin'] . "&fecha_reg=" . $fila['fecha_reg'], "Cálculo de Horas Extras con Marcaciones", "../imagenes/70.png");
                                                }
                                                else 
                                                {
                                                     echo "<td></td>";

                                                }

                                                switch ($fila['status']) {
                                                    case 'Pendiente':
                                                        $estado = "<label class='label label-danger'>Pendiente </label>";
                                                    break;
                                                    case 'Edicion':
                                                        $estado = "<label class='label label-warning'>En Edición </label>";
                                                    break;
                                                    case 'Preaprobado':
                                                        $estado = "<label class='label label-primary'>Preaprobada </label>";
                                                    break;
                                                    case 'Aprobado':
                                                        $estado = "<label class='label label-success'>Aprobada </label>";
                                                    break;
                                                }

                                                if($_SESSION['generar_control_manual'])
                                                {
                                                    //icono("control_acceso_generacion_manual.php?reg=".$id."&pagina=".$pagina,"Generar Control Manual","../img_sis/icons/clock.png");
                                                    echo "<td><a href='javascript:enviar(1, ". $fila['cod_enca'].");' title='Generar Control Manual'><img src='../img_sis/icons/clock.png' width='16' height='16'></a></td>";
                                                    
                                                }
                                                else 
                                                {
                                                    echo "<td></td>";

                                                }
                                               
                                               

                                            ?>
                                            <td align="center"><?php echo $estado; ?></td>
                                        </tr>
                                        <?php
                                        }
                                        ?>
                                        </tbody>
                                    </table>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <span id="fileGet" style="display:none;"></span>
    <?php include("../footer4.php"); ?>

    <style type="text/css">
        #table_datatable_filter input {
            display: none !important;
        }
    </style>
    
    <script type="text/javascript" src="../lib/common.js"></script>
    <script type="text/javascript">
    function enviar(op,id, ini, fin)
    {
        if (op==1){		// Generar Detalles de Encabezado
            AbrirVentana('barraprogreso_3.php?registro_id='+id,600,800,0);
        }
        if (op==2){		// Generar Detalles de Encabezado
            var fecha_ini = String(ini).replace("/","-");
            var fecha_fin = String(fin).replace("/","-");
            $.ajax({
                type : 'GET',
                url : 'rpt_horas_trabajadas_xls2.php',
                data: { reg : id},
                beforeSend : function() {
                    $('#table_datatable_wrapper').block({ 
                        message: '<span><img src="../imagenes/loader.gif"></span><br><span>Generando Reporte...</b></span>', 
                        css: { border: '1px solid #a00' } 
                    }); 
                },
                success : function(response){
                    var data = JSON.parse(response);
                    var $a = $("<a>");
                    $a.attr("href",data.file);
                    $("body").append($a);
                    $a.attr("download","ausencias_tardanzas_"+fecha_ini+"_"+fecha_fin+".xls");
                    $a[0].click();
                    $a.remove();
                    $("#table_datatable_wrapper").unblock($.unblockUI);
                }
            });
            //AbrirVentana('rpt_horas_trabajadas_xls2.php?reg='+id,600,800,0);
        }
    }
    </script>
    <script type="text/javascript">

        function cargar_anio(){
            var search_anio=$("#search_anio").val();
            window.location.href='?search_anio='+search_anio;
        }

   $(document).ready(function() { 
    //$('#table_datatable').DataTable();
            // begin first table
            $('#table_datatable').DataTable({
              //"oSearch": {"sSearch": "Escriba frase para buscar"},
              "bStateSave" : true,
              renderer: {
                    "header": "jqueryui",
                    "pageButton": "bootstrap"
                },
              "iDisplayLength": 25,
              "sPaginationType": "bootstrap_extended", 
              //"sPaginationType": "full_numbers",
                "oLanguage": {
                  "sSearch": "<img src='../../includes/imagenes/icons/magnifier.png' width='16' height='16' > Buscar:",
                    "sLengthMenu": "Mostrar _MENU_",
                    //"sInfo": "Showing page _PAGE_ of _PAGES_", // Mostrando 1 to 5 de 18 entradas
                    //"sInfoEmpty": "No hay registros para mostrar",
                    "sInfoEmpty": "",
                    //"sInfo": "",
                    "sInfo":"Total _TOTAL_ registros",
                    "sInfoFiltered": "",
                  "sEmptyTable":  "No hay datos disponibles",//"No data available in table",
              //      "sZeroRecords": "No se encontraron registros",//"No matching records found",
                    "oPaginate": {
                        "sPrevious": "P&aacute;gina Anterior",//"Prev",
                        "sNext": "P&aacute;gina Siguiente",//"Next",
                        "sPage": "P&aacute;gina",//"Page",
                        "sPageOf": "de",//"of"
                    }
                },
                "aLengthMenu": [ // set available records per page
                    [5, 10, 25, 50,  -1],
                    [5, 10, 25, 50, "Todos"]
                ],      
                "aaSorting": [[ 0, "desc" ]],
                "aoColumnDefs": [
                    { 'bSortable': false, 'aTargets': [5] },
                    { "bSearchable": false, "aTargets": [ 5 ] },
                    { 'bSortable': false, 'aTargets': [6] },
                    { "bSearchable": false, "aTargets": [ 6 ] },
                    { 'bSortable': false, 'aTargets': [7] },
                    { "bSearchable": false, "aTargets": [ 7 ] },
                    { 'bSortable': false, 'aTargets': [8] },
                    { "bSearchable": false, "aTargets": [ 8 ] },
                    { 'bSortable': false, 'aTargets': [9] },
                    { "bSearchable": false, "aTargets": [ 9 ] },
                    { 'bSortable': false, 'aTargets': [10] },
                    { "bSearchable": false, "aTargets": [ 10 ] },
                    { 'bSortable': false, 'aTargets': [11] },
                    { "bSearchable": false, "aTargets": [ 11 ] },
                    { 'bSortable': false, 'aTargets': [12] },
                    { "bSearchable": false, "aTargets": [ 12 ] },
                    { 'bSortable': false, 'aTargets': [13] },
                    { "bSearchable": false, "aTargets": [ 13 ] },
                    { 'bSortable': false, 'aTargets': [14] },
                    { "bSearchable": false, "aTargets": [ 14 ] },
                    { 'bSortable': false, 'aTargets': [15] },
                    { "bSearchable": false, "aTargets": [ 15 ] },
                    { 'bSortable': false, 'aTargets': [16] },
                    { "bSearchable": false, "aTargets": [ 16 ] }
                ],
         "fnDrawCallback": function() {
                $('#table_datatable_filter input').attr("placeholder", "Escriba frase para buscar");
         }
            });

            $('#table_datatable').on('change', 'tbody tr .checkboxes', function(){
                 $(this).parents('tr').toggleClass("active");
            });
            $('#div_search_situ').insertBefore("#table_datatable_wrapper .dataTables_filter input");
            $('#table_datatable_wrapper .dataTables_filter input').addClass("form-control input-medium input-inline"); 
            $('#table_datatable_wrapper .dataTables_length select').addClass("form-control input-xsmall"); 
   });
    </script> 
