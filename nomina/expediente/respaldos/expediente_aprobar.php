<?php 
session_start();
	
//DECLARACION DE LIBRERIAS
require_once '../lib/common.php';
require_once '../paginas/func_bd.php';
include ("../paginas/funciones_nomina.php");
//include ("../header.php");

include ("../header4.php");
$conexion=conexion();
$correlativo = 0;

if(isset($_REQUEST['cod_aprobar']))
    $codigo=$_REQUEST['cod_aprobar'];

$consulta_expediente="SELECT * FROM expediente WHERE cod_expediente_det='$codigo'";
$resultado_expediente=query($consulta_expediente,$conexion);
$fetch_expediente=fetch_array($resultado_expediente,$conexion);
$tipo=$fetch_expediente['tipo'];
$subtipo=$fetch_expediente['subtipo'];
$cedula=$fetch_expediente['cedula'];
$descripcion=$fetch_expediente['descripcion'];
$fecha=$fetch_expediente['fecha'];
$fecha_actual = date('Y-m-d');

$consulta_expediente_tipo="SELECT * FROM expediente_tipo WHERE id_expediente_tipo='$tipo'";
$resultado_expediente_tipo=query($consulta_expediente_tipo,$conexion);
$fetch_expediente_tipo=fetch_array($resultado_expediente_tipo,$conexion);
$expediente_tipo=$fetch_expediente_tipo['nombre_tipo'];
//echo "TIPO EXPEDIENTE: "; echo $expediente_tipo;

$consulta_expediente_subtipo="SELECT * FROM expediente_subtipo WHERE id_expediente_subtipo='$subtipo'";
$resultado_expediente_subtipo=query($consulta_expediente_subtipo,$conexion);
$fetch_expediente_subtipo=fetch_array($resultado_expediente_subtipo,$conexion);
$expediente_subtipo=$fetch_expediente_subtipo['nombre_subtipo'];
//echo "\n"; echo "SUBTIPO EXPEDIENTE: "; echo $expediente_subtipo;

$situacion = $expediente_tipo." ".$expediente_subtipo;
//echo "\n"; echo "SITUACION: "; echo $situacion;

//ACTUALIZACION SITUACIONES (ESTATUS)
$consulta_nomsituacion="SELECT situacion FROM nomsituaciones";
$resultado_nomsituacion=query($consulta_nomsituacion,$conexion);

$bandera=0;

while($fila_nomsituacion=fetch_array($resultado_nomsituacion))
{
//    echo "\n"; echo "NOMSITUACION: "; echo $fila_nomsituacion['situacion'];
    if($situacion==$fila_nomsituacion['situacion'])
    {
        $bandera=1;
    }
}

if($bandera==0)
{
     $consulta_situacion="INSERT INTO nomsituaciones
                        (codigo, situacion)
                        VALUES  
                        ('','{$situacion}')";
//     echo "\n"; echo "CONSULTA NOMSITUACION: "; echo $consulta_situacion;
    $resultado_situacion=query($consulta_situacion,$conexion);          
}
//exit;

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


$usuario = $_SESSION['usuario'];

$consulta_posicion="SELECT Posicion FROM posicionempleado WHERE IdEmpleado='$personal_id'";
$resultado_posicion=query($consulta_posicion,$conexion);
$fetch_posicion=fetch_array($resultado_posicion,$conexion);
$posicion=$fetch_posicion['Posicion'];

$consulta_adjunto="SELECT * FROM expediente_adjunto WHERE cod_expediente_det='$codigo'";
$resultado_adjunto=query($consulta_adjunto,$conexion);
$fetch_adjunto=fetch_array($resultado_adjunto,$conexion);
$filas_adjunto  = count($fetch_adjunto);

//ESTUDIOS ACADEMICOS  - APROBAR
if ($tipo=="1")
{   
    $ejerce=$fetch_expediente['ejerce'];
     $tipo_accion=38;
    if($ejerce==1)
    {
        $titulo_profesional=$fetch_expediente['titulo_profesional'];
        $institucion_educativa=$fetch_expediente['institucion_educativa_nueva'];
        if($subtipo==1)
            $nivel_educativo=1;
        else if($subtipo==2)
            $nivel_educativo=2;
        else if($subtipo==3)
            $nivel_educativo=5;
        else if($subtipo==4)
            $nivel_educativo=6;
        else if($subtipo==5)
            $nivel_educativo=8;
        else if($subtipo==6)
            $nivel_educativo=10;
        else if($subtipo==83)
            $nivel_educativo=11;
        else if($subtipo==84)
            $nivel_educativo=13;
        else if($subtipo==85)
            $nivel_educativo=14;
        else if($subtipo==86)
            $nivel_educativo=15;
        else if($subtipo==87)
            $nivel_educativo=16;
        else if($subtipo==88)
            $nivel_educativo=17;
        else if($subtipo==89)
            $nivel_educativo=18;
         //ACTUALIZACION PERSONAL
        $consulta_personal = "UPDATE nompersonal SET IdNivelEducativo = '{$nivel_educativo}', titulo_profesional = '{$titulo_profesional}', institucion = '{$institucion_educativa}'"
                   . "WHERE cedula='{$cedula}'";
        //echo $consulta2;
        $resultado_personal=query($consulta_personal,$conexion); 
    }
    
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

//MOVIMIENTO PERSONAL - APROBAR
if ($tipo=="9")
{   
    $nivel1=$fetch_expediente['gerencia_nueva'];
    $nivel2=$fetch_expediente['departamento_nuevo'];
    $nivel3=$fetch_expediente['seccion_nueva'];
    $cargo=$fetch_expediente['cod_cargo_nuevo'];
    $funcion=$fetch_expediente['funcion_nueva'];
    $fecha_inicio=$fetch_expediente['fecha_inicio'];
    $fecha_fin=$fetch_expediente['fecha_fin'];
    $fecha_resolucion=$fetch_expediente['fecha_resolucion'];
    $numero_resolucion=$fetch_expediente['numero_resolucion'];
    $tipo_accion=20;
     
     //ACTUALIZACION PERSONAL
    $consulta_personal = "UPDATE nompersonal SET codnivel1 = '{$nivel1}', codnivel2 = '{$nivel2}', codnivel3 = '{$nivel3}', "
                        . "codcargo = '{$cargo}', nomfuncion_id = '{$funcion}', num_resolucion = '{$numero_resolucion}', "
                        . "fecha_resolucion = '{$fecha_resolucion}',inicio_periodo = '{$fecha_inicio}',fin_periodo = '{$fecha_fin}'"
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

//VACACIONES - APROBAR
if ($tipo=="11")
{   
    $cedula=$fetch_expediente['cedula'];
    $fecha_inicio=$fetch_expediente['fecha_inicio'];
    if($fecha_inicio==NULL)
    {
        $fecha_inicio="0000-00-00";
    }
    $fecha_fin=$fetch_expediente['fecha_fin'];
    if($fecha_fin==NULL)
    {
        $fecha_fin="0000-00-00";
    }
    $fecha_inicio_periodo=$fetch_expediente['fecha_inicio_periodo'];
    if($fecha_inicio_periodo==NULL)
    {
        $fecha_inicio_periodo="0000-00-00";
    }
    $fecha_fin_periodo=$fetch_expediente['fecha_fin_periodo'];
    if($fecha_fin_periodo==NULL)
    {
        $fecha_fin_periodo="0000-00-00";
    }
    $fecha_resolucion=$fetch_expediente['fecha_resolucion']; 
    if($fecha_resolucion==NULL)
    {
        $fecha_resolucion="0000-00-00";
    }
    $fecha=$fetch_expediente['fecha']; 
    if($fecha==NULL)
    {
        $fecha="0000-00-00";
    }
    $dias=$fetch_expediente['dias'];    
    $restante=$fetch_expediente['restante'];
    $descripcion=$fetch_expediente['descripcion'];
    $periodo_vacacion=$fetch_expediente['periodo_vacacion'];
    if($periodo_vacacion==NULL)
    {
        $periodo_vacacion=0;
    }
    $numero_resolucion=$fetch_expediente['numero_resolucion']; 
    if($numero_resolucion==NULL)
    {
        $numero_resolucion=0;
    }
    $usuario_creacion=$fetch_expediente['usuario_creacion'];
    $usuario_modificacion=$fetch_expediente['usuario_modificacion'];
    if($usuario_modificacion!="")
        $usuario=$usuario_modificacion;
    else
        $usuario=$usuario_creacion;
    $tipo_accion=19;    
    $tipo_justificacion=7;
    
    $consulta_personal="SELECT personal_id, cedula, apenom, tipnom, nomposicion_id, fecing, useruid, usuario_workflow "
        . "FROM nompersonal WHERE cedula = '{$cedula}'";

    $resultado_persona=sql_ejecutar($consulta_personal);
    $fetch_personal=fetch_array($resultado_persona); 
    $personal_id=$fetch_personal['personal_id'];    
    $apenom=$fetch_personal['apenom'];
    $tipnom=$fetch_personal['tipnom'];
    $nomposicion_id=$fetch_personal['nomposicion_id'];
    $fecing=$fetch_personal['fecing'];
    $usr_uid=$fetch_personal['useruid'];
    $cod_user=$fetch_personal['usuario_workflow'];
    
    $dias_incapacidad=$dias*(-1);
    $tiempo_incapacidad=$dias*(-1);
    $saldo=$restante-$dias;
    //INSERCION DIAS INCAPACIDAD        
    $consulta_incapacidad="INSERT INTO dias_incapacidad
            (id, cod_user, tipo_justificacion, fecha, tiempo, observacion, documento, st, usr_uid, fecha_vence, 
            dias, horas, minutos, idparent, cedula)
            VALUES  
            ('','{$nomposicion_id}','{$tipo_justificacion}','{$fecha}','{$tiempo_incapacidad}','{$descripcion}','','','{$usr_uid}',"
            . "'{$fecha_fin}','{$dias_incapacidad}','','','{$codigo}' ,'{$cedula}')";
    $consulta_incapacidad;    
    $resultado_incapacidad=query($consulta_incapacidad,$conexion);    
    if($periodo_vacacion==0)
    {
        $ultimo_id_incapacidad = mysqli_insert_id($conexion);

         //INSERCIÓN PERIODOS VACACIONES 
        $consulta_vacaciones="INSERT INTO periodos_vacaciones
                    (id_periodo_vacacion, usr_uid, fini_periodo, ffin_periodo, no_resolucion, fecha_resolucion, dias,
                    saldo, fecha_efectivas, fecha_creacion, usuario_creacion, id_dias_incapacidad, vac_desde,
                    vac_hasta, cod_expediente_det, cedula)
                    VALUES  
                    ('','{$user_uid}','{$fecha_inicio_periodo}', '{$fecha_fin_periodo}', '{$numero_resolucion}','{$fecha_resolucion}',"
                    . "'{$dias}', '{$saldo}', '{$fecha_inicio}','{$fecha}','{$usuario}','{$ultimo_id_incapacidad}','{$fecha_inicio}', '{$fecha_fin}',"
                    . "'{$codigo}','{$cedula}')";
        $resultado_vacaciones=query($consulta_vacaciones,$conexion);
    }
    else
    {
        
        $actualizacion_periodo = "UPDATE periodos_vacaciones SET saldo = '{$saldo}'"
               . "WHERE id_periodo_vacacion='{$periodo_vacacion}'";
        //echo $consulta2;
        $resultado_actualizacion=query($actualizacion_periodo,$conexion);  
    }
     //ACTUALIZACION PERSONAL
    if($fecha_inicio!="0000-00-00" && $fecha_fin!="0000-00-00")
    {
        $consulta_personal = "UPDATE nompersonal SET fechavac = '{$fecha_inicio}', fechareivac = '{$fecha_fin}' "
                   . "WHERE cedula='{$cedula}'";
        //echo $consulta2;
        $resultado_personal=query($consulta_personal,$conexion); 
    }
    
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

//LICENCIA - APROBAR
if ($tipo=="15" || $tipo=="16" || $tipo=="17" )
{
    $consulta_subtipo="SELECT * FROM expediente_subtipo WHERE id_expediente_subtipo='$subtipo'";
    $resultado_subtipo=query($consulta_subtipo,$conexion);
    $fetch_subtipo=fetch_array($resultado_subtipo,$conexion);
    $nombre_subtipo = $fetch_subtipo['nombre_subtipo'];
    
    $fecha_aprobado=$fetch_expediente['fecha_aprobado'];
    $fecha_enterado=$fetch_expediente['fecha_enterado'];
    $fecha_inicio=$fetch_expediente['fecha_inicio'];
    $fecha_fin=$fetch_expediente['fecha_fin'];
    $fecha_resolucion=$fetch_expediente['fecha_resolucion'];
    $desde=$fetch_expediente['desde'];
    $hasta=$fetch_expediente['hasta'];
    $meses=$fetch_expediente['meses'];
    $dias=$fetch_expediente['dias'];
    $horas=$fetch_expediente['$horas'];
    $minutos=$fetch_expediente['$minutos'];
    $duracion=$fetch_expediente['duracion'];
    $restante=$fetch_expediente['restante'];
    $numero_resolucion=$fetch_expediente['numero_resolucion'];
    $observacion = "ENVIO DE LICENCIA ". $nombre_subtipo;
    $id_mov_tipo = 2;
    
    
    if($tipo=="15")
    {
        $tipo_funcionario=4;
        $licencia_tipo = 0;
//        $estado = "Licencia con Sueldo";
    }
    if($tipo=="16")
    {
        $tipo_funcionario=5;
        $licencia_tipo = 1;
//        $estado = "Licencia sin sueldo";
    }
    if($tipo=="17")
    {
        $tipo_funcionario=6;
        $licencia_tipo = 2;
//        if($subtipo=="52")
//            $estado = "Licencia por Gravidez";
//        else if($subtipo=="53"||$subtipo=="82")
//            $estado = "Licencia por enfermedad";
//        else if($subtipo=="54")
//            $estado = "Licencia Por Riesgos Profesionales";
    }    
    $estado = $expediente_tipo." ".$expediente_subtipo;
    
    $arrayFecha = explode("-", $fecha, 3);
    $anio = $arrayFecha[0];
    $mes = $arrayFecha[1];
    $dia = $arrayFecha[2];
    
    if($dia>0 && $dia<=15)
        $quincena=1;
    else
        $quincena=2;
    
    //INSERCIÓN LICENCIA   
     $consulta_licencia="INSERT INTO licencias
                (id_licencia, usr_uid, tipo_licencia, nro_resolucion, fecha_resolucion, fecha_desde, fecha_hasta, 
                motivo, cod_expediente_det)
                VALUES  
                ('','{$user_uid}','{$tipo}','{$numero_resolucion}','{$fecha_resolucion}', "
                . "'{$fecha_inicio}','{$fecha_fin}','{$descripcion}','{$codigo}')";
    
    $resultado_licencia=query($consulta_licencia,$conexion);

     //ACTUALIZACION PERSONAL
    $consulta_personal = "UPDATE nompersonal SET tipo_funcionario = '{$tipo_funcionario}',estado = '{$estado}',num_resolucion = '{$numero_resolucion}',"
                        . "fecha_resolucion = '{$fecha_resolucion}',inicio_periodo = '{$fecha_inicio}',fin_periodo = '{$fecha_fin}'"
                        . "WHERE cedula='{$cedula}'";
    $resultado_personal=query($consulta_personal,$conexion); 
    
    //INSERCIÓN MOV CONTRALORIA
     $consulta_contraloria="INSERT INTO mov_contraloria
                (id_mov_contraloria, personal_id, quincena, mes, ano, num_decreto, fecha_decreto, nomposicion_id, cedula, seguro_social, 
                clave_ir, sexo, nombres, apellido_paterno, apellido_materno, apellido_casada, fecing, titular_interino, tipemp, observacion,
                id_mov_tipo, fecha, cod_expediente_det)
                VALUES  
                ('','{$personal_id}','{$quincena}','{$mes}','{$ano}','{$numero_resolucion}','{$fecha_resolucion}','{$nomposicion_id}',"
                . "'{$cedula}','{$seguro_social}','{$clave_ir}','{$sexo}','{$nombres}','{$apellido_paterno}','{$apellido_materno}',"
                . "'{$apellido_casada}','{$fecing}','{$titular_interino}','{$tipemp}','{$observacion}','{$id_mov_tipo}',"
                . "'{$fecha}','{$codigo}')";
    
    $resultado_contraloria=query($consulta_contraloria,$conexion);
    $ultimo_id = mysqli_insert_id($conexion);
    
    //LOG TRANSACCIONES - MOV CONTRALORIA

    $descripcion_transaccion = 'Insertado Movimiento Contraloria: ' . $ultimo_id . ', Código' . $codigo  . ' Tipo: Licencia '. $tipo;

    $consulta_transaccion = "INSERT INTO log_transacciones (cod_log, descripcion, fecha_hora, modulo, url, accion, valor, usuario) 
                VALUES ('', '".$descripcion_transaccion."', now(), 'Expediente-Aprobación', 'expediente_aprobar.php', 'Aprobar','".$codigo."','".$usuario."')";

    $resultado_transaccion=query($consulta_transaccion,$conexion);
    
    //INSERCIÓN MOV LICENCIA
    
     $consulta_licencia="INSERT INTO mov_licencia
                (id_mov_licencia, id_mov_contraloria, licencia_tipo, licencia_meses, licencia_dias, licencia_desde, licencia_hasta, licencia_descripcion)
                VALUES  
                ('','{$ultimo_id}','{$licencia_tipo}','{$meses}','{$dias}','{$fecha_inicio}','{$fecha_fin}','{$nombre_subtipo}')";
    
    $resultado_licencia=query($consulta_licencia,$conexion);
    
    //LOG TRANSACCIONES - MOV LICENCIA

    $descripcion_transaccion = 'Insertado Movimiento Licencia: ' . $ultimo_id . ', Código' . $codigo  . ' Tipo: Licencia '. $tipo;

    $consulta_transaccion = "INSERT INTO log_transacciones (cod_log, descripcion, fecha_hora, modulo, url, accion, valor, usuario) 
                VALUES ('', '".$descripcion_transaccion."', now(), 'Expediente-Aprobación', 'expediente_aprobar.php', 'Aprobar','".$codigo."','".$usuario."')";

    $resultado_transaccion=query($consulta_transaccion,$conexion);
    
    //ACCION FUNCIONARIO
    if($tipo=="15")
    {
        $tipo_accion=7;
    }
    else if($tipo=="16")
    {

        $tipo_accion=8;
    }
    else if($tipo=="17")
    {

        $tipo_accion=9;
    }   
    
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

//BAJA - APROBAR
if ($tipo=="19")
{
    $fecha_baja=$fetch_expediente['fecha_resolucion'];
    $numero_baja=$fetch_expediente['numero_resolucion'];

    $fecha = date('Y-m-d');
    $estado = "Egresado";

     //ACTUALIZACION PERSONAL
    $consulta_personal = "UPDATE nompersonal SET num_decreto_baja = '{$numero_baja}', fecha_decreto_baja = '{$fecha_baja}',estado = '{$estado}' "
               . "WHERE cedula='{$cedula}'";
    //echo $consulta2;
    $resultado_personal=query($consulta_personal,$conexion); 
    
    //ACTUALIZACION POSICION EMPLEADO    
    $consulta_posicionempleado = "DELETE FROM  posicionempleado "
               . "WHERE IdEmpleado='$personal_id'";
    //echo $consulta3;   
    $resultado_posicionempleado=query($consulta_posicionempleado,$conexion);  
    
    //ACTUALIZACION POSICION
    $estado = 'D';
    $consulta_posicion = "UPDATE nomposicion SET estado = '{$estado}'"
               . "WHERE nomposicion_id='{$posicion}'";
    //echo $consulta2;
    $resultado_posicion=query($consulta_posicion,$conexion);  
}

//AJUSTE DE TIEMPO - APROBAR
if ($tipo=="27")
{
    $subtipo=$fetch_expediente['subtipo'];
    
    $consulta_justificacion="SELECT * FROM expediente_subtipo WHERE id_expediente_subtipo='$subtipo'";
    $resultado_justificacion=query($consulta_justificacion,$conexion);
    $fetch_justificacion=fetch_array($resultado_justificacion,$conexion);
    $tipo_justificacion=$fetch_justificacion['tipo_justificacion'];
    
    $fecha=$fetch_expediente['fecha_inicio'];
    $tiempo=$fetch_expediente['duracion'];
    $descripcion=$fetch_expediente['descripcion'];
    $tipo_ajuste=$fetch_expediente['tipo_ajuste'];
    
    $dias=$fetch_expediente['dias'];
    $restantes=$fetch_expediente['restante'];
    $horas=$fetch_expediente['horas'];
    
    if($subtipo==61)
    {
       $tiempo=$dias;
       $periodo_vacacion=$fetch_expediente['periodo_vacacion'];
   
    //ACTUALIZACION PERIODO       
        $consulta_periodo = "SELECT  saldo
                FROM   periodos_vacaciones 
                WHERE  id_periodo_vacacion='{$periodo_vacacion}'";

        $resultado_consulta=sql_ejecutar($consulta_periodo);    
        $fetch_consulta=fetch_array($resultado_consulta);      
        $saldo=$fetch_consulta['saldo'];
        
        if($tipo_ajuste==1)
        {
            $saldo=$saldo+$dias;
        }
        else
        {
            $saldo=$saldo-$dias;
        }
            
        $actualizacion_periodo = "UPDATE periodos_vacaciones SET saldo = '{$saldo}'"
               . "WHERE id_periodo_vacacion='{$periodo_vacacion}'";
        //echo $consulta2;
        $resultado_actualizacion=query($actualizacion_periodo,$conexion);  
    }
    
    if($tipo_ajuste==2)
    {
        $dias=$dias*(-1);
        $tiempo=$tiempo*(-1);
        $horas=$horas*(-1);
    }
    //INSERCION DIAS INCAPACIDAD
    $consulta_incapacidad="INSERT INTO dias_incapacidad
                        (cod_user, tipo_justificacion, fecha, tiempo, observacion, documento, st, usr_uid, dias, horas, idparent, cedula) 
                        VALUES
                        ('{$nomposicion_id}','{$tipo_justificacion}', '{$fecha}','{$tiempo}', '{$descripcion}', NULL, NULL,'{$user_uid}','{$dias}','{$horas}','{$codigo}', '{$cedula}')";
    $resultado=query($consulta_incapacidad,$conexion);
    
}


//MISION OFICIAL - APROBAR
if ($tipo=="28" )
{          
    $tipo_accion=36;
    
//    $estado = "REGULAR";
//    //ACTUALIZACION PERSONAL
//    
//    $consulta_personal = "UPDATE nompersonal SET estado = '{$estado}'"
//               . " WHERE cedula='{$cedula}'";
//    
//    $resultado_personal=query($consulta_personal,$conexion); 
    
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

//CERTIFICACION TRABAJO  - APROBAR
if ($tipo=="29" )
{          
    $tipo_accion=37;
    
//    $estado = "REGULAR";
//    //ACTUALIZACION PERSONAL
//    
//    $consulta_personal = "UPDATE nompersonal SET estado = '{$estado}'"
//               . " WHERE cedula='{$cedula}'";
//    
//    $resultado_personal=query($consulta_personal,$conexion); 
    
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

//ASCENSO - APROBAR
if ($tipo=="30" )
{          
    $tipo_accion=5;
    
//    $estado = "REGULAR";
//    //ACTUALIZACION PERSONAL
//    
//    $consulta_personal = "UPDATE nompersonal SET estado = '{$estado}'"
//               . " WHERE cedula='{$cedula}'";
//    
//    $resultado_personal=query($consulta_personal,$conexion); 
    
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

//AUMENTO (AJUSTE SALARIAL) - APROBAR
if ($tipo=="31" )
{          
    $tipo_accion=6;
    $salario_nuevo=$fetch_expediente['monto_nuevo'];
    
    //ACTUALIZACION PERSONAL
    
    $consulta_personal = "UPDATE nompersonal SET sueldopro = '{$salario_nuevo}', suesal = '{$salario_nuevo}'"
               . " WHERE cedula='{$cedula}'";
    
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

//REVOCATORIA - APROBAR
if ($tipo=="32" )
{          
    $tipo_accion=11;
    
//    $estado = "REGULAR";
//    //ACTUALIZACION PERSONAL
//    
//    $consulta_personal = "UPDATE nompersonal SET estado = '{$estado}'"
//               . " WHERE cedula='{$cedula}'";
//    
//    $resultado_personal=query($consulta_personal,$conexion); 
    
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

//MODIFICACION DECRETO - APROBAR
if ($tipo=="33" )
{          
    $tipo_accion=12;
    
//    $estado = "REGULAR";
//    //ACTUALIZACION PERSONAL
//    
//    $consulta_personal = "UPDATE nompersonal SET estado = '{$estado}'"
//               . " WHERE cedula='{$cedula}'";
//    
//    $resultado_personal=query($consulta_personal,$conexion); 
    
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

//SOBRESUELDO - APROBAR
if ($tipo=="34" )
{       
    
    if($subtipo=="65") //ANTIGUEDAD
    {
        $tipo_accion=13; 
        $sobresueldo = $fetch_expediente['sobresueldo_antiguedad'];
        $cadena = "antiguedad";
    }
    else if($subtipo=="66") //ZONAS APARTADAS
    {

        $tipo_accion=14;
        $sobresueldo = $fetch_expediente['sobresueldo_altoriesgo'];
        $cadena = "zona_apartada";
    }
    else if($subtipo=="67") //JEFATURA
    {

        $tipo_accion=15;
        $sobresueldo = $fetch_expediente['sobresueldo_jefatura'];
        $cadena = "jefaturas";
    }
    else if($subtipo=="68") //ESPECIALIDAD O EXC
    {

        $tipo_accion=16;
         $sobresueldo = $fetch_expediente['sobresueldo_especialidad'];
        $cadena = "especialidad";
    } 
    else if($subtipo=="69") //OTROS
    {

        $tipo_accion=17;
         $sobresueldo = $fetch_expediente['sobresueldo_otros'];
        $cadena = "otros";
    }
    else if($subtipo=="70") //GASTOS REPRESENTACION
    {

        $tipo_accion=18;
         $sobresueldo = $fetch_expediente['sobresueldo_gastos_representacion'];
        $cadena = "gastos_representacion";
    }   
    
     //ACTUALIZACION PERSONAL
    
    $consulta_personal = "UPDATE nompersonal SET $cadena = '{$sobresueldo}'"
               . " WHERE cedula='{$cedula}'";
    
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

//JUBILACION - APROBAR
if ($tipo=="35")
{
    $tipo_accion=21;
    $fecha_retiro=$fetch_expediente['fecha_enterado'];
   
    $estado = "Egresado";
    $motivo_retiro = "JUBILACION";

     //ACTUALIZACION PERSONAL
    $consulta_personal = "UPDATE nompersonal SET fecharetiro = '{$fecha_retiro}', estado = '{$estado}', motivo_retiro = '{$motivo_retiro}' "
               . "WHERE cedula='{$cedula}'";
    //echo $consulta2;
    $resultado_personal=query($consulta_personal,$conexion); 
    
    //ACTUALIZACION POSICION EMPLEADO    
    $consulta_posicionempleado = "DELETE FROM  posicionempleado "
               . "WHERE IdEmpleado='$personal_id'";
    //echo $consulta3;   
    $resultado_posicionempleado=query($consulta_posicionempleado,$conexion);  
    
    //ACTUALIZACION POSICION
    $estado = 'D';
    $consulta_posicion = "UPDATE nomposicion SET estado = '{$estado}'"
               . "WHERE nomposicion_id='{$posicion}'";
    //echo $consulta2;
    $resultado_posicion=query($consulta_posicion,$conexion);
    
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

//PRORROGA CONTINACION - APROBAR
if ($tipo=="36" )
{          
    $tipo_accion=24;
    
//    $estado = "REGULAR";
//    //ACTUALIZACION PERSONAL
//    
//    $consulta_personal = "UPDATE nompersonal SET estado = '{$estado}'"
//               . " WHERE cedula='{$cedula}'";
//    
//    $resultado_personal=query($consulta_personal,$conexion); 
    
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

//INICIO DE LABORES- APROBAR
if ($tipo=="37" )
{          
    $tipo_accion=25;
    
//    $estado = "REGULAR";
//    //ACTUALIZACION PERSONAL
//    
//    $consulta_personal = "UPDATE nompersonal SET estado = '{$estado}'"
//               . " WHERE cedula='{$cedula}'";
//    
//    $resultado_personal=query($consulta_personal,$conexion); 
    
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

//CAMBIO NOMBRE/APELLIDO - APROBAR
if ($tipo=="38" )
{          
    $tipo_accion=26;
    $nombres_anterior=$fetch_expediente['nombres_anterior'];
    $apellido_paterno_anterior=$fetch_expediente['apellido_paterno_anterior'];
    $apellido_materno_anterior=$fetch_expediente['apellido_materno_anterior'];
    $apellido_casada_anterior=$fetch_expediente['apellido_casada_anterior'];
    
    $nombres_nuevo=$fetch_expediente['nombres_nuevo'];
    $apellido_paterno_nuevo=$fetch_expediente['apellido_paterno_nuevo'];
    $apellido_materno_nuevo=$fetch_expediente['apellido_materno_nuevo'];
    $apellido_casada_nuevo=$fetch_expediente['apellido_casada_nuevo'];
    
    if($nombres_nuevo=='')
    {
        $nombres=$nombres_anterior;
    }
    else 
    {
        $nombres=$nombres_nuevo;
    }
    
    if($apellido_paterno_nuevo=='')
    {
        $apellidos=$apellido_paterno_anterior;
    }
    else 
    {
        $apellidos=$apellido_paterno_nuevo;
    }
    
    $apenom = $nombres .' '.$apellidos;
    
    if($apellido_materno_nuevo=='')
    {
        $apellido_materno=$apellido_materno_anterior;
    }
    else 
    {
        $apellido_materno=$apellido_materno_nuevo;
    }
    
    if($apellido_casada_nuevo=='')
    {
        $apellido_casada=$apellido_casada_anterior;
    }
    else 
    {
        $apellido_casada=$apellido_casada_nuevo;
    }
      
    
//    $estado = "REGULAR";
//    //ACTUALIZACION PERSONAL
//    
    $consulta_personal = "UPDATE nompersonal SET apenom = '{$apenom}',nombres = '{$nombres}',apellidos = '{$apellidos}',"
                        . "apellido_materno = '{$apellido_materno}',apellido_casada = '{$apellido_casada}'"
                        . " WHERE cedula='{$cedula}'";
    
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

//DEFUNCION - APROBAR
if ($tipo=="39")
{
    $tipo_accion=30;
    $fecha_retiro=$fetch_expediente['fecha_enterado'];
   
    $estado = "Egresado";
    $motivo_retiro = "DEFUNCION";

     //ACTUALIZACION PERSONAL
    $consulta_personal = "UPDATE nompersonal SET fecharetiro = '{$fecha_retiro}', estado = '{$estado}', motivo_retiro = '{$motivo_retiro}' "
               . "WHERE cedula='{$cedula}'";
    //echo $consulta2;
    $resultado_personal=query($consulta_personal,$conexion); 
    
    //ACTUALIZACION POSICION EMPLEADO    
    $consulta_posicionempleado = "DELETE FROM  posicionempleado "
               . "WHERE IdEmpleado='$personal_id'";
    //echo $consulta3;   
    $resultado_posicionempleado=query($consulta_posicionempleado,$conexion);  
    
    //ACTUALIZACION POSICION
    $estado = 'D';
    $consulta_posicion = "UPDATE nomposicion SET estado = '{$estado}'"
               . "WHERE nomposicion_id='{$posicion}'";
    //echo $consulta2;
    $resultado_posicion=query($consulta_posicion,$conexion);
    
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

//REINCORPORACION - APROBAR
if ($tipo=="40" )
{          
    $consulta_subtipo="SELECT * FROM expediente_subtipo WHERE id_expediente_subtipo='$subtipo'";
    $resultado_subtipo=query($consulta_subtipo,$conexion);
    $fetch_subtipo=fetch_array($resultado_subtipo,$conexion);
    $nombre_subtipo = $fetch_subtipo['nombre_subtipo'];
    
    $fecha=$fetch_expediente['fecha'];
    
    $numero_decreto="S/N";
    $fecha_decreto=NULL;
    $observacion = "RETORNO DE LICENCIA POR: ". $nombre_subtipo;
    $id_mov_tipo = 4;
   
    $arrayFecha = explode("-", $fecha, 3);
    $anio = $arrayFecha[0];
    $mes = $arrayFecha[1];
    $dia = $arrayFecha[2];
    
    if($dia>0 && $dia<=15)
        $quincena=1;
    else
        $quincena=2;
    
    $tipo_accion=31;
    
    $estado = "REGULAR";
    //ACTUALIZACION PERSONAL
    
    $consulta_personal = "UPDATE nompersonal SET estado = '{$estado}'"
               . " WHERE cedula='{$cedula}'";
    
    $resultado_personal=query($consulta_personal,$conexion);
    
    //INSERCIÓN MOV CONTRALORIA
     $consulta_contraloria="INSERT INTO mov_contraloria
                (id_mov_contraloria, personal_id, quincena, mes, ano, num_decreto, fecha_decreto, nomposicion_id, cedula, seguro_social, 
                clave_ir, sexo, nombres, apellido_paterno, apellido_materno, apellido_casada, fecing, titular_interino, tipemp, observacion,
                id_mov_tipo, fecha, cod_expediente_det)
                VALUES  
                ('','{$personal_id}','{$quincena}','{$mes}','{$ano}','{$numero_decreto}','{$fecha_decreto}','{$nomposicion_id}',"
                . "'{$cedula}','{$seguro_social}','{$clave_ir}','{$sexo}','{$nombres}','{$apellido_paterno}','{$apellido_materno}',"
                . "'{$apellido_casada}','{$fecing}','{$titular_interino}','{$tipemp}','{$observacion}','{$id_mov_tipo}',"
                . "'{$fecha}','{$codigo}')";
    
    $resultado_contraloria=query($consulta_contraloria,$conexion);
    $ultimo_id = mysqli_insert_id($conexion);
    
    //LOG TRANSACCIONES - MOV CONTRALORIA

    $descripcion_transaccion = 'INSERTADO MOVIMIENTO DE CONTRALORIA ' . $ultimo_id . ', CODIGO' . $codigo  . ' DESCRIPCION: '. $observacion;

    $consulta_transaccion = "INSERT INTO log_transacciones (cod_log, descripcion, fecha_hora, modulo, url, accion, valor, usuario) 
                VALUES ('', '".$descripcion_transaccion."', now(), 'Expediente-Aprobación', 'expediente_aprobar.php', 'Aprobar','".$codigo."','".$usuario."')";

    $resultado_transaccion=query($consulta_transaccion,$conexion);
    
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

//RECLASIFICACION- APROBAR
if ($tipo=="41" )
{          
    $tipo_accion=33;
    
    $fecha_reclasificacion=$fetch_expediente['fecha'];
   
    $gerencia=$fetch_expediente['gerencia_nueva'];
    $departamento=$fetch_expediente['departamento_nuevo'];
    $seccion=$fetch_expediente['seccion_nueva'];
    $cargo=$fetch_expediente['cod_cargo_nuevo'];
    $funcion=$fetch_expediente['funcion_nueva'];

    $fecha = date('Y-m-d');

     //ACTUALIZACION PERSONAL
    $consulta_personal = "UPDATE nompersonal SET codnivel1 = '{$gerencia}', codnivel2 = '{$departamento}',codnivel3 = '{$seccion}',"
                        . "codcargo = '{$cargo}', nomfuncion_id = '{$funcion}'"
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

//AUMENTO HORAS - APROBAR
if ($tipo=="42" )
{          
    $tipo_accion=34;
    
//    $estado = "REGULAR";
//    //ACTUALIZACION PERSONAL
//    
//    $consulta_personal = "UPDATE nompersonal SET estado = '{$estado}'"
//               . " WHERE cedula='{$cedula}'";
//    
//    $resultado_personal=query($consulta_personal,$conexion); 
    
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

//LIBRE NOMBRAMIENTO / REMOCION - APROBAR
if ($tipo=="43" )
{          
    $tipo_accion=35;
    
//    $estado = "REGULAR";
//    //ACTUALIZACION PERSONAL
//    
//    $consulta_personal = "UPDATE nompersonal SET estado = '{$estado}'"
//               . " WHERE cedula='{$cedula}'";
//    
//    $resultado_personal=query($consulta_personal,$conexion); 
    
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

//CESE DE LABORES - APROBAR
if ($tipo=="52")
{
    $fecha_baja=$fetch_expediente['fecha_resolucion'];
    $numero_baja=$fetch_expediente['numero_resolucion'];

    $fecha = date('Y-m-d');
    $estado = "Egresado";

     //ACTUALIZACION PERSONAL
    $consulta_personal = "UPDATE nompersonal SET num_resolucion_baja = '{$numero_baja}', fecha_resolucion_baja = '{$fecha_baja}',estado = '{$estado}' "
               . "WHERE cedula='{$cedula}'";
    //echo $consulta2;
    $resultado_personal=query($consulta_personal,$conexion); 
    
    //ACTUALIZACION POSICION EMPLEADO    
    $consulta_posicionempleado = "DELETE FROM  posicionempleado "
               . "WHERE IdEmpleado='$personal_id'";
    //echo $consulta3;   
    $resultado_posicionempleado=query($consulta_posicionempleado,$conexion);  
    
    //ACTUALIZACION POSICION
    $estado = 'D';
    $consulta_posicion = "UPDATE nomposicion SET estado = '{$estado}'"
               . "WHERE nomposicion_id='{$posicion}'";
    //echo $consulta2;
    $resultado_posicion=query($consulta_posicion,$conexion);  
}


//ABANDONO DE CARGO - APROBAR
if ($tipo=="54")
{
    $fecha_baja=$fetch_expediente['fecha_resolucion'];
    $numero_baja=$fetch_expediente['numero_resolucion'];

    $fecha = date('Y-m-d');
    $estado = "Egresado";

     //ACTUALIZACION PERSONAL
    $consulta_personal = "UPDATE nompersonal SET num_resolucion_baja = '{$numero_baja}', fecha_resolucion_baja = '{$fecha_baja}',estado = '{$estado}' "
               . "WHERE cedula='{$cedula}'";
    //echo $consulta2;
    $resultado_personal=query($consulta_personal,$conexion); 
    
    //ACTUALIZACION POSICION EMPLEADO    
    $consulta_posicionempleado = "DELETE FROM  posicionempleado "
               . "WHERE IdEmpleado='$personal_id'";
    //echo $consulta3;   
    $resultado_posicionempleado=query($consulta_posicionempleado,$conexion);  
    
    //ACTUALIZACION POSICION
    $estado = 'D';
    $consulta_posicion = "UPDATE nomposicion SET estado = '{$estado}'"
               . "WHERE nomposicion_id='{$posicion}'";
    //echo $consulta2;
    $resultado_posicion=query($consulta_posicion,$conexion);  
}

//AUMENTESE, ASCIENDASE Y TRASLADESE - APROBAR
if ($tipo=="56")
{
    $tipo_accion=40;
   
    $fecha_decreto=$fetch_expediente['fecha_decreto'];
    $numero_decreto=$fetch_expediente['numero_decreto'];
    $gerencia=$fetch_expediente['gerencia_nueva'];
    $departamento=$fetch_expediente['departamento_nuevo'];
    $seccion=$fetch_expediente['seccion_nueva'];
    $posicion=$fetch_expediente['posicion_nueva'];
    $cargo=$fetch_expediente['cod_cargo_nuevo'];
    $funcion=$fetch_expediente['funcion_nueva'];
    $planilla=$fetch_expediente['planilla_nueva'];
    
    
    $consulta_nomposicion = "SELECT sueldo_propuesto,gastos_representacion,sobresueldo_otros_1,dieta,combustible FROM nomposicion "
                        . "WHERE nomposicion_id = '$posicion'";
    $resultado_nomposicion=query($consulta_nomposicion,$conexion);
    $fetch_nomposicion=fetch_array($resultado_nomposicion,$conexion);
    $salario = $fetch_nomposicion["sueldo_propuesto"];
    $gastos_representacion = $fetch_nomposicion["gastos_representacion"];
    $sobresueldo = $fetch_nomposicion["sobresueldo_otros_1"];
    $dieta = $fetch_nomposicion["dieta"];
    $combustible = $fetch_nomposicion["combustible"];

    $fecha = date('Y-m-d');
    

     //ACTUALIZACION PERSONAL
    $consulta_personal = "UPDATE nompersonal SET fecha_decreto = '{$fecha_decreto}', num_decreto = '{$numero_decreto}',"
                        . "codnivel1 = '{$gerencia}', codnivel2 = '{$departamento}',codnivel3 = '{$seccion}',nomposicion_id = '{$posicion}',"
                        . "codcargo = '{$cargo}', nomfuncion_id = '{$funcion}',tipnom = '{$planilla}',sueldopro = '{$salario}',suesal = '{$salario}',"
                        . "gastos_representacion = '{$gastos_representacion}',otros = '{$sobresueldo}',dieta = '{$dieta}',combustible = '{$combustible}'"
                        . "WHERE cedula='{$cedula}'";    
    $resultado_personal=query($consulta_personal,$conexion); 
    
    //ACTUALIZACION POSICION EMPLEADO    
//    $consulta_posicionempleado = "DELETE FROM  posicionempleado "
//               . "WHERE IdEmpleado='$personal_id'";
//    //echo $consulta3;   
//    $resultado_posicionempleado=query($consulta_posicionempleado,$conexion);  
    
    //ACTUALIZACION POSICION
    $estado = 1;
    $consulta_posicion = "UPDATE nomposicion SET estado = '{$estado}'"
               . "WHERE nomposicion_id='{$posicion}'";
    //echo $consulta2;
    $resultado_posicion=query($consulta_posicion,$conexion);  
    
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

//BAJA (OFICIAL) - APROBAR
if ($tipo=="57")
{
    $tipo_accion=41;
    $fecha_baja=$fetch_expediente['fecha_resolucion'];
    $numero_baja=$fetch_expediente['numero_resolucion'];

    $fecha = date('Y-m-d');
    $estado = $expediente_tipo. " ". $expediente_subtipo;

     //ACTUALIZACION PERSONAL
    $consulta_personal = "UPDATE nompersonal SET num_resolucion_baja = '{$numero_baja}', fecha_resolucion_baja = '{$fecha_baja}',estado = '{$estado}'"
               . "WHERE cedula='{$cedula}'";
    //echo $consulta2;
    $resultado_personal=query($consulta_personal,$conexion); 
    
    //ACTUALIZACION POSICION EMPLEADO    
    $consulta_posicionempleado = "DELETE FROM  posicionempleado "
               . "WHERE IdEmpleado='$personal_id'";
    //echo $consulta3;   
    $resultado_posicionempleado=query($consulta_posicionempleado,$conexion);  
    
    //ACTUALIZACION POSICION
    $estado = 0;
    $consulta_posicion = "UPDATE nomposicion SET estado = '{$estado}'"
               . "WHERE nomposicion_id='{$posicion}'";
    //echo $consulta2;
    $resultado_posicion=query($consulta_posicion,$conexion);  
    
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

//REINTEGRO - APROBAR
if ($tipo=="58")
{
    $tipo_accion=42;
    $fecha_reintegro=$fetch_expediente['fecha'];
    $fecha_inicio=$fetch_expediente['fecha_inicio'];
    $fecha_fin=$fetch_expediente['fecha_fin'];
    $fecha_decreto=$fetch_expediente['fecha_decreto_ingreso_nuevo'];
    $numero_decreto=$fetch_expediente['numero_decreto'];
    $gerencia=$fetch_expediente['gerencia_nueva'];
    $departamento=$fetch_expediente['departamento_nuevo'];
    $seccion=$fetch_expediente['seccion_nueva'];
    $posicion=$fetch_expediente['posicion_nueva'];
    $cargo=$fetch_expediente['cod_cargo_nuevo'];
    $funcion=$fetch_expediente['funcion_nueva'];
    $planilla=$fetch_expediente['planilla_nueva'];
    $estatus=$fetch_expediente['situacion_nueva'];
    
    $consulta_nomposicion = "SELECT sueldo_propuesto,gastos_representacion,sobresueldo_otros_1,dieta,combustible FROM nomposicion "
                        . "WHERE nomposicion_id = '$posicion'";
    $resultado_nomposicion=query($consulta_nomposicion,$conexion);
    $fetch_nomposicion=fetch_array($resultado_nomposicion,$conexion);
    $salario = $fetch_nomposicion["sueldo_propuesto"];
    $gastos_representacion = $fetch_nomposicion["gastos_representacion"];
    $sobresueldo = $fetch_nomposicion["sobresueldo_otros_1"];
    $dieta = $fetch_nomposicion["dieta"];
    $combustible = $fetch_nomposicion["combustible"];

    $fecha = date('Y-m-d');
    if($estatus==1)
        $estado = "REGULAR";
    if($estatus==40)
        $estado = "RESERVADO";
    if($estatus==42)
        $estado = "INTERINO";

     //ACTUALIZACION PERSONAL
    $consulta_personal = "UPDATE nompersonal SET fecha_decreto = '{$fecha_decreto}', num_decreto = '{$numero_decreto}',estado = '{$estado}',"
                        . "codnivel1 = '{$gerencia}', codnivel2 = '{$departamento}',codnivel3 = '{$seccion}',nomposicion_id = '{$posicion}',"
                        . "codcargo = '{$cargo}', nomfuncion_id = '{$funcion}',tipnom = '{$planilla}',sueldopro = '{$salario}',suesal = '{$salario}',"
                        . "gastos_representacion = '{$gastos_representacion}',otros = '{$sobresueldo}',dieta = '{$dieta}',combustible = '{$combustible}',"
                        . "inicio_periodo = '{$fecha_inicio}',fin_periodo = '{$fecha_fin}'"
                        . "WHERE cedula='{$cedula}'";    
    $resultado_personal=query($consulta_personal,$conexion); 
    
    //ACTUALIZACION POSICION EMPLEADO    
//    $consulta_posicionempleado = "DELETE FROM  posicionempleado "
//               . "WHERE IdEmpleado='$personal_id'";
//    //echo $consulta3;   
//    $resultado_posicionempleado=query($consulta_posicionempleado,$conexion);  
    
    //ACTUALIZACION POSICION
    $estado = 1;
    $consulta_posicion = "UPDATE nomposicion SET estado = '{$estado}'"
               . "WHERE nomposicion_id='{$posicion}'";
    //echo $consulta2;
    $resultado_posicion=query($consulta_posicion,$conexion);  
    
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

//EXPEDIENTE - ACTUALIZAR
if($resultado || $resultado_accion_tipo || $resultado_posicion)
{
    $estatus = 1;
     $consulta = "UPDATE expediente SET "
                    . "estatus = '{$estatus}',numero_accion = '{$correlativo}',fecha_aprobacion = '{$fecha_actual}',usuario_aprobacion = '{$usuario}'"  
                    . " WHERE cod_expediente_det='{$codigo}'";

    $resultado=query($consulta,$conexion); 
    
    //LOG TRANSACCIONES - APROBACION EXPEDIENTE

    $descripcion_transaccion = 'Aprobado Expediente: ' . $descripcion . ', Código' . $codigo  . ' Tipo: '. $tipo;

    $consulta_transaccion = "INSERT INTO log_transacciones (cod_log, descripcion, fecha_hora, modulo, url, accion, valor, usuario) 
                VALUES ('', '".$descripcion_transaccion."', now(), 'Expediente-Aprobación', 'expediente_aprobar.php', 'Aprobar','".$codigo."','".$usuario."')";

    $resultado_transaccion=query($consulta_transaccion,$conexion);
    if($resultado_transaccion)
    {
        ?>

        <script type="text/javascript">

                     alert("Movimiento Aprobado con Éxito");

        </script>
        <?
        
        activar_pagina("expediente_list.php?cedula=$cedula");
        
    }    
    
}
?>




