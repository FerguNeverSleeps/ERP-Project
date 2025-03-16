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

$consulta_movimientos="SELECT * FROM rrhh_estudios";               
$resultado_movimientos=mysqli_query($conexion,$consulta_movimientos);
//$fetch_baja=mysqli_fetch_array($resultado_movimientos); 
echo '<table border="1">';
echo '<tbody>';
echo '<tr>';
echo '<td>num</td><td>id_estudio</td><td>id_empleado</td><td>cedula_empleado</td><td>institucion</td><td>anios</td><td>fecha_egreso</td><td>idoneidad</td>';
echo '<td>ejerce</td><td>creado por</td><td>fecha creacion</td><td>modificado por</td><td>fecha modificacion</td><td>id_empleado_estudio</td>';
echo '</tr>';
$cont=1;
while($fila=mysqli_fetch_array($resultado_movimientos))
{   
    $id=$fila['id_empleado_estudio'];
    $id_estudio=$fila['id_estudio'];
    $id_empleado=$fila['id_empleado'];
    $institucion=$fila['institucion'];
    $anios=$fila['anios'];
    $fecha_egreso=$fila['fecha_egreso'];
    $idoneidad=$fila['idoneidad'];
    $ejerce=$fila['ejerce'];
    $creado_por=$fila['creado_por'];
    $fecha_crea=$fila['fecha_creacion'];
    $modificado_por=$fila['modificado_por'];
    $fecha_modificacion=$fila['fecha_modificacion'];
    $id_empleado_estudio=$fila['id_empleado_estudio'];
    echo '<tr>';
    echo "<td>".$cont."</td>";
    echo "<td>".$id_estudio."</td>";
    
    $consultap="SELECT * FROM nompersonal WHERE personal_id=$id_empleado";               
    $resultadop=mysqli_query($conexion,$consultap);
    $fetchCon=mysqli_fetch_array($resultadop);                
    $cedula=$fetchCon['cedula'];
    
    echo "<td>".$id_empleado."</td>";
    echo "<td>".$cedula."</td>";
    echo "<td>".$institucion."</td>";
    echo "<td>".$anios."</td>";
    echo "<td>".$fecha_egreso."</td>";
    echo "<td>".$idoneidad."</td>";   
    echo "<td>".$ejerce."</td>";
    echo "<td>".$creado_por."</td>";
    echo "<td>".$fecha_crea."</td>";
    echo "<td>".$modificado_por."</td>";
    echo "<td>".$fecha_modificacion."</td>";
    echo "<td>".$id_empleado_estudio."</td>";
    
    $descripcion="MOVIMIENTO DE ESTUDIO EN LA INSTITUCION: ". utf8_decode($institucion);     
    $tipo_registro=1;
   
    echo '</tr>';
    //INSERCIÓN EXPEDIENTE
    $estatus=0;
    $fecha_creacion = date("Y-m-d");
    $usuario = "importacion";
    $consulta_expediente="INSERT INTO expediente
                    (cod_expediente_det, cedula, descripcion, fecha, titulo_profesional, idoneidad, ejerce, estatus,
                    tipo, fecha_creacion, usuario_creacion )
                    VALUES  
                    ('','{$cedula}','{$descripcion}','{$fecha_creacion}','{$id_estudio}','{$idoneidad}','{$ejerce}','{$estatus}',"
                    . "'{$tipo_registro}','{$fecha_creacion}','{$usuario}')";
                        
    //$resultado_expediente=mysqli_query($conexion,$consulta_expediente);
    echo "<br>";echo $consulta_expediente; echo ";";
    $cont++;
}
echo '</tbody>';
echo '</table>';



?>