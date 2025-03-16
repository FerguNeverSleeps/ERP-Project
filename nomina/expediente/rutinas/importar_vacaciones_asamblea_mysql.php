<?php
include('../../../generalp.config.inc.php');
session_start();

error_reporting(E_ALL);

$db_user   = DB_USUARIO;
$db_pass   = DB_CLAVE;
$db_name   = $_SESSION['bd'];
$db_host   = DB_HOST;
$usuario = $_SESSION['usuario'];

$conexion =  mysqli_connect( $db_host, $db_user, $db_pass, $db_name  ) or
      die( 'Could not open connection to server' );

mysqli_query($conexion, 'SET CHARACTER SET utf8');    

$consulta_vacaciones="SELECT * FROM rrhh_vacaciones";               
$resultado_vacaciones=mysqli_query($conexion,$consulta_vacaciones);
echo '<table border="1">';
echo '<tbody>';
echo '<tr>';
echo '<td>num</td><td>POSICION</td><td>CEDULA</td><td>NUMERO MARCAR</td><td>NOMBRE</td><td>APELLIDO</td><td>PERIODO</td><td>FECHA INICIO</td>';
echo '<td>FECHA FIN</td><td>DIAS</td><td>ID RESUELTO</td><td>OBSERVACION</td><td>FECHA AGREGADO</td><td>DIAS RESTANTES</td><td>RESUELTAS</td><td>FECHA RESUELTO</td><td>MINUTOS RESTANTES</td>';
echo '</tr>';
$cont=1;
while($fila=mysqli_fetch_array($resultado_vacaciones))
{          
    $posicion=$fila['posicion'];
    $cedula=$fila['cedula_completa'];
    $numero_marcar=$fila['numero_marcar'];
    $primer_nombre=$fila['primer_nombre'];
    $primer_apellido=$fila['primer_apellido'];
    $periodo=$fila['periodo'];
    $fecha_inicio_periodo=$fila['fecha_inicio'];
    $fecha_fin_periodo=$fila['fecha_fin'];    
    $dias=$fila['dias'];
    $idVacacionResuelto=$fila['idVacacionResuelto'];
    $observacion=$fila['observacion'];
    $fecha_agregado=$fila['fecha_agregado'];
    $restante=$fila['dias_restantes'];
    $resueltas=$fila['resueltas'];
    $fecha_resuelto=$fila['fecha_resuelto'];
    $minutos_restantes=$fila['minutos_restantes'];
    
    $consultap="SELECT useruid, usuario_workflow FROM nompersonal WHERE cedula='$cedula'";               
    $resultadop=mysqli_query($conexion,$consultap);
    $fetchCon=mysqli_fetch_array($resultadop);
    $user_uid=$fetchCon['useruid'];
    $cod_user=$fetchCon['usuario_workflow'];

  
    
    if($observacion=="" || $observacion==" ")
    {
        $descripcion="PERIODO VACACIONAL MIGRADO DESDE EL SISTEMA LEGADO - FECHA CREADA: ". $fecha_agregado . " PERIODO: ".$periodo;     
    }
    else
    {
        $descripcion="PERIODO VACACIONAL MIGRADO DESDE EL SISTEMA LEGADO - FECHA CREADA: ". $fecha_agregado . " PERIODO: ".$periodo. "- ".strtoupper($observacion);
    }
    
    $tipo=11;
    $subtipo=113;
    $estatus=1;
    $fecha_creacion = date("Y-m-d");     

    //INSERCIÓN EXPEDIENTE
    $consulta_expediente="INSERT INTO expediente
                (cod_expediente_det, cedula, descripcion, fecha_inicio_periodo, fecha_fin_periodo, fecha,
                dias, restante, tipo, subtipo, estatus, fecha_creacion, usuario_creacion)
                VALUES  
                ('','{$cedula}','{$descripcion}', '{$fecha_inicio_periodo}','{$fecha_fin_periodo}',"
                . "'{$fecha_agregado}','{$dias}','{$restante}','{$tipo}','{$subtipo}','{$estatus}','{$fecha_creacion}','{$usuario}')";
    $insert_expediente=mysqli_query($conexion,$consulta_expediente);
//        echo "CONSULTA EXPEDIENTE: "; echo $consulta_expediente; 
//        echo "<br>";
//        echo "<br>";

    $ultimo_id_expediente = mysqli_insert_id($conexion);
    
    $tipo_justificacion=7;
    //INSERCION DIAS INCAPACIDAD
    $tiempo =  $restante;
    $consulta_incapacidad="INSERT INTO dias_incapacidad
            (id, cod_user, tipo_justificacion, fecha, tiempo, observacion, documento, st, usr_uid, fecha_vence, 
            dias, horas, minutos, idparent, cedula)
            VALUES  
            ('','{$posicion}','{$tipo_justificacion}', '{$fecha_creacion}', '{$tiempo}', '{$descripcion}', '', '', '{$user_uid}',"
            . "'{$fecha_fin}', '{$restante}', '','', '{$ultimo_id_expediente}', '{$cedula}')";
    $insert_incapacidad=mysqli_query($conexion,$consulta_incapacidad);
//        echo "CONSULTA DIAS INCAPACIDAD: "; echo $consulta_incapacidad; 
//        echo "<br>";
//        echo "<br>";
    $ultimo_id_incapacidad = mysqli_insert_id($conexion);
    
    //INSERCION EN PERIODOS VACACIONES
    $consulta_vacaciones="INSERT INTO periodos_vacaciones
            (id_periodo_vacacion, usr_uid, fini_periodo, ffin_periodo, dias,
            saldo, fecha_efectivas, fecha_creacion, usuario_creacion, id_dias_incapacidad, vac_desde,
            vac_hasta, resueltas, cod_expediente_det, cedula)
            VALUES  
            ('','{$user_uid}','{$fecha_inicio_periodo}', '{$fecha_fin_periodo}',"
            . "'{$dias}', '{$restante}', '','{$fecha_creacion}','{$usuario}','{$ultimo_id_incapacidad}','', '','{$resueltas}',"
            . "'{$ultimo_id_expediente}', '{$cedula}')";
    $insert_vacaciones=mysqli_query($conexion,$consulta_vacaciones);
//        echo "CONSULTA VACACIONES: "; echo $consulta_vacaciones; 
//        echo "<br>";
//        echo "<br>";
        
    echo '<tr>';
    echo "<td>".$cont."</td>";
    echo "<td>".$posicion."</td>";   
    echo "<td>".$cedula."</td>";
    echo "<td>".$numero_marcar."</td>";
    echo "<td>".$primer_nombre."</td>";
    echo "<td>".$primer_apellido."</td>";
    echo "<td>".$periodo."</td>";
    echo "<td>".$fecha_fin."</td>";   
    echo "<td>".$fecha_inicio."</td>";
    echo "<td>".$dias."</td>";
    echo "<td>".$idVacacionResuelto."</td>";
    echo "<td>".$descripcion."</td>";
    echo "<td>".$fecha_agregado."</td>";
    echo "<td>".$restante."</td>";
    echo "<td>".$resueltas."</td>";  
    echo "<td>".$fecha_resuelto."</td>";  
    echo "<td>".$minutos_restantes."</td>";  
    echo '</tr>';   
       
    $cont++;
   
}

echo '</tbody>';
echo '</table>';

$truncate="TRUNCATE TABLE rrhh_vacaciones";               
$resultado_truncate=mysqli_query($conexion,$truncate);
?>

