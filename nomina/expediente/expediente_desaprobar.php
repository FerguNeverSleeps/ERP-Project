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

if(isset($_REQUEST['cod_desaprobar']))
    $codigo=$_REQUEST['cod_desaprobar'];

$consulta_expediente="SELECT * FROM expediente WHERE cod_expediente_det='$codigo'";
$resultado_expediente=query($consulta_expediente,$conexion);
$fetch_expediente=fetch_array($resultado_expediente,$conexion);
$tipo=$fetch_expediente['tipo'];
$subtipo=$fetch_expediente['subtipo'];
$cedula=$fetch_expediente['cedula'];
$descripcion=$fetch_expediente['descripcion'];
$fecha=$fetch_expediente['fecha'];
$fecha_actual = date('Y-m-d');
$numero_accion=$fetch_expediente['numero_resolucion']; 
$id_solicitud_caso=$fetch_expediente['registro'];

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
$suesal=$fetch_personal['suesal'];
$ficha=$fetch_personal['ficha'];


$usuario = $_SESSION['usuario'];

$consulta_posicion="SELECT Posicion FROM posicionempleado WHERE IdEmpleado='$personal_id'";
$resultado_posicion=query($consulta_posicion,$conexion);
$fetch_posicion=fetch_array($resultado_posicion,$conexion);
$posicion=$fetch_posicion['Posicion'];

$consulta_adjunto="SELECT * FROM expediente_adjunto WHERE cod_expediente_det='$codigo'";
$resultado_adjunto=query($consulta_adjunto,$conexion);
$fetch_adjunto=fetch_array($resultado_adjunto,$conexion);
$filas_adjunto  = count($fetch_adjunto);  

//ESTUDIOS ACADEMICOS - DESAPROBAR
if ($tipo=="1")
{
    
    $consulta_estudios="DELETE FROM empleado_estudios WHERE cod_expediente_det='$codigo'";
    $resultado_estudios=query($consulta_estudios,$conexion);
    
    $tipo_accion=38;
    //ACCION FUNCIONARIO        
    $correlativo = $numero_accion-1;

    $consulta_accion="DELETE FROM accion_funcionario WHERE numero_accion='$numero_accion'";
    $resultado_accion=query($consulta_accion,$conexion);           

    $consulta_accion_tipo ="UPDATE accion_funcionario_tipo SET correlativo='".$correlativo."' "
                        . "WHERE id_accion_funcionario_tipo='".$tipo_accion."'";
    $resultado_accion_tipo=query($consulta_accion_tipo,$conexion);  
}

//CAPACITACION - DESAPROBAR
if ($tipo=="2")
{
    
    $consulta_capacitacion="DELETE FROM empleado_capacitacion WHERE cod_expediente_det='$codigo'";
    $resultado_capacitacion=query($consulta_capacitacion,$conexion);
    
    $tipo_accion=39;
    //ACCION FUNCIONARIO        
    $correlativo = $numero_accion-1;

    $consulta_accion="DELETE FROM accion_funcionario WHERE numero_accion='$numero_accion'";
    $resultado_accion=query($consulta_accion,$conexion);           

    $consulta_accion_tipo ="UPDATE accion_funcionario_tipo SET correlativo='".$correlativo."' "
                        . "WHERE id_accion_funcionario_tipo='".$tipo_accion."'";
    $resultado_accion_tipo=query($consulta_accion_tipo,$conexion);  
}

//PERMISO - DESAPROBAR
if ($tipo=="4")
{
    //DIAS INCAPACIDAD - ELIMINAR
    $consulta_incapacidad="DELETE FROM dias_incapacidad WHERE idparent='$codigo'";
    $resultado_incapacidad=query($consulta_incapacidad,$conexion);
    $consulta_solicitud_caso="DELETE FROM solicitudes_casos WHERE id_solicitudes_casos='$id_solicitud_caso'";
    $resultado_solicitud_caso=query($consulta_solicitud_caso,$conexion);
}

//SUSPENSION - DESAPROBAR
if ($tipo=="6")
{   
       
    $estado="Activo";
    
    $consulta_suspension="DELETE FROM suspenciones WHERE cod_expediente_det='$codigo'";
            $resultado_suspension=query($consulta_suspension,$conexion);

    //ACCION FUNCIONARIO  
    $tipo_accion=28;
    //ACCION FUNCIONARIO        
    $correlativo = $numero_accion-1;

    $consulta_accion="DELETE FROM accion_funcionario WHERE numero_accion='$numero_accion'";
    $resultado_accion=query($consulta_accion,$conexion);           

    $consulta_accion_tipo ="UPDATE accion_funcionario_tipo SET correlativo='".$correlativo."' "
                        . "WHERE id_accion_funcionario_tipo='".$tipo_accion."'";
    $resultado_accion_tipo=query($consulta_accion_tipo,$conexion);  
    
   
        //ACTUALIZACION PERSONAL
       $consulta_personal = "UPDATE nompersonal SET "
               . " fechasus = '', "
               . " fechareisus = '', "
               . " estado = '{$estado}'"
                  . "WHERE cedula='{$cedula}'";
       //echo $consulta2;
       $resultado_personal=query($consulta_personal,$conexion);     
        
       
        $campo_adicional1="SI";
        $campo_adicional2="";

       
         $consulta_adicional1 = "UPDATE nomcampos_adic_personal SET valor = '{$campo_adicional1}'"
                            . "WHERE  ficha='$personal_id' AND id='7'";     
       
        $resultado_adicional1=query($consulta_adicional1,$conexion); 

       
        $consulta_adicional2 = "UPDATE nomcampos_adic_personal SET valor = '{$campo_adicional2}'"
                            . "WHERE  ficha='$personal_id' AND id='8'";

        $resultado_adicional2=query($consulta_adicional2,$conexion); 
   
}

//VACACIONES - DESAPROBAR
if ($tipo=="11")
{   
    $cedula=$fetch_expediente['cedula'];
    $fecha_inicio=$fetch_expediente['fecha_inicio'];
    $fecha_fin=$fetch_expediente['fecha_fin'];
    $fecha_inicio_periodo=$fetch_expediente['fecha_inicio_periodo'];
    $fecha_fin_periodo=$fetch_expediente['fecha_fin_periodo'];
    $fecha_resolucion=$fetch_expediente['fecha_resolucion']; 
    $fecha=$fetch_expediente['fecha']; 
    $dias=$fetch_expediente['dias'];    
    $restante=$fetch_expediente['restante'];
    $descripcion=$fetch_expediente['descripcion'];
    $periodo_vacacion=$fetch_expediente['periodo_vacacion'];
    $numero_resolucion=$fetch_expediente['numero_resolucion']; 
    $numero_accion=$fetch_expediente['numero_accion']; 
   
    $tipo_accion=19;    
    
    
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
    
    
    $saldo=$restante+$dias;
    //ELIMINACION DIAS INCAPACIDAD    
    $consulta_incapacidad="DELETE FROM dias_incapacidad WHERE idparent='$codigo'";
    $resultado_incapacidad=mysqli_query($conexion,$consulta_incapacidad);
    
    if($periodo_vacacion==0)
    {
        //ELIMINACION PERIODOS VACACIONES         
        $consulta_vacaciones="DELETE FROM periodos_vacaciones WHERE cod_expediente_det='$codigo'";
        $resultado_vacaciones=query($consulta_vacaciones,$conexion);
    }
    else
    {
        
        $actualizacion_periodo = "UPDATE periodos_vacaciones SET saldo = '{$saldo}'"
               . "WHERE id='{$periodo_vacacion}'";
        //echo $consulta2;
        $resultado_actualizacion=query($actualizacion_periodo,$conexion);  
    }
     //ACTUALIZACION PERSONAL
    if($fecha_inicio!="0000-00-00" && $fecha_fin!="0000-00-00")
    {
        $fecha_inicio="0000-00-00";
        $fecha_fin="0000-00-00";
        $consulta_personal = "UPDATE nompersonal SET fechavac = '{$fecha_inicio}', fechareivac = '{$fecha_fin}' "
                   . "WHERE cedula='{$cedula}'";
        //echo $consulta2;
        $resultado_personal=query($consulta_personal,$conexion); 
    }
    
    //ACCION FUNCIONARIO        
    $correlativo = $numero_accion-1;

    $consulta_accion="DELETE FROM accion_funcionario WHERE numero_accion='$numero_accion'";
    $resultado_accion=query($consulta_accion,$conexion);           

    $consulta_accion_tipo ="UPDATE accion_funcionario_tipo SET correlativo='".$correlativo."' "
                        . "WHERE id_accion_funcionario_tipo='".$tipo_accion."'";
    $resultado_accion_tipo=query($consulta_accion_tipo,$conexion);  
    
}

//TIEMPO COMPENSATORIO - DESAPROBAR
if ($tipo=="12")
{
    //DIAS INCAPACIDAD - ELIMINAR
    $consulta_incapacidad="DELETE FROM dias_incapacidad WHERE idparent='$codigo'";
    $resultado_incapacidad=query($consulta_incapacidad,$conexion);
}
//LICENCIAS - DESAPROBAR
if ($tipo=="15" || $tipo=="16" || $tipo=="17" )
{   
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
    $numero_accion=$fetch_expediente['numero_accion']; 
   
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
       
    
    //ACCION FUNCIONARIO        
    $correlativo = $numero_accion-1;

    $consulta_accion="DELETE FROM accion_funcionario WHERE numero_accion='$numero_accion'";
    $resultado_accion=query($consulta_accion,$conexion);           

    $consulta_accion_tipo ="UPDATE accion_funcionario_tipo SET correlativo='".$correlativo."' "
                        . "WHERE id_accion_funcionario_tipo='".$tipo_accion."'";
    $resultado_accion_tipo=query($consulta_accion_tipo,$conexion);  
    
    $estado="Activo";
     //ACTUALIZACION PERSONAL
       $consulta_personal = "UPDATE nompersonal SET estado = '{$estado}', fechalic = '',fechareilic = ''"
                           . "WHERE cedula='{$cedula}'";
       $resultado_personal=query($consulta_personal,$conexion); 

       $consulta_adicional1 = "UPDATE nomcampos_adic_personal SET valor = 'SI'"
                           . "WHERE  ficha='$personal_id' AND id='7'";
      
       $resultado_adicional1=query($consulta_adicional1,$conexion); 

      
    $consulta_adicional2 = "UPDATE nomcampos_adic_personal SET valor = ''"
                           . "WHERE  ficha='$personal_id' AND id='8'";

    $resultado_adicional2=query($consulta_adicional2,$conexion); 
    
    //LICENCIAS - ELIMINAR
    $consulta_licencias="DELETE FROM licencias WHERE cod_expediente_det='$codigo'";
    $resultado_licencias=query($consulta_licencias,$conexion);
    
}

//AJUSTE DE TIEMPO - DESAPROBAR
if ($tipo=="27")
{
    //DIAS INCAPACIDAD - ELIMINAR
    $consulta_incapacidad="DELETE FROM dias_incapacidad WHERE idparent='$codigo'";
    $resultado_incapacidad=query($consulta_incapacidad,$conexion);
}

//BAJA (OFICIAL) - DESAPROBAR
if ($tipo=="57")
{   
    $cedula=$fetch_expediente['cedula'];    
    $nomposicion_id=$fetch_expediente['posicion_anterior'];
    $numero_accion=$fetch_expediente['numero_accion']; 
   
    $tipo_accion=41;    
    
    $consulta_personal="SELECT * FROM nompersonal WHERE cedula = '{$cedula}'";

    $resultado_persona=sql_ejecutar($consulta_personal);
    $fetch_personal=fetch_array($resultado_persona); 
    $personal_id=$fetch_personal['personal_id'];    
    $apenom=$fetch_personal['apenom'];
    $tipnom=$fetch_personal['tipnom'];
    $fecing=$fetch_personal['fecing'];
    $nomfuncion_id=$fetch_personal['nomfuncion_id'];
    $ctacontab=$fetch_personal['ctacontab'];    
    $IdDepartamento=$fetch_personal['IdDepartamento'];    
    $codpro=$fetch_personal['codpro'];
    $codcargo=$fetch_personal['codcargo'];   
    $tipo_funcionario=$fetch_personal['tipo_funcionario'];   
    $suesal=$fetch_personal['suesal'];   
    $gastos_representacion=$fetch_personal['gastos_representacion']; 
    $num_decreto=$fetch_personal['num_decreto']; 
    $fecha_decreto=$fetch_personal['fecha_decreto']; 
    $otros=$fetch_personal['otros']; 
  
     //ACTUALIZACION PERSONAL
    
    $numero_baja="";
    $fecha_baja="0000-00-00";
    $estado = "REGULAR";
    $consulta_personal = "UPDATE nompersonal SET num_resolucion_baja = '{$numero_baja}', fecha_resolucion_baja = '{$fecha_baja}',estado = '{$estado}'"
               . "WHERE cedula='{$cedula}'";
    //echo $consulta2;
    $resultado_personal=query($consulta_personal,$conexion); 
    
    //ACTUALIZACION POSICION EMPLEADO    
    $consulta_posicionempleado = "INSERT INTO `posicionempleado`
	(`IdEmpleado`,
	 `FechaInicio`,
	`Posicion`, 
	`IdFuncion`,
	`Planilla`,
	`CuentaContable`,
	`IdDepartamento`,
	`IdTituloInstitucional`,
	`IdTipoEmpleado`,
	`Salario`,
	`gastos_repre`,
        `Resolucion`,
        `fecha_decre`,
        `otros`,
        `decre_nombra`)
        VALUES ('".$personal_id."',
            '".$fecing."',
            '".$nomposicion_id."',
            '".$nomfuncion_id."',
            '".$tipnom."',
            '".$ctacontab."',
            '".$IdDepartamento."',
            '".$codcargo."',
            '".$tipo_funcionario."',
            '".$suesal."',
            '".$gastos_representacion."',
            '".$num_decreto."',
            '".$fecha_decreto."',
            '".$otros."',
            '".$num_decreto."' 
    )"; 
    //echo $consulta3;   
    $resultado_posicionempleado=query($consulta_posicionempleado,$conexion);  
    
    //ACTUALIZACION POSICION
    $estado = 1;
    $consulta_posicion = "UPDATE nomposicion SET estado = '{$estado}'"
               . "WHERE nomposicion_id='{$posicion}'";
    //echo $consulta2;
    $resultado_posicion=query($consulta_posicion,$conexion);  
    
    //ACCION FUNCIONARIO        
    $correlativo = $numero_accion-1;

    $consulta_accion="DELETE FROM accion_funcionario WHERE numero_accion='$numero_accion'";
    $resultado_accion=query($consulta_accion,$conexion);           

    $consulta_accion_tipo ="UPDATE accion_funcionario_tipo SET correlativo='".$correlativo."' "
                        . "WHERE id_accion_funcionario_tipo='".$tipo_accion."'";
    $resultado_accion_tipo=query($consulta_accion_tipo,$conexion);  
    
}

//RENOVACION DE CONTRATOS - DESAPROBAR
if ($tipo=="66")
{
    

    $fecha = date('Y-m-d');
   
 
    $consulta_personal = "UPDATE nompersonal SET "
                        ."codcargo='{$fetch_expediente['cod_cargo_anterior']}',
                        suesal='{$fetch_expediente['monto']}',
                        sueldopro='{$fetch_expediente['monto']}',
                        inicio_periodo='',
                        fin_periodo='',
                        proyecto='{$fetch_expediente['proyecto_anterior']}'"
                        . "WHERE cedula='{$fetch_expediente['cedula']}'"; 
   
    $resultado_personal=query($consulta_personal,$conexion);     

    
    
}

//RENOVACION DE CARGO - DESAPROBAR
if ($tipo=="67")
{
    

    $fecha = date('Y-m-d');
   
 
    $consulta_personal = "UPDATE nompersonal SET "
                        ."codcargo='{$fetch_expediente['cod_cargo_anterior']}',
                        suesal='{$fetch_expediente['monto']}',
                        sueldopro='{$fetch_expediente['monto']}'"
                        . "WHERE cedula='{$fetch_expediente['cedula']}'"; 
   
    $resultado_personal=query($consulta_personal,$conexion);     

    
    
}

//REGISTRO DE CONTRATOS - DESAPROBAR
if ($tipo=="68")
{
    $fecha = date('Y-m-d');
    
    $fecha_inicio_anterior = ($fetch_expediente['fecha_inicio_anterior']!='0000-00-00')?$fetch_expediente['fecha_inicio_anterior']:'';
    $fecha_fin_anterior = ($fetch_expediente['fecha_fin_anterior']!='0000-00-00')?$fetch_expediente['fecha_fin_anterior']:'';

    $consulta_personal = "UPDATE nompersonal SET 
        codcargo='{$fetch_expediente['cod_cargo_anterior']}',
        suesal='{$fetch_expediente['monto']}',
        sueldopro='{$fetch_expediente['monto']}',
        inicio_periodo='{$fecha_inicio_anterior}',
        fin_periodo='{$fecha_fin_anterior}',
        proyecto='{$fetch_expediente['proyecto_anterior']}',
        codnivel1='{$fetch_expediente['gerencia_anterior']}'
    WHERE cedula='{$fetch_expediente['cedula']}'"; 
   
    $resultado_personal=query($consulta_personal,$conexion);   
    
}
//Reclamo de pago rhexpress 
if ($tipo=="81")
{
    ?>
        <script type="text/javascript">
            alert("Debe anular el préstamo en el módulo de préstamos");
        </script>
    <?
}
//Reclamo de pago rhexpress 
if ($tipo=="82")
{
    //DIAS INCAPACIDAD - ELIMINAR    
    $consulta_solicitud_caso="DELETE FROM solicitudes_casos WHERE id_solicitudes_casos='$id_solicitud_caso'";
    $resultado_solicitud_caso=query($consulta_solicitud_caso,$conexion);
}
//compras prestamos creditos rhexpress
if ($tipo=="83")
{
    ?>
        <script type="text/javascript">
            alert("Debe anular el préstamo en el módulo de préstamos");
        </script>
    <?
    //DIAS INCAPACIDAD - ELIMINAR    
    $consulta_solicitud_caso="DELETE FROM solicitudes_casos WHERE id_solicitudes_casos='$id_solicitud_caso'";
    $resultado_solicitud_caso=query($consulta_solicitud_caso,$conexion);
}
//actualizacion anual bienes rhexpress
if ($tipo=="84")
{
    //DIAS INCAPACIDAD - ELIMINAR    
    $consulta_solicitud_caso="DELETE FROM solicitudes_casos WHERE id_solicitudes_casos='$id_solicitud_caso'";
    $resultado_solicitud_caso=query($consulta_solicitud_caso,$conexion);
}

/*
//INCIDENTES - DESAPROBAR
if ($tipo=="81")
{
    $fecha = date('Y-m-d'); 

    //PRESTAMO DETALLE TMP - ELIMINAR
    $sql1 = "DELETE FROM nomprestamos_cabecera WHERE numpre='$numero_accion'";
    $resultado_delete=query($sql1,$conexion);    
    
    //PRESTAMO TMP - ELIMINAR
	$sql2 = "DELETE FROM nomprestamos_detalles WHERE numpre='$numero_accion'";
    $resultado_delete=query($sql2,$conexion);
    
}*/
//EXPEDIENTE - ACTUALIZAR
//if($resultado || $resultado_accion_tipo || $resultado_posicion)
//{
    $estatus = $numero_accion = 0;
     $consulta = "UPDATE expediente SET "
                    . "estatus = '{$estatus}',numero_accion = '{$numero_accion}', fecha_desaprobacion = '{$fecha_actual}',usuario_desaprobacion = '{$usuario}'"  
                    . " WHERE cod_expediente_det='{$codigo}'";
//    echo  $consulta;
//    exit;
    $resultado=query($consulta,$conexion); 
    
    //LOG TRANSACCIONES - APROBACION EXPEDIENTE

    $descripcion_transaccion = 'Desaprobado Expediente: ' . $descripcion . ', Código' . $codigo  . ' Tipo: '. $tipo;

    $consulta_transaccion = "INSERT INTO log_transacciones ( descripcion, fecha_hora, modulo, url, accion, valor, usuario, host) 
                VALUES ( '".$descripcion_transaccion."', now(), 'Expediente-Desaprobación', 'expediente_desaprobar.php', 'Desprobar','".$codigo."','".$usuario."','".$host_ip."')";

    $resultado_transaccion=query($consulta_transaccion,$conexion);
    if($resultado_transaccion)
    {
        ?>

        <script type="text/javascript">

                     alert("Movimiento Desprobado con Éxito");

        </script>
        <?
        
        activar_pagina("expediente_list.php?cedula=$cedula");
        
    }    
    
//}
?>




