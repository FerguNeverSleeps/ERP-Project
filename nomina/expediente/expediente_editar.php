<?php
$consulta_personal="SELECT * FROM nompersonal WHERE cedula='$cedula'";
$resultado_personal=query($consulta_personal,$conexion);
$fetch_personal=fetch_array($resultado_personal,$conexion);
$personal_id=$fetch_personal['personal_id'];
$nombre=$fetch_personal['apenom'];
$user_uid=$fetch_personal['useruid'];
$id_empleado=$fetch_personal['personal_id'];
$nomposicion_id=$fetch_personal['nomposicion_id'];
$seguro_social=$fetch_personal['seguro_social'];
$clave_ir=$fetch_personal['clave_ir'];
$sexo=$fetch_personal['sexo'];
$nombres=$fetch_personal['nombres'];
$apellido_paterno=$fetch_personal['apellidos'];
$apellido_materno=$fetch_personal['apellido_materno'];
$apellido_casada=$fetch_personal['apellido_casada'];
$fecing=$fetch_personal['fecing'];
$titular_interino=$fetch_personal['tipo_empleado'];
$tipemp=$fetch_personal['condicion'];
$situacion=$fetch_personal['estado'];
$inicio_periodo=$fetch_personal['inicio_periodo'];
$fin_periodo=$fetch_personal['fin_periodo'];
$fecha_permanencia=$fetch_personal['fecha_permanencia'];
$ficha_persona=$fetch_personal['ficha'];

query("set names utf8",$conexion);
//ESTUDIOS ACADEMICOS - EDITAR
if ($_POST['tipo_registro']=="1")
{
        if($_POST['fecha_inicio']!='')
                    $fecha_inicio=fecha_sql($_POST['fecha_inicio']);				
            if($_POST['fecha_fin']!='')
                    $fecha_fin=fecha_sql($_POST['fecha_fin']);
        $fecha = date("Y-m-d");
        $fecha_modificacion = date("Y-m-d");

         //ACTUALIZACION EXPEDIENTE       
        
        $consulta = "UPDATE expediente SET "
                . "descripcion = '{$_POST['descripcion']}',"
                . "institucion_educativa_nueva = '{$_POST['institucion_educativa_nueva']}',"
                . "titulo_profesional = '{$_POST['titulo_profesional']}',"
                . "idoneidad = '{$_POST['idoneidad']}',"
                . "ejerce = '{$_POST['ejerce']}',"
                . "fecha = '{$fecha}',"
                . "fecha_inicio = '{$fecha_inicio}',"
                . "fecha_fin = '{$fecha_fin}',"
                . "dias = '{$_POST['duracion']}',"
                . "subtipo = '{$_POST['tipo_tiporegistro']}',"
                . "fecha_modificacion = '{$fecha_modificacion}',"
                . "usuario_modificacion = '{$usuario}'"
                . " WHERE cod_expediente_det='{$_POST['codigo']}'";
        $resultado=query($consulta,$conexion);
        
}

//CAPACITACION - EDITAR
if ($_POST['tipo_registro']=="2")
{
        if($_POST['fecha_inicio']!='')
           $fecha_inicio=fecha_sql($_POST['fecha_inicio']);				
        if($_POST['fecha_fin']!='')
             $fecha_fin=fecha_sql($_POST['fecha_fin']);
        $fecha = date("Y-m-d");
        $fecha_modificacion = date("Y-m-d");

         //ACTUALIZACION EXPEDIENTE       
        
        $consulta = "UPDATE expediente SET "
                . "descripcion = '{$_POST['descripcion']}',"
                . "institucion = '{$_POST['institucion']}',"
                . "nombre_especialista = '{$_POST['nombre_especialista']}',"
                . "fecha = '{$fecha}',"
                . "fecha_inicio = '{$fecha_inicio}',"
                . "fecha_fin = '{$fecha_fin}',"
                . "duracion = '{$_POST['duracion']}',"
                . "subtipo = '{$_POST['tipo_tiporegistro']}',"
                . "fecha_modificacion = '{$fecha_modificacion}',"
                . "usuario_modificacion = '{$usuario}'"
                . " WHERE cod_expediente_det='{$_POST['codigo']}'";
        $resultado=query($consulta,$conexion);
        
}

//PERMISOS - EDITAR
if ($_POST['tipo_registro']=="4"){
        if($_POST['fecha_inicio']!='')
                $fecha_inicio=fecha_sql($_POST['fecha_inicio']);

        if($_POST['fecha_fin']!='')
                $fecha_fin=fecha_sql($_POST['fecha_fin']);

//        if($_POST['fecha_aprobado']!='')
//                $fecha_aprobado=fecha_sql($_POST['fecha_aprobado']);

        if($_POST['fecha_resolucion']!='')
                $fecha_resolucion=fecha_sql($_POST['fecha_resolucion']);	

        if($_POST['fecha']!='')
                $fecha=fecha_sql($_POST['fecha']);

          if($_POST['horas']!='')
        {
            $horas=$_POST['horas'];
        }
        else
        {
            $horas=0;
        }
        
        if($_POST['minutos']!='')
        {
            $minutos=$_POST['minutos'];
        }
        else
        {
            $minutos=0;
        }
        
        if($_POST['restante']!='')
        {
            $restantes=$_POST['restante'];
        }
        else
        {
            $restantes=0;
        }
        
        if($_POST['dias']!='')
        {
            $dias=$_POST['dias'];
        }
        else
        {
            $dias=0;
        }  
        
        if($_POST['duracion']!='')
        {
            $duracion=$_POST['duracion'];
        }
        else
        {
            $duracion=0;
        }  

        $duracion = $dias*8 + $horas +($minutos/60);
        
        $fecha_modificacion = date("Y-m-d");

        
                $consulta = "UPDATE expediente SET "
                . "descripcion = '{$_POST['descripcion']}',"
                . "desde = '{$_POST['desde']}',"
                . "fecha_inicio = '{$fecha_inicio}',"
                . "hasta = '{$_POST['hasta']}',"
                . "fecha_fin = '{$fecha_fin}',"
                . "dias = '{$dias}',"
                . "horas = '{$horas}',"
                . "minutos = '{$minutos}',"
                . "duracion = '{$duracion}',"
                . "numero_resolucion = '{$_POST['numero_resolucion']}',"
                . "fecha = '{$fecha}',"
                . "fecha_resolucion = '{$fecha_resolucion}',"
                . "id_centro = '{$_POST['id_centro']}', "
                . "nombre_medico = '{$_POST['nombre_medico']}', "
                . "proyecto = '{$_POST['proyecto']}', "
                . "subtipo = '{$_POST['tipo_tiporegistro']}',"
                . "fecha_modificacion = '{$fecha_modificacion}',"
                . "usuario_modificacion = '{$usuario}'"
                . " WHERE cod_expediente_det='{$_POST['codigo']}'";
        $resultado=query($consulta,$conexion);

}

//AMONESTACIONES  - EDITAR
if ($_POST['tipo_registro']=="5")
{
        if($_POST['fecha_amonestacion']!='')
                $fecha_amonestacion=fecha_sql($_POST['fecha_amonestacion']);
        
        $fecha_modificacion = date("Y-m-d");
        
        

         //ACTUALIZACION EXPEDIENTE       
        
        $consulta = "UPDATE expediente SET "
                . "descripcion = '".($_POST['descripcion'])."',"
                . "fecha = '{$fecha_amonestacion}',"
                . "numeral = '{$_POST['numeral']}',"
                . "numeral_descripcion = '".($_POST['numeral_descripcion'])."',"
                . "articulo = '{$_POST['articulo']}',"
                . "tipo_falta = '{$_POST['tipo_falta']}',"
                . "subtipo = '{$_POST['tipo_tiporegistro']}',fecha_modificacion = '{$fecha_modificacion}',usuario_modificacion = '{$usuario}'"
                . " WHERE cod_expediente_det='{$_POST['codigo']}'";
        $resultado=query($consulta,$conexion);
        
}

//SUSPENSIONES  - EDITAR
if ($_POST['tipo_registro']=="6")
{
         if($_POST['fecha_resolucion']!='')
                    $fecha_resolucion=fecha_sql($_POST['fecha_resolucion']);				
            if($_POST['fecha_desde']!='')
                    $fecha_desde=fecha_sql($_POST['fecha_desde']);                        
            if($_POST['fecha_hasta']!='')
                    $fecha_hasta=fecha_sql($_POST['fecha_hasta']);
        
        $fecha_modificacion = date("Y-m-d");                

         //ACTUALIZACION EXPEDIENTE       
        
        $consulta = "UPDATE expediente SET "
                . "descripcion = '{$_POST['descripcion']}',"
                . "fecha = '{$fecha_resolucion}',"
                . "fecha_resolucion = '{$fecha_resolucion}',"
                . "numero_resolucion = '{$_POST['numero_resolucion']}',"
                . "fecha_inicio = '{$fecha_desde}',"
                . "fecha_fin = '{$fecha_hasta}',"
                . "dias = '{$_POST['dias']}',"
                . "numeral = '{$_POST['numeral']}',"
                . "numeral_descripcion = '{$_POST['numeral_descripcion']}',"
                . "articulo = '{$_POST['articulo']}',"
                . "tipo_falta = '{$_POST['tipo_falta']}',"
                . "fecha_modificacion = '{$fecha_modificacion}',usuario_modificacion = '{$usuario}'"
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
                . "descripcion = '{$_POST['descripcion']}',fecha = '{$fecha_modificacion}',gerencia_nueva = '{$_POST['gerencia_nueva']}',departamento_nuevo = '{$_POST['departamento_nuevo']}',"
                . "seccion_nueva = '{$_POST['seccion_nueva']}',cod_cargo_nuevo = '{$_POST['cargo_nuevo']}',funcion_nueva = '{$_POST['funcion_nueva']}',institucion_nueva = '{$_POST['institucion_nueva']}',"
                . "numero_resolucion = '{$_POST['numero_resolucion']}',fecha_resolucion = '{$fecha_resolucion}',fecha_inicio = '{$fecha_inicio}',"
                . "fecha_fin = '{$fecha_fin}',motivo_traslado = '{$_POST['motivo_traslado']}',posicion_nueva='{$_POST['posicion_nueva']}',"
                . "subtipo = '{$_POST['tipo_tiporegistro']}',fecha_modificacion = '{$fecha_modificacion}',usuario_modificacion = '{$usuario}'"
                . " WHERE cod_expediente_det='{$_POST['codigo']}'";
        $resultado=query($consulta,$conexion);
        
}

//EVALUACION DESEMPEÑO - EDITAR
if ($_POST['tipo_registro']=="10")
{
        if($_POST['fecha']!='')//CAMBIAR fecha_salida POR fecha_aplicacion.
                    $fecha=fecha_sql($_POST['fecha']);
            if($_POST['fecha_inicio_periodo']!='')
            $fecha_inicio_periodo=fecha_sql($_POST['fecha_inicio_periodo']);
            if($_POST['fecha_fin_periodo']!='')
                $fecha_fin_periodo=fecha_sql($_POST['fecha_fin_periodo']);
            $tipo="Ordinario";
//            echo $_POST['tipo_tiporegistro'];
//            exit;
            if ($_POST['tipo_tiporegistro']=="35")
            {
                $tipo="Periodo Probatorio";
//                echo "AQUI";
//                exit;
              
            }
        $fecha_modificacion = date("Y-m-d");

         //ACTUALIZACION EXPEDIENTE       
        
        $consulta = "UPDATE expediente SET "
                . "descripcion = '{$_POST['descripcion']}',"
                . "fecha_inicio_periodo = '{$fecha_inicio_periodo}',"
                . "fecha_fin_periodo = '{$fecha_fin_periodo}',"
                . "fecha = '{$fecha}',"
                . "puntaje = '{$_POST['puntaje']}', "
                . "funcionario_evalua = '{$_POST['funcionario_evalua']}', "
                . "subtipo = '{$_POST['tipo_tiporegistro']}', "
                . "fecha_modificacion = '{$fecha_modificacion}',"
                . "usuario_modificacion = '{$usuario}'"
                . " WHERE cod_expediente_det='{$_POST['codigo']}'";
        $resultado=query($consulta,$conexion);
        
        $consulta_evaluacion = "UPDATE empleado_evaluacion SET "
                . "comentarios = '{$_POST['descripcion']}',"
                . "fini_periodo = '{$fecha_inicio_periodo}',"
                . "ffin_periodo = '{$fecha_fin_periodo}',"
                . "f_eval = '{$fecha}',"
                . "puntaje = '{$_POST['puntaje']}', "
                . "tipo = '{$tipo}',"
                . "persona_evalua = '{$_POST['funcionario_evalua']}', "
                . "fecha_creacion = '{$fecha_modificacion}',"
                . "usuario_creacion = '{$usuario}'"
                . " WHERE cod_expediente_det='{$_POST['codigo']}'";
        $resultado_evaluacion=query($consulta_evaluacion,$conexion);
        
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
            
        $resuelto=$_POST['resuelto'];
        
        $fecha = date("Y-m-d");
        $fecha_modificacion = date("Y-m-d");

         //ACTUALIZACION EXPEDIENTE       
        
        $consulta = "UPDATE expediente SET "
                . "descripcion = '{$_POST['descripcion']}',fecha_inicio_periodo='{$fecha_inicio_periodo}',fecha_fin_periodo = '{$fecha_fin_periodo}',"
                . "fecha_inicio='{$fecha_inicio}',fecha_fin = '{$fecha_fin}',numero_resolucion = '{$numero_resolucion}', dias = '{$_POST['dias']}',"
                . "restante = '{$_POST['restante']}',periodo_vacacion = '{$periodo_vacacion}',resuelto = '{$resuelto}', fecha = '{$fecha}',"
                . "fecha_resolucion = '{$fecha_resolucion}',subtipo = '{$_POST['tipo_tiporegistro']}',fecha_modificacion = '{$fecha_modificacion}',usuario_modificacion = '{$usuario}'"
                . " WHERE cod_expediente_det='{$_POST['codigo']}'";
        $resultado=query($consulta,$conexion);
        
}

//TIEMPO COMPENSATORIO - EDITAR
else if ($_POST['tipo_registro']=="12")
{                  
    if($_POST['fecha']!='')
            $fecha=fecha_sql($_POST['fecha']);

    if($_POST['fecha_aprobado']!='')
            $fecha_aprobado=fecha_sql($_POST['fecha_aprobado']);

    if($_POST['fecha_inicio']!='')
            $fecha_inicio=fecha_sql($_POST['fecha_inicio']); 

    if($_POST['fecha_fin']!='')
            $fecha_fin=fecha_sql($_POST['fecha_fin']); 

    if($_POST['horas']!='')
    {
        $horas=$_POST['horas'];
    }
    else
    {
        $horas=0;
    }
    if($_POST['minutos']!='')
    {
        $minutos=$_POST['minutos'];
    }
    else
    {
        $minutos=0;
    }
    if($_POST['restante']!='')
    {
        $restantes=$_POST['restante'];
    }
    else
    {
        $restantes=0;
    }
    if($_POST['dias']!='')
    {

        $dias=$_POST['dias'];
    }
    else
    {
        $dias=0;
    }      
    if($_POST['duracion']!='')
    {

        $duracion=$_POST['duracion'];
    }
    else
    {
        $duracion=0;
    }      

//    $duracion = $horas + ($minutos / 60);             

    $tipo_ajuste=$_POST['tipo_ajuste'];
        
    $fecha_modificacion = date("Y-m-d");
   
    //ACTUALIZACION EXPEDIENTE

    $consulta = "UPDATE expediente SET "           
            . "tipo_ajuste = '{$tipo_ajuste}',"
            . "dias = '{$dias}',"
            . "restante = '{$restantes}',"
            . "duracion = '{$duracion}',"
            . "minutos = '{$minutos}',"
            . "horas = '{$horas}',"
            . "fecha = '{$fecha}',"
            . "fecha_aprobado = '{$fecha_aprobado}',"
            . "fecha_inicio = '{$fecha_inicio}',"
            . "fecha_fin = '{$fecha_fin}',"
            . "descripcion = '{$_POST['descripcion']}',"
            . " usuario_modificacion ='{$usuario}',"
            . "fecha_modificacion = '{$fecha_modificacion}'"    
            . "WHERE cod_expediente_det='{$_POST['codigo']}'";
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

        $consulta = "UPDATE `expediente_documento` SET
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
                . "restante = '{$_POST['disponible']}', posicion_anterior = '{$posicion}', posicion_nueva = '{$posicion}', numero_resolucion = '{$_POST['numero_resolucion']}', fecha = '{$fecha_resolucion}',"
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

//ROTACION / REASIGNACION / APOYO TEMPORAL- EDITAR
if ($_POST['tipo_registro']=="24" || $_POST['tipo_registro']=="25" || $_POST['tipo_registro']=="26")
{
        if($_POST['fecha_memo']!='')
                    $fecha_memo=fecha_sql($_POST['fecha_memo']);
        if($_POST['fecha_inicio']!='')
            $fecha_inicio=fecha_sql($_POST['fecha_inicio']);
        if($_POST['fecha_fin']!='')
            $fecha_fin=fecha_sql($_POST['fecha_fin']);
        
        $fecha_modificacion = date("Y-m-d");
        
        
         //ACTUALIZACION EXPEDIENTE       
        
        $consulta = "UPDATE expediente SET "
                . "descripcion = '{$_POST['descripcion']}',"
                . "planilla_nueva = '{$_POST['planilla_nueva']}',"
                . "departamento_nuevo = '{$_POST['departamento_nuevo']}',"
                . "num_memo = '{$_POST['num_memo']}',"
                . "fecha_memo = '{$fecha_memo}',"
                . "fecha_inicio = '{$fecha_inicio}',"
                . "fecha_fin = '{$fecha_fin}',"
                . "dejado = '{$_POST['dejado']}',"
                . "fecha_modificacion = '{$fecha_modificacion}',"
                . "usuario_modificacion = '{$usuario}'"
                . " WHERE cod_expediente_det='{$_POST['codigo']}'";
        $resultado=query($consulta,$conexion);
        
        //ACTUALIZACION EMPLEADO CARGO       
        if($_POST['tipo_registro']==24)
        {
            $tipo_movimiento = "T";
            $tipo_accion=48;
        }
        if($_POST['tipo_registro']==25)
        {
            $tipo_movimiento = "R";
            $tipo_accion=49;
        }
        if($_POST['tipo_registro']==26)
        {
            $tipo_accion=50;
            $tipo_movimiento = "A";
        }
        
        $sql_empleado_cargo = "UPDATE empleado_cargo SET "
                . "IdDepartamento = '{$_POST['departamento_nuevo']}',"
                . "num_memo = '{$_POST['num_memo']}',"
                . "fecha_memo = '{$fecha_memo}',"
                . "FechaInicio = '{$fecha_inicio}',"
                . "FechaFinal = '{$fecha_fin}',"
                . "dejado = '{$_POST['dejado']}',"
                . "TipoMovimiento = '{$tipo_movimiento}'"
                . " WHERE cod_expediente_det='{$_POST['codigo']}'";
         $resultado_empleado_cargo=query($sql_empleado_cargo,$conexion); 
         
         //ACTUALIZACION PERSONAL
        $consulta_personal = "UPDATE nompersonal SET "
                            . "fechamov = '{$fecha_inicio}', "
                            . "fechareimov = '{$fecha_fin}', "
                            . "tipnom = '{$_POST['planilla_nueva']}',"
                            . "IdDepartamento = '{$_POST['departamento_nuevo']}'"
                            . "WHERE cedula='{$cedula}'";
        //echo $consulta2;
        $resultado_personal=query($consulta_personal,$conexion); 
        
}

//AJUSTE DE TIEMPO - EDITAR
else if ($_POST['tipo_registro']=="27")
{                  
    
    $fecha_modificacion = date("Y-m-d");       
        
         if($_POST['fecha_inicio']!='')
            $fecha_inicio=fecha_sql($_POST['fecha_inicio']);
        if($_POST['fecha_fin']!='')
            $fecha_fin=fecha_sql($_POST['fecha_fin']);
        
        $tipo_ajuste=$_POST['tipo_ajuste'];
        
        if($_POST['horas']!='')
        {
            $horas=$_POST['horas'];
        }
        else
        {
            $horas=0;
        }
        
        if($_POST['minutos']!='')
        {
            $minutos=$_POST['minutos'];
        }
        else
        {
            $minutos=0;
        }
        
        if($_POST['restante']!='')
        {
            $restantes=$_POST['restante'];
        }
        else
        {
            $restantes=0;
        }
        
        if($_POST['dias']!='')
        {
            $dias=$_POST['dias'];
        }
        else
        {
            $dias=0;
        }  
        
        if($_POST['duracion']!='')
        {
            $duracion=$_POST['duracion'];
        }
        else
        {
            $duracion=0;
        }  

    //$duracion = $horas + ($minutos / 60);
        
    $fecha = date("Y-m-d");
   
    //ACTUALIZACION EXPEDIENTE

    $consulta = "UPDATE expediente SET "           
            . "tipo_ajuste = '{$tipo_ajuste}',"
            . "dias = '{$dias}',"
            . "restante = '{$restantes}',"
            . "minutos = '{$minutos}',"
            . "duracion = '{$duracion}',"
            . "horas = '{$horas}', "
             . "fecha_inicio = '{$fecha_inicio}',"
                . "fecha_fin = '{$fecha_fin}',"
            . "subtipo = '{$_POST['tipo_tiporegistro']}', "
            . "descripcion = '{$_POST['descripcion']}', "
            . "usuario_modificacion ='{$usuario}',fecha_modificacion = '{$fecha_modificacion}'"    
            . "WHERE cod_expediente_det='{$_POST['codigo']}'";
    $resultado=query($consulta,$conexion); 
    
}

//MISION OFICIAL - EDITAR
    else if ($_POST['tipo_registro']=="28")
    {       

        $fecha_modificacion = date("Y-m-d");       
        
         if($_POST['fecha_inicio']!='')
            $fecha_inicio=fecha_sql($_POST['fecha_inicio']);
        if($_POST['fecha_fin']!='')
            $fecha_fin=fecha_sql($_POST['fecha_fin']);
        
        $tipo_ajuste=$_POST['tipo_ajuste'];
        
        if($_POST['horas']!='')
        {
            $horas=$_POST['horas'];
        }
        else
        {
            $horas=0;
        }
        
        if($_POST['minutos']!='')
        {
            $minutos=$_POST['minutos'];
        }
        else
        {
            $minutos=0;
        }
        
        if($_POST['restante']!='')
        {
            $restantes=$_POST['restante'];
        }
        else
        {
            $restantes=0;
        }
        
        if($_POST['dias']!='')
        {
            $dias=$_POST['dias'];
        }
        else
        {
            $dias=0;
        }  
        
        if($_POST['duracion']!='')
        {
            $duracion=$_POST['duracion'];
        }
        else
        {
            $duracion=0;
        }  

    //$duracion = $horas + ($minutos / 60);
        
    $fecha = date("Y-m-d");
   
    //ACTUALIZACION EXPEDIENTE

    $consulta = "UPDATE expediente SET " 
            . "dias = '{$dias}',"
            . "restante = '{$restantes}',"
            . "minutos = '{$minutos}',"
            . "duracion = '{$duracion}',"
            . "horas = '{$horas}', "
            . "tipo_mision = '{$_POST['tipo_mision']}', "
             . "fecha_inicio = '{$fecha_inicio}',"
                . "fecha_fin = '{$fecha_fin}',"
            . "descripcion = '{$_POST['descripcion']}', "
            . "usuario_modificacion ='{$usuario}',fecha_modificacion = '{$fecha_modificacion}'"    
            . "WHERE cod_expediente_det='{$_POST['codigo']}'";
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
        $monto_nuevo = str_replace(",","",$_POST['salario_nuevo']); 

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
    
//EXCEDENTE - EDITAR
    else if ($_POST['tipo_registro']=="34")
    {       

        if($_POST['fecha']!='')
                $fecha=fecha_sql($_POST['fecha']);

        $fecha_modificacion = date('Y-m-d');      
        
        $monto=str_replace(",","",$_POST['monto']);
        
        //ACUTALIZACION EXPEDIENTE
        
        $consulta = "UPDATE expediente SET "
            . "fecha = '{$fecha}', monto = '{$monto}', descripcion = '{$_POST['descripcion']}',fecha_modificacion = '{$fecha_modificacion}',usuario_modificacion = '{$usuario}'"    
            . " WHERE cod_expediente_det='{$_POST['codigo']}'";
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
        
        if($_POST['fecha_inicio']!='')
                $fecha_inicio=fecha_sql($_POST['fecha_inicio']);       
        if($_POST['fecha_permanencia']!='')
                $fecha_permanencia=fecha_sql($_POST['fecha_permanencia']);       
        if($_POST['fecha_inicio_periodo']!='')
                $fecha_inicio_periodo=fecha_sql($_POST['fecha_inicio_periodo']);       
        if($_POST['fecha_fin_periodo']!='')
                $fecha_fin_periodo=fecha_sql($_POST['fecha_fin_periodo']);        
        if($_POST['fecha_decreto_nuevo']!='')
                $fecha_decreto_nuevo=fecha_sql($_POST['fecha_decreto_nuevo']);        
        if($_POST['numero_decreto_nuevo']!='')
                $numero_decreto_nuevo=$_POST['numero_decreto_nuevo'];

        $fecha_modificacion = date('Y-m-d');      
        
        //ACUTALIZACION EXPEDIENTE
        
        $consulta = "UPDATE expediente SET "
            . "fecha_inicio = '{$fecha_inicio}', fecha_permanencia = '{$fecha_permanencia}', "
            . "fecha_inicio_periodo = '{$fecha_inicio_periodo}', fecha_fin_periodo = '{$fecha_fin_periodo}', situacion_nueva = '{$_POST['situacion_nueva']}',"
            . "fecha_decreto_ingreso_nuevo = '{$fecha_decreto_nuevo}',numero_decreto = '{$numero_decreto_nuevo}',"
            . "descripcion = '{$_POST['descripcion']}',fecha_modificacion = '{$fecha_modificacion}',usuario_modificacion = '{$usuario}'"    
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

//ACREDITACION DE CARRERA MIGRATORIA - EDITAR
else if ($_POST['tipo_registro']=="59")
{
   if($_POST['cm_fecha_notificacion_ingreso_anterior']!='')
                    $cm_fecha_notificacion_ingreso_anterior=fecha_sql($_POST['cm_fecha_notificacion_ingreso_anterior']);

            if($_POST['cm_fecha_notificacion_ingreso']!='')
                    $cm_fecha_notificacion_ingreso=fecha_sql($_POST['cm_fecha_notificacion_ingreso']);
            
            if($_POST['cm_fecha_resolucion_anterior']!='')
                    $cm_fecha_resolucion_anterior=fecha_sql($_POST['cm_fecha_resolucion_anterior']);

            if($_POST['cm_fecha_resolucion']!='')
                    $cm_fecha_resolucion=fecha_sql($_POST['cm_fecha_resolucion']);
            
            if($_POST['cm_fecha_notificacion_homologacion_anterior']!='')
                    $cm_fecha_notificacion_homologacion_anterior=fecha_sql($_POST['cm_fecha_notificacion_homologacion_anterior']);

            if($_POST['cm_fecha_notificacion_homologacion']!='')
                    $cm_fecha_notificacion_homologacion=fecha_sql($_POST['cm_fecha_notificacion_homologacion']);
            
            if($_POST['cm_fecha_resolucion_homologacion_anterior']!='')
                    $cm_fecha_resolucion_homologacion_anterior=fecha_sql($_POST['cm_fecha_resolucion_homologacion_anterior']);

            if($_POST['cm_fecha_resolucion_homologacion']!='')
                    $cm_fecha_resolucion_homologacion=fecha_sql($_POST['cm_fecha_resolucion_homologacion']);

    $fecha_modificacion = date('Y-m-d');

     //ACTUALIZACION EXPEDIENTE       

    $consulta = "UPDATE expediente SET "
            . "descripcion = '{$_POST['descripcion']}',"
            ."cm_fecha_notificacion_ingreso='{$cm_fecha_notificacion_ingreso}',
            cm_numero_resolucion='{$_POST['cm_numero_resolucion']}',
            cm_fecha_resolucion='{$cm_fecha_resolucion}',
            cm_tipo_proceso='{$_POST['cm_tipo_proceso']}', 
            cm_sobresueldo='{$_POST['cm_sobresueldo']}',
            cm_gasto_responsabilidad='{$_POST['cm_gasto_responsabilidad']}',
            cm_gasto_representacion='{$_POST['cm_gasto_representacion']}',
            cm_incentivo_titulo='{$_POST['cm_incentivo_titulo']}', 
            cm_ascenso='{$_POST['cm_ascenso']}', 
            cm_directiva_confidencialidad='{$_POST['cm_directiva_confidencialidad']}', 
            cm_carta_compromiso='{$_POST['cm_carta_compromiso']}', 
            cm_fecha_notificacion_homologacion='{$cm_fecha_notificacion_homologacion}',
            cm_numero_resolucion_homologacion='{$_POST['cm_numero_resolucion_homologacion']}',
            cm_fecha_resolucion_homologacion='{$cm_fecha_resolucion_homologacion}',
            cm_acreditacion_personal_ordinario='{$_POST['cm_acreditacion_personal_ordinario']}',
            cm_auditoria_puesto='{$_POST['cm_auditoria_puesto']}',
            cm_placa='{$_POST['cm_placa']}',
            cm_promocion='{$_POST['cm_promocion']}',
            cm_jubliacion_partida='{$_POST['cm_jubliacion_partida']}',
            cm_jubilacion_anio='{$_POST['cm_jubilacion_anio']}',"
            . "fecha_modificacion = '{$fecha_modificacion}',usuario_modificacion = '{$usuario}'"
            . "WHERE cod_expediente_det='{$_POST['codigo']}'";
    $resultado=query($consulta,$conexion);
}

//INVESTIGACIONES - EDITAR
if ($_POST['tipo_registro']=="61")
{
        if($_POST['fecha']!='')
                    $fecha=fecha_sql($_POST['fecha']);
        $fecha_modificacion = date("Y-m-d");

         //ACTUALIZACION EXPEDIENTE       
        
        $consulta = "UPDATE expediente SET "
                . "descripcion = '{$_POST['descripcion']}',"
                . "institucion_publica = '{$_POST['institucion']}',"
                . "tipo_investigacion = '{$_POST['tipo_investigacion']}',"
                . "tipo_comparecencia_investigacion = '{$_POST['tipo_comparecencia']}',"
                . "tipo_falta_investigacion = '{$_POST['tipo_falta']}',"
                . "fecha = '{$fecha}',"
                . "fecha_modificacion = '{$fecha_modificacion}',"
                . "usuario_modificacion = '{$usuario}'"
                . " WHERE cod_expediente_det='{$_POST['codigo']}'";
        $resultado=query($consulta,$conexion);
        
}

//EXTEMPORANEAS - EDITAR
if ($_POST['tipo_registro']=="62")
{
        if($_POST['fecha']!='')
                    $fecha=fecha_sql($_POST['fecha']);
        $fecha_modificacion = date("Y-m-d");

         //ACTUALIZACION EXPEDIENTE       
        
        $consulta = "UPDATE expediente SET "
                . "descripcion = '{$_POST['descripcion']}',"
                . "fecha = '{$fecha}',"
                . "fecha_modificacion = '{$fecha_modificacion}',"
                . "usuario_modificacion = '{$usuario}'"
                . " WHERE cod_expediente_det='{$_POST['codigo']}'";
        $resultado=query($consulta,$conexion);
        
}

//DIAGNOSTICO - EDITAR
if ($_POST['tipo_registro']=="63")
{
        if($_POST['fecha']!='')
                    $fecha=fecha_sql($_POST['fecha']);
        $fecha_modificacion = date("Y-m-d");

         //ACTUALIZACION EXPEDIENTE       
        
        $consulta = "UPDATE expediente SET "
                . "descripcion = '{$_POST['descripcion']}',"
                . "fecha = '{$fecha}',"
                . "id_centro = '{$_POST['id_centro']}', "
                . "num_certificado = '{$_POST['num_certificado']}', "
                . "nombre_medico = '{$_POST['nombre_medico']}', "
                . "fecha_modificacion = '{$fecha_modificacion}',"
                . "usuario_modificacion = '{$usuario}'"
                . " WHERE cod_expediente_det='{$_POST['codigo']}'";
        $resultado=query($consulta,$conexion);
        
}

//EVALUACION DE ANTECEDENTES - EDITAR
else if ($_POST['tipo_registro']=="64")
{
    if($_POST['fecha']!='')
                    $fecha=fecha_sql($_POST['fecha']);
            
            $fecha_creacion = date('Y-m-d');
            $ascenso_anio_1="2016";
            $ascenso_anio_2="2017";
            $ascenso_anio_actual="2018";
    $fecha_modificacion = date('Y-m-d');

     //ACTUALIZACION EXPEDIENTE       

    $consulta = "UPDATE expediente SET "
            . "descripcion = '{$_POST['descripcion']}',"
            ."fecha='{$fecha}',
            ascenso_salario_anterior='{$_POST['ascenso_salario_anterior']}',
            ascenso_salario_nuevo='{$_POST['ascenso_salario_nuevo']}', 
            ascenso_nivel_anterior='{$_POST['ascenso_nivel_anterior']}',
            ascenso_nivel_nuevo='{$_POST['ascenso_nivel_nuevo']}',
            ascenso_cargo_anterior='{$_POST['ascenso_cargo_anterior']}',
            ascenso_cargo_nuevo='{$_POST['ascenso_cargo_nuevo']}', 
            ascenso_desempenio_1_1='{$_POST['ascenso_desempenio_1_1']}', 
            ascenso_desempenio_1_2='{$_POST['ascenso_desempenio_1_2']}', 
            ascenso_desempenio_2_1='{$_POST['ascenso_desempenio_2_1']}', 
            ascenso_desempenio_2_2='{$_POST['ascenso_desempenio_2_2']}',
            ascenso_desempenio_total='{$_POST['ascenso_desempenio_total']}',
            ascenso_desempenio_porcentaje='{$_POST['ascenso_desempenio_porcentaje']}',
            ascenso_conducta_1='{$_POST['ascenso_conducta_1']}',
            ascenso_conducta_2='{$_POST['ascenso_conducta_2']}',
            ascenso_conducta_actual='{$_POST['ascenso_conducta_actual']}',
            ascenso_conducta_porcentaje='{$_POST['ascenso_conducta_porcentaje']}',
            ascenso_participativa_actual='{$_POST['ascenso_participativa_actual']}',
            ascenso_participativa_porcentaje='{$_POST['ascenso_participativa_porcentaje']}',
            ascenso_investigacion='{$_POST['ascenso_investigacion']}',
            ascenso_puntaje_total='{$_POST['ascenso_puntaje_total']}',
            dejado='{$_POST['formato']}',"
            . "fecha_modificacion = '{$fecha_modificacion}',usuario_modificacion = '{$usuario}'"
            . "WHERE cod_expediente_det='{$_POST['codigo']}'";
    $resultado=query($consulta,$conexion);
}

//TRABAJO SOCIAL - EDITAR
else if ($_POST['tipo_registro']=="65")
{
    if($_POST['fecha_entrevista']!='')
                    $fecha_entrevista=fecha_sql($_POST['fecha_entrevista']);

            if($_POST['fecha_elaboracion']!='')
                    $fecha_elaboracion=fecha_sql($_POST['fecha_elaboracion']);       

    $fecha_modificacion = date('Y-m-d');

     //ACTUALIZACION EXPEDIENTE       

    $consulta = "UPDATE expediente SET "
            . "descripcion = '{$_POST['recomendaciones']}',"
            ."fecha_entrevista='{$cm_fecha_notificacion_ingreso}',
            fecha_elaboracion='{$cm_fecha_resolucion}',
            nombre_adulto_responsable='{$_POST['nombre_adulto_responsable']}', 
            cedula_adulto_responsable='{$_POST['cedula_adulto_responsable']}',
            motivo_investigacion='{$_POST['motivo_investigacion']}',
            socioeconomica_relaciones_familiares='{$_POST['socioeconomica_relaciones_familiares']}',
            socioeconomica_salud='{$_POST['socioeconomica_salud']}', 
            socioeconomica_vivienda='{$_POST['socioeconomica_vivienda']}', 
            socioeconomica_economica='{$_POST['socioeconomica_economica']}', 
            socioeconomica_ingresos='{$_POST['socioeconomica_ingresos']}', 
            socioeconomica_egresos='{$_POST['socioeconomica_egresos']}',
            socioeconomica_total_ingresos='{$_POST['socioeconomica_total_ingresos']}',
            socioeconomica_total_egresos='{$_POST['socioeconomica_total_egresos']}',
            socioeconomica_total_disponible='{$_POST['socioeconomica_total_disponible']}',
            situacion_encontrada='{$_POST['situacion_encontrada']}',
            labor_trabajador_social='{$_POST['labor_trabajador_social']}',
            diagnostico='{$_POST['diagnostico']}',
            recomendaciones='{$_POST['labor_trabajador_social']}',
            condicion_salud='{$_POST['labor_trabajador_social']}',
            especialistas_atencion='{$_POST['labor_trabajador_social']}',
            metodologia_atencion='{$_POST['labor_trabajador_social']}',
            conclusiones='{$_POST['labor_trabajador_social']}',"
            . "fecha_modificacion = '{$fecha_modificacion}',"
            . "usuario_modificacion = '{$usuario}'"
            . "WHERE cod_expediente_det='{$_POST['codigo']}'";
    $resultado=query($consulta,$conexion);
}

//RENOVACION DE CONTRATOS - EDITAR
else if ($_POST['tipo_registro']=="66")
{
    if($_POST['fecha_inicio']!='')
                    $fecha_inicio=fecha_sql($_POST['fecha_inicio']);

            if($_POST['fecha_fin']!='')
                    $fecha_fin=fecha_sql($_POST['fecha_fin']);

            if($_POST['fecha_resolucion']!='')
                    $fecha_resolucion=fecha_sql($_POST['fecha_resolucion']);
            
            if($_POST['fecha']!='')
                    $fecha=fecha_sql($_POST['fecha']);
            
            
    $fecha_modificacion = date('Y-m-d');

     //ACTUALIZACION EXPEDIENTE       

    $consulta = "UPDATE expediente SET "
            . "descripcion = '{$_POST['descripcion']}',"
            ."fecha='{$fecha}',
            fecha_inicio='{$fecha_inicio}',
            fecha_fin='{$fecha_fin}',
            fecha_resolucion='{$fecha_resolucion}',
            numero_resolucion='{$_POST['numero_resolucion']}',
            monto='{$_POST['salario_anterior']}',
            monto_nuevo='{$_POST['salario_nuevo']}', 
            proyecto_anterior='{$_POST['proyecto_anterior']}',
            proyecto='{$_POST['proyecto_nuevo']}',
            cod_cargo_anterior='{$_POST['cargo_anterior']}',
            cod_cargo_nuevo='{$_POST['cargo_nuevo']}',"
            . "fecha_modificacion = '{$fecha_modificacion}',usuario_modificacion = '{$usuario}'"
            . "WHERE cod_expediente_det='{$_POST['codigo']}'";
    $resultado=query($consulta,$conexion);
}

//RENOVACION DE CARGO - EDITAR
else if ($_POST['tipo_registro']=="67")
{
    
            
            if($_POST['fecha']!='')
                    $fecha=fecha_sql($_POST['fecha']);
            
            
    $fecha_modificacion = date('Y-m-d');

     //ACTUALIZACION EXPEDIENTE       

    $consulta = "UPDATE expediente SET "
            . "descripcion = '{$_POST['descripcion']}',"
            ."fecha='{$fecha}',
            monto='{$_POST['salario_anterior']}',
            monto_nuevo='{$_POST['salario_nuevo']}', 
            cod_cargo_anterior='{$_POST['cargo_anterior']}',
            cod_cargo_nuevo='{$_POST['cargo_nuevo']}',"
            . "fecha_modificacion = '{$fecha_modificacion}',usuario_modificacion = '{$usuario}'"
            . "WHERE cod_expediente_det='{$_POST['codigo']}'";
    $resultado=query($consulta,$conexion);
}

//REGISTRO DE CONTRATOS - EDITAR
else if ($_POST['tipo_registro']=="68")
{
        if($_POST['fecha_inicio']!='')
                $fecha_inicio=fecha_sql($_POST['fecha_inicio']);

        if($_POST['fecha_fin']!='')
                $fecha_fin=fecha_sql($_POST['fecha_fin']);

        if($_POST['fecha_resolucion']!='')
                $fecha_resolucion=fecha_sql($_POST['fecha_resolucion']);

        if($_POST['fecha']!='')
                $fecha=fecha_sql($_POST['fecha']);


        $fecha_modificacion = date('Y-m-d');

        //ACTUALIZACION EXPEDIENTE       

        $consulta = "UPDATE expediente SET
                descripcion = '{$_POST['descripcion']}',
                fecha='{$fecha_resolucion}',
                fecha_inicio='{$fecha_inicio}',
                fecha_fin='{$fecha_fin}',
                dias='{$_POST['dias']}',
                numero_resolucion='{$_POST['numero_resolucion']}',
                fecha_resolucion='{$fecha_resolucion}',
                proyecto='{$_POST['proyecto_nuevo']}',
                gerencia_nueva='{$_POST['gerencia_nueva']}',
                monto_nuevo='{$_POST['salario_nuevo']}', 
                cod_cargo_anterior='{$_POST['cargo_anterior']}',
                cod_cargo_nuevo='{$_POST['cargo_nuevo']}',
                fecha_modificacion = '{$fecha_modificacion}',
                usuario_modificacion = '{$usuario}'
        WHERE cod_expediente_det='{$_POST['codigo']}'";
        $resultado=query($consulta,$conexion);
}

//CURRICULO - EDITAR
if($_POST['tipo_registro']>=69 && $_POST['tipo_registro']<=79)
{
        $tipo_registro = $_POST['tipo_registro'];
        $nombre_documento  = (isset($_POST['nombre_documento']))  ? $_POST['nombre_documento']  : NULL;
        $fecha_vencimiento = str_replace('-', '/', $_POST['fecha_vencimiento']);
        $fecha_vencimiento = fecha_sql($_POST['fecha_vencimiento']);

        // Registrar en nomexpediente
        $consulta = "UPDATE expediente SET
                `descripcion` = '{$_POST['descripcion']}',
                `fecha_fin` = '{$fecha_vencimiento}'
                WHERE cod_expediente_det='{$_POST['codigo']}'";

        $resultado=query($consulta,$conexion);

        $consulta = "UPDATE `expediente_documento` SET
                `nombre_documento`  = '{$_POST['tipo_registro']}',
                `descripcion`       = '{$_POST['descripcion']}',
                `fecha_vencimiento` = '{$fecha_vencimiento}'
                WHERE cod_expediente_det='{$_POST['codigo']}'";
        $resultado=query($consulta,$conexion);
}

//Entrega Kit - GUARDAR
else if($_POST['tipo_registro']==80)
{

    $fecha = str_replace('-', '/', $_POST['fecha']);
    $fecha = fecha_sql($fecha);
    $fecha = date('Y-m-d');

    $tipo_registro = $_POST['tipo_registro'];

    $seccion_anterior  = (isset($_POST['seccion_anterior']))  ? $_POST['seccion_anterior']  : 0;
    $seccion_nueva  = (isset($_POST['seccion_nueva']))  ? $_POST['seccion_nueva']  : 0;
    $funcion_anterior  = (isset($_POST['funcion_anterior']))  ? $_POST['funcion_anterior']  : 0;
    $funcion_nueva  = (isset($_POST['funcion_nueva']))  ? $_POST['funcion_nueva']  : 0;
    $institucion_anterior  = (isset($_POST['institucion_anterior']))  ? $_POST['institucion_anterior']  : 0;

    $tipo_estudio  = (isset($_POST['tipo_estudio']))  ? $_POST['tipo_estudio']  : "";
    $nivel_actual  = (isset($_POST['nivel_actual']))  ? $_POST['nivel_actual']  : "";
    $cargo_estructura  = (isset($_POST['cargo_estructura']))  ? $_POST['cargo_estructura']  : "";
    $cargo_funcion  = (isset($_POST['cargo_funcion']))  ? $_POST['cargo_funcion']  : "";
    $concepto  = (isset($_POST['concepto']))  ? $_POST['concepto']  : "";
    
    $consulta = "UPDATE expediente SET
    `descripcion` = '{$_POST['descripcion']}',
    `cedula` = '{$cedula}',
    `seccion_anterior` = '{$seccion_anterior}',
    `seccion_nueva` = '{$seccion_nueva}',
    `funcion_anterior` = '{$funcion_anterior}',
    `funcion_nueva` = '{$funcion_nueva}',
    `institucion_anterior` = '{$institucion_anterior}',

    `tipo_estudio` = '{$tipo_estudio}',
    `nivel_actual` = '{$nivel_actual}',
    `cargo_estructura` = '{$cargo_estructura}',
    `cargo_funcion` = '{$cargo_funcion}',
    `concepto` = '{$concepto}'
    WHERE cod_expediente_det='{$_POST['codigo']}'";

    $resultado=query($consulta,$conexion);
}

//Registro incidente - GUARDAR
else if($_POST['tipo_registro']==81)
{

        $fecha_resolucion = str_replace('-', '/', $_POST['fecha_resolucion']);
        $fecha_resolucion = fecha_sql($fecha_resolucion);

        $fecha_inicio = str_replace('-', '/', $_POST['fecha_inicio']);
        $fecha_inicio = fecha_sql($fecha_inicio);

        $fecha_hora_inicio = str_replace('-', '/', $_POST['fecha_hora_inicio']);
        $fecha_hora_inicio = fecha_sql($fecha);
    $fecha = date('Y-m-d');

    $tipo_registro = $_POST['tipo_registro'];
        
    //fecha,fecha_resolucion,fecha_inicio,fecha_hora_inicio,posicion_anterior,cargo_estructura,monto,monto_nuevo,comentarios_1,comentarios_2,analista,concepto

    $posicion_anterior  = (isset($_POST['posicion_anterior']))  ? $_POST['posicion_anterior']  : 0;
    $cargo_estructura  = (isset($_POST['cargo_estructura'])) ? utf8_decode($_POST['cargo_estructura'])  : 0;
    $monto  = (isset($_POST['monto']))  ? $_POST['monto']  : 0;
    $monto_nuevo  = (isset($_POST['monto_nuevo'])) ? $_POST['monto_nuevo']  : 0;
    $comentarios_1  = (isset($_POST['comentarios_1'])) ? utf8_decode($_POST['comentarios_1'])  : "";
    $comentarios_2  = (isset($_POST['comentarios_2'])) ? utf8_decode($_POST['comentarios_2'])  : "";
    $analista  = (isset($_POST['analista'])) ? $_POST['analista']  : "";
    $concepto  = (isset($_POST['concepto'])) ? utf8_decode($_POST['concepto'] ) : "";
    
    $consulta = "UPDATE expediente SET
    `descripcion` = '{$_POST['descripcion']}',
    `cedula` = '{$cedula}',
    `fecha_resolucion` = '{$fecha_resolucion}',
    `fecha_inicio` = '{$fecha_inicio}',
    `posicion_anterior` = '{$posicion_anterior}',
    `cargo_estructura` = '{$cargo_estructura}',
    `monto` = '{$monto}',
    `monto_nuevo` = '{$monto_nuevo}',
    `comentarios_1` = '{$comentarios_1}',
    `comentarios_2` = '{$comentarios_2}',
    `analista` = '{$analista}',
    `concepto` = '{$concepto}'
    WHERE cod_expediente_det='{$_POST['codigo']}'";

    $resultado=query($consulta,$conexion);
        if(isset($_POST['numcuo1']) && $resultado)
        {
                $consulta="INSERT INTO nomprestamos_cabecera_tmp
                SET 
                numpre          = $_POST[numpre],
                ficha           = $_POST[ficha],
                fechaapro       = '".fecha_sql($_POST[fechaap])."',
                fecpricup       = '".fecha_sql($_POST[fecha1])."',
                monto           = $_POST[montopre],
                estadopre       = 'Pendiente',
                detalle         = '$_POST[descrip]',
                codigopr        = '$_POST[tipo]',
                codnom          = $_SESSION[codigo_nomina],
                totpres         = $_POST[montopre],
                cuotas          = $_POST[numcuota],
                mtocuota        = $_POST[montocuota],
                diciembre       = '$_POST[diciembre]',
                gastos_admon    = '$_POST[gastos_admon]',
                id_tipoprestamo = '$_POST[tipos_prestamos]',
                frededu         = '$_POST[frededu]'";
                //echo $consulta;exit;
                if($resultado=query($consulta,$conexion))
                {
                $i=0;
                while($i<$_POST['numcuota'])
                {
                        $i+=1;
                        $numcuo="numcuo".$i;
                        $vence="vence".$i;
                        $cad=explode("/",$_POST[$vence]);
                        $salini="salini".$i;
                        $mtocuo="mtocuo".$i;
                        $salfin="salfin".$i;
                        $consulta="INSERT INTO nomprestamos_detalles_tmp SET 
                        numpre     = $_POST[numpre],
                        ficha      = $_POST[ficha],
                        numcuo     = '".$_POST[$numcuo]."',
                        fechaven   = '".fecha_sql($_POST[$vence])."',
                        anioven    = $cad[2],
                        mesven     = $cad[1],
                        salinicial = ".$_POST[$salini].",
                        montocuo   = ".$_POST[$mtocuo].",
                        salfinal   = ".$_POST[$salfin].",
                        estadopre  = 'Pendiente',
                        codnom     = $_SESSION[codigo_nomina]";
                        $resultado2=query($consulta,$conexion);
                }
                if($resultado2)
                {
                        
                        ?>
                        <script type="text/javascript">
                        alert("PRESTAMO GUARDADO EXITOSAMENTE!!!")
                        parent.cont.location.href="prestamos_list.php"
                        </script>
                        <?php	
                }
                else
                {
                        ?>
                        <script type="text/javascript">
                        alert("PRESTAMO NO GUARDADO !!!")
                        </script>
                        <?php	
                }
                }
                else
                {
                        ?>
                        <script type="text/javascript">
                        alert("PRESTAMO NO GUARDADO !!!")
                        </script>
                        <?php	
                }
        }
        else
        {
                echo "no se registro en prestamo";
        }
        
        
        $consulta = "UPDATE expediente SET "
            . "descripcion = '{$descripcion}',
             monto = '{$monto}',            
             ajuste_discrecional = '{$precio}',            
             monto_nuevo = '{$cuota}',            
             numero_decreto = '{$_POST['numpre']}',            
             concepto = '{$observacion_jefe}'"            
            . " WHERE cod_expediente_det='{$_POST['codigo']}'";
                $resultado=query($consulta,$conexion); 
}

//EXCEDENTE  - EDITAR
    else if ($_POST['tipo_registro']=="83")
    {       

        if($_POST['fecha']!='')
                $fecha=fecha_sql($_POST['fecha']);
        $monto_nuevo = str_replace(",","",$_POST['monto_nuevo']); 

        // $fecha_modificacion = date('Y-m-d');  
        // $posicion_anterior  = (isset($_POST['posicion_anterior']))  ? $_POST['posicion_anterior']  : 0;
        // $cargo_estructura  = (isset($_POST['cargo_estructura']))  ? $_POST['cargo_estructura']  : 0;
        $monto  = (isset($_POST['monto']))  ? $_POST['monto']  : 0;
        $cuota  = (isset($_POST['cuota']))  ? $_POST['cuota']  : 0;
        $precio  = (isset($_POST['precio']))  ? $_POST['precio']  : 0;
        //$monto_nuevo  = (isset($_POST['monto_nuevo']))  ? $_POST['monto_nuevo']  : 0;
        // $comentarios_1  = (isset($_POST['comentarios_1']))  ? $_POST['comentarios_1']  : 0;
        // $comentarios_2  = (isset($_POST['comentarios_2']))  ? $_POST['comentarios_2']  : "";
        $descripcion  = (isset($_POST['descripcion']))  ? $_POST['descripcion']  : "";
        $observacion_jefe  = (isset($_POST['observacion_jefe']))  ? $_POST['observacion_jefe']  : "";    
        //echo $numpre  = (isset($_POST['numpre']))  ? $_POST['numpre']  : "";    
        $cedula  = (isset($_POST['cedula']))  ? $_POST['cedula']  : "";    
        $codigo  = (isset($_POST['codigo']))  ? $_POST['codigo']  : "";    
        //$nombre;
        
        //ACUTALIZACION EXPEDIENTE
        "consulta1";
        $consulta="INSERT INTO nomprestamos_cabecera_tmp "
        . "SET numpre='$_POST[numpre]', "
        . "ficha='$ficha_persona',"
        . "fechaapro='".fecha_sql($_POST[fechaap])."', "
        . "fecpricup='".fecha_sql($_POST[fecha1])."', "
        . "monto=$_POST[montopre], "
        . "estadopre='Pendiente', "
        . "detalle='$_POST[descrip]', "
        . "codigopr='$_POST[tipo]', "
        . "codnom=$_SESSION[codigo_nomina], "
        . "totpres=$_POST[montopre], "
        . "cuotas=$_POST[numcuota], "
        . "mtocuota=$_POST[montocuota], "
        . "diciembre='$_POST[diciembre]',"
        . "gastos_admon='$_POST[gastos_admon]',"
        . "id_tipoprestamo='$_POST[tipos_prestamos]',"
        . "frededu='$_POST[frededu]'" 
        . "ON DUPLICATE KEY UPDATE "
        . "ficha='$ficha_persona',"
        . "fechaapro='".fecha_sql($_POST[fechaap])."', "
        . "fecpricup='".fecha_sql($_POST[fecha1])."', "
        . "monto=$_POST[montopre], "
        . "estadopre='Pendiente', "
        . "detalle='$_POST[descrip]', "
        . "codigopr='$_POST[tipo]', "
        . "codnom=$_SESSION[codigo_nomina], "
        . "totpres=$_POST[montopre], "
        . "cuotas=$_POST[numcuota], "
        . "mtocuota=$_POST[montocuota], "
        . "diciembre='$_POST[diciembre]',"
        . "gastos_admon='$_POST[gastos_admon]',"
        . "id_tipoprestamo='$_POST[tipos_prestamos]'";
        $consulta;
        if($resultado=query($consulta,$conexion))
        {
                $select = "SELECT COALESCE(numpre, '----') numpre FROM nomprestamos_detalles_tmp WHERE numpre = '".$_POST['numpre']."'";
                $resultado_select=query($select,$conexion);
                $fetch_select = fetch_array($resultado_select);
                if($fetch_select["numpre"] != "----"){
                       $delete_select =  "DELETE FROM nomprestamos_detalles_tmp WHERE numpre = '".$_POST['numpre']."'";
                       query($delete_select,$conexion);
                }
                $i=0;
                while($i<$_POST['numcuota'])
                {
                        $i+=1;
                        $numcuo="numcuo".$i;
                        $vence="vence".$i;
                        $cad=explode("/",$_POST[$vence]);
                        $salini="salini".$i;
                        $mtocuo="mtocuo".$i;
                        $salfin="salfin".$i;
                        $consulta="INSERT INTO nomprestamos_detalles_tmp SET 
                        numpre=$_POST[numpre],
                        ficha=$ficha_persona,
                        numcuo='".$_POST[$numcuo]."',
                        fechaven='".fecha_sql($_POST[$vence])."',
                        anioven=$cad[2],
                        mesven=$cad[1],
                        salinicial=".$_POST[$salini].",
                        montocuo=".$_POST[$mtocuo].",
                        salfinal=".$_POST[$salfin].",
                        estadopre='Pendiente',
                        codnom=$_SESSION[codigo_nomina]";
                        $resultado2=query($consulta,$conexion);
                }
        }
        
        
        $consulta = "UPDATE expediente SET "
            . "descripcion = '{$descripcion}',
             monto = '{$monto}',            
             ajuste_discrecional = '{$precio}',            
             monto_nuevo = '{$cuota}',            
             numero_decreto = '{$_POST['numpre']}',            
             concepto = '{$observacion_jefe}'"            
            . " WHERE cod_expediente_det='{$_POST['codigo']}'";
                $resultado=query($consulta,$conexion); 
                        

    }    
    
/*
//ENTREGA DE IMPLEMENTOS - EDITAR
else if ($_POST['tipo_registro']=="79")
{
    
            
            if($_POST['fecha']!='')
                    $fecha=fecha_sql($_POST['fecha']);
            
            
    $fecha_modificacion = date('Y-m-d');
    
    //EXPEDIENTE - ELIMINAR
    $delete_implemento="DELETE FROM expediente_implemento "
                    . "WHERE cod_expediente_det='{$_POST['codigo']}'";
    $resultado_delete=query($delete_implemento,$conexion);
    
     if(isset($_POST["expediente_implemento"]))
    {
        $data=$_POST["expediente_implemento"];
        $data=json_decode($data,true);
        for($i=0; $i < count($data); $i++)
        { 

            $consulta_implemento="INSERT INTO expediente_implemento
                (id, 
                id_implemento, 
                marca, 
                modelo, 
                talla, 
                color,
                fecha_entrega,
                fecha_vencimiento,
                cantidad,
                cod_expediente_det)
                VALUES  
                ('',
                '{$data[$i]["articulo"]}',"
                . "'{$data[$i]["marca"]}', "
                . "'{$data[$i]["modelo"]}',"
                . "'{$data[$i]["talla"]}',"
                . "'{$data[$i]["color"]}',"
                . "'{$data[$i]["cantidad"]}',"
                . "'{$data[$i]["entrega"]}',"
                . "'{$data[$i]["vencimiento"]}',"
                . "'{$_POST['codigo']}')";    
    //            echo $consulta;
                $resultado_implemento=query($consulta_implemento,$conexion);
        }
    }

     //ACTUALIZACION EXPEDIENTE       

    $consulta = "UPDATE expediente SET "
            . "descripcion = '{$_POST['descripcion']}',"
            ."fecha='{$fecha}',"
            . "fecha_modificacion = '{$fecha_modificacion}',"
            . "usuario_modificacion = '{$usuario}'"
            . "WHERE cod_expediente_det='{$_POST['codigo']}'";
    $resultado=query($consulta,$conexion);
}

//AUSENCIAS - EDITAR
if ($_POST['tipo_registro']=="70"){
        if($_POST['fecha_inicio']!='')
                $fecha_inicio=fecha_sql($_POST['fecha_inicio']);

        if($_POST['fecha_fin']!='')
                $fecha_fin=fecha_sql($_POST['fecha_fin']);

        if($_POST['fecha_resolucion']!='')
                $fecha_resolucion=fecha_sql($_POST['fecha_resolucion']);	

        if($_POST['fecha']!='')
                $fecha=fecha_sql($_POST['fecha']);

          if($_POST['horas']!='')
        {
            $horas=$_POST['horas'];
        }
        else
        {
            $horas=0;
        }
        
        if($_POST['minutos']!='')
        {
            $minutos=$_POST['minutos'];
        }
        else
        {
            $minutos=0;
        }
        
        if($_POST['restante']!='')
        {
            $restantes=$_POST['restante'];
        }
        else
        {
            $restantes=0;
        }
        
        if($_POST['dias']!='')
        {
            $dias=$_POST['dias'];
        }
        else
        {
            $dias=0;
        }  
        
        if($_POST['duracion']!='')
        {
            $duracion=$_POST['duracion'];
        }
        else
        {
            $duracion=0;
        }  

        $duracion = $dias*8 + $horas +($minutos/60);
        
        $fecha_modificacion = date("Y-m-d");

        
                $consulta = "UPDATE expediente SET "
                . "descripcion = '{$_POST['descripcion']}',"
                . "desde = '{$_POST['desde']}',"
                . "fecha_inicio = '{$fecha_inicio}',"
                . "hasta = '{$_POST['hasta']}',"
                . "fecha_fin = '{$fecha_fin}',"
                . "dias = '{$dias}',"
                . "horas = '{$horas}',"
                . "minutos = '{$minutos}',"
                . "duracion = '{$duracion}',"
                . "numero_resolucion = '{$_POST['numero_resolucion']}',"
                . "fecha = '{$fecha}',"
                . "fecha_resolucion = '{$fecha_resolucion}',"
                . "id_centro = '{$_POST['id_centro']}', "
                . "nombre_medico = '{$_POST['nombre_medico']}', "
                . "proyecto = '{$_POST['proyecto']}', "
                . "subtipo = '{$_POST['tipo_tiporegistro']}',"
                . "fecha_modificacion = '{$fecha_modificacion}',"
                . "usuario_modificacion = '{$usuario}'"
                . " WHERE cod_expediente_det='{$_POST['codigo']}'";
        $resultado=query($consulta,$conexion);

}

//TARDANZAS - EDITAR
if ($_POST['tipo_registro']=="71"){
        if($_POST['fecha_inicio']!='')
                $fecha_inicio=fecha_sql($_POST['fecha_inicio']);

        if($_POST['fecha_fin']!='')
                $fecha_fin=fecha_sql($_POST['fecha_fin']);

        if($_POST['fecha_resolucion']!='')
                $fecha_resolucion=fecha_sql($_POST['fecha_resolucion']);	

        if($_POST['fecha']!='')
                $fecha=fecha_sql($_POST['fecha']);

          if($_POST['horas']!='')
        {
            $horas=$_POST['horas'];
        }
        else
        {
            $horas=0;
        }
        
        if($_POST['minutos']!='')
        {
            $minutos=$_POST['minutos'];
        }
        else
        {
            $minutos=0;
        }
        
        if($_POST['restante']!='')
        {
            $restantes=$_POST['restante'];
        }
        else
        {
            $restantes=0;
        }
        
        if($_POST['dias']!='')
        {
            $dias=$_POST['dias'];
        }
        else
        {
            $dias=0;
        }  
        
        if($_POST['duracion']!='')
        {
            $duracion=$_POST['duracion'];
        }
        else
        {
            $duracion=0;
        }  

        $duracion = $dias*8 + $horas +($minutos/60);
        
        $fecha_modificacion = date("Y-m-d");

        
                $consulta = "UPDATE expediente SET "
                . "descripcion = '{$_POST['descripcion']}',"
                . "desde = '{$_POST['desde']}',"
                . "fecha_inicio = '{$fecha_inicio}',"
                . "hasta = '{$_POST['hasta']}',"
                . "fecha_fin = '{$fecha_fin}',"
                . "dias = '{$dias}',"
                . "horas = '{$horas}',"
                . "minutos = '{$minutos}',"
                . "duracion = '{$duracion}',"
                . "numero_resolucion = '{$_POST['numero_resolucion']}',"
                . "fecha = '{$fecha}',"
                . "fecha_resolucion = '{$fecha_resolucion}',"
                . "id_centro = '{$_POST['id_centro']}', "
                . "nombre_medico = '{$_POST['nombre_medico']}', "
                . "proyecto = '{$_POST['proyecto']}', "
                . "subtipo = '{$_POST['tipo_tiporegistro']}',"
                . "fecha_modificacion = '{$fecha_modificacion}',"
                . "usuario_modificacion = '{$usuario}'"
                . " WHERE cod_expediente_det='{$_POST['codigo']}'";
        $resultado=query($consulta,$conexion);

}*/
?>
