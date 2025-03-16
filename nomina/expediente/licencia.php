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

//$horas_totales = $meses * 12;

$horas_totales = 120;

//echo "horas totales: "; echo $horas_totales;

$consulta="SELECT sum(dias)as dias,sum(horas) as horas,sum(minutos) as minutos FROM expediente where cedula='$_GET[cedula]' "
        . "and fecha BETWEEN '".date('Y')."-01-01' and '".date('Y')."-12-31' AND tipo IN (15,16,17)";
$resultado2=sql_ejecutar($consulta);
$fetch=fetch_array($resultado2);


if(!$fetch['dias'])
    $dias=0;
else
    $dias=$fetch['dias'];
    
if(!$fetch['horas'])
    $horas=0;
else 
    $horas=$fetch['horas'];

if(!$fetch['minutos'])
    $minutos=0;
else
    $minutos=$fetch['minutos'];

$horas_disponibles = $horas_totales - $horas;



?>

<div class="form-group">
    <label class="col-md-2 control-label" for="txtcodigo">Tipo registro:</label>
    <div class="col-md-7">                               
            <div  id="tipo_tipo">
            <SELECT name="tipo_tiporegistro" class="form-control" id="tipo_tiporegistro" onchange="mostrar_especial();">
            <option value="">Seleccione</option>
            <?php                        
                   
                while($fila=fetch_array($resultado))
                {
                    //echo "AQUI";
                    if ($codigo=='')
                    {                        
                        {?>
                            <option  value="<?=$fila['id_expediente_subtipo'];?>"><?=utf8_encode($fila['nombre_subtipo']);?></option>
                        <?}
                        
                    }
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

<div name="especiales" id="especiales" style="display: none;">
    <div class="form-group">         
        <label class="col-sm-2 control-label">Licencia:  </label>
        <div class="col-md-6">
            <div class="radio-list">
                <label class="radio-inline">
                    <input type="radio" name="licencia_sueldo" id="licencia_con_sueldo" value="2" <?php echo $licencia_con_sueldo; ?>> Con Sueldo</label>
                <label class="radio-inline">
                <input type="radio" name="licencia_sueldo" id="licencia_sin_sueldo" value="1" <?php echo $licencia_sin_sueldo; ?>> Sin Sueldo</label>
                
            </div>
        </div> 
    </div> 
    <div class="form-group"> 
        
        <label class="col-sm-2 control-label">Tipo Enfermedad:  </label>
        <div class="col-md-6">
            <div class="radio-list">
                <label class="radio-inline">
                    <input type="radio" name="licencia_enfermedad" id="inicial" value="1" <?php echo $inicial; ?>> Inicial</label>
                <label class="radio-inline">
                <input type="radio" name="licencia_enfermedad" id="recaida" value="2" <?php echo $recaida; ?>> Recaida</label>
                <label class="radio-inline">
                <input type="radio" name="licencia_enfermedad" id="continuacion" value="3" <?php echo $continuacion; ?>> Continuación</label>
            </div>
        </div> 
    </div> 
</div>
<?php
    echo"
        <script type='text/javascript'>
            alert('Entro');
            mostrar_especial();
        </script>
        "; 
?>

<div class="form-group">  
     <label for="txtcodigo" class="col-md-2 control-label">Número Secuencial: </label>                                    
    <div class="col-md-7">
        <input class="form-control" type="text" name="numero_secuencial" id="numero_secuencial" readonly="true"  value="" /> 
    </div>
   
</div>

<div class="form-group">                           
    <label class="col-md-2 control-label" for="txtcodigo">Fecha Inicio (Institución):</label>                            
    <div class="col-md-2">                                
       <div style="color:green;"><?php echo $fecing1?></div> 
<!--                                <span style="color:green;"><strong><?php echo $fecing1?></strong></span>-->
    </div>

    <label class="col-md-2 control-label" for="txtcodigo">Registrado ( <?php echo date('Y');?> ): </label>  

    <div class="col-md-2">
        <input size="7" type="text" name="tiempo" class="form-control" readonly id="tiempo" value="<? echo $dias.' / '.$horas.' / '.$minutos;?>">

    </div>
    <div class="col-md-2">
        (Dias/Horas/Minutos)                                
    </div>

</div>

<div class="form-group">                           
    <label class="col-md-2 control-label" for="txtcodigo">Disponible:</label>  
    <div class="col-md-4">
        Dias: <span style="color:green;"><strong><?php echo $horas_disponibles/8?></strong></span> 
        Horas: <span style="color:green;"><?php echo $horas_disponibles?></span>     
        
    </div>
</div>

<div class="form-group">
    <label class="col-md-2 control-label" for="txtcodigo">Desde Hora(s):</label>
    <div class="col-md-3"><input type="text" class="form-control" placeholder="(00:00 am/pm)" size="7" name="desde" id="desde" onclick="javascript:inicio();"  <? if (isset($fetch33['desde'])) echo "value='$fetch33[desde]'"?>/></div>

    <label class="col-md-1 control-label" for="txtcodigo">Fecha Inicio:</label>
    <div class="col-md-3">
        <div class="input-group date date-picker" data-provide="datepicker" data-date-format="dd/mm/yyyy">
            <input size="10" type="text" onchange="calcular_duracion_licencia();" name="fecha_inicio" class="form-control" placeholder="(dd/mm/aaaa)" id="fecha_inicio" value="<?if(isset($fetch33['fecha_inicio'])) echo fecha($fetch33['fecha_inicio']);?>">
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
            <input size="10" type="text" onchange="calcular_duracion_licencia();" class="form-control" placeholder="(dd/mm/aaaa)" name="fecha_fin" id="fecha_fin"  value="<?if(isset($fetch33['fecha_fin'])) echo fecha($fetch33['fecha_fin']);?>">
            <span class="input-group-btn">
                <button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
            </span>    
        </div>
    </div>
</div>

<div class="form-group">
    <label class="col-md-2 control-label" for="txtcodigo">Duraci&oacute;n:</label>
    <div class="col-md-1">
        <input class="form-control" readonly="readonly"type="text" size="3" name="anios" id="anios" maxlength="2" <? if (isset($fetch33['anios'])) echo "value='$fetch33[anios]'"?>/> Años
    </div>
    <div class="col-md-1">
        <input class="form-control" readonly="readonly" type="text" size="3" name="meses" id="meses" maxlength="2" <? if (isset($fetch33['meses'])) echo "value='$fetch33[meses]'"?>/> Meses
    </div>
    <div class="col-md-1">
        <input class="form-control" type="text" readonly="readonly" size="3" name="dias" id="dias" maxlength="2" <? if (isset($fetch33['dias'])) echo "value='$fetch33[dias]'"?>/> Dias
    </div>
    <div class="col-md-1" style="display: none;">
        <input type="text" readonly="readonly" class="form-control" size="3" name="horas" id="horas" maxlength="2" <? if (isset($fetch33['horas'])) echo "value='$fetch33[horas]'"?>/> Horas
    </div>
    <div class="col-md-2" style="display: none;">
        <input type="text" size="5" readonly="readonly" class="form-control" name="minutos" id="minutos" maxlength="2" <? if (isset($fetch33['minutos'])) echo "value='$fetch33[minutos]'"?>/> Minutos
    </div>
</div>

<div class="form-group">
    <label class="col-md-2 control-label" for="txtcodigo">Numero de Resolucion:</label>
    <div class="col-md-3">
         <input type="text" name="numero_resolucion" class="form-control" id="numero_resolucion" <? if (isset($fetch33['numero_resolucion'])) echo "value='$fetch33[numero_resolucion]'"?> size="30"/> 

    </div>

    <label class="col-md-1 control-label" for="txtcodigo">Fecha Resolución:</label>
    <div class="col-md-3">
        <div class="input-group date date-picker" data-provide="datepicker" data-date-format="dd/mm/yyyy">
            <input size="10" type="text" name="fecha_resolucion" id="fecha_resolucion" class="form-control"  placeholder="(dd/mm/aaaa)" value="<?if(isset($fetch33['fecha_resolucion'])) echo fecha($fetch33['fecha_resolucion']);?>">
            <span class="input-group-btn">
                <button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
            </span>    
        </div>
    </div>
</div>


<div class="form-group">
    <label class="col-md-2 control-label" for="txtcodigo">Fecha Aprobado:</label>
    <div class="col-md-3">
        <div class="input-group date date-picker" data-provide="datepicker" data-date-format="dd/mm/yyyy">
            <input size="10" class="form-control" placeholder="(dd/mm/aaaa)" type="text" name="fecha_aprobado" id="fecha_aprobado"  value="<?if(isset($fetch33['fecha_aprobado'])) echo fecha($fetch33['fecha_aprobado']);?>">
            <span class="input-group-btn">
                <button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
            </span> 
        </div>
    </div>

    <label class="col-md-1 control-label" for="txtcodigo">Fecha Enterado:</label>
    <div class="col-md-3">
        <div class="input-group date date-picker" data-provide="datepicker" data-date-format="dd/mm/yyyy">
            <input size="10" type="text" class="form-control" placeholder="(dd/mm/aaaa)" name="fecha_enterado" id="fecha_enterado"  value="<?if(isset($fetch33['fecha_enterado'])) echo fecha($fetch33['fecha_enterado']);?>">
            <span class="input-group-btn">
                <button class="btn default" type="button" style="height: 34px;"><i class="fa fa-calendar"></i></button>
            </span> 
        </div>
    </div>
</div>

<div class="form-group">

        <label class="col-md-2 control-label" for="txtcodigo">Observaciones: </label>                               
       <div class="col-md-7">
            <textarea name="descripcion" id="descripcion" class="form-control" rows="3" style="resize:vertical;"><?php if (isset($fetch33['descripcion'])) echo "$fetch33[descripcion]"?></textarea>
       </div>
   </div>    

<input type="hidden" name="disponible" id="disponible" value="<?php echo $horas_disponibles?>">						
<?
