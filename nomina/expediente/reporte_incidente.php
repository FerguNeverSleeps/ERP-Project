<div id="registro">

    <style>
        .row{
            margin-top: 15px;
        }
        .margin-top-30{
            margin-top: 30px; 
        }
    </style>
    <?php
        $conexion = conexion();
        
        // echo $sql2 = "SELECT * FROM expediente
        // WHERE  cedula='{$_GET['cedula']}'";
        // $res2=query($sql2,$conexion);
        // $data_expediente = $res2->fetch_object();
        // echo $data_expediente->numero_resolucion;exit;
        
        $sql = "SELECT a.*,b.codorg,b.descrip 
        FROM   nompersonal a 
        LEFT JOIN nomnivel1 b ON a.codnivel1=b.codorg
        WHERE  cedula='{$_GET['cedula']}'";
        $res1=query($sql,$conexion);
        $empleado = $res1->fetch_object();

        
        $sql2 ="SELECT consecutivo_reporte_incidencia FROM nomempresa";
        $res2=query($sql2,$conexion);
        $consecutivo = $res2->fetch_object();
        $c=$consecutivo->consecutivo_reporte_incidencia+1;
        $editar = 0;
        if(isset($_GET['codigo']) && $_GET['codigo']!='')
        {            
        $sql = "SELECT * 
                            FROM   expediente
                            WHERE  cod_expediente_det='{$_GET['codigo']}'";

            $res=query($sql,$conexion);

            $expediente = $res->fetch_object();

            $fecha = DateTime::createFromFormat('Y-m-d', $expediente->fecha);
            $fecha = ($fecha !== false) ? $fecha->format('d/m/Y') : '';	
            $editar = 1;
            $aprobado = (isset($_GET['aprobado']) AND $_GET['aprobado'] == 1) ? 1 : 0;
        }
    ?>
    <div class="form-horizontal margin-top-30">    
        <fieldset>
            <div class="row">
                <div class="form-group">
                    <label class="col-md-2 control-label" for="txtcodigo">Subtipo:</label>
                    <div class="col-md-7">                               
                        <div  id="tipo_tipo">
                        <input type="hidden" id="editar_reporte_incidente" value = <?= $editar ?> ?>
                        <input type="hidden" id="aprobado" value = <?= $aprobado ?> ?>
                            <SELECT name="tipo_tiporegistro" class="form-control" id="tipo_tiporegistro" <?php if(!$editar){ ?> onclick="cargar_form_prestamo();" <?php } ?>>
                            
							<option selected disabled>Seleccione el tipo de incidente</option>
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
            </div>

            <div class="row">
                <label for="apenom" class="col-sm-2 control-label">EMPLEADO:</label>
                <div class="col-sm-7">
                    <input type="text" name="apenom" class="form-control" id="apenom" readonly="true" 
                    <? if (isset($empleado->apenom)) { echo "value='$empleado->apenom'"; }?> size="70"/>
                    
                </div>
            </div>

            <div class="row">
                <label for="posicion_anterior" class="col-sm-2 control-label">POSICIÓN:</label>
                <div class="col-sm-7">
                    <input type="text" name="posicion_anterior" class="form-control" id="posicion_anterior" readonly="true" 
                    <? if (isset($empleado->ficha)) { echo "value='$empleado->ficha'"; }else{ echo "value='".fecha($fetch33['posicion_anterior'])."'"; }?> size="70"/>
                    
                </div>
            </div>

            <div class="row">
                <label for="cargo_estructura" class="col-sm-2 control-label">SUCURSAL:</label>
                <div class="col-sm-7">
                    <input type="text" name="cargo_estructura" class="form-control" id="cargo_estructura" readonly="true" 
                    <? if (isset($empleado->descrip)) { echo "value='$empleado->descrip'"; }else{ echo "value='".fecha($fetch33['cargo_estructura'])."'"; }?> size="70"/>
                    
                </div>
            </div>

            <div class="row">
                <div class="form-group">                            
                        
                    <label class="col-md-2 control-label" for="txtcodigo">Fecha:</label>                                
                    <div class="col-md-3">
                        <div class="input-group date date-picker" data-provide="datepicker"  data-date-format="dd/mm/yyyy" >
                            <input type="text" name="fecha_resolucion" class="form-control" id="fecha_resolucion" placeholder="(dd/mm/aaaa)" size="10" value="<?if(isset($fetch33['fecha_resolucion'])) echo fecha($fetch33['fecha']);?>" maxlength="10"/>
                            <span class="input-group-btn">
                                <button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
                            </span>
                        </div>

                    </div>
                </div>
            </div>

            <div class="row">
                <div class="form-group">                            
                        
                    <label class="col-md-2 control-label" for="txtcodigo">Fecha del Incidente:</label>                                
                    <div class="col-md-3">
                        <div class="input-group date date-picker" data-provide="datepicker"  data-date-format="dd/mm/yyyy" >
                            <input type="text" name="fecha_inicio" class="form-control" id="fecha_inicio" placeholder="(dd/mm/aaaa)" size="10" value="<?if(isset($fetch33['fecha_inicio'])) echo fecha($fetch33['fecha_inicio']);?>" maxlength="10"/>
                            <span class="input-group-btn">
                                <button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
                            </span>
                        </div>
            
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="form-group">                            
                        
                    <label class="col-md-2 control-label" for="txtcodigo">Hora aproximada:</label>                                
                    <div class="col-md-3">
                        <div class="input-group">
                            <input placeholder="Selected time" value="22:00" type="time" id="fecha_hora_inicio" name="fecha_hora_inicio" class="form-control">
                            <span class="input-group-btn">
                                <button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
                            </span>
                        </div>
            
                    </div>
                </div> 
            </div>           
            
            <div class="row">
                <label for="descripcion" class="col-sm-2 control-label">Numero concecutivo:</label>
                <div class="col-sm-7">
                    <input type="text" name="num_incidente" class="form-control" id="descripcion" 
                    <? if (isset($_GET['codigo']) && $_GET['codigo']!='' )
                     { echo "value='$expediente->numero_resolucion'"; }else{
                        echo "value='$c'";
                     }?>
                    size="70"/>
                    
                </div>
            </div>     
             <div class="row">
                <label for="descripcion" class="col-sm-2 control-label">Incidente:</label>
                <div class="col-sm-7">
                    <input type="text" name="descripcion" class="form-control" id="descripcion" 
                    <? if (isset($_GET['codigo']) && $_GET['codigo']!='' )
                     { echo "value='$expediente->descripcion'"; }?>              
                    size="70"/>
                    
                </div>
            </div>        

            <!--<div class="row">
                <label for="monto" class="col-sm-2 control-label">Monto:</label>
                <div class="col-sm-7">
                    <input type="text" name="monto" class="form-control" id="monto" 
                    <? if (isset($fetch33['monto'])) { echo "value='".$fetch33['monto']."'"; }?> size="70"/>
                    
                </div>
            </div>            

            <div class="row">
                <label for="monto_nuevo" class="col-sm-2 control-label">Cuota:</label>
                <div class="col-sm-7">
                    <input type="text" name="monto_nuevo" class="form-control" id="monto_nuevo" 
                    <? if (isset($fetch33['monto_nuevo'])) { echo "value='".$fetch33['monto_nuevo']."'"; }else{ echo "value=''"; }?> size="70"/>
                    
                </div>
            </div>-->            

            <div class="row">
                <div class="form-group">

                    <label class="col-md-2 control-label" for="txtcodigo">Comentario del Jefe o Supervisor Inmediato: </label>                               
                    <div class="col-md-7">
                        <textarea name="comentarios_1" id="comentarios_1" class="form-control" rows="3" style="resize:vertical;"><?php if (isset($fetch33['comentarios_1'])) echo utf8_encode($fetch33['comentarios_1']); ?></textarea>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="form-group">

                    <label class="col-md-2 control-label" for="txtcodigo">Comentario del Colaborador: </label>                               
                    <div class="col-md-7">
                        <textarea name="comentarios_2" id="comentarios_2" class="form-control" rows="3" style="resize:vertical;"><?php if (isset($fetch33['comentarios_2'])) echo utf8_encode($fetch33['comentarios_2']); ?></textarea>
                    </div>
                </div> 
            </div>           

            <div class="row">
                <label for="analista" class="col-sm-2 control-label">Recursos Humanos aprueba:</label>
                <div class="col-sm-7">
                    <input type="text" name="analista" class="form-control" id="analista" 
                    <? if (isset($fetch33['analista'])) { echo "value='".$fetch33['analista']."'"; }else{ echo "value=''"; }?> size="70"/>
                    
                </div>
            </div>        

            <div class="row">
                <label for="concepto" class="col-sm-2 control-label">Tipo de Amonestación:</label>
                <div class="col-sm-7">
                    <input type="text" name="concepto" class="form-control" id="concepto" 
                    <? if (isset($fetch33['concepto'])) { echo "value='".$fetch33['concepto']."'"; }else{ echo "value=''"; }?> size="70"/>
                    
                </div>
            </div>
            <div class="row">
                <div class="form-group">
                    <label class="col-md-2 control-label" for="txtcodigo">Tipo de Amonestación:</label>
                    <div class="col-md-7">                               
                        <div  id="tipo_tipo">
                            <SELECT name="concepto" class="form-control" id="concepto">                            
							<option selected disabled>Seleccione el tipo de amonestación</option>
							<option value="Verbal">Verbal</option>
							<option value="Escrita">Escrita</option>
                            </SELECT>
                        </div>            
                    </div>
                </div>
            </div>
            <br>

        </fieldset>
        <fieldset>                         
            <div id="div_form_prestamo">
            </div>
        </fieldset>
    </div>
</div>
<?php
