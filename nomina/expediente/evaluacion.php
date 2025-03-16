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
    <label class="col-md-2 control-label" for="txtcodigo">Departamento: </label>                                
    <div class="col-md-7">
        <input type="text" name="departamento_ant" class="form-control" id="departamento_ant" readonly="true" <? if (isset($nivel2)) echo "value='$nivel2'"?> size="70"/>
        <input type="hidden" name="departamento_anterior" class="form-control" id="departamento_anterior" <? if (isset($codnivel2)) echo "value='$codnivel2'"?> size="70"/>
    
    </div>    
</div>

</fieldset>

<fieldset>
    <legend >Evaluación</legend>
    
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
        <label  class="col-md-2 control-label" for="txtcodigo">Fecha Inicio Periodo:</label>                                
        <div class="col-md-3">
           <div class="input-group date date-picker" data-provide="datepicker" data-date-format="dd/mm/yyyy" >
               <input size="10" type="text" name="fecha_inicio_periodo" class="form-control" placeholder="(dd/mm/aaaa)" id="fecha_inicio_periodo" value="<?if($fetch33['fecha_inicio_periodo']!="0000-00-00") { echo fecha($fetch33['fecha_inicio_periodo']); } else { echo date("d/m/Y"); } ?>">
               <span class="input-group-btn">
                   <button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
               </span>
           </div>
        </div>
        
    </div>
    
    <div class="form-group">                                
       
         <label  class="col-md-2 control-label" for="txtcodigo">Fecha Fin Periodo:</label>                                
        <div class="col-md-3">
           <div class="input-group date date-picker" data-provide="datepicker" data-date-format="dd/mm/yyyy" >
               <input size="10" type="text" name="fecha_fin_periodo" class="form-control" placeholder="(dd/mm/aaaa)" id="fecha_fin_periodo" value="<?if($fetch33['fecha_fin_periodo']!="0000-00-00") { echo fecha($fetch33['fecha_fin_periodo']); } else { echo date("d/m/Y"); } ?>">
               <span class="input-group-btn">
                   <button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
               </span>
           </div>
        </div>
    </div>
    
    <div class="form-group">
        <label class="col-md-2 control-label" for="txtcodigo">Fecha Evaluación:</label>                                
        <div class="col-md-3">
            <div class="input-group date date-picker" data-provide="datepicker" data-date-format="dd/mm/yyyy" >
                <input type="text" name="fecha" class="form-control" id="fecha" placeholder="(dd/mm/aaaa)" size="10" value="<?if(isset($fetch33['fecha'])) echo fecha($fetch33['fecha']);?>" maxlength="10"/>
                <span class="input-group-btn">
                    <button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
                </span>
            </div>

        </div>
    </div>
    
    <div class="form-group">  
        <label class="col-md-2 control-label" for="txtcodigo">Puntaje: </label>                               
        <div class="col-md-3">
            <input type="text" name="puntaje" class="form-control" id="puntaje" <? if (isset($fetch33['puntaje'])) echo "value='$fetch33[puntaje]'"?> size="70"/>

        </div>
    </div>    

    <div class="form-group">
    
    <div class="form-group">
        <label class="col-md-2 control-label" for="txtcodigo">Funcionario Evaluador:</label>
        <div class="col-md-7">                               
                <div  id="tipo_tipo">
                <SELECT name="funcionario_evalua" class="form-control" id="funcionario_evalua">
                <option value="">Seleccione</option>
                <?php                        
                    $consulta_funcionario="SELECT personal_id, cedula, apenom FROM nompersonal";
                    $resultado_funcionario=sql_ejecutar($consulta_funcionario);
                    while($fila_funcionario=fetch_array($resultado_funcionario))
                    {
                        //echo "AQUI";
                        if ($codigo=='')
                        {                        
                            {?>
                                <option  value="<?=$fila_funcionario['personal_id'];?>"><?=$fila_funcionario['cedula']." - ".utf8_encode($fila_funcionario['apenom']);?></option>
                            <?}

                        }
                        else
                        {
                            if($fetch33['funcionario_evalua']==$fila_funcionario['personal_id'])
                            {?>
                                 <option  value="<?=$fila_funcionario['personal_id'];?>" selected><?=$fila_funcionario['cedula']." - ".utf8_encode($fila_funcionario['apenom']);?></option> 
                            <?}
                            else
                            {?>
                                 <option  value="<?=$fila_funcionario['personal_id'];?>"><?=$fila_funcionario['cedula']." - ".utf8_encode($fila_funcionario['apenom']);?></option>
                            <?}         
                        }

                    }

                ?>
                </SELECT>
                </div>            
        </div>
    </div>

     <label class="col-md-2 control-label" for="txtcodigo">Observaciones: </label>                               
    <div class="col-md-7">
         <textarea name="descripcion" id="descripcion" class="form-control" rows="3" style="resize:vertical;"><?php if (isset($fetch33['descripcion'])) echo "$fetch33[descripcion]"?></textarea>
    </div>
</div>
</fieldset>
<?

