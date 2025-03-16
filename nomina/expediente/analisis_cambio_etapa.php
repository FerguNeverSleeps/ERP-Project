<?php
    $conexion = conexion();
    if(isset($_GET['codigo']) && $_GET['codigo']!='')
    {
            

            $sql1 = "SELECT  *
                    FROM   expediente_analisis 
                    WHERE  cod_expediente_det='{$_GET['codigo']}'";
            
            $resultado1=sql_ejecutar($sql1);
            $fetch1=fetch_array($resultado1); 
            
            $codigo = $_GET['codigo'];            
            

    }
    
    if(isset($_GET['cedula']) && $_GET['cedula']!='')
    {
           
            $sql2 = "SELECT  codcargo, nomfuncion_id
                    FROM   nompersonal
                    WHERE  cedula='{$_GET['cedula']}'";                    
           
            
            $resultado2=sql_ejecutar($sql2);
            $fetch2=fetch_array($resultado2);           
             
            
            $sql3 = "SELECT  * FROM   nomcargos";
            
            $sql4 = "SELECT  * FROM   nomfuncion";
            
            

    }
?>

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
    <label class="col-md-2 control-label" for="txtcodigo">Cargo: </label>                                
    <div class="col-md-3">
        <input type="text" name="cargo" class="form-control" id="cargo" readonly="true" <? if (isset($cargo)) echo "value='$cargo'"?> size="70"/>
    </div>
    <label for="txtcodigo" class="col-md-2 control-label">Seguro Social: </label>                                    
    <div class="col-md-2">
        <input class="form-control" type="text" name="seguro_social" id="seguro_social" readonly="true"  <? if (isset($seguro_social)) echo "value='$seguro_social'"?> /> 
    </div>

</div>
<div class="form-group">                                
    <label class="col-md-2 control-label" for="txtcodigo">Posición: </label>                                
    <div class="col-md-3">
        <input type="text" name="posicion" class="form-control" id="posicion" readonly="true" <? if (isset($posicion)) echo "value='$posicion'"?> size="70"/>
    </div>
    <label for="txtcodigo" class="col-md-2 control-label">Planilla: </label>                                    
    <div class="col-md-2">
        <input class="form-control" type="text" name="planilla" id="planilla" readonly="true"  <? if (isset($pplanilla)) echo "value='$pplanilla'"?> /> 
    </div>

</div>

<div class="form-group">                                
    <label class="col-md-2 control-label" for="txtcodigo">Partida: </label>                                
    <div class="col-md-4">
        <input type="text" name="posicion" class="form-control" id="posicion" readonly="true" <? if (isset($partida)) echo "value='$partida'"?> size="70"/>
    </div>

</div>    

<div class="form-group">
    <label class="col-md-2 control-label" for="txtcodigo">Cargo Según Estructura: </label>
    <div class="col-md-7">                               
            
            <select name="cargo_estructura" class="select2 form-control" id="cargo_estructura" disabled>
                <option value="">Seleccione</option>
                <?php 
                    $resultado3=sql_ejecutar($sql3);  
                    while($fila=fetch_array($resultado3))
                    {

                        if(!$fetch2['codcargo'])
                        {?>
                            <option  value="<?=$fila['cod_car'];?>"><?=utf8_encode($fila['des_car']);?></option>
                        <?}
                        else
                        { 
                           if($fetch2['codcargo']==$fila['cod_car'])
                           {?>
                                <option  value="<?=$fila['cod_car'];?>" selected><?=utf8_encode($fila['des_car']);?></option> 
                           <?}
                           else
                           {?>
                                <option  value="<?=$fila['cod_car'];?>"><?=utf8_encode($fila['des_car']);?></option>
                           <?}
                        }                  

                    }
                    ?>
            </select>
            
    </div>
</div>

<div class="form-group">
    <label class="col-md-2 control-label" for="txtcodigo">Cargo Según Funciones: </label>
    <div class="col-md-7">                               
            
            <select name="cargo_funcion" class="select2 form-control" id="cargo_funcion">
                <option value="">Seleccione</option>
                <?php                     
            
                    $resultado4=sql_ejecutar($sql4);
                    
                   
                    while($fila_funcion=fetch_array($resultado4))
                    {
                        //echo "AQUI";
                        if ($codigo=='')
                        {
                            if(!$fetch2['nomfuncion_id'])
                            {?>
                                <option  value="<?=$fila_funcion['nomfuncion_id'];?>"><?=utf8_encode($fila_funcion['descripcion_funcion']);?></option>
                            <?}
                            else
                            { 
                               if($fetch2['nomfuncion_id']==$fila_funcion['nomfuncion_id'])
                               {?>
                                    <option  value="<?=$fila_funcion['nomfuncion_id'];?>" selected><?=utf8_encode($fila_funcion['descripcion_funcion']);?></option> 
                               <?}
                               else
                               {?>
                                    <option  value="<?=$fila_funcion['nomfuncion_id'];?>"><?=utf8_encode($fila_funcion['descripcion_funcion']);?></option>
                               <?}
                            }
                        }
                        else
                        {
                            if($fetch33['cargo_funcion']==$fila_funcion['nomfuncion_id'])
                            {?>
                                 <option  value="<?=$fila_funcion['nomfuncion_id'];?>" selected><?=utf8_encode($fila_funcion['descripcion_funcion']);?></option> 
                            <?}
                            else
                            {?>
                                 <option  value="<?=$fila_funcion['nomfuncion_id'];?>"><?=utf8_encode($fila_funcion['descripcion_funcion']);?></option>
                            <?}         
                        }

                    }
                    ?>
            </select>
    </div>       
    
</div>
<div class="form-group">                                
    <label class="col-md-2 control-label" for="txtcodigo">Salario Base: </label>                                
    <div class="col-md-3">
        <input type="text" name="salario_base" class="form-control" id="salario_base"  onblur="calcular_porcentaje();monto_analisis_cambio_etapa();" <? if (isset($fetch33['salario_base'])) echo "value='$fetch33[salario_base]'"?> size="70"/>
    </div>
    <label for="txtcodigo" class="col-md-2 control-label">Ajuste: </label>                                    
    <div class="col-md-2">
        <input class="form-control" type="text" name="ajuste" id="ajuste" onblur="monto_analisis_cambio_etapa();"  <? if (isset($fetch33['ajuste'])) echo "value='$fetch33[ajuste]'"?> /> 
    </div>

</div>

<div class="form-group">                                
    <label class="col-md-2 control-label" for="txtcodigo">Ajuste Discrecional: </label>                                
    <div class="col-md-3">
        <input type="text" name="ajuste_discrecional" class="form-control" id="ajuste_discrecional" onblur="monto_analisis_cambio_etapa();" <? if (isset($fetch33['ajuste_discrecional'])) echo "value='$fetch33[ajuste_discrecional]'"?> size="70"/>
    </div>
    <label for="txtcodigo" class="col-md-2 control-label">Ajuste Salario Minimo: </label>                                    
    <div class="col-md-2">
        <input class="form-control" type="text" name="ajuste_salario_minimo" id="ajuste_salario_minimo" onblur="monto_analisis_cambio_etapa();" <? if (isset($fetch33['ajuste_salario_minimo'])) echo "value='$fetch33[ajuste_salario_minimo]'"?> /> 
    </div>

</div>

<div class="form-group">                                
    <label class="col-md-2 control-label" for="txtcodigo">Porcentaje (10 -12 - 14%): </label>                                
    <div class="col-md-3">
        <input type="text" name="porcentaje" class="form-control" id="porcentaje" onblur="calcular_salario_porcentaje();monto_analisis_cambio_etapa();" <? if (isset($fetch33['porcentaje'])) echo "value='$fetch33[porcentaje]'"?> size="70"/>
    </div>
    <label for="txtcodigo" class="col-md-2 control-label">Salario Base / Porcentaje: </label>                                    
    <div class="col-md-2">
        <input class="form-control" type="text" readonly="true" name="salario_base_porcentaje" id="salario_base_porcentaje" <? if (isset($fetch33['salario_base_porcentaje'])) echo "value='$fetch33[salario_base_porcentaje]'"?> /> 
    </div>

</div>

<div class="form-group"> 
    <label class="col-md-2 control-label" for="txtcodigo">Otros: </label>                                
    <div class="col-md-3">
        <input type="text" name="ajuste_otros" class="form-control" id="ajuste_otros" onblur="monto_analisis_cambio_etapa();" <? if (isset($fetch33['ajuste_otros'])) echo "value='$fetch33[ajuste_otros]'"?> size="70"/>
    </div>
    <label class="col-md-2 control-label" for="txtcodigo">Acuerdo: </label>                                
    <div class="col-md-2">
        <input type="text" name="acuerdo" class="form-control" id="acuerdo" onblur="monto_analisis_cambio_etapa()" <? if (isset($fetch33['acuerdo'])) echo "value='$fetch33[acuerdo]'"?> size="70"/>
    </div>
    
</div>

<div class="form-group">                                
    <label class="col-md-2 control-label" for="txtcodigo"><strong>Análisis </strong> </label>      
</div>
 <div class="form-group" style="position: relative; left: 8%;" >
    <table class="table" style="width: 80%">
        <thead>
          <tr>
            <th>Fecha</th>
            <th>Grado&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
            <th>Etapa&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>            
            <th>Monto&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
            <th>Resuelto y/o Decreto&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>                           
          </tr>
        </thead>
        <tbody>
          <tr>                                       
            <td>
                <div class="input-group date date-picker" data-provide="datepicker" data-date-format="dd/mm/yyyy" >
                    <input type="text" name="fecha_inicio_lab" class="form-control" id="fecha_inicio_lab" value="<?if(isset($fetch1['fecha'])) echo fecha($fetch1['fecha']);?>" size="30"/>
                    <span class="input-group-btn">
                            <button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
                    </span>
                </div>
            </td>
            <td>
                <input type="text" name="grado_analisis" class="form-control" id="grado_analisis" <? if (isset($fetch1['grado'])) echo "value='$fetch1[grado]'"?> size="30"/>
            </td>
            <td>
                <input type="text" name="etapa_analisis" class="form-control" id="etapa_analisis" onblur="monto_analisis_cambio_etapa()" <? if (isset($fetch1['etapa'])) echo "value='$fetch1[etapa]'"?> size="30"/>
            </td>
            <td>
                <input type="text" name="salario_analisis" readonly="true" class="form-control" id="salario_analisis" <? if (isset($fetch1['salario'])) echo "value='$fetch1[salario]'"?> size="30"/>
            </td>
            <td>
                 <input type="text" name="resuelto_analisis_1" class="form-control" id="resuelto_analisis_1" <? if (isset($fetch1['resuelto_1'])) echo "value='$fetch1[resuelto_1]'"?> size="30"/>
            </td>

          </tr>                                      
        </tbody>
      </table>

</div>
<div class="form-group">

    <label class="col-md-2 control-label" for="txtcodigo">Observaciones: </label>                               
   <div class="col-md-9">
        <textarea name="descripcion" id="descripcion" class="form-control" rows="3" style="resize:vertical;"><?php if (isset($fetch33['descripcion'])) echo "$fetch33[descripcion]"?></textarea>
   </div>
</div>
<input type="hidden" name="cargo_estructura2" id="cargo_estructura2"  value="<?echo $fetch2['codcargo'];?>">
<?

