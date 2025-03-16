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

$consulta_dependientes="SELECT * FROM rrhh_dependientes";               
$resultado_dependientes=mysqli_query($conexion,$consulta_dependientes);
//$fetch_baja=mysqli_fetch_array($resultado_movimientos); 
//echo '<br><br>Dependientes: <table border="1">';
//echo '<tbody>';
//echo '<tr>';
//echo '<td>num</td><td>id empleado</td><td>id dependiente</td><td>tipo</td><td>nombre</td><td>fecha nacimiento</td><td>sexo</td><td>creado por</td>';
//echo '<td>fecha creacion</td><td>modificado por</td><td>fecha modificacion</td><td>discapacidad</td>';
//echo '</tr>';
$cont=1;
while($fila=mysqli_fetch_array($resultado_dependientes))
{
    
    $id_empleado=$fila['id_empleado'];
    $consultap="SELECT * FROM nompersonal WHERE personal_id='$id_empleado'";   
    $resultadop=mysqli_query($conexion,$consultap);
    $fetchCon=mysqli_fetch_array($resultadop);                
    $cedula=$fetchCon['cedula'];
    $ficha=$fetchCon['ficha'];
    $tipnom=$fetchCon['tipnom'];
    
    $id_dependiente=$fila['id_dependiente'];
    
    $tipo=$fila['tipo'];
    if($tipo!="")
    {
        if($tipo=="Madre")
            $parentesco=1;
        else if ($tipo=="Padre")
            $parentesco=2;
        else if ($tipo=="Hijo" || $tipo=="Hija")
            $parentesco=3;
        else if ($tipo=="Esposo" || $tipo=="Esposa")
            $parentesco=7;
       
    }
    $nombre=utf8_decode($fila['nombre']);
    $fecha_nacimiento=$fila['fecha_nacimiento'];
    $sexo=$fila['sexo'];
    if($sexo!="")
    {
        if($sexo=="M")
            $s="Masculino";
        else if ($sexo=="F")
            $s="Femenino";       
    }
    
    $creado_por=$fila['creado_por'];
    $fecha_creacion=$fila['fecha_creacion'];
    $modificado_por=$fila['modificado_por'];
    $fecha_modificacion=$fila['fecha_modificacion'];
    $discapacidad=$fila['discapacidad'];
    
//    echo '<tr>';
//    echo "<td>".$cont."</td>";
//    echo "<td>".$id_empleado."</td>";   
//    echo "<td>".$id_dependiente."</td>";
//    echo "<td>".$tipo."</td>";
//    echo "<td>".$nombre."</td>";
//    echo "<td>".$fecha_nacimiento."</td>";
//    echo "<td>".$sexo."</td>";
//    echo "<td>".$creado_por."</td>";
//    echo "<td>".$fecha_creacion."</td>";
//    echo "<td>".$modificado_por."</td>";
//    echo "<td>".$fecha_modificacion."</td>"; 
//    echo "<td>".$discapacidad."</td>"; 
//     echo '</tr>';
    
    if($nombre!="" && $tipo!="")  
    {
        $consulta_familiar="INSERT INTO nomfamiliares
                    (correl, cedula, ficha, nombre, codpar, sexo, fecha_nac, tipnom, cedula_beneficiario, discapacidad )
                    VALUES  
                    ('','{$cedula}','{$ficha}','{$nombre}','{$parentesco}','{$s}','{$fecha_nacimiento}','{$tipnom}','{$cedula}','{$discapacidad}')";
        //$resultado_familiar=mysqli_query($conexion,$consulta_familiar);
    }
   
    echo "<br>"; echo $consulta_familiar; echo ";";
    
    $cont++;
}
echo '</tbody>';
echo '</table>';



?>