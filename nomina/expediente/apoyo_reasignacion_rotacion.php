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


</fieldset>

<fieldset>
    <legend >Movimiento</legend>

<div class="form-group">                                
    <label  class="col-md-2 control-label" for="txtcodigo">Fecha Inicio:</label>                                
    <div class="col-md-3">
       <div class="input-group date date-picker" data-provide="datepicker" data-date-format="dd/mm/yyyy" >
           <input size="10" type="text" name="fecha_inicio" class="form-control" placeholder="(dd/mm/aaaa)" id="fecha_inicio" value="<?if($fetch33['fecha_inicio']!="0000-00-00") { echo fecha($fetch33['fecha_inicio']); }  ?>">
           <span class="input-group-btn">
               <button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
           </span>
       </div>
    </div>
     <label  class="col-md-1 control-label" for="txtcodigo">Fecha Fin:</label>                                
    <div class="col-md-3">
       <div class="input-group date date-picker" data-provide="datepicker" data-date-format="dd/mm/yyyy" >
           <input size="10" type="text" name="fecha_fin" class="form-control" placeholder="(dd/mm/aaaa)" id="fecha_fin" value="<?if($fetch33['fecha_fin']!="0000-00-00") { echo fecha($fetch33['fecha_fin']); }  ?>">
           <span class="input-group-btn">
               <button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
           </span>
       </div>
    </div>
</div>
    
<div class="form-group">                                
    <label  class="col-md-2 control-label" for="txtcodigo">Fecha Memo:</label>                                
    <div class="col-md-3">
       <div class="input-group date date-picker" data-provide="datepicker" data-date-format="dd/mm/yyyy" >
           <input size="10" type="text" name="fecha_memo" class="form-control" placeholder="(dd/mm/aaaa)" id="fecha_memo" value="<?if($fetch33['fecha_memo']!="0000-00-00") { echo fecha($fetch33['fecha_memo']); }  ?>">
           <span class="input-group-btn">
               <button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
           </span>
       </div>
    </div>

    <label class="col-md-1 control-label" for="txtcodigo">Número Memo:</label>                               
    <div class="col-md-3">
        <input size="10" type="text" name="num_memo" class="form-control" id="num_memo" value="<?if(isset($fetch33['num_memo'])) echo$fetch33['num_memo'];?>">
    </div>
</div>

<div class="form-group">
        <?php
                //$tipemp1 = 'checked';
                $dejado1 = '';
                $dejado2 = '';
                

                if($codigo!='')
                {
                        $dejado1 = ($fetch33['dejado']=='0')  ? 'checked' : '';
                        $dejado2 = ($fetch33['dejado']=='1')  ? 'checked' : '';

                        
                } 
        ?>
        <label class="control-label col-md-2" for="txtcodigo">Dejado</label>
        <div class="col-md-7">
                <div class="radio-list">
                        <label class="radio-inline">
                            <input type="radio" name="dejado" id="dejado1" value="0" <?php echo $dejado1; ?>> No</label>
                        <label class="radio-inline">
                            <input type="radio" name="dejado" id="dejado2" value="1" <?php echo $dejado2; ?>> Si</label>
                </div>
        </div>
</div>

<div class="form-group">
    <label class="col-md-2 control-label" for="txtcodigo">Planilla Anterior: </label>
    <div class="col-md-7">                               
            
            <select name="planilla_ant" class="form-control" id="planilla_ant"disabled>
                <option value="">Seleccione</option>
                <?php                     
                     $consulta_planilla="SELECT codtip, descrip FROM nomtipos_nomina";
                     $resultado_planilla=sql_ejecutar($consulta_planilla);
                    while($fila_planilla=fetch_array($resultado_planilla))
                    {
                        //echo "AQUI";
                        if ($codigo=='')
                        {
                            if(!$fetch2['tipnom'])
                            {?>
                                <option  value="<?=$fila_planilla['codtip'];?>"><?=utf8_encode($fila_planilla['descrip']);?></option>
                            <?}
                            else
                            { 
                               if($fetch2['tipnom']==$fila_planilla['codtip'])
                               {?>
                                    <option  value="<?=$fila_planilla['codtip'];?>" selected><?=utf8_encode($fila_planilla['descrip']);?></option> 
                               <?}
                               else
                               {?>
                                    <option  value="<?=$fila_planilla['codtip'];?>"><?=utf8_encode($fila_planilla['descrip']);?></option>
                               <?}
                            }
                        }
                        else
                        {
                            if($fetch33['planilla_nueva']==$fila_planilla['codtip'])
                            {?>
                                 <option  value="<?=$fila_planilla['codtip'];?>" selected><?=utf8_encode($fila_planilla['descrip']);?></option> 
                            <?}
                            else
                            {?>
                                 <option  value="<?=$fila_planilla['codtip'];?>"><?=utf8_encode($fila_planilla['descrip']);?></option>
                            <?}         
                        }

                    }
                    ?>
            </select>
    </div>       
    
</div>  
    
<div class="form-group">
    <label class="col-md-2 control-label" for="txtcodigo">Planilla Nueva: </label>
    <div class="col-md-7">                               
            
            <select name="planilla_nueva" class="form-control" id="planilla_nueva">
                <option value="">Seleccione</option>
                <?php                     
                     $consulta_planilla="SELECT codtip, descrip FROM nomtipos_nomina";
                     $resultado_planilla=sql_ejecutar($consulta_planilla);
                    while($fila_planilla=fetch_array($resultado_planilla))
                    {
                        //echo "AQUI";
                        if ($codigo=='')
                        {
                            if(!$fetch2['tipnom'])
                            {?>
                                <option  value="<?=$fila_planilla['codtip'];?>"><?=utf8_encode($fila_planilla['descrip']);?></option>
                            <?}
                            else
                            { 
                               if($fetch2['tipnom']==$fila_planilla['codtip'])
                               {?>
                                    <option  value="<?=$fila_planilla['codtip'];?>" selected><?=utf8_encode($fila_planilla['descrip']);?></option> 
                               <?}
                               else
                               {?>
                                    <option  value="<?=$fila_planilla['codtip'];?>"><?=utf8_encode($fila_planilla['descrip']);?></option>
                               <?}
                            }
                        }
                        else
                        {
                            if($fetch33['planilla_nueva']==$fila_planilla['codtip'])
                            {?>
                                 <option  value="<?=$fila_planilla['codtip'];?>" selected><?=utf8_encode($fila_planilla['descrip']);?></option> 
                            <?}
                            else
                            {?>
                                 <option  value="<?=$fila_planilla['codtip'];?>"><?=utf8_encode($fila_planilla['descrip']);?></option>
                            <?}         
                        }

                    }
                    ?>
            </select>
    </div>       
    
</div> 

<div class="form-group">
    <label class="col-md-2 control-label" for="txtcodigo">Departamento Anterior: </label>
    <div class="col-md-7">                               
            
        <select name="departamento_ant" class="form-control" id="departamento_ant" disabled>
                <option value="">Seleccione</option>
                <?php                        
                   $consulta_departamento="SELECT IdDepartamento, Descripcion FROM departamento";
                    $resultado_departamento=sql_ejecutar($consulta_departamento);
                    while($fila=fetch_array($resultado_departamento))
                    {
                        
                            if($fetch2['IdDepartamento']==$fila['IdDepartamento'])
                            {?>
                                 <option  value="<?=$fila['IdDepartamento'];?>" selected><?=utf8_encode($fila['Descripcion']);?></option> 
                            <?}
                            else
                            {?>
                                 <option  value="<?=$fila['IdDepartamento'];?>"><?=utf8_encode($fila['Descripcion']);?></option>
                            <?}         
                        

                    }

                ?>
            </select>
    </div>       
    
</div>
    
<div class="form-group">
    <label class="col-md-2 control-label" for="txtcodigo">Departamento Nuevo: </label>
    <div class="col-md-7">                               
            
            <select name="departamento_nuevo" class="form-control" id="departamento_nuevo">
                <option value="">Seleccione</option>
                <?php                        
                   $consulta_departamento="SELECT IdDepartamento, Descripcion FROM departamento";
                    $resultado_departamento=sql_ejecutar($consulta_departamento);
                    while($fila=fetch_array($resultado_departamento))
                    {
                        //echo "AQUI";
                        if ($codigo=='')
                        {                        
                            if($fetch2['IdDepartamento']==$fila['IdDepartamento'])
                            {?>
                                 <option  value="<?=$fila['IdDepartamento'];?>" selected><?=utf8_encode($fila['Descripcion']);?></option> 
                            <?}
                            else
                            {?>
                                 <option  value="<?=$fila['IdDepartamento'];?>"><?=utf8_encode($fila['Descripcion']);?></option>
                            <?}         

                        }
                        else
                        {
                            if($fetch33['departamento_nuevo']==$fila['IdDepartamento'])
                            {?>
                                 <option  value="<?=$fila['IdDepartamento'];?>" selected><?=utf8_encode($fila['Descripcion']);?></option> 
                            <?}
                            else
                            {?>
                                 <option  value="<?=$fila['IdDepartamento'];?>"><?=utf8_encode($fila['Descripcion']);?></option>
                            <?}         
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
