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

$consulta_expediente="SELECT * 
                        FROM  `expediente` 
                        WHERE `cod_expediente_det` IN ( 27051, 28055, 27043, 23651, 27109, 21306, 26999, 21565, 27000, 21637, 26983, 22170, 23906, 23907 ) 
                        AND `descripcion` LIKE  '%VACACIONES GENERADAS AUTOMATICAMENTE POR EL SISTEMA%'
                        ORDER BY cod_expediente_det ASC ";               
$resultado_expediente=mysqli_query($conexion,$consulta_expediente);
//echo '<table border="1">';
//echo '<tbody>';
//echo '<tr>';
//echo '<td>num</td><td>POSICION</td><td>CEDULA</td><td>NUMERO MARCAR</td><td>NOMBRE</td><td>APELLIDO</td><td>PERIODO</td><td>FECHA INICIO</td>';
//echo '<td>FECHA FIN</td><td>DIAS</td><td>ID RESUELTO</td><td>OBSERVACION</td><td>FECHA AGREGADO</td><td>DIAS RESTANTES</td><td>RESUELTAS</td><td>FECHA RESUELTO</td><td>MINUTOS RESTANTES</td>';
//echo '</tr>';
$cont=1;
while($fila=mysqli_fetch_array($resultado_expediente))
{          
    
    $codigo=$fila['cod_expediente_det'];    
   
    //ELIMINACION DIAS INCAPACIDAD
    $consulta_incapacidad="DELETE FROM dias_incapacidad WHERE idparent='$codigo'";
    $delete_incapacidad=mysqli_query($conexion,$consulta_incapacidad);
//    echo "CONSULTA DIAS INCAPACIDAD: "; echo $consulta_incapacidad; 
//        echo "<br>";
//        echo "<br>";
    $ultimo_id_incapacidad = mysqli_insert_id($conexion);
    
    //ELIMINACION PERIODOS VACACIONES
    $consulta_vacaciones="DELETE FROM periodos_vacaciones WHERE cod_expediente_det='$codigo'";
    $delete_vacaciones=mysqli_query($conexion,$consulta_vacaciones);
//    echo "CONSULTA VACACIONES: "; echo $consulta_vacaciones; 
//        echo "<br>";
//        echo "<br>";
    
    //ELIMINACION EXPEDIENTE
    $consulta_expediente="DELETE FROM expediente WHERE cod_expediente_det='$codigo'";
    $delete_expediente=mysqli_query($conexion,$consulta_expediente);
//    echo "CONSULTA EXPEDIENTE: "; echo $consulta_expediente; 
//        echo "<br>";
//        echo "<br>";
         
    $cont++;
   
}
echo "CANTIDAD DE REGISTROS BORRADOS POR TABLA: "; echo $cont++;
?>

