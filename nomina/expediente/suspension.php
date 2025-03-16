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
    <legend >Suspensión</legend>
    
    <div class="form-group">                             
        <label class="col-md-2 control-label" for="txtcodigo">Número Resolución: </label>                                
        <div class="col-md-3">
            <input type="text" name="numero_resolucion" class="form-control" id="numero_resolucion" <? if (isset($fetch33['numero_resolucion'])) echo "value='$fetch33[numero_resolucion]'"?> size="70"/>
        </div>     
        <label class="col-md-1 control-label" for="txtcodigo">Fecha Resolución:</label>                                
        <div class="col-md-3">
            <div class="input-group date date-picker" data-provide="datepicker" data-date-format="dd/mm/yyyy" >
                <input type="text" name="fecha_resolucion" class="form-control" id="fecha_resolucion" placeholder="(dd/mm/aaaa)" size="10" value="<?if(isset($fetch33['fecha'])) echo fecha($fetch33['fecha']);?>" maxlength="10"/>
                <span class="input-group-btn">
                    <button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
                </span>
            </div>
        </div>
    </div>
    <div class="form-group">                                
        <label class="col-md-2 control-label" for="txtcodigo">Desde:</label>                                
        <div class="col-md-3">
            <div class="input-group date date-picker" data-provide="datepicker" data-date-format="dd/mm/yyyy" >
                <input type="text" name="fecha_desde"  onchange="dias_suspension();" class="form-control" id="fecha_desde" placeholder="(dd/mm/aaaa)" size="10" value="<?if(isset($fetch33['fecha_inicio'])) echo fecha($fetch33['fecha_inicio']);?>" maxlength="10"/>
                <span class="input-group-btn">
                    <button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
                </span>
            </div>
        </div> 
        <label class="col-md-1 control-label" for="txtcodigo">Hasta:</label>                                
        <div class="col-md-3">
            <div class="input-group date date-picker" data-provide="datepicker" data-date-format="dd/mm/yyyy" >
                <input type="text" name="fecha_hasta" onchange="dias_suspension();" class="form-control" id="fecha_hasta" placeholder="(dd/mm/aaaa)" size="10" value="<?if(isset($fetch33['fecha_fin'])) echo fecha($fetch33['fecha_fin']);?>" maxlength="10"/>
                <span class="input-group-btn">
                    <button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
                </span>
            </div>
        </div>
    </div>
    <div class="form-group">                                
         <label class="col-md-2 control-label" for="txtcodigo">Días: </label>                                
        <div class="col-md-3">
            <input type="text" name="dias" class="form-control" id="dias" <? if (isset($fetch33['dias'])) echo "value='$fetch33[dias]'"?> size="70"/>
        </div>
         
    </div>
    <div class="form-group">
        <label class="col-md-2 control-label" for="txtcodigo">Tipo Falta: </label>
        <div class="col-md-7">                               

            <select name="tipo_falta" class="form-control" id="tipo_falta" onchange="buscar_datos_falta();">
                    <option value="">Seleccione</option>
                    <?php                        
                       $consulta_falta="SELECT * FROM tipos_faltas";
                        $resultado_falta=sql_ejecutar($consulta_falta);
                        while($fila=fetch_array($resultado_falta))
                        {

                                if($fetch33['tipo_falta']==$fila['id'])
                                {?>
                                     <option  value="<?=$fila['id'];?>" selected><?=utf8_encode($fila['articulo'])." - ".utf8_encode($fila['numeral'])." - ".$fila['descripcion'];?></option> 
                                <?}
                                else
                                {?>
                                     <option  value="<?=$fila['id'];?>"><?=utf8_encode($fila['articulo'])." - ".utf8_encode($fila['numeral'])." - ".$fila['descripcion'];?></option>
                                <?}         


                        }

                    ?>
                </select>
        </div>       

    </div>
    <div class="form-group">                                
        <label class="col-md-2 control-label" for="txtcodigo">Artículo: </label>                                
        <div class="col-md-7">
            <input type="text" name="articulo" class="form-control" id="articulo" readonly <? if (isset($fetch33['articulo'])) echo "value='$fetch33[articulo]'"?> size="70"/>
        </div>                                
    </div>
    <div class="form-group">                                
        <label class="col-md-2 control-label" for="txtcodigo">Numeral: </label>                                
        <div class="col-md-7">
            <input type="text" name="numeral" class="form-control" id="numeral" readonly <? if (isset($fetch33['numeral'])) echo "value='$fetch33[numeral]'"?> size="70"/>
        </div>                                
    </div>   
     <div class="form-group">

        <label class="col-md-2 control-label" for="txtcodigo">Numeral Descripción: </label>                               
       <div class="col-md-7">
            <textarea name="numeral_descripcion" id="numeral_descripcion" class="form-control" readonly rows="3" style="resize:vertical;"><?php if (isset($fetch33['numeral_descripcion'])) echo "$fetch33[numeral_descripcion]"?></textarea>
       </div>
   </div>  
    <div class="form-group">

        <label class="col-md-2 control-label" for="txtcodigo">Motivo: </label>                               
       <div class="col-md-7">
            <textarea name="descripcion" id="descripcion" class="form-control" rows="3" style="resize:vertical;"><?php if (isset($fetch33['descripcion'])) echo "$fetch33[descripcion]"?></textarea>
       </div>
   </div>  
   
</fieldset>
<?
