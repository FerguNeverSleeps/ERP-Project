<?php 
session_start();
ob_start();
$termino=$_SESSION['termino'];
// include ("../header.php");
include("../lib/common.php") ;
include("func_bd.php");	
include ("../header4.php");
?>

<script>

function Enviar(){					
		
	if (document.frmPrincipal.registro_id.value==0)
  { 
		document.frmPrincipal.op_tp.value=1
  }
	else
  { 	
		document.frmPrincipal.op_tp.value=2
  }		
	if (document.frmPrincipal.txtdescripcion.value==0)
  {
		document.frmPrincipal.op_tp.value=-1
		alert("Debe ingresar una descripción valida. Verifique...");
  }				
}

</script>



<?php 

	
	
  $registro_id =$_POST[registro_id];
  $op_tp       =$_POST[op_tp];
  $validacion  =0;
	
	if ($registro_id==0) // Si el registro_id es 0 se va a agregar un registro nuevo
	{			
		
		if ($op_tp==1)
		{
		if (isset($_POST[chkUsarTablas])){$usartablas=1;}else{$usartablas=0;}
		
		$codigo_nuevo=AgregarCodigo("nomtipos_nomina","codtip");
		$fecha = date("Y-m-d");
    $fecinc = $_POST['txtfechaincremento'];
    list($dia,$mes,$ano) = explode("-",$fecinc);
    if((isset($dia))and(isset($mes))and(isset($ano)))
    {
      $fecha = $ano."-".$mes."-".$dia;
    }
		$query="insert into nomtipos_nomina 
		(codtip,descrip,diasdisfrute,tiempoor,diasbonvac,diasincremdis,
		diasmaxincdis,diasincrem,diasmaxinc,tipodisfrute,diasantiguedad,antigincremvac,
		usatablas,ruta,tipo_ingreso,desglose_moneda,fecha,codigo_banco,quinquenio,partida)
		values ('$_POST[id]','$_POST[txtdescripcion]','$_POST[txtdiasvacaciones]',
		'$_POST[txttiempoordinario]','$_POST[txtdiasbono]','$_POST[txtdiasporano]',
		'$_POST[txthastamaximo]','$_POST[txtdiasbonoporano]','$_POST[txtmaximoincremento]',
		'$_POST[optTipoDisfrute]','$_POST[txtantiguedadvacaciones]','$_POST[antigincremvac]','$usartablas',
		'$_POST[txtrutacontable]','$_POST[optTipoIngreso]','$_POST[txtdesglose]',
		'$fecha','$_POST[codigo_banco]','$_POST[quinquenio]','$_POST[partida]')";
		
		$result=sql_ejecutar($query);	
		activar_pagina("tipos_nominas.php");				
		
		}
	}
	else // Si el registro_id es mayor a 0 se va a editar el registro actual
	{	
	
    $query                       ="select * from nomtipos_nomina where codtip=$registro_id";		
    $result                      =sql_ejecutar($query);	
    $row                         = mysqli_fetch_array ($result);	
    $codigo                      =$row['codtip'];	
    $nombre                      =$row['descrip'];
    $tiempo_ordinario            =$row['tiempoor'];	
    $dias_bon_vac                =$row['diasbonvac'];
    $dias_disfrute               =$row['diasdisfrute'];
    $dias_adic_por_ano           =$row['diasincremdis'];
    $max_dias_adic_por_ano       =$row['diasmaxincdis'];
    $dias_incre_bono_por_ano     =$row['diasincrem'];
    $max_dias_incre_bono_por_ano =$row['diasmaxinc'];
    $tipo_disfrute               =$row['tipodisfrute']; // continuo ('Co') y Habiles ('Ha')	
    $dias_antiguedad             =$row['diasantiguedad'];
    $antigincremvac              =$row['antigincremvac'];
    $usa_tablas_escalares        =$row['usatablas'];
    $ruta_contable               =$row['ruta'];
    $desglose_moneda             =$row['desglose_moneda'];
    $tipo_ingreso                =$row['tipo_ingreso'];
    $fecha_incremento            =date("d-m-Y",strtotime($row['fecha'])); // fecha de nacimiento
    $codigo_banco                =$row['codigo_banco'];
    $quinquenio                  =$row['quinquenio'];
    $partida                     =$row['partida'];
	}	
		
	if ($op_tp==2)
		{					

				if (isset($_POST[chkUsarTablas])){$usartablas=1;}else{$usartablas=0;}
        $fecha = date("Y-m-d");
        $fecinc = $_POST['txtfechaincremento'];
        list($dia,$mes,$ano) = explode("-",$fecinc);
        if((isset($dia))and(isset($mes))and(isset($ano)))
        {
          $fecha = $ano."-".$mes."-".$dia;
        }
        $query          ="UPDATE nomtipos_nomina set 
        
        descrip         ='$_POST[txtdescripcion]',
        diasdisfrute    ='$_POST[txtdiasvacaciones]',
        tiempoor        ='$_POST[txttiempoordinario]',
        diasbonvac      ='$_POST[txtdiasbono]',
        diasincremdis   ='$_POST[txtdiasporano]',
        diasmaxincdis   ='$_POST[txthastamaximo]',
        diasincrem      ='$_POST[txtdiasbonoporano]',
        diasmaxinc      ='$_POST[txtmaximoincremento]',
        tipodisfrute    ='$_POST[optTipoDisfrute]',
        diasantiguedad  ='$_POST[txtantiguedadvacaciones]',
        antigincremvac  ='$_POST[antigincremvac]',	
        usatablas       ='$usartablas',
        ruta            ='$_POST[txtrutacontable]',
        tipo_ingreso    ='$_POST[optTipoIngreso]',
        desglose_moneda ='$_POST[txtdesglose]',
        fecha           ='$fecha',
        codigo_banco    ='$_POST[codigo_banco]',
        quinquenio      ='$_POST[quinquenio]',
        partida         ='$_POST[partida]'
        where codtip    ='$registro_id'";	
        
        $result         =sql_ejecutar($query);				
				activar_pagina("tipos_nominas.php");										
		{			
	}
}	
?>
<script src="../../includes/assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
<link href="../../includes/assets/plugins/bootstrap-datepicker/css/datepicker.css" rel="stylesheet" type="text/css" />
<form action="" method="post" name="frmPrincipal" id="frmPrincipal">
<div class="page-container">
        <!-- BEGIN CONTENT -->
  <div class="page-content-wrapper">
    <div class="page-content">
       <div class="row">
          <div class="col-md-12">
             <div class="portlet box blue">
                <div class="portlet-title">
                    <h4><?php
                              if ($registro_id==0)
                              {
                              echo "Agregar Tipos de $termino";
                              }
                              else
                              {
                              echo "Modificar Tipos de $termino";
                              }
                        ?>
                </h4>
                </div>
                    <div class="portlet-body">
                      <!--- PRIMER PANEL -->
                      <div class="panel panel-info">
                            <div class="panel-heading">Datos Principales</div>
                            <div class="panel-body">
                                  <div class="row">
                                      <div class="col-md-3">
                                          ID:
                                      </div>
                                      <div class="col-md-4"> 
                                          <input name="op_tp" type="Hidden" id="op_tp" value="-1">
                                          <input name="registro_id" type="Hidden" id="registro_id" value="<?php echo $_POST[registro_id]; ?>">
                                          <input name="id" type="text" id="id" class="form-control" placeholder="Inserte su ID" value="<?php if ($registro_id!=0){ echo $registro_id; }  ?>" >
                                      </div>  
                                  </div>
                                  <br>
                                  <div class="row">
                                      <div class="col-md-3">
                                          <label>Descripci&oacute;n:</label>
                                      </div>
                                      <div class="col-md-4"> 
                                          <input name="txtdescripcion" type="text" id="txtdescripcion" class="form-control" placeholder="Inserte su Descripcion" value="<?php if ($registro_id!=0){ echo $nombre; }  ?>" >
                                      </div>  
                                  </div>
                                  <br>
                                  <div class="row">
                                      <div class="col-md-3">
                                          <label>Desglose de Moneda:</label>
                                      </div>
                                      <div class="col-md-4"> 
                                          <input name="txtdesglose" type="text" id="txtdesglose" class="form-control" placeholder="Inserte su desglose de moneda" value="<?php echo $desglose_moneda; ?>" >
                                      </div>  
                                  </div>
                                  <br>
                                  <div class="row">
                                      <div class="col-md-3">
                                        <label>Tiempo Ordinario de Trabajo Diario :</label>
                                      </div>
                                      <div class="col-md-4"> 
                                         <input name="txttiempoordinario" type="text" id="txttiempoordinario" class="form-control" placeholder="Inserte tiempo ordinario de trabajo" value="<?php if ($registro_id!=0){ echo $tiempo_ordinario; }  ?>" maxlength="10">
                                      </div>  
                                </div>
                                <br>
                                <div class="row">
                                      <div class="col-md-3">
                                        <label>C&oacute;digo Bancario (Para Generaci&oacute;n de TXT):</label>
                                      </div>
                                      <div class="col-md-4"> 
                                         <input name="codigo_banco" type="text" id="codigo_banco" class="form-control" placeholder="Inserte codigo bancario" value="<?php if ($registro_id!=0){ echo $codigo_banco; }  ?>" maxlength="10">
                                      </div>  
                                </div>
                                <br>
                                <div class="row">
                                      <div class="col-md-3">
                                        <label>Numero de Partida (Para Generaci&oacute;n de TXT):</label>
                                      </div>
                                      <div class="col-md-4"> 
                                         <input name="partida" type="text" id="partida" class="form-control" placeholder="Inserte numero de partida" value="<?php if ($registro_id!=0){ echo $partida; }  ?>" maxlength="40">
                                      </div>  
                                </div>
                                <br>
                                <div class="row">
                                      <div class="col-md-3">
                                        <label>Tipo de Ingreso:</label>
                                      </div>
                                      <div class="col-md-4"> 
                                          <input name="optTipoIngreso" type="radio" value="D"
                                           <?php if ($tipo_ingreso=="D"){?> checked="true" <?}?> >
                                            Diario          
                                           <input name="optTipoIngreso" type="radio" value="S"
                                           <?if($tipo_ingreso=="S"){?> checked="true" <?}?>>
                                            Semanal 
                                           <input name="optTipoIngreso" type="radio" value="Q"
                                           <?if($tipo_ingreso=="Q"){?> checked="true" <?}?>>
                                            Quincenal 
                                           <input name="optTipoIngreso" type="radio" value="M"
                                           <?if($tipo_ingreso=="M"){?> checked="true" <?}?>>
                                            Mensual 
                                           <input name="optTipoIngreso" type="radio" value="O"
                                           <?if($tipo_ingreso=="O"){?> checked="true" <?}?>> 
                                            Otras   
                                      </div>  
                                </div>
                                <br>
                                <div class="row">
                                      <div class="col-md-3">
                                        <label>Usa Tablas Escalares para C&aacute;lculo de Vacaciones:</label>
                                      </div>
                                      <div class="col-md-4"> 
                                         <input name="chkUsarTablas" type="checkbox" id="chkUsarTablas" value="checkbox"
                                         <?php if ($usa_tablas_escalares==1){?>  checked="checked"<?php }?>>  
                                      </div>  
                                </div>
                                <br>
                            </div>              
                      </div>
                      <!--- FIN PRIMER PANEL -->
                      <!--- COMIENZO DEL SEGUNDO PANEL -->
                      <div class="panel panel-info">
                            <div class="panel-heading">Par&aacute;metros de Vacaciones Normales</div>
                            <div class="panel-body">
                                <div class="row">
                                      <div class="col-md-3">
                                          Antig&#252;edad para derecho a Vacaciones (D&iacute;as):
                                      </div>
                                      <div class="col-md-4"> 
                                          <input name="txtantiguedadvacaciones" class="form-control" placeholder="Inserte numero de dias" type="text" id="txtantiguedadvacaciones" onKeyPress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" value="<?php if ($registro_id!=0){ echo $dias_antiguedad; }  ?>" maxlength="10">
                                      </div>  
                                </div>
                                <br>
                                <div class="row">
                                      <div class="col-md-3">
                                          Antig&#252;edad para incremento de dias de Vacaciones (A&ntilde;os):
                                      </div>
                                      <div class="col-md-4"> 
                                          <input name="antigincremvac" type="text" id="antigincremvac" class="form-control" placeholder="Inserte numero de años" onKeyPress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" value="<?php if ($registro_id!=0){ echo $antigincremvac; }  ?>" maxlength="10">   
                                      </div>  
                                </div>
                                <br>
                                <div class="row">
                                      <div class="col-md-3">
                                          <strong>Disfrute</strong>
                                      </div>
                                </div>
                                <br>
                                <div class="row">
                                      <div class="col-md-3">
                                          Incremento por Quinquenio?
                                      </div>
                                      <div class="col-md-4"> 
                                           <input name="quinquenio" id="quinquenio" type="checkbox" <? if($quinquenio==1){ echo "checked='true'";}?> value="1"/> 
                                      </div>  
                                </div>
                                <br>
                                <div class="row">
                                      <div class="col-md-3">
                                         D&iacute;as de Vacaciones: 
                                      </div>
                                      <div class="col-md-4"> 
                                         <input name="txtdiasvacaciones" type="text" id="txtdiasvacaciones" class="form-control" placeholder="Inserte numero de dias" onKeyPress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" value="<?php if ($registro_id!=0){ echo $dias_disfrute; }  ?>" maxlength="10">   
                                      </div>  
                                </div>
                                <br>
                                <div class="row">
                                      <div class="col-md-3">
                                         Forma de Disfrute: 
                                      </div>
                                      <div class="col-md-4"> 
                                          <input name="optTipoDisfrute" type="radio" value="Co" <?php if ($tipo_disfrute=='Co'){?>checked="checked"<?php }?>>
                                          Continuos
                                          <input name="optTipoDisfrute" type="radio" value="Ha"
                                          <?php if ($tipo_disfrute=='Ha'){?>checked="checked"<?php }?>>
                                          H&aacute;biles  
                                      </div>  
                                </div>
                                <br>
                                <div class="row">
                                      <div class="col-md-3">
                                         D&iacute;as Adicionales Por A&ntilde;o/Quinquenio: 
                                      </div>
                                      <div class="col-md-2"> 
                                        <input name="txtdiasporano" type="text" id="txtdiasporano" class="form-control" placeholder="Inserte dias" onKeyPress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" value="<?php if ($registro_id!=0){ echo $dias_adic_por_ano; }  ?>" maxlength="10">
                                      </div>  
                                      <div class="col-md-2">
                                         Hasta un M&aacute;ximo de: 
                                      </div>
                                      <div class="col-md-2"> 
                                        <input name="txthastamaximo" type="text" id="txthastamaximo" class="form-control" placeholder="Inserte dias" onKeyPress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" value="<?php if ($registro_id!=0){ echo $max_dias_adic_por_ano; }  ?>" maxlength="10">
                                      </div>  
                                </div>
                                <br>
                                <div class="row">
                                     <div class="col-md-3">
                                          Fecha desde Aplicaci&oacute;n Incremento:   
                                      </div>
                                      <div class="col-md-4">
                                            <div class="input-group date date-picker" data-provide="datepicker" data-date-end-date="0d">  
                                              <input name="txtfechaincremento" type="text"  class="form-control" placeholder="Inserte fecha" id="campo_fecha" value="<?php if ($registro_id!=0){ echo $fecha_incremento; }  ?>" maxlength="10">
                                              <span class="input-group-btn">
                                                  <button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
                                              </span>
                                            </div>
                                      </div> 
                                </div>
                                <br>
                                <div class="row">
                                      <div class="col-md-3">
                                          <strong>Bono Vacacional</strong>
                                      </div>
                                </div>
                                <br>
                                <div class="row">
                                      <div class="col-md-3">
                                         Nro. de D&iacute;as Bono:
                                      </div>
                                      <div class="col-md-4"> 
                                        <input name="txtdiasbono" type="text" id="txtdiasbono" class="form-control" placeholder="Inserte dias" onKeyPress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" value="<?php if ($registro_id!=0){ echo $dias_bon_vac; }  ?>" maxlength="10">
                                      </div>  
                                </div>
                                <br>
                                <div class="row">
                                      <div class="col-md-3">
                                         D&iacute;as Incremento Bono por A&ntilde;o:
                                      </div>
                                      <div class="col-md-2"> 
                                        <input name="txtdiasbonoporano" type="text" id="txtdiasbonoporano" class="form-control" placeholder="Inserte dias" onKeyPress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" value="<?php if ($registro_id!=0){ echo $dias_incre_bono_por_ano; }  ?>" maxlength="10">
                                      </div>  
                                      <div class="col-md-2">
                                         Hasta un M&aacute;ximo de Incremento: 
                                      </div>
                                      <div class="col-md-2"> 
                                         <input name="txtmaximoincremento" type="text" id="txtmaximoincremento" class="form-control" placeholder="Inserte dias" onKeyPress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" value="<?php if ($registro_id!=0){ echo $max_dias_incre_bono_por_ano; }  ?>" maxlength="10">
                                      </div>  
                                </div>
                                <br>
                                <div class="row">
                                      <div class="col-md-3">
                                         Ruta Contable:
                                      </div>
                                      <div class="col-md-4"> 
                                         <input name="txtrutacontable" type="text" class="form-control" placeholder="Inserte Ruta Contable" id="txtrutacontable" value="<?php if ($registro_id!=0){ echo $ruta_contable; }  ?>" maxlength="30">
                                      </div>   
                                </div>
                                <br>
                                <div class="actions" align="right">
                                    <a class="btn btn-sm blue"  href="javascript: Enviar(); document.frmPrincipal.submit();">
                                    <i class="fa fa-plus"></i>
                                      Agregar
                                    </a>
                                    <a class="btn btn-sm blue"  href="javascript: history.back();">
                                    <i class="fa fa-plus"></i>
                                      Cancelar
                                    </a>
                                </div>
                            </div> 
                      </div>

                </div>
             </div>
          </div>
       </div>
    </div>
  </div>
</div>
</form>
<?php 
/*
<form action="" method="post" name="frmPrincipal" id="frmPrincipal">
  <p>
  <input name="op_tp" type="Hidden" id="op_tp" value="-1">
  <input name="registro_id" type="Hidden" id="registro_id" value="<?php echo $_POST[registro_id]; ?>">
  </p>
  <table width="780" height="125" border="0" class="row-br">
    <tr>
      <td height="31" class="row-br"><font color="#000066"><strong>&nbsp;<font color="#000066">
        <?php
		if ($registro_id==0)
		{
		echo "Agregar Tipos de $termino";
		}
		else
		{
		echo "Modificar Tipos de $termino";
		}
		?>
      </font></strong></font></td>
    </tr>
    <tr>
      <td width="489" height="86" class="ewTableAltRow"><table width="790" border="0" bordercolor="#0066FF">
        <tr valign="middle" bgcolor="#FFFFFF">
          <td height="24" bgcolor="#FFFFFF" class="ewTableAltRow" ><font size="2" face="Arial, Helvetica, sans-serif">ID:</font></td>
          <td valign="middle" bgcolor="#FFFFFF" class="ewTableAltRow" ><font size="2" face="Arial, Helvetica, sans-serif">
            <input name="id" type="text" id="id" style="width:300px" value="<?php if ($registro_id!=0){ echo $registro_id; }  ?>" >
          </font></td>
        </tr>
        <tr valign="middle" bgcolor="#FFFFFF">
          <td height="24" bgcolor="#FFFFFF" class="ewTableAltRow" ><font size="2" face="Arial, Helvetica, sans-serif">Descripci&oacute;n:</font></td>
          <td valign="middle" bgcolor="#FFFFFF" class="ewTableAltRow" ><font size="2" face="Arial, Helvetica, sans-serif">
            <input name="txtdescripcion" type="text" id="txtdescripcion" style="width:300px" value="<?php if ($registro_id!=0){ echo $nombre; }  ?>" >
          </font></td>
          </tr>
        
        <tr bgcolor="#FFFFFF">
          <td height="24" bgcolor="#FFFFFF" class="ewTableAltRow">Desglose de Moneda:</td>
          <td bgcolor="#FFFFFF" class="ewTableAltRow"><font size="2" face="Arial, Helvetica, sans-serif">
            <input name="txtdesglose" type="text" id="txtdesglose" style="width:400px" value="<?php echo $desglose_moneda; ?>" >
          </font></td>
        </tr>
        
        <tr bgcolor="#FFFFFF">
          <td height="24" bgcolor="#FFFFFF" class="ewTableAltRow">Tiempo Ordinario de Trabajo Diario : </td>
          <td bgcolor="#FFFFFF" class="ewTableAltRow"><font size="2" face="Arial, Helvetica, sans-serif">
            <input name="txttiempoordinario" type="text" id="txttiempoordinario" style="width:40px" value="<?php if ($registro_id!=0){ echo $tiempo_ordinario; }  ?>" maxlength="10">
          </font></td>
        </tr>
        
        <tr bgcolor="#FFFFFF">
          <td height="24" bgcolor="#FFFFFF" class="ewTableAltRow">C&oacute;digo Bancario (Para Generaci&oacute;n de TXT):</td>
          <td bgcolor="#FFFFFF" class="ewTableAltRow"><font size="2" face="Arial, Helvetica, sans-serif">
            <input name="codigo_banco" type="text" id="codigo_banco" style="width:60px" value="<?php if ($registro_id!=0){ echo $codigo_banco; }  ?>" maxlength="10">
          </font></td>
        </tr>
        
        <tr bgcolor="#FFFFFF">
          <td height="24" bgcolor="#FFFFFF" class="ewTableAltRow">Tipo de Ingreso:            </td>
          <td bgcolor="#FFFFFF" class="ewTableAltRow">
          <input name="optTipoIngreso" type="radio" value="D"
             <?php if ($tipo_ingreso=="D"){?> checked="true" <?}?> >
             Diario          
             <input name="optTipoIngreso" type="radio" value="S"
             <?if($tipo_ingreso=="S"){?> checked="true" <?}?>>
            Semanal 
            <input name="optTipoIngreso" type="radio" value="Q"
            <?if($tipo_ingreso=="Q"){?> checked="true" <?}?>>
            Quincenal 
            <input name="optTipoIngreso" type="radio" value="M"
            <?if($tipo_ingreso=="M"){?> checked="true" <?}?>>
            Mensual 
            <input name="optTipoIngreso" type="radio" value="O"
            <?if($tipo_ingreso=="O"){?> checked="true" <?}?>> 
            Otras 
          </td>
        </tr>
        <tr bgcolor="#FFFFFF">
          <td height="24" bgcolor="#FFFFFF" class="ewTableAltRow">Usa Tablas Escalares para C&aacute;lculo de Vacaciones:</td>
          <td bgcolor="#FFFFFF" class="ewTableAltRow"><label>
            <input name="chkUsarTablas" type="checkbox" id="chkUsarTablas" value="checkbox"
			     <?php if ($usa_tablas_escalares==1){?>	checked="checked"<?php }?>>
          </label></td>
        </tr>
         
        <tr bgcolor="#FFFFFF">
          <td height="24" colspan="2" bgcolor="#FFFFFF" class="ewTableAltRow"><fieldset>
          <legend><strong>Par&aacute;metros de Vacaciones Normales</strong></legend>
            <table width="758" border="0">
              <tr>
                <td colspan="2">Antig&#252;edad para derecho a Vacaciones (D&iacute;as):</td>
                <td colspan="3"><font size="2" face="Arial, Helvetica, sans-serif">
                  <input name="txtantiguedadvacaciones" type="text" id="txtantiguedadvacaciones" style="width:40px" onKeyPress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" value="<?php if ($registro_id!=0){ echo $dias_antiguedad; }  ?>" maxlength="10">
                </font></td>
              </tr>
             
<tr>
<td colspan="2">Antig&#252;edad para incremento de dias de Vacaciones (A&ntilde;os):</td>
<td colspan="3"><font size="2" face="Arial, Helvetica, sans-serif">
<input name="antigincremvac" type="text" id="antigincremvac" style="width:40px" onKeyPress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" value="<?php if ($registro_id!=0){ echo $antigincremvac; }  ?>" maxlength="10">
</font></td>
</tr>
 
<tr>
<td colspan="2"><strong>Disfrute </strong></td>
<td colspan="3">&nbsp;</td>
</tr>

<tr>
<td colspan="2">Incremento por Quinquenio?&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <input name="quinquenio" id="quinquenio" type="checkbox" <? if($quinquenio==1){ echo "checked='true'";}?> value="1"/></td>
<td colspan="3">&nbsp;</td>
</tr>

              <tr>
                <td width="199">D&iacute;as de Vacaciones:</td>
                <td width="50"><font size="2" face="Arial, Helvetica, sans-serif">
                  <input name="txtdiasvacaciones" type="text" id="txtdiasvacaciones" style="width:40px" onKeyPress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" value="<?php if ($registro_id!=0){ echo $dias_disfrute; }  ?>" maxlength="10">
                </font></td>
                <td width="174">Forma de Disfrute: </td>
                <td colspan="2"><input name="optTipoDisfrute" type="radio" value="Co"
				<?php if ($tipo_disfrute=='Co'){?>checked="checked"<?php }?>>
Continuos
  <input name="optTipoDisfrute" type="radio" value="Ha"
  <?php if ($tipo_disfrute=='Ha'){?>checked="checked"<?php }?>>
  H&aacute;biles</td>
              </tr>
              
              <tr>
                <td>D&iacute;as Adicionales Por A&ntilde;o/Quinquenio: </td>
                <td><font size="2" face="Arial, Helvetica, sans-serif">
                  <input name="txtdiasporano" type="text" id="txtdiasporano" style="width:40px" onKeyPress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" value="<?php if ($registro_id!=0){ echo $dias_adic_por_ano; }  ?>" maxlength="10">
                </font></td>
                <td>Hasta un M&aacute;ximo de: </td>
                <td colspan="2"><font size="2" face="Arial, Helvetica, sans-serif">
                  <input name="txthastamaximo" type="text" id="txthastamaximo" style="width:40px" onKeyPress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" value="<?php if ($registro_id!=0){ echo $max_dias_adic_por_ano; }  ?>" maxlength="10">
                </font></td>
              </tr>
              
              <tr>
                <td>Fecha desde Aplicaci&oacute;n Incremento:</td>
                <td colspan="4"><font size="2" face="Arial, Helvetica, sans-serif">
                  <div align="left">
  <input name="txtfechaincremento" type="text" id="campo_fecha" style="width:100px" value="<?php if ($registro_id!=0){ echo $fecha_incremento; }  ?>" maxlength="10">
                    
  <input name="image" type="image" id="b_fecha" src="../lib/jscalendar/cal.gif" />
  
                    
  <!-- script que define y configura el calendario--> 
  <script type="text/javascript"> 
   Calendar.setup({ 
    inputField     :    "campo_fecha",     // id del campo de texto 
     ifFormat     :     "%d/%m/%Y",     // formato de la fecha que se escriba en el campo de texto 
     button     :    "b_fecha"     // el id del bot�n que lanzar� el calendario 
}); 
</script>           
                      </div>
                </tr>
                
              <tr>
                <td><strong>Bono Vacacional </strong></td>
                <td colspan="4">&nbsp;</td>
              </tr>
              <tr>
                <td>Nro. de D&iacute;as Bono: </td>
                <td colspan="4"><font size="2" face="Arial, Helvetica, sans-serif">
                  <input name="txtdiasbono" type="text" id="txtdiasbono" style="width:40px" onKeyPress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" value="<?php if ($registro_id!=0){ echo $dias_bon_vac; }  ?>" maxlength="10">
                </font></td>
              </tr>
              
              <tr>
                <td>D&iacute;as Incremento Bono por A&ntilde;o: </td>
                <td><font size="2" face="Arial, Helvetica, sans-serif">
                  <input name="txtdiasbonoporano" type="text" id="txtdiasbonoporano" style="width:40px" onKeyPress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" value="<?php if ($registro_id!=0){ echo $dias_incre_bono_por_ano; }  ?>" maxlength="10">
                </font></td>

                <td colspan="2">Hasta un M&aacute;ximo de Incremento: </td>
                <td width="296"><font size="2" face="Arial, Helvetica, sans-serif">
                  <input name="txtmaximoincremento" type="text" id="txtmaximoincremento" style="width:40px" onKeyPress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" value="<?php if ($registro_id!=0){ echo $max_dias_incre_bono_por_ano; }  ?>" maxlength="10">
                </font></td>
              </tr>
            </table>
            </fieldset></td>
        </tr>
        <tr bgcolor="#FFFFFF">
          <td height="24" bgcolor="#FFFFFF" class="ewTableAltRow">Ruta Contable: </td>
          <td bgcolor="#FFFFFF" class="ewTableAltRow"><font size="2" face="Arial, Helvetica, sans-serif">
            <input name="txtrutacontable" type="text" id="txtrutacontable" style="width:300px" value="<?php if ($registro_id!=0){ echo $ruta_contable; }  ?>" maxlength="30">
          </font></td>
        </tr>
        
        <tr bgcolor="#FFFFFF">
          <td height="26" bgcolor="#FFFFFF" class="ewTableAltRow">&nbsp;</td>
          <td bgcolor="#FFFFFF" class="ewTableAltRow"><div align="right">
            <table width="85" border="0">
              <tr>
                <td width="39"><div align="center"><font size="2" face="Arial, Helvetica, sans-serif">
                    <?php btn('cancel','history.back();',2) ?>
                </font></div></td>
                <td width="36"><div align="center"><font size="2" face="Arial, Helvetica, sans-serif">
                    <?php btn('ok','Enviar(); document.frmPrincipal.submit();',2) ?>
                </font></div></td>
              </tr>
            </table>
          </div></td>
        </tr>
      </table>      </td>
    </tr>
  </table>
  <p>&nbsp;</p>
  <p>&nbsp;</p>
  <p>&nbsp;</p>
  <p>&nbsp;</p>
  <p>&nbsp;</p>  
  <p>&nbsp;</p>
  
</form>
*/
        ?>
</body>
</html>

