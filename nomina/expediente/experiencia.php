	
    <div class="form-group">                             
        <label class="col-md-2 control-label" for="txtcodigo" >Tipo Registro: </label>                               
        <div class="col-md-7">
            <select name="tipo_tiporegistro" id="tipo_tiporegistro" class="select2 form-control">
                <?
                while($fila=fetch_array($resultado)){?>
                    <option value="<?=$fila['id_expediente_subtipo']?>"><?=$fila['nombre_subtipo']?></option>                                            
                <?}?>                                       
            </select>                                                                       
        </div> 
    </div>
    
    <div class="form-group">                            
                                       
        <label for="txtcodigo" class="col-md-2 control-label">Instituci&oacute;n: </label>                                    
        <div class="col-md-3">
            <input class="form-control" type="text" name="institucion" id="institucion" <? if (isset($fetch33['institucion'])) echo "value='$fetch33[institucion]'"?> /> 
        </div>

        <label for="txtcodigo" class="col-md-1 control-label">Pública: </label>                                    
        <div class="col-md-2">
            <input type="checkbox" name="institucion_publica" id="institucion_publica" value="1" <? if (isset($fetch33['institucion_publica'])) echo "checked='true'"?>/>
        </div>
    </div>

     <div class="form-group">                             

        <label for="txtcodigo" class="col-md-2 control-label">Labor Realizada: </label>                                    
        <div class="col-md-3">
            <input  class="form-control" type="text" name="labor" id="labor" <? if (isset($fetch33['labor'])) echo "value='$fetch33[labor]'"?> size="50"/>
        </div>
        <label for="txtcodigo" class="col-md-1 control-label">Cargo: </label>                                    
        <div class="col-md-3">
            <input class="form-control"  type="text" name="cargo" id="cargo" <? if (isset($fetch33['cargo'])) echo "value='$fetch33[cargo]'"?> size="50"/>
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
        <label class="col-md-1 control-label" for="txtcodigo"> Fecha Culminaci&oacute;n:</label>                                                                  
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

        <label class="col-md-2 control-label" for="txtcodigo">Observaciones: </label>                               
       <div class="col-md-7">
            <textarea name="descripcion" id="descripcion" class="form-control" rows="3" style="resize:vertical;"><?php if (isset($fetch33['descripcion'])) echo "$fetch33[descripcion]"?></textarea>
       </div>
   </div>                                              			
<? 
