<?php
    
    if(isset($_GET['codigo']) && $_GET['codigo']!='')
    {
            $conexion = conexion();

            $sql1 = "SELECT  *
                    FROM   expediente_analisis 
                    WHERE  cod_expediente_det='{$_GET['codigo']}'";
            
            $resultado1=sql_ejecutar($sql1);
            $fetch1=fetch_array($resultado1);
            
            $sql2 = "SELECT  *
                    FROM   expediente_bienal 
                    WHERE  cod_expediente_det='{$_GET['codigo']}'";
            
            $resultado2=sql_ejecutar($sql2);
            $fetch2=fetch_array($resultado2);

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
    <label class="col-md-2 control-label" for="txtcodigo">Fecha Idoneidad:</label>                                
    <div class="col-md-4">
        <div class="input-group date date-picker" data-provide="datepicker" data-date-format="dd/mm/yyyy" >
            <input type="text" name="fecha_idoneidad" class="form-control" id="fecha_idoneidad" placeholder="(dd/mm/aaaa)" size="10" value="<?if(isset($fetch33['fecha_idoneidad'])) echo fecha($fetch33['fecha_idoneidad']);?>" maxlength="10"/>
            <span class="input-group-btn">
                    <button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
            </span>
        </div>
    </div>          
</div>

 <div class="form-group">

    <label class="col-md-2 control-label" for="txtcodigo">Registro: </label>                                
    <div class="col-md-4">
        <input type="text" name="registro" class="form-control" id="registro" <? if (isset($fetch33['registro'])) echo "value='$fetch33[registro]'";?> size="70"/>
    </div>        

</div>

 <div class="form-group">       
    <label class="col-md-2 control-label" for="txtcodigo">Folio: </label>                                
    <div class="col-md-4">
        <input type="text" name="folio" class="form-control" id="folio" <? if (isset($fetch33['folio'])) echo "value='$fetch33[folio]'"?> size="70"/>
    </div>           
</div>


<div class="form-group">                                
    <label class="col-md-2 control-label" for="txtcodigo">Inicio Labores otra Inst. de Salud: </label>                                
    <div class="col-md-3">
        <div class="input-group date date-picker" data-provide="datepicker" data-date-format="dd/mm/yyyy" >
            <input type="text" name="fecha_ini_labor_otra_inst" class="form-control" id="fecha_ini_labor_otra_inst" placeholder="(dd/mm/aaaa)" size="10" value="<?if(isset($fetch33['fecha_ini_labor_otra_inst'])) echo fecha($fetch33['fecha_ini_labor_otra_inst']);?>" maxlength="10"/>
            <span class="input-group-btn">
                    <button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
            </span>
        </div>
    </div>
    
    <label class="col-md-2 control-label" for="txtcodigo">Fin Labores otra Inst. de Salud: </label>                                
    <div class="col-md-3">
        <div class="input-group date date-picker" data-provide="datepicker" data-date-format="dd/mm/yyyy" >
            <input type="text" name="fecha_fin_labor_otra_inst" class="form-control" id="fecha_fin_labor_otra_inst" placeholder="(dd/mm/aaaa)" size="10" value="<?if(isset($fetch33['fecha_fin_labor_otra_inst'])) echo fecha($fetch33['fecha_fin_labor_otra_inst']);?>" maxlength="10"/>
            <span class="input-group-btn">
                    <button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
            </span>
        </div>
    </div>
</div>

<div class="form-group">                                
    <label class="col-md-2 control-label" for="txtcodigo">Inicio Labores por Contrato: </label>                                
    <div class="col-md-3">
        <div class="input-group date date-picker" data-provide="datepicker" data-date-format="dd/mm/yyyy" >
            <input type="text" name="fecha_ini_labor_contrato" class="form-control" id="fecha_ini_labor_contrato" placeholder="(dd/mm/aaaa)" size="10" value="<?if(isset($fetch33['fecha_ini_labor_contrato'])) echo fecha($fetch33['fecha_ini_labor_contrato']);?>" maxlength="10"/>
            <span class="input-group-btn">
                    <button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
            </span>
        </div>
    </div>
    
    <label class="col-md-2 control-label" for="txtcodigo">Fin Labores por Contrato: </label>                                
    <div class="col-md-3">
        <div class="input-group date date-picker" data-provide="datepicker" data-date-format="dd/mm/yyyy" >
            <input type="text" name="fecha_fin_labor_contrato" class="form-control" id="fecha_fin_labor_contrato" placeholder="(dd/mm/aaaa)" size="10" value="<?if(isset($fetch33['fecha_fin_labor_contrato'])) echo fecha($fetch33['fecha_fin_labor_contrato']);?>" maxlength="10"/>
            <span class="input-group-btn">
                    <button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
            </span>
        </div>
    </div>
</div>

<div class="form-group">                                
    <label class="col-md-2 control-label" for="txtcodigo">Inicio de Labores Permanente: </label>                                
    <div class="col-md-3">
        <div class="input-group date date-picker" data-provide="datepicker" data-date-format="dd/mm/yyyy" >
            <input type="text" name="fecha_labor_permanente" class="form-control" id="fecha_labor_permanente" placeholder="(dd/mm/aaaa)" size="10" value="<?if(isset($fetch33['fecha_labor_permanente'])) echo fecha($fetch33['fecha_labor_permanente']);?>" maxlength="10"/>
            <span class="input-group-btn">
                    <button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
            </span>
        </div>
    </div>
</div>

<div class="form-group">                                
    <label class="col-md-2 control-label" for="txtcodigo">SOBRESUELDOS: </label>                                
    <div class="col-md-6">                                    
    </div>                                
</div>

<div class="form-group">                                
    <label class="col-md-2 control-label" for="txtcodigo">Jefatura: </label>                                
    <div class="col-md-2">
        <input type="text" name="sobresueldo_jefatura" class="form-control" id="sobresueldo_jefatura" <? if (isset($fetch33['sobresueldo_jefatura'])) echo "value='$fetch33[sobresueldo_jefatura]'"?> size="70"/>
    </div>
    <label for="txtcodigo" class="col-md-2 control-label">Exclusividad: </label>                                    
    <div class="col-md-2">
        <input class="form-control" type="text" name="sobresueldo_exclusividad" id="sobresueldo_exclusividad" <? if (isset($fetch33['sobresueldo_exclusividad'])) echo "value='$fetch33[sobresueldo_exclusividad]'"?> /> 
    </div>

</div>
<div class="form-group">                                
    <label class="col-md-2 control-label" for="txtcodigo">Alto Riesgo: </label>                                
    <div class="col-md-2">
        <input type="text" name="sobresueldo_altoriesgo" class="form-control" id="sobresueldo_altoriesgo"  <? if (isset($fetch33['sobresueldo_altoriesgo'])) echo "value='$fetch33[sobresueldo_altoriesgo]'"?> size="70"/>
    </div>
    <label for="txtcodigo" class="col-md-2 control-label">Especialidad: </label>                                    
    <div class="col-md-2">
        <input class="form-control" type="text" name="sobresueldo_especialidad" id="sobresueldo_especialidad"  <? if (isset($fetch33['sobresueldo_especialidad'])) echo "value='$fetch33[sobresueldo_especialidad]'"?> /> 
    </div>

</div>

<div class="form-group" style="position: relative; left: 8%;" >
    <table class="table" style="width: 80%">
        <thead>
          <tr>
            <th>Inicio de Lab.</th>
            <th>Etapa&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
            <th>Salario&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
            <th>Resuelto&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
            <th>20%&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
            <th>40%&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
            <th>Resuelto&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
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

                    <input type="text" name="etapa_analisis" class="form-control" id="etapa_analisis" <? if (isset($fetch1['etapa'])) echo "value='$fetch1[etapa]'"?> size="30"/>

            </td>
            <td>

                    <input type="text" name="salario_analisis" class="form-control" id="salario_analisis" <? if (isset($fetch1['salario'])) echo "value='$fetch1[salario]'"?> size="30"/>

            </td>
            <td>

                    <input type="text" name="resuelto_analisis_1" class="form-control" id="resuelto_analisis_1" <? if (isset($fetch1['resuelto_1'])) echo "value='$fetch1[resuelto_1]'"?> size="30"/>

            </td>
            <td>

                    <input type="text" name="veinte_porciento" class="form-control" id="veinte_porciento"   <? if (isset($fetch1['veinte_porciento'])) echo "value='$fetch1[veinte_porciento]'"?> size="30"/>

            </td>
             <td>

                    <input type="text" name="cuarenta_porciento" class="form-control" id="cuarenta_porciento" <? if (isset($fetch1['cuarenta_porciento'])) echo "value='$fetch1[cuarenta_porciento]'"?> size="30"/>

            </td>
            <td>

                    <input type="text" name="resuelto_analisis_2" class="form-control" id="resuelto_analisis_2" <? if (isset($fetch1['resuelto_2'])) echo "value='$fetch1[resuelto_2]'"?> size="30"/>

            </td>
          </tr>                                      
        </tbody>
      </table>

</div>
 <div class="form-group">                                
    <label class="col-md-2 control-label" for="txtcodigo">BIENALES: </label>                                
    <div class="col-md-6">                                    
    </div>                                
</div>
 <div class="form-group" style="position: relative; left: 8%;" >
    <table class="table" style="width: 80%">
        <thead>
          <tr>
            <th>Fecha</th>
            <th>N_Bienal&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
            <th>Salario&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
            <th>Resuelto&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
            <th>Monto_Mensual&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
            <th>Acumulativo&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>                                       
          </tr>
        </thead>
        <tbody>
          <tr>                                       
            <td>
                <div class="input-group date date-picker" data-provide="datepicker" data-date-format="dd/mm/yyyy" >
                    <input type="text" name="fecha_bienal" class="form-control" id="fecha_bienal" value="<?if(isset($fetch2['fecha'])) echo fecha($fetch2['fecha']);?>" size="30"/>
                    <span class="input-group-btn">
                            <button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
                    </span>
                </div>
            </td>
            <td>

                    <input type="text" name="numero_bienal" class="form-control" id="numero_bienal" <? if (isset($fetch2['numero'])) echo "value='$fetch2[numero]'"?> size="30"/>

            </td>
            <td>

                    <input type="text" name="salario_bienal" class="form-control" id="salario_bienal" <? if (isset($fetch2['salario'])) echo "value='$fetch2[salario]'"?> size="30"/>

            </td>
            <td>

                    <input type="text" name="resuelto_bienal" class="form-control" id="resuelto_bienal" <? if (isset($fetch2['resuelto'])) echo "value='$fetch2[resuelto]'"?> size="30"/>

            </td>
            <td>

                    <input type="text" name="monto_mensual_bienal" class="form-control" id="monto_mensual_bienal" <? if (isset($fetch2['monto_mensual'])) echo "value='$fetch2[monto_mensual]'"?> size="30"/>

            </td>
             <td>

                    <input type="text" name="acumulativo_bienal" class="form-control" id="acumulativo_bienal" <? if (isset($fetch2['acumulativo'])) echo "value='$fetch2[acumulativo]'"?> size="30"/>

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

<?

