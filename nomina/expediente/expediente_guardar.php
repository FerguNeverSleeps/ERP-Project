<?php
header('X-XSS-Protection:0');
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
query("set names utf8",$conexion);
//ESTUDIO ACADEMICOS - GUARDAR
    if ($_POST['tipo_registro']=="1"){
            if($_POST['fecha_inicio']!='')
                    $fecha_inicio=fecha_sql($_POST['fecha_inicio']);				
            if($_POST['fecha_fin']!='')
                    $fecha_fin=fecha_sql($_POST['fecha_fin']);
            $fecha_creacion = date("Y-m-d");
            $fecha = date("Y-m-d");
             //INSERCIÓN EXPEDIENTE           
            $consulta="INSERT INTO expediente
                        (cod_expediente_det, 
                        cedula, 
                        descripcion, 
                        institucion_educativa_nueva, 
                        titulo_profesional,
                        idoneidad,
                        ejerce,
                        fecha, 
                        fecha_inicio, 
                        fecha_fin, 
                        dias, 
                        tipo, 
                        subtipo,
                        fecha_creacion, 
                        usuario_creacion )
                        VALUES  
                        ('',
                        '{$cedula}',"
                        . "'{$_POST['descripcion']}', "
                        . "'{$_POST['institucion_educativa_nueva']}',"
                        . "'{$_POST['titulo_profesional']}', "
                        . "'{$_POST['idoneidad']}',"
                        . "'{$_POST['ejerce']}',"
                        . "'{$fecha}',"
                        . "'{$fecha_inicio}',"
                        . " '{$fecha_fin}',"
                        . "'{$_POST['duracion']}',"
                        . "'{$_POST['tipo_registro']}',"
                        . "'{$_POST['tipo_tiporegistro']}',"
                        . "'{$fecha_creacion}',"
                        . "'{$usuario}')";
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
                        (cod_expediente_det,
                        cedula, 
                        descripcion,
                        institucion, 
                        fecha,
                        fecha_inicio, 
                        fecha_fin,
                        duracion, 
                        nombre_especialista, 
                        tipo, 
                        subtipo, 
                        fecha_creacion, 
                        usuario_creacion )
                        VALUES  
                        ('',
                        '{$cedula}',"
                        . "'{$_POST['descripcion']}',"
                        . "'{$_POST['institucion']}', "
                        . "'{$fecha}',"
                        . "'{$fecha_inicio}',"
                        . "'{$fecha_fin}',"
                        . "'{$_POST['duracion']}',"
                        . "'{$_POST['nombre_especialista']}',"
                        . "'{$_POST['tipo_registro']}',"
                        . "'{$_POST['tipo_tiporegistro']}',"
                        . "'{$fecha_creacion}',"
                        . "'{$usuario}')";
            $resultado=query($consulta,$conexion);
    }

    //PERMISOS - GUARDAR
    else if ($_POST['tipo_registro']=="4"){
            if($_POST['fecha_inicio']!='')
                    $fecha_inicio=fecha_sql($_POST['fecha_inicio']);

            if($_POST['fecha_fin']!='')
                    $fecha_fin=fecha_sql($_POST['fecha_fin']);

//            if($_POST['fecha_aprobado']!='')
//                    $fecha_aprobado=fecha_sql($_POST['fecha_aprobado']);

            if($_POST['fecha_resolucion']!='')
                    $fecha_resolucion=fecha_sql($_POST['fecha_resolucion']);	

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
                        (cod_expediente_det, 
                        cedula,
                        descripcion,
                        desde, 
                        fecha_inicio,
                        hasta, 
                        fecha_fin, 
                        dias, 
                        horas, 
                        minutos, 
                        duracion,
                        fecha, 
                        numero_resolucion,                        
                        fecha_resolucion, 
                        id_centro, 
                        nombre_medico, 
                        proyecto,
                        tipo, 
                        subtipo,
                        fecha_creacion,
                        usuario_creacion )
                        VALUES  
                        ('',
                        '{$cedula}',"
                        . "'{$_POST['descripcion']}',"
                        . " '{$_POST['desde']}',"
                        . " '{$fecha_inicio}',"
                        . "'{$_POST['hasta']}', "
                        . "'{$fecha_fin}',"
                        . "'{$dias}',"
                        . "'{$horas}',"
                        . "'{$minutos}',"
                        . "'{$duracion}',"
                        . "'{$fecha}',"
                        . "'{$_POST['numero_resolucion']}', "                        
                        . "'{$fecha_resolucion}',"
                        . "'{$_POST['id_centro']}',"
                        . " '{$_POST['nombre_medico']}',"
                        . " '{$_POST['proyecto']}',"
                        . "'{$_POST['tipo_registro']}',"
                        . "'{$_POST['tipo_tiporegistro']}',"
                        . "'{$fecha_creacion}',"
                        . "'{$usuario}')";
//            echo $consulta;
            $resultado=query($consulta,$conexion);


    }

    //AMONESTACIONES  - GUARDAR
    else if ($_POST['tipo_registro']=="5")
    {
        query("set names utf8",$conexion);
            if($_POST['fecha_amonestacion']!='')
                $fecha_amonestacion=fecha_sql($_POST['fecha_amonestacion']);
            $fecha_creacion = date("Y-m-d");
            //INSERCIÓN EXPEDIENTE
            $consulta="INSERT INTO expediente
                        (cedula,
                        descripcion,
                        fecha,
                        numeral,
                        numeral_descripcion,
                        articulo, 
                        tipo_falta,
                        tipo,
                        subtipo,
                        fecha_creacion,
                        usuario_creacion )
                        VALUES  
                        ('{$cedula}','{$_POST['descripcion']}', '{$fecha_amonestacion}', '{$_POST['numeral']}', "
                        . "'".$_POST['numeral_descripcion']."', '{$_POST['articulo']}', '{$_POST['tipo_falta']}','{$_POST['tipo_registro']}',"
                        . "'{$_POST['tipo_tiporegistro']}','{$fecha_creacion}','{$usuario}')";
            $resultado=query($consulta,$conexion);

//            //INSERCIÓN AMONESTACION
//            $ultimo_id = mysqli_insert_id($conexion);
//            if ($_POST['tipo_tiporegistro']==24)
//                $tipo_amonestacion="VERBAL";
//            else
//                $tipo_amonestacion="ESCRITA";
//             $consulta="INSERT INTO amonestaciones
//                        (id_amonestacion, usr_uid, fecha, numeral_numero, numeral_descripcion, articulo, motivo, tipo, cod_expediente_det)
//                        VALUES  
//                        ('','{$user_uid}','{$fecha_amonestacion}', '{$_POST['numeral']}', '{$_POST['numeral_descripcion']}', "
//                        . "'{$_POST['articulo']}', '{$_POST['descripcion']}', '{$_POST['tipo_amonestacion']}',"
//                        . "'{$ultimo_id}')";
//            $resultado=query($consulta,$conexion);
            

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
                        (cod_expediente_det, cedula, descripcion, dias, fecha_inicio, fecha_fin, fecha, fecha_resolucion, numero_resolucion, numeral, 
                        numeral_descripcion, articulo, tipo_falta, tipo, fecha_creacion, usuario_creacion)
                        VALUES  
                        ('','{$cedula}','{$_POST['descripcion']}', '{$_POST['dias']}', '{$fecha_desde}', '{$fecha_hasta}',"
                        . "'{$fecha_resolucion}','{$fecha_resolucion}', '{$_POST['numero_resolucion']}', '{$_POST['numeral']}',"
                        . "'{$_POST['numeral_descripcion']}','{$_POST['articulo']}','{$_POST['tipo_falta']}',"
                        . "'{$_POST['tipo_registro']}','{$fecha_creacion}','{$usuario}')";
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
        $numero_resolucion_anterior=$fetch_personal['num_resolucion'];
        $fecha_resolucion_anterior=$fetch_personal['fecha_resolucion'];
        $fecha_inicio_anterior=$fetch_personal['inicio_periodo'];
        $fecha_fin_anterior=$fetch_personal['fin_periodo'];
        $posicion_anterior=$fetch_personal['nomposicion_id'];
       
        
        //INSERCIÓN EXPEDIENTE
        $consulta="INSERT INTO expediente
                    (cod_expediente_det, cedula, descripcion, fecha, numero_resolucion_anterior, fecha_resolucion_anterior, 
                    fecha_inicio_anterior, fecha_fin_anterior, fecha_resolucion, fecha_inicio, fecha_fin, numero_resolucion, 
                    gerencia_anterior, departamento_anterior, seccion_anterior,cod_cargo_anterior, funcion_anterior, institucion_anterior, gerencia_nueva, 
                    departamento_nuevo, seccion_nueva, cod_cargo_nuevo, funcion_nueva, institucion_nueva, motivo_traslado, posicion_anterior, posicion_nueva,
                    tipo, subtipo,fecha_creacion, usuario_creacion)
                    VALUES  
                    ('','{$cedula}','{$_POST['descripcion']}','{$fecha_creacion}','{$numero_resolucion_anterior}','{$fecha_resolucion_anterior}',"
                    . "'{$fecha_inicio_anterior}','{$fecha_fin_anterior}','{$fecha_resolucion}','{$fecha_inicio}','{$fecha_fin}',"
                    . "'{$_POST['numero_resolucion']}','{$gerencia_anterior}','{$departamento_anterior}','{$seccion_anterior}',"
                    . "'{$cod_cargo_anterior}','{$funcion_anterior}','{$_POST['institucion_anterior']}','{$_POST['gerencia_nueva']}',"
                    . "'{$_POST['departamento_nuevo']}','{$_POST['seccion_nueva']}','{$_POST['cargo_nuevo']}','{$_POST['funcion_nueva']}',"
                    . "'{$_POST['institucion_nueva']}','{$_POST['motivo_traslado']}','{$posicion_anterior}','{$_POST['posicion_nueva']}',"
                    . "'{$_POST['tipo_registro']}','{$_POST['tipo_tiporegistro']}','{$fecha_creacion}','{$usuario}')";
        $resultado=query($consulta,$conexion);
    } 


    //EVALUACION DE DESEMPEÑO - GUARDAR
    else if ($_POST['tipo_registro']=="10"){
            if($_POST['fecha']!='')//CAMBIAR fecha_salida POR fecha_aplicacion.
                    $fecha=fecha_sql($_POST['fecha']);
            if($_POST['fecha_inicio_periodo']!='')
            $fecha_inicio_periodo=fecha_sql($_POST['fecha_inicio_periodo']);
            if($_POST['fecha_fin_periodo']!='')
                $fecha_fin_periodo=fecha_sql($_POST['fecha_fin_periodo']);
            $tipo="Ordinario";
            if ($_POST['tipo_tiporegistro']=="35")
            {
                $tipo="Periodo Probatorio";
              
            }
            $fecha_creacion = date("Y-m-d");
            
            //INSERCIÓN EXPEDIENTE
            $consulta="INSERT INTO expediente
                        (cod_expediente_det,
                        cedula, 
                        descripcion,
                        fecha_inicio_periodo,
                        fecha_fin_periodo,
                        fecha, 
                        puntaje,
                        funcionario_evalua,
                        tipo,
                        subtipo,
                        fecha_creacion,
                        Usuario_creacion)
                        VALUES  
                        ('','{$cedula}',"
                        . "'{$_POST['descripcion']}',"
                        . "'{$fecha_inicio_periodo}',"
                        . "'{$fecha_fin_periodo}',"
                        . "'{$fecha_creacion}',"
                        . "'{$_POST['puntaje']}', "
                        . "'{$_POST['funcionario_evalua']}',"
                        . "'{$_POST['tipo_registro']}',"
                        . "'{$_POST['tipo_tiporegistro']}',"
                        . "'{$fecha_creacion}',"
                        . "'{$usuario}')";
            $resultado=query($consulta,$conexion);
            
            $ultimo_id_expediente = mysqli_insert_id($conexion);
            
            $consulta_evaluacion="INSERT INTO empleado_evaluacion
                        (ID, 
                        IdEmpleado, 
                        fini_periodo, 
                        ffin_periodo, 
                        f_eval, 
                        persona_evalua, 
                        tipo, 
                        puntaje,
                        comentarios, 
                        fecha_creacion, 
                        usuario_creacion, 
                        cod_expediente_det, 
                        cedula)
                        VALUES  
                        ('',"
                        . "'{$personal_id}',"
                        . "'{$fecha_inicio_periodo}', "
                        . "'{$fecha_fin_periodo}',"
                        . "'{$fecha}',"
                        . "'{$tipo}',"
                        . "'{$_POST['funcionario_evalua']}',"
                        . "'{$_POST['puntaje']}',"
                        . "'{$_POST['descripcion']}',"
                        . "'{$fecha_creacion}',"
                        . "'{$usuario}',"
                        . "'{$ultimo_id_expediente}',"
                        . "'{$cedula}')";
//             echo $consulta_evaluacion;
//             exit;
            $resultado_evaluacion=query($consulta_evaluacion,$conexion);
            
    } 


    //VACACIONES - GUARDAR
    else if ($_POST['tipo_registro']=="11"){            

            

            if($_POST['fecha_inicio']!='')
                    $fecha_inicio=fecha_sql($_POST['fecha_inicio']);
             else
                $fecha_inicio="0000-00-00";

            if($_POST['fecha_fin']!='')
                    $fecha_fin=fecha_sql($_POST['fecha_fin']);
             else
                $fecha_fin="0000-00-00";          
            
            if($_POST['periodo_vacacion']!='')
                    $periodo_vacacion=$_POST['periodo_vacacion'];
            else
                $periodo_vacacion=0;
            
           
            
            $fecha = date("Y-m-d");
            $fecha_creacion = date("Y-m-d");

             //INSERCIÓN EXPEDIENTE
            $consulta="INSERT INTO expediente
                        (cod_expediente_det, 
                        cedula, 
                        descripcion, 
                        fecha_inicio, 
                        fecha_fin,
                        fecha,
                        ddisfrute,
                        dpago,
                        dpagob,
                        saldo_dias,
                        dias_ppagar,
                        saldo_vacaciones,
                        dias_solic_ppagar,
                        dias_vac_disfrute,
                        saldo_dias_pdisfrutar,
                        dias_solic_pdisfrutar,
                        periodo_vacacion, 
                        tipo, 
                        subtipo,
                        fecha_creacion,
                        usuario_creacion)
                        VALUES  
                        ('',
                        '{$cedula}',"
                        . "'{$_POST['descripcion']}',"
                        . " '{$fecha_inicio}',"
                        . " '{$fecha_fin}',"
                        . "'{$fecha}',"
                        . "'{$_POST['ddisfrute']}',"
                        . "'{$_POST['dpago']}',"
                        . "'{$_POST['dpagob']}',"
                        . "'{$_POST['saldo_dias']}',"
                        . "'{$_POST['dias_ppagar']}',"
                        . "'{$_POST['saldo_vacaciones']}',"
                        . "'{$_POST['dias_solic_ppagar']}',"
                        . "'{$_POST['dias_vac_disfrute']}',"
                        . "'{$_POST['saldo_dias_pdisfrutar']}',"
                        . "'{$_POST['dias_solic_pdisfrutar']}',"
                        . "'{$periodo_vacacion}',"
                        . "'{$_POST['tipo_registro']}',"
                        . "'{$_POST['tipo_tiporegistro']}',"
                        . "'{$fecha_creacion}',"
                        . "'{$usuario}')";
           
            $resultado=query($consulta,$conexion);
            
    } 

    //TIEMPO COMPENSATORIO  - GUARDAR
    else if ($_POST['tipo_registro']=="12"){

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
        
//        $duracion = $horas + ($minutos / 60);
        
        $tipo_ajuste=$_POST['tipo_ajuste'];

        $fecha_creacion = date("Y-m-d");
       
        $consulta_expediente="INSERT INTO expediente
                    (cod_expediente_det, 
                    cedula, 
                    descripcion, 
                    fecha, 
                    fecha_aprobado, 
                    fecha_inicio, 
                    fecha_fin, 
                    dias, 
                    horas, 
                    minutos, 
                    duracion, 
                    restante, 
                    tipo_ajuste, 
                    tipo, 
                    fecha_creacion, 
                    usuario_creacion)
                    VALUES  
                     ('',
                     '{$cedula}',"
                     . "'{$_POST['descripcion']}',"
                     . "'{$fecha}',"
                     . "'{$fecha_aprobado}',"
                     . "'{$fecha_inicio}',"
                     . "'{$fecha_fin}',"
                     . "'{$dias}',"
                     . "'{$horas}',"
                     . "'{$minutos}',"
                     . "'{$duracion}',"
                     . "'{$restantes}',"
                     . "'{$tipo_ajuste}',"
                     . "'{$_POST['tipo_registro']}',"
                     . "'{$fecha_creacion}',"
                     . "'{$usuario}')";
        $resultado_expediente=query($consulta_expediente,$conexion);

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
                                if (!mkdir("navegador_archivos/archivos/".$cedula, 0755, true)) {
                                    exit("carpeta no ha sido creada");
                                }
                                else {
                                    exit("carpeta ha sido creada");
                                }
                            }
                            $archivo = basename($_FILES['archivo']['name']);
                            $archivo = str_replace(' ', '', strtolower($archivo));
                            $archivo = "navegador_archivos/archivos/" . $cedula . "/" . time() . '_' . $archivo ;

                            if (!move_uploaded_file($_FILES['archivo']['tmp_name'], $archivo) )
                                    exit("¡Error! Al mover el archivo");
                    }
            }

            // REGISTRAR EXPEDIENTE
            //$descripcion_registro="DOCUMENTO";
            $consulta="INSERT INTO expediente
                               ( cedula, descripcion, tipo, subtipo, fecha)
                               VALUES  
                                ('{$cedula}','{$_POST['descripcion']}','{$_POST['tipo_registro']}','{$_POST['tipo_tiporegistro']}','".$fecha."')";

            $resultado=query($consulta,$conexion);

            if($resultado)
            {
                    //REGISTRAR DOCUMENTO
                    $ultimo_id = mysqli_insert_id($conexion);

                    $consulta = "INSERT INTO `expediente_documento` 
                                 ( `nombre_documento`, `descripcion`, `url_documento`, `fecha_registro`, `fecha_vencimiento`, `cod_expediente_det`) 
                                 VALUES 
                                 ('{$_POST['tipo_tiporegistro']}', '{$_POST['descripcion']}', '{$archivo}', '{$fecha}', '{$fecha_vencimiento}', '{$ultimo_id}')";
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
                        restante, posicion_anterior, posicion_nueva, licencia_sueldo, licencia_enfermedad, numero_resolucion, fecha, fecha_resolucion, fecha_aprobado, fecha_enterado, tipo, id_tipoempleado_anterior, subtipo,
                        fecha_creacion, usuario_creacion)
                        VALUES  
                        ('','{$cedula}','{$_POST['descripcion']}', '{$_POST['desde']}', '{$fecha_inicio}','{$_POST['hasta']}', "
                        . "'{$fecha_fin}','{$meses}','{$dias}','{$horas}','{$minutos}','{$duracion}','{$_POST['disponible']}',"
                        . "'{$posicion}','{$posicion}','{$_POST['licencia_sueldo']}','{$_POST['licencia_enfermedad']}','{$_POST['numero_resolucion']}', '{$fecha_resolucion}', "
                        . "'{$fecha_resolucion}','{$fecha_aprobado}', '{$fecha_enterado}',"
                        . "'{$_POST['tipo_registro']}','{$id_tipoempleado_anterior}','{$_POST['tipo_tiporegistro']}','{$fecha_creacion}','{$usuario}')";    
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
//    else if ($_POST['tipo_registro']=="24")
//    {                   
//        $fecha = date('Y-m-d');
//        //INSERCIÓN EXPEDIENTE
//        $consulta="INSERT INTO expediente
//                (cod_expediente_det, cedula, descripcion, fecha, planilla_anterior, planilla_nueva,
//                departamento_anterior, departamento_nuevo, funcion_anterior, funcion_nueva, tipo)
//                VALUES  
//                 ('','{$cedula}','{$_POST['descripcion']}','{$fecha}','{$_POST['planilla_anterior']}','{$_POST['planilla_nueva']}',"
//                 . "'{$_POST['departamento_anterior']}','{$_POST['departamento_nuevo2']}',"
//                 . "'{$_POST['funcion_anterior']}','{$_POST['funcion_nueva']}','{$_POST['tipo_registro']}')";
//        $resultado=query($consulta,$conexion); 
//
//
//        if($resultado)
//        {                                 
//
//        }                 
//
//    }
    
     //ROTACION / REASIGNACION / APOYO TEMPORAL - GUARDAR
    else if ($_POST['tipo_registro']=="24" || $_POST['tipo_registro']=="25" || $_POST['tipo_registro']=="26")
    {                   
        if($_POST['fecha_memo']!='')
            $fecha_memo=fecha_sql($_POST['fecha_memo']);
        if($_POST['fecha_inicio']!='')
            $fecha_inicio=fecha_sql($_POST['fecha_inicio']);
        if($_POST['fecha_fin']!='')
            $fecha_fin=fecha_sql($_POST['fecha_fin']);
            
        $fecha_creacion = date("Y-m-d");
        
        $consulta_personal="SELECT * FROM nompersonal WHERE cedula='$cedula'";
        $resultado_personal=query($consulta_personal,$conexion);
        $fetch_personal=fetch_array($resultado_personal,$conexion);    
        $departamento_anterior=$fetch_personal['IdDepartamento'];     
        
        //INSERCIÓN EXPEDIENTE
        $consulta="INSERT INTO expediente
                    (cod_expediente_det, 
                    cedula, 
                    descripcion, 
                    fecha, 
                    fecha_memo,
                    num_memo,
                    fecha_inicio, 
                    fecha_fin,
                    planilla_anterior,
                    planilla_nueva,
                    departamento_anterior,
                    departamento_nuevo,
                    dejado,
                    tipo,
                    fecha_creacion,
                    usuario_creacion)
                    VALUES  
                    ('',
                    '{$cedula}',"
                    . "'{$_POST['descripcion']}',"
                    . "'{$fecha_creacion}',"
                    . "'{$fecha_memo}',"
                    . "'{$_POST['num_memo']}',"
                    . "'{$fecha_inicio}','"
                    . "{$fecha_fin}',"
                    . "'{$_POST['planilla_anterior']}',"
                    . "'{$_POST['planilla_nueva']}',"
                    . "'{$departamento_anterior}',"
                    . "'{$_POST['departamento_nuevo']}',"
                    . "'{$_POST['dejado']}',"
                    . "'{$_POST['tipo_registro']}',"
                    . "'{$fecha_creacion}',"
                    . "'{$usuario}')";
        $resultado=query($consulta,$conexion);
        $ultimo_id = mysqli_insert_id($conexion);
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

        $sql_empleado_cargo="INSERT INTO empleado_cargo
                        (ID, 
                        IdEmpleado, 
                        IdDepartamento, 
                        FechaInicio,
                        FechaFinal, 
                        TipoMovimiento, 
                        fecha_creacion, 
                        usuario_crea,
                        fecha_memo, 
                        num_memo, 
                        dejado,
                        cedula,
                        cod_expediente_det)
                        VALUES  
                        ('',
                        '{$id_empleado}',"
                        . "'{$_POST['departamento_nuevo']}',"
                        . "'{$fecha_inicio}',"
                        . "'{$fecha_fin}',"
                        . "'{$tipo_movimiento}',"
                        . "'{$fecha_creacion}',"
                        . "'{$usuario}',"
                        . "'{$fecha_memo}',"
                        . "'{$_POST['num_memo']}',"
                        . "'{$_POST['dejado']}',"
                        . "'{$cedula}',"
                        . "'{$ultimo_id}')";
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

        //ACCION FUNCIONARIO    
        $consulta_correlativo = "SELECT correlativo FROM accion_funcionario_tipo "
                        . "WHERE id_accion_funcionario_tipo = '$tipo_accion'";
        $resultado_correlativo=query($consulta_correlativo,$conexion);
        $fetch_correlativo=fetch_array($resultado_correlativo,$conexion);
        $correlativo = $fetch_correlativo["correlativo"];
        $correlativo = $correlativo+1;

       $consulta_accion="INSERT INTO accion_funcionario
                (id_accion_funcionario, tipo_accion, numero_accion,id_funcionario)
                VALUES  
                ('','{$tipo_accion}','{$correlativo}', '{$id_empleado}')";
        $resultado_accion=query($consulta_accion,$conexion);           

        $consulta_accion_tipo ="UPDATE accion_funcionario_tipo SET correlativo='".$correlativo."' "
                            . "WHERE id_accion_funcionario_tipo='".$tipo_accion."'";
        $resultado_accion_tipo=query($consulta_accion_tipo,$conexion);  

    }

    // AJUSTE DE TIEMPO - GUARDAR
    else if($_POST['tipo_registro']=="27")
    {
                
        $fecha_creacion = date("Y-m-d");       
        
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
        
//        $duracion = $horas + ($minutos / 60);
        
        $fecha = date("Y-m-d");
       

        $consulta_expediente="INSERT INTO expediente
                    (cod_expediente_det,
                    cedula, 
                    descripcion, 
                    fecha,
                    tipo, 
                    subtipo, 
                    dias, 
                    restante, 
                    horas,
                    minutos, 
                    duracion, 
                    tipo_ajuste, 
                    fecha_inicio, 
                    fecha_fin,
                    fecha_creacion, 
                    usuario_creacion)
                    VALUES  
                     ('',
                     '{$cedula}',"
                     . "'{$_POST['descripcion']}',"
                     . "'{$fecha_creacion}',"
                     . "'{$_POST['tipo_registro']}',"
                     . "'{$_POST['tipo_tiporegistro']}',"
                     . "'{$dias}',"
                     . "'{$restantes}',"
                     . "'{$horas}',"
                     . "'{$minutos}',"
                     . "'{$duracion}',"
                     . "'{$tipo_ajuste}', "
                     . "'{$fecha_inicio}',"
                     . "'{$fecha_fin}',"
                     . " '{$fecha_creacion}',"
                     . "'{$usuario}')";
        $resultado_expediente=query($consulta_expediente,$conexion);
            
    }
    
    //MISION OFICIAL- GUARDAR
    else if ($_POST['tipo_registro']=="28")
    {       

          $fecha_creacion = date("Y-m-d");       
        
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
        
//        $duracion = $horas + ($minutos / 60);
        
        $fecha = date("Y-m-d");
       

        $consulta_expediente="INSERT INTO expediente
                    (cod_expediente_det,
                    cedula, 
                    descripcion, 
                    fecha,
                    tipo, 
                    dias, 
                    restante, 
                    horas,
                    minutos, 
                    duracion,
                    tipo_mision,
                    fecha_inicio, 
                    fecha_fin,
                    fecha_creacion, 
                    usuario_creacion)
                    VALUES  
                     ('',
                     '{$cedula}',"
                     . "'{$_POST['descripcion']}',"
                     . "'{$fecha_creacion}',"
                     . "'{$_POST['tipo_registro']}',"
                     . "'{$dias}',"
                     . "'{$restantes}',"
                     . "'{$horas}',"
                     . "'{$minutos}',"
                     . "'{$duracion}',"
                     . "'{$_POST['tipo_mision']}',"
                     . "'{$fecha_inicio}',"
                     . "'{$fecha_fin}',"
                     . " '{$fecha_creacion}',"
                     . "'{$usuario}')";
        $resultado_expediente=query($consulta_expediente,$conexion);         
                        

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
        $monto_nuevo = str_replace(",","",$_POST['salario_nuevo']); 
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
    
    //EXCEDENTE - GUARDAR
    else if ($_POST['tipo_registro']=="34")
    {       

        if($_POST['fecha']!='')
                $fecha=fecha_sql($_POST['fecha']);        
        

        $fecha_creacion = date('Y-m-d');
        
        
        $monto=str_replace(",","",$_POST['monto']);
        
        //INSERCIÓN EXPEDIENTE
        $consulta="INSERT INTO expediente
                (cod_expediente_det, cedula, descripcion, fecha, monto, tipo, fecha_creacion, usuario_creacion)
                VALUES  
                 ('','{$cedula}','{$_POST['descripcion']}','{$fecha}','{$monto}','{$_POST['tipo_registro']}','{$fecha_creacion}','{$usuario}')";
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

        if($_POST['fecha_inicio_anterior']!='')
                $fecha_inicio_anterior=fecha_sql($_POST['fecha_inicio_anterior']);
        
        if($_POST['fecha_inicio']!='')
                $fecha_inicio=fecha_sql($_POST['fecha_inicio']);
        
        if($_POST['fecha_permanencia_anterior']!='')
                $fecha_permanencia_anterior=fecha_sql($_POST['fecha_permanencia_anterior']);
        
        if($_POST['fecha_permanencia']!='')
                $fecha_permanencia=fecha_sql($_POST['fecha_permanencia']);
        
        if($_POST['fecha_inicio_periodo_anterior']!='')
                $fecha_inicio_periodo_anterior=fecha_sql($_POST['fecha_inicio_periodo_anterior']);
        
        if($_POST['fecha_inicio_periodo']!='')
                $fecha_inicio_periodo=fecha_sql($_POST['fecha_inicio_periodo']);
        
        if($_POST['fecha_fin_periodo_anterior']!='')
                $fecha_fin_periodo_anterior=fecha_sql($_POST['fecha_fin_periodo_anterior']);
        
        if($_POST['fecha_fin_periodo']!='')
                $fecha_fin_periodo=fecha_sql($_POST['fecha_fin_periodo']);
        
        if($_POST['fecha_decreto_anterior']!='')
                $fecha_decreto_anterior=fecha_sql($_POST['fecha_decreto_anterior']);
        
        if($_POST['fecha_decreto_nuevo']!='')
                $fecha_decreto_nuevo=fecha_sql($_POST['fecha_decreto_nuevo']);
        
        if($_POST['numero_decreto_anterior']!='')
                $numero_decreto_anterior=$_POST['numero_decreto_anterior'];
        
        if($_POST['numero_decreto_nuevo']!='')
                $numero_decreto_nuevo=$_POST['numero_decreto_nuevo'];
        

        $fecha_creacion = date('Y-m-d');
        
        
        //INSERCIÓN EXPEDIENTE
        $consulta="INSERT INTO expediente
                (cod_expediente_det, cedula, descripcion, fecha, fecha_inicio, fecha_inicio_periodo, fecha_fin_periodo, 
                fecha_inicio_periodo_anterior, fecha_fin_periodo_anterior, fecha_permanencia, fecha_permanencia_anterior,
                fecha_inicio_anterior, situacion_anterior, situacion_nueva, fecha_decreto_ingreso_anterior,fecha_decreto_ingreso_nuevo,
                numero_decreto_anterior,numero_decreto,tipo, fecha_creacion, usuario_creacion)
                VALUES  
                 ('','{$cedula}','{$_POST['descripcion']}','{$fecha_creacion}','{$fecha_inicio}', '{$fecha_inicio_periodo}', "
                 . "'{$fecha_fin_periodo}', '{$fecha_inicio_periodo_anterior}', '{$fecha_fin_periodo_anterior}', '{$fecha_permanencia}',"
                 . " '{$fecha_permanencia_anterior}','{$fecha_inicio_anterior}', '{$_POST['situacion_anterior']}','{$_POST['situacion_nueva']}',"
                 . "'{$fecha_decreto_anterior}','{$fecha_decreto_nuevo}','{$numero_decreto_anterior}','{$numero_decreto_nuevo}',"
                 . "'{$_POST['tipo_registro']}','{$fecha_creacion}','{$usuario}')";
//        echo $consulta;
//        exit;
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
    
     //CARRERA MIGRATORIA - GUARDAR
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
            
            
            
            $fecha_creacion = date('Y-m-d');
                                    
             //INSERCIÓN EXPEDIENTE
            $consulta="INSERT INTO expediente
                        (cod_expediente_det, 
                        cedula, 
                        descripcion, 
                        fecha, 
                        cm_fecha_notificacion_ingreso_anterior, 
                        cm_fecha_notificacion_ingreso,
                        cm_numero_resolucion_anterior, 
                        cm_numero_resolucion,
                        cm_fecha_resolucion_anterior, 
                        cm_fecha_resolucion, 
                        cm_tipo_proceso_anterior, 
                        cm_tipo_proceso, 
                        cm_sobresueldo_anterior, 
                        cm_sobresueldo, 
                        cm_gasto_responsabilidad_anterior,
                        cm_gasto_responsabilidad, 
                        cm_gasto_representacion_anterior,
                        cm_gasto_representacion,
                        cm_incentivo_titulo_anterior, 
                        cm_incentivo_titulo, 
                        cm_ascenso_anterior, 
                        cm_ascenso, 
                        cm_directiva_confidencialidad_anterior,
                        cm_directiva_confidencialidad, 
                        cm_carta_compromiso_anterior, 
                        cm_carta_compromiso, 
                        cm_fecha_notificacion_homologacion_anterior,
                        cm_fecha_notificacion_homologacion,
                        cm_numero_resolucion_homologacion_anterior,
                        cm_numero_resolucion_homologacion,
                        cm_fecha_resolucion_homologacion_anterior,
                        cm_fecha_resolucion_homologacion,
                        cm_acreditacion_personal_ordinario_anterior,
                        cm_acreditacion_personal_ordinario,
                        cm_auditoria_puesto_anterior,
                        cm_auditoria_puesto,
                        cm_placa_anterior,
                        cm_placa,
                        cm_promocion_anterior,
                        cm_promocion,
                        cm_jubliacion_partida_anterior,
                        cm_jubliacion_partida,
                        cm_jubilacion_anio_anterior,
                        cm_jubilacion_anio,
                        tipo, 
                        fecha_creacion, 
                        usuario_creacion)
                        VALUES  
                        ('',
                        '{$cedula}',"
                        . "'{$_POST['descripcion']}', "
                        . "'{$fecha_creacion}',"
                        . "'{$cm_fecha_notificacion_ingreso_anterior}',"
                        . "'{$cm_fecha_notificacion_ingreso}',"
                        . "'{$_POST['cm_numero_resolucion_anterior']}',"
                        . "'{$_POST['cm_numero_resolucion']}',"
                        . "'{$cm_fecha_resolucion_anterior}',"
                        . "'{$cm_fecha_resolucion}',"
                        . "'{$_POST['cm_tipo_proceso_anterior']}',"
                        . "'{$_POST['cm_tipo_proceso']}',"
                        . "'{$_POST['cm_sobresueldo_anterior']}',"
                        . "'{$_POST['cm_sobresueldo']}',"
                        . "'{$_POST['cm_gasto_responsabilidad_anterior']}',"
                        . "'{$_POST['cm_gasto_responsabilidad']}',"
                        . "'{$_POST['cm_gasto_representacion_anterior']}',"
                        . "'{$_POST['cm_gasto_representacion']}',"
                        . "'{$_POST['cm_incentivo_titulo_anterior']}',"
                        . "'{$_POST['cm_incentivo_titulo']}',"
                        . "'{$_POST['cm_ascenso_anterior']}',"
                        . "'{$_POST['cm_ascenso']}',"
                        . "'{$_POST['cm_directiva_confidencialidad_anterior']}',"
                        . "'{$_POST['cm_directiva_confidencialidad']}',"
                        . "'{$_POST['cm_carta_compromiso_anterior']}',"
                        . "'{$_POST['cm_carta_compromiso']}',"
                        . "'{$cm_fecha_notificacion_homologacion_anterior}',"
                        . "'{$cm_fecha_notificacion_homologacion}',"
                        . "'{$_POST['cm_numero_resolucion_homologacion_anterior']}',"
                        . "'{$_POST['cm_numero_resolucion_homologacion']}',"
                        . "'{$cm_fecha_resolucion_homologacion_anterior}',"
                        . "'{$cm_fecha_resolucion_homologacion}',"
                        . "'{$_POST['cm_acreditacion_personal_ordinario_anterior']}',"
                        . "'{$_POST['cm_acreditacion_personal_ordinario']}',"
                        . "'{$_POST['cm_auditoria_puesto_anterior']}',"
                        . "'{$_POST['cm_auditoria_puesto']}',"
                        . "'{$_POST['cm_placa_anterior']}',"
                        . "'{$_POST['cm_placa']}',"
                        . "'{$_POST['cm_promocion_anterior']}',"
                        . "'{$_POST['cm_promocion']}',"
                        . "'{$_POST['cm_jubliacion_partida']}',"
                        . "'{$_POST['cm_jubliacion_partida']}',"
                        . "'{$_POST['cm_jubilacion_anio_anterior']}',"
                        . "'{$_POST['cm_jubilacion_anio']}',"
                        . "'{$_POST['tipo_registro']}',"
                        . "'{$fecha_creacion}',"
                        . "'{$usuario}')";    
//            echo $consulta;
            $resultado=query($consulta,$conexion);

    }
    
    //INVESTIGACIONES - GUARDAR
    if ($_POST['tipo_registro']=="61"){
            if($_POST['fecha']!='')
                    $fecha=fecha_sql($_POST['fecha']);	
            $fecha_creacion = date("Y-m-d");
             //INSERCIÓN EXPEDIENTE
            $consulta="INSERT INTO expediente
                        (cod_expediente_det, 
                        cedula, 
                        descripcion, 
                        institucion_publica, 
                        tipo_investigacion, 
                        tipo_comparecencia_investigacion, 
                        tipo_falta_investigacion,
                        fecha,
                        tipo, 
                        fecha_creacion, 
                        usuario_creacion )
                        VALUES  
                        ('','{$cedula}',"
                        . "'{$_POST['descripcion']}', "
                        . "'{$_POST['institucion']}',"
                        . "'{$_POST['tipo_investigacion']}', "
                        . "'{$_POST['tipo_comparecencia']}',"
                        . "'{$_POST['tipo_falta']}',"
                        . "'{$fecha}',"
                        . "'{$_POST['tipo_registro']}',"
                        . "'{$fecha_creacion}',"
                        . "'{$usuario}')";
            $resultado=query($consulta,$conexion);

    }
    
     //EXTEMPORANEAS - GUARDAR
    if ($_POST['tipo_registro']=="62"){
            if($_POST['fecha']!='')
                    $fecha=fecha_sql($_POST['fecha']);	
            $fecha_creacion = date("Y-m-d");
             //INSERCIÓN EXPEDIENTE
            $consulta="INSERT INTO expediente
                        (cod_expediente_det, 
                        cedula, 
                        descripcion,
                        fecha,
                        tipo, 
                        fecha_creacion, 
                        usuario_creacion )
                        VALUES  
                        ('','{$cedula}',"
                        . "'{$_POST['descripcion']}', "
                        . "'{$fecha}',"
                        . "'{$_POST['tipo_registro']}',"
                        . "'{$fecha_creacion}',"
                        . "'{$usuario}')";
            $resultado=query($consulta,$conexion);

    }
    
    //DIAGNOSTICO - GUARDAR
    if ($_POST['tipo_registro']=="63"){
            if($_POST['fecha']!='')
                    $fecha=fecha_sql($_POST['fecha']);	
            $fecha_creacion = date("Y-m-d");
             //INSERCIÓN EXPEDIENTE
            $consulta="INSERT INTO expediente
                        (cod_expediente_det, 
                        cedula, 
                        descripcion,
                        fecha,
                        id_centro, 
                        num_certificado, 
                        nombre_medico,
                        tipo, 
                        fecha_creacion, 
                        usuario_creacion )
                        VALUES  
                        ('','{$cedula}',"
                        . "'{$_POST['descripcion']}', "
                        . "'{$fecha}',"
                        . "'{$_POST['id_centro']}',"
                        . "'{$_POST['num_certificado']}', "
                        . "'{$_POST['nombre_medico']}',"
                        . "'{$_POST['tipo_registro']}',"
                        . "'{$fecha_creacion}',"
                        . "'{$usuario}')";
            $resultado=query($consulta,$conexion);

    }
    
    //EVALUACION DE ANTECEDENTES - GUARDAR
    else if ($_POST['tipo_registro']=="64")
    {
            if($_POST['fecha']!='')
                    $fecha=fecha_sql($_POST['fecha']);
            
            $fecha_creacion = date('Y-m-d');
            $ascenso_anio_1="2016";
            $ascenso_anio_2="2017";
            $ascenso_anio_actual="2018";
                                    
             //INSERCIÓN EXPEDIENTE
            $consulta="INSERT INTO expediente
                        (cod_expediente_det, 
                        cedula, 
                        descripcion, 
                        fecha, 
                        ascenso_salario_anterior, 
                        ascenso_salario_nuevo,
                        ascenso_nivel_anterior, 
                        ascenso_nivel_nuevo,
                        ascenso_cargo_anterior, 
                        ascenso_cargo_nuevo, 
                        ascenso_anio_1, 
                        ascenso_anio_2, 
                        ascenso_anio_actual, 
                        ascenso_desempenio_1_1, 
                        ascenso_desempenio_1_2,
                        ascenso_desempenio_2_1, 
                        ascenso_desempenio_2_2,
                        ascenso_desempenio_total,
                        ascenso_desempenio_porcentaje, 
                        ascenso_conducta_1, 
                        ascenso_conducta_2,
                        ascenso_conducta_actual,
                        ascenso_conducta_porcentaje, 
                        ascenso_participativa_actual,
                        ascenso_participativa_porcentaje, 
                        ascenso_investigacion, 
                        ascenso_puntaje_total,
                        dejado,
                        tipo, 
                        fecha_creacion, 
                        usuario_creacion)
                        VALUES  
                        ('',
                        '{$cedula}',"
                        . "'{$_POST['descripcion']}', "
                        . "'{$fecha_creacion}',"
                        . "'{$_POST['ascenso_salario_anterior']}',"
                        . "'{$_POST['ascenso_salario_nuevo']}',"
                        . "'{$_POST['ascenso_nivel_anterior']}',"
                        . "'{$_POST['ascenso_nivel_nuevo']}',"
                        . "'{$_POST['ascenso_cargo_anterior']}',"
                        . "'{$_POST['ascenso_cargo_nuevo']}',"
                        . "'{$ascenso_anio_1}',"
                        . "'{$ascenso_anio_2}',"
                        . "'{$ascenso_anio_actual}',"
                        . "'{$_POST['ascenso_desempenio_1_1']}',"
                        . "'{$_POST['ascenso_desempenio_1_2']}',"
                        . "'{$_POST['ascenso_desempenio_2_1']}',"
                        . "'{$_POST['ascenso_desempenio_2_2']}',"
                        . "'{$_POST['ascenso_desempenio_total']}',"
                        . "'{$_POST['ascenso_desempenio_porcentaje']}',"
                        . "'{$_POST['ascenso_conducta_1']}',"
                        . "'{$_POST['ascenso_conducta_2']}',"
                        . "'{$_POST['ascenso_conducta_actual']}',"
                        . "'{$_POST['ascenso_conducta_porcentaje']}',"
                        . "'{$_POST['ascenso_participativa_actual']}',"
                        . "'{$_POST['ascenso_participativa_porcentaje']}',"
                        . "'{$_POST['ascenso_investigacion']}',"
                        . "'{$_POST['ascenso_puntaje_total']}',"
                        . "'{$_POST['formato']}',"
                        . "'{$_POST['tipo_registro']}',"
                        . "'{$fecha_creacion}',"
                        . "'{$usuario}')";    
//            echo $consulta;
            $resultado=query($consulta,$conexion);

    }
    
     //TRABAJO SOCIAL - GUARDAR
    else if ($_POST['tipo_registro']=="65")
    {
            if($_POST['fecha_entrevista']!='')
                    $fecha_entrevista=fecha_sql($_POST['fecha_entrevista']);

            if($_POST['fecha_elaboracion']!='')
                    $fecha_elaboracion=fecha_sql($_POST['fecha_elaboracion']);       
            
            $fecha_creacion = date('Y-m-d');
                                    
             //INSERCIÓN EXPEDIENTE
            $consulta="INSERT INTO expediente
                        (cod_expediente_det, 
                        cedula,    
                        descripcion,
                        fecha, 
                        fecha_entrevista, 
                        fecha_elaboracion,
                        nombre_adulto_responsable, 
                        cedula_adulto_responsable,
                        motivo_investigacion, 
                        socioeconomica_relaciones_familiares, 
                        socioeconomica_salud, 
                        socioeconomica_vivienda, 
                        socioeconomica_economica, 
                        socioeconomica_ingresos, 
                        socioeconomica_egresos,
                        socioeconomica_total_ingresos, 
                        socioeconomica_total_egresos,
                        socioeconomica_total_disponible,
                        situacion_encontrada, 
                        labor_trabajador_social, 
                        diagnostico, 
                        recomendaciones, 
                        condicion_salud,
                        especialistas_atencion, 
                        metodologia_atencion, 
                        conclusiones,
                        tipo, 
                        subtipo, 
                        fecha_creacion, 
                        usuario_creacion)
                        VALUES  
                        ('',
                        '{$cedula}',"                        
                        . "'{$_POST['recomendaciones']}',"
                        . "'{$fecha_creacion}',"
                        . "'{$fecha_entrevista}',"
                        . "'{$fecha_elaboracion}',"
                        . "'{$_POST['nombre_adulto_responsable']}',"
                        . "'{$_POST['cedula_adulto_responsable']}',"
                        . "'{$_POST['motivo_investigacion']}',"
                        . "'{$_POST['socioeconomica_relaciones_familiares']}',"
                        . "'{$_POST['socioeconomica_salud']}',"
                        . "'{$_POST['socioeconomica_vivienda']}',"
                        . "'{$_POST['socioeconomica_economica']}',"
                        . "'{$_POST['socioeconomica_ingresos']}',"
                        . "'{$_POST['socioeconomica_egresos']}',"
                        . "'{$_POST['socioeconomica_total_ingresos']}',"
                        . "'{$_POST['socioeconomica_total_egresos']}',"
                        . "'{$_POST['socioeconomica_total_disponible']}',"
                        . "'{$_POST['situacion_encontrada']}',"
                        . "'{$_POST['labor_trabajador_social']}',"
                        . "'{$_POST['diagnostico']}',"
                        . "'{$_POST['recomendaciones']}',"
                        . "'{$_POST['condicion_salud']}',"
                        . "'{$_POST['especialistas_atencion']}',"
                        . "'{$_POST['metodologia_atencion']}',"
                        . "'{$_POST['conclusiones']}',"
                        . "'{$_POST['tipo_registro']}',"
                        . "'{$_POST['tipo_tiporegistro']}',"
                        . "'{$fecha_creacion}',"
                        . "'{$usuario}')";    
//            echo $consulta;
            $resultado=query($consulta,$conexion);

    }
    
    //RENOVACION DE CONTRATOS - GUARDAR
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
        
        $fecha_creacion = date('Y-m-d');
        
                                
        //INSERCIÓN EXPEDIENTE
        $consulta="INSERT INTO expediente
                        (cod_expediente_det, 
                        cedula, 
                        descripcion, 
                        fecha, 
                        fecha_inicio,
                        fecha_fin, 
                        dias, 
                        numero_resolucion,
                        fecha_resolucion, 
                        proyecto_anterior, 
                        proyecto,
                        monto, 
                        monto_nuevo,
                        cod_cargo_anterior, 
                        cod_cargo_nuevo, 
                        tipo, 
                        fecha_creacion, 
                        usuario_creacion)
                        VALUES  
                        ('',
                        '{$cedula}',"
                        . "'{$_POST['descripcion']}', "
                        . "'{$fecha}',"
                        . "'{$fecha_inicio}',"
                        . "'{$fecha_fin}',"
                        . "'{$_POST['dias']}',"
                        . "'{$_POST['numero_resolucion']}',"
                        . "'{$fecha_resolucion}',"
                        . "'{$_POST['proyecto_anterior']}',"
                        . "'{$_POST['proyecto_nuevo']}',"
                        . "'{$_POST['salario_anterior']}',"
                        . "'{$_POST['salario_nuevo']}',"
                        . "'{$_POST['cargo_anterior']}',"
                        . "'{$_POST['cargo_nuevo']}',"
                        . "'{$_POST['tipo_registro']}',"
                        . "'{$fecha_creacion}',"
                        . "'{$usuario}')";    
        $resultado=query($consulta,$conexion);
    
    }
    
     //RENOVACION DE CARGO - GUARDAR
    else if ($_POST['tipo_registro']=="67")
    {
                        
            if($_POST['fecha']!='')
                    $fecha=fecha_sql($_POST['fecha']);
            
            $fecha_creacion = date('Y-m-d');
            
                                    
             //INSERCIÓN EXPEDIENTE
            $consulta="INSERT INTO expediente
                        (cod_expediente_det, 
                        cedula, 
                        descripcion, 
                        fecha, 
                        monto, 
                        monto_nuevo,
                        cod_cargo_anterior, 
                        cod_cargo_nuevo, 
                        tipo, 
                        fecha_creacion, 
                        usuario_creacion)
                        VALUES  
                        ('',
                        '{$cedula}',"
                        . "'{$_POST['descripcion']}', "
                        . "'{$fecha}',"
                        . "'{$_POST['salario_anterior']}',"
                        . "'{$_POST['salario_nuevo']}',"
                        . "'{$_POST['cargo_anterior']}',"
                        . "'{$_POST['cargo_nuevo']}',"
                        . "'{$_POST['tipo_registro']}',"
                        . "'{$fecha_creacion}',"
                        . "'{$usuario}')";    
//            echo $consulta;
            $resultado=query($consulta,$conexion);
    
    }
    
    //REGISTRO DE CONTRATOS - GUARDAR
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
        
        $fecha_creacion = date('Y-m-d');
        
                                
        //INSERCIÓN EXPEDIENTE
        $consulta="INSERT INTO expediente
                        (
                        cedula, 
                        descripcion, 
                        fecha, 
                        fecha_inicio,
                        fecha_fin, 
                        dias, 
                        numero_resolucion,
                        fecha_resolucion, 
                        proyecto,
                        gerencia_nueva,
                        monto_nuevo,
                        cod_cargo_anterior,
                        cod_cargo_nuevo,  
                        tipo,
                        subtipo,
                        fecha_creacion, 
                        usuario_creacion)
                        VALUES  
                        (
                        '{$cedula}',"
                        . "'{$_POST['descripcion']}', "
                        . "'{$fecha_creacion}',"
                        . "'{$fecha_inicio}',"
                        . "'{$fecha_fin}',"
                        . "'{$_POST['dias']}',"
                        . "'{$_POST['numero_resolucion']}',"
                        . "'{$fecha_resolucion}',"
                        . "'{$_POST['proyecto_nuevo']}',"
                        . "'{$_POST['gerencia_nueva']}',"
                        . "'{$_POST['salario_nuevo']}',"
                        . "'{$_POST['cargo_anterior']}',"
                        . "'{$_POST['cargo_nuevo']}',"
                        . "'{$_POST['tipo_registro']}',"
                        . "'{$_POST['tipo_tiporegistro']}',"
                        . "'{$fecha_creacion}',"
                        . "'{$usuario}')";    
        $resultado=query($consulta,$conexion);
        //echo "<br><br><br><br>".$consulta;exit;
    
    }

    //Curriculo - GUARDAR
    else if($_POST['tipo_registro']>=69 && $_POST['tipo_registro']<= 79)
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
                ( cedula, descripcion, tipo, fecha,fecha_fin)
                VALUES  
                ('{$cedula}','{$_POST['descripcion']}','{$_POST['tipo_registro']}','".$fecha."','".$fecha_vencimiento."')";

            $resultado=query($consulta,$conexion);

            if($resultado)
            {
                //REGISTRAR DOCUMENTO
                $ultimo_id = mysqli_insert_id($conexion);

                $consulta = "INSERT INTO `expediente_documento` 
                        ( `nombre_documento`, `descripcion`, `url_documento`, `fecha_registro`, `fecha_vencimiento`, `cod_expediente_det`) 
                        VALUES 
                        ('{$_POST['tipo_registro']}', '{$_POST['descripcion']}', '{$archivo}', '{$fecha}', '{$fecha_vencimiento}', '{$ultimo_id}')";
                $resultado=query($consulta,$conexion);
            }
    }

    //Entrega Kit - GUARDAR
    else if($_POST['tipo_registro']==80)
    {

        $fecha = str_replace('-', '/', $_POST['fecha']);
        $fecha = fecha_sql($fecha);
        $fecha = date('Y-m-d');

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

        // REGISTRAR EXPEDIENTE
        $consulta="INSERT INTO expediente
        ( cedula, descripcion, tipo, fecha, seccion_anterior, seccion_nueva, funcion_anterior, funcion_nueva, 
        institucion_anterior, tipo_estudio, nivel_actual, cargo_estructura, cargo_funcion, concepto )
        VALUES  
        ('{$cedula}',
        '{$_POST['descripcion']}',
        '{$_POST['tipo_registro']}',
        '".$fecha."',
        '".$seccion_anterior."',
        '".$seccion_nueva."',
        '".$funcion_anterior."',
        '".$funcion_nueva."',
        '".$institucion_anterior."',
        '".$tipo_estudio."',
        '".$nivel_actual."',
        '".$cargo_estructura."',
        '".$cargo_funcion."',
        '".$concepto."')";

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
        
        //fecha,fecha_resolucion,fecha_inicio,fecha_hora_inicio,posicion_anterior,proyecto,monto,monto_nuevo,comentarios_1,comentarios_2,analista,concepto

        $posicion_anterior  = (isset($_POST['posicion_anterior']))  ? $_POST['posicion_anterior']  : 0;
        $cargo_estructura  = (isset($_POST['cargo_estructura']))  ? mb_convert_encoding($_POST['cargo_estructura'],'UTF-8')  : 0;
        $monto  = (isset($_POST['monto']))  ? $_POST['monto']  : 0;
        $monto_nuevo  = (isset($_POST['monto_nuevo']))  ? $_POST['monto_nuevo']  : 0;
        $comentarios_1  = (isset($_POST['comentarios_1']))  ? mb_convert_encoding($_POST['comentarios_1'],'UTF-8')  : 0;
        $comentarios_2  = (isset($_POST['comentarios_2']))  ? mb_convert_encoding($_POST['comentarios_2'],'UTF-8')  : "";
        $analista  = (isset($_POST['analista']))  ? $_POST['analista']  : "";
        $concepto  = (isset($_POST['concepto']))  ?  mb_convert_encoding($_POST['concepto'] ,'UTF-8') : "";
        //obtener consecutivo
        $sql2 ="SELECT consecutivo_reporte_incidencia FROM nomempresa";
        $res2=query($sql2,$conexion);
        $consecutivo = $res2->fetch_object();
        $c=$consecutivo->consecutivo_reporte_incidencia+1;
        //actualizar consecutivo      
         $consulta2="UPDATE nomempresa SET consecutivo_reporte_incidencia='{$c}' WHERE cod_emp=1";
         $resultado=query($consulta2,$conexion);
        // REGISTRAR EXPEDIENTE        
        $consulta="INSERT INTO expediente
        ( cedula, descripcion, tipo, subtipo, fecha, fecha_resolucion,fecha_inicio,posicion_anterior,cargo_estructura,monto,monto_nuevo
        ,numero_resolucion,comentarios_1,comentarios_2,analista,concepto,numero_decreto,fecha_hora_inicio )
        VALUES  
        ('{$cedula}',
        '{$_POST['descripcion']}',
        '{$_POST['tipo_registro']}',
        '{$_POST['tipo_tiporegistro']}',
        '".$fecha."',
        '".$fecha_resolucion."',
        '".$fecha_inicio."',
        '".$posicion_anterior."',
        '".$cargo_estructura."',
        '".$monto."',
        '".$monto_nuevo."',
        '".$_POST['num_incidente']."',
        '".$comentarios_1."',
        '".$comentarios_2."',
        '".$analista."',
        '".$concepto."',
        '".$_POST['numpre']."',
        '".$_POST['fecha_hora_inicio']."')";
        //INSERCIÓN DESTITUCION
        $exp_id = mysqli_insert_id($conexion);

        $resultado=query($consulta,$conexion);
        if(isset($_POST['numcuo1']) && $resultado)
        {
                $consulta="INSERT INTO nomprestamos_cabecera_tmp "
                        . "SET numpre=$_POST[numpre], "
                        . "ficha=$_POST[ficha], "
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
                        . "frededu='$_POST[frededu]'";
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
                        numpre=$_POST[numpre],
                        ficha=$_POST[ficha],
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
        }else{
                echo "no se registro en prestamo";
        }
    }

    //Solicitud Empleo - GUARDAR
    else if($_POST['tipo_registro']==82 )
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
                ( cedula, descripcion, tipo, fecha,fecha_fin)
                VALUES  
                ('{$cedula}','{$_POST['descripcion']}','{$_POST['tipo_registro']}','".$fecha."','".$fecha_vencimiento."')";

            $resultado=query($consulta,$conexion);

            if($resultado)
            {
                //REGISTRAR DOCUMENTO
                $ultimo_id = mysqli_insert_id($conexion);

                $consulta = "INSERT INTO `expediente_documento` 
                        ( `nombre_documento`, `descripcion`, `url_documento`, `fecha_registro`, `fecha_vencimiento`, `cod_expediente_det`) 
                        VALUES 
                        ('{$_POST['tipo_registro']}', '{$_POST['descripcion']}', '{$archivo}', '{$fecha}', '{$fecha_vencimiento}', '{$ultimo_id}')";
                $resultado=query($consulta,$conexion);
            }
    }
    
    //EXCEDENTE - GUARDAR
    else if ($_POST['tipo_registro']=="83")
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
    /*
    //ENTREGA DE IMPLEMENTOS - GUARDAR
    else if ($_POST['tipo_registro']=="69")
    {
                        
            if($_POST['fecha']!='')
                    $fecha=fecha_sql($_POST['fecha']);
            
            
            
            $fecha_creacion = date('Y-m-d');
            
                                    
             //INSERCIÓN EXPEDIENTE
            $consulta="INSERT INTO expediente
                        (
                        cedula, 
                        descripcion, 
                        fecha, 
                        tipo, 
                        fecha_creacion, 
                        usuario_creacion)
                        VALUES  
                        (
                        '{$cedula}',"
                        . "'{$_POST['descripcion']}', "
                        . "'{$fecha}',"
                        . "'{$_POST['tipo_registro']}',"
                        . "'{$fecha_creacion}',"
                        . "'{$usuario}')";    
//            echo $consulta;
            $resultado=query($consulta,$conexion);
            
            $ultimo_id = mysqli_insert_id($conexion);
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
                        . "'{$ultimo_id}')";    
            //            echo $consulta;
                        $resultado_implemento=query($consulta_implemento,$conexion);
                }
            }
    
    }
    
    //AUSENCIAS - GUARDAR
    else if ($_POST['tipo_registro']=="70"){
            if($_POST['fecha_inicio']!='')
                    $fecha_inicio=fecha_sql($_POST['fecha_inicio']);

            if($_POST['fecha_fin']!='')
                    $fecha_fin=fecha_sql($_POST['fecha_fin']);

//            if($_POST['fecha_aprobado']!='')
//                    $fecha_aprobado=fecha_sql($_POST['fecha_aprobado']);

            if($_POST['fecha_resolucion']!='')
                    $fecha_resolucion=fecha_sql($_POST['fecha_resolucion']);	

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
                        (cod_expediente_det, 
                        cedula,
                        descripcion,
                        desde, 
                        fecha_inicio,
                        hasta, 
                        fecha_fin, 
                        dias, 
                        horas, 
                        minutos, 
                        duracion,
                        fecha, 
                        numero_resolucion,                        
                        fecha_resolucion, 
                        id_centro, 
                        nombre_medico, 
                        proyecto,
                        tipo, 
                        subtipo,
                        fecha_creacion,
                        usuario_creacion )
                        VALUES  
                        ('',
                        '{$cedula}',"
                        . "'{$_POST['descripcion']}',"
                        . " '{$_POST['desde']}',"
                        . " '{$fecha_inicio}',"
                        . "'{$_POST['hasta']}', "
                        . "'{$fecha_fin}',"
                        . "'{$dias}',"
                        . "'{$horas}',"
                        . "'{$minutos}',"
                        . "'{$duracion}',"
                        . "'{$fecha}',"
                        . "'{$_POST['numero_resolucion']}', "                        
                        . "'{$fecha_resolucion}',"
                        . "'{$_POST['id_centro']}',"
                        . " '{$_POST['nombre_medico']}',"
                        . " '{$_POST['proyecto']}',"
                        . "'{$_POST['tipo_registro']}',"
                        . "'{$_POST['tipo_tiporegistro']}',"
                        . "'{$fecha_creacion}',"
                        . "'{$usuario}')";
//            echo $consulta;
            $resultado=query($consulta,$conexion);


    }

    //TARDANZAS - GUARDAR
    else if ($_POST['tipo_registro']=="71"){
        if($_POST['fecha_inicio']!='')
                $fecha_inicio=fecha_sql($_POST['fecha_inicio']);

        if($_POST['fecha_fin']!='')
                $fecha_fin=fecha_sql($_POST['fecha_fin']);

        if($_POST['fecha_resolucion']!='')
                $fecha_resolucion=fecha_sql($_POST['fecha_resolucion']);	

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
                (cod_expediente_det, 
                cedula,
                descripcion,
                desde, 
                fecha_inicio,
                hasta, 
                fecha_fin, 
                dias, 
                horas, 
                minutos, 
                duracion,
                fecha, 
                numero_resolucion,                        
                fecha_resolucion, 
                id_centro, 
                nombre_medico, 
                proyecto,
                tipo, 
                subtipo,
                fecha_creacion,
                usuario_creacion )
                VALUES  
                ('',
                '{$cedula}',"
                . "'{$_POST['descripcion']}',"
                . " '{$_POST['desde']}',"
                . " '{$fecha_inicio}',"
                . "'{$_POST['hasta']}', "
                . "'{$fecha_fin}',"
                . "'{$dias}',"
                . "'{$horas}',"
                . "'{$minutos}',"
                . "'{$duracion}',"
                . "'{$fecha}',"
                . "'{$_POST['numero_resolucion']}', "                        
                . "'{$fecha_resolucion}',"
                . "'{$_POST['id_centro']}',"
                . " '{$_POST['nombre_medico']}',"
                . " '{$_POST['proyecto']}',"
                . "'{$_POST['tipo_registro']}',"
                . "'{$_POST['tipo_tiporegistro']}',"
                . "'{$fecha_creacion}',"
                . "'{$usuario}')";
//            echo $consulta;
        $resultado=query($consulta,$conexion);


    }*/
?>
