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
$host_ip = $_SERVER['REMOTE_ADDR'];

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
$numpre_tmp=$fetch_expediente['numero_decreto'];

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

$consulta_personal="SELECT * FROM nompersonal WHERE cedula='$cedula'";
$resultado_personal=query($consulta_personal,$conexion);
$fetch_personal=fetch_array($resultado_personal,$conexion);
$personal_id=$fetch_personal['personal_id'];
$ficha=$fetch_personal['ficha'];
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
$tipnom=$fetch_personal['tipnom'];
$proyecto=$fetch_personal['proyecto'];
$codnivel1=$fetch_personal['codnivel1'];
$suesal=$fetch_personal['suesal'];

$usuario = $_SESSION['usuario'];

$consulta_posicion="SELECT Posicion FROM posicionempleado WHERE IdEmpleado='$personal_id'";
$resultado_posicion=query($consulta_posicion,$conexion);
$fetch_posicion=fetch_array($resultado_posicion,$conexion);
$posicion=$fetch_posicion['Posicion'];

$consulta_adjunto="SELECT * FROM expediente_adjunto WHERE cod_expediente_det='$codigo'";
$resultado_adjunto=query($consulta_adjunto,$conexion);
$fetch_adjunto=fetch_array($resultado_adjunto,$conexion);
$filas_adjunto  = count($fetch_adjunto);

//Funcion para validar si existen incidencias
function validar_incidencia( $ficha,$fecha,$inc,$conexion )
{
    $sql = "SELECT * FROM caa_incidencias_empleados WHERE ficha = '{$ficha}' AND fecha = '{$fecha}' AND id_incidencia = '{$inc}'";
    $res = query($sql,$conexion);
    if ( mysqli_num_rows($res) > 0 ) {
        return true;
    }else{
        return false;
    }
}

//ESTUDIOS ACADEMICOS  - APROBAR
if ($tipo=="1")
{   
    $ejerce=$fetch_expediente['ejerce'];
     $tipo_accion=38;
    
    $titulo_profesional=$fetch_expediente['titulo_profesional'];    
    $institucion_educativa=$fetch_expediente['institucion_educativa_nueva'];
    
    $sql_titulo = "SELECT  * FROM nomprofesiones WHERE codorg='{$titulo_profesional}'";
    $resultado_titulo=sql_ejecutar($sql_titulo);
    $fetch_titulo=fetch_array($resultado_titulo,$conexion);
    $titulo=$fetch_titulo['descrip'];
    
    $sql_institucion = "SELECT  * FROM institucion_educativa WHERE id_institucion='{$institucion_educativa}'";
    $resultado_institucion=sql_ejecutar($sql_institucion);
    $fetch_institucion=fetch_array($resultado_institucion,$conexion);
    $institucion=$fetch_institucion['nombre'];
    
    $comentario = utf8_decode($titulo)." - EN: ".utf8_decode($institucion)." - ".utf8_decode($fetch_expediente['descripcion']);
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
//        $consulta_personal = "UPDATE nompersonal SET IdNivelEducativo = '{$nivel_educativo}', titulo_profesional = '{$titulo_profesional}', institucion = '{$institucion_educativa}'"
//                   . "WHERE cedula='{$cedula}'";
//        //echo $consulta2;
//        $resultado_personal=query($consulta_personal,$conexion); 
     
     $consulta_estudios="INSERT INTO empleado_estudios
                        (IdEmpleado,
                        Id,
                        IdNivelEducativo,
                        Institucion,                        
                        fecha_ini,
                        fecha_fin, 
                        comentario,                       
                        cedula, 
                        cod_expediente_det )
                        VALUES  
                        ('{$id_empleado}',
                        '',"
                        . "'{$nivel_educativo}',"
                        . "'{$institucion}',"
                        . "'{$fetch_expediente['fecha_inicio']}',"
                        . "'{$fetch_expediente['fecha_fin']}', "
                        . "'".$comentario."',"
                        . "'{$cedula}',"
                        . "'{$codigo}')";
     $resultado_estudios=query($consulta_estudios,$conexion);
    
    
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

//CAPACITACION  - APROBAR
if ($tipo=="2")
{   
     $tipo_accion=39;
     $comentario = $expediente_subtipo. " - EN: ".utf8_decode($fetch_expediente['institucion']). " - DICTADA(O) POR: ".utf8_decode($fetch_expediente['nombre_especialista'])." - ".utf8_decode($fetch_expediente['descripcion']);
     $consulta_capacitacion="INSERT INTO empleado_capacitacion
                        (Id,
                        IdEmpleado, 
                        fecha_ini,
                        fecha_fin, 
                        comentario,
                        fecha_creacion, 
                        usuario_creacion,
                        cedula, 
                        cod_expediente_det )
                        VALUES  
                        ('',
                        '{$id_empleado}',"
                        . "'{$fetch_expediente['fecha_inicio']}',"
                        . "'{$fetch_expediente['fecha_fin']}', "
                        . "'".$comentario."',"
                        . "'{$fetch_expediente['fecha']}',"
                        . "'{$usuario}',"
                        . "'{$cedula}',"
                        . "'{$codigo}')";
            $resultado_capacitacion=query($consulta_capacitacion,$conexion);
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

//PERMISOS  - APROBAR
if ($tipo=="4")
{
    $fecha=$fetch_expediente['fecha'];
//    $fecha_aprobado=$fetch_expediente['fecha_aprobado'];
    $fecha_inicio=$fetch_expediente['fecha_inicio'];
    $fecha_fin=$fetch_expediente['fecha_fin'];
    $descripcion=$fetch_expediente['descripcion'];
    $subtipo=$fetch_expediente['subtipo'];
    $subtipo;
    if($subtipo==18 || $subtipo==19 || $subtipo==20)   
    {    
        $dias=$fetch_expediente['dias'];    
        $restantes=$fetch_expediente['restante'];
        $horas=$fetch_expediente['horas'];
        $minutos=$fetch_expediente['minutos'];
        $duracion=$fetch_expediente['duracion'];
        $tipo_justificacion=2; 
    }
    if($subtipo==21|| $subtipo==17)
    {
        $dias=(-1)*($fetch_expediente['dias']);    
        $restantes=$fetch_expediente['restante'];
        $horas=(-1)*($fetch_expediente['horas']);
        $minutos=(-1)*($fetch_expediente['minutos']);
        $duracion=(-1)*($fetch_expediente['duracion']);
        $tipo_justificacion=5; 
    }
    if($subtipo==22)   
    {    
        $dias=$fetch_expediente['dias'];    
        $restantes=$fetch_expediente['restante'];
        $horas=$fetch_expediente['horas'];
        $minutos=$fetch_expediente['minutos'];
        $duracion=$fetch_expediente['duracion'];
        $tipo_justificacion=9;
    }     
        if($subtipo==23)   
    {    
        $dias=$fetch_expediente['dias'];    
        $restantes=$fetch_expediente['restante'];
        $horas=$fetch_expediente['horas'];
        $minutos=$fetch_expediente['minutos'];
        $duracion=$fetch_expediente['duracion'];
        $tipo_justificacion=3;
    } 
    //INSERCION DIAS INCAPACIDAD
    $consulta_incapacidad="INSERT INTO dias_incapacidad
                        (cod_user, tipo_justificacion, fecha, tiempo, observacion, documento, st, usr_uid, dias, horas, 
                        minutos, idparent, cedula) 
                        VALUES
                        ('{$ficha}','{$tipo_justificacion}', '{$fecha}','{$duracion}','{$descripcion}', NULL, NULL,"
                        . "'','{$dias}','{$horas}','{$minutos}','{$codigo}','{$cedula}')";
 
     $resultado=query($consulta_incapacidad,$conexion);
 
    //echo "<br>"; echo "CONSULTA INCAPACIDAD: "; echo $consulta_incapacidad;
   
}

//AMONESTACION - APROBAR
if ($tipo=="5")
{   
       
//    $estado="Suspendido";
//    //ACTUALIZACION PERSONAL
//   $consulta_personal = "UPDATE nompersonal SET "
//           . " fechasus = '{$fetch_expediente['fecha_inicio']}', "
//           . " fechareisus = '{$fetch_expediente['fecha_fin']}', "
//           . " estado = '{$estado}'"
//              . "WHERE cedula='{$cedula}'";
//   //echo $consulta2;
//   $resultado_personal=query($consulta_personal,$conexion); 

    //ACCION FUNCIONARIO  
    $tipo_accion=27;
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
    
     //INSERCIÓN AMONESTACION
            $ultimo_id = mysqli_insert_id($conexion);
            if ($fetch_expediente['subtipo']==24)
                $tipo_amonestacion="VERBAL";
            else
                $tipo_amonestacion="ESCRITA";
             $consulta_amonestacion="INSERT INTO amonestaciones
                        (usr_uid, 
                        fecha, 
                        numeral_numero, 
                        numeral_descripcion, 
                        articulo, 
                        motivo, 
                        tipo, 
                        cedula,
                        cod_expediente_det)
                        VALUES  
                        ('{$user_uid}',"
                        . "'{$fetch_expediente['fecha']}', "
                        . "'{$fetch_expediente['numeral']}',"
                        . " '{$fetch_expediente['numeral_descripcion']}', "
                        . "'{$fetch_expediente['articulo']}',"
                        . " '{$fetch_expediente['descripcion']}',"
                        . " '{$tipo_amonestacion}',"
                        . " '{$cedula}',"
                        . "'{$codigo}')";
            $resultado_amonestacion=query($consulta_amonestacion,$conexion);
}


//SUSPENSION - APROBAR
if ($tipo=="6")
{   
       
    $estado="Suspendido";
    
    $consulta_suspension="INSERT INTO suspenciones
                        (usr_uid, 
                        fecha_resolucion, 
                        fecha_desde, 
                        fecha_hasta, 
                        dias, 
                        numeral_numero, 
                        numeral_descripcion,
                        motivo, 
                        nro_resolucion, 
                        articulo, 
                        cod_expediente_det, 
                        cedula)
                        VALUES  
                        ('{$user_uid}',"
                        . "'{$fetch_expediente['fecha_resolucion']}',"
                        . "'{$fetch_expediente['fecha_inicio']}', "
                        . "'{$fetch_expediente['fecha_fin']}',"
                        . "'{$fetch_expediente['dias']}',"
                        . "'{$fetch_expediente['numeral']}',"
                        . "'{$fetch_expediente['numeral_descripcion']}',"
                        . "'{$fetch_expediente['descripcion']}',"
                        . "'{$fetch_expediente['numero_resolucion']}',"
                        . "'{$fetch_expediente['articulo']}',"
                        . "'{$codigo}',"
                        . "'{$cedula}')";
            $resultado_suspension=query($consulta_suspension,$conexion);

    //ACCION FUNCIONARIO  
    $tipo_accion=28;
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
    
   if($fecha_actual==$fetch_expediente['fecha_inicio'])
   {
        //ACTUALIZACION PERSONAL
       $consulta_personal = "UPDATE nompersonal SET "
               . " fechasus = '{$fetch_expediente['fecha_inicio']}', "
               . " fechareisus = '{$fetch_expediente['fecha_fin']}', "
               . " estado = '{$estado}'"
                  . "WHERE cedula='{$cedula}'";
       //echo $consulta2;
       $resultado_personal=query($consulta_personal,$conexion);     
        
        $consulta_campo_adicional1="SELECT valor FROM nomcampos_adic_personal WHERE ficha='$personal_id' AND id='7'";
        $resultado_campo_adicional1=query($consulta_campo_adicional1,$conexion);
        $fetch_campo_adicional1=fetch_array($resultado_campo_adicional1,$conexion);
        $valor_campo_adicional1 = $fetch_campo_adicional1['valor'];

        $filas_campo_adicional1= mysqli_num_rows($resultado_campo_adicional1);

    //    echo $filas_campo_adicional1; echo " ";
    //    echo $valor_campo_adicional1; echo " ";

        $consulta_campo_adicional2="SELECT valor FROM nomcampos_adic_personal WHERE ficha='$personal_id' AND id='8'";
        $resultado_campo_adicional2=query($consulta_campo_adicional2,$conexion);
        $fetch_campo_adicional2=fetch_array($resultado_campo_adicional2,$conexion);
        $valor_campo_adicional2 = $fetch_campo_adicional2['valor'];

        $filas_campo_adicional2= mysqli_num_rows($resultado_campo_adicional2);

        $campo_adicional1="NO";
        $campo_adicional2=$estado. " DESDE: ".$fecha_inicio." HASTA: ".$fecha_fin. " FECHA RESOLUCION: ".$fecha_resolucion." NUMERO RESOLUCION: ".$numero_resolucion;

        if($filas_campo_adicional1>0)
        {
            $consulta_adicional1 = "UPDATE nomcampos_adic_personal SET valor = '{$campo_adicional1}'"
                            . "WHERE  ficha='$personal_id' AND id='7'";

        }
        else
        {
            $consulta_adicional1="INSERT INTO nomcampos_adic_personal
                    (ficha, id, valor, mascara, tipo, codorgd, ee, tiponom)
                    VALUES  
                    ('{$personal_id}','7','{$campo_adicional1}',NULL, "
                    . "'A',NULL,NULL,'{$tipnom}')";
        }
        $resultado_adicional1=query($consulta_adicional1,$conexion); 

        if($filas_campo_adicional2>0)
        {
            $consulta_adicional2 = "UPDATE nomcampos_adic_personal SET valor = '{$campo_adicional2}'"
                            . "WHERE  ficha='$personal_id' AND id='8'";

        }
        else
        {
            $consulta_adicional2="INSERT INTO nomcampos_adic_personal
                    (ficha, id, valor, mascara, tipo, codorgd, ee, tiponom)
                    VALUES  
                    ('{$personal_id}','8','{$campo_adicional2}',NULL, "
                    . "'A',NULL,NULL,'{$tipnom}')";
        }
        $resultado_adicional2=query($consulta_adicional2,$conexion); 
   }
}

//MOVIMIENTO PERSONAL - APROBAR
if ($tipo=="9")
{   
    if($fetch_expediente['gerencia_nueva']==NULL || $fetch_expediente['gerencia_nueva']=='' || $fetch_expediente['gerencia_nueva']==0)
    {
        $nivel1=$fetch_expediente['gerencia_anterior'];
        
    }
    else
    {
        $nivel1=$fetch_expediente['gerencia_nueva'];
    }
    
    if($fetch_expediente['departamento_nuevo']==NULL || $fetch_expediente['departamento_nuevo']=='' || $fetch_expediente['departamento_nuevo']==0)
    {
        $nivel2=$fetch_expediente['departamento_anterior'];
    }
    else
    {
        $nivel2=$fetch_expediente['departamento_nuevo'];
    }
    
    if($fetch_expediente['seccion_nueva']==NULL || $fetch_expediente['seccion_nueva']=='' || $fetch_expediente['seccion_nueva']==0)
    {
        $nivel3=$fetch_expediente['seccion_anterior'];
    }
    else
    {
        $nivel3=$fetch_expediente['seccion_nueva'];
    }
    
    if($fetch_expediente['cod_cargo_nuevo']==NULL || $fetch_expediente['cod_cargo_nuevo']=='' || $fetch_expediente['cod_cargo_nuevo']==0)
    {
        $cargo=$fetch_expediente['cod_cargo_anterior'];
    }
    else
    {
        $cargo=$fetch_expediente['cod_cargo_nuevo'];
    }
    
    if($fetch_expediente['funcion_nueva']==NULL || $fetch_expediente['funcion_nueva']=='' || $fetch_expediente['funcion_nueva']==0)
    {
        $funcion=$fetch_expediente['funcion_anterior'];
    }
    else
    {
        $funcion=$fetch_expediente['funcion_nueva'];
    }
    
    if($fetch_expediente['fecha_inicio']==NULL || $fetch_expediente['fecha_inicio']=='' || $fetch_expediente['fecha_inicio']==0 || $fetch_expediente['fecha_inicio']=="0000-00-00")
    {
        $fecha_inicio=$fetch_expediente['fecha_inicio_anterior'];
    }
    else
    {
        $fecha_inicio=$fetch_expediente['fecha_inicio'];
    }
    
    if($fetch_expediente['fecha_fin']==NULL || $fetch_expediente['fecha_fin']=='' || $fetch_expediente['fecha_fin']==0 || $fetch_expediente['fecha_fin']=="0000-00-00")
    {
        $fecha_fin=$fetch_expediente['fecha_fin_anterior'];
    }
    else
    {
        $fecha_fin=$fetch_expediente['fecha_fin'];
    }
    
    if($fetch_expediente['fecha_resolucion']==NULL || $fetch_expediente['fecha_resolucion']=='' || $fetch_expediente['fecha_resolucion']==0 || $fetch_expediente['fecha_resolucion']=="0000-00-00")
    {
        $fecha_resolucion=$fetch_expediente['fecha_resolucion_anterior'];
    }
    else
    {
        $fecha_resolucion=$fetch_expediente['fecha_resolucion'];
    }
    
    if($fetch_expediente['numero_resolucion']==NULL || $fetch_expediente['numero_resolucion']=='' || $fetch_expediente['numero_resolucion']==0)
    {
        $numero_resolucion=$fetch_expediente['numero_resolucion_anterior'];
    }
    else
    {
        $numero_resolucion=$fetch_expediente['numero_resolucion'];
    }
    
     if(($fetch_expediente['posicion_nueva']==NULL || $fetch_expediente['posicion_nueva']=='' || $fetch_expediente['posicion_nueva']==0) 
       && ($fetch_expediente['posicion_anterior']==NULL || $fetch_expediente['posicion_anterior']=='' || $fetch_expediente['posicion_anterior']==0) )
    {
         $posicion=$nomposicion_id;
    }
    else
    {
        if($fetch_expediente['posicion_nueva']==NULL || $fetch_expediente['posicion_nueva']=='' || $fetch_expediente['posicion_nueva']==0)
        {
            $posicion=$fetch_expediente['posicion_anterior'];
        }
        else
        {
            $posicion=$fetch_expediente['posicion_nueva'];
        }
    }
    
    $tipo_accion=20;
     
     //ACTUALIZACION PERSONAL
    $consulta_personal = "UPDATE nompersonal SET codnivel1 = '{$nivel1}', codnivel2 = '{$nivel2}', codnivel3 = '{$nivel3}', "
                        . "codcargo = '{$cargo}', nomfuncion_id = '{$funcion}', nomposicion_id='{$posicion}'"
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

//EVALUACION DE DESEMPEÑO - APROBAR
if ($tipo=="10")
{   
       
    
    //ACCION FUNCIONARIO  
    $tipo_accion=52;
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
    
    $consulta_evaluacion="INSERT INTO empleado_evaluacion
                        (ID, 
                        IdEmpleado, 
                        fini_periodo, 
                        ffin_periodo, 
                        f_eval, 
                        persona_evalua, 
                        puntaje,
                        comentarios, 
                        fecha_creacion, 
                        usuario_creacion, 
                        cod_expediente_det, 
                        cedula)
                        VALUES  
                        ('',"
                        . "'{$personal_id}',"
                        . "'{$fetch_expediente['fecha_inicio_periodo']}', "
                        . "'{$fetch_expediente['fecha_fin_periodo']}',"
                        . "'{$fetch_expediente['fecha']}',"
                        . "'{$fetch_expediente['funcionario_evalua']}',"
                        . "'{$fetch_expediente['puntaje']}',"
                        . "'{$fetch_expediente['descripcion']}',"
                        . "'{$fetch_expediente['fecha_modificacion']}',"
                        . "'{$fetch_expediente['usuario_modificacion']}',"
                        . "'{$codigo}',"
                        . "'{$cedula}')";
            $resultado_evaluacion=query($consulta_evaluacion,$conexion);
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
    $subtipo=$fetch_expediente['subtipo'];
    if($periodo_vacacion==NULL)
    {
        $periodo_vacacion=0;
    }
    $numero_resolucion=$fetch_expediente['numero_resolucion']; 
    if($numero_resolucion==0 || $fetch_expediente['resuelto']==0)
    {
        
        $resuelto=0;
    }
    if($numero_resolucion!=0 || $fetch_expediente['resuelto']==1)
    {
        $resuelto=1;
    }
    
    if($periodo_vacacion!=NULL)
    {
        $sql_periodo = "SELECT  resueltas
                FROM   periodos_vacaciones 
                WHERE  id='{$periodo_vacacion}'";
        $resultado_periodo=sql_ejecutar($sql_periodo);
        $fetch_periodo=fetch_array($resultado_periodo); 
        $resueltas=$fetch_periodo['resueltas']; 
        if($resueltas==1)
        {
            $resuelto=1;
        }        
        
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
    
    if($subtipo==110 || $subtipo==111)
    {
        $resuelto=1;
    }
    
    if($subtipo==110 || $subtipo==111 || $subtipo==115)
    {
        $saldo=$restante-$dias;
        $dias_incapacidad=$dias*(-1);
        $tiempo_incapacidad=$dias*(-1);        
    }
    if($subtipo==114)
    {
        $saldo=$restante+$dias;    
        $dias_incapacidad=$dias;
        $tiempo_incapacidad=$dias;
    }
    if($subtipo==112 || $subtipo==113)
    {
        $saldo=$restante;    
        $dias_incapacidad=$dias;
        $tiempo_incapacidad=$dias;
    }
    
    //INSERCION DIAS INCAPACIDAD        
    $consulta_incapacidad="INSERT INTO dias_incapacidad
            (id, cod_user, tipo_justificacion, fecha, tiempo, observacion, documento, st, usr_uid, fecha_vence, 
            dias, horas, minutos, idparent, cedula)
            VALUES  
            ('','{$nomposicion_id}','{$tipo_justificacion}','{$fecha}','{$tiempo_incapacidad}','{$descripcion}','','','{$usr_uid}',"
            . "'{$fecha_fin}','{$dias_incapacidad}','','','{$codigo}' ,'{$cedula}')";
    //echo $consulta_incapacidad;    
    $resultado_incapacidad=query($consulta_incapacidad,$conexion);    
    if($periodo_vacacion==0)
    {
        $ultimo_id_incapacidad = mysqli_insert_id($conexion);

         //INSERCIÓN PERIODOS VACACIONES 
        $consulta_vacaciones="INSERT INTO periodos_vacaciones
                    (id, usr_uid, fini_periodo, ffin_periodo, no_resolucion, fecha_resolucion, dias,
                    saldo, fecha_efectivas, fecha_creacion, usuario_creacion, id_dias_incapacidad, vac_desde,
                    vac_hasta, resueltas, cod_expediente_det, cedula)
                    VALUES  
                    ('','{$user_uid}','{$fecha_inicio_periodo}', '{$fecha_fin_periodo}', '{$numero_resolucion}','{$fecha_resolucion}',"
                    . "'{$dias}', '{$saldo}', '{$fecha_inicio}','{$fecha}','{$usuario}','{$ultimo_id_incapacidad}','{$fecha_inicio}', '{$fecha_fin}',"
                    . "'{$resuelto}','{$codigo}','{$cedula}')";
        //echo $consulta_vacaciones;
        $resultado_vacaciones=query($consulta_vacaciones,$conexion);
    }
    else
    {
        
        $actualizacion_periodo = "UPDATE periodos_vacaciones SET saldo = '{$saldo}', resueltas = '{$resuelto}' "
               . "WHERE id='{$periodo_vacacion}'";
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

//TIEMPO COMPENSATORIO  - APROBAR
if ($tipo=="12")
{
    $fecha=$fetch_expediente['fecha'];
    $fecha_aprobado=$fetch_expediente['fecha_aprobado'];
    $fecha_inicio=$fetch_expediente['fecha_inicio'];
    $fecha_fin=$fetch_expediente['fecha_fin'];
    $descripcion=$fetch_expediente['descripcion'];
    $tipo_ajuste=$fetch_expediente['tipo_ajuste'];
    
    $dias=$fetch_expediente['dias'];    
    $restantes=$fetch_expediente['restante'];
    $horas=$fetch_expediente['horas'];
    $minutos=$fetch_expediente['minutos'];
    $duracion=$fetch_expediente['duracion'];
       
    $tipo_justificacion=3; 
    
    //echo "<br>"; echo "DIAS : "; echo $dias; echo " - HORAS: "; echo $horas; echo " - MINUTOS: "; echo $minutos; 
    
    //LLEVAMOS LAS HORAS A DIAS / HORAS SEPARADO
    $consulta_personal="SELECT hora_base FROM nompersonal WHERE cedula = '{$cedula}'";

    $resultado_persona=sql_ejecutar($consulta_personal);
    $fetch_personal=fetch_array($resultado_persona);    
    $hora_base=$fetch_personal['hora_base'];
    
    if($hora_base!=8.00 && $hora_base!=4.00)
    {
        //echo "CEDULA: "; echo $cedula; echo " - HORA BASE: "; echo $hora_base;
        $hora_base=8;
        //exit;
    }
    else
    {
        if($hora_base==8.00)            
        {
            $hora_base = 8;
        }
        if($hora_base==4.00)            
        {
            $hora_base = 4;
        }
    }
    
    //echo "<br>"; echo "CEDULA: "; echo $cedula; echo " - HORA BASE: "; echo $hora_base; 
    
    if($horas>=$hora_base)
    {    
        $dias_calculo = $horas / $hora_base;
        
        $dias_separado = explode(".",$dias_calculo);
        $parte_entera = $dias_separado[0];
        $parte_decimal = "0.".$dias_separado[1];
        $dias2 = $parte_entera;
        $horas2 = $parte_decimal * $hora_base;

    }
    else
    {
        $dias2 = 0;
        $horas2 = $horas;
    }
    //echo "<br>"; echo "DIAS CALCULO: "; echo $dias_calculo; echo " - ENTERA: "; echo $parte_entera; echo " - DECIMAL: "; echo $parte_decimal; 
    //echo "<br>"; echo "DIAS: "; echo $dias2; echo " - HORAS: "; echo $horas2; echo " - MINUTOS: "; echo $minutos; 
   
    
    //OBTENEMOS LOS SALDOS ACTUALES DE TIEMPO COMPENSATORIO PARA EL FUNCIONARIO
    $consulta_tiempo="SELECT * FROM tiempos WHERE cedula='$cedula' AND tipo_justificacion='$tipo_justificacion'";
    //echo "<br>"; echo "CONSULTA: "; echo $consulta_tiempo;
    $resultado_tiempo=query($consulta_tiempo,$conexion);
    $filas = mysqli_num_rows($resultado_tiempo);
    //echo "<br>"; echo "FILAS CONSULTA: "; echo $filas;
    if($filas>0)
    {
        $fetch_tiempo=fetch_array($resultado_tiempo,$conexion);
        $restante = $fetch_tiempo['restante'];
        $dias_restante = $fetch_tiempo['dias_restante'];
        $horas_restante = $fetch_tiempo['horas_restante'];
        $minutos_restante = $fetch_tiempo['minutos_restante'];
    }
    
    //echo "<br>"; echo "DIAS RESTANTES: "; echo $dias_restante; echo " - HORAS RESTANTES: "; echo $horas_restante; echo " - MINUTOS RESTANTS: "; echo $minutos_restante; 
    
    
    if($tipo_ajuste==2)
    {
        //echo "<br>"; echo "ENTRO AJUSTE DISMINUCION";
        if($filas>0)
        {
            $restante_actualizado = $restante - $duracion;
            $dias_restante_actualizado = $dias_restante - $dias2;
            $horas_restante_actualizado = $horas_restante - $horas2;
            $minutos_restante_actualizado = $minutos_restante - $minutos;
        }
//        if($restante_actualizado < 0)
//        {
//            ?>

<!--            <script type="text/javascript">

                         alert("Cantidad de Tiempo excede el Restante. Verifique");

            </script>-->
            //<?
//
//            activar_pagina("expediente_list.php?cedula=$cedula");
//            exit;
//
//        }    
                
        $dias=$dias*(-1);
        $horas=$horas*(-1);
        $minutos=$minutos*(-1);
        $duracion=$duracion*(-1);       
        
    }
    else
    {
        //echo "<br>"; echo "ENTRO AJUSTE AUMENTO";
        if($filas>0)
        {
            $restante_actualizado = $restante + $duracion;
            $dias_restante_actualizado = $dias_restante + $dias2;
            $horas_restante_actualizado = $horas_restante + $horas2;
            $minutos_restante_actualizado = $minutos_restante + $minutos;            
           
        }
    }
    
    if($minutos_restante_actualizado>=60)
    {
        //echo "<br>"; echo "ENTRO MINUTOS RESTANTES ACTUALIZADOS MAYOR QUE 60";
        $minutos_restante_actualizado=$minutos_restante_actualizado-60;
        $horas_restante_actualizado=$horas_restante_actualizado+1;                
    }
    if($horas_restante_actualizado>=$hora_base)
    {
        //echo "<br>"; echo "ENTRO HORAS RESTANTES ACTUALIZADOS MAYOR QUE HORA BASE";
        $horas_restante_actualizado=$horas_restante_actualizado-$hora_base;
        $dias_restante_actualizado=$dias_restante_actualizado+1;
    }
    
    if($minutos_restante_actualizado<0)
    {
        //echo "<br>"; echo "ENTRO MINUTOS RESTANTES ACTUALIZADOS MENOR QUE CERO";
        $minutos_restante_actualizado=60+$minutos_restante_actualizado;
        $horas_restante_actualizado=$horas_restante_actualizado-1;                
    }
    if($horas_restante_actualizado<0)
    {
        //echo "<br>"; echo "ENTRO HORAS RESTANTES ACTUALIZADOS MENOR QUE CERO";
        $horas_restante_actualizado=$hora_base+$horas_restante_actualizado;
        $dias_restante_actualizado=$dias_restante_actualizado-1;
    }
        
    //echo "<br>"; echo "DIAS RESTANTES ACTUALIZADOS: "; echo $dias_restante_actualizado; echo " - HORAS RESTANTES ACTUALIZADOS: "; echo $horas_restante_actualizado; echo " - MINUTOS RESTANTES ACTUALIZADOS: "; echo $minutos_restante_actualizado; 
   
    //exit;
    if($filas>0)
    {
        //ACTUALIZACION TIEMPOS
        $actualizacion_tiempos = "UPDATE tiempos SET restante = '{$restante_actualizado}',dias_restante = '{$dias_restante_actualizado}',"
                            . "horas_restante = '{$horas_restante_actualizado}', minutos_restante = '{$minutos_restante_actualizado}'"
                            . "WHERE cedula='{$cedula}' AND tipo_justificacion='{$tipo_justificacion}'";
        $resultado_actualizacion_tiempos=query($actualizacion_tiempos,$conexion); 
        $dias_restante_final = $dias_restante_actualizado;
        $horas_restante_final = $horas_restante_actualizado;
        $minutos_restante_final = $minutos_restante_actualizado;
        //echo "<br>"; echo "CONSULTA ACTUALIZACION: "; echo $actualizacion_tiempos;
    }
    else
    {
        //INSERCION TIEMPOS
        $insercion_tiempos="INSERT INTO tiempos
                            (id, cedula, tipo_justificacion, restante, dias_restante, horas_restante, minutos_restante) 
                            VALUES
                            ('','{$cedula}','{$tipo_justificacion}', '{$duracion}','{$dias2}','{$horas2}','{$minutos}')";
        $resultado_insercion_tiempos=query($insercion_tiempos,$conexion);
        $dias_restante_final = $dias2;
        $horas_restante_final = $horas2;
        $minutos_restante_final = $minutos;
    }
    
    //INSERCION DIAS INCAPACIDAD
    $consulta_incapacidad="INSERT INTO dias_incapacidad
                        (cod_user, tipo_justificacion, fecha, tiempo, observacion, documento, st, usr_uid, dias, horas, 
                        minutos, idparent, cedula, dias_restante, horas_restante, minutos_restante) 
                        VALUES
                        ('{$nomposicion_id}','{$tipo_justificacion}', '{$fecha}','{$duracion}','{$descripcion}', NULL, NULL,"
                        . "'{$user_uid}','{$dias}','{$horas}','{$minutos}','{$codigo}','{$cedula}',"
                        . "'{$dias_restante_final}','{$horas_restante_final}','{$minutos_restante_final}')";
    $resultado=query($consulta_incapacidad,$conexion);
    //echo "<br>"; echo "CONSULTA INCAPACIDAD: "; echo $consulta_incapacidad;
   
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
        $licencia_tipo = 3;
        if($subtipo=="44")
            $estado = "Licencias con Sueldo - Estudios";
        if($subtipo=="45")
            $estado = "Licencias con Sueldo - Capacitación";
        if($subtipo=="46")
            $estado = "Licencias con Sueldo - Representación de la Institución, Estado o País";
         if($subtipo=="47")
            $estado = "Licencias con Sueldo - Representación de la asociación de servidor";
        if($subtipo=="109")
            $estado = "Licencias con Sueldo - Razones Extraordinarias";
    }
    if($tipo=="16")
    {
        $tipo_funcionario=5;
        $licencia_tipo = 2;
        if($subtipo=="48")
            $estado = "Licencias sin Sueldo - Asumir cargo de elección popular";
        if($subtipo=="49")
            $estado = "Licencias sin Sueldo - Asumir cargo de libre nombramiento y remoción";
        if($subtipo=="50")
            $estado = "Licencias sin Sueldo - Estudiar";
        if($subtipo=="51")
            $estado = "Licencias sin Sueldo - Asuntos Personales";
    }
    if($tipo=="17")
    {
//        $tipo_funcionario=6;
//        $licencia_tipo = 2;
         if($subtipo=="52")
         {
            $estado = "Licencias Especiales - Gravidez";
            $licencia_tipo = 1;
         }
        if($subtipo=="53")
            $estado = "Licencias Especiales - Enfermedad/Incapacidad superior quince días";
        if($subtipo=="54")
        {
            $estado = "Licencias Especiales - Riesgos Profesionales";
            $licencia_tipo = 4;
        }
        if($subtipo=="82")
            $estado = "Licencias Especiales - Enfermedad Profesional";
    }    
    
    $estado= utf8_decode($estado);    
    
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
                (usr_uid, tipo_licencia, nro_resolucion, fecha_resolucion, fecha_desde, fecha_hasta, 
                motivo, cod_expediente_det,cedula)
                VALUES  
                ('{$user_uid}','{$licencia_tipo}','{$numero_resolucion}','{$fecha_resolucion}', "
                . "'{$fecha_inicio}','{$fecha_fin}','{$estado}','{$codigo}','{$cedula}')";
    
    $resultado_licencia=query($consulta_licencia,$conexion);
    
//    //INSERCIÓN MOV CONTRALORIA
//     $consulta_contraloria="INSERT INTO mov_contraloria
//                (id_mov_contraloria, personal_id, quincena, mes, ano, num_decreto, fecha_decreto, nomposicion_id, cedula, seguro_social, 
//                clave_ir, sexo, nombres, apellido_paterno, apellido_materno, apellido_casada, fecing, titular_interino, tipemp, observacion,
//                id_mov_tipo, fecha, usuario,cod_expediente_det) 
//                VALUES  
//                ('','{$personal_id}','{$quincena}','{$mes}','{$ano}','{$numero_resolucion}','{$fecha_resolucion}','{$nomposicion_id}',"
//                . "'{$cedula}','{$seguro_social}','{$clave_ir}','{$sexo}','{$nombres}','{$apellido_paterno}','{$apellido_materno}',"
//                . "'{$apellido_casada}','{$fecing}','{$titular_interino}','{$tipemp}','{$observacion}','{$id_mov_tipo}',"
//                . "'{$fecha}','".$usuario."','{$codigo}')";
//    
//    $resultado_contraloria=query($consulta_contraloria,$conexion);
//    $ultimo_id = mysqli_insert_id($conexion);
//    
//    //LOG TRANSACCIONES - MOV CONTRALORIA
//
//    $descripcion_transaccion = 'Insertado Movimiento Contraloria: ' . $ultimo_id . ', Código' . $codigo  . ' Tipo: Licencia '. $tipo;
//    $descripcion_transaccion=utf8_decode($descripcion_transaccion);
//    $consulta_transaccion = "INSERT INTO log_transacciones (cod_log, descripcion, fecha_hora, modulo, url, accion, valor, usuario) 
//                VALUES ('', '".$descripcion_transaccion."', now(), 'Expediente-Aprobación', 'expediente_aprobar.php', 'Aprobar','".$codigo."','".$usuario."')";
//
//    $resultado_transaccion=query($consulta_transaccion,$conexion);
//    
//    //INSERCIÓN MOV LICENCIA
//    
//     $consulta_licencia="INSERT INTO mov_licencia
//                (id_mov_licencia, id_mov_contraloria, licencia_tipo, licencia_meses, licencia_dias, licencia_desde, licencia_hasta, licencia_descripcion)
//                VALUES  
//                ('','{$ultimo_id}','{$licencia_tipo}','{$meses}','{$dias}','{$fecha_inicio}','{$fecha_fin}','{$estado}')";
//    
//    $resultado_licencia=query($consulta_licencia,$conexion);
//    
//    //LOG TRANSACCIONES - MOV LICENCIA
//
//    $descripcion_transaccion = 'Insertado Movimiento Licencia: ' . $ultimo_id . ', Código' . $codigo  . ' Tipo: Licencia '. $tipo;
//    $descripcion_transaccion=utf8_decode($descripcion_transaccion);
//    $consulta_transaccion = "INSERT INTO log_transacciones (cod_log, descripcion, fecha_hora, modulo, url, accion, valor, usuario) 
//                VALUES ('', '".$descripcion_transaccion."', now(), 'Expediente-Aprobación', 'expediente_aprobar.php', 'Aprobar','".$codigo."','".$usuario."')";
//
//    $resultado_transaccion=query($consulta_transaccion,$conexion);
    
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
    
    if($fecha_inicio<=$fecha_actual && $fecha_fin>$fecha_actual)
    {
        $consulta_campo_adicional1="SELECT valor FROM nomcampos_adic_personal WHERE ficha='$personal_id' AND id='7'";
        $resultado_campo_adicional1=query($consulta_campo_adicional1,$conexion);
        $fetch_campo_adicional1=fetch_array($resultado_campo_adicional1,$conexion);
        $valor_campo_adicional1 = $fetch_campo_adicional1['valor'];

        $filas_campo_adicional1= mysqli_num_rows($resultado_campo_adicional1);

    //    echo $filas_campo_adicional1; echo " ";
    //    echo $valor_campo_adicional1; echo " ";

        $consulta_campo_adicional2="SELECT valor FROM nomcampos_adic_personal WHERE ficha='$personal_id' AND id='8'";
        $resultado_campo_adicional2=query($consulta_campo_adicional2,$conexion);
        $fetch_campo_adicional2=fetch_array($resultado_campo_adicional2,$conexion);
        $valor_campo_adicional2 = $fetch_campo_adicional2['valor'];

        $filas_campo_adicional2= mysqli_num_rows($resultado_campo_adicional2);

    //    echo $filas_campo_adicional2; echo " ";
    //    echo $valor_campo_adicional2; 

        //exit;
        $campo_adicional1="NO";
        $campo_adicional2=$estado. " DESDE: ".$fecha_inicio." HASTA: ".$fecha_fin. " FECHA RESOLUCION: ".$fecha_resolucion." NUMERO RESOLUCION: ".$numero_resolucion;

        //ACTUALIZACION PERSONAL
       $consulta_personal = "UPDATE nompersonal SET estado = '{$estado}', fechalic = '{$fecha_inicio}',fechareilic = '{$fecha_fin}'"
                           . "WHERE cedula='{$cedula}'";
       $resultado_personal=query($consulta_personal,$conexion); 

       if($filas_campo_adicional1>0)
       {
           $consulta_adicional1 = "UPDATE nomcampos_adic_personal SET valor = '{$campo_adicional1}'"
                           . "WHERE  ficha='$personal_id' AND id='7'";

       }
       else
       {
           $consulta_adicional1="INSERT INTO nomcampos_adic_personal
                   (ficha, id, valor, mascara, tipo, codorgd, ee, tiponom)
                   VALUES  
                   ('{$personal_id}','7','{$campo_adicional1}',NULL, "
                   . "'A',NULL,NULL,'{$tipnom}')";
       }
       $resultado_adicional1=query($consulta_adicional1,$conexion); 

       if($filas_campo_adicional2>0)
       {
           $consulta_adicional2 = "UPDATE nomcampos_adic_personal SET valor = '{$campo_adicional2}'"
                           . "WHERE  ficha='$personal_id' AND id='8'";

       }
       else
       {
           $consulta_adicional2="INSERT INTO nomcampos_adic_personal
                   (ficha, id, valor, mascara, tipo, codorgd, ee, tiponom)
                   VALUES  
                   ('{$personal_id}','8','{$campo_adicional2}',NULL, "
                   . "'A',NULL,NULL,'{$tipnom}')";
       }
       $resultado_adicional2=query($consulta_adicional2,$conexion); 
    }
    
}

//BAJA - APROBAR
if ($tipo=="19")
{
    $fecha_baja=$fetch_expediente['fecha_resolucion'];
    $numero_baja=$fetch_expediente['numero_resolucion'];

    $fecha = date('Y-m-d');
    $estado = "De Baja";

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

//ROTACION / REASIGNACION / APOYO TEMPORAL - APROBAR
if ($tipo=="24" || $tipo=="25" || $tipo=="26")
{   
        
    if($fetch_expediente['departamento_nuevo']==NULL || $fetch_expediente['departamento_nuevo']=='' || $fetch_expediente['departamento_nuevo']==0)
    {
        $nivel2=$fetch_expediente['departamento_anterior'];
    }
    else
    {
        $nivel2=$fetch_expediente['departamento_nuevo'];
    }
   
   
    if($tipo==24)
    {
        $tipo_movimiento = "T";
        $tipo_accion=48;
    }
    if($tipo==25)
    {
        $tipo_movimiento = "R";
        $tipo_accion=49;
    }
    if($tipo==26)
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
                    . "'{$fetch_expediente['departamento_nuevo']}',"
                    . "'{$fetch_expediente['fecha_inicio']}',"
                    . "'{$fetch_expediente['fecha_fin']}',"
                    . "'{$tipo_movimiento}',"
                    . "'{$fecha_actual}',"
                    . "'{$usuario}',"
                    . "'{$fetch_expediente['fecha_memo']}',"
                    . "'{$fetch_expediente['num_memo']}',"
                    . "'{$fetch_expediente['dejado']}',"
                    . "'{$cedula}',"
                    . "'{$codigo}')";
    $resultado_empleado_cargo=query($sql_empleado_cargo,$conexion); 
    
    //ACTUALIZACION PERSONAL
    $consulta_personal = "UPDATE nompersonal SET "
                        . "fechamov = '{$fetch_expediente['fecha_inicio']}', "
                        . "fechareimov = '{$fetch_expediente['fecha_fin']}', "
                        . "IdDepartamento = '{$nivel2}'"
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

//AJUSTE DE TIEMPO - APROBAR
if ($tipo=="27")
{
    $subtipo=$fetch_expediente['subtipo'];
    
    $consulta_justificacion="SELECT * FROM expediente_subtipo WHERE id_expediente_subtipo='$subtipo'";
    $resultado_justificacion=query($consulta_justificacion,$conexion);
    $fetch_justificacion=fetch_array($resultado_justificacion,$conexion);
    $tipo_justificacion=$fetch_justificacion['tipo_justificacion'];
    
    $fecha=$fetch_expediente['fecha'];
    $descripcion=$fetch_expediente['descripcion'];
    $tipo_ajuste=$fetch_expediente['tipo_ajuste'];
    
    $dias=$fetch_expediente['dias'];
    $restantes=$fetch_expediente['restante'];
    $horas=$fetch_expediente['horas'];
    $minutos=$fetch_expediente['minutos'];
    $duracion=$fetch_expediente['duracion'];
    
        if($tipo_ajuste==2)
        {
            $dias=$dias*(-1);
            $horas=$horas*(-1);
            $minutos=$minutos*(-1);
            $duracion=$duracion*(-1);
        }
        //INSERCION DIAS INCAPACIDAD
        $consulta_incapacidad="INSERT INTO dias_incapacidad
                            (cod_user, tipo_justificacion, fecha, tiempo, observacion, documento, st, usr_uid, dias, 
                            horas, minutos, idparent, cedula) 
                            VALUES
                            ('{$nomposicion_id}','{$tipo_justificacion}', '{$fecha}','{$duracion}', '{$descripcion}', NULL, NULL,'{$user_uid}',"
                            . "'{$dias}','{$horas}','{$minutos}','{$codigo}', '{$cedula}')";
        $resultado=query($consulta_incapacidad,$conexion);
   
}


//MISION OFICIAL - APROBAR
if ($tipo=="28" )
{          
    $tipo_accion=36;
    $tipo_justificacion=4;
    
    $fecha=$fetch_expediente['fecha'];
    $fecha_inicio=$fetch_expediente['fecha_inicio'];
    $fecha_fin=$fetch_expediente['fecha_fin'];
    $descripcion=$fetch_expediente['descripcion'];
    
    $dias=$fetch_expediente['dias'];
    $restantes=$fetch_expediente['restante'];
    $horas=$fetch_expediente['horas'];
    $minutos=$fetch_expediente['minutos'];
    $duracion=$fetch_expediente['duracion'];
    
    
    $tipo_mision=$fetch_expediente['tipo_mision'];
	
	//INSERCION DIAS INCAPACIDAD
        $consulta_incapacidad="INSERT INTO dias_incapacidad
                            (cod_user, tipo_justificacion, fecha, tiempo, observacion, documento, st, usr_uid, dias, horas, 
                            minutos, idparent, cedula, dias_restante, horas_restante, minutos_restante, tipo_mision) 
                            VALUES
                            ('{$nomposicion_id}','{$tipo_justificacion}', '{$fecha}','{$duracion}','{$descripcion}', NULL, NULL,"
                            . "'{$user_uid}','{$dias}','{$horas}','{$minutos}','{$codigo}','{$cedula}',"
                            . "'{$dias_restante_final}','{$horas_restante_final}','{$minutos_restante_final}','{$tipo_mision}')";
        $resultado=query($consulta_incapacidad,$conexion);
    
    if($fecha_actual==$fecha_inicio)
    {
        $estado = "Misión Oficial";
        //ACTUALIZACION PERSONAL

        $consulta_personal = "UPDATE nompersonal SET estado = '{$estado}'"
                   . " WHERE cedula='{$cedula}'";

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
//if ($tipo=="34" )
//{       
//    
//    if($subtipo=="65") //ANTIGUEDAD
//    {
//        $tipo_accion=13; 
//        $sobresueldo = $fetch_expediente['sobresueldo_antiguedad'];
//        $cadena = "antiguedad";
//    }
//    else if($subtipo=="66") //ZONAS APARTADAS
//    {
//
//        $tipo_accion=14;
//        $sobresueldo = $fetch_expediente['sobresueldo_altoriesgo'];
//        $cadena = "zona_apartada";
//    }
//    else if($subtipo=="67") //JEFATURA
//    {
//
//        $tipo_accion=15;
//        $sobresueldo = $fetch_expediente['sobresueldo_jefatura'];
//        $cadena = "jefaturas";
//    }
//    else if($subtipo=="68") //ESPECIALIDAD O EXC
//    {
//
//        $tipo_accion=16;
//         $sobresueldo = $fetch_expediente['sobresueldo_especialidad'];
//        $cadena = "especialidad";
//    } 
//    else if($subtipo=="69") //OTROS
//    {
//
//        $tipo_accion=17;
//         $sobresueldo = $fetch_expediente['sobresueldo_otros'];
//        $cadena = "otros";
//    }
//    else if($subtipo=="70") //GASTOS REPRESENTACION
//    {
//
//        $tipo_accion=18;
//         $sobresueldo = $fetch_expediente['sobresueldo_gastos_representacion'];
//        $cadena = "gastos_representacion";
//    }   
//    
//     //ACTUALIZACION PERSONAL
//    
//    $consulta_personal = "UPDATE nompersonal SET $cadena = '{$sobresueldo}'"
//               . " WHERE cedula='{$cedula}'";
//    
//    $resultado_personal=query($consulta_personal,$conexion); 
//    
//    //ACCION FUNCIONARIO    
//    $consulta_correlativo = "SELECT correlativo FROM accion_funcionario_tipo "
//                    . "WHERE id_accion_funcionario_tipo = '$tipo_accion'";
//    $resultado_correlativo=query($consulta_correlativo,$conexion);
//    $fetch_correlativo=fetch_array($resultado_correlativo,$conexion);
//    $correlativo = $fetch_correlativo["correlativo"];
//    $correlativo = $correlativo+1;
//
//   $consulta_accion="INSERT INTO accion_funcionario
//            (id_accion_funcionario, tipo_accion, numero_accion,id_funcionario)
//            VALUES  
//            ('','{$tipo_accion}','{$correlativo}', '{$id_empleado}')";
//    $resultado_accion=query($consulta_accion,$conexion);           
//
//    $consulta_accion_tipo ="UPDATE accion_funcionario_tipo SET correlativo='".$correlativo."' "
//                        . "WHERE id_accion_funcionario_tipo='".$tipo_accion."'";
//    $resultado_accion_tipo=query($consulta_accion_tipo,$conexion);  
//    
//}

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
    
    if($fetch_expediente['fecha_inicio']==NULL || $fetch_expediente['fecha_inicio']=='' || $fetch_expediente['fecha_inicio']==0 || $fetch_expediente['fecha_inicio']=="0000-00-00")
    {
        $fecha_ingreso=$fetch_expediente['fecha_inicio_anterior'];
    }
    else
    {
        $fecha_ingreso=$fetch_expediente['fecha_inicio'];
    }
    
    if($fetch_expediente['fecha_inicio_periodo']==NULL || $fetch_expediente['fecha_inicio_periodo']=='' || $fetch_expediente['fecha_inicio_periodo']==0 || $fetch_expediente['fecha_inicio_periodo']=="0000-00-00")
    {
        $fecha_inicio_periodo=$fetch_expediente['fecha_inicio_periodo_anterior'];
    }
    else
    {
        $fecha_inicio_periodo=$fetch_expediente['fecha_inicio_periodo'];
    }
    
    if($fetch_expediente['fecha_fin_periodo']==NULL || $fetch_expediente['fecha_fin_periodo']=='' || $fetch_expediente['fecha_fin_periodo']==0 || $fetch_expediente['fecha_fin_periodo']=="0000-00-00")
    {
        $fecha_fin_periodo=$fetch_expediente['fecha_fin_periodo_anterior'];
    }
    else
    {
        $fecha_fin_periodo=$fetch_expediente['fecha_fin_periodo'];
    }
    
    if($fetch_expediente['fecha_permanencia']==NULL || $fetch_expediente['fecha_permanencia']=='' || $fetch_expediente['fecha_permanencia']==0 || $fetch_expediente['fecha_permanencia']=="0000-00-00")
    {
        $fecha_permanencia=$fetch_expediente['fecha_permanencia_anterior'];
    }
    else
    {
        $fecha_permanencia=$fetch_expediente['fecha_permanencia'];
    }
    
    $estado = $fetch_expediente['situacion_nueva'];
   
    
    $sql_situacion = "SELECT codigo,situacion FROM nomsituaciones WHERE `codigo`='{$estado}' ";
    $resultado_situacion=sql_ejecutar($sql_situacion);
    $fetch_situacion=fetch_array($resultado_situacion); 
    $situacion=$fetch_situacion['situacion']; 
       
    
    //ACTUALIZACION PERSONAL
    
    $consulta_personal = "UPDATE nompersonal SET fecing = '{$fecha_ingreso}', inicio_periodo = '{$fecha_inicio_periodo}', fin_periodo = '{$fecha_fin_periodo}', "
                        . "fecha_permanencia = '{$fecha_permanencia}', estado = '{$estado}'"
                        . " WHERE cedula='{$situacion}'";
    
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
                id_mov_tipo, fecha,usuario,cod_expediente_det)
                VALUES  
                ('','{$personal_id}','{$quincena}','{$mes}','{$ano}','{$numero_decreto}','{$fecha_decreto}','{$nomposicion_id}',"
                . "'{$cedula}','{$seguro_social}','{$clave_ir}','{$sexo}','{$nombres}','{$apellido_paterno}','{$apellido_materno}',"
                . "'{$apellido_casada}','{$fecing}','{$titular_interino}','{$tipemp}','{$observacion}','{$id_mov_tipo}',"
                . "'{$fecha}','".$usuario."','{$codigo}')";
    
    $resultado_contraloria=query($consulta_contraloria,$conexion);
    $ultimo_id = mysqli_insert_id($conexion);
    
    //LOG TRANSACCIONES - MOV CONTRALORIA

    $descripcion_transaccion = 'INSERTADO MOVIMIENTO DE CONTRALORIA ' . $ultimo_id . ', CODIGO' . $codigo  . ' DESCRIPCION: '. $observacion;

    $consulta_transaccion = "INSERT INTO log_transacciones (cod_log, descripcion, fecha_hora, modulo, url, accion, valor, usuario, host) 
                VALUES ('', '".$descripcion_transaccion."', now(), 'Expediente-Aprobación', 'expediente_aprobar.php', 'Aprobar','".$codigo."','".$usuario."','".$host_ip."')";

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
    $consulta_personal = "UPDATE nompersonal SET otros = '{$sobresueldo}', dieta = '{$dieta}',"
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
    $estado = "De Baja";
    
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
//    
//     echo "aqui";
//    exit;
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
    $posicion_anterior=$fetch_expediente['posicion_anterior'];
    $posicion=$fetch_expediente['posicion_nueva'];
    $cargo=$fetch_expediente['cod_cargo_nuevo'];
    $funcion=$fetch_expediente['funcion_nueva'];
    $planilla=$fetch_expediente['planilla_nueva'];
    //***************************************************************
    //cargar estatus anterior desde la tabla de nompersonal
    $consulta_nompersonal = "SELECT estado FROM nompersonal WHERE cedula = '$cedula'";
    $resultado_nompersonal=query($consulta_nompersonal,$conexion);
    $fetch_nompersonal=fetch_array($resultado_nompersonal,$conexion);
    $estatus_anterior=$fetch_nompersonal['estado'];
    //***************************************************************
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
        
    $situacion1   = 'Licencia con Sueldo';
    $situacion2   = 'Licencias con Sueldo';
    $situacion3   = 'Licencia Por Riesgos Profesionales';
    $situacion4   = 'Licencias Especiales Riesgos Profesionales';
    
    $fecha1=date_create($inicio_periodo);
    $fecha2=date_create($fin_periodo);
    $diff=date_diff($fecha1,$fecha2);
    $dias =$diff->format("%a");
            
    $comparacion1 = strpos($situacion, $situacion1);
    $comparacion2 = strpos($situacion, $situacion2);
    $comparacion3 = strpos($situacion, $situacion3);
    $comparacion4 = strpos($situacion, $situacion4);
    
//    echo "<br>";
//    echo "Dias: "; echo $dias;
//    echo "<br>";
//    echo "Comparación1: "; echo $comparacion1;
//    echo " Comparación2: "; echo $comparacion2;
//    echo " Comparación3: "; echo $comparacion3;
//    echo " Comparación4: "; echo $comparacion4;
//    exit;
    if ($comparacion1 !== false || $comparacion2 !== false)
    {
        $fecha_reintegro=$fecha_permanencia;
    }
    
    if (($comparacion3 !== false || $comparacion4 !== false) && $dias<=120)
    {
        $fecha_reintegro=$fecha_permanencia;
    }
    //*******************************************************************************************
    //ACTUALIZACION PERSONAL
    if ($posicion_anterior == $posicion) {
        //CUANDO SE REITEGRA A LA MISMA POSICION ANTERIOR NO SE CAMBIA LA FECHA DE PERMANENCIA
        $consulta_personal = "UPDATE nompersonal SET fecha_decreto = '{$fecha_decreto}', num_decreto = '{$numero_decreto}',estado = '{$estado}',"
                            . "codnivel1 = '{$gerencia}', codnivel2 = '{$departamento}',codnivel3 = '{$seccion}',nomposicion_id = '{$posicion}',"
                            . "codcargo = '{$cargo}', nomfuncion_id = '{$funcion}',tipnom = '{$planilla}',sueldopro = '{$salario}',suesal = '{$salario}',"
                            . "gastos_representacion = '{$gastos_representacion}',otros = '{$sobresueldo}',dieta = '{$dieta}',combustible = '{$combustible}',"
                            . "inicio_periodo = '{$fecha_inicio}',fin_periodo = '{$fecha_fin}',fecha_permanencia= '$fecha_reintegro'"
                            . "WHERE cedula='{$cedula}'"; 
    }elseif( ( $posicion_anterior != $posicion ) && ( strpos($estatus_anterior, 'Baja') !== false ) ) {
        //CUANDO SE REITEGRA A LA MISMA POSICION ANTERIOR NO SE CAMBIA LA FECHA DE PERMANENCIA
        $consulta_personal = "UPDATE nompersonal SET fecha_decreto = '{$fecha_decreto}', num_decreto = '{$numero_decreto}',estado = '{$estado}',"
                            . "codnivel1 = '{$gerencia}', codnivel2 = '{$departamento}',codnivel3 = '{$seccion}',nomposicion_id = '{$posicion}',"
                            . "codcargo = '{$cargo}', nomfuncion_id = '{$funcion}',tipnom = '{$planilla}',sueldopro = '{$salario}',suesal = '{$salario}',"
                            . "gastos_representacion = '{$gastos_representacion}',otros = '{$sobresueldo}',dieta = '{$dieta}',combustible = '{$combustible}',"
                            . "inicio_periodo = '{$fecha_inicio}',fin_periodo = '{$fecha_fin}',fecha_permanencia= '$fecha_reintegro'"
                            . "WHERE cedula='{$cedula}'"; 
    }else{
        //SI SE CAMBIA LA FECHA DE PERMANENCIA
        $consulta_personal = "UPDATE nompersonal SET fecha_decreto = '{$fecha_decreto}', num_decreto = '{$numero_decreto}',estado = '{$estado}',"
                        . "codnivel1 = '{$gerencia}', codnivel2 = '{$departamento}',codnivel3 = '{$seccion}',nomposicion_id = '{$posicion}',"
                        . "codcargo = '{$cargo}', nomfuncion_id = '{$funcion}',tipnom = '{$planilla}',sueldopro = '{$salario}',suesal = '{$salario}',"
                        . "gastos_representacion = '{$gastos_representacion}',otros = '{$sobresueldo}',dieta = '{$dieta}',combustible = '{$combustible}',"
                        . "inicio_periodo = '{$fecha_inicio}',fin_periodo = '{$fecha_fin}'"
                        . "WHERE cedula='{$cedula}'";  
    }
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

//ACREDITACION DE CARRERA MIGRATORIA - APROBAR
if ($tipo=="59")
{
    $tipo_accion=43;

    $fecha = date('Y-m-d');
   
 
    $consulta_personal = "UPDATE nompersonal SET "
                        ."cm_fecha_notificacion_ingreso='{$fetch_expediente['cm_fecha_notificacion_ingreso']}',
                        cm_numero_resolucion='{$fetch_expediente['cm_numero_resolucion']}',
                        cm_fecha_resolucion='{$fetch_expediente['cm_fecha_resolucion']}',
                        cm_tipo_proceso='{$fetch_expediente['cm_tipo_proceso']}', 
                        otros='{$fetch_expediente['cm_sobresueldo']}',
                        cm_gasto_responsabilidad='{$fetch_expediente['cm_gasto_responsabilidad']}',
                        gastos_representacion='{$fetch_expediente['cm_gasto_representacion']}',
                        cm_incentivo_titulo='{$fetch_expediente['cm_incentivo_titulo']}', 
                        cm_ascenso='{$fetch_expediente['cm_ascenso']}', 
                        cm_directiva_confidencialidad='{$fetch_expediente['cm_directiva_confidencialidad']}', 
                        cm_carta_compromiso='{$fetch_expediente['cm_carta_compromiso']}', 
                        cm_fecha_notificacion_homologacion='{$fetch_expediente['cm_fecha_notificacion_homologacion']}',
                        cm_numero_resolucion_homologacion='{$fetch_expediente['cm_numero_resolucion_homologacion']}',
                        cm_fecha_resolucion_homologacion='{$fetch_expediente['cm_fecha_resolucion_homologacion']}',
                        cm_acreditacion_personal_ordinario='{$fetch_expediente['cm_acreditacion_personal_ordinario']}',
                        cm_auditoria_puesto='{$fetch_expediente['cm_auditoria_puesto']}',
                        cm_placa='{$fetch_expediente['cm_placa']}',
                        cm_promocion='{$fetch_expediente['cm_promocion']}',
                        cm_jubliacion_partida='{$fetch_expediente['cm_jubliacion_partida']}',
                        cm_jubilacion_anio='{$fetch_expediente['cm_jubilacion_anio']}',
                        codcat='9'"
                        . "WHERE cedula='{$fetch_expediente['cedula']}'"; 
   
    $resultado_personal=query($consulta_personal,$conexion);     

    
    //ACTUALIZACION POSICION
        $consulta_posicion = "UPDATE nomposicion SET "
                                ."sobresueldo_otros_1='{$fetch_expediente['cm_sobresueldo']}',
                                gasto_responsabilidad='{$fetch_expediente['cm_gasto_responsabilidad']}',
                                gastos_representacion='{$fetch_expediente['cm_gasto_representacion']}',
                                incentivo_titulo='{$fetch_expediente['cm_incentivo_titulo']}', 
                                ascenso='{$fetch_expediente['cm_ascenso']}'"
                                . "WHERE id='{$nomposicion_id}'"; 

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

//INVESTIGACION - APROBAR
if ($tipo=="61")
{
    $tipo_accion=45;

    $fecha = date('Y-m-d');   
    
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

//INVESTIGACION - APROBAR
if ($tipo=="62")
{
    $tipo_accion=46;

    $fecha = date('Y-m-d');   
    
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

//INVESTIGACION - APROBAR
if ($tipo=="63")
{
    $tipo_accion=47;

    $fecha = date('Y-m-d');   
    
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

//RENOVACION DE CONTRATOS - APROBAR
if ($tipo=="66")
{
    

    $fecha = date('Y-m-d');
   
 
    $consulta_personal = "UPDATE nompersonal SET "
                        ."codcargo='{$fetch_expediente['cod_cargo_nuevo']}',
                        suesal='{$fetch_expediente['monto_nuevo']}',
                        sueldopro='{$fetch_expediente['monto_nuevo']}',
                        inicio_periodo='{$fetch_expediente['fecha_inicio']}',
                        fin_periodo='{$fetch_expediente['fecha_fin']}',
                        proyecto='{$fetch_expediente['proyecto']}', estado='Activo', fecharetiro ='0000-00-00'"
                        . "WHERE cedula='{$fetch_expediente['cedula']}'"; 
   
    $resultado_personal=query($consulta_personal,$conexion);     

    
    
}

//RENOVACION DE CARGO - APROBAR
if ($tipo=="67")
{
    

    $fecha = date('Y-m-d');
   
 
    $consulta_personal = "UPDATE nompersonal SET "
                        ."codcargo='{$fetch_expediente['cod_cargo_nuevo']}',
                        suesal='{$fetch_expediente['monto_nuevo']}',
                        sueldopro='{$fetch_expediente['monto_nuevo']}'"
                        . "WHERE cedula='{$fetch_expediente['cedula']}'"; 
   
    $resultado_personal=query($consulta_personal,$conexion);     

    
    
}

//REGISTRO DE CONTRATOS - APROBAR
if ($tipo=="68")
{
    $fecha = date('Y-m-d');   
    $inicio_periodo = ($inicio_periodo!='')?$inicio_periodo:'0000-00-00';
    $fin_periodo = ($fin_periodo!='')?$fin_periodo:'0000-00-00';
    $consulta_exp = "UPDATE expediente SET
                monto='{$suesal}',
                proyecto_anterior='{$proyecto}',
                gerencia_anterior = '{$codnivel1}',
                fecha_inicio_anterior = '{$inicio_periodo}',
                fecha_fin_anterior = '{$fin_periodo}'
        WHERE cod_expediente_det='$codigo'";
    $resultado_exp=query($consulta_exp,$conexion); 
   
    $resultado_personal=query($consulta_personal,$conexion); 
    $consulta_personal = "UPDATE nompersonal SET "
                        ."codcargo='{$fetch_expediente['cod_cargo_nuevo']}',
                        suesal='{$fetch_expediente['monto_nuevo']}',
                        sueldopro='{$fetch_expediente['monto_nuevo']}',
                        inicio_periodo='{$fetch_expediente['fecha_inicio']}',
                        fin_periodo='{$fetch_expediente['fecha_fin']}',
                        proyecto='{$fetch_expediente['proyecto']}', 
                        codnivel1='{$fetch_expediente['gerencia_nueva']}', 
                        estado='Activo', 
                        fecharetiro ='0000-00-00'"
                        . "WHERE cedula='{$fetch_expediente['cedula']}'"; 
   
    $resultado_personal=query($consulta_personal,$conexion); 
    
}

//INCIDENTES - APROBAR
if ($tipo=="81")
{
    $fecha = date('Y-m-d');    
    $consulta_top_prestamo = "SELECT MAX(numpre*1)+1 as numpre from nomprestamos_cabecera";
    $resultado_top_prestamo = query($consulta_top_prestamo,$conexion);
    $top_prestamo = fetch_array($resultado_top_prestamo);
    
    $consulta1 = "INSERT INTO nomprestamos_cabecera
    (numpre,ficha,fechaapro,fecpricup,monto,estadopre,detalle,codigopr,codnom,totpres,
    cuotas,mtocuota,diciembre,gastos_admon,id_tipoprestamo,frededu)
    SELECT '".$top_prestamo['numpre']."',ficha,fechaapro,fecpricup,monto,estadopre,detalle,codigopr,
    codnom,totpres,cuotas,mtocuota,diciembre,gastos_admon,id_tipoprestamo,frededu
    FROM nomprestamos_cabecera_tmp
    WHERE numpre='$numpre_tmp'";
    $resultado_exp1=query($consulta1,$conexion); 

    $consulta2 = "INSERT INTO nomprestamos_detalles
    (numpre,ficha,numcuo,fechaven,anioven,mesven,salinicial,montocuo,salfinal,estadopre,codnom)
    SELECT '".$top_prestamo['numpre']."',ficha,numcuo,fechaven,anioven,mesven,salinicial,montocuo,salfinal,estadopre,codnom
    FROM nomprestamos_detalles_tmp
    WHERE numpre='{$numpre_tmp}'";    
    $resultado_exp2=query($consulta2,$conexion); 

    $consulta_delete1 = "DELETE FROM   nomprestamos_cabecera_tmp  WHERE numpre='{$numpre_tmp}';";
    $resultado_exp2=query($consulta_delete1,$conexion); 

    $consulta_delete2 = "DELETE FROM   nomprestamos_detalles_tmp  WHERE numpre='{$numpre_tmp}';";
    $resultado_exp1=query($consulta_delete2,$conexion); 

    $update_expediente1 = "UPDATE expediente SET numero_decreto='".$top_prestamo['numpre']."'  WHERE cod_expediente_det='{$codigo}';";
    $resultado_exp1=query($update_expediente1,$conexion); 

    if($resultado_exp1){
        ?>
            <script type="text/javascript">
                alert("Prestamo registrado con Éxito");
            </script>
        <?
    }
    
}
if ($tipo=="83")
{
    $fecha = date('Y-m-d');    
     $consulta_top_prestamo = "SELECT MAX(numpre*1)+1 as numpre from nomprestamos_cabecera";
    $resultado_top_prestamo = query($consulta_top_prestamo,$conexion);
    $top_prestamo = fetch_array($resultado_top_prestamo);

    $consulta1 = "INSERT INTO nomprestamos_cabecera
    (numpre,ficha,fechaapro,fecpricup,monto,estadopre,detalle,codigopr,codnom,totpres,
    cuotas,mtocuota,diciembre,gastos_admon,id_tipoprestamo,frededu)
    SELECT '".$top_prestamo['numpre']."',ficha,fechaapro,fecpricup,monto,estadopre,detalle,codigopr,
    codnom,totpres,cuotas,mtocuota,diciembre,gastos_admon,id_tipoprestamo,frededu
    FROM nomprestamos_cabecera_tmp
    WHERE numpre='$numpre_tmp'";
    $resultado_exp1=query($consulta1,$conexion); 

    $consulta2 = "INSERT INTO nomprestamos_detalles
    (numpre,ficha,numcuo,fechaven,anioven,mesven,salinicial,montocuo,salfinal,estadopre,codnom)
    SELECT '".$top_prestamo['numpre']."',ficha,numcuo,fechaven,anioven,mesven,salinicial,montocuo,salfinal,estadopre,codnom
    FROM nomprestamos_detalles_tmp
    WHERE numpre='$numpre_tmp'"; 
    $resultado_exp2=query($consulta2,$conexion); 

    if($resultado_exp1){
        ?>
            <script type="text/javascript">
                alert("Prestamo registrado con Éxito");
            </script>
        <?
    }
    
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

    $consulta_transaccion = "INSERT INTO log_transacciones (descripcion, fecha_hora, modulo, url, accion, valor, usuario, host) 
                VALUES ('".$descripcion_transaccion."', now(), 'Expediente-Aprobación', 'expediente_aprobar.php', 'Aprobar','".$codigo."','".$usuario."','".$host_ip."')";

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




