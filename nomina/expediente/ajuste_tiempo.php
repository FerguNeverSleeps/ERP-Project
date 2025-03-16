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
    <div class="col-md-3">
        <input class="form-control" type="text" name="cedula" id="cedula" readonly="true" <? if (isset($cedula)) echo "value='$cedula'"?> /> 
    </div>

</div>

<div class="form-group">                                
    <label class="col-md-2 control-label" for="txtcodigo">Ficha: </label>                                
    <div class="col-md-3">
        <input type="text" name="ficha" class="form-control" id="ficha" readonly="true" <? if (isset($ficha)) echo "value='$ficha'"?> size="70"/>
    </div>
    <label for="txtcodigo" class="col-md-2 control-label">Proyecto Base: </label>                                    
    <div class="col-md-3">
        <input class="form-control" type="text" name="proyecto_base" id="proyecto_base" readonly="true"  <? if (isset($proyecto_base)) echo "value='$proyecto_base'"?> /> 
    </div>

</div>

<div class="form-group">                                
    <label class="col-md-2 control-label" for="txtcodigo">Planilla: </label>                                
    <div class="col-md-3">
        <input type="text" name="planilla_anterior" class="form-control" id="planilla_anterior" readonly="true" <? if (isset($pplanilla)) echo "value='$pplanilla'"?> size="70"/>
    </div>
    <label class="col-md-2 control-label" for="txtcodigo">Cargo: </label>                                
    <div class="col-md-3">
        <input type="text" name="cargo" class="form-control" id="cargo" readonly="true" <? if (isset($cargo)) echo "value='$cargo'"?> size="70"/>
    </div> 
</div>


</fieldset>

<fieldset>
    <legend >Ajuste</legend>
<div class="form-group">
    <label class="col-md-2 control-label" for="txtcodigo">Ajuste de:</label>
    <div class="col-md-7">                               
            <div  id="tipo_tipo">
                <SELECT name="tipo_tiporegistro" class="form-control" id="tipo_tiporegistro" onchange="cargar_ajuste_tiempo();">
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


    
<div class="form-group" id="tipo"> 
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
                <input type="radio" name="tipo_ajuste" id="aumenta" value="1" <?=$aumenta?>> Aumenta</label>
            <label class="radio-inline">
            <input type="radio" name="tipo_ajuste" id="disminuye" value="2" <?=$disminuye?>> Disminuye</label>
        </div>
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
<div id="tiempos">
    <div class="form-group">   
         <label class="col-md-2 control-label" for="txtcodigo">Días:</label>                               
        <div class="col-md-3">
            <input type="text" class="form-control"  name="dias" id="dias" onblur="calcular_duracion();" maxlength="5" <? if (isset($fetch33['dias'])){ echo "value='$fetch33[dias]'";} else{ echo "value=''";}?>/>                                
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-2 control-label" for="txtcodigo">Horas:</label>    
        <div class="col-md-3">
            <input type="text" class="form-control" size="10" name="horas" id="horas" onblur="calcular_duracion();" maxlength="10" <? if (isset($fetch33['horas'])) echo "value='$fetch33[horas]'"?>/>
        </div>

    </div>

    <div class="form-group">
        <label class="col-md-2 control-label" for="txtcodigo">Minutos:</label>    
        <div class="col-md-3">
            <input type="text" class="form-control" size="10" name="minutos" id="minutos" onblur="calcular_duracion();" maxlength="10" <? if (isset($fetch33['minutos'])) echo "value='$fetch33[minutos]'"?>/>
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
    
<!--<div id="medico"> 
<div class="form-group">
    <label class="col-md-2 control-label" for="txtcodigo">Centro Médico:</label>    
    <div class="col-md-7">
        <input type="text" class="form-control" size="100" name="id_centro" id="id_centro"  maxlength="100" <? if (isset($fetch33['id_centro'])) echo "value='$fetch33[id_centro]'"?>/>
    </div>
</div>

<div class="form-group">
    <label class="col-md-2 control-label" for="txtcodigo">Nº Certificado:</label>    
    <div class="col-md-7">
        <input type="text" class="form-control" size="100" name="num_certificado" id="num_certificado" maxlength="100" <? if (isset($fetch33['num_certificado'])) echo "value='$fetch33[num_certificado]'"?>/>
    </div>
</div>
    
<div class="form-group">
    <label class="col-md-2 control-label" for="txtcodigo">Nombre Médico:</label>    
    <div class="col-md-7">
        <input type="text" class="form-control" size="100" name="nombre_medico" id="nombre_medico"  maxlength="100" <? if (isset($fetch33['nombre_medico'])) echo "value='$fetch33[nombre_medico]'"?>/>
    </div>
</div>     
</div>-->

<div id="observacion"> 
<div class="form-group">

        <label class="col-md-2 control-label" for="txtcodigo">Observaciones: </label>                               
       <div class="col-md-7">
            <textarea name="descripcion" id="descripcion" class="form-control" rows="3" style="resize:vertical;"><?php if (isset($fetch33['descripcion'])) echo "$fetch33[descripcion]"?></textarea>
       </div>
   </div>    
</div>
</fieldset>

<?
