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
    <label class="col-md-2 control-label" for="txtcodigo">Fecha Permanencia: </label>                                
    <div class="col-md-3">
        <input type="text" name="fecha_ingreso" class="form-control" id="fecha_ingreso" readonly="true" value="<?if(isset($fecha_permanencia)) echo fecha($fecha_permanencia);?>" size="70"/>
    </div>
    <label for="txtcodigo" class="col-md-2 control-label">Seguro Social: </label>                                    
    <div class="col-md-2">
        <input class="form-control" type="text" name="seguro_social" id="seguro_social" readonly="true"  <? if (isset($seguro_social)) echo "value='$seguro_social'"?> /> 
    </div>

</div>
</fieldset>

<fieldset>
    <legend >Ajuste</legend>
    <div class="form-group">
        <label class="col-md-2 control-label">Tipo: </label>
        <div class="col-md-7">
            <?php
                            $aumenta = 'checked';													
                            $disminuye = '';
                            if ($codigo!='')
                            {
                                if($fetch33['tipo_ajuste']==1)
                                {
                                    $aumenta = 'checked';													
                                    $disminuye = '';
                                }
                                else
                                {
                                    $aumenta = '';													
                                    $disminuye = 'checked';
                                }
                            }   

            ?>
            <div class="radio-list">
                <label class="radio-inline">
                    <input type="radio" name="tipo_ajuste" id="aumenta" value="1"<?=$aumenta?>> Aumenta</label>
                <label class="radio-inline">
                <input type="radio" name="tipo_ajuste" id="disminuye" value="2" <?=$disminuye?>> Disminuye</label>
            </div>
        </div>
    </div>
     <div class="form-group">
        <label class="col-md-2 control-label" for="txtcodigo">Fecha:</label>                                
        <div class="col-md-2">
            <div class="input-group date date-picker" data-provide="datepicker" data-date-format="dd/mm/yyyy" >
                <input type="text" name="fecha" class="form-control" id="fecha" placeholder="(dd/mm/aaaa)" value="<? if (isset($fetch33['fecha'])) echo fecha($fetch33['fecha']);?>">                                        
                <span class="input-group-btn">
                    <button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
                </span>
            </div>                                    
        </div>        
        <label class="col-md-2 control-label" for="txtcodigo">Fecha Aprobación:</label>                                
        <div class="col-md-2">
            <div class="input-group date date-picker" data-provide="datepicker" data-date-format="dd/mm/yyyy" >                                                                                                                        
                <input type="text" name="fecha_aprobado" class="form-control" placeholder="(dd/mm/aaaa)" id="fecha_aprobado" onclick="javascript:inicio();" value="<?if(isset($fetch33['fecha_aprobado'])) echo fecha($fetch33['fecha_aprobado']);?>">
                <span class="input-group-btn">
                    <button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
                </span>
            </div>                                    
        </div>
    </div>
    
    <div class="form-group">
        <label class="col-md-2 control-label" for="txtcodigo">Fecha Inicio:</label>                                
        <div class="col-md-2">
            <div class="input-group date date-picker" data-provide="datepicker" data-date-format="dd/mm/yyyy" >                                                                                                                        
                <input type="text" name="fecha_inicio" class="form-control" placeholder="(dd/mm/aaaa)" id="fecha_inicio" onchange="calcular_dias();" onclick="javascript:inicio();" value="<?if(isset($fetch33['fecha_inicio'])) echo fecha($fetch33['fecha_inicio']);?>">
                <span class="input-group-btn">
                    <button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
                </span>
            </div>                                    
        </div>

        <label class="col-md-2 control-label" for="txtcodigo">Fecha Fin</label>                                
        <div class="col-md-2">  
            <div class="input-group date date-picker" data-provide="datepicker" data-date-format="dd/mm/yyyy" >                                                                                                                                                                
                <input  type="text" name="fecha_fin" class="form-control" placeholder="(dd/mm/aaaa)" id="fecha_fin"  onchange="calcular_dias();" onclick="javascript:inicio();" value="<?if(isset($fetch33['fecha_fin'])) echo fecha($fetch33['fecha_fin']);?>">
                <span class="input-group-btn">
                    <button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
                </span>
            </div>                                                                                                                                                
        </div>
    </div>

<div id="tiempos">
    <div class="form-group">   
         <label class="col-md-2 control-label" for="txtcodigo">Días:</label>                               
        <div class="col-md-3">
            <input type="text" class="form-control"  name="dias" id="dias" onblur="calcular_duracion_tiempo();" maxlength="5" <? if (isset($fetch33['dias'])){ echo "value='$fetch33[dias]'";} else{ echo "value=''";}?>/>                                
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-2 control-label" for="txtcodigo">Horas:</label>    
        <div class="col-md-3">
            <input type="text" class="form-control" size="10" name="horas" id="horas" onblur="calcular_duracion_tiempo();" maxlength="10" <? if (isset($fetch33['horas'])) echo "value='$fetch33[horas]'"?>/>
        </div>

    </div>

    <div class="form-group">
        <label class="col-md-2 control-label" for="txtcodigo">Minutos:</label>    
        <div class="col-md-3">
            <input type="text" class="form-control" size="10" name="minutos" id="minutos" onblur="calcular_duracion_tiempo();" maxlength="10" <? if (isset($fetch33['minutos'])) echo "value='$fetch33[minutos]'"?>/>
        </div>
    </div>

    <div class="form-group">                            
    <label class="col-md-2 control-label" for="txtcodigo">Duración:</label>                               
        <div class="col-md-3">
            <input type="text" class="form-control"  name="duracion" id="duracion"  readonly maxlength="2" <? if (isset($fetch33['duracion'])){ echo "value='$fetch33[duracion]'";} else { echo "value=''";}?>/>                                
        </div>
   
     <label class="col-md-1 control-label" for="txtcodigo">Disponible:</label>                               
        <div class="col-md-3">
            <input type="text" class="form-control"  name="restante" id="restante" readonly maxlength="2" <? if (isset($fetch33['restante'])){ echo "value='$fetch33[restante]'";} else { echo "value=''";}?>/>                                
        </div>
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
