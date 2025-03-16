<?php
include('../generalp.config.inc.php');
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


$consulta_planilla="SELECT DISTINCT (
                    ficha
                    ), codnom
                    FROM `nom_movimientos_nomina`
                    WHERE `codnom`
                    IN ( 49, 48, 35, 30, 29, 28, 27, 22, 21, 20, 5 )
                    AND `codnivel1` IS NULL
                    ORDER BY `nom_movimientos_nomina`.`ficha` ASC";

$resultado_planilla=mysqli_query($conexion,$consulta_planilla);

$cont=1;
while($fila=mysqli_fetch_array($resultado_planilla))
{          
   
    $ficha              = $fila['ficha'];
    $codnom             = $fila['codnom'];
    
    $consulta_cc="SELECT codnivel1
                    FROM `nom_movimientos_nomina`
                    WHERE ficha='$ficha' AND codnom='$codnom' AND codcon=200";

    $resultado_cc=mysqli_query($conexion,$consulta_cc);
    $fila_cc=mysqli_fetch_array($resultado_cc);
    $codnivel1= $fila_cc['codnivel1'];
    
    $query="UPDATE nom_movimientos_nomina SET
            codnivel1='$codnivel1'
            WHERE ficha='$ficha' AND codnom='$codnom' AND `codnivel1` IS NULL";
    echo $query;echo ";<br>";         
               
    //$resultado=mysqli_query($conexion,$query);
					
	    
    $cont++;
}

?>

