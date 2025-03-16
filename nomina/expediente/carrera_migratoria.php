<?
$conexion = conexion();
    if(isset($_GET['codigo']) && $_GET['codigo']!='')
    {
            

            $sql1 = "SELECT  *
                    FROM   expediente
                    WHERE  cod_expediente_det='{$_GET['codigo']}'";
            
            $resultado1=sql_ejecutar($sql1);
            $fetch1=fetch_array($resultado1); 
            
            $codigo = $_GET['codigo'];            
            

    }
    
    if(isset($_GET['cedula']) && $_GET['cedula']!='')
    {
           
            $sql2 = "SELECT  *
                    FROM   nompersonal
                    WHERE  cedula='{$_GET['cedula']}'";                    
           
            
            $resultado2=sql_ejecutar($sql2);
            $fetch2=fetch_array($resultado2);  
    }


?>


<div class="form-group">  
     <label for="txtcodigo" class="col-md-2 control-label">Número Secuencial: </label>                                    
    <div class="col-md-7">
        <input class="form-control" type="text" name="numero_secuencial" id="numero_secuencial" readonly="true"  value="" /> 
    </div>
   
</div>

<fieldset>
    <legend >Información Actual</legend>
<div class="form-group">                                
    <label class="col-md-2 control-label" for="txtcodigo">Nombre y Apellido: </label>                                
    <div class="col-md-3">
        <input type="text" name="nombre_apellido" class="form-control" id="nombre_apellido" readonly="true" <? if (isset($nombre)) echo "value='$nombre'"?> size="70"/>
    </div>
    <label for="txtcodigo" class="col-md-2 control-label">Cédula: </label>                                    
    <div class="col-md-2">
        <input class="form-control" type="text" name="cedula" id="cedula" readonly="true" <? if (isset($cedula)) echo "value='$cedula'"?> /> 
    </div>

</div>

<div class="form-group">                                
    <label class="col-md-2 control-label" for="txtcodigo">Posición: </label>                                
    <div class="col-md-3">
        <input type="text" name="posicion" class="form-control" id="posicion" readonly="true" <? if (isset($posicion)) echo "value='$posicion'"?> size="70"/>
    </div>
    <label for="txtcodigo" class="col-md-2 control-label">Cuenta Contable: </label>                                    
    <div class="col-md-2">
        <input class="form-control" type="text" name="cuenta_contable" id="cuenta_contable" readonly="true"  <? if (isset($cuenta_contable)) echo "value='$cuenta_contable'"?> /> 
    </div>

</div>

<div class="form-group">                                
    <label class="col-md-2 control-label" for="txtcodigo">Planilla: </label>                                
    <div class="col-md-3">
        <input type="text" name="planilla_anterior" class="form-control" id="planilla_anterior" readonly="true" <? if (isset($pplanilla)) echo "value='$pplanilla'"?> size="70"/>
    </div>
   

</div>

<div class="form-group">  
     <label for="txtcodigo" class="col-md-2 control-label">Tipo Empleado: </label>                                    
    <div class="col-md-2">
        <input class="form-control" type="text" name="tipo_empleado" id="tipo_empleado" readonly="true"  <? if (isset($tipo_empleado)) echo "value='$tipo_empleado'"?> /> 
    </div>
    <label class="col-md-2 control-label" for="txtcodigo">Cargo: </label>                                
    <div class="col-md-3">
        <input type="text" name="cargo" class="form-control" id="cargo" readonly="true" <? if (isset($cargo)) echo "value='$cargo'"?> size="70"/>
    </div> 
</div>

<div class="form-group">  
     <label for="txtcodigo" class="col-md-2 control-label">Función: </label>                                    
    <div class="col-md-2">
        <input class="form-control" type="text" name="funcion_anterior" id="funcion_anterior" readonly="true"  <? if (isset($funcion)) echo "value='$funcion'"?> /> 
    </div>
    <label class="col-md-2 control-label" for="txtcodigo">Partida: </label>                                
    <div class="col-md-3">
        <input type="text" name="partida" class="form-control" id="partida" readonly="true" <? if (isset($partida)) echo "value='$partida'"?> size="70"/>
    </div> 
</div>

<div class="form-group">                                
    <label class="col-md-2 control-label" for="txtcodigo">Salario: </label>                                
    <div class="col-md-3">
        <input type="text" name="salario" class="form-control" id="salario" readonly="true" <? if (isset($salario)) echo "value='$salario'"?> size="70"/>
    </div>
    <label for="txtcodigo" class="col-md-2 control-label">Gastos Representación: </label>                                    
    <div class="col-md-2">
        <input class="form-control" type="text" name="gastos_representacion" id="gastos_representacion" readonly="true"  <? if (isset($gastos_representacion)) echo "value='$gastos_representacion'"?> /> 
    </div>

</div>

<div class="form-group">                                
    <label class="col-md-2 control-label" for="txtcodigo">Fecha Inicio (Institución): </label>                                
    <div class="col-md-3">
        <input type="text" name="fecha_ingreso" class="form-control" id="fecha_ingreso" readonly="true" value="<?if(isset($fecha_inicio)) echo fecha($fecha_inicio);?>" size="70"/>
    </div>
    <label for="txtcodigo" class="col-md-2 control-label">Seguro Social: </label>                                    
    <div class="col-md-2">
        <input class="form-control" type="text" name="seguro_social" id="seguro_social" readonly="true"  <? if (isset($seguro_social)) echo "value='$seguro_social'"?> /> 
    </div>
</div>
<div class="form-group">                                
    <label class="col-md-2 control-label" for="txtcodigo">Número Decreto (Ingreso): </label>                                
    <div class="col-md-3">
        <input type="text" name="numero_decreto_ingreso" class="form-control" id="numero_decreto_ingreso" readonly="true" value="<?if(isset($numero_decreto_ingreso)) echo fecha($numero_decreto_ingreso);?>" size="70"/>
    </div>
    <label for="txtcodigo" class="col-md-2 control-label">Fecha Decreto (Ingreso): </label>                                    
    <div class="col-md-2">
        <input class="form-control" type="text" name="fecha_decreto_ingreso" id="fecha_decreto_ingreso" readonly="true"  <? if (isset($fecha_decreto_ingreso)) echo "value='$fecha_decreto_ingreso'"?> /> 
    </div>
</div>
<div class="form-group">                                
    <label class="col-md-2 control-label" for="txtcodigo">Número Resuelto (Baja): </label>                                
    <div class="col-md-3">
        <input type="text" name="numero_resuelto_baja" class="form-control" id="numero_resuelto_baja" readonly="true" value="<?if(isset($numero_resuelto_baja)) echo fecha($numero_resuelto_baja);?>" size="70"/>
    </div>
    <label for="txtcodigo" class="col-md-2 control-label">Fecha Resuelto (Baja): </label>                                    
    <div class="col-md-2">
        <input class="form-control" type="text" name="fecha_resuelto_baja" id="fecha_resuelto_baja" readonly="true"  <? if (isset($fecha_resuelto_baja)) echo "value='$fecha_resuelto_baja'"?> /> 
    </div>
</div>
<div class="form-group">                                
    <label class="col-md-2 control-label" for="txtcodigo">Causal Baja: </label>                                
    <div class="col-md-3">
        <input type="text" name="causal_baja" class="form-control" id="causal_baja" readonly="true" value="<?if(isset($fecha_inicio)) echo fecha($fecha_inicio);?>" size="70"/>
    </div>
    
</div>
<div class="form-group">                                
    <label class="col-md-2 control-label" for="txtcodigo">Gerencia: </label>                                
    <div class="col-md-7">
        <input type="text" name="gerencia_ant" class="form-control" id="gerencia_ant" readonly="true" <? if (isset($nivel1)) echo "value='$nivel1'"?> size="70"/>
        <input type="hidden" name="gerencia_anterior" class="form-control" id="gerencia_anterior" <? if (isset($codnivel1)) echo "value='$codnivel1'"?> size="70"/>
    </div>
    
</div>
<div class="form-group">                                
    <label class="col-md-2 control-label" for="txtcodigo">Departamento: </label>                                
    <div class="col-md-7">
        <input type="text" name="departamento_ant" class="form-control" id="departamento_ant" readonly="true" <? if (isset($nivel2)) echo "value='$nivel2'"?> size="70"/>
        <input type="hidden" name="departamento_anterior" class="form-control" id="departamento_anterior" <? if (isset($codnivel2)) echo "value='$codnivel2'"?> size="70"/>
    
    </div>    
</div>
<div class="form-group">                                
    <label class="col-md-2 control-label" for="txtcodigo">Seccion: </label>                                
    <div class="col-md-7">
        <input type="text" name="seccion_ant" class="form-control" id="seccion_ant" readonly="true" <? if (isset($nivel3)) echo "value='$nivel3'"?> size="70"/>
        <input type="hidden" name="seccion_anterior" class="form-control" id="seccion_anterior" <? if (isset($codnivel3)) echo "value='$codnivel3'"?> size="70"/>
    
    </div>    
</div>

</fieldset>

<fieldset>
    <legend >Carrera Migratoria</legend>

<div class="form-group" style = "display:none;">
    <label class="col-md-2 control-label">Fecha Notificación Ingreso (anterior)</label>
    <div class="col-md-7">
            <div class="input-group date date-picker" data-provide="datepicker" data-date-end-date="0d">	
                    <input type="text" class="form-control" name="cm_fecha_notificacion_ingreso_anterior" readonly id="cm_fecha_notificacion_ingreso_anterior" value="<?if(isset($fetch33['cm_fecha_notificacion_ingreso_anterior'])){ if($fetch33['cm_fecha_notificacion_ingreso_anterior']!="0000-00-00") {echo fecha($fetch33['cm_fecha_notificacion_ingreso_anterior']);} else{echo fecha($fetch2['cm_fecha_notificacion_ingreso']);} } else{echo fecha($fetch2['cm_fecha_notificacion_ingreso']);}?>">
                    <span class="input-group-btn">
                            <button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
                    </span>
            </div>
    </div>
</div>    

<div class="form-group">
    <label class="col-md-2 control-label">Fecha Notificación Ingreso</label>
    <div class="col-md-7">
            <div class="input-group date date-picker" data-provide="datepicker" data-date-end-date="0d">	
                    <input type="text" class="form-control" name="cm_fecha_notificacion_ingreso" id="cm_fecha_notificacion_ingreso" value="<?if(isset($fetch33['cm_fecha_notificacion_ingreso'])){ if($fetch33['cm_fecha_notificacion_ingreso']!="0000-00-00") {echo fecha($fetch33['cm_fecha_notificacion_ingreso']);}}?>">
                    <span class="input-group-btn">
                            <button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
                    </span>
            </div>
    </div>
</div>

<div class="form-group" style = "display:none">
    <label class="col-md-2 control-label">Número Resolución (anterior)</label>
    <div class="col-md-7">
            <input type="text" name="cm_numero_resolucion_anterior" id="cm_numero_resolucion_anterior" class="form-control" readonly value="<?if(isset($fetch33['cm_numero_resolucion_anterior'])){ if($fetch33['cm_numero_resolucion_anterior']!="") {echo $fetch33['cm_numero_resolucion_anterior'];} else{echo $fetch2['cm_numero_resolucion'];} } else{echo $fetch2['cm_numero_resolucion'];}?>">
            </div>
</div>
    
<div class="form-group">
    <label class="col-md-2 control-label">Número Resolución </label>
    <div class="col-md-7">
            <input type="text" name="cm_numero_resolucion" id="cm_numero_resolucion" class="form-control" value="<?if(isset($fetch33['cm_numero_resolucion'])){ if($fetch33['cm_numero_resolucion']!="") {echo $fetch33['cm_numero_resolucion'];} else{echo $fetch2['cm_numero_resolucion'];} } else{echo $fetch2['cm_numero_resolucion'];}?>">
            </div>
</div>

<div class="form-group" style = "display:none">
    <label class="col-md-2 control-label">Fecha Resolución (Anterior)</label>
    <div class="col-md-7">
            <div class="input-group date date-picker" data-provide="datepicker" data-date-end-date="0d">	
                    <input type="text" class="form-control" name="cm_fecha_resolucion_anterior" id="cm_fecha_resolucion_anterior" readonly value="<?if(isset($fetch33['cm_fecha_resolucion_anterior'])){ if($fetch33['cm_fecha_resolucion_anterior']!="0000-00-00") {echo fecha($fetch33['cm_fecha_resolucion_anterior']);} else{echo fecha($fetch2['cm_fecha_resolucion']);} }else{echo fecha($fetch2['cm_fecha_resolucion']);}?>">
                    <span class="input-group-btn">
                            <button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
                    </span>
            </div>
    </div>
</div>    

<div class="form-group">
    <label class="col-md-2 control-label">Fecha Resolución</label>
    <div class="col-md-7">
            <div class="input-group date date-picker" data-provide="datepicker" data-date-end-date="0d">	
                    <input type="text" class="form-control" name="cm_fecha_resolucion" id="cm_fecha_resolucion" value="<?if(isset($fetch33['cm_fecha_resolucion'])){ if($fetch33['cm_fecha_resolucion']!="0000-00-00") {echo fecha($fetch33['cm_fecha_resolucion']);}}?>">
                    <span class="input-group-btn">
                            <button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
                    </span>
            </div>
    </div>
</div>
    
<div class="form-group" style = "display:none">
    <?php
            $ordinario = 'checked';
            $especial = '';
            if(isset($fetch2['cm_tipo_proceso']))
            {
                    $ordinario = ($fetch2['cm_tipo_proceso']=='B') ? 'checked' : '';
                    $especial = ($fetch2['cm_tipo_proceso']=='A')  ? 'checked' : '';	
            } 
    ?>		
    <label class="col-md-2">Proceso (Anterior)</label>
    <div class="col-md-7">
            <div class="radio-list" readonly>
                    <label class="radio-inline">
                    <input type="radio" name="cm_tipo_proceso_anterior" id="ordinario" readonly value="B" <?php echo $ordinario;?>>Ordinario</label> 
                    <label class="radio-inline"> 
                    <input type="radio" name="cm_tipo_proceso_anterior" id="especial" readonly value="A" <?php echo $especial;?>>Especial</label>

            </div>
    </div>
</div>  

<div class="form-group">
    <?php
            $ordinario = 'checked';
            $especial = '';
            if(isset($fetch33['cm_tipo_proceso']))
            {
                    $ordinario = ($fetch33['cm_tipo_proceso']=='B') ? 'checked' : '';
                    $especial = ($fetch33['cm_tipo_proceso']=='A')  ? 'checked' : '';	
            } 
    ?>		
    <label class="col-md-2">Proceso</label>
    <div class="col-md-7">
            <div class="radio-list">
                    <label class="radio-inline">
                    <input type="radio" name="cm_tipo_proceso" id="ordinario" value="B" <?php echo $ordinario;?>>Ordinario</label> 
                    <label class="radio-inline"> 
                    <input type="radio" name="cm_tipo_proceso" id="especial" value="A" <?php echo $especial;?>>Especial</label>

            </div>
    </div>
</div>  

<div class="form-group" style = "display:none">
    <label class="col-md-2 control-label">Sobresueldo (Anterior)</label>
    <div class="col-md-7"><input type="text" name="cm_sobresueldo_anterior" id="cm_sobresueldo_anterior" class="form-control" readonly value="<?if(isset($fetch33['cm_sobresueldo_anterior'])){ if($fetch33['cm_sobresueldo_anterior']!="") {echo $fetch33['cm_sobresueldo_anterior'];} else{echo $fetch2['otros'];} } else{echo $fetch2['otros'];}?>"></div>
</div>    
    
<div class="form-group">
    <label class="col-md-2 control-label">Sobresueldo </label>
    <div class="col-md-7"><input type="text" name="cm_sobresueldo" id="cm_sobresueldo" class="form-control" value="<?if(isset($fetch33['cm_sobresueldo'])){ if($fetch33['cm_sobresueldo']!="") {echo $fetch33['cm_sobresueldo'];} else{echo $fetch2['otros'];} } else{echo $fetch2['otros'];}?>"></div>
</div>

<div class="form-group" style = "display:none">
    <label class="col-md-2 control-label">Gastos Responsabilidad (Anterior)</label>
    <div class="col-md-7"><input type="text" name="cm_gasto_responsabilidad_anterior" id="cm_gasto_responsabilidad_anterior" readonly class="form-control" value="<?if(isset($fetch33['cm_gasto_responsabilidad_anterior'])){ if($fetch33['cm_gasto_responsabilidad_anterior']!="") {echo $fetch33['cm_gasto_responsabilidad_anterior'];} else{echo $fetch2['cm_gasto_responsabilidad'];} } else{echo $fetch2['cm_gasto_responsabilidad'];}?>"></div>
</div>
    
<div class="form-group">
    <label class="col-md-2 control-label">Gastos Responsabilidad</label>
    <div class="col-md-7"><input type="text" name="cm_gasto_responsabilidad" id="cm_gasto_responsabilidad" class="form-control" value="<?if(isset($fetch33['cm_gasto_responsabilidad'])){ if($fetch33['cm_gasto_responsabilidad']!="") {echo $fetch33['cm_gasto_responsabilidad'];} else{echo $fetch2['cm_gasto_responsabilidad'];} } else{echo $fetch2['cm_gasto_responsabilidad'];}?>"></div>
</div>

<div class="form-group" style = "display:none">
    <label class="col-md-2 control-label">Gastos Representacion (Anterior)</label>
    <div class="col-md-7"><input type="text" name="cm_gasto_representacion_anterior" id="cm_gasto_representacion_anterior" readonly class="form-control" value="<?if(isset($fetch33['cm_gasto_representacion_anterior'])){ if($fetch33['cm_gasto_representacion_anterior']!="") {echo $fetch33['cm_gasto_representacion_anterior'];} else{echo $fetch2['gastos_representacion'];} } else{echo $fetch2['gastos_representacion'];}?>"></div>
</div>    
    
<div class="form-group">
    <label class="col-md-2 control-label">Gastos Representacion</label>
    <div class="col-md-7"><input type="text" name="cm_gasto_representacion" id="cm_gasto_representacion" class="form-control" value="<?if(isset($fetch33['cm_gasto_representacion'])){ if($fetch33['cm_gasto_representacion']!="") {echo $fetch33['cm_gasto_representacion'];} else{echo $fetch2['gastos_representacion'];} } else{echo $fetch2['gastos_representacion'];}?>"></div>
</div>

<div class="form-group" style = "display:none">
    <label class="col-md-2 control-label">Incentivo Título Universitario (Anterior)</label>
    <div class="col-md-7"><input type="text" name="cm_incentivo_titulo_anterior" id="cm_incentivo_titulo_anterior" class="form-control" readonly value="<?if(isset($fetch33['cm_incentivo_titulo_anterior'])){ if($fetch33['cm_incentivo_titulo_anterior']!="") {echo $fetch33['cm_incentivo_titulo_anterior'];} else{echo $fetch2['cm_incentivo_titulo'];} } else{echo $fetch2['cm_incentivo_titulo'];}?>"></div>
</div>    
    
<div class="form-group">
    <label class="col-md-2 control-label">Incentivo Título Universitario </label>
    <div class="col-md-7"><input type="text" name="cm_incentivo_titulo" id="cm_incentivo_titulo" class="form-control" value="<?if(isset($fetch33['cm_incentivo_titulo'])){ if($fetch33['cm_incentivo_titulo']!="") {echo $fetch33['cm_incentivo_titulo'];} else{echo $fetch2['cm_incentivo_titulo'];} } else{echo $fetch2['cm_incentivo_titulo'];}?>"></div>
</div>

<div class="form-group" style = "display:none">
    <label class="col-md-2 control-label">Ascenso (Anterior)</label>
    <div class="col-md-7"><input type="text" name="cm_ascenso_anterior" id="cm_ascenso_anterior" class="form-control" readonly value="<?if(isset($fetch33['cm_ascenso_anterior'])){ if($fetch33['cm_ascenso_anterior']!="") {echo $fetch33['cm_ascenso_anterior'];} else{echo $fetch2['cm_ascenso'];} } else{echo $fetch2['cm_ascenso'];}?>"></div>
</div>    
    
<div class="form-group">
    <label class="col-md-2 control-label">Ascenso </label>
    <div class="col-md-7"><input type="text" name="cm_ascenso" id="cm_ascenso" class="form-control" value="<?if(isset($fetch33['cm_ascenso'])){ if($fetch33['cm_ascenso']!="") {echo $fetch33['cm_ascenso'];} else{echo $fetch2['cm_ascenso'];} } else{echo $fetch2['cm_ascenso'];}?>"></div>
</div>

<div class="form-group" style = "display:none">
    <?php
            $confidencialidad_no = 'checked';
            $confidencialidad_si = '';
            if(isset($fetch2['cm_directiva_confidencialidad']))
            {
                    $confidencialidad_no = ($fetch2['cm_directiva_confidencialidad']=='0') ? 'checked' : '';
                    $confidencialidad_si = ($fetch2['cm_directiva_confidencialidad']=='1')  ? 'checked' : '';	
            } 
    ?>		
    <label class="col-md-2">Directiva Confidencialidad (Anterior)</label>
    <div class="col-md-5">
            <div class="radio-list" readonly>
                    <label class="radio-inline">
                    <input type="radio" name="cm_directiva_confidencialidad_anterior" id="confidencialidad_no" readonly value="0" <?php echo $confidencialidad_no;?>>No</label> 
                    <label class="radio-inline"> 
                    <input type="radio" name="cm_directiva_confidencialidad_anterior" id="confidencialidad_si" readonly value="1" <?php echo $confidencialidad_si;?>>Si</label>

            </div>
    </div>
</div>  
    
    
<div class="form-group">
    <?php
            $confidencialidad_no = 'checked';
            $confidencialidad_si = '';
            if(isset($fetch33['cm_directiva_confidencialidad']))
            {
                    $confidencialidad_no = ($fetch33['cm_directiva_confidencialidad']=='0') ? 'checked' : '';
                    $confidencialidad_si = ($fetch33['cm_directiva_confidencialidad']=='1')  ? 'checked' : '';	
            } 
    ?>		
    <label class="col-md-2">Directiva Confidencialidad</label>
    <div class="col-md-5">
            <div class="radio-list">
                    <label class="radio-inline">
                    <input type="radio" name="cm_directiva_confidencialidad" id="confidencialidad_no" value="0" <?php echo $confidencialidad_no;?>>No</label> 
                    <label class="radio-inline"> 
                    <input type="radio" name="cm_directiva_confidencialidad" id="confidencialidad_si" value="1" <?php echo $confidencialidad_si;?>>Si</label>

            </div>
    </div>
</div>  

<div class="form-group" style = "display:none">
    <?php
            $carta_compromiso_no = 'checked';
            $carta_compromiso_si = '';
            if(isset($fetch2['cm_carta_compromiso']))
            {
                    $carta_compromiso_no = ($fetch2['cm_carta_compromiso']=='0') ? 'checked' : '';
                    $carta_compromiso_si = ($fetch2['cm_carta_compromiso']=='1')  ? 'checked' : '';	
            } 
    ?>		
    <label class="col-md-2">Carta Compromiso Institucion (anterior)</label>
    <div class="col-md-5">
            <div class="radio-list" readonly>
                    <label class="radio-inline">
                    <input type="radio" name="cm_carta_compromiso_anterior" id="carta_compromiso_no" readonly value="0" <?php echo $carta_compromiso_no;?>>No</label> 
                    <label class="radio-inline"> 
                    <input type="radio" name="cm_carta_compromiso_anterior" id="carta_compromiso_si" readonly value="1" <?php echo $carta_compromiso_si;?>>Si</label>

            </div>
    </div>
</div>
    
<div class="form-group">
    <?php
            $carta_compromiso_no = 'checked';
            $carta_compromiso_si = '';
            if(isset($fetch33['cm_carta_compromiso']))
            {
                    $carta_compromiso_no = ($fetch33['cm_carta_compromiso']=='0') ? 'checked' : '';
                    $carta_compromiso_si = ($fetch33['cm_carta_compromiso']=='1')  ? 'checked' : '';	
            } 
    ?>		
    <label class="col-md-2">Carta Compromiso Institucion</label>
    <div class="col-md-5">
            <div class="radio-list">
                    <label class="radio-inline">
                    <input type="radio" name="cm_carta_compromiso" id="carta_compromiso_no" value="0" <?php echo $carta_compromiso_no;?>>No</label> 
                    <label class="radio-inline"> 
                    <input type="radio" name="cm_carta_compromiso" id="carta_compromiso_si" value="1" <?php echo $carta_compromiso_si;?>>Si</label>

            </div>
    </div>
</div>

<div class="form-group" style = "display:none">
    <label class="col-md-2 control-label">Fecha Notificación Homologación (Anterior)</label>
    <div class="col-md-7">
            <div class="input-group date date-picker" data-provide="datepicker" data-date-end-date="0d">	
                    <input type="text" class="form-control" name="cm_fecha_notificacion_homologacion_anterior" id="cm_fecha_notificacion_homologacion_anterior" readonly value="<?if(isset($fetch33['cm_fecha_notificacion_homologacion_anterior'])){ if($fetch33['cm_fecha_notificacion_homologacion_anterior']!="0000-00-00") {echo fecha($fetch33['cm_fecha_notificacion_homologacion_anterior']);} else{echo fecha($fetch2['cm_fecha_notificacion_homologacion']);} }else{echo fecha($fetch2['cm_fecha_notificacion_homologacion']);}?>">
                    <span class="input-group-btn">
                            <button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
                    </span>
            </div>
    </div>
</div>
    
<div class="form-group">
    <label class="col-md-2 control-label">Fecha Notificación Homologación</label>
    <div class="col-md-7">
            <div class="input-group date date-picker" data-provide="datepicker" data-date-end-date="0d">	
                    <input type="text" class="form-control" name="cm_fecha_notificacion_homologacion" id="cm_fecha_notificacion_homologacion" value="<?if(isset($fetch33['cm_fecha_notificacion_homologacion'])){ if($fetch33['cm_fecha_notificacion_homologacion']!="0000-00-00") {echo fecha($fetch33['cm_fecha_notificacion_homologacion']);}} ?>">
                    <span class="input-group-btn">
                            <button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
                    </span>
            </div>
    </div>
</div>

<div class="form-group" style = "display:none">
    <label class="col-md-2 control-label">Número Resolución Homologación (Anterior)</label>
    <div class="col-md-7">
            <input type="text" name="cm_numero_resolucion_homologacion_anterior" id="cm_numero_resolucion_homologacion_anterior" class="form-control" readonly value="<?if(isset($fetch33['cm_numero_resolucion_homologacion_anterior'])){ if($fetch33['cm_numero_resolucion_homologacion_anterior']!="") {echo $fetch33['cm_numero_resolucion_homologacion_anterior'];} else{echo $fetch2['cm_numero_resolucion_homologacion'];} } else{echo $fetch2['cm_numero_resolucion_homologacion'];}?>">
            </div>
</div>
    
<div class="form-group">
    <label class="col-md-2 control-label">Número Resolución Homologación</label>
    <div class="col-md-7">
            <input type="text" name="cm_numero_resolucion_homologacion" id="cm_numero_resolucion_homologacion" class="form-control" value="<?if(isset($fetch33['cm_numero_resolucion_homologacion'])){ if($fetch33['cm_numero_resolucion_homologacion']!="") {echo $fetch33['cm_numero_resolucion_homologacion'];} else{echo $fetch2['cm_numero_resolucion_homologacion'];} } else{echo $fetch2['cm_numero_resolucion_homologacion'];}?>">
            </div>
</div>

<div class="form-group" style = "display:none">
    <label class="col-md-2 control-label">Fecha Resolución Homologación (Anterior)</label>
    <div class="col-md-7">
            <div class="input-group date date-picker" data-provide="datepicker" data-date-end-date="0d">	
                    <input type="text" class="form-control" name="cm_fecha_resolucion_homologacion_anterior" id="cm_fecha_resolucion_homologacion_anterior" readonly value="<?if(isset($fetch33['cm_fecha_resolucion_homologacion_anterior'])){ if($fetch33['cm_fecha_resolucion_homologacion_anterior']!="0000-00-00") {echo fecha($fetch33['cm_fecha_resolucion_homologacion_anterior']);} else{echo fecha($fetch2['cm_fecha_resolucion_homologacion']);} }else{echo fecha($fetch2['cm_fecha_resolucion_homologacion']);}?>">
                    <span class="input-group-btn">
                            <button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
                    </span>
            </div>
    </div>
</div>
    
<div class="form-group">
    <label class="col-md-2 control-label">Fecha Resolución Homologación</label>
    <div class="col-md-7">
            <div class="input-group date date-picker" data-provide="datepicker" data-date-end-date="0d">	
                    <input type="text" class="form-control" name="cm_fecha_resolucion_homologacion" id="cm_fecha_resolucion_homologacion" value="<?if(isset($fetch33['cm_fecha_resolucion_homologacion'])){ if($fetch33['cm_fecha_resolucion_homologacion']!="0000-00-00") {echo fecha($fetch33['cm_fecha_resolucion_homologacion']);}}?>">
                    <span class="input-group-btn">
                            <button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
                    </span>
            </div>
    </div>
</div>

<div class="form-group" style = "display:none">
    <label class="col-md-2 control-label">Acreditación Personal Ordinario (Anterior)</label>
    <div class="col-md-7">
            <input type="text" name="cm_acreditacion_personal_ordinario_anterior" id="cm_acreditacion_personal_ordinario_anterior" class="form-control" readonly value="<?if(isset($fetch33['cm_acreditacion_personal_ordinario_anterior'])){ if($fetch33['cm_acreditacion_personal_ordinario_anterior']!="") {echo $fetch33['cm_acreditacion_personal_ordinario_anterior'];} else{echo $fetch2['cm_acreditacion_personal_ordinario'];} } else{echo $fetch2['cm_acreditacion_personal_ordinario'];}?>">
            </div>
</div>
    
<div class="form-group">
    <label class="col-md-2 control-label">Acreditación Personal Ordinario</label>
    <div class="col-md-7">
            <input type="text" name="cm_acreditacion_personal_ordinario" id="cm_acreditacion_personal_ordinario" class="form-control" value="<?if(isset($fetch33['cm_acreditacion_personal_ordinario'])){ if($fetch33['cm_acreditacion_personal_ordinario']!="") {echo $fetch33['cm_acreditacion_personal_ordinario'];} else{echo $fetch2['cm_acreditacion_personal_ordinario'];} } else{echo $fetch2['cm_acreditacion_personal_ordinario'];}?>">
            </div>
</div>

<div class="form-group" style = "display:none">
    <?php
            $migratoria = 'checked';
            $administrativa = '';
            $jubilado = '';
            if(isset($fetch2['cm_auditoria_puesto']))
            {
                    $migratoria = ($fetch2['cm_auditoria_puesto']=='A') ? 'checked' : '';
                    $administrativa = ($fetch2['cm_auditoria_puesto']=='B')  ? 'checked' : '';
                    $jubilado = ($fetch2['cm_auditoria_puesto']=='C')  ? 'checked' : '';
            } 
    ?>		
    <label class="col-md-2">Auditoria de Puesto (anterior)</label>
    <div class="col-md-7">
            <div class="radio-list" readonly>
                    <label class="radio-inline">
                    <input type="radio" name="cm_auditoria_puesto_anterior" id="migratoria" readonly value="A" <?php echo $migratoria;?>>A.-Migratoria</label> 
                    <label class="radio-inline"> 
                    <input type="radio" name="cm_auditoria_puesto_anterior" id="administrativa" readonly value="B" <?php echo $administrativa;?>>B.-Administrativa</label>
                    <label class="radio-inline"> 
                    <input type="radio" name="cm_auditoria_puesto_anterior" id="jubilado" readonly value="C" <?php echo $jubilado;?>>C.-Jubilados</label>
            </div>
    </div>
</div>
    
<div class="form-group">
    <?php
            $migratoria = 'checked';
            $administrativa = '';
            $jubilado = '';
            if(isset($fetch33['cm_auditoria_puesto']))
            {
                    $migratoria = ($fetch33['cm_auditoria_puesto']=='A') ? 'checked' : '';
                    $administrativa = ($fetch33['cm_auditoria_puesto']=='B')  ? 'checked' : '';
                    $jubilado = ($fetch33['cm_auditoria_puesto']=='C')  ? 'checked' : '';
            } 
    ?>		
    <label class="col-md-2">Auditoria de Puesto</label>
    <div class="col-md-7">
            <div class="radio-list">
                    <label class="radio-inline">
                    <input type="radio" name="cm_auditoria_puesto" id="migratoria" value="A" <?php echo $migratoria;?>>A.-Migratoria</label> 
                    <label class="radio-inline"> 
                    <input type="radio" name="cm_auditoria_puesto" id="administrativa" value="B" <?php echo $administrativa;?>>B.-Administrativa</label>
                    <label class="radio-inline"> 
                    <input type="radio" name="cm_auditoria_puesto" id="jubilado" value="C" <?php echo $jubilado;?>>C.-Jubilados</label>
            </div>
    </div>
</div>

<div class="form-group" style = "display:none">
    <label class="col-md-2 control-label">Placa (Anterior)</label>
    <div class="col-md-7">
            <input type="text" name="cm_placa_anterior" id="cm_placa_anterior" class="form-control" readonly value="<?if(isset($fetch33['cm_placa_anterior'])){ if($fetch33['cm_placa_anterior']!="") {echo $fetch33['cm_placa_anterior'];} else{echo $fetch2['cm_placa'];} } else{echo $fetch2['cm_placa'];}?>">
            </div>
</div>
    
<div class="form-group">
    <label class="col-md-2 control-label">Placa</label>
    <div class="col-md-7">
            <input type="text" name="cm_placa" id="cm_placa" class="form-control" value="<?if(isset($fetch33['cm_placa'])){ if($fetch33['cm_placa']!="") {echo $fetch33['cm_placa'];} else{echo $fetch2['cm_placa'];} } else{echo $fetch2['cm_placa'];}?>">
            </div>
</div>

<div class="form-group" style = "display:none">
    <label class="col-md-2 control-label">Promoción (Anterior)</label>
    <div class="col-md-7">
            <input type="text" name="cm_promocion_anterior" id="cm_promocion_anterior" class="form-control" readonly value="<?if(isset($fetch33['cm_promocion_anterior'])){ if($fetch33['cm_promocion_anterior']!="") {echo $fetch33['cm_promocion_anterior'];} else{echo $fetch2['cm_promocion'];} } else{echo $fetch2['cm_promocion'];}?>">
            </div>
</div>
    
<div class="form-group">
    <label class="col-md-2 control-label">Promoción</label>
    <div class="col-md-7">
            <input type="text" name="cm_promocion" id="cm_promocion" class="form-control" value="<?if(isset($fetch33['cm_promocion'])){ if($fetch33['cm_promocion']!="") {echo $fetch33['cm_promocion'];} else{echo $fetch2['cm_promocion'];} } else{echo $fetch2['cm_promocion'];}?>">
            </div>
</div>

<div class="form-group" style = "display:none">
    <label class="col-md-2 control-label">Año Jubilación (Anterior)</label>
    <div class="col-md-7">
            <input type="text" name="cm_jubilacion_anio_anterior" id="cm_jubilacion_anio_anterior" class="form-control" readonly value="<?if(isset($fetch33['cm_jubilacion_anio_anterior'])){ if($fetch33['cm_jubilacion_anio_anterior']!="") {echo $fetch33['cm_jubilacion_anio_anterior'];} else{echo $fetch2['cm_jubilacion_anio'];} } else{echo $fetch2['cm_jubilacion_anio'];}?>">
            </div>
</div>
    
<div class="form-group">
    <label class="col-md-2 control-label">Año Jubilación</label>
    <div class="col-md-7">
            <input type="text" name="cm_jubilacion_anio" id="cm_jubilacion_anio" class="form-control" value="<?if(isset($fetch33['cm_jubilacion_anio'])){ if($fetch33['cm_jubilacion_anio']!="") {echo $fetch33['cm_jubilacion_anio'];} else{echo $fetch2['cm_jubilacion_anio'];} } else{echo $fetch2['cm_jubilacion_anio'];}?>">
            </div>
</div>

<div class="form-group">
    <label class="col-md-2 control-label" for="cm_jubliacion_partida">Partida Jubilación</label>
    <div class="col-md-7">
    <?php 
            $consulta_partida="SELECT * FROM cwprecue";
            $resultado_partida=sql_ejecutar($consulta_partida);
    ?>
            <select name="cm_jubliacion_partida_anterior" id="cm_jubliacion_partida_anterior" class="select2 form-control" readonly>
                    <option value="">Seleccione</option>
                    <?php
                    while( $fila_partida=fetch_array($resultado_partida) )
                    { 
                            if( $fetch2['cm_jubliacion_partida']==$fila_partida['CodCue'] )
                            { ?>
                                    <option value="<?php echo $fila_partida['CodCue']; ?>" selected><?php echo $fila_partida['CodCue']; ?></option>
                                    <?php
                            }
                            else
                            { ?>
                                    <option value="<?php echo $fila_partida['CodCue']; ?>"><?php echo $fila_partida['CodCue']; ?></option>
                                    <?php
                            }
                    }
                    ?>
            </select>
    </div>
</div> 

<!--<div class="form-group">
    <label class="col-md-2 control-label" for="txtcodigo">Partida Jubilación: </label>
    <div class="col-md-7">                               
            
            <select name="cm_jubliacion_partida" class="form-control" id="cm_jubliacion_partida">
                <option value="">Seleccione</option>
                <?php                     
                   $consulta_partida="SELECT * FROM cwprecue";
                   $resultado_partida=sql_ejecutar($consulta_partida);
                    while($fila_partida=fetch_array($resultado_partida))
                    {
                        //echo "AQUI";
                        if ($codigo=='')
                        {
                            if(!$fetch2['cm_jubliacion_partida'])
                            {?>
                                <option value="<?php echo $fila_partida['CodCue']; ?>"><?php echo $fila_partida['CodCue']; ?></option>
                            <?}
                            else
                            { 
                               if($fetch2['cm_jubliacion_partida']==$fila_partida['CodCue'])
                               {?>
                                    <option value="<?php echo $fila_partida['CodCue']; ?>" selected><?php echo $fila_partida['CodCue']; ?></option>
                               <?}
                               else
                               {?>
                                    <option value="<?php echo $fila_partida['CodCue']; ?>"><?php echo $fila_partida['CodCue']; ?></option>
                               <?}
                            }
                        }
                        else
                        {
                            if($fetch33['cm_jubliacion_partida']==$fila_partida['CodCue'])
                            {?>
                                 <option value="<?php echo $fila_partida['CodCue']; ?>" selected><?php echo $fila_partida['CodCue']; ?></option>
                            <?}
                            else
                            {?>
                                 <option value="<?php echo $fila_partida['CodCue']; ?>"><?php echo $fila_partida['CodCue']; ?></option>
                            <?}         
                        }

                    }
                    ?>
            </select>
    </div>       
    
</div> -->

<div class="form-group">

     <label class="col-md-2 control-label" for="txtcodigo">Observaciones: </label>                               
    <div class="col-md-7">
         <textarea name="descripcion" id="descripcion" class="form-control" rows="3" style="resize:vertical;"><?php if (isset($fetch33['descripcion'])) echo "$fetch33[descripcion]"?></textarea>
    </div>
</div>
</fieldset>

<?
