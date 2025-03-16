<?php

//ESTUDIO ACADEMICOS - GUARDAR
    if ($_POST['tipo_registro']=="1"){
            if($_POST['fecha_inicio']!='')
                    $fecha_inicio=fecha_sql($_POST['fecha_inicio']);				
            if($_POST['fecha_culminacion']!='')
                    $fecha_culminacion=fecha_sql($_POST['fecha_culminacion']);
            $fecha_creacion = date("Y-m-d");
            $fecha = date("Y-m-d");
             //INSERCIÓN EXPEDIENTE
            $consulta="INSERT INTO expediente
                        (cod_expediente_det, cedula, descripcion, institucion_educativa_nueva, titulo_profesional, idoneidad, ejerce,
                        fecha, fecha_inicio, fecha_fin, dias, tipo, subtipo, fecha_creacion, usuario_creacion )
                        VALUES  
                        ('','{$cedula}','{$_POST['descripcion']}', '{$_POST['institucion_educativa_nueva']}','{$_POST['titulo_profesional']}', "
                        . "'{$_POST['idoneidad']}','{$_POST['ejerce']}','{$fecha}','{$fecha_inicio}', '{$fecha_culminacion}','{$_POST['duracion']}',"
                        . "'{$_POST['tipo_registro']}','{$_POST['tipo_tiporegistro']}','{$fecha_creacion}','{$usuario}')";
            $resultado=query($consulta,$conexion);

    }
     //CAPACITACION - GUARDAR
    else if ($_POST['tipo_registro']=="2"){
            if($_POST['fecha_inicio']!='')
                    $fecha_inicio=fecha_sql($_POST['fecha_inicio']);				
            if($_POST['fecha_fin']!='')
                    $fecha_fin=fecha_sql($_POST['fecha_fin']);
            $fecha_creacion = date("Y-m-d");
            $fecha = date("Y-m-d");
             //INSERCIÓN EXPEDIENTE
            $consulta="INSERT INTO expediente
                        (cod_expediente_det, cedula, descripcion, pagado_por_emp, institucion, nivel_actual, fecha,
                        fecha_inicio, fecha_fin, dias, costo_persona, num_participantes, nombre_especialista, tipo, subtipo, fecha_creacion, usuario_creacion )
                        VALUES  
                        ('','{$cedula}','{$_POST['descripcion']}', '{$_POST['pagado_por_emp']}', '{$_POST['institucion']}', "
                        . "'{$_POST['nivel_actual']}','{$fecha}', '{$fecha_inicio}', '{$fecha_fin}','{$_POST['dias']}',"
                        . "'{$_POST['costo_persona']}','{$_POST['num_participantes']}','{$_POST['nombre_especialista']}',"
                        . "'{$_POST['tipo_registro']}','{$_POST['tipo_tiporegistro']}','{$fecha_creacion}','{$usuario}')";
            $resultado=query($consulta,$conexion);
    }

    //PERMISOS - GUARDAR
    else if ($_POST['tipo_registro']=="4"){
            if($_POST['fecha_inicio']!='')
                    $fecha_inicio=fecha_sql($_POST['fecha_inicio']);

            if($_POST['fecha_fin']!='')
                    $fecha_fin=fecha_sql($_POST['fecha_fin']);

            if($_POST['fecha_aprobado']!='')
                    $fecha_aprobado=fecha_sql($_POST['fecha_aprobado']);

            if($_POST['fecha_enterado']!='')
                    $fecha_enterado=fecha_sql($_POST['fecha_enterado']);	

            if($_POST['fecha']!='')
                    $fecha=fecha_sql($_POST['fecha']);

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
            $fecha_creacion = date("Y-m-d");
            
             //INSERCIÓN EXPEDIENTE
            $consulta="INSERT INTO expediente
                        (cod_expediente_det, cedula, descripcion, desde, fecha_inicio, hasta, fecha_fin, dias, horas, minutos, duracion,
                        numero_resolucion, fecha, fecha_aprobado, fecha_enterado, tipo, subtipo, fecha_creacion, usuario_creacion )
                        VALUES  
                        ('','{$cedula}','{$_POST['descripcion']}', '{$_POST['desde']}', '{$fecha_inicio}','{$_POST['hasta']}', "
                        . "'{$fecha_fin}','{$dias}','{$horas}','{$minutos}','{$duracion}',"
                        . "'{$_POST['numero_resolucion']}', '{$fecha}','{$fecha_aprobado}', '{$fecha_enterado}',"
                        . "'{$_POST['tipo_registro']}','{$_POST['tipo_tiporegistro']}','{$fecha_creacion}','{$usuario}')";
//            echo $consulta;
            $resultado=query($consulta,$conexion);


    }

    //AMONESTACIONES  - GUARDAR
    else if ($_POST['tipo_registro']=="5")
    {
            if($_POST['fecha_amonestacion']!='')
                $fecha_amonestacion=fecha_sql($_POST['fecha_amonestacion']);
            $fecha_creacion = date("Y-m-d");
            //INSERCIÓN EXPEDIENTE
            $consulta="INSERT INTO expediente
                        (cod_expediente_det, cedula, descripcion, fecha, numeral, numeral_descripcion, articulo, tipo, subtipo, fecha_creacion, usuario_creacion )
                        VALUES  
                        ('','{$cedula}','{$_POST['descripcion']}', '{$fecha_amonestacion}', '{$_POST['numeral']}', "
                        . "'{$_POST['numeral_descripcion']}', '{$_POST['articulo']}', '{$_POST['tipo_registro']}',"
                        . "'{$_POST['tipo_tiporegistro']}','{$fecha_creacion}','{$usuario}')";
            $resultado=query($consulta,$conexion);

            //INSERCIÓN AMONESTACION
            $ultimo_id = mysqli_insert_id($conexion);
            if ($_POST['tipo_tiporegistro']==24)
                $tipo_amonestacion="VERBAL";
            else
                $tipo_amonestacion="ESCRITA";
             $consulta="INSERT INTO amonestaciones
                        (id_amonestacion, usr_uid, fecha, numeral_numero, numeral_descripcion, articulo, motivo, tipo, cod_expediente_det)
                        VALUES  
                        ('','{$user_uid}','{$fecha_amonestacion}', '{$_POST['numeral']}', '{$_POST['numeral_descripcion']}', "
                        . "'{$_POST['articulo']}', '{$_POST['descripcion']}', '{$_POST['tipo_amonestacion']}',"
                        . "'{$ultimo_id}')";
            $resultado=query($consulta,$conexion);
            

    }                
    //SUSPENSIONES - GUARDAR
    else if ($_POST['tipo_registro']=="6")
    {
            if($_POST['fecha_resolucion']!='')
                    $fecha_resolucion=fecha_sql($_POST['fecha_resolucion']);				
            if($_POST['fecha_desde']!='')
                    $fecha_desde=fecha_sql($_POST['fecha_desde']);                        
            if($_POST['fecha_hasta']!='')
                    $fecha_hasta=fecha_sql($_POST['fecha_hasta']);
            $fecha_creacion = date("Y-m-d");
            //INSERCIÓN EXPEDIENTE
            $consulta="INSERT INTO expediente
                        (cod_expediente_det, cedula, descripcion, dias, fecha_inicio, fecha_fin, fecha, numero_resolucion, numeral, 
                        numeral_descripcion, articulo, tipo, fecha_creacion, usuario_creacion)
                        VALUES  
                        ('','{$cedula}','{$_POST['descripcion']}', '{$_POST['dias']}', '{$fecha_desde}', '{$fecha_hasta}',"
                        . "'{$fecha_resolucion}', '{$_POST['numero_resolucion']}', '{$_POST['numeral']}',"
                        . "'{$_POST['numeral_descripcion']}','{$_POST['articulo']}','{$_POST['tipo_registro']}','{$fecha_creacion}','{$usuario}')";
            $resultado=query($consulta,$conexion);

            //INSERCIÓN SUSPENSION
            $ultimo_id = mysqli_insert_id($conexion);

             $consulta="INSERT INTO suspensiones
                        (id_suspension, usr_uid, fecha_resolucion, fecha_desde, fecha_hasta, dias, numeral_numero, numeral_descripcion,
                        motivo, nro_resolucion, articulo, cod_expediente_det)
                        VALUES  
                        ('','{$user_uid}','{$fecha_resolucion}', '{$fecha_desde}', '{$fecha_hasta}','{$_POST['dias']}',"
                        . "'{$_POST['numeral']}', '{$_POST['numeral_descripcion']}', '{$_POST['descripcion']}','{$_POST['numero_resolucion']}',"
                        . "'{$_POST['articulo']}','{$ultimo_id}')";
            $resultado=query($consulta,$conexion);


    }   

    //RENUNCIAS - GUARDAR
    else if ($_POST['tipo_registro']=="7")
    {
            if($_POST['fecha_resolucion']!='')
                    $fecha_resolucion=fecha_sql($_POST['fecha_resolucion']);
            if($_POST['fecha']!='')
                    $fecha=fecha_sql($_POST['fecha']);
            $fecha_creacion = date("Y-m-d");
            //INSERCIÓN EXPEDIENTE
            $consulta="INSERT INTO expediente
                        (cod_expediente_det, cedula, descripcion, fecha, numero_resolucion, fecha_resolucion, tipo, fecha_creacion, usuario_creacion)
                        VALUES  
                        ('','{$cedula}','{$_POST['descripcion']}', '{$fecha}','{$_POST['numero_resolucion']}', "
                        . "'{$fecha_resolucion}','{$_POST['tipo_registro']}','{$fecha_creacion}','{$usuario}')";
            $resultado=query($consulta,$conexion);

            //INSERCIÓN RENUNCIA
            $ultimo_id = mysqli_insert_id($conexion);

             $consulta="INSERT INTO renuncia
                        (id_renuncia, usr_uid, fecha, nro_resolucion, fecha_resolucion, observaciones, cod_expediente_det)
                        VALUES  
                        ('','{$user_uid}','{$fecha}','{$_POST['numero_resolucion']}','{$fecha_resolucion}','{$_POST['descripcion']}',"
                        . "'{$ultimo_id}')";
            $resultado=query($consulta,$conexion);
    }

    //DESTITUCIONES - GUARDAR
    else if ($_POST['tipo_registro']=="8")
    {
            if($_POST['fecha_resolucion']!='')
                    $fecha_resolucion=fecha_sql($_POST['fecha_resolucion']);

            if($_POST['fecha_notificacion']!='')
                    $fecha_notificacion=fecha_sql($_POST['fecha_notificacion']);

            if($_POST['fecha_edicto']!='')
                    $fecha_edicto=fecha_sql($_POST['fecha_edicto']);
            $fecha_creacion = date("Y-m-d");

            //INSERCIÓN EXPEDIENTE
            $consulta="INSERT INTO expediente
                        (cod_expediente_det, cedula, descripcion, fecha, fecha_resolucion, fecha_notificacion, fecha_edicto, numero_resolucion, 
                         numero_edicto, tipo, fecha_creacion, usuario_creacion)
                        VALUES  
                        ('','{$cedula}','{$_POST['descripcion']}','{$fecha_resolucion}', '{$fecha_resolucion}', '{$fecha_notificacion}', '{$fecha_edicto}',"
                        . "'{$_POST['numero_resolucion']}', '{$_POST['numero_edicto']}', '{$_POST['tipo_registro']}','{$fecha_creacion}','{$usuario}')";
            $resultado=query($consulta,$conexion);

            //INSERCIÓN DESTITUCION
            $ultimo_id = mysqli_insert_id($conexion);

             $consulta="INSERT INTO destituciones
                        (id_destitucion, usr_uid, nro_resolucion, fecha_resolucion, fecha_notificacion, nro_edicto, fecha_edicto, 
                        observaciones, cod_expediente_det)
                        VALUES  
                        ('','{$user_uid}','{$_POST['numero_resolucion']}','{$fecha_resolucion}', '{$fecha_notificacion}',"
                        . "'{$_POST['numero_edicto']}','{$fecha_edicto}','{$_POST['descripcion']}','{$ultimo_id}')";
            $resultado=query($consulta,$conexion);

    } 

//MOVIMIENTO DE PERSONAL
    else if ($_POST['tipo_registro']=="9"){
        if($_POST['fecha_resolucion']!='')
            $fecha_resolucion=fecha_sql($_POST['fecha_resolucion']);
        if($_POST['fecha_inicio']!='')
            $fecha_inicio=fecha_sql($_POST['fecha_inicio']);
        if($_POST['fecha_fin']!='')
            $fecha_fin=fecha_sql($_POST['fecha_fin']);
            
        $fecha_creacion = date("Y-m-d");
        
        $consulta_personal="SELECT * FROM nompersonal WHERE cedula='$cedula'";
        $resultado_personal=query($consulta_personal,$conexion);
        $fetch_personal=fetch_array($resultado_personal,$conexion);       
        $gerencia_anterior=$fetch_personal['codnivel1'];
        $departamento_anterior=$fetch_personal['codnivel2'];
        $seccion_anterior=$fetch_personal['codnivel3'];        
        $cod_cargo_anterior=$fetch_personal['codcargo'];
        $funcion_anterior=$fetch_personal['nomfuncion_id'];
       
        
        //INSERCIÓN EXPEDIENTE
        $consulta="INSERT INTO expediente
                    (cod_expediente_det, cedula, descripcion, fecha, fecha_resolucion, fecha_inicio, fecha_fin, numero_resolucion, gerencia_anterior, 
                    departamento_anterior, seccion_anterior,cod_cargo_anterior, funcion_anterior, institucion_anterior, gerencia_nueva, 
                    departamento_nuevo, seccion_nueva, cod_cargo_nuevo, funcion_nueva, institucion_nueva, motivo_traslado,tipo, subtipo, 
                    fecha_creacion, usuario_creacion)
                    VALUES  
                    ('','{$cedula}','{$_POST['descripcion']}','{$fecha_resolucion}','{$fecha_resolucion}','{$fecha_inicio}','{$fecha_fin}',"
                    . "'{$_POST['numero_resolucion']}','{$gerencia_anterior}','{$departamento_anterior}','{$seccion_anterior}',"
                    . "'{$cod_cargo_anterior}','{$funcion_anterior}','{$_POST['institucion_anterior']}','{$_POST['gerencia_nueva']}',"
                    . "'{$_POST['departamento_nuevo']}','{$_POST['seccion_nueva']}','{$_POST['cargo_nuevo']}','{$_POST['funcion_nueva']}',"
                    . "'{$_POST['institucion_nueva']}','{$_POST['motivo_traslado']}', "
                    . "'{$_POST['tipo_registro']}','{$_POST['tipo_tiporegistro']}','{$fecha_creacion}','{$usuario}')";
        $resultado=query($consulta,$conexion);
    } 


    //EVALUACION DE DESEMPEÑO - GUARDAR
    else if ($_POST['tipo_registro']=="10"){
            if($_POST['fecha']!='')//CAMBIAR fecha_salida POR fecha_aplicacion.
                    $fecha=fecha_sql($_POST['fecha']);
            $fecha_creacion = date("Y-m-d");
            //INSERCIÓN EXPEDIENTE
            $consulta="INSERT INTO expediente
                        (cod_expediente_det, cedula, descripcion, fecha, puntaje,
                         tipo, subtipo, fecha_creacion, usuario_creacion)
                        VALUES  
                        ('','{$cedula}','{$_POST['descripcion']}','{$fecha}','{$_POST['puntaje']}', "
                        . "'{$_POST['tipo_registro']}','{$_POST['tipo_tiporegistro']}','{$fecha_creacion}','{$usuario}')";
            $resultado=query($consulta,$conexion);
    } 


    //VACACIONES - GUARDAR
    else if ($_POST['tipo_registro']=="11"){            

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
            $fecha_creacion = date("Y-m-d");

             //INSERCIÓN EXPEDIENTE
            $consulta="INSERT INTO expediente
                        (cod_expediente_det, cedula, descripcion, fecha_inicio, fecha_fin, fecha_inicio_periodo, fecha_fin_periodo, fecha,
                        fecha_resolucion, numero_resolucion, dias, restante, periodo_vacacion, tipo, subtipo, fecha_creacion, usuario_creacion)
                        VALUES  
                        ('','{$cedula}','{$_POST['descripcion']}', '{$fecha_inicio}', '{$fecha_fin}','{$fecha_inicio_periodo}','{$fecha_fin_periodo}', "
                        . "'{$fecha}','{$fecha_resolucion}', '{$numero_resolucion}','{$_POST['dias']}','{$_POST['restante']}','{$periodo_vacacion}',"
                        . "'{$_POST['tipo_registro']}',{$_POST['tipo_tiporegistro']}','{$fecha_creacion}','{$usuario}')";
           
            $resultado=query($consulta,$conexion);
            
    } 

    //TIEMPO COMPENSATORIO  - GUARDAR
    else if ($_POST['tipo_registro']=="12"){

            if($_POST['fecha']!='')
                    $fecha=fecha_sql($_POST['fecha']);

            if($_POST['fecha_efectividad']!='')
                    $fecha_efectividad=fecha_sql($_POST['fecha_efectividad']);

            if($_POST['fecha_aprobado']!='')
                    $fecha_aprobado=fecha_sql($_POST['fecha_aprobado']);

            if($_POST['fecha_enterado']!='')
                    $fecha_enterado=fecha_sql($_POST['fecha_enterado']); 


        $tipo_justificacion=3;
        $tipo=$_POST['tipo'];
        $dias=$_POST['dias'];

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

        $tiempo=($dias*8)*60 + $horas*60 + $minutos;
        $tiempo=$tiempo/60;                    


        if($tipo==2)
            $tiempo=-($tiempo);

            //INSERCIÓN EXPEDIENTE
            $consulta="INSERT INTO expediente
                        (cod_expediente_det, cedula, descripcion, fecha, fecha_efectividad, fecha_aprobado, fecha_enterado, desde, hasta, 
                        dias, horas, minutos, duracion, restante, tipo)
                        VALUES  
                        ('','{$cedula}','{$_POST['descripcion']}', '{$fecha}', '{$fecha_efectividad}','{$fecha_aprobado}','{$fecha_enterado}', "
                        . "'{$_POST['desde']}','{$_POST['hasta']}','{$_POST['dias']}','{$horas}','{$minutos}',"
                        . "'{$tiempo}','{$_POST['restante']}','{$_POST['tipo_registro']}')";
            $resultado=query($consulta,$conexion);

            //INSERCION DIAS INCAPACIDAD
            $consulta2="INSERT INTO dias_incapacidad
                        (cod_user, tipo_justificacion, fecha, tiempo, observacion, documento, st, usr_uid, dias, horas, minutos) 
                        VALUES
                        ('{$posicion}','{$tipo_justificacion}', '{$fecha}','{$tiempo}', '{$_POST['descripcion']}', NULL, NULL,'{$user_uid}',"
                        . "'{$dias}','{$horas}','{$minutos}')";
            $resultado2=query($consulta2,$conexion);
    }

    //DOCUMENTOS - GUARDAR
    else if($_POST['tipo_registro']=="13")
    {
            $nombre_documento  = (isset($_POST['nombre_documento']))  ? $_POST['nombre_documento']  : NULL;
            $fecha_vencimiento = str_replace('-', '/', $_POST['fecha_vencimiento']);
            $fecha_vencimiento = fecha_sql($fecha_vencimiento);
            $fecha = date('Y-m-d');
            //$descripcion_docu = (isset($_POST['descripcion'])) ? $_POST['descripcion'] : NULL;

            // Cargar el documento

            if(isset($_FILES['archivo']))
            {
                    if ($_FILES['archivo']["error"] > 0)
                            exit("¡Error al subir el archivo! Código: " . $_FILES['archivo']["error"]);
                    else
                    {
                            
                            if (!file_exists("navegador_archivos/archivos/".$cedula)) 
                            {
                                mkdir("navegador_archivos/archivos/".$cedula, 0755, true);
                            }
                            $archivo = basename($_FILES['archivo']['name']);
                            $archivo = str_replace(' ', '', strtolower($archivo));
                            $archivo = "navegador_archivos/archivos/" . $cedula . "/" . time() . '_' . $archivo ;

                            if (! move_uploaded_file($_FILES['archivo']['tmp_name'], $archivo) ) 
                                    exit("¡Error! Al mover el archivo");
                    }
            }

            // REGISTRAR EXPEDIENTE
            //$descripcion_registro="DOCUMENTO";
            $consulta="INSERT INTO expediente
                               (cod_expediente_det, cedula, descripcion, tipo, subtipo, fecha)
                               VALUES  
                                ('','{$cedula}','{$_POST['descripcion']}','{$_POST['tipo_registro']}','{$_POST['tipo_tiporegistro']}','".$fecha."')";

            $resultado=query($consulta,$conexion);

            if($resultado)
            {
                    //REGISTRAR DOCUMENTO
                    $ultimo_id = mysqli_insert_id($conexion);

                    $consulta = "INSERT INTO `expediente_documento` 
                                 (`id_documento`, `nombre_documento`, `descripcion`, `url_documento`, `fecha_registro`, `fecha_vencimiento`, `cod_expediente_det`) 
                                 VALUES 
                                 ('', '{$_POST['tipo_tiporegistro']}', '{$_POST['descripcion']}', '{$archivo}', '{$fecha}', '{$fecha_vencimiento}', '{$ultimo_id}')";
                    $resultado=query($consulta,$conexion);
            }
    }


    //EXPERIENCIA - GUARDAR
    else if($_POST['tipo_registro']=="14")
    {


            if($_POST['fecha_inicio']!='')
                $fecha_inicio = fecha_sql($_POST['fecha_inicio']);
            if($_POST['fecha_fin']!='')
                $fecha_fin= fecha_sql($_POST['fecha_fin']);

             $fecha = date("Y-m-d");

            $consulta="INSERT INTO expediente
                        (cod_expediente_det, cedula, descripcion, fecha, fecha_inicio, fecha_fin, institucion_publica, 
                        labor, cargo, tipo, subtipo)
                        VALUES  
                         ('','{$cedula}','{$_POST['descripcion']}','{$fecha}', '{$fecha_inicio}', '{$fecha_fin}','{$_POST['institucion']}', "
                         . "'{$_POST['labor']}','{$_POST['cargo']}', '{$_POST['tipo_registro']}','{$_POST['tipo_tiporegistro']}')";
            $resultado=query($consulta,$conexion);

    }

    //LICENCIAS - GUARDAR
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

            //$duracion = $dias*8 + $horas +($minutos/60);
            $duracion = $horas;
            
//            if ($_POST['licencia_sueldo']=="on")
//                $licencia_sueldo=2;
//            else
//                $licencia_sueldo=1;

                        
            $fecha_creacion = date('Y-m-d');
            
             //INSERCIÓN EXPEDIENTE
            $consulta="INSERT INTO expediente
                        (cod_expediente_det, cedula, descripcion, desde, fecha_inicio, hasta, fecha_fin, meses, dias, horas, minutos, duracion,
                        restante, licencia_sueldo, licencia_enfermedad, numero_resolucion, fecha, fecha_resolucion, fecha_aprobado, fecha_enterado, tipo, subtipo,
                        fecha_creacion, usuario_creacion)
                        VALUES  
                        ('','{$cedula}','{$_POST['descripcion']}', '{$_POST['desde']}', '{$fecha_inicio}','{$_POST['hasta']}', "
                        . "'{$fecha_fin}','{$meses}','{$dias}','{$horas}','{$minutos}','{$duracion}','{$_POST['disponible']}',"
                        . "'{$_POST['licencia_sueldo']}','{$_POST['licencia_enfermedad']}','{$_POST['numero_resolucion']}', '{$fecha_resolucion}', "
                        . "'{$fecha_resolucion}','{$fecha_aprobado}', '{$fecha_enterado}',"
                        . "'{$_POST['tipo_registro']}','{$_POST['tipo_tiporegistro']}','{$fecha_creacion}','{$usuario}')";    
//            echo $consulta;
            $resultado=query($consulta,$conexion);

    }

    //OBSERVACION - GUARDAR
     else if ($_POST['tipo_registro']=="18"){
            if($_POST['fecha_resolucion']!='')
                    $fecha_resolucion=fecha_sql($_POST['fecha_resolucion']);		

            $fecha = date('Y-m-d');

             //INSERCIÓN EXPEDIENTE
            $consulta="INSERT INTO expediente
                        (cod_expediente_det, cedula, descripcion, fecha, fecha_resolucion, numero_resolucion, tipo )
                        VALUES  
                        ('','{$cedula}','{$_POST['descripcion']}', '{$fecha}','{$fecha_resolucion}','{$_POST['numero_resolucion']}',"
                        . "'{$_POST['tipo_registro']}')";    
//            echo $consulta;
            $resultado=query($consulta,$conexion);
    }

    //BAJA - GUARDAR
    else if ($_POST['tipo_registro']=="19"){
            if($_POST['fecha_resolucion']!='')
                    $fecha_resolucion=fecha_sql($_POST['fecha_resolucion']);		

            $fecha = date('Y-m-d');

             //INSERCIÓN EXPEDIENTE
            $consulta="INSERT INTO expediente
                        (cod_expediente_det, cedula, descripcion, fecha, fecha_resolucion, numero_resolucion, tipo )
                        VALUES  
                        ('','{$cedula}','{$_POST['descripcion']}', '{$fecha}','{$fecha_resolucion}','{$_POST['numero_resolucion']}',"
                        . "'{$_POST['tipo_registro']}')";    
//            echo $consulta;
            $resultado=query($consulta,$conexion);                                                

    }

    //SUSPENDER PAGO - GUARDAR
    else if ($_POST['tipo_registro']=="20"){
            if($_POST['fecha_resolucion']!='')
                    $fecha_resolucion=fecha_sql($_POST['fecha_resolucion']);		

            $fecha = date('Y-m-d');

             //INSERCIÓN EXPEDIENTE
            $consulta="INSERT INTO expediente
                        (cod_expediente_det, cedula, descripcion, fecha, fecha_resolucion, numero_resolucion, tipo )
                        VALUES  
                        ('','{$cedula}','{$_POST['descripcion']}', '{$fecha}','{$fecha_resolucion}','{$_POST['numero_resolucion']}',"
                        . "'{$_POST['tipo_registro']}')";    
//            echo $consulta;
            $resultado=query($consulta,$conexion);                                

    }

    //ANALISIS CAMBIO CATEGORIA/GREMIO - GUARDAR
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

        //INSERCIÓN EXPEDIENTE
        $consulta="INSERT INTO expediente
                (cod_expediente_det, cedula, descripcion,sobresueldo_jefatura,sobresueldo_exclusividad,
                sobresueldo_altoriesgo, sobresueldo_especialidad, fecha, fecha_idoneidad, 
                fecha_ini_labor_otra_inst, fecha_fin_labor_otra_inst, fecha_ini_labor_contrato, fecha_fin_labor_contrato,
                fecha_labor_permanente, registro, folio, tipo)
                VALUES  
                 ('','{$cedula}','{$_POST['descripcion']}','{$sobresueldo_jefatura}',"
                 . "'{$sobresueldo_exclusividad}','{$sobresueldo_altoriesgo}',"
                 . "'{$sobresueldo_especialidad}','{$fecha}','{$fecha_idoneidad}',"
                 . "'{$fecha_ini_labor_otra_inst}','{$fecha_fin_labor_otra_inst}',"
                 . "'{$fecha_ini_labor_contrato}','{$fecha_fin_labor_contrato}','{$fecha_labor_permanente}',"
                 . "'{$_POST['registro']}','{$_POST['folio']}','{$_POST['tipo_registro']}')";
        $resultado=query($consulta,$conexion); 


        if($resultado)
        {
            //INSERCIÓN EXPEDIENTE ANALISIS
            $ultimo_id = mysqli_insert_id($conexion);

            $salario_analisis = str_replace(",","",$_POST['salario_analisis']); 
            $veinte_porciento = str_replace(",","",$_POST['veinte_porciento']);  
            $cuarenta_porciento = str_replace(",","",$_POST['cuarenta_porciento']);

             $consulta2="INSERT INTO expediente_analisis
                        (id_analisis, fecha, etapa, salario, resuelto_1, veinte_porciento, 
                        cuarenta_porciento, resuelto_2, cod_expediente_det)
                        VALUES  
                        ('','{$fecha_inicio_lab}','{$_POST['etapa_analisis']}',"
                        . "'{$salario_analisis}','{$_POST['resuelto_analisis_1']}',"
                        . "'{$veinte_porciento}','{$cuarenta_porciento}',"
                        . "'{$_POST['resuelto_analisis_2']}','{$ultimo_id}')";
//            echo $consulta2;
            $resultado2=query($consulta2,$conexion);

            //INSERCIÓN EXPEDIENTE BIENAL

            $salario_bienal = str_replace(",","",$_POST['salario_bienal']); 
            $monto_mensual_bienal = str_replace(",","",$_POST['monto_mensual_bienal']);  
            $acumulativo_bienal = str_replace(",","",$_POST['acumulativo_bienal']);

             $consulta3="INSERT INTO expediente_bienal
                        (id_bienal, fecha, numero, salario, resuelto, monto_mensual, 
                        acumulativo, cod_expediente_det)
                        VALUES  
                        ('','{$fecha_bienal}','{$_POST['numero_bienal']}',"
                        . "'{$salario_bienal}','{$_POST['resuelto_bienal']}',"
                        . "'{$monto_mensual_bienal}','{$acumulativo_bienal}',"
                        . "'{$ultimo_id}')";
            echo $consulta3;
            $resultado3=query($consulta3,$conexion);                        
        }                 

    }

    //ANALISIS CAMBIO ETAPA - GUARDAR
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

        //INSERCIÓN EXPEDIENTE
        $consulta="INSERT INTO expediente
                (cod_expediente_det, cedula, descripcion, fecha, salario_base, ajuste, ajuste_discrecional, ajuste_salario_minimo, ajuste_otros, porcentaje,
                salario_base_porcentaje, acuerdo, cargo_estructura, cargo_funcion, tipo)
                VALUES  
                 ('','{$cedula}','{$_POST['descripcion']}','{$fecha}',"
                 . "'{$salario_base}','{$ajuste}', '{$ajuste_discrecional}','{$ajuste_salario_minimo}','{$ajuste_otros}','{$porcentaje}',"
                 . "'{$salario_base_porcentaje}','{$acuerdo}','{$_POST['cargo_estructura2']}',"
                 . "'{$_POST['cargo_funcion']}','{$_POST['tipo_registro']}')";
        $resultado=query($consulta,$conexion); 


        if($resultado)
        {
            //INSERCIÓN EXPEDIENTE ANALISIS
            $ultimo_id = mysqli_insert_id($conexion);
            $salario_analisis = str_replace(",","",$_POST['salario_analisis']);
             $consulta2="INSERT INTO expediente_analisis
                        (id_analisis, fecha, grado, etapa, salario, resuelto_1, cod_expediente_det)
                        VALUES  
                        ('','{$fecha_inicio_lab}','{$_POST['grado_analisis']}','{$_POST['etapa_analisis']}',"
                        . "'{$salario_analisis}','{$_POST['resuelto_analisis_1']}',"
                        . "'{$ultimo_id}')";
//            echo $consulta2;
            $resultado2=query($consulta2,$conexion);                       

        }                 

    }

    //VIGENCIAS EXPIRADAS - GUARDAR
    else if ($_POST['tipo_registro']=="23")
    {                   
        $fecha = date('Y-m-d');
        //INSERCIÓN EXPEDIENTE
        $consulta="INSERT INTO expediente
                (cod_expediente_det, cedula, descripcion, fecha, concepto, analista,
                 auditor, tipo)
                VALUES  
                 ('','{$cedula}','{$_POST['descripcion']}','{$fecha}','{$_POST['concepto']}',"
                 . "'{$_POST['analista']}','{$_POST['auditor']}','{$_POST['tipo_registro']}')";
        $resultado=query($consulta,$conexion); 


        if($resultado)
        {
            //INSERCIÓN EXPEDIENTE VIGENCIAS
            $ultimo_id = mysqli_insert_id($conexion);
            $salario_devengado = str_replace(",","",$_POST['salario_devengado']); 
            $salario_devengar = str_replace(",","",$_POST['salario_devengar']);  
            $diferencia = str_replace(",","",$_POST['diferencia']);
            $monto_pagar = str_replace(",","",$_POST['monto_pagar']);

             $consulta2="INSERT INTO expediente_vigencia
                        (id_vigencia, periodo_adeudado, salario_devengado, categoria_1, 
                        salario_devengar, categoria_2, diferencia, periodo_adeudado_amd,
                        monto_pagar, cod_expediente_det)
                        VALUES  
                        ('','{$_POST['periodo_adeudado']}','{$salario_devengado}',"
                        . "'{$_POST['categoria_1']}','{$salario_devengar}',"
                        . "'{$_POST['categoria_2']}','{$diferencia}',"
                        . "'{$_POST['periodo_adeudado_amd']}','{$monto_pagar}','{$ultimo_id}')";
//            echo $consulta2;
            $resultado2=query($consulta2,$conexion);                       

        }                 

    }
    
    //REASIGNACION - GUARDAR
    else if ($_POST['tipo_registro']=="24")
    {                   
        $fecha = date('Y-m-d');
        //INSERCIÓN EXPEDIENTE
        $consulta="INSERT INTO expediente
                (cod_expediente_det, cedula, descripcion, fecha, planilla_anterior, planilla_nueva,
                departamento_anterior, departamento_nuevo, funcion_anterior, funcion_nueva, tipo)
                VALUES  
                 ('','{$cedula}','{$_POST['descripcion']}','{$fecha}','{$_POST['planilla_anterior']}','{$_POST['planilla_nueva']}',"
                 . "'{$_POST['departamento_anterior']}','{$_POST['departamento_nuevo2']}',"
                 . "'{$_POST['funcion_anterior']}','{$_POST['funcion_nueva']}','{$_POST['tipo_registro']}')";
        $resultado=query($consulta,$conexion); 


        if($resultado)
        {                                 

        }                 

    }
    
     //ROTACION/APOYO TEMPORAL - GUARDAR
    else if ($_POST['tipo_registro']=="25" || $_POST['tipo_registro']=="26")
    {                   
        $fecha = date('Y-m-d');
        if($_POST['fecha_inicio']!='')
                $fecha_inicio=fecha_sql($_POST['fecha_inicio']);

        if($_POST['fecha_fin']!='')
                $fecha_fin=fecha_sql($_POST['fecha_fin']);
        //INSERCIÓN EXPEDIENTE
        $consulta="INSERT INTO expediente
                (cod_expediente_det, cedula, descripcion, fecha, fecha_inicio, fecha_fin,
                departamento_anterior, departamento_nuevo, tipo)
                VALUES  
                 ('','{$cedula}','{$_POST['descripcion']}','{$fecha}','{$fecha_inicio}','{$fecha_fin}',"
                 . "'{$_POST['departamento_anterior']}','{$_POST['departamento_nuevo2']}',"
                 . "'{$_POST['tipo_registro']}')";
        $resultado=query($consulta,$conexion); 


        if($resultado)
        {                                 

        }                 

    }

    // AJUSTE DE TIEMPO - GUARDAR
    else if($_POST['tipo_registro']=="27")
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
        
        $fecha_creacion = date("Y-m-d");        
        
        $tipo_ajuste=$_POST['tipo_ajuste'];
        $dias=$_POST['dias'];        
        
        if($_POST['restante']!='')
        {
            $restantes=$_POST['restante'];
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
       

            $consulta1="INSERT INTO expediente
                        (cod_expediente_det, cedula, descripcion, fecha, fecha_inicio, fecha_fin, fecha_inicio_periodo, fecha_fin_periodo, tipo, subtipo, dias, restante, 
                        horas, duracion, tipo_ajuste, periodo_vacacion, fecha_creacion, usuario_creacion)
                        VALUES  
                         ('','{$cedula}','{$_POST['descripcion']}','{$fecha_creacion}','{$fecha_inicio}','{$fecha_fin}','{$fecha_inicio_periodo}','{$fecha_fin_periodo}',"
                         . "'{$_POST['tipo_registro']}','{$_POST['tipo_tiporegistro']}',"
                         . "'{$dias}','{$restantes}','{$horas}', '{$tiempo}','{$tipo_ajuste}','{$periodo_vacacion}','{$fecha_creacion}','{$usuario}')";
            $resultado1=query($consulta1,$conexion);
            
    }
    
    //MISION OFICIAL- GUARDAR
    else if ($_POST['tipo_registro']=="28")
    {       

        if($_POST['fecha']!='')
                $fecha=fecha_sql($_POST['fecha']);        
        

        $fecha_creacion = date('Y-m-d');
        
        
        //INSERCIÓN EXPEDIENTE
        $consulta="INSERT INTO expediente
                (cod_expediente_det, cedula, descripcion, fecha, tipo, fecha_creacion, usuario_creacion)
                VALUES  
                 ('','{$cedula}','{$_POST['descripcion']}','{$fecha}','{$_POST['tipo_registro']}','{$fecha_creacion}','{$usuario}')";
        $resultado=query($consulta,$conexion);                           
                        

    }
    
    //CERTIFICACION TRABAJO - GUARDAR
    else if ($_POST['tipo_registro']=="29")
    {       

        if($_POST['fecha']!='')
                $fecha=fecha_sql($_POST['fecha']);        
        

        $fecha_creacion = date('Y-m-d');
        
        
        //INSERCIÓN EXPEDIENTE
        $consulta="INSERT INTO expediente
                (cod_expediente_det, cedula, descripcion, fecha, tipo, fecha_creacion, usuario_creacion)
                VALUES  
                 ('','{$cedula}','{$_POST['descripcion']}','{$fecha}','{$_POST['tipo_registro']}','{$fecha_creacion}','{$usuario}')";
        $resultado=query($consulta,$conexion);                           
                        

    }
    
    //ASCENSO - GUARDAR
    else if ($_POST['tipo_registro']=="30")
    {       

        if($_POST['fecha']!='')
                $fecha=fecha_sql($_POST['fecha']);        
        

        $fecha_creacion = date('Y-m-d');
        
        
        //INSERCIÓN EXPEDIENTE
        $consulta="INSERT INTO expediente
                (cod_expediente_det, cedula, descripcion, fecha, tipo, fecha_creacion, usuario_creacion)
                VALUES  
                 ('','{$cedula}','{$_POST['descripcion']}','{$fecha}','{$_POST['tipo_registro']}','{$fecha_creacion}','{$usuario}')";
        $resultado=query($consulta,$conexion);                           
                        

    }
    
    //AUMENTO (AJUSTE SALARIAL) - GUARDAR
    else if ($_POST['tipo_registro']=="31")
    {       

        if($_POST['fecha']!='')
                $fecha=fecha_sql($_POST['fecha']);        
        $monto_nuevo = str_replace(",","",$_POST['monto_nuevo']); 
        $monto = str_replace(",","",$_POST['salario']); 

        $fecha_creacion = date('Y-m-d');
        
        
        //INSERCIÓN EXPEDIENTE
        $consulta="INSERT INTO expediente
                (cod_expediente_det, cedula, descripcion, monto, monto_nuevo, fecha, tipo, fecha_creacion, usuario_creacion)
                VALUES  
                 ('','{$cedula}','{$_POST['descripcion']}','{$monto}','{$monto_nuevo}','{$fecha}','{$_POST['tipo_registro']}','{$fecha_creacion}','{$usuario}')";
        $resultado=query($consulta,$conexion);                           
                        

    }
    
    //REVOCATORIA - GUARDAR
    else if ($_POST['tipo_registro']=="32")
    {       

        if($_POST['fecha']!='')
                $fecha=fecha_sql($_POST['fecha']);        
        

        $fecha_creacion = date('Y-m-d');
        
        
        //INSERCIÓN EXPEDIENTE
        $consulta="INSERT INTO expediente
                (cod_expediente_det, cedula, descripcion, fecha, tipo, fecha_creacion, usuario_creacion)
                VALUES  
                 ('','{$cedula}','{$_POST['descripcion']}','{$fecha}','{$_POST['tipo_registro']}','{$fecha_creacion}','{$usuario}')";
        $resultado=query($consulta,$conexion);                           
                        

    }
    
    //MODIFICACION DECRETO - GUARDAR
    else if ($_POST['tipo_registro']=="33")
    {       

        if($_POST['fecha']!='')
                $fecha=fecha_sql($_POST['fecha']);        
        

        $fecha_creacion = date('Y-m-d');
        
        
        //INSERCIÓN EXPEDIENTE
        $consulta="INSERT INTO expediente
                (cod_expediente_det, cedula, descripcion, fecha, tipo, fecha_creacion, usuario_creacion)
                VALUES  
                 ('','{$cedula}','{$_POST['descripcion']}','{$fecha}','{$_POST['tipo_registro']}','{$fecha_creacion}','{$usuario}')";
        $resultado=query($consulta,$conexion);                           
                        

    }
    
    //SOBRESUELDO - GUARDAR
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
        
        

        //INSERCIÓN EXPEDIENTE
        $consulta="INSERT INTO expediente
                (cod_expediente_det, cedula, descripcion,sobresueldo_jefatura,sobresueldo_exclusividad,
                sobresueldo_altoriesgo, sobresueldo_especialidad, sobresueldo_antiguedad, sobresueldo_otros, sobresueldo_gastos_representacion, 
                salario_base, porcentaje, salario_base_porcentaje, fecha, fecha_inicio, tipo, subtipo)
                VALUES  
                 ('','{$cedula}','{$_POST['descripcion']}','{$sobresueldo_jefatura}',"
                 . "'{$sobresueldo_exclusividad}','{$sobresueldo_altoriesgo}',"
                 . "'{$sobresueldo_especialidad}','{$sobresueldo_antiguedad}','{$sobresueldo_otros}',"
                 . "'{$sobresueldo_gastos_representacion}','{$_POST['salario']}','{$porcentaje_sobresueldo}','{$sobresueldo}',"
                 . "'{$fecha}','{$fecha_inicio}','{$_POST['tipo_registro']}','{$_POST['tipo_tiporegistro']}')";
        $resultado=query($consulta,$conexion);

    }    
    
    //JUBILACION - GUARDAR
    else if ($_POST['tipo_registro']=="35")
    {       

        if($_POST['fecha']!='')
                $fecha=fecha_sql($_POST['fecha']);
        if($_POST['fecha_enterado']!='')
                $fecha_enterado=fecha_sql($_POST['fecha_enterado']);              

        $fecha_creacion = date('Y-m-d');        
        
        //INSERCIÓN EXPEDIENTE
        $consulta="INSERT INTO expediente
                (cod_expediente_det, cedula, descripcion, fecha, fecha_enterado, motivo_jubilacion, tipo, fecha_creacion, usuario_creacion)
                VALUES  
                 ('','{$cedula}','{$_POST['descripcion']}','{$fecha}','{$fecha_enterado}','{$_POST['motivo_jubilacion']}',"
                 . "'{$_POST['tipo_registro']}','{$fecha_creacion}','{$usuario}')";
        $resultado=query($consulta,$conexion);  
    }
    
    //PRORROGRA CONTINUACION - GUARDAR
    else if ($_POST['tipo_registro']=="36")
    {       

        if($_POST['fecha']!='')
                $fecha=fecha_sql($_POST['fecha']);        
        

        $fecha_creacion = date('Y-m-d');
        
        
        //INSERCIÓN EXPEDIENTE
        $consulta="INSERT INTO expediente
                (cod_expediente_det, cedula, descripcion, fecha, tipo, fecha_creacion, usuario_creacion)
                VALUES  
                 ('','{$cedula}','{$_POST['descripcion']}','{$fecha}','{$_POST['tipo_registro']}','{$fecha_creacion}','{$usuario}')";
        $resultado=query($consulta,$conexion);                           
                        

    }
        
    //INICIO DE LABORES - GUARDAR
    else if ($_POST['tipo_registro']=="37")
    {       

        if($_POST['fecha']!='')
                $fecha=fecha_sql($_POST['fecha']);        
        

        $fecha_creacion = date('Y-m-d');
        
        
        //INSERCIÓN EXPEDIENTE
        $consulta="INSERT INTO expediente
                (cod_expediente_det, cedula, descripcion, fecha, tipo, fecha_creacion, usuario_creacion)
                VALUES  
                 ('','{$cedula}','{$_POST['descripcion']}','{$fecha}','{$_POST['tipo_registro']}','{$fecha_creacion}','{$usuario}')";
        $resultado=query($consulta,$conexion);                           
                        

    }
    
    //CAMBIO NOMBRE/APELLIDO - GUARDAR
    else if ($_POST['tipo_registro']=="38")
    {       

        if($_POST['fecha']!='')
                $fecha=fecha_sql($_POST['fecha']);        
        

        $fecha_creacion = date('Y-m-d');
        
        
        //INSERCIÓN EXPEDIENTE
        $consulta="INSERT INTO expediente
                (cod_expediente_det, cedula, descripcion, fecha, nombres_anterior, apellido_paterno_anterior, 
                apellido_materno_anterior,apellido_casada_anterior, nombres_nuevo, apellido_paterno_nuevo, 
                apellido_materno_nuevo, apellido_casada_nuevo, tipo, fecha_creacion, usuario_creacion)
                VALUES  
                 ('','{$cedula}','{$_POST['descripcion']}','{$fecha}','{$nombres}','{$apellido_paterno}',"
                 . "'{$apellido_materno}','{$apellido_casada}','{$_POST['nombres_nuevo']}','{$_POST['apellido_paterno_nuevo']}',"
                 . "'{$_POST['apellido_materno_nuevo']}','{$_POST['apellido_casada_nuevo']}','{$_POST['tipo_registro']}',"
                 . "'{$fecha_creacion}','{$usuario}')";
        $resultado=query($consulta,$conexion);  
    }
    
    //DEFUNCION - GUARDAR
    else if ($_POST['tipo_registro']=="39")
    {       

        if($_POST['fecha']!='')
                $fecha=fecha_sql($_POST['fecha']);
        if($_POST['fecha_enterado']!='')
                $fecha_enterado=fecha_sql($_POST['fecha_enterado']);              

        $fecha_creacion = date('Y-m-d');        
        
        //INSERCIÓN EXPEDIENTE
        $consulta="INSERT INTO expediente
                (cod_expediente_det, cedula, descripcion, fecha, fecha_enterado, causa_defuncion, tipo, fecha_creacion, usuario_creacion)
                VALUES  
                 ('','{$cedula}','{$_POST['descripcion']}','{$fecha}','{$fecha_enterado}','{$_POST['causa_defuncion']}',"
                 . "'{$_POST['tipo_registro']}','{$fecha_creacion}','{$usuario}')";
        $resultado=query($consulta,$conexion);  
    }
    
    //REINCORPORACIÓN - GUARDAR
    else if ($_POST['tipo_registro']=="40")
    {       

        if($_POST['fecha']!='')
                $fecha=fecha_sql($_POST['fecha']);
        
        if($_POST['fecha_enterado']!='')
                $fecha_enterado=fecha_sql($_POST['fecha_enterado']);
        
        if($_POST['fecha_enterado_jefe']!='')
                $fecha_enterado_jefe=fecha_sql($_POST['fecha_enterado_jefe']);

        $fecha_creacion = date('Y-m-d');
        
        
        //INSERCIÓN EXPEDIENTE
        $consulta="INSERT INTO expediente
                (cod_expediente_det, cedula, descripcion, fecha, fecha_enterado, fecha_enterado_jefe, tipo, subtipo,
                fecha_creacion, usuario_creacion)
                VALUES  
                 ('','{$cedula}','{$_POST['descripcion']}','{$fecha}','{$fecha_enterado}','{$fecha_enterado_jefe}',"
                 . "'{$_POST['tipo_registro']}','{$_POST['tipo_tiporegistro']}','{$fecha_creacion}','{$usuario}')";
        $resultado=query($consulta,$conexion);
    }

    //RECLASIFICACION- GUARDAR
    else if ($_POST['tipo_registro']=="41")
    {       

        if($_POST['fecha_reclasificacion']!='')
            $fecha_reclasificacion=fecha_sql($_POST['fecha_reclasificacion']);

        if($_POST['fecha_decreto']!='')
           $fecha_decreto=fecha_sql($_POST['fecha_decreto']);

        $fecha_creacion = date('Y-m-d');

        $consulta_personal="SELECT * FROM nompersonal WHERE cedula='$cedula'";
        $resultado_personal=query($consulta_personal,$conexion);
        $fetch_personal=fetch_array($resultado_personal,$conexion);            
        $gerencia_anterior=$fetch_personal['codnivel1'];
        $departamento_anterior=$fetch_personal['codnivel2'];
        $seccion_anterior=$fetch_personal['codnivel3'];           
        $cod_cargo_anterior=$fetch_personal['codcargo'];
        $funcion_anterior=$fetch_personal['nomfuncion_id'];

         //INSERCIÓN EXPEDIENTE
        $consulta="INSERT INTO expediente
                    (cod_expediente_det, cedula, descripcion, fecha, fecha_decreto,gerencia_anterior, gerencia_nueva, departamento_anterior, 
                    departamento_nuevo, seccion_anterior, seccion_nueva,cod_cargo_anterior, cod_cargo_nuevo, funcion_anterior, funcion_nueva,numero_decreto,
                    tipo, fecha_creacion, usuario_creacion)
                    VALUES  
                    ('','{$cedula}','{$_POST['descripcion']}', '{$fecha_reclasificacion}','{$fecha_decreto}','{$gerencia_anterior}','{$_POST['gerencia_nueva']}',"
                    . "'{$departamento_anterior}','{$_POST['departamento_nuevo']}','{$seccion_anterior}','{$_POST['seccion_nueva']}',"
                    . "'{$cod_cargo_anterior}','{$_POST['cargo_nuevo']}','{$funcion_anterior}','{$_POST['funcion_nueva']}','{$_POST['numero_decreto']}',"
                    . "'{$_POST['tipo_registro']}','{$fecha_creacion}','{$usuario}')";    
//            echo $consulta;
        $resultado=query($consulta,$conexion);  
                        

    }
    
    //AUMENTO HORAS - GUARDAR
    else if ($_POST['tipo_registro']=="42")
    {       

        if($_POST['fecha']!='')
                $fecha=fecha_sql($_POST['fecha']);        
        

        $fecha_creacion = date('Y-m-d');
        
        
        //INSERCIÓN EXPEDIENTE
        $consulta="INSERT INTO expediente
                (cod_expediente_det, cedula, descripcion, fecha, tipo, fecha_creacion, usuario_creacion)
                VALUES  
                 ('','{$cedula}','{$_POST['descripcion']}','{$fecha}','{$_POST['tipo_registro']}','{$fecha_creacion}','{$usuario}')";
        $resultado=query($consulta,$conexion);                           
                        

    }
    
    //LIBRE NOMBRAMIENTO/REMOCION - GUARDAR
    else if ($_POST['tipo_registro']=="43")
    {       

        if($_POST['fecha']!='')
                $fecha=fecha_sql($_POST['fecha']);        
        

        $fecha_creacion = date('Y-m-d');
        
        
        //INSERCIÓN EXPEDIENTE
        $consulta="INSERT INTO expediente
                (cod_expediente_det, cedula, descripcion, fecha, tipo, fecha_creacion, usuario_creacion)
                VALUES  
                 ('','{$cedula}','{$_POST['descripcion']}','{$fecha}','{$_POST['tipo_registro']}','{$fecha_creacion}','{$usuario}')";
        $resultado=query($consulta,$conexion);                           
                        

    }
    
    //CESE DE LABORES - GUARDAR
    else if ($_POST['tipo_registro']=="52")
    {
            if($_POST['fecha_resolucion']!='')
                    $fecha_resolucion=fecha_sql($_POST['fecha_resolucion']);
            if($_POST['fecha']!='')
                    $fecha=fecha_sql($_POST['fecha']);
            $fecha_creacion = date("Y-m-d");
            //INSERCIÓN EXPEDIENTE
            $consulta="INSERT INTO expediente
                        (cod_expediente_det, cedula, descripcion, fecha, fecha_efectividad, numero_resolucion, fecha_resolucion, tipo, 
                        fecha_creacion, usuario_creacion)
                        VALUES  
                        ('','{$cedula}','{$_POST['descripcion']}', '{$fecha_creacion}', '{$fecha}','{$_POST['numero_resolucion']}', "
                        . "'{$fecha_resolucion}','{$_POST['tipo_registro']}','{$fecha_creacion}','{$usuario}')";
            $resultado=query($consulta,$conexion);

            
    }
    
    //ABANDONO DEL CARGO - GUARDAR
    else if ($_POST['tipo_registro']=="54")
    {
            if($_POST['fecha_resolucion']!='')
                    $fecha_resolucion=fecha_sql($_POST['fecha_resolucion']);
            if($_POST['fecha']!='')
                    $fecha=fecha_sql($_POST['fecha']);
            $fecha_creacion = date("Y-m-d");
            //INSERCIÓN EXPEDIENTE
            $consulta="INSERT INTO expediente
                        (cod_expediente_det, cedula, descripcion, fecha, fecha_efectividad, numero_resolucion, fecha_resolucion, tipo, 
                        fecha_creacion, usuario_creacion)
                        VALUES  
                        ('','{$cedula}','{$_POST['descripcion']}', '{$fecha_creacion}', '{$fecha}','{$_POST['numero_resolucion']}', "
                        . "'{$fecha_resolucion}','{$_POST['tipo_registro']}','{$fecha_creacion}','{$usuario}')";
            $resultado=query($consulta,$conexion);

            
    }
    
    //AUMENTESE, ASCIENDASE Y TRASLADESE
    else if ($_POST['tipo_registro']=="56"){
            
            if($_POST['fecha_decreto']!='')
                    $fecha_decreto=fecha_sql($_POST['fecha_decreto']);
            
            $fecha_creacion = date('Y-m-d');
            
            $consulta_personal="SELECT * FROM nompersonal WHERE cedula='$cedula'";
            $resultado_personal=query($consulta_personal,$conexion);
            $fetch_personal=fetch_array($resultado_personal,$conexion);
           
            $gerencia_anterior=$fetch_personal['codnivel1'];
            $departamento_anterior=$fetch_personal['codnivel2'];
            $seccion_anterior=$fetch_personal['codnivel3'];
            $posicion_anterior=$fetch_personal['nomposicion_id'];
            $cod_cargo_anterior=$fetch_personal['codcargo'];
            $funcion_anterior=$fetch_personal['nomfuncion_id'];
            $planilla_anterior=$fetch_personal['tipnom'];
            
            
             //INSERCIÓN EXPEDIENTE
            $consulta="INSERT INTO expediente
                        (cod_expediente_det, cedula, descripcion, fecha, fecha_decreto ,gerencia_anterior, gerencia_nueva, 
                        departamento_anterior, departamento_nuevo, seccion_anterior, seccion_nueva,posicion_anterior, posicion_nueva,cod_cargo_anterior, 
                        cod_cargo_nuevo, funcion_anterior, funcion_nueva, planilla_anterior, planilla_nueva, numero_decreto,
                        tipo, subtipo, fecha_creacion, usuario_creacion)
                        VALUES  
                        ('','{$cedula}','{$_POST['descripcion']}', '{$fecha_decreto}','{$fecha_decreto}','{$gerencia_anterior}','{$_POST['gerencia_nueva']}',"
                        . "'{$departamento_anterior}','{$_POST['departamento_nuevo']}','{$seccion_anterior}','{$_POST['seccion_nueva']}',"
                        . "'{$posicion_anterior}','{$_POST['posicion_nueva']}','{$cod_cargo_anterior}','{$_POST['cargo_nuevo']}',"
                        . "'{$funcion_anterior}','{$_POST['funcion_nueva']}','{$planilla_anterior}','{$_POST['planilla_nueva']}',"
                        . "'{$_POST['numero_decreto']}',"
                        . "'{$_POST['tipo_registro']}','{$_POST['tipo_tiporegistro']}','{$fecha_creacion}','{$usuario}')";    
//            echo $consulta;
            $resultado=query($consulta,$conexion);
    }
    
    //BAJA (OFICIAL) - GUARDAR
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
            
            $fecha_creacion = date('Y-m-d');
            
             //INSERCIÓN EXPEDIENTE
            $consulta="INSERT INTO expediente
                        (cod_expediente_det, cedula, descripcion, fecha,fecha_notificacion, numero_resolucion, fecha_resolucion, numero_edicto, 
                        fecha_edicto, posicion_anterior, posicion_nueva, tipo, subtipo,fecha_creacion, usuario_creacion)
                        VALUES  
                        ('','{$cedula}','{$_POST['descripcion']}', '{$fecha_baja}','{$fecha_notificacion}','{$_POST['numero_resuelto']}',"
                        . "'{$fecha_resuelto}', '{$_POST['numero_edicto']}','{$fecha_edicto}','{$_POST['posicion_anterior']}','{$_POST['posicion_anterior']}',"
                        . "'{$_POST['tipo_registro']}','{$_POST['tipo_tiporegistro']}','{$fecha_creacion}','{$usuario}')";    
//            echo $consulta;
            $resultado=query($consulta,$conexion);

    }
    
    //REINTEGRO - GUARDAR
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
            
            $fecha_creacion = date('Y-m-d');
            
            $consulta_personal="SELECT * FROM nompersonal WHERE cedula='$cedula'";
            $resultado_personal=query($consulta_personal,$conexion);
            $fetch_personal=fetch_array($resultado_personal,$conexion);
            $fecha_decreto_ingreso_anterior=$fetch_personal['fecha_decreto'];
            $gerencia_anterior=$fetch_personal['codnivel1'];
            $departamento_anterior=$fetch_personal['codnivel2'];
            $seccion_anterior=$fetch_personal['codnivel3'];
            $posicion_anterior=$fetch_personal['nomposicion_id'];
            $cod_cargo_anterior=$fetch_personal['codcargo'];
            $funcion_anterior=$fetch_personal['nomfuncion_id'];
            $planilla_anterior=$fetch_personal['tipnom'];
            $numero_decreto_anterior=$fetch_personal['num_decreto'];
            $situacion_anterior=6;
            $fecing=$fetch_personal['fecing'];
            
             //INSERCIÓN EXPEDIENTE
            $consulta="INSERT INTO expediente
                        (cod_expediente_det, cedula, descripcion, fecha, fecha_inicio, fecha_fin,fecha_notificacion, fecha_decreto_ingreso_anterior,
                        fecha_decreto_ingreso_nuevo, gerencia_anterior, gerencia_nueva, departamento_anterior, departamento_nuevo, seccion_anterior, seccion_nueva,
                        posicion_anterior, posicion_nueva,cod_cargo_anterior, cod_cargo_nuevo, funcion_anterior, funcion_nueva, planilla_anterior, planilla_nueva,
                        numero_decreto_anterior, numero_decreto, situacion_anterior, situacion_nueva,
                        tipo, fecha_creacion, usuario_creacion)
                        VALUES  
                        ('','{$cedula}','{$_POST['descripcion']}', '{$fecha_reintegro}','{$fecha_reintegro}','{$fecha_fin}','{$fecha_notificacion}',"
                        . "'{$fecha_decreto_ingreso_anterior}','{$fecha_decreto_ingreso_nuevo}','{$gerencia_anterior}','{$_POST['gerencia_nueva']}',"
                        . "'{$departamento_anterior}','{$_POST['departamento_nuevo']}','{$seccion_anterior}','{$_POST['seccion_nueva']}',"
                        . "'{$posicion_anterior}','{$_POST['posicion_nueva']}','{$cod_cargo_anterior}','{$_POST['cargo_nuevo']}',"
                        . "'{$funcion_anterior}','{$_POST['funcion_nueva']}','{$planilla_anterior}','{$_POST['planilla_nueva']}',"
                        . "'{$numero_decreto_anterior}','{$_POST['numero_decreto']}','{$situacion_anterior}','{$_POST['situacion_nueva']}',"
                        . "'{$_POST['tipo_registro']}','{$fecha_creacion}','{$usuario}')";    
//            echo $consulta;
            $resultado=query($consulta,$conexion);

    }

    
?>
