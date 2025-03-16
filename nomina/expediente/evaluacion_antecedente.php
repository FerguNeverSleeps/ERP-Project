<?
$conexion = conexion();

    $ascenso_anio_1="2016";
    $ascenso_anio_2="2017";
    $ascenso_anio_actual="2018";
    
    $ascenso_desempenio_1_1=0.0;
    $ascenso_desempenio_1_2=0.0;
    $ascenso_desempenio_2_1=0.0;
    $ascenso_desempenio_2_2=0.0;
    $ascenso_desempenio_total=0.0;
    $ascenso_desempenio_porcentaje=0.0;
    $ascenso_conducta_1=0.0;
    $ascenso_conducta_2=0.0;
    $ascenso_conducta_actual=0.0;
    $ascenso_conducta_porcentaje=30.0;
    $ascenso_participativa_actual=0.0;
    $ascenso_participativa_porcentaje=0.0;
    $ascenso_investigacion="NO";
    $ascenso_puntaje_total=0.0;
    
    if(isset($_GET['cedula']) && $_GET['cedula']!='')
    {
           
            $sql2 = "SELECT *
                    FROM   nompersonal
                    WHERE  cedula='{$_GET['cedula']}'";                    
           
            
            $resultado2=sql_ejecutar($sql2);
            $fetch2=fetch_array($resultado2);  
            
            //EVALUACIONES
            $sql_evaluacion = "SELECT YEAR(fini_periodo) as anio, puntaje, cedula
                    FROM   empleado_evaluacion
                    WHERE  cedula='{$_GET['cedula']}'
                    AND (YEAR(fini_periodo)='{$ascenso_anio_1}')
                    ORDER BY YEAR(fini_periodo)";    
            $resultado_evaluacion1=sql_ejecutar($sql_evaluacion);
            $i=1;
             while($fila_evaluacion1=fetch_array($resultado_evaluacion1))
            {
                if($i==1) 
                {
                    $ascenso_desempenio_1_1=$fila_evaluacion1['puntaje'];
                }
                else
                {
                    $ascenso_desempenio_1_2=$fila_evaluacion1['puntaje'];
                }  
                $i++;
            }
            
            $sql_evaluacion = "SELECT YEAR(fini_periodo) as anio, puntaje, cedula
                    FROM   empleado_evaluacion
                    WHERE  cedula='{$_GET['cedula']}'
                    AND (YEAR(fini_periodo)='{$ascenso_anio_2}')
                    ORDER BY YEAR(fini_periodo)";    
            $resultado_evaluacion2=sql_ejecutar($sql_evaluacion);
            $i=1;
             while($fila_evaluacion2=fetch_array($resultado_evaluacion2))
            {
                if($i==1) 
                {
                    $ascenso_desempenio_2_1=$fila_evaluacion2['puntaje'];
                }
                else
                {
                    $ascenso_desempenio_2_2=$fila_evaluacion2['puntaje'];
                }  
                $i++;
            }
            
            $ascenso_desempenio_total=$ascenso_desempenio_1_1+ $ascenso_desempenio_1_2+$ascenso_desempenio_2_1+$ascenso_desempenio_2_2;
            $ascenso_desempenio_porcentaje=($ascenso_desempenio_total/4)*0.40;
            
            //AMONESTACIONES
            $amonestacion1=$amonestacion2=0;
            $amonestacion_verbal_169_1=$amonestacion_escrita_169_1=$amonestacion_escrita_171_1=0;
            $sql_amonestacion = "SELECT YEAR(fecha) as anio, articulo, tipo, cedula
                    FROM   amonestaciones
                    WHERE  cedula='{$_GET['cedula']}'
                    AND (YEAR(fecha)='{$ascenso_anio_1}')
                    ORDER BY YEAR(fecha)";    
            $resultado_amonestacion1=sql_ejecutar($sql_amonestacion);           
             while($fila_amonestacion1=fetch_array($resultado_amonestacion1))
            {
                if(trim($fila_amonestacion1['articulo'])=='169' && trim($fila_amonestacion1['tipo'])=="VERBAL")
                {
                    $amonestacion_verbal_169_1++;
                }
                
                if(trim($fila_amonestacion1['articulo'])=='169' && trim($fila_amonestacion1['tipo'])=="ESCRITA")
                {
                    $amonestacion_escrita_169_1++;
                }
                
                 if(trim($fila_amonestacion1['articulo'])=='171' && trim($fila_amonestacion1['tipo'])=="ESCRITA")
                {
                    $amonestacion_escrita_171_1++;
                }
                    
            }
            $amonestacion1=$amonestacion_verbal_169_1*0.3 + $amonestacion_escrita_169_1*1.5 + $amonestacion_escrita_171_1*1.5;
            //echo $amonestacion_1;
            
            $amonestacion_verbal_169_2=$amonestacion_escrita_169_2=$amonestacion_escrita_171_2=0;
            $sql_amonestacion = "SELECT YEAR(fecha) as anio, articulo, tipo, cedula
                    FROM   amonestaciones
                    WHERE  cedula='{$_GET['cedula']}'
                    AND (YEAR(fecha)='{$ascenso_anio_2}')
                    ORDER BY YEAR(fecha)";    
            $resultado_amonestacion2=sql_ejecutar($sql_amonestacion);
            $i=1;
             while($fila_amonestacion2=fetch_array($resultado_amonestacion2))
            {
                
                if(trim($fila_amonestacion1['articulo'])=='169' && trim($fila_amonestacion1['tipo'])=="VERBAL")
                {
                    $amonestacion_verbal_169_2++;
                }
                
                if(trim($fila_amonestacion1['articulo'])=='169' && trim($fila_amonestacion1['tipo'])=="ESCRITA")
                {
                    $amonestacion_escrita_169_2++;
                }
                
                 if(trim($fila_amonestacion1['articulo'])=='171' && trim($fila_amonestacion1['tipo'])=="ESCRITA")
                {
                    $amonestacion_escrita_171_2++;
                }
               
            }
            $amonestacion2=$amonestacion_verbal_169_2*0.3 + $amonestacion_escrita_169_2*1.5 + $amonestacion_escrita_171_2*1.5;
            
             $amonestacion_verbal_169_actual=$amonestacion_escrita_169_actual=$amonestacion_escrita_171_actual=0;
            $sql_amonestacion = "SELECT YEAR(fecha) as anio, articulo, tipo, cedula
                    FROM   amonestaciones
                    WHERE  cedula='{$_GET['cedula']}'
                    AND (YEAR(fecha)='{$ascenso_anio_actual}')
                    ORDER BY YEAR(fecha)";    
            $resultado_amonestacionactual=sql_ejecutar($sql_amonestacion);
            $i=1;
             while($fila_amonestacionactual=fetch_array($resultado_amonestacionactual))
            {
                
                if(trim($fila_amonestacionactual['articulo'])=='169' && trim($fila_amonestacionactual['tipo'])=="VERBAL")
                {
                    $amonestacion_verbal_169_actual++;
                }
                
                if(trim($fila_amonestacionactual['articulo'])=='169' && trim($fila_amonestacionactual['tipo'])=="ESCRITA")
                {
                    $amonestacion_escrita_169_actual++;
                }
                
                 if(trim($fila_amonestacionactual['articulo'])=='171' && trim($fila_amonestacionactual['tipo'])=="ESCRITA")
                {
                    $amonestacion_escrita_171_actual++;
                }
               
            }
            $amonestacionactual=$amonestacion_verbal_169_actual*0.3 + $amonestacion_escrita_169_actual*1.5 + $amonestacion_escrita_171_actual*1.5;
            
            
            
            //SUSPENSIONES
            $suspension1=$suspension2=0;
            $suspensiones_porcentaje_169_1=$suspensiones_porcentaje_171_1=$suspensiones_porcentaje_169_2=$suspensiones_porcentaje_171_2=0;
            $supensiones_dias_169_1=$supensiones_dias_171_1=0;            
            $sql_suspension = "SELECT YEAR(fecha_desde) as anio, articulo, dias, cedula
                    FROM   suspenciones
                    WHERE  cedula='{$_GET['cedula']}'
                    AND (YEAR(fecha_desde)='{$ascenso_anio_1}')
                    ORDER BY YEAR(fecha_desde)";    
            $resultado_suspension1=sql_ejecutar($sql_suspension);           
             while($fila_suspension1=fetch_array($resultado_suspension1))
            {
                if(trim($fila_suspension1['articulo'])=='169')
                {
                    $supensiones_dias_169_1+=$fila_suspension1['dias'];
                }
                if(trim($fila_suspension1['articulo'])=='171')
                {
                    $supensiones_dias_171_1+=$fila_suspension1['dias'];
                }
                
                    
            }
            
            if($supensiones_dias_169_1>=2 && $supensiones_dias_169_1<3)
            {
                $suspensiones_porcentaje_169_1=6;
            }
            if($supensiones_dias_169_1>=3 && $supensiones_dias_169_1<5)
            {
                $suspensiones_porcentaje_169_1=9;
            }
             if($supensiones_dias_169_1>=5)
            {
                $suspensiones_porcentaje_169_1=15;
            }
            
            if($supensiones_dias_171_1>=2 && $supensiones_dias_171_1<3)
            {
                $suspensiones_porcentaje_171_1=6;
            }
            if($supensiones_dias_171_1>=3 && $supensiones_dias_171_1<5)
            {
                $suspensiones_porcentaje_171_1=9;
            }
            if($supensiones_dias_171_1>=5 && $supensiones_dias_171_1<10)
            {
                $suspensiones_porcentaje_171_1=15;
            }
            if($supensiones_dias_171_1>=10)
            {
                $suspensiones_porcentaje_171_1=25.5;
            }            
            $suspension1=$suspensiones_porcentaje_169_1+$suspensiones_porcentaje_171_1;
            
            $supensiones_dias_169_2=$supensiones_dias_171_2=0;
            $sql_suspension = "SELECT YEAR(fecha_desde) as anio, articulo, dias, cedula
                    FROM   suspenciones
                    WHERE  cedula='{$_GET['cedula']}'
                    AND (YEAR(fecha_desde)='{$ascenso_anio_2}')
                    ORDER BY YEAR(fecha_desde)";    
            $resultado_suspension2=sql_ejecutar($sql_suspension);           
             while($fila_suspension2=fetch_array($resultado_suspension2))
            {
                if(trim($fila_suspension2['articulo'])=='169')
                {
                    $supensiones_dias_169_2+=$fila_suspension2['dias'];
                }
                if(trim($fila_suspension2['articulo'])=='171')
                {
                    $supensiones_dias_171_2+=$fila_suspension2['dias'];
                }
                
                    
            }
            if($supensiones_dias_169_2>=2 && $supensiones_dias_169_2<3)
            {
                $suspensiones_porcentaje_169_2=6;
            }
            if($supensiones_dias_169_2>=3 && $supensiones_dias_169_2<5)
            {
                $suspensiones_porcentaje_169_2=9;
            }
             if($supensiones_dias_169_2>=5)
            {
                $suspensiones_porcentaje_169_2=15;
            }
            
            if($supensiones_dias_171_2>=2 && $supensiones_dias_171_2<3)
            {
                $suspensiones_porcentaje_171_2=6;
            }
            if($supensiones_dias_171_2>=3 && $supensiones_dias_171_2<5)
            {
                $suspensiones_porcentaje_171_2=9;
            }
            if($supensiones_dias_171_2>=5 && $supensiones_dias_171_2<10)
            {
                $suspensiones_porcentaje_171_2=15;
            }
            if($supensiones_dias_171_2>=10)
            {
                $suspensiones_porcentaje_171_2=25.5;
            }            
            $suspension2=$suspensiones_porcentaje_169_2+$suspensiones_porcentaje_171_2;
            
              $supensiones_dias_169_actual=$supensiones_dias_171_actual=0;
            $sql_suspension = "SELECT YEAR(fecha_desde) as anio, articulo, dias, cedula
                    FROM   suspenciones
                    WHERE  cedula='{$_GET['cedula']}'
                    AND (YEAR(fecha_desde)='{$ascenso_anio_2}')
                    ORDER BY YEAR(fecha_desde)";    
            $resultado_suspensionactual=sql_ejecutar($sql_suspension);           
             while($fila_suspensionactual=fetch_array($resultado_suspensionactual))
            {
                if(trim($fila_suspensionactual['articulo'])=='169')
                {
                    $supensiones_dias_169_actual+=$fila_suspensionactual['dias'];
                }
                if(trim($fila_suspensionactual['articulo'])=='171')
                {
                    $supensiones_dias_171_actual+=$fila_suspensionactual['dias'];
                }
                
                    
            }
            if($supensiones_dias_169_actual>=2 && $supensiones_dias_169_actual<3)
            {
                $suspensiones_porcentaje_169_actual=6;
            }
            if($supensiones_dias_169_actual>=3 && $supensiones_dias_169_actual<5)
            {
                $suspensiones_porcentaje_169_actual=9;
            }
             if($supensiones_dias_169_actual>=5)
            {
                $suspensiones_porcentaje_169_actual=15;
            }
            
            if($supensiones_dias_171_actual>=2 && $supensiones_dias_171_actual<3)
            {
                $suspensiones_porcentaje_171_actual=6;
            }
            if($supensiones_dias_171_actual>=3 && $supensiones_dias_171_actual<5)
            {
                $suspensiones_porcentaje_171_actual=9;
            }
            if($supensiones_dias_171_actual>=5 && $supensiones_dias_171_actual<10)
            {
                $suspensiones_porcentaje_171_actual=15;
            }
            if($supensiones_dias_171_actual>=10)
            {
                $suspensiones_porcentaje_171_actual=25.5;
            }
            
            $suspensionactual=$suspensiones_porcentaje_169_actual+$suspensiones_porcentaje_171_actual;
            
            //echo $amonestacion1; echo " / "; echo $suspension1; echo " / "; echo $supensiones_dias_171_1; 
            $ascenso_conducta_1=$amonestacion1+$suspension1;
            
            $ascenso_conducta_2=$amonestacion2+$suspension2;
            
            $ascenso_conducta_actual=$amonestacionactual+$suspensionactual;
            
            $ascenso_conducta_porcentaje=30-($ascenso_conducta_1+$ascenso_conducta_2+$ascenso_conducta_actual);
            $ascenso_puntaje_total=$ascenso_desempenio_porcentaje+$ascenso_conducta_porcentaje;
    }
    
    
    if(isset($_GET['codigo']) && $_GET['codigo']!='')
    {
            

            $sql1 = "SELECT  *
                    FROM   expediente
                    WHERE  cod_expediente_det='{$_GET['codigo']}'";
            
            $resultado1=sql_ejecutar($sql1);
            $fetch1=fetch_array($resultado1); 
            
            $codigo = $_GET['codigo'];            
            $ascenso_salario_anterior=$fetch33['ascenso_salario_anterior'];
            $ascenso_salario_nuevo=$fetch33['ascenso_salario_nuevo'];
            $ascenso_nivel_anterior=$fetch33['ascenso_nivel_anterior'];
            $ascenso_nivel_nuevo=$fetch33['ascenso_nivel_nuevo'];
            $ascenso_cargo_anterior=$fetch33['ascenso_cargo_anterior'];
            $ascenso_cargo_nuevo=$fetch33['ascenso_cargo_nuevo'];
            $ascenso_desempenio_1_1=$fetch33['ascenso_desempenio_1_1'];
            $ascenso_desempenio_1_2=$fetch33['ascenso_desempenio_1_2'];
            $ascenso_desempenio_2_1=$fetch33['ascenso_desempenio_2_1'];
            $ascenso_desempenio_2_2=$fetch33['ascenso_desempenio_2_2'];
            $ascenso_desempenio_total=$fetch33['ascenso_desempenio_total'];
            $ascenso_desempenio_porcentaje=$fetch33['ascenso_desempenio_porcentaje'];
            $ascenso_conducta_1=$fetch33['ascenso_conducta_1'];
            $ascenso_conducta_2=$fetch33['ascenso_conducta_2'];
            $ascenso_conducta_actual=$fetch33['ascenso_conducta_actual'];
            $ascenso_conducta_porcentaje=$fetch33['ascenso_conducta_porcentaje'];
            $ascenso_participativa_actual=$fetch33['ascenso_participativa_actual'];
            $ascenso_participativa_porcentaje=$fetch33['ascenso_participativa_porcentaje'];
            $ascenso_investigacion=$fetch33['ascenso_investigacion'];
            $ascenso_puntaje_total=$fetch33['ascenso_puntaje_total'];
            

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
    <div class="col-md-7">
        <input type="text" name="nombre_apellido" class="form-control" id="nombre_apellido" readonly="true" <? if (isset($nombre)) echo "value='$nombre'"?> size="70"/>
    </div>
</div> 
    
 <div class="form-group">   
    <label for="txtcodigo" class="col-md-2 control-label">Nº de Cédula: </label>                                    
    <div class="col-md-7">
        <input class="form-control" type="text" name="cedula" id="cedula" readonly="true" <? if (isset($cedula)) echo "value='$cedula'"?> /> 
    </div>

</div>

<div class="form-group">                                
    <label class="col-md-2 control-label" for="txtcodigo">Nº de Posición: </label>                                
    <div class="col-md-7">
        <input type="text" name="posicion" class="form-control" id="posicion" readonly="true" <? if (isset($posicion)) echo "value='$posicion'"?> size="70"/>
    </div>
    

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

<div class="form-group">                                
    <label class="col-md-2 control-label" for="txtcodigo">Fecha Inicio (Institución): </label>                                
    <div class="col-md-3">
        <input type="text" name="fecha_ingreso" class="form-control" id="fecha_ingreso" readonly="true" value="<?if(isset($fecha_inicio)) echo fecha($fecha_inicio);?>" size="70"/>
    </div>
    
</div>
  
<div class="form-group">                               
    
    <label for="txtcodigo" class="col-md-2 control-label">Departamento: </label>                                    
    <div class="col-md-7">
        <input class="form-control" type="text" name="departamento" id="departamento" readonly="true"  <? if (isset($nivel2)) echo "value='$nivel2'"?> /> 
    </div>
</div>

<div class="form-group">
    <label class="col-md-2 control-label" for="txtcodigo">Nivel Actual: </label>
    <div class="col-md-7">                               
            
            <select name="ascenso_nivel_anterior" class="form-control" id="ascenso_nivel_anterior" disabled>
                <option value="">Seleccione</option>
                <?php                     
                    $consulta_ascenso="SELECT * FROM tipo_ascenso";
                    $resultado_ascenso_anterior=sql_ejecutar($consulta_ascenso);
                    while($fila_ascenso_anterior=fetch_array($resultado_ascenso_anterior))
                    {
                        //echo "AQUI";
                        if ($codigo=='')
                        {
                            if(!$fetch2['nivel'])
                            {?>
                                <option  value="<?=$fila_ascenso_anterior['id'];?>"><?=utf8_encode($fila_ascenso_anterior['nombre']);?></option>
                            <?}
                            else
                            { 
                               if($fetch2['nivel']==$fila_ascenso_anterior['id'])
                               {?>
                                    <option  value="<?=$fila_ascenso_anterior['id'];?>" selected><?=utf8_encode($fila_ascenso_anterior['nombre']);?></option> 
                               <?}
                               else
                               {?>
                                    <option  value="<?=$fila_ascenso_anterior['id'];?>"><?=utf8_encode($fila_ascenso_anterior['nombre']);?></option>
                               <?}
                            }
                        }
                        else
                        {
                            if($fetch33['ascenso_nivel_anterior']==$fila_ascenso_anterior['id'])
                            {?>
                                 <option  value="<?=$fila_ascenso_anterior['id'];?>" selected><?=utf8_encode($fila_ascenso_anterior['nombre']);?></option> 
                            <?}
                            else
                            {?>
                                 <option  value="<?=$fila_ascenso_anterior['id'];?>"><?=utf8_encode($fila_ascenso_anterior['nombre']);?></option>
                            <?}         
                        }

                    }
                    ?>
            </select>
    </div>       
    
</div>    

<div class="form-group">
    <label class="col-md-2 control-label" for="txtcodigo">Nivel a Ascender: </label>
    <div class="col-md-7">                               
            
            <select name="ascenso_nivel_nuevo" class="form-control" id="ascenso_nivel_nuevo">
                <option value="">Seleccione</option>
                <?php                     
                    $consulta_ascenso="SELECT * FROM tipo_ascenso";
                    $resultado_ascenso=sql_ejecutar($consulta_ascenso);
                    while($fila_ascenso=fetch_array($resultado_ascenso))
                    {
                        //echo "AQUI";
                        if ($codigo=='')
                        {
                            if(!$fetch2['nivel'])
                            {?>
                                <option  value="<?=$fila_ascenso['id'];?>"><?=utf8_encode($fila_ascenso['nombre']);?></option>
                            <?}
                            else
                            { 
                               if($fetch2['nivel']==$fila_ascenso['id'])
                               {?>
                                    <option  value="<?=$fila_ascenso['id'];?>" selected><?=utf8_encode($fila_ascenso['nombre']);?></option> 
                               <?}
                               else
                               {?>
                                    <option  value="<?=$fila_ascenso['id'];?>"><?=utf8_encode($fila_ascenso['nombre']);?></option>
                               <?}
                            }
                        }
                        else
                        {
                            if($fetch33['ascenso_nivel_nuevo']==$fila_ascenso['id'])
                            {?>
                                 <option  value="<?=$fila_ascenso['id'];?>" selected><?=utf8_encode($fila_ascenso['nombre']);?></option> 
                            <?}
                            else
                            {?>
                                 <option  value="<?=$fila_ascenso['id'];?>"><?=utf8_encode($fila_ascenso['nombre']);?></option>
                            <?}         
                        }

                    }
                    ?>
            </select>
    </div>       
    
</div>    

<div class="form-group">
    <label class="col-md-2 control-label" for="txtcodigo">Cargo Actual: </label>
    <div class="col-md-7">                               
            
            <select name="ascenso_cargo_anterior" class="form-control" id="ascenso_cargo_anterior" disabled>
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
                            if($fetch33['ascenso_cargo_anterior']==$fila_cargo_anterior['cod_car'])
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
    <label class="col-md-2 control-label" for="txtcodigo">Cargo a Ascender: </label>
    <div class="col-md-7">                               
            
            <select name="ascenso_cargo_nuevo" class="form-control" id="ascenso_cargo_nuevo">
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
                            if($fetch33['ascenso_cargo_nuevo']==$fila_cargo['cod_car'])
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
        <input type="text" name="ascenso_salario_anterior" class="form-control" id="ascenso_salario_anterior" readonly <? echo "value='$salario'"?> size="70"/>
    </div>
</div>

<div class="form-group">                                
    <label class="col-md-2 control-label" for="txtcodigo">Salario a Ascender: </label>                                
    <div class="col-md-7">
        <input type="text" name="ascenso_salario_nuevo" class="form-control" id="ascenso_salario_nuevo" <? echo "value='$ascenso_salario_nuevo'"?> size="70"/>
    </div>
</div>

<div class="form-group">
        <?php
                //$tipemp1 = 'checked';
                $formato1 = '';
                $formato2 = '';
                

                if($codigo!='')
                {
                        $formato1 = ($fetch33['dejado']=='1')  ? 'checked' : '';
                        $formato2 = ($fetch33['dejado']=='2')  ? 'checked' : '';

                        
                } 
        ?>
        <label class="control-label col-md-2" for="txtcodigo">Formato Documento</label>
        <div class="col-md-7">
                <div class="radio-list">
                        <label class="radio-inline">
                            <input type="radio" name="formato" id="formato1" value="1" <?php echo $formato1; ?>> 1</label>
                        <label class="radio-inline">
                            <input type="radio" name="formato" id="formato2" value="2" <?php echo $formato2; ?>> 2</label>
                </div>
        </div>
</div>

</fieldset>


<fieldset>
    <legend >Evaluación de Antecedentes</legend>
<div class="form-group">
    <label class="col-md-2 control-label" for="txtcodigo">Fecha:</label>
    <div class="col-md-3">
        <div class="input-group date date-picker" data-provide="datepicker" data-date-format="dd/mm/yyyy">
            <input size="10" class="form-control" placeholder="(dd/mm/aaaa)" type="text" name="fecha" id="fecha" readonly="readonly" value="<?if(isset($fetch33['fecha'])){ if($fetch33['fecha']!="0000-00-00") {echo fecha($fetch33['fecha']);} else{echo date("d/m/Y");} }?>">
            <span class="input-group-btn">
                <button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
            </span> 
        </div>
    </div>
</div>
    
<div class="form-group">                                
    <label class="col-md-2 control-label" for="txtcodigo">Antecedentes Vigentes</label> 
    <div class="col-md-1">           
    </div>
    <div class="col-md-1">
            
    </div>
    <div class="col-md-1">       
          
    </div>
    <div class="col-md-1">             
        
    </div>
    <label class="col-md-2 control-label" for="txtcodigo">Bajo Investigación:</label> 
    <div class="col-md-1">
        <input type="text" name="ascenso_investigacion" class="form-control" id="ascenso_investigacion" <? echo "value='$ascenso_investigacion'"?> size="70"/>
    </div>
</div>
<div class="form-group">                                
    <label class="col-md-2 control-label" for="txtcodigo">Evaluación de Desempeño: </label> 
    <div class="col-md-1">
        <input type="text" name="ascenso_desempenio_1_1" class="form-control" id="ascenso_desempenio_1_1" <? echo "value='$ascenso_desempenio_1_1'"?>  onblur="calcular_desempenio_porcentaje();calcular_total_porcentaje();" size="70"/>
        <label class="control-label" for="txtcodigo"><? echo $ascenso_anio_1;?> </label>             
    </div>
    <div class="col-md-1">
        <input type="text" name="ascenso_desempenio_1_2" class="form-control" id="ascenso_desempenio_1_2" <? echo "value='$ascenso_desempenio_1_2'"?> onblur="calcular_desempenio_porcentaje();calcular_total_porcentaje();" size="70"/>
        <label class="control-label" for="txtcodigo"><? echo $ascenso_anio_1;?> </label>          
    </div>
    <div class="col-md-1">       
       <input type="text" name="ascenso_desempenio_2_1" class="form-control" id="ascenso_desempenio_2_1" <? echo "value='$ascenso_desempenio_2_1'"?> onblur="calcular_desempenio_porcentaje();calcular_total_porcentaje();" size="70"/>
       <label class="control-label" for="txtcodigo"><? echo $ascenso_anio_2;?> </label>    
    </div>
    <div class="col-md-1">             
        <input type="text" name="ascenso_desempenio_2_2" class="form-control" id="ascenso_desempenio_2_2" <? echo "value='$ascenso_desempenio_2_2'"?> onblur="calcular_desempenio_porcentaje();calcular_total_porcentaje();"  size="70"/>
        <label class="col-md-1 control-label" for="txtcodigo"><? echo $ascenso_anio_2;?> </label>
    </div>
    <label class="col-md-1 control-label" for="txtcodigo">Porcentaje: </label> 
    <label class="col-md-1 control-label" for="txtcodigo">40%</label> 
    <div class="col-md-1">
        <input type="text" name="ascenso_desempenio_porcentaje" class="form-control" id="ascenso_desempenio_porcentaje" <? echo "value='$ascenso_desempenio_porcentaje'"?> size="70"/>
    </div>
</div>

<div class="form-group">                                
    <label class="col-md-2 control-label" for="txtcodigo">Evaluación de Conducta: </label> 
    <div class="col-md-1">           
    </div>
    <div class="col-md-1">
        <input type="text" name="ascenso_conducta_1" class="form-control" id="ascenso_conducta_1" <? echo "value='$ascenso_conducta_1'"?> onblur="calcular_conducta_porcentaje();calcular_total_porcentaje();"  size="70"/>
        <label class="control-label" for="txtcodigo"><? echo $ascenso_anio_1;?> </label>          
    </div>
    <div class="col-md-1">       
          
    </div>
    <div class="col-md-1">             
        <input type="text" name="ascenso_conducta_2" class="form-control" id="ascenso_conducta_2" <? echo "value='$ascenso_conducta_2'"?>  onblur="calcular_conducta_porcentaje();calcular_total_porcentaje();"  size="70"/>
        <label class="col-md-1 control-label" for="txtcodigo"><? echo $ascenso_anio_2;?> </label>
    </div>
    <div class="col-md-1">             
        <input type="text" name="ascenso_conducta_actual" class="form-control" id="ascenso_conducta_actual" <? echo "value='$ascenso_conducta_actual'"?>  onblur="calcular_conducta_porcentaje();calcular_total_porcentaje();"  size="70"/>
        <label class="col-md-1 control-label" for="txtcodigo"><? echo $ascenso_anio_actual;?> </label>
    </div>
    
    <label class="col-md-1 control-label" for="txtcodigo">30%</label> 
    <div class="col-md-1">
        <input type="text" name="ascenso_conducta_porcentaje" class="form-control" id="ascenso_conducta_porcentaje" <? echo "value='$ascenso_conducta_porcentaje'"?> size="70"/>
    </div>
</div>
    
<div class="form-group">                                
    <label class="col-md-2 control-label" for="txtcodigo">Evaluación Participativa: </label> 
    <div class="col-md-1">           
    </div>
    <div class="col-md-1">
            
    </div>
    <div class="col-md-1">       
          
    </div>
    <div class="col-md-1">       
          
    </div>
    <div class="col-md-1">             
        <input type="text" name="ascenso_participativa_actual" class="form-control" id="ascenso_participativa_actual" <? echo "value='$ascenso_participativa_actual'"?> onblur="calcular_participativa_porcentaje();calcular_total_porcentaje();"  size="70"/>
        <label class="col-md-1 control-label" for="txtcodigo"><? echo $ascenso_anio_actual;?> </label>
    </div>
    
    <label class="col-md-1 control-label" for="txtcodigo">30%</label> 
    <div class="col-md-1">
        <input type="text" name="ascenso_participativa_porcentaje" class="form-control" id="ascenso_participativa_porcentaje" <? echo "value='$ascenso_participativa_porcentaje'"?> size="70"/>
    </div>
</div>

<div class="form-group">                                
    <label class="col-md-2 control-label" for="txtcodigo"> </label> 
    <div class="col-md-1">           
    </div>
    <div class="col-md-1">
            
    </div>
    <div class="col-md-1">       
          
    </div>
    <div class="col-md-1">             
        
    </div>
    <label class="col-md-1 control-label" for="txtcodigo">Puntos:</label> 
    <label class="col-md-1 control-label" for="txtcodigo">100%</label> 
    <div class="col-md-1">
        <input type="text" name="ascenso_puntaje_total" class="form-control" id="ascenso_puntaje_total" <? echo "value='$ascenso_puntaje_total'"?> size="70"/>
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


