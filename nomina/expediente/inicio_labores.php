<?
$conexion = conexion();
       
    if(isset($_GET['cedula']) && $_GET['cedula']!='')
    {
           
            $sql2 = "SELECT   fecing, inicio_periodo, fin_periodo, fecha_permanencia, estado
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
    <legend >Corrección de Datos</legend>
    
<div class="form-group">                                
    <label  class="col-md-2 control-label" for="txtcodigo">Fecha Ingreso (Anterior):</label>                                
    <div class="col-md-2">
        <input type="text" name="fecha_inicio_anterior" class="form-control" id="fecha_inicio_anterior" readonly="true" value="<?if(isset($fetch2['fecing'])) echo fecha($fetch2['fecing']);?>" size="70"/>
    </div>                          
    <label  class="col-md-2 control-label" for="txtcodigo">Fecha Ingreso (Nueva):</label>                                
    <div class="col-md-2">
       <div class="input-group date date-picker" data-provide="datepicker" data-date-format="dd/mm/yyyy" >
           <input size="10" type="text" name="fecha_inicio" class="form-control" placeholder="(dd/mm/aaaa)" id="fecha_inicio" value="<?if($fetch33['fecha_inicio']!="0000-00-00") { echo fecha($fetch33['fecha_inicio']); } else { echo fecha($fetch2['fecing']); } ?>">
           <span class="input-group-btn">
               <button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
           </span>
       </div>
    </div>
</div>

<div class="form-group">                                
    <label  class="col-md-2 control-label" for="txtcodigo">Fecha Permanencia (Anterior):</label>                                
    <div class="col-md-2">
        <input type="text" name="fecha_permanencia_anterior" class="form-control" id="fecha_permanencia_anterior" readonly="true" value="<?if(isset($fetch2['fecha_permanencia'])) echo fecha($fetch2['fecha_permanencia']);?>" size="70"/>
    </div>                          
    <label  class="col-md-2 control-label" for="txtcodigo">Fecha Permanencia (Nueva):</label>                                
    <div class="col-md-2">
       <div class="input-group date date-picker" data-provide="datepicker" data-date-format="dd/mm/yyyy" >
           <input size="10" type="text" name="fecha_permanencia" class="form-control" placeholder="(dd/mm/aaaa)" id="fecha_permanencia" value="<?if($fetch33['fecha_permanencia']!="0000-00-00") { echo fecha($fetch33['fecha_permanencia']); } else { echo fecha($fetch2['fecha_permanencia']); } ?>">
           <span class="input-group-btn">
               <button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
           </span>
       </div>
    </div>
</div>
    
<div class="form-group">                                
    <label  class="col-md-2 control-label" for="txtcodigo">Fecha Inicio Periodo (Anterior):</label>                                
    <div class="col-md-2">
        <input type="text" name="fecha_inicio_periodo_anterior" class="form-control" id="fecha_inicio_periodo_anterior" readonly="true" value="<?if(isset($fetch2['inicio_periodo'])) echo fecha($fetch2['inicio_periodo']);?>" size="70"/>
    </div>
    <label  class="col-md-2 control-label" for="txtcodigo">Fecha Inicio Periodo (Nueva):</label>                                
    <div class="col-md-2">
       <div class="input-group date date-picker" data-provide="datepicker" data-date-format="dd/mm/yyyy" >
           <input size="10" type="text" name="fecha_inicio_periodo" class="form-control" placeholder="(dd/mm/aaaa)" id="fecha_inicio_periodo" value="<?if($fetch33['fecha_inicio_periodo']!="0000-00-00") { echo fecha($fetch33['fecha_inicio_periodo']); } else { echo fecha($fetch2['inicio_periodo']); } ?>">
           <span class="input-group-btn">
               <button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
           </span>
       </div>
    </div>    
</div>
    
<div class="form-group">                                
    <label  class="col-md-2 control-label" for="txtcodigo">Fecha Fin Periodo (Anterior):</label>                                
    <div class="col-md-2">
        <input type="text" name="fecha_fin_periodo_anterior" class="form-control" id="fecha_fin_periodo_anterior" readonly="true" value="<?if(isset($fetch2['fin_periodo'])) echo fecha($fetch2['fin_periodo']);?>" size="70"/>
    </div>
    <label  class="col-md-2 control-label" for="txtcodigo">Fecha Fin Periodo (Nueva):</label>                                
    <div class="col-md-2">
       <div class="input-group date date-picker" data-provide="datepicker" data-date-format="dd/mm/yyyy" >
           <input size="10" type="text" name="fecha_fin_periodo" class="form-control" placeholder="(dd/mm/aaaa)" id="fecha_fin_periodo" value="<?if($fetch33['fecha_fin_periodo']!="0000-00-00") { echo fecha($fetch33['fecha_fin_periodo']); } else { echo fecha($fetch2['fin_periodo']); } ?>">
           <span class="input-group-btn">
               <button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
           </span>
       </div>
    </div>
</div>

<div class="form-group">
    <label for="txtcodigo" class="col-md-2 control-label">Numero de Decreto (Anterior): </label>                                    
    <div class="col-md-2">
        <input class="form-control" type="text" name="numero_decreto_anterior" id="numero_decreto_anterior" readonly="true"  <? if (isset($fetch2['num_decreto'])) echo "value='$fetch2[num_decreto]'"?> /> 
    </div>
    
    <label class="col-md-2 control-label" for="txtcodigo">Numero de Decreto (Nuevo):</label>
    <div class="col-md-3">
         <input type="text" name="numero_decreto_nuevo" class="form-control" id="numero_decreto_nuevo" value="<?if($fetch33['numero_decreto']!="0000-00-00") { echo $fetch33['numero_decreto']; } else { echo $fetch2['num_decreto']; } ?>" size="30"/> 
    </div>    
</div>
    
<div class="form-group">                                
    <label  class="col-md-2 control-label" for="txtcodigo">Fecha Decreto (Anterior):</label>                                
    <div class="col-md-2">
        <input type="text" name="fecha_decreto_anterior" class="form-control" id="fecha_decreto_anterior" readonly="true" value="<?if(isset($fetch2['fecha_decreto'])) echo fecha($fetch2['fecha_decreto']);?>" size="70"/>
    </div>
    <label  class="col-md-2 control-label" for="txtcodigo">Fecha Decreto (Nueva):</label>                                
    <div class="col-md-2">
       <div class="input-group date date-picker" data-provide="datepicker" data-date-format="dd/mm/yyyy" >
           <input size="10" type="text" name="fecha_decreto_nuevo" class="form-control" placeholder="(dd/mm/aaaa)" id="fecha_decreto_nuevo" value="<?if($fetch33['fecha_decreto_ingreso_nuevo']!="0000-00-00") { echo fecha($fetch33['fecha_decreto_ingreso_nuevo']); } else { echo fecha($fetch2['fecha_decreto']); } ?>">
           <span class="input-group-btn">
               <button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
           </span>
       </div>
    </div>
</div>
    
<div class="form-group">
    <label for="txtcodigo" class="col-md-2 control-label">Situación (Anterior): </label>                                    
    <div class="col-md-2">
        <input class="form-control" type="text" name="situacion_anterior" id="situacion_anterior" readonly="true"  <? if (isset($fetch2['estado'])) echo "value='$fetch2[estado]'"?> /> 
    </div>
</div>
    
<div class="form-group">
    <label class="col-md-2 control-label" for="txtcodigo">Situación (Nueva): </label>
    <div class="col-md-6">                               
            
        <select name="situacion_nueva" id="situacion_nueva" class="form-control">
                
                <?php                     
                    $consulta_estatus="SELECT codigo,situacion FROM nomsituaciones WHERE `codigo` IN ( 1, 40, 42 ) ";
                    $resultado_estatus=sql_ejecutar($consulta_estatus);
                    while($fila_estatus=fetch_array($resultado_estatus))
                    {
                        //echo "AQUI";
                        if ($codigo=='')
                        {
                            if(!$fetch2['estado'])
                            {?>
                                <option  value="<?=$fila_estatus['codigo'];?>"><?=utf8_encode($fila_estatus['situacion']);?></option>
                            <?}
                            else
                            { 
                               if($fetch2['estado']==$fila_estatus['situacion'])
                               {?>
                                    <option  value="<?=$fila_estatus['codigo'];?>" selected><?=utf8_encode($fila_estatus['situacion']);?></option> 
                               <?}
                               else
                               {?>
                                    <option  value="<?=$fila_estatus['codigo'];?>"><?=utf8_encode($fila_estatus['situacion']);?></option>
                               <?}
                            }
                        }
                        else
                        {
                            if($fetch33['situacion_nueva']==NULL || $fetch33['situacion_nueva']==0 )
                            {
                                if($fetch2['estado']==$fila_estatus['situacion'])
                               {?>
                                    <option  value="<?=$fila_estatus['codigo'];?>" selected><?=utf8_encode($fila_estatus['situacion']);?></option> 
                               <?}
                               else
                               {?>
                                    <option  value="<?=$fila_estatus['codigo'];?>"><?=utf8_encode($fila_estatus['situacion']);?></option>
                               <?}
                            }
                            else
                            {
                                if($fetch33['situacion_nueva']==$fila_estatus['codigo'])
                                {?>
                                     <option  value="<?=$fila_estatus['codigo'];?>" selected><?=utf8_encode($fila_estatus['situacion']);?></option> 
                                <?}
                                else
                                {?>
                                     <option  value="<?=$fila_estatus['codigo'];?>"><?=utf8_encode($fila_estatus['situacion']);?></option>
                                <?}
                            }
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


