<?php
session_start();
ob_start();
$termino=$_SESSION['termino'];
include ("../header4.php");
include("../lib/common.php");
include("func_bd.php");
//include("../../includes/dependencias.php");
date_default_timezone_set('America/Panama');
?>
<script>
function ActualizarNombre(Nomina)
{
    frecuencia=document.frmPrincipal.cboFrecuencia.options[document.frmPrincipal.cboFrecuencia.selectedIndex].text;
    cod_fre=document.frmPrincipal.cboFrecuencia.value;
    numper=document.frmPrincipal.sel_periodo.value;
    document.frmPrincipal.txtdescripcion.value=Nomina + ' - ' + frecuencia + ' - DEL ' + document.frmPrincipal.txtfechainicio.value + ' - AL '+document.frmPrincipal.txtfechafinal.value;
}

function ActualizarNombre2()
{
	cod_fre=document.frmPrincipal.cboFrecuencia.value
	numper=document.frmPrincipal.sel_periodo.value
	if(cod_fre==1)
	document.frmPrincipal.txtdescripcion.value=document.frmPrincipal.txtdescripcion.value+' Semana '+numper
}
function Enviar()
{

  if (document.frmPrincipal.registro_id.value==0)
  {

  	document.frmPrincipal.op_tp.value=1
  }
  if (document.frmPrincipal.registro_id.value!=0)
  {
    
  	document.frmPrincipal.op_tp.value=2
  }

  if (document.frmPrincipal.txtdescripcion.value==0)
  {
  	document.frmPrincipal.op_tp.value=-1
  	alert("Debe ingresar una descripcion valida. Verifique...");
  }
}

function periodo()
{
	var frecuencia=document.getElementById('cboFrecuencia')
	if(frecuencia.value==1)
	 periodo2();
}

function buscar_periodo()
{
//            alert(valor);
    var id_periodo;
    id_periodo=document.getElementById('sel_periodo').value;

    $.ajax({
            type: "POST",
            url:"ajax/buscar_fecha_inicio_periodo.php?id_periodo="+id_periodo,
            async: true,
            success: function(datos){
                /*
                var dataJson = eval(datos);

                for(var i in dataJson){
                    alert(dataJson[i].id_accion_funcionario_tipo + " _ " + dataJson[i].nombre_accion + " _ " + dataJson[i].correlativo);
                }*/
                //alert(datos);
                if (datos!='')
                {   
                    var fecha_inicio_periodo=datos;
                    document.getElementById('txtfechainicio').value=fecha_inicio_periodo;
                    $.ajax({
                            type: "POST",
                            url:"ajax/buscar_fecha_fin_periodo.php?id_periodo="+id_periodo,
                            async: true,
                            success: function(datos){
                                /*
                                var dataJson = eval(datos);

                                for(var i in dataJson){
                                    alert(dataJson[i].id_accion_funcionario_tipo + " _ " + dataJson[i].nombre_accion + " _ " + dataJson[i].correlativo);
                                }*/
                                //alert(datos);
                                if (datos!='')
                                {   
                                    var fecha_fin_periodo=datos;
                                    document.getElementById('txtfechafinal').value=fecha_fin_periodo;
                                    $.ajax({
                                            type: "POST",
                                            url:"ajax/buscar_num_periodo.php?id_periodo="+id_periodo,
                                            async: true,
                                            success: function(datos){
                                                /*
                                                var dataJson = eval(datos);

                                                for(var i in dataJson){
                                                    alert(dataJson[i].id_accion_funcionario_tipo + " _ " + dataJson[i].nombre_accion + " _ " + dataJson[i].correlativo);
                                                }*/
                                                //alert(datos);
                                                  if (datos!='')
                                                {   
                                                    var num_periodo=datos;
                                                    document.getElementById('num_periodo').value=num_periodo;
                                                    ActualizarNombre('<?php echo ($_SESSION[nomina]) ?>');
                                                }


                                            },
                                            error: function (obj, error, objError){
                                                //avisar que ocurrió un error
                                            }
                                    });
                                    
                                    
                                }

                            },
                            error: function (obj, error, objError){
                                //avisar que ocurrió un error
                            }
                    });
                }

            },
            error: function (obj, error, objError){
                //avisar que ocurrió un error
            }
    });

    

    
    
}  

</script>

<?php
/*function fecha_sql($value) { // fecha de DD/MM/YYYY a YYYYY/MM/DD
 return substr($value,6,4) ."/". substr($value,3,2) ."/". substr($value,0,2);
}*/
	$registro_id=( (isset($_POST['registro_id'])) ? $_POST['registro_id'] : 0 );
	$op_tp=( (isset($_POST['op_tp'])) ? $_POST['op_tp'] : 0 );
	//$fecha_preaprobada   = ($row[fecha_preaprobada] != "0000-00-00 00:00:00") ? $fecha_preaprobada : "" ;
	$fecha_preaprobada   = $row['fecha_preaprobada'] ;
	//$fecha_aprobacion    = ($row[fecha_aprobacion] != "0000-00-00 00:00:00") ? $fecha_aprobacion : "" ;
	$fecha_aprobacion    = $row['fecha_aprobacion'] ;
  $validacion=0;
  function get_client_ip() {
    $ipaddress = '';
    if (getenv('HTTP_CLIENT_IP'))
        $ipaddress = getenv('HTTP_CLIENT_IP');
    else if(getenv('HTTP_X_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    else if(getenv('HTTP_X_FORWARDED'))
        $ipaddress = getenv('HTTP_X_FORWARDED');
    else if(getenv('HTTP_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_FORWARDED_FOR');
    else if(getenv('HTTP_FORWARDED'))
        $ipaddress = getenv('HTTP_FORWARDED');
    else if(getenv('REMOTE_ADDR'))
        $ipaddress = getenv('REMOTE_ADDR');
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
  }
	if ($registro_id==0 AND !empty($_POST)) // Si el registro_id es 0 se va a agregar un registro nuevo
	{
    //Validar periodo, tipo planilla y frecuencia
    $_sql = "SELECT *  
    FROM nom_nominas_pago 
    WHERE periodo_ini = '".fecha_sql($_POST['txtfechainicio'])."' AND periodo_fin='".fecha_sql($_POST['txtfechafinal'])."' "
            . "AND frecuencia = '".$_POST[cboFrecuencia]."' AND codtip = '".$_SESSION[codigo_nomina]."'";
    $result=sql_ejecutar($_sql);
    //echo $_sql." || ".$result->num_rows;
//    if ($result->num_rows == 0) 
//    {
      if ($op_tp==1)
      {
        $mes = substr(fecha_sql($_POST['txtfechafinal']),5,2);
        if (isset($_POST[chkUsarTablas])){
          $usartablas=1;
        }else{
          $usartablas=0;
        }

        $codigo_nuevo=$_POST['txtcodigo'];
    
        //echo $_POST['txtfechafinal']." ".$_POST['txtfechapago']." ".fecha_sql($_POST['txtfechapago'])." ".date("Y/d/m",$_POST['txtfechafinal']);
        //exit(0);
        
        $fecha_fin = strtotime(fecha_sql($_POST['txtfechafinal']));
        $anio = date('Y',$fecha_fin);
        $mes = date('m',$fecha_fin);
        $query="INSERT INTO nom_nominas_pago 
          (codnom,
          descrip,
          fechapago,
          periodo_ini,
          periodo_fin,
          anio,
          mes,
          frecuencia,
          periodo,
          status,
          codtip,
          tipnom,
          usuario_creacion,
          anio_sipe,
          mes_sipe,
          fecha)
          VALUES 
          ('$codigo_nuevo',"
          . "'$_POST[txtdescripcion]',"
          . "'".fecha_sql($_POST['txtfechapago'])."',
          '".fecha_sql($_POST['txtfechainicio'])."',"
          . "'".fecha_sql($_POST['txtfechafinal'])."',
          ".$anio.","
          . "'".$mes."',"
          . "'$_POST[cboFrecuencia]',"
          . "'$_POST[sel_periodo]',"
          . "'A',"
          . "'".$_SESSION['codigo_nomina']."',"
          . "'".$_SESSION['codigo_nomina']."',"
          . "'".$_SESSION['usuario']."',"
          . "'$_POST[anio_sipe]',"
          . "'$_POST[mes_sipe]',"
          . "'".fecha_sql( $_POST['fecha_contab'] )."')";
    
        $result=sql_ejecutar($query);
        //LOG TRANSACCIONES -CREAR PLANILLA            
        $descripcion_transaccion = 'CREAR PLANILLA: ' . $_POST['txtdescripcion'] . ', ' . $_POST['txtfechapago']  . ', ' . $_POST['txtfechapago']  ;
        $sql_transaccion = "INSERT INTO log_transacciones (cod_log, descripcion, fecha_hora, modulo, url, accion, valor, usuario,host) 
        VALUES ('', '".$descripcion_transaccion."', now(), 'CREAR PLANILLA', 'ag_nomina_pago.php', 'CREAR','".$_SESSION['usuario']."','".$_SESSION['usuario']."','".get_client_ip()."')";
        $result=sql_ejecutar($sql_transaccion);
        echo "<script>alert('PLANILLA CREADA EXITOSAMENTE')</script>";
    
        activar_pagina("nomina_de_pago.php");

      }
		
      
//    }
//    else{
//      echo "<script>alert('Planilla ya creada para el periodo y la frecuencia seleccionada')</script>";
//    }
	}else{ // Si el registro_id es mayor a 0 se va a editar el registro actual
		$query="SELECT * FROM nom_nominas_pago "
      . "WHERE codnom='".$registro_id."' AND codtip='".$_SESSION['codigo_nomina']."' AND tipnom='".$_SESSION['codigo_nomina']."'";
    //echo $query;exit;            
    $result              = sql_ejecutar($query);
    $row                 = mysqli_fetch_array ($result);

    $codigo              = $row['codnom'];
    $nombre              = $row['descrip'];
    $estatus             = $row['status'];

    $fecha_pago          = date("d/m/Y",strtotime($row['fechapago']));
    $periodo_ini         = date("d/m/Y",strtotime($row['periodo_ini']));
    $periodo_fin         = date("d/m/Y",strtotime($row['periodo_fin']));
    $ano                 = $row['anio'];
    $frecuencia          = $row['frecuencia'];
    $periodo             = $row['periodo'];
    $bisemana            = $row['periodo'];
    $fecha_contab        = $row['fecha'];
    $fecha_preaprobada   = $row['fecha_preaprobada'];
    $fecha_preaprobada   = ($row['fecha_preaprobada'] != "0000-00-00 00:00:00") ? $fecha_preaprobada : date('d-m-Y H:i:s') ;

    $usuario_preaprobada = $row['usuario_preaprobada'];
    $fecha_aprobacion    = $row['fecha_aprobacion'];
    $fecha_aprobacion    = ($row['fecha_aprobacion'] != "0000-00-00 00:00:00") ? $fecha_aprobacion : date('d-m-Y H:i:s') ;
    $usuario_aprobacion  = $row['usuario_aprobacion'];
    $usuario_creacion    = $row['usuario_creacion'];

    $anio_sipe           = $row['anio_sipe'];
    $mes_sipe            = $row['mes_sipe'];
    //                echo $anio_sipe;
    switch ($estatus) {
      case 'A':
          $estado = "<label class='label label-info'>Abierta </label>";
        break;
      case 'P':
          $estado = "<label class='label label-danger'>Preaprobada </label>";
        break;
      case 'C':
          $estado = "<label class='label label-success'>Aprobada </label>";
        break;
    }
	}

	if ($op_tp==2)
	{
    //Validar periodo, tipo planilla y frecuencia
    $_sql = "SELECT *  
    FROM nom_nominas_pago 
    WHERE codtip = '".$_SESSION[codigo_nomina]."' "
    . "AND tipnom='".$_SESSION['codigo_nomina']."' AND codnom='$registro_id'";

    $result=sql_ejecutar($_sql);
    $sql_ag_nomina = "SELECT codnom from nom_nominas_pago "
            . "WHERE  codnom='$registro_id' AND codtip='".$_SESSION['codigo_nomina']."' "
            . "AND tipnom='".$_SESSION['codigo_nomina']."'";
    $result2=sql_ejecutar($sql_ag_nomina);
    if ($result->num_rows == 1 AND $result2->num_rows == 1) 
    {
      $fecha_fin = strtotime(fecha_sql($_POST['txtfechafinal']));
      $anio = date('Y',$fecha_fin);
      $mes = date('m',$fecha_fin);
        
      $query="UPDATE nom_nominas_pago "
            . "SET "
            . "codnom='$registro_id',	"
            . "descrip='$_POST[txtdescripcion]', "
            . "fechapago='".fecha_sql($_POST['txtfechapago'])."',"
            . "periodo_ini='".fecha_sql($_POST['txtfechainicio'])."',	"
            . "periodo_fin='".fecha_sql($_POST['txtfechafinal'])."',	"
            . "anio_sipe='".$_POST['anio_sipe']."',	"
            . "mes_sipe='".$_POST['mes_sipe']."',	"
            . "anio='" .$anio."', "
            . "mes='" .$mes."', "
            . "frecuencia='$_POST[cboFrecuencia]',	"
            . "periodo='$_POST[sel_periodo]' ,	"
            . "fecha='$_POST[fecha_contab]' "
            . "WHERE codnom='$registro_id' and codtip='".$_SESSION['codigo_nomina']."' and tipnom='".$_SESSION['codigo_nomina']."'";
      $result=sql_ejecutar($query);
      //LOG TRANSACCIONES -ACTUALIZAR PLANILLA            
      $descripcion_transaccion = 'ACTUALIZAR PLANILLA: ' . $_POST['txtdescripcion'] . ', ' . $_POST['txtfechapago']  . ', ' . $_POST['txtfechainicio']. ', ' . $_POST['txtfechafinal']  ;
      $sql_transaccion = "INSERT INTO log_transacciones (cod_log, descripcion, fecha_hora, modulo, url, accion, valor, usuario,host) 
      VALUES ('', '".$descripcion_transaccion."', now(), 'ACTUALIZAR PLANILLA', 'ag_nomina_pago.php', 'ACTUALIZAR','".$_SESSION['usuario']."','".$_SESSION['usuario']."','".get_client_ip()."')";
      $result=sql_ejecutar($sql_transaccion);
      echo "<script>alert('PLANILLA ACTUALIZADA EXITOSAMENTE')</script>";
      activar_pagina("nomina_de_pago.php");
      
    }else{
      echo "<script>alert('Error no entra".$_sql."')</script>";
    }
  }/*else{
    echo "<script>alert('Hay una planilla creada para el periodo y la frecuencia seleccionada')</script>";
  }*/
//}

?>
<link rel="stylesheet" type="text/css" href="../../includes/assets/plugins/clockface/css/clockface.css"/>
<link rel="stylesheet" type="text/css" href="../../includes/assets/plugins/bootstrap-datepicker/css/datepicker3.css"/>
<link rel="stylesheet" type="text/css" href="../../includes/assets/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css"/>
<link rel="stylesheet" type="text/css" href="../../includes/assets/plugins/bootstrap-colorpicker/css/colorpicker.css"/>
<link rel="stylesheet" type="text/css" href="../../includes/assets/plugins/bootstrap-daterangepicker/daterangepicker-bs3.css"/>
<link rel="stylesheet" type="text/css" href="../../includes/assets/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css"/>
<link rel="stylesheet" type="text/css" href="../../includes/assets/plugins/select2/select2.css"/>
<link rel="stylesheet" type="text/css" href="../../includes/assets/plugins/select2/select2-metronic.css"/>
<div class="page-container">
  <!-- BEGIN CONTENT -->
  <div class="page-content-wrapper">
    <div class="page-content">
      <!-- BEGIN PAGE CONTENT-->
      <div class="row">
        <div class="col-md-12">
          <!-- BEGIN EXAMPLE TABLE PORTLET-->
          <div class="portlet box blue">
            <div class="portlet-title">
            <div class="caption">
              <?php
                if ($registro_id==0)
                {
                  $op_tp_value = 1;
                  $readonly="readonly";
                  echo "Agregar $termino de Pago";
                }
                else
                {
                  $op_tp_value = 2;
                  $readonly="";
                  echo "Modificar $termino de Pago";
                }
                ?>
            </div>

            </div>
            <div class="portlet-body form">
              <form class="form-horizontal" id="frmPrincipal" name="frmPrincipal" method="post" role="form" style="margin-bottom: 5px;" autocomplete="off">
                <input name="op_tp" type="Hidden" id="op_tp" value="<?php echo $op_tp_value; ?>">
                <input name="registro_id" type="Hidden" id="registro_id" value="<?php echo $registro_id; ?>">
                <input name="num_periodo" type="Hidden" id="num_periodo" >
                <div class="form-body">
                  <div class="row">
                    <label class="col-md-2 control-label" for="codigo">Tipo de <?php echo $termino?></label>
                    <div class="col-md-9">
                      <input type="text" class="form-control input-sm" 
                           id="codigo" name="codigo" value="<?php echo ($_SESSION[nomina]) ?>">
                    </div>
                  </div>
                  <div class="row"> &nbsp;</div>
                  <div class="row">
                    <label class="col-md-2 control-label" for="txtcodigo">Codigo:</label>
                    <div class="col-md-9">
                      <input name="txtcodigo" type="text" id="txtcodigo" class="form-control" onKeyPress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" value="<?php
                      if ($registro_id!=0)
                      {
                        echo $codigo;
                      }
                      else
                      {
                        $codigo_nuevo=AgregarCodigo("nom_nominas_pago","codnom", "where codtip='".$_SESSION['codigo_nomina']."'");
                        echo $codigo_nuevo;
                      }
                      ?>
                      ">
                    </div>
                  </div>
                  <div class="row"> &nbsp;</div>
                  <div class="row">
                    <label class="col-md-2 control-label" for="cboFrecuencia">Frecuencia:</label>
                    <div class="col-md-9">
                      <select class="form-control" onChange="ActualizarNombre('<?php echo ($_SESSION[nomina]) ?>');" name="cboFrecuencia"  id="cboFrecuencia">
                            <?php
                                $query="select codfre,descrip from nomfrecuencias";
                                $result=sql_ejecutar($query);

                                  //ciclo para mostrar los datos
                                  while ($row = fetch_array($result))
                                  {
                                // Opcion de modificar, se selecciona la situacion del registro a modificar
                                  if ($row[codfre]==$frecuencia){ ?>
                          <option value="<?php echo $row[codfre];?>" selected > <?php echo strtoupper($row[descrip]);?> </option>
                                          <?php
                                }
                                else // opcion de agregar
                                {
                                   ?>
                                          <option value="<?php echo $row[codfre];?>"><?php echo strtoupper($row[descrip]);?></option>
                                          <?php
                                }
                                }//fin del ciclo while
                                ?>
                          </select>
                    </div>
                  </div>

                  <div class="row"> &nbsp;</div>
<!--                  <div class="row">
                    <label class="col-md-2 control-label" for="sel_periodo">Periodo:</label>
                    <div class="col-md-9">
                      <SELECT name="sel_periodo" class="form-control" disabled="true" id="sel_periodo" onchange="javascript:cargar_fecha()">
                        <option value="0">Seleccione un periodo</option>
                      </SELECT>
                    </div>
                  </div>-->
                  
                  <div class="row">
                    <label class="col-md-2 control-label" for="sel_periodo">Bisemana/Periodo:</label>
                    <div class="col-md-9">                        
                        <select class="form-control"  name="sel_periodo" id="sel_periodo" onChange="buscar_periodo();">
                          <?php
                              $query="SELECT idBisemanas, numBisemana, fechaInicio, fechaFin FROM bisemanas";
                              $result=sql_ejecutar($query);

                                //ciclo para mostrar los datos
                                while ($row = fetch_array($result))
                                {
                              // Opcion de modificar, se selecciona la situacion del registro a modificar
                                    if ($row[idBisemanas]==$bisemana){ ?>
                                            <option value="<?php echo $row[numBisemana];?>" selected > <?php echo "PERIODO ".$row[numBisemana].": DEL ".date("d/m/Y", strtotime($row[fechaInicio]))." AL ".date("d/m/Y", strtotime($row[fechaFin]));?> </option>
                                            <?php
                                    }
                                    else // opcion de agregar
                                    {
                                       ?>
                                              <option value="<?php echo $row[numBisemana];?>"><?php echo "PERIODO ".$row[numBisemana].": DEL ".date("d/m/Y", strtotime($row[fechaInicio]))." AL ".date("d/m/Y", strtotime($row[fechaFin]));?></option>
                                              <?php
                                    }
                              }//fin del ciclo while
                              ?>
                        </select>
                    </div>
                  </div>
                  <div class="row"> &nbsp;</div>
                  <div class="row">

                    <div class="col-md-2 text-right">
                      <label for="txtfechainicio">Fecha Inicio:</label>
                    </div>
                    <div class="col-md-2">
                      <div class="input-group date date-picker1" data-date-format="dd/mm/yyyy" data-date-viewmode="years">
                      <input class="form-control" onChange="ActualizarNombre('<?php echo ($_SESSION[nomina]) ?>');" name="txtfechainicio" type="text" id="txtfechainicio"  value="<?php if ($registro_id!=0){ echo $periodo_ini; }  ?>" <?php echo $readonly;?> >
                        <span class="input-group-btn">
                          <button class="btn default" type="button"><i class="fa fa-calendar"></i></button>
                        </span>
                      </div>
                      <span class="help-block">
                        Seleccione la Fecha de Inicio</span>
  
                    </div>
                 
                    <div class="col-md-1">
                      <label for="txtfechafinal">Fecha Fin:</label>
                    </div>
                    <div class="col-md-2">
                    <div class="input-group date date-picker2" data-date-format="dd/mm/yyyy" data-date-viewmode="years">
                      <input class="form-control" onChange="ActualizarNombre('<?php echo ($_SESSION[nomina]) ?>');" name="txtfechafinal" type="text" id="txtfechafinal"  value="<?php if ($registro_id!=0){ echo $periodo_fin; }  ?>" <?php echo $readonly;?> >
                        <span class="input-group-btn">
                          <button class="btn default" type="button"><i class="fa fa-calendar"></i></button>
                        </span>
                      </div>
                      <span class="help-block">
                        Seleccione la Fecha Final
                      </span>
                    </div>
                  
                    <div class="col-md-1">
                      <label for="txtfechapago">Fecha Pago:</label>
                    </div>
                    <div class="col-md-2">
                      <div class="input-group date date-picker3" data-date-format="dd/mm/yyyy" data-date-viewmode="years">
                        <input class="form-control" onChange="ActualizarNombre('<?php echo ($_SESSION[nomina]) ?>');" name="txtfechapago" type="text" id="txtfechapago"  value="<?php if ($registro_id!=0){ echo $periodo_fin; }  ?>" <?php echo $readonly;?> >
                          <span class="input-group-btn">
                            <button class="btn default" type="button"><i class="fa fa-calendar"></i></button>
                          </span>
                        </div>
                        <span class="help-block">
                          Seleccione la Fecha de Pago
                        </span>
                    </div>
                  </div>
                  <div class="row"> &nbsp;</div>
                  <div class="row">

                    <div class="col-md-2 text-right">
                      <label for="txtfechainicio">Mes SIPE:</label>
                    </div>
                    <div class="col-md-2">
                        <select name="mes_sipe" id="mes_sipe" class="form-control">

                                  <option value="0" <?php if($mes_sipe==0) echo "selected";?>>Seleccione Mes...</option>
                                  <option value="1" <?php if($mes_sipe==1) echo "selected";?>>Enero</option>
                                  <option value="2" <?php if($mes_sipe==2) echo "selected";?>>Febrero</option>
                                  <option value="3" <?php if($mes_sipe==3) echo "selected";?>>Marzo</option>
                                  <option value="4" <?php if($mes_sipe==4) echo "selected";?>>Abril</option>
                                  <option value="5" <?php if($mes_sipe==5) echo "selected";?>>Mayo</option>
                                  <option value="6" <?php if($mes_sipe==6) echo "selected";?>>Junio</option>
                                  <option value="7" <?php if($mes_sipe==7) echo "selected";?>>Julio</option>
                                  <option value="8" <?php if($mes_sipe==8) echo "selected";?>>Agosto</option>
                                  <option value="9" <?php if($mes_sipe==9) echo "selected";?>>Septiembre</option>
                                  <option value="10" <?php if($mes_sipe==10) echo "selected";?>>Octubre</option>
                                  <option value="11" <?php if($mes_sipe==11) echo "selected";?>>Noviembre</option>
                                  <option value="12" <?php if($mes_sipe==12) echo "selected";?>>Diciembre</option>
                          </select>
                        <span class="help-block">
                          Seleccione Mes</span>

                      </div>
                 
                        <div class="col-md-1">
                          <label for="txtfechafinal">Año SIPE:</label>
                        </div>
                        <div class="col-md-2">
                       <select name="anio_sipe" id="anio_sipe" class="form-control">
                                <option value="">Seleccione Año...</option>
                                <?php 
                                        function obtener_lista_anios($adelanta)
                                        {
                                            $anios = array();
                                            for($i = date("Y"); $i >= date("Y") - 10; $i--){
                                                $anios[] = array($i, $i + $adelanta);
                                            }
                                            return $anios;
                                        }
                                        $anio_sipe_array = obtener_lista_anios(2);
                                        foreach($anio_sipe_array as $k => $v)
                                        {

                                                $selected_anio="";   
                                                if($v[1]==$anio_sipe)
                                                     $selected_anio="selected";   
                                                echo "<option value='".$v[1]."' $selected_anio>".$v[1]."</option>";
                                        }

                                        ?>

                                <?php ?>
                        </select>
                          <span class="help-block">
                            Seleccione Año
                          </span>
                        </div>
                  
                      <div class="col-md-1">
                        <label for="fecha_contab">Fecha Contable:</label>
                      </div>
                      <div class="col-md-2">
                        <div class="input-group date date-picker3" data-date-format="dd/mm/yyyy" data-date-viewmode="years">
                          <input required class="form-control" onChange="ActualizarNombre('<?php echo ($_SESSION[nomina]) ?>');" name="fecha_contab" type="text" id="fecha_contab"  value="<?php if ($registro_id!=0){ echo $fecha_contab; }  ?>" <?php echo $readonly;?> >
                            <span class="input-group-btn">
                              <button class="btn default" type="button"><i class="fa fa-calendar"></i></button>
                            </span>
                          </div>
                          <span class="help-block">
                            Seleccione la Fecha Contabilización
                          </span>
                      </div>
                  
                  </div>
                  <div class="row"> &nbsp;</div>
                  <div class="row"> 
                    <div class="col-md-2 text-right">
                      Descripci&oacute;n:
                    </div>
                    <div class="col-md-9">
                       <input name="txtdescripcion" type="text" id="txtdescripcion" class="form-control" value="<?php
                    if ($registro_id==0)
                      {echo ($_SESSION[nomina]);}
                    else
                      {echo $nombre;}
                    ?>" >
                    </div>

                  </div>
                  <div class="row"> &nbsp;</div>
                  <div class="row">
                    <div class="col-md-2 text-right">
                      Usuario Creación:
                    </div>
                    <div class="col-md-9">
                     <?php
                    echo $usuario_creacion;
                    ?>
                    </div>
                   </div>
                  <div class="row"> &nbsp;</div>
                  <div class="row"> &nbsp;</div>
                  <div class="row">
                    <div class="col-md-2 text-right">
                      Usuario Preaprobación:
                    </div>
                    <div class="col-md-9">
                     <?php
                    echo $usuario_preaprobada;
                    ?>
                    </div>
                   </div>
                  <div class="row"> &nbsp;</div>
                  <div class="row">
                    <div class="col-md-2 text-right">
                      Fecha Preaprobación:
                    </div>
                    <div class="col-md-9">
                     <?php
                    echo $fecha_preaprobada;
                    ?>
                    </div>
                   </div>
                    <div class="row"> &nbsp;</div>
                  <div class="row">
                    <div class="col-md-2 text-right">
                      Usuario Aprobación:
                    </div>
                    <div class="col-md-9">
                     <?php
                    echo $usuario_aprobacion;
                    ?>
                    </div>
                   </div>
                  <div class="row"> &nbsp;</div>
                  <div class="row">
                    <div class="col-md-2 text-right">
                      Fecha Aprobación:
                    </div>
                    <div class="col-md-9">
                     <?php
                    echo $fecha_aprobacion;
                    ?>
                    </div>
                   </div>
                  <div class="row"> &nbsp;</div>
                  <div class="row">
                    <div class="col-md-2 text-right">
                      Estatus:
                    </div>
                    <div class="col-md-9">
                     <?php
                    echo $estado;
                    ?>
                    </div>
                   </div>
                  <div class="row"> &nbsp;</div>
                  &nbsp;
                  <div class="row text-center">
                    <?php boton_metronic('ok','Enviar(); document.frmPrincipal.submit();',2) ?>
                    <?php boton_metronic('cancel',"location.href='nomina_de_pago.php?modulo=203'",2) ?>
                  </div>
                </div>
              </form>
            </div>
          </div>
          <!-- END EXAMPLE TABLE PORTLET-->
        </div>
      </div>
      <!-- END PAGE CONTENT-->
    </div>
  </div>
  <!-- END CONTENT -->
</div>
<?php include("../footer4.php"); ?>        



<script type="text/javascript" src="../../includes/assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
<script type="text/javascript" src="../../includes/assets/plugins/bootstrap-datepicker/js/locales/bootstrap-datepicker.es.js"></script>
<script type="text/javascript" src="../../includes/assets/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js"></script>
<script type="text/javascript" src="../../includes/assets/plugins/clockface/js/clockface.js"></script>
<script type="text/javascript" src="../../includes/assets/plugins/bootstrap-daterangepicker/moment.min.js"></script>
<script type="text/javascript" src="../../includes/assets/plugins/bootstrap-daterangepicker/daterangepicker.js"></script>
<script type="text/javascript" src="../../includes/assets/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.js"></script>
<script type="text/javascript" src="../../includes/assets/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"></script>
<script type="text/javascript" src="../../includes/assets/plugins/select2/select2.min.js"></script>

<!--<script type="text/javascript" src="../../includes/assets/plugins/select2/select2_locale_es.js"></script>-->
<script type="text/javascript">
  $(document).ready(function() { 
    
    $('.date-picker1').datepicker({
        orientation: "left",
        language: 'es',
        autoclose: true
    });
    $('.date-picker2').datepicker({
        orientation: "left",
        language: 'es',
        autoclose: true
    });
    $('.date-picker3').datepicker({
        orientation: "left",
        language: 'es',
        autoclose: true
    });
    $("#btn-guardar").on("click",function()
    {
      if ($("#registro_id").val()==0)
      {
        $("#op_tp").val()=1;
        console.log("op_tp");
        
      }
      else
      {
        $("#op_tp").val()=2;
      }

      if ($("#txtdescripcion").val()==0)
      {
        $("#op_tp").val()=-1;
        alert("Debe ingresar una descripcion valida. Verifique...");
      }
      $("#frmPrincipal").submit();
    });
    $("#sel_periodo").select2();
    $("#cboFrecuencia").select2();
    //buscar_periodo();
  });
</script>
</body>
</html>
