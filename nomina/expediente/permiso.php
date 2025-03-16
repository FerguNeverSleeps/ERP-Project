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
    
$consulta="SELECT fecing FROM nompersonal where cedula='$_GET[cedula]'";
$resultado0=sql_ejecutar($consulta);
$fetch=fetch_array($resultado0);
$fecing = $fetch[fecing];
$fecing1 = fecha($fetch[fecing]);

$anio = date('Y')."-01-01";

if($fecing<=$anio)
        $inicio = $anio;
elseif($fecing>$anio)
        $inicio = $fecing;

$consulta="select timestampdiff(month,'$inicio',curdate()) as meses";
$resultado1=sql_ejecutar($consulta);
$fetch=fetch_array($resultado1);
$meses = $fetch[meses];

//$horas = $meses * 12;

//$horas=15*8;
$consulta="SELECT SUM(tiempo) AS tiempo, SUM(dias) AS dias, SUM(horas) AS horas, SUM(minutos) AS minutos "
        . "FROM dias_incapacidad "
        . "WHERE cedula='$_GET[cedula]' AND tipo_justificacion=5";// and tipo_tiporegistro in (1,2)
$resultado2=sql_ejecutar($consulta);
$fetch=fetch_array($resultado2);

$tiempo_disponible=$fetch['tiempo'];
$dias_disponible=$fetch['dias'];
$horas_disponible=$fetch['horas'];
$minutos_disponible=$fetch['minutos'];

/*$consulta="SELECT SUM(tiempo) AS tiempo, SUM(dias) AS dias, SUM(horas) AS horas, SUM(minutos) AS minutos "
        . "FROM dias_incapacidad "
        . "WHERE cedula='$_GET[cedula]' AND tipo_justificacion=5 AND tiempo <0";// and tipo_tiporegistro in (1,2)
$resultado3=sql_ejecutar($consulta);
$fetch=fetch_array($resultado3);*/

$tiempo_registrado=$fetch1['tiempo'];
$dias_registrado=$fetch1['dias'];
$horas_registrado=$fetch1['horas'];
$minutos_registrado=$fetch1['minutos'];

$consulta="SELECT SUM(tiempo) AS tiempo, SUM(dias) AS dias, SUM(horas) AS horas, SUM(minutos) AS minutos "
        . "FROM dias_incapacidad "
        . "WHERE cedula='$_GET[cedula]' AND tipo_justificacion=5 AND tiempo >0";// and tipo_tiporegistro in (1,2)
$resultado4=sql_ejecutar($consulta);
$fetch=fetch_array($resultado4);

$tiempo_acumulado=$fetch['tiempo'];
$dias_acumulado=$fetch['dias'];
$horas_acumulado=$fetch['horas'];
$minutos_acumulado=$fetch['minutos'];

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
    <legend >Permiso</legend>
<div class="form-group">
    <label class="col-md-2 control-label" for="txtcodigo">Tipo registro:</label>
    <div class="col-md-7">                               
            <div  id="tipo_tipo">
             <SELECT name="tipo_tiporegistro" class="form-control" id="tipo_tiporegistro">
            <option value="">Seleccione</option>
            <?php 
               
                while($fila=fetch_array($resultado))
                {
                    
                    if(!$fetch33['subtipo'])
                    {?>
                        <option  value="<?=$fila['id_expediente_subtipo'];?>"><?=utf8_encode($fila['nombre_subtipo']);?></option>
                    <?}
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

<div class="form-group">                           
<!--    <label class="col-md-2 control-label" for="txtcodigo">Fecha Inicio (Institución):</label>                            
    <div class="col-md-2">                                
       <div style="color:green;"><?php echo $fecing1?></div> 
                                <span style="color:green;"><strong><?php echo $fecing1?></strong></span>
    </div>-->

    <label class="col-md-2 control-label" for="txtcodigo">Acumulado: </label>  
     <div class="col-md-1">
       <span style="color:blue;"><?php echo $tiempo_acumulado?></span> (Horas)
        
    </div>
    <div class="col-md-2">
        <input size="5" type="text" name="tiempo" class="form-control" readonly id="tiempo" value="<? echo $dias_acumulado.' / '.$horas_acumulado.' / '.$minutos_acumulado;?>">

    </div>
    <div class="col-md-2">
        (Dias/Horas/Minutos)                                
    </div>

</div>
    
<div class="form-group">                           
<!--    <label class="col-md-2 control-label" for="txtcodigo">Fecha Inicio (Institución):</label>                            
    <div class="col-md-2">                                
       <div style="color:green;"><?php echo $fecing1?></div> 
                                <span style="color:green;"><strong><?php echo $fecing1?></strong></span>
    </div>-->

    <label class="col-md-2 control-label" for="txtcodigo">Solicitado: </label>  
     <div class="col-md-1">
       <span style="color:red;"><?php echo $tiempo_registrado?></span> (Horas)
        
    </div>
    <div class="col-md-2">
        <input size="5" type="text" name="tiempo" class="form-control" readonly id="tiempo" value="<? echo $dias_registrado.' / '.$horas_registrado.' / '.$minutos_registrado;?>">

    </div>
    <div class="col-md-2">
        (Dias/Horas/Minutos)                                
    </div>

</div>
    

<div class="form-group">                           
    <label class="col-md-2 control-label" for="txtcodigo">Disponible:</label>  
    <div class="col-md-1">
       <span style="color:green;"><?php echo $tiempo_disponible?></span> (Horas)
        
    </div>
    <div class="col-md-2">
        <input size="5" type="text" name="tiempo" class="form-control" readonly id="tiempo" value="<? echo $dias_disponible.' / '.$horas_disponible.' / '.$minutos_disponible;?>">

    </div>
    <div class="col-md-2">
        (Dias/Horas/Minutos)                                
    </div>
</div>


<div class="form-group">
    <label class="col-md-2 control-label" for="txtcodigo">Desde Hora(s):</label>
    <div class="col-md-3"><input type="text" class="form-control" placeholder="(00:00 am/pm)" size="7" name="desde" id="desde" onclick="javascript:inicio();"  <? if (isset($fetch33['desde'])) echo "value='$fetch33[desde]'"?>/></div>

    <label class="col-md-1 control-label" for="txtcodigo">Fecha Inicio:</label>
    <div class="col-md-3">
        <div class="input-group date date-picker" data-provide="datepicker" data-date-format="dd/mm/yyyy">
            <input size="10" type="text" name="fecha_inicio"  class="form-control" onchange="calcular_duracion_permiso();" placeholder="(dd/mm/aaaa)" id="fecha_inicio" value="<?if(isset($fetch33['fecha_inicio'])) echo fecha($fetch33['fecha_inicio']);?>">
            <span class="input-group-btn">
                <button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
            </span>
        </div>
    </div>
</div>

<div class="form-group">
    <label class="col-md-2 control-label" for="txtcodigo">Hasta Hora(s): </label>
    <div class="col-md-3">
        <input type="text" size="7" class="form-control" placeholder="(00:00 am/pm)" name="hasta" id="hasta" onclick="javascript:inicio();"  <? if (isset($fetch33['hasta'])) echo "value='$fetch33[hasta]'"?>/>
    </div>

    <label class="col-md-1 control-label" for="txtcodigo">Fecha Fin: </label>
    <div class="col-md-3">
        <div class="input-group date date-picker" data-provide="datepicker" data-date-format="dd/mm/yyyy">
            <input size="10" type="text" class="form-control" placeholder="(dd/mm/aaaa)" name="fecha_fin" id="fecha_fin"  onchange="calcular_duracion_permiso();" value="<?if(isset($fetch33['fecha_fin'])) echo fecha($fetch33['fecha_fin']);?>">
            <span class="input-group-btn">
                <button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
            </span>    
        </div>
    </div>
</div>

<div class="form-group">
    <label class="col-md-2 control-label" for="txtcodigo">Duraci&oacute;n:</label>
    <div class="col-md-1">
        <input class="form-control" type="text" size="5" name="dias" id="dias" maxlength="5" <? if (isset($fetch33['dias'])) echo "value='$fetch33[dias]'"?>/> Dias
    </div>
    <div class="col-md-1">
        <input type="text" class="form-control" size="5" name="horas" id="horas" maxlength="5" <? if (isset($fetch33['horas'])) echo "value='$fetch33[horas]'"?>/> Horas
    </div>
    <div class="col-md-1">
        <input type="text" size="5" class="form-control" name="minutos" id="minutos" maxlength="5" <? if (isset($fetch33['minutos'])) echo "value='$fetch33[minutos]'"?>/> Minutos
    </div>
</div>
<div class="form-group">
<!--    <label class="col-md-2 control-label" for="txtcodigo">Fecha Aprobado:</label>
    <div class="col-md-3">
        <div class="input-group date date-picker" data-provide="datepicker" data-date-format="dd/mm/yyyy">
            <input size="10" class="form-control" placeholder="(dd/mm/aaaa)" type="text" name="fecha_aprobado" id="fecha_aprobado" value="<?if(isset($fetch33['fecha_aprobado'])) echo fecha($fetch33['fecha_aprobado']);?>">
            <span class="input-group-btn">
                <button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
            </span> 
        </div>
    </div>-->

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
    <label class="col-md-2 control-label" for="txtcodigo">Numero Certificado:</label>
    <div class="col-md-3">
         <input type="text" name="numero_resolucion" class="form-control" id="numero_resolucion" <? if (isset($fetch33['numero_resolucion'])) echo "value='$fetch33[numero_resolucion]'"?> size="30"/> 

    </div>

    <label class="col-md-1 control-label" for="txtcodigo">Fecha Certificado:</label>
    <div class="col-md-3">
        <div class="input-group date date-picker" data-provide="datepicker" data-date-format="dd/mm/yyyy">
            <input size="10" type="text" name="fecha_resolucion" id="fecha_resolucion" class="form-control" placeholder="(dd/mm/aaaa)" value="<?if(isset($fetch33['fecha_resolucion'])) echo fecha($fetch33['fecha_resolucion']);?>">
            <span class="input-group-btn">
                <button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
            </span>    
        </div>
    </div>
</div>



    
<div class="form-group">
    <label class="col-md-2 control-label" for="txtcodigo">Centro Médico:</label>    
    <div class="col-md-7">
        <input type="text" class="form-control" size="100" name="id_centro" id="id_centro"  maxlength="100" <? if (isset($fetch33['id_centro'])) echo "value='$fetch33[id_centro]'"?>/>
    </div>
</div>

<!--<div class="form-group">
    <label class="col-md-2 control-label" for="txtcodigo">Nº Certificado:</label>    
    <div class="col-md-7">
        <input type="text" class="form-control" size="100" name="num_certificado" id="num_certificado" maxlength="100" <? if (isset($fetch33['num_certificado'])) echo "value='$fetch33[num_certificado]'"?>/>
    </div>
</div>-->
    
<div class="form-group">
    <label class="col-md-2 control-label" for="txtcodigo">Nombre Médico:</label>    
    <div class="col-md-7">
        <input type="text" class="form-control" size="100" name="nombre_medico" id="nombre_medico"  maxlength="100" <? if (isset($fetch33['nombre_medico'])) echo "value='$fetch33[nombre_medico]'"?>/>
    </div>
</div>     

<div class="form-group">
    <label class="col-md-2 control-label" for="txtcodigo">Proyecto Asociado: </label>
    <div class="col-md-7">                               
            
            <select name="proyecto" class="form-control" id="proyecto">
                <option value="">Seleccione</option>
                <?php                     
                     $consulta_proyecto="SELECT idProyecto, numProyecto, descripcionLarga FROM proyectos";
                     $resultado_proyecto=sql_ejecutar($consulta_proyecto);
                    while($fila_proyecto=fetch_array($resultado_proyecto))
                    {
                        //echo "AQUI";
                        if ($codigo=='')
                        {
                            if(!$fetch2['proyecto'])
                            {?>
                                <option  value="<?=$fila_proyecto['idProyecto'];?>"><?=utf8_encode($fila_proyecto['numProyecto']." - ".$fila_proyecto['descripcionLarga']);?></option>
                            <?}
                            else
                            { 
                               if($fetch2['proyecto']==$fila_proyecto['idProyecto'])
                               {?>
                                    <option  value="<?=$fila_proyecto['idProyecto'];?>" selected><?=utf8_encode($fila_proyecto['numProyecto']." - ".$fila_proyecto['descripcionLarga']);?></option> 
                               <?}
                               else
                               {?>
                                    <option  value="<?=$fila_proyecto['idProyecto'];?>"><?=utf8_encode($fila_proyecto['numProyecto']." - ".$fila_proyecto['descripcionLarga']);?></option>
                               <?}
                            }
                        }
                        else
                        {
                            if($fetch33['proyecto']==$fila_proyecto['idProyecto'])
                            {?>
                                 <option  value="<?=$fila_proyecto['idProyecto'];?>" selected><?=utf8_encode($fila_proyecto['numProyecto']." - ".$fila_proyecto['descripcionLarga']);?></option> 
                            <?}
                            else
                            {?>
                                 <option  value="<?=$fila_proyecto['idProyecto'];?>"><?=utf8_encode($fila_proyecto['numProyecto']." - ".$fila_proyecto['descripcionLarga']);?></option>
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
 
<input type="hidden" name="dispo" id="dispo" value="<?php echo $horass?>">			
<?

