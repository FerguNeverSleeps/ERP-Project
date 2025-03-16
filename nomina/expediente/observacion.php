   <div class="form-group">                             
        <label class="col-md-2 control-label" for="txtcodigo">Número: </label>                                
        <div class="col-md-3">
            <input type="text" name="numero_resolucion" class="form-control" id="numero_resolucion" <? if (isset($fetch33['numero_resolucion'])) echo "value='$fetch33[numero_resolucion]'"?> size="70"/>
        </div>     
        <label class="col-md-1 control-label" for="txtcodigo">Fecha:</label>                                
        <div class="col-md-3">
            <div class="input-group date date-picker" data-provide="datepicker" data-date-format="dd/mm/yyyy">
                <input type="text" name="fecha_resolucion" class="form-control" id="fecha_resolucion" placeholder="(dd/mm/aaaa)" size="10" value="<?if(isset($fetch33['fecha_resolucion'])) echo fecha($fetch33['fecha_resolucion']);?>" maxlength="10"/>
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