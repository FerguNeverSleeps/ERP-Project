<?
$conexion = conexion();
       
    if(isset($_GET['cedula']) && $_GET['cedula']!='')
    {
           
            $sql2 = "SELECT  codnivel1, codnivel2, codnivel3, nomposicion_id, codcargo, tipnom, nomfuncion_id, estado
                    FROM   nompersonal
                    WHERE  cedula='{$_GET['cedula']}'";                    
           
            
            $resultado2=sql_ejecutar($sql2);
            $fetch2=fetch_array($resultado2);  
    }


?>

<div class="form-group">
    <label class="col-md-2 control-label" for="txtcodigo">Tipo registro:</label>
    <div class="col-md-7">                               
            <div  id="tipo_tipo">
            <SELECT name="tipo_tiporegistro" class="form-control" id="tipo_tiporegistro">
            <option value="">Seleccione</option>
            <?php                        
                   
                while($fila=fetch_array($resultado))
                {
                    //echo "AQUI";
                    if ($codigo=='')
                    {                        
                        {?>
                            <option  value="<?=$fila['id_expediente_subtipo'];?>"><?=utf8_encode($fila['nombre_subtipo']);?></option>
                        <?}
                        
                    }
                    else
                    {
                        if($fetch33['subtipo']==$fila['id_expediente_subtipo'])
                        {?>
                             <option  value="<?=$fila['id_expediente_subtipo'];?>" selected><?=utf8_encode($fila['nombre_subtipo']);?></option> 
                        <?}
                        else
                        {?>
                             <option  value="<?=$fila['id_expediente_subtipo'];?>"><?=utf8_encode($fila['nombre_subtipo']);?></option>
                        <?}         
                    }

                }
                
            ?>
            </SELECT>
            </div>            
    </div>
</div>

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
    <legend >Movimiento</legend>
<div class="form-group">
        <?php
                //$tipemp1 = 'checked';
                $traslado1 = '';
                $traslado2 = $traslado3 = $traslado4 = '';
                

                if($codigo!='')
                {
                        $traslado1 = ($fetch33['motivo_traslado']=='1')  ? 'checked' : '';
                        $traslado2 = ($fetch33['motivo_traslado']=='2')  ? 'checked' : '';
                        $traslado3 = ($fetch33['motivo_traslado']=='3')  ? 'checked' : '';
                        $traslado4 = ($fetch33['motivo_traslado']=='4')    ? 'checked' : '';

                        
                } 
        ?>
        <label class="control-label col-md-2" for="txtcodigo">Motivo</label>
        <div class="col-md-7">
                <div class="radio-list">
                        <label class="radio-inline">
                            <input type="radio" name="motivo_traslado" id="motivo_traslado1" value="1" <?php echo $traslado1; ?>> Necesidad del Servicio</label>
                        <label class="radio-inline">
                        <input type="radio" name="motivo_traslado" id="motivo_traslado2" value="2" <?php echo $traslado2; ?>> Solicitud del Funcionario</label>
                </div>	
                <div class="radio-list">
                        <label class="radio-inline">
                        <input type="radio" name="motivo_traslado" id="motivo_traslado3" value="3" <?php echo $traslado3; ?>> Instrucciones Superior</label>
                        <label class="radio-inline">
                        <input type="radio" name="motivo_traslado" id="motivo_traslado4" value="4" <?php echo $traslado4; ?>> Otros</label>														
                </div>
        </div>
</div>


<div class="form-group">                                
    <label  class="col-md-2 control-label" for="txtcodigo">Fecha Resolución:</label>                                
    <div class="col-md-3">
       <div class="input-group date date-picker" data-provide="datepicker" data-date-format="dd/mm/yyyy" >
           <input size="10" type="text" name="fecha_resolucion" class="form-control" placeholder="(dd/mm/aaaa)" id="fecha_resolucion" value="<?if($fetch33['fecha_resolucion']!="0000-00-00") { echo fecha($fetch33['fecha_resolucion']); } else { echo date("d/m/Y"); } ?>">
           <span class="input-group-btn">
               <button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
           </span>
       </div>
    </div>

    <label class="col-md-1 control-label" for="txtcodigo">Número Resolución</label>                               
    <div class="col-md-3">
        <input size="10" type="text" name="numero_resolucion" class="form-control" id="numero_resolucion" value="<?if(isset($fetch33['numero_resolucion'])) echo$fetch33['numero_resolucion'];?>">
    </div>
</div>


<div class="form-group">                                
    <label  class="col-md-2 control-label" for="txtcodigo">Fecha Inicio:</label>                                
    <div class="col-md-3">
       <div class="input-group date date-picker" data-provide="datepicker" data-date-format="dd/mm/yyyy" >
           <input size="10" type="text" name="fecha_inicio" class="form-control" placeholder="(dd/mm/aaaa)" id="fecha_inicio" value="<?if($fetch33['fecha_inicio']!="0000-00-00") { echo fecha($fetch33['fecha_inicio']); } else { echo date("d/m/Y"); } ?>">
           <span class="input-group-btn">
               <button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
           </span>
       </div>
    </div>
     <label  class="col-md-1 control-label" for="txtcodigo">Fecha Fin:</label>                                
    <div class="col-md-3">
       <div class="input-group date date-picker" data-provide="datepicker" data-date-format="dd/mm/yyyy" >
           <input size="10" type="text" name="fecha_fin" class="form-control" placeholder="(dd/mm/aaaa)" id="fecha_fin" value="<?if($fetch33['fecha_fin']!="0000-00-00") { echo fecha($fetch33['fecha_fin']); } else { echo date("d/m/Y"); } ?>">
           <span class="input-group-btn">
               <button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
           </span>
       </div>
    </div>
</div>
<div class="form-group">
    <label class="col-md-2 control-label" for="txtcodigo">Posición (Anterior): </label>
    <div class="col-md-2">
        <input type="text" name="posicion_anterior" class="form-control" id="posicion_anterior" readonly="true"  <? if ($fetch33['posicion_anterior']==NULL || $fetch33['posicion_anterior']==0){ echo "value='$posicion'"; } else { echo "value='$fetch33[posicion_anterior]'"; }?> size="70"/>
    </div>
    <label class="col-md-1 control-label" for="txtcodigo">Cargo: </label>                                
    <div class="col-md-4">
        <input type="text" name="cargo_posicion_anterior" class="form-control" id="cargo_posicion_anterior" readonly="true" size="70"/>
    </div>
    
</div>
<div class="form-group">        
    <label class="col-md-2 control-label" for="txtcodigo"></label>                                
    <div class="col-md-2">
        
    </div>
    <label class="col-md-1 control-label" for="txtcodigo"></label>                                
    <div class="col-md-4">
        <input type="text" name="des_car_posicion_anterior" class="form-control" id="des_car_posicion_anterior" readonly="true" size="70"/>
    </div>
</div> 
<div class="form-group">        
    <label class="col-md-2 control-label" for="txtcodigo"></label>                                
    <div class="col-md-2">
        
    </div>
    <label class="col-md-1 control-label" for="txtcodigo">Salario: </label>                                
    <div class="col-md-4">
        <input type="text" name="salario_posicion_anterior" class="form-control" id="salario_posicion_anterior" readonly="true" size="70"/>
    </div>
</div> 

<div class="form-group">
    <label class="col-md-2 control-label" for="txtcodigo">Posición (Nueva): </label>
    <div class="col-md-2">                               
            
        <select name="posicion_nueva" class="form-control" id="posicion_nueva" onchange="buscar_datos_posicion_nueva();">
                <option value="">Seleccione</option>
                <?php                     
                    $consulta_posicion="SELECT nomposicion_id, descripcion_posicion FROM nomposicion";
                    $resultado_posicion=sql_ejecutar($consulta_posicion);
                    while($fila_posicion=fetch_array($resultado_posicion))
                    {
                        //echo "AQUI";
                        if ($codigo=='')
                        {
                            if(!$fetch2['nomposicion_id'])
                            {?>
                                <option  value="<?=$fila_posicion['nomposicion_id'];?>"><?=utf8_encode($fila_posicion['nomposicion_id']);?></option>
                            <?}
                            else
                            { 
                               if($fetch2['nomposicion_id']==$fila_posicion['nomposicion_id'])
                               {?>
                                    <option  value="<?=$fila_posicion['nomposicion_id'];?>" selected><?=utf8_encode($fila_posicion['nomposicion_id']);?></option> 
                               <?}
                               else
                               {?>
                                    <option  value="<?=$fila_posicion['nomposicion_id'];?>"><?=utf8_encode($fila_posicion['nomposicion_id']);?></option>
                               <?}
                            }
                        }
                        else
                        {
                            if($fetch33['posicion_nueva']==NULL || $fetch33['posicion_nueva']==0 )
                            {
                                if($fetch2['nomposicion_id']==$fila_posicion['nomposicion_id'])
                               {?>
                                    <option  value="<?=$fila_posicion['nomposicion_id'];?>" selected><?=utf8_encode($fila_posicion['nomposicion_id']);?></option> 
                               <?}
                               else
                               {?>
                                    <option  value="<?=$fila_posicion['nomposicion_id'];?>"><?=utf8_encode($fila_posicion['nomposicion_id']);?></option>
                               <?}
                            }
                            else
                            {
                                if($fetch33['posicion_nueva']==$fila_posicion['nomposicion_id'])
                                {?>
                                     <option  value="<?=$fila_posicion['nomposicion_id'];?>" selected><?=utf8_encode($fila_posicion['nomposicion_id']);?></option> 
                                <?}
                                else
                                {?>
                                     <option  value="<?=$fila_posicion['nomposicion_id'];?>"><?=utf8_encode($fila_posicion['nomposicion_id']);?></option>
                                <?}
                            }
                        }

                    }
                    ?>
            </select>
    </div>       
    <label class="col-md-1 control-label" for="txtcodigo">Cargo: </label>                                
    <div class="col-md-4">
        <input type="text" name="cargo_nuevo" class="form-control" id="cargo_nuevo" readonly="true" size="70"/>
    </div>
    
</div>
<div class="form-group">        
    <label class="col-md-2 control-label" for="txtcodigo"></label>                                
    <div class="col-md-2">
        
    </div>
    <label class="col-md-1 control-label" for="txtcodigo"></label>                                
    <div class="col-md-4">
        <input type="text" name="des_car_posicion" class="form-control" id="des_car_posicion" readonly="true" size="70"/>
    </div>
</div> 
<div class="form-group">        
    <label class="col-md-2 control-label" for="txtcodigo"></label>                                
    <div class="col-md-2">
        
    </div>
    <label class="col-md-1 control-label" for="txtcodigo">Salario: </label>                                
    <div class="col-md-4">
        <input type="text" name="salario_posicion" class="form-control" id="salario_posicion" readonly="true" size="70"/>
    </div>
</div>
<div class="form-group">
    <label class="col-md-2 control-label" for="txtcodigo">Gerencia Anterior: </label>
    <div class="col-md-7">                               
            
            <select name="gerencia_ant" class="form-control" id="gerencia_ant" disabled>
                <option value="">Seleccione</option>
                <?php                        
                   $consulta_gerencia="SELECT codorg, descrip FROM nomnivel1";
                    $resultado_gerencia=sql_ejecutar($consulta_gerencia);
                    while($fila=fetch_array($resultado_gerencia))
                    {
                        
                            if($fetch33['gerencia_anterior']==$fila['codorg'])
                            {?>
                                 <option  value="<?=$fila['codorg'];?>" selected><?=utf8_encode($fila['descrip']);?></option> 
                            <?}
                            else
                            {?>
                                 <option  value="<?=$fila['codorg'];?>"><?=utf8_encode($fila['descrip']);?></option>
                            <?}         
                        

                    }

                ?>
            </select>
    </div>       
    
</div>
    
<div class="form-group">
    <label class="col-md-2 control-label" for="txtcodigo">Gerencia Nueva: </label>
    <div class="col-md-7">                               
            
            <select name="gerencia_nueva" class="form-control" id="gerencia_nueva">
                <option value="">Seleccione</option>
                <?php                        
                   $consulta_gerencia="SELECT codorg, descrip FROM nomnivel1";
                    $resultado_gerencia=sql_ejecutar($consulta_gerencia);
                    while($fila=fetch_array($resultado_gerencia))
                    {
                        //echo "AQUI";
                        if ($codigo=='')
                        {                        
                            {?>
                                <option  value="<?=$fila['codorg'];?>"><?=utf8_encode($fila['descrip']);?></option>
                            <?}

                        }
                        else
                        {
                            if($fetch33['gerencia_nueva']==$fila['codorg'])
                            {?>
                                 <option  value="<?=$fila['codorg'];?>" selected><?=utf8_encode($fila['descrip']);?></option> 
                            <?}
                            else
                            {?>
                                 <option  value="<?=$fila['codorg'];?>"><?=utf8_encode($fila['descrip']);?></option>
                            <?}         
                        }

                    }

                ?>
            </select>
    </div>       
    
</div>

<div class="form-group">
    <label class="col-md-2 control-label" for="txtcodigo">Departamento Anterior: </label>
    <div class="col-md-7">                               
            
        <select name="departamento_ant" class="form-control" id="departamento_ant" disabled>
                <option value="">Seleccione</option>
                <?php                        
                   $consulta_departamento="SELECT codorg, descrip FROM nomnivel2";
                    $resultado_departamento=sql_ejecutar($consulta_departamento);
                    while($fila=fetch_array($resultado_departamento))
                    {
                        
                            if($fetch33['departamento_anterior']==$fila['codorg'])
                            {?>
                                 <option  value="<?=$fila['codorg'];?>" selected><?=utf8_encode($fila['descrip']);?></option> 
                            <?}
                            else
                            {?>
                                 <option  value="<?=$fila['codorg'];?>"><?=utf8_encode($fila['descrip']);?></option>
                            <?}         
                        

                    }

                ?>
            </select>
    </div>       
    
</div>
    
<div class="form-group">
    <label class="col-md-2 control-label" for="txtcodigo">Departamento Nuevo: </label>
    <div class="col-md-7">                               
            
            <select name="departamento_nuevo" class="form-control" id="departamento_nuevo">
                <option value="">Seleccione</option>
                <?php                        
                   $consulta_departamento="SELECT codorg, descrip FROM nomnivel2";
                    $resultado_departamento=sql_ejecutar($consulta_departamento);
                    while($fila=fetch_array($resultado_departamento))
                    {
                        //echo "AQUI";
                        if ($codigo=='')
                        {                        
                            {?>
                                <option  value="<?=$fila['codorg'];?>"><?=utf8_encode($fila['descrip']);?></option>
                            <?}

                        }
                        else
                        {
                            if($fetch33['departamento_nuevo']==$fila['codorg'])
                            {?>
                                 <option  value="<?=$fila['codorg'];?>" selected><?=utf8_encode($fila['descrip']);?></option> 
                            <?}
                            else
                            {?>
                                 <option  value="<?=$fila['codorg'];?>"><?=utf8_encode($fila['descrip']);?></option>
                            <?}         
                        }

                    }

                ?>
            </select>
    </div>       
    
</div>

<div class="form-group">
    <label class="col-md-2 control-label" for="txtcodigo">Seccion Anterior: </label>
    <div class="col-md-7">                               
            
        <select name="seccion_ant" class="form-control" id="seccion_ant" disabled>
                <option value="">Seleccione</option>
                <?php                        
                   $consulta_seccion="SELECT codorg, descrip FROM nomnivel3";
                    $resultado_seccion=sql_ejecutar($consulta_seccion);
                    while($fila=fetch_array($resultado_seccion))
                    {
                        
                            if($fetch33['seccion_anterior']==$fila['codorg'])
                            {?>
                                 <option  value="<?=$fila['codorg'];?>" selected><?=utf8_encode($fila['descrip']);?></option> 
                            <?}
                            else
                            {?>
                                 <option  value="<?=$fila['codorg'];?>"><?=utf8_encode($fila['descrip']);?></option>
                            <?}         
                        

                    }

                ?>
            </select>
    </div>       
    
</div>
    
<div class="form-group">
    <label class="col-md-2 control-label" for="txtcodigo">Seccion Nueva: </label>
    <div class="col-md-7">                               
            
            <select name="seccion_nueva" class="form-control" id="seccion_nueva">
                <option value="">Seleccione</option>
                <?php                        
                   $consulta_seccion="SELECT codorg, descrip FROM nomnivel3";
                    $resultado_seccion=sql_ejecutar($consulta_seccion);
                    while($fila=fetch_array($resultado_seccion))
                    {
                        //echo "AQUI";
                        if ($codigo=='')
                        {                        
                            {?>
                                <option  value="<?=$fila['codorg'];?>"><?=utf8_encode($fila['descrip']);?></option>
                            <?}

                        }
                        else
                        {
                            if($fetch33['seccion_nueva']==$fila['codorg'])
                            {?>
                                 <option  value="<?=$fila['codorg'];?>" selected><?=utf8_encode($fila['descrip']);?></option> 
                            <?}
                            else
                            {?>
                                 <option  value="<?=$fila['codorg'];?>"><?=utf8_encode($fila['descrip']);?></option>
                            <?}         
                        }

                    }

                ?>
            </select>
    </div>       
    
</div>
    
<div class="form-group">
    <label class="col-md-2 control-label" for="txtcodigo">Institución Anterior: </label>
    <div class="col-md-7">                               
            
            <select name="institucion_anterior" class="form-control" id="institucion_anterior">
                <option value="">Seleccione</option>
                <?php                        
                   $consulta_institucion="SELECT id, descripcion FROM instituciones";
                    $resultado_institucion=sql_ejecutar($consulta_institucion);
                    while($fila=fetch_array($resultado_institucion))
                    {
                        //echo "AQUI";
                        if ($codigo=='')
                        {                        
                            {?>
                                <option  value="<?=$fila['id'];?>"><?=utf8_encode($fila['descripcion']);?></option>
                            <?}

                        }
                        else
                        {
                            if($fetch33['institucion_anterior']==$fila['id'])
                            {?>
                                 <option  value="<?=$fila['id'];?>" selected><?=utf8_encode($fila['descripcion']);?></option> 
                            <?}
                            else
                            {?>
                                 <option  value="<?=$fila['id'];?>"><?=utf8_encode($fila['descripcion']);?></option>
                            <?}         
                        }

                    }

                ?>
            </select>
    </div>       
    
</div>
<div class="form-group">
    <label class="col-md-2 control-label" for="txtcodigo">Institución Nueva: </label>
    <div class="col-md-7">                               
            
            <select name="institucion_nueva" class="form-control" id="institucion_nueva">
                <option value="">Seleccione</option>
                <?php                        
                   $consulta_institucion="SELECT id, descripcion FROM instituciones";
                    $resultado_institucion=sql_ejecutar($consulta_institucion);
                    while($fila=fetch_array($resultado_institucion))
                    {
                        //echo "AQUI";
                        if ($codigo=='')
                        {                        
                            {?>
                                <option  value="<?=$fila['id'];?>"><?=utf8_encode($fila['descripcion']);?></option>
                            <?}

                        }
                        else
                        {
                            if($fetch33['institucion_nueva']==$fila['id'])
                            {?>
                                 <option  value="<?=$fila['id'];?>" selected><?=utf8_encode($fila['descripcion']);?></option> 
                            <?}
                            else
                            {?>
                                 <option  value="<?=$fila['id'];?>"><?=utf8_encode($fila['descripcion']);?></option>
                            <?}         
                        }

                    }

                ?>
            </select>
    </div>       
    
</div>
    
<div class="form-group">
    <label class="col-md-2 control-label" for="txtcodigo">Cargo Anterior: </label>
    <div class="col-md-7">                               
            
            <select name="cargo_ant" class="form-control" id="cargo_ant" disabled>
                <option value="">Seleccione</option>
                <?php                     
                    $consulta_cargo="SELECT cod_cargo, des_car FROM nomcargos";
                    $resultado_cargo=sql_ejecutar($consulta_cargo);
                    while($fila_cargo=fetch_array($resultado_cargo))
                    {
                        //echo "AQUI";
                        
                            if($fetch33['cod_cargo_anterior']==$fila_cargo['cod_cargo'])
                            {?>
                                 <option  value="<?=$fila_cargo['cod_cargo'];?>" selected><?=utf8_encode($fila_cargo['des_car']);?></option> 
                            <?}
                            else
                            {?>
                                 <option  value="<?=$fila_cargo['cod_cargo'];?>"><?=utf8_encode($fila_cargo['des_car']);?></option>
                            <?}         
                       

                    }
                    ?>
            </select>
    </div>       
    
</div>  
    
<div class="form-group">
    <label class="col-md-2 control-label" for="txtcodigo">Cargo Nuevo: </label>
    <div class="col-md-7">                               
            
            <select name="cargo_nuevo" class="form-control" id="cargo_nuevo">
                <option value="">Seleccione</option>
                <?php                     
                    $consulta_cargo="SELECT cod_cargo, des_car FROM nomcargos";
                    $resultado_cargo=sql_ejecutar($consulta_cargo);
                    while($fila_cargo=fetch_array($resultado_cargo))
                    {
                        //echo "AQUI";
                        if ($codigo=='')
                        {
                            if(!$fetch2['codcargo'])
                            {?>
                                <option  value="<?=$fila_cargo['cod_cargo'];?>"><?=utf8_encode($fila_cargo['des_car']);?></option>
                            <?}
                            else
                            { 
                               if($fetch2['codcargo']==$fila_cargo['cod_cargo'])
                               {?>
                                    <option  value="<?=$fila_cargo['cod_cargo'];?>" selected><?=utf8_encode($fila_cargo['des_car']);?></option> 
                               <?}
                               else
                               {?>
                                    <option  value="<?=$fila_cargo['cod_cargo'];?>"><?=utf8_encode($fila_cargo['des_car']);?></option>
                               <?}
                            }
                        }
                        else
                        {
                            if($fetch33['cod_cargo_nuevo']==$fila_cargo['cod_cargo'])
                            {?>
                                 <option  value="<?=$fila_cargo['cod_cargo'];?>" selected><?=utf8_encode($fila_cargo['des_car']);?></option> 
                            <?}
                            else
                            {?>
                                 <option  value="<?=$fila_cargo['cod_cargo'];?>"><?=utf8_encode($fila_cargo['des_car']);?></option>
                            <?}         
                        }

                    }
                    ?>
            </select>
    </div>       
    
</div>  

<div class="form-group">
    <label class="col-md-2 control-label" for="txtcodigo">Función Anterior: </label>
    <div class="col-md-7">                               
            
            <select name="funcion_ant" class="form-control" id="funcion_ant" disabled>
                <option value="">Seleccione</option>
                <?php                     
                    $consulta_funcion="SELECT nomfuncion_id, descripcion_funcion FROM nomfuncion";
                    $resultado_funcion=sql_ejecutar($consulta_funcion);
                    while($fila_funcion=fetch_array($resultado_funcion))
                    {
                        //echo "AQUI";
                        
                            if($fetch33['funcion_anterior']==$fila_funcion['nomfuncion_id'])
                            {?>
                                 <option  value="<?=$fila_funcion['nomfuncion_id'];?>" selected><?=utf8_encode($fila_funcion['descripcion_funcion']);?></option> 
                            <?}
                            else
                            {?>
                                 <option  value="<?=$fila_funcion['nomfuncion_id'];?>"><?=utf8_encode($fila_funcion['descripcion_funcion']);?></option>
                            <?}         
                        

                    }
                    ?>
            </select>
    </div>       
    
</div>
    
<div class="form-group">
    <label class="col-md-2 control-label" for="txtcodigo">Función Nueva: </label>
    <div class="col-md-7">                               
            
            <select name="funcion_nueva" class="form-control" id="funcion_nueva">
                <option value="">Seleccione</option>
                <?php                     
                    $consulta_funcion="SELECT nomfuncion_id, descripcion_funcion FROM nomfuncion";
                    $resultado_funcion=sql_ejecutar($consulta_funcion);
                    while($fila_funcion=fetch_array($resultado_funcion))
                    {
                        //echo "AQUI";
                        if ($codigo=='')
                        {
                            if(!$fetch2['nomfuncion_id'])
                            {?>
                                <option  value="<?=$fila_funcion['nomfuncion_id'];?>"><?=utf8_encode($fila_funcion['descripcion_funcion']);?></option>
                            <?}
                            else
                            { 
                               if($fetch2['nomfuncion_id']==$fila_funcion['nomfuncion_id'])
                               {?>
                                    <option  value="<?=$fila_funcion['nomfuncion_id'];?>" selected><?=utf8_encode($fila_funcion['descripcion_funcion']);?></option> 
                               <?}
                               else
                               {?>
                                    <option  value="<?=$fila_funcion['nomfuncion_id'];?>"><?=utf8_encode($fila_funcion['descripcion_funcion']);?></option>
                               <?}
                            }
                        }
                        else
                        {
                            if($fetch33['funcion_nueva']==$fila_funcion['nomfuncion_id'])
                            {?>
                                 <option  value="<?=$fila_funcion['nomfuncion_id'];?>" selected><?=utf8_encode($fila_funcion['descripcion_funcion']);?></option> 
                            <?}
                            else
                            {?>
                                 <option  value="<?=$fila_funcion['nomfuncion_id'];?>"><?=utf8_encode($fila_funcion['descripcion_funcion']);?></option>
                            <?}         
                        }

                    }
                    ?>
            </select>
    </div>       
    
</div>

<div class="form-group">

     <label class="col-md-2 control-label" for="txtcodigo">Observaciones: </label>                               
    <div class="col-md-7">
         <textarea name="descripcion" id="descripcion" class="form-control" rows="3" style="resize:vertical;"><?php if (isset($fetch33['descripcion'])) echo "$fetch33[descripcion]"?></textarea>
    </div>
</div>
</fieldset>

<?
