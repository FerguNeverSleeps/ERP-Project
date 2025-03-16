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
    <legend >Cargo</legend>

<div class="form-group">
    

    <label class="col-md-2 control-label" for="txtcodigo">Fecha Enterado:</label>
        <div class="col-md-3">
            <div class="input-group date date-picker" data-provide="datepicker" data-date-format="dd/mm/yyyy">
                <input size="10" type="text" class="form-control" placeholder="(dd/mm/aaaa)" name="fecha" id="fecha"  value="<?if(isset($fetch33['fecha'])) echo fecha($fetch33['fecha']);?>">
                <span class="input-group-btn">
                    <button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
                </span> 
            </div>
        </div>
    </div>
    


<div class="form-group">
    <label class="col-md-2 control-label" for="txtcodigo">Cargo Actual: </label>
    <div class="col-md-7">                               
            
            <select name="cargo_anterior" class="form-control" id="cargo_anterior" readonly>
                <option value="">Seleccione</option>
                <?php                     
                    $consulta_cargo="SELECT cod_car, des_car FROM nomcargos";
                    $resultado_cargo_anterior=sql_ejecutar($consulta_cargo);
                    while($fila_cargo_anterior=fetch_array($resultado_cargo_anterior))
                    {
                        //echo "AQUI";
                        if ($codigo=='')
                        {
                            if(!$fetch2['codcargo'])
                            {?>
                                <option  value="<?=$fila_cargo_anterior['cod_car'];?>"><?=utf8_encode($fila_cargo_anterior['des_car']);?></option>
                            <?}
                            else
                            { 
                               if($fetch2['codcargo']==$fila_cargo_anterior['cod_car'])
                               {?>
                                    <option  value="<?=$fila_cargo_anterior['cod_car'];?>" selected><?=utf8_encode($fila_cargo_anterior['des_car']);?></option> 
                               <?}
                               else
                               {?>
                                    <option  value="<?=$fila_cargo_anterior['cod_car'];?>"><?=utf8_encode($fila_cargo_anterior['des_car']);?></option>
                               <?}
                            }
                        }
                        else
                        {
                            if($fetch33['cod_cargo_anterior']==$fila_cargo_anterior['cod_car'])
                            {?>
                                 <option  value="<?=$fila_cargo_anterior['cod_car'];?>" selected><?=utf8_encode($fila_cargo_anterior['des_car']);?></option> 
                            <?}
                            else
                            {?>
                                 <option  value="<?=$fila_cargo_anterior['cod_car'];?>"><?=utf8_encode($fila_cargo_anterior['des_car']);?></option>
                            <?}         
                        }

                    }
                    ?>
            </select>
    </div>       
    
</div>    

<div class="form-group">
    <label class="col-md-2 control-label" for="txtcodigo">Cargo Nuevo: </label>
    <div class="col-md-7">                               
            
            <select name="cargo_nuevo" class="form-control" id="cargo_nuevo" onchange="buscar_salario_cargo();">
                <option value="">Seleccione</option>
                <?php                     
                    $consulta_cargo="SELECT cod_car, des_car FROM nomcargos";
                    $resultado_cargo=sql_ejecutar($consulta_cargo);
                    while($fila_cargo=fetch_array($resultado_cargo))
                    {
                        //echo "AQUI";
                        if ($codigo=='')
                        {
                            if(!$fetch2['codcargo'])
                            {?>
                                <option  value="<?=$fila_cargo['cod_car'];?>"><?=utf8_encode($fila_cargo['des_car']);?></option>
                            <?}
                            else
                            { 
                               if($fetch2['codcargo']==$fila_cargo['cod_car'])
                               {?>
                                    <option  value="<?=$fila_cargo['cod_car'];?>" selected><?=utf8_encode($fila_cargo['des_car']);?></option> 
                               <?}
                               else
                               {?>
                                    <option  value="<?=$fila_cargo['cod_car'];?>"><?=utf8_encode($fila_cargo['des_car']);?></option>
                               <?}
                            }
                        }
                        else
                        {
                            if($fetch33['cod_cargo_nuevo']==$fila_cargo['cod_car'])
                            {?>
                                 <option  value="<?=$fila_cargo['cod_car'];?>" selected><?=utf8_encode($fila_cargo['des_car']);?></option> 
                            <?}
                            else
                            {?>
                                 <option  value="<?=$fila_cargo['cod_car'];?>"><?=utf8_encode($fila_cargo['des_car']);?></option>
                            <?}         
                        }

                    }
                    ?>
            </select>
    </div>       
    
</div>

<div class="form-group">                                
    <label class="col-md-2 control-label" for="txtcodigo">Salario Actual: </label>                                
    <div class="col-md-7">
        <input type="text" name="salario_anterior" class="form-control" id="salario_anterior" readonly <? echo "value='$salario'"?> size="70"/>
    </div>
</div>

<div class="form-group">                                
    <label class="col-md-2 control-label" for="txtcodigo">Salario Nuevo: </label>                                
    <div class="col-md-7">
        <input type="text" name="salario_nuevo" class="form-control" id="salario_nuevo" <? if ($fetch33['monto_nuevo']=='' || $fetch33['monto_nuevo']==0){ echo "value='$salario'";} else {echo "value='".$fetch33[monto_nuevo]."'";}?> size="70"/>
    </div>
</div>



<div class="form-group">

     <label class="col-md-2 control-label" for="txtcodigo">Observaciones: </label>                               
    <div class="col-md-7">
         <textarea name="descripcion" id="descripcion" class="form-control" rows="3" style="resize:vertical;"><?php if (isset($fetch33['descripcion'])) echo "$fetch33[descripcion]"?></textarea>
    </div>
</div>
</fieldset>
 
<input type="hidden" name="proyecto_anterior" id="proyecto_anterior" value="<?php echo $horass?>">			
<?

