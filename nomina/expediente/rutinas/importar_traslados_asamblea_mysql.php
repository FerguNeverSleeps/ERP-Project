<?php

session_start();

error_reporting(E_ALL);

$db_user   = root;
$db_pass   = haribol;
$db_name   = asamblea_rrhh;
$db_host   = localhost;


$conexion =  mysqli_connect( $db_host, $db_user, $db_pass, $db_name  ) or
      die( 'Could not open connection to server' );

mysqli_query($conexion, 'SET CHARACTER SET utf8');

$consulta_movimientos="SELECT * FROM rrhh_traslados";               
$resultado_movimientos=mysqli_query($conexion,$consulta_movimientos);
//$fetch_baja=mysqli_fetch_array($resultado_movimientos); 
echo '<br><br>Movimientos: <table border="1">';
echo '<tbody>';
echo '<tr>';
echo '<td>num</td><td>id_traslado</td><td>id_empleado</td><td>cedula_empleado</td><td>posicion_empleado</td><td>gerencia</td><td>departamento</td><td>seccion</td>';
echo '<td>fecha_traslado</td><td>creado por</td><td>fecha creacion</td><td>modificado por</td><td>fecha modificacion</td><td>cargo estructura</td><td>cargo real</td><td>observacion</td>';
echo '</tr>';
$cont=1;
while($fila=mysqli_fetch_array($resultado_movimientos))
{   
    $id=$fila['id_traslado'];
    $id_empleado=$fila['id_empleado'];
    $posicion=$fila['posicion_empleado'];
    $gerencia=$fila['gerencia'];
    $departamento=$fila['departamento'];
    $seccion=$fila['seccion'];
    $fecha=$fila['fecha_traslado'];
    $creado_por=$fila['creado_por'];
    $fecha_crea=$fila['fecha_creacion'];
    $modificado_por=$fila['modificado_por'];
    $fecha_modificacion=$fila['fecha_modificacion'];
    $cargo_estructura=$fila['cargo_estructura'];
    $cargo_real=$fila['cargo_real']; 
    $observacion=$fila['observacion'];
    
    echo '<tr>';
    echo "<td>".$cont."</td>";
    echo "<td>".$id."</td>";
    
    $consultap="SELECT * FROM nompersonal WHERE personal_id=$id_empleado";               
    $resultadop=mysqli_query($conexion,$consultap);
    $fetchCon=mysqli_fetch_array($resultadop);                
    $cedula=$fetchCon['cedula'];
    if($cont==2319)
    {    
        //echo " Consulta Personal "; echo $consultap; echo " "; echo "cedula"; echo $cedula;
    }
    echo "<td>".$id_empleado."</td>";
    echo "<td>".$cedula."</td>";
    echo "<td>".$posicion."</td>";
    echo "<td>".$gerencia."</td>";
    echo "<td>".$departamento."</td>";
    echo "<td>".$seccion."</td>";   
    echo "<td>".$fecha."</td>";
    echo "<td>".$creado_por."</td>";
    echo "<td>".$fecha_crea."</td>";
    echo "<td>".$modificado_por."</td>";
    echo "<td>".$fecha_modificacion."</td>";
    echo "<td>".$cargo_estructura."</td>";
    echo "<td>".$cargo_real."</td>";
    
    $descripcion='IMPORTADO DESDE SISTEMA ANTERIOR - CONCEPTO: TRASLADO -'.$observacion;    
    echo "<td>".$descripcion."</td>";
    
    $tipo_registro=9;
    $tipo_tiporegistro=30;
   
    echo '</tr>';
    //INSERCIÓN EXPEDIENTE
    $estatus=1;
    $fecha_creacion = date("Y-m-d");
    $usuario = "importacion";
    $consulta_expediente="INSERT INTO expediente
                    (cod_expediente_det, cedula, descripcion, fecha, departamento_nuevo, gerencia_nueva, seccion_nueva, estatus,
                    tipo, subtipo, fecha_creacion, usuario_creacion )
                    VALUES  
                    ('','{$cedula}','{$descripcion}','{$fecha}','{$departamento}','{$gerencia}','{$seccion}','{$estatus}',"
                    . "'{$tipo_registro}','{$tipo_tiporegistro}','{$fecha_creacion}','{$usuario}')";
    
                    
    $resultado_expediente=mysqli_query($conexion,$consulta_expediente);
    $cont++;
}
echo '</tbody>';
echo '</table>';



?>