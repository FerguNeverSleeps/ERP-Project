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


$consulta_personal="SELECT * FROM nompersonal "
        . "ORDER BY ficha ASC";

$resultado_personal=mysqli_query($conexion,$consulta_personal);

$cont=1;
while($fila=mysqli_fetch_array($resultado_personal))
{          
   
    $id                 = $fila['personal_id'];
    $cedula             = $fila['cedula'];
    $primer_nombre      = $fila['nombres'];
    $segundo_nombre     = $fila['nombres2'];
    $primer_apellido    = $fila['apellidos'];
    $segundo_apellido   = $fila['apellido_materno'];
    $apellido_casada    = $fila['apellido_casada'];
    $apenom ="";
    
    if($primer_apellido!='' && $primer_apellido!=' ' && $primer_apellido!=NULL)
        $apenom.=$primer_apellido;
    
    if($apellido_casada!='' && $apellido_casada!=' ' && $apellido_casada!=NULL)
        $apenom.=" De ".$apellido_casada;    
    else if($segundo_apellido!='' && $segundo_apellido!=' ' && $segundo_apellido!=NULL)
        $apenom.=" ".$segundo_apellido;
    
    if($primer_nombre!='' && $primer_nombre!=' ' && $primer_nombre!=NULL)
        $apenom.=", ".$primer_nombre;

    if($segundo_nombre!='' && $segundo_nombre!=' ' && $segundo_nombre!=NULL)
        $apenom.=" ".$segundo_nombre;

    

    
   
    

    $query="UPDATE nompersonal SET
            apenom='$apenom'
            where personal_id='$id';\n";
             
    echo $query;echo "<br>";            
    //$resultado=mysqli_query($conexion,$query);
					
	    
    $cont++;
}

?>

