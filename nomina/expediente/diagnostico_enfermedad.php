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
    <legend >Diagnostico por Enfermedad</legend>
<div class="form-group">
   <label class="col-md-2 control-label" for="txtcodigo">Fecha:</label>                            
   <div class="col-md-7">
       <div class="input-group date date-picker" data-provide="datepicker" data-date-format="dd/mm/yyyy" >
               <input size="10" type="text" name="fecha" id="fecha" class="form-control" readonly="readonly" placeholder="(dd/mm/aaaa)" value="<?if(isset($fetch33['fecha'])) echo fecha($fetch33['fecha']);?>">
               <span class="input-group-btn">
                   <button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
               </span>    
           </div>                                
   </div>    
</div>
    
<div id="medico"> 
<div class="form-group">
    <label class="col-md-2 control-label" for="txtcodigo">Centro Médico:</label>    
    <div class="col-md-7">
        <input type="text" class="form-control" size="50" name="id_centro" id="id_centro" maxlength="10" <? if (isset($fetch33['id_centro'])) echo "value='$fetch33[id_centro]'"?>/>
    </div>
</div>

<div class="form-group">
    <label class="col-md-2 control-label" for="txtcodigo">Nº Certificado:</label>    
    <div class="col-md-7">
        <input type="text" class="form-control" size="50" name="num_certificado" id="num_certificado" maxlength="10" <? if (isset($fetch33['num_certificado'])) echo "value='$fetch33[num_certificado]'"?>/>
    </div>
</div>
    
<div class="form-group">
    <label class="col-md-2 control-label" for="txtcodigo">Nombre Médico:</label>    
    <div class="col-md-7">
        <input type="text" class="form-control" size="50" name="nombre_medico" id="nombre_medico" maxlength="10" <? if (isset($fetch33['nombre_medico'])) echo "value='$fetch33[nombre_medico]'"?>/>
    </div>
</div>     
</div>

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
