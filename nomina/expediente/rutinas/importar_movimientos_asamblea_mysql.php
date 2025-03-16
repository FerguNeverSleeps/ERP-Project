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

$consulta_movimientos="SELECT * FROM rrhh_movimientos";               
$resultado_movimientos=mysqli_query($conexion,$consulta_movimientos);
//$fetch_baja=mysqli_fetch_array($resultado_movimientos); 
echo '<br><br>Movimientos: <table border="1">';
echo '<tbody>';
echo '<tr>';
echo '<td>num</td><td>id</td><td>id_empleado</td><td>cedula_empleado</td><td>fecha</td><td>tipo</td><td>gerencia</td><td>departamento</td><td>seccion</td><td>posicion</td><td>salario_real</td>';
echo '<td>causal baja</td><td>fecha registro</td><td>registrado por</td><td>observacion</td>';
echo '</tr>';
$cont=1;
while($fila=mysqli_fetch_array($resultado_movimientos))
{
    $tipo_tiporegistro=0;    
    $id=$fila['id'];
    $id_empleado=$fila['id_empleado'];
    $fecha=$fila['fecha'];
    $tipo=$fila['tipo'];
    $gerencia=$fila['gerencia'];
    $departamento=$fila['departamento'];
    $seccion=$fila['seccion'];
    $posicion=$fila['posicion'];
    $salario_real=$fila['salario_real'];
    $causal_baja=$fila['causal_baja'];
    $fecha_registro=$fila['fecha_registro'];
    $registrado_por=$fila['registrado_por'];
    $observacion=$fila['observacion'];
    
    echo '<tr>';
    echo "<td>".$cont."</td>";
    echo "<td>".$id."</td>";
    
    $consultap="SELECT * FROM nompersonal WHERE personal_id=$id_empleado";               
    $resultadop=mysqli_query($conexion,$consultap);
    $fetchCon=mysqli_fetch_array($resultadop);                
    $cedula=$fetchCon['cedula'];
    
    echo "<td>".$id_empleado."</td>";
    echo "<td>".$cedula."</td>";
    
    //$fecha_aux = strtotime($fecha);
    list($dia, $mes, $anio) = split('[/.-]', $fecha);
    if($mes="ene")
            $mes=1;
    else if($mes="feb")
            $mes=2;
    else if($mes="mar")
            $mes=3;
    else if($mes="abr")
            $mes=4;
    else if($mes="may")
            $mes=5;
    else if($mes="jun")
            $mes=6;
    else if($mes="jul")
            $mes=7;
    else if($mes="ago")
            $mes=8;
    else if($mes="sep")
            $mes=9;
    else if($mes="oct")
            $mes=10;
    else if($mes="nov")
            $mes=11;
    else if($mes="dic")
            $mes=12;
    $anio='20'.$anio;
    $fecha_mysql=$anio.'-'.$mes.'-'.$dia;
    echo "<td>".$fecha_mysql."</td>";
    
    echo "<td>".$tipo."</td>";
    echo "<td>".$gerencia."</td>";
    echo "<td>".$departamento."</td>";
    echo "<td>".$seccion."</td>";
    echo "<td>".$posicion."</td>";
    echo "<td>".$salario_real."</td>";
    echo "<td>".$causal_baja."</td>";
    echo "<td>".$fecha_registro."</td>";
    echo "<td>".$registrado_por."</td>"; 
    
    $descripcion='IMPORTADO DESDE SISTEMA ANTERIOR - CONCEPTO: '.$tipo;    
    echo "<td>".$descripcion."</td>";
    
    if($tipo=="Traslado")
    {    
        //echo "Ingreso";
        $tipo_registro=9;
        $tipo_tiporegistro=30;
    }
    else if($tipo=="Ingreso")
    {    
        //echo "Ingreso";
        $tipo_registro=55;                    
    }
    else if($tipo=="Reintegro")
    {    
        //echo "Ingreso";
        $tipo_registro=40;                    
    }
    else if($tipo=="Baja")
    {           
        $consulta_baja="SELECT * FROM causal_baja WHERE id_causal_baja='$causal_baja'";               
        $resultado_baja=mysqli_query($conexion,$consulta_baja);
        $fetch_baja=mysqli_fetch_array($resultado_baja);                
        $tipo_registro=$fetch_baja['id_expediente_tipo'];
    }
    echo '</tr>';
    //INSERCIÓN EXPEDIENTE
    $estatus=1;
    $fecha_creacion = date("Y-m-d");
    $usuario = "importacion";
    if($tipo_tiporegistro!=0)
    {
        $consulta_expediente="INSERT INTO expediente
                    (cod_expediente_det, cedula, descripcion, fecha, departamento_nuevo, gerencia_nueva, seccion_nueva, estatus,
                    tipo, subtipo, fecha_creacion, usuario_creacion )
                    VALUES  
                    ('','{$cedula}','{$descripcion}','{$fecha_mysql}','{$departamento}','{$gerencia}','{$seccion}','{$estatus}',"
                    . "'{$tipo_registro}','{$tipo_tiporegistro}','{$fecha_creacion}','{$usuario}')";
    }
    else
    {
        $consulta_expediente="INSERT INTO expediente
                    (cod_expediente_det, cedula, descripcion, fecha, departamento_nuevo, gerencia_nueva, seccion_nueva, estatus,
                    tipo, fecha_creacion, usuario_creacion )
                    VALUES  
                    ('','{$cedula}','{$descripcion}','{$fecha_mysql}','{$departamento}','{$gerencia}','{$seccion}','{$estatus}',"
                    . "'{$tipo_registro}','{$fecha_creacion}','{$usuario}')";
    }
   
    $resultado_expediente=mysqli_query($conexion,$consulta_expediente);
    //echo $resultado_expediente;
    $cont++;
}
echo '</tbody>';
echo '</table>';



?>