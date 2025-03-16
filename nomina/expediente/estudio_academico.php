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
    <legend >Estudio Académico</legend>
<div class="form-group">
    <label class="col-md-2 control-label" for="txtcodigo">Tipo registro:</label>
    <div class="col-md-7">                               
            <div  id="tipo_tipo">
            <SELECT name="tipo_tiporegistro" class="select2 form-control" id="tipo_tiporegistro">
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
    <label class="col-md-2 control-label" for="txtcodigo">Institucion Educativa:</label>
    <div class="col-md-7">                               
            <div  id="tipo_tipo">
            <SELECT name="institucion_educativa_nueva"class="select2 form-control" id="institucion_educativa_nueva">
            <option value="">Seleccione</option>
            <?php                        
                $sql_institucion = "SELECT  * FROM institucion_educativa";
                $resultado_institucion=sql_ejecutar($sql_institucion);
                while($fila_institucion=fetch_array($resultado_institucion))
                {
                    //echo "AQUI";
                    if ($codigo=='')
                    {                        
                        {?>
                            <option  value="<?=$fila_institucion['id_institucion'];?>"><?=utf8_encode($fila_institucion['nombre']);?></option>
                        <?}
                        
                    }
                    else
                    {
                        if($fetch33['institucion_educativa_nueva']==$fila_institucion['id_institucion'])
                        {?>
                             <option  value="<?=$fila_institucion['id_institucion'];?>" selected><?=utf8_encode($fila_institucion['nombre']);?></option> 
                        <?}
                        else
                        {?>
                             <option  value="<?=$fila_institucion['id_institucion'];?>"><?=utf8_encode($fila_institucion['nombre']);?></option>
                        <?}         
                    }

                }
                
            ?>
            </SELECT>
            </div>            
    </div>
</div>

<div class="form-group">
    <label class="col-md-2 control-label" for="txtcodigo">Titulo Profesional:</label>
    <div class="col-md-7">                               
            <div  id="tipo_tipo">
            <SELECT name="titulo_profesional" class="select2 form-control" id="titulo_profesional">
            <option value="">Seleccione</option>
            <?php 
                $sql_titulo = "SELECT  * FROM nomprofesiones";
                $resultado_titulo=sql_ejecutar($sql_titulo);
                while($fila_titulo=fetch_array($resultado_titulo))
                {
                    //echo "AQUI";
                    if ($codigo=='')
                    {                        
                        {?>
                            <option  value="<?=$fila_titulo['codorg'];?>"><?=utf8_encode($fila_titulo['descrip']);?></option>
                        <?}
                        
                    }
                    else
                    {
                        if($fetch33['titulo_profesional']==$fila_titulo['codorg'])
                        {?>
                             <option  value="<?=$fila_titulo['codorg'];?>" selected><?=utf8_encode($fila_titulo['descrip']);?></option> 
                        <?}
                        else
                        {?>
                             <option  value="<?=$fila_titulo['codorg'];?>"><?=utf8_encode($fila_titulo['descrip']);?></option>
                        <?}         
                    }

                }
                
            ?>
            </SELECT>
            </div>            
    </div>
</div>


<div class="form-group">
    <label class="col-md-2 control-label" for="txtcodigo">Fecha Inicio:</label>                                
    <div class="col-md-3">

        <div class="input-group date date-picker" data-provide="datepicker" data-date-format="dd/mm/yyyy">
            <input size="10" type="text" name="fecha_inicio" id="fecha_inicio" class="form-control" readonly="readonly" placeholder="(dd/mm/aaaa)" value="<?if(isset($fetch33['fecha_inicio'])) echo fecha($fetch33['fecha_inicio']);?>">
            <span class="input-group-btn">
                <button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
            </span>    
        </div>                                                                       
    </div>
    <label class="col-md-1 control-label" for="txtcodigo"> Fecha Fin:</label>                                                                  
    <div class="col-md-3">
        <div class="input-group date date-picker" data-provide="datepicker" data-date-format="dd/mm/yyyy">
            <input size="10" type="text" name="fecha_fin" id="fecha_fin" class="form-control" readonly="readonly" placeholder="(dd/mm/aaaa)" value="<?if(isset($fetch33['fecha_fin'])) echo fecha($fetch33['fecha_fin']);?>">
            <span class="input-group-btn">
                <button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
            </span>    
        </div>                                    
    </div>                                                      
</div>

 <div class="form-group">                                          
    <label class="col-md-2 control-label" for="txtcodigo">Duraci&oacute;n: </label>                                
    <div class="col-md-3">
          <input type="text" name="duracion" id="duracion" class="form-control"  placeholder="" value="<?if(isset($fetch33['dias'])) echo $fetch33['dias'];?>">                             
    </div>                    
</div> 
<div class="form-group">                            
         
        <label for="txtcodigo" class="col-md-2 control-label">Ejerce Actualmente: </label>                                    
        <div class="col-md-2">
            <input type="checkbox" name="ejerce" id="ejerce" value="1" <? if (isset($fetch33['ejerce'])) echo "checked='true'"?>/>
        </div>
        <label for="txtcodigo" class="col-md-2 control-label">Tiene Idoneidad: </label>                                    
        <div class="col-md-2">
            <input type="checkbox" name="idoneidad" id="idoneidad" value="1" <? if (isset($fetch33['idoneidad'])) echo "checked='true'"?>/>
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

