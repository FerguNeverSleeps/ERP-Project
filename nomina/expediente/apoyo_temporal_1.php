<fieldset>
    <legend >Posición Actual</legend>
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
    <label for="txtcodigo" class="col-md-2 control-label">Departamento: </label>                                    
    <div class="col-md-2">
        <input class="form-control" type="text" name="departamento_anterior" id="departamento_anterior" readonly="true"  <? if (isset($departamento)) echo "value='$departamento'"?> /> 
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
    <label class="col-md-2 control-label" for="txtcodigo">Fecha Inicio: </label>                                
    <div class="col-md-3">
        <input type="text" name="fecha_inicio" class="form-control" id="fecha_inicio" readonly="true" value="<?if(isset($fecha_inicio)) echo fecha($fecha_inicio);?>" size="70"/>
    </div>
    <label for="txtcodigo" class="col-md-2 control-label">Seguro Social: </label>                                    
    <div class="col-md-2">
        <input class="form-control" type="text" name="seguro_social" id="seguro_social" readonly="true"  <? if (isset($seguro_social)) echo "value='$seguro_social'"?> /> 
    </div>

</div>
</fieldset>

<fieldset>
    <legend >Posición Nueva</legend>

<div class="form-group">

   <label class="col-md-2 control-label" for="txtcodigo">Departamento:</label>                                
   <div class="col-md-7">
       <select name="departamento_nuevo2" id="departamento_nuevo2" class="select2 form-control">
           <option value="">Seleccione</option>
           <?
           $consulta="SELECT IdDepartamento, Descripcion FROM departamento";
           $resultado4= sql_ejecutar($consulta);
           while($fila_funcion=fetch_array($resultado4))
            {
                //echo "AQUI";
                if(!$fetch33['departamento_nuevo'])
                {?>
                    <option  value="<?=$fila_funcion['IdDepartamento'];?>"><?=utf8_encode($fila_funcion['Descripcion']);?></option>
                <?}
                else
                { 
                   if($fetch33['departamento_nuevo']==$fila_funcion['IdDepartamento'])
                   {?>
                        <option  value="<?=$fila_funcion['IdDepartamento'];?>" selected><?=utf8_encode($fila_funcion['Descripcion']);?></option> 
                   <?}
                   else
                   {?>
                        <option  value="<?=$fila_funcion['IdDepartamento'];?>"><?=utf8_encode($fila_funcion['Descripcion']);?></option>
                   <?}
                }                  

            }           
           ?>

           </select>
   </div>
</div>    

<div class="form-group">                                
    <label  class="col-md-2 control-label" for="txtcodigo">Fecha Inicio:</label>                                
    <div class="col-md-2">
       <div class="input-group date date-picker" data-provide="datepicker" data-date-format="dd/mm/yyyy" >
           <input size="10" type="text" name="fecha_inicio" class="form-control" placeholder="(dd/mm/aaaa)" id="fecha_inicio" value="<?if(isset($fetch33['fecha_inicio'])) echo fecha($fetch33['fecha_inicio']);?>">
           <span class="input-group-btn">
               <button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
           </span>
       </div>
    </div>

    <label  class="col-md-2 control-label" for="txtcodigo">Fecha Fin:</label>                                
    <div class="col-md-2">
       <div class="input-group date date-picker" data-provide="datepicker" data-date-format="dd/mm/yyyy" >
           <input size="10" type="text" name="fecha_fin" class="form-control" placeholder="(dd/mm/aaaa)" id="fecha_fin" value="<?if(isset($fetch33['fecha_fin'])) echo fecha($fetch33['fecha_fin']);?>">
           <span class="input-group-btn">
               <button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
           </span>
       </div>
    </div>
</div>

<<div class="form-group">

        <label class="col-md-2 control-label" for="txtcodigo">Observaciones: </label>                               
       <div class="col-md-7">
            <textarea name="descripcion" id="descripcion" class="form-control" rows="3" style="resize:vertical;"><?php if (isset($fetch33['descripcion'])) echo "$fetch33[descripcion]"?></textarea>
       </div>
   </div>  
</fieldset>
<?

