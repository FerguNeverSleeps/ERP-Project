<?php
//ESTUDIOS ACADEMICOS - EDITAR
if ($_POST['tipo_registro']=="1")
{
        if($_POST['fecha_inicio']!='')
                    $fecha_inicio=fecha_sql($_POST['fecha_inicio']);				
            if($_POST['fecha_culminacion']!='')
                    $fecha_culminacion=fecha_sql($_POST['fecha_culminacion']);
        $fecha = date("Y-m-d");
        $fecha_modificacion = date("Y-m-d");

         //ACTUALIZACION EXPEDIENTE       
        
        $consulta = "UPDATE expediente SET "
                . "descripcion = '{$_POST['descripcion']}',institucion_educativa_nueva = '{$_POST['institucion_educativa_nueva']}',titulo_profesional = '{$_POST['titulo_profesional']}',"
                . "idoneidad = '{$_POST['idoneidad']}',ejerce = '{$_POST['ejerce']}',fecha = '{$fecha}',fecha_inicio = '{$fecha_inicio}',fecha_fin = '{$fecha_culminacion}',dias = '{$_POST['duracion']}',"
                . "subtipo = '{$_POST['tipo_tiporegistro']}',fecha_modificacion = '{$fecha_modificacion}',usuario_modificacion = '{$usuario}'"
                . " WHERE cod_expediente_det='{$_POST['codigo']}'";
        $resultado=query($consulta,$conexion);
        
}

//MOVIMIENTO DE PERSONAL - EDITAR
if ($_POST['tipo_registro']=="9")
{
        if($_POST['fecha_resolucion']!='')
                    $fecha_resolucion=fecha_sql($_POST['fecha_resolucion']);
        if($_POST['fecha_inicio']!='')
            $fecha_inicio=fecha_sql($_POST['fecha_inicio']);
        if($_POST['fecha_fin']!='')
            $fecha_fin=fecha_sql($_POST['fecha_fin']);
        
        $fecha_modificacion = date("Y-m-d");

         //ACTUALIZACION EXPEDIENTE       
        
        $consulta = "UPDATE expediente SET "
                . "descripcion = '{$_POST['descripcion']}',gerencia_nueva = '{$_POST['gerencia_nueva']}',departamento_nuevo = '{$_POST['departamento_nuevo']}',"
                . "seccion_nueva = '{$_POST['seccion_nueva']}',cod_cargo_nuevo = '{$_POST['cargo_nuevo']}',funcion_nueva = '{$_POST['funcion_nueva']}',institucion_nueva = '{$_POST['institucion_nueva']}',"
                . "numero_resolucion = '{$_POST['numero_resolucion']}',fecha_resolucion = '{$fecha_resolucion}',fecha_inicio = '{$fecha_inicio}',"
                . "fecha_fin = '{$fecha_fin}',motivo_traslado = '{$_POST['motivo_traslado']}',"
                . "subtipo = '{$_POST['tipo_tiporegistro']}',fecha_modificacion = '{$fecha_modificacion}',usuario_modificacion = '{$usuario}'"
                . " WHERE cod_expediente_det='{$_POST['codigo']}'";
        $resultado=query($consulta,$conexion);
        
}

//VACACIONES - EDITAR
if ($_POST['tipo_registro']=="11")
{
       if($_POST['fecha_inicio_periodo']!='')
                    $fecha_inicio_periodo=fecha_sql($_POST['fecha_inicio_periodo']);
            else
                $fecha_inicio_periodo="0000-00-00";

            if($_POST['fecha_fin_periodo']!='')
                    $fecha_fin_periodo=fecha_sql($_POST['fecha_fin_periodo']);
             else
                $fecha_fin_periodo="0000-00-00";

            if($_POST['fecha_inicio']!='')
                    $fecha_inicio=fecha_sql($_POST['fecha_inicio']);
             else
                $fecha_inicio="0000-00-00";

            if($_POST['fecha_fin']!='')
                    $fecha_fin=fecha_sql($_POST['fecha_fin']);
             else
                $fecha_fin="0000-00-00";

            if($_POST['fecha_resolucion']!='')
                    $fecha_resolucion=fecha_sql($_POST['fecha_resolucion']);
             else
                $fecha_resolucion="0000-00-00";
            
            if($_POST['periodo_vacacion']!='')
                    $periodo_vacacion=$_POST['periodo_vacacion'];
            else
                $periodo_vacacion=0;
            
            if($_POST['numero_resolucion']!='')
                    $numero_resolucion=$_POST['numero_resolucion'];
            else
                $numero_resolucion=0;
        
        $fecha = date("Y-m-d");
        $fecha_modificacion = date("Y-m-d");

         //ACTUALIZACION EXPEDIENTE       
        
        $consulta = "UPDATE expediente SET "
                . "descripcion = '{$_POST['descripcion']}',fecha_inicio_periodo='{$fecha_inicio_periodo}',fecha_fin_periodo = '{$fecha_fin_periodo}',"
                . "fecha_inicio='{$fecha_inicio}',fecha_fin = '{$fecha_fin}',numero_resolucion = '{$numero_resolucion}', dias = '{$_POST['dias']}',"
                . "restante = '{$_POST['restante']}',periodo_vacacion = '{$periodo_vacacion}', fecha = '{$fecha}',"
                . "fecha_resolucion = '{$fecha_resolucion}',subtipo = '{$_POST['tipo_tiporegistro']}',fecha_modificacion = '{$fecha_modificacion}',usuario_modificacion = '{$usuario}'"
                . " WHERE cod_expediente_det='{$_POST['codigo']}'";
        $resultado=query($consulta,$conexion);
        
}

//DOCUMENTO - EDITAR
if($_POST['tipo_registro']=="13")
{
        $nombre_documento  = (isset($_POST['nombre_documento']))  ? $_POST['nombre_documento']  : NULL;
        $fecha_vencimiento = str_replace('-', '/', $_POST['fecha_vencimiento']);
        $fecha_vencimiento = fecha_sql($_POST['fecha_vencimiento']);

        // Registrar en nomexpediente
        $consulta = "UPDATE expediente SET
                            `tipo_tiporegistro` = '{$nombre_documento}'
                             WHERE cod_expediente_det='{$_POST['codigo']}'";

        $resultado=query($consulta,$conexion);

        $consulta = "UPDATE `expediente_documentos` SET
                                 `nombre_documento`  = '{$nombre_documento}',
                                 `descripcion`       = '{$_POST['descripcion']}',
                                 `fecha_vencimiento` = '{$fecha_vencimiento}'
                                 WHERE cod_expediente_det='{$_POST['codigo']}'";
        $resultado=query($consulta,$conexion);
}

//LICENCIAS - EDITAR
else if ($_POST['tipo_registro']=="15" || $_POST['tipo_registro']=="16" || $_POST['tipo_registro']=="17")
{
        if($_POST['fecha_inicio']!='')
                $fecha_inicio=fecha_sql($_POST['fecha_inicio']);

        if($_POST['fecha_fin']!='')
                $fecha_fin=fecha_sql($_POST['fecha_fin']);

        if($_POST['fecha_aprobado']!='')
                $fecha_aprobado=fecha_sql($_POST['fecha_aprobado']);

        if($_POST['fecha_enterado']!='')
                $fecha_enterado=fecha_sql($_POST['fecha_enterado']);	

        if($_POST['fecha_resolucion']!='')
                $fecha_resolucion=fecha_sql($_POST['fecha_resolucion']);
        
        if($_POST['meses']=='')
                $meses=0;
         else
             $meses=$_POST['meses'];

         if($_POST['dias']=='')
                $dias=0;
         else
             $dias=$_POST['dias'];

         if($_POST['horas']=='')
                $horas=0;
         else
                $horas=$_POST['horas'];

         if($_POST['minutos']=='')
                $minutos=0;
         else
                $minutos=$_POST['minutos'];

        $duracion = $dias*8 + $horas +($minutos/60);

        $fecha_hoy = date('Y-m-d');

         //ACTUALIZACION EXPEDIENTE       
        
        $consulta = "UPDATE expediente SET "
                . "descripcion = '{$_POST['descripcion']}', desde = '{$_POST['desde']}',fecha_inicio='{$fecha_inicio}', hasta = '{$_POST['hasta']}',"
                . "fecha_fin = '{$fecha_fin}', meses = '{$meses}', dias = '{$dias}', horas = '{$horas}', minutos = '{$minutos}', duracion = '{$duracion}',"
                . "restante = '{$_POST['disponible']}', numero_resolucion = '{$_POST['numero_resolucion']}', fecha = '{$fecha_resolucion}',"
                . "fecha_resolucion = '{$fecha_resolucion}', fecha_aprobado = '{$fecha_aprobado}', fecha_enterado = '{$fecha_enterado}',"     
                . "tipo = '{$_POST['tipo_registro']}', fecha_labor_permanente = '{$fecha_labor_permanente}',"                                   
                . "registro = '{$_POST['registro']}', subtipo = '{$_POST['tipo_tiporegistro']}'"
                . "WHERE cod_expediente_det='{$_POST['codigo']}'";
        $resultado=query($consulta,$conexion);

        //ACTUALIZACION LICENCIA
        $ultimo_id = mysqli_insert_id($conexion);

        
        $consulta2 = "UPDATE licencias SET "
                . "usr_uid = '{$user_uid}', tipo_licencia = '{$_POST['tipo_registro']}',nro_resolucion ='{$_POST['numero_resolucion']}',"
                . "fecha_resolucion = '{$fecha}',fecha_desde = '{$fecha_inicio}', fecha_hasta = '{$fecha_fin}',"
                . "motivo = '{$_POST['descripcion']}'"  
                . "WHERE cod_expediente_det='{$_POST['codigo']}'";
        $resultado2=query($consulta2,$conexion);  
}

//BAJA - EDITAR
else if ($_POST['tipo_registro']=="19")
{
    if($_POST['fecha_resolucion']!='')
            $fecha_resolucion=fecha_sql($_POST['fecha_resolucion']);		

    $fecha = date('Y-m-d');

     //ACTUALIZACION EXPEDIENTE
    $consulta = "UPDATE expediente SET "
        . "descripcion = '{$_POST['descripcion']}', fecha = '{$fecha}',fecha_resolucion='{$fecha_resolucion}',"
        . "numero_resolucion = '{$_POST['numero_resolucion']}'"
        . "WHERE cod_expediente_det='{$_POST['codigo']}'";
    $resultado=query($consulta,$conexion);  
}

//ANALISIS CAMBIO CATEGORIA/GREMIO - EDITAR
else if ($_POST['tipo_registro']=="21")
{
    if($_POST['fecha_idoneidad']!='')
            $fecha_idoneidad=fecha_sql($_POST['fecha_idoneidad']);	

    if($_POST['fecha_ini_labor_otra_inst']!='')
            $fecha_ini_labor_otra_inst=fecha_sql($_POST['fecha_ini_labor_otra_inst']);

     if($_POST['fecha_fin_labor_otra_inst']!='')
            $fecha_fin_labor_otra_inst=fecha_sql($_POST['fecha_fin_labor_otra_inst']);                    

    if($_POST['fecha_ini_labor_contrato']!='')
            $fecha_ini_labor_contrato=fecha_sql($_POST['fecha_ini_labor_contrato']);

     if($_POST['fecha_fin_labor_contrato']!='')
            $fecha_fin_labor_contrato=fecha_sql($_POST['fecha_fin_labor_contrato']);

    if($_POST['fecha_labor_permanente']!='')
            $fecha_labor_permanente=fecha_sql($_POST['fecha_labor_permanente']);

    if($_POST['fecha_inicio_lab']!='')
            $fecha_inicio_lab=fecha_sql($_POST['fecha_inicio_lab']);

    if($_POST['fecha_bienal']!='')
            $fecha_bienal=fecha_sql($_POST['fecha_bienal']);

    $fecha = date('Y-m-d');

    $sobresueldo_jefatura = str_replace(",","",$_POST['sobresueldo_jefatura']); 
    $sobresueldo_exclusividad = str_replace(",","",$_POST['sobresueldo_exclusividad']);  
    $sobresueldo_altoriesgo = str_replace(",","",$_POST['sobresueldo_altoriesgo']);
    $sobresueldo_especialidad = str_replace(",","",$_POST['sobresueldo_especialidad']);

     //ACTUALIZACION EXPEDIENTE

    $consulta = "UPDATE expediente SET "
                . "sobresueldo_jefatura = '{$sobresueldo_jefatura}', sobresueldo_exclusividad = '{$sobresueldo_exclusividad}',"
                . "sobresueldo_altoriesgo = '{$sobresueldo_altoriesgo}',sobresueldo_especialidad = '{$sobresueldo_especialidad}',"
                . "fecha_idoneidad = '{$fecha_idoneidad}', fecha_ini_labor_otra_inst = '{$fecha_ini_labor_otra_inst}',"
                . "fecha_fin_labor_otra_inst = '{$fecha_fin_labor_otra_inst}', fecha_ini_labor_contrato = '{$fecha_ini_labor_contrato}',"     
                . "fecha_fin_labor_contrato = '{$fecha_fin_labor_contrato}', fecha_labor_permanente = '{$fecha_labor_permanente}',"                                   
                . "registro = '{$_POST['registro']}', folio = '{$_POST['folio']}',descripcion = '{$_POST['descripcion']}'"
                . "WHERE cod_expediente_det='{$_POST['codigo']}'";
    $resultado=query($consulta,$conexion);

    if($resultado)
    {
        //ACTUALIZACION EXPEDIENTE ANALISIS
        $ultimo_id = mysqli_insert_id($conexion);

        $salario_analisis = str_replace(",","",$_POST['salario_analisis']); 
        $veinte_porciento = str_replace(",","",$_POST['veinte_porciento']);  
        $cuarenta_porciento = str_replace(",","",$_POST['cuarenta_porciento']);

        $consulta2 = "UPDATE expediente_analisis SET "
                . "fecha = '{$fecha_inicio_lab}', etapa = '{$_POST['etapa_analisis']}',"
                . "salario = '{$salario_analisis}',resuelto_1 = '{$_POST['resuelto_analisis_1']}',"
                . "veinte_porciento = '{$veinte_porciento}',cuarenta_porciento = '{$cuarenta_porciento}',"
                . "resuelto_2 = '{$_POST['resuelto_analisis_2']}'"    
                . "WHERE cod_expediente_det='{$_POST['codigo']}'";
        $resultado2=query($consulta2,$conexion);  


        //ACTUALIZACION EXPEDIENTE BIENAL

        $salario_bienal = str_replace(",","",$_POST['salario_bienal']); 
        $monto_mensual_bienal = str_replace(",","",$_POST['monto_mensual_bienal']);  
        $acumulativo_bienal = str_replace(",","",$_POST['acumulativo_bienal']);

         $consulta3 = "UPDATE expediente_bienal SET "
                . "fecha = '{$fecha_bienal}', numero = '{$_POST['numero_bienal']}',"
                . "salario = '{$salario_bienal}',resuelto = '{$_POST['resuelto_bienal']}',"
                . "monto_mensual = '{$monto_mensual_bienal}',acumulativo = '{$acumulativo_bienal}'"
                . "WHERE cod_expediente_det='{$_POST['codigo']}'";
        $resultado3=query($consulta3,$conexion);  

    }                 
}


 //ANALISIS CAMBIO ETAPA - EDITAR
else if ($_POST['tipo_registro']=="22")
{                   
    $fecha = date('Y-m-d');
     if($_POST['fecha_inicio_lab']!='')
            $fecha_inicio_lab=fecha_sql($_POST['fecha_inicio_lab']);


    $salario_base = str_replace(",","",$_POST['salario_base']); 
    $ajuste = str_replace(",","",$_POST['ajuste']);  
    $ajuste_discrecional = str_replace(",","",$_POST['ajuste_discrecional']);
    $ajuste_salario_minimo = str_replace(",","",$_POST['ajuste_salario_minimo']); 
    $ajuste_otros = str_replace(",","",$_POST['ajuste_otros']); 
    $porcentaje = str_replace(",","",$_POST['porcentaje']);
    $salario_base_porcentaje = str_replace(",","",$_POST['salario_base_porcentaje']);
    $acuerdo = str_replace(",","",$_POST['acuerdo']);

    //ACTUALIZACION EXPEDIENTE

    $consulta = "UPDATE expediente SET "
                . "salario_base = '{$salario_base}', ajuste = '{$ajuste}',ajuste_discrecional = '{$ajuste_discrecional}',ajuste_salario_minimo = '{$ajuste_salario_minimo}',ajuste_otros = '{$ajuste_otros}',"
                . "porcentaje = '{$porcentaje}',salario_base_porcentaje = '{$salario_base_porcentaje}',acuerdo = '{$acuerdo}',"
                . "cargo_funcion = '{$_POST['cargo_funcion']}', descripcion = '{$_POST['descripcion']}'"    
                . "WHERE cod_expediente_det='{$_POST['codigo']}'";
    $resultado=query($consulta,$conexion);                   


    if($resultado)
    {
        //ACTUALIZACION EXPEDIENTE ANALISIS

        $salario_analisis = str_replace(",","",$_POST['salario_analisis']);

        $consulta2 = "UPDATE expediente_analisis SET "
                . "fecha = '{$fecha_inicio_lab}', grado = '{$_POST['grado_analisis']}',"
                . "etapa = '{$_POST['etapa_analisis']}', salario = '{$salario_analisis}',"
                . "resuelto_1 = '{$_POST['resuelto_analisis_1']}'"    
                . "WHERE cod_expediente_det='{$_POST['codigo']}'";
        $resultado2=query($consulta2,$conexion);  

    }                 

}

//VIGENCIAS EXPIRADAS - EDITAR
else if ($_POST['tipo_registro']=="23")
{                   
    $fecha = date('Y-m-d');
    //ACTUALIZACION EXPEDIENTE

    $consulta = "UPDATE expediente SET "
            . "concepto = '{$_POST['concepto']}', analista = '{$_POST['analista']}',"
            . "auditor = '{$_POST['auditor']}', descripcion = '{$_POST['descripcion']}'"    
            . "WHERE cod_expediente_det='{$_POST['codigo']}'";
    $resultado=query($consulta,$conexion); 

    if($resultado)
    {
        //ACTUALIZACION EXPEDIENTE VIGENCIAS            
        $salario_devengado = str_replace(",","",$_POST['salario_devengado']); 
        $salario_devengar = str_replace(",","",$_POST['salario_devengar']);  
        $diferencia = str_replace(",","",$_POST['diferencia']);
        $monto_pagar = str_replace(",","",$_POST['monto_pagar']);

        $consulta2 = "UPDATE expediente_vigencia SET "
            . "periodo_adeudado = '{$_POST['periodo_adeudado']}', salario_devengado = '{$salario_devengado}',"
            . "categoria_1 = '{$_POST['categoria_1']}', salario_devengar = '{$salario_devengar}',"
            . "categoria_2 = '{$_POST['categoria_2']}', diferencia = '{$diferencia}', "
            . "periodo_adeudado_amd = '{$_POST['periodo_adeudado_amd']}', monto_pagar = '{$monto_pagar}'"    
            . "WHERE cod_expediente_det='{$_POST['codigo']}'";
        $resultado2=query($consulta2,$conexion);                                  

    }                 

}

//AJUSTE DE TIEMPO - EDITAR
else if ($_POST['tipo_registro']=="27")
{                   
    if($_POST['fecha_inicio']!='')
        $fecha_inicio=fecha_sql($_POST['fecha_inicio']);

    if($_POST['fecha_fin']!='')
        $fecha_fin=fecha_sql($_POST['fecha_fin']);
    
    if($_POST['tipo_tiporegistro']==61)
    {
       $periodo_vacacion=$_POST['periodo_vacacion'];           
        if($_POST['fecha_inicio_periodo']!='')
                $fecha_inicio_periodo=fecha_sql($_POST['fecha_inicio_periodo']);

        if($_POST['fecha_fin_periodo']!='')
            $fecha_fin_periodo=fecha_sql($_POST['fecha_fin_periodo']);
    } 

    $fecha_modificacion = date("Y-m-d");        
        
    $tipo_ajuste=$_POST['tipo_ajuste'];
    $dias=$_POST['dias'];
    
     if($_POST['restantes']!='')
    {
        $restantes=$_POST['restantes'];
    }
    else
    {
        $restantes=0;
    }
    
    if($_POST['horas']!='')
    {
        $horas=$_POST['horas'];
    }
    else
    {
        $horas=0;
    }
    
    $tiempo=($dias*8)*60 + $horas*60;
    $tiempo=$tiempo/60;

    $fecha = date("Y-m-d");
   
    //ACTUALIZACION EXPEDIENTE

    $consulta = "UPDATE expediente SET "
            . "fecha_inicio = '{$fecha_inicio}', fecha_fin = '{$fecha_fin}',fecha_inicio_periodo = '{$fecha_inicio_periodo}', fecha_fin_periodo = '{$fecha_fin_periodo}',"
            . "tipo_ajuste = '{$tipo_ajuste}',periodo_vacacion = '{$periodo_vacacion}',dias = '{$dias}',restante = '{$restantes}',"
            . "horas = '{$horas}', duracion = '{$tiempo}', subtipo = '{$_POST['tipo_tiporegistro']}', "
            . "descripcion = '{$_POST['descripcion']}', usuario_modificacion ='{$usuario}',fecha_modificacion = '{$fecha_modificacion}'"    
            . "WHERE cod_expediente_det='{$_POST['codigo']}'";
    $resultado=query($consulta,$conexion); 
    
}

//MISION OFICIAL - EDITAR
    else if ($_POST['tipo_registro']=="28")
    {       

        if($_POST['fecha']!='')
                $fecha=fecha_sql($_POST['fecha']);

        $fecha_modificacion = date('Y-m-d');      
        
        //ACUTALIZACION EXPEDIENTE
        
        $consulta = "UPDATE expediente SET "
            . "fecha = '{$fecha}', descripcion = '{$_POST['descripcion']}',fecha_modificacion = '{$fecha_modificacion}',usuario_modificacion = '{$usuario}'"    
            . " WHERE cod_expediente_det='{$_POST['codigo']}'";
    $resultado=query($consulta,$conexion); 
                        

    }

//CERTIFICACION TRABAJO  - EDITAR
    else if ($_POST['tipo_registro']=="29")
    {       

        if($_POST['fecha']!='')
                $fecha=fecha_sql($_POST['fecha']);

        $fecha_modificacion = date('Y-m-d');      
        
        //ACUTALIZACION EXPEDIENTE
        
        $consulta = "UPDATE expediente SET "
            . "fecha = '{$fecha}', descripcion = '{$_POST['descripcion']}',fecha_modificacion = '{$fecha_modificacion}',usuario_modificacion = '{$usuario}'"    
            . " WHERE cod_expediente_det='{$_POST['codigo']}'";
    $resultado=query($consulta,$conexion); 
                        

    }

//ASCENSO  - EDITAR
    else if ($_POST['tipo_registro']=="30")
    {       

        if($_POST['fecha']!='')
                $fecha=fecha_sql($_POST['fecha']);

        $fecha_modificacion = date('Y-m-d');      
        
        //ACUTALIZACION EXPEDIENTE
        
        $consulta = "UPDATE expediente SET "
            . "fecha = '{$fecha}', descripcion = '{$_POST['descripcion']}',fecha_modificacion = '{$fecha_modificacion}',usuario_modificacion = '{$usuario}'"    
            . " WHERE cod_expediente_det='{$_POST['codigo']}'";
    $resultado=query($consulta,$conexion); 
                        

    }
    
//AUMENTO (AJUSTE SALARIAL)  - EDITAR
    else if ($_POST['tipo_registro']=="31")
    {       

        if($_POST['fecha']!='')
                $fecha=fecha_sql($_POST['fecha']);
        $monto_nuevo = str_replace(",","",$_POST['monto_nuevo']); 

        $fecha_modificacion = date('Y-m-d');      
        
        //ACUTALIZACION EXPEDIENTE
        
        $consulta = "UPDATE expediente SET "
            . "fecha = '{$fecha}', monto_nuevo = '{$monto_nuevo}', descripcion = '{$_POST['descripcion']}',fecha_modificacion = '{$fecha_modificacion}',usuario_modificacion = '{$usuario}'"    
            . " WHERE cod_expediente_det='{$_POST['codigo']}'";
    $resultado=query($consulta,$conexion); 
                        

    }    

//REVOCATORIA  - EDITAR
    else if ($_POST['tipo_registro']=="32")
    {       

        if($_POST['fecha']!='')
                $fecha=fecha_sql($_POST['fecha']);

        $fecha_modificacion = date('Y-m-d');      
        
        //ACUTALIZACION EXPEDIENTE
        
        $consulta = "UPDATE expediente SET "
            . "fecha = '{$fecha}', descripcion = '{$_POST['descripcion']}',fecha_modificacion = '{$fecha_modificacion}',usuario_modificacion = '{$usuario}'"    
            . " WHERE cod_expediente_det='{$_POST['codigo']}'";
    $resultado=query($consulta,$conexion); 
                        

    }    
    
//MODIFICACION DECRETO - EDITAR
    else if ($_POST['tipo_registro']=="33")
    {       

        if($_POST['fecha']!='')
                $fecha=fecha_sql($_POST['fecha']);

        $fecha_modificacion = date('Y-m-d');      
        
        //ACUTALIZACION EXPEDIENTE
        
        $consulta = "UPDATE expediente SET "
            . "fecha = '{$fecha}', descripcion = '{$_POST['descripcion']}',fecha_modificacion = '{$fecha_modificacion}',usuario_modificacion = '{$usuario}'"    
            . " WHERE cod_expediente_det='{$_POST['codigo']}'";
    $resultado=query($consulta,$conexion); 
                        

    }    
    
//SOBRESUELDO - EDITAR
    else if ($_POST['tipo_registro']=="34")
    {       

        if($_POST['fecha_inicio']!='')
                $fecha_inicio=fecha_sql($_POST['fecha_inicio']);

        $fecha = date('Y-m-d');
        $porcentaje_sobresueldo=str_replace(",","",$_POST['porcentaje_sobresueldo']);
        $sobresueldo=str_replace(",","",$_POST['sobresueldo']);
        
        $sobresueldo_antiguedad=$sobresueldo_altoriesgo=$sobresueldo_jefatura=$sobresueldo_especialidad=$sobresueldo_otros=$sobresueldo_gastos_representacion=NULL;
        
        if($_POST['tipo_tiporegistro']==65)
        {
            $sobresueldo_antiguedad = str_replace(",","",$_POST['sobresueldo']);
            //$sobresueldo_antiguedad = $_POST['sobresueldo'];
        }
        if($_POST['tipo_tiporegistro']==66)
        {
            $sobresueldo_altoriesgo = str_replace(",","",$_POST['sobresueldo']); 
            //$sobresueldo_altoriesgo = $_POST['sobresueldo'];
        }
        if($_POST['tipo_tiporegistro']==67)
        {
            $sobresueldo_jefatura = str_replace(",","",$_POST['sobresueldo']);
            //$sobresueldo_jefatura = $_POST['sobresueldo'];
        }
        if($_POST['tipo_tiporegistro']==68)
        {
            $sobresueldo_especialidad = str_replace(",","",$_POST['sobresueldo']);   
            //$sobresueldo_especialidad = $_POST['sobresueldo'];
        }  
        if($_POST['tipo_tiporegistro']==69)
        {
            $sobresueldo_otros = str_replace(",","",$_POST['sobresueldo']); 
            //$sobresueldo_otros = $_POST['sobresueldo'];
        }
        if($_POST['tipo_tiporegistro']==70)
        {
            $sobresueldo_gastos_representacion = str_replace(",","",$_POST['sobresueldo']); 
            //$sobresueldo_gastos_representacion = $_POST['sobresueldo'];
        }
        
        //ACUTALIZACION EXPEDIENTE
        
        $consulta = "UPDATE expediente SET "
            . "sobresueldo_jefatura = '{$sobresueldo_jefatura}', sobresueldo_exclusividad = '{$sobresueldo_exclusividad}',"
            . "sobresueldo_altoriesgo = '{$sobresueldo_altoriesgo}', sobresueldo_especialidad = '{$sobresueldo_especialidad}',"
            . "sobresueldo_antiguedad = '{$sobresueldo_antiguedad}', sobresueldo_otros = '{$sobresueldo_otros}',"
            . "sobresueldo_gastos_representacion = '{$sobresueldo_gastos_representacion}', salario_base = '{$_POST['salario']}',"
            . "porcentaje = '{$porcentaje_sobresueldo}', salario_base_porcentaje = '{$sobresueldo}',"
            . "fecha_inicio = '{$fecha_inicio}', subtipo = '{$_POST['tipo_tiporegistro']}',"
            . "descripcion = '{$_POST['descripcion']}'"    
            . "WHERE cod_expediente_det='{$_POST['codigo']}'";
    $resultado=query($consulta,$conexion);                         

    }

//JUBILACION - EDITAR
    else if ($_POST['tipo_registro']=="35")
    {       

        if($_POST['fecha']!='')
                $fecha=fecha_sql($_POST['fecha']);
        if($_POST['fecha_enterado']!='')
                $fecha_enterado=fecha_sql($_POST['fecha_enterado']);        

        $fecha_modificacion = date('Y-m-d');      
        
        //ACUTALIZACION EXPEDIENTE
        
        $consulta = "UPDATE expediente SET "
            . "fecha = '{$fecha}', fecha_enterado = '{$fecha_enterado}', descripcion = '{$_POST['descripcion']}',"
            . "motivo_jubilacion = '{$_POST['motivo_jubilacion']}',fecha_modificacion = '{$fecha_modificacion}',usuario_modificacion = '{$usuario}'"    
            . " WHERE cod_expediente_det='{$_POST['codigo']}'";
    $resultado=query($consulta,$conexion);                         

    }     

//PRORROGA CONTINUACION - EDITAR
    else if ($_POST['tipo_registro']=="36")
    {       

        if($_POST['fecha']!='')
                $fecha=fecha_sql($_POST['fecha']);

        $fecha_modificacion = date('Y-m-d');      
        
        //ACUTALIZACION EXPEDIENTE
        
        $consulta = "UPDATE expediente SET "
            . "fecha = '{$fecha}', descripcion = '{$_POST['descripcion']}',fecha_modificacion = '{$fecha_modificacion}',usuario_modificacion = '{$usuario}'"    
            . " WHERE cod_expediente_det='{$_POST['codigo']}'";
    $resultado=query($consulta,$conexion); 
                        

    }    
    
    //INICIO LABORES - EDITAR
    else if ($_POST['tipo_registro']=="37")
    {       

        if($_POST['fecha']!='')
                $fecha=fecha_sql($_POST['fecha']);

        $fecha_modificacion = date('Y-m-d');      
        
        //ACUTALIZACION EXPEDIENTE
        
        $consulta = "UPDATE expediente SET "
            . "fecha = '{$fecha}', descripcion = '{$_POST['descripcion']}',fecha_modificacion = '{$fecha_modificacion}',usuario_modificacion = '{$usuario}'"    
            . " WHERE cod_expediente_det='{$_POST['codigo']}'";
    $resultado=query($consulta,$conexion); 
                        

    }
    
    //CAMBIO NOMBRE/APELLIDO - EDITAR
    else if ($_POST['tipo_registro']=="38")
    {       

        if($_POST['fecha']!='')
                $fecha=fecha_sql($_POST['fecha']);

        $fecha_modificacion = date('Y-m-d');      
        
        //ACUTALIZACION EXPEDIENTE
        
        $consulta = "UPDATE expediente SET "
            . "fecha = '{$fecha}', descripcion = '{$_POST['descripcion']}',nombres_nuevo = '{$_POST['nombres_nuevo']}',"
            . "apellido_paterno_nuevo = '{$_POST['apellido_paterno_nuevo']}',apellido_materno_nuevo = '{$_POST['apellido_materno_nuevo']}',"
            . "apellido_casada_nuevo = '{$_POST['apellido_casada_nuevo']}',fecha_modificacion = '{$fecha_modificacion}',usuario_modificacion = '{$usuario}'"    
            . " WHERE cod_expediente_det='{$_POST['codigo']}'";
    $resultado=query($consulta,$conexion);                         

    }    
    
    //DEFUNCION - EDITAR
    else if ($_POST['tipo_registro']=="39")
    {       

        if($_POST['fecha']!='')
                $fecha=fecha_sql($_POST['fecha']);
        if($_POST['fecha_enterado']!='')
                $fecha_enterado=fecha_sql($_POST['fecha_enterado']);        

        $fecha_modificacion = date('Y-m-d');      
        
        //ACUTALIZACION EXPEDIENTE
        
        $consulta = "UPDATE expediente SET "
            . "fecha = '{$fecha}', fecha_enterado = '{$fecha_enterado}', descripcion = '{$_POST['descripcion']}',"
            . "causa_defuncion = '{$_POST['causa_defuncion']}',fecha_modificacion = '{$fecha_modificacion}',usuario_modificacion = '{$usuario}'"    
            . " WHERE cod_expediente_det='{$_POST['codigo']}'";
    $resultado=query($consulta,$conexion);                         

    }    
    
    //REINCORPORACION - EDITAR
    else if ($_POST['tipo_registro']=="40")
    {       

        if($_POST['fecha']!='')
                $fecha=fecha_sql($_POST['fecha']);
        
        if($_POST['fecha_enterado']!='')
                $fecha_enterado=fecha_sql($_POST['fecha_enterado']);
        
        if($_POST['fecha_enterado_jefe']!='')
                $fecha_enterado_jefe=fecha_sql($_POST['fecha_enterado_jefe']);

        $fecha_modificacion = date('Y-m-d');      
        
        //ACUTALIZACION EXPEDIENTE
        
        $consulta = "UPDATE expediente SET "
            . "fecha = '{$fecha}',fecha_enterado = '{$fecha_enterado}',fecha_enterado_jefe = '{$fecha_enterado_jefe}', subtipo = '{$_POST['tipo_tiporegistro']}',"
            . "descripcion = '{$_POST['descripcion']}',fecha_modificacion = '{$fecha_modificacion}',usuario_modificacion = '{$usuario}'"    
            . " WHERE cod_expediente_det='{$_POST['codigo']}'";
    $resultado=query($consulta,$conexion);                         

    }
    
    //RECLASIFICACION - EDITAR
    else if ($_POST['tipo_registro']=="41")
    {       

        if($_POST['fecha_reclasificacion']!='')
            $fecha_reclasificacion=fecha_sql($_POST['fecha_reclasificacion']);

        if($_POST['fecha_decreto']!='')
           $fecha_decreto=fecha_sql($_POST['fecha_decreto']);

        $fecha_modificacion = date('Y-m-d');

         //ACTUALIZACION EXPEDIENTE       

        $consulta = "UPDATE expediente SET "
                . "descripcion = '{$_POST['descripcion']}', fecha='{$fecha_reclasificacion}',numero_decreto = '{$_POST['numero_decreto']}',fecha_decreto = '{$fecha_decreto}',"
                . "gerencia_nueva = '{$_POST['gerencia_nueva']}',departamento_nuevo = '{$_POST['departamento_nuevo']}',seccion_nueva = '{$_POST['seccion_nueva']}', "
                . "cod_cargo_nuevo = '{$_POST['cargo_nuevo']}',funcion_nueva = '{$_POST['funcion_nueva']}',"
                . "fecha_modificacion = '{$fecha_modificacion}',usuario_modificacion = '{$usuario}'"
                . "WHERE cod_expediente_det='{$_POST['codigo']}'";
        $resultado=query($consulta,$conexion);
                        

    }    
    
    //AUMENTO HORAS - EDITAR
    else if ($_POST['tipo_registro']=="42")
    {       

        if($_POST['fecha']!='')
                $fecha=fecha_sql($_POST['fecha']);

        $fecha_modificacion = date('Y-m-d');      
        
        //ACUTALIZACION EXPEDIENTE
        
        $consulta = "UPDATE expediente SET "
            . "fecha = '{$fecha}', descripcion = '{$_POST['descripcion']}',fecha_modificacion = '{$fecha_modificacion}',usuario_modificacion = '{$usuario}'"    
            . " WHERE cod_expediente_det='{$_POST['codigo']}'";
    $resultado=query($consulta,$conexion); 
                        

    }    
    
    //LIBRE NOMBRAMIENTO / REMOCION - EDITAR
    else if ($_POST['tipo_registro']=="43")
    {       

        if($_POST['fecha']!='')
                $fecha=fecha_sql($_POST['fecha']);

        $fecha_modificacion = date('Y-m-d');      
        
        //ACUTALIZACION EXPEDIENTE
        
        $consulta = "UPDATE expediente SET "
            . "fecha = '{$fecha}', descripcion = '{$_POST['descripcion']}',fecha_modificacion = '{$fecha_modificacion}',usuario_modificacion = '{$usuario}'"    
            . " WHERE cod_expediente_det='{$_POST['codigo']}'";
    $resultado=query($consulta,$conexion); 
                        

    }    
    
//CESE DE LABORES - EDITAR
else if ($_POST['tipo_registro']=="52")
{
    if($_POST['fecha_resolucion']!='')
                    $fecha_resolucion=fecha_sql($_POST['fecha_resolucion']);
            if($_POST['fecha']!='')
                    $fecha=fecha_sql($_POST['fecha']);
            $fecha_modificacion = date("Y-m-d");

     //ACTUALIZACION EXPEDIENTE
    $consulta = "UPDATE expediente SET "
        . "descripcion = '{$_POST['descripcion']}', fecha_efectividad = '{$fecha}',fecha_resolucion='{$fecha_resolucion}',"
        . "numero_resolucion = '{$_POST['numero_resolucion']}',fecha_modificacion = '{$fecha_modificacion}',usuario_modificacion = '{$usuario}'"
        . "WHERE cod_expediente_det='{$_POST['codigo']}'";
    $resultado=query($consulta,$conexion);  
}

//ABANDONO DE CARGO - EDITAR
else if ($_POST['tipo_registro']=="54")
{
    if($_POST['fecha_resolucion']!='')
                    $fecha_resolucion=fecha_sql($_POST['fecha_resolucion']);
            if($_POST['fecha']!='')
                    $fecha=fecha_sql($_POST['fecha']);
            $fecha_modificacion = date("Y-m-d");

     //ACTUALIZACION EXPEDIENTE
    $consulta = "UPDATE expediente SET "
        . "descripcion = '{$_POST['descripcion']}', fecha_efectividad = '{$fecha}',fecha_resolucion='{$fecha_resolucion}',"
        . "numero_resolucion = '{$_POST['numero_resolucion']}',fecha_modificacion = '{$fecha_modificacion}',usuario_modificacion = '{$usuario}'"
        . "WHERE cod_expediente_det='{$_POST['codigo']}'";
    $resultado=query($consulta,$conexion);  
}

//AUMENTESE, ASCIENDASE Y TRASLADESE  - EDITAR
else if ($_POST['tipo_registro']=="56")
{
      if($_POST['fecha_decreto']!='')
            $fecha_decreto=fecha_sql($_POST['fecha_decreto']);

    $fecha_modificacion = date('Y-m-d');

     //ACTUALIZACION EXPEDIENTE       

    $consulta = "UPDATE expediente SET "
            . "descripcion = '{$_POST['descripcion']}', fecha='{$fecha_decreto}',numero_decreto = '{$_POST['numero_decreto']}',fecha_decreto = '{$fecha_decreto}',"
            . "gerencia_nueva = '{$_POST['gerencia_nueva']}',departamento_nuevo = '{$_POST['departamento_nuevo']}',seccion_nueva = '{$_POST['seccion_nueva']}', "
            . "posicion_nueva = '{$_POST['posicion_nueva']}',cod_cargo_nuevo = '{$_POST['cargo_nuevo']}',funcion_nueva = '{$_POST['funcion_nueva']}',"
            . "planilla_nueva = '{$_POST['planilla_nueva']}',subtipo = '{$_POST['tipo_tiporegistro']}',"
            . "fecha_modificacion = '{$fecha_modificacion}',usuario_modificacion = '{$usuario}'"
            . "WHERE cod_expediente_det='{$_POST['codigo']}'";
    $resultado=query($consulta,$conexion);
}
        
//BAJA (OFICIAL) - EDITAR
else if ($_POST['tipo_registro']=="57")
{
    if($_POST['fecha_baja']!='')
        $fecha_baja=fecha_sql($_POST['fecha_baja']);

    if($_POST['fecha_notificacion']!='')
            $fecha_notificacion=fecha_sql($_POST['fecha_notificacion']);

    if($_POST['fecha_resuelto']!='')
            $fecha_resuelto=fecha_sql($_POST['fecha_resuelto']);

    if($_POST['fecha_edicto']!='')
            $fecha_edicto=fecha_sql($_POST['fecha_edicto']);

    $fecha_modificacion = date('Y-m-d');

     //ACTUALIZACION EXPEDIENTE       

    $consulta = "UPDATE expediente SET "
            . "descripcion = '{$_POST['descripcion']}', fecha='{$fecha_baja}',fecha_notificacion = '{$fecha_notificacion}',"
            . "numero_resolucion = '{$_POST['numero_resuelto']}', fecha_resolucion = '{$fecha_resuelto}',"
            . "numero_edicto = '{$_POST['numero_edicto']}', fecha_edicto = '{$fecha_edicto}',"
            . "subtipo = '{$_POST['tipo_tiporegistro']}',fecha_modificacion = '{$fecha_modificacion}',usuario_modificacion = '{$usuario}'"
            . "WHERE cod_expediente_det='{$_POST['codigo']}'";
    $resultado=query($consulta,$conexion);
}

//REINTEGRO - EDITAR
else if ($_POST['tipo_registro']=="58")
{
   if($_POST['fecha_reintegro']!='')
            $fecha_reintegro=fecha_sql($_POST['fecha_reintegro']);

    if($_POST['fecha_notificacion']!='')
            $fecha_notificacion=fecha_sql($_POST['fecha_notificacion']);

    if($_POST['fecha_fin']!='')
            $fecha_fin=fecha_sql($_POST['fecha_fin']);

    if($_POST['fecha_decreto']!='')
            $fecha_decreto_ingreso_nuevo=fecha_sql($_POST['fecha_decreto']);

    $fecha_modificacion = date('Y-m-d');

     //ACTUALIZACION EXPEDIENTE       

    $consulta = "UPDATE expediente SET "
            . "descripcion = '{$_POST['descripcion']}', fecha='{$fecha_reintegro}',fecha_inicio='{$fecha_reintegro}',fecha_fin='{$fecha_fin}',"
            . "fecha_notificacion = '{$fecha_notificacion}',numero_decreto = '{$_POST['numero_decreto']}',fecha_decreto_ingreso_nuevo = '{$fecha_decreto_ingreso_nuevo}',"
            . "gerencia_nueva = '{$_POST['gerencia_nueva']}',departamento_nuevo = '{$_POST['departamento_nuevo']}',seccion_nueva = '{$_POST['seccion_nueva']}', "
            . "posicion_nueva = '{$_POST['posicion_nueva']}',cod_cargo_nuevo = '{$_POST['cargo_nuevo']}',funcion_nueva = '{$_POST['funcion_nueva']}',"
            . "planilla_nueva = '{$_POST['planilla_nueva']}',situacion_nueva = '{$_POST['situacion_nueva']}',"
            . "fecha_modificacion = '{$fecha_modificacion}',usuario_modificacion = '{$usuario}'"
            . "WHERE cod_expediente_det='{$_POST['codigo']}'";
    $resultado=query($consulta,$conexion);
}

?>
