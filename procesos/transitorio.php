<?php
require_once '../lib/config.php';
require_once '../lib/common.php';
$nomina = @$_GET['nomina'];
$conexion = conexion();

echo "INICIO PROCESO MODIFICACION DE FUNCIONARIOS EN ESTATUS TRANSITORIO"; echo "<br>";

$consultap="select cedula, personal_id,nomposicion_id,fin_periodo from nompersonal where tipo_empleado = 'Transitorio'";
$resultadop = query($consultap, $conexion);

$i=0;
while($filap=fetch_array($resultadop))
{    
    $fecha_baja=$filap['fin_periodo'];
    $fecha_registro="2017-01-01";
    $usuario = $_SESSION['usuario'];

    //ASGINACION DE TIPO EXPEDIENTE BAJA (57) Y SUBTIPO TERMINACION DE NOMBRAMIENTO TRANSITORIO (102)
    $tipo=57;
    $subtipo=102;
    $estatus=1;
    $tipo_accion = 41;
    $descripcion ="De Baja - Proceso Automatizado Finalización de Período en Nombramiento Transitorio";
    
    //DATOS DEL FUNCIONARIOS 
    $cedula= $filap['cedula'];
    $personal_id=$filap['personal_id'];
    $nomposicion_id=$filap['nomposicion_id'];   
    $numero_resuelto=$cedula. "_".$fecha_registro;
    
    //ASIGNACION DEL ESTADO CON QUE SE ACTUALIZARA EL EMPLEADO    
    $estado = "De Baja";
    
    echo "<br>";
    //ACTUALIZACION PERSONAL
    $consulta_personal = "UPDATE nompersonal SET fecha_resolucion_baja = '{$fecha_registro}',num_resolucion_baja = '{$numero_resuelto}',"
                         . "fin_periodo='{$fecha_baja}', fecharetiro='{$fecha_baja}', fecfin='{$fecha_baja}', estado = '{$estado}'"
               . " WHERE cedula='{$cedula}'";
    $resultado_personal=query($consulta_personal,$conexion); 
    echo "ACTUALIZACION FUNCIONARIO (".$i."): "; echo $consulta_personal; echo "<br>";   
    
    //ACTUALIZACION POSICION
    $estado = 0;
    $consulta_posicion = "UPDATE nomposicion SET estado = 0"
               . " WHERE nomposicion_id='{$nomposicion_id}'";
    //echo $consulta2;
    $resultado_posicion=query($consulta_posicion,$conexion);
    echo "ACTUALIZACION POSICION (".$i."): "; echo $consulta_posicion; echo "<br>";
    
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
            ('','{$tipo_accion}','{$correlativo}', '{$personal_id}')";
    $resultado_accion=query($consulta_accion,$conexion);  
    echo "GENERACION ACCION FUNCIONARIO (".$i."): "; echo $consulta_accion; echo "<br>";

    $consulta_accion_tipo ="UPDATE accion_funcionario_tipo SET correlativo='".$correlativo."' "
                        . "WHERE id_accion_funcionario_tipo='".$tipo_accion."'";
    $resultado_accion_tipo=query($consulta_accion_tipo,$conexion);  
    
    $consultae="INSERT INTO expediente
                (cod_expediente_det, cedula, descripcion, fecha, fecha_notificacion, numero_resolucion, fecha_resolucion, 
                tipo, subtipo, estatus, numero_accion,fecha_creacion, usuario_creacion, fecha_aprobacion, usuario_aprobacion)
                VALUES  
                ('','{$cedula}','{$descripcion}', '{$fecha_baja}','{$fecha_registro}','{$numero_resuelto}','{$fecha_registro}',"
                . "'{$tipo}','{$subtipo}','{$estatus}','{$correlativo}','{$fecha_registro}','{$usuario}','{$fecha_registro}','{$usuario}')";    
    
    $resultadoe=query($consultae,$conexion);
    echo "GENERACION EXPEDIENTE (".$i."): "; echo $consultae; echo "<br>";
    $i++;
}
echo "<br>";
echo "FIN PROCESO MODIFICACION DE FUNCIONARIOS EN ESTATUS TRANSITORIO"; 
echo "<br>";
echo "<br>";
echo "TOTAL FUNCIONARIOS: "; echo $i; echo "<br>";
?>
 