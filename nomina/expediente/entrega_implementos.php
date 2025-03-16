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
    <legend >Información Empleado</legend>
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
    <label class="col-md-2 control-label" for="txtcodigo">Planilla: </label>                                
    <div class="col-md-3">
        <input type="text" name="planilla_anterior" class="form-control" id="planilla_anterior" readonly="true" <? if (isset($pplanilla)) echo "value='$pplanilla'"?> size="70"/>
    </div>
    <label for="txtcodigo" class="col-md-2 control-label">Departamento: </label>                                    
    <div class="col-md-2">
        <input class="form-control" type="text" name="departamento_anterior" id="departamento_anterior" readonly="true"  <? if (isset($departamento)) echo "value='$departamento'"?> /> 
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
    <label class="col-md-2 control-label" for="txtcodigo">Salario: </label>                                
    <div class="col-md-3">
        <input type="text" name="salario" class="form-control" id="salario" readonly="true" <? if (isset($salario)) echo "value='$salario'"?> size="70"/>
    </div>
    <label for="txtcodigo" class="col-md-2 control-label">Gastos Representación: </label>                                    
    <div class="col-md-2">
        <input class="form-control" type="text" name="gastos_representacion" id="gastos_representacion" readonly="true"  <? if (isset($gastos_representacion)) echo "value='$gastos_representacion'"?> /> 
    </div>

</div>

</fieldset>

<fieldset>
    <legend >Implementos / Herramientas</legend>         
    <div class="form-group">
    

    <label class="col-md-2 control-label" for="txtcodigo">Fecha:</label>
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
        <div class="col-md-12" >
            <!-- BEGIN EXAMPLE TABLE PORTLET-->
            <div class="portlet box blue">
                <div class="portlet-title">
                    <div class="caption">
                        <img src="../imagenes/21.png" width="22" height="22" class="icon">
                    </div>
                    <div class="actions">
                        <a class="btn btn-sm blue" onclick="onAgregar()">
                            <i class="fa fa-plus"></i> Agregar
                        </a>
                    </div>
                </div>
                <div class="portlet-body">	
                    <table class="table table-striped table-bordered table-hover" id="implemento_datatable">
                       <thead>	                          
                            <th width="8%">Artículo</th>
                            <th width="5%">Marca</th>                                                          
                            <th width="5%">Modelo</th>
                            <th width="3%">Talla</th>
                            <th width="5%">Color</th>
                            <th width="3%">Cantidad</th>
                            <th width="5%">Entrega</th>
                            <th width="3%">Vencimiento</th>
<!--                            <th width="8%">Caracteristicas</th>-->
                            <th width="1%">Acciones</th>
                        </thead>
                        <tbody>
                            
                        </tbody>
                    </table>
                    </div>
            </div>
            <!-- END EXAMPLE TABLE PORTLET-->
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

