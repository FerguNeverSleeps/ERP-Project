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

//GENERAR TIEMPOS

$consulta_personal="SELECT cedula, hora_base FROM nompersonal";

$resultado_personal=mysqli_query($conexion,$consulta_personal);

$cont=1;
while($fila=mysqli_fetch_array($resultado_personal))
{

    $cedula=$fila['cedula'];
    $hora_base=$fila['hora_base'];
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
    $consulta_incapacidad = "SELECT a.cedula AS cedula, b.descripcion as justificacion, b.idtipo as idtipo, SUM(a.tiempo) as tiempo, SUM(a.horas) as horas, "
        . "SUM(a.minutos) as minutos , SUM(a.dias) as dias FROM dias_incapacidad as a, tipo_justificacion as b "
        . "WHERE a.tipo_justificacion=b.idtipo AND a.cedula='$cedula' AND b.idtipo=3 GROUP BY a.tipo_justificacion";
    $resultado_incapacidad=mysqli_query($conexion,$consulta_incapacidad);
    
    while($fila2=mysqli_fetch_array($resultado_incapacidad))
    {
        $cedula=$fila2['cedula'];
        $tipo=$fila2['idtipo'];
        $tiempo=$fila2['tiempo'];
        $minutos=$fila2['minutos'];
        
        if($tiempo>=$hora_base)
        {    
            $dias_calculo = $tiempo / $hora_base;
            $dias_separado = explode(".",$dias_calculo);
            $parte_entera = $dias_separado[0];
            $parte_decimal = $dias_separado[1];
            $dias2 = $parte_entera;
            $horas2 = $parte_decimal * $hora_base;

        }
        else
        {
            $dias2 = 0;
            $horas2 = $tiempo;
        }
        
        $insercion_tiempos="INSERT INTO tiempos
                            (id, cedula, tipo_justificacion, restante, dias_restante, horas_restante, minutos_restante) 
                            VALUES
                            ('','{$cedula}','{$tipo}', '{$tiempo}','{$dias2}','{$horas2}','{$minutos}')";
        $resultado_insercion_tiempos=mysqli_query($conexion,$insercion_tiempos);
    }

}

//GENERAR CAMPOS DIAS INCAPACIDAD
$consulta_incapacidad="SELECT * FROM dias_incapacidad";

$resultado_incapacidad=mysqli_query($conexion,$consulta_incapacidad);

$cont=1;
while($fila=mysqli_fetch_array($resultado_incapacidad))
{

    $id=$fila['id'];
    $dias=$fila['dias'];
    $horas=$fila['horas'];
    $minutos=$fila['minutos'];
    //ACTUALIZACION INCAPACIDAD
    $actualizacion_incapacidad = "UPDATE dias_incapacidad SET dias_restante = '{$dias}', "
                        . "horas_restante = '{$horas}', minutos_restante = '{$minutos}'"
                        . " WHERE id='{$id}' AND tipo_justificacion='3'";
    
    
    $resultado_actualizacion_incapacidad=mysqli_query($conexion,$actualizacion_incapacidad);

}

?>