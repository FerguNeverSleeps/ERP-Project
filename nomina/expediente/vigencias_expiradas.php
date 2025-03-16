<?php
    
    if(isset($_GET['codigo']) && $_GET['codigo']!='')
    {
            $conexion = conexion();

            $sql1 = "SELECT  *
                    FROM   expediente_vigencia 
                    WHERE  cod_expediente_det='{$_GET['codigo']}'";
            
            $resultado1=sql_ejecutar($sql1);
            $fetch1=fetch_array($resultado1);         
           

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
    <label class="col-md-6 control-label" for="txtcodigo"><strong>MONTO QUE ADEUDA AL ESTADO </strong> </label>                                
</div>

<div class="form-group">                                
    <label class="col-md-2 control-label" for="txtcodigo"><strong>En Concepto De:</strong> </label>                                
    <div class="col-md-7">
        <input type="text" name="concepto" class="form-control" id="concepto" <? if (isset($fetch33['concepto'])) echo "value='$fetch33[concepto]'"?> size="70"/>
    </div>  
</div>

 <div class="form-group" style="position: relative; left: 8%;" >
    <table class="table" style="width: 90%">
        <thead>
          <tr>
            <th>Período Adeudado</th>
            <th>Salario Devengado</th>
            <th>Categoría</th>
            <th>Salario que Debió Devengar</th>
            <th>Categoría</th>
            <th>Diferencia</th>
            <th>Período Adeudado (A/M/D)</th>  
            <th>Monto a Pagar</th>  

          </tr>
        </thead>
        <tbody>
          <tr>                                       
            <td>
                <input type="text" name="periodo_adeudado" class="form-control" id="periodo_adeudado" <? if (isset($fetch1['periodo_adeudado'])) echo "value='$fetch1[periodo_adeudado]'"?> size="30"/>

            </td>
            <td>
                 <input type="text" name="salario_devengado" class="form-control" id="salario_devengado" <? if (isset($fetch1['salario_devengado'])) echo "value='$fetch1[salario_devengado]'"?> size="30"/>

            </td>
            <td>
                <input type="text" name="categoria_1" class="form-control" id="categoria_1" <? if (isset($fetch1['categoria_1'])) echo "value='$fetch1[categoria_1]'"?> size="30"/>

            </td>
            <td>
                <input type="text" name="salario_devengar" class="form-control" id="salario_devengar" <? if (isset($fetch1['salario_devengar'])) echo "value='$fetch1[salario_devengar]'"?> size="30"/>

            </td>
            <td>
                <input type="text" name="categoria_2" class="form-control" id="categoria_2" <? if (isset($fetch1['categoria_2'])) echo "value='$fetch1[categoria_2]'"?> size="30"/>

            </td>
            <td>
                <input type="text" name="diferencia" class="form-control" id="diferencia" <? if (isset($fetch1['diferencia'])) echo "value='$fetch1[diferencia]'"?> size="30"/>

            </td>
            <td>
                <input type="text" name="periodo_adeudado_amd" class="form-control" id="periodo_adeudado_amd" <? if (isset($fetch1['periodo_adeudado_amd'])) echo "value='$fetch1[periodo_adeudado_amd]'"?> size="30"/>

            </td>
            <td>
                <input type="text" name="monto_pagar" class="form-control" id="monto_pagar" <? if (isset($fetch1['monto_pagar'])) echo "value='$fetch1[monto_pagar]'"?> size="30"/>

            </td>

          </tr>                                      
        </tbody>
      </table>

</div>   

<div class="form-group">                                
    <label class="col-md-2 control-label" for="txtcodigo">Analista Responsable: </label>                                
    <div class="col-md-7">
        <input type="textarea" name="analista" class="form-control" id="analista" <? if (isset($fetch33['analista'])) echo "value='$fetch33[analista]'"?> size="70"/>
    </div>
</div>

<div class="form-group">                                
    <label class="col-md-2 control-label" for="txtcodigo">Auditor: </label>                                
    <div class="col-md-7">
        <input type="textarea" name="auditor" class="form-control" id="auditor" <? if (isset($fetch33['auditor'])) echo "value='$fetch33[auditor]'"?> size="70"/>
    </div>
</div>

<div class="form-group">

    <label class="col-md-2 control-label" for="txtcodigo">Observaciones: </label>                               
   <div class="col-md-7">
        <textarea name="descripcion" id="descripcion" class="form-control" rows="3" style="resize:vertical;"><?php if (isset($fetch33['descripcion'])) echo "$fetch33[descripcion]"?></textarea>
   </div>
</div>

<?
