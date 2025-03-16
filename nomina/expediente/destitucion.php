    <div class="form-group">                             
        <label class="col-md-2 control-label" for="txtcodigo">Número Resolución: </label>                                
        <div class="col-md-3">
            <input type="text" name="numero_resolucion" class="form-control" id="numero_resolucion" <? if (isset($fetch33['numero_resolucion'])) echo "value='$fetch33[numero_resolucion]'"?> size="70"/>
        </div>     
        <label class="col-md-1 control-label" for="txtcodigo">Fecha Resolución:</label>                                
        <div class="col-md-3">
            <div class="input-group date date-picker" data-provide="datepicker"  data-date-format="dd/mm/yyyy" data-language="es" >
                 <input type="text" name="fecha_resolucion" class="form-control" id="fecha_resolucion" placeholder="(dd/mm/aaaa)" size="10" value="<?if(isset($fetch33['fecha_resolucion'])) echo fecha($fetch33['fecha_resolucion']);?>" maxlength="10"/>
                <span class="input-group-btn">
                    <button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
                </span>
            </div>                                    
        </div>
    </div>
    <div class="form-group"> 
        <label class="col-md-2 control-label" for="txtcodigo">Fecha Notificación:</label>                                
        <div class="col-md-3">
            <div class="input-group date date-picker" data-provide="datepicker"  data-date-format="dd/mm/yyyy" >
                 <input type="text" name="fecha_notificacion" class="form-control" id="fecha_notificacion" placeholder="(dd/mm/aaaa)" size="10" value="<?if(isset($fetch33['fecha_notificacion'])) echo fecha($fetch33['fecha_notificacion']);?>" maxlength="10"/>
                <span class="input-group-btn">
                    <button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
                </span>
            </div>                                    
        </div>
        <label class="col-md-1 control-label" for="txtcodigo">Número Edicto: </label>                                
        <div class="col-md-3">
            <input type="text" name="numero_edicto" class="form-control" id="numero_edicto" <? if (isset($fetch33['numero_edicto'])) echo "value='$fetch33[numero_edicto]'"?> size="70"/>
        </div>                                    
    </div>
    <div class="form-group"> 
        <label class="col-md-2 control-label" for="txtcodigo">Fecha Edicto:</label>                                
        <div class="col-md-3">
            <div class="input-group date date-picker" data-provide="datepicker"  data-date-format="dd/mm/yyyy" >
                <input type="text" name="fecha_edicto" class="form-control" id="fecha_edicto" placeholder="(dd/mm/aaaa)" size="10" value="<?if(isset($fetch33['fecha_edicto'])) echo fecha($fetch33['fecha_edicto']);?>" maxlength="10"/>
                <span class="input-group-btn">
                    <button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
                </span>
            </div>                                    
        </div>                                
    </div>          
    <div class="form-group">

        <label class="col-md-2 control-label" for="txtcodigo">Observaciones: </label>                               
       <div class="col-md-7">
            <textarea name="descripcion" id="descripcion" class="form-control" rows="3" style="resize:vertical;"><?php if (isset($fetch33['descripcion'])) echo "value='$fetch33[descripcion]'"?></textarea>
       </div>
   </div>
    

<?

