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

<div class="form-group">
    <label class="col-md-2 control-label" for="txtcodigo">Tipo registro:</label>
    <div class="col-md-7">                               
            <div  id="tipo_tipo">
            <SELECT name="tipo_tiporegistro" class="form-control" id="tipo_tiporegistro" onchange="mostrar_especial();">
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

<fieldset>
    <legend >I.- Datos Generales</legend>
<div class="form-group">                                
    <label class="col-md-2 control-label" for="txtcodigo">Nombre y Apellidos: </label>                                
    <div class="col-md-3">
        <input type="text" name="nombre_apellido" class="form-control" id="nombre_apellido" readonly="true" <? if (isset($nombre)) echo "value='$nombre'"?> size="70"/>
    </div>     
    
    <label for="txtcodigo" class="col-md-2 control-label">Cédula: </label>                                    
    <div class="col-md-3">
        <input class="form-control" type="text" name="cedula" id="cedula" readonly="true" <? if (isset($cedula)) echo "value='$cedula'"?> /> 
    </div>
</div>

<div class="form-group">                                
    <label class="col-md-2 control-label" for="txtcodigo">Fecha de Nacimiento: </label>                                
    <div class="col-md-3">
        <input type="text" name="fecha_nacimiento" class="form-control" id="fecha_nacimiento" readonly="true" value="<?if(isset($fecha_nacimiento)) echo fecha($fecha_nacimiento);?>" size="70"/>
    </div>
                               
    <label class="col-md-2 control-label" for="txtcodigo">Estado Civil: </label>                                
    <div class="col-md-3">
        <input type="text" name="estado_civil" class="form-control" id="estado_civil" readonly="true" <? if (isset($estado_civil)) echo "value='$estado_civil'"?> size="70"/>
    </div>
   
</div>
    
<div class="form-group">                                
    <label class="col-md-2 control-label" for="txtcodigo">Domicilio: </label>                                
    <div class="col-md-8">
        <input type="text" name="direccion" class="form-control" id="direccion" readonly="true" <? if (isset($direccion)) echo "value='$direccion'"?> size="70"/>
    </div>
   
</div>
    
<div class="form-group">                                
    <label class="col-md-2 control-label" for="txtcodigo">Posición: </label>                                
    <div class="col-md-3">
        <input type="text" name="posicion" class="form-control" id="posicion" readonly="true" <? if (isset($posicion)) echo "value='$posicion'"?> size="70"/>
    </div>
   
                    
    <label class="col-md-2 control-label" for="txtcodigo">Teléfono: </label>                                
    <div class="col-md-3">
        <input type="text" name="telefono" class="form-control" id="telefono" readonly="true" <? if (isset($telefono)) echo "value='$telefono'"?> size="70"/>
    </div>
   
</div>

<div class="form-group">                                
    <label class="col-md-2 control-label" for="txtcodigo">Ocupación: </label>                                
    <div class="col-md-3">
        <input type="text" name="ocupacion" class="form-control" id="ocupacion" readonly="true" <? if (isset($ocupacion)) echo "value='$ocupacion'"?> size="70"/>
    </div>
   
                           
    <label class="col-md-2 control-label" for="txtcodigo">Hijos: </label>                                
    <div class="col-md-3">
        <input type="text" name="hijos" class="form-control" id="hijos" readonly="true" <? if (isset($hijos)) echo "value='$hijos'"?> size="70"/>
    </div>
   
</div>

<div class="form-group">                                
    <label class="col-md-2 control-label" for="txtcodigo">Nombre del Adulto Responsable: </label>                                
    <div class="col-md-3">
        <input type="text" name="nombre_adulto_responsable" id="nombre_adulto_responsable" class="form-control"  value="<?if(isset($fetch33['nombre_adulto_responsable'])){ if($fetch33['nombre_adulto_responsable']!="") {echo $fetch33['nombre_adulto_responsable'];}}?>">
    </div>
   
                          
    <label class="col-md-2 control-label" for="txtcodigo">Cédula del Adulto Responsable: </label>                                
    <div class="col-md-3">
        <input type="text" name="cedula_adulto_responsable" id="cedula_adulto_responsable" class="form-control"  value="<?if(isset($fetch33['cedula_adulto_responsable'])){ if($fetch33['cedula_adulto_responsable']!="") {echo $fetch33['cedula_adulto_responsable'];}}?>">
    </div>
   
</div>
    
<div class="form-group">
    <label class="col-md-2 control-label">Fecha Entrevista: </label>
    <div class="col-md-3">
            <div class="input-group date date-picker" data-provide="datepicker" data-date-end-date="0d">	
                    <input type="text" class="form-control" name="fecha_entrevista" id="fecha_entrevista" value="<?if(isset($fetch33['fecha_entrevista'])){ if($fetch33['fecha_entrevista']!="0000-00-00") {echo fecha($fetch33['fecha_entrevista']);}}?>">
                    <span class="input-group-btn">
                            <button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
                    </span>
            </div>
    </div>

    <label class="col-md-2 control-label">Fecha Elaboración Informe: </label>
    <div class="col-md-3">
            <div class="input-group date date-picker" data-provide="datepicker" data-date-end-date="0d">	
                    <input type="text" class="form-control" name="fecha_elaboracion" id="fecha_elaboracion" value="<?if(isset($fetch33['fecha_elaboracion'])){ if($fetch33['fecha_elaboracion']!="0000-00-00") {echo fecha($fetch33['fecha_elaboracion']);}}?>">
                    <span class="input-group-btn">
                            <button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
                    </span>
            </div>
    </div>
</div>
    

    


</fieldset>

<fieldset>
    <legend >II.- Motivo de la Investigación</legend>

<div class="form-group">

     <label class="col-md-2 control-label" for="txtcodigo"></label>                               
    <div class="col-md-7">
         <textarea name="motivo_investigacion" id="motivo_investigacion" class="form-control" rows="3" style="resize:vertical;"><?php if (isset($fetch33['motivo_investigacion'])) echo "$fetch33[motivo_investigacion]"?></textarea>
    </div>
</div>
</fieldset>

<fieldset>
    <legend >III.- Situación Socioeconómica</legend>

<div class="form-group">

     <label class="col-md-2 control-label" for="txtcodigo">A.- Relaciones Familiares:</label>                               
    <div class="col-md-7">
         <textarea name="socioeconomica_relaciones_familiares" id="socioeconomica_relaciones_familiares" class="form-control" rows="3" style="resize:vertical;"><?php if (isset($fetch33['socioeconomica_relaciones_familiares'])) echo "$fetch33[socioeconomica_relaciones_familiares]"?></textarea>
    </div>
</div>

<div class="form-group">

     <label class="col-md-2 control-label" for="txtcodigo">B.- Salud:</label>                               
    <div class="col-md-7">
         <textarea name="socioeconomica_salud" id="socioeconomica_salud" class="form-control" rows="3" style="resize:vertical;"><?php if (isset($fetch33['socioeconomica_salud'])) echo "$fetch33[socioeconomica_salud]"?></textarea>
    </div>
</div>

<div class="form-group">

     <label class="col-md-2 control-label" for="txtcodigo">C.- Vivienda:</label>                               
    <div class="col-md-7">
         <textarea name="socioeconomica_vivienda" id="socioeconomica_vivienda" class="form-control" rows="3" style="resize:vertical;"><?php if (isset($fetch33['socioeconomica_vivienda'])) echo "$fetch33[socioeconomica_vivienda]"?></textarea>
    </div>
</div>
    
<div class="form-group">

     <label class="col-md-2 control-label" for="txtcodigo">D.- Económica:</label>                               
    <div class="col-md-7">
         <textarea name="socioeconomica_economica" id="socioeconomica_economica" class="form-control" rows="3" style="resize:vertical;"><?php if (isset($fetch33['socioeconomica_economica'])) echo "$fetch33[socioeconomica_economica]"?></textarea>
    </div>
</div>
    
<div class="form-group">

     <label class="col-md-2 control-label" for="txtcodigo">Ingresos:</label>                               
    <div class="col-md-7">
         <textarea name="socioeconomica_ingresos" id="socioeconomica_ingresos" class="form-control" rows="3" style="resize:vertical;"><?php if (isset($fetch33['socioeconomica_ingresos'])) echo "$fetch33[socioeconomica_ingresos]"?></textarea>
    </div>
</div>
    
<div class="form-group">

     <label class="col-md-2 control-label" for="txtcodigo">Egresos:</label>                               
    <div class="col-md-7">
         <textarea name="socioeconomica_egresos" id="socioeconomica_egresos" class="form-control" rows="3" style="resize:vertical;"><?php if (isset($fetch33['socioeconomica_egresos'])) echo "$fetch33[socioeconomica_egresos]"?></textarea>
    </div>
</div>

<div class="form-group">
    <label class="col-md-2 control-label">Total Ingresos: </label>
    <div class="col-md-7"><input type="text" name="socioeconomica_total_ingresos" id="socioeconomica_total_ingresos" class="form-control" value="<?if(isset($fetch33['socioeconomica_total_ingresos'])){ if($fetch33['socioeconomica_total_ingresos']!="") {echo $fetch33['socioeconomica_total_ingresos'];}}?>"></div>
</div>

<div class="form-group">
    <label class="col-md-2 control-label">Total Egresos: </label>
    <div class="col-md-7"><input type="text" name="socioeconomica_total_egresos" id="socioeconomica_total_egresos" class="form-control" value="<?if(isset($fetch33['socioeconomica_total_egresos'])){ if($fetch33['socioeconomica_total_egresos']!="") {echo $fetch33['socioeconomica_total_egresos'];}}?>"></div>
</div>

<div class="form-group">
    <label class="col-md-2 control-label">Total Neto Disponible: </label>
    <div class="col-md-7"><input type="text" name="socioeconomica_total_disponible" id="socioeconomica_total_disponible" class="form-control" value="<?if(isset($fetch33['socioeconomica_total_disponible'])){ if($fetch33['socioeconomica_total_disponible']!="") {echo $fetch33['socioeconomica_total_disponible'];}}?>"></div>
</div>
    
</fieldset>

<fieldset>
    <legend >IV.- Situación Encontrada</legend>

<div class="form-group">

     <label class="col-md-2 control-label" for="txtcodigo"></label>                               
    <div class="col-md-7">
         <textarea name="situacion_encontrada" id="situacion_encontrada" class="form-control" rows="3" style="resize:vertical;"><?php if (isset($fetch33['situacion_encontrada'])) echo "$fetch33[situacion_encontrada]"?></textarea>
    </div>
</div>
</fieldset>

<fieldset>
    <legend >Labor del Trabajador social</legend>

<div class="form-group">

     <label class="col-md-2 control-label" for="txtcodigo"></label>                               
    <div class="col-md-7">
         <textarea name="labor_trabajador_social" id="labor_trabajador_social" class="form-control" rows="3" style="resize:vertical;"><?php if (isset($fetch33['labor_trabajador_social'])) echo "$fetch33[labor_trabajador_social]"?></textarea>
    </div>
</div>
</fieldset>

<fieldset>
    <legend >V. Diagnóstico</legend>

<div class="form-group">

     <label class="col-md-2 control-label" for="txtcodigo"></label>                               
    <div class="col-md-7">
         <textarea name="diagnostico" id="diagnostico" class="form-control" rows="3" style="resize:vertical;"><?php if (isset($fetch33['diagnostico'])) echo "$fetch33[diagnostico]"?></textarea>
    </div>
</div>
</fieldset>

<fieldset>
    <legend >VI. Recomendaciones</legend>

<div class="form-group">

     <label class="col-md-2 control-label" for="txtcodigo"></label>                               
    <div class="col-md-7">
         <textarea name="recomendaciones" id="recomendaciones" class="form-control" rows="3" style="resize:vertical;"><?php if (isset($fetch33['recomendaciones'])) echo "$fetch33[recomendaciones]"?></textarea>
    </div>
</div>
</fieldset>
<?
