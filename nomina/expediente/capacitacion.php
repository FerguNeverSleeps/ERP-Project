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
        <input type="text" name="nombre_apellido" class="form-control" id="nombre_apellido" readonly="true" <? if (isset($nombre)) echo "value='".utf8_encode ($nombre)."'"?> size="70"/>
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


</fieldset>

<fieldset>
    <legend >Capacitación</legend>
<div class="form-group">

   <label class="col-md-2 control-label" for="txtcodigo">Tipo: </label>                            
   <div class="col-md-7">
       <select name="tipo_tiporegistro" id="tipo_tiporegistro" class="form-control">
           <?
               while($fila=fetch_array($resultado)){?>
                   <option value="<?=$fila['id_expediente_subtipo']?>"><?=$fila['nombre_subtipo']?></option>

               <?} ?>
       </select>
   </div>
  
</div>

<div class="form-group">                            
   <label class="col-md-2 control-label" for="txtcodigo">Instituci&oacute;n:</label>                            
   <div class="col-md-7">
       <input type="text" name="institucion" class="form-control" id="institucion" <? if (isset($fetch33['institucion'])) echo "value='$fetch33[institucion]'"?> size="30"/> 
   </div>
<!--   <label class="col-md-1 control-label" for="txtcodigo">Nivel Actual:</label>                            
   <div class="col-md-3">
       <select name="nivel_actual" id="nivel_actual" class="form-control">
           <option value="Primaria" <? if ($fetch33['nivel_actual']=="Primaria") echo "selected='true'"?>>Primaria</option>
           <option value="Basico" <? if ($fetch33['nivel_actual']=="Basico") echo "selected='true'"?>>Basico</option>
           <option value="Diversificado" <? if ($fetch33['nivel_actual']=="Diversificado") echo "selected='true'"?>>Diversificado</option>
           <option value="Pre-Grado" <? if ($fetch33['nivel_actual']=="Pre-Grado") echo "selected='true'"?>>Pre-Grado</option>
           <option value="Post-Grado" <? if ($fetch33['nivel_actual']=="Post-Grado") echo "selected='true'"?>>Post-Grado</option>
       </select>
   </div>-->
</div>


<div class="form-group">
   <label class="col-md-2 control-label" for="txtcodigo">Fecha Inicio:</label>                            
   <div class="col-md-3">
       <div class="input-group date date-picker" data-provide="datepicker" data-date-format="dd/mm/yyyy" >
               <input size="10" type="text" name="fecha_inicio" id="fecha_inicio" class="form-control" readonly="readonly" placeholder="(dd/mm/aaaa)" value="<?if(isset($fetch33['fecha_inicio'])) echo fecha($fetch33['fecha_inicio']);?>">
               <span class="input-group-btn">
                   <button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
               </span>    
           </div>                                
   </div>                            

   <label class="col-md-1 control-label" for="txtcodigo">Fecha Fin:</label>                            
   <div class="col-md-3">
       <div class="input-group date date-picker" data-provide="datepicker" data-date-format="dd/mm/yyyy" >
               <input size="10" type="text" name="fecha_fin" id="fecha_fin" class="form-control" readonly="readonly" placeholder="(dd/mm/aaaa)" value="<?if(isset($fetch33['fecha_fin'])) echo fecha($fetch33['fecha_fin']);?>">
               <span class="input-group-btn">
                   <button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
               </span>    
       </div>  
   </div>

</div>

<div class="form-group">
   <label class="col-md-2 control-label" for="txtcodigo">Duraci&oacute;n: </label>                            
   <div class="col-md-7">
       <input type="text" name="duracion" id="duracion" class="form-control" <? if (isset($fetch33['duracion'])) echo "value='$fetch33[duracion]'"?> size="10"/>
   </div>
<!--   <label class="col-md-1 control-label" for="txtcodigo">Costo por persona: </label>                            
   <div class="col-md-3">
       <input type="text" name="costo_persona" id="costo_persona" class="form-control" <? if (isset($fetch33['dias'])) echo "value='$fetch33[dias]'"?> size="10"/>
   </div>-->

</div>

<div class="form-group">

   <label class="col-md-2 control-label" for="txtcodigo">Especialista: </label>                            
   <div class="col-md-7">
       <input type="text" name="nombre_especialista" class="form-control" id="nombre_especialista" <? if (isset($fetch33['nombre_especialista'])) echo "value='$fetch33[nombre_especialista]'"?> size="30"/>
   </div>                      
<!--   <label class="col-md-1 control-label" for="txtcodigo">Cant. Personas:</label>                            
   <div class="col-md-3"> 
       <input type="text" name="num_participantes" id="num_participantes" class="form-control" <? if (isset($fetch33['num_participantes'])) echo "value='$fetch33[num_participantes]'"?> size="10"/>
   </div>-->
</div>
<div class="form-group">

     <label class="col-md-2 control-label" for="txtcodigo">Observaciones: </label>                               
    <div class="col-md-7">
         <textarea name="descripcion" id="descripcion" class="form-control" rows="3" style="resize:vertical;"><?php if (isset($fetch33['descripcion'])) echo "$fetch33[descripcion]"?></textarea>
    </div>
</div>
</fieldset>

<?

